<script>
    window.parent.addNavClass(21);
</script>
<div class="contract-wrap clearfix">
    <div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll2">
		<!-- 上部菜单选项，按钮-->
		<div class="search_box clearfix" style="margin-top:0;padding:5px 15px 4px 10px;overflow: hidden" id="js_search_box_02">
			<form name="search_form" id="subform" method="post" action="">
				<div class="fg_box">
					<p class="fg fg_tex">合同编号：</p>
					<div class="fg">
					    <input type="text" name="number" value="<?=$post_param['number'];?>" class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">款类：</p>

					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name="money_type">
							<option value="">不限</option>
							<?php foreach($config['money_type'] as $key=>$val){?>
							<option value="<?=$key;?>" <?=$key==$post_param['money_type']?'selected':'';?>><?=$val;?></option>
							<?php }?>
						</select>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">款方：</p>

					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name="collect_type">
							<option value="">不限</option>
							<?php foreach($config['collect_type'] as $key=>$val){?>
							<option value="<?=$key;?>" <?=$key==$post_param['collect_type']?'selected':'';?> ><?=$val;?></option>
							<?php }?>
						</select>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">状态：</p>
					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name="status">
							<option value="">请选择</option>
							<?php foreach($config['flow_status'] as $key=>$val){?>
							<option value="<?=$key;?>" <?=$key==$post_param['status'] && $post_param['status'] != ''?'selected':'';?>><?=$val;?></option>
							<?php }?>
						</select>
					</div>
				</div>
				<div class="fg_box">
					<div class="fg mr10" style="*padding-top:10px;">
					    <select class="select w80" name="datetype">
						<?php foreach($config['datetype1'] as $key=>$val){?>
						    <option value="<?=$key;?>" <?=$key==$post_param['datetype']?'selected':'';?>><?=$val;?></option>
						<?php }?>
					    </select>
					</div>
					<div class="fg">
					    <input type="text" class="fg-time" name="start_time" style="width:100px;height:24px;" value="<?=$post_param['start_time'];?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off" onchange="check_time();">
					</div>
					<div class="fg fg_tex03">—</div>
					<div class="fg fg_tex03">
					    <input type="text" class="fg-time" name="end_time" style="width:100px;height:24px;" value="<?=$post_param['end_time'];?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off" onchange="check_time();">
					    &nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="time_reminder"></span>
					</div>
				</div>
			    <script>
				function check_time(){
				    var timemin    =    $("input[name='start_time']").val();	//最小面积
				    var timemax    =    $("input[name='end_time']").val();	//最大面积

				    if(!timemin && !timemax){
					$("#time_reminder").html("");
					$("input[name='is_submit']").val('1');
				    }

				    //最小面积timemin 必须小于 最大面积timemax
				    if(timemin && timemax){
					if(timemin>timemax){
					    $("#time_reminder").html("时间筛选区间输入有误！");
					    $("input[name='is_submit']").val('0');
					    return;
					}else{
					    $("#time_reminder").html("");
					    $("input[name='is_submit']").val('1');
					}
				    }
				}
			    </script>
				<div class="fg_box">
					<input type="hidden" name="page" value="1">
					<input type="hidden" name="orderby_id" value="<?=$post_param['orderby_id'];?>">
					<input type="hidden" name="is_submit" value="1">
					<div class="fg"> <a href="javascript:void(0);" onclick="$('#subform :input[name=page]').val('1');$('#subform').attr('action', '/contract_flow/should_list/');form_submit();return false;" class="btn"><span class="btn_inner">搜索</span></a> </div>
					<div class="fg"> <a href="javascript:void(0);" onclick="$('#subform').attr('action', '/contract_flow/exportShould/');form_submit();$('#subform').attr('action', '');return false;" class="btn"><span class="btn_inner">导出</span></a> </div>
					<div class="fg"> <a href="/contract_flow/should_list" class="reset">重置</a> </div>
				</div>
			</form>
		</div>
<script>
$(function(){
	document.onkeydown = function(e){ //enter
		var ev = document.all ? window.event : e;
		if(ev.keyCode==13) {
			form_submit();return false;
		 }
	}
});
</script>
		<!-- 上部菜单选项，按钮---end-->

		<div class="table_all">
			<div class="title shop_title" id="js_title">
				<table class="table">
					<tr>
						<td class="c10" style="width:11%;">签约日期</td>
						<td class="c10" style="width:11%;">合同编号</td>
						<td class="c10" style="width:11%;">款类</td>
						<td class="c10" style="width:11%;">收款方</td>
						<td class="c10" style="width:11%;"><div class="info"><a href="javascript:void(0)" class="i_text <?php if ($post_param['orderby_id']==1) {echo 'i_down';} else if ($post_param['orderby_id']==2) {echo 'i_up';}?>" onclick="list_order(2);return false;">应收金额<br>(元)</a></div></td>
						<td class="c10" style="width:11%;">付款方</td>
						<td class="c10" style="width:11%;"><div class="info"><a href="javascript:void(0)" class="i_text <?php if ($post_param['orderby_id']==3) {echo 'i_down';} else if ($post_param['orderby_id']==4) {echo 'i_up';}?>" onclick="list_order(4);return false;">应付金额<br>(元)</a></div></td>
						<td class="c10">收付日期</td>
						<td>状态</td>
					</tr>
				</table>
			</div>
			<div class="inner shop_inner" id="js_inner">
				<table class="table" style="*+width:98%;_width:98%;">
				    <?php if($list){foreach($list as $key=>$val){?>
					<tr class="<?=$key%2==1?'bg':''?>" onclick="view_detail(<?=$val['f_id'];?>);">
						<td class="c10"  style="width:11%;"><div class="info"><?=date('Y-m-d',$val['signing_time']);?></div></td>
						<td class="c10"  style="width:11%;"><div class="info c227ac6"><a href="/contract/contract_detail/<?=$val['id'];?>"><?=$val['number'];?></a></div></td>
						<td class="c10"  style="width:11%;"><div class="info"><?=$config['money_type'][$val['money_type']];?></div></td>
						<td class="c10"  style="width:11%;"><div class="info"><?=$config['collect_type'][$val['collect_type']]?$config['collect_type'][$val['collect_type']]:'—';?></div></td>
						<td class="c10"  style="width:11%;"><div class="info"><?=$val['collect_money']?strip_end_0($val['collect_money']):'—';?></div></td>
						<td class="c10"  style="width:11%;"><div class="info"><?=$config['pay_type'][$val['pay_type']]?$config['pay_type'][$val['pay_type']]:'—';?></div></td>
						<td class="c10"  style="width:11%;"><div class="info"><?=$val['pay_money']?strip_end_0($val['pay_money']):'—';?></div></td>
						<td class="c10"  style="width:11%;"><div class="info"><?=$val['flow_time'];?></div></td>
						<td>
                            <?php if($val['flow_status']==2){?>
							<div class="info f00">审核不通过</div>
						    <?php }elseif($val['flow_status']==1){?>
							<div class="info c680">审核通过</div>
						    <?php }else{?>
							<div class="info c999">待审核</div>
						    <?php }?>
						</td>
					</tr>
				    <?php }}else{?>
					<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td>
				    <?php }?>
				</table>
			</div>
		</div>
		<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
            <div class="count_info count_info_float">
                <table>
                    <tr>
                        <td style="padding-right:20px;">应收总计：<span class="bold highlight color_fontsize"><?=strip_end_0($total['collect_money_total']);?></span>&nbsp;元</td>
                        <td style="padding-right:20px;">应付总计：<span class="bold highlight color_fontsize"><?=strip_end_0($total['pay_money_total']);?></span>&nbsp;元</td>
                    </tr>
                </table>
            </div>
			<div class="get_page">
			    <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
			</div>
		</div>
	</div>
</div>

<!--应收应付详情弹框-->
<div id="js_should_pop" class="iframePopBox" style="width: 582px;height:313px;border:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="582px" height="313px" class='iframePop' src="" id="should"></iframe>
</div>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js"></script>
<script>
$(function () {
	function re_width2(){//有表格的时候
		var h1 = $(window).height();
		var w1 = $(window).width() - 170;
		$(".tab-left").height(h1-70);
		$(".forms_scroll2").width(w1);
	};
	re_width2();
	$(window).resize(function(e) {
		re_width2();
	});
	$("#flow_type i").click(function(){
	    $(this).siblings().removeClass("labelOn");
	    $(this).addClass("labelOn");
	    $(this).siblings().find("input").attr('checked',false);
	    $(this).find("input").attr('checked',true);
	})

});	//通过参数判断是否可以被提交
	function form_submit(){
	    $("input[name='page']").val(1);
	    var is_submit = $("input[name='is_submit']").val();
	    if(is_submit ==1){
		$('#subform').submit();
	    }
	}

	//合同列表页 排序
	function list_order(id)
	{
	    var orderby_id = $("input[name='orderby_id']").val();
	    var other_id = id - 1;
	    if( orderby_id == id )
	    {
		$("input[name='orderby_id']").val(other_id);
		$("#subform").submit();
	    }
	    else
	    {
		$("input[name='orderby_id']").val(id);
		$("#subform").submit();
	    }
	}

    $(".table tr").find("a").click(function(event){
            event.stopPropagation();
    });

    function view_detail(id){
        $('#should').attr('src', '/contract/contract_should_detail/'+id);
        openWin('js_should_pop');
    }
</script>
