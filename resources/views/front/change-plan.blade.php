@extends('front')
@section('main')
<meta name="_token" content="{!! csrf_token() !!}"/>
<div class="row">
   @include('front.partials.setting-menu') 
   <div class="col-lg-9 col-md-9 col-sx-9 account-right">
      <div class="right-profile">
         <div class="row">
            <div class="col-md-8 padding-rigth-none">
               <div class="plan_left_sec">
                  <div class="plan_sec clearfix" id="refresh">
                     <h3 class="heding">Change your plan</h3>
                     @foreach ($plan_list as $item)
                     @if(! empty($user_plan) && $user_plan->plan_id == $item->id )
                     <div class="plan_box text-center current_plan">
                        <h3>{{$item->name}}</h3>
                        <span class="user">Up to {{$item->n_allowed_users or 0}} user</span>
                        <span class="price">${{$item->cost}} /month</span>
                        <button type="button" class="btn btn-success find_pay_amount" plan_amount="{{$item->cost}}" data-toggle="modal" id="{{$item->id}}">Current Plan</button>
                     </div>
                     @elseif(!empty($n_allowed_users) && $n_allowed_users > $item->n_allowed_users && isset($user_plan))
                     <div class="plan_box text-center">
                        <h3>{{$item->name}}</h3>
                        <span class="user">Up to {{$item->n_allowed_users or 0}} user</span>
                        <span class="price">${{$item->cost}} /month</span>
                        @if($coupon_used == 1) 
                           <button type="button" class="btn btn-danger coupon_used find_pay_amount" plan_amount="{{$item->cost}}" myclass="downgrade" data-toggle="modal" id="{{$item->id}}">Downgrade</button>
                        @else
                           <button type="button" class="btn btn-danger downgrade find_pay_amount" plan_amount="{{$item->cost}}" data-toggle="modal" id="{{$item->id}}" >Downgrade</button>
                        @endif
                     </div>
                     @elseif(!empty($n_allowed_users) && $n_allowed_users < $item->n_allowed_users && isset($user_plan))
                     <div class="plan_box text-center">
                        <h3>{{$item->name}}</h3>
                        <span class="user">Up to {{$item->n_allowed_users or 0}} user</span>
                        <span class="price">${{$item->cost}} /month</span>
                        @if($coupon_used == 1) 
                           <button type="button" class="btn btn-success coupon_used find_pay_amount" plan_amount="{{$item->cost}}" myclass="upgrade" data-toggle="modal" id="{{$item->id}}">Upgrade</button>
                        @else
                           <button type="button" class="btn btn-success upgrade find_pay_amount" plan_amount="{{$item->cost}}" data-toggle="modal" id="{{$item->id}}">Upgrade</button>
                        @endif
                     </div>
                     @else 
                     <div class="plan_box text-center">
                        <h3>{{$item->name}}</h3>
                        <span class="user">Up to {{$item->n_allowed_users or 0}} user</span>
                        <span class="price">${{$item->cost}} /month</span>
                        <button type="button" class="btn btn-success select-plan find_pay_amount" plan_amount="{{$item->cost}}" data-toggle="modal" id="{{$item->id}}">Select Plan</button>
                     </div>
                     @endif
                     @endforeach
                  </div>
                  <div class="plan_details clearfix">
                     <h4>How upgrading and downgrading works</h4>
                     <ul class="plan_info">
                        <li>Plan changes incur pro-rated changes and/or</li>
                        <li>Credit balances factor into potential charges</li>
                        <li>Upgrading is as simple as choosing your plan and authorizing us to bill the new amount, which, depending upon your billing cycle,may be pro-rated</li>
                        <li>An account cannot be upgraded if there is an unpaid balance on the account</li>
                     </ul>
                     <ul class="plan_info">
                        <li>Downgrading an account requires a reconciliation of the number of users on your account and the maximum number of users the desired account affords.This may require deactivating and/or deleting users.</li>
                        <li>
                           If downgrading an account results in a credit balance, that credit will be applied to future billing cycles until resolved
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
            <div class="col-md-4 padding-left-none">
               <div class="plan_detail">
                  <h3 class="heding" >{{$user_plan->plan_name or 'Trial'}} PLAN</h3>
                  <ul class="detail_list">
                     <li>
                        <label>Company Name</label>
                        <span>{{$user->com_name}}</span>
                     </li>
                     <li>
                        <label>Subscription Status</label>
                        @if($user->status==1)
                        <span>Active</span>
                        @endif
                        @if($user->status==0)
                        <span>Inactive</span>
                        @endif
                     </li>
                     <li>
                        <label>Balanace</label>
                        <span>${{$userBalance}}</span>
                     </li>
                     <li>
                        <label>Active Users </label>
                        <span id="refer"> {{ $total_user }} of {{$n_allowed_users}}</span>
                     </li>
                  </ul>
               </div>
               <div class="offer_sec">
                  <h4>Promotion code</h4>
                  <p>If you have a promotion code, enter it below and click the Upgrade button of the account you wish to upgrade to.</p>
                  <form role="form" id="couponForm">
                     <div class="row">
                        <div class="col-md-12">
                           <div class="clearfix"></div>
                           <div class="form-group">
                              <span class="border-span"><input type="text" class="form-control" id="coupon" placeholder="Enter your promotion code here" name="coupon"></span>
                               <input type="button" value="Apply" class="btn btn-info coupon_apply">
                              <span class="coupon_error"> </span>
                              <span class="coupon_success"> </span>
                               
                           </div>
                        </div>
                        <div class="clearfix"></div>
<!--
                        <div class="col-md-5 pull-right">
                           <input type="button" value="Apply" class="btn btn-info coupon_apply">
                        </div>
-->
                     </div>
                     
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="loader">
      <img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading..">
   </div>
</div>
</div>
</section>
<div class="modal fade" id="upgrad_plan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog account-model" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Upgrade Plan</h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <p class="info">Confirming your account upgrade will authorize changing the credit card on your account in the amount of <span class="plan_amount"></span> from the next billing period forward.</p>
                  <p>The pro-rated amount from now through the end of the current billing period will be <span class="pay_amount"></span>, changed immediately to the credit card on file.</p>
                  <div class="text-right">
                     <button type="button" class="btn btn-success upgrade-confirm" id=""  data-toggle="modal">Confirm</button>
                     <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="balance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog account-model" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Card Declined</h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <p class="info">We were not able to charge the pro-rated amount of <span class="plan_amount"></span> to the card on file.</p>
                  <p>Please update your credit card information in order to proceed.</p>
                  <div class="text-right">
                     <button type="button" class="btn btn-success balance-update" data-toggle="modal" >Update your credit card</button>
                     <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="downgrade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog account-model" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Downgrade Plan</h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <p class="info">Confirming your account downgrade will lessen your monthly changes to <span class="plan_amount"></span> from the next billing period forward.</p>
                  <p>The pro-rated credit of <span class="pay_amount"></span> will be subtracted from the next billing cycle(s) in a manner that you are changed the full monthly amount until your credit has been fully used.</p>
                  <div class="text-right">
                     <button type="button" id="" onclick="upAndDownPlan(this.id);" class="btn btn-danger downgrade-confirm" data-toggle="modal" > Confirm</button>
                     <button type="button" class="btn btn-success" data-dismiss="modal">cancel</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="many_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog account-model" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Too Many Users</h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  @if($user->current_plan != 0) 
                        <p class="info">There are more users on your account than the plan you have chosen. You will need to deactivate and/or delete users to downgrade. Click the "Confirm" button, below to resolve this matter or the "Cancel" button to revert this change.</p>
                  @else 
                        <p class="info">There are more "Active Users" on your account than the plan you have chosen. Click the "Confirm" button, below to deactivate and/or delete users or the "Cancel" button to choose a larger plan.</p>
                  @endif
                  <div class="text-right">
                     <button type="button" class="btn btn-success" data-dismiss="modal">cancel</button>
                     <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#upgrade_user" onclick="passRefer();">Confirm</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="otherError" tabindex="-1" role="dialog" aria-labelledby="otherErrorModalLabel">
   <div class="modal-dialog account-model" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="otherErrorModalLabel">Error Occured</h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <p class="info" id="otherErrorDesc"></p>
                  <div class="text-right">
                     <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="coupon_terminate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog account-model" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Confirmation </h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <p class="info">Your previous coupon will be expire when you change plan.</p>
                  <p>
                        Do you want to change plan?
                  </p>
                  <div class="text-right">
                     <button type="button" class="btn btn-success coupon_exist" id=""  data-toggle="modal" data-target="" data-dismiss="modal">Confirm</button>
                     <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   var coupon_used = {{$coupon_used}};
   var remining_days = {{$remaining_days or 30}};
</script>
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
{{ HTML::script('assets/js/planChange.js') }}

@endsection