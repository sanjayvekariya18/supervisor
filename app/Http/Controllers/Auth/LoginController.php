<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use File;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.user.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->guard()->validate($this->credentials($request))) {
            $user = $this->guard()->getLastAttempted();

            // Check if user is Active
            if (($user->status_id==1) && ($this->attemptLogin($request))) {
                if($request->wantsJson()){
                    File::append(public_path('/_log/api.log'),date('Y-m-d H:i:s').json_encode($request->all())."\n");
                    $user->generateToken($request->input('token',NULL),$request->input('device_type',NULL));
                    $response['success'] = true;
                    $response['output'] = "";
                    $response['data'] = $user;
                    return response()->json($response, 200);
                }else{
                    return $this->sendLoginResponse($request);
                }
            }else{
                if($request->wantsJson()){
                    $msg = (!empty($user->inactivate_reason)) ? ' Due to ' . $user->inactivate_reason : '';

                    $response['success'] = false;
                    $response['output'] = "Your account is Not Activated" . $msg;
                    $response['data'] = [];
                    return response()->json($response, 200);
                }else{
                    // when User is not activated
                    $msg = '';
                    if (!empty($user->inactivate_reason)) {
                        $msg = 'Due to ' . $user->inactivate_reason;
                    }
                    return redirect()->back()->withInput($request->only('username'))->withError('Your account is Not Activated ' . $msg);
                }
            }

        }
        if($request->wantsJson()){
            $response['success'] = false;
            $response['output'] = "Invalid Username or password, Please try again";
            $response['data'] = [];
            return response()->json($response, 200);
        }else{
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            return redirect()->back()->withInput($request->only('username'))->withError('Invalid Username or password, Please try again');
        }
    }

    public function userLogout(Request $request)
    {
        $user = \Auth::guard('api')->user();
        if ($user) {
            $user->api_token = null;
            $user->token = null;
            $user->save();
        }
        $response['success'] = true;
        $response['output'] = "User logged out.";
        $response['data'] = [];
        return response()->json($response, 200);
    }

}
