$(function () {
  document.oncontextmenu = function (e) {
    return false;
  }//禁止右键
  tab_fun("js_tab_t01", "js_tab_b01", function () {
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


  innerHeight();
  $(window).resize(function (e) {
    innerHeight()
  });

});

function innerHeight() {//窗口改变大小的时候  计算高度
  var _height = document.documentElement.clientHeight;
  var _height_btn = $("#js_fun_btn").outerHeight(true);
  var _height_btn2 = $("#js_fun_btn2").outerHeight(true);
  var _height_tab = $("#js_tab_box").outerHeight(true);
  var _height_search = $("#js_search_box").outerHeight(true);
  var _hieght_search_02 = $("#js_search_box_02").length > 0 ? $("#js_search_box_02").outerHeight(true) : 0;
  var _height_title = $("#js_title").outerHeight(true);
  var allH = _height - _height_btn - _height_tab - _height_search - _height_title - _hieght_search_02 - _height_btn2 - 25;
  var allH2 = _height - _height_btn - _height_tab - _height_search - _height_title - _hieght_search_02 - _height_btn2 - 10;
  _height > 580 ? $("#js_inner").height(allH) : $("#js_inner").height(330);
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


function show_hide_info(obj) { // 搜索项 展开收起
  var d = $(obj).attr("data-h");
  if (d == 0) {
    $(obj).parent().find(".hide").show();
    $(obj).html('收起<span class="iconfont">&#xe60a;</span>');
    $(obj).attr("data-h", "1")
  }
  else {
    $(obj).parent().find(".hide").hide();
    $(obj).html('展开<span class="iconfont">&#xe609;</span>');
    $(obj).attr("data-h", "0")
  }
  innerHeight();
};



