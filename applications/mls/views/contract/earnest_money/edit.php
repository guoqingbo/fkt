<div class="pop_box_g" id="js_pop_add_attendance_kq" style="width:820px; height:476px; display: block;">
    <div class="hd header">
        <div class="title"><?php echo $earnest_money_id == '' ? '新增' : '编辑';?>诚意金</div>
    </div>
    <div class="reclaim-mod" style="height:415px;overflow-y:auto;padding: 10px 25px 0 25px;overflow-x:hidden;">
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
                       <input class="input_text w90" style="width:60px;" type="text" size="14" name="house_id" id="house_id"
                       value="<?php if (isset($earnest_money['house_id']) && $earnest_money['house_id'] != '') {echo $earnest_money['house_id'];}?>" <?=$earnest_money['house_id']?'disabled':'';?>><b style="width:28px;border:1px solid #d1d1d1;height:22px;float:left;display:inline;background:#fcfcfc;border-left:none;text-align:center" onclick="parent.open_house(<?=$post_config['type_id']?>,$('#house_id').val());">选择</b>
                       <div class="errorBox"></div>
                    </td>
					<td width="77" class="label">物业类型：</td>
                    <td width="102">
                        <select class="select" style="width:94px" name="sell_type" id="sell_type" <?=$earnest_money['house_id']?'disabled':'';?>>
                            <?php foreach($post_config['sell_type'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if (isset($earnest_money['sell_type']) && $key == $earnest_money['sell_type']) {echo 'selected';}?>><?=$val;?></option>
                            <?php }?>
                        </select>
                        <div class="errorBox"></div>
                    </td>
					<td width="77"  class="label">意向金额：</td>
                    <td>
                        <input class="input_text mr5 w60 test_money" type="text" name="intension_price" id="intension_price"  value="<?php if (isset($earnest_money['intension_price']) && $earnest_money['intension_price'] != '') {echo strip_end_0($earnest_money['intension_price']);}?>"> <?php echo $post_config['type_id'] == 1 ? '万' : '';?>元
                        <div class="errorBox"></div>
                    </td>
				</tr>
				<tr>
					<td class="label"><font class="red">*</font>楼盘名称：</td>
                    <td colspan="3">
                        <input type="text" name="block_name" value="<?php if (isset($earnest_money['block_name']) && $earnest_money['block_name'] != '') {echo $earnest_money['block_name'];}?>" class="input w248 ui-autocomplete-input input_text" autocomplete="off" id="block_name" style="width:275px;" <?=$earnest_money['house_id']?'disabled':'';?>>
                          <input name="block_id" value="<?php if (isset($earnest_money['block_id']) && $earnest_money['block_id'] != '') {echo $earnest_money['block_id'];}?>" type="hidden" id="block_id">
                        <div class="errorBox"></div>
                        </td>
					<td class="label"><font class="red">*</font>房源地址：</td>
                    <td colspan="3"><input class="input_text mr5 w248 address_lenth" type="text" id="address" name="address"  value="<?php if (isset($earnest_money['address']) && $earnest_money['address'] != '') {echo $earnest_money['address'];}?>" <?=$earnest_money['house_id']?'disabled':'';?>>
                    <div class="errorBox"></div>
                    </td>
				</tr>
			</table>

			<h3 style="font-weight:bold;">卖方信息</h3>
            <table>
				<tr>
					<td width="77" class="label"><font class="red">*</font>业主姓名：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="seller_owner" name="seller_owner" value="<?php if (isset($earnest_money['seller_owner']) && $earnest_money['seller_owner'] != '') {echo $earnest_money['seller_owner'];}?>" <?=$earnest_money['house_id']?'disabled':'';?>>
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label"><font class="red">*</font>联系方式：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="seller_telno" name="seller_telno" value="<?php if (isset($earnest_money['seller_telno']) && $earnest_money['seller_telno'] != '') {echo $earnest_money['seller_telno'];}?>" <?=$earnest_money['house_id']?'disabled':'';?>>
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label">身份证号：</td>
                    <td width="102">
                        <input class="input_text w248" type="text" id="seller_idcard" name="seller_idcard" value="<?php if (isset($earnest_money['seller_idcard']) && $earnest_money['seller_idcard'] != '') {echo $earnest_money['seller_idcard'];}?>" <?=$earnest_money['house_id']?'disabled':'';?>>
                        <div class="errorBox"></div>
                    </td>
					<td width="77"  class="label"></td>
                    <td></td>
				</tr>
			</table>
			<h3  style="font-weight:bold;">买方信息</h3>
            <table>
				<tr>
					<td width="77" class="label"><font class="red">*</font>客户姓名：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="buyer_owner" name="buyer_owner" value="<?php if (isset($earnest_money['buyer_owner']) && $earnest_money['buyer_owner'] != '') {echo $earnest_money['buyer_owner'];}?>">
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label"><font class="red">*</font>联系方式：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="buyer_telno" name="buyer_telno"  value="<?php if (isset($earnest_money['buyer_telno']) && $earnest_money['buyer_telno'] != '') {echo $earnest_money['buyer_telno'];}?>">
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label">身份证号：</td>
                    <td width="102">
                        <input class="input_text w90" type="text" id="buyer_idcard" name="buyer_idcard" value="<?php if (isset($earnest_money['buyer_idcard']) && $earnest_money['buyer_idcard'] != '') {echo $earnest_money['buyer_idcard'];}?>">
                        <div class="errorBox"></div>
                    </td>
					<td width="77" class="label"><font class="red">*</font>诚意金额：</td>
                    <td>
                        <input class="input_text mr5 w60 test_money" type="text" id="earnest_price" name="earnest_price" value="<?php if (isset($earnest_money['earnest_price']) && $earnest_money['earnest_price'] != '') {echo strip_end_0($earnest_money['earnest_price']);}?>"> 元
                        <div class="errorBox"></div>
                    </td>
				</tr>
				<tr>
					<td class="label"><font class="red">*</font>收款日期：</td>
                    <td>
                        <input type="text" class="input_text time_bg" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" name="collection_time" id="collection_time" value="<?php if (isset($earnest_money['collection_time']) && $earnest_money['collection_time'] != '') {echo $earnest_money['collection_time'];}?>">
                        <div class="errorBox"></div>
                    </td>
					<td class="label"><font class="red">*</font>诚意金状态：</td>
                    <td >
                        <select class="select" style="width:94px" name="status" id="status">
                           <?php foreach($post_config['status'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if (isset($earnest_money['status']) && $key == $earnest_money['status']) {echo 'selected';}?>><?=$val;?></option>
                            <?php }?>
                        </select>
                        <div class="errorBox"></div>
					</td>
					<td class="label"><font class="red">*</font>收款人：</td>
                    <td colspan="3">
                        <select class="select mr10" style="width:180px" name="payee_agency_id" id="payee_agency_id">
                            <?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"
                                        <?php if (isset($earnest_money['payee_agency_id']) && $val['id'] == $earnest_money['payee_agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }
                            }?>
                        </select>
                        <select class="select" style="width:80px" name="payee_broker_id" id="payee_broker_id">
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
                        <select class="select" style="width:94px" name="collect_type" id="collect_type">
                           <?php foreach($post_config['collect_type'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if (isset($earnest_money['collect_type']) && $key == $earnest_money['collect_type']) {echo 'selected';}?>><?=$val;?></option>
                            <?php }?>
                        </select>
                        <div class="errorBox"></div>
					</td>
					<td class="label">退款方式：</td>
                    <td >
                        <select class="select" style="width:94px" name="refund_type" id="refund_type">
                           <?php foreach($post_config['refund_type'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if (isset($earnest_money['refund_type']) && $key == $earnest_money['refund_type']) {echo 'selected';}?>><?=$val;?></option>
                            <?php }?>
                        </select>
                        <div class="errorBox"></div>
					</td>
					<td class="label">退款说明：</td>
                    <td colspan="3">
                        <input class="input_text mr5 w248 mtk_lenth" type="text" id="refund_reason" name="refund_reason" value="<?php if (isset($earnest_money['refund_reason']) && $earnest_money['refund_reason'] != '') {echo $earnest_money['refund_reason'];}?>">
                        <div class="errorBox"></div>
                    </td>
				</tr>
				<tr>
                    <td class="label">备注：</td>
                    <td colspan="7">
                        <textarea name="remark" style="width:617px;" id="remark" class="textarea  mbz_lenth"><?php if (isset($earnest_money['remark']) && $earnest_money['remark'] != '') {echo $earnest_money['remark'];}?></textarea>
                        <div class="errorBox"></div>
                    </td>
				</tr>
                <tr>
                	<td colspan="8" class="center">
                        <input type="hidden" name ="earnest_money_id" value="<?=$earnest_money_id?>" id="earnest_money_id">
						<button type="submit" id="sumbit_earnest" class="btn-lv1 btn-left">确定</button>
						<button type="button" class="btn-hui1 JS_Close">取消</button>
					</td>
                </tr>
            </table>
        </form>
    </div>
</div>
<script type="text/javascript">
                $(function(){

                        $(".address_lenth").blur(function(){

                            Test_length(".address_lenth",'40',".errorBox");

                        })


                         $(".mtk_lenth").blur(function(){

                                if(($(".mtk_lenth").val().length >50)){

                                    $(this).next(".errorBox").html("输入字符大于50，请重新输入！");

                                    $(this).focus();

                                      return false;
                                }

                        })

                          $(".mbz_lenth").blur(function(){

                                if(($(".mbz_lenth").val().length >100)){

                                    $(this).next(".errorBox").html("输入字符大于100，请重新输入！");

                                    $(this).focus();


                                }

                        })

                      //验证金额
                        $(".test_money").blur(function(){

                            if(!(/[0-9]$/).test($(this).val())){$(this).next(".errorBox").html("请输入数字！");}

                            if($(this).val().length > 10){
                                $(this).next(".errorBox").html("您输入的数字超过最大数，请从新输入");


                            }
                        })



                })


                function Test_length(obj,num,showClass){

                    var val = $(obj).val();
                   // if((val != "")){return false}
                    if((val.length > num)){

                        $(obj).next(showClass).html("输入字符超过"+num+"，请从新输入");
                        $(obj).focus();
                    }

                }



            </script>



<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop" style="width:75%;">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button" onclick="parent.window.location.reload(true)">确定</button>
            </div>
         </div>
    </div>
</div>
<script>
    $(function () {
        //楼盘名称联想
        $.widget( "custom.autocomplete", $.ui.autocomplete, {
            _renderItem: function( ul, item ) {
                if(item.id>0){
                return $( "<li>" )
                .data( "item.autocomplete", item )
                .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.label+'</span><span class="ui_district">'+item.districtname+'</span><span class="ui_address">'+item.address+'</span></a>')
                .appendTo( ul );
                }else{
                return $( "<li>" )
                .data( "item.autocomplete", item )
                .append('<a class="ui-corner-all" tabindex="-1">'+item.label+'</a>')
                .appendTo( ul );
                }
            }
        });
        $("input[name='block_name']").autocomplete({
            source: function( request, response ) {
            var term = request.term;
            $("input[name='block_id']").val("");
            $.ajax({
                url: "/community/get_cmtinfo_by_kw/",
                type: "GET",
                dataType: "json",
                data: {
                    keyword: term
                },
                success: function(data) {
                //判断返回数据是否为空，不为空返回数据。
                if( data[0]['id'] != '0'){
                    response(data);
                }else{
                    response(data);
                }
                }
            });
            },
            minLength: 1,
            removeinput: 0,
            select: function(event,ui) {
                if(ui.item.id > 0){
                var blockname = ui.item.label;
                var id = ui.item.id;
                var streetid = ui.item.streetid;
                var streetname = ui.item.streetname;
                var dist_id = ui.item.dist_id;
                var districtname = ui.item.districtname;
                var address = ui.item.address;

                //操作
                $("input[name='block_id']").val(id);
                $("input[name='block_name']").val(blockname);
                removeinput = 2;
                }else{
                removeinput = 1;
                }
            },
            close: function(event) {
                if(typeof(removeinput)=='undefined' || removeinput == 1){
                $("input[name='block_name']").val("");
                $("input[name='block_id']").val("");
                }
            }
        });

        //获取门店下经纪人
        $("#payee_agency_id").change(function(){
            var agency_id = $('#payee_agency_id').val();
            if(agency_id){
                $.ajax({
                    url:"/contract_earnest_money/broker_list",
                    type:"GET",
                    dataType:"json",
                    data:{
                       agency_id:agency_id
                    },
                    success:function(data){
                    if(data['result'] == 1){
                        var html = "";
                        for(var i in data['list']){
                        html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
                        }
                        $('#payee_broker_id').append(html);
                    }
                    }
                });
            } else {
                $('#payee_broker_id').html("<option value=''>请选择</option>");
            }
        });

        //关闭父窗口
        $('.JS_Close').bind('click', function() {
            closeParentWin('js_edit_pop');
        });
    });

</script>
