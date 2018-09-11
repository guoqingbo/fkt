<div class="pop_box_g pop_see_inform pop_see_info_deal" id="js_pop_see_info_house" style="height:448px;">
    <div class="hd">
        <div class="title">成交房源详情</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod mod_bg" style="padding-right:0;">
        <div class="inform_inner" style="overflow-x:hidden; overflow-y:scroll; height:385px; width:585px;">


            <table class="deal_table deal_table_see">
                <tr>
                    <th>区属：</th>
                    <td><?php echo $data_info['house_info']['district_name'];?></td>
                    <th>板块：</th>
                    <td><?php echo $data_info['house_info']['street_name'];?></td>
                    <th>楼盘：</th>
                    <td><?php echo $data_info['house_info']['block_name'];?></td>
                </tr>
                <tr>
                    <th>楼盘地址：</th>
                    <td><?php echo $data_info['house_info']['address'];?></td>
                    <th>用途：</th>
                    <td><?php echo $config['sell_type'][$data_info['house_info']['sell_type']]; ?></td>
                    <th>类型：</th>
                    <td>
                        <?php 
                            if($data_info['house_info']['sell_type'] == 2 && $data_info['house_info']['villa_type']){
                                echo $config['villa_type'][$data_info['house_info']['villa_type']];
                            }elseif($data_info['house_info']['sell_type'] == 3 && $data_info['house_info']['shop_type']){
                                echo $config['shop_type'][$data_info['house_info']['shop_type']];
                            }elseif($data_info['house_info']['sell_type'] == 4 && $data_info['house_info']['office_type']){
                                echo $config['office_type'][$data_info['house_info']['office_type']];
                            }elseif($data_info['house_info']['sell_type'] == 1 && $data_info['house_info']['house_type']){
                                echo $config['house_type'][$data_info['house_info']['house_type']];
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <?php if($type == "sell"){?>
                    
                    <th>总价：</th>
                    <td><?php echo strip_end_0($data_info['house_info']['price']); ?>万</td>
                    <th>单价：</th>
                    <td><?php echo strip_end_0($data_info['house_info']['avgprice']); ?>元/㎡</td>
                    
                    <?php }elseif($type == "rent"){?>
                    
                    <th>租金：</th>
                    <td><?php echo strip_end_0($data_info['house_info']['price']); ?>元/月</td>
                    <th>委托类型：</th>
                    <td><?php echo $config['rententrust'][$data_info['house_info']['rententrust']]; ?></td>
                    
                    <?php }?>
                    
                    <th>物业费：</th>
                    <td>
                        <?php 
						if($data_info['house_info']['strata_fee']){
							echo strip_end_0($data_info['house_info']['strata_fee']);
						}
						?><?php if($data_info['house_info']['sell_type']==1){echo "元/月/m²";}else{echo "元/月";}?>
                    </td>
                </tr>

                <tr>
                    <th>朝向：</th>
                    <td><?php echo $config['forward'][$data_info['house_info']['forward']]; ?></td>
                    <th>户型：</th>
                    <td><?php echo $data_info['house_info']['room'];?>室<?php echo $data_info['house_info']['hall'];?>厅<?php echo $data_info['house_info']['toilet'];?>卫<?php echo $data_info['house_info']['kitchen'];?>厨<?php echo $data_info['house_info']['balcony'];?>阳台</td>
                    <th>面积：</th>
                    <td><?php echo strip_end_0($data_info['house_info']['buildarea']); ?>㎡</td>
                </tr>

                <tr>
                    <th>楼层：</th>
                    <td><?php echo $data_info['house_info']['floor'];?><?php if($data_info['house_info']['floor_type'] == 2){ echo "-".$data_info['house_info']['subfloor'];}?>/<?php echo $data_info['house_info']['totalfloor'];?></td>
                    <th>装修：</th>
                    <td><?php echo $config['fitment'][$data_info['house_info']['fitment']]; ?></td>
                    <th>发布时间：</th>
                    <td><?php echo date('Y.m.d',$data_info['house_info']['createtime']);?></td>
                </tr>
                <tr>
                    <th>建筑年代：</th>
                    <td><?php echo $data_info['house_info']['buildyear']; ?></td>
                    <th>联系人：</th>
                    <td><?php echo $data_info['house_info']['owner']; ?></td>
                    <th>电话：</th>
                    <td><?php echo $data_info['house_info']['telnos']; ?></td>
                </tr>
			</table>
			<p class="clearfix td_5"><span><?php echo $data_info['house_info']['remark'];?></span>备注：</p>
			<table class="deal_table deal_table_see">
                <tr class="last_tr">
                    <th>价格：</th>
                    <td><?php echo $data_info['price']; ?><?php if($type == "sell"){echo "万";}elseif($type == "rent"){echo "元/月";}?></td>
                    <th>姓名：</th>
                    <td><?php echo $data_info['name']; ?></td>
                    <th>电话：</th>
                    <td><?php echo $data_info['tel']; ?></td>
                </tr>
            </table>

            <div class="clear">&nbsp;</div>
            <button class="btn-lv1 btn-mid JS_Close">确定</button>

        </div>

    </div>
</div>

<script>
$(function() {
    openWin('js_pop_see_info_house');
    <?php if($flag == 1){?>
    add_data();
    <?php }?>
    
    $(".JS_Close").click(function(){
        $(window.parent.document).find('#js_pop_add_info_house').hide();
		$(window.parent.document).find('#' + 'GTipsCover' + 'js_pop_add_info_house').remove();
    });
});
function add_data(){
    var html ="<tr class='bg'>"
      			+"<td class='c7'><div class='info'><?php echo $config['sell_type'][$data_info['house_info']['sell_type']]; ?></div></td>"
                +"<td class='c6'><div class='info'><?php echo $data_info['house_info']['district_name'];?></div></td>"
                +"<td class='c6'><div class='info'><?php echo $data_info['house_info']['street_name'];?></div></td>"
                +"<td class='c12'><div class='info'><?php echo $data_info['house_info']['block_name'];?></div></td>"
                +"<td class='c7'>"
                    +"<div class='info'>"
                        +"<?php echo $data_info['house_info']['room'];?>室<?php echo $data_info['house_info']['hall'];?>厅<?php echo $data_info['house_info']['toilet'];?>卫<?php echo $data_info['house_info']['kitchen'];?>厨<?php echo $data_info['house_info']['balcony'];?>阳台"
                    +"</div>"
                +"</td>"
                +"<td class='c6'>"
                    +"<div class='info'>";
                        <?php if($data_info['house_info']['sell_type'] == 2 && $data_info['house_info']['villa_type']){?>
                               html += "<?php echo $config['villa_type'][$data_info['house_info']['villa_type']];?>";
                        <?php }elseif($data_info['house_info']['sell_type'] == 3 && $data_info['house_info']['shop_type']){?>
                               html += "<?php  echo $config['shop_type'][$data_info['house_info']['shop_type']];?>";
                        <?php }elseif($data_info['house_info']['sell_type'] == 4 && $data_info['house_info']['office_type']){?>
                                html += "<?php echo $config['office_type'][$data_info['house_info']['office_type']];?>";
                        <?php }elseif($data_info['house_info']['sell_type'] == 1 && $data_info['house_info']['house_type']){?>
                                html += "<?php echo $config['house_type'][$data_info['house_info']['house_type']];?>";
                        <?php }?>
                        
                     html += "</div>"
                +"</td>"
  				+"<td class='c5'><div class='info'><?php echo $config['forward'][$data_info['house_info']['forward']]; ?></div></td>"
                +"<td class='c5'>"
                    +"<div class='info'>"
                        +"<?php echo $data_info['house_info']['floor'];?><?php if($data_info['house_info']['floor_type'] == 2){ echo "-".$data_info['house_info']['subfloor'];}?>/<?php echo $data_info['house_info']['totalfloor'];?>"
                    +"</div>"
                +"</td>"
                +"<td class='c6'><div class='info'><?php echo $config['fitment'][$data_info['house_info']['fitment']]; ?></div></td>"
                +"<td class='c7'><div class='info'><?php echo strip_end_0($data_info['house_info']['buildarea']); ?></div></td>"
                +"<td class='c7'><div class='info'><?php echo strip_end_0($data_info['house_info']['price']); ?></div></td>"
                +"<td class='c7'><div class='info'><?php echo strip_end_0($data_info['price']); ?></div></td>"
                +"<td class='c12'><div class='info'><?php echo date('Y-m-d H:i:s',$data_info['createtime']);?></div></td>"
                +'<td><div class="info"><a onclick="open_js_pop_see_info_house('+"'my_deal_<?php echo $type;?>'"+',<?php echo $data_info['id']; ?>);" href="javascript:void(0)">查看</a></div></td>'
            +"</tr>";
    $(window.parent.document).find('#js_inner .table').prepend(html);
}
</script>

