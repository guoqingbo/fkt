function getSwf(movieName) {
  if (window.document[movieName]) {
    return window.document[movieName];
  } else if (navigator.appName.indexOf("Microsoft") == -1) {
    if (document.embeds && document.embeds[movieName])
      return document.embeds[movieName];
  } else {
    return document.getElementById(movieName);
  }
}

//award = '';//得到奖品名称
award_writer = '';//得到奖品描述
credit_total = '';//抽奖者剩余积分
credit_num = '';//抽奖者剩余抽奖次数
is_lottery = true;
function start_lottery() {
  if (is_lottery == true) {
    is_lottery = false;
  }
  else {
    return false;
  }
  var scode = $('#scode').val();
  $.ajax({
    url: '/gift_exchange/lottery?scode=' + scode,
    type: "get",
    data: {code: $('#code').val()},
    dataType: "json",
    cache: false,
    beforeSend: function () {// 提交之前
    },
    error: function () {//出错
      getSwf('lottery').reset_lottery();//取消“正中抽奖中”标志，则可重新抽奖
      alert('服务端出错！');
    },
    success: function (res) {//成功
      is_lottery = true;
      if (res.result == 1) {
        if (typeof(res.award_id) != 'undefined' && res.award_id != '') {
          //award = res.award_name;//得到奖品名称
          award_writer = res.award_writer;
          getSwf('lottery').show_lottery();//展现转动效果
          setTimeout(function () {//得到抽奖结果，等5秒钟转动效果才显示结果
            getSwf('lottery').stop_lottery(res.award_id);
          }, 5000);
        } else {
          getSwf('lottery').reset_lottery();//取消“正中抽奖中”标志，则可重新抽奖
          award = res.award_name;//得到奖品名称
          award_writer = res.award_writer;
          lottery_result();
        }
        credit_total = res.credit_total;//抽奖者剩余积分
        credit_num = res.credit_num;//抽奖者剩余抽奖次数
      }
      else {
        alert(res.reason);
      }
    }
  });
}
//结束后调用的函数
function lottery_result() {
  //结束后调用的函数
  //$("#layer h3").html(award);
  if (credit_total != '') {
    $("#credit_total").html(credit_total);
    $("#credit_num").html(credit_num);
  }
  $("#layer #layer_text").html(award_writer);
  $("#layer").show();
  $(".app_cj_con_bj_remind").show();
}
