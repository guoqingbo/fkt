//***************IE6下Fixed定位*******************
;(function ($) {
  var isIE6 = ($.browser.msie && ($.browser.version == "6.0") && !$.support.style)
    , _$html = $('html');
  // 给IE6 fixed 提供一个"不抖动的环境"
  // 只需要 html 与 body 标签其一使用背景静止定位即可让IE6下滚动条拖动元素也不会抖动
  // 注意：IE6如果 body 已经设置了背景图像静止定位后还给 html 标签设置会让 body 设置的背景静止(fixed)失效
  if (isIE6 && _$html.css('backgroundAttachment') !== 'fixed' && $('body').css('backgroundAttachment') !== 'fixed') {
    _$html.css({
      zoom: 1,// 避免偶尔出现body背景图片异常的情况
      backgroundImage: 'url(about:blank)',
      backgroundAttachment: 'fixed'
    });
  }
  ;
  $.fn.fixed = function () {
    var elem = $(this)[0];
    if (isIE6) {
      var style = elem.style,
        dom = '(document.documentElement || document.body)',
        _left = ($(window).width() - $(this).width()) / 2,
        _top = ($(window).height() - $(this).height()) / 2;
      $(this).css("position", 'absolute').css({
        "left": $(window).scrollLeft() + _left,
        "top": $(window).scrollTop() + _top
      });
      style.setExpression('left', 'eval(' + dom + '.scrollLeft + ' + _left + ') + "px"');
      style.setExpression('top', 'eval(' + dom + '.scrollTop + ' + _top + ') + "px"');
    } else {
      $(this).css({
        "position": 'fixed',
        left: ($(window).width() - $(this).width()) / 2,
        top: ($(window).height() - $(this).height()) / 2
      });
    }
    return $(this);
  }
})(jQuery);
