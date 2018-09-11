<body>
    <form name="search_form" id='search_form' action ="" method="post">
        <div class="pop_box_g pop_box_cooperation" id="js_pop_box_cooperation" style="display:block;">
            <!--公用合同信息-->
            <?php $this->view('cooperate/send_cooperate_common_info');?>
            <div class="checkbox_x">
                <label><input type="checkbox" disabled="disabled" id='agreement' checked name='agreement'>我已阅读并同意</label>
                <a href="javascript:void(0)" onclick="openWin('js_pop_protocol')">《合作协议》</a>
            </div>
        </div>
			<?php if(!empty($cooperate_info['brokerinfo_b']['broker_id']) && $brokerid == $cooperate_info['brokerinfo_b']['broker_id']){ ?>
		<style>
			.cooperation_detailed{ height:450px;}
		</style>
		<div class="btn_box">
			<button type="button" class="grey_btn" onclick="show_cancle_window('js_pop_box_cooperation_cancle')">取消合作</button>
		</div>
			<?php }?>
    </div>
</form>

<!--引入公用模板-->
<?php $this->view('cooperate/cooperate_common_dialog_box');?>

<!--引入取消弹框模板-->
<?php $this->view('cooperate/cooperate_common_cancle_window');?>

<!--载入合作协议页面-->
<?php $this->view('cooperate/cooperative_agreements');?>