<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Commons\UserAccess; 

use App\Http\Requests;
use Illuminate\Routing\Controller; 
 
use Session;  
use Request;
use Response;

class CmsController extends Controller
{
	public function __construct()
    {
    	$this->middleware('guest');
    }
    public function termOfService(){
    	return view('front.termofservice');
    }
    public function privacyPolicy(){
    	return view('front.privacy-policy');
    }

}