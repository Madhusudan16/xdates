<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Commons\UserAccess; 
use App\Http\Requests;
use Illuminate\Routing\Controller;
use Hash;
use App\User;
use Validator;
use JsValidator;
use Request;
use Response;
use Illuminate\Support\Facades\Input;
use App\Models\Front\Notification;
use App\Models\Front\CountryCode;
use Illuminate\Contracts\Auth\Guard;
use App\Commons\AppMailer; 
use App\Models\Front\NotificationFrequency;
use Twilio\Rest\Client;
use Session;

class NotificationController extends Controller
{
	/**
     * The module id
     */
    protected $moduleId = 9;
	
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

    public function __construct(UserAccess $userAccess)
    {
        //$request = new Request;
        $pos = strstr(Request::path(), "confirm") ;
        if(!$pos) {
            $this->middleware('auth'); 
    	    $this->user = Auth::guard($this->guard)->user(); 
       
           $this->userObj = new User();
           if(!empty($this->user)){ 
    			$this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
    			$this->vdata['user'] = $this->user; 
    			$this->vdata['curModAccess'] = $this->access['current'];
    			$this->vdata['allModAccess'] = $this->access['all'];
    			$this->vdata['page_title'] = 'Notification';
    			$this->vdata['page_section_class'] = 'top-padding-10 notification';
    			$this->vdata['countryCode'] = CountryCode::get();
    			$this->vdata['status'] = array('activation required','active');
                $this->vdata['frequencies'] = NotificationFrequency::where('type',1)->get();
                $this->vdata['follow_up_frequencies'] = array('None','Daily','Weekly');
    	   }
        }
    }

    /**
    * index function return view file of notification
    * 
    * @return  notification.blade.php file 
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
        if($this->user->show_noti_msg == 1) {
            $this->vdata['first_time_view'] = 1 ;
            $this->user->show_noti_msg = 2;
            $this->user->save();
        }
        if($this->vdata['curModAccess']['view'] != 1){
           return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        if(!check_user($this->user->parent_user_id)) {
            Auth::logout();
        }
    	$rules = array(
    			'email' => 'required|email'
            );
    	$validator = JsValidator::make($rules,[],[],'#addEmailForm');
        $is_show_conformation_modal = Session::get('is_show_conformation_modal');
        if(isset($is_show_conformation_modal)) {
            $this->vdata['is_show_conformation_modal'] = 'in show-confirmation-modal';
            Session::forget('is_show_conformation_modal');
        }
    	$notificationConfi = $this->getNotification();
    	$this->vdata['notificationConfi'] = $notificationConfi;
    	$this->vdata['validator'] = $validator;
    	return view('front.notification',$this->vdata); 
    }

    /**
    * this function email form data 
    * 
    * @return true on success
    */
    public function addEmail()
    {
        if(isset($this->vdata['curModAccess']['add']) && $this->vdata['curModAccess']['add'] == 1 || isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){
            $status_check = array(0,1);
        	if(Request::isMethod('post')) {
        		$validator = Validator::make(Input::all(),[
    			    'email' => "required|email|unique:user_noti_config,obj_value,null,id,status,0|check_email"
    			]);
                if ($validator->fails()) {
                    return Response::json(['error_msg'=>$validator->errors()->all()],422);
                }
        			$is_edit  = Input::get('is_edit');
        			$setData['obj_value']= Input::get('email');
    		    	$setData['obj_type'] = 1;
                    $setData['token'] = hash_hmac('sha256', str_random(40), config('app.key'));
                    if($setData['obj_value'] == $this->user->email) {
                        return Response::json(['error_msg'=>'The email has already been taken.'],422);
                    }
        		    if($is_edit == 0) {
        		    	$setData['status']   = 0;
        		    	$setData['user_id']  = $this->user->id;
                        $id= $this->save($setData,$is_edit);
                        return Response::json(['id'=>$id],200);
        		    } else {
        		    	$id = Input::get('id');
                        $setData['id']= $id;
        		    	//$this->save($setData,$is_edit,$id);
                        if($this->save($setData,$is_edit) != false) {
                            return Response::json(['msg'=>'Email have been saved'],200);
                        } else {
                            return Response::json(['error_msg'=>'something went wrong'],404);
                        }
        		    }
                }
        } else {
            return Response::json(['error_msg'=>'You have not permission to add and edit email.'],403);
        }
    }

    /**
    * this function  add phone number 
    *
    * @return true on success 
    */
    public function addPhone()
    {
        if(isset($this->vdata['curModAccess']['add']) && $this->vdata['curModAccess']['add'] == 1 || isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){
        	if(Request::isMethod('post')) {
        		$data = Input::get('data'); 
    			$data = parse_str($data,$formData);
                $is_edit  = Input::get('is_edit');
                $error_message =  array('unique'=>"You have already entered this number into the system.");
                if($is_edit == 1) {
                    if(isset($formData['notification_receive'])) {
                        $validator = Validator::make($formData,[
            			    'phone' => "required|confirm_number:user_noti_config,$formData[notification_receive],$formData[country_code]|unique:user_noti_config,obj_value,$formData[id],id"
            			],$error_message);
                    } else {
                        $validator = Validator::make($formData,[
                            'phone' => "required|confirm_number:user_noti_config|unique:user_noti_config,obj_value,$formData[id],id"
                        ],$error_message);
                    }
                } else {
                    $validator = Validator::make($formData,[
                        'phone' => "required|confirm_number:user_noti_config|unique:user_noti_config,obj_value,2,status"
                    ],$error_message);
                }
                if ($validator->fails()) {
                    return Response::json(['error_msg'=>$validator->errors()->all()],422);
                }
    			$setData['obj_value']= $formData['phone'];
    			$setData['obj_type'] = 2;
                
    			if($is_edit == 0) {
                    $moble_number = $formData['countryCode']."".$setData['obj_value'];
                    $setData['country_code']= $formData['countryCode'];
    		    	$setData['user_id']  = $this->user->id;
    		    	$setData['status']   = 0;
                    $ver_code = $this->send_sms($moble_number);
                    if($ver_code) {
                        $setData['verification_code'] = $ver_code;
                    }
    		    	$id = $this->save($setData,$is_edit);
                    return Response::json(['id'=>$id],200);
                } else {
                    $moble_number = $formData['country_code']."".$setData['obj_value'];
    		    	$setData['id'] = $formData['id'];
                    if(isset($formData['notification_receive'])) {
                        $notificationData = Notification::where('id',$setData['id'])->get()->first();
                        /*return Response::json(['data'=>$notificationData],404);*/
                        if($setData['obj_value'] == $notificationData->obj_value) {
                            $setData['status'] = $notificationData->status;
                            
                        } else {
                            $setData['status'] = 0;
                        } 
                        if($formData['country_code'] == $notificationData->country_code) {
                            $setData['country_code']= $notificationData->country_code;
                        }  else {
                            $setData['status'] = 0;
                            $setData['country_code']= $formData['country_code'];
                        }   
                        if(($setData['obj_value'] != $notificationData->obj_value) || $formData['country_code'] != $notificationData->country_code) {
                            $setData['is_active'] = 0;
                            $ver_code = $this->send_sms($moble_number);
                            if($ver_code) {
                                $setData['verification_code'] = $ver_code;
                            }
                        }
                        
                        $setData['is_active'] = $formData['notification_receive'];
                    } else {
                        $ver_code = $this->send_sms($moble_number);
                        if($ver_code) {
                            $setData['verification_code'] = $ver_code;
                        }
                        $setData['status'] = 0;
                        $setData['is_active'] = 0;
                        $setData['country_code']= $formData['country_code'];
                    }  

    		    	if($this->save($setData,$is_edit) != false) {
                        return Response::json(['msg'=>'Phone number has been saved'],200);
                    } else {
                        return Response::json(['error_msg'=>'something went wrong'],404);
                    }
                }
                
    	    }
        } else {
            return Response::json(['error_msg'=>'You have not permission to add and edit phone.'],404);
        }
    }

    /**
    * this function changed status of email and phone number  
    *
    * @return true on success 
    */
    public function changeStatus()
    {
        if(isset($this->vdata['curModAccess']['add']) && $this->vdata['curModAccess']['add'] == 1 || isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){
        	if(Request::isMethod('post')) {
        		$formData = Input::all();
        		$is_edit = 1;
        		$id= $formData['id'];
        		if($formData['status'] == true || $formData['status'] == 1 ) {
        			$formData['is_active'] = 1;
        		} else {
        			$formData['is_active'] = 0;
        		}
                $is_verified = $this->getNotification($id,1);
        		$setData['is_active'] = $formData['is_active'];
                $setData['id'] = $id;
                if($is_verified) {
            		if($this->save($setData,$is_edit) != false) {
            			return Response::json(['success_msg'=>'success'],200);
            		} else {
            		    return Response::json(['message'=>'Something went wrong.'],404);
            		}
                } else{
                    return Response::json(['message'=>'Verification required.'],422);
                }
        	}
        } else {
            return Response::json(['message'=>'you have not permission to changed it'],403);
        }
    }

    /**
    * this function soft delete email and phone number 
    * 
    * @return true on success 
    */
    public function delete()
    {
        if(isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){
        	if(Request::isMethod('post')) {
        		$id = Input::get('id');
                $setData['id'] = $id;
        		$setData['status'] = 2;
        		$is_edit = 1;
        		if($this->save($setData,$is_edit) != false) {
        			return Response::json(['success_msg'=>'Record deleted successfully'],200);
        		} else {
        			return Response::json(['error_msg'=>'something went wrong'],404);
        		}
        	}
        } else {
            return Response::json(['error_msg'=>'you have not permission to delete'],404);
        }
    }

    /**
    * this function add and save notification data 
    *
    * @return true on success 
    */
    public function save($notificationData,$is_edit,$id=null)
    {	
        $is_save = 0;
    	if($is_edit == 0) {
            $created = Notification::create($notificationData);
            if($created) {
                $is_save = 1;
            } 
    	} else {
    		$where = array('user_id'=>$this->user->id,'id'=>$notificationData['id']);
    		if(Notification::where($where)->update($notificationData)) {
    			 $is_save = 1;
    		} 
    	}
        if($is_save == 1) {
            if(isset($notificationData['obj_type']) && $notificationData['obj_type'] == 1) {
                $mailer = new AppMailer;
                $notificationData['user_id'] = $this->user->id;
                $notificationData['user_name'] = $this->user->name;
                $mailer->sendVerificationMail($notificationData);
                if($is_edit == 0) {
                    return $created->id;
                } else {
                    return true;
                }
            } else {
                if($is_edit == 0) {
                    return $created->id;
                } else {
                    return true;
                }
            }
        }  else {
            return false;
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
        $data = Notification::whereToken($token)->firstOrFail();
        if(!empty($data)) {
            $data->token = '';
            $data->status = 1;
            $data->save();
            Session::put('is_show_conformation_modal',1);
            return redirect('notification');
/*            $this->vdata['notificationConfi'] = $data;
            $this->vdata['is_show_conformation_modal'] = 'in show-confirmation-modal';
            return view('front.notification',$this->vdata);*/  
        }
    }

    /**
    * resend verification mail  
    *
    * @return true on success 
    */
    public function resendMail()
    {
        if(Request::isMethod('post')) {
            $id = Input::get('id');
            $email = Input::get('email');
            $setData['token'] = hash_hmac('sha256', str_random(40), config('app.key'));
            $setData['status'] =  0;
            $setData['id'] =  $id;
            $setData['obj_type'] = 1;
            $setData['obj_value'] = $email;
            $is_edit = 1;
            if($this->save($setData,$is_edit,$id) != false) {
                return Response::json(['msg'=>"Confirmation mail send to $email"],200);
            } else {
                return Response::json(['error_msg'=>'Something went wrong'],200);
            }
        }
    }
    /**
    * this function change notification frequency
    *
    * @return true on success 
    */
    public function updateFrequency()
    {
        if(isset($this->vdata['curModAccess']['add']) && $this->vdata['curModAccess']['add'] == 1 || isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){
            if(Request::isMethod('post')) {
                $frequency = Input::get('frequency');
                $rules = array(
                        'frequency'=>'required|numeric'
                    );
                $validator = Validator::make(Input::all(),$rules);
                if($validator->passes()) {
                    $followUp = Input::get('followup');
                    $is_follow = isset($followUp)?Input::get('followup'):false;
                    $via = Input::get('via');
                    if($via == 1 && $is_follow) {
                        $updatedData = array('noti_email_followup_frequency'=>$frequency);
                    } else if($via == 1 && !$is_follow) {
                        $updatedData = array('noti_email_frequency'=>$frequency);
                    } else if($via == 2 && $is_follow) {
                        $updatedData = array('noti_mob_followup_frequency'=>$frequency);
                    } else if($via == 2 && !$is_follow) {
                         $updatedData = array('noti_mob_frequency'=>$frequency);
                    }
                    $isUpdated = User::where('id',$this->user->id)->update($updatedData);
                    if($isUpdated) {
                        return Response::json(['success_msg'=>'Frequency changed successfully'],200);
                    }
                } else {
                    return Response::json(['error'=>'Something went wrong'],404);
                }
            } else {
                return Response::json(['error'=>'Something went wrong'],404);
            }
        } else {
                return Response::json(['error'=>'You have not permission to changed'],404);
        }
    }

    /**
    * this function return notification data
    *
    * @return data on success 
    */
    public function getNotification($noti_id = null,$is_check = 0) 
    {
        if($is_check == 1 && $noti_id != null) {
            $where = array('id'=>$noti_id,'user_id'=>$this->user->id,'status'=>1);
            $notificationData =  Notification::where($where)->get()->count();
            if($notificationData == 0) {
                $notificationData = false;
            } else {
                $notificationData = true;
            }
        }  else {
            $notificationData =  Notification::where('user_id',$this->user->id)->where('status','<>',2)->get();
        }
        return $notificationData;
    }

    /**
    * send message 
    * @return true on success false otherwise
    */
    public function send_sms($phone_number="+918511307343")
    {
        $verification_code = rand(50000,99999);
        $text_msg = set_sms($verification_code);
        if(!$text_msg) {
            return false;
        }
        $sid = env('SMS_API_ACCOUNT_ID');
        $token = env('SMS_API_TOKEN');
        //echo $sid;
        $client = new Client($sid, $token);
        $from =  env('SMS_API_FROM');
        $data = $client->messages->create($phone_number,array('from'=>$from ,'body' =>"$text_msg"));
        return $verification_code;
    }

    /**
    * verify mobile number
    * @return true on success 
    */
    public function veriry_number()
    {
        if(Request::ajax()) {
            $code = Request::get('code');
            if(empty($code)) {
                return Response::json(['invalid code'],404);
            }
            $where = array('verification_code'=>$code);
            $notificationData = Notification::where($where)->get()->first();
            if(!empty($notificationData)) {
                $created_time = $notificationData->created_at;
                $time = $this->time_diff($created_time);
                /*if($time>5)  {
                    return Response::json(['invlide code'],402);
                }*/
                $notificationData->verification_code = null;
                $notificationData->status = 1;
                $notificationData->save();
                return Response::json(['verified'],200);

            } else {
                return Response::json(['invalid code'],404);
            }
        }
    }

    /**
    * find time different 
    */
    public function time_diff($created_date)
    {
        if($created_date)  {  
            $create_date=date_create("$created_date");
            $todays = date("Y-m-d H:i:s");
            $to_day=date_create("$todays");
            $diff=date_diff($create_date,$to_day);
            if($diff->h == 0 && $diff->d == 0) { 
                return $diff->i;  
            }   
        }              
    }

    /**
    * resend code to given user mobile number 
    */
    public function resend_code()
    {
        if(Request::ajax()) {
            $id = Request::get('id');
            if(!isset($id)) {
                return Response::json(['Something went wrong'],404);
            } 
            $where = array('id'=>$id,'obj_type'=>2);
            $notificationData = Notification::where($where)->where('status','<>',2)->get()->first();
            if(empty($notificationData)) {
                return Response::json(['Something went wrong'],404);
            }
            $number = $notificationData->country_code."".$notificationData->obj_value;
            $code = $this->send_sms($number);
            if($code) {
                $notificationData->verification_code = $code;
                $notificationData->save();
                return Response::json(['Done'],200);
            }
        }
    }
}