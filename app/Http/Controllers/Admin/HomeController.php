<?php
namespace App\Http\Controllers\Admin;

use App\Commons\AdminUserAccess;
use Illuminate\Routing\Controller;
use App\Models\Admin\AdminFilter;
use Illuminate\Support\Facades\Input;
use App\User;
use Auth;
use Request;
use Response;

class HomeController extends Controller
{   
    
    /**
     * The module id
     */
    protected $moduleId = 1;

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

    /**
    * store client data
    */
    public $clientData = array();

    /**
    *  this variable store user status 
    */
    public $status = array();

    public function __construct(AdminUserAccess $userAccess)
    {
        $this->middleware('admin');
        $this->user = Auth::guard($this->guard)->user();
        $this->status = array('inactive','active','all');
        if(!empty($this->user)){
            $this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);
            $this->vdata['user'] = $this->user;
            $this->vdata['curModAccess'] = $this->access['current'];
            $this->vdata['allModAccess'] = $this->access['all'];
        }
        $this->vdata['page_section_class'] = "admin-home-page admin-xdates";
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pastFilter = array();
        $tab = array('allClients'=>'all','activeClients'=>'active','inactiveClients'=>'inactive');
        
        $activeUser = 1;
        $inactiveUser = 0;
        $this->vdata['page_title'] = 'Home';
        foreach($tab as $key => $tab_name) {
            $filteData = $this->arrangeUserData($tab_name,null,true);
            $pastFilter[$tab_name] =  $filteData;
            $filterData = json_decode($filteData,true);
            $resultData = $this->filterData($filterData,$tab_name);
            $this->vdata[$key] =  $resultData['data'];       
            $this->vdata[$key.'_filter'] =  $resultData['is_apply_filter'];  
        }
        $this->vdata['pastFilter'] = $pastFilter;
        //preF($this->vdata['allClients']);
        //preF($this->vdata);
        $this->vdata['status'] = $this->status;
        return view('admin.index',$this->vdata);
    }

    /**
    * this search user
    *
    * @return match user name ,com_name and email
    */
    public function search()
    {
        
        if(Request::ajax()) {
            $is_email = 0;
            $searchString = Request::get('term');
            $fetch_result_data = array('name','com_name','id','parent_user_id','email');
            $clientData = User::where('name', 'LIKE', "%$searchString%")->Orwhere('com_name', 'LIKE', "%$searchString%")->Orwhere('email', 'LIKE', "%$searchString%")->where('status','<>',2)->get($fetch_result_data);
            /*if(stristr($searchString,"@")) {
                $clientData = $clientData->Orwhere('email', 'LIKE', "%$searchString%");
                $is_email = 1;
                array_push($fetch_result_data,'email');

            }
            $clientData = $clientData->where('status','<>',2)->get($fetch_result_data); */
            $allSearchData = array();
            $clientData = $clientData->toArray();
            foreach($clientData as $client){

                $client['search_type'] = 'user';
                $allSearchData[] = $client;
                if($client['com_name'] != ''){
                    $client['search_type'] = 'company';
                    $allSearchData[] = $client;
                }
                if(stristr($client['email'], $searchString)) {
                    if($client['email'] != '' && !empty($client['email'])) {
                        $client['search_type'] = 'email';
                        $allSearchData[] = $client;
                    }
                }
                //if()
            }
           // preF($allSearchData);
            
            //$clientData = end($clientData);
           /* $clientData = array_values($clientData);
            echo json_encode($clientData);*/
            return Response::json($allSearchData);
        }
    }
    
    /**
    * this function filterdata by given date 
    * @return filterData
    */
    public function filterData($formData = null ,$tab_name = null)
    {   
        $apply_filter = 'no-filter';
/*        $user_required_field = array('users.name','status','created_at','trial_end_date','account_exp');
        $user_required_field = implode("','",$user_required_field);*/
        if(Request::ajax()) {
                    $apply_old_filter = false;
                    $data = Input::get('data');
                    $tab_name = Input::get('tab_name');
                    parse_str($data,$formData);
                    $filterJson = json_encode($formData);
                    $this->arrangeUserData($tab_name,$filterJson);
        } else if(!empty($formData) ) {
            $apply_old_filter = true;
        }
            $status = array_search($tab_name,$this->status);
            if(($status !=  "" && !empty($status)) || $status == 0) {
                if($status == 2 ) {
                    $clients = User::with('card')->where('users.status','<>',2)->where(['users.parent_user_id'=>0])->orderBy('com_name','asc');
                } else {
                    if($status == 1) {
                        $clients = User::with('card')->where('users.status',$status)->where('users.is_expired',0)->where(['users.parent_user_id'=>0])->orderBy('com_name','asc');
                    } else {
                        $clients = User::with('card')->where(['users.parent_user_id'=>0])->where(function($query)
                        {
                            $query->where('users.status',0)->orWhere('users.is_expired',1);
                        })->orderBy('com_name','asc');    
                    }
                    
                }
            }
            $sortedData = array();
            if(!empty($formData) && $formData != null && is_array($formData)) {
                if(isset($formData['sign_up_quick']) && !empty($formData['sign_up_quick'])) {
                    $apply_filter = 'filter';
                    $CurrentDate=date_create(date("Y-m"));
                    $year = date('Y'); 
                    $searchDate=date_create(date('Y-m',strtotime("$year-$formData[sign_up_quick]")));
                    $diff=date_diff($CurrentDate,$searchDate);
                    if($diff->format("%R%a") > 0) {
                        $signUpYear = date('Y', strtotime('-1 year'));
                    } else {
                        $signUpYear = date('Y');
                    }
                    $signUpMonth = $formData['sign_up_quick'];
                    $clients = $clients->whereYear('users.created_at','=',$signUpYear)->whereMonth('users.created_at','=',$signUpMonth);
                } else if(!empty($formData['sign_up_from']) && !empty($formData['signup_to'])) {
                    $apply_filter = 'filter';
                    $signUpFrom = date('Y-m-d', strtotime(str_replace('-','/', $formData['sign_up_from'])));
                    $signUpTo = date('Y-m-d', strtotime(str_replace('-','/', $formData['signup_to'])));
                    $clients = $clients->whereDate('users.created_at','>=',$signUpFrom)->whereDate('users.created_at','<=',$signUpTo);
                   
                }
                if(isset($formData['trail_quick']) && !empty($formData['trail_quick'])) {
                    $apply_filter = 'filter';
                    $CurrentDate=date_create(date("Y-m"));
                    $year = date('Y'); 
                    $searchDate=date_create(date('Y-m',strtotime("$year-$formData[trail_quick]")));
                    $diff=date_diff($CurrentDate,$searchDate);
                    if($diff->format("%R%a") < 0) {
                        $trialYear = date('Y', strtotime('+1 year'));
                    } else {
                        $trialYear = date('Y');
                    }
                    $trialMonth = $formData['trail_quick'];
                    $clients = $clients->whereYear('trial_end_date','=',$trialYear)->whereMonth('trial_end_date','=',$trialMonth);
                    
                } else if(!empty($formData['trial_from']) && !empty($formData['trial_to'])) {
                    $apply_filter = 'filter';
                    $trialFrom = date('Y-m-d', strtotime(str_replace('-','/', $formData['trial_from'])));
                    $trialTo = date('Y-m-d', strtotime(str_replace('-','/', $formData['trial_to'])));
                    $clients = $clients->whereDate('trial_end_date','>=',$trialFrom)->whereDate('trial_end_date','<=',$trialTo);
                }
                if(isset($formData['account_quick']) && !empty($formData['account_quick'])) {
                    $apply_filter = 'filter';
                    $CurrentDate=date_create(date("Y-m"));
                    $year = date('Y'); 
                    $searchDate=date_create(date('Y-m',strtotime("$year-$formData[account_quick]")));
                    $diff=date_diff($CurrentDate,$searchDate);
                    if($diff->format("%R%a") > 0) {
                        $accountYear = date('Y', strtotime('-1 year'));
                    } else {
                        $accountYear = date('Y');
                    }
                    $accountMonth = $formData['account_quick'];
                    
                    $clients = $clients->whereYear('account_exp','=',$accountYear)->whereMonth('account_exp','=',$accountMonth);
                } else if(!empty($formData['account_from']) && !empty($formData['account_to'])) {
                    $apply_filter = 'filter';
                    $accountFrom = date('Y-m-d', strtotime(str_replace('-','/', $formData['account_from'])));
                    $accountTo = date('Y-m-d', strtotime(str_replace('-','/', $formData['account_to'])));
                    $clients = $clients->whereDate('account_exp','>=',$accountFrom)->whereDate('account_exp','<=',$accountTo);
                }
                if(isset($formData['credit_quick']) && !empty($formData['credit_quick'])) {
                    $apply_filter = 'filter';
                    $CurrentDate=date_create(date("Y-m"));
                    $year = date('Y'); 
                    $searchDate=date_create(date('Y-m',strtotime("$year-$formData[credit_quick]")));
                    $diff=date_diff($CurrentDate,$searchDate); 
                   // echo $diff->format("%R%a days");
                    if($diff->format("%R%a") < 0) {
                        $creditToYear = date('Y', strtotime('+1 year'));
                    } else {
                        $creditToYear = date('Y');
                    }
                    $creditFrom = date("$creditToYear-$formData[credit_quick]");
                    $clients->join('user_credit_card', 'users.id', '=', 'user_credit_card.user_id'); 
                    $clients = $clients = $clients->where('expiry_date','=',$creditFrom);
                } else if(!empty($formData['credit_from']) && !empty($formData['credit_to'])) {
                   $apply_filter = 'filter';
                    $creditFrom = date('Y-m', strtotime(str_replace('-','/', $formData['credit_from'])));
                    $creditTo = date('Y-m', strtotime(str_replace('-','/', $formData['credit_to'])));
                    $clients->join('user_credit_card', 'users.id', '=', 'user_credit_card.user_id');       
                    $clients = $clients->where('expiry_date','>=',$creditFrom)->where('expiry_date','<=',$creditTo);
                
                }
            }
          
            if((!empty($formData['credit_from']) && !empty($formData['credit_to'])) || (isset($formData['credit_quick']) && !empty($formData['credit_quick']))) {

                $clientsData = $clients->get(['users.name','users.status','users.created_at','users.trial_end_date','users.account_exp','users.com_name','users.is_expired','user_credit_card.expiry_date','user_credit_card.card_no']);
            } else {

                 $clientsData = $clients->get();
            }
            $clientData = array();
           
                foreach($clientsData as $key=>$client) {
                    if(isset($client->card) && !empty($client->card)) {
                        $client->card->expiry_date =  isset($client->card->expiry_date)? date("m/y",strtotime($client->card->expiry_date)) : '';
                    } 
                    if(isset($client->expiry_date) && !empty($client->expiry_date)) {
                        $client->expiry_date =  isset($client->expiry_date)? date("m/y",strtotime($client->expiry_date)) : '';
                    }
                    $createDate = date("m/d/Y",strtotime($client->created_at));
                    if(!empty($client->trial_end_date) && $client->trial_end_date != "0000-00-00" ) {
                        $client->trial_end_date =  date("m/d/Y",strtotime($client->trial_end_date));
                    } else if($client->trial_end_date == "0000-00-00") {
                        $client->trial_end_date = "";
                    }
                    $client->createDate = $createDate;
                    $client->status = ($client->is_expired == 1) ? 0 : $client->status; 
                    $client->account_exp = !empty($client->account_exp)?date("m/d/Y",strtotime($client->account_exp)):'';
                    if($tab_name == 'all') {
                        $sortedData['allData'][] = $client;
                    } else if($client->status == 1 && $tab_name == 'active') {
                        $sortedData['active'][] = $client;
                    } if($client->status ==  0  && $tab_name == 'inactive') {
                        $sortedData['inactive'][] = $client;
                    }
                    $clientData[] = $client;

                }
                if(isset($apply_old_filter) && $apply_old_filter == false) {
                    $jsonFilter = json_encode($formData);
                    return Response::json(['filterData' => $sortedData,'jsonFilter'=>$jsonFilter], 200);
                } else {
                    $filter['data'] = $clientData;
                    $filter['is_apply_filter'] = $apply_filter;
                    return $filter;
                }
        
    }
    
    /**
    * this function arrange previous filter data 
    *
    * @return filter Data
    */
    public function arrangeUserData( $tab_name = null,$filterJson = null , $is_print = false)
    {
        $currentUser = $this->user->id;
        if($tab_name == null ) {
            $tab_name = "all";
        }
    
        $searchUserFilterData =  AdminFilter::where('tab_name',$tab_name)->where('user_id',$currentUser)->get(['tab_name','filter_obj'])->first();

        if(isset($searchUserFilterData) && $searchUserFilterData->count() != 0 &&  !empty($searchUserFilterData)) {
            
             if($is_print) {
                return $searchUserFilterData->filter_obj;
             } else {
                $updatedData['filter_obj'] = $filterJson;
                AdminFilter::where('tab_name',$tab_name)->where('user_id',$currentUser)->update($updatedData);
                return true;
             }
        } else {
            if($is_print == false) {
                $filterJson = $filterJson;
                $createData = array('user_id'=>$currentUser,'filter_obj'=>$filterJson,'tab_name'=>$tab_name);
                AdminFilter::create($createData);
            }
            return true;
        }
    }
}
