<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\User;
use Validator;
use JsValidator;
use Request;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Routing\Controller;
use App\Commons\UserAccess; 
use App\Models\Front\Feedback;
use App\Commons\AppMailer;
use App\Commons\SettingVars;

class FeedbackController extends Controller
{
	/**
     * The module id
     */
    protected $moduleId = 10;
	
	/**
     * The guard name
     */
    protected $guard = 'web';
	
	/**
     * view data
     */
    protected $vdata = array();
	
   	public function __construct(UserAccess $userAccess)
    {
       $this->middleware('auth'); 
	   $this->user = Auth::guard($this->guard)->user(); 
       $this->userObj = new User();
       if(!empty($this->user)) { 
			$this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
			$this->vdata['user'] = $this->user; 
			$this->vdata['curModAccess'] = $this->access['current'];
			$this->vdata['allModAccess'] = $this->access['all'];
			$this->vdata['page_section_class'] = 'cart';
	   }
    }

    /**
    * call feedback.blade.php file 
    *
    * @return view feedback.blade.php 
    */
    public function index()
    {
        if($page_url = prevent_user($this->user)) {
           return redirect($page_url);
        }
        $checkAccess = acoount_expire_text($this->user);
        $this->vdata['check_access'] = $checkAccess;
        if($checkAccess  && isset($checkAccess) &&  $checkAccess['allow_access'] == 1) {
            return redirect()->to('/');
        }
    	$rules = array(
    			'feedback' => 'required|min:3'
    		);
        if(!check_user($this->user->parent_user_id)) {
            Auth::logout();
        }
        $this->vdata['page_title'] = 'Feedback';
    	$validator = JsValidator::make($rules,[],[],'#feedbackForm');
    	$this->vdata['validator'] = $validator;
    	$this->vdata['saveFeedback'] = false;
    	return view('front.feedback',$this->vdata); 
    }

    /**
    * save feed back to database 
    *
    * @return view feedback.blade.php
    */
    public function addFeedback()
    {
        /*if(isset($this->vdata['curModAccess']['add']) && $this->vdata['curModAccess']['add'] == 1){*/
        	if(!empty(Input::get('feedback'))) {
                $userRoles = array(1=>'Owner',2=>"Admin",3=>"User");
    	    	$insertedData['owner_id'] = ($this->user->parent_user_id != 0 ) ? $this->user->parent_user_id : $this->user->id ; 
    	    	$insertedData['feedback_text'] = Input::get('feedback');
    	    	$insertedData['user_id'] = $this->user->id;
    	    	$inserted = Feedback::create($insertedData);
    			$this->vdata['saveFeedback'] = true;
    			$this->vdata['page_section_class'] = 'feedback_thankyou';
                $feedback_mail_data = array();
                $feedback_mail_data['email'] = $this->user->email;
                $feedback_mail_data['name'] = $this->user->name;
                $feedback_mail_data['role'] = $userRoles[$this->user->user_type];
                $feedback_mail_data['number_of_feedback'] = $this->find_number_by_user($this->user->id);
                $feedback_mail_data['data'] = $insertedData['feedback_text'];
                $this->send_mail($feedback_mail_data);
                 $this->vdata['page_title'] = 'Thank you for your feedback';
                return view('front.feedback',$this->vdata); 
        	}
       /* } else {
            return redirect('/feedback');    
        }*/
    } 

    /**
    * this function send feedback mail to X-Dates owner
    */
    public function send_mail($mail_content)
    {
        if(!empty($mail_content)) {
            $owner_email = SettingVars::get_setting_value('feedback_noti_email');
            if($owner_email) {
                $appMailer = new AppMailer;
                $appMailer->user_feedback($owner_email,$mail_content);
            }
        }
    }

    /**
    * this function find number of did by this user 
    * @return number of feedback
    */
    public function find_number_by_user($user_id)
    {
        if(empty($user_id)) {
            return false;
        }
        $number_of_feedback = Feedback::where('user_id',$user_id)->count();
        return ($number_of_feedback != 0) ? $number_of_feedback : 1;
    }
}