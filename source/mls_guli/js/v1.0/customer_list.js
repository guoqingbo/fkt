function SetCookie(name, value)//
{
  var Days = 30; //此 cookie 将被保存 30 天
  var exp = new Date();    //new Date("December 31, 9998");
  exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
  document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
}

//列表批量操作函数（设置合作、取消合作、删除）
function submit_all(actiontype, kind, mark, id) {
  var arr = new Array();
  var select_num = 0;
  var ids = '';
  if (id > 0) {
    ids = id;
    select_num = 1;
  }
  else {
    $(".table").find("input:checked[name=customer_id]").each(function (i) {
      arr[i] = $(this).val();
      select_num++;
    });

    var arr = unique(arr);
    ids = arr.join("_");
  }

  if (ids) {
    //判断所选房源是否都存在
    $.ajax({
      url: MLS_URL + "/" + kind + "/check_is_exist_house_str",
      type: "GET",
      dataType: "json",
      data: {customer_id_str: ids},
      success: function (data) {
        if ('success' == data.msg) {
          var exist_ids = data.exist_ids2;
          if (actiontype == "delete_customer") {
            if (mark == 1) {
              //关闭当前确认窗口
              close_confirm_window();

              //ajax提交删除操作
              del_customer_by_ids(exist_ids, kind);
            }
            else {
              if (select_num > 1) {
                confirm("确定要删除选定的" + select_num + "条记录吗？", actiontype, kind, exist_ids);
              }
              else {
                confirm("确定要删除选定的记录吗？", actiontype, kind, exist_ids);
              }
            }
          }
          else if (actiontype == "set_share") {
            if (mark == 1) {
              //关闭当前确认窗口
              close_confirm_window();

              //判断所选客源是否已经是合作客源、是否被举报过
              //如果所选客源都是合作客源，弹出提示框。只要有一个是非合作状态，发送ajax请求。
              ids_arr = exist_ids.split(',');
              is_share_result = 1;//默认所选客源都是合作客源
              is_report_result = 1;//默认所选的非合作客源都已经被举报
              is_select_arr = new Array();
              no_cooperate_is_report_arr = new Array();//所选的非合作客源是否被举报结果
              no_cooperate_no_report_ids = '';//所选的非合作客源字符串
              for (var i = 0; i < ids_arr.length; i++) {
                var is_share = $(".table").find("#share_num" + ids_arr[i]).val();
                var is_report = $(".table").find("#is_report" + ids_arr[i]).val();
                if (is_share == 0) {
                  no_cooperate_is_report_arr.push(is_report);
                  //筛选所有的非合作未被举报的客源
                  if (is_report == 0) {
                    no_cooperate_no_report_ids += ids_arr[i] + ',';
                  }
                }
                is_select_arr[i] = is_share;
              }
              if (no_cooperate_no_report_ids != '') {
                var new_true_ids = no_cooperate_no_report_ids.substring(0, no_cooperate_no_report_ids.length - 1);
              }
              //判断所选客源是否都是合作客源
              for (var j = 0; j < is_select_arr.length; j++) {
                if (is_select_arr[j] != 1) {
                  is_share_result = 0;
                  break;
                }
              }
              //判断所选非合作客源是否都被举报
              for (var j = 0; j < no_cooperate_is_report_arr.length; j++) {
                if (no_cooperate_is_report_arr[j] == 0) {
                  is_report_result = 0;
                  break;
                }
              }
              if (is_share_result == 1) {
                $("#dialog_do_warnig_tip").html('该客源已是合作客源');
                openWin('js_pop_do_warning');
              } else {
                if (is_report_result == 1) {
                  $("#dialog_do_warnig_tip").html('该客源被举报，不能设置合作');
                  openWin('js_pop_do_warning');
                } else {
                  //ajax设置合作
                  set_share_by_ids(new_true_ids, kind);
                }
              }
            }
            else {
              if (select_num > 1) {
                confirm("确定要合作选定的" + select_num + "条客源合作吗？<br><br>合作后您的客源将开放给全网经纪人，助您快速成交！", actiontype, kind, exist_ids);
              }
              else {
                confirm("确定要合作选定的客源吗？<br><br>合作后该客源将开放给全网经纪人，助您快速成交！", actiontype, kind, exist_ids);
              }
            }
          }
          else if (actiontype == "cancle_share") {
            if (mark == 1) {
              //关闭当前确认窗口
              close_confirm_window();

              //判断所选客源是否已经是非合作客源
              //如果所选客源都是非合作客源，弹出提示框。只要有一个是合作状态，发送ajax请求。
              ids_arr = exist_ids.split(',');
              is_share_result = 0;//默认所选客源都是非合作客源
              is_share_arr = new Array();
              true_ids = '';//所选的合作客源
              for (var i = 0; i < ids_arr.length; i++) {
                var is_share = $(".table").find("#share_num" + ids_arr[i]).val();
                if (is_share != 0) {
                  true_ids += ids_arr[i] + ',';
                }
                is_share_arr[i] = is_share;
              }
              if (true_ids != '') {
                var new_true_ids = true_ids.substring(0, true_ids.length - 1);
              }
              for (var j = 0; j < is_share_arr.length; j++) {
                if (is_share_arr[j] != 0) {
                  is_share_result = 1;
                  break;
                }
              }
              if (is_share_result == 0) {
                $("#dialog_do_warnig_tip").html('该客源已是非合作客源');
                openWin('js_pop_do_warning');
              } else {
                //ajax取消合作
                cancle_share_by_ids(new_true_ids, kind);
              }

            }
            else {
              if (select_num > 1) {
                confirm("您确定要将选定的" + select_num + "条客源取消合作吗？<br><br>取消合作后您的客源信息将错过全网经纪人，可能会影响您快速成交，再考虑下吧！", actiontype, kind, exist_ids);
              }
              else {
                confirm("您确定要将此客源取消合作吗？<br><br>取消合作后该客源信息将错过全网经纪人，可能会影响客源快速成交，再考虑下吧！", actiontype, kind, exist_ids);
              }
            }
          }

        } else {
          $("#dialog_do_warnig_tip").html("该客源已注销");
          openWin('js_pop_do_warning');
        }
      }
    });


  }
  else {
    $("#dialog_do_warnig_tip").html("请先选定要操作的信息！");
    openWin('js_pop_do_warning');
  }

  return false;
}

//确认弹框
function confirm(tips, act, kind, ids) {
  $("#dialogSaveDiv").html(tips);
  openWin("jss_pop_tip");
  $('#dialog_share').bind('click', function () {
    //记录操作的数据，为当前页的第几条，存入cookie
    var page_id = $('#tr' + ids).attr('page_id');
    SetCookie('page_id', page_id);
    submit_all(act, kind, 1, ids);
  });
}

//删除客源信息
function del_customer_by_ids(ids, kind) {
  openWin('docation_loading');
  $.getJSON(
    '/' + kind + '/del_customerinfo_by_ids/',
    {'customer_ids': ids},
    function (data) {
      close_doaction_loading();
      if (data['errorCode'] == '401') {
        login_out();
        $("#jss_pop_tip").hide();
      }
      else if (data['errorCode'] == '403') {
        /*purview_none();
         $("#jss_pop_tip").hide();
         //closeWindowWin("jss_pop_tip");*/
        $("#dialog_do_itp").html('对不起，您没有访问权限！');
        openWin('js_pop_do_success');
      } else {
        if (data.result == 1) {
          $("#dialog_do_itp").html(data.msg);
          $("input[name='page']").val(1);
          openWin('js_pop_do_success');
        }
        else if (data.result == 0) {
          $("#dialog_do_itp").html(data.msg);
          openWin('js_pop_do_success');
        }
        else {
          $("#dialog_do_itp").html('异常错误');
          openWin('js_pop_do_success');
        }
      }
    }
  );
}
//详情页删除
function del_customer_details(ids, kind, per_public_type) {
  if (0 == per_public_type) {
    $("#dialog_do_warnig_tip").html("您不能注销他人私有客源");
    openWin('js_pop_do_warning');
    return false;
  }

  $("#dialogSaveDiv").html("确定要注销该客源吗？");
  openWin('jss_pop_tip');

  $("#dialog_share").click(function () {
    $.getJSON(
      '/' + kind + '/del_customerinfo_by_ids/',
      {'customer_ids': ids},
      function (data) {
        if (data['errorCode'] == '401') {
          login_out();
          $("#jss_pop_tip").hide();
        }
        else if (data['errorCode'] == '403') {
          /*purview_none();
           $("#jss_pop_tip").hide();
           //closeWindowWin("jss_pop_tip");*/
          $("#dialog_do_itp").html('对不起，您没有访问权限！');
          openWin('js_pop_do_success');
        } else {
          if (data.result == 1) {
            $("#dialog_do_itp").html(data.msg);
            $("input[name='page']").val(1);
            openWin('js_pop_do_success');
          }
          else if (data.result == 0) {
            $("#dialog_do_warnig_tip").html(data.msg);
            openWin('js_pop_do_warning');
          }
          else {
            var msg = '异常错误';
            $("#dialog_do_warnig_tip").html('异常错误');
            openWin('js_pop_do_warning');
          }
        }
      }
    );
  });
}

//求购客源列表页 排序
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

//设置客源合作
function set_share_by_ids(ids, kind) {
  openWin('docation_loading');
  $.getJSON(
    '/' + kind + '/set_customer_share/',
    {'customer_ids': ids},
    function (data) {
      close_doaction_loading();
      if (data['errorCode'] == '401') {
        login_out();
        $("#jss_pop_tip").hide();
      }
      else if (data['errorCode'] == '403') {
        /*purview_none();
         $("#jss_pop_tip").hide();
         //closeWindowWin("jss_pop_tip");*/
        $("#dialog_do_itp").html('对不起，您没有访问权限！');
        openWin('js_pop_do_success');
      } else {
        if (data.result == 1) {
          $("#dialog_do_itp").html(data.msg);
          openWin('js_pop_do_success');
        }
        else if (data.result == 0) {
          $("#dialog_do_itp").html(data.msg);
          openWin('js_pop_do_success');
        }
        else {
          $("#dialog_do_itp").html('异常错误');
          openWin('js_pop_do_success');
        }
      }
    }
  );
}


//设置客源合作
function cancle_share_by_ids(ids, kind) {
  openWin('docation_loading');
  $.getJSON(
    '/' + kind + '/cancle_customer_share/',
    {'customer_ids': ids},
    function (data) {
      close_doaction_loading();
      if (data['errorCode'] == '401') {
        login_out();
        $("#jss_pop_tip").hide();
      }
      else if (data['errorCode'] == '403') {
        /*purview_none();
         $("#jss_pop_tip").hide();
         closeWindowWin("jss_pop_tip");*/
        $("#dialog_do_itp").html('对不起，您没有访问权限！');
        openWin('js_pop_do_success');
      } else {
        if (data.result == 1) {
          $("#dialog_do_itp").html(data.msg);
          openWin('js_pop_do_success');
        }
        else if (data.result == 0) {
          $("#dialog_do_itp").html(data.msg);
          openWin('js_pop_do_success');
        }
        else {
          $("#dialog_do_itp").html('异常错误');
          openWin('js_pop_do_warning');
        }
      }
    }
  );
}

/*去掉数组重复项----------------*/
function unique(data) {
  data = data || [];
  var a = {};
  len = data.length;

  for (var i = 0; i < len; i++) {
    var v = data[i];
    if (typeof(a[v]) == 'undefined') {
      a[v] = 1;
    }
  }

  data.length = 0;
  for (var i in a) {
    data[data.length] = i;
  }

  return data;
}

//提交表单
function sub_form(form_name) {
  form_name = typeof(form_name) == 'undefined' ? 'search_form' : form_name;
  $('input[name=page]').val(1);
  $('#' + form_name).submit();
}

//重置表单
function reset_form() {
  $('#search_form')[0].reset()
}

//关闭确认窗口
function close_confirm_window() {
  $("#gTipsCoverdialogtc").remove();
  $("#js_pop_tip").hide();
}

//关闭loading状态弹框
function close_doaction_loading() {
  $('#docation_loading').hide();
}

//右键删除客源信息
function del_customer_right(type) {
  var c_id = $("#right_id").val();
  submit_all('delete_customer', type, 0, c_id);
}

//右键设置客源信息合作
function set_share_right(type) {
  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  } else {
    var c_id = $("#right_id").val();
    submit_all('set_share', type, 0, c_id);
  }
}

//右键取消客源信息合作
function cancle_share_right(type) {
  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  } else {
    var c_id = $("#right_id").val();
    submit_all('cancle_share', type, 0, c_id);
  }
}

//合作审核操作
function share_check(type) {
  var msg = "";
  var c_id = $("#right_id").val();
  var share_val = $(".table").find("#share_num" + c_id).val();
  if (share_val == 2) {
    $("#dialog_do_warnig_tip").html("该客源已经发送审核");
    openWin('js_pop_do_warning');
    return false;
  }
  if (share_val == 1) {
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

  //判断该房源是否存在
  $.ajax({
    url: MLS_URL + "/" + type + "/check_is_exist_house_str",
    type: "GET",
    dataType: "json",
    data: {customer_id_str: c_id},
    success: function (data) {
      if ('success' == data.msg) {
        openWin('js_pop_set_share_warning2');
        $("#dialog_share_share2").click(function () {
          $("#js_pop_set_share_warning2").hide();
          $.ajax({
            url: MLS_URL + "/" + type + "/set_is_share_2/",
            type: "GET",
            dataType: 'json',
            data: {
              str: c_id,
              flag: 2
            },
            success: function (result) {
              if ('success' == result.msg) {
                openWin('js_pop_do_warning_share_check');
                $("#sure_yes_share_check").click(function () {
                  $("#js_pop_do_warning_share_check").hide();
                  $('#search_form').submit();
                })
              } else {
                $("#dialog_do_warnig_tip").html('操作失败');
                openWin('js_pop_do_warning');
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

//打开匹配页面
function open_match_customer(type, is_public, customer_id) {
  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  }
  var arr = new Array();
  var select_num = 0;

  if (customer_id > 0) {
    var customerid = customer_id;
  }
  else {
    $(".table").find("input:checked[name=customer_id]").each(function (i) {
      arr[i] = $(this).val();
      select_num++;
    });

    if (select_num > 1 || select_num == 0) {
      $("#dialog_do_warnig_tip").html('智能匹配必须选择一条客源');
      openWin('js_pop_do_warning');
      return false;
    }

    var customerid = arr[0];
  }
  //from = (!from && typeof(from)!="undefined" && from!=0) ? 1  : from;
  var _url = '/' + type + '/match/' + customerid + '/';
  if (is_public > 0) {
    _url = _url + '/' + is_public;
  }
  if (_url) {
    $("#js_pop_box_g_match .iframePop").attr("src", _url);
  }

  openWin('js_pop_box_g_match');
}


//打开匹配页面
function open_match_right(type, is_public) {
  var customer_id = $("#right_id").val();
  var _url = MLS_URL + '/' + type + '/match/' + customer_id;

  if (is_public > 0) {
    _url = _url + '/' + is_public;
  }
  if (_url) {
    $("#js_pop_box_g_match .iframePop").attr("src", _url);
  }

  openWin('js_pop_box_g_match');
}

//设为公共房源
function set_public_customer(type, customer_id) {

  $("#dialogSaveDiv").html('确定要设为公共客源吗？');
  openWin('jss_pop_tip');
  $("#dialog_share").unbind('click').click(function () {
    $.ajax({
      url: "/" + type + "/set_public_customer/",
      type: "GET",
      dataType: "json",
      data: {
        customer_id: customer_id
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

//自动联想小区名称
$(function () {
  $.widget("custom.autocomplete", $.ui.autocomplete, {
    _renderItem: function (ul, item) {
      if (item.id > 0) {
        return $("<li>")
          .data("item.autocomplete", item)
          .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">' + item.label + '</span><span class="ui_district">' + item.districtname + '</span><span class="ui_address">' + item.address + '</span></a>')
          .appendTo(ul);
      }
      else {
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

        $('#cmt_id').val(id);
        removeinput = 2;
      }
      else {
        removeinput = 1;
      }
    },
    close: function (event) {
      if (typeof(removeinput) == 'undefined' || removeinput == 1) {
        $(this).val('');
        $('#cmt_id').val('');
      }
    }
  });
  $("#block01").on("autocompletechange", function (event, ui) {
      $('#cmt_id').val('');
    }
  );
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
      $("#" + child_object_id).append("<option selected='' value='0'>不限</option>");
      $.each(data, function (i, item) {
        var child_option = "<option value=" + item.id + ">" + item.streetname + "</option>";
        $("#" + child_object_id).append(child_option);
      });
    }
  );
}


//门店、经纪人二级联动
function get_broker_by_agencyid(obj, child_object_id) {
  var agency_id = parseInt($(obj).val());

  $.getJSON(
    '/agency/get_brokerinfo_by_agencyid/',
    {'agency_id': agency_id},
    function (data) {
      if (data == 'errorCode401') {
        jump_win('', '请重新登录');
        return false;
      }

      $("#" + child_object_id).empty();
      $("#" + child_object_id).append("<option selected='' value='0'>不限</option>");
      $.each(data, function (i, item) {
        var child_option = "<option value=" + item.broker_id + ">" + item.truename + "</option>";
        $("#" + child_object_id).append(child_option);
      });
    }
  );
}

//收藏
function collect_customer(c_id, type, from) {
  var customer_id = parseInt(c_id);
  var controller = '';
  if ('buy_customer' == type) {
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
        if (customer_id == 0) {
          $("#dialog_do_warnig_tip").html("请选择需要收藏的信息！");
          openWin('js_pop_do_warning');
          return false;
        }

        $("#dialogSaveDiv").html("确定要收藏该客源吗？");
        openWin('jss_pop_tip');

        $("#dialog_share").click(function () {
          $.getJSON(
            MLS_URL + '/customer_collect/add_collect_customer/',
            {'customer_id': customer_id, 'type': type},
            function (data) {
              if (data['is_ok'] == 1) {
                if (from != 'info') {
                  $('#collect_' + customer_id).css('color', '#b2b2b2');
                  $('#collect_' + customer_id).css('text-decoration', 'none');
                  $('#collect_' + customer_id).html(data['msg']);
                  $('#collect_' + customer_id).removeAttr("onclick");
                  $("#dialog_do_itp").html(data['msg']);
                  openWin('js_pop_do_success');
                }
                else if (from == 'info') {
                  $('#collect_info' + customer_id).css('text-decoration', 'none');
                  $('#collect_info' + customer_id).css('text-decoration', 'none');
                  $('#collect_info' + customer_id).removeAttr("onclick");
                  $('#collect_info' + customer_id).removeClass("collect");
                  $('#collect_info' + customer_id).html("已收藏");
                  $('#collect_info' + customer_id).addClass("collect-success");
                  $("#dialog_do_itp").html(data['msg']);
                  $(window.parent.document).find('#collect_' + customer_id).html(data['msg']);
                  $(window.parent.document).find('#collect_' + customer_id).css('color', '#b2b2b2');
                  $(window.parent.document).find('#collect_' + customer_id).css('text-decoration', 'none');
                  $(window.parent.document).find('#collect_' + customer_id).removeAttr("onclick");
                  //openWin('js_pop_do_success');
                }
              }
              else {
                $("#dialog_do_warnig_tip").html(data['msg']);
                openWin('js_pop_do_warning');
                return false;
              }
            }
          );
        });
      } else {
        $("#dialog_do_warnig_tip").html("该客源为非有效、合作客源");
        openWin('js_pop_do_warning');
      }
    }
  });


}

$('.JS_Close').bind('click', function () {
  $('#dialog_share').unbind('click');
});

//取消收藏
function cancle_collect_customer(collect_id, type) {
  var collect_id = parseInt(collect_id);

  $("#dialogSaveDiv").html("确定要取消收藏该客源吗？");
  openWin('jss_pop_tip');

  $("#dialog_share").click(function () {
    $.getJSON(
      MLS_URL + '/customer_collect/cancle_collect_customer/',
      {'type': type, 'collect_id': collect_id},
      function (data) {
        if (data['is_ok'] == 1) {
          $("#dialog_do_itp").html(data['msg']);
          openWin('js_pop_do_success');
          $('#collect_' + customer_id).removeAttr("onclick");
        }
        else {
          $("#dialog_do_warnig_tip").html(data['msg']);
          openWin('js_pop_do_warning');
          return false;
        }
      }
    );
  });
  event.stopPropagation();
}

//合作
function cooperate_customer(buy_customer, c_id) {
  $param = '?customer_id=' + customer_id + '&kind=' + buy_customer;
  var _url = MLS_URL + '/cooperate/apply_customer_cooperate_window/' + $param;

  if (_url) {
    $("#js_pop_box_cooperation .iframePop").attr("src", _url);
  }

  openWin('js_pop_box_cooperation');
}

//添加跟进信息
function openfollow(type, num) {
  var customer_id = $("#right_id").val();
  var _url = MLS_URL + '/' + type + '/follow/' + customer_id + '/' + num;
  if (_url) {
    $("#js_genjin .iframePop").attr("src", _url);
  }
  openWin('js_genjin');
}

//客源跟进
function open_follow(type, num) {
  var customer_id = $("#right_id").val();
  var _url = MLS_URL + '/' + type + '/customer_follow/' + customer_id + '/' + num;
  if (_url) {
    $("#js_genjin .iframePop").attr("src", _url);
  }
  openWin('js_genjin');
}

//详情页添加跟进信息
function xq_openfollow(type, customer_id, num) {
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
      var _url = '/' + type + '/house_follow/' + customer_id + '/' + num;
    } else {
      var _url = '/' + type + '/customer_follow/' + customer_id + '/' + num;
    }
  } else {
    var _url = '/' + type + '/customer_follow/' + customer_id + '/' + num;
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

  $(".table").find("input:checked[name=customer_id]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });

  if (select_num > 1) {
    $("#dialog_do_warnig_tip").html("请选择一条客源跟进");
    openWin('js_pop_do_warning');
    return false;
  }
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要跟进的客源");
    openWin('js_pop_do_warning');
    return false;
  }
  var customer_id = arr[0];
  var _url = MLS_URL + '/' + type + '/follow/' + customer_id + '/' + num;

  if (_url) {
    $("#js_genjin .iframePop").attr("src", _url);
  }

  openWin('js_genjin');
}

//举报页面弹跳
function report_mes(type, customer_id) {
  //判断该客源是否存在
  $.ajax({
    url: MLS_URL + "/" + type + "/check_is_qualified_house",
    type: "GET",
    dataType: 'json',
    data: {customer_id: customer_id},
    success: function (data) {
      if ('success' == data.msg) {
        var _url = MLS_URL + '/' + type + '/report/' + customer_id;

        if (_url) {
          $("#js_woyaojubao .iframePop").attr("src", _url);
        }
        openWin('js_woyaojubao');
      } else {
        $("#dialog_do_warnig_tip").html("该客源为非有效、合作客源");
        openWin('js_pop_do_warning');
      }
    }
  });
}

/**
 * 导出客源求购部分
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
    if (ch == 4) {
      $("input[name='myoffset']").attr("disabled", false);
      $("input[name='mylimit']").attr("disabled", false);
    } else {
      $("input[name='myoffset']").attr("disabled", true);
      $("input[name='mylimit']").attr("disabled", true);
    }
  });
});

//点击求购客源中的“报表导出”按钮时
function click_buy_customer_export() {
  //重置按钮及输入框的值
  $("input[name='ch']").attr("checked", false);
  $("input[name='start_page']").val('');
  $("input[name='end_page']").val('');
  $("input[name='start_page']").attr("disabled", true);
  $("input[name='end_page']").attr("disabled", true);
  openWin('js_buy_export');
}

//点击求租客源中的“报表导出”按钮时
function click_rent_customer_export() {
  //重置按钮及输入框的值
  $("input[name='ch']").attr("checked", false);
  $("input[name='start_page']").val('');
  $("input[name='end_page']").val('');
  $("input[name='start_page']").attr("disabled", true);
  $("input[name='end_page']").attr("disabled", true);
  openWin('js_rent_export')
}

//点击确定按钮后的操作
function sub_export_btn() {
  //先判断是否选择一种导出方式
  var ch = $("input[name='ch']:checked").val();
  if (!ch) {
    //alert('请选择导出种类');
    $(".span_msg").html("请选择导出种类！");
    $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
    openWin("js_pop_msg");
    return;
  }
  //如果是第一种方式，则要传递所选ID以获取数据
  if (ch == 1) {
    var arr = new Array();
    var select_num = 0;

    $(".table").find("input:checked[name=customer_id]").each(function (i) {
      arr[i] = $(this).val();
      select_num++;
    });

    if (select_num > 0) {
      var form_data = $("#search_form").serialize();
      $("input[name='final_data']").val(form_data);
      $("input[name='ch_1_data']").val(arr);
      $("#myform").submit();
    } else {
      //alert('请选择要导出的数据！');
      $(".span_msg").html("您还没有选择要导出的客源哦！如您想导出所有客源可以选择导出当前页所有客源或者导出多页客源！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
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
      //alert('页码范围不能超过10页！');
      $(".span_msg").html("页码范围不能超过10页！");
      $(".img_msg").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
      openWin("js_pop_msg");
      return;
    }
  } else if (ch == 2) {
    var start_page = "";
    var end_page = "";
  } else if (ch == 4) {
    $('#mylimit2').val($('#mylimit').val());
    $('#myoffset2').val($('#myoffset').val());
  }

  var form_data = $("#search_form").serialize();
  $("input[name='final_data']").val(form_data);
  $("#myform").submit();
}


//求购求租设为公客、私客
function public_type_change(flag, type) {
  var arr = new Array();
  var select_num = 0;
  var text = "";
  $(".table").find("input:checked[name=customer_id]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });
  var arr1 = unique(arr);
  text = arr1.join("_");
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要设置的客源");
    openWin('js_pop_do_warning');
    return false;

  } else if (flag == 1 || flag == 2) {
    var msg = "";
    var url = "";
    if (flag == 1) {
      msg = "确定设为私客吗？";
      url = MLS_URL + "/" + type + "/set_private/";
    } else if (flag == 2) {
      msg = "确定设为公客吗？";
      url = MLS_URL + "/" + type + "/set_public/";
    }
    $("#dialogSaveDiv").html(msg);
    openWin('jss_pop_tip');
    $('#dialog_share').bind('click', function () {
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
            $("#jss_pop_tip").hide();
          }
          else if (data['errorCode'] == '403') {
            purview_none();
            $("#jss_pop_tip").hide();
          } else {
            if (data['result'] == 'ok') {
              $("#dialog_do_itp").html(data['msg']);
              openWin('js_pop_do_success');
              $("#jss_pop_tip").hide();
            } else {
              $("#dialog_do_warnig_tip").html(data['msg']);
              openWin('js_pop_do_warning');
              $("#jss_pop_tip").hide();
            }
          }

          $("#public_type_ul").hide();
          $("#openList").hide();
        }
      });
    });
  }
}

//点击确定导出按钮后的操作
function sub_export_btn_2() {
  var form_data = $("#search_form").serialize();
  $("input[name='final_data']").val(form_data);
  $("#myform").submit();
}

//求购求租锁定、解锁
function lockchange(flag, type) {
  var arr = new Array();
  var select_num = 0;
  var text = "";
  $(".table").find("input:checked[name=customer_id]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });
  var arr1 = unique(arr);
  text = arr1.join("_");
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要设置的客源");
    openWin('js_pop_do_warning');
    return false;

  } else if (flag == 0 || flag == 1) {
    var msg = "";
    var url = "";
    if (flag == 0) {
      msg = "确定解锁客源吗？";
      url = MLS_URL + "/" + type + "/set_unlock/";
    } else if (flag == 1) {
      msg = "确定锁定客源吗？";
      url = MLS_URL + "/" + type + "/set_lock/";
    }
    $("#dialogSaveDiv").html(msg);
    openWin('jss_pop_tip');
    $('#dialog_share').bind('click', function () {
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
            $("#jss_pop_tip").hide();
          }
          else if (data['errorCode'] == '403') {
            purview_none();
            $("#jss_pop_tip").hide();
          } else {
            if (data['result'] == 'ok') {
              $("#dialog_do_itp").html(data['msg']);
              openWin('js_pop_do_success');
              $("#jss_pop_tip").hide();
            } else {
              $("#dialog_do_warnig_tip").html(data['msg']);
              openWin('js_pop_do_warning');
              $("#jss_pop_tip").hide();
            }
          }

          $("#lock_ul").hide();
          $("#openList").hide();
        }
      });
    });
  }
}

$('.JS_Close').bind('click', function () {
  $('#dialog_btn').unbind('click');
});

//点击分配客源
function allocate_customer(type) {
  var customer_id = '';
  var arr = new Array();
  var select_num = 0;
  $(".table").find("input:checked[name=customer_id]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });
  customer_id = arr.join("_");
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要分配的客源");
    openWin('js_pop_do_warning');
    return false;
  } else {
    var _url = '/' + type + '/allocate_customer/' + customer_id;
    if (_url) {
      $("#js_allocate_customer .iframePop").attr("src", _url);
    }
    openWin('js_allocate_customer');
  }
}

//右键分配任务
function ringt_tasks(type, num) {
  var customer_id = $("#right_id").val();
  var _url = '/' + type + '/share_tasks/' + customer_id + '/' + num;
  if (_url) {
    $("#js_fenpeirenwu .iframePop").attr("src", _url);
  }
  openWin('js_fenpeirenwu');
}

//点击分配任务
function click_tasks(type, num) {
  var customer_id = '';
  var arr = new Array();
  var select_num = 0;

  $(".table").find("input:checked[name=customer_id]").each(function (i) {
    arr[i] = $(this).val();
    select_num++;
  });

  customer_id = arr.join("_");

  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("请选择要分配的客源");
    openWin('js_pop_do_warning');
    return false;
  }
  else {
    var _url = '/' + type + '/share_tasks/' + customer_id + '/' + num;

    if (_url) {
      $("#js_fenpeirenwu .iframePop").attr("src", _url);
    }
    openWin('js_fenpeirenwu');
  }
}

//详情页分配任务
function xqtasks(type, num, customer_id) {
  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  }
  var _url = '/' + type + '/share_tasks/' + customer_id + '/' + num;

  if (_url) {
    $("#js_fenpeirenwu .iframePop").attr("src", _url);
  }

  openWin('js_fenpeirenwu');
}

//详情页分配客源
function xqcustomer(type, customer_id) {
  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  }
  var _url = '/' + type + '/allocate_customer/' + customer_id;

  if (_url) {
    $("#js_allocate_customer .iframePop").attr("src", _url);
  }

  openWin('js_allocate_customer');
}

//点击获取房源编号
function opensource() {
  $('.input_t').val("");
  $(window.parent.document).find("#kputid").show();
  var cname = $("input[name=radio3]:checked").val();
  var _id = $("input[name=radio3]:checked").siblings(".js_hidden_val").val();
  $(window.parent.document).find("#house_id").val(_id);
  $(window.parent.document).find("#kputid").html(cname);
  $(window.parent.document).find("#js_keyuan").find(".iframePop").attr('src', '');
  $(window.parent.document).find("#js_keyuan").hide().end().find("#GTipsCoverjs_keyuan").remove();
}
