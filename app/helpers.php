<?php  
	 function activeLink($path){
		return Request::is($path . '*') ? ' active' :  '';
	 }
	 function dropdownOpenClass($path){
		return Request::is($path . '*') ? ' open' :  '';
	 }
	 /* print formated data
	  * @Data pass data as array or variable
	  * @return echo with pre and exit 
	  */
	function preF($Data,$exit=true){
		echo "<pre>";
		print_r($Data);
		echo "</pre>";
		if($exit){
			exit;
		}
	}

	function get_note_text($noteData)
	{
        if($noteData->note_type == 2 ) {
            $trialText = array(-1=>"Your trial expired email (what happened?!) sent.",0=>"Your trial expired email (day of) sent.",1=>'Trial expiration email ('.$noteData->remaining_trial_days.' days left) sent.');
            if($noteData->remaining_trial_days > 0) {
                $key = 1;
            }  else {
                $key = $noteData->remaining_trial_days;
            }
            $defaultNote = $trialText[$key];
            return $defaultNote;
        } 

        $type = isset($noteData['get_actioner_user']->user_type) ? $noteData['get_actioner_user']->user_type : $noteData['get_user']->user_type;
        $note_type = (empty($noteData->detail) && empty($noteData->remaining_trial_days)) ? 3 : $noteData->detail;
        $is_approve = $noteData->is_approved;
        $name = $noteData['get_user']->name;
       if($noteData['get_user']->user_type == 4  && $note_type == 0 && $is_approve == 0) {
            $defaultNote = "<strong>$name</strong> requested a free trial extension of 30 days with the following note: $noteData->detail";
        } else if(($noteData['get_user']->user_type == 2 || $noteData['get_user']->user_type == 1 || $noteData['get_user']->user_type == 3) &&  $is_approve == 1 && $note_type != 3) {
            $is_admin =  true;
            $defaultNote = "<strong>$name</strong> authorized a free trial extension of 30 days with the following note: $noteData->detail";
        }  else if(isset($noteData['get_actioner_user']->name) && $note_type == 3 && $is_approve == 1) {
            $name = $noteData['get_actioner_user']->name;
            $requester_name = $noteData['get_user']->name;
            $defaultNote = "<strong>$name</strong> approved the trial extension request submitted by <strong>$requester_name</strong>.";
        } else {
            $defaultNote = "<strong>$name</strong> authorized a free trial extension of 30 days.";
        }
        return $defaultNote;
	}

	function get_log_text($log_type,$data,$name = null) 
	{ 
       // Custom field" field deleted, To restore click here
		if($log_type == 1) {
			$log_note = "Custom field ".$data . " deleted, To restore mail  click";
		} else if($log_type == 2) {
			$log_note = "Custom field ".$data." restored by ".$name .".";
		} 
		return $log_note;
       
	} 

	
	//this function convert string to UTC time zone
	function convertTimeToUTCzone($str, $userTimezone='', $format = 'Y-m-d H:i:s'){
	    if($userTimezone != ''){ 
	    	$new_str = new DateTime($str, new DateTimeZone(  $userTimezone  ) );
		}else{
			$new_str = new DateTime($str);
		}
	    $new_str->setTimeZone(new DateTimeZone('UTC'));
	    return $new_str->format( $format);
	} 
	
	//this function converts string from UTC time zone to current user timezone
	function convertTimeToUSERzone($str, $userTimezone='', $format = 'Y-m-d H:i:s'){
	    if(empty($str)){
            return '';
	    }
	     
	    $new_str = new DateTime($str, new DateTimeZone('UTC') );
		if($userTimezone != ''){
			$new_str->setTimeZone(new DateTimeZone( $userTimezone ));
		}
	    
	    return $new_str->format( $format);
	} 
	
	/**
    * this function return  days different
    */
    function find_days_diff($end_date , $start_date = null) 
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
    * this function set note text whee note type 1
    */
    function default_xdate_note_text($xdate_name , $note_data)
    {
        if(!empty($note_data)) {
            $default_text = "$xdate_name reassigned from Producer \"$note_data[old_producer]\" to Producer \"$note_data[new_producer]\" by User \"$note_data[user]\".";
            return $default_text;
        } else {
            return false;
        }
    }


    /**
    * this function return tell friend message 
    */
    function tell_friend_default_text($sort_url) 
    {
    	if(!empty($sort_url)) {
    		$text['heading'] = "Hey,"; 
    		$text['center_content'] = "I've been using X-Dates to manage my insurance leads. It beats the typical spreadsheet and is very inexpensive. If you use this link, you'll get 1 FREE month to try-it-out: $sort_url";
    		$text['end_text'] = "Be well,";
    		return ($text);
    	} else {
    		return false;
    	}
    }

    /**
    * mobile verication code text
    */
    function set_sms($code)
    {
    	if(!empty($code)) {
    		$text  ="Your X-Dates mobile verification code is: $code ";
    		return $text;
    	} else {
    		return false;
    	}
    }

    /**
    * set xdate notification message
    */
    function xdate_notification_msg($xDateDetails,$type)
    {
        if($type == 2 ){
            $message = "Hello $xDateDetails[name],\n\nYou need to followup $xDateDetails[xname] on $xDateDetails[follow_up_date].";

        } else {
            $message = "Hello $xDateDetails[name],\n\nRenewal reminder for the $xDateDetails[xname]  on $xDateDetails[xdate].";
        }
        return $message;
    }

    /**
    * owner account is expired or not 
    */
    function check_user($parent_user_id)
    {
        return true;
       /* if($parent_user_id == 0) {
            return true;
        }
        $where = array('id'=>$parent_user_id,'is_expired'=>0,'status'=>1);
        $userData = App\User::where($where)->where($where)->get()->first();
        if(empty($userData)) {
            return false;
        } else {
            return true;
        }*/
    }

    /**
    * update note  message on note
    */
    function update_note_text($user_data) 
    {
        $note_text = "";
        if(empty($user_data)) {
            return false;
        }

        $note_text = "Requested \"$user_data[producer_id]\" provide an updated note on this X-Date.";
        return $note_text;
    }

    /**
    * trial decline note
    */
    function trial_decline_text($trialData) 
    {
        $action_by = $trialData['get_actioner_user']->name;
        $requester_name = $trialData['get_user']->name;
        $note_text =  "<strong>$action_by</strong> denied the trial extension request submitted by <strong>$requester_name</strong> with the following note: $trialData->detail.";
        return $note_text;
    }

    /**
    * this function set text for expired  account
    */
    function acoount_expire_text($userData)
    {
        $popUpData =  array();
        $popUpData['signup_btn'] = 0;
        $popUpData['logout_btn'] = 0;
        $popUpData['logout_text'] = "Logout";
        $popUpData['signup_text'] = "Sign Up";
        $popUpData['allow_access'] = 0;
        $popUpData['show_pop_up'] = 1; 
        $current_plan = $userData->current_plan;
        $account_suspended = $userData->account_suspended;
        $is_expired = $userData->is_expired;
        $user_status = $userData->status;
        if($userData->parent_user_id > 0) {
            $where = array('id'=>$userData->parent_user_id);
            $ownerData = App\User::where($where)->where($where)->get()->first();
            $is_expired = $ownerData->is_expired;
            $account_suspended = $ownerData->account_suspended;
            $current_plan = $ownerData->current_plan;
            $user_status = $ownerData->status;
        }
        if($is_expired == 0) {
            return false;
        }  
        if($user_status != 1) {
            Auth::logout();
        }
        if($current_plan == 0 ) {
            if($userData->user_type != 1) {
                $popUpData['logout_btn'] = 1;
                $popUpData['title'] = "Free trial expired";
                $popUpData['content'] = "Your free trial has expired. If you found value in this service, please encourage the account owner, $ownerData->name, to sign-up for the service.";
                $popUpData['allow_access'] = 1;
                $popUpData['show_pop_up'] = 0; 
            } else {
                $popUpData['signup_btn'] = 1;
                $popUpData['logout_btn'] = 1;
                $popUpData['title'] = "Free trial expired";
                $popUpData['content'] = "Your free trial has expired. Click the <strong>SIGN UP</strong> button below to subscribe and reactivate your account.";
                $popUpData['allow_access'] = 1;
                $popUpData['show_pop_up'] = 0; 
                $popUpData['redirect_plan'] = 1;
            }
        } else {
            if($account_suspended == 1) {
                if($userData->user_type != 1) {
                    $popUpData['title'] = "Account suspended";
                    $popUpData['content'] = "Your account has been suspended. Please contact the account owner, $ownerData->name, and ask them to investigate the matter.";
                    $popUpData['logout_btn'] = 1;
                    $popUpData['allow_access'] = 1;
                    $popUpData['show_pop_up'] = 0;
                } else  {
                    $popUpData['title'] = "Account suspended";
                    $popUpData['content'] = "Your account has been suspended. Click the UPDATE button below to update your credit card and reactivate your account.";
                    $popUpData['signup_btn'] = 1;
                    $popUpData['logout_btn'] = 1;
                    $popUpData['allow_access'] = 1;
                    $popUpData['show_pop_up'] = 0;
                    $popUpData['signup_text'] = "Update";
                    $popUpData['redirect_plan'] = 0;
                }
            } else if($userData->user_type == 1){
                $popUpData['title'] = "Payment declined";
                $popUpData['content'] = "Your payment didn't go through. Your card may have expired or reached its limit. Click the <strong>Update</strong> button below to update your credit card and to keep your account from suspending.";
                $popUpData['signup_btn'] = 1;
                $popUpData['logout_btn'] = 1;
                $popUpData['signup_text'] = "Update";
                $popUpData['logout_text'] = "Dismiss";
                $popUpData['show_pop_up'] = 0;
            } 
        }  
        //preF($popUpData);
        return $popUpData; 
    }

    function set_notification_message(){
        $msg = "Please take a moment to add additional email addresses you would like to use and cell phones you would like to receive text notifications on. We have set notification frequencies to recommended settings. You may adjust them now or later.";
        return $msg;
    }
    
    function prevent_user($userData) 
    {   
        $page_url = false;
        if($userData->is_need_change_pass == 1) {
            $page_url = "change-password";
        } else if(empty($userData->choosed_timezone) || empty($userData->com_name) ) {
           $page_url = "myprofile";
        }
        return $page_url;
    }
