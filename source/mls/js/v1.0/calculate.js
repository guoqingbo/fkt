$(function () {
  $("#js_taxquanxian01").change(function () { //权属 住宅
    var qs = $("#js_qsbj01,#js_jyzhuzhai01,#js_chanquanzhuzhai01,#js_gtzhuzhai01")
    if (this.checked) {
      qs.attr("checked", true);//设置 契税和交易手续费
    }
  });
  $("#js_taxquanxian02").change(function () {//权属 商业
    var qs = $("#js_qsbj02,#js_jyzhuzhai02,#js_chanquanzhuzhai02,#js_gtzhuzhai02");
    if (this.checked) {
      qs.attr("checked", true);//设置 契税和交易手续费
    }
  });

  $("#js_taxleibie01").change(function () {//设置 电梯和非电梯
    if (this.checked) {
      $("#js_wxdianti02").attr({"checked": false, "disabled": true});
      $("#js_wxdianti01").attr("checked", true).removeAttr("disabled");
    }
  });

  $("#js_taxleibie02").change(function () {//设置 电梯和非电梯
    if (this.checked) {
      $("#js_wxdianti01").attr({"checked": false, "disabled": true});
      $("#js_wxdianti02").attr("checked", true).removeAttr("disabled");
    }
  });

  $("#js_wxdianti01").change(function () {// 设置维修基金
    if (this.checked) {
      $("#js_wxdianti02").attr({"checked": false, "disabled": true});
      $("#js_taxleibie01").attr("checked", true);
    }
    else {
      $("#js_wxdianti02").attr("checked", false).removeAttr("disabled");
      $("#js_taxleibie01").attr("checked", false);
    }
  });

  $("#js_wxdianti02").change(function () {// 设置维修基金
    if (this.checked) {
      $("#js_wxdianti01").attr({"checked": false, "disabled": true});
      $("#js_taxleibie02").attr("checked", true);
    }
    else {
      $("#js_wxdianti01").attr("checked", false).removeAttr("disabled");
      $("#js_taxleibie02").attr("checked", false);
    }
  });

  $("#js_yingyeshui").change(function () {//营业税
    if (this.checked) {
      $("#js_yysbj01").attr("checked", true);
      $("#js_yysbj02").attr({"checked": false, "disabled": true});
    }
    else {
      $("#js_yysbj01").attr("checked", false);
      $("#js_yysbj02").removeAttr("disabled");
    }
    $("#js_yysbj01").removeAttr("disabled");
  });

  $("#js_yysbj01").change(function () {//营业税
    if (this.checked) {
      $("#js_yingyeshui").attr("checked", true);
      $("#js_yysbj02").attr({"checked": false, "disabled": true});
    }
    else {
      $("#js_yingyeshui").attr("checked", false);
      $("#js_yysbj02").removeAttr("disabled");
    }
  });

  $("#js_yysbj02").change(function () {//营业税
    if (this.checked) {
      $("#js_yingyeshui").attr("checked", true);
      $("#js_yysbj01").attr({"disabled": true});
    }
    else {
      $("#js_yysbj01").removeAttr("disabled");
      $("#js_yingyeshui").attr("checked", false);
    }
  });

  $("#js_geshui").change(function () {//个税
    if (this.checked) {
      $("#js_perbj01").attr("checked", true);
      $("#js_perbj02").attr({"checked": false, "disabled": true});
    }
    else {
      $("#js_perbj01").attr("checked", false);
      $("#js_perbj02").removeAttr("disabled");
    }
    $("#js_perbj01").removeAttr("disabled");
  });

  $("#js_perbj01").change(function () {//个税
    var gs = $("#js_geshui");
    var pe = $("#js_perbj02");
    if (this.checked) {
      gs.attr("checked", true);
      pe.attr({"checked": false, "disabled": true});
    }
    else {
      gs.attr("checked", false);
      pe.removeAttr("disabled");
    }
  });

  $("#js_perbj02").change(function () {//个税
    var gs = $("#js_geshui");
    var pe = $("#js_perbj01");
    if (this.checked) {
      gs.attr("checked", true);
      pe.attr({"disabled": true});
    }
    else {
      pe.removeAttr("disabled");
      gs.attr("checked", false);
    }
  });

  $("#js_calculate_btn").click(function () {
    if ($("#js_yysbj02").prop("checked") || $("#js_perbj02").prop("checked")) {
      $("#js_y_jiage").addClass("js_r_input");
    }
    else {
      $("#js_y_jiage").removeClass("js_r_input e_bg_input");
    }
    ;

    $(".js_r_input").each(function (index, element) {
      var _this = $(this);
      var num = parseFloat(_this.val())
      if (num != "") {
        if (isNaN(num)) {
          _this.addClass("e_bg_input");
        }
        else {
          _this.removeClass("e_bg_input");
          _this.val(num);
        }
      }
      else {
        _this.addClass("e_bg_input")
      }
      ;
    });
    var z_j = $("#js_mianji").val() * $("#js_danjia").val();//报税总价
    if ($(".e_bg_input").length < 1)//如果没有漏填 就开始计算
    {
      //计算契税开始
      var q_l = $("#js_qsbj01").prop("checked") ? $("#js_qsbj_input").val() : $("#js_qsbj_input02").val();
      $("#js_qs_m01").html(z_j * q_l / 100 + "元");
      $("#js_qs_m02").html("无");
      //计算契税结束

      //计算印花税开始
      if ($("#js_yhsbj").prop("checked")) {
        $("#js_yhsbj_input01,#js_yhsbj_input02").html(z_j * $("#js_yhsbj_input").val() / 100 + "元")
      }
      else {
        $("#js_yhsbj_input01,#js_yhsbj_input02").html("")
      }
      //计算印花税结束

      //计算交易手续费开始
      if ($("#js_jyzhuzhai01").prop("checked")) {
        $("#js_jys_input01,#js_jys_input02")
          .html($("#js_mianji").val() * $("#js_sxf_m").val() + "元");
      }
      else {
        $("#js_jys_input01,#js_jys_input02")
          .html(z_j * $("#js_sc_jysxf").val() / 100 + "元");
      }
      //计算交易手续费结束

      //计算维修基金开始
      if ($("#js_wxdianti01").prop("checked")) {
        $("#js_wxj").html("无");
        $("#js_wxj_01").html($("#js_mianji").val() * $("#js_j_fdt").val() + "元");
      }
      else if ($("#js_wxdianti02").prop("checked")) {
        $("#js_wxj").html("无");
        $("#js_wxj_01").html($("#js_mianji").val() * $("#js_j_dt").val() + "元");
      }
      else {
        $("#js_wxj_01,#js_wxj").html("");
      }
      //计算维修基金结束

      //计算营业税开始
      if ($("#js_yysbj01").prop("checked")) {
        $("#js_yys_input01").html("无");
        $("#js_yys_input02").html(z_j * $("#js_yys001").val() / 100 + "元")
      }
      else if ($("#js_yysbj02").prop("checked")) {
        $("#js_yys_input01").html("无");
        var _m = z_j - $("#js_y_jiage").val() * 10000 > 0 ? z_j - $("#js_y_jiage").val() * 10000 : 0
        $("#js_yys_input02").html(_m * $("#js_yys001").val() / 100 + "元")
      }
      else {
        $("#js_yys_input01,#js_yys_input02").html("");
      }
      //计算营业税结束

      //计算个人所得税开始
      if ($("#js_perbj01").prop("checked")) {
        $("#js_grsd_tex01").html("无");
        $("#js_grsd_tex02").html(z_j * $("#js_grsd_input01").val() / 100 + "元");
      }
      else if ($("#js_perbj02").prop("checked")) {
        $("#js_grsd_tex01").html("无");
        var m = z_j - $("#js_y_jiage").val() * 10000 > 0 ? z_j - $("#js_y_jiage").val() * 10000 : 0
        $("#js_grsd_tex02").html(m * $("#js_grsd_input02").val() / 100 + "元");
      }
      else {
        $("#js_grsd_tex01,#js_grsd_tex02").html("");
      }

      //计算个人所得税结束
      //产权登记费开始
      if ($("#js_chanquanzhuzhai01").prop("checked")) {
        $("#js_cqdj_tex01").html($("#js_cqdjs_input01").val() + "元");
        $("#js_cqdj_tex02").html("无");
      }
      else {
        $("#js_cqdj_tex01").html($("#js_cqdjs_input02").val() + "元");
        $("#js_cqdj_tex02").html("无");
      }

      //产权登记费结束

      //国土证工本费开始
      if ($("#js_gtzhuzhai01").prop("checked")) {
        $("#js_gtgbf01").html($("#js_gtgbf_inoput01").val() + "元");
        $("#js_gtgbf02").html("无");
      }
      else {
        $("#js_gtgbf01").html($("#js_gtgbf_inoput02").val() + "元");
        $("#js_gtgbf02").html("无");
      }
      //国土证工本费结束

      //土地出让金开始
      if ($("#js_tdcrj_checkbox").prop("checked")) {
        $("#js_tdcrj_text01").html(z_j * $("#js_tdcrj_input").val() / 100 + "元");
        $("#js_tdcrj_text02").html("无");
      }
      else {
        $("#js_tdcrj_text01,#js_tdcrj_text02").html("");
      }
      //土地出让金结束

      //报税总价开始
      $("#js_bszj_input").val(z_j / 10000)
      //报税总价结束

      //税费总计开始
      var m01_1 = isNaN(parseFloat($("#js_qs_m01").text())) ? 0 : parseFloat($("#js_qs_m01").text());
      var m01_2 = isNaN(parseFloat($("#js_yhsbj_input01").text())) ? 0 : parseFloat($("#js_yhsbj_input01").text());
      var m01_3 = isNaN(parseFloat($("#js_jys_input01").text())) ? 0 : parseFloat($("#js_jys_input01").text());
      var m01_4 = isNaN(parseFloat($("#js_wxj").text())) ? 0 : parseFloat($("#js_wxj").text());
      var m01_5 = isNaN(parseFloat($("#js_yys_input01").text())) ? 0 : parseFloat($("#js_yys_input01").text());
      var m01_6 = isNaN(parseFloat($("#js_grsd_tex01").text())) ? 0 : parseFloat($("#js_grsd_tex01").text());
      var m01_7 = isNaN(parseFloat($("#js_cqdj_tex01").text())) ? 0 : parseFloat($("#js_cqdj_tex01").text());
      var m01_8 = isNaN(parseFloat($("#js_tdcrj_text01").text())) ? 0 : parseFloat($("#js_tdcrj_text01").text());
      var m01_9 = isNaN(parseFloat($("#js_gtgbf01").text())) ? 0 : parseFloat($("#js_gtgbf01").text());

      var m01 = m01_1 + m01_2 + m01_3 + m01_4 + m01_5 + m01_6 + m01_7 + m01_8 + m01_9;

      var m02_1 = isNaN(parseFloat($("#js_qs_m02").text())) ? 0 : parseFloat($("#js_qs_m02").text());
      var m02_2 = isNaN(parseFloat($("#js_yhsbj_input02").text())) ? 0 : parseFloat($("#js_yhsbj_input02").text());
      var m02_3 = isNaN(parseFloat($("#js_jys_input02").text())) ? 0 : parseFloat($("#js_jys_input02").text());
      var m02_4 = isNaN(parseFloat($("#js_wxj_01").text())) ? 0 : parseFloat($("#js_wxj_01").text());
      var m02_5 = isNaN(parseFloat($("#js_yys_input02").text())) ? 0 : parseFloat($("#js_yys_input02").text());
      var m02_6 = isNaN(parseFloat($("#js_grsd_tex02").text())) ? 0 : parseFloat($("#js_grsd_tex02").text());
      var m02_7 = isNaN(parseFloat($("#js_cqdj_tex02").text())) ? 0 : parseFloat($("#js_cqdj_tex02").text());
      var m02_8 = isNaN(parseFloat($("#js_tdcrj_text02").text())) ? 0 : parseFloat($("#js_tdcrj_text02").text());
      var m02_9 = isNaN(parseFloat($("#js_gtgbf02").text())) ? 0 : parseFloat($("#js_gtgbf02").text());
      var m02 = m02_1 + m02_2 + m02_3 + m02_4 + m02_5 + m02_6 + m02_7 + m02_8 + m02_9;
      $("#js_bszj_input").val(z_j / 10000);
      $("#js_sfzj_input").val(m01 + m02);
      $("#js_m01_num").val(m01);
      $("#js_m02_num").val(m02);
      //税费总计结束

    }

  });


  $("#js_mianji").keyup(function () {//面积
    var num = parseFloat($("#js_mianji").val())
    if (num < 90) {
      $("#js_qsbj_input").val("1");
    }
    if (num > 90) {
      if (num < 144) {
        $("#js_qsbj_input").val("1.5");
      }
    }
    if (num > 144) {
      $("#js_qsbj_input").val("3");
    }
  });
});


/**贷款计算器**/

$(function () {
  $("#js_dengename02").change(function () {
    if (this.checked) {
      $("#js_yjhk_parent").hide();
      $("#js_ym_ys_parent,#js_hkxe_parent").show();
      $("#js_last_tr").addClass("taxtrbg");
    }
  });
  $("#js_dengename01").change(function () {
    if (this.checked) {
      $("#js_yjhk_parent").show();
      $("#js_ym_ys_parent,#js_hkxe_parent").hide();
      $("#js_last_tr").removeClass("taxtrbg");
    }
  });

  $("#js_hklb_select").change(function () {
    if ($(this).val() == 1 || $(this).val() == 2) {
      $(".js_dk_textInput").show();
      $(".js_zhdk_texInput").hide();
    }
    else {
      $(".js_dk_textInput").hide();
      $(".js_zhdk_texInput").show();
    }
  });

  $("#js_years,#lilv").change(function () {
    $("#js_gjg_tex").text(returnLiLv()[0]);
    $("#js_sd_tex").text(returnLiLv()[1]);
  });

  //按揭年数、倍数下拉框触发手动输入
  $("#years,#lilv").change(function () {
    //手动输入赋值
    //贷款类别
    var my_type = $('input[name="my-type"]:checked').val();
    //按揭年数
    var years = $('#years').val();
    //贷款利率
    var lilv_val = $("#lilv option:selected").val();
    var lilv = getlilv(lilv_val, my_type, years);//得到利率
    //小数点处理
    var lilv_result = lilv * 100;
    $("#lilv_input").val(lilv_result.toFixed(2));
    //根据手动输入倍数，计算出结果
    var lilv_multiple = $('#lilv_multiple').val();
    var result = (lilv_result.toFixed(2)) * lilv_multiple;
    $("#lilv_result").text(result.toFixed(2));
  });

  $("#js_calculate_btn_daikuan").click(function () {
    var val;
    $("[name='dengename']").each(function () {
      if ($(this).attr("checked")) {
        val = $(this).attr("value");
      }
    });
    if (val == 1) {
      var type;
      type = $("#js_hklb_select").val();
      if (type == 1) {
        $("#show_all_dai").text(calFun01()[0]);
        $("#show_gjj").text(calFun01()[1]);
        $("#show_sd").text(calFun01()[2]);
        $("#show_all_huan").text(calFun01()[3]);
        $("#show_lixi").text(calFun01()[5]);
        $("#show_yueshu").text(calFun01()[7]);
        $("#show_yuejun").text(calFun01()[8]);
      } else if (type == 2) {
        $("#show_all_dai").text(calFun01()[0]);
        $("#show_gjj").text(calFun01()[1]);
        $("#show_sd").text(calFun01()[2]);
        $("#show_all_huan").text(calFun01()[4]);
        $("#show_lixi").text(calFun01()[6]);
        $("#show_yueshu").text(calFun01()[7]);
        $("#show_yuejun").text(calFun01()[9]);
      } else {
        $("#show_all_dai").text(calFun01()[0]);
        $("#show_gjj").text(calFun01()[1]);
        $("#show_sd").text(calFun01()[2]);
        $("#show_all_huan").text(calFun01()[11]);
        $("#show_lixi").text(calFun01()[10]);
        $("#show_yueshu").text(calFun01()[7]);
        $("#show_yuejun").text(calFun01()[12]);
      }
    } else {
      var type;
      type = $("#js_hklb_select").val();
      if (type == 1 || type == 2) {
        $("#show_all_dai").text(calFun02()[0]);
        $("#show_gjj").text(calFun02()[5]);
        $("#show_sd").text(calFun02()[6]);
        $("#show_all_huan").text(calFun02()[2]);
        $("#show_lixi").text(calFun02()[8]);
        $("#show_yueshu").text(calFun02()[7]);
        $("#show_syhk").text(calFun02()[1]);
        $("#show_myhk").text(calFun02()[4]);
        $("#js_hkxe").text(calFun02()[3]);
      } else {
        $("#show_all_dai").text(calFun02()[0]);
        $("#show_gjj").text(calFun02()[5]);
        $("#show_sd").text(calFun02()[6]);
        $("#show_all_huan").text(calFun02()[2]);
        $("#show_lixi").text(calFun02()[8]);
        $("#show_yueshu").text(calFun02()[7]);
        $("#show_syhk").text(calFun02()[1]);
        $("#show_myhk").text(calFun02()[4]);
        $("#js_hkxe").text(calFun02()[3]);
      }
    }
  });

});


function returnLiLv() {
  var arr = new Array();
  var _year = $("#js_years").val();
  var zk = $("#lilv").val()
  //arr[0] 公积金利率
  //arr[1] 商贷利率
  if (_year == 1) {
    arr[0] = Math.floor((3.25 * zk) * 100) / 100;
    arr[1] = Math.floor((5.1 * zk) * 100) / 100;
    return arr;
  }
  else if (_year > 1 && _year <= 5) {
    arr[0] = Math.floor((3.25 * zk) * 100) / 100;
    arr[1] = Math.floor((5.5 * zk) * 100) / 100;
    return arr;
  }
  else {
    arr[0] = Math.floor((3.75 * zk) * 100) / 100;
    arr[1] = Math.floor((5.65 * zk) * 100) / 100;
    return arr;
  }
}

function calFun01() {//等额本息
  /**
   等额本息还款法:
   每月月供额={贷款本金×月利率×(1＋月利率)＾还款月数}÷{[(1＋月利率)＾还款月数]-1}
   总利息=还款月数×每月月供额-贷款本金

   ***/
  var h = $("#js_hklb_select").val();
  var m = $("#js_years").val() * 12;
  var arr = new Array();
  var mLi01 = returnLiLv()[1] * 0.001 * 10 / 12  //商业月利率
  var mLi02 = returnLiLv()[0] * 0.001 * 10 / 12  //公积金月利率

  if (h == 1 || h == 2) {
    var o = $("#js_dk_textInput .js_input");
    var z = parseFloat(o.val());

    if (!isNaN(z)) {
      var y_s = Math.round((z * mLi01 * Math.pow((1 + mLi01), m)) / (Math.pow((1 + mLi01), m) - 1));
      //商业月均还款
      var y_g = Math.round((z * mLi02 * Math.pow((1 + mLi02), m)) / (Math.pow((1 + mLi02), m) - 1));
      //公积金月均还款
      o.val(z);
      o.removeClass('e_red_input');
      arr[0] = z;//贷款总额
      arr[1] = returnLiLv()[0];//公积金利率
      arr[2] = returnLiLv()[1];//商业利率
      arr[3] = y_s * m;//商业还款总额
      arr[4] = y_g * m;//公积金还款总额
      arr[5] = m * y_s - z;//商业贷款支付利息款
      arr[6] = m * y_g - z;//公积金贷款支付利息款
      arr[7] = m;//贷款月数
      arr[8] = y_s;//商业月均还款
      arr[9] = y_g;//公积金月均还款
      return arr;
    }
    else {
      o.val('');
      o.addClass('e_red_input');
      return;
    }
  }
  else {
    var oSY = $("#js_input_SY");
    var oGJJ = $("#js_input_GJJ");
    var z_oSY = parseFloat(oSY.val());
    var z_oGJJ = parseFloat(oGJJ.val());
    if (!isNaN(z_oSY) && !isNaN(z_oGJJ)) {
      var y_s = Math.round((z_oSY * mLi01 * Math.pow((1 + mLi01), m)) / (Math.pow((1 + mLi01), m) - 1));
      //商业月均还款
      var y_g = Math.round((z_oGJJ * mLi02 * Math.pow((1 + mLi02), m)) / (Math.pow((1 + mLi02), m) - 1));
      //公积金月均还款
      oSY.val(z_oSY);
      oGJJ.val(z_oGJJ);
      oSY.removeClass('e_red_input');
      oGJJ.removeClass('e_red_input');
      arr[0] = z_oSY + z_oGJJ;//贷款总额
      arr[1] = returnLiLv()[0];//公积金利率
      arr[2] = returnLiLv()[1];//商业利率
      arr[3] = y_s * m;//商业还款总额
      arr[4] = y_g * m;//公积金还款总额
      arr[5] = m * y_s - z_oSY;//商业贷款支付利息款
      arr[6] = m * y_g - z_oGJJ;//公积金贷款支付利息款
      arr[7] = m;//贷款月数
      arr[8] = y_s;//商业月均还款
      arr[9] = y_g;//公积金月均还款
      arr[10] = arr[5] + arr[6];//总共利息
      arr[11] = arr[10] + arr[0];//总还款额
      arr[12] = arr[11] / arr[7];//月均还款
      return arr;
    }
  }
};
function calFun02() {//等额本金
  /**
   等额本金还款法:
   每月月供额=(贷款本金÷还款月数)+(贷款本金-已归还本金累计额)×月利率
   每月应还本金=贷款本金÷还款月数
   每月应还利息=剩余本金×月利率=(贷款本金-已归还本金累计额)×月利率
   每月月供递减额=每月应还本金×月利率=贷款本金÷还款月数×月利率
   总利息=还款月数×(总贷款额×月利率-月利率×(总贷款额÷还款月数)*(还款月数-1)÷2+总贷款额÷还款月数)
   **/
  var h = $("#js_hklb_select").val();
  var m = $("#js_years").val() * 12;
  var arr = new Array();
  var mLi01 = returnLiLv()[1] * 0.001 * 10 / 12  //商业月利率
  var mLi02 = returnLiLv()[0] * 0.001 * 10 / 12  //公积金月利率

  if (h == 1 || h == 2) {
    var o = $("#js_dk_textInput .js_input");
    var z = parseFloat(o.val());

    if (!isNaN(z)) {
      arr[0] = z;
      var b_s = Math.round(z / m);
      //本金月均还款
      var i;
      var m_str = '';
      var arr_str = new Array();
      if (h == 1) {
        arr[2] = 0;//还款总额
        for (i = 1; i <= m; i++) {
          arr_str[i] = b_s + Math.round((z - b_s * (i - 1)) * mLi01);
          m_str += '第' + i + '期：需还款 ' + arr_str[i] + '元；\n';
          arr[1] = arr_str[1];
          arr[2] = arr[2] + arr_str[i];
        }
        arr[4] = arr_str[m];//末期须还
      } else {
        arr[2] = 0;//还款总额
        for (i = 1; i <= m; i++) {
          arr_str[i] = b_s + Math.round((z - b_s * (i - 1)) * mLi02);
          m_str += '第' + i + '期：需还款 ' + arr_str[i] + '元；\n';
          arr[1] = arr_str[1];
          arr[2] = arr[2] + arr_str[i];
        }
        arr[4] = arr_str[m];//末期须还
      }
      arr[3] = m_str;

      o.val(z);
      o.removeClass('e_red_input');
      arr[5] = returnLiLv()[0];//公积金利率
      arr[6] = returnLiLv()[1];//商业利率
      arr[7] = m;//贷款月数
      arr[8] = arr[2] - arr[0];
      return arr;
    }
    else {
      o.val('');
      o.addClass('e_red_input');
      return;
    }
  } else {
    var oSY = $("#js_input_SY");
    var oGJJ = $("#js_input_GJJ");
    var z_oSY = parseFloat(oSY.val());
    var z_oGJJ = parseFloat(oGJJ.val());
    if (!isNaN(z_oSY) && !isNaN(z_oGJJ)) {
      arr[0] = z_oSY + z_oGJJ;
      var bsy_s = Math.round(z_oSY / m);
      var bgjj_s = Math.round(z_oGJJ / m);
      //本金月均还款
      var i;
      var m_str = '';
      var arr_str = new Array();
      arr[2] = 0;//还款总额
      for (i = 1; i <= m; i++) {
        arr_str[i] = bsy_s + Math.round((z_oSY - bsy_s * (i - 1)) * mLi01) + bgjj_s + Math.round((z_oGJJ - bgjj_s * (i - 1)) * mLi02);
        m_str += '第' + i + '期：需还款 ' + arr_str[i] + '元；\n';
        arr[1] = arr_str[1];
        arr[2] = arr[2] + arr_str[i];
      }
      arr[4] = arr_str[m];//末期须还
      arr[3] = m_str;
      arr[5] = returnLiLv()[0];//公积金利率
      arr[6] = returnLiLv()[1];//商业利率
      arr[7] = m;//贷款月数
      arr[8] = arr[2] - arr[0];
      return arr;
    }
  }
}




