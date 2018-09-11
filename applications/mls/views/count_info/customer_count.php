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
						for(var i=0;i < msg.length;i++){
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
            text: '统计分析'
        },
        subtitle: {
            text: '客源统计'
        },
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
                text: '房源量(个)'
            }
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: '<span style="width:50px;float:left;display:inline;font-size:10px;text-align:center;">{point.key}</span><table  style="width:50px;float:left;display:inline;">',
            pointFormat: '<tr style="width:100px;"><td style="width:50%;float:left;display:inline;color:{series.color};padding:0;font-size:10px;text-align:center;white-space:nowrap; word-break:keep-all;">{series.name}: </td>' +
                '<td style="width:50%;float:left;display:inline;padding:0;font-size:10px;text-align:center;white-space:nowrap; word-break:keep-all;"><b>{point.y} 个</b></td></tr>',
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
            name: '客源',
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
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="data_aly_content clearfix" style="padding-top:13px;background:#fff">
	<div class="main_menu fl" style="width:150px;border:1px solid rgb(230,230,230)">
		<div class="tab_box wh_hover" id="js_tab_box" style="background:#fff;border:none;height:94px;overflow:hidden">
			<a href="/count_info/index/" class="link">工作统计</a>
			<a href="/count_info/index/1" class="link">房源统计</a>
			<a href="/count_info/index/2" class="link link_on">客源统计</a>
		</div>
	</div>
	<div class="content_r" id="js_inner2" style="position:relative;overflow-y:scroll;">
	<form name="search_form" id="search_form" method="post" action="" >
		<div class="top_bar clearfix">
			<select class="sel_shop fl" name="type">
				<option value="0" <?php if($post_param['type'] == '0'){ echo 'selected="selected"';}?>>类型</option>
				<option value="1" <?php if($post_param['type'] == '1'){ echo 'selected="selected"';}?>>出售</option>
				<option value="2" <?php if($post_param['type'] == '2'){ echo 'selected="selected"';}?>>出租</option>
			</select>

			<select class="sel_shop fl" name="config">
				<option value="1" <?php if($post_param['config'] == '1'){ echo 'selected="selected"';}?>>客源状态</option>
				<option value="2" <?php if($post_param['config'] == '2'){ echo 'selected="selected"';}?>>客源来源</option>
			</select>
            <?php
            if($agency_info) {
            ?>
			<select class="sel_shop fl" name="agency_id" id="agency_id">
				<option value="0">不限</option>
				<?php foreach ($agency_info as $v) { ?>
				<option value="<?=$v['agency_id']?>"<?php if((!empty($post_param['agency_id']) && $post_param['agency_id'] == $v['agency_id'])){echo 'selected="selected"';}?>><?=$v['agency_name']?></option>
				<?php } ?>
			</select>
			<select class="sel_shop fl" name="broker_id" id="broker_id">
				<option value="0">不限</option>
				<?php
				if(is_array($broker) && !empty($broker)){
				    foreach($broker as $value){ ?>
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
			<?php
			}
			if($agency_info2){
			?>
			<select class="sel_shop fl" id="agency_id" name="agency_id">
				<option value="0">不限</option>
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
			<?php
			}
			?>
			<input type="text" class="inp_time fl" id="start_date_begin" name="start_date_begin" onfocus="WdatePicker()" value="<?=$post_param['start_date_begin']?>">
			<p class="fl time_to">-</p>
			<input type="text" class="inp_time fl" id="start_date_end" name="start_date_end" onfocus="WdatePicker()" value="<?=$post_param['start_date_end']?>">
			<div class="top_bar_r fr">
				<input type="button" class="re" value="统计" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;">
				<input type="button" class="re" value="重置" onclick="location.href='/count_info/index/2'">
			</div>
		</div>

		<div class="top_charts_customer" id="container" style="background:#fff"></div>
		<div class="middle_bar clearfix">
			<span class="title fl">客源状态统计</span>
			<a class="daochu fr" style="text-align:center; line-height:24px;" href="/count_info/customer_export/<?=$post_param['type']?>/<?=$post_param['config']?>/<?=$post_param['agency_id']?>/<?=$post_param['broker_id']?>">导出</a>
		</div>
		<div class="table_paihang_wrap">
			<table class="table_paihang">
				<tr>
					<th class="td_long">用户名</th>
					<?php
					if($xAxis){
						foreach ($xAxis as $value){
					?>
					<th><?=$value ?></th>
					<?php
						}
					}
					?>
				</tr>
				<?php
				if($broker_customer){
					foreach ($broker_customer as $k=>$v){
				?>
				<tr>
					<td class="orange"><?=$v['truename']?></td>
					<?php
						if($xAxis){
							foreach ($xAxis as $key=>$value){
					?>
					<td>
					<?php
					switch($post_param['config']){
						case 1:
							echo $v['status_'.$key];
							break;
						case 2:
							echo $v['infofrom_'.$key];
							break;
					}
					?>
					</td>
					<?php
							}
						}
					?>
				</tr>
				<?php
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
