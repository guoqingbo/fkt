
<div class="contract-wrap clearfix">
<div class="tab-left"><?=$user_tree_menu?></div>
<div class="forms_scroll h90">
    <form action="" id="addcont_form" method="post">
    <div class="contract_top_main">
        <div class="i_box" style=" padding:0;background:#f7f7f7">
	    <div class="clearfix"  style=" padding: 12px 16px;background:#f7f7f7">
		<table width="100%">
		    <thead>
			<tr>
			    <td class="h4">合同信息</td>
			</tr>
		    </thead>
		    <tbody>
                    <tr>
                        <td>
			    <div class="zws_ht_w">
				<ul>
				    <li>
					<span class="zws_border_span">
					    <p class="border_input_title zws_li_p_w "><b class="resut_table_state_1 zws_em ">*</b>
					    合同编号：</p>
					    <div class="input_add_F">
						<input type="text" class="border_color input_add_F zws_W128" value="<?=$contract['number'];?>" name="number" autocomplete="off">
						<div class="zws_block errorBox"></div>
					    </div>
					</span>

				    </li>
				    <li>
						<span class="zws_border_span" id="zws_num">
						    <p class="border_input_title zws_li_p_w"></b>房源编号：</p>
						    <div class="input_add_F">
							<input type="text" class="border_color input_add_F zws_border_W110" value="<?=$contract['house_id'];?>" name="house_id" autocomplete="off" <?=$contract['house_id']?'disabled':''?>>
							<button class="zws_border_button input_add_F" type="button" style="height:28px;line-height:28px;" onclick="open_house_pop();" >选择</button>
						    </div>
						    <div  class="zws_block padd_L errorBox"></div>
						</span>
				    </li>
				    <li>
                        <span class="zws_border_span">
                                <p class="border_input_title zws_li_p_w "><b class="resut_table_state_1 zws_em ">*</b>楼盘名称：</p>
                              <div class="input_add_F">
                                <span  class="input_add_F">
                                  <input type="text" class="border_color zws_W154" value="<?=$contract['block_name'];?>" name="block_name" autocomplete="off" <?=$contract['house_id']?'disabled':''?>>
                                  <input type="hidden" value="<?=$contract['block_id'];?>" name="block_id">
                                  <div  class="zws_block errorBox"></div>
                                 </span>
                              </div>
                          </span>
				    </li>
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
				    <li>
				    <span class="zws_border_span ">
					<p class="border_input_title zws_li_p_w"><b class="resut_table_state_1">*</b>房源面积：</p>
					<div class="input_add_F">
					    <input type="text" class="border_color zws_W128 input_add_F" value="<?=$contract['buildarea'];?>" name="buildarea" autocomplete="off" <?=$contract['house_id']?'disabled':''?>><strong class="zws_padd">m²</strong>
					    <div  class="zws_block errorBox"></div>
					</div>
				    </span>
				    </li>
				</ul>
			    </div>
                        </td>
                    </tr>
                    <tr>
			<td>
			    <div class="zws_ht_w">
				<ul>
				    <li class="zws_li_w">
					<span class="zws_border_span zws_W50">
					    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em ">*</b>房源地址：</p>
					    <div class="zws_li_p_w406 input_add_F">
					    <input type="text" class="border_color zws_li_p_w406 zws_color input_add_F"  value="<?=$contract['house_addr'];?>" name="house_addr" autocomplete="off" <?=$contract['house_id']?'disabled':''?>>
					    <div  class="zws_block errorBox"></div>
					    </div>
					</span>

				    </li>
				    <li>
					<span class="zws_border_span">
					    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em " ></b>物业类型：</p>
					    <select  class="border_color input_add_F zws_li_p_w130" style="height:28px;line-height:28px;background:#FFF;" name="sell_type" <?=$contract['house_id']?'disabled':''?>>
						<option value="">请选择</option>
						<?php foreach($config['sell_type'] as $key=>$val){?>
						    <option value="<?=$key;?>" <?=$contract['sell_type']==$key?'selected':'';?>><?=$val;?></option>
						<?php }?>
					    </select>
					</span>

				    </li>
				    <li>
					<span class="zws_border_span">
					    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em ">*</b>签约日期：</p>
					    <div class="input_add_F">
						<input type="text" class="border_color zws_W154 input_add_F time_bg" value="<?=isset($contract['signing_time'])?date('Y-m-d',$contract['signing_time']):'';?>" name="signing_time" style="border:1px solid #d1d1d1;" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off">
					    <div  class="zws_block errorBox"></div>
					</span>
				    </li>
                </ul>
                </div>
			</td>
            </tr>
            <!--第三行-->
            <tr>
			<td>
			    <div class="zws_ht_w">
				<ul>
                    <li>
                        <span class="zws_border_span">
                            <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em ">*</b>
                            交易方式：</p>
                            <div class="input_add_F">
                            <select  class="border_color zws_W128 input_add_F" style="height:28px;line-height:28px;background:#FFF;width:133px;" name="type" disabled>
                                <option value="1">出售</option>
                            </select>
                            <div  class="zws_block errorBox"></div>
                            </div>
                        </span>
                    </li>
                    <li>
                        <span class="zws_border_span" id="zws_num">
                            <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em ">*</b>成交金额：</p>
                            <div class="input_add_F">
                                <input type="text" class="border_color input_add_F zws_li_p_w120" value="<?=$contract['price'];?>" name="price" autocomplete="off">
                                <strong class="zws_padd">元</strong>
                                <div  class="zws_block errorBox"></div>
                            </div>
                        </span>
				    </li>
				    <li>
                        <span class="zws_border_span">
                            <p class="border_input_title zws_li_p_w100" style="line-height:28px;"><b class="resut_table_state_1 zws_em ">*</b>是否合作房源：</p>
                            <div class="input_add_F" id="zws_radio_tab">
                            <p class="zws_radio_no <?=isset($contract['is_cooperate']) && $contract['is_cooperate'] !== '0'?'yesOn':'';?>">是<input type="radio" value="1" name="is_cooperate" <?=isset($contract['is_cooperate']) && $contract['is_cooperate'] !== '0'?'checked':'';?> style="display: none"></p>
                            <p class="zws_radio_no <?=!isset($contract['is_cooperate']) || $contract['is_cooperate'] == '0'?'yesOn':'';?>">否<input type="radio" value="0" name="is_cooperate" <?=!isset($contract['is_cooperate']) || $contract['is_cooperate'] == '0'?'checked':'';?> style="display: none"></p>
                            </div>
                        </span>
				    </li>
				    <script>
					$(function(){
					    $("#zws_radio_tab .zws_radio_no").live('click',function(){
						var value = $(this).find('input').val();
						if(value == '0'){
						    $("#cooperate_divide").hide();
						    $("#choose_order").hide();
						}else{
						    $("#cooperate_divide").show();
						    $("#choose_order").show();
						}
					    })
					})
				    </script>
				    <?php if($contract['is_cooperate']==0){?>
				    <script>
					$(function(){
					    $("#cooperate_divide").hide();
					    $("#choose_order").hide();
					})
				    </script>
				    <?php }?>
                                    <li id="choose_order">
                                        <span class="zws_border_span ">
					    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1">*</b>合作编号：</p>
					    <div class="input_add_F">
						<input type="text" class="border_color input_add_F zws_border_W110" value="<?=$contract['order_sn'];?>" name="order_sn" autocomplete="off" <?=$contract['order_sn']?'disabled':''?>>
						<button class="zws_border_button input_add_F" type="button" style="height:28px;line-height:28px;" onclick="$('#js_cooperate_box .iframePop').attr('src','/contract/get_cooperate/1');openWin('js_cooperate_box');">选择</button>
						<div  class="zws_block errorBox"></div>
					    </div>
					</span>
                                    </li>
				</ul>
			    </div>
                        </td>
                    </tr>
		</tbody>
	    </table>
        </div>
        <!--卖方信息-->
        <dl class="sale_message">
            <dd class="aad_pop_pB_10">
               <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/saler_03.png" />
                <p>卖方信息</p>
            </dd>
            <dt>
                <div class="aad_pop_p_B10" style="display:inline;">
                      <li>
                        <strong><em class="resut_table_state_1">*</em>业主姓名：</strong>
                        <b>
                        	<input type="text" class="border_color input_add_F zws_W128" value="<?=$contract['owner'];?>" name="owner" autocomplete="off" <?=$contract['house_id']?'disabled':''?>>
                        	<span  class="zws_block errorBox"></span>
                        </b>

                      </li>
                      <li>
                        <strong><em class="resut_table_state_1">*</em>联系方式：</strong>
                        <b>
                        <input type="text" class="border_color input_add_F zws_W128" value="<?=$contract['owner_tel'];?>" name="owner_tel" autocomplete="off" <?=$contract['house_id']?'disabled':''?>>
                        <span  class="zws_block errorBox"></span>
                        </b>

                      </li>
                      <li>
                        <strong><em class="resut_table_state_1"></em>身份证号：</strong>
                        <b>
	                        <input type="text" class="border_color input_add_F zws_W128" value="<?=$contract['owner_idcard'];?>" name="owner_idcard" autocomplete="off" <?=$contract['house_id']?'disabled':''?>>
                        </b>

                      </li>
                </div>
                <div  style="display:inline;">
                      <li>
                        <strong><em class="resut_table_state_1">*</em>签约门店：</strong>
                        <b>
			    <select  class="border_color zws_W128 input_add_F" name="agency_id_a" style="height:28px;line-height:28px;background:#FFF;width:133px;">
				<?php if($agencys_a){foreach($agencys_a as $key =>$val){?>
				<option value="<?=$val['id'];?>" <?=$contract['agency_id_a']==$val['id']?"selected":""?>><?=$val['name'];?></option>
				<?php }}?>
			    </select>
			    <span  class="zws_block errorBox"></span>
			</b>

                      </li>
                      <li>
                        <strong><em class="resut_table_state_1">*</em>签约人：</strong>
                        <b>
			    <select  class="border_color zws_W128 input_add_F" name="broker_id_a" style="height:28px;line-height:28px;background:#FFF;width:133px;">
				<?php if($brokers_a){ foreach($brokers_a as $key=>$val){ ?>
				    <option value='<?=$val['broker_id']?>' <?=$contract['broker_id_a']==$val['broker_id']?"selected":""?>><?=$val['truename']?></option>
				<?php }}?>
			    </select>
			    <span  class="zws_block errorBox"></span>
			</b>
                      </li>
		      <script>
                $("select[name='agency_id_a']").change(function(){
                    var agency_id = $("select[name='agency_id_a']").val();
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
                                    var html = "<option value=''>请选择</option>";
                                    for(var i in data['list']){
                                    html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
                                    }
                                    $("select[name='broker_id_a']").html(html);
                                }else{
                                    $("select[name='broker_id_a']").html("<option value=''>请选择</option>");
                                }
                            }
                        })
                    }else{
                        $("select[name='broker_id_a']").html("<option value=''>请选择</option>");
                    }
                })
                $("select[name='broker_id_a']").change(function(){
                    var broker_id = $("select[name='broker_id_a']").val();
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
                                    $("input[name='broker_tel_a']").val(data['data']['phone']);
                                    $("input[name='broker_tel_a']").attr('disabled','true');
                                }
                            }
                        })
                    }else{
                        $("input[name='broker_tel_a']").val('');
                        $("input[name='broker_tel_a']").removeAttr('disabled');
                    }
                })
		    </script>
                      <li>
                        <strong><em class="resut_table_state_1">*</em>联系方式：</strong>
                        <b>
                        <input type="text" class="border_color input_add_F zws_W128" value="<?=$contract['broker_tel_a'];?>" name="broker_tel_a" autocomplete="off" <?=$contract['broker_tel_a']?'disabled':''?>>
                        	<span  class="zws_block errorBox"></span>

                        </b>

                      </li>
                </div>

            </dt>
        </dl>
        <!--买方信息-->
        <dl class="sale_message">
            <dd  class="aad_pop_pB_10">
               <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/buyer_06.png" />
                <p>买方信息</p>
            </dd>
            <dt>
            <div class="aad_pop_p_B10 input_add_F"  style="display:inline;">
		    <li>
			<strong>客源编号：</strong>
			<b style="width:134px;">
			    <span class="input_add_F" id="zws_choice">
				<input type="text" class="border_color input_add_F zws_border_W85" value="<?=$contract['customer_id'];?>" name="customer_id" autocomplete="off" <?=$contract['customer_id']?'disabled':''?>>
				<button class="zws_border_button" type="button" style="height:28px;line-height:28px;float:left;" onclick="open_customer_pop();">选择</button>
			    </span>
			</b>
		    </li>
		    <li>
                <strong><em class="resut_table_state_1">*</em>客户姓名：</strong>
                <b>
                <input type="text" class="border_color input_add_F zws_W128" value="<?=$contract['customer'];?>" name="customer" autocomplete="off" <?=$contract['customer_id']?'disabled':''?>>
                <span  class="zws_block errorBox"></span>
                </b>
		    </li>
		    <li>
			<strong><em class="resut_table_state_1">*</em>联系方式：</strong>
			<b><input type="text" class="border_color input_add_F zws_W128" value="<?=$contract['customer_tel'];?>" name="customer_tel" autocomplete="off" <?=$contract['customer_id']?'disabled':''?>><span  class="zws_block errorBox"></span></b>

		    </li>
		    <li>
			<strong><em class="resut_table_state_1"></em>身份证号：</strong>
			<b><input type="text" class="border_color input_add_F zws_W128" value="<?=$contract['customer_idcard'];?>" name="customer_idcard" autocomplete="off" <?=$contract['customer_id']?'disabled':''?>><span  class="zws_block errorBox"></span></b>

		    </li>
		</div>
		<div class="aad_pop_p_B20 input_add_F"  style="display:inline;">
		    <li>
			<strong><em class="resut_table_state_1">*</em>签约门店：</strong>
			<b>
			    <select  class="border_color zws_W128 input_add_F" name="agency_id_b" style="height:28px;line-height:28px;background:#FFF;width:133px;">
				<?php if($agencys_b){foreach($agencys_b as $key =>$val){?>
				<option value="<?=$val['id'];?>" <?=$contract['agency_id_b']==$val['id']?"selected":""?>><?=$val['name'];?></option>
				<?php }}?>
			    </select>
			    <span  class="zws_block errorBox"></span>
			</b>

		    </li>
		    <li>
			<strong><em class="resut_table_state_1">*</em>签约人：</strong>
			<b>
			    <select  class="border_color zws_W128 input_add_F" name="broker_id_b" style="height:28px;line-height:28px;background:#FFF;width:133px;">
				<?php if($brokers_b){ foreach($brokers_b as $key=>$val){ ?>
				    <option value='<?=$val['broker_id']?>' <?=$contract['broker_id_b']==$val['broker_id']?"selected":""?>><?=$val['truename']?></option>
				<?php }}?>
			    </select>
			    <span  class="zws_block errorBox"></span>
			</b>

		    </li>
		    <script>
			$("select[name='agency_id_b']").change(function(){
			    var agency_id = $("select[name='agency_id_b']").val();
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
					    var html = "<option value=''>请选择</option>";
					    for(var i in data['list']){
						html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
					    }
					    $("select[name='broker_id_b']").html(html);
					}else{
					    $("select[name='broker_id_b']").html("<option value=''>请选择</option>");
					}
				    }
				})
			    }else{
				$("select[name='broker_id_b']").html("<option value=''>请选择</option>");
			    }
			})
            $("select[name='broker_id_b']").change(function(){
                var broker_id = $("select[name='broker_id_b']").val();
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
                                $("input[name='broker_tel_b']").val(data['data']['phone']);
                                $("input[name='broker_tel_b']").attr('disabled','true');
                            }
                        }
                    })
                }else{
                    $("input[name='broker_tel_b']").val('');
                    $("input[name='broker_tel_b']").removeAttr('disabled');
                }
            })
		    </script>
		    <li>
			<strong><em class="resut_table_state_1">*</em>联系方式：</strong>
			<b><input type="text" class="border_color input_add_F  zws_W128" value="<?=$contract['broker_tel_b'];?>" name="broker_tel_b"  autocomplete="off" <?=$contract['broker_tel_b']?'disabled':''?>>
				<span  class="zws_block errorBox"></span>
			</b>

		    </li>
                </div>
            </dt>
        </dl>
    </div>
</div>
<div class="sale_message_h" style="line-height:1px;"></div>
<div style="clear:both;"></div>
<!--佣金结算-->
    <div class="sale_message_commission" style="margin-bottom:0;">
            <div style="width:100%;clear:both;display:block;">
                <h4 class="h4 padding_size zws_h4_font">付款信息</h4>
	    </div>
	    <div class="sale_message_commission_detial" style="display:block;width:100%;float:left;">
		<div style="display:inline;width:100%;float:left;">
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1">*</em>付款方式：</b>
			<strong class="zws_ip_W150">
			    <select  class="border_color zws_ip_W130 zws_line input_add_F" name="buy_type">
				<?php foreach($contract_config['buy_type_s'] as $key=>$val){?>
				<option value="<?=$key;?>" <?=$contract['buy_type_s']==$key?'selected':''?>><?=$val;?></option>
				<?php }?>
			    </select>
			    <span  class="zws_block errorBox"></span>
			</strong>
		    </p>
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1"></em>客户首付金额：</b>
			<span class="input_add_F">
			<strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F" value="<?=$contract['shoufu'];?>" name="shoufu" autocomplete="off" >&nbsp;元</strong>
			<span  class="zws_block errorBox"></span>
			</span>
		    </p>
		    <p class="aad_pop_p_B10">
				<b class="zws_ip_W100"><em class="resut_table_state_1"></em>客户贷款金额：</b>
				<span class="input_add_F">
				<strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F" value="<?=$contract['loan'];?>" name="loan" autocomplete="off" >&nbsp;元</strong>
				<span  class="zws_block errorBox"></span>
				</span>
		    </p>
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1">*</em>税费支付：</b>
			<strong class="zws_ip_W150">
			    <select  class="border_color zws_ip_W130 zws_line input_add_F" name="tax_pay_type" style="width:126px;">
				<?php foreach($contract_config['tax_pay_type'] as $key=>$val){?>
				<option value="<?=$key;?>" <?=$contract['tax_pay_type']==$key?'selected':''?>><?=$val;?></option>
				<?php }?>
			    </select>
			    <em class="input_add_F">&nbsp;　</em>
			    <span  class="zws_block padd_L errorBox"></span>
			</strong>

		    </p>
                </div>
                <div  style="display:block;width:100%;float:left;">
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1">*</em>营业税：</b>
			<span class="input_add_F" style="width:50%">
			    <em class="zws_radio_no <?=$contract['business_tax'] !== '0'?'yesOn':'';?>">有<input type="radio" value="1" name="business_tax" <?=$contract['business_tax'] !== '0'?'checked':'';?> style="display: none"></em>
			    <em class="zws_radio_no <?=$contract['business_tax'] == '0'?'yesOn':'';?>">无<input type="radio" value="0" name="business_tax" <?=$contract['business_tax'] == '0'?'checked':'';?> style="display: none"></em>
			</span>
		    </p>
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1">*</em>个税：</b>
			<span class="input_add_F" style="width:50%">
			    <em class="zws_radio_no <?=$contract['tax'] !== '0'?'yesOn':'';?>">有<input type="radio" value="1" name="tax" <?=$contract['tax'] !== '0'?'yesOn':'';?> style="display: none"></em>
			    <em class="zws_radio_no <?=$contract['tax'] == '0'?'yesOn':'';?>">无<input type="radio" value="0" name="tax" <?=$contract['tax'] == '0'?'yesOn':'';?> style="display: none"></em>
			</span>
		    </p>
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1"></em>业主税费合计：</b>
			<strong class="zws_ip_W150">
			    <span class="input_add_F">
				<input type="text" class="border_color zws_ip_W130 input_add_F" value="<?=$contract['owner_tax_total'];?>" name="owner_tax_total" autocomplete="off" ><em class="input_add_F">&nbsp;元</em>
			    </span>
			</strong>
		    </p>
			<p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1"></em>客户税费合计：</b>
			<strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F" value="<?=$contract['customer_tax_total'];?>" name="customer_tax_total" autocomplete="off" ><em class="input_add_F">&nbsp;元</em></strong>
		    </p>
		</div>
		<div style="display:block;width:100%;float:left;">
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1">*</em>业主应付佣金：</b>
			<strong class="zws_ip_W150">
			    <span class="input_add_F">
				<input type="text" class="border_color zws_ip_W130 input_add_F" value="<?=$contract['owner_commission'];?>" name="owner_commission" autocomplete="off" onkeyup="total();">
				<em class="input_add_F">&nbsp;元</em>
				<span  class="zws_block padd_L errorBox" style="text-indent:0;padding-left:0;"></span>
			    </span>
			</strong>
		    </p>
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1">*</em>客户应付佣金：</b>
			<strong class="zws_ip_W150">
			    <span class="input_add_F">
				<input type="text" class="border_color zws_ip_W130 input_add_F"  value="<?=$contract['customer_commission'];?>" name="customer_commission" autocomplete="off" onkeyup="total();">
				<em class="input_add_F">&nbsp;元</em>
				<span  class="zws_block padd_L errorBox" style="text-indent:0;padding-left:0;"></span>
			    </span>
			</strong>
		    </p>
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1">*</em>其他收入：</b>
			<strong class="zws_ip_W150">
			    <span class="input_add_F">
				<input type="text" class="border_color zws_ip_W130 input_add_F" value="<?=$contract['other_income'];?>" name="other_income" autocomplete="off" onkeyup="total();">
				<em class="input_add_F">&nbsp;元</em>
				<span  class="zws_block errorBox" style="text-indent:0;padding-left:0;"></span>
			    </span>
			</strong>
		    </p>
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1">*</em>佣金总计收入：</b>
			<strong class="zws_ip_W150">
			    <span class="input_add_F">
                    <input type="text" class="border_color zws_ip_W130 input_add_F zws_color_red" value="<?=$contract['commission_total'];?>" name="commission_total" disabled>
				<em class="input_add_F">&nbsp;元</em>
				<span  class="zws_block errorBox" style="text-indent:0;padding-left:0;"></span>
			    </span>
			</strong>
		    </p>
		    <script>
			function total(){
			    var money1 = $("input[name='owner_commission']").val();
			    var money2 = $("input[name='customer_commission']").val();
			    var money3 = $("input[name='other_income']").val();
                var total = 0;
                if(!parseFloat(money1) && !parseFloat(money2) && !parseFloat(money3)){
                     total = 0;
                }else{
                    if(parseFloat(money1)){
                        total =total + parseFloat(money1);
                    }
                    if(parseFloat(money2)){
                        total =total + parseFloat(money2);
                    }
                    if(parseFloat(money3)){
                        total =total + parseFloat(money3);
                    }
                }

			    $("input[name='commission_total']").val(total.toFixed(2));
			}
		    </script>

		</div>
		<div id="cooperate_divide">
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1"></em>合作分佣比例：</b>
			<strong class="zws_ip_W150">
			    <span class="input_add_F">
				<input type="text" class="border_color zws_ip_W130 input_add_F" value="<?=$contract['divide_percent'];?>" name="divide_percent" autocomplete="off" onkeyup="total1();">
				<em class="input_add_F">&nbsp;%</em>
				<span  class="zws_block errorBox" style="text-indent:0;padding-left:0;"></span>
			    </span>
			</strong>
		    </p>
		    <p class="aad_pop_p_B10">
			<b class="zws_ip_W100"><em class="resut_table_state_1"></em>合作分佣金额：</b>
			<strong class="zws_ip_W150">
			    <span class="input_add_F">
                    <input type="text" class="border_color zws_ip_W130 input_add_F" value="<?=$contract['divide_money'];?>" name="divide_money" disabled>
				<em class="input_add_F">&nbsp;元</em>
				<span  class="zws_block errorBox" style="text-indent:0;padding-left:0;"></span>
			    </span>
			</strong>
		    </p>
		    </div>
            <script>
			function total1(){
                var total = $("input[name='commission_total']").val();
			    var percent = $("input[name='divide_percent']").val();
			    var money = '';
                if(!parseFloat(total) || !parseFloat(percent)){
                    money = '';
                }else{
                    money = parseFloat(total*percent/100);
                    money = money.toFixed(2);
                }
			    $("input[name='divide_money']").val(money);
			}
		    </script>
                <dl>
                      <dd>合同备注：</dd>
                      <dt><textarea class="zws_textarea" name="remarks"><?=$contract['remarks'];?></textarea></dt>
                </dl>
           </div>

        </div>
 <!--保存和确认-->
	<div  style="padding-top:10px;clear: both;">
	  <table width="100%">
	    <tr>
	      <td class="zws_center">
		  <?php if($id){?>
		  <input type="hidden" name="contract_id" value="<?=$id?>">
		 <input type="hidden" name="submit_flag" value="modify">
		  <?php }else{?>
		 <input type="hidden" name="submit_flag" value="add">
		  <?php }?>
		 <button type="submit" class="btn-lv1 btn-left">保存</button>
		 <button type="button" class="btn-hui1" onclick="history.go(-1);">取消</button>
	     </td>
	    </tr>
	  </table>
	</div>
    </form>
</div>
</div>

<!--房源选择弹框-->
<div id="js_house_box" class="iframePopBox" style="width: 980px;height:575px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="980" height="575px" class='iframePop' src=""></iframe>
</div>

<!--客源选择弹框-->
<div id="js_customer_box" class="iframePopBox" style="width: 980px;height:575px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="980" height="575px" class='iframePop' src=""></iframe>
</div>

<!--合作选择弹框-->
<div id="js_cooperate_box" class="iframePopBox" style="width: 980px;height:575px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="980" height="575px" class='iframePop' src=""></iframe>
</div>

<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="location.href='/contract/contract_list/<?=$type;?>';return false;"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt1"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button" onclick="location.href='/contract/contract_list/<?=$type;?>';return false;">确定</button>
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
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>
<script type="text/javascript">


$(function () {
    function re_width(){
      var h1 = $(window).height();
      var w1 = $(window).width() - 180;
      $(".tab-left, .forms_scroll").height(h1-35);
      $(".forms_scroll").width(w1).show();
    };
    re_width();
    $(window).resize(function(e) {
      re_width();
      $(".sale_message dt").css("width",($(".sale_message").width()-100-36)+"px");
    });


    $(".sale_message dt").css("width",($(".sale_message").width()-100-36)+"px");
     //items   table   隔行换色
    //房源地址输入框宽度
    $(".zws_W60").css("width",($("#zws_first_tr").width()+$("#zws_num").width()-$(this).find(".border_input_title").width())+"px");

    $(".input_add_F").children().click(function(){
        $(this).siblings().removeClass("yesOn");
        $(this).addClass("yesOn");
	$(this).parent().find("input").attr('checked',false);
	$(this).find("input").attr('checked',true);
    })


    $("#zws_choice").css("width",$("#zws_input_w").width()+"px");


});

$(window).resize(function(){
  //房源地址输入框宽度
    $(".zws_W60").css("width",($("#zws_first_tr").width()+$("#zws_num").width()-$(this).find(".border_input_title").width())+"px");

    $("#zws_choice").css("width",$("#zws_input_w").width()+"px");

})

    function open_house_pop(){
        var house_id = $("input[name='house_id']").val();
        $("#js_house_box .iframePop").attr('src','/contract/get_house/1/'+house_id);
        openWin('js_house_box');
    }

    function get_info(id){
        closeWindowWin('js_house_box');
        if(id){
            $.post(
                '/contract/get_info',
                {'id':id,
                 'type':1
                },
                function(data){
                    $("input[name='block_id']").val(data['block_id']);
                    $("input[name='block_name']").val(data['block_name']);
                    $("input[name='house_addr']").val(data['address']+data['dong']+'栋'+data['unit']+'单元'+data['door']+'室');
                    $("input[name='house_id']").val(data['house_id']);
                    $("select[name='sell_type']").val(data['sell_type']);
                    $("input[name='buildarea']").val(data['buildarea']);
                    $("input[name='owner']").val(data['owner']);
                    $("input[name='owner_tel']").val(data['telno1']);
                    $("input[name='owner_idcard']").val(data['idcare']);
                    $("input[name='block_name']").attr('disabled','true');
                    $("input[name='house_addr']").attr('disabled','true');
                    $("input[name='house_id']").attr('disabled','true');
                    $("select[name='sell_type']").attr('disabled','true');
                    $("input[name='buildarea']").attr('disabled','true');
                    $("input[name='owner_tel']").attr('disabled','true');
                    $("input[name='owner']").attr('disabled','true');
                    $("input[name='owner_idcard']").attr('disabled','true');
                },'json'
            );
        }else{
            $("input[name='block_id']").val('');
            $("input[name='block_name']").val('');
            $("input[name='house_addr']").val('');
            $("input[name='house_id']").val('');
            $("select[name='sell_type']").val('');
            $("input[name='buildarea']").val('');
            $("input[name='owner']").val('');
            $("input[name='owner_tel']").val('');
            $("input[name='owner_idcard']").val('');
            $("input[name='block_name']").removeAttr('disabled');
            $("input[name='house_addr']").removeAttr('disabled');
            $("input[name='house_id']").removeAttr('disabled');
            $("select[name='sell_type']").removeAttr('disabled');
            $("input[name='buildarea']").removeAttr('disabled');
            $("input[name='owner_tel']").removeAttr('disabled');
            $("input[name='owner']").removeAttr('disabled');
            $("input[name='owner_idcard']").removeAttr('disabled');
        }
    }

    function open_customer_pop(){
        var customer_id = $("input[name='customer_id']").val();
        $('#js_customer_box .iframePop').attr('src','/contract/get_customer/1/'+customer_id);
        openWin('js_customer_box');
    }

    function get_customer_info(id){
        closeWindowWin('js_customer_box');
        if(id){
            $.post(
                '/contract/get_customer_info',
                {'id':id,
                 'type':1
                },
                function(data){
                    $("input[name='customer_id']").val(data['customer_id']);
                    $("input[name='customer']").val(data['truename']);
                    $("input[name='customer_tel']").val(data['telno1']);
                    $("input[name='customer_idcard']").val(data['idno']);
                    $("input[name='customer_id']").attr('disabled','true');
                    $("input[name='customer']").attr('disabled','true');
                    $("input[name='customer_tel']").attr('disabled','true');
                    $("input[name='customer_idcard']").attr('disabled','true');
                },'json'
            );
        }else{
            $("input[name='customer_id']").val('');
            $("input[name='customer']").val('');
            $("input[name='customer_tel']").val('');
            $("input[name='customer_idcard']").val('');
            $("input[name='customer_id']").removeAttr('disabled');
            $("input[name='customer']").removeAttr('disabled');
            $("input[name='customer_tel']").removeAttr('disabled');
            $("input[name='customer_idcard']").removeAttr('disabled');
        }
    }

    function open_cooperate_pop(){
        var order_sn = $("input[name='order_sn']").val();
        $('#js_cooperate_box .iframePop').attr('src','/contract/get_cooperate/1/'+order_sn);
        openWin('js_cooperate_box');
    }

    function get_cooperate_info(id){
        closeWindowWin('js_cooperate_box');
        if(id){
            $.post(
                '/contract/get_cooperate_info',
                {'id':id
                },
                function(data){
                    $("input[name='order_sn']").val(data['order_sn']);
                    $("input[name='order_sn']").attr('disabled','true');
                },'json'
            );
        }else{
            $("input[name='order_sn']").val('');
            $("input[name='order_sn']").removeAttr('disabled');
        }
    }
</script>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js"></script>
