<div id="js_pop_publish_house" class="pop_box_g pop_see_inform pop_publish_house" style='display: block;border:none'>
    <div class="hd">
        <div class="title">群发房源</div>
        <!--<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>-->
    </div>
    <div class="mod">
        <div class="inform_inner">
            <?php if($list){ 
                   foreach($list as $key => $val) {
                ?>
			<!-- h3 class="house_up_title"><?php echo $val['title'];?></h3  -->
            <div class="house_up_list">
                <label class="label">
                    <span class="text">区域：</span>
                    <input  class="input_text2" type="text" value="<?php echo $district[$val['district_id']]['district'];?>">
                </label>
                <label class="label">
                    <span class="text">板块：</span>
                    <input  class="input_text2" type="text" value="<?php echo $street[$val['street_id']]['streetname'];?>">
                </label>
                <label class="label">
                    <span class="text">楼盘名称：</span>
                    <input  class="input_text2 input_text3" type="text" value="<?php echo $val['block_name'];?>">
                </label>
                <label class="label">
                    <span class="text">楼盘地址：</span>
                    <input  class="input_text2 input_text4" type="text" value="<?php echo $val['address'];?>">
                </label>
                <?php if($type == 'sell'){ ?>
                <label class="label">
                    <span class="text">总价：</span>
                    <input  class="input_text2 " type="text" value="<?php echo strip_end_0($val['price']);?>万元">
                </label>
                <?php }elseif($type == 'rent'){ ?>
                <label class="label">
                    <span class="text ">租金：</span>
                    <input  class="input_text2 " type="text" value="<?php echo strip_end_0($val['price']); if($val['price_danwei'] == 1){echo "元/㎡*天";}else{echo "元/月";}?>">
                </label>
                <?php } ?>      
                <label class="label">
                    <span class="text">楼层：</span>
                    <input  class="input_text2" type="text" value="<?php echo $val['floor'];?><?php if($val['floor_type'] == 2){ echo "-".$val['subfloor'];}?>/<?php echo $val['totalfloor'];?>">
                </label>
                <label class="label">
                    <span class="text  text2">物业类型：</span>
                    <input  class="input_text2 input_text3" type="text" value="<?php echo $config['sell_type'][$val['sell_type']]; ?>">
                </label>          
                
                <label class="label">
                    <span class="text text2">面积：</span>
                    <input  class="input_text2 input_text4" type="text" value="<?php echo strip_end_0($val['buildarea']); ?>平方米">
                </label>
                <label class="label">
                    <span class="text">朝向：</span>
                    <input  class="input_text2" type="text" value="<?php echo $config['forward'][$val['forward']];?>">
                </label>
                <label class="label">
                    <span class="text">装修：</span>
                    <input  class="input_text2" type="text" value="<?php echo $config['fitment'][$val['fitment']];?>">
                </label>
                <label class="label">
                    <span class="text text2">建筑年代：</span>
                    <input  class="input_text2 input_text3" type="text" value="<?php echo $val['buildyear'];?>年">
                </label>
                <?php if($val['sell_type'] <= 2 ){?>
                <label class="label">
                    <span class="text text2">户型：</span>
                    <input  class="input_text2 input_text4" type="text" value="<?php echo $val['room'].'室'.$val['hall'].'厅'.$val['toilet'].'卫';?>">
                </label>
                <?php } ?>
            </div>
            <?php                 
                   }
                }
            ?>
            <!--<div class="house_up_list">
                <?php if($list){ 
                   foreach($list as $key => $val) {
                ?>
                <div class="item"><span class="id_house"><?php echo $val['id'];?></span>|<span class="info_house"><?php echo $val['block_name'].$val['room'].'室'.$val['hall'].'厅'.$val['toilet'].'卫'.strip_end_0($val['buildarea']).'㎡'.$val['buildyear'];?></span><span class="pirce_house"><?php echo strip_end_0($val['price']);?>万</span></div>
                <?php                 
                   }
                }else{
                ?>
                <div class="item">请选择群发房源</div>
                <?php } ?>
            </div>-->
         
         	<p class="f_s_website">发布站点：</p>
         	<div class="s_website">
            	<div class="s_all"><label><input style="vartical-align:middle; margin-top:-4px;" type="checkbox" onChange="checkboxAll(this,'js_s_o')">&nbsp;&nbsp;全选</label>|</div>
                <input type='hidden' name='house_id' id='house_id' value='<?php echo $house_id;?>'>
                <div class="s_o" id ='sitename'> 
                    <?php if($siteinfo){
                        foreach($siteinfo as $key => $val){
                    ?>                    
                    <label><input type="checkbox" name='site' <?php if(in_array($val['id'],$site_arr)){ echo "checked"; }?> value='<?php echo $val['id'];?>' class="js_s_o"><?php echo $val['name'];?></label>
                    <?php       
                        }
                    }else{
                    ?>
                    <label><a href="/site_set/index" target='_parent' class="l">配置站点</a></label>   
                    <?php }?>
               </div>
            </div>
         	<div class="m_bd">
          		<button type="button" class="btn-lv1 btn-left" onClick="group_publishing('sell');">立即发布</button>
                <button type="button" class="btn-hui1" onclick ="window.parent.close_group_publish();">取消</button>
            </div>
        </div>
    </div>
</div>