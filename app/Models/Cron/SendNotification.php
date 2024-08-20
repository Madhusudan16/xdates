<?php

namespace App\Models\Cron;

use Illuminate\Database\Eloquent\Model;

class SendNotification extends Model
{
    // this model for send_notification table
    protected $table = 'send_notification';   

   /**
	* mass-assignment for fillable
    *
    */
    protected $fillable = array('user_id','type','event_id','status','send_by');
    
}
