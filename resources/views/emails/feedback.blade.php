@extends('emails.mailtemplate')
@section('title',"X-Dates - user feedback (#$mail_content[number_of_feedback])")
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
                                    <td align="left" style="padding: 20px 0 0 0; font-size: 15px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding">
                                    <p>
                                         Name: {{$mail_content['name'] or ''}} <br>
                                         Email: {{$mail_content['email'] or ''}} <br>
                                         Role: {{$mail_content['role'] or ''}}     <br>
                                    </p>
                                    <p>
                                        Message: <br>
                                        -------------------------------<br>
                                        {{$mail_content['data'] or ''}}
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
