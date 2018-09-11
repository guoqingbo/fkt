<div class="contract-wrap clearfix">
	<div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll h90" >
		<div class="contract_top_main contract_table_box mb10 " style="padding-top:10px;">
			<a class=" btn-lv1" style="float:right;" href="javascript:void(0)" onclick="history.go(-1);return false;">返回</a>
			<?php if($collo_detail['status'] != 2){?>
			<a class=" btn-lv1" style="float:right;" href="/collocation_contract/modify/<?=$collo_detail['id']?>">编辑</a>
			<?php }?>
			<h3>托管合同信息</h3>
			<div class="t_list border-b">
				<table class="table02" align="center;">
					<tbody>
						<tr>
							<td style="width:25%"><span style="width:110px;">托管合同编号：</span><font class="c227ac6"><?=$collo_detail['collocation_id']?></font></td>
							<td style="width:22%"><span style="width:110px;">房源编号：</span><font class="c227ac6"><?=$collo_detail['house_id']?></font></td>
							<td style="width:30%"><span style="width:110px;">楼盘名称：</span><?=$collo_detail['block_name']?></td>
							<td style="width:23%"><span style="width:110px;">房源面积：</span><strong><?=$collo_detail['houses_area']?></strong>㎡</td>
						</tr>
						<tr>
							<td colspan="2"><span  style="width:110px;">房源地址：</span><?=$collo_detail['houses_address']?></td>
							<td>
								<span style="width:110px;">物业类型：</span>
								<?php
									if($collo_detail['type'] == 1){
										echo '住宅';
									}elseif($collo_detail['type'] == 2){
										echo '公寓';
									}elseif($collo_detail['type'] == 3){
										echo '别墅';
									}elseif($collo_detail['type'] == 4){
										echo '写字楼';
									}elseif($collo_detail['type'] == 5){
										echo '厂房';
									}elseif($collo_detail['type'] == 6){
										echo '库房';
									}
								?>
							</td>
							<td><span style="width:110px;">签约日期：</span><?php echo date('Y-m-d',$collo_detail['signing_time']);?></td>
						</tr>
						<tr>
							<td colspan="2"><span style="width:110px;">托管日期：</span><?php echo date('Y-m-d',$collo_detail['collo_start_time']);?>至<?php echo date('Y-m-d',$collo_detail['collo_end_time']);?>　共<strong class="f00"><?=$collo_detail['total_month']?></strong>个月</td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="t_list border-b border-t">
				<table class="table02">
					<tbody>
						<tr>
							<td style="width:25%"><span style="width:110px;">业主姓名：</span><?=$collo_detail['owner']?></td>
							<td style="width:22%"><span style="width:110px;">联系方式：</span><?=$collo_detail['owner_tel']?></td>
							<td colspan="2"><span style="width:110px;">身份证号：</span><?=$collo_detail['owner_idcard']?></td>
						</tr>
						<tr>
							<td style="width:25%"><span style="width:110px;">签约门店：</span><?=$collo_detail['agency_name']?></td>
							<td style="width:22%"><span style="width:110px;">签 约 人：</span><?=$collo_detail['broker_name']?></td>
							<td style="width:30%"><span style="width:110px;">联系方式：</span><?=$collo_detail['broker_tel']?></td>
							<td style="width:23%"><span style="width:110px;">付款渠道：</span><?=$collo_detail['pay_ditch']?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="t_list border-t">
				<table class="table02">
					<tbody>
						<tr>
							<td  style="width:15%"><span style="width:110px;">每月租金：</span><?=strip_end_0($collo_detail['rental'])?></td>
							<td  style="width:22%">
								<span style="width:110px;">付款方式：</span>
								<?php
									if($collo_detail['pay_type'] == 1){
										echo '月付';
									}elseif($collo_detail['pay_type'] == 2){
										echo '季付';
									}elseif($collo_detail['pay_type'] == 3){
										echo '半年付';
									}elseif($collo_detail['pay_type'] == 4){
										echo '年付';
									}elseif($collo_detail['pay_type'] == 5){
										echo '其他';
									}
								?>
							</td>
							<td  style="width:30%"><span style="width:110px;">租金总额：</span><?=strip_end_0($collo_detail['rental_total'])?>元</td>
							<td  style="width:33%"><span style="width:110px;">押金金额：</span><?=strip_end_0($collo_detail['desposit'])?>元</td>
						</tr>
						<tr>
							<td style="width:15%"><span style="width:110px;">违约金额：</span><?=strip_end_0($collo_detail['penal_sum'])?>元</td>
							<td style="width:22%">
								<span style="width:110px;">税费承担：</span>
								<?php
									if($collo_detail['tax_type'] == 1){
										echo '业主';
									}elseif($collo_detail['tax_type'] == 2){
										echo '客户';
									}elseif($collo_detail['tax_type'] == 3){
										echo '公司';
									}
								?>
							</td>
							<td style="width:30%"><span style="width:110px;">物业费用：</span><?=strip_end_0($collo_detail['property_fee'])?>元/月</td>
							<td style="width:33%">
								<span style="width:110px;">物管承担：</span>
								<?php
									if($collo_detail['property_manage_assume'] == 1){
										echo '业主';
									}elseif($collo_detail['property_manage_assume'] == 2){
										echo '客户';
									}elseif($collo_detail['property_manage_assume'] == 3){
										echo '公司';
									}
								?>
							</td>
						</tr>
						<tr>
							<td colspan="2"><span style="width:110px;">中介佣金：</span><?=strip_end_0($collo_detail['agency_commission'])?></td>
							<td colspan="2"><span style="width:110px;">免租时间：</span><?=intval($collo_detail['rent_free_time'])?>天</td>
						</tr>
						<tr>
							<td colspan="2"><span style="width:110px;">业绩分成1：</span><?=$collo_detail['divide_a_agency_name']?>&nbsp;<?=$collo_detail['divide_a_broker_name']?>&nbsp;<?=strip_end_0($collo_detail['divide_a_money'])?>元</td>
							<td colspan="2"><span style="width:110px;">业绩分成2：</span><?=$collo_detail['divide_b_agency_name']?>&nbsp;<?=$collo_detail['divide_b_broker_name']?>&nbsp;<?=strip_end_0($collo_detail['divide_b_money'])?>元</td>
						</tr>
						<tr>
							<td><span style="width:110px;">退房经纪：</span><?=$collo_detail['out_agency_name']?>&nbsp;&nbsp;<?=$collo_detail['out_broker_name']?></td>
							<td colspan="3"><span style="width:110px;">终止协议号：</span><?=$collo_detail['stop_agreement_num']?></td>
						</tr>
						<tr>
							<td colspan="4"><span style="width:110px;">物品清单：</span><?=$collo_detail['list_items']?></td>
						</tr>
						<tr>
							<td colspan="4"><span style="width:110px;">合同备注：</span><?=$collo_detail['remarks']?></td>
						</tr>
						<tr>
							<td colspan="4"><span style="width:110px;">审核意见：</span><font class="c680"><?=$collo_detail['audit_view']?></font></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div id="js_search_box" class="shop_tab_title  scr_clear m0">
		<?php if($tab == 1){?>
			<?php if (isset($auth['add']['auth']) && $auth['add']['auth']) { ?>
				<a href="#" class="btn-lv fr ml10" onclick="$('#js_pop_add_attendance_pl').find('.empty').val('');openWin('js_pop_add_attendance_pl');"><span>+ 批量添加应付业主</span></a>
				<a href="#" class="btn-lv fr" onclick="$('#js_pop_add_attendance_kq').find('.empty').val('');openWin('js_pop_add_attendance_kq');"><span>+ 添加应付业主</span></a>
			<?php }else{?>
				<a href="#" class="btn-lv fr ml10" onclick="permission_none();"><span>+ 批量添加应付业主</span></a>
				<a href="#" class="btn-lv fr" onclick="permission_none();"><span>+ 添加应付业主</span></a>
			<?php }?>
		<?php }elseif($tab == 2 && !empty($need_date)){?>
			<?php if (isset($auth['add']['auth']) && $auth['add']['auth']) { ?>
				<a href="#" class="btn-lv fr" onclick="$('#js_pop_add_attendance_sf').find('.empty').val('');openWin('js_pop_add_attendance_sf');"><span>+ 添加实付业主</span></a>
			<?php }else{?>
				<a href="#" class="btn-lv fr" onclick="permission_none();"><span>+ 添加实付业主</span></a>
			<?php }?>
		<?php }elseif($tab == 3){?>
			<?php if (isset($auth['add_ste']['auth']) && $auth['add_ste']['auth']) { ?>
				<a href="#" class="btn-lv fr" onclick="$('#js_pop_add_attendance_gj').find('.empty').val('');openWin('js_pop_add_attendance_gj');"><span>+ 添加管家费用</span></a>
			<?php }else{?>
				<a href="#" class="btn-lv fr" onclick="permission_none();"><span>+ 添加管家费用</span></a>
			<?php }?>
		<?php }elseif($tab == 4){?>
			<?php if (isset($auth['add_rent']['auth']) && $auth['add_rent']['auth']) { ?>
				<a href="/collocation_contract/add_rent_contract/<?=$collo_detail['id']?>" class="btn-lv fr"><span>+ 添加出租合同</span></a>
			<?php }else{?>
				<a href="#" class="btn-lv fr" onclick="permission_none();"><span>+ 添加出租合同</span></a>
			<?php }?>
		<?php }?>
			<a href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/1" class="link <?php if($tab == 1){?> link_on <?php }?>">应付业主<span class="iconfont hide"></span></a>
			<a href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/2" class="link <?php if($tab == 2){?> link_on <?php }?>">实付业主<span class="iconfont hide"></span></a>
			<a href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/3" class="link <?php if($tab == 3){?> link_on <?php }?>">管家费用<span class="iconfont hide"></span></a>
			<a href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/4" class="link <?php if($tab == 4){?> link_on <?php }?>">出租合同<span class="iconfont hide"></span></a>
			<a href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/5" class="link <?php if($tab == 5){?> link_on <?php }?>">合同跟进<span class="iconfont hide"></span></a>
		</div>
		<div class="table_all m0">
			<form name="search_form" id="search_form" method="post" action="">
			<?php if($tab == 1 || $tab == 2){?>
				<div class="title shop_title">
					<table class="table">
						<tbody><tr>
							<td class="c10">日期</td>
							<td class="c4">租金</td>
							<td class="c4">水费</td>
							<td class="c4">电费</td>
							<td class="c5">燃气费</td>
							<td class="c4">网费</td>
							<td class="c5">电视费</td>
							<td class="c5">物业费</td>
							<td class="c5">维护费</td>
							<td class="c5">垃圾费</td>
							<td class="c4">杂费</td>
							<td class="c5">合计</td>
							<?php if($tab == 2){?>
							<td class="c6">单据号</td>
							<?php }?>
							<td class="c12">录入门店</td>
							<td class="c5">录入人</td>

							<td class="c5">状态</td>
							<td>操作</td>
						</tr>
					</tbody></table>
				</div>
				<div class="inner shop_inner" style="height:223px;">
					<table class="table" style="*+width:99%;_width:99%;">
					<?php if($tab == 1){?>
						<tbody>
						<?php
								if($need_pay){
									foreach($need_pay as $val){
						?>
							<tr>
								<td class="c10" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?php echo date('Y-m-d',$val['need_pay_time']);?></div></td>
								<td class="c4" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['rental'])?></div></td>
								<td class="c4" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['water_fee'])?></div></td>
								<td class="c4" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['ele_fee'])?></div></td>
								<td class="c5" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['gas_fee'])?></div></td>
								<td class="c4" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['int_fee'])?></div></td>
								<td class="c5" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['tv_fee'])?></div></td>
								<td class="c5" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['property_fee'])?></div></td>
								<td class="c5" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['preserve_fee'])?></div></td>
								<td class="c5" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['garbage_fee'])?></div></td>
								<td class="c4" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['other_fee'])?></div></td>
								<td class="c5" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['total_fee'])?></div></td>
								<td class="c12" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=$brokerinfo['agency_name']?></div></td>
								<td class="c5" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info"><?=$brokerinfo['truename']?></div></td>
								<td class="c5" onclick = "need_pay_edit('<?=$val['id']?>',1)"><div class="info c999"><?php if($val['status'] == 1){echo '待审核';}elseif($val['status'] == 2){echo '<font color="green">审核通过</font>';}else{echo '<font color="red">审核未通过</font>';}?></div></td>
								<td>
								<?php if($val['status'] != 2){?>
									<?php if (isset($auth['edit']['auth']) && $auth['edit']['auth']) { ?>
										<a href="javascript:void(0)" onclick="need_pay_edit('<?=$val['id']?>',1)">修改</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">修改</a>
									<?php }?>
										<span style="margin:0 5px;color:#b2b2b2;">|</span>
									<?php if (isset($auth['delete']['auth']) && $auth['delete']['auth']) { ?>
										<a href="javascript:void(0)" onclick="del_need_pay('<?=$val['id']?>',1,'<?=$val['c_id']?>')">删除</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">删除</a>
									<?php }?>
								<?php }else{?>
									<span style='grey'>修改<span style="margin:0 5px;color:#b2b2b2;">|</span>删除</span>
								<?php }?>
								</td>
							</tr>
						<?php }}else{?>
							<tr><td><span class="no-data-tip">您还未添加应付业主！</span></td></tr>
						<?php }?>
						</tbody>
					<?php }else{?>
						<tbody>
						<?php
								if($actual_pay){
									foreach($actual_pay as $val){
						?>
							<tr>
								<td class="c10"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?php echo date('Y-m-d',$val['actual_pay_time']);?></div></td>
								<td class="c4"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['rental'])?></div></td>
								<td class="c4"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['water_fee'])?></div></td>
								<td class="c4"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['ele_fee'])?></div></td>
								<td class="c5"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['gas_fee'])?></div></td>
								<td class="c4"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['int_fee'])?></div></td>
								<td class="c5"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['tv_fee'])?></div></td>
								<td class="c5"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['property_fee'])?></div></td>
								<td class="c5"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['preserve_fee'])?></div></td>
								<td class="c5"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['garbage_fee'])?></div></td>
								<td class="c4"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['other_fee'])?></div></td>
								<td class="c5"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['total_fee'])?></div></td>
								<td class="c6"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=$val['receipts_num']?></div></td>
								<td class="c12"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=$brokerinfo['agency_name']?></div></td>
								<td class="c5"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info"><?=$brokerinfo['truename']?></div></td>
								<td class="c6"  onclick="need_pay_edit('<?=$val['id']?>',2)"><div class="info c999"><?php if($val['status'] == 1){echo '待审核';}elseif($val['status'] == 2){echo '<font color="green">审核通过</font>';}else{echo '<font color="red">审核未通过</font>';}?></div></td>
								<td>
								<?php if($val['status'] != 2 && $val['fund_status'] == 1){?>
									<?php if (isset($auth['edit']['auth']) && $auth['edit']['auth']) { ?>
										<a href="javascript:void(0)" onclick="need_pay_edit('<?=$val['id']?>',2)">修改</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">修改</a>
									<?php }?>
										<span style="margin:0 5px;color:#b2b2b2;">|</span>
									<?php if (isset($auth['delete']['auth']) && $auth['delete']['auth']) { ?>
										<a href="javascript:void(0)" onclick="del_need_pay('<?=$val['id']?>',2,'<?=$val['c_id']?>');">删除</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">删除</a>
									<?php }?>
								<?php }elseif($val['status'] == 2 && $val['fund_status'] == 1){?>
									<?php if (isset($auth['sure']['auth']) && $auth['sure']['auth']) { ?>
										<a href='#' onclick="modify_fund_status('<?=$val['id']?>','<?=$val['c_id']?>')"><font color='blue'>确认付款</font></a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">确认付款</a>
									<?php }?>
								<?php }else{?><!--审核通过，资金状态修改为2-->
									<a href='#'><font color='blue'>已确认</font></a>
								<?php }?>
								</td>
							</tr>
						<?php }}else{?>
							<tr><td><span class="no-data-tip">您还未添加实付业主！</span></td></tr>
						<?php }?>
						</tbody>
					<?php }?>
					</table>
				</div>
			<?php }elseif($tab == 3){?>
				<div class="title shop_title">
					<table class="table">
						<tr>
							<td class="c9">报销日期</td>
							<td class="c9">项目名称</td>
							<td class="c6">费用总计</td>
							<td class="c6">业主承担</td>
							<td class="c6">客户承担</td>
							<td class="c6">公司承担</td>
							<td class="c15">报销部门</td>
							<td class="c9">扣款日期</td>
							<td class="c10">说明</td>
							<td class="c9">状态</td>
							<td>操作 </td>
						</tr>
					</table>
				</div>
				<div class="inner shop_inner" style="height:223px;">
					<table class="table">
					<?php
						if($steward_pay){
							foreach($steward_pay as $val){
					?>
						<tr>
							<td class="c9"  onclick="need_pay_edit('<?=$val['id']?>',3)"><div class="info"><?php echo date('Y-m-d',$val['reimbursement_time']);?></div></td>
							<td class="c9"  onclick="need_pay_edit('<?=$val['id']?>',3)"><div class="info"><?=$val['project_name']?></div></td>
							<td class="c6"  onclick="need_pay_edit('<?=$val['id']?>',3)"><div class="info"><?=strip_end_0($val['total_fee'])?></div></td>
							<td class="c6"  onclick="need_pay_edit('<?=$val['id']?>',3)"><div class="info"><?=strip_end_0($val['owner_bear'])?></div></td>
							<td class="c6"  onclick="need_pay_edit('<?=$val['id']?>',3)"><div class="info"><?=strip_end_0($val['customer_bear'])?></div></td>
							<td class="c6"  onclick="need_pay_edit('<?=$val['id']?>',3)"><div class="info"><?=strip_end_0($val['company_bear'])?></div></td>
							<td class="c15"  onclick="need_pay_edit('<?=$val['id']?>',3)"><div class="info"><?=$val['agency_name']?></div></td>
							<td class="c9"  onclick="need_pay_edit('<?=$val['id']?>',3)"><div class="info"><?php if($val['withhold_time']){echo date('Y-m-d',$val['withhold_time']);}else{echo '--';}?></div></td>
							<td class="c10"  onclick="need_pay_edit('<?=$val['id']?>',3)"><div class="info"><?=$val['remark']?></div></td>
							<td class="c9"  onclick="need_pay_edit('<?=$val['id']?>',3)">
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
							<td>
								<?php if($val['status'] != 2){?>
									<?php if (isset($auth['edit_ste']['auth']) && $auth['edit_ste']['auth']) { ?>
										<a href="javascript:void(0)" onclick="need_pay_edit('<?=$val['id']?>',3)">修改</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">修改</a>
									<?php }?>
										<span style="margin:0 5px;color:#b2b2b2;">|</span>
									<?php if (isset($auth['delete_ste']['auth']) && $auth['delete_ste']['auth']) { ?>
										<a href="javascript:void(0)" onclick="del_need_pay('<?=$val['id']?>',3,'<?=$val['c_id']?>');">删除</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">删除</a>
									<?php }?>
								<?php }else{?>
									<span style='grey'>修改<span style="margin:0 5px;color:#b2b2b2;">|</span>删除</span>
								<?php }?>
							</td>
						</tr>
					<?php }}else{?>
						<tr><td><span class="no-data-tip">您还未添加管家费用！</span></td></tr>
					<?php }?>
					</table>
				</div>
			<?php }elseif($tab == 4){?>
				<div class="title shop_title">
					<table class="table">
						<tr>
							<td class="c9">出租合同编号</td>
							<td class="c9">租客姓名</td>
							<td class="c9">租金（元/月）</td>
							<td class="c9">付款方式</td>
							<td class="c9">起租时间</td>
							<td class="c9">停租时间</td>
							<td class="c9">签约时间</td>
							<td class="c12">签约门店</td>
							<td class="c9">签约人</td>
							<td class="c9">合同状态</td>
							<td>操作 </td>
						</tr>
					</table>
				</div>
				<div class="inner shop_inner"  style="height:223px;">
					<table class="table">
					<?php
						if($rent_pay){
							foreach($rent_pay as $val){
					?>
						<tr>
							<td class="c9" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1'"><div class="info c227ac6"><?=$val['collo_rent_id']?></div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1'"><div class="info"><?=$val['customer_name']?></div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1'"><div class="info"><?=strip_end_0($val['rental'])?></div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1'">
								<div class="info">
									<?php
											if($val['pay_type']==1){
												echo '月付';
											}elseif($val['pay_type']==2){
												echo '季付';
											}elseif($val['pay_type']==3){
												echo '半年付';
											}elseif($val['pay_type']==4){
												echo '年付';
											}else{
												echo '其他';
									}?>
								</div>
							</td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1'"><div class="info"><?php echo date('Y-m-d',$val['rent_start_time']);?></div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1'"><div class="info"><?php echo date('Y-m-d',$val['rent_end_time']);?></div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1'"><div class="info"><?php echo date('Y-m-d',$val['signing_time']);?></div></td>
							<td class="c12" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1'"><div class="info"><?=$val['agency_name']?></div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1'"><div class="info"><?=$val['broker_name']?></div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1'">
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
							<td>
							<?php if($val['status'] !=2 ){?>
								<?php if (isset($auth['edit_rent']['auth']) && $auth['edit_rent']['auth']) { ?>
									<a href="/collocation_contract/rent_modify/<?=$val['id']?>">修改</a>
								<?php }else{?>
									<a href="javascript:void(0)" onclick="permission_none();">修改</a>
								<?php }?>
									<span style="margin:0 5px;color:#b2b2b2;">|</span>
								<?php if (isset($auth['delete_rent']['auth']) && $auth['delete_rent']['auth']) { ?>
									<a href="javascript:void(0)" onclick="del_need_pay('<?=$val['id']?>',4,'<?=$val['c_id']?>');">删除</a>
								<?php }else{?>
									<a href="javascript:void(0)" onclick="permission_none();">删除</a>
								<?php }?>
							<?php }else{?>
								<?php if (isset($auth['cancel_rent']['auth']) && $auth['cancel_rent']['auth']) { ?>
									<a href="javascript:void(0)" onclick="rent_contract_cancel('<?=$val['id']?>','<?=$val['c_id']?>')">作废</a>
								<?php }else{?>
									<a href="javascript:void(0)" onclick="permission_none();">作废</a>
								<?php }?>
							<?php }?>
							</td>
						</tr>
					<?php }}else{?>
						<tr><td><span class="no-data-tip">您还未添加出租合同！</span></td></tr>
					<?php }?>
					</table>
				</div>
			<?php }elseif($tab == 5){?>
				<div class="title shop_title">
					<table class="table">
						<tr>
							<td class="c20">跟进日期</td>
							<td class="c20">类别</td>
							<td class="c40">内容</td>
							<td>修改人</td>
						</tr>
					</table>
				</div>
				<div class="inner shop_inner"  style="height:223px;">
					<table class="table">
					<?php
						if($follow){
							foreach($follow as $val){
					?>
						<tr>
							<td class="c20"><div class="info"><?php echo date('Y-m-d H:i:s',$val['updatetime'])?></div></td>
							<td class="c20"><div class="info"><?=$val['type_name']?></div></td>
							<td class="c40"><div class="info align-left"><?=$val['content']?></div></td>
							<td><div class="info"><?=$val['broker_name']?></div></td>
						</tr>
					<?php }}else{?>
						<tr><td><span class="no-data-tip">您还未有跟进记录！</span></td></tr>
					<?php }?>
					</table>
				</div>
			<?php }?>
				<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn" style="margin-left:0;margin-right:0;">
					<?php if($tab == 1 || $tab == 2){?>
					<p class="fl">
						应付总计：<strong class="ff9d11"><?=strip_end_0($need_total_fee);?></strong>元　　
						实付总计：<strong class="ff9d11"><?=strip_end_0($actual_total_fee);?></strong>元　　
						余额总计：<strong class="ff9d11"><?=strip_end_0($value);?></strong>元
					</p>
					<?php }?>
					<div class="get_page">
						<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!--单次添加,编辑应付业主弹窗-->
<div class="pop_box_g" id="js_pop_add_attendance_kq" style="width:510px; height:430px; display: none;">
    <div class="hd header">
        <div class="title" id='only_add'>添加应付业主</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="reclaim-mod reclaim-mod2">
        <form action="#" method="post" id='need_pay_add'>
            <table>
				<tr>
					<td width="70" class="label">租金：</td>
                    <td width="105"><input class="input_text w90 need_fee empty" type="text" id="rental" name='rental'></td>
                    <td width="60">元</td>
					<td width="70"  class="label">水费：</td>
                    <td width="105"><input class="input_text w90 need_fee empty" type="text" id="water_fee" name='water_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">电费：</td>
                    <td><input class="input_text w90 need_fee empty" type="text" id="ele_fee" name='ele_fee'></td>
                    <td>元</td>
					<td  class="label">燃气费：</td>
                    <td><input class="input_text w90 need_fee empty" type="text" id="gas_fee" name='gas_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">网费：</td>
                    <td><input class="input_text w90 need_fee empty" type="text" id="int_fee" name='int_fee'></td>
                    <td>元</td>
					<td  class="label">电视费：</td>
                    <td><input class="input_text w90 need_fee empty" type="text" id="tv_fee" name='tv_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">物业费：</td>
                    <td><input class="input_text w90 need_fee empty" type="text" id="property_fee" name='property_fee'></td>
                    <td>元</td>
					<td  class="label">维护费：</td>
                    <td><input class="input_text w90 need_fee empty" type="text" id="preserve_fee" name='preserve_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">垃圾费：</td>
                    <td><input class="input_text w90 need_fee empty" type="text" id="garbage_fee" name='garbage_fee'></td>
                    <td>元</td>
					<td  class="label">杂费：</td>
                    <td><input class="input_text w90 need_fee empty" type="text" id="other_fee" name='other_fee'></td>
                    <td>元</td>
				</tr>
			</table>
			<div class="total clearfix">
				<div class="fr"><strong>合计：</strong><font class="f60 f14 totle_fee empty_fee" id='need_total'></font> 元</div>
			</div>
			<script>
				$(document).ready(function(){
					$('.need_fee').change(function(){
						var total_fee=0;
						$(".need_fee").each(function(){
							if($(this).val()!=0){
								total_fee +=  parseFloat($(this).val());
							}
						});
						 $(".totle_fee").html(total_fee);
						$("#need_pay_total").val(total_fee);
					});
					$('#need_pay_time').focus(function(){
						$('.text_time').hide();
					});
				});
			</script>
            <table>
                <tr>
					<td width="70" class="label"><font class="red">*</font>应付日期：</td>
                    <td><input type="text" class="input_text time_bg" value='<?php echo date('Y-m-d',time())?>' onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" name="need_pay_time" id='need_pay_time'><div class="errorBox"></div></td>
					<td><span style='display:none' class='text_time'>请选择一个应付日期</span></td>
                </tr>
                <tr>
                    <td class="label">说明：</td>
                    <td colspan="2"><textarea name="remark" id="remark" class="textarea textarea2 empty"></textarea></td>
                </tr>
                <tr>
                	<td colspan="3" class="center">
						<button type="button" id="dialog_share" class="btn-lv1 btn-left">确定</button>
						<button type="button" id="dialog_share3" class="btn-lv1 btn-left" style='display:none'>保存</button>
						<button type="button" class="btn-hui1 JS_Close">取消</button>
					</td>
                </tr>
            </table>
			<input type="hidden" class="input" name="c_id" value="<?=$collo_detail['id']?>" id='c_id'>
			<input type="hidden" class="input" name="collocation_id" value="<?=$collo_detail['collocation_id']?>" id='c_id'>
			<input type="hidden" class="input" name="need_pay_id" value="" id='need_pay_id'>
			<input type="hidden" class="input empty" name="need_pay_total" value="" id='need_pay_total'>
        </form>
    </div>
</div>
<!--批量添加应付业主弹窗-->
<div class="pop_box_g" id="js_pop_add_attendance_pl" style="width:510px; height:465px; display: none;">
    <div class="hd header">
        <div class="title">批量添加应付业主</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="reclaim-mod reclaim-mod2">
        <form action="#" method="post" id='need_pay_add_pl'>
            <table>
				<tr>
					<td class="label">起付日期：</td>
                    <td><input type="text" class="input_text time_bg empty" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" name='need_pay_time' id='need_pay_time_p'></td>
                    <td></td>
					<td class="label">停付日期：</td>
                    <td><input type="text" class="input_text time_bg empty" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" name='stop_time' id='stop_time'></td>
                    <td></td>
				</tr>
				<!--<tr>
                    <td colspan='3'><span style='display:none;' class='text_date_start'>起付日期应该在托管的开始日期之内</span></td>
                    <td colspan='3'><span style='display:none;' class='text_date_end'>停付日期不能超过托管的最后的日期</span></td>
					<td colspan='6'><p style='display:none' class='text_date'>停付时间不可早于起付时间</p></td>
				</tr>-->
				<tr>
					<td width="70" class="label">付款方式：</td>
                    <td width="105">
                        <select class="select empty" style="width:100px;" name="pay_type" id="pay_type">
							<option value="">请选择</option>
                            <option value="1">月付</option>
                            <option value="2">季付</option>
							<option value="3">半年付</option>
							<option value="4">年付</option>
                        </select>
					</td>
                    <td width="60"></td>
					<td width="70"  class="label">应付次数：</td>
                    <td width="105"><font class="f60 f14 pay_nums empty_fee"></font>次</td>
                    <td></td>
				</tr>
				<tr>
					<td class="label">租金：</td>
                    <td><input class="input_text w90 need_fee_pl empty" type="text" id="rental_p" name='rental'></td>
                    <td>元</td>
					<td class="label">水费：</td>
                    <td><input class="input_text w90 need_fee_pl empty" type="text" id="water_fee_p" name='water_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">电费：</td>
                    <td><input class="input_text w90 need_fee_pl empty" type="text" id="ele_fee_p" name='ele_fee'></td>
                    <td>元</td>
					<td  class="label">燃气费：</td>
                    <td><input class="input_text w90 need_fee_pl empty" type="text" id="gas_fee_p" name='gas_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">网费：</td>
                    <td><input class="input_text w90 need_fee_pl empty" type="text" id="int_fee_p" name='int_fee'></td>
                    <td>元</td>
					<td  class="label">电视费：</td>
                    <td><input class="input_text w90 need_fee_pl empty" type="text" id="tv_fee_p" name='tv_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">物业费：</td>
                    <td><input class="input_text w90 need_fee_pl empty" type="text" id="property_fee_p" name='property_fee'></td>
                    <td>元</td>
					<td  class="label">维护费：</td>
                    <td><input class="input_text w90 need_fee_pl empty" type="text" id="preserve_fee_p" name='preserve_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">垃圾费：</td>
                    <td><input class="input_text w90 need_fee_pl empty" type="text" id="garbage_fee_p" name='garbage_fee'></td>
                    <td>元</td>
					<td  class="label">杂费：</td>
                    <td><input class="input_text w90 need_fee_pl empty" type="text" id="other_fee_p" name='other_fee'></td>
                    <td>元</td>
				</tr>
			</table>
			<div class="total clearfix">
				<div class="fl">
					<strong>单次合计：</strong><font class="f60 f14 danci_fee empty_fee"></font>元
				</div>
				<p><strong>应付总计：</strong><font class="f60 f14 totle_fee empty_fee"></font> 元</p>
			</div>
			<script>
				function DayNumOfMonth(Year,Month)//获得首付月份有多少天
				{
					Month--;
					var d = new Date(Year,Month,1);
					d.setDate(d.getDate()+32-d.getDate());
					return (32-d.getDate());
				}
				$(function(){
					$('#stop_time').blur(function(){
						var start_time = $('#need_pay_time_p').val();
						var end_time = $('#stop_time').val();
						if(end_time < start_time){
							$('.text_date').attr('style','display:inline');
							$('.text_date').css('color','red');
						}else{
							$('.text_date').hide();
						}
					});
					/*$('#need_pay_time').focus(function(){
						$('.text_date_start').attr('style','display:none');
					});
					$('#stop_time').focus(function(){
						$('.text_date_end').attr('style','display:none');
					});*/
					//次数由时间跟付款方式一起决定
					$('#stop_time,#pay_type').change(function(){
						var pay_type = $("#pay_type").find("option:selected").val();
						var start_time = $('#need_pay_time_p').val();
						var date1 = new Date(start_time.replace(/-/g, "/"));
						//alert(date1);




						var str1 = (date1.getTime()/1000);//起付时间戳
						var year = date1.getFullYear();
						var month = date1.getMonth() +1;
						var first_month_day = DayNumOfMonth(year,month);//获取首月有多少天

						var end_time = $('#stop_time').val();
						//console.log(start_time);
						//console.log(end_time);
						var date2 = new Date(end_time.replace(/-/g, "/"));  //字符强制装换
						//date2 = parseISO8601(date2);
						var str2 = (date2.getTime()/1000);//停付时间戳
						var day = (str2-str1)/86400 //获取两个日期之间一共有多少天
						//alert(start_time);return false;
						if(first_month_day >= day){//起付跟停付之间有几个月
							month_times = 1;
						}else{
							month_times =  Math.ceil(day/first_month_day);//向上取整
						}
						var collo_start_time = $('#collo_start_time').val();
						var collo_end_time = $('#collo_end_time').val();

						/*if(start_time < collo_start_time){
							$('.text_date_start').attr('style','display:inline');
							$('.text_date_start').css('color','red');
						}
						if(end_time > collo_end_time){//停付日期应不超过托管最后的日期
							$('.text_date_end').attr('style','display:inline');
							$('.text_date_end').css('color','red');
						}*/

						if(pay_type == 1){//月付
							$('.pay_nums').html(month_times);
							$('#pay_times').val(month_times);
						}
						if(pay_type == 2){//季付
							if(month_times < 3){
								$('.pay_nums').html('1');
								$('#pay_times').val('1');
							}else{
								$('.pay_nums').html(Math.ceil(month_times/3));
								$('#pay_times').val(Math.ceil(month_times/3));
							}
						}
						if(pay_type == 3){//半年付
							if(month_times < 6){
								$('.pay_nums').html('1');
								$('#pay_times').val('1');
							}else{
								$('.pay_nums').html(Math.ceil(month_times/6));
								$('#pay_times').val(Math.ceil(month_times/6));
							}
						}
						if(pay_type == 4){//年付
							if(month_times < 12){
								$('.pay_nums').html('1');
								$('#pay_times').val('1');
							}else{
								$('.pay_nums').html(Math.ceil(month_times/12));
								$('#pay_times').val(Math.ceil(month_times/12));
							}
						}
					});
				});
				$(document).ready(function(){
					//统计价格
					$('.need_fee_pl').change(function(){
						var total_fee=0;
						$(".need_fee_pl").each(function(){
							if($(this).val()!=0){
								total_fee +=  parseFloat($(this).val());
							}
						});
						var pay_times = $('#pay_times').val();
						$(".danci_fee").html(total_fee)
						$(".totle_fee").html(total_fee*pay_times);
						$("#need_pay_total_pl").val(total_fee);
					});
				});
			</script>
            <table>
                <tr>
                    <td width="70" class="label">说明：</td>
                    <td><textarea name="remark" id="remark" class="textarea textarea2 empty"></textarea></td>
                </tr>
                <tr>
                	<td colspan="2" class="center">
						<button type="button" id="dialog_share1" class="btn-lv1 btn-left">确定</button>
						<button type="button" class="btn-hui1 JS_Close">取消</button>
					</td>
                </tr>
            </table>
			<input type="hidden" class="input" name="collo_start_time" value="<?php echo date('Y-m-d',$collo_detail['collo_start_time']);?>" id='collo_start_time'>
			<input type="hidden" class="input" name="collo_end_time" value="<?php echo date('Y-m-d',$collo_detail['collo_end_time']);?>" id='collo_end_time'>
			<input type="hidden" class="input" name="c_id" value="<?=$collo_detail['id']?>" id='c_id'>
			<input type="hidden" class="input" name="collocation_id" value="<?=$collo_detail['collocation_id']?>" id='c_id'>
			<input type="hidden" class="input" name="need_pay_total_pl" value="" id='need_pay_total_pl'>
			<input type="hidden" id="pay_times" name='pay_times'>
        </form>
    </div>
</div>
<!--添加实付业主-->
<div class="pop_box_g" id="js_pop_add_attendance_sf" style="width:510px; height:500px; display: none;">
    <div class="hd header">
        <div class="title" id='edit_actual'>添加实付业主</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="reclaim-mod reclaim-mod2">
        <form action="#" method="post" id='add_actual_fee'>
            <table>
				<tr>
					<td width="70" class="label">租金：</td>
                    <td width="105"><input class="input_text w90 actual_fee empty" type="text" id="rental_s" name='rental'></td>
                    <td width="60">元</td>
					<td width="70"  class="label">水费：</td>
                    <td width="105"><input class="input_text w90 actual_fee empty" type="text" id="water_fee_s" name='water_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">电费：</td>
                    <td><input class="input_text w90 actual_fee empty" type="text" id="ele_fee_s" name='ele_fee'></td>
                    <td>元</td>
					<td  class="label">燃气费：</td>
                    <td><input class="input_text w90 actual_fee empty" type="text" id="gas_fee_s" name='gas_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">网费：</td>
                    <td><input class="input_text w90 actual_fee empty" type="text" id="int_fee_s" name='int_fee'></td>
                    <td>元</td>
					<td  class="label">电视费：</td>
                    <td><input class="input_text w90 actual_fee empty" type="text" id="tv_fee_s" name='tv_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">物业费：</td>
                    <td><input class="input_text w90 actual_fee empty" type="text" id="property_fee_s" name='property_fee'></td>
                    <td>元</td>
					<td  class="label">维护费：</td>
                    <td><input class="input_text w90 actual_fee empty" type="text" id="preserve_fee_s" name='preserve_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">垃圾费：</td>
                    <td><input class="input_text w90 actual_fee empty" type="text" id="garbage_fee_s" name='garbage_fee'></td>
                    <td>元</td>
					<td  class="label">杂费：</td>
                    <td><input class="input_text w90 actual_fee empty" type="text" id="other_fee_s" name='other_fee'></td>
                    <td>元</td>
				</tr>
			</table>
			<div class="total clearfix"><div class="fr"><strong>合计：</strong><font class="f60 f14 total_actual_fee empty_fee" id='actual_total'></font> 元</div></div>
			<script>
				$(document).ready(function(){
					$('.actual_fee').change(function(){
						var total_fee=0;
						$(".actual_fee").each(function(){
							if($(this).val()!=0){
								total_fee +=  parseFloat($(this).val());
							}
						});
						 $(".total_actual_fee").html(total_fee);
						$("#actual_pay_total").val(total_fee);
					});
					$('#actual_pay_time,#agency_id').focus(function(){
						$('.text_actual_pay').hide();
					});
				});
			</script>

            <table>
                <tr>
					<td width="70" class="label"><font class="red">*</font>付款人：</td>
                    <td width="210">
						<select class="select w80 empty" name="agency_id" id="agency_id">
								 <?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>" <?php if ($val['id'] == $post_param['agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }
                            }?>
						</select>
                        <select class="select w80 empty" name="broker_id" id="broker_id">
							<!--<?php if (is_full_array($post_config['agencys'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if ($val['broker_id'] == $post_param['broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                             <?php }}?>-->
							 <option value="">请选择</option>
						</select>
					</td>
					<script>
					$("#agency_id").change(function(){
						var agency_id = $('#agency_id').val();
						if(agency_id){
							$.ajax({
								url:"/collocation_contract/broker_list",
								type:"GET",
								dataType:"json",
								data:{
								   agency_id:agency_id
								},
								success:function(data){
									var html = "<option value=''>请选择</option>";
									if(data['result'] == 1){
										for(var i in data['list']){
										html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
										}
										$('#broker_id').html(html);
									}
									$('#broker_id').html(html);
								}
							})
						}else{
							$('#broker_id').html("<option value=''>请选择</option>");
						}
					});
					</script>
					<td width="70" class="label"><font class="red">*</font>实付日期：</td>
                    <td><input type="text" class="input_text time_bg empty" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='actual_pay_time' name='actual_pay_time' value='<?php echo date('Y-m-d',time())?>'></td>
                </tr>
				<tr>
					<td colspan='2'><span style='display:none' class='text_person'>请选择付款人</span></td>
					<td colspan='2'><span style='display:none' class='text_actual_pay'>请选择一个实付日期</span></td>
				</tr>
                <tr>
					<td class="label"><font class="red">*</font>实付方式：</td>
                    <td>
						<select class="select mr10 w90 empty" name="actual_pay_type" id="actual_pay_type">
                            <option value="1">现金</option>
                            <option value="2">支票</option>
							<option value="3">转账</option>
							<option value="4">汇款</option>
                        </select>
					</td>
					<td width="70" class="label">单据号：</td>
                    <td><input type="text" class="input_text w90 empty" name='receipts_num' id='receipts_num'></td>
                </tr>
                <tr>
                    <td class="label">说明：</td>
                    <td colspan="3" height="95">
						<textarea name="remark" id="remark_actual" class="textarea textarea2 empty"></textarea>
						<!--当审核通过，编辑页面的时候，才显示这个按钮，勾上相当于点击确定收款-->
						<p class="check_box" style='display:none'><b class="label"><input type="checkbox" class="js_checkbox input_checkbox" name='fund_status' id='fund_status'> 实付业主已确认</b></p>
					</td>
                </tr>
                <tr>
                	<td colspan="4" class="center">
						<button type="button" id="dialog_share4" class="btn-lv1 btn-left">确定</button>
						<button type="button" id="dialog_share5" class="btn-lv1 btn-left" style='display:none'>保存</button>
						<button type="button" class="btn-hui1 JS_Close">取消</button>
					</td>
                </tr>
            </table>
			<input type="hidden" class="input" name="c_id" value="<?=$collo_detail['id']?>" id='c_id'>
			<input type="hidden" class="input" name="collocation_id" value="<?=$collo_detail['collocation_id']?>" id='c_id'>
			<input type="hidden" class="input" name="actual_pay_id" value="" id='actual_pay_id'>
			<input type="hidden" class="input empty" name="actual_pay_total" value="" id='actual_pay_total'>
        </form>
    </div>
</div>
<!--管家费用添加-->
<div class="pop_box_g" id="js_pop_add_attendance_gj" style="width:510px; height:350px; display: none;margin-top:-150px;">
    <div class="hd header">
        <div class="title" id='edit_steward'>添加管家费用</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="reclaim-mod reclaim-mod2">
        <form action="#" method="post" id='add_steward_expenses'>
			<table>
                <tr>
					<td width="70" class="label"><font class="red">*</font>报销日：</td>
                    <td width="105"><input type="text" value="<?php echo date('Y-m-d',time());?>" class="input_text time_bg" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='reimbursement_time' name='reimbursement_time'></td>
					<td width="60"></td>
					<td width="70" class="label"><font class="red">*</font>项目名称：</td>
                    <td width="105"><input class="input_text w90 empty" type="text" id="project_name" name='project_name'></td>
					<td></td>
                </tr>
				<tr>
					<td colspan='3'><span style='display:none' class='text_baoxiao'>请选择一个报销日期</span></td>
					<td colspan='3'><span style='display:none' class='text_project'>请填写一个项目名称</span></td>
				</tr>
                <tr>
					<td width="70" class="label"><font class="red">*</font>费用总计：</td>
                    <td><input class="input_text w90 empty" type="text" id="total_fee" name='total_fee'></td>
					<td>元</td>
					<td width="70" class="label">业主承担：</td>
                    <td><input class="input_text w90 empty" type="text" id="owner_bear" name='owner_bear'></td>
					<td>元</td>
                </tr>
				<tr><td colspan='6'><span style='display:none' class='text_total_fee'>请填写费用总计</span></td></tr>
                <tr>
					<td width="70" class="label">客户承担：</td>
                    <td><input class="input_text w90 empty" type="text" id="customer_bear" name='customer_bear'></td>
					<td>元</td>
					<td width="70" class="label">公司承担：</td>
                    <td><input class="input_text w90 empty" type="text" id="company_bear" name='company_bear'></td>
					<td>元</td>
                </tr>
                <tr>
					<td width="70" class="label">扣款日：</td>
                    <td width="105"><input type="text" class="input_text time_bg empty" id='withhold_time' name='withhold_time' onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"></td>
					<td></td>
					<td width="70" class="label"><font class="red">*</font>报销部门：</td>
                    <td>
						<select class="select w102 empty" name="agency_id"  id="agency_id_gj">
								 <?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>" <?php if ($val['id'] == $post_param['agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }
                            }?>
						</select>
					</td>
					<td></td>
                </tr>
				<tr><td colspan='3'></td><td colspan='3'><span style='display:none' class='text_branch'>请选择报销部门</span></td></tr>
                <tr>
                    <td class="label">说明：</td>
                    <td colspan="5"><textarea name="remark" id="remark_steward" class="textarea textarea2 empty"></textarea></td>
                </tr>
                <tr>
                	<td colspan="6" class="center">
						<button type="button" id="dialog_share6" class="btn-lv1 btn-left">确定</button>
						<button type="button" id="dialog_share7" class="btn-lv1 btn-left JS_Close" style='display:none'>保存</button>
						<button type="button" class="btn-hui1 JS_Close">取消</button>
					</td>
                </tr>
            </table>
			<input type="hidden" class="input" name="c_id" value="<?=$collo_detail['id']?>" id='c_id'>
			<input type="hidden" class="input" name="collocation_id" value="<?=$collo_detail['collocation_id']?>" id='c_id'>
			<input type="hidden" class="input" name="steward_expenses_id" value="" id='steward_expenses_id'>
        </form>
    </div>
</div>
<!--询问操作确定弹窗-->
<div id="jss_pop_tip" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" id="dialogSaveDiv" style="font-size:14px;"></p>
                     <div class="center">
                    <button type="button" id = 'dialog_share2' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button"  class="btn-hui1 JS_Close">取消</button>
                    </div>
                    <input type ="hidden" name='ci_id' id = 'rowid' value = ''>
                    <input type ="hidden" name='secret_key' id = 'secret_key' value = ''>
                    <input type ="hidden" name='atction_type' id = 'atction_type' value = ''>
                    <input type ="hidden" name='do_type' id = 'do_type' value = ''>
                </div>
            </div>
    </div>
</div>
<!-- 确认通过+提示 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg1">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="#"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">

                <p class="text"><img class="img_msg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                    <span id="dialog_do_itp" class="span_msg"></span>
                </p>
            </div>
        </div>
    </div>
</div>
<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp_'></p>
            </div>
        </div>
    </div>
</div>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
<script>
$(function () {
  function re_width(){
    var h1 = $(window).height();
    var w1 = $(window).width() - 180;
    $(".tab-left, .forms_scroll").height(h1-65);
    $(".forms_scroll").width(w1).show();
  };
  re_width();
  $(window).resize(function(e) {
    re_width();
    $(".sale_message dt").css("width",($(".sale_message").width()-100-36)+"px")
  });


  $(".sale_message dt").css("width",($(".sale_message").width()-100-36)+"px")

});
</script>
<script>

$(function(){
	//应付单次添加
	$('#dialog_share').click(function(){
		if($('#rental').val() || $('#ele_fee').val()  || $('#ele_fee').val() || $('#gas_fee').val()  || $('#int_fee').val() || $('#tv_fee').val() || $('#property_fee').val() || $('#preserve_fee').val() || $('#garbage_fee').val() || $('#other_fee').val()){
			if($('#need_pay_time').val()){
				$('.text_time').hide();
				$.ajax({
					url: "<?php echo MLS_URL;?>/collocation_contract/add_need_pay/1",
					type: "POST",
					dataType: "json",
					data:$("#need_pay_add").serialize(),
					success: function(data) {
						if(data == 'ok')
						{
							$('#js_pop_add_attendance_kq').hide();
							openWin('js_pop_msg1');
							$("#dialog_do_itp").html('添加应付业主成功');
							setTimeout(function(){location.href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/1"},2000);
						}else if(data['errorCode'] == '403'){//无权限
							permission_none();
						}
					}
				});
			}else{
				$('.text_time').attr('style','display:inline');
				$('.text_time').css('color','red');
			}
		}else{
			$('#dialog_do_itp_').html('请填写至少一项应付信息！');
			openWin('js_pop_do_success');
			//$('#js_pop_do_success').attr('style','position: absolute; z-index: 299706; left: 50%; margin-left: -151px; margin-top: -58px; top: 50%; display: block;');
		}
	});
	//应付批量添加
	$('#dialog_share1').click(function(){
		if($('#rental_p').val() || $('#ele_fee_p').val()  || $('#ele_fee_p').val() || $('#gas_fee_p').val()  || $('#int_fee_p').val() || $('#tv_fee_p').val() || $('#property_fee_p').val() || $('#preserve_fee_p').val() || $('#garbage_fee_p').val() || $('#other_fee_p').val())
		{
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/add_need_pay/2",
				type: "POST",
				dataType: "json",
				data:$("#need_pay_add_pl").serialize(),
				success: function(data) {
					if(data == 'ok')
					{
						$('#js_pop_add_attendance_pl').hide();
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('批量添加应付业主成功');
						setTimeout(function(){location.href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/1"},2000);
					}else if(data['errorCode'] == '403'){//无权限
							permission_none();
						}
				}
			});
		}else{
			$('#dialog_do_itp_').html('请填写至少一项应付信息！');
			openWin('js_pop_do_success');
		}
	});
	//应付编辑保存
	$('#dialog_share3').click(function(){
		if($('#rental').val() || $('#ele_fee').val()  || $('#ele_fee').val() || $('#gas_fee').val()  || $('#int_fee').val() || $('#tv_fee').val() || $('#property_fee').val() || $('#preserve_fee').val() || $('#garbage_fee').val() || $('#other_fee').val()){
			if($('#need_pay_time').val()){
				$('.text_time').hide();
				$.ajax({
					url: "<?php echo MLS_URL;?>/collocation_contract/save_need_pay/1",
					type: "POST",
					dataType: "json",
					data:$("#need_pay_add").serialize(),
					success: function(data) {
						if(data == 'ok')
						{
							$('#js_pop_add_attendance_kq').hide();
							openWin('js_pop_msg1');
							$("#dialog_do_itp").html('修改应付业主成功');
							setTimeout(function(){location.href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/1"},2000);
						}else if(data['errorCode'] == '403'){//无权限
							permission_none();
						}
					}
				});
			}else{
				$('.text_time').attr('style','display:inline');
				$('.text_time').css('color','red');
			}
		}else{
			$('#dialog_do_itp_').html('请填写至少一项应付信息！');
			openWin('js_pop_do_success');
		}
	});
	//实付添加
	$('#dialog_share4').click(function(){
		//如果勾选了 在列表页就改变资金状态为  已确认
		/*if($("input[type='checkbox']").is(":checked")){
			$('#fund_status').val('2');
		}*/
		if($('#rental_s').val() || $('#ele_fee_s').val()  || $('#ele_fee_s').val() || $('#gas_fee_s').val()  || $('#int_fee_s').val() || $('#tv_fee_s').val() || $('#property_fee_s').val() || $('#preserve_fee_s').val() || $('#garbage_fee_s').val() || $('#other_fee_s').val()){
			if($('#actual_pay_time').val() && $('#agency_id').val()){
				$('.text_person').hide();
				$('.text_actual_pay').hide();
				$.ajax({
						url: "<?php echo MLS_URL;?>/collocation_contract/add_need_pay/3",
						type: "POST",
						dataType: "json",
						data:$("#add_actual_fee").serialize(),
						success: function(data) {
							if(data == 'ok')
							{
								$('#js_pop_add_attendance_sf').hide();
								openWin('js_pop_msg1');
								$("#dialog_do_itp").html('该实收客户已确认');
								setTimeout(function(){location.href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/2"},2000);
							}else if(data['errorCode'] == '403'){//无权限
								permission_none();
							}
						}
					});
			}else if($('#actual_pay_time').val() == ''){
				$('.text_actual_pay').attr('style','display:inline');
				$('.text_actual_pay').css('color','red');
			}else{
				$('.text_person').attr('style','display:inline');
				$('.text_person').css('color','red');
			}

		}else{
			$('#dialog_do_itp_').html('请填写至少一项应付信息！');
			openWin('js_pop_do_success');
		}
	});
	//实付编辑保存
	$('#dialog_share5').click(function(){
		if($("input[type='checkbox']").is(":checked")){
			$('#fund_status').val('2');
		}
		if($('#rental_s').val() || $('#ele_fee_s').val()  || $('#ele_fee_s').val() || $('#gas_fee_s').val()  || $('#int_fee_s').val() || $('#tv_fee_s').val() || $('#property_fee_s').val() || $('#preserve_fee_s').val() || $('#garbage_fee_s').val() || $('#other_fee_s').val()){
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/save_need_pay/2",
				type: "POST",
				dataType: "json",
				data:$("#add_actual_fee").serialize(),
				success: function(data) {
					if(data == 'ok')
					{
						$('#js_pop_add_attendance_sf').hide();
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('修改实付业主成功');
						setTimeout(function(){location.href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/2"},2000);
					}else if(data['errorCode'] == '403'){//无权限
							permission_none();
					}
				}
			});
		}else{
			$('#dialog_do_itp_').html('请填写至少一项应付信息！');
			openWin('js_pop_do_success');
		}
	});
	//管家费用添加
	$('#dialog_share6').click(function(){
		if($('#reimbursement_time').val() && $('#project_name').val() && $('#total_fee').val() && $('#agency_id_gj').val())
		{
			$('.text_baoxiao,.text_project,.text_total_fee,.text_branch').hide();
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/add_need_pay/4",
				type: "POST",
				dataType: "json",
				data:$("#add_steward_expenses").serialize(),
				success: function(data) {
					if(data == 'ok')
					{
						$('#js_pop_add_attendance_gj').hide();
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('管家费用添加成功');
						setTimeout(function(){location.href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/3"},2000);
					}else if(data['errorCode'] == '403'){//无权限
							permission_none();
					}
				}
			});
		}else if($('#reimbursement_time').val() == ''){

			$('.text_project,.text_total_fee,.text_branch').hide();
			$('.text_baoxiao').attr('style','display:inline');
			$('.text_baoxiao').css('color','red');
		}else if($('#project_name').val() == ''){
			$('.text_baoxiao,.text_total_fee,.text_branch').hide();
			$('.text_project').attr('style','display:inline');
			$('.text_project').css('color','red');
		}else if($('#total_fee').val() == ''){
			$('.text_baoxiao,.text_project,.text_branch').hide();
			$('.text_total_fee').attr('style','display:inline');
			$('.text_total_fee').css('color','red');
		}else if($('#agency_id_').val() == ''){
			$('.text_baoxiao,.text_project,.text_total_fee').hide();
			$('.text_branch').attr('style','display:inline');
			$('.text_branch').css('color','red');
		}
	});
	//管家编辑保存
	$('#dialog_share7').click(function(){
		$.ajax({
			url: "<?php echo MLS_URL;?>/collocation_contract/save_need_pay/3",
			type: "POST",
			dataType: "json",
			data:$("#add_steward_expenses").serialize(),
			success: function(data) {
				if(data == 'ok')
				{
					openWin('js_pop_msg1');
					$("#dialog_do_itp").html('管家费用修改成功');
					setTimeout(function(){location.href="/collocation_contract/contract_detail/<?=$collo_detail['id']?>/3"},2000);
				}else if(data['errorCode'] == '403'){//无权限
					permission_none();
				}
			}
		});
	});
});
//将时间戳转化的方法
function getLocalTime(nS) {
   return new Date(parseInt(nS) * 1000).toLocaleString().replace(/\//g, "-");
}
//应付，实付，管家 修改
function need_pay_edit(id,tab){

	$.ajax({
		url: "<?php echo MLS_URL;?>/collocation_contract/need_pay_edit/",
		type: "GET",
		dataType: "json",
		data: {
			id:id,
			tab:tab
		},
		success: function(data) {
			if(data['result'] == 1){
				if(tab == 1 || tab == 2){
					$("input[name='rental']").val(data['arr']['rental']);
					$("input[name='water_fee']").val(data['arr']['water_fee']);
					$("input[name='ele_fee']").val(data['arr']['ele_fee']);
					$("input[name='gas_fee']").val(data['arr']['gas_fee']);
					$("input[name='int_fee']").val(data['arr']['int_fee']);
					$("input[name='tv_fee']").val(data['arr']['tv_fee']);
					$("input[name='property_fee']").val(data['arr']['property_fee']);
					$("input[name='garbage_fee']").val(data['arr']['garbage_fee']);
					$("input[name='preserve_fee']").val(data['arr']['preserve_fee']);
					$("input[name='other_fee']").val(data['arr']['other_fee']);

					if(tab == 1){//应付
						$("#need_total").text(data['arr']['total_fee']);
						$("#need_pay_total").val(data['arr']['total_fee']);
						$("#remark").val(data['arr']['remark']);
						$("input[name='need_pay_time']").val(data['arr']['need_pay_time']);
						$('#need_pay_id').val(id);
						$("#dialog_share").attr('style','display:none');
						$("#dialog_share3").attr('style','display:inline');
						openWin('js_pop_add_attendance_kq');
						$('#only_add').text('编辑应付业主');
					}else if(tab == 2){//实付
						$("#actual_total").text(data['arr']['total_fee']);
						$("#actual_pay_total").val(data['arr']['total_fee']);
						$("#remark_actual").val(data['arr']['remark']);
						$("#agency_id").val(data['arr']['agency_id']);

			var html = "<option value=''>请选择</option>";
		    for(var i in data['broker_list']){
			html +='<option value="'+data['broker_list'][i]['broker_id']+'">'+data['broker_list'][i]['truename']+'</option>';
		    }
		    $("#broker_id").html(html);

						$("select[name='broker_id']").val(data['arr']['broker_id']);
						$("input[name='actual_pay_time']").val(data['arr']['actual_pay_time']);
						$("#actual_pay_type").val(data['arr']['actual_pay_type']);
						$("input[name='receipts_num']").val(data['arr']['receipts_num']);
						$('#actual_pay_id').val(id);
						if(data['arr']['status'] == 2){//审核通过后，确认付款按钮展示
							$(".check_box").attr('style','display:inline');
						}
						//根据fund_status 确定checkbox是否勾选
						if(data['arr']['fund_status'] == 2){
							$('.label').addClass('labelOn');
						}
						$("#dialog_share4").attr('style','display:none');
						$("#dialog_share5").attr('style','display:inline');
						openWin('js_pop_add_attendance_sf');
						$('#edit_actual').text('编辑实付业主');
					}
				}else if(tab == 3){//管家
					$("input[name='reimbursement_time']").val(data['arr']['reimbursement_time']);
					$("input[name='project_name']").val(data['arr']['project_name']);
					$("input[name='total_fee']").val(data['arr']['total_fee']);
					$("input[name='owner_bear']").val(data['arr']['owner_bear']);
					$("input[name='customer_bear']").val(data['arr']['customer_bear']);
					$("input[name='company_bear']").val(data['arr']['company_bear']);
					$("input[name='withhold_time']").val(data['arr']['withhold_time']);
					$("#remark_steward").val(data['arr']['remark']);
					$("#agency_id_gj").val(data['arr']['agency_id']);
					$('#steward_expenses_id').val(id);
					$("#dialog_share6").attr('style','display:none');
					$("#dialog_share7").attr('style','display:inline');
					openWin('js_pop_add_attendance_gj');
					$('#edit_steward').text('修改管家费用');
				}
			}
		}
	});
}
//删除
	function del_need_pay(id,tab,c_id){
		if(tab == 1){
			$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 应付业主信息删除后不可恢复，是否确认删除？');
		}else if(tab == 2){
			$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 实付业主信息删除后不可恢复，是否确认删除？');
		}else if(tab == 3){
			$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 管家费用删除后不可恢复，是否确认删除？');
		}else if(tab == 4){
			$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 合同删除后不可恢复，是否确认删除？');
		}

		openWin('jss_pop_tip');
		$("#dialog_share2").click(function(){
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/del_need_pay/",
				type: "GET",
				dataType: "json",
				data: {
					id:id,
					c_id:c_id,
					tab:tab
				},
				success: function(data) {
					if(data == 'ok')
					{
						openWin('js_pop_msg1');
						if(tab == 1){
							$("#dialog_do_itp").html('应付业主信息已删除');
						}else if (tab == 2){
							$("#dialog_do_itp").html('实付业主信息已删除');
						}else if(tab == 3){
							$("#dialog_do_itp").html('管家费用已删除');
						}else if(tab == 4){
							$("#dialog_do_itp").html('合同已删除');
						}
						location.reload();
					}else if(data['errorCode'] == '403'){//无权限
							permission_none();
					}
				}
			});
		});
	}
//核销(核销是在审核通过的情况下才能进行的处理)
	function cancel_verification(id,tab){
		$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 是否确定当前应付款项已核销');
		openWin('jss_pop_tip');
		$("#dialog_share2").click(function(){
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/cancel_verification/",
				type: "GET",
				dataType: "json",
				data: {
					id:id,
					tab:tab
				},
				success: function(data) {
					if(data == 'ok')
					{
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('应付业主信息已核销');
						location.reload();
					}
				}
			});
		});
	}
//实付业主：在审核通过情况下，可以点击修改确认付款
	function modify_fund_status(id,c_id){
		$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 是否确定已付款');
		openWin('jss_pop_tip');
		$("#dialog_share2").click(function(){
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/cancel_verification/",
				type: "GET",
				dataType: "json",
				data: {
					id:id,
					c_id:c_id,
					tab:'2'
				},
				success: function(data) {
					if(data == 'ok')
					{
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('已确认');
						location.reload();
					}else if(data['errorCode'] == '403'){//无权限
							permission_none();
						}
				}
			});
		});
	}
//出租合同合同状态生效下作废合同
	function rent_contract_cancel(id,c_id){
		$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 合同作废后不可恢复，是否确认作废？');
		openWin('jss_pop_tip');
		$("#dialog_share2").click(function(){
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/cancel_verification/",
				type: "GET",
				dataType: "json",
				data: {
					id:id,
					c_id:c_id,
					tab:'4'
				},
				success: function(data) {
					if(data == 'ok')
					{
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('合同已作废');
						location.reload();
					}else if(data['errorCode'] == '403'){//无权限
							permission_none();
						}
				}
			});
		});
	}
</script>
