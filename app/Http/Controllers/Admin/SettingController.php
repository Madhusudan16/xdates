<?php
namespace App\Http\Controllers\Admin;
use App\Models\Admin\Setting;
use Auth;
use App\Commons\AdminUserAccess; 
use App\Http\Requests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
use JsValidator;
use Response; 

class SettingController extends Controller
{
	/**
     * The module id
     */
    protected $moduleId = 5;
	
	/**
     * The guard name
     */
    protected $guard = 'admin';
	
	/**
     * view data
     */
    protected $vdata = array();
	
   /**
    *  this save page common setting like page title, page heading etc.
    */
   
    public $pageSetting = array();
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   

    public function __construct(AdminUserAccess $userAccess)
    {
        $this->middleware($this->guard);
		    $this->user = Auth::guard($this->guard)->user();  
        $this->settingObj = new Setting(); // create setting class Object
		    if(!empty($this->user)){ 
      			$this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
      			$this->vdata['user'] = $this->user;
      			$this->vdata['curModAccess'] = $this->access['current'];
      			$this->vdata['allModAccess'] = $this->access['all'];
		    } 
		$this->vdata['page_section_class'] = 'my-account';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if($this->vdata['curModAccess']['view'] != 1){
            return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        $rules = array(  
                'setting[admin_email]' => 'required|email',
                'setting[trial_duration]' => 'required',
                'setting[referral_trial_days]' => 'required|numeric',
                'setting[address]' => 'required',
                'setting[customer_support_mail]' => 'required|email',
                'setting[monthly_yearly_notification_on]' => 'required|email',
                'setting[cancel_account_restore]' => 'required|numeric',
                'setting[feedback_noti_email]' => 'required|email',

        ); // set validation rules 
        $msg = array( // 
                'setting[admin_email].required' => 'Email Field is Required',
                'setting[trial_duration].required' => 'Name Field is Required',
                'setting[admin_email].email' => 'The email address is invalid.',
                'setting[referral_trial_days].required' => 'Referral trial field is required',
                'setting[referral_trial_days].numeric' => 'Referral trial field must be number value',
                'setting[address].required' => 'Address field is required',
                'setting[customer_support_mail].required' => 'customer support mail address field is required',
                'setting[customer_support_mail].email' => 'invalid customer support mail address',
                'setting[monthly_yearly_notification_on].email' => 'The email address is invalid.',
                'setting[monthly_yearly_notification_on].required' => 'Monthly yearly notification field required',
                'setting[cancel_account_restore].required' => 'Cancel account restore field required',
                'setting[cancel_account_restore].numeric' => 'Cancel account restore field  must be number value',
                'setting[feedback_noti_email].required' => "Feedback notification mail field is required.",
                'setting[feedback_noti_email].email' => "The email address is invalid.",
        );      // set validation message
        $validator = JsValidator::make($rules,$msg,array(),'#settingForm');  // apply JsValidation validation 
    	  
        if(Input::get('setting')){ //check if setting array is isset or not 
           $settingData = Input::get('setting'); // get all setting array data
           //preF($settingData);
            foreach ($settingData as $field_key => $field_value) {   
                    $settingObj = new Setting;
                    $getdata = $settingObj->where('field_key',$field_key)->count();
                    // check data is already exist or not 
                    $this->insertUpdateSettingData($getdata,$field_key,$field_value); // this function either update or insert data 
            }
        }

        $tableData = $this->settingObj->where('status','!=',2)->get(); // get all data where status not equal to 2 
        $this->vdata['setting']  = $this->getSettingData($tableData); //  this function format data
        $this->vdata['page_title'] = 'General Settings'; // set page title 
        $this->vdata['page_heading'] = "General Setting"; // set page heading 
        $this->vdata['validator'] = $validator; 
        return view('admin.settings',$this->vdata); // return view with data 
    }

    /**
    * this function return all setting data
    */
    public function getSettingData($data){
            foreach ($data as $key => $value) {
               $setValue[$value->field_key] = $value->field_value; // set data of pair  key and value 
            }
            return $setValue; // return data 
    }
    /**
    *  this function whether insert or update if $isRecordExist variable value is 0 then insert
    *  data otherwise update data 
    */
    public function insertUpdateSettingData($isRecordExist,$field_key,$field_value){
            if(is_array($field_value)){
                  $data = $this->array_non_empty_items($field_value);
                  if(!empty($data)){  
                      $field_value = json_encode($data); 
                  }   
            }else if(!empty($data)){
                  $field_value = $data;
            }
            $settingObj = new Setting;
            if($isRecordExist == 0){ // check data already exist or not if not then call if section otherwise go to else part
                  $settingObj->field_key = $field_key; // set key for insert record
                  $settingObj->field_value = $field_value; // set value for insert record
                  $settingObj->save(); // insert data 
            }
            else{
                  $settingObj->where('field_key',$field_key)->update(array('field_value'=>$field_value));// this function update field value 
            }
            //return 0;
    }
    public function array_non_empty_items($input) {
                // If it is an element, then just return it
                if (!is_array($input)) {
                  return $input;
                }
                $non_empty_items = array();
            
                foreach ($input as $key => $value) {
                  // Ignore empty cells
                  if($value) {
                    // Use recursion to evaluate cells 
                    $non_empty_items[$key] = $this->array_non_empty_items($value);
                  }
                }
                // Finally return the array without empty items
                return $non_empty_items;
    }
}
