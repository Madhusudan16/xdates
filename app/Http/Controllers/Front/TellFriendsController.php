<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Commons\UserAccess; 
use App\Http\Requests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use UrlShortener;
use URL;
use Crypt;
use App\Models\Front\Userrefererurl;
use App\Commons\AppMailer; 
use Illuminate\Support\Facades\Input;
use App\Models\Front\Invite;
use Validator;
use JsValidator;
use Response;
use DB;

class TellFriendsController extends Controller
{
	/**
     * The module id
     */
    protected $moduleId = 7;
	
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

    public function __construct(UserAccess $userAccess)
    {
        $this->middleware('auth'); 
		$this->user = Auth::guard($this->guard)->user(); 
		if(!empty($this->user)){ 
			$this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
			$this->vdata['user'] = $this->user;
			$this->vdata['curModAccess'] = $this->access['current'];
			$this->vdata['allModAccess'] = $this->access['all'];
            $this->vdata['short_url'] = $this->shortUrlExist($this->user->id);
            $this->vdata['inviteFriendStatus'] = array('Invited','In-Trial','Subscribed','Cancelled');
		    $this->vdata['page_section_class'] = 'top-padding-10 cart tell-friend';
            $this->vdata['page_title'] = 'Tell a friend';
        } 
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        if($page_url = prevent_user($this->user)) {
           return redirect($page_url);
        }
        $checkAccess = acoount_expire_text($this->user);
        $this->vdata['check_access'] = $checkAccess;
        if($checkAccess  && isset($checkAccess) &&  $checkAccess['allow_access'] == 1) {
            return redirect()->to('/');
        }
        if($this->vdata['curModAccess']['view'] != 1){
           return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        if(!check_user($this->user->parent_user_id)) {
            Auth::logout();
        }
        $rules = array(
            'friendEmail'=>'required',
            'message'    =>'required',
            );
        $msg = array(
            'friendEmail:required' => 'Email Field is required',
            'message:required'     => 'Message field is required'
            );

        $validator = JsValidator::make($rules,$msg,[],'#mailToFriend');
        $this->vdata['validator'] = $validator;
        $this->vdata['userInvitedFriends'] = $this->getInviteFriends();
        
        if(count($this->vdata['userInvitedFriends']) != 0) {
            $totalTrialCredit = $this->get_total_credit();
            $totalAmountCredit = $this->get_total_credit(2);
            $this->vdata['total_credit_days'] = round($totalTrialCredit,2);
            $this->vdata['totalAmountCredit'] = round($totalAmountCredit,2);

            $this->vdata['remaining_trial_days'] = $this->find_remaining($totalTrialCredit);
            $this->vdata['remaining_balance'] = $this->find_remaining($totalAmountCredit,2);
        }
        return view('front.tell-friends',$this->vdata);
    }
    /**
    * Generate short url 
    *
    * @return shorter generated url 
    */
    public function createShortUrl($url)
    {
        $shortUrl = UrlShortener::shorten($url); // generate shorter url
        echo $shortUrl = UrlShortener::driver('bitly')->shorten($url);
        return $shortUrl;
    }
    /**
    * this function check short url exist or not if not then create it.
    *
    * @return shorter url
    */
    
    public function shortUrlExist($userId){

        $data = Userrefererurl::where('user_id',$userId)->get();
       
        if($data->count() == 0){
            $userreferObj = new Userrefererurl;
            $encryptedUserId = Crypt::encrypt($this->user->id);
            $registerUrl = URL::to("/register?user_ref=$encryptedUserId");
            $shorterUrl  = UrlShortener::driver('bitly')->shorten($registerUrl); 
            $userreferObj->user_id = $userId;
            $userreferObj->short_url = $shorterUrl;
            $userreferObj->save();
            return $shorterUrl;
        }   else {
                foreach($data  as $value){
                    $short_url = $value->short_url;
                }
                    return $short_url;
            }
    }
    /**
    * send mail to friends 
    *
    * 
    */
    
    public function inviteFriends(AppMailer $mailer)
    {
        if(isset($this->vdata['curModAccess']['invite']) && $this->vdata['curModAccess']['invite'] == 1){
            $email = Input::get('friendEmail');
            $receipts =  explode(',',$email);
            $msg = Input::get('message');
           /* if(!empty($msg)) {
                $msg = nl2br($msg);
            }*/
            $user_name = $this->user->name;
            $mailer->mailToFriend($receipts,$msg,$user_name);
            $this->vdata['emails'] = $receipts;
            $this->addInviteFriend($receipts);
            $this->vdata['userInvitedFriends'] = $this->getInviteFriends();
            return view('front.tell-friends',$this->vdata);
        } else {
            return redirect('/tell-friends');
        }
    }
    /**
    * Insert Invite friend record 
    *
    * @return true on success
    */

    public function addInviteFriend($data)
    {

        if(!empty($data)){
            foreach($data as $email){

                $where = array('from_user_id'=>$this->user->id,'friend_email'=>$email);
                $getInviteFriendsData = Invite::where($where)->get();
                if($getInviteFriendsData->count() == 0){
                    $insertData = array('from_user_id'=>$this->user->id,'friend_email'=>$email);
                    if($this->user->parent_user_id !=0) {
                        $insertData['owner_id'] = $this->user->parent_user_id;
                    }
                    Invite::create($insertData);
                }
            }
            
        }
    }
    /**
    * Get Invite friend record by ID 
    *
    * @return all record by id 
    */

    public function getInviteFriends($status = null)
    {
        $ownerId = ($this->user->parent_user_id != 0) ? $this->user->parent_user_id :$this->user->id;
        if($status == null ) {
            $where = array('from_user_id'=>$ownerId);
            $orWhere = array('owner_id'=>$ownerId);
        } else {
            $where = array('status'=>$status,'from_user_id'=>$ownerId);
            $orWhere = array('status'=>$status,'owner_id'=>$ownerId);
        }

        $userFriendData = Invite::where($where)->orWhere($orWhere)->get()->toArray();

        $userFriendData = $this->set_data($userFriendData);
        return $userFriendData;
    }

    /**
    * this function set data
    */
    public function set_data($userData)
    {
        if(!empty($userData) ) {
            foreach($userData as $key=>$invite_data ) {
                if($invite_data['status'] == 1) {
                    $trial_days = $invite_data['trial_days'];
                    $date=strtotime(date('Y-m-d',strtotime($invite_data['invitation_accept_date'])));
                    $trialEndDatedate = date('Y-m-d',strtotime("+$trial_days days",$date));
                    $trial_end = date_create($trialEndDatedate);
                    $invitation_accept = date_create();
                   // preF($invitation_accept);
                    $date_diff=date_diff($trial_end,$invitation_accept);
                    
                    if($date_diff->invert != 0) {
                        $remaining_days = $date_diff->days;
                    } else {
                        $remaining_days = 0;
                    }
                    $userData[$key]['left_days'] =$remaining_days;
                } else if($invite_data['status'] == 2) {
                    if(!empty($invite_data['subscribe_date'])) {
                        $currentDate = date_create();
                        $subscribedDate = date_create($invite_data['subscribe_date']);
                        $date_diff=date_diff($currentDate,$subscribedDate);
                        //preF($date_diff);
                        $span_days = $date_diff->days;
                        $daysInMonth = 30;
                        if($date_diff->invert != 0 && $span_days < $daysInMonth) {
                            if($span_days != 0) {
                                $perDaysCharge = $invite_data['amount']/$daysInMonth;
                                $amount_left = round((($daysInMonth-$span_days) * $perDaysCharge),2);
                            } else {
                                $amount_left =  $invite_data['amount'];
                            } 
                        } else {
                            $amount_left = 0;
                        }

                        $userData[$key]['amount_left'] =$amount_left;
                    }
                }
            }
        }
        return $userData;
    }

    /**
    * this function count total credit
    */
    public function get_total_credit($status = 1)
    {
        $ownerId = ($this->user->parent_user_id != 0) ? $this->user->parent_user_id :$this->user->id;
        $where = array('status'=>$status);
        if($status == 2) {
            $date = DB::table('invites')->where($where)->where(function ($query) use($ownerId) {
                $query->where('from_user_id',$ownerId)->orWhere('owner_id',$ownerId); })->first();
            
            $balanceWhere = array('type'=>1,'owner_id'=>$ownerId);
            if(isset($date->subscribe_date) && !empty($date->subscribe_date)) {
                $total = DB::table('user_balance')->whereDate('created_at','>=',$date->subscribe_date)->where($balanceWhere)->where('balance_from_user_id','<>',0)->sum('amount');
            } else {
                $total = 0;
            }
            //preF($total);
            //$total = Invite::where($where)->get()->sum('amount');
        } else {
            /*$total = Invite::where('status',1)->where('owner_id',$ownerId)->orWhere('from_user_id',$ownerId)->get();*/
            /*$total = Invite::where('status',1)->where('from_user_id',$ownerId)->get()->sum('trial_days');*/

           $total =  DB::table('invites')->where('status','<>',0)->where(function ($query) use($ownerId) {
                $query->where('from_user_id',$ownerId)->orWhere('owner_id',$ownerId); })->sum('trial_days');
        }

        return $total; 
    }

    /**
    * this function find remaining trial days 
    */
    public function find_remaining($totalCredit,$status = 1)
    {

        $ownerId = ($this->user->parent_user_id != 0) ? $this->user->parent_user_id :$this->user->id;
        $where = array('status'=>$status);
        if($status == 2) {
            $balanceWhere = array('type'=>2,'owner_id'=>$ownerId);
            $date = Invite::where($where)->where('owner_id',$ownerId)->Orwhere('from_user_id',$ownerId)->get()->min('subscribe_date');
            $debitData = DB::table('user_balance')->whereDate('created_at','>=',$date)->where($balanceWhere)->sum('amount');
            
        } else {
            if($this->user->current_plan == 0) {
                /*$date = Invite::where('status','<>',0)->where('from_user_id',$ownerId)->orWhere('owner_id',$ownerId)->get()->min('invitation_accept_date');*/
                $inviteData =  DB::table('invites')->where('status','<>',0)->where(function ($query) use($ownerId) {
                $query->where('from_user_id',$ownerId)->orWhere('owner_id',$ownerId); })->first();
                
                if(isset($inviteData->invitation_accept_date) && !empty($inviteData->invitation_accept_date)) {
                    $date = $inviteData->invitation_accept_date;
                } else {
                    return 0;
                }
                $debitData = $this->find_days_diff($date);
            } else {
                $inviteData =  DB::table('invites')->where('status',2)->where(function ($query) use($ownerId) {
                $query->where('from_user_id',$ownerId)->orWhere('owner_id',$ownerId); })->first();
                return 0;
            }
            
        }
        
        if($debitData >= 0) {
            $total = $totalCredit - $debitData;
            
            if($total > 0 ) {
                return $total;
            } else {
                return 0;
            }
        } else {
            return 0;
        }

    }

    /**
    * this function find days different 
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
        
        if($date_diff->invert != 0) {
            if($date_diff->days == 0) {
                return 0;
            }
            return $date_diff->days ;
        } else {
            return false;
        }
    }
}
