$(document).ready(function(){
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });
});

var tempNewField = null;
function addNewField(policyType) {
    var uniqueNumber = Math.floor((Math.random() * 1000) + 1);
    if (policyType == 'lines') {
       methodName = 'createLines(this)';        
    }else if (policyType == 'industry') {
             methodName = 'createIndustry(this)';    
         } else if (policyType == 'personal') {
             methodName = 'createPersonal(this)';    
           } else if (policyType == 'commercial') {
               methodName = 'createCommercial(this)';   
             }  
    $('#' +policyType).append("<li><fieldset class='form-group'><span class='border-span'><input type='text' name='lines[]' class='form-control new-added-"+uniqueNumber+"' data-id ='"+uniqueNumber+"'   id='exampleInputEmail1' data-type="+policyType+"  onchange="+methodName+"; placeholder=''></span> <a href='#' onclick='deleted(this)' class='remove_line remove_button-"+uniqueNumber+"' id=''  data-toggle='modal' data-target=''><img src='"+site_url+"/assets/images/close_line.png'></a></fieldset></li>");
}
function confirmDelete() { 
    $('#delete_line').modal('hide'); 
    var id = $('#policyId').val();

     if(id == "" && tempNewField != null){ 
     	tempNewField.closest('li').remove();
     	tempNewField = null;
     }else{ 
	        $.ajax({
	           method: 'delete',
	           url: 'policy/delete',
	           data: {
	             value: id
	           },
	           success: function (data) {        
	              $('#' + id).parent().parent().remove();
	           },
	          beforeSend: function(){
	            $('.loader').show();
	          },
	          complete: function(){
	           $('.loader').hide();
	          },
	     });
     }
}

function isDuplicateField(obj,objVal,objType){
	var rFlag = false;
	$('input[data-type='+objType+']').not(obj).each(function(){
		console.log($.trim($(this).val())+"=="+objVal);
		if($.trim($(this).val().toLowerCase()) == (objVal).toLowerCase()){
			$(obj).addClass('duplicate-error');
			rFlag = true;
			return true;
		}
	}) 
	return rFlag;
}
function createLines(currentField) {
	var name = currentField.value.trim();
	var uniqueNumber = $(currentField).attr('data-id');
	var objType = $(currentField).attr('data-type');
	$('input[data-type='+objType+']').removeClass('duplicate-error');
	
	if (name.length <=0 || isDuplicateField(currentField,name,objType)) {
		//$(currentField).parent().parent().remove();
    		return false;	
	}
    $.ajax({
        method: 'POST',
        url: 'policy/lines',
        data: {
            name: name,
            type: 1
        },
	    success: function (data) {        
          //console.log('sucessfull');
	      console.log(data.id);
          $(".remove_button-"+uniqueNumber).attr('id',data.id);
          $(currentField).attr('onchange',"updateLines("+data.id+", this)");
          $(".remove_button-"+uniqueNumber).attr('data-target',"#delete_line");
        },
        beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
    });
}

function updateLines(id, currentField) {
	var name = currentField.value.trim();
	
	var objType = $(currentField).attr('data-type');
	$('input[data-type='+objType+']').removeClass('duplicate-error');
	
	if (name.length <=0  || isDuplicateField(currentField,name,objType)) {
		//$(currentField).parent().parent().remove();
    		return false;	
	}
    $.ajax({
       method: 'POST',
        url: site_url+'/policy/lines/update',
        data: {
            id: id,
            name: name
        },
        success: function (data) {        
          //console.log('sucessfull');
          //console.log(data);
        },
        beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
    });
}

function createIndustry(currentField) {
	var name = currentField.value.trim();
    var uniqueNumber = $(currentField).attr('data-id');
	var objType = $(currentField).attr('data-type');
	$('input[data-type='+objType+']').removeClass('duplicate-error');
	
	if (name.length <=0  || isDuplicateField(currentField,name,objType)) {
		//$(currentField).parent().parent().remove();
    		return false;	
	}
    $.ajax({
        method: 'POST',
        url: 'policy/industry',
        data: {
           
            name: name,
            type: 2
         },
        success: function (data) {        
          //console.log('sucessfull');
          //console.log(data);
          $(".remove_button-"+uniqueNumber).attr('id',data.id);
          $(currentField).attr('onchange',"updateLines("+data.id+", this)");
          $(".remove_button-"+uniqueNumber).attr('data-target',"#delete_line");
        },
        beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
    });
}

function updateIndustry(id, currentField) {
	var name = currentField.value.trim();
	var objType = $(currentField).attr('data-type');
	$('input[data-type='+objType+']').removeClass('duplicate-error');
	
	if (name.length <=0  || isDuplicateField(currentField,name,objType)) {
		//$(currentField).parent().parent().remove();
    		return false;	
	}
    $.ajax({
       method: 'PUT',
        url: 'policy/industry/update',
        data: {
            id: id,
            name: name 
        },
        success: function (data) {        
          //console.log('sucessfull');
          //console.log(data);

        },
        beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
    });
}

function createPersonal(currentField) {
	var name = currentField.value.trim();
    var uniqueNumber = $(currentField).attr('data-id');
	var objType = $(currentField).attr('data-type');
	$('input[data-type='+objType+']').removeClass('duplicate-error');
	
	if (name.length <=0  || isDuplicateField(currentField,name,objType)) {
		//$(currentField).parent().parent().remove();
    		return false;	
	}
    $.ajax({
        method: 'POST',
        url: 'policy/personal',
        data: {
            type: 3,
            name: name
         },
        success: function (data) {        
          //console.log('sucessfull');
          //console.log(data);
          $(".remove_button-"+uniqueNumber).attr('id',data.id);
          $(currentField).attr('onchange',"updateLines("+data.id+", this)");
          $(".remove_button-"+uniqueNumber).attr('data-target',"#delete_line");
        },
        beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
    });
}

function updatePersonal(id, currentField) {
	var name = currentField.value.trim();
	var objType = $(currentField).attr('data-type');
	$('input[data-type='+objType+']').removeClass('duplicate-error');
	
	if (name.length <=0  || isDuplicateField(currentField,name,objType)) {
		//$(currentField).parent().parent().remove();
    		return false;	
	}
    $.ajax({
       method: 'PUT',
        url: 'policy/personal/update',
        data: {
            id: id,
            name: name 
        },
        success: function (data) {        
          //console.log('sucessfull');
          //console.log(data);
        },
        beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
    });
}
function createCommercial(currentField) {
	var name = currentField.value.trim();
    var uniqueNumber = $(currentField).attr('data-id');
	var objType = $(currentField).attr('data-type');
	$('input[data-type='+objType+']').removeClass('duplicate-error');
	
	if (name.length <=0  || isDuplicateField(currentField,name,objType)) {
		//$(currentField).parent().parent().remove();
    		return false;	
	}
    $.ajax({
        method: 'POST',
        url: 'policy/commercial',
        data: {
            type: 4,
            name: name
         },
        success: function (data) {        
          //console.log('sucessfull');
          //console.log(data);
          $(".remove_button-"+uniqueNumber).attr('id',data.id);
          $(currentField).attr('onchange',"updateLines("+data.id+", this)");
          $(".remove_button-"+uniqueNumber).attr('data-target',"#delete_line");
        },
        beforeSend: function(){
         $('.loader').show();
        },
        complete: function(){
         $('.loader').hide();
        },
    });
}

function updateCommercial(id, currentField) {
	var name = currentField.value.trim();
	var objType = $(currentField).attr('data-type');
	$('input[data-type='+objType+']').removeClass('duplicate-error');
	
	if (name.length <=0  || isDuplicateField(currentField,name,objType)) {
		//$(currentField).parent().parent().remove();
    		return false;	
	}
    $.ajax({
       method: 'PUT',
        url: 'policy/commercial/update',
        data: {
            id: id,
            name: name
        },
        success: function (data) {        
          //console.log('sucessfull');
          //console.log(data);
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
$('#delete_line').on('show.bs.modal', function(e) {
	
	if(!e.relatedTarget.id || e.relatedTarget.id == ""){
		tempNewField = $(e.relatedTarget);
	}else{
		tempNewField = null;
	}
    //populate the textbox
    $(e.currentTarget).find('input[name="policyId"]').val(e.relatedTarget.id);
   
});

function deleted(element){
   console.log($(element).attr('id'));
    if($(element).attr('id') == "") {
        $(element).closest('li').remove();
    } 
}