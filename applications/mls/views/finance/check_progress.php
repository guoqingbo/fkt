
<div class="wrap" id="view_progress">
    <div class="check_big_photo2 check_progrss">
        <h2>查看进度</h2>
        <div class="inner_show">
		<?php foreach($my_progress['list'] as $key=>$value){?>
            <div class="<?php if(!is_null($value['status']) && ($value['status'] == 1 || $value['status'] == 0)){echo 'progress_now';}?> progress_1 clearfix">
                <div class="pro_left">
                    <span class="pro_bar">&nbsp;</span>
                    <strong><?=$value['step_str']?></strong>
                </div>
                <div class="success_now pro_right">
                    <h1 class="pro_tit">
							<?php if($value['status'] == -1){?>
								审核未通过
							<?php }elseif($value['status'] == 1 && $value['step'] == 3){?>
								已预约
							<?php }elseif($value['status'] == 1 && $value['step'] == 5){?>
								已支付
							<?php }elseif($value['status'] == 1 && $value['step'] == 6){?>
								已下款
							<?php }elseif($value['status'] == 1 && $value['step'] == 7){?>
								已完成
							<?php }elseif($value['status'] == 0 && $value['step'] == 3){?>
								预约中
							<?php }elseif($value['status'] == 1){?>
								审核<?=$value['status_str']?>
							<?php }elseif($value['status'] == 0 && $value['step'] == 4){?>
								审批中
							<?php }elseif($value['status'] == 0 && $value['step'] == 5){?>
								等待支付评估费
							<?php }elseif($value['status'] == 0 && $value['step'] == 6){?>
								等待下款
							<?php }elseif($value['status'] == 0){?>
								<?=$value['status_str']?>
							<?php }?>
					</h1>
					<?php if($value['status'] == 0 && $value['step'] != 3){?>
                    <p style="display:block">等待<?=$value['step_str']?></p>
					<?php }elseif($value['status'] == 0 && $value['step'] == 3){?>
					<p style="display:block">等待确认时间</p>
					<?php }elseif($value['status'] == -1){?>
					<p style="display:block">原因：<?=$value['reason']?></p>
					<?php }elseif($value['status'] == 1 && $value['step'] == 5){?>
					<p style="display:block">金额：<?=$value['evaluate_cost']?>元</p>
					<?php }elseif($value['status'] == 1 && ($value['step'] == 6 || $value['step'] == 7)){?>
					<p style="display:block">金额：<?=$value['total_loan']?>万元</p>
					<?php }?>
                    <strong><?php if($value['steptime']){?><?php echo date('Y-m-d H:i:s',$value['steptime'])?><?php }?></strong>
                </div>
            </div>
		<?php }?>
        </div>
    </div>
</div>
<script>
	$(function(){
	$(".check_close").click(function(){
		$("#view_progress").hide();
	});	
}); 
</script>