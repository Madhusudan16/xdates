<div class="modal fade " id="add_email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog account-model" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add another email address</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="post" id="addEmailForm">
                            <div class="form-group">
                                <span class="border-span">
                                    <input type="email" class="form-control" id="email_add" name="email" autocomplete='off' placeholder="Enter an email address">
                                </span>
                            </div>
                            
                            <p class="info">We will send an activation link to the email address you have added. Please  check your email and follow the instructions.</p>
                            <p class="error"></p>
                            <div class="text-right">
                                <button type="button" class="btn disable-btn save" data-toggle="modal"  id="save_email">Continue</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                        </form>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade " id="add_phone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog account-model" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add another mobile phone</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="error" style="display:none"></p>
                        <form method="post" id="addPhoneForm">
                            <div class="form-group">
                                <span class="border-span">
                                    <select class="selectpicker selected-country-code countryCode"  name="countryCode">
                                        @foreach($countryCode as $code)
                                            <option  value="{{$code['code']}}">{{$code['country_name']}}</option>
                                        @endforeach
                                    </select>
                                </span>
                            </div>
                            <div class="form-group">
                                <span class="border-span">
                                    <input type="text" class="form-control" id="phone_add" name="phone" placeholder="Enter a phone number" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                                </span>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-success save" data-toggle="modal"  id="save_phone">Continue</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                                
                            </div>
                         </form>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="edit_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog account-model" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form>
                            <div class="form-group">
                                <span class="border-span">
                                    <input type="email" class="form-control" id="edit_email" name="email" placeholder="Enter an email address">
                                    <input type="hidden" class="email-id" value="">
                                </span>
                            </div>
                            <p class="info confirm-email-info">This above email address has been confirmed. If you change the email address, click the Save button below.</p>
                            <p class="info unconfirm-email-info">The above email address is not yet confirmed. Click send confirmation email below to receive another confirmation email.</p>
                            <div class="text-left">
                                <button type="button" class="btn disabled-btn save" data-toggle="modal"  id="save_edited_data">Save</button>&nbsp; &nbsp; 
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                           </div>
                            <div class="error" > </div>
                                <p class="last_p" ><u><a class="resendmail" dataId="" email="" href="javascript:void(0)">Resend email </a></u> (please wait at least 5 minutes before requesting a new email and also check your spam folder).</p>
                        </form>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade popup" id="edit_details_phone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog account-model" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit details</h4>
            </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="post" id="editPhoneForm">
                                <div class="form-group">
                                        <span class="border-span">
                                            <select class="selectpicker selectedCountry" name="country_code" >
                                                @foreach($countryCode as $code)
                                                    <option value="{{$code['code']}}" >{{$code['country_name']}}</option>
                                                @endforeach
                                            </select>
                                        </span>
                                    </div>
                                <div class="form-group">
                                    <span class="border-span">
                                        <input type="text" class="form-control" id="edit_phone" placeholder="(456) 789-0123" name="phone" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                                        <input type="hidden" value="" name="id" id="phone-id">
                                        <input type="hidden" value="" name="status" id="isactive">
                                    </span>
                                </div>
                            </form> 
                            <p class="info ">
                                The above number is not yet confirmed. Click send activation code to receive your code in a text message.
                            </p>
                            <p class="error"></p>
                            <div class="text-left">
                                <button type="button" class="btn save_edited_phone" data-toggle="modal"  onclick="savePhone(1,this)">Save</button>&nbsp; &nbsp; 
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                            <p class="last_p"><u><a href="javascript:void(0)" class="resend_mobile_code" id="">Resend code </a></u> (please wait at least 5 minutes before requesting a new code).</p>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade popup" id="details_phone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Edit details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form name="edit_phone_form" id = "edit_phone_form">
                                <div class="form-group">
                                        <span class="border-span">
                                            <select class="selectpicker edit_country_code " name="country_code">
                                               @foreach($countryCode as $code)
                                                    <option value="{{$code['code']}}" >{{$code['country_name']}}</option>
                                                @endforeach
                                            </select>
                                        </span>
                                    </div>
                                <div class="form-group">
                                    <span class="border-span">
                                        <input type="text" class="form-control" name="phone" id="edit_phone_save" placeholder="(456) 789-0123" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                                    </span>
                                </div>
                           
                            <p class="info_green noti_cofi">The above number is confirmed.</p>
                            <p class="error " style="color:red"></p>
                            <div class="form-group">
                                    <p class="info ">Would you like this number to receive text notifications?</p>
                                        <span class="border-span">
                                            <select class="selectpicker notification_receive" name="notification_receive">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                                
                                            </select>
                                        </span>
                                    </div>
                                    <input type="hidden" value="" name="id" class="edit_confirm_number">
                            </form>
                            
                            <div class="text-right">
                                
                                <button type="button" class="btn save confirm-save-btn"  data-toggle="modal" noti-active="" number="" country="" onclick="savePhone(2,this)" >Save</button>&nbsp; &nbsp; 
                                <button type="button" class="btn btn-danger black" data-dismiss="modal">cancel</button>
                                
                                
                            </div>
                        
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade popup" id="confirm_code" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Enter your confirmation code</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>You should receive a text at <span class="recent-phone"></span> with your confirmation code soon.</p>
                            <form>
                                <div class="form-group">
                                    <span class="border-span">
                                        <input type="text" class="form-control" id="verificationCode" placeholder="Enter your confirmation code">
                                    </span>
                                </div>
                            </form>
                            <p class="code_error" style="display:none">You entered an incorrect code. Try again.</p>
                           
                            
                            <div class="text-right">
                                
                                <button type="button" class="btn save" data-toggle="modal" id="checkCode" >Continue</button>&nbsp; &nbsp; 
                                <button type="button" class="btn btn-danger cancel_click" data-dismiss="modal">cancel</button>
                                
                                
                            </div>
                            <p class="last_p"><u><a href="javascript:void(0)" class="resend_mobile_code" id="">Resend code </a></u> (please wait at least 5 minutes before requesting a new code).</p>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade diseble-popup" id="edit_details_save" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Edit details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form>
                                <div class="form-group">
                                    <span class="border-span">
                                        <input type="email" class="form-control" id="needConfirmationEmail" placeholder="Enter an email address" name="email">
                                        <input type="hidden" value="" class="edited_email_id">
                                    </span>
                                </div>
                            </form>
                            <p class="info">The above email address is not yet confirmed.  Click send confirmation email below to receive another confirmation email.</p>
                            
                            <div class="text-left">
                                
                                <button type="button" class="btn btn-success save" data-toggle="modal" data-target="#confirm_email">Save</button>&nbsp; &nbsp; 
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                                
                                
                            </div>
                            <p class="last_p"><u><a class="resendmail" dataId="" email="" href="javascript:void(0)">Resend email </a></u> (please wait at least 5 minutes before requesting a new email and also check your spam folder).</p>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirm_email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Confirm your email address</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <p class="info_1">We have sent an activation link to the email address you provided. Please check your email and follow the instructions to confirm your email address.</p>
                            <p>if you do not find the email after 5 minutes, please check your spam folder.</p>
                            <div class="text-right">
                                
                                    <!-- <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="window.location.reload()" data-toggle="modal" >Cancel</button> -->
                                     <button type="button" class="btn btn-danger" data-dismiss="modal" data-toggle="modal" >Cancel</button>
                                 
                            </div>
                            <p class="last_p"><u><a class="resendmail" dataId="" email="" href="javascript:void(0)">Resend email </a></u> (please wait at least 5 minutes before requesting a new email and also check your spam folder).</p>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade {{$is_show_conformation_modal or ''}}" id="thenkyou" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Thanks for confirming your email address.</h4>
                </div>
        
            </div>
        </div>
    </div>
    <div class="modal fade" id="remove_email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Remove email</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong class="confirm-email"></strong></p>
                            <p class="info_1">This email will no longer receive notification from X-Dates.</p>
                            <input type="hidden" value="" class="deletedId">
                            <div class="text-right">
                                    <button type="button" class="btn btn-danger" id="removeEmail">Remove Email</button>
                                    <button type="button" class="btn btn-danger black" data-dismiss="modal">Cancel</button>
                                 
                            </div>
                            
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="thanks" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Thanks for confirming your phone number.</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center">
                                    <button type="button" class="btn btn-danger" onClick="window.location.href=window.location.href">save</button>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="remove_phone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Remove number</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong class="confirm-phone"></strong></p>
                            <p class="info_1">This phone number  will no longer receive notifactions from X-Dates.</p>
                            <input type="hidden" value="" class="remove-phone-number">
                            <div class="text-right">
                                    <button type="button" class="btn btn-danger deleteNumber" >Remove Phone Number</button>
                                    <button type="button" class="btn btn-danger black" data-dismiss="modal">Cancel</button>
                                 
                            </div>
                            
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>