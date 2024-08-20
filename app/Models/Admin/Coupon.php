<?php

namespace App\Models\Admin;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Coupon extends Authenticatable
{
	  protected $table = 'coupons';
	 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_type', 'coupon', 'coupan_day','coupon_percent','coupon_expire',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
 }
