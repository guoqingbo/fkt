function message(id, box) {
  var _h3 = $('#' + box + ' .mod .inform_inner h3');
  var _time = $('#' + box + '  .mod .inform_inner .time');
  var _text = $('#' + box + '  .mod .inform_inner .text')
  _h3.empty();
  _time.empty();
  _text.empty();
  $.ajax({
    type: 'POST',
    url: "/message/details/",
    data: {id: id},
    dataType: 'json',
    success: function (data) {
      $("#tr" + id + " .c2 .info span").remove();
      _h3.html(data.title);
      _time.html(data.createtime);
      _text.html(data.message);
    }
  });
  openWin('js_see_msg_info');
}
