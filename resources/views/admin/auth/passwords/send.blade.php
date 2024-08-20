@extends('back')

<!-- Main Content -->
@section('main')
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box_bg">
                <h2 class="title text-center">Almost there...</h2>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="forgot_massage text-center">
                           <p>Instructions to <strong>reset your password</strong> has been sent to the <strong>email address</strong> provided, if an account with that email address exists.</p>

                        </div>

                    </div>
                </div>
                <div class="info_text text-center">
                    <h5>A note about spam filters</h5>
                    <p>If you don't get an email from us within a few minutes please be sure to check your spam filter.<br><strong>The email will be coming from <a href="mailto:no-reply@xdates.net"><em>no-reply@xdates.net</em></a></strong></p>
                </div>
            </div>
            <p class="text_last">Never mind, <a href="{{url('/admin/login')}}"><u>Take me back to log in screen</u></a></p>
        </div>
    </div>
@endsection
