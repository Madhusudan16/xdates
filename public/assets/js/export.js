$(document).ready(function(){
	$(".generate_csv").prop('disabled',true);
	$(".information").addClass('hide');
	if ($('.expired_link').length > 0) {
		$(".remove-expired").prop('disabled',false);
	} else {
		$(".remove-expired").prop('disabled',true);
	}
	$(".export_btn").click(function(){
		if($(this).hasClass('export_btn_active')) {
			$(this).blur();
			$(this).removeClass('export_btn_active');
		} else {
			$(this).blur();
			$(this).addClass('export_btn_active');
		}
		$numberOfActive = 0;
		$(".export_btn").each(function(index,value){
			if($(this).hasClass('export_btn_active')) {
				$numberOfActive++;
			}
		});
		console
		if($numberOfActive != 0) {
			$(".generate_csv").attr('disabled',false);
		} else {
			$(".generate_csv").attr('disabled',true);
		}
	});

	$(".generate_csv").click(function(index){
		$csvRequest = "";
		var i =0;
		$(".export_btn").each(function(index){
			if($(this).hasClass('export_btn_active')) {

				if(i == 0) {
					$csvRequest =  $csvRequest.concat($(this).attr('data-csv'));
				} else {
					$csvRequest +=  ","+$(this).attr('data-csv');
				}
				i++;
			}
		});
		if($csvRequest != "") {
			$(".information").removeClass('hide');
			$(".loader").show();
			$.post("/generate-csv",{type:$csvRequest}, function(response){ 
				changeExportList(response.resultData);
				$(".loader").hide();
				$(".information").addClass('hide');
			}).error(function(){
				$(".loader").hide();

			});
		} else {
			$('.error-msg').text('Please select export data button');
			$('.error-modal').modal('show');
			$(".information").addClass('hide');
		}
	});

	$(".remove-expired").click(function(){
		$(".loader").show();
		$.post("/export/remove", function(response){ 
			changeExportList(response.resultData);
			$(".loader").hide();
		}).error(function(){
			$(".loader").hide();
		});
	});

	window.onbeforeunload = function() {
	    $(".export_btn").each(function(index,value){
			if($(this).hasClass('export_btn_active')) {
				localStorage.setItem($(value).attr('data-btn'), 1);
			} else {
				localStorage.setItem($(value).attr('data-btn'), 0);
			}
		});
	}	
	window.onload = function() {
		$(".export_btn").each(function(index,value){
    		var btn_name = localStorage.getItem($(value).attr('data-btn'));
			if(btn_name == 1) {
				$(this).addClass('export_btn_active');
				$(".generate_csv").prop('disabled',false);
			}
		});    
	}
});
function goToSubPage()
{
   window.location.href = $subpageUrl;
}

function changeExportList($data)
{
	$html = "";
	$i = 1;
	$expired = 0;
	$(".export-result").removeClass('hide');
	$('tbody').html('');
	if(!$.isEmptyObject($data)) {
		$($data).each(function(key,subdata){
			if(subdata.is_expired) {
				$download = "<td><a href="+$downloadUrl+"/"+subdata.id+">download</a></td>";
			} else {
				$expired ++;
				$download = '<td class="text-center"><span class="expired_link">Expired</span></td>';
			}
			
			$html += "<tr><td>"+$i+"</td><td>"+subdata.createdDate+"</td><td>"+subdata.exportType+"</td><td>"+subdata.formatType+"</td><td>"+subdata.number_of_item+"</td><td>"+subdata.fileSize+"  KB</td><td>"+subdata.expiredDate+" 12:00AM</td><td>"+subdata.user_name+"</td>"+$download+"<tr>";
			$i++;
			
			if($expired != 0) {
				$(".remove-expired").prop('disabled',false)
			}
		});
		$('tbody').html($html);
	} else {
		$(".export-result").addClass('hide');
	}
	
}