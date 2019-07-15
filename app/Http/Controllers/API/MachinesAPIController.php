<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\WorkersController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AbettorHelper;
use App\Models\User;
use App\Models\Admin;
use App\Models\Worker;
use App\Models\Machine;

use Validator;
use Auth;

class MachinesAPIController extends Controller
{
    var $Abettor;

    public function __construct(AbettorHelper $AbettorHelper)
    {
        $this->Abettor = $AbettorHelper;
    }

    // Get Customers Machines
    public function get(Request $request)
    {
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false
        ];

        if ($request->isMethod('post')) {
            
            $cust_id = !empty($request->input('cust_id')) ? $request->input('cust_id') : null;
            if (!empty($cust_id)) {

                $machine = Machine::with('worker:id,first_name,last_name,contact_number_1')
                ->where('status_id',1)
                ->where('cust_id',$cust_id)
                ->orderByRaw('LENGTH(machine_id) ASC')
                ->orderBy('machine_id','ASC')
                ->get()->toArray();

                $result = [
                    'messages'=>'Data retrieved successfully',
                    'status'=> true,
                    'data' => $machine,
                ];
                
            }else{
                $result = [
                    'messages'=>'One of the following params is missing',
                    'status'=>false,
                    'data' => [],
                ];
            }
            

        }

        return response()->json($result);
    }

    /**
     * Save Machine Buzzer settings
     *
     * @param Request $request
     * @return $result
     */
    public function save_machine_buzzer_setting(Request $request)
    {
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false
        ];

        if ($request->isMethod('post')) {
            
            $machine_id = $request->input('machine_id');
            $buzzer_time_data = buzz_blank_time();
            $machine = Machine::find($machine_id);

            if (!empty($machine)) {

                $buzzer_time_data['10_min'] = !empty($request->input('10_min')) ? $request->input('10_min') : 0;
                $buzzer_time_data['20_min'] = !empty($request->input('20_min')) ? $request->input('20_min') : 0;    
                $buzzer_time_data['30_min'] = !empty($request->input('30_min')) ? $request->input('30_min') : 0;
                
                $buzzer_time_data = array_filter($buzzer_time_data);
                $buzzer_time_data = json_encode($buzzer_time_data);
                $machine->buzzer_time = $buzzer_time_data;
                if($machine->save()){
                    $result = [
                        'messages'=>'Buzzer time updated successfully',
                        'status'=> true,
                    ];
                }else{
                    $result = [
                        'messages'=>'Unable to update Buzzer time, Please try after some time.',
                        'status'=> flase,
                    ];
                }
                
                
            }else{
                $result = [
                    'messages'=>'Oops!!! Invalid Machine',
                    'status'=>false,
                ];
            }
            
        }

        return response()->json($result);
    }

    public function get_machines_by_group(Request $request)
    {
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false
        ];

        if ($request->isMethod('post')) {
            
            $cust_id = !empty($request->input('cust_id')) ? $request->input('cust_id') : null;
            $group_id = !empty($request->input('group_id')) ? $request->input('group_id') : null;

            $machines = $this->Abettor->get_machines($group_id,$cust_id);
            $result = [
                'messages'=>'Data retrieved successfully',
                'status'=> true,
                'data' => $machines,
            ];
        }

        return response()->json($result);

    }

    public function get_group_by_cust(Request $request)
    {
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false
        ];

        if ($request->isMethod('post')) {
            
            $cust_id = !empty($request->input('cust_id')) ? $request->input('cust_id') : null;

            $groups = $this->Abettor->get_group_by_cust($cust_id);
            $result = [
                'messages'=>'Data retrieved successfully',
                'status'=> true,
                'data' => $groups,
            ];
        }

        return response()->json($result);
    }

    public function getMachinesByGroup(Request $request)
    {
        $machineGroups = $this->Abettor->getMachineGroupByCust();
        if(count($machineGroups) == 0){
            $result = [
                'messages'=>'Machine Group is not created.',
                'status'=>false,
                'data' => [],
            ];
            return response()->json($result);
        }
        foreach ($machineGroups as $key => $machineGroup) {
            $machines = $this->Abettor->get_machines_detail($machineGroup->id,$machineGroup->cust_id);
            $machineGroups[$key]->machines = $machines;
        }
        $result = [
            'messages'=>'Data retrieved successfully',
            'status'=> true,
            'data' => $machineGroups,
        ];
        return response()->json($result,200);
    }

    public function getUnassignedMachine()
    {
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $machines_list = Machine::where([
            'cust_id' => $cust_id,
            'status_id' => 1,
            'group_id' => 0,
        ])->orderByRaw('LENGTH(machine_id) ASC, machine_id ASC')
        ->get();
        if(count($machines_list) == 0){
            $result = [
                'messages'=>'All Machines already Assigned',
                'status'=> true,
                'data' => [],
            ];
        }else{
            $result = [
                'messages'=>'',
                'status'=> true,
                'data' => $machines_list,
            ];
        }
        return response()->json($result,200);
    }

    // Get Customers Machines
    public function getMachines(Request $request)
    {   
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $machineGroups = $this->Abettor->getMachineGroupByCust();
       
        $machines = [];
        if(count($machineGroups) == 0){
            $machines = $this->Abettor->get_machines_detail("all",$cust_id);
        }else{
            foreach($machineGroups as $key => $machineGroup) {
                $machineList = $this->Abettor->get_machines_detail($machineGroup->id,$machineGroup->cust_id);
                $machines = array_merge($machines,$machineList);
            }
        }
        $adminSetting = Admin::find(1);
        $result = [
                'messages'=>'Data retrieved successfully',
                'status'=> true,
                'head' => $adminSetting->setting,
                'data' => $machines,
            ];
        return response()->json($result);
    }

    public function changeWorker(Request $request)
    {
        try {
            $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
            $machine_id = $request->get('machine_id');
            $worker_id = $request->get('worker_id');
            $isChecked = $request->get('isChecked');
            $shift = $request->get('shift');
            
            $machines = Machine::
                    where('cust_id',$cust_id)
                    ->where(function($q) use($worker_id){
                        $q->where('day_worker_id',$worker_id)
                            ->orWhere('night_worker_id',$worker_id);
                    })
                    ->get();

            if($isChecked == 1 && count($machines) > 0){
                $result = [
                    'messages'=>'Worker already assign with Machine Number '.$machines[0]->machine_number,
                    'status'=> false,
                    'data' => [],
                ];
                return response()->json($result,200);
            }
            
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

            if($machine){
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
                    $result['status'] = true;
                    $result['data'] = [];
                }else{
                    $result['message'] = 'Unable to assigned Worker, Please try again later';
                    $result['status'] = false;
                    $result['data'] = [];
                }
            }else{
                $result['message'] = 'Invalid Machine Id';
                    $result['status'] = false;
                    $result['data'] = [];
            }
            
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $result['status'] = false;
            $result['data'] = [];
        }
        return response()->json($result,200);
    }
}
