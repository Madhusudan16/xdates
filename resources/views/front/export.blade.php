@extends('front')
@section('main')
<meta name="_token" content="{!! csrf_token() !!}"/>
<div class="row">
   @include('front.partials.setting-menu')
   <div class="col-lg-9 col-md-9 col-sx-9 account-right">
      <div class="right-profile {{$export_page or ''}}">
         <div class="export_sec">
            <div class="export_top">
               <h3 class="heding">Export data one of the below formats</h3>
               <button type="button" onclick="goToSubPage();" class="btn export"><span>CSV</span> &nbsp;<img src="{{url('assets/images/ex_btn.png')}}"></button> <!-- &nbsp; &nbsp; <button type="button" onclick="" class="btn export"><span>EXCEL</span> &nbsp; <img src="{{url('assets/images/ex_btn.png')}}"></button> -->
            </div>
            <div class="export_bottom">
               <h3 class="heding">Exports available to download</h3>
               <p>Your data exports will appear here as files for download</p>
            </div>
         </div>
      </div>
      <div class="right-profile {{$export_sub_page or ''}}">
         <div class="export_select_sec">
            <div class="export_top clearfix">
               <h3 class="heding">Export the following data</h3>
               <p>Select one or more from below and then choose the format to the right to download.</p>
               <div class="col-md-8 padding-0 btn_left">
                  <button type="button" class="btn btn-primary export_btn xdates " data-btn = "export_xdates" data-csv = "xdates">XDates</button> &nbsp;<!--  <button type="button" class="btn btn-primary export_btn  export_btn_active organizations" data-csv ="organizations">Organizations</button> &nbsp; <button type="button" class="btn btn-primary export_btn people" data-csv ="people">People</button> &nbsp; <button type="button" class="btn btn-primary export_btn export_btn_active activities" data-csv ="activities">Activities</button> &nbsp; --> <button type="button" class="btn btn-primary export_btn notes " data-btn = "export_notes" data-csv ="notes">Notes</button>
               </div>
               <div class="col-md-4 padding-0 text-right btn_right">
                  <button type="button" class="btn export generate_csv"><span>CSV</span> &nbsp;<img src="{{url('assets/images/ex_btn.png')}}"></button> &nbsp; <!-- &nbsp; <button type="button" class="btn export export-active"><span>EXCEL</span> &nbsp; <img src="{{url('assets/images/ex_btn.png')}}"></button> -->
               </div>
            </div>
            <div class="export_bottom clearfix">
               <h3 class="heding">Exports available to download</h3>
               <p>Your data exports will appear here as files for download</p>
               <div class="col-md-8 padding-0 export-result @if(empty($exports)) hide @endif">
                  <p class="information"><i>Export started. Once it is ready, you can download the file from the list below.</i></p>
               </div>
               @if($user->user_type == 1 || $user->user_type == 2 )
               <div class="col-md-4 text-right padding-0 export-result @if(empty($exports)) hide @endif">
                  <button type="button" class="btn export remove-expired">Remove expired exports</button>
               </div>
               @endif
            </div>
         </div>
         <div class="data_list table-responsive export-result @if(empty($exports)) hide @endif ">
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th>Id</th>
                     <th>Exported</th>
                     <th>kind</th>
                     <th>format</th>
                     <th>items</th>
                     <th>size</th>
                     <th>available until</th>
                     <th>Requested by</th>
                     <th>download</th>
                  </tr>
               </thead>
               <tbody>
                  <?php $i = 1 ; ?>
                  @if(!empty($exports))
                  @foreach($exports as $key=>$export)
                  <tr>
                     <td align="center">{{$i}}</td>
                     <td>{{$export['createdDate'] or ''}}</td>
                     <td>{{$export['exportType'] or ''}}</td>
                     <td>{{$export['formatType'] or ''}}</td>
                     <td>{{$export['number_of_item'] or ''}}</td>
                     <td>{{$export['fileSize'] or '0'}} KB</td>
                     <td>{{$export['expiredDate']}} 11:59 PM</td>
                     <td>{{$export['user_name'] or ''}}</td>
                     @if($export['is_expired']) 
                     <td><a href="{{url('/download/').'/'.$export['id']}}">download</a></td>
                     @else 
                     <td class="text-center"><span class="expired_link" >Expired</span></td>
                     @endif
                  </tr>
                  <?php $i++; ?>
                  @endforeach
                  @endif
               </tbody>
            </table>
         </div>
      </div>
      <div class="loader">
         <img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading..">
      </div>
   </div>
</div>
@endsection
@section('footerscripts')  
<div class="modal error-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">Error !!</h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12"> 
                  <span class="error-msg" style="color:red"></span>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
{!! $validator or '' !!}
<script>
   $(document).ready(function(){
        $subpageUrl = "{{url('export/data')}}";
        $downloadUrl = "{{url('download')}}";
        $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });
   });
</script>
{!! HTML::script(asset('assets/js/export.js')) !!}
@endsection