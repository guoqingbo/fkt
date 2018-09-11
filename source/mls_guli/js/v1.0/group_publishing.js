$(function () {
  $(".js_t1").click(function () {
    $('.tab_pop_hd dd').removeClass('itemOn');
    $(this).addClass('itemOn');
    $('.tr-true').show();
    $('.tr-false').show();
  });
  $(".js_t2").click(function () {
    $('.tab_pop_hd dd').removeClass('itemOn');
    $(this).addClass('itemOn');
    $('.tr-true').show();
    $('.tr-false').hide();
  });
  $(".js_t3").click(function () {
    $('.tab_pop_hd dd').removeClass('itemOn');
    $(this).addClass('itemOn');
    $('.tr-false').show();
    $('.tr-true').hide();
  });

  $("#block_name_pro").autocomplete({
    search: function (event, ui) {
      $("#forbid").removeClass('btn-lv1').addClass('btn-hui1').attr("disabled", "disabled");  //不可用
      $("#load2").html('<img src="' + MLS_SOURCE_URL + '/mls/images/v1.0/load2.gif" />');
    },
    source: function (request, response) {
      var term = request.term;
      var ajax_url = $("#ajax_url").val();
      $.ajax({
        url: ajax_url,
        type: "GET",
        dataType: "json",
        data: {
          keyword: term,
          sell_type: sell_type
        },
        success: function (data) {
          if (data.length > 0 && data[0]['label'].indexOf('script') > 0) {
            data[0]['label'] = '登录信息已过期，请重新绑定帐号';
            data[0]['id'] = 0;
          }
          response(data);
        }
      });
    },
    minLength: 1,
    delay: delaytime,
    removeinput: 0,
    select: function (event, ui) {
      if (ui.item.id) {
        $("#block_id").val(ui.item.id);
        $("#block_name").val(ui.item.value);
        if (ui.item.address) {
          $("#address").val(ui.item.address);
        }
        if (ui.item.district) {
          $("#district").val(ui.item.district);
        }
        if (ui.item.street) {
          $("#street").val(ui.item.street);
        }
        removeinput = 2;
        $("#forbid").removeClass('btn-hui1').addClass('btn-lv1').removeAttr("disabled");
      } else {
        removeinput = 1;
      }
    },
    close: function (event) {
      if (typeof(removeinput) == 'undefined' || removeinput == 1) {
        $("#block_name").val("");
        $("#block_id").val("");
      }
      $("#load2").html('');
    }
  });
});

$("#checkCodeUrl").live("click", function () {
  var ganjisessid = $("#ganjisessid").val();
  $(this).attr("src", MLS_URL + "/" + group_control + "/get_ganjivip_code/" + ganjisessid + "/?nocache=" + (new Date() * 1));
});

$("#checkCodeUrl2").live("click", function () {
  var ganjisessid = $("#ganjisessid").val();
  $(this).attr("src", MLS_URL + "/" + group_control + "/get_ganjivip_code/" + ganjisessid + "/?type=2&nocache=" + (new Date() * 1));
});

function get_block_58(msg) {
  $("#block_58").show();
  var dis_html = str_html = '<option value="-1">请选择</option>';
  var str_hide = '';
  $.each(msg['block_area']['district'], function (i, v) {
    dis_html += '<option value="' + i + '" class="dis_58"> ' + v + ' </option>';
  });
  $.each(msg['block_area']['street'], function (i, v) {
    str_hide += '<option value="' + i + '" class="str_58 pro_' + v.pro + '"> ' + v.name + ' </option>';
  });
  $("#district_58").html(dis_html);
  $("#street_58").html(str_html);
  $("#street_58_hide").html(str_hide);

  $("#district_58").live('change', function () {
    var dis_select = $("#district_58").val();
    var dis_select_name = $("#district_58").find("option:selected").text();
    $("#district").val(dis_select);
    $("#street").val('');

    str_html = '<option value="-1">请选择</option>';
    var obj = $(".pro_" + dis_select);
    if (obj.length < 1) {
      str_html += '<option value="' + dis_select + '" class="dis_58"> ' + dis_select_name + ' </option>';
    } else {
      $.each(obj, function (i, v) {
        str_html += v.outerHTML;
      });
    }
    $("#street_58").html(str_html);
  });
  $("#street_58").live('change', function () {
    var str_select = $("#street_58").val();
    $("#street").val(str_select);
  });
}
