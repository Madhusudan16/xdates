@extends('emails.mailtemplate')
@section('title','X-Dates: free trial extension request')
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
                                    <td align="left" style="padding: 20px 0 0 0; font-size: 15px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #323232;" class="padding">
                                        <h2>X-Dates: FREE Trial Extension Request</h2>

                                        <p>An X-Dates team member has requested a free trial extension for a trial account. Details are as follows: </p>
                                        <p>
                                        <ul >
                                        <li>Requestor: {{$data['requestor_name'] or ''}}</li>
                                        <li>Trial Account: {{$data['com_name'] or ''}}</li>
                                        <li>Note: {{$data['note'] or ''}}</li>
                                        </ul>
                                        </p>
                                        <p><a href='{{ url("admin/trial-extend") }}'>Click here</a> to make a decision on this request.</p>
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
