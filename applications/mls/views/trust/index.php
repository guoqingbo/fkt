<div class="broker-info">
		<div class="info info1">
			<p><?php echo $broker_info['truename'];?></p>
            <?php echo $broker_info['trust_level'];?>
            <a class="btn-lv" href="javascript:void(0);" onclick="open_detail(<?php echo $broker_id;?>)"><span>查看他的评价</span></a>
		</div>
		<div class="info info2">
            <?php if($is_phone_show){?>
			<p class="p1">
                    <?php echo $broker_info['phone'];?>
            </p>
            <?php }?>
			<p class="p2"><?php echo $broker_info['agency_name'];?></p>
		</div>
		<div class="info info2">
			<p class="p2"><?php echo $broker_info['company_name'];?></p>
		</div>
		<div class="info info3">
			<div class="dv1">
				<p class="p1">好评率&nbsp;
                    <?php
                        echo $good_avg_rate['good_rate'] == '' ? '--'
                                : $good_avg_rate['good_rate']."%";
                    ?>
                </p>
				<?php
                if($good_avg_rate['good_rate'] !== '--' && !empty($good_avg_rate['good_rate']))
                {
                    if(!empty($good_avg_rate) && $good_avg_rate['good_rate_avg_high'] > 0)
                    {
                        echo '<p class="p2"><span>高</span>'.$good_avg_rate['good_rate_avg_high'].'%</p>';
                    }
                    else if(!empty($good_avg_rate) && $good_avg_rate['good_rate_avg_high'] < 0)
                    {
                       echo '<p class="p2 p3"><span>低</span>'.abs($good_avg_rate['good_rate_avg_high']).'%</p>';
                    }
                    else
                    {
                        echo '<p class="p2">平均值持平</p>';
                    }
                }
                ?>
			</div>
			<div class="dv2">
				<p class="p1">合作成功率&nbsp;
				<?php echo empty($cop_succ_ratio_info['cop_succ_ratio'])?'--':$cop_succ_ratio_info['cop_succ_ratio'].'%';?>
                </p>
                <?php
                if($cop_succ_ratio_info['cop_succ_ratio'] > 0)
                {
                    $n = $avg_cop_suc_ratio > 0 ? round(($cop_succ_ratio_info['cop_succ_ratio'] - $avg_cop_suc_ratio)/$avg_cop_suc_ratio , 2) : 0;
                    if( $n > 0)
                    {
                        echo '<p class="p2"><span>高</span>'.abs($n).'%</p>';
                    }
                    else if( $n < 0)
                    {
                        echo '<p class="p2 p3"><span>低</span>'.abs($n).'%</p>';
                    }
                    else
                    {
                        echo '<p class="p2">平均值持平</p>';
                    }
                }
                ?>
			</div>
		</div>
		<div class="info info4">
			<div class="dv1">
				<p class="p1">信息真实度</p>
                <?php if ($appraise_avg['infomation']['rate'] >= 0) { ?>
                <p class="p2 up"><?php echo $appraise_avg['infomation']['score'];?>↑</p>
                <?php } else { ?>
                <p class="p2 down"><?php echo $appraise_avg['infomation']['score'];?>↓</p>
                <?php } ?>
			</div>
			<div class="dv2">
				<p class="p1">态度满意度</p>
                <?php if ($appraise_avg['attitude']['rate'] >= 0) { ?>
                <p class="p2 up"><?php echo $appraise_avg['attitude']['score'];?>↑</p>
                <?php } else { ?>
                <p class="p2 down"><?php echo $appraise_avg['attitude']['score'];?>↓</p>
                <?php } ?>
			</div>
			<div class="dv3">
				<p class="p1">业务专业度</p>
                <?php if ($appraise_avg['business']['rate'] >= 0) { ?>
                <p class="p2 up"><?php echo $appraise_avg['business']['score'];?>↑</p>
                <?php } else { ?>
                <p class="p2 down"><?php echo $appraise_avg['business']['score'];?>↓</p>
                <?php } ?>
			</div>
		</div>
		<div class="arrow"></div>
		<div class="mask"></div>
	</div>

<script type="text/javascript">
function open_detail(id)
{
    var _id = parseInt(id);
    var _url = '<?php echo MLS_URL;?>/my_trust_info/evaluate/'+ _id;

    if(_url)
    {
         $("#js_pop_box_appraise1 .iframePop").attr("src",_url);
    }
    openWin('js_pop_box_appraise1');
}
</script>
