$(function () {
  document.oncontextmenu = function (e) {
    return false;
  }//禁止右键
  tab_fun("js_tab_t01", "js_tab_b01", function () {
  });
  tab_fun("js_tab_t02", "js_tab_b02", function () {
  })
  ;
  (function ($) { //全选  反选 按钮
    $("#js_checkbox").on("click", function () {
      if (this.checked) {
        $(".checkbox").attr("checked", true);
        $(".checkbox").parents("tr").addClass("tr_hover")
      }
      else {
        $(".checkbox").attr("checked", false);
        $(".checkbox").parents("tr").removeClass("tr_hover")
      }
    });
    $(".checkbox").on("click", function () {
      this.checked ? $(this).parents("tr").addClass("tr_hover") : $(this).parents("tr").removeClass("tr_hover")
    });
  })(jQuery);


  ;
  (function ($) {//table划过行 变色
    $("#js_inner tr").hover(function () {
      $(this).addClass('tr_hover_h');


    }, function () {
      $(this).removeClass("tr_hover_h");

    })
  })(jQuery);
  ;
  (function ($) {//列表点击，弹出详情页
    if (document.getElementById('openList')) {
      $(".table_all .inner tr").each(function (index, element) {
        var _url = $(this).attr("date-url");
        var type = $(this).attr("controller");
        var id = $(this).attr("_id");
        var min_title = $(this).attr("min_title");
        var ajax_data = {};
        var msg = '';
        $(this).find("td:gt(0)").on("click", function (event) {
          //最小化窗口标题
          $('#window_min_name').val(min_title);
          //最小化窗口地址
          $('#window_min_url').val(_url);
          //最小化窗口id
          $('#window_min_id').val(id);
          //最小化标签高亮
          $('.curSmall_S').removeClass("curSmall_S");
          $(this).parent().addClass("tr_hover").siblings().removeClass("tr_hover");
          $('#window_min_id_' + id).children().first().addClass("curSmall_S");
          if (!$(this).hasClass("js_no_click")) {
            if (_url) {
              $("#js_pop_box_g .iframePop").attr("src", _url);
              openWin('js_pop_box_g');

              $(this).parent(".table").addClass("tr_hover").find(".checkbox").attr("checked", true);
            }
            event.stopPropagation();
          }
          else {
            $(this).parent(".table").addClass("tr_hover").find(".checkbox").attr("checked", true);
            event.stopPropagation();
          }
        });

        this.oncontextmenu = function (ev) {
          if (_url) {
            var oEvent = ev || event;
            var oUl = document.getElementById('openList');
            var w = $("body").width();
            var h = document.documentElement.clientHeight;
            var oH = $(oUl).outerHeight(true);
            var oW = $(oUl).outerWidth(true);
            var _id = $(this).find(".checkbox").length ? $(this).find(".checkbox").val() : $(this).attr("info_id");
            $("#openList").find(".js_input").val(_id);
            oUl.style.display = 'block';
            w < (oW + oEvent.clientX) ? oUl.style.left = w - oW - 1 + 'px' : oUl.style.left = oEvent.clientX - 1 + 'px';
            h < (oH + oEvent.clientY) ? oUl.style.top = h - oH - 1 + 'px' : oUl.style.top = oEvent.clientY - 1 + 'px';
            $(".checkbox,#js_checkbox").attr("checked", false);
            $(this).addClass("tr_hover").siblings().removeClass("tr_hover");
            $(this).find(".checkbox").attr("checked", true);
            return false;
          }
        };
      });

      var timer_tr = null;
      $("#openList").hover(function () {
        clearTimeout(timer_tr)
      }, function () {
        var _this = $(this);
        timer_tr = setTimeout(function () {
          _this.hide();
        }, 400);
      });

      document.onclick = function () {
        if (oUl) {
          var oUl = document.getElementById('openList');
          oUl.style.display = 'none';
        }
      };
    }
  })(jQuery);
  ;
  (function ($) {//右键菜单划过变色
    $("#openList").find("li").hover(function () {
      if (!$(this).hasClass("line")) {
        $(this).addClass("li_hover")
      }
    }, function () {
      $(this).removeClass("li_hover")
    })
  })(jQuery)
  ;
  (function ($) {//分享 与 取消分享/设为公盘 与 私盘/锁定 与 解锁 下拉菜单
    $("#js_show_l,#js_show_n,#js_show_s").on("click", function () {
      $(this).siblings(".list").show()
    });
    $("#js_show_l,#js_show_n,#js_show_s").siblings(".list").on("mouseleave", function () {
      $(this).hide()
    })
  })(jQuery);

  $(window).resize(function (e) {
    innerHeight()
  });
  innerHeight();


  $("#js_gz_box_bg .item").hover(function () {
    $(this).find(".fun").show();
  }, function () {
    $(this).find(".fun").hide();
  })

});

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
  } else {
    openWin('jss_pop_error');
  }
}

function ajax_import(id, type, broker_id) {
  var url;
  if (type == 'broker_info') {
    url = MLS_URL + "/" + type + "/sure/";
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

//采集管理：房源设为已联系状态
/*
 function change_contact_status(house_id,type,broker_id)
 {
 $.ajax({
 url: MLS_SIGN_URL + "/house_collections_new/change_contact_status/",
 type: "GET",
 dataType: "json",
 data: {house_id:house_id,type:type,broker_id:broker_id},
 success: function(data) {
 if(data.status == 'okay')
 {
 $('.is_saw_contact').css('display','inline-block');
 $('.is_check_contact').css('display','none');
 }else{

 }
 }
 });
 }
 */
//设为已读
function read(type) {
  var text = "";
  var arr = new Array();
  var select_num = 0;
  var textarr = new Array();
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    text += "," + arr[i];
    textarr[arr[i]] = 'tr' + arr[i];
    select_num++;
  });
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("<img style='width:20px;' src='" + MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png'>&nbsp;没有选中任何邮件，请重新选择！");
    openWin('js_pop_do_warning');
    return false;
  } else {
    $("#dialogSaveDiv").html("<img style='width:20px;' src='" + MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png'>&nbsp;你确定设为已读吗？");
    openWin('jss_pop_tip');
    $("#dialog_share").click(function () {
      $.ajax({
        url: "/" + type + "/read/",
        type: "GET",
        dataType: "json",
        data: {
          str: text,
          isajax: 1
        },
        success: function (data) {
          if (data['result'] == 'ok') {
            $("#js_pop_tip").remove();
            $("#dialog_do_itp").html("<img style='width:20px;' src='" + MLS_SOURCE_URL + "/mls/images/v1.0/dakacg.gif'>&nbsp;邮件已读设置成功!");
            openWin('js_pop_do_success');
            for (var i in textarr) {
              $("#" + textarr[i] + " .c2 .info span").remove();
            }
          }
        }
      });
    });
  }
}


//设为已读
function read_reminder(type) {
  var text = "";
  var arr = new Array();
  var select_num = 0;
  var textarr = new Array();
  $(".table").find("input:checked[name=items]").each(function (i) {
    arr[i] = $(this).val();
    text += "," + arr[i];
    textarr[arr[i]] = 'tr' + arr[i];
    select_num++;
  });
  if (select_num == 0) {
    $("#dialog_do_warnig_tip").html("<img style='width:20px;' src='" + MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png'>&nbsp;你确定设为已读吗？");
    openWin('js_pop_do_warning');
    return false;
  } else {
    $("#dialogSaveDiv").html("<img style='width:20px;' src='" + MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png'>&nbsp;你确定设为已读吗？");
    openWin('jss_pop_tip');
    $("#dialog_share").click(function () {
      $.ajax({
        url: "/" + type + "/read/",
        type: "GET",
        dataType: "json",
        data: {
          str: text,
          isajax: 1
        },
        success: function (data) {
          if (data['result'] == 'ok') {
            $("#js_pop_tip").remove();
            $("#dialog_do_itp").html("<img style='width:20px;' src='" + MLS_SOURCE_URL + "/mls/images/v1.0/dakacg.gif'>&nbsp;邮件已读设置成功!");
            openWin('js_pop_do_success');
            for (var i in textarr) {
              $("#" + textarr[i] + " .c2 .info span").remove();
            }
          }
        }
      });
    });
  }
}

//提交表单
function sub_form(form_name) {
  form_name = form_name != '' ? form_name : 'search_form';
  $('#' + form_name).submit();
}

$(function () {
  innerHeight();
});

function innerHeight() {//窗口改变大小的时候  计算高度
  if ($("#js_inner").length > 0) {
    var _height = document.documentElement.clientHeight; //- 30;/*2015.04.01 wty*/

    var _height_btn = $("#js_fun_btn").length > 0 ? $("#js_fun_btn").outerHeight(true) : 0;
    var _height_btn2 = $("#js_fun_btn2").outerHeight(true);
    var _height_tab = $("#js_tab_box").length > 0 ? $("#js_tab_box").outerHeight(true) : 0;
    var _height_search = $("#js_search_box").length > 0 ? $("#js_search_box").outerHeight(true) : 0;
    var _hieght_search_02 = $("#js_search_box_02").length > 0 ? $("#js_search_box_02").outerHeight(true) : 0;
    var _height_title = $("#js_title").length > 0 ? $("#js_title").outerHeight(true) : 0;
    var _height_gz = $("#js_gz_box_bg").length > 0 ? $("#js_gz_box_bg").outerHeight(true) : 0;
    var allH = _height_btn + _height_tab + _height_search + _height_title + _hieght_search_02 + _height_gz + _height_btn2 + 15;
    $("#js_inner").css("height", _height - allH);
    //$("#js_inner").text($("#js_inner").height())
  }
};

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

function show_ys_inner(customer_id) {
  var customer_id = parseInt(customer_id);
  $.ajax({
    url: '/customer/get_secret_info/',
    type: 'GET',
    dataType: 'JSON',
    data: {customer_id: customer_id},
    success: function (data) {
      //判断返回数据是否为空，不为空返回数据。
      if (data.id > 0) {
        $('#truename').html(data.truename);
        $('#telno').html(data.telno);
      }
      else {
        $('.link_btn_b').html('很遗憾，您无权查看相关保密信息。');
      }
    }
  });
}

//求购客源保密信息查看
function sell_show_ys_inner(customer_id) {
  var customer_id = parseInt(customer_id);
  $.ajax({
    url: '/customer/get_secret_info/',
    type: 'GET',
    dataType: 'JSON',
    data: {customer_id: customer_id},
    success: function (data) {
      if (data.errorCode == '401') {
        login.out();
      } else if (data.errorCode == '403') {
        $('.link_btn_b').html('很遗憾，您无权查看相关保密信息。');
      } else {
        //判断返回数据是否为空，不为空返回数据。
        if (data.id > 0) {
          $.ajax({
            url: MLS_URL + '/broker/check_baomi_time/3/' + customer_id,
            dataType: 'json',
            type: 'GET',
            success: function (msg) {
              if (msg.success) {
                $('#truename').html(data.truename);
                $('#telno').html(data.telno1);
                $('#telno2').html(data.telno2);
                $('#telno3').html(data.telno3);
                $('#idno').html(data.idno);
                $('#address').html(data.address);
                $('#job_type').html(data.job_type_str);
                $('#user_level').html(data.user_level_str);
                $('#age_group').html(data.age_group_str);
                //$('.link_btn_b').attr('style','display:none;');
                $('#modify_baomi_button').show();
                $('#show_baomi_button').hide();
                add_brower_customer_log(customer_id);
              } else {
                $("#dialog_do_warnig_tip").html("已达当天查看次数上限");
                openWin('js_pop_do_warning');
              }
            }
          });

        }
      }
    }
  });
}

//房源编辑保密信息
function modify_baomi_info(is_auth) {
  if (1 == is_auth) {
    $('#dong').hide();
    $('#dong_input').show();
    $('#unit').hide();
    $('#unit_input').show();
    $('#door').hide();
    $('#door_input').show();
    $('#owner').hide();
    $('#owner_input').show();
    $('#telnos').hide();
    $('#telno1_input').show();
    $('#telnos2').hide();
    $('#telno2_input').show();
    $('#telnos3').hide();
    $('#telno3_input').show();
    $('#idcare').hide();
    $('#idcare_input').show();

    $('#modify_baomi_button').hide();
    $('#show_baomi_button').hide();
    $('#modify_baomi_submit_button').show();
  } else {
    $('.link_btn_b').html('很遗憾，您无权修改相关保密信息。');
  }
}

//客源编辑保密信息
function customer_modify_baomi_info(is_auth) {
  if (1 == is_auth) {
    $('#truename').hide();
    $('#truename_input').show();
    $('#telno').hide();
    $('#telno_input').show();
    $('#telno2').hide();
    $('#telno2_input').show();
    $('#telno3').hide();
    $('#telno3_input').show();
    $('#idno').hide();
    $('#idno_input').show();
    $('#address').hide();
    $('#address_input').show();
    $('#job_type').hide();
    $('#job_type_input').show();
    $('#user_level').hide();
    $('#user_level_input').show();
    $('#age_group').hide();
    $('#age_group_input').show();

    $('#modify_baomi_button').hide();
    $('#show_baomi_button').hide();
    $('#modify_baomi_submit_button').show();
  } else {
    $('.link_btn_b').html('很遗憾，您无权修改相关保密信息。');
  }
}

//房源提交保密信息
function submit_baomi_info(house_id, type) {
  var dong = $('input[name="dong"]').val();
  var unit = $('input[name="unit"]').val();
  var door = $('input[name="door"]').val();
  var owner = $('input[name="owner"]').val();
  var telno1 = $('input[name="telno1"]').val();
  var telno2 = $('input[name="telno2"]').val();
  var telno3 = $('input[name="telno3"]').val();
  var idcare = $('input[name="idcare"]').val();

  $.ajax({
    url: '/' + type + '/submit_secret_info/',
    type: 'GET',
    dataType: 'json',
    data: {
      house_id: house_id,
      dong: dong,
      unit: unit,
      door: door,
      owner: owner,
      telno1: telno1,
      telno2: telno2,
      telno3: telno3,
      idcare: idcare
    },
    success: function (data) {
      if ('success' == data.msg) {
        $("#dialog_do_itp").html("修改成功");
        openWin('js_pop_do_success');
      } else {
        $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
        $("#dialog_do_warnig_tip").html('修改失败');
        openWin('js_pop_do_warning');
      }
    }
  });
}

//客源提交保密信息
function customer_submit_baomi_info(customer_id, type) {
  var truename = $('input[name="truename"]').val();
  var telno = $('input[name="telno"]').val();
  var telno2 = $('input[name="telno2"]').val();
  var telno3 = $('input[name="telno3"]').val();
  var idno = $('input[name="idno"]').val();
  var address = $('input[name="address"]').val();
  var job_type = $('select[name="job_type"]').find("option:selected").val();
  var user_level = $('select[name="user_level"]').find("option:selected").val();
  var age_group = $('select[name="age_group"]').find("option:selected").val();

  $.ajax({
    url: '/' + type + '/submit_secret_info/',
    type: 'GET',
    dataType: 'json',
    data: {
      customer_id: customer_id,
      truename: truename,
      telno: telno,
      telno2: telno2,
      telno3: telno3,
      idno: idno,
      address: address,
      job_type: job_type,
      user_level: user_level,
      age_group: age_group
    },
    success: function (data) {
      if ('success' == data.msg) {
        $("#dialog_do_itp").html("修改成功");
        openWin('js_pop_do_success');
      } else {
        $("#imgg").attr("src", MLS_SOURCE_URL + '/mls/images/v1.0/s_ico.png');
        $("#dialog_do_warnig_tip").html('修改失败');
        openWin('js_pop_do_warning');
      }
    }
  });
}

//求租客源保密信息查看
function rent_show_ys_inner(customer_id) {
  var customer_id = parseInt(customer_id);
  $.ajax({
    url: '/rent_customer/get_secret_info/',
    type: 'GET',
    dataType: 'JSON',
    data: {customer_id: customer_id},
    success: function (data) {
      if (data.errorCode == '401') {
        login.out();
      } else if (data.errorCode == '403') {
        $('.link_btn_b').html('很遗憾，您无权查看相关保密信息。');
      } else {
        //判断返回数据是否为空，不为空返回数据。
        if (data.id > 0) {
          $.ajax({
            url: MLS_URL + '/broker/check_baomi_time/4/' + customer_id,
            dataType: 'json',
            type: 'GET',
            success: function (msg) {
              if (msg.success) {
                $('#truename').html(data.truename);
                $('#telno').html(data.telno1);
                $('#telno2').html(data.telno2);
                $('#telno3').html(data.telno3);
                $('#idno').html(data.idno);
                $('#address').html(data.address);
                $('#job_type').html(data.job_type_str);
                $('#user_level').html(data.user_level_str);
                $('#age_group').html(data.age_group_str);
                //$('.link_btn_b').attr('style','display:none;');
                $('#modify_baomi_button').show();
                $('#show_baomi_button').hide();
                add_rent_brower_customer_log(customer_id);
              } else {
                $("#dialog_do_warnig_tip").html("已达当天查看次数上限");
                openWin('js_pop_do_warning');
              }
            }
          });

        }
      }
    }
  });
}

//添加求购客源浏览记录
function add_brower_customer_log(customer_id) {
  $.ajax({
    url: '/customer/add_brower_customer_log/',
    type: 'GET',
    dataType: 'json',
    data: {customer_id: customer_id}
  });
}

//添加求租客源浏览记录
function add_rent_brower_customer_log(customer_id) {
  $.ajax({
    url: '/customer/add_rent_brower_customer_log/',
    type: 'GET',
    dataType: 'json',
    data: {customer_id: customer_id}
  });
}

/*
 功能：根据传过来的house_id 和 类型type 来判断是否给经纪人显示该房源的电话
 日期：2015-01-08
 作者：angel_in_us
 */
function show_tel(house_id, type, broker_id, oldurl, is_input) {
  house_id = parseInt(house_id);
  broker_id = parseInt(broker_id);
  $.ajax({
    url: "/house_collections/get_" + type + "_detail/" + house_id + "/" + broker_id,
    type: 'GET',
    dataType: 'JSON',
    data: {house_id: house_id, broker_id: broker_id},
    success: function (data) {
      //判断返回数据是否为空，不为空返回数据。
      if (data.id > 0) {
        var angel = '#angel_' + house_id;
        window.parent.change_is_checked(angel, "<span class='s'>已查看</span>");
        window.parent.change_state(angel, house_id)
        $('#tel').attr("value", data.telno1);
        $('#old_url').html(oldurl);
        $('.is_saw').css("display", "inline-block");
        $('.is_saw1').css("display", "inline-block");
        $('.disappear').css("display", "none");
      }
      else {
        alert("抱歉！您是体验用户，只能查看5条信息");
      }
    }
  });
}

//测试南京站
/*
 function show_tel_new(house_id,type,broker_id,oldurl,keep)
 {
 house_id = parseInt(house_id);
 broker_id = parseInt(broker_id);
 //keep表示同公司已录入点击继续查看
 if (keep) {
 $.ajax({
 url: "/house_collections_new/get_"+type+"_detail/"+house_id+"/"+broker_id,
 type: 'GET',
 dataType: 'JSON',
 data: {house_id: house_id,broker_id:broker_id},
 success: function(data)
 {
 //判断返回数据是否为空，不为空返回数据。
 if( data.id > 0 )
 {
 var angel = '#angel_'+ house_id;
 window.parent.change_is_checked(angel,"<span class='s'>已查看</span>");
 window.parent.change_state(angel,house_id);
 //                $('#tel').attr("value",data.telno1);
 $('#tel').html(data.telno1);
 $("#zhuangtai").html('已查看');
 //                $('#old_url').html(oldurl);
 $('.is_saw').css("display","inline-block");
 $('.is_saw1').css("display","inline-block");
 $('.disappear').css("display","none");
 $(".zws_descript_js").hide();
 $(".zws_descript_W_name_dl dt").show();
 $(".zws_caozuo").show();
 $(".zws_download").show();
 }
 else
 {
 alert("抱歉！您是体验用户，只能查看5条信息");
 return false;
 }
 }
 });
 } else {
 $.ajax({
 type: 'post',
 url : '/house_collections_new/collect_'+type+'_publish_check',
 data :{'id':house_id},
 dataType:'json',
 success: function(msg){
 if(msg > 0){
 openWin('js_publish');
 }else{
 $.ajax({
 url: "/house_collections_new/get_"+type+"_detail/"+house_id+"/"+broker_id,
 type: 'GET',
 dataType: 'JSON',
 data: {house_id: house_id,broker_id:broker_id},
 success: function(data)
 {
 //判断返回数据是否为空，不为空返回数据。
 if( data.id > 0 )
 {
 var angel = '#angel_'+ house_id;
 window.parent.change_is_checked(angel,"<span class='s'>已查看</span>");
 window.parent.change_state(angel,house_id)
 //                $('#tel').attr("value",data.telno1);
 $('#tel').html(data.telno1);
 $("#zhuangtai").html('已查看');
 //                $('#old_url').html(oldurl);
 $('.is_saw').css("display","inline-block");
 $('.is_saw1').css("display","inline-block");
 $('.disappear').css("display","none");
 $(".zws_descript_js").hide();
 $(".zws_descript_W_name_dl dt").show();
 $(".zws_caozuo").show();
 $(".zws_download").show();
 }
 else
 {
 alert("抱歉！您是体验用户，只能查看5条信息");
 return false;
 }
 }
 });
 }
 }
 });
 }
 }
 */
//采集设置
/*
 function collect_set(url){
 if(url)
 {
 $("#js_pop_box_g_set .iframePop").attr("src",url);
 }
 openWin('js_pop_box_g_set');
 }
 */
function show_tel_and_btn(obj) {
  $("#js_s_tel").hide();
  $("#js_num_tel").show();
  $(obj).hide().siblings(".btn_c").show();
}

function checkboxAll(id_all, s_class) {
  if (id_all.checked) {
    $("." + s_class).attr("checked", true);
  }
  else {
    $("." + s_class).attr("checked", false);
  }
}


function modifyInfo(type) {
  var house_id = $("#right_id").val();
  //判断是否修改权限
  $.ajax({
    type: 'get',
    dataType: 'JSON',
    url: MLS_URL + '/' + type + '/modify_per_check/' + house_id,
    success: function (msg) {
      if ('is_seal' == msg.result) {
        $("#dialog_do_purview_none_tip").html(msg.seal_msg);
        openWin('js_pop_do_purview_none');
      } else {
        if ('yes_per_modify' == msg.result) {
          location.href = "/" + type + "/modify/" + house_id;
        } else {
          $("#dialog_do_purview_none_tip").html(" 对不起，您没有访问权限!");
          openWin('js_pop_do_purview_none');
        }
      }
    }
  });
}

function check_modify_info(type, house_id) {
  //判断是否修改权限
  $.ajax({
    type: 'get',
    url: '/group_publish_' + type + '/check_modify/' + house_id,
    success: function (msg) {
      if ('yes_per_modify' == msg) {
        var tmp_com = 'group_publish_' + type;
        var nopublish = $("#nopublish").val() || '';
        location.href = "/" + type + "/modify/" + house_id + "?comdict=" + tmp_com + "&nopublish=" + nopublish;
      } else {
        var _url = '/group_publish_' + type + '/temporaryinfo/' + house_id;
        if (_url) {
          $("#js_pop_box_g_temporaryinfo .iframePop").attr("src", _url);
        }
        openWin('js_pop_box_g_temporaryinfo');
      }
    }
  });
}

function openCollectDetails(url) {
  if (url) {
    $("#js_pop_box_g .iframePop").attr("src", url);
  }
  openWin('js_pop_box_g');
}
//举报中介房源
function report_list() {
  var r_addtime = $('#r_addtime').val();
  var r_reason = $('#r_reason').val().replace(/\s+/g, "");//获取值并去空格
  var r_person = $('#r_person').val();
  var broker_id = $('#broker_id').val();
  var r_tel = $('#r_tel').val();
  if (r_reason == '' || r_reason == null) {
    $('.tip11').show();
    return;
  }

  //ajax 改变 往 agent_reportlist 表里的 插入举报信息
  $.ajax({
    type: 'get',
    url: MLS_URL + '/house_collections/report_agent',
    data: {'r_addtime': r_addtime, 'r_reason': r_reason, 'r_person': r_person, 'broker_id': broker_id, 'r_tel': r_tel},
    dataType: 'json',
    success: function (msg) {
      if (msg == '123') {
        openWin('js_report_s_pop_fail');
        $(".js-close").live("click", function () {
          $('#js_report_pop').hide();
          $("#GTipsCoverjs_pop_box_g", parent.document).remove();
          $("#GTipsCoverjs_report_pop").remove();
        });
      } else {
        openWin('js_report_s_pop');
        $('#js_report_pop').hide();
        $("#GTipsCovernew_moban").remove();
        return;
      }
    }
  });
}
//删除提示语
function clear_reminder() {
  var r_reason = $('#r_reason').val().replace(/\s+/g, "");//获取值并去空格
  if (r_reason != '') {
    $('#r_reason').val('');
  }
  $(".tip11").hide();
}

// 搜索项 展开收起
function show_hide_info(obj, cookie_name) {
  var d = $(obj).attr("data-h");

  if (d == 0) {
    $(obj).parent().find(".hide").css("display", "inline");
    $(obj).html('收起<span class="iconfont">&#xe60a;</span>');
    $(obj).attr("data-h", "1");
    if (cookie_name !== null || cookie_name !== undefined || cookie_name !== '') {
      SetCookie(cookie_name, '1');
    }
  }
  else {
    $(obj).parent().find(".hide").hide();
    $(obj).html('更多<span class="iconfont">&#xe609;</span>');
    $(obj).attr("data-h", "0");
    if (cookie_name !== null || cookie_name !== undefined || cookie_name !== '') {
      SetCookie(cookie_name, '0');
    }
  }
  innerHeight();
};


//右击详情
function openDetails(type, is_public) {
  var customer_id = $("#right_id").val();
  var _url = '/' + type + '/details/' + customer_id;

  if (is_public == 1) {
    _url = _url + '/' + is_public;
  }

  if (_url) {
    $("#js_pop_box_g .iframePop").attr("src", _url);
  }

  openWin('js_pop_box_g');
};

$(function () {//权限管理
  $(".js_role_new_item .js_role_checkbox").on("click", function (event) {
    $(".js_h_div").hide();
    var p = $(this).parent().siblings(".js_h_div");
    var v = $(this).val();
    if (this.checked) {
      if (v > 0) {
        p.find(":radio[value=" + v + "]").attr("checked", true);
      } else {
        p.find(":radio").eq(0).attr("checked", true);
      }
      p.show();
    } else {
      p.find(":radio").attr("checked", false);
      p.hide();
    }
    event.stopPropagation();
  });

  $(".js_c").on("click", function () {
    $(this).parent(".js_h_div").hide()
  });

  $(document).on('click', function () {
    $(".js_h_div").hide();
  });

  $('.js_h_div').on("click", function (event) {
    event.stopPropagation();
  });
})
