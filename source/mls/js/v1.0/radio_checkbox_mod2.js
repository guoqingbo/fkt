$(function () {
  //模拟单选按钮
  $("i.label").on('click', function () {
    var i = $(this);
    i.siblings(".label").find(".input_radio").attr("checked", false);
    i.siblings(".label").removeClass("labelOn");
    i.find(".input_radio").attr("checked", true);
    i.addClass("labelOn");
  })
  //房源管理-模拟单选按钮
  $(".check_box b.label").on('click', function () {
    var i = $(this);
    if ($(this).hasClass("labelOn")) {
      i.find(".js_checkbox").prop("checked", false);
      i.removeClass("labelOn");
    }
    else {
      i.find(".js_checkbox").prop("checked", true);
      i.addClass("labelOn");
    }
  })
});
