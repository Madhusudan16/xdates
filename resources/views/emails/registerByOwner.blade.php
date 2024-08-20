@extends('emails.mailtemplate')
@section('title','X-Dates: welcome | login credentials')
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
                                    <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding"> Hello, {{$user->first_name}}!<br>
						        	<p>You have been added to the {{$user->com_name}} {{env('APP_NAME')}} account. <br>
						        	Login credentials are as follows:</p>
						        	<p>
                                    URL: <a href="{{url('/')}}">{{url('/')}}</a><br>
						        	Email:  <a name="myname" style="color:#666666">{{$user->email}}</a><br>
						        	Password: {{$user->decrypt_pass}}
                                    </p>
                                    <p>Happy selling!<br>
                                    ~ The X-Dates Team</p>
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
