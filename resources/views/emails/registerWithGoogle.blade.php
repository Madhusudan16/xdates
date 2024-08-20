@extends('emails.mailtemplate')
@section('title','User Password')
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
                                   
                                </tr>
                                <tr>
                                    <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding">Hello, {{$user->first_name}}!<br><br>
                                    You have been added to the {{$user->com_name}} {{env('APP_NAME')}} account. <br>
                                    Login credentials are as follow:<br><br>
                                    
                                    URL: <a href="{{url('/')}}">{{url('/')}}</a><br>
                                    Email:   <a name="myname" style="color:#666666">{{$user->email}}</a><br>
                                    Password: {{$user->password}}<br>
                                    <br>
                                    Happy selling!<br>
                                    ~ The X-Dates Team<br><br>
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
