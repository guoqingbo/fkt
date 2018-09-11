$(function () {
  document.oncontextmenu = function (e) {
    return false;
  }//禁止右键
  if (!window.XMLHttpRequest) {
    $(".forms_scroll").scroll(function () {
      $(".ui-autocomplete").hide();
    })
  }
});
$.validator.addMethod("valid_name", function (value, element, params) {
  var reg = /^[A-Za-z0-9\u4e00-\u9fa5]+$/;
  if (reg.test(value)) {
    return true;
  }
}, "业主姓名只能包含汉字、字母、数字");

$.validator.addMethod("isCardNo", function (value, element, params) {
  var reg = /(^\d{15}$)|(^\d{18}$)|(^\[a-zA-Z]{6}$)|(^\d{17}(\d|X|x)$)/;
  if (reg.test(value)) {
    return true;
  }
}, "身份证输入不合法");


$.validator.addMethod("isZWNo", function (value, element, params) {
  var reg = /[\u0391-\uFFE5]/;
  if (!reg.test(value)) {
    return true;
  }
}, "电话不能有中文");

$.validator.addMethod("isZMNo", function (value, element, params) {
  var reg = /(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/;
  if (reg.test(value)) {
    return true;
  }
}, "电话只能包含7-13位数字和中划线&nbsp;");

$.validator.addMethod("isTNAME", function (value, element, params) {
  if (value.length <= 5) {
    return true;
  }
}, "业主姓名最多5个字符");

$(function () {

  $("#jsUpForm").validate({
    errorPlacement: function (error, element) {
      element.parents(".pane").find(".errorBox").html(error);
    },
    submitHandler: function (form) {
      lol_chushen();
    },
    rules: {
      s_id: {
        required: true
      },
      seller_owner: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      buyer_owner: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      seller_idcard: {
        required: true,
        isCardNo: true
      },
      buyer_idcard: {
        required: true,
        isCardNo: true
      },
      seller_telno: {
        required: true,
        isZMNo: true,
        isZWNo: true
      },
      buyer_telno: {
        required: true,
        isZMNo: true,
        isZWNo: true
      }
    },
    messages: {
      s_id: {
        required: '请选择合同编号'
      },
      seller_owner: {
        required: '请填写业主姓名'
      },
      buyer_owner: {
        required: '请填写买方姓名'
      },
      seller_idcard: {
        required: '请填写业主身份证',
        isCardNo: '请正确填写'
      },
      buyer_idcard: {
        required: '请填写买方身份证',
        isCardNo: '请正确填写'
      },
      seller_telno: {
        required: "请填写电话号码"
      },
      buyer_telno: {
        required: "请填写电话号码"
      }
    }
  });

  $("#chushen_form").validate({
    errorPlacement: function (error, element) {
      element.parents(".pane").find(".errorBox").html(error);
    },
    submitHandler: function (form) {
      cooperate_chushen();
    },
    rules: {
      c_id: {
        required: true
      },
      seller_owner: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      buyer_owner: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      seller_idcard: {
        required: true,
        isCardNo: true
      },
      buyer_idcard: {
        required: true,
        isCardNo: true
      },
      seller_telno: {
        required: true,
        isZMNo: true,
        isZWNo: true
      },
      buyer_telno: {
        required: true,
        isZMNo: true,
        isZWNo: true
      }
    },
    messages: {
      c_id: {
        required: '请选择合同编号'
      },
      seller_owner: {
        required: '请填写业主姓名'
      },
      buyer_owner: {
        required: '请填写买方姓名'
      },
      seller_idcard: {
        required: '请填写业主身份证',
        isCardNo: '请正确填写'
      },
      buyer_idcard: {
        required: '请填写买方身份证',
        isCardNo: '请正确填写'
      },
      seller_telno: {
        required: "请填写电话号码"
      },
      buyer_telno: {
        required: "请填写电话号码"
      }
    }
  });


  //活动提交初审资料
  function lol_chushen() {
    var p_filename = new Array;
    $("input[name='p_filename1[]']").each(function (index, item) {
      p_filename.push($(this).val());
    });
    if (p_filename.length > 0) {
      $.ajax({
        type: 'POST',
        url: '/cooperate_lol/add_apply',
        data: {
          s_id: $("#s_id").val(),
          seller_owner: $("input[name='seller_owner']").val(),
          seller_idcard: $("input[name='seller_idcard']").val(),
          seller_telno: $("input[name='seller_telno']").val(),
          buyer_owner: $("input[name='buyer_owner']").val(),
          buyer_idcard: $("input[name='buyer_idcard']").val(),
          buyer_telno: $("input[name='buyer_telno']").val(),
          p_filename: p_filename
        },
        dataType: 'json',
        success: function (data) {
          if (data['result'] == 200) {
            openWin('js_pop_do_success');
            $("#dialog_share").click(function () {
              window.parent.search_form.submit();
              return false;
            })
          }
          else {
            openWin('js_pop_do_warning');
          }
        }
      });
    } else {
      $("#photo").css('display', 'block');
    }
  }

  //合作审核提交初审资料
  function cooperate_chushen() {
    var p_filename = new Array;
    $("input[name='p_filename1[]']").each(function (index, item) {
      p_filename.push($(this).val());
    });
    if (p_filename.length > 0) {
      $.ajax({
        type: 'POST',
        url: '/cooperate/add_apply',
        data: {
          c_id: $("#c_id").val(),
          seller_owner: $("input[name='seller_owner']").val(),
          seller_idcard: $("input[name='seller_idcard']").val(),
          seller_telno: $("input[name='seller_telno']").val(),
          buyer_owner: $("input[name='buyer_owner']").val(),
          buyer_idcard: $("input[name='buyer_idcard']").val(),
          buyer_telno: $("input[name='buyer_telno']").val(),
          p_filename: p_filename
        },
        dataType: 'json',
        success: function (data) {
          if (data['result'] == 200) {
            openWin('js_pop_do_success');
            $("#dialog_share").click(function () {
              window.parent.search_form.submit();
              return false;
            })
          }
          else {
            openWin('js_pop_do_warning');
          }
        }
      });
    } else {
      $("#photo").css('display', 'block');
    }
  }


})
