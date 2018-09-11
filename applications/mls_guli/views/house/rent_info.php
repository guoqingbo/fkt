<script>
//预约详情页删除
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
<div class="pop_box_g" id="js_pop_box_g"  style="display:block;">
    <div class="hd">
        <div class="title">房源详情</div>
        <div class="close_pop"></div>
    </div>
    <div class="mod">
        <div class="tab_pop_hd">
            <dl class="clearfix" id="js_tab_t01">
                <?php if($is_pub == 1 || $is_pub == 4 || $is_pub == 5){?>
                <a class=" item <?php if($tab == 1) { ?> itemOn <?php }?>" href="/rent/details_house/<?php echo $data_info['id'];?>/<?=$is_pub?>/1/<?php echo $hide_btn;?>/<?=$app_id?>">房源详情</a>
                <a class=" item <?php if($tab == 2) { ?> itemOn <?php }?>" href="/rent/details_secret/<?php echo $data_info['id'];?>/<?=$is_pub?>/2/<?php echo $hide_btn;?>/<?=$app_id?>">保密信息</a>
                <a class=" item <?php if($tab == 3) { ?> itemOn <?php }?>" href="/rent/details_image/<?php echo $data_info['id'];?>/<?=$is_pub?>/3/<?php echo $hide_btn;?>/<?=$app_id?>">房源图片</a>
				<?php if($data_info['video_id']){?>
				<a class=" item <?php if($tab == 7) { ?> itemOn <?php }?>" href="/rent/details_video/<?php echo $data_info['id'];?>/<?=$is_pub?>/7/<?php echo $hide_btn;?>/<?=$app_id?>">房源视频</a>
				<?php }?>
                <?php if($xiaoquflag == 1){?>
                <a class=" item <?php if($tab == 4) { ?> itemOn <?php }?>" href="/rent/details_district/<?php echo $data_info['id'];?>/<?=$is_pub?>/4/<?php echo $hide_btn;?>/<?=$app_id?>">小区概况</a>
                <?php }?>
                <?php }?>
                <?php if($is_pub == 2 || $is_pub == 3 || $is_pub == 6){?>
				<a class=" item <?php if($tab == 1) { ?> itemOn <?php }?>" href="/rent/details_house/<?php echo $data_info['id'];?>/<?php echo $is_pub;?>/1">房源详情</a>
                <a class=" item <?php if($tab == 3) { ?> itemOn <?php }?>" href="/rent/details_image/<?php echo $data_info['id'];?>/<?php echo $is_pub;?>/3">房源图片</a>
				<?php if($data_info['video_id']){?>
				<a class=" item <?php if($tab == 7) { ?> itemOn <?php }?>" href="/rent/details_video/<?php echo $data_info['id'];?>/<?php echo $is_pub;?>/7/<?php echo $hide_btn;?>/<?=$app_id?>">房源视频</a>
				<?php }?>
                <?php if($xiaoquflag == 1){?>
                <a class=" item <?php if($tab == 4) { ?> itemOn <?php }?>" href="/rent/details_district/<?php echo $data_info['id'];?>/<?php echo $is_pub;?>/4">小区概况</a>
                <?php }?>
                <?php }?>

				<?php if($xiaoqumapflag == 1){?>
                <a class=" item <?php if($tab == 6) { ?> itemOn <?php }?>" href="/rent/details_map/<?php echo $data_info['id'];?>/<?php echo $is_pub;?>/6/0/<?=$app_id?>">小区地图</a>
                <?php }?>
                <?php if($is_pub == 1 || $is_pub == 5){?>
				<a class=" item <?php if($tab == 5) { ?> itemOn <?php }?>" href="/rent/details_hezuo/<?php echo $data_info['id'];?>/1/5/<?php echo $hide_btn;?>/<?=$app_id?>">合作统计</a>
                <?php }?>
            </dl>
        </div>
        <div class="tab_pop_mod clear" id="js_tab_b01">
            <?php if($tab==1){?>
            <div class="js_d inner" style="display:block;">
			<?php $type = '暂不售（租）';?>
                <table class="table">
					<?php if( $is_pub == 1 || $is_pub == 4 || $is_pub == 5 || $is_pub == 2){ ?>
					<tr>
						<td class="w70 t_l">房源编号：</td>
						<td class="w170">CZ<?php echo $data_info['id']; ?></td>
						<td class="w70 t_l">状态：</td>
						<td class="w170" style="color:#F75000">
						<?php echo $config['status'][$data_info['status']] == $type?'暂不租' : $config['status'][$data_info['status']]; ?>
						</td>
						<td class="w70 t_l">物业类型：</td>
						<td class="w170" style="color:#F75000"><?php echo $config['sell_type'][$data_info['sell_type']]; ?></td>
					</tr>
					<?php }?>
                    <tr>
                        <td class="w70 t_l">楼盘名称：</td>
                        <td class="w170" style="color:#F75000"><?php echo $data_info['block_name'];?></td>
                        <td class="w70 t_l">区属：</td>
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
                            <?php if(1==$is_pub || 4==$is_pub || 5==$is_pub){
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
                                if('2'==$data_info['sell_type'] || '3'==$data_info['sell_type'] || '5'==$data_info['sell_type'] || '6'==$data_info['sell_type'] || '7'==$data_info['sell_type']){
                                    $floor_str = '低楼层';
                                }
                                echo $floor_str; echo '/';echo $data_info['totalfloor'];

                             }?>
                        <td class="w70 t_l">朝向：</td>
						<td><?php echo $config['forward'][$data_info['forward']]; ?></td>

					</tr>
					<tr>
                        <td class="w70 t_l">装修：</td>
                        <td class="w170" style="color:#F75000"><?php

						if($data_info['fitment']){
							echo $config['fitment'][$data_info['fitment']];}
						 ?></td>
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
						<td class="w70 t_l">租金：</td>
                        <td class="w170" style="color:#F75000"><?php echo strip_end_0($data_info['price']); if($data_info['price_danwei'] == 1){echo "元/㎡*天";}else{echo "元/月";}?></td>
                        <?php if(1==$is_pub || 5==$is_pub){?>
                        <td class="w70 t_l">押金：</td>
                        <td class="w170"><?php if($data_info['deposit']){
                            echo $data_info['deposit'].'元';
                        }
                        ?></td>
                        <?php }?>
                        <?php if(1==$is_pub || 4==$is_pub || 5==$is_pub){?>
						 <td class="w70 t_l">租赁期限：</td>
                        <td><?php
						if($data_info['renttime']){
						echo $config['renttime'][$data_info['renttime']];
						}
						?></td>
                        <?php }?>
						<?php if(2==$is_pub || $is_pub == 6){?>
						<td class="w70 t_l">出租面积：</td>
						<td style="color:#F75000"><?php echo strip_end_0($data_info['buildarea']); ?>平方米</td>
						<td class="w70 t_l">物业类型：</td>
						<td class="w170" style="color:#F75000"><?php echo $config['sell_type'][$data_info['sell_type']]; ?></td>
						<?php }?>
                    </tr>
					<?php if(1==$is_pub || 4==$is_pub || 5==$is_pub){?>
                    <tr>
					<td class="w70 t_l">房源性质：</td>
                        <td style="color:#F75000"><?php echo $config['nature'][$data_info['nature']]; ?></td>
                        <td class="w70 t_l">出租面积：</td>
						<td style="color:#F75000"><?php echo strip_end_0($data_info['buildarea']); ?>平方米</td>
                        <td class="w70 t_l">钥匙编号：</td>
                        <td style="color:#F75000"><?php if($data_info['keys']){echo $data_info['key_number'];}else{echo "无";} ?></td>
                    </tr>
                    <tr>
                        <?php if(1==$is_pub || 5==$is_pub){?>
                        <td class="w70 t_l">产权：</td>
                        <td class="w170"><?php
						if($data_info['property']){
						echo $config['property'][$data_info['property']];
						}
						 ?></td>
                        <?php }?>
                        <td class="w70 t_l">委托类型：</td>
                        <td class="w170" style="color:#F75000"><?php
							if($data_info['rententrust']){
							echo $config['rententrust'][$data_info['rententrust']];
							}
						?></td>
                        <?php if(1==$is_pub || 5==$is_pub){?>
                        <td class="w70 t_l">类型：</td>
                        <td>
                            <?php
                                if($data_info['sell_type'] == 2 && $data_info['villa_type']){
                                    echo $config['villa_type'][$data_info['villa_type']];
                                }elseif($data_info['sell_type'] == 3 && $data_info['shop_type']){
                                    echo $config['shop_type'][$data_info['shop_type']];
                                }elseif($data_info['sell_type'] == 4 && $data_info['office_type']){
                                    echo $config['office_type'][$data_info['office_type']];
                                }elseif($data_info['house_type']){
                                    echo $config['house_type'][$data_info['house_type']];
                                }
                            ?>
                        </td>
                        <?php }?>
                    </tr>
                    <?php if($data_info['sell_type'] == 2){ ?>
                    <tr>
                        <td class="w70 t_l">厅结构：</td>
                        <td class="w170"><?php
						if($data_info['hall_struct']){
							echo $config['hall_struct'][$data_info['hall_struct']];
						}
						?></td>
                        <td class="w70 t_l">地下面积：</td>
                        <td class="w170"><?php if($data_info['floor_area'] > 0){
                            echo strip_end_0($data_info['floor_area']); ?>平方米 <?php
						    if($data_info['light_type']){
							    echo $config['light_type'][$data_info['light_type']];
						        }
                            }
						?></td>
                        <td class="w70 t_l">花园面积：</td>
                        <td><?php if($data_info['garden_area'] > 0)
                        {
                            echo strip_end_0($data_info['garden_area']).'平方米';
                        }
                        ?></td>
                    </tr>
                    <?php } ?>
                    <?php if(1==$is_pub || 5==$is_pub){?>
                    <tr>
                        <td class="w70 t_l">付款方式：</td>
                        <td class="w170"><?php

						if($data_info['rentpaytype']){
							echo $config['rentpaytype'][$data_info['rentpaytype']];
						}
						 ?></td>
                        <td class="w70 t_l">物业费：</td>
                        <td class="w170"><?php
						if($data_info['strata_fee'] > 0){
							echo strip_end_0($data_info['strata_fee']);
                            echo "元/平方米·月 ";
						}
						?></td>
                        <td class="w70 t_l">现状：</td>
                        <td><?php if($data_info['current']){ echo $config['current'][$data_info['current']];} ?></td>
                    </tr>
                    <?php }?>
                    <?php if(1==$is_pub || 5==$is_pub){?>
                    <tr>
                        <td class="w70 t_l">信息来源：</td>
                        <td class="w170">
                        <?php
						if($data_info['infofrom']){
							echo $config['infofrom'][$data_info['infofrom']];
						}
						 ?>
                        </td>
                        <?php if($data_info['sell_type'] == 2){ ?>
                        <td class="w70 t_l">车位数量：</td>
                        <td class="w170"><?php if($data_info['park_num']){
                            echo $data_info['park_num'].'个';
                        }
                        ?></td>
                        <?php }else{ ?>
                        <td class="w70 t_l"></td>
                        <td class="w170"></td>
                        <td class="w70 t_l"></td>
                        <td></td>
                        <?php } ?>
                    </tr>
                    <tr>
						<td class="w70 t_l">房源等级：</td>
                        <td><?php echo $config['house_grade'][$data_info['house_grade']]; ?></td>
						<td class="w70 t_l">房屋结构：</td>
                        <td><?php echo $config['house_structure'][$data_info['house_structure']]; ?></td>
						<td class="w70 t_l">看房时间：</td>
                        <td><?php echo $config['read_time'][$data_info['read_time']]; ?></td>
                    </tr>
                    <?php }else{?>
                    <?php if($data_info['sell_type'] == 2 || $data_info['sell_type'] == 3 || $data_info['sell_type'] == 4){?>
                    <tr>
                        <?php if($data_info['sell_type'] == 2){ ?>
                        <td class="w70 t_l">车位数量：</td>
                        <td class="w170"><?php if($data_info['park_num']){
                            echo $data_info['park_num'].'个';
                        }
                        ?></td>
                        <?php }elseif($data_info['sell_type'] == 4){ ?>
                        <td class="w70 t_l">可分割：</td>
                        <td class="w170"><?php if($data_info['division']==1){echo "是";}elseif($data_info['division']==2){echo "否";} ?></td>
                        <td class="w70 t_l">级别：</td>
                        <td><?php if($data_info['office_trade']){echo $config['office_trade'][$data_info['office_trade']];} ?></td>
                        <?php }elseif($data_info['sell_type'] == 3){ ?>
                        <td class="w70 t_l">可分割：</td>
                        <td class="w170"><?php if($data_info['division']==1){echo "是";}elseif($data_info['division']==2){echo "否";} ?></td>

                        <?php }else{ ?>
                        <td class="w70 t_l"></td>
                        <td class="w170"></td>
                        <td class="w70 t_l"></td>
                        <td></td>
                        <?php } ?>
                    </tr>
                    <?php }?>
                    <?php }?>

                    <?php if($data_info['sell_type'] == 1 || $data_info['sell_type'] == 2 || $data_info['sell_type'] == 3 || $data_info['sell_type'] == 4){ ?>
					<tr>
                        <td class="w70 t_l">车库面积：</td>
                        <td class="w170" style="color:#F75000"><?php echo strip_end_0($data_info['garage_area']);?>平方米</td>
                        <td class="w70 t_l">阁楼面积：</td>
                        <td class="w170" style="color:#F75000"><?php echo strip_end_0($data_info['loft_area']);?>平方米</td>
					</tr>
                    <?php } ?>

					<tr>
						<td class="w70 t_l">是否合作：</td>
                        <td class="w170" style="color:#F75000">
                            <?php if(1 == $data_info['isshare']){
                                    echo '是';
                                }else if(2 == $data_info['isshare']){
                                    echo '审核中';
                                }else{
                                    echo '否';
                                }
                            ?>
                        </td>
					</tr>
					<tr>
                        <td colspan="6">
                            <div class="share">
                                <h1 class="bold">房源合作佣金分配　<span style="color:#f00; font-weight:normal; font-size:12px;">此佣金分配方案仅做参考，具体佣金方案需经纪人线下商定</span></h1>
                                <div>
                                   <table>
                                        <tr>
                                            <td>甲方可获得本次交易租赁双方佣金总金额<span class="highlight bold">50</span><span class="highlight">%</span></td>
                                            <td>乙方可获得本次交易租赁双方佣金总金额<span class="highlight bold">50</span><span class="highlight">%</span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="arrow"></div>
                            </div>
                        </td>
                    </tr>

					<tr>
                        <td class="w70 t_l">委托时间：</td>
                        <td class="w170"><?php echo date('Y-m-d',$data_info['createtime']);?></td>
                        <td class="w70 t_l">跟进时间：</td>
                        <td><?php echo date('Y-m-d H:i:s',$data_info['updatetime']);?></td>
                    </tr>
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
										if($val){
											echo $config['shop_trade'][$val].'、';
										}

                                    }
                                }
                            ?></div>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(1==$is_pub || 5==$is_pub){?>
                    <tr>
                        <td class="w70 t_l">房屋设施：</td>
                        <td><div class="ie-break-all"><?php
                                if($data_info['equipment_arr']){
                                    foreach($data_info['equipment_arr'] as $key => $val)
                                    {
										if($val){ echo $config['equipment'][$val].'、';}

                                    }
                                }
                            ?></div></td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">已同步：</td>
                        <td>
                            <?php
                                if(isset($data_info['is_outside'])){
                                    echo '1'==$data_info['is_outside']?'是':'否';
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">周边环境：</td>
                        <td>
                            <?php
                                if($data_info['setting_arr']){
                                    foreach($data_info['setting_arr'] as $key => $val)
                                    {
										if($val){echo $config['setting'][$val].'、';}
                                    }
                                }
                            ?>
                        </td>
                    </tr>
                    <?php }?>
                   <tr>
					<td class="w70 t_l">房源标题：</td>
					 <td><?php
					 if($data_info['title']){
						  echo $data_info['title'];
					 }
					?></td>
					</tr>
					<tr>
					<td class="w70 t_l">房源描述：</td>
					<td><div class="ie-break-all"><?php if($data_info['bewrite']){
						 echo $data_info['bewrite'];
					 }?></div></td>
					</tr>
					<tr>
					<td class="w70 t_l">标签：</td>
                    <td>
                        <div class="ie-break-all"><?php
                            if($data_info['rent_tag_arr']){
                                foreach($data_info['rent_tag_arr'] as $key => $val)
                                {
                                    if($val)
                                    {
                                        echo $config['rent_tag'][$val].'、';
                                    }
                                }
                            }
                        ?></div>
                    </td>
					</tr>
                    <tr>
                        <td class="w70 t_l">备注：</td>
                        <td><div class="ie-break-all"><?php echo $data_info['remark'];?></div></td>
                    </tr>
					<?php }?>
				</table>
				<table class="table">
					<?php if(2==$is_pub||3==$is_pub || $is_pub == 6){?>
					<tr>
                        <td colspan="6">
                            <div class="share">
                                <h1 class="bold">房源合作佣金分配　<span style="color:#f00; font-weight:normal; font-size:12px;">此佣金分配方案仅做参考，具体佣金方案需经纪人线下商定</span></h1>
                                <div>
                                   <table>
                                        <tr>
                                            <td>甲方可获得本次交易租赁双方佣金总金额<span class="highlight bold">50</span><span class="highlight">%</span></td>
                                            <td>乙方可获得本次交易租赁双方佣金总金额<span class="highlight bold">50</span><span class="highlight">%</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
					<?php }?>
					<tr class="clear-line">
					<td class="w80 t_l">委托经纪人：</td>
					<td class="w170">
					<?php echo $broker_agency_name['truename'];?>
					</td>
					<td class="w70 t_l">委托门店：</td>
					<td class="w170">
					<?php echo $broker_agency_name['agency_name'];?>
                    <?php if(!empty($company_name)){
                        echo '('.$company_name.')';
                    }?>
					</td>
					<td class="w70 t_l">联系电话：</td>
					<td class="w170">
                        <?php if($is_phone_show){?>
                            <?php  echo $broker_agency_name['phone'];?>
                        <?php }else{?>
                        <span style="color:red;">提交申请后方能查看</span>
                        <?php }?>
					</td>
					</tr>
                </table>
            </div>
            <?php }?>
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
                            <td class="w170">
                                <strong class="color js_y_hide" id="dong">
                                    <?php
                                    if('1'==$data_info['is_public']){
                                        echo $data_info['dong'];
                                    }else{
                                        echo ('1'==$is_secrecy_information)?'***':$data_info['dong'];
                                    }
                                    ?>
                                </strong>
                                <input style="display:none;" class="input_text input_text_r w60" name="dong" id="dong_input" value="<?php echo $data_info['dong'];?>" type="text">
                            </td>
                            <td class="w70 t_l">单元：</td>
                            <td>
                                <strong class="color js_y_hide" id="unit">
                                <?php
                                    if('1'==$data_info['is_public']){
                                        echo $data_info['unit'];
                                    }else{
                                        echo ('1'==$is_secrecy_information)?'***':$data_info['unit'];
                                    }
                                ?>
                                </strong>
                                <input style="display:none;" class="input_text input_text_r w60" name="unit" id="unit_input" value="<?php echo $data_info['unit'];?>" type="text">
                            </td>
                        </tr>
                        <tr>
                            <td class="w70 t_l">门牌：</td>
                            <td class="w170">
                                <strong class="color js_y_hide" id="door">
                                    <?php
                                        if('1'==$data_info['is_public']){
                                            echo $data_info['door'];
                                        }else{
                                            echo ('1'==$is_secrecy_information)?'***':$data_info['door'];
                                        }
                                    ?>
                                </strong>
                                <input style="display:none;" class="input_text input_text_r w60" name="door" id="door_input" value="<?php echo $data_info['door'];?>" type="text">
                            </td>
                            <td class="w70 t_l">业主姓名：</td>
                            <td class="w170">
                                <strong class="color js_y_hide" id="owner">
                                    <?php
                                        if('1'==$data_info['is_public']){
                                            echo $data_info['owner'];
                                        }else{
                                            echo '***';
                                        }
                                    ?>
                                </strong>
                                <input style="display:none;" class="input_text input_text_r w60" name="owner" id="owner_input" value="<?php echo $data_info['owner'];?>" type="text">
                            </td>
                        </tr>
                        <tr>
                            <td class="w70 t_l">身份证号：</td>
                            <td class="w170">
                                <strong class="color js_y_hide" id="idcare"><?php echo $data_info['idcare'];?></strong>
                                <input style="display:none;" class="input_text input_text_r w130" name="idcare" id="idcare_input" value="<?php echo $data_info['idcare'];?>" type="text">
                            </td>
                            <td class="w70 t_l">面积：</td>
                            <td class="w170"><?php echo strip_end_0($data_info['buildarea']);?>平方米</td>
                            <td class="w70 t_l">租金：</td>
                            <td> <?php echo strip_end_0($data_info['price']);if($data_info['price_danwei'] == 1){echo "元/㎡*天";}else{echo "元/月";}?></td>
                        </tr>
                        <tr>
                            <td class="w70 t_l">业主电话：</td>
                            <td>
                                <strong class="color js_y_hide" id="telnos">
                                    <?php
                                        if('1'==$data_info['is_public']){
                                            echo $data_info['telno1'];
                                        }else{
                                            echo '***';
                                        }
                                    ?>
                                </strong>
                                <input style="display:none;" class="input_text input_text_r w80" name="telno1" id="telno1_input" value="<?php echo $data_info['telno1'];?>" type="text">
                            </td>
                            <td class="w70 t_l">业主电话2：</td>
                            <td>
                                <strong class="color js_y_hide" id="telnos2">
                                    <?php
                                        if('1'==$data_info['is_public']){
                                            echo $data_info['telno2'];
                                        }else{
                                            echo '***';
                                        }
                                    ?>
                                </strong>
                                <input style="display:none;" class="input_text input_text_r w80" name="telno2" id="telno2_input" value="<?php echo $data_info['telno2'];?>" type="text">
                            </td>
                            <td class="w70 t_l">业主电话3：</td>
                            <td>
                                <strong class="color js_y_hide" id="telnos3">
                                    <?php
                                        if('1'==$data_info['is_public']){
                                            echo $data_info['telno3'];
                                        }else{
                                            echo '***';
                                        }
                                    ?>
                                </strong>
                                <input style="display:none;" class="input_text input_text_r w80" name="telno3" id="telno3_input" value="<?php echo $data_info['telno3'];?>" type="text">
                            </td>
                        </tr>
                       <tr>
                            <td class="w70 t_l">底价：</td>
                            <td class="w170">
                                <strong class="color js_y_hide" id="lowprice">
                                    <?php
                                        if('1'==$data_info['is_public']){
                                            echo intval($data_info['lowprice']).'万元';
                                        }else{
                                            echo '***';
                                        }
                                    ?>
                                </strong>
                                <p id="lowprice_input" style="display:none;">
                                    <input class="input_text input_text_r w60" name="lowprice" value="<?php echo intval($data_info['lowprice']);?>" type="text"> 万元
                                </p>
                            </td>
                        </tr>
                    </table>
                    <p class="link_btn_b">
                        <?php if(!('1'==$data_info['is_public'])){ ?>
                            <?php if( $data_info['lock']==0 || in_array( $broker_id , array($data_info['broker_id'],$data_info['lock']) ) ){ ?>
                            <a id="show_baomi_button" onClick="show_rentbaomi_info('<?php echo $data_info['id'];?>','rent')" href="javascript:void(0)"><span class="iconfont">&#xe610;</span> 查看保密信息</a>
                            <a style="display:none;" id="modify_baomi_button" onClick="modify_baomi_info(<?php echo $modify_secret_per; ?>)" href="javascript:void(0)"><span class="iconfont">&#xe610;</span> 编辑保密信息</a>
                            <a style="display:none;" id="modify_baomi_submit_button" onClick="submit_baomi_info(<?php echo $data_info['id']; ?>,'rent')" href="javascript:void(0)"><span class="iconfont">&#xe610;</span> 保存修改</a>
                            <?php }else{ ?>
                            很遗憾，您无权查看相关保密信息。
                            <?php } ?>
                        <?php } ?>
                    </p>
                </div>
                <div class="clearfix pop_fg_fun_box">
                    <div class="text left"><span class="fg">总查阅次数：<a href="#"><?php echo $user_num;?></a></span><span class="fg">今日查阅次数：<a href="#"><?php echo
					$today_num?></a></span></div>
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
			<?php }?>
            <?php if($tab==3){
					ob_flush();
					flush();
			?>
            <div class="js_d inner inner02"  style="display:block">
                <p class="pic-title"><a class="on" href="javascript:void(0);">室内图（<?php echo $shineipic_count;?>）</a> |　<a href="javascript:void(0);">户型图（<?php echo $huxingpic_count; ?>）</a></p>
                <div class="show_house_pic clearfix">
                    <div class="pic" style="overflow:hidden;">
						<img style="height:340px;" src="<?php if(!empty($shineipic[0])){echo changepic($shineipic[0]['url']);}else{echo MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg';}?>" style="height:340px;">
					</div>
                    <div class="small_pic">
                        <div class="prev" style="float:right;"><span class="iconfont">&#xe607;</span></div>
                        <div class="list" style="float:right;">
                            <ul class="clearfix">
                                <?php
                                    if($shineipic)
                                    {
                                        foreach($shineipic as $key => $val)
                                        {
                                            echo '<li class="item" style="float:right;display:inline;"><img src="'.$val['url'].'" height="54" width="72"></li>';
                                        }
                                    }else{
                                        for($i=0;$i<5;$i++){
                                            echo '<li class="item" style="float:right;display:inline;"><img src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg" height="54" width="72"></li>';
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="next iconfont" style="float:right;"><span class="iconfont">&#xe607;</span></div>
                    </div>
                </div>
                <div class="show_house_pic clearfix" style="display:none;">
                    <div class="pic" style="overflow:hidden;">
                        <img style="height:340px;" alt="" src="<?php if(!empty($huxingpic[0])){ echo changepic($huxingpic[0]['url']);}else{echo MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg';}?>" style="height:340px;">
                    </div>
                    <div class="small_pic">
                        <div class="prev prev_click" style="float:right;"><span class="iconfont">&#xe607;</span></div>
                        <div class="list" style="float:right;">
                            <ul class="clearfix">
                                 <?php
                                    if($huxingpic)
                                    {
                                        foreach($huxingpic as $key => $val)
                                        {
                                            echo '<li class="item" style="float:right;display:inline;"><img src="'.$val['url'].'" height="54" width="72" ></li>';
                                        }
                                    }else{
                                        for($i=0;$i<5;$i++){
                                            echo '<li class="item" style="float:right;display:inline;"><img src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg" height="54" width="72"></li>';
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="next iconfont" style="float:right;"><span class="iconfont">&#xe607;</span></div>
                    </div>
                </div>
            </div>
			<?php }?>
			<?php if($tab == 7){?>
			<div class="js_d inner"  style="display:block">
				<div id="youkuplayer" style="width:720px;height:360px"></div>
				<script type="text/javascript" src="http://player.youku.com/jsapi">
					player = new YKU.Player('youkuplayer',{
						styleid: '0',
						client_id: 'e4eaa03b0e105be9',
						vid: '<?=$data_info['video_id']?>'
					});
				</script>
			</div>
			<?php } ?>
            <?php if($tab==4){?>
            <div class="js_d inner"  style="display:block">
                <table class="table">
                    <tr>
                        <td class="w70 t_l">楼盘名称：</td>
                        <td class="w170"><?php echo $data_info['block_name'];?></td>
                        <td class="w70 t_l">区属：</td>
                        <td class="w170"><?php
							if(!empty($data_info['district_name'])){
								echo $data_info['district_name'];
							}
						?></td>
                        <td class="w70 t_l">板块：</td>
                        <td><?php
						if(!empty($data_info['street_name'])){
							echo $data_info['street_name'];
						}

						?></td>
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
						?></td>
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
                        }?></td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">绿化率：</td>
                        <td class="w170"><?php if($community_info['green_rate'] > 0){
                            echo ($community_info['green_rate']*100).'%';
                        }?></td>
                        <td class="w70 t_l">容积率：</td>
                        <td class="w170"><?php if($community_info['plot_ratio'] > 0){
                            echo $community_info['plot_ratio'];
                        }?></td>
                        <td class="w70 t_l">物业费：</td>
                        <td><?php if($community_info['property_fee'] > 0){
                            echo $community_info['property_fee'].'元/月•平米';
                        }?></td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">总栋数：</td>
                        <td class="w170"><?php if($community_info['build_num']) {
                            echo $community_info['build_num'];
                        }?></td>
                        <td class="w70 t_l">总户数：</td>
                        <td class="w170"><?php if($community_info['total_room']){
                            echo $community_info['total_room'];
                        }?></td>
                        <td class="w70 t_l">楼层状况：</td>
                        <td><?php echo $community_info['floor_instruction'];?></td>
                    </tr>
				</table>
				<table class="table">
                    <tr>
                        <td class="w70 t_l">楼盘简介：</td>
                        <td>
						<div class="ie-break-all"><?php echo $community_info['introduction'];?>
						</div>
						</td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">周边环境：</td>

                        <td>
						<div class="ie-break-all">
						<?php echo $facilities;?>
						</div>
						</td>
                    </tr>
                    <tr>
                        <td class="w70 t_l">图片：</td>
                        <td>
						<div class="ie-break-all">
						<?php
						foreach($cmt_arr as $key=>$val){
							echo '<img alt="" src="'.$val['image'].'" height="105" width="140" style="float:left; margin:0 10px 10px 0;">';
						}
						?>
						</div>
						</td>
                    </tr>
                </table>
            </div>
			<?php }?>
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
                    <div class="title"><span class="fg">本房源被申请合作次数：<?php echo $brower_list3['cooperate_num'] ?></span></div>
                    <?php if($brower_list3['cooperate_pages']>0){?>
                    <div class="get_page">
                        <span><strong id='cooperate_thispage'><?php echo $brower_list3['cooperate_page'];?></strong>/<?php echo $brower_list3['cooperate_pages'];?>页</span>
                        <input type="hidden" name="cooperate_pg" value="1"/>
                        <?php if($brower_list3['cooperate_pages']!=1){?>
                        <a href="javascript:void(0)" id="cooperate_prev_page">上一页</a><a href="javascript:void(0)" id="cooperate_next_page">下一页</a>
                        <?php }?>
                        <span>共<?php echo $brower_list3['cooperate_num'];?>条</span>
                    </div>
                    <?php }?>
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
			<?php }?>

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

		<?php if($is_pub == 1){  ?>
        <div class="tab_pop_bd">
            <?php if(1==$data_info['is_public']){ ?>
                <div>
                    <a class="btn-lan" style="position:relative;z-index: 11" href="javascript:void(0);" onclick="parent.xq_openfollow('rent',<?php echo $data_info['id'] ?>,1)"><span>房源跟进</span></a>
                </div>
            <?php }else{ ?>
                <div>
                    <a id="rent_bianji" class="btn-hui1 btn-left" href="/rent/modify/<?php echo $data_info['id'];?>/<?=$app_id?>" target="_parent"><span>编辑</span></a>
                    <?php if($app_id){?>
                    <a class="btn-hui1 btn-left" href="javascript:void(0);"  onclick="adel('appoint_center',<?php echo $data_info['id'];?>,'app_sell',<?=$app_id?>)"><span>删除</span></a>
                    <?php }else{?>
                    <a id="rent_zhuxiao" class="btn-hui1 btn-left"  onclick="xdel('rent',<?php echo $data_info['id'];?>,'lists',<?php echo $data_info['is_outside'];?>,<?php echo $data_info['nature_per'];?>)" target="_parent" href="javascript:void(0);"><span>注销</span></a>
                    <?php } ?>
                    <?php if($shineipic_count || $huxingpic_count ){?>
                    <form action="<?php echo MLS_SIGN_URL;?>/rent/download_pic/<?php echo $data_info['id']; ?>" method="get">
                    <input class="zws_download" type="submit" value="下载所有图片"/>
                    </form>
                    <?php } ?>
                </div>
                <div>
                    <a id="rent_allocate_house" class="btn-lan" href="javascript:void(0);" onclick="parent.xqhouse('rent',<?php echo $data_info['id']?>)"><span>分配房源</span></a>
                    <a id="rent_house_share_tasks" href="javascript:void(0);"class="btn-lan" onclick="parent.xqtasks('rent',1,<?php echo $data_info['id']?>)"><span>分配任务</span></a>
                    <a class="btn-lan" style="position:relative;z-index: 11" href="javascript:void(0);" onclick="parent.xq_openfollow('rent',<?php echo $data_info['id'] ?>,1)"><span>房源跟进</span></a>
                    <a id="rent_house_match" class="btn-lan" href="javascript:void(0);" onclick="parent.open_match('rent',0,<?php echo $data_info['id'];?>)"><span>智能匹配</span></a>
                    <!--                    <a class="btn-lan" onclick="parent.house_publish('rent',<?php echo $data_info['id']; ?> )" href="javascript:void(0);"><span>群发房源</span></a>-->
                </div>
            <?php } ?>
        </div>
		<?php }else if($is_pub == 2){ ?>
		<div class="tab_pop_bd">
            <div>
                <?php if(in_array($data_info['id'] , $num_id)){ ?>
                    <a class="collect-btn collect-success" href="javascript:void(0);" >已收藏</a>
                <?php }else{ ?>
                    <a class="collect-btn collect" href="javascript:void(0);" id = "cang_info<?php echo $data_info['id']?>"onclick="shcang('house_collect','rent_house',<?php echo $data_info['id']; ?>,'info')">收藏房源</a>
                <?php } ?>

            </div>
            <div>
                <a class="btn-lan" href="javascript:void(0);" onclick="parent.open_match('rent' ,2,<?php echo $data_info['id'];?>)"><span>智能匹配</span></a>
                <?php if('1'==$open_cooperate){?>
                <a class="btn-lan" href="javascript:void(0);" onclick="parent.cooperate_house('rent',<?php echo $data_info['id'];?>,<?php echo $data_info['broker_id'];?>);"><span>合作申请</span></a>
                <?php }?>
            </div>
        </div>
		<?php } ?>

        <?php } ?>
    </div>
</div>

<div class="mask_bg2" id="mask_bg2">dddd2</div>
<style type="text/css">
     .mask_bg2{width:100%;height: 34%;float:left;display:inline;background:#000;display:none;position:absolute;left:0;bottom:0;z-index: 9;opacity:0;filter:alpha(opacity=0);filter: progid:DXImageTransform.Microsoft.Alpha(opacity=0);}
</style>
<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>
<script type="text/javascript">
$(function(){
    //保密与跟进进程，判断是否直接弹出弹框
    var alert_house_id = <?php echo $alert_house_id; ?>;
    if(alert_house_id > 0){
        //所有按钮置灰，只保留房源跟进
        $('#rent_house_match').attr('class','btn-hui fr');
        $('#rent_house_share_tasks').attr('class','btn-hui fr');
        $('#rent_allocate_house').attr('class','btn-hui fr');
        $('#rent_zhuxiao').hide();
        $('#rent_bianji').hide();
        $('.mask_bg2').show();
    }

	$('.pic-title').find('a').click(function(){
		$('.pic-title').find('a').removeClass('on');
		$(this).addClass('on');
		$('.show_house_pic').hide();
		$('.show_house_pic').eq($(this).index()).show();
	});//房源图片  TAB

	$("#a_ratio").blur(function(){
		var a_ratio=$("#a_ratio").val();
		var b_ratio=100-a_ratio;
		$("#b_ratio").val("");
		if(!$.isNumeric(a_ratio)){
			$("#b_ratio").val(b_ratio);
		}
	})
});
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
            ajax_url = '/rent/ajax_get_cooperate_log/'
        } else {
            ajax_url = '/rent/ajax_get_view_log/'
        }
    } else {
        ajax_url = '/rent/ajax_get_brower_log/'
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


</script>
