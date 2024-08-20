<?php
namespace App\Models\Front;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class UserPlan extends Model
{
    /* this model for invites table */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   
    protected $fillable = [ 'plan_id', 'user_id', 'plan_start_date','plan_end_date','invoice_no'];

    public $timestamps = false;
    protected $table = 'user_plan';
   
}
