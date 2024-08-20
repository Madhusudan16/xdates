$(document).ready(function(){
	$.ajaxSetup({
		headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
	$(".trial_decline_comfirm_btn").prop('disabled',true);
	$("#trial_decline").keyup(function(){
		if($(this).val().length >10 ) {
			$(".trial_decline_comfirm_btn").prop('disabled',false);
		} else {
			$(".trial_decline_comfirm_btn").prop('disabled',true);
		}
	});

	$(".approve_trial").click(function(){
		var id = $(this).attr('user');
		if(id != '' && id != null) {
			$(".trial_approve_comfirm").attr('who',id);
			$("#comfirm_trial").modal('show');
		} 
	});
	
	$(".decline_trial").click(function(){
		$("#trial_decline").val('');
		var id = $(this).attr('user');
		if(id != '' && id != null) {
			$(".trial_decline_comfirm_btn").attr('who',id);
			$("#edit_trial").modal('show');
		} 
	});
	$(".sortable-table").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } }); 
});

function extend(element,is_edit) {
	$(element).text("confirming...");
	var id = $(element).attr('who');
	if(is_edit == 0) {
		var reason = "";
	} else {
		var reason = $("#trial_decline").val();
	}
	if(id != '' && id != null) {
		$.post(site_url+'/extend_trial_approve',{id:id,reason:reason,is_edit:is_edit},function(){
			window.location.reload();
			$(element).text("Confirm");
		}).fail(function(){
			$(element).text("Confirm");
		});
	}
	$('#edit_trial').on('shown.bs.modal', function() {
    	$(".trial_decline_comfirm_btn").prop('disabled',true);
	})

}