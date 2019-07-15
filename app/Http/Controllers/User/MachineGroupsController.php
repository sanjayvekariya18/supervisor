<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MachineGroup;
use App\Models\Machine;
use Validator;
use Auth;

class MachineGroupsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        // Searching
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('machine.groups.list.search','');
            return redirect(route('machine.groups.list'));
        }elseif ($request->_token) {
            $request->session()->put('machine.groups.list.search',$request->all());
            $search = $request->session()->get('machine.groups.list.search');
        }else{
            $search = $request->session()->get('machine.groups.list.search');
        }

        if (!empty($search)) {
            if (!empty($search['group_name'])) {
                $conditions[] = ['group_name',$search['group_name']];
            }
        }
        if(isAdmin()){
            $conditions[] = ['cust_id',Auth::id()];
        }else{
            $conditions[] = ['supervisor_id','LIKE','%'.Auth::id().'%'];
        }

        $machine_groups = MachineGroup::with('machine:group_id,machine_name,machine_number')->sortable(['created_at'=>'desc'])->where($conditions)->paginate(PAGE_LIMIT);

        return view('user.machine_groups.list',[
            'machine_groups' => $machine_groups,
            'machine_group_search' => $search,
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

            $validate = Validator::make($request->all(),MachineGroup::final_rules(),MachineGroup::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            // Save Machine Group
            $machine_group = new MachineGroup;
            $machine_group->group_name = $request->input('group_name');
            $machine_group->cust_id = Auth::id();
            if ($machine_group->save()) {
                $assigned_machines = $request->input('assigned_machines');
                $machine_id = $machine_group->id;

                $this->assign_machines_to_group($assigned_machines,$machine_id);
                return redirect(route('machine.groups.list'))->withSuccess('Machine Group created successfully.');
            }
        }

        $machines_list = Machine::select('id','machine_name','machine_number','group_id')
        ->where([
            'cust_id' => Auth::id(),
            'status_id' => 1,
            'group_id' => 0,
        ])
        ->orderByRaw('LENGTH(machine_id) ASC, machine_id ASC')
        ->get()->toArray();

        return view('user.machine_groups.create',[
            'request' => $request,
            'machines_list' => $machines_list,
            'machine_group_id' => 0,
            'action' => 'create',
        ]);
    }

    /**
     * Show the MachineGroup create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update($machine_group_id = null, Request $request)
    {

        $machine_group = MachineGroup::find($machine_group_id);

        if (empty($machine_group)) {
            return redirect(route('machine.groups.list'))->withError('Oops!!! Invalid Machine Group');
        }

        if ($request->isMethod('post')) {
            $validate = Validator::make($request->all(),MachineGroup::update_rules($request),MachineGroup::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            // Update Machine Group
            $machine_group->group_name = $request->input('group_name');
            
            if ($machine_group->save()) {

                $assigned_machines = $request->input('assigned_machines');
                $this->assign_machines_to_group($assigned_machines,$machine_group_id);

                return redirect(route('machine.groups.list'))->withSuccess('Machine Group updated successfully.');
            }
        }

        $machines_list = Machine::select('id','machine_name','machine_number','group_id')
        ->where([
            'cust_id' => Auth::id(),
            'status_id' => 1,
            'group_id' => 0,
        ])
        ->orderByRaw('LENGTH(machine_id) ASC, machine_id ASC')
        ->orWhere(['group_id' => $machine_group_id])->get()->toArray();

        return view('user.machine_groups.create',[
            'request' => $machine_group,
            'machines_list' => $machines_list,
            'machine_group_id' => $machine_group_id,
            'action' => 'update',
        ]);
    }

    public function assign_machines_to_group($machine_ids=[],$group_id=0)
    {
        if (!empty($group_id)) {

            // Reset Group Machines
            Machine::where(['group_id' => $group_id])->update(['group_id' => 0]);

            // Assign Machines to Group
            if (!empty($machine_ids)) {
                foreach ($machine_ids as $key => $machine_id) {
                    $machine = Machine::find($machine_id);
                    $machine->group_id = $group_id;
                    $machine->save();
                }
            }
        }

    }

    public function delete($id='')
    {
        $group = MachineGroup::find($id);

        if (empty($group)) {
            return redirect(route('machine.groups.list'))->withError('Oops!!! Invalid Group');
        }

        if ($group->delete()) {
            Machine::where('group_id',$id)->update(['group_id' => 0]);
            return redirect(route('machine.groups.list'))->withSuccess('Machine Group deleted successfully.');
        }else{
            return redirect(route('machine.groups.list'))->withError('Unable to delete Machine Group, Please try again laster.');
        }
    }

}
