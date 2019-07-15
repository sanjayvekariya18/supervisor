<?php 
	
namespace App\Helpers;
use App\Models\User;
use App\Models\Machine;
use App\Models\Worker;
use App\Models\MachineGroup;
use App\Models\ColorRange;
use App\Models\SmsRechargeHistory;
use DB;
use Auth;

class AbettorHelper
{
    /**
     * Get Machines Group wise
     *
     * @param Int $group_id
     * @param Int $cust_id
     * @param Array $cust_id
     * @param String List or All
     */
    public function get_machines($group_id='all',$cust_id=null,$options = ['list'])
    {
        if (empty($cust_id)) return [];

        $conditions = [];
        $isAdmin = (auth()->check() && $this->isAdmin()) ? true : false;

        if (!empty($group_id) && $group_id != 'all') {
            $conditions['group_id'] = $group_id;
        }
        if($isAdmin){
            $conditions['cust_id'] = $cust_id;
            $conditions['status_id'] = 1;
            $machines = Machine::where($conditions)->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC');
        }else{
            $conditions['machine_groups.supervisor_id'] = auth()->id();
            $conditions['status_id'] = 1;
            $machines = Machine::where($conditions)
                    ->join('machine_groups','machine_groups.id','machines.group_id')
                    ->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC');
        }

        if (!empty($options) && in_array('list',$options)) {
            $tmp_machines = $machines->get()->toArray();
            
            $machines = [];
            foreach ($tmp_machines as $key => $value) {
                if (!empty($value['machine_name'])) {
                    // If machine name is available
                    if (!empty($options) && in_array('inc_name',$options)) {
                        $machines[$value['machine_number']] = $value['machine_number'];
                    }else{
                        $machines[$value['machine_number']] = $value['machine_number'];
                    }
                }else{
                    $machines[$value['machine_number']] = $value['machine_number'];
                }
            }
        }else{
            $machines = $machines->get()->toArray();            
        }
        return $machines;
    }

    public function get_machines_data($group_id='all',$cust_id=null)
    {
        if (empty($cust_id)) return [];

        $conditions = [];
        $isAdmin = (auth()->check() && $this->isAdmin()) ? true : false;

        if (!empty($group_id) && $group_id != 'all') {
            $conditions['group_id'] = $group_id;
        }
        if($isAdmin){
            $conditions['cust_id'] = $cust_id;
            $conditions['status_id'] = 1;
            $machines = Machine::where($conditions)->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC')->get()->toArray();
        }else{
            $conditions['machine_groups.supervisor_id'] = auth()->id();
            $conditions['status_id'] = 1;
            $machines = Machine::where($conditions)
                    ->join('machine_groups','machine_groups.id','machines.group_id')
                    ->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC')
                    ->get()->toArray();
        }
        return $machines;
    }

    public function get_machines_detail($group_id='all',$cust_id=null)
    {
        if (empty($cust_id)) return [];

        $conditions = [];

        if (!empty($group_id) && $group_id != 'all') {
            $conditions['group_id'] = $group_id;
        }
        $conditions['cust_id'] = $cust_id;


        $machines = Machine::select('machines.*',DB::raw('SEC_TO_TIME(stop_time) AS total_stop_time'),DB::raw("IF(
            (TIMESTAMPDIFF(SECOND,last_sync,NOW())) > 60, SEC_TO_TIME(TIMESTAMPDIFF(SECOND,last_sync,NOW())), 'Running...') AS stop_time"))
                    ->where($conditions)
                    ->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC')
                    ->with('worker')
                    ->with('day_worker')
                    ->with('night_worker')
                    ->get()->toArray();
        return $machines;
    }

/**
     * Get Machines Group wise
     *
     * @param Int $group_id
     * @param Int $cust_id
     * @param Array $cust_id
     * @param String List or All
     */
    public function get_machines_ids($group_id='all',$cust_id=null,$options = ['list'])
    {
        if (empty($cust_id)) return [];

        $conditions = [];

        if (!empty($group_id) && $group_id != 'all') {
            $conditions['group_id'] = $group_id;
        }
        $conditions['cust_id'] = $cust_id;


        $machines = Machine::where($conditions)->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC');

        if (!empty($options) && in_array('list',$options)) {
            $tmp_machines = $machines->select(['machine_id','machine_number','machine_name'])->get()->toArray();
            $machines = [];
            foreach ($tmp_machines as $key => $value) {
                if (!empty($value['machine_name'])) {
                    // If machine name is available
                    if (!empty($options) && in_array('inc_name',$options)) {
                        $machines[$value['machine_id']] = $value['machine_number'] .' ('. $value['machine_name'].')';
                    }else{
                        $machines[$value['machine_id']] = $value['machine_number'];
                    }
                }else{
                    $machines[$value['machine_id']] = $value['machine_number'];
                }
            }
        }else{
            $machines = $machines->get()->toArray();            
        }

        return $machines;
    }

    /**
     * Get Next Worker ID of customer
     *
     * @return Worker Id
     */
    public function get_next_worker_id()
    {
        $cust_id = ($this->isAdmin()) ? Auth::id() : Auth::user()->parent_id;
        $worker = Worker::where('cust_id',$cust_id)->orderBy('created_at','desc')->limit(1)->pluck('worker_id')->first();
        //$worker = DB::select("select worker_id from `workers` where `cust_id` = '".$cust_id."' and `workers`.`deleted_at` is null order by worker_id DESC LIMIT 1");
        
        if($worker){
            $nextWorkerID = ++$worker;
        }else{
            $username = explode('@',$cust_id);
            $nextWorkerID = $username[0]."@W101";
        }

        /* if (!empty($worker[0]->worker_id)) {
            $worker_id = (int) filter_var($worker[0]->worker_id, FILTER_SANITIZE_NUMBER_INT) + 1;
        }else{
            $worker_id = 101;
        } */

        return $nextWorkerID;
        
    }

    public function get_next_supervisor_id()
    {
        $cust_id = ($this->isAdmin()) ? Auth::id() : Auth::user()->parent_id;
        $supervisor = User::where('parent_id',$cust_id)->orderBy('created_at','desc')->limit(1)->pluck('id')->first();
        if($supervisor){
            $nextSupervisorID = ++$supervisor;
        }else{
            $username = explode('@',$cust_id);
            $nextSupervisorID = $username[0]."@SUP1";
        }
        return $nextSupervisorID;
    }

    public function get_group_name_by_id($group_id = 0)
    {
        $group = MachineGroup::find($group_id)->toArray();
        if (!empty($group['group_name'])) {
            return $group['group_name'];
        }else{
            return "";
        }
    }

    public function get_worker_fullname_by_id($worker_id = 0)
    {
        $worker = Worker::find($worker_id)->toArray();
        if (!empty($worker['first_name'])) {
            return $worker['first_name'] . ' ' . $worker['last_name'];
        }else{
            return "";
        }
    }

    public function get_customer_color_range($cust_id='')
    {
        $_color_range = ColorRange::where('cust_id',$cust_id)->orderBy('id','ASC')->get()->toArray();
        $color_range = [];
        foreach ($_color_range as $key => $value) {
            $color_range[] = [
                'color_code' => $value['color_code'],
                'from' => $value['from_stitches'],
                'to' => $value['to_stitches'],
            ];
        }

        return $color_range;
    }

    public function get_customer_list()
    {
        $tmp_users = User::where(['parent_id' => NULL,'status_id'=>1])->get()->toArray();
        $users = [];

        foreach ($tmp_users as $key => $value) {
            $users[$value['id']] = $value['company_name'] . ' - ' . $value['first_name'];
        }

        return $users;
    }

    public function get_group_by_cust($cust_id='')
    {
        $group_list = MachineGroup::where([
            'cust_id' => $cust_id,
        ])->pluck('group_name','id')->toArray();

        $group_list = ['all'=>'All'] + $group_list;

        return $group_list;
    }

    public function get_group_by_supervisor($supervisor_id='')
    {
        $group_list = MachineGroup::where('supervisor_id','LIKE',"%$supervisor_id%")->pluck('group_name','id')->toArray();

        $group_list = ['all'=>'All'] + $group_list;

        return $group_list;
    }



    public function isAdmin()
    {
        return (auth()->user()->parent_id == NULL) ? true : false;
    }

    public function getMachineGroupByCust()
    {
        if($this->isAdmin()){
            return MachineGroup::where('cust_id',auth()->user()->id)->get();
        }else{
            return MachineGroup::where('supervisor_id','LIKE',"%".auth()->user()->id."%")->get();
        }
    }
}

?>