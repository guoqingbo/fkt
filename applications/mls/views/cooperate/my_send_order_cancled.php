<body>
<form name="search_form" id='search_form' action ="" method="post">
<div class="pop_box_g pop_box_cooperation pop_box_g_border_none" id="js_pop_box_cooperation02" style="display:block;">
    <!--公用合同信息-->
    <?php $this->view('cooperate/send_cooperate_common_info');?>
    <div class="checkbox_x">
    <label><input type="checkbox" disabled="disabled" id='agreement' checked name='agreement'>我已阅读并同意</label>
    <a href="javascript:void(0)" onclick="openWin('js_pop_protocol')">《合作协议》</a>
    </div>
    </div>
        <?php if(!empty($cooperate_info['brokerinfo_b']['broker_id']) && $brokerid == $cooperate_info['brokerinfo_b']['broker_id']){ 
            if(!$cooperate_info['appraise_b']){
        ?>
    <div class="btn_box">
        <button type="button" class="grey_btn" onclick="show_appraise_window('send',<?php echo $cooperate_info['id']; ?>)">评价</button>
    </div>
        <?php }
        }
        ?>
</div>
</form>
<!--载入合作协议页面-->
<?php $this->view('cooperate/cooperative_agreements');?>
<script type="text/javascript">
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