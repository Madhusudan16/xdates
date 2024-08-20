<?php
namespace App\Models\Front;
use Illuminate\Database\Eloquent\Model;

class LogModel extends Model
{
	protected $table = "logs";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['log_type','event_user_id','event_data','notes','owner_id','token'];

    /**
    * this function return log type
    *
    */
    public static function get_log_types()
    {
    	return array('1'=>'delete customize field','restore customize field');
    }

     /**
    * this function join user table
    *
    */
    public function get_user()
    {
        return $this->hasOne('App\User', 'id','event_user_id')->select(array('name','profile_image','id'));
    }
}
