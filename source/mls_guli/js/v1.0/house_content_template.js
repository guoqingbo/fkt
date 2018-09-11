$(function () {
  var ajax_url_1 = '';
  var _text = '';
  //房源标题模板点击事件
  $('#content_template_button').on("click", function () {
    $('#content_template_list').html('');
    //区分出售出租
    if ('string' == typeof($('#price').val())) {
      ajax_url_1 = '/sell/get_all_content_template';
      _text = '出售模板';
    } else {
      ajax_url_1 = '/rent/get_all_content_template';
      _text = '出租模板';
    }
    //楼盘名
    var name = $('#block_name').val();
    //户型
    var room = $('select[name="room"]').val();
    var hall = $('select[name="hall"]').val();
    var toilet = $('select[name="toilet"]').val();
    var kitchen = $('select[name="kitchen"]').val();
    var balcony = $('select[name="balcony"]').val();
    if ('' == kitchen) {
      kitchen = 0;
    }
    if ('' == balcony) {
      balcony = 0;
    }
    if ('' == name || '' == hall || '' == toilet || '' == room) {
      $("#dialog_do_warnig_tip").html('楼盘和户型未填写');
      openWin('js_pop_do_warning');
      return false;
    }

    $.ajax({
      type: 'get',
      url: ajax_url_1,
      dataType: 'json',
      success: function (data) {
        var name_pattern = '{name}';
        var room_pattern = '{room}';
        var hall_pattern = '{hall}';
        var toilet_pattern = '{toilet}';
        var kitchen_pattern = '{kitchen}';
        var balcony_pattern = '{balcony}';

        var house_name_template_str = '';
        for (var i = 0; i < data.length; i++) {
          var house_name_template = data[i].content;
          //楼盘名称替换
          if (house_name_template.indexOf(name_pattern) != -1) {
            house_name_template = house_name_template.replace(name_pattern, name);
          }
          //户型替换
          if (house_name_template.indexOf(room_pattern) != -1) {
            house_name_template = house_name_template.replace(room_pattern, room);
          }
          if (house_name_template.indexOf(hall_pattern) != -1) {
            house_name_template = house_name_template.replace(hall_pattern, hall);
          }
          if (house_name_template.indexOf(toilet_pattern) != -1) {
            house_name_template = house_name_template.replace(toilet_pattern, toilet);
          }
          if (house_name_template.indexOf(kitchen_pattern) != -1) {
            house_name_template = house_name_template.replace(kitchen_pattern, kitchen);
          }
          if (house_name_template.indexOf(balcony_pattern) != -1) {
            house_name_template = house_name_template.replace(balcony_pattern, balcony);
          }
          house_name_template_str += '<li class="on"><h3>' + _text + (i + 1) + '</h3><p>' + house_name_template + '</p></li>';
        }
        $('#content_template_list').append(house_name_template_str);
        openWin('ms_moban');
      }
    });
  });

  $('#content_template_list li').live('click', function () {
    $('.ke-container-default, #bewrite').remove();
    var content = '<textarea id="bewrite" style="margin-top: 5px; width: 835px; height: 155px; visibility: hidden; display: none;" rows="0" cols="0" name="bewrite">' + $(this).find('p').html();
    +'</textarea>';
    $('.eidter').after(content);
    setTimeout(function () {
      //页面编辑器
      var editor;
      KindEditor.ready(function (K) {
        editor = K.create('#bewrite', {
          width: '820px',
          height: '350px',
          resizeType: 0,
          newlineTag: "p",
          allowPreviewEmoticons: false,
          allowImageUpload: false,
          items: ['fontname', 'fontsize', '|', 'forecolor',
            'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
            'insertunorderedlist', '|', 'wordpaste', '|', 'image'],
          afterBlur: function () {
            this.sync();
          }
        });
      });
    }, 5);


    //       $('.ke-edit-iframe').append(content);
//            $('#ms_moban').hide();
//            $('#GTipsCoverzj_moban').remove();
  });

});
