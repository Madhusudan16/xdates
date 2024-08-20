<?php

namespace App\Http\Controllers\Admin\Auth;
use Validator;
use JsValidator;
use App\Http\Controllers\Controller; 
use App\Models\Admin\Admin;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use DB;
use Response;
use Redirect;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $redirectTo = '/admin';  
    protected $guard = 'admin';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subject = 'X-Dates - Reset your password'; // change resend password mail subject
        $this->middleware('adminguest');
        Password::setDefaultDriver('admin');  
    }
    
      /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateSendResetLinkEmail($request);

        $email = $request->input('email');
        $resultCount = Admin::where(['email'=>$email,'status'=>1])->get()->first();
        
        if(empty($resultCount) || $resultCount->count() == 0) {
            return Redirect::back()->withErrors(['invalid'=>"Record Not Found!"]); 
        } 
        if(isset($resultCount->status)) {
            if($resultCount->count() > 1 && $resultCount->status != 1){
                return Redirect::back()->withErrors(['invalid'=>"This account has been suspended!"]); 
            }
        }
        $broker = null;
        $response = Password::broker($broker)->sendResetLink(
            $this->getSendResetLinkEmailCredentials($request),
            $this->resetEmailBuilder()
        );
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $this->getSendResetLinkEmailSuccessResponse($response);
            case Password::INVALID_USER:
            default:
                return $this->getSendResetLinkEmailFailureResponse($response);
        }
    }
    
        /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {

        $rules = array(
            'email' => 'required|email'
        );
        
        $validator = JsValidator::make($rules); 

        if (property_exists($this, 'linkRequestView')) {
            return view($this->linkRequestView, ['validator'=>$validator]);
        }
        if (view()->exists('admin.auth.passwords.email')) {
            return view('admin.auth.passwords.email', ['validator'=>$validator]);
        }
        
        
        return view('admin.auth.password', ['validator'=>$validator]);
    }
    
    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->input('email');

        if (is_null($token)) {
            return $this->getEmail();
        } else {
            $result = DB::table('admin_password_resets')->where(['email'=>$email,'token'=>$token])->count();
            if($result !=1) {
                return Response::view('errors.404',array('message'=>"Password reset link has been expired.",'title'=>"Link expired"),404);
            }
        }

        
        
        $rules = array( 
            'password' => 'required|min:8|regex:/^(?=\S*[a-z A-Z])(?=\S*[\d])\S*$/',
            'password_confirmation' => 'required|same:password' 
        );
        $messages = array();
        $messages['password'] = array();
        $messages['password.required'] = "The password field is required.";
        $messages['password.min'] = "Password must be contain at least 8 characters.";
        $messages['password.regex'] = "Password must include letter(s) and number(s).";
        $messages['password_confirmation.same'] = "New password and confirm password do not match.";
        $validator = JsValidator::make($rules,$messages);     

         
        if (property_exists($this, 'resetView')) {
            return view($this->resetView)->with(compact('token', 'email','validator'));
        }
        if (view()->exists('admin.auth.passwords.reset')) {
            return view('admin.auth.passwords.reset')->with(compact('token', 'email','validator'));
        }
        
        return view('admin.auth.passwords.reset')->with(compact('token', 'email','validator'));
    }
    
    public function send(){

        return view('admin.auth.passwords.send');
    }
    /**
     * Validate the request of sending reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateSendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);
    }
     /**
     * Get the needed credentials for sending the reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getSendResetLinkEmailCredentials(Request $request)
    {
        return $request->only('email');
    }
    
    protected function getSendResetLinkEmailSuccessResponse($response)
    {
        return redirect('/admin/password/reset/send')->with('status', trans($response));
    }
 
}
