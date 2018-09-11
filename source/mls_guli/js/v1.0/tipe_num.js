// 提示还剩多少字
//obj  为输入文字框的ID
//n 为最多字数
//showNum 显示剩余字数的ID
function maxText(obj, n, showNum) {
  $("#" + obj).keyup(function () {
    //定义最多输入数字
    var _max = n;
    //取得用户输入的字符的长度
    var _length = $("#" + obj).val().length;

    if (_length < _max) {
      $("#" + showNum).html(_max - _length);
    } else if (_length = _max) {
      $("#" + showNum).html('0');
      var num = $("#" + obj).val().substr(0, n);
      $("#" + obj).val(num);
    }
  })

};
maxText('d_f_textarea', 20, "js_num");
