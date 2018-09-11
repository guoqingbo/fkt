<form name="search_form" id='search_form' action ="" method="post">
	<div class="pop_box_g pop_box_cooperation pop_box_g_border_none" id="js_pop_box_cooperation03" style="display:block;">
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
		<a  class="grey_btn grey_btn3" id="js_grey_btn003" onclick="commission_allocation()">提交佣金分配</a> 
		<button type="button" class="grey_btn" onclick="show_cancle_window('js_pop_box_cooperation_cancle')">取消合作</button>
		<!--<button type="button" class="grey_btn JS_Close" onclick="window.parent.closePopFun('js_pop_box_cooperation');">关闭</button>-->
	</div>
	<?php }?>
</div>
</form>
<!--载入合作协议页面-->
<?php $this->view('cooperate/cooperative_agreements');?>
<!--引入模板-->
<?php $this->view('cooperate/cooperate_common_dialog_box');?>
<!-- 引入取消合作模板-->
<?php $this->view('cooperate/cooperate_common_cancle_window');?>
<script type="text/javascript">
//提交佣金分配方案
function commission_allocation(){
	var buyer_ratio_a = $.trim($('#buyer_ratio_a').val());
	var seller_ratio_a = $.trim($('#seller_ratio_a').val());
	var ratio_a = $.trim($('#ratio_a').val());
	var ratio_b = $.trim($('#ratio_b').val());
	var c_id = $('#c_id').val();
	var old_esta = $('#old_esta').val();
	var step=$("#step").val();
	var secret_key = $('#secret_key').val();
	 if(!$('#agreement').prop('checked'))
			{   
				var msg = '需要勾选我已阅读并同意《合作协议》';
				$("#dialog_do_warnig_tip").html(msg);
				openWin('js_pop_do_warning');
				return false;
			}else if(buyer_ratio_a=='' || seller_ratio_a=='' || ratio_a=='' || ratio_b=='' || step==''){
				$("#dialog_do_warnig_tip").html('佣金比例不能为空');
				openWin('js_pop_do_warning');
				return false;
			}else if(c_id=='' || old_esta=='' || secret_key==''){
				return false;
			}else{
				$.ajax({
					type: "GET",
					dataType: "json",
					url: "/cooperate/sub_allocation_scheme/",
					data:{'c_id':c_id ,'old_esta':old_esta ,'buyer_ratio':buyer_ratio_a ,'seller_ratio':seller_ratio_a ,'a_ratio':ratio_a ,'b_ratio':ratio_b ,'secret_key':secret_key,'step':step},
					 success:function(data){
						 if(data['is_ok'] == 1)
									{   
										showParentDialog('dialog_do_itp' ,'js_pop_do_success',data['msg']);
									}
									else if(data['is_ok'] == 0)
									{
										showParentDialog('dialog_do_warnig_tip' , 'js_pop_do_warning' , data['msg']);
									}
									
									window.parent.closePopFun('js_pop_box_cooperation');
					 }
					
				})
			}
}

</script>