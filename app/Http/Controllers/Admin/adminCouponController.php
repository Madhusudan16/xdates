<?php
namespace App\Http\Controllers\Admin;

use Auth;
use App\Commons\AdminUserAccess;  
use Illuminate\Http\Request;
use Hash;
use App\Models\Admin\Coupon;
use App\Models\Admin\Admin;
use Validator;
use JsValidator;
use Requests;
use Response;
use Log;
use App\Commons\Date;
use App\Models\Front\Tbltimezone;
use App\Http\Controllers\Controller;
use App\Commons\AppMailer;

class adminCouponController extends Controller
{
  /**
     * The module id
     */
    protected $moduleId = 6;
  
  /**
     * The guard name
     */
    protected $guard = 'admin';
  
    /**
     * view data
     */
    protected $vdata = array();
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $status = array();

     public function __construct(AdminUserAccess $userAccess)
     {
       $this->middleware($this->guard); 
       $this->user = Auth::guard($this->guard)->user();
       $this->userObj = new Admin();
       $this->timezoneObj = new Tbltimezone();
       $this->status = array('Inactive','Active',3=>'Used',4=>'Expired');
       if(!empty($this->user)){ 
          $this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
          $this->vdata['user'] = $this->user; 
          $this->vdata['curModAccess'] = $this->access['current'];
          $this->vdata['allModAccess'] = $this->access['all'];
          $this->vdata['page_section_class'] = 'my-account user-setting';
          $this->vdata['status'] = $this->status;
       } 
    }


    protected function validator(array $data)
    {
    
      $message =   array(
          'no_of_terms.required'=> 'Please enter number of time user will be use this coupon',
          'no_of_terms.numeric' => 'Please enter only numeric value'
        );
      if($data['user_type']==2){ 
       return Validator::make($data, [
         'trial' =>'required|integer|min:1',
         'discount' => 'numeric|between:1,100',
         'coupon' => 'required|max:8|unique:coupons',
       ],$message);
     }else{
       return Validator::make($data, [
         'discount' => 'required|numeric|between:1,100',
         'trial' =>'integer',
         'coupon' => 'required|max:8|unique:coupons',
       ]);
     }
    }

    protected function validator_edit(array $data) 
    {
        $message =   array(
          'no_of_terms.required'=> 'Please enter number of time user will be use this coupon',
          'no_of_terms.numeric' => 'Please enter only numeric value'
        );
      Log::info($data);
     if($data['user_type']==2){ 
       return Validator::make($data, [
         'trial' =>'required|integer|min:1',
         //'discount' => 'numeric|between:1,100',
         'coupon' => 'required|max:8|unique:coupons,coupon,' .$data['value'],
         
         //'required|email|max:255|unique:users,email,'.$data['value'],
       ]);
     }else{
       return Validator::make($data, [
         'discount' => 'required|numeric|between:1,100',
         'trial' =>'integer|min:1',
         'coupon' => 'required|max:8|unique:coupons,coupon,' .$data['value'],
         'no_of_terms'=> 'required|numeric'
       ]);
     }
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {    
        if($this->vdata['curModAccess']['view'] != 1){
            return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        $this->vdata['page_section_class'] = 'my-account user-setting';
        $this->vdata['page_title'] = 'Manage Coupon';
        $this->vdata['coupon_list'] =$coupon =Coupon::where('status','<>',2)->where('status','<>',0)->get();
        $this->vdata['coupon_listd'] =$coupon =Coupon::where('status',0)->get();
        return view('admin.coupon',$this->vdata);
 
    }

     public function create(Request $request)
     {    
     
      if(isset($this->vdata['curModAccess']['add']) && $this->vdata['curModAccess']['add'] == 6){
        $validator = $this->validator($request->all());
        if ($validator->fails())
        {
          $this->throwValidationException( 
          $request, $validator);
        }
        if($this->user->user_type==1 || $this->user->user_type==2){
          $coupon = new Coupon;
         
          $coupon->user_type = $request->user_type;
          $coupon->coupon = $request->coupon;
          $coupon->status = $request->status;
          $coupon->coupan_day = $request->trial;
          $coupon->coupon_percent = $request->discount;
          $coupon->no_of_time = $request->no_of_terms;
          $date=date('Y-m-d',strtotime($request->expire));
          $coupon->coupon_expire = $date;
          $coupon->save(); 
          return Response::json(['message' => 'User successfully created.'], 200); // Status code here

        }else{
         return response()->json(['error' => 'Oops.. Permission Denied.'],403);
        } 
      }else{
         return response()->json(['error' => 'Oops.. Permission Denied.'],403);
      }
   }



    public function activeCoupon(Request $request)
    {    
       $coupon = new Coupon;
       $coupon = $coupon->where('id', $request->value)->get()->first();
       if($coupon->status==4 || $coupon->status == 3){ 
            return Response::json(['message' => 'Can Not Delete,Coupon Already Used'], 403); // Status code here
       }
       $coupon->status=1;
       $coupon->save();
       return Response::json(['message' => 'Coupon successfully activated.'], 200); // Status code here
     
    }
    public function deactiveCoupon(Request $request)
    {    
        $coupon = new Coupon;
        $coupon = $coupon->where('id', $request->value)->get()->first();
        if($coupon->status==4 || $coupon->status == 3){ 
            return Response::json(['message' => 'Can Not Delete,Coupon Already Used'], 403); // Status code here
        }

        $coupon->status=0;
        $coupon->save();
        return Response::json(['message' => 'Coupon successfully deactivated.'], 200); // Status code here
      
    
    }
    public function deleteCoupon(Request $request)
    {    
      if(isset($this->vdata['curModAccess']['delete']) && $this->vdata['curModAccess']['delete'] == 6){
         $coupon = new Coupon;
         $coupon = $coupon->where('id', $request->value)->get()->first();
         if($coupon->status==4 || $coupon->status == 3){ 
          
           return Response::json(['message' => 'Can Not Delete,Coupon Already Used'], 403); // Status code here
         }else{
          $coupon->status=2;
          $coupon->save();
          return Response::json(['message' => 'Coupon successfully Deleted.'], 200); // Status code here
         }
      }else{
         return response()->json(['error' => 'Oops.. Permission Denied.'],403); 
      }  
    }

    public function editCoupon(Request $request)
    {    
      if(isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 6){ 
        $validator = $this->validator_edit($request->all());
        if ($validator->fails())
        {
          $this->throwValidationException(
            $request, $validator);
        }
        $coupon = new Coupon;
        $coupon = $coupon->where('id', $request->value)->get()->first();
        if($coupon->status==4 || $coupon->status==3){
             return Response::json(['message' => 'Can Not Edit,Coupon Already Used or expired'], 403); // Status code 
         }else{


          $coupon->user_type = $request->user_type;
          $coupon->coupon = $request->coupon;
          $coupon->status = $request->status;
          if($request->user_type==1){
            $coupon->coupon_percent = $request->discount;
            $coupon->coupan_day=null;
          }else{
            $coupon->coupan_day = $request->trial;
            $coupon->coupon_percent=null;
          }
          if(isset($request->no_of_terms)) {
              $coupon->no_of_time = $request->no_of_terms;
          }
          $coupon->coupon_expire = date("Y-m-d",strtotime($request->expire));
          $coupon->save();
        return Response::json(['message' => 'Coupon successfully Edit.'], 200); // Status code here
       }
    }else{
          return response()->json(['error' => 'Oops.. Permission Denied.'],403);
         }  
    }
} 
