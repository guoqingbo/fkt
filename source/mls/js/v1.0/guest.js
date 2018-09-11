//公盘列表页 区属联动
function districtchange(districtid, type) {
  $.ajax({
    type: 'get',
    url: MLS_URL + '/' + type + '/find_street_bydis/' + districtid,
    dataType: 'json',
    success: function (msg) {
      var str = '';
      if (msg.result == 'no result') {
        str = '<option value="">不限</option>';
      } else {
        str = '<option value="">不限</option>';
        for (var i = 0; i < msg.length; i++) {
          str += '<option value="' + msg[i].id + '">' + msg[i].streetname + '</option>';
        }
      }
      $('#street').empty();
      $('#street').append(str);
    }
  });
}
