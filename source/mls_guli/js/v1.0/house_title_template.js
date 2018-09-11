$(function () {
  //房源标题模板点击事件
  $('#title_template_button').on("click", function () {
    //默认勾选‘全部’
    $('#title_template_select').find('b').addClass('labelOn');
    $('#title_template_select b').find(".js_checkbox").prop("checked", true);
    //录入页面物业类型
    var sell_type = $('input[name="sell_type"]:checked').val();
    //修改页面物业类型
    var sell_type2 = $('input[name="sell_type"]').val();
    var name = $('#block_name').val();
    //价格字段处理（出售出租分开）
    var ajax_url_1 = '';
    var price_num = '';
    var price = '';
    var price_notice = '';
    var house_type = '';//急售、急租描述
    if ('string' == typeof($('#price').val())) {
      ajax_url_1 = '/sell/get_all_title_template';
      house_type = '急售';
      price_num = $('#price').val();
      price = $('#price').val() + '万';
      price_notice = '请填写总价';
    } else {
      ajax_url_1 = '/rent/get_all_title_template';
      house_type = '急租';
      price_num = $('#rent_price').val();
      if (sell_type == '3' || sell_type == '4' || sell_type == '5' || sell_type == '6' || sell_type == '7' ||
        sell_type2 == '3' || sell_type2 == '4' || sell_type2 == '5' || sell_type2 == '6' || sell_type2 == '7') {
        var danwei = $('select[name="price_danwei"] option:selected').val();
        if (danwei == 0) {
          price = $('#rent_price').val() + '元/月';
        } else if (danwei == 1) {
          price = $('#rent_price').val() + '元/㎡*天';
        }
      } else {
        price = $('#rent_price').val() + '元/月';
      }
      price_notice = '请填写租金';
    }
    var area = $('input[name="buildarea"]').val();
    var room = $('select[name="room"]').val();
    var fitment = $('input[name="fitment"]:checked').val();
    //装修
    fitment_str = '';
    if (fitment == 1) {
      fitment_str = '毛坯';
    } else if (fitment == 2) {
      fitment_str = '简装';
    } else if (fitment == 3) {
      fitment_str = '中装';
    } else if (fitment == 4) {
      fitment_str = '精装';
    } else if (fitment == 5) {
      fitment_str = '豪装';
    } else if (fitment == 6) {
      fitment_str = '婚装';
    }
    if (name == '') {
      $('#dialog_do_itp_house_title_template').html('请填写楼盘名称');
      openWin('js_house_title_template_success');
    } else if (price_num == '') {
      $('#dialog_do_itp_house_title_template').html(price_notice);
      openWin('js_house_title_template_success');
    } else if (area == '') {
      $('#dialog_do_itp_house_title_template').html('请填写面积');
      openWin('js_house_title_template_success');
    } else {
      $.ajax({
        type: 'get',
        url: ajax_url_1,
        dataType: 'json',
        success: function (data) {
          var name_pattern = '{name}';
          var price_pattern = '{price}';
          var area_pattern = '{area}';
          var room_pattern = '{room}';
          var fitment_pattern = '{fitment}';
          var housetype_pattern = '急售';

          var house_name_template_str = '';
          $('#btmb_select_list').empty();
          for (var i = 0; i < data.length; i++) {
            var house_name_template = data[i].content;
            //判断物业类型是否为住宅、别墅，如果不是，去除{room}
            //录入页面
            if (typeof(sell_type) != 'undefined' && sell_type != '1' && sell_type != '2') {
              house_name_template = house_name_template.replace(room_pattern + '房', '');
            }
            //修改页面
            if (typeof(sell_type) == 'undefined' && sell_type2 != '1' && sell_type2 != '2') {
              house_name_template = house_name_template.replace(room_pattern + '房', '');
            }
            //楼盘名称替换
            if (house_name_template.indexOf(name_pattern) != -1) {
              house_name_template = house_name_template.replace(name_pattern, name);
            }
            //房价
            if (house_name_template.indexOf(price_pattern) != -1) {
              house_name_template = house_name_template.replace(price_pattern, price);
            }
            //面积
            if (house_name_template.indexOf(area_pattern) != -1) {
              house_name_template = house_name_template.replace(area_pattern, area);
            }
            //户型（房间数）
            if (house_name_template.indexOf(room_pattern) != -1) {
              house_name_template = house_name_template.replace(room_pattern, room);
            }
            //装修
            if (house_name_template.indexOf(fitment_pattern) != -1) {
              house_name_template = house_name_template.replace(fitment_pattern, fitment_str);
            }
            //急售、急租替换
            if (house_name_template.indexOf(housetype_pattern) != -1) {
              house_name_template = house_name_template.replace(housetype_pattern, house_type);
            }
            house_name_template_str += '<li class="btmb_l" data_type="lp"><a class="info_a" href="#">' + house_name_template + '</a></li>';
          }
          $('#btmb_select_list').append(house_name_template_str);
          openWin('zj_moban');
        }
      });
    }
  });
  //房源管理-模拟单选按钮
  $(".check_box2 b.label").on('click', function () {
    var chknum = $(".check_box2 b.label").size();
    var chk = 0;

    var i = $(this);
    if ($(this).hasClass("labelOn")) {
      i.find(".js_checkbox").prop("checked", false);
      i.removeClass("labelOn");
      i.parent().siblings(".checkbox_all").removeClass("labelOn");
      i.parent().siblings(".checkbox_all").find(".js_checkbox").prop("checked", false);
    }
    else {
      i.find(".js_checkbox").prop("checked", true);
      i.addClass("labelOn");
    }
    ;

    $(".check_box2 b.label").each(function () {
      if ($(this).attr("checked") == true) {
        chk++;
      }
    });
    if (chknum == chk) {//全选
      i.parent().siblings(".checkbox_all").prop("checked", true);
    } else {//不全选
      i.parent().siblings(".checkbox_all").prop("checked", false);
    }
  })
  //选择标题模板事件
  $('.info_a').live('click', function () {
    var title = $(this).html();
    if (title.length > 30) {
      $('#dialog_do_itp_house_title_template').html('房源名称不能超过30字');
      openWin('js_house_title_template_success');
    } else {
      $('#title').val(title);
      $('#house_title_num').html('您还可以输入' + (30 - title.length) + '个字');
      $('#zj_moban').hide();
      $('#GTipsCoverzj_moban').remove();
    }
  });

  //标题模板筛选
  $('input[name="title_category"]').parent().click(function () {
    //录入页面物业类型
    var sell_type = $('input[name="sell_type"]:checked').val();
    //修改页面物业类型
    var sell_type2 = $('input[name="sell_type"]').val();
    var name = $('#block_name').val();
    //价格字段处理（出售出租分开）
    var ajax_url_2 = '';
    var price = '';
    var house_type = '';//急售、急租描述
    if ('string' == typeof($('#price').val())) {
      ajax_url_2 = '/sell/get_title_template_by_cond';
      house_type = '急售';
      price = $('#price').val() + '万';
    } else {
      ajax_url_2 = '/rent/get_title_template_by_cond';
      house_type = '急租';
      if (sell_type == '3' || sell_type == '4' || sell_type == '5' || sell_type == '6' || sell_type == '7' ||
        sell_type2 == '3' || sell_type2 == '4' || sell_type2 == '5' || sell_type2 == '6' || sell_type2 == '7') {
        var danwei = $('select[name="price_danwei"] option:selected').val();
        if (danwei == 0) {
          price = $('#rent_price').val() + '元/月';
        } else if (danwei == 1) {
          price = $('#rent_price').val() + '元/㎡*天';
        }
      } else {
        price = $('#rent_price').val() + '元/月';
      }
    }
    var area = $('input[name="buildarea"]').val();
    var room = $('select[name="room"]').val();
    var fitment = $('input[name="fitment"]:checked').val();
    //装修
    fitment_str = '';
    if (fitment == 1) {
      fitment_str = '毛坯';
    } else if (fitment == 2) {
      fitment_str = '简装';
    } else if (fitment == 3) {
      fitment_str = '中装';
    } else if (fitment == 4) {
      fitment_str = '精装';
    } else if (fitment == 5) {
      fitment_str = '豪装';
    } else if (fitment == 6) {
      fitment_str = '婚装';
    }

    var title_category = [];
    $('input[name="title_category"]').parents(".select_info").find('.labelOn').each(function () {
      title_category.push($(this).children().val());
    });
    $.ajax({
      type: 'get',
      url: ajax_url_2,
      data: 'title_category=' + title_category,
      dataType: 'json',
      success: function (data) {
        if (data.result == 'nodata') {
          $('#btmb_select_list').empty();
        } else {
          var name_pattern = '{name}';
          var price_pattern = '{price}';
          var area_pattern = '{area}';
          var room_pattern = '{room}';
          var fitment_pattern = '{fitment}';
          var housetype_pattern = '急售';

          var house_name_template_str = '';
          $('#btmb_select_list').empty();
          for (var i = 0; i < data.length; i++) {
            var house_name_template = data[i].content;
            //判断物业类型是否为住宅、别墅，如果不是，去除{room}
            //录入页面
            if (typeof(sell_type) != 'undefined' && sell_type != '1' && sell_type != '2') {
              house_name_template = house_name_template.replace(room_pattern + '房', '');
            }
            //修改页面
            if (typeof(sell_type) == 'undefined' && sell_type2 != '1' && sell_type2 != '2') {
              house_name_template = house_name_template.replace(room_pattern + '房', '');
            }
            //楼盘名称替换
            if (house_name_template.indexOf(name_pattern) != -1) {
              house_name_template = house_name_template.replace(name_pattern, name);
            }
            //房价
            if (house_name_template.indexOf(price_pattern) != -1) {
              house_name_template = house_name_template.replace(price_pattern, price);
            }
            //面积
            if (house_name_template.indexOf(area_pattern) != -1) {
              house_name_template = house_name_template.replace(area_pattern, area);
            }
            //户型（房间数）
            if (house_name_template.indexOf(room_pattern) != -1) {
              house_name_template = house_name_template.replace(room_pattern, room);
            }
            //装修
            if (house_name_template.indexOf(fitment_pattern) != -1) {
              house_name_template = house_name_template.replace(fitment_pattern, fitment_str);
            }
            //急售、急租替换
            if (house_name_template.indexOf(housetype_pattern) != -1) {
              house_name_template = house_name_template.replace(housetype_pattern, house_type);
            }
            house_name_template_str += '<li class="btmb_l" data_type="lp"><a class="info_a" href="#">' + house_name_template + '</a></li>';
          }
          $('#btmb_select_list').append(house_name_template_str);
        }
      }
    });
  });
});
