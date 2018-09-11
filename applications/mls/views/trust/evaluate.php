<div class="pop_box_g appeal_bg" id="js_woyaopingjia" style="display:block; border:0;">
	<div class="hd">
        <div class="title"><?php echo $page_title;?></div>
        <div class="close_pop"></div>
    </div>
   <div class="appeal_main">
    <div class="appeal_content" style="width:720px; overflow:hidden;">
    	<div class="appeal_top">
    		<table class="table appeal_detail">
    			<tr>
	    			<td class="apl_name"><?php echo $broker_info['truename'];?></td>
	    			<!--<td><div class="apl_zizhi">10年资质</div></td>-->
	    			<td><?php echo $broker_info['trust_level'];?></td>
	    			<?php if ($broker_info['ident_auth'] == 1) { ?>
	    			<td><span class="pericon per1"></span></td>
	    			<?php } ?>
	    			<?php if ($broker_info['quali_auth'] == 1) { ?>
	    			<td><span class="pericon per0"></span></td>
	    			<?php } ?>
                    <td><div class="appeal_adr"><?php echo $broker_info['agency_name'];?></div></td>
    			</tr>
			</table>
			
    	</div>
    	<!--经纪人信用评价-->
    	<div class="credit_title">
    		<span>经纪人信用评价</span>
            <div class="credit_rate">好评率：
            <?php echo empty($trust_appraise_count['good_rate'])?'&nbsp;&nbsp;&nbsp;&nbsp;--':$trust_appraise_count['good_rate'].'%';?>
            </div>
    	</div>
        <style>
        .ctrbg td{ background:#efefef;}
		.bg_fff td{ background:#fff;}
        </style>
    	<table class="table ctrcenter" style="width:740px;">
    		<tr class="ctrbg">
    			<td class="cd200">总数</td>
                <td class="cd200">好评</td>
                <td class="cd200">中评</td>
                <td class="cd200">差评</td>
    		</tr>
    		<tr class="bg_fff">
    			<td class="cd200"><?php echo $trust_appraise_count['total'];?></td>
    			<td class="cd200 cblue"><?php echo $trust_appraise_count['good'];?></td>
    			<td class="cd200 cblue"><?php echo $trust_appraise_count['medium'];?></td>
    			<td class="cd200 cblue"><?php echo $trust_appraise_count['bad'];?></td>
    		</tr>
            <tr>
            	<td colspan="4" style="height:15px; line-height:15px;">&nbsp;</td>
            </tr>
    		<tr class="ctrbg ctrleft">
		          <td colspan="4">
		          <span>合作成功率：</span>
	              <span class="cred">
	              <?php echo empty($cop_succ_ratio_info['cop_succ_ratio'])?'--':$cop_succ_ratio_info['cop_succ_ratio'].'%';?>
                 </span>
                 <?php 
                    if($cop_succ_ratio_info['cop_succ_ratio']>0){
                    ?>
                 <span>比平均值：</span><span class="cred">
                    <?php
                        $n = $avg_cop_suc_ratio > 0 ? round(($cop_succ_ratio_info['cop_succ_ratio'] - $avg_cop_suc_ratio)/$avg_cop_suc_ratio , 2) : 0;
                        if($n>0){
                            echo '高'.abs($n).'%';
                        }elseif($n<0){
                            echo '低'.abs($n).'%';
                        }else{
                            echo '持平';
                        }
                    ?>
                </span>
                    <?php
                    }
                    ?>
                </td>
    		</tr>
            <tr class="bg_fff">
               <td class="cd200">收到合作：<span class="cblue"><?php echo $received; ?></span>次</td>
               <td class="cd200">发起合作：<span class="cblue"><?php echo $initiate; ?></span>次</td>
               <td class="cd200">接受合作：<span class="cblue"><?php echo $accept; ?></span>次</td>
               <td class="cd200">被接受合作：<span class="cblue"><?php echo $accepted; ?></span>次</td>
            </tr>
    	</table>
    	<!--经纪人动态评分-->
    	<div class="credit_title">经纪人动态评分</div>
    	<div class="broker_pf">
    		<ul class="pf_left">
    			<li class="pf_bg">
    				<div class="pf_taidu">信息真实度</div>
    				<div class="pf_socre">得分<span><?=$appraise_avg_info['infomation']['score']?></span>分</div>
    				
    				<div class="pf_bijiao">
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
    				    <span class="pf_bfb"><?=abs($appraise_avg_info['infomation']['rate'])?>%</span></div>
    			</li>    			
    			<li>
    				<div class="pf_taidu">信息真实度</div>
    				<div class="pf_socre">得分<span><?=$appraise_avg_info['attitude']['score']?></span>分</div>
    				<div class="pf_bijiao">
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
    				    <span class="pf_bfb"><?=abs($appraise_avg_info['attitude']['rate'])?>%</span></div>
    			</li>    			
    			<li>
    				<div class="pf_taidu">信息真实度</div>
    				<div class="pf_socre">得分<span><?=$appraise_avg_info['business']['score']?></span>分</div>
    				<div class="pf_bijiao">
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
    				    <span class="pf_bfb"><?=abs($appraise_avg_info['business']['rate'])?>%</span></div>
    			</li>
    		</ul>
    		<div class="pf_right">
    		    <?php 
    		        $appraise_type_alias = array('infomation', 'attitude', 'business');
    		        foreach ($appraise_type_alias as $v) {
    		    ?>
    			<div class="pf_con" <?php if ($v == 'infomation') {echo 'style="display:block;"';}?>>
    				<div class="pf_result">
    				    <?=$appraise_info[$v.'_score_html']?>
        				<span class="bjicon bj0"></span> 共<?=$appraise_info[$v . '_sum']?>人
    				</div>
    				<table class="table pf_score">
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span></td><td class="pfd20">5分</td>
                            <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info[$v][5]['percent']?>px;"></div>
                                <?=$appraise_info[$v][5]['percent']?>%
                            </td>
    					</tr>    					
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span></td><td class="pfd20">4分</td>
                            <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info[$v][4]['percent']?>px;"></div>
                                <?=$appraise_info[$v][4]['percent']?>%
                            </td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td><td class="pfd20">3分</td>
                            <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info[$v][3]['percent']?>px;"></div>
                                <?=$appraise_info[$v][3]['percent']?>%
                            </td>
    					</tr>    					
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td><td class="pfd20">2分</td>
                            <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info[$v][2]['percent']?>px;"></div>
                                <?=$appraise_info[$v][2]['percent']?>%
                            </td>
    					</tr>    					
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span><span class="djicon dj0"></span></td><td class="pfd20">1分</td>
                            <td class="pfd150"><div class="pf_bfbbg" style="width:<?=$appraise_info[$v][1]['percent']?>px;"></div>
                                <?=$appraise_info[$v][1]['percent']?>%
                            </td>
    					</tr>
    				</table>
    			</div>  
    			<?php } ?>  			 
    		</div>
    	</div>
    </div>
        	<!--来自合作方的评价-->
    	<ul class="shop_tab_title" style="margin-left:0;">
    		<a class="link <?php if(!$type){echo 'link_on';}?>" href="/my_trust_info/evaluate/<?=$broker_id?>/">来自合作方的评价<span class="iconfont hide">&#xe607;</span></a>
    	</ul>
        <div class="clearfix">
            <div class="left">
                <table class="table partner_check">
                    <tr>
                        <td>
                        	<a href="/my_trust_info/evaluate/<?=$broker_id?>/?type=<?=$type ?>"><input type="radio" name="pj" <?php if(empty($trust)){echo 'checked';}?> style="float:left; margin-top:-3px;">全部</a>
                        </td>
                        <td>
                       		 <a href="/my_trust_info/evaluate/<?=$broker_id?>/?type=<?=$type ?>&trust=1"><input type="radio" name="pj" <?php if($trust == 1){echo 'checked';}?> style="float:left; margin-top:-3px;">好评</a>
                        </td>
                        <td>
                       		 <a href="/my_trust_info/evaluate/<?=$broker_id?>/?type=<?=$type ?>&trust=2"><input type="radio" name="pj" <?php if($trust == 2){echo 'checked';}?> style="float:left; margin-top:-3px;">中评</a>
                        </td>
                        <td>
                      		  <a href="/my_trust_info/evaluate/<?=$broker_id?>/?type=<?=$type ?>&trust=3"><input type="radio" name="pj" <?php if($trust == 3){echo 'checked';}?> style="float:left; margin-top:-3px;">差评</a>
                       </td>
                    </tr>
                </table>
             </div>
            <div class="right" style="padding:10px 20px;">
       			 <form name="search_form" id="search_form" method="post" action="/my_trust_info/evaluate/<?=$broker_id?>/?type=<?=$type ?>&trust=<?=$trust ?>" >
        	    <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?> 
        	    </form>
        	</div>
      	 </div>
    	<table class="table partner_box">
    		<tr class="ctrbg">
    			<td class="pard1">交易编号</td>
    			<td class="pard2">合作房源</td>
    			<td class="pard3">整体评价</td>
    			<td class="pard4">细节评价</td>
    			<td class="pard5">评价内容</td>
    			<td class="pard6">评价时间</td>
    			<td class="pard7">评价人</td>
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
                $house_info_price = $house_info['tbl']=="sell"?$house_info['price'].'W':$house_info['price'].'元/月';
                echo $house_info['districtname'].'-'.$house_info['streetname'].' '.
                $house_info['blockname'].' '.$house_info['room'].'室'.$house_info['hall'].'厅'.
                $house_info['toilet'].'卫 '.$house_info_add['fitment_name'].' '.$house_info_add['forward_name'].' '.$house_info['buildarea'].' ㎡ '.$house_info_price;

                ?>
                </td>
                <td><div class="good_pj"><?=$value['trust_name'] ?></div></td>
                <td>
                    <div class="pjxj">
                        <div class="pjxj_name">信息真实度</div>
                        <div class="pjxj_dj"><?=$value['info_star'] ?></div>
                    </div>
                    <div class="pjxj">
                        <div class="pjxj_name">态度满意度</div>
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