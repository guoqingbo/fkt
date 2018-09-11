<script type="text/javascript">
$(function(){
    $('#district').change(function(){
    	var dist_name = $('#district').val();
    	if(dist_name){
            $.ajax({
            	type: 'get',
                url : '/entrust/street/'+dist_name,
                dataType:'json',
                cache:false,
                success: function(data){
                    if(data){
                    	var str = '<option value="0">不限</option>';
                    	var len = data.length;
                    	for(var i=0;i<len;i++){
                            str +='<option value="'+data[i].streetid+'">'+data[i].streetname+'</option>';
                        }
                    	$('#street').html(str);
                    }
                }
            });
    	}else{
    		$('#street').html("<option value='0'>不限</option>");
    	}
    });

    $('#search_entrust').click(function(){
    	$('input[name=page]').val(1);
        $('#search_form').submit();
        return false;
    });

    /*$('#reset_entrust').click(function(){
    	//$('#search_form')[0].reset();
    	$("#search_form").find(":input").not(":button,:submit,:reset,:hidden").val("").removeAttr("checked").removeAttr("selected");
    	$('#street').html("<option value='0'>不限</option>");
    	return false;
    });*/
});

window.parent.addNavClass(17);
</script>
<body>
<div class="tab_box" id="js_tab_box">
    <p class="right"><img width="16" height="16" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico2.png"> 什么是业主委托，我该怎么抢房源？<a href="#">查看帮助</a> </p>
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="search_box clearfix"  id="js_search_box_02">
	<form name="search_form" id="search_form" method="post" action="">
    <div class="fg_box">
        <p class="fg fg_tex">区属：</p>
        <div class="fg">
            <select id="district" name="district" class="select" style="width:100px">
				<option value='0'>不限</option>
				<?php foreach ($district as $value) { ?>
					<option value="<?=$value?>" <?php if($post_param['district']==$value){echo 'selected="selected"';}?>><?=$value?></option>
				<?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">板块：</p>
        <div class="fg">
            <select id="street" name="street" class="select" style="width:100px">
				<option value='0'>不限</option>
				<?php
                if(is_full_array($street)){
                    foreach($street as $v){
                ?>
                    <option value="<?=$v['streetid']?>" <?php if($post_param['street']==$v['streetid']){echo 'selected="selected"';}?>><?=$v['streetname']?></option>
                <?php
                    }
                }
                ?>
			</select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">面积：</p>
        <div class="fg">
            <input type="text" name="area_min" id="area_min" onkeyup="check_num()" value="<?=$post_param['area_min']?>" class="input w30">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" name="area_max" id="area_max" onkeyup="check_num()" value="<?=$post_param['area_max']?>" class="input w30">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="buildarea1_reminder"></span>
        </div>
        <p class="fg fg_tex fg_tex03">平米</p>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">总价：</p>
        <div class="fg">
            <input type="text" name="price_min" id="price_min" onkeyup="check_num()" value="<?=$post_param['price_min']?>" class="input w30">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" name="price_max" id="price_max" onkeyup="check_num()" value="<?=$post_param['price_max']?>" class="input w30">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="price1_reminder"></span>
        </div>
        <p class="fg fg_tex fg_tex03">万元</p>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">户型：</p>
        <div class="fg">
            <select class="select" id="room" name="room">
                <option value="0" <?php if($post_param['room']==0){echo 'selected="selected"';}?>>不限</option>
                <option value="1" <?php if($post_param['room']==1){echo 'selected="selected"';}?>>一室</option>
                <option value="2" <?php if($post_param['room']==2){echo 'selected="selected"';}?>>二室</option>
                <option value="3" <?php if($post_param['room']==3){echo 'selected="selected"';}?>>三室</option>
                <option value="4" <?php if($post_param['room']==4){echo 'selected="selected"';}?>>四室</option>
                <option value="5" <?php if($post_param['room']==5){echo 'selected="selected"';}?>>五室</option>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 朝向：</p>
        <div class="fg">
            <select class="select" id="forward" name="forward">
                <option value="0" <?php if($post_param['forward']==0){echo 'selected="selected"';}?>>不限</option>
                <option value="东" <?php if($post_param['forward']=='东'){echo 'selected="selected"';}?>>东</option>
                <option value="西" <?php if($post_param['forward']=='西'){echo 'selected="selected"';}?>>西</option>
                <option value="南" <?php if($post_param['forward']=='南'){echo 'selected="selected"';}?>>南</option>
                <option value="北" <?php if($post_param['forward']=='北'){echo 'selected="selected"';}?>>北</option>
                <option value="东西" <?php if($post_param['forward']=='东西'){echo 'selected="selected"';}?>>东西</option>
                <option value="南北" <?php if($post_param['forward']=='南北'){echo 'selected="selected"';}?>>南北</option>
                <option value="东南" <?php if($post_param['forward']=='东南'){echo 'selected="selected"';}?>>东南</option>
                <option value="东北" <?php if($post_param['forward']=='东北'){echo 'selected="selected"';}?>>东北</option>
                <option value="西南" <?php if($post_param['forward']=='西南'){echo 'selected="selected"';}?>>西南</option>
                <option value="西北" <?php if($post_param['forward']=='西北'){echo 'selected="selected"';}?>>西北</option>
            </select>
        </div>
    </div>
	<input type="hidden" name="page" value="1">
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" id="search_entrust" class="btn"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="/entrust/index/" id="reset_entrust" class="reset">重置</a> </div>
    </div>
    </form>
</div>

<div class="table_all">
    <div class="inner shop_inner" id="js_inner">
        <table class="table table2">
            <?php
            if($entrust_list){
                foreach ($entrust_list as $key=>$value){
            ?>
			<tr>
				<td width="2%">
				    <div class="info" style="text-align:right;">
				    <?php if($value['pics']){?>
                        <span title="此房源有图片" class="iconfont ts"></span>
                    <?php }?>
				    </div>
			    </td>
				<td width="13%">
					<div class="info">
                        <div class="tit-1"><?=$value['blockname'];?></div>
                    </div>
				</td>
                <td width="19%"><div class="info" style="text-align:left;"><?=$value['district_street']?> | <?=$value['housetype']?> | <?=strip_end_0($value['buildarea'])?>平米</div></td>
                <td width="15%"><div class="info"><em class="num-16"><?=strip_end_0($value['price'])?></em> 万</div></td>
                <td width="20%"><div class="info"><?=date('Y-m-d H:i:s', $value['begtime'])?></div></td>
                <td width="20%"><div class="info">已有<em class="num-12"><?=$value['num']?></em>人抢拍，还剩<em class="num-12"><?=$value['remain_num']?></em>个名额</div></td>
  				<td><div class="info">
  				<?php
                if($value['status'] == 1){
                ?>
  				<a class="btn-lan" href="<?='/entrust/entrust_detail/'.$value['id']?>"><span>查看</span></a>
  				<?php }?>
  				</div></td>
            </tr>
			<?php
                }
            }else{
                echo '<tr><td colspan="6">暂无委托房源</td></tr>';
			}
			?>
        </table>
    </div>
</div>

<div id="js_fun_btn" class="fun_btn fun_btn_bottom clearfix">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>

<div id="js_pop_do_warning"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_warnig_tip">操作失败！</p>
			</div>
		</div>
	</div>
</div>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
