<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Worker;
use App\Models\Machine;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AbettorHelper;
use App\Helpers\ConsentHelper;
use Validator;
use Auth;

class WorkersController extends Controller
{
    var $Abettor;
    var $Consent;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AbettorHelper $AbettorHelper, ConsentHelper $ConsentHelper)
    {
        $this->middleware('auth');
        $this->Abettor = $AbettorHelper;
        $this->Consent = $ConsentHelper;

        $this->middleware(function ($request, $next) {
            $this->Consent->init();
            return $next($request);
        });

    }

    public function index(Request $request)
    {

        // Searching
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('worker.list.search','');
            return redirect(route('workers.list'));
        }elseif ($request->_token) {
            $request->session()->put('worker.list.search',$request->all());
            $search = $request->session()->get('worker.list.search');
        }else{
            $search = $request->session()->get('worker.list.search');
        }

        $conditions[] = ['workers.cust_id',$this->Consent->cust_id()];
        if (!empty($search)) {
            if (!empty($search['worker_id'])) {
                $conditions[] = ['workers.worker_id','LIKE','%'.$search['worker_id'].'%'];
            }
        }

        $workers = Worker::selectRaw('workers.*,machines.machine_number,day_worker_id,night_worker_id')
                    ->leftjoin('machines',function($join){
                        $join->on('machines.day_worker_id','=','workers.id'); // i want to join the users table with either of these columns
                        $join->orOn('machines.night_worker_id','=','workers.id');
                    })
                    ->orderByRaw('ISNULL(machines.machine_id),LENGTH(machines.machine_id) ASC,machines.machine_id ASC')
                    ->sortable(['created_at'=>'desc'])->where($conditions)->paginate(PAGE_LIMIT);
       /*  echo "<pre>";
        print_r($workers->toarray());
        die; */
        return view('user.workers.list',[
            'workers' => $workers,
            'worker_search' => $search,
        ]);
    }

    /**
     * Show the Worker create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),Worker::final_rules(),Worker::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }
            
            $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
            /* $isAssign = Worker::where('shift',$request->input('shift'))
                            ->where('machine_number',$request->input('machine_number'))
                            ->where('cust_id',$cust_id)
                            ->first(); */

            // Save Worker
            $worker = new Worker;
            $worker->worker_id = $request->input('worker_id');
            $worker->cust_id = $this->Consent->cust_id();
            $worker->status_id = 1;
            $worker->first_name = $request->input('first_name');
            $worker->last_name = $request->input('last_name');
            $worker->contact_number_1 = $request->input('contact_number_1');
            $worker->contact_number_2 = $request->input('contact_number_2');
            $worker->aadhar_card_number = $request->input('aadhar_card_number');
            //$worker->machine_number = (!$isAssign) ? $request->input('machine_number') : NULL;
            $worker->reference_by = $request->input('reference_by');
            //$worker->shift = $request->input('shift');
            $worker->salary = $request->input('salary');
            $worker->address_1 = $request->input('address_1');
            $worker->address_2 = $request->input('address_2');
            
            if ($worker->save()) {
               /*  if($isAssign){
                    return redirect()->back()->withErrors("Worker Created.<br> Machine Already Assign! Please Assign another Machine.");
                }else{
                    $this->assign_machines_to_worker($worker);
                    return redirect(route('workers.add'))->withSuccess('Worker created successfully.');
                } */
                return redirect(route('workers.add'))->withSuccess('Worker created successfully.');
            }
        }

        //$machines_list = $this->Abettor->get_machines('all',$this->Consent->cust_id());
        //$machines_list = [''=>'Select'] + $machines_list;
        $next_worker_id = $this->Abettor->get_next_worker_id();
        return view('user.workers.create',[
            'request' => $request, 
            //'machines_list' => $machines_list,
            'next_worker_id' => $next_worker_id,
            'worker_id' => 0,
            'action' => 'create',
        ]);
    }

    /**
     * Show the Worker create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update($worker_id = null, Request $request)
    {

        $worker = Worker::find($worker_id);

        if (empty($worker)) {
            return redirect(route('workers.list'))->withError('Oops!!! Invalid Worker');
        }

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),Worker::update_rules($request),Worker::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            // Update Worker
            $worker->first_name = $request->input('first_name');
            $worker->last_name = $request->input('last_name');
            $worker->contact_number_1 = $request->input('contact_number_1');
            $worker->contact_number_2 = $request->input('contact_number_2');
            $worker->aadhar_card_number = $request->input('aadhar_card_number');
            $worker->reference_by = $request->input('reference_by');
            /* $worker->machine_number = $request->input('machine_number');
            $worker->shift = $request->input('shift'); */
            $worker->salary = $request->input('salary');
            $worker->address_1 = $request->input('address_1');
            $worker->address_2 = $request->input('address_2');
            
            if ($worker->save()) {

                /* if (!empty($request->input('machine_number'))) {

                    
                    $this->assign_machines_to_worker($worker);
                } */

                return redirect(route('workers.list'))->withSuccess('Worker updated successfully.');
            }
        }

        /* $machines_list = $this->Abettor->get_machines('all',$this->Consent->cust_id());
        $machines_list = [''=>'Select'] + $machines_list; */

        return view('user.workers.create',[
            'request' => $worker,
            // 'machines_list' => $machines_list,
            'worker_id' => $worker_id,
            'action' => 'update',
        ]);
    }

    /**
     * Change Worker status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function change_status($worker_id = null, $status_id = null, $inactivate_reason = null, Request $request)
    {

        $worker = Worker::find($worker_id);

        if (empty($worker)) {
            return redirect(route('workers.list'))->withError('Oops!!! Invalid Worker');
        }

        if (!in_array($status_id,[0,1])) {
            return redirect(route('workers.list'))->withError('Oops!!! Invalid Worker Status');
        }

        if ($request->isMethod('get')) {
            // Update Worker status
            $worker->status_id = $status_id;
            $worker->inactivate_reason = $inactivate_reason;
            if ($worker->save()) {
                return redirect(route('workers.list'))->withSuccess('Worker status updated successfully.');
            }
        }

        return redirect(route('workers.list'))->withError('Invalid request.');
    }

    public function assign_machines_to_worker($worker)
    {

        $user = Auth::user();
        $user_day_shift = $user['day_shift'];
        $user_night_shift = $user['night_shift'];
        $current_time = date("H:i:00");

        if ($worker->shift==1) {
            $udpate_array['day_worker_id'] = $worker->id;
        }else{
            $udpate_array['night_worker_id'] = $worker->id;
        }

        // Day Shift
        if ($worker->shift==1 && ($current_time >= $user_day_shift && $current_time < $user_night_shift)) {
            $udpate_array['worker_id'] = $worker->id;
        }

        // Night Shift
        if ($worker->shift==2 && ($current_time >= $user_night_shift || $current_time < $user_day_shift)) {
            $udpate_array['worker_id'] = $worker->id;
        }


        Machine::where([
            'cust_id'=>$this->Consent->cust_id(),
            'machine_number' => $worker->machine_number
        ])->update($udpate_array);


        // if (!empty($worker_id)) {

        //     // Reset Group Machines
        //     Machine::where(['worker_id' => $worker_id])->update(['worker_id' => 0]);

        //     // Assign Machines to Group
        //     if (!empty($machine_ids)) {
        //         foreach ($machine_ids as $key => $machine_id) {
        //             $machine = Machine::find($machine_id);
        //             if (!empty($machine)) {
        //                 $machine->worker_id = $worker_id;
        //                 $machine->save();
        //             }
        //         }
        //     }
        // }

    }

}
