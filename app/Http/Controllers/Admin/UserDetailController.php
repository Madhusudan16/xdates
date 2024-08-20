<?php
namespace App\Http\Controllers\Admin;

use Auth;
use App\Commons\AdminUserAccess;
use App\User;
use Illuminate\Routing\Controller;
use Request;
use Illuminate\Support\Facades\Input;
use Response;
use Validator;
use JsValidator;
use App\Models\Admin\ExtendTrial;
use App\Commons\AppMailer;
use App\Models\Admin\Note;
use App\Models\Admin\Admin;
use App\Models\Front\LogModel;
use App\Models\Admin\Plan;
use App\Models\Front\UserPlan;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use App\Models\Front\Card;
use App\Models\Front\Country;
use App\Models\Front\Invoice;
use App\Models\Front\CancelAccount;
use App\Models\Front\UserTransaction;
use Session;
use App\UserCustomFields;

class UserDetailController extends Controller
{   
     use ResetsPasswords;
    /**
     * The module id
     */
    protected $moduleId = 1;

	/**
     * The guard name
     */
    protected $guard = 'admin';

	/**
     * view data
     */
    protected $vdata = array();

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
    * store client data
    */
    public $clientData = array();

    /**
    *  this variable store user status 
    */
    public $status = array();

    /**
    * this variable store plans
    */
    public $allPlan = array();

    /**
    * types of users 
    */
    public $userType = array();

    public function __construct(AdminUserAccess $userAccess)
    {
        $this->middleware('admin');
		$this->user = Auth::guard($this->guard)->user();
        $this->status = array('inactive','active','cancelled');
        $this->userType = array(1=>'Owner','Admin','User');
		if(!empty($this->user)){
			$this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);
			$this->vdata['user'] = $this->user;
			$this->vdata['curModAccess'] = $this->access['current'];
			$this->vdata['allModAccess'] = $this->access['all'];
        }
        $this->vdata['page_section_class'] = "admin-home-page";
        $allPlans = $this->getPlanById();
        foreach($allPlans as $plan) {
            $this->allPlan[$plan->id] = $plan->name;
        }
        
        $this->vdata['allPlan'] = $allPlans;
    }	
    
    /**
    * this functiom extend trial
    * 
    * @return true on success 
    */
    public function requestExtendTrial()
    {
    	$requester_id =  $this->user->id;
    	$is_admin = ($this->user->user_type == 1 || $this->user->user_type == 2 || $this->user->user_type == 3)? true : false;
        $user_id = Input::get('id');
        $note_detail = Input::get('note');
        if(strlen($note_detail) >=10) {
            $token = str_random(30);
            $requester_profile = $this->user->profile_image;
            if($is_admin) {
                $addNote = array('requester_id'=>$requester_id,'note_type'=>0,'detail'=>$note_detail,'requester_name'=>$this->user->name,'user_id'=>$user_id,'requester_profile'=>$requester_profile,'is_approved'=>1);
            } else {
                $addNote = array('requester_id'=>$requester_id,'note_type'=>0,'detail'=>$note_detail,'requester_name'=>$this->user->name,'user_id'=>$user_id,'requester_profile'=>$requester_profile);
            }
        	if($is_admin) {
        		$createData = array('user_id'=>$user_id,'requester_id'=>$requester_id,'token'=>$token,'is_approved'=>1);
        	} else {
        		$createData = array('user_id'=>$user_id,'requester_id'=>$requester_id,'token'=>$token);
                $setData = $this->setUserData($user_id,$token);
            }
        	ExtendTrial::create($createData);
            Note::create($addNote);
        	if($is_admin) {
        		$this->approveTrial($token);
        	} else {
               $mailer = new AppMailer;
               $setData['requestor_name'] = $this->user->name;
               $setData['note'] = $note_detail;
               $mailer->trialExtendVerificationMail($setData);
            }
        	return Response::json(['msg'=>'success'],200);
        } else {
            return Response::json(['msg'=>'error'],200);
        }
    }

    /**
    * approved trial 
    *
    * @return true on success 
    */
    public function approveTrial($token)
    {
        $mailer = new AppMailer;
    	//$UpdatedData = array();
    	$userData = ExtendTrial::where('token',$token)->get()->first();
    	if(isset($userData) && !empty($userData) && $userData->count() != 0) {
    		//$UpdatedData['trial_end_date'] = date('Y-m-d',strtotime("+30 day"));
    		$userObj = User::find($userData->user_id);
            $userData->trial_end_date =  $userObj->trial_end_date;
            $userObj->trial_start_date = date('Y-m-d');
            $userObj->is_expired = 0;
            $userObj->status = 1;
            $userObj->account_exp = null;
			$userObj->trial_end_date = date('Y-m-d',strtotime("+30 day"));
			$userObj->save();
    		$userData->is_approved = 1 ;

    		$userData->token =  '';
    		$userData->save();
            $mailer->informClientAboutTrial($userObj->email);
            return redirect('/admin')->with(['approvedTrial'=>1]);
    	} else {
    		return Response::view('errors.404',array('message'=>"The trial already extended for this user.",'title'=>"Trial Extended!",'is_admin'=>'admin'),404);
    	}
    }

    /**
    * this function return set dataget_user
    *
    * @return set Data
    */
    public function setUserData($userId,$token)
    {
        $mailTo = array(1,2,3);
        $adminData = Admin::whereIn('user_type',$mailTo)->where('status',1)->get(['email']);

        foreach($adminData as $admin) {
            $adminEmails[] = $admin->email;
        }
        $userData = User::where('id',$userId)->get()->first();
        $setData['email'] = $adminEmails;
        $setData['user_email'] = $userData->email;
        $setData['user_id'] = $userId;
        $setData['user_name'] = $userData->name;
        $setData['com_name'] = $userData->com_name;
        $setData['token'] = $token;
        return $setData;
    }

    /**
    * this function add note
    *
    * @return true on success
    */
    public function addNote()
    {
        if(Request::ajax()) {
            $requester_id = $this->user->id;
            $user_id  = Input::get('id');
            $note = Input::get('note');
            if(strlen($note) >=10 ) {
                $noteData = array('requester_id'=>$requester_id,'user_id'=>$user_id,'detail'=>$note,'note_type'=>1);
                if(Note::create($noteData)){
                    return Response::json(['msg'=>'success'],200);
                }
            } else {
                return Response::json(['msg'=>'fail'],200);
            }
        }
    }

    /**
    * this function show user details
    * 
    * @return  user-details.blade.php file
    */
    public function userDetails($id)
    {
        
        if($id && !empty($id)) {
            $clientData = $this->getClients($id); 
            if(empty($clientData)) {
                exit;
            } 
            if((!empty($clientData->account_exp)) && ($clientData->status != 1 || $clientData->is_expired == 1)) {
                $countActiveDays = $this->cal_activated_days($clientData->created_at,$clientData->account_exp);
                $this->vdata['countActiveDays'] = $countActiveDays;
            } else {
                $current_date = date("Y-m-d");
                $countActiveDays = $this->cal_activated_days($clientData->created_at,$current_date);
                $this->vdata['countActiveDays'] = $countActiveDays;
            } 
            $planData = $this->getPlanDetails($clientData);
            $trialExtendedData = $this->getTrialExtended($id);
            if($planData != false && !empty($planData)) {
                $this->vdata['planData'] = $planData;
            }
            $this->vdata['logs'] = $this->getLog($id);
            $this->vdata['notes'] = $this->getNote($id);
            //preF($this->vdata['notes']);
            $this->vdata['card_details'] = $this->get_card_details($id);
            $this->vdata['number_of_user'] = $this->find_number_of_user($id);
            $this->vdata['trialExtendedData'] = $trialExtendedData;
            $this->vdata['page_title'] = 'User Details';
            $this->vdata['subClients'] = $this->getClients(null,$id);
            $this->vdata['status'] = $this->status;
            $this->vdata['invoices'] = $this->get_invoice($id);
            $this->vdata['plans'] = $this->allPlan;
            $this->vdata['client'] = $clientData;
            $this->vdata['declined_payments'] = $this->get_declined_charge_log($id);
            $this->vdata['declinedTrialList'] = $this->get_declined_trial_list($id);
            $this->vdata['cancel_account_token'] = $this->check_account($id);
            $this->vdata['userType'] = $this->userType;
            return view('admin.user-details',$this->vdata);
        }
    }

    /**
    * this function return specific client's plan  data
    *
    * @return data on success 
    */
    public function getPlanDetails($data = null )
    {
        if($data != null) {
            $plan = $data->current_plan;
            if($plan != 0) {
                $planData = $this->getPlanById($plan);

                return $planData;
            } else {

                return false;
            }   
        }
    }

    /**
    * 
    * get plan data by plan id
    *
    * @return data on success
    */
    public function getPlanById($planId = null) 
    {
        if($planId != null) {
            $where = array('id'=>$planId,'status'=>1);
            $planData = Plan::where($where)->get()->first();
            $numberOfUser = UserPlan::where('plan_id',$planId)->where('status',0)->get();
            $planData['allowed_users'] = $planData->n_allowed_users;
            $planData['numberOfUser']  = $numberOfUser->count();
        } else {
            $where = array('status'=>1);
            $planData = Plan::where($where)->orderBy('cost')->get();
        }
        return $planData;
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail()
    {
        $email['email'] = Request::get('email');
        $rules = array(
                'email' => 'required|email'
            );
        $Validator = JsValidator::make($rules);
        //$broker = null;
        
        Password::setDefaultDriver('tempusers'); // change password reset config 
/*        $pass = new  ResetsPasswords;
        $pass->subject =  "X-Dates:reset your password";*/
        $response = Password::broker(null)->sendResetLink(
            $email,$this->resetEmailBuilder()
        );
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return Response::json(['message' => 'Password reset email successfully sent.'], 200);
            case Password::INVALID_USER:
                return Response::json(['message' => 'Sorry Unable to send reset Password'], 403);
            default:
                return Response::json(['message' => 'Somethig went wrong'], 404);
        }
    }

    /**
    * this function return number of time trial extended 
    *
    * 
    */
    public function getTrialExtended($id)
    {
        $data = ExtendTrial::where(['user_id'=>$id,'is_approved'=>1])->get(['trial_end_date']);
        return $data;
    }

    /**
    *  get note list
    *  
    * @return list of note
    */
    public function getNote($id)
    {

        $noteData = Note::with('get_user','get_actioner_user')->where('user_id',$id)->where('is_approved','<>',2)->orderBy('created_at', 'desc')->get();

        if($noteData->count() != 0) {
            foreach($noteData as $key=> $note) {
                $noteData[$key]->created_at = convertTimeToUSERzone($note->created_at,$this->user->choosed_timezone);

            }
        }
        //preF($noteData);
        return $noteData;
    }

    /**
    * get log data
    * @return log data
    */
    public function getLog($ownerId = null, $log_id = null)
    {
        if(isset($ownerId) && $ownerId != null && !empty($ownerId)) {
            $where['owner_id'] = $ownerId;
            $logData = LogModel::with('get_user')->where($where)->orderBy('created_at','desc')->get();
        } else if(isset($log_id) && $log_id != null && !empty($log_id)) {
            $where['id'] = $log_id;
            $logData = LogModel::with('get_user')->where($where)->orderBy('created_at','desc')->get()->first();
        } 

        if($log_id == null && $logData->count() != 0) {
            foreach($logData as $key=>$log) {
                $logData[$key]->created_at = convertTimeToUSERzone($log->created_at,$this->user->choosed_timezone);
            }
        }
        return $logData;
    }

    /**
    * this function return all user from users table
    *
    * @return all users data
    */
    public function getClients($id = null,$subUser = 0)
    {
        if(isset($id) && $id != null) {
            $where  = array('id'=>$id,'parent_user_id'=>0);
            $clients = User::where($where)->orderBy('name')->get()->first();
            
        } else {
            $where  = array('parent_user_id'=>$subUser);
            $orWhere  = array('id'=>$subUser);
            $clients = User::where('status','<>',2)->where($where)->orWhere($orWhere)->orderBy('user_type')->get();
        }
        return $clients;
    }

    /**
    * this function send mail to user for restore deleted Custom field
    *
    * @return true on success 
    */
    public function restore_mail()
    {
        if(Request::ajax()) {
            $id = Request::get('id');
            $logData = $this->getLog(null,$id);
            if(!empty($logData) && $logData->count() != 0) {
                $logs = json_decode($logData->notes,false);
                if(!empty($logs->id)) {
                    $where = array('id'=>$logs->id,'status'=>2);
                    $customFieldsData = UserCustomFields::where($where)->get()->first();
                    if(empty($customFieldsData) || count($customFieldsData) == 0) {
                        return Response::json(['message' => "$logs->name field already restored."], 200);    
                    }
                }
                $logs->requested_by = $this->user->name; 
                $logs->message = "If you want to restore below field data then click on restore link";
                $mailer =new AppMailer();
                $mailer->sendEmailRestoreTo($logs);
                if($mailer) {
                    return Response::json(['message' => 'Custom field restore link sent successfully.'], 200);
                }
            }
        }
    }

    /**
    * find current number of user for this owner
    *
    * @return true on success 
    */
    public function find_number_of_user($id)
    {   
        if($id) {
            $numberOfUser = User::where('status','!=',2)->where(['parent_user_id'=>$id])->orWhere('id',$id)->get()->count();
            return $numberOfUser;
        } else {
            return 0;
        }
    }

    /**
    * this function return number of active account 
    * 
    * @return active days
    */
    public function cal_activated_days($sign_up_date,$expired_date)
    {

        if(!empty($sign_up_date) && !empty($expired_date)) {
            $sign_up_date=date_create($sign_up_date);
            $expired_date=date_create($expired_date);
            $diff=date_diff($sign_up_date,$expired_date);

            return $diff->format("%a days");
        }
    }

    /**
    * this function return card Data
    * @return card data
    */
    public function get_card_details($id)
    {
        $cardDetails = Card::with('get_country','get_state')->where('user_id',$id)->get()->first();
        
        return $cardDetails;
    }

    /**
    * get invoice data
    */
    public function get_invoice($id = null)
    {
        if($id != null) {
            $where = array('status'=>1,'owner_id'=>$id);
        } else {
            $where = array('owner_id'=>$id);
        }
        $invoiceData = Invoice::where($where)->orderBy('bill_date','desc')->get()->toArray();
        //$invoiceData = $this->filter_invoice_record($invoiceData);
        return $invoiceData;
    }

    /**
    * this function filter invoice data
    */
    public function filter_invoice_record($invoiceData = null ,$paymentDeclinedData =null) 
    {

        if($invoiceData != null) {
            $allData = $invoiceData;
            //preF($filterData);
            
        } else if($paymentDeclinedData != null ){

            $allData = $paymentDeclinedData;
        }
        if(!empty($allData)) {
            foreach($allData as $data) {
                if(isset($data['trans_details']) && !empty($data['trans_details'])) {
                    $cardDetail =  json_decode($data['trans_details'],true);
                    if($cardDetail['expiry_date']) {
                        $data['expired_date'] = date('m/y',strtotime($cardDetail['expiry_date']));
                    }
                    $data['card_no'] = $cardDetail['accountNumber'];
                }
                    $filterData[$this->allPlan[$data['plan_id']]][] = $data;
            }
        }
        if(!empty($filterData)) {
            return $filterData;
        }
    }

    /**
    * this function check user account cancel or not
    */
    public function check_account($owner_id)
    {
        
        if(!empty($owner_id)) {
            $where = array('owner_id'=>$owner_id,'status'=>0);
            $cancelAccountData = CancelAccount::where($where)->get(['token','created_at'])->first();
            if(!empty($cancelAccountData)) {
                $days = find_days_diff($cancelAccountData->created_at);
                if($days <= -30) {
                    $userData = User::where('id',$owner_id)->get()->first();
                    $userData->status = 2;
                    $userData->save();
                    return false;
                } else {
                    return $cancelAccountData->token;
                }
            } else {
                return false;
            }
        }
    }

    /**
    * this function return declined charge result 
    */
    public function get_declined_charge_log($id) 
    {
        if(!empty($id)) {
            $where = array('user_id'=>$id,'status'=>0);
            $logs = UserTransaction::where($where)->get();
            if(!empty($logs)) {
                $logs = $logs->toArray();
                //preF($logs);
               // $logs = $this->filter_invoice_record(null,$logs);
                return $logs;
            } else {
                return false;
            }
        }
    }

    /**
    * this function allow admin user to login customer account
    */
    public function login_as_customer()
    {
        $user_id = Input::get('id');
        if(!empty($user_id)) {
            
            Auth::loginUsingId($user_id);
            if (Auth::check()) {
                Session::set('login_as_customer',$user_id);
                return Request::json(['msg'=>'successfully login'],200);
            } else {
                return Request::json(['msg'=>'unable to login with using this id'],404);
            }
        }
    }
    
    /**
    * this function get declined trial request log
    * @return data on success
    */
    public function get_declined_trial_list($id)
    {
        $where = array('user_id'=>$id,'note_type'=>0,'is_approved'=>2);
        $trialData = Note::with('get_user','get_actioner_user')->where($where)->get();
        //preF($trialData);
        if($trialData->count() != 0) {
            foreach($trialData as $key=>$trialLog) {
                $trialData[$key]->updated_at = convertTimeToUSERzone($trialLog->updated_at,$this->user->choosed_timezone);
            }
        }
        return $trialData;
    }
}