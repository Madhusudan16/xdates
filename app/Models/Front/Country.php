<?php
namespace App\Models\Front;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use DB;

class Country extends Model
{
    protected $table = 'country';
    /**
    * this function return all country 
    *
    * @return country from country table 
    */
    public function getCountry($id=null)
    {
        if(!empty($id) && $id) {
            return DB::table('country')->where('id',$id)->get();
        }
    	return DB::table('country')->get();
    }

    /**
    * this function fetch state by country id
    *
    * @return state from state table 
    */

    public function  getState($id=null,$state_id = null )
    {
    	if($id != null) {
    		return DB::table('state')->where('country_id',$id)->get();
    	} else if($state_id != null ) {
            return DB::table('state')->where('id',$state_id)->get();
    	} else {
            return DB::table('state')->get();
        }
    }

}
