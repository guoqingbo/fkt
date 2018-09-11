
	<!--房源详情弹框-->
	<div class="pop_box_g pop_see_info_deal" id="house_detail" style="width:760px;height:480px; display:block;">
		<div class="hd">
			<div class="title">商品详情</div>
		</div>
		<div class="mod integral">
			<div class="inner clearfix">
				<img class="pull-left fl" width="280" height="280" src="<?php echo str_replace('thumb/','',$list['product_picture']);?>">
				<div class="pull-right fr">
					<h3><?php echo $list['product_name'];?></h3>
					<p><?php echo preg_replace('/\n|\r\n/', '<br/>', $list['product_detail']);?></p>
					<div class="mb10 mt10">所需积分：<strong class="f00"><?php echo $list['score'];?></strong>　<span class="c999">|</span>　我的积分：<strong class="f00"><?php echo $list['my_credit'];?></strong></div>
				<!--库存足够-->
				<?php if($list['type'] == 1){
					if($list['stock'] > 0){?>
					<?php if($list['score'] <= $list['my_credit']){?>
					<a class="btn-lv-big" onclick="liji_duihuan('<?php echo $list['product_name']?>','<?php echo $list['id'];?>','<?php echo $list['score']?>')"><span>立即兑换</span></a>
					<?php }elseif($list['score'] > $list['my_credit']){?>
					<a class="btn-hui-big"><span>积分不足</span></a>
					<?php }?>
				<!--库存不够-->
				<?php }else{?>
					<a class="btn-hui-big"><span>库存不足</span></a>
				<?php }}?>
				</div>
			</div>
		</div>
	</div>

<!--询问操作确定弹窗-->
<div id="jss_pop_tip" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
					<span class="img"><img alt="" id="imgg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></span>
                    <p class="text" id="dialogSaveDiv"></p>
                    <button type="button" id = 'dialog_share' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
    </div>
</div>

<!--成功兑换提示-->
<div class="pop_box_g pop_see_inform pop_no_q_up" id='js_pop_msg1'>
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
				<div class="text-wrap">
					<table>
						<tr>
                            <td><div class="img"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></div></td>
                            <td class="msg"><span class="bold" id="dialogSaveSpan"></span></td>
                        </tr>
					</table>
				</div>
				<button class="btn-lv1 btn-left JS_Close" type="button" id='sure'>确定</button>
			 </div>
         </div>
    </div>
</div>

<!--操作结果弹出警告-->
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_warnig_tip'></p>
            </div>
        </div>
    </div>
</div>

<script>
	function liji_duihuan(name,id,score){
		var product_name = name;
		$('#dialogSaveDiv').html("您确定兑换"+product_name+"吗？");
		openWin('jss_pop_tip');
		$("#dialog_share").click(function(){
			$.ajax({
				url: "<?php echo MLS_URL;?>/gift_exchange/exchange/",
				type: "GET",
				dataType: "json",
				data: {
					gift_id:id,
					score:score
				},
				success: function(data) {
					if(data == 'ok')
					{
						$('#dialogSaveSpan').html("您已成功兑换"+name+"，请等待工作人员的联系。");
						openWin('js_pop_msg1');
					}else{

						$("#dialog_do_warnig_tip").html(data);
						openWin('js_pop_do_warning');
					}
				}
			});
		});

	}
	$(function(){
		$('#sure').click(function(){
			//关闭父窗口
			window.parent.closePopFun('js_pop_box_details');
		})
	})
</script>
