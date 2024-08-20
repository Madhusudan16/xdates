<?php 
namespace App\Http\Controllers\Cron;

use Illuminate\Routing\Controller;
use App\User;
use App\Commons\AppMailer;
use App\Commons\PlanPayments;
use App\Commons\Date;
use App\Models\Admin\Plan;
use App\Models\Front\UserPlan;
use App\Commons\UserBalance; 
use DB;
use App\Models\Front\Invite;
use App\Models\Front\Invoice;
use App\Models\Front\Card;
use App\Models\Admin\Setting;
use App\Models\Admin\CouponLog;
use App\Models\Admin\Coupon;

class Billing extends Controller
{
	
    /** 
    * set account suspended after days
    */
    public $suspended_account = 3;

    /** 
    * set account deactivate after days
    */
    public $deactivate_account = 18; 

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

    /**
    * This function get due plan amount record  
    */
    public function dua_amount_users()
    {
    	$next_bill_date = date("Y-m-d");
    	$where = array('status'=>1,'user_type'=>1);
    	$userData = User::whereDate('next_bill_date','<',$next_bill_date)->where($where)->where('current_plan','<>',0)->get();
      //preF($userData);
      if($userData->count() != 0) {
            foreach($userData as $key=> $user) {
            if($this->billing_log(null,$user->id)) {
                $days_diff = $this->find_days_diff($user->next_bill_date);
                
                if($days_diff == -($this->deactivate_account )) {
              		 $user->status = 2;
                     $this->deactivate_user($user->id);
                     $logsData['owner_id'] = $user->id;
                     $logsData['status'] = 0;
                     $this->billing_log($logsData);

                } else {
                    if(!$this->update_balance($user)) {
                      $user->is_expired = 1; 
                      $user->account_exp = date('Y-m-d h:i:s');
                      if($days_diff == -($this->suspended_account)) {
                         $user->account_suspended = 1;      
                      }
                      $logsData['owner_id'] = $user->id;
                      $logsData['status'] = 0;
                      $this->billing_log($logsData);

                    } else {
                      $logsData['owner_id'] = $user->id;
                      $logsData['status'] = 1;
                      $this->billing_log($logsData);
                      $user->is_expired = 0; 
                      $user->account_suspended = 0;
                      $user->account_exp = null;
                    }
                  
                }
              $user->save();
    		  }
              
        }
    	}
    }

    /**
    * update balance 
    */
    public function update_balance($userData)
    {
		if($userData->parent_user_id > 0) {
            $ownerID = $userData->parent_user_id;
            $adminID = $userData->id;
        } else {
            $ownerID = $userData->id;;
            $adminID = 0;
        } 
        $userplan   =  new UserPlan;
        $user_plan   = $userplan->where('status',1)->where('user_id',$ownerID)->get()->first();
        $planID = $user_plan->plan_id; 
    	$plan = Plan::where('id',$planID)->get()->first();
        if(empty($plan)) {
            return false;
        }	
    	$uBalance = new UserBalance;
    	$userBalance = $uBalance->getUserBalance($ownerID); 
    		//getUserBalance($userID,$type='amount');
    		
    	$amountNeedsToPay = $plan->cost;
    		
    		
    	//$previous_plan_amount =  $user_plan->plan_pay_amount;
    	$currentDate = date("Y-m-d");
    		
    	$user = new User;
        $users_count= $user->where(['parent_user_id'=>$ownerID, 'status'=>1])->orWhere(['id'=>$ownerID , 'status'=>1])->count(); 
          
    	  //check if no. of users exceed as per selected plan
        $max_allowed_users = $plan->n_allowed_users;
        if($users_count > $max_allowed_users){
            return false;
        }
    		
    	$new_user_plan = new UserPlan;
    	$new_user_plan->plan_id = $plan->id;
        $new_user_plan->user_id = $ownerID;
        $new_user_plan->plan_name  = $plan->name;
        $new_user_plan->plan_amount = $plan->cost;
        $new_user_plan->n_allowed_users = $plan->n_allowed_users;
        $new_user_plan->status = 1;
        $new_user_plan->plan_start_date= date('Y-m-d');
        $new_user_plan->plan_end_date  = date('Y-m-d', strtotime("+30 days")); 
    	$new_user_plan->plan_pay_amount = $amountNeedsToPay;
    	$new_user_plan->referal_discount = $plan->refer_percentage;
    	$debitArr = array();
    	$creditArr = array();
    	if(!empty($user_plan)){
            $new_user_plan->coupon_id = $user_plan->coupon_id;
            $oneDayAmount = $user_plan->plan_amount - $user_plan->discount_amount;
    	    $oneDayAmount = $oneDayAmount/30;
    		$nUsedDays = $this->find_days_diff($user_plan->plan_end_date." 23:59:59",$user_plan->plan_start_date." 00:00:00");
    		$amountDeducted = round($oneDayAmount * $nUsedDays,2);
    		$user_plan->plan_pay_amount = $amountDeducted;
    		$userBalance = $userBalance - $amountDeducted;
    		$user_plan->status = 0;
    		$debitArr[] =  array('amount'=>$amountDeducted);
      			
      		if($userBalance >= $plan->cost){
      			$amountNeedsToPay = 0;	
      		}else{
      			$amountNeedsToPay = ($plan->cost - $userBalance); 
      		}
    		//print('Next Date ' . date('Y-m-d', strtotime('-1 day', strtotime($date_raw))));
    		//$new_user_plan->plan_end_date = $user_plan->plan_end_date; 
    			
    		//$user_plan->plan_end_date = date('Y-m-d', strtotime('-1 day', strtotime($currentDate))) ;
    		//$user_plan->plan_end_date = date('Y-m-d');
    			
    	} 
        /* coupon apply Code */ 

    	$discount_amount  = $this->coupon($new_user_plan,$userData);
        if($discount_amount && is_array($discount_amount)) {
            $new_user_plan->coupon_id = $user_plan->coupon_id;
            $new_user_plan->discount_amount = $discount_amount['discount'];
            $new_user_plan->coupon_id = $discount_amount['coupon_id'];
            if($discount_amount['plan_pay_amount'] > 0) {
                $amountNeedsToPay  = $amountNeedsToPay - $discount_amount['plan_pay_amount'];
                if($amountNeedsToPay < 0) {
                  $amountNeedsToPay = 0;
                }
                //$new_user_plan->plan_pay_amount = $amountNeedsToPay;
            }
        } else {
            $new_user_plan->coupon_id = 0;
        }
        /* end coupon apply code */
    	if($amountNeedsToPay > 0){
    		$PlanPayments = new PlanPayments;
    		if(!empty($user_plan)){
                $payment = $PlanPayments->addPlanAndPayment($amountNeedsToPay, $userData, $new_user_plan,$user_plan);
    			} else {
    			    $payment = $PlanPayments->addPlanAndPayment($amountNeedsToPay, $userData, $new_user_plan);
    			}
    			
    		if($payment['type'] == 'success'){
                if(isset($discount_amount['coupon_log']) && !empty($discount_amount['coupon_log']))  {
                    $discount_amount['coupon_log']->save();
                  
                }   
                if(isset($discount_amount['old_coupon_log']) && !empty($discount_amount['old_coupon_log']))  {
                    $discount_amount['old_coupon_log']->save();
                  
                }
                $invoiceNo = $this->create_invoice($new_user_plan);
                $new_user_plan->invoice_no = $invoiceNo;
    			$new_user_plan->save();
    			$creditArr[] = array('amount'=>$amountNeedsToPay);
    			//add credit balance to user  
                $credit_amount = 0;
    			foreach($creditArr as $credit){ 
    				if($credit['amount'] > 0){ 
                        $credit_amount += $credit['amount']; 
    					$uBalance->addCredit($credit['amount'],$ownerID,'amount',$adminID);
    			    }
    			}

    		    if($userData->refer_via != null && isset($userData->refer_via)) {
    				$this->user_referral($userData,$user_plan->invoice_no);
                }
				//add debit balance to user 
                foreach($debitArr as $debit){
        			if($debit['amount'] > 0){  
        				$uBalance->addDebit($debit['amount'],$ownerID,'amount',$adminID);
        			}
    			}
    			$this->send_mail($new_user_plan,$userData,1,$credit_amount);
    			return true;
    				
    		} else if($payment['type'] == 'error' && $payment['msg_type'] == 'credit_card_not_found') { 
    			$this->send_mail($new_user_plan,$userData,0);
    			return false;
    		} else if($payment['type'] == 'error' && $payment['msg_type'] == 'transaction_error'){
    			$this->send_mail($new_user_plan,$userData,0);
    			return false;
    		} else {
    			$this->send_mail($new_user_plan,$userData,0);
    			return false;
    		} 

    	} else{

            if(isset($discount_amount['coupon_log']) && !empty($discount_amount['coupon_log']))  {
                $discount_amount['coupon_log']->save();
            }  
            
            if(isset($discount_amount['old_coupon_log']) && !empty($discount_amount['old_coupon_log']))  {
                    $discount_amount['old_coupon_log']->save();
            }

            $userData->current_plan = $new_user_plan->plan_id;
    		if($userData->refer_via != null && isset($userData->refer_via)) {
    			$this->user_referral($userData,$user_plan->invoice_no);
    		}
    	    foreach($debitArr as $debit) {
    	        if($debit['amount'] > 0) {  
    	            $debit_amount = $debit['amount'];
    	            $uBalance->addDebit($debit_amount,$ownerID,'amount',$adminID);
    	        }
    	    }
    	    $userData->next_bill_date = $new_user_plan->plan_end_date;
    	    $userData->save();
    		//save new plan
    		$invoiceNo = $this->create_invoice($new_user_plan);
            $new_user_plan->invoice_no = $invoiceNo;
    		$new_user_plan->trans_id = 0;
    		$new_user_plan->save();
    		$this->send_mail($new_user_plan,$userData);
    	    //save previous plan if exists
    		if(!empty($user_plan)){
    			$user_plan->save();
    		}
    		return true;			
    	} 
	}

	/**
    * this function check this user is referral via any other
    */
    public function user_referral($userData,$invoice_no)
    {

        if(!empty($userData) && !empty($invoice_no) ) {
            
            $userPlanData = UserPlan::where('invoice_no',$invoice_no)->get(); // get all which buy for previous month
            $amount = 0;
            if($userPlanData->count() != 0) {
                $totalCost = 0;
                foreach($userPlanData as $plan){
                    $plan_pay_amount = round($plan->plan_pay_amount*$plan->referal_discount/100,2);
                    $totalCost += $plan_pay_amount;
                }
                $amount = $totalCost;
            }
            $uBalanceObj = new UserBalance;
           
            $referralUserData = User::where('id',$userData->refer_via)->get()->first();
            if(!empty($referralUserData)) {
                $owner_id = ($referralUserData->parent_user_id != 0) ? $referralUserData->parent_user_id : $referralUserData->id;
                $real_user_id = ($referralUserData->parent_user_id != 0) ? $referralUserData->id : $referralUserData->parent_user_id;

                $count_referral = $uBalanceObj->number_of_referral_record($owner_id,$real_user_id,$userData->id);
                if($count_referral <= 3) {
                    $uBalanceObj->addCredit($amount,$owner_id,'amount',$real_user_id,$userData->id);
                    $where = array('to_user_id'=>$userData->id,'from_user_id'=>$userData->refer_via);
                    $inviteData = $this->get_previous_balance($where);
                    if(isset($inviteData->amount) && !empty($inviteData->amount)) {
                        $amount += $inviteData->amount;
                    }
                    $update_data = array('amount'=>$amount,'status'=>2,'subscribe_date'=>date('Y-m-d'));
                    $this->update_referral_user_data($where,$update_data);
                }
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
    * this function return days different
    */
    private function getDaysDiffFromNow($startDate,$endDate){
		$toDate = strtotime($endDate); // or your date as well
	    $fromDate = strtotime($startDate);
	    $datediff = $toDate - $fromDate;
      return floor($datediff/(60*60*24));
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
          if(!empty($adminAddress)) {
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
    public function send_mail($planData , $userData , $is_success = 1,$card_charge=0) 
    {
        if(!empty($planData)) {
            $appMailer = new AppMailer;
            if($appMailer->payment_mail($planData,$userData,$is_success,$card_charge) ) {
                return true;
            } else {
                return false; 
            }
        }
    }

    /**
    * this function return  days different
    */
    public function find_days_diff($end_date , $start_date = null) 
    {
        if(!empty($start_date)) {
            $start_date = date_create($start_date);
        } else {
            $start_date = date_create();
        }
        if(!empty($end_date)) {
            $end_date = date_create($end_date);
        } else {
            return false;
        }
        $date_diff = date_diff($start_date,$end_date);
        return $date_diff->format("%R%a");
    }

    /**
    * this function  save billing log 
    */
    public function billing_log($logData , $owner_id = null) 
    {
      if(empty($logData) && !empty($owner_id)) {
            $to_day = date('Y-m-d H:i:s');
            
            $logs = DB::table('billing_cron_log')->where('owner_id',$owner_id)->orderBy('created_at','desc')->limit(1)->get();
            //preF($logs);
            if(empty($logs)) {
                return true;
            }
            $date_diff = find_days_diff($to_day,$logs[0]->created_at);
            //echo $date_diff;
            if($date_diff >= 1) {
               return true;
            } else {
                return false;
            }
        } else if(!empty($logData)){
            DB::table('billing_cron_log')->insert($logData); 
        }
    }

    /**
    * invites previous balance 
    */
    public function get_previous_balance($where) 
    {
        if(!empty($where)) {
            $inviteData = Invite::where($where)->get()->first();
            return $inviteData;
        }
    }

    /**
    * check coupon applied for this user if yes then add discount
    */
    public function coupon($planData,$userData)
    {
        $first_use = 0;
        if(!empty($planData) && !empty($userData))  {
            if($planData->coupon_id != 0) {
                $where  = array('owner_id'=>$userData->id,'plan_id'=>$planData->plan_id,'coupon_id'=>$planData->coupon_id);
                $n_coupon_apply = CouponLog::where('status',3)->where($where)->orderBy('created_at','desc')->get()->count();

                $couponLogData = CouponLog::where('status',1)->where($where)->orderBy('created_at','desc')->get()->first();
                if($couponLogData->n_time_allow == $n_coupon_apply+1) {
                    return false;
                }
                $reNewCouponData = new CouponLog;
                $reNewCouponData->coupon_id = $couponLogData->coupon_id;
                $reNewCouponData->owner_id = $couponLogData->owner_id;
                $reNewCouponData->discount = $couponLogData->discount; 
                $reNewCouponData->plan_id = $couponLogData->plan_id; 
                $reNewCouponData->plan_amount = $planData->plan_amount;
                $reNewCouponData->n_time_allow = $couponLogData->n_time_allow;  
            } else {
                $first_use = 1;
                $where  = array('owner_id'=>$userData->id,'status'=>0);
                $couponLogData = CouponLog::where($where)->orderBy('created_at','desc')->get()->first();
                if(empty($couponLogData)) {
                    return false;
                }
                if($couponLogData->n_time_allow == 0) {
                    $couponLogData->status = 3;
                    $couponLogData->save();
                    return false;
                }
                $couponLogData->plan_id = $planData->plan_id;
                $couponLogData->plan_id = $planData->plan_id;
                $couponLogData->plan_amount = $planData->plan_amount;

            }
            if(!empty($couponLogData)) {
                $amount = round(($planData->plan_amount * $couponLogData->discount) / 100,2);
                $new_plan_amount = $planData->plan_amount - $amount;
                $couponLogData->plan_id = $planData->plan_id;
                $couponLogData->plan_amount = $planData->plan_amount;
                $couponLogData->discount_amount = $amount;
                if($first_use == 1) {
                    $couponLogData->status = 1;
                    $couponLogData->discount_amount = $amount;
                    $couponLogData->status = 1;
                    $discount = array('plan_amount'=>$new_plan_amount,'plan_pay_amount'=>$amount,'coupon_id'=>$couponLogData->coupon_id,'coupon_log'=>$couponLogData,'discount'=>$amount);
                } else {
                    $couponLogData->status = 3;
                    $reNewCouponData->discount_amount = $amount; 
                    $reNewCouponData->status = 1;
                    //$reNewCouponData->save();
                    $discount = array('plan_amount'=>$new_plan_amount,'plan_pay_amount'=>$amount,'coupon_id'=>$couponLogData->coupon_id,'coupon_log'=>$reNewCouponData,'old_coupon_log'=>$couponLogData,'discount'=>$amount);
                }
                return $discount;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
    * this function deactivate sub user 
    * @return true on success
    */
    public function deactivate_user($id)
    {
        User::where('parent_user_id',$id)->update(['status'=>2]);
        return true;
    }
}

?>