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
use PDF;
use App\Models\Front\Invoice;
use App\Models\Front\InvoiceItem;
use App\Commons\UserBalance; 
use App\Models\Admin\Plan;
use DB;
use App\Models\Front\UserPlan;

class InvoiceController extends Controller
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
            $this->vdata['page_section_class'] = 'top-padding-10 invoice';
            $this->vdata['page_title'] = 'Invoices';
        }

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
        if($this->vdata['curModAccess']['view'] != 1){
           return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        if(!check_user($this->user->parent_user_id)) {
            Auth::logout();
        }
        $owner_id = ($this->user->parent_user_id == 0 ) ? $this->user->id :$this->user->parent_user_id;
        $uBalance = new UserBalance;
        $pay_amount =  $this->find_plan_pay_amount();
        $this->vdata['userBalance'] = round($uBalance->getUserBalance($owner_id)-$pay_amount,2);
        $this->vdata['invoiceData'] = $this->get_invoice_data();
        $this->vdata['user'] = $this->user;
        $this->vdata['status'] = array('inactive','active');
        $user = new User;
        $this->vdata['total_user']=$users_count= $user->where(['parent_user_id'=>$owner_id , 'status'=>1])->orWhere(['id'=>$owner_id , 'status'=>1])->count();
        
        $this->vdata['plan_details'] = $this->plan_user();
        return view('front.invoices',$this->vdata);
        //return $pdf->download('template.pdf');
       //return view('pdf.template',$this->vdata);
    }

    /* this function create pdf file */
    public function generate_pdf($invoiceData,$invoice_no)
    { 
        //preF($invoiceData);
        //return view('pdf.template',$invoiceData);
        $file_name = $invoice_no."_".$this->user->id.'.pdf';  // name of file 
        $pdf = PDF::loadView('pdf.template',$invoiceData);
        //return $pdf->download($file_name);
        return $pdf->inline($file_name);
    }

    /**
    * this function return invoice data
    */
    public function get_invoice_data($invoice_no = null)
    {
        $where = array('owner_id'=>$this->user->id,'status'=>1);
        if($invoice_no == null ) {
            $invoiceData = Invoice::where($where)->orderBy('bill_date','desc')->get();
        } else {
            $where['id'] = $invoice_no;
            $invoiceData = Invoice::where($where)->get()->first();
            if(count($invoiceData) != 0) {
                $invoiceData->toArray();
            }
        }
        return $invoiceData;
    }

    /**
    * this function get data for pdf
    */
    public function get_pdf_data($invoice_no)
    {
        $invoiceItems = InvoiceItem::where('invoice_id',$invoice_no)->get()->toArray();
        $invoiceData['items'] = $invoiceItems;
        $invoices = $this->get_invoice_data($invoice_no);
        if(!empty($invoices['to_address'])) {
            $toAddress = json_decode($invoices['to_address'],true);
            $invoices['to_address'] = $toAddress;
        }
        /*if(!empty($invoices['from_address'])) {
            $fromAddress = json_decode($invoices['from_address'],true);
            $invoices['from_address'] = $fromAddress;
        }*/
        $invoiceData['invoices'] = $invoices;
        $invoiceData['cost'] =$this->calculate_cost($invoiceItems);
        return $this->generate_pdf($invoiceData,$invoice_no);
    }

    /*
    * this function return user plan data
    */
    public function plan_user()
    {
        $plan = new Plan; 
        if($this->user->current_plan != 0) {
            $plan_details =$plan->where('id', $this->user->current_plan)->get(['n_allowed_users','name'])->first()->toArray();
        } else {
            $n_allowed_users = DB::table('plans')->where('status',1)->max('n_allowed_users');
            $plan_details = $plan->where('n_allowed_users',$n_allowed_users)->where('status',1)->get(['n_allowed_users'])->first()->toArray();
        }
        return $plan_details;
    }

    /**
    *  this function calculate cost
    */
    public function calculate_cost($invoiceData) 
    {
        if(!empty($invoiceData)) {
            $totalPlanAmount  = 0;
            $totalPaidAmount  = 0; 
            foreach($invoiceData as $invoice) {
                $totalPlanAmount  += $invoice['plan_amount'];
                $totalPaidAmount  += $invoice['paid_amount'];
            }

            $paymentDetails['totalPlanAmount'] = $totalPlanAmount;
            $paymentDetails['totalPaidAmount'] = $totalPaidAmount;
            $paymentDetails['due_payment'] = $totalPaidAmount - $totalPaidAmount;
           // preF($paymentDetails);
            return $paymentDetails;
        }
    }

    /**
    * find current plan pay amount 
    */
    public function find_plan_pay_amount()
    {
        $owner_id = ($this->user->parent_user_id > 0) ? $this->user->parent_user_id :$this->user->id;
        $userplan = new UserPlan;
        $user_plan   = $userplan->where('status',1)->where('user_id',$owner_id)->get()->first();
        if(!empty($user_plan)) {
            return $user_plan->plan_pay_amount-$user_plan->discount_amount;
        } else {
            return 0;
        }
    }
}
