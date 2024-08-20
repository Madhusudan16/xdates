<?php

namespace App\Commons;

use App\User;
use Mail;

class AppMailer
{

    /**
     * The Laravel Mailer instance.
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * The sender of the email.
     *
     * @var string
     */
    protected $from = 'no-reply@xdates.net';
	
	/**
     * The app name for the email.
     *
     * @var string
     */
    protected $fromName = 'X-Dates';

    /**
     * The recipient of the email.
     *
     * @var string
     */
    protected $to;
	
	/**
     * The subject of the email.
     *
     * @var string
     */
    protected $subject;

    /**
     * The view for the email.
     *
     * @var string
     */
    protected $view;

    /**
     * The data associated with the view for the email.
     *
     * @var array
     */
    protected $data = [];

    /**
    * The variable $headers set header of mail  
    *
    * @var array
    */

    protected $headers = [];

    /**
     * Create a new app mailer instance.
     *
     * @param Mailer $mailer
     */
 

    public function __construct()
    {
      //$this->view = $param1;
      //$this->subject = $param2;
      //$this->data = $param3;
    }

    /**
     * Deliver the email confirmation.
     *
     * @param  User $user
     * @return void
     */
    public function sendEmailConfirmationTo(User $user)
    {
        $this->to = $user->email;
        $this->toName = $user->name;
        $this->view = 'emails.confirm';
        $this->data = compact('user');
		$this->subject = 'X-Dates: signup confirmation';
        $this->deliver();
    }
   
    public function sendEmailRestoreTo($user, $policy = null )
    {
        if($policy == null ) {
            $policy = $user;
        }
        $this->to = $user->email;
        $this->toName = isset($user->name) ? $user->name : $user->owner_name;
        $this->view = 'emails.policy';
        $this->data = compact('policy','user');
        $this->subject = 'X-Dates: deletion notification';
        $this->deliver();
    }
    public function sendUserPasswordByOwner($user){
        if(!empty($user)){
            $this->to = $user->email;
            $this->toName = $user->name;
            $this->view = 'emails.registerByOwner';
            $this->data = compact('user');
            $this->subject = 'X-Dates: welcome | login credentials';
            $this->deliver();
        }
    }
	
	public function sendAdminUserPasswordByOwner($user){
        if(!empty($user)){
            $this->to = $user->email;
            $this->toName = $user->name;
            $this->view = 'emails.adminRegisterByOwner';
            $this->data = compact('user');
            $this->subject = 'X-Dates: welcome | login credentials';
            $this->deliver();
        }
    }

    /**
     * Deliver the email.
     *
     * @return void
     */
    public function deliver()
    { 
            if(isset($this->headers) && !empty($this->headers)) {
    		    Mail::send($this->view, $this->data, function ($m){
                    $m->from($this->from, $this->fromName);
                    $m->subject($this->subject);
                    $m->bcc($this->headers);
                }); 
            } else {
                Mail::send($this->view, $this->data, function ($m){
                    $m->from($this->from, $this->fromName);
                    $m->to($this->to, $this->toName)->subject($this->subject);
                });
            }
    }
    /**
    * send user password when user register with google 
    *
    * @return true if success 
    */
    public function sendUserPassword($user){
        if(!empty($user)){
            $this->to = $user->email;
            $this->toName = $user->name;
            $this->view = 'emails.registerWithGoogle';
            $this->data = compact('user');
            $this->subject = 'X-Dates: login credentials';
            $this->deliver();
        }
    }
    /**
    * refer friend 
    *
    * @return true if success 
    */
    public function mailToFriend($to,$msg,$requester_name)
    {
        if(!empty($to)) {
            $this->toName = "";
            $this->headers = $to;
            $this->view = 'emails.inviteFriend';
            $this->data = compact('msg','requester_name');
            $this->subject = "X-Dates: invitation from $requester_name";
            $this->deliver();
        }
    }
    /**
    * send verification mail to user 
    * 
    * @return true on success 
    */
    public function sendVerificationMail($notificationData)
    {
        if(!empty($notificationData)) {
            $token = $notificationData['token'];
            $this->to = $notificationData['obj_value'];
            $this->toName = $notificationData['user_name'];
            $this->view = 'emails.mailVerification';
            $this->data = compact('token');
            $this->subject = 'X-Dates: confirm your email address';
            $this->deliver();
        }
    }

    /**
    * send xdate end soon notification 
    *
    * @return true on success
    */
    public function sendNotification($addressList,$xdateData,$frequencyType,$temp_before)
    { 
        if($frequencyType == 2) {
            $days = array(0=>'Daily',7=>'Weekly');
            $temp_days = $days[$temp_before];
        }
        if(!empty($addressList) && !empty($xdateData)) {
            $this->to = $addressList;
            $this->toName = "";
            $this->view = 'emails.xdate-notification';
            $this->data = compact('xdateData','frequencyType','temp_days');
            if($frequencyType == 1) {
                $this->subject = 'X-Dates notification';
            } else {
                 $this->subject = 'X-Dates: follow-up notification';
            }
            $this->deliver();
            return true;
        }
    }

    /**
    * cancel Account restore mail
    * 
    * @return true on success 
    */
    public function cancelAccount($userData)
    {
        if(!empty($userData)) {
            $this->to = $userData['email'];
            $this->toName = $userData['owner_name'];
            $this->view = 'emails.cancel-account';
            $this->data = compact('userData');
            $this->subject = 'X-Dates: cancel account notification';
            $this->deliver();
        }
    }
    /**
    * trial extend mail  
    * 
    * @return true on success 
    */
    public function trialExtendVerificationMail($data)
    {
        if(!empty($data)) {
            $this->to =$data['email'];
            $this->toName = '';
            $this->view = 'emails.trialConfirm';
            $this->data = compact('data');
            $this->subject = 'X-Dates: free trial extension request';
            $this->deliver();
        }
    }
    /**
    * Inform to client for trial extendtion approved by admin 
    * 
    * @return true on success 
    */
    public function informClientAboutTrial($email)
    {
            $this->to = $email;
            $this->toName = '';
            $this->view = 'emails.infoUsertrial';
            $this->subject = 'X-Dates: 30-day trial extension';
            $this->deliver();
    }

    /**
    * trial expired  notification 
    *
    * @return true on success
    */
    public function trialExpireNotification($userData,$is_expire)
    { 

        if(!empty($userData)) {
            $this->to = $userData['email'];
            $this->toName = $userData['name'];
            $this->view = 'emails.trial-expire-notification';
          
            if($is_expire != 1){
                if($userData['days'] == 0) {

                    $temp_text = "day";
                } else {
                    $temp_text = "days";
                }
                $userData['days']++;
                $userData['bill_days'] = 30 + $userData['days'];
                $subject = "X-Dates: $userData[days] $temp_text left in your free trial";
            } else if($is_expire == 1 &&  $userData['days'] == 1) {
                $subject = "X-Dates: your free trial EXPIRED";
            } else {
                $subject = "X-Dates: account expiration follow-up";
            }
            
            $this->data = compact('userData','is_expire');
            $this->subject = $subject;
            $this->deliver();
            return true;
        }
    }

    /**
    * this function send mail about payment
    */
    public function payment_mail($planData,$user,$is_success,$card_charge)
    {
        if(!empty($planData) && !empty($user)) {
            $this->to = $user->email;
            $this->toName = $user->name;
            
            if($is_success == 1) {
                $pay_amount = $planData->plan_pay_amount-$planData->discount_amount;
            }
            $this->view = 'emails.payment_email';
            $this->data = compact('user','planData','is_success','card_charge','pay_amount');
            $this->subject = 'X-Dates: payment notification';
            $this->deliver();
            return true;
        } else {
            return false;
        }
    }

    /**
    * this function send mail about invoice
    */
    public function invoice_mail($user,$invoice_no)
    {   
        if(!empty($invoice_no) && !empty($user)) {
            $this->to = $user->email;
            $this->toName = $user->name;
            $this->view = 'emails.invoice_email';
            $this->data = compact('user','invoice_no');
            $this->subject = 'X-Dates: invoice notification';
            $this->deliver();
            return true;
        } else {
            return false;
        }
    }

    /**
    * this functionn send mail when X-Dates owner restore front end user account
    */
    public function restore_cancel_account($userData)
    {
        if(!empty($userData) ) {
            $this->to = $userData->email;
            $this->toName = $userData->name;
            $this->view = 'emails.restore_account';
            $this->data = compact('userData');
            $this->subject = 'X-Dates: account restore notification';
            $this->deliver();
            return true;
        } else {
            return false;
        }
    }

    /**
    * this functionn send mail to Xdate company owner about most referral of the month or year 
    */
    public function most_referral_notification($email ,$userData,$mailData)
    {
        if(!empty($userData) && !empty($email) ) {
            $this->to = $email;
            $this->toName = '';
            $this->view = 'emails.most_referral_mail';
            $this->data = compact('userData','mailData');
            $this->subject = 'X-Dates: most referral notification';
            $this->deliver();
            return true;
        } else {
            return false;
        }
    }

    /*
    * this function send mail to owner about user feedback
    */
    public function user_feedback($email,$mail_content)
    {
        if(!empty($email) && !empty($mail_content)) {
            $this->to = $email;
            $this->toName = '';
            $this->view = 'emails.feedback';
            $this->data = compact('mail_content');
            $this->subject = "X-Dates: user feedback (#$mail_content[number_of_feedback])";
            $this->deliver();
            return true;
        } else {
            return false;
        }
    }

    /**
    * send notification to xdates owner and admin account about trial extend pending request
    */
    public function trial_extend_notification($emails ,$user)
    {
        if(!empty($user) ) {
            $this->to = $emails;
            $this->toName = '';
            $this->view = 'emails.trial_extend_noti';
            $this->data = compact('user');
            $this->subject = 'X-Dates: trial extend notification';
            $this->deliver();
            return true;
        } else {
            return false;
        }
    }

    /**
    * this function send mail to user which asssigned xdate
    *  @return true on success
    */
    public function xdate_assign_mail($userData,$xname) 
    {
        if(!empty($userData) ) {
            $this->to = $userData->email;
            $this->toName = $userData->name;
            $this->view = 'emails.xdate_assign_mail';
            $this->data = compact('userData','xname');
            $this->subject = 'X-Dates: x-date assigned to you';
            $this->deliver();
            return true;
        } else {
            return false;
        }
    }

    /**
    * this function send mail to producer which assign for current x_dates
    * @return true on success
    */
    public function request_update_mail($data) {
        $this->to = $data['userData']->email;
        $this->toName = $data['userData']->name;
        $this->view = 'emails.x_date_request_update';
        $this->data = compact('data');
        $this->subject = 'X-Dates: record update request';
        $this->deliver();
        return true;
    }
}
