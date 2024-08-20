<?php

namespace App\Http\Controllers\Admin\Auth;

use App\User;
use Auth;
use Hash;
use Validator;
use JsValidator;
use Session;
use App\Commons\AppMailer; 
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use App\Models\Admin\Admin;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */
   

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = 'admin/';  
    protected $guard = 'admin';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('adminguest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255', 
            'email' => 'required|email|max:255|super_unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }


 
	/**
     * Perform the registration.
     *
     * @param  Request   $request
     * @param  AppMailer $mailer
     * @return \Redirect
     */
	public function register(Request $request, AppMailer $mailer)
	{
		  
		/*$validator = $this->validator($request->all());
		if ($validator->fails())
		{
			$this->throwValidationException(
				$request, $validator
			);
		}
		$user = $this->create($request->all()); 
		$mailer->sendEmailConfirmationTo($user);*/
		return abort(404); 
	}
	
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'com_name' => $data['com_name'],
            'user_type' => 1,
            'password' => $data['password'],
        ]);
    }
	
	 /**
     * Confirm a user's email address.
     *
     * @param  string $token
     * @return mixed
     */
    public function confirmEmail($token)
    {
        $userData = Admin::whereToken($token)->firstOrFail(); 
        Auth::guard($this->getGuard())->loginUsingId($userData->id);
        $userData->token = '';
        $userData->save();
        return redirect('admin/change-password');
    }
	
	
	 /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();
        if ($throttles && $lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        $credentials = $this->getCredentials($request);
        $userData = Admin::where('email',$credentials['email'])->where('status','<>',2)->get(['id','status','verified'])->first();
        if(!empty($userData) && ($userData->status == 0  &&  $userData->verified != 1)){
            return redirect()->back()
                ->withInput($request->only($this->loginUsername(), 'remember'))
                ->withErrors([
                    $this->loginUsername() => 'Email address not confirmed, Please confirm your email!',
                ]);             
        } else if(isset($userData->status) && $userData->status == 0 ) {
            return redirect()->back()
                ->withInput($request->only($this->loginUsername(), 'remember'))
                ->withErrors([
                    $this->loginUsername() => 'This account has been suspended!',
                ]);
        }
		$credentials['status'] = 1; 
		 
		 
		
        if (Auth::guard($this->getGuard())->attempt($credentials, $request->has('remember'))) {
        	 
			if(Auth::guard($this->getGuard())->user()->status == '1'){ 
            	return $this->handleUserWasAuthenticated($request, $throttles);
			}else{
				return redirect()->back()
	            ->withInput($request->only($this->loginUsername(), 'remember'))
	            ->withErrors([
	                $this->loginUsername() => 'Email address not confirmed, Please confirm your email!',
	            ]);	
			}
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles && ! $lockedOut) {
            $this->incrementLoginAttempts($request);
        }
        return $this->sendFailedLoginResponse($request);
    }

	/**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $throttles
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request, $throttles)
    {

        if ($throttles) {
            $this->clearLoginAttempts($request);
        }
        if (method_exists($this, 'authenticated')) {
            return $this->authenticated($request, Auth::guard($this->getGuard())->user());
        }
        if(Auth::guard($this->getGuard())->user()->is_need_change_pass == 1) {
            $id = Auth::guard($this->getGuard())->user()->id;
            Admin::where('id',$id)->update(['is_need_change_pass'=>0]);
            return redirect('admin/change-password');
        } 
        $url = Session::get('extend_url');
        if(isset($url) && !empty($url)) {
            Session::forget('extend_url');
            return redirect($url);
        } else {
            if(!isset(Auth::guard($this->getGuard())->user()->choosed_timezone)  || empty(Auth::guard($this->getGuard())->user()->choosed_timezone)) {
                return redirect('admin/myprofile'); 
            }
            return redirect($this->redirectTo);
        }
    }

	/**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $rules = array(
            'email' => 'required|check_email|email',
            'password' => 'required',
        );
        $validator = JsValidator::make($rules); 
        return view('admin.auth.login', ['validator'=>$validator,'page_title' => 'Login']);
    } 
	
	
	
	  /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $validator = JsValidator::validator(
            $this->validator([])
        );
        return view('admin.auth.register', ['validator'=>$validator]);
    } 

	
	public function logout(){ 
		Auth::guard($this->getGuard())->logout();
		return redirect('admin/login');
	}
}
