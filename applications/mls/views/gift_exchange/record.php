
<script>
    window.parent.addNavClass(10);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div id="js_search_box">
    <div  class="shop_tab_title">
        <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
		<a class="wh fr" href="javascript:void(0);" onclick="openWin('js_pop_rule')">兑换规则</a>
    </div>
</div>


<!--<div class="condition_box"  id="js_search_box_02">
	<span class="iconfont" style="color:#F5AD00; vertical-align:middle;">&#xe65b;</span> 我的积分：<span style="color:#ff7800"><?php echo $credit_total;?></span>
</div>-->
<div class="condition_box"  id="js_search_box_02">
	<span class="iconfont" style="color:#F5AD00; vertical-align:middle;">&#xe65b;</span> 我的积分：<span style="color:#ff7800"><?php echo $credit_total;?></span>
</div>


<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
              	<td class="c15"><div class="info">商品图片</div></td>
                <td class="c15"><div class="info">订单号</div></td>
                <td class="c15"><div class="info">名称</div></td>
                <td class="c15"><div class="info">详情</div></td>
                <td class="c15"><div class="info">积分</div></td>
                <td><div class="info">备注</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
			<?php
			if($gift_data){
				foreach($gift_data as $vo){?>
			<tr>
              	<td class="c15"><div class="info img-border"><img width="90" height="90" src="<?=$vo['product_picture']?>"></div></td>
                <td class="c15"><div class="info"><?=$vo['order']?></div></td>
                <td class="c15"><div class="info"><?=($vo['type'] == 1)?$vo['product_name']:'抽奖';?></div></td>
                <td class="c15"><div class="info"><a href="javascript:void(0);" onclick='instance_exchange("<?php echo $vo['gift_id']?>")'>查看</a></div></td>
                <td class="c15"><div class="info"><?=$vo['score']?></div></td>
                <td><div class="info"><?=($vo['type'] == 1)?'':$vo['product_name'];?></div></td>
            </tr>
			<?php }}else{ ?>
			<tr>
				<td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td>
			</tr>
			<?php }?>
        </table>
    </div>
</div>
<div id="js_fun_btn" class="fun_btn clearfix">
    <input type="hidden" class="input" name="page" value="1">
    <form action="" name="search_form" method="post" id="subform">
	<div class="get_page">
		<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
	</div>
    </form>
</div>

<!--详情弹框-->
<div id="js_pop_box_details" class="iframePopBox" style="width:760px;height:480px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="760" height="480" class='iframePop' src=""></iframe>
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

<!--在兑换的时候后台将商品下架/商品删除的弹窗-->
	<div class="pop_box_g pop_see_inform pop_no_q_up" id = 'down_pop' >
		<div class="hd">
			<div class="title">提示</div>
			<div class="close_pop">
				<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
			</div>
		</div>
		<div class="mod">
			<div class="inform_inner">
				<div class="up_inner">
					 <p class="text" id='dowm_del'></p>
				</div>
			</div>
		</div>
	</div>

<!--载入兑换页面-->
<?php $this->view('gift_exchange/exchange_rule');?>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->


<script>
	function instance_exchange(id){
		var _id = parseInt(id);
		var _url = '<?php echo MLS_URL;?>/gift_exchange/detail/'+ _id;
		$.ajax({
				url: _url + '/ajax/',
				type: "GET",
				dataType: "json",
				success: function(data) {
					if(data == 'down')
					{
						$('#dowm_del').html("商品已下架，请重新兑换其他商品");
						openWin('down_pop');
					} else if(data == 'del'){

						$('#dowm_del').html("商品已删除，请选择兑换其他商品");
						openWin('down_pop');
					} else {
						$("#js_pop_box_details .iframePop").attr("src",_url);
						openWin('js_pop_box_details');
					}
				}
			});

	}
</script>
