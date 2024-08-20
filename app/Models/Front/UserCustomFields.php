<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class UserCustomFields extends Model
{


     protected $table = 'user_customize_fields';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'name', 'type','owner_id','status',
     ];

	protected $guarded = array();

    
	
}
