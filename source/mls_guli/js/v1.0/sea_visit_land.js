$(function () {
  //弹出框高度
  var aScreen_pop_H = $(window).height(); //屏幕高度
  var aScreen_pop_W = $(window).width();//屏幕宽度
  var _pop_L = (aScreen_pop_W - 1200) / 2;
  // $(".sea_land_scroll_pop_con").css("height", aScreen_pop_H + "px");

  var img_H = aScreen_pop_H - 250;
  var img_W = img_H * 959 / 680;

  $(".sea_land_scroll_pop_con_img_sc_top img").css({"height": img_H + "px", "width": img_W + "px"})


  function pop_H() {

    var _pop_H = $(".sea_land_scroll_pop_con_img_sc").height(); //弹框的高度
    var _pop_big_H = $(".sea_land_scroll_pop_con_img_sc_top li").height(); //大图容器高度


    $(".sea_land_scroll_pop_con").css("left", _pop_L + "px");

    //判断弹框是否超过屏幕宽度

    $(".sea_land_scroll_pop_con_left").css("padding-top", (_pop_big_H - 128) / 2 + "px");
    $(".sea_land_scroll_pop_con_right").css("padding-top", (_pop_big_H - 128) / 2 + "px");
  }

  tab_Pop("#room_1", "0", 0);
  tab_Pop("#room_2", "0", 1);
  tab_Pop("#room_3", "0", 2);
  tab_Pop("#room_4", "0", 3);
  tab_Pop("#room_5", "0", 4);
  tab_Pop("#room_6", "0", 5);
  //pop_H();

  //图片切换
  function tab_Pop(ID, num_index, cur) {
    var ID = $(ID); //容器名
    var ID_pre = ID.find(".room_style_nex");//获取左箭头
    var ID_nex = ID.find(".room_style_pre");//获取左箭头
    var ID_ul = ID.find("ul");//获取要循环的容器名
    var ID_li = ID.find("li").length;//获取要循环的内容数量
    var ID_li_obj = ID.find("li");
    var ID_li_w = ID.find("li").outerWidth();//获取要循环的内容宽度

    var num_index = num_index;//默认起始位置
    var _big_img_show = 0; //弹出框大图当前图
    var _small_img_show = 0; //弹出框小图的位置
    var _big_img_show_W = 958; //弹框大图宽度
    var _small_img_show_W = 158; //弹框小图宽度


    //alert(ID_li );
    //左切换函数
    ID_pre.on("click", function () {
      if (num_index < (ID_li - 4)) {
        num_index++;
      }
      else {
        num_index = 0;
      }
      ID_ul.animate({"margin-left": -ID_li_w * num_index + "px"}, 300);
      //alert(num_index);

    })

    //右切换函数
    ID_nex.on("click", function () {

      if (num_index < 1) {
        num_index = (ID_li - 4);
      }
      else {
        num_index--;
      }
      ID_ul.animate({"margin-left": -ID_li_w * num_index + "px"}, 300);
      //alert(num_index);
    })

    //点击弹出图片
    ID_li_obj.on("click", function () {
      pop_H();

      $(".sea_land_scroll_pop_con").css("left", "-100000px");
      $(".sea_land_scroll_pop_con_img_sc_top img").css({"height": img_H + "px", "width": img_W + "px"})

      ID_li = $(this).parent("ul").find("li").length;
      $(".sea_land_scroll_pop_bg").show(); //遮罩层显示
      $(".sea_land_scroll_pop_con").eq(cur).css("left", _pop_L + "px");//当前弹出层显示
      $(".sea_land_scroll_pop_con").eq(cur).show();//当前弹出层显示
      _big_img_show = $(this).index();
      _small_img_show = $(this).index();


      $(".sea_land_scroll_pop_con_img_sc_bottom").eq(cur).find(" ul").css("width", (ID_li * _small_img_show_W) + "px");
      $(".sea_land_scroll_pop_con_img_sc_top").eq(cur).find(" ul").css("width", (ID_li * _big_img_show_W) + "px");
      $(".sea_land_scroll_pop_con_img_sc_bottom li").removeClass("pop_li_on");
      $(".sea_land_scroll_pop_con_img_sc_bottom li").eq(_small_img_show).addClass("pop_li_on");

      $(".sea_land_scroll_pop_con_img_sc_top ul").eq(cur).css("margin-left", -(_big_img_show * _big_img_show_W) + "px");
      $(".sea_land_scroll_pop_con_img_sc_bottom ul").eq(cur).css("margin-left", -(_small_img_show * _small_img_show_W) + "px");

    })

    //弹出框图片切换
    $(".sea_land_scroll_pop_con_img_sc_bottom").eq(cur).find("li").on("click", function () {

      _small_img_show = $(this).index();
      _big_img_show = $(this).index();
      pop_img_A();
    })

    //右切换
    $(".sea_land_scroll_pop_con_right").eq(cur).on("click", function () {
      _small_img_show++;
      _big_img_show++;

      if (ID_li < 6) {

        if (_big_img_show < ID_li) {

          _small_img_show = 0;
          _big_img_show = _big_img_show;

        }
        else {
          _small_img_show = 0;
          _big_img_show = ID_li - 1;
        }


      }
      else {
        if (_small_img_show < ID_li) {

          _small_img_show = _small_img_show;
          _big_img_show = _big_img_show;

        }
        else {
          _small_img_show = ID_li - 1;
          _big_img_show = ID_li - 1;
        }
        alert("b");

      }


      pop_img_A();

    })


    //左切换
    $(".sea_land_scroll_pop_con_left").eq(cur).on("click", function () {
      _small_img_show--;
      _big_img_show--;
      if (ID_li < 6) {

        if (_big_img_show < 0) {

          _small_img_show = 0;
          _big_img_show = 0;

        }
        else {
          _small_img_show = 0;
          _big_img_show = _big_img_show;
        }
      }
      else {


        if (_big_img_show < 0) {

          _small_img_show = 0;
          _big_img_show = 0;

        }
        else {
          _small_img_show = _small_img_show;
          _big_img_show = _big_img_show;
        }

      }


      pop_img_A();


    })


    //位置判断


    //动作执行
    function pop_img_A() {
      var _samll_div_img = $(".sea_land_scroll_pop_con_img_sc_bottom").eq(cur);
      _samll_div_img.find("li").removeClass("pop_li_on");
      _samll_div_img.find("li").eq(_big_img_show).addClass("pop_li_on");
      $(".sea_land_scroll_pop_con_img_sc_top ul").stop().animate({"margin-left": -(_big_img_show * _big_img_show_W) + "px"}, 300);
      _samll_div_img.find("ul").stop().animate({"margin-left": -(_small_img_show * _small_img_show_W) + "px"}, 300)

    }


    //关闭弹出框按钮
    $(".sea_land_scroll_pop_close").on("click", function () {

      $(".sea_land_scroll_pop_bg").hide(); //遮罩层隐藏

      $(".sea_land_scroll_pop_con").css("left", "-10000px");//弹出层隐藏
    })


  }


  //窗口自适应
  $(window).resize(function () {
    //pop_H();
    //弹出框高度
    var aScreen_pop_H = $(window).height(); //屏幕高度
    var aScreen_pop_W = $(window).width();//屏幕宽度
    var _pop_L = (aScreen_pop_W - 1200) / 2;


    var img_H = aScreen_pop_H - 250;
    var img_W = img_H * 959 / 680;
    //$(".sea_land_scroll_pop_con").css("height", aScreen_pop_H + "px");
    $(".sea_land_scroll_pop_con_img_sc_top img").css({"height": img_H + "px", "width": img_W + "px"})
  })

})
