/*经纪人评人动态JS*/
jinjren("pf_left", "li", "pf_right", "pf_con");
function jinjren(clas1, sub1, clas2, sub2) {
  $("." + clas1).find(sub1).each(function (index, element) {
    $(this).hover(function () {
      $(this).addClass("pf_bg").siblings(sub1).removeClass("pf_bg");
      $("." + clas2).find("." + sub2).eq(index).show().siblings("." + sub2).hide();
    });
  });
}
//我要评价选择向卡效果
$('.partner_list li').click(function () {
  $(this).addClass('selbg').siblings('li').removeClass('selbg');
});
//业务工具--新增资料修改
$('.mdone').click(function () {
  if ($(this).hasClass('mdcolor')) {
//		$('#modifynew').find('.md').show();
//		$('#modifynew').find('.amd').hide();
//		$(this).removeClass('mdcolor');

//      单个修改，需要在每个数据后面加.mdone的图标元素
    $(this).removeClass('mdcolor');
    $(this).siblings('.md').show().siblings('.amd').hide();
    $("#js_information_input").val("0");
    $(this).siblings(".js_button").hide();
  } else {
//		$('#modifynew').find('.md').hide();
//		$('#modifynew').find('.amd').show();
//		$(this).addClass('mdcolor');

//      单个修改，需要在每个数据后面加.mdone的图标元素
    $(this).addClass('mdcolor').show();
    $(this).siblings(".js_button").show();
    $(this).siblings('.md').hide().siblings('.amd').show();
    $("#js_information_input").val("1")

  }
})

$('.hezuo').click(function () {
  $(this).html("已申请");
  $(this).css('color', '#999');
});




