//申请房源合作
function cooperate_house(tbl, rowid, broker_a_id) {
  //判断该房源是否存在
  $.ajax({
    url: MLS_URL + '/' + tbl + "/check_is_qualified_house",
    type: "GET",
    dataType: 'json',
    data: {house_id: rowid},
    success: function (data) {
      if ('success' == data.msg) {
        $param = '?tbl=' + tbl + '&rowid=' + rowid + '&broker_a_id=' + broker_a_id;
        var _url = MLS_URL + '/cooperate/apply_cooperate/' + $param;

        if (_url) {
          $("#js_pop_box_cooperation_customer .iframePop").attr("src", _url);
        }

        openWin('js_pop_box_cooperation_customer');
      } else {
        $("#dialog_do_warnig_tip").html("该房源为非合作、有效房源");
        openWin('js_pop_do_warning');
      }
    }
  });
}


//申请客源合作
function cooperate_customer(kind, c_id) {
  var customer_id = parseInt(c_id);
  var controller = '';
  if ('buy_customer' == kind) {
    controller = 'customer';
  } else {
    controller = 'rent_customer';
  }
  //判断该客源是否存在
  $.ajax({
    url: MLS_URL + "/" + controller + "/check_is_qualified_house",
    type: "GET",
    dataType: 'json',
    data: {customer_id: customer_id},
    success: function (data) {
      if ('success' == data.msg) {
        $param = '?customer_id=' + customer_id + '&kind=' + kind;
        var _url = MLS_URL + '/cooperate/apply_customer_cooperate_window/' + $param;
        if (_url) {
          $("#js_pop_box_cooperation .iframePop").attr("src", _url);
        }

        openWin('js_pop_box_cooperation');
      } else {
        $("#dialog_do_warnig_tip").html("该客源为非有效、合作客源");
        openWin('js_pop_do_warning');
      }
    }
  });
}

//客源合作提交页面
function show_customer_cooperate(tbl, rowid, broker_a_id, customer_id, apply_type) {
  $("#js_pop_box_cooperation").hide();
  $("#GTipsCoverjs_pop_box_cooperation").remove();

  $param = '?tbl=' + tbl + '&rowid=' + rowid + '&broker_a_id=' + broker_a_id + '&customer_id=' + customer_id + '&apply_type=' + apply_type;
  var _url = MLS_URL + '/cooperate/apply_cooperate/' + $param;

  if (_url) {
    $("#js_pop_box_cooperation_customer .iframePop").attr("src", _url);
  }

  openWin('js_pop_box_cooperation_customer');
}

//打开父亲页面提醒弹框
function showParentDialog(dateId, popId, msg) {
  $(window.parent.document).find("#" + dateId).html(msg);
  window.parent.openWin(popId);
}

//关闭父亲页面（IFRAME）弹框
function closePopFun(dialog_id) {
  $("#GTipsCover" + dialog_id).remove();
  $("#" + dialog_id).hide();
}

//举报页面弹跳
function report(type, ct_id, cooper_type) {
  var _url = MLS_URL + '/cooperate/' + type + '/' + ct_id + '/' + cooper_type;

  if (_url) {
    $("#js_woyaojubao .iframePop").attr("src", _url);
  }
  openWin('js_woyaojubao');
}

function changeTwoDecimal(floatNum, oInput) {//保留两位小数
  var num = parseFloat(floatNum);
  if (isNaN(num) || num < 0) {
    // $(oInput).addClass("errorBgInput");
    return floatNum;
  }
  else {
    var num = Math.floor(num * 100) / 100;
    //   $(oInput).removeClass("errorBgInput");
    return num;
  }
};

function changeIntNum(floatNum, oInput) {
  //取整数
  var num = parseInt(floatNum);

  if (isNaN(num) || num < 0) {
    return floatNum;
  }
  else {
    if (num > 100) {
      // $(oInput).addClass("errorBgInput");
      return floatNum;
    }
    else {
      //    $(oInput).removeClass("errorBgInput");
      return num;
    }
  }
};

$(function () {
  $(".js_input_t_yj01").blur(function () {
    var _val = $(this).val();
    $(".js_input_t_yj01").val(changeTwoDecimal(_val, ".js_input_t_yj01"));
  });

  $(".js_input_t_yj02").blur(function () {
    var _val = $(this).val();
    $(".js_input_t_yj02").val(changeTwoDecimal(_val, ".js_input_t_yj02"));
  });

  $(".js_input_t_yj03").blur(function () {
    var _val = $(this).val();

    if (_val != '') {
      if (_val > 100) {
        _val = 100;
      }
      if (_val < 0) {
        _val = 0;
      }
      var _num = changeTwoDecimal(_val, ".js_input_t_yj03");
      (!isNaN(_num) && _num != "") ? $(".js_input_t_yj03").val(100 - _num) : $(".js_input_t_yj03").val('');
      if (_num == 0) {
        $(".js_input_t_yj03").val(100)
        $(this).val(0)
      }
      $(this).val(_num);
    }
  });
});
