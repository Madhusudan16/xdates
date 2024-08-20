@extends('back')
@section('main')
<meta name="_token" content="{!! csrf_token() !!}"/>
<div class="row">
   @include('admin.partials.setting-menu')
   <div class="col-lg-9 col-md-9 col-sx-9 account-right">
      <div class="white-bg">
         <div class="row">
            <div class="col-md-7 col-sm-7">
               <div class="add_user trial_myprofile">
                  <span class="table_title trial_list_title">Trial Extension Requests</span>

               </div>
            </div>
        </div>
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="activated">
               <div class="table-responsive">
                  <table class="table table-bordered sortable-table" id="activate_tab">
                     <thead>
                        <tr>
                           <th>
                              Client Name
                           </th>
                           <th>
                              Requested by
                           </th>
                           <th>
                              Requested date
                           </th>
                           <th class="no-sort">
                              &nbsp;
                           </th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($trialList as $detail)
                        <?php 
                          $user_id = isset($detail['get_user']->id) ? $detail['get_user']->id : 0;
                        ?>
                        <tr>
                           <td>
                              <a href="{{url('admin/user/'.$user_id)}}">{{$detail['get_user']->com_name or ''}}</a>
                           <td>
                              {{$detail['get_requester_user']->name or ''}}
                           </td>
                           <td>
                              {{date("m/d/Y", strtotime($detail->created_at))}}
                           </td>

                           <td>
                              <button type="button" class="btn btn-success text-uppercase approve_trial" user="{{$detail['get_user']->id or ''}}" data-toggle="modal"  >Approve</button>
                              <button type="button" class="btn btn-primary decline_trial" data-toggle="modal" user="{{$detail['get_user']->id or ''}}"  >Decline</button>
                              
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
               @if($trialList->isEmpty())
               <div class="row" id="act">
                  <div class="col-md-12">
                     <p class="error_mas">
                       No trial extend request data found.
                     </p>
                  </div>
               </div>
               @endif 
            </div>
            
         </div>
      </div>
      <div class="loader">
         <img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading..">
      </div>
   </div>
</div>
</div> 
</section>
<div class="modal fade" id="edit_trial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog trial_modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Trial Extension Declination</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                             <form>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <p class="error"></p>
                                        <label for="trial_decline">Why are you declining this trial extension request?</label>
                                        <textarea class= "form-control" id="trial_decline" name="trial_decline"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group text-right button-right pull-right">
                                        <button type="button" who="" class="btn btn-danger trial_decline_comfirm_btn" onclick="extend(this,1)" data-toggle="modal">Confirm</button>
                                        <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                            </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="comfirm_trial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Are yor sure you want to extend trial for this user?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12">
                            <p class="error"></p>
                            <p>After confirm this user will  get 30 days trial.</p>
                            <div class="text-right">
                                <button type="button" who=""  class="btn btn-success trial_approve_comfirm"  onclick="extend(this,0)">Confirm</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
{{ HTML::script('assets/js/trial_extend.js') }}
@endsection