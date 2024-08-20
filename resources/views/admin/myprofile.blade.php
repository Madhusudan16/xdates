@extends('back')

@section('main')

<meta name="_token" content="{!! csrf_token() !!}"/>
<div class="row">
	@include('admin.partials.setting-menu')



	<div class="col-md-9 account-right">
		@if($curModAccess['view'])
	    <div class="right-profile my-profile">
	        <div class="white-space"></div>
	        <div class="row">
	            <div class="col-md-6 col-sm-6 padding-right-none">
	                <div class="fild-sec">
	                    <form class="form-horizontal" method="post" role="form" enctype="multipart/form-data">

	                        <div class="form-group">
	                            <label class="control-label col-sm-3" for="pwd">First name</label>
	                            <div class="col-sm-9">
	                              <span class="border-span"><input type="text" class="form-control" id="f_name" name="first_name" value="{{$user->first_name or ''}}"></span>
	                                <span class="icon"><img src="{{asset('assets/images/note_icon.png')}}"></span>
	                            </div>
	                          </div>
	                        <div class="form-group">
	                            <label class="control-label col-sm-3" for="pwd">Last name</label>
	                            <div class="col-sm-9">
	                              <span class="border-span"><input type="text" class="form-control" id="l_name" name="last_name" value="{{$user->last_name or ''}}"></span>
	                                 <span class="icon"><img src="{{asset('assets/images/note_icon.png')}}"></span>
	                            </div>
	                          </div>
	                          <div class="form-group">
	                            <label class="control-label col-sm-3" for="email">Email</label>
	                            <div class="col-sm-9">
	                              <span class="border-span"><input type="email" class="form-control" id="email" name="email" value={{$user->email or ''}}></span>
	                                 <span class="icon"><img src="{{asset('assets/images/note_icon.png')}}"></span>
	                            </div>
	                          </div>
	                        <div class="form-group">
	                            <label class="control-label col-sm-3" for="email">Time Zone</label>
	                            <div class="col-sm-9">
	                                <span class="border-span">
	                                    <select class="selectpicker" id="timeZone" name="choosed_timezone">
	                                    	<option hidden>select timezone</option>
	                                   		@foreach ($timezones as $timezone)
											    <option value={{$timezone->timezone_value}} @if($user->choosed_timezone == $timezone->timezone_value) {{"selected"}}  @endif>{{ $timezone->timezone_name }}</option>
											@endforeach
	                                    </select>
	                                </span>
	                            </div>
	                        </div>
	                        <div class="form-group text-right">
	                            <button type="button" class="btn btn-success btn-change-psw">Change Password</button>
	                        </div>
	                    </form>
	                </div>
	            </div>

	             {{ Form::open(array('class'=>'form-horizontal imageUploadForm',  'files' => true)) }}
	            <div class="col-md-6 col-sm-6 padding-left-none img_sec">
	                <div class="pic-sec">
	                     <div class="form-group heding">
	                         <label class="control-label heding-label" for="">Upload avatar</label>
	                    </div>
	                    <div class="images_sec clearfix">

	                        <div class="profile-pic">
	                            @if(!empty($user->profile_image))
	                           	 	<div class="images"><span><img src="{{asset(config('constants.FILEUPLOAD').$user->profile_image)}}" id="profileImage" width="85" height="85"></span></div>
	                           	@else
	                           		<div class="images"><span><img src="{{asset(config('constants.FILEUPLOAD').''.config('constants.DEFAULT_PROFILE')) }}" id="profileImage" width="85" height="85"></span></div>
	                           	@endif

	                        </div>


	                        <input type="file" name="profile_image" id="file">
	                    
	                    </div>
	                </div>
	                {{ Form::close() }}
	            </div>


	        </div>

	    </div>
	    <div class="loader">
                <img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading..">
        </div>
        @endif
	</div>
</div>

@endsection


@section('footerscripts')
<script type="text/javascript">
		$(document).ready(function(){
			$.ajaxSetup({
			   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
			});

			$('#filterdropdown').click(function() {
           	 $('.filtertab-group').toggle();
      		  });
			$("#f_name,#l_name,#email").change(function(){

				var fieldName = $(this).attr('name');
				var fieldvalue= $(this).val();
				var data = $.makeArray();
				setValues(fieldName,fieldvalue);

			});
			// user changed timezone
			$("#timeZone").change(function(){

				var fieldName = $(this).attr('name');
				var fieldvalue= $(this).val();
				setValues(fieldName,fieldvalue);
			});
			$("#file").change(function(){

				var fieldvalue = {};
				var fieldName = $(this).attr('name');
				var file_data = new FormData($(".imageUploadForm")[0]);
				updateData(file_data,true);
			});
			$(".btn-change-psw").click(function(){
				 window.location.href = 'change-password';
			});
		});
		/*  set input values  */
		function setValues(fieldName,fieldvalue){
			$('.loader').show();
			   var data =[]; // create data array for store input data
			   var obj = {}; // create obj object for set fieldName from variable
			   obj[fieldName] = fieldvalue;  // set value pair of key = value
			   data.push(obj);
			   updateData(data,false);
		}
		function updateData(data,isFile){ // this function called updateProfile function in MyprofileController class
				$('.loader').show();

				if(isFile){
					$.ajax({
					    url : "/admin/updateProfile" ,
					    type: 'POST',
						processData: false,
					    contentType: false,
					    cache:false,
					    data: data
					}).done(function(response) {
						if(response != "" && response != null ){
					    	$("#profileImage").attr('src',response);
					    	$(".profile-image").attr('src',response);
					    }
					    setTimeout(function(){
					    		$(".loader").hide();
					    },2000);
					});

				}
				else{
					$.post("/admin/updateProfile",{'data':data}, function(response){
							if(response != "" && response != null ){
								if(response =='unauthorized'){
									window.reload();
								}
				      			var responseData = $.parseJSON(response);

				      			if(responseData.field_name == "choosed_timezone"){
				      				$("#timeZone").val(responseData.field_value).prop('selected', true);
				      			}
				      			else{
					      			$("input[name = "+responseData.field_name+"]").val(responseData.field_value);
					      		}
				      		}
					    if($('#f_name').val() != '' && $('#l_name').val() != '')  {
						    var full_name = $('#f_name').val()+' '+ $('#l_name').val();
							$('.usr-name').html(full_name);
						}
						$(".loader").hide();
					});
				}
		}
	</script>
{!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}

@endsection
