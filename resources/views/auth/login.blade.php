@extends('front')

@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="box_bg send_email">
            <h2 class="title text-center">Sign in</h2>
            <div class="row">
                <div class="col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8 col-xs-offset-1 col-xs-10">
                    <div class="login_form">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {!! csrf_field() !!}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="inputEmail" class="col-sm-3 control-label">Email</label>

                                <div class="col-sm-7">
                                    <span class="border-span"><input type="text" class="form-control" name="email" id="inputEmail" value="{{ old('email') }}"></span>
                                </div>
                            </div>

                            <div class="form-group margin_none {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="inputPassword" class="col-sm-3 control-label">Password</label>

                                <div class="col-sm-7">
                                    <span class="border-span"><input type="password" id="inputPassword" class="form-control" name="password">
                                    </span>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <span>{{ $errors->first('password') }}</span>
                                        </span>
                                    @endif
                                </div>
                                <a class="forget" href="{{ url('/password/reset') }}"><u>Forgot?</u></a>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-10">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember"> Remember me
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('email'))
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-7">
                                        <span class="help-block invalid-data" style="color:red">
                                            <span>{{ $errors->first('email') }}</span>
                                        </span>
                                    </div>
                            </div>
                            @endif
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-3">
                                    <button type="submit" class="btn btn-success sign-in">Sign in</button>
                                </div>
                                <a class="alredy_account" href="{{ url('/register') }}"><u>Don’t have an account?</u></a>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 google_btn_sec text-center">
                                    <span class="or_text">OR</span>
                                    <a href="{{url('auth/google/signin')}}" class="google-btn"><img src="{{url('assets/images/google.jpg')}}">&nbsp; &nbsp;Sign IN with google</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(session()->has('is_show'))
   <?php $is_show =  session('is_show'); ?> ?>
@endif
@endsection
@section('footerscripts')

<div class="modal fade {{ $is_show or '' }}" id="reactive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Your account restored!</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Your account has been successfully restored, now you can login to Xdate.</p>
                            <div class="text-right">
                <form>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
 {!! $validator !!}
 <script>
    $(document).ready(function(){
        $("#inputEmail,#inputPassword,.sign-in").on('focus click',function(){
            $(".invalid-data").hide();
        });
        if($("#reactive").hasClass('reactive-account')) {
             $("#reactive").modal('show');
             setTimeout(function(){
                $("#reactive").modal('hide');
             },5000);
        }
    });
 </script>
@endsection
