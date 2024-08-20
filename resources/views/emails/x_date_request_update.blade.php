@extends('emails.mailtemplate')
@section('title','X-Dates: record update request')
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
                                    <p> <h2> X-Dates: Update Request </h2></p>
                                    <p> <strong>{{$data['user_name']}}</strong> has requested that you provide an updated note on <strong>{{trim($data['x_name'])}}</strong>. <a href="{{url('/login')}}">Click here</a> to login and do so.</p>
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
