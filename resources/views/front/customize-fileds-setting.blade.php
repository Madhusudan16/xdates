@extends('front')

@section('main')
<meta name="_token" content="{!! csrf_token() !!}"/>
<div class="row">
    @include('front.partials.setting-menu')
     <div class="col-lg-9 col-md-9 col-sx-9 account-right">
                    <div class="white-bg">
                        <div class="row column-3-layout">
                            <div class="col-md-4 col-sm-6 pad-l-0 pad-r-0 column border-right">
                                <div class="column-header clearfix">
                                    <span class="table_title">Lines</span>
                                    <!-- <button data-target="#addNewUser" data-toggle="modal" class="btn btn-success btn-add-new" type="button" onclick="addNewField('lines');">
                                        Add New <span class="plus">+</span>
                                    </button> -->
                                </div>
                                <div class="column-body">
                                    <ul class="list-unstyled" id="lines">
                                          @foreach ($lines_policies as $item)
                                               <li><fieldset class='form-group'><span class='border-span'><input type='text' name='lines[]' value='{{$item->name}}' class='form-control'   id='exampleInputEmail1' data-type="lines" readonly=""  placeholder=''></span> <!-- <a href='#' class="remove_line" id={{$item->id}} data-toggle='modal'  data-target='#delete_line'><img src='{{asset('assets/images/close_line.png')}}'></a> --></fieldset></li>
                                          @endforeach

                                    </ul>
                                </div>
                                <div class="column-header clearfix">
                                    <span class="table_title">Industry</span>
                                    <button data-target="#addNewUser" data-toggle="modal" class="btn btn-success btn-add-new" type="button" onclick="addNewField('industry');">
                                        Add New <span class="plus">+</span>
                                    </button>
                                </div>
                                <div class="column-body">
                                    <ul class="list-unstyled" id="industry">
                                          @foreach ($industry_policies as $item)
                                               <li><fieldset class='form-group'><span class='border-span'><input type='text' name='lines[]' value='{{$item->name}}' class='form-control'   id='exampleInputEmail1' data-type="industry" @if($item->is_permanent == 0) onchange='updateIndustry({{ $item->id }}, this)'; @else readonly @endif placeholder=''></span> @if($item->is_permanent == 0) 
                                               	<a href='#' class="remove_line" id={{$item->id}} data-toggle='modal' data-target='#delete_line'><img src='{{asset('assets/images/close_line.png')}}'></a>
                       @endif                        	
                                               	</fieldset></li>
                                          @endforeach

                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6 pad-l-0 pad-r-0 column no-border">
                               <div class="column-header clearfix">
                                    <span class="table_title">Personal Policy</span>
                                    <button data-target="#addNewUser" data-toggle="modal" class="btn btn-success btn-add-new" type="button" onclick="addNewField('personal');">
                                        Add New <span class="plus">+</span>
                                    </button>
                                </div>
                                <div class="column-body">
                                    <ul class="list-unstyled" id="personal">
                                             @foreach ($personal_policies as $item)
                                               <li><fieldset class='form-group'><span class='border-span'><input type='text' name='lines[]' value='{{$item->name}}' class='form-control'   id='exampleInputEmail1' data-type="personal" onchange='updatePersonal({{ $item->id }}, this)'; placeholder=''></span> <a href='#' class="remove_line" id={{$item->id}}  data-toggle='modal' data-target='#delete_line'><img src='{{asset('assets/images/close_line.png')}}'></a></fieldset></li>
                                          @endforeach


                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 pad-l-0 pad-r-0 column">
                                <div class="column-header clearfix">
                                    <span class="table_title">Commercial Policy</span>
                                    <button data-target="#addNewUser" data-toggle="modal" class="btn btn-success btn-add-new" type="button" onclick="addNewField('commercial');">
                                        Add New <span class="plus">+</span>
                                    </button>
                                </div>
                                <div class="column-body">
                                    <ul class="list-unstyled" id="commercial">
                                          @foreach ($commercial_policies as $item)
                                               <li><fieldset class='form-group'><span class='border-span'><input type='text' name='lines[]' value='{{$item->name}}' class='form-control'   id='exampleInputEmail1'  data-type="commercial"  onchange='updateCommercial({{ $item->id }}, this)'; placeholder=''></span> <a href='#' class="remove_line" id={{$item->id}} data-toggle='modal' data-target='#delete_line'><img src='{{asset('assets/images/close_line.png')}}'></a></fieldset></li>
                                          @endforeach

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="loader">
			                <img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading..">
			        </div>
                </div>
            </div>

        </div>

</div>
<div class="modal fade" id="delete_line" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Are you sure you wish to delete the option?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>The deleted option will be permanently remove from the system and may affect numerous X-Dates in your account. This action cannot be reverted.</p>
                            <div class="text-right">
                <form>
                <input type="hidden" name="policyId" id="policyId">
                                <button type="button" class="btn btn-success" onclick="confirmDelete();">Confirm</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ HTML::script('assets/js/customize.js') }}
@endsection
