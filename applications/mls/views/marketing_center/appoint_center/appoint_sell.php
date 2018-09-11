<script>
    window.parent.addNavClass(20);
</script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div id="js_search_box">
    <div  class="shop_tab_title clearfix">
        <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
    </div>
</div>


<form name="search_form" id="search_form" method="post" action="<?php echo MLS_URL;?>/appoint_center/app_sell">
<input type="hidden" name="is_submit" value="1">
<div class="search_box clearfix"  id="js_search_box_02">
	<a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this)" data-h="0">展开<span class="iconfont">&#xe609;</span></a>
    <div class="fg_box">
        <p class="fg fg_tex">电话：</p>
        <div class="fg">
            <input type="text" name="phone" id="phone" value="<?=$post_param['phone']?>" class="input w110" onkeyup="this.value=this.value.replace(/[^\d]/ig,'')" maxlength='11'>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 房源编号：</p>
        <div class="fg">
            <input type="text" name="house_id" id="house_id" value="<?=isset($post_param['house_id'])?$post_param['house_id']:""?>" class="input w60">
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">区属：</p>
        <div class="fg">
			<select id="district" name="dist_id" aria-controls="dataTables-example" class="form-control input-sm" style="line-height:25px;height:25px;font-size:12px;border-radius:3px;">
				<option value="0">请选择</option>
				<?php foreach ($district as $k => $v) { ?>
					<option value="<?php echo $v['id'] ?>" <?php if($v['id']==$post_param['dist_id']){ echo "selected"; }?>><?php echo $v['district'] ?></option>
				<?php } ?>
			</select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 板块：</p>
        <div class="fg">
            <select id="street" name="street_id" aria-controls="dataTables-example" class="form-control input-sm" style="line-height:25px;height:25px;font-size:12px;border-radius:3px;">
				<option value="0">请选择</option>
				<?php
					if($post_param['dist_id']>0)
					{
						foreach($street as $k => $v)
						{
							if($v['dist_id'] == $post_param['dist_id'])
							{
								echo "<option value='".$v['id']."'";
								if($v['id'] == $post_param['street_id'])
									echo " selected ";
								echo ">".$v['streetname']."</option>";
							}
						}
					}
				?>
			</select>
        </div>
    </div>
    <div class="fg_box">
		<p class="fg fg_tex"> 楼盘：</p>
		<div class="fg">
			<input type="text" name="block_name" id="block_name" value="<?php echo $post_param['block_name']; ?>" class="input w90">
			<input name="block_id" id="block_id" value="<?php echo $post_param['block_id']?>" type="hidden">
		</div>
	</div>
	<script type="text/javascript">
		$(function(){
			$.widget( "custom.autocomplete", $.ui.autocomplete, {
				_renderItem: function( ul, item ) {
					if(item.id>0){
						return $( "<li>" )
						.data( "item.autocomplete", item )
						.append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.label+'</span><span class="ui_district">'+item.districtname+'</span><span class="ui_address">'+item.address+'</span></a>')
						.appendTo( ul );
					}else{
						return $( "<li>" )
						.data( "item.autocomplete", item )
						.append('<a class="ui-corner-all" tabindex="-1">'+item.label+'</a>')
						.appendTo( ul );
					}
				}
			});
		$("#block_name").autocomplete({
			source: function( request, response ) {
				var term = request.term;
				$("#block_id").val("");
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
					var blockname = ui.item.label;
					var id = ui.item.id;
					var streetid = ui.item.streetid;
					var streetname = ui.item.streetname;
					var dist_id = ui.item.dist_id;
					var districtname = ui.item.districtname;
					var address = ui.item.address;

					//操作
					$("#block_id").val(id);
					$("#block_name").val(blockname);
					removeinput = 2;
				}else{
					removeinput = 1;
				}
			},
			close: function(event) {
				if(typeof(removeinput)=='undefined' || removeinput == 1){
					$("#block_name").val("");
					$("#block_id").val("");
				}
			}
		});
	});
	</script>
    <div class="fg_box">
        <p class="fg fg_tex"> 户型：</p>
        <div class="fg">
            <select class="select" name="room">
                <option value="0">不限</option>
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
            <input type="text" name="buildarea1" id="buildarea1" onkeyup="check_num()" value="<?=isset($post_param['buildarea1'])?$post_param['buildarea1']:""?>" class="input w30">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" name="buildarea2" id="buildarea2" onkeyup="check_num()" value="<?=isset($post_param['buildarea2'])?$post_param['buildarea2']:""?>" class="input w30">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="buildarea1_reminder"></span>
        </div>
        <p class="fg fg_tex fg_tex03">平米</p>
    </div>
	<div class="fg_box hide">
        <p class="fg fg_tex">总价：</p>
        <div class="fg">
            <input type="text" name="price1" id="price1" onkeyup="check_num()" class="input w30" value="<?=isset($post_param['price1'])?$post_param['price1']:""?>">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" name="price2" id="price2" onkeyup="check_num()" class="input w30" value="<?=isset($post_param['price2'])?$post_param['price2']:""?>">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="price1_reminder"></span>
        </div>
        <p class="fg fg_tex fg_tex03">万元</p>
    </div>
	<div class="fg_box hide">
        <p class="fg fg_tex">看房时间：</p>
        <div class="fg">
			<input type="text" class="hr_framework_text hr_framework_timebg fl" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id="stimemin" name="stimemin" value="<?=isset($post_param['stimemin'])?$post_param['stimemin']:""?>"onblur="check_num()">

        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
			<input type="text" class="hr_framework_text hr_framework_timebg fl" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id="stimemax" name="stimemax" value="<?=isset($post_param['stimemax'])?$post_param['stimemax']:""?>"onblur="check_num()">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="stimemin_reminder"></span>
        </div>
    </div>
	<input type="hidden" name="pg" value="1">
	<input type="hidden" name="black" value="blacklist">
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');form_submit();return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="<?php echo MLS_URL;?>/appoint_center/app_sell" class="reset">重置</a> </div>
    </div>
</div>


<div class="table_all" >
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
              	<td class="c6"><div class="info">房源编号</div></td>
                <td class="c6"><div class="info">区属</div></td>
                <td class="c6"><div class="info">板块</div></td>
                <td class="c12"><div class="info">楼盘</div></td>
                <td class="c6"><div class="info">户型</div></td>
                <td class="c6"><div class="info">面积(㎡)</div></td>
  				<td class="c6"><div class="info">总价(万)</div></td>
                <td class="c6"><div class="info">楼层</div></td>
                <td class="c8"><div class="info">姓名</div></td>
                <td class="c8"><div class="info">联系方式</div></td>
                <td class="c15"><div class="info">看房时间</div></td>
                <td><div class="info">发布时间</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
			<?php
			if($list){
				foreach($list as $vo){?>
					<tr id="tr<?php echo $vo['id'];?>" date-url="/sell/details_house/<?php echo $vo['id'];?>/1/1/0/<?=$vo['app_id']?>" controller="appoint_center" _id="<?php echo $vo['id'];?>">
						<td class="c3" style="display:none">
							<div class="info">
								<input type="checkbox" name="items" value="<?php echo $vo['id'];?>" class="checkbox" style="display:none;">
							</div>
						</td>
						<input type='hidden' id="inp<?=$vo['id']?>" value="<?=$vo['app_id']?>">
						<td class="c6"><div class="info c227ac6" id="" style="cursor:pointer"><?=$vo['house_id']?></div></td>
						<td class="c6"><div class="info"><?=$district[$vo['district_id']]['district']?></div></td>
						<td class="c6"><div class="info"><?=$street[$vo['street_id']]['streetname']; ?></div></td>
						<td class="c12"><div class="info"><?=$vo['block_name']?></div></td>
						<td class="c6"><div class="info"><?=$vo['room'];?>-<?=$vo['hall'];?>-<?=$vo['toilet'];?></div></td>
						<td class="c6"><div class="info"><?=$vo['buildarea'];?></div></td>
						<td class="c6"><div class="info cf60"><strong class="f60"><?=$vo['price'];?></strong></div></td>
						<td class="c6"><div class="info"><?php echo $vo['floor']; ?><?php if($vo['floor_type']==2){ echo "-".$vo['subfloor'];}?>/<?php echo $vo['totalfloor']; ?></div></td>
						<td class="c8"><div class="info"><?=$vo['uname'];?></div></td>
						<td class="c8"><div class="info"><?=$vo['phone'];?></div></td>
						<td class="c15"><div class="info"><?=$vo['sdate']?> <?=$vo['stime']?></div></td>
						<td><div class="info c999"><?=date("Y-m-d H:i:s",$vo['ctime']);?></div></td>
					</tr>
					<?php }}else{?>
						<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
					<?php }?>
        </table>
    </div>
</div>

<ul id="openList" >
    <input type="hidden" id="right_id" class="js_input">
    <!--右键菜单-->
	<li onClick="openHouseDetails_app('sell',1);" class="js_input_1">查看详情</li>

</ul>
<div class="fun_btn clearfix" id="js_fun_btn">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>
</form>
<script>
//区属板块触发
$(function(){
	$('#district').change(function(){
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
				$('#street').empty();
				$('#street').append(str);
			}
		});
	});
});
//右键查看详情
function openHouseDetails_app(type , is_pub)
{
    var house_id = $("#right_id").val();
	var app_id = $("#inp"+house_id).val();
    //判断该房源是否存在
    $.ajax({
        url: "<?php echo MLS_URL;?>/"+type+"/check_is_exist_house",
        type: "GET",
        data: {house_id:house_id},
        success: function(data) {
            if('success'==data){
                var _url = '/'+ type +'/details_house/'+ house_id+'/'+is_pub+'/1/0/'+app_id;

                if(_url)
                {
                    $("#js_pop_box_g .iframePop").attr("src",_url);
                }

                openWin('js_pop_box_g');
            }else{
                $("#dialog_do_warnig_tip").html("该房源不存在");
                openWin('js_pop_do_warning');
            }
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
	var stimemin	  =    $("#stimemin").val();		//最大总价
	var stimemax	  =    $("#stimemax").val();		//最大总价
	//alert(stimemin);

	if(!buildarea1 && !buildarea2){
		$("#buildarea1_reminder").html("");
		$("input[name='is_submit']").val('1');
	}
	//最小面积
	if(buildarea1){
		var   type="^\\d{1,9}$|^\\d{1,9}[.]\\d{1,3}$";
		var   re   =   new   RegExp(type);

		if(buildarea1.match(re)==null)
		{
			$("#buildarea1_reminder").html("面积必须为正数！");
			$("input[name='is_submit']").val('0');
			return;
		}else{
			$("#buildarea1_reminder").html("");
			$("input[name='is_submit']").val('1');
		}
	}

	//最大面积
	if(buildarea2){
		var   type="^\\d{1,9}$|^\\d{1,9}[.]\\d{1,3}$";
		var   re   =   new   RegExp(type);

		if(buildarea2.match(re)==null)
		{
			$("#buildarea1_reminder").html("面积必须为正数！");
			$("input[name='is_submit']").val('0');
			return;
		}else{
			$("#buildarea1_reminder").html("");
			$("input[name='is_submit']").val('1');
		}
	}

	//最小面积 buildarea1 必须小于 最大面积 buildarea2
	if(buildarea1 && buildarea2){
		buildarea1 = parseInt(buildarea1);
		buildarea2 = parseInt(buildarea2);
		if(buildarea1>buildarea2){
			$("#buildarea1_reminder").html("面积筛选区间输入有误！");
			$("input[name='is_submit']").val('0');
			return;
		}else{
			$("#buildarea1_reminder").html("");
			$("input[name='is_submit']").val('1');
		}
	}

	if(!price1 && !price2){
		$("#price1_reminder").html("");
		$("input[name='is_submit']").val('1');
	}
	//最小总价
	if(price1){
		var   type="^\\d{1,9}$|^\\d{1,9}[.]\\d{1,3}$";
		var   re   =   new   RegExp(type);

		if(price1.match(re)==null)
		{
			$("#price1_reminder").html("总价必须为正数！");
			$("input[name='is_submit']").val('0');
			return;
		}else{
			$("#price1_reminder").html("");
			$("input[name='is_submit']").val('1');
		}
	}

	//最大总价
	if(price2){
		var   type="^\\d{1,9}$|^\\d{1,9}[.]\\d{1,3}$";
		var   re   =   new   RegExp(type);

		if(price2.match(re)==null)
		{
			$("#price1_reminder").html("总价必须为正数！");
			$("input[name='is_submit']").val('0');
			return;
		}else{
			$("#price1_reminder").html("");
			$("input[name='is_submit']").val('1');
		}
	}

	//最小租金 price1 必须小于 最大总价 price2
	if(price1 && price2){
		price1 = parseInt(price1);
		price2 = parseInt(price2);
		if(price1>price2){
			$("#price1_reminder").html("价格筛选区间输入有误！");
			$("input[name='is_submit']").val('0');
			return;
		}else{
			$("#price1_reminder").html("");
			$("input[name='is_submit']").val('1');
		}
	}

	//最小时间 stimemin 必须小于 最大时间 stimemax
	if(stimemin && stimemax){
		/*stimemin = stimemin.replace(/-/g,'/');
		stimemin = new Date(stimemin);
		stimemin = date.getTime().toString();
		stimemin = stimemin.substr(0,10);
		//alert(stimemin);
		stimemax = stimemax.replace(/-/g,'/');
		stimemax = new Date(stimemax);
		stimemax = date.getTime().toString();
		stimemax = stimemax.substr(0,10);
*/
		//stimemin = parseInt(stimemin);
		//stimemax = parseInt(stimemax);
		if(stimemin>stimemax){
			$("#stimemin_reminder").html("时间筛选区间输入有误！");
			$("input[name='is_submit']").val('0');
			return;
		}else{
			$("#stimemin_reminder").html("");
			$("input[name='is_submit']").val('1');
		}
	}

}

//通过参数判断是否可以被提交
function form_submit(){
	var is_submit = $("input[name='is_submit']").val();
	if(is_submit ==1){
		$('#search_form').submit();
	}
}

</script>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->

<!--分配任务-->
<div id="js_fenpeirenwu" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--分配房源-->
<div id="js_allocate_house" class="iframePopBox" style=" width:816px; height:340px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="340" class='iframePop' src=""></iframe>
</div>
<!--跟进信息弹框-->
<div id="js_genjin" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--详情页弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--匹配页弹框-->
<div id="js_pop_box_g_match" class="iframePopBox" style=" width:930px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="930" height="540" class='iframePop' src=""></iframe>
</div>
<!--页面处理中弹层-->
<div style="display:none; text-align: center;" id ='docation_loading'>
    <img src ="<?php echo MLS_SOURCE_URL; ?>/common/images/loading_6.gif">
    <p style="font-size: 16px; font-family:'微软雅黑'; line-height: 30px; color: #fff;">正在处理</p>
</div>
