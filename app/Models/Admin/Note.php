<?php 
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Admin;

class Note extends Model
{
	 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'note_type', 'user_id', 'detail','requester_id','is_approved','action_by','remaining_trial_days'
    ];
    /**
    * this function join user table
    *
    */
    public function get_user()
    {
        return $this->hasOne('App\Models\Admin\Admin', 'id','requester_id')->select(array('name','profile_image','id','user_type'));
    }

    /**
    *this function join user table on action_by column in note table
    * @return record
    */
    public function get_actioner_user() 
    {
        return $this->hasOne('App\Models\Admin\Admin', 'id','action_by')->select(array('name','profile_image','id','user_type'));   
    }
    
}
?>