<?php
namespace App\Commons;
 
use DB;
use Session;

class UserAccess{ 
    /**
     * The user type id who has all type of access.
     *
     * @var array
     */
    protected $masterUserType = 1;

    /**
     * Create a new user access instance.
     */
    public function __construct()
    {
      //do intital things
    }

    /**
     * check and return access of action by user type  
     * @param  Module ID $moduleId
	 * @param  User Type $userType
     * @return void
     */
    public function checkModuleAccessByUserType($currentModuleId=0,$userType)
    {
    	if(!empty(Session::get('login_as_customer'))) {
    		$userType = 4;
    	}
    	$modActions  = array(); 
		$modActions['all'] = array();
		$modActions['current'] = array();
		
    	$modData = $this->getAllModuleData();
        $typeData = $this->getUserTypeById($userType);
		
		
		if(!empty($modData)){
			$uTypeAction = $typeData->module_access;
			$uTypeActionData = array();
			if($uTypeAction != ''){
				$uTypeActionData = json_decode($typeData->module_access,true);
			}
			foreach($modData as $module){
				$moduleId =  $module->id; 
				
				$uTypeActionArr = array();
				if(!empty($uTypeActionData)){	
					$uTypeActionArr = (isset($uTypeActionData[$moduleId]))? $uTypeActionData[$moduleId] : array(); 
				}
				$actions = explode(",",$module->actions);
				$actData = array(); //$modActions[$moduleId]
				foreach($actions as $action){
					
					$actData[$action] = false;
					if($this->masterUserType == $userType){
						$actData[$action] = true;	
					}else{
						if(in_array($action, $uTypeActionArr)){
							$actData[$action] = true;		
						}
					}
					  
				} 
				$modActions['all'][$moduleId] = $actData;
				
				if($moduleId == $currentModuleId){
					$modActions['current'] = $actData;

				}

			}
		}else{
			$modActions = array('all'=>true);
		}
		//preF($modActions);
		return $modActions; 
    }
	
	/**
     * get the data from user modules
     *
     * @param  Module ID $moduleId
     * @return void
     */
	public function getAllModuleData(){
		$modData = DB::table('user_modules')->get();
		return $modData;
	}
	
	/**
     * get the data from user modules
     *
     * @param  Module ID $moduleId
     * @return void
     */
	public function getModuleDataById($moduleId){
		$modData = DB::table('user_modules')->where('id', $moduleId)->first();
		return $modData;
	}
	
	/**
     * get the user type data
     *
     * @param  Module ID $moduleId
     * @return void
     */
	public function getUserTypeById($typeId){
		$modData = DB::table('user_types')->where('id', $typeId)->first();
		return $modData;
	}
 
}
