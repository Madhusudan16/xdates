@extends('back')

@section('main')

<div class="row">
    @include('admin.partials.setting-menu')
                <div class="col-lg-9 col-md-9 col-sx-9 account-right">
                    <div class="white-bg">
                        <div class="row">
                            <div class="col-md-7 col-sm-7">
                                <div class="add_user">
                                    <span class="table_title">Plans</span>
                                    <button type="button" class="btn btn-success text-uppercase" data-toggle="modal" data-target="#add_plan">ADD NEW Plan<span class="plus">+</span></button>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-5">
                                <div class="user-status">
                                    <span class="status_label">Plan Status:</span>
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#activated" aria-controls="activated" role="tab" data-toggle="tab">Activated</a></li>
                                        <li role="presentation"><a href="#deactivated" aria-controls="deactivated" role="tab" data-toggle="tab">Deactivated</a></li>
                                    </ul>

                                </div>
                            </div>
                        </div>
                        <div class="tab-content"> 
                        
                        
                            <div role="tabpanel" class="tab-pane active" id="activated">
                                <div class="table-responsive">
                            <table class="table table-bordered" id="activate_tab">
                            <thead>
                         
                                <tr>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        Number Of Users
                                    </th>
                                    <th>
                                       Cost
                                    </th>
                                    <th>
                                       Refer Percentage
                                    <th class="no-sort">
                                      &nbsp;
                                    </th>

                                </tr>

                            </thead>
                             
                            <tbody>
                             
                                  @foreach ($plan_list as $item)
                                <tr>
                                    <td>
                                       {{$item->name}}
                                    </td>
                                    <td>
                                       {{$item->n_allowed_users}}
                                    </td>
                                    <td>
                                       {{$item->cost}}
                                    </td>
                                    <td>
                                       {{$item->refer_percentage}}
                                        
                                    </td>
                                    <td>
                                   
                                        <button type="button" class="btn btn-success text-uppercase" data-toggle="modal" data-target="#edit_plan" nameplan='{{$item->name}}' numberplan={{$item->n_allowed_users}} costplan={{$item->cost}} referplan={{$item->refer_percentage}} id='{{$item->id}}' >edit</button>
                                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#deactive_user" id={{$item->id}}>Deactivate</button>
                                      
                                      
                                    </td>

                                </tr>
                            
                                 @endforeach
                            </tbody>
                                   
                            
                        </table>

                        </div>
                        @if($plan_list->isEmpty())
                          <div class="row" id="act">
                             <div class="col-md-12">
                                <p class="error_mas">
                                    No Active plan found!.
                                </p>
                            </div>
                          </div>
                     
                        @endif 
                            </div>
                      
                           <div role="tabpanel" class="tab-pane" id="deactivated">
                                <div class="table-responsive">
                            <table class="table table-bordered" id="deactivated_tab">
                            <thead>
                                   <tr>
                                        <th>
                                            Name
                                        </th>
                                        <th>
                                            Number Of Users
                                        </th>
                                        <th>
                                           Cost
                                        </th>
                                        <th>
                                           Refer Percentage
                                        <th class="no-sort">
                                          &nbsp;
                                        </th>

                                    </tr>
                                
                            </thead>
                          
                            <tbody>
                             
                                  @foreach ($plan_lists as $item)
                                <tr>
                                    <td>
                                       {{$item->name}}
                                    </td>
                                    <td>
                                       {{$item->n_allowed_users}}
                                    </td>
                                    <td>
                                       {{$item->cost}}
                                    </td>
                                    <td>
                                       {{$item->refer_percentage}}
                                        
                                    </td>
                                    <td>

                                        <button type="button" class="btn btn-success text-uppercase" data-toggle="modal" data-target="#active_user" id={{$item->id}}>Activate</button>
                                        <button type="button" class="btn btn-primary text-uppercase" data-toggle="modal" data-target="#delete_plan" id={{$item->id}}>delete</button>
                                       
                                    </td>

                                </tr>
 @endforeach                  
                                </tbody>
                   
                        </table>

                        </div>
                          @if($plan_lists->isEmpty())
                        <div class="row" id="deact">
                            <div class="col-md-12">
                                <p class="error_mas">
                                    No deactive plan found!.
                                </p>
                            </div>
                        </div>
                     
                    @endif 
                        </div>
                    
                    </div>
                
                  
                    <div class="loader">
			                <img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading..">
			        </div>
                </div>
            </div>

        </div> 
 
    </section>
   
    <div class="modal fade" id="basic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Plan Create</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Plan Create successfully</p>
                            <div class="text-right">
                                 
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="" >Confirm</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   

<div class="modal fade" id="add_plan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Add Plan</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form name="addUserForm" id="addUserForm">
                                <fieldset class="form-group name">
                                    <label for="exampleInputName">Name</label>
                                    <span class="border-span"><input type="text" name='name'  class="form-control" id="exampleInputName" placeholder="Name"></span>
                                    <span class="error-msg" style="color:#a94442;" id="name_error"></span>
                                </fieldset>

                                <fieldset class="form-group number">
                                    <label for="exampleInputNumberOfUser">Number Of Users</label>
                                    <span class="border-span"><input type="text" name='number'  class="form-control" id="exampleInputNumberOfUser" placeholder="Number Of Users"></span>
                                    <span class="error-msg" style="color:#a94442;" id="number_error"></span>
                                </fieldset>

                                <fieldset class="form-group name cost">
                                    <label for="exampleInputCost">Cost</label>
                                    <span class="border-span"><input type="text" name='cost'  class="form-control" id="exampleInputCost" placeholder="cost"></span>
                                    <span class="error-msg" style="color:#a94442;" id="cost_error"></span>
                                </fieldset>

                                <fieldset class="form-group refer">
                                    <label for="exampleInputPercentage">Refer Percentage</label>
                                    <span class="border-span"><input type="text" name='refer'  class="form-control" id="exampleInputPercentage" placeholder="refer percentage"></span>
                                    <span class="error-msg" style="color:#a94442;" id="refer_error"></span> 
                                </fieldset>

                                <fieldset class="form-group">
                                    <button type="button" class="btn btn-success" id="add_new_user_btn" data-toggle="modal" data-target="" onclick="addPlan();" >Save</button>
                                    <button type="button" id="cancel" class="btn btn-danger" data-dismiss="modal">cancel</button>
                                </fieldset>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <div class="modal fade" id="edit_plan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Edit Plan</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form name="addUserForm" id="addUserForm">
                                <fieldset class="form-group name">
                                    <label for="exampleInputEmail1">Name</label>
                                    <span class="border-span"><input type="text" name='namen'  class="form-control" id="exampleInputName" placeholder="Name"></span>
                                    <span class="error-msg" style="color:#a94442;" id="edit_name_error"></span>
                                </fieldset>
                                   <fieldset class="form-group number">
                                    <label for="exampleInputEmail1">Number Of Users</label>
                                    <span class="border-span"><input type="text" name='numbern'  class="form-control" id="exampleInputName" placeholder="Number Of Users"></span>
                                    <span class="error-msg" style="color:#a94442;" id="edit_number_error"></span>
                                </fieldset>
                                    <fieldset class="form-group name cost">
                                    <label for="exampleInputEmail1">Cost</label>
                                    <span class="border-span"><input type="text" name='costn'  class="form-control" id="exampleInputName" placeholder="cost"></span>
                                    <span class="error-msg" style="color:#a94442;" id="edit_cost_error"></span>
                                </fieldset>
                                    <fieldset class="form-group refer">
                                    <label for="exampleInputEmail1">Refer Percentage</label>
                                    <span class="border-span"><input type="text" name='refern'  class="form-control" id="exampleInputName" placeholder="refer percentage"></span>
                                    <span class="error-msg" style="color:#a94442;" id="edit_refer_error"></span>
                                </fieldset>
                                <fieldset class="form-group">
                                   <input type=hidden name="planId" id="planId">
                                    <button type="button" class="btn btn-success" id="edit_new_user_btn" data-toggle="modal" data-target="" onclick="editPlan()">Save</button>
                                    <button type="button" id="cancel" class="btn btn-danger" data-dismiss="modal">cancel</button>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
       <div class="modal fade" id="active_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Are you sure wish to activate this Plan?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>This will active the plan and available to user.</p>
                            <div class="text-right">
                                 <input type=hidden name="useract" id="useract">
                                <button type="button" class="btn btn-success" id="user_activate_btn" data-toggle="modal" data-target="" onclick="userActive();">Confirm</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="modal fade" id="deactive_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Are you sure you wish to deactivate this Plan?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Plan will not be available to users!</p> 
                            <div class="text-right">
                                <input type=hidden name="userdeact" id="userdeact">
                                <button type="button" class="btn btn-success" id="user_deactivate_btn"  onclick="userDeactive();">Confirm</button>
                                <button type="button" class="btn btn-danger"  data-dismiss="modal">cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  
    <div class="modal fade" id="delete_plan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Are yor sure you wish to permanently delete this Plan?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p> The deactivated Plan will be permanently removed from the system. This action cannot be reverted.</p>
                            <div class="text-right">
                                <input type=hidden name="del" id="del">
                                <button type="button" id="user_delete_btn" class="btn btn-success"  onclick="deletePlan()">Confirm</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 {!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
 {{ HTML::script('assets/js/plan.js') }}
@endsection

    
