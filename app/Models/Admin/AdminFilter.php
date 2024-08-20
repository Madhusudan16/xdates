<?php 
namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class AdminFilter extends Model
{
    /*
    * set table name
    */
    protected $table = "admin_filters";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tab_name', 'user_id', 'filter_obj'
    ];
    
}
?>