@extends('emails.mailtemplate')
@section('title','Cancel Account')
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
                                    <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding">
                                        Dear Owner {{$userData['owner_name']}} ,
                                        <p> <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;You have cancelled your account, if you want to reactive your account please contact to X-Dates owner in {{$userData['restoreIn'] or 30}} days. After {{$userData['restoreIn'] or 30}} days your account detail remove from system.</p>
                                        <p>
                                            X-Dates mail Address : {{$userData['owner_email']}}
                                        </p>
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
