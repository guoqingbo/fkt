<style>
.highcharts-legend-item {display:none}
</style>
<script type="text/javascript">
window.parent.addNavClass(18);

function GetDateStr(AddDayCount) {
	var dd = new Date();
	dd.setDate(dd.getDate()+AddDayCount);//获取AddDayCount天后的日期
	var y = dd.getFullYear();
	var m = dd.getMonth()+1;//获取当前月份的日期
	var d = dd.getDate();
	return y+"-"+m+"-"+d;
}

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
	$("#fast_time").change(function(){
    	var fast_time = $(this).val();
    	switch(fast_time){
 	        case '0':
  	           $("#start_date").attr("value","");
  	           $("#end_date").attr("value","");
  	           break;
 	        case '1':
  	           $("#start_date").attr("value",GetDateStr(-1));
	           $("#end_date").attr("value",GetDateStr(0));
  	           break;
            case '2':
               $("#start_date").attr("value",GetDateStr(-6));
    	       $("#end_date").attr("value",GetDateStr(0));
  	           break;
 	        case '3':
	           $("#start_date").attr("value",GetDateStr(-14));
    	       $("#end_date").attr("value",GetDateStr(0));
  	           break;
  	        case '4':
  	           $("#start_date").attr("value",GetDateStr(-29));
    	       $("#end_date").attr("value",GetDateStr(0));
  	           break;
    	}
    });

	$('#agency_id').change(function(){
        var agencyId = $(this).val();
        if(agencyId>0){
            $.ajax({
                type: 'get',
                url : '/my_task/get_broker_ajax/'+agencyId,
                dataType:'json',
                success: function(msg){
                    var str = '';
                    if(msg===''){
                        str = '<option value="0">不限</option>';
                    }else{
                        str = '<option value="0">不限</option>';
                        for(var i=0;i<msg.length;i++){
                            str +='<option value="'+msg[i].broker_id+'">'+msg[i].truename+'</option>';
                        }
                    }
                    $('#broker_id').html(str);
                }
            });
        }else{
        	$('#broker_id').html('<option value="0">不限</option>');
        }
    });

    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ''
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
                text: '分成统计'
            }
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: '<span style="width:70px;float:left;display:inline;font-size:10px;text-align:center;">{point.key}</span><table style="width:70px;float:left;display:inline;">',
			pointFormat: '<tr style="width:70px;"><td style="color:{series.color};padding:0;width:40%;float:left;display:inline;font-size:10px;text-align:center;white-space:nowrap; word-break:keep-all;">{series.name}: </td>' +
            '<td style="width:60%;float:left;display:inline;padding:0;font-size:10px;text-align:center;white-space:nowrap; word-break:keep-all;"><b>{point.y} 笔</b></td></tr>',
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
            name: '分成',
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
	$('#search_form :input[name=page]').val('1');
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

function order(type){
	$('#type').val(type);
	do_submit();
}
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<input type="hidden" name="is_submit" value="1">
<div class="data_aly_content clearfix" style="padding-top:13px;background:#fff">
	<div class="main_menu fl" style="width:150px;border:1px solid rgb(230,230,230)">
		<div class="tab_box wh_hover" id="js_tab_box" style="background:#fff;border:none;height:94px;overflow:hidden">
			<a href="/count_info/performance_count/" class="link">业绩排行</a>
			<a href="/count_info/performance_count/1" class="link">合同统计</a>
			<a href="/count_info/performance_count/2" class="link link_on">分成统计</a>
		</div>
	</div>
	<div class="content_r" id="js_inner2" style="position:relative;overflow-y:scroll;">
	<form name="search_form" id="search_form" method="post" action="" >
		<input type='hidden' value='<?php echo $post_param['type'];?>' id='type' name='type'>
		<div class="top_bar clearfix">
            <?php if($agency_info) { ?>
			<select class="sel_shop fl" name="agency_id" id="agency_id">
				<option value="0">不限</option>
				<?php foreach ($agency_info as $v) { ?>
				<option value="<?=$v['agency_id']?>"<?php if((!empty($post_param['agency_id']) && $post_param['agency_id'] == $v['agency_id'])){echo 'selected="selected"';}?>><?=$v['agency_name']?></option>
				<?php } ?>
			</select>
			<select class="sel_shop fl" id="broker_id" name="broker_id">
				<option value="0">不限</option>
				<?php
				if(is_array($broker) && !empty($broker)){
					foreach($broker as $value){
				?>
				<option value="<?php echo $value['broker_id'];?>" <?php if($post_param['broker_id'] == $value['broker_id']){ echo 'selected="selected"';  } ?>>
				<?php echo $value['truename'];?>
				</option>
				<?php
					}
				}
				?>
			</select>
			<?php
            }
			if($agency_info1){
			?>
			<select class="sel_shop fl">
				<option><?=$agency_info1['agency_name'] ?></option>
			</select>
			<select class="sel_shop fl">
				<option><?=$agency_info1['broker_name'] ?></option>
			</select>
			<?php }
			if($agency_info2){?>
			<select class="sel_shop fl" id="agency_id" name="agency_id">
				<!--<option value="0">业绩范围</option>-->
				<option value="<?=$agency_info2['agency_id']?>"<?php if((!empty($post_param['agency_id']) && $post_param['agency_id'] == $agency_info2['agency_id'])){echo 'selected="selected"';}?>><?=$agency_info2['agency_name']?></option>
			</select>
			<select class="sel_shop fl" id="broker_id" name="broker_id">
				<option value="0">不限</option>
				<?php
				if(is_array($broker) && !empty($broker)){
					foreach($broker as $value){
				?>
				<option value="<?php echo $value['broker_id'];?>" <?php if($post_param['broker_id'] == $value['broker_id']){ echo 'selected="selected"';  } ?>>
				<?php echo $value['truename'];?>
				</option>
				<?php
					}
				}
				?>
			</select>
			<?php } ?>
			<input type="text" class="inp_time fl" id="start_date" name="start_date" onfocus="WdatePicker()" value="<?=$post_param['start_date']?>" onchange='check_date();'>
			<p class="fl time_to">-</p>
			<input type="text" class="inp_time fl" id="end_date" name="end_date" onfocus="WdatePicker()" value="<?=$post_param['end_date']?>" onchange='check_date();'>
			<p class="fl time_to" style="font-weight:bold;color:red;" id="end_date_error"></p>
			<select class="sel_shop add_time fl" name="fast_time" id="fast_time">
				<option value="0" <?php if($post_param['fast_time'] == '0'){ echo 'selected="selected"';}?>>快捷日期</option>
				<option value="2" <?php if($post_param['fast_time'] == '2'){ echo 'selected="selected"';}?>>过去7天</option>
				<option value="3" <?php if($post_param['fast_time'] == '3'){ echo 'selected="selected"';}?>>过去15天</option>
				<option value="4" <?php if($post_param['fast_time'] == '4'){ echo 'selected="selected"';}?>>过去30天</option>
			</select>
			<div class="top_bar_r fr">
				<input type="button" class="re" value="统计" onclick="do_submit();">
				<input type="button" class="re" value="重置" onclick="location.href='/count_info/performance_count/2'">
			</div>
		</div>

		<div class="top_charts_customer" id="container" style="background:#fff"></div>
		<div class="middle_bar clearfix">
			<span class="title fl">统计详情</span>
			<a class="daochu fr" style="text-align:center; line-height:24px;" href="/count_info/performance_count_export/3/<?=$post_param['agency_id']?>/<?=$post_param['type']?>/<?=$post_param['start_date']?>/<?=$post_param['end_date']?>/<?=$post_param['broker_id']?>">导出</a>
		</div>
		<div class="table_all">
			<div class="title" id="js_title">
				<table class="table">
					<tbody>
						<tr>
							<td class="c7">排名</td>
							<td class="c7">所属部门</td>
							<td class="c7">
								<div class="info">
									<a href="javascript:void(0);" onclick="order(1);return false;" class="i_text2 <?php if($post_param['type'] == 1 ){ echo 'i_down2'; } ?>">房源<br></a>
								</div>
							</td>
							<td class="c7">
								<div class="info">
									<a href="javascript:void(0);" onclick="order(2);return false;" class="i_text2 <?php if($post_param['type'] == 2 ){ echo 'i_down2'; } ?>">客源<br></a>
								</div>
							</td>
							<td class="c7">
								<div class="info">
									<a href="javascript:void(0);" onclick="order(3);return false;" class="i_text2 <?php if($post_param['type'] == 3 ){ echo 'i_down2'; } ?>">钥匙<br></a>
								</div>
							</td>
							<td class="c7">
								<div class="info">
									<a href="javascript:void(0);" onclick="order(4);return false;" class="i_text2 <?php if($post_param['type'] == 4 ){ echo 'i_down2'; } ?>">独家<br></a>
								</div>
							</td>
							<td class="c7">
								<div class="info">
									<a href="javascript:void(0);" onclick="order(5);return false;" class="i_text2 <?php if($post_param['type'] == 5 ){ echo 'i_down2'; } ?>">签合同<br></a>
								</div>
							</td>
							<td class="c7">
								<div class="info">
									<a href="javascript:void(0);" onclick="order(6);return false;" class="i_text2 <?php if($post_param['type'] == 6 ){ echo 'i_down2'; } ?>">转介绍<br></a>
								</div>
							</td>
							<td class="c7">
								<div class="info">
									<a href="javascript:void(0);" onclick="order(7);return false;" class="i_text2 <?php if($post_param['type'] == 7 ){ echo 'i_down2'; } ?>">收房<br></a>
								</div>
							</td>
							<td class="c7">
								<div class="info">
									<a href="javascript:void(0);" onclick="order(8);return false;" class="i_text2 <?php if($post_param['type'] == 8 ){ echo 'i_down2'; } ?>">勘察<br></a>
								</div>
							</td>
							<td class="c7">
								<div class="info">
									<a href="javascript:void(0);" onclick="order(9);return false;" class="i_text2 <?php if($post_param['type'] == 9 ){ echo 'i_down2'; } ?>">代办贷款<br></a>
								</div>
							</td>
							<td class="c7">
								<div class="info">
									<a href="javascript:void(0);" onclick="order(10);return false;" class="i_text2 <?php if($post_param['type'] == 10 ){ echo 'i_down2'; } ?>">其他<br></a>
								</div>
							</td>
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
									<td class="c7"><?=$vo['rank']?></td>
									<td class="c7"><?=$vo['broker_name']?></td>
									<td class="c7"><?=$vo['divide1']?></td>
									<td class="c7"><?=$vo['divide2']?></td>
									<td class="c7"><?=$vo['divide3']?></td>
									<td class="c7"><?=$vo['divide4']?></td>
									<td class="c7"><?=$vo['divide5']?></td>
									<td class="c7"><?=$vo['divide6']?></td>
									<td class="c7"><?=$vo['divide7']?></td>
									<td class="c7"><?=$vo['divide8']?></td>
									<td class="c7"><?=$vo['divide9']?></td>
									<td class="c7"><?=$vo['divide10']?></td>
								</tr>
						<?php }} ?>
					</tbody>
				</table>
			</div>
			<div class="get_page">
			<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
			</div>
		</div>
	</form>
	</div>
</div>
