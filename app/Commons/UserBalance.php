<?php
namespace App\Commons;
 
use DB;

class UserBalance{ 

    /**
     * Create a new user access instance.
     */
    public function __construct()
    {
      //do intital things
    } 
	
	public function addCredit($amountOrDays,$ownerID,$amtDaysType='amount',$realUserID=0,$balance_from = 0){
		
		$dataToInsert = array();
		
		if($amtDaysType =='amount' && $amountOrDays != ''){
			$dataToInsert['amount'] = $amountOrDays;
			$dataToInsert['real_user_id'] = $realUserID;
			$dataToInsert['owner_id'] = $ownerID;
			$dataToInsert['balance_from_user_id'] = $balance_from;
			$dataToInsert['type'] = 1;
		}else if($amtDaysType =='days' && $amountOrDays != ''){
			$dataToInsert['trial_days'] = $amountOrDays;
			$dataToInsert['real_user_id'] = $realUserID;
			$dataToInsert['owner_id'] = $ownerID;
			$dataToInsert['balance_from_user_id'] = $balance_from;
			$dataToInsert['type'] = 1;
		}
		if(!empty($dataToInsert)){
			DB::table('user_balance')->insert($dataToInsert);	
		}
		
	}
	
	public function addDebit($amountOrDays,$ownerID,$amtDaysType='amount',$realUserID=0){
		
		$dataToInsert = array();
		
		if($amtDaysType =='amount' && $amountOrDays != ''){
			$dataToInsert['amount'] = $amountOrDays;
			$dataToInsert['real_user_id'] = $realUserID;
			$dataToInsert['owner_id'] = $ownerID;
			$dataToInsert['type'] = 2;
		}else if($amtDaysType =='days' && $amountOrDays != ''){
			$dataToInsert['trial_days'] = $amountOrDays;
			$dataToInsert['real_user_id'] = $realUserID;
			$dataToInsert['owner_id'] = $ownerID;
			$dataToInsert['type'] = 2;
		}
		if(!empty($dataToInsert)){
			DB::table('user_balance')->insert($dataToInsert);	
		}
		
	}
     /**
     * get user balance
     *
     * @param  USER ID $userID
	 * @param  amount,trial days $type
     * @return void
     */
	public function getUserBalance($userID,$type='amount'){
		$userBalanceData = DB::table('user_balance')->where('owner_id', $userID)->where('type', 1)->where('status',1)->get();
		if(count($userBalanceData) == 0) {
			return 0;
		}
		if($type == 'amount'){
			$cAmount = $this->getTotalAmtCredit($userID);
			$dAmount = $this->getTotalAmtDebit($userID);
			return ( float )($cAmount-$dAmount);
		}else if($type == 'days'){
			$cAmount = $this->getTotalDayCredit($userID);
			$dAmount = $this->getTotalDayDebit($userID);
			return ($cAmount-$dAmount);
		} 
		
		return 0;
	}
	
	/**
     * get total amount credit
     *
     * @param  USER ID $userID
     * @return void
     */
	public function getTotalAmtCredit($userID){
		
		$qData = DB::table('user_balance')->select(DB::raw('SUM(amount) as total_amount'))->where('owner_id', $userID)->where('type', 1)->where('status',1)->get();
		
		$totalAmount = $qData[0]->total_amount;
		
		return $totalAmount;
	}
	
	/**
     * get total amount debit
     *
     * @param  USER ID $userID
     * @return void
     */
	public function getTotalAmtDebit($userID){
		
		$qData = DB::table('user_balance')->select(DB::raw('SUM(amount) as total_amount'))->where('owner_id', $userID)->where('type', 2)->where('status',1)->get();
		
		$totalAmount = $qData[0]->total_amount;
		
		return $totalAmount;
	}
	
	/**
     * get total days credit
     *
     * @param  USER ID $userID
     * @return void
     */
	public function getTotalDayCredit($userID){
		
		$qData = DB::table('user_balance')->select(DB::raw('SUM(trial_days) as total_days'))->where('owner_id', $userID)->where('type', 1)->where('status',1)->get();
		$totalDays = $qData[0]->total_days;
		
		return $totalDays;
	}
	
	/**
     * get total days debit
     *
     * @param  USER ID $userID
     * @return void
     */
	public function getTotalDayDebit($userID){
		
		$qData = DB::table('user_balance')->select(DB::raw('SUM(trial_days) as total_days'))->where('owner_id', $userID)->where('type', 2)->where('status',1)->get();
		$totalDays = $qData[0]->total_days;
		
		return $totalDays;
	}

	/**
	* number of time get balance from referral
	* @return number of record
	*/
	public function number_of_referral_record($user_id,$real_user_id,$balance_from)
	 {
		$where = array('owner_id'=>$user_id,'real_user_id'=>$real_user_id,'balance_from_user_id'=>$balance_from);
		$record = DB::table('user_balance')->where($where)->count();
		return $record;
	}
}
