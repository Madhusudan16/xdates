var currentPgUrl = window.location.href; 

function reloadUsersData(){
  if(!$('.loader').is(':visible')){
    $('.loader').show();
  }
  $.get(currentPgUrl, function(data) { 
     var activeTab = $(data).find('#activated');
     var deactiveTab = $(data).find('#deactivated');
     $('#activated').html(activeTab.html());
     $('#deactivated').html(deactiveTab.html());
      $("#activate_tab").tablesorter();
     $("#deactivated_tab").tablesorter();
     $('.loader').hide();
  }); 
}

//Genrate coupon code 
function randomString() {
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    var string_length = 8;
    var randomstring = '';
    for (var i=0; i<string_length; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        randomstring += chars.substring(rnum,rnum+1);
    }
    document.randform.randomfield.value = randomstring;

}
//Genrate coupon code for edit 
function randomStringEdit() {
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    var string_length = 8;
    var randomstring = '';
    for (var i=0; i<string_length; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        randomstring += chars.substring(rnum,rnum+1);
    }
    document.randform1.randomfield1.value = randomstring;

}
var currentPgUrl = window.location.href;
  $.get(currentPgUrl, function(data) { 
     var activeTab = $(data).find('#activated');
     var deactiveTab = $(data).find('#deactivated');
     $('#activated').html(activeTab.html());
     $('#deactivated').html(deactiveTab.html());
     $('.loader').hide();
  }); 

// Refresh bootstap modal when coupon added
$("#add_coupon").on("hidden.bs.modal", function(){
   $("#trial_error").empty();
   $("#randomfield_error").empty();
   $("#discount_error").empty();
   $("#no_of_terms_error").empty();
   $("input[name=trial]").val("");
   $("input[name=discount]").val("");
   $("input[name=randomfield]").val("");
   $("#no_of_terms").val("");
   $("#addCouponForm").validate().resetForm();
   
});

// Refresh bootstap modal when coupon edit

$("#edit_coupon").on("hidden.bs.modal", function(){
   $("#edit_trial_error").empty();
   $("#edit_randomfield_error").empty();
   $("#edit_discount_error").empty();
   $("#edit_no_of_terms_error").empty();
   $("input[name=trial]").val("");
   $("input[name=discount]").val("");
   $("input[name=randomfield]").val("");
   $("#edit_no_of_terms").val("");
   //$( "#editCouponForm" ).validate()
   $("#editCouponForm").validate().resetForm();
   
});
$('.form_date').datetimepicker({
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: true,
            startDate: '-0d',
            changeMonth: true
         
        });    

// Add input type according to user type
function getval(sel) {
 if(sel.value==1){
   $("#numberOfUser").show();
   $("#Trial_cop").hide();
   $("#discount_cop").show();
 }else{
   $("#numberOfUser").hide();
   $("#Trial_cop").show();
   $("#discount_cop").hide();
 }        
}

function getvalEdit(sel) {
 if(sel.value==1){
   $("#numberOfUserEdit").show();
   $("#Trial_cop1").hide();
   $("#discount_cop1").show();
 }else{
   $("#numberOfUserEdit").hide();
   $("#Trial_cop1").show();
   $("#discount_cop1").hide();
 }        
}

// Ajax Call for add coupon 
function addCoupon() {
  if($( "#addCouponForm" ).valid()){
    $('#add_new_user_btn').addClass('disabled').text('Saving...');
    $.ajax({ 
        method: 'POST',
        url: 'coupon-create',
        data: {
          
            discount: $("input[name=discount]").val(),
            expire: $("input[name=expire]").val(),
            trial: $("input[name=trial]").val(),
            coupon: $("input[name=randomfield]").val(),
            status: $("select[name=status]").val(),
            user_type: $("select[name=user_type]").val(),
            no_of_terms: $("#no_of_terms").val(),
        },
        
        success: function (data) { 
          //$('#' + id).parent().parent().remove();
          $('.loader').hide();       
          $("#trial_error").empty();
          $("#discount_error").empty();
          $("#randomfield_error").empty();
          $("#no_of_terms_error").empty();
          $('#add_coupon').modal('hide');
          $(".success_message").text('');
          $(".success_message").text('Coupon code created successfully.');
          $('#basic').modal('show');
           $('#add_new_user_btn').removeClass('disabled').text('Save');
          reloadUsersData();
          setInterval(function(){$('#basic').modal('hide')},3000); 
        },

         beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
        error: function (jqXHR, exception) {
           $('#add_new_user_btn').removeClass('disabled').text('Save');
            $('#addCouponForm .error-msg').show();
            $("#edit_no_of_terms_error").empty();
           $("#trial_error").empty();
           $("#discount_error").empty()
           $("#randomfield_error").empty()
          if (jqXHR.status == 422) {
               var parsed = JSON.parse(jqXHR.responseText);
              if (typeof parsed.trial != 'undefined') {
                $("#trial_error").append(parsed.trial[0]);
              }
              if (typeof parsed.discount != 'undefined') {
                $("#discount_error").append(parsed.discount[0]);
              }  
              if (typeof parsed.coupon != 'undefined') {
                $("#randomfield_error").append(parsed.coupon[0]);
              }
          }
        },
   
    });
 }else{
     $('#addCouponForm .error-msg').hide();
    }   
}

// Ajax Call for Edit coupon 
function couponEdit() { 
  if($( "#editCouponForm" ).valid()){
    $('#edit_new_user_btn').addClass('disabled').text('Saving...');  
    var id = $('#userId').val();
    $.ajax({ 
        method: 'put',
        url: 'coupon-edit',
        data: {
            value: id,
            discount: $("input[name=discount1]").val(), 
            expire: $("input[name=expire1]").val(),
            trial: $("input[name=trial1]").val(),
            coupon: $("input[name=randomfield1]").val(),
            status: $("select[name=status1]").val(),
            user_type: $("select[name=user_type1]").val(),
            no_of_terms: $("#edit_no_of_terms").val(),
        },
        
        success: function (data) { 
          //$('#' + id).parent().parent().remove();
          $('.loader').hide();       
          $("#trial_error").empty();
          $("#discount_error").empty()
          $("#randomfield_error").empty()
          $('#add_coupon').modal('hide');
          $(".success_message").text('');
          $(".success_message").text('Coupon code updated successfully.');
          $('#basic').modal('show');
          $('#edit_new_user_btn').removeClass('disabled').text('Save');
          $('#edit_coupon').modal('hide');
          reloadUsersData();
          setInterval(function(){$('#basic').modal('hide')},3000); 
          
        },

         beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
        error: function (jqXHR, exception) {
           $('#edit_new_user_btn').removeClass('disabled').text('Save');
            $('#editCouponForm .error-msg').show();
           $("#edit_trial_error").empty();
           $("#edit_discount_error").empty()
           $("#edit_randomfield_error").empty()
           if (jqXHR.status == 422) {
               var parsed = JSON.parse(jqXHR.responseText);
               
              if (typeof parsed.trial != 'undefined') {
                $("#edit_trial_error").append(parsed.trial[0]);
              }
              if (typeof parsed.discount != 'undefined') {
                $("#edit_discount_error").append(parsed.discount[0]);
              }  
              if (typeof parsed.coupon != 'undefined') {
                $("#edit_randomfield_error").append(parsed.coupon[0]);
              }
           }
        },
   
    });
  }else{
     $('#editCouponForm .error-msg').hide();
  } 
}
function couponDeactive() {
        
    $('#user_deactivate_btn').addClass('disabled').text('Deactivating...');
    var id = $('#coupondeact').val();
   
    $.ajax({
       method: 'put',
       url: 'coupon/deactive',
       data: {
         value: id
       },
       success: function (data) {        
         // $('#' + id).parent().parent().remove();
          $('#user_deactivate_btn').removeClass('disabled').text('Confirm');
          $('#deactive_coupon').modal('hide'); 
          reloadUsersData();
       },
        error: function (jqXHR, exception) {
            $('#user_deactivate_btn').removeClass('disabled').text('Confirm');
        }
    });
      
}
// Ajax Call for Activate coupon
function couponActive() {
   
    
    $('#user_activate_btn').addClass('disabled').text('Activating...');
    var id = $('#couponact').val();

    $.ajax({
       method: 'put',
       url: 'coupon/active',
       data: {
         value: id
       },
       success: function (data) {        
         $('#user_activate_btn').removeClass('disabled').text('Confirm');
         $('#active_coupon').modal('hide');
         reloadUsersData();
       },
       error: function (data) {
            $('#user_activate_btn').removeClass('disabled').text('Confirm');

      }
      
    });
      
}
// Ajax Call for Delete coupon
function couponDelete(currentField) {
  
   $('#delete_plan').modal('hide');
    var id = $('#del').val();
    $.ajax({
        method: 'delete',
        url: 'coupon-delete',
        data: {
            value: id
           
        },
     success: function (data) {        
         $('#delete_coupon').modal('hide');
         reloadUsersData();
        },
        beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
    });
}


//triggered when modal is about to be shown
$('#deactive_coupon').on('show.bs.modal', function(e) {

    //populate the textbox
    $(e.currentTarget).find('input[name="coupondeact"]').val(e.relatedTarget.id);

})
//triggered when modal is about to be shown
$('#active_coupon').on('show.bs.modal', function(e) {

    //populate the textbox
    $(e.currentTarget).find('input[name="couponact"]').val(e.relatedTarget.id);
  
})
$('#delete_coupon').on('show.bs.modal', function(e) {

    //populate the textbox
    $(e.currentTarget).find('input[name="del"]').val(e.relatedTarget.id);
  
})

//triggered when modal is about to be shown

$('#edit_coupon').on('show.bs.modal', function(e) {
    //populate the textbox
    $(e.currentTarget).find('input[name="userId"]').val(e.relatedTarget.id);
    $(e.currentTarget).find('input[name="randomfield1"]').val($(e.relatedTarget).attr("coupon"));
    $(e.currentTarget).find('input[name="expire1"]').val($(e.relatedTarget).attr("expire"));
    $(e.currentTarget).find('input[name="trial1"]').val($(e.relatedTarget).attr("day"));
    $(e.currentTarget).find('input[name="discount1"]').val($(e.relatedTarget).attr("percent"));

    var user_type = $(e.relatedTarget).attr('user_type');  
    if(user_type==1){
       $(e.currentTarget).find('input[name="edit_no_of_terms"]').val($(e.relatedTarget).attr("number_of_terms"));
       $("#numberOfUserEdit").show();
       $("#Trial_cop1").hide();
       $("#discount_cop1").show();
    }else{
      $("#numberOfUserEdit").hide();
      $("#Trial_cop1").show();
      $("#discount_cop1").hide();
    } 
    $('#'+e.currentTarget.id + ' option[value='+ $(e.relatedTarget).attr('user_type')+']').attr('selected','selected');
   
     
});
// Tablesorter for coupon list
$(document).ready(function() 
    { 
        $("#activate_tab").tablesorter(); 
       
    } 
); 
$(document).ready(function() 
    { 
        $("#deactivated_tab").tablesorter();  
    } 
);  
// coupon validation using validate 
$(document).ready(function(){
    
    $( "#addCouponForm" ).validate({
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
            discount: "required",
            expire: "required",
            trial: "required",
            randomfield: {
                required: true,
                maxlength: 8,
                alphanumeric:true
            },
            no_of_terms : {
                required : true,
                number:true,
            },
          }, 
            messages: {
             discount: "The Discount field is required.",
             expire:   "The Expiry Date field is required.",
             trial:    "The Trial Days field is required.",
             randomfield:{
                required : "The coupon code field is required.",
                maxlength: "The coupon may not be greater than 8 characters.",
                alphanumeric:"This feild should contain alphanumeric value"
             },
             no_of_terms: {
                  required : "Please enter number of time user will be use.",
                  number : "Please enter only numeric value"
             }
            }
     
    });
    jQuery.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
    }); 
    
    $( "#editCouponForm" ).validate({
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
            discount1: "required",
            expire1: "required",
            trial1: "required",
            randomfield1: {
                required: true,
                maxlength: 8,
                alphanumeric:true
            },
            edit_no_of_terms : {
                required : true,
                number:true,
            },
          }, 
            messages: {
            discount1: "The Discount field is required.",
             expire1:   "The Expiry Date field is required.",
             trial1:    "The Trial Days field is required.",
             randomfield1:{
                required : "The coupon code field is required.",
                maxlength: "The coupon may not be greater than 8 characters.",
                alphanumeric:"This feild should contain alphanumeric value"
             },
             edit_no_of_terms: {
                  required : "Please enter number of time user will be use.",
                  number : "Please enter only numeric value"
             }

            }
     
    });
})
