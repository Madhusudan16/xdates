@extends('back')

@section('main')

<div class="sign_up_section">
    <div class="row">
        <div class="col-md-6 col-sm-6">
            <div class="row">
                <div class="col-md-11">
                    <div class="left_section">
                        <h2 class="form_title">Sign up for your free account</h2>
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                        	{!! csrf_field() !!}
                              <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-sm-5 control-label">Your Name</label>
                                <div class="col-sm-7">
                                    <span class="border-span"><input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"></span>
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <span>{{ $errors->first('name') }}</span>
                                        </span>
                                    @endif
                                </div>
                              </div>
                            <div class="form-group{{ $errors->has('com_name') ? ' has-error' : '' }}">
                                <label for="com_name" class="col-sm-5 control-label">Your company name</label>
                                <div class="col-sm-7">
                                    <span class="border-span"><input type="text" name="com_name" class="form-control" value="{{ old('com_name') }}" id="com_name" ></span>
                                    @if ($errors->has('com_name'))
                                        <span class="help-block">
                                            <span>{{ $errors->first('com_name') }}</span>
                                        </span>
                                    @endif
                                </div>
                              </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-sm-5 control-label">Your email address</label>
                                <div class="col-sm-7"> 
                                    <span class="border-span"><input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}"></span>
                                    @if ($errors->has('email'))
	                                    <span class="help-block">
	                                        <span>{{ $errors->first('email') }}</span>
	                                    </span>
	                                @endif
                                </div>
                              </div>
                              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-sm-5 control-label">Choose a password</label>
                                <div class="col-sm-7">
                                    <span class="border-span"><input type="password" name="password" class="form-control" id="password"></span>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <span>{{ $errors->first('password') }}</span>
                                        </span>
                                    @endif
                                </div>
                              </div>
                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="password_confirmation" class="col-sm-5 control-label">Confirm password</label>
                                <div class="col-sm-7">
                                    <span class="border-span"><input type="password" name="password_confirmation" class="form-control" id="password_confirmation"></span>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <span>{{ $errors->first('password_confirmation') }}</span>
                                        </span>
                                    @endif
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-sm-12">
                                  <button type="submit" class="btn btn-danger">Sign in</button>
                                </div>
                              </div>
                        </form>
                        <p>By clicking the "Sign up" button, you accept our <a href="#"><u>Terms of Service</u></a> and <a href="#"><u>Privacy Policy</u></a>.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6">
            <div class="right_section">
                <h3 class="info_text">No billing information required at this point</h3>
                <ul>
                    <li> At the end of your trial period, we will ask you for your billing information to continue using X-Dates.</li>
                    <li>If you do not wish to continue using X-Dates after your trial period you won't  be charged.</li>
                    <li>You can upgrade, downgrade, or cancel any time.</li>
                </ul>
                <a class="account" href="{{ url('/login') }}"><u>Already have an account?</u></a>
            </div>
        </div>
    </div>
</div>
             
@endsection

@section('footerscripts')  
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
 {!! $validator !!}
@endsection
