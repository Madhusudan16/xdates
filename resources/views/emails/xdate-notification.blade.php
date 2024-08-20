@extends('emails.mailtemplate')
@section('title','Notification')
@section('content')
<style>

</style>
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;" class="responsive-table">
   <tr>
      <td>
         <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
               <td>
                  <!-- COPY -->
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                     <tr>
                        <td align="left" style="font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding">
                        @if($frequencyType == 2)
                           X-Dates: Follow-up Notification
                        @else
                           Xdates Notification
                        @endif
                        </td>
                     </tr>
                     <tr>
                        <td><p>
                        @if($frequencyType == 2)
                           <?php $temp_type = "follow-ups"; ?>
                        @else
                           <?php $temp_type = "x-dates"; ?>
                        @endif
                        You asked us to remind you of the below {{$temp_type}} you scheduled. Tired of receiving these notifications? Scroll to the bottom of this email for instructions on cancelling them.
                        </p></td>
                     </tr>
                  </table>
                  <?php $status = array('Live','Converted'); ?>
                  <table class="xdate-table" width="100%"  border="1">
                        <thead>
                           <tr class = "xdate-head">
                              <th align="center" style="padding: 1%;   font-family: Helvetica, Arial, sans-serif; white-space: nowrap;" class="padding pro-col-wdth">X-Date</th>
                              <th align="center" style="padding: 1%;  font-family: Helvetica, Arial, sans-serif;  white-space: nowrap;" class="padding pro-col-wdth">Producer</th>
                              <th align="center" style="padding: 1%;   font-family: Helvetica, Arial, sans-serif;  white-space: nowrap;" class="padding" >Name</th>
                              <th align="center" style="padding: 1%;  line-height: 25%; font-family: Helvetica, Arial, sans-serif;  white-space: nowrap;" class="padding loc-col-wdth"  >Location</th>
                              <th width="130" align="center" style="padding: 1%;   font-family: Helvetica, Arial, sans-serif;  white-space: nowrap;" class="padding pro-col-wdth">Follow-up Date</th>
                              <th align="center" style="padding: 1%; font-family: Helvetica, Arial, sans-serif;  white-space: nowrap;" class="padding" >Status</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if(!empty($xdateData))
                              @foreach($xdateData as $xdate)
                                 <tr class="x-row xdate-body" >
                                    <td  align="center" style="padding: 1%;   font-family: Helvetica, Arial, sans-serif; word-wrap: break-word;width:50px" class="padding" >{{date('m/d',strtotime($xdate['xdate']))}}</td>
                                    <td align="center" style="padding: 1%; font-family: Helvetica, Arial, sans-serif; word-wrap: break-word; width:70px;" class="padding" >{{$xdate['user_name']}}</td>
                                    <td align="center" style="padding: 1%; font-family: Helvetica, Arial, sans-serif; " class="padding">{{$xdate['xname']}}</td>
                                    <td align="center" style="padding: 1%; font-family: Helvetica, Arial, sans-serif; word-wrap: break-word;" class="padding">
                                       @if(!empty($xdate['city']))
                                       {{$xdate['city']}},
                                       @endif
                                       {{$xdate['state']}}
                                    </td>
                                    <td align="center" style="padding: 1%; font-family: Helvetica, Arial, sans-serif; color: #666666;  word-wrap: break-word;max-width:100px" class="padding " >{{date('m/d/Y',strtotime($xdate['follow_up_date']))}}</td>
                                    <td align="center" align="center" style="font-family: Helvetica, Arial, sans-serif; color: #666666; min-width:60px" class="padding" >{{$status[$xdate['status']]}}</td>
                                 </tr>
                              @endforeach
                           @endif
                         </tbody>
                     </table>
               </td>

            </tr>
         </table>
      </td>
   </tr>
   <tr>
      <td>
            <p>
               @if($frequencyType == 2)
                     To edit notification settings, login to your account and go to the <strong>Notifications</strong> tab under <strong>Settings</strong>. The drop-down at the bottom of the page titled <strong>Via Email</strong> will allow you to select between <strong>None</strong>, <strong>Daily</strong> and <strong>Weekly</strong>.
               @else
                     To edit notification settings, login to your account and go to the <strong>Notifications</strong> tab under <strong>Settings</strong>. The drop-down at the bottom of the page titled <strong>Via Email</strong> will allow you to select option.
               @endif
            </p>
      </td>
   </tr>
</table>
@endsection
