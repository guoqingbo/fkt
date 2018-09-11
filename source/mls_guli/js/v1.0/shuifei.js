function check() {
  var resultform = document.getElementById('resultform');
  var housekind = resultform.housekind.value;
  //var district = resultform.district.value;
  var averprice = resultform.averprice.value;
  var buildarea = resultform.buildarea.value;
  var transferyear = resultform.transferyear.value;
  var firstbuy = resultform.firstbuy.value;
  //var banlance = resultform.banlance.value;
  var banlance = '';
  var housetype;
  var ghfee_rate;
  var yyfee_rate;
  var zzfee_rate; //土地增值税率
  var qfee_rate; //契税
  var lab_s1;
  var lab_s2;//营业税
  var lab_s3;
  var lab_s4;
  var lab_b1;
  var lab_b2;
  var lab_b3;
  var lab_b4;
  var stop = 0;

  var lab_grsds = document.getElementById('lab_grsds').value;
  lab_grsds = lab_grsds == '' ? '全额1%' : lab_grsds;

  if (averprice == '') {
    alert("请输入单价");
    resultform.averprice.focus();
    return false;
  }
  if (buildarea == '') {
    alert("请输入面积");
    resultform.buildarea.focus();
    return false;
  }

  housetype = housekind == 2 ? "非住宅" : "普通住房";

  if (housetype == "普通住房") {
    if (buildarea >= 144) {
      housetype = "非普通住房";
    }
    /*
     if(district==1) {
     if(averprice>=9900) housetype="非普通住房";
     }
     if(district==2) {
     if(averprice>=6000) housetype="非普通住房";
     }
     if(district==3) {
     if(averprice>=4900) housetype="非普通住房";
     }
     */
  }

  resultform.totalprice.value = (averprice * buildarea) / 10000;
  resultform.housetype.value = housetype;


  if (housetype == '普通住房') {
    ghfee_rate = 3;
    //契税
    if (firstbuy == 1) {
      if (buildarea <= 90) {
        qfee_rate = 0.01;
      } else {
        qfee_rate = 0.015;
      }
    } else {
      if (buildarea <= 90) {
        qfee_rate = 0.01;
      } else {
        qfee_rate = 0.02;
      }
    }
    qf = qfee_rate * 100;
    djfee_rate = housekind == 3 ? 40 : 80;
    gbfee_rate = housekind == 3 ? 18 : 20;
    lab_s1 = housekind == 3 ? "(房改房为100元/套)" : "(3元/平方米)";
    lab_s4 = "(全额1%)";
    lab_b1 = housekind == 3 ? "(房改房为100元/套)" : "(3元/平方米)";
    //lab_b2=buildarea<=90? "(成交价1%)":"(成交价1%,政府补贴0.5%)";
    lab_b2 = "(成交价" + qf + "%)";
    lab_b3 = "(" + djfee_rate + "元/人)";
    lab_b4 = "(" + gbfee_rate + "元)";

    switch (transferyear) {
      case "1":
        /*
         if(banlance==''){
         alert("不输入转让差额，卖方营业税,个人所得税无法计算");
         stop=1;
         }
         */
        yyfee_rate = 0.0555;
        zzfee_rate = 0.05;
        // lab_s2="(差额5.55%)";
        lab_s2 = "(全额5.55%)";
        lab_s3 = "(全额5%)";
        lab_s4 = "(" + lab_grsds + ")";
        break;

      case "2":
        yyfee_rate = 0;
        zzfee_rate = 0;
        lab_s2 = "(暂不征收)";
        lab_s3 = "(暂不征收)";
        lab_s4 = "(" + lab_grsds + ")";
        break;

    }
  }

  if (housetype == '非普通住房') {
    ghfee_rate = 3;
    if (firstbuy == 1) {
      qfee_rate = 0.015;
      lab_b2 = "(成交价1.5%)";
    } else {
      qfee_rate = 0.02;
      lab_b2 = "(成交价2%)";
    }
    djfee_rate = housekind == 3 ? 40 : 80;
    gbfee_rate = housekind == 3 ? 18 : 20;
    lab_s1 = housekind == 3 ? "(房改房为100元/套)" : "(3元/平方米)";
    lab_s4 = "(" + lab_grsds + ")";
    lab_b1 = housekind == 3 ? "(房改房为100元/套)" : "(3元/平方米)";
    lab_b3 = "(" + djfee_rate + "元/人)";
    lab_b4 = "(" + gbfee_rate + "元)";

    switch (transferyear) {
      case "1":
        yyfee_rate = 0.0555;
        zzfee_rate = 0.05;
        lab_s2 = "(全额5.55%)";
        lab_s3 = "(全额5%)";
        break;

      case "2":
        /*if(banlance==''){
         alert("不输入转让差额，卖方营业税无法计算");
         stop=1;
         }*/
        yyfee_rate = 0.0555;
        zzfee_rate = 0;
        lab_s2 = "(差额5.55%)";
        lab_s3 = "(暂不征收)";
        lab_s4 = "(" + lab_grsds + ")";
        break;

    }
  }

  if (housetype == '非住宅') {
    ghfee_rate = 5;
    qfee_rate = 0.03;
    djfee_rate = 30;
    gbfee_rate = 90;
    lab_s1 = "(5元/平方米)";
    lab_s4 = "(差额20%)";
    lab_b1 = "(5元/平方米)";
    lab_b2 = "(成交价3%)";
    lab_b3 = "(20元+10元/套/人)";
    lab_b4 = "(" + gbfee_rate + "元)";

    /*if(banlance==''){
     alert("不输入转让差额，卖方个人所得税无法计算");
     stop=1;
     }*/

    switch (transferyear) {
      case "1":
        yyfee_rate = 0.0555;
        zzfee_rate = 0.05;
        lab_s2 = "(全额5.55%)";
        lab_s3 = "(全额5%)";
        break;

      case "2":
        yyfee_rate = 0.0555;
        zzfee_rate = 0;
        lab_s2 = "(全额5.55%)";
        break;

    }
  }

  if (stop != 1) {
    resultform.sell_ghfee.value = housekind == 3 ? 100 : (ghfee_rate * buildarea).toFixed(2);
    document.getElementById('lab_s1').innerHTML = lab_s1;
    //营业税赋值（营改增，营业税全部为0）
    resultform.sell_yyfee.value = 0.00;
    lab_s2 = '(暂不征收)';
    document.getElementById('lab_s2').innerHTML = lab_s2;
    resultform.sell_zzfee.value = ((averprice * buildarea) * zzfee_rate).toFixed(2);
    document.getElementById('lab_s3').innerHTML = lab_s3;


    var grsds = document.getElementById('grsds').value;
    grsds = grsds != '' ? grsds : 0.01;
    grsds = parseFloat(grsds);
    if (housetype == '普通住房') resultform.sell_sdfee.value = transferyear == 1 ? ((averprice * buildarea) * grsds).toFixed(2) : ((averprice * buildarea) * 0.01).toFixed(2);
    if (housetype == '非普通住房') resultform.sell_sdfee.value = ((averprice * buildarea) * grsds).toFixed(2);
    if (housetype == '非住宅') resultform.sell_sdfee.value = ((averprice * buildarea) * grsds).toFixed(2);

    document.getElementById('lab_s4').innerHTML = lab_s4;
    resultform.sell_sum.value = (parseFloat(resultform.sell_ghfee.value) + parseFloat(resultform.sell_yyfee.value) + parseFloat(resultform.sell_zzfee.value) + parseFloat(resultform.sell_sdfee.value)).toFixed(2);

    resultform.buy_ghfee.value = housekind == 3 ? 100 : (ghfee_rate * buildarea).toFixed(2);
    document.getElementById('lab_b1').innerHTML = lab_b1;
    resultform.buy_qfee.value = ((averprice * buildarea) * qfee_rate).toFixed(2);
    document.getElementById('lab_b2').innerHTML = lab_b2;
    resultform.buy_djfee.value = djfee_rate.toFixed(2);
    document.getElementById('lab_b3').innerHTML = lab_b3;
    resultform.buy_gbfee.value = gbfee_rate.toFixed(2);
    document.getElementById('lab_b4').innerHTML = lab_b4;
    resultform.buy_sum.value = (parseFloat(resultform.buy_ghfee.value) + parseFloat(resultform.buy_qfee.value) + 100).toFixed(2);

    /*document.all.houseinfo.style.display="";
     document.all.sellinfo.style.display="";
     document.all.buyinfo.style.display="";
     document.all.remark.style.display="";*/
  }

  return false;

}
