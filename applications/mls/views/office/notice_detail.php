<?php
	//echo "<pre>"; 
	//print_r($notice_detail[0]);die;
?>
<div class="pop_box_g pop_see_inform" id="js_see_inform"  style="display:block;border:0px;width:504px;height:274px">
    <div class="hd">
        <div class="title">查看通知</div>
       
    </div>
    <div class="mod">
    					<div class="inform_inner">
         					<div class="clearfix">
              			<p class="l_item">标题：</p>
                 <p class="r_info"><?php echo $notice_detail[0]['title'];?></p>	
              </div>
              <div class="clearfix">
              			<p class="l_item">内容：</p>
                 <p class="r_info"><?php echo $notice_detail[0]['contents'];?></p>
              </div>
              <div class="clearfix">
              		<p class="l_item">对象：</p>
                <p class="r_info"><?php echo $notice_detail[0]['receiver_name'];?></p>
              </div>
             	<div class="clearfix">
              		<p class="l_item">时间：</p>
                <p class="r_info"><?php 
					date_default_timezone_set('PRC');echo date('Y-m-d H:i',$notice_detail[0]['notice_time']);?></p>
              </div>
             	<div class="clearfix">
              		<p class="l_item">级别：</p>
                <p class="r_info"><?php if($notice_detail[0]['level']==1){echo "重要";}else if($notice_detail[0]['level']==2){echo "紧急";}else if($notice_detail[0]['level']==0){echo "普通";}?></p>
             </div>
        	<button class="JS_Close btn-lv1 btn-mid" type="button">确定</button>
         </div>
         
         
    </div>
</div>
<script>
$(function(){
	$(".JS_Close").click(function(){
		$(window.parent.document).find("#js_pop_box_g").hide();
		$(window.parent.document).find("#GTipsCoverjs_pop_box_g").remove();
	});
});
</script>