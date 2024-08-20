<?php
namespace App\Commons;
 
use DB;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\Commons\UserBalance; 
use App\Models\Front\Card;
use App\Models\Front\UserTransaction;
class PlanPayments{ 

    /**
     * Create a new plan payments
     */
    public function __construct()
    {
      //do intital things
    } 
	
	public function addPlanAndPayment($amountNeedsToPay,$puser,$newUserPlan,$oldUserPlan=null){
		
		$returnData = array();
		$returnData['type'] = 'error';
		$returnData['message'] = '';
		
		$cardData = Card::where('user_id',$puser->id)->get()->first();

        if(!empty($cardData) && $cardData->customer_payment_profile_id != '') {
        	
        	//auth_card_id - Customer Profile ID,  customer_payment_profile_id - Payment Profile(s)	
        	$payment = $this->processPayment($cardData->auth_card_id, $cardData->customer_payment_profile_id, $amountNeedsToPay);
			if($payment['type'] == 'success'){
				
				//save transactions
				$transaction = new UserTransaction;
				$transaction->trans_id = $payment['transId'];
				$transaction->plan_id = $newUserPlan->plan_id;
				$transaction->amount = $amountNeedsToPay;
				$transaction->user_id = $puser->id;
				$transaction->plan_name = $newUserPlan->plan_name;
				$transaction->trans_auth_code = $payment['authCode'];
				$transaction->trans_details = json_encode($payment['extra_data']);
				$transaction->status = 1; 
				$transaction->save();
				
				//save new plan to user
				if(!empty($oldUserPlan)){
					$puser->next_bill_date = $newUserPlan->plan_end_date;
				} else {
					$puser->next_bill_date = $newUserPlan->plan_end_date;
				}
				
				$puser->current_plan = $newUserPlan->plan_id;
				$puser->save();
				
				//save new plan
				$newUserPlan->trans_id = $transaction->id;
				$newUserPlan->save();
				
				//save previous plan if exists
				if($oldUserPlan != null){
					$oldUserPlan->save();
				}
				
				$returnData['type'] = 'success';
				$returnData['msg_type'] = 'transaction_success';
				$returnData['message'] = 'Transacation has been done successfully.';
							 
			}else{
				$transaction = new UserTransaction;
				$transaction->trans_id = '0';
				$transaction->plan_id = $newUserPlan->plan_id;
				$transaction->amount = $amountNeedsToPay;
				$transaction->user_id = $puser->id;
				$transaction->plan_name = $newUserPlan->plan_name;
				$transaction->trans_auth_code = '';
				if(isset($payment['extra_data'] )) {
					$transaction->trans_details = json_encode($payment['extra_data']);
				} else {
					$card = array();
					$card  = array('accountNumber'=>$cardData->card_no,'expiry_date'=>$cardData->expiry_date);
					$transaction->trans_details = json_encode($card);
				}
				$transaction->status = 0; 
				$transaction->save();
				
				$returnData['type'] = 'error';
				$returnData['msg_type'] = 'transaction_error';
				$returnData['message'] = 'Error occured while processing transactions.';
			}
        } else {
            $returnData['type'] = 'error';
			$returnData['msg_type'] = 'credit_card_not_found';
			$returnData['message'] = 'Credit ';
        } 
		
		return $returnData;
	} 
	
	function processPayment($profileid, $paymentprofileid, $amount){
		
	    // Common setup for API credentials
	    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	    $merchantAuthentication->setName(env('AUTHORIZED_LOGIN_ID'));
	    $merchantAuthentication->setTransactionKey(env('AUTHORIZED_TRANSACTION_KEY'));
	    $refId = 'ref' . time();
		
	    $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
	    $profileToCharge->setCustomerProfileId($profileid);
		
	    $paymentProfile = new AnetAPI\PaymentProfileType();
	    $paymentProfile->setPaymentProfileId($paymentprofileid);
	    $profileToCharge->setPaymentProfile($paymentProfile);
		
	    $transactionRequestType = new AnetAPI\TransactionRequestType();
	    $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
	    $transactionRequestType->setAmount($amount);
	    $transactionRequestType->setProfile($profileToCharge);
		
	    $request = new AnetAPI\CreateTransactionRequest();
	    $request->setMerchantAuthentication($merchantAuthentication);
	    $request->setRefId( $refId);
	    $request->setTransactionRequest( $transactionRequestType);
		
	    $controller = new AnetController\CreateTransactionController($request);
		
		//\net\authorize\api\constants\ANetEnvironment::PRODUCTION
	    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
		
		$dataReturn = array();
		$dataReturn['type'] = 'error';
		$dataReturn['msg'] = '';
		
	    if ($response != null){
	      
	      $tresponse = $response->getTransactionResponse();
			
	      if (($tresponse != null) && ($tresponse->getResponseCode()== "1") ){
	      	 
			$dataReturn['type'] = 'success';
			$dataReturn['msg'] = 'Amount charged successfully';
			$dataReturn['transId'] = $tresponse->getTransId();
			$dataReturn['authCode'] = $tresponse->getAuthCode(); 
			$extraData = array();
			$extraData['accountNumber'] = $tresponse->getAccountNumber();
			$extraData['accountType'] = $tresponse->getAccountType();
			
			$dataReturn['extra_data'] = $extraData;
			 
	      }elseif (($tresponse != null) && ($tresponse->getResponseCode()=="2") ){
	      	
			$messages = $response->getMessages();
			$msg = $messages->getMessage();
			
	        $dataReturn['type'] = 'error';
			$dataReturn['msg'] = 'Error occured';
			$dataReturn['transId'] = '';
			$dataReturn['authCode'] = ''; 
			$extraData = array();
			$extraData['error_code'] = $msg->getCode();
			$extraData['error_text'] = $msg->getText();
			
			$dataReturn['extra_data'] = $extraData; 
			
	      }elseif (($tresponse != null) && ($tresponse->getResponseCode()=="4") ){
	      	
	        $dataReturn['type'] = 'success';
			$dataReturn['msg'] = 'Amount are not hold';
			$dataReturn['transId'] = $tresponse->getTransId();
			$dataReturn['authCode'] = $tresponse->getAuthCode(); 
			$extraData = array();
			$extraData['accountNumber'] = $tresponse->getAccountNumber();
			$extraData['accountType'] = $tresponse->getAccountType();
			
			$dataReturn['extra_data'] = $extraData;
			
	      }
		  
	    }else{ 
	      	$dataReturn['type'] = 'error';
			$dataReturn['msg'] = 'Authorize Api does not respond!';
		  
	    }
		
	    return $dataReturn;
		
	  }
 
}/*
<?xml version="1.0" encoding="utf-8"?> 
<createTransactionResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
  <refId>
    123456
  </refId>
  <messages>
    <resultCode>
      Error
    </resultCode>
    <message>
      <code>
        E00013
      </code>
      <text>
        Customer Profile ID is invalid.
      </text>
    </message>
  </messages>
  <transactionResponse />
</createTransactionResponse>


<?xml version="1.0" encoding="utf-8"?>
<createTransactionResponse 
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
xmlns="AnetApi/xml/v1/schema/AnetA
piSchema.xsd">
  <refId>123456</refId>
  <messages>
    <resultCode>Ok</resultCode>
    <message>
      <code>I00001</code>
      <text>Successful.</text>
    </message>
  </messages>
  <transactionResponse>
    <responseCode>1</responseCode>
    <authCode>UGELQC</authCode>
    <avsResultCode>E</avsResultCode>
    <cavvResultCode />
    <transId>2148061808</transId>
    <refTransID />
    <transHash>0B428D8A928AAC61121AF2F6EAC5FF3F</transHash>
    <testRequest>0</testRequest>
    <accountNumber>XXXX0015</accountNumber>
    <accountType>MasterCard</accountType>
    <message>
      <code>1</code>
      <description>This transaction has been approved.</description>
    </message>
    <userFields>
      <userField>
        <name>MerchantDefinedFieldName1</name>
        <value>MerchantDefinedFieldValue1</value>
      </userField>
      <userField>
        <name>favorite_color</name>
        <value>lavender</value>
      </userField>
    </userFields>
  </transactionResponse>
</createTransactionResponse>*/
