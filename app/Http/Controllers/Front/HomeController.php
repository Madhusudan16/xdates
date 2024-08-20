<?php
namespace App\Http\Controllers\Front;

use Auth;
use App\Commons\UserAccess; 
 
use Illuminate\Routing\Controller; 

use App\Models\Front\Xdate;
use App\Models\Front\UserXdateFilter;
use App\UserCustomFields;
use App\User;
use App\Models\Front\State;
use Session;
use Validator;
use JsValidator;
use Illuminate\Http\Request;
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
		} 

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->vdata['popUpData'] = acoount_expire_text($this->user);
        $this->vdata['check_access'] = $this->vdata['popUpData']['allow_access'];
        if($page_url = prevent_user($this->user)) {
           return redirect($page_url);
        }
        
        $xdate = new Xdate;

        $userMod = new User;
        $stateMod = new State;

        $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id; 
        $whereCust = array('owner_id'=>$ownerID,'status'=>1);
        $policy = new UserCustomFields; 
        $this->vdata['linesList'] = $policy->where($whereCust)->where('type', '1')->get();
        $this->vdata['industryList'] = $policy->where($whereCust)->where('type', '2')->get();
        $this->vdata['policyList'] = $policy->where($whereCust)->where('type', '3')->get();
        $this->vdata['commercialList'] = $policy->where($whereCust)->where('type', '4')->get();
        
        $this->vdata['allStatus']  = $xdate->getAllStatus();
        $this->vdata['producers']  = $userMod->where(function ($query) use ($ownerID){
                                        $query->where('parent_user_id',$ownerID)->Orwhere('id',$ownerID);
                                     })->where(function ($query){
                                        $query->where('status',1);
                                    })->orderBy('name','asc')->get();

        $this->vdata['page_title'] = "Home";
        $this->vdata['stateList'] =  $stateMod->where('country_id',1)->get();
        $this->vdata['is_expired'] = $this->user->is_expired;
        /*if($this->vdata['is_expired'] == 1) {
            $this->vdata['popup_data'] = acoount_expire_text($this->user);
        }*/
        $this->vdata['page_section_class'] = 'add_home';
        //Session::put('is_uncomplete_profile','');

        //check if user has filled up the information or not
        if($this->user->parent_user_id < 1 && $this->user->com_name == '') { 
            $this->vdata['askTofillMisData'] = true; 
        } 
        $commonWhere = array('owner_id'=>$ownerID);
        $locData = $xdate->distinct()->select(array('city','state'))->where($commonWhere)->get();
        $this->vdata['allLocations'] = $locData; 

        $withData = array('line_data','policy_type_data','industry_data','producer_data'); 

        //$xDateData = $xdate->with($withData)->where($commonWhere)->get();
        //$allData = $this->arrangeXdateData($xDateData);

        $filters = array();
        $userFilter = new UserXdateFilter; 

        $userFiltersAll = $userFilter->where('user_id',$this->user->id)->where('tab_name','all')->get()->first();

        if(!empty($userFiltersAll) && $userFiltersAll->filter_obj != ''){

            $jsonObj = json_decode($userFiltersAll->filter_obj);
            if($jsonObj->f_policy_type != ''){
                $filters['all'] = $userFiltersAll->filter_obj;
                $this->vdata['xall'] = $this->filterData($request,json_decode($userFiltersAll->filter_obj),true) ;
            }else{ 
                $this->vdata['xall'] =  $this->arrangeXdateData($xdate->with($withData)->where($commonWhere)->orderBy('xname','asc')->get(),true); 
            }
        }else{ 
            $this->vdata['xall'] =  $this->arrangeXdateData($xdate->with($withData)->where($commonWhere)->orderBy('xname','asc')->get(),true); 
        }

        //$currentTimeStamp = strtotime(date("Y-m-d"));

        $commonWhere['status'] = 0;
        $userFiltersAll = $userFilter->where('user_id',$this->user->id)->where('tab_name','live')->get()->first();

        if(!empty($userFiltersAll) && $userFiltersAll->filter_obj != ''){

            $jsonObj = json_decode($userFiltersAll->filter_obj);
            if($jsonObj->f_policy_type != ''){
                $filters['live'] = $userFiltersAll->filter_obj;
                $this->vdata['xlive'] = $this->filterData($request,json_decode($userFiltersAll->filter_obj),true) ;
            }else{ 
                $this->vdata['xlive'] =  $this->arrangeXdateData($xdate->with($withData)->where($commonWhere)->orderByRaw('(case when DATEDIFF(follow_up_date, NOW()) < 0 then 1 else 0 end), abs(DATEDIFF(follow_up_date, NOW())) asc ')->get(),true); 
            }
        }else{ 
            $this->vdata['xlive'] =  $this->arrangeXdateData($xdate->with($withData)->where($commonWhere)->orderByRaw('(case when DATEDIFF(follow_up_date, NOW()) < 0 then 1 else 0 end), abs(DATEDIFF(follow_up_date, NOW())) asc ')->get(),true); 
        }

        $commonWhere['status'] = 1;
        $userFiltersAll = $userFilter->where('user_id',$this->user->id)->where('tab_name','converted')->get()->first();

        if(!empty($userFiltersAll) && $userFiltersAll->filter_obj != ''){

            $jsonObj = json_decode($userFiltersAll->filter_obj);
            if($jsonObj->f_policy_type != ''){
                $filters['converted'] = $userFiltersAll->filter_obj;
                $this->vdata['xconverted'] = $this->filterData($request,json_decode($userFiltersAll->filter_obj),true) ;
            }else{ 
                $this->vdata['xconverted'] =  $this->arrangeXdateData($xdate->with($withData)->where($commonWhere)->orderByRaw('(case when DATEDIFF(follow_up_date, NOW()) < 0 then 1 else 0 end), abs(DATEDIFF(follow_up_date, NOW())) asc ')->get(),true); 
            }
        }else{ 
            $this->vdata['xconverted'] =  $this->arrangeXdateData($xdate->with($withData)->where($commonWhere)->orderByRaw('(case when DATEDIFF(follow_up_date, NOW()) < 0 then 1 else 0 end), abs(DATEDIFF(follow_up_date, NOW())) asc ')->get(),true); 
        }

        $commonWhere['status'] = 2;
        $userFiltersAll = $userFilter->where('user_id',$this->user->id)->where('tab_name','dead')->get()->first();


        if(!empty($userFiltersAll) && $userFiltersAll->filter_obj != ''){

            $jsonObj = json_decode($userFiltersAll->filter_obj);
            if($jsonObj->f_policy_type != ''){
                $filters['dead'] = $userFiltersAll->filter_obj;
                $this->vdata['xdead'] = $this->filterData($request,json_decode($userFiltersAll->filter_obj),true) ;
            }else{ 
                $this->vdata['xdead'] = $this->arrangeXdateData($xdate->with($withData)->where($commonWhere)->orderByRaw(' (case when DATEDIFF(follow_up_date, NOW()) < 0 then 1 else 0 end), abs(DATEDIFF(follow_up_date, NOW())) asc ')->get(),true); 
            }
        }else{ 
            $this->vdata['xdead'] =  $this->arrangeXdateData($xdate->with($withData)->where($commonWhere)->orderByRaw('(case when DATEDIFF(follow_up_date, NOW()) < 0 then 1 else 0 end), abs(DATEDIFF(follow_up_date, NOW())) asc  ')->get(),true); 
        }

        $this->vdata['filters'] = $filters;
        
        $this->vdata['isXdatePage'] = true;
        //preF($allData);
    	//print_r($this->access);    
        return view('front.index',$this->vdata);
    }
	
	function filterData(Request $request,$filterObj= null,$returnObj=false){
		$ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id; 
        $whereCust = array('owner_id'=>$ownerID,'status'=>1); 
		$withData = array('line_data','policy_type_data','industry_data','producer_data');
		
		$policy = new UserCustomFields;
		$xdateObj = new Xdate;
		$xdates = $xdateObj->with($withData);

        if(empty($filterObj)){
            $filterObj = (object) array();
            $filterObj->f_policy_type = $request->f_policy_type;
            $filterObj->f_industry = $request->f_industry;
            $filterObj->f_location = $request->f_location;
            $filterObj->f_producer = $request->f_producer;
            $filterObj->f_x_from_date = $request->f_x_from_date;
            $filterObj->f_x_to_date = $request->f_x_to_date;
            $filterObj->f_x_quick_date = $request->f_x_quick_date;
            $filterObj->f_f_from_date = $request->f_f_from_date;
            $filterObj->f_f_to_date = $request->f_f_to_date;
            $filterObj->f_f_quick_date = $request->f_f_quick_date; 
            $filterObj->c_tab = $request->c_tab;  
        }
		 
		if(!empty($filterObj->f_policy_type) && $filterObj->f_policy_type != "-1"){
			if($filterObj->f_policy_type == "-2"){
				$line = $policy->select('id')->where($whereCust)->where('type', '1')->whereRaw('LOWER(name) = ?', ['personal'])->get()->first();
				$xdates = $xdates->where(function ($query) use ($filterObj,$ownerID,$whereCust,$line){
	                    $query->where('line',$line->id);
	            });
			}else if($filterObj->f_policy_type == "-3"){
				$line = $policy->select('id')->where($whereCust)->where('type', '1')->whereRaw('LOWER(name) = ?', ['commercial'])->get()->first();
				$xdates = $xdates->where(function ($query) use ($filterObj,$ownerID,$whereCust,$line){
	                    $query->where('line',$line->id);
	            });
			}else{
				$xdates = $xdates->where(function ($query) use ($filterObj,$ownerID,$whereCust){
	                    $query->where('policy_type',$filterObj->f_policy_type);
	            });
			}
		}
		
		if(!empty($filterObj->f_industry) && $filterObj->f_industry != "-1"){ 
			$xdates = $xdates->where(function ($query) use ($filterObj,$ownerID,$whereCust){
                    $query->where('industry',$filterObj->f_industry);
            }); 
		}

		if(!empty($filterObj->f_location) && $filterObj->f_location != "-1"){
			$splitLoc = explode(",", $filterObj->f_location);
			if(!empty($splitLoc[0])){
				$xdates = $xdates->where(function ($query) use ($filterObj,$ownerID,$whereCust,$splitLoc){
	                    $query->where('city',$splitLoc['0']);
	            });
			}
			if(!empty($splitLoc[1])){
				$xdates = $xdates->where(function ($query) use ($filterObj,$ownerID,$whereCust,$splitLoc){
	                    $query->where('state',$splitLoc['1']);
	            });
			}   
		}
		
		if(!empty($filterObj->f_producer) && $filterObj->f_producer != "-1"){ 
			$xdates = $xdates->where(function ($query) use ($filterObj,$ownerID,$whereCust){
                    $query->where('producer',$filterObj->f_producer);
            }); 
		}else{
			$xdates = $xdates->where(function ($query) use ($filterObj,$ownerID,$whereCust){
                    $query->where('owner_id',$ownerID);
            }); 
		}
		if(!empty($filterObj->f_x_from_date) || !empty($filterObj->f_x_to_date)){
			if(!empty($filterObj->f_x_from_date)){
				$frmDate =  explode("/", $filterObj->f_x_from_date); 
				$xdates = $xdates->whereMonth('xdate','>=',$frmDate[0]);
				$xdates = $xdates->whereDay('xdate','>=',$frmDate[1]); 
			}
			
			if(!empty($filterObj->f_x_to_date)){
				$toDate =  explode("/", $filterObj->f_x_to_date); 
				$xdates = $xdates->whereMonth('xdate','<=',$toDate[0]);
				$xdates = $xdates->whereDay('xdate','<=',$toDate[1]); 
			}
		}else if(!empty($filterObj->f_x_quick_date)){ 
			$xdates = $xdates->whereMonth('xdate','=',$filterObj->f_x_quick_date);
		}
		
		if(!empty($filterObj->f_f_from_date) || !empty($filterObj->f_f_to_date)){
			if(!empty($filterObj->f_f_from_date)){
				$frmDate =  date("Y-m-d", strtotime($filterObj->f_f_from_date)); 
				$xdates = $xdates->where('follow_up_date','>=',$frmDate);
			}
 			if(!empty($filterObj->f_f_to_date)){
				$toDate =  date("Y-m-d", strtotime($filterObj->f_f_to_date)); //explode("/", $filterObj->f_f_from_date); 
                $xdates = $xdates->where('follow_up_date','<=',$toDate); 
			}
		}else if(!empty($filterObj->f_f_quick_date)){ 
            $currentMonth  = date("m");
            $quickMonth = $filterObj->f_f_quick_date;

            $year = date("Y");
            if($quickMonth < $currentMonth){
                $year = $year + 1;
            }
			$xdates = $xdates->whereMonth('follow_up_date','=',$filterObj->f_f_quick_date)->whereYear('follow_up_date','=',$year);
		} 
		 		 
		if(!empty($filterObj->c_tab) || $filterObj->c_tab != 'all'){
			if($filterObj->c_tab == 'live'){
				$xdates = $xdates->where('status','=','0');	
			}else if($filterObj->c_tab == 'converted'){
				$xdates = $xdates->where('status','=','1');
			}else if($filterObj->c_tab == 'dead'){
				$xdates = $xdates->where('status','=','2');
			} 
		}

        if(!empty($filterObj->c_tab)){
            if($filterObj->c_tab == 'all'){ 
                $xdates = $xdates->orderBy('xname','asc'); 
            }else if($filterObj->c_tab == 'live'){
                $xdates = $xdates->orderByRaw(' (case when DATEDIFF(follow_up_date, NOW()) < 0 then 1 else 0 end), abs(DATEDIFF(follow_up_date, NOW())) asc '); 
            }else if($filterObj->c_tab == 'converted'){
                $xdates = $xdates->orderByRaw(' (case when DATEDIFF(follow_up_date, NOW()) < 0 then 1 else 0 end), abs(DATEDIFF(follow_up_date, NOW())) asc ');
            }else if($filterObj->c_tab == 'dead'){
                $xdates = $xdates->orderByRaw(' (case when DATEDIFF(follow_up_date, NOW()) < 0 then 1 else 0 end), abs(DATEDIFF(follow_up_date, NOW())) asc  ');
            } 
        }
        
        $this->saveUserFilter($filterObj);

		$xdates = $xdates->get();			  
		$allData = $this->arrangeXdateData($xdates,true);
        if($returnObj){ 
		  return $allData; 		  
        }else{
            return Response::json(['success' => true,  'xdates' => $allData]);        
        }
	}

    function saveUserFilter($filterObj){
      $userID = $this->user->id;
      $userFilter = new UserXdateFilter;
      if(!empty($filterObj)){

         $tempUserFilter = $userFilter->where('user_id',$userID)->where('tab_name',$filterObj->c_tab)->get()->first();
         if(!empty($tempUserFilter)){
            $userFilter = $tempUserFilter;
         }
         $userFilter->user_id = $userID;
         $userFilter->tab_name = $filterObj->c_tab;
         $userFilter->filter_obj = json_encode($filterObj);
         $userFilter->save();
      } 
    }

    public function arrangeXdateData($xData,$onlyAll = false){
        $data = array();

        if($onlyAll == false){ 
            $data['all'] = array();$data['live'] = array();$data['converted'] = array();$data['dead'] = array();
        }

        $xdate = new Xdate;
        $xAllStatus = $xdate->getAllStatus();

        foreach($xData as $tmpData){

            $tempDate = $tmpData->xdate;
            $tmpData->xdate = date('m/d/Y',strtotime($tempDate));
            $tmpData->xdate_org = date('Y-m-d',strtotime($tempDate));
            $tmpData->xdate_txt = date('m/d',strtotime($tempDate));

            
			$tmpData->xcontact = (!empty($tmpData->xcontact)) ? $tmpData->xcontact : '';
			
            $tmpData->line_txt = ($tmpData->line_data) ? $tmpData->line_data->name : ''; 
            $tmpData->policy_type_txt = ($tmpData->policy_type_data) ? $tmpData->policy_type_data->name : ''; 
            $tmpData->industry_txt = ($tmpData->industry_data) ? $tmpData->industry_data->name : '';  
            $tmpData->producer_txt = ($tmpData->producer_data) ? $tmpData->producer_data->name : '';  
            
            $tempDate = $tmpData->follow_up_date;
            $tmpData->follow_up_date = date('m/d/Y',strtotime($tempDate));
            $tmpData->follow_up_date_txt = date('m/d/Y',strtotime($tempDate)); 
			 
			$tempLastNote = $tmpData->last_note_datetime;
            if(!empty($tempLastNote)){
            	//echo $this->user->choosed_timezone;
				
            	$tempLastNote = (!empty($tempLastNote)) ?  convertTimeToUSERzone($tempLastNote,$this->user->choosed_timezone,'m/d/Y').' at '.convertTimeToUSERzone($tempLastNote,$this->user->choosed_timezone,'g:i a') :''; 
				$tmpData->last_note_txt = (!empty($tempLastNote)) ? $tempLastNote : '' ;
		
                //$tmpData->last_note_txt = date('m/d/Y',strtotime($tempLastNote)).' at '.date('g:i a',strtotime($tempLastNote));
            }else{
                $tmpData->last_note_txt = "";
            }  
            $tmpData->status_txt  = $xAllStatus[$tmpData->status]; 
			 
            unset($tmpData->policy_type_data);
            unset($tmpData->industry_data);
            unset($tmpData->line_data);
            //unset($tmpData->user_id);
            unset($tmpData->owner_id);

            if($onlyAll == true){ 
                $data[] = $tmpData;
            }else{
                 $data['all'][] = $tmpData;
                if($tmpData->status == '0'){
                    $data['live'][] = $tmpData;
                }else if($tmpData->status == '1'){
                    $data['converted'][] = $tmpData;
                }else if($tmpData->status == '2'){
                    $data['dead'][] = $tmpData;
                }
            }
 
        } 

        return $data; 
    }
	
    /**
    * this search user
    *
    * @return match user name
    */
    public function search(Request $request)
    {
        
        if($request->ajax()) {
            $searchString = $request->get('term');

            $xdate = new Xdate;

            $ownerID = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id : $this->user->id; 

            $withData = array('line_data','policy_type_data','industry_data','producer_data');
            $commonWhere = array('owner_id'=>$ownerID,);
 
           
            $xDateData = $xdate->with($withData)->where(function ($query) use ($searchString){
                $query->where('xname', 'LIKE', "%$searchString%")->Orwhere('xcontact', 'LIKE', "%$searchString%");
                        })->where(function ($query) use($commonWhere) {
                            $query->where($commonWhere);
                        })->orderBy('xname','asc')->orderBy('xcontact','asc')->get();

            
           // preF( $xDateData);

            $allSearchData = array(); 
            foreach($xDateData as $tempdata){
                //preF($client); 
                $allSearchData[] = $tempdata; 
                 
            } 


            $allData = $this->arrangeXdateData($allSearchData,true);
          
            //$clientData = end($clientData);
           /* $clientData = array_values($clientData); */
           // $allData =  json_encode($allData);
            return Response::json($allData);
        }
    }


    /**
    * update User Data
    */
    public function updateUserData(Request $request){
         
        if(isset($this->vdata['curModAccess']['add']) && $this->vdata['curModAccess']['add'] == 1) { 
            if($request->ajax()){ 
                    $data = $request->all();

                    $user = new User;
                    $user = $user->where('id', $this->user->id)->get()->first();
                    $user->com_name =$data['com_name']; 
                    $user->save();
                    return Response::json(['message' => 'Data successfully Saved.'], 200); // Status code here        

            }else{
                return response()->json(['error' => 'Oops.. Permission Denied.'],403);
            }
        } else {
            return response()->json(['error' => 'Oops.. Permission Denied.'],403);
        }

    }

    
}
