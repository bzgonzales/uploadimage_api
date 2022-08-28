
var isHttps = location.protocol.match(/https/);
var http = isHttps ? 'https://' : 'http://';
var site_url = http + location.host;
var site_url = site_url + '/test';

$(document).ready(function () {


  //load_phones();

  $("form[name='login-form']").submit(function() {
    var data = {
      user_name: $("input[name='input-login-user-name']").val(),
      password: $("input[name='input-login-password']").val()
    }
    console.log(data);
    $.ajax({
      type: 'post',
      dataType: 'json',
      data: data,
      url: site_url + '/request/login_verification/',
      beforeSend: function() {
        $("form[name='login-form']").find('input,textarea,select').each(function() {
          $(this).attr('disabled', 'disabled');
        });
        
      },
      success: function(response) {
        if (response.status == 'success') {
          location.href = response.url;
        } else if(response.status == 'inactive') {
          $("#login-form-notification").html('<div class="alert wf-alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> Your account is not active yet.</div>').fadeIn();
        
        } else {
          $("#login-form-notification").html('<div class="alert wf-alert-danger alert-dismissable"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> Invalid Username or Password</div>').fadeIn();
        }
      },
      complete: function() {
        $("form[name='login-form']").find('input,textarea,select').each(function() {
          $(this).removeAttr('disabled');
        });
        
      }
    });
    
    return false;
  });


    
  $(document).on('click', '#add-phone-btn', function(event) {
    load_phone_details('add');
  })

  $(document).on('click', '.call-phone-modal', function(event) {

    var phoneid = $(this).attr('data-phone-id');
    load_phone_details(phoneid);

  })  

  $(document).on('click', '.save-changes-btn', function(event) {

    var status = $(this).attr('data-status');
    var phoneid = $(this).attr('data-phone-id');



    var data = {
      phoneid: phoneid,
      status: status,
      full_name: $('#full-name').val(),
      address: $('#address').val(),
      phone: $('#phone').val(),
      gender: $('#gender').val(),
    }

    console.log('data',data);

    $.ajax({
      type: 'post',
      dataType: 'json',
      data: data,
      url: site_url + '/request.php',
      beforeSend: function() {
        
      },
      success: function(response) {
        
        if(response.success=='error'){
          alert('Saving '+response.success+'.');
        }else{
          alert('Phone Detail has been '+response.success+'.');
        }
        
        //jQuery('#phone-modal').modal('hide');
        jQuery('#close-button').trigger('click');
        //phonestable.ajax.reload();
      },
      complete: function() {
        
      }
    });
    
    return false;

  })

});



  function load_phone_details(phone_id){

    var data = {
      phone_id: phone_id,
    }

    $.ajax({
      type: 'post',
      dataType: 'json',
      data: data,
      url: site_url + '/request/phone_data_callback/',
      beforeSend: function() {
        
      },
      success: function(response) {
          $("#phone-content").html(response.form);
          $("#phone-buttons").html(response.buttons);
          //$('#select-form-clients').selectpicker();
          $('#deliverydate').datetimepicker({
              format: 'YYYY-MM-DD'
          });
      },
      complete: function() {
        
      }
    });
    
    return false;

  }



  function load_phones(){


    $.ajax({
      type: 'post',
      dataType: 'json',
      url: site_url + '/request/phonebooks/',
      beforeSend: function() {
        
      },
      success: function(response) {

      },
      complete: function() {
        
      }
    });
    
    return false;
        
  }
