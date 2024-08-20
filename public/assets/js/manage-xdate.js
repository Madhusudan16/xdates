// When the document is ready

    jQuery.validator.addMethod("lettersonly", function(value, element) {
        return this.optional(element) || /^[a-z ]+$/i.test(value);
    }, "Letters only please");

    var nextXarray = new Array();
    var isChangesXForm = false;
	jQuery(document).ready(function ($) {
        var sel2 =  $('.select2').select2();
        /*var sel2 =  $('.select2').select2({
            placeholder: '--Select One--'
        }) ;*/

       setTimeout(function(){
            $('.select2-selection--single').focus(function(){
                $(this).closest('.detail-field').addClass('focused');
            });
            $('.select2-selection--single').blur(function(){
                $(this).closest('.detail-field').removeClass('focused');
            });
       });
        /*$('.addNewUser').on('hidden.bs.modal', function () {
            $(".full-width-tr").width('100%');
            
        });*/

       $("#searchString").on('keypress keyup',function(){
                if($("#searchString").val().length >=2 ){
                    $("#searchString").autocomplete({ source:site_url+"/search",minLength:3}).data("uiAutocomplete")._renderItem = function (ul, item) {
                        var reg = new RegExp(this.term, "i") ;
                        var id = item.id;
                        var jsonArray = JSON.stringify(item);
                         jsonArray = jsonArray.replace(/'/g, "&rsquo;");
                        //var jsonArray =
                        var html = '';
						var cityStateText = '';

						if(item.city != ''){
						 	cityStateText += item.city+', ';
						}
						cityStateText += item.state;

                        if(typeof item.xcontact != undefined && item.xcontact != null && item.xcontact != ''){
                            var name = item.xcontact.replace(reg,"<span style='background-color:#eac5c8;'>" + this.term + "</span>");
                            name = name.toLowerCase();
                            var html =  $("<li class='search-result'></li>").data("autocomplete", item).append('<div  onclick=\'xautocomepleteClick('+jsonArray+')\'  class="media"><a class="media-left media-middle" href="#"><img class="media-object" src="'+base_url+'/assets/images/common/user_male.png" ></a><div class="media-body media-middle"><h4 class="media-heading">'+name+' ('+item.policy_type_txt+')</h4>'+cityStateText+'</div></div>').appendTo(ul);
                        }
                        if(typeof item.xname != undefined && item.xname != null && item.xname != ''){
                                var x_name = item.xname.replace(reg,"<span style='background-color:#eac5c8;'>" + this.term + "</span>");
                                x_name = x_name.toLowerCase();
                                var  html = $("<li class='search-result'></li>").data("autocomplete", item).append('<div  onclick=\'xautocomepleteClick('+jsonArray+')\' class="media"><a class="media-left media-middle" href="#"><img class="media-object" src="'+base_url+'/assets/images/common/xdate-icon.png" ></a><div class="media-body media-middle"><h4 class="media-heading">'+x_name+' ('+item.policy_type_txt+')</h4>'+cityStateText+'</div></div>').appendTo(ul);
                        }
                        return html;
                    };
                }
        });

        $('.form_date').datetimepicker({
            weekStart: 0,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            pickerPosition: "bottom-left"
        });

        $('.xdate-picker').datetimepicker({
            weekStart: 0,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            pickerPosition: "bottom-left"
        }).on('changeDate', function(ev){

            var xnewdatetime = new Date(ev.date.valueOf());
            var yr = xnewdatetime.getFullYear();
            var mon = xnewdatetime.getMonth();
            var dt = xnewdatetime.getDate();

            $('#xdate').val(pad((mon+1),2)+'/'+pad(dt,2));
            $('#xdate_org').val(yr+'-'+pad((mon+1),2)+'-'+pad(dt,2));
            enableSkipDateBtn();
        });

        $('.fdate-picker').datetimepicker({
            weekStart: 0,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            pickerPosition: "top-left"
        });

        intializeXdatePicker();

        var hScroll = ($(window).width() < 769) ? true : false;
         if(!$('html').hasClass('touch')){
            $('.scrollbox').enscroll({
                horizontalScrolling: hScroll,
                verticalTrackClass: 'track',
                verticalHandleClass: 'handle',
                clickTrackToScroll: true,
                minScrollbarLength: '50',
                scrollUpButtonClass: 'scroll-up',
                scrollDownButtonClass: 'scroll-down'
            });
        }
        $(window).resize(function(){
        	$("#all > table,#live > table,#converted > table,#dead > table").each(function(){
				$('thead th',this).removeAttr('style');
		    	$('tbody td',this).removeAttr('style');
		    	$('thead > tr',this).removeAttr('style');
		    	$(this).removeAttr('style');
			});

            var hScroll = ($(window).width() < 769) ? true : false;
            if(!$('html').hasClass('touch')){
                $('.scrollbox').enscroll('destroy');
                $('.scrollbox').enscroll({
                    horizontalScrolling: hScroll,
                    verticalTrackClass: 'track',
                    verticalHandleClass: 'handle',
                    clickTrackToScroll: true,
                    minScrollbarLength: '50',
                    scrollUpButtonClass: 'scroll-up',
                    scrollDownButtonClass: 'scroll-down'
                });
                floatThead();
            }
            
        });

		$('.scrollbox').scroll(function (event) {
	        var sc = $(this).scrollTop();
	        var scL = $(this).scrollLeft();
	        if(scL == 0){
	        	//$('thead',this).removeAttr('style');
	        	$('thead > tr',this).css({'left':0+'px'});
	        }else{
	        	$('thead > tr',this).css({'left':-(scL)+'px'});
		        //$('thead > tr',this).css({'position':'absolute','top':((sc)+41)+'px'});
	        }

	        //console.log(sc);
	    });

	    $(window).load(function(){
	    	if(!$('html').hasClass('touch')){
               floatThead();
            }
        });

       //apply sorting to tables
        $("#all > table,#live > table,#converted > table,#dead > table").tablesorter({ cssAsc : 'headerSortDown', cssDesc : 'headerSortUp',headers: {  '.no-sort' : { sorter: false} } });


        /***** Filter Functionality ******/
	    $('#filterdropdown').click(function() {
	        $('.filtertab-group').toggle();
	    });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            var $target = $(e.target);
            var currentTabID = $target.attr('href').replace('#','');
            $filterData = JSON.parse($('#'+currentTabID).attr('data-filter'));

            if($('#'+currentTabID).hasClass('hasFilterData')){
            	$('#filterdropdown').addClass('active');
            }else{
            	$('#filterdropdown').removeClass('active');
            }

            setTimeout(function(){ 
                if(!$('html').hasClass('touch')){
                    floatThead();
                }
             },500);

            disableEnableFilterOption(currentTabID,$filterData);
        });

        if($('#all').attr('data-filter') != '{}'){
            var currentTabID = 'all';
            $filterData = JSON.parse($('#'+currentTabID).attr('data-filter'));
            if($('#'+currentTabID).hasClass('hasFilterData')){
                $('#filterdropdown').addClass('active');
            }else{
                $('#filterdropdown').removeClass('active');
            }
            disableEnableFilterOption('all',$filterData);
        }
		$('#div_f_x_from_date').on('changeDate', function(ev){
			var date = new Date(ev.date.valueOf());
			var yr = date.getFullYear();
			var mon = date.getMonth();
			var dt = date.getDate();

			$('#div_f_x_to_date input').val('');
			$('#div_f_x_to_date').datetimepicker('update');
			$('#div_f_x_to_date').datetimepicker('setStartDate', yr+'-'+pad((mon+1),2)+'-'+pad(dt,2));

            disableXquickDate();

		});

        $('#div_f_f_from_date').on('changeDate', function(ev){
            var date = new Date(ev.date.valueOf());
            var yr = date.getFullYear();
            var mon = date.getMonth();
            var dt = date.getDate();

            $('#div_f_f_to_date input').val('');
            $('#div_f_f_to_date').datetimepicker('update');
            $('#div_f_f_to_date').datetimepicker('setStartDate', yr+'-'+pad((mon+1),2)+'-'+pad(dt,2));

            disableFquickDate();

        });

        $('#f_x_quick_date').change(function(){
            console.log($('option:selected',this).val());

            if($('option:selected',this).val() != ''){
                disableXfromtoDate();
            }else{
                enableXfromtoDate();
            }
        });

        $('#f_f_quick_date').change(function(){

            if($('option:selected',this).val() != ''){
                disableFfromtoDate();
            }else{
                enableFfromtoDate();
            }
        });

        $('#f_policy_type').change(function(){
            var $selOpt = $('option:selected',this);
            if(typeof $selOpt.attr('data-type') != undefined && $selOpt.attr('data-type') == 'personal-opt'){
                disableFindustry();
            }else{
                enableFindustry();
            }
        });

        $('body').on('click','.disabled',function(e){
            e.preventDefault();
            return false;
        });

        $('#xdate_filter_form input,#xdate_filter_form select').on('change',function(){
            checkAndEnableFilterBtn();
        });

        $('#x_filter_btn').click(function(){

            var $activeTab = $('.tab-pane.active');
            var $filterForm = $('#xdate_filter_form');

            var fObj = $filterForm.serializeObject();
            $activeTab.attr('data-filter',JSON.stringify(fObj));

            $('.x-row',$activeTab).remove();

            $activeTab.addClass('hasFilterData');
            $('#filterdropdown').addClass('active');

            filterFormData(fObj,$activeTab);
            closeFdropdown();
            //, no-xrecord, no-xrecord-filter


        });

        $('.x_clear_btn').click(function(){
            var $activeTab = $('.tab-pane.active');
            var $filterForm = $('#xdate_filter_form');
            closeFdropdown();
            removeTabFilter($activeTab.attr('id'));
        });


        disableFilterSection('follow');

        /**** End of filter functionality ****/


        /******* Add/View Xdate functionality *****/
        var phones = [{ "mask": "(###) ###-####"},{ "mask": "(###) ###-#####"}];
        $('#phone').inputmask({
            mask: phones,
            greedy: false,
            definitions: { '#': { validator: "[0-9]", cardinality: 1}}
        });

        //addNewUserModal add_new_xdate_btn
        $('#add_new_xdate_btn').click(function(){
            disableEnableOtherFeatures('add','');
            nextXarray = new Array();
            openAddXdateForm();
        });

        $('#line').change(function(){
            changePolicyDropDown($('option:selected',this).text().toLowerCase());
            disableEnableIndustry($('option:selected',this).text().toLowerCase());
            disableEnableContact($('option:selected',this).text().toLowerCase());
            addRemoveValidation($('option:selected',this).text().toLowerCase());
        });

        $('#website').change(function(){
            var thisVal = $(this).val();
            if($.trim(thisVal) !=  '') {
                var removeHttpStr = thisVal.replace(/.*?:\/\//g, "");
                $(this).val(removeHttpStr.toLowerCase());
            }
        });


        /// xdate form validation
        $( "#add_edit_xdate_form" ).validate({
            ignore: [],
            errorElement: 'span',
            errorClass: 'help-block error-help-block',
            errorPlacement: function(error, element) {

            },
            highlight: function(element) {
                $(element).closest('.detail-field').addClass('has-error').addClass('requiredfield'); // add the Bootstrap error class to the control group
            },
            unhighlight: function(element) {
                $(element).closest('.detail-field').removeClass('has-error').removeClass('requiredfield'); // add the Bootstrap error class to the control group
            },
            rules: {

                xdate: "required",
                xname: "required",
                line: "required",
                policy_type: "required",
                producer: "required",
                city: { required: true, lettersonly: true },
                state: "required",
                email:{
                    email: true
                },
                status: "required",
                follow_up_date: "required"
            }
        });

        $( "#add_note_form" ).validate({
            ignore: [],
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
                $(element).parent('div').addClass('has-error').addClass('requiredfield'); // add the Bootstrap error class to the control group
            },
            unhighlight: function(element) {
                $(element).parent('div').removeClass('has-error').removeClass('requiredfield'); // add the Bootstrap error class to the control group
            },
            rules: {
                notes_txt: "required"
            }
        });




        $('#xdate, #xname, #line, #policy_type, #industry, #state, #status, #follow_up_date').change(function(){
            $(this).trigger('focusout');
        });

        $('body').on('click', '.x-row', function () {
            if($(this).attr('data-xdata') != ''){
                var xObjData = JSON.parse($(this).attr('data-xdata'));
                openViewXdateForm(xObjData);
                nextXarray = new Array();
            }
        });

        $('#edit_xdate_btn').click(function(){
            $('#addNewUserModal').removeClass('form-view').addClass('form-edit-view');
        });

        //save xdate data
        $('#save_xdate_btn').click(function(){
            var $saveBtn = $(this);
            var $xdate_Id = $('#current_xdate_id').val();
            if($( "#add_edit_xdate_form" ).valid()){
                $('.x-error-msg,.x-success-msg').addClass('hide');
                $saveBtn.addClass('disabled').text('Saving...');
                disableEnableDetails('disable');
                $.ajax({
                    method: 'POST',
                    url: site_url+'/xdate/addupate',
                    data: $('#add_edit_xdate_form').serialize(),
                    success: function (data) {
                        var resData = data;
                        updateDataToTables(resData.xdate);
                        setTimeout(function(){
                            $('.x-success-msg').html(resData.message).removeClass('hide');
                            $saveBtn.text('Saved');
                            setTimeout(function(){
                                disableEnableDetails('enable');

                                if($('#current_xdate_id').val() != ''){
                                    openViewXdateForm(resData.xdate,true);
                                }else{
                                    $('#addNewUserModal').modal('hide');
                                }
                                loadNotes($xdate_Id);
                                setTimeout(function(){
                                    $('.x-error-msg,.x-success-msg').addClass('hide');
                                    $saveBtn.removeClass('disabled').text('Save');
                                },2000);

                            },500);
                        },100);
                    },
                    error: function (jqXHR, exception) {
                        var parsed = JSON.parse(jqXHR.responseText);
                        $('.x-success-msg').addClass('hide');
                        $('.x-error-msg').html('Error:: '+parsed.message).removeClass('hide');
                        $('#add_new_user_btn').removeClass('disabled').text('Save');
                        disableEnableDetails('enable');
                    },
                });

            }else{
                return false;
            }
        });

        //add notes to xdate
        $('#save_note_btn').click(function(){
            var $saveBtn = $(this);
            if($( "#add_note_form" ).valid() && $('#current_xdate_id').val() != ''){
                $('.xnote-error-msg,.xnote-success-msg').addClass('hide');
                $saveBtn.addClass('disabled').text('Saving...');
                $.ajax({
                    method: 'POST',
                    url: site_url+'/xdatenotes/add',
                    data: {notes:  $('#notes_txt').val(), xdate_id : $('#current_xdate_id').val()},
                    success: function (data) {

                        var resData = data;
                        addNoteRow(resData.note,true);

                        $('.add-note-container').addClass('hide');
                        $('#notes_txt').val('');

                        $('.notes-loader-content,.no-notes-content').addClass('hide');
                        $('#all-notes-list').removeClass('hide');
                        $(".notes-scrollbox").scrollTop(0);


                        $saveBtn.removeClass('disabled').text('Save');

                    },
                    error: function (jqXHR, exception) {
                        var parsed = JSON.parse(jqXHR.responseText);
						$('.xnote-success-msg').addClass('hide');
                        $('.xnote-error-msg').html('Error:: '+parsed.message).removeClass('hide');
                        $saveBtn.removeClass('disabled').text('Save');
                    },
                });

            }else{
                return false;
            }
        });

        //detect form changes

        $('#add_edit_xdate_form input, #add_edit_xdate_form select').on('change',function(){
            isChangesXForm = true;
        });

        $('#add_note_btn').click(function(){
            $('.add-note-container textarea').css({height: '32px'});

            $('.add-note-container').removeClass('hide');
            setTimeout(function(){
                $(".notes-scrollbox").scrollTop(0);
                $('#notes_txt').focus();
            },50);
        });

        $('#skip_next_btn').click(function(){

            if($('#current_xdate_id').val() != ''){
                nextXarray.push($('#current_xdate_id').val());
            }
            if($('#xdate').val() != ''){
                skiptoNextDate($('#xdate').val());
            }
        });

         $("body").on("click",function(ev){
            if($(ev.target).closest('#filteration_dropdown_box').length < 1){
                $(".filtertab-group").hide();
            }
        });

         /**** End of add/view xdate ***/

	});

    // request Update request
    $("#request_update").click(function(){
        var xID = $(this).attr('xId');
        //var data = new Array();
        var event_data = $(this);
        var old_text = $(this).text();
        var button_text = "Requesting....";
        $(this).prop('disabled',true);
        $(this).text(button_text);
        $.post(site_url + "/xdate/request_update", {
              'xid': xID
          }, function(response) {
             loadNotes(xID);
             $(event_data).text(old_text);
             $(event_data).prop('disabled',false);
          }).error(function(){
              $(event_data).text(old_text);
              $(event_data).prop('disabled',false);
          });

        
    });

	function reFloatThead(){
		$("#all > table,#live > table,#converted > table,#dead > table").each(function(){
			$('thead th',this).removeAttr('style');
	    	$('tbody td',this).removeAttr('style');
	    	$('thead > tr',this).removeAttr('style');
	    	$(this).removeAttr('style');
		});
		setTimeout(function(){ 
            if(!$('html').hasClass('touch')){
               floatThead();
            } 
        }, 500);
	}
	function floatThead(){
		$("#all > table,#live > table,#converted > table,#dead > table").each(function(){
    		var applyWHFlag = ($(window).width() > 769) ? true : false;
    		if($(this).is(":visible")){
    			var thisObj = this;
    			var tdI = 0;
	        	$('thead th',this).each(function(i){
	        		var innerWidth = $(this).outerWidth();
	        		var innerHeight = $(this).outerHeight();

	        		if(!applyWHFlag){
	        			var innerWidth = (innerWidth);
	        			if(tdI == 0){
	        				//innerWidth = innerWidth - 1;
	        			}else{
	        				//minWidth = minWidth + 1;
	        			}
	        		}

	        		$(this).css({'width':innerWidth+'px','height':innerHeight+'px'});
	        		if(!applyWHFlag){
	        			//innerWidth = innerWidth -1;
	        			$(this).css({'min-width':(innerWidth)+'px'});
	        		}
	        		tdI++;

	        	});

	        	var tdI = 0;
	        	$('tbody td',thisObj).each(function(){
	        		var innerWidth = $(this).outerWidth();
	        		var innerHeight = $(this).outerHeight();

	        		if(!applyWHFlag){
	        			var innerWidth = (innerWidth);
	        			if(tdI == 0){
	        				innerWidth = innerWidth - 1;
	        			}else{
	        				//minWidth = minWidth + 1;
	        			}

	        		}

	        		$(this).css({'width':innerWidth+'px','height':innerHeight+'px'});
	        		if(!applyWHFlag){
	        			if(tdI == 1){
	        				innerWidth = innerWidth;
	        			}else{
	        				innerWidth = innerWidth -1;
	        			}
	        			$(this).css({'min-width':innerWidth+'px'});
	        		}
	        		tdI++;
	        	});

	        	var topPos = $('.nav-tabs').outerHeight();
	        	$('thead > tr',this).css({'position':'absolute','top':0+'px','left':0+'px'});
	        	var innerHeight = $('thead th:first',this).outerHeight();
	        	$(this).css({'margin-top':innerHeight+'px'});
	        }else{
	        	$('thead th',this).removeAttr('style');
	        	$('tbody td',this).removeAttr('style');
	        	$('thead > tr',this).removeAttr('style');
	        	$(this).removeAttr('style');
	        }
        });
	}

	function closeFdropdown(){
		$('#filterdropdown').parent('.dropdown').removeClass("open");
        $('#filterdropdown').next('.dropdown-menu').hide();
	}
    function removeTabFilter(tabID){
        var $activeTab = $('#'+tabID);
        resetFilterForm();

        var $filterForm = $('#xdate_filter_form');
        $('#filterdropdown').removeClass('active');

        var fObj = $filterForm.serializeObject();
        fObj.f_producer = '';
        $activeTab.attr('data-filter',JSON.stringify(fObj));

		disableEnableFilterOption(tabID,fObj);

        if($activeTab.hasClass('hasFilterData')){
            $('.x-row',$activeTab).remove();

            filterFormData(fObj,$activeTab,true);
            $activeTab.removeClass('hasFilterData')
        }
    }


    function filterFormData($filtrObj,$activeTab,hideFilterReset){

        $('.no-xrecord',$activeTab).addClass('hide');
        $('.no-xrecord-filter',$activeTab).addClass('hide');
        $('.load-xrecord',$activeTab).removeClass('hide');

        $filtrObj.c_tab = $activeTab.attr('id');

        $.ajax({
            method: 'POST',
            url: site_url+'/xdate/filter',
            data: $filtrObj,
            dataType: 'json',
            success: function (data) {
                var resData = data;
                if(typeof resData.xdates != undefined && !$.isEmptyObject(resData.xdates)){
                    printFiltersData(resData.xdates,$activeTab);
                }else{
                    $('.no-xrecord',$activeTab).removeClass('hide');

                }
                if(typeof hideFilterReset != undefined && hideFilterReset == true){
                    $('.no-xrecord-filter',$activeTab).addClass('hide');
                }else{
                    $('.no-xrecord-filter',$activeTab).removeClass('hide');
                }
                $('.load-xrecord',$activeTab).addClass('hide');
            },
            error: function (jqXHR, exception) {
                $('.no-xrecord-filter',$activeTab).removeClass('hide');
                $('.load-xrecord',$activeTab).addClass('hide');
            },
        });

    }

    function printFiltersData(dataObj,$activeTab){
        $.each(dataObj,function(i,xdate){
            var rowHtml =  getXrowHtml(xdate);
            var trHtml = ($activeTab.attr('id') == 'all') ? rowHtml['all'] : rowHtml['other'];
            $('table tbody',$activeTab).append(trHtml);
            if(!$('html').hasClass('touch')){
               reFloatThead();
            }
            $("#all > table,#live > table,#converted > table,#dead > table").trigger('update');
        });
    }
    function getXrowHtml(xdate){
        var row = new Array();
        var stringData = JSON.stringify(xdate);
        stringData = stringData.replace(/'/g, "&rsquo;");
        var notesText = (xdate.last_note_txt != '') ? xdate.last_note_txt : '<i>No notes</i>';

        row['all'] = '';

       row['all'] += '<tr class="x-row" data-xdata=\''+stringData+'\'   data-xid="'+xdate.id+'"><td>'+xdate.xdate_txt+'</td><td>'+xdate.producer_txt+'</td><td>'+xdate.policy_type_txt+'</td><td>'+xdate.xname+'</td><td>'+xdate.phone+'</td><td>'+xdate.industry_txt+'</td>';

        row['all'] += '<td>';
        if(xdate.city != ''){
            row['all'] += xdate.city+", ";
        }
        row['all'] += xdate.state;
        row['all'] += '</td>';
        row['all'] += '<td class="last-note-txt">'+notesText+'</td>';
        row['all'] += '<td>'+xdate.status_txt +'</td></tr>';

        row['other'] = '<tr class="x-row" data-xdata=\''+stringData+'\'   data-xid="'+xdate.id+'"><td>'+xdate.xdate_txt+'</td><td>'+xdate.producer_txt+'</td><td>'+xdate.policy_type_txt+'</td><td>'+xdate.xname+'</td><td>'+xdate.xcontact+'</td><td>'+xdate.phone+'</td><td>'+xdate.industry_txt+'</td>';

        row['other'] += '<td>';
        if(xdate.city != ''){
            row['other'] += xdate.city+", ";
        }
        row['other'] += xdate.state +'</td> <td class="last-note-txt">'+notesText+'</td> <td>'+xdate.follow_up_date+'</td></tr>';


        return row;
    }
	function updateDataToTables(xdate){

		var rowHtml =  getXrowHtml(xdate);
        var allTabTR = rowHtml['all'];
        var otherTabTR = rowHtml['other'];

		if($('table tr[data-xid="'+xdate.id+'"]').length > 0){
			$('#all table tr[data-xid="'+xdate.id+'"]').replaceWith(allTabTR);
			if(xdate.status == '0'){
				if($('#live table tr[data-xid="'+xdate.id+'"]').length > 0){
					$('#live table tr[data-xid="'+xdate.id+'"]').replaceWith(otherTabTR);
				}else{
					$('#converted,#dead').find('tr[data-xid="'+xdate.id+'"]').remove();
					$('#live table tbody').prepend(otherTabTR);
				}
			}else if(xdate.status == '1'){
				if($('#converted table tr[data-xid="'+xdate.id+'"]').length > 0){
					$('#converted table tr[data-xid="'+xdate.id+'"]').replaceWith(otherTabTR);
				}else{
					$('#live,#dead').find('tr[data-xid="'+xdate.id+'"]').remove();
					$('#converted table tbody').prepend(otherTabTR);
				}
			}else if(xdate.status == '2'){
				if($('#dead table tr[data-xid="'+xdate.id+'"]').length > 0){
					$('#dead table tr[data-xid="'+xdate.id+'"]').replaceWith(otherTabTR);
				}else{
					$('#live,#converted').find('tr[data-xid="'+xdate.id+'"]').remove();
					$('#dead table tbody').prepend(otherTabTR);
				}
			}
		}else{
			$('#all table tbody').prepend(allTabTR);
			if(xdate.status == '0'){
				$('#live table tbody').prepend(otherTabTR);
			}else if(xdate.status == '1'){
				$('#converted table tbody').prepend(otherTabTR);
			}else if(xdate.status == '2'){
				$('#dead table tbody').prepend(otherTabTR);
			}
		}

		$('#all,#converted,#dead,#live').each(function(){
            if($('.x-row',this).length > 0){
				$('.no-xrecord',this).addClass('hide');
			}else{
				$('.no-xrecord',this).removeClass('hide');
			}
		});
		if(!$('html').hasClass('touch')){
           reFloatThead();
        }

		$("#all > table,#live > table,#converted > table,#dead > table").trigger('update');
	}
    function disableEnableDetails(doWhat){
        if(doWhat == 'disable'){
            $('.xdetail-disabled').removeClass('hide');
        }else if(doWhat == 'enable'){
            $('.xdetail-disabled').addClass('hide');
        }

    }
    function addRemoveValidation(ptype){
        if(ptype == 'commercial'){
            $('#industry').rules('add', { required: true });
            $('#contact').rules('add', { required: true });
        }else if(ptype == 'personal'){
            $('#industry').rules('remove');
            $('#contact').rules('remove');
        }
    }
    function disableEnableOtherFeatures(mode,currentID){
        if(mode == 'add'){
            $('#add_note_btn').attr('disabled','disabled');
            $('#current_xdate_id').val('');
            $('#xaction').val('add');
        }else{
            $('#add_note_btn').removeAttr('disabled');
            $('#current_xdate_id').val(currentID);
            $('#xaction').val('edit');
        }
    }

    function intializeXdatePicker(){
        $('.x_form_date').datetimepicker({
            weekStart: 0,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
    }


    function disableEnableIndustry(ptype){
        $('#industry option[value=""]').prop("selected","selected");

        if(ptype == 'commercial'){
            $('#industry').removeAttr('disabled',true).trigger('change').closest('.detail-field').removeClass('disabled');
        }else if(ptype == 'personal'){
            $('#industry').attr('disabled',true).trigger('change').closest('.detail-field').addClass('disabled');
        }else{
            $('#industry').removeAttr('disabled',true).trigger('change').closest('.detail-field').removeClass('disabled');
        }

    }


    function disableEnableContact(ptype){
        $('#contact').val("");

        if(ptype == 'commercial'){
            $('#contact').removeAttr('disabled',true).closest('.detail-field').removeClass('disabled');
        }else if(ptype == 'personal'){
            $('#contact').attr('disabled',true).closest('.detail-field').addClass('disabled');
        }else{
            $('#contact').removeAttr('disabled',true).closest('.detail-field').removeClass('disabled');
        }
    }

    function changePolicyDropDown(ptype){
        $('#policy_type option').not('[value=""]').remove();

        if(ptype == 'personal'){
            $.each(personPolicy,function(i,data){
                $("#policy_type").append("<option value='"+data.id+"'>"+data.name+"</option>");
            });
        }else if(ptype == 'commercial'){
            $.each(commericalList,function(i,data){
                $("#policy_type").append("<option value='"+data.id+"'>"+data.name+"</option>");
            });
        }
        $('#policy_type option[value=""]').prop('selected');
        $('#policy_type').trigger('change');
    }

    function openAddXdateForm(){
		emptyNotesArea();

        fillUpFormData(xBlankData);
		$('#edit_xdate_btn').addClass('hide');
        $('#addNewUserModal').removeClass('form-view').addClass('form-edit-view').modal('show');

    }

    function openViewXdateForm(xdata,reloadOnlyXdata){

        isChangesXForm = false;

        disableSkipDateBtn();

        $('.detail-field').removeClass('has-error').removeClass('requiredfield');

        if(typeof reloadOnlyXdata == undefined || reloadOnlyXdata != true){
            emptyNotesArea();
        }

        fillUpFormData(xdata);

		if(currentUser.user_type > 2 && currentUser.id != xdata.user_id) {
			$('#edit_xdate_btn').addClass('hide');
		}else{
			$('#edit_xdate_btn').removeClass('hide');
		}
        $('#addNewUserModal').removeClass('form-edit-view').addClass('form-view');
        if(typeof reloadOnlyXdata == undefined || reloadOnlyXdata != true){
            $('#addNewUserModal').modal('show');
            loadNotes(xdata.id);
            $('#add_note_btn').removeAttr('disabled');
        }

    }

    function fillUpFormData(xData){

        if(typeof xData.id != undefined && parseInt(xData.id) > 0){
            $('#current_xdate_id').val(xData.id);
            $('#xaction').val('edit');
        }else{
            $('#current_xdate_id').val('');
            $('#xaction').val('add');
        }

        if(xData.xdate_txt != ''){
            enableSkipDateBtn();
        }
        $("#request_update").attr('xID',xData.id);
        $('#add_new_user_btn').removeClass('disabled').text('Save');
        $('[data-xdate-text]').text(xData.xdate_txt);
        $('#xdate').val(xData.xdate);
        $('.xdate-picker').datetimepicker('update');
        $('#xdate').val(xData.xdate_txt);
        if(typeof xData.xdate_org != undefined){
            $('#xdate_org').val(xData.xdate_org);
        }else{
            $('#xdate_org').val('');
        }


        $('[data-xname-text]').text(xData.xname);
        $('#xname').val(xData.xname);

        $('[data-line-text]').text(xData.line_txt);
        $('#line option[value="'+xData.line+'"]').prop('selected','selected').trigger('change');

        changePolicyDropDown(xData.line_txt.toLowerCase()); //populate policy type dropdown as per selection

        $('[data-policytype-text]').text(xData.policy_type_txt);
        $('#policy_type option[value="'+xData.policy_type+'"]').prop('selected','selected').trigger('change');

        $('[data-industry-text]').text(xData.industry_txt);
        $('#industry option[value="'+xData.industry+'"]').prop('selected','selected').trigger('change');

        $('[data-contact-text]').text(xData.xcontact);
        $('#contact').val(xData.xcontact);

        $('[data-producer-text]').text(xData.producer_txt);
        $('#producer option[value="'+xData.producer+'"]').prop('selected','selected').trigger('change');

        $('[data-phone-text]').text(xData.phone);
        $('#phone').val(xData.phone);

        $('[data-city-text]').text(xData.city);
        $('#city').val(xData.city);

        $('[data-state-text]').text(xData.state);
        $('#state option[value="'+xData.state+'"]').prop('selected','selected').trigger('change');

        $('[data-website-text]').text(xData.website);
        $('#website').val(xData.website);

        $('[data-email-text]').text(xData.email);
        $('#email').val(xData.email);

        $('[data-status-text]').text(xData.status_txt);
        $('#status option[value="'+xData.status+'"]').prop('selected','selected').trigger('change');

        $('[data-followupdate-text]').text(xData.follow_up_date_txt);
        $('#follow_up_date').val(xData.follow_up_date);
        if(xData.follow_up_date_txt != ''){
            $('.fdate-picker').datetimepicker('update');
        }

        $('.detail-field').removeClass('has-error').removeClass('requiredfield');

        $('.x_form_date').datetimepicker('destroy');
        intializeXdatePicker();

        isChangesXForm = false;

    }

    function loadNotes(xID){
        $('.no-notes-content').addClass('hide');
        $('.notes-loader-content').removeClass('hide');
        $('#all-notes-list').addClass('hide').html('');
        $(".notes-scrollbox").css({'width':'100%'});

        $.ajax({
            method: 'GET',
            url: site_url+'/get/notes/'+xID,
            success: function (data) {
                var resData = data;
                var allNotes = resData.notes;

                if(typeof allNotes != undefined && allNotes.length > 0){
                    $.each(allNotes,function(i,notedata){
                        addNoteRow(notedata,false);
                    });
                    $('.notes-loader-content').addClass('hide');
                    $('#all-notes-list').removeClass('hide');
                    $(".notes-scrollbox").scrollTop(0);
                    //setTimeout(function(){ $(window).trigger('resize'); }, 1000);
                }else{
                    $('.no-notes-content').removeClass('hide');
                    $('.notes-loader-content').addClass('hide');
                }

            },
            error: function (jqXHR, exception) {
                $('.no-notes-content').removeClass('hide');
                $('.notes-loader-content').addClass('hide');
            },
        });
    }

    function addNoteRow(note,prependFlg){
        var note_html = '<div class="note-container"><div class="note-header"><div class="media">';

        note_html += '<div class="media-left"><img class="media-object" width="46" height="46" src="'+note.user_data.profile_image+'" alt="'+note.user_data.name+'"></div>';
        note_html += '<div class="media-body media-middle"><h6>'+note.user_data.name+'</h6><div class="note-date-time"><span>'+note.date_txt+'</span><span>'+note.time_txt+'</span></div></div></div></div>';
        note_html += '<div class="note-body"><p>'+note.notes+'</p></div></div>';

        if($('tr[data-xid="'+note.xdate_id+'"]').length){
          $('tr[data-xid="'+note.xdate_id+'"] .last-note-txt').html(note.date_txt+' at '+ note.time_txt);
        }
        if(typeof prependFlg != undefined && prependFlg == true){
            $('#all-notes-list').prepend(note_html);
        }else{
            $('#all-notes-list').append(note_html);
        }
    }

    function emptyNotesArea(){
        $(".notes-scrollbox").scrollTop(0);
        $('.add-note-container').addClass('hide');
        $('.add-note-container textarea').val('');
        $('.add-note-container btn').removeClass('disabled').removeAttr('disabled');
        $('.no-notes-content').addClass('hide');
        $('.notes-loader-content').addClass('hide');
        $('#all-notes-list').addClass('hide').html('');
    }

    function disableEnableFilterOption(currentTab,filterData){

        if(!$.isEmptyObject(filterData)){
            //if(filterData.)
            var filterVal = (typeof filterData.f_policy_type != undefined) ? filterData.f_policy_type : '';
            $('#f_policy_type option[value="'+filterVal+'"]').prop('selected','selected').trigger('change');

            filterVal = (typeof filterData.f_industry != undefined) ? filterData.f_industry : '';
            $('#f_industry option[value="'+filterVal+'"]').prop('selected','selected').trigger('change');

            filterVal = (typeof filterData.f_location != undefined) ? filterData.f_location : '';
            $('#f_location option[value="'+filterVal+'"]').prop('selected','selected').trigger('change');

            filterVal = (typeof filterData.f_producer != undefined) ? filterData.f_producer : '';
            $('#f_producer option[value="'+filterVal+'"]').prop('selected','selected').trigger('change');

            filterVal = (typeof filterData.f_x_from_date != undefined) ? filterData.f_x_from_date : '';
            $('#f_x_from_date').val(filterVal).trigger('change');

            filterVal = (typeof filterData.f_x_to_date != undefined) ? filterData.f_x_to_date : '';
            $('#f_x_to_date').val(filterVal).trigger('change');

            filterVal = (typeof filterData.f_x_quick_date != undefined) ? filterData.f_x_quick_date : '';
            $('#f_x_quick_date option[value="'+filterVal+'"]').prop('selected','selected').trigger('change');

            filterVal = (typeof filterData.f_f_from_date != undefined) ? filterData.f_f_from_date : '';
            $('#f_f_from_date').val(filterVal).trigger('change');

            filterVal = (typeof filterData.f_f_to_date != undefined) ? filterData.f_f_to_date : '';
            $('#f_f_to_date').val(filterVal).trigger('change');

            filterVal = (typeof filterData.f_f_quick_date != undefined) ? filterData.f_f_quick_date : '';
            $('#f_f_quick_date option[value="'+filterVal+'"]').prop('selected','selected').trigger('change');

        }else{
            resetFilterForm();
        }

        if(currentTab == 'all'){
            disableFilterSection('follow');
        }else {
            enableFilterSection('follow');
        }

    }

    function resetFilterForm(){
        document.getElementById("xdate_filter_form").reset();
        $('#filterdropdown').removeClass('active');

        $('#f_policy_type').trigger('change');
        $('#f_industry').trigger('change');
        $('#f_location').trigger('change');
        $('#f_producer').trigger('change');
        $('#f_x_from_date').trigger('change');
        $('#f_x_to_date').trigger('change');
        $('#f_x_quick_date').trigger('change');
        $('#f_f_from_date').trigger('change');
        $('#f_f_to_date').trigger('change');
        $('#f_f_quick_date').trigger('change');

        enableFquickDate();
        enableXquickDate();

    }


    function disableXquickDate(){
        $('.x-quick-date-box').addClass('disabled');
        $('#f_x_quick_date option[value=""]').prop('selected','selected');
    }

    function enableXquickDate(){
        $('.x-quick-date-box').removeClass('disabled');
        $('#f_x_quick_date option[value=""]').prop('selected','selected');
    }
    function disableXfromtoDate(){
        $('.x-fromto').addClass('disabled');
        $('.x-fromto input').val('');
        $('#div_f_x_from_date').datetimepicker('update');
        $('#div_f_x_to_date').datetimepicker('update');
    }

    function disableFfromtoDate(){
        $('.f-fromto').addClass('disabled');
        $('.f-fromto input').val('');
        $('#div_f_f_from_date').datetimepicker('update');
        $('#div_f_f_to_date').datetimepicker('update');
    }

    function disableFquickDate(){
        $('.f-quick-date-box').addClass('disabled');
        $('#f_f_quick_date option[value=""]').prop('selected','selected');
    }

    function enableFquickDate(){
        $('.f-quick-date-box').removeClass('disabled');
        $('#f_f_quick_date option[value=""]').prop('selected','selected');
    }

    function enableXfromtoDate(){
        $('.x-fromto').removeClass('disabled');
    }

    function enableFfromtoDate(){
       $('.f-fromto').removeClass('disabled');
    }

    function disableFindustry(){
        $('.industry-fbox').addClass('disabled');
        $('#f_industry option[value=""]').prop('selected','selected');
    }

    function enableFindustry(){
        $('.industry-fbox').removeClass('disabled');
    }

    function checkAndEnableFilterBtn(){

        var ptypeOpt = $('#f_policy_type option:selected');
        var industryOpt = $('#f_industry option:selected');
        var locationOpt = $('#f_location option:selected');

        var enableFlg = false;
        if(ptypeOpt.attr('data-type') == 'commercial-opt'){
            if(ptypeOpt.val() != '' && industryOpt.val() != '' && locationOpt.val() != ''){
                enableFlg = true;
            }
        }else{
            if(ptypeOpt.val() != '' && locationOpt.val() != ''){
                enableFlg = true;
            }
        }

        if(enableFlg){
            $('#x_filter_btn').removeAttr('disabled').removeClass('btn-default').addClass('btn-success');
            $('.x_clear_btn').removeAttr('disabled').removeClass('btn-default').addClass('btn-danger');
        }else{
            $('#x_filter_btn').prop('disabled','disabled').removeClass('btn-success').addClass('btn-default');
            $('.x_clear_btn').prop('disabled','disabled').removeClass('btn-danger').addClass('btn-default');
        }

    }

    function disableFilterSection(secName){
        if(secName == 'follow'){
            $('#f_formto_filter h5').addClass('disabled');
            $('#f_formto_filter .f-fromto').addClass('disabled');
            $('#f_formto_filter .f-quick-date-box').addClass('disabled');
            $('#f_formto_filter input').val('').trigger('change');
            $('#f_formto_filter select option[value=""]').prop('selected');
        }else{

        }

    }

    function enableFilterSection(secName){
        if(secName == 'follow'){
            $('#f_formto_filter h5').removeClass('disabled');
            $('#f_formto_filter .f-fromto').removeClass('disabled');
            $('#f_formto_filter .f-quick-date-box').removeClass('disabled');
        }else{

        }

    }

    function hideXModal(modalID){
        $('#'+modalID).modal('hide');
        setTimeout(function(){
            if($('#addNewUserModal').is(":visible")){
                $('body').addClass('modal-open');
            }
        },500);
    }

    function disableSkipDateBtn(){
        $('#skip_next_btn').text('skip to next x-date').prop('disabled','disabled');
    }
    function enableSkipDateBtn(){
        $('#skip_next_btn').text('skip to next x-date').removeAttr('disabled');
    }
    function loadingSkipDateBtn(){
        $('.xfulldetail-disabled').removeClass('hide');
        //$('#skip_next_btn').text('Loading... next x-date').prop('disabled','disabled');
    }
    function SaveXformChanges(){
        hideXModal('x_save_discard_modal');
        $('#save_xdate_btn').trigger('click');
    }
    function DiscardXformChanges(){
        hideXModal('x_save_discard_modal');
        skiptoNextDate($('#xdate').val(),true);
    }

    function skiptoNextDate(dateTxt,forceOpen){

        if(isChangesXForm == true && (typeof forceOpen == 'undefined' || forceOpen  != true)){
            $('#x_save_discard_modal').modal('show');
            return false;
        }

        if(dateTxt != ''){

            loadingSkipDateBtn();
            $.ajax({
                method: 'POST',
                url: site_url+'/xdate/skiptonext',
                data: {current_x_date : dateTxt, xids : nextXarray},
                dataType:'json',
                success: function (data) {
                    var resData = data;
                    console.log(resData.xdate.id);
                    if(!$.isEmptyObject(resData.xdate) && typeof resData.xdate.id != undefined && resData.xdate.id != ''){
                        nextXarray.push(resData.xdate.id);
                        openViewXdateForm(resData.xdate);

                    }else{
                        disableSkipDateBtn();
                        $('#no_next_date_found').modal('show');
                    }

                    setTimeout(function(){
                        $('.xfulldetail-disabled').addClass('hide');
                    },200);

                    //openViewXdateForm(resData.xdate);
                },
                error: function (jqXHR, exception) {
                    disableSkipDateBtn();
                    $('.xfulldetail-disabled').addClass('hide');
                },
            });

        }
    }

function xautocomepleteClick(thisVal){
    //console.log(thisVal);
    if(thisVal != ''){
        openViewXdateForm(thisVal);
    }
}
