@extends('emails.mailtemplate')
@section('title','X-Dates - Most referral notification')
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
                                        <p>
                                            Hello X-Dates, <br>
                                            &nbsp;&nbsp;&nbsp; Most referral of the {{$mailData['type'] or 'Month'}} details are given below.
                                        </p>
                                        <p>
                                            <table border="2">
                                                <tr>
                                                    <th>Sr.no</th>
                                                    <th>User name</th>
                                                    <th>Email </th>
                                                    <th>Number of user refer </th>
                                                    <th>Owner name</th>
                                                    <th>Owner email </th>
                                                    <th>From date</th>
                                                    <th>To date </th>
                                                </tr>
                                                <?php  $i = 1 ;?>
                                                @foreach($userData['details'] as $user)
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>{{$user['user']['name']}}</td>
                                                    <td>{{$user['user']['email']}}</td>
                                                    <td>{{$userData['count'][$user['from_user_id']]}}</td>
                                                    <td>{{$user['owner_name'] or 'self'}}</td>
                                                    <td>{{$user['owner_email'] or 'self'}}</td>
                                                    <td>{{date('m/d/Y',strtotime($mailData['from_date']))}}</td>
                                                    <td>{{date('m/d/Y',strtotime($mailData['to_date']))}}</td>
                                                </tr>
                                                <?php $i++;?>
                                                @endforeach
                                            </table>
                                            
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
