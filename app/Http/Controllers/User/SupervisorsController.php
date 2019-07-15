<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MachineGroup;
use App\Models\SmsRechargeHistory;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AbettorHelper;
use Validator;
use Auth;
use App\Models\Permission;

class SupervisorsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AbettorHelper $AbettorHelper)
    {
        $this->middleware('auth');
        $this->Abettor = $AbettorHelper;
    }

    public function index(Request $request)
    {

        // Searching
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('supervisors.list.search','');
            return redirect(route('supervisors.list'));
        }elseif ($request->_token) {
            $request->session()->put('supervisors.list.search',$request->all());
            $search = $request->session()->get('supervisors.list.search');
        }else{
            $search = $request->session()->get('supervisors.list.search');
        }

        $conditions = [];
        $conditions[] = ['parent_id',Auth::id()];
        if (!empty($search)) {
            if (!empty($search['id'])) {
                $conditions[] = ['id','LIKE','%'.$search['id'].'%'];
            }
        }

        $supervisors = User::sortable(['created_at'=>'desc'])->where($conditions)->paginate(PAGE_LIMIT);
        /* echo "<pre>";
        print_r($supervisors->toarray());
        die; */
        return view('user.supervisor.list',[
            'supervisors' => $supervisors,
            'supervisor_search' => $search,
        ]);
    }

    /**
     * Show the Supervisor create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {
        if(permissions()->total_user <= getTotalUser()){
            return redirect(route('supervisors.list'))->withError('Your Total User Limit Reached');
        }
        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),User::supervisor_rules(),User::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }
            // echo "<pre>";
            $permissions = (array)auth()->user()->permission;
            //$superPermission = [];
            // print_r($permissions);
            // die;
            foreach ($permissions as $key => $value) {
                if($key == "id" || $key == "name" || $key == "total_user")
                    continue;
                $permissions[$key] = 0;
                // if($permissions[$key])    
                //     $superPermission[$key] = 0;
            }
           
            // print_r($superPermission);
            // die;
            // Save Supervisor
            $user = new User;
            $user->id = strtoupper($request->input('id'));
            $user->username = $request->input('username');
            $user->status_id = 1;
            $user->parent_id = (auth()->user()->parent_id == NULL) ? Auth::id() : auth()->user()->parent_id;
            $user->permission = $permissions;
            // $user->permission = $superPermission;
            $user->first_name = $request->input('first_name');
            $user->password = Hash::make($request->input('password_disp'));
            $user->password_disp = $request->input('password_disp');
            $user->contact_number_1 = $request->input('contact_number_1');
            $user->contact_number_2 = $request->input('contact_number_2');
            $user->email = $request->input('email');
            $user->address_1 = $request->input('address_1');
            $user->address_2 = $request->input('address_2');

            /*  echo "<pre>";
            print_r($user->toarray());
            print_r($user->save());
            die; */
            if ($user->save()) {
                $group_ids = $request->input('group_ids');
                $supervisor_id = $user->id;

                $this->assign_group_to_supervisor($group_ids,$supervisor_id);
                return redirect(route('supervisors.list'))->withSuccess('Supervisor created successfully.');
            }
        }

        $group_list = MachineGroup::select('id','group_name','supervisor_id')
        ->where([
            'cust_id' => Auth::id()
        ])->get()->toArray();
        /* print_r($group_list);
        die; */
        $next_supervisor_id = $this->Abettor->get_next_supervisor_id();
        
        return view('user.supervisor.create',[
            'user' => $request, 
            'supervisor_group_id' => 0,
            'next_supervisor_id' => $next_supervisor_id,
            'group_list' => $group_list,
            'action' => 'create',
        ]);
    }

    /**
     * Show the Supervisor create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update($supervisor_id = null, Request $request)
    {

        $user = User::find($supervisor_id);

        if (empty($user)) {
            return redirect(route('supervisors.list'))->withError('Oops!!! Invalid Supervisor');
        }

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),User::supervisor_update_rules($request->id),User::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            // Update Supervisor
            $user->first_name = $request->input('first_name');
            if (!empty($request->input('password_disp'))) {
                $user->password = Hash::make($request->input('password_disp'));
                $user->password_disp = $request->input('password_disp');
            }
            $user->contact_number_1 = $request->input('contact_number_1');
            $user->contact_number_2 = $request->input('contact_number_2');
            $user->email = $request->input('email');
            $user->address_1 = $request->input('address_1');
            $user->address_2 = $request->input('address_2');
            
            if ($user->save()) {
                $group_ids = $request->input('group_ids');
                $this->assign_group_to_supervisor($group_ids,$supervisor_id);

                return redirect(route('supervisors.list'))->withSuccess('Supervisor updated successfully.');
            }
        }


        /* $group_list = MachineGroup::select('id','group_name','supervisor_id')
        ->where([
            'cust_id' => Auth::id()
        ])
        ->whereIn('supervisor_id',[$supervisor_id,'0'])->get()->toArray(); */

        $group_list = MachineGroup::select('id','group_name','supervisor_id')
        ->where([
            'cust_id' => Auth::id()
        ])->get()->toArray();

        return view('user.supervisor.create',[
            'user' => $user,
            'group_list' => $group_list,
            'supervisor_group_id' => $supervisor_id,
            'action' => 'update',
        ]);
    }

    public function delete($id = null,Request $request)
    {
        $user = User::find($id);

        if (!$request->isMethod('post') || empty($user)) {
            return redirect(route('supervisors.list'))->withError('Oops!!! Invalid Supervisor');
        }
        if ($user->delete()) {
            $machineGroups = MachineGroup::where('supervisor_id','LIKE',"%$id%")->get();
            foreach ($machineGroups as $key => $machineGroup) {
                $groups = $machineGroup->supervisor_id;
                unset($groups[array_search($id,$groups)]);
                $groups = (count($groups) > 0) ? implode(',',$groups) : NULL;
                MachineGroup::where('id',$machineGroup->id)->update(['supervisor_id' => $groups]);
            }
            return redirect(route('supervisors.list'))->withSuccess('Supervisor deleted successfully.');
        }else{
            return redirect(route('supervisors.list'))->withError('Unable to delete Supervisor, Please try again laster.');
        }

    }


    /**
     * Change Supervisor status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function change_status($supervisor_id = null, $status_id = null, $inactivate_reason = null, Request $request)
    {

        $user = User::find($supervisor_id);

        if (empty($user)) {
            return redirect(route('supervisors.list'))->withError('Oops!!! Invalid Supervisor');
        }

        if (!in_array($status_id,[0,1])) {
            return redirect(route('supervisors.list'))->withError('Oops!!! Invalid Supervisor Status');
        }

        if ($request->isMethod('get')) {
            // Update Supervisor status
            $user->status_id = $status_id;
            $user->inactivate_reason = $inactivate_reason;
            if ($user->save()) {
                return redirect(route('supervisors.list'))->withSuccess('Supervisor status updated successfully.');
            }
        }

        return redirect(route('supervisors.list'))->withError('Invalid request.');
    }

    public function assign_group_to_supervisor($group_ids=[],$supervisor_id=0)
    {
        if (!empty($supervisor_id)) {

            // Reset Group Machines
            $machineGroups = MachineGroup::where(['cust_id' => Auth::id()])->get();
            echo "<pre>";
            // print_r($machineGroups->toarray());
            // print_r($group_ids);
            // print_r($supervisor_id);
            // die;

            // Assign MachineGroups to Group
            if (!empty($group_ids)) {
                foreach ($machineGroups as $key => $groupDetail) {
                    /* print_r($groupDetail->toarray());
                    continue; */
                    $machineGroup = MachineGroup::find($groupDetail->id);
                    if(in_array($groupDetail->id,$group_ids)){
                        // echo "EXIST<br>";
                        if($groupDetail->supervisor_id == NULL){
                            $machineGroup->supervisor_id = [$supervisor_id];
                        }else{
                            $supervisors = $groupDetail->supervisor_id;
                            if(is_array($supervisors) && !in_array($supervisor_id,$supervisors)){
                                $supervisors[] = $supervisor_id;
                                $machineGroup->supervisor_id = $supervisors;
                            }
                        }
                    }else{
                        // echo "NOT EXIST<br>";
                        if($groupDetail->supervisor_id != NULL){
                            $supervisors = $groupDetail->supervisor_id;
                            
                            if(is_array($supervisors) && in_array($supervisor_id,$supervisors)){
                                unset($supervisors[array_search($supervisor_id,$supervisors)]);
                                // echo "<br>Remove : $supervisor_id<br>";
                                // print_r($supervisors);
                                $machineGroup->supervisor_id = $supervisors;
                            }
                        }
                    }
                    // print_r($machineGroup->toarray());
                    $machineGroup->save();
                }
                // die;
            }   
        }
    }

    public function getPermission($supervisor_id)
    {
        
        $user = User::find($supervisor_id);
        /* echo "<pre>";
        print_r($supervisor->toarray());
        die; */
        $permissions = $user->permission;
       /*  echo "<pre>";
        print_r($permissions);
        die; */
        if (empty($user)) {
            return redirect(route('supervisors.list'))->withError('Oops!!! Invalid Supervisor');
        }
        return view('user.supervisor.permission',[
            'user' => $user,
            'permission' => $permissions
        ]);

    }

    public function updatePermission(Request $request,$id)
    {
        $user = User::find($id);
        $permission = $user->permission;
        // echo "<pre>";
        // print_r($request->except('_token'));
        // print_r($permission);
        // die;
        foreach ($permission as $key => $value) {
            if($key == "name" || $key == "total_user")
                continue;
            if(array_key_exists($key,$request->except('_token'))){
                $permission->{$key} = 1;
            }else{
                $permission->{$key} = 0;
            }
            
        }
        // print_r($permission);
        // die;
        $user->permission = $permission;
        $user->save();
        return redirect('/supervisors/list');
    }

        
}
