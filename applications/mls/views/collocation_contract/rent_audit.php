<div class="contract-wrap clearfix">
	<div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll2">
		<form name="search_form" id="search_form" method="post" action="">
			<!-- 上部菜单选项，按钮-->
			<div class="search_box clearfix" style="margin-top:0;overflow: hidden" id="js_search_box_02">
				<div class="fg_box">
					<p class="fg fg_tex">出租合同编号：</p>
					<div class="fg">
						<input type="text" name='collo_rent_id' value="<?php echo $post_param['collo_rent_id']?>" class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">状态：</p>

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
				<div class="fg_box">
					<p class="fg fg_tex">签约门店：</p>
					<div class="fg">
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
					<div class="fg">
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
								//var broker_id = <?=$post_param['broker_id']?>;
								var html = "";
								for(var i in data['list']){
									//if(data['list'][i]['broker_id'] == broker_id){
										//$('.broker"'+data['list'][i]['broker_id']+'"').attr('selected',true);
									//}
									html+="<option value='"+data['list'][i]['broker_id']+"' class='broker"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
								}
								$('#broker_id').html(html);
							}
							}
						})
						}else{
						$('#broker_id').html("<option value=''>请选择</option>");
						}
					});
				</script>
				<div class="fg_box">
					<p class="fg fg_tex">签约时间：</p>
					<div class="fg">
						<input type="text" class="fg-time" name='start_time' value="<?=$post_param['start_time'];?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
					</div>
					<div class="fg fg_tex03">—</div>
					<div class="fg fg_tex03">
					<input type="text" class="fg-time" name='end_time' value="<?=$post_param['end_time'];?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
					</div>
				</div>
				<div class="fg_box">
					<input type="hidden" name="orderby_id" id="orderby_id" value="">
					<div class="fg">
						<a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;"><span  class="btn_inner">搜索</span></a>
					</div>
					<div class="fg"> <a href="/contract_audit/rent_contract_audit/" class="reset">重置</a> </div>
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
			<div class="contract-crumbs clearfix" id="js_fun_btn2">
				<p>共有<strong class="ff7800"><?=$total;?></strong>条待审核合同</p>
			</div>
			<div class="table_all">
				<div class="title shop_title" id="js_title" style="padding-right:0;">
					<table class="table">
						<tr>
							<td class="c9">托管合同编号</td>
							<td class="c9">出租合同编号</td>
							<td class="c20">房源地址</td>
							<td class="c9">客户</td>
							<td class="c9">租金（元/月）</td>
							<td class="c6">出租时长</td>
							<td class="c9">签约门店</td>
							<td class="c9">签约人</td>
							<td class="c9">状态</td>
							<td>操作</td>
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
							<td class="c9" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['c_id']?>/4';return false;"><div class="info c227ac6"><?=$val['collocation_id']?></div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['r_id']?>/1';return false;"><div class="info c227ac6"><?=$val['collo_rent_id']?></div></td>
							<td class="c20"><div class="info"><?=$val['houses_address']?></div></td>
							<td class="c9"><div class="info"><?=$val['customer_name']?></div></td>
							<td class="c9"><div class="info"><?=strip_end_0($val['rental'])?></div></td>
							<td class="c6"><div class="info"><?=$val['rent_total_month']?></div></td>
							<td class="c9"><div class="info"><?=$val['agency_name']?></div></td>
							<td class="c9"><div class="info"><?=$val['broker_name']?></div></td>
							<td class="c9">
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
								<?php if($val['status'] == 1){?>
									<?php if (isset($auth['audit']['auth']) && $auth['audit']['auth']) { ?>
										<a href="javascript:void(0)" onclick="rent_audit('<?=$val['id']?>',1,'<?=$val['c_id']?>')">审核</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">审核</a>
									<?php }?>
								<?php }elseif($val['status'] == 2){?>
									<?php if (isset($auth['turn_audit']['auth']) && $auth['turn_audit']['auth']) { ?>
										<a href="javascript:void(0)" onclick="rent_audit('<?=$val['id']?>',2,'<?=$val['c_id']?>')">反审核</a>
									<?php }else{?>
										<a href="javascript:void(0)" onclick="permission_none();">反审核</a>
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
			<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
				<div class="get_page">
					<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
				</div>
			</div>
		</form>
	</div>
</div>

<!--审核弹窗-->
<div class="pop_box_g" id="js_pop_add_attendance_kq" style="width:300px; height:230px; display: none;background:#FFF;">
    <div class="hd header">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="reclaim-mod" style="padding: 20px 0 0 15px;">
        <form action="#" method="post">
            <table>
				<tr>
					<td width="70" class="label"><font class="red">*</font>审核结果：</td>
                    <td>
                    	<div class="input_add_F" style="padding-right:10px;"><input type="radio" value="1" name="audit_end">通过</div>
                    	<div class="input_add_F"><input type="radio" value="2" name="audit_end">不通过</div>
                    </td>
				</tr>
                <tr>
					<td width="70" class="label">审核结果：</td>
                    <td>
                    	<textarea style="background:#fcfcfc;width:180px;height:70px;border:1px solid #e6e6e6;" name='audit_view' id='audit_view'></textarea>
                    </td>
                </tr>
                <tr>
                	<td colspan="2" class="center">

						<button type="button" id="dialog_share" class="btn-lv1 btn-left JS_Close">确定</button>
						<button type="button" class="btn-hui1 JS_Close">取消</button>

                	</td>
                </tr>
			</table>

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
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->

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
	//审核
	function rent_audit(id,type,c_id){
		if(type == 1){//审核
			openWin('js_pop_add_attendance_kq');
			$('#dialog_share').click(function(){
				var audit_end = $("input[name='audit_end']:checked").val();
				var audit_view = $('#audit_view').val();
				if(audit_end){
					$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 审核完成后合同将不可修改，是否确认当前操作？');
					openWin('jss_pop_tip');
					$('#dialog_share2').click(function(){
						$.ajax({
							url: "<?php echo MLS_URL;?>/collocation_contract/pay_audit/",
							type: "GET",
							dataType: "json",
							data: {
								id:id,
								c_id:c_id,
								audit_end:audit_end,
								audit_view:audit_view,
								type:type,
								tab:'4'
							},
							success: function(data) {
								if(data == 'ok')
								{
									openWin('js_pop_msg1');
									$("#dialog_do_itp").html('审核通过');
									location.reload();
								}else if (data == 'no'){
									openWin('js_pop_msg1');
									$("#dialog_do_itp").html('审核不通过');
									location.reload();
								}
								else if(data['errorCode'] == '403')//没有权限
								{
									permission_none();
								}
							}
						});
					});
				}else{
					$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 请选择是否通过审核');
					openWin('jss_pop_tip');return false;
				}
			});
		}else{//反审核
			$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt="">当前合同已生效，反审核后合同将可修改是否确认操作？');
			openWin('jss_pop_tip');
			$('#dialog_share2').click(function(){
				$.ajax({
					url: "<?php echo MLS_URL;?>/collocation_contract/pay_audit/",
					type: "GET",
					dataType: "json",
					data: {
						id:id,
						type:type,
						tab:'4'
					},
					success: function(data) {
						if(data == 'ok')
						{
							openWin('js_pop_msg1');
							$("#dialog_do_itp").html('反审核操作成功');
							location.reload();
						}
						else if(data['errorCode'] == '403')//没有权限
						{
							permission_none();
						}
					}
				});
			});
		}
	}
</script>
