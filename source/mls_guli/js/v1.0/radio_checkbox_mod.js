$(function () {//模拟单选按钮
  $("i.label").on('click', function () {
    var i = $(this);
    i.parent(".js_fields").find(".input_radio").attr("checked", false);
    i.siblings(".label").removeClass("labelOn");
    i.find(".input_radio").attr("checked", true);
    i.addClass("labelOn");
  })
});
