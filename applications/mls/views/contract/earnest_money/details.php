<div class="pop_box_g" id="js_pop_add_attendance_kq" style="width:765px; height:476px; display: block;">
    <div class="hd header">
        <div class="title">诚意金详情</div>
    </div>
    <div class="reclaim-mod" style="height:415px;overflow-y:auto;padding: 10px 0 0 20px;">
        <form action="" method="post" id="earnest_edit_form">
            <table>
				<tr>
					<td width="77" class="label"><font class="red">*</font>交易类型：</td>
                    <td width="102">
                        <select class="select" style="width:94px;" name="trade_type" id="trade_type" disabled="true" id="trade_type">
                            <?php foreach($post_config['trade_type'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if ((isset($earnest_money['trade_type']) && $key == $earnest_money['trade_type']) || $key == $post_config['type_id']) {echo 'selected';}?>><?=$val?></option>
                            <?php }?>
                        </select>
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label">房源编号：</td>
                    <td width="102">
                       <input class="input_text w90" type="text" size="14" name="house_id" id="house_id"
                       value="<?php if (isset($earnest_money['house_id']) && $earnest_money['house_id'] != '') {echo $earnest_money['house_id'];}?>" disabled="true" >
                       <div class="errorBox"></div>
                    </td>
					<td width="77" class="label">物业类型：</td>
                    <td width="102">
                        <select class="select" style="width:94px" name="sell_type" id="sell_type" disabled="true" >
                            <?php foreach($post_config['sell_type'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if (isset($earnest_money['sell_type']) && $key == $earnest_money['sell_type']) {echo 'selected';}?>><?=$val;?></option>
                            <?php }?>
                        </select>
                        <div class="errorBox"></div>
                    </td>
					<td width="77"  class="label">意向金额：</td>
                    <td>
                        <input class="input_text mr5 w60" type="text" name="intension_price" id="intension_price"  value="<?php if (isset($earnest_money['intension_price']) && $earnest_money['intension_price'] != '') {echo strip_end_0($earnest_money['intension_price']);}?>" disabled="true" > <?php echo $post_config['type_id'] == 1 ? '万' : '';?>元
                        <div class="errorBox"></div>
                    </td>
				</tr>
				<tr>
					<td class="label"><font class="red">*</font>楼盘名称：</td>
                    <td colspan="3">
                        <input type="text" name="block_name" value="<?php if (isset($earnest_money['block_name']) && $earnest_money['block_name'] != '') {echo $earnest_money['block_name'];}?>" class="input_text w248 ui-autocomplete-input" autocomplete="off" id="block_name" disabled="true" >
                        <input name="block_id" value="<?php if (isset($earnest_money['block_id']) && $earnest_money['block_id'] != '') {echo $earnest_money['block_id'];}?>" type="hidden" id="block_id">
                        <div class="errorBox"></div>
                        </td>
					<td class="label"><font class="red">*</font>房源地址：</td>
                    <td colspan="3"><input class="input_text mr5 w248" type="text" id="address" name="address"  value="<?php if (isset($earnest_money['address']) && $earnest_money['address'] != '') {echo $earnest_money['address'];}?>" disabled="true" >
                    <div class="errorBox"></div>
                    </td>
				</tr>
			</table>
			<h3>卖方信息</h3>
            <table>
				<tr>
					<td width="77" class="label"><font class="red">*</font>业主姓名：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="seller_owner" name="seller_owner" value="<?php if (isset($earnest_money['seller_owner']) && $earnest_money['seller_owner'] != '') {echo $earnest_money['seller_owner'];}?>" disabled="true" >
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label"><font class="red">*</font>联系方式：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="seller_telno" name="seller_telno" value="<?php if (isset($earnest_money['seller_telno']) && $earnest_money['seller_telno'] != '') {echo $earnest_money['seller_telno'];}?>" disabled="true" >
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label">身份证号：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="seller_idcard" name="seller_idcard" value="<?php if (isset($earnest_money['seller_idcard']) && $earnest_money['seller_idcard'] != '') {echo $earnest_money['seller_idcard'];}?>" disabled="true" >
                        <div class="errorBox"></div>
                    </td>
					<td width="77"  class="label"></td>
                    <td></td>
				</tr>
			</table>
			<h3>买方信息</h3>
            <table>
				<tr>
					<td width="77" class="label"><font class="red">*</font>客户姓名：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="buyer_owner" name="buyer_owner" value="<?php if (isset($earnest_money['buyer_owner']) && $earnest_money['buyer_owner'] != '') {echo $earnest_money['buyer_owner'];}?>" disabled="true" >
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label"><font class="red">*</font>联系方式：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="buyer_telno" name="buyer_telno"  value="<?php if (isset($earnest_money['buyer_telno']) && $earnest_money['buyer_telno'] != '') {echo $earnest_money['buyer_telno'];}?>" disabled="true" >
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label">身份证号：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="buyer_idcard" name="buyer_idcard" value="<?php if (isset($earnest_money['buyer_idcard']) && $earnest_money['buyer_idcard'] != '') {echo $earnest_money['buyer_idcard'];}?>" disabled="true" >
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label"><font class="red">*</font>诚意金额：</td>
                    <td>
                        <input class="input_text mr5 w60" type="text" id="earnest_price" name="earnest_price" value="<?php if (isset($earnest_money['earnest_price']) && $earnest_money['earnest_price'] != '') {echo strip_end_0($earnest_money['earnest_price']);}?>" disabled="true" > 元
                        <div class="errorBox"></div>
                    </td>
				</tr>
				<tr>
					<td class="label"><font class="red">*</font>收款日期：</td>
                    <td>
                        <input type="text" class="input_text time_bg" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" name="collection_time" id="collection_time" value="<?php if (isset($earnest_money['collection_time']) && $earnest_money['collection_time'] != '') {echo $earnest_money['collection_time'];}?>" disabled="true" >
                        <div class="errorBox"></div>
                    </td>
					<td class="label"><font class="red">*</font>诚意金状态：</td>
                    <td >
                        <select class="select" style="width:94px" name="status" id="status" disabled="true" >
                           <?php foreach($post_config['status'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if (isset($earnest_money['status']) && $key == $earnest_money['status']) {echo 'selected';}?>><?=$val;?></option>
                            <?php }?>
                        </select>
                        <div class="errorBox"></div>
					</td>
					<td class="label"><font class="red">*</font>收款人：</td>
                    <td colspan="3">
                        <select class="select mr10" style="width:104px" name="payee_agency_id" id="payee_agency_id" disabled="true" >
                            <?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"
                                        <?php if (isset($earnest_money['payee_agency_id']) && $val['id'] == $earnest_money['payee_agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }
                            }?>
                        </select>
                        <select class="select" style="width:104px" name="payee_broker_id" id="payee_broker_id" disabled="true" >
                            <?php
                                if (is_full_array($post_config['agencys'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if (isset($earnest_money['payee_broker_id']) && $val['broker_id'] == $earnest_money['payee_broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php
                                    }
                                }
                                 ?>
                        </select>
                        <div class="errorBox"></div>
					</td>
				</tr>
				<tr>
					<td class="label"><font class="red">*</font>收款方式：</td>
                    <td>
                        <select class="select" style="width:94px" name="collect_type" id="collect_type" disabled="true" >
                           <?php foreach($post_config['collect_type'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if (isset($earnest_money['collect_type']) && $key == $earnest_money['collect_type']) {echo 'selected';}?>><?=$val;?></option>
                            <?php }?>
                        </select>
                        <div class="errorBox"></div>
					</td>
					<td class="label">退款方式：</td>
                    <td >
                        <select class="select" style="width:94px" name="refund_type" id="refund_type" disabled="true" >
                           <?php foreach($post_config['refund_type'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if (isset($earnest_money['refund_type']) && $key == $earnest_money['refund_type']) {echo 'selected';}?>><?=$val;?></option>
                            <?php }?>
                        </select>
                        <div class="errorBox"></div>
					</td>
					<td class="label">退款说明：</td>
                    <td colspan="3">
                        <input class="input_text mr5 w248" type="text" id="refund_reason" name="refund_reason" value="<?php if (isset($earnest_money['refund_reason']) && $earnest_money['refund_reason'] != '') {echo $earnest_money['refund_reason'];}?>" disabled="true" >
                        <div class="errorBox"></div>
                    </td>
				</tr>
				<tr>
                    <td class="label">备注：</td>
                    <td colspan="7">
                        <textarea name="remark" style="width:617px;" id="remark" class="textarea" disabled="true" ><?php if (isset($earnest_money['remark']) && $earnest_money['remark'] != '') {echo $earnest_money['remark'];}?></textarea>
                        <div class="errorBox"></div>
                    </td>
				</tr>
                <tr>
                	<td colspan="8" class="center">

					</td>
                </tr>
            </table>
        </form>
    </div>
</div>
