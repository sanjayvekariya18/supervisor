<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;

class AdminController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    public function about(Request $request)
    {
        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),Admin::update_rules());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput()->withError('Unable to update profile');
            }

            // Update admin
            $admin = Admin::find(Auth::id());
            $admin->first_name = $request->input('first_name');
            $admin->last_name = $request->input('last_name');
            $admin->contact_number = $request->input('contact_number');
            $admin->email = $request->input('email');
            
            if ($admin->save()) {
                return redirect(route('admin.profile.about'))->withSuccess('Profile updated successfully.');
            }
        }

        $admin = Auth::user();
        return view('admin.admin.profile',[
            'admin' => $admin,
            'current_tab' => 'about',
        ]);
    }

    public function change_password(Request $request)
    {

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),admin::change_password($request));
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput()->withError('Unable to change password');
            }

            // Check for old password
            $old_password = $request->input('old_password');
            $current_pass = Auth::user()->password;

            if (Hash::check($old_password, $current_pass)) {
                
                $user = admin::find(Auth::id());
                $user->password = Hash::make($request->input('new_password'));
                
                if ($user->save()) {
                    return redirect(route('admin.profile.change.password'))->withSuccess('Password changed successfully.');
                }
            }else{
                return redirect()->back()->withError('Old password did not matched');
            }
        }
        return view('admin.admin.change_password',[
            'admin' => [],
            'current_tab' => 'change_password',
        ]);
    }
}
