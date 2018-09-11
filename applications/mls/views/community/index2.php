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
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="addBlock()"><span class="btn_inner">新增</span></a> </div>
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
	<script type="text/javascript">
		function addBlock(){
			//新增楼盘弹框中内容设置为空
			$('#js_newinfo input').val('');
			$('#js_newinfo select').each(function(){
				$(this).children('option').first().attr('selected','selected');
			});
			$('#js_newinfo textarea').val('');
			$('#js_newinfo p').html('');
			openWin('js_newinfo');
		}
	</script>
</div>
<!--主要内容-->

<div class="table_all tableallborder">
    <div id="js_inner" class="loupan_imlist new_reimg" >

		<ul style="margin-bottom:-4px;">
			<?php
			if(isset($community2) && !empty($community2)){
				foreach($community2 as $k=>$v){
			?>
			<li class="clearfix zd_info" onclick="openWin('js_information<?php echo $v['id'];?>');">
                <div class="new_reimg js_img fl">
                  <img width="150" height="112" class="js_img fl" src="<?php echo $v['surface_img'];?>" id1="<?php echo $v['id'];?>">
                  <?php if($this_user['city_id']!='37' && '1'==$v['is_lock']){ ?>
                    <span class="icon_suo"></span>
                  <?php } ?>
                </div>

				<div class="left">
					<div class="f_d"><span><?php echo $v['cmt_name'];?></span></div>
					<p><em>区属：</em><?php echo $v['dist_name'];?>　<em>板块：</em><?php echo $v['street_name'];?>　<em>类型：</em><?php echo $v['build_type'];?></p>
					<p><em>建筑年代：</em><?php echo $v['build_date'] > 0 ? $v['build_date'] .'年' : '暂无资料';?>　<em>套数：</em><?php echo $v['total_room'];?></p>
					<p><em>开发商：</em><?php echo $v['developers'];?></p>
				</div>

				<div class="right" style="text-align:right; ">
                    <?php if($this_user['city_id']!='37' && '1'==$is_lock_cmt && isset($this_user['role_level']) && intval($this_user['role_level']) < 6 && intval($this_user['role_level']) > 0){ ?>
                    <div class="bt_vvm"><span class="bt_dymp"><a href="#" style="color:white;" class="dong_unit_door" value="<?php echo $v['id']; ?>">楼栋单元门牌</a></span></div>
                    <?php } ?>
					<p><em>容积率：</em><?php echo $v['plot_ratio'];?>　<em>车位：</em><?php echo $v['parking']>0?$v['parking'].'个':'暂无资料';?></p>
					<p><em>绿化：</em><?php echo ($v['green_rate']*100).'%';?>　<em>占地：</em><?php echo $v['coverarea'];?>㎡<em></p>
					<p>物业公司：</em><?php echo $v['property_company']==""?'暂无资料':$v['property_company'];?></p>
				</div>
				<!--div class="f_d">配套：<?php echo $v['facilities_path'];?></div>
				<div class="left" style="width:200px;">交通：<?php echo $v['traffic_path'];?></div-->
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
<div id="js_tupian" class="iframePopBox">
	<a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1"></a>
	<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/loading.gif" id="mainloading" style="position: absolute; z-index: 299706; left: 50%; margin-left: -48px; margin-top: -48px; top: 50%; display: none;">
    <iframe frameborder="0" scrolling="no" width="697" height="420" class='iframePop' src=""></iframe>
</div>
<?php
            if(isset($community2) && !empty($community2)){
                foreach($community2 as $k=>$v){
//                    echo '<pre>';print_r($v);die;
?>
<form method="POST" action="<?php echo MLS_URL;?>/community/correct_community">
<!--详细资料-->
<div class="pop_box_g information_popup" id="js_information<?php echo $v['id'];?>" style="width:800px; heihgt:500px;">
	<div class="hd">
        <div class="title">详细资料</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="information_content">
        <input type="hidden" name="cmt_id" value="<?php echo $v['id'];?>"/>
        <input type="hidden" value="0" id="js_information_input">
    	<table class="table if_table2" id="modifynew">
            <tr>
				<td width="50%"><em class="c666">楼盘名称：</em><?php echo $v['cmt_name'];?><a href="javascript:void(0);" class="js_img2" id1="<?php echo $v['id'];?>">查看图片</a></td><td width="50%"><em class="c666">区属：</em><?php echo $v['dist_name']; ?>
<!--    				<select id="district<?php echo $k;?>" name="dist_id" class="amd mdh">
                        <option value="">请选择</option>
                        <?php foreach ($district as $key => $val) { ?>
                            <option value="<?php echo $val['id'] ?>"<?php if($val['id']==$v['dist_id']){echo 'selected="selected"';}?>><?php echo $val['district'] ?></option>
                        <?php } ?>
    				</select></td>-->
			</tr>
            <tr>
				<td><em class="c666">板块：</em><?php echo $v['street_name'];?>
<!--    				<select id="street<?php echo $k;?>" name="streetid" class="select amd mdh">
                        <option value="">请选择</option>
                        <?php foreach ($v['street_arr'] as $key => $val) { ?>
                            <option value="<?php echo $val['id'] ?>"<?php if($val['id']==$v['streetid']){echo 'selected="selected"';}?>><?php echo $val['streetname'] ?></option>
                        <?php } ?>
   				</select></td>-->
				<td><em class="c666">楼盘地址：</em><?php echo $v['address'];?></td>
			</tr>
            <tr>
				<td><em class="c666">物业类型：</em><?php if(strchr($v['build_type'], '住宅')){echo '住宅';} ?> <?php if(strchr($v['build_type'], '别墅')){echo '别墅';} ?> <?php if(strchr($v['build_type'], '写字楼')){echo '写字楼';} ?> <?php if(strchr($v['build_type'], '商铺')){echo '商铺';} ?> <?php if(strchr($v['build_type'], '厂房仓库')){echo '厂房仓库';} ?></span></td>
				<td><em class="c666">建筑年代：</em><?php echo $v['build_date'] > 0 ? $v['build_date'].'年' : '暂无资料';?></td>
			</tr>

            <tr>
				<td><em class="c666">占地：</em><span class="md"><?php echo $v['coverarea']>0?$v['coverarea']:'暂无资料';?></span><input type="text" value="<?php echo $v['buildarea'];?>" name="buildarea" class="w100 amd mdh">㎡</td>
				<td><em class="c666">产权年限：</em><span class="md"><span class="md"><?php echo $v['property_year']>0?$v['property_year'].'年':'暂无资料';?></span></span><input type="text" value="<?php echo $v['property_year'].'年';?>" name="property_year" class="w100 amd mdh"></td>
			</tr>
            <tr>
				<td><em class="c666">物业公司：</em><span class="md"><?php echo $v['property_company']==""?'暂无资料':$v['property_company'];?></span><input type="text" value="<?php echo $v['property_company'];?>" name="property_company" class="w100 amd mdh"></td>
				<td><em class="c666">开发商：</em><?php echo $v['developers']==""?'暂无资料':$v['developers'];?></td>
			</tr>
			<tr>
				<td><em class="c666">停车位：</em><span class="md"><?php echo empty($v['parking'])?'暂无资料':$v['parking'];?></span><input type="text" value="<?php echo $v['parking'];?>" name="parking" class="w60 amd mdh"></td>
				<td><em class="c666">绿化率：</em><span class="md"><?php echo $v['green_rate']>0?$v['green_rate']*100:'暂无资料';?></span><input type="text" value="<?php echo $v['green_rate']*100;?>" name="green_rate" class="w60 amd mdh">%</td>
			</tr>
			<tr>
				<td><em class="c666">容积率：</em><span class="md"><?php echo $v['plot_ratio']>0?$v['plot_ratio']:'暂无资料';?></span><input type="text" value="<?php echo $v['plot_ratio'];?>" name="plot_ratio" class="w60 amd mdh"></td><td><em class="c666">物业费：</em><span class="md"><?php echo $v['property_fee']>0?$v['property_fee']:'暂无资料';?></span><input type="text" value="<?php echo $v['property_fee'];?>" name="property_fee" class="w60 amd mdh">元/㎡</td>
			</tr>
			<tr>
				<td><em class="c666">总栋数：</em><?php echo $v['build_num']>0?$v['build_num'].'栋':'暂无资料';?></td><td><em class="c666">总户数：</em><?php echo $v['total_room'];?>户</td>
			</tr>
            <tr>
				<td><em class="c666">地铁：</em><span class="md"><?php echo $v['subway']==""?'暂无资料':$v['subway'];?></span><input type="text" value="<?php echo $v['subway'];?>" name="subway" id="subway" class="w180 amd mdh"></td>
				<td><em class="c666">公交：</em><span class="md"><?php echo $v['bus_line']==""?'暂无资料':$v['bus_line'];?></span><input type="text" value="<?php echo $v['bus_line'];?>" name="bus_line" id="bus_line" class="w180 amd mdh"></td>
			</tr>
			<tr>
				<td colspan="2"><em class="c666">楼层状况：</em><span class="md"><?php echo $v['floor_instruction']==""?'暂无资料':$v['floor_instruction'];?></span><input style="width:489px;" type="text" value="<?php echo $v['floor_instruction'];?>" name="floor_instruction" class="w60 amd mdh"></td>
			</tr>
			<tr>
				<td colspan="2"><em class="c666">配套：</em><span class="md"><?php echo $v['facilities']==""?'暂无资料':$v['facilities'];?></span><input style="width:489px;" type="text" value="<?php echo $v['facilities'];?>" name="facilities" class="w370 amd mdh"></td>
			</tr>
			<tr>
				<td colspan="2"><em class="c666">楼盘介绍：</em><span class="md"><?php echo $v['introduction'];?></span><textarea class="iftextarea amd mdh" name="introduction"><?php echo $v['introduction'];?></textarea></td>
			</tr>
    		<!--tr>
    			<td width="72" class="ifw1"><font color="red">*</font>楼盘名称：</td>
    			<td width="260" class="ifw2" id="td_editor<?php echo $k.'1';?>">
    				<span class="md"><?php echo $v['cmt_name'];?></span><input class="amd mdh" name="cmt_name" id="modify_cmt_name<?php echo $k;?>" type="text" value="<?php echo $v['cmt_name'];?>">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="楼盘名称"/>
                    <span class="mdone iconfont" title="编辑">&#xe62b;</span>
                </td>
    			<td width="72" class="ifw3"><font color="red">*</font>区属：</td>
    			<td width="200" class="ifw4" id="td_editor<?php echo $k.'2';?>">
    				<span class="md"><?php echo $v['dist_name']; ?></span>
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
                    <span class="md"><?php echo $v['street_name'];?></span>
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
    				<span class="md"><?php echo $v['address'];?></span>
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
    				<span class="md"><?php echo date('Y',$v['build_date']);?></span>
                    <input type="text" class="w80 amd mdh" name="build_date" value="<?php echo date('Y',$v['build_date']);?>"/>年
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="建筑年代"/>
                    <span class="mdone iconfont" style="display:none; cursor: pointer;">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">占地：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'7';?>">
    				<span class="md"><?php echo $v['coverarea'];?></span>
    				<input type="text" value="<?php echo $v['coverarea'];?>" name="coverarea" class="w60 amd mdh">&nbsp M²
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="占地"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3">产权年限：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'8';?>">

    				<input type="text" value="<?php echo $v['property_year'];?>" name="property_year" class="w60 amd mdh">年
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="产权年限"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">物业公司：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'9';?>">
    				<span class="md"><?php echo $v['property_company'];?></span>
    				<input type="text" value="<?php echo $v['property_company'];?>" name="property_company" class="w100 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="物业公司"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3">开发商：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'10';?>">
    				<span class="md"><?php echo $v['developers'];?></span>
    				<input type="text" value="<?php echo $v['developers'];?>" name="developers" class="w120 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="开发商"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">停车位：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'11';?>">
    				<span class="md"><?php echo $v['parking'];?></span>
    				<input type="text" value="<?php echo $v['parking'];?>" name="parking" class="w60 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="停车位"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3">绿化率：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'12';?>">
    				<span class="md"><?php echo $v['green_rate']*100;?></span>
    				<input type="text" value="<?php echo $v['green_rate']*100;?>" name="green_rate" class="w60 amd mdh">%
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="绿化率"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">容积率：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'13';?>">
    				<span class="md"><?php echo $v['plot_ratio'];?></span>
    				<input type="text" value="<?php echo $v['plot_ratio'];?>" name="plot_ratio" class="w60 amd mdh">
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="容积率"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3">物业费：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'14';?>">
    				<span class="md"><?php echo $v['property_fee'];?></span>
    				<input type="text" value="<?php echo $v['property_fee'];?>" name="property_fee" class="w60 amd mdh">元/m²
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="物业费"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">总栋数：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'15';?>">
    				<span class="md"><?php echo $v['build_num'];?></span>
    				<input type="text" value="<?php echo $v['build_num'];?>" name="build_num" class="w60 amd mdh">栋
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="总栋数"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    			<td class="ifw3">总户数：</td>
    			<td class="ifw4" id="td_editor<?php echo $k.'16';?>">
    				<span class="md"><?php echo $v['total_room'];?></span>
    				<input type="text" value="<?php echo $v['total_room'];?>" name="total_room" class="w60 amd mdh">户
                    <button type="button" class="mdcolor js_button iconfont" title="确定" style="display:none" id="modify_submit<?php echo $v['id']?>" value="<?php echo $v['id']?>">&#xe62b;</button>
                    <input type="hidden" value="总户数"/>
                    <span class="mdone iconfont">&#xe62b;</span>
    			</td>
    		</tr>
    		<tr>
    			<td class="ifw1">楼层状况：</td>
    			<td class="ifw2" id="td_editor<?php echo $k.'17';?>">
    				<span class="md"><?php echo $v['floor_instruction'];?></span>
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
    		</tr-->
    	</table>
    </div>
    <?php if($this_user['city_id']=='37'){ ?>
        <div class="information_btn information_btn1">
            <a href="javascript:void(0);" id="put_right" class="btn-lv"><span>我要纠错</span></a>
        </div>
    <?php }else{ ?>
        <?php if(!('1'==$v['is_lock'])){ ?>
        <div class="information_btn information_btn1">
            <a href="javascript:void(0);" id="put_right" class="btn-lv"><span>我要纠错</span></a>
        </div>
        <?php } ?>
    <?php } ?>

	<div class="information_btn information_btn2">
        <button type="button" id="dialog_share" class="btn-lv1 btn-left JS_Close" onclick="form.submit();">确定</button><button type="button" class="btn-hui1 JS_Close">取消</button>
	</div>
</div>
</form>
            <?php }} ?>
<!--新增楼盘-弹层-->
<div  id="js_newinfo" class="pop_box_g information_popup"  style="width:345px; height:380px; display:hidden;">
    <div class="hd">
        <div class="title">新建楼盘</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="create_newb_wrapall">

            <div class="create_newb_wrap clearfix">
                <span class="name fl"><font color="red">*</font>楼盘名称：</span>
                <input type="text" class="loupan fl" name="cmt_name" id="add_cmt_name" value="" style="width:180px">
            </div>
			<p id="warn_cmt_name" style="color:red;margin-left:70px"></p>

            <div class="create_newb_wrap clearfix">
                <span class="name fl"><font color="red">*</font>区属：</span>
                    <select id="add_cmt_district" name="dist_id" class="qushu fl">
                        <option value="">请选择</option>
                        <?php foreach ($district as $key => $val) { ?>
                            <option value="<?php echo $val['id'] ?>" ><?php echo $val['district'] ?></option>
                        <?php } ?>
    		    </select>
            </div>
			<p id="warn_cmt_district" style="color:red;margin-left:70px"></p>

            <div class="create_newb_wrap clearfix">
                <span class="name fl"><font color="red">*</font>模块：</span>
				<select class="qushu fl" id="add_cmt_street" name="streetid">
						<option value="">请选择</option>
				</select>
            </div>
			<p id="warn_cmt_street" style="color:red;margin-left:70px"></p>

            <div class="create_newb_wrap clearfix">
                <span class="name fl"><font color="red">*</font>地址：</span>
                <textarea class="address fl" id="add_address" name="address"></textarea>
            </div>
			<p id="warn_cmt_address" style="color:red;margin-left:70px"></p>


        </div>

        <div style="margin-left:95px">
            <button type="button" style="float:left;"  id="add_cmt_button" class="btn-lv1 btn-left JS_Close">确定</button>
            <button type="button" class="btn-hui1 JS_Close" id="move_cmt_button">取消</button>
        </div>
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

<!--修改楼栋弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:950px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" id="window_min_close">&#xe60c;</a>
    <iframe frameborder="0" name="detialIframe" scrolling="no" width="950" height="540" class='iframePop detialIframe' id="detialIframe" src=""></iframe>
</div>

<script>
$(function(){
	$(".js_img, .js_img2").bind("click",function(event){
		id1 = $(this).attr('id1');
		$('#js_tupian .iframePop').attr('src','<?php echo MLS_URL;?>/community/img_detail/'+ id1);
    openWin('js_tupian');
    event.stopPropagation();
	});
	//移上去加蓝色背景
	$(".zd_info").hover(function(){
		$(this).toggleClass("zd_info_hover");
	});
	//点击我要纠错
	$("#put_right").live("click",function(){
		$(".information_btn1").hide();
		$(".information_btn2").show();
		$(".md").hide();
		$(".mdh").css("display","inline-block");
	});
	$(".JS_Close").click(function(){
		$(".information_btn1").show();
		$(".information_btn2").hide();
		$(".md").show();
		$(".mdh").hide();
	});
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
        var addData = {
            'cmt_name':cmt_name,
            'dist_id':dist_id,
            'streetid':streetid,
            'address':address
        };
		if(cmt_name == ""){
      	    $("#warn_cmt_name").html("楼盘名称为必填字段");
      	    return false;
        }else{
			$("#warn_cmt_name").html("");
		}

		if(dist_id == ""){
      	    $("#warn_cmt_district").html("请选择区属");
      	    return false;
        }else{
			$("#warn_cmt_district").html("");
		}

		if(streetid == ""){
      	    $("#warn_cmt_street").html("请选择板块");
      	    return false;
        }else{
			$("#warn_cmt_street").html("");
		}

		if(address == ""){
      	    $("#warn_cmt_address").html("请填写地址");
      	    return false;
        }else{
			$("#warn_cmt_address").html("");
		}

        $.ajax({
            type: 'get',
            url : '/community/add_community',
            data: addData,
            success: function(msg){
                if('true'==msg){
                    $('#dialog_do_itp').html('新建成功');
					openWin('js_pop_do_success');
                    $("#js_newinfo").css('display','none');
                }else if('500'==msg){
					$('#dialog_do_itp').html('已存在同名小区');
					openWin('js_pop_do_success');
					$("#xqerror").html('已存在同名小区!');
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

    $('.dong_unit_door').click(function(event){
          event.stopPropagation();
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

    $('.dong_unit_door').click(function(){
        var cmt_id = $(this).attr('value');
        var _url = '/community/cmt_dong/'+cmt_id;
        if(_url)
        {
            $("#js_pop_box_g .iframePop").attr("src",_url);
        }
        openWin('js_pop_box_g');
    });
});

</script>
</body>
</html>
