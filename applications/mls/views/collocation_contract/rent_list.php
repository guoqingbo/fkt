<div class="contract-wrap clearfix">
    <div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll2">
		<form name="search_form" id="search_form" method="post" action="">
			<!-- 上部菜单选项，按钮-->
			<div class="search_box clearfix"  id="js_search_box_02">
				<div class="fg_box">
					<p class="fg fg_tex">托管合同编号：</p>
					<div class="fg">
						<input type="text" value="<?php echo $post_param['collocation_id']?>" name='collocation_id' class="input w90 ui-autocomplete-input" autocomplete="off">
						<input type='hidden' name='c_id' id='c_id' value=''/>
					</div>
				</div>
				<div class="fg_box" >
					<p class="fg fg_tex">出租合同编号：</p>
					<div class="fg">
						<input type="text" value="<?php echo $post_param['collo_rent_id']?>" name='collo_rent_id' class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">楼盘名称：</p>
					<div class="fg">
						<input type="text" name="block_name" id="block_name" value="<?php echo $post_param['block_name']?>" class="input w90 ui-autocomplete-input" autocomplete="off">
						<input name="block_id" id="block_id" value="<?php echo $post_param['block_id']?>" type="hidden">
					</div>
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
				<div class="fg_box" >
					<p class="fg fg_tex">客户姓名：</p>
					<div class="fg">
						<input type="text" value="<?=$post_param['customer_name']?>" name='customer_name' id='customer_name' class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box" >
						<p class="fg fg_tex mr10">
							<select class="select w80" name='search_where'>
								<option value='0'>选择时间</option>
								<option value='rent_start_time' <?php if((!empty($post_param['search_where']) && $post_param['search_where'] == 'rent_start_time')){echo 'selected="selected"';}?>>出租开始时间</option>
								<option value='rent_end_time' <?php if((!empty($post_param['search_where']) && $post_param['search_where'] == 'rent_end_time')){echo 'selected="selected"';}?>>出租结束时间</option>
								<option value='signing_time' <?php if((!empty($post_param['search_where']) && $post_param['search_where'] == 'signing_time')){echo 'selected="selected"';}?>>签约时间</option>
							</select>
						</p>
						<div class="fg">
							<input type="text" name="time_s" class="fg-time" value="<?php if(isset($_POST['time_s'])) {echo $_POST['time_s'];}?>" onClick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
						</div>
						<div class="fg fg_tex03">—</div>
						<div class="fg fg_tex03">
							<input type="text" name="time_e" class="fg-time" value="<?php if(isset($_POST['time_e'])) {echo $_POST['time_e'];}?>" onClick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
						</div>

				</div>
				<div class="fg_box" >
					<p class="fg fg_tex">付款方式：</p>
					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name='pay_type'>
						<option value='0' <?php if(isset($post_param['pay_type'])){echo "selected";}?>>请选择</option>
						<option value='1' <?php if($post_param['pay_type']=='1'){echo "selected";}?>>月付</option>
						<option value='2' <?php if($post_param['pay_type']=='2'){echo "selected";}?>>季付</option>
						<option value='3' <?php if($post_param['pay_type']=='3'){echo "selected";}?>>半年付</option>
						<option value='4' <?php if($post_param['pay_type']=='4'){echo "selected";}?>>年付</option>
						<option value='5' <?php if($post_param['pay_type']=='5'){echo "selected";}?>>其他</option>
						</select>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">签约门店：</p>
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
					<p class="fg fg_tex">签约人：</p>
					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name="broker_id" id="broker_id">
							<?php if (is_full_array($post_config['brokers'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if ($val['broker_id'] == $post_param['broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php
                                    }
                                }
                            ?>
						</select>
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
			})
		    </script>
				<div class="fg_box" >
					<p class="fg fg_tex">合同状态：</p>
					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name='status'>
						<option value='0' <?php if(isset($post_param['status'])){echo "selected";}?>>请选择</option>
						<option value='1' <?php if($post_param['status']=='1'){echo "selected";}?>>待审核</option>
						<option value='2' <?php if($post_param['status']=='2'){echo "selected";}?>>生效</option>
						<option value='3' <?php if($post_param['status']=='3'){echo "selected";}?>>终止</option>
						<option value='4' <?php if($post_param['status']=='4'){echo "selected";}?>>审核不通过</option>
						</select>
					</div>
				</div>
				<div class="fg_box" >
					<input type="hidden" name="pg" value="1">
					<input type="hidden" name="orderby_id" id="orderby_id" value="">
					<div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;"><span  class="btn_inner">搜索</span></a> </div>
					<div class="fg"> <a href="/collocation_contract/export/3/4" class="btn"><span class="btn_inner">导出</span></a></div>
					<div class="fg"> <a href="/collocation_contract/rent_contract_list/" class="reset">重置</a> </div>
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

		</form>
        <!--1-->
			<div class="contract-crumbs clearfix" id="js_fun_btn2">
			<?php if (isset($auth['add']['auth']) && $auth['add']['auth']) { ?>
				<a href="/collocation_contract/add_rent_contract/" class="btn-lv fl mt7"><span>+ 新增托管出租合同</span></a>
			<?php }else{?>
				<a href="#" class="btn-lv" onclick="permission_none();"><span>+ 新增托管出租合同</span></a>
			<?php }?>
			</div>

            <!--2-->
			<div class="table_all">
				<div class="title shop_title" id="js_title">
					<table class="table">
						<tr>
							<td class="c8">托管编号</td>
							<td class="c8">出租合同编号</td>
							<td class="c8">租客姓名</td>
							<td class="c8">租金（元/月）</td>
							<td class="c8">付款方式</td>
							<td class="c8">起租时间</td>
							<td class="c8">停租时间</td>
							<td class="c8">签约时间</td>
							<td class="c8">签约门店</td>
							<td class="c8">签约人</td>
							<td class="c8">合同状态</td>
							<td>操作 </td>
						</tr>
					</table>
				</div>
				<div class="inner shop_inner" id="js_inner">
					<table class="table">
					<?php
						if($list){
							foreach($list as $val){
					?>
						<tr>
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;"><div class="info c227ac6"><?=$val['collocation_id']?></div></td>
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;"><div class="info c227ac6"><?=$val['collo_rent_id']?></div></td>
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?=$val['customer_name']?></div></td>
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?=strip_end_0($val['rental'])?></div></td>
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;">
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
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?php echo date('Y-m-d',$val['rent_start_time'])?></div></td>
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?php echo date('Y-m-d',$val['rent_end_time'])?></div></td>
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?php echo date('Y-m-d',$val['signing_time'])?></div></td>
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?=$val['agency_name']?></div></td>
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?=$val['broker_name']?></div></td>
							<td class="c8"  onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['id']?>/1';return false;">
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
							<td style="border-left:1px dotted #EDEDED">
							<?php if($val['status'] !=2 ){?>
								<?php if (isset($auth['edit']['auth']) && $auth['edit']['auth']) { ?>
									<a href="/collocation_contract/rent_modify/<?=$val['id']?>">修改</a>
								<?php }else{?>
									<a href="javascript:void(0)" onclick="permission_none();">修改</a>
								<?php }?>
									<span style="margin:0 5px;color:#b2b2b2;">|</span>
								<?php if (isset($auth['delete']['auth']) && $auth['delete']['auth']) { ?>
									<a href="javascript:void(0)" onclick="del_rent_contract('<?=$val['id']?>',4,'<?=$val['c_id']?>');">删除</a>
								<?php }else{?>
									<a href="javascript:void(0)" onclick="permission_none();">删除</a>
								<?php }?>
							<?php }else{?>
								<?php if (isset($auth['cancel']['auth']) && $auth['cancel']['auth']) { ?>
									<a href="javascript:void(0)" onclick="rent_contract_cancel('<?=$val['id']?>','<?=$val['c_id']?>')">作废</a>
								<?php }else{?>
									<a href="javascript:void(0)" onclick="permission_none();">作废</a>
								<?php }?>
							<?php }?>
							</td>
						</tr>
					<?php }}else{?>
						<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
					<?php }?>
					</table>
				</div>
			</div>

            <!--3-->
			<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
				<div class="get_page">
					<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
				</div>
			</div>

	</div>
</div></div>
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
<script>
//删除
	function del_rent_contract(id,tab,c_id){
		$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 合同删除后不可恢复，是否确认删除？');
		openWin('jss_pop_tip');
		$("#dialog_share2").click(function(){
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/del_need_pay/",
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
						$("#dialog_do_itp").html('合同已删除');
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
