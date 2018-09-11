<body>
	<!--实收实付添加弹窗开始-->
	<div class="achievement_money_pop real_W580" style="display: block;">
	    <dl class="title_top">
            <dd id='title_top'><?=$id?"编辑":"新增"?>实收实付</dd>
	    </dl>
	    <!--弹出框内容-->
	   <div class="add_pop_messages raal_H372">
		<div class="aad_pop_line1">
		    <form action="" id="add_actual" method="post">
		    <div style="width:98%; padding:1%;float:left;display:inline;">
		    	<ul>
					<li class="aad_pop_line1_title " style="width:10%;float:left;display:inline;">

							实收
					</li>
					<li class="aad_pop_line1_title " style="width:28%;float:left;display:inline;font-weight:normal;">

							<p class="aad_pop_line1_title_p"><b class="resut_table_state_1 input_add_F" style="font-weight:normal;">*</b>款项：</p>
						    <select class="aad_pop_select_W70" name="actual_money_type">
							<?php foreach($config['money_type'] as $key=>$val){?>
							<option value="<?=$key;?>" <?=$flow_list['money_type']==$key?'selected':''?>><?=$val;?></option>
							<?php }?>
						    </select>
						    <div class="errorBox"></div>
					</li>
					<li class="aad_pop_line1_title " style="width:28%;float:left;display:inline;font-weight:normal;">
						<p class="aad_pop_line1_title_p">　　收方：</p>
					    <select class="aad_pop_select_W70" name="actual_collect_type">
                        <option value="0">请选择</option>
						<?php foreach($config['collect_type'] as $key=>$val){?>
						<option value="<?=$key;?>" <?=$flow_list['collect_type']==$key?'selected':''?>><?=$val;?></option>
						<?php }?>
					    </select>
					</li>
					<li class="" style="float:left;display:inline;font-weight:normal;">

							 <p class="aad_pop_line1_title_p">实收金额：</p>
                             <input type="text" class="aad_pop_select_W70 test_money" name="actual_collect_money" value='<?=$flow_list['collect_money'];?>' autocomplete="off">元
		                    <div class="errorBox" style="text-align:right;"></div>
					</li>
		    	</ul>
		    </div>

			<div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
		    	<ul>
					<li class="aad_pop_line1_title " style="width:10%;float:left;display:inline;">

							实付
					</li>
					<li class="aad_pop_line1_title " style="width:28%;float:left;display:inline;font-weight:normal;">

							<p style=" width:3.5em;text-align:right" class="aad_pop_line1_title_p">付方：</p>

							<select class="aad_pop_select_W70" name="actual_pay_type">
                                <option value="0">请选择</option>
							    <?php foreach($config['pay_type'] as $key=>$val){?>
							    <option value="<?=$key;?>" <?=$flow_list['pay_type']==$key?'selected':''?>><?=$val;?></option>
							    <?php }?>
							</select>
							<div class="errorBox"></div>
					</li>
					<li class="aad_pop_line1_title " style="width:32%;float:left;display:inline;font-weight:normal;">
						<p class="aad_pop_line1_title_p">实付金额：</p>
                        <input type="text" class="aad_pop_select_W70 test_money" name="actual_pay_money" value="<?=$flow_list['pay_money'];?>" autocomplete="off">元
						<div class="errorBox" style="text-indent:6em;"></div>
					</li>


		    	</ul>
		    </div>

		    <div style="width:98%; padding:1%;float:left;display:inline;">
		    	<ul>
					<li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
						<p class="aad_pop_line1_title_p"><b class="resut_table_state_1">*</b>收付日期：</p>
					    <input type="text" class="aad_pop_select_W100 time_bg" name="actual_flow_time" value="<?=$flow_list['flow_time'];?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off">
					    <div class="errorBox" style="text-indent:6em;"></div>
					</li>
					<li class="aad_pop_line1_title " style="width:56%;float:left;display:inline;font-weight:normal;">

							<p class="aad_pop_line1_title_p ">　收付人：</p>
						    <select class="aad_pop_select_W100" name="actual_flow_agency" id="actual_flow_agency">
							<?php if($agencys){foreach($agencys as $key=>$val){?>
							<option value="<?=$val['id'];?>" <?=$flow_list['flow_agency_id']==$val['id']?'selected':''?>><?=$val['name'];?></option>
							<?php }}?>
						    </select>
						    <select class="aad_pop_select_W100" name="actual_flow_broker"  id="actual_flow_broker">
							<?php if($brokers){foreach($brokers as $key=>$val){?>
							<option value="<?=$val['broker_id'];?>" <?=$flow_list['flow_broker_id']==$val['broker_id']?'selected':''?>><?=$val['truename'];?></option>
							<?php }}?>
						    </select>
					</li>
		    	</ul>
		    </div>

			<div style="width:98%; padding:1%;float:left;display:inline;">
		    	<ul>
					<li class="aad_pop_line1_title " style="width:100%;float:left;display:inline;font-weight:normal;">
						<p class="aad_pop_line1_title_p"><b class="resut_table_state_1">*</b>收付方式：</p>
					    <select class="aad_pop_select_W100" name="actual_payment_method">
						<option value="">请选择</option>
						<?php foreach($config['payment_method'] as $key=>$val){?>
						    <option value="<?=$key;?>" <?=$flow_list['payment_method']==$key?'selected':''?>><?=$val;?></option>
						<?php }?>
					    </select>
					    <div class="errorBox" style="text-indent:6em;"></div>
					</li>

		    	</ul>
		    </div>

		    <div style="width:98%; padding:1%;float:left;display:inline;">
		    	<ul>
					<li class="aad_pop_line1_title " style="width:16%;float:left;display:inline;font-weight:normal;">
						<input type="checkbox" name="is_fee" value="1" <?=$flow_list['counter_fee']?'checked':''?>>刷卡手续费
					</li>
					<li class="aad_pop_line1_title " style="width:18.5%;float:left;display:inline;font-weight:normal;">

						<input type="text" class="aad_pop_select_W70" name="actual_counter_fee" value="<?=$flow_list['counter_fee']?>" <?=!$flow_list['counter_fee']?'disabled':''?>> 元
					</li>
					<li class="aad_pop_line1_title " style="width:31%;float:left;display:inline;font-weight:normal;">
						<p class="aad_pop_line1_title_p">单据号：</p>
				    	<input type="text" class="aad_pop_select_W100" name="actual_docket" value="<?=$flow_list['docket']?>" autocomplete="off" <?=!$flow_list['docket']?'disabled':''?>>
					</li>
					<li class="aad_pop_line1_title " style="width:31%;float:left;display:inline;font-weight:normal;">
						<p class="aad_pop_line1_title_p">单据类型：</p>
				    <select class="aad_pop_select_W100" name="actual_docket_type" <?=!$flow_list['docket_type']?'disabled':''?>>
					<?php foreach($config['docket_type'] as $key=>$val){?>
					    <option value="<?=$key;?>" <?=$flow_list['docket_type']==$key?'selected':''?>><?=$val;?></option>
					<?php }?>
				    </select>
					</li>

		    	</ul>
		    </div>
		 	<table width="100%">
			    <tbody>
				<tr>
				<td width="12%" style="text-align:right" class="label aad_pop_p_T20">收付说明：</td>
				<td width="86%" class="aad_pop_p_T20"><textarea class="aad_pop_select_textare_W" name="actual_remark"><?=$flow_list['remark'];?></textarea><div class="errorBox"></div></td>
				</tr>
			    </tbody>
			</table>
			<table width="100%" align="center">
			    <tbody><tr>
				<td style="text-align:center" class="aad_pop_p_T20">
                <input type="hidden" id="contract_id" value="<?=$c_id?>">
                <input type="hidden" id="flow_id" value="<?=$id?>">
                <button class="btn-lv1 btn-left" type="submit">确定</button>
                <button class="btn-hui1" type="button"   onclick="closeParentWin('js_actual_pop');">取消</button>
				</td>
			    </tr>
			    </tbody>
			</table>
			</form>
		    </div>
		</div>
	    </div>
	<!--实收实付添加弹窗结束-->
    <!--操作失败弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_false">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                            <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>
</body>

<script type="text/javascript">
    $(function() {
        $("input[name='is_fee']").click(function(){
            if($("input[name='is_fee']:checked").val() ==1){
                $("input[name='actual_counter_fee']").removeAttr('disabled');
                $("select[name='actual_docket_type']").removeAttr('disabled');
                $("input[name='actual_docket']").removeAttr('disabled');
            }else{
                $("input[name='actual_counter_fee']").attr('disabled','true');
                $("select[name='actual_docket_type']").attr('disabled','true');
                $("input[name='actual_docket']").attr('disabled','true');
            }
        })

        //获取门店下经纪人
        $("#actual_flow_agency").change(function(){
            var agency_id = $('#actual_flow_agency').val();
            if(agency_id){
                $.ajax({
                    url:"/contract_earnest_money/broker_list/",
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
                        }
                        $('#actual_flow_broker').html(html);
                    }
                });
            } else {
                $('#actual_flow_broker').html("<option value=''>请选择</option>");
            }
        });
    });
</script>
