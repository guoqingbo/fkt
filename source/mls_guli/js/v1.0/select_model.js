// 模拟select
/****
 html格式  其中 id="select_model_input01"  这个id是不停在变化的

 <div class="select_model_box w100 left"></div>
 w100是控制宽度的 如需宽度可以令改
 left是控制浮动的 如果不需要浮动可以去掉



 <div class="select_model_box w100 left">
 <div class="select_model_hd">
 <div class="select_model_hd_inner">
 <label class="select_model_label" for="select_model_input01">请选择</label>
 <input type="text" class="select_model_input" id="select_model_input01" value="">
 <input type="hidden" class="select_model_input_hidden">
 </div>
 </div>
 <div class="select_model_mod">
 <dl class="select_model_list">
 <dd class="select_model_item"><a href="javascript:;" class="select_model_link" data-val="0"><span class="select_model_text">雨花台</span></a></dd>
 <dd class="select_model_item"><a href="javascript:;" class="select_model_link" data-val="1"><span class="select_model_text">秦淮</span></a></dd>
 </dl>
 </div>
 </div>


 ****/

$(function () {
  if ($(".select_model_input").length) {
    $(".select_model_input").each(function (index, element) {
      if ($(this).val() != "") {
        $(this).siblings(".select_model_label").hide();
      }
    });
    var show_select_list_timer = new Array(), hide_select_list_timer = new Array();
    $(".select_model_box").on("mouseover", function () {
      var o = $(this);
      var h = o.outerHeight(true);
      var l = o.find(".select_model_item");
      var len = l.length;
      var mod = o.find(".select_model_mod");
      var mod_h = mod.outerHeight(true);
      var list = o.find(".select_model_list");
      var id = o.find(".select_model_input").attr("id");
      var top_off = o.offset().top;
      var num = 8; //选项大于8个时 显示滚动条
      if (len >= num) {
        list.addClass("select_model_list_hidden");
        list.css("height", num * l.height() + "px");
      }
      ;
      if (top_off + mod_h + h > $(window).height() && $(window).scrollTop() + top_off > mod_h) {
        if (l.length > num) {
          mod.css("top", (-num * l.height()) - 2 + "px")
        }
        else {
          mod.css("top", (-l.height() - h) - 2 + "px")
        }
      }
      else {
        mod.css("top", h - 1 + "px");
      }
      clearTimeout(hide_select_list_timer[id]);
      show_select_list_timer[id] = setTimeout(function () {
        o.addClass("select_model_relative");
        o.find(".select_model_mod").show();
      }, 200);
    });
    $(".select_model_box").on("mouseout", function () {
      var o = $(this);
      var id = o.find(".select_model_input").attr("id");
      clearTimeout(show_select_list_timer[id]);
      hide_select_list_timer[id] = setTimeout(function () {
        o.removeClass("select_model_relative");
        o.find(".select_model_mod").hide();
      }, 200);
    });
    $(".select_model_box").find(".select_model_link").on("click", function () {
      var o = $(this);
      var p = o.parents('.select_model_box');
      var m = o.parents('.select_model_mod');
      var b = p.find('.select_model_label');
      var v = p.find('.select_model_input');
      var h = p.find(".select_model_input_hidden");
      var t = o.text();
      var i = o.attr("data-val");
      b.hide();
      v.val(t);
      h.val(i);
      m.hide();
    });

  }
  ;
});
