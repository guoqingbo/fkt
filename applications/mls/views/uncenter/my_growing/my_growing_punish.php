<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div id="js_search_box">
    <div  class="shop_tab_title">
        <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
    </div>
</div>

<div id="js_search_box_02" class="xy_box_t_title">
	
	<div class="title">累计被处罚<strong class="color"><?=$total_count?></strong>次       平台所有经纪人均值<strong class="color"><?=$avg?></strong>次</div>
	
	<table class="table">
		<tr>
			<td>恶意评价被处罚<strong class="color"><?=$total1?></strong>次</td>
			<td>房源虚假被处罚<strong class="color"><?=$total2?></strong>次</td>
			<td>客源虚假被处罚<strong class="color"><?=$total3?></strong>次</td>
			<td>不按协议履行合同被处罚<strong class="color"><?=$total4?></strong>次</td>
			<td>取消合作被处罚<strong class="color"><?=$total5?></strong>次</td>
			<td>处理合作审核不及时被处罚<strong class="color"><?=$total6?></strong>次</td>
		</tr>
	</table>
	
</div>

<div class="table_all">
	<div class="title shop_title" id="js_title">
        <table class="table">
           <tr>
              	<td class="c20"><div class="info">交易编号</div></td>
                <td class="c40"><div class="info">对象</div></td>
                <td class="c12"><div class="info">处罚类型</div></td>
                <td class="c12"><div class="info">详情</div></td>
                <td><div class="info">生效时间</div></td>
            </tr>
     	</table>
    </div>
    <div id="js_inner" class="inner shop_inner">
        <table class="table">
            <?php 
            if($punish_info){
               $num=0; 
                foreach ($punish_info as $key=>$value){
                    $num = $num+1;
            ?>
            <tr <?php if($num%2==0){echo 'class="bg"';}?>>
                
                <td class="c20"><div class="info"><?=$value['number']?></div></td>
                <td class="c40">
                    <div class="info">
                    <?php 
                    $house_info = $value['house_info'];
                    if ($house_info['tbl'] == 'sell' || $house_info['tbl'] == 'buy_customer')
                    {
                        $unit = '万';
                    }
                    else
                    {
                        $unit = '元/月';
                    }
                    echo $house_info['districtname'].'-'.$house_info['streetname'].' '.
                    $house_info['blockname'].' '.$house_info['room'].'室';
                    if ($house_info['hall'])
                    {
                        echo $house_info['hall'].'厅';
                    }
                    if ($house_info['toilet'])
                    {
                        echo $house_info['toilet'].'卫';
                    }
                    echo $house_info['fitment_str'].' '.$house_info['forward_str'].' '.$house_info['buildarea'].' ㎡ '.$house_info['price'] . $unit;
                    ?>
                    <!-- 鼓楼区-三牌楼  天福园 3室2厅1卫   精装   南  102 ㎡  200W-->
                    </div>
                </td>
                <td class="c12"><div class="info"><?=$value['type_name']?></div></td>
                <td class="c12"><div class="info"><p class="color"><?=$value['description']?></p></div></td>
                <td><div class="info"><?=date('Y-m-d H:i:s', $value['create_time'])?></div></td>

               
            </tr>
            <?php 
                    
                }
                $num++;
            }  
            ?>
            
         </table>
    </div>
</div>

<div id="js_fun_btn" class="fun_btn fun_btn_bottom clearfix">
    <div class="get_page">
        <form name="search_form" id="search_form" method="post" action="/my_growing_punish/" >
            <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?> 
	    </form> 			
    </div>
</div>