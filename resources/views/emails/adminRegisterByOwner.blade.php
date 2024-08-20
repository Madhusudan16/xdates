@extends('emails.mailtemplate')
@section('title','X-Dates: welcome| login credentials')
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
                                    <td align="left" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding"> Hello, {{$user->first_name}}!<br><br>
						        	You have been added to {{env('APP_NAME')}}. Login credentials are as follows:<br><br>
						        							        	
						        	URL: <a href="{{url('/admin/')}}">{{url('/admin/')}}</a><br>
						        	Email:  <a name="myname" style="color:#666666">{{$user->email}}</a> <br>
						        	Password: {{$user->decrypt_pass}}<br><br>
						        	
                                   <!--  <p>Also you can change your password using following link : <a href="{{url('/admin/confirm/'.$user->token)}}">{{url('/admin/confirm/'.$user->token)}}</a></p> -->
                                    
                                    Welcome to the team!
                                    <br>
                                    <span style="color:#666666">~ X-Dates</span>
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
