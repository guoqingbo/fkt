//举报页面弹跳
function report(type, ct_id, cooper_type) {
  var _url = MLS_URL + '/cooperate/' + type + '/' + ct_id + '/' + cooper_type;

  if (_url) {
    $("#js_woyaojubao .iframePop").attr("src", _url);
  }

  openWin('js_woyaojubao');
}
