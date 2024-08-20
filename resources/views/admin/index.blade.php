@extends('back')
@section('main')

<meta name="_token" content="{!! csrf_token() !!}"/>
<div class="red-tabs user-tabs">
  <!-- Nav tabs -->
   <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="tab-btn active">
         <a href="#all" aria-controls="all" role="tab" data-toggle="tab">All</a>
      </li>
      <li role="presentation" class="tab-btn">
         <a href="#active" aria-controls="active" role="tab" data-toggle="tab">Active</a>
      </li>
      <li role="presentation" class="tab-btn">
         <a href="#inactive" aria-controls="inactive" role="tab" data-toggle="tab">Inactive</a>
      </li>
      <li role="presentation" id="filteration_dropdown_box">
         <div class="dropdown">
            <button class="btn dropdown-toggle filterdropdown-btn" type="button" id="filterdropdown">
            Filter By
            <span class="drop-icon">
            <img src="{{asset('assets/images/dropdown-icon.jpg')}}" alt="" />
            </span>
            </button>
            <form id="filterForm">
               <div class="dropdown-menu arrow_box filtertab-group colum-4" aria-labelledby="filterdropdown" id="filtertab-group">
                  <div class="filtertab-col sign-up">
                     <h5>sign-up</h5>
                     <div class="filtertab-content sign-up-filter">
                        <div class="filtertab-content-fromto">
                           <label for="">Date From</label>
                           <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                              <input class="form-control" size="16" type="text" value="" id="signUpfrom" name="sign_up_from" readonly>
                              <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                           </div>
                           <label for="">To</label>
                           <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                              <input class="form-control" size="16" type="text" value="" id="signUpTo" name="signup_to" readonly>
                              <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                           </div>
                        </div>
                        <div class="filtertab-content-quick ">
                           <label for=""><span>OR</span> Quick Date</label>
                           <div class="quick-date-select">
                              <select class="form-control"  id="signUpQuickDate" name="sign_up_quick">
                                 <?php
                                    $date = new DateTime();
                                    $last_month = $date->modify("-1 month");
                                    $date = new DateTime();
                                    $next_month = $date->modify("+1 month");
                                    ?>
                                 <option value="">--Select One--</option>
                                 <option value="{{$last_month->format('m')}}">last month</option>
                                 <option value="{{date('m')}}">this month</option>

                                 @for($i=2;$i<=13; $i++)
                                 {{--*/ @$time =  mktime(0,0,0,$i,0,0)  /*--}}
                                 <option value={{date('m',$time)}}>{{date('F',strtotime(date('F',$time)))}}
                                 </option>
                                 @endfor
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="filtertab-col trial-exp">
                     <h5>Trial Exp.</h5>
                     <div class="filtertab-content trial-filter">
                        <div class="filtertab-content-fromto">
                           <label for="">Date From</label>
                           <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                              <input class="form-control" size="16" type="text" value="" id="trialFrom" name="trial_from" readonly>
                              <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                           </div>
                           <label for="">To</label>
                           <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                              <input class="form-control" size="16" type="text" value="" id="trialTo" name="trial_to" readonly>
                              <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                           </div>
                        </div>
                        <div class="filtertab-content-quick">
                           <label for=""><span>OR</span> Quick Date</label>
                           <div class="quick-date-select">
                              <select class="form-control" name="trail_quick" id="trialQuick">
                                 <?phpdata
                                    $date = new DateTime();
                                    $last_month = $date->modify("-1 month");
                                    $date = new DateTime();
                                    $next_month = $date->modify("+1 month");
                                    ?>
                                 <option value="">--Select One--</option>
                                 <option value="{{$last_month->format('m')}}">last month</option>
                                 <option value="{{date('m')}}">this month</option>
                                 <option value="{{$next_month->format('m')}}">next month</option>
                                 @for($i=2;$i<=13; $i++)
                                 {{--*/ @$time =  mktime(0,0,0,$i,0,0)  /*--}}
                                 <option value={{date('m',$time)}}>{{date('F',strtotime(date('F',$time)))}}
                                 </option>
                                 @endfor
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="filtertab-col account-exp">
                     <h5>Account Exp.</h5>
                     <div class="filtertab-content account-filter">
                        <div class="filtertab-content-fromto">
                           <label for="">Date From</label>
                           <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                              <input class="form-control" size="16" type="text" value="" name="account_from"  id="accountFrom" readonly>
                              <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                           </div>
                           <label for="">To</label>
                           <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                              <input class="form-control" size="16" type="text" value="" name="account_to" id="accountTo" readonly>
                              <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                           </div>
                        </div>
                        <div class="filtertab-content-quick">
                           <label for=""><span>OR</span> Quick Date</label>
                           <div class="quick-date-select">
                              <select class="form-control" name="account_quick" id="accountQuick">
                                 <?php
                                    $date = new DateTime();
                                    $last_month = $date->modify("-1 month");
                                    $date = new DateTime();
                                    $next_month = $date->modify("+1 month");
                                    ?>
                                 <option value="">--Select One--</option>
                                 <option value="{{$last_month->format('m')}}">last month</option>
                                 <option value="{{date('m')}}">this month</option>
                                 <option value="{{$next_month->format('m')}}">next month</option>
                                 @for($i=2;$i<=13; $i++)
                                 {{--*/ @$time =  mktime(0,0,0,$i,0,0)  /*--}}
                                 <option value={{date('m',$time)}}>{{date('F',strtotime(date('F',$time)))}}
                                 </option>
                                 @endfor
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="filtertab-col credit-card-exp">
                     <h5>Credit Card Exp.</h5>
                     <div class="filtertab-content credit-card-filter">
                        <div class="filtertab-content-fromto">
                           <label for="">Date From</label>
                           <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                              <input class="form-control" size="16" type="text" value="" id="creditFrom" name="credit_from" readonly>
                              <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                           </div>
                           <label for="">To</label>
                           <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                              <input class="form-control" size="16" type="text" id="creditTo" name="credit_to" readonly>
                              <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                           </div>
                        </div>
                        <div class="filtertab-content-quick">
                           <label for=""><span>OR</span> Quick Date</label>
                           <div class="quick-date-select">
                              <select class="form-control" name="credit_quick" id="creditQuick">
                                 <?php
                                    $date = new DateTime();
                                    $last_month = $date->modify("-1 month");
                                    $date = new DateTime();
                                    $next_month = $date->modify("+1 month");
                                    ?>
                                 <option value="">--Select One--</option>
                                 <option value="{{$last_month->format('m')}}">last month</option>
                                 <option value="{{date('m')}}">this month</option>
                                 <option value="{{$next_month->format('m')}}">next month</option>
                                 @for($i=2;$i<=13; $i++)
                                 {{--*/ @$time =  mktime(0,0,0,$i,0,0)  /*--}}
                                 <option value={{date('m',$time)}}>{{date('F',strtotime(date('F',$time)))}}
                                 </option>
                                 @endfor
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="clear">&nbsp;</div>
                  <div class="filtertab-buttons">
                     <button type="reset" class="btn btn-danger clear-all" >clear all</button>
                     <button type="button" class="btn btn-success filter">Filter</button>
                  </div>
               </div>
            </form>
         </div>
      </li>
   </ul>
   <!-- Tab panes -->
   <div class="tab-content">
      <div role="tabpanel" class="tab-pane active table-responsive scrollbox" id="all"  data-filter ='{{$pastFilter['all']}}'>
      <table class="user-details table table-bordered changeOrder ">
         <thead>
            <tr >
               <th align="center">
                  Name
               </th>
               <th align="center">
                  Status
               </th>
               <th align="center">
                  Sign-Up
               </th>
               <th align="center">
                  Trial Exp.
               </th>
               <th align="center">
                  Account Exp.
               </th>
               <th align="center">
                  Credit Card Number
               </th>
               <th align="center">
                  Credit Card Exp.
               </th>
            </tr>
         </thead>
         <tbody class="allData" >
            @if( !empty($allClients))
            @foreach($allClients as  $client)
            <tr class='clickable-row alldata'   data-href={{url("admin/user/$client->id")}}>
               <td>
                  {{$client->com_name or '-'}}
               </td>
               <td>
                  {{$status[$client->status]}}
               </td>
               <td>
                  {{$client->createDate or ''}}
               </td>
               <td>
                  {{$client->trial_end_date or ''}}
               </td>
               <td>
                  @if($client->status = 1 && $client->current_plan != 0)
                  {{$client->account_exp or ''}}
                  @endif
               </td>
               @if(!empty($client->card))
               <td>
                  {{$client->card->card_no or ''}}
               </td>
               <td>
                  {{$client->card->expiry_date or  ''}}
               </td>
               @else
               <td>
                  {{$client->card_no or ''}}
               </td>
               <td>
                  {{$client->expiry_date or ''}}
               </td>
               @endif
            </tr>
            @endforeach
            @else
            <tr>
               <td colspan = "7" align="center"><strong>No record found</strong></td>
            </tr>
            @endif
         </tbody>
      </table>
      <div class="clearfix">
         <div  id="all-filter" class="no-xrecord-filter dead-no-filter-data col-md-12 text-left @if($allClients_filter == 'no-filter') hide @endif">Not seeing enough data? <a href="javascript:void(0)" class="no-filter-click">Click here</a> to remove all filters.</div>
      </div>
   </div>
   <div role="tabpanel" class="tab-pane table-responsive scrollbox" id="active" data-filter ='{{$pastFilter['active']}}'>
   <table class="table table-bordered changeOrder">
      <thead>
         <tr>
            <th align="center" >
               Name
            </th>
            <th align="center">
               Sign-Up
            </th>
            <th align="center">
               Trial Exp.
            </th>
            <th align="center">
               Credit Card Number
            </th>
            <th align="center">
               Credit Card Exp.
            </th>
         </tr>
      </thead>
      <tbody class="activeData">
         @if(!empty($activeClients))
         @foreach($activeClients as $activeclient)
         <tr class='clickable-row activedata'   data-href={{url("admin/user/".$activeclient->id)}}>
            <td>
               {{$activeclient->com_name or '-'}}
            </td>
            <td>
               {{$activeclient->createDate or ''}}
            </td>
            <td>
              {{$activeclient->trial_end_date or ''}}
            </td>
            @if(!empty($activeclient->card))
            <td>
               {{$activeclient->card->card_no or ''}}
            </td>
            <td>
               {{$activeclient->card->expiry_date or ''}}
            </td>
            @else
            <td>
               {{$activeclient->card_no or ''}}
            </td>
            <td>
               {{$activeclient->expiry_date or ''}}
            </td>
            @endif
         </tr>
         @endforeach
         @else
         <tr>
            <td colspan = "5" align="center"><strong>No record found</strong></td>
         </tr>
         @endif
      </tbody>
   </table>
   <div class="clearfix" >
      <div id="active-filter" class="no-xrecord-filter dead-no-filter-data col-md-12 text-left @if($activeClients_filter == 'no-filter') hide @endif">Not seeing enough data? <a href="javascript:void(0)" class="no-filter-click">Click here</a> to remove all filters.</div>
   </div>
</div>
<div role="tabpanel" class="tab-pane table-responsive scrollbox" id="inactive" data-filter ='{{$pastFilter['inactive']}}' >
<table class="table table-bordered changeOrder">
   <thead>
      <tr>
         <th align="center">
            Name
         </th>
         <th align="center">
            Sign-Up
         </th>
         <th align="center">
            Trial Exp.
         </th>
         <th align="center">
            Account Exp.
         </th>
         <th align="center">
            Credit Card Number
         </th>
         <th align="center">
            Credit Card Exp.
         </th>
      </tr>
   </thead>
   <tbody class="inactiveData">
      @if( !empty($inactiveClients))
      @foreach($inactiveClients as $inactiveclient)
      <tr class='clickable-row inactivedata'   data-href={{url("admin/user/$inactiveclient->id")}}>
         <td>
            {{$inactiveclient->com_name or '-'}}
         </td>
         <td>
            {{$inactiveclient->createDate or ''}}
         </td>
         <td>
            {{$inactiveclient->trial_end_date or ''}}
         </td>
         <td>
            @if($inactiveclient->current_plan != 0) 
               {{$inactiveclient->account_exp or ''}}
            @endif
         </td>
         @if(!empty($inactiveclient->card))
         <td>
            {{$inactiveclient->card->card_no or ''}}
         </td>
         <td>
            {{$inactiveclient->card->expiry_date or ''}}
         </td>
         @else
         <td>
            {{$inactiveclient->card_no or ''}}
         </td>
         <td>
            {{$inactiveclient->expiry_date or ''}}
         </td>
         @endif
      </tr>
      @endforeach
      @else
      <tr>
         <td colspan = "6" align="center"><strong>No record found</strong></td>
      </tr>
      @endif
   </tbody>
</table>
<div class="clearfix" >
   <div id="inactive-filter" class="no-xrecord-filter dead-no-filter-data col-md-12 text-left @if($inactiveClients_filter == 'no-filter') hide @endif">Not seeing enough data? <a href="javascript:void(0)" class="no-filter-click">Click here</a> to remove all filters.</div>
</div>

</div>
</div>
<div class="loader">
   <img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading..">
</div>
@endsection

@section('footerscripts')
{!! HTML::script(asset('assets/js/bootstrap-datetimepicker.min.js')) !!}
{!! HTML::script(asset('assets/js/enscroll-0.6.1.min.js')) !!}
{!! HTML::script(asset('assets/js/admin_home.js')) !!}
{!! HTML::script(asset('assets/js/iscroll.js')) !!}
<script type="text/javascript">
   $(document).ready(function() {
      //var myScroll = new IScroll('.admin-xdates');
       @if(Session::get('approvedTrial') == 1)
       $("#thankyou").modal('show');
       setTimeout(function() {
           $("#thankyou").modal('hide');
       }, 5200);
       <?php Session::forget('approvedTrial'); ?>
       @endif
       $(".filter,.clear-all,.no-filter-click").click(function() {
           if (window.location.hash == '#active') {
               $("#active").find('tbody').html('');
               var tab_name = "active";
               var resetFilter = window.location.hash + '-filter';
               $("#" + tab_name + '-filter').removeClass('hide');
           } else if (window.location.hash == '#inactive') {
               $("#inactive").find('tbody').html('');
               var tab_name = "inactive";
               var resetFilter = window.location.hash + '-filter';
               $("#" + tab_name + '-filter').removeClass('hide');
           } else {
               $("#all").find('tbody').html('');
               var tab_name = "all";
               var resetFilter = window.location.hash + '-filter';
               $("#" + tab_name + '-filter').removeClass('hide');
           }

           if ($(this).hasClass('clear-all')) {
               resetFilterForm();
               $("#" + tab_name + '-filter').addClass('hide');
               $("#" + tab_name).attr('data-filter', '');
               $('#filterForm').trigger('reset');
           } else if ($(this).hasClass('no-filter-click')) {
               resetFilterForm();
               if(tab_name != '' && tab_name != null) {
                  $("#" + tab_name).attr('data-filter', '');
                  $("#" + tab_name + '-filter').addClass('hide');
               } else {
                  $("#all").attr('data-filter', '');
                  $("#all-filter").addClass('hide');
               }
               $('#filterForm').trigger('reset');
           }
           $("#filtertab-group").hide();
           $("."+tab_name+"Data").html('');

           if(tab_name == 'active') {
               var col = 5;
           } else if(tab_name == 'inactive') {
               var col = 6;
           } else {
               var col = 7;
           }
           $("."+tab_name+"Data").append("<tr class='loading'><td align='center' colspan="+col+">Loading data...</td></tr>");
           var filterData = $('#filterForm').serialize();

           $.post(site_url + "/filterData", {
               data: filterData,
               tab_name: tab_name
           }, function(result) {
               $("."+tab_name+"Data").html('');
               $("#" + tab_name).attr('data-filter', result.jsonFilter);
               if (result.filterData.length != 0) {
                   $.each(result.filterData, function(key, value) {
                       if (value.length == 0 && key == 'active') {
                           var htmlactive = "<tr><td colspan="+col+" align='center'><strong>No record found</strong></td></tr>";
                           $(".activeData").append(html);
                       }
                       if (value.length == 0 && key == 'inactive') {
                           var htmlinactive = "<tr><td colspan="+col+" align='center'><strong>No record found</strong></td></tr>";
                           $(".inactiveData").append(html);
                       }
                       $.each(value, function(subkey, subdata) {

                           if (typeof subdata.card != 'undefined' && subdata.card != null) {

                               var card = subdata.card.card_no;
                               var expiry_date = subdata.card.expiry_date;
                           } else if ((typeof subdata.card_no != 'undefined' && subdata.card_no != null)) {
                               var card = subdata.card_no;
                               var expiry_date = subdata.expiry_date;
                           } else {
                               var card = "";
                               var expiry_date = "";
                           }
                           if (subdata.status == 0) {
                               var status = 'inactive';
                           } else if (subdata.status == 1) {
                               var status = 'active';
                           } else if (subdata.status == 3) {
                               var status = 'account expired';
                           }
                           if(!$('html').hasClass('touch')){
                               floatThead();
                           }
                          // reFloatThead();
                           if (key == 'allData' && tab_name == 'all') {
                               var html = "<tr class='clickable-row alldata' data-href=" + '{{url("admin/user/")}}/' + subdata.id + " role='row'><td>" + subdata.com_name + "</td><td>" + status + "</td><td>" + subdata.createDate + "</td><td>" + subdata.trial_end_date + "</td><td>" + subdata.account_exp + "</td><td>" + card + "</td><td>" + expiry_date + "</td></tr>";
                               //$(".allData").append(html);
                               $('.allData').append(html);
                           }
                           if (key == 'active' && tab_name == 'active') {
                               var html = "<tr class='clickable-row activedata' data-href=" + '{{url("admin/user/")}}/' + subdata.id + " role='row'><td>" + subdata.com_name + "</td><td>" + subdata.createDate + "</td><td>" + subdata.trial_end_date + "</td>  <td>" + card + "</td><td>" + expiry_date + "</td></tr>";
                               $('.activeData').append(html);
                           }
                           if (key == 'inactive' && tab_name == 'inactive') {
                               var html = "<tr class='clickable-row' data-href=" + '{{url("admin/user/")}}/' + subdata.id + " role='row'><td>" + subdata.com_name + "</td><td>" + subdata.createDate + "</td><td>" + subdata.trial_end_date + "</td><td>" + subdata.account_exp + "</td><td>" + card + "</td><td>" + expiry_date + "</td></tr>";
                               //$(".inactiveData").append(html);
                               $('.inactiveData').append(html);
                           }
                           $("#all > table,#active > table,#inactive > table").trigger('update');
                       });
                   });
               } else {
                  var htmlactive = "<tr><td colspan="+col+" align='center'><strong>No record found</strong></td></tr>";
                  $("."+tab_name+"Data").append(htmlactive);
                   /*if (tab_name == 'active') {
                       var htmlactive = "<tr><td colspan='5' align='center'><strong>No record found</strong></td></tr>";
                       $(".activeData").append(htmlactive);
                   } else if (tab_name == 'inactive') {
                       var htmlinactive = "<tr><td colspan='6' align='center'><strong>No record found</strong></td></tr>";
                       $(".inactiveData").append(htmlinactive);
                   } else if (tab_name == 'all') {
                       var htmlAllData = "<tr><td colspan='7' align='center'><strong>No record found</strong></td></tr>";
                       $(".allData").append(htmlAllData);
                   }*/
               }
              /* $(".loading").hide();
               $('.loading').remove();*/
           });
       });
   });
</script>
<div class="modal" id="thankyou" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog account-model" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Trial period successfully extended!</h4>
         </div>
      </div>
   </div>
</div>
@endsection
