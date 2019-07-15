<?php

namespace App\Http\Controllers\Auth\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }

    public function forgotPassword(Request $request){
        $user = User::where('contact_number_1',$request->mobile)->orWhere('contact_number_1',$request->mobile)->first();
        if($user){
            $plainPassword = $this->generate_string($this->permitted_chars,5);
            $password = Hash::make($plainPassword);
            $user->password_disp = $plainPassword;
            $user->password = $password;
            $user->save();
            sendMessage($request->mobile,"Your password is : $plainPassword");
            return redirect("login")->withSuccess("Password successfully send");
        }else{
            return redirect("login")->withError("Mobile number not registered");
        }
    }   
 
    function generate_string($input, $strength = 16) {
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }    
        return $random_string;
    }
}
