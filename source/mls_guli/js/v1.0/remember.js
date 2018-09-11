//图片滚动展示  wxr
;(function ($) {
  $.fn.extend({
    'remenberFun': function (options) {

      options = $.extend({
        popup: "popup",
        dataSource: [{
          "id": 0,
          "label": "楼盘0",
          "averprice": 0,
          "address": "地址0",
          "status": -1,
          "districtname": "好房0",
          "streetname": "新街口0"
        }, {
          "id": 1,
          "label": "楼盘1",
          "averprice": 1,
          "address": "地址1",
          "status": -1,
          "districtname": "好房1",
          "streetname": "新街口1"
        }, {
          "id": 2,
          "label": "楼盘2",
          "averprice": 2,
          "address": "地址2",
          "status": -1,
          "districtname": "好房2",
          "streetname": "新街口2"
        }]
      }, options);

      $(this).each(function () {
        var _this = $(this);
        var widstr = _this.outerWidth();
        $("." + options.popup).css("width", widstr + "px");
        _this.blur(function () {
          $("." + options.popup).addClass("hide");
        })


        _this.keydown(function (e) {
          var e = e || event;
          var currKey = e.keyCode || e.which || e.charCode;
          var keyName;
          if ((currKey > 7 && currKey < 14) || (currKey > 31 && currKey < 47)) {
            switch (currKey) {
              case 38:
                keyuphandler();
                break;
              case 40:
                keydownhandler();
                break;
              case 13:
                enterhandler();
                break;
            }
          }
          else {
            var str = $("#input_Txt").val();
            checkData(options.dataSource);
          }
        })

        var curLi;
        var curIndex = -1;
        var clickObj;

        function checkData(d) {
          curIndex = -1;
          $(".contentUl").empty();
          var len = d.length;
          var str = "";
          if (len == 0)return;
          setXY();

          for (var i = 0; i < len; i++) {
            str += "<li class='info_li' tab_index=" + "'" + i + "'" + "><a class='info_a' href='javascript:;'>" + d[i].label + "</a></li>";
          }
          $(".contentUl").append(str);
          if ($("." + options.popup).hasClass("hide")) {
            $("." + options.popup).removeClass("hide");
            $("." + options.popup).addClass("show");
          }

          $("." + options.popup).find(".info_li").click(function () {
            //列表  点击事件
            if ($("." + options.popup).hasClass("show")) {
              $("." + options.popup).addClass("hide");
              $("." + options.popup).removeClass("show");
            }
          })


          $("." + options.popup).find(".info_li").mouseover(function () {
            var current = $(this);
            if (clickObj != null) {
              if (clickObj == current)return;
            }
            if (clickObj == null) {
              if (curIndex != -1) {
                $(".info_li").eq(curIndex).removeClass("li_bg");
              }
              current.addClass("li_bg");
              curIndex = 0;
            }
            else {
              clickObj.removeClass("li_bg");
              current.addClass("li_bg");
              curIndex = current.attr("tab_index");
            }
            clickObj = current;
          })
          $("." + options.popup).find(".info_li").mousemove(function () {

            var current = $(this);
            if (clickObj != null) {
              if (clickObj == current)return;
            }
            if (clickObj == null) {
              curIndex = 0;
            }
            else {
              curIndex = current.attr("tab_index");
            }
            setTxtValue();

          })
        }

        function setXY() {
          var x_1 = _this.offset();
          var lOffset = x_1.left;
          var tOffset = x_1.top + _this.outerHeight();
          $("." + options.popup).css({left: lOffset, top: tOffset});
        }


        function keyuphandler() {
          var len = $(".info_li").length;
          if (curIndex == -1 || curIndex == 0) {
            if (curIndex == 0) {
              $(".info_li").eq(0).removeClass("li_bg");
            }
            curIndex = len - 1;
            $(".info_li").eq(curIndex).focus();
            $(".info_li").eq(curIndex).addClass("li_bg");
          } else {
            curIndex--;
            $(".info_li").eq(curIndex).focus();
            $(".info_li").eq(curIndex).addClass("li_bg");
            $(".info_li").eq(curIndex + 1).removeClass("li_bg");
          }
          setTxtValue();
        }

        function keydownhandler() {
          if (curIndex == -1) {
            curIndex++;
            $(".info_li").eq(curIndex).focus();
            $(".info_li").eq(curIndex).addClass("li_bg");
          }
          else {
            var len = $(".info_li").length;
            if (curIndex == len - 1)curIndex = -1;
            curIndex++;
            $(".info_li").eq(curIndex).focus();
            $(".info_li").eq(curIndex).addClass("li_bg");
            $(".info_li").eq(curIndex - 1).removeClass("li_bg");
          }
          clickObj = $(".info_li").eq(curIndex);
          console.log(curIndex);
          setTxtValue();
        }

        function enterhandler() {
          if ($("." + options.popup).hasClass("show")) {
            $("." + options.popup).addClass("hide");
            $("." + options.popup).removeClass("show");
          }
          if (curIndex == -1)return;
          var data1 = options.dataSource[curIndex];
          _this.val(data1.label);
          _this.attr("streetname", data1.streetname);
          _this.attr("districtna", data1.districtna);
          _this.attr("status", data1.status);
          _this.attr("address", data1.address);
          _this.attr("averprice", data1.averprice);
          curIndex = -1;//重置
          //	_this.focus();
        }

        function setTxtValue() {
          if ($("." + options.popup).hasClass("show")) {
            var data1 = options.dataSource[curIndex];
            _this.val(data1.label);
            _this.attr("streetname", data1.streetname);
            _this.attr("districtna", data1.districtna);
            _this.attr("status", data1.status);
            _this.attr("address", data1.address);
            _this.attr("averprice", data1.averprice);
          }

        }

        var js_this = this;
        $(window).resize(function (e) {
          var lOffset = js_this.offsetLeft;
          var tOffset = js_this.offsetTop + _this.outerHeight();
          $("." + options.popup).css({left: lOffset, top: tOffset});
        });


      })


    }
  })
})(jQuery);


$(function () {
  $("#input_Txt").remenberFun({});
});
