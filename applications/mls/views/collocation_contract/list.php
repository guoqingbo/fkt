<!--<script>
    window.parent.addNavClass(17);
</script>-->
<div class="contract-wrap clearfix">
	<div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll2">
		<form name="search_form" id="search_form" method="post" action="">
			<!-- 上部菜单选项，按钮-->
			<div class="search_box clearfix" style="margin-top:0;overflow: hidden" id="js_search_box_02">
				<div class="fg_box">
					<p class="fg fg_tex">托管编号：</p>
					<div class="fg">
						<input type="text" name='collocation_id' value="<?php echo $post_param['collocation_id']?>" class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">房源编号：</p>
					<div class="fg">
						<input type="text" name='house_id' value="<?php echo $post_param['house_id']?>" class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">楼盘名称：</p>
					<div class="fg">
						<input type="text" name="block_name" id="block_name" value="<?php echo $post_param['block_name']?>" class="input w120 ui-autocomplete-input" autocomplete="off"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
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
				<div class="fg_box">
					<p class="fg fg_tex">业主姓名：</p>
					<div class="fg">
						<input type="text" name="owner" id="owner" value="" class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box">
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
				<div class="fg_box">
					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name='search_where'>
							<option value="0">选择日期</option>
							<option value='collo_start_time' <?php if((!empty($post_param['search_where']) && $post_param['search_where'] == 'collo_start_time')){echo 'selected="selected"';}?>>托管开始时间</option>
							<option value='collo_end_time' <?php if((!empty($post_param['search_where']) && $post_param['search_where'] == 'collo_end_time')){echo 'selected="selected"';}?>>托管结束时间</option>
							<option value='signing_time' <?php if((!empty($post_param['search_where']) && $post_param['search_where'] == 'signing_time')){echo 'selected="selected"';}?>>签约时间</option>
						</select>
					</div>
					<div class="fg">
						<input type="text" name="time_s" class="fg-time" value="<?php if(isset($_POST['time_s'])) {echo $_POST['time_s'];}?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
					</div>
					<div class="fg fg_tex03">—</div>
					<div class="fg fg_tex03">
						<input type="text" name="time_e" class="fg-time" value="<?php if(isset($_POST['time_e'])) {echo $_POST['time_e'];}?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">签约门店：</p>
					<div class="fg">
						<select class="select w80" name="agency_id" id="agency_id">
							<!--<option value="">请选择</option>
							<?php foreach($agency as $key =>$val){?>
							<option value="<?=$val['id'];?>" <?php if($post_param['agency_id'] == $val['id']){echo 'selected';}?>><?=$val['name'];?></option>
							<?php }?>-->
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
							<!--<option value="">请选择</option>-->
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
				<input type="hidden" name="pg" value="1">
				<input type="hidden" name="black" value="blacklist">
				<div class="fg_box">
					<div class="fg">
						<a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;"><span  class="btn_inner">搜索</span></a>
					</div>
					<div class="fg"> <a href="/collocation_contract/index/" class="reset">重置</a> </div>
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
			<div class="contract-crumbs clearfix" id="js_fun_btn2" style="padding-left:11px;">
			<?php if (isset($auth['add']['auth']) && $auth['add']['auth']) { ?>
				<a href="/collocation_contract/add_contract/" class="btn-lv"><span>+ 添加托管合同</span></a>
			<?php }else{?>
				<a href="#" class="btn-lv" onclick="permission_none();"><span>+ 添加托管合同</span></a>
			<?php }?>
			</div>
			<div class="table_all">
				<div class="title shop_title" id="js_title">
					<table class="table">
						<tr>
							<td class="c6">托管编号</td>
							<td class="c6">房源编号</td>
							<td class="c7">楼盘名称</td>
							<td class="c5">业主姓名</td>
							<td class="c7">租金（元/月）</td>
							<td class="c5">付款方式</td>
							<td class="c9">托管开始日期</td>
							<td class="c9">托管终止日期</td>
							<td class="c9">签约日期</td>
							<td class="c15">签约门店</td>
							<td class="c9">签约人</td>
							<td class="c7">合同状态</td>
							<td  style="padding-right:3px;">操作</td>
						</tr>
					</table>
				</div>
				<div class="inner shop_inner" id="js_inner">
					<table class="table" style="*+width:98.5%;_width:98.5%;">
					<?php
						if($list){
							foreach($list as $val){
					?>
						<tr>
							<td class="c6" style="cursor:pointer;" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><div class="info c227ac6"><?=$val['collocation_id']?></div></td>
							<td class="c6"  style="cursor:pointer;" onclick="$('#js_pop_box .iframePop').attr('src','/rent/details_house/<?=substr($val['house_id'],2);?>/4');openWin('js_pop_box');return false;"><div class="info c227ac6"><?=$val['house_id']?></div></td>
							<td class="c7" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?=$val['block_name']?></div></td>
							<td class="c5" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?=$val['owner']?></div></td>
							<td class="c7" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><div class="info" style="color:#f60"><?=strip_end_0($val['rental'])?></div></td>
							<td class="c5" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><div class="info">
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
							</div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?=date('Y-m-d',$val['collo_start_time'])?></div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?=date('Y-m-d',$val['collo_end_time'])?></div></td>
							<td class="c9"><div class="info" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><?=date('Y-m-d',$val['signing_time'])?></div></td>
							<td class="c15" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?=$val['agency_name']?></div></td>
							<td class="c9" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><div class="info"><?=$val['broker_name']?></div></td>
							<td class="c7" onclick = "window.location.href='/collocation_contract/contract_detail/<?=$val['id']?>/1';return false;"><div class="info c999">
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
							</div></td>
							<td style="padding-right:3px;">
							<?php
								if($val['status']==2){
							?>
								<?php if (isset($auth['cancel']['auth']) && $auth['cancel']['auth']) { ?>
									<a href="javascript:void(0)" onclick="handle_contract('<?=$val['id']?>',2);">作废</a>
								<?php } else {?>
									<a href="javascript:void(0)" onclick="permission_none();">作废</a>
								<?php }?>
							<?php }else{?>
								<?php if (isset($auth['edit']['auth']) && $auth['edit']['auth']) { ?>
									<a href="/collocation_contract/modify/<?=$val['id']?>">编辑</a>
								<?php } else {?>
									<a href="javascript:void(0)" onclick="permission_none();">编辑</a>
								<?php }?>
								<span style="color:#b2b2b2;">|</span>
								<?php if (isset($auth['delete']['auth']) && $auth['delete']['auth']) { ?>
									<a href="javascript:void(0)" onclick="handle_contract('<?=$val['id']?>',1);">删除</a>
								<?php } else {?>
									<a href="javascript:void(0)" onclick="permission_none();">删除</a>
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
<!--详情页弹框
<div id="js_pop_box_c" class="iframePopBox" style=" width:1260px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="1260" height="540" class='iframePop' src=""></iframe>
</div>-->

<!--房源详情弹跳页-->
<div id="js_pop_box" class="iframePopBox" style="width:816px; height:540px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
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
                    <button type="button" id = 'dialog_share' class="btn-lv1 btn-left JS_Close" >确定</button>
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
<div id="jss_pop_tip1" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" id="dialogSaveDiv1" style="font-size:14px;"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 该托管合同下有出租合同正在进行，不能作废！</p>
                     <div class="center">
                    <!--<button type="button" id = 'dialog_share_' class="btn-lv1 btn-left JS_Close" >确定</button>-->
                    <button type="button"  style="" class="btn-lv1 btn-left JS_Close">确认</button>
                    </div>
                </div>
            </div>
    </div>
</div>
<div id="jss_pop_tip2" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" id="dialogSaveDiv2" style="font-size:14px;"></p>
                     <div class="center">
                    <button type="button" id = 'dialog_share_' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button"   class="btn-lv1 btn-left JS_Close">取消</button>

                    </div>
                </div>
            </div>
    </div>
</div>
<!-- 确认通过+提示 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg1">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="<?php echo MLS_URL;?>/collocation_contract/index"></a>
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
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
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
	//删除,作废合同
	function handle_contract(id,type){
		if(type == 1){
			$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 合同删除后不可恢复，是否确认删除？');
			openWin('jss_pop_tip');
			$("#dialog_share").click(function(){
				$.ajax({
					url: "<?php echo MLS_URL;?>/collocation_contract/del/",
					type: "GET",
					dataType: "json",
					data: {
						id:id
					},
					success: function(data) {
						if(data == 'ok')
						{
							openWin('js_pop_msg1');
							$("#dialog_do_itp").html('删除成功');
						}else if(data['errorCode'] == '403'){//无权限
							permission_none();
						}
					}
				});
			});
		}else{
			//作废前去出租合同表里找是否有正在进行的出租合同
			$.ajax({
					url: "<?php echo MLS_URL;?>/collocation_contract/get_rent_info/",
					type: "GET",
					dataType: "json",
					data: {
						c_id:id
					},
					success: function(data) {
						if(data == 'ok')
						{
							//托管下有出租合同，并且出租没有作废，则托管合同也不能作废
							openWin('jss_pop_tip1');

						}else if(data == 'no'){//托管下有出租合同，且出租合同已作废
							$("#dialogSaveDiv2").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 该托管合同下有出租合同正在进行，确认作废？');
							openWin('jss_pop_tip2');
							$('#dialog_share_').click(function(){
								$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 合同作废后不可恢复，是否确认作废？');
								openWin('jss_pop_tip');
								$("#dialog_share").click(function(){
									$.ajax({
										url: "<?php echo MLS_URL;?>/collocation_contract/cancel/",
										type: "GET",
										dataType: "json",
										data: {
											id:id
										},
										success: function(data) {
											if(data == 'ok')
											{
												openWin('js_pop_msg1');
												$("#dialog_do_itp").html('合同已作废');
											}
										}
									});
								});
							});
						}else{//托管下没有出租合同
							$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 合同作废后不可恢复，是否确认作废？');
							openWin('jss_pop_tip');
							$("#dialog_share").click(function(){
								$.ajax({
									url: "<?php echo MLS_URL;?>/collocation_contract/cancel/",
									type: "GET",
									dataType: "json",
									data: {
										id:id
									},
									success: function(data) {
										if(data == 'ok')
										{
											openWin('js_pop_msg1');
											$("#dialog_do_itp").html('合同已作废');
										}
									}
								});
							});
						}
					}
			});
		}

	}
</script>
