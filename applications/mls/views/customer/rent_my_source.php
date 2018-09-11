<div class="pop_box_g pop_box_g03" id="js_keyuan" style="display:block; border:none;">
    <div class="hd">
        <div class="title">我的房源</div>
    </div>
    <div class="mod">
        <div class="inner inner02">
			<form action="" method="post" id="myform" name="search_form">
            <div class="inner_ky_box">
                <div class="title">房源楼盘：<input type="text" class="input_t" name="cname" value="<?php echo $cname; ?>"><button class="btn" onclick="$('#myform').submit();">查询</button>
                </div>
                <table class="table">
                    <tr>
                        <th class="w45">&nbsp;</th>
                        <th class="w60">交易</th>
                        <th class="w160">房源编号</th>
                        <th class="w70">业主姓名</th>
                        <th>总价</th>
                    </tr>
                 <?php 
				 if($list){
                    foreach($list as $key=>$var){
				 ?>
                    <tr class="bg">
                        <td><input type="radio" name="radio3" value="<?php echo 'CS'.$var['id']?>">
						<input class="js_hidden_val" type="hidden" value="<?php echo $var['id']?>">
						</td>
                        <td>租</td>
                        <td><?php echo 'CZ'.$var['id']?></td>
                        <td ><?php echo $var['owner']?></td>
                        <td><?php echo strip_end_0($var['price'])?>元/月</td>
                    </tr>
					
				 <?php }
                    }else{
                 ?>
				 <tr><td colspan=5>很遗憾，没有找到相关房源哦!</td></tr>
				 <?php } ?>
                </table>
				
            </div>
			<?php if($list){?>
            <div class="clearfix pop_fg_fun_box">
                <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
            </div>
			<?php }?>
			</form>
            <a class="btn-lv1 btn-mid JS_Close mt10" onclick="opensource()" date-iframe ="1" href="javascript:void(0)">确定</a> </div>
    </div>
</div>