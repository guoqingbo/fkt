
<head>
    <meta charset="utf-8">
    <title>考勤记录-弹出框</title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/integral.css " rel="stylesheet" type="text/css">
</head>


<!--房源详情弹框-->
<div class="pop_box_g" id="" style="width:600px; height:440px; display:block;">
    <div class="hd">
        <div class="title"><?php echo $broker_info['truename']?>&nbsp;&nbsp;<?php echo $broker_info['agency_name']?>&nbsp;&nbsp;<?php echo $month?>月 考勤记录</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod work_time_con" style="padding-bottom: 15px;">
        <div class="inner_con clearfix" style='height:338px'>
			<span class="work_time">
                <p>未打卡：<b><?php echo $none?></b></p>
                <p>迟到：<b><?php echo $late?></b></p>
                <p>早退：<b><?php echo $early?></b></p>
                <p>请假：<b><?php echo $leave?></b></p>
            </span>
            <!--打卡记录-->
            <div class="work_time_list">
				<span>
					<p class="work_timeP1">日期</p>
					<p class="work_timeP1">星期</p>
					<p class="work_timeP1">上午</p>
					<p class="work_timeP1">下午</p>
                    <p class="work_timeP1">打卡IP</p>
				</span>
				<?php if(is_full_array($work_info)){?>
                <ul>
					<?php foreach($work_info as $key=>$vo){?>
                    <li>
                        <p class="work_timeP1"><?php echo $vo['year'].'-'.sprintf('%02d',$vo['month']).'-'.sprintf('%02d',$vo['day']);?></p>
                        <p class="work_timeP1"><?php echo $vo['week']?></p>
						<?php
						if($vo['status'] == 7){
                            echo "<p class='work_timeP1 work_time_late'>未打卡</p><p class='work_timeP1 work_time_late'>未打卡</p>";
						}else{
							if($is_check_work){
								if($vo['cktime1']){
                                    echo "<p class='work_timeP1'>正常(" . substr($vo['cktime1'], 0, -3) . ")</p>";
								}elseif($vo['cktime5']){
                                    echo "<p class='work_timeP1 work_time_late'>迟到(" . substr($vo['cktime5'], 0, -3) . ")</p>";
								}
								if($vo['cktime2']){
                                    echo "<p class='work_timeP1'>正常(" . substr($vo['cktime2'], 0, -3) . ")</p>";
								}elseif($vo['cktime6']){
                                    echo "<p class='work_timeP1 work_time_late'>早退(" . substr($vo['cktime6'], 0, -3) . ")</p>";
								}elseif(!$vo['lup3'] && !$vo['lup4'] ){
                                    echo "<p class='work_timeP1 work_time_late'>未打卡</p>";
								}
								if(($vo['lup3'] || $vo['lup4']) && !$vo['cktime1'] && !$vo['cktime5']){
									//if(date('Y-m-d',time())>$date_past){
										if($vo['lup3'] && $vo['lup3'] <= '12:00:00'){
                                            echo "<p class='work_timeP1 work_time_leave'>请假</p>";
										}elseif($vo['lup4'] && $vo['lup4'] <= '12:00:00'){
                                            echo "<p class='work_timeP1 work_time_leave'>外出</p>";
										}else{
                                            echo "<p class='work_timeP1 work_time_late'>未打卡</p>";
										}
									/*}else{
										if($vo['lup3'] && $vo['lup3'] <= '12:00:00'){
											echo "<p class='work_timeP1 work_time_leave'>请假</p>";
										}
									}*/
								}
								if(($vo['ldown3'] || $vo['ldown4']) && !$vo['cktime2'] && !$vo['cktime6']){
									//if(date('Y-m-d',time())>$date_past){
										if($vo['ldown3'] && $vo['ldown3'] >= '12:00:00'){
                                            echo "<p class='work_timeP1 work_time_leave'>请假</p>";
										}elseif($vo['ldown4'] && $vo['ldown4'] >= '12:00:00'){
                                            echo "<p class='work_timeP1 work_time_leave'>外出</p>";
										}else{
                                            echo "<p class='work_timeP1 work_time_late'>未打卡</p>";
										}
									/*}else{
										if($vo['ldown3'] && $vo['ldown3'] >= '12:00:00'){
											echo "<p class='work_timeP1 work_time_leave'>请假</p>";
										}
									}*/
								}
							}else{
								if($vo['cktime1']){
                                    echo "<p class='work_timeP1'>正常(" . substr($vo['cktime1'], 0, -3) . ")</p>";
                                    echo "<p class='work_timeP1'>正常(" . substr($vo['cktime1'], 0, -3) . ")</p>";
								}elseif($vo['cktime5']){
                                    echo "<p class='work_timeP1 work_time_late'>迟到(" . substr($vo['cktime5'], 0, -3) . ")</p>";
                                    echo "<p class='work_timeP1 work_time_late'>迟到(" . substr($vo['cktime5'], 0, -3) . ")</p>";
								}
								if(($vo['lup3'] || $vo['lup4']) && !$vo['cktime1'] && !$vo['cktime5']){
									//if(date('Y-m-d',time())>$date_past){
										if($vo['lup3'] && $vo['lup3'] <= '12:00:00'){
                                            echo "<p class='work_timeP1 work_time_leave'>请假</p>";
										}elseif($vo['lup4'] && $vo['lup4'] <= '12:00:00'){
                                            echo "<p class='work_timeP1 work_time_leave'>外出</p>";
										}else{
                                            echo "<p class='work_timeP1 work_time_late'>未打卡</p>";
										}
									/*}else{
										if($vo['lup3'] && $vo['lup3'] <= '12:00:00'){
											echo "<p class='work_timeP1 work_time_leave'>请假</p>";
										}
									}*/
								}
								if(($vo['ldown3'] || $vo['ldown4']) && !$vo['cktime1'] && !$vo['cktime5']){
									//if(date('Y-m-d',time())>$date_past){
										if($vo['ldown3'] && $vo['ldown3'] >= '12:00:00'){
                                            echo "<p class='work_timeP1 work_time_leave'>请假</p>";
										}elseif($vo['ldown4'] && $vo['ldown4'] >= '12:00:00'){
                                            echo "<p class='work_timeP1 work_time_leave'>外出</p>";
										}else{
                                            echo "<p class='work_timeP1 work_time_late'>未打卡</p>";
										}
									/*}else{
										if($vo['ldown3'] && $vo['ldown3'] >= '12:00:00'){
											echo "<p class='work_timeP1 work_time_leave'>请假</p>";
										}
									}*/
								}
							}
                        } ?>
                        <p class="work_timeP1"><?php echo $vo['ip'] ?></p>
                    </li>
					<?php } ?>
                </ul>
				<?php }?>
            </div>
			<div class="work_time_list_page">
				<form action="" name="search_form" method="post" id="subform">
				<div class="get_page">
					<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
				</div>
				</form>
			</div>
		</div>
        <button class="work_time_list_btn" onclick ="parent.window.close_detail()" style="cursor:pointer">关闭</button>
    </div>
</div>

