<?php
namespace App\Models\Front;

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

      /**
     * Get the card record associated with the user.
     */
    public function user()
    {
        return $this->belongsTO('App\User','user_id','id');
    }

    /*
    * this function join country and card table
    *
    */
    public function get_country()
    {
        return $this->hasOne('App\Models\Front\Country','id','country');
    }

    /*
    * this function join state and card table
    *
    */
    public function get_state()
    {
        return $this->hasOne('App\Models\Front\State','id','state');
    }
}
