@extends('front')
@section('main') 

<meta name="_token" content="{!! csrf_token() !!}"/>
<div class="row">
	@include('front.partials.setting-menu')



	<div class="col-md-9 account-right">
		@if($curModAccess['view'])
	    <div class="right-profile my-profile">
	        <div class="white-space @if(isset($need_timezone)) require_data @endif">
	        	@if(isset($need_timezone) || empty($user->com_name)) 
	        		<h3 class="dynamic_msg">@if(empty($user->com_name) && empty($user->choosed_timezone))Please enter your Company name and  select a Time Zone.@elseif(empty($user->com_name) && $user->user_type == 1)Please enter your Company name. @elseif(empty($user->choosed_timezone))Please select a Time Zone. @endif </h3>
	        	@endif
	        </div>
	        <div class="row">
	            <div class="col-md-6 col-sm-6 padding-right-none">
	                <div class="fild-sec">
	                    <form class="form-horizontal" method="post" role="form" enctype="multipart/form-data">

	                        <div class="form-group">
	                            <label class="control-label col-sm-3" for="com_name">Comapany</label>
	                            <div class="col-sm-9">
	                              <span class="border-span"><input type="text" class="form-control" id="com_name" name="com_name" value="{{$user->com_name or ''}}" @if($user->user_type != 1 ) readonly @endif @if(!empty($user->com_name)) {{$need_timezone or ''}} @endif></span>
	                                <span class="icon"><img src="{{asset('assets/images/note_icon.png')}}"></span>
	                            </div>
	                        </div>

	                        <div class="form-group">
	                            <label class="control-label col-sm-3" for="pwd">First name</label>
	                            <div class="col-sm-9">
	                              <span class="border-span"><input type="text" class="form-control" id="f_name" name="first_name" value="{{$user->first_name or ''}}" {{$need_timezone or ''}} )></span>
	                                <span class="icon"><img src="{{asset('assets/images/note_icon.png')}}"></span>
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="control-label col-sm-3" for="pwd">Last name</label>
	                            <div class="col-sm-9">
	                              <span class="border-span"><input type="text" class="form-control" id="l_name" name="last_name" value="{{$user->last_name or ''}}" {{$need_timezone or ''}}></span>
	                                 <span class="icon"><img src="{{asset('assets/images/note_icon.png')}}"></span>
	                            </div>
	                          </div>
	                          <div class="form-group">
	                            <label class="control-label col-sm-3" for="email">Email</label>
	                            <div class="col-sm-9">
	                              <span class="border-span"><input type="email" class="form-control" id="email" name="email" value={{$user->email or ''}} {{$need_timezone or ''}}></span>
	                                 <span class="icon"><img src="{{asset('assets/images/note_icon.png')}}"></span>
	                            </div>
	                          </div>
	                        <div class="form-group">
	                            <label class="control-label col-sm-3" for="email">Time Zone</label>
	                            <div class="col-sm-9">
	                                <span class="border-span full-width">
	                                    <select class="selectpicker" id="timeZone" name="choosed_timezone">
	                                    	<option >select timezone</option>
	                                   		@foreach ($timezones as $timezone)
											    <option value={{$timezone->timezone_value}} @if($user->choosed_timezone == $timezone->timezone_value) {{"selected"}}  @endif>{{ $timezone->timezone_name }}</option>
											@endforeach
	                                    </select>
	                                </span>
	                            </div>
	                        </div>
	                        <div class="form-group text-right change_pass">
	                            <button type="button" class="btn btn-success btn-change-psw">Change Password</button>
	                        </div>
	                    </form>
	                </div>
	            </div>

	            {{ Form::open(array('class'=>'form-horizontal imageUploadForm',  'files' => true)) }}
	            <div class="col-md-6 col-sm-6 padding-left-none img_sec">
	                <div class="pic-sec">
	                     <div class="form-group heding">
	                         <label class="control-label heding-label" for="">Upload avatar</label>
	                    </div>

	                    <div class="images_sec clearfix">

	                        <div class="profile-pic">
	                            @if(!empty($user->profile_image))
	                           	 	<div class="images"><span><img src={{url(config('constants.FILEUPLOAD').$user->profile_image)}} id="profileImage" width="85" height="85"></span>
	                           	 	<span class="remove_avatar"> <a href="javascript:void(0)" class="removeAvatar" id="remove_image">remove avatar</a></span>
	                           	 	</div>
	                           	@else
	                           		<div class="images"><span><img src={{config('constants.FILEUPLOAD').''.config('constants.DEFAULT_PROFILE') }} id="profileImage" width="85" height="85"></span>
	                           		<span class="remove_avatar_default"> <a href="javascript:void(0)" class="removeAvatar" id="remove_image">remove avatar</a></span>
	                           		</div>
	                           	@endif

	                        </div>
	                        <input type="file" name="profile_image" id="file">
	                     
	                    </div>
	                </div>
	                {{ Form::close() }}
	                <div class="account_cancel {{$is_cancel or '' }} ">
	                @if($curModAccess['cancel_account'])
	                
	                	<form class="form-inline" role="form" id="accountCancel">
	                        <div class="form-group">
	                            <label for="email">Cancel Account</label>
	                            <span class="border-span"> <input type="email" name="email" class="form-control" id="cancel-account-email" placeholder="Verify email" autocomplete="off"></span>
	                        </div>
	                      <button type="button" class="btn btn-primary cancel-account-btn" data-toggle="modal" >Confirm</button>
	                      <div class="row">
	                      <div class="col-md-12 col-sm-12 error-massage">
	                      <span class="error" style="color:#d21a26;"></span>
	                  </div>
	              </div>
	                    </form>
	                    <p>enter email and press confirm to cancel your account</p>
	                 
	                 @endif

	                </div>

	            </div>


	        </div>
	      <!--   @if(isset($need_timezone))
	        	<div  class="access_prevent_msg"><h3 style=" color:red; text-align: center; font-size:14px; px">Please select Time zone and insert Company name to access further.</h3></div>
	        @endif -->
	    </div>
	    <div class="loader">
            <img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading..">
        </div>
        @endif
	</div>
</div>

@endsection


@section('footerscripts')
<script type="text/javascript">
		var default_image_path = "{{config('constants.FILEUPLOAD').''.config('constants.DEFAULT_PROFILE') }}";
		$(document).ready(function(){
			$.ajaxSetup({
			   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
			});
			$(".remove_avatar_default").hide();
		});

		$("#f_name,#l_name,#email,#com_name").change(function(){

        var fieldName = $(this).attr('name');
        var fieldvalue= $(this).val();
        fieldvalue = fieldvalue.trim();
        var data = $.makeArray();
        setValues(fieldName,fieldvalue);

      });
      // user changed timezone
      $("#timeZone").change(function(){

        var fieldName = $(this).attr('name');
        var fieldvalue= $(this).val();
        setValues(fieldName,fieldvalue);
      });
      $("#file").change(function(){
			var fieldvalue = {};
        	var fieldName = $(this).attr('name');
        	var file_data = new FormData($(".imageUploadForm")[0]);
        	updateData(file_data,true);
      });
	  $(".btn-change-psw").click(function(){
         window.location.href = 'change-password';
      });
	  $(document).ready(function(){

        $( "#accountCancel" ).validate({
            errorElement: 'span',
            errorClass: 'help-block error-help-block',

            errorPlacement: function(error, element) {
                
                if (element.parent('.input-group').length || element.parent('.border-span').length ||
                    element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {

                	//$(".error").append(error);
                    error.insertBefore('.error');
                   // else just place the validation message immediatly after the input
                } else { 
                	//$(".error").html(error);
                	error.insertBefore('.error');
                    //error.insertAfter(element);
                }
            },
            highlight: function(element) {
                
                $(element).closest('.form-group').addClass('has-error'); // add the Bootstrap error class to the control group
            },  
              rules: {

                email: {
                    required: true,
                    email: true
                },
               },
              messages: {
               
                email: {
                    required : "The email field is required.",
                    email: "The email field is not valid."
                }
              }
         });
            $(".cancel-account-btn").click(function(){
                    if($("#accountCancel").valid()){ 
                    	var email = "{{$user->email}}";
                     	if($("#cancel-account-email").val() == email) {
                        	$("#cancel_account").modal('show');
                    	} else {
                    		$(".error").html("This email is not belong to you");
                    	}
                    }
            });
            $("#cancel-account-email").on('focus',function(){
            	$('.error').html("");
            });

            $("#remove_image").click(function(){
            	$("#avatar_remove").modal('show');
            });
            
    });

	</script>
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
{!! HTML::script(asset('assets/js/profile-manage.js')) !!}

<div class="modal fade" id="cancel_account" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <div class="modal-dialog account-model" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Are you sure want to cancel your account?</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <span>We are really sad to see you go</span>
                        <p class="1p">Maybe we should discuss the matter. if this is an option, please contact our <a href="mailto:{{$customer_support_number or ''}}">customer support team</a>.</p>
                        <p>Cancelling your account will stop all future billing, but you may continue to access the account until the end of you billing cycle. After that, your account will be deactivated. You will not be able to login again and your data will be deleted form our servers.</p>
                        <div class="text-right">
                            <button type="button" class="btn btn-success btn-text-success" data-dismiss="modal">No, do not cancel my account.</button>
                                <button type="button" onclick="cancelAccount()" class="btn btn-danger btn-text-danger cancel-account-cmf">Yes, I understand. Cancel my account.</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 
	this modal confirm that you really want to remove avatar
	-->
<div class="modal fade" id="avatar_remove">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Confirmation </h4>
      </div>
      <div class="modal-body">
        <p>Do you really want to remove this profile image?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary " onclick="removeAvatar()" >Confirm</button>
      </div>
    </div>
  </div>
</div>
@endsection
