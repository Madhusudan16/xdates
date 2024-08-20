<?php
namespace App\Models\Front;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model 
{ 
    protected $fillable = ['amount','discount','bill_date','to_address','from_address','owner_id','status','plan_id'];
    
}