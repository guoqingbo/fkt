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
			<span class="tab_ajd"><a href="/finance/my_customer/pledge/">抵押贷</a></span>
			<!--<span class="tab_ajd tab_dyd current"><a href="/finance/my_customer/mortgage/">按揭贷</a></span>-->
			<span class="tab_ajd tab_dyd current"><a href="/finance/my_customer/rental/">租金贷</a></span>
		</div>
        <div class="table_mains">
            <div class="table_vv dyd_table">
              <table class="jr_table">
                  <thead>
                      <tr>
                          <th width="8%">借款人</th>
                          <th>手机号码</th>
                          <th>借款金额（元）</th>
                          <th>提交时间</th>
                          <th>申请状态</th>
                          <th></th>
                      </tr>
                  </thead>
                  <tbody class="jr_table_tb">
                    <?php
                        if($customer_list['list']){
                            foreach($customer_list['list'] as $key=>$value){
                                ?>
                                <tr class="<?php if($k%2 == 0){?>color1<?php }else{?>color2<?php }?>">
                                    <td><?=$value['tenant_name']?></td>
                                    <td><?=$value['tenant_phone']?></td>
                                    <td class="c_money"><?=$value['tenant_price']?></td>
                                    <td class="c_time">
                                        <?php if($value['create_dateline']){?>
                                        <?php echo date("Y-m-d",$value['create_dateline']);?><br/><?php echo date("H:i:s",$value['create_dateline']);?>
                                        <?php }else{?>
                                        --
                                        <?php }?>
                                    </td>
                                    <td><?=$value['status_str']?></td>
                                    <td class="color5 zj_upload">
									<?php
										if('4' == $value['step'] && '1' == $value['status']){
										?>
										扫二维码下载浦发APP
										<?php
										}
									?>
									</td>
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
              <div class="zg_pf">
                <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dyd/pf.jpg" />
              </div>
            </div>
        </div>
    </div>
</form>
</div>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/openWin.js" type="text/javascript"></script>
<script>
	window.onload = function(){
		setInterval(function(){
			var winHeight = $(window).height()-60;
			$('.wrapper').css('height',winHeight);
		},500);

		$('.zg_pf').click(function(){
			$(this).hide();
		});

		$('.zj_upload').click(function(){
			$('.zg_pf').show();
		});
	}
</script>
