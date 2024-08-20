<?php
namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Auth;
use App\Models\Admin\Admin;

class Employee extends Controller
{
	public function __construct(){
        $this->middleware('admin');
   }
	
	public function index(){
		
		return view('admin.home');
    }
}