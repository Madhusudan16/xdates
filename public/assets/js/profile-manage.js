  // link share on twitter 
  function openTwitter(url) {
      var url = "http://www.twitter.com/share?url=" + url;
      window.open(url, '', 'height=600,width=600');
      return false;
  }

  function openLinkShare(url) {
      window.open('http://www.linkedin.com/shareArticle?mini=true&url=' + url, 'sharer', 'toolbar=0, status=0, width=626, height=436');
  }
  // end tell-frinds pop up 

  /*  set input values  */
  function setValues(fieldName, fieldvalue) {
      $('.loader').show();
      var data = []; // create data array for store input data
      var obj = {}; // create obj object for set fieldName from variable 
      obj[fieldName] = fieldvalue; // set value pair of key = value
      data.push(obj);
      updateData(data, false);
  }

  function updateData(data, isFile) { // this function called updateProfile function in MyprofileController class
      $('.loader').show();
      if (isFile) {
          $.ajax({
              url: site_url + "/updateProfile",
              type: 'POST',
              processData: false,
              contentType: false,
              cache: false,
              data: data
          }).done(function(response) {
              if (response != "" && response != null) {
                 
                  $("#profileImage").attr('src', response);
                  $(".profile-image").attr('src', response);
                  $(".remove_avatar_default").show();
                  $(".remove_avatar").show();
              }
              setTimeout(function() {
                  $(".loader").hide();
              }, 2000);
          }).fail(function(){
              $(".loader").hide();
          })

      } else {
          $.post(site_url + "/updateProfile", {
              'data': data
          }, function(response) {
              
                   console.log(response);
                   if (response.reload == 1) {
                      window.location.replace('/'+response.page_url);
                   } else if(response.reload  == 0) {
                      if(response.field_type == 1) {
                          $("#com_name").attr('readonly',true);
                          $(".dynamic_msg").text('Please select a Time Zone.');
                      } else if(response.field_type == 2 ) {
                          $(".dynamic_msg").text('Please enter your Company name.');
                      }
                   } 
              
              $(".loader").hide();
          }).error(function(responseData){
            if (responseData.field_name == "choosed_timezone") {
                $("#timeZone").val(responseData.field_value).prop('selected', true);
            } else {
                $("input[name = " + responseData.field_name + "]").val(responseData.field_value);
            }
            if ($('#f_name').val() != '' && $('#l_name').val() != '') {
                  var full_name = $('#f_name').val() + ' ' + $('#l_name').val();
                  $('.usr-name').html(full_name);
              }
              $(".loader").hide();
          });
      }
  }
  // edit email on notification 
  function editEmail(element) {
      // $("#save_edited_data").addClass('disable-btn');
      $("#save_edited_data").prop('disabled', true);
      var isactive = $(element).attr('isactive');
      var email = $(element).attr('email');
      var id = $(element).attr('dataId');
      if (isactive == 0) {
          $(".confirm-email-info").hide();
          $(".unconfirm-email-info").show();
      } else if(isactive == 1) {
          $(".confirm-email-info").show();
          $(".unconfirm-email-info").hide();
      }
      $("#edit_email").val(email);
      $(".email-id").val(id);
      $(".resendmail").attr('dataId', id);
      $(".resendmail").attr('email', email);
      $("#edit_details").modal('show');
  }
  // edit phone on notification  
  function editPhone(element) {
      $(".error").text('');
      var phone = $(element).attr('phone');
      $(element).val(phone.trim());
     /* phone = phone.match(/[\d\.]+/g);
      if (phone != null){
         var phone = phone.toString();
         phone = phone.replace(/,/g, '');
      }*/
      var isActive = $(element).attr('isActive');
      var countryCode = $(element).attr('countryCode');
      var id = $(element).attr('dataId');
      $("#isactive").val(isActive);
      $(".selectedCountry").val(countryCode).change();
      $("#phone-id").val(id);
      $(".edit_confirm_number").val(id);
      if (isActive == 1) {
          var notification_set = $(element).attr('receive_noti');
           var countryCode = $(element).attr('countrycode');
          $(".notification_receive").val(notification_set).change();
          $("#edit_phone_save").val(phone);
          $(".edit_country_code").val(countryCode).change();
          $("#details_phone").modal('show');
          $(".confirm-save-btn").attr('noti-active',notification_set);
          $(".confirm-save-btn").attr('number',phone);
          $(".confirm-save-btn").attr('country',countryCode);
      } else {
          $("#edit_phone").val(phone);
          $(".resend_mobile_code").attr('id',id);
          $("#edit_details_phone").modal('show');
      }
      $(".save_edited_phone").removeClass('feedback-success');
      $(".save_edited_phone").prop('disabled', true);
  }

  function savePhone(is_confirm,element) {
   
    $(".loader").show();
      if (is_confirm == 1) {
          var phone = $("#edit_phone").val();
          var id = $("#phone-id").val();
          var countryCode = $(".selectedCountry").val();
          var formData = $("#editPhoneForm").serialize();
      } else {
        var noti_active_or_not = $(".notification_receive").val();
          var phone = $("#edit_phone_save").val();
          var id = $(".edit_confirm_number").val();
          var formData = $("#edit_phone_form").serialize();
          var countryCode = $(".edit_country_code").val();
      }
      var is_edit = 1;

      $.post(site_url + "/add-phone", {
              data: formData,
              is_edit: is_edit
          }, function(result) {
              if (result.msg != null && result.msg != 'undefined') {
                  var msg = result.msg;
                  $(".error").show();
                  $(".error").html(msg);
                  $setValue = "(" + countryCode + ") " + phone;
                  
                      $(".phone-" + id).text($setValue);
                      if (is_confirm != 1) {
                          $("#details_phone").modal('hide');
                          
                          var number = $(".confirm-save-btn").attr('number');
                          var noti_active = $(".confirm-save-btn").attr('noti-active');
                          var country_code = $(".confirm-save-btn").attr('country');
                          
                          if(number != phone || countryCode != country_code) {
                              $("#confirm_code").modal({
                                  backdrop: 'static',
                                  keyboard: true
                              });

                              $(".resend_mobile_code").attr('id',id);
                              $(".phone-status-"+id).children('.value').removeClass('active');
                              $(".phone-status-"+id).children('.value').text('ACTIVATION REQUIRED');
                          } 
                          if(noti_active_or_not == 1) {
                            $(".phone-active-"+id).prop('checked',true);
                          } else {
                            $(".phone-active-"+id).prop('checked',false);
                          }
                      } else {
                            $("#edit_details_phone").modal('hide');
                            $("#confirm_code").modal({
                                backdrop: 'static',
                                keyboard: true
                            });
                      }
                      $(".recent-phone").text(countryCode +" "+ phone);
              }
              $(".loader").hide();
          })
          .fail(function(response) {
              console.log(response.responseText);
              var msg = $.parseJSON(response.responseText);
              console.log(msg);
              $prev_msg = $(".error").text();
              $(".error").html("");
              $(".error").show();
              $(".noti_cofi").hide();
              if(msg.error_msg[0] != null && msg.error_msg[0] != "undefined") {
                $(".error").html(msg.error_msg[0]);
              } else {
                $(".error").html(msg.error_msg);
              }
              $(element).removeClass('feedback-success');
              $(element).prop('disabled',true);
             /* setTimeout(function() {
                  $(".error").html("");
                  $(".error").text($prev_msg);
              }, 1000);*/
              $(".loader").hide();
          });
          $(element).text('save');
  }

  function changeStatus(element) {
      $(".loader").show();
      var id = $(element).attr('Id');
      var status = $(element).prop('checked');
      if(status == true) {
          status = 1; 
      } else {
          status = 0;
      }
      $.post(site_url + "/change-status", { id: id,status: status }, function(result) {
          if (result.success_msg != null && result.success_msg != 'undefined') {
              console.log(result);
          } 
          $(".edit-btn-"+id).attr('receive_noti',status);
          $(".loader").hide();
      })
      .fail(function(response) {
            $(".loader").hide();
           $(element).prop('checked',false);
      });
  }

  function deleteEmailData(element) {

      $(element).closest('li').addClass('deleted_li');
      var id = $(element).attr('Id');
      $(".deletedId").val(id);
      var email = $(element).attr('email');
      $(".confirm-email").html(email);
      $("#remove_email").modal('show');
  }
  $('#remove_email').on('hidden.bs.modal', function () {
        $('body').find('.deleted_li').removeClass('deleted_li');
        $(".deletedId").val('');
  });
  function deletePhoneData(element) {
      console.log(element);
      $(element).closest('li').addClass('deleted_li');
      var id = $(element).attr('Id');
      console.log(id);
      $(".remove-phone-number").val(id);
      console.log($(".remove-phone-number").val());
      var phone = $(element).attr('phone');
      $(".confirm-phone").text(phone);
      $("#remove_phone").modal('show');
  }

  $('#remove_phone').on('hidden.bs.modal', function () {
        $('body').find('.deleted_li').removeClass('deleted_li');
        $(".remove-phone-number").val('');
  });

  function addElement(email, id) {
      console.log(id);
      var html = "<li class='clearfix email-list'><div class='col-md-3 padding-0 email unique-email  email-"+id+"'>" + email + "</div><div class='col-md-5 padding-0 status'><span class='name'>current status:</span><span class='value'>ACTIVATION REQUIRED</span></div><div class='col-md-2 padding-0 on-off'><label class='switch'><input type='checkbox' class='activeStatus' onchange=changeStatus(this) id=" + id + "><div class='slider round'></div></label></div><div class='col-md-2 padding-0 edit_remove'><button type='button' class='btn btn-success' isactive='0' data-toggle='modal' id='editbtn' dataid=" + id + " email=" + email + " onclick=editEmail(this)>Edit</button> &nbsp; <a class=remove href='#'' data-toggle='modal' email=" + email + " id=" + id + " onclick=deleteEmailData(this)>remove</a></div></li>";
      $('.email-text').append(html);
  }

  function IsEmail(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email);
  }

  function addEmail(element) {
      $(".error").text('');
      $("#save_email").addClass('disable-btn');
      $("#save_email").prop('disabled', true);
      $("#email_add").val('');
      $("#add_email").modal('show');
  }

  function addPhoneElement(phone, Country, id) {

      var html = "<li class='clearfix'> <div class='col-md-3 padding-0 phone-"+id+" phone'><strong>"+phone + "</strong></div>  <div class='col-md-5 padding-0 status'><span class='name'>current status:</span><span class='value'>ACTIVATION REQUIRED</span></div><div class='col-md-2 padding-0 on-off'><label class='switch'><input type='checkbox' class='activeStatus' onchange='changeStatus(this)'  id=" + id + "><div class='slider round'></div></label></div><div class='col-md-2 padding-0 edit_remove'><button type='button' class='btn btn-success' data-toggle='modal' isactive='0' dataid="+id+" phone="+phone.replace(/ /g,'+')+" countrycode="+Country+" onclick='editPhone(this)'>Edit</button> &nbsp; <a class='remove' href='#' data-toggle='modal' phone=" + phone + " id=" + id + " onclick='deletePhoneData(this)'>remove</a></div> </li>";
      $('.phone-text').append(html);
  }

  function cancelAccount() {
      $('.loader').show();
      var email = $("#cancel-account-email").val();
      console.log(email);
      $.post(site_url + "/cancel-account", {
          'email': email
      }, function(response) {
          console.log(response)
              //var data =$.parseJSON(response)
          console.log(response.message);
          console.log(response.status);
          if (response.status == 200) {
              $('.loader').hide();
              window.location.reload();
          }
          $("#cancel_account").modal('hide');
      }).fail(function(response) {
          var data = $.parseJSON(response.responseText)
          console.log(response);
          $('.loader').hide();
          $(".error").text(data.message);
      });
      $("#cancel_account").modal('hide');
  }

  function removeAvatar()
  {
      $('.loader').show();

      $.post(site_url + "/remove-avatar", {
      }, function(response) {
          $("#profileImage").attr('src',site_url+'/'+default_image_path);
          $(".profile-image").attr('src',site_url+'/'+default_image_path);
          $(".remove_avatar").hide();
          $(".remove_avatar_default").hide();
          $('.loader').hide();

      }).fail(function(response) {
          var data = $.parseJSON(response.responseText)
          console.log(response);
          $('.loader').hide();
          $(".error").text(data.message);
      });
      $("#avatar_remove").modal('hide');
  }