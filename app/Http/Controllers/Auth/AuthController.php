<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Auth;
use Hash;
use Validator;
use JsValidator;
use App\Commons\AppMailer; 
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Socialite;
use Response;
use Illuminate\Support\Facades\Input;
use Crypt;
use App\Models\Front\Invite;
use Session;
use App\Commons\SettingVars;
use DB;
use App\Models\Front\CancelAccount;
use App\Commons\UserBalance;
use App\Models\Admin\Setting;
use App\Models\Admin\CouponLog;
use App\Models\Admin\Coupon;

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
    protected $redirectTo = '/';  
    protected $guard = 'web';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
		$this->settingData = SettingVars::getVars(); 
 
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
            'com_name' => 'required|max:255',
            'email' => 'required|max:255|super_unique:users|check_email',
            'password' => 'required|min:8|regex:/^(?=\S*[a-z A-Z])(?=\S*[\d])\S*$/|confirmed' 
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
		  
		$validator = $this->validator($request->all());
		if ($validator->fails())
		{
			$this->throwValidationException(
				$request, $validator
			);
		} else {
            if(isset($request->promotion_code)) {
                $couponApply = $this->check_coupon($request->promotion_code);
                //preF($couponApply);
                if(!empty($couponApply)  && !isset($couponApply['success']) && empty($couponApply['success'])) {
                    $error_type = array_keys($couponApply);
                    //preF($error_type);
                    return redirect()->back()->withInput($request->all($request->all, 'remember'))->withErrors(['promotion_code' => $couponApply[$error_type[0]]]);             
                }
            }
        }
        // set default value 
        $request->noti_mob_frequency = 90;
        $request->noti_email_frequency = 90;
        $request->noti_email_followup_frequency = 1;
        $request->noti_mob_followup_frequency    = 1;
        $request->show_noti_msg = 1;
        //end 
		$user = $this->create($request->all()); 
        if(isset($couponApply['success'])) {
           $this->apply_coupon($request->promotion_code,$user);
        }
		$mailer->sendEmailConfirmationTo($user);
		return redirect('thankyou-signup'); 
	}
	
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {   
        
        $setName = $this->setUserName($data['name']);
        $createUserData = array( 'name' => $data['name'],'email' => $data['email'],'com_name' => $data['com_name'],'user_type' => 1,'password' => $data['password'],'first_name'=>$setName['firstName'],'last_name'=>$setName['lastName'],'noti_mob_frequency' => 90,'noti_email_frequency'=>90, 'noti_email_followup_frequency'=>1,'noti_mob_followup_frequency'=>1,'show_noti_msg'=>1,'is_need_change_pass' => 1);

        if(isset($data['google_id'])){
            $createUserData['google_id'] = $data['google_id'];
        }
        if(isset($data['profile_image'])){
            $createUserData['profile_image'] = $data['profile_image'];
        } 

        //insert trial expiry date
        if(isset($this->settingData['trial_duration'])){
            $createUserData['trial_start_date'] = date('Y-m-d');
            $createUserData['trial_end_date'] = date('Y-m-d',strtotime("+".$this->settingData['trial_duration']." day"));
        }

        $user = User::create($createUserData);
        if(!empty(Session::get('user_ref')) && Session::get('user_ref')){
            $this->changeInvitedFriendStatus($data['email'],$user->id);
        }
        if($user){ 
            $this->insertDefaultCustomField($user->id); //insert default customize fields to users
        }
        return $user;
    }
	
     /**
     * Insert default custom fields to user
     *
     * @param  string $user id
     * @return null
     */
    public function  insertDefaultCustomField($userID){
        $defaultCustomFields = DB::table('default_customize_fields')->where('status', '1')->get();

        $toInsertData = array();
        foreach($defaultCustomFields as $cfields){
            $toInsertData[] = array('type'=>$cfields->type,'name'=>$cfields->name,'is_permanent'=>$cfields->is_permanent,'display_order'=>$cfields->display_order,'owner_id'=>$userID,'status'=>1);
        }

        if(!empty($toInsertData)){ 
            DB::table('user_customize_fields')->insert($toInsertData);
        }

    }

	 /**
     * Confirm a user's email address.
     *
     * @param  string $token
     * @return mixed
     */
    public function confirmEmail($token)
    {
        $userData = User::whereToken($token)->where('status','<>',2)->get()->first();
        User::whereToken($token)->where('status','<>',2)->firstOrFail()->confirmEmail(); 
        $this->add_trial($userData);
        return redirect('login')->with('message','You are now confirmed. Please login.');
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
        
        $userData = User::where('email',$credentials['email'])->where('status','<>',2)->get(['id','status','verified','parent_user_id'])->first();
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
        if(!empty($userData) && $userData->count != 0) {
            $is_active = $this->checkOwnerStatus($credentials['email']);
        } else {
            $is_active = true;
        }        
        if($is_active) {
           
        $credentials['status'] = 1; 
            if (Auth::guard($this->getGuard())->attempt($credentials, $request->has('remember'))) {
                
                if(Auth::guard($this->getGuard())->user()->status == '1'){
                    if(Auth::guard($this->getGuard())->user()->is_need_change_pass == 1) {
                        $id = Auth::guard($this->getGuard())->user()->id;
                        //User::where('id',$id)->update(['is_need_change_pass'=>0]);
                        $this->check_user_account();
                        return redirect('change-password');
                    }  
                    $this->check_user_account();
                    /*if(!isset(Auth::guard($this->getGuard())->user()->choosed_timezone)  || empty(Auth::guard($this->getGuard())->user()->choosed_timezone)) {
                        return redirect('myprofile'); 
                    }*/
                	return $this->handleUserWasAuthenticated($request, $throttles);
    			}else{

    				return redirect()->back()
    	            ->withInput($request->only($this->loginUsername(), 'remember'))
    	            ->withErrors([
    	                $this->loginUsername() => 'It looks like you have not confirm your email yet. Please confirm you email.',
    	            ]);	
    			}
            }  else {
                 if ($throttles && ! $lockedOut) {
                        $this->incrementLoginAttempts($request);
                    }
                    return $this->sendFailedLoginResponse($request);
                 }
        } else {

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            if ($throttles && ! $lockedOut) {
                $this->incrementLoginAttempts($request);
            }
            return $this->sendFailedLoginResponse($request); 
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
        return view('auth.login', ['validator'=>$validator,'page_title'=>'Login']);
    } 
	
	  /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $rules = array( 
            'name' => 'required|max:255',
            'com_name' => 'required|max:255',
            'email' => 'required|max:255|check_email|super_unique|users',
            'password' => 'required|min:8|regex:/^(?=\S*[a-z A-Z])(?=\S*[\d])\S*$/',
            'password_confirmation' => 'required|same:password'
        );
        $messages = array();
        $messages['com_name.required'] = "The company name is required.";
        $messages['name.required'] = "The name field is required.";
        $messages['password.required'] = "The password field is required.";
        $messages['password.min'] = "Password must be contain at least 8 characters.";
        $messages['password.regex'] = "Password must include letter(s) and number(s).";
        $messages['password.confirmed'] = "New password and confirm password do not match.";
        $validator = JsValidator::make($rules,$messages);   

        $userRef = Input::get('user_ref');
        $isUserRef = false;
        if(isset($userRef) && !empty($userRef)){
            $isUserRef = true;
            $userid = Crypt::decrypt($userRef);
            $url = DB::table('userrefererurls')->where('user_id',$userid)->get(['short_url']);
            Session::put('user_ref',$userid);
            $short_url = $url[0]->short_url;
        } else {
            $short_url = "";
        }
        return view('auth.register', ['validator'=>$validator,'isUserRef'=>$isUserRef,'page_title'=>'Registration Page','url'=>$short_url]);
    } 

	
	public function logout(){
        if(!empty(Session::get('login_as_customer'))) {
            Session::forget('login_as_customer');
        } 
        Auth::guard($this->getGuard())->logout();
		return redirect('/login');
	}
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return Response
     */
    public function handleProviderCallback(AppMailer $mailer)
    { 
        try {
            $user = Socialite::driver('google')->stateless()->user();
            $userData = User::where('email',$user->email)->first();
                
                if ($userData && !empty($userData->id) && $userData->status != 2) {  // check user is already register or not 
                    if($userData->status == 0) {
                        return redirect('login')->withErrors(['email'=>'This account has been suspended.']);
                    } else {
                        Auth::guard($this->getGuard())->loginUsingId($userData->id,true);
                        if(empty($userData->choosed_timezone )) {
                            return redirect('change-password'); 
                        }
                        return redirect('/');
                    }
                    
                }else { // register new user 
                    $password = str_random(10); // generate password 
                   
                    $fileName  = time().'.png'; // set uploaded file name 
                    $data = file_get_contents($user->avatar); //  get file from google account 
                    $saveFile = config('constants.FILEUPLOAD').''.$fileName; // new file name with save path 
                    file_put_contents($saveFile, $data);// Write the contents back to a new file
                    $setName = $this->setUserName($user->name);
                    $data  = array('name' => $user->name,
                        'email' => $user->email,
                        'google_id' => $user->id,
                        'first_name' => $setName['firstName'],
                        'last_name' => $setName['lastName'],
                        'com_name' => '',
                        'profile_image' => $fileName,
                        'user_type' => 1,
                        'password' => $password
                        
                        );

                    $userAdded = $this->create($data);  //create

                    $user->password = $password;
                    $user->first_name = $setName['firstName'];
                    $user->com_name = '';
                    $mailer->sendUserPassword($user); // send password and user name to user mail 


                    User::where('email',$user->email)->first()->confirmEmail(); // allow user to login 
                    Auth::guard($this->getGuard())->loginUsingId($userAdded->id,true); // keep user login after regster 
                    return redirect('/change-password'); 
                }
        } catch (Exception $e) {
            die($e); 
        }
       
    }
    
    /**
    * this function set user first name and last name
    *
    *  @return set name array
    */
    public function setUserName($data)
    {
        $setData = array();
        $setName = explode(' ',$data);
        $firstName = $setName[0];
        $lastName = '';
        if(count($setName) > 1){
            unset($setName[0]);
            $lastName = implode(" ",$setName);
        }
        $setData['firstName'] = $firstName;
        $setData['lastName']  = $lastName;
        return $setData;
    }

    /**
    * this function created new user invited by any user if yes then 
    * updated invites table status
    * 
    * @return user found then return true otherwise false
    */
    public function changeInvitedFriendStatus($data = null,$userId)
    {
        $is_added_user = 0;
        if(Session::get('user_ref') && !empty(Session::get('user_ref'))) {
            $fromUserId = Session::get('user_ref'); // this is user refer id 
            $myUser = User::where('id',$fromUserId)->get()->first();

            if(!empty($data) && $data && $data != null ) {
                $where = array('friend_email'=>$data,'from_user_id'=>$fromUserId);
                $record = Invite::where($where)->get();
                $getTrialDays = Setting::where('field_key','referral_trial_days')->get(['field_value'])->first();
                if($record->count() == 1) {
                    $updatedData = array('to_user_id'=>$userId,'status'=>1,'invitation_accept_date'=>date('Y-m-d h:i:s'),'trial_days'=>$getTrialDays->field_value,'owner_id'=>$myUser->parent_user_id);
                    Invite::where($where)->update($updatedData);
                    $is_added_user = 1;
                } else {
                    $insertData = array('from_user_id'=>$fromUserId,'to_user_id'=>$userId,'friend_email'=>$data,'status'=>1,'invitation_accept_date'=>date('Y-m-d h:i:s'),'trial_days'=>$getTrialDays->field_value,'owner_id'=>$myUser->parent_user_id);
                    Invite::create($insertData);
                    $is_added_user = 1;
                }
                if($is_added_user == 1 ) {
                   $userData = User::where('id',$userId)->get()->first();
                   $userData->refer_via = $fromUserId;
                   $userData->save();
                   Session::forget('user_ref');
                   $uBalanceObj = new UserBalance;
                   $amountIn = "days";
                   if($myUser->parent_user_id != 0) {
                     $real_user_id = $myUser->id;
                     $fromUserId = $myUser->parent_user_id;
                     
                    } else {
                        $fromUserId = $myUser->id;
                        $real_user_id = 0;
                        
                    }
                    $myUser->trial_end_date = date ("Y-m-d", strtotime ($myUser->trial_end_date ."+$getTrialDays->field_value days"));
                    $myUser->save();
                   $uBalanceObj->addCredit($getTrialDays->field_value,$fromUserId,$amountIn,$real_user_id,$userId);
                }
            } 
        }
    }

    /**
    * This function check current user owner active or not 
    *
    * @return true on active else false
    */
    public function checkOwnerStatus($email)
    {
        $data = User::where('email',$email)->get(['id','status','verified','parent_user_id'])->first();
        $owner_id =  ($data->parent_user_id != 0)? $data->parent_user_id : $data->id;
        $checkAccountStatus = CancelAccount::where(['owner_id'=>$owner_id,'status'=>0])->get()->first();

        if(isset($checkAccountStatus) && $checkAccountStatus->count() != 0) {
            return false;
        } else {
            return true;
        }
    }

    public function check_user_account() 
    {
       
        $user = Auth::guard($this->getGuard())->user();
        $userObj = new User;
        if($user->parent_user_id == 0) {
           $is_expired = $user->is_expired;
        } else {
            $userData = User::where('id',$user->parent_user_id)->get()->first();
            $is_expired =  $userData->is_expired;
        }
        $userObj->where('id',$user->id)->update(['is_expired'=>$is_expired]);   
    }

    /**
    * this function  validate coupon which user enter 
    * @return succes on apply
    */
    public function check_coupon($coupon_code)
    {
        if(!empty($coupon_code)) {
            $message = $this->get_message();
            $where =  array('coupon'=>$coupon_code,'user_type'=>2);
            $coupon_details = Coupon::where('status','<>',0)->where($where)->get()->first();
            $validationMessage = $this->validate_coupon($coupon_details);
            $setMessage = array($validationMessage => $message[$validationMessage]);
            return $setMessage;
        }
    }

    /**
    * this function check coupon exist or not and allow for current user
    */
    public function validate_coupon($coupon_details) 
    {
        if(empty($coupon_details)) {
            return 'invalid';
        } else {
            $coupon_expired_days = find_days_diff($coupon_details['coupon_expire']);
            
            if($coupon_details['status'] == 3) {
                return 'used';
            } 

            if($coupon_details['status'] == 4 ||  $coupon_expired_days < 0) {
                return 'expired';
            }
            if($coupon_details['user_type'] == 1) {
                return 'invalid_user';
            } 
                
          }
          return 'success';
    } 
    /**
    * this function set all error and success message
    * @return array 
    */
    public function get_message()
    {
        $setMessage['used'] = 'This coupon code have been already used';
        $setMessage['expired'] = 'This coupon code have been expired';
        $setMessage['success'] = 'This coupon is successfully applied, now we will add coupon discount in next plan';
        $setMessage['error'] = "opps! something went wrong";
        $setMessage['invalid_user'] = "Invalid coupon, This code does not applicable!";
        $setMessage['invalid'] = "Invalid coupon, This code does not applicable!";
        return $setMessage;
    }

    /**
    * this function apply coupon which user enter 
    * @return succes on apply
    */
    public function apply_coupon($coupon_code,$user)
    {
        if(!empty($coupon_code)) {
            $where =  array('coupon'=>$coupon_code);
            $coupon_details = Coupon::where('status','<>',0)->where($where)->get()->first();
            $type = 1;
            $coupon_log = array('coupon_id'=>$coupon_details->id,'owner_id'=>$user->id,'type'=>$type);
            if($type == 1 ) {
                $coupon_log['trial_days'] = $coupon_details->coupan_day;
                $coupon_log['status'] =  0;
            }   
            CouponLog::create($coupon_log);
            $coupon_details->status = 3;
            if($coupon_details->save()) {
                return true;
            } else {
                return false;
            }
        
        }
    }
    /**
    * this function add trial days
    * @return true on success
    */
    public function add_trial($userData) 
    {
        if(!empty($userData)) {
            $where = array('status'=>0,'owner_id'=>$userData->id);
            $couponData = CouponLog::where($where)->get()->first();
            if(empty($couponData)) {
                return false;
            }

            $dateDiff = find_days_diff($userData->trial_end_date);
            if($dateDiff > 0) {
                $newTrialEndDate = date("Y-m-d", strtotime ($userData->trial_end_date ."+$couponData->trial_days days"));
            } else {
                $newTrialEndDate = date("Y-m-d", strtotime ("+$couponData->trial_days days"));
            }
            $userData->trial_end_date = $newTrialEndDate ;
            $userData->save();
            $couponData->status = 1;
            $couponData->save();
            return true;
        } else {
          return false;
        }
    }
}
