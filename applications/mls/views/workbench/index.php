<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>index</title>
</head>
<body style="overflow:hidden;">
    <div style="height:100%; overflow-y:scroll; *position:relative; *left:0; *top:0;">
	<div class="index_top clearfix">
    <div class="index_top_l">
        <div class="p_info_index">
            <div class="pic">
                <img alt="" src=<?php if(!empty($broker['photo'])){echo $broker['photo'];}else{echo MLS_SOURCE_URL."/mls/images/v1.0/365mls.png";}?> width="130" height="130">
            </div>
            <div class="p_info">
                <div class="name_b clearfix">
                    <p class="name"><?php echo $broker['truename'];?></p>
                    <div class="d_b"><?php echo $broker['trust_level']['level'];?></div>
                    <span class="pericon per<?php if($broker['ident_auth']==1){echo '1';}else{echo '2';}?>"></span> 
                    <span class="pericon per<?php if($broker['quali_auth']==1){echo '0';}else{echo '3';}?>"></span> 
                </div>
                <div class="h_p_box clearfix">
                    <p class="t">好  评  率：</p>
                    <div class="p_bg">
                        <div class="bg">
                            <div class="bg_l" style=" width:<?php echo $broker['trust_appraise']['good_rate'];?>;">&nbsp;</div>
                            <div class="text"><?php echo $broker['trust_appraise']['good_rate'];?></div>
                            <div class="tip_b">
                            			比平均值<span class="color">高<?php echo $broker['good_rate_avg_high'];?>%</span>
                               <div class="s_ico">&nbsp;</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h_p_box clearfix">
                    <p class="t">合作成功率：</p>
                    <div class="p_bg">
                        <div class="bg">
                            <div class="bg_l" style="width:<?php echo strip_end_0($broker['cop_suc_ratio']);?>%">&nbsp;</div>
                            <div class="text"><?php echo strip_end_0($broker['cop_suc_ratio']);?>%</div>
                            <?php if($broker['cop_suc_ratio'] > 0) { ?>
                            <div class="tip_b">
                             <?php if( $broker['cop_suc_ratio'] > 0 && $broker['cop_suc_ratio'] > $broker['cop_suc_ratio_avg']){?>
                                比平均值<span class="color">高<?php echo abs(($broker['cop_suc_ratio'] - $broker['cop_suc_ratio_avg'])/$broker['cop_suc_ratio_avg']);?>%</span>
                             <?php }else if($broker['cop_suc_ratio'] > 0 && $broker['cop_suc_ratio'] < $broker['cop_suc_ratio_avg']) {?>
                                比平均值<span class="color">低<?php echo abs(($broker['cop_suc_ratio'] - $broker['cop_suc_ratio_avg'])/$broker['cop_suc_ratio_avg']);?>%</span>
                             <?php }else if($broker['cop_suc_ratio'] > 0 && $broker['cop_suc_ratio'] == $broker['cop_suc_ratio_avg']) {?>
                                与平均值<span class="color">持平</span>
                             <?php }?>
                               <div class="s_ico">&nbsp;</div>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <div class="x_box">
                    <div class="item">
                        <p> 信息真实度</p>
                        <p class="f <?php if($broker['appraise_and_avg']['infomation']['up_down']=='down'){echo 'f_j';}?>">
                            <?php echo $broker['appraise_and_avg']['infomation']['score'];?>
                            <img class="img" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/<?php if($broker['appraise_and_avg']['infomation']['up_down']=='up'){echo 'sj_i.png';}else{echo 'dj_i.png';}?>">
                        </p>
                    </div>
                    <div class="item item_c">
                        <p> 态度满意度</p>
                        <p class="f <?php if($broker['appraise_and_avg']['attitude']['up_down']=='down'){echo 'f_j';}?>">
                            <?php echo $broker['appraise_and_avg']['attitude']['score'];?>
                            <img class="img" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/<?php if($broker['appraise_and_avg']['attitude']['up_down']=='up'){echo 'sj_i.png';}else{echo 'dj_i.png';}?>">
                        </p>
                    </div>
                    <div class="item">
                        <p> 业务满意度</p>
                        <p class="f <?php if($broker['appraise_and_avg']['business']['up_down']=='down'){echo 'f_j';}?>">
                            <?php echo $broker['appraise_and_avg']['business']['score'];?>
                            <img class="img" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/<?php if($broker['appraise_and_avg']['business']['up_down']=='up'){echo 'sj_i.png';}else{echo 'dj_i.png';}?>">
                        </p>
                    </div>
                </div>
            </div>
            <div class="p_table_b">
                <table class="table">
                    <tr>
                        <th class="title" rowspan="2">我发起的<br>
                            合作申请</th>
                        <th>待处理申请</th>
                        <th>合作生效</th>
                        <th>待评价合作</th>
                        <th>交易成功</th>
                    </tr>
                    <tr>
                        <td><?php echo $send['all_estas_num1'];?></td>
                        <td><?php echo $send['all_estas_num2'];?></td>
                        <td><?php echo $send['all_estas_num3'];?></td>
                        <td><?php echo $send['all_estas_num4'];?></td>
                    </tr>
                    <tr>
                        <th class="title" rowspan="2">我收到的<br>
                            合作申请</th>
                        <th>待处理申请</th>
                        <th>合作生效</th>
                        <th>待评价合作</th>
                        <th>交易成功</th>
                    </tr>
                    <tr>
                        <td><?php echo $accept['all_estas_num1'];?></td>
                        <td><?php echo $accept['all_estas_num2'];?></td>
                        <td><?php echo $accept['all_estas_num3'];?></td>
                        <td><?php echo $accept['all_estas_num4'];?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="tq_box">
        <div class="inner">
            <iframe width="345" scrolling="no" height="122" frameborder="0" allowtransparency="true" src="http://i.tianqi.com/index.php?c=code&id=19&icon=1&num=3"></iframe>
            <div class="p_div"> </div>
        </div>
    </div>
</div>

	<div class="item_box_l_r clearfix">
    <div class="item_box_l">
        <div class="item">
            <div class="hd"> <a class="hd_c hd_on" href="javascript:void(0)">出售公盘</a> <a class="hd_c" href="javascript:void(0)">出租公盘</a> </div>
            <div class="mod">
                <div class="list">
                    <table class="table" onclick="to_url('pub_sell_house_list');">
                        <tr>
                            <th>房源编号</th>
                            <th>楼盘名称</th>
                            <th>总价(W)</th>
                            <th>户型</th>
                            <th>面积(㎡)</th>
                            <th>楼层</th>
                            <th>委托人</th>
                        </tr>
                        <?php for($i=0;$i<count($pub_sell_house_list);$i++){ ?>
                        <tr <?php if($i%2!=0){echo 'class="bg"';}?>>
                            <td><div class="info" style="width:93px;"><?php echo $pub_sell_house_list[$i]['id'];?></div></td>
                            <td><div class="info" style="width:159px;"><?php echo $pub_sell_house_list[$i]['block_name'];?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $pub_sell_house_list[$i]['price'];?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $pub_sell_house_list[$i]['room'];?>-<?php echo $pub_sell_house_list[$i]['hall'];?>-<?php echo $pub_sell_house_list[$i]['toilet'];?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $pub_sell_house_list[$i]['buildarea'];?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $pub_sell_house_list[$i]['floor'];?></div></td>
                            <td><div class="info" style="width:75px;"><?php echo $pub_sell_house_list[$i]['broker_name'];?></div></td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
                <div class="list" style="display:none">
                    <table class="table" onclick="to_url('pub_rent_house_list');">
                        <tr>
                            <th>房源编号</th>
                            <th>楼盘名称</th>
                            <th>总价(W)</th>
                            <th>户型</th>
                            <th>面积(㎡)</th>
                            <th>楼层</th>
                            <th>委托人</th>
                        </tr>
                        <?php for($i=0;$i<count($pub_rent_house_list);$i++){ ?>
                        <tr <?php if($i%2!=0){echo 'class="bg"';}?>>
                            <td><div class="info" style="width:93px;"><?php echo $pub_rent_house_list[$i]['id'];?></div></td>
                            <td><div class="info" style="width:159px;"><?php echo $pub_rent_house_list[$i]['block_name'];?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $pub_rent_house_list[$i]['price'];?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $pub_rent_house_list[$i]['room'];?>-<?php echo $pub_rent_house_list[$i]['hall'];?>-<?php echo $pub_rent_house_list[$i]['toilet'];?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $pub_rent_house_list[$i]['buildarea'];?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $pub_rent_house_list[$i]['floor'];?></div></td>
                            <td><div class="info" style="width:75px;"><?php echo $pub_rent_house_list[$i]['broker_name'];?></div></td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="hd"> <a class="hd_c hd_on" href="javascript:void(0)">采集出售</a> <a class="hd_c" href="javascript:void(0)">采集出租</a> </div>
            <div class="mod">
                <div class="list">
                    <table class="table" onclick="to_url('collect_sell_house');">
                        <tr>
                            <th>房源编号</th>
                            <th>楼盘名称</th>
                            <th>总价(W)</th>
                            <th>户型</th>
                            <th>面积(㎡)</th>
                            <th>楼层</th>
                            <th>发布人</th>
                        </tr>
                        <?php for($i=0;$i<count($collect_sell_house);$i++){ ?>
                        <tr <?php if($i%2!=0){echo 'class="bg"';}?>>
                            <td><div class="info" style="width:93px;"><?php echo $collect_sell_house[$i]['id']?></div></td>
                            <td><div class="info" style="width:159px;"><?php echo $collect_sell_house[$i]['house_name']?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $collect_sell_house[$i]['price']?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $collect_sell_house[$i]['room']?>-<?php echo $collect_sell_house[$i]['hall']?>-<?php echo $collect_sell_house[$i]['toilet']?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $collect_sell_house[$i]['buildarea']?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $collect_sell_house[$i]['floor']?></div></td>
                            <td><div class="info" style="width:75px;"><?php echo $collect_sell_house[$i]['owner']?></div></td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
                <div class="list" style="display:none">
                    <table class="table" onclick="to_url('collect_rent_house');">
                        <tr>
                            <th>房源编号</th>
                            <th>楼盘名称</th>
                            <th>总价(W)</th>
                            <th>户型</th>
                            <th>面积(㎡) </th>
                            <th>楼层</th>
                            <th>发布人</th>
                        </tr>
                        <?php for($i=0;$i<count($collect_rent_house);$i++){ ?>
                        <tr <?php if($i%2!=0){echo 'class="bg"';}?>>
                            <td><div class="info" style="width:93px;"><?php echo $collect_rent_house[$i]['id']?></div></td>
                            <td><div class="info" style="width:159px;"><?php echo $collect_rent_house[$i]['house_name']?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $collect_rent_house[$i]['price']?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $collect_rent_house[$i]['room']?>-<?php echo $collect_sell_house[$i]['hall']?>-<?php echo $collect_sell_house[$i]['toilet']?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $collect_rent_house[$i]['buildarea']?></div></td>
                            <td><div class="info" style="width:51px;"><?php echo $collect_rent_house[$i]['floor']?></div></td>
                            <td><div class="info" style="width:75px;"><?php echo $collect_rent_house[$i]['owner']?></div></td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="hd"> <a class="hd_c hd_on" href="javascript:void(0)">个人业绩排行</a> <a class="hd_c" href="javascript:void(0)">门店业绩排行</a> </div>
            <div class="mod">
                <div class="list">
                    <table class="table">
                        <tr>
                            <th>名次</th>
                            <th>门店员工</th>
                            <th>业绩</th>
                        </tr>
                        <tr>
                            <td><div class="info" style="width:90px;">1</div></td>
                            <td><div class="info" style="width:346px;">王大功 / 我爱我家三牌楼店</div></td>
                            <td><div class="info" style="width:135px;">750万</div></td>
                        </tr>
                        <tr class="bg">
                            <td><div class="info" style="width:90px;">1</div></td>
                            <td><div class="info" style="width:346px;">王大功 / 我爱我家三牌楼店</div></td>
                            <td><div class="info" style="width:135px;">750万</div></td>
                        </tr>
                        <tr>
                            <td><div class="info" style="width:90px;">1</div></td>
                            <td><div class="info" style="width:346px;">王大功 / 我爱我家三牌楼店</div></td>
                            <td><div class="info" style="width:135px;">750万</div></td>
                        </tr>
                        <tr  class="bg">
                            <td><div class="info" style="width:90px;">1</div></td>
                            <td><div class="info" style="width:346px;">王大功 / 我爱我家三牌楼店</div></td>
                            <td><div class="info" style="width:135px;">750万</div></td>
                        </tr>
                        <tr>
                            <td><div class="info" style="width:90px;">1</div></td>
                            <td><div class="info" style="width:346px;">王大功 / 我爱我家三牌楼店</div></td>
                            <td><div class="info" style="width:135px;">750万</div></td>
                        </tr>
                    </table>
                </div>
                <div class="list" style="display:none">
                    <table class="table">
                        <tr>
                            <th>名次</th>
                            <th>门店员工</th>
                            <th>业绩</th>
                        </tr>
                        <tr>
                            <td><div class="info" style="width:90px;">1</div></td>
                            <td><div class="info" style="width:346px;">王大功 / 我爱我家三牌楼店</div></td>
                            <td><div class="info" style="width:135px;">750万</div></td>
                        </tr>
                        <tr class="bg">
                            <td><div class="info" style="width:90px;">1</div></td>
                            <td><div class="info" style="width:346px;">王大功 / 我爱我家三牌楼店</div></td>
                            <td><div class="info" style="width:135px;">750万</div></td>
                        </tr>
                        <tr>
                            <td><div class="info" style="width:90px;">1</div></td>
                            <td><div class="info" style="width:346px;">王大功 / 我爱我家三牌楼店</div></td>
                            <td><div class="info" style="width:135px;">750万</div></td>
                        </tr>
                        <tr  class="bg">
                            <td><div class="info" style="width:90px;">1</div></td>
                            <td><div class="info" style="width:346px;">王大功 / 我爱我家三牌楼店</div></td>
                            <td><div class="info" style="width:135px;">750万</div></td>
                        </tr>
                        <tr>
                            <td><div class="info" style="width:90px;">1</div></td>
                            <td><div class="info" style="width:346px;">王大功 / 我爱我家三牌楼店</div></td>
                            <td><div class="info" style="width:135px;">750万</div></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="item_box_r">
        <div class="item">
            <div class="hd"> <a class="hd_c hd_on" href="javascript:void(0)">求购公客</a> <a class="hd_c" href="javascript:void(0)">求租公客</a> </div>
            <div class="mod">
                <div class="list">
                    <table class="table" onclick="to_url('pub_sell_customer_list');">
                        <tr>
                            <th>客源编号</th>
                            <th>意向楼盘</th>
                            <th>面积(㎡)</th>
                            <th>总价(W)</th>
                            <th>委托人</th>
                        </tr>
                        <?php for($i=0;$i<count($pub_sell_customer_list);$i++){?>
                        <tr <?php if($i%2!=0){echo 'class="bg"';}?>>
                            <td><div class="info" style="width:100px;"><?php echo $pub_sell_customer_list[$i]['id'];?></div></td>
                            <td><div class="info" style="width:170px;"><?php echo $pub_sell_customer_list[$i]['cmt_name1'].$pub_sell_customer_list[$i]['cmt_name2'].$pub_sell_customer_list[$i]['cmt_name3'];?></div></td>
                            <td><div class="info" style="width:90px;"><?php echo $pub_sell_customer_list[$i]['area_min'];?></div></td>
                            <td><div class="info" style="width:90px;"><?php echo $pub_sell_customer_list[$i]['price_min'];?></div></td>
                            <td><div class="info" style="width:92px;"><?php echo $pub_sell_customer_list[$i]['broker_name'];?></div></td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
                <div class="list" style="display:none;">
                    <table class="table" onclick="to_url('pub_rent_customer_list');">
                        <tr>
                            <th>房源编号</th>
                            <th>意向楼盘</th>
                            <th>面积(㎡) </th>
                            <th>总价(W)</th>
                            <th>委托人</th>
                        </tr>
                        <?php for($i=0;$i<count($pub_rent_customer_list);$i++){?>
                        <tr <?php if($i%2!=0){echo 'class="bg"';}?>>
                            <td><div class="info" style="width:100px;"><?php echo $pub_rent_customer_list[$i]['id'];?></div></td>
                            <td><div class="info" style="width:170px;"><?php echo $pub_rent_customer_list[$i]['cmt_name1'].$pub_sell_customer_list[$i]['cmt_name2'].$pub_sell_customer_list[$i]['cmt_name3'];?></div></td>
                            <td><div class="info" style="width:90px;"><?php echo $pub_rent_customer_list[$i]['area_min'];?></div></td>
                            <td><div class="info" style="width:90px;"><?php echo $pub_rent_customer_list[$i]['price_min'];?></div></td>
                            <td><div class="info" style="width:92px;"><?php echo $pub_rent_customer_list[$i]['broker_name'];?></div></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="hd"> <a class="hd_c hd_on" href="javascript:void(0)">跟进任务</a> </div>
            <div class="mod">
                <div class="list">
                    <table class="table" onclick="to_url('task_info');">
                        <tr>
                            <th>执行日期</th>
                            <th>任务类型</th>
                            <th>任务说明</th>
                            <th>房源/客源编号 </th>
                            <th>分配人</th>
                        </tr>
                        <?php for($i=0;$i<count($task_info);$i++){ ?>
                        <tr <?php if($i%2!=0){echo 'class="bg"';}?>>
                            <td><div class="info" style="width:100px;"><?php echo date('Y-m-d',$task_info[$i]['start_date']);?></div></td>
                            <td>
                                <div class="info" style="width:90px;">
                                    <?php 
                                    if($task_info[$i]['task_type']==1){
                                        echo '系统跟进';
                                    }else if($task_info[$i]['task_type']==2){
                                        echo '房源跟进';
                                    }else if($task_info[$i]['task_type']==3){
                                        echo '客源跟进';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td><div class="info" style="width:170px;"><?php echo $task_info[$i]['content'];?></div></td>
                            <td>
                                <div class="info" style="width:90px;">
                                    <?php 
                                    if($task_info[$i]['task_type']==1||$task_info[$i]['task_type']==2){
                                        echo $task_info[$i]['house_id'];
                                    }else if($task_info[$i]['task_type']==3||$task_info[$i]['task_type']==4){
                                        echo $task_info[$i]['custom_id'];
                                    }
                                    ?>
                                </div>
                            </td>
                            <td><div class="info" style="width:92px;"><?php echo $task_info[$i]['allot_truename'];?></div></td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
            </div>
        </div>
        <div class="item item_i">
            <div class="hd"> <a class="hd_c hd_on" href="javascript:void(0)">公告</a> </div>
            <div class="mod">
                <dl class="i_list" onclick="to_url('message_list');">
                    <?php foreach($message_list as $k=>$v){?>
                    <dd class="i_item"> <div class="link"><?php echo $v->title;?></div> <span class="time"><?php echo date('Y-m-d',$v->updatetime);?></span> </dd>
                    <?php } ?>
                </dl>
            </div>
        </div>
        <div class="item item_i">
            <div class="hd"> <a class="hd_c hd_on" href="javascript:void(0)">帮助中心</a> </div>
            <div class="mod">
                <dl class="i_list">
                    <dd class="i_item"> <a class="link" href="#">外星飞船在地球坠毁？美空军档案网将公布</a> </dd>
                    <dd class="i_item"> <a class="link" href="../sell/lists_pub">外星飞船在地球坠毁？美空军档案网将公布</a> </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

	</div>

<script>
function to_url(type){
    var mls_url = '<?php echo MLS_URL;?>';
    var url = '';
    switch(type){
        case 'pub_sell_house_list':
            url = mls_url+'/sell/lists_pub';break;
        case 'pub_rent_house_list':
            url = mls_url+'/rent/lists_pub';break;
        case 'pub_sell_customer_list':
            url = mls_url+'/customer/manage_pub';break;
        case 'pub_rent_customer_list':
            url = mls_url+'/rent_customer/manage_pub';break;
        case 'collect_sell_house':
            url = mls_url+'/my_collections/my_collect_sell';break;
        case 'collect_rent_house':
            url = mls_url+'/my_collections/my_collect_rent';break;
        case 'task_info':
            url = mls_url+'/my_task/';break;
        case 'message_list':
            url = mls_url+'/message/bulletin';break;
    }
    window.location.href = url;
}
    
$(function(){

$(".item_box_l_r .item").each(function(index, element) {
    $(this).find(".hd_c").each(function(index, element) {
        $(this).click(function(){
            $(this).addClass("hd_on").siblings().removeClass("hd_on");
            $(this).parent().siblings(".mod").find(".list").eq(index).show().siblings(".list").hide();
        })
    });
});
					
$(".p_info_index .h_p_box .bg").hover(function(){
    $(this).find(".tip_b").show();
},function(){
    $(this).find(".tip_b").hide();
})
    
});
</script>
</body>
</html>
