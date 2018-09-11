<div class="contract-wrap clearfix">
	<div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll2">
		<div class="shop_tab_title scr_clear" id="js_search_box">
			<a href="/collocation_contract/receive_customer_audit/1" class="link <?php if($tag == 1){?> link_on <?php }?>"><span class="iconfont hide"></span>应付业主</a>
			<a href="/collocation_contract/receive_customer_audit/2" class="link <?php if($tag == 2){?> link_on <?php }?>"><span class="iconfont hide"></span>实付业主</a>
		</div>

		<form name="search_form" id="search_form" method="post" action="">
			<!-- 上部菜单选项，按钮-->
			<div class="search_box clearfix" id="js_search_box_02">
				<div class="fg_box">
					<p class="fg fg_tex">托管合同编号：</p>
					<div class="fg">
						<input type="text" value="<?php echo $post_param['collocation_id']?>" name='collocation_id' class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">状态：</p>

					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name='status'>
						<option value='0' <?php if(isset($post_param['status'])){echo "selected";}?>>请选择</option>
						<option value='1' <?php if($post_param['status']=='1'){echo "selected";}?>>待审核</option>
						<option value='2' <?php if($post_param['status']=='2'){echo "selected";}?>>审核通过</option>
						<option value='3' <?php if($post_param['status']=='3'){echo "selected";}?>>审核不通过</option>
						</select>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">录入门店：</p>
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
					<p class="fg fg_tex">录入人：</p>
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
			})
			</script>
				<div class="fg_box">
					<p class="fg fg_tex">录入时间：</p>
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
					<div class="fg"> <a href="/contract_audit/receive_customer_audit/" class="reset">重置</a> </div>
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
				<p>共有<strong class="ff7800"><?php if($tag == 1){echo $wait_audit_1;}else{echo $wait_audit_2;}?></strong>条待审核<?php if($tag == 1){?>应收客户<?php }else{?>实收客户<?php }?></p>
			</div>
			<div class="table_all">
				<div class="title shop_title" id="js_title" >
					<table class="table">
						<tr>
							<td class="c5"><?php if($tag == 1){?>应收时间<?php }else{?>实收时间<?php }?></td>
							<td class="c5" style="width:9%;">托管<br>合同编号</td>
							<td class="c5" style="width:9%;">出租<br>合同编号</td>
							<td class="c5">租金</td>
							<td class="c4">水费</td>
							<td class="c4">电费</td>
							<td class="c4">燃气费</td>
							<td class="c4">网费</td>
							<td class="c5">电视费</td>
							<td class="c4">物业费</td>
							<td class="c4">维护费</td>
							<td class="c4">垃圾费</td>
							<td class="c4">杂费</td>
							<td class="c5">合计</td>
							<?php if($tag == 2){?>
							<td class="c4">单据号</td>
							<?php }?>
							<td class="c10" style="width:11%;">录入门店</td>
							<td class="c4">录入人</td>
							<td class="c5">录入日期</td>
							<td class="c5">状态</td>
							<td>操作</td>
						</tr>
					</table>
				</div>
				<div class="inner shop_inner" id="js_inner">
					<table class="table" style="+width:98.5%;_width:98.5%;">
					<?php
						if($list){
							foreach($list as $val){
					?>
						<tr>

							<td class="c5"><div class="info"><?php if($tag ==1){echo date('Y-m-d',$val['need_receive_time']);}else{echo date('Y-m-d',$val['actual_receive_time']);}?></div></td>
							<td class="c5" style="width:9%;"><div class="info"><?=$val['collocation_id']?></div></td>
							<td class="c5" style="width:9%;" onclick = "window.location.href='/collocation_contract/rent_contract_detail/<?=$val['r_id']?>/<?=$tag?>';return false;"><div class="info c227ac6"><?=$val['collo_rent_id']?></div></td>
							<td class="c4"><div class="info c227ac6"><?=strip_end_0($val['rental'])?></div></td>
							<td class="c4"><div class="info"><?=strip_end_0($val['water_fee'])?></div></td>
							<td class="c4"><div class="info"><?=strip_end_0($val['ele_fee'])?></div></td>
							<td class="c4"><div class="info"><?=strip_end_0($val['gas_fee'])?></div></td>
							<td class="c4"><div class="info"><?=strip_end_0($val['int_fee'])?></div></td>
							<td class="c5"><div class="info"><?=strip_end_0($val['tv_fee'])?></div></td>
							<td class="c4"><div class="info"><?=strip_end_0($val['property_fee'])?></div></td>
							<td class="c4"><div class="info"><?=strip_end_0($val['preserve_fee'])?></div></td>
							<td class="c4"><div class="info"><?=strip_end_0($val['garbage_fee'])?></div></td>
							<td class="c4"><div class="info"><?=strip_end_0($val['other_fee'])?></div></td>
							<td class="c5"><div class="info"><?=strip_end_0($val['total_fee'])?></div></td>
							<?php if($tag == 2){?>
							<td class="c4"><div class="info"><?=$val['receipts_num']?></div></td>
							<?php }?>
							<td class="c10" style="width:11%;"><div class="info"><?=$val['agency_name']?></div></td>
							<td class="c4"><div class="info"><?=$val['broker_name']?></div></td>
							<td class="c5"><div class="info"><?php echo date('Y-m-d',$val['create_time'])?></div></td>
							<td class="c5">
								<div class="info c999">
									<?php if($val['status'] == 1){?>
										待审核
									<?php }elseif($val['status'] == 2){?>
										审核通过
									<?php }else{?>
										审核不通过
									<?php }?>
								</div>
							</td>
							<td>
							<?php if($val['status'] == 1){?>
								<?php if (isset($auth['audit']['auth']) && $auth['audit']['auth']) { ?>
									<a href="javascript:void(0)" onclick="receive_customer_audit('<?=$val['id']?>',1,'<?=$val['r_id']?>')">通过</a>
									<span style="margin:0 5px;color:#b2b2b2;">|</span>
									<a href="javascript:void(0)" onclick="receive_customer_audit('<?=$val['id']?>',2,'<?=$val['r_id']?>')">拒绝</a>
								<?php }else{?>
									<a href="javascript:void(0)" onclick="permission_none();">通过</a>
									<span style="margin:0 5px;color:#b2b2b2;">|</span>
									<a href="javascript:void(0)" onclick="permission_none();">拒绝</a>
								<?php }?>
							<?php }elseif($val['status'] == 2){?>
								<?php if (isset($auth['turn_audit']['auth']) && $auth['turn_audit']['auth']) { ?>
									<a href="javascript:void(0)" onclick="receive_customer_audit('<?=$val['id']?>',3,'<?=$val['r_id']?>')">反审核</a>
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
	//审核
	function receive_customer_audit(id,type,r_id){
		var tag = <?=$tag?>;
		if(type == 3){//反审核
			$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 反审核后当前信息将可修改是否确认操作？');
		}else if(type == 1){//通过
			if(tag == 1){//应收
				$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 审核完成后应收客户信息将不可修改是否确定当前操作？');
			}else{//实收
				$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 审核完成后实收客户信息将不可修改是否确定当前操作？');
			}
		}else if(type == 2){//拒绝
			$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 是否确认拒绝？');
		}
		openWin('jss_pop_tip');
		$('#dialog_share2').click(function(){
			$.ajax({
				url: "<?php echo MLS_URL;?>/collocation_contract/receive_audit/",
				type: "GET",
				dataType: "json",
				data: {
					id:id,
					type:type,
					tag:tag,
					r_id:r_id
				},
				success: function(data) {
					if(data == 'ok1')
					{
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('审核通过');
						location.reload();
					}else if(data == 'ok2'){
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('审核不通过');
						location.reload();
					}else if(data == 'ok3'){
						openWin('js_pop_msg1');
						$("#dialog_do_itp").html('反审核操作成功');
						location.reload();
					}else if(data['errorCode'] == '403')//没有权限
					{
						permission_none();
					}
				}
			});
		});
	}
</script>
