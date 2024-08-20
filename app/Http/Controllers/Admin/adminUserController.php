<?php
namespace App\Http\Controllers\Admin;

use Auth;
use App\Commons\AdminUserAccess;  
//use App\Http\Requests;
//use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Hash;
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

class adminUserController extends Controller
{
  /**
     * The module id
     */
    protected $moduleId = 2;
  
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
   
      $msg = array(
        'check_user_type'=>'something went wrong',
        );
      return Validator::make($data, [
          'name' => 'required|max:250',
          'email' => 'required|email|max:255|super_unique:admins|check_email',
          'type'=>'required|check_user_type'
      ],$msg);
    }

    protected function validator_edit(array $data)
    {
        $msg = array(
        'check_user_type'=>'something went wrong',
        );
        return Validator::make($data, [
          'name' => 'required|max:255',
          'email' => 'required|email|max:255|super_unique:admins,'.$data['value']."|check_email",
          'typen'=>'required|check_user_type'
        ],$msg);
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {    
        //preF($this->vdata['curModAccess']);

        if($this->vdata['curModAccess']['view'] != '1'){
            return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }

        $this->vdata['page_section_class'] = 'my-account user-setting';
        $this->vdata['page_title'] = 'Manage Users';
        $this->vdata['user_list'] =$users =Admin::where('status',1)->orderBy('first_name', 'asc')->get();
        $this->vdata['userd_list'] =$users =Admin::where('status',0)->orderBy('first_name', 'asc')->get();
        return view('admin.user-manage',$this->vdata);
 
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
        if(($this->user->user_type==1) || ($this->user->user_type==2 && ($request->type==3 || $request->type==4)) || ($this->user->user_type==3 && $request->type==4)) {
          $user = new Admin;
          $user->name = $request->name;
          $user_name = explode(" ", $request->name);
          if(count($user_name)>1){
            $user->first_name = $user_name['0'];
            $user->last_name = $user_name['1'];
          }else{
            $user->first_name = $request->name;
            $user->last_name = '';
          }
          $user->user_type = $request->type;
          $user->email = $request->email;
          $user->is_need_change_pass = 1;
          $user->status = 1;
          $user->verified = 1;
          $pass =  str_random(8);
          $user->token = str_random(40);
          $user->password = bcrypt($pass);
          $owner =Admin::where('id','1')->get()->first();
          $user->choosed_timezone= $owner->choosed_timezone;
          $user->save();
          $user->decrypt_pass = $pass;
          $mailer =new AppMailer();
          $mailer->sendAdminUserPasswordByOwner($user);
          return Response::json(['message' => 'User successfully created.'], 200); // Status code here
        }else{
         return response()->json(['error' => 'Oops.. Permission Denied.'],403);
        } 
      }else{
         return response()->json(['error' => 'Oops.. Permission Denied.'],403);
      }
   }

    public function active(Request $request)
    {    
    if(isset($this->vdata['curModAccess']['activate_deactive']) && $this->vdata['curModAccess']['activate_deactive'] == 1){
       $user = new Admin;
       $user = $user->where('id', $request->value)->get()->first();
       $user->status=1;
       $user->save();
       return Response::json(['message' => 'User successfully activated.'], 200); // Status code here
       }else{
          return response()->json(['error' => 'Oops.. Permission Denied.'],403);
       }  
    }
    public function deactive(Request $request)
    {    
       if(isset($this->vdata['curModAccess']['activate_deactive']) && $this->vdata['curModAccess']['activate_deactive'] == 1){
         $user = new Admin;
         $user = $user->where('id', $request->value)->get()->first();
         $user->status=0;
         $user->save();
         return Response::json(['message' => 'User successfully deactivated.'], 200); // Status code here
      
       }else{
         return response()->json(['error' => 'Oops.. Permission Denied.'],403);
        }  
    }
    public function editUser(Request $request)
    {    
      if(isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){
        $validator = $this->validator_edit($request->all());
        if ($validator->fails())
        {
          $this->throwValidationException(
            $request, $validator);
        }

        $user = new Admin;
        $user = $user->where('id', $request->value)->get()->first();
        $user->name = $request->name;
        $user->user_type = $request->typen;
        $user->email = $request->email;
        $user->save();
        return Response::json(['message' => 'User successfully Edit.'], 200); // Status code here
    
        }else{
          return response()->json(['error' => 'Oops.. Permission Denied.'],403);
         }  
    }
    public function deleteUser(Request $request)
    {    
        
       $user = new Admin;
       $user = $user->where('id', $request->value)->get()->first();
       $user->status=2;
       $user->save();
       return Response::json(['message' => 'User successfully Delete.'], 200); // Status code here
    }

} 
