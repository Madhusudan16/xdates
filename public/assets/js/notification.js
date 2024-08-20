$(document).ready(function() {
    

    if ($("#thenkyou").is(':visible')) {
        setTimeout(function() {
            window.location.href = site_url + '/notification';
        }, 2000);
    }
    $(".confirm-save-btn").prop('disabled',true);
    $('#save_email').prop('disabled', true);
    $('#save_edited_data').prop('disabled', true);
    $("#save_phone").prop('disabled', true);
    $(".save_edited_phone").prop('disabled', true);

    $("#save_email").click(function() {
        $(".loader").show();
        var email = $("#email_add").val();
        var is_edit = 0;
        var element = $(this);
        $(element).text('Continuing');
        $.post(site_url + "/add-email", {email: email,is_edit: is_edit }, function(result) {
                if (result.id != null && result.id != 'undefined') {
                    var insertId = result.id;
                    addElement(email, insertId);
                    $("#add_email").modal('hide');
                    $(".resendmail").attr('dataid',insertId);
                    $(".resendmail").attr('email',email);
                    $("#confirm_email").modal('show');
                                    }
                $(element).text('Continue');
                $(".loader").hide();
            })
            .fail(function(response) {
                $(element).text('Continue');
                var msg = $.parseJSON(response.responseText);
                $prev_msg = $(".error").text();
                $(".error").text("");
                $(this).prop('disabled',true);
                $(".loader").hide();
                $(".error").text(msg.error_msg);
                console.log($(element).text());
            });
        });
        $(".selectedCountry").change(function() {
            $(".save_edited_phone").prop('disabled', false);
            $(".save_edited_phone").addClass('feedback-success');
        });
    
    $("#phone_add,#edit_phone,#edit_phone_save").on('change blur',function() {
        console.log($(this).val().length);

        if ($(this).val().length == 10) {
            var phones = [{
                "mask": "(###) ###-####"
            }, {
                "mask": "(###) ###-#####"
            }];
            $(this).inputmask({
                mask: phones,
                greedy: false,
                definitions: {
                    '#': {
                        validator: "[0-9]",
                        cardinality: 1
                    }
                }
            });
        }
    });

    /*$("#phone_add,#edit_phone,#edit_phone_save").on('focus',function() {
        var phones = [{
                "mask": ""
            }, {
                "mask": ""
            }];
        console.log($(this).inputmask({mask:phones,greedy: false }));
    });
*/
    $("#save_phone").click(function() {
        $(".loader").show();
        var element = $(this);
        var phone = $("#phone_add").val();
        $(this).val(phone.trim());
        var countryCode = $(".countryCode").val();
        var is_edit = 0;
        var formData = $("#addPhoneForm").serialize();
        $.post(site_url + "/add-phone", {
                data: formData,
                is_edit: is_edit
            }, function(result) {
                if (result.id != null && result.id != 'undefined') {
                    var insertedId = result.id;
                    $(".no-record-found").hide();
                    addPhoneElement(phone, countryCode, insertedId);
                    $("#add_phone").modal('hide');
                    
                    $(".resend_mobile_code").attr('id',result.id);
                    $("#confirm_code").modal({
                        backdrop: 'static',
                        keyboard: true
                    });
                    $(".recent-phone").text(countryCode +" "+ phone);
                } else if (result.msg != null && result.msg != 'undefined') {
                    var msg = result.msg;
                    $(".error").show();
                    $(".error").html(msg);
                    $.setTimeout(function() {
                        $("#add_phone").modal('hide');
                       
                        $("#confirm_code").modal({
                            backdrop: 'static',
                            keyboard: true
                        });
                    }, 2000);
                    $(".recent-phone").text(countryCode +" "+ phone);
                }
                $(".loader").hide();
            })
            .fail(function(response) {
                var msg = $.parseJSON(response.responseText);
                $prev_msg = $(".error").text();
                console.log(msg.error_msg)
                $(".error").text("");
                $(".error").show();
                $(".loader").hide();
                $(element).removeClass('feedback-success');
                $(element).prop('disabled',true);
                $(".error").text(msg.error_msg);
                /*setTimeout(function() {
                    $(".error").text("");
                    $(".error").hide();
                    $(".error").text($prev_msg);
                }, 5000);*/
            });
    });
    $("#save_edited_data").click(function() {
        $(".loader").show();
        var email = $("#edit_email").val();
        var id = $(".email-id").val();
        var is_edit = 1;
        $.post(site_url + "/add-email", { email: email, is_edit: is_edit,id: id}, function(result) { 
            if (result.msg != null && result.msg != 'undefined') {
                $("#needConfirmationEmail").val(email);
                $(".email-" + id).text(email);
                $(".edited_email_id").val(id);
                $(".resendmail").attr('dataid',id);
                $(".resendmail").attr('email',email);
                $("#edit_details").modal('hide');
                $("#confirm_email").modal('show');
                //$("#edit_details_save").modal('show');
            }
            $(".loader").hide();
            })
            .fail(function(response) {
                $(".loader").hide();
                var msg = $.parseJSON(response.responseText);
                $prev_msg = $(".error").text();
                $(".error").html("");
                //$(this).attr('disabled',true);
                $(".error").html(msg.error_msg);
               /* setTimeout(function() {
                    $(".error").html("");
                    $(".error").text($prev_msg);
                }, 1000);*/
            });
    });
    $("#removeEmail").click(function() {
        $(".loader").show();
        var id = $(".deletedId").val();
        $.post(site_url + "/notification/delete", { id: id }, function(result) {
            if (result.success_msg != null && result.success_msg != 'undefined') {
                $(".deleted_li").remove();
                
                $('#remove_email').modal('hide');
            } 
            $(".loader").hide();
        })
        .fail(function(response) {
            $(".loader").hide();
            $("li").find('deleted_li').removeClass('deleted_li');
        });
    });
    $(".deleteNumber").click(function() {
        $(".loader").show();
        var id = $(".remove-phone-number").val();
        $.post(site_url + "/notification/delete", { id: id}, function(result) {
            if (result.success_msg != null && result.success_msg != 'undefined') {
                $(".deleted_li").remove();
                $("#remove_phone").modal('hide');
                console.log($(".phone_list li").size());
                if($(".phone_list li").size() == 1) {
                    $(".no-record-found").removeClass('hide');
                    $(".no-record-found").addClass('show');
                }
            }
             $(".loader").hide();
        })
        .fail(function(response) {
             $(".loader").hide();
             $("li").find('deleted_li').removeClass('deleted_li');
        });
    });
    $("#email_add").on('keyup blur change', function() {
        if ($(this).closest('.form-group').hasClass('has-error')) {
            $("#save_email").removeClass('btn-success');
            $("#save_email").addClass('disable-btn');
            $('#save_email').prop('disabled', true);

        } else {
            if (IsEmail($(this).val())) {
                $("#save_email").removeClass('disable-btn');
                $("#save_email").addClass('btn-success');
                $('#save_email').prop('disabled', false);
            } else {
                $("#save_email").removeClass('btn-success');
                $("#save_email").addClass('disable-btn');
                $('#save_email').prop('disabled', true);
            }
        }
    });
    $("#phone_add").on('keyup blur change', function(e) {

        var count_number = ($(this).val().match(/\d/g) || []).length;
        //var setValue = phone.trim().replace(/[^0-9]+/gi, '');
        //$(this).val(setValue);
       
        if(e.which === 13) {
            if(count_number == 10) {
                $('#save_phone').click();
            }
        }
        if (count_number == 10) {
            $("#save_phone").prop('disabled', false);
            $("#save_phone").addClass('feedback-success');
        } else {
            $("#save_phone").prop('disabled', true);
            $("#save_phone").removeClass('feedback-success');
        }
    });
    $("#verificationCode").on('keyup blur change', function() {
        if ($(this).val().length > 0) {
            $("#checkCode").addClass('feedback-success');
            $("#checkCode").prop('disabled',false);
        } else {
            $("#checkCode").removeClass('feedback-success');
            $("#checkCode").prop('disabled',true);
        }
    });
    $("#edit_phone").on('keyup change', function() {
        var count_number = ($(this).val().match(/\d/g) || []).length;
        console.log(count_number);
        $country = $(".selectedCountry").val();
        console.log($country);
        if (count_number == 10 ) {
            if(typeof $country != 'undefined' && $country) {
                $(".save_edited_phone").prop('disabled', false);
                $(".save_edited_phone").addClass('feedback-success');
            } else {
                $(".save_edited_phone").prop('disabled', true);
                $(".save_edited_phone").removeClass('feedback-success');
            }
        } else {
            $(".save_edited_phone").prop('disabled', true);
            $(".save_edited_phone").removeClass('feedback-success');
        }
    });
 
     $("#edit_phone_save,.notification_receive,.edit_country_code").on('keyup change', function() {
        var count_number = ($("#edit_phone_save").val().match(/\d/g) || []).length;
        console.log(count_number);
        $country = $(".edit_country_code").val();
        console.log($country);
        if (count_number == 10 ) {
            if(typeof $country != 'undefined' && $country) {
               $(".confirm-save-btn").prop('disabled', false);
                    $(".confirm-save-btn").addClass('feedback-success');
            } else {
                $(".confirm-save-btn").prop('disabled', true);
                $(".confirm-save-btn").removeClass('feedback-success');
            }
        } else {
            $(".confirm-save-btn").prop('disabled', true);
                $(".confirm-save-btn").removeClass('feedback-success');
           
        }
    });
    
    $(".save_edited_phone").click(function() {
        if ($(this).hasClass('feedback-success')) {
            $("#confirm_code").modal('hide');
            //$("#thanks").modal('show');
        }
    });
    $("#edit_email").on('keyup', function() {
        if ($(this).closest('.form-group').hasClass('has-error')) {
            $("#save_edited_data").removeClass('btn-success');
            $("#save_edited_data").addClass('disable-btn');
            $('#save_edited_data').prop('disabled', true);

        } else {
            if (IsEmail($(this).val())) {
                $("#save_edited_data").removeClass('disable-btn');
                $("#save_edited_data").addClass('btn-success');
                $('#save_edited_data').prop('disabled', false);
            } else {
                $("#save_edited_data").removeClass('btn-success');
                $("#save_edited_data").addClass('disable-btn');
                $('#save_edited_data').prop('disabled', true);
            }
        }
    });
    $(".resendmail").click(function(e) {
        e.preventDefault();
        $(".loader").show();
        var id = $(this).attr('dataId');
        var email = $(this).attr('email');
        $prev_text = $(".resendmail").html();
        $(".resendmail").html('sending...');
        $.post(site_url + "/notification/resend-mail", {
                id: id,
                email: email
            }, function(result) {
                if (result.msg != null && result.msg != 'undefined') {
                    
                    //$("#edit_details").modal('hide');
                    //setTimeout(function() {
                        $(".resendmail").html($prev_text);
                        //$prev_text = $(".confirm-email-info").text();
                        //$(".confirm-email-info").text('')
                        //$(".confirm-email-info").text(result.msg);
                        //$("#edit_details").modal('show');
                   // }, 1000);
                }
                $(".loader").hide();
            })
            .fail(function(response) {
                $(".loader").hide();
                var msg = $.parseJSON(response.responseText);
                $prev_msg = $(".error").text();
                $(".error").html("");
                $(".error").html(msg.error_msg);
                /*setTimeout(function() {
                    $(".error").html("");
                    $(".error").text($prev_msg);
                }, 1000);*/
            });
    });
    $("#FrequencyEmail").on('focus',function() {
        $prev_selected_value = $(this).val();
    }); 
    $("#FrequencyEmail").change(function() {
        $(".loader").show();
        var frequency = $("#FrequencyEmail").val();
        var via = 1;
        $.post(site_url + "/notification/changeFrequency", {
            frequency: frequency,
            via: via
        }, function(result) {
            $(".loader").hide();
            console.log('success');
        }).fail(function(response) {
            $(".loader").hide();
            $("#FrequencyEmail").val($prev_selected_value);
        });
    });
    $("#FrequencyPhone").on('focus',function() {
        $prev_selected_value = $(this).val();
    });
    $("#FrequencyPhone").change(function() {
        $(".loader").show();
        var frequency = $("#FrequencyPhone").val();
        var via = 2;
        $.post(site_url + "/notification/changeFrequency", {
            frequency: frequency,
            via: via
        }, function(result) {
            $(".loader").hide();
            console.log('success');
        }).fail(function(response) {
            $(".loader").hide();
          $("#FrequencyPhone").val($prev_selected_value);
        });
    });
    /*$(".save").click(function() {
        var element = $(this);
        var oldText = $(this).text();
        var setText = '';
        if (oldText == "Continue") {
            setText = "Continuing...";
        } else {
            setText = "Saving...";
        }
        $(this).text(setText);
        setTimeout(function() {
            $(element).text(oldText);
            if ($(element).hasClass('btn-success')) {
                $(element).removeClass('btn-success');
            }
            if ($(element).hasClass('feedback-success')) {
                $(element).removeClass('feedback-success');
            }
            $(element).prop('disabled',true);
        }, 2000);
    });
*/    $("#followUpNotificationEmail").on('focus',function() {
        $prev_selected_value = $(this).val();
    });
    $("#followUpNotificationEmail").change(function() {
        $(".loader").show();
        var frequency = $("#followUpNotificationEmail").val();
        var via = 1;
        var followup = 1;
        $.post(site_url + "/notification/changeFrequency", {
            frequency: frequency,
            via: via,
            followup: followup
        }, function(result) {
            $(".loader").hide();
            
        }).fail(function(response) {
            $(".loader").hide();
          $("#followUpNotificationEmail").val($prev_selected_value);
        });;
    });
    $("#followUpNotificationPhone").on('focus',function() {
        $prev_selected_value = $(this).val();
    });
    $("#followUpNotificationPhone").change(function() {
        $(".loader").show();
        var frequency = $("#followUpNotificationPhone").val();
        var via = 2;
        var followup = 1;
        $.post(site_url + "/notification/changeFrequency", {
            frequency: frequency,
            via: via,
            followup: followup
        }, function(result) {
            if (result.success_msg != null && result.success_msg != 'undefined') {
                console.log('success');
            }
            $(".loader").hide();
        }).fail(function(response) {
          $("#followUpNotificationPhone").val($prev_selected_value);
          $(".loader").hide();
        });
    });

    $(".add_new_phone_number").click(function(){
        $(".error").text('');
        $("#addPhoneForm").trigger("reset");
        $("#save_phone").removeClass('feedback-success');
        $("#save_phone").prop('disabled',true);
    });
    $(".activeStatus-primary").change(function(){
       $(this).prop('checked',true);
    });
    
    $('.modal').on('shown.bs.modal', function() {
        if($("body").hasClass('modal-open')) {
            $(".loader").css('background-color','transparent');
            $(".save").prop('disabled',true);
            $(".save").removeClass('feedback-success');
        }
    });
    
    $('#confirm_email').on('hidden.bs.modal', function () {
        $(".resendmail").attr('email','');
        $(".resendmail").attr('dataid','');
    });
    $('#add_phone').on('hidden.bs.modal', function () {
        $(".error").hide();
    });
    $('#add_phone').on('shown.bs.modal', function () {
        $(".error").hide();
    });

    $("#checkCode").click(function(){
        $(".code_error").hide();
        var verificationCode = $("#verificationCode").val();
        if(verificationCode != null && verificationCode != "") {
            $.post(site_url + "/notification/verify_number",{code:verificationCode}, function(data) {
                $("#confirm_code").modal('hide');
                $("#thanks").modal('show');
               // window.location.reload();   
            }).fail(function(){
                $(".code_error").show();
                $("#checkCode").prop('disabled',true);
                $("#checkCode").removeClass('feedback-success');
            });
        }
    });

    $('#confirm_code').on('hidden.bs.modal', function () {
        $(".code_error").hide();
    });

    $(".resend_mobile_code").click(function(){
        var id = $(this).attr('id');
        var prev_text = $(this).text();
        var element = $(this);
        $(this).text('Sending...');
        $.post(site_url + "/notification/mobile/resend_code",{id:id}, function(data) {
            $("#confirm_code").modal({
                backdrop: 'static',
                keyboard: true
            });
            $("#edit_details_phone").modal('hide');
            $(element).text(prev_text);
        }).fail(function(){
            $(element).text(prev_text);
        });
    });

    $(".cancel_click").click(function(){
        window.location.reload();
    });
    
});
