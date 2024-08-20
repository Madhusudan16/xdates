
@extends('back')

@section('main')

<div class="row">
    @include('admin.partials.setting-menu')
                <div class="col-lg-9 col-md-9 col-sx-9 account-right">
                @if($curModAccess['view'])
                    <div class="white-bg">
                        <div class="row">
                            <div class="col-md-7 col-sm-7"> 
                                <div class="add_user">
                                    <span class="table_title">Coupon</span>
                                    <button type="button" class="btn btn-success text-uppercase" data-toggle="modal" data-target="#add_coupon">ADD NEW Coupon<span class="plus">+</span></button>
                                </div>
                            </div>
                            
                            <div class="col-md-5 col-sm-5">
                                <div class="user-status">
                                    <span class="status_label" >Coupon Status:</span>
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
                            <table class="table table-bordered coupon" id="activate_tab">
                            <thead>
                                <tr>
                                    <th>
                                        Type
                                    </th>
                                    <th>
                                        Coupon code
                                    </th>
                                    <th>
                                        Expiry Date
                                    </th>
                                    <th>
                                       Days or Discount
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th class="no-sort">
                                      &nbsp;
                                    </th>

                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($coupon_list as $item)
                                <tr>
                                       <td>
                                   @if($item->user_type==1)
                                        Current User
                                   @else
                                       New User
                                   @endif
                                    </td>
                                    <td>
                                        {{$item->coupon}}
                                    </td>
                                    <td>
                                        {{$item->coupon_expire}}
                                    </td>
                                    <td>
                                        @if($item->coupan_day==0)

                                       {{$item->coupon_percent}}%
                                       
                                       @else
                                        {{$item->coupan_day}} Days
                                       @endif
                                    </td>
                                    <td>
                                        {{$status[$item->status]}}
                                    </td>
                                    <td>
                                        @if($item->status == 3 || $item->status ==4) 
                                             <?php $class ="disable_btn";  ?>
                                        @else 
                                               <?php $class=""; ?>
                                        @endif
                                        <button type="button"  class="btn btn-success text-uppercase {{$class}}" data-toggle="modal" data-target="#edit_coupon" number_of_terms="{{$item->no_of_time}}" user_type={{$item->user_type}} coupon={{$item->coupon}} expire="{{date('m/d/Y',strtotime($item->coupon_expire))}}" day={{$item->coupan_day}} percent={{$item->coupon_percent}} id={{$item->id}} status={{ $item->status }} @if($item->status == 3 || $item->status == 4 ) {{'disabled'}} @endif>edit</button>
                                        <button type="button" class="btn btn-primary {{$class}} " data-toggle="modal" data-target="#deactive_coupon" @if($item->status == 3 || $item->status == 4 ) {{'disabled'}} @endif id={{$item->id}}>Deactivate</button>
                                    </td>

                                </tr>
                              
                              @endforeach
                            </tbody>
                        </table>

                        </div>
                            @if($coupon_list->isEmpty())
                          <div class="row" id="act">
                             <div class="col-md-12">
                                <p class="error_mas">
                                    No Aactive Coupon found!.
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
                                        Type
                                    </th>
                                    <th>
                                        Coupon code
                                    </th>
                                    <th>
                                        Expiry Date
                                    </th>
                                    <th>
                                       Days or Discount
                                    </th>
                                    <th>
                                      &nbsp;
                                    </th>

                                </tr>
                            </thead>
                            <tbody>
                             @foreach ($coupon_listd as $item)
                                <tr>
                                  <td>
                                   @if($item->type==1)
                                        Current User
                                   @else
                                       New User
                                   @endif
                                    </td>
                                    <td>
                                        {{$item->coupon}}
                                    </td>
                                    <td>
                                      
                                        {{$item->coupon_expire}}
                                    </td>
                                    <td>
                                        @if($item->coupan_day==0)
                                           {{$item->coupon_percent}}%
                                        
                                       @else
                                        {{$item->coupan_day}} Days
                                       @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success text-uppercase" data-toggle="modal" data-target="#active_coupon" id={{$item->id}}>Activate</button>
                                        <button type="button" class="btn btn-primary text-uppercase" data-toggle="modal" data-target="#delete_coupon" id={{$item->id}}>delete</button>
                                    </td>

                                </tr>
                                @endforeach
                                </tbody>
                        </table>

                        </div>
                            @if($coupon_listd->isEmpty())
                          <div class="row" id="act">
                             <div class="col-md-12">
                                <p class="error_mas"> 
                                    No Deactive Coupon found!.
                                </p>
                            </div>
                          </div>
                     
                        @endif 
                            </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <p class="error_mas" style="display:none;">
                                    There are no deactivated coupon on your account. 
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                  @endif
            </div>

        </div>
    </section> 
        <div class="modal fade" id="basic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title success_message coupon_message" id="myModalLabel">Coupon code created successfully.</h4>
                </div>
                <!-- <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="success_message">Coupon code created successfully.</p>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>   
    <div class="modal fade" id="add_coupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Add Coupon</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form name="randform" id="addCouponForm">
                                <div class="form-group  col-md-6 col-sm-6">
                                    <label for="exampleSelect1">Type</label>
                                   <span class="border-span"> <select class="form-control" id="exampleSelect1" name="user_type"  onchange="getval(this);">
                                        <option value="1">Current User</option>
                                        <option value="2">New User</option>
                                       </select></span>
                                </div>
                                
                                
                                <div class="filtertab-content-fromto col-md-6 col-sm-6">
                                
                                    <label for="">Expiry Date</label>
                                    <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                                        <input class="form-control" size="16" type="text" value="" id="signUpfrom" name="expire" readonly>
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                           
                                    </div>
                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group name col-md-6 col-sm-6">
                                    <label for="exampleInputEmail1">Coupon Code </label>
                                    <span class="border-span"><input type="text" name="randomfield" class="form-control" id="exampleInputName" placeholder=""></span>
                                     <span class="error-msg" style="color:#a94442;" id="randomfield_error"></span>  
                                   
                                </div>

                                <div class="filtertab-content-fromto filtertab-content-fromto-btn col-md-5 col-sm-5" >
                                    <label></label>
                                    <div>  <input type="button" value="Create Coupon" onClick="randomString();" class="btn btn-info"></div>&nbsp;
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group name col-md-6 col-sm-6" id="Trial_cop" style="display: none;">
                                    <label for="exampleInputEmail1">Free Trial Days(For New Users) </label>
                                    <span class="border-span"><input type="text" name="trial" class="form-control" id="exampleInputName1" placeholder=""></span>
                                   <span class="error-msg" style="color:#a94442;" id="trial_error"></span> 
                                </div>         
                                <div class="form-group name col-md-6 col-sm-6" id="discount_cop">
                                    <label for="exampleInputEmail1">Discount(For Existing Users)</label>
                                    <span class="border-span"><input type="text" name="discount" class="form-control" id="exampleInputName2" placeholder=""></span>
                                <span class="error-msg" style="color:#a94442;" id="discount_error"></span> 
                                </div> 
                                <div class="form-group  col-md-6 col-sm-6 status">
                                    <label for="exampleSelect1">status</label>
                                   <span class="border-span"> <select class="form-control" id="exampleSelect1" name="status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                       </select></span>
                                </div>

                                <div class="form-group name col-md-6 col-sm-6" id="numberOfUser">
                                    <label for="no_of_terms">Number of terms</label>
                                    <span class="border-span"><input type="text" name="no_of_terms" class="form-control" id="no_of_terms" placeholder=""></span>
                                <span class="error-msg" style="color:#a94442;" id="no_of_terms_error"></span> 
                                </div> 


                                <div class="form-group col-md-6 col-sm-6 coupon_button pull-right text-right">
                                    <button type="button" class="btn btn-success" data-toggle="modal" id="add_new_user_btn" data-target="" onClick="addCoupon();">Save</button>
                                    <button type="button" class="btn btn-danger" id="cancel"  data-dismiss="modal">cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_coupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Edit Coupons</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form name="randform1"  id="editCouponForm">

                                <div class="form-group  col-md-6 col-sm-6">
                                    <label for="user_type1">Type</label>
                                   <span class="border-span"> <select class="form-control" id="user_type1" name="user_type1"  onchange="getvalEdit(this);">
                                        <option value="1">Current User</option>
                                        <option value="2">New User</option>
                                       </select></span>
                                </div>

                                <div class="filtertab-content-fromto col-md-6 col-sm-6">
                                
                                    <label for="">Expiry Date</label>
                                    <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                                        <input class="form-control" size="16" type="text" value="" id="signUpfrom" name="expire1" readonly>
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                           
                                    </div>
                                    
                                </div>

                                <div class="clearfix"></div>
                                <div class="form-group name col-md-6 col-sm-6">
                                    <label for="exampleInputName">Coupon Code </label>
                                    <span class="border-span"><input type="text" name="randomfield1" class="form-control" id="exampleInputName" placeholder=""></span>
                                     <span class="error-msg" style="color:#a94442;" id="edit_randomfield_error"></span>  
                                   
                                </div>

                                

                                <div class="filtertab-content-fromto col-md-5 col-sm-5" >
                                    <label></label>
                                    <div>  <input type="button" value="Create Coupon" onClick="randomStringEdit();" class="btn btn-info"></div>&nbsp;
                                </div>

                                <div class="form-group name col-md-6 col-sm-6" id="Trial_cop1" >
                                    <label for="exampleInputEmail1">Free Trial Days(For New Users) </label>
                                    <span class="border-span"><input type="text" name="trial1" class="form-control" id="exampleInputName1" placeholder=""></span>
                                   <span class="error-msg" style="color:#a94442;" id="edit_trial_error"></span> 
                                </div> 

                                <div class="form-group name col-md-6 col-sm-6" id="discount_cop1">
                                    <label for="exampleInputEmail1">Discount(For Existing Users)</label>
                                    <span class="border-span"><input type="text" name="discount1" class="form-control discount1" id="exampleInputName2" placeholder=""></span>
                                <span class="error-msg" style="color:#a94442;" id="edit_discount_error"></span> 
                                </div>

                                <div class="form-group  col-md-6 col-sm-6">
                                    <label for="exampleSelect1">status</label>
                                   <span class="border-span"> <select class="form-control" id="exampleSelect1" name="status1">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                       </select></span>
                                </div>

                                <div class="form-group name col-md-6 col-sm-6" id="numberOfUserEdit">
                                    <label for="edit_no_of_terms">Number of terms</label>
                                    <span class="border-span"><input type="text" name="edit_no_of_terms" class="form-control" id="edit_no_of_terms" placeholder=""></span>
                                <span class="error-msg" style="color:#a94442;" id="edit_no_of_terms_error"></span> 
                                </div> 
 
                                
                                <div class="form-group col-md-6 col-sm-6 pull-right text-right">
                                    <input type="hidden" name="userId" id="userId">
                                    <button type="button" class="btn btn-success" data-toggle="modal" id="edit_new_user_btn" data-target="" onClick="couponEdit();">Save</button>
                                    <button type="button" class="btn btn-danger" id="cancel"  data-dismiss="modal">cancel</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deactive_coupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Are you sure you wish to deactivate this Coupon?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>They will no longer have access to the system.</p>
                            <div class="text-right">
                              <input type=hidden name="coupondeact" id="coupondeact">
                                <button type="button" class="btn btn-success"  id="user_deactivate_btn"  onclick="couponDeactive();">Confirm</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="active_coupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Are you sure wish to activate this coupon?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>The deactivated coupon will once again have access to the system.</p>
                            <div class="text-right">
                             <input type=hidden name="couponact" id="couponact">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#upgrade_user"  id="user_deactivate_btn"  onclick="couponActive();">Confirm</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="delete_coupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Are you sure you want to delete this coupon?"</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p></p>
                            <div class="text-right">
                             <input type=hidden name="del" id="del">
                                <button type="button" class="btn btn-success" onclick="couponDelete()">Confirm</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

 {!! HTML::script(asset('vendor/jsvalidation/js/jsvalidation.js')) !!}
 {!! HTML::script(asset('assets/js/bootstrap-datetimepicker.min.js')) !!}  
 {{ HTML::script('assets/js/coupon.js') }} 
 <script>
    $(document).ready(function(){
        $(".discount1").focus(function(){
            $("#edit_discount_error").text('');
        });
    })
 </script>
@endsection
