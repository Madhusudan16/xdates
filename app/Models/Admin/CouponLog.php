<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CouponLog extends Model
{
    /**
    * set table name 
    */
	
    protected $table = 'coupon_logs';
	 
     /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $guarded = [];
}
