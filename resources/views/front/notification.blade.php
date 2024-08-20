@extends('front')

@section('main')
{!! HTML::style(asset('assets/css/notification.css')) !!}  
<meta name="_token" content="{!! csrf_token() !!}"/>
<div class="row">
  @include('front.partials.setting-menu')
    <div class="col-lg-9 col-md-9 col-sx-9 account-right"> 
        <div class="white-bg">
            <div class="notification-sec  email_notification">
                  @if(isset($first_time_view ) )
                      <div class="col-lg-12 col-md-12 col-sx-12 require_data first_time_noti_view">
                          <h6 class="static_notification_msg">{{set_notification_message()}}</h6>
                      </div>
                  @endif
                  <div class="row title-row">
                       
                      <div class="col-md-6 col-sm-6 col-xs-4 text-left full-width-text">
                          <h3 class="heding">Email Notifications</h3>
                      </div>
                      <div class="col-md-6 col-sm-6 col-xs-8 text-right full-width-btn">
                          <button type="button" class="btn btn-danger" data-toggle="modal"  onclick="addEmail(this)">Add an email address</button>
                      </div>
                            
                  </div>
                  <div class="email_cont">
                      <h4 class="noti_title">Email adresses</h4>
                      <ul class="list email_list padding-0 email-text">
                          <li class="clearfix ">
                                      <div class="col-md-3 padding-0 email unique-email ">{{$user->email}}</div>
                                      <div class="col-md-5 padding-0 status">
                                          <span class="name">current status:</span>
                                          <span class="value active ">Active</span>
                                      </div>
                                      <div class="col-md-2 padding-0 on-off">
                                          <label class="switch">
                                          <input type="checkbox" class="activeStatus-primary disabled "  checked="checked">
                                          <div class="slider round"></div>
                                          </label>
                                      </div>
                                      <div class="col-md-2 padding-0 edit_remove disabled"><button type="button" class="btn btn-success" data-toggle="modal" id="editbtn" >Edit</button> &nbsp; <a class="remove disabled" href="#" data-toggle="modal">remove</a></div>
                                  </li>
                          @foreach($notificationConfi as $emailData)
                              @if($emailData['obj_type'] == 1)
                                  <li class="clearfix email-list" >
                                      <div class="col-md-3 padding-0 email unique-email email-{{$emailData['id']}}">{{$emailData['obj_value']}}</div>
                                      <div class="col-md-5 padding-0 status status-{{$emailData['id']}}">
                                          <span class="name">current status: </span>
                                          <span class="value{{($emailData['status']==1) ? ' active' : ''}}">{{strtoupper($status[$emailData['status']])}}</span>
                                      </div>
                                      <div class="col-md-2 padding-0 on-off">
                                          <label class="switch">
                                          <input type="checkbox" class="activeStatus activeStatus-{{$emailData['id']}}" onchange="changeStatus(this)" Id ="{{$emailData['id']}}" {{($emailData['is_active']==1)? 'checked =checked':''}}>
                                          <div class="slider round"></div>
                                          </label>
                                      </div>
                                      <div class="col-md-2 padding-0 edit_remove"><button type="button" class="btn btn-success" isactive = "{{$emailData['status']}}" data-toggle="modal" id="editbtn" dataId = "{{$emailData['id']}}" email= "{{$emailData['obj_value']}}" onclick="editEmail(this)">Edit</button> &nbsp; <a class="remove" href="#" data-toggle="modal" email="{{$emailData['obj_value']}}" Id="{{$emailData['id']}}" onclick="deleteEmailData(this)">remove</a></div>
                                  </li>
                              @endif
                          @endforeach
                      </ul>
                  </div>
                        
              </div>
              <div class="notification-sec text_notification">
                  <div class="row title-row"> 
                      <div class="col-md-6 col-sm-6 col-xs-4 text-left full-width-text" >
                          <h3 class="heding">Text Notifications</h3>
                      </div>
                      <div class="col-md-6 col-sm-6 col-xs-8 text-right full-width-btn">
                          <button type="button" class="btn btn-danger add_new_phone_number" data-toggle="modal" data-target="#add_phone" >Add a mobile phone</button>
                      </div>
                  </div>
                  <div class="email_cont">
                      <h4 class="noti_title">Your Phones:</h4>
                      <ul class="list email_list phone_list padding-0 phone-text">
                            {{-- */$i=0;/* --}}
                            @foreach($notificationConfi as $phoneData)
                                @if($phoneData['obj_type'] == 2)
                                    {{--*/$i++/*--}}
                                    <li class="clearfix">
                                        <div class="col-md-3 padding-0 email phone phone-{{$phoneData['id']}}">{{$phoneData['obj_value'] or ''}}</div>
                                        <div class="col-md-5 padding-0 status phone-status-{{$phoneData['id'  ]}}">
                                            <span class="name">current status: </span>
                                            <span class="value{{($phoneData['status']==1) ? ' active' : ''}}">{{strtoupper($status[$phoneData['status']])}}</span>
                                        </div>
                                        <div class="col-md-2 padding-0 on-off">
                                            <label class="switch">
                                                <input type="checkbox" class="activeStatus phone-active-{{$phoneData['id']}}" onchange="changeStatus(this)" Id ="{{$phoneData['id']}}" {{($phoneData['is_active']==1)? 'checked =checked':''}}>
                                                <div class="slider round"></div>
                                            </label>
                                        </div>
                                        <div class="col-md-2 padding-0 edit_remove"><button type="button" class="btn btn-success edit-btn-{{$phoneData['id']}}" data-toggle="modal" receive_noti = "{{$phoneData['is_active']}}" isActive = "{{$phoneData['status']}}" dataId = "{{$phoneData['id']}}" phone= "{{$phoneData['obj_value']}}" countryCode = "{{$phoneData['country_code']}}" onclick="editPhone(this)">Edit</button> &nbsp; <a class="remove" href="#" data-toggle="modal"  phone="{{$phoneData['obj_value']}}" Id="{{$phoneData['id']}}" onclick="deletePhoneData(this)">remove</a></div>
                                    </li>
                                @endif
                            @endforeach
                            <li class="no-record-found <?php echo ($i == 0) ? 'show' : 'hide' ?>">No record Found</li>
                           
                      </ul>
                        </div>
                        
              </div>
              <div class="notification-sec last_notification">
                  <div class="row title-row"> 
                      <div class="col-md-12 text-left">
                          <h3 class="heding">Notification frequency</h3>
                      </div>
                  </div>
                  <div class="email_cont ">
                      <h4 class="noti_title">How many days in advance would you like to be notified of approaching x-dates?</h4>
                          <form class="form-inline">
                                <div class="form-group">
                                    <h4>Via Email</h4>
                                    <span class="border-span">
                                        <select class="selectpicker" id="FrequencyEmail">
                                           
                                            @foreach($frequencies as $frequency)
                                                <option value="{{$frequency->frequency_keys}}" {{($user->noti_email_frequency == $frequency->frequency_keys)?'selected':'' }}>{{($frequency->frequency_keys==0)?'None':$frequency->frequency_keys}}
                                            @endforeach
                                         </select>
                                    </span>
                                  </div>
                                  <div class="form-group phone-frequency">
                                    <h4>Via Text</h4>
                                     <span class="border-span">
                                          <select class="selectpicker" id="FrequencyPhone">
                                            @foreach($frequencies as $frequency)
                                                <option value="{{$frequency->frequency_keys}}" {{($user->noti_mob_frequency == $frequency->frequency_keys)?'selected':'' }} >{{($frequency->frequency_keys==0)?'None':$frequency->frequency_keys}}
                                            @endforeach
                                         </select>
                                         </select>
                                    </span>
                                </div>
                          </form>

                  </div>

                  <div class="email_cont follow-up-noti">
                      <h4 class="noti_title">Would you like to receive follow-up notification of approaching x-dates? </h4>
                          <form class="form-inline">
                                <div class="form-group">
                                    <h4>Via Email</h4>
                                    <span class="border-span">
                                        <select class="selectpicker" id="followUpNotificationEmail">
                                            @foreach($follow_up_frequencies as $key=>$frequency)
                                            <option value="{{$key}}" {{($user->noti_email_followup_frequency == $key)?'selected':'' }}>{{$frequency}}
                                            @endforeach
                                         </select>
                                    </span>
                                  </div>
                                  <div class="form-group phone-frequency">
                                    <h4>Via Text</h4>
                                    <span class="border-span">
                                          <select class="selectpicker" id="followUpNotificationPhone">
                                            @foreach($follow_up_frequencies as $key=>$frequency)
                                                <option value="{{$key}}" {{($user->noti_mob_followup_frequency == $key)?'selected':'' }} >{{$frequency}}
                                            @endforeach
                                         </select>
                                         </select>
                                    </span>
                                </div>
                          </form>

                  </div>

              </div>
        </div>
        <div class="loader">
                <img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading..">
        </div>
    </div>
</div>
 
@endsection


@section('footerscripts')  
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
{!! $validator or '' !!}
@include('front.partials.notification-modals')
<!-- <div class="loader sub-loader">
                <img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading..">
        </div> -->
<script>
    $(document).ready(function(){
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });
    });

</script>

{!! HTML::script(asset('assets/js/jquery.inputmask.js')) !!}
{!! HTML::script(asset('assets/js/profile-manage.js')) !!}
{!! HTML::script(asset('assets/js/notification.js')) !!}
@endsection