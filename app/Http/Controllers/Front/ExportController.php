<?php
namespace App\Http\Controllers\Front;

use App;
use Auth;
use App\User;
use Validator;
use JsValidator;
use Request;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Routing\Controller;
use App\Commons\UserAccess;
use Redirect;
use App\Models\Front\Xdate;
use App\Models\Front\XdateNote;
use App\Models\Front\Export;

class ExportController extends Controller
{
    /**
     * The module id
     */
    protected $moduleId = 8;

    /**
     * The guard name
     */
    protected $guard = 'web';

    /**
     * view data
     */
    protected $vdata = array();

    /**
    * set status of exported data
    */ 
    public $export_status  = array();

    /**
    * set file format
    */
    public $file_format = array();

    public function __construct(UserAccess $userAccess)
    {
       $this->middleware('auth');
       $this->user = Auth::guard($this->guard)->user();
       $this->userObj = new User();
       if(!empty($this->user)) {
            $this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);
            $this->vdata['user'] = $this->user;
            $this->vdata['curModAccess'] = $this->access['current'];
            $this->vdata['allModAccess'] = $this->access['all'];
            $this->vdata['page_section_class'] = 'top-padding-10 export';
            $this->vdata['page_title'] = 'export data';
        }
        $this->export_status = array(1=>'Xdate','Note');
        $this->file_format = array(1=>'CSV','EXCEL');
    }

    /**
    * call export.blade.php file
    *
    * @return view export.blade.php
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
        if(!check_user($this->user->parent_user_id)) {
            Auth::logout();
        }
        $this->vdata['export_sub_page'] = 'hide';
        if($this->vdata['curModAccess']['view'] != 1){
           return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
       return view('front.export',$this->vdata);
    }

    /**
    * call export.blade.php file
    *
    * @return view export.blade.php
    */
    public function export_sub_page($id = null)
    {
        if($id != null) {
            $exportedData = $this->get_exported_list($id);
            $exportedData = end($exportedData);
            return $this->download($exportedData['file_name']);
        }
        $this->vdata['export_page'] = 'hide';
        if($this->vdata['curModAccess']['view'] != 1){
           return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        $this->vdata['export_status'] = $this->export_status;
        $this->vdata['file_format'] = $this->file_format;
        $this->vdata['exports'] = $this->get_exported_list();
        return view('front.export',$this->vdata);
    }

    /**
    * create csv file
    * @return true on success 
    */
    public function request_csv()
    {
        if(isset($this->vdata['curModAccess']['export']) && $this->vdata['curModAccess']['export'] == 1){
            if(Request::ajax()) {
                $type = Request::get('type');
                if(!empty($type)) {

                    $typesArray = explode(',', $type);
                    $getData = $this->getData($typesArray);
                    $exportedList = $this->get_exported_list();
                    return Response::json(['resultData'=>$exportedList],200);
                }
            }
        } else {
            return Response::json(['msg'=>'access denied'],403);
        }  
    }

    /**
    * this function get data of given type
    * @return data
    */
    public function getData($types = null) 
    {
        if($types != null) {
            $allData = array();
            foreach($types as  $type) {
                $owner_id = ($this->user->parent_user_id != 0) ? $this->user->parent_user_id : $this->user->id; 
                if($type == 'notes') {
                    $xnote = new XdateNote;
                    $allXnotes = $xnote->with('user_data','xdate_data')->where('notes','<>','')->orderBy('id', 'DESC');
                    $allXnotes->join('xdates', 'xdates.id', '=', 'xdate_notes.xdate_id'); 
                    $allData['notes'] = $allXnotes->where('owner_id',$owner_id)->get(['xdate_notes.*','xdates.owner_id'])->toArray();
                   
                } else if($type == 'xdates') {
                    $allData['xdates'] = Xdate::with('line_data','policy_type_data','industry_data','producer_data')->where('owner_id',$owner_id)->orderBy('xname','asc')->get()->toArray();
                    
                }
            }
           // preF($allData);
            $this->set_data($allData);
        }

    }

    /**
    * this function generate csv file
    * @return true on success
    */
    public function generate_csv($file_name,$csvData)
    {
        if(!empty($file_name) && !empty($csvData)) {
            $file_path =storage_path('/exported_csv')."/$file_name";
            $file = fopen($file_path,"a+");
            fputcsv($file,$csvData);
            fclose($file);
        }
    }

    /**
    * set csv file format and created log in exported_list table
    * 
    * @return set Data
    */
    public function set_data($csvData)
    {
        $csvFileData = array();
        $currentData = date('Y-m-d');
        $xdateStatus = array('Live','Converted','Dead');
        //preF($csvData);
        $is_notes = false; 
        $is_xdate = false;
        if(!empty($csvData) && count($csvData) != 0) {
            foreach($csvData as $key=> $data) {
                $file_name = time()."-$key.csv";
                // $file_name = storage_path()."/$file_name";
                if(count($data) != 0) {
                    $this->set_title($key,$file_name);
                }
                foreach ($data as $index => $csv) {
                    
                    if($key == 'xdates') {

                       if(count($csv) != 0) {
                           $is_xdate = true;
                           $csvFileData['xdate'] = isset($csv['xdate'])?date('m/d',strtotime($csv['xdate'])) : '';
                           $csvFileData['XdateName'] = $csv['xname'];
                           $csvFileData['producer'] = $csv['producer_data']['name'];
                           $csvFileData['line'] = $csv['line_data']['name'];
                           
                           $csvFileData['industry'] = $csv['industry_data']['name'];
                           
                           $csvFileData['phone'] = $csv['phone'];
                           $csvFileData['location'] = (isset($csv['city']) && $csv['state']) ? $csv['city'] .',' .$csv['state'] : '';
                           $csvFileData['status'] = $xdateStatus[$csv['status']];
                           if(isset($csv['last_note_datetime'] )) {
                                $csv['last_note_datetime'] = convertTimeToUSERzone($csv['last_note_datetime'],$this->user->choosed_timezone);
                            }
                           $csvFileData['last_note_datetime'] = isset($csv['last_note_datetime'])?date('m/d/Y',strtotime($csv['last_note_datetime'])). ' at ' . date('H:i a',strtotime($csv['last_note_datetime'])) : 'No notes';
                           $csv['created_at'] = convertTimeToUSERzone($csv['created_at'],$this->user->choosed_timezone);
                           $csvFileData['created_at'] = isset($csv['created_at'])?date('m/d/Y',strtotime($csv['created_at'])) : '';
                           $csv['updated_at'] = convertTimeToUSERzone($csv['updated_at'],$this->user->choosed_timezone);
                           
                           $exported_list['type'] = 1;
                          
                           $exported_list['number_of_item'] = $index+1;
                           $this->generate_csv($file_name,$csvFileData);
                        } else {
                            $is_xdate = false;
                        }
                    } else if($key == 'notes') {
                       $exported_list['number_of_item'] = "";
                       if(count($csv) != 0) {
                           $is_notes = true;
                           $csvFileNoteData['XdateName'] = $csv['xdate_data']['xname'];
                           $csvFileNoteData['User_Name'] = $csv['user_data']['name'];
                           
                           if($csv['note_type'] == 1) {
                                $usersName = $this->get_user_name_by_id($csv['user_id'],$csv['notes']);
                                $noteData = default_xdate_note_text($csvFileNoteData['XdateName'],$usersName);

                                $csvFileNoteData['Note'] = $noteData;
                            } else {
                                $csvFileNoteData['Note'] = $csv['notes'];
                            }

                            $csv['created_at'] = convertTimeToUSERzone($csv['created_at'],$this->user->choosed_timezone);
                            $csvFileNoteData['created_at'] = isset($csv['created_at'])?date('m/d/Y',strtotime($csv['created_at'])). ' at ' . date('H:i a',strtotime($csv['created_at'])) : '';
                           $exported_list['type'] = 2;
                           $exported_list['number_of_item'] = $index+1;
                           $this->generate_csv($file_name,$csvFileNoteData);
                        } else {
                            $is_notes = false;
                        }
                    }
                   
                }
                if(($key == 'xdates' && $is_xdate) || ($key == 'notes' && $is_notes) ) {
                    $exported_list['owner_id'] = ($this->user->parent_user_id != 0) ? $this->user->parent_user_id : $this->user->id;
                    $exported_list['file_name'] = $file_name;
                    $file_path = storage_path('/exported_csv')."/$file_name";
                    $exported_list['file_size'] = filesize($file_path)/1024;
                    $exported_list['format'] = 1;
                    $exported_list['user_id'] =$this->user->id;
                    $exported_list['user_name'] =$this->user->name;
                    $exported_list['expired_date'] = date('Y-m-d',strtotime('+1 month'));
                    Export::create($exported_list);
                }
            }
        }
    }

    /**
    * this function set title of csv file
    * @return true on success
    */ 
    public function set_title($type,$file_name) 
    {
        if(!empty($type) && !empty($file_name)) {
            if($type == 'notes') {
                $titles['xdate_name'] = 'Xdate name';
                $titles['user_Name'] = 'User name';
                $titles['note'] = 'Note description';
                $titles['created_date'] = 'Created date';
            } else if($type == 'xdates') {
                $titles['xdate_date'] = 'X-Date';
                $titles['XdateName'] = 'Name';
                $titles['producer'] = 'Producer';
                $titles['line'] = 'Line';
                $titles['industry'] = 'Industry';
                $titles['phone'] = 'Phone';
                $titles['location'] = 'Location';
                $titles['status'] = 'Status';
                $titles['last_note'] = 'Last note';
                $titles['created'] = 'Created date';
                
            }
            $this->generate_csv($file_name,$titles);
        }
    }

    /** 
    * this function return exported data
    *
    */
    public function get_exported_list($id = null)
    {
        $owner_id = ($this->user->parent_user_id != 0) ? $this->user->parent_user_id : $this->user->id;
        if($id != null ) {
            $where = array('status'=>0,'id'=>$id,'owner_id'=>$owner_id);
        } else {
            $where = array('status'=>0,'owner_id'=>$owner_id);
        }
        $exported_list = Export::where($where)->orderBy('created_at','desc')->get()->toArray();
        $printFormatData = $this->print_format_data($exported_list);
        return $printFormatData;
    }

    /**
    * this function for download csv file 
    * @return true on success
    */
    public function download($file_name)
    {
        $filePath = storage_path('exported_csv')."/$file_name";
        if($file_name) {
            $headers = array('Content-Type: text/csv');
            return Response::download($filePath, $file_name, $headers);
        }
    }

    /**
    *  this function remove expired exports
    *  @return true on success
    */
    public function remove_expired_export()
    {
        if(isset($this->vdata['curModAccess']['export']) && $this->vdata['curModAccess']['export'] == 1){
            $currentDate = date('Y-m-d');
            Export::whereDate('expired_date','<',$currentDate)->update(['status'=>1]);
            $exportData = $this->get_exported_list();
            return Response::json(['resultData'=>$exportData],200);
        } else {
            return Response::json(['msg'=>'you don not have permission to remove'],404);
        }
    }

    /**
    * set print format data
    * @return printFormat data
    */
    public function print_format_data($exportData)
    {
        if(!empty($exportData)) {
            foreach($exportData as $key=>$export) {
                $exportsData[$key] = $export; 
                if($export['expired_date']) {
                    $exportsData[$key]['expiredDate'] = date("M d, Y",strtotime($export['expired_date']));    
                } 
                if($export['type']) {
                    $exportsData[$key]['exportType'] = $this->export_status[$export['type']]; 
                } 
                if($export['format']) {
                    $exportsData[$key]['formatType'] = $this->file_format[$export['format']];
                } 
                if($export['created_at']) {
                     $exportsData[$key]['createdDate'] = $this->calculate_date($export['created_at']);
                }
                if($export['file_size']) {
                     $exportsData[$key]['fileSize'] = round($export['file_size'],2);
                }
                if($export['expired_date'] < date('Y-m-d')) {
                    $exportsData[$key]['is_expired'] =  0;
                } else {
                    $exportsData[$key]['is_expired'] =  1;

                }
            }
            
            return $exportsData;
        }
    }

    /**
    * this function calculate file create time
    *
    */
    public function calculate_date($date)
    {

        if(!empty($date)) {
            if(isset($this->user->choosed_timezone)) {
                $currentDate = convertTimeToUSERzone(date("Y-m-d H:i:s"),$this->user->choosed_timezone);
                $date = convertTimeToUSERzone($date,$this->user->choosed_timezone);
            } else {
                $currentDate = date('Y-m-d H:i:s');
                
            }
            $currentDate=date_create($currentDate);
            $createdDate=date_create($date);
            $diff=date_diff($currentDate,$createdDate);
            //$createBefore = "";
           // preF($diff);
            if($diff->m != 0) {
                $createBefore = $diff->m." Month ago";
            } else if(($diff->days > 0) && ($diff->m == 0)) {
                $createBefore = $diff->days." Days ago";
            } else if(($diff->days == 0) && ($diff->h != 0)) {
                $createBefore = $diff->h." hours ago";
            } else if(($diff->days == 0) && ($diff->h == 0) && ($diff->i != 0 )) {
                $createBefore = $diff->i." minutes ago";
            } else if(($diff->days == 0) && ($diff->i == 0) && ($diff->h == 0)) {
                $createBefore = "Just now";
            }
            
            return $createBefore;
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
}
