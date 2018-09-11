//群发 站点选择
function house_publish(type, house_id) {
  //经纪人所属用户组（是否认证）
  var group_id = $('#group_id').val();
  var pub_first = $('#pub_first').val() || 0;  //外援标记
  if ('1' == group_id) {
    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
    openWin('js_pop_do_warning');
    return false;
  } else {
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
    //判断是否有权限群发他人房源
    if (text) {
      $.ajax({
        type: 'get',
        url: '/group_publish_' + type + '/check_publish_other_house/' + text,
        dataType: 'json',
        success: function (msg) {
          if (msg == 0) {
            $('#dialog_do_warnig_tip').html('没有群发他人房源的权限');
            openWin('js_pop_do_warning');
          } else if (msg == 1) { //可以发布房源
            if (pub_first == '1') {
              var _url = '/group_publish_' + type + '/group_site/' + text;
              if (_url) {
                $("#js_pop_box_g_publishing_first .iframePop").attr("src", _url);
              }
              openWin('js_pop_box_g_publishing_first');
            } else {
              var _url = '/group_publish_' + type + '/group_site/' + text;
              if (_url) {
                $("#js_pop_box_g .iframePop").attr("src", _url);
              }
              openWin('js_pop_box_g');
            }
          }
        }
      });
    }
  }
}

//外援 右键群发
function publish_before(type) {
  var house_id = $("#right_id").val();
  house_publish(type, house_id);
}

//一键 发布
function house_publishing(type) {
  var house_id = $("#houseids").val();
  var arr = Array();
  var sitearr = '';
  $("input:checked[name=sites]").each(function (i) {
    arr[i] = $(this).val();
  });
  sitearr = arr.join("%7C");
  if (sitearr) {
    var _url = '/group_publish_' + type + '/group_publishing/' + house_id + '%7C%7C' + sitearr;
    window.parent.close_house_publish();
    window.parent.open_house_publishing(_url);
  }
}

//一键 发布 加入队列 +++++++++++++ type 1发布 2刷新 3下架
function house_add_queue(act, type) {
  var house_id = $("#houseids").val();
  var arr = new Array();
  var sitearr = '';
  $("input:checked[name=sites]").each(function (i) {
    arr[i] = $(this).val();
  });
  sitearr = arr.join("%7C");
  if (sitearr) {
    $.ajax({
      type: 'get',
      url: '/group_site_deal/add2queue/' + act + '/' + house_id + '%7C%7C' + sitearr,
      dataType: 'json',
      data: {type: type},
      success: function (data) {
        if (act == 'rent') {
          window.parent.location.href = "/group_site_deal/queue_rent/";
        } else {
          window.parent.location.href = "/group_site_deal/queue_sell/";
        }
//        		window.parent.openWin('js_queue_succ_pop');
//        		window.parent.close_house_publish();
      }
    });
  }
}


//批量 刷新 队列
function house_refresh_all(act, type) {
  var text = "";
  var arr = new Array();
  var sitearr = '';
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
  });
  text = arr.join("%7C");

  if (text) {
    $.ajax({
      type: 'get',
      url: '/group_publish_' + act + '/check_publish_other_house/' + text,
      dataType: 'json',
      success: function (msg) {
        if (msg == 0) {
          $('#dialog_do_warnig_tip').html('没有刷新他人房源的权限');
          openWin('js_pop_do_warning');
        } else if (msg == 1) { //可以刷新房源
          $.ajax({
            type: 'get',
            url: '/group_site_deal/add2queue/' + act + '/' + text + '%7C%7C' + sitearr,
            dataType: 'json',
            data: {refresh_all: 1, type: type},
            success: function (data) {
              window.location.href = "/group_site_deal/queue_sell/";
//				        		window.openWin('js_queue_succ_pop');
//				        		window.close_house_publish();
            }
          });
        }
      }
    });
  }
}
//刷新 站点选择
function house_refer(type, house_id) {
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
    $.ajax({
      type: 'get',
      url: '/group_publish_' + type + '/check_publish_other_house/' + text,
      dataType: 'json',
      success: function (msg) {
        if (msg == 0) {
          $('#dialog_do_warnig_tip').html('没有刷新他人房源的权限');
          openWin('js_pop_do_warning');
        } else if (msg == 1) { //可以刷新房源
          var _url = '/group_publish_' + type + '/refer_site/' + text;
          if (_url) {
            $("#js_pop_box_g .iframePop").attr("src", _url);
          }
          openWin('js_pop_box_g');
        }
      }
    });
  }
}
//一键刷新
function house_refering(type) {
  var house_id = $("#houseids").val();
  var arr = Array();
  var sitearr = '';
  $("input:checked[name=sites]").each(function (i) {
    arr[i] = $(this).val();
  });
  sitearr = arr.join("%7C");
  if (sitearr) {
    var _url = '/group_publish_' + type + '/group_refering/' + house_id + '%7C%7C' + sitearr;
    window.parent.close_house_publish();
    window.parent.open_house_publishing(_url);
  }
}

//下架 站点选择
function house_esta(type, house_id) {
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
    $.ajax({
      type: 'get',
      url: '/group_publish_' + type + '/check_publish_other_house/' + text,
      dataType: 'json',
      success: function (msg) {
        if (msg == 0) {
          $('#dialog_do_warnig_tip').html('没有下架他人房源的权限');
          openWin('js_pop_do_warning');
        } else if (msg == 1) { //可以下架房源
          var _url = '/group_publish_' + type + '/esta_site/' + text;
          if (_url) {
            $("#js_pop_box_g .iframePop").attr("src", _url);
          }
          openWin('js_pop_box_g');
        }
      }
    });
  }
}
//一键下架
function house_estaing(type) {
  var house_id = $("#houseids").val();
  var arr = Array();
  var sitearr = '';
  $("input:checked[name=sites]").each(function (i) {
    arr[i] = $(this).val();
  });
  sitearr = arr.join("%7C");
  if (sitearr) {
    var _url = '/group_publish_' + type + '/group_estaing/' + house_id + '%7C%7C' + sitearr;
    window.parent.close_house_publish();
    window.parent.open_house_publishing(_url);
  }
}

//预约刷新 -打开- 站点选择
function pro_refresh(type, house_id, site_id) {
  $.ajax({
    type: 'get',
    url: '/group_publish_' + type + '/check_publish_other_house/' + house_id,
    dataType: 'json',
    success: function (msg) {
      if (msg == 0) {
        $('#dialog_do_warnig_tip').html('没有预约刷新他人房源的权限');
        openWin('js_pop_do_warning');
      } else if (msg == 1) { //可以预约刷新房源
        var _url = '/group_site_deal/pro_refresh_site/' + type + '/' + house_id + '/' + site_id;
        $("#js_pop_refer_choose .iframePop").attr("src", _url);
        openWin("js_pop_refer_choose");
      }
    }
  });
}
//预约刷新 -关闭- 站点选择
function close_pro_site() {
  $("#js_pop_refer_choose .iframePop").attr("src", '');
  $("#js_pop_refer_choose").hide();
  $('#GTipsCoverjs_pop_refer_choose').remove();
}
//预约刷新 -打开- 时间选择
function open_pro_time(url) {
  $("#js_pop_refer_time .iframePop").attr("src", url);
  openWin('js_pop_refer_time');
}
//预约刷新 -关闭- 时间选择
function close_pro_time() {
  $("#js_pop_refer_time .iframePop").attr("src", '');
  $("#js_pop_refer_time").hide();
  $('#GTipsCoverjs_pop_refer_time').remove();
}
//预约刷新 -打开- 成功失败
function open_pro_msg(type, house_id) {
  var _url = '/group_site_deal/pro_refresh_msg/' + type + '/' + house_id;
  $("#js_pop_refer_msg .iframePop").attr("src", _url);
  openWin("js_pop_refer_msg");
}
//预约刷新 -关闭- 成功失败
function close_pro_msg(type, house_id) {
  $("#js_pop_refer_msg .iframePop").attr("src", '');
  $("#js_pop_refer_msg").hide();
  $('#GTipsCoverjs_pop_refer_msg').remove();
//    var html = '<a href="javascript:void(0);" onclick="pro_refresh(\''+type+'\','+house_id+',0);" class="fun_link pro_ref">预约刷新</a>';
//    $("#tr"+house_id).find(".pro_ref").replaceWith(html);
}
//预约刷新：改变为 已预约 状态
function change_pro_link(house_id, type) {
  var html = '<a href="javascript:void(0);" onclick="pro_refresh(\'' + type + '\',' + house_id + ',0);" class="fun_link">已预约</a>';
  $("#tr" + house_id).find(".pro_ref").replaceWith(html);
}


//打开 群发、刷新、下架 队列
function open_house_publishing(url) {
  $("#js_pop_box_g_publishing .iframePop").attr("src", url);
  openWin('js_pop_box_g_publishing');
}
//关闭 群发、刷新、下架
function close_house_publish() {
  $("#js_pop_box_g .iframePop").attr("src", '');
  $("#js_pop_box_g").hide();
  $('#GTipsCoverjs_pop_box_g').remove();
}
//打开 查看详情
function group_info(type, house_id) {
  var _url = '/group_publish_' + type + '/publish_info/' + house_id;
  $("#js_pop_box_g_publishinfo .iframePop").attr("src", _url);
  openWin('js_pop_box_g_publishinfo');
}
//关闭 查看详情
function close_group_info() {
  $("#js_pop_box_g_publishinfo .iframePop").attr("src", '');
  $("#js_pop_box_g_publishinfo").hide();
  $('#GTipsCoverjs_pop_box_g_publishinfo').remove();
}
