<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
	 protected $table = 'plans';
	 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'n_allowed_users', 'cost','refer_percentage',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
  
}
