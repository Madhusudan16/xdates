<?php
namespace App\Http\Controllers\Admin; 

use Auth;
use App\Commons\AdminUserAccess; 
use App\Http\Requests;
use Illuminate\Routing\Controller;
use Hash;
use App\Models\Admin\Admin;
use Validator;
use JsValidator;
use Request;
use Response;
use Illuminate\Support\Facades\Input;
use App\Models\Front\Tbltimezone;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Contracts\Auth\Guard;

class MyprofileController extends Controller
{
	/**
     * The module id
     */
    protected $moduleId = 4;
	
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
    * create variable for class User
    */
    public $userObj = array();
    
    /**
    * create variable for class Tbltimezone 
    */
    public $timezoneObj = array();

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(AdminUserAccess $userAccess)
    {
        
       $this->middleware($this->guard); 
	   $this->user = Auth::guard($this->guard)->user(); 
       $this->userObj = new Admin();
       
       $this->timezoneObj = new Tbltimezone();

	   if(!empty($this->user)){ 
			$this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
			$this->vdata['user'] = $this->user; 
            $this->vdata['curModAccess'] = $this->access['current'];
			$this->vdata['allModAccess'] = $this->access['all'];
			$this->vdata['page_section_class'] = 'my-account';
	   }
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
                    'first_name' => 'required',
                    'last_name' => 'required',
                );  
       $this->vdata['default_profile'] = config('constants.DEFAULT_PROFILE');
       $this->vdata['page_title'] = 'User profile'; // set page title 
       $validator = JsValidator::make($rules);
       $timeZoneData = array(); // this variable store timezone data which coming from database
       $timeZoneData = $this->timezoneObj->all(); // fetch all data from tbltimezones table 
       $this->vdata['page_section_class'] = "my-account"; // set main section class
       $this->vdata['timezones'] = $timeZoneData;  // store all timezone 
       $this->vdata['validator'] = $validator;     
       
       return view('admin.myprofile',$this->vdata); // called view file front/myprofile.blade.php
    }
    /**
    * update User Profile 
    */
    public function updateProfile(){
        
        $updateData = array(); // store array values of update data which coming from 
       
        if(Request::ajax() && isset($this->vdata['curModAccess']['update']) && $this->vdata['curModAccess']['update'] == 1){

            if(Input::hasFile('profile_image')){  // check file isset or not 
                $rules = array(
                    'profile_image' => 'required|image'
                );
                $file = Input::file('profile_image');
                $validation = $this->profileValidation(array('profile_image'=>$file), $rules);
                if($validation == true){
                     // save file object in $file variable 
                    $imagePath = $this->uploadFile($file); // called file upload function 
                    $updateData['profile_image'] = $imagePath['file_name']; 
                    
                }
            } 
            else{
                
                $profileData = Input::get('data');
                $profileData = end($profileData);
                $field_name = implode(' ',array_keys($profileData)); // get field name from input
                $field_value = implode(' ',array_values($profileData)); // get field value from input 
                if($field_name == 'email'){ // set rules by field name 
                    $rules = array(
                           $field_name => 'Required|email|unique:users' 
                    );  
                }
                else if($field_name == 'choosed_timezone'){ 
                    $rules = array(
                           $field_name => 'Required'
                    );  
                }
                else{
                    $rules = array(
                           $field_name => 'Required|Max:80'
                    );  
                }
                $validation = $this->profileValidation($profileData, $rules); // apply  validation  
                    
                        if($field_name == 'first_name'){
                            $updateData[$field_name] = $field_value;
                            $updateData['name'] = $field_value. ' '. $this->user->last_name; // change name field in database 
                        }
                        else if($field_name == 'last_name'){
                            $updateData[$field_name] = $field_value;
                            $updateData['name'] =  $this->user->first_name . ' '. $field_value; // change name field in database 
                        }
                        else {
                             $updateData[$field_name] = $field_value; // set value for update method 
                        }

            }

            if($validation == true){
                Admin::where('id',$this->user->id)->update($updateData); // apply update Query 
            
                if(isset($updateData['profile_image'])) { // check profile_image is isset or not 
                    echo url($imagePath['path'].''.$updateData['profile_image']); // if isset then print full path name and file name for update image after change.
                } 
                
            }
            else {
                if(!isset($updateData['profile_image']) && !empty($updateData)){
                    $responseData['field_name'] =$field_name;
                    $responseData['field_value'] =$this->user->$field_name;
                    echo json_encode($responseData);
                }
            }

        }else{
        	return response()->json(['error' => 'Oops.. Permission Denied.'],403);
        }
         
    }
    /**
    * this function move file to server
    */
    public function uploadFile($file){
             $imageTempName = $file->getPathname(); // get image temp name 
             $imageName = $file->getClientOriginalName(); // get file original name 
             $ext = $file->getClientOriginalExtension();
             $file_name  = time().'.'.$ext;
             $path =config('constants.FILEUPLOAD');  // file path constant /assets/uploads/profile
             $upload = $file->move($path , $file_name); // move file  to server 
             $imageDetails['path'] = $path;
             $imageDetails['file_name'] = $file_name;
             return $imageDetails;  // return file path with  name for  display image after saved
     }
     /**
     *  this function called change-password view and validate form input 
     */
    public function changePassword(){ 
        $page_section_class = 
        $rules = array(
                 'password' => 'required|min:8',  
                 'new_password' => 'required|min:8|regex:/^(?=\S*[a-z A-Z])(?=\S*[\d])\S*$/', // field is valid when data is alphabetical and numeric
                 'password_confirmation' => 'required|same:new_password' 
                );
        $messages = array();
        $messages['password.required'] = "The password field is required";
        $messages['password.min'] = "Password must be contain at least 8 characters.";
        $messages['new_password.required'] = "The password field is required.";
        $messages['new_password.min'] = "Password must be contain at least 8 characters.";
        $messages['new_password.regex'] = "Password must include letter(s) and number(s).";
        $messages['password_confirmation.same'] = "New password and confirm password do not match.";
        $validator = JsValidator::make($rules,$messages,[],'#changePasswordForm');
        $this->vdata['page_title'] = 'Change Password'; // set page title 
        $this->vdata['validator'] = $validator;       
        $this->vdata['page_section_class'] = "change-pass"; // set main section class
        return view('admin.change-password',$this->vdata); // call change-password.blade.php file  
    }
    
   
    /**
    *   this function validate profile data 
    */
    public function profileValidation($data, $rules){  // check my profile validation 
        $validation = Validator::make($data, $rules);
        if($validation->passes()){ /* check given input is valid or not  */
            return true;
        }
        else 
        {
            return false;
        }
    }
    /**
    * this function  save password in users table when it velid 
    */
    public function savePassword(){
        $updateData = array(); // store updated data in this array
        if (Request::isMethod('post') && isset($this->vdata['curModAccess']['update']) && $this->vdata['curModAccess']['update'] == 1) {
            $formData = Input::get();
            if(!empty(Input::get('data') )){
                 parse_str($formData['data'], $formData);  // convert form data to array
                if (Auth::guard($this->guard)->validate(array('email'=>$this->user->email,'password'=>$formData['password'],'status'=>1))) { // check given password is valid for current user 
                    if($formData['new_password'] == $formData['password_confirmation']){ // check new password and confirm password both is same or not
                        $newPassword  = bcrypt($formData['new_password']); // encrypt new password
                        $updateData['password'] = $newPassword; 
                        $updateData['is_need_change_pass'] = 0;
                        Admin::where('id',$this->user->id)->update($updateData); // apply update Query 
                        return Response::json('success');
                    }
                } else {
                   return Response::json(['error'=>'password does not changed',403]);  // print error when password does not match
                }
            }
        }
    }
}
