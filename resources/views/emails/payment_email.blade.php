@extends('emails.mailtemplate')
@section('title','X-Dates - Payment notification')
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
                                    @if($is_success)
                                        <p>
                                            Hello {{$user->first_name}}, <br>
                                            &nbsp;&nbsp;&nbsp; Your plan {{$planData->name}} payment have been success.see your plan details below. 
                                        </p>
                                        <p>
                                            <table border="2">
                                                <tr>
                                                    <th>Sr.no</th>
                                                    <th>Plan Name</th>
                                                    <th>Plan Amount </th>
                                                    <th>Pay Amount </th>
                                                    <th>Amount debited from card </th>
                                                    <th>Plan Start Date </th>
                                                    <th>Plan End Date </th>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td>{{$planData->plan_name}}</td>
                                                    <td>${{$planData->plan_amount or 0}}</td>
                                                    <td>${{$pay_amount or 0}}</td>
                                                    <td>${{$card_charge or 0}}</td>
                                                    <td>{{date('M d,Y',strtotime($planData->plan_start_date))}}</td>
                                                    <td>{{date('M d,Y',strtotime($planData->plan_end_date))}}</td>
                                                </tr>
                                            </table>
                                            <br><br>
                                                Happy selling!<br>
                                                ~ The X-Dates Team
                                            </p>
                                        @else
                                        <p>
                                            Hello {{$user->first_name}}, <br>
                                            &nbsp;&nbsp;&nbsp; Your plan {{$planData->name}} is expired and we are unable to charge your credit card. Please update your card details else your account will be deactiveted after 15 days. 
                                        </p>
                                        @endif
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
