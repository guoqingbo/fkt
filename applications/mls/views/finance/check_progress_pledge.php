
<div class="wrap" id="view_progress">
    <div class="check_big_photo2 check_progrss">
        <h2>查看进度</h2>
        <div class="inner_show">
		<?php foreach($my_progress['list'] as $key=>$value){?>
            <div class="<?php if(!is_null($value['status']) && ($value['status'] == 1 || $value['status'] == 0)){echo 'progress_now';}?> progress_1 clearfix">
                <div class="pro_left">
                    <span class="pro_bar">&nbsp;</span>
                    <strong><?=$value['status_str']?></strong>
                </div>
                <div class="success_now pro_right">
                    <strong><?php if($value['steptime']){?><?php echo date('Y-m-d H:i:s',$value['steptime'])?><?php }?></strong>
                    <p style="display:block"><?php if(!empty($value['reason'])){?><?=$value['reason']?><?php }?></p>
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