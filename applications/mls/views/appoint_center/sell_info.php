<div class="pop_box_g" id="js_pop_box_g"  style="display:block; border:none">
    <div class="hd">
        <div class="title">房源详情</div>
        <div class="close_pop"></div>
    </div>
    <div class="mod">
        <div class="tab_pop_hd">
            <div class="clearfix">
                <?php if($is_pub == 1){?>
                <a class=" item <?php if($tab == 1) { ?> itemOn <?php }?>" href="/sell/details_house/<?php echo $data_info['id'];?>/1/1/<?php echo $hide_btn;?>">房源详情</a>
                <a class=" item <?php if($tab == 2) { ?> itemOn <?php }?>" href="/sell/details_secret/<?php echo $data_info['id'];?>/1/2/<?php echo $hide_btn;?>">保密信息</a>
                <a class=" item <?php if($tab == 3) { ?> itemOn <?php }?>" href="/sell/details_image/<?php echo $data_info['id'];?>/1/3/<?php echo $hide_btn;?>">房源图片</a>
                <?php if($xiaoquflag == 1){?>
                <a class=" item <?php if($tab == 4) { ?> itemOn <?php }?>" href="/sell/details_district/<?php echo $data_info['id'];?>/1/4/<?php echo $hide_btn;?>">小区概况</a>
                <?php }?>
				<?php }?>
                <?php if($is_pub == 2 || $is_pub == 3){?>
				<a class=" item <?php if($tab == 1) { ?> itemOn <?php }?>" href="/sell/details_house/<?php echo $data_info['id'];?>/<?php echo $is_pub;?>/1">房源详情</a>
                <a class=" item <?php if($tab == 3) { ?> itemOn <?php }?>" href="/sell/details_image/<?php echo $data_info['id'];?>/<?php echo $is_pub;?>/3">房源图片</a>
                <?php if($xiaoquflag == 1){?>
                <a class=" item <?php if($tab == 4) { ?> itemOn <?php }?>" href="/sell/details_district/<?php echo $data_info['id'];?>/<?php echo $is_pub;?>/4">小区概况</a>
                <?php }?>
                <?php }?>

				<?php if($xiaoqumapflag == 1){?>
                <a class=" item <?php if($tab == 6) { ?> itemOn <?php }?>" href="/sell/details_map/<?php echo $data_info['id'];?>/<?php echo $is_pub;?>/6">小区地图</a>
                <?php }?>
                <?php if($is_pub == 1){?>
				 <a class=" item <?php if($tab == 5) { ?> itemOn <?php }?>" href="/sell/details_hezuo/<?php echo $data_info['id'];?>/1/5/<?php echo $hide_btn;?>">合作统计</a>
                <?php }?>
            </div>
        </div>
        <div class="tab_pop_mod clear" id="js_tab_b01" <?php if($tab==6){?>style="overflow:auto;"<?php } ?>>
            <?php if($tab==1){?>
            <div class="js_d inner" style="display:block;">
			<?php $type = '暂不售（租）';?>
				<!--span class="xsh xsh-1">悬赏</span-->
                <table class="table">
					<tr>
						<td class="w70 t_l">房源编号：</td>
                        <td class="w170">CS<?php echo $data_info['id']; ?></td>
						<td class="w70 t_l">状态：</td>
                        <td class="w170" style="color:#F75000">
							<?php echo $config['status'][$data_info['status']] == $type?'暂不售' : $config['status'][$data_info['status']]; ?>
						</td>
                        <td class="w70 t_l">物业类型：</td>
                        <td class="w170" style="color:#F75000"><?php if($data_info['sell_type']){ echo $config['sell_type'][$data_info['sell_type']];} ?></td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">楼盘名称：</td>
                        <td class="w170" style="color:#F75000"><?php echo $data_info['block_name'];?></td>
                        <td class="w70 t_l" >区属：</td>
                        <td class="w170" style="color:#F75000"><?php echo $data_info['district_name'];?></td>
                        <td class="w70 t_l">板块：</td>
                        <td style="color:#F75000"><?php echo $data_info['street_name'];?></td>
                    </tr>

                    <tr>
					     <td class="w70 t_l">地址：</td>
                        <td class="w170"><?php echo $data_info['address'];?></td>
						 <td class="w70 t_l">楼层：</td>
                        <td class="w170">
                            <!--房源列表详情和合作列表房源详情分离
                            -->
                            <?php if(1==$is_pub){
                                echo $data_info['floor'];if($data_info['floor_type'] == 2){ echo "-".$data_info['subfloor'];} echo '/';echo $data_info['totalfloor'];
                             }else{
                                $floor_str = '';
                                if(!empty($data_info['totalfloor'])){
                                    $floor_rate = $data_info['floor']/$data_info['totalfloor'];
                                }else{
                                    $floor_rate = 0;
                                }
                                if($floor_rate<0.4){
                                    $floor_str = '低楼层';
                                }else if(($floor_rate>0.4 && $floor_rate<0.7) || $floor_rate==0.4 || $floor_rate==0.7){
                                    $floor_str = '中楼层';
                                }else {
                                    $floor_str = '高楼层';
                                }
                                //厂房、仓库、车库、别墅类型，都是低楼层
                                if('2'==$data_info['sell_type'] || '5'==$data_info['sell_type'] || '6'==$data_info['sell_type'] || '7'==$data_info['sell_type']){
                                    $floor_str = '低楼层';
                                }
                                echo $floor_str; echo '/';echo $data_info['totalfloor'];

                             }?>
                        </td>
						 <td class="w70 t_l">朝向：</td>
                        <td><?php echo $config['forward'][$data_info['forward']]; ?></td>

                    </tr>
                    <tr>
                        <td class="w70 t_l">装修：</td>
                        <td class="w170" style="color:#F75000"><?php echo $config['fitment'][$data_info['fitment']]; ?></td>
                        <td class="w70 t_l">房龄：</td>
                        <td class="w170" style="color:#F75000"><?php echo $data_info['buildyear']; ?>年</td>
                        <?php if($data_info['sell_type'] <= 2) {?>
                        <td class="w70 t_l">户型：</td>
                        <td class="w170" style="color:#F75000"><?php echo $data_info['room'];?>室<?php echo $data_info['hall'];?>厅<?php echo $data_info['toilet'];?>卫<?php echo $data_info['kitchen'];?>厨<?php echo $data_info['balcony'];?>阳台</td>
                        <?php }else{ ?>
                        <td class="w70 t_l"></td>
                        <td class="w170" style="color:#F75000"></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td class="w70 t_l">售价：</td>
                        <td class="w170" style="color:#F75000"><?php echo strip_end_0($data_info['price']); ?>万元</td>
                        <td class="w70 t_l">面积：</td>
                        <td style="color:#F75000"><?php echo strip_end_0($data_info['buildarea']); ?>平方米</td>
						<td class="w70 t_l">单价：</td>
                        <td class="w170"><?php echo strip_end_0($data_info['avgprice']); ?>元/平米</td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">税费：</td>
                        <td style="color:#F75000">
                        <?php if($data_info['taxes']){echo $config['taxes'][$data_info['taxes']];}?>
				        </td>
                        <td class="w70 t_l">钥匙编号：</td>
                        <td style="color:#F75000"><?php if($data_info['keys']){echo $data_info['key_number'];}else{echo "无";} ?></td>
						<?php if(1==$is_pub){?>
                        <td class="w70 t_l">产权：</td>
                        <td class="w170">
                        <?php
						if($data_info['property']){
							echo $config['property'][$data_info['property']];
						}
						?>
						</td>
                        <?php } ?>
                    </tr>
					<tr>
                        <td class="w70 t_l">委托类型：</td>
                        <td class="w170" style="color:#F75000"><?php echo $config['entrust'][$data_info['entrust']]; ?></td>
						<?php if(1==$is_pub){?>
                        <td class="w70 t_l">类型：</td>
                        <td>
                        <?php
                        if($data_info['sell_type'] == 2 && $data_info['villa_type']){
                            echo $config['villa_type'][$data_info['villa_type']];
                        }elseif($data_info['sell_type'] == 3 && $data_info['shop_type']){
                            echo $config['shop_type'][$data_info['shop_type']];
                        }elseif($data_info['sell_type'] == 4 && $data_info['office_type']){
                            echo $config['office_type'][$data_info['office_type']];
                        }elseif($data_info['house_type'] && $data_info['house_type']){
                            echo $config['house_type'][$data_info['house_type']];
                        }
                        ?>
                        </td>
						  <td class="w70 t_l">物业费：</td>
                        <td class="w170">
                        <?php
						if($data_info['strata_fee'] > 0){
							echo strip_end_0($data_info['strata_fee']);
                            echo "元/平方米·月";
						}?>
                        </td>
                        <?php }?>
					</tr>
					<tr>
					 <td class="w70 t_l">是否合作：</td>
                        <td class="w170" style="color:#F75000">
                        <?php echo $data_info['isshare'] == 1 ? "是":"否";?>
                        </td>
						<?php if($data_info['isshare']==1){?>
						<tr>
                        <td colspan="6">
                            <div class="share">
                                 <table>
									<tr class="tr-one">
										<td width="120"><strong>房源合作佣金分配</strong></td>
										<td>甲方可获得本次交易双方佣金总金额&nbsp;<strong class="f60 f14">50%</strong><em>|</em>乙方可获得本次交易双方佣金总金额&nbsp;<strong class="f60 f14">50%</strong>
										<p class="b8b7b7">注：此佣金分配方案仅做参考，具体佣金方案需经纪人线下商定</p></td>
									</tr>
                                    <!--
									<tr>
										<td><strong>房源合作悬赏赏金</strong></td>
										<td><strong class="f60 f14">¥1000 元</strong> （房源成交价的1%）
										<p class="b8b7b7">注：此佣金分配方案仅做参考，具体佣金方案需经纪人线下商定</p></td>
									</tr>
                                    -->
								</table>
                                <div class="arrow"></div>
                            </div>
                        </td>
                    </tr>

						<?php }?>
						<td class="w70 t_l">房源性质：</td>
                        <td style="color:#F75000"><?php echo $config['nature'][$data_info['nature']]; ?></td>
                        <?php if(1==$is_pub){?>
						<td class="w70 t_l">信息来源：</td>
						<td class="w170">
						<?php
						if($data_info['infofrom']){
							echo $config['infofrom'][$data_info['infofrom']];
						}
						?>
						</td>
                        <?php }?>
					</tr>
                    <?php if(1==$is_pub){ ?>
                    <tr>
						<td class="w70 t_l">房源等级：</td>
                        <td><?php echo $config['house_grade'][$data_info['house_grade']]; ?></td>
						<td class="w70 t_l">房屋结构：</td>
                        <td><?php echo $config['house_structure'][$data_info['house_structure']]; ?></td>
						<td class="w70 t_l">看房时间：</td>
                        <td><?php echo $config['read_time'][$data_info['read_time']]; ?></td>
                    </tr>
                    <?php }?>
                    <?php if($data_info['sell_type'] == 2){ ?>
                    <tr>
                        <td class="w70 t_l">厅结构：</td>
                        <td class="w170"><?php
						if($data_info['hall_struct']){
							echo $config['hall_struct'][$data_info['hall_struct']];
						}
						?></td>
                        <td class="w70 t_l">地下面积：</td>
                        <td class="w170">
                        <?php
						if(strip_end_0($data_info['floor_area'])){
							echo strip_end_0($data_info['floor_area']).'平方米 ';
						}
    				    if($data_info['light_type']){
				            echo $config['light_type'][$data_info['light_type']];
    					}
						?>
						</td>
                        <td class="w70 t_l">花园面积：</td>
                        <td>
                        <?php
                        if(strip_end_0($data_info['garden_area'])){
                            echo strip_end_0($data_info['garden_area']).'平方米';
                        }
                        ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(1==$is_pub){?>
                    <tr>
                        <td class="w70 t_l">现状：</td>
                        <td class="w170">
                        <?php
						if($data_info['current']){
							echo $config['current'][$data_info['current']];
						}
						?>
						</td>
                        <?php if($data_info['sell_type'] == 2){ ?>
                        <td class="w70 t_l">车位数量：</td>
                        <td class="w170">
                        <?php
                        if($data_info['park_num']){
                            echo $data_info['park_num'].'个';
                        }
                        ?>
                        </td>
                        <td class="w70 t_l">&nbsp;</td>
                        <td>&nbsp;</td>
                        <?php }elseif($data_info['sell_type'] == 4){ ?>
                        <td class="w70 t_l">可分割：</td>
                        <td class="w170"><?php if($data_info['division']==1){echo "是";}elseif($data_info['division']==2){echo "否";} ?></td>
                        <td class="w70 t_l">级别：</td>
                        <td>
                        <?php
						if($data_info['office_trade']){
							echo $config['office_trade'][$data_info['office_trade']];
						}
						?>
						</td>
                        <?php }elseif($data_info['sell_type'] == 3){ ?>
                        <td class="w70 t_l">可分割：</td>
                        <td class="w170"><?php if($data_info['division']==1){echo "是";}elseif($data_info['division']==2){echo "否";} ?></td>
                        <td class="w70 t_l">&nbsp;</td>
                        <td>&nbsp;</td>
                        <?php }else{ ?>
                        <td class="w70 t_l">&nbsp;</td>
                        <td class="w170">&nbsp;</td>
                        <td class="w70 t_l">&nbsp;</td>
                        <td>&nbsp;</td>
                        <?php } ?>
                    </tr>
                    <?php }else{
                        if($data_info['sell_type']==3 || $data_info['sell_type']==4 || $data_info['sell_type']==2){
                    ?>
                    <tr>
                        <?php if($data_info['sell_type'] == 2){ ?>
                        <td class="w70 t_l">车位数量：</td>
                        <td class="w170">
                        <?php
                        if($data_info['park_num']){
                            echo $data_info['park_num'].'个';
                        }
                        ?>
                        </td>
                        <td class="w70 t_l">&nbsp;</td>
                        <td>&nbsp;</td>
                        <?php }else{ ?>
                        <td class="w70 t_l">&nbsp;</td>
                        <td class="w170">&nbsp;</td>
                        <td class="w70 t_l">&nbsp;</td>
                        <td>&nbsp;</td>
                        <?php } ?>
                    </tr>
                    <?php }}?>
				</table>
				<table class="table">
                    <?php if($data_info['sell_type'] == 3){ ?>
                    <tr>
                        <td class="w70 t_l">目标业态：</td>
                        <td>
                            <div class="ie-break-all"><?php
                                if($data_info['shop_trade_arr'] && $config['shop_trade']){
                                    foreach($data_info['shop_trade_arr'] as $key => $val)
                                    {
										if($val)
                                        {
											echo $config['shop_trade'][$val].'、';
										}
                                    }
                                }
                            ?></div>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(1==$is_pub){?>
                    <tr>
                        <td class="w70 t_l">房屋设施：</td>
                        <td>
                            <div class="ie-break-all"><?php
                                if(isset($data_info['equipment_arr'])){
                                    foreach($data_info['equipment_arr'] as $key => $val)
                                    {
										if($val)
                                        {
											echo $config['equipment'][$val].'、';
										}
                                    }
                                }
                            ?></div>
                        </td>
                    </tr>
                    <?php }?>
                    <?php if(1==$is_pub){?>
                    <tr>
                        <td class="w70 t_l">已同步：</td>
                        <td>
                            <div class="ie-break-all"><?php
                                if(isset($data_info['is_outside'])){
                                    echo '1'==$data_info['is_outside']?'是':'否';
                                }
                            ?></div>
                        </td>
                    </tr>
                    <?php }?>
                    <?php if(1==$is_pub){?>
                    <tr>
                        <td class="w70 t_l">周边环境：</td>
                        <td>
                            <div class="ie-break-all"><?php
                                if($data_info['setting_arr']){
                                    foreach($data_info['setting_arr'] as $key => $val)
                                    {
										if($val)
                                        {
											echo $config['setting'][$val].'、';
										}
                                    }
                                }
                            ?></div>
                        </td>
                    </tr>
                    <?php }?>
					<tr>
					<td class="w70 t_l">房源标题：</td>
					 <td><div class="ie-break-all"><?php
					 if($data_info['title'])
                     {
						echo $data_info['title'];
					 }
					?></div></td>
					</tr>
					<tr>
					<td class="w70 t_l">房源描述：</td>
					 <td width="627"><div class="ie-break-all"><?php if($data_info['bewrite']){
						 echo $data_info['bewrite'];
					 }?></div></td>
					</tr>
                    <tr>
                        <td class="w70 t_l">备注：</td>
                        <td><div class="ie-break-all"><?php echo $data_info['remark'];?></div></td>
                    </tr>
				</table>
				<table class="table">
                    <tr>
                        <td class="w70 t_l">委托时间：</td>
                        <td class="w170"><?php echo date('Y-m-d',$data_info['createtime']);?></td>
                        <td class="w70 t_l">跟进时间：</td>
                        <td class="w170">
						<?php echo date('Y-m-d H:i:s',$data_info['updatetime']);?>
						</td>
                    </tr>
					<tr class="clear-line">
					<td class="w80 t_l">委托经纪人：</td>
					<td class="w170">
					<?php echo $broker_agency_name['truename'];?>
					</td>
					<td class="w70 t_l">委托门店：</td>
					<td class="w170">
					<?php echo $broker_agency_name['agency_name'];?>
					</td>
					<td class="w70 t_l">联系电话：</td>
					<td class="w170">
					<?php  echo $broker_agency_name['phone'];?>
					</td>
					</tr>
                </table>
            </div>
            <?php } ?>
			<?php if($tab==2){?>
            <div class="js_d inner inner02"  style="display:block">
                <div class="t_box">
                    <table class="table" id="js_table_ys">
                        <tr>
                            <td class="w70 t_l">楼盘名称：</td>
                            <td class="w170"><?php echo $data_info['block_name'];?></td>
                            <td class="w70 t_l">区属：</td>
                            <td class="w170"><?php echo $data_info['district_name'];?></td>
                            <td class="w70 t_l">板块：</td>
                            <td><?php echo $data_info['street_name'];?></td>
                        </tr>
                        <tr>
                            <td class="w70 t_l">地址：</td>
                            <td class="w170"><?php echo $data_info['address'];?></td>
                            <td class="w70 t_l">栋座：</td>
                            <td class="w170"><strong class="color js_y_hide" id="dong">***</strong></td>
                            <td class="w70 t_l">单元：</td>
                            <td><strong class="color js_y_hide" id="unit">***</strong><strong class="color js_y_hide hide">3</strong></td>
                        </tr>
                        <tr>
                            <td class="w70 t_l">门牌：</td>
                            <td class="w170"><strong class="color js_y_hide" id="door">***</strong></td>
                            <td class="w70 t_l">业主姓名：</td>
                            <td class="w170"><strong class="color js_y_hide" id="owner">***</strong></td>
                            <td class="w70 t_l">业主电话：</td>
                            <td><strong class="color js_y_hide" id="telnos"><?php echo ($is_phone_per)?$data_info['telnos']:'***'?></strong></td>
                        </tr>
                        <tr>
                            <td class="w70 t_l">身份证号：</td>
                            <td class="w170"><strong class="color js_y_hide" id="idcare">***</strong></td>
                            <td class="w70 t_l">面积：</td>
                            <td class="w170"><?php echo strip_end_0($data_info['buildarea']);?>平方米</td>
                            <td class="w70 t_l">售价：</td>
                            <td><?php echo strip_end_0($data_info['price']);?>万元</td>
                        </tr>
                        <tr>
                            <td class="w70 t_l">底价：</td>
                            <td class="w170"><strong class="color js_y_hide" id="lowprice">***</strong></td>
                            <td class="w70 t_l">单价：</td>
                            <td class="w170"><?php echo strip_end_0($data_info['avgprice']);?>元/平米</td>
                        </tr>
                    </table>
                    <p class="link_btn_b">
                        <?php if( $data_info['lock']==0 || in_array( $broker_id , array($data_info['broker_id'],$data_info['lock']) ) ){ ?>
                        <a onClick="show_baomi_info('<?php echo $data_info['id'];?>','sell')" href="javascript:void(0)"><span class="iconfont">&#xe610;</span> 查看保密信息</a>
                        <?php }else{ ?>
                        很遗憾，您无权查看相关保密信息。
                        <?php } ?>
                    </p>
                </div>
                 <div class="clearfix pop_fg_fun_box">
                    <div class="text left"><span class="fg">总查阅次数：<a href="#"><?php echo $user_num;?></a></span><span class="fg">今日查阅次数：<a href="#"><?php echo $today_num; ?></a></span></div>
                    <div class="get_page">
                        <span><strong id='thispage'><?php echo $page;?></strong>/<?php echo $pages;?>页</span>
                        <input type="hidden" name="pg" value="1"/>
                        <?php if($pages!=1){?>
                        <a href="javascript:void(0)" id="prev_page">上一页</a><a href="javascript:void(0)" id='next_page'>下一页</a>
                        <?php }?>
                        <span>共<?php echo $group_by_num;?>条</span>
                    </div>
                </div>
                <div class="table_list_box">
                    <table class="table_list" id='table_list'>
                        <tr>
                            <th class="w130">最近查阅时间</th>
                            <th class="w170">查阅门店</th>
                            <th class="w70">查阅人</th>
                            <th class="w90">总查阅次数</th>
                            <th class="w90">今日查阅次数</th>
                            <th>初次查阅时间</th>
                        </tr>
                        <?php foreach($brower_list2 as $k => $v){?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i:s',$v['recent_brower']);?></td>
                            <td><?php echo $v['agency_name'];?></td>
                            <td><?php echo $v['broker_name'];?></td>
                            <td><?php echo $v['brower_num'];?></td>
                            <td><?php echo $v['today_brower_num'];?></td>
                            <td><?php echo date('Y-m-d H:i:s',$v['first_brower']);?></td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
            </div>
			<?php } ?>
            <?php if($tab==3){?>
            <div class="js_d inner inner02"  style="display:block">
                <div class="show_house_pic">
                    <p class="title">室内图</p>
                    <div class="pic">
                        <img src="<?php if(!empty($shineipic[0])){ echo changepic($shineipic[0]['url']);}else{ echo MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg';}?>" width="340" height="220">
                    </div>
                    <div class="small_pic">
                        <div class="prev"><span class="iconfont">&#xe607;</span></div>
                        <div class="list">
                            <ul class="clearfix">
                                <?php
                                    if($shineipic)
                                    {
                                        foreach($shineipic as $key => $val)
                                        {
                                            echo '<li class="item"><img alt="" src="'.changepic($val['url']).'" height="55" width="72"></li>';
                                        }
                                    }
                                    else
                                    {
                                        for($i=0;$i<4;$i++)
                                        {
                                            echo '<li class="item"><img alt="" src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg" height="55" width="72"></li>';
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="next"><span class="iconfont">&#xe607;</span></div>
                    </div>
                </div>
                <div class="show_house_pic_fg">&nbsp;</div>
                <div class="show_house_pic">
                    <p class="title">户型图</p>
                    <div class="pic">
                        <img alt="" src="<?php if(!empty($huxingpic[0])){ echo changepic($huxingpic[0]['url']);}else{echo MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg';}?>" width="340" height="220">
                    </div>
                    <div class="small_pic">
                        <div class="prev"><span class="iconfont">&#xe607;</span></div>
                        <div class="list">
                            <ul class="clearfix">
                                <?php
                                    if($huxingpic)
                                    {
                                        foreach($huxingpic as $key => $val)
                                        {
                                            echo '<li class="item"><img alt="" src="'.changepic($val['url']).'" height="55" width="72"></li>';
                                        }
                                    }else{
                                        for($i=0;$i<4;$i++){
                                            echo '<li class="item"><img alt="" src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg" height="55" width="72"></li>';
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="next"><span class="iconfont">&#xe607;</span></div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if($tab == 4){?>
            <div class="js_d inner"  style="display:block">
                <table class="table">
                    <tr>
                        <td class="w70 t_l">楼盘名称：</td>
                        <td class="w170"><?php echo $data_info['block_name'];?></td>
                        <td class="w70 t_l">区属：</td>
                        <td class="w170"><?php echo $data_info['district_name'];?></td>
                        <td class="w70 t_l">板块：</td>
                        <td><?php echo $data_info['street_name'];?></td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">地址：</td>
                        <td class="w170"><?php echo $community_info['address'];?></td>
                        <td class="w70 t_l">物业类型：</td>
                        <td class="w170">
						<?php echo $build_type;?>
						</td>
                        <td class="w70 t_l">建筑年代：</td>
                        <td><?php
						if($community_info['build_date']){
						   echo date('Y',$community_info['build_date']).'年';
						}
						?>
						</td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">产权年限：</td>
                        <td class="w170"><?php
						if($community_info['property_year']){
							echo $community_info['property_year']."年";
						}
						?></td>
                        <td class="w70 t_l">建筑面积：</td>
                        <td class="w170"><?php
						if($community_info['buildarea']){
							echo $community_info['buildarea']."平方米";
						}
						?></td>
                        <td class="w70 t_l">占地面积：</td>
                        <td><?php
						if($community_info['coverarea']){
							echo $community_info['coverarea']."平方米";
						}

						?></td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">物业公司：</td>
                        <td class="w170"><?php echo $community_info['property_company'];?></td>
                        <td class="w70 t_l">开发商：</td>
                        <td class="w170"><?php echo $community_info['developers'];?></td>
                        <td class="w70 t_l">停车位：</td>
                        <td><?php if($community_info['parking']){
                            echo $community_info['parking'];
                        }
                        ?></td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">绿化率：</td>
                        <td class="w170">
                        <?php
                        if($community_info['green_rate'] > 0){
                            echo ($community_info['green_rate']*100).'%';
                        }
                        ?>
                        </td>
                        <td class="w70 t_l">容积率：</td>
                        <td class="w170">
                        <?php
                        if($community_info['plot_ratio'] > 0){
                            echo $community_info['plot_ratio'];
                        }
                        ?>
                        </td>
                        <td class="w70 t_l">物业费：</td>
                        <td>
                        <?php
                        if($community_info['property_fee'] > 0){
                            echo $community_info['property_fee'].'元/月•平米';
                        }
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">总栋数：</td>
                        <td class="w170">
                        <?php
                        if($community_info['build_num']){
                            echo $community_info['build_num'];
                        }
                        ?>
                        </td>
                        <td class="w70 t_l">总户数：</td>
                        <td class="w170">
                        <?php
                        if($community_info['total_room']){
                            echo $community_info['total_room'];
                        }
                        ?>
                        </td>
                        <td class="w70 t_l">楼层状况：</td>
                        <td><?php echo $community_info['floor_instruction'];?></td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">楼盘简介：</td>
                        <td style="width:610px;overflow:hidden;"><?php echo $community_info['introduction'];?></td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">周边环境：</td>
                        <td style="width:610px;overflow:hidden;">
						<?php echo $facilities;?>
						</td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">图片：</td>
                        <td style="width:610px;overflow:hidden;">
						<?php
						foreach($cmt_arr as $key=>$val){
							echo '<img alt="" src="'.$val['image'].'" height="105" width="140" style="float:left; margin:0 10px 10px 0;">';
						}
						?>
					</td>
                    </tr>
                </table>
            </div>
            <?php } ?>
			<?php if($tab==5){?>
            <div class="js_d inner inner02"  style="display:block">
                <div class="hz_inner_info">
                    <div class=" clearfix">
                        <div class="title left"><span class="fg">本房源被查看次数：<?php echo $brower_list4['view_num'];?></span>本房源被查看人数：<?php echo $brower_list4['view_people'];?></div>
                        <?php if($brower_list4['pages']>0){?>
                        <div class="get_page">
                            <span><strong id='thispage'><?php echo $brower_list4['page'];?></strong>/<?php echo $brower_list4['pages'];?>页</span>
                            <input type="hidden" name="pg" value="1"/>
                            <?php if($brower_list4['pages']!=1){?>
                            <a href="javascript:void(0)" id="prev_page">上一页</a><a href="javascript:void(0)" id='next_page'>下一页</a>
                            <?php }?>
                            <span>共<?php echo $brower_list4['log_num'];?>条</span>
                        </div>
                        <?php }?>
                    </div>

                    <div class="info">
                        <div class="list">
                            <table class="table" id="table_list">
                                <tr>
                                    <th class="w90">查看人</th>
                                    <th class="w200">所属门店</th>
                                    <th class="w160">联系方式</th>
                                    <th class="w70">查看次数</th>
                                    <th>最近查看时间</th>
                                </tr>
                                <?php if( $brower_list4['view_people'] > 0){?>
                                <?php foreach($brower_list4['view_log_list'] as $key => $value){ ?>
                                <tr <?php if($key%2 == 0) {?> class="bg"<?php }?>>
                                    <td><?php echo $value['broker_name_v'];?></td>
                                    <td><?php echo $value['agency_name_v'];?></td>
                                    <td><?php echo $value['broker_telno_v'];?></td>
                                    <td><?php echo $value['num'];?></td>
                                    <td><?php echo date('Y-m-d H:i:s',$value['datetime']);?></td>
                                </tr>
                                <?php }?>
                                <?php }else {?>
                                <tr  class="bg"><td>暂无数据</td></tr>
                                <?php }?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="hz_inner_info">
                    <div class=" clearfix">
                        <div class="title left"><span class="fg">本房源被申请合作次数：<?php echo $brower_list3['cooperate_num'] ?></span></div>
                        <?php if($brower_list3['cooperate_pages']>0){ ?>
                        <div class="get_page">
                            <span><strong id='cooperate_thispage'><?php echo $brower_list3['cooperate_page'];?></strong>/<?php echo $brower_list3['cooperate_pages'];?>页</span>
                            <input type="hidden" name="cooperate_pg" value="1"/>
                            <?php if($brower_list3['cooperate_pages']!=1){?>
                            <a href="javascript:void(0)" id="cooperate_prev_page">上一页</a><a href="javascript:void(0)" id="cooperate_next_page">下一页</a>
                            <?php }?>
                            <span>共<?php echo $brower_list3['cooperate_num'];?>条</span>
                        </div>
                        <?php }?>
                    </div>
                    <div class="info">
                        <div class="list">
                            <table class="table" id="cooperate_table_list">
                                <tr>
                                    <th class="w90">申请人</th>
                                    <th class="w240">所属门店</th>
                                    <th class="w120">联系方式</th>
                                    <th class="w170">申请时间</th>
                                    <th>状态</th>
                                </tr>
                                <?php if( is_array($brower_list3['cooperate_log_list']) && !empty($brower_list3['cooperate_log_list']) ){?>
                                    <?php foreach ($brower_list3['cooperate_log_list'] as $key => $value ){?>
                                    <tr class="bg">
                                        <td><?php echo $value['broker_name_b'];?></td>
                                        <td><?php echo $value['agency_name_b'];?></td>
                                        <td><?php echo $value['phone_b'];?></td>
                                        <td><?php echo date('Y-m-d H:i:s',$value['creattime']);?></td>
                                        <td><strong class="s_z"><?php echo $cooperate_conf['esta'][$value['esta']];?></strong></td>
                                    </tr>
                                    <?php }?>
                                <?php }else {?>
                                    <tr  class="bg"><td>暂无数据</td></tr>
                                <?php }?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
			<?php } ?>
			<?php if($tab==6){?>
			<style type="text/css">
			#container{width:100%;height:390px;}
			</style>
            <div class="js_d inner inner02"  style="display:block">
                <script type="text/javascript" src="http://api.map.baidu.com/api?v=1.5&ak=s4xTcbCABxjTGG3EfdZpQxaT"></script>
				<div id="container"></div>
				<script type="text/javascript">
				var map = new BMap.Map("container");          // 创建地图实例
				var point = new BMap.Point(<?php echo $b_map_x; ?>, <?php echo $b_map_y; ?>);  // 创建点坐标
				map.centerAndZoom(point, 15);                 // 初始化地图，设置中心点坐标和地图级别
				map.enableScrollWheelZoom();				//启用滚轮放大缩小
				var marker = new BMap.Marker(point);        // 创建标注
				map.addOverlay(marker);                     // 将标注添加到地图中

				map.addControl(new BMap.NavigationControl());
				map.addControl(new BMap.ScaleControl());
				map.addControl(new BMap.OverviewMapControl());
				map.addControl(new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP,BMAP_SATELLITE_MAP,BMAP_HYBRID_MAP ]}));
				</script>
            </div>
			<?php } ?>
        </div>
        <?php if( $hide_btn == 0){ ?>

        <?php if( $is_pub == 1){ ?>
        <div class="tab_pop_bd">
            <div>
                <a class="btn-hui1 btn-left" href="/appoint_center/modify/<?php echo $data_info['id'];?>" target="_parent"><span>编辑</span></a>
                <a class="btn-hui1 btn-left" href="javascript:void(0);"  onclick="adel('appoint_center',<?php echo $data_info['id'];?>,'app_sell',<?=$app_id?>)"><span>删除</span></a>
            </div>
            <div>
                <a class="btn-lan" href="javascript:void(0);" onclick="parent.xqhouse('sell',<?php echo $data_info['id']?>)"><span>分配房源</span></a>
                <a href="javascript:void(0);"class="btn-lan" onclick="parent.xqtasks('sell',1,<?php echo $data_info['id']?>)"><span>分配任务</span></a>
                <a class="btn-lan" onclick="parent.xq_openfollow('sell',<?php echo $data_info['id'] ?>,1)" href="javascript:void(0);"><span>房源跟进</span></a>
                <a class="btn-lan" onclick="parent.open_match('sell',1 , <?php echo $data_info['id'];?>)" href="javascript:void(0);"><span>智能匹配</span></a>
            </div>
        </div>
		<?php }else if($is_pub == 2){ ?>
		<div class="tab_pop_bd">
            <div>
                <?php if(in_array($data_info['id'] , $num_id)){ ?>
                    <a class="collect-btn collect-success" href="javascript:void(0);" >已收藏</a>
                <?php }else{ ?>
                    <a class="collect-btn collect" href="javascript:void(0);" id = "cang_info<?php echo $data_info['id']?>" onclick="shcang('house_collect','sell_house',<?php echo $data_info['id']; ?>,'info')">收藏房源</a>
                <?php } ?>
            </div>
            <div>
                <a class="btn-lan" onclick="parent.open_match('sell' ,2,<?php echo $data_info['id'];?>);" href="javascript:void(0);"><span>智能匹配</span></a>
                <?php if('1'==$open_cooperate){?>
                <a class="btn-lan" href="javascript:void(0);" onclick="parent.cooperate_house('sell',<?php echo $data_info['id'];?> , <?php echo $data_info['broker_id'];?>);"><span>合作申请</span></a>
                <?php }?>
            </div>
        </div>
		<?php } ?>

        <?php } ?>
    </div>
</div>
<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>

<script type="text/javascript">
var tab = '<?php echo $tab;?>';
function ajax_submit(method,page,houseid,is_cooperate){
    var submit_data = {};
    if('next_page'==method){
        submit_data.pg = parseInt(page)+1;
    }else if('prev_page'==method){
        submit_data.pg = parseInt(page)-1;
    }else if('to_page'==method){
        submit_data.pg = page;
    }
    if(tab==5) {
        if('cooperate'==is_cooperate){
            ajax_url = '/sell/ajax_get_cooperate_log/'
        } else {
            ajax_url = '/sell/ajax_get_view_log/'
        }
    } else {
        ajax_url = '/sell/ajax_get_brower_log/'
    }
    $.ajax({
        url: ajax_url + houseid,
        type: 'GET',
        data: submit_data,
        dataType: 'json',
        success:function(return_data){
           if (tab == 5) {
                if('cooperate'==is_cooperate){
                    var html_str = '<tbody>';
                    html_str += '<tr>';
                    html_str += '<th class="w90">申请人</th>';
                    html_str += '<th class="w200">所属门店</th>';
                    html_str += '<th class="w160">联系方式</th>';
                    html_str += '<th class="w170">查看时间</th>';
                    html_str += '<th>状态</th>';
                    html_str += '</tr>';
                    for(var i=0;i<return_data.length;i++){
                        html_str += '<tr>';
                        html_str += '<td>'+return_data[i].broker_name_b+'</td>';//申请人
                        html_str += '<td>'+return_data[i].agency_name_b+'</td>';//所属门店
                        html_str += '<td>'+return_data[i].phone_b+'</td>';//联系方式
                        html_str += '<td>'+return_data[i].creattime+'</td>';//查看时间
                        html_str += '<td>'+return_data[i].esta+'</td>';//状态
                        html_str += '</tr>';
                    }
                    html_str += '</tbody>';
                    $('#cooperate_table_list').empty().html(html_str);
                }else{
                    var html_str = '<tbody>';
                    html_str += '<tr>';
                    html_str += '<th class="w90">查看人</th>';
                    html_str += '<th class="w200">所属门店</th>';
                    html_str += '<th class="w160">联系方式</th>';
                    html_str += '<th class="w70">查看次数</th>';
                    html_str += '<th>最近查看时间</th>';
                    html_str += '</tr>';
                    for(var i=0;i<return_data.length;i++){
                        html_str += '<tr>';
                        html_str += '<td>'+return_data[i].broker_name_v+'</td>';//查看人
                        html_str += '<td>'+return_data[i].agency_name_v+'</td>';//所属门店
                        html_str += '<td>'+return_data[i].broker_telno_v+'</td>';//联系方式
                        html_str += '<td>'+return_data[i].num+'</td>';//查看次数
                        html_str += '<td>'+return_data[i].datetime+'</td>';//最近查看时间
                        html_str += '</tr>';
                    }
                    html_str += '</tbody>';
                    $('#table_list').empty().html(html_str);
                }
            } else {
                var html_str = '<tbody>';
                html_str += '<tr>';
                html_str += '<th class="w130">最近查阅时间</th>';
                html_str += '<th class="w170">查阅门店</th>';
                html_str += '<th class="w70">查阅人</th>';
                html_str += '<th class="w90">总查阅次数</th>';
                html_str += '<th class="w90">今日查阅次数</th>';
                html_str += '<th>初次查阅时间</th>';
                html_str += '</tr>';
                for(var i=0;i<return_data.length;i++){
                    html_str += '<tr>';
                    html_str += '<td>'+return_data[i].browerdate+'</td>';//查阅时间
                    html_str += '<td>'+return_data[i].agency_name+'</td>';//查阅门店
                    html_str += '<td>'+return_data[i].broker_name+'</td>';//查阅人
                    html_str += '<td>'+return_data[i].brower_num+'</td>';//总查阅次数
                    html_str += '<td>'+return_data[i].today_brower_num+'</td>';//今日查阅次数
                    html_str += '<td>'+return_data[i].recent_brower+'</td>';//初次查阅时间
                    html_str += '</tr>';
                }
                html_str += '</tbody>';
                $('#table_list').empty().html(html_str);
            }
        }
    });
}

$(function(){
    var house_id = <?php echo $house_id;?>;//房源id
    var page = $('input[name="pg"]').val();
    var page_cooperate = $('input[name="cooperate_pg"]').val();
    if(page == '1'){
         $('#prev_page').css('display','none');
    }
    if(page_cooperate == '1'){
        $('#cooperate_prev_page').css('display','none');
    }
    <?php if($tab == 5){ ?>
    //下一页
    $('#next_page').click(function(){
        var pages = <?php echo $brower_list4['pages'];?>;
        var page = $('input[name="pg"]').val();
        ajax_submit('next_page',page,house_id);
        $('input[name="pg"]').val(parseInt(page)+1);
        var newpage = $('input[name="pg"]').val();
        $('#thispage').html(newpage);
        $('#prev_page').attr('style','');
        if(newpage>pages-1){
            $('#next_page').css('display','none');
        }else{
            $('#next_page').attr('style','');
        }
    });
    //上一页
    $('#prev_page').click(function(){
        var page = $('input[name="pg"]').val();
        ajax_submit('prev_page',page,house_id);
        $('input[name="pg"]').val(parseInt(page)-1);
        var newpage = $('input[name="pg"]').val();
        $('#thispage').html(newpage);
        $('#next_page').attr('style','');
        if(newpage<2){
            $('#prev_page').css('display','none');
        }else{
            $('#prev_page').attr('style','');
        }
    });
    //合作申请下一页
    $('#cooperate_next_page').click(function(){
        var pages = <?php echo $brower_list3['cooperate_pages'];?>;
        var page = $('input[name="cooperate_pg"]').val();
        ajax_submit('next_page',page,house_id,'cooperate');
        $('input[name="cooperate_pg"]').val(parseInt(page)+1);
        var newpage = $('input[name="cooperate_pg"]').val();
        $('#cooperate_thispage').html(newpage);
        $('#cooperate_prev_page').attr('style','');
        if(newpage>pages-1){
            $('#cooperate_next_page').css('display','none');
        }else{
            $('#cooperate_next_page').attr('style','');
        }
    });
    //合作申请上一页
    $('#cooperate_prev_page').click(function(){
        var page = $('input[name="cooperate_pg"]').val();
        ajax_submit('prev_page',page,house_id,'cooperate');
        $('input[name="cooperate_pg"]').val(parseInt(page)-1);
        var newpage = $('input[name="cooperate_pg"]').val();
        $('#cooperate_thispage').html(newpage);
        $('#cooperate_next_page').attr('style','');
        if(newpage<2){
            $('#cooperate_prev_page').css('display','none');
        }else{
            $('#cooperate_prev_page').attr('style','');
        }
    });
    <?php } ?>
});


//详情页删除
function adel(type,id,fun,app_id){
    var text=id;
	//alert(text);
    var alert_html = '';

    alert_html = '确定要删除该预约吗';

    $("#dialogSaveDiv").html(alert_html);
    openWin('jss_pop_tip');
    $("#dialog_share").click(function(){
        $.ajax({
              url: "/"+type+"/del/",
              type: "GET",
              dataType: "json",
              data: {
                  str: text,
                  isajax:1,
				  app_id:app_id
              },
              success: function(data) {
				  //alert(data);
				  if(data['errorCode'] == '401')
				  {
					  login_out();
					  return false;
				  }
				  else if(data['errorCode'] == '403')
				  {
					  permission_none();
					  return false;
				  }

				  if(data['result'] == 'ok')
				  {
					  //$("#js_pop_tip").remove();
					  //window.parent.location.href="/"+type+"/"+fun+"/";
					  window.parent.location.reload();
				  }
			  }
          });
    });
}
</script>
