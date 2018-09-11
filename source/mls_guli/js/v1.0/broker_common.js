//获取经纪人诚信信息
function get_trust_info(broker_id, data_id, type) {
  $.ajax({
    type: 'get',
    url: '/my_trust_info/',
    data: {
      broker_id: broker_id,
      data_id: data_id,
      type: type
    },
    dataType: 'html',
    async: false,
    success: function (data) {
      if (data['errorCode'] == '401') {
        login_out();
        return false;
      }
      else if (data['errorCode'] == '403') {
        purview_none();
        return false;
      }
      $('#broker_info_wrap').html(data);
      //$("#broker_info_wrap").show();
    }
  });
}

$(function () {
  if ($("#broker_info_wrap").length) {
    var iTimerID = null;
    var iTimerID02 = null;
    var broker_info_wrap = $("#broker_info_wrap");
    $(".broker").mouseover(function () {
      clearTimeout(iTimerID02);
      var self = $(this);
      var brokerId = self.attr("data-brokerId");
      var data_id = self.attr("data_id");
      var type = self.attr("type");

      get_trust_info(brokerId, data_id, type);
      var W = broker_info_wrap.innerWidth();
      var H = broker_info_wrap.innerHeight();
      var w = self.width();
      var h = self.height();
      var x = self.offset().left;
      var y = self.offset().top;
      var X = x + w / 2 - W / 2;
      var Y = y - H;
      broker_info_wrap.css({
        'top': Y + 'px',
        'left': X + 'px'
      });
      iTimerID = setTimeout(function () {
        broker_info_wrap.show()
      }, 100)
    }).mouseout(function () {
      clearTimeout(iTimerID);
      iTimerID02 = setTimeout(function () {
        broker_info_wrap.hide()
      }, 100);
    });

    broker_info_wrap.hover(function () {
      clearTimeout(iTimerID);
      clearTimeout(iTimerID02);
      broker_info_wrap.show();
    }, function () {
      clearTimeout(iTimerID);
      clearTimeout(iTimerID02);
      broker_info_wrap.hide();
      broker_info_wrap.empty();
    });
  }
});
