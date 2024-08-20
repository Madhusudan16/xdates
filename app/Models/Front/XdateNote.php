<?php

namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class XdateNote extends Model
{
    //
    protected $table = 'xdate_notes';

     /**
     * The user that belong to the xdate notes.
     */
    public function user_data()
    {
        return $this->hasOne('App\User', 'id', 'user_id')->select(array('com_name','id','name','profile_image'));
    }

    /*
    * this function make relationship with Xdate
    */
    public function xdate_data()
    {
        return $this->hasOne('App\Models\Front\Xdate', 'id', 'xdate_id')->select(array('xname','id','owner_id'));
    }
}
