<div class="pop_box_g pop_box_g03 pop_box_g_border_none" id="js_keyuan" style="display:block">
    <div class="hd">
        <div class="title">我的客源</div>
        <!--<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>-->
    </div>
    <div class="mod">
        <div class="inner inner02">
            <div class="inner_ky_box">
			 <form action="" method="post" id="myform">
                <div class="title">客户姓名：<input type="text" class="input_t" name="cname"><button class="btn" onclick="$('#myform').submit();">查询</button>
                </div>
			</form>
                <table class="table">
                    <tr>
                        <th class="w45">&nbsp;</th>
                        <th class="w60">交易</th>
                        <th class="w160">客户编号</th>
                        <th class="w70">客户姓名</th>
                        <th>价格范围</th>
                    </tr>
                   
                  
                 <?php 
				 foreach($list as $key=>$var){
				 ?>
                    <tr class="bg">
                        <td><input type="radio" name="radio3" value="<?php echo $var['truename']?>">
						<input class="js_hidden_val" type="hidden" value="<?php echo $var['id']?>">
						</td>
                        <td>求租</td>
                        <td><?php echo format_info_id($var['id'], 'rent_customer');?></td>
                        <td ><?php echo $var['truename']?></td>
                        <td><?php echo $var['price_min']?>-<?php echo $var['price_max']?>万元</td>
                    </tr>
				 <?php }?>
                </table>
				
            </div>
		
            <div class="clearfix pop_fg_fun_box">
                <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
            </div>
            <a class="btn-lv1 btn-mid JS_Close mt10" onclick="opensource()">保存</a> </div>
    </div>
</div>
