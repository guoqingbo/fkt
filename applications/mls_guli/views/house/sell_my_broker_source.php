<div class="pop_box_g pop_box_g03" id="js_keyuan" style="width: 530px;display:block; border:none;">
    <div class="hd">
        <div class="title">选择经纪人</div>
    </div>
    <div class="mod">
        <div class="inner inner02">
            <div class="inner_ky_box">
			<form action="" method="post" id="myform">
                <div class="title">
                    门店：<select id="list_broker" name="post_agency_id" class="select">
                            <option value="0">不限</option>
                            <?php foreach($agency_list as $key => $value){ ?>
                            <option value="<?php echo $value['id']; ?>" <?php if($post_agency_id==$value['id']){echo 'selected="selected"';} ?> ><?php echo $value['name']; ?></option>
                            <?php } ?>
					      </select>
                    &nbsp;&nbsp;&nbsp;
                    姓名：<input type="text" class="input_t" name="cname" value="<?php echo empty($cname)?'可搜索姓名':$cname; ?>" onfocus="Onfocus()" onblur="Onblur()" id="search"><button class="btn" onclick="$('#myform').submit();">查询</button>
                </div>
			</form>
                <table class="table">
                    <tr>
                        <th class="w45">&nbsp;</th>
                        <th class="w60">姓名</th>
                        <th class="w160">手机号</th>
                        <th class="w70">所属门店</th>
                    </tr>
                 <?php 
				 if($list){
                    foreach($list as $key=>$var){
				 ?>
                    <tr class="bg">
                        <td><input type="radio" name="radio3" value="<?php echo $var['truename']?>">
						<input class="js_hidden_val" type="hidden" value="<?php echo $var['broker_id']?>">
						</td>
                        <td><?php echo $var['truename']; ?></td>
                        <td><?php echo $var['phone']; ?></td>
                        <td ><?php echo $var['agency_id']?></td>
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
            <a class="btn-lv1 btn-mid JS_Close mt10" onclick="opensource_broker()" date-iframe ="1" href="javascript:void(0)">确定</a> </div>
    </div>
</div>
<script type="text/javascript">
var Search=document.getElementById("search");
function Onfocus()
{
    if(Search.value=="可搜索姓名")
    {
        Search.value="";
    }
}
function Onblur()
{
    if(Search.value=="")
    {
        Search.value="可搜索姓名";
    }
}
</script>