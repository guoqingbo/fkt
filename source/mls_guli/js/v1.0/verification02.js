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

$.validator.addMethod("noNum0", function (value, element, params) {
  if (value != 0) {
    return true;
  }
  else {
    return false;
  }
}, "不能为0");

$.validator.addMethod("isCardNo", function (value, element, params) {
  var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
  if (reg.test(value) || value == "") {
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
  if (value.length <= 10) {
    return true;
  }
}, "业主姓名最多10个字符");

$(function () {
  $("#addTel01").live("click", function () {
    if ($(".field-tel02").css("display") == 'none') {
      $(".field-tel02").show();
    } else {
      $(".field-tel03").show();
    }
  });

  $("#delTel02,#delTel03").click(function () {
    $(this).siblings(".input_text").attr("value", "");
    $(this).parent().hide();
  })
  $("#addBlock01").live("click", function () {
    if ($("#block02").parent().css("display") == 'none') {
      $("#block02").parent().show();
    }
    else {
      $("#block03").parent().show();
    }
  });

  $("#delBlock02,#delBlock03").click(function () {
    $(this).siblings(".input_text").attr("value", "");
    $(this).siblings(".cmt_id").attr("value", "");
    $(this).parent().hide();
  })

  $("#addQS01").live("click", function () {
    if ($("#QS02").css("display") == 'none') {
      $("#QS02").show();
    } else {
      $("#QS03").show();
    }
  });

  $("#delQS02,#delQS03").click(function () {
    $(this).siblings(".js_fields").find(".select").find("option").removeAttr("selected");
    $(this).siblings(".js_fields").find(".select").find("option:first").attr("selected", "selected");
    $(this).parent().hide();
  })

  $("#jsUpForm").validate({
    errorPlacement: function (error, element) {
      error.appendTo(element.parents(".js_fields").find(".errorBox"));
    },
    submitHandler: function (form) {
      var publish_type = $("#publish_type").val();

      if (publish_type == 'buy_customer_publish') {
        buy_customer_publish();
      }
      else if (publish_type == 'rent_customer_publish') {
        rent_customer_publish();
      }
      else if (publish_type == 'buy_customer_modify') {
        var old_nature = $("#old_nature").val();
        var new_nature = $("input[name='public_type']:checked").val();
        var group_id = $("#group_id").val();
        //未认证用户不能修改客源性质
        if ('1' == group_id && old_nature != new_nature) {
          $("#dialog_do_warnig_tip").html("您的帐号尚未认证,不能修改客源性质");
          openWin('js_pop_do_warning');
          return false;
        }
        buy_customer_modify();
      }
      else if (publish_type == 'rent_customer_modify') {
        var old_nature = $("#old_nature").val();
        var new_nature = $("input[name='public_type']:checked").val();
        var group_id = $("#group_id").val();
        //未认证用户不能修改客源性质
        if ('1' == group_id && old_nature != new_nature) {
          $("#dialog_do_warnig_tip").html("您的帐号尚未认证,不能修改客源性质");
          openWin('js_pop_do_warning');
          return false;
        }
        rent_customer_modify();
      }
    },
    rules: {
      truename: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      idno: {
        isCardNo: true
      },
      'telno[]': {
        required: true,
        isZMNo: true,
        isZWNo: true
      },
      status: {
        required: true
      },
      public_type: {
        required: true
      },
      is_share: {
        required: true
      },
      property_type: {
        required: true
      },
      room_min: {
        required: true,
        number: true,
        digits: true,
        min: 1
      },
      room_max: {
        required: true,
        number: true,
        digits: true,
        noNum0: true,
        min: function () {
          if (!$("#room_min").val() == "") {
            return parseFloat($("#room_min").val());
          }
        }
      },
      floor_min: {
        number: true,
        digits: true,
        min: 1
      },
      floor_max: {
        number: true,
        digits: true,
        min: function () {
          if (!$("#floor_min").val() == "") {
            return parseFloat($("#floor_min").val());
          }
        }
      },
      price_min: {
        min: 1,
        required: true,
        number: true
      },
      price_max: {
        noNum0: true,
        required: true,
        number: true,
        min: function () {
          if (!$("#price_min").val() == "") {
            return parseFloat($("#price_min").val());
          }
        }
      },
      area_min: {
        min: 1,
        required: true,
        number: true
      },
      area_max: {
        noNum0: true,
        number: true,
        required: true,
        min: function () {
          if (!$("#mianji01").val() == "") {
            return parseFloat($("#mianji01").val())
          }
        }
      },
      'dist_id[]': {
        min: 1
      }/*,
       'street_id[]':{
       min:1
       }*/
    },
    messages: {
      truename: {
        required: '请填写业主姓名'
      },
      idno: {
        isCardNo: '请正确填写'
      },
      'telno[]': {
        required: "请填写电话号码"
      },
      status: {
        required: '请选择状态'
      },
      public_type: {
        required: '请选择客源性质'
      },
      is_share: {
        required: '请选择是否合作'
      },
      property_type: {
        required: '选择物业类型'
      },
      room_min: {
        required: '请填写户型',
        number: '请填写数字',
        digits: '请填写大于0的整数',
        min: '最小1室'
      },
      room_max: {
        required: '请填写',
        number: '请填数字',
        digits: '请填大于0整数',
        min: '不能比前面小'
      },
      floor_min: {
        number: '请填写数字',
        digits: '请填写大于0的整数',
        min: '最小1层'
      },
      floor_max: {
        number: '请填数字',
        digits: '请填大于0整数',
        min: '不能比前面小'
      },
      price_min: {
        min: '最小为1',
        required: '请填写',
        number: '请填数字'
      },
      price_max: {
        min: '不能比前面小',
        required: '请填写',
        number: '请填数字'
      },
      area_min: {
        min: '最小为1',
        required: '请填写',
        number: '请填数字'
      },
      area_max: {
        min: '不能比前面小',
        required: '请填写',
        number: '请填数字'
      },
      'dist_id[]': {
        min: '请选择区属'
      }/*,
       'street_id[]':{
       min:'请选择板块'
       }*/
    }
  });
})

//求购客源发布验证
function buy_customer_publish() {
  var form_data = $("#jsUpForm").serialize();
  $.ajax({
    type: 'POST',
    url: '/customer/add/',
    data: form_data,
    dataType: 'JSON',
    success: function (data) {
      if (data.ret == 1) {
        var list_url = '/customer/manage/';
        var msg = data.msg + "<br><a href ='" + list_url + "'>跳转到客源列表页&gt;&gt;</a>";
        $("#dialog_do_itp").html(msg);
        openWin('js_pop_do_success');
        setTimeout("jump_to_url('" + list_url + "')", 3000);
      }
      else {
        $("#dialog_do_warnig_tip").html(data.msg);
        openWin('js_pop_do_warning');
      }
    },
    error: function (er) {
      var error_msg = '异常错误';
      $("#dialog_do_warnig_tip").html(error_msg);
      openWin('js_pop_do_warning');
      return false;
    }
  });
}

//更新求购客源信息
function buy_customer_modify() {
  var form_data = $("#jsUpForm").serialize();
  $.ajax({
    type: 'POST',
    url: '/customer/update/',
    data: form_data,
    dataType: 'JSON',
    success: function (data) {
      if (data['errorCode'] == '401') {
        login_out();
        $("#jss_pop_tip").hide();
      }
      else if (data['errorCode'] == '403') {
        permission_none();
        $("#jss_pop_tip").hide();
      } else {
        if (data.ret == 1) {
          var list_url = '/customer/manage/';
          var msg = data.msg + "<br><a href ='" + list_url + "'>跳转到客源列表页&gt;&gt;</a>";
          $("#dialog_do_itp").html(msg);
          openWin('js_pop_do_success');
          setTimeout("jump_to_url('" + list_url + "')", 3000);
        }
        else {
          $("#dialog_do_warnig_tip").html(data.msg);
          openWin('js_pop_do_warning');
        }
      }
    },
    error: function (er) {
      var error_msg = '异常错误';
      $("#dialog_do_warnig_tip").html(error_msg);
      openWin('js_pop_do_warning');
      return false;
    }
  });
}

//更新求租客源信息
function rent_customer_modify() {
  var form_data = $("#jsUpForm").serialize();
  $.ajax({
    type: 'POST',
    url: '/rent_customer/update/',
    data: form_data,
    dataType: 'JSON',
    success: function (data) {
      if (data.ret == 1) {
        var list_url = '/rent_customer/manage/';
        var msg = data.msg + "<br><a href ='" + list_url + "'>跳转到客源列表页&gt;&gt;</a>";
        $("#dialog_do_itp").html(msg);
        openWin('js_pop_do_success');
        setTimeout("jump_to_url('" + list_url + "')", 3000);
      }
      else {
        $("#dialog_do_warnig_tip").html(data.msg);
        openWin('js_pop_do_warning');
      }
    },
    error: function (er) {
      var error_msg = '异常错误';
      $("#dialog_do_warnig_tip").html(error_msg);
      openWin('js_pop_do_warning');
      return false;
    }
  });
}

//求租客源发布
function rent_customer_publish() {
  var form_data = $("#jsUpForm").serialize();
  $.ajax({
    type: 'POST',
    url: '/rent_customer/add/',
    data: form_data,
    dataType: 'JSON',
    success: function (data) {
      if (data['errorCode'] == '401') {
        login_out();
        $("#jss_pop_tip").hide();
      } else if (data['errorCode'] == '403') {
        permission_none();
        $("#jss_pop_tip").hide();
      } else {
        if (data.ret == 1) {
          var list_url = '/rent_customer/manage/';
          var msg = data.msg + "<br><a href ='" + list_url + "'>跳转到客源列表页&gt;&gt;</a>";
          $("#dialog_do_itp").html(msg);
          openWin('js_pop_do_success');
          setTimeout("jump_to_url('" + list_url + "')", 3000);
        }
        else {
          $("#dialog_do_warnig_tip").html(data.msg);
          openWin('js_pop_do_warning');
        }
      }
    },
    error: function (er) {
      var error_msg = '异常错误';
      $("#dialog_do_warnig_tip").html(error_msg);
      openWin('js_pop_do_warning');
      return false;
    }
  });
}

function jump_to_url(url) {
  if (url != '') {
    location.href = url;
  }
}

function checkedAll(obj, id) {
  var i = $("#" + id);
  if ($(obj).hasClass('labelOn')) {
    i.find("b.label").removeClass("labelOn");
    i.find(".js_checkbox").attr("checked", false);
  }
  else {
    i.find("b.label").addClass("labelOn");
    i.find(".js_checkbox").attr("checked", true);
  }
}

function check_unique_customer(kind, check_input) {
  var truename = $.trim($('#truename').val());
  var telno1 = $.trim($('#telno1').val());
  var telno2 = $.trim($('#telno2').val());
  var telno3 = $.trim($('#telno3').val());
  var customer_id = $.trim($('#customer_id').val());


  //业主姓名为空验证
  if (check_input == 'truename') {
    if (telno1 == '' && telno2 == '' && telno3 == '') {
      return false;
    }
  }

  //手机号码格式验证
  //var reg = /(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/;
  // if(!reg.test(telno1) || (telno3 != '' && !reg.test(telno2)) || (telno3 != '' && !reg.test(telno3)) )
  //{
  //    $('.tip_text').html('<font style="color:red;">电话号码格式错误，无法录入客源</font>');
  //    return false;
  // }

  if (truename != '' && telno1 != '') {
    $.ajax({
      url: "/" + kind + "/check_unique_customer/",
      type: "GET",
      dataType: "json",
      data: {truename: truename, telno1: telno1, telno2: telno2, telno3: telno3, customer_id: customer_id},
      success: function (data) {
        //判断返回数据是否为空，不为空返回数据。
        if ('success' == data.msg) {
          $('.tip_text').html('非重复客源，可以录入');
        }
        else {
          $('.tip_text').html('<font style="color:red;">您的库中已有该客源，不可重复录入</font>');
        }
      }
    });
  }
}

$(function () {
  $.widget("custom.autocomplete", $.ui.autocomplete, {
    _renderItem: function (ul, item) {
      if (item.id > 0) {
        return $("<li>")
          .data("item.autocomplete", item)
          .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">' + item.label + '</span><span class="ui_district">' + item.districtname + '</span><span class="ui_address">' + item.address + '</span></a>')
          .appendTo(ul);
      } else {
        return $("<li>")
          .data("item.autocomplete", item)
          .append('<a class="ui-corner-all" tabindex="-1">' + item.label + '</a>')
          .appendTo(ul);
      }
    }
  });

  $("#block01").autocomplete({
    source: function (request, response) {
      var cmt_name = request.term;
      $.ajax({
        url: "/community/get_cmtinfo_by_kw/",
        type: "GET",
        dataType: "JSON",
        data: {keyword: cmt_name},
        success: function (data) {
          //判断返回数据是否为空，不为空返回数据。
          if (data[0]['id'] != '0') {
            response(data);
          }
          else {
            response(data);
          }
        }
      });
    },
    minLength: 1,
    removeinput: 0,
    select: function (event, ui) {
      if (ui.item.id > 0) {
        var cmt_name = ui.item.label;
        var id = ui.item.id;
        $(this).val(cmt_name);
        $('#cmt_id01').val(id);
        //change_tag控制change事件发生时,是否允许删除对应小区ID，1代表可以更改，0代表无须更改
        $(this).attr('change_tag', '1');
        removeinput = 2;
      }
      else {
        //还原属性值，下次onchange事件发生时，不许要删除对应小区编号
        $(this).attr('change_tag', '0');
        removeinput = 1;
      }
    },
    change: function (event, ui) {
      if ($(this).attr('change_tag') == '1' && ( typeof(removeinput) == 'undefined' || removeinput == 0 || removeinput == 1)) {
        $('#cmt_id01').val('');
        //还原属性值，下次onchange事件发生时，不许要删除对应小区编号
        $(this).attr('change_tag', '0');
      }
      else {
        removeinput = 0;
      }
    },
    close: function (event) {
      if (typeof(removeinput) == 'undefined' || removeinput == 1) {
        $(this).val('');
        $('#cmt_id01').val('');
        //还原属性值，下次onchange事件发生时，不许要删除对应小区编号
        $(this).attr('change_tag', '0');
      }
    }
  });

  $("#block02").autocomplete({
    source: function (request, response) {
      var cmt_name = request.term;
      $.ajax({
        url: "/community/get_cmtinfo_by_kw/",
        type: "GET",
        dataType: "JSON",
        data: {keyword: cmt_name},
        success: function (data) {
          //判断返回数据是否为空，不为空返回数据。
          if (data[0]['id'] != '0') {
            response(data);
          }
          else {
            response(data);
          }
        }
      });
    },
    minLength: 1,
    removeinput: 0,
    select: function (event, ui) {
      if (ui.item.id > 0) {
        var cmt_name = ui.item.label;
        var id = ui.item.id;
        $(this).val(cmt_name);
        $('#cmt_id02').val(id);
        //change_tag控制change事件发生时,是否允许删除对应小区ID，1代表可以更改，0代表无须更改
        $(this).attr('change_tag', '1');
        removeinput = 2;
      }
      else {
        //还原属性值，下次onchange事件发生时，不许要删除对应小区编号
        $(this).attr('change_tag', '0');
        removeinput = 1;
      }
    },
    change: function (event, ui) {
      if ($(this).attr('change_tag') == '1' && (typeof(removeinput) == 'undefined' || removeinput == 0 || removeinput == 1)) {
        $('#cmt_id02').val('');
        //还原属性值，下次onchange事件发生时，不许要删除对应小区编号
        $(this).attr('change_tag', '0');
      }
      else {
        removeinput = 0;
      }
    },
    close: function (event) {
      if (typeof(removeinput) == 'undefined' || removeinput == 1) {
        $(this).val('');
        $('#cmt_id02').val('');
        //还原属性值，下次onchange事件发生时，不许要删除对应小区编号
        $(this).attr('change_tag', '0');
      }
    }
  });

  $("#block03").autocomplete({
    source: function (request, response) {
      var cmt_name = request.term;
      $.ajax({
        url: "/community/get_cmtinfo_by_kw/",
        type: "GET",
        dataType: "JSON",
        data: {keyword: cmt_name},
        success: function (data) {
          //判断返回数据是否为空，不为空返回数据。
          if (data[0]['id'] != '0') {
            response(data);
          }
          else {
            response(data);
          }
        }
      });
    },
    minLength: 1,
    removeinput: 0,
    select: function (event, ui) {
      if (ui.item.id > 0) {
        var cmt_name = ui.item.label;
        var id = ui.item.id;
        $(this).val(cmt_name);
        $('#cmt_id03').val(id);
        //change_tag控制change事件发生时,是否允许删除对应小区ID，1代表可以更改，0代表无须更改
        $(this).attr('change_tag', '1');
        removeinput = 2;
      }
      else {
        //还原属性值，下次onchange事件发生时，不许要删除对应小区编号
        $(this).attr('change_tag', '0');
        removeinput = 1;
      }
    },
    change: function (event, ui) {
      if ($(this).attr('change_tag') == '1' && (typeof(removeinput) == 'undefined' || removeinput == 0 || removeinput == 1)) {
        $('#cmt_id03').val('');
        //还原属性值，下次onchange事件发生时，不许要删除对应小区编号
        $(this).attr('change_tag', '0');
      }
      else {
        removeinput = 0;
      }
    },
    close: function (event) {
      if (typeof(removeinput) == 'undefined' || removeinput == 1) {
        $(this).val('');
        $('#cmt_id03').val('');
        //还原属性值，下次onchange事件发生时，不许要删除对应小区编号
        $(this).attr('change_tag', '0');
      }
    }
  });
});

//区属找板块
function get_street_by_id(obj, child_object_id) {
  var dist_id = parseInt($(obj).val());

  $.getJSON(
    '/district_street/get_streetinfo_by_distid/',
    {'dist_id': dist_id},
    function (data) {
      if (data == 'errorCode401') {
        jump_win('', '请重新登录');
        return false;
      }

      $("#" + child_object_id).empty();
      $("#" + child_object_id).append("<option selected='' value='0'>请选择板块</option>");
      $.each(data, function (i, item) {
        var child_option = "<option value=" + item.id + ">" + item.streetname + "</option>";
        $("#" + child_object_id).append(child_option);
      });
    }
  );
}
