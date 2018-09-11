//图片滚动展示  wxr
;(function ($) {
  $.fn.extend({
    'wxrScrollPic': function (options) {

      options = $.extend({
        numPic: 4 //显示小图的数量 默认4个
      }, options);

      $(this).each(function () {

        var _this = $(this);
        var _list = _this.find(".list").find("ul"); //小图的列表
        var _item = _list.find(".item"); //包裹小图的li
        var _bigPic = _this.find(".pic").find("img"); //大图
        var _leftBtn = _this.find(".prev"); //左按钮
        var _rightBtn = _this.find(".next"); //右按钮

        _item.css({
          "float": "left"
        });

        _list.css({
          "width": _item.length * _item.outerWidth(true),
          "position": "absolute",
          "left": 0,
          "top": 0
        }).parent().css({
          "position": "relative",
          "overflow": "hidden",
          "width": options.numPic * _item.outerWidth(true) + "px"
        })
          .find("li:first").addClass("active");

        var bigPicShow = function () {//切换大图
          var _url = _list.find(".active").find("img").attr("src");
          var _bigUrl = _url.replace("thumb/", "");
          _bigPic.attr("src", _url);
        };

        var rightScollBox = function () {//右翻页
          if (_list.outerWidth(true) - (-_list.position().left) > _list.parent().width()) {
            if (!_list.is("animate")) {
              _list.animate({
                "left": parseInt(_list.css("left")) - _item.outerWidth(true) + "px"
              }, 50);
            }
          }
          ;
        };

        var leftScollBox = function () {//左翻页
          if (_list.position().left != 0) {
            if (!_list.is("animate")) {
              _list.animate({
                "left": parseInt(_list.css("left")) + _item.outerWidth(true) + "px"
              }, 50);
            }
          }
          ;
        };

        var leftBtnClass = function () {//左边按钮是否可点击状态
          if (_list.find(".active").prev("li").length > 0) {
            _leftBtn.addClass("prev_click");
          }
          else {
            _leftBtn.removeClass("prev_click");
          }
        };
        leftBtnClass();

        var rightBtnClass = function () {//右边按钮是否可点击状态
          if (_list.find(".active").next("li").length > 0) {
            _rightBtn.addClass("next_click");
          }
          else {
            _rightBtn.removeClass("next_click");
          }
        };
        rightBtnClass();

        _item.click(function () {//点击小图切换大图
          $(this).addClass("active").siblings().removeClass("active");
          bigPicShow();
          leftBtnClass();
          rightBtnClass();
        });

        _leftBtn.click(function () {//点击左边按钮

          if (_list.find(".active").prev("li").length > 0) {
            _list.find(".active").removeClass("active").prev("li").addClass("active");
            bigPicShow();
          }
          leftBtnClass();
          rightBtnClass();
          leftScollBox();
        });

        _rightBtn.click(function () {//点击右边按钮
          if (_list.find(".active").next("li").length > 0) {
            _list.find(".active").removeClass("active").next("li").addClass("active");
            bigPicShow();
          }

          leftBtnClass();
          rightBtnClass();
          rightScollBox();

        });
      })

    }
  })
})(jQuery);


$(function () {
  $(".show_house_pic").wxrScrollPic();
});
