
@extends('front')
@section('main')
    <meta name="_token" content="{!! csrf_token() !!}"/>

    <div class="red-tabs user-tabs">
                <!-- Button trigger add new user -->
                <button type="button" class="btn btn-success btn-add-new" id="add_new_xdate_btn">Add New +</button>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#all" aria-controls="all" role="tab" data-toggle="tab">All</a>
                    </li>
                    <li role="presentation">
                        <a href="#live" aria-controls="active" role="tab" data-toggle="tab">Live</a>
                    </li>
                    <li role="presentation">
                        <a href="#converted" aria-controls="active" role="tab" data-toggle="tab">Converted</a>
                    </li>
                    <li role="presentation">
                        <a href="#dead" aria-controls="active" role="tab" data-toggle="tab">Dead</a>
                    </li>
                    <li role="presentation" id="filteration_dropdown_box">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle filterdropdown-btn @if(isset($filters['all'])) active @endif" type="button" id="filterdropdown">
                                Filter By
                                <span class="drop-icon">
                                    <img src="{{asset('assets/images/dropdown-icon.jpg')}}" alt="" />
                                </span>
                            </button>
                             <div class="dropdown-menu arrow_box filtertab-group colum-3" aria-labelledby="filterdropdown">
                                <form id="xdate_filter_form" name="xdate_filter_form">
                                <div class="filtertab-col general-fcol" id="general_form_filter">
                                    <h5 class="flter-head">General</h5>
                                    <div class="filtertab-content">
                                        <div class="filtertab-content-fromto policy-type-fbox">
                                            <label for="">Policy Type <span class="streak-sign">*</span></label>
                                            <div class="quick-date-select">
                                                <select class="form-control" name="f_policy_type" id="f_policy_type">
                                                    <option value="">--Select One--</option>
                                                    <option data-type="" value="-1">All</option>
                                                    <optgroup label="Personal Policies">
                                                        <option data-type="personal-opt" value="-2">All</option>
                                                        @foreach($policyList as $item)
                                                            <option data-type="personal-opt" value="{{$item->id}}">{{$item->name}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    <optgroup label="Commercial Policies">
                                                        <option data-type="commercial-opt" value="-3">All</option>
                                                        @foreach($commercialList as $item)
                                                            <option data-type="commercial-opt" value="{{$item->id}}">{{$item->name}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="filtertab-content-fromto industry-fbox">
                                            <label for="">Industry<span class="streak-sign">*</span></label>
                                            <div class="quick-date-select">
                                                <select class="form-control" name="f_industry" id="f_industry">
                                                    <option value="">--Select One--</option>
                                                    <option value="-1">All</option>
                                                    @foreach ($industryList as $item)
                                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="filtertab-content-fromto location-fbox">
                                            <label for="">Location<span class="streak-sign">*</span></label>
                                            <div class="quick-date-select">
                                                <select class="form-control" id="f_location" name="f_location">
                                                    <option value="">--Select One--</option>
                                                    <option value="-1">All</option>
                                                    @foreach($allLocations as $loc)
                                                        <option value="{{$loc->city.','.$loc->state}}">{{$loc->city.', '.$loc->state}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="filtertab-content-fromto clearstatcache producer-fbox">
                                            <label for="">Producer</label>
                                            <div class="quick-date-select">
                                                <select class="form-control" id="f_producer" name="f_producer">
                                                    <option value="">--Select One--</option>
                                                    <option value="-1">All</option>
                                                    @foreach ($producers as $item)

                                                        @if($item->id == $user->id)
                                                        <option value="{{$item->id}}" selected="selected">
                                                            {{$item->name}} (you)
                                                        @else
                                                        <option value="{{$item->id}}">
                                                            {{$item->name}}
                                                        @endif
                                                        </option>

                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="filtertab-col xdate-fcol" id="x_formto_filter">
                                    <h5>X-Date</h5>
                                    <div class="filtertab-content">
                                        <div class="filtertab-content-fromto x-fromto">
                                            <label for="">Date From</label>
                                            <div class="input-group date form_date" id="div_f_x_from_date" data-date="" data-date-format="mm/dd" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                                                <input class="form-control" size="16" type="text" readonly  name="f_x_from_date" id="f_x_from_date" value="">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                            <label for="">To</label>
                                            <div class="input-group date form_date" id="div_f_x_to_date" data-date="" data-date-format="mm/dd" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                                                <input class="form-control" size="16" type="text" readonly  id="f_x_to_date" name="f_x_to_date" value="">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                        <div class="filtertab-content-quick x-quick-date-box">
                                            <label for=""><span>OR</span> Quick Date</label>
                                            <div class="quick-date-select">
                                                <select class="form-control" id="f_x_quick_date" name="f_x_quick_date">
                                                    <option value="">--Select One--</option>
                                                    <?php
                                                    $date = new DateTime();
                                                    $last_month = $date->modify("-1 month");
                                                    $date = new DateTime();
                                                    $next_month = $date->modify("+1 month");
                                                    ?>
                                                    <option value="{{$next_month->format('m')}}">next month</option>
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
                                        <button type="button"  class="btn btn-default x_clear_btn hidden-xs" disabled>clear all</button>
                                    </div>
                                </div>
                                <div class="filtertab-col xdate-fcol" id="f_formto_filter">
                                    <h5>Follow-up</h5>
                                    <div class="filtertab-content">
                                        <div class="filtertab-content-fromto f-fromto">
                                            <label for="">Date From</label>
                                            <div class="input-group date form_date" id="div_f_f_from_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                                                <input class="form-control" size="16" type="text" readonly name="f_f_from_date" id="f_f_from_date" value="">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                            <label for="">To</label>
                                            <div class="input-group date form_date" data-date="" id="div_f_f_to_date" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                                                <input class="form-control" size="16" type="text" readonly  id="f_f_to_date" name="f_f_to_date" value="">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                        <div class="filtertab-content-quick f-quick-date-box">
                                            <label for=""><span>OR</span> Quick Date</label>
                                            <div class="quick-date-select">
                                                <select class="form-control" id="f_f_quick_date" name="f_f_quick_date">
                                                    <option value="">--Select One--</option>
                                                    <?php
                                                    $date = new DateTime();
                                                    $last_month = $date->modify("-1 month");
                                                    $date = new DateTime();
                                                    $next_month = $date->modify("+1 month");
                                                    ?>
                                                    <option value="{{$next_month->format('m')}}">next month</option>
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
                                        <button type="button" id="x_filter_btn" class="btn btn-default" disabled>filter</button>
                                        <button type="button"  class="btn btn-default x_clear_btn visible-xs-block" disabled>clear all</button>
                                    </div>
                                </div>
                                </form>
<!--
                                <div class="filtertab-col">
                                    <h5>Credit Card Exp.</h5>
                                    <div class="filtertab-content">
                                        <div class="filtertab-content-fromto">
                                            <label for="">Date From</label>
                                            <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                                                <input class="form-control" size="16" type="text" value="">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                            <label for="">To</label>
                                            <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                                                <input class="form-control" size="16" type="text" value="">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                        <div class="filtertab-content-quick">
                                            <label for=""><span>OR</span> Quick Date</label>
                                            <div class="quick-date-select">
                                                <select class="form-control" name="">
                                                    <option value="this month">this month</option>
                                                    <option value="this year">this year</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
-->
                            </div>


                        </div>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active  @if(isset($filters['all']) &&  $filters['all'] != '') hasFilterData @endif table-responsive scrollbox"   data-filter='@if(isset($filters['all']) &&  $filters['all'] != '') {{ $filters['all'] }} @else {} @endif' id="all" >
                        <table class="table table-bordered changeOrder">
                            <thead>
                                <tr class="full-width-tr">
                                    <th align="center">X-Date</th>
                                    <th align="center" class="pro-col-wdth">Producer</th>
                                    <th align="center">Line</th>
                                    <th align="center">Name</th>
                                    <th align="center" class="phone-col-wdth">Phone</th>
                                    <th align="center">Industry</th>
                                    <th align="center"  class="loc-col-wdth">Location</th>
                                    <th align="center" class="last-note-head">Last Note</th>
                                    <th align="center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($xall))

                                    @foreach($xall as $xdate)
                                    <tr class="x-row" data-xdata='{{json_encode($xdate)}}' data-xid="{{$xdate->id}}">
                                        <td>{{$xdate->xdate_txt}}</td>
                                        <td class="pro-col-wdth">{{$xdate->producer_txt}}</td>
                                        <td>{{$xdate->policy_type_txt}}</td>
                                        <td>{{$xdate->xname}}</td>
                                        <td class="phone-col-wdth">{{$xdate->phone}}</td>
                                        <td>{{$xdate->industry_txt}}</td>
                                        <td class="loc-col-wdth">
                                            @if(!empty($xdate->city))
                                                {{$xdate->city}},
                                            @endif
                                            {{$xdate->state}}
                                        </td>
                                        <td class="last-note-txt">
                                        @if($xdate->last_note_txt != '')
                                            {{$xdate->last_note_txt}}
                                        @else
                                            <i>No notes</i>
                                        @endif
                                        </td>
                                        <td>{{$xdate->status_txt}}</td>
                                    </tr>
                                    @endforeach

                                @endif
								<tr><td colspan="10" align="center" class="no-xrecord @if(!empty($xall)) hide @endif">No record(s) found!</td></tr>
                                <tr><td colspan="10" align="center" class="load-xrecord hide">Loading data...</td></tr>
                            </tbody>
                        </table>
                        <div class="clearfix">
                            <div class="no-xrecord-filter all-no-filter-data col-md-12 text-left @if(!isset($filters['all'])) hide @endif">Not seeing enough data? <a href="javascript:removeTabFilter('all');" class="no-filter-click">Click here</a> to remove all filters.</div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane table-responsive @if(isset($filters['live']) &&  $filters['live'] != '') hasFilterData @endif scrollbox"  data-filter='@if(isset($filters['live']) &&  $filters['live'] != '') {{ $filters['live'] }} @else {} @endif' id="live">
                         <table class="table table-bordered changeOrder">
                            <thead>
                                <tr>
                                    <th align="center">X-Date</th>
                                    <th align="center" class="pro-col-wdth">Producer</th>
                                    <th align="center">Line</th>
                                    <th align="center">Name</th>
                                    <th align="center">Contact</th>
                                    <th align="center" class="phone-col-wdth">Phone</th>
                                    <th align="center">Industry</th>
                                    <th align="center" class="loc-col-wdth">Location</th>
                                    <th align="center" class="last-note-head">Last Note</th>
                                    <th align="center">Follow Up</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if(!empty($xlive))

                                    @foreach($xlive as $xdate)
                                    <tr class="x-row"  data-xdata="{{json_encode($xdate)}}" data-xid="{{$xdate->id}}">
                                        <td>{{$xdate->xdate_txt}}</td>
                                        <td class="pro-col-wdth">{{$xdate->producer_txt}}</td>
                                        <td>{{$xdate->policy_type_txt}}</td>
                                        <td>{{$xdate->xname}}</td>
                                        <td>{{$xdate->xcontact}}</td>
                                        <td class="phone-col-wdth">{{$xdate->phone}}</td>
                                        <td>{{$xdate->industry_txt}}</td>
                                        <td class="loc-col-wdth">
                                            @if(!empty($xdate->city))
                                                {{$xdate->city}},
                                            @endif
                                            {{$xdate->state}}
                                        </td>
                                        <td class="last-note-txt">
                                        @if($xdate->last_note_txt != '')
                                            {{$xdate->last_note_txt}}
                                        @else
                                            <i>No notes</i>
                                        @endif
                                        </td>
                                        <td class="follow-date-txt">{{$xdate->follow_up_date}}</td>
                                    </tr>
                                    @endforeach
								@endif
								<tr><td colspan="10" align="center" class="no-xrecord @if(!empty($xlive)) hide @endif">No record(s) found!</td></tr>
                                <tr><td colspan="10" align="center" class="load-xrecord hide">Loading data...</td></tr>
                            </tbody>
                        </table>
                        <div class="clearfix">
                            <div class="no-xrecord-filter live-no-filter-data col-md-12 text-left @if(!isset($filters['live'])) hide @endif">Not seeing enough data? <a href="javascript:removeTabFilter('live');" class="no-filter-click">Click here</a> to remove all filters.</div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane table-responsive @if(isset($filters['converted']) &&  $filters['converted'] != '') hasFilterData @endif scrollbox"  data-filter='@if(isset($filters['converted']) &&  $filters['converted'] != '') {{ $filters['converted'] }} @else {} @endif'  id="converted">
                         <table class="table table-bordered changeOrder">
                            <thead>
                                <tr>
                                    <th align="center">X-Date</th>
                                    <th align="center" class="pro-col-wdth">Producer</th>
                                    <th align="center">Line</th>
                                    <th align="center">Name</th>
                                    <th align="center">Contact</th>
                                    <th align="center" class="phone-col-wdth">Phone</th>
                                    <th align="center">Industry</th>
                                    <th align="center"  class="loc-col-wdth">Location</th>
                                    <th align="center" class="last-note-head">Last Note</th>
                                    <th align="center">Follow Up</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($xconverted))

                                    @foreach($xconverted as $xdate)
                                    <tr class="x-row" data-xdata="{{json_encode($xdate)}}" data-xid="{{$xdate->id}}">
                                        <td>{{$xdate->xdate_txt}}</td>
                                        <td class="pro-col-wdth">{{$xdate->producer_txt}}</td>
                                        <td>{{$xdate->policy_type_txt}}</td>
                                        <td>{{$xdate->xname}}</td>
                                        <td>{{$xdate->xcontact}}</td>
                                        <td class="phone-col-wdth">{{$xdate->phone}}</td>
                                        <td>{{$xdate->industry_txt}}</td>
                                        <td  class="loc-col-wdth">
                                            @if(!empty($xdate->city))
                                                {{$xdate->city}},
                                            @endif
                                            {{$xdate->state}}
                                        </td>
                                        <td class="last-note-txt">
                                        @if($xdate->last_note_txt != '')
                                            {{$xdate->last_note_txt}}
                                        @else
                                            <i>No notes</i>
                                        @endif
                                        </td>
                                        <td class="follow-date-txt">{{$xdate->follow_up_date}}</td>
                                    </tr>
                                    @endforeach

                                @endif
								<tr><td colspan="10" align="center" class="no-xrecord @if(!empty($xconverted)) hide @endif">No record(s) found!</td></tr>
                                <tr><td colspan="10" align="center" class="load-xrecord hide">Loading data...</td></tr>
                            </tbody>
                        </table>
                        <div class="clearfix">
                            <div class="no-xrecord-filter converted-no-filter-data col-md-12 text-left @if(!isset($filters['converted'])) hide @endif">Not seeing enough data? <a href="javascript:removeTabFilter('converted');" class="no-filter-click">Click here</a> to remove all filters.</div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane table-responsive @if(isset($filters['dead']) &&  $filters['dead'] != '') hasFilterData @endif scrollbox" data-filter='@if(isset($filters['dead']) &&  $filters['dead'] != '') {{ $filters['dead'] }} @else {} @endif' id="dead">
                         <table class="table table-bordered changeOrder">
                            <thead>
                                <tr>
                                    <th align="center">X-Date</th>
                                    <th align="center" class="pro-col-wdth">Producer</th>
                                    <th align="center">Line</th>
                                    <th align="center">Name</th>
                                    <th align="center">Contact</th>
                                    <th align="center" class="phone-col-wdth">Phone</th>
                                    <th align="center">Industry</th>
                                    <th align="center" class="loc-col-wdth">Location</th>
                                    <th align="center" class="last-note-head">Last Note</th>
                                    <th align="center">Follow Up</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($xdead))

                                    @foreach($xdead as $xdate)
                                    <tr class="x-row" data-xdata="{{json_encode($xdate)}}" data-xid="{{$xdate->id}}">
                                        <td>{{$xdate->xdate_txt}}</td>
                                        <td class="pro-col-wdth">{{$xdate->producer_txt}}</td>
                                        <td>{{$xdate->policy_type_txt}}</td>
                                        <td>{{$xdate->xname}}</td>
                                        <td>{{$xdate->xcontact}}</td>
                                        <td class="phone-col-wdth">{{$xdate->phone}}</td>
                                        <td>{{$xdate->industry_txt}}</td>
                                        <td class="loc-col-wdth">
                                            @if(!empty($xdate->city))
                                                {{$xdate->city}},
                                            @endif
                                            {{$xdate->state}}
                                        </td>
                                        <td class="last-note-txt">
                                        @if($xdate->last_note_txt != '')
                                            {{$xdate->last_note_txt}}
                                        @else
                                            <i>No notes</i>
                                        @endif
                                        </td>
                                        <td class="follow-date-txt">{{$xdate->follow_up_date}}</td>
                                    </tr>
                                    @endforeach
                                @endif

								<tr><td colspan="10" align="center" class="no-xrecord @if(!empty($xdead)) hide @endif">No record(s) found!</td></tr>
                                <tr><td colspan="10" align="center" class="load-xrecord hide">Loading data...</td></tr>

                            </tbody>
                        </table>
                        <div class="clearfix">
                            <div class="no-xrecord-filter dead-no-filter-data col-md-12 text-left @if(!isset($filters['dead'])) hide @endif">Not seeing enough data? <a href="javascript:removeTabFilter('dead');" class="no-filter-click">Click here</a> to remove all filters.</div>
                        </div>
                    </div>
                </div>

            </div>

     <?php $defaultLine = ''; $defaultLineText = ''; ?>

      @foreach ($linesList as $item)
        @if(strtolower($item->name) == 'commercial')
            <?php $defaultLine = $item->id; $defaultLineText = $item->name; ?>
        @endif
     @endforeach

     @include('front.partials.xdate-add-edit-form')

     <div class="modal fade" id="update_user_data_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog account-model" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Update your missing profile information</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="updateUserDataForm">
                                    <fieldset class="form-group name">
                                        <label for="exampleInputEmail1">Company Name</label>
                                        <span class="border-span"><input type="text" name='com_name' class="form-control" id="exampleInputName"></span>
                                        <span class="error-msg" style="color:#a94442;display:none;" id="name_error"></span>
                                    </fieldset>
                                    <fieldset class="form-group text-right button-right">
                                        <button type="button" class="btn btn-success" id="update_user_date_btn" data-toggle="modal" data-target="" onclick="updateUserData()">Update</button>
                                    </fieldset>
                                </form>
                            </div>
                            <div class="clearfix"></div>
                            <div id="profile_data_success" class="alert alert-success hide">
                                Profile data has saved succesfully.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="account_expired" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog account-model" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">{{$popUpData['title'] or ''}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form method="post" id="accountExpired">
                                    {!! $popUpData['content'] or ''!!}
                                    <p class="error"></p>
                                    <div class="text-right pull-right">
                                        <button type="button" class="btn btn-danger @if(!isset($popUpData['logout_btn']) || $popUpData['logout_btn'] == 0) hide @endif" @if(isset($popUpData['allow_access']) && $popUpData['allow_access'] == 1 ) onclick="logout(); " @else data-dismiss="modal" @endif>{{$popUpData['logout_text'] or ''}}</button>
                                        <button type="button" class="btn btn-success @if(!isset($popUpData['signup_btn']) || $popUpData['signup_btn'] == 0) hide @endif" onclick="gotoCard({{$popUpData['redirect_plan']}});">{{$popUpData['signup_text'] or ''}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection



@section('footerscripts')

	{!! HTML::script(asset('assets/js/bootstrap-datetimepicker.min.js')) !!}
    {!! HTML::script(asset('assets/js/select2.min.js')) !!}
	{!! HTML::script(asset('assets/js/enscroll-0.6.1.min.js')) !!}
    {!! HTML::script(asset('assets/js/jquery.inputmask.js')) !!}
    {!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
	<script type="text/javascript">

    var xBlankData = {owner_id:'',user_id:'',xdate:'',xdate_txt:'',xname:'',line:'{{$defaultLine}}',line_txt:'{{$defaultLineText}}',policy_type:'',policy_type_txt:'',industry:'',industry_txt: '',xcontact:'',producer:{{$user->   id}},producer_txt:'{{$user->name}}',phone:'',city:'',state:'',website:'',email:'',follow_up_date:'',follow_up_date_txt:'',status:'',status_txt:'',created_at:'',updated_at:'', notes:null};
	var currentUser = { user_type : '{{$user->user_type}}', id : '{{$user->id}}' };
    var personPolicy = {!!@json_encode($policyList)!!};
    var commericalList = {!!@json_encode($commercialList)!!};
    var profileImgPath = '{{url(config('constants.FILEUPLOAD'))}}';
    $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });


   $(document).ready(function(){

        @if((isset($popUpData['show_pop_up']) && $popUpData['show_pop_up'] == 0))
            $('#account_expired').modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#account_expired').modal('show');
        @endif

        $( "#updateUserDataForm" ).validate({
            errorElement: 'span',
            errorClass: 'help-block error-help-block',

            errorPlacement: function(error, element) {
                if (element.parent('.input-group').length || element.parent('.border-span').length ||
                    element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                    error.insertAfter(element.parent());
                   // else just place the validation message immediatly after the input
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error'); // add the Bootstrap error class to the control group
            },
              rules: {
                com_name: "required",
              },
                messages: {
                 com_name: "Please fill Company Name.",
                }

        });



       // show.bs.tab

        @if(isset($askTofillMisData) && $askTofillMisData == true )
            $('#update_user_data_modal').modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#update_user_data_modal').modal('show');
        @endif
   });
    // Ajax Call for Edit users
function updateUserData() {
     if($( "#updateUserDataForm" ).valid()){
        $('#updateUserDataForm .error-msg').hide();
        $('#profile_data_success').addClass('hide');
        $('#update_user_date_btn').addClass('disabled').text('Updating...');

            $.ajax({
               method: 'POST',
               url: '{{url('/updateUserData')}}',
               data: {
                com_name: $("input[name=com_name]").val(),
               },
               success: function (data) {
                  //$('#' + id).parent().parent().remove();
                    $('#update_user_date_btn').removeClass('disabled').text('Update');
                    $('#profile_data_success').removeClass('hide');
                    setTimeout(function(){
                        $('#update_user_data_modal').modal('hide');
                    },1500);
               },
              error: function (jqXHR, exception) {
                      $('#update_user_date_btn').removeClass('disabled').text('Save');

                      $('#updateUserDataForm .error-msg').show();


                      if (jqXHR.status == 422) {
                           var parsed = JSON.parse(jqXHR.responseText);
                          if (typeof parsed.name != 'undefined') {
                            $("#name_error").append(parsed.name[0]);
                          }
                          if (typeof parsed.email != 'undefined') {
                            $("#name_error").append(parsed.email[0]);
                          }
                      }
                },

            });
    }else{
        //$('#updateUserDataForm .error-msg').hide();
    }
}
    function logout()
    {
        window.location.href="{{url('logout')}}";
    }
    function gotoCard($redirect_to)
    {
        console.log($redirect_to);
        if($redirect_to == 1 ) {
            window.location.href="{{url('planbill/change-plan')}}";    
        } else {
            window.location.href="{{url('planbill/card')}}";
        }
    }
	</script>
    {!! HTML::script(asset('assets/js/manage-xdate.js')) !!}
@endsection
