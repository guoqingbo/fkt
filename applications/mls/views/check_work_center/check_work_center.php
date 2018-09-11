<script>
    window.parent.addNavClass(10);
</script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<!--<div id="js_search_box">
    <div  class="shop_tab_title clearfix">
        <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
    </div>
</div>-->


<form name="search_form" id="search_form" method="post" action="<?php echo MLS_URL;?>/check_work_center/index">
<input type="hidden" name="is_submit" value="1">
<div class="search_box clearfix" id="js_search_box">
    <div class="fg_box">
		<p class="fg fg_tex">年份：</p>
        <div class="fg">
			<select class="select" name='year' id='year' onchange='check_date();'>
				<option value="0">请选择</option>
					<?php
						for ( $i = 2016; $i <= date('Y',time()); $i++  ) {
					?>
						<option value="<?php echo $i;?>" <?=($post_param['year']==$i || $year == $i)?'selected':''?>><?php echo $i;?></option>
					<?php } ?>
			</select>
		</div>
        <p class="fg fg_tex">月份：</p>
        <div class="fg">
			<select class="select" name='month' id='month' onchange='check_date();'>
				<option value="0">请选择</option>
					<?php
						for ( $i = 1; $i <= 12; $i++  ) {
					?>
						<option value="<?php echo $i;?>" <?=($post_param['month']==$i || $month == $i)?'selected':''?>><?php echo $i;?></option>
					<?php } ?>
			</select>&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="year_month"></span>
		</div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0);" class="btn"><span class="btn_inner" onclick="form_submit();return false;">搜索</span></a> </div>
        <div class="fg"> <a href="/check_work_center/index/" class="reset" onclick='window.location.href=location'>重置</a> </div>
    </div>
</div>
</form>
<h1 class="attendance-title" id="js_search_box_02"><?=$year?>年<?=$month?>月　<?=$broker_info['company_name'];?>-<?=$broker_info['agency_name'];?>　<?=$broker_info['truename']?>　考勤表</h1>
<div class="attendance-wrap">
    <div class="thead" id="js_title">
        <table>
			<thead>
				<tr>
					<th width="14.2%">星期日</th>
					<th width="14.2%">星期一</th>
					<th width="14.2%">星期二</th>
					<th width="14.2%">星期三</th>
					<th width="14.2%">星期四</th>
					<th width="14.2%">星期五</th>
					<th width="14.2%">星期六</th>

				</tr>
			</thead>
		</table>
    </div>
	<div class="tbody" id="js_inner">
		<table>
			<tbody>
			<?php
				foreach ( $week as $ke => $val ) { ?>
					<tr>
					<?php
						for($i = 0; $i < 7; $i ++) {
							if($val[$i] && in_array($i,$workdays)){?>
								<td width="14.2%">
									<div class="record-wrap active">
									<?php if(is_full_array($data1)){?>
										<div class="day"><?=$val[$i]?>
											<?php if(in_array($val[$i],$day_data_new)){?>
												<a href="javascript:void(0);" onclick="work_detail(<?=$broker_id?>,<?=$year?>,<?=$month?>,<?=$val[$i]?>);">+</a>
											<?php } ?>
										</div>
										<div class="record clearfix">
											<ul>
												<?php
												foreach($data1 as $key=>$vo){
													if($val[$i] == $key){
														if($is_check_work){
															if($vo['cktime1']){
																echo "<li title='正常打卡'>上午</li>";
															}elseif($vo['cktime5']){
																echo "<li title='迟到' class='late'>上午</li>";
															}
															if($vo['cktime2']){
																echo "<li title='正常打卡'>下午</li>";
															}elseif($vo['cktime6']){
																echo "<li title='早退' class='late'>下午</li>";
															}elseif(!$vo['lup3'] && !$vo['lup4'] ){
																echo "<li title='未打卡' class='dont'>下午</li>";
															}
															if(($vo['lup3'] || $vo['lup4']) && !$vo['cktime1'] && !$vo['cktime5']){
																if(date('Y-m-d',time())>$date_past){
																	if($vo['lup3'] && $vo['lup3'] <= '12:00:00'){
																		echo "<li title='请假' class='leave'>上午</li>";
																	}elseif($vo['lup4'] && $vo['lup4'] <= '12:00:00'){
																		echo "<li title='外出' class='dout'>上午</li>";
																	}else{
																		echo "<li title='未打卡' class='dont'>上午</li>";
																	}
																}else{
																	if($vo['lup3'] && $vo['lup3'] <= '12:00:00'){
																		echo "<li title='请假' class='leave'>上午</li>";
																	}
																}
															}
															if(($vo['ldown3'] || $vo['ldown4']) && !$vo['cktime2'] && !$vo['cktime6']){
																if(date('Y-m-d',time())>$date_past){
																	if($vo['ldown3'] && $vo['ldown3'] >= '12:00:00'){
																		echo "<li title='请假' class='leave'>下午</li>";
																	}elseif($vo['ldown4'] && $vo['ldown4'] >= '12:00:00'){
																		echo "<li title='外出' class='dout'>下午</li>";
																	}else{
																		echo "<li title='未打卡' class='dont'>下午</li>";
																	}
																}else{
																	if($vo['ldown3'] && $vo['ldown3'] >= '12:00:00'){
																		echo "<li title='请假' class='leave'>下午</li>";
																	}
																}
															}
														}else{
															if($vo['cktime1']){
																echo "<li title='正常打卡'>上午</li>";
																echo "<li title='正常打卡'>下午</li>";
															}elseif($vo['cktime5']){
																echo "<li title='迟到' class='late'>上午</li>";
																echo "<li title='迟到' class='late'>下午</li>";
															}
															if(($vo['lup3'] || $vo['lup4']) && !$vo['cktime1'] && !$vo['cktime5']){
																if(date('Y-m-d',time())>$date_past){
																	if($vo['lup3'] && $vo['lup3'] <= '12:00:00'){
																		echo "<li title='请假' class='leave'>上午</li>";
																	}elseif($vo['lup4'] && $vo['lup4'] <= '12:00:00'){
																		echo "<li title='外出' class='dout'>上午</li>";
																	}else{
																		echo "<li title='未打卡' class='dont'>上午</li>";
																	}
																}else{
																	if($vo['lup3'] && $vo['lup3'] <= '12:00:00'){
																		echo "<li title='请假' class='leave'>上午</li>";
																	}
																}
															}
															if(($vo['ldown3'] || $vo['ldown4']) && !$vo['cktime1'] && !$vo['cktime5']){
																if(date('Y-m-d',time())>$date_past){
																	if($vo['ldown3'] && $vo['ldown3'] >= '12:00:00'){
																		echo "<li title='请假' class='leave'>上午</li>";
																	}elseif($vo['ldown4'] && $vo['ldown4'] >= '12:00:00'){
																		echo "<li title='外出' class='dout'>上午</li>";
																	}else{
																		echo "<li title='未打卡' class='dont'>上午</li>";
																	}
																}else{
																	if($vo['ldown3'] && $vo['ldown3'] >= '12:00:00'){
																		echo "<li title='请假' class='leave'>上午</li>";
																	}
																}
															}
														}
													}
												}
												if(!in_array($val[$i],$day_data_new)){
													if(date('Y-m-d',time())>$date_past && $date_past_month!=date('Y-m',time()) ){
														echo "<li title='未打卡' class='dont'>上午</li>";
														echo "<li title='未打卡' class='dont'>下午</li>";
													}elseif(date('d',time())>$val[$i] && $date_past_month==date('Y-m',time())){
														echo "<li title='未打卡' class='dont'>上午</li>";
														echo "<li title='未打卡' class='dont'>下午</li>";
													}
												}
												?>
											</ul>
										</div>
									<?php }else{?>
										<div class="day"><?=$val[$i]?></div>
										<div class="record clearfix">
											<?php if(date('m',time())>$month && date('Y',time()) == $year ){?>
											<ul>
												<li title="未打卡" class='dont'>上午</li>
												<li title="未打卡" class='dont'>下午</li>
											</ul>
											<?php }elseif(date('Y',time()) > $year){ ?>
											<ul>
												<li title="未打卡" class='dont'>上午</li>
												<li title="未打卡" class='dont'>下午</li>
											</ul>
											<?php }elseif(date('d',time())>$val[$i] && $date_past_month==date('Y-m',time())){ ?>
											<ul>
												<li title="未打卡" class='dont'>上午</li>
												<li title="未打卡" class='dont'>下午</li>
											</ul>
											<?php } ?>
										</div>
									<?php } ?>
									</div>
								</td>
					<?php }else if($val[$i]){ ?>
						<td width="14.2%">
							<div class="record-wrap active">
								<div class="day"><?=$val[$i]?></div>
								<div class="record clearfix">
								</div>
							</div>
						</td>
					<?php }else{ ?>
						<td width="14.2%">
							<div class="record-wrap active">
								<div class="day"></div>
								<div class="record clearfix">
								</div>
							</div>
						</td>
					<?php }} ?>
					</tr>
				<?php } ?>

			</tbody>
		</table>
		<div class="record-tip clearfix">
			<ul>
				<li>代表打卡正常打卡</li><li class="dont">代表未打卡</li><li class="late">代表迟到或者早退</li><li class="leave">代表请假</li><li class="dout">代表外出</li>
			</ul>
		</div>
	</div>
</div>
<!--考勤记录-->
<div class="pop_box_g" style="width:455px; height:430px; display: none;" id='work_details'>
    <div class="hd header">
        <div class="title" id="title">2014-12-12 <?=$broker_info['truename']?>的考勤记录</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="record-list">
        <ul id='worklist'>
		</ul>
    </div>
	<button type="button" class="btn-hui1 btn-mid JS_Close">关闭</button>
</div>

<script>
//考勤详情弹窗
function work_detail(broker_id,year,month,day){
	$.ajax({
		type: 'post',
		url : '/check_work_center/work_detail/',
		dataType:'json',
		data: {broker_id:broker_id,year:year,month:month,day:day,is_check_work:'<?=$is_check_work?>'},
		success: function(data){
			$('#worklist').html('');
			$("#title").html(data['year']+'-'+data['month']+'-'+data['day']+' '+"<?=$broker_info['truename']?>"+'的考勤记录');
			var result = '';
			$.each(data['work_info'],function (i,val) {
				var status = '';
				var content = '';
				var remark = '';
				if(val['remark']){
					remark = val['remark'];
				}else{
					remark = '';
				}
				if(val['status'] == 3){
					content += "<div class='left'><h3>请假</h3><p>"+val['ltime_up']+"--"+val['ltime_down']+"<br>"+remark+"</p></div>";
				}else if(val['status'] == 4){
					content += "<div class='left'><h3>外出</h3><p>"+val['ltime_up']+"--"+val['ltime_down']+"<br>"+remark+"</p></div>";
				}else{
					content += "<div class='left'><h3>打卡</h3></div>";
				}
				result +="<li class='clearfix'><span class='fl'>"+val['createtime']+"</span>"+content+"</li>"
			});
			$('#worklist').append(result);
			openWin('work_details');
		}
	});

	openWin('work_details');
}
/*
*	aim:	年月等事件的校验
*	author: angel_in_us
*	date:	2015.03.04
*/
function check_date(){
	var year    =    $("#year option:selected").val();	//年
	var month    =    $("#month option:selected").val();	//月

	//alert(year);return false;

	if(!year && !month){
		$("#year_month").html("");
		$("input[name='is_submit']").val('1');
	}else if(year!=0 && month!=0){
		$("#year_month").html("");
		$("input[name='is_submit']").val('1');
	}else{
		$("#year_month").html("请选择年和月！");
		$("input[name='is_submit']").val('0');
	}
}

//通过参数判断是否可以被提交
function form_submit(){
	var is_submit = $("input[name='is_submit']").val();
	if(is_submit ==1){
		$('#search_form').submit();
	}
}

</script>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->


