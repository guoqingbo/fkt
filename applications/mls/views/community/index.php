<script src="<?php echo MLS_SOURCE_URL;?>/common/third/My97DatePicker/WdatePicker.js"></script>
<script>
    window.parent.addNavClass(9);
</script>
<!--导航栏-->
<div class="tab_box" id="js_tab_box">
<?php
    echo $user_menu;
?>
</div>
<!--搜索区-->
<form method="POST" action="" name="search_form">
<div class="search_box clearfix" id="js_search_box_02">
    <div class="loupan_city">城市：<?php echo $this_user['cityname'];?></div>
 	<div class="fg_box">
        <p class="fg fg_tex"> 楼盘：</p>
        <div class="fg">
            <input type="text" class="input w200" name="strcode" id="block_name" value="<?php echo $strcode;?>">
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" id="search_by_cmtname" onclick="return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="openWin('js_newinfo')"><span class="btn_inner">新增</span></a> </div>
    </div>
</div>
<script>
$(function(){
	document.onkeydown = function(e){ //enter
		var ev = document.all ? window.event : e;
		if(ev.keyCode==13) {
			$('form[name="search_form"]').submit();return false;
		 }
	}
});
</script>
<!--主要内容-->

<div class="table_all tableallborder">
    <div id="js_inner" class="loupan_imlist" >
     <style>
     	.zd_pic{ padding:0 10px; float:left; width:132px; height:99px; overflow:hidden;}
		.zd_info{ width:1050px; float:left; line-height:30px;}
		.f_d{ float:left; width:380px;}
		.f_d span{ color:#1f6dcd; font-size:14px; font-weight:bold;}
     </style>

                    <ul style="margin-bottom:-4px;">
                        <?php
                        if(isset($community2) && !empty($community2)){
                            foreach($community2 as $k=>$v){
                        ?>
                        <li class="clearfix">


                            <div class="zd_pic"><img style="height:99px;width:132px;"class="js_img" src="<?php echo $v['surface_img'];?>" onclick="$('#js_tupian .iframePop').attr('src','<?php echo MLS_URL;?>/community/img_detail/'+'<?php echo $v['id'];?>');openWin('js_tupian');event.stopPropagation();"></div>

                                <div class="zd_info" onclick="openWin('js_information<?php echo $v['id'];?>');">
                                	<div class="clearfix ">
                                    	<div class="f_d"><span><?php echo $v['cmt_name'];?></span></div>
                                   	 	<div class="left" style="width:145px;">区属：<?php echo $v['dist_name'];?></div>
                                		<div class="left" style="width:145px;">板块：<?php echo $v['street_name'];?></div>
                                    	<div class="left" style="width:200px;">类型：<?php echo $v['build_type'];?></div>
                                    	<div class="left" style="width:170px;">建筑日期：<?php echo date('Y年',$v['build_date']);?></div>
                                    </div>

                                    <div class="clearfix ">
                                    	<div class="f_d">开发商：<?php echo $v['developers'];?></div>
                                   	 	<div class="left" style="width:145px;">占地：<?php echo $v['coverarea'];?>M²</div>
                                		<div class="left" style="width:145px;">车位：<?php echo $v['parking'];?></div>
                                    	<div class="left" style="width:200px;">物管：<?php echo $v['property_company'];?></div>
                                    	<div class="left" style="width:170px;">绿化：<?php echo ($v['green_rate']*100).'%';?></div>
                                    </div>


                                     <div class="clearfix ">
                                    	<div class="f_d">配套：<?php echo $v['facilities_path'];?></div>
                                   	 	<div class="left" style="width:145px;">容积率：<?php echo $v['plot_ratio'];?></div>
                                		<div class="left" style="width:145px;">套数：<?php echo $v['total_room'];?></div>
                                    	<div class="left" style="width:200px;">交通：<?php echo $v['traffic_path'];?></div>
                                    </div>
                                </div>
                        </li>
                        <?php }} ?>
                    </ul>

    </div>
</div>

<!--页码导航-->
<div id="js_fun_btn" class="fun_btn fun_btn_bottom clearfix">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
  </div>
</form>

<!--图片详情弹框-->
<div id="js_tupian" class="iframePopBox" style="height:512px;">
	<a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1"></a>
    <iframe frameborder="0" scrolling="no" width="700" height="512" class='iframePop' src=""></iframe>
</div>
<?php
            if(isset($community2) && !empty($community2)){
                foreach($community2 as $k=>$v){
?>
<!--详细资料-->
<div class="pop_box_g information_popup" id="js_information<?php echo $v['id'];?>">
	<div class="hd">
        <div class="title">详细资料</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="information_content">
        <input type="hidden" name="cmt_id" value="<?php echo $v['id'];?>"/>
        <input type="hidden" value="0" id="js_information_input">
    	<table class="table if_table" id="modifynew">
    		<tr>
    			<td width="72" class="ifw1"><font color="red">*</font>楼盘名称：</td>
    			<td width="260" class="ifw2" id="td_editor<?php echo $k.'1';?>">
    				<span class="md md1"><?php echo $v['cmt_name'];?></span><input class="amd mdh" name="cmt_name" id="modify_cmt_name<?php echo $k;?>" type="text" value="<?php echo $v['cmt_name'];?>">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="楼盘名称"/>
                    <span class="mdone iconfont" title="编辑">&#xe62b;</span>
                </td>
    			<td width="72" class="ifw3"><font color="red">*</font>区属：</td>
    			<td width="200" class="ifw4" id="td_editor<?php echo $k.'2';?>">
    				<span class="md md1"><?php echo $v['dist_name']; ?></span>
    				<select id="district<?php echo $k;?>" name="dist_id" class="amd mdh">
                        <option value="">请选择</option>
                        <?php foreach ($district as $key => $val) { ?>
                            <option value="<?php echo $val['id'] ?>"<?php if($val['id']==$v['dist_id']){echo 'selected="selected"';}?>><?php echo $val['district'] ?></option>
                        <?php } ?>
    				</select>
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="区属"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1"><font color="red">*</font>板块：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'3';?>">
                    <span class="md md1"><?php echo $v['street_name'];?></span>
    				<select id="street<?php echo $k;?>" name="streetid" class="select amd mdh">
                        <option value="">请选择</option>
                        <?php foreach ($v['street_arr'] as $key => $val) { ?>
                            <option value="<?php echo $val['id'] ?>"<?php if($val['id']==$v['streetid']){echo 'selected="selected"';}?>><?php echo $val['streetname'] ?></option>
                        <?php } ?>
    				</select>
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="板块"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3"><font color="red">*</font>楼盘地址：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'4';?>">
    				<span class="md md1"><?php echo $v['address'];?></span>
    				<input type="text" id="modify_address<?php echo $k;?>" name="address" value="<?php echo $v['address'];?>" class="w120 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="楼盘地址"/>
                    <span class="mdone iconfont" style="display:none; cursor: pointer;">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">物业类型：</td>
    			<td class="ifw2" style="white-space:nowrap; " id="td_editor<?php echo $k.'5';?>">
                    <input type="checkbox" value="住宅" name="build_type[]" <?php if(strchr($v['build_type'], '住宅')){?>checked='checked'<?php } ?> disabled="disabled">住宅<input type="checkbox" value="别墅" name="build_type[]" <?php if(strchr($v['build_type'], '别墅')){?>checked='checked'<?php } ?> disabled="disabled">别墅<input type="checkbox" value="写字楼" name="build_type[]" <?php if(strchr($v['build_type'], '写字楼')){?>checked='checked'<?php } ?> disabled="disabled">写字楼<input type="checkbox" value="商铺" name="build_type[]" <?php if(strchr($v['build_type'], '商铺')){?>checked='checked'<?php } ?> disabled="disabled">商铺<input type="checkbox" value="厂房仓库" name="build_type[]" <?php if(strchr($v['build_type'], '厂房仓库')){?>checked='checked'<?php } ?> disabled="disabled">厂房仓库
    			</td>
    			<td class="ifw3"><font color="red">*</font>建筑年代：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'6';?>">
    				<span class="md md1"><?php echo date('Y',$v['build_date']);?></span>
                    <input type="text" class="w80 amd mdh" name="build_date" value="<?php echo date('Y',$v['build_date']);?>"/>年
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="建筑年代"/>
                    <span class="mdone iconfont" style="display:none; cursor: pointer;">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">占地：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'7';?>">
    				<span class="md md1"><?php echo $v['coverarea'];?></span>
    				<input type="text" value="<?php echo $v['coverarea'];?>" name="coverarea" class="w60 amd mdh">&nbsp M²
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="占地"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3">产权年限：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'8';?>">
    				<span class="md md1"><?php echo $v['property_year'];?></span>
    				<input type="text" value="<?php echo $v['property_year'];?>" name="property_year" class="w60 amd mdh">年
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="产权年限"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">物业公司：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'9';?>">
    				<span class="md md1"><?php echo $v['property_company'];?></span>
    				<input type="text" value="<?php echo $v['property_company'];?>" name="property_company" class="w100 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="物业公司"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3">开发商：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'10';?>">
    				<span class="md md1"><?php echo $v['developers'];?></span>
    				<input type="text" value="<?php echo $v['developers'];?>" name="developers" class="w120 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="开发商"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">停车位：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'11';?>">
    				<span class="md md1"><?php echo $v['parking'];?></span>
    				<input type="text" value="<?php echo $v['parking'];?>" name="parking" class="w60 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="停车位"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3">绿化率：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'12';?>">
    				<span class="md md1"><?php echo $v['green_rate']*100;?></span>
    				<input type="text" value="<?php echo $v['green_rate']*100;?>" name="green_rate" class="w60 amd mdh">%
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="绿化率"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">容积率：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'13';?>">
    				<span class="md md1"><?php echo $v['plot_ratio'];?></span>
    				<input type="text" value="<?php echo $v['plot_ratio'];?>" name="plot_ratio" class="w60 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="容积率"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3">物业费：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'14';?>">
    				<span class="md md1"><?php echo $v['property_fee'];?></span>
    				<input type="text" value="<?php echo $v['property_fee'];?>" name="property_fee" class="w60 amd mdh">元/m²
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="物业费"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">总栋数：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'15';?>">
    				<span class="md md1"><?php echo $v['build_num'];?></span>
    				<input type="text" value="<?php echo $v['build_num'];?>" name="build_num" class="w60 amd mdh">栋
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="总栋数"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3">总户数：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'16';?>">
    				<span class="md md1"><?php echo $v['total_room'];?></span>
    				<input type="text" value="<?php echo $v['total_room'];?>" name="total_room" class="w60 amd mdh">户
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="总户数"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">楼层状况：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'17';?>">
    				<span class="md md1"><?php echo $v['floor_instruction'];?></span>
    				<input type="text" value="<?php echo $v['floor_instruction'];?>" name="floor_instruction" class="w60 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="楼层状况"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3"></td>
    			<td class="ifw4"></td>
    		</tr>
    		<tr>
    			<td class="ifw1">楼盘介绍：</td>
    			<td colspan="3" class="ifcolor1" id="td_editor<?php echo $k.'18';?>">
    				<span class="md"><?php echo $v['introduction'];?></span>
                    <textarea class="iftextarea amd mdh" name="introduction"><?php echo $v['introduction'];?></textarea>
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="楼盘介绍"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">配套：</td>
    			<td class="ifcolor1" colspan="3" id="td_editor<?php echo $k.'19';?>">
    				<span class="md"><?php echo $v['facilities'];?></span>
    				<input type="text" value="<?php echo $v['facilities'];?>" name="facilities" class="w370 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="配套"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">交通：</td>
    			<td class="ifcolor1" colspan="3" id="td_editor<?php echo $k.'20';?>">
    				<span class="md"><?php echo $v['traffic'];?></span>
    				<span class="amd mdh">地铁</span><input type="text" value="<?php echo $v['subway'];?>" name="subway" id="subway" class="w180 amd mdh">
    				<span class="amd mdh">公交</span><input type="text" value="<?php echo $v['bus_line'];?>" name="bus_line" id="bus_line" class="w180 amd mdh">
    				<input type="hidden" value="" name="traffic" class="w370 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
    			</td>
    		</tr>
    	</table>
    </div>
</div>
            <?php }} ?>

<!--新增资料-->
<div class="pop_box_g information_popup" id="js_newinfo">
	<div class="hd">
        <div class="title">新增资料</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="information_content">
    	<table class="table if_table">
    		<tr>
    			<td class="ifw1">楼盘名称<font color="red">*</font>：</td>
    			<td class="ifw2"><input type="text" class="input" name="cmt_name" id="add_cmt_name"></td>
    			<td class="ifw3">区属<font color="red">*</font>：</td>
    			<td class="ifw4">
    				<select id="add_cmt_district" name="dist_id" class="select">
                        <option value="">请选择</option>
                        <?php foreach ($district as $key => $val) { ?>
                            <option value="<?php echo $val['id'] ?>" ><?php echo $val['district'] ?></option>
                        <?php } ?>
    				</select>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">板块<font color="red">*</font>：</td>
    			<td class="ifw2">
                    <select class="select" id="add_cmt_street" name="streetid">
                        <option value="">请选择</option>
    				</select>
    			</td>
    			<td class="ifw3">楼盘地址<font color="red">*</font>：</td>
    			<td class="ifw4"><input type="text" class="input" name="address" id="add_address"></td>
    		</tr>
    		<tr>
    			<td class="ifw1">物业类型：</td>
    			<td class="ifw2">
                    <input type="checkbox" value="住宅" name="build_type[]" id="add_build_type1">住宅
                    <input type="checkbox" value="别墅" name="build_type[]" id="add_build_type2">别墅
                    <input type="checkbox" value="写字楼" name="build_type[]" id="add_build_type3">写字楼
                    <input type="checkbox" value="商铺" name="build_type[]" id="add_build_type4">商铺
                    <input type="checkbox" value="厂房仓库" name="build_type[]" id="add_build_type5">厂房仓库
    			</td>
    			<td class="ifw3">建筑年代<font color="red">*</font>：</td>
    			<td class="ifw4">
                    <input type="text" class="input w80" name="build_date" id="add_build_date" value=""/>（例：1980）
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">占地：</td>
                <td class="ifw2"><input type="text" class="input w60" name="coverarea" id="add_coverarea">亩</td>
    			<td class="ifw3">产权年限：</td>
    			<td class="ifw4"><input type="text" class="input w60" name="property_year" id="add_property_year">年</td>
    		</tr>
    		<tr>
    			<td class="ifw1">物业公司：</td>
    			<td class="ifw2"><input type="text" class="input w120" name="property_company" id="add_property_company"></td>
    			<td class="ifw3">开发商：</td>
    			<td class="ifw4"><input type="text" class="input w150" name="developers" id="add_developers"></td>
    		</tr>
    		<tr>
    			<td class="ifw1">停车位：</td>
    			<td class="ifw2"><input type="text" class="input w60" name="parking" id="add_parking">个</td>
    			<td class="ifw3">绿化率：</td>
    			<td class="ifw4"><input type="text" class="input w60" name="green_rate" id="add_green_rate">%</td>
    		</tr>
    		<tr>
    			<td class="ifw1">容积率：</td>
    			<td class="ifw2"><input type="text" class="input w60" name="plot_ratio" id="add_plot_ratio"></td>
    			<td class="ifw3">物业费：</td>
    			<td class="ifw4"><input type="text" class="input w60" name="property_fee" id="add_property_fee">元/m²</td>
    		</tr>
    		<tr>
    			<td class="ifw1">总栋数：</td>
    			<td class="ifw2"><input type="text" class="input w60" name="build_num" id="add_build_num">栋</td>
    			<td class="ifw3">总户数：</td>
    			<td class="ifw4"><input type="text" class="input w60" name="total_room" id="add_total_room">户</td>
    		</tr>
    		<tr>
    			<td class="ifw1">楼层状况：</td>
    			<td class="ifw2"><input type="text" class="input w60" name="floor_instruction" id="add_floor_instruction">层</td>
    			<td class="ifw3"></td>
    			<td class="ifw4"></td>
    		</tr>
    		<tr>
    			<td class="ifw1">楼盘介绍：</td>
    			<td colspan="3" class="ifcolor1">
    				<textarea class="iftextarea" name="introduction" id="add_introduction"></textarea>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">配套：</td>
    			<td colspan="3"><input type="text" class="input w260" name="facilities" id="add_facilities"></td>
    		</tr>
    		<tr>
    			<td class="ifw1">交通：</td>
    			<td colspan="3"><input type="text" class="input w260"></td>
    		</tr>
            <tr>
                <td colspan="6">
                    <div style="color:red;" id="xqerror" class="errorBox clear"></div>
                </td>
            </tr>
    	</table>
    </div>
      	<div class="photo_anniu" style="width:90px;text-align:center;margin:0 auto;">
			<a href="#" class="photo_btn btn_l" id="add_cmt_button">我要添加</a>
		</div>

</div>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="sub_form();" title="关闭" class="JS_Close iconfont" id="close_refresh"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'></p>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    //搜索
    $('#search_by_cmtname').click(function(){
        $('form[name="search_form"]').submit();
    });

    /*编辑按钮显示、隐藏动作
    $('td[id^="td_editor"]').hover(function(){
        var _val = $("#js_information_input").val();
        if(_val==0 || $(this).find('.mdone').hasClass('mdcolor'))
        {
            $(this).find('.mdone').show();
        }
    });*/
	$('.mdone').click(function(){
		$(this).hide();
		$(this).siblings('.mdcolor').show();
	});

    //楼盘纠错弹框 区属、板块二级联动
    $('select[id^="district"]').change(function(){
        var districtID = $(this).val();
        $.ajax({
            type: 'get',
            url : '/community/find_street_bydis/'+districtID,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg.result=='no result'){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
                    }
                }
                $('select[id^="street"]').empty();
                $('select[id^="street"]').append(str);
            }
        });
    });

    //添加楼盘弹框 区属、板块二级联动
    $('#add_cmt_district').change(function(){
        var districtID = $(this).val();
        $.ajax({
            type: 'get',
            url : '/community/find_street_bydis/'+districtID,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg.result=='no result'){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
                    }
                }
                $('#add_cmt_street').empty();
                $('#add_cmt_street').append(str);
            }
        });
    });

    //添加楼盘提交动作
    $('#add_cmt_button').click(function(){
        var cmt_name = $('#add_cmt_name').val();//楼盘名称
        var dist_id = $('#add_cmt_district').val();//区属
        var streetid = $('#add_cmt_street').val();//板块
        var address = $('#add_address').val();//地址
        var build_date = $('#add_build_date').val();//建筑年代
        var property_year = $('#add_property_year').val();//产权年限
        var coverarea = $('#add_coverarea').val();//占地面积
        var property_company = $('#add_property_company').val();//物业公司
        var developers = $('#add_developers').val();//开发商
        var parking = $('#add_parking').val();//车位
        var green_rate = $('#add_green_rate').val();//绿化率
        var plot_ratio = $('#add_plot_ratio').val();//容积率
        var property_fee = $('#add_property_fee').val();//物业费
        var build_num = $('#add_build_num').val();//总栋数
        var total_room = $('#add_total_room').val();//总户数
        var floor_instruction = $('#add_floor_instruction').val();//楼层情况
        var introduction = $('#add_introduction').val();//楼盘介绍
        var facilities = $('#add_facilities').val();//配套
        //物业类型
        var build_type = [];
        $('input[id^="add_build_type"]:checked').each(function(){
            build_type.push($(this).val());
        });
        var addData = {
            'cmt_name':cmt_name,
            'dist_id':dist_id,
            'streetid':streetid,
            'address':address,
            'build_date':build_date,
            'property_year':property_year,
            'coverarea':coverarea,
            'property_company':property_company,
            'developers':developers,
            'parking':parking,
            'green_rate':green_rate,
            'plot_ratio':plot_ratio,
            'property_fee':property_fee,
            'build_num':build_num,
            'total_room':total_room,
            'floor_instruction':floor_instruction,
            'introduction':introduction,
            'facilities':facilities,
            'build_type':build_type
        };
        $.ajax({
            type: 'get',
            url : '/community/add_community',
            data: addData,
            success: function(msg){
                if('true'==msg){
                    $('#dialog_do_itp').html('新建成功');
					openWin('js_pop_do_success');
                    $("#js_newinfo").css('display','none');
                }else{
                    if('100'==msg){
                        $('#dialog_do_itp').html('楼盘名称为必填字段');
                        openWin('js_pop_do_success');
                        $("#xqerror").html('楼盘名称在2-8个汉字之间');
                    }else if('200'==msg){
                        $('#dialog_do_itp').html('请选择区属');
                        openWin('js_pop_do_success');
                        $("#xqerror").html('请选择区属!');
                    }else if('300'==msg){
                        $('#dialog_do_itp').html('请选择板块');
                        openWin('js_pop_do_success');
                        $("#xqerror").html('请选择板块!');
                    }else if('400'==msg){
                        $('#dialog_do_itp').html('请填写地址');
                        openWin('js_pop_do_success');
                        $("#xqerror").html('请填写地址!');
                    }else if('500'==msg){
                        $('#dialog_do_itp').html('已存在同名小区');
                        openWin('js_pop_do_success');
                        $("#xqerror").html('已存在同名小区!');
                    }
                }

            }
        });
    });

    //纠错信息提交动作
    $('button[id^="modify_submit"]').click(function(){
        var cmt_id = $(this).val();//楼盘id
        var field = $(this).prev().attr('name');//纠错字段
        var field_name = $(this).next().val();//纠错字段名称
        var information = $(this).prev().val();//纠错内容

        var correct_data = {
            'cmt_id':cmt_id,
            'field':field,
            'field_name':field_name,
            'information':information
        };
        $('#js_information'+cmt_id).hide();
        $('.js_GTipsCoverWxr').attr('style','display:none;');
        $.ajax({
            type: 'get',
            url : '/community/cmt_correction',
            data: correct_data,
            success: function(msg){
                if('add success'==msg){
                    $('#dialog_do_itp').html('纠错信息已提交，请等待审核...');
                    openWin('js_pop_do_success');
                }else if('same info'==msg){
                    $('#dialog_do_itp').html('纠错信息不能与原来相同');
                    openWin('js_pop_do_success');
                }else if('cmt_name is null'==msg){
                    $('#dialog_do_itp').html('楼盘名称为必填项，不能为空');
                    openWin('js_pop_do_success');
                }else if('dist_id is null'==msg){
                    $('#dialog_do_itp').html('区属为必填项，不能为空');
                    openWin('js_pop_do_success');
                }else if('street_id is null'==msg){
                    $('#dialog_do_itp').html('板块为必填项，不能为空');
                    openWin('js_pop_do_success');
                }else if('address is null'==msg){
                    $('#dialog_do_itp').html('楼盘地址为必填项，不能为空');
                    openWin('js_pop_do_success');
                }else if('build_date is null'==msg){
                    $('#dialog_do_itp').html('建筑年代必填项，不能为空');
                    openWin('js_pop_do_success');
                }else{
                    $('#dialog_do_itp').html('纠错信息提交失败');
                    openWin('js_pop_do_success');
                }
            }
        });

    });

    $('#close_refresh').click(function(){
        location.reload();
    });

    $("#block_name").autocomplete({
        source: function( request, response ) {
            var term = request.term;
            $.ajax({
                url: "/community/get_cmtinfo_by_kw/",
                type: "GET",
                dataType: "json",
                data: {
                    keyword: term
                },
                success: function(data) {
                    //判断返回数据是否为空，不为空返回数据。
                    if( data[0]['id'] != '0'){
                        response(data);
                    }else{
                        response(data);
                    }
                }
            });
        },
        minLength: 1,
        removeinput: 0,
        select: function(event,ui) {
            if(ui.item.id > 0){
                removeinput = 2;
            }else{
                removeinput = 1;
            }
        },
        close: function(event) {
            if(typeof(removeinput)=='undefined' || removeinput == 1){
                $("#block_name").val("");
            }
        }
    });
});

</script>
</body>
</html>
