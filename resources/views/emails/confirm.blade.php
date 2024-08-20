@extends('emails.mailtemplate')
@section('title','X-Dates - sign up confirmation')
@section('content')
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" class="responsive-table">
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <!-- COPY -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="left" style="font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding">
                                        X-Dates: Sign-up Confirmation
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding: 5px 0 0 0; font-size: 14px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" >

                                    <p>Thanks for signing-up with X-Dates! We appreciate you taking the time to give us to try. Please click the following activation link to confirm your account and get started with X-Dates: <a href='{{ url("register/confirm/{$user->token}") }}'>{{ url("register/confirm/{$user->token}") }}</a></p>
                                    <p>If the above URL does not work, try copying and pasting the URL into your browser.</p>
                                    <p>To contact us, email <a href="mailto:support@xdates.net" >support@xdates.net</a> or call us at +1 423-414-2500.</p>
                                    <p>~ The X-Dates Team</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
