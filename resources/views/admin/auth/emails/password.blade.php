@extends('emails.mailtemplate')
@section('title','X-Dates - Reset your password')
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
                                            X-Dates: Reset Your Password
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding">Forgot your password? No worries. To create a new password, click the following link :<a href="{{ $link = url('admin/password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> Create a new password</a><br>Link doesn't work? Copy/paste the following link into your web browser's address bar:<a href="{{$link = url('admin/password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a> This is part of the procedure to create a new password. If you DID NOT request a new password, please ignore this email and your password will remain the same.
                                    <br>~The X-Dates Team</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
@endsection
