<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\Machine;
use App\Models\Setting;
class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function setHead(Request $request)
    {
        $user = Admin::find(\Auth::id());
        $setting = \Auth::user()->setting;
        /* echo "<pre>";
        print_r($request->all());
        die; */
        if($request->isMethod('get')){
            if(isset($setting->head)){
                $head =  $setting->head;
            }else{
                $head = (object) [
                    "min_head" => "",
                    "type" => ""
                ];
            }
            return view('admin.setting.head_setting',[
                    'head' => $head
                ]);
        }else{
            $setting->head = $request->head;
            $user->setting = $setting;
            $user->save();
            return redirect('admin/setting/head_setting');
        }
    }

    public function machineSetting(Request $request)
    {
        if($request->isMethod('get')){
            $setting = Setting::where('name','MACHINE_SETTINGS')->first();
            $settingData = json_decode($setting->value);
            return view('admin.setting.machine_setting',compact('settingData'));
        }else{
            $setting = Setting::where('name','MACHINE_SETTINGS')->firstOrFail();
            $setting->value = json_encode($request->settings);
            $setting->save();
            return redirect('admin/setting/machine_setting');           
        }    
    }

    public function shiftSetting(Request $request)
    {

        if($request->isMethod('get')){
            $users = User::where('parent_id',NULL)->get();
            return view('admin.setting.shift_setting',compact('users'));
        }else{
            $request->validate([
                'user_id' => 'required',
                'shift' => 'required|numeric'
            ]);
            // echo "<pre>";
            // print_r($request->all());
            // die;
            $conditions = [];
            $conditions[] = ['cust_id',$request->user_id];
            if($request->filled('machine_id')){
                $conditions[] =['id',$request->machine_id];
            }
            // print_r($conditions);
            // die;
            Machine::where($conditions)->update([
                "shift_change" => $request->shift
            ]);
            // die("updated");
            return redirect('admin/setting/shift_setting')->withSuccess('Shift Change Apply.');           
        }
    }

    public function getMachines($user_id)
    {
        $machines = Machine::select('id','machine_number')
                    ->where('cust_id',$user_id)
                    ->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC')
                    ->get();
        return response()->json($machines);
    }

    public function FunctionName(Type $var = null)
    {
        # code...
    }

}
