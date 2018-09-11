//门店和所拥有的经纪人的二级联动

//转出经纪人

$(function () {
  function get_store_out_broker() {
    var id = $("#store_name_out").val();

    $.ajax({
      url: MLS_URL + "/data_transfer/get_all_by",
      type: "post",
      dataType: "json",
      data: {
        id: id,
        mark: "buy_customer"
      },
      success: function (data) {
        var broker_id_out = $("#broker_id_out").val();
        if ("none" == data) {
          $("#group_list_out").empty();
          $("#group_list_out").append("<option value='0'>请选择</option>");
        } else if (data.length == "0") {
          $("#group_list_out").empty();
          $("#group_list_out").append("<option value='0'>请选择</option>");
        } else {
          $("#group_list_out").empty();
          $("#group_list_out").append("<option value='0'>请选择</option>");
          for (var i in data) {
            var bid = data[i]['broker_id'];
            $("#group_list_out").append("<option id='op" + bid + "' value='" + bid + "'>" + data[i]['truename'] + "</option>");
            if (bid == broker_id_out) {
              $("#op" + bid).attr("selected", true);
            }
          }
        }
      }
    });
  }

  $("#store_name_out").change(function () {
    get_store_out_broker();
  });

  get_store_out_broker();
});


//转入经纪人
$(function () {
  function get_store_in_broker() {
    var id = $("#store_name_in").val();

    $.ajax({
      url: MLS_URL + "/data_transfer/get_all_by",
      type: "post",
      dataType: "json",
      data: {
        id: id,
        mark: "buy_customer"
      },
      success: function (data) {
        var broker_id_in = $("#broker_id_in").val();
        if ("none" == data) {
          $("#group_list_in").empty();
          $("#group_list_in").append("<option value='0'>请选择</option>");
        } else if (data.length == "0") {
          $("#group_list_in").empty();
          $("#group_list_in").append("<option value='0'>请选择</option>");
        } else {
          $("#group_list_in").empty();
          //$("#group_list_in").append("<option value='0'>请选择</option>");
          for (var i in data) {
            var bid = data[i]['broker_id'];
            $("#group_list_in").append("<option id='opt" + bid + "' value='" + bid + "'>" + data[i]['truename'] + "</option>");
            if (bid == broker_id_in) {
              $("#opt" + bid).attr("selected", true);
            }
          }
        }
      }
    });

  }


  $("#store_name_in").change(function () {
    get_store_in_broker();
  });

  get_store_in_broker();
});
