<?php 
namespace App\Http\Controllers\Cron;

use Illuminate\Routing\Controller;
use App\Models\Cron\SendNotification; 
use App\User;
use App\Commons\AppMailer;
use App\Models\Admin\Note;


class TrialExpire extends Controller
{
	public function trialExpireData()
	{
		$userData = array();
		/* 
		Here, daysNotification array store value of send notification whether after or before given days.
		And second array decided after or before send notification 
		Note : $beforeAfter array value must be 0 or 1 
		0 : before expire
		1 : after expired
		*/
		$daysNotification = array(9,4,0,1,5);
		$beforeAfter = array(0,0,0,1,1);  
		foreach($daysNotification as $key=>$frequency) {
			if($beforeAfter[$key] == 0) {
				$days = date('Y-m-d',strtotime("+$frequency days"));
			} else {
				$days = date('Y-m-d',strtotime("-$frequency days"));
			}
			$userData = User::whereDate('trial_end_date','=',$days)->where('status','<>',2)->where('user_type',1)->where('current_plan',0)->get();
			
			if($userData->count() != 0) {
				if($frequency == 1 && $beforeAfter[$key] == 1) {
					foreach($userData as $user) {
						$updateUser['is_expired'] = 1;
						$updateUser['account_exp'] = date('Y-m-d h:i:s');
						User::where('id',$user->id)->update($updateUser);
	            	}
	            }

				$userData = $userData->toArray();
				if($this->check_time($userData[0]['choosed_timezone'])) {
					$this->get_email_address($userData,$beforeAfter[$key],$frequency);
				}
			}
		}
	}
		
	/**
	* send notification  to user 
	*  $key variable decide type of notification 
	* @return true on success
	*/ 
	function sendMail($user_data,$is_expired,$sendNotificationData) 
	{
		$appMailer = new AppMailer;
		if($appMailer->trialExpireNotification($user_data,$is_expired)) {
			$today = date("Y-m-d H:i:s");
			$userTime = convertTimeToUSERzone($today,$user_data['choosed_timezone']);
			$updatedData = array('status'=>1,'send_date'=>date('Y-m-d'),'updated_at'=>$userTime);
			SendNotification::whereIn('id',$sendNotificationData)->update($updatedData); 
			$this->addNote($user_data,$is_expired);
			return true;
		} else {
			return false;
		}
	}

	public function get_email_address($userData,$is_expired,$days)
	{
		if(!empty($userData)) {
			foreach($userData as $user_key=>$user) {
				$whereData = array('user_id'=>$user['id'],'type'=>3,'status'=>1);
				$is_send = $this->send_before($whereData,$user['choosed_timezone']);
				if($is_send) {
					$saveData = array('user_id'=>$user['id'],'type'=>3,'status'=>0);
					$data = SendNotification::create($saveData);
					$sendNotificationData['id'] = $data['id'];
					$user['days'] = $days;
					$this->sendMail($user,$is_expired,$sendNotificationData);
				}
			}
		}
	}

	public function send_before($where,$timeZone)
	{
		$today = date("Y-m-d");
		$notification = SendNotification::where($where)->whereDate('created_at','=',$today)->get();
		if($notification->count() != 0) {
			return false;
		} else {
			return true;
		}
	}

	/**
	* this function create note in admin side 
	* @return true on created false else 
	*/
	public function addNote($userData,$is_expired)
	{
		if($is_expired == 1) {
			$userData['days'] = ($userData['days'] == 1) ? 0 : -1;
		} else {
			$userData['days'] = $userData['days']+1;
		}
		$noteData = array('remaining_trial_days'=>$userData['days'],'note_type'=>2,'user_id'=>$userData['id']);
		Note::create($noteData);
		return true;
	}
	/**
	* check user timezone and send email at 6am and send sms at 7am 
	* $timeZone parameter hold user timezone value
	* $by parameter decided whether send email or sms  
	*/
	public function check_time($timeZone) { 
		if(empty($timeZone)) {
			return true;
		}
		$userTime = date("Y-m-d H:i:s");
		$userTime = convertTimeToUSERzone($userTime,$timeZone);
		$set_time = date('Y-m-d 05:59:59');
		if(strtotime($userTime) > strtotime($set_time)) {
			return true;
		}
		return false;
	}
}
?>