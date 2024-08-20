<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Commons\UserAccess; 

use App\Http\Requests;
use Illuminate\Routing\Controller; 

use App\Models\Front\Xdate;
use App\UserCustomFields;

use App\User;
use App\Models\Front\State;
use App\Models\Front\XdateNote;
use Session;

use Illuminate\Http\Request;
use Validator;
use JsValidator; 
use Response;
use DB;

use App\Commons\AppMailer;

class XdateController extends Controller
{
	/**
     * The module id
     */
    protected $moduleId = 1;
	
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
		if(!empty($this->user)){ 
			$this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
			$this->vdata['user'] = $this->user; 
			$this->vdata['curModAccess'] = $this->access['current'];
			$this->vdata['allModAccess'] = $this->access['all'];
            if(!check_user($this->user->parent_user_id)) {
                 Auth::logout();
            }
		} 
        $this->vdata['page_section_class'] = 'add_home';
		$this->customField = new UserCustomFields;
    }


    public function addUpdateXDate(Request $request){
    	$ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id; 

    	$currentAction = $request->xaction;
    	$xDate = new Xdate;

    	//first check the action is exist in our functionality
    	if(isset($currentAction) && in_array($currentAction,array('add','edit'))){ 

    		// checking user has access to do the repective actions
    		$hasAccess = false;
    		if($currentAction == 'add' && (isset($this->vdata['curModAccess']['add'])  && $this->vdata['curModAccess']['add'] == 1)){ 
    			$hasAccess = true;
    		}else if($currentAction == 'edit' && (isset($this->vdata['curModAccess']['edit'])  && $this->vdata['curModAccess']['edit'] == 1)){ 

				$xDate = $xDate->where('id',$request->current_xdate_id)->get()->first();

				if($xDate->owner_id == $ownerID){
					if($this->user->user_type > 2 && $xDate->user_id != $this->user->id){
						$hasAccess = false;
						$fixProducer = $this->user->id;
					}else{
						$hasAccess = true;
					}
				}else{
					$hasAccess = false;
				}  

    		}

    		if($hasAccess){
    			try{ 
                    $previous_produces = $xDate->producer;
	    			$xDate->owner_id = $ownerID;
	    			$xDate->user_id = $this->user->id;

	    			$xDate->xdate = date("Y-m-d",strtotime($request->xdate_org));//create date from m/d/Y
	    			$xDate->xname = $request->xname;
	    			$xDate->line = $request->line;
	    			$xDate->policy_type = $request->policy_type;
	    			$xDate->industry = $request->industry;
	    			$xDate->xcontact = $request->contact;  
	    			$xDate->producer = ($this->user->user_type > 2) ? $this->user->id : $request->producer; 
	    			$xDate->phone = $request->phone;
	    			$xDate->city = $request->city;
	    			$xDate->state = $request->state;
	    			$xDate->website = $request->website;
	    			$xDate->email = $request->email;
	    			$xDate->follow_up_date = date("Y-m-d",strtotime($request->follow_up_date));//create date from m/d/Y
	    			$xDate->status = $request->status;

	    			if($currentAction == "add"){ 
	    				$xDate->created_at = date("Y-m-d H:i:s"); 
	    			}else if($currentAction == "edit"){
	    				$xDate->updated_at = date("Y-m-d H:i:s"); 
                        if($request->producer != $previous_produces) {
                            $this->send_email($request->producer,$xDate->xname);
                            $is_added = $this->change_producer_note($request->current_xdate_id,$request->producer,$previous_produces);
                            if($is_added) {
                                $xDate->last_note_datetime = date("Y-m-d H:i:s");
                            }
                        }
	    			}
	    			$xDate->save();

	    			$xData = $this->getXdateData($xDate);
	    			return Response::json(['success' => true, 'message' => 'The X-Date has been saved successfully.', 'xdate' => $xData]);

    			}catch(Exception $e){
			       // do task when error
    				return Response::json(['message' => $e->getMessage()], 403); 
			    }
    		}else{
    			return Response::json(['message' => 'Permission denied.'], 403); 
    		}


    	}else{
    		return Response::json(['message' => 'Unauthorized access.'], 403); 
    	}
 
    }

    public function getNextDate(Request $request){
        $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id;       
         

        if(!empty($request->current_x_date)){ 
             
            if(empty($request->xids)){
                $request->xids = array();
            }
            $frmDate =  str_replace('/','',$request->current_x_date); 
            $data = DB::table('xdates')
                ->select(array('id','xname',DB::raw("DATE_FORMAT(xdate,'%m%d') as md")))
                ->where('owner_id',$ownerID)
                ->whereNotIn('id', $request->xids)
                ->where(DB::raw("DATE_FORMAT(xdate,'%m%d')"),'>=',$frmDate) 
                ->orderBy('md','asc')->orderBy('xname','asc')->get(); 
   
            if(!empty($data)) {
                $allData = $this->getXdateData(null,$data[0]->id);
                return Response::json(['success' => true,  'xdate' => $allData]);                 
            }else{
                return Response::json(['success' => true,  'xdate' => array()]);
            }  
            
        }else { 
            return Response::json(['message' => 'Unauthorized access.'], 403); 
        }

    }
    public function getXdateData($xDate = null,$xID = 0){

    	if(empty($xDate) && !empty($xID)){
    		$tempXdate = new Xdate;
	    	$xDate = $tempXdate->where('id',$xID)->get()->first();
    	} 
    	$tempDate = $xDate->xdate;
    	$xDate->xdate = date('m/d/Y',strtotime($tempDate));
        $xDate->xdate_org = date('Y-m-d',strtotime($tempDate));
    	$xDate->xdate_txt = date('m/d',strtotime($tempDate));
    	
		$xDate->xcontact = (!empty($xDate->xcontact)) ? $xDate->xcontact : '';
		
    	$lineData = (!empty($xDate->line)) ? $this->customField->where('id',$xDate->line)->get(array('name'))->first() : false;
    	$xDate->line_txt = ($lineData) ? $lineData->name : '';

    	$policyData = (!empty($xDate->policy_type)) ? $this->customField->where('id',$xDate->policy_type)->get(array('name'))->first() : false;
    	$xDate->policy_type_txt = ($policyData) ? $policyData->name : '';

    	$industryData = (!empty($xDate->industry)) ? $this->customField->where('id',$xDate->industry)->get(array('name'))->first() : false;
    	$xDate->industry_txt = ($industryData) ? $industryData->name : '';

    	$userData = (!empty($xDate->producer)) ? User::where('id',$xDate->producer)->get(array('name'))->first() : false;
    	$xDate->producer_txt = ($userData) ? $userData->name : '';
    	
    	$tempDate = $xDate->follow_up_date;
    	$xDate->follow_up_date = date('m/d/Y',strtotime($tempDate));
    	$xDate->follow_up_date_txt = date('m/d/Y',strtotime($tempDate)); 

		//$tempLastNote = $xDate->last_note_datetime;
		//echo $xDate->last_note_datetime;
		
		$tempLastNote = (!empty($xDate->last_note_datetime)) ?  convertTimeToUSERzone($xDate->last_note_datetime,$this->user->choosed_timezone,'m/d/Y').' at '.convertTimeToUSERzone($xDate->last_note_datetime,$this->user->choosed_timezone,'g:i a') :''; 
		$xDate->last_note_txt = (!empty($tempLastNote)) ? $tempLastNote : '' ;
		
		
    	$xAllStatus = $xDate->getAllStatus();
    	$xDate->status_txt  = $xAllStatus[$xDate->status];
    	
    	return $xDate;
    }
	
	
	
    function getAllNotes($xID){
        if(!empty($xID)){
            $xnote = new XdateNote;
            $allXnotes = $xnote->with('user_data')->where('xdate_id',$xID)->orderBy('id', 'DESC')->get()->toArray();
            if(!empty($allXnotes)){
                foreach ($allXnotes as $key => $note) {
                	
                	//$noteDate = (!empty($note['created_at'])) ? convertTimeToUSERzone($note['created_at'],$this->user->choosed_timezone) : '';
                    $allXnotes[$key]['date_txt'] =  convertTimeToUSERzone($note['created_at'],$this->user->choosed_timezone,"m/d/Y"); // date("m/d/Y",strtotime($noteDate));
                    $allXnotes[$key]['time_txt'] =  convertTimeToUSERzone($note['created_at'],$this->user->choosed_timezone,"h:i a");//date("h:i a",strtotime($noteDate));
                    if($note['note_type'] == 1) {
                        $user_name = $this->get_user_name_by_id($note['user_id'],$note['notes']);
                        $xdateData = $this->getXdateData(null,$note['xdate_id']);
                        if(!empty($xdateData)) {
                            $xdate_name = $xdateData['xname'];
                        } 
                        $allXnotes[$key]['notes'] = default_xdate_note_text($xdate_name,$user_name);
                    } else if($note['note_type'] == 2) {
                        $user_data = $this->get_user_name_by_id($note['user_id'],$note['notes']);
                        $xdateData = $this->getXdateData(null,$note['xdate_id']);
                        if(!empty($xdateData)) {
                            $xdate_name = $xdateData['xname'];
                        } 
                        $allXnotes[$key]['notes'] = update_note_text($user_data);

                    } else {
                        $allXnotes[$key]['notes'] =  nl2br($note['notes']); //date("h:i a",strtotime($note['created_at']));
                    }

                    if(!empty($allXnotes[$key]['user_data']['profile_image'])){
                        $allXnotes[$key]['user_data']['profile_image'] = url(config('constants.FILEUPLOAD').$allXnotes[$key]['user_data']['profile_image']);
                    }else{
                        $allXnotes[$key]['user_data']['profile_image'] = url(config('constants.FILEUPLOAD').''.config('constants.DEFAULT_PROFILE'));
                    }
                }
            }
           
            return Response::json(['success' => true,  'notes' => $allXnotes]); 
        }else{
            return Response::json(['error' => 'No notes found!!!'], 403); 
        }
    }

    function insertNote(Request $request){

        $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id;  
        $xDate = new Xdate;
        $xDate = $xDate->where('id',$request->xdate_id)->get()->first();

        $hasAccess = false;

        //preF($this->vdata['curModAccess']);

        if((isset($this->vdata['curModAccess']['edit_notes'])  && $this->vdata['curModAccess']['edit_notes'] == 1)){  
            if($xDate->owner_id == $ownerID){ 
                $hasAccess = true; 
            }else{
                $hasAccess = false;
            }   
        }

        if($hasAccess){
            try{ 
					$xDate->last_note_datetime = date("Y-m-d H:i:s");
					$xDate->save(); 
				
                    $xDateNote = new XdateNote;
                    $xDateNote->xdate_id = $request->xdate_id;
                    $xDateNote->user_id = $this->user->id;
                    $xDateNote->notes = $request->notes;
                    $xDateNote->status = 1;

                    $xDateNote->created_at = date("Y-m-d H:i:s");
                    $xDateNote->updated_at = date("Y-m-d H:i:s");

                    $xDateNote->save(); 
                     
                    $xDateNote->date_txt = convertTimeToUSERzone($xDateNote->created_at,$this->user->choosed_timezone,"m/d/Y");//date("m/d/Y",strtotime($noteDate));
                    $xDateNote->time_txt = convertTimeToUSERzone($xDateNote->created_at,$this->user->choosed_timezone,"h:i a");//date("h:i a",strtotime($noteDate));
                    $xDateNote->user_data = User::where('id',$xDateNote->user_id)->select(array('com_name','id','name','profile_image'))->get()->first();
                    $xDateNote->notes = nl2br($request->notes);

                    if(!empty($xDateNote->user_data->profile_image)){
                        $xDateNote->user_data->profile_image = url(config('constants.FILEUPLOAD').$this->user->profile_image);
                    }else{
                        $xDateNote->user_data->profile_image = url(config('constants.FILEUPLOAD').''.config('constants.DEFAULT_PROFILE'));
                    }

                    return Response::json(['success' => true, 'message' => 'The X-Date Notes has been saved successfully.', 'note' => $xDateNote]);

                }catch(Exception $e){
                   // do task when error
                    return Response::json(['message' => $e->getMessage()], 403); 
                } 
        }else{
            return Response::json(['message' => 'Permission denied.'], 403); 
        } 
        
    }

    /**
    * this function created note when user changed produces of Xdate
    */
    public function change_producer_note($xdate_id,$new_produces,$old_producer)
    {
        if(!empty($new_produces) && !empty($old_producer)) {
            $note_text = array();
            $note_text['old_producer'] = $old_producer;
            $note_text['new_producer'] = $new_produces;
            $notes = json_encode($note_text);
            
            $xDateNote = new XdateNote;
            $xDateNote->xdate_id = $xdate_id;
            $xDateNote->user_id = $this->user->id;
            $xDateNote->notes = $notes;
            $xDateNote->status = 1;
            $xDateNote->note_type = 1;
            $xDateNote->created_at = date("Y-m-d H:i:s");
            $xDateNote->updated_at = date("Y-m-d H:i:s");
            $xDateNote->save();
        }
    }

    /**
    * this function return user name array 
    */
    public function get_user_name_by_id($user_id , $note_users = null)
    {
        $userNames = array();
        if($note_users != null) {
            $note_users = json_decode($note_users , true);
        }
        $note_users['user'] = $user_id;
        foreach($note_users as $key=>$user) {
            $usersData = User::where('id',$user)->get(['name'])->first();
            $userNames[$key] = $usersData['name'];
        }
        return $userNames;
    }

    /**
    * this function send mail to producer
    * @return true on success
    */
    public function send_email($producer_id,$xname) 
    {
        $appMailer = New AppMailer;
        $userData = User::where('id',$producer_id)->get(['name','email'])->first();
        if(!empty($userData)) {
            $appMailer->xdate_assign_mail($userData,$xname);
        }
    }

    /**
    * this function send mail when request for status change
    * @return true on success 
    */ 
    public function status_change_request(Request $request) 
    {
        if($request->ajax()) { 
              $add_note_data = array();
              $user_name = $this->user->name;
              $xID = $request->get('xid');
              if(empty($xID)) {
                    return Response::json(['message' => 'something went wrong.'], 404); 
              }
              $xData = $this->getXdateData(null,$xID);
              $userData = User::where('id',$xData->producer)->get(['email','name'])->first();
              if(empty($userData)) {
                    return Response::json(['message' => 'something went wrong.'], 404); 
              }
              $appMailer = new AppMailer;
              $reqValue = array('user_name'=>$user_name,'x_name'=>$xData->xname,'userData'=>$userData);
              $add_note_data = array("xdate_id"=>$xID,"requester_id"=>$this->user->id,"producer_id"=>$xData->producer);
              if($this->add_status_change_note($add_note_data))  {
                 return Response::json(['message' => 'something went wrong.'], 404); 
              } 

              $appMailer->request_update_mail($reqValue);
              return Response::json(['message' => 'success.'], 200); 
        }
        return Response::json(['message' => 'Permission denied.'], 403); 
    }

    /**
    * create note for  status update in X-Dates
    * @return true on success
    */
    public function add_status_change_note($require_data =array() )
    {
        if(empty($require_data)) {
            return false;
        }
        $note_text = array();
        $note_text['requester_id'] = $require_data['requester_id'];
        $note_text['producer_id'] = $require_data['producer_id'];
        $notes = json_encode($note_text);
            
        $xDateNote = new XdateNote;
        $xDateNote->xdate_id = $require_data['xdate_id'];
        $xDateNote->user_id = $this->user->id;
        $xDateNote->notes = $notes;
        $xDateNote->status = 1;
        $xDateNote->note_type = 2;
        $xDateNote->created_at = date("Y-m-d H:i:s");
        $xDateNote->updated_at = date("Y-m-d H:i:s");
        $xDateNote->save();
    }

}