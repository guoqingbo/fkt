<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.5&ak=s4xTcbCABxjTGG3EfdZpQxaT"></script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
	<a href="/tourism/index" class="btn-lv" style="float:right; margin-right:10px;"><span>&lt;&lt;返回地产列表</span></a>
</div>
<div class='tourism' style='width:100%'>
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
                            <span class="sea_small_pre left left_arrow"><!--向左箭头--></span>
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
                            <h2 class="oversea_dl_f oversea_list_detial_mess_r_h2 ">[<?=$list['city_name']?>]<?=$list['block_name']?></h2>

                            <dl class="oversea_dl2 sea_padd_T">
                                <dd>均价：<b><?=intval($list['avg_price'])?>元/m²</b></dd>
                                <!--<dt>佣金：<b><?=$list['brokerage']?><?php if($list['brokerage_type'] == 1){?>元<?php }else{?>成交价<?php }?></b></dt>-->
                            </dl>
                            <span class="oversea_visit">
                                <p>楼盘地址：<?=$list['address']?></p>
								<?php if($huxing_list){?>
                                <p>户　　型：
									<?php
										foreach($huxing_list as $key=>$val){
											if($key <= 2){
									?>
									<?php echo $val['room'].'室'.$val['hall'].'厅'?>
									<?php }}?>
								</p>
								<?php }?>
							<?php if($list['small_area'] != $list['big_area']){?>
                                <p>面　　积：<?=$list['small_area']?>-<?=$list['big_area']?>平米</p>
                                <p>价　　格：<?=round(($list['small_area']*$list['avg_price'])/10000)?> - <?=round(($list['big_area']*$list['avg_price'])/10000)?>万</p>
							<?php }else{?>
								<p>面　　积：<?=$list['small_area']?>平米</p>
                                <p>价　　格：<?=round(($list['small_area']*$list['avg_price'])/10000)?>万</p>
							<?php }?>
                                <p>交房时间：<?php echo date('Y-m-d
								',$list['deliver_dateline'])?></p>
                            </span>
                            <dl class="oversea_dl4">
                                 <dd style="width: 43%">xxx-xxx-xxx-xx</dd>
                                 <dt><a href="javascript:void(0);" <?php if($group_id==2){?>onclick="openWin('js_report_pop');"<?php }else{?>onclick="permission_none('您的帐号尚未认证');"<?php }?>>客户报备</a></dt>
                            </dl>
                        </span>

                    </div>
                </div>
            </div>

            <!--项目介绍-->
            <div class="oversea_con_part oversea_dl_f">
                <span class="oversea_title oversea_dl_f"><p>项目介绍</p></span>
                <div class="oversea_list_con_detial oversea_dl_f">
                    <p class="oversea_list_detial_lef_p oversea_dl_f text_line"><?=$list['house_info']?></p>

                </div>
            </div>

            <!--在售户型-->
            <div class="oversea_con_part oversea_dl_f">
                <span class="oversea_title oversea_dl_f"><p>在售户型</p></span>
                <div class="oversea_list_con_detial oversea_dl_f">
				<?php
					if($room_list){
						foreach($room_list as $key=>$val){
				?>
                    <!--一居室-->
					<?php if($key == 1){?>
                    <div class="visit_sell">
                         <span class="visit_sell_left sea_padd_T ">
                             <table class="oversea_table ">
                                 <tr>
                                     <td class="td_color1 sea_bold sea_font text_line">1居室</td>
                                     <td class="td_color2 text_line"><b>建筑面积</b><br/>
									 <?php if($room_build_list[1]['small'] != $room_build_list[1]['big']){?>
										<?=$room_build_list[1]['small'];?>-<?=$room_build_list[1]['big']?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[1]['small']*$list['avg_price'])/10000);?>万-<?=round(($room_build_list[1]['big']*$list['avg_price'])/10000);?>万</td>
									 <?php }else{?>
										<?=$room_build_list[1]['small'];?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[1]['small']*$list['avg_price'])/10000);?>万</td>
									<?php }?>

                                 </tr>
                             </table>
                         </span>
                        <!--户型展示-->

                        <span class="visit_sell_right " id="one_room">
                            <span class="visit_sell_btn sea_type_pre"><!--左箭头--></span>
                            <div class="visit_sell_type_list">
                                <ul>
								<?php foreach($val as $v){?>
                                    <li>
                                        <img src="<?=changepic($v['huxing_pic'])?>" alt="" />
                                        <p>
                                            <b><?=$v['room']?>室<?=$v['hall']?>厅<?=$v['toilet']?>卫</b>
                                            <b><?=$v['area']?>m²</b>
                                            <b>约<?php echo round(($v['area']*$list['avg_price'])/10000);?>万元</b>
                                        </p>
                                    </li>
								<?php }?>
                                </ul>
                            </div>
                            <span class="visit_sell_btn sea_type_nex"><!--右箭头--></span>
                        </span>

                    </div>
					<?php }elseif($key == 2){?>
                    <!--二居室-->
                    <div class="visit_sell">
                        <span class="visit_sell_left sea_padd_T ">
                            <table class="oversea_table ">
                                <tr>
                                    <td class="td_color1 sea_bold sea_font text_line">2居室</td>
                                    <td class="td_color2 text_line"><b>建筑面积</b><br />
									 <?php if($room_build_list[2]['small'] != $room_build_list[2]['big']){?>
										<?=$room_build_list[2]['small'];?>-<?=$room_build_list[2]['big']?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[2]['small']*$list['avg_price'])/10000);?>万-<?=round(($room_build_list[2]['big']*$list['avg_price'])/10000);?>万</td>
									 <?php }else{?>
										<?=$room_build_list[2]['small'];?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[1]['small']*$list['avg_price'])/10000);?>万</td>
									<?php }?>

                                </tr>
                            </table>
                        </span>
                        <!--户型展示-->

                        <span class="visit_sell_right " id="two_room">
                            <span class="visit_sell_btn sea_type_pre"><!--左箭头--></span>
                            <div class="visit_sell_type_list">
                                <ul>
								<?php foreach($room_list['2'] as $v){?>
                                    <li>
                                        <img src="<?=changepic($v['huxing_pic'])?>" alt="" />
                                        <p>
                                            <b><?=$v['room']?>室<?=$v['hall']?>厅<?=$v['toilet']?>卫</b>
                                            <b><?=$v['area']?>m²</b>
                                            <b>约<?php echo round(($v['area']*$list['avg_price'])/10000);?>万元</b>
                                        </p>
                                    </li>
								<?php }?>
                                </ul>
                            </div>
                            <span class="visit_sell_btn sea_type_nex"><!--右箭头--></span>
                        </span>

                    </div>
					<?php }elseif($key == 3){?>
                    <!--三居室-->
                    <div class="visit_sell">
                        <span class="visit_sell_left sea_padd_T ">
                            <table class="oversea_table ">
                                <tr>
                                    <td class="td_color1 sea_bold sea_font text_line">3居室</td>
                                    <td class="td_color2 text_line"><b>建筑面积</b><br />
									<?php if($room_build_list[3]['small'] != $room_build_list[3]['big']){?>
										<?=$room_build_list[3]['small'];?>-<?=$room_build_list[3]['big']?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[3]['small']*$list['avg_price'])/10000);?>万-<?=round(($room_build_list[3]['big']*$list['avg_price'])/10000);?>万</td>
									 <?php }else{?>
										<?=$room_build_list[3]['small'];?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[3]['small']*$list['avg_price'])/10000);?>万</td>
									<?php }?>
                                </tr>
                            </table>
                        </span>
                        <!--户型展示-->

                        <span class="visit_sell_right" id="three_room">
                            <span class="visit_sell_btn sea_type_pre"><!--左箭头--></span>
                            <div class="visit_sell_type_list">
                                <ul>
								<?php foreach($room_list['3'] as $v){?>
                                    <li>
                                        <img src="<?=changepic($v['huxing_pic'])?>" alt="" />
                                        <p>
                                            <b><?=$v['room']?>室<?=$v['hall']?>厅<?=$v['toilet']?>卫</b>
                                            <b><?=$v['area']?>m²</b>
                                            <b>约<?php echo round(($v['area']*$list['avg_price'])/10000);?>万元</b>
                                        </p>
                                    </li>
								<?php }?>
                                </ul>
                            </div>
                            <span class="visit_sell_btn sea_type_nex"><!--右箭头--></span>
                        </span>

                    </div>
					<?php }elseif($key == 4){ ?>
                    <!--四居室-->
                    <div class="visit_sell">
                        <span class="visit_sell_left sea_padd_T ">
                            <table class="oversea_table ">
                                <tr>
                                    <td class="td_color1 sea_bold sea_font text_line">4居室</td>
                                    <td class="td_color2 text_line"><b>建筑面积</b><br />
									 <?php if($room_build_list[4]['small'] != $room_build_list[4]['big']){?>
										<?=$room_build_list[4]['small'];?>-<?=$room_build_list[4]['big']?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[4]['small']*$list['avg_price'])/10000);?>万-<?=round(($room_build_list[4]['big']*$list['avg_price'])/10000);?>万</td>
									 <?php }else{?>
										<?=$room_build_list[4]['small'];?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[4]['small']*$list['avg_price'])/10000);?>万</td>
									<?php }?>
                                </tr>
                            </table>
                        </span>
                        <!--户型展示-->

                        <span class="visit_sell_right" id="four_room">
                            <span class="visit_sell_btn sea_type_pre"><!--左箭头--></span>
                            <div class="visit_sell_type_list">
                                <ul>
								<?php foreach($room_list['4'] as $v){?>
                                    <li>
                                       <img src="<?=changepic($v['huxing_pic'])?>" alt="" />
                                        <p>
                                            <b><?=$v['room']?>室<?=$v['hall']?>厅<?=$v['toilet']?>卫</b>
                                            <b><?=$v['area']?>m²</b>
                                            <b>约<?php echo round(($v['area']*$list['avg_price'])/10000);?>万元</b>
                                        </p>
                                    </li>
								<?php }?>
                                </ul>
                            </div>
                            <span class="visit_sell_btn sea_type_nex"><!--右箭头--></span>

                        </span>

                    </div>
					<?php }elseif($key == 5){?>
					<!--五居室-->
                    <div class="visit_sell">
                        <span class="visit_sell_left sea_padd_T ">
                            <table class="oversea_table ">
                                <tr>
                                    <td class="td_color1 sea_bold sea_font text_line">5居室</td>
                                    <td class="td_color2 text_line"><b>建筑面积</b><br />
									 <?php if($room_build_list[5]['small'] != $room_build_list[5]['big']){?>
										<?=$room_build_list[5]['small'];?>-<?=$room_build_list[5]['big']?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[5]['small']*$list['avg_price'])/10000);?>万-<?=round(($room_build_list[5]['big']*$list['avg_price'])/10000);?>万</td>
									 <?php }else{?>
										<?=$room_build_list[5]['small'];?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[5]['small']*$list['avg_price'])/10000);?>万</td>
									<?php }?>
                                </tr>
                            </table>
                        </span>
                        <!--户型展示-->

                        <span class="visit_sell_right" id="four_room">
                            <span class="visit_sell_btn sea_type_pre"><!--左箭头--></span>
                            <div class="visit_sell_type_list">
                                <ul>
								<?php foreach($room_list['5'] as $v){?>
                                    <li>
                                       <img src="<?=changepic($v['huxing_pic'])?>" alt="" />
                                        <p>
                                            <b><?=$v['room']?>室<?=$v['hall']?>厅<?=$v['toilet']?>卫</b>
                                            <b><?=$v['area']?>m²</b>
                                            <b>约<?php echo round(($v['area']*$list['avg_price'])/10000);?>万元</b>
                                        </p>
                                    </li>
								<?php }?>
                                </ul>
                            </div>
                            <span class="visit_sell_btn sea_type_nex"><!--右箭头--></span>

                        </span>

                    </div>
					<?php }elseif($key == 6){?>
					<!--六居室-->
                    <div class="visit_sell">
                        <span class="visit_sell_left sea_padd_T ">
                            <table class="oversea_table ">
                                <tr>
                                    <td class="td_color1 sea_bold sea_font text_line">6居室</td>
                                    <td class="td_color2 text_line"><b>建筑面积</b><br />
									<?php if($room_build_list[6]['small'] != $room_build_list[6]['big']){?>
										<?=$room_build_list[6]['small'];?>-<?=$room_build_list[6]['big']?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[6]['small']*$list['avg_price'])/10000);?>万-<?=round(($room_build_list[6]['big']*$list['avg_price'])/10000);?>万</td>
									 <?php }else{?>
										<?=$room_build_list[6]['small'];?>
										平方</td>
										<td class="td_color2 text_line"><b>总价</b><br /><?=round(($room_build_list[6]['small']*$list['avg_price'])/10000);?>万</td>
									<?php }?>
                                </tr>
                            </table>
                        </span>
                        <!--户型展示-->

                        <span class="visit_sell_right" id="four_room">
                            <span class="visit_sell_btn sea_type_pre"><!--左箭头--></span>
                            <div class="visit_sell_type_list">
                                <ul>
								<?php foreach($room_list['6'] as $v){?>
                                    <li>
                                       <img src="<?=changepic($v['huxing_pic'])?>" alt="" />
                                        <p>
                                            <b><?=$v['room']?>室<?=$v['hall']?>厅<?=$v['toilet']?>卫</b>
                                            <b><?=$v['area']?>m²</b>
                                            <b>约<?php echo round(($v['area']*$list['avg_price'])/10000);?>万元</b>
                                        </p>
                                    </li>
								<?php }?>
                                </ul>
                            </div>
                            <span class="visit_sell_btn sea_type_nex"><!--右箭头--></span>

                        </span>

                    </div>
					<?php }?>
				<?php }}?>
                </div>
            </div>

            <!--基本信息 -->
            <div class="oversea_con_part oversea_dl_f">
                <span class="oversea_title oversea_dl_f"><p>基本信息 </p></span>
                <div class="oversea_list_con_detial oversea_dl_f L_ping">
                    <span class="visit_sell_base">
                      <div class="visit_sell_base_info">
                        <dl>
                          <dd>楼盘位置：</dd>
                          <dt>
                            <?=$list['address']?>
                          </dt>
                        </dl>
                          <dl>
                            <dd>建筑面积：</dd>
                            <dt><?=$list['buildarea']?>平方米 </dt>
                        </dl>
                      </div>
                      <div class="visit_sell_base_info">
                        <dl>
                          <dd>户    数：</dd>
                          <dt>
                            <?=$list['total_room']?>
                          </dt>
                        </dl>
                        <dl>
                          <dd>户型面积：</dd>
                          <dt>
                            <?=$list['small_area']?>-<?=$list['big_area']?>平米
                          </dt>
                        </dl>
                      </div>
                      <div class="visit_sell_base_info">
                        <dl>
                          <dd>道路交通：</dd>
                          <dt>
                            <?=$list['traffic_info']?>
                          </dt>
                        </dl>
                        <dl>
                          <dd>绿化率：</dd>
                          <dt>
                            <?=$list['green_rate']?>%
                          </dt>
                        </dl>
                      </div>


                        <!--<dl>
                            <dd>楼盘位置：</dd>
                            <dt><?=$list['address']?> </dt>
                        </dl>
                        <dl>
                            <dd>户    数：</dd>
                            <dt><?=$list['total_room']?>  </dt>
                        </dl>
                        <dl>
                            <dd>道路交通：</dd>
                            <dt><?=$list['traffic_info']?> </dt>
                        </dl>

                        <dl>
                            <dd>建筑面积：</dd>
                            <dt><?=$list['buildarea']?>平方米 </dt>
                        </dl>
                        <dl>
                            <dd>户型面积：</dd>
                            <dt><?=$list['small_area']?>-<?=$list['big_area']?>平米  </dt>
                        </dl>-->

                    </span>
                </div>
            </div>
            <!--周边配套-->
            <div class="oversea_con_part oversea_dl_f">
                <span class="oversea_title oversea_dl_f"><p>周边配套</p></span>
                <div class="oversea_list_con_detial oversea_dl_f L_ping">
                    <!--附近内容地图展示-->
                    <span class="visit_sell_around_things_map map_wrap" id='map_wrap'>

                    </span>
                    <!--配套设施-->
                    <div class="visit_sell_around_things_list">
                        <!--tab-->
                        <span class="visit_sell_tab tab_id top_option">
                            <p class="tabOn"><a href="javascript:void(0);" class="checked fl" onclick="select_map('交通')" style="cursor:pointer">交通</a></p>
                            <p><a href="javascript:void(0);" class="fl" onclick="select_map('学校')" style="cursor:pointer">教育</a></p>
                            <p><a href="javascript:void(0);" class="fl" onclick="select_map('生活')" style="cursor:pointer">生活</a></p>
                            <p><a href="javascript:void(0);" class="fl" onclick="select_map('医院')" style="cursor:pointer">健康</a></p>
                            <p><a href="javascript:void(0);" class="fl" onclick="select_map('餐饮')" style="cursor:pointer">餐饮</a></p>
                        </span>
						<div class="r-result"><h3></h3><div id="r-result" style="border:none;"></div></div>
                    </div>

                </div>
            </div>
			<script type="text/javascript" src="<?php echo MLS_SOURCE_URL;?>/mls/web/js/overlay.js"></script>
			<script type="text/javascript">
			//地图标签选中效果
			var $map_option = $(".top_option").eq(0);
			$map_option.find("p").click(function () {
				$map_option.find("p").removeClass("tabOn");
				$(this).addClass("tabOn");
			});


			var lng = "<?php echo $list['b_map_x'];?>";
			var lat = "<?php echo $list['b_map_y'];?>";
			var cmt_name = "<?php echo $list['block_name'];?>";
			var map = new BMap.Map("map_wrap");          // 创建地图实例
			var point = new BMap.Point(lng, lat);  // 创建点坐标
			map.centerAndZoom(point, 15);                 // 初始化地图，设置中心点坐标和地图级别
			map.enableScrollWheelZoom();				//启用滚轮放大缩小
			var html = '<table border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="blockleft"><div>' + cmt_name + '</div></td><td class="blockright">&nbsp;</td></tr></tbody></table>';
			var myOverlay = new DeOverlayC(point, html);
			map.addOverlay(myOverlay);
			//var marker = new BMap.Marker(point);        // 创建标注
			//map.addOverlay(marker);
			map.addControl(new BMap.NavigationControl());
			map.addControl(new BMap.ScaleControl());
			map.addControl(new BMap.OverviewMapControl());
			map.addControl(new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP,BMAP_SATELLITE_MAP,BMAP_HYBRID_MAP ]}));
			//var circle = new BMap.Circle(point,3000,{fillColor:"blue", strokeWeight: 1 ,fillOpacity: 0.3, strokeOpacity: 0.3});
			//map.addOverlay(circle);
			/*var options = {
				onSearchComplete: function(results){
					// 判断状态是否正确
					if (local.getStatus() == BMAP_STATUS_SUCCESS){
						var s = [];
						for (var i = 0; i < results.getCurrentNumPois(); i ++){
							s.push(results.getPoi(i).title + ", " + results.getPoi(i).address);
						}
						document.getElementById("r-result").innerHTML = s.join("<br/>");
					}
				}
			};*/
			var local = new BMap.LocalSearch(map, {renderOptions: {map: map, panel: "r-result"}});
			//var local =  new BMap.LocalSearch(map, {renderOptions: {map: map, autoViewport: false}});
			//var local =  new BMap.LocalSearch(map, options);
			local.searchNearby('公交站',point);
			function select_map(content){
				//$(".r-result h3").html(content);
				local.searchNearby(content,point);
			}
		</script>
        </span>
    </div>
</div>
<!--客户报备弹窗-->
<div class="sea_pop" style="display: none;" id="js_report_pop">
     <span class="sea_pop_title">
      <b style="float:left;width:auto;display:inline;">客户报备</b><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" style="float:right;color:#fff"></a></span>
    <div class="sea_pop_con">
        <li class="sea_color">请填写您的客户报备信息</li>
        <li>
            <dl class="sea_pop_con_dl">
                <dd>姓名：</dd>
                <dt><input type="text" class="sea_input" id="user_name"></dt>
            </dl>
            <dl class="errorBox" id="name_error" style="display: none">请填写姓名</dl>
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
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;text-indent:3em;color:#ff0000;" id="js_pop_success">
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
      var aBody_H = $(window).height()-50;
      $(".tourism").css("height",aBody_H+"px");
      $(".tourism").css("overflow-y","auto");
      //console.log(aLi_W);
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

      $(".sea_input").on('focus',function(){
            $(this).parent().parent().siblings('.errorBox').hide();
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

            /**选项卡**/
            $(".tab_id p").click(function () {
                $(".tab_list").hide();
                $(".tab_id p").removeClass("tabOn");
                $(this).addClass("tabOn");
                $(".tab_list").eq($(this).index()).show();
                $(".visit_sell_around_things_map img").hide();
                $(".visit_sell_around_things_map img").eq($(this).index()).show();
            })

            //循环赋值
            for (var i = 0 ; i < $(".visit_sell_type_list").length; i++) {
                var temp = $(".visit_sell_type_list").eq(i);
                wLength(temp);


            }

            //图片切换
            function scroll_img(ID, num_index) {

                var num_index = num_index;//默认起始位置
                var ID = $(ID); //容器名
                var ID_pre = ID.find(".sea_type_pre");//获取左箭头
                var ID_nex = ID.find(".sea_type_nex");//获取左箭头
                var ID_ul = ID.find("ul");//获取要循环的容器名
                var ID_li = ID.find("li").length;//获取要循环的内容数量
                var ID_li_w = ID.find("li").outerWidth();//获取要循环的内容宽度
                //左切换函数
                ID_nex.on("click", function () {
                  if(num_index < 3){

                    num_index = 0;

                }else{
                  if (num_index < (ID_li - 3)) {
                        num_index++;
                    }
                    else {
                        num_index = ID_li - 3;
                    }

                    }


                    ID_ul.animate({ "margin-left": -ID_li_w * num_index + "px" }, 300);
                    //alert(num_index);

                })

                //右切换函数
                ID_pre.on("click", function () {


                      if(num_index < 3 ){
                        num_index = 0;
                      }
                      else{
                           if (num_index < 1) {
                                num_index = 0;
                            }
                            else {
                                num_index--;
                            }

                        }


                    ID_ul.animate({ "margin-left": -ID_li_w * num_index + "px" }, 300);
                    //alert(num_index);
                })

            }

            //户型切换
            scroll_img("#one_room",0);
            scroll_img("#two_room",0);
            scroll_img("#three_room",0);
            scroll_img("#four_room",0);

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
                    url:"/tourism_report/add",
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
