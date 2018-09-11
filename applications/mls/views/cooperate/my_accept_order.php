<body>
<form name="search_form" id='search_form' action ="" method="post">
<div class="pop_box_g pop_box_cooperation pop_box_g_border_none" id="js_pop_box_cooperation" style="display:block;">
    <!--公用合同信息-->
    <?php $this->view('cooperate/cooperate_common_info');?>
    <div class="checkbox_x">
    <label><input type="checkbox" disabled="disabled" id='agreement' checked name='agreement'>我已阅读并同意</label>
    <a href="javascript:void(0)" onclick="openWin('js_pop_protocol')">《合作协议》</a>
    </div>
    </div>
    <?php if(!empty($cooperate_info['brokerinfo_a']['broker_id']) && $brokerid == $cooperate_info['brokerinfo_a']['broker_id']){ ?>
	<style>
		.cooperation_detailed{ height:450px;}
	</style>
    <div class="btn_box">
        <button type="button" class="grey_btn grey_btn3" onclick="cooperate_aduit()">接受合作申请</button>
        <button type="button" class="grey_btn" onclick="show_refuse_window('js_pop_box_cooperation_refuse')">拒绝</button>
        <!--<button type="button" class="grey_btn JS_Close"  onclick="window.parent.closePopFun('js_pop_box_cooperation');">关闭</button>-->
    </div>
    <?php }?>
</div>
</form>
<!--载入合作协议页面-->
<?php $this->view('cooperate/cooperative_agreements');?>
<!-- 引入取消合作模板-->
<?php $this->view('cooperate/cooperate_common_refuse_window');?>
<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>
<script>
//接受合作
function cooperate_aduit()
{
    if(!$('#agreement').attr('checked'))
    {
        var msg = '需要勾选我已阅读并同意《合作协议》';
        $("#dialog_do_warnig_tip").html(msg);
        openWin('js_pop_do_warning');
        return false;
    }

    var c_id = $('#c_id').val();
    var step = $('#step').val();
    var old_esta = $('#old_esta').val();
    var secret_key = $('#secret_key').val();

    $.ajax({
        url: "<?php echo MLS_URL;?>/cooperate/accept_cooperation/",
        data:{'c_id':c_id,'step':step,'old_esta':old_esta ,'secret_key':secret_key},
        type: "GET",
        dataType:'JSON',
        success:function (data)
        {
            if(data['is_ok'] == 1)
            {
                showParentDialog('dialog_do_itp' , 'js_pop_do_success' , data['msg']);
            }
            else if(data['is_ok'] == 0)
            {
                showParentDialog('dialog_do_warnig_tip' , 'js_pop_do_warning' , data['msg']);
            }
        },
        error:function(er)
        {
            var error_msg = '异常错误';
            showParentDialog('dialog_do_warnig_tip' , 'js_pop_do_warning' , error_msg);
        }
    });

    window.parent.closePopFun('js_pop_box_cooperation');
}
</script>
