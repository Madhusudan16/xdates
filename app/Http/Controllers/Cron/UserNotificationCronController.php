<?php 
namespace App\Http\Controllers\Cron;
use App\Models\Front\Xdate;
use Illuminate\Routing\Controller;
use App\Models\Cron\SendNotification; 
use App\User;
use App\Commons\AppMailer;
use App\Models\Front\Notification;
use App\Models\Front\NotificationFrequency;
use Twilio\Rest\Client;



class UserNotificationCronController extends Controller
{
	
	/**
	* this variable save send mail before x-dates
	*/ 
	public $temp_before = null; 
	/**
	* get xdate for given day 
	*  
	*/

	public function sendXdateFollowUp()
	{
		$daysNotification = $this->getFrequency();
		
		$xdateData = array();
		$notificationBy = array('email'=>1,'mobile'=>2);
		foreach($notificationBy as $by) { 
			foreach($daysNotification as $key=>$frequency) {
				if($frequency->frequency_keys != 0)  {
					if($by == 1) {
						$follow_up_field = "noti_email_followup_frequency";
						$xdate_field = "noti_email_frequency";
					} else {
						$follow_up_field = "noti_mob_followup_frequency";
						$xdate_field = "noti_mob_frequency";
					}
					
					$dateAfter = date('Y-m-d', strtotime("+$frequency->frequency days"));
					if($frequency->type == 2) {  //

						$xdateData = Xdate::where('follow_up_date',$dateAfter);
						$where = array($follow_up_field=>$frequency->frequency_keys);
					} else {
						$xdateData = Xdate::where('xdate',$dateAfter);
						$where = array($xdate_field=>$frequency->frequency_keys);
					}
					$xdateData->join('users', 'users.id', '=', 'xdates.producer');
					$xdateData = $xdateData->where($where);
					$xdateData = $xdateData->where('xdates.status','<>',2)->where('users.status','<>',2)->get(['xdates.*','users.email as user_email','noti_email_followup_frequency','name as user_name','users.noti_email_frequency','users.noti_mob_followup_frequency','users.name','users.choosed_timezone'])->toArray();
					$filterData = $this->filterData($xdateData);
					$this->temp_before = $frequency->frequency;
					if(count($filterData) != 0) {
						foreach($filterData[1] as $user_key=>$subfilterData) {
							$filter = $filterData[0];
							$this->get_email_or_number_address($filter[$subfilterData],$frequency->type,$by);
						}
					}
				}
			}
		}
	}
		
		/**
		* send notification  to user 
		*  $key variable decide type of notification 
		* @return true on success
		*/ 
		function sendMail($addressList,$user_data,$frequencyType,$sendNotificationData) 
		{

			$appMailer = new AppMailer;
			$allEmail = array();
			$allEmail[] = $sendNotificationData['user_email'];
			foreach ($addressList as  $list) {
				$allEmail[] = $list['obj_value'];
			}
			if($appMailer->sendNotification($allEmail,$user_data,$frequencyType,$this->temp_before)) {
				$updatedData = array('status'=>1,'send_date'=>date('Y-m-d'));
				SendNotification::whereIn('id',$sendNotificationData['id'])->update($updatedData); 
				return true;
			} else {
				return false;
			}
		}

		public function get_email_or_number_address($userData,$frequencyType,$by = 1)
		{

			/*preF($userData);*/
			if(!empty($userData)) {
				$is_send = false;
				foreach($userData as $user_key=>$user) {
					if($this->check_time($user['choosed_timezone'],$by)) { 
						$notificationData = Notification::where(['user_id'=>$user['producer'],'is_active'=>1,'obj_type'=>$by])->where('status',1)->get(['obj_value','country_code'])->toArray();
						$whereData = array('user_id'=>$user['producer'],'type'=>$frequencyType,'status'=>1,'send_by'=>$by);
						$is_send = $this->send_before($whereData);
						if($is_send) {
							$userDetails[] = $user;
							if($by == 1) {
								$sendNotificationData['user_email'] = $user['user_email'];
							}
							$saveData = array('user_id'=>$user['producer'],'type'=>$frequencyType,'event_id'=>$user['id'],'status'=>0,'send_by'=>$by);
							$data = SendNotification::create($saveData);
							$notification['id-'.$user_key] = $data['id'];
						}
					}
				}
				if($is_send) {
					$sendNotificationData['id'] = $notification;
					if($by == 1) {
						$this->sendMail($notificationData,$userDetails,$frequencyType,$sendNotificationData);
					} else {
						$this->send_sms($notificationData,$userDetails,$frequencyType,$sendNotificationData);
					}
				}
			}
		}

		public function send_before($where)
		{
			$today = date('Y-m-d');
			$notification = SendNotification::where($where)->whereDate('send_date','=',$today)->get();
			
			if($notification->count() != 0) {
				return false;
			} else {
				return true;
			}
		}

		/**
		* this function filter xdate data by user_id
		*
		* @return filter Data
		*/

		public function filterData($xdateData) 
		{
			if(!empty($xdateData)) {
				$newXdate = array();
				foreach($xdateData as $x_key=>$xdate) {
					if(array_key_exists($xdate['producer'], $newXdate)) {
						$newXdate[$xdate['producer']][] = $xdate;
					} else {
						$userId[] = $xdate['producer'];
						$newXdate[$xdate['producer']][] = $xdate;
					}
				}
				$newArray[] = $newXdate;
				$newArray[] = $userId;
				return $newArray;
			}
		}

		public function getFrequency()
		{
			$frequencyData = NotificationFrequency::get();
			return $frequencyData;
		}

		/**
		* send notification  to user via sms
		*  $key variable decide type of notification 
		* @return true on success
		*/ 
		function send_sms($mobileNumberList,$user_data,$frequencyType,$sendNotificationData) 
		{
			$sid = env('SMS_API_ACCOUNT_ID');
			$token = env('SMS_API_TOKEN');
			$client = new Client($sid, $token);
			$appMailer = new AppMailer;
			$allMobile = array();
			foreach ($mobileNumberList as  $list) {
				$allMobile[] = $list['country_code']."".$list['obj_value'];
			}

			$from =  env('SMS_API_FROM');

	        foreach($user_data as $xdate) {
                $text_msg = xdate_notification_msg($xdate,$frequencyType);
            	foreach($allMobile as $phone_number) {
    	    		$data = $client->messages->create($phone_number,array('from'=>$from ,'body' =>"$text_msg"));
    	    	}
    	    }
    	    if(isset($data) && !empty($data)){
				$updatedData = array('status'=>1,'send_date'=>date('Y-m-d'));
				SendNotification::whereIn('id',$sendNotificationData['id'])->update($updatedData); 
				return true; 
			} else {
				return false;
			}
		} 

		/**
		* check user timezone and send email at 6am and send sms at 7am 
		* $timeZone parameter hold user timezone value
		* $by parameter decided whether send email or sms  
		*/
		public function check_time($timeZone,$by = 1) {
			if(empty($timeZone)) {
				return true;
			}
			$userTime = date("Y-m-d H:i:s");
			$userTime = convertTimeToUSERzone($userTime,$timeZone);
			if($by == 1) {
				$set_time = date('Y-m-d 05:59:59');
			} else {
				$set_time = date('Y-m-d 06:59:59');
			}
			if(strtotime($userTime) > strtotime($set_time)) {
				return true;
			}
			return false;
		}
}
?>