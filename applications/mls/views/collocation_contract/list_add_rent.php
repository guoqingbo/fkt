
<div class="contract-wrap clearfix">
	<div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms contract-forms forms_scroll">
		<form action="#" method="post" id='jsUpForm_rent' name='jsUpForm_rent'>
			<input type="hidden" id='action' name='action' value='2'>
			<div class="forms_details_fg forms_details_fg_bg clearfix">
			   <div class="clearfix">
					<h3 class="h3">出租合同信息</h3>
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
				$("#block_id").val("");
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
						<span class="text_fg"><b class="red">*</b>托管合同编号：</span>
						<div class="y_fg js_fields">
							<input class="input_text w77" type="text" id='collocation_id' name='collocation_id' value=''>
							<input type="hidden" id='c_id' name='c_id' value=''>
							<div class="errorBox clear"></div>
						</div>
						<div class="y_fg js_fields">

							<input class="select-a" value="选择" type="button" id='rent_contract_choice' onclick="open_pop_box();"  style="height:22px;*height:24px;_height:21px;">

						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>出租合同编号：</span>
						<div class="y_fg js_fields w132"  style="width:95px;">
							<input class="input_text w77" value="" type="text" id='collo_rent_id' name='collo_rent_id'>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">楼盘名称：</span>
						<div class="y_fg js_fields w132" style="width:95px;">
							<input class="input_text w77" value="" type="text" id='block_name' name='block_name'>
							<input type="hidden" id='block_id' name='block_id' value=''>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label" style="width:382px;">
						<span class="text_fg"><b class="red">*</b>所属经纪人：</span><div class="y_fg y_fg2 js_fields">
							<select class="select w132" name='agency_id_a' id='agency_id_a'>
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"><?=$val['name'];?></option>
                                <?php }}?>
							</select>
							<div class="errorBox clear"></div>
						</div>

						<div class="y_fg y_fg2 js_fields">
							<select class="select w116" name='broker_id_a' id='broker_id_a'>
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>"><?=$val['truename'];?></option>
                                <?php }}?>
							</select>
							<div class="errorBox clear"></div>
						</div>
					</label>
				</div>
				<script>
			$("#agency_id_a").change(function(){
			    var agency_id = $('#agency_id_a').val();
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
					    $('#broker_id_a').html(html);
					}
				    }
				})
			    }else{
				$('#broker_id_a').html("<option value=''>请选择</option>");
			    }
			});
			</script>
				<input type='hidden' id='rent_total_month' name='rent_total_month' value=''/>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg"><b class="red">*</b>房源地址：</span>
						<div class="y_fg js_fields">
							<input class="input_text w119" value="" type="text" id='houses_address' name='houses_address'>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>出租时间：</span>
						<div class="y_fg y_fg2 js_fields w389">
							<input type="text" size="14" class="input_text time_bg w120" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='rent_start_time' name='rent_start_time'><span class="fl">-</span><input type="text" size="14" class="input_text time_bg w120" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='rent_end_time' name='rent_end_time'><em class="t">共<strong class="f00"><font class="f60 f14 totle_month" ></font></strong>个月</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>签约时间：</span>
						<div class="y_fg y_fg2 js_fields">
							<input type="text" size="14" class="input_text time_bg w120" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='signing_time' name='signing_time' value= '<?php echo date('Y-m-d',time());?>'>
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
						var date1 = new Date(rent_start_time.replace(/-/g, "/"));
						var str1 = (date1.getTime()/1000);//开始时间戳
						var year1 = date1.getFullYear();
						var month1 = date1.getMonth() +1;
                        var day1 = date1.getDate();

						var first_month_day = DayNumOfMonth(year1,month1);//获取首月有多少天

						var date2 = new Date(rent_end_time.replace(/-/g, "/"));
                        var year2 = date2.getFullYear();
                        var month2 = date2.getMonth() +1;
                        var day2 = date2.getDate();

						var str2 = (date2.getTime()/1000);//停付时间戳
						var day = (str2-str1)/86400 //获取两个日期之间一共有多少天

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
						<span class="text_fg"><b class="red">*</b>客户姓名： </span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" id='customer_name' name='customer_name'>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>联系方式：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" id='customer_tel' name='customer_tel' maxlength="11">
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">身份证号：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" id='customer_idcard' name='customer_idcard'>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">付款渠道：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" placeholder="银行名称、帐号或支付宝微信帐号" type="text" name='pay_ditch' id='pay_ditch'>
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
                                        <option value="<?=$val['id'];?>" <?=$agency_id==$val['id']?'selected':'';?>><?=$val['name'];?></option>
									<?php }
								}?>
							</select>
							<div class="errorBox clear"></div>
						</div>

					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>签约人：</span>
						<div class="y_fg js_fields">

							<select class="select w132" name="broker_id" value="<?=$post_param['broker_id'];?>" id="broker_id">
								<option value="">请选择</option>

							<!-- <select class="select w80" name="broker_id" id="broker_id"> -->
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
							<input class="input_text w120" value="" type="text" id='broker_tel' name='broker_tel' maxlength="11">
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

				$(function(){
					$('#rental').blur(function(){
						var total_month = $('#rent_total_month').val();
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
							<input class="input_text w120" value="" type="text" id='rental' name='rental'><em class="t">元</em>
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
						</div>
						<div class="errorBox clear"></div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>租金总额：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" id='rental_total' name='rental_total'><em class="t">元</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
					<label class="label">
						<span class="text_fg"><b class="red">*</b>押金金额：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" id='desposit' name='desposit'><em class="t">元</em>
							<div class="errorBox clear"></div>
						</div>
					</label>
				</div>
				<div class="item_fg clearfix">
					<label class="label">
						<span class="text_fg">违约金额：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" id='penal_sum' name='penal_sum'><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">税费承担：</span>
						<div class="y_fg js_fields">
							<select class="select w132" name='tax_type' id='tax_type'>
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
							<input class="input_text w120" value="" type="text" id='property_fee' name='property_fee'><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">物管承担：</span>
						<div class="y_fg js_fields">
							<select class="select w132" name='property_manage_assume' id='property_manage_assume'>
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
							<input class="input_text w120" value="" type="text" id='agency_commission' name='agency_commission'><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">免租时间：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" id='rent_free_time' name='rent_free_time'><em class="t">天</em>
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
							<select class="select w132" name='houses_preserve_agency_id' id='houses_preserve_agency_id'>
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"><?=$val['name'];?></option>
                                <?php }}?>
							</select>
						</div>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w116" name='houses_preserve_broker_id' id='houses_preserve_broker_id'>
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>"><?=$val['truename'];?></option>
                                <?php }}?>
							</select>
						</div>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='houses_preserve_money' id='houses_preserve_money'><em class="t">元</em>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">客源维护：</span>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w132"  name='customer_preserve_agency_id' id='customer_preserve_agency_id' value='<?=$post_param['customer_preserve_agency_id'];?>'>
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"><?=$val['name'];?></option>
                                <?php }}?>
							</select>
						</div>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w132"  name='customer_preserve_broker_id' id='customer_preserve_broker_id'>
									<?php
                                if (is_full_array($post_config['agencys'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>"><?=$val['truename'];?></option>
                                <?php }}?>
							</select>
						</div>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" name='customer_preserve_money' id='customer_preserve_money'><em class="t">元</em>
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
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>"><?=$val['name'];?></option>
                                <?php }}?>
							</select>
						</div>
						<div class="y_fg y_fg2 js_fields">
							<select class="select w116" name='out_broker_broker_id' id='out_broker_broker_id'>
								<?php
                                if (is_full_array($post_config['agencys'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>"><?=$val['truename'];?></option>
                                <?php }}?>
							</select>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">终止协议号：</span>
						<div class="y_fg js_fields">
							<input class="input_text w120" value="" type="text" id='stop_agreement_num' name='stop_agreement_num'>
						</div>
					</label>
					<label class="label">
						<span class="text_fg">到期日期：</span>
						<div class="y_fg y_fg2 js_fields">
							<input type="text" size="14" class="input_text time_bg w120" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id='expire_time' name='expire_time'>
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
						<div class="y_fg y_fg2 js_fields" style="height:auto;">
							<textarea class="textarea" name="remark" id='remark'></textarea>
						</div>
					</label>
				</div>
			</div>
			<div class="center">
				<button type="submit" class="btn-lv1 btn-left" style="margin-right:10px;">保存</button>
				<button type="button" class="btn-hui1" onclick="history.go(-1);">取消</button>
			</div>
			<input type="hidden" name="submit_flag" value="add">
		</form>
	</div>
</div>
<!--选择托管合同-->
<div id="js_pop_box_c" class="iframePopBox" style=" width:967px; height:457px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="967" height="457" class='iframePop' src=""></iframe>
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
        closeWindowWin('js_pop_box_c');
        if(id){
            $.post(
                '/collocation_contract/get_info',
                {'id':id},
                    function(data){
                        $("input[name='collocation_id']").val(data['collocation_id']);
                        $("input[name='c_id']").val(data['id']);
                        $("input[name='block_name']").val(data['block_name']);
                        $("input[name='block_id']").val(data['block_id']);
                        $("input[name='houses_address']").val(data['houses_address']);
                        $("input[name='collocation_id']").attr('disabled','true');
                        $("input[name='block_name']").attr('disabled','true');
                        $("input[name='houses_address']").attr('disabled','true');
                    },'json'
                );
        }else{
            $("input[name='collocation_id']").val('');
            $("input[name='c_id']").val('');
            $("input[name='block_name']").val('');
            $("input[name='block_id']").val('');
            $("input[name='houses_address']").val('');
            $("input[name='collocation_id']").removeAttr('disabled');
            $("input[name='block_name']").removeAttr('disabled');
            $("input[name='houses_address']").removeAttr('disabled');
        }
    }

    function open_pop_box(){
        var id = $("input[name='c_id']").val();
        $('.iframePop').attr('src','/collocation_contract/get_collocation_contract/'+id);
        openWin('js_pop_box_c');
    }
</script>
