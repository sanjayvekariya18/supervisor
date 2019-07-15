<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\WorkersController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AbettorHelper;
use App\Models\User;
use App\Models\Worker;
use App\Models\Machine;
use App\Models\ColorRange;

use Validator;
use Auth;

class CustomersAPIController extends Controller
{
    var $Abettor;

    public function __construct(AbettorHelper $AbettorHelper)
    {
        $this->Abettor = $AbettorHelper;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function login(Request $request)
    {

        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false
        ];

        if ($request->isMethod('post')) {
            
            $username = !empty($request->input('username')) ? $request->input('username') : null;
            $password = !empty($request->input('password')) ? $request->input('password') : null;

            if (!empty($username) && !empty($password)) {
                // Check Email
                $user =  User::where(['username' => $username])->get()->first()->toArray();

                $current_pass = !empty($user['password']) ? $user['password'] : '';
                if (!empty($current_pass) && (Hash::check($password, $current_pass))) {
                    unset($user['password']);

                    // Get Groups
                    if(!empty($user['parent_id'])){
                        $user['groups'] = $this->Abettor->get_group_by_supervisor($user['id']);
                    }else{
                        $user['groups'] = $this->Abettor->get_group_by_cust($user['id']);
                    }

                    // Check User status
                    if ($user['status_id'] == 1) {
                        $result = [
                            'messages'=>'Logged in successfully',
                            'status'=> true,
                            'data' => $user,
                        ];
                    }else{
                        $msg = !empty($user['inactive_reason']) ? 'Due to ' .$user['inactive_reason'] : '';
                        $result = [
                            'messages'=>'Your account is Not Activated ' . $msg,
                            'status'=> false,
                            'data' => []
                        ];
                    }
                }else{
                    $result = [
                        'messages'=>'Invalid Username or Password',
                        'status'=>false,
                        'data' => []
                    ];
                }
            }else{
                $result = [
                    'messages'=>'Invalid Username or Password',
                    'status'=>false,
                    'data' => []
                ];
            }
        }
        return response()->json($result);

    }

    /**
     * Provide Common Settings
     *
     * @return Common Settings
     */
    public function common_settings(Request $request)
    {
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false
        ];

        if ($request->isMethod('post')) {
            
            $result = [
                'messages'=>'Data processed successfully',
                'status'=>true,
                'data' => [
                    'shifts' => shifts(),
                    'range_colors' => range_colors(),
                    'day_shifts' => day_shifts(),
                    'night_shifts' => night_shifts(),
                    'buzz_time' => buzz_time(),
                    'buzz_blank_time' => buzz_blank_time(),
                    'default_buzz_time_minutes' => default_buzz_time_minutes(),
                    'default_buzz_time_seconds' => default_buzz_time_seconds()
                ]
            ];
        }
        return response()->json($result);
    }

    /**
     * Color Range Settings
     *
     * @param Request $request
     * @return $result
     */
    public function color_range(Request $request)
    {
        
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false,
            'data' => []
        ];

        if ($request->isMethod('post')) {
            
            $ranges_data = $request->all();
            $stitches = $request->input('stitches');
            $cust_id = $request->input('cust_id');

            if (empty($cust_id) || empty($stitches)) {
                $result = [
                    'messages'=>'Invalid Data',
                    'status'=>false,
                    'data' => [],
                ];

                return response()->json($result);
            }

            // Delete Old Ranges
            ColorRange::where('cust_id',$cust_id)->delete();

            foreach ($stitches as $key => $stitche) {
                $_stitches = explode(',',$stitche);

                $color_range = new ColorRange;
                $color_range->cust_id = $cust_id;
                $color_range->from_stitches = $_stitches[0];
                $color_range->to_stitches = $_stitches[1];
                $color_range->color_code = $_stitches[2];
                $color_range->save();

            }
            
            $result = [
                'messages'=>'Color ranges updated successfully.',
                'status'=>true,
                'data' => []
            ];

        }
        return response()->json($result);


    }

}
