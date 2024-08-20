<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\User;
use Request;
use Response;
use Illuminate\Support\Facades\Input;
use App\Models\Front\Notification;

class ConfirmNotificationMail extends Controller
{

	/**
     * Confirm a user's email address.
     *
     * @param  string $token
     * @return mixed
    */
    
    public function confirmEmail($token)
    {
        $data = Notification::whereToken($token)->firstOrFail();
        if(isset($this->user)) {
        $setData['token'] = '';
        $setData['status'] = 1;
        $setData['id'] = $data->id;
        $is_edit = 1;
        $this->save($setData,$is_edit);
        
            $notificationConfi = Notification::where('user_id',$this->user->id)->where('status','<>',2)->get();
            $this->vdata['notificationConfi'] = $notificationConfi;
            $this->vdata['is_show_conformation_modal'] = 'in show-confirmation-modal';
            return view('front.notification',$this->vdata);  
        } else {
        	exit;
            $data->token = '';
            $data->status = 1;
            $data->save();
            $this->vdata['notificationConfi'] = $data;
            $this->vdata['is_show_conformation_modal'] = 'in show-confirmation-modal';
            return view('front.notification',$this->vdata); 
        }
        
    }
}