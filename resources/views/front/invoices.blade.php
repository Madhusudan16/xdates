@extends('front')
@section('main')
<meta name="_token" content="{!! csrf_token() !!}"/>
    <div class="row">
        @include('front.partials.setting-menu')
        <div class="col-lg-9 col-md-9 col-sx-9 account-right">
            <div class="right-profile">
                <div class="row"> 
                    <div class="col-md-8 padding-rigth-none">
                        <div class="invoice_left_sec">
                            <h3 class="heding">Invoices</h3> 
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>invoices no.</th> 
                                            <th>date</th>
                                            <th>amount</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    	@if($invoiceData->count() != 0 ) 
                                    		@foreach($invoiceData as $invoice)
		                                        <tr>
		                                            <td>{{$invoice->id}}</td>
		                                            <td>{{date('M d,Y',strtotime($invoice->bill_date))}}</td>
		                                            <td>${{$invoice->amount}}</td>
		                                            <td align="center"><a href="{{url('generate-pdf')}}/{{$invoice->id}}" target="_blank"><img src="{{asset('assets/images/pdf.png')}}"> &nbsp; PDF</a></td>
		                                        </tr>
	                                        @endforeach
                                        @else 
                                            <tr><td colspan="4" align="center"><strong>Record 
                                            not found!</strong></td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                                    
                        </div>
                    </div>
                    
                    <div class="col-md-4 padding-left-none">
                        <div class="plan_detail">
                            <h3 class="heding">{{$plan_details['name'] or 'Trial'}} Plan</h3>
                                <ul class="detail_list">
                                    <li>
                                        <label>Company Name</label>
                                        <span>{{$user->com_name}}</span>
                                    </li>
                                    <li>
                                        <label>Subscription Status</label>
                                        <span>{{$status[$user->status]}}</span>
                                    </li>
                                    <li>
                                        <label>Balanace</label>
                                        <span>${{$userBalance}}</span>
                                    </li>
                                    <li>
                                        <label>Active Users </label>
                                        <span>{{ $total_user }} of {{$plan_details['n_allowed_users']}}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection


@section('footerscripts')  
   {!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
   {!! $validator or '' !!}
@endsection