<body>
<div class="tab_box" id="js_tab_box"> 
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="personal_box">
    <div id="js_inner" class="pop_box_g appeal_bg appeal_bg_box" style="border:0px; overflow-y:scroll; overflow-x:hidden; position:relative; left:0; top:0;">
        <div class="appeal_main appeal_main_auto" style="height:auto;">
            <div class="appeal_content appeal_content_auto" style="padding:0;">
                <div class="appeal_top" style="padding:0 10px;">
                    <table class="table appeal_detail">
                        <tr>
                            <td class="apl_name"><span style="font-weight:bold; font-size:14px;"><?=$broker_info['truename']?></span></td>
                            <!--  <td><div class="apl_zizhi">10年资质</div></td>-->
                            <td><?=$trust_level['level']?></td>
							<td><span title="身份资质认证" alt="身份资质认证" id="shenfen" class="pericon per3<?php 
								if($broker_info['group_id']==2){				
									echo 'pericon per0'; 
								}
							?>"></span></td>
							<td><span title="身份资质认证" alt="身份资质认证" id="zizhi" class="pericon per2<?php		if($broker_info['group_id']==2){				
									echo 'pericon per1'; 
								}
							?>"></span></td>
                            <td><div class="appeal_adr"><span style="padding-right:20px;">手机：<?=$broker_info['phone']?></span>所属门店：<?=$broker_info['agency_name']?></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="clearfix" style="padding:0 10px 20px;">
                    <div class="left c49">
                        <div class="ctrcenter_50"> 
                            <!--经纪人信用评价-->
                            <div class="credit_title" style="color:#0ca470; font-weight:bold;"> <span>经纪人信用评价</span>
                                <!--<div class="credit_rate">好评率：</div>-->
                            </div>
<style>
.pf_bg{ background:#ffffff;}
.pf_left li{ margin:0;}
.broker_pf_50 .pf_left li{ height:50px; line-height:50px;}
.pf_left li .pf_bijiao .high_low{ margin-top:15px;}
.pf_left li .pf_bijiao .pf_bfb{ margin-top:15px;}
.ctrbg_pj .th,
.ctrbg_pj td{border-bottom:1px solid #eeeeee; height:29px; line-height:29px;}
.pj_tj_box{ padding-bottom:10px; padding-top:1px;}
.pj_tj_box .tj{ margin-right:10px; float:left; line-height:20px;}
.pj_tj_box .gy{ float:left; width:150px; height:20px; line-height:20px; line-height:20px; color:#fff; background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/pj01.png) no-repeat 0 0; text-align:center;}
.pj_tj_box .gy02{background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/pj02.png) no-repeat 0 0;}
.ctrcenter_50 .table{ margin-bottom:0;}
.table_100 .partner_box{ _width:98.7% !important;}
</style>
                            <div class="clearfix" >
                                <div class="left" style="width:45%;">
                                    <div style=" margin:0 15px 0 5px;">
                                    <div class="clearfix pj_tj_box">
                                    	<span class="tj">好评率:<strong style="color:#df5458;">
                                        	<?php echo empty($count_info['good_rate'])?'--':$count_info['good_rate'].'%';?>
                                        	</strong>
                                    	</span>
                                    	<span class="gy">
                                    	<?php 
                                    	if($count_info['good_rate']){
                                    	    echo '比平均值';
                                            if($diff_good_rate>0){ 
                                                echo '高'.abs($diff_good_rate).'%';   
                                            }elseif($diff_good_rate<0){
                                                echo '低'.abs($diff_good_rate).'%';
                                            }else{
                                                echo '持平';
                                            }
                                        }
                                        ?>
                                        </span>
                                    </div>
                                    <table class="table ctrcenter" style="border:1px solid #eeeeee; border-bottom:0;">
                                        <tr class="ctrbg_pj">
                                            <th class="th" style="width:73px; padding-right:27px;text-align:right; background:#f7f6f6;">总数</th>
                                            <td><?=$count_info['total']?></td>
                                        </tr>   
                                        <tr class="ctrbg_pj">   
                                            <th class="th" style="width:73px; padding-right:27px;text-align:right;background:#f7f6f6;"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/pj03.png" style="margin-right:5px;">好评</th>
                                            <td><?=$count_info['good']?></td>
                                         </tr>  
                                         <tr class="ctrbg_pj">
                                            <th class="th" style="width:73px; padding-right:27px;text-align:right;background:#f7f6f6;"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/pj04.png" style="margin-right:5px;">中评</th>
                                            <td><?=$count_info['medium']?></td>
                                         </tr>
                                         <tr class="ctrbg_pj">  
                                            <th class="th" style="width:73px; padding-right:27px; text-align:right;background:#f7f6f6;"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/pj05.png" style="margin-right:5px;">差评</th>
                                            <td><?=$count_info['bad']?></td>
                                        </tr>
                                    </table>
                                    </div>
                                </div>
                                <div class="left" style="width:54%;">
                                    <div style=" margin:0 15px 0 5px;">
                                        <div class="clearfix pj_tj_box">
                                        	<span class="tj" >合作成功率:<strong style="color:#df5458;">
                                            	<?php echo empty($cop_succ_ratio_info['cop_succ_ratio'])?'--':$cop_succ_ratio_info['cop_succ_ratio'].'%';?>
                                                </strong>
                                             </span>
                                             <span class="gy gy02">
                                            <?php 
                                            if($cop_succ_ratio_info['cop_succ_ratio']>0){
                                                echo '比平均值';
                                                $n = $avg_cop_suc_ratio > 0 ? round(($cop_succ_ratio_info['cop_succ_ratio'] - $avg_cop_suc_ratio)/$avg_cop_suc_ratio , 2) : 0;
                                                if($n>0){
                                                    echo '高'.abs($n).'%';
                                                }elseif($n<0){
                                                    echo '低'.abs($n).'%';
                                                }else{
                                                    echo '持平';
                                                }
                                            }
                                            ?>
                                            </span>
                                        </div>
                                        <table class="table ctrcenter" style="border:1px solid #eeeeee; border-bottom:0;">
                                            <tr class="ctrbg_pj">
                                                <th class="th" style="width:73px; padding-right:27px;text-align:right; background:#f7f6f6;">收到合作</th>
                                                <td><?=$received?></td>
                                            </tr>   
                                            <tr class="ctrbg_pj">   
                                                <th class="th" style="width:73px; padding-right:27px;text-align:right;background:#f7f6f6;">发起合作</th>
                                                <td><?=$initiate?></td>
                                             </tr>  
                                             <tr class="ctrbg_pj">
                                                <th class="th" style="width:73px; padding-right:27px;text-align:right;background:#f7f6f6;">接受合作</th>
                                                <td><?=$accept?></td>
                                             </tr>
                                             <tr class="ctrbg_pj">  
                                                <th class="th" style="width:73px; padding-right:27px; text-align:right;background:#f7f6f6;">被接受合作</th>
                                                <td><?=$accepted?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                 
                    <div class="right c50"> <!--经纪人动态评分-->
                        <div class="credit_title" style="color:#0ca470; font-weight:bold;">经纪人动态评分</div>
                        <div class="broker_pf broker_pf_50">
                            <div class="clearfix" style="border:1px solid #eeeeee;">
                                <ul class="pf_left" style="background:#f7f6f6; width:47%; height:150px; overflow:hidden;">
                                    <li class="pf_bg" style="height:50px; line-height:50px; overflow:hidden;">
                                        <div class="pf_taidu">信息真实度</div>
                                        <div class="pf_socre">得分<span><?=$appraise_avg_info['infomation']['score']?></span>分</div>
                                        <div class="pf_bijiao" style="width:48%;">
                                            <span>比平均值</span>
                                            <?php 
                                            if($appraise_avg_info['infomation']['score'] >= $appraise_avg_info['infomation']['avg']){
                                                $diff_infomation = 'high';
                                            }else{
                                                $diff_infomation = 'low';
                                            }
                                            ?>
                                            <span class="high_low" <?php if($diff_infomation == 'low'){?> style="background:#0bb343;"<?php }?>>
                                            <?php
                                            if($diff_infomation == 'high'){
                                                echo '高';
                                            }else{
                                                echo '低';
                                            } 
                                            ?>
                                            </span>
                                            <span class="pf_bfb"><?=abs($appraise_avg_info['infomation']['rate'])?>%</span>
                                        </div>
                                    </li>
                                    <li style="height:50px; line-height:50px; overflow:hidden;">
                                        <div class="pf_taidu">合作满意度</div>
                                        <div class="pf_socre">得分<span><?=$appraise_avg_info['attitude']['score']?></span>分</div>
                                        <div class="pf_bijiao" style="width:48%;">
                                            <span>比平均值</span>
                                            <?php 
                                            if($appraise_avg_info['attitude']['score'] >= $appraise_avg_info['attitude']['avg']){
                                                $diff_attitude = 'high';
                                            }else{
                                                $diff_attitude = 'low';
                                            }
                                            ?>
                                            <span class="high_low" <?php if($diff_attitude == 'low'){?> style="background:#0bb343;"<?php }?>>
                                            <?php 
                                            if($diff_attitude == 'high'){
                                                echo '高';
                                            }else{
                                                echo '低';
                                            }
                                            ?>
                                            </span>
                                            <span class="pf_bfb"><?=abs($appraise_avg_info['attitude']['rate'])?>%</span>
                                        </div>
                                    </li>
                                    <li style="height:50px; line-height:50px; overflow:hidden;">
                                        <div class="pf_taidu">业务专业度</div>
                                        <div class="pf_socre">得分<span><?=$appraise_avg_info['business']['score']?></span>分</div>
                                        <div class="pf_bijiao" style="width:48%;">
                                            <span>比平均值</span>
                                            <?php 
                                            if($appraise_avg_info['business']['score'] >= $appraise_avg_info['business']['avg']){
                                                $diff_business = 'high';
                                            }else{
                                                $diff_business = 'low';
                                            }
                                            ?>
                                            <span class="high_low" <?php if($diff_business == 'low'){?> style="background:#0bb343;"<?php }?>>
                                            <?php 
                                            if($diff_business == 'high'){
                                                echo '高';
                                            }else{
                                                echo '低';
                                            }
                                            ?>
                                            </span>
                                            <span class="pf_bfb"><?=abs($appraise_avg_info['business']['rate'])?>%</span>
                                        </div>
                                    </li>
                                </ul>
                                <div class="pf_right" style="background:#fff;  width:52%;">
                                    <div class="pf_con" style="display:block;">
                                        <div class="pf_result"><?=$appraise_info['infomation_score_html']?> 共<?=$appraise_info['infomation_sum']?>人</div>
                                        <table class="table pf_score">
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span></td>
                                                <td class="pfd20">5分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['infomation'][5]['info_percent']?>px;"></div>
                                                    <?=$appraise_info['infomation'][5]['info_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">4分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['infomation'][4]['info_percent']?>px;"></div>
                                                    <?=$appraise_info['infomation'][4]['info_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">3分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['infomation'][3]['info_percent']?>px;"></div>
                                                    <?=$appraise_info['infomation'][3]['info_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">2分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['infomation'][2]['info_percent']?>px;"></div>
                                                    <?=$appraise_info['infomation'][2]['info_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">1分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['infomation'][1]['info_percent']?>px;"></div>
                                                    <?=$appraise_info['infomation'][1]['info_percent']?>%</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="pf_con">
                                        <div class="pf_result"><?=$appraise_info['attitude_score_html']?> 共<?=$appraise_info['attitude_sum']?>人</div>
                                        <table class="table pf_score">
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span></td>
                                                <td class="pfd20">5分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['attitude'][5]['atti_percent']?>px;"></div>
                                                    <?=$appraise_info['attitude'][5]['atti_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">4分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['attitude'][4]['atti_percent']?>px;"></div>
                                                    <?=$appraise_info['attitude'][4]['atti_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">3分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['attitude'][3]['atti_percent']?>px;"></div>
                                                    <?=$appraise_info['attitude'][3]['atti_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">2分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['attitude'][2]['atti_percent']?>px;"></div>
                                                    <?=$appraise_info['attitude'][2]['atti_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">1分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['attitude'][1]['atti_percent']?>px;"></div>
                                                    <?=$appraise_info['attitude'][1]['atti_percent']?>%</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="pf_con">
                                        <div class="pf_result"><?=$appraise_info['business_score_html']?> 共<?=$appraise_info['business_sum']?>人</div>
                                        <table class="table pf_score">
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span></td>
                                                <td class="pfd20">5分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['business'][5]['busi_percent']?>px;"></div>
                                                    <?=$appraise_info['business'][5]['busi_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">4分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['business'][4]['busi_percent']?>px;"></div>
                                                    <?=$appraise_info['business'][4]['busi_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">3分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['business'][3]['busi_percent']?>px;"></div>
                                                    <?=$appraise_info['business'][3]['busi_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">2分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['business'][2]['busi_percent']?>px;"></div>
                                                    <?=$appraise_info['business'][2]['busi_percent']?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td>
                                                <td class="pfd20">1分</td>
                                                <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info['business'][1]['busi_percent']?>px;"></div>
                                                    <?=$appraise_info['business'][1]['busi_percent']?>%</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--来自合作方的评价-->
            <div class="shop_tab_title shop_tab_title002" style=" position:relative; overflow:visible;" >
                  <a class="link <?php if(!$type){echo 'link_on';}?>" href="/my_evaluate/?type=0">来自合作方的评价<span class="iconfont hide">&#xe607;</span></a>
                  <a class="link <?php if($type){echo 'link_on';}?>"  href="/my_evaluate/?type=1">我给合作方的评价<span class="iconfont hide">&#xe607;</span></a>
            </div>
            <div class="clearfix" style=" position:relative; overflow:visible;">
                <div class="left">
                    <table class="table partner_check" >
                        <tr>
                            <td><label><a href="/my_evaluate/?type=<?=$type ?>"><input type="radio" name="pj" <?php if(empty($trust)){echo 'checked';}?>>全部</a></label></td>
                            <td><label><a href="/my_evaluate/?type=<?=$type ?>&trust=1"><input type="radio" name="pj" <?php if($trust == 1){echo 'checked';}?>>好评</a></label></td>
                            <td><label><a href="/my_evaluate/?type=<?=$type ?>&trust=2"><input type="radio" name="pj" <?php if($trust == 2){echo 'checked';}?>>中评</a></label></td>
                            <td><label><a href="/my_evaluate/?type=<?=$type ?>&trust=3"><input type="radio" name="pj" <?php if($trust == 3){echo 'checked';}?>>差评</a></label></td>
                        </tr>   
                    </table>
                </div>
                <div class="right">
                    <form name="search_form" id="search_form" method="post" action="/my_evaluate/" >
                        <div class="get_page">
                        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
                        </div> 
                    </form>
                </div>
            </div>
            <div class="table_100">
                <table class="table partner_box">
                    <tr class="ctrbg">
                        <td class="c12">交易编号</td>
                        <td class="c17">合作房源</td>
                        <td class="c10">整体评价</td>
                        <td class="c15">细节评价</td>
                        <td class="c20">评价内容</td>
                        <td class="c12">评价时间</td>
                        <td class="c7">合作方</td>
                        <?php 
                        if(!$type){
                        ?>                      
                        <td >操作</td>
                        <?php 
                        }
                        ?>
                    </tr>
                    <?php 
                    if($cooperate_info){
                        foreach ($cooperate_info as $key=>$value){
                    ?>
                    <tr>
                        <td><?=$value['transaction_id']?></td>
                        <td>
                        <?php 
                        $house_info = unserialize($value['house_info']);
                        $house_info_add = $value['house_info_add'];
                        $_price = ('1'==$house_info['price_danwei'])?$house_info['price']/$house_info['buildarea']/30:$house_info['price'];
                        echo $house_info['districtname'].'-'.$house_info['streetname'].' '.
                        $house_info['blockname'].' '.$house_info['room'].'室'.$house_info['hall'].'厅'.
                        $house_info['toilet'].'卫 '.$house_info_add['fitment_name'].' '.$house_info_add['forward_name'].' '.$house_info['buildarea'].' ㎡ '.$_price;
                        echo ('1' == $house_info['price_danwei']) ? '元/㎡*天' : $house_info_add['price_danwei'];
                        ?>
                        </td>
                        <td><div class="good_pj"><?=$value['trust_name'] ?></div></td>
                        <td>
                            <div class="pjxj">
                                <div class="pjxj_name">信息真实度</div>
                                <div class="pjxj_dj"><?=$value['info_star'] ?></div>
                            </div>
                            <div class="pjxj">
                                <div class="pjxj_name">合作满意度</div>
                                <div class="pjxj_dj"><?=$value['atti_star'] ?></div>
                            </div>
                            <div class="pjxj">
                                <div class="pjxj_name">业务专业度</div>
                                <div class="pjxj_dj"><?=$value['busi_star'] ?></div>
                            </div>
                        </td>
                        <td><?=$value['content']?></td>
                        <td><?=date('Y-m-d H:i:s',$value['create_time'])?></td>
                        <td><?=$value['truename'] ?><br><?=$value['broker_level']['level']?></td>
                        <?php 
                        if(!$type){
                        ?> 
                        <td>
                            <?php 
                            switch ($value['status']){
                                case 0:
                                    echo '<a href="javascript:void(0)" onClick=appeal("'.$value['id'].'","'.$value['transaction_id'].'")>申诉</a>';break;
                                case 1:
                                    echo '<span class="c808080">申诉待处理</span>';break;
                                case 2:
                                    echo '<span class="c4bcb00">申诉成功<br />评价失效</span>';break;
                                case 3:
                                    echo '<span class="f30000">申诉失败</span>';break;
                            }
                            ?>
                        </td>
                        <?php 
                        }
                        ?>
                    </tr>
                    <?php 
                        }
                    }else{
                        echo '<tr><td colspan="7">暂无评价</td></tr>';
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

<!--申诉信息弹框-->
<div id="js_woyaoshensu" class="iframePopBox" style=" width:500px; height:445px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="500" height="445" class='iframePop' src=""></iframe>
</div>


<script type="text/javascript">
function appeal(id,transaction_id){
    $("#js_woyaoshensu .iframePop").attr("src","/my_evaluate/shensu/"+id+"/"+transaction_id);
	openWin('js_woyaoshensu');
}
</script>
