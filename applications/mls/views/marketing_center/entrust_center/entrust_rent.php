<script>
    window.parent.addNavClass(20);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div id="js_search_box">
    <div  class="shop_tab_title clearfix">
        <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
    </div>
</div>

<div class="search_box clearfix"  id="js_search_box_02">
	<form name="search_form" id="search_form" method="post" action="<?php echo MLS_URL;?>/entrust_center/ent_rent">
	<a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this)" data-h="0">展开<span class="iconfont">&#xe609;</span></a>
    <div class="fg_box">
        <p class="fg fg_tex">姓名：</p>
        <div class="fg">
            <input type="text" name="realname" id="realname" value="<?=isset($post_param['realname'])?$post_param['realname']:""?>" class="input w110">
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">电话：</p>
        <div class="fg">
            <input type="text" name="phone" id="phone" value="<?=isset($post_param['phone'])?$post_param['phone']:""?>" class="input w110">
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 楼盘：</p>
        <div class="fg">
            <input type="text" name="comt_name" id="comt_name" value="<?=isset($post_param['comt_name'])?$post_param['comt_name']:""?>" class="input w110">
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 户型：</p>
        <div class="fg">
            <select class="select" name="room">
                <option value="">不限</option>
                <option value="1" <?=($post_param['room']==1)?"selected":""?>>一室</option>
                <option value="2" <?=($post_param['room']==2)?"selected":""?>>二室</option>
                <option value="3" <?=($post_param['room']==3)?"selected":""?>>三室</option>
                <option value="4" <?=($post_param['room']==4)?"selected":""?>>四室</option>
                <option value="5" <?=($post_param['room']==5)?"selected":""?>>五室</option>
                <option value="6" <?=($post_param['room']==6)?"selected":""?>>六室</option>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 面积：</p>
        <div class="fg">
            <input type="text" name="buildarea1" id="buildarea1" onblur="check_num()" value="<?=isset($post_param['buildarea1'])?$post_param['buildarea1']:""?>" class="input w30">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" name="buildarea2" id="buildarea2" onblur="check_num()" value="<?=isset($post_param['buildarea2'])?$post_param['buildarea2']:""?>" class="input w30">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="buildarea1_reminder"></span>
        </div>
        <p class="fg fg_tex fg_tex03">平米</p>
    </div>
	<div class="fg_box">
        <p class="fg fg_tex">租金：</p>
        <div class="fg">
            <input type="text" name="price1" id="price1" onblur="check_num()" class="input w30" value="<?=isset($post_param['price1'])?$post_param['price1']:""?>">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" name="price2" id="price2" onblur="check_num()" class="input w30" value="<?=isset($post_param['price2'])?$post_param['price2']:""?>">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="price1_reminder"></span>
        </div>
        <p class="fg fg_tex fg_tex03">万元</p>
    </div>
    <div class="fg_box hide">
        <p class="fg fg_tex"> 状态：</p>
        <div class="fg">
            <select class="select" name="is_look">
                <option value="0">不限</option>
                <option value="1" <?=($post_param['is_look']==1)?"selected":""?>>未查看</option>
                <option value="2" <?=($post_param['is_look']==2)?"selected":""?>>已查看</option>
            </select>
        </div>
    </div>
	<div class="fg_box">
        <p class="fg fg_tex"> 出租方式：</p>
        <div class="fg">
            <select class="select" name="type">
                <option value="0">不限</option>
                <option value="1" <?=($post_param['type']==1)?"selected":""?>>整租</option>
                <option value="2" <?=($post_param['type']==2)?"selected":""?>>单间</option>
                <option value="3" <?=($post_param['type']==3)?"selected":""?>>床位</option>
            </select>
        </div>
    </div>
	<input type="hidden" name="pg" value="1">
	<input type="hidden" name="spec" value="rent">
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="sub_form('search_form');return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="<?php echo MLS_URL;?>/entrust_center/ent_rent" class="reset">重置</a> </div>
    </div>
	<div class="fun_btn clearfix" id="js_fun_btn" style="display:none">
		<div class="get_page">
			<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
		</div>
	</div>
	</form>
</div>


<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
              	<td class="c9"><div class="info">序号</div></td>
                <td class="c9"><div class="info">业主姓名</div></td>
                <td class="c12"><div class="info">电话</div></td>
                <td class="c15"><div class="info">楼盘</div></td>
                <td class="c11"><div class="info">户型</div></td>
                <td class="c11"><div class="info">面积(㎡)</div></td>
  				<td class="c11"><div class="info">租金（元/月）</div></td>
                <td class="c12"><div class="info">出租方式</div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
			<?php foreach($list as $vo){?>
			<tr onClick="detail_pop(<?php echo $vo['id'];?>)">
				<td class="c9"><div class="info"><?=$vo['id'];?></div></td>
				<td class="c9"><div class="info"><?=$vo['realname'];?></div></td>
				<td class="c12"><div class="info"><?=$vo['phone'];?></div></td>
				<td class="c15"><div class="info"><?=$vo['comt_name'];?></div></td>
				<td class="c11"><div class="info"><?=$vo['room'];?>-<?=$vo['hall'];?>-<?=$vo['toilet'];?></div></td>
				<td class="c11"><div class="info"><?=$vo['area'];?></div></td>
				<td class="c11"><div class="info"><strong class="f60"><?=$vo['hprice'];?></strong></div></td>
				<td class="c12"><div class="info">
					<?php switch($vo['type']){
						case "1" : echo "整租";
						break;
						case "2" : echo "单间";
						break;
						case "3" : echo "床位";
						break;

					}?>
				</div></td>
				<td ><div class="info"><a href="javascript:void(0)" id="is_look<?=$vo['id']?>"><?=($vo['is_look']==2)?"<font color='#1dc680'>已查看</font>":"查看"?></a></div></td>
			</tr>
			<?php } ?>
        </table>
    </div>
</div>

<div class="fun_btn clearfix" id="js_fun_btn">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>

<!--房源详情弹框-->
<div class="pop_box_g pop_see_info_deal" id="js_pop_see_info_house" style="width:430px; height:340px; display:none;">
    <div class="hd">
        <div class="title">客户出售备注</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="mod-inner2">
			<table class="table-col2">
				<tr>
					<td style="width:50px; color:#666; text-align:right;">姓名：</td>
					<td width="170" id="d_realname"></td>
					<td style="color:#666; text-align:right;">电话：</td>
					<td id="d_phone"></td>
				</tr>
				<tr>
					<td style="width:50px; color:#666; text-align:right;">楼盘：</td>
					<td width="170" id="d_comt_name"></td>
					<td style="color:#666; text-align:right;">户型：</td>
					<td><span id="d_room"></span>室<span id="d_hall"></span>厅<span id="d_toilet"></span>卫</td>
				</tr>
				<tr>
					<td style="width:50px; color:#666; text-align:right;">面积：</td>
					<td width="170"><span id="d_area"></span>㎡</td>
					<td style="color:#666; text-align:right;">租金：</td>
					<td><strong class="f60" id="d_hprice"></strong>元/月</td>
				</tr>
				<tr>
					<td style="width:50px; color:#666; text-align:right;">方式：</td>
					<td width="170"><span id="d_type"></span></td>
					<td style="color:#666; text-align:right;"></td>
					<td></td>
				</tr>
			</table>
			<table class="table-col2 table-col3">
				<tr>
					<td style="color:#666; padding-left:14px;">备注：</td>
				</tr>
				<tr>
					<td style="padding-left:14px;" id="d_remark"></td>
				</tr>
			</table>
		</div>
		<a href="javascript:void(0)" class="btn-lv1 btn-mid JS_Close" style="margin-top:10px;" >确定</a>
    </div>
</div>

<script>
//详情操作弹出框
function detail_pop(id){
	$.ajax({
		type: "POST",
		url: "/entrust_center/detail_rent/",
		data: "id="+id,
		dataType:"json",
		cache:false,
		error:function(){
			alert("系统错误");
			return false;
		},
		success: function(data){
			$("#d_realname").html(data['realname']);
			$("#d_phone").html(data['phone']);
			$("#d_comt_name").html(data['comt_name']);
			$("#d_room").html(data['room']);
			$("#d_hall").html(data['hall']);
			$("#d_toilet").html(data['toilet']);
			$("#d_area").html(data['area']);
			$("#d_hprice").html(data['hprice']);
			$("#d_remark").html(data['remark']);
			$("#d_type").html(data['type']);
			switch(data['type']){
				case "1" : $("#d_type").html("整租");
				break;
				case "2" : $("#d_type").html("单间");
				break;
				case "3" : $("#d_type").html("床位");
				break;
			}
			if(data['is_look']==2){
				$("#is_look"+data['id']).html("<font color='#1dc680'>已查看</font>");
			}
			openWin('js_pop_see_info_house');
		}
	});

}

/*
*	aim:	面积、总价等 onblur 事件的校验
*	author: angel_in_us
*	date:	2015.03.04
*/
function check_num(){
	var buildarea1    =    $("#buildarea1").val();	//最小面积
	var buildarea2    =    $("#buildarea2").val();	//最大面积
	var price1		  =    $("#price1").val();		//最小总价
	var price2		  =    $("#price2").val();		//最大总价

	//最小面积
	if(buildarea1){
		var   type="^\\d{1,9}$|^\\d{1,9}[.]\\d{1,3}$";
		var   re   =   new   RegExp(type);

		if(buildarea1.match(re)==null)
		{
			$("#buildarea1_reminder").html("面积必须为正数！");
			return;
		}else{
			$("#buildarea1_reminder").html("");
		}
	}

	//最大面积
	if(buildarea2){
		var   type="^\\d{1,9}$|^\\d{1,9}[.]\\d{1,3}$";
		var   re   =   new   RegExp(type);

		if(buildarea2.match(re)==null)
		{
			$("#buildarea1_reminder").html("面积必须为正数！");
			return;
		}else{
			$("#buildarea1_reminder").html("");
		}
	}

	//最小面积 buildarea1 必须小于 最大面积 buildarea2
	if(buildarea1 && buildarea2){
		buildarea1 = parseInt(buildarea1);
		buildarea2 = parseInt(buildarea2);
		if(buildarea1>buildarea2){
			$("#buildarea1_reminder").html("面积筛选区间输入有误！");
		}else{
			$("#buildarea1_reminder").html("");
		}
	}

	//最小总价
	if(price1){
		var   type="^\\d{1,9}$|^\\d{1,9}[.]\\d{1,3}$";
		var   re   =   new   RegExp(type);

		if(price1.match(re)==null)
		{
			$("#price1_reminder").html("总价必须为正数！");
			return;
		}else{
			$("#price1_reminder").html("");
		}
	}

	//最大总价
	if(price2){
		var   type="^\\d{1,9}$|^\\d{1,9}[.]\\d{1,3}$";
		var   re   =   new   RegExp(type);

		if(price2.match(re)==null)
		{
			$("#price1_reminder").html("总价必须为正数！");
			return;
		}else{
			$("#price1_reminder").html("");
		}
	}

	//最小租金 price1 必须小于 最大总价 price2
	if(price1 && price2){
		price1 = parseInt(price1);
		price2 = parseInt(price2);
		if(price1>price2){
			$("#price1_reminder").html("价格筛选区间输入有误！");
		}else{
			$("#price1_reminder").html("");
		}
	}
}

</script>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->

