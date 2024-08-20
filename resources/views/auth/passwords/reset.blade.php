@extends('front')

@section('main')

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box_bg">
            <h2 class="title text-center">Reset Password</h2>
            <div class="row">
                <div class="col-md-offset-3 col-md-6 col-sm-6 col-sm-offset-2 col-xs-10 col-xs-offset-1">
                    <div class="reseat_form text-right">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                        	{!! csrf_field() !!}

                        	<input type="hidden" name="token" value="{{ $token }}">
                        	
                        	 <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
	                            <label for="email" class="col-sm-4 control-label">Email address</label> 
	                            <div class="col-sm-7">
	                                <span class="border-span"><input readonly="readonly" type="email" class="form-control" name="email" id="email" value="{{ $email or old('email') }}"></span>
	                                @if ($errors->has('email'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('email') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>
                        
                        	
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="password" class="col-sm-4 control-label">New password</label>
                                <div class="col-sm-7">
                                    <span class="border-span"><input type="password" name="password" class="form-control" id="password"></span>
                                    @if ($errors->has('password'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('password') }}</strong>
	                                    </span>
	                                @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password_confirmation" class="col-sm-4 control-label">Confirm new password</label>
                                <div class="col-sm-7">
                                    <span class="border-span"><input type="password" class="form-control" id="password_confirmation" name="password_confirmation"></span>
                                     @if ($errors->has('password_confirmation'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
	                                    </span>
	                                @endif
                                </div>
                            </div>

                            

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-9">
                                    <button type="button" class="btn btn-disebal" id="changePasswordbtn">change password</button>
                                </div>
                            </div>
                        </form>

                    </div>

                </div>
                <div class="col-md-offset-0 col-sm-offset-0 col-xs-offset-1 col-md-3 col-sm-4 col-xs-10 padding-left-none password_intro">
                    <div class="left_text">
                        <p>Passwords much be at least 8 characters in length and are case sensitive.</p>
                        <p>You must include letters and numbers and, optionally, may include symbols.</p>
                    </div>
                </div>
            </div>

        </div>
        <p class="text_last">Never mind, <a href="{{url('/login')}}"><u>Take me back to log in screen</u></a></p>
    </div>
</div> 
@endsection

@section('footerscripts')  
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
 {!! $validator !!}

 <script>
    $(document).ready(function(){
        $("#changePasswordbtn").prop('disable',true);
        $("#password,#password_confirmation").bind('blur focus',function(){ 
               if(!$("#password,#password_confirmation").closest(".form-group").hasClass('has-error') && $("#password").val() != "" && $("  #password_confirmation").val() != "" ){ // check all form value is valid or not
                     $("#changePasswordbtn").prop('disable',false);
                     $("#changePasswordbtn").removeClass('btn-disebal'); // remove disable class from button 
                     $("#changePasswordbtn").addClass('btn-success');  // add success class to button
                     $("#changePasswordbtn").css('cursor','pointer');
                     $("#changePasswordbtn").attr('type','submit');
               } else {
                     $("#changePasswordbtn").attr('type','button');
                     $("#changePasswordbtn").prop('disable',false);
                     $("#changePasswordbtn").addClass('btn-disebal');
                     $("#changePasswordbtn").removeClass('btn-success');
                     $("#changePasswordbtn").css('cursor','default');
               }
         });
    });
 </script>
@endsection
