<?php 
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ExtendTrial extends Model
{
	 protected $table = 'trial_extend_request';
	 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'requester_id', 'token','is_approved','action_by'
    ];

    public function get_user()
    {
        return $this->hasOne('App\User','id','user_id')->where('status','!=',2)->select(array('id','name','com_name'));
    }
    public function get_requester_user()
    {
        return $this->hasOne('App\Models\Admin\Admin','id','requester_id')->select(array('id','name'));
    }
}
?>