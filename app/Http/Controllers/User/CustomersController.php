<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
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
        $this->middleware('auth');
    }

    public function about(Request $request)
    {
        if ($request->isMethod('post')) {
            $validate = Validator::make($request->all(),User::update_rules(Auth::id()),User::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput()->withError('Unable to update profile');
            }

            // Update Customer
            $user = User::find(Auth::id());
            $user->company_name = $request->input('company_name');
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->contact_number_1 = $request->input('contact_number_1');
            $user->contact_number_2 = $request->input('contact_number_2');
            $user->sms_notification_numbers = $request->input('sms_notification_numbers');
            $user->whatsapp_notification_numbers = $request->input('whatsapp_notification_numbers');
            $user->email = $request->input('email');
            $user->address_1 = $request->input('address_1');
            $user->address_2 = $request->input('address_2');
            
            if ($user->save()) {
                return redirect(route('customers.profile.about'))->withSuccess('Profile updated successfully.');
            }
        }

        $customer = Auth::user();
        return view('user.customers.profile',[
            'customer' => $customer,
            'current_tab' => 'about',
        ]);
    }

    public function change_password(Request $request)
    {
        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),User::change_password($request));
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput()->withError('Unable to change password');
            }

            // Check for old password
            $old_password = $request->input('old_password');
            $current_pass = Auth::user()->password;

            if (Hash::check($old_password, $current_pass)) {
                
                $user = User::find(Auth::id());
                $user->password = Hash::make($request->input('new_password'));
                $user->password_disp = $request->input('new_password');
                
                if ($user->save()) {
                    return redirect(route('customers.profile.change.password'))->withSuccess('Password changed successfully.');
                }
            }else{
                return redirect()->back()->withError('Old password did not matched');
            }
        }
        return view('user.customers.change_password',[
            'customer' => [],
            'current_tab' => 'change_password',
        ]);
    }

}
