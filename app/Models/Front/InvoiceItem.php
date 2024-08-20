<?php
namespace App\Models\Front;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{ 
	protected $table = "invoice_item"; 

	/**
	*  masassigment 
	*/
	protected $fillable = ['invoice_id','from_date','to_date','plan_id','plan_name','plan_amount','paid_amount'];
}