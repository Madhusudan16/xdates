<?php 
namespace App\Http\Controllers\Cron;

use Illuminate\Routing\Controller;
use App\User;
use App\Models\Front\Invite;
use App\Commons\AppMailer;
use App\Commons\SettingVars;
use App\Models\Cron\MostReferral;

class findMostReferral extends Controller
{
	/**
	* this function find most referral of the month
	* 
	*/ 
	public function find_most_referral()
	{
		$dateArray = $this->find_date();
		if(!empty($dateArray)) {
			foreach($dateArray as $key=>$date) {
				$is_run = $this->logs(1,$key);
				if($is_run == 0) {
					$userData = $this->get_invites_data($date['start'],$date['end']);
					if(!empty($userData)) {
						$newData = $this->get_owner_details($userData['details']);
						if(!empty($newData)) {
							$userData['details'] = $newData;
						}
						$mailData = array('type'=>$key,'from_date'=>$date['start'],'to_date'=>$date['end']);
						if($this->send_mail($userData,$mailData)) {
							$users = array_keys($userData['count']);
							$logData['user_id'] = json_encode($users);
							$logData['from_date'] = $date['start']; 
							$logData['to_date'] = $date['end'];
							$this->logs(2,$key,$logData);
						} 
					} 
				}
			}
		}
	}

	/**
	* this function find all referral for month
	*/
	public function get_invites_data($start_date,$end_date) 
	{
		if(!empty($start_date) && !empty($end_date)) {
			$invitesData = Invite::with('user')->where('status','<>',0)->whereDate('invitation_accept_date','>=',$start_date)->whereDate('invitation_accept_date','<=',$end_date)->get()->toArray();
			$invitesData = $this->sort_user($invitesData);
			return $invitesData;
		}
	}

	/**
	* sort most 3 referral 
	*/
	public function sort_user($invoiceData)
	{
		if(!empty($invoiceData)) {
			$sortData = array();
			foreach($invoiceData as $invoice) {
				$sortData[$invoice['from_user_id']][] = $invoice;
			}
			$allIds = array_keys($sortData);
			//$maxData = array_count_values($sortData);
			$maxData = $this->count_array($allIds,$sortData);
			return $maxData;
		}
	}

	/**
	* count record  in array
 	*/
 	public function count_array($keys,$records)
 	{
 		if(!empty($keys) && !empty($records)) {
 			foreach($keys as $user_id) {
 				$numberOfRecord[$user_id] = count($records[$user_id]); 
 			}
 			arsort($numberOfRecord);
 			$maxRecord = $this->max_record($numberOfRecord,$records,3);
 			return $maxRecord;
 		}
 	}

 	/**
 	* this function return only 3 max record 
 	*/
 	public function max_record($count_record,$records,$max) 
 	{
 		if(!empty($count_record) && !empty($records)) {
 			$count = 1;
 			$maxRecord = array();
 			foreach($count_record as $key => $record) {
 				if($count <= $max) {
	 				$maxRecord[] = $records[$key][0];
	 				$totalReferral[$key] = $record;
	 			}
	 			$count++;
 			}
 			$allRecords['count'] = $totalReferral;
 			$allRecords['details'] = $maxRecord;
 			return $allRecords;
 		}
 	}

 	/**
 	* find date 
 	*/
 	public function find_date()
 	{

 		$startMonthDate = date('Y-m-d', strtotime('-1 month',strtotime(date('Y-m-1'))));
		/*$previousMonthDate = date('Y-m-d',strtotime('-1 month', strtotime($startMonthDate)));*/
		$endMonthDate = date("Y-m-t",strtotime($startMonthDate));

		if(date('m') == 1) {
			$startYearDate = date('Y-m-d', strtotime('-1 year',strtotime(date('Y-m-1'))));
			$endYearDate = date("Y-m-t",strtotime('+11 months',strtotime($startYearDate)));
			$dateArray['year'] = array('start'=>$startYearDate,'end'=>$endYearDate); 
		}
		$dateArray['month'] = array('start'=>$startMonthDate,'end'=>$endMonthDate);
		return $dateArray;
	}

	/**
	* this function send mail to owner 
	*/ 
	public function send_mail($invitesData,$mailData) 
	{
		$mail_address = SettingVars::get_setting_value('monthly_yearly_notification_on');
		if($mail_address && !empty($invitesData)) {
			$appMailer = new AppMailer;
			return $appMailer->most_referral_notification($mail_address,$invitesData,$mailData);
		}	
	}

	/**
	* insert or get log 
	* action = 1 means fetch data or 2 means insert Data
	*/
	public function logs($action = 1 ,$type = 'month', $logData =null) 
	{
		if($type == 'year') {
			$type = 1;
		} else {
			$type = 0;
		}
		if($logData != null && $action == 2) {
			$logData['status'] = $type;
			MostReferral::create($logData);
		} else {
			$where = array('status'=>$type);
			$logs =  MostReferral::where($where)->whereMonth('created_at','=',date('m'))->get()->count();
			return $logs;
		}
	}

	/**
	* this function fetch ownerData
	* @return owner data
	*/
	public function get_owner_details($userData) 
	{
		if(!empty($userData)) {
			foreach($userData as $key=>$user) {
				if($user['user']['parent_user_id'] > 0) {
					$ownerDetails = User::where('id',$user['user']['parent_user_id'])->first();
					if(!empty($ownerDetails)) {
						$userData[$key]['owner_name']  = $ownerDetails->name;
						$userData[$key]['owner_email']  = $ownerDetails->email;
					}
				}
			}
		}
		return $userData;
	}
	
}