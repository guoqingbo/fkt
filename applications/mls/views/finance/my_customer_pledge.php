<script>
    window.parent.addNavClass(24);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="wrapper jr" style="margin:auto;background:#fff;margin-top:20px;">
<form action='/finance/my_customer/pledge/' method='post' id='search_form' name='search_form'>
    <div class="jr_custom">
        <div class="tab">
			<span class="tab_ajd current"><a href="/finance/my_customer/pledge/">抵押贷</a></span>
			<!--<span class="tab_ajd tab_dyd current"><a href="/finance/my_customer/mortgage/">按揭贷</a></span>-->
			<span class="tab_ajd tab_dyd"><a href="/finance/my_customer/rental/">租金贷</a></span>
		</div>
        <div class="table_mains">
            <div class="table_vv dyd_table">
                 <table class="jr_table">
                    <thead>
                      <tr>
                          <th width="8%">借款人</th>
                          <th>手机号码</th>
                          <th>房产小区</th>
                          <th>进件时间</th>
                          <th>房屋总价（元）</th>
                          <th>意向额度（元）</th>
                          <th>实际额度（元）</th>
                          <th>状态</th>
                          <th>操作</th>
                      </tr>
                    </thead>
                    <tbody class="jr_table_tb">
                    <?php
                        if($customer_list['list']){
                            foreach($customer_list['list'] as $key=>$value){
                                ?>
                                <tr class="<?php if($k%2 == 0){?>color1<?php }else{?>color2<?php }?>">
                                    <td><?=$value['borrower']?></td>
                                    <td><?=$value['phone']?></td>
                                    <td><?=$value['block_name']?></td>
                                    <td class="c_time">
                                        <?php if($value['create_dateline']){?>
                                        <?php echo date("Y-m-d",$value['create_dateline']);?><br/><?php echo date("H:i:s",$value['create_dateline']);?>
                                        <?php }else{?>
                                        --
                                        <?php }?>
                                    </td>
                                    <td class="c_money"><?=$value['price']?>万</td>
                                    <td class="c_money"><?=$value['intentional_money']?>万</td>
                                    <td class="c_money"><?=$value['actual_amount']?>万</td>
                                    <td><?=$value['status_str']?></td>
                                    <td class="color5"><a href="javascript:void(0)" onclick="look_progress(<?=$value['id']?>)">查看进度</a></td>
                                </tr>
                                <?php
                            }
                        }
                    ?>
                    </tbody>
                </table>
                <div class="bt_custom_nav clearfix">
                     <div style='float:right'><?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?></div>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
<script>

function look_progress(id)
{
	var url = "<?php echo MLS_URL;?>/finance/progress/pledge?id="+id
	/*$.ajax({
		url: url,
		type: "GET",
		dataType: "json",
		//data: {id:id},
		success: function(data) {
			if(data['result'] == 'ok')
			{

			}
		}
	});*/
	if(url){
		$("#js_progress").find(".iframePop").attr("src",url);
				openWin('js_progress');
	}
}
</script>
<!--查看进度弹窗-->
<div id="js_progress" class="iframePopBox" style="width:660px;height:432px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="660px" height="432px" class='iframePop' src=""></iframe>
</div>

<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/openWin.js" type="text/javascript"></script>
<script>
	window.onload = function(){
		setInterval(function(){
			var winHeight = $(window).height()-60;
			$('.wrapper').css('height',winHeight);
		},500);
	}
</script>
