// 个人中心


$(function () {
  document.oncontextmenu = function (e) {
    return false;
  }//禁止右键
  $("#js_personal_btn").click(function () {
    $(this).hide();
    $("#js_personal_change_from").show();
    $("#js_personal_table_l").show();
  });

  $("#js_personal_btn_n").click(function () {
    $("#js_personal_table_l").hide();
    $("#js_personal_change_from").hide();
    $("#js_personal_btn").show();
  });

  $(".personal_photo").hover(function () {
    $(this).find(".del_amend").show();
  }, function () {
    $(this).find(".del_amend").hide();
  });

  $(".btn_wrap .btn").hover(function () {
    $(this).addClass("btn_over");
  }, function () {
    $(this).removeClass("btn_over");
  })
  /*$("#confirm_btn").click(function(){
   var $span=$(this).find("span");

   var spanTxt=$span.text();
   if(spanTxt=="修改资料")
   {
   $span.text("保存资料");
   }
   else{
   $span.text("修改资料");
   }
   })*/
  $(".describe_photo .upload_btn").hover(function () {
    $(this).addClass("upload_btn_over")
  }, function () {
    $(this).removeClass("upload_btn_over")
  })


  $(".my_info .tx_normal").hover(function () {
    $(this).addClass("tx_normal_over")
  }, function () {
    $(this).removeClass("tx_normal_over")
  })


  /*$(".myself_photo_a").hover(function(){
   $(".show_editor_remove").show();
   },function(){$(".show_editor_remove").hide();
   })

   $(".show_editor_remove").hover(function(){
   $(".show_editor_remove").show();
   },function(){$(".show_editor_remove").hide();})*/

  $(".b_line .info").hover(function () {
    /*var x_1=$(this).offset();
     var lOffset=x_1.left;
     var tOffset=x_1.top+$(this).outerHeight();
     $(".replace_stores_popUp").css({left:lOffset,top:tOffset});*/
    $(".r_s_popUP").show();
  }, function () {
    $(".r_s_popUP").hide();
  })

});

