<?php
namespace App\Models\Front;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    /* this model for feedbacks table */
    protected $fillable = ['owner_id','user_id','feedback_text'];   
}
