<form name="search_form" id='search_form' action ="" method="post">
<div class="pop_box_g pop_box_cooperation pop_box_g_border_none" id="js_pop_box_cooperation03" style="display:block;">
    <!--公用合同信息-->
    <?php include_once 'send_cooperate_common_info.php';?>
    <div class="checkbox_x">
    <label><input type = "checkbox" disabled="disabled" id = 'agreement' checked name='agreement'>我已阅读并同意</label>
    <a href="javascript:void(0)" onclick="openWin('js_pop_protocol')">《合作协议》</a>
    </div>
    </div>
        <?php if( ( !empty($cooperate_info['brokerinfo_b']['broker_id']) || !empty($cooperate_info['brokerinfo_a']['broker_id']))
                && ( $brokerid == $cooperate_info['brokerinfo_b']['broker_id'] ||  $brokerid == $cooperate_info['brokerinfo_a']['broker_id']) ){ ?>
	<style>
		.cooperation_detailed{ height:450px;}
	</style>
    <div class="btn_box">
        <?php if($house_owner == $brokerid){ ?>
		<a href="javascript:void(0);" class="btn-lan btn-left" onclick="sub_cooperate_result()"><span>确认提交</span></a>
        <?php }?>
		<a href="javascript:void(0);" class="grey_btn" onclick="show_cancle_window('js_pop_box_cooperation_cancle')"><span>取消合作</span></a>
    </div>
        <?php }?>
    </div>
</div>
</form>
<!--引入模板-->
<?php $this->view('cooperate/cooperate_common_dialog_box');?>
<?php $this->view('cooperate/cooperate_common_cancle_window');?>
<!--载入合作协议页面-->
<?php $this->view('cooperate/cooperative_agreements');?>
<script>
//我要举报弹框
function show_report_window(type , ct_id , cooper_type)
{
    //判断该房源是否存在
    $.ajax({
        url: "<?php echo MLS_URL;?>/cooperate/get_step_esta_by_id",
        type: "GET",
        data: {ct_id:ct_id},
        success: function(data) {
            var data_obj = eval('('+data+')');
            if('3'==data_obj.step && '4'==data_obj.esta){
                //判断当前合作状态
                if(!$('#agreement').attr('checked'))
                {
                    var msg = '需要勾选我已阅读并同意《合作协议》';
                    $("#dialog_do_warnig_tip").html(msg);
                    openWin('js_pop_do_warning');
                    return false;
                }
                report(type , ct_id , cooper_type);
            }else{
                $("#dialog_do_warnig_tip").html("合作状态有变更，请刷新页面");
                openWin('js_pop_do_warning');
            }
        }
     });
}

function sub_cooperate_result()
{
    if(!$('#agreement').attr('checked'))
    {
        var msg = '需要勾选我已阅读并同意《合作协议》';
        $("#dialog_do_warnig_tip").html(msg);
        openWin('js_pop_do_warning');
        return false;
    }

    var c_id = $('#c_id').val();
	var type = $('#type').val(); //房源类型
    var step = $('#step').val();
    var old_esta = $('#old_esta').val();
    var esta = $("input[name='esta']:checked").val();
    var total_price = $('#total_price').val();
    var secret_key = $('#secret_key').val();

    if(esta == 7 && (isNaN(total_price) || (total_price == null || typeof total_price == undefined || total_price == '')))
    {
        $('#show_error').html('*成交价必须是数字').show();
        return false;
    }

    $.ajax({
        url: "<?php echo MLS_URL;?>/cooperate/sub_cooperate_result/",
        data:{'c_id':c_id,'esta':esta,'old_esta':old_esta,'total_price':total_price,'step':step,'secret_key':secret_key},
        type : "GET",
        dataType : 'JSON',
        success:function (data)
        {
            if(data['is_ok'] == 1)
            {
		if(esta ==7){
                    if(type =="sell"){
			$(window.parent.document).find("#js_chushen_pop .iframePop").attr('src','/cooperate/chushen/'+c_id);
			window.parent.openWin('js_chushen_pop');
		    }else{
			showParentDialog('dialog_do_itp' ,'js_pop_do_success',data['msg']);
		    }
		}else{
		    showParentDialog('dialog_do_itp' ,'js_pop_do_success',data['msg']);
		}
            }
            else if(data['is_ok'] == 0)
            {
                showParentDialog('dialog_do_warnig_tip' ,'js_pop_do_warning',data['msg']);
            }
        },
        error:function(er)
        {
            var error_msg = '异常错误';
            showParentDialog('dialog_do_warnig_tip' ,'js_pop_do_warning',error_msg);
        }
    });

    window.parent.closePopFun('js_pop_box_cooperation');
}


$(function(){
    $("#total_price").blur(function()
    {
        var total_price = $(this).val();
        if(isNaN(total_price) || (total_price == null || typeof total_price == undefined || total_price == ''))
        {
            $('#show_error').html('*成交价必须是数字').show();
        }
        else
        {
            $('#show_error').html('').hide();
        }
    });
 });
</script>
<!--举报信息弹框-->
<div id="js_woyaojubao" class="iframePopBox" style=" width:500px; height:445px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="500" height="445" class='iframePop' src=""></iframe>
</div>
