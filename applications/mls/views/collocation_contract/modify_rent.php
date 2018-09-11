
<div class="contract-wrap clearfix">
	<div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms contract-forms forms_scroll">
		<form action="#" method="post" id='jsUpForm_rent_modify' name='jsUpForm_rent_modify'>
			<div class="forms_details_fg forms_details_fg_bg clearfix">
			   <div class="clearfix">
					<h3 class="h3">出租合同信息</h3>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">托管合同编号：</span>
						<div class="y_fg js_fields">
							<input class="input_text w77" value="<?=$collo_rent_list['collocation_id']?>" type="text" id='collocation_id' name='collocation_id' disabled>
							<input type="hidden" id='c_id' name='c_id' value='<?=$collo_rent_list['c_id']?>'>
							<input type="hidden" name='collocation_id' value='<?=$collo_rent_list['collocation_id']?>'>
							<div class="errorBox clear"></div>
						</div>
						<div class="y_fg js_fields">
							<input class="select-a" value="选择" type="text" id='rent_contract_choice' >
						</div>
					</label>
					<label class="label">
						<span class="text_fg">出租合同编号：</span>
						<div class="y_fg js_fields w132">
							<input class="input_text w77" value="<?=$collo_rent_list['collo_rent_id']?>" type="text" id='collo_rent_id' name='collo_rent_id'>
							<input type="hidden" id='id' name='id' value='<?=$collo_rent_list['id']?>'>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>楼盘名称：</span>
						<div class="y_fg js_fields w132">
							<input class="input_text w77" value="<?=$collo_rent_list['block_name']?>" type="text" id='block_name' name='block_name' disabled>
							<input type="hidden" id='block_id' name='block_id' value='<?=$collo_rent_list['block_id']?>'>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label" style="width:382px;">
						<span class="text_fg"><b class="red">*</b>所属经纪人：</span>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w132" name='agency_id_a' id='agency_id_a'>
								<option value="<?=$collo_rent_list['agency_id_a'];?>" selected><?=$collo_rent_list['agency_name']?></option>
							</select>
							<div class="errorBox clear"></div>
						</div>

						<div class="y_fg y_fg2 js_fields">
							<select class="select w116" name='broker_id_a'>
								<option value="<?=$collo_rent_list['broker_id_a']?>" selected><?=$collo_rent_list['broker_name']?></option>
							</select>
							<div class="errorBox clear"></div>
						</div>

					</label>
				</div>
				<input type='hidden' id='rent_total_month' name='rent_total_month' value='<?=$collo_rent_list['rent_total_month']?>'/>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>房源地址：</span>
						<div class="y_fg js_fields">
							<input class="input_text w119" value="<?=$collo_rent_list['houses_address']?>" type="text" id='houses_address' name='houses_address' disabled>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>出租时间：</span>
						<div class="y_fg y_fg2 js_fields w389">
							<input type="text" size="14" class="input_text time_bg w120" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='rent_start_time' name='rent_start_time' value='<?php echo date('Y-m-d',$collo_rent_list['rent_start_time']);?>'><span class="fl">-</span><input type="text" size="14" class="input_text time_bg w120" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='rent_end_time' name='rent_end_time' value='<?php echo date('Y-m-d',$collo_rent_list['rent_end_time']);?>'><em class="t">共<strong class="f00"><font class="f60 f14 totle_month" ><?=$collo_rent_list['rent_total_month']?></font></strong>个月</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>签约时间：</span>
						<div class="y_fg y_fg2 js_fields">
							<input type="text" size="14" class="input_text time_bg w120" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='signing_time' name='signing_time' value='<?php echo date('Y-m-d',$collo_rent_list['signing_time']);?>'>
							<div class="errorBox clear"></div>
						</div>
					</label>
				</div>
				<div class="item_fg clearfix">
				</div>
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
					$('#rent_end_time').blur(function(){
						var rent_start_time = $('#rent_start_time').val();
						var rent_end_time = $(this).val();
						//计算两个日期之间有几个月
						var date1 = new Date(rent_start_time);
						var str1 = (date1.getTime()/1000);//开始时间戳
						var year = date1.getFullYear();
						var month = date1.getMonth() +1;
						var first_month_day = DayNumOfMonth(year,month);//获取首月有多少天

						var date2 = new Date(rent_end_time);
						var str2 = (date2.getTime()/1000);//停付时间戳
						var day = (str2-str1)/86400 //获取两个日期之间一共有多少天

						if(first_month_day >= day){//起付跟停付之间有几个月
							month_times = 1;
						}else{
							month_times =  Math.ceil(day/first_month_day);//向上取整
						}
						if(rent_end_time != ''){
							$(".totle_month").html(month_times);
							$('#rent_total_month').val(month_times);
						}
					});
				});
			</script>
			<div class="forms_details_fg forms_details_fg_bg clearfix bt-none">
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>客户姓名</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['customer_name']?>" type="text" id='customer_name' name='customer_name'>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>联系方式：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['customer_tel']?>" type="text" id='customer_tel' name='customer_tel'>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">身份证号：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['customer_idcard']?>" type="text" id='customer_idcard' name='customer_idcard'>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">付款渠道：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" placeholder="银行名称、帐号或支付宝微信帐号" type="text" name='pay_ditch' id='pay_ditch' value='<?=$collo_rent_list['pay_ditch']?>'>
						</div>
					</label>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>签约门店：</span>
						<div class="left js_fields">
							<select class="select w132" name="agency_id" id="agency_id">
								<?php
                                if (is_full_array($post_config['agencys'])){
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"
                                        <?php if (isset($collo_rent_list['agency_id']) && $val['id'] == $collo_rent_list['agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
								<?php }}?>
							</select>
							<div class="errorBox clear"></div>
						</div>

					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>签约人：</span>
						<div class="y_fg js_fields">
							<select class="select w80" name="broker_id" id="broker_id">
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if (isset($collo_rent_list['broker_id']) && $val['broker_id'] == $collo_rent_list['broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php }}?>
							</select>
							<div class="errorBox clear"></div>
						</div>

					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>联系方式： </span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['broker_tel']?>" type="text" id='broker_tel' name='broker_tel'>
							<div class="errorBox clear"></div>
						</div>
					</label>
				</div>
			</div>
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
					if(data['result'] == 1){
					    var html = "";
					    for(var i in data['list']){
						html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
					    }
					    $('#broker_id').html(html);
					}
				    }
				})
			    }else{
				$('#broker_id').html("<option value=''>请选择</option>");
			    }
			});

            $("select[name='broker_id']").change(function(){
                var broker_id = $("select[name='broker_id']").val();
                if(broker_id){
                    $.ajax({
                        url:"/contract/get_broker_info",
                        type:"GET",
                        dataType:"json",
                        data:{
                           broker_id:broker_id
                        },
                        success:function(data){
                            if(data['result'] == 1){
                                $("input[name='broker_tel']").val(data['data']['phone']);
                                $("input[name='broker_tel']").attr('disabled','true');
                            }
                        }
                    })
                }else{
                    $("input[name='broker_tel']").val('');
                    $("input[name='broker_tel']").removeAttr('disabled');
                }
            })
			</script>
			<div class="forms_details_fg forms_details_fg_bg clearfix bt-none">
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>每月租金：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['rental']?>" type="text" id='rental' name='rental'><em class="t">元</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>付款方式：</span>
						<div class="y_fg js_fields">
							<select class="select w132" name='pay_type'>
								<option value="" <?php if(isset($collo_rent_list['pay_type'])){echo "selected";}?>>请选择</option>
								<option value="1" <?php if($collo_rent_list['pay_type'] == 1){echo "selected";}?>>月付</option>
								<option value="2" <?php if($collo_rent_list['pay_type'] == 2){echo "selected";}?>>季付</option>
								<option value="3" <?php if($collo_rent_list['pay_type'] == 3){echo "selected";}?>>半年付</option>
								<option value="4" <?php if($collo_rent_list['pay_type'] == 4){echo "selected";}?>>年付</option>
								<option value="5" <?php if($collo_rent_list['pay_type'] == 5){echo "selected";}?>>其他</option>
							</select>
						</div>
						<div class="errorBox clear"></div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>租金总额：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['rental_total']?>" type="text" id='rental_total' name='rental_total'><em class="t">元</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>押金金额：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['desposit']?>" type="text" id='desposit' name='desposit'><em class="t">元</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">违约金额：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['penal_sum']?>" type="text" id='penal_sum' name='penal_sum'><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">税费承担：</span>
						<div class="y_fg js_fields">
							<select class="select w132" name='tax_type' id='tax_type'>
								<option value="" <?php if(isset($collo_rent_list['tax_type'])){echo "selected";}?>>请选择</option>
								<option value="1" <?php if($collo_rent_list['tax_type'] == 1){echo "selected";}?>>业主</option>
								<option value="2" <?php if($collo_rent_list['tax_type'] == 2){echo "selected";}?>>客户</option>
								<option value="3" <?php if($collo_rent_list['tax_type'] == 3){echo "selected";}?>>公司</option>
							</select>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">每月物业费用：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['property_fee']?>" type="text" id='property_fee' name='property_fee'><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">物管承担：</span>
						<div class="y_fg js_fields">
							<select class="select w132" name='property_manage_assume' id='property_manage_assume'>
								<option value="" <?php if(isset($collo_rent_list['property_manage_assume'])){echo "selected";}?>>请选择</option>
								<option value="1" <?php if($collo_rent_list['property_manage_assume'] == 1){echo "selected";}?>>业主</option>
								<option value="2" <?php if($collo_rent_list['property_manage_assume'] == 2){echo "selected";}?>>客户</option>
								<option value="3" <?php if($collo_rent_list['property_manage_assume'] == 3){echo "selected";}?>>公司</option>
							</select>
						</div>
					</label>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">中介佣金：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['agency_commission']?>" type="text" id='agency_commission' name='agency_commission'><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">免租时间：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['rent_free_time']?>" type="text" id='rent_free_time' name='rent_free_time'><em class="t">天</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">出租状态：</span>
						<div class="y_fg js_fields">
							<select class="select w132" id='rent_type' name='rent_type'>
								<option value="">请选择</option>
								<option value="1">签约</option>
								<option value="2">交割</option>
								<option value="3">出租</option>
								<option value="4">空置</option>
								<option value="5">正退</option>
								<option value="6">转退</option>
								<option value="7">退租</option>
								<option value="8">作废</option>
							</select>
						</div>
					</label>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">房源维护：</span>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w132" name='houses_preserve_agency_id' id='houses_preserve_agency_id' value='<?=$post_param['houses_preserve_agency_id'];?>'>
								<?php
                                if (is_full_array($post_config['houses_preserve_agencys'])){
                                    foreach($post_config['houses_preserve_agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"
                                        <?php if (isset($collo_rent_list['houses_preserve_agency_id']) && $val['id'] == $collo_rent_list['houses_preserve_agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
								<?php }}?>
							</select>
						</div>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w116" name='houses_preserve_broker_id' id='houses_preserve_broker_id'>
								<?php
                                if (is_full_array($post_config['houses_preserve_agencys'])) {
                                foreach($post_config['houses_preserve_brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if (isset($collo_rent_list['houses_preserve_broker_id']) && $val['broker_id'] == $collo_rent_list['houses_preserve_broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php }}?>
							</select>
						</div>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['houses_preserve_money']?>" type="text" name='houses_preserve_money' id='houses_preserve_money'><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">客源维护：</span>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w132"  name='customer_preserve_agency_id' id='customer_preserve_agency_id'>
								<?php
                                if (is_full_array($post_config['customer_preserve_agencys'])) {
                                foreach($post_config['customer_preserve_agencys'] as $val){?>
                                <option value="<?=$val['id'];?>" <?php if (isset($collo_rent_list['customer_preserve_agency_id']) && $val['id'] == $collo_rent_list['customer_preserve_agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }}?>
							</select>
						</div>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w132"  name='customer_preserve_broker_id' id='customer_preserve_broker_id'>
								<?php
                                if (is_full_array($post_config['customer_preserve_agencys'])) {
                                foreach($post_config['customer_preserve_brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if (isset($collo_rent_list['customer_preserve_broker_id']) && $val['broker_id'] == $collo_rent_list['customer_preserve_broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php }}?>
							</select>
						</div>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['customer_preserve_money'];?>" type="text" name='customer_preserve_money' id='customer_preserve_money'><em class="t">元</em>
						</div>
					</label>
				</div>
				<script>
			$("#houses_preserve_agency_id").change(function(){
			    var houses_preserve_agency_id = $('#houses_preserve_agency_id').val();
			    if(houses_preserve_agency_id){
				$.ajax({
				    url:"/collocation_contract/broker_list",
				    type:"GET",
				    dataType:"json",
				    data:{
				       agency_id:houses_preserve_agency_id
				    },
				    success:function(data){
					if(data['result'] == 1){
					    var html = "";
					    for(var i in data['list']){
						html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
					    }
					    $('#houses_preserve_broker_id').html(html);
					}
				    }
				})
			    }else{
				$('#houses_preserve_broker_id').html("<option value=''>请选择</option>");
			    }
			});
			$("#customer_preserve_agency_id").change(function(){
			    var customer_preserve_agency_id = $('#customer_preserve_agency_id').val();
			    if(customer_preserve_agency_id){
				$.ajax({
				    url:"/collocation_contract/broker_list",
				    type:"GET",
				    dataType:"json",
				    data:{
				       agency_id:customer_preserve_agency_id
				    },
				    success:function(data){
					if(data['result'] == 1){
					    var html = "";
					    for(var i in data['list']){
						html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
					    }
					    $('#customer_preserve_broker_id').html(html);
					}
				    }
				})
			    }else{
				$('#customer_preserve_broker_id').html("<option value=''>请选择</option>");
			    }
			});

		    </script>
				<div class="item_fg clearfix">
					<label class="label w503">
						<span class="text_fg">退房经纪：</span>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w132" name='out_broker_agency_id' id='out_broker_agency_id'>
								<?php
                                if (is_full_array($post_config['out_agencys'])){
                                    foreach($post_config['out_agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"
                                        <?php if (isset($collo_rent_list['out_broker_agency_id']) && $val['id'] == $collo_rent_list['out_broker_agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
									<?php }
								}?>
							</select>
						</div>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w116" name='out_broker_broker_id' id='out_broker_broker_id'>
								<?php
                                if (is_full_array($post_config['out_agencys'])) {
                                foreach($post_config['out_brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if (isset($collo_rent_list['out_broker_broker_id']) && $val['broker_id'] == $collo_rent_list['out_broker_broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php
                                    }
                                }
                                 ?>
							</select>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">终止协议号：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="<?=$collo_rent_list['stop_agreement_num']?>" type="text" id='stop_agreement_num' name='stop_agreement_num'>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">到期日期：</span>
						<div class="y_fg y_fg2 js_fields">
							<input type="text" size="14" class="input_text time_bg w120" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='expire_time' name='expire_time' value='<?php echo $collo_rent_list['expire_time']?date('Y-m-d',$collo_rent_list['expire_time']):'';?>'>
						</div>
					</label>
				</div>
				<script>

			$("#out_broker_agency_id").change(function(){
			    var out_broker_agency_id = $('#out_broker_agency_id').val();
			    if(out_broker_agency_id){
				$.ajax({
				    url:"/collocation_contract/broker_list",
				    type:"GET",
				    dataType:"json",
				    data:{
				       agency_id:out_broker_agency_id
				    },
				    success:function(data){
					if(data['result'] == 1){
					    var html = "";
					    for(var i in data['list']){
						html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
					    }
					    $('#out_broker_broker_id').html(html);
					}
				    }
				})
			    }else{
				$('#out_broker_broker_id').html("<option value=''>请选择</option>");
			    }
			});
				</script>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">备注：</span>
						<div class="y_fg y_fg2 js_fields">
							<textarea class="textarea" name="remark" id='remark'><?=$collo_rent_list['remark']?></textarea>
						</div>
					</label>
				</div>
			</div>
			<div class="center">
				<button type="submit" class="btn-lv1" style="margin-right:10px;">保存</button>
				<button type="button" class="btn-hui1" onclick="history.go(-1);">取消</button>
			</div>
			<input type="hidden" name="submit_flag" value="modify">
			<input type="hidden" name="modify_id" value="<?=$collo_rent_list['id']?>" id = 'rent_modify_id'>
		</form>
	</div>
</div>
<!--选择托管合同-->
<div id="js_pop_box_c" class="iframePopBox" style=" width:967px; height:620px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="967" height="620" class='iframePop' src=""></iframe>
</div>
<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop">
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
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>

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
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;"  id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js"></script>
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
	});
});
</script>
<script>
	function get_info(id){
	$("#js_pop_box_c").hide();
	$("#GTipsCoverjs_pop_box_c").remove();
	$.post(
	    '/collocation_contract/get_info',
	    {'id':id},
            function(data){
                $("input[name='collocation_id']").val(data['collocation_id']);
				$("input[name='c_id']").val(data['id']);
			},'json'
        );
    }
</script>
