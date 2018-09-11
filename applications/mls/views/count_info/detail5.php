<?php 
switch ($type){
    case 1:
        $checked1 = 'checked';
        $show_html1 = '<span class="iconfont">&#xe607;</span>';
        $tr_price = '总价（W）';
        break;
    case 2:
        $checked2 = 'checked';
        $show_html2 = '<span class="iconfont">&#xe607;</span>';
        $tr_price = '租金（元/月）';
        break;
    case 3:
        $checked3 = 'checked';
        $show_html3 = '<span class="iconfont">&#xe607;</span>';
        $tr_price = '总价（W）';
        break;
    case 4:
        $checked4 = 'checked';
        $show_html4 = '<span class="iconfont">&#xe607;</span>';
        $tr_price = '租金（元/月）';
        break;
}
$start_date_begin = $this->input->get('start_date_begin', true);
$start_date_end = $this->input->get('start_date_end', true);
$search_time = '?start_date_begin=' . $start_date_begin . '&start_date_end=' . $start_date_end;
?>
<form name="search_form" id="search_form" method="post" action="" >
<div class="pop_box_g" id="" style="display:block;background: #fff">
    <div class="hd">
        <div class="title"><?=$truename?>带看数据</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="logging_data_tab">
            <a href="/count_info/detail/<?=$broker_id?>/5/1/<?=$search_time?>" class="tab fl <?=$checked1?>"><?=$show_html1?>出售</a>
            <a href="/count_info/detail/<?=$broker_id?>/5/2/<?=$search_time?>" class="tab fl <?=$checked2?>"><?=$show_html2?>出租</a>
            <a href="/count_info/detail/<?=$broker_id?>/5/3/<?=$search_time?>" class="tab fl <?=$checked3?>"><?=$show_html3?>求购</a>
            <a href="/count_info/detail/<?=$broker_id?>/5/4/<?=$search_time?>" class="tab fl <?=$checked4?>"><?=$show_html4?>求租</a>
        </div>
        <div class="logging_data_wrap">
            <table class="logging_data_table">
            <?php 
            if($type==1 or $type==2){
            ?>
                <tr>
                    <th class="w90">房源编号</th>
                    <th class="w170">楼盘</th>
                    <th class="w80">户型</th>
                    <th class="w80">面积（㎡）</th>
                    <th class="w90"><?=$tr_price?></th>
                    <th>楼层</th>
                    <th class="w90">客户姓名</th>
                    <th class="w90">操作人</th>
                    <th>次数</th>
                </tr>
                <?php 
                if($count_log_info){
                    foreach ($count_log_info as $key=>$value){
                ?>
                <tr>
                    <td class="blue">
                    <?php 
                    if($type==1){
                    ?>
                    <a href="javascript:void(0)" date-url="/sell/details/<?=$value['house_info']['id']?>/1" onClick="openUrl(this)"><?=$value['house_info']['id']?></a>
                    <?php 
                    }else{
                    ?>
                    <a href="javascript:void(0)" date-url="/rent/details/<?=$value['house_info']['id']?>/1" onClick="openUrl(this)"><?=$value['house_info']['id']?></a>
                    <?php 
                    }
                    ?>
                    </td>
                    <td><?=$value['house_info']['block_name']?></td>
                    <td><?=$value['house_info']['room']?>-<?=$value['house_info']['hall']?>-<?=$value['house_info']['toilet']?></td>
                    <td><?=$value['house_info']['buildarea']?></td>
                    <td><?=$value['house_info']['price']?></td>
                    <td><?=$value['house_info']['floor']?>/<?=$value['house_info']['totalfloor']?></td>
                    <td><?=$value['customer_info']['truename']?></td>
                    <td><?=$truename?></td>
                    <td><?=$value['count(id)']?></td>
                </tr>
            <?php        
                    }
                }
            }else{
            ?>
                <tr>
                    <th class="w60">客源编号</th>
                    <th class="w90">客户姓名</th>
                    <th class="w70">物业类型</th>
                    <th class="w80">户型</th>
                    <th class="w80">面积（㎡）</th>
                    <th class="w80"><?=$tr_price?></th>
                    <th class="w90">房源编号</th>
                    <th class="w90">楼盘</th>
                    <th class="w90">操作人</th>
                    <th>次数</th>
                </tr>
                <?php 
                if($count_log_info){
                    foreach ($count_log_info as $key=>$value){
                ?>
                <tr>
                    <td class="blue">
                    <?php 
                    if($type==3){
                    ?>
                    <a href="javascript:void(0)" date-url="/customer/details/<?=$value['customer_info']['id']?>" onClick="openUrl(this)"><?=$value['customer_info']['id']?></a>
                    <?php 
                    }else{
                    ?>
                    <a href="javascript:void(0)" date-url="/rent_customer/details/<?=$value['customer_info']['id']?>" onClick="openUrl(this)"><?=$value['customer_info']['id']?></a>
                    <?php 
                    }
                    ?>
                    </td>
                    <td><?=$value['customer_info']['truename']?></td>
                    <td><?=$conf_customer['property_type'][$value['customer_info']['property_type']]?></td>
                    <td><?=$value['customer_info']['room_min']?>-<?=$value['customer_info']['room_max']?></td>
                    <td><?=strip_end_0($value['customer_info']['area_min'])?>-<?=strip_end_0($value['customer_info']['area_max'])?></td>
                    <td><?=strip_end_0($value['customer_info']['price_min'])?>-<?=strip_end_0($value['customer_info']['price_max'])?></td>
                    <td><?=$value['house_info']['id']?></td>
                    <td><?=$value['house_info']['block_name']?></td>
                    <td><?=$truename?></td>
                    <td><?=$value['count(id)']?></td>
                </tr>
            <?php
                    }
                }
            }
            ?>
            </table>
            <div class="get_page">
            <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
            </div>
        </div>
    </div>
</div>
</form>

<!--分配任务-->
<div id="js_fenpeirenwu" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--分配客源-->
<div id="js_allocate_customer" class="iframePopBox" style=" width:816px; height:340px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="340" class='iframePop' src=""></iframe>
</div>
<!--分配房源-->
<div id="js_allocate_house" class="iframePopBox" style=" width:816px; height:340px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="340" class='iframePop' src=""></iframe>
</div>
<!--跟进信息弹框-->
<div id="js_genjin" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--详情页弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--匹配详情页弹框-->
<div id="js_pop_box_g_match" class="iframePopBox" style=" width:1200px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="1200" height="540" class='iframePop' src=""></iframe>
</div>

<script type="text/javascript">
function openUrl(obj)
{
    var _url = $(obj).attr("date-url");
    $("#js_pop_box_g .iframePop").attr("src",_url);
    openWin('js_pop_box_g');
    
}
</script>