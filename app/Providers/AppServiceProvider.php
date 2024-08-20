<?php

namespace App\Providers;
use Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Input;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // validate year 
        Validator::extend('check_expiry_year', function($attribute, $value, $parameters) {
            $currentMonth = date("m");
            $currentYear  = date("Y");
            $year = Input::get($parameters[0]); //whether month or year
            $month = Input::get($parameters[1]); //whether month or year
            if($year == $currentYear && $month < $currentMonth) {
                return false;
            } else{
                return true;
            }
        }); 
        Validator::extend('super_unique', function($attribute, $value, $parameters) {
            $model = $parameters[0];
            if($model == 'users') {
                $model = "App\User";
            } else if('admins') {
                $model = "App\Models\Admin\Admin";
            }
            if(isset($parameters[1]))  {
                $id = $parameters[1];
                $record = $model::where('email',$value)->where('status','<>',2)->where('id','<>',$id)->get()->count();
            } else {
                $record = $model::where('email',$value)->where('status','<>',2)->get()->count();
            }
            
            if($record == 0) {
                return true;
            } else {
                return false;
            }
        }); 

        Validator::extend('confirm_number', function($attribute, $value, $parameters) {
            $model = $parameters[0];
            
            $where = array();
            if(isset($parameters[1])) {
                $notification_receive = $parameters[1];
                $where['is_active']= $notification_receive;
            } 
            if(isset($parameters[2])) {
                $country_code = $parameters[2];
                $where['country_code']= $country_code;
            }
            if($model == 'user_noti_config') {
                $model = "App\Models\Front\Notification";
                $where['obj_value'] = $value;
                $where ['status'] = 1;
                $record = $model::where($where)->get()->count();
            } 
            if($record == 0) {
                return true;
            } else {
                return false;
            }
            
            
        });
        /**
        * validatate user type
        */
        Validator::extend('check_user_type', function($attribute, $value) {
            if($value == 1) {
                return false;
            }  else {
                return true;
            }   
        });

        Validator::extend('check_email', function($attribute, $value) {
            if(!preg_match("^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+){2,10}\.([a-zA-Z]{2,5})^", $value)) {
                return false;
            } else {
                return true;
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
