//验证用户名
jQuery.validator.addMethod("userNameCheck", function (value, element) {
  return this.optional(element) || /^[a-zA-Z]\w{4,20}$/.test(value);
}, "请输入4-20位字母开头的字母或数字和下划线");

//字符验证
jQuery.validator.addMethod("stringCheck", function (value, element) {
  return this.optional(element) || /^[\u0391-\uFFE5\w]+$/.test(value);
}, "只能包括中文字、英文字母、数字和下划线");

//邮政编码验证
jQuery.validator.addMethod("isEmail", function (value, element) {
  return this.optional(element) || /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/.test(value);
}, "请正确填写邮箱格式");

//手机号码验证
jQuery.validator.addMethod("isMobile", function (value, element) {
  var length = value.length;
  var mobile = /(^(13|14|15|18)\d{9}$)/;
  return this.optional(element) || (length == 11 && mobile.test(value));
}, "请正确填写您的手机号码");

//电话号码验证
jQuery.validator.addMethod("isTel", function (value, element) {
  var tel = /^\d{3,4}-?\d{7,9}$/;    //电话号码格式010-12345678
  return this.optional(element) || (tel.test(value));
}, "请正确填写您的电话号码");

//联系电话(手机/电话皆可)验证
jQuery.validator.addMethod("isPhone", function (value, element) {
  var length = value.length;
  var mobile = /(^(13|14|15|18)\d{9}$)|(^0(([1,2]\d)|([3-9]\d{2}))\d{7,8}$)/;
  var tel = /^\d{3,4}-?\d{7,9}$/;
  return this.optional(element) || (tel.test(value) || mobile.test(value));

}, "请正确填写您的联系电话");
$(function () {
  $(".find_password .input_t").blur(function () {
    if ($(this).val() == "") {
      $(this).next(".placeholder_for").show();
    } else {
      $(this).next(".placeholder_for").hide();
    }
  });
  $(".find_password .input_t").focus(function () {
    if ($(this).val() == "") {
      $(this).next(".placeholder_for").hide();
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
              $('#verify_password_error').html('<label class="error" for="password">密码不一致</label>');
              break;
            case 'no_user':
              $('#phone').addClass('error');
              $('#phone_error').html('<label class="error" for="phone">该电话号码不存在</label>');
              break;
            default:
              $('#error_submit').html('<label class="error" for="phone">设置失败，请稍后再试。</label>');
              break;
          }
        }
      });
    },

    errorPlacement: function (error, element) {
      error.appendTo(element.siblings(".error_add"));
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
        equalTo: "#password"
      }
    },
    messages: {
      phone: {
        required: "请填写手机号码",
        number: "请正确填写手机号码",
        minlength: "请输入11位手机号码"
      },
      validcode: {
        required: "请输入验证码"
      },
      password: {
        required: "请输入密码"
      },
      verify_password: {
        required: "请输入密码",
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
              $('#validcode_error').html('<label class="error" for="validcode">手机号未验证或验证失败</label>');
              break;
            case 'phonecheck_error':
              $('#validcode').addClass('error');
              $('#validcode_error').html('<label class="error" for="validcode">手机号语音验证失败</label>');
              break;
            case 'had_register':
              $('#phone').addClass('error');
              $('#phone_error').html('<label class="error" for="phone">此号码已经被注册过</label>');
              break;
            default:
              $('#error_submit').html('<label class="error">注册失败，请稍后再试。</label>');
              break;
          }
        }
      });
    },

    errorPlacement: function (error, element) {
      error.appendTo(element.siblings(".error_add"));
    },
    rules: {
      corpName: {
        required: true
      },
      name2: {
        required: true
      },
      userName: {
        required: true
      },
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
      }
    },
    messages: {
      corpName: {
        required: "请填写正确的公司"
      },
      name2: {
        required: "请填写正确的门店"
      },
      userName: {
        required: "请填写姓名"
      },
      phone: {
        required: "请填写手机号码",
        number: "请正确填写手机号码",
        minlength: "请输入11位手机号码"
      },
      validcode: {
        required: "请输入验证码"
      },
      password: {
        required: "请输入密码"
      }
    }
  })
});
