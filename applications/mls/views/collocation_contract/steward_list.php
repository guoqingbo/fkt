<div class="contract-wrap clearfix">
	<div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll2">
		<form name="search_form" id="search_form" method="post" action="">
			<!-- 上部菜单选项，按钮-->
			<div class="search_box clearfix" style="margin-top:0;overflow: hidden" id="js_search_box_02">
				<div class="fg_box">
					<p class="fg fg_tex">托管编号：</p>
					<div class="fg">
						<input type="text" value="<?php echo $post_param['collocation_id']?>" name='collocation_id' class="input w90 ui-autocomplete-input" autocomplete="off">
						<input type='hidden' name='c_id' id='c_id' value=''/>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex mr10">
						<select class="select w80" name='search_where_time'>
							<option value='0'>选择时间</option>
							<option value='reimbursement_time' <?php if((!empty($post_param['search_where_time']) && $post_param['search_where_time'] == 'reimbursement_time')){echo 'selected="selected"';}?>>报销时间</option>
							<option value='withhold_time' <?php if((!empty($post_param['search_where_time']) && $post_param['search_where_time'] == 'withhold_time')){echo 'selected="selected"';}?>>扣款时间</option>
						</select>
					</p>
					<div class="fg">
						<input type="text" name="time_st" class="fg-time" value="<?php if(isset($_POST['time_st'])) {echo $_POST['time_st'];}?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
					</div>
					<div class="fg fg_tex03">—</div>
					<div class="fg fg_tex03">
						<input type="text" name="time_et" class="fg-time" value="<?php if(isset($_POST['time_et'])) {echo $_POST['time_et'];}?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">报销门店：</p>
					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name="agency_id" id="agency_id">
							<?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>" <?php if ($val['id'] == $post_param['agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }
                            }?>
						</select>
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
					<input type="hidden" name="pg" value="1">
					<input type="hidden" name="orderby_id" id="orderby_id" value="">
					<div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;"><span  class="btn_inner">搜索</span></a> </div>
					<div class="fg"> <a href="/collocation_contract/export/2/3" class="btn"><span class="btn_inner">导出</span></a> </div>
					<div class="fg"> <a href="/collocation_contract/steward_list/" class="reset">重置</a> </div>
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
							<td class="c9">托管合同编号</td>
							<td class="c9">报销日期</td>
							<td class="c9">项目名称</td>
							<td class="c9">费用总计</td>
							<td class="c9">业主承担</td>
							<td class="c9">客户承担</td>
							<td class="c9">公司承担</td>
							<td class="c10">报销部门</td>
							<td class="c9">扣款日期</td>
							<td class="c10">说明</td>
							<td>状态</td>
						</tr>
					</table>
				</div>
				<div class="inner shop_inner" id="js_inner">
					<table class="table" style="*width:98.5%;_width:98.5%;">
					<?php
						if($list){
							foreach($list as $val){
					?>
						<tr>
							<td class="c9" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['c_id']?>/3';return false;"><div class="info c227ac6"><?=$val['collocation_id']?></div></td>
							<td class="c9"><div class="info"><?php echo date('Y-m-d',$val['reimbursement_time'])?></div></td>
							<td class="c9"><div class="info"><?=$val['project_name']?></div></td>
							<td class="c9"><div class="info"><?=strip_end_0($val['total_fee'])?></div></td>
							<td class="c9"><div class="info"><?=strip_end_0($val['owner_bear'])?></div></td>
							<td class="c9"><div class="info"><?=strip_end_0($val['customer_bear'])?></div></td>
							<td class="c9"><div class="info"><?=strip_end_0($val['company_bear'])?></div></td>
							<td class="c10"><div class="info"><?=$val['agency_name']?></div></td>
							<td class="c9"><div class="info"><?php if($val['withhold_time']){echo date('Y-m-d',$val['withhold_time']);}else{echo '—';}?></div></td>
							<td class="c10"><div class="info"><?=$val['remark']?$val['remark']:'—'?></div></td>
							<td>
								<div class="info c999">
									<?php
										if($val['status']==1){
											echo '待审核';
										}elseif($val['status']==2){
											echo '<font color="#33ffcc">生效</font>';
										}elseif($val['status']==3){
											echo '终止';
										}else{
											echo '<font color="red">审核不通过</font>';
										}
									?>
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

});
</script>
