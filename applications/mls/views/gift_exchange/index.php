<!doctype html>
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

<div class="gift" id="js_inner">
<?php if($gift_data){?>
	<div class="gift-inner">
		<ul class="clearfix">
		<?php
			foreach($gift_data as $value){
		?>
			<li>
                <a href="javascript:void(0);" onclick='instance_exchange("<?php echo $value['id']?>")'>
				<img width="200" height="200" src="<?php echo str_replace('thumb/','',$value['product_picture'])?>">
				<p>
				<?php if(strlen($value['product_name'])>45){?>
					<?php echo utf8Substr($value['product_name'], 0, 15).'...'?>
				<?php }else{?>
					<?php echo $value['product_name'];?>
				<?php }?>
				</p>
				<div class="option"><span class="btn-lan fr"><span>立即兑换</span></span>积分<strong class="f00"><?php echo $value['score']?></strong></div>
                </a>
			</li>
		<?php }?>
		</ul>
	</div>
<?php }else{?>
	<span style="width:100%;height:auto;float:left;text-align:center;display:inline;" id="none_gift">
        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/no_gift_03.png" />
        <p>积分商品暂未上线，敬请期待哦！</p>
        <script type="text/javascript">
            $(function () {

                $("#none_gift").css("margin-top", ($(window).height() - 142)/6 + "px");

            })
        </script>
    </span>
<?php }?>
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
    <iframe frameborder="0" scrolling="no" width="760px" height="480px" class='iframePop' src=""></iframe>
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

<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/cooperate_common.js"></script>
<!--载入兑换页面-->
<?php $this->view('gift_exchange/exchange_rule');?>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->

<!--<script>
	function instance_exchange(id){
		var _id = parseInt(id);
		var _url = '<?php echo MLS_URL;?>/gift_exchange/detail/'+ _id;
		if(_url)
		{
			 $("#js_pop_box_details .iframePop").attr("src",_url);
		}
		openWin('js_pop_box_details');
	}
</script>-->
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

						setInterval(function(){
							window.location.reload();
						},2000);
					} else if(data == 'del'){

						$('#dowm_del').html("商品已删除，请选择兑换其他商品");
						openWin('down_pop');

						setInterval(function(){
							window.location.reload();
						},2000);
					} else {
						$("#js_pop_box_details .iframePop").attr("src",_url);
						openWin('js_pop_box_details');
					}
				}
			});

	}
</script>
