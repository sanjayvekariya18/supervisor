<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ColorRange;
use App\Models\User;
use App\Models\Machine;
use App\Helpers\AbettorHelper;

use Validator;
use Auth;
class SettingsController extends Controller
{
    var $Abettor;
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

    public function color_range(Request $request)
    {
        $range_colors = range_colors();

        if ($request->isMethod('post')) {
            $ranges_data = $request->all();
            $count = count($request->input('from_stitches'));

            // Delete Old Ranges
            ColorRange::where('cust_id',Auth::id())->delete();

            for ($i=0; $i < $count; $i++) { 
                $color_range = new ColorRange;
                $color_range->cust_id = Auth::id();
                $color_range->from_stitches = $ranges_data['from_stitches'][$i];
                $color_range->to_stitches = $ranges_data['to_stitches'][$i];
                $color_range->color_code = $ranges_data['color_code'][$i];
                $color_range->save();
            }
            
            return redirect(route('settings.color.range'))->withSuccess('Color ranges created successfully.');
        }

        $data = ColorRange::where('cust_id',Auth::id())->orderBy('id','ASC')->get();

        return view('user.settings.color_range',[
            'data' => $data,
            'current_tab' => 'color_range',
            'range_colors' => $range_colors
        ]);
    }

    public function supervisor_permissions(Request $request)
    {
        if(auth()->user()->permission->supervisor){
            
            $permissions = supervisor_permissions();

            if ($request->isMethod('post')) {
                
                $permissions = array_merge($permissions,$request->all());
                unset($permissions['_token']);
                $permissions = json_encode($permissions);

                $update_flag = User::where('parent_id',Auth::id())->orWhere('id',Auth::id())->update(['permissions'=>$permissions]);

                if ($update_flag) {
                    return redirect(route('settings.supervisor.permissions'))->withSuccess('Supervisor permission updated successfully.');
                }else{
                    return redirect(route('settings.supervisor.permissions'))->withError('Unable to update Supervisor permission, Please try again later.');
                }

            }

            $current_permissions = json_decode(Auth::user()->permissions,1);

            if (!empty($current_permissions)) {
                $permissions = array_merge($permissions,$current_permissions);
            }
        
            return view('user.settings.supervisor_permissions',[
                'data' => $permissions,
                'current_tab' => 'supervisor_permissions',
            ]);
            
        }else{
            return redirect('/');
        }
    }  
    
    public function machineSetting(Request $request)
    {
        if($request->isMethod('get')){
            $machine_list = $this->Abettor->get_machines_data('all',Auth::id());
            $machine_setting_list = array();
            foreach($machine_list as $machine){
                $machine_setting_list[$machine['machine_number']] = array(
                    'machine_number' => $machine['machine_number'],
                    'settings' => json_decode($machine['settings'])
                );
            }     
            /* echo "<pre>";
            print_r($machine_setting_list);
            die;   */     
            return view('user.settings.machine_setting',compact('machine_setting_list'));
        }else{
            $cust_id = Auth::id();
            $settingsData = $request->settings;

            foreach($settingsData as $machine_number => $setting){
                $machineSetting = Machine::where('cust_id',Auth::id())->where('machine_number',$machine_number)->firstOrFail();
                $machineSetting->settings = json_encode($setting);
                $machineSetting->save();                 
            }
            return redirect('settings/machine_setting');
        }    
    }

}
