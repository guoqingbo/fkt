
<div class="contract-wrap clearfix">
	<div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll h90">
		<div class="contract_top_main contract_table_box mb10">
			<div class="pr20">
				<a class="btn-lv1 fr" href="javascript:void(0)" onclick="history.go(-1);return false;">返回</a>
				<a class="btn-lv1 fr" href="/collocation_contract/rent_modify/<?=$collo_rent_detail['id']?>">编辑</a>
			</div>
			<h3>托管出租合同信息</h3>
			<div class="sale_message_commission" style="border-bottom:none;margin-bottom:0;" >

				<div class="t_item clearfix contract_mess" style="width:98%;">
				    <p class="item w235"><span class="tex zws_w88 input_add_F zws_align_r">托管合同编号：</span><font class="c227ac6"><?=$collo_rent_detail['collocation_id']?></font></p>
				    <p class="item w260"><span class="tex zws_w88 input_add_F zws_align_r">出租合同编号：</span><font class="c227ac6"><?=$collo_rent_detail['collo_rent_id']?></font></p>
				    <p class="item w267"><span class="tex zws_w88 input_add_F zws_align_r">房源编号：</span><font class="c227ac6"><?=$collo_rent_detail['house_id']?></font></p>
				    <p class="item w235"><span class="tex zws_w88 input_add_F zws_align_r">楼盘名称：</span><?=$collo_rent_detail['block_name']?></p>
				    <p class="item wauto" style="width:149px;padding-right:18px;"><span class="tex zws_w88 input_add_F zws_align_r">房源面积：</span><b style="font-weight:bold;"><?=$collo_rent_detail['houses_area']?></b>m²</p>
				</div>
				<div class="t_item clearfix contract_mess"  style="width:98%;">
				    <p class="item w500"><span class="tex zws_w88 input_add_F zws_align_r">房源地址：</span><font style="font-size:12px;"><?=$collo_rent_detail['houses_address']?></font></p>
				    <p class="item w267"><span class="tex zws_w88 input_add_F zws_align_r">物业类型：</span>
				    		<?php
									if($collo_rent_detail['type'] == 1){
										echo '住宅';
									}elseif($collo_rent_detail['type'] == 2){
										echo '公寓';
									}elseif($collo_rent_detail['type'] == 3){
										echo '别墅';
									}elseif($collo_rent_detail['type'] == 4){
										echo '写字楼';
									}elseif($collo_rent_detail['type'] == 5){
										echo '厂房';
									}elseif($collo_rent_detail['type'] == 6){
										echo '库房';
									}
								?></p>
				    <p class="item float:left;"><span class="tex zws_w88 input_add_F zws_align_r">签约日期：</span><?php echo date('Y-m-d',$collo_rent_detail['signing_time']);?></p>
				</div>

				<div class="t_item clearfix contract_mess"   style="width:98%;">
				    <p class="item"><span class="tex zws_w88 input_add_F zws_align_r">出租日期：</span><?php echo date('Y-m-d',$collo_rent_detail['rent_start_time']);?>至<?php echo date('Y-m-d',$collo_rent_detail['rent_end_time']);?>　共<strong class="f00"><?=$collo_rent_detail['rent_total_month']?></strong>个月</p>

				</div>
			</div>
			<div class="sale_message_commission " style="margin-top:0;" >

				<dl class="sale_message" style="padding-bottom: 15px;">
		            <dt>

		                <div>
		                    <p><span class="input_add_F" style="width:80px;text-align:right;">客户姓名：</span><b><?=$collo_rent_detail['customer_name']?></b></p>
		                    <p><span class="input_add_F" style="width:80px;text-align:right;">联系方式：</span><b><?=$collo_rent_detail['customer_tel']?></b></p>
		                    <p><span class="input_add_F" style="width:80px;text-align:right;" >身份证号：</span><b><?=$collo_rent_detail['customer_idcard']?></b></p>
		                </div>
						<div>
		                    <p><span class="input_add_F" style="width:80px;text-align:right;">签约门店：</span><b><?=$collo_rent_detail['agency_name']?></b></p>
		                    <p><span class="input_add_F" style="width:80px;text-align:right;">签 约 人：</span><b><?=$collo_rent_detail['broker_name']?></b></p>
		                    <p><span class="input_add_F" style="width:80px;text-align:right;">联系方式：</span><b><?=$collo_rent_detail['broker_tel']?></b></p>
		                    <p><span class="input_add_F" style="width:80px;text-align:right;">付款渠道：</span><b><?=$collo_rent_detail['pay_ditch']?></b></p>
		                </div>
		            </dt>
		        </dl>
				<dl class="sale_message">
		            <dt>

		                <div>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">每月租金：</span><b><?=strip_end_0($collo_rent_detail['rental'])?></b></p>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">付款方式：</span><b><?php
									if($collo_rent_detail['pay_type'] == 1){
										echo '月付';
									}elseif($collo_rent_detail['pay_type'] == 2){
										echo '季付';
									}elseif($collo_rent_detail['pay_type'] == 3){
										echo '半年付';
									}elseif($collo_rent_detail['pay_type'] == 4){
										echo '年付';
									}elseif($collo_rent_detail['pay_type'] == 5){
										echo '其他';
									}
								?></b></p>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;" >租金总额：</span><b><?=strip_end_0($collo_rent_detail['rental_total'])?></b></p>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;" >押金金额：</span><b><?=strip_end_0($collo_rent_detail['desposit'])?></b></p>
		                </div>
						<div>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">违约金额：</span><b><?=strip_end_0($collo_rent_detail['penal_sum'])?>元</b></p>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">税费承担：</span><b><?php
									if($collo_rent_detail['tax_type'] == 1){
										echo '业主';
									}elseif($collo_rent_detail['tax_type'] == 2){
										echo '客户';
									}elseif($collo_rent_detail['tax_type'] == 3){
										echo '公司';
									}
								?></b></p>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">物业费用：</span><b><?=strip_end_0($collo_rent_detail['property_fee'])?>元/月</b></p>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">物管承担：</span><b><?php
									if($collo_rent_detail['property_manage_assume'] == 1){
										echo '业主';
									}elseif($collo_rent_detail['property_manage_assume'] == 2){
										echo '客户';
									}elseif($collo_rent_detail['property_manage_assume'] == 3){
										echo '公司';
									}
								?></b></p>
		                </div>
		                <div>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">中介佣金：</span><b><?=strip_end_0($collo_rent_detail['agency_commission'])?></b></p>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">免租时间：</span><b><?=$collo_rent_detail['rent_free_time']?>天</b></p>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">房源维护：</span><b><?=$collo_rent_detail['houses_preserve_agency_name']?>&nbsp;&nbsp;<?=$collo_rent_detail['houses_preserve_broker_name']?>&nbsp;&nbsp;<?=intval($collo_rent_detail['houses_preserve_money'])?>元</b></p>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">客源维护：</span><b><?=$collo_rent_detail['customer_preserve_agency_name']?>&nbsp;&nbsp;<?=$collo_rent_detail['customer_preserve_broker_name']?>&nbsp;&nbsp;<?=intval($collo_rent_detail['customer_preserve_money'])?>元</b></p>
		                </div>
		                <div>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">退房经纪：</span><b><?=$collo_rent_detail['out_broker_agency_name']?>&nbsp;&nbsp;<?=$collo_rent_detail['out_broker_broker_name']?></b></p>
		                    <p class="zws_news"><span class="input_add_F" style="width:80px;text-align:right;">终止协议号：</span><b><?=$collo_rent_detail['stop_agreement_num']?></b></p>

		                </div>


		            </dt>
		        </dl>

				<!-- <table class="table02">
					<tbody>
						<tr>
							<td><span>客户姓名：</span><?=$collo_rent_detail['customer_name']?></td>
							<td><span>联系方式：</span><?=$collo_rent_detail['customer_tel']?></td>
							<td colspan="2"><span>身份证号：</span><?=$collo_rent_detail['customer_idcard']?></td>
						</tr>
						<tr>
							<td><span>签约门店：</span><?=$collo_rent_detail['agency_name']?></td>
							<td><span>签 约 人：</span><?=$collo_rent_detail['broker_name']?></td>
							<td><span>联系方式：</span><?=$collo_rent_detail['broker_tel']?></td>
							<td><span>付款渠道：</span><?=$collo_rent_detail['pay_ditch']?></td>
						</tr>
					</tbody>
				</table> -->
			</div>
			<div class="t_list">
				<!-- <table class="table02">
					<tbody>
						<tr>
							<td><span>每月租金：</span><?=$collo_rent_detail['rental']?></td>
							<td>
								<span>付款方式：</span>
								<?php
									if($collo_rent_detail['pay_type'] == 1){
										echo '月付';
									}elseif($collo_rent_detail['pay_type'] == 2){
										echo '季付';
									}elseif($collo_rent_detail['pay_type'] == 3){
										echo '半年付';
									}elseif($collo_rent_detail['pay_type'] == 4){
										echo '年付';
									}elseif($collo_rent_detail['pay_type'] == 5){
										echo '其他';
									}
								?>
							</td>
							<td><span>租金总额：</span><?=$collo_rent_detail['rental_total']?>元</td>
							<td><span>押金金额：</span><?=$collo_rent_detail['desposit']?>元</td>
						</tr>
						<tr>
							<td><span>违约金额：</span><?=$collo_rent_detail['penal_sum']?>元</td>
							<td>
								<span>税费承担：</span>
								<?php
									if($collo_rent_detail['tax_type'] == 1){
										echo '业主';
									}elseif($collo_rent_detail['tax_type'] == 2){
										echo '客户';
									}elseif($collo_rent_detail['tax_type'] == 3){
										echo '公司';
									}
								?>
							</td>
							<td><span>物业费用：</span><?=$collo_rent_detail['property_fee']?>元/月</td>
							<td>
								<span>物管承担：</span>
								<?php
									if($collo_rent_detail['property_manage_assume'] == 1){
										echo '业主';
									}elseif($collo_rent_detail['property_manage_assume'] == 2){
										echo '客户';
									}elseif($collo_rent_detail['property_manage_assume'] == 3){
										echo '公司';
									}
								?>
							</td>
						</tr>
						<tr>
							<td><span>中介佣金：</span><?=$collo_rent_detail['agency_commission']?></td>
							<td><span>免租时间：</span><?=$collo_rent_detail['rent_free_time']?>天</td>
							<td><span>房源维护：</span><?=$collo_rent_detail['houses_preserve_agency_name']?>&nbsp;&nbsp;<?=$collo_rent_detail['houses_preserve_broker_name']?>&nbsp;&nbsp;<?=intval($collo_rent_detail['houses_preserve_money'])?>元</td>
							<td><span>客源维护：</span><?=$collo_rent_detail['customer_preserve_agency_name']?>&nbsp;&nbsp;<?=$collo_rent_detail['customer_preserve_broker_name']?>&nbsp;&nbsp;<?=intval($collo_rent_detail['customer_preserve_money'])?>元</td>
						</tr>
						<tr>
							<td><span>退房经纪：</span><?=$collo_rent_detail['out_broker_agency_name']?>&nbsp;&nbsp;<?=$collo_rent_detail['out_broker_broker_name']?></td>
							<td colspan="3"><span>终止协议号：</span><?=$collo_rent_detail['stop_agreement_num']?></td>
						</tr>
						<tr>
							<td colspan="4"><span>合同备注：</span><?=$collo_rent_detail['remark']?></td>
						</tr>
						<tr>
							<td colspan="4"><span>审核意见：</span><font class="c680"><?=$collo_rent_detail['audit_view']?></font></td>
						</tr>
					</tbody>
				</table> -->
			</div>
		</div>
		<div id="js_search_box" class="shop_tab_title  scr_clear m0">
		<?php if($tag == 1){?>
			<?php if (isset($auth['add']['auth']) && $auth['add']['auth']) { ?>
				<a href="#" class="btn-lv fr ml10" onclick="$('#js_pop_add_attendance_plys').find('.empty').val('');openWin('js_pop_add_attendance_plys');"><span>+ 批量添加应收客户</span></a>
				<a href="#" class="btn-lv fr" onclick="$('#js_pop_add_attendance_dcys').find('.empty').val('');openWin('js_pop_add_attendance_dcys');"><span>+ 添加应收客户</span></a>
			<?php }else{?>
				<a href="#" class="btn-lv fr ml10" onclick="permission_none();"><span>+ 批量添加应收客户</span></a>
				<a href="#" class="btn-lv fr" onclick="permission_none();"><span>+ 添加应收客户</span></a>
			<?php }?>
		<?php }elseif($tag == 2 && !empty($need_date)){?>
			<?php if (isset($auth['add']['auth']) && $auth['add']['auth']) { ?>
				<a href="#" class="btn-lv fr" onclick="$('#js_pop_add_attendance_ss').find('.empty').val('');openWin('js_pop_add_attendance_ss');"><span>+ 添加实收客户</span></a>
			<?php }else{?>
				<a href="#" class="btn-lv fr" onclick="permission_none();"><span>+ 添加实收客户</span></a>
			<?php }?>
		<?php }?>
		   <a href="/collocation_contract/rent_contract_detail/<?=$collo_rent_detail['id']?>/1" class="link <?php if($tag == 1){?> link_on <?php }?>">应收客户<span class="iconfont hide"></span></a>
			<a href="/collocation_contract/rent_contract_detail/<?=$collo_rent_detail['id']?>/2" class="link <?php if($tag == 2){?> link_on <?php }?>">实收客户<span class="iconfont hide"></span></a>
			<a href="/collocation_contract/rent_contract_detail/<?=$collo_rent_detail['id']?>/3" class="link <?php if($tag == 3){?> link_on <?php }?>">合同跟进<span class="iconfont hide"></span></a>
		   <span class="fl">单位：元</span>
		</div>
		<div class="table_all m0">
			<form name="search_form" id="search_form" method="post" action="">
			<?php if($tag == 1 || $tag == 2){?>
				<div class="title shop_title">
					<table class="table">
						<tbody>
							<tr>
								<td class="c9">日期</td>
								<td class="c5">租金</td>
								<td class="c4">水费</td>
								<td class="c4">电费</td>
								<td class="c4">燃气费</td>
								<td class="c4">网费</td>
								<td class="c4">电视费</td>
								<td class="c5">物业费</td>
								<td class="c5">维护费</td>
								<td class="c5">垃圾费</td>
								<td class="c6">杂费</td>
								<td class="c6">合计</td>
								<?php if($tag == 2){?>
								<td class="c8">单据号</td>
								<?php }?>
								<td class="c6">说明</td>
								<td class="c10">录入门店</td>
								<td class="c5">录入人</td>
								<td class="c6">状态</td>
								<td>操作</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="inner shop_inner">
					<table class="table">
					<?php if($tag == 1){?>
						<tbody>
						<?php
							if($need_receive){
								foreach($need_receive as $val){
						?>
							<tr>
								<td class="c9"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?php echo date('Y-m-d',$val['need_receive_time']);?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['rental'])?></div></td>
								<td class="c4"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['water_fee'])?></div></td>
								<td class="c4"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['ele_fee'])?></div></td>
								<td class="c4"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['gas_fee'])?></div></td>
								<td class="c4"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['int_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['tv_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['property_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['preserve_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['garbage_fee'])?></div></td>
								<td class="c6"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['other_fee'])?></div></td>
								<td class="c6"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=strip_end_0($val['total_fee'])?></div></td>
								<td class="c6"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=$val['remark']?></div></td>
								<td class="c10"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=$brokerinfo['agency_name']?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',1)"><div class="info"><?=$brokerinfo['truename']?></div></td>
								<td class="c6"  onclick="need_receive_edit('<?=$val['id']?>',1)">
									<div class="info c999">
										<?php
											if($val['status'] == 1){
												echo '待审核';
											}elseif($val['status'] == 2){
												echo '<font color="green">审核通过</font>';
											}else{
												echo '<font color="red">审核未通过</font>';
										}?>
									</div>
								</td>
								<td>
								<?php if($val['status'] != 2){?>
									<?php if (isset($auth['edit']['auth']) && $auth['edit']['auth']) { ?>
										<a href="javascript:void(0)" onclick="need_receive_edit('<?=$val['id']?>',1)">修改</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">修改</a>
									<?php }?>
										<span style="margin:0 5px;color:#b2b2b2;">|</span>
									<?php if (isset($auth['delete']['auth']) && $auth['delete']['auth']) { ?>
										<a href="javascript:void(0)" onclick="del_need_receive('<?=$val['id']?>',1,'<?=$val['r_id']?>');">删除</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">删除</a>
									<?php }?>
								<?php }else{?>
									<span style='grey'>修改<span style="margin:0 5px;color:#b2b2b2;">|</span>删除</span>
								<?php }?>
								</td>
							</tr>
						<?php }}else{?>
							<tr><td><span class="no-data-tip">您还未添加应收客户！</span></td></tr>
						<?php }?>
						</tbody>
					<?php }else{?>
						<tbody>
						<?php
							if($actual_receive){
								foreach($actual_receive as $val){
						?>
							<tr>
								<td class="c9"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?php echo date('Y-m-d',$val['actual_receive_time']);?></div></td>
								<td class="c4"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['rental'])?></div></td>
								<td class="c4"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['water_fee'])?></div></td>
								<td class="c4"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['ele_fee'])?></div></td>
								<td class="c4"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['gas_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['int_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['tv_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['property_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['preserve_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['garbage_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['other_fee'])?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=strip_end_0($val['total_fee'])?></div></td>
								<td class="c8"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=$val['receipts_num']?></div></td>
								<td class="c6"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=$val['remark']?></div></td>
								<td class="c10"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=$brokerinfo['agency_name']?></div></td>
								<td class="c5"  onclick="need_receive_edit('<?=$val['id']?>',2)"><div class="info"><?=$brokerinfo['truename']?></div></td>
								<td class="c6"  onclick="need_receive_edit('<?=$val['id']?>',2)">
									<div class="info c999">
										<?php
											if($val['status'] == 1){
												echo '待审核';
											}elseif($val['status'] == 2){
												echo '<font color="green">审核通过</font>';
											}else{
												echo '<font color="red">审核未通过</font>';
										}?>
									</div>
								</td>
								<td>
								<?php if($val['status'] != 2 && $val['fund_status'] == 1){?>
									<?php if (isset($auth['edit']['auth']) && $auth['edit']['auth']) { ?>
										<a href="javascript:void(0)" onclick="need_receive_edit('<?=$val['id']?>',2)">修改</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">修改</a>
									<?php }?>
										<span style="margin:0 5px;color:#b2b2b2;">|</span>
									<?php if (isset($auth['delete']['auth']) && $auth['delete']['auth']) { ?>
										<a href="javascript:void(0)" onclick="del_need_receive('<?=$val['id']?>',2,'<?=$val['r_id']?>');">删除</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">删除</a>
									<?php }?>
								<?php }elseif($val['status'] == 2 && $val['fund_status'] == 1){?>
									<?php if (isset($auth['sure']['auth']) && $auth['sure']['auth']) { ?>
										<a href='#' onclick="modify_fund_status('<?=$val['id']?>','<?=$val['r_id']?>')"><font color='blue'>确认付款</font></a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">确认付款</a>
									<?php }?>
								<?php }else{?><!--审核通过，确认收款修改为2-->
									<a href='#'><font color='blue'>已确认</font></a>
								<?php }?>
								</td>
							</tr>
						<?php }}else{?>
							<tr><td><span class="no-data-tip">您还未添加实收客户！</span></td></tr>
						<?php }?>
						</tbody>
					<?php }?>
					</table>
				</div>
			<?php }elseif($tag == 3){?>
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
				<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
					<?php if($tag == 1 || $tag == 2){?>
					<p class="fl">
						应收总计：<strong class="ff9d11"><?=strip_end_0($need_total_fee);?></strong>元　　
						实收总计：<strong class="ff9d11"><?=strip_end_0($actual_total_fee);?></strong>元　　
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
<!--单次添加,编辑应收客户弹窗-->
<div class="pop_box_g" id="js_pop_add_attendance_dcys" style="width:510px; height:430px; display: none;">
    <div class="hd header">
        <div class="title" id='only_add'>添加应收客户</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="reclaim-mod reclaim-mod2">
        <form action="#" method="post" id='need_receive_add'>
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
			<div class="total clearfix"><div class="fr"><strong>合计：</strong><font class="f60 f14 totle_fee" id='need_total'></font> 元</div></div>
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
						$("#need_receive_total").val(total_fee);
					});
					$('#need_receive_time').focus(function(){
						$('.text_time').hide();
					});
				});
			</script>
            <table>
                <tr>
					<td width="70" class="label"><font class="red">*</font>应收日期：</td>
                    <td><input type="text" class="input_text time_bg empty" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" name="need_receive_time" id='need_receive_time' value='<?php echo date('Y-m-d',time())?>'><div class="errorBox"></div></td>
					<td><span style='display:none' class='text_time'>请选择一个应收日期</span></td>
                </tr>
                <tr>
                    <td class="label">说明：</td>
                    <td colspan="2"><textarea name="remark" id="remark" class="textarea textarea2 empty"></textarea></td>
                </tr>
                <tr>
                	<td colspan="3" class="center">
						<button type="button" id="dialog_share" class="btn-lv1 btn-left">确定</button>
						<button type="button" id="dialog_share3" class="btn-lv1 btn-left JS_Close" style='display:none'>保存</button>
						<button type="button" class="btn-hui1 JS_Close">取消</button>
					</td>
                </tr>
            </table>
			<input type="hidden" class="input" name="r_id" value="<?=$collo_rent_detail['id']?>" id='r_id'>
			<input type="hidden" class="input" name="collocation_id" value="<?=$collo_rent_detail['collocation_id']?>" id='collocation_id'>
			<input type="hidden" class="input" name="collo_rent_id" value="<?=$collo_rent_detail['collo_rent_id']?>" id='collo_rent_id'>
			<input type="hidden" class="input" name="need_receive_id" value="" id='need_receive_id'>
			<input type="hidden" class="input empty" name="need_receive_total" value="" id='need_receive_total'>
        </form>
    </div>
</div>
<!--批量添加应收客户弹窗-->
<div class="pop_box_g" id="js_pop_add_attendance_plys" style="width:510px; height:465px; display: none;">
    <div class="hd header">
        <div class="title">批量添加应收客户</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="reclaim-mod reclaim-mod2">
        <form action="#" method="post" id='need_receive_add_pl'>
            <table>
				<tr>
					<td class="label">起付日期：</td>
                    <td><input type="text" class="input_text time_bg empty" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" name='need_receive_time' id='need_receive_time_p'></td>
                    <td></td>
					<td class="label">停付日期：</td>
                    <td><input type="text" class="input_text time_bg empty" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" name='stop_time' id='stop_time'></td>
                    <td></td>
				</tr>
				<!--<tr>
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
                    <td width="105"><font class="f60 f14 pay_nums"></font>次</td>
                    <td></td>
				</tr>

				<tr>
					<td class="label">租金：</td>
                    <td><input class="input_text w90 receive_fee_pl empty" type="text" id="rental_p" name='rental'></td>
                    <td>元</td>
					<td class="label">水费：</td>
                    <td><input class="input_text w90 receive_fee_pl empty" type="text" id="water_fee_p" name='water_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">电费：</td>
                    <td><input class="input_text w90 receive_fee_pl empty" type="text" id="ele_fee_p" name='ele_fee'></td>
                    <td>元</td>
					<td  class="label">燃气费：</td>
                    <td><input class="input_text w90 receive_fee_pl empty" type="text" id="gas_fee_p" name='gas_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">网费：</td>
                    <td><input class="input_text w90 receive_fee_pl empty" type="text" id="int_fee_p" name='int_fee'></td>
                    <td>元</td>
					<td  class="label">电视费：</td>
                    <td><input class="input_text w90 receive_fee_pl empty" type="text" id="tv_fee_p" name='tv_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">物业费：</td>
                    <td><input class="input_text w90 receive_fee_pl empty" type="text" id="property_fee_p" name='property_fee'></td>
                    <td>元</td>
					<td  class="label">维护费：</td>
                    <td><input class="input_text w90 receive_fee_pl empty" type="text" id="preserve_fee_p" name='preserve_fee'></td>
                    <td>元</td>
				</tr>
				<tr>
					<td class="label">垃圾费：</td>
                    <td><input class="input_text w90 receive_fee_pl empty" type="text" id="garbage_fee_p" name='garbage_fee'></td>
                    <td>元</td>
					<td  class="label">杂费：</td>
                    <td><input class="input_text w90 receive_fee_pl empty" type="text" id="other_fee_p" name='other_fee'></td>
                    <td>元</td>
				</tr>
			</table>
			<div class="total clearfix">
				<div class="fl">
					<strong>单次合计：</strong><font class="f60 f14 danci_fee"></font>元
				</div>
				<p><strong>应收总计：</strong><font class="f60 f14 totle_fee"></font> 元</p>
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
						var start_time = $('#need_receive_time_p').val();
						var end_time = $('#stop_time').val();
						//alert(start_time);
						if(end_time < start_time){
							$('.text_date').attr('style','display:inline');
							$('.text_date').css('color','red');
						}else{
							$('.text_date').hide();
						}
					});
					/*$('#need_receive_time').focus(function(){
						$('.text_date_start').attr('style','display:none');
					});
					$('#stop_time').focus(function(){
						$('.text_date_end').attr('style','display:none');
					});*/
					//次数由时间跟付款方式一起决定
					$('#pay_type').change(function(){
						var pay_type = $("#pay_type").find("option:selected").val();
						var start_time = $('#need_receive_time_p').val();
						var date1 = new Date(start_time.replace(/-/g, "/"));
						var str1 = (date1.getTime()/1000);//起付时间戳
						var year = date1.getFullYear();
						var month = date1.getMonth() +1;
						var first_month_day = DayNumOfMonth(year,month);//获取首月有多少天

						var end_time = $('#stop_time').val();
						var date2 = new Date(end_time.replace(/-/g, "/"));
						var str2 = (date2.getTime()/1000);//停付时间戳
						var day = (str2-str1)/86400 //获取两个日期之间一共有多少天

						if(first_month_day >= day){//起付跟停付之间有几个月
							month_times = 1;
						}else{
							month_times =  Math.ceil(day/first_month_day);//向上取整
						}
						var rent_start_time = $('#rent_start_time').val();
						var rent_end_time = $('#rent_end_time').val();
						if(start_time < rent_start_time){
							$('.text_date_start').attr('style','display:inline');
							$('.text_date_start').css('color','red');
						}
						if(end_time > rent_end_time){//停付日期应不超过托管最后的日期
							$('.text_date_end').attr('style','display:inline');
							$('.text_date_end').css('color','red');
						}

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
					$('.receive_fee_pl').change(function(){
						var total_fee=0;
						$(".receive_fee_pl").each(function(){
							if($(this).val()!=0){
								total_fee +=  parseFloat($(this).val());
							}
						});
						var pay_times = $('#pay_times').val();
						$(".danci_fee").html(total_fee)
						$(".totle_fee").html(total_fee*pay_times);
						$("#need_receive_total_pl").val(total_fee);
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
			<input type="hidden" class="input" name="r_id" value="<?=$collo_rent_detail['id']?>" id='r_id'>
			<input type="hidden" class="input" name="collocation_id" value="<?=$collo_rent_detail['collocation_id']?>" id='collocation_id'>
			<input type="hidden" class="input" name="collo_rent_id" value="<?=$collo_rent_detail['collo_rent_id']?>" id='collo_rent_id'>
			<input type="hidden" class="input" name="rent_start_time" value="<?php echo date('Y-m-d',$collo_rent_detail['rent_start_time']);?>" id='rent_start_time'>
			<input type="hidden" class="input" name="rent_end_time" value="<?php echo date('Y-m-d',$collo_rent_detail['rent_end_time']);?>" id='rent_end_time'>
			<input type="hidden" class="input empty" name="need_receive_total_pl" value="" id='need_receive_total_pl'>
			<input type="hidden" id="pay_times" name='pay_times'>
        </form>
    </div>
</div>
<!--添加实收客户-->
<div class="pop_box_g" id="js_pop_add_attendance_ss" style="width:510px; height:500px; display: none;">
    <div class="hd header">
        <div class="title" id='edit_actual'>添加实收客户</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="reclaim-mod reclaim-mod2">
        <form action="#" method="post" id='add_actual_receive'>
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
			<div class="total clearfix"><div class="fr"><strong>合计：</strong><font class="f60 f14 total_actual_fee" id='actual_total'></font> 元</div></div>
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
						$("#actual_receive_total").val(total_fee);
					});
				});
			</script>
            <table>
                <tr>
					<td width="70" class="label"><font class="red">*</font>付款人：</td>
                    <td width="210">
						<select class="select mr10 w90 empty" name="agency_id" id="agency_id">
                             <?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>" <?php if ($val['id'] == $post_param['agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }
                            }?>
                        </select>
						<select class="select w80 empty" name="broker_id" id="broker_id">
								<!--<?php if (is_full_array($post_config['brokers'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if ($val['broker_id'] == $post_param['broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php }}?>-->
								 <option value="">请选择</option>
						</select>
					</td>
					<td width="70" class="label"><font class="red">*</font>实收日期：</td>
                    <td><input type="text" class="input_text time_bg empty" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='actual_receive_time' name='actual_receive_time' value='<?php echo date('Y-m-d',time())?>'></td>
                </tr>
				<tr>
					<td colspan='2'><span style='display:none' class='text_person'>请选择付款人</span></td>
					<td colspan='2'><span style='display:none' class='text_actual_pay'>请选择一个实付日期</span></td>
				</tr>
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
									var html = "";
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
                <tr>
					<td class="label"><font class="red">*</font>实付方式：</td>
                    <td>
						<select class="select mr10 w90 empty" name="receipt_type" id="receipt_type">
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
					<td class="label"></td>
                    <td colspan="3">
						<p class="check_box"><b class="label fl mr10"><input type='checkbox' class="js_checkbox input_checkbox"> 刷卡手续费</b> <input type="text" class="input_text w90 mr10 empty" id='slot_card_fee' name='slot_card_fee'>元<span class='shuaka'></span></p>
					</td>
                </tr>
				<script>
					/*$(function(){
						$('#slot_card_fee').change(function(){
							if(("input[type='checkbox']").is(":checked")){
								$('.shuaka').hide();
							}else{
								$('.shuaka').html('<font color="red">请确认刷卡手续费</font>');
								return false;
							}
						});
					});*/
				</script>
                <tr>
                    <td class="label">说明：</td>
                    <td colspan="3">
						<textarea name="remark" id="remark_actual" class="textarea textarea2 empty"></textarea>
					</td>
                </tr>
                <tr>
                	<td colspan="4" class="center">
						<button type="button" id="dialog_share4" class="btn-lv1 btn-left">确定</button>
						<button type="button" id="dialog_share5" class="btn-lv1 btn-left JS_Close" style='display:none'>保存</button>
						<button type="button" class="btn-hui1 JS_Close">取消</button>
					</td>
                </tr>
            </table>
			<input type="hidden" class="input" name="r_id" value="<?=$collo_rent_detail['id']?>" id='r_id'>
			<input type="hidden" class="input" name="collocation_id" value="<?=$collo_rent_detail['collocation_id']?>" id='collocation_id'>
			<input type="hidden" class="input" name="collo_rent_id" value="<?=$collo_rent_detail['collo_rent_id']?>" id='collo_rent_id'>
			<input type="hidden" class="input" name="actual_receive_id" value="" id='actual_receive_id'>
			<input type="hidden" class="input" name="actual_receive_total" value="" id='actual_receive_total'>
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
                    <button type="button"   class="btn-hui1 JS_Close">取消</button>

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
	//应收单次添加
	$('#dialog_share').click(function(){
		if($('#rental').val() || $('#ele_fee').val()  || $('#ele_fee').val() || $('#gas_fee').val()  || $('#int_fee').val() || $('#tv_fee').val() || $('#property_fee').val() || $('#preserve_fee').val() || $('#garbage_fee').val() || $('#other_fee').val()){
			if($('#need_receive_time').val()){
				$('.text_time').hide();
				$.ajax({
					url: "<?php echo MLS_URL;?>/collocation_contract/add_need_receive/1",
					type: "POST",
					dataType: "json",
					data:$("#need_receive_add").serialize(),
					success: function(data) {
						if(data == 'ok')
						{
							$('#js_pop_add_attendance_dcys').hide();
							openWin('js_pop_msg1');
							$("#dialog_do_itp").html('添加应收客户成功');
							setTimeout(function(){location.href="/collocation_contract/rent_contract_detail/<?=$collo_rent_detail['id']?>/1"},2000);
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
			$('#dialog_do_itp_').html('请填写至少一项应收信息！');
			openWin('js_pop_do_success');
		}
	});
	//应收批量添加
	$('#dialog_share1').click(function(){
		if($('#rental_p').val() || $('#ele_fee_p').val()  || $('#ele_fee_p').val() || $('#gas_fee_p').val()  || $('#int_fee_p').val() || $('#tv_fee_p').val() || $('#property_fee_p').val() || $('#preserve_fee_p').val() || $('#garbage_fee_p').val() || $('#other_fee_p').val()){
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/add_need_receive/2",
				type: "POST",
				dataType: "json",
				data:$("#need_receive_add_pl").serialize(),
				success: function(data) {
					if(data == 'ok')
					{
						$('#js_pop_add_attendance_plys').hide();
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('批量添加应收客户成功');
						setTimeout(function(){location.href="/collocation_contract/rent_contract_detail/<?=$collo_rent_detail['id']?>/1"},2000);
					}else if(data['errorCode'] == '403'){//无权限
							permission_none();
						}
				}
			});
		}else{
			$('#dialog_do_itp_').html('请填写至少一项应收信息！');
			openWin('js_pop_do_success');
		}
	});
	//应收编辑保存
	$('#dialog_share3').click(function(){
		if($('#rental').val() || $('#ele_fee').val()  || $('#ele_fee').val() || $('#gas_fee').val()  || $('#int_fee').val() || $('#tv_fee').val() || $('#property_fee').val() || $('#preserve_fee').val() || $('#garbage_fee').val() || $('#other_fee').val()){
			if($('#need_receive_time').val()){
				$('.text_time').hide();
				$.ajax({
					url: "<?php echo MLS_URL;?>/collocation_contract/save_need_receive/1",
					type: "POST",
					dataType: "json",
					data:$("#need_receive_add").serialize(),
					success: function(data) {
						if(data == 'ok')
						{
							$('#js_pop_add_attendance_dcys').hide();
							openWin('js_pop_msg1');
							$("#dialog_do_itp").html('修改应收客户成功');
							setTimeout(function(){location.href="/collocation_contract/rent_contract_detail/<?=$collo_rent_detail['id']?>/1"},2000);
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
			$('#dialog_do_itp_').html('请填写至少一项应收信息！');
			openWin('js_pop_do_success');
		}
	});
	//实收添加
	$('#dialog_share4').click(function(){
		//如果勾选了 在列表页就改变资金状态为  已确认
		/*if($("input[type='checkbox']").is(":checked")){
			$('#fund_status').val('2');
		}*/
		if($('#rental_s').val() || $('#ele_fee_s').val()  || $('#ele_fee_s').val() || $('#gas_fee_s').val()  || $('#int_fee_s').val() || $('#tv_fee_s').val() || $('#property_fee_s').val() || $('#preserve_fee_s').val() || $('#garbage_fee_s').val() || $('#other_fee_s').val()){
			if($('#actual_receive_time').val() && $('#agency_id').val()){
				$('.text_person').hide();
				$('.text_actual_pay').hide();
				$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 是否确认当前款项已收付');
				openWin('jss_pop_tip');
				$('#dialog_share2').click(function(){
					$.ajax({
						url: "<?php echo MLS_URL;?>/collocation_contract/add_need_receive/3",
						type: "POST",
						dataType: "json",
						data:$("#add_actual_receive").serialize(),
						success: function(data) {
							if(data == 'ok')
							{
								openWin('js_pop_msg1');
								$("#dialog_do_itp").html('该实收客户已确认');
								setTimeout(function(){location.href="/collocation_contract/rent_contract_detail/<?=$collo_rent_detail['id']?>/2"},2000);
							}else if(data['errorCode'] == '403'){//无权限
								permission_none();
							}
						}
					});
				});
			}else if($('#actual_receive_time').val() == ''){
				$('.text_actual_pay').attr('style','display:inline');
				$('.text_actual_pay').css('color','red');
			}else{
				$('.text_person').attr('style','display:inline');
				$('.text_person').css('color','red');
			}
		}else{
			$('#dialog_do_itp_').html('请填写至少一项实收信息！');
			openWin('js_pop_do_success');
		}
	});
	//实收编辑保存
	$('#dialog_share5').click(function(){
		/*if($("input[type='checkbox']").is(":checked")){
			$('#fund_status').val('2');
		}*/
		$.ajax({
			url: "<?php echo MLS_URL;?>/collocation_contract/save_need_receive/2",
			type: "POST",
			dataType: "json",
			data:$("#add_actual_receive").serialize(),
			success: function(data) {
				if(data == 'ok')
				{
					openWin('js_pop_msg1');
					$("#dialog_do_itp").html('修改实收客户成功');
					setTimeout(function(){location.href="/collocation_contract/rent_contract_detail/<?=$collo_rent_detail['id']?>/2"},2000);
				}else if(data['errorCode'] == '403'){//无权限
							permission_none();
				}
			}
		});
	});
});
//应收，实收 修改
function need_receive_edit(id,tag){
	$.ajax({
		url: "<?php echo MLS_URL;?>/collocation_contract/rent_edit/",
		type: "GET",
		dataType: "json",
		data: {
			id:id,
			tag:tag
		},
		success: function(data) {
			if(data['result'] == 1){

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

				if(tag == 1){//应收
					$("#need_total").text(data['arr']['total_fee']);
					$("#need_receive_total").val(data['arr']['total_fee']);
					$("#remark").val(data['arr']['remark']);
					$("input[name='need_receive_time']").val(data['arr']['need_receive_time']);
					$('#need_receive_id').val(id);
					$("#dialog_share").attr('style','display:none');
					$("#dialog_share3").attr('style','display:inline');
					openWin('js_pop_add_attendance_dcys');
					$('#only_add').text('编辑应收客户');
				}else if(tag == 2){//实收
					$("#actual_total").text(data['arr']['total_fee']);
					$("#actual_receive_total").val(data['arr']['total_fee']);
					$("#slot_card_fee").val(data['arr']['slot_card_fee']);
					$("#remark_actual").val(data['arr']['remark']);
					$("#agency_id").val(data['arr']['agency_id']);

			var html = "<option value=''>请选择</option>";
		    for(var i in data['broker_list']){
			html +='<option value="'+data['broker_list'][i]['broker_id']+'">'+data['broker_list'][i]['truename']+'</option>';
		    }
		    $("#broker_id").html(html);

					$("select[name='broker_id']").val(data['arr']['broker_id']);
					$("input[name='actual_receive_time']").val(data['arr']['actual_receive_time']);
					$("#receipt_type").val(data['arr']['receipt_type']);
					$("input[name='receipts_num']").val(data['arr']['receipts_num']);
					$('#actual_receive_id').val(id);
					//根据slot_card_fee 确定checkbox是否勾选
					if(data['arr']['slot_card_fee']){
						$('.label').addClass('labelOn');
					}
					$("#dialog_share4").attr('style','display:none');
					$("#dialog_share5").attr('style','display:inline');
					openWin('js_pop_add_attendance_ss');
					$('#edit_actual').text('编辑实收客户');
				}

			}
		}
	});
}
//删除
function del_need_receive(id,tag,r_id){
	if(tag == 1){
		$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 应收客户信息删除后不可恢复，是否确认删除？');
	}else if(tag == 2){
		$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 实收客户信息删除后不可恢复，是否确认删除？');
	}
	openWin('jss_pop_tip');
	$("#dialog_share2").click(function(){
		$.ajax({
			url: "<?php echo MLS_URL;?>/collocation_contract/del_need_receive/",
			type: "GET",
			dataType: "json",
			data: {
				id:id,
				r_id:r_id,
				tag:tag
			},
			success: function(data) {
				if(data == 'ok')
				{
					openWin('js_pop_msg1');
					if(tag == 1){
						$("#dialog_do_itp").html('应收客户信息已删除');
					}else if (tag == 2){
						$("#dialog_do_itp").html('实收客户信息已删除');
					}
					location.reload();
				}else if(data['errorCode'] == '403'){//无权限
							permission_none();
				}
			}
		});
	});
}
//实收客户：在审核通过情况下，可以点击修改确认付款
	function modify_fund_status(id,r_id){
		$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 是否确定已收款');
		openWin('jss_pop_tip');
		$("#dialog_share2").click(function(){
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/sure_receipt/",
				type: "GET",
				dataType: "json",
				data: {
					id:id,
					r_id:r_id,
					tag:'2'
				},
				success: function(data) {
					if(data == 'ok')
					{
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('已确认');
						setTimeout(function(){location.href="/collocation_contract/rent_contract_detail/<?=$collo_rent_detail['id']?>/2"},2000);
					}else if(data['errorCode'] == '403'){//无权限
							permission_none();
					}
				}
			});
		});
	}
</script>
