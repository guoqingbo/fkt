function exc_zuhe(fmobj, v) {
  //var fmobj=document.calc1;
  if (fmobj.name == "calc1") {
    if (v == 3) {
      document.getElementById("calc1_jsfs").style.display = 'none';
      document.getElementById("calc1_years").style.display = 'none';

      document.getElementById("calc1_zuhe").style.display = 'block';
      fmobj.jisuan_radio[1].checked = true;
      exc_js(fmobj, 2);
    } else {
      document.getElementById("calc1_jsfs").style.display = 'block';
      document.getElementById("calc1_years").style.display = 'block';

      document.getElementById("calc1_zuhe").style.display = 'none';
    }
  } else {
    if (v == 3) {
      document.getElementById("calc2_jsfs").style.display = 'none';
      document.getElementById("calc2_years").style.display = 'none';

      document.getElementById("calc2_zuhe").style.display = 'block';
      fmobj.jisuan_radio[1].checked = true;
      exc_js(fmobj, 2);
    } else {
      document.getElementById("calc2_jsfs").style.display = 'block';
      document.getElementById("calc2_years").style.display = 'block';

      document.getElementById("calc2_zuhe").style.display = 'none';
    }
  }
}
function exc_js(fmobj, v) {
  //var fmobj=document.calc1;
  if (fmobj.name == "calc1") {
    if (v == 1) {
      document.getElementById("calc1_js_div1").style.display = 'block';
      document.getElementById("calc1_js_div2").style.display = 'block';
      document.getElementById("calc1_zuhe").style.display = 'none';
      fmobj.type.value = 1;
    } else {
      document.getElementById("calc1_js_div1").style.display = 'none';
      document.getElementById("calc1_js_div2").style.display = 'block';
    }
  } else {
    if (v == 1) {
      document.getElementById("calc2_js_div1").style.display = 'block';
      document.getElementById("calc2_js_div2").style.display = 'block';
      document.getElementById("calc2_zuhe").style.display = 'none';
      fmobj.type.value = 1;
    } else {
      document.getElementById("calc2_js_div1").style.display = 'none';
      document.getElementById("calc2_js_div2").style.display = 'block';
    }
  }
}
function formReset(fmobj) {
  //var fmobj=document.calc1;
  /*if (fmobj.name=="calc1"){
   document.getElementById("calc_js_div1").style.display='block';
   document.getElementById("calc1_js_div2").style.display='none';
   document.getElementById("calc1_zuhe").style.display='none';
   document.getElementById("calc1_benxi").style.display='none';

   }else{
   document.getElementById("calc2_js_div1").style.display='block';
   document.getElementById("calc2_js_div2").style.display='none';
   document.getElementById("calc2_zuhe").style.display='none';
   document.getElementById("calc2_benjin").style.display='none';
   }*/

  fmobj.reset();
}

//显示右边的比较div
function showRightDiv(fmobj) {
  if (ext_total(fmobj) == false) {
    return;
  }
  //alert(document.calc1.month_money2.value);
  var a = window.open('', 'calc_win', 'status=yes,scrollbars=yes,resizable=yes,width=550,height=500,left=0,top=0')//790*520
  if (fmobj.name == "calc1") {
    document.calc1.target = "calc_win";
    document.calc1.submit();
  } else {
    document.calc2.target = "calc_win";
    document.calc2.submit();
  }

}


//验证是否为数字
function reg_Num(str) {
  if (str.length == 0) {
    return false;
  }
  var Letters = "1234567890.";

  for (i = 0; i < str.length; i++) {
    var CheckChar = str.charAt(i);
    if (Letters.indexOf(CheckChar) == -1) {
      return false;
    }
  }
  return true;
}


//得到利率
function getlilv(lilv_class, type, years) {
  var lilv_class = parseInt(lilv_class);//新旧利率。1:旧利率，2:新利率

  if (lilv_class == 2) {
    //2005年	1月的新利率
    if (years <= 5) {
      if (type == 2) {
        return 0.0378;//公积金 1～5年 3.78%
      } else {
        return 0.0495;//商贷 1～5年 4.95%
      }
    } else {
      if (type == 2) {
        return 0.0423//公积金 5-30年 4.23%
      } else {
        return 0.0531//商贷 5-30年 5.31%
      }
    }
  } else if (lilv_class == 3) {
    //2006年	1月的新利率下限
    if (years <= 5) {
      if (type == 2) {
        return 0.0396//公积金 1～5年 3.96%
      } else {
        return 0.0495//商贷 1～5年 4.95%
      }
    } else {
      if (type == 2) {
        return 0.0441//公积金 5-30年 4.41%
      } else {
        return 0.0551//商贷 5-30年 5.51%
      }
    }
  } else if (lilv_class == 4) {
    //2006年	1月的新利率上限
    if (years <= 5) {
      if (type == 2) {
        return 0.0396//公积金 1～5年 3.96%
      } else {
        return 0.0495//商贷 1～5年 4.95%
      }
    } else {
      if (type == 2) {
        return 0.0441//公积金 5-30年 4.41%
      } else {
        return 0.0612//商贷 5-30年 6.12%
      }
    }
  } else if (lilv_class == 5) {
    //2006年	4月的新利率上限
    if (years <= 5) {
      if (type == 2) {
        return 0.0414//公积金 1～5年 3.96%
      } else {
        return 0.0612//商贷 1～5年 4.95%
      }
    } else {
      if (type == 2) {
        return 0.0459//公积金 5-30年 4.41%
      } else {
        return 0.0639//商贷 5-30年 6.12%
      }
    }
  } else if (lilv_class == 6) {
    //2006年	8月的新利率上限
    if (years <= 5) {
      if (type == 2) {
        return 0.0414//公积金 1～5年 3.96%
      } else {
        return 0.0648//商贷 1～5年 4.95%
      }
    } else {
      if (type == 2) {
        return 0.0459//公积金 5-30年 4.41%
      } else {
        return 0.0684//商贷 5-30年 6.12%
      }
    }
  } else if (lilv_class == 7) {
    //2007年	3月的新利率下限
    if (years <= 5) {
      if (type == 2) {
        return 0.0432//公积金 1～5年 3.96%
      } else {
        return 0.057375//商贷 1～5年 4.95%
      }
    } else {
      if (type == 2) {
        return 0.0477//公积金 5-30年 4.41%
      } else {
        return 0.060435//商贷 5-30年 6.12%
      }
    }
  } else if (lilv_class == 8) {
    //2007年	3月的新基准利率
    if (years <= 5) {
      if (type == 2) {
        return 0.0432//公积金 1～5年 3.96%
      } else {
        return 0.0675//商贷 1～5年 4.95%
      }
    } else {
      if (type == 2) {
        return 0.0477//公积金 5-30年 4.41%
      } else {
        return 0.0711//商贷 5-30年 6.12%
      }
    }
  } else if (lilv_class == 9) {
    //2007年	5月的新利率下限
    if (years <= 5) {
      if (type == 2) {
        return 0.0441//公积金 1～5年
      } else {
        return 0.058905//商贷 1～5年
      }
    } else {
      if (type == 2) {
        return 0.0486//公积金 5-30年
      } else {
        return 0.0612//商贷 5-30年
      }
    }
  } else if (lilv_class == 10) {
    //2007年	5月的新基准利率
    if (years <= 5) {
      if (type == 2) {
        return 0.0441//公积金 1～5年
      } else {
        return 0.0693//商贷 1～5年
      }
    } else {
      if (type == 2) {
        return 0.0486//公积金 5-30年
      } else {
        return 0.0720//商贷 5-30年
      }
    }

  } else if (lilv_class == 11) {
    //2007年	7月的新基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0450 //公积金 1年
      } else {
        return 0.06840 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0450 //公积金 2～3年
      } else {
        return 0.07020 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0450 //公积金 4～5年
      } else {
        return 0.0720//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0495 //公积金5年以上
      } else {
        return 0.0738 //商贷5年以上
      }
    }

  } else if (lilv_class == 12) {
    //2007年	7月的新基准利率下限
    if (years <= 1) {
      if (type == 2) {
        return 0.0450 //公积金 1年
      } else {
        return 0.05814 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0450 //公积金 2～3年
      } else {
        return 0.05967 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0450 //公积金 4～5年
      } else {
        return 0.06120//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0495 //公积金5年以上
      } else {
        return 0.06273 //商贷5年以上
      }
    }

  } else if (lilv_class == 13) {
    //2007年	8月的新基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0459 //公积金 1年
      } else {
        return 0.0702 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0459 //公积金 2～3年
      } else {
        return 0.0720 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0459 //公积金 4～5年
      } else {
        return 0.0738//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0504 //公积金5年以上
      } else {
        return 0.0756 //商贷5年以上
      }
    }
  } else if (lilv_class == 14) {
    //2007年	8月的利率下限
    if (years <= 1) {
      if (type == 2) {
        return 0.0459 //公积金 1年
      } else {
        return 0.05967 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0459 //公积金 2～3年
      } else {
        return 0.0612 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0459 //公积金 4～5年
      } else {
        return 0.06273//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0504 //公积金5年以上
      } else {
        return 0.06426 //商贷5年以上
      }
    }

  } else if (lilv_class == 15) {
    //2007年	9月的新基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0477 //公积金 1年
      } else {
        return 0.0729 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0477 //公积金 2～3年
      } else {
        return 0.0747 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0477 //公积金 4～5年
      } else {
        return 0.0765//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0522 //公积金5年以上
      } else {
        return 0.0783 //商贷5年以上
      }
    }

  } else if (lilv_class == 16) {
    //2007年	9月的利率下限
    if (years <= 1) {
      if (type == 2) {
        return 0.0477 //公积金 1年
      } else {
        return 0.061965 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0477 //公积金 2～3年
      } else {
        return 0.063495 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0477 //公积金 4～5年
      } else {
        return 0.065025//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0522 //公积金5年以上
      } else {
        return 0.066555 //商贷5年以上
      }
    }

  } else if (lilv_class == 17) {
    //2007年	12月的新基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0477 //公积金 1年
      } else {
        return 0.0747 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0477 //公积金 2～3年
      } else {
        return 0.0756 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0477 //公积金 4～5年
      } else {
        return 0.0774//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0522 //公积金5年以上
      } else {
        return 0.0783 //商贷5年以上
      }
    }

  } else if (lilv_class == 18) {
    //2007年	12月的利率下限
    if (years <= 1) {
      if (type == 2) {
        return 0.0477 //公积金 1年
      } else {
        return 0.063495 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0477 //公积金 2～3年
      } else {
        return 0.064515 //商贷 2～3年 0.0756
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0477 //公积金 4～5年
      } else {
        return 0.06579//商贷 4～5年 0.0774
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0522 //公积金5年以上
      } else {
        return 0.066555 //商贷5年以上 0.0783
      }
    }

  } else if (lilv_class == 19) {
    //2007年12月的利率上限
    if (years <= 1) {
      if (type == 2) {
        return 0.0477 //公积金 1年
      } else {
        return 0.0698445 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0477 //公积金 2～3年
      } else {
        return 0.0709665 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0477 //公积金 4～5年
      } else {
        return 0.072369//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0522 //公积金5年以上
      } else {
        return 0.0732105 //商贷5年以上
      }
    }

  } else if (lilv_class == 20) {
    //2008年	9月的新基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0459 //公积金 1年
      } else {
        return 0.0720 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0459 //公积金 2～3年
      } else {
        return 0.0729 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0459 //公积金 4～5年
      } else {
        return 0.0756//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0513 //公积金5年以上
      } else {
        return 0.0774 //商贷5年以上
      }
    }

  } else if (lilv_class == 21) {
    //2008年	9月的利率下限
    if (years <= 1) {
      if (type == 2) {
        return 0.0459 //公积金 1年
      } else {
        return 0.0612 //商贷 1年 0.072
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0459 //公积金 2～3年
      } else {
        return 0.061965 //商贷 2～3年 0.0729
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0459 //公积金 4～5年
      } else {
        return 0.06426//商贷 4～5年 0.0756
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0513 //公积金5年以上
      } else {
        return 0.06579 //商贷5年以上 0.0774
      }
    }

  } else if (lilv_class == 22) {
    //2008年9月的利率上限
    if (years <= 1) {
      if (type == 2) {
        return 0.0459 //公积金 1年
      } else {
        return 0.0792 //商贷 1年 0.0720
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0459 //公积金 2～3年
      } else {
        return 0.08019 //商贷 2～3年 0.0729
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0459 //公积金 4～5年
      } else {
        return 0.08316//商贷 4～5年 0.0756
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0513 //公积金5年以上
      } else {
        return 0.08514 //商贷5年以上 0.0774
      }
    }

  } else if (lilv_class == 23) {
    //2008年	10月的新基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0432 //公积金 1年
      } else {
        return 0.0693 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0432 //公积金 2～3年
      } else {
        return 0.0702 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0432 //公积金 4～5年
      } else {
        return 0.0729//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0486 //公积金5年以上
      } else {
        return 0.0747 //商贷5年以上
      }
    }

  } else if (lilv_class == 24) {
    //2008年	10月的利率下限
    if (years <= 1) {
      if (type == 2) {
        return 0.0432 //公积金 1年
      } else {
        return 0.058905 //商贷 1年 0.0693
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0432 //公积金 2～3年
      } else {
        return 0.05967 //商贷 2～3年 0.0702
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0432 //公积金 4～5年
      } else {
        return 0.061965//商贷 4～5年 0.0729
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0486 //公积金5年以上
      } else {
        return 0.063495 //商贷5年以上 0.0747
      }
    }

  } else if (lilv_class == 25) {
    //2008年10月的利率上限
    if (years <= 1) {
      if (type == 2) {
        return 0.0432 //公积金 1年
      } else {
        return 0.07623 //商贷 1年 0.0693
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0432 //公积金 2～3年
      } else {
        return 0.07722 //商贷 2～3年 0.0702
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0432 //公积金 4～5年
      } else {
        return 0.08019//商贷 4～5年 0.0729
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0486 //公积金5年以上
      } else {
        return 0.08217 //商贷5年以上 0.0747
      }
    }

  } else if (lilv_class == 26) {
    //2008年	10月27的新基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0405 //公积金 1年
      } else {
        return 0.0693 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0405 //公积金 2～3年
      } else {
        return 0.0702 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0405 //公积金 4～5年
      } else {
        return 0.0729//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0459 //公积金5年以上
      } else {
        return 0.0747 //商贷5年以上
      }
    }

  } else if (lilv_class == 27) {
    //2008年	10月27的利率下限
    if (years <= 1) {
      if (type == 2) {
        return 0.0405 //公积金 1年
      } else {
        return 0.04851 //商贷 1年 0.0693
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0405 //公积金 2～3年
      } else {
        return 0.04914 //商贷 2～3年 0.0702
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0405 //公积金 4～5年
      } else {
        return 0.05103//商贷 4～5年 0.0729
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0459 //公积金5年以上
      } else {
        return 0.05229 //商贷5年以上 0.0747
      }
    }

  } else if (lilv_class == 28) {
    //2008年	10月30的新基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0405 //公积金 1年
      } else {
        return 0.0666 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0405 //公积金 2～3年
      } else {
        return 0.0675 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0405 //公积金 4～5年
      } else {
        return 0.0702//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0459 //公积金5年以上
      } else {
        return 0.072 //商贷5年以上
      }
    }

  } else if (lilv_class == 29) {
    //2008年	10月30的利率下限30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0405 //公积金 1年
      } else {
        return 0.04662 //商贷 1年 0.0666
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0405 //公积金 2～3年
      } else {
        return 0.04725 //商贷 2～3年 0.0675
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0405 //公积金 4～5年
      } else {
        return 0.04914//商贷 4～5年 0.0702
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0459 //公积金5年以上
      } else {
        return 0.0504 //商贷5年以上 0.072
      }
    }

  } else if (lilv_class == 30) {
    //2008年	10月30的利率下限15%
    if (years <= 1) {
      if (type == 2) {
        return 0.0405 //公积金 1年
      } else {
        return 0.05661 //商贷 1年 0.0666
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0405 //公积金 2～3年
      } else {
        return 0.057375 //商贷 2～3年 0.0675
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0405 //公积金 4～5年
      } else {
        return 0.05967//商贷 4～5年 0.0702
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0459 //公积金5年以上
      } else {
        return 0.0612 //商贷5年以上 0.072
      }
    }

  } else if (lilv_class == 31) {
    //2008年	11月27的新基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0351 //公积金 1年
      } else {
        return 0.0558 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0351 //公积金 2～3年
      } else {
        return 0.0567 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0351 //公积金 4～5年
      } else {
        return 0.0594//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0405 //公积金5年以上
      } else {
        return 0.0612 //商贷5年以上
      }
    }

  } else if (lilv_class == 32) {
    //2008年	11月27的利率下限30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0351 //公积金 1年
      } else {
        return 0.03906 //商贷 1年 0.0558
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0351 //公积金 2～3年
      } else {
        return 0.03969 //商贷 2～3年 0.0567
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0351 //公积金 4～5年
      } else {
        return 0.04158//商贷 4～5年 0.0594
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0405 //公积金5年以上
      } else {
        return 0.04284 //商贷5年以上 0.0612
      }
    }

  } else if (lilv_class == 33) {
    //2008年	11月27的利率下限15%
    if (years <= 1) {
      if (type == 2) {
        return 0.0351 //公积金 1年
      } else {
        return 0.04743 //商贷 1年 0.0558
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0351 //公积金 2～3年
      } else {
        return 0.048195 //商贷 2～3年 0.0567
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0351 //公积金 4～5年
      } else {
        return 0.05049//商贷 4～5年 0.0594
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0405 //公积金5年以上
      } else {
        return 0.05202 //商贷5年以上 0.0612
      }
    }

  } else if (lilv_class == 34) {
    //2008年	12月23的新基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0333 //公积金 1年
      } else {
        return 0.0531 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0333 //公积金 2～3年
      } else {
        return 0.0540 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0333 //公积金 4～5年
      } else {
        return 0.0576//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0387 //公积金5年以上
      } else {
        return 0.0594 //商贷5年以上
      }
    }

  } else if (lilv_class == 35) {
    //2008年	12月23的利率下限30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0333 //公积金 1年
      } else {
        return 0.03717 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0333 //公积金 2～3年
      } else {
        return 0.0378 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0333 //公积金 4～5年
      } else {
        return 0.04032//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0387 //公积金5年以上
      } else {
        return 0.04158 //商贷5年以上 0.0594
      }
    }

  } else if (lilv_class == 36) {
    //2008年	12月23的利率下限15%
    if (years <= 1) {
      if (type == 2) {
        return 0.0333 //公积金 1年
      } else {
        return 0.045135 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0333 //公积金 2～3年
      } else {
        return 0.0459 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0333 //公积金 4～5年
      } else {
        return 0.04896//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0387 //公积金5年以上
      } else {
        return 0.05049 //商贷5年以上 0.0594
      }
    }

  } else if (lilv_class == 37) {
    //2008年	12月23的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0333 //公积金 1年
      } else {
        return 0.05841 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0333 //公积金 2～3年
      } else {
        return 0.0594 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0333 //公积金 4～5年
      } else {
        return 0.06336//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0387 //公积金5年以上
      } else {
        return 0.06534 //商贷5年以上 0.0594
      }
    }


  } else if (lilv_class == 40) {
    //2010年	10月20的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0350 //公积金 1年
      } else {
        return 0.06116 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0350 //公积金 2～3年
      } else {
        return 0.0616 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0350 //公积金 4～5年
      } else {
        return 0.06556//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0405 //公积金5年以上
      } else {
        return 0.06754 //商贷5年以上 0.0594
      }
    }

  } else if (lilv_class == 39) {
    //2010年	10月20的利率下限15%
    if (years <= 1) {
      if (type == 2) {
        return 0.0350 //公积金 1年
      } else {
        return 0.04726 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0350 //公积金 2～3年
      } else {
        return 0.0476 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0350 //公积金 4～5年
      } else {
        return 0.05066//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0405 //公积金5年以上
      } else {
        return 0.05219 //商贷5年以上 0.0594
      }
    }

  } else if (lilv_class == 38) {
    //2010年	10月20的利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0350 //公积金 1年
      } else {
        return 0.0556 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0350 //公积金 2～3年
      } else {
        return 0.0560 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0350 //公积金 4～5年
      } else {
        return 0.0596//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0405 //公积金5年以上
      } else {
        return 0.0614 //商贷5年以上 0.0594
      }
    }

  } else if (lilv_class == 41) {
    //2010年	10月20的利率下限30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0350 //公积金 1年
      } else {
        return 0.03892 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0350 //公积金 2～3年
      } else {
        return 0.0392 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0350 //公积金 4～5年
      } else {
        return 0.04172//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0405 //公积金5年以上
      } else {
        return 0.04298 //商贷5年以上 0.0594
      }
    }

  } else if (lilv_class == 42) {
    //2010年	12月26的利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0375 //公积金 1年
      } else {
        return 0.0581 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0375 //公积金 2～3年
      } else {
        return 0.0585 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0375 //公积金 4～5年
      } else {
        return 0.0622//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0430 //公积金5年以上
      } else {
        return 0.0640 //商贷5年以上 0.0594
      }
    }

  } else if (lilv_class == 43) {
    //2010年	12月26的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0375 //公积金 1年
      } else {
        return 0.06391 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0375 //公积金 2～3年
      } else {
        return 0.06435 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0375 //公积金 4～5年
      } else {
        return 0.06842//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0430 //公积金5年以上
      } else {
        return 0.0704 //商贷5年以上 0.0594
      }
    }

  } else if (lilv_class == 44) {
    //2010年	12月26的利率下限15%
    if (years <= 1) {
      if (type == 2) {
        return 0.0375 //公积金 1年
      } else {
        return 0.049385 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0375 //公积金 2～3年
      } else {
        return 0.049725 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0375 //公积金 4～5年
      } else {
        return 0.05287//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0430 //公积金5年以上
      } else {
        return 0.0544 //商贷5年以上 0.0594
      }
    }

  } else if (lilv_class == 45) {
    //2010年	12月26的利率下限30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0375 //公积金 1年
      } else {
        return 0.04067 //商贷 1年 0.0531
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0375 //公积金 2～3年
      } else {
        return 0.04095 //商贷 2～3年 0.0540
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0375 //公积金 4～5年
      } else {
        return 0.04354//商贷 4～5年 0.0576
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0430 //公积金5年以上
      } else {
        return 0.0448 //商贷5年以上 0.0594
      }
    }

  } else if (lilv_class == 46) {
    //2011年	2月9的利率
    if (years <= 1) {
      if (type == 2) {
        return 0.04 //公积金 1年
      } else {
        return 0.0606 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.04 //公积金 2～3年
      } else {
        return 0.0610 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.04 //公积金 4～5年
      } else {
        return 0.0645//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0450 //公积金5年以上
      } else {
        return 0.0660 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 47) {
    //2011年	2月9的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.04 //公积金 1年
      } else {
        return 0.06666 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.04 //公积金 2～3年
      } else {
        return 0.0671 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.04 //公积金 4～5年
      } else {
        return 0.07095//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0450 //公积金5年以上
      } else {
        return 0.0726 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 48) {
    //2011年	2月9的利率下限15%
    if (years <= 1) {
      if (type == 2) {
        return 0.04 //公积金 1年
      } else {
        return 0.05151 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.04 //公积金 2～3年
      } else {
        return 0.05185 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.04 //公积金 4～5年
      } else {
        return 0.054825//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0450 //公积金5年以上
      } else {
        return 0.0561 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 49) {
    //2011年	2月9的利率下限30%
    if (years <= 1) {
      if (type == 2) {
        return 0.04 //公积金 1年
      } else {
        return 0.04242 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.04 //公积金 2～3年
      } else {
        return 0.0427 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.04 //公积金 4～5年
      } else {
        return 0.04515//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0450 //公积金5年以上
      } else {
        return 0.0462 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 50) {
    //2011年	4月6的利率下限15%
    if (years <= 1) {
      if (type == 2) {
        return 0.042 //公积金 1年
      } else {
        return 0.0631 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.042 //公积金 2～3年
      } else {
        return 0.064 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.042 //公积金 4～5年
      } else {
        return 0.0665//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.047 //公积金5年以上
      } else {
        return 0.0680 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 51) {
    //2011年	4月6的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.042 //公积金 1年
      } else {
        return 0.06941 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.042 //公积金 2～3年
      } else {
        return 0.0704 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.042 //公积金 4～5年
      } else {
        return 0.07315//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.047 //公积金5年以上
      } else {
        return 0.0748 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 52) {
    //2011年	4月6的利率下限15%
    if (years <= 1) {
      if (type == 2) {
        return 0.042 //公积金 1年
      } else {
        return 0.053635 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.042 //公积金 2～3年
      } else {
        return 0.0544 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.042 //公积金 4～5年
      } else {
        return 0.056525//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.047 //公积金5年以上
      } else {
        return 0.0578 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 53) {
    //2011年	4月6的利率下限30%
    if (years <= 1) {
      if (type == 2) {
        return 0.042 //公积金 1年
      } else {
        return 0.04417 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.042 //公积金 2～3年
      } else {
        return 0.0448 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.042 //公积金 4～5年
      } else {
        return 0.04655//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.047 //公积金5年以上
      } else {
        return 0.0476 //商贷5年以上 0.0660
      }
    }////////////////////////////////////////////////////////////////////////////////////////////////

  } else if (lilv_class == 54) {
    //2011年	7月7的利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0445 //公积金 1年
      } else {
        return 0.0656 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0445 //公积金 2～3年
      } else {
        return 0.0665 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0445 //公积金 4～5年
      } else {
        return 0.069//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.049 //公积金5年以上
      } else {
        return 0.0705 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 55) {
    //2011年	7月7的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0445 //公积金 1年
      } else {
        return 0.07216 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0445 //公积金 2～3年
      } else {
        return 0.07315 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0445 //公积金 4～5年
      } else {
        return 0.0759//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.049 //公积金5年以上
      } else {
        return 0.07755 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 56) {
    //2011年	7月7的利率下限30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0445 //公积金 1年
      } else {
        return 0.04592 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0445 //公积金 2～3年
      } else {
        return 0.04655 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0445 //公积金 4～5年
      } else {
        return 0.0483//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.049 //公积金5年以上
      } else {
        return 0.04935 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 57) {
    //2011年	7月7的利率下限15%
    if (years <= 1) {
      if (type == 2) {
        return 0.0445 //公积金 1年
      } else {
        return 0.05576 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0445 //公积金 2～3年
      } else {
        return 0.056525 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0445 //公积金 4～5年
      } else {
        return 0.05865//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.049 //公积金5年以上
      } else {
        return 0.059925 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 58) {
    //2011年	10.24的利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0445 //公积金 1年
      } else {
        return 0.0656 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0445 //公积金 2～3年
      } else {
        return 0.0665 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0445 //公积金 4～5年
      } else {
        return 0.069//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.049 //公积金5年以上
      } else {
        return 0.0705 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 59) {
    //2011年	10.24的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0445 //公积金 1年
      } else {
        return 0.07216 //商贷 1年 0.0606
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0445 //公积金 2～3年
      } else {
        return 0.07315 //商贷 2～3年 0.0610
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0445 //公积金 4～5年
      } else {
        return 0.0759//商贷 4～5年 0.0645
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0539 //公积金5年以上上浮10%
      } else {
        return 0.07755 //商贷5年以上 0.0660
      }
    }

  } else if (lilv_class == 60) {
    //2012年	6.8的利率
    if (years <= 1) {
      if (type == 2) {
        return 0.042 //公积金 1年
      } else {
        return 0.0631 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.042 //公积金 2～3年
      } else {
        return 0.064 //商贷 2～3年 0.064
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.042 //公积金 4～5年
      } else {
        return 0.0665//商贷 4～5年 0.0665
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.047 //公积金5年以上
      } else {
        return 0.068 //商贷5年以上 0.068
      }
    }

  } else if (lilv_class == 60) {
    //2012年	6.8的利率
    if (years <= 1) {
      if (type == 2) {
        return 0.042 //公积金 1年
      } else {
        return 0.0631 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.042 //公积金 2～3年
      } else {
        return 0.064 //商贷 2～3年 0.064
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.042 //公积金 4～5年
      } else {
        return 0.0665//商贷 4～5年 0.0665
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.047 //公积金5年以上
      } else {
        return 0.068 //商贷5年以上 0.068
      }
    }

  } else if (lilv_class == 61) {
    //2012年	6.8的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.042 * 1.1 //公积金 1年
      } else {
        return 0.0631 * 1.1 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.042 * 1.1 //公积金 2～3年
      } else {
        return 0.064 * 1.1 //商贷 2～3年 0.064
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.042 * 1.1 //公积金 4～5年
      } else {
        return 0.0665 * 1.1 //商贷 4～5年 0.0665
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.047 * 1.1 //公积金5年以上
      } else {
        return 0.068 * 1.1 //商贷5年以上 0.068
      }
    }

  } else if (lilv_class == 62) {
    //2012年	6.8的利率下浮15%
    if (years <= 1) {
      if (type == 2) {
        return 0.042 * 0.85 //公积金 1年
      } else {
        return 0.0631 * 0.85 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.042 * 0.85 //公积金 2～3年
      } else {
        return 0.064 * 0.85 //商贷 2～3年 0.064
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.042 * 0.85 //公积金 4～5年
      } else {
        return 0.0665 * 0.85 //商贷 4～5年 0.0665
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.047 * 0.85 //公积金5年以上
      } else {
        return 0.068 * 0.85 //商贷5年以上 0.068
      }
    }

  } else if (lilv_class == 63) {
    //2012年	6.8的利率下浮20%
    if (years <= 1) {
      if (type == 2) {
        return 0.042 * 0.8 //公积金 1年
      } else {
        return 0.0631 * 0.8 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.042 * 0.8 //公积金 2～3年
      } else {
        return 0.064 * 0.8 //商贷 2～3年 0.064
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.042 * 0.8 //公积金 4～5年
      } else {
        return 0.0665 * 0.8 //商贷 4～5年 0.0665
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.047 * 0.8 //公积金5年以上
      } else {
        return 0.068 * 0.8 //商贷5年以上 0.068
      }
    }

  } else if (lilv_class == 64) {
    //2012年	6.8的利率下浮30%
    if (years <= 1) {
      if (type == 2) {
        return 0.042 * 0.7 //公积金 1年
      } else {
        return 0.0631 * 0.7 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.042 * 0.7 //公积金 2～3年
      } else {
        return 0.064 * 0.7 //商贷 2～3年 0.064
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.042 * 0.7 //公积金 4～5年
      } else {
        return 0.0665 * 0.7 //商贷 4～5年 0.0665
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.047 * 0.7 //公积金5年以上
      } else {
        return 0.068 * 0.7 //商贷5年以上 0.068
      }
    }

  } else if (lilv_class == 65) {
    //2012年	6.8的利率
    if (years <= 1) {
      if (type == 2) {
        return 0.040 //公积金 1年
      } else {
        return 0.060 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.040 //公积金 2～3年
      } else {
        return 0.0615 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.040 //公积金 4～5年
      } else {
        return 0.0640//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.045 //公积金5年以上
      } else {
        return 0.0655 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 66) {
    //2012年	6.8的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.040 * 1.1 //公积金 1年
      } else {
        return 0.060 * 1.1 //商贷 1年 0.060
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.040 * 1.1 //公积金 2～3年
      } else {
        return 0.0615 * 1.1 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.040 * 1.1 //公积金 4～5年
      } else {
        return 0.0640 * 1.1 //商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.045 * 1.1 //公积金5年以上
      } else {
        return 0.0655 * 1.1 //商贷5年以上 0.0655
      }
    }

  } else if (lilv_class == 67) {
    //2012年	6.8的利率下浮15%
    if (years <= 1) {
      if (type == 2) {
        return 0.040 * 0.85 //公积金 1年
      } else {
        return 0.060 * 0.85 //商贷 1年 0.060
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.040 * 0.85 //公积金 2～3年
      } else {
        return 0.0615 * 0.85 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.040 * 0.85 //公积金 4～5年
      } else {
        return 0.0640 * 0.85 //商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.045 * 0.85 //公积金5年以上
      } else {
        return 0.0655 * 0.85 //商贷5年以上 0.0655
      }
    }

  } else if (lilv_class == 68) {
    //2012年	6.8的利率下浮20%
    if (years <= 1) {
      if (type == 2) {
        return 0.040 * 0.8 //公积金 1年
      } else {
        return 0.060 * 0.8 //商贷 1年 0.060
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.040 * 0.8 //公积金 2～3年
      } else {
        return 0.0615 * 0.8 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.040 * 0.8 //公积金 4～5年
      } else {
        return 0.0640 * 0.8 //商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.045 * 0.8 //公积金5年以上
      } else {
        return 0.0655 * 0.8 //商贷5年以上 0.0655
      }
    }

  } else if (lilv_class == 69) {
    //2012年	6.8的利率下浮30%
    if (years <= 1) {
      if (type == 2) {
        return 0.040 * 0.7 //公积金 1年
      } else {
        return 0.060 * 0.7 //商贷 1年 0.060
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.040 * 0.7 //公积金 2～3年
      } else {
        return 0.0615 * 0.7 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.040 * 0.7 //公积金 4～5年
      } else {
        return 0.0640 * 0.7 //商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.045 * 0.7 //公积金5年以上
      } else {
        return 0.0655 * 0.7 //商贷5年以上 0.0655
      }
    }

  } else if (lilv_class == 70) {
    //2012年	6.8的利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0375 //公积金 1年
      } else {
        return 0.056 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0375 //公积金 2～3年
      } else {
        return 0.060 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0375 //公积金 4～5年
      } else {
        return 0.060//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0425 //公积金5年以上
      } else {
        return 0.0615 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 71) {
    //2012年	6.8的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0375 * 1.1 //公积金 1年
      } else {
        return 0.056 * 1.1 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0375 * 1.1 //公积金 2～3年
      } else {
        return 0.060 * 1.1 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0375 * 1.1 //公积金 4～5年
      } else {
        return 0.060 * 1.1//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0425 * 1.1 //公积金5年以上
      } else {
        return 0.0615 * 1.1 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 72) {
    //2012年	6.8的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0375 * 0.85 //公积金 1年
      } else {
        return 0.056 * 0.85 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0375 * 0.85 //公积金 2～3年
      } else {
        return 0.060 * 0.85 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0375 * 0.85 //公积金 4～5年
      } else {
        return 0.060 * 0.85//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0425 * 0.85 //公积金5年以上
      } else {
        return 0.0615 * 0.85 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 73) {
    //2012年	6.8的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0375 * 0.8 //公积金 1年
      } else {
        return 0.056 * 0.8 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0375 * 0.8 //公积金 2～3年
      } else {
        return 0.060 * 0.8 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0375 * 0.8 //公积金 4～5年
      } else {
        return 0.060 * 0.8//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0425 * 0.8 //公积金5年以上
      } else {
        return 0.0615 * 0.8 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 74) {
    //2012年	6.8的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0375 * 0.7 //公积金 1年
      } else {
        return 0.056 * 0.7 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0375 * 0.7 //公积金 2～3年
      } else {
        return 0.060 * 0.7 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0375 * 0.7 //公积金 4～5年
      } else {
        return 0.060 * 0.7//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0425 * 0.7 //公积金5年以上
      } else {
        return 0.0615 * 0.7 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 75) {
    //2015年3月1	基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.035 //公积金 1年
      } else {
        return 0.0535 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.035 //公积金 2～3年
      } else {
        return 0.0575 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.035 //公积金 4～5年
      } else {
        return 0.0575//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.04 //公积金5年以上
      } else {
        return 0.059 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 76) {
    //2015年3月1号	6.8的利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.035 * 1.1 //公积金 1年
      } else {
        return 0.0535 * 1.1 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.035 * 1.1 //公积金 2～3年
      } else {
        return 0.0575 * 1.1 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.035 * 1.1 //公积金 4～5年
      } else {
        return 0.0575 * 1.1//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.04 * 1.1 //公积金5年以上
      } else {
        return 0.059 * 1.1 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 77) {
    //2015年3月1号	6.8的利率下浮15%
    if (years <= 1) {
      if (type == 2) {
        return 0.035 * 0.85 //公积金 1年
      } else {
        return 0.0535 * 0.85 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.035 * 0.85 //公积金 2～3年
      } else {
        return 0.0575 * 0.85 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.035 * 0.85 //公积金 4～5年
      } else {
        return 0.0575 * 0.85//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.04 * 0.85 //公积金5年以上
      } else {
        return 0.059 * 0.85 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 78) {
    //2015年3月1号	6.8的利率下浮20%
    if (years <= 1) {
      if (type == 2) {
        return 0.035 * 0.8 //公积金 1年
      } else {
        return 0.0535 * 0.8 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.035 * 0.8 //公积金 2～3年
      } else {
        return 0.0575 * 0.8 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.035 * 0.8 //公积金 4～5年
      } else {
        return 0.0575 * 0.8//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.04 * 0.8 //公积金5年以上
      } else {
        return 0.059 * 0.8 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 79) {
    //2015年3月1号	6.8的利率下浮30%
    if (years <= 1) {
      if (type == 2) {
        return 0.035 * 0.7 //公积金 1年
      } else {
        return 0.0535 * 0.7 //商贷 1年 0.0631
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.035 * 0.7 //公积金 2～3年
      } else {
        return 0.0575 * 0.7 //商贷 2～3年 0.0615
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.035 * 0.7 //公积金 4～5年
      } else {
        return 0.0575 * 0.7//商贷 4～5年 0.0640
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.04 * 0.7 //公积金5年以上
      } else {
        return 0.059 * 0.7 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 80) {
    //2015年5月11	基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0325 //公积金 1年
      } else {
        return 0.051 //商贷 1年 0.051
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0325 //公积金 2～3年
      } else {
        return 0.055 //商贷 2～3年 0.055
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0325 //公积金 4～5年
      } else {
        return 0.055//商贷 4～5年 0.055
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0375 //公积金5年以上
      } else {
        return 0.0565 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 81) {
    //2015年5月11号	利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0325 * 1.1 //公积金 1年
      } else {
        return 0.051 * 1.1 //商贷 1年 0.051
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0325 * 1.1 //公积金 2～3年
      } else {
        return 0.055 * 1.1 //商贷 2～3年 0.055
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0325 * 1.1 //公积金 4～5年
      } else {
        return 0.055 * 1.1//商贷 4～5年 0.055
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0375 * 1.1 //公积金5年以上
      } else {
        return 0.0565 * 1.1 //商贷5年以上 0.0565
      }
    }

  } else if (lilv_class == 82) {
    //2015年5月11号	利率下浮15%
    if (years <= 1) {
      if (type == 2) {
        return 0.0325 * 0.85 //公积金 1年
      } else {
        return 0.051 * 0.85 //商贷 1年 0.051
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0325 * 0.85 //公积金 2～3年
      } else {
        return 0.055 * 0.85 //商贷 2～3年 0.055
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0325 * 0.85 //公积金 4～5年
      } else {
        return 0.055 * 0.85//商贷 4～5年 0.055
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0375 * 0.85 //公积金5年以上
      } else {
        return 0.0565 * 0.85 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 83) {
    //2015年5月11号	利率下浮20%
    if (years <= 1) {
      if (type == 2) {
        return 0.0325 * 0.8 //公积金 1年
      } else {
        return 0.051 * 0.8 //商贷 1年 0.051
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0325 * 0.8 //公积金 2～3年
      } else {
        return 0.055 * 0.8 //商贷 2～3年 0.055
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0325 * 0.8 //公积金 4～5年
      } else {
        return 0.055 * 0.8//商贷 4～5年 0.055
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0375 * 0.8 //公积金5年以上
      } else {
        return 0.0565 * 0.8 //商贷5年以上 0.0565
      }
    }

  } else if (lilv_class == 84) {
    //2015年5月11号	利率下浮30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0325 * 0.7 //公积金 1年
      } else {
        return 0.051 * 0.7 //商贷 1年 0.051
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0325 * 0.7 //公积金 2～3年
      } else {
        return 0.055 * 0.7 //商贷 2～3年 0.055
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0325 * 0.7 //公积金 4～5年
      } else {
        return 0.055 * 0.7//商贷 4～5年 0.055
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0375 * 0.7 //公积金5年以上
      } else {
        return 0.0565 * 0.7 //商贷5年以上 0.0565
      }
    }

  } else if (lilv_class == 85) {
    //2015年6月28	基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0300 //公积金 1年
      } else {
        return 0.0485 //商贷 1年 0.0485
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0300 //公积金 2～3年
      } else {
        return 0.0525 //商贷 2～3年 0.0525
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0300 //公积金 4～5年
      } else {
        return 0.0525//商贷 4～5年 0.0525
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0350 //公积金5年以上
      } else {
        return 0.0540 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 86) {
    //2015年6月28号	利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0300 * 1.1 //公积金 1年
      } else {
        return 0.0485 * 1.1 //商贷 1年 0.0485
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0300 * 1.1 //公积金 2～3年
      } else {
        return 0.0525 * 1.1 //商贷 2～3年 0.0525
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0300 * 1.1 //公积金 4～5年
      } else {
        return 0.0525 * 1.1//商贷 4～5年 0.0525
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0350 * 1.1 //公积金5年以上
      } else {
        return 0.0540 * 1.1 //商贷5年以上 0.0540
      }
    }

  } else if (lilv_class == 87) {
    //2015年6月28号	利率下浮15%
    if (years <= 1) {
      if (type == 2) {
        return 0.0300 * 0.85 //公积金 1年
      } else {
        return 0.0485 * 0.85 //商贷 1年 0.0485
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0300 * 0.85 //公积金 2～3年
      } else {
        return 0.0525 * 0.85 //商贷 2～3年 0.0525
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0300 * 0.85 //公积金 4～5年
      } else {
        return 0.0525 * 0.85//商贷 4～5年 0.0525
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0350 * 0.85 //公积金5年以上
      } else {
        return 0.0540 * 0.85 //商贷5年以上 0.065
      }
    }

  } else if (lilv_class == 88) {
    //2015年6月28号	利率下浮20%
    if (years <= 1) {
      if (type == 2) {
        return 0.0300 * 0.8 //公积金 1年
      } else {
        return 0.0485 * 0.8 //商贷 1年 0.0485
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0300 * 0.8 //公积金 2～3年
      } else {
        return 0.0525 * 0.8 //商贷 2～3年 0.0525
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0300 * 0.8 //公积金 4～5年
      } else {
        return 0.0525 * 0.8//商贷 4～5年 0.0525
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0350 * 0.8 //公积金5年以上
      } else {
        return 0.0540 * 0.8 //商贷5年以上 0.0540
      }
    }

  } else if (lilv_class == 89) {
    //2015年6月28号	利率下浮30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0300 * 0.7 //公积金 1年
      } else {
        return 0.0485 * 0.7 //商贷 1年 0.0485
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0300 * 0.7 //公积金 2～3年
      } else {
        return 0.0525 * 0.7 //商贷 2～3年 0.0525
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0300 * 0.7 //公积金 4～5年
      } else {
        return 0.0525 * 0.7//商贷 4～5年 0.0525
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0350 * 0.7 //公积金5年以上
      } else {
        return 0.0540 * 0.7 //商贷5年以上 0.0540
      }
    }

  } else if (lilv_class == 90) {
    //2015年8月26	基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 //公积金 1年
      } else {
        return 0.0460 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 //公积金 2～3年
      } else {
        return 0.0500 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 //公积金 4～5年
      } else {
        return 0.0500//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 //公积金5年以上
      } else {
        return 0.0515 //商贷5年以上
      }
    }

  } else if (lilv_class == 91) {
    //2015年6月28号	利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 1.1 //公积金 1年
      } else {
        return 0.0460 * 1.1 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 1.1 //公积金 2～3年
      } else {
        return 0.0500 * 1.1 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 1.1 //公积金 4～5年
      } else {
        return 0.0500 * 1.1//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 1.1 //公积金5年以上
      } else {
        return 0.0515 * 1.1 //商贷5年以上
      }
    }

  } else if (lilv_class == 92) {
    //2015年6月28号	利率下浮15%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 0.85 //公积金 1年
      } else {
        return 0.0460 * 0.85 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 0.85 //公积金 2～3年
      } else {
        return 0.0500 * 0.85 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 0.85 //公积金 4～5年
      } else {
        return 0.0500 * 0.85//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 0.85 //公积金5年以上
      } else {
        return 0.0515 * 0.85 //商贷5年以上
      }
    }

  } else if (lilv_class == 93) {
    //2015年6月28号	利率下浮20%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 0.8 //公积金 1年
      } else {
        return 0.0460 * 0.8 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 0.8 //公积金 2～3年
      } else {
        return 0.0500 * 0.8 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 0.8 //公积金 4～5年
      } else {
        return 0.0500 * 0.8//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 0.8 //公积金5年以上
      } else {
        return 0.0515 * 0.8 //商贷5年以上
      }
    }

  } else if (lilv_class == 94) {
    //2015年6月28号	利率下浮30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 0.7 //公积金 1年
      } else {
        return 0.0460 * 0.7 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 0.7 //公积金 2～3年
      } else {
        return 0.0500 * 0.7 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 0.7 //公积金 4～5年
      } else {
        return 0.0500 * 0.7//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 0.7 //公积金5年以上
      } else {
        return 0.0515 * 0.7 //商贷5年以上
      }
    }

  } else if (lilv_class == 95) {
    //2015年10月24	基准利率
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 //公积金 1年
      } else {
        return 0.0435 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 //公积金 2～3年
      } else {
        return 0.0475 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 //公积金 4～5年
      } else {
        return 0.0475//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 //公积金5年以上
      } else {
        return 0.0490 //商贷5年以上
      }
    }

  } else if (lilv_class == 96) {
    //2015年6月28号	利率上浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 1.1 //公积金 1年
      } else {
        return 0.0435 * 1.1 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 1.1 //公积金 2～3年
      } else {
        return 0.0475 * 1.1 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 1.1 //公积金 4～5年
      } else {
        return 0.0475 * 1.1//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 1.1 //公积金5年以上
      } else {
        return 0.0490 * 1.1 //商贷5年以上
      }
    }

  } else if (lilv_class == 97) {
    //2015年6月28号	利率下浮15%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 0.85 //公积金 1年
      } else {
        return 0.0435 * 0.85 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 0.85 //公积金 2～3年
      } else {
        return 0.0475 * 0.85 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 0.85 //公积金 4～5年
      } else {
        return 0.0475 * 0.85//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 0.85 //公积金5年以上
      } else {
        return 0.0490 * 0.85 //商贷5年以上
      }
    }

  } else if (lilv_class == 98) {
    //2015年6月28号	利率下浮20%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 0.8 //公积金 1年
      } else {
        return 0.0435 * 0.8 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 0.8 //公积金 2～3年
      } else {
        return 0.0475 * 0.8 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 0.8 //公积金 4～5年
      } else {
        return 0.0475 * 0.8//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 0.8 //公积金5年以上
      } else {
        return 0.0490 * 0.8 //商贷5年以上
      }
    }

  } else if (lilv_class == 99) {
    //2015年6月28号	利率下浮30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 0.7 //公积金 1年
      } else {
        return 0.0435 * 0.7 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 0.7 //公积金 2～3年
      } else {
        return 0.0475 * 0.7 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 0.7 //公积金 4～5年
      } else {
        return 0.0475 * 0.7//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 0.7 //公积金5年以上
      } else {
        return 0.0490 * 0.7 //商贷5年以上
      }
    }

  } else if (lilv_class == 100) {
    //2015年6月28号	利率下浮10%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 0.9 //公积金 1年
      } else {
        return 0.0435 * 0.9 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 0.9 //公积金 2～3年
      } else {
        return 0.0475 * 0.9 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 0.9 //公积金 4～5年
      } else {
        return 0.0475 * 0.9//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 0.9 //公积金5年以上
      } else {
        return 0.0490 * 0.9 //商贷5年以上
      }
    }

  } else if (lilv_class == 101) {
    //2015年6月28号	利率下浮5%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 0.95 //公积金 1年
      } else {
        return 0.0435 * 0.95 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 0.95 //公积金 2～3年
      } else {
        return 0.0475 * 0.95 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 0.95 //公积金 4～5年
      } else {
        return 0.0475 * 0.95//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 0.95 //公积金5年以上
      } else {
        return 0.0490 * 0.95 //商贷5年以上
      }
    }

  } else if (lilv_class == 102) {
    //2015年6月28号	利率上浮20%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 1.2 //公积金 1年
      } else {
        return 0.0435 * 1.2 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 1.2 //公积金 2～3年
      } else {
        return 0.0475 * 1.2 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 1.2 //公积金 4～5年
      } else {
        return 0.0475 * 1.2//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 1.2 //公积金5年以上
      } else {
        return 0.0490 * 1.2 //商贷5年以上
      }
    }

  } else if (lilv_class == 103) {
    //2015年10月24号	利率上浮25%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 1.25 //公积金 1年
      } else {
        return 0.0435 * 1.25 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 1.25 //公积金 2～3年
      } else {
        return 0.0475 * 1.25 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 1.25 //公积金 4～5年
      } else {
        return 0.0475 * 1.25//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 1.25 //公积金5年以上
      } else {
        return 0.0490 * 1.25 //商贷5年以上
      }
    }

  } else if (lilv_class == 104) {
    //2015年10月24号	利率上浮30%
    if (years <= 1) {
      if (type == 2) {
        return 0.0275 * 1.3 //公积金 1年
      } else {
        return 0.0435 * 1.3 //商贷 1年
      }
    }

    if (years == 2 || years == 3) {
      if (type == 2) {
        return 0.0275 * 1.3 //公积金 2～3年
      } else {
        return 0.0475 * 1.3 //商贷 2～3年
      }
    }

    if (years == 4 || years == 5) {
      if (type == 2) {
        return 0.0275 * 1.3 //公积金 4～5年
      } else {
        return 0.0475 * 1.3//商贷 4～5年
      }
    }

    if (years > 5) {
      if (type == 2) {
        return 0.0325 * 1.3 //公积金5年以上
      } else {
        return 0.0490 * 1.3 //商贷5年以上
      }
    }

  } else {
    //2004年之前的旧利率
    if (years <= 5) {
      if (type == 2) {
        return 0.0360//公积金 1～5年 3.60%
      } else {
        return 0.0477//商贷 1～5年 4.77%
      }
    } else {
      if (type == 2) {
        return 0.0405//公积金 5-30年 4.05%
      } else {
        return 0.0504//商贷 5-30年 5.04%
      }
    }
  }
}

//本金还款的月还款额(参数: 年利率 / 贷款总额 / 贷款总月份 / 贷款当前月0～length-1)
function getMonthMoney2(lilv, total, month, cur_month) {
  var lilv_month = lilv / 12;//月利率
  //return total * lilv_month * Math.pow(1 + lilv_month, month) / ( Math.pow(1 + lilv_month, month) -1 );
  var benjin_money = total / month;
  return (total - benjin_money * cur_month) * lilv_month + benjin_money;

}


//本息还款的月还款额(参数: 年利率/贷款总额/贷款总月份)
function getMonthMoney1(lilv, total, month) {
  var lilv_month = lilv / 12;//月利率
  return total * lilv_month * Math.pow(1 + lilv_month, month) / ( Math.pow(1 + lilv_month, month) - 1 );
}

function ext_total(fmobj) {

  //var fmobj=document.calc1;
  //先清空月还款数下拉框
  //while ((k=fmobj.month_money2.length-1)>=0){
  //	fmobj.month_money2.options.remove(k);
  //}
  var years = fmobj.years.value;
  var month = fmobj.years.value * 12;

  fmobj.month1.value = month + "(月)";
  fmobj.month2.value = month + "(月)";

  if (fmobj.type.value == 3) {

    var years_sy = fmobj.years_sy.value;
    var month_sy = fmobj.years_sy.value * 12;

    var years_gjj = fmobj.years_gjj.value;
    var month_gjj = fmobj.years_gjj.value * 12;

    fmobj.month1.value = "商业:" + month_sy + "月 公积金:" + month_gjj + "月";
    fmobj.month2.value = "商业:" + month_sy + "月 公积金:" + month_gjj + "月";

    //--  组合型贷款(组合型贷款的计算，只和商业贷款额、和公积金贷款额有关，和按贷款总额计算无关)
    if (!reg_Num(fmobj.total_sy.value)) {
      alert("混合型贷款请填写商贷金额");
      fmobj.total_sy.focus();
      return false;
    }
    if (!reg_Num(fmobj.total_gjj.value)) {
      alert("混合型贷款请填写公积金金额");
      fmobj.total_gjj.focus();
      return false;
    }
    if (fmobj.total_sy.value == null) {
      fmobj.total_sy.value = 0;
    }
    if (fmobj.total_gjj.value == null) {
      fmobj.total_gjj.value = 0;
    }
    var total_sy = fmobj.total_sy.value;
    var total_gjj = fmobj.total_gjj.value;
    fmobj.fangkuan_total1.value = "略";//房款总额
    fmobj.fangkuan_total2.value = "略";//房款总额
    fmobj.money_first1.value = 0;//首期付款
    fmobj.money_first2.value = 0;//首期付款

    //贷款总额
    var total_sy = parseInt(fmobj.total_sy.value);
    var total_gjj = parseInt(fmobj.total_gjj.value);
    var daikuan_total = total_sy + total_gjj;
    fmobj.daikuan_total1.value = daikuan_total;
    fmobj.daikuan_total2.value = daikuan_total;

    //利率
//			var lilv_sy = getlilv(fmobj.lilv.value,1, years_sy);//得到商贷利率
//			var lilv_gjj = getlilv(fmobj.lilv_2.value,2, years_gjj);//得到公积金利率
    //商贷利率
    var lilv_input = fmobj.lilv_input.value;
    var lilv_multiple = fmobj.lilv_multiple.value;
    var lilv_sy = lilv_input * lilv_multiple * 0.01;
    //公积金利率
    var lilv_input_2 = fmobj.lilv_input_2.value;
    var lilv_multiple_2 = fmobj.lilv_multiple_2.value;
    var lilv_gjj = lilv_input_2 * lilv_multiple_2 * 0.01;

    //1.本金还款
    //月还款
    var all_total2 = 0;
    var month_money2 = "";
    var mm = new Array(month_sy >= month_gjj ? month_sy : month_gjj);

    for (j = 0; j < mm.length; j++) {
      mm[j] = 0;
    }

    for (j = 0; j < month_sy; j++) {
      //调用函数计算: 本金月还款额
      huankuan = getMonthMoney2(lilv_sy, total_sy, month_sy, j);
      huankuan = Math.round(huankuan * 100) / 100;
      all_total2 += huankuan;
      mm[j] = huankuan;
    }

    for (j = 0; j < month_gjj; j++) {
      //调用函数计算: 本金月还款额
      huankuan = getMonthMoney2(lilv_gjj, total_gjj, month_gjj, j);
      huankuan = Math.round(huankuan * 100) / 100;
      all_total2 += huankuan;
      mm[j] = mm[j] + huankuan;
    }

    //月还款
    for (j = 0; j < mm.length; j++) {
      month_money2 += (j + 1) + "月," + mm[j].toFixed(2) + "(元)\n";
    }

    //每月还款细表
    fmobj.month_money2.value = month_money2;

    //还款总额
    fmobj.all_total2.value = Math.round(all_total2 * 100) / 100;
    //支付利息款
    fmobj.accrual2.value = Math.round((all_total2 - daikuan_total) * 100) / 100;


    //2.本息还款
    //月均还款
    var month_money_sy = getMonthMoney1(lilv_sy, total_sy, month_sy);
    var month_money_gjj = getMonthMoney1(lilv_gjj, total_gjj, month_gjj);
    var month_money1 = month_money_sy + month_money_gjj;
    fmobj.month_money1.value = Math.round(month_money1 * 100) / 100 + "(元)";
    //还款总额
    var all_total1 = month_money_sy * month_sy + month_money_gjj * month_gjj;
    fmobj.all_total1.value = Math.round(all_total1 * 100) / 100;
    //支付利息款
    fmobj.accrual1.value = Math.round((all_total1 - daikuan_total) * 100) / 100;

  } else {
    //--  商业贷款、公积金贷款
    //区分商业贷款、公积金贷款，得到不同的利率
    if (fmobj.type.value == 1) {
      //var lilv = getlilv(fmobj.lilv.value,fmobj.type.value, fmobj.years.value);//得到利率
      var lilv_input = fmobj.lilv_input.value;
      var lilv_multiple = fmobj.lilv_multiple.value;
      var lilv_result = lilv_input * lilv_multiple * 0.01;
    } else if (fmobj.type.value == 2) {
      //var lilv = getlilv(fmobj.lilv_2.value,fmobj.type.value, fmobj.years.value);//得到利率
      var lilv_input_2 = fmobj.lilv_input_2.value;
      var lilv_multiple_2 = fmobj.lilv_multiple_2.value;
      var lilv_result = lilv_input_2 * lilv_multiple_2 * 0.01;
    }

    if (fmobj.jisuan_radio[0].checked == true) {
      //------------ 根据单价面积计算
      if (!reg_Num(fmobj.price.value)) {
        alert("请填写单价");
        fmobj.price.focus();
        return false;
      }
      if (!reg_Num(fmobj.sqm.value)) {
        alert("请填写面积");
        fmobj.sqm.focus();
        return false;
      }

      //房款总额
      var fangkuan_total = fmobj.price.value * fmobj.sqm.value;
      fmobj.fangkuan_total1.value = fangkuan_total;
      fmobj.fangkuan_total2.value = fangkuan_total;
      //贷款总额
      var daikuan_total = (fmobj.price.value * fmobj.sqm.value) * (fmobj.anjie.value / 10);
      fmobj.daikuan_total1.value = daikuan_total;
      fmobj.daikuan_total2.value = daikuan_total;
      //首期付款
      var money_first = fangkuan_total - daikuan_total;
      fmobj.money_first1.value = money_first
      fmobj.money_first2.value = money_first;
    } else {
      //------------ 根据贷款总额计算
      if (fmobj.daikuan_total000.value.length == 0) {
        alert("请填写贷款总额");
        fmobj.daikuan_total000.focus();
        return false;
      }

      //房款总额
      fmobj.fangkuan_total1.value = "略";
      fmobj.fangkuan_total2.value = "略";
      //贷款总额
      var daikuan_total = fmobj.daikuan_total000.value;
      fmobj.daikuan_total1.value = daikuan_total;
      fmobj.daikuan_total2.value = daikuan_total;
      //首期付款
      fmobj.money_first1.value = 0;
      fmobj.money_first2.value = 0;
    }
    //1.本金还款
    //月还款
    var all_total2 = 0;
    var month_money2 = "";
    for (j = 0; j < month; j++) {
      //调用函数计算: 本金月还款额
      huankuan = getMonthMoney2(lilv_result, daikuan_total, month, j);
      all_total2 += huankuan;
      huankuan = Math.round(huankuan * 100) / 100;
      //fmobj.month_money2.options[j] = new Option( (j+1) +"月," + huankuan + "(元)", huankuan);
      month_money2 += (j + 1) + "月," + huankuan + "(元)\n";
    }
    fmobj.month_money2.value = month_money2;
    //还款总额
    fmobj.all_total2.value = Math.round(all_total2 * 100) / 100;
    //支付利息款
    fmobj.accrual2.value = Math.round((all_total2 - daikuan_total) * 100) / 100;


    //2.本息还款
    //月均还款
    var month_money1 = getMonthMoney1(lilv_result, daikuan_total, month);//调用函数计算
    fmobj.month_money1.value = Math.round(month_money1 * 100) / 100 + "(元)";
    //还款总额
    var all_total1 = month_money1 * month;
    fmobj.all_total1.value = Math.round(all_total1 * 100) / 100;
    //支付利息款
    fmobj.accrual1.value = Math.round((all_total1 - daikuan_total) * 100) / 100;

  }
}

//提前还歀计算
function play() {
  if (document.tqhdjsq.dkzws.value == '') {
    alert('请填入贷款总额');
    return false;
  } else dkzys = parseFloat(document.tqhdjsq.dkzws.value) * 10000;

  if (document.tqhdjsq.tqhkfs[1].checked && document.tqhdjsq.tqhkws.value == '') {
    alert('请填入部分提前还款额度');
    return false;
  }
  s_yhkqs = parseInt(document.tqhdjsq.yhkqs.value);

  //月利率

  if (document.tqhdjsq.dklx[0].checked) {
    if (s_yhkqs > 60) {
      dklv = getlilv(document.tqhdjsq.dklv_class.value, 2, 10) / 12; //公积金贷款利率5年以上4.23%
    } else {
      dklv = getlilv(document.tqhdjsq.dklv_class.value, 2, 3) / 12;  //公积金贷款利率5年(含)以下3.78%
    }
  }
  if (document.tqhdjsq.dklx[1].checked) {
    if (s_yhkqs > 60) {
      dklv = getlilv(document.tqhdjsq.dklv_class.value, 1, 10) / 12; //商业性贷款利率5年以上5.31%
    } else {
      dklv = getlilv(document.tqhdjsq.dklv_class.value, 1, 3) / 12; //商业性贷款利率5年(含)以下4.95%
    }
  }

  //已还贷款期数
  yhdkqs = (parseInt(document.tqhdjsq.tqhksjn.value) * 12 + parseInt(document.tqhdjsq.tqhksjy.value)) - (parseInt(document.tqhdjsq.yhksjn.value) * 12 + parseInt(document.tqhdjsq.yhksjy.value));

  if (yhdkqs < 0 || yhdkqs > s_yhkqs) {
    alert('预计提前还款时间与第一次还款时间有矛盾，请查实');
    return false;
  }

  yhk = dkzys * (dklv * Math.pow((1 + dklv), s_yhkqs)) / (Math.pow((1 + dklv), s_yhkqs) - 1);
  yhkjssj = Math.floor((parseInt(document.tqhdjsq.yhksjn.value) * 12 + parseInt(document.tqhdjsq.yhksjy.value) + s_yhkqs - 2) / 12) + '年' + ((parseInt(document.tqhdjsq.yhksjn.value) * 12 + parseInt(document.tqhdjsq.yhksjy.value) + s_yhkqs - 2) % 12 + 1) + '月';
  yhdkys = yhk * yhdkqs;

  yhlxs = 0;
  yhbjs = 0;
  for (i = 1; i <= yhdkqs; i++) {
    yhlxs = yhlxs + (dkzys - yhbjs) * dklv;
    yhbjs = yhbjs + yhk - (dkzys - yhbjs) * dklv;
  }

  remark = '';
  if (document.tqhdjsq.tqhkfs[1].checked) {
    tqhkys = parseInt(document.tqhdjsq.tqhkws.value) * 10000;
    if (tqhkys + yhk >= (dkzys - yhbjs) * (1 + dklv)) {
      remark = '您的提前还款额已足够还清所欠贷款！';
    } else {
      yhbjs = yhbjs + yhk;
      byhk = yhk + tqhkys;
      if (document.tqhdjsq.clfs[0].checked) {
        yhbjs_temp = yhbjs + tqhkys;
        for (xdkqs = 0; yhbjs_temp <= dkzys; xdkqs++) yhbjs_temp = yhbjs_temp + yhk - (dkzys - yhbjs_temp) * dklv;
        xdkqs = xdkqs - 1;
        xyhk = (dkzys - yhbjs - tqhkys) * (dklv * Math.pow((1 + dklv), xdkqs)) / (Math.pow((1 + dklv), xdkqs) - 1);
        jslx = yhk * s_yhkqs - yhdkys - byhk - xyhk * xdkqs;
        xdkjssj = Math.floor((parseInt(document.tqhdjsq.tqhksjn.value) * 12 + parseInt(document.tqhdjsq.tqhksjy.value) + xdkqs - 2) / 12) + '年' + ((parseInt(document.tqhdjsq.tqhksjn.value) * 12 + parseInt(document.tqhdjsq.tqhksjy.value) + xdkqs - 2) % 12 + 1) + '月';
      } else {
        xyhk = (dkzys - yhbjs - tqhkys) * (dklv * Math.pow((1 + dklv), (s_yhkqs - yhdkqs))) / (Math.pow((1 + dklv), (s_yhkqs - yhdkqs)) - 1);
        jslx = yhk * s_yhkqs - yhdkys - byhk - xyhk * (s_yhkqs - yhdkqs);
        xdkjssj = yhkjssj;
      }
    }
  }

  if (document.tqhdjsq.tqhkfs[0].checked || remark != '') {
    byhk = (dkzys - yhbjs) * (1 + dklv);
    xyhk = 0;
    jslx = yhk * s_yhkqs - yhdkys - byhk;
    xdkjssj = document.tqhdjsq.tqhksjn.value + '年' + document.tqhdjsq.tqhksjy.value + '月';
  }

  document.tqhdjsq.ykhke.value = Math.round(yhk * 100) / 100;
  document.tqhdjsq.yhkze.value = Math.round(yhdkys * 100) / 100;
  document.tqhdjsq.yhlxe.value = Math.round(yhlxs * 100) / 100;
  document.tqhdjsq.gyyihke.value = Math.round(byhk * 100) / 100;
  document.tqhdjsq.xyqyhke.value = Math.round(xyhk * 100) / 100;
  document.tqhdjsq.jslxzc.value = Math.round(jslx * 100) / 100;
  document.tqhdjsq.yzhhkq.value = yhkjssj;
  document.tqhdjsq.xdzhhkq.value = xdkjssj;
  document.tqhdjsq.jsjgts.value = remark;
}
