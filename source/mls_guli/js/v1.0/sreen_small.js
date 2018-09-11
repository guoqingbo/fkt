$(function () {
  function samllTab() {
    var tab_pre = $(".small_pre"); //左切换
    var tab_nex = $(".small_nex"); //右切换
    var aObj = $(".zws_bottom_nav_dao");
    var aObjUl = aObj.find("ul");
    var aObjLl = aObj.find("li");
    var aObjLlWidth = aObj.find("li").outerWidth() + 10;
    UlLength(aObjUl, aObjLl);
    var objNum = 0;
    var totalNumLi = aObjLl.length - Math.ceil(aObj.width() / aObjLlWidth);

    //左切换
    tab_pre.on("click", function () {
      objNum--;
      objNum = objNum < 0 ? 0 : objNum;
      aObjUl.animate({"margin-left": -objNum * aObjLlWidth + "px"}, 300)

    })
    //右切换
    tab_nex.on("click", function () {
      objNum++;
      objNum = objNum < totalNumLi ? objNum : totalNumLi;
      aObjUl.animate({"margin-left": -objNum * aObjLlWidth + "px"}, 300)

    })


    //当前标签处理
    aObjLl.on("click", function () {
      $(".zws_bottom_nav_dao_img").removeClass("curSmall_S");
      $(this).find(".zws_bottom_nav_dao_img").addClass("curSmall_S");

    })
    //标签关闭处理
    aObjLl.find(".zws_bottom_span_close").on("click", function () {
      $(this).parent("li").remove();
      UlLength(aObjUl, aObjLl);
    })


  }


  //容器长度赋值
  function UlLength(obj, tagert) {
    var aUlWidth = $(obj).find(tagert).length * ($(obj).find(tagert).outerWidth() + 10);
    $(obj).css("width", $(obj).find(tagert).length * ($(obj).find(tagert).outerWidth() + 10) + "px");
    compareShow(aUlWidth, $(".zws_bottom_nav_dao").width());

  }

  //个数判断
  function compareShow(obj, target) {
    if (obj < target) {
      $(".zws_container").hide();
    }
    else {
      $(".zws_container").show();
    }
  }

  //samllTab();
})
