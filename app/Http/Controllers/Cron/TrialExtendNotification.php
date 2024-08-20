<?php 
namespace App\Http\Controllers\Cron;

use Illuminate\Routing\Controller;
use App\Models\Cron\SendNotification; 
use App\User;
use App\Commons\AppMailer;
use App\Models\Admin\Admin;
use App\Models\Admin\ExtendTrial;
use DB;

class TrialExtendNotification extends Controller
{
	/**
    * get trial extend list 
    */
    public function get_trial_extend_request_data()
    {
        $where = array('is_approved'=>0);
        $trial_list = ExtendTrial::with('get_user')->where($where)->get();
        $this->sendMail($trial_list);
    }
		
	/**
	* send notification  to user 
	*  $key variable decide type of notification 
	* @return true on success
	*/ 
	function sendMail($trial_list) 
	{
		if($trial_list->count() == 0) {
			return false;
		}
		$mailTo = array(1,2,3);
        $adminData = Admin::whereIn('user_type',$mailTo)->where('status',1)->get(['email']);
        $adminEmails = array();
        foreach($adminData as $admin) {
            $adminEmails[] = $admin->email;
        }
		$appMailer = new AppMailer;
		foreach($trial_list as $list) {
			if($this->send_before($list['get_user']->id)) {
				if($appMailer->trial_extend_notification($adminEmails,$list)) {
					$insert = array('user_id'=>$list['get_user']->id,'is_send'=>1);
				} else {
					$insert = array('user_id'=>$list['get_user']->id,'is_send'=>0);
				}
				DB::table('trial_extend_log')->insert($insert);
			}
		}
	}
	
	public function send_before($user_id)
	{
		$today = date('Y-m-d');
		$where = array('user_id'=>$user_id,'is_send'=>1);
		$notification = DB::table('trial_extend_log')->where($where)->whereDate('created_at','=',$today)->get();
		if(count($notification) != 0) {
			return false;
		} else {
			return true;
		}
	}
}
?>