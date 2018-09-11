<div class="contract-wrap clearfix">
<!--left 菜单部分-->
    <div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll2">
		<div id="js_search_box" class="shop_tab_title  scr_clear " style="margin-left:15px;">
			<a href="/collocation_contract/pay_owner_list/1" class="link <?php if($tab == 1){?> link_on <?php }?>">应付业主列表<span class="iconfont hide"></span></a>
			<a href="/collocation_contract/pay_owner_list/2" class="link <?php if($tab == 2){?> link_on <?php }?>">实付业主列表<span class="iconfont hide"></span></a>
		</div>
		<form name="search_form" id="search_form" method="post" action="">
			<!-- 上部菜单选项，按钮-->
			<div class="search_box clearfix" style="margin-top:0;overflow: hidden" id="js_search_box_02">
				<div class="fg_box">
					<p class="fg fg_tex">托管合同编号：</p>
					<div class="fg">
						<input type="text" value="<?php echo $post_param['collocation_id']?>" name='collocation_id' class="input w90 ui-autocomplete-input" autocomplete="off">
						<input type='hidden' name='c_id' id='c_id' value=''/>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">房源编号：</p>
					<div class="fg">
						<input type="text" value="<?php echo $post_param['house_id']?>" name='house_id' class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">状态：</p>

					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name='status'>
						<option value='0' <?php if(isset($post_param['status'])){echo "selected";}?>>请选择</option>
						<option value='1' <?php if($post_param['status']=='1'){echo "selected";}?>>待审核</option>
						<option value='2' <?php if($post_param['status']=='2'){echo "selected";}?>>审核通过</option>
						<option value='3' <?php if($post_param['status']=='3'){echo "selected";}?>>审核不通过</option>
						</select>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex mr10">
				<?php if($tab == 1){?>
					<select class="select w80" name='search_where'>
						<option value='need_pay_time' <?php if((!empty($post_param['search_where']) && $post_param['search_where'] == 'need_pay_time')){echo 'selected="selected"';}?>>应付时间</option>
					</select>
				<?php }else{?>
					<select class="select w80" name='search_where'>
						<option value='actual_pay_time' <?php if((!empty($post_param['search_where']) && $post_param['search_where'] == 'actual_pay_time')){echo 'selected="selected"';}?>>实付时间</option>
					</select>
				<?php }?>
					</p>
					<div class="fg">
						<input type="text" name="time_s" class="fg-time" value="<?php if(isset($_POST['time_s'])) {echo $_POST['time_s'];}?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
					</div>
					<div class="fg fg_tex03">—</div>
					<div class="fg fg_tex03">
						<input type="text" name="time_e" class="fg-time" value="<?php if(isset($_POST['time_e'])) {echo $_POST['time_e'];}?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
					</div>
				</div>
				<div class="fg_box">
					<input type="hidden" name="pg" value="1">
					<input type="hidden" name="orderby_id" id="orderby_id" value="">
					<div class="fg">
						<a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;"><span  class="btn_inner">搜索</span></a>
					</div>
					<div class="fg"> <a href="/collocation_contract/export/1/<?=$tab?>" class="btn"><span class="btn_inner">导出</span></a> </div>
					<div class="fg"> <a href="/collocation_contract/pay_owner_list/<?=$tab?>" class="reset">重置</a> </div>
				</div>
			</div>
<script>
$(function(){
	document.onkeydown = function(e){ //enter
		var ev = document.all ? window.event : e;
		if(ev.keyCode==13) {
			$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;
		 }
	}
});
</script>
			<!-- 上部菜单选项，按钮---end-->

			<div class="table_all">
				<div class="title shop_title" id="js_title">
					<table class="table">
						<tr>
							<td class="c6" style="width:7%;"><?php if($tab == 1){?>应付时间<?php }else{?>实付时间<?php }?></td>
							<td class="c6" style="width:8%;">托管合同编号</td>
							<td class="c6" style="width:8%;">房源编号</td>
							<td class="c6">租金</td>
							<td class="c5">水费</td>
							<td class="c5">电费</td>
							<td class="c5">燃气费</td>
							<td class="c5">网费</td>
							<td class="c6">电视费</td>
							<td class="c5">物业费</td>
							<td class="c5">维护费</td>
							<td class="c5">垃圾费</td>
							<td class="c5">杂费</td>
							<td class="c6">合计</td>
							<?php if($tab == 2){?>
							<td class="c6">收据号</td>
							<?php }?>
							<td class="c6" style="width:8%;">签约时间</td>
							<td>状态</td>
						</tr>
					</table>
				</div>
				<div class="inner shop_inner" id="js_inner" style="*+width:98.5%;*+padding:0 1.5% 0 0;_width:98.5%;_padding:0 1.5% 0 0;">
					<table class="table">
					<?php
						if($list){
							foreach($list as $val){
					?>
						<tr class="">
							<td class="c6" style="width:7%;" >
								<div class="info">
									<?php
										if($val['type'] == '1'){
											echo date('Y-m-d',$val['need_pay_time']);
										}else{
											echo date('Y-m-d',$val['actual_pay_time']);
										}
									?>
								</div>
							</td>
							<td class="c6" style="width:8%;" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['c_id']?>/<?=$tab?>';return false;"><div class="info c227ac6"><?=$val['collocation_id']?></div></td>
							<td class="c6" style="width:8%;" onclick="$('#js_pop_box .iframePop').attr('src','/rent/details_house/<?=substr($val['house_id'],2);?>/4');openWin('js_pop_box');return false;"><div class="info c227ac6"><?=$val['house_id']?></div></td>
							<td class="c6"><div class="info"><?=strip_end_0($val['rental']);?></div></td>
							<td class="c5"><div class="info"><?=strip_end_0($val['water_fee'])?></div></td>
							<td class="c5"><div class="info"><?=strip_end_0($val['ele_fee'])?></div></td>
							<td class="c5"><div class="info"><?=strip_end_0($val['gas_fee'])?></div></td>
							<td class="c5"><div class="info"><?=strip_end_0($val['int_fee'])?></div></td>
							<td class="c6"><div class="info"><?=strip_end_0($val['tv_fee'])?></div></td>
							<td class="c5"><div class="info"><?=strip_end_0($val['property_fee'])?></div></td>
							<td class="c5"><div class="info"><?=strip_end_0($val['preserve_fee'])?></div></td>
							<td class="c5"><div class="info"><?=strip_end_0($val['garbage_fee'])?></div></td>
							<td class="c5"><div class="info"><?=strip_end_0($val['other_fee'])?></div></td>
							<td class="c6"><div class="info"><?=strip_end_0($val['total_fee'])?></div></td>
							<?php if($tab == 2){?>
							<td class="c6" ><div class="info"><?=$val['receipts_num']?></div></td>
							<?php }?>
							<td class="c6"  style="width:8%;" ><div class="info"><?php echo date('Y-m-d',$val['signing_time']);?></div></td>
							<td>
								<div class="info c999">
									<?php if($val['status'] == 1){?>
										待审核
									<?php }elseif($val['status'] == 2){?>
										审核通过
									<?php }else{?>
										审核不通过
									<?php }?>
								</div>
							</td>
						</tr>
					<?php }}else{?>
						<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
					<?php }?>
					</table>
				</div>
			</div>
			<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
				<div class="get_page">
					<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
				</div>
			</div>
		</form>
	</div>
</div>

<!--房源详情弹跳页-->
<div id="js_pop_box" class="iframePopBox" style="width:816px; height:540px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
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

});
</script>
