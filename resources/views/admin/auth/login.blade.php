@extends('back')

@section('main')

<div class="row">
    <div class="col-md-12">
        <div class="box_bg send_email">
            <h2 class="title text-center">Administrator Sign in</h2>
            <div class="row">
                <div class="col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8 col-xs-offset-1 col-xs-10">
                    <div class="login_form">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/admin/login') }}"> 
                        {!! csrf_field() !!}

                            <div class="form-group">
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
                                <a class="forget" href="{{ url('/admin/password/reset') }}"><u>Forgot?</u></a> 
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
                                
                            </div> 
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
@endsection
@section('footerscripts')  
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
 {!! $validator or ''!!}

 <script>
    $(document).ready(function(){
        $("#inputEmail,#inputPassword,.sign-in").on('focus click',function(){
            $(".invalid-data").hide();
        });
    });
 </script>
@endsection
