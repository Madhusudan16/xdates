<?php

namespace App;
use Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','first_name','last_name','email', 'com_name', 'password','last_login','trial_start_date','trial_end_date','account_suspended','show_noti_msg'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	 /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->token = str_random(30);
        });
    }  
    /**
     * Set the password attribute.
     *
     * @param string $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = (Hash::needsRehash($password)) ? bcrypt($password) : $password;
    }
	 /**
     * Confirm the user.
     *
     * @return void
     */
    public function confirmEmail()
    {
        $this->verified = true; 
        $this->status = 1;
        $this->token = null;
        $this->save();
    }
        /**
     * Many-To-Many Relationship Method for accessing the User->customfields
     *
     * @return QueryBuilder Object
     */
    public function UserCustomFields()
    {
        return $this->belongsToMany('App\UserCustomFields');
    }
    /**
     * Get the card record associated with the user.
     */
    public function card()
    {
        return $this->hasOne('App\Models\Front\Card','user_id','id')->select(array('id', 'expiry_date','user_id','card_no','auth_card_id'));
    }
}
