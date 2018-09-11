<div class="tab_box" id="js_tab_box">
    <?php echo $user_menu;?>
</div>
<div id="js_search_box">
     <div  class="shop_tab_title">
        <?php echo $user_func_menu;?>
        <a class="add_p_rz" onclick="open_js_add_house_p('my_deal_<?php echo $type;?>')"  href="javascript:void(0)"><span>我要添加</span></a>
     </div>
     
</div>
<div class="table_all">
    <div id="js_title" class="title shop_title">
        <table class="table">
           <tr>
              	<td class="c7"><div class="info">物业类型</div></td>
                <td class="c6"><div class="info">区属</div></td>
                <td class="c6"><div class="info">板块</div></td>
                <td class="c12"><div class="info">楼盘名称</div></td>
                <td class="c7"><div class="info">户型</div></td>
                <td class="c6"><div class="info">类型</div></td>
  				<td class="c5"><div class="info">朝向</div></td>
                <td class="c5"><div class="info">楼层</div></td>
                <td class="c6"><div class="info">装修</div></td>
                <td class="c7"><div class="info">面积(㎡)</div></td>
                <td class="c7"><div class="info">报价(<?php if($type == "sell"){echo "W";}elseif($type == "rent"){echo "元/月";}?>)</div></td>
                <td class="c7"><div class="info">成交价(<?php if($type == "sell"){echo "W";}elseif($type == "rent"){echo "元/月";}?>)</div></td>
                <td class="c12"><div class="info">录入时间</div></td>
                <td><div class="info">操作</div></td>
            </tr>
     	</table>
    </div>
    <div id="js_inner" class="inner shop_inner" style="height: 310px;">
        <table class="table">
            <?php 
            if($list)
            {
                foreach($list as $key => $val)
                {
            ?>
            <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?>>
      			<td class="c7"><div class="info"><?php echo $config['sell_type'][$val['house_info']['sell_type']]; ?></div></td>
                <td class="c6"><div class="info"><?php echo $val['house_info']['district_name'];?></div></td>
                <td class="c6"><div class="info"><?php echo $val['house_info']['street_name'];?></div></td>
                <td class="c12"><div class="info"><?php echo $val['house_info']['block_name'];?></div></td>
                <td class="c7">
                    <div class="info">
                        <?php echo $val['house_info']['room'];?>室<?php echo $val['house_info']['hall'];?>厅<?php echo $val['house_info']['toilet'];?>卫<?php echo $val['house_info']['kitchen'];?>厨<?php echo $val['house_info']['balcony'];?>阳台
                    </div>
                </td>
                <td class="c6">
                    <div class="info">
                        <?php 
                            if($val['house_info']['sell_type'] == 2 && $val['house_info']['villa_type']){
                                echo $config['villa_type'][$val['house_info']['villa_type']];
                            }elseif($val['house_info']['sell_type'] == 3 && $val['house_info']['shop_type']){
                                echo $config['shop_type'][$val['house_info']['shop_type']];
                            }elseif($val['house_info']['sell_type'] == 4 && $val['house_info']['office_type']){
                                echo $config['office_type'][$val['house_info']['office_type']];
                            }elseif($val['house_info']['sell_type'] == 1 && $val['house_info']['house_type']){
                                echo $config['house_type'][$val['house_info']['house_type']];
                            }
                        ?>
                    </div>
                </td>
  				<td class="c5"><div class="info"><?php echo $config['forward'][$val['house_info']['forward']]; ?></div></td>
                <td class="c5">
                    <div class="info">
                        <?php echo $val['house_info']['floor'];?><?php if($val['house_info']['floor_type'] == 2){ echo "-".$val['house_info']['subfloor'];}?>/<?php echo $val['house_info']['totalfloor'];?>
                    </div>
                </td>
                <td class="c6"><div class="info"><?php echo $config['fitment'][$val['house_info']['fitment']]; ?></div></td>
                <td class="c7"><div class="info"><?php echo strip_end_0($val['house_info']['buildarea']); ?></div></td>
                <td class="c7"><div class="info"><?php echo strip_end_0($val['house_info']['price']); ?></div></td>
                <td class="c7"><div class="info"><?php echo strip_end_0($val['price']); ?></div></td>
                <td class="c12"><div class="info"><?php echo date('Y-m-d H:i:s',$val['createtime']);?></div></td>
                <td><div class="info"><a onclick="open_js_pop_see_info_house('my_deal_<?php echo $type;?>',<?php echo $val['id']; ?>);" href="javascript:void(0)">查看</a></div></td>
            </tr>
            <?php        
                }
            }else{
            ?>
            <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php }?>
            
            </table>
    </div>
</div>

<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
    <div class="get_page">
    	<?php echo $page_list;?>
    </div>
</div>


<!--成交房源详情-->
<div id="js_pop_see_info_house" class="iframePopBox" style=" width:600px;height: 448px;">
    <!--<a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
	04.08 wty
	-->
    <iframe frameborder="0" scrolling="no" width="600" height="448" class='iframePop' src=""></iframe>
</div>

<!--添加成交房源-->
<div id="js_add_house_p" class="iframePopBox" style=" width:540px; height:345px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="540" height="345" class='iframePop' src=""></iframe>
</div>



<!--添加成交房源-->
<div id="js_pop_add_info_house" class="iframePopBox" style=" width:600px;height: 448px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="600" height="448" class='iframePop' src=""></iframe>
</div>

<script>
function open_js_pop_see_info_house(type,id)
{ 
    var _url = '/'+ type +'/details/'+id;         
    
    $("#js_pop_see_info_house .iframePop").attr("src",_url);
    
    openWin('js_pop_see_info_house');
};

function open_js_add_house_p(type)
{ 
    var _url = '/'+ type +'/house_list/';        
    
    $("#js_add_house_p .iframePop").attr("src",_url);
    
    openWin('js_add_house_p');
};

function open_js_pop_add_info_house(type,house_id)
{ 
    var _url = '/'+ type +'/add/'+house_id;        
    
    $("#js_pop_add_info_house .iframePop").attr("src",_url);
    
    openWin('js_pop_add_info_house');
};
</script>

