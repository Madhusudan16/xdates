<?php
namespace App\Http\Controllers\Admin;

use Auth;
use App\Commons\AdminUserAccess;
use App\User;
use Illuminate\Routing\Controller;
use Request;
use Illuminate\Support\Facades\Input;
use Response;
use Validator;
use JsValidator;
use App\Models\Admin\ExtendTrial;
use App\Commons\AppMailer;
use App\Models\Admin\Note;
use Session;

class TrialExtendController extends Controller
{   
     
    /**
     * The module id
     */
    protected $moduleId = 7;

	/**
     * The guard name
     */
    protected $guard = 'admin';

	/**
     * view data
     */
    protected $vdata = array();

    public function __construct(AdminUserAccess $userAccess)
    {

        $pos = strstr(Request::path(), "trial-extend") ;
        if($pos) {
            Session::put('extend_url','/admin/trial-extend');
        }
        $this->middleware('admin');
		$this->user = Auth::guard($this->guard)->user();
        if(!empty($this->user)){
            Session::forget('extend_url');
			$this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);
			$this->vdata['user'] = $this->user;
			$this->vdata['curModAccess'] = $this->access['current'];
			$this->vdata['allModAccess'] = $this->access['all'];
        }
        $this->vdata['page_section_class'] = "user-setting";
    }	
    
    /**
    * this function display trial_extend page 
    * @return page view with data
    */
    public function index()
    {
        if($this->vdata['curModAccess']['view'] != 1){
            return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        $this->vdata['trialList'] = $this->get_trial_extend_request_data();
        return view('admin/trial-extend',$this->vdata);
    }

    /**
    * get trial extend list 
    */
    public function get_trial_extend_request_data($id = null)
    {
        $where = array('is_approved'=>0);
        if($id != null) {
            $where['user_id'] = $id;
            $trial_list = ExtendTrial::with('get_user','get_requester_user')->where($where)->get()->first();
        } else {
            $trial_list = ExtendTrial::with('get_user','get_requester_user')->where($where)->get();
        }
        //preF($trial_list)
        return $trial_list;
    }
    
        /**
    * approved trial 
    *
    * @return true on success 
    */
    public function approveTrial()
    {
        if(Request::ajax()) {
            $id = Request::get('id');
            $note = Request::get('reason');
            $is_edit = Request::get('is_edit');
            if($id != '' && $id && !intval($id)) {
                return Response::json(['error'=>'something went wrong.'],404);
            }
            $userData = $this->get_user($id);
            if(empty($userData)) {
                return Response::json(['error'=>'something went wrong.'],404);
            }
            $trialRecord = $this->get_trial_extend_request_data($id);
            if(empty($trialRecord)) {
                return Response::json(['error'=>'something went wrong.'],404);
            }
            if($is_edit == 0) {

                $userData->trial_start_date = date("Y-m-d");
                $userData->trial_end_date = date('Y-m-d',strtotime("+30 days"));
                /*if(find_days_diff($userData->trial_end_date) > 0) {
                    $userData->trial_end_date = date('Y-m-d',strtotime("+30 days"));
                } else {
                    $userData->trial_end_date = date('Y-m-d',strtotime($userData->trial_end_date." +30 days"));
                }*/
                $userData->is_expired = 0;
                $userData->status = 1;
                $userData->account_exp = null;
                $userData->save();
                $trialRecord->is_approved = 1;
                $note = "";
                $mailer = new AppMailer;
                $mailer->informClientAboutTrial($userData->email);

            } else {
                $trialRecord->is_approved = 2;
            }
            $trialRecord->action_by = $this->user->id;
            $trialRecord->save();
            $noteData = array('requester_id'=>$trialRecord->requester_id,'action_by'=>$this->user->id,'user_id'=>$id,'detail'=>$note,'note_type'=>0,'is_approved'=>$trialRecord->is_approved);
            $this->add_note($noteData);
            //return Response::json(['success'=>'request accepted'],200);
            //return redirect('/admin')->with(['approvedTrial'=>$trialRecord->is_approved]);
        }
    }

    /**
    * get user details 
    * @return user detail
    */
    public function get_user($id = null) 
    {
        $where = array();
        if($id != null) {
            $where['id'] = $id;
            $userData = User::where('status','<>',2)->where($where)->get()->first();
        } else {
            $userData = User::where('status','<>',2)->get();
        }
        return $userData;
    }

    /**
    * this function add note 
    * @return true on success 
    */
    public function add_note($noteData)
    {
        if(empty($noteData)) {
            return false;
        } 
        Note::create($noteData);
        return true;
    }
}