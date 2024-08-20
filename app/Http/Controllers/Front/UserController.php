<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Commons\UserAccess; 
//use App\Http\Requests;
//use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Hash;
use App\Models\Admin\Plan;
use App\Models\Front\UserPlan;
use App\User;
use Validator;
use JsValidator;
//use Request;
use Illuminate\Support\Facades\Input;
use Response;
use Log;
use App\Commons\Date;
use App\Models\Front\Tbltimezone;
use App\Http\Controllers\Controller;
use App\Commons\AppMailer;
use Redirect;
use App\UserCustomFields;

class UserController extends Controller
{
  /**
     * The module id
     */
    protected $moduleId = 2;
  
  /**
     * The guard name
     */
    protected $guard = 'web';
  
  /**
     * view data
     */
    protected $vdata = array();
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserAccess $userAccess)
    {
       $this->middleware('auth'); 
       $this->user = Auth::guard($this->guard)->user(); 
       $this->userObj = new User();
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

      return Validator::make($data, [
          'name' => 'required|max:255',
          'email' => "required|max:255|super_unique:users|check_email",
          'type'=> "required|check_user_type", 
      ]);
    }

    protected function validator_edit(array $data)
    {
        $id = $data['value'];
        return Validator::make($data, [
          'name' => 'required|max:255',
          'email' => "required|email|max:255|super_unique:users,".$data['value']."|check_email",
          'typen'=> "required|check_user_type",
        ]);
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {    
        if($page_url = prevent_user($this->user)) {
           return redirect($page_url);
        }
        $checkAccess = acoount_expire_text($this->user);
        $this->vdata['check_access'] = $checkAccess;
        /*if($checkAccess  && isset($checkAccess) &&  $checkAccess['allow_access'] == 1) {
            return redirect()->to('/');
        }*/
        if($this->vdata['curModAccess']['view'] != 1){
            return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        if(!check_user($this->user->parent_user_id)) {
            Auth::logout();
        }
        $user = $this->user;
        $owner_id =  $user->id;  
        if ( $user->parent_user_id  > 0) {
           $owner_id =   $user->parent_user_id ;
        }
       
        $this->vdata['page_section_class'] = 'my-account user-setting';
        $this->vdata['page_title'] = 'Manage Users';

        $uWhere = array('id'=>$owner_id,'status'=>1);
        $uOrWhere = array('parent_user_id'=>$owner_id,'status'=>1);
        $adminWhere =  array('parent_user_id'=>$owner_id,'status'=>1,'user_type'=>2);
        $this->vdata['user_list'] =$users = User::where($uWhere)->orwhere($uOrWhere)->orderBy('first_name', 'asc')->get();

        /*$this->vdata['admin_list']  = User::where($adminWhere)->orderBy('first_name', 'asc')->get();*/

        $uWhere = array('id'=>$owner_id,'status'=>0);
        $uOrWhere = array('parent_user_id'=>$owner_id,'status'=>0);
        
        $this->vdata['userd_list'] =$users =User::where($uWhere)->orwhere($uOrWhere)->orderBy('first_name', 'asc')->get();
        //preF($this->vdata['userd_list']);
        return view('front.user-manage',$this->vdata);
 
    }

    /**
     * create user
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) 
    {    
      if($this->user->is_expired == 1 ) {
          return response()->json(['error' => 'You have no permission to access this functionality.'],403);
      }

      if(isset($this->vdata['curModAccess']['add']) && $this->vdata['curModAccess']['add'] == 1){
        $validator = $this->validator($request->all());
        if ($validator->fails())
        {
          $this->throwValidationException(
          $request, $validator);
        }

        //$user = new User;
        $owner_id =  ($this->user->parent_user_id == 0 ) ? $this->user->id : $this->user->parent_user_id;  
        $ownerUser = User::where('id',$owner_id)->get()->first(); //fetch owner user for current plan

        $user = new User;
        $users_count= $user->where(['parent_user_id'=>$owner_id , 'status'=>1])->orWhere(['id'=>$owner_id , 'status'=>1])->count();
       
        $n_allowed_users = 0;
        if($ownerUser->current_plan > 0){ 
          $plan = Plan::where('id',$ownerUser->current_plan)->get()->first();
          $n_allowed_users = $plan->n_allowed_users;
        }else{
          $n_allowed_users = Plan::where('status',1)->max('n_allowed_users'); 
        }
           
        //if user has 
        if($users_count >= $n_allowed_users){
           return response()->json(['error' => 'number of user exceed.'],403);
        } 

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
        $user->noti_mob_frequency = 90;
        $user->noti_email_frequency = 90;
        $user->noti_email_followup_frequency = 1;
        $user->noti_mob_followup_frequency    = 1;
        $user->show_noti_msg = 1;

        $user->status = 1;
        $user->com_name = $ownerUser->com_name;
        $user->verified = 1;
        $pass =  str_random(8);
        $user->password = $pass;
        $loggedInUser = Auth::guard($this->guard)->user(); 
        
        $owner_id =  $loggedInUser->id;  
        if ( $loggedInUser->parent_user_id  > 0) {
           $owner_id =   $loggedInUser->parent_user_id ;
        }
        //$ownerUser = $user->where('id', $owner_id)->get()->first();
       
        //$user->choosed_timezone=$ownerUser->choosed_timezone;
        if($loggedInUser->parent_user_id > 0){
           $user->parent_user_id=$loggedInUser->parent_user_id;
           
        }else{
           $user->parent_user_id = $loggedInUser->id;
        }
         $user->save();
         $user->decrypt_pass = $pass;
         $mailer =new AppMailer();
		     $user->com_name = $ownerUser->com_name;
         $mailer->sendUserPasswordByOwner($user);
         return response()->json(['message' => 'User successfully created.'], 200); // Status code here
      }else{
         return response()->json(['error' => 'Oops.. Permission Denied.'],403);
      }
   }

    public function active(Request $request)
    {    
      if($this->user->is_expired == 1 ) {
          return response()->json(['error' => 'You have no permission to access this functionality.'],403);
      }
      if(isset($this->vdata['curModAccess']['activate_deactive']) && $this->vdata['curModAccess']['activate_deactive'] == 1){
        $user = new User;
        $owner_id =  $this->user->id;  
        if ( $this->user->parent_user_id  > 0) {
           $owner_id =   $this->user->parent_user_id ;
        }
       $users_count= $user->where(['parent_user_id'=>$owner_id , 'status'=>1])->orWhere(['id'=>$owner_id , 'status'=>1])->count();
       //Log::info($users_count);
       $plan_data=  UserPlan::where('user_id',$owner_id)->where('status',1)->get()->first();
       //log::info($plan_data->plan_id);
        if(!empty($plan_data) && $plan_data->count() != 0) {
            $where  = array('id'=>$plan_data->plan_id,'status'=>1);
            $plan= Plan::where($where)->get()->first();
            $max_allowed_users=$plan->n_allowed_users;
        } else {
            $where =  array('status'=>1);
            $plan= Plan::where($where)->get()->first()->max('n_allowed_users');
            $max_allowed_users=$plan;
        }
        
        
       if($users_count >= $max_allowed_users){
           return response()->json(['error' => 'number of user exceed.'],403);
       }
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
         $user = new User;
         $user = $user->where('id', $request->value)->get()->first();
         if($user->parent_user_id==0){
            return response()->json(['error' => 'Oops.. Permission Denied It is Owner Account.'],403);
         }
         $user->status=0;
         $user->save();
         if($this->user->id == $request->value) {
             Auth::logout();
             return Response::json('1'); 
         }
         return Response::json(['message' => 'User successfully deactivated.'], 200); // Status code here
      
       }else{
          return response()->json(['error' => 'Oops.. Permission Denied.'],403);
        }  
    }
    public function editUser(Request $request)
    {    
      if($this->user->is_expired == 1 ) {
          return response()->json(['error' => 'You have no permission to access this functionality.'],403);
      }
      if(isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){ 
        $validator = $this->validator_edit($request->all());
        if ($validator->fails())
        {
          $this->throwValidationException(
            $request, $validator);
        }
        $user = new User;
        $user = $user->where('id', $request->value)->get()->first();
        $user->name = $request->name;
        $user_name = explode(" ", $request->name);
        if(count($user_name)>1){
        $user->first_name = $user_name['0'];
        $user->last_name = $user_name['1'];
        }else{
          $user->first_name = $request->name;
          $user->last_name = '';
        }
        
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
        
       $user = new User;
       $user = $user->where('id', $request->value)->get()->first();
       $user->status=2;
       $user->save();
       return Response::json(['message' => 'User successfully Delete.'], 200); // Status code here
    }

    /**
    * this function change owner ship and made owner deactive
    *
    * @return true on success 
    */
    public function changeOwnerShip()
    {

        $redirect = new Redirect;
        if(Input::get('id') && !empty(Input::get('id'))) {
            $id = Input::get('id');
            $com_name = $this->user->com_name;
            $currentDataUpdate = array('status'=>0,'user_type'=>2,'parent_user_id'=>$id);
            $where = array('id'=>$id,'status'=>1);
            $updatedData = array('user_type'=>1,'com_name'=>$com_name,'parent_user_id'=>0);
            $changeParentId = array('parent_user_id'=>$id);
            if($id != "" && !empty($id) && $id) {
                UserCustomFields::where('owner_id',$this->user->id)->update(['owner_id'=>$id]);
                User::where('parent_user_id',$this->user->id)->update($changeParentId);
                User::where($where)->update($updatedData);
                User::where('id',$this->user->id)->update($currentDataUpdate);
                Auth::logout();
               
            } else {
              
               //return Response::json('Opps! something went wrong',403);
            }

        }

    }
} 





