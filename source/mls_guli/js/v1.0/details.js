//列表批量操作函数
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
    ids = arr.join(",");
  }

  if (ids) {
    if (actiontype == "delete_customer") {
      if (mark == 1) {
        //关闭当前确认窗口
        close_confirm_window();

        //ajax提交删除操作
        del_customer_by_ids(ids, kind);
      }
      else {
        if (select_num > 1) {
          confirm("确定要删除选定的" + select_num + "条记录吗？", actiontype, kind, ids);
        }
        else {
          confirm("确定要删除此客源吗？", actiontype, kind, ids);
        }
      }
    }
    else if (actiontype == "set_share") {
      if (mark == 1) {
        //关闭当前确认窗口
        close_confirm_window();

        //ajax设置合作
        set_share_by_ids(ids, kind);
      }
      else {
        if (select_num > 1) {
          confirm("确定要合作选定的" + select_num + "条客源合作吗？<br><br>合作后您的客源将开放给全网经纪人，助您快速成交！", actiontype, kind, ids);
        }
        else {
          confirm("确定要合作选定的客源吗？<br><br>合作后您的客源将开放给全网经纪人，助您快速成交！", actiontype, kind, ids);
        }
      }
    }
    else if (actiontype == "cancle_share") {
      if (mark == 1) {
        //关闭当前确认窗口
        close_confirm_window();

        //ajax取消合作
        cancle_share_by_ids(ids, kind);
      }
      else {
        if (select_num > 1) {
          confirm("您确定要将选定的" + select_num + "条客源取消合作吗？<br><br>取消合作后您的客源信息将错过全网经纪人，可能会迎新爱国您快速成交，再考虑下吧！", actiontype, kind, ids);
        }
        else {
          confirm("您确定要将此客源取消合作吗？<br><br>取消合作后您的客源信息将错过全网经纪人，可能会迎新爱国您快速成交，再考虑下吧！", actiontype, kind, ids);
        }
      }
    }
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
  openWin("js_pop_tip");
  $('#dialog_btn').bind('click', function () {
    submit_all(act, kind, 1, ids);
  });
}

//关闭确认窗口
function close_confirm_window() {
  $("#gTipsCoverdialogtc").remove();
  $("#js_pop_tip").hide();
}

//删除客源信息
function del_customer_by_ids(ids, kind) {
  openWin('docation_loading');
  $.getJSON(
    '/' + kind + '/del_customerinfo_by_ids/',
    {'customer_ids': ids},
    function (data) {
      close_doaction_loading();
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
  );
}

//关闭loading状态弹框
function close_doaction_loading() {
  $('#docation_loading').hide();
}
