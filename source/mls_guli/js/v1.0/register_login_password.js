$(function () {


  $(".find_password .input_t").focus(function () {
    $(this).next(".placeholder_for").hide();
  }).blur(function () {
    if ($(this).val() == "") {
      $(this).next(".placeholder_for").show();
    }
  });

  var findpw_query = $("#js_find_password").serialize();
  $("#js_find_password").validate({
    submitHandler: function (form) {
      $(form).ajaxSubmit({
        type: 'POST',
        url: '/login/findpw/',
        data: register_query,
        dataType: 'json',
        success: function (data) {
          switch (data.result) {
            case 'findpw_success':
              $('#findpw_ok_div').show();
              $('#findpw_form_div').hide();
              break;
            case 'validcode_error':
              $('#validcode').addClass('error');
              $('#validcode_error').html('<label class="error" for="validcode">验证码错误或已失效</label>');
              break;
            case 'password_error':
              $('#verify_password').addClass('error');
              $('#verify_password_error').html('<label class="error" for="phone">密码不一致</label>');
              break;
            case 'no_user':
              $('#phone').addClass('error');
              $('#phone_error').html('<label class="error" for="phone">该电话号码不存在</label>');
              break;
            default:
              $('#error_submit').html('<label class="error" for="phone">注册失败，请稍后再试。</label>');
              break;
          }
        }
      });
    },

    errorPlacement: function (error, element) {
      error.appendTo(element.parents(".info").find(".error_add"));
    },
    rules: {
      phone: {
        required: true,
        number: true,
        minlength: 11
      },
      validcode: {
        required: true
      },
      password: {
        required: true
      },
      verify_password: {
        required: true,
        equalTo: "#find_new_password"
      }
    },
    messages: {
      phone: {
        required: "请填写手机号码",
        number: "请正确填写手机号码",
        minlength: "请输入11位手机号码"
      },
      validcode: {
        required: "请填写验证码"
      },
      password: {
        required: "请填写密码"
      },
      verify_password: {
        required: "请填写密码",
        equalTo: "密码不一致"
      }
    }
  });


  var register_query = $("#js_register_form").serialize();
  $("#js_register_form").validate({
    submitHandler: function (form) {
      $(form).ajaxSubmit({
        type: 'POST',
        url: '/register/signup/',
        data: register_query,
        dataType: 'json',
        success: function (data) {
          switch (data.result) {
            case 'register_success':
              $('#register_ok_div').show();
              $('#register_form_div').hide();
              break;
            case 'validcode_error':
              $('#validcode').addClass('error');
              $('#validcode_error').html('<label class="error" for="validcode">验证码错误或已失效</label>');
              break;
            case 'had_register':
              $('#phone').addClass('error');
              $('#phone_error').html('<label class="error" for="phone">此号码已经被注册过</label>');
              break;
            default:
              $('#error_submit').html('<label class="error" for="phone">注册失败，请稍后再试。</label>');
              break;
          }
        }
      });
    },

    errorPlacement: function (error, element) {
      error.appendTo(element.parents(".info").find(".error_add"));
    },
    rules: {
      phone: {
        required: true,
        number: true,
        minlength: 11
      },
      validcode: {
        required: true
      },
      password: {
        required: true
      },
      province: {
        required: true
      },
      city_id: {
        min: 1
      }
    },
    messages: {
      phone: {
        required: "请填写手机号码",
        number: "请正确填写手机号码",
        minlength: "请输入11位手机号码"
      },
      validcode: {
        required: "请填写验证码"
      },
      password: {
        required: "请填写密码"
      },
      province: {
        required: "请选择所在省份和城市"
      },
      city_id: {
        min: "请选择所在省份和城市"
      }
    }
  })
});
