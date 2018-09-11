<div class="pop_box_g pop_box_g03" id="js_keyuan" style="width: 530px;display:block; border:none;">
    <div class="hd">
        <div class="title">我的客源</div>
    </div>
    <div class="mod">
        <div class="inner inner02">
            <div class="inner_ky_box">
			<form action="" method="post" id="myform">
                <div class="title">客户姓名：<input type="text" class="input_t" name="cname" value="<?php echo empty($cname)?'可搜索姓名或电话':$cname; ?>" onfocus="Onfocus()" onblur="Onblur()" id="search"><button class="btn" onclick="$('#myform').submit();">查询</button>
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
				 if($list){
                    foreach($list as $key=>$var){
				 ?>
                    <tr class="bg">
                        <td><input type="radio" name="radio3" value="<?php echo $var['truename']?>">
						<input class="js_hidden_val" type="hidden" value="<?php echo $var['id']?>">
						</td>
                        <td>买</td>
                        <td><?php echo 'QG'.$var['id']?></td>
                        <td ><?php echo $var['truename']?></td>
                        <td><?php echo strip_end_0($var['price_min'])?>-<?php echo strip_end_0($var['price_max'])?>W</td>
                    </tr>
					
				 <?php }
                    }else{
                 ?>
				 <tr><td colspan=5>很遗憾，没有找到相关客源哦!</td></tr>
				 <?php } ?>
                </table>
				
            </div>
			<?php if($list){?>
            <div class="clearfix pop_fg_fun_box">
                <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
            </div>
			<?php }?>
            <a class="btn-lv1 btn-mid JS_Close mt10" onclick="opensource(<?php echo $type; ?>)" date-iframe ="1" href="javascript:void(0)">确定</a> </div>
    </div>
</div>
<script type="text/javascript">
var Search=document.getElementById("search");
function Onfocus()
{
    if(Search.value=="可搜索姓名或电话")
    {
        Search.value="";
    }
}
function Onblur()
{
    if(Search.value=="")
    {
        Search.value="可搜索姓名或电话";
    }
}
</script>