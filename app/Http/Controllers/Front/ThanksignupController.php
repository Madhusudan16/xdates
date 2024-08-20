<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Http\Requests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;


class ThanksignupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {    
        return view('front.thanks-signup');
    }
}
