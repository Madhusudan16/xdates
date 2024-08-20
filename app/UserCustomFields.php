<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class UserCustomFields extends Model
{
    //
/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'status'
    ];

     protected $table = 'user_customize_fields';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

	//public function user() {
        	//return $this->hasOne('App\User');
    	//}

    
	
}
