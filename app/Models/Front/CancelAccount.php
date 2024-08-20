<?php
namespace App\Models\Front;
use Illuminate\Database\Eloquent\Model;

class CancelAccount extends Model
{ 
	/**
     * below varible  indicate which table use for this Model
     *
     * @var bool
    */
    protected $table = 'cancel_account';

    protected $fillable = ['owner_id','expired_date','status','token','child_user','referral_user_data','referral_balance_data'];
}