<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Commons\UserAccess; 
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;
use App\UserCustomFields;
use Auth;
use App\Commons\AppMailer;
use Response;
use App\Models\Front\LogModel;
use App\Models\Front\Tbltimezone;

class PolicyController extends Controller
{
 /**
     * The module id
     */
    protected $moduleId = 3;
  
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
       //$this->userObj = new User();
       $this->timezoneObj = new Tbltimezone();

     if(!empty($this->user)){ 
      $this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
      $this->vdata['user'] = $this->user; 
      $this->vdata['curModAccess'] = $this->access['current'];
      $this->vdata['allModAccess'] = $this->access['all'];
      
      //preF($this->access);      
      if($this->vdata['curModAccess']['view'] != 1){
            return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
      $this->vdata['page_section_class'] = 'my-account user-setting';
     }
    }
	public function create(Request $request) {
      $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id; 
      $whereCust = array('owner_id'=>$ownerID,'status'=>1);

      if(isset($this->vdata['curModAccess']['add']) && $this->vdata['curModAccess']['add'] == 1){
		    $policy = UserCustomFields::where('type', $request->type)->where($whereCust)->where('name', $request->value);
	
		  if ($policy->exists()) {
			   Log::info("Exists");
			   return response()->json(['error' => 'Policy already exists.']);
			//return Response::json(['error' => 'Policy already exists.'], 203); // Status code here
		  } 
      $fieldData = array();
      
      //echo $fieldData['owner_id'];
      $fieldData['name'] = $request->name;
      if(isset($request->id) && $request->id != "") {
          if(isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1) {
              UserCustomFields::where('id',$request->id)->update($fieldData);
              return Response::json(['message' =>"updated" ], 200); 
          } else {
              return Response::json(['message' => 'Permission denied.'], 403); 
          }   
      } else{
          
          $fieldData['status'] = 1;
          $fieldData['type'] = $request->type;
          $createdData = UserCustomFields::create($fieldData);
          $createdData->owner_id = $ownerID;
          $createdData->save();
          return Response::json(['id' =>$createdData->id], 200); 
      } 
  }
}
	
    public function delete(Request $request) {
        if(isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){

            $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id; 
            $whereCust = array('owner_id'=>$ownerID);

		        $policy = new UserCustomFields;
		        $policy = $policy->where('id', $request->value)->where($whereCust)->get()->first();
      	    $policy->status = 2;
            $policy->save();
            $notes['name'] = $policy->name;
            $notes['id'] = $request->value; 
            $notes['type'] = $policy->type;
            $notes['user_name'] = $this->user->name;
            $user = Auth::guard($this->guard)->user(); 
            $user =$user->where('id',$policy->owner_id)->get()->first();
            Log::info($user->email);
            $notes['email'] = $user->email;
            $notes['owner_name'] = $user->name;
            $policy->deleted_by = $this->user->name;
            $notesJson = json_encode($notes); 
            $token = str_random(5);
            $logData = array('log_type'=>1,'event_user_id'=>$this->user->id,'notes'=>$notesJson,'owner_id'=>$ownerID,'token'=>$token);
            LogModel::create($logData);
            $mailer =new AppMailer();
            $mailer->sendEmailRestoreTo($user,$policy);

          }else{
            return Response::json(['message' => 'Permission denied.'], 403); 
          }
        }
        public function policyRecreate($id){
           $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id; 
           $whereCust = array('owner_id'=>$ownerID);
            if(isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){
               $policy = new UserCustomFields; 
               $policy = $policy->where('id', $id)->where($whereCust)->get()->first();
               if($policy->status != 1){
                    $notes = $policy->name;
                    $logData = array('log_type'=>2,'event_user_id'=>$this->user->id,'notes'=>$notes,'owner_id'=>$ownerID,'token'=>$policy->token);
                    LogModel::create($logData);
                    $policy->status = 1;
                    $policy->save(); 
               } else {
                 
                 return Response::view('errors.404',array('message'=>"Custom restore field link has been expired.",'title'=>"Link expired!"),404);
          
                 }
            }
            return redirect('manage-customize-fields');
        }


}
