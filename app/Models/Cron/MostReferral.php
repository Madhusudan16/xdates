<?php
namespace App\Models\Cron;
use Illuminate\Database\Eloquent\Model;

class MostReferral extends Model
{ 
	protected $table = "most_referrall_user_logs"; 

	/**
	*  masassigment 
	*/
	protected $fillable = ['user_id','from_date','to_date','status'];
}