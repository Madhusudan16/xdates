<?php
namespace App\Http\Controllers\Admin;

use App;
use Auth;
use App\User;
use Request;
use Response;
use Illuminate\Routing\Controller;
use Redirect;
use PDF;
use App\Models\Front\Invoice;
use App\Models\Front\InvoiceItem;
use App\Commons\UserBalance; 
use App\Models\Admin\Plan;
use DB;

class invoiceGeneratorController extends Controller
{
    
    /**
     * view data
     */
    protected $vdata = array();

    /* this function create pdf file */
    public function generate_pdf($invoiceData,$invoice_no)
    { 
        //preF($invoiceData);
        //return view('pdf.template',$invoiceData);
        $file_name = $invoice_no.'.pdf';  // name of file 
        $pdf = PDF::loadView('pdf.template',$invoiceData);
        //return $pdf->download($file_name);
        return $pdf->inline($file_name);
    }

    /**
    * this function return invoice data
    */
    public function get_invoice_data($invoice_no = null)
    {
        
        if($invoice_no != null) {
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
}
