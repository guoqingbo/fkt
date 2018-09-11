<script>
    window.parent.addNavClass(21);
</script>
<div class="contract-wrap clearfix">
	<div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll2">
		<!-- 上部菜单选项，按钮-->
		<div class="search_box clearfix" style="margin-top:0;;overflow: hidden" id="js_search_box_02">
			<form name="search_form" id="subform" method="post" action="">
				<div class="fg_box">
					<p class="fg fg_tex">合同编号：</p>
					<div class="fg">
					    <input type="text" name="number" value="<?=$post_param['number'];?>" class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">状态：</p>

					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select w80" name="status">
							<option value="">请选择</option>
							<?php foreach($config['flow_status'] as $key=>$val){?>
							<option value="<?=$key;?>" <?=isset($post_param['status']) && $key=="{$post_param['status']}"  && $post_param['status'] != ''?'selected':'';?>><?=$val;?></option>
							<?php }?>
						</select>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">录入门店：</p>

					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select" name="entry_agency_id" id="entry_agency_id">
                            <?php foreach($agencys as $val){?>
                                        <option value="<?=$val['id'];?>" <?php if ($val['id'] == $post_param['entry_agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }?>
						</select>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">录入人：</p>
					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select" name="entry_broker_id" id="entry_broker_id">
                            <?php  foreach($brokers as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if ($val['broker_id'] == $post_param['entry_broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php  }?>
						</select>
					</div>
				</div>
                <script>
                $("#entry_agency_id").change(function(){
                    var agency_id = $('#entry_agency_id').val();
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
                            $('#entry_broker_id').append(html);
                        }
                        }
                    })
                    }else{
                    $('#entry_broker_id').html("<option value=''>请选择</option>");
                    }
                })
                </script>
				<div class="fg_box">
					<p class="fg fg_tex">录入时间：</p>
					<div class="fg">
						<input type="text" class="fg-time" name="start_time" value="<?=$post_param['start_time'];?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off"  onchange="check_num();">
					</div>
					<div class="fg fg_tex03">—</div>
					<div class="fg fg_tex03">
					<input type="text" class="fg-time" name="end_time" value="<?=$post_param['end_time'];?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off"  onchange="check_num();">
					&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="time_reminder"></span>
                    </div>
				</div>
				<div class="fg_box">
					<input type="hidden" name="page" value="1">
					<input type="hidden" name="is_submit" value="1">
                    <input type="hidden" id="flow_id">
                    <input type="hidden" id="review_type">
					<div class="fg"> <a href="javascript:void(0)" onclick="$('#subform :input[name=page]').val('1');form_submit();return false;" class="btn"><span class="btn_inner">搜索</span></a> </div>
					<div class="fg"> <a href="/contract_flow/actual_review_list" class="reset">重置</a> </div>
				</div>
			</form>
		</div>
<script>
$(function(){
	document.onkeydown = function(e){ //enter
		var ev = document.all ? window.event : e;
		if(ev.keyCode==13) {
			$('#subform :input[name=page]').val('1');form_submit();return false;
		 }
	}
});
</script>
		<!-- 上部菜单选项，按钮---end-->

		<div class="table_all">
			<div class="title shop_title" id="js_title">
				<table class="table">
					<tr>
						<td class="c10">收付日期</td>
						<td class="c10">合同编号</td>
						<td class="c6">款类</td>
						<td class="c6">收款方</td>
						<td class="c10"><div class="info">应收金额<br>(元)</div></td>
						<td class="c6">付款方</td>
						<td class="c10"><div class="info">应付金额<br>(元)</div></td>
                        <td class="c6">录入门店</td>
                        <td class="c6">录入人</td>
						<td class="c10">录入日期</td>
						<td>状态</td>
                        <td>操作</td>
					</tr>
				</table>
			</div>
			<div class="inner shop_inner" id="js_inner">
				<table class="table"  style="*+width:98.5%;*+padding:0 1.5% 0 0;_width:98.5%;_padding:0 1.5% 0 0;">
				    <?php if($list){foreach($list as $key=>$val){?>
					<tr class="<?=$key%2==1?'bg':''?>">
						<td class="c10"><div class="info"><?=$val['flow_time'];?></div></td>
                        <td class="c10"><div class="info c227ac6"><a href="/contract/contract_detail/<?=$val['id'];?>"><?=$val['number'];?></a></div></td>
						<td class="c6"><div class="info"><?=$config['money_type'][$val['money_type']];?></div></td>
						<td class="c6"><div class="info"><?=$val['collect_type']?$config['collect_type'][$val['collect_type']]:'—';?></div></td>
						<td class="c10"><div class="info"><?=strip_end_0($val['collect_money']);?></div></td>
						<td class="c6"><div class="info"><?=$val['pay_type']?$config['pay_type'][$val['pay_type']]:'—';?></div></td>
						<td class="c10"><div class="info"><?=strip_end_0($val['pay_money']);?></div></td>
                        <td class="c6"><div class="info"><?=$val['entry_agency_name'];?></div></td>
                        <td class="c6"><div class="info"><?=$val['entry_broker_name'];?></div></td>
						<td class="c10"><div class="info"><?=date('Y-m-d',$val['entry_time']);?></div></td>
						<td style="border-right:1px dashed #e6e6e6">
						    <?php if($val['flow_status']==0){?>
							<div class="info c999"><?=$config['flow_status'][$val['flow_status']];?></div>
						    <?php }elseif($val['flow_status']==1){?>
							<div class="info c680"><?=$config['flow_status'][$val['flow_status']];?></div>
						    <?php }else{?>
							<div class="info f00"><?=$config['flow_status'][$val['flow_status']];?></div>
						    <?php }?>
						</td>
                        <td class="c10">
                            <?php if($val['flow_status']==0){?>
                            <a href="javascript:void(0);" <?php if($auth['review']['auth']){?>onclick="$('#flow_id').val(<?=$val['f_id'];?>);$('#review_type').val('1');openWin('js_review_pop');"<?php }else{?>onclick="permission_none();"<?php }?>>通过</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href="javascript:void(0);" <?php if($auth['review']['auth']){?>onclick="$('#flow_id').val(<?=$val['f_id'];?>);$('#review_type').val('2');openWin('js_review_pop');"<?php }else{?>onclick="permission_none();"<?php }?>>拒绝</a>
                            <?php }elseif($val['flow_status']==1){?>
                            <a href="javascript:void(0);" <?php if($auth['fanreview']['auth']){?>onclick="$('#flow_id').val(<?=$val['f_id'];?>);openWin('js_cancel_review_pop');"<?php }else{?>onclick="permission_none();"<?php }?>>反审核</a>
                            <?php }?>
                        </td>
					</tr>
				    <?php }}else{?>
					<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td>
				    <?php }?>
				</table>
			</div>
		</div>
		<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
			<div class="get_page">
			    <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
			</div>
		</div>
	</div>
</div>

<div id="js_review_pop" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;审核完成后应收应付信息将不可修改,<br/>是否确定当前操作？</p>
		<button type="button" class="btn-lv1 JS_Close" onclick="review_this();">确定</button>
		<button type="button" class="btn-hui1 JS_Close" style="margin-left:10px;">取消</button>
	    </div>
	</div>
    </div>
</div>

<div id="js_cancel_review_pop" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;反审核后该信息将可修改，是否确定此操作</p>
		<button type="button" class="btn JS_Close" onclick="cancel_this();">确定</button>
		<button type="button" class="btn btn_none JS_Close">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="location=location;return false;"></a></div>
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
                <button class="btn JS_Close" type="button" onclick="location=location;return false;">确定</button>
            </div>
         </div>
    </div>
</div>

<!--审核不通过操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success1">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="location=location;return false;"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt3"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button" onclick="location=location;return false;">确定</button>
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
                            <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
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

});	//通过参数判断是否可以被提交
	function form_submit(){
	    $("input[name='page']").val(1);
	    var is_submit = $("input[name='is_submit']").val();
	    if(is_submit ==1){
		$('#subform').submit();
	    }
	}
	//审核该条合同
    function review_this(){
        var flow_id = $('#flow_id').val();
        $.ajax({
            url:"/contract_flow/sure_review",
            type:"POST",
            dataType:"json",
            data:{
                flow_id:flow_id,
                review_type:$("#review_type").val(),
                flow_type:'actual'
            },
            success:function(data){
                var type = $("#review_type").val();
                if(data['result'] == 'ok'){
                    if(type ==1){
                        $('#js_prompt1').text('审核通过！');
                        openWin('js_pop_success');
                    }else{
                        $('#js_prompt3').text('审核不通过！');
                        openWin('js_pop_success1');
                    }
                }else{
                    $('#js_prompt2').text('审核失败！');
                    openWin('js_pop_false');
                }
            }
        })
    }

    //反审核该条合同
    function cancel_this(){
        var flow_id = $('#flow_id').val();
        $.ajax({
            url:"/contract_flow/cancel_review",
            type:"POST",
            dataType:"json",
            data:{
                flow_id:flow_id,
                flow_type:'actual'
            },
            success:function(data){
                if(data['result'] == 'ok'){
                    $('#js_prompt1').text('反审核成功！');
                    openWin('js_pop_success');
                }else{
                    $('#js_prompt2').text('反审核失败！');
                    openWin('js_pop_false');
                }
            }
        })
    }

    function check_num()
    {
        var timemin    =    $("input[name='start_time']").val();	//最小面积
        var timemax    =    $("input[name='end_time']").val();	//最大面积

        if(!timemin && !timemax){
            $("#time_reminder").html("");
            $("input[name='is_submit']").val('1');
        }

        //最小面积timemin 必须小于 最大面积timemax
        if(timemin && timemax){
            if(timemin>timemax){
                $("#time_reminder").html("时间筛选区间输入有误！");
                $("input[name='is_submit']").val('0');
                return;
            }else{
                $("#time_reminder").html("");
                $("input[name='is_submit']").val('1');
            }
        }
    }
</script>
