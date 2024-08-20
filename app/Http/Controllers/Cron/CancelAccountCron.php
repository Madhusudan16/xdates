<?php 
namespace App\Http\Controllers\Cron;

use Illuminate\Routing\Controller;
use App\User;
use App\Models\Front\CancelAccount;

class CancelAccountCron extends Controller
{
	/**
	* get cancel account users list 
	*/
	public function get_users() 
	{
		$toDay = date('Y-m-d');
		$users = CancelAccount::whereDate('expired_date','<',$toDay)->where('status',0)->get();
		if($users->count() == 0) {
			return false;
		}
		$this->cancel_account($users);
	}

	/**
	* cancel account 
	*/
	public function cancel_account($cancelList) 
	{
		foreach($cancelList as $cancel) {

			$userData = User::where('id',$cancel->owner_id)->get()->first();
			$userData->status = 2 ;
			$userData->save();
			$cancel->status = 2;
			$cancel->token = null;
			$cancel->save();
		}
	}
}