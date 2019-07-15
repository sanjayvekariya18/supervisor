<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index',[
            'permissions' => $permissions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = new Permission;
       /*  echo "<pre>";
        print_r($request->toarray());
        die; */
        $permission->name = $request->name;
        $permission->total_user = ($request->has('total_user')) ? $request->total_user : 0;
        $permission->machine_name = ($request->has('machine_name')) ? $request->machine_name : 0;
        $permission->machine_no = ($request->has('machine_no')) ? $request->machine_no : 0;
        $permission->worker_account = ($request->has('worker_account')) ? $request->worker_account : 0;
        $permission->machine_group = ($request->has('machine_group')) ? $request->machine_group : 0;
        $permission->bonus = ($request->has('bonus')) ? $request->bonus : 0;
        $permission->disconnect_machine = ($request->has('disconnect_machine')) ? $request->disconnect_machine : 0;
        $permission->range_color = ($request->has('range_color')) ? $request->range_color : 0;
        $permission->report_hour_12 = ($request->has('report_hour_12')) ? $request->report_hour_12 : 0;
        $permission->report_hour_6 = ($request->has('report_hour_6')) ? $request->report_hour_6 : 0;
        $permission->report_hour_3 = ($request->has('report_hour_3')) ? $request->report_hour_3 : 0;
        $permission->report_min_5 = ($request->has('report_min_5')) ? $request->report_min_5 : 0;
        $permission->report_avg = ($request->has('report_avg')) ? $request->report_avg : 0;
        $permission->report_avg_weekly = ($request->has('report_avg_weekly')) ? $request->report_avg_weekly : 0;
        $permission->report_total = ($request->has('report_total')) ? $request->report_total : 0;
        $permission->report_salary = ($request->has('report_salary')) ? $request->report_salary : 0;
        $permission->setting = ($request->has('setting')) ? $request->setting : 0;
        $permission->head = ($request->has('head')) ? $request->head : 0;
        $permission->tb = ($request->has('tb')) ? $request->tb : 0;
        $permission->max_rpm = ($request->has('max_rpm')) ? $request->max_rpm : 0;
        $permission->live_rpm = ($request->has('live_rpm')) ? $request->live_rpm : 0;
        $permission->stop_time = ($request->has('stop_time')) ? $request->stop_time : 0;
        $permission->last_stop_time = ($request->has('last_stop_time')) ? $request->last_stop_time : 0;
        $permission->buzzer = ($request->has('buzzer')) ? $request->buzzer : 0;
        $permission->shift = ($request->has('shift')) ? $request->shift : 0;
        $permission->supervisor = ($request->has('supervisor')) ? $request->supervisor : 0;
        $permission->remark = ($request->has('remark')) ? $request->remark : 0;
        $permission->phone = ($request->has('phone')) ? $request->phone : 0;
        $permission->camera = ($request->has('camera')) ? $request->camera : 0;
        $permission->save();

        return redirect('admin/permission');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::find($id);
        return view('admin.permissions.edit',[
            'permission' => $permission
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);
        /* echo "<pre>";
        print_r($request->toarray());
        die; */
        $permission->name = $request->name;
        $permission->total_user = ($request->has('total_user')) ? $request->total_user : 0;
        $permission->machine_name = ($request->has('machine_name')) ? $request->machine_name : 0;
        $permission->machine_no = ($request->has('machine_no')) ? $request->machine_no : 0;
        $permission->worker_account = ($request->has('worker_account')) ? $request->worker_account : 0;
        $permission->machine_group = ($request->has('machine_group')) ? $request->machine_group : 0;
        $permission->bonus = ($request->has('bonus')) ? $request->bonus : 0;
        $permission->disconnect_machine = ($request->has('disconnect_machine')) ? $request->disconnect_machine : 0;
        $permission->range_color = ($request->has('range_color')) ? $request->range_color : 0;
        $permission->report_hour_12 = ($request->has('report_hour_12')) ? $request->report_hour_12 : 0;
        $permission->report_hour_6 = ($request->has('report_hour_6')) ? $request->report_hour_6 : 0;
        $permission->report_hour_3 = ($request->has('report_hour_3')) ? $request->report_hour_3 : 0;
        $permission->report_min_5 = ($request->has('report_min_5')) ? $request->report_min_5 : 0;
        $permission->report_avg = ($request->has('report_avg')) ? $request->report_avg : 0;
        $permission->report_avg_weekly = ($request->has('report_avg_weekly')) ? $request->report_avg_weekly : 0;
        $permission->report_total = ($request->has('report_total')) ? $request->report_total : 0;
        $permission->report_salary = ($request->has('report_salary')) ? $request->report_salary : 0;
        $permission->setting = ($request->has('setting')) ? $request->setting : 0;
        $permission->head = ($request->has('head')) ? $request->head : 0;
        $permission->tb = ($request->has('tb')) ? $request->tb : 0;
        $permission->max_rpm = ($request->has('max_rpm')) ? $request->max_rpm : 0;
        $permission->live_rpm = ($request->has('live_rpm')) ? $request->live_rpm : 0;
        $permission->stop_time = ($request->has('stop_time')) ? $request->stop_time : 0;
        $permission->last_stop_time = ($request->has('last_stop_time')) ? $request->last_stop_time : 0;
        $permission->buzzer = ($request->has('buzzer')) ? $request->buzzer : 0;
        $permission->shift = ($request->has('shift')) ? $request->shift : 0;
        $permission->supervisor = ($request->has('supervisor')) ? $request->supervisor : 0;
        $permission->remark = ($request->has('remark')) ? $request->remark : 0;
        $permission->phone = ($request->has('phone')) ? $request->phone : 0;
        $permission->camera = ($request->has('camera')) ? $request->camera : 0;
        $permission->save();
        
        // echo "<pre>";
        // $permissions  = $request->except(['_token','_method']);
        // echo json_encode($permission);
        // die;
        // $users = User::where('permission_id',$permission->id)->get();
        // print_r($permissions);
        // die;
        // $request->except('_token');
        $userPermission = [];
        $supPermission = [];
        foreach ($request->except(['_token','_method']) as $key => $value) {
            if($key == "name" || $key == "total_user"){
                $userPermission[$key] = $value;
                $supPermission[$key] = $value;
                continue;
            }else{
                $userPermission[$key] = 1;
                $supPermission[$key] = 0;
            }
        }
        /* print_r($userPermission);
        print_r($supPermission);
        die; */
        
        User::where('permission_id',$permission->id)->update([
            'permission' => json_encode($userPermission)
        ]);
        $users = User::where('permission_id',$permission->id)->get();
        foreach ($users as $key => $user) {
            User::where('parent_id',$user->id)->update([
                'permission' => json_encode($supPermission)
            ]);
        }
        return redirect('admin/permission');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
