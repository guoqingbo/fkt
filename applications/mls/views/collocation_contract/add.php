<!--<script>
    window.parent.addNavClass(17);
</script>-->
<div class="contract-wrap clearfix">
	<div class="tab-left">
		<?=$user_tree_menu?>
	</div>
	<form action="" method="post" id='jsUpForm' name='jsUpForm'>
	<div class="forms contract-forms forms_scroll">

			<div class="forms_details_fg forms_details_fg_bg clearfix">
			   <div class="clearfix">
					<h3 class="h3">托管合同信息</h3>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>托管合同编号：</span>
						<div class="y_fg js_fields">
                            <input class="input_text w120" value="" type="text" name='collocation_id' autocomplete="off">
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">房源编号：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='house_id' id='house_id' autocomplete="off"/>
							<div class="errorBox clear"></div>
						</div>
						<div class="y_fg js_fields">
							<input class="select-a" value="选择" type="button" onclick="open_house_pop();">
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>楼盘名称：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='block_name' id="block_name" autocomplete="off">
							<input type="hidden" name="block_id" id="block_id" value="">
							<div class="errorBox clear"></div>
						</div>
					</label>

					<label class="label">
						<span class="text_fg"><b class="red">*</b>房源面积：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='houses_area' autocomplete="off"><em class="t">㎡</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
				</div>
				<script type="text/javascript">
					$(function(){
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
				});
				</script>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>房源地址：</span>
						<div class="y_fg js_fields">
							<input class="input_text w378" value="" type="text" name='houses_address' style="width:420px;" autocomplete="off">
							<div class="errorBox clear"></div>
						</div>
					</label>
					<div class="label "> <span class="text_fg">物业类型：</span>
                        <div class="y_fg">
                            <div class="left js_fields">
                                <select class="select" name='type' id='type' style="width:132px;">
                                    <option value="">请选择</option>
                                    <option value="1">住宅</option>
                                    <option value="2">公寓</option>
                                    <option value="3">别墅</option>
                                    <option value="4">写字楼</option>
                                    <option value="5">厂房</option>
                                    <option value="6">库房</option>
                                </select>
                            </div>
						</div>
					</div>
				</div>
				<input type='hidden' id='total_month' name='total_month' value=''/>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>托管日期：</span>
						<div class="y_fg y_fg2 js_fields">
							<input type="text" size="14" class="input_text time_bg w120" name='collo_start_time' id='collo_start_time' onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off">
							<span class="fl">-</span>
							<input type="text" size="14" class="input_text time_bg w120" name='collo_end_time' id='collo_end_time' onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off">


							<div class="errorBox clear"></div>
						</div>
					</label>
					<em class="t" style="float:left; line-height:26px;">共<strong class="f00"><font class="f60 f14 totle_month" ></font></strong>个月</em>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>签约日期：</span>
						<div class="y_fg js_fields">
							<input type="text" size="14" class="input_text time_bg w120" name='signing_time' id='signing_time' autocomplete="off" value= '<?php echo date('Y-m-d',time());?>' onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
							<div class="errorBox clear"></div>
						</div>
					</label>
				</div>
				<!--<div><span id='time_warning' style='display:none'><font color='red'>托管结束时间不能小于开始时间</font></span></div>-->
			</div>
			<script type="text/javascript">
			function DayNumOfMonth(Year,Month)//获得首付月份有多少天
			{
				Month--;
				var d = new Date(Year,Month,1);
				d.setDate(d.getDate()+32-d.getDate());
				return (32-d.getDate());
			}
			$(function(){
				$('#collo_end_time').blur(function(){
				var collo_start_time = $('#collo_start_time').val();
				var collo_end_time = $('#collo_end_time').val();
				if (collo_start_time == '' || collo_end_time == '') {return false;}


	        var date1 = new Date(collo_start_time.replace(/-/g,"/"));

	        var str1 = (date1.getTime()/1000);//开始时间戳
	        var year1 = date1.getFullYear();
	        var month1 = date1.getMonth() +1;
	        var day1 = date1.getDate();

	        var first_month_day = DayNumOfMonth(year1,month1);//获取首月有多少天
	        //alert(year+"+"+str1);
	        var date2 = new Date(collo_end_time.replace(/-/g,"/"));
            var year2 = date2.getFullYear();
            var month2 = date2.getMonth() +1;
            var day2 = date2.getDate();

	        var str2 = (date2.getTime()/1000);//停付时间戳
	        var day = (str2-str1)/86400 //获取两个日期之间一共有多少天

        if(str2 < str1){
							$('#time_warning').show();return false;
						}else{
							$('#time_warning').hide();
						}
						if(first_month_day >= day){//起付跟停付之间有几个月
							month_times = 1;
						}else{
                            month_diff = (year2-year1) * 12 + (month2 - month1);
	                        if (day2 > day1){
                                month_diff++;
                            }
                            month_times = month_diff;
                            // month_times =  Math.ceil(day/first_month_day);//向上取整
						}
						if(collo_end_time != ''){
							$(".totle_month").html(month_times);
							$('#total_month').val(month_times);
						}
					});
				$('#collo_end_time,#collo_start_time').focus(function(){
					$('#time_warning').hide();
				});
			});
			</script>
			<div class="forms_details_fg forms_details_fg_bg clearfix bt-none">
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>业主姓名：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='owner' autocomplete="off">
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>联系方式：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='owner_tel' maxlength="11" autocomplete="off">
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">身份证号：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='owner_idcard' autocomplete="off">
						</div>
					</label>
					<label class="label">
						<span class="text_fg">付款渠道：</span>
						<div class="y_fg js_fields">
							<input class="input_text w175" placeholder="银行名称、帐号或支付宝微信帐号" type="text" name='pay_ditch' autocomplete="off">
						</div>
					</label>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>签约门店：</span>
						<div class="left js_fields">
							<select class="select w132" name="agency_id" id="agency_id" >
								<?php
                                if (is_full_array($post_config['agencys'])){
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"
                                        <?php if (isset($agency_id) && $val['id'] == $agency_id) {echo 'selected';}?>><?=$val['name'];?></option>
									<?php }
								}?>
							</select>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>签约人：</span>
						<div class="y_fg js_fields">
							<select class="select w132" name="broker_id" id="broker_id">
								<?php
                                if (is_full_array($post_config['brokers'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>"><?=$val['truename'];?></option>
                                <?php
                                    }
                                }
                                 ?>
							</select>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>联系方式： </span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='broker_tel' maxlength="11" autocomplete="off">
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
					    var html = "<option value=''>请选择</option>";
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
			})
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
            $(function(){
                $('#rental').blur(function(){
                    var total_month = $('#total_month').val();
                    var rental = $(this).val();
                        $('#rental_total').val(total_month * rental);
                });
            });
			</script>
			<div class="forms_details_fg forms_details_fg_bg clearfix bt-none">
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>每月租金：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='rental' id='rental' autocomplete="off"><em class="t">元</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>付款方式：</span>
						<div class="y_fg js_fields">
							<select class="select w132" name='pay_type'>
								<option value="">请选择</option>
								<option value="1">月付</option>
								<option value="2">季付</option>
								<option value="3">半年付</option>
								<option value="4">年付</option>
								<option value="5">其他</option>
							</select>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>租金总额：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='rental_total' id='rental_total' autocomplete="off"><em class="t">元</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>押金金额：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='desposit' autocomplete="off"><em class="t">元</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">违约金额：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='penal_sum' autocomplete="off"><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">税费承担：</span>
						<div class="y_fg js_fields">
							<select class="select w132" name='tax_type'>
								<option value="">请选择</option>
								<option value="1">业主</option>
								<option value="2">客户</option>
								<option value="3">公司</option>
							</select>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">每月物业费用：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='property_fee' autocomplete="off"><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">物管承担：</span>
						<div class="y_fg js_fields">
							<select class="select w132"  name='property_manage_assume' >
								<option value="">请选择</option>
								<option value="1">业主</option>
								<option value="2">客户</option>
								<option value="3">公司</option>
							</select>
						</div>
					</label>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">中介佣金：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='agency_commission' autocomplete="off"><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">免租时间：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='rent_free_time' autocomplete="off"><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">托管状态：</span>
						<div class="y_fg js_fields">
							<select class="select w132" name='desposit_type'>
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
						<span class="text_fg">业绩分成1：</span>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w80" style="width:140px;" name="divide_a_agency_id" id="divide_a_agency_id">
								<!--<option value="">请选择</option>
								<?php foreach($agency as $key =>$val){?>
								<option value="<?=$val['id'];?>"><?=$val['name'];?></option>
								<?php }?>-->
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"
                                        <?php if (isset($collocation['divide_a_agency_id']) && $val['id'] == $collocation['divide_a_agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }
                            }?>
							</select>
						</div>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w80" name="divide_a_broker_id" id="divide_a_broker_id">
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if (isset($collocation['divide_a_broker_id']) && $val['broker_id'] == $collocation['divide_a_broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php
                                    }
                                }
                                 ?>
							</select>
						</div>
						<div class="y_fg js_fields">
							<input class="input_text w120" style="width:60px;" value="<?=$post_param['divide_a_money']?>" type="text" name='divide_a_money' id='divide_a_money' autocomplete="off"><em class="t" style="left:74px;">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">业绩分成2：</span>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w80" style="width:140px;" name="divide_b_agency_id" id="divide_b_agency_id">
								<!--<option value="">请选择</option>
								<?php foreach($agency as $key =>$val){?>
								<option value="<?=$val['id'];?>"><?=$val['name'];?></option>
								<?php }?>-->
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"
                                        <?php if (isset($collocation['divide_b_agency_id']) && $val['id'] == $collocation['divide_b_agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }
                            }?>
							</select>
						</div>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w80" name="divide_b_broker_id" id="divide_b_broker_id">
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if (isset($collocation['divide_b_broker_id']) && $val['broker_id'] == $collocation['divide_b_broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php
                                    }
                                }
                                 ?>
							</select>
						</div>
						<div class="y_fg js_fields">
							<input class="input_text w120" style="width:60px;" value="<?=$post_param['divide_b_money']?>" type="text" name='divide_b_money' id='divide_b_money' autocomplete="off"><em class="t" style="left:77px;">元</em>
						</div>
					</label>
				</div>
				<script>
			$("#divide_a_agency_id").change(function(){
			    var divide_a_agency_id = $('#divide_a_agency_id').val();
			    if(divide_a_agency_id){
				$.ajax({
				    url:"/collocation_contract/broker_list",
				    type:"GET",
				    dataType:"json",
				    data:{
				       agency_id:divide_a_agency_id
				    },
				    success:function(data){
					if(data['result'] == 1){
					    var html = "";
					    for(var i in data['list']){
						html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
					    }
					    $('#divide_a_broker_id').html(html);
					}
				    }
				})
			    }else{
				$('#divide_a_broker_id').html("<option value=''>请选择</option>");
			    }
			})
			$("#divide_b_agency_id").change(function(){
			    var divide_b_agency_id = $('#divide_b_agency_id').val();
			    if(divide_b_agency_id){
				$.ajax({
				    url:"/collocation_contract/broker_list",
				    type:"GET",
				    dataType:"json",
				    data:{
				       agency_id:divide_b_agency_id
				    },
				    success:function(data){
					if(data['result'] == 1){
					    var html = "";
					    for(var i in data['list']){
						html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
					    }
					    $('#divide_b_broker_id').html(html);
					}
				    }
				})
			    }else{
				$('#divide_b_broker_id').html("<option value=''>请选择</option>");
			    }
			})
		    </script>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">退房经纪：</span>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w80" name="out_agency_id" id="out_agency_id">
								 <?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"
                                        <?php if (isset($collocation['out_agency_id']) && $val['id'] == $collocation['out_agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }
                            }?>
							</select>
						</div>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w80" name="out_broker_id" id="out_broker_id">
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if (isset($collocation['out_broker_id']) && $val['broker_id'] == $collocation['out_broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
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
							<input class="input_text w120" value="" type="text" name='stop_agreement_num' autocomplete="off">
						</div>
					</label>
				</div>
				<script>
			$("#out_agency_id").change(function(){
			    var out_agency_id = $('#out_agency_id').val();
			    if(out_agency_id){
				$.ajax({
				    url:"/collocation_contract/broker_list",
				    type:"GET",
				    dataType:"json",
				    data:{
				       agency_id:out_agency_id
				    },
				    success:function(data){
					if(data['result'] == 1){
					    var html = "";
					    for(var i in data['list']){
						html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
					    }
					    $('#out_broker_id').html(html);
					}
				    }
				})
			    }else{
				$('#out_broker_id').html("<option value=''>请选择</option>");
			    }
			})
		    </script>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">物品清单：</span>
						<div class="y_fg js_fields">
							<input class="input_text w929" value="" type="text" name='list_items' autocomplete="off">
						</div>
					</label>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">备注：</span>
						<div class="y_fg y_fg2 js_fields" style="height:auto;">
							<textarea class="textarea" name="remarks"></textarea>
						</div>
					</label>
				</div>
			</div>
			<div class="center">
				<button type="submit" class="btn-lv1 btn-left">保存</button>
				<button type="button" class="btn-hui1" onclick="history.go(-1);">取消</button>
			</div>
			<input type="hidden" name="submit_flag" value="add" id = 'action'>
	</div>
	</form>
</div>
<!--房源选择弹框-->
<div id="js_house_box" class="iframePopBox" style="width: 980px;height:575px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="980" height="575px" class='iframePop' src=""></iframe>
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
	function get_info(id){
        closeWindowWin('js_house_box');
        if(id){
            $.post(
                '/contract/get_info',
                {'id':id,
                'type':2},
                function(data){
                    $("input[name='house_id']").val(data['house_id']);
                    $("input[name='block_name']").val(data['block_name']);
                    $("input[name='block_id']").val(data['block_id']);
                    $("input[name='houses_area']").val(data['buildarea']);
                    $("input[name='houses_address']").val(data['address']+data['dong']+'栋'+data['unit']+'单元'+data['door']+'室');
                    $("select[name='type']").val(data['sell_type']);
                    $("input[name='owner']").val(data['owner']);
                    $("input[name='owner_tel']").val(data['telno1']);
                    $("input[name='owner_idcard']").val(data['idcare']);
                    $("input[name='block_name']").attr('disabled','true');
                    $("input[name='houses_address']").attr('disabled','true');
                    $("input[name='house_id']").attr('disabled','true');
                    $("select[name='type']").attr('disabled','true');
                    $("input[name='houses_area']").attr('disabled','true');
                    $("input[name='owner_tel']").attr('disabled','true');
                    $("input[name='owner']").attr('disabled','true');
                    $("input[name='owner_idcard']").attr('disabled','true');
                },'json'
            );
        }else{
            $("input[name='house_id']").val('');
            $("input[name='block_name']").val('');
            $("input[name='block_id']").val('');
            $("input[name='houses_area']").val('');
            $("input[name='houses_address']").val('');
            $("select[name='type']").val('');
            $("input[name='owner']").val('');
            $("input[name='owner_tel']").val('');
            $("input[name='owner_idcard']").val('');
            $("input[name='block_name']").removeAttr('disabled');
            $("input[name='houses_address']").removeAttr('disabled');
            $("input[name='house_id']").removeAttr('disabled');
            $("select[name='type']").removeAttr('disabled');
            $("input[name='houses_area']").removeAttr('disabled');
            $("input[name='owner_tel']").removeAttr('disabled');
            $("input[name='owner']").removeAttr('disabled');
            $("input[name='owner_idcard']").removeAttr('disabled');
        }
    }

    function open_house_pop(){
        var house_id = $("input[name='house_id']").val();
        $("#js_house_box .iframePop").attr('src','/contract/get_house/2/'+house_id);
        openWin('js_house_box');
    }
</script>

