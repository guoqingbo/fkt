//图片滚动展示  wxr
;(function ($) {
  $.fn.extend({
    'wxrScrollPic': function (options) {

      options = $.extend({
        numPic: 5 //显示小图的数量 默认4个
      }, options);

      $(this).each(function () {

        var _this = $(this);
        var _list = _this.find(".list").find("ul"); //小图的列表
        var _item = _list.find(".item"); //包裹小图的li
        var _bigPic = _this.find(".pic").find("img"); //大图
        var _leftBtn = _this.find(".prev"); //左按钮
        var _rightBtn = _this.find(".next"); //右按钮

        var _imgHeight = _bigPic.height(); //大图高度
        var _picActPre = 635 / 340;//大图实际比例
        _list.css({
          "height": _item.length * _item.outerHeight(true),
          "position": "absolute",
          "right": 0,
          "top": 0
        }).parent().css({
          "height": options.numPic * _item.outerHeight(true) + "px"
        })
          .find("li:first").addClass("active");

        var bigPicShow = function () {//切换大图
          var _url = _list.find(".active").find("img").attr("src");
          var _bigUrl = _url.replace("thumb/", "");
          _bigPic.attr("src", _bigUrl);

          //计算大图宽度和高度

          var _bigPicHeight = _bigPic.height(); //切换获取大图高度
          var _bigPicWidth = _bigPic.width();   //切换获取大图宽度
          var _bigPicPer = _bigPicWidth / _bigPicHeight; // 大图宽高比
          //console.log(_bigPicHeight+"+++"+_bigPicWidth);
          if (_bigPicPer < _picActPre) {
            var _bigPicNewWidth = 340 / _bigPicPer;
            _bigPic.css({
              "margin-top": "0",
              //"width"     :_bigPicNewWidth+"px",
              "height": "340px"
            })
            //console.log(_bigPicPer);
          }
          else {
            var _bigPicNewHeight = 635 * _bigPicPer;
            _bigPic.css({
              "margin-top": (340 - _bigPicNewHeight) / 2 + "px",
              "width": "635px"
              //"height"    :_bigPicNewHeight+"px"
            })

            //console.log(2);
          }

        };

        function pos(that) {
          _bigPic.css({
            "margin-top": 0
          })
          if (that.find(".img").height() < 340) {
            _bigPic.css({
              //"margin-top":(340 - that.find(".img").height())/2
            })
          }
        }

        var rightScollBox = function () {//右翻页
          if (_list.outerHeight(true) - (-_list.position().top) > _list.parent().height()) {
            if (!_list.is("animate")) {
              _list.animate({
                "top": parseInt(_list.css("top")) - _item.outerHeight(true) + "px"
              }, 50);
            }
          }
          ;
        };

        var leftScollBox = function () {//左翻页
          if (_list.position().top != 0) {
            if (!_list.is("animate")) {
              _list.animate({
                "top": parseInt(_list.css("top")) + _item.outerHeight(true) + "px"
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
          pos(_list.find(".active"));
          bigPicShow();
          leftBtnClass();
          rightBtnClass();
        });

        _leftBtn.click(function () {//点击左边按钮

          if (_list.find(".active").prev("li").length > 0) {
            _list.find(".active").removeClass("active").prev("li").addClass("active");
            pos(_list.find(".active"));
            bigPicShow();
          }
          leftBtnClass();
          rightBtnClass();
          leftScollBox();
        });

        _rightBtn.click(function () {//点击右边按钮
          if (_list.find(".active").next("li").length > 0) {
            _list.find(".active").removeClass("active").next("li").addClass("active");
            pos(_list.find(".active"));
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
