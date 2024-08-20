<?php
namespace App\Models\Front;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /* this model for user_noti_config table */
    
    protected $table = "user_noti_config";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['obj_type','obj_value','status','user_id','country_code','token','verification_code'];


}
