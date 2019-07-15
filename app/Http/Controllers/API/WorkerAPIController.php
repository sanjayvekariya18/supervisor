<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\WorkersController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AbettorHelper;
use App\Helpers\ConsentHelper;
use App\Models\User;
use App\Models\Admin;
use App\Models\Worker;
use App\Models\Machine;

use Validator;
use Auth;

class WorkerAPIController extends Controller
{
	// SELECT DATE_FORMAT(`date`,"%Y-%m-%d") AS report_date , shift, SUM(st) AS stitches FROM `report_tmp` WHERE DATE_FORMAT(`date`,"%Y-%m-%d") >= "2019-04-01" AND `date` <= DATE_ADD("2019-04-01", INTERVAL 7 DAY) AND m_no=15 GROUP BY DATE_FORMAT(`date`,"%Y-%m-%d") , shift ORDER BY `report_tmp`.`date` ASC, shift ASC

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    var $Abettor;
    var $Consent;

    public function __construct(AbettorHelper $AbettorHelper,ConsentHelper $ConsentHelper)
    {
        $this->Abettor = $AbettorHelper;
        $this->Consent = $ConsentHelper;
    }

    /**
     * Change Worker status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function change_status(Request $request)
    {
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false,
            'data' => []
        ];

        if ($request->isMethod('post')) {
            
            $worker_id = $request->input('id');
            $status_id = $request->input('status_id');

            $worker = Worker::find($worker_id);
            $errors = 0;

            if (empty($worker)) {
                $result = [
                    'errors'=>'Oops!!! Invalid Worker',
                    'status'=> false,
                    'data' => []
                ];
                return response()->json($result);
            }

            if (!in_array($status_id,[0,1])) {
                $result = [
                    'errors'=>'Oops!!! Invalid Worker Status',
                    'status'=> false,
                    'data' => []
                ];
                return response()->json($result);
            }

            $worker->status_id = $status_id;
            if ($worker->save()) {
                $result = [
                    'messages'=>'Worker status updated successfully.',
                    'status'=> true,
                    'data' => []
                ];
            }else{
                $result = [
                    'errors'=>'Oops!!! Unable to update worker status',
                    'status'=> false,
                    'data' => []
                ];
            }
        }
        return response()->json($result);

    }

    // Get Customers Worker
    public function getWorkers(Request $request)
    {
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        
        $workers = Worker::selectRaw('workers.*,machines.machine_number,day_worker_id,night_worker_id')
                    ->leftjoin('machines',function($join){
                        $join->on('machines.day_worker_id','=','workers.id'); // i want to join the users table with either of these columns
                        $join->orOn('machines.night_worker_id','=','workers.id');
                    })
                    ->where('workers.cust_id',$cust_id)
                    ->orderByRaw('ISNULL(machines.machine_id),LENGTH(machines.machine_id) ASC,machines.machine_id ASC')
                    ->get();
                
        // $workers = Worker::where('cust_id',$cust_id)->get();
        if(count($workers) == 0){
            $result = [
                'messages'=>'Worker list not available.',
                'status'=>false,
                'data' => [],
            ];
        }else{
            $result = [
                'messages'=>'Data retrieved successfully',
                'status'=> true,
                'data' => $workers,
            ];
        }    
        return response()->json($result,200);
    }

    // Get Worker Info
    public function getWorker(Request $request)
    {
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false,
            'data' => []
        ];

        if ($request->isMethod('post')) {
            $id = $request->id;
            $worker = Worker::where('cust_id',$cust_id)
                        ->where('id', $id)
                        ->first();    
            if(!$worker){
                $result = [
                    'messages'=>'Worker not available.',
                    'status'=>false,
                    'data' => [],
                ];
            }else{
                $result = [
                    'messages'=>'Data retrieved successfully',
                    'status'=> true,
                    'data' => $worker,
                ];
            }    
        }
        return response()->json($result,200);
    }
    
    /**
     * Add worker for Customer
     *
     * @param Request $request
     * @return 
     */
    public function createWorker(Request $request, WorkersController $WorkersController)
    {
        $rules = [
            'aadhar_card_number'                => 'nullable|max:20|min:3',
            'first_name'                        => 'required|max:100|min:3',
            'contact_number_1'                  => 'required|max:10|min:10',
            'salary'                            => 'nullable|integer|max:50000|min:1',
        ];
        $messages = [
            'worker_id.required' => 'Worker ID is required',
            'worker_id.unique' => 'Worker ID already taken',
        ];
        $validate = Validator::make($request->all(),$rules,$messages);
        if ($validate->fails()) {
            $result = [
                'messages'=>'Unable to process request',
                'status'=> false,
                'data' => $validate->messages(),
            ];
            return response()->json($result);
        }

        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();

        /* $isAssign = Worker::where('shift',$request->input('shift'))
                            ->where('machine_number',$request->input('machine_number'))
                            ->where('cust_id',$cust_id)
                            ->first(); */
        $next_worker_id = $this->Abettor->get_next_worker_id();
        // Save Worker
        $worker = new Worker;
        $worker->worker_id = $next_worker_id;
        $worker->cust_id = $cust_id;
        $worker->status_id = 1;
        $worker->first_name = $request->input('first_name');
        $worker->last_name = $request->input('last_name');
        $worker->contact_number_1 = $request->input('contact_number_1');
        $worker->contact_number_2 = $request->input('contact_number_2');
        $worker->aadhar_card_number = $request->input('aadhar_card_number');
        // $worker->machine_number = (!$isAssign) ? $request->input('machine_number') : NULL;
        $worker->reference_by = $request->input('reference_by');
        // $worker->shift = $request->input('shift');
        $worker->salary = $request->input('salary');
        $worker->address_1 = $request->input('address_1');
        $worker->address_2 = $request->input('address_2');
        
        if ($worker->save()) {
            /* if($isAssign){
               $msg = "Worker Created.<br> Machine Already Assign! Please Assign another Machine.";
            }else{
                $WorkersController->assign_machines_to_worker($worker);
                $msg = 'Worker created successfully.';
            } */
             $msg = 'Worker created successfully.';
            $result = [
                'messages'=>$msg,
                'status'=> true,
                'data' => $worker
            ];
        }else{
            $result = [
                'messages'=>'Unable to add Worker, please try again laster',
                'status'=> false,
                'data' => []
            ];
        }
        return response()->json($result,200);
    }

    public function updateWorker(Request $request, WorkersController $WorkersController)
    {
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $id = $request->input('id');
        $rules = [
            'aadhar_card_number'                => 'nullable|max:20|min:3',
            'first_name'                        => 'required|max:100|min:3',
            'contact_number_1'                  => 'required|max:10|min:10',
            'salary'                            => 'nullable|integer|max:50000|min:1',
        ];
        $messages = [
            'worker_id.required' => 'Worker ID is required',
            'worker_id.unique' => 'Worker ID already taken',
        ];
        $validate = Validator::make($request->all(),$rules,$messages);
        if ($validate->fails()) {
            $result = [
                'messages'=>'Unable to process request',
                'status'=> false,
                'data' => $validate->messages(),
            ];
            return response()->json($result,200);
        }

        $worker = Worker::where('cust_id',$cust_id)
                        ->where('id',$id)
                        ->first();
        if (empty($worker)) {
            $result = [
                'messages'=>'Oops!!! Invalid Worker',
                'status'=> false,
                'data' => [],
            ];
            return response()->json($result,200);
        }
        
        $worker = Worker::find($id);
        /* $isAssign = Worker::where('shift',$request->input('shift'))
                        ->where('machine_number',$request->input('machine_number'))
                        ->where('cust_id',$cust_id)
                        ->where('id','<>',$id)
                        ->first(); */

        // Save Worker
        // $worker->worker_id = $request->input('worker_id');
        $worker->first_name = $request->input('first_name');
        $worker->last_name = $request->input('last_name');
        $worker->contact_number_1 = $request->input('contact_number_1');
        $worker->contact_number_2 = $request->input('contact_number_2');
        $worker->aadhar_card_number = $request->input('aadhar_card_number');
        /* if(!$isAssign){
           $worker->machine_number = $request->input('machine_number');
        } */
        $worker->reference_by = $request->input('reference_by');
        // $worker->shift = $request->input('shift');
        $worker->salary = $request->input('salary');
        $worker->address_1 = $request->input('address_1');
        $worker->address_2 = $request->input('address_2');
        
        if ($worker->save()) {
            /* if($isAssign){
               $msg = "Worker Updated.<br> Machine Already Assign! Please Assign another Machine.";
            }else{
                $WorkersController->assign_machines_to_worker($worker);
                $msg = 'Worker updated successfully.';
            } */
            $msg = "Worker updated successfully.";
            $result = [
                'messages'=>$msg,
                'status'=> true,
                'data' => $worker
            ];
        }else{
            $result = [
                'messages'=>'Unable to update Worker, please try again laster',
                'status'=> false,
                'data' => []
            ];
        }
        return response()->json($result,200);
    }

    /* public function deleteWorker($id,Request $request)
    {
        
        if (!$request->isMethod('post')) {
            $result = [
                'messages'=>'Method Not Allowed',
                'status'=>false,
                'data' => []
            ];
            return response()->json($result);       
        }
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $worker = Worker::where('cust_id',$cust_id)
                        ->where('id',$id)
                        ->delete();
        if(!$worker){
            $result = [
                'errors'=>'Oops!!! Invalid Worker',
                'status'=> false,
                'data' => []
            ];
            return response()->json($result);
        }

        $result = [
            'messages'=>'Worker deleted successfully.',
            'status'=> true,
            'data' => []
        ];
        return response()->json($result);
    } */
}
