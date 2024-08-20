@extends('emails.mailtemplate')
@section('title','X-Dates: 30-day trial extension')
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
                                    <td align="left" style="padding: 20px 0 0 0;  line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #323232;" class="padding">
                                    <p>Great news!</p><br\>
                                    <p>Your X-Dates FREE trial has been extended 30 days! We're glad you have decided to continue testing our software and hope you think it's as great as we do!</p><br\>
                                    <p>~The X-Dates Team</p>
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
