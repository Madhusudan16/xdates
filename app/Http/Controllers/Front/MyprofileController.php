<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Commons\UserAccess; 
use App\Commons\AppMailer;
use App\Http\Requests;
use Illuminate\Routing\Controller;
use Hash;
use App\User;
use App\Models\Front\CancelAccount;
use Validator;
use JsValidator;
use Request;
use Response;
use Illuminate\Support\Facades\Input;
use App\Models\Front\Tbltimezone;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Contracts\Auth\Guard;
use App\Models\Front\Invite;
use DB;
use App\Commons\SettingVars;

class MyprofileController extends Controller
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

    public $setTimeZone = 0 ;

    public function __construct(UserAccess $userAccess)
    {
       $this->middleware('auth'); 
	   $this->user = Auth::guard($this->guard)->user(); 
       $this->userObj = new User();
       $this->timezoneObj = new Tbltimezone();
       if(empty($this->user->choosed_timezone) || empty($this->user->com_name)) {
           $this->setTimeZone = 1;
           $this->vdata['need_timezone'] = 'readonly'; 
        }
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
        
        $checkAccess = acoount_expire_text($this->user);
        $this->vdata['check_access'] = $checkAccess;
        if($checkAccess  && isset($checkAccess) &&  $checkAccess['allow_access'] == 1) {
            return redirect()->to('/');
        }
        if($this->vdata['curModAccess']['view'] != 1){
            return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        if(!check_user($this->user->parent_user_id)) {
            Auth::logout();
        }
        $rules = array(
                    'first_name' => 'required',
                    'last_name' => 'required',
                );  
        if($this->user->user_type != 1) {
            $this->vdata['is_cancel'] = 'no-cancel';
        }
       $this->vdata['default_profile'] = config('constants.DEFAULT_PROFILE');
       $this->vdata['page_title'] = 'User profile'; // set page title 
       $validator = JsValidator::make($rules);
       $timeZoneData = array(); // this variable store timezone data which coming from database
       $timeZoneData = $this->timezoneObj->orderBy('timezone_name','asc')->get(); // fetch all data from tbltimezones table 
       $this->vdata['page_section_class'] = "my-account"; // set main section class
       $this->vdata['timezones'] = $timeZoneData;  // store all timezone 
       $this->vdata['validator'] = $validator;     
       $this->vdata['customer_support_number'] = SettingVars::get_setting_value('customer_support_mail');
      
       return view('front.myprofile',$this->vdata); // called view file front/myprofile.blade.php
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
                               $field_name => 'Required|email|unique:users|check_email' 
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
                    
                    if(isset($updateData['com_name'])) {
                        if($this->user->user_type == 1) {
                             User::where('id',$this->user->id)->orWhere('parent_user_id',$this->user->id)->update($updateData); // apply update Query
                             if(!empty($this->user->choosed_timezone)) {
                                return Response::json(['reload' =>1,'page_url'=>'notification',''=>$this->user->choosed_timezone],200);
                             } else {
                                return Response::json(['reload' =>0,'field_type'=>1],200); // 1 - timezone
                             }

                        } else {
                            return response()->json(['error' => 'Oops.. Permission Denied.'],404);
                        }
                    } else {
                        User::where('id',$this->user->id)->update($updateData); // apply update Query
                        if($this->setTimeZone == 1 && $field_name == "choosed_timezone") {
                            if(!empty($this->user->com_name)) {
                                return Response::json(['reload' => 1,'page_url'=>'notification',''=>$this->user->com_name],200);
                            } else {
                                return Response::json(['reload' =>0,'field_type'=>2],200); // 2 - com_name
                            }
                        }
                    }
                    
                    if(isset($updateData['profile_image'])) { // check profile_image is isset or not 
                        echo $imagePath['path'].''.$updateData['profile_image']; // if isset then print full path name and file name for update image after change.
                    } 
                    
                } else {
                    if(!isset($updateData['profile_image']) && !empty($updateData)){
                        $responseData['field_name'] =$field_name;
                        $responseData['field_value'] =$this->user->$field_name;
                        return Response::json(['data' =>$responseData],404); 
                        //echo json_encode($responseData);
                    }
                }

            }else{
            	return response()->json(['error' => 'Oops.. Permission Denied.'],404);
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
        if(isset($this->vdata['curModAccess']['update']) && $this->vdata['curModAccess']['update'] == 1){
           /* if(empty($this->user->choosed_timezone)) {
                return redirect()->to('myprofile');
            }  */ 
            $checkAccess = acoount_expire_text($this->user);
            $this->vdata['check_access'] = $checkAccess;
            if($checkAccess  && isset($checkAccess) &&  $checkAccess['allow_access'] == 1) {
                return redirect()->to('/');
            }
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
            return view('front.change-password',$this->vdata); // call change-password.blade.php file  
        } else {
            return redirect('/myprofile');
        }
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
                if (Auth::guard('web')->validate(array('email'=>$this->user->email,'password'=>$formData['password'],'status'=>1))){ // check given password is valid for current user 
                    if($formData['new_password'] == $formData['password_confirmation']){ // check new password and confirm password both is same or not
                        $newPassword  = bcrypt($formData['new_password']); // encrypt new password
                        $updateData['password'] = $newPassword; 
                        $updateData['is_need_change_pass'] = 0;
                        User::where('id',$this->user->id)->update($updateData); // apply update Query 
                        if(empty($this->user->choosed_timezone ) || empty($this->user->com_name)) {
                            return Response::json(['page_url'=>'myprofile']);    
                        } 
                        return Response::json('success');
                    }
                }
                else {
                    return Response::json(['error'=>'password does not changed',404]);  // print error when password does not match
                }
            }
        }
    }

    /**
    * This function calcel owner account
    *
    * @return true on success
    */
    public function cancelAccount()
    {
        if(isset($this->vdata['curModAccess']['update']) && $this->vdata['curModAccess']['update'] == 1){
            if(Request::ajax()) {
                $mail = new AppMailer;
                $owner_id = $this->user->id;
                $email    = Request::get('email');
                $rules = array(
                        'email'=>'required|email|super_unique:users,'.$this->user->id,
                    );
                $validation = Validator::make(Request::all(), $rules);

                if(!$validation->passes()){ /* check given input is valid or not  */
                   return Response::json(['error'=>'invalid email'],403);
                } else {
                    $where = array('id'=>$owner_id,'email'=>$email,'parent_user_id'=>0);
                    $resultUser = User::where($where)->get()->first();
                    if($resultUser->count() != 0)  {
                        $date = date("Y-m-d");
                        $days = SettingVars::get_setting_value('cancel_account_restore');
                        $owner_email = SettingVars::get_setting_value('admin_email');
                        if(!isset($days) && $days > 0) {
                            $days = 30;
                        }
                        $expiryDate = date('Y-m-d',strtotime("+$days days"));
                        $token = str_random(30);
                        $childUser = $this->getChildUser($owner_id);
                        $setData = array('owner_id'=>$owner_id,'expired_date'=>$expiryDate,'status'=>0,'token'=>$token,'child_user'=>$childUser);
                        $url = URL('/reactive-account').'/'.$token;
                        
                        $sendMailData = array('expired_date'=>$expiryDate,'url'=>$url,'owner_name'=>$this->user->name,'email'=>$this->user->email,'restoreIn'=>$days,'owner_email'=>$owner_email);
                        if($this->user->refer_via != null && $this->user->refer_via != 0 ) {

                            $setData['referral_user_data'] = $this->get_referral_data($this->user->refer_via);
                            $this->change_referral_user_status($this->user->refer_via);
                        }
                        CancelAccount::create($setData);
                        $resultUser->status = 0; 
                        $resultUser->save();
                        $mail->cancelAccount($sendMailData);
                        Auth::logout();
                        return Response::json(['message'=>'data saved successfully','status'=>200],200);
                    } else {
                        return Response::json(['message'=>'This Account is not belong to you'],403);
                    } 
                }
            }
        } else {
            //return Response::json(['msg'=>'access denied'],403);
        }
    }
    
    /**
    * get child user by id and set their status  2
    *
    * @return Json on success
    */
    public function getChildUser($owner_id)
    {
        $allUserByOwner = User::where('parent_user_id',$owner_id)->get();
        if($allUserByOwner->count()) {
            $count = 0;
            foreach($allUserByOwner as $user) {
                $userData[$count]['id'] = $user->id;
                $userData[$count]['status'] = $user->status;  
                $user->status = 2;
                $user->save();
                $count++;
            }
            $jsonData = json_encode($userData);
            return $jsonData;
        } else {
            return false;
        }
    }

    /**
    * change referral user status
    */
    public function change_referral_user_status($user_id)
    {
        $where = array('from_user_id'=>$user_id,'to_user_id'=>$this->user->id);
        $update_data = array('status'=>3,'cancelled_date'=>date('Y-m-d H:i:s'));
        if(Invite::where($where)->update($update_data)) {
           return true;
        }
    }

    /**
    * this function return referral user data in json format
    */
    public function get_referral_data($user_id)
    {
        $where = array('from_user_id'=>$user_id,'to_user_id'=>$this->user->id);
        $inviteData = Invite::where($where)->get(['from_user_id','status'])->first()->toArray();
        if(!empty($inviteData)) {
            return json_encode($inviteData);
        } else {
            return null;
        }
    }

    /**
    * this function remove user balance which their get from this user
    */
   /* public function deduct_referral_user_balance($referral_user_id)
    {
        $userBalanceData = DB::table('user_balance')->where('owner_id', $referral_user_id)->where('type', 1)->where('status',1)->where('balance_from_user_id',$this->user->id)->get(['id','status']);
        if(count($userBalanceData) != 0) {
            DB::table('user_balance')->where('owner_id', $referral_user_id)->where('type', 1)->where('balance_from_user_id',$this->user->id)->update(['status'=>0]);
            return json_encode($userBalanceData);
        } else {
            return null;
        }
    }*/

    public function remove_avatar()
    {
        if(isset($this->vdata['curModAccess']['update']) && $this->vdata['curModAccess']['update'] == 1) {
            if(Request::ajax()) {
                $this->user->profile_image = '';
                $this->user->save();
                
                return Response::json(['message'=>'file deleted'],200);
            } else {
                 return Response::json(['message'=>'Opps! something went wrong',],404);
            }
        } else {
            return Response::json(['message'=>'Opps! something went wrong',],404);
        }
    }
}
