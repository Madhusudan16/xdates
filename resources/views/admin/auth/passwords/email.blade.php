@extends('back')

<!-- Main Content -->
@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="box_bg">
            <h2 class="title text-center">Forgot your password?</h2>
            @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
            @endif
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <div class="forgot_form text-center">
                       <p>Enter your email address below and we'll send password reset instructions.</p>
                        <form class="form-inline" autocomplete="off" role="form" method="POST" action="{{ url('/admin/password/email') }}">
                        	{!! csrf_field() !!}
                              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="sr-only" for="exampleInputEmail3">Email address</label>
                                <span class="border-span">
                                    <input type="email" class="form-control email"  name="email" value="{{ old('email') }}">
                                </span>
                                @if($errors->has('invalid'))
                                    <span class="help-block error-help-block invalid-user text-left">
                                       <span>{{ $errors->first('invalid') }}</span>
                                     </span>
                                @endif
                                @if ($errors->has('email') )
                                    <span class="help-block invalid-user">
                                       <span>{{ $errors->first('email') }}</span>
                                     </span>
                                @endif
                              </div>
                              <button type="submit" class="btn btn-success forgot-btn">
                                    submit
                              </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="info_text text-center">
                <h5>A note about spam filters</h5>
                <p>If you don't get an email from us within a few minutes please be sure to check your spam filter.<br><strong>The email will be coming from
                    <a href="mailto:no-reply@xdates.net" class="italic"><em>no-reply@xdates.net</em></a></strong>
                </p>
            </div>
        </div>
        <p class="text_last">Never mind, <a  href="{{ url('/admin/login') }}"><u>Take me back to log in screen</u></a></p>
    </div>
</div>

@endsection

@section('footerscripts')
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}

{!! $validator !!}
<script>
    $(document).ready(function(){
        $(".forgot-btn").click(function(){
            $(".invalid-user").hide();
            console.log('here');
        });

        $(".email").focus(function(){
            console.log('ldlfd');
            $(".invalid-user").hide();
        });
    });
</script>
@endsection
