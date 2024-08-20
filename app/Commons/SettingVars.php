<?php
namespace App\Commons;

use App\Models\Admin\Setting;

class SettingVars{ 
    
	
    /**
     * Create a new user access instance.
     */
    public function __construct()
    {
      //do intital things
      
    }
	
	public static function getVars(){
	  $tempData = Setting::where('status','!=',2)->get();
	  $setValue = array();
	  foreach ($tempData as $key => $value) {
           $setValue[$value->field_key] = $value->field_value; // set data of pair  key and value 
      }
	  return $setValue;  
	}

  public static function get_setting_value($keys) 
  {
      if(!empty($keys)) {
          $where = array('field_key'=>$keys,'status'=>1);
          $settingValue = Setting::where($where)->get(['field_value'])->first();
          
          if(!empty($settingValue)) {
              return $settingValue->field_value;
          } else {
              return false;
          }
      } else {
          return false;
      }
  } 
}

