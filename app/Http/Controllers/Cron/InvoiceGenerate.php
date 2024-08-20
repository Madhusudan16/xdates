<?php 
namespace App\Http\Controllers\Cron;

use Illuminate\Routing\Controller;
use App\User;
use App\Models\Admin\Plan;
use App\Models\Front\UserPlan;
use App\Commons\UserBalance; 
use DB;
use App\Models\Front\Invite;
use App\Models\Front\Invoice;
use App\Models\Front\Card;
use App\Models\Front\InvoiceItem;
use App\Commons\AppMailer;

class InvoiceGenerate extends Controller
{
	/**
    * This function get due plan amount record  
    */
    public function generate_invoice()
    {
    	$toDay = date('Y-m-d');
    	$getInvoiceData = Invoice::whereDate('bill_date','<=',$toDay)->where('status',0)->get();
    	if(!empty($getInvoiceData)) {
    		foreach($getInvoiceData as $invoice) {
    			$userData = $this->check_billing($invoice->owner_id);
    			if($userData) {
    				$userPlanData = $this->get_plan_data($invoice->id);
    				if(!empty($userPlanData)) {
    					$amount = $this->set_data($userPlanData);
    					if(!empty($amount)) {

	    					$invoice->amount = array_sum($amount);
	    					$invoice->status = 1; 
	    					$invoice->save();
	    					$this->invoice_send_mail($userData,$invoice->id);
	    				}
    				}
    			}
    		}
    	}
	}

	/**
	* this function return user_plan data
	*/
	public function get_plan_data($invoice_no)
	{
		if(!empty($invoice_no)) {
			$userPlanData = UserPlan::where('invoice_no',$invoice_no)->get();
			return $userPlanData;
		} else {
			return false;
		}
	}

	/**
	* this function check billing done or not 
	*/
	public function check_billing($owner_id) 
	{
		if(!empty($owner_id)) {
			$next_bill_date = date('Y-m-d', strtotime("+30 days"));

			$userData = User::whereDate('next_bill_date','=',$next_bill_date)->orWhere('is_expired',1)->get()->first();
			
			if(!empty($userData)) {
				return $userData;
			} else {
				return false;
			}
		}
	}

	/**
	* this function set invoice_item data 
	*/
	public function set_data($invoiceItemData) 
	{
		//print_r($invoiceItemData);
		$paidAmount = array();
		if(!empty($invoiceItemData)) {
			foreach($invoiceItemData as $invoiceItem) {
				$tempInvoiceItem = array();
				$tempInvoiceItem['invoice_id'] = $invoiceItem->invoice_no;
				$tempInvoiceItem['from_date'] =  $invoiceItem->plan_start_date;
				$tempInvoiceItem['to_date'] = 	 $invoiceItem->plan_end_date;
				$tempInvoiceItem['plan_id'] = 	 $invoiceItem->plan_id;
				$tempInvoiceItem['plan_name'] = $invoiceItem->plan_name;
				$tempInvoiceItem['paid_amount'] = $invoiceItem->plan_pay_amount;
				$tempInvoiceItem['plan_amount'] = $invoiceItem->plan_amount;
				$paidAmount[] = $invoiceItem->plan_pay_amount;
				$this->insert_invoice_item($tempInvoiceItem);
			}
			return $paidAmount;
		}
	}

	/**
	* this function insert invoice_item Data
	*/ 
	public function insert_invoice_item($invoiceItemData) 
	{
		if(!empty($invoiceItemData)) {
			InvoiceItem::create($invoiceItemData);
		} else {
			return false;
		}
	}

	/**
    * send mail 
    */ 
    public function invoice_send_mail($userData,$invoice_no) 
    {
    	if(!empty($userData) && !empty($invoice_no)) {
            $appMailer = new AppMailer;
            
            if($appMailer->invoice_mail($userData,$invoice_no) ) {
                return true;
            } else {
                return false; 
            }
        }
    }

}
?>