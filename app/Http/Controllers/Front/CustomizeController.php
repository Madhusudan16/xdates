<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Commons\UserAccess; 
use App\Http\Requests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Hash;
use App\User;
use Validator;
use JsValidator;
use App\UserCustomFields;
use Response;
use DB;
class CustomizeController extends Controller
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
       $this->vdata['page_section_class'] = 'my-account user-setting';
        $this->vdata['page_title'] = 'Customize Fields';


       if(!empty($this->user)){ 

        $this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);  
        $this->vdata['user'] = $this->user; 
        $this->vdata['curModAccess'] = $this->access['current'];
        $this->vdata['allModAccess'] = $this->access['all'];


        $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id; 
        $whereCust = array('owner_id'=>$ownerID,'status'=>1);

        $policy = new UserCustomFields; 
        $this->vdata['lines_policies'] = $policies = $policy->where($whereCust)->where('type', '1')->orderBy('is_permanent','desc')->orderBy('display_order','asc')->orderBy('name','asc')->get();
        $this->vdata['industry_policies'] = $policies = $policy->where($whereCust)->where('type', '2')->orderBy('is_permanent','desc')->orderBy('display_order','asc')->orderBy('name','asc')->get();
        $this->vdata['personal_policies'] = $policies = $policy->where($whereCust)->where('type', '3')->orderBy('is_permanent','desc')->orderBy('display_order','asc')->orderBy('name','asc')->get();
        $this->vdata['commercial_policies'] = $policies = $policy->where($whereCust)->where('type', '4')->orderBy('is_permanent','desc')->orderBy('display_order','asc')->orderBy('name','asc')->get();
       }
    }

	public function insertDefaultCustom(){
		$whereCust = array('parent_user_id'=>0,'status'=>1);
		$User = User::where($whereCust)->where('id','!=',217)->get();
		foreach($User as $udata){
			
			$toInsertData = array();
            $toInsertData[] = array('type'=>2,'name'=>'Multiple Operations','is_permanent'=>1,'display_order'=>1,'owner_id'=>$udata->id,'status'=>1);
			$toInsertData[] = array('type'=>2,'name'=>'Other','is_permanent'=>1,'display_order'=>2,'owner_id'=>$udata->id,'status'=>1);
	        if(!empty($toInsertData)){ 
	           // DB::table('user_customize_fields')->insert($toInsertData);
	        }
		
		}
		
		echo "INNN";
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
        if($checkAccess  && isset($checkAccess) &&  $checkAccess['allow_access'] == 1) {
            return redirect()->to('/');
        }
        if($this->vdata['curModAccess']['view'] != 1){
            return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        return view('front.customize-fileds-setting',$this->vdata);
    }
}