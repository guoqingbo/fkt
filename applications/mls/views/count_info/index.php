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

$(function(){
	$(window).resize(function(e) {
		innerHeight2()
	});
	innerHeight2();
	function innerHeight2(){
		$("#js_inner2").height(document.documentElement.clientHeight-53);
		$(".data_aly_content .main_menu").height($(document.body).outerHeight(true));
		$(".content_r").width($(window).width() - 181);
	};
	$("#checkAll").click(function() {
		if ($('input[name="state[]"]').is(':checked')==true) {
			$('input[name="state[]"]').removeAttr("checked",'checked');
			$("#checkAll").val('全选');
		}else{
			$('input[name="state[]"]').attr("checked",'checked');
			$("#checkAll").val('取消全选');
		}
        
    });
    $('.wh_hover a:not(.link_on)').hover(function(){
        $(this).addClass('link_cover_wh');
    },function(){
       $(this).removeClass('link_cover_wh');
    })
    $("#fast_time").change(function(){
    	var fast_time = $(this).val();
    	switch(fast_time){
 	        case '0':
  	           $("#start_date_begin").attr("value","");
  	           $("#start_date_end").attr("value","");
  	           break;
 	        case '1':
  	           $("#start_date_begin").attr("value",GetDateStr(-1));
	           $("#start_date_end").attr("value",GetDateStr(0));
  	           break;
            case '2':
               $("#start_date_begin").attr("value",GetDateStr(-6));
    	       $("#start_date_end").attr("value",GetDateStr(0));
  	           break;
 	        case '3':
	           $("#start_date_begin").attr("value",GetDateStr(-14));
    	       $("#start_date_end").attr("value",GetDateStr(0));
  	           break;
  	        case '4':
  	           $("#start_date_begin").attr("value",GetDateStr(-29));
    	       $("#start_date_end").attr("value",GetDateStr(0));
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
            text: '统计分析'
        },
        subtitle: {
            text: '工作统计'
        },
        xAxis: {
            categories: [
             <?php
             if($chat_data){
                foreach($chat_data as $value){
                    $truename .= '"'.$value['truename'].'",';
                }
                echo $truename;
             }else{
                echo '"暂无数据"';
             }
             ?>
            ]
        },
        yAxis: {
            min: 0,
            title: {
                text: '总操作量(次)'
            }
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: '<span style="width:70px;float:left;display:inline;font-size:10px;white-space:nowrap; word-break:keep-all;">{point.key}</span><table style="width:70px;float:left;display:inline;">',
            pointFormat: '<tr style="width:70px;"><td style="color:{series.color};padding:0;width:60%;float:left;display:inline;font-size:10px;white-space:nowrap; word-break:keep-all;">{series.name}: </td>' +
                '<td style="width:40%;float:left;display:inline;padding:0;font-size:10px;text-align:center;white-space:nowrap; word-break:keep-all;"><b>{point.y} 次</b></td></tr>',
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
            name: '工作量',
            data: [
                   <?php
                   if($chat_data){
                      foreach($chat_data as $value){
                          $sum_num .= $value['sum_num'].',';
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

function openDetailUrl(obj){
	var _url = $(obj).attr("date-url");
    _url += "/?start_date_begin=<?=$post_param['start_date_begin']?>&start_date_end=<?=$post_param['start_date_end']?>";
    $("#js_detail .iframePop").attr("src",_url);
    openWin('js_detail');
}


</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="data_aly_content clearfix" style="padding-top:13px;background:#fff">
	<div class="main_menu fl" style="width:150px;border:1px solid rgb(230,230,230)">
		<div class="tab_box wh_hover" id="js_tab_box" style="background:#fff;border:none;height:94px;overflow:hidden">
			<a href="/count_info/index/" class="link link_on " >工作统计</a>
			<a href="/count_info/index/1" class="link" >房源统计</a>
			<a href="/count_info/index/2" class="link" >客源统计</a>
		</div>
	</div>
	<div class="content_r" id="js_inner2" style="position:relative;overflow-y:scroll;">
		<form name="search_form" id="search_form" method="post" action="" >
			<div class="top_bar clearfix">
                <?php
                if($agency_info) {
                ?>
				<select class="sel_shop fl" id="agency_id" name="agency_id">
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
				<select class="sel_shop add_time fl" name="fast_time" id="fast_time">
					<option value="0" <?php if($post_param['fast_time'] == '0'){ echo 'selected="selected"';}?>>快捷日期</option>
					<option value="2" <?php if($post_param['fast_time'] == '2'){ echo 'selected="selected"';}?>>过去7天</option>
					<option value="3" <?php if($post_param['fast_time'] == '3'){ echo 'selected="selected"';}?>>过去15天</option>
					<option value="4" <?php if($post_param['fast_time'] == '4'){ echo 'selected="selected"';}?>>过去30天</option>
				</select>
				<div class="top_bar_r fr">
					<input type="button" class="re" value="统计" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;">
					<input type="button" class="re" value="重置" onclick="location.href='/count_info/index/'">
				</div>
			</div>
			<?php
			if($post_param['state']){
				foreach ($post_param['state'] as $v){
					switch ($v){
						case 1:
							$checked1 = 'checked="checked"';
							break;
						case 2:
							$checked2 = 'checked="checked"';
							break;
						case 3:
							$checked3 = 'checked="checked"';
							break;
						case 4:
							$checked4 = 'checked="checked"';
							break;
						case 5:
							$checked5 = 'checked="checked"';
							break;
						case 6:
							$checked6 = 'checked="checked"';
							break;
						case 7:
							$checked7 = 'checked="checked"';
							break;
						case 8:
							$checked8 = 'checked="checked"';
							break;
						case 9:
							$checked9 = 'checked="checked"';
							break;
					}
				}
			}else{
				$checked1 = 'checked="checked"';
				$checked2 = 'checked="checked"';
				$checked3 = 'checked="checked"';
				$checked4 = 'checked="checked"';
				$checked5 = 'checked="checked"';
				$checked6 = 'checked="checked"';
                $checked7 = 'checked="checked"';
                $checked8 = 'checked="checked"';
                $checked9 = 'checked="checked"';
			}
			?>
			<div class="top_option clearfix">
				<input type="button" class="check_all fl" id="checkAll" value="取消全选">
				<label class="check_single fl">
					<input type="checkbox" name="state[]" value="1" <?=$checked1?>><span>信息录入</span>
				</label>
				<label class="check_single fl">
					<input type="checkbox" name="state[]" value="2" <?=$checked2?>><span>信息修改</span>
				</label>
				<label class="check_single fl">
					<input type="checkbox" name="state[]" value="3" <?=$checked3?>><span>图片上传</span>
				</label>
				<label class="check_single fl">
					<input type="checkbox" name="state[]" value="7" <?=$checked7?>><span>视频上传</span>
				</label>
				<label class="check_single fl">
					<input type="checkbox" name="state[]" value="6" <?=$checked6?>><span>钥匙提交</span>
				</label>
				<label class="check_single fl">
					<input type="checkbox" name="state[]" value="8" <?=$checked8?>><span>查看保密信息</span>
				</label>
				<label class="check_single fl">
					<input type="checkbox" name="state[]" value="4" <?=$checked4?>><span>勘房</span>
				</label>
				<label class="check_single fl">
					<input type="checkbox" name="state[]" value="5" <?=$checked5?>><span>带看</span>
				</label>
				<label class="check_single fl">
					<input type="checkbox" name="state[]" value="9" <?=$checked9?>><span>普通跟进</span>
				</label>
			</div>

			<div class="top_charts" id="container" style="height:400px;background:#fff"></div>
			<div class="middle_bar clearfix">
				<span class="title fl">工作量排行</span>
				<a class="daochu fr" style="text-align:center; line-height:24px;" onclick="$('#search_form').attr('action', '/count_info/export/');$('#search_form').submit();$('#search_form').attr('action', '');return false;">导出</a>
			</div>
			<div class="table_paihang_wrap">
				<table class="table_paihang">
					<tr>
						<th class="td_long bw71">排名</th>
						<th class="">用户名</th>
						<th class="bw71">信息录入</th>
						<th class="bw71">信息修改</th>
						<th class="bw71">图片上传</th>
                        <th class="bw71">视频上传</th>
                        <th class="bw71">钥匙提交</th>
                        <th class="bw71">查看保密信息</th>
						<th class="bw71">勘房</th>
						<th class="bw71">带看</th>
						<th class="bw71">普通跟进</th>
					</tr>
					<?php
					if($count_num_info){
						foreach ($count_num_info as $key=>$value){
					?>
					<tr>
						<td class="orange"><?=$key+1?></td>
						<td class="black"><?=$value['truename']?></td>
						<td><a href="javascript:void(0)" date-url="/count_info/detail/<?=$value['broker_id']?>/1/1" onClick="openDetailUrl(this)"><?=$value['insert_num']?></a></td>
						<td><a href="javascript:void(0)" date-url="/count_info/detail/<?=$value['broker_id']?>/2/1" onClick="openDetailUrl(this)"><?=$value['modify_num']?></a></td>
						<td><a href="javascript:void(0)" date-url="/count_info/detail/<?=$value['broker_id']?>/3/1" onClick="openDetailUrl(this)"><?=$value['upload_num']?></a></td>
						<td><a href="javascript:void(0)" date-url="/count_info/detail/<?=$value['broker_id']?>/7/1" onClick="openDetailUrl(this)"><?=$value['video_num']?></a></td>
						<td><a href="javascript:void(0)" date-url="/count_info/detail/<?=$value['broker_id']?>/6/1" onClick="openDetailUrl(this)"><?=$value['key_num']?></a></td>
						<td><a href="javascript:void(0)" date-url="/count_info/detail/<?=$value['broker_id']?>/8/1" onClick="openDetailUrl(this)"><?=$value['secret_num']?></a></td>
						<td><a href="javascript:void(0)" date-url="/count_info/detail/<?=$value['broker_id']?>/4/1" onClick="openDetailUrl(this)"><?=$value['look_num']?></a></td>
						<td><a href="javascript:void(0)" date-url="/count_info/detail/<?=$value['broker_id']?>/5/1" onClick="openDetailUrl(this)"><?=$value['looked_num']?></a></td>
						<td><a href="javascript:void(0)" date-url="/count_info/detail/<?=$value['broker_id']?>/9/1" onClick="openDetailUrl(this)"><?=$value['follow_num']?></a></td>
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
		</form>
	</div>
</div>

<div id="js_detail" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
