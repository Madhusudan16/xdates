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
      $("#activate_tab").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } }); 
     $("#deactivated_tab").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } }); 
     $('.loader').hide();
  })

}


$("#cancel").click(function () {
    $("input[name=name]").val("");
    $("input[name=email]").val("");
    $("#exampleSelect1111").val('');

});

$("#add_user").on("hidden.bs.modal", function(){
   $("#name_error").empty();
   $("#email_error").empty();
   $("input[name=name]").val("");
   $("input[name=email]").val(""); 
   $('select[name="account_type"] option:selected').attr("selected",null);
   $("#addUserForm").validate().resetForm();
  
});
$("#edit_user").on("hidden.bs.modal", function(){
   $("#edit_name_error").empty();
   $("#edit_email_error").empty();
   $("#editUserForm").validate().resetForm();

});
// Ajax Call for Create users
function addNewUser() {
    if($( "#addUserForm" ).valid()){
    	$('#add_new_user_btn').addClass('disabled').text('Saving...');  
	    $.ajax({
	        method: 'POST',
	        url: 'user',
	        data: {
	            name: $("input[name=name]").val(),
	            email: $("input[name=email]").val(),
	            type: $("select[name=account_type]").val(),
	        },
	        success: function (data) { 
	          //$('#' + id).parent().parent().remove();
	          $('.loader').hide();       
	          $("#name_error").empty();
	          $("#email_error").empty()
	          $("input[name=name]").val("");
	          $("input[name=email]").val("");
	          $('#add_user').modal('hide');
	          $('.user-tab a[href="#activated"]').tab('show');
	          $('#add_new_user_btn').removeClass('disabled').text('Save');
	          reloadUsersData(); 
	        },
	        error: function (jqXHR, exception) {
	          $('#add_new_user_btn').removeClass('disabled').text('Save');
	          
	          $('#addUserForm .error-msg').show();
	          
	          $("#name_error").empty();
	          $("#email_error").empty();
	        //var msg = '';
	        
	          if (jqXHR.status == 422) {
	               var parsed = JSON.parse(jqXHR.responseText);
	              if (typeof parsed.name != 'undefined') {
	                $("#name_error").append(parsed.name[0]);
	              }
	              if (typeof parsed.email != 'undefined') {
	                $("#email_error").append(parsed.email[0]);
	              }
	         }else if(jqXHR.status == 403)
                  var parsed = JSON.parse(jqXHR.responseText);
                  if(parsed.error=='number of user exceed.'){
                   $("#add_user").modal('hide');
                   $('#upgrade_user').modal('show');
                  }
	    	},
	    });
	}else{
		$('#addUserForm .error-msg').hide();
	}
}
// Ajax Call for Edit users
function userEdit() {
     if($( "#editUserForm" ).valid()){
    	$('#edit_new_user_btn').addClass('disabled').text('Saving...');  
	    var id = $('#userId').val();
	        $.ajax({
	           method: 'put',
	           url: 'user/edit',
	           data: {
	            value: id,
	            name: $("input[name=namen]").val(),
	            email: $("input[name=emailn]").val(),
	      		typen: $("select[name=account_typen]").val(),
	           },
	           success: function (data) {        
	              //$('#' + id).parent().parent().remove();
	                $('#edit_new_user_btn').removeClass('disabled').text('Save');
	                $('#edit_user').modal('hide');
	                
	               reloadUsersData();
	           },
	          error: function (jqXHR, exception) {
	          		  $('#edit_new_user_btn').removeClass('disabled').text('Save');
	          
	          			$('#editUserForm .error-msg').show();
	          
			          $("#edit_name_error").empty();
			          $("#edit_email_error").empty();
			
			          if (jqXHR.status == 422) {
			               var parsed = JSON.parse(jqXHR.responseText);
			              if (typeof parsed.name != 'undefined') {
			                $("#edit_name_error").append(parsed.name[0]);
			              }
			              if (typeof parsed.email != 'undefined') {
			                $("#edit_email_error").append(parsed.email[0]);
			              }
			          }
			    },
	         
	        });
	}else{
		$('#editUserForm .error-msg').hide();
	}
}
// Ajax Call for Deactivate users
function userDeactive() {
    	
	$('#user_deactivate_btn').addClass('disabled').text('Deactivating...');
	var id = $('#userdeact').val();
   
        $.ajax({
           method: 'put',
       url: 'user/deactive',
       data: {
         value: id
       },
       success: function (data) {        
         // $('#' + id).parent().parent().remove();
          if(data == 1) {
          	window.location.reload();
          }
          $('#user_deactivate_btn').removeClass('disabled').text('Confirm');
          $('#deactive_user').modal('hide'); 
          $('.user-tab a[href="#deactivated"]').tab('show');
          reloadUsersData();
       },
        error: function (jqXHR, exception) {
        	$('#user_deactivate_btn').removeClass('disabled').text('Confirm');
        }
    });
      
}
// Ajax Call for Activate users
function userActive() {
    //$('#active_user').modal('hide');
    
    $('#user_activate_btn').addClass('disabled').text('Activating...');
    var id = $('#useract').val();
    $.ajax({
       method: 'put',
       url: 'user/active',
       data: {
         value: id
       },
       success: function (data) {        
         $('#user_activate_btn').removeClass('disabled').text('Confirm');
         $('#active_user').modal('hide');
         $('.user-tab a[href="#activated"]').tab('show');
         reloadUsersData();
       },
       error: function (data) {
    		//$('#user_activate_btn').removeClass('disabled').text('Confirm');
       var errors = data.responseJSON;
       if(errors.error=='number of user exceed.'){

       $('#upgrade_user').modal('show');
      }
    	}
    });
      
}
// Ajax Call for Delete users
function userDelete(currentField) {
   //$('#delete_user').modal('hide');
   
   $('#user_delete_btn').addClass('disabled').text('Deleting...');
    var id = $('#del').val();
    $.ajax({
        method: 'delete',
        url: 'user/delete',
        data: {
            value: id
            
        },
     success: function (data) {        
         $('#user_delete_btn').removeClass('disabled').text('Confirm');
         $('#delete_user').modal('hide');
         reloadUsersData();
        },
       error: function (jqXHR, exception) {
    		$('#user_delete_btn').removeClass('disabled').text('Confirm');
    	}
    });
}

$(document).ready(function(){
	jQuery.validator.addMethod("first_last_name", function(value, element) {
  		return this.optional(element) || /^([A-Za-z0-9]*((\s)))+[A-Za-z0-9]+/.test(value);
	}, "This field should be first and last name separated by space.");

	jQuery.validator.addMethod("check_select", function(value, element) {
		if(value == "") {
  			return false;
  		} else {
  			return true;
  		}
	}, "Please select account type.");

	$( "#addUserForm" ).validate({
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
		  	name: {
		  		required: true,
		  		first_last_name: true
		  	},
		    email: {
				required: true,
				email: true
			},
			account_type: {
				check_select: true
			},
		  },
		  messages: {
			name: {
				required : "The name field is required.",
				first_last_name : "This field should have first and last name with space."
			},
			email: {
				required : "The email field is required.",
				email: "The email field is not valid."
			},
			account_type: {
				required: "Please select account type."
			}
		  }
	});
	
	$( "#editUserForm" ).validate({
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
		  	namen: {
		  		required: true,
		  		first_last_name: true
		  	},
		    email: {
				required: true,
				email: true
			},
			account_typen: {
				check_select: true
			},
		  },
		  messages: {
			name: "The name field is required.",
			email: {
				required : "The email field is required.",
				email: "The email field is not valid."
			}
		  }
	});


	$("#updateOwnerShip").validate({
		errorElement: 'span',
    	errorClass: 'help-block error-help-block',

    	errorPlacement: function(error, element) {
    		console.log('here');
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
		  	admin_list: "required"
		  },
		  messages: {
			admin_list: "you have not selected any value",
		 }
	});
})
//triggered when modal is about to be shown
$('#edit_user').on('show.bs.modal', function(e) {
    //populate the textbox
    $(e.currentTarget).find('input[name="userId"]').val(e.relatedTarget.id);
    $(e.currentTarget).find('input[name="namen"]').val($(e.relatedTarget).attr("username"));
    $(e.currentTarget).find('input[name="emailn"]').val($(e.relatedTarget).attr("email"));
    $('#'+e.currentTarget.id + ' option[value='+ $(e.relatedTarget).attr('acc_type')+']').attr('selected','selected');
})
//triggered when modal is about to be shown
$('#deactive_user').on('show.bs.modal', function(e) {

    //populate the textbox
    $(e.currentTarget).find('input[name="userdeact"]').val(e.relatedTarget.id);

})
//triggered when modal is about to be shown
$('#active_user').on('show.bs.modal', function(e) {

    //populate the textbox
    $(e.currentTarget).find('input[name="useract"]').val(e.relatedTarget.id);
  
})
$('#delete_user').on('show.bs.modal', function(e) {

    //populate the textbox
    $(e.currentTarget).find('input[name="del"]').val(e.relatedTarget.id);
  
})
$(document).ready(function() 
    { 
    	$("#activate_tab").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } }); 
    } 
); 
$(document).ready(function() 
    { 
        $("#deactivated_tab").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } }); 

        $(".upgrade_need_plan").click(function(){
        	window.location.href=site_url+"/planbill/change-plan";
        });
    } 
); 
function updateOwnerShip()
{
	console.log($("#admin_list").val());
	
	if($("#updateOwnerShip").valid()){ 
		var id =$("#admin_list").val();
		if(id != "" && id != null ) {
			$("#update_user_date_btn").text('updating...');
			$.get(site_url+"/change-ownership",{id:id}, function(data, status){
				$("#update_user_date_btn").text('update');
				window.location.reload();
		    });
		}
	}

}
