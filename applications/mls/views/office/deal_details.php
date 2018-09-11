<div id="js_pop_see_info_deal" class="pop_box_g pop_see_inform pop_see_info_deal"  style="display: block;height:410px;">
    <div class="hd">
        <div class="title">查看详情</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">


            <table class="deal_table">
                <tr>
                    <th>楼盘名称：</th>
                    <td><?php echo $data_info['house_info']['block_name'];?></td>
                    <th>区属：</th>
                    <td><?php echo $data_info['house_info']['district_name'];?></td>
                </tr>
               	<tr>
                    <th>板块：</th>
                    <td><?php echo $data_info['house_info']['street_name'];?></td>
                    <th>楼盘地址：</th>
                    <td><?php echo $data_info['house_info']['address'];?></td>
                </tr>

               	<tr>
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
                    <?php if($data_info['type'] == 1){?>
                    
                    <th>总价：</th>
                    <td><?php echo strip_end_0($data_info['house_info']['price']); ?>万</td>
                    <th>单价：</th>
                    <td><?php echo strip_end_0($data_info['house_info']['avgprice']); ?>元/㎡</td>
                    
                    <?php }elseif($data_info['type'] == 2){?>
                    
                    <th>租金：</th>
                    <td><?php echo strip_end_0($data_info['house_info']['price']); ?>元/月</td>
                    <th>委托类型：</th>
                    <td><?php echo $config['rententrust'][$data_info['house_info']['rententrust']]; ?></td>
                    
                    <?php }?>
                </tr>
                <tr>
                    <th>朝向：</th>
                    <td><?php echo $config['forward'][$data_info['house_info']['forward']]; ?></td>
                    <th>户型：</th>
                    <td><?php echo $data_info['house_info']['room'];?>室<?php echo $data_info['house_info']['hall'];?>厅<?php echo $data_info['house_info']['toilet'];?>卫<?php echo $data_info['house_info']['kitchen'];?>厨<?php echo $data_info['house_info']['balcony'];?>阳台</td>
                </tr>
                <tr>
                    <th>面积：</th>
                    <td><?php echo strip_end_0($data_info['house_info']['buildarea']); ?>㎡</td>
                    <th>楼层：</th>
                    <td><?php echo $data_info['house_info']['floor'];?><?php if($data_info['house_info']['floor_type'] == 2){ echo "-".$data_info['house_info']['subfloor'];}?>/<?php echo $data_info['house_info']['totalfloor'];?></td>
                </tr>
                <tr>
                    <th>发布时间：</th>
                    <td><?php echo date('Y.m.d',$data_info['house_info']['createtime']);?></td>
                    <th>装修：</th>
                    <td><?php echo $config['fitment'][$data_info['house_info']['fitment']]; ?></td>
                </tr>

                <tr>
                    <th>联系人：</th>
                    <td><?php echo $data_info['house_info']['owner']; ?></td>
                    <th>联系电话：</th>
                    <td><?php echo $data_info['house_info']['telnos']; ?></td>
                </tr>
            </table>

            <div class="pirce">成交价<strong class="b"><?php echo $data_info['price']; ?></strong><?php if($data_info['type'] == 1){echo "万";}elseif($data_info['type'] == 2){echo "元/月";}?></div>
        </div>


    </div>
</div>