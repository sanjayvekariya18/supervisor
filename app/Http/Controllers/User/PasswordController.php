<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

use Validator;
use Auth;
use Illuminate\Support\Facades\Hash;
class PasswordController extends Controller
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

    public function changePassword(Request $request)
    {
       
        if($request->isMethod('get')){
            return view('user.password.change_password');
        }else{
            $user = User::findorfail(Auth::id());
            if (Hash::check($request->oldPassword, $user->password)) {
                $user->password = Hash::make($request->newPassword);
                $user->password_disp = $request->newPassword;
                $user->save();
                return redirect('password/change_password')->withSuccess('Password Updated');
            }else{
                return redirect('password/change_password')->withError('Old Password Is Wrong');
            }    
        }    
    }
}
