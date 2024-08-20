<?php
namespace App\Models\Front;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
	
	protected $table = "exported_list";
    /* this model for feedbacks table */
    protected $fillable = ['type','number_of_item','expired_date','file_name','file_size','format','status','user_id','user_name','owner_id'];   
}
