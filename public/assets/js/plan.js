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
  }); 
}
$("#cancel").click(function () {
    $("input[name=name]").val("");
    $("input[name=number]").val("");
    $("input[name=cost]").val("");
    $("input[name=refer]").val("");

});
$("#add_plan").on("hidden.bs.modal", function(){
   $("#name_error").empty();
   $("#number_error").empty();
   $("#cost_error").empty();
   $("#refer_error").empty();
   $("input[name=name]").val("");
   $("input[name=number]").val("");
   $("input[name=cost]").val("");
   $("input[name=refer]").val("");
});
$("#edit_plan").on("hidden.bs.modal", function(){
   $("#edit_name_error").empty();
   $("#edit_number_error").empty();
   $("#edit_cost_error").empty();
   $("#edit_refer_error").empty();

});
// Ajax Call for Create plan
function addPlan() {
 

    $('#add_new_user_btn').addClass('disabled').text('Saving...'); 
    $.ajax({ 
        method: 'POST',
        url: 'plan-create',
        data: {
          
            name: $("input[name=name]").val(),
            number: $("input[name=number]").val(),
            cost: $("input[name=cost]").val(),
            refer: $("input[name=refer]").val(),
         
        },
        
        success: function (data) { 
          //$('#' + id).parent().parent().remove();
          $('.loader').hide();       
          $("#name_error").empty();
          $("#number_error").empty()
          $("#cost_error").empty()
          $("#refer_error").empty()
          //$("input[name=name]").val("");
	        //$("input[name=number]").val("");
         // $("input[name=cost]").val("");
          //$("input[name=refer]").val("");
          $('#add_plan').modal('hide');
          $('#basic').modal('show');
           $('#add_new_user_btn').removeClass('disabled').text('Save');
          reloadUsersData();
          setInterval(function(){$('#basic').modal('hide')},1000); 
        },

         beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
        error: function (jqXHR, exception) {
           $('#add_new_user_btn').removeClass('disabled').text('Save');
          $("#name_error").empty();
          $("#number_error").empty();
          $("#cost_error").empty();
          $("#refer_error").empty();
          
          if (jqXHR.status == 422) {
               var parsed = JSON.parse(jqXHR.responseText);
              if (typeof parsed.name != 'undefined') {
                $("#name_error").append(parsed.name[0]);
              }
              if (typeof parsed.number != 'undefined') {
                $("#number_error").append(parsed.number[0]);
              }  
              if (typeof parsed.cost != 'undefined') {
                $("#cost_error").append(parsed.cost[0]);
              }
              if (typeof parsed.refer!= 'undefined') {
                $("#refer_error").append(parsed.refer[0]);
              }
          }
    },
   
    });

}
// Ajax Call for Edit users
function editPlan() {
    $('#edit_new_user_btn').addClass('disabled').text('Saving...'); 
    var id = $('#planId').val();
  
        $.ajax({
           method: 'put',
           url: 'plan-edit',
           data: {
            value: id,
           
            name: $("input[name=namen]").val(),
            number: $("input[name=numbern]").val(),
            cost: $("input[name=costn]").val(),
            refer: $("input[name=refern]").val(),
        
           },
           success: function (data) {        
              //$('#' + id).parent().parent().remove();
              
                $('#edit_plan').modal('hide');
                $('#edit_new_user_btn').removeClass('disabled').text('Save');
               reloadUsersData();
           },
          error: function (jqXHR, exception) {
             $('#addUserForm .error-msg').show();
          $("#edit_name_error").empty();
          $("#edit_number_error").empty();
          $("#edit_cost_error").empty();
          $("#edit_refer_error").empty();
          $('#edit_new_user_btn').removeClass('disabled').text('Save'); 
          if (jqXHR.status == 422) {
               var parsed = JSON.parse(jqXHR.responseText);
              if (typeof parsed.name != 'undefined') {
                $("#edit_name_error").append(parsed.name[0]);
              }
              if (typeof parsed.number != 'undefined') {
                $("#edit_number_error").append(parsed.number[0]);
              }
              if (typeof parsed.cost != 'undefined') {
                $("#edit_cost_error").append(parsed.cost[0]);
              }
              if (typeof parsed.refer != 'undefined') {
                $("#edit_refer_error").append(parsed.refer[0]);
              }
          }
    },
        beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
        });
      
}
// Ajax Call for Deactivate users

// Ajax Call for Delete plan
function deletePlan(currentField) {
  
   $('#delete_plan').modal('hide');
    var id = $('#del').val();
 
    $.ajax({
        method: 'delete',
        url: 'plan-delete',
        data: {
            value: id
           
        },
     success: function (data) {        
              console.log('sucessfull'); 
        console.log(data);
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
// Ajax Call for Deactivate users
function userDeactive() {
    	
	$('#user_deactivate_btn').addClass('disabled').text('Deactivating...');
	var id = $('#userdeact').val();
   
        $.ajax({
           method: 'put',
       url: 'plan/deactive',
       data: {
         value: id
       },
       success: function (data) {        
         // $('#' + id).parent().parent().remove();
          $('#user_deactivate_btn').removeClass('disabled').text('Confirm');
          $('#deactive_user').modal('hide'); 
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
       url: 'plan/active',
       data: {
         value: id
       },
       success: function (data) {        
         $('#user_activate_btn').removeClass('disabled').text('Confirm');
         $('#active_user').modal('hide');
         reloadUsersData();
       },
       error: function (data) {
    		$('#user_activate_btn').removeClass('disabled').text('Confirm');
       var errors = data.responseJSON;
       if(errors.error=='number of user exceed.'){

       $('#upgrade_user').modal('show');
      }
    	}
    });
      
}
//triggered when modal is about to be shown
$('#edit_plan').on('show.bs.modal', function(e) {
    //populate the textbox
    $(e.currentTarget).find('input[name="planId"]').val(e.relatedTarget.id);
    $(e.currentTarget).find('input[name="namen"]').val($(e.relatedTarget).attr("nameplan"));
    $(e.currentTarget).find('input[name="numbern"]').val($(e.relatedTarget).attr("numberplan"));
    $(e.currentTarget).find('input[name="costn"]').val($(e.relatedTarget).attr("costplan"));
    $(e.currentTarget).find('input[name="refern"]').val($(e.relatedTarget).attr("referplan"));
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
$('#delete_plan').on('show.bs.modal', function(e) {

    //populate the textbox
    $(e.currentTarget).find('input[name="del"]').val(e.relatedTarget.id);
  
})
$(document).ready(function() 
    { 
        $("#activate_tab").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } });  
        $("#deactivated_tab").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } }); 
    } 
);  
