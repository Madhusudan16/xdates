<?php 
namespace App\Http\Controllers\Cron;

use Illuminate\Routing\Controller;
use App\Models\Admin\Coupon;

class CouponExpireCron extends Controller
{
	/**
    * get trial extend list 
    */
    public function get_expired_coupon() 
    {
    	$toDay = date('Y-m-d');

    	$couponData  = Coupon::whereDate('coupon_expire','<',$toDay)->where('status','<>',3)->where('status','<>',4)->get();

    	$this->changed_status($couponData);
    }

    /**
    * this function change status
    *
    */
    public function changed_status($couponData) 
    {
    	if($couponData->count() == 0) {
    		return false;
    	}
		foreach($couponData as $coupon) {
    		$coupon->status = 4;
    		$coupon->save();
    	}
    }
    
}
?>