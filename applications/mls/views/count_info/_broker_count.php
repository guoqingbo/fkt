<style>
.highcharts-legend-item {display:none}
</style>
<script type="text/javascript">
window.parent.addNavClass(18);

$(function () {
	$(window).resize(function(e) {
		innerHeight2()
	});
	innerHeight2();
	function innerHeight2(){
		$("#js_inner2").height(document.documentElement.clientHeight-53);
		$(".data_aly_content .main_menu").height($(document.body).outerHeight(true));
		$(".content_r").width($(window).width() - 181);
	};
     $('.wh_hover a:not(.link_on)').hover(function(){
            $(this).addClass('link_cover_wh');
        },function(){
           $(this).removeClass('link_cover_wh');
        })
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: '员工业绩排行Top10'
        },
        /*subtitle: {
            text: '员工业绩排行'
        },*/
        xAxis: {
            categories: [
            <?php
            if($xAxis){
               foreach($xAxis as $value){
                   $name .= '"'.$value.'",';
               }
               echo $name;
            }else{
               echo '"暂无数据"';
            }
            ?>
            ]
        },
        yAxis: {
            min: 0,
            title: {
                text: '员工业绩排行'
            }
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: '<span style="width:70px;float:left;display:inline;font-size:10px;text-align:center;">{point.key}</span><table style="width:70px;float:left;display:inline;">',
			<?php if($role_level>7){?>
            pointFormat: '<tr style="width:70px;"><td style="color:{series.color};padding:0;width:40%;float:left;display:inline;font-size:10px;text-align:center;white-space:nowrap; word-break:keep-all;">{series.name}: </td>' +
                '<td style="padding:0"><b>**** 元</b></td></tr>',
			<?php }else{?>
			pointFormat: '<tr style="width:70px;"><td style="color:{series.color};padding:0;width:40%;float:left;display:inline;font-size:10px;text-align:center;white-space:nowrap; word-break:keep-all;">{series.name}: </td>' +
                '<td style="width:60%;float:left;display:inline;padding:0;font-size:10px;text-align:center;white-space:nowrap; word-break:keep-all;"><b>{point.y} 元</b></td></tr>',
			<?php } ?>
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0,
                pointWidth:30
            }
        },
        series: [{
            name: '业绩',
            data: [
                <?php
                if($yAxis){
					foreach($yAxis as $value){
                       $sum_num .= $value.',';
					}
					echo $sum_num;
                }else{
                   echo 0;
                }
                ?>
               ]

        }]
    });
});

function do_submit(){
	//$('#search_form :input[name=page]').val('1');
	var is_submit = $("input[name='is_submit']").val();
	if(is_submit ==1){
		$('#search_form').submit();
	}
	return false;
}
function check_date(type){
	var start_date    =    $("#start_date").val();	//起始时间
	var end_date    =    $("#end_date").val();	//结束时间
	if(end_date < start_date && end_date != ''){
		$("#end_date_error").html("时间筛选区间有误！");
		$("input[name='is_submit']").val('0');
	}else{
		$("#end_date_error").html("");
		$("input[name='is_submit']").val('1');
	}
}
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<input type="hidden" name="is_submit" value="1">
<div class="data_aly_content clearfix" style="padding-top:13px;background:#fff">
	<div class="main_menu fl" style="width:150px;border:1px solid rgb(230,230,230)">
		<div class="tab_box wh_hover" id="js_tab_box" style="background:#fff;border:none;height:94px;overflow:hidden">
			<a href="/count_info/performance_count/" class="link link_on">业绩排行</a>
			<a href="/count_info/performance_count/1" class="link">合同统计</a>
			<a href="/count_info/performance_count/2" class="link">分成统计</a>
		</div>
	</div>
	<div class="content_r" id="js_inner2" style="position:relative;overflow-y:scroll;">
	<form name="search_form" id="search_form" method="post" action="" >
		<div class="top_bar clearfix" style="padding-left:30px">
			<p class="fl time_to">业绩类型：</p>
			<select class="sel_shop fl" name="perfortype">
				<option value="0" <?php if($post_param['perfortype'] == '0'){ echo 'selected="selected"';}?>>员工</option>
				<option value="1" <?php if($post_param['perfortype'] == '1'){ echo 'selected="selected"';}?>>门店</option>
			</select>
            <?php if($agency_info) { ?>
			<p class="fl time_to">业绩范围：</p>
			<select class="sel_shop fl" name="agency_id" id="agency_id">
				<!--<option value="0">业绩范围</option>-->
				<?php foreach ($agency_info as $v) { ?>
				<option value="<?=$v['agency_id']?>"<?php if((!empty($post_param['agency_id']) && $post_param['agency_id'] == $v['agency_id'])){echo 'selected="selected"';}?>><?=$v['agency_name']?></option>
				<?php } ?>
			</select>
			<?php
            }
			if($agency_info1){
			?>
			<p class="fl time_to">业绩范围：</p>
			<select class="sel_shop fl">
				<option><?=$agency_info1['agency_name'] ?></option>
			</select>
			<?php }
			if($agency_info2){?>
			<p class="fl time_to">业绩范围：</p>
			<select class="sel_shop fl" id="agency_id" name="agency_id">
				<!--<option value="0">业绩范围</option>-->
				<option value="<?=$agency_info2['agency_id']?>"<?php if((!empty($post_param['agency_id']) && $post_param['agency_id'] == $agency_info2['agency_id'])){echo 'selected="selected"';}?>><?=$agency_info2['agency_name']?></option>
			</select>
			<?php } ?>
			<p class="fl time_to">业绩方式：</p>
			<select class="sel_shop fl" name="type">
				<option value="0" <?php if($post_param['type'] == '0'){ echo 'selected="selected"';}?>>全部</option>
				<option value="1" <?php if($post_param['type'] == '1'){ echo 'selected="selected"';}?>>买卖</option>
				<option value="2" <?php if($post_param['type'] == '2'){ echo 'selected="selected"';}?>>租赁</option>
			</select>
			<p class="fl time_to">日期选择：</p>
			<input type="text" class="inp_time fl" id="start_date" name="start_date" onfocus="WdatePicker()" value="<?=$post_param['start_date']?>" onchange='check_date();'>
			<p class="fl time_to">-</p>
			<input type="text" class="inp_time fl" id="end_date" name="end_date" onfocus="WdatePicker()" value="<?=$post_param['end_date']?>" onchange='check_date();'>
			<p class="fl time_to" style="font-weight:bold;color:red;" id="end_date_error"></p>
			<div class="top_bar_r fr">
				<input type="button" class="re" value="统计" onclick="do_submit();">
				<input type="button" class="re" value="重置" onclick="location.href='/count_info/performance_count/'">
			</div>
		</div>

		<div class="top_charts_customer" id="container" style="background:#fff"></div>
		<div class="middle_bar clearfix">
			<span class="title fl">员工业绩排行榜</span>
			<a class="daochu fr" style="text-align:center; line-height:24px;" href="/count_info/performance_count_export/0/<?=$post_param['agency_id']?>/<?=$post_param['type']?>/<?=$post_param['start_date']?>/<?=$post_param['end_date']?>">导出</a>
		</div>
		<div class="table_all">
			<div class="title" id="js_title">
				<table class="table">
					<tbody>
						<tr>
							<td class="c10">排名</td>
							<td class="c21">所属部门</td>
							<td class="c21">员工</td>
							<td class="c21">买卖业绩</td>
							<td class="c14">租赁业绩</td>
							<td>总业绩</td>
						</tr>
					<tbody>
				</table>
			</div>
			<div class="inner">
				<table class="table table_q">
					<tbody>
						<?php
						if($broker_info_new){
							foreach ($broker_info_new as $key=>$vo){?>
								<tr>
									<td class="c10"><?=$vo['rank']?></td>
									<td class="c21"><?=$vo['agency_name']?></td>
									<td class="c21"><?=$vo['broker_name']?></td>
									<td class="c21"><?=$vo['divide_price_sell']?></td>
									<td class="c14"><?=$vo['divide_price_rent']?></td>
									<td><?=$vo['divide_price_total']?></td>
								</tr>
						<?php }} ?>
					</tbody>
				</table>
			</div>
			<div class="title" id="js_title">
				<table class="table">
					<tbody>
						<tr>
							<td style="width:50%;"></td>

							<td style="width:20%;"></td>
							<td>合计：<strong class="f60"><?=$divide_price_total?></strong></td>
						</tr>
					<tbody>
				</table>
			</div>
			<!--<div class="get_page">
			<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
			</div>-->
		</div>
	</div>
</div>
