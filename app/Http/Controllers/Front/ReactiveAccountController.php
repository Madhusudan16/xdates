<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Commons\AppMailer;
use App\Http\Requests;
use Illuminate\Routing\Controller;
use App\Models\Front\CancelAccount;
use Request;
use Response;
use Redirect;
use App\User;
use Session;
use DB;
use App\Models\Front\Invite;

class ReactiveAccountController extends Controller
{

	public function __construct()
    {
      
    }

   /**
    * this function active User Account
    *
    * @return true on success
    */
    public function activeAccount($token) 
    {
        //$token = Request::get('token');
        
        $accountRecord = CancelAccount::where(['token'=>$token,'status'=>0])->get()->first();
       
        if(isset($accountRecord) && $accountRecord->count() != 0) {
        	 
            $currentDate=date_create(date("Y-m-d"));
            $expiryDate=date_create(date("Y-m-d",strtotime($accountRecord->expired_date)));
            $diff=date_diff($currentDate,$expiryDate);
            $daysLeft = $diff->format("%R%a");
            
            if($daysLeft >= 0) {
            	$accountRecord->status = 1;
                $accountRecord->token = '';
                $accountRecord->save();
                $this->change_referral_user_status($accountRecord->referral_user_data,$accountRecord->owner_id);
                //$this->refund_balance($accountRecord->referral_balance_data);
                $this->reactiveAllChildUsers($accountRecord->owner_id,$accountRecord->child_user);
                //$data['is_show'] = 'reactive-account';
               // return redirect("login")->with('is_show','reactive-account');
                if(!Request::ajax()) {
                    Session::put('is_show','reactive-account');
                    return Redirect::to('login')->with(['is_show'=>'reactive-account']);
                } else {
                    
                    $this->send_mail($accountRecord->owner_id);
                    return Response::json(['msg'=>'account has been restored'],200);
                }
               // return view('auth.login', ['page_title'=>'Login','is_show'=>'reactive-account']);
            } else {
                if(!Request::ajax()) {
                     abort(404);
                }
            }
        } else {
            if(!Request::ajax()) {
                abort(404);
            }
        }
    }

    /**
    * This function active all user associate with owner ID
    *
    * @return true on success
    */
    public function reactiveAllChildUsers($owner_id,$userData)
    {
    	$userData = json_decode($userData,true);
    	//$allUserByOwner = User::where(['id'=>$owner_id])->orWhere('parent_user_id',$owner_id)->get();
        if(!empty($userData)) {
          	foreach($userData as $key => $user) {
            	$where = array('id'=>$user['id']);
            	$updateData = array('status'=>$user['status']);
            	User::where($where)->update($updateData);
            }
       	} 
       	if(isset($owner_id)) {
       		$where = array('id'=>$owner_id);
            $updateData = array('status'=>1);
            User::where($where)->update($updateData);
       	}
       	return true;
    }

    /**
    * change referral user status
    */
    public function change_referral_user_status($referral_data,$to_user_id)
    {
        if(!empty($referral_data)) {
            $referral_data = json_decode($referral_data,true);
            $where = array('from_user_id'=>$referral_data['from_user_id'],'to_user_id'=>$to_user_id);
            $update_data = array('status'=>$referral_data['status']);
            Invite::where($where)->update($update_data);
        }
    }

    /**
    * refund balance to referral user 
    */
    /*public function refund_balance($userBalanceData = null)
    {
        if($userBalanceData != null && !empty($userBalanceData)) {
            $balanceData = json_decode($userBalanceData,true);
            $dataToUpdate = array();
            foreach($balanceData as $bData) {
                $dataToUpdate['status'] = $bData['status'];
                DB::table('user_balance')->where('id', $bData['id'])->update($dataToUpdate);
            }
        }
    }*/

    /**
    * reactive account mail
    */
    public function send_mail($user_id) 
    {
        if(!empty($user_id)) {
            $where = array('id'=>$user_id);
            $userData = User::where($where)->get()->first();
            if(!empty($userData)) {
                $appMailer = new AppMailer;
                $appMailer->restore_cancel_account($userData);
            }
        }
    }
}