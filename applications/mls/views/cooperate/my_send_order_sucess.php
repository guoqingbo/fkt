<form name="search_form" id='search_form' action ="" method="post">
<div class="pop_box_g pop_box_cooperation pop_box_g_border_none" id="js_pop_box_cooperation03" style="display:block;">
    <!--公用合同信息-->
    <?php $this->view('cooperate/send_cooperate_common_info');?>
    <div class="qr_bd">
        <h5 class="h5">该房源已成交! 成交价<?php echo strip_end_0($cooperate_info['price']);?>
        <?php if($cooperate_info['tbl'] == 'sell'){?>
           万
        <?php }else {
         echo ('1'==$cooperate_info['house']['price_danwei'])?'元/㎡*天':'元/月';
        }?>
        </h5>
    </div>
    <div class="checkbox_x">
    <label><input type="checkbox" disabled="disabled" id='agreement' checked name='agreement'>我已阅读并同意</label>
    <a href="javascript:void(0)" onclick="openWin('js_pop_protocol')">《合作协议》</a>
    </div>
    </div>
        <?php if(!empty($cooperate_info['brokerinfo_b']['broker_id']) && $brokerid == $cooperate_info['brokerinfo_b']['broker_id']){ 
            if(!$cooperate_info['appraise_b']){
        ?>
		<style>
			.cooperation_detailed{ height:450px;}
		</style>
    <div class="btn_box">
        <button type="button" class="grey_btn" onclick="show_appraise_window('send',<?php echo $cooperate_info['id']; ?>)">评价</button>
    </div>
        <?php }
            }
        ?>
</div>
</form>
<!--引入模板-->
<?php $this->view('cooperate/cooperate_common_dialog_box');?>
<!--载入合作协议页面-->
<?php $this->view('cooperate/cooperative_agreements');?>

<!--举报信息弹框-->
<div id="js_woyaojubao" class="iframePopBox" style=" width:500px; height:445px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="500" height="445" class='iframePop' src=""></iframe>
</div>

<script type="text/javascript">
//我要举报弹框
function show_report_window(type , ct_id , cooper_type)
{   
    if(!$('#agreement').attr('checked'))
    {   
        var msg = '需要勾选我已阅读并同意《合作协议》';
        $("#dialog_do_warnig_tip").html(msg);
        openWin('js_pop_do_warning');
        return false;   
    }
    
    report(type,ct_id,cooper_type);
}

function show_appraise_window( type , c_id)
{   
    if(!$('#agreement').attr('checked'))
    {   
        var msg = '需要勾选我已阅读并同意《合作协议》';
        $("#dialog_do_warnig_tip").html(msg);
        openWin('js_pop_do_warning');
        return false;   
    }
    window.parent.open_appraise_details(type , c_id);
}
</script>