<?php
namespace App\Http\Controllers\Admin;

use Auth;
use App\Commons\AdminUserAccess;  
//use App\Http\Requests;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Hash;
use App\Models\Admin\Plan;
use Validator;
use JsValidator;
use Requests;
use Response;
use Log;
use App\Commons\Date;
use App\Models\Front\Tbltimezone;
use App\Http\Controllers\Controller;
use App\Commons\AppMailer;

class adminManagePlanController extends Controller
{
  /**
     * The module id
     */
    protected $moduleId = 3;
  
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
    public function __construct(AdminUserAccess $userAccess)
    {
       $this->middleware($this->guard); 
       $this->user = Auth::guard($this->guard)->user();
       $this->userObj = new Admin();
       $this->timezoneObj = new Tbltimezone(); 

       if(!empty($this->user)){ 
          $this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
          $this->vdata['user'] = $this->user; 
          $this->vdata['curModAccess'] = $this->access['current'];
          $this->vdata['allModAccess'] = $this->access['all'];
          $this->vdata['page_section_class'] = 'my-account user-setting';
       }
    }


    protected function validator(array $data)
    {
      $message = $this->set_validation_message();
      return Validator::make($data, [ 
          'name' => 'required|max:255|unique:plans,name,2,status',
          'number' =>'required|integer|between:1,100',
          'cost' => 'required|numeric|between:1,99999999.99',
          'refer' => 'required|numeric|between:1,999.99',

      ],$message);
    }

    protected function validator_edit(array $data)
    {
        $message = $this->set_validation_message();
        return Validator::make($data, [
          'name' => 'required|max:255|unique:plans,name,'.$data['value'],
          'number' => 'required|integer|between:1,100',
          'cost' => 'required|numeric|between:1,99999999.99',
          'refer' => 'required|numeric|between:1,999.99',
        ],$message);
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
        $plan = new Plan; 
        $this->vdata['page_section_class'] = 'my-account user-setting';
        $this->vdata['page_title'] = 'Manage Plan';
        $this->vdata['plan_list'] =$plans =$plan->where('status',1)->get();
        $this->vdata['plan_lists'] =$plans =$plan->where('status',0)->get();
        return view('admin.plan',$this->vdata);
       
    }

    /**
     * create user
     *
     * @return \Illuminate\Http\Response
     */
 public function create(Request $request)
    {    
      if(isset($this->vdata['curModAccess']['add']) && $this->vdata['curModAccess']['add'] == 1){
        $validator = $this->validator($request->all());
        if ($validator->fails())
        {
          $this->throwValidationException(
          $request, $validator);
        }
       $plan= new Plan();
       $plan->name = $request->name;
       $plan->n_allowed_users = $request->number;
       $plan->cost = $request->cost;
       $plan->refer_percentage = $request->refer;
       $plan->status=1;
       $plan->save();
       return Response::json(['message' => 'Plan successfully Added.'], 200); // 
  

    }else{
         return response()->json(['error' => 'Oops.. Permission Denied.'],403);
      }
  }
    public function editPlan(Request $request)
    {    
      if(isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){ 
        $validator = $this->validator_edit($request->all());
        if ($validator->fails())
        {
          $this->throwValidationException(
            $request, $validator);
        }
        $plan = new Plan;
        $plan = $plan->where('id', $request->value)->get()->first();
        $plan->name = $request->name;
        $plan->n_allowed_users = $request->number;
        $plan->cost = $request->cost;
        $plan->refer_percentage = $request->refer;
        $plan->save();
        return Response::json(['message' => 'Plan successfully Edit.'], 200); // 

       }else{
          return response()->json(['error' => 'Oops.. Permission Denied.'],403);
       }  
    }
        public function activePlan(Request $request)
    {    
    
       $plan = new Plan;
       $plan = $plan->where('id', $request->value)->get()->first();
       $plan->status=1;
       $plan->save();
       return Response::json(['message' => 'User successfully activated.'], 200); // Status code here
       
    }
    public function deactivePlan(Request $request)
    {    
       
         $plan = new Plan;
         $plan = $plan->where('id', $request->value)->get()->first();
         $plan->status=0;
         $plan->save();
         return Response::json(['message' => 'User successfully deactivated.'], 200); // Status code here
      
      
    }
    public function deletePlan(Request $request)
    {    
       if(isset($this->vdata['curModAccess']['delete']) && $this->vdata['curModAccess']['delete'] == 1){ 
       $plan = new Plan;
       $plan = $plan->where('id', $request->value)->get()->first();
       $plan->status=2;
       $plan->save();
       return Response::json(['message' => 'Plan successfully Delete.'], 200); // 
       }else{
          return response()->json(['error' => 'Oops.. Permission Denied.'],403);
       }
    }

    public function set_validation_message()
    {
        $validation_message = array(
              'name.required' => 'Plan name field is required.',
              'name.max'      => 'Plan field length exceeded.',
              'name.unique'   => 'Plan name must be unique.',
              'number.required' => 'Number of user field is required.',
              'number.integer'=> 'Number of user field value must be numeric.',
              'number.between'=> 'Number of user field value between 1 to 100.',
              'cost.required' => "Plan cost field is required.",
              'cost.numeric'  => "Plan cost field value must be numeric.",
              'cost.between'  => "Plan cost value between 1 to 99999999.99.",
              'refer.required' => "Refer user percentage field is required.",
              'refer.numeric' => 'Percentage value must be numeric.',
              'refer.between' => 'Percentage value between 1 to 999.99.'
          );
        return $validation_message;
    }

} 
