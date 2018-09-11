<script>
    window.parent.addNavClass(24);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
    <div class="wrapper jr_index">
		<?php
			if('cd' == $city){
				?>
				<div class="model-1">
					<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dyd//kk2.jpg" alt=""/>
					<a href="/finance/apply_rental"<?php if($group_id != 2){ ?> onclick="openWin('js_pop');return false;"<?php } ?> class="f_bt_zj">&nbsp;</a>
				</div>
				<div class="model-2">
					<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dyd//kk3.jpg" alt=""/>
					<a href="javascript:void(0)" class="f_bt_xy">&nbsp;</a>
				</div>
				<?php
			}else{
				?>
				<div class="model-1">
					<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dyd//kk1.jpg" alt=""/>
					<a href="/finance/apply_pledge"<?php if($group_id != 2){ ?> onclick="openWin('js_pop');return false;"<?php } ?> class="f_bt_dyd">&nbsp;</a>
				</div>
				<div class="model-2">
					<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dyd//kk2.jpg" alt=""/>
					<a href="/finance/apply_rental"<?php if($group_id != 2){ ?> onclick="openWin('js_pop');return false;"<?php } ?> class="f_bt_zj">&nbsp;</a>
				</div>
				<div class="model-3">
					<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dyd//kk3.jpg" alt=""/>
					<a href="javascript:void(0)" class="f_bt_xy">&nbsp;</a>
				</div>
				<?php
			}
		?>
    </div>
	<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop">
		<div class="hd">
			<div class="title">提示</div>
			<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
		</div>
		 <div class="mod">
			<div class="inform_inner">
			<div class="up_inner">
					<table class="del_table_pop">
						<tr>
							<td width="25%" align="right" style="padding-right:10px;">
					<img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
							<td>
					<p class="left" style="font-size:14px;color:#666;" id="js_prompt">只有认证经纪人才能申请!</p>
							</td>
						</tr>
					</table>
					<button class="btn JS_Close" type="button">确定</button>
				</div>
			 </div>
		</div>
	</div>

  <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/openWin.js" type="text/javascript"></script>
	<script>
		window.onload = function(){
		    setInterval(function(){
		      var winHeight = $(window).height();
              $('.wrapper').css('height',winHeight);
		    },500);

		}
	</script>
