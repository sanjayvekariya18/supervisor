<?php 
	
namespace App\Helpers;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Machine;
use App\Models\Worker;
use App\Models\MachineGroup;
use App\Models\ColorRange;
use App\Models\SmsRechargeHistory;
use DB;


class ConsentHelper
{
    protected $who;
    protected $pk;
    protected $cust_id;
    protected $user;

    // Initialize required permission for User
    public function init()
    {
        // Check if Logged in User is Supervisor or Admin
        $user_details = Auth::user();

        if (!empty($user_details->parent_id)) {
            $this->cust_id = $user_details->parent_id;
            $user = User::find($this->cust_id);
            $this->who = 'supervisor';
        }else{
            $this->cust_id = $user_details->id;
            $user = Auth::user();
            $this->who = 'admin';
        }
        $this->pk = $user_details->id;

        $this->user = $user;

    }

    public function who()
    {
        return $this->who;
    }

    public function cust_id()
    {
        return $this->cust_id;
    }

    public function pk()
    {
        return $this->pk;
    }

    public function day_shift()
    {
        return $this->user->day_shift;
    }

    public function night_shift()
    {
        return $this->user->night_shift;
    }

    public function groups_ids()
    {
        $groups = MachineGroup::where('supervisor_id','LIKE',"%".$this->pk."%")->pluck('id')->toArray();
        return $groups;
    }
}

?>