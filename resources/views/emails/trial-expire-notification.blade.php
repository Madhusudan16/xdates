@extends('emails.mailtemplate')
@section('title','trial expire notification')
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
                        <td align="left" style="font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding">
                        @if($is_expire == 1 &&  $userData['days'] == 1)
                           X-Dates: Your Free Trial EXPIRED
                        @elseif($is_expire == 1 ) 
                           X-Dates: Account Expiration Follow-up
                        @else
                            X-Dates: FREE Trial Expiration Notification 
                        @endif
                        </td>
                     </tr>
                    
                     <tr>
                        <td align="left" style="padding: 20px 0 0 0; font-size: 15px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding"> 
                              
                              @if($is_expire == 0 && isset($userData['bill_days']) ) 
                                 @if($userData['days'] == 10 )
                                 <p>Hello, {{$userData['first_name']}}! </p>
                                 <p>
                                    We hope you are making use of your FREE trial of X-Dates and experiencing all of the benefits it has to offer. This email serves as a reminder that @if($userData['days'] > 1) you only have {{$userData['days']}} days left in your trial. We'll send a few more reminders, but welcome you to <a href='{{url("/planbill/change-plan")}}'>click here</a> to sign-up. @else today is the final day in your trial. This is your final reminder. If you don't <a href='{{url("/planbill/change-plan")}}'>click here</a> to sign-up, you will not be able to access your account tomorrow.@endif
                                 </p>  
                                 @elseif($userData['days'] == 5) 
                                    <p>{{$userData['first_name']}}, it's us again!</p>
                                    <p>This is another friendly reminder that your FREE trial of X-Dates is drawing to a close. You only have {{$userData['days']}} days left before the trial ends. We'll send a few more emails, but you might want to <a href='{{url("/planbill/change-plan")}}'>click here</a> to sign-up.</p>
                                 @elseif($userData['days'] == 1) 
                                    <p>{{$userData['first_name']}},</p><p>PANIC! The world is coming to an end! Ok...the world isn't ending, but your FREE trial of X-Dates is. Kinda the same thing, right?</p>
                                    <p>This is your final reminder. If you don't sign-up, you won't be able to access your account tomorrow. <a href='{{url("/planbill/change-plan")}}'>Click here</a> to ensure your account isn't suspended.</p>
                                    <p>
                                     If there is a reason you have not signed-up, we'd love to hear from you. You can contact us, via email, at <a href="mailto:support@xdates.net" >support@xdates.net</a> or call us at +1 423-414-2500.
                                    </p>
                                 @endif
                                <!--  <p> 
                                 Do note that signing-up now WILL NOT cancel your free trial. We'll bill your credit card, but the next billing won't happen for {{$userData['bill_days']}} days (and then we'll resume the normal 30 day billing cycle). 
                                 </p> -->
                                 
                              @elseif($is_expire == 1 &&  $userData['days'] == 1) 
                              <p>
                                 Your free trial of X-Dates has EXPIRED. None of your users are currently able to access their accounts! If you're like most insurance producers, following-ups on x-dates is a BIG deal. <a href='{{url("/planbill/change-plan")}}'>Click here</a> to sign-up for our service and reactivate your account.
                              </p>
                              <p>
                                 If there is a reason you haven't signed-up, we'd love to hear from you. Our software was developed by an insurance producer who  wanted a better way to manage his x-dates. When he couldn't find a service to subscribe to, he got to work on his own solution. We want to make our software the best it can be. If we've overlooked something, please bring it to our attention.
                              </p>
                               @elseif($is_expire == 1 ) 
                                 <p>
                                       {{$userData['first_name']}}, what happened to us?! Everyone thought we were such a perfect fit. Break-ups are never easy, but we didn't see this coming...
                                 </p>
                                 <p>
                                    On a serious note, we're sorry you didn't find value in a subscription to X-Dates. It's our aim to be the "must-have" tool for insurance producers. If you have a few minutes, we'd love to know what we could have done to earn your business. Who knows...maybe it's something other producers would also like.
                                 </p>
                              @endif   
                              @if($userData['days'] != 1 || $is_expire == 1 ) 
                              <p>
                                 To contact us, email <a href="mailto:support@xdates.net" >support@xdates.net</a> or call us at +1 423-414-2500. 
                              </p>
                              @endif
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