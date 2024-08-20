<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Commons\UserAccess; 
use App\Commons\UserBalance; 
use Illuminate\Http\Request;
use Hash;
use App\User;
use App\Models\Admin\Plan;
use App\Models\Front\UserPlan;
use Validator;
use JsValidator;
use Requests;
use Response;
use Log;
use App\Commons\Date;
use App\Models\Front\Tbltimezone;
use App\Http\Controllers\Controller;
use App\Commons\AppMailer;
use Illuminate\Support\Facades\Input;
use App\Commons\PlanPayments;
use DB;
use App\Models\Front\Invite;
use App\Models\Front\Invoice;
use App\Models\Front\Card;
use App\Models\Admin\Setting;
use App\Models\Admin\CouponLog;
use App\Models\Admin\Coupon;

class userChangePlanController extends Controller
{
  /**
     * The module id
     */
    protected $moduleId = 4;
  
  /**
     * The guard name
     */
    protected $guard = 'web';
  
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
    * save coupon exist for this user or not 
    */
    public $coupon_status = null;

    public function __construct(UserAccess $userAccess)
    {
       $this->middleware('auth'); 
       $this->user = Auth::guard($this->guard)->user(); 
       //$this->userObj = new User();
       $this->timezoneObj = new Tbltimezone();
       $this->coupon_status = $this->check_coupon();
       if(!empty($this->user)){ 
           $this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
           $this->vdata['user'] = $this->user; 
           $this->vdata['curModAccess'] = $this->access['current'];
           $this->vdata['allModAccess'] = $this->access['all'];
           $this->vdata['page_section_class'] = 'my-account cart plan';
       }
    }

     public function index()
    {
        $checkAccess = acoount_expire_text($this->user);
        $this->vdata['check_access'] = $checkAccess;
        
        if($page_url = prevent_user($this->user)) {
           return redirect($page_url);
        }
    	  $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id;     

        if($this->vdata['curModAccess']['upgrade'] != 1){
            return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        $plan = new Plan; 
        
        $this->vdata['page_title'] = 'Change Plan';
        $this->vdata['plan_list'] =$plans =$plan->where('status',1)->orderBy('n_allowed_users', 'ASC')->get();
        $user = new User;
        $this->vdata['total_user']=$users_count= $user->where(['parent_user_id'=>$ownerID , 'status'=>1])->orWhere(['id'=>$ownerID , 'status'=>1])->count();
        $userplan   =  new UserPlan;
        $user_plan   = $userplan->where('status',1)->where('user_id',$ownerID)->get()->first();
        if(!empty($user_plan)) {  
            $this->vdata['user_plan'] =$user_plan;
            $this->vdata['remaining_days'] = $this->get_remaining_plan_days($user_plan->plan_end_date);
        }
        if(!check_user($this->user->parent_user_id)) {
            Auth::logout();
        }
        $this->vdata['n_allowed_users'] = $this->plan_user();
         
        $uBalance = new UserBalance;

        $pay_amount = $this->find_plan_pay_amount();
        $this->vdata['coupon_used'] = $this->coupon_status;
        $user_balance = $uBalance->getUserBalance($ownerID);  
        //echo ( float )$user_balance-$pay_amount;
        $user_balance = round($user_balance-$pay_amount,2);
        
        $this->vdata['userBalance'] = $user_balance;
        return view('front.change-plan',$this->vdata);
    }
    
    
     public function changePlan(Request $request)
    {
      exit;
    	$ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id;  
		  $is_save = 0;  
      $userplan   =  new UserPlan;
      $userplans  =$userplan->where('status',1)->where('user_id',$ownerID)->get()->first();
       $plan= Plan::where('id',$request->plan)->get()->first();
      if ($userplans) {
           $user = new User;
           $users_count= $user->where(['parent_user_id'=>$ownerID, 'status'=>1])->orWhere(['id'=>$ownerID , 'status'=>1])->count(); 
          
           $max_allowed_users=$plan;
           if($users_count > $max_allowed_users->n_allowed_users){
              return response()->json(['error' => 'number of user exceed.'],403);
           }
          if($userplans->plan_end_date > date('Y-m-d') ){
               
                $userplan->plan_id = $request->plan;
                $userplan->user_id = $ownerID;
                $userplan->plan_name  = $plan->name;
                $userplan->plan_amount = $plan->cost;
                $userplan->n_allowed_users = $plan->n_allowed_users;
                $userplan->status=1;
                $userplan->plan_start_date= date('Y-m-d');
                $userplan->plan_end_date  = $userplans->plan_end_date;
                $userplan->save();
                $userplans->debit_amount = $this->get_amount($userplans->plan_start_date,$userplans->plan_amount,$plan->cost);
                $userplans->status=0;  
                $userplans->save();
                $this->user->current_plan = $request->plan;
                $this->user->save();
                $is_save = 1;
          }
           else{
              $userplans->status=0; 
               $userplans->save();
               $userplan->plan_id = $request->plan;
               $userplan->user_id = $ownerID;
               $userplan->plan_name  = $plan->name;
               $userplan->plan_amount = $plan->cost;
               $userplan->n_allowed_users = $plan->n_allowed_users;
               $userplan->status=1;
               $userplan->plan_start_date= date('Y-m-d');
               $userplan->plan_end_date  = date('Y-m-d', strtotime("+29 days"));
               $userplan->save();
               $this->user->current_plan = $request->plan;
               $this->user->save();
               $is_save = 1;
          }
           
      }else{
        $userplan->plan_id = $request->plan;
        $userplan->user_id = $ownerID;
        $userplan->plan_name  = $plan->name;
        $userplan->plan_amount = $plan->cost;
        $userplan->n_allowed_users = $plan->n_allowed_users;
        $userplan->status=1;
        $userplan->plan_start_date= date('Y-m-d');
        $userplan->plan_end_date  = date('Y-m-d', strtotime("+30 days"));
        $userplan->save();
        $is_save = 1;
     }
      if($is_save == 1) {
          if($this->user->refer_via != null && isset($this->user->refer_via)) {
              //$this->user_referral($this->user->refer_via,$plan->cost,$plan->refer_percentage);
          }
      }
    }

    /**
    * this function find remaining amount 
    *
    * @return amount 
    */

    public function get_amount($startDate, $planAmount,$currentPlan)
    {
        $planAmountDiff = $currentPlan - $planAmount;
        if($planAmountDiff > 0) {
            if(!empty($startDate) && isset($startDate) && !empty($planAmount)) {
                $startDate=date_create($startDate);
                $currentDate=date_create();
                $diff=date_diff($currentDate,$startDate);
                $days = $diff->format("%R%a");
                if($days > 0 ) {
                  $amount = $days * $planAmountDiff / 30;
                } else if($days == 0) {
                   $amount = $planAmountDiff;
                } else {
                    return -1;
                }
                return round($amount,2);
            }
        } else {
              return -1;
        }
    }
	private function getDaysDiffFromNow($startDate,$endDate){
		
		  $toDate = strtotime($endDate); // or your date as well
	    $fromDate = strtotime($startDate);
	    $datediff = $toDate - $fromDate;
      return floor($datediff/(60*60*24));
		 
	} 
	public function upgradeDowngradePlan(Request $request){
      if($this->vdata['curModAccess']['upgrade'] != 1){
          return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
      }
      if(isset($this->vdata['remaining_days']) && $this->vdata['remaining_days'] <= 0) {
          return response()->json(['error' => 'error_occured','msg'=>'Error occured, Please try again!'],403);
      }
      // $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id; 
      if($this->user->parent_user_id > 0) {
          $ownerID = $this->user->parent_user_id;
          $adminID = $this->user->id;
      } else {
          $ownerID = $this->user->id;;
          $adminID = 0;
      }
      $tempUser = new User;
		  $ownerUser = $tempUser->where('id',$ownerID)->first();
		  $planID = $request->input('plan'); 
		  $plan = Plan::where('id',$planID)->get()->first();
		  if(empty($plan)){
			    return response()->json(['error' => 'no_plan_found','msg'=>'Plan not found, Please try to choose another plan.'],403);
		  }
		  $uBalance = new UserBalance;
		  $userBalance = $uBalance->getUserBalance($ownerID); 
		  //getUserBalance($userID,$type='amount');
		  $amountNeedsToPay = $plan->cost;
		  $userplan   =  new UserPlan;
      $user_plan   = $userplan->where('status',1)->where('user_id',$ownerID)->get()->first();
		  //$previous_plan_amount =  $user_plan->plan_pay_amount;
		  $currentDate = date("Y-m-d");
		  $user = new User;
      $users_count= $user->where(['parent_user_id'=>$ownerID, 'status'=>1])->orWhere(['id'=>$ownerID , 'status'=>1])->count(); 
      //check if no. of users exceed as per selected plan
      $max_allowed_users = $plan->n_allowed_users;
      if($users_count > $max_allowed_users){
          return response()->json(['error' => 'n_user_exceed'],403);
      }
		  $new_user_plan = new UserPlan;
		  $new_user_plan->plan_id = $plan->id;
      $new_user_plan->user_id = $ownerID;
      $new_user_plan->plan_name  = $plan->name;
      $new_user_plan->plan_amount = $plan->cost;
      $new_user_plan->n_allowed_users = $plan->n_allowed_users;
      $new_user_plan->referal_discount = $plan->refer_percentage;
      $new_user_plan->status = 1;
      $new_user_plan->plan_start_date= date('Y-m-d');
      if(empty($user_plan)){
          $new_user_plan->plan_end_date  = date('Y-m-d', strtotime("+30 days")); 
      }
      
	   	$new_user_plan->plan_pay_amount = $amountNeedsToPay;
		  $debitArr = array();
		  $creditArr = array();
		  if(!empty($user_plan)){
			    // get amount of plan as per no. of days used
          $new_user_plan->coupon_id = $user_plan->coupon_id;
          $oneDayAmount = $user_plan->plan_amount - $user_plan->discount_amount;
          
			    $oneDayAmount = $oneDayAmount/30;
			    $nUsedDays = find_days_diff($currentDate,$user_plan->plan_start_date);
			    $amountDeducted = round($oneDayAmount * $nUsedDays,2);
			    $user_plan->plan_pay_amount = $amountDeducted;
			    $userBalance = $userBalance - $amountDeducted;
			    $user_plan->status = 0;
			    $debitArr[] =  array('amount'=>$amountDeducted);
			    //get amount of plan as per no. of days will be used
			    $f_oneDayAmount = $plan->cost;
			    $f_oneDayAmount = $f_oneDayAmount/30;
  			  $f_nUsedDays = find_days_diff($user_plan->plan_end_date." 23:59:59",$currentDate."00:00:00");
  			  $amountWillCharge = round($f_oneDayAmount * $f_nUsedDays,2);
			    //$debitArr[] =  array('amount'=>$amountWillCharge);
			    $new_user_plan->plan_pay_amount = $amountWillCharge;
          $new_user_plan->invoice_no = $user_plan->invoice_no;
          
			    if($userBalance >= $amountWillCharge){
				      $amountNeedsToPay = 0;	
			    } else { 
				      $amountNeedsToPay = ($amountWillCharge - $userBalance); 
			    }
			    //print('Next Date ' . date('Y-m-d', strtotime('-1 day', strtotime($date_raw))));
    			$new_user_plan->plan_end_date = $user_plan->plan_end_date; 
    			
    			$user_plan->plan_end_date = date('Y-m-d', strtotime('-1 day', strtotime($currentDate))) ;
			
      } else {
          $userBalance = $uBalance->getUserBalance($ownerID);
          $amountNeedsToPay = $amountNeedsToPay -$userBalance;
      }
		  $discount_amount  = $this->coupon($new_user_plan);
      //preF($discount_amount);
      if($discount_amount && is_array($discount_amount)) {
          $new_user_plan->discount_amount = $discount_amount['discount'];
          $new_user_plan->coupon_id = $discount_amount['coupon_id'];
          //print_r($new_user_plan);
           //preF($new_user_plan);
          if($discount_amount['plan_pay_amount'] > 0) {
              
              $amountNeedsToPay  = $amountNeedsToPay - $discount_amount['plan_pay_amount'];
              if($amountNeedsToPay < 0) {
                  $amountNeedsToPay = 0;
              }
              /*$new_pay_amount = $new_user_plan->plan_pay_amount-$discount_amount['plan_pay_amount'];*/
              /*if($new_pay_amount > 0) {
                  $new_user_plan->plan_pay_amount = $new_pay_amount;
              } else {
                  $new_user_plan->plan_pay_amount = 0;
              }*/
              
          }
      } else {
          $new_user_plan->coupon_id = 0;
      }
		  if($amountNeedsToPay > 0){
    			$PlanPayments = new PlanPayments;
    			if(!empty($user_plan)){ 
    				$payment = $PlanPayments->addPlanAndPayment($amountNeedsToPay, $ownerUser, $new_user_plan,$user_plan);
    			}else{
            $payment = $PlanPayments->addPlanAndPayment($amountNeedsToPay, $ownerUser, $new_user_plan);
          }
			if($payment['type'] == 'success'){
            $this->user->is_expired = 0;
            $this->user->account_exp = null;
            $this->user->save();
            if(isset($discount_amount['coupon_log']) && !empty($discount_amount['coupon_log']))  {
                  $discount_amount['coupon_log']->save();
            }
            if(empty($user_plan)){
              $invoiceNo = $this->create_invoice($new_user_plan);
              $new_user_plan->invoice_no = $invoiceNo;
            }
            $new_user_plan->save();
           
    				$creditArr[] = array('amount'=>$amountNeedsToPay);
            //add credit balance to user  
            $credit_amount = 0; 
    				foreach($creditArr as $credit){ 
    					if($credit['amount'] > 0){  
                $credit_amount += $credit['amount'];
    						$uBalance->addCredit($credit['amount'],$ownerID,'amount',$adminID);
                /*if($this->user->refer_via != null && isset($this->user->refer_via) && empty($user_plan)) {
                    $this->user_referral($this->user->refer_via,$plan->cost,$plan->refer_percentage);
                }*/
    					}
    				}
				   $this->send_mail($new_user_plan,1,$credit_amount);
    				//add debit balance to user 
    				foreach($debitArr as $debit){
    					if($debit['amount'] > 0){  
    						$uBalance->addDebit($debit['amount'],$ownerID,'amount',$adminID);
    					}
    				}
				
				  return response()->json(['success'=>'true','msg'=>'Your plan has been switched successfully.'],200);
				
			} else if($payment['type'] == 'error' && $payment['msg_type'] == 'credit_card_not_found') { 
				  return response()->json(['error' => 'credit_card_not_found.','msg'=>$payment['message']],403);
			} else if($payment['type'] == 'error' && $payment['msg_type'] == 'transaction_error'){
          $this->send_mail($new_user_plan,0);
				  return response()->json(['error' => 'credit_card_not_found.','msg'=>$payment['message']],403);
			} else {
          $this->send_mail($new_user_plan,0);
				  return response()->json(['error' => 'error_occured','msg'=>'Error occured, Please try again!'],403);
			} 

		  } else {

          if(isset($discount_amount['coupon_log']) && !empty($discount_amount['coupon_log']))  {
              $discount_amount['coupon_log']->save();
          }

          $ownerUser->current_plan = $new_user_plan->plan_id;
    			$ownerUser->save();
          foreach($debitArr as $debit){
              if($debit['amount'] > 0){  
                $debit_amount = $debit['amount'];
                $uBalance->addDebit($debit_amount,$ownerID,'amount',$adminID);
              }
          }

    			//save new plan
          if(!empty($user_plan)){
            $user_plan->save();
          } else {
            $invoiceNo = $this->create_invoice($new_user_plan);
            $new_user_plan->invoice_no = $invoiceNo;
            $this->user->next_bill_date = $new_user_plan->plan_end_date;
            $this->user->save();
          }
          $this->send_mail($new_user_plan);
    			$new_user_plan->trans_id = 0;
    			$new_user_plan->save();
    			  $this->user->is_expired = 0;
            $this->change_sub_user_flag($this->user->id);
            $this->user->account_exp = null;
            $this->user->save();
    			//save previous plan if exists
    			
    			return response()->json(['success'=>'true','msg'=>'Your plan has been switched successfully.'],200); 
			
		  } 
	}
    /**
    * get current user data
    * 
    * @return amount
    */
    public function getUserPlanWithAmount()
    {
    	  $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id;  
		
        $planId = Input::get('id');
        $plan= Plan::where('id',$planId)->get()->first();
        $userplan   =  new UserPlan;
        $user_plan   = $userplan->where('status',1)->where('user_id',$ownerID)->get()->first();
        $amount = $this->get_amount($user_plan->plan_start_date,$user_plan->plan_amount,$plan->cost);
        return Response::json(['amount'=>$amount],200);
    }

    /*
    * this function return user plan data
    */
    public function plan_user()
    {
        $plan = new Plan; 
        if($this->user->current_plan != 0) {
            $n_allowed_users =$plan->where('id', $this->user->current_plan)->get(['n_allowed_users'])->first()->toArray();
        } else {
            $n_allowed_users['n_allowed_users'] = DB::table('plans')->where('status',1)->max('n_allowed_users');
        }
        return $n_allowed_users['n_allowed_users'];
    }

    /**
    * this function check this user is referral via any other
    */
    public function user_referral($user_id,$cost,$refer_percentage)
    {

        if(!empty($user_id) && !empty($cost) && !empty($refer_percentage)) {
            $amount = round($cost*$refer_percentage/100,2); // referral account get 50% amount of pack
            $referralUserData = User::where('id',$user_id)->get()->first();
            if(!empty($referralUserData)) {
                $uBalanceObj = new UserBalance;
                $owner_id = ($referralUserData->parent_user_id != 0) ? $referralUserData->parent_user_id : $referralUserData->id;
                $real_user_id = ($referralUserData->parent_user_id != 0) ? $referralUserData->id : $referralUserData->parent_user_id;

                $uBalanceObj->addCredit($amount,$owner_id,'amount',$real_user_id,$this->user->id);
                $where = array('to_user_id'=>$this->user->id,'from_user_id'=>$user_id);
                $update_data = array('amount'=>$amount,'status'=>2,'subscribe_date'=>date('Y-m-d'));
                $this->update_referral_user_data($where,$update_data);
            }
        }
    }

    /**
    * update invite table data
    */
    public function update_referral_user_data($where,$update_data)
    {
        if(Invite::where('status','<>',3)->where($where)->update($update_data)) {
           return true;
        }
    }

    /**
    * this function made invoice new entry
    */
    public function create_invoice($planData)
    {
      
      if(!empty($planData)) {
          $tempAddress = array();
          $toAddress = "";
          $get_card_data = $this->get_card_data($planData->user_id);
          if($get_card_data) {
            $tempAddress['first_name'] = $get_card_data->billing_first_name;
            $tempAddress['last_name'] = $get_card_data->billing_last_name;
            $tempAddress['address'] = $get_card_data->address_line_1;
            $tempAddress['city'] = $get_card_data->city;
            $tempAddress['state'] = $get_card_data['get_state']->state_name;
            $tempAddress['country'] = $get_card_data['get_country']->name;
            $tempAddress['zip_code'] = $get_card_data->zip_code;
            $toAddress = json_encode($tempAddress);
          }
          $adminAddress = Setting::where('field_key','address')->get(['field_value'])->first();
          if(!empty($adminAddress->field_value)) {
              $createData = array('bill_date'=>$planData->plan_end_date,'owner_id'=>$planData->user_id,'to_address'=>$toAddress,'from_address'=>$adminAddress->field_value,'plan_id'=>$planData->plan_id);
          } else {
              $createData = array('bill_date'=>$planData->plan_end_date,'owner_id'=>$planData->user_id,'to_address'=>$toAddress,'plan_id'=>$planData->plan_id);
          }
          $invoiceData = Invoice::create($createData);
          return $invoiceData['id'];
      }
    }

    /**
    * this function return user card record
    */
    public function get_card_data($owner_id) 
    {
        if(!empty($owner_id)) {
            $cardData = Card::with('get_country','get_state')->where('user_id',$owner_id)->get()->first();
            if(!empty($cardData)) {
              return $cardData;
            } else {
              return false;
            }
        }
    }

    /**
    * send mail 
    */ 
    public function send_mail($planData , $is_success = 1,$card_charge = 0) 
    {
        if(!empty($planData)) {
            $appMailer = new AppMailer;
            if($appMailer->payment_mail($planData,$this->user,$is_success,$card_charge) ) {
                return true;
            } else {
                return false; 
            }
        }
    }

    /**
    * this function apply coupon which user enter 
    * @return succes on apply
    */
    public function apply_coupon(Request $request)
    {
        if($request->ajax()) {
            $coupon_code = $request->input('coupon');
            $message = $this->get_message();
            $where =  array('coupon'=>$coupon_code);
            $coupon_details = Coupon::where('status','<>',0)->where($where)->get()->first();
            $validationMessage = $this->validate_coupon($coupon_details);
            if($validationMessage != 'success') {
                return Response::json(['msg'=>$message[$validationMessage]],403);
            } else {
                $type = ($coupon_details->user_type == 2) ? 1 : 0;
                $coupon_log = array('coupon_id'=>$coupon_details->id,'owner_id'=>$this->user->id,'type'=>$type);

                if($type == 1 ) {
                    $coupon_log['trial_days'] = $coupon_details->coupan_day;
                    $this->add_trial($coupon_details->coupan_day);
                    $coupon_log['status'] =  1;
                    
                } else {
                    $coupon_log['discount'] = $coupon_details->coupon_percent;
                    $coupon_log['n_time_allow'] = $coupon_details->no_of_time;
                }

                CouponLog::create($coupon_log);
                $coupon_details->status = 3;
                $coupon_details->save();
                return Response::json(['msg'=>$message[$validationMessage]],200);
            }
        }
    }

    /**
    * this function check coupon exist or not and allow for current user
    */
    public function validate_coupon($coupon_details) 
    {
        if(empty($coupon_details) || $coupon_details->status == 2) {
            return 'invalid';
        } else {
            $coupon_expired_days = find_days_diff($coupon_details['coupon_expire']);
            
            if($coupon_details['status'] == 3) {
                return 'used';
            } 
            if($coupon_details['status'] == 4 ||  $coupon_expired_days < 0) {
                return 'expired';
            }
            if(($coupon_details['user_type'] == 2 && $this->user->current_plan != 0) || ($coupon_details['user_type'] == 1 && $this->user->current_plan == 0)) {
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
        $setMessage['used'] = 'This coupon code have been already used.';
        $setMessage['expired'] = 'This coupon code have been expired.';
        $setMessage['success'] = 'This coupon is successfully applied, now we will add coupon discount in next plan.';
        $setMessage['error'] = "opps! something went wrong.";
        $setMessage['invalid_user'] = "Invalid coupon, This code does not applicable!";
        $setMessage['invalid'] = "Invalid coupon, This code does not applicable!";
        return $setMessage;
    }

    /**
    * check coupon applied for this user if yes then add discount
    */
    public function coupon($planData)
    {
        if(!empty($planData)) {
            $where  = array('status'=>0,'owner_id'=>$this->user->id);
            $couponLogData = CouponLog::where($where)->orderBy('created_at','desc')->get()->first();
            if(!empty($couponLogData)) {
                $amount = round(($planData->plan_amount * $couponLogData->discount) / 100,2);
                $new_plan_amount = $planData->plan_amount - $amount;
                $plan_pay_amount = round(($planData->plan_pay_amount * $couponLogData->discount) / 100,2);
                $couponLogData->plan_id = $planData->plan_id;
                $couponLogData->plan_amount = $planData->plan_amount;
                $couponLogData->discount_amount = $amount;
                $couponLogData->status = 1;
                //$couponLogData->save();
                $discount = array('plan_amount'=>$new_plan_amount,'plan_pay_amount'=>$plan_pay_amount,'coupon_id'=>$couponLogData->coupon_id,'coupon_log'=>$couponLogData,'discount'=>$amount);
                return $discount;
            } else {
                if($planData->coupon_id != 0) {
                    $couponLogData = CouponLog::where('coupon_id',$planData->coupon_id)->orderBy('created_at','desc')->get()->first();
                    $couponLogData->status = 4 ; 
                    $couponLogData->save();
                }
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
    * this function add trial days
    * @return true on success
    */
    public function add_trial($days) 
    {
        if(!empty($days)) {
            $dateDiff = find_days_diff($this->user->trial_end_date);
            if($dateDiff > 0) {
                $newTrialEndDate = date("Y-m-d", strtotime ($this->user->trial_end_date ."+$days days"));
            } else {
                $newTrialEndDate = date("Y-m-d", strtotime ("+$days days"));
            }
            $this->user->trial_end_date = $newTrialEndDate ;
            $this->user->save();
            
            return true;
        } else {
          return false;
        }
    }

    /**
    * this function check coupon apply or not for current plan
    */
    public function check_coupon()
    {
        $where = array('owner_id'=>$this->user->id,'status'=>1,'plan_id'=>$this->user->current_plan);
        $couponData = CouponLog::where($where)->orderBy('created_at','desc')->get()->first();
        if(!empty($couponData)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
    * find current plan pay amount 
    */
    public function find_plan_pay_amount()
    {
        $owner_id = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id :$this->user->id;
        $userplan = new UserPlan;
        $user_plan   = $userplan->where('status',1)->where('user_id',$owner_id)->get()->first();
        //echo $user_plan->plan_end_date;
        //preF($user_plan);
        if(!empty($user_plan) ) {
            $is_plan_end = find_days_diff($user_plan->plan_end_date);
            if($is_plan_end < 0) {
                return $user_plan->plan_amount + $user_plan->plan_pay_amount;
            } 
            return $user_plan->plan_pay_amount-$user_plan->discount_amount;
        } else {
            return 0;
        }
    }

    /**
    * this function count days different from today to next billing date 
    * @return days
    */
    public function get_remaining_plan_days($plan_end_date)
    {
        if(!empty($plan_end_date)) {
            $currentDate = date('Y-m-d');
            $remaining_days = find_days_diff($plan_end_date." 23:59:59",$currentDate."00:00:00");
            return $remaining_days;
        }
        return 30;
    }

    /**
    * this function change expired flag in database
    */
    public function change_sub_user_flag($id) 
    {
        User::where('parent_user_id',$id)->update('is_expired',0);
        return true;
    }
} 
