//删除角色
function delete_role(ids) {
  // openWin('js_pop_do_delete');
  // $('#dialog_btn').bind('click', function () {
  //   $("#js_pop_do_delete").hide();
  //   //保存角色
  //   $.ajax({
  //     url: '/permission/delete_role/',
  //     type: 'POST',
  //     dataType: 'json',
  //     data: {'ids': ids},
  //     success: function (data) {
  //       if (data == 'errorCode401') {
  //         login_out();
  //       } else if (data == 'errorCode403') {
  //         permission_none();
  //       } else if (data == 1) {
  //         $('#dialog_do_success_tip').html('删除角色成功！');
  //         openWin('js_pop_do_success');
  //       } else if (data == 3) {
  //         $('#dialog_do_warnig_tip').html('角色挂靠经纪人，删除角色失败！');
  //         openWin('js_pop_do_warning');
  //       } else {
  //         $('#dialog_do_warnig_tip').html('删除角色失败！');
  //         openWin('js_pop_do_warning');
  //       }
  //     }
  //   });
  // });
}

function show_page(c_obj, s_obj) {//翻页弹框
  $("#" + c_obj).on("click", function (event) {
    $("#" + s_obj).show();
    event.stopPropagation();
  });
  $(document).click(function () {
    $("#" + s_obj).hide();
  });
  $("#" + s_obj).click(function (event) {
    event.stopPropagation();
  });
};

$(function () {
  document.oncontextmenu = function (e) {
    return false;
  }//禁止右键
  show_page("js_get_page_to", "js_f_input");
  $(".js_lable_chegked_box").find("input:checkbox").change(function () {
    if (this.checked) {
      $(this).parent().siblings().find("input:checkbox").attr("checked", false)
    }
  });

  $("#js_checkbox").on("click", function () {
    if (this.checked) {
      $(".checkbox").attr("checked", true);
      $(".checkbox[value=2]").attr("checked", false);
      $(".checkbox[value=1]").attr("checked", false);
      $(".checkbox").parents("tr").addClass("tr_hover")
    }
    else {
      $(".checkbox").attr("checked", false);
      $(".checkbox").parents("tr").removeClass("tr_hover")
    }
  });
  //单击权限修改页
  $('.role_modify').bind('click', function () {
    var data_url = $(this).parent().parent().parent().attr('date-url');
    $('.iframePop').attr('src', data_url);
  });

  //保存角色
  $('#save_role').bind('click', function () {
    var name = $.trim($('#name').val());
    var description = $.trim($('#description').val());
    if (name == '' || description == '') {
      $('#dialog_do_warnig_tip').html('角色名称和角色说明不能为空！');
      openWin('js_pop_do_warning');
      return;
    }
    // //保存角色
    // $.ajax({
    //   url: '/permission/save_role/',
    //   type: 'POST',
    //   dataType: 'json',
    //   data: {'name': name, 'description': description},
    //   success: function (data) {
    //     if (data == 'errorCode401') {
    //       login_out();
    //     } else if (data == 'errorCode403') {
    //       permission_none();
    //     } else if (data == 1) {
    //       openWin('js_pop_do_success');
    //     } else {
    //       openWin('js_pop_do_warning');
    //       if (data == 3) {
    //         $('#dialog_do_warnig_tip').html('角色名称和角色说明不能为空！');
    //       } else if (data == 4) {
    //         $('#dialog_do_warnig_tip').html('角色名称已存在');
    //       } else if (data == 0) {
    //         $('#dialog_do_warnig_tip').html('添加角色失败！');
    //       }
    //     }
    //   }
    // });
  });

  //删除角色
  $('.role_delete').bind('click', function () {
    var package_id = $(this).attr('package-id');
    if (package_id != 1 && package_id != 2) {
      delete_role($(this).attr('attr-id'));
    }
  });

  //批量删除角色
  $('#batch_delete_role').bind('click', function () {
    var arr = new Array();
    var ids = '';
    $(".table").find("input:checked[name=role_id]").each(function (i) {
      arr[i] = $(this).val();
    });
    var arr = $.unique(arr);
    ids = arr.join(",");
    if (ids == '') {
      openWin('js_pop_do_warning');
      $('#dialog_do_warnig_tip').html('请先选定要操作的信息！');
    } else {
      delete_role(ids);
    }
  });
});
