<script src="http://ditu.google.cn/maps/api/js?sensor=false&v=3.5" type="text/javascript"></script>
<?php if(!$is_bgy){?>
<script type="text/javascript">
        var map;
        var latlng;
        var infowindow = new google.maps.InfoWindow({
        });
        $(function () {
            initialize();
            /*$(".OverSea_Facility dd span").click(function () {
                $(this).addClass("CurHover").siblings().removeClass("CurHover");
            })*/
        });

        function initialize() {

            var Pos_X = <?=$list['b_map_x']?>;
            var Pos_Y = <?=$list['b_map_y']?>;
            if (Pos_X != "" && Pos_Y != "") {
                // geocoder = new google.maps.Geocoder();
                latlng = new google.maps.LatLng(Pos_X, Pos_Y);

                var MapTypeIds = new Array();
                MapTypeIds[0] = "roadmap";
                MapTypeIds[1] = "bybird";
                var myOptions = {
                    zoom: 15,
                    center: latlng,
                    mapTypeId: 'roadmap',
                    scrollwheel: false,
                    MapTypeControlOptions: MapTypeIds
                }
                //         var myOptions = { zoom: 13, center: latlng, mapTypeId: google.maps.MapTypeId.ROADMAP, disableDefaultUI: true, zoomControl: true };
                //
                map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
                var locationMarker = new google.maps.Marker({ position: latlng, map: map, title: "<?=$list['block_name']?>", draggable: false, icon: true });
                locationMarker.setVisible(true);
            }
        }
    </script>
    <?php }?>
<div class="tab_box" id="js_tab_box">

    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
	<a href="/abroad/index" class="btn-lv" style="float:right; margin-right:10px;"><span>&lt;&lt;返回海外列表</span></a>
</div>
<div class='abroad' style='width:100%;height:530px;'>
 <div class="oversea_con_main">
        <!--详情页-->
        <span class="oversea_con_detial">
            <!--楼盘详情-->
            <div class="oversea_con_part oversea_dl_f">
                <span class="oversea_title oversea_dl_f"><p>楼盘详情</p></span>
                <div class="oversea_list_con_detial oversea_dl_f">
                    <div class="oversea_list_detial_con_left oversea_dl_f">
                        <!--大图-->
                        <div class="oversea_list__big_img oversea_dl_f">
                            <ul>
                                <li><img src="<?=changepic($list['pic'])?>" alt="" /></li>
								<?php
									foreach($list['pics'] as $val){
										if($val){
								?>
								<li><img src="<?=changepic($val)?>" alt="" /></li>
								<?php }}?>
                            </ul>
                        </div>
                        <!--小图-->
                        <span class="oversea_list__small_img">
                            <span class="sea_small_pre left_arrow"><!--向左箭头--></span>
                            <!--小图列表-->
                            <div class="oversea_list__small_img_items">
                                <ul>
									<li class="show_small_img">
                                        <img src="<?=$list['pic']?>" alt="" />
                                        <div></div>
                                        <p></p>
                                    </li>
								<?php
									foreach($list['pics'] as $val){
										if($val){
								?>
                                    <li>
                                        <img src="<?=$val?>" alt="" />
                                        <div></div>
                                        <p></p>
                                    </li>
								<?php }}?>
                                </ul>
                            </div>
                            <span class="sea_small_nex right_arrow"><!--向右箭头--></span>
                        </span>
                    </div>

                    <!--右侧详情部分-->
                    <div class="oversea_list_detial_mess_r">
                        <span class="oversea_list_detial_mess_r_title">
                            <h2 class="oversea_dl_f oversea_list_detial_mess_r_h2"><?=$list['block_name']?></h2>
                            <p class="oversea_list_detial_left_map oversea_dl_f"><?=$list['country_name']?> - <?=$list['city_name']?> - <?=$list['country_name_english']?>，<?=$list['city_name_english']?></p>
                            <p class="oversea_list_detial_lef_p oversea_dl_f">楼盘地址：<?=$list['address']?></p>
                            <dl class="oversea_dl2">
                                <dd>首付：<b><?=strip_end_0($list['first_pay'])?>万<?=$list['money_unit'];?>起</b></dd>
                                <dt>佣金：<b><?=$list['brokerage']?><?php if($list['brokerage_type'] == 1){?>元<?php }else{?>成交价<?php }?></b></dt>
                            </dl>
                            <dl class="oversea_dl3">
                                <dd><?=$list['feature']?></dd>
                                <dt>
                                    <p>国家：<?=$list['country_name']?></p>
                                    <p>城市：<?=$list['city_name']?></p>
                                    <p>类型：<?=$list['house_type']?></p>
                                    <p>户型：<?=$list['room']?></p>
                                    <p>价格：<?=strip_end_0($list['price'])?>万<?=$list['money_unit'];?></p>
                                    <p>使用面积：<?=$list['area']?>㎡</p>
                                    <p>交房时间：<?php echo date('Y-m-d',$list['pay_dateline']);?></p>
                                    <p>建筑设计：<?=$list['architects']?></p>
                                </dt>
                            </dl>
                            <dl class="oversea_dl4">
                                 <dd>xxx-xxx-xxx</dd>
                                 <dt><a href="javascript:void(0);" <?php if($group_id==2){?>onclick="openWin('js_report_pop');"<?php }else{?>onclick="permission_none();"<?php }?>>客户报备</a></dt>
                            </dl>
                        </span>

                    </div>
                </div>
            </div>

            <!--项目介绍-->
            <?php if ($is_bgy) { ?>
               <!--碧桂园城市花园 项目介绍-->
                <div class="oversea_con_part oversea_dl_f">
                    <span class="oversea_title oversea_dl_f">
                        <p class="addProject <?php if ($type == 1) {echo 'proCur';} ?>"><a href="<?=$base_url?>/1/">项目介绍</a></p>
                        <p class="addProject <?php if ($type == 2) {echo 'proCur';} ?>"><a href="<?=$base_url?>/2/">优惠信息</a></p>
                        <p class="addProject <?php if ($type == 3) {echo 'proCur';} ?>"><a href="<?=$base_url?>/3/">看房团行程</a></p>
                    </span>
                    <div class="oversea_list_con_detial oversea_dl_f">
                        <p class="oversea_list_detial_lef_p oversea_dl_f text_line">
                        <?php
                            if ($type == 1)
                            {
                                $this->load->view('/project/bgy/induce.php');
                            }
                            else if($type == 2)
                            {
                                $this->load->view('/project/bgy/discount.php');
                            }
                            else if ($type == 3)
                            {
                                $this->load->view('/project/bgy/trip.php');
                            }
                        ?>
                        </p>
                    </div>
                </div>
            <?php } else { ?>
                <div class="oversea_con_part oversea_dl_f">
                    <span class="oversea_title oversea_dl_f"><p>项目介绍</p></span>
                    <div class="oversea_list_con_detial oversea_dl_f">
                        <p class="oversea_list_detial_lef_p oversea_dl_f text_line"><?=$list['house_info']?></p>

                    </div>
                </div>
            <!--地理位置-->
            <div class="oversea_con_part oversea_dl_f">
                <span class="oversea_title oversea_dl_f"><p>地理位置</p></span>
                <div class="oversea_list_con_detial oversea_dl_f">
                    <p class="oversea_list_detial_lef_p oversea_dl_f text_line">地址：<?=$list['address']?></p>
                    <div class="oversea_list_detial_mess_r_map" id="map_canvas" style='width:900px;height:320px'>

                    </div>
                </div>
            </div>
            <!--区域简介-->
            <div class="oversea_con_part oversea_dl_f">
                <span class="oversea_title oversea_dl_f"><p>区域简介</p></span>
                <div class="oversea_list_con_detial oversea_dl_f L_ping">
                   <div class="oversea_list_detial_area_left" id="area_ind">
                       <ul>
						<?php
							if($list['city_pic_ids']){
								foreach($list['city_pic_ids'] as $val){
						?>
                           <li><img src="<?=$val?>" alt="" /></li>
						<?php }}else{?>
							<li><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/gallery/no_house.jpg" alt="" /></li>
						<?php }?>
                       </ul>
                       <div class="div_bg"></div>
                       <p class="div_bg_p">（1/2）图1</p>
                       <span class="div_bg_p_pre">&lt;</span>
                       <span class="div_bg_p_nex">&gt;</span>
                   </div>
                    <span class="oversea_list_detial_area_right">
                        <p>-<?=$list['city_info']?></p>
                        <!--<p>-为政府计划重点发展西南区域的中心地带 </p>
                        <p>-离最近的火车站5分钟路程</p>
                        <p>-距百年名校West</p>
                        <p>-墨尔本市中心西南区21公里</p>
                        <p>-为政府计划重点发展西南区域的中心地带 </p>
                        <p>-离最近的火车站5分钟路程</p>
                        <p>-距百年名校West</p>-->
                    </span>
                </div>
            </div>
            <!--项目配套-->
            <div class="oversea_con_part oversea_dl_f">
                <span class="oversea_title oversea_dl_f"><p>项目配套</p></span>
                <div class="oversea_list_con_detial oversea_dl_f L_ping" id="facility">
                    <div class="oversea_list_detial_area_left">
                        <ul>
						<?php if($list['project_pic_ids']){foreach($list['project_pic_ids'] as $val){?>
                            <li><img src="<?=$val?>" alt=""/></li>
						<?php }}else{?>
							<li><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/gallery/no_house.jpg" alt="" /></li>
						<?php }?>
                        </ul>
                        <div class="div_bg"></div>
                        <p class="div_bg_p">（1/2）图1</p>
                        <span class="div_bg_p_pre">&lt;</span>
                        <span class="div_bg_p_nex">&gt;</span>
                    </div>
                    <span class="oversea_list_detial_project_right">
                        <p><?=$list['house_support']?></p>
                        <!--<p>2.篮球/球赛半场</p>
                        <p>3.Boulderling /健身器材</p>
                        <p>4.开放的草地</p>
                        <p>5.篮球/球赛半场</p>
                        <p>6.Boulderling /健身器材</p>
                        <p>7.开放的草地</p>
                        <p>8.篮球/球赛半场</p>
                        <p>9.Boulderling /健身器材</p>
                        <p>10.开放的草地</p>
                        <p>11.篮球/球赛半场</p>-->

                    </span>
                </div>
            </div>
            <?php } ?>
        </span>
    </div>
</div>

<!--客户报备弹窗-->
<div class="sea_pop" style="display: none;" id="js_report_pop">
    <span class="sea_pop_title"><b style="float:left;width:auto;display:inline;">客户报备</b><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" style="float:right;color:#fff"></a></span>
    <div class="sea_pop_con">
        <li class="sea_color">请填写您的客户报备信息</li>
        <li>
            <dl class="sea_pop_con_dl">
                <dd>姓名：</dd>
                <dt><input type="text" class="sea_input" id="user_name"></dt>
            </dl>
            <dl class="errorBox" id="name_error" style="display: none;text-indent:3em;color:#ff0000;">请填写姓名</dl>
        </li>
        <li>
            <dl class="sea_pop_con_dl">
                <dd>电话：</dd>
                <dt><input type="text" class="sea_input" id="user_phone" onkeyup="value=value.replace(/[^\d-]/g,'')"></dt>
            </dl>
            <dl class="errorBox" id="phone_error" style="display: none;text-indent:3em;color:#ff0000;">请填写电话</dl>
        </li>
        <input type="hidden" id="house_id" value="<?=$house_id;?>">
        <li class="sea_margin sea_align"><a class="sea_btn_sub" href="javascript:void(0);" onclick="add_report();">立即报备</a>
    </li></div>
</div>
<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                        <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
                        <p class="left" style="font-size:14px;color:#666;">报备成功</p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>

<!--操作失败弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_false">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                        <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
                        <p class="left" style="font-size:14px;color:#666;">报备失败！</p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>

    <script type="text/javascript">
      $(function () {
      function reWidth() {

      var aLi_W = $(".oversea_list li").width()*0.98;
      var aBody_H = $(window).height();
      $(".abroad").css("height",(aBody_H-20)+"px");
      $(".abroad").css("overflow-y","auto");
      //alert(aBody_H);
      $(".oversea_list_detial").css("width", (aLi_W - 218) + "px");
      $(".oversea_list_detial_right").css("padding-top", (150 - $(".oversea_list_detial_right").height()) / 2 + "px");
      }
      reWidth();

      $(window).resize(function () {
      reWidth();
      })
      //图片轮播切换
      wLength(".oversea_list__small_img_items");//小图容器宽度赋值
      function Carousel() {
      var small_num_w = $(".oversea_list__small_img_items li").outerWidth();
      var small_num = 0;
      var big_num = 0;
      //点击小图切换大图
      $(".oversea_list__small_img_items li").on("click", function () {

      big_num = small_num = $(this).index();
      if ($(".oversea_list__small_img_items li").length < 5) {
                        if (big_num < $(".oversea_list__small_img_items li").length) {
                            big_num = big_num;
                        }
                        else {
                            big_num = 0;

                        }

                        small_num = 0;

                    } else {
                        Judge();
                    }
                    Img_action();
                })



                //左箭头切换方法
                $(".right_arrow").on("click", function () {
                    small_num++;
                    big_num++;
                    if ($(".oversea_list__small_img_items li").length < 5) {
                        if (big_num < $(".oversea_list__small_img_items li").length) {
                            big_num = big_num;
                        }
                        else {
                            big_num = 0;

                        }

                        small_num = 0;

                    } else {
                        Judge();
                    }
                    Img_action();

                })
                //右箭头切换
                $(".left_arrow").on("click", function () {
                    small_num--;
                    big_num--;
                    if (big_num < 0) {

                        if ($(".oversea_list__small_img_items li").length < 5) {
                            big_num = $(".oversea_list__small_img_items li").length - 1;
                            small_num = 0;
                        }
                        else {

                            big_num = $(".oversea_list__small_img_items li").length - 1;
                            small_num = $(".oversea_list__small_img_items li").length - 5;
                        }

                    }
                    else {
                        if (big_num < 5) {
                            small_num = 0;
                            big_num = big_num;
                        }
                        else if (big_num < ($(".oversea_list__small_img_items li").length - 5)) {
                            small_num = small_num;
                            big_num = big_num;
                        }
                        else{
                            big_num = big_num;
                            small_num = $(".oversea_list__small_img_items li").length - 5;
                        }

                    }

                    Img_action();

                })


                //动作执行
                function Img_action() {
                    $(".oversea_list__small_img_items ul").animate({ "margin-left": -(small_num_w * small_num) + "px" }, 300);
                    $(".oversea_list__small_img_items li").removeClass("show_small_img").eq(big_num).addClass("show_small_img");
                    //大图处理
                    $(".oversea_list__big_img li").fadeOut(200);
                    $(".oversea_list__big_img li").eq(big_num).fadeIn(300);

                };

                //当前位置判断
                function Judge() {

                    if (small_num < $(".oversea_list__small_img_items li").length) {

                        if (small_num > ($(".oversea_list__small_img_items li").length - 5)) {
                            big_num = (big_num < $(".oversea_list__small_img_items li").length) ? big_num : 0;
                            if (big_num == 0) {

                                small_num = 0;
                            }
                            else {
                                small_num = $(".oversea_list__small_img_items li").length - 5;
                            }

                        }
                        else {
                            small_num = small_num;
                            big_num = big_num;
                        }
                    }
                    else {

                        big_num = small_num = 0;
                    }
                }
            }
            Carousel()//调用


            //图片切换
            for (var i = 0; i < $(".oversea_list_detial_area_left").length; i++) {

                var obj_name = $(".oversea_list_detial_area_left").eq(i);

                wLength(obj_name);//小图容器宽度赋值
            }

            //左切换
            function img_tab(obj) {

                var this_j = $(obj);
                var img_num = 0;
                this_j.find(".div_bg_p").html("（" + (img_num + 1) + "/" + this_j.find("li").length + "）图" + (img_num + 1));
                var aimg_w = this_j.find("li").outerWidth();
                var aimg_text = this_j.find("div_bg_p");
                var a_tab_img_pre = this_j.find(".div_bg_p_pre");
                var a_tab_img_nex = this_j.find(".div_bg_p_nex");
                //左切换
                a_tab_img_nex.on("click", function () {
                    img_num++;
                    img_num = (img_num < this_j.find("li").length) ? img_num : 0;
                    this_j.find("ul").animate({ "margin-left": -img_num * aimg_w + "px" }, 300);
                    this_j.find(".div_bg_p").html("（" + (img_num+1) + "/" + this_j.find("li").length+ "）图" + (img_num+1));
                })
                //右切换
                a_tab_img_pre.on("click", function () {
                    img_num--;
                    img_num = (img_num < 0) ? (this_j.find("li").length-1) : img_num;
                    this_j.find("ul").animate({ "margin-left": -img_num * aimg_w + "px" }, 300);
                    this_j.find(".div_bg_p").html("（" + (img_num + 1) + "/" + this_j.find("li").length + "）图" + (img_num + 1));
                })

            }

            //右切换




            img_tab("#facility");
            img_tab("#area_ind");

            $(".sea_input").on('focus',function(){
                $(this).parent().parent().siblings('.errorBox').hide();
            })

        })
        //滚动图片获取
        function wLength(obj) {

            var aLi = $(obj).find("li").outerWidth();
            $(obj).find("ul").css("width", $(obj).find("li").length * aLi + "px");

        }

        function add_report(){
            var name = $('#user_name').val();
            var phone = $('#user_phone').val();
            var house_id = $('#house_id').val();
            var phonereg = /(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/;
            if(name ==''){
                $("#name_error").text('请填写姓名');
                $("#name_error").css('display','block');
                return false;
            }
            if(phone ==''){
                $("#phone_error").text('请填写电话号码');
                $("#phone_error").css('display','block');
                return false;
            }

            if(!phonereg.test(phone)){
                $("#phone_error").text('请正确填写电话号码');
                $("#phone_error").css('display','block');
                return false;
            }
            if(name && phone){
                $.ajax({
                    url:"/abroad_report/add",
                    type:"post",
                    dataType:"json",
                    data:{
                       name:name,
                       phone:phone,
                       house_id:house_id
                    },
                    success: function(data){
                        if(data['result']==1){
                            closeWindowWin('js_report_pop');
                            openWin('js_pop_success');
                        }else{
                            openWin('js_pop_false');
                        }
                    }
                })
            }
        }
    </script>
