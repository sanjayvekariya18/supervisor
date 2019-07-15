<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\MachineGroup;
use App\Models\User;
use App\Models\ColorRange;
use App\Models\Worker;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AbettorHelper;
use App\Helpers\ConsentHelper;
use Validator;
use DB;
use Auth;
use Carbon\Carbon;
class MachinesController extends Controller
{
    var $Abettor;
    var $Consent;
    var $cust_id;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AbettorHelper $AbettorHelper,ConsentHelper $ConsentHelper)
    {
        
        $this->middleware('auth');
        $this->Abettor = $AbettorHelper;
        $this->Consent = $ConsentHelper;

        $this->middleware(function ($request, $next) {
            $this->Consent->init();
           
            $this->cust_id = ($this->Abettor->isAdmin()) ? Auth::id() : auth()->user()->parent_id ;
            return $next($request);
        });

    }

    /**
     * Show the all live machines connected to with client
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard(Request $request)
    {

        $current_date_time = date('Y-m-d H:s:i');
        $permission = \Auth::user()->permission;
        if ($request->isMethod('post')) {
            $worker_id = $request->get('worker_id');
            $machine_id = $request->input('machine_id');
            $shift = $request->input('shift');

            $machines = Machine::where('cust_id',$this->cust_id)
                    ->where(function($q) use($worker_id){
                        $q->where('day_worker_id',$worker_id);
                        $q->orWhere('night_worker_id',$worker_id);
                    })->get();
            /* echo "<pre>";
            print_r($request->toarray());
            die; */

            foreach ($machines as $key => $machine) {
                if($machine->day_worker_id == $worker_id){
                    Machine::where('id',$machine->id)->update(['day_worker_id'=> NULL]);
                }else if($machine->night_worker_id == $worker_id){
                    Machine::where('id',$machine->id)->update(['night_worker_id'=> NULL]);
                }
                if($machine->worker_id == $worker_id){
                    Machine::where('id',$machine->id)->update(['worker_id'=> NULL]);
                }
            }

            $machine = Machine::find($machine_id);
            if($shift == 2){
                $machine->night_worker_id = ($worker_id) ? $worker_id : NULL;
            }else{
                $machine->day_worker_id = ($worker_id) ? $worker_id : NULL;
            }
            if($shift == $machine->shift){
                $machine->worker_id = $worker_id;
            }
            if ($machine->save()) {
                return redirect()->route('customer.dashboard')->withSuccess('Worker Assign successfully.');
            }else{
                return redirect()->route('customer.dashboard')->withError('Worker Not Assign.');
            }
            /* $udpate_array = [
                'day_worker_id' => $request->input('day_worker_id'),
                'night_worker_id' => $request->input('night_worker_id'),
                'worker_id' => ($machine->shift == 1) ? $request->input('day_worker_id') : $request->input('night_worker_id')
            ]; */
            // Machine::where('id',$machine_id)->update($udpate_array);
            // return redirect()->route('customer.dashboard')->withSuccess('Worker Updated successfully.');

        }
        /* echo "<pre>";
        print_r($permission->toarray());
        die; */
        $m_conditions['cust_id'] = $this->cust_id;
        $m_conditions['status_id'] = 1;

        $machines = Machine::select(
                            'machines.*',
                            DB::raw('SEC_TO_TIME(stop_time) AS total_stop_time'),'working_head',
                            DB::raw("IF((TIMESTAMPDIFF(SECOND,last_sync,NOW())) > 60, SEC_TO_TIME(TIMESTAMPDIFF(SECOND,last_sync,NOW())), 'Running...') AS stop_time")
                    )
                    ->with('worker:id,first_name')
                    ->where($m_conditions);

        
        if ($this->Consent->who()=='supervisor') {
            $machines = $machines->whereIn('group_id',$this->Consent->groups_ids());
        }
        $machines = $machines->orderByRaw('LENGTH(machine_id) ASC')
        ->orderBy('machine_id','ASC')
        ->get();

        

        $worker = Worker::select(['first_name','id'])->where(['cust_id'=>$this->cust_id])->pluck('first_name','id')->toArray();
        $_color_range = ColorRange::where('cust_id',$this->cust_id)->orderBy('id','ASC')->get()->toArray();
        $color_range = [];
        foreach ($_color_range as $key => $value) {
            $color_range[] = [
                'color_code' => $value['color_code'],
                'from' => $value['from_stitches'],
                'to' => $value['to_stitches'],
            ];
        }

        $reports_settings = json_decode(Auth::user()->reports_settings,1);

        $show_thread_break = 0;
        $show_working_head = 0;

        if (!empty($reports_settings['columns']['thred_break'])) $show_thread_break = 1; 
        if (!empty($reports_settings['columns']['working_head'])) $show_working_head = 1;

        if ($this->Consent->who()=='supervisor') {
            $groups = $this->Abettor->get_group_by_supervisor($this->Consent->pk());
        }else{
            $groups = $this->Abettor->get_group_by_cust($this->cust_id);
        }
        /* echo "<pre>";
        print_r($worker);
        die; */
        return view('user.dashboard',[
            'cust_id' => $this->cust_id,
            'machines' => $machines,
            'worker' => $worker,
            'groups' => $groups,
            'color_range' => $color_range,
            'show_thread_break' => $show_thread_break,
            'show_working_head' => $show_working_head,
            'permission' => $permission,
        ]);
    }

    public function index(Request $request)
    {

        $buzzer_time_data = buzz_blank_time();

        // Save Buzzer Settings
        if ($request->isMethod('post')) {
            $default_stop_minutes = $request->input('default_stop_minutes');
            $default_stop_seconds = $request->input('default_stop_seconds');
            $default_buzzer_time = date("H:i:s",strtotime("00:".$default_stop_minutes.':'.$default_stop_seconds));
            $udpate_array['default_buzzer_time'] = $default_buzzer_time;

            $buzzer_time_data['10_min'] = !empty($request->input('10_min')) ? $request->input('10_min') : 0;
            $buzzer_time_data['20_min'] = !empty($request->input('20_min')) ? $request->input('20_min') : 0;
            $buzzer_time_data['30_min'] = !empty($request->input('30_min')) ? $request->input('30_min') : 0;

            $buzzer_time_data = array_filter($buzzer_time_data);
            if (!empty($buzzer_time_data)) {
                $buzzer_time_data = json_encode($buzzer_time_data);
                $udpate_array['buzzer_time'] = $buzzer_time_data;
            }
            
            $default_buzzer_time = $default_buzzer_time;
            
            $selected_machine = $request->input('selected_machine');


            $update = Machine::whereIn('id',$selected_machine)->update($udpate_array);
            if($update){
                return redirect()->route('machines.list')->withSuccess('Buzzer setting updated successfully.');
            }else{
                return redirect()->route('machines.list')->withError('Unable to update Buzzer setting, please try again later.');
            }

        }

        // Searching
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('machine.list.search','');
            return redirect(route('machines.list'));
        }elseif ($request->_token) {
            $request->session()->put('machine.list.search',$request->all());
            $search = $request->session()->get('machine.list.search');
        }else{
            $search = $request->session()->get('machine.list.search');
        }

        $conditions[] = ['cust_id',$this->cust_id];
        $conditions[] = ['status_id',1];
        if (!empty($search)) {
            if (!empty($search['machine_number'])) {
                $conditions[] = ['machine_number','LIKE','%'.$search['machine_number'].'%'];
            }
        }

        $machines = Machine::with('machine_group:id,group_name')
        ->with('day_worker:id,first_name,last_name')
        ->with('night_worker:id,first_name,last_name')
        ->sortable(['updated_at'=>'desc'])
        ->orderByRaw('LENGTH(machine_id) ASC')
        ->orderBy('machine_id','ASC')
        ->where($conditions)->paginate(PAGE_LIMIT);
        
        $machine_group = MachineGroup::where(['cust_id'=>$this->cust_id])->pluck('group_name','id')->toArray();
        
        $worker = Worker::select('id',DB::raw('CONCAT_WS(" ",first_name,last_name) as full_name'))
                    ->where(['cust_id'=>$this->cust_id,'status_id'=>1])
                    ->pluck('full_name','id')->toArray();
        
        $worker = array('' => 'Select') + $worker;
        // $worker = array_merge(array('Select' => 'Select'), $worker);
        /* echo "<pre>";
        print_r($worker);
        die; */
        $buzzer_time_data = buzz_blank_time();
        return view('user.machines.list',[
            'machines' => $machines,
            'worker' => $worker,
            'machine_group' => $machine_group,
            'machine_search' => $search,
            'buzzer_time_data' => $buzzer_time_data,
        ]);
    }

    public function disconnected(Request $request)
    {
        $Nowdate = Carbon::now();
        $cust_id = ($this->Abettor->isAdmin()) ? auth()->id() : auth()->user()->parent_id;
        $query = "SELECT 
			IFNULL(l.id,0) id
			,IFNULL(l.machine_number,0) machine_number
			,IFNULL(l.shift,0) shift
			,TIMESTAMPDIFF(MINUTE,l.`updated_at`,NOW()) AS stop_since_min
			,CONCAT(FLOOR(TIMESTAMPDIFF(MINUTE,l.`updated_at`,NOW())/60),'h ',MOD(TIMESTAMPDIFF(MINUTE,l.`updated_at`,NOW()),60),'m')  as stop_since_hrs
			FROM machines l WHERE cust_id='$cust_id' AND TIMESTAMPDIFF(MINUTE, l.updated_at, NOW()) >= l.disconnect_alert
            ORDER BY LENGTH(machine_id) ASC,machine_id ASC";
        $machines = DB::select($query);
            
        return view('user.machines.disconnected',[
            'machines' => $machines
        ]);
    }

    /**
     * Show the machine create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update($machine_id = null, Request $request)
    {

        $customers = User::select(['id',DB::raw('CONCAT(id," - ",first_name," ",last_name) as full_name')])
                    ->where('status_id',1)
                    ->orderBy('first_name','ASC')
                    ->pluck('full_name','id')
                    ->toArray();

        $user = Machine::find($machine_id);

        if (empty($user)) {
            return redirect(route('machines.list'))->withError('Oops!!! Invalid machine');
        }

        if ($request->isMethod('post')) {
            return redirect(route('machines.list'))->withSuccess('machine updated successfully.');
        }

        return view('user.machines.create',[
            'form_data' => $user,
            'customers' => $customers,
            'action' => 'update',
        ]);
    }

    public function change_machine_group(Request $request)
    {
        $result['message'] = 'Invalid Request';
        $result['code'] = 400;

        if ($request->ajax()) {
            try {

                $machine_id = $request->get('pk');
                $machine_group_id = $request->get('value');
                if ($machine_group_id > 0) {
                    // Update Machine Group
                    $machine = Machine::find($machine_id);
                    $machine->group_id = $machine_group_id;
                    if ($machine->save()) {
                        $result['message'] = 'Group assigned to Machine successfully';
                        $result['code'] = 200;
                    }else{
                        $result['message'] = 'Unable to assigned machine, Please try again later';
                        $result['code'] = 400;
                    }
                }else{
                    $result['message'] = 'Please select Machine Group from list';
                    $result['code'] = 400;
                }
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['code'] = 400;
            }
        }
        return response()->json($result['message'],$result['code']);
    }

    public function change_machine_worker($shift = NULL,Request $request)
    {
        $result['message'] = 'Invalid Request';
        $result['code'] = 400;
        
        if ($request->ajax()) {
            try {
                $machine_id = $request->get('pk');
                $worker_id = $request->get('value');

                /* echo "<pre>";
                print_r($request->all());
                die; */
                $machines = Machine::where('day_worker_id',$worker_id)
                        ->orWhere('night_worker_id',$worker_id)
                        ->get();
                foreach ($machines as $key => $machine) {
                    if($machine->day_worker_id == $worker_id){
                        Machine::where('id',$machine->id)->update(['day_worker_id'=> NULL]);
                    }else if($machine->night_worker_id == $worker_id){
                        Machine::where('id',$machine->id)->update(['night_worker_id'=> NULL]);
                    }
                    if($machine->worker_id == $worker_id){
                        Machine::where('id',$machine->id)->update(['worker_id'=> NULL]);
                    }
                }
                // print_r($machines->toarray());
                // die("updated");
                //if ($worker_id > 0) {
                    // Update Worker Group
                    $machine = Machine::find($machine_id);
                    
                    if($shift != NULL && $shift == 2){
                        $machine->night_worker_id = ($worker_id) ? $worker_id : NULL;
                    }else{
                        $machine->day_worker_id = ($worker_id) ? $worker_id : NULL;
                    }
                    if($shift == $machine->shift){
                        $machine->worker_id = $worker_id;
                    }
                    if ($machine->save()) {
                        $result['message'] = 'Worker assigned to Machine successfully';
                        $result['code'] = 200;
                    }else{
                        $result['message'] = 'Unable to assigned Worker, Please try again later';
                        $result['code'] = 400;
                    }
                /* }else{
                    $result['message'] = 'Please select Worker from list';
                    $result['code'] = 400;
                } */
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['code'] = 400;
            }
        }
        return response()->json($result['message'],$result['code']);
    }

    
    public function change_machine_name(Request $request)
    {
        $result['message'] = 'Invalid Request';
        $result['code'] = 400;

        if ($request->ajax()) {
            try {
                $machine_id = $request->get('pk');
                $machine_name = $request->get('value');
                if (!empty($machine_name)) {
                    
                    // Update Machine Name
                    $machine = Machine::find($machine_id);
                    $machine->machine_name = $machine_name;
                    if ($machine->save()) {
                        $result['message'] = 'Machine name changed successfully';
                        $result['code'] = 200;
                    }else{
                        $result['message'] = 'Unable to  change Machine name, Please try again later';
                        $result['code'] = 400;
                    }
                }else{
                    $result['message'] = 'Please enter Machine name';
                    $result['code'] = 400;
                }
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['code'] = 400;
            }
        }
        return response()->json($result['message'],$result['code']);
    }

    public function change_machine_number(Request $request)
    {
        $result['message'] = 'Invalid Request';
        $result['code'] = 400;

        if ($request->ajax()) {
            try {
                $machine_id = $request->get('pk');
                $machine_number = $request->get('value');
                if ($machine_number >= 1) {
                    
                    // Check duplicate
                    $duplicate = Machine::where([
                        'cust_id' => $this->cust_id,
                        'machine_number' => $machine_number,
                    ])->count();

                    if (empty($duplicate)) {
                        // Update Machine Number
                        $machine = Machine::find($machine_id);
                        $machine->machine_number = $machine_number;
                        if ($machine->save()) {
                            $result['message'] = 'Machine number changed successfully';
                            $result['code'] = 200;
                        }else{
                            $result['message'] = 'Unable to  change Machine number, Please try again later';
                            $result['code'] = 400;
                        }
                    }else{
                        $result['message'] = 'Machine number already exist';
                        $result['code'] = 400;
                    }
                }else{
                    $result['message'] = 'Please enter valid Machine number';
                    $result['code'] = 400;
                }
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['code'] = 400;
            }
        }
        return response()->json($result['message'],$result['code']);
    }

    public function setting_buzzer($machine_id = null, Request $request)
    {
        $machine_id = decrypt_str($machine_id);

        if (empty($machine_id)) {
            return redirect()->back()->withError('Invalid Machine');
        }

        $buzzer_time_data = buzz_blank_time();
        $machine = Machine::find($machine_id);
        
        if ($request->isMethod('post')) {

            $buzzer_time_data['10_min'] = !empty($request->input('10_min')) ? $request->input('10_min') : 0;
            $buzzer_time_data['20_min'] = !empty($request->input('20_min')) ? $request->input('20_min') : 0;
            $buzzer_time_data['30_min'] = !empty($request->input('30_min')) ? $request->input('30_min') : 0;

            $buzzer_time_data = array_filter($buzzer_time_data);
            $buzzer_time_data = json_encode($buzzer_time_data);
            $machine->buzzer_time = $buzzer_time_data;
            if($machine->save()){
                return redirect()->route('machines.settings.buzzer',encrypt_str($machine_id))->withSuccess('Buzzer time updated successfully.');
            }else{
                return redirect()->route('machines.settings.buzzer',encrypt_str($machine_id))->withError('Unable to update Buzzer time, Please try after some time.');
            }

        }
        $time_arr = !empty(json_decode($machine->buzzer_time,1)) ? json_decode($machine->buzzer_time,1) : [];
        $buzzer_time_data = array_merge($buzzer_time_data,$time_arr);

        return view('user.machines.settings.buzzer',[
            'buzzer_time_data' => $buzzer_time_data,
            'machine' => $machine,
            'current_tab' => 'setting_buzzer',
        ]);
    }


    public function get_machines_by_group(Request $request)
    {
        $result['data']['message'] = 'Invalid Request';
        $result['code'] = 400;

        if ($request->ajax()) {
            try {
                $group_id = $request->get('group_id');
                if (!empty($group_id)) {
                    
                    $machines_list = $this->Abettor->get_machines($group_id,$this->cust_id);
                    $machines_list = $machines_list;

                    $result['data']['message'] = 'Operation Successful';
                    $result['data']['data'] = $machines_list;
                    $result['code'] = 200;

                }else{
                    $result['data']['message'] = 'Invalid Group';
                    $result['code'] = 400;
                }
            } catch (Exception $e) {
                $result['data']['message'] = $e->getMessage();
                $result['code'] = 400;
            }
        }
        return response()->json($result['data'],$result['code']);
    }



}