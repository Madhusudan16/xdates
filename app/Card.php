<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{ 
   
     /**
     * below varible  indicate which table use for this Model
     *
     * @var bool
     */
    protected $table = 'user_credit_card';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   // protected $fillable = ['card_no,billing_first_name,billing_last_name,address_line_1,address_line_2,city,state,country,zip_code,user_id,expiry_date,status,card_cvv'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['next_bill_date',];

     

}
