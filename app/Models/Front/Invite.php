<?php
namespace App\Models\Front;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    /* this model for invites table */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from_user_id','status','friend_email','invitation_accept_date','to_user_id','trial_days','owner_id'];

    /**
     * Get the user record associated with the invoice.
     */
    public function user()
    {
        return $this->hasOne('App\User','id','from_user_id');
    }
}
