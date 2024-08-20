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
use App\Models\Front\Card;
use App\Models\Front\Country;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Redirect;
use App\Models\Front\Invite;
use App\Models\Front\Invoice;
use App\Models\Admin\Setting;
use DB;
use App\Commons\UserBalance; 
use App\Commons\AppMailer;
use App\Commons\PlanPayments;
use App\Commons\Date;
use App\Models\Admin\Plan;
use App\Models\Front\UserPlan;
use App\Models\Admin\CouponLog;
use App\Models\Admin\Coupon;

class CardController extends Controller
{
	/**
     * The module id
     */
    protected $moduleId = 6;

	/**
     * The guard name
     */
    protected $guard = 'web';

	/**
     * view data
     */
    protected $vdata = array();

    /**
    * country Class Object
    */
    public $countryObj = array();
    /**
    *   this variable check data going to whether insert or update
    */
	public $isInsert = 0;
    /**
    * store all month
    */
    public $month = array();

    /**
    * set auth id
    */
    public $auth_card_id = '';

    /**
    * set customer payment profile id
    *
    */
    public $customerPaymentProfileId = '';

   	public function __construct(UserAccess $userAccess)
    {

       $this->middleware('auth');
	   $this->user = Auth::guard($this->guard)->user();
       $this->userObj = new User();
       $this->countryObj = new Country();
       if(!empty($this->user)) {
			$this->access = $userAccess->checkModuleAccessByUserType($this->moduleId,$this->user->user_type);
			$this->vdata['user'] = $this->user;
			$this->vdata['curModAccess'] = $this->access['current'];
			$this->vdata['allModAccess'] = $this->access['all'];
			$this->vdata['page_section_class'] = 'top-padding-10 cart';
            $this->vdata['page_title'] = 'Manage Card';
            $this->vdata['countries'] = $this->countryObj->getCountry();
            $this->month = array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
            $this->vdata['months'] = $this->month;
            $this->vdata['states'] = $this->countryObj->getState();

        }
    }

    /**
    * call card.blade.php file
    *
    * @return view card.blade.php
    */
    public function index()
    {
        if($page_url = prevent_user($this->user)) {
           return redirect($page_url);
        }
		if($this->vdata['curModAccess']['view'] != 1){
           return Response::view('errors.404',array('message'=>"You are not authorized to access this page.",'title'=>"Access denied!"),404);
        }
        $checkAccess = acoount_expire_text($this->user);
        $this->vdata['check_access'] = $checkAccess;
        if(!check_user($this->user->parent_user_id)) {
            Auth::logout();
        }
        $rules =  array(
                'address_line_1' => 'required',
                'zip_code'       => 'required|numeric',
                'card_no'        => 'required',
                'card_cvv'       => 'required|numeric|digits_between:3,4',
                'billing_first_name' => 'required',
                'billing_last_name'  => 'required',
                'city'          => 'required',
                'state'         => 'required',
                'country'       =>  'required',
                'expires_month' => "required",
                'expires_year'  => 'required|check_expiry_year:expires_year,expires_month'
            );
        $msg = array(
            'address_line_1' => 'Billing address 1  field is required.',
            'card_no'        => 'Card number field is required.',
            'card_cvv.digits_between' =>"The card cvv must be 3 or 4 digits.",
            'card_no.required' => "Invalid card number!",
            );
        $cardData = $this->getCard();
        if($cardData != false) {
            $cardData = $this->setData($cardData);
            $id = $cardData->id;

            $this->auth_card_id = $cardData->auth_card_id;
            $this->customerPaymentProfileId = $cardData['customer_payment_profile_id'];
        }
        if(Request::isMethod('post')) {
            $formData = Request::all();
            $validationOnServerSide = validator::make($formData,$rules,$msg);
            if($validationOnServerSide->passes()) {
                $isValid = $this->validateCreditcard($formData['card_no']);
                if($isValid == 1 ) {
                    $setFormData = $this->setData($formData,1);
                    if($cardData != false) {
                        if($this->inserUpdateCard($setFormData,$id)) {
                            $cardData = $this->getCard();
                            $cardData = $this->setData($cardData);
                            $this->vdata['success_message'] = "Card/Billing info updated successfully";
                        } else {
                            $this->vdata['error_message'] = "Card/Billing info does not updated";
                        }
                    } else {
                        if($this->inserUpdateCard($setFormData)){
                            $cardData = $this->getCard();
                            $cardData = $this->setData($cardData);
                            $this->vdata['success_message'] = "Card/Billing info updated successfully";
                        } else {
                            $this->vdata['error_message'] = "Card detail not saved!";
                        }
                    }
                } else {
                    $this->vdata['error_message'] =$isValid;
                }
            }
        }
        $validator = JsValidator::make($rules,$msg,[],'#billingInfo');
        $this->vdata['validator'] = $validator;
        $this->vdata['cardData']  = $cardData;
        if(!empty($cardData->state) && isset($cardData->state)) {
            if($cardData->state && !empty($cardData->state)) {
                $this->vdata['states'] = $this->countryObj->getState($cardData->country);
            }
        }
    	return view('front.card',$this->vdata);
    }

    /**
    * this function insert Update user card  data
    *
    * @return true on success
    */

    public function inserUpdateCard($formData,$id = null)
    {
        if(isset($this->vdata['curModAccess']['edit']) && $this->vdata['curModAccess']['edit'] == 1){ 
            if($id != null && $this->auth_card_id != '' ) {
                 $card_update = $this->updateCustomerProfile($formData,$this->auth_card_id,$this->customerPaymentProfileId);
            } else {
                $sandboxData = $this->createCustomerPaymentProfile($formData);
                if(isset($sandboxData['customer_profile_id'])) {
                    $formData['auth_card_id'] = $sandboxData['customer_profile_id'];
                    $formData['customer_payment_profile_id'] = $sandboxData['customer_payment_profile_id'];
                } else {
                    return false;
                }
            }
            $formData['card_no'] = $this->changeCardFormat($formData['card_no']);
            unset($formData['card_cvv']);
            if($this->isInsert == 0 && $id == null ) {
                if(Card::create($formData)){
                   return true;
                } else {
                    return false;
                }
            } else {
                if(Card::where('id',$id)->update($formData)) {
                    if($card_update == 1) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
    }

    /**
    * get save card data for current login user
    *
    * @return data on succes
    */
    public function getCard()
    {
        $cardData = Card::where('user_id',$this->user->id)->get();
        if($cardData->count() == 1) {
            $this->isInsert = 1;
            return $cardData;
        } else {
            return false;
        }
    }

    /**
    * this function set value for update,insert and print
    *  $for = 1 means insert or update otherwise print
    * @return set data
    */
    public function setData($data,$for = null)
    {
        $setData = $data;
        if($for == 1) {
            if(isset($setData['_token'])) {
                unset($setData['_token']);
            }
            $setData['expiry_date'] = $setData['expires_year'].'-'.$setData['expires_month'];
            $setData['user_id'] = $this->user->id;
            $setData['status'] = 0;
            unset($setData['expires_month']);
            unset($setData['expires_year']);
            return $setData;
        } else {
            foreach($data as $myCardData){
                if(!empty($myCardData->expiry_date)) {
                    $expiryDate = explode('-',$myCardData->expiry_date);
                    $myCardData->expires_month = $expiryDate[1];
                    $myCardData->expires_year = $expiryDate[0];
                    $setData = $myCardData;
                }
            }
            return $setData;
        }
    }

    /**
    * validate credit card on server side
    *
    * @return 1 on valid card
    */
    public function validateCreditcard($credit_card_number)
    {

        $firstnumber = substr($credit_card_number, 0, 1);
        switch ($firstnumber) {
            case 3:
                if (!preg_match('/^3\d{3}[ \-]?\d{6}[ \-]?\d{5}$/', $credit_card_number)) {
                    return 'This is not a valid American Express card number';
                }
                break;
            case 4:
                if (!preg_match('/^4\d{3}[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $credit_card_number)) {
                    return 'This is not a valid Visa card number';
                }
                break;
            case 5:
                if (!preg_match('/^5\d{3}[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $credit_card_number))  {
                    return 'This is not a valid MasterCard card number';
                }
                break;
            case 6:
                if (!preg_match('/^6011[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $credit_card_number)) {
                    return 'This is not a valid Discover card number';
                }
                break;
            default:
                return 'This is not a valid credit card number';
        }
        $credit_card_number = str_replace('-', '', $credit_card_number);
        $map = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
                    0, 2, 4, 6, 8, 1, 3, 5, 7, 9);
        $sum = 0;
        $last = strlen($credit_card_number) - 1;
        for ($i = 0; $i <= $last; $i++) {
            $sum += $map[$credit_card_number[$last - $i] + ($i & 1) * 10];
        }
        if ($sum % 10 != 0) {
            return 'This is not a valid credit card number';
        }
        return 1;
    }
    /**
    * get state by country id
    *
    * @return all state which match with country id
    */
    public function getState($countryId=null)
    {
        $listOfState = '';
        $stateData = $this->countryObj->getState($countryId);
        if(count($stateData)==0) {
            $listOfState .= "<option value=''>Select State</option>";
            echo $listOfState;
        } else {
            foreach($stateData as $state) {
                $listOfState .= "<option value=$state->id>$state->state_name</option><br>";
            }
            echo $listOfState;
        }
    }
    /**
    * create userProfile to sandbox account
    *
    * @return sandbox user id on success
    */
    public function createCustomerPaymentProfile($formData,$existingcustomerprofileid = null)
        {
        $this->is_registered_before(); // this function check account exist for current user email in sandbox
        $otherDetails = $this->getCompanyNameAndEmail();
        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(env('AUTHORIZED_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(env('AUTHORIZED_TRANSACTION_KEY'));
        $refId = 'ref' . time();
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($formData['card_no']);
        $creditCard->setCardCode($formData['card_cvv']);
        $creditCard->setExpirationDate($formData['expiry_date']);
        $paymentCreditCard = new AnetAPI\PaymentType();
        $paymentCreditCard->setCreditCard($creditCard);
        // Create the Bill To info
        $billto = new AnetAPI\CustomerAddressType();
        $billto->setFirstName($formData['billing_first_name']);
        $billto->setLastName($formData['billing_last_name']);
        $billto->setCompany($otherDetails['comp_name']);
        $billto->setAddress($formData['address_line_1']);
        $billto->setCity($formData['city']);
        $state = $this->get_country_state($formData['state'],'state');
        $billto->setState($state->state_code);
        $billto->setZip($formData['zip_code']);
        $country = $this->get_country_state($formData['country'],'country');
        $billto->setCountry($country->name);

        // Create a Customer Profile Request
        //  1. create a Payment Profile
        //  2. create a Customer Profile
        //  3. Submit a CreateCustomerProfile Request
        //  4. Validate Profiiel ID returned

        $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
        $paymentprofile->setCustomerType('individual');
        $paymentprofile->setBillTo($billto);
        $paymentprofile->setPayment($paymentCreditCard);
        $paymentprofiles[] = $paymentprofile;
        $customerprofile = new AnetAPI\CustomerProfileType();
        $customerprofile->setDescription('X-Date :'. $this->user->name);
        $customerprofile->setMerchantCustomerId($this->user->id);
        $customerprofile->setEmail($otherDetails['email']);

        $customerprofile->setPaymentProfiles($paymentprofiles);
        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId( $refId);

        $request->setProfile($customerprofile);
        $controller = new AnetController\CreateCustomerProfileController($request);
        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
            $paymentProfiles = $response->getCustomerPaymentProfileIdList();
            //echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";
            $customerData['customer_profile_id'] = $response->getCustomerProfileId();
            $customerData['customer_payment_profile_id'] = $paymentProfiles[0];
            return  $customerData;
        } else {
            echo "ERROR :  Invalid response\n";
            $errorMessages = $response->getMessages()->getMessage();
            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
        }
        return $response;
    }

    /**
    * check user is owner or not
    *
    * return owner company name
    */
    public function getCompanyNameAndEmail()
    {
        $data['email'] = $this->user->email;
        if($this->user->parent_user_id == 0) {
            $data['comp_name'] = $this->user->com_name;
        } else {
            $getOwnerData = User::where('id',$this->user->parent_user_id)->get();
            if($getOwnerData->count() == 1) {
                foreach($getOwnerData as $ownerData) {
                $data['comp_name'] = $ownerData->com_name;
                }
            }
        }
        return $data;
    }

    /**
    * this function update customer payment details
    *
    * @return  true on success
    */
    public function updateCustomerProfile($formData,$customerProfileId,$customerPaymentProfileId) {
        //$this->is_registered_before(); // this function check account exist for current user email in sandbox
        // Common setup for API credentials
        $otherDetails = $this->getCompanyNameAndEmail();
        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(env('AUTHORIZED_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(env('AUTHORIZED_TRANSACTION_KEY'));
        $refId = 'ref' . time();
        $request = new AnetAPI\UpdateCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setCustomerProfileId($customerProfileId);
        $controller = new AnetController\GetCustomerProfileController($request);
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($formData['card_no']);
        $creditCard->setCardCode($formData['card_cvv']);
        $creditCard->setExpirationDate($formData['expiry_date']);
        $paymentCreditCard = new AnetAPI\PaymentType();
        $paymentCreditCard->setCreditCard($creditCard);
        // Create the Bill To info
        $billto = new AnetAPI\CustomerAddressType();
        $billto->setFirstName($formData['billing_first_name']);
        $billto->setLastName($formData['billing_last_name']);
        $billto->setCompany($otherDetails['comp_name']);
        $billto->setAddress($formData['address_line_1']);
        $billto->setCity($formData['city']);
        $state = $this->get_country_state($formData['state'],'state');
        $billto->setState($state->state_code);
        $billto->setZip($formData['zip_code']);
        $country = $this->get_country_state($formData['country'],'country');
        $billto->setCountry($country->name);
        $paymentprofile = new AnetAPI\CustomerPaymentProfileExType();
        $paymentprofile->setCustomerPaymentProfileId($customerPaymentProfileId);
        $paymentprofile->setBillTo($billto);
        $paymentprofile->setPayment($paymentCreditCard);

        // Submit a UpdatePaymentProfileRequest

        
        $request->setPaymentProfile( $paymentprofile );

        $controller = new AnetController\UpdateCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

        //set profile details 
        $updatecustomerprofile = new AnetAPI\CustomerProfileExType();
        $updatecustomerprofile->setCustomerProfileId($customerProfileId);
        $updatecustomerprofile->setMerchantCustomerId($this->user->id);
        $updatecustomerprofile->setEmail($this->user->email);
        $customeProfile = new AnetAPI\UpdateCustomerProfileRequest();
        $customeProfile->setMerchantAuthentication($merchantAuthentication);
        $customeProfile->setProfile($updatecustomerprofile);
        $controller = new AnetController\UpdateCustomerPaymentProfileController($customeProfile);
        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        //end 

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
            // Update only returns success or fail, if success
            // confirm the update by doing a GetCustomerPaymentProfile
            $getRequest = new AnetAPI\GetCustomerPaymentProfileRequest();
            $getRequest->setMerchantAuthentication($merchantAuthentication);
            $getRequest->setRefId( $refId);
            $getRequest->setCustomerProfileId($customerProfileId);
            $getRequest->setCustomerPaymentProfileId($customerPaymentProfileId);

            $controller = new AnetController\GetCustomerPaymentProfileController($getRequest);
            $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
            if(($response != null)) {
                if ($response->getMessages()->getResultCode() == "Ok") {
                    //echo "GetCustomerPaymentProfile SUCCESS: " . "\n";
                    //echo "Customer Payment Profile Id: " . $response->getPaymentProfile()->getCustomerPaymentProfileId() . "\n";
                   // echo "Customer Payment Profile Billing Address: " . $response->getPaymentProfile()->getbillTo()->getAddress(). "\n";
                } else {
                   /* echo "GetCustomerPaymentProfile ERROR :  Invalid response\n";
                    $errorMessages = $response->getMessages()->getMessage();
                    echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";*/
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            /*echo "Update Customer Payment Profile: ERROR Invalid response\n";
            $errorMessages = $response->getMessages()->getMessage();
            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";*/
            return 0;
        }
        $payment_due = $this->find_days_diff($this->user->next_bill_date);
        
        if($payment_due < 0) {
            if($this->update_plan()) {
                $this->user->is_expired = 0; 
                $this->user->account_exp = null;
                $logsData['owner_id'] = $this->user->id;
                $logsData['status'] = 1;
                $this->billing_log(null,$logsData);
                $this->user->save();
                return 1;
            } else {
                $this->user->is_expired = 1; 
                $this->user->account_exp = date('Y-m-d h:i:s');
                $logsData['owner_id'] = $this->user->id;
                $logsData['status'] = 0;
                $this->billing_log(null,$logsData);
                $this->user->save();
                return 0;
            }
        } else {
            return 1;
        }
    }

    /**
    * this function change card number format
    *
    * return card number format like  xxxx1111
    */
    public function changeCardFormat($cardNumber)
    {
        $length = strlen($cardNumber);
        $lastDigit = $length - 4;
        $getLastDigit = substr($cardNumber,$lastDigit);
        return 'XXXX-'.$getLastDigit;
    }

    /**
    * this function validate expiry month and year
    *
    * @return true on success
    */
    public function validateDate($month,$year)
    {
        $currentMonth = date("m");
        $currentYear  = date("Y");
        echo $currentMonth . "= $month year =$year =  " . $currentYear;
        if($month>$currentMonth && $currentYear >= $year) {
            return 1;
        } else {
            return "Invalid Expiry Date";
        }
    }

    /**
    * this function return country and state
    */
    public function get_country_state($id, $find = "state")
    {
         $country_obj = new Country;
        if($find == 'state') {
            $state = $country_obj->getState('',$id);
            return end($state);
        } else if($find == 'country') {
            $country = $country_obj->getCountry($id);
            return end($country);
        }
    }

    /**
    * this function try to take payment while next_billing _date expired
    */
    public function update_plan()
    {
        $userData = $this->user;
        $next_billing_date = $this->user->next_bill_date;
        $payment_due = $this->find_days_diff($next_billing_date);
        
        if($payment_due < 0) { 
            if($userData->parent_user_id > 0) {
                $ownerID = $userData->parent_user_id;
                $adminID = $userData->id;
            } else {
                $ownerID = $userData->id;;
                $adminID = 0;
            } 
            $userplan   =  new UserPlan;
            $user_plan   = $userplan->where('status',1)->where('user_id',$ownerID)->get()->first();
            $planID = $user_plan->plan_id; 
            $plan = Plan::where('id',$planID)->get()->first();
        
            $uBalance = new UserBalance;
            $userBalance = $uBalance->getUserBalance($ownerID); 
            //getUserBalance($userID,$type='amount');
        
            $amountNeedsToPay = $plan->cost;
            //$previous_plan_amount =  $user_plan->plan_pay_amount;
            $currentDate = date("Y-m-d");
        
            $user = new User;
            $users_count= $user->where(['parent_user_id'=>$ownerID, 'status'=>1])->orWhere(['id'=>$ownerID , 'status'=>1])->count(); 
      
        //check if no. of users exceed as per selected plan
            $max_allowed_users = $plan->n_allowed_users;
            if($users_count > $max_allowed_users){
                return false;
            }
        
            $new_user_plan = new UserPlan;
            $new_user_plan->plan_id = $user_plan->plan_id;
            $new_user_plan->user_id = $ownerID;
            $new_user_plan->plan_name  = $user_plan->plan_name;
            $new_user_plan->plan_amount = $user_plan->plan_amount;
            $new_user_plan->n_allowed_users = $user_plan->n_allowed_users;
            $new_user_plan->status = 1;
            $new_user_plan->plan_start_date= date('Y-m-d');
            $new_user_plan->plan_end_date  = date('Y-m-d', strtotime("+30 days")); 
            $new_user_plan->plan_pay_amount = $amountNeedsToPay;
            $new_user_plan->referal_discount = $plan->refer_percentage;
            $debitArr = array();
            $creditArr = array();
            if(!empty($user_plan)){
                $new_user_plan->coupon_id = $user_plan->coupon_id;
                $oneDayAmount = $user_plan->plan_amount - $user_plan->discount_amount;
                $oneDayAmount = $oneDayAmount/30;
                $nUsedDays = $this->getDaysDiffFromNow($user_plan->plan_start_date,$user_plan->plan_end_date);
                $amountDeducted = round($oneDayAmount * $nUsedDays,2);
                $user_plan->plan_pay_amount = $amountDeducted;
                $userBalance = $userBalance - $amountDeducted;
                $user_plan->status = 0;
                $debitArr[] =  array('amount'=>$amountDeducted);
            
                //get amount of plan as per no. of days will be used
                /*$f_oneDayAmount = $plan->cost;
                $f_oneDayAmount = round(($f_oneDayAmount/30),2);
            
                $f_nUsedDays = $this->getDaysDiffFromNow($currentDate,$user_plan->plan_end_date);
            
                $amountWillCharge = round($f_oneDayAmount * $f_nUsedDays,2);*/
            
                //$debitArr[] =  array('amount'=>$amountWillCharge);

                if($userBalance >= $plan->cost){
                    $amountNeedsToPay = 0;  
                }else{
                    $amountNeedsToPay = ($plan->cost - $userBalance); 
                }
                //print('Next Date ' . date('Y-m-d', strtotime('-1 day', strtotime($date_raw))));
                //$new_user_plan->plan_end_date = $user_plan->plan_end_date; 
            
                $user_plan->plan_end_date = date('Y-m-d', strtotime('-1 day', strtotime($currentDate))) ;
                //$user_plan->plan_end_date = date('Y-m-d');
            
            } 
            /* coupon apply Code */ 
            $discount_amount  = $this->coupon($new_user_plan,$userData);
            if($discount_amount && is_array($discount_amount)) {
                
                $new_user_plan->discount_amount = $discount_amount['discount'];
                $new_user_plan->coupon_id = $discount_amount['coupon_id'];
                if($discount_amount['plan_pay_amount'] > 0) {
                    $amountNeedsToPay  = $amountNeedsToPay - $discount_amount['plan_pay_amount'];
                    if($amountNeedsToPay < 0) {
                      $amountNeedsToPay = 0;
                    }
                    //$new_user_plan->plan_pay_amount = $amountNeedsToPay;
                }
            } else {
                $new_user_plan->coupon_id = 0;
            }   
            /* end coupon apply code */
            if($amountNeedsToPay > 0){
            
                $PlanPayments = new PlanPayments;
                if(!empty($user_plan)){

                    $payment = $PlanPayments->addPlanAndPayment($amountNeedsToPay, $userData, $new_user_plan,$user_plan);
                }else{
                    $payment = $PlanPayments->addPlanAndPayment($amountNeedsToPay, $userData, $new_user_plan);
                }
            
            if($payment['type'] == 'success'){
                if(isset($discount_amount['coupon_log']) && !empty($discount_amount['coupon_log']))  {
                    $discount_amount['coupon_log']->save();
                } 
                if(isset($discount_amount['old_coupon_log']) && !empty($discount_amount['old_coupon_log']))  {
                    $discount_amount['old_coupon_log']->save();
                }
                $logsData['owner_id'] = $ownerID;
                $logsData['status'] = 1;
                $this->billing_log(null,$logsData);
                $invoiceNo = $this->create_invoice($new_user_plan);
                $new_user_plan->invoice_no = $invoiceNo;
                $new_user_plan->save();
                $creditArr[] = array('amount'=>$amountNeedsToPay);
                //add credit balance to user 
                $credit_amount = 0; 
                foreach($creditArr as $credit){ 
                    if($credit['amount'] > 0){  
                        $credit_amount += $credit['amount'];
                        $uBalance->addCredit($credit['amount'],$ownerID,'amount',$adminID);
                    }
                }

                if($userData->refer_via != null && isset($userData->refer_via)) {
                    $this->user_referral($userData,$user_plan->invoice_no);
                }
                //add debit balance to user 
                
                foreach($debitArr as $debit){
                    if($debit['amount'] > 0){  
                        $uBalance->addDebit($debit['amount'],$ownerID,'amount',$adminID);
                    }
                }
                $this->send_mail($new_user_plan,$userData,1,$credit_amount);
                return true;
                
                }else if($payment['type'] == 'error' && $payment['msg_type'] == 'credit_card_not_found'){ 
                    $this->send_mail($new_user_plan,$userData,0);
                    return false;
                }else if($payment['type'] == 'error' && $payment['msg_type'] == 'transaction_error'){
                    $this->send_mail($new_user_plan,$userData,0);
                    return false;
                }else{
                    $this->send_mail($new_user_plan,$userData,0);
                    return false;
                } 

            }  else {

                if(isset($discount_amount['coupon_log']) && !empty($discount_amount['coupon_log']))  {
                    $discount_amount['coupon_log']->save();
                }
                if(isset($discount_amount['old_coupon_log']) && !empty($discount_amount['old_coupon_log']))  {
                    $discount_amount['old_coupon_log']->save();
                }
                $userData->current_plan = $new_user_plan->plan_id;
                
                if($userData->refer_via != null && isset($userData->refer_via)) {
                    $this->user_referral($userData,$user_plan->invoice_no);
                }
                foreach($debitArr as $debit){
                   if($debit['amount'] > 0){  
                       $debit_amount = $debit['amount'];
                       $uBalance->addDebit($debit_amount,$ownerID,'amount',$adminID);
                       
                    }
                }
                $userData->next_bill_date = $new_user_plan->plan_end_date;
                $userData->save();
                //save new plan
                $invoiceNo = $this->create_invoice($new_user_plan);
                $new_user_plan->invoice_no = $invoiceNo;
                $new_user_plan->trans_id = 0;
                $new_user_plan->save();
                $this->send_mail($new_user_plan,$userData);
                //save previous plan if exists
                if(!empty($user_plan)){
                    $user_plan->save();
                }
                return true;            
            } 
        }
    }
    /**
    * this function check this user is referral via any other
    */
    public function user_referral($userData,$invoice_no)
    {

        if(!empty($userData) && !empty($invoice_no)) {
            $userPlanData = UserPlan::where('invoice_no',$invoice_no)->get(); // get all which buy for previous month
            $amount = 0;
            if($userPlanData->count() != 0) {
                $totalCost = 0;
                foreach($userPlanData as $plan){
                    $plan_pay_amount = round($plan->plan_pay_amount*$plan->referal_discount/100,2);
                    $totalCost += $plan_pay_amount;
                }
                $amount = $totalCost;
            }
            $uBalanceObj = new UserBalance;
            $referralUserData = User::where('id',$userData->refer_via)->get()->first();
            if(!empty($referralUserData)) {
                $owner_id = ($referralUserData->parent_user_id != 0) ? $referralUserData->parent_user_id : $referralUserData->id;
                $real_user_id = ($referralUserData->parent_user_id != 0) ? $referralUserData->id : $referralUserData->parent_user_id;

                $count_referral = $uBalanceObj->number_of_referral_record($owner_id,$real_user_id,$userData->id);
                if($count_referral <= 3) {
                    $uBalanceObj->addCredit($amount,$owner_id,'amount',$real_user_id,$userData->id);
                    $where = array('to_user_id'=>$userData->id,'from_user_id'=>$userData->refer_via);
                    $inviteData = $this->get_previous_balance($where);
                    if(isset($inviteData->amount) && !empty($inviteData->amount)) {
                        $amount += $inviteData->amount;
                    }
                    $update_data = array('amount'=>$amount,'status'=>2,'subscribe_date'=>date('Y-m-d'));
                    $this->update_referral_user_data($where,$update_data);
                }
            }
            
        }
    }
    /**
    * update invite table data
    */
    public function update_referral_user_data($where,$update_data)
    {
        if(Invite::where('status','<>',3)->where($where)->update($update_data)) {
           return true;
        }
    }

    /**
    * this function return days different
    */
    private function getDaysDiffFromNow($startDate,$endDate){
        $toDate = strtotime($endDate); // or your date as well
        $fromDate = strtotime($startDate);
        $datediff = $toDate - $fromDate;
        return floor($datediff/(60*60*24));
    } 

    /**
    * this function made invoice new entry
    */
    public function create_invoice($planData)
    {
        
      if(!empty($planData)) {
          $tempAddress = array();
          $toAddress = "";
          $get_card_data = $this->get_card_data($planData->user_id);
          if($get_card_data) {
            $tempAddress['first_name'] = $get_card_data->billing_first_name;
            $tempAddress['last_name'] = $get_card_data->billing_last_name;
            $tempAddress['address'] = $get_card_data->address_line_1;
            $tempAddress['city'] = $get_card_data->city;
            $tempAddress['state'] = $get_card_data['get_state']->state_name;
            $tempAddress['country'] = $get_card_data['get_country']->name;
            $tempAddress['zip_code'] = $get_card_data->zip_code;
            $toAddress = json_encode($tempAddress);
          }
          $adminAddress = Setting::where('field_key','address')->get(['field_value'])->first();
          if(!empty($adminAddress)) {
              $createData = array('bill_date'=>$planData->plan_end_date,'owner_id'=>$planData->user_id,'to_address'=>$toAddress,'from_address'=>$adminAddress->field_value,'plan_id'=>$planData->plan_id);
              
          } else {
              $createData = array('bill_date'=>$planData->plan_end_date,'owner_id'=>$planData->user_id,'to_address'=>$toAddress,'plan_id'=>$planData->plan_id);
          }
          $invoiceData = Invoice::create($createData);
          return $invoiceData['id'];
      }
    }

    /**
    * this function return user card record
    */
    public function get_card_data($owner_id) 
    {
        if(!empty($owner_id)) {
            $cardData = Card::with('get_country','get_state')->where('user_id',$owner_id)->get()->first();
            if(!empty($cardData)) {
              return $cardData;
            } else {
              return false;
            }
        }
    }

    /**
    * send mail 
    */ 
    public function send_mail($planData , $userData , $is_success = 1,$card_charge = 0) 
    {
        if(!empty($planData)) {
            $appMailer = new AppMailer;
            if($appMailer->payment_mail($planData,$userData,$is_success,$card_charge) ) {
                return true;
            } else {
                return false; 
            }
        }
    }

    /**
    * this function return  days different
    */
    public function find_days_diff($end_date , $start_date = null) 
    {
        if(!empty($start_date)) {
            $start_date = date_create($start_date);
        } else {
            $start_date = date_create();
        }
        if(!empty($end_date)) {
            $end_date = date_create($end_date);
        } else {
            return false;
        }
        $date_diff = date_diff($start_date,$end_date);
        return $date_diff->format("%R%a");
    }

    /**
    * this function  save billing log 
    */
    public function billing_log($owner_id = null , $logData = null) 
    {
        if($logData == null && !empty($owner_id)) {
            $to_day = date('Y-m-d');
            $logs = DB::table('billing_cron_log')->where('owner_id',$owner_id)->whereDate('created_at','=',$to_day)->get();
            if(count($logs) != 0) {
                return false;
            } else {
              return true;
            }
        } else if(!empty($logData)){
            DB::table('billing_cron_log')->insert($logData); 
        }
    }

    /**
    * invites previous balance 
    */
    public function get_previous_balance($where) 
    {
        if(!empty($where)) {
            $inviteData = Invite::where($where)->get()->first();
            return $inviteData;
        }
    }
    
    /**
    * check coupon applied for this user if yes then add discount
    */
    public function coupon($planData,$userData)
    {
        $first_use = 0;
        if(!empty($planData) && !empty($userData))  {
            if($planData->coupon_id != 0) {
                $where  = array('owner_id'=>$userData->id,'plan_id'=>$planData->plan_id,'coupon_id'=>$planData->coupon_id);
                $n_coupon_apply = CouponLog::where('status',3)->where($where)->orderBy('created_at','desc')->get()->count();

                $couponLogData = CouponLog::where('status',1)->where($where)->orderBy('created_at','desc')->get()->first();
                if($couponLogData->n_time_allow == $n_coupon_apply+1) {
                    $couponLogData->status = 3;
                    $couponLogData->save();
                    return false;
                }
                $reNewCouponData = new CouponLog;
                $reNewCouponData->coupon_id = $couponLogData->coupon_id;
                $reNewCouponData->owner_id = $couponLogData->owner_id;
                $reNewCouponData->discount = $couponLogData->discount; 
                $reNewCouponData->plan_id = $couponLogData->plan_id; 
                $reNewCouponData->plan_amount = $planData->plan_amount;
                $reNewCouponData->n_time_allow = $couponLogData->n_time_allow;  
            } else {
                $first_use = 1;
                $where  = array('owner_id'=>$userData->id,'status'=>0);
                $couponLogData = CouponLog::where($where)->orderBy('created_at','desc')->get()->first();
                if(empty($couponLogData)) {
                    return false;
                }
                if($couponLogData->n_time_allow == 0) {
                    return false;
                }
                $couponLogData->plan_id = $planData->plan_id;
                $couponLogData->plan_id = $planData->plan_id;
                $couponLogData->plan_amount = $planData->plan_amount;

            }
            if(!empty($couponLogData)) {
                $amount = round(($planData->plan_amount * $couponLogData->discount) / 100,2);
                $new_plan_amount = $planData->plan_amount - $amount;
                $couponLogData->plan_id = $planData->plan_id;
                $couponLogData->plan_amount = $planData->plan_amount;
                $couponLogData->discount_amount = $amount;
                $discount = array('plan_amount'=>$new_plan_amount,'plan_pay_amount'=>$amount,'coupon_id'=>$couponLogData->coupon_id);
                if($first_use == 1) {
                    $couponLogData->status = 1;
                    $couponLogData->discount_amount = $amount;
                    $couponLogData->status = 1;
                    $discount = array('plan_amount'=>$new_plan_amount,'plan_pay_amount'=>$amount,'coupon_id'=>$couponLogData->coupon_id,'coupon_log'=>$couponLogData,'discount'=>$amount);
                } else {
                    $couponLogData->status = 3;
                    $reNewCouponData->discount_amount = $amount; 
                    $reNewCouponData->status = 1;
                    $discount = array('plan_amount'=>$new_plan_amount,'plan_pay_amount'=>$amount,'coupon_id'=>$couponLogData->coupon_id,'coupon_log'=>$reNewCouponData,'old_coupon_log'=>$couponLogData,'discount'=>$amount);
                }
                return $discount;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
    * this function check current email address register before or not and if register then 
    * get customer profile id from card table and delete from sandbox account 
    * @return true if delete, false otherwise
    */ 
    public function is_registered_before()
    {
        $where  =  array('email'=>$this->user->email,'status'=>2);
        $userAndCardData = User::with('card')->where($where)->get()->first();
        if(!empty($userAndCardData)) {
            if(isset($userAndCardData['card']->auth_card_id) && $this->user->id != $userAndCardData->id) {
                $this->deleteCustomerProfile($userAndCardData['card']->auth_card_id);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
    * delete customer profile form sandbox account 
    * @return true on success otherwise false
    */
    function deleteCustomerProfile($customerProfileId)
    {
        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(env('AUTHORIZED_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(env('AUTHORIZED_TRANSACTION_KEY'));
        $refId = 'ref' . time();

          // Delete an existing customer profile  
        $request = new AnetAPI\DeleteCustomerProfileRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setCustomerProfileId( $customerProfileId );

        $controller = new AnetController\DeleteCustomerProfileController($request);
        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
                return true;
        } else {
            return false;
            /*echo "ERROR :  DeleteCustomerProfile: Invalid response\n";
            $errorMessages = $response->getMessages()->getMessage();
            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";*/
        }
        return $response;
    }
}
