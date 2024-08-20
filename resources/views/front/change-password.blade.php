@extends('front')

@section('main')

<meta name="_token" content="{!! csrf_token() !!}"/>
<div class="row">
	@include('front.partials.setting-menu')
	<div class="col-lg-9 col-md-9 col-sx-9 account-right">
	
	    <div class="right-profile">
	        
	        <div class="success-pass-mas">Password changed successfully.</div>
           <div class="error-pass-mas">Current password does not match.</div>
           <div class="white-space"></div>
          <div class="row">
           		 <div class="padding-cont clearfix disable-after-submit">
            		<h3 class="heding">Change Password</h3>
            			<div class="col-md-6 col-sm-6 padding-right-none ">
           					<div class="fild-sec">
                      <form action="save-password" method="post" id="changePasswordForm"  >
           							  <div class="form-group row">
                                        <label for="inputPassword3" class="col-sm-4 form-control-label">Current Password</label>
                                        <div class="col-sm-8">
                                            <span class="border-span"><input type="password" class="form-control" id="inputPassword3" placeholder="Current Password" name="password"></span>
                                        </div>
                                      </div>
                                      <div class="form-group row">
                                        <label for="inputPassword4" class="col-sm-4 form-control-label">New Password</label>
                                        <div class="col-sm-8">
                                            <span class="border-span"><input type="password" class="form-control" id="inputPassword4" placeholder="New Password" name="new_password"></span>
                                        </div>
                                      </div>
                                        <div class="form-group row">
                                        <label for="inputPassword5" class="col-sm-4 form-control-label">Confirm Password</label>
                                        <div class="col-sm-8">
                                            <span class="border-span"><input type="password" class="form-control" id="inputPassword5" placeholder="Confirm Password" name="password_confirmation"></span>
                                        </div>
                                      </div>
                                        <div class="form-group row">
                                        <label for="inputPassword3" class="col-sm-4 form-control-label display-none">&nbsp;</label>
                                        
                                        <div class="col-sm-8">
                                            <button type="button" class="btn btn-danger" id="cancelChangePassword">cancel</button>
                                            <button type="button" class="btn btn-disebal" id="changePasswordbtn">Change Password</button>
                                        </div>
                                      </div>
                                     </form>
           					      </div>
           				     </div>

                                         <div class="col-md-6 col-sm-6 padding-left-none">
                                            <div class="left_text">
                                               <p>Passwords much be at least 8 characters in length and are case sensitive.</p>
                                               <p>You must include letters and numbers and, optionally, may include symbols.</p>
                                            </div>
                                         </div>
           		</div>
            </div>
            </div>
			
			
	   </div>
	</div>
</div>
 
@endsection
@section('footerscripts')  
  {!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
    {!! $validator !!}
    <script>
     $(document).ready(function(){
        $(".error-pass-mas").hide();
        $.ajaxSetup({
           headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });

         $("#inputPassword5,#inputPassword3,#inputPassword4").bind('blur focus',function(){ 
               if(!$("#inputPassword5,#inputPassword3,#inputPassword4").closest(".form-group").hasClass('has-error') && $("#inputPassword3").val() != "" && $("  #inputPassword4").val() != "" && $("#inputPassword5").val() != ""){ // check all form value is valid or not
                     $("#changePasswordbtn").removeClass('btn-disebal'); // remove disable class from button 
                     $("#changePasswordbtn").addClass('btn-success');  // add success class to button
                     $("#changePasswordbtn").css('cursor','pointer');
               } else {
                     $("#changePasswordbtn").addClass('btn-disebal');
                     $("#changePasswordbtn").removeClass('btn-success');
                     $("#changePasswordbtn").css('cursor','default');
               }
         });
        
        $("#changePasswordbtn").click(function(){  // when button click  class this function
            var data = [];
            if(!$(".form-group").hasClass('has-error') && $("#inputPassword3").val() != "" && $("#inputPassword4").val() != "" && $("#inputPassword5").val() != "" && $(".success-pass-mas").is(':hidden')){
               $(this).addClass('btn-success');   // add class success when all form value is valid 
               var data = $("#changePasswordForm").serialize();  // get all form data using serialize method
               $.post("/save-password",{'data':data}, function(response){  // call  savaPassword method of MyprofileController 
                    $(".success-pass-mas").show();   // show success message 
                    $(".error-pass-mas").hide();      // if error message is currently display on screen then hide it
                    $(".disable-after-submit").addClass('diseble-sec'); // disable form section
                    $("#changePasswordbtn").removeClass('btn-success'); // remove success class from button
                    $("#changePasswordbtn").addClass('btn-disebal'); // add disebal class in button
                    if(response.page_url) {
                        window.location.href = response.page_url;
                    }
                    
              }).fail(function(error_text) {
                    $(".error-pass-mas").show(); // show error message 
                    $(".success-pass-mas").hide(); // if success message is currently display on screen then hide it
               });
            }
           
        });
              $("#cancelChangePassword").click(function(){
                  window.location.href = 'myprofile';
              });
     });
      
    </script> 
@endsection