@extends('back')

@section('main') 
{!! HTML::style(asset('assets/css/user_details.css')) !!}
<meta name="_token" content="{!! csrf_token() !!}"/>
<section class="main-content">
<!--   <div class="container">-->
      <h1 class="boxedtitle"> {{$client->com_name}} <!-- <span class = "pull-right "> <a href="javascript:void(0)"  class="btn btn-success btn-xs btn-flat login_as_customer" id="{{$client->id}}"><span class="glyphicon glyphicon-log-in"></span> &nbsp; Login as Customer</a></span> --></h1>

      <div class="red-tabs company-tabs">
         <!-- Nav tabs -->
         <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
               <a href="#details" aria-controls="details" role="tab" data-toggle="tab">Details</a>
            </li>
            <li role="presentation">
               <a href="#billing" aria-controls="billing" role="tab" data-toggle="tab">Billing</a>
            </li>
            <li role="presentation">
               <a href="#users" aria-controls="users" role="tab" data-toggle="tab">Users</a>
            </li>
            <li role="presentation">
               <a href="#notes" aria-controls="notes" role="tab" data-toggle="tab">Notes</a>
            </li>
            <li role="presentation">
               <a href="#logs" aria-controls="logs" role="tab" data-toggle="tab">Logs</a>
            </li>
            <li role="presentation">
               <a href="#declined_trial" aria-controls="declined_trial" role="tab" data-toggle="tab">Trial Logs</a>
            </li>
         </ul>
         <!-- Tab panes -->
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane active table-responsive" id="details">
               <table class="table table-bordered">
                  <colgroup>
                     <col width="1">
                     </col>
                     <col>
                     </col>
                  </colgroup>
                  <tbody>
                     <tr>
                        <th>
                           Status
                        </th>
                        <td>
                           @if($client->is_expired == 1) 
                              {{$status[0]}} 
                           @else 
                              {{$status[$client->status]}}
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <th>
                           Signup
                        </th>
                        <td>
                           {{date("m-d-Y",strtotime($client->created_at))}}
                        </td>
                     </tr>
                     <tr>
                        <th>
                           Trial Exp.
                        </th>
                        @if(isset($planData) && !empty($planData)) 
                        <td>
                           N/A
                        </td>
                        @else 
                        <td>
                           @if(!empty($trialExtendedData))
                           @foreach($trialExtendedData as $value)
                              @if(isset($value->trial_end_date)) 
                                 {{date("m-d-Y",strtotime($value->trial_end_date))}}(Expired),
                              @endif
                           @endforeach
                           @endif
                           {{date("m-d-Y",strtotime($client->trial_end_date))}} @if($client->trial_end_date < date('Y-m-d')) (Expired) @endif 
                           <a href="#" class="btn btn-primary text-uppercase btn-extend extend" data-toggle="modal" data-target=".extendtrialsm" user-id = "{{$client->id}}">extend 30 days</a>
                        </td>
                        @endif

                     </tr>
                     <tr>
                        <th>
                           Account Exp.
                        </th>
                        <td>
                           @if($client->status == 0 )
                           {{date('m-d-Y',strtotime($client->trial_end_date))}}
                           @else 
                           N/A
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <th>
                           Period of Activity
                        </th>
                        <td>
                           {{$countActiveDays or 'N/A'}}
                        </td>
                     </tr>
                     <tr>
                        <th>
                           Current Number of Users
                        </th>
                        <td>
                           {{$number_of_user}}
                        </td>
                     </tr>
                     <tr>
                        <th>
                           Max. Number of Users
                        </th>
                        <td>
                        @if(isset($planData) && !empty($planData)) 
                        
                           {{$planData['allowed_users']}}
                        @else 
                           N/A
                        @endif
                          </td>
                     </tr>
                     @if(!empty($cancel_account_token))
                     <tr>
                        <th>
                           Restore Cancel Account
                        </th>
                        <td>
                           <a href="javascript:void(0)"  data-toggle="modal" data-target=".restore_cancel_account"  class="btn btn-sm btn-flat btn-primary ">restore account</a>
                        </td>
                     </tr>
                     @endif
                  </tbody>
               </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="billing">
               <div class="row">
                  <div class="col-md-4 pad-r-0 billing-left">
                     <div class="billing-left-inner">
                        <div class="billing-block block">
                           <h5 class="block-title">Billing Address</h5>
                           <p>
                            
                                @if(!empty($card_details->address_line_1)) 
                                    {{$card_details->address_line_1}}<br> {{$card_details->get_state->state_name}} {{$card_details->get_country->code}}, {{$card_details->zip_code}}
                                @else 
                                    N/A
                                @endif
                           </p>
                        </div>
                        <div class="credit-block block">
                           <h5 class="block-title">Credit Card</h5>
                           <p>
                                @if(!empty($card_details->card_no)) 
                                    XXXX-XXXX-{{$card_details->card_no}} ({{date('m/y',strtotime($card_details->expiry_date))}})
                                @else 
                                    N/A
                                @endif
                           </p>
                        </div>
                        <div class="plan-block block">
                           <h5 class="block-title">Plan</h5>
                           <!-- Nav tabs -->
                           <ul class="nav nav-tabs" role="tablist">
                                @if(!empty($allPlan))
                                    <li role="presentation" class ="@if(0 == $client->current_plan) active @endif display_plan not_clickable"><a href="#trial_plan"  aria-controls="trial_plan" role="tab" class="myplans" data-toggle="tab">FREE TRIAL (Unlimited Users)
                                        
                                     </a></li>
                                    @foreach($allPlan as $plan)
                                        <li role="presentation" class ="@if($plan->id == $client->current_plan) active @endif display_plan not_clickable"><a href="#{{$plan->name}}"  aria-controls="{{$plan->name}}" role="tab" class="myplans" data-toggle="tab">${{$plan->cost}} {{$plan->name}} 
                                        @if($plan->n_allowed_users == 1 ) 
                                            ({{$plan->n_allowed_users}} User)
                                        @else
                                         (Up to {{$plan->n_allowed_users}} Users) 
                                         @endif
                                     </a></li>
                                    @endforeach
                                @endif
                             
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-8 pad-l-0 billing-right">
                     <div class="user-status">
                        <ul class="nav nav-tabs" role="tablist">
                           <li role="presentation" class="active plan_sub_tab invoices"><a href="" aria-controls="" role="tab" data-toggle="tab">Invoices</a></li>
                           <li role="presentation" class="plan_sub_tab payments"><a href="" aria-controls="" role="tab" data-toggle="tab">Declined Charges</a></li>
                        </ul>
                     </div>
                     <!-- Tab panes -->
                     <div class="tab-content">
                              <div role="tabpanel" class="tab-pane table-responsivetrial-extend scrollbox billing-scrollbox allPlans active invoices_content" >
                                 <table class="table table-bordered changeOrder" >
                                    <thead>
                                       <tr>
                                          <th>
                                             Invoice no.
                                          </th>
                                          <th>
                                             Date
                                          </th>
                                          <th>
                                             Plan
                                          </th>
                                          <th>
                                             Amount
                                          </th>
                                          <th class="no-sort">
                                             &nbsp;
                                          </th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       @if(!empty($invoices))
                                          @foreach($invoices as $invoice)
                                             <tr>
                                                <td>
                                                   {{$invoice['id']}}
                                                </td>
                                                <td>
                                                   {{date('m/d/Y',strtotime($invoice['bill_date']))}}
                                                </td>
                                                <td>
                                                   {{$plans[$invoice['plan_id']]}}
                                                </td>
                                                <td>
                                                  ${{$invoice['amount']}}
                                                </td>
                                                <td>
                                                   <a href="{{url('admin/generate-pdf')}}/{{$invoice['id']}}" target="_blank">
                                                   <img src="{{url('assets/images/pdf.png')}}"> &nbsp; PDF </a>
                                                </td>
                                             </tr>
                                          @endforeach
                                       @else 
                                          <tr>
                                             <td colspan="5" align="center"><strong>No record found!</strong></td>
                                          </tr>
                                       @endif  
                                    </tbody>
                                 </table>
                              </div>
                        

                        
                              <div role="tabpanel" class="tab-pane table-responsivetrial-extend scrollbox billing-scrollbox allPlans payment_content">
                                 <table class="table table-bordered changeOrder" >
                                    <thead>
                                       <tr>
                                          <th>
                                             Date
                                          </th>
                                          <th>
                                             Amount
                                          </th>
                                         <th>Plan name</th>
                                         <th>Credit card number</th>
                                         <th>Credit card EXP.</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       @if(!empty($declined_payments))
                                          @foreach($declined_payments as $payments)
                                             <tr>
                                                <td>
                                                   {{date('m/d/Y',strtotime($payments['created_at']))}}
                                                </td>
                                                <td>
                                                   ${{$payments['amount'] or 0}}
                                                </td>
                                                <td>
                                                   {{$payments['plan_name']}}
                                                </td>
                                                <td>
                                                   {{$payments['card_no'] or ''}}
                                                </td>
                                                <td>
                                                   {{$payments['expired_date'] or  ''}}
                                                </td>
                                             </tr>
                                          @endforeach
                                       @else 
                                          <tr>
                                             <td colspan="5" align="center"><strong>No record found!</strong></td>
                                          </tr>
                                       @endif  
                                    </tbody>
                                 </table>
                              </div>
                       
                     </div>
                  </div>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane table-responsive scrollbox users-scrollbox" id="users">
               <table class="table table-bordered changeOrder" >
                  <thead>
                     <tr>
                        <th>
                           Sr
                        </th>
                        <th>
                           Name
                        </th>
                        <th>
                           Email
                        </th>
                        <th>
                           Last Login
                        </th>
                        <th>
                           Role
                        </th>
                        <th class="no-sort">
                           Reset Password
                        </th>
                        <th class="no-sort">
                           &nbsp;&nbsp;
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $i = 0; ?>
                     @if(!empty($subClients) && count($subClients) != 0)  
                     @foreach($subClients as $clients)
                     <tr>
                        <td>
                           {{++$i}}
                        </td>
                        <td>
                           {{$clients->name }}
                        </td>
                        <td>
                           {{$clients->email}}
                        </td>
                        <td>

                           {{isset($clients->last_login)? date('m/d/y',strtotime($clients->last_login)) : ''}}
                        </td>
                        <td>
                           {{$userType[$clients->user_type] or 'Unknown'}}
                        </td>
                        <td class="resendpass">
                           <a href="javascript:void(0)" data-email= "{{$clients->email}}"  class="reset">Email</a>
                        </td>
                        <td>
                           <a href="javascript:void(0)"  class="btn btn-success btn-sm btn-flat login_as_customer" id="{{$clients->id}}"><span class="glyphicon glyphicon-log-in"></span> &nbsp; Login as User</a>
                        </td>
                     </tr>
                     @endforeach
                     @else
                     <tr>
                        <td colspan="6" align="center">
                           <h4>No Record Found</h4>
                        </td>
                     </tr>
                     @endif
                  </tbody>
               </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="notes">
               <div class="add_note clearfix note-full">
                  <button type="button" class="btn btn-success add-note-btn">ADD NOTE</button>
                  <form class="" id="noteForm"> 
                     <div class="form-group">
                        <textarea class="form-control my-comment" rows="1" id="comment"></textarea>
                     </div>
                  </form>
                  <button type="button" class="btn btn-default comment-btn" value="SAVE" style="display:none">SAVE</button>
               </div>
               <div class="overflow table-responsive scrollbox tbl-full notes-scrollbox note-section-tbl">
                  <table class="table table-bordered  table-responsive note-table changeOrder">
                     <thead>
                        <tr>
                           <th align="center">
                              User
                           </th>
                           <th width="125px" align="center" >
                              Date
                           </th>
                           <th width="135px" align="center" class="no-sort">
                              time
                           </th>
                           <th class="no-sort">
                              details
                           </th>
                        </tr>
                     </thead>
                     <tbody class="note-list">

                        @if(!empty($notes) && $notes->count() != 0)
                        @foreach($notes as $note)
                        <?php 
                           if($note->note_type == 2) {
                              $image  =  config('constants.XDATES_PROFILE');
                              $name = "";
                           } else {
                              $image = isset($note['get_actioner_user']->profile_image) ? $note['get_actioner_user']->profile_image : $note['get_user']->profile_image;
                              $name = isset($note['get_actioner_user']->name) ? $note['get_actioner_user']->name : $note['get_user']->name;
                           }
                        ?>
                        <tr>
                           <td >
                              @if(!empty($image))
                              <img src={{url(config('constants.FILEUPLOAD').$image)}}  width="42" height="42" alt="" class="profile-image"/>
                              @else 
                              <img  src={{url(config('constants.FILEUPLOAD').''.config('constants.DEFAULT_PROFILE'))}} width="42" height="42" alt="" class="profile-image"/>
                              @endif
                              <br> <span class="name" align="center">{{$name}}</span>
                           </td>
                           <td align="center">
                              {{date('m/d/y',strtotime($note->created_at))}}
                           </td>
                           <td align="center">
                              {{date('H:ia',strtotime($note->created_at))}}
                           </td>
                           <td class="note-detail">
                             {!! get_note_text($note) !!}
                           </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                           <td colspan="4" align="center" class="not-found-note">
                              <h4>No Record Found</h4>
                           </td>
                        </tr>
                        @endif
                     </tbody>
                  </table>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="logs">
               <div class="overflow table-responsive scrollbox tbl-full logs-scrollbox log-section-tbl">
                  <table class="table changeOrder table-bordered  table-responsive logs-table">
                     <thead>
                        <tr>
                           <th align="center">
                              User
                           </th>
                           <th width="125px" align="center" >
                              Date
                           </th>
                           <th width="135px" align="center" class="no-sort">
                              time
                           </th>
                           <th class="no-sort">
                              details
                           </th>
                        </tr>
                     </thead>
                     <tbody class="log-list">
                        @if(!empty($logs) && $logs->count() != 0)
                        @foreach($logs as $log)
                        <tr>
                           <td >
                              @if(!empty($log['get_user']->profile_image))
                              <img src={{url(config('constants.FILEUPLOAD').$log['get_user']->profile_image)}}  width="42" height="42" alt="" class="profile-image"/>
                              @else 
                              <img  src={{url(config('constants.FILEUPLOAD').''.config('constants.DEFAULT_PROFILE'))}} width="42" height="42" alt="" class="profile-image"/>
                              @endif
                              <br> <span class="name" align="center">{{$log['get_user']->name}}</span>
                           </td>
                           <td align="center">
                              {{date('m/d/y',strtotime($log->created_at))}}
                           </td>
                           <td align="center">
                              {{date('H:ia',strtotime($log->created_at))}}
                           </td>
                           <td>
                              @if($log->log_type == 1 )
                              <?php 
                                 $logsData = json_decode($log->notes,true); 
                                 ?>
                              {{get_log_text($log->log_type,$logsData['name'])}} <a href="javascript:void(0)" class="restoredFieldMail" data-id={{$log->id}}>here.</a>
                              @else 
                              {{get_log_text($log->log_type,$log->notes,$log['get_user']->name)}}
                              @endif
                           </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                           <td colspan="4" align="center">
                              <h4>No Record Found</h4>
                           </td>
                        </tr>
                        @endif
                     </tbody>
                  </table>
               </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="declined_trial">
               <div class="overflow table-responsive scrollbox tbl-full logs-scrollbox log-section-tbl">
                  <table class="table changeOrder table-bordered  table-responsive logs-table">
                     <thead>
                        <tr>
                           <th align="center">
                              User
                           </th>
                           <th width="125px" align="center" >
                              Date
                           </th>
                           <th width="135px" align="center" class="no-sort">
                              time
                           </th>
                           <th class="no-sort">
                              details
                           </th>
                        </tr>
                     </thead>
                     <tbody class="declined-list">
                        @if(!empty($declinedTrialList) && $declinedTrialList->count() != 0)
                        @foreach($declinedTrialList as $list)
                        <tr>
                           <td>
                              @if(!empty($list['get_actioner_user']->profile_image))
                              <img src={{url(config('constants.FILEUPLOAD').$list['get_actioner_user']->profile_image)}}  width="42" height="42" alt="" class="profile-image"/>
                              @else 
                              <img  src={{url(config('constants.FILEUPLOAD').''.config('constants.DEFAULT_PROFILE'))}} width="42" height="42" alt="" class="profile-image"/>
                              @endif
                              <br> <span class="name" align="center">{{$list['get_actioner_user']->name}}</span>
                           </td>
                           <td align="center">
                              {{date('m/d/y',strtotime($list->updated_at))}}
                           </td>
                           <td align="center">
                              {{date('H:ia',strtotime($list->updated_at))}}
                           </td>
                           <td>
                              {!! trial_decline_text($list) !!}
                             
                           </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                           <td colspan="4" align="center">
                              <h4>No Record Found</h4>
                           </td>
                        </tr>
                        @endif
                     </tbody>
                  </table>
               </div>
            </div>

         </div>
      </div>
</section>
@endsection
@section('footerscripts')  
{!! HTML::script(asset('assets/js/bootstrap-datetimepicker.min.js')) !!}
{!! HTML::script(asset('assets/js/enscroll-0.6.1.min.js')) !!}  
<script>
   $(document).ready(function(){
       $.ajaxSetup({
           headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
       }); 
      <?php $time  = convertTimeToUSERzone(date('h:i:s'),$user->choosed_timezone); ?>
       var currentTime = "<?php echo date('h:ia',strtotime($time))?>";
       $(".resendpass").unbind("click");
       $(".changeOrder").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } }); 
   
       $(".reset").click(function(event){
           var email = $(this).attr('data-email');
           var oldText = $(this).html();
           var eventStore = $(this);
           $(this).html('sending...');
           $.post(site_url+'/password-reset-link',{email:email}, function(result){
               eventStore.html(oldText);
               $("#myModalLabel").text(result.message);
               $("#thankyou").modal('show');
               setTimeout(function(){
                   $("#thankyou").modal('hide');
               },5000);
           });
       });
       $(".extend").click(function(){
           var user_id = {{$client->id}}; 
           $("#extendTrialModal").modal('show');
           $(".entend-btn").attr('data-id',user_id);
           
       });
   
       $(".extend-btn").click(function(){
           var user_id = {{$client->id}};
           var note = $("#extendTrialText").val();
           var btnText = $(".extend-btn").text();
           $(".extend-btn").text('Submitting...');
           $.post(site_url+"/extend-trial",{id:user_id,note:note},function(result){
               if(result.msg =='success') {
                   $("#extendTrialModal").modal('hide');
                   $("#extendTrial").modal('show');
               } else {
                   $(".trial-extend-msg").text('');
                   $(".trial-extend-msg").text('opps!!something went wrong');
                   $("#extendTrialModal").modal('hide');
                   $("#extendTrial").modal('show');
               }
               $(".extend-btn").text(btnText);
           });
       });
    
        $("#comment").on('focus',function(){
            $("#noteForm").addClass("note_form");
            $(".comment-btn").show();
        });

       $(".comment-btn").click(function(){
           var btnText = $(".comment-btn").text();
           $(".comment-btn").text('Saving...');
           @if(!empty($user->profile_image))
               var user_image =" {{url(config('constants.FILEUPLOAD').$user->profile_image)}}";
           @else
               var user_image = "{{url(config('constants.FILEUPLOAD').''.config('constants.DEFAULT_PROFILE'))}}";
           @endif
           var user_id = {{$client->id}};
           var note = $("#comment").val();
           $.post(site_url+"/add-note",{id:user_id,note:note},function(result){
               if(result.msg =='success') {
                   var html = "<tr><td><img src="+user_image+" }} width='42' height='42' ><br>{{$user->name}}</td><td align='center'> {{date('d/m/y')}}</td><td align='center'>"+currentTime+"</td><td><b>{{$user->name}} </b>: "+note+"</td></tr>";
                   $(".note-list").prepend(html);
               } else {
                    $(".trial-extend-msg").text('');
                    $(".trial-extend-msg").text('opps!!something went wrong');
                    $("#extendTrial").modal('show');
               }
               $("#comment").val("");
               $(".not-found-note").hide();
               $(".comment-btn").text(btnText);
               $(".comment-btn").hide();
           });
       });
   
       $(".trialExtend").click(function(){
           window.location.reload();
       });
       $(".extend-btn").prop('disabled',true);
       $("#extendTrialText").on('keyup',function(){
           
           if($(this).val().length >10) {
               $(".extend-btn").prop('disabled',false);
               $(".extend-btn").addClass('btn-success');
               $(".extend-btn").removeClass('btn-default');
           } else {
               $(".extend-btn").prop('disabled',true);
               $(".extend-btn").removeClass('btn-success');
               $(".extend-btn").addClass('btn-default');
           }
       });
       $(".comment-btn").prop('disabled',true);
       $("#comment").on('keyup',function(){
          
           if($(this).val().length >10) {
               $(".comment-btn").prop('disabled',false);
               $(".comment-btn").removeClass('btn-default');
               $(".comment-btn").addClass('btn-danger');
           } else {
               $(".comment-btn").prop('disabled',true);
               $(".comment-btn").addClass('btn-default');
               $(".comment-btn").removeClass('btn-danger');
           }
       });
        $(".add-note-btn").click(function(){
           $("#comment").focus();
        });
       $(".note-table").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } });
      
       $(".logs-table").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } });
       //var hScroll = ($(window).width() < 768) ? true : false;
           $('.scrollbox').enscroll({
               horizontalScrolling: true,
               verticalTrackClass: 'track',
               verticalHandleClass: 'handle',
               clickTrackToScroll: true,
               minScrollbarLength: '50',
               scrollUpButtonClass: 'scroll-up',
               scrollDownButtonClass: 'scroll-down',
           });
       $(".restoredFieldMail").click(function(){
           var id = $(this).attr('data-id');
           var oldText = $(this).html();
           var eventStore = $(this);
           $(this).html('sending...');
           $.post(site_url+'/field-restore-mail',{id:id}, function(result){
               eventStore.html(oldText);
               $("#myModalLabel").text(result.message);
               $("#thankyou").modal('show');
               setTimeout(function(){
                   $("#thankyou").modal('hide');
               },5000);
           });
       })

       $(".restore-account").click(function(){
            $token = $(this).attr('token');
            var btnText = $(this).text();
            $(this).text('Requesting...')
            $.get(base_url+'/reactive-account/'+$token, function(result){
               $(this).text(btnText);
               window.location.reload();
            });
       });

      /*$(".plan_sub_tab").click(function(){
         var $hashLink = $(".display_plan.active").children('a').attr('href');
         if($(this).hasClass('invoices')) {
            $hashLink = $hashLink +"-invoice";
         } else {
            $hashLink = $hashLink +"-payment";
         }
         $(this).children('a').attr('href',$hashLink);
      });*/

      $('.myplans[data-toggle="tab"]').on('shown.bs.tab', function () {
         var $hashLink = $(this).attr('href');
         if($(".invoices").hasClass('active')) {
            $($hashLink+".invoices_content").addClass('active');
            $(".payment_content").removeClass('active');
         } else {
            console.log($hashLink);
            setTimeout(function(){
               console.log($hashLink);
               $(".invoices_content").removeClass('active');
               $($hashLink+".payment_content").addClass('active');
            },10);  
         }
         
      });
      $(".plan_sub_tab").click(function(){
         /*var $hashLink = $(".display_plan.active").find('a:first').attr('href');
         if($hashLink == undefined && $hashLink == null) {
            $hashLink = $(".display_plan").find('a:first').attr('href');
         }
          */
         if($(this).hasClass('invoices')) {
            $(".invoices_content").addClass('active');
            $(".payment_content").removeClass('active');
         } else {

            $(".payment_content").addClass('active');
            $(".invoices_content").removeClass('active');
         }
      });
      /*
         default active tab
      */
      $(".payment_content").removeClass('active');

      $(".login_as_customer").click(function(){
         var $id =  $(this).attr('id');
         $.post(site_url+'/login_as_customer',{id:$id},function(response){
             window.open(base_url,'_blank');
         });
      });
   });
</script>
<div class="modal fade" id="extendTrial" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div class="modal-body">
            <div class="text-center">
               <h4 class="trial-extend-msg">
               @if($user->user_type == 4)
                  Your Request has been submitted for approval.
               @else 
                  Trial has been extended.
               @endif
               <h4>
            </div>
            <div class="text-center trial-extend">
               <button type="button" class="btn btn-success trialExtend" data-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal" id="thankyou" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog account-model" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel"></h4>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="extendTrialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="exampleModalLabel">Trial extension request</h4>
         </div>
         <div class="modal-body">
            <form>
               <div class="form-group">
                  <label for="extendTrial" class="form-control-label">

                  @if($user->user_type == 4) 
                  Why are you requesting an extension of the free trial?
                  @elseif($user->user_type == 2 || $user->user_type == 1 || $user->user_type == 3)  
                  Why are you extending the free trial?
                  @endif
                  </label>
                  <textarea class="form-control" id="extendTrialText"></textarea>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default extend-btn" value="Save">Submit</button>
         </div>
      </div>
   </div>
</div>

<div class="modal fade restore_cancel_account">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Confirmation </h4>
      </div>
      <div class="modal-body">
        <p>Do you really want to restore this user account?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success restore-account" token="{{$cancel_account_token or ''}}" >Confirm</button>
      </div>
    </div>
  </div>
</div>
@endsection