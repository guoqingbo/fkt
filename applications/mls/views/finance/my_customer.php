<script>
    window.parent.addNavClass(24);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="wrapper jr" style="margin:auto;background:#fff;margin-top:20px;">
<form action='/finance/my_customer/mortgage/' method='post' id='search_form' name='search_form'>
    <div class="jr_custom">
        <div class="tab">
			<span class="tab_ajd"><a href="/finance/my_customer/pledge/">抵押贷</a></span>
			<!--<span class="tab_ajd tab_dyd current"><a href="/finance/my_customer/mortgage/">按揭贷</a></span>-->
		</div>
        <div class="table_mains">
            <div class="table_vv ajd_table">
                <table class="jr_table">
                    <thead>
                        <tr>
                            <th width="8%">借款人</th>
                            <th>手机号码</th>
                            <th>进件时间</th>
                            <th>预约时间</th>
                            <th>下款时间</th>
                            <th>银行</th>
                            <th>支行</th>
                            <th>贷款总额&nbsp;(元)</th>
                            <th>评估费&nbsp;(元)</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody class="jr_table_tb">
                    <?php
                        if($customer_list['list']){
                            foreach($customer_list['list'] as $key=>$value){
                    ?>
                        <tr <?php if($k%2 == 0){?>class="color1"<?php }else{?>class="color2"<?php }?>>
                            <td><?=$value['borrower']?></td>
                            <td><?=$value['borrower_phone']?></td>
                            <td class="c_time"><?php echo date("Y-m-d",$value['create_dateline']);?><br/><?php echo date("H:i:s",$value['create_dateline']);?></td>
                            <td class="c_time">
                                <?php if($value['apnt_dateline']){?>
                                <?php echo date("Y-m-d",$value['apnt_dateline']);?><br/><?php echo date("H:i:s",$value['apnt_dateline']);?>
                                <?php }else{?>
                                --
                                <?php }?>
                            </td>
                            <td class="c_time">
                                <?php if($value['give_money_dateline']){?>
                                <?php echo date("Y-m-d",$value['give_money_dateline']);?><br/><?php echo date("H:i:s",$value['give_money_dateline']);?>
                                <?php }else{?>
                                --
                                <?php }?>
                            </td>
                            <td><?=$value['bank_last_name']?></td>
                            <td><?=$value['bank_first_name']?></td>
                            <td class="color3"><?php if($value['total_loan'] != 0){echo strip_end_0($value['total_loan']).'万';}else{echo "--";}?></td>
                            <td class="color3"><?php if($value['evaluate_cost'] != 0){?><?=strip_end_0($value['evaluate_cost'])?><?php }else{?>--<?php }?></td>
                            <td class="color4 <?php if($value['status'] == 1){echo "pizhun";}?>"><?php if($value['status'] == 0){echo "审核中";}elseif($value['status'] == 1){echo "已批准";}else{echo "审核拒绝";}?></td>
                            <td class="color5">
                                <span><a href="javascript:void(0)" onclick="look_progress(<?=$value['id']?>)">查看进度</a></span>
                                &nbsp;&nbsp;&nbsp;
                                <a href="/finance/modify/<?=$value['id']?>">修改资料</a>
                            </td>
                        </tr>
                    <?php }}?>
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
	var url = "<?php echo MLS_URL;?>/finance/progress/"+id
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
        var winHeight = $(window).height();
        $('.wrapper').css('height',winHeight);
    }
</script>
