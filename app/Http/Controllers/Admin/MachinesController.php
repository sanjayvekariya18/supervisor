<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AbettorHelper;
use Validator;
use DB;
use Carbon\Carbon;

class MachinesController extends Controller
{
    var $Abettor;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AbettorHelper $AbettorHelper)
    {
        $this->middleware('auth:admin');
        $this->Abettor = $AbettorHelper;
    }

    public function index(Request $request)
    {

        // Searching
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('admin.machine.list.search','');
            return redirect(route('admin.machines.list'));
        }elseif ($request->_token) {
            $request->session()->put('admin.machine.list.search',$request->all());
            $search = $request->session()->get('admin.machine.list.search');
        }else{
            $search = $request->session()->get('admin.machine.list.search');
        }

        $conditions = [];
        $machines = [];
        if (!empty($search)) {
            if (!empty($search['machine_number'])) {
                $conditions[] = ['machine_number',$search['machine_number']];
            }
            if (!empty($search['cust_id'])) {
                $conditions[] = ['cust_id','LIKE','%'.$search['cust_id'].'%'];
            }

            $machines = Machine::with('machine_group:id,group_name')
            ->with('worker:id,first_name,last_name')
            ->sortable([])->where($conditions)
            ->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC')
            ->paginate(PAGE_LIMIT);
        }


        $customer_list = $this->Abettor->get_customer_list();
        $customer_list = ['0' => 'Select'] + $customer_list;
        return view('admin.machines.list',[
            'machines' => $machines,
            'machine_search' => $search,
            'customer_list' => $customer_list,
        ]);
    }

    /**
     * Show the machine create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {
        $tmp_customers = User::select(['id','first_name','last_name'])
                    ->where('status_id',1)
                    ->where('parent_id','=',null)
                    ->orderBy('first_name','ASC')
                    ->get()
                    ->toArray();
        $customers = [];
        foreach ($tmp_customers as $key => $value) {
            $customers[$value['id']] = $value['id'] . '-' . $value['first_name'] . $value['last_name'];
        }
        $selected_cust = null;
        if ($request->isMethod('post')) {
            
            $validate = Validator::make($request->all(),Machine::admin_rules($request),Machine::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            $shifts = User::get_shifts($request->input('cust_id'));
            $day_shift = !empty($shifts[0]['day_shift']) ? $shifts[0]['day_shift'] : '09:00:00';
            $night_shift = !empty($shifts[0]['night_shift']) ? $shifts[0]['night_shift'] : '21:00:00';
            $disconnect_alert = !empty($shifts[0]['disconnect_alert']) ? $shifts[0]['disconnect_alert'] : '0';

            $request->session()->put('create.machine.selected.customer',$request->input('cust_id'));

            // Save machine
            $machine = new Machine;
            $machine->machine_id = strtoupper($request->input('machine_id'));
            $machine->cust_id = $request->input('cust_id');
            $machine->machine_number = $request->input('machine_number');
            $machine->machine_name = $request->input('machine_name');
            $machine->default_buzzer_time = '00:00:00';
            $machine->day_shift = $day_shift;
            $machine->night_shift = $night_shift;
            $machine->disconnect_alert = $disconnect_alert;
            $machine->status_id = 1;
            $machine->last_sync = Carbon::now();
            $machine->updated_at = Carbon::now();
            
            if ($machine->save()) {
                return redirect(route('admin.machines.create'))->withSuccess('Machine created successfully.');
            }
        }
        $selected_cust = $request->session()->get('create.machine.selected.customer');
        return view('admin.machines.create',[
            'form_data' => $request,
            'selected_cust' => $selected_cust,
            'customers' => $customers,
            'action' => 'create',
        ]);
    }

    /**
     * Show the machine create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update($machine_id = null, Request $request)
    {

        
        $tmp_customers = User::select(['id','first_name','last_name'])
                    ->where('status_id',1)
                    ->where('parent_id','=',null)
                    ->orderBy('first_name','ASC')
                    ->get()
                    ->toArray();
        $customers = [];
        foreach ($tmp_customers as $key => $value) {
            $customers[$value['id']] = $value['id'] . '-' . $value['first_name'] . $value['last_name'];
        }
        
        $machine = Machine::find($machine_id);

        if (empty($machine)) {
            return redirect(route('admin.machines.list'))->withError('Oops!!! Invalid machine');
        }

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),Machine::admin_update_rules($request,$machine_id),Machine::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            // Save machine
            $machine->cust_id = $request->input('cust_id');
            $machine->machine_number = $request->input('machine_number');
            $machine->machine_id = $request->input('machine_id');
            if ($machine->save()) {
                return redirect(route('admin.machines.update',$machine_id))->withSuccess('Machine updated successfully.');
            }
        }

        return view('admin.machines.create',[
            'form_data' => $machine,
            'customers' => $customers,
            'selected_cust' => null,
            'action' => 'update',
        ]);
    }

    /**
     * Change machine status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function change_status($machine_id = null, $status_id = null, $inactivate_reason = null, Request $request)
    {

        $machine = Machine::find($machine_id);

        if (empty($machine)) {
            return redirect(route('admin.machines.list'))->withError('Oops!!! Invalid machine');
        }

        if (!in_array($status_id,[0,1])) {
            return redirect(route('admin.machines.list'))->withError('Oops!!! Invalid machine Status');
        }

        if ($request->isMethod('get')) {
            // Update machine status
            $machine->status_id = $status_id;
            $machine->inactivate_reason = $inactivate_reason;
            if ($machine->save()) {
                return redirect(route('admin.machines.list'))->withSuccess('Machine status updated successfully.');
            }
        }

        return redirect(route('admin.machines.list'))->withError('Invalid request.');
    }

    public function change_rpm_cal(Request $request)
    {
        $result['message'] = 'Invalid Request';
        $result['code'] = 400;

        if ($request->ajax()) {
            try {

                $machine_id = $request->get('pk');
                $rpm_cal = $request->get('value');
                if ($rpm_cal > 0) {
                    // Update Machine Group
                    $machine = Machine::find($machine_id);
                    $machine->rpm_cal = $rpm_cal;
                    if ($machine->save()) {
                        $result['message'] = 'RPM Cal assigned to Machine successfully';
                        $result['code'] = 200;
                    }else{
                        $result['message'] = 'Unable to assigned machine RPM Cal , Please try again later';
                        $result['code'] = 400;
                    }
                }else{
                    $result['message'] = 'Please enter valid RPM Cal';
                    $result['code'] = 400;
                }
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['code'] = 400;
            }
        }
        return response()->json($result['message'],$result['code']);
    }

    public function calibration(Request $request)
    {
        // Searching
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('admin.calibration.search','');
            return redirect(route('admin.machines.calibration'));
        }elseif ($request->_token) {
            $request->session()->put('admin.calibration.search',$request->all());
            $search = $request->session()->get('admin.calibration.search');
        }else{
            $search = $request->session()->get('admin.calibration.search');
        }

        $conditions = [];
        $machines = [];
        // $conditions[] = ['parent_id',NULL];
        if (!empty($search)) {
            if (!empty($search['cust_id'])) {
                $conditions[] = ['cust_id','LIKE','%'.$search['cust_id'].'%'];
            }

            $machines = Machine::with('machine_group:id,group_name')
            ->with('worker:id,first_name,last_name')
            ->sortable([])->where($conditions)
            ->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC')
            ->paginate(500);
        }


        $cust_id = !empty($search['cust_id']) ? $search['cust_id'] : 'no-customer';
        $customers = $this->Abettor->get_customer_list();
        return view('admin.machines.device_calibration',[
            'machines' => $machines,
            'customers' => $customers,
            'search' => $search,
            'cust_id' => $cust_id,
        ]);
    }

    public function save_calibration(Request $request)
    {
        if ($request->isMethod('post')) {
            $all_machines = $request->all();
            $all_machines = !empty($all_machines['machines']) ? $all_machines['machines'] : [];

            foreach ($all_machines as $machine_id => $cal_data) {

                $machine_details = Machine::find($machine_id);
                $machine_details->head_cal_x = !empty($cal_data['head_cal_x']) ? $cal_data['head_cal_x'] : 0;
                $machine_details->head_cal_y = !empty($cal_data['head_cal_y']) ? $cal_data['head_cal_y'] : 0;
                $machine_details->rpm_cal = !empty($cal_data['rpm_cal']) ? $cal_data['rpm_cal'] : 1;
                $machine_details->stop_time_cal = !empty($cal_data['stop_time_cal']) ? $cal_data['stop_time_cal'] : 1;
                $machine_details->save();

            }

            return redirect(route('admin.machines.calibration'))->withSuccess('Calibration saved successfully.');
        }
        return redirect(route('admin.machines.calibration'))->withError('Invalid request.');

    }

    public function delete($id)
    {
       $machine = Machine::find($id);
       if($machine){
           Machine::destroy($id);
       }
       return redirect(route('admin.machines.list'))->withSuccess('Machine Delete successfully.');
    }

}
