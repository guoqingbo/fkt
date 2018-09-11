//设置cookie
function SetCookie(name, value)//
{
  var Days = 30; //此 cookie 将被保存 30 天
  var exp = new Date();    //new Date("December 31, 9998");
  exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
  document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
}

//导入
function openn_import(type, broker_id) {
  //先清空上传文本框
  $("input[name='upfile']").val('');
  $("#aa").val('');
  $("#broker_id").val(broker_id);
  openWin('jss_pop_import');
}

//确认导入
function openn_sure(type) {
  var id = $("#xx1x").contents().find("#tmp_id").val();
  var broker_id = $("#broker_id").val();
  if (id > 0) {
    $("#xx1x").contents().find("body").empty();
    openWin('jss_pop_sure', ajax_import(id, type, broker_id));
  }
  else {
    openWin('jss_pop_error');
  }
}

function ajax_import(id, type, broker_id) {
  var url;
  if (type == 'broker_info') {
    url = "/" + type + "/sure/";
  } else {
    url = MLS_URL + "/" + type + "/sure/";
  }
  $.ajax({

    url: url,
    type: "POST",
    dataType: "json",
    data: {id: id, broker_id: broker_id},
    success: function (data) {
      if (data.status == 'ok') {
        $('#jss_pop_sure .mod .inform_inner .text span').html(data.success);
        $("#jss_pop_sure .mod .inform_inner .text img").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/r_ico.png");
      } else {
        $('#jss_pop_sure .mod .inform_inner .text span').html(data.error);
        $("#jss_pop_sure .mod .inform_inner .text img").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/error_ico.png");
      }
    }
  });
}

//链接智能匹配
function open_match(type, is_public, house_id) {
  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  }
  var arr = new Array();
  var select_num = 0;

  if (house_id > 0) {
    var house_id = house_id;
  }
  else {
    $(".table").find("input:checked[name=items]").each(function (i) {
      arr[i] = $(this).val();
      select_num++;
    });

    if (select_num > 1) {
      $("#dialog_do_warnig_tip").html("请选择一条房源匹配");
      openWin('js_pop_do_warning');
      return false;
    }

    if (select_num == 0) {
      $("#dialog_do_warnig_tip").html("请选择要匹配的房源");
      openWin('js_pop_do_warning');
      return false;
    }

    var house_id = arr[0];
  }

  var _url = '/' + type + '/match/' + house_id;
  if (is_public > 0) {
    _url = _url + '/' + is_public;
  }
  if (_url) {
    $("#js_pop_box_g_match .iframePop").attr("src", _url);
  }

  openWin('js_pop_box_g_match');
}

//右键匹配弹跳
function openMatch(type, is_public) {
  var house_id = $("#right_id").val();

  var _url = '/' + type + '/match/' + house_id;

  if (is_public > 0) {
    _url = _url + '/' + is_public;
  }
  if (_url) {
    $("#js_pop_box_g_match .iframePop").attr("src", _url);
  }
  openWin('js_pop_box_g_match');
}

//出售出租删除房源
function del(type) {
  var text = "";
  var arr = new Array();
  var select_num = 0;
  var textarr = new Array();
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    textarr[arr[i]] = 'tr' + arr[i];
    select_num++;
  });
  text = arr.join(",");
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要删除的房源");
    openWin('js_pop_do_warning');
    return false;
  } else {
    $("#dialogSaveDiv").html("你确定删除吗？");
    openWin('jss_pop_tip');
    $("#dialog_share").click(function () {
      $.ajax({
        url: MLS_URL + "/" + type + "/del/",
        type: "GET",
        dataType: "json",
        data: {
          str: text,
          isajax: 1
        },
        success: function (data) {
          if (data['errorCode'] == '401') {
            login_out();
            return false;
          }
          else if (data['errorCode'] == '403') {
            purview_none();
            return false;
          }

          if (data['result'] == 'ok') {
            $("#js_pop_tip").remove();
            $('#search_form :input[name=page]').val('1');
            $("#dialog_do_itp").html("删除成功");
            openWin('js_pop_do_success');
            $('#search_form').submit();
            return false;
          }
        }
      });
    });
  }
}

//删除消息管理中的 公告、系统消息
function del_bulletin(type, broker_id) {
  var text = "";
  var arr = new Array();
  var select_num = 0;
  var textarr = new Array();
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    textarr[arr[i]] = 'tr' + arr[i];
    select_num++;
  });
  text = arr.join(",");
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要删除的内容！");
    openWin('js_pop_do_warning');
    return false;
  } else {
    $("#dialogSaveDiv").html("你确定删除吗？");
    openWin('jss_pop_tip');
    $("#dialog_share").click(function () {
      $.ajax({
        url: MLS_URL + "/" + type + "/del/",
        type: "GET",
        dataType: "json",
        data: {
          str: text,
          isajax: 1,
          broker_id: broker_id
        },
        success: function (data) {
          if (data['errorCode'] == '401') {
            login_out();
            return false;
          }
          else if (data['errorCode'] == '403') {
            purview_none();
            return false;
          }

          if (data['result'] == 'ok') {
            $("#js_pop_tip").remove();
            $("#dialog_do_itp").html("删除成功");
            openWin('js_pop_do_success');
            location.reload();
          }
        }
      });
    });
  }
}

//设为公共房源
function set_public(type, house_id) {

  $("#dialogSaveDiv").html('确定要设为公共房源吗？');
  openWin('jss_pop_tip');
  $("#dialog_share").unbind('click').click(function () {
    $.ajax({
      url: "/" + type + "/set_public_house/",
      type: "GET",
      dataType: "json",
      data: {
        house_id: house_id
      },
      success: function (data) {
        if (data['errorCode'] == '401') {
          login_out();
          return false;
        }
        else if (data['errorCode'] == '403') {
          purview_none();
          return false;
        }

        if (data['result'] == 'ok') {
          $("#js_pop_tip").remove();
          $("#dialog_do_itp").html("操作成功");
          openWin('js_pop_do_success');
          location.reload();
        }
      }
    });
  });
}

//详情页删除
function xdel(type, id, fun, is_outside, nature_per) {
  if (0 == nature_per) {
    $("#dialog_do_warnig_tip").html("您不能注销他人私盘");
    openWin('js_pop_do_warning');
    return false;
  }

  var text = id;
  var alert_html = '';

  if ('1' == is_outside) {
    alert_html = '该房源将从平台下架,<br>确定要注销该房源吗';
  } else {
    alert_html = '确定要注销该房源吗';
  }
  $("#dialogSaveDiv").html(alert_html);
  openWin('jss_pop_tip');
  $("#dialog_share").click(function () {
    if ('1' == is_outside) {
      $.ajax({
        url: MLS_URL + "/" + type + "/change_house_is_outside",
        type: "GET",
        data: {
          house_id: id,
          is_outside: 0
        }
      });
    }
    $.ajax({
      url: "/" + type + "/del/",
      type: "GET",
      dataType: "json",
      data: {
        str: text,
        isajax: 1
      },
      success: function (data) {
        if (data['errorCode'] == '401') {
          login_out();
          return false;
        }
        else if (data['errorCode'] == '403') {
          purview_none();
          return false;
        }

        if (data['result'] == 'ok') {
          $("#js_pop_tip").remove();
          window.parent.location.href = "/" + type + "/" + fun + "/";
        }
      }
    });
  });
}

//出售出租设置合作
function sharechange(flag, type, friend) {
  var msg = "";
  var arr = new Array();
  var select_num = 0;
  var text = "";
  var b_ratio = '';
  var textarr = new Array();
  var judge = 1;
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
    textarr[arr[i]] = 'share' + arr[i];
  });
  text = arr.join(",");
  var share_val = 'share_num' + $(".table").find("input:checked[name=items]").val();
  var share_id_val = $("#" + share_val).val();
  var is_report_val = 'is_report' + $(".table").find("input:checked[name=items]").val();
  var is_report_id_val = $("#" + is_report_val).val();
  //状态，是否有效
  var status_val = 'status' + $(".table").find("input:checked[name=items]").val();
  var status_val_id_val = $("#" + status_val).val();

  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  }

  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要合作的房源");
    openWin('js_pop_do_warning');
    return false;
  }
  if (select_num > 1) {
    $("#dialog_do_warnig_tip").html("只能选择一条房源");
    openWin('js_pop_do_warning');
    return false;
  }
  if (share_id_val == 1) {
    $("#dialog_do_warnig_tip").html("该房源已经是合作房源");
    openWin('js_pop_do_warning');
    return false;
  }
  if (share_id_val == 2) {
    $("#dialog_do_warnig_tip").html("该房源已经发送店长审核");
    openWin('js_pop_do_warning');
    return false;
  }
  if (share_id_val == 3) {
    $("#dialog_do_warnig_tip").html("该房源已经发送资料审核");
    openWin('js_pop_do_warning');
    return false;
  }
  //判断是否被举报过
  if (is_report_id_val == 1) {
    $("#dialog_do_warnig_tip").html("该房源已被举报，不能设置合作");
    openWin('js_pop_do_warning');
    return false;
  }
  //判断是否是有效房源
  if (status_val_id_val != 1) {
    $("#dialog_do_warnig_tip").html("该房源非有效房源，不能设置合作");
    openWin('js_pop_do_warning');
    return false;
  }
  //判断是否发布到朋友圈
  if (!friend) {
    friend = 0;
  }

  if (flag == 1) {
    //判断该房源是否存在
    $.ajax({
      url: MLS_URL + "/" + type + "/check_is_exist_house",
      type: "GET",
      dataType: 'json',
      data: {house_id: text},
      success: function (data) {
        if ('success' == data.msg) {
          openWin('js_pop_set_share_warning');
          $("#dialog_share_share").click(function () {
            //记录操作的数据，为当前页的第几条，存入cookie
            var page_id = $('#tr' + text).attr('page_id');
            SetCookie('page_id', page_id);
            $.ajax({
              url: MLS_URL + "/" + type + "/set_share/",
              type: "GET",
              dataType: "json",
              data: {
                str: text,
                flag: flag,
                friend: friend,
                commission_ratio: $("#commission_ratio").val()
              },
              success: function (data) {
                if (data['errorCode'] == '401') {
                  login_out();
                  return false;
                } else if (data['errorCode'] == '403') {
                  closeWindowWin('js_pop_set_share');
                  purview_none();
                  return false;
                }
                if (data['result'] == 'ok') {
                  var aa = 1;
                  $("#" + share_val).val(aa);
                  $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/r_ico.png');
                  $("#dialog_do_warnig_tip").html(data.msg);
                  openWin('js_pop_do_warning');
                  $("#js_pop_set_share").hide();
                  $("#js_pop_set_share_warning").hide();
                  $("#sure_yes").click(function () {
                    $("#GTipsCoverjs_pop_set_share").remove();
                    $("#search_form").submit();
                  })
                  for (var i in data['arr']) {
                    $("#" + textarr[data['arr'][i]]).html("是");
                  }
                } else {
                  $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
                  $("#dialog_do_warnig_tip").html('设置失败');
                  openWin('js_pop_do_warning');
                  $("#js_pop_set_share").hide();
                  $("#GTipsCoverjs_pop_set_share").remove();
                  $(window.parent.document).find("#search_form").submit();
                }
              }
            });

          });
        } else {
          $("#dialog_do_warnig_tip").html("该房源已注销");
          openWin('js_pop_do_warning');
        }
      }
    });
  }
}

//出售出租取消合作
function sharecancel(flag, type, house_id, friend) {
  var msg = "";
  var arr = new Array();
  var select_num = 0;
  var text = "";
  var textarr = new Array();
  if (house_id) {
    arr[0] = house_id;
    select_num++;
    textarr[arr[0]] = 'share' + arr[0];
  } else {
    $(".table").find("input:checked[name=items]").each(function (i) {
      arr[i] = $(this).val();
      select_num++;
      textarr[arr[i]] = 'share' + arr[i];
    });
  }
  text = arr.join(",");
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要取消合作的房源");
    openWin('js_pop_do_warning');
    return false;

  }

  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  }

  if (flag == 0) {
    //判断该房源是否存在
    $.ajax({
      url: MLS_URL + "/" + type + "/check_is_exist_house_str",
      type: "GET",
      dataType: "json",
      data: {house_id_str: text},
      success: function (data) {
        if ('success' == data.msg) {
          $("#dialogSaveDiv").html(msg);
          openWin('js_pop_cancel_share_warning');
          $("#quxiao_share").click(function () {
            //记录操作的数据，为当前页的第几条，存入cookie
            var page_id = $('#tr' + text).attr('page_id');
            SetCookie('page_id', page_id);

            $("#js_pop_cancel_share_warning").hide();
            $.ajax({
              url: MLS_URL + "/" + type + "/cancel_share/",
              type: "GET",
              dataType: "json",
              data: {
                str: text,
                flag: flag,
                friend: friend
              },
              success: function (data) {
                if (data['errorCode'] == '401') {
                  login_out();
                  return false;
                } else if (data['errorCode'] == '403') {
                  closeWindowWin('js_pop_cancel_share_warning');
                  purview_none();
                  return false;
                }
                if (data['result'] == 'ok') {
                  $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/r_ico.png');
                  if (house_id) {
                    $("#dialog_do_warnig_tip").html('下架成功');
                  } else {
                    $("#dialog_do_warnig_tip").html('取消成功');
                  }
                  openWin('js_pop_do_warning');
                  $("#sure_yes").click(function () {
                    $("#js_pop_cancel_share_warning").hide();
                    $("#GTipsCoverjs_pop_cancel_share_warning").remove();
                    $('#search_form').submit();
                  })

                  for (var i in data['arr']) {

                    $("#" + textarr[data['arr'][i]]).html("否");

                  }

                } else {
                  $("#dialog_do_warnig_tip").html(data['msg']);
                  $("#js_pop_cancel_share_warning").hide();
                  $("#GTipsCoverjs_pop_cancel_share_warning").remove();
                  openWin('js_pop_do_warning');
                  $("#js_pop_tip").hide();
                }

                $("#share_ul").hide();
                $("#openList").hide();

              }
            });
          });
        } else {
          $("#dialog_do_warnig_tip").html("该房源已删除");
          openWin('js_pop_do_warning');
        }
      }
    });
  }
}

//合作审核操作
function share_check(type, friend) {
  var msg = "";
  var arr = new Array();
  var text = "";
  var textarr = new Array();
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
  });
  text = arr.join(",");
  var share_val = 'share_num' + $(".table").find("input:checked[name=items]").val();
  var share_id_val = $("#" + share_val).val();
  if (share_id_val == 2) {
    $("#dialog_do_warnig_tip").html("该房源已经发送店长审核");
    openWin('js_pop_do_warning');
    return false;
  }
  if (share_id_val == 3) {
    $("#dialog_do_warnig_tip").html("该房源已经发送资料审核");
    openWin('js_pop_do_warning');
    return false;
  }
  if (share_id_val == 1) {
    $("#dialog_do_warnig_tip").html("该房源已经是合作房源");
    openWin('js_pop_do_warning');
    return false;
  }

  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  }
  //判断是否到朋友圈
  if (!friend) {
    friend = 0;
  }
  //判断该房源是否存在
  $.ajax({
    url: MLS_URL + "/" + type + "/check_is_exist_house_str",
    type: "GET",
    dataType: "json",
    data: {house_id_str: text},
    success: function (data) {
      if ('success' == data.msg) {
        $("#dialogSaveDiv").html(msg);
        openWin('js_pop_set_share_warning');
        $("#dialog_share_share").click(function () {
          $("#js_pop_set_share_warning").hide();
          $.ajax({
            url: MLS_URL + "/" + type + "/set_is_share_2/",
            type: "GET",
            dataType: 'json',
            data: {
              str: text,
              flag: 2,
              friend: friend,
              commission_ratio: $('#commission_ratio').val()
            },
            success: function (result) {
              if ('success' == result.msg) {
                openWin('js_pop_do_warning_share_check');
                $("#sure_yes_share_check").click(function () {
                  $("#js_pop_do_warning_share_check").hide();
                  $('#search_form').submit();
                })
              } else {
                $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
                $("#dialog_do_warnig_tip").html('操作失败');
                openWin('js_pop_do_warning');
                $("#sure_yes").click(function () {
                  $("#js_pop_do_warning").hide();
                  $('#search_form').submit();
                })
              }

            }
          });
        });
      } else {
        $("#dialog_do_warnig_tip").html("该房源已注销");
        openWin('js_pop_do_warning');
      }
    }
  });
}


//出售出租设为公盘、私盘
function naturechange(flag, type) {
  var arr = new Array();
  var select_num = 0;
  var text = "";
  var textarr = new Array();
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
    textarr[arr[i]] = 'nature' + arr[i];
  });
  text = arr.join(",");
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要设置的房源");
    openWin('js_pop_do_warning');
    return false;

  } else if (flag == 1 || flag == 2) {
    var msg = "";
    var url = "";
    if (flag == 1) {
      msg = "确定设为私盘吗？";
      url = MLS_URL + "/" + type + "/set_private/";
    } else if (flag == 2) {
      msg = "确定设为公盘吗？";
      url = MLS_URL + "/" + type + "/set_public/";
    }
    $("#dialogSaveDiv").html(msg);
    openWin('jss_pop_tip');
    $("#dialog_share").click(function () {
      $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        data: {
          str: text,
          flag: flag
        },
        success: function (data) {
          if (data['errorCode'] == '401') {
            login_out();
            $("#js_pop_tip").hide();
          }
          else if (data['errorCode'] == '403') {
            purview_none();
            $("#js_pop_tip").hide();
          } else {
            if (data['result'] == 'ok') {
              $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/r_ico.png');
              $("#dialog_do_warnig_tip").html(data['msg']);
              openWin('js_pop_do_warning');
              $("#jss_pop_tip").hide();
              for (var i in data['arr']) {
                if (flag == 1) {
                  $("#" + textarr[data['arr'][i]]).html("私盘");
                }
                else {
                  $("#" + textarr[data['arr'][i]]).html("公盘");
                }
              }
            } else {
              $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
              $("#dialog_do_warnig_tip").html(data['msg']);
              openWin('js_pop_do_warning');
              $("#js_pop_tip").hide();
              $(window.parent.document).find("#search_form").submit();
            }
          }
          $("#nature_ul").hide();
          $("#openList").hide();
        }
      });
    });
  }
}

//出售出租锁定、解锁
function lockchange(flag, type) {
  var arr = new Array();
  var select_num = 0;
  var text = "";
  var textarr = new Array();
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
    textarr[arr[i]] = 'lock' + arr[i];
  });
  text = arr.join(",");
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要设置的房源");
    openWin('js_pop_do_warning');
    return false;

  } else if (flag == 0 || flag == 1) {
    var msg = "";
    var url = "";
    if (flag == 0) {
      msg = "确定解锁房源吗？";
      url = MLS_URL + "/" + type + "/set_unlock/";
    } else if (flag == 1) {
      msg = "确定锁定房源吗？";
      url = MLS_URL + "/" + type + "/set_lock/";
    }
    $("#dialogSaveDiv").html(msg);
    openWin('jss_pop_tip');
    $("#dialog_share").click(function () {
      $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        data: {
          str: text,
          flag: flag
        },
        success: function (data) {
          if (data['errorCode'] == '401') {
            login_out();
            $("#js_pop_tip").hide();
          }
          else if (data['errorCode'] == '403') {
            purview_none();
            $("#js_pop_tip").hide();
          } else {
            if (data['result'] == 'ok') {
              $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/r_ico.png');
              $("#dialog_do_warnig_tip").html(data['msg']);
              openWin('js_pop_do_warning');
              $("#jss_pop_tip").hide();
              $("#sure_yes").click(function () {
                $('#search_form').submit();
              })
            } else {
              $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
              $("#dialog_do_warnig_tip").html(data['msg']);
              openWin('js_pop_do_warning');
              $("#js_pop_tip").hide();
            }
          }

          $("#lock_ul").hide();
          $("#openList").hide();
        }
      });
    });
  }
}

//点击获取客源姓名
function opensource(type) {
  $('.input_t').val("");
  $(window.parent.document).find("#kputid").show();
  var cname = $("input[name=radio3]:checked").val();
  var _id = $("input[name=radio3]:checked").siblings(".js_hidden_val").val();
  //带看客户
  if (1 == type) {
    $(window.parent.document).find("#cn_id").val(_id);
    $(window.parent.document).find("#cn_id_f").val('');
    $(window.parent.document).find("#kputid").html(cname);
    $(window.parent.document).find("#kputid_2").html('');
  } else if (2 == type) {
    //封盘 商谈客户
    $(window.parent.document).find("#cn_id").val('');
    $(window.parent.document).find("#cn_id_f").val(_id);
    $(window.parent.document).find("#kputid").html('');
    $(window.parent.document).find("#kputid_2").html(cname);
  }
  $(window.parent.document).find("#js_keyuan").find(".iframePop").attr('src', '');
  $(window.parent.document).find("#js_keyuan").hide().end().find("#GTipsCoverjs_keyuan").remove();
}

//点击获取经纪人姓名
function opensource_broker(type) {
  $('.input_t').val("");
  $(window.parent.document).find("#kputid").show();
  var cname = $("input[name=radio3]:checked").val();
  var _id = $("input[name=radio3]:checked").siblings(".js_hidden_val").val();
  //商谈经纪人
  $(window.parent.document).find("#broker_id_f").val(_id);
  $(window.parent.document).find("#kputid_broker").html(cname);

  $(window.parent.document).find("#js_keyuan").find(".iframePop").attr('src', '');
  $(window.parent.document).find("#js_keyuan").hide().end().find("#GTipsCoverjs_keyuan").remove();
}

//加入收藏
function shcang(type, dbname, rw_id, from) {
  var controller = '';
  if ('sell_house' == dbname) {
    controller = 'sell';
  } else {
    controller = 'rent';
  }
  //判断该房源是否存在
  $.ajax({
    url: MLS_URL + "/" + controller + "/check_is_qualified_house",
    type: "GET",
    dataType: 'json',
    data: {house_id: rw_id},
    success: function (data) {
      if ('success' == data.msg) {
        var row_id = parseInt(rw_id);
        $("#dialogSaveDiv").html("确定要收藏该房源吗？");
        openWin('jss_pop_tip');

        $("#dialog_share").click(function () {
          $.ajax({
            url: "/" + type + "/add_collect/",
            type: "GET",
            dataType: "json",
            data: {'row_id': row_id, 'dbname': dbname},
            success: function (data) {
              if (data['is_ok'] == 2) {
                $("#dialog_do_warnig_tip").html("你已经收藏过该房源");
                openWin('js_pop_do_warning');
                $("#jss_pop_tip").hide();
              }
              else if (data['is_ok'] == 1) {
                if (from != 'info') {
                  $("#cang" + row_id).html("已收藏");
                  $('#cang' + row_id).css('color', '#b2b2b2');
                  $('#cang' + row_id).css('text-decoration', 'none');
                  $('#cang' + row_id).removeAttr("onclick");
                  $("#jss_pop_tip").hide();
                  $("#dialog_share").attr("alt", type);
                  $("#dialog_do_itp").html("收藏成功");
                  openWin('js_pop_do_success');
                  $("#dialog_share").attr("rel", flag);
                } else {
                  $("#cang_info" + row_id).html("已收藏");
                  $('#cang_info' + row_id).css('text-decoration', 'none');
                  $('#cang_info' + row_id).removeAttr("onclick");
                  $('#cang_info' + row_id).removeClass("collect");
                  $('#cang_info' + row_id).addClass("collect-success");
                  $(window.parent.document).find("#cang" + row_id).html("已收藏");
                  $(window.parent.document).find("#cang" + row_id).css('color', '#b2b2b2');
                  $(window.parent.document).find("#cang" + row_id).css('text-decoration', 'none');
                  $(window.parent.document).find("#cang" + row_id).removeAttr("onclick");
                }
              }
            }
          });
        });
      } else {
        $("#dialog_do_warnig_tip").html("该房源为非有效、合作房源");
        openWin('js_pop_do_warning');
      }
    }
  });
}

//同步
function fang100(type, flag, rw_id) {
  var house_id = $("#right_id").val();
  //判断所选房源是否都存在
  $.ajax({
    url: MLS_URL + "/" + type + "/check_is_exist_house",
    type: "GET",
    dataType: 'json',
    data: {house_id: house_id},
    success: function (data) {
      var msg = "";
      var url = "";
      if (flag == 1) {
        msg = "确定同步至平台吗？";
        url = MLS_URL + "/" + type + "/fang100/";
      } else if (flag == 0) {
        msg = "确定从平台下架吗？";
        url = MLS_URL + "/" + type + "/fang100/";
      }
      $("#dialogSaveDiv").html(msg);
      openWin('jss_pop_tip');
      $("#dialog_share").click(function () {
        $.ajax({
          url: url,
          type: "GET",
          dataType: 'json',
          data: {
            house_id: house_id,
            flag: flag
          },
          success: function (data) {
            var message = data.msg;
            if (message.indexOf('-')) {
              var arr_message = message.split('-');
              message = arr_message[0];
            }
            if ('fang100_success' == message) {
              $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/r_ico.png');
              if (arr_message[1]) {
                var text = '操作成功,+' + arr_message[1] + '积分';
                if (arr_message[2]) {
                  text = text + ' +' + arr_message[2] + '成长值';
                }
                $("#dialog_do_warnig_tip").html(text);
              }
              else {
                $("#dialog_do_warnig_tip").html('操作成功');
              }
              if (flag == 1) {
                $("#fang100" + house_id).css("display", "inline");
              } else {
                $("#fang100" + house_id).css("display", "none");
              }
              openWin('js_pop_do_warning');
            } else if ('fang100_failed' == message) {
              $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
              $("#dialog_do_warnig_tip").html('操作失败');
              openWin('js_pop_do_warning');
            } else if ('is_outside_1' == message) {
              $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
              $("#dialog_do_warnig_tip").html('该房源已同步');
              openWin('js_pop_do_warning');
            } else if ('is_outside_0' == message) {
              $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
              $("#dialog_do_warnig_tip").html('该房源已从平台下架');
              openWin('js_pop_do_warning');
            } else if ('status_failed' == message) {
              $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
              $("#dialog_do_warnig_tip").html('该房源为非有效房源');
              openWin('js_pop_do_warning');
            } else if ('group_id_1' == message) {
              $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
              $("#dialog_do_warnig_tip").html('您未认证，不可使用此功能');
              openWin('js_pop_do_warning');
            } else if ('no_purview' == message) {
              purview_none();
              $("#js_pop_tip").hide();
            }
          }
        });
      });

    }
  });
}

//平安好房房源同步
function pingan_data_deal(type, broker_id) {
  var house_id = $("#right_id").val();
  //获得房源所在的楼盘数据
  $.ajax({
    url: MLS_URL + "/" + type + "/get_data_by_house_id",
    type: "GET",
    data: {'house_id': house_id},
    dataType: "json",
    success: function (data) {
      $.ajax({
        url: MLS_URL + "/pinganhaofang/post_all_data/",
        type: "GET",
        dataType: "json",
        data: data,
        success: function (data) {
          if (data.code != 'success') {
            $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
            $("#dialog_do_warnig_tip").html(data.msg);
            openWin('js_pop_do_warning');
          } else {
            $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/r_ico.png');
            $("#dialog_do_warnig_tip").html('操作成功');
            openWin('js_pop_do_warning');
          }
        }
      });
    }
  });
}

//平安好房数据下架
function pingan_data_down() {
  var id = $("#right_id").val();
  $.ajax({
    url: MLS_URL + "/pinganhaofang/house_down/",
    type: "GET",
    data: {'house_id': id},
    dataType: "json",
    success: function (data) {
      if (data.code != 'success') {
        $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
        $("#dialog_do_warnig_tip").html(data.msg);
        openWin('js_pop_do_warning');
      } else {
        $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/r_ico.png');
        $("#dialog_do_warnig_tip").html('操作成功');
        openWin('js_pop_do_warning');
      }
    }
  });
}


$('.JS_Close').bind('click', function () {
  $('#dialog_share').unbind('click');
});

//取消收藏
function qucang(type, dbname, rw_id) {
  var row_id = parseInt(rw_id);
  $("#dialogSaveDiv").html("确定要取消收藏该房源吗？");
  openWin('jss_pop_tip');

  $("#dialog_share").click(function () {
    $.ajax({
      url: "/" + type + "/cancle_collect/",
      type: "GET",
      dataType: "json",
      data: {row_id: row_id, dbname: dbname},

      success: function (data) {
        if (data['is_ok'] == 1) {
          $("#dialog_do_itp").html("取消成功");
          $("#tr" + row_id).remove();
          openWin('js_pop_do_success');
          $("#jss_pop_tip").hide();
        }
      }
    });
  });
}


//出租房源列表页 排序
function renntlist_order(id) {
  var orderby_id = $("#orderby_id").val();
  var other_id = id + 1;
  if (orderby_id == id) {
    $("#orderby_id").val(other_id);
    $("#search_form").submit();
  }
  else {
    $("#orderby_id").val(id);
    $("#search_form").submit();
  }
}

//出售房源列表页 排序
function selllist_order(id) {
  var orderby_id = $("#orderby_id").val();
  $("#orderby_id").val(other_id);
  var other_id = id + 1;
  if (orderby_id == id) {
    $("#orderby_id").val(other_id);
    $("#search_form").submit();
  }
  else {
    $("#orderby_id").val(id);
    $("#search_form").submit();
  }
}

//查看出租隐私内容
function show_rentbaomi_info(house_id, type) {
  house_id = parseInt(house_id);
  if (type == 'sell') {
    var danwei = '万元';
  } else {
    var danwei = '元/月';
  }
  $.ajax({
    url: '/' + type + '/get_secret_info/',
    type: 'GET',
    dataType: 'JSON',
    data: {house_id: house_id},
    success: function (data) {
      //判断返回数据是否为空，不为空返回数据。
      if (data.id > 0) {
        $.ajax({
          url: MLS_URL + '/broker/check_baomi_time/2/' + house_id,
          dataType: 'json',
          type: 'GET',
          success: function (msg) {
            if (msg.success) {
              $('#dong').html(data.dong);
              $('#unit').html(data.unit);
              $('#door').html(data.door);
              $('#owner').html(data.owner);
              $('#lowprice').html(data.lowprice + danwei);
              $('#telnos').html(data.telno1);
              $('#telnos2').html(data.telno2);
              $('#telnos3').html(data.telno3);
              //$('.link_btn_b').attr('style','display:none;');
              $('#modify_baomi_button').show();
              $('#show_baomi_button').hide();
              //记录查看日志，并根据基本设置检测和跟进进程是否结束
              var result = add_brower_log(type, house_id);
              if ('add_success' == result.msg) {
                $('#rent_house_match').attr('class', 'btn-hui fr');
                $('#rent_house_share_tasks').attr('class', 'btn-hui fr');
                $('#rent_allocate_house').attr('class', 'btn-hui fr');
                $('#rent_zhuxiao').hide();
                $('#rent_bianji').hide();
                $('.mask_bg2').show();
                $(window.parent.document).find("#window_min_click").attr('class', 'close_pop iconfont');
                $(window.parent.document).find("#window_min_close").attr('class', 'close_pop iconfont');
                $(window.parent.document).find("#window_min_close").attr("id", 'window_min_close_2');
                $(window.parent.document).find("#window_min_click").attr("id", 'window_min_click_2');
                window.parent.show_noneClick();
              }
            } else {
              $("#dialog_do_warnig_tip").html("已达当天查看次数上限");
              openWin('js_pop_do_warning');
            }
          }
        });
      }
      else {
        //是否封盘
        if ('is_seal' == data['errorCode']) {
          $('.link_btn_b').html(data['seal_msg']);
        } else {
          $('.link_btn_b').html('很遗憾，您无权查看相关保密信息。');
        }
      }
    }
  });
}
//查看出售的隐私内容
function show_baomi_info(house_id, type) {
  house_id = parseInt(house_id);
  if (type == 'sell') {
    var danwei = '万元';
  } else {
    var danwei = '元/月';
  }
  $.ajax({
    url: '/' + type + '/get_secret_info/',
    type: 'GET',
    dataType: 'JSON',
    data: {house_id: house_id},
    success: function (data) {
      //判断返回数据是否为空，不为空返回数据。
      if (data.id > 0) {
        $.ajax({
          url: MLS_URL + '/broker/check_baomi_time/1/' + house_id,
          dataType: 'json',
          type: 'GET',
          success: function (msg) {
            if (msg.success) {
              $('#dong').html(data.dong);
              $('#unit').html(data.unit);
              $('#door').html(data.door);
              $('#owner').html(data.owner);
              $('#lowprice').html(data.lowprice + danwei);
              $('#telnos').html(data.telno1);
              $('#telnos2').html(data.telno2);
              $('#telnos3').html(data.telno3);
              $('#idcare').html(data.idcare);
              $('#proof').html(data.proof);
              $('#mound_num').html(data.mound_num);
              $('#record_num').html(data.record_num);
              //$('.link_btn_b').attr('style','display:none;');
              $('#modify_baomi_button').show();
              $('#show_baomi_button').hide();
              var result = add_brower_log(type, house_id);
              //判断基本设置是否开启了查看保密信息必须写跟进，按钮置灰，添加遮罩
              if ('add_success' == result.msg) {
                $('#sell_house_match').attr('class', 'btn-hui fr');
                $('#sell_house_share_tasks').attr('class', 'btn-hui fr');
                $('#sell_allocate_house').attr('class', 'btn-hui fr');
                $('#sell_zhuxiao').hide();
                $('#sell_bianji').hide();
                $('.mask_bg2').show();
                $(window.parent.document).find("#window_min_click").attr('class', 'close_pop iconfont');
                $(window.parent.document).find("#window_min_close").attr('class', 'close_pop iconfont');
                $(window.parent.document).find("#window_min_close").attr("id", 'window_min_close_2');
                $(window.parent.document).find("#window_min_click").attr("id", 'window_min_click_2');
                window.parent.show_noneClick();
              }
            } else {
              $("#dialog_do_warnig_tip").html("已达当天查看次数上限");
              openWin('js_pop_do_warning');
            }
          }
        });
      }
      else {
        //是否封盘
        if ('is_seal' == data['errorCode']) {
          $('.link_btn_b').html(data['seal_msg']);
        } else {
          $('.link_btn_b').html('很遗憾，您无权查看相关保密信息。');
        }
      }
    }
  });
}

//添加房源浏览记录
function add_brower_log(type, house_id) {
  var result = '';
  $.ajax({
    url: '/' + type + '/add_brower_log/',
    type: 'GET',
    dataType: 'json',
    async: false,
    data: {house_id: house_id},
    success: function (data) {
      result = data;
    }
  });
  return result;
}

//房源列表页 区属联动
function districtchange(districtid) {
  $.ajax({
    type: 'get',
    url: '/sell/find_street_bydis/' + districtid,
    dataType: 'json',
    success: function (msg) {
      var str = '';
      if (msg.result == 'no result') {
        str = '<option value="">不限</option>';
      } else {
        str = '<option value="">不限</option>';
        for (var i = 0; i < msg.length; i++) {
          str += '<option value="' + msg[i].id + '">' + msg[i].streetname + '</option>';
        }
      }
      $('#street').empty();
      $('#street').append(str);
    }
  });
}

function tab_fun(t_obj, i_obj, fn) {//tab切换
  $("#" + t_obj).find(".js_t").each(function (index, element) {
    $(this).click(function () {
      $(this).addClass("itemOn").siblings(".js_t").removeClass("itemOn");
      $("#" + i_obj).find(".js_d").eq(index).show().siblings(".js_d").hide();
      if (fn) {
        fn();
      }
      ;
    });
  });
};

//右键查看详情
function openHouseDetails(type, is_pub) {
  var house_id = $("#right_id").val();
  var _url = '/' + type + '/details_house/' + house_id + '/' + is_pub;
  if (_url) {
    $("#js_pop_box_g .iframePop").attr("src", _url);
  }
  openWin('js_pop_box_g');
}

//右键房源打印
function house_print(type) {
  var house_id = $("#right_id").val();
  var _url = '/' + type + '/house_print/' + house_id;
  if (_url) {
    $("#js_house_print .iframePop").attr("src", _url);
  }
  openWin('js_house_print');
}

//添加跟进信息
function openfollow(type, num) {
  var house_id = $("#right_id").val();
  var _url = '/' + type + '/follow/' + house_id + '/' + num;
  if (_url) {
    $("#js_genjin .iframePop").attr("src", _url);
  }
  openWin('js_genjin');
}

//新房源跟进页面
function open_follow(type, num) {
  var house_id = $("#right_id").val();
  var _url = '/' + type + '/house_follow/' + house_id + '/' + num;
  if (_url) {
    $("#js_genjin .iframePop").attr("src", _url);
  }
  openWin('js_genjin');
}

//详情页添加跟进信息
function xq_openfollow(type, house_id, num) {
  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  }
  var my_task = $("#my_task").val();
  if (my_task) {
    if (type == 'sell' || type == 'rent') {
      var _url = '/' + type + '/house_follow/' + house_id + '/' + num;
    } else {
      var _url = '/' + type + '/customer_follow/' + house_id + '/' + num;
    }
  } else {
    var _url = '/' + type + '/house_follow/' + house_id + '/' + num;
  }
  if (_url) {
    $("#js_genjin .iframePop").attr("src", _url);
  }
  openWin('js_genjin');
}


//链接跟进按钮
function openn_follow(type, num) {
  var arr = new Array();
  var select_num = 0;

  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });

  if (select_num > 1) {
    $("#dialog_do_warnig_tip").html("请选择一条房源跟进");
    openWin('js_pop_do_warning');
    return false;
  }
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要跟进的房源");
    openWin('js_pop_do_warning');
    return false;
  }
  var house_id = arr[0];
  var _url = '/' + type + '/follow/' + house_id + '/' + num;

  if (_url) {
    $("#js_genjin .iframePop").attr("src", _url);
  }
  openWin('js_genjin');
}


//举报页面弹跳
function report_mes(type, house_id, broker_id) {
  //判断该房源是否存在
  $.ajax({
    url: MLS_URL + "/" + type + "/check_is_qualified_house",
    type: "GET",
    dataType: 'json',
    data: {house_id: house_id},
    success: function (data) {
      if ('success' == data.msg) {
        var _url = '/' + type + '/report/' + house_id + '/' + broker_id;
        if (_url) {
          $("#js_woyaojubao .iframePop").attr("src", _url);
        }
        openWin('js_woyaojubao');
      } else {
        $("#dialog_do_warnig_tip").html("该房源为非有效、合作房源");
        openWin('js_pop_do_warning');
      }
    }
  });
}
//收藏举报页面弹跳
function report_messhou(type, house_id, broker_id) {
  var _url = '/' + type + '/report/' + house_id + '/' + broker_id;
  if (_url) {
    $("#js_woyaojubao .iframePop").attr("src", _url);
  }
  openWin('js_woyaojubao');
}
//群发
function group_publish(type, house_id) {
  var text = "";
  if (house_id) {
    text = house_id;
  } else {
    var arr = new Array();
    $(".table").find("input:checked[name=items]").each(function (i) {
      arr[i] = $(this).val();
    });
    text = arr.join("%7C");
  }

  if (text) {
    var _url = '/' + type + '/group_site/' + text;
    if (_url) {
      $("#js_pop_box_g .iframePop").attr("src", _url);
    }
    openWin('js_pop_box_g');
  }
}

//关闭群发iframe
function close_group_publish() {
  $("#js_pop_box_g .iframePop").attr("src", '');
  $("#js_pop_box_g").hide();
  $('#GTipsCoverjs_pop_box_g').remove();
}

//立即发布
function group_publishing(type) {
  var house_id = $("#house_id").val();
  var arr = Array();
  var sitearr = '';
  $("input:checked[name=site]").each(function (i) {
    arr[i] = $(this).val();
  });
  sitearr = arr.join("%7C");

  var _url = '/' + type + '/group_publishing/' + house_id + '%7C%7C' + sitearr;
  window.parent.close_group_publish();
  window.parent.open_publishing(_url);
}

//打开立即发布弹框
function open_publishing(url) {
  $("#js_pop_box_g_publishing .iframePop").attr("src", url);
  openWin('js_pop_box_g_publishing');
}

/**
 * 房源管理导出部分
 * @author    kang
 */
$(function () {
  $("input[name='ch']").change(function () {
    //获取选择的导出方式
    var ch = $("input[name='ch']:checked").val();
    if (ch == 3) {
      $("input[name='start_page']").attr("disabled", false);
      $("input[name='end_page']").attr("disabled", false);
    } else {
      $("input[name='start_page']").attr("disabled", true);
      $("input[name='end_page']").attr("disabled", true);
    }
  });
});

//出售房源点击导出按钮时
function sell_click_export() {
  var arr = new Array();
  var select_num = 0;

  //重置按钮及输入框的值
  $("input[name='ch']").attr("checked", false);
  $("input[name='start_page']").val('');
  $("input[name='end_page']").val('');
  $("input[name='start_page']").attr("disabled", true);
  $("input[name='end_page']").attr("disabled", true);

  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });
  $("input[name='con1']").val(select_num);
  $("input[name='con2']").val(select_num);
  openWin('js_sell_export');
}
//出租房源点击导出按钮时
function rent_click_export() {
  var arr = new Array();
  var select_num = 0;

  //重置按钮及输入框的值
  $("input[name='ch']").attr("checked", false);
  $("input[name='start_page']").val('');
  $("input[name='end_page']").val('');
  $("input[name='start_page']").attr("disabled", true);
  $("input[name='end_page']").attr("disabled", true);

  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });
  $("input[name='con1']").val(select_num);
  $("input[name='con2']").val(select_num);
  openWin('js_rent_export');
}

//点击确定导出按钮后的操作
function sub_export_btn() {
  //先去除form的target属性
  //$("#myform").removeAttr('target');
  //先判断是否选择一种导出方式
  var ch = $("input[name='ch']:checked").val();
  if (!ch) {
    $(".span_msg").html("请选择导出种类！");
    $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
    openWin("js_pop_msg");
    //alert('请选择导出种类');
    return;
  }
  //如果是第一种方式，则要传递所选ID以获取数据
  if (ch == 1) {
    var arr = new Array();
    var select_num = 0;

    $(".table").find("input:checked[name=items]").each(function (i) {
      arr[i] = $(this).val();
      select_num++;
    });
    //alert(arr);return;
    if (select_num > 0) {
      var form_data = $("#search_form").serialize();

      $("input[name='final_data']").val(form_data);
      $("input[name='ch_1_data']").val(arr);
      $("#myform").submit();
      return false;
    } else {
      $(".span_msg").html("您还没有选择要导出的房源哦！如您想导出所有房源可以选择导出当前页所有房源或者导出多页房源！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      //alert('请选择要导出的数据！');
    }
    return;
  }
  //如果是第三种方式，则获取页码范围
  if (ch == 3) {
    var start_page = $("input[name='start_page']").val();
    var end_page = $("input[name='end_page']").val();
    var n = end_page - start_page;
    //查询当前页面的总页数
    var pages = $("input[name='hid_total_page']").val();

    if (!start_page || !end_page || parseInt(start_page) < 1 || parseInt(end_page) < 1) {
      $(".span_msg").html("请填写正确的页码范围！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      return false;
    }

    if (parseInt(start_page) > parseInt(end_page)) {
      $(".span_msg").html("起始页数不能大于终止页数！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      return false;
    }

    if (parseInt(end_page) > parseInt(pages)) {
      $(".span_msg").html("页数超出范围，无法导出！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      return false;
    }

    if (n > 10) {
      $(".span_msg").html("页码范围不能超过10页！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      //alert('页码范围不能超过10页！');
      return;
    }

  }
  /*else {
   var start_page = "";
   var end_page = "";
   }*/

  var form_data = $("#search_form").serialize();

  $("input[name='final_data']").val(form_data);
  $("#myform").submit();
}

//点击确定导出按钮后的操作
function sub_export_btn_2() {
  var form_data = $("#search_form").serialize();
  $("input[name='final_data']").val(form_data);
  $("#myform").submit();
}


//出售房源选择打印预览时
function sub_print_btn() {
  //先判断是否选择一种打印方式
  var ch = $("input[name='ch']:checked").val();
  if (ch != 4 && ch != 5 && ch != 6) {
    $(".span_msg").html("请选择打印方式！");
    $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
    openWin("js_pop_msg");
    //alert('请选择打印方式');
    return;
  }
  //判断是否有选取数据
  var arr = new Array();
  var select_num = 0;
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });

  //根据打印类型选择提交的数据
  if (ch == 4) {
    if (select_num > 0) {
      var form_data = $("#search_form").serialize();

      /*$("input[name='final_data']").val(form_data);
       $("input[name='ch_1_data']").val(arr);
       $("#myform").submit();*/

      $.post("/sell/exportReport", {final_data: form_data, ch_1_data: arr, ch: ch}, function (data) {
        /* var dt = JSON.stringify(data);
         $("#hid_data").val(dt);
         $("#hid_form").submit();*/
        if (data.status == 1) {
          window.open("/sell/print_hid_one");
        }
      }, "json");

    } else {
      $(".span_msg").html("请先选择数据！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      //alert('请先选择数据！');
      return;
    }
  } else if (ch == 5) {
    if (select_num > 0) {
      var con2 = $("input[name='con2']").val();
      if (con2 > select_num) {
        $(".span_msg").html("要打印的条数超出您所选条数！");
        $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
        openWin("js_pop_msg");
        //alert('要打印的条数超出您所选条数！');
        return;
      }
      var form_data = $("#search_form").serialize();

      /*$("input[name='final_data']").val(form_data);
       $("input[name='ch_1_data']").val(arr);
       $("#myform").submit();*/
      $.post("/sell/exportReport", {final_data: form_data, ch_1_data: arr, ch: ch, con2: con2}, function (data) {
        /*var dt = JSON.stringify(data);
         $("#hid_data1").val(dt);
         $("#hid_form1").submit();*/
        if (data.status == 1) {
          window.open("/sell/print_hid_two");
        }
      }, "json");

    } else {
      $(".span_msg").html("请先选择数据！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      //alert('请先选择数据！');
      return;
    }
  } else if (ch == 6) {
    var new_arr;
    if (select_num >= 1) {
      new_arr = arr[0];
    } else {
      $(".span_msg").html("请先选择数据！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      //alert('请先选择数据！');
      return;
    }
    var form_data = $("#search_form").serialize();

    //alert(new_arr);
    $.post("/sell/exportReport", {final_data: form_data, ch_1_data: new_arr, ch: ch}, function (data) {
      /* var dt = JSON.stringify(data);
       $("#hid_data2").val(dt);
       $("#hid_form2").submit(); */
      if (data.status == 1) {
        window.open("/sell/print_hid_three");
      }
    }, "json");

  } else {
  }

}

//出租房源打印预览
function sub_rent_print_btn() {
  //先判断是否选择一种打印方式
  var ch = $("input[name='ch']:checked").val();
  if (ch != 4 && ch != 5 && ch != 6) {
    $(".span_msg").html("请选择打印方式！");
    $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
    openWin("js_pop_msg");
    //alert('请选择打印方式');
    return;
  }
  //判断是否有选取数据
  var arr = new Array();
  var select_num = 0;
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });

  //根据打印类型选择提交的数据
  if (ch == 4) {
    if (select_num > 0) {
      var form_data = $("#search_form").serialize();

      $.post("/rent/exportReport", {final_data: form_data, ch_1_data: arr, ch: ch}, function (data) {
        /*var dt = JSON.stringify(data);
         $("#hid_data").val(dt);
         $("#hid_form").submit();*/
        if (data.status == 1) {
          window.open("/rent/print_hid_one");
        }
      }, "json");

    } else {
      $(".span_msg").html("请先选择数据！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      //alert('请先选择数据！');
      return;
    }
  } else if (ch == 5) {
    if (select_num > 0) {
      var con2 = $("input[name='con2']").val();
      if (con2 > select_num) {
        $(".span_msg").html("要打印的条数超出您所选条数！");
        $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
        openWin("js_pop_msg");
        //alert('要打印的条数超出您所选条数！');
        return;
      }
      var form_data = $("#search_form").serialize();

      $.post("/rent/exportReport", {final_data: form_data, ch_1_data: arr, ch: ch, con2: con2}, function (data) {
        /*var dt = JSON.stringify(data);
         $("#hid_data1").val(dt);
         $("#hid_form1").submit();*/
        if (data.status == 1) {
          window.open("/rent/print_hid_two");
        }
      }, "json");

    } else {
      $(".span_msg").html("请先选择数据！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      //alert('请先选择数据！');
      return;
    }
  } else if (ch == 6) {
    var new_arr;
    if (select_num >= 1) {
      new_arr = arr[0];
    } else {
      $(".span_msg").html("请先选择数据！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      //alert('请先选择数据！');
      return;
    }
    var form_data = $("#search_form").serialize();

    $.post("/rent/exportReport", {final_data: form_data, ch_1_data: new_arr, ch: ch}, function (data) {
      /*var dt = JSON.stringify(data);
       $("#hid_data2").val(dt);
       $("#hid_form2").submit();*/
      if (data.status == 1) {
        window.open("/rent/print_hid_three");
      }
    }, "json");

  } else {
  }

}


//右键分配任务
function ringt_tasks(type, num) {
  var house_id = $("#right_id").val();
  var _url = '/' + type + '/share_tasks/' + house_id + '/' + num;

  if (_url) {
    $("#js_fenpeirenwu .iframePop").attr("src", _url);
  }
  openWin('js_fenpeirenwu');
}

//点击分配任务
function click_tasks(type, num) {
  var house_id = '';
  var arr = new Array();
  var select_num = 0;
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });
  house_id = arr.join("%7C");

  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要分配的房源");
    openWin('js_pop_do_warning');
    return false;
  }
  else {
    var _url = '/' + type + '/share_tasks/' + house_id + '/' + num;
    if (_url) {
      $("#js_fenpeirenwu .iframePop").attr("src", _url);
    }
    openWin('js_fenpeirenwu');
  }
}

//点击分配房源
function allocate_house(type) {
  var house_id = '';
  var arr = new Array();
  var select_num = 0;
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });
  house_id = arr.join("_");

  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要分配的房源");
    openWin('js_pop_do_warning');
    return false;
  }
  else {
    var _url = '/' + type + '/allocate_house/' + house_id;
    if (_url) {
      $("#js_allocate_house .iframePop").attr("src", _url);
    }
    openWin('js_allocate_house');
  }
}

//详情页分配任务
function xqtasks(type, num, house_id) {
  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  }
  var _url = '/' + type + '/share_tasks/' + house_id + '/' + num;
  if (_url) {
    $("#js_fenpeirenwu .iframePop").attr("src", _url);
  }
  openWin('js_fenpeirenwu');

}

//详情页分配房源
function xqhouse(type, house_id) {
  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  }
  var _url = '/' + type + '/allocate_house/' + house_id;

  if (_url) {
    $("#js_allocate_house .iframePop").attr("src", _url);
  }

  openWin('js_allocate_house');
}


//申请客源合作
function cooperate_customer(kind, c_id) {
  var customer_id = parseInt(c_id);
  $param = '?customer_id=' + customer_id + '&kind=' + kind;
  var _url = MLS_URL + '/cooperate/apply_customer_cooperate_window/' + $param;
  if (_url) {
    $("#js_pop_box_cooperation .iframePop").attr("src", _url);
  }

  openWin('js_pop_box_cooperation');
}
