<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script type="text/javascript">
window.parent.addNavClass(10);
$(function(){
    $('#allot_agency').change(function(){
        var agencyId = $(this).val();
        $.ajax({
            type: 'get',
            url : '/my_task/get_broker_ajax/'+agencyId,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg===''){
                    str = '<option value="0">不限</option>';
                }else{
                    str = '<option value="0">不限</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].broker_id+'">'+msg[i].truename+'</option>';
                    }
                }
                $('#allot_broker').html(str);
            }
        });
    });

    $('#run_agency').change(function(){
        var agencyId = $(this).val();
        $.ajax({
            type: 'get',
            url : '/my_task/get_broker_ajax/'+agencyId,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg===''){
                    str = '<option value="0">不限</option>';
                }else{
                    str = '<option value="0">不限</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].broker_id+'">'+msg[i].truename+'</option>';
                    }
                }
                $('#run_broker').html(str);
            }
        });
    });

    $('#search_task').click(function(){
    	$('input[name=page]').val(1);
        $('#search_form').submit();
        return false;
    });

    /*$('#reset_task').click(function(){
    	//$('#search_form')[0].reset();
    	$("#search_form").find(":input").not(":button,:submit,:reset,:hidden").val("").removeAttr("checked").removeAttr("selected");
        $("#allot_broker").html("<option value='0'>不限</option>");
        $("#run_broker").html("<option value='0'>不限</option>");
        return false;
    });*/
});
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<form name="search_form" id="search_form" method="post" action="" >
	<input type='hidden' id='my_task' value='1'>
	<div class="search_box clearfix" id="js_search_box">
		<div class="fg_box">
			<p class="fg fg_tex">任务类型：</p>
			<div class="fg">
				<select class="select" name="task_type" id="task_type">
					<option value="">不限</option>
					<option value="1" <?php if($task_type == 1){echo 'selected="selected"';}?>>系统跟进</option>
					<option value="2" <?php if($task_type == 2){echo 'selected="selected"';}?>>房源跟进</option>
					<option value="3" <?php if($task_type == 3){echo 'selected="selected"';}?>>客源跟进</option>
				</select>
			</div>
		</div>
		<div class="fg_box">
			<p class="fg fg_tex"> 执行日期：</p>
			<div class="fg">
				<input type="text" class="input time_bg w90" id="start_date_begin" name="start_date_begin" onclick="WdatePicker()" value="<?=$start_date_begin?>">
			</div>
			<p class="fg fg_tex fg_tex02">—</p>
			<div class="fg">
				<input type="text" class="input time_bg w90" id="start_date_end" name="start_date_end" onclick="WdatePicker()" value="<?=$start_date_end?>">
			</div>
		</div>

		<div class="fg_box">
			<p class="fg fg_tex"> 分配部门：</p>
			<div class="fg">
				<select class="select" id="allot_agency" name="allot_agency" >
					<option value="0">不限</option>
					<?php
					if(!empty($company_id)){
						if ($agency_info) {
							foreach ($agency_info as $v) {
						?>
						<option value="<?=$v['agency_id']?>"<?php if((!empty($allot_agency) && $allot_agency == $v['agency_id'])){echo 'selected="selected"';}?>><?=$v['agency_name']?></option>
						<?php
							}
						}
					}
					?>
				</select>
			</div>

		</div>
		<div class="fg_box">
			<p class="fg fg_tex"> 分配人：</p>
			<div class="fg">
				<select class="select" id="allot_broker" name="allot_broker">
					<option value="0">不限</option>
					<?php if(is_array($broker_info_allot) && !empty($broker_info_allot)){ ?>
					<?php foreach($broker_info_allot as $key =>$value){ ?>
					<option value="<?php echo $value['broker_id'];?>" <?php if($allot_broker == $value['broker_id']){ echo 'selected="selected"';  } ?>>
					<?php echo $value['truename'];?>
					</option>
					<?php } ?>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="fg_box">
			<p class="fg fg_tex"> 状态：</p>
			<div class="fg">
				<select class="select" id="status" name="status">
					<option value="0" <?php if($status == 0){echo 'selected="selected"';}?>>全部</option>
					<option value="1" <?php if($status == 1){echo 'selected="selected"';}?>>已处理</option>
					<option value="2" <?php if($status == 2){echo 'selected="selected"';}?>>待处理</option>
					<option value="3" <?php if($status == 3){echo 'selected="selected"';}?>>已逾期</option>
                    <option value="4" <?php if ($status == 4) {
                        echo 'selected="selected"';
                    } ?>>已撤销
                    </option>
				</select>
			</div>
		</div>
		<div class="fg_box">
			<div class="fg"> <a href="javascript:void(0)" class="btn" id="search_task"><span class="btn_inner">搜索</span></a> </div>
			<div class="fg"> <a href="/my_task/index/" class="reset" id="reset_task">重置</a> </div>
		</div>
	</div>

    <div class="table_all">
        <div class="title shop_title" id="js_title">
            <table class="table">
                <tr>
                    <td class="c10"><div class="info">任务类型</div></td>
                    <td class="c10"><div class="info">房源/客源编号</div></td>
                    <td class="c15"><div class="info">任务说明</div></td>
                    <td class="c10"><div class="info">执行期限</div></td>
					<td class="c10"><div class="info">分配人</div></td>
                    <td class="c10"><div class="info">分配时间</div></td>
                    <td class="c10"><div class="info">状态</div></td>
                    <td class="c10"><div class="info">执行时间</div></td>
                    <td><div class="info">操作</div></td>
                </tr>
            </table>
        </div>
        <div class="inner shop_inner" id="js_inner">
            <table class="table">
                <?php
                if($task_info){
                    foreach ($task_info as $key=>$value){
                ?>
                <tr>
                    <td class="c10">
                        <div class="info">
                        <?php
                        if($value['task_type'] == 1){
                            echo '系统跟进';
                        }elseif($value['task_type'] == 2){
                            echo '房源跟进';
                        }elseif($value['task_type'] == 3){
                            echo '客源跟进';
                        }
                        ?>
                        </div>
                    </td>
                    <td class="c10">
                        <div class="info">
                            <?php
                            if($value['task_style'] == 1){
                            ?>
                            <a href="javascript:void(0)" date-url="/sell/details/<?=$value['house_id']?>/1" onClick="openUrl(this)"><?=$value['format_house_id'] ?></a>
                            <?php
                            }elseif($value['task_style'] == 2){
                            ?>
                            <a href="javascript:void(0)" date-url="/rent/details/<?=$value['house_id']?>/1" onClick="openUrl(this)"><?=$value['format_house_id'] ?></a>
                            <?php
                            }elseif($value['task_style'] == 3){
                            ?>
                            <a href="javascript:void(0)" date-url="/customer/details/<?=$value['custom_id'] ?>" onClick="openUrl(this)"><?=$value['format_custom_id'] ?></a>
                            <?php
                            }elseif($value['task_style'] == 4){
                            ?>
                            <a href="javascript:void(0)" date-url="/rent_customer/details/<?=$value['custom_id'] ?>" onClick="openUrl(this)"><?=$value['format_custom_id'] ?></a>
                            <?php
                            }
                            ?>
                        </div>
                    </td>
                    <td class="c15"><div class="info"><?=$value['content']?></div></td>
                    <td class="c10"><div class="info"><?=$value['over_date'] == 0 ? '' : date('Y-m-d', $value['over_date'])?></div></td>
                    <td class="c10"><div class="info"><?=$value['allot_truename']?></div></td>
                    <td class="c10"><div class="info"><?=$value['insert_date'] == 0 ? '' : date('Y-m-d', $value['insert_date'])?></div></td>

                    <td class="c10">
                        <div class="info">
                            <?php
							if($value['over_date']>time()){
								switch ($value['status']){
									case 1:echo '<p class="s">已处理</p>';break;
									case 2:echo '待处理';break;
									case 3:echo '<font color="red">已逾期</font>';break;
                                    case 4:
                                        echo '已撤销';
                                        break;
								}
							}else{
								echo '<font color="red">已逾期</font>';
							}
                            ?>
                        </div>
                    </td>
                    <td class="c10"><div class="info"><?=$value['start_date'] == 0 ? '' : date('Y-m-d', $value['start_date'])?></div></td>
                    <td>
                        <div class="info">
                        <?php  if($value['status'] == 2 && $value['over_date']>time()){
                                    if($value['task_style'] == 1) {?>
                                        <a href="javascript:void(0)" date-url="/sell/house_follow/<?=$value['house_id']?>/1/<?=$value['id']?>" onClick="openGenjinUrl(this)">跟进</a><span style="margin:0 10px;color:#b2b2b2;">|</span><a href="javascript:void(0)" onclick="revoke(<?=$value['id']?>)">不跟进</a>
                            <?php }elseif($value['task_style'] == 2){?>
                                        <a href="javascript:void(0)" date-url="/rent/house_follow/<?=$value['house_id']?>/1/<?=$value['id']?>" onClick="openGenjinUrl(this)">跟进</a><span style="margin:0 10px;color:#b2b2b2;">|</span><a href="javascript:void(0)" onclick="revoke(<?=$value['id']?>)">不跟进</a>
                            <?php }elseif($value['task_style'] == 3){?>
                                        <a href="javascript:void(0)" date-url="/customer/customer_follow/<?=$value['custom_id'] ?>/1/<?=$value['id']?>" onClick="openGenjinUrl(this)">跟进</a><span style="margin:0 10px;color:#b2b2b2;">|</span><a href="javascript:void(0)" onclick="revoke(<?=$value['id']?>)">不跟进</a>
                            <?php }elseif($value['task_style'] == 4){?>
                                        <a href="javascript:void(0)" date-url="/rent_customer/customer_follow/<?=$value['custom_id'] ?>/1/<?=$value['id']?>" onClick="openGenjinUrl(this)">跟进</a><span style="margin:0 10px;color:#b2b2b2;">|</span><a href="javascript:void(0)" onclick="revoke(<?=$value['id']?>)">不跟进</a>
                        <?php }}else if($value['status'] == 1 && $value['over_date']>time()){?>
							<?php if(empty($value['reason'])){?>
								<?php if($value['task_style'] == 1){ ?>
								<a href="javascript:void(0)" onclick="xq_openfollow('sell',<?php echo $value['house_id'] ?>,1)">查看</a>
								<?php }elseif($value['task_style'] == 2){ ?>
								<a href="javascript:void(0)" onclick="xq_openfollow('rent',<?php echo $value['house_id'] ?>,1)">查看</a>
								<?php }elseif($value['task_style'] == 3){ ?>
								<a href="javascript:void(0)" onclick="xq_openfollow('customer',<?php echo $value['custom_id'] ?>,1)">查看</a>
								<?php }elseif($value['task_style'] == 4){ ?>
								<a href="javascript:void(0)" onclick="xq_openfollow('rent_customer',<?php echo $value['custom_id'] ?>,1)">查看</a>
								<?php }?>
							<?php }else{?>
								<a href="javascript:void(0)" onclick="$('#reason_details').html('<?=$value['reason']?>');openWin('js_pop_do_cancel_details');">查看</a>
							<?php }?>
						<?php }else{?>
                              --
                        <?php }?>
                        </div>
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

<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">操作成功！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="location.href='/my_task/'">确定</button>
			</div>
		</div>
	</div>
</div>
<div id="js_pop_do_warning"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_warnig_tip">操作失败！</p>
			</div>
		</div>
	</div>
</div>
<div id="js_pop_do_delete"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_delete_tip">确定要撤销选定的任务吗？</p>
				<button type="button"  class="btn-lv1 btn-left">确定</button>
				<button type="button" class="btn-hui1 JS_Close">取消</button>
			</div>
		</div>
	</div>
</div>
<div class="pop_box_g pop_txt" id="js_pop_do_cancel">
    <div class="hd">
        <div class="title">处理任务</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="$('#cancel_wrong').hide();"></a></div>
    </div>
    <div class="mod">
    	<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"><span>您是否确定不对当前任务房/客源进行跟进？</span></p>
		<div class="textarea"><p>请填写不跟进的原因:<font color='red' style="display:none" id="cancel_wrong">&nbsp;*请输入原因</font></p><textarea id="reason" ></textarea></div>
		<div class="mt10 center">
			<button class="btn-lv1 btn-left " id="dialog_btn" type="button">确定</button>
			<button class="btn-hui1 JS_Close" type="button">取消</button>
		</div>
    </div>
</div>

<div class="pop_box_g pop_txt" id="js_pop_do_cancel_details">
    <div class="hd">
        <div class="title">处理任务详情</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="$('#cancel_wrong').hide();"></a></div>
    </div>
    <div class="mod">
    	<p class="text"><span>房/客源未跟进</span></p>
		<div class="textarea"><p>原因:</p><textarea id="reason_details" readonly></textarea></div>
		<div class="mt10 center">
			<button class="btn-lv1 btn-left JS_Close" type="button">确定</button>
		</div>
    </div>
</div>

<!--分配任务-->
<div id="js_fenpeirenwu" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--分配客源-->
<div id="js_allocate_customer" class="iframePopBox" style=" width:816px; height:340px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="340" class='iframePop' src=""></iframe>
</div>
<!--分配房源-->
<div id="js_allocate_house" class="iframePopBox" style=" width:816px; height:340px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="340" class='iframePop' src=""></iframe>
</div>
<!--跟进信息弹框-->
<div id="js_genjin" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--详情页弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--匹配详情页弹框-->
<div id="js_pop_box_g_match" class="iframePopBox" style=" width:1200px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="1200" height="540" class='iframePop' src=""></iframe>
</div>
<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>


<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js,calculate.js"></script>

<script type="text/javascript">

function openUrl(obj)
{
    var _url = $(obj).attr("date-url");
    $("#js_pop_box_g .iframePop").attr("src",_url);
    openWin('js_pop_box_g');

}

function openGenjinUrl(obj){
	var _url = $(obj).attr("date-url");
    $("#js_genjin .iframePop").attr("src",_url);
    openWin('js_genjin');
}

function revoke(id){
	openWin('js_pop_do_cancel');
	$('#dialog_btn').bind('click', function() {
		var reason = $("#reason").val();
		if(reason.length< 1){
			openWin('cancel_wrong');
			return false;
		}
    	$.ajax({
            type: 'post',
            url: '/my_task/revoke/',
            data:{id:id,reason:reason},
            success: function(data){
                $("#dialog_do_success_tip").html(data);
        		openWin('js_pop_do_success');
            }
        });
	})
}

$("#reason").keyup(function(){
	if($("#reason").val().length>0){
		$("#cancel_wrong").hide();
	}

})

</script>
