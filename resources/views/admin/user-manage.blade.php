@extends('back')

@section('main')


@inject('date', 'App\Commons\Date')

<div class="row">
    @include('admin.partials.setting-menu')
                <div class="col-lg-9 col-md-9 col-sx-9 account-right">
                    <div class="white-bg">
                        <div class="row">
                            <div class="col-md-7 col-sm-7">
                                <div class="add_user">

                                    <span class="table_title">Users</span>
                                    <button type="button" class="btn btn-success text-uppercase" data-toggle="modal" data-target="#add_user">ADD NEW user<span class="plus">+</span></button>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-5">
                                <div class="user-status">
                                    <span class="status_label">User Status:</span>
                                    <ul class="nav nav-tabs admin-tab" role="tablist">
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
                                        Email
                                    </th>
                                    <th>
                                       Last Login
                                    </th>
                                    <th>
                                       Account Type
                                    <th class="no-sort">
                                      &nbsp;
                                    </th>

                                </tr>

                            </thead>
                            <tbody>
                               @foreach ($user_list as $item)
                                 
                                <tr>
                                    <td>
                                    @if($user->id==$item->id)  

                                        {{$item->name}}(you)
                                       @else

                                         {{$item->name}}
                                    @endif
                                    </td>
                                    <td>
                                        {{$item->email}}
                                    </td>
                                    <td>
                                       {{$date::humanTiming(strtotime($item->last_login))}}
                                    </td>
                                    <td>
                                    @if($item->user_type =='1')
                                          Owner
                                    @elseif($item->user_type =='2')
                                          Super Admin
                                    @elseif($item->user_type =='3')  
                                          Admin
                                    @else
                                         User
                                    @endif
                                        
                                    </td>
                                    <td>
                                    @if(($user->id!=$item->id) && ($user->user_type == 1 || ($user->user_type == 2 && ($item->user_type == 3 || $item->user_type == 4))||($user->user_type == 3 && $item->user_type == 4)))
                                        <button type="button" class="btn btn-success text-uppercase" data-toggle="modal" data-target="#edit_user" username='{{$item->name}}' email={{$item->email}} acc_type={{$item->user_type}} id='{{$item->id}}'>edit</button>
                                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#deactive_user" id={{$item->id}}>Deactivate</button>
                                      @endif
                                      
                                    </td>

                                </tr>
                              @endforeach
                               
                            </tbody>
                        </table>

                        </div>
                        
                           @if($user_list->isEmpty())
                          <div class="row" id="act">
                             <div class="col-md-12">
                                <p class="error_mas">
                                    No Active User found!.
                                </p>
                            </div>
                          </div>
                     
                        @endif 
                            </div>
                            <div role="tabpanel" class="tab-pane" id="deactivated">
                                @if(!$userd_list->isEmpty())
                                <div class="table-responsive">
                            <table class="table table-bordered" id="deactivated_tab">
                            <thead>
                                <tr>
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
                                       Account Type
                                    </th>
                                    <th  class="no-sort">
                                      &nbsp;
                                    </th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userd_list as $item)
                                <tr>
                                    <td>
                                      @if($user->id==$item->id)  

                                        {{$item->name}}(you)
                                       @else

                                         {{$item->name}}
                                    @endif
                                    </td>
                                    <td>
                                        {{$item->email}}
                                    </td>
                                    <td>
                                        {{$date::humanTiming(strtotime($item->last_login))}}
                                    </td>
                                    <td>
                                    @if($item->user_type =='1')
                                          Owner
                                    @elseif($item->user_type =='2')
                                          Super Admin
                                    @elseif($item->user_type =='3')
                                          Admin   
                                    @else
                                          User
                                    @endif
                                        
                                    </td>
                                    <td>
                                      @if(($user->user_type == 1 || ($user->user_type == 2 && ($item->user_type == 3 || $item->user_type == 4))||($user->user_type == 3 && $item->user_type == 4)))
                                        <button type="button" class="btn btn-success text-uppercase" data-toggle="modal" data-target="#active_user" id={{$item->id}}>Activate</button>
                                        <button type="button" class="btn btn-primary text-uppercase" data-toggle="modal" data-target="#delete_user" id={{$item->id}}>delete</button>
                                        @endif
                                    </td>

                                </tr>
 @endforeach
                                </tbody>
                        </table>

                        </div>
                        @endif
                    @if($userd_list->isEmpty())
                        <div class="row tab-no-record">
                            <div class="col-md-12">
                                <p class="error_mas">
                                    There are no deactivated users on your account.
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
   
    <div class="modal fade" id="basic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">User Create</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>User Create successfully</p>
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

<div class="modal fade" id="add_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Add users</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form name="addUserForm" id="addUserForm">
                                <fieldset class="form-group name">
                                    <label for="exampleInputEmail1">Name</label>
                                    <span class="border-span"><input type="text" name='name'  class="form-control" id="exampleInputName" placeholder="Name"></span>
                                    <span class="error-msg" style="color:#a94442;display:none;" id="name_error"></span>
                                </fieldset>
                                <fieldset class="form-group email">
                                    <label for="exampleInputPassword1">Email</label>
                                    <span class="border-span"><input  type="email" name='email' class="form-control" id="exampleInputemail" placeholder="Email"></span>
                                    <span class="error-msg" style="color:#a94442;display:none;" id="email_error"></span>
                                </fieldset>
                                <fieldset class="form-group type">
                                    <label for="exampleSelect1">Account Type</label>
                                   <span class="border-span"> <select name="account_type" class="form-control" id="exampleSelect1111">
                                    <option value="">- Select One -</option>
                                      @if($user->user_type==1)
                                        <option value="2">Super Admin</option>
                                        <option value="3">Admin</option>
                                        <option value="4">User</option>
                                      @endif 
                                        @if($user->user_type==2)
                                        <option value="3">Admin</option>
                                        <option value="4">User</option>
                                      @endif 
                                        @if($user->user_type==3)
                                        
                                        <option value="4">User</option>
                                      @endif 
                                       </select></span>
                                </fieldset>
                                <fieldset class="form-group text-right button-right">
                                    <button type="button" class="btn btn-success" id="add_new_user_btn" data-toggle="modal" data-target="" onclick="addNewUser()">Save</button>
                                    <button type="button" id="cancel" class="btn btn-danger" data-dismiss="modal">cancel</button>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Edit User Details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="editUserForm" name="editUserForm">
                                <fieldset class="form-group name">
                                    <label for="exampleInputEmail1">Name</label>
                                    <span class="border-span"><input type="text" name='namen' class="form-control" id="exampleInputName"></span>
                                    <span class="error-msg" style="color:#a94442;" id="edit_name_error"></span>
                                </fieldset>
                                <fieldset class="form-group email">
                                    <label for="exampleInputPassword1">Email</label>
                                    <span class="border-span"><input  type="email" name='emailn' class="form-control" id="exampleInputemail" placeholder="clarksmith@xdates.net"></span>
                                     <span class="error-msg" style="color:#a94442;" id="edit_email_error"></span>
                                </fieldset>
                                <fieldset class="form-group type">
                                    <label for="exampleSelect1">Account Type</label>
                                    <span class="border-span"><select class="form-control" name='account_typen' id="exampleSelect1">
                                        <option value="">- Select One -</option>
                                        @if($user->user_type==1)
                                        <option value="2">Super Admin</option>
                                        <option value="3">Admin</option>
                                        <option value="4">User</option>
                                      @endif 
                                        @if($user->user_type==2)
                                        <option value="3">Admin</option>
                                        <option value="4">User</option>
                                      @endif 
                                        @if($user->user_type==3)
                                        
                                        <option value="4">User</option>
                                      @endif 
                                        </select></span>
                                </fieldset>
                                <fieldset class="form-group text-right button-right">
                                    <input type=hidden name="userId" id="userId">
                                    <button type="button" id="edit_new_user_btn" class="btn btn-success" onclick="userEdit();">Save</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                                </fieldset>
                            </form>
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
                    <h4 class="modal-title" id="myModalLabel">Are you sure you wish to deactivate this user?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>They will no longer have access to the system.</p> 
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
    <div class="modal fade" id="add_user_confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">This will exceed the maximum nuber of users on your plan</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Do you wish to upgrade plans or cancel the request to add anthor user?</p>
                            <div class="text-right">
                                <button type="button" class="btn btn-success">upgrade</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
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
                    <h4 class="modal-title" id="myModalLabel">Are you sure wish to activate this user?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>The deactivated user will once again have access to the system.</p>
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
    <div class="modal fade" id="upgrade_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Upgrade your account</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Activating this user will exceed the maximum number of users in you current plan.</p>
                            <p><i>Click continue to upgrade your plan or cancel to revert this action.</i></p>
                            <div class="text-right">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="">Confirm</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="permission" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Inadequate Permissions</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>You have inadequate permissions to make billing-related changes.</p>
                            <p><i>Only the account owner may make such changes.</i></p>
                            <div class="text-right">

                                <button type="button" class="btn btn-danger" data-dismiss="modal">I Understand</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Are yor sure you wish to permanently delete this user?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                           <p>The deactivated user account and all of associated data will be permanently removed from the system. This action cannot be reverted.</p>
                            <div class="text-right">
                                <input type=hidden name="del" id="del">
                                <button type="button" id="user_delete_btn" class="btn btn-success"  onclick="userDelete()">Confirm</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 {!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}

 {{ HTML::script('assets/js/admin.js') }}
@endsection

    
