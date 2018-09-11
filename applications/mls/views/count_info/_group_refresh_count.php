<style>
.highcharts-legend-item {display:none};
.zws_mass_float{clear:both;}
.zws_mass_svg{ width:100%;height:295px;float:left;display:inline;overflow:hidden;background:#ffffff;}
.zws_mass_svg_left{width:66%;height:295px;float:left;display:inline;overflow:hidden;}

.zws_mass_svg_left_vertical{width:100%;height:295px;float:left;display:inline;overflow:hidden;}
.zws_mass_svg_right{width:33%;height:295px;float:right;display:inline;overflow:hidden;}
.zws_mass_svg_num{color:#ff9d11;font-size:12px;}
.zws_mass_svg_topNum1{width:100%;height:26px;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/top_num1_03.gif) no-repeat center;font-size:14px;color:#FFF;line-height:26px;float:left;}
.zws_mass_svg_topNum2{width:100%;height:26px;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/top_num2_06.gif) no-repeat center;font-size:14px;color:#FFF;line-height:26px;float:left;}
.zws_mass_svg_topNum3{width:100%;height:26px;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/top_num3_08.gif) no-repeat center;font-size:14px;color:#FFF;line-height:26px;float:left;}
.zws_mass_num_w8{width:8%;}
.zws_mass_color{color:#227ac6;}
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
});
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="data_aly_content clearfix" style="padding-top:13px;background:#fff">
	<div class="main_menu fl" style="width:150px;border:1px solid rgb(230,230,230)">
		<div class="tab_box wh_hover" id="js_tab_box" style="background:#fff;border:none;height:94px;overflow:hidden">
			<a href="/count_info/mass_count/" class="link">综合统计</a>
            <a href="/count_info/mass_count/1" class="link">站点发布</a>
			<a href="/count_info/mass_count/2" class="link link_on">站点刷新</a>
		</div>
	</div>
 <div class="data_aly_content clearfix" style="background:#fff;border:none">
        <div class="content_r">
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
					<input type="button" class="re" value="重置" onclick="location.href='/count_info/mass_count/2'">
				</div>
            </div>

            <div class="zws_mass_svg">
                <!--门店业绩排行属性图-->
                <div class="zws_mass_svg_left">
                    <span class="zws_mass_svg_left_title"></span>
                    <div class="zws_mass_svg_left_vertical" id="svg_left_vertical">

                    </div>
                </div>
                <!--站点数据对比饼状图-->
                <div class="zws_mass_svg_right">

                    <div class="zws_mass_svg_left_vertical"   id="container">

                    </div>
                </div>
            </div>
            <div class="middle_bar clearfix">
                <span class="title fl">工作量排行</span>
                <a class="daochu fr" style="text-align:center; line-height:24px;"  onclick="$('#search_form').attr('action', '/count_info/export_count/2');$('#search_form').submit();$('#search_form').attr('action', '');return false;">导出</a>
            </div>
            <div class="table_paihang_wrap">
                <table class="table_paihang">
                    <tr>
                        <th class="zws_mass_num_w8">排名</th>
                        <th class="">用户名</th>
                        <!--<th class="">58同城</th>-->
                        <th class="">58网邻通</th>
                        <!--<th class="">赶集网</th>-->
                        <th class="">赶集VIP</th>
                        <!--<th class="">安居客</th>-->
                        <th class="">365淘房</th>
                        <th class="">房天下</th>
                    </tr>

                  					<?php
					if($count_num_info){
						foreach ($count_num_info as $key=>$value){
					?>
                    <tr class="date_per">
                        <td class="zws_mass_num_w8"><b class="zws_mass_svg_topNum<?=$key+1?>"><?=$key+1?>&nbsp;</b></td>
                        <td><?=$value['truename']?></td>
                        <!--<td class="zws_mass_color"><?=$value['wuba_num']?></td>-->
                        <td class="zws_mass_color"><?=$value['wuba_vip_num']?></td>
                        <!--<td class="zws_mass_color"><?=$value['ganji_num']?></td>-->
                        <td class="zws_mass_color"><?=$value['ganji_vip_num']?></td>
                        <!--<td class="zws_mass_color"><?=$value['anjuke_num']?></td>-->
                        <td class="zws_mass_color"><?=$value['taofang_num']?></td>
                        <td class="zws_mass_color"><?=$value['fang_num']?></td>
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
<div class="zws_mass_float"></div>

  <script type="text/javascript">
$(function () {

	$(".date_per").on("click",function(){

		var arr = new Array();
		var arrPer = new Array();
		var total = 0;
		for(var i = 2; i < $(this).find("td").length;i++){

		var n= i-2;
		arr[n] = $(this).find("td").eq(i).html();

		total = total+parseInt(arr[n]);

		}

		for(var i = 0 ;i <arr.length;i++ ){


			var str = parseFloat(arr[i])/parseFloat(total)*100;
			str = parseFloat(str.toFixed(2)); //截取小数点两个字符toFixed 转换以后为字符
 			arrPer[i] = str;

		}




		$('#container').highcharts({
        chart: {
            type: 'pie',
            options2d: {
                enabled: true,
                alpha: 0,
                beta: 1
            }
        },
        title: {
            text: '各站点数据占比'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: '数据占比',
            data: [
                //['58同城'+arrPer[0]+"%", arrPer[0]],
                ['58网邻通', arrPer[0]],
                //['赶集网 '+arrPer[2]+"%", arrPer[2]],
                ['赶集VIP', arrPer[1]],
                //['安居客 '+arrPer[4]+"%", arrPer[4]],
                ['365淘房', arrPer[2]],
                ['房天下', arrPer[3]]
            ]
        }]
    });


	})

    if (typeof ($(".date_per").eq(0).find("td").eq(2).html()) == 'undefined') {
        $('#container').highcharts({
            chart: {
                type: 'pie',
                options2d: {
                    enabled: true,
                    alpha: 0,
                    beta: 1
                }
            },
            title: {
                text: '各站点数据占比'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    depth: 35,
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}'
                    }
                }
            },
            series: [{
                type: 'pie',
                name: '数据占比',
                data: [



                ]
            }]

            })
    } else {
var first_date = new Array();
		//first_date[0] = $(".date_per").eq(0).find("td").eq(2).html();
		first_date[0] = $(".date_per").eq(0).find("td").eq(2).html();
		//first_date[2] = $(".date_per").eq(0).find("td").eq(4).html();
		first_date[1] = $(".date_per").eq(0).find("td").eq(3).html();
		//first_date[4] = $(".date_per").eq(0).find("td").eq(6).html();
		first_date[2] = $(".date_per").eq(0).find("td").eq(4).html();
		first_date[3] = $(".date_per").eq(0).find("td").eq(5).html();

	var fitstTotal = 0;
	var firstPer = new Array();
	for(var i = 0; i< first_date.length;i++){
		fitstTotal = parseFloat(fitstTotal) +parseFloat(first_date[i]);
	}

	for(var i = 0 ;i< first_date.length;i++){
		firstPer[i] = parseFloat((parseFloat(first_date[i])/parseFloat(fitstTotal)*100).toFixed(2));
	}


	$('#container').highcharts({
        chart: {
            type: 'pie',
            options2d: {
                enabled: true,
                alpha: 0,
                beta: 1
            }
        },
        title: {
            text: '各站点数据占比'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: '数据占比',
            data: [

				//['58同城'+firstPer[0]+"%", firstPer[0]],
                ['58网邻通', firstPer[0]],
                //['赶集网 '+firstPer[2]+"%", firstPer[2]],
                ['赶集VIP', firstPer[1]],
                //['安居客 '+firstPer[4]+"%", firstPer[4]],
                ['365淘房', firstPer[2]],
                ['房天下', firstPer[3]]

            ]
        }]

		})

    }


});


    </script>

    <script type="text/javascript">
$(function () {
    $('#svg_left_vertical').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: '经纪人群发排行TOP10'
        },

        xAxis: {
            categories: <?=json_encode($top_data['truename'])?>,
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '工作量'
            }
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [ {
            name: '总数',
            data: <?=json_encode($top_data['sum_num'])?>

        }]
    });
});
    </script>
</div>
