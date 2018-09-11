var openWin = function (obj, fun, cfun) {
  var winElemt = $('#' + obj);
  var $coverDom = '<div class="js_GTipsCoverWxr" id="GTipsCover' + obj + '" style="background:#000;position:absolute;filter:alpha(opacity=0);opacity:0;width:100%;left:0;top:0;z-index:9999901;height:' + $(window).height() + 'px;"><iframe src=\"about:blank\" style=\"height:' + $(document).height() + 'px;width:100%;filter:alpha(opacity=0);opacity:0;scrolling=no;\"></iframe></div>'
  var win = {
    height: function () {
      return winElemt.outerHeight();
    },
    width: function () {
      return winElemt.outerWidth();
    },
    show: function () {
      winElemt.css({
        'position': 'absolute',
        // 'position': 'fixed',
        'z-index': 99902 * ($(".js_GTipsCoverWxr").length + 2),
        'left': '50%',
        'margin-left': -(win.width() / 2) + 'px',
        'margin-top': -(win.height() / 2) + 'px',
        'top': '50%'//$(window).height()/2 - (win.height()/2) + $(window).scrollTop() + 'px'
      }).show();
      $($coverDom).appendTo('body').css({
        opacity: 0,
        'z-index': 99901 * ($(".js_GTipsCoverWxr").length + 1)
      })
    },
    hide: function () {
      winElemt.hide();
      $('#' + 'GTipsCover' + obj).remove();
      $(window.parent.document).find('#' + obj).hide();
      $(window.parent.document).find('#' + 'GTipsCover' + obj).remove();
    }

  };

  /*	$(window).scroll(function(){
   winElemt.
   ({
   'top':$(window).height()/2-(win.height()/2)+$(window).scrollTop()+'px'
   });
   });
   */
  $(window).resize(function (e) {
    $("#GTipsCover" + obj).css({
      'height': $(window).height() + 'px',
      'width': '100%'
    });
  });
  win.show();
  $('#' + obj).find('.JS_Close').live("click", function () {
    win.hide();
    var fn = $(this).attr("date-iframe");
    if (fn == 1) {
      $('#' + obj).find('.iframePop').attr('src', '');
    }
    if (cfun) {
      cfun();
    }
  });
  if (fun) {
    fun();
  }
};

function closeParentWin(iframe_id) {
  $(window.parent.document).find("#" + iframe_id).css('display', 'none');
  $(window.parent.document).find("#GTipsCover" + iframe_id).remove();
}

function closeWindowWin(iframe_id) {
  $("#" + iframe_id).css('display', 'none');
  $("#GTipsCover" + iframe_id).remove();
}

//登出
function login_out(url, msg, delay_time, elements_id) {
  window.external.go2Login("");
  return true;
  var skipUrl = url ? url : (MLS_URL + "/login/");//跳转的页面地址 url有传值就为url 否则默认登录页
  var skipMsg = msg ? msg : "请重新登录";//提示文字 msg如果有传值就为msg 否则默认"请重新登录"
  var skipTime = delay_time ? delay_time : 3000;//多长时间后自动跳转 delay_time传值就为delay_time 否则默认3000毫秒
  var skipId = elements_id ? elements_id : "skipIdWxr";//创建提示框的ID  elements_id有传值时候为elements_id 否则默认为"skipIdWxr"
  var oDiv = '<div style="width:240px;" class="dialog radius5" id="' + skipId + '">';
  oDiv += '<div class="hd"><h3 class="h3">提示</h3></div><div class="mod">';//<span class="close close-win" title="关闭">Χ</span>
  oDiv += '<div class="text" style="margin-left:37px;font-size:14px;">' + skipMsg + '</div>';
  oDiv += '<div style="padding:10px 10px 0;text-align: center;">'
  oDiv += '如果页面不能自动跳转，<a style="color:#3887f2" href="' + skipUrl + '">';
  oDiv += '请点击这里</a></div></div></div>';

  $(oDiv).appendTo("body");

  if ($(".jsGTipsCover").length > 0) {
    //如果页面已经存在遮罩层 则先移除遮罩层
    $(".jsGTipsCover").remove();
  }
  ;
  openWin(skipId);
  setInterval(function () {
    window.location.href = skipUrl
  }, skipTime);
  return false;
}

//没有权限访问
var permission_none = function (msg) {
  msg = typeof msg == 'undefined' ? '&nbsp;对不起，您没有访问权限！' : msg;
  $('#dialog_do_purview_none_tip').html(msg);
  openWin('js_pop_do_purview_none');
}
//没有权限访问
var purview_none = function (msg) {
  msg = typeof msg == 'undefined' ? '&nbsp;对不起，您没有访问权限！' : msg;
  $('#dialog_do_purview_none_tip').html(msg);
  openWin('js_pop_do_purview_none');
}
//IEinput:focus Hack
if (jQuery.browser.msie === true) {
  jQuery('input,textarea')
    .live('focus', function () {
      $(this).addClass('ieFocusHack');
    }).live('blur', function () {
    $(this).removeClass('ieFocusHack');
  });
}

$(function () {
  //打开im对话框
  $("#im_icon").click(function (event) {
    $(window.parent.document).find('#target_id').val($(this).attr('broker_id'));
    $(window.parent.document).find('#conversationTitle').html($(this).attr('broker_name'));
    $(window.parent.document).find('#iframe_im').focus();
    $(window.parent.document).find('#im_box').show();
    $(window.parent.document).find('#mainContent').focus();
    event.stopPropagation();    //  阻止事件冒泡
  });
  $("#im_icon1").click(function (event) {
    $(window.parent.parent.document).find('#target_id').val($(this).attr('broker_id'));
    $(window.parent.parent.document).find('#conversationTitle').html($(this).attr('broker_name'));
    $(window.parent.parent.document).find('#iframe_im').focus();
    $(window.parent.parent.document).find('#im_box').show();
    $(window.parent.parent.document).find('#mainContent').focus();
    event.stopPropagation();    //  阻止事件冒泡
  });

  $("#uncooperate").click(function (event) {
    $("#dialog_do_warnig_tip").html("只有发起合作后才能与对方聊天");
    openWin('js_pop_do_warning');
    event.stopPropagation();    //  阻止事件冒泡
  });
});
