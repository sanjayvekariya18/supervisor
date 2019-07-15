<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Machine;
use App\Models\SmsRechargeHistory;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Helpers\CustomerHelper AS CH;
use Auth;

class CustomersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {

        // Searching
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('admin.customer.list.search','');
            return redirect(route('admin.customers.list'));
        }elseif ($request->_token) {
            $request->session()->put('admin.customer.list.search',$request->all());
            $search = $request->session()->get('admin.customer.list.search');
        }else{
            $search = $request->session()->get('admin.customer.list.search');
        }

        $conditions = [];
        $conditions[] = ['parent_id',NULL];
        if (!empty($search)) {
            if (!empty($search['id'])) {
                $conditions[] = ['id','LIKE','%'.$search['id'].'%'];
            }
        }

        $customers = User::sortable(['created_at'=>'desc'])->where($conditions)->paginate(PAGE_LIMIT);

        return view('admin.customers.list',[
            'customers' => $customers,
            'customer_search' => $search,
        ]);
    }

    /**
     * Show the Customer create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),User::final_rules(),User::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }
            
            // Save Customer
            $user = new User;
            $user->id = strtoupper($request->input('id'));
            $user->username = $request->input('username');
            $user->permission_id = $request->input('permission_id');
            $user->permission = Permission::find($user->permission_id);
            $user->status_id = 0;
            $user->company_name = $request->input('company_name');
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->password = Hash::make($request->input('password_disp'));
            $user->password_disp = $request->input('password_disp');
            $user->contact_number_1 = $request->input('contact_number_1');
            $user->contact_number_2 = $request->input('contact_number_2');
            $user->sms_notification_numbers = $request->input('sms_notification_numbers');
            $user->whatsapp_notification_numbers = $request->input('whatsapp_notification_numbers');
            $user->email = $request->input('email');
            $user->address_1 = $request->input('address_1');
            $user->address_2 = $request->input('address_2');
            
            if ($user->save()) {
                $message = "Dear $user->first_name,\nWe are happy and thankful to you for being a valued customer of 'Supervisor 0.7'";
                sendMessage($user->contact_number_1,$message);
                return redirect(route('admin.customers.list'))->withSuccess('Customer created successfully.');
            }
        }

        return view('admin.customers.create',[
            'permissions' => Permission::all(),
            'user' => $request, 
            'action' => 'create',
        ]);
    }

    /**
     * Show the Customer create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update($user_id = null, Request $request)
    {

        $user = User::find($user_id);

        if (empty($user)) {
            return redirect(route('admin.customers.list'))->withError('Oops!!! Invalid Customer');
        }

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),User::update_rules($user_id),User::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            // Update Customer
            $user->permission_id = $request->input('permission_id');
            $user->permission = Permission::find($user->permission_id);
            $user->company_name = $request->input('company_name');
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            if (!empty($request->input('password_disp'))) {
                $user->password = Hash::make($request->input('password_disp'));
                $user->password_disp = $request->input('password_disp');
            }
            $user->contact_number_1 = $request->input('contact_number_1');
            $user->contact_number_2 = $request->input('contact_number_2');
            $user->sms_notification_numbers = $request->input('sms_notification_numbers');
            $user->whatsapp_notification_numbers = $request->input('whatsapp_notification_numbers');
            $user->email = $request->input('email');
            $user->address_1 = $request->input('address_1');
            $user->address_2 = $request->input('address_2');
            
            if ($user->save()) {
                return redirect(route('admin.customers.list'))->withSuccess('Customer updated successfully.');
            }
        }

        return view('admin.customers.create',[
            'permissions' => Permission::all(),
            'user' => $user,
            'action' => 'update',
        ]);
    }

    public function delete($id,Request $request)
    {
        $verifyOTP = \Session::get('OTP');
        if($verifyOTP == $request->otp){
            User::destroy($id);
            \Session::forget('OTP');
            $response = ["status" => true,"msg" => "Customer Deleted"];
        }else{
            $response = ["status" => false,"msg" => "Invalid OTP"];
        }
        return response()->json($response);
    }

    /**
     * Change Customer status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function change_status($customer_id = null, $status_id = null, $inactivate_reason = null, Request $request)
    {

        $user = User::find($customer_id);

        if (empty($user)) {
            return redirect(route('admin.customers.list'))->withError('Oops!!! Invalid Customer');
        }

        if (!in_array($status_id,[0,1])) {
            return redirect(route('admin.customers.list'))->withError('Oops!!! Invalid Customer Status');
        }

        if ($request->isMethod('get')) {
            // Update Customer status
            $user->status_id = $status_id;
            $user->inactivate_reason = $inactivate_reason;
            if ($user->save()) {
                return redirect(route('admin.customers.list'))->withSuccess('Customer status updated successfully.');
            }
        }

        return redirect(route('admin.customers.list'))->withError('Invalid request.');
    }

    /**
     * Customer General Settings
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function general_settings($customer_id = null, Request $request)
    {

        $customer = User::find($customer_id);

        if (empty($customer)) {
            return redirect(route('admin.customers.list'))->withError('Oops!!! Invalid Customer');
        }

        if ($request->isMethod('post')) {

            // Update Customer Settings
            $sms_notification_status = !empty($request->input('sms_notification_status')) ? true : false;
            $whatsapp_notification_status = !empty($request->input('whatsapp_notification_status')) ? true : false;
            $disconnect_alert = !empty($request->input('disconnect_alert')) ? $request->input('disconnect_alert') : '0';

            $customer->sms_notification_status = $sms_notification_status;
            $customer->whatsapp_notification_status = $whatsapp_notification_status;
            $customer->disconnect_alert = $disconnect_alert;
            if ($customer->save()) {

                $flag = Machine::where([
                    'cust_id' => $customer_id
                ])->update([
                    'disconnect_alert' => $disconnect_alert
                ]);

                return redirect(route('admin.customers.settings.general_settings',$customer_id))->withSuccess('General settings updated successfully.');
            }
        }

        return view('admin.customers.settings.general_settings',[
            'current_tab' => 'general_settings',
            'customer' => $customer,
        ]);
    }

    public function shift_change($customer_id = null, Request $request)
    {
        $customer = User::find($customer_id);

        if (empty($customer)) {
            return redirect(route('admin.customers.list'))->withError('Oops!!! Invalid Customer');
        }

        if ($request->isMethod('post')) {
            

            $customer->day_shift = $request->input('day_shift');
            $customer->night_shift = $request->input('night_shift');
            if($customer->save()){

                $flag = Machine::where([
                    'cust_id' => $customer_id
                ])->update([
                    'day_shift' => $request->input('day_shift'),
                    'night_shift' => $request->input('night_shift'),
                ]);

                return redirect(route('admin.customers.settings.shift.change',$customer_id))->withSuccess('Shift changed successfully.');
            }else{
                return redirect(route('admin.customers.settings.shift.change',$customer_id))->withError('Unable to change Shift, please try again later.');
            }
        
            
        }

        $day_shift = $customer->day_shift;
        $night_shift = $customer->night_shift;

        return view('admin.customers.settings.shift_change',[
            'current_tab' => 'shift_change',
            'customer' => $customer,
            'day_shift' => $day_shift,
            'night_shift' => $night_shift
        ]);
    }

    /**
     * Customer SMS Recharge
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function sms_recharge($customer_id = null, Request $request)
    {


        $customer = User::find($customer_id);

        if (empty($customer)) {
            return redirect(route('admin.customers.list'))->withError('Oops!!! Invalid Customer');
        }

        $recharge_history = SmsRechargeHistory::with('admin:id,first_name,last_name')->where(['cust_id'=>$customer_id])->orderBy('updated_at','desc')->get();

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),SmsRechargeHistory::admin_rules());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            $sms_recharge = new SmsRechargeHistory;
            $sms_recharge->cust_id = $customer_id;
            $sms_recharge->transaction_type = $request->input('transaction_type');
            $sms_recharge->sms_volume = $request->input('sms_volume');
            $sms_recharge->note = $request->input('note');
            $sms_recharge->activity_by = Auth::id();
            if ($sms_recharge->save()) {
                CH::adjust_sms($customer_id);
                if ($request->input('transaction_type') == 'credit') {
                    $transaction_type = 'credited';
                }else{
                    $transaction_type = 'debited';
                }
                return redirect(route('admin.customers.settings.sms_recharge',$customer_id))->withSuccess('SMS '.$transaction_type.' successfully.');
            }
        }

        return view('admin.customers.settings.sms_recharge',[
            'current_tab' => 'sms_recharge',
            'customer' => $customer,
            'recharge_history' => $recharge_history,
        ]);
    }

    public function reports_settings($customer_id = null, Request $request)
    {
        $customer = User::find($customer_id);

        if (empty($customer)) {
            return redirect(route('admin.customers.list'))->withError('Oops!!! Invalid Customer');
        }
        $columns_skeleton = columns_skeleton();

        if ($request->isMethod('post')) {

            $tmp_data = $columns_skeleton;
            $report_array = $request->input('report');

            $customer->reports_settings = json_encode($report_array);
            $customer->save();
            return redirect(route('admin.customers.settings.reports_settings',$customer_id))->withSuccess('Reports Setting saved successfully.');
        }
        $reports_settings = !empty($customer['reports_settings']) ? json_decode($customer['reports_settings'],1) : [];


        // Fill User settings
        foreach ($columns_skeleton as $report_name => $report_fields) {
            
            // Check if Key exist
            if (!empty($reports_settings[$report_name])) {

                foreach ($report_fields as $field_key => $field_value) {

                    if (!empty($reports_settings[$report_name][$field_key])) {
                        $columns_skeleton[$report_name][$field_key] = 1;
                    }else{
                        $columns_skeleton[$report_name][$field_key] = 0;
                    }
                }
            }
        }

        return view('admin.customers.settings.reports_settings',[
            'current_tab' => 'reports_settings',
            'customer' => $customer,
            'reports_settings' => $columns_skeleton,

        ]);
    }

    public function sendOTP()
    {
        $admin = Admin::find(1);
        $response = [];
        if(!$admin || $admin->contact_number == NULL){
            $response = ["status" => false,"msg" => "Contact number not set"];
             return response()->json($response);
        }
        $six_digit_random_number = mt_rand(100000, 999999);
        \Session::put('OTP',$six_digit_random_number);
        $message ="Dear Admin, your OTP for Deleting Customer is $six_digit_random_number.\nUse this password to validate your request";
        $res = sendMessage($admin->contact_number,$message);
        if($res == "success"){
            $response = ["status" => true,"msg" => ""];
        }else{
            $response = ["status" => true,"msg" => $res];
        }
        return response()->json($response);
    }    
}
