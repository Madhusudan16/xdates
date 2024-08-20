   //this function set width of table column
   function reFloatThead() {
       $("#all > table,#active > table,#inactive > table").each(function() {

           $('thead th', this).removeAttr('style');
           $('tbody td', this).removeAttr('style');
           $('thead > tr', this).removeAttr('style');
           $(this).removeAttr('style');
       });
       if(!$('html').hasClass('touch')){
          setTimeout(function() {
            floatThead();
          }, 500);
        }
       
   }

   function floatThead() {
      
       $("#all > table,#active > table,#inactive > table").each(function() {
           var applyWHFlag = ($(window).width() > 769) ? true : false;

           if ($(this).is(":visible")) {
               var thisObj = this;
               var tdI = 0;
               $('thead th', this).each(function(i) {
                   var innerWidth = $(this).outerWidth();
                   var innerHeight = $(this).outerHeight();

                   if (!applyWHFlag) {
                       var innerWidth = (innerWidth);
                       if (tdI == 0) {
                           //innerWidth = innerWidth - 1;
                       } else {
                           //minWidth = minWidth + 1;
                       }
                   }

                   $(this).css({
                       'width': innerWidth + 'px',
                       'height': innerHeight + 'px'
                   });
                   if (!applyWHFlag) {
                       //innerWidth = innerWidth -1;
                       $(this).css({
                           'min-width': (innerWidth) + 'px'
                       });
                   }
                   tdI++;

               });

               var tdI = 0;
               $('tbody td', thisObj).each(function() {
                   var innerWidth = $(this).outerWidth();
                   var innerHeight = $(this).outerHeight();
                   if (!applyWHFlag) {
                       var innerWidth = (innerWidth);
                       if (tdI == 0) {
                          // innerWidth = innerWidth - 1;
                       } else {
                           //minWidth = minWidth + 1;
                       }

                   }

                   $(this).css({
                       'width': innerWidth + 'px',
                       'height': innerHeight + 'px'
                   });
                   if (!applyWHFlag) {
                       if (tdI == 1) {
                           innerWidth = innerWidth;
                       } else {
                           innerWidth = innerWidth - 1;
                       }
                       $(this).css({
                           'min-width': innerWidth + 'px'
                       });
                   }
                   tdI++;
               });

               var topPos = $('.nav-tabs').outerHeight();
               
               $('thead > tr', this).css({
                   'position': 'absolute',
                   'top': 0 + 'px',
                   'left': 0 + 'px'
               });
               var innerHeight = $('thead th:first', this).outerHeight();
               $(this).css({
                   'margin-top': innerHeight + 'px'
               });
           } else {
               $('thead th', this).removeAttr('style');
               $('tbody td', this).removeAttr('style');
               $('thead > tr', this).removeAttr('style');
               $(this).removeAttr('style');
           }
       });
   }


   // When the document is ready
   jQuery(document).ready(function($) {
       $.ajaxSetup({
           headers: {
               'X-CSRF-Token': $('meta[name=_token]').attr('content')
           }
       });
       // default filter button disabled
       $(".filter").prop('disabled', true);

       // click on filter by button
       $('#filterdropdown').click(function() {
           $('.filtertab-group').toggle();
           var filterActive = window.location.hash + "-filter";
           if (!$(filterActive).hasClass('hide')) {
               $("#filterdropdown").addClass('active');
           } else {
               $("#filterdropdown").toggleClass('active');
           }
       });

       // when filter box is open and click on out side box then close box
       $("body").on("click", function(ev) {
            if ($(ev.target).closest('#filteration_dropdown_box').length < 1) {
                $(".filtertab-group").hide();
                $("#filterdropdown").removeClass('active');
                if(window.location.hash == null || window.location.hash == '' ) {
                    filterActive = "#all-filter";
                } else {
                    var filterActive = window.location.hash + "-filter";
                }
                //console.log(filterActive);
                if (!$(filterActive).hasClass('hide')) {
                    $("#filterdropdown").addClass('active');
                } else {
                    if (!$(ev.target).closest('.no-filter-click').length < 1) {
                      $("#filterdropdown").removeClass('active');
                    } else {
                      //console.log('fsf');
                      //$("#filterdropdown").toggleClass('active');
                    }
                }
            }
       });


       setDefaultSetting();

       $('.form_date').datetimepicker({
           weekStart: 0,
           todayBtn: 1,
           autoclose: 1,
           todayHighlight: 1,
           startView: 2,
           minView: 2,
           showOn: "none",
           forceParse: 0
       });

       var hScroll = ($(window).width() < 768) ? true : false;
       if(!$('html').hasClass('touch')){
           $('.scrollbox').enscroll({
               horizontalScrolling: hScroll,
               verticalTrackClass: 'track',
               verticalHandleClass: 'handle',
               clickTrackToScroll: true,
               minScrollbarLength: '50',
               scrollUpButtonClass: 'scroll-up',
               scrollDownButtonClass: 'scroll-down',
           });
       }

       $(window).resize(function() {
           $("#all > table,#active > table,#inactive > table").each(function() {
               $('thead th', this).removeAttr('style');
               $('tbody td', this).removeAttr('style');
               $('thead > tr', this).removeAttr('style');
               $(this).removeAttr('style');
           });
           var hScroll = ($(window).width() < 768) ? true : false;
           $(".scrollbox.tbl-full").width('100%');
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

        if(!$('html').hasClass('touch')){
          floatThead();
         }
       $(window).load(function() {
            if(!$('html').hasClass('touch')){
               floatThead();
            }
       });

       $('.scrollbox').scroll(function(event) {
           var sc = $(this).scrollTop();
           var scL = $(this).scrollLeft();
           if (scL == 0) {
               $('thead > tr', this).css({
                   'left': 0 + 'px ' 
               });
           } else {
               $('thead > tr', this).css({
                   'left': -(scL) + 'px'
               });
           };
       });

       /*$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
           setTimeout(function() {
              floatThead();
           }, 500);
       });*/

       $('table').next('div').css('height', '0px');

       // apply table sorter
       $(".changeOrder").tablesorter({
           cssAsc: 'headerSortDown',
           cssDesc: 'headerSortUp',
           headers: {
               '.no-sort': {
                   sorter: false
               }
           }
       });

       //on click table row then redirect user details page
       $('body').on("click", ".clickable-row", function() {
            window.document.location = $(this).data("href");
       });

       // disabled/enabled filter box input
       $("#signUpfrom,#signUpTo,#signUpQuickDate,#trialFrom,#trialTo,#trialQuick,#accountFrom,#accountTo,#accountQuick,#creditFrom,#creditTo,#creditQuick").change(function() {
           if (($("#signUpfrom").val() != "" && $("#signUpTo").val() != "") || $("#signUpQuickDate").val() != "" || ($("#trialFrom").val() != "" && $("#trialTo").val() != "") || $("#trialQuick").val() != "" || ($("#accountFrom").val() != "" && $("#accountTo").val() != "") || $("#accountQuick").val() != "" || ($("#creditFrom").val() != "" && $("#creditTo").val() != "") || $("#creditQuick").val() != "") {
               //$(".filter").addClass('btn-success');
               $(".filter").prop('disabled', false);
           } else {
               $(".filter").prop('disabled', true);
           }
       });

       // this event perform when change tab
       $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
           var $target = $(e.target);
           var currentTabID = $target.attr('href').replace('#', '');
           window.location.hash = '#' + currentTabID;
           var filterActive = currentTabID + "-filter";
           if (!$('#' + filterActive).hasClass('hide')) {
               $("#filterdropdown").addClass('active');
           } else {
               $("#filterdropdown").removeClass('active');
           }

           if (currentTabID == 'active') {
               $(".account-exp").addClass('disabled');
               $(".account-exp").find(':input').prop('disabled', true);
           } else {
               if ($(".account-exp").hasClass('disabled')) {
                   $(".account-exp").removeClass('disabled');
                   $(".account-exp").find(':input').prop('disabled', false);
               }
           }
           if(!$('html').hasClass('touch')){
              setTimeout(function() {
                floatThead();
              }, 500);
            }
           /*setTimeout(function() {
               floatThead();
           }, 500);*/
           var tab_id = window.location.hash;
           if (tab_id == "") {
               tab_id = "#all";
           }
           $filterData = JSON.parse($(tab_id).attr('data-filter'));
           disableEnableFilterOption('', $filterData);
           enabledDisabledButton();
       });

       $("#signUpfrom,#signUpTo,#signUpQuickDate,#trialFrom,#trialTo,#trialQuick,#accountFrom,#accountTo,#accountQuick,#creditFrom,#creditTo,#creditQuick").on('change', function() {
           disabledInput();
           enabledInput();
           enabledDisabledButton();
       });

   });

   function disableEnableFilterOption(currentTab, filterData) {

       if (!$.isEmptyObject(filterData) && filterData != 1) {
           //if(filterData.)
           filterVal = (typeof filterData.sign_up_from != undefined) ? filterData.sign_up_from : '';
           $('#signUpfrom').val(filterVal).trigger('change');

           filterVal = (typeof filterData.signup_to != undefined) ? filterData.signup_to : '';
           $('#signUpTo').val(filterVal).trigger('change');

           filterVal = (typeof filterData.sign_up_quick != undefined) ? filterData.sign_up_quick : '';
           $('#signUpQuickDate option[value="' + filterVal + '"]').prop('selected', 'selected').trigger('change');

           filterVal = (typeof filterData.trial_from != undefined) ? filterData.trial_from : '';
           $('#trialFrom').val(filterVal).trigger('change');

           filterVal = (typeof filterData.trial_to != undefined) ? filterData.trial_to : '';
           $('#trialTo').val(filterVal).trigger('change');

           filterVal = (typeof filterData.trail_quick != undefined) ? filterData.trail_quick : '';
           $('#trialQuick option[value="' + filterVal + '"]').prop('selected', 'selected').trigger('change');

           filterVal = (typeof filterData.account_from != undefined) ? filterData.account_from : '';
           $('#accountFrom').val(filterVal).trigger('change');

           filterVal = (typeof filterData.account_to != undefined) ? filterData.account_to : '';
           $('#accountTo').val(filterVal).trigger('change');

           filterVal = (typeof filterData.account_quick != undefined) ? filterData.account_quick : '';
           $('#accountQuick option[value="' + filterVal + '"]').prop('selected', 'selected').trigger('change');

           filterVal = (typeof filterData.credit_from != undefined) ? filterData.credit_from : '';
           $('#creditFrom').val(filterVal).trigger('change');

           filterVal = (typeof filterData.credit_to != undefined) ? filterData.credit_to : '';
           $('#creditTo').val(filterVal).trigger('change');

           filterVal = (typeof filterData.credit_quick != undefined) ? filterData.credit_quick : '';
           $('#creditQuick option[value="' + filterVal + '"]').prop('selected', 'selected').trigger('change');
       } else {
           resetFilterForm();
       }
       disabledInput();
   }

   function resetFilterForm() {
       document.getElementById("filterForm").reset();
       //enabledDisabledButton();
       $(".clear-all").addClass('btn-default');
       $(".filter").addClass('btn-default');
       $('#filterdropdown').removeClass('active');

       $('#signUpfrom').trigger('change');
       $('#signUpTo').trigger('change');
       $('#trialFrom').trigger('change');
       $('#trialTo').trigger('change');
       $('#signUpQuickDate').trigger('change');
       $('#trialQuick').trigger('change');
       $('#accountFrom').trigger('change');
       $('#accountTo').trigger('change');
       $('#accountQuick').trigger('change');
       $('#creditFrom').trigger('change');
       $('#creditTo').trigger('change');
       $('#creditQuick').trigger('change');
       enabledInput();
       $("#filterdropdown").removeClass('active');
   }

   function disabledInput() {

       if ($('#signUpfrom').val() != '' || $('#signUpTo').val() != '') {
           $('#signUpQuickDate').addClass('disabled');

       }
       if ($('#trialFrom').val() != '' || $('#trialTo').val() != '') {
           $('#trialQuick').addClass('disabled');

       }

       if ($('#accountFrom').val() != '' || $('#accountTo').val() != '') {
           $('#accountQuick').addClass('disabled');

       }
       if ($('#creditFrom').val() != '' || $('#creditTo').val() != '') {
           $('#creditQuick').addClass('disabled');

       }
       if ($('#signUpQuickDate').val() != '') {
           $(".sign-up-filter").find('.filtertab-content-fromto').addClass('disabled');

       }
       if ($('#trialQuick').val() != '') {
           $(".trial-filter").find('.filtertab-content-fromto').addClass('disabled');

       }
       if ($('#accountQuick').val() != '') {
           $(".account-filter").find('.filtertab-content-fromto').addClass('disabled');

       }
       if ($('#creditQuick').val() != '') {
           $(".credit-card-filter").find('.filtertab-content-fromto').addClass('disabled');

       }
   }

   function enabledInput() {
       if ($('#signUpfrom').val() == '' && $('#signUpTo').val() == '') {
           $('#signUpQuickDate').removeClass('disabled');
       }
       if ($('#trialFrom').val() == '' && $('#trialTo').val() == '') {
           $('#trialQuick').removeClass('disabled');
       }
       if ($('#accountFrom').val() == '' && $('#accountTo').val() == '') {
           $('#accountQuick').removeClass('disabled');
       }
       if ($('#creditFrom').val() == '' && $('#creditTo').val() == '') {
           $('#creditQuick').removeClass('disabled');
       }
       if ($('#signUpQuickDate').val() == '') {
           $(".sign-up-filter").find('.filtertab-content-fromto').removeClass('disabled');
       }
       if ($('#trialQuick').val() == '') {
           $(".trial-filter").find('.filtertab-content-fromto').removeClass('disabled');
       }
       if ($('#accountQuick').val() == '') {
           $(".account-filter").find('.filtertab-content-fromto').removeClass('disabled');
       }
       if ($('#creditQuick').val() == '') {
           $(".credit-card-filter").find('.filtertab-content-fromto').removeClass('disabled');
       }
   }

   function enabledDisabledButton() {
       if (($('#signUpfrom').val() != '' && $('#signUpTo').val() != '') || ($('#trialFrom').val() != '' && $('#trialTo').val() != '') || ($('#accountFrom').val() != '' && $('#accountTo').val() != '') || ($('#creditFrom').val() != '' && $('#creditTo').val() != '') || ($('#signUpQuickDate').val() != '') || ($('#trialQuick').val() != '') || ($('#accountQuick').val() != '') || ($('#creditQuick').val() != '')) {
           $('.filter').addClass('btn-success');
           $('.clear-all').removeClass('btn-default');
           $('.filter').removeClass('btn-default');
           $('.clear-all').addClass('btn-danger');
           $('.filter').prop('disabled', false);
           $('.clear-all').prop('disabled', false);
       } else {
           $('.filter').prop('disabled', true);
           $('.clear-all').prop('disabled', true);
           $('.clear-all').removeClass('btn-danger');
           $('.filter').removeClass('btn-success');
           $('.clear-all').addClass('btn-default');
           $('.filter').addClass('btn-default');
       }
   }

   // this function check  filter button active or not
   function setDefaultSetting() {
       var currentLink = window.location.hash;
       if (currentLink == "") {
           currentLink = "#all"
       }
       var filterActive = currentLink + "-filter";
       if (!$(filterActive).hasClass('hide')) {
           $("#filterdropdown").addClass('active');
       } else {
           $("#filterdropdown").removeClass('active');
       }

       if (window.location.hash != null && window.location.hash != "" && window.location.hash != "undefined") {
           $(".tab-btn a[href=" + window.location.hash + "]").tab('show'); // show tab according hash link in url
           if (window.location.hash == '#active') {
               $(".account-exp").addClass('disabled');
               $(".account-exp").find(':input').prop('disabled', true);
           }
           var tab_id = window.location.hash;
           $filterData = JSON.parse($(tab_id).attr('data-filter'));
       } else {
           $filterData = JSON.parse($("#all").attr('data-filter'));
       }
       disableEnableFilterOption(tab_id, $filterData);
       enabledDisabledButton();
   }
