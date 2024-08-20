<?php

namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use App\UserCustomFields;
use App\User;
class Xdate extends Model
{
   
    /**
     * all status .
     */
    public function getAllStatus()
    {
        return array('0'=>'Live','1'=>'Converted','2'=>'Dead');
    }

    /**
     * The producer that belong to the xdate.
     */
    public function producer_data()
    {
        return $this->hasOne('App\User', 'id', 'producer')->select(array('name','id'));
    }

    /**
     * The line that belong to the xdate.
     */
    public function line_data()
    {
        return $this->hasOne('App\UserCustomFields', 'id', 'line')->select(array('id','name','type','status'));
    }

    /**
     * The policy that belong to the xdate.
     */
    public function policy_type_data()
    {
        return $this->hasOne('App\UserCustomFields', 'id', 'policy_type')->select(array('id','name','type','status'));
    }

    /**
     * The industry that belong to the xdate.
     */
    public function industry_data()
    {
        return $this->hasOne('App\UserCustomFields', 'id', 'industry')->select(array('id','name','type','status'));
    }
}
