$(function () {
  document.oncontextmenu = function (e) {
    return false;
  }//禁止右键

  // if (!window.XMLHttpRequest) {
  $(".forms_scroll").scroll(function () {
    $(".ui-autocomplete").hide();
  })
  //}
});

//改变房源的is_outside字段
function change_house_is_outside(type, house_id, is_outside) {
  $.ajax({
    url: MLS_URL + "/" + type + "/change_house_is_outside",
    type: "GET",
    dataType: 'json',
    data: {
      house_id: house_id,
      is_outside: is_outside
    }
  });
}

function get_avgprice() {
  var price = $('#price').val();
  var buildarea = $('#buildarea').val();
  if (price > 0 && buildarea > 0) {
    var avgprice = Math.round(price * 10000 / buildarea);
    $('#avgprice').val(avgprice);
  }
}


function all_checked(obj, to_obj, s_class) {//全选
  var c = $(obj).attr("checked")
  if (c) {
    $("#" + to_obj).find("." + s_class).attr("checked", true)
  }
  else {
    $("#" + to_obj).find("." + s_class).attr("checked", false)
  }
};

function show_input(s_obi, h_obj) {
  $("#" + s_obi).show();
  $("#" + h_obj).hide();
}

$.validator.addMethod("noNum0", function (value, element, params) {
  if (value != 0)
    return true;
  else
    return false;
}, "不能为0");


$.validator.addMethod("isCardNo", function (value, element, params) {
  var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
  if (reg.test(value) || value == "") {
    return true
  }
}, "身份证输入不合法");


$.validator.addMethod("isZWNo", function (value, element, params) {
  var reg = /[\u0391-\uFFE5]/;
  if (!reg.test(value)) {
    return true
  }
}, "电话号码不能有中文");


$.validator.addMethod("angel_unit1", function (value, element, params) {
  var reg = /[\u0391-\uFFE5]/;
  if (!reg.test(value)) {
    return true
  }
}, "单元不能有中文");

$.validator.addMethod("angel_unit2", function (value, element, params) {
  var reg = /[!@#$%^&*()_\/\\\{\}\-\`+=~]/;
  if (!reg.test(value)) {
    return true
  }
}, "单元不能有特殊字符");

$.validator.addMethod("angel_record_num1", function (value, element, params) {
  var reg = /[\u0391-\uFFE5]/;
  if (!reg.test(value)) {
    return true
  }
}, "备案号只能包含数字字母");

$.validator.addMethod("angel_record_num2", function (value, element, params) {
  var reg = /[!@#$%^&*()_\/\\\{\}\-\`+=~]/;
  if (!reg.test(value)) {
    return true
  }
}, "备案号只能包含数字字母");


$.validator.addMethod("angel_door2", function (value, element, params) {
  var reg = /[!@#$%^&*()_\/\\\{\}\-\`+=~]/;
  if (!reg.test(value)) {
    return true
  }
}, "门牌不能有特殊字符");

$.validator.addMethod("angel_owner1", function (value, element, params) {
  var reg = /[!@#$%^&*()_\/\\\{\}\-\`+=~￥]/;
  if (!reg.test(value)) {
    return true
  }
}, "姓名不能有特殊字符");


$.validator.addMethod("angel_dong1", function (value, element, params) {
  var reg = /[\u0391-\uFFE5]/;
  if (!reg.test(value)) {
    return true
  }
}, "栋座不能有中文");

$.validator.addMethod("angel_dong2", function (value, element, params) {
  var reg = /[!@#$%^&*()_\/\\\{\}\-\`+=~]/;
  if (!reg.test(value)) {
    return true
  }
}, "栋座不能有特殊字符");


$.validator.addMethod("isZMNo", function (value, element, params) {
  var reg = /[A-Za-z]/;
  if (!reg.test(value)) {
    return true
  }
}, "电话号码不能有字母");

$.validator.addMethod("isZMNo", function (value, element, params) {
  var reg = /(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/;
  if (reg.test(value)) {
    return true;
  }
}, "电话只能包含7-13位数字和中划线&nbsp;");


//

$(function () {

    $("#addTel01").live("click", function () {
      if ($(".field-tel02").css("display") == 'none') {
        $(".field-tel02").show();
      } else {
        $(".field-tel03").show();
      }
    });

    $("#delTel02,#delTel03").click(function () {
      $(this).parent().hide();
      $(this).siblings(".input_text").val('');
    })

    $("#js_house_type_ZZ").parent().click(function () {
      $(".js_s_h_info,.js_select_pirce,.js_item_hide,.js_s_info_WYF,.js_s_info_CQ,.js_s_info_XZ,.js_s_fenge_info").hide();
      $(".js_s_ZZ_info,.js_show_pirce,.js_s_info_CQ,.js_s_info_XZ").show();
    });

    $("#js_house_type_BS").parent().click(function () {
      $(".js_s_h_info,.js_select_pirce,.js_item_hide,.js_s_info_WYF,.js_s_info_CQ,.js_s_info_XZ").hide();
      $(".js_s_BS_info,.js_show_pirce,.js_s_info_CQ,.js_s_info_XZ,.js_s_fenge_info").show();
    });

    $("#js_house_type_SP").parent().click(function () {
      $(".js_s_h_info,.js_show_pirce,.js_item_hide,.js_s_info_CQ,.js_s_info_XZ").hide();
      $(".js_s_SP_info,.js_select_pirce,.js_s_info_WYF,.js_s_fenge_info").show();
    });

    $("#js_house_type_XZL").parent().click(function () {
      $(".js_s_h_info,.js_show_pirce,.js_item_hide,.js_s_info_CQ,.js_s_info_XZ").hide();
      $(".js_s_XZL_info,.js_select_pirce,.js_s_info_WYF,.js_s_fenge_info").show();
    });

    $("#js_house_type_CF").parent().click(function () {
      $(".js_s_h_info,.js_show_pirce,.js_item_hide,.js_s_info_WYF,.js_s_info_CQ,.js_s_info_XZ,.js_s_fenge_info").hide();
      $(".js_s_CF_info,.js_select_pirce").show();
    });

    $("#js_house_type_CK01").parent().click(function () {
      $(".js_s_h_info,.js_show_pirce,.js_item_hide,.js_s_info_WYF,.js_s_info_CQ,.js_s_info_XZ,.js_s_fenge_info").hide();
      $(".js_s_CK01_info,.js_select_pirce").show();
    });

    $("#js_house_type_CK02").parent().click(function () {
      $(".js_s_h_info,.js_show_pirce,.js_item_hide,.js_s_info_WYF,.js_s_info_CQ,.js_s_info_XZ,.js_s_fenge_info").hide();
      $(".js_s_CK02_info,.js_select_pirce").show();
    });

    $(".js_s_h_btn").click(function () {
      var i = $(this).parent().siblings(".js_s_h_info_house")
      if (i.is(":hidden")) {
        i.show();
        $(this).html('收起<span class="iconfont">&#xe60a;</span>');
      }
      else {
        i.hide();
        $(this).html('展开<span class="iconfont">&#xe609;</span>');
      }
    })


    $("#jsUpForm").validate({
      errorPlacement: function (error, element) {
        error.appendTo(element.parents(".js_fields").find(".errorBox"));
      },
      submitHandler: function (form) {
        var add_num = $("#add_num").val();
        var action = $("#action").val();
        var house_id = $('#house_id').val();
        //房源性质
        var old_nature = $("#old_nature").val();
        var new_nature = $("input[name='nature']:checked").val();
        //状态
        var old_status = $("#old_status").val();
        var new_status = $("input[name='status']:checked").val();
        if ('undefined' == typeof(new_status)) {
          new_status = $("input[name='status']").val();
        }
        //是否已同步
        var is_outside = $('#is_outside').val();

        var group_id = $("#group_id").val();
        var property_type_per = $("#property_type_per").val();

        if (add_num == 1) {
          //出售房源修改，相关提示
          if (action != 'add') {
            if (old_nature != new_nature) {
              //未认证用户不能修改房源性质
              if ('1' == group_id) {
                $("#dialog_do_itp").html("您的帐号尚未认证,不能修改房源性质");
                openWin('js_pop_do_success');
                $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
                return false;
              }
              //没有权限
              if ('2' == property_type_per) {
                $("#dialog_do_itp").html("您没有权限修改房源性质");
                openWin('js_pop_do_success');
                $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
                return false;
              }
            }

            //状态变成非有效，提示房源从平台下架
            if ('1' == is_outside && '1' == old_status && old_status != new_status) {
              openWin('js_pop_warning_change_status');
              $('#btn_confirm_change_status').click(function () {
                //修改当前房源的同步状态
                change_house_is_outside('sell', house_id, 0);
                sell_add(action, 1);
              });
              return false;
            }

          }
          sell_add(action, 1);
        }
        else if (add_num == 3) {
          //出售房源修改，相关提示
          if (action != 'add') {
            if (old_nature != new_nature) {
              //未认证用户不能修改房源性质
              if ('1' == group_id) {
                $("#dialog_do_itp").html("您的帐号尚未认证,不能修改房源性质");
                openWin('js_pop_do_success');
                $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
                return false;
              }
              //没有权限
              if ('2' == property_type_per) {
                $("#dialog_do_itp").html("您没有权限修改房源性质");
                openWin('js_pop_do_success');
                $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
                return false;
              }
            }

            //状态变成非有效，提示房源从下架
            if ('1' == is_outside && '1' == old_status && old_status != new_status) {
              openWin('js_pop_warning_change_status');
              $('#btn_confirm_change_status').click(function () {
                //修改当前房源的同步状态
                change_house_is_outside('sell', house_id, 0);
                sell_add(action, 2);
              });
              return false;
            }

          }
          sell_add(action, 2);
        }
        else if (add_num == 4) {
          //出租房源修改,相关提示
          if (action != 'add') {
            if (old_nature != new_nature) {
              //未认证用户不能修改房源性质
              if ('1' == group_id) {
                $("#dialog_do_itp").html("您的帐号尚未认证,不能修改房源性质");
                openWin('js_pop_do_success');
                $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
                return false;
              }
              //没有权限
              if ('2' == property_type_per) {
                $("#dialog_do_itp").html("您没有权限修改房源性质");
                openWin('js_pop_do_success');
                $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
                return false;
              }
            }

            //状态变成非有效，提示房源从下架
            if ('1' == is_outside && '1' == old_status && old_status != new_status) {
              openWin('js_pop_warning_change_status');
              $('#btn_confirm_change_status').click(function () {
                //修改当前房源的同步状态
                change_house_is_outside('rent', house_id, 0);
                returne_add(action, 2);
              });
              return false;
            }
          }

          returne_add(action, 2);
        }
        else {
          //出租房源修改,相关提示
          if (action != 'add') {
            if (old_nature != new_nature) {
              //未认证用户不能修改房源性质
              if ('1' == group_id) {
                $("#dialog_do_itp").html("您的帐号尚未认证,不能修改房源性质");
                openWin('js_pop_do_success');
                $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
                return false;
              }
              //没有权限
              if ('2' == property_type_per) {
                $("#dialog_do_itp").html("您没有权限修改房源性质");
                openWin('js_pop_do_success');
                $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
                return false;
              }
            }

            //状态变成非有效，提示房源从下架
            if ('1' == is_outside && '1' == old_status && old_status != new_status) {
              openWin('js_pop_warning_change_status');
              $('#btn_confirm_change_status').click(function () {
                //修改当前房源的同步状态
                change_house_is_outside('rent', house_id, 0);
                returne_add(action, 1);
              });
              return false;
            }
          }

          returne_add(action, 1);
        }
      },
      rules: {
        sell_type: {
          required: true
        },
        block_name: {
          required: true
        },
        title: {
          required: true,
          maxlength: 30,
          minlength: 5
        },
        dong: {
          required: function () {
            //录入页面
            var sell_type = parseInt($('input[name="sell_type"]:checked').val());
            //修改页面
            var sell_type_modify = parseInt($('input[name="sell_type"]').val());
            if (sell_type > 4) {
              return false;
            } else {
              if (sell_type_modify > 4) {
                return false;
              } else {
                return true;
              }
            }
          }
        },
        unit: {
          required: function () {
            //录入页面
            var sell_type = parseInt($('input[name="sell_type"]:checked').val());
            //修改页面
            var sell_type_modify = parseInt($('input[name="sell_type"]').val());
            if (sell_type > 4) {
              return false;
            } else {
              if (sell_type_modify > 4) {
                return false;
              } else {
                return true;
              }
            }
          }
        },
        door: {
          required: function () {
            //录入页面
            var sell_type = parseInt($('input[name="sell_type"]:checked').val());
            //修改页面
            var sell_type_modify = parseInt($('input[name="sell_type"]').val());
            if (sell_type > 4) {
              return false;
            } else {
              if (sell_type_modify > 4) {
                return false;
              } else {
                return true;
              }
            }
          }
        },
        owner: {
          required: true,
          angel_owner1: true
        },
        idcare: {
          isCardNo: true
        },
        telno1: {
          required: true,
          isZMNo: true,
          isZWNo: true
        },
        telno2: {
          required: true,
          isZWNo: true,
          isZMNo: true
        },
        telno3: {
          required: true,
          isZWNo: true,
          isZMNo: true
        },

        proof: {
          number: true
        },
        mound_num: {
          number: true
        },
        record_num: {
          angel_record_num1: true,
          angel_record_num2: true
        },
        status: {
          required: true
        },
        nature: {
          required: true
        },
        room: {
          min: 0,
          required: true
        },
        hall: {
          min: 0,
          required: true
        },
        toilet: {
          min: 0,
          required: true
        },
        totalfloor: {
          number: true,
          required: true,
          min: function () {
            if (!$("#floor").val() == "") {
              return parseFloat($("#floor").val());
            }
            if (!$("#subfloor").val() == "") {
              return parseFloat($("#subfloor").val());
            }
          }
        },
        floor: {
          number: true,
          digits: true,
          required: true,
          noNum0: true
        },
        floor2: {
          number: true,
          digits: true,
          required: true,
          noNum0: true
        },
        subfloor: {
          number: true,
          digits: true,
          required: true,
          noNum0: true,
          min: function () {
            if (!$("#floor2").val() == "") {
              return parseFloat($("#floor2").val());
            }
          }
        },
        forward: {
          required: true
        },
        serverco: {
          required: true
        },
        buildyear: {
          min: 1
        },
        buildarea: {
          required: true,
          number: true,
          min: 1
        },
        buildarea1: {
          number: true,
          min: 1
        },
        buildarea2: {
          number: true,
          min: 1
        },
        price: {
          required: true,
          number: true,
          min: 1
        },
        rent_price: {
          required: true,
          number: true,
          min: 1
        },
        park_num: {
          number: true,
          digits: true,
          min: 1
        },
        floor_area: {
          number: true,
          min: 1
        },
        garden_area: {
          number: true,
          min: 1
        },
        strata_fee: {
          number: true,
          min: 0
        },
        deposit: {
          number: true,
          digits: true,
          min: 1
        },
        lowprice: {
          number: true,
          min: 1
        },
        shangjin: {
          number: true,
          digits: true,
          min: 1000,
          max: function () {
            if (!$("#price").val() == "") {
              var total_price = parseFloat($("#price").val());
              return total_price * 10000 * 0.03;
            }
          }
        },
        taxes: {
          required: true
        },
        keys: {
          required: true
        },
        key_number: {
          remote: {
            url: '/key/check_number',
            type: 'post',
            data: {
              house_id: function () {
                return $('#house_id').val();
              },
              number: function () {
                return $('#key_number').val();
              }
            }
          }
        },
        pact: {
          required: true
        },
        a_ratio: {
          required: true,
          number: true,
          digits: true,
          max: 100
        },
        b_ratio: {
          required: true
        },
        buyer_ratio: {
          required: true,
          number: true,
          max: 100
        },
        seller_ratio: {
          required: true,
          number: true,
          max: function () {
            if ($("#buyer_ratio").val() + $("#seller_ratio").val() > 100) {
              return parseFloat(100 - $("#buyer_ratio").val());
            }
          }
        },
        entrust: {
          required: true
        }

      },
      messages: {
        sell_type: {
          required: "请选择物业类型"
        },
        block_name: {
          required: "请填写楼盘名"
        },
        title: {
          required: "请填写房源标题",
          maxlength: "房源标题最多为30个字",
          minlength: "房源标题最少为5个字"
        },
        a_ratio: {
          required: "填写甲方佣金比例",
          number: '比例必须为数字',
          digits: "请填写正整数",
          max: '比例不能大于100'
        },
        b_ratio: {
          required: "填写乙方佣金比例"
        },
        buyer_ratio: {
          required: "填写买方金额",
          number: '比例必须为数字',
          max: '比例不能大于100'
        },
        seller_ratio: {
          required: "填卖买方金额",
          number: '比例必须为数字',
          max: '比例总和不能大于100'
        },
        dong: {
          required: "请填写栋座"
        },
        unit: {
          required: "请填写单元"
        },
        door: {
          required: "请填写门牌"
        },
        owner: {
          required: "请填写姓名"
        },
        idcare: {
          isCardNo: '身份证输入不合法'
        },
        telno1: {
          required: "请填写电话号码"
        },
        telno2: {
          required: "请填写电话号码"
        },
        telno3: {
          required: "请填写电话号码"
        },
        proof: {
          number: "书证号只能为数字"
        },
        mound_num: {
          number: "丘地号只能为数字"
        },
        record_num: {},

        status: {
          required: "请选择状态信息"
        },
        nature: {
          required: "请选择房源性质"
        },
        room: {
          min: "请选择室",
          required: "请选户型"
        },
        hall: {
          min: "请选择厅",
          required: "请选户型"
        },
        toilet: {
          min: "请选择卫",
          required: "请选户型"
        },
        totalfloor: {
          number: "总楼层只能为数字",
          required: "请填写总楼层",
          min: "不能比前面小"
        },
        floor: {
          number: "楼层只能为数字",
          required: "请填写楼层",
          digits: "必须是正整数"
        },
        floor2: {
          number: "楼层只能为数字",
          required: "请填写楼层",
          digits: "必须是正整数"
        },
        subfloor: {
          number: "楼层只能为数字",
          required: "请填写楼层",
          digits: "必须是正整数",
          min: "不能比前面小"
        },
        forward: {
          required: "请选朝向"
        },
        serverco: {
          required: "请选装修"
        },
        buildyear: {
          min: "请选择房龄"
        },
        buildarea: {
          required: "请填写面积",
          number: "面积必须是数字",
          min: "面积不能小于1"
        },
        buildarea1: {
          number: "面积必须是数字",
          min: "面积不能小于1"
        },
        buildarea2: {
          number: "面积必须是数字",
          min: "面积不能小于1"
        },
        price: {
          required: '请填写总价',
          number: '总价必须为数字',
          min: '总价不能小于1'
        },
        rent_price: {
          required: '请填写租金',
          number: '租金必须为数字',
          min: '租金不能小于1'
        },
        floor_area: {
          number: '面积必须为数字',
          min: '面积不能小于1'
        },
        garden_area: {
          number: '面积必须为数字',
          min: '面积不能小于1'
        },
        strata_fee: {
          number: '物业费必须为数字',
          min: '物业费不能小于0'
        },
        park_num: {
          number: '车位必须为数字',
          digits: "请填写正整数",
          min: '车位不能小于1'
        },
        deposit: {
          number: '押金必须为数字',
          digits: "请填写正整数",
          min: '押金不能小于1'
        },
        lowprice: {
          number: '底价必须为数字',
          min: '底价不能小于1'
        },
        shangjin: {
          number: '赏金必须为数字',
          digits: '赏金必须为整数',
          min: '赏金不能小于1000元',
          max: '赏金不能大于房源总价的3%'
        },
        taxes: {
          required: '请选择税费'
        },
        keys: {
          required: '请选择钥匙情况'
        },
        key_number: {
          remote: '钥匙编号不能重复'
        },
        pact: {
          required: '请选择委托协议'
        },
        entrust: {
          required: '请选择委托类型'
        }
      }
    });
  }
);

function mbFuncData(arr) {
  var obj = $("#btmb_select_list .btmb_l");
  obj.each(function () {
    if ($(this).hasClass("lp_btmb")) {
      $(this).removeClass("lp_btmb");
    }
    if ($(this).hasClass("zx_btmb")) {
      $(this).removeClass("zx_btmb");
    }
    if ($(this).hasClass("hx_btmb")) {
      $(this).removeClass("hx_btmb");
    }
    if ($(this).hasClass("jg_btmb")) {
      $(this).removeClass("jg_btmb");
    }
    if ($(this).hasClass("mj_btmb")) {
      $(this).removeClass("mj_btmb");
    }
  })
  for (var i = 0; i < arr.length; i++) {
    var sx = arr[i];
    var obj = $("#btmb_select_list .btmb_l");
    obj.each(function () {
      if ($(this).attr("data_type") == sx && sx == "lp") {
        $(this).addClass("lp_btmb");
      }
      else if ($(this).attr("data_type") == sx && sx == "zx") {
        $(this).addClass("zx_btmb")
      }
      else if ($(this).attr("data_type") == sx && sx == "hx") {
        $(this).addClass("hx_btmb")
      }
      else if ($(this).attr("data_type") == sx && sx == "jg") {
        $(this).addClass("jg_btmb")
      }
      else if ($(this).attr("data_type") == sx && sx == "mj") {
        $(this).addClass("mj_btmb")
      }
    })
  }
}


function checkedAll(obj, id) {
  var i = $("#" + id);
  if (obj.hasClass('labelOn')) {
    obj.removeClass("labelOn");
    obj.find(".js_checkbox").prop("checked", false);
    i.find("b.label").removeClass("labelOn");
    i.find(".js_checkbox").prop("checked", false);
  }
  else {
    obj.addClass("labelOn");
    obj.find(".js_checkbox").prop("checked", true);
    i.find("b.label").addClass("labelOn");
    i.find(".js_checkbox").prop("checked", true);
  }

}

$(".checkbox_all").on("click", function () {
  var srrc = $(this).attr("srrc");
  checkedAll($(this), srrc)
});

function returne_add(action, type) {
  var url = action == 'add' ? '/rent/add/' : '/rent/update';
  $.ajax({
    type: 'POST',
    url: url,
    data: $("#jsUpForm").serialize(),
    dataType: 'json',
    success: function (data) {
      if (data['errorCode'] == '401') {
        login_out();
        return false;
      }
      else if (data['errorCode'] == '403') {
        permission_none();
        return false;
      }
      if (data['modify'] == 1) {
        if (data['result'] && data['result'] > 0) {
          $("#dialog_do_itp").html(data.msg);
          openWin('js_pop_do_success');
          if (type == 1) {
            var comdict = $("#comdict").val() || '';
            var nopublish = $("#nopublish").val() || '';
            if (comdict) {
              setTimeout(function () {
                location.href = '/' + comdict + '/lists?nopublish=' + nopublish
              }, 2000);
            } else {
              setTimeout(function () {
                location.href = "/rent/lists"
              }, 2000)
            }
          } else if (type == 2) {
            setTimeout(function () {
              location.href = "/appoint_center/app_rent"
            }, 2000)
          }

        } else {
          if (data.house_num_check) {
            if (!data.house_private_check) {
              $("#dialog_do_itp").html(data.house_private_check_text);
              openWin('js_pop_do_success');
              $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
            } else {
              $("#dialog_do_itp").html("房源修改失败");
              openWin('js_pop_do_success');
              $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
            }
          } else {
            $("#dialog_do_itp").html("房源修改失败,该房源已经存在");
            openWin('js_pop_do_success');
            $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
          }
        }
      } else {
        if (data['hosue_id'] > 0) {
          //$("#dialog_do_itp").html('data.msg');
          $.ajax({
            type: 'GET',
            url: '/sell/change_is_pub/rent/' + data['hosue_id'],
            dataType: 'json',
            success: function (data) {
            }
          });
          $('#y_publish').val(data['hosue_id']);
          openWin('is_publish');
        } else {
          if (data.house_num_check) {
            if (!data.house_private_check) {
              $("#dialog_do_itp").html(data.house_private_check_text);
              openWin('js_pop_do_success');
              $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
            }
          } else {
            $("#dialog_do_itp").html("房源录入失败,该房源已经存在");
            openWin('js_pop_do_success');
            $("#image_id").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/s_ico.png");
          }
        }
      }
    }
  });
}
//setTimeout("",time)

function sell_add(action, type) {
  var url = action == 'add' ? '/sell/add/' : '/sell/update';
  $.ajax({
    type: 'POST',
    url: url,
    data: $("#jsUpForm").serialize(),
    dataType: 'json',
    success: function (data) {
      if (data['errorCode'] == '401') {
        login_out();
        return false;
      }
      else if (data['errorCode'] == '403') {
        permission_none();
        return false;
      }
      if ('/sell/update' == url) {
        if (data['result'] && data['result'] > 0) {
          $("#dialog_do_itp").html(data.msg);
          openWin('js_pop_do_success');
          if (type == 1) {
            var comdict = $("#comdict").val() || '';
            var nopublish = $("#nopublish").val() || '';
            if (comdict) {
              setTimeout(function () {
                location.href = '/' + comdict + '/lists?nopublish=' + nopublish
              }, 2000);
            } else {
              setTimeout(function () {
                location.href = "/sell/lists"
              }, 2000)
            }
          } else if (type == 2) {
            setTimeout(function () {
              location.href = "/appoint_center/app_sell"
            }, 2000)
          }

        } else {
          if (data.house_num_check === false) {
            $("#dialog_do_warnig_tip").html("房源修改失败,该房源已经存在");
            openWin('js_pop_do_warning');
          } else {
            if (data.house_private_check === false) {
              $("#dialog_do_warnig_tip").html(data.house_private_check_text);
              openWin('js_pop_do_warning');
            } else {
              if (data.is_reward === false) {
                $("#dialog_do_warnig_tip").html("设置悬赏房源不能超过5条");
                openWin('js_pop_do_warning');
              } else if (data.is_reward === true) {
                if (data.is_reward_plus === false) {
                  $("#dialog_do_warnig_tip").html("修改合作悬赏增幅必须大于等于100");
                  openWin('js_pop_do_warning');
                } else {
                  if (data.coo_ziliao_check_3 === false) {
                    $("#dialog_do_warnig_tip").html("请重新上传合作资料");
                    openWin('js_pop_do_warning');
                  } else {
                    if (data.coo_ziliao_check_1 === false) {
                      $("#dialog_do_warnig_tip").html("选择悬赏方式，必须上传委托协议书，卖家身份证及房产证");
                      openWin('js_pop_do_warning');
                    } else {
                      if (data.coo_ziliao_check_2 === false) {
                        $("#dialog_do_warnig_tip").html("选择佣金方式需满足：不上传任何资料或者 上传房产证及身份证，或者三证都传");
                        openWin('js_pop_do_warning');
                      } else {
                        $("#dialog_do_warnig_tip").html("系统错误");
                        openWin('js_pop_do_warning');
                      }
                    }
                  }
                }
              } else {
                $("#dialog_do_warnig_tip").html("系统错误");
                openWin('js_pop_do_warning');
              }
            }
          }
        }
      } else if ('/sell/add/' == url) {
        if (data['hosue_id'] > 0) {
          $.ajax({
            type: 'GET',
            url: '/sell/change_is_pub/sell/' + data['hosue_id'],
            dataType: 'json',
            success: function (data) {
            }
          });
          $('#y_publish').val(data['hosue_id']);
          openWin('is_publish');
        } else {
          if (data.house_num_check === false) {
            $("#dialog_do_warnig_tip").html("房源录入失败,该房源已经存在");
            openWin('js_pop_do_warning');
          } else {
            if (data.house_private_check === false) {
              $("#dialog_do_warnig_tip").html(data.house_private_check_text);
              openWin('js_pop_do_warning');
            } else {
              if (data.is_reward === false) {
                $("#dialog_do_warnig_tip").html("设置悬赏房源不能超过5条");
                openWin('js_pop_do_warning');
              } else {
                if (data.coo_ziliao_check_1 === false) {
                  $("#dialog_do_warnig_tip").html("选择悬赏方式，必须上传委托协议书，卖家身份证及房产证");
                  openWin('js_pop_do_warning');
                } else {
                  if (data.coo_ziliao_check_2 === false) {
                    $("#dialog_do_warnig_tip").html("选择佣金方式需满足：不上传任何资料或者 上传房产证及身份证，或者三证都传");
                    openWin('js_pop_do_warning');
                  } else {
                    $("#dialog_do_warnig_tip").html("系统错误");
                    openWin('js_pop_do_warning');
                  }
                }
              }
            }
          }

        }
      } else {
        $("#dialog_do_warnig_tip").html("系统错误");
        openWin('js_pop_do_warning');
      }

    }
  });
}
