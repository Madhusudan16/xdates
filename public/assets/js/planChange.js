var currentPgUrl = window.location.href;
// Ajax Call for Create users

function reloadUsersData(){

  if(!$('.loader').is(':visible')){

    $('.loader').show();
  }
  $.get(currentPgUrl, function(data) {
      var activeTab = $(data).find('#refresh');
      var activeTab1 = $(data).find('#refer');
    $('#refresh').html(activeTab.html());
    $('#refer').html(activeTab1.html());
     $('.loader').hide();
  }); 
}

function addNewPlan(id) {
    $('.loader').show();
    $.ajax({
        method: 'POST',
        url: 'userPlan-change',
        data: {
            plan: id
           
        },
     success: function (data) {        
          reloadUsersData(); 
          $('.loader').hide();
     },
     error: function(data){
        var errors = data.responseJSON;
       if(errors.error=='number of user exceed.'){
			   $('#many_user').modal('show');
        }
        $('.loader').hide();
       }
    });
}

function upAndDownPlan(planId){
  $('.loader').show();
	$.ajax({
        method: 'POST',
        url: site_url+'/planbill/upDownPlan',
        data: {plan: planId },
        success: function (data) {        
          //reloadUsersData();
          //console.log(data); 
          window.location = site_url+'/planbill/change-plan';
          $('.loader').hide();
     	},
	    error: function(data){
        $('.loader').hide();
        var resData = data.responseJSON;
	      
	      if(resData.error=='n_user_exceed'){
			   $('#many_user').modal('show');
	      }else if(resData.error=='no_plan_found'){
	      	$('#otherErrorDesc').html(resData.msg);
	      	$('#otherError').modal('show');
	      }else if(resData.error == 'error_occured'){
	      	$('#otherErrorDesc').html(resData.msg);
	      	$('#otherError').modal('show');
	      }else{
	      	$("#balance").modal('show');
	      }
	    }
    });
}
function passRefer() {
	window.location.href = site_url+"/user-manage";
}

  function apply_coupon() {
      var coupon = $("#coupon").val();
      $(".loader").show();
      $(".coupon_apply").val('Applying')
      $.post(site_url+'/planbill/apply_coupon',{coupon:coupon},function(responseData){
          $(".coupon_apply").val('Applied');
          $(".coupon_success").show();
          $(".coupon_error").hide();
          $(".coupon_success").text(responseData.msg);
          $(".loader").hide();
      }).fail(function(responseData) {
          var message = $.parseJSON(responseData.responseText);
          $(".coupon_success").hide();
          $(".coupon_error").show();
          $(".coupon_error").text(message.msg);
          $(".coupon_apply").val('Apply');
          $(".loader").hide();
      });
  }

$(document).ready(function(){
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
	 $('body').on("click", ".upgrade", function(event) {
        var id =  $(event.target).attr('id');
        $(".upgrade-confirm").attr('id',id);
        $("#upgrad_plan").modal('show');
    });
    $('body').on("click", ".downgrade", function(event) {
        var id =  $(event.target).attr('id');
        $(".downgrade-confirm").attr('id',id);
        $("#downgrade").modal('show');
    });
    
    $(".upgrade-confirm").click(function(event){
          $("#upgrad_plan").modal('hide');
          var id = $(event.target).attr('id');
          upAndDownPlan(id);
    });
    
    $(".downgrade-confirm").click(function(){
        $("#downgrade").modal('hide');

    });
    $(".balance-update").click(function(){
        window.location.href = site_url + "/planbill/card";
    });

    $(".select-plan").click(function(){
      $id = $(this).attr('id');
      upAndDownPlan($id);
    });

    $( "#couponForm" ).validate({
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
            coupon: "required",
          }, 
          messages: {
            coupon: "Please enter coupon code.",
          }
    });
    $(".coupon_apply").click(function(){
        if($("#couponForm").valid()) {
            apply_coupon();
        } 
    });

    $(".coupon_used").click(function(){
        var element = $(this);
        $(".coupon_exist").addClass($(this).attr('myclass'));
        $(".coupon_exist").attr('id',$(this).attr('id'));
        $("#coupon_terminate").modal('show');

    });

    $(".find_pay_amount").click(function(){

        var plan_amount = $(this).attr('plan_amount');
        console.log(plan_amount);
        var one_days_charge = parseFloat(plan_amount)/30;
        var pay_amount = (one_days_charge * remining_days).toFixed(2);
        $(".plan_amount").text("$"+plan_amount);
        $(".pay_amount").text("$"+pay_amount);
    });
    if(remining_days <= 0) {
        console.log(remining_days);
        $(".find_pay_amount").prop('disabled',true);
        $(".find_pay_amount").css('background','#5c5c5c');
    }
});