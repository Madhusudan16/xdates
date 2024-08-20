  
@extends('back')

@section('main') 
<div class="row">
	@include('admin.partials.setting-menu')
	<div class="col-md-9 account-right global-variables">
		@if($curModAccess['view'])
		    <div class="right-profile"> 
	        <div class="row">
	            <div class="col-md-12 col-sm-12"> 
                   		<h3 class="right-heading">Configure Global Variables</h3>
                        <form class="form-horizontal" id="settingForm" role="form" method="POST" action="{{ url('/admin/manage-variables') }}"> 
                        {!! csrf_field() !!}

                            	<div class="form-group{{ $errors->has('admin_email') ? ' has-error' : '' }}">
                            		<label for="inputEmail" class="col-sm-4 control-label">App Admin Email</label>
	                                <div class="col-sm-6">
	                                    <span class="border-span"><input type="text" class="form-control" name="setting[admin_email]" id="inputEmail" value={{ $setting['admin_email'] or  ''}}></span>

	                                    @if ($errors->has('[admin_email]'))
	                                        <span class="help-block">
	                                            <strong>{{ $errors->first('admin_email') }}</strong>
	                                        </span>
	                                    @endif
	                                </div>
                            	</div>
                            	
                                <div class="form-group{{ $errors->has('trial_duration') ? ' has-error' : '' }}">
	                                	<label for="inputName" class="col-sm-4 control-label">Default Trial Duration(in days)</label>
		                                 <div class="col-sm-6">
		                                    <span class="border-span"><input type="text" class="form-control" name="setting[trial_duration]" id="inputName" value={{ $setting['trial_duration'] or ''}}></span>
		                                     @if ($errors->has('[trial_duration]'))
		                                        <span class="help-block">
		                                            <strong>{{ $errors->first('trial_duration') }}</strong>
		                                        </span>
	                                   		 @endif
		                                </div>
                         	    </div>

                         	    <div class="form-group{{ $errors->has('referral_trial_days') ? ' has-error' : '' }}">
	                                	<label for="inputReferralDuration" class="col-sm-4 control-label">Referral Trial Duration(in days)</label>
		                                 <div class="col-sm-6">
		                                    <span class="border-span"><input type="text" class="form-control" name="setting[referral_trial_days]" id="inputReferralDuration" value={{ $setting['referral_trial_days'] or ''}}></span>
		                                     @if ($errors->has('[referral_trial_days]'))
		                                        <span class="help-block">
		                                            <strong>{{ $errors->first('referral_trial_days') }}</strong>
		                                        </span>
	                                   		 @endif
		                                </div>
                         	    </div>

                    			<div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
	                                	<label for="address" class="col-sm-4 control-label">Address</label>
		                                 <div class="col-sm-6">
		                                    <span class="border-span"><textarea cols="4" class="form-control setting-address" name="setting[address]" id="address">{{ $setting['address'] or ''}}</textarea></span>
		                                     @if ($errors->has('[address]'))
		                                        <span class="help-block">
		                                            <strong>{{ $errors->first('address') }}</strong>
		                                        </span>
	                                   		 @endif
		                                </div>
                         	    </div>

                         	    <div class="form-group{{ $errors->has('customer_support_mail') ? ' has-error' : '' }}">
	                                	<label for="customer_support_mail" class="col-sm-4 control-label">Customer support mail address</label>
		                                 <div class="col-sm-6">
		                                     <span class="border-span"><input type="email" class="form-control" name="setting[customer_support_mail]" id="customer_support_mail" value={{ $setting['customer_support_mail'] or ''}}></span>
		                                     @if ($errors->has('[customer_support_mail]'))
		                                        <span class="help-block">
		                                            <strong>{{ $errors->first('customer_support_mail') }}</strong>
		                                        </span>
	                                   		 @endif
		                                </div>
                         	    </div>

                         	    <div class="form-group{{ $errors->has('cancel_account_restore') ? ' has-error' : '' }}">
	                                	<label for="cancel_account_restore" class="col-sm-4 control-label">Cancel account restore(in days)</label>
		                                 <div class="col-sm-6">
		                                    <span class="border-span"><input type="text" class="form-control" name="setting[cancel_account_restore]" id="cancel_account_restore" value={{ $setting['cancel_account_restore'] or ''}}></span>
		                                     @if ($errors->has('[cancel_account_restore]'))
		                                        <span class="help-block">
		                                            <strong>{{ $errors->first('cancel_account_restore') }}</strong>
		                                        </span>
	                                   		 @endif
		                                </div>
                         	    </div>

                         	    <div class="form-group{{ $errors->has('monthly_yearly_notification_on') ? ' has-error' : '' }}">
                            		<label for="monthly_yearly_notification_on" class="col-sm-4 control-label">Monthly yearly notification on </label>
	                                <div class="col-sm-6">
	                                    <span class="border-span"><input type="text" class="form-control" name="setting[monthly_yearly_notification_on]" id="monthly_yearly_notification_on" value={{ $setting['monthly_yearly_notification_on'] or  ''}}></span>

	                                    @if ($errors->has('[monthly_yearly_notification_on]'))
	                                        <span class="help-block">
	                                            <strong>{{ $errors->first('monthly_yearly_notification_on') }}</strong>
	                                        </span>
	                                    @endif
	                                </div>
                            	</div>

                            	<div class="form-group{{ $errors->has('feedback_noti_email') ? ' has-error' : '' }}">
                            		<label for="feedback_noti_email" class="col-sm-4 control-label">Feedback mail receive on</label>
	                                <div class="col-sm-6">
	                                    <span class="border-span"><input type="text" class="form-control" name="setting[feedback_noti_email]" id="feedback_noti_email" value={{ $setting['feedback_noti_email'] or  ''}}></span>

	                                    @if ($errors->has('[feedback_noti_email]'))
	                                        <span class="help-block">
	                                            <strong>{{ $errors->first('feedback_noti_email') }}</strong>
	                                        </span>
	                                    @endif
	                                </div>
                            	</div>

			                    <div class="form-group">
			                                <div class="col-sm-offset-4 col-sm-3 no-pedding-mobile">
			                                    <button type="submit" class="btn btn-success">Save All</button>
			                                </div>
			                    </div>
			                     
                        </form> 
                </div>
              </div>
           </div>
          @endif
 </div>
</div>

@endsection
@section('footerscripts')  
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}

 {!! $validator !!}
@endsection


