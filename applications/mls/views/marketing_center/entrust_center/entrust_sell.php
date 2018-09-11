<script>
    window.parent.addNavClass(20);
</script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div id="js_search_box" class="shop_tab_title forms">
	<p class="fr">今日新增<strong class="f00"><?=$today_total;?></strong>条房源</p>
	<?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
	<b class="label" onclick = "javascript:location.href='/entrust_center/ent_sell/1';">已抢房源</b>
	<a class="wh fl" href="javascript:void(0);" onclick="openWin('js_grab_rule');">查看抢拍规则</a>
</div>


<div class="search_box clearfix"  id="js_search_box_02">
	<form name="search_form" id="search_form" method="post" action="<?php echo MLS_URL;?>/entrust_center/ent_sell">
	<input type="hidden" name="is_submit" value="1">
	<!--<a href="javascript:void(0);" class="s_h" onClick="show_hide_info(this)" data-h="0">展开<span class="iconfont">&#xe609;</span></a>-->
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
		<p class="fg fg_tex">区属：</p>
		<div class="fg">
			<select class="select" id='district' name='district' onchange="districtchange(this.value);">
				<option value='0'>不限</option>
				<?php foreach ($district as $k => $v) { ?>
					<option value="<?php echo $v['id'] ?>" <?php if($v['id']==$post_param['district']){ echo "selected"; }?>><?php echo $v['district'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="fg_box">
		<p class="fg fg_tex"> 板块：</p>
		<div class="fg">
			<select class="select" name='street' id='street'>
				<option value='0'>不限</option>
				<?php
					if($post_param['district']>0)
					{
						foreach($street as $k => $v)
						{
							if($v['dist_id'] == $post_param['district'])
							{
								echo "<option value='".$v['id']."'";
								if($v['id'] == $post_param['street'])
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
	<div class="fg_box">
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
	<input type="hidden" name="pg" value="1">
	<input type="hidden" name="spec" value="sell">
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');form_submit();return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="<?php echo MLS_URL;?>/entrust_center/ent_sell" class="reset">重置</a> </div>
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
                <td class="c15"><div class="info">楼盘</div></td>
              	<td class="c11"><div class="info">区属</div></td>
                <td class="c11"><div class="info">板块</div></td>
                <td class="c11"><div class="info">面积(㎡)</div></td>
  				<td class="c11"><div class="info">总价(万)</div></td>
                <td class="c12"><div class="info">委托时间</div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
		<?php
			if($list){
				foreach($list as $vo){?>
			<!--<tr onClick="detail_pop(<?php echo $vo['id'];?>)">-->
			<tr>
                <td class="c15"><div class="info"><?=$vo['comt_name'];?></div></td>
                <td class="c11">
					<div class="info">
					<?php
					foreach ($district as $k => $v) {
						if($v['id']== $vo['dist_id']){
							echo $v['district'];
						}
					}
					?>
					</div>
				</td>
                <td class="c11">
					<div class="info">
					<?php
					foreach ($street as $k => $v) {
						if($v['id']== $vo['streetid']){
							echo $v['streetname'];
						}
					}
					?>
					</div>
				</td>
                <td class="c11"><div class="info"><?=$vo['area'];?></div></td>
  				<td class="c11"><div class="info"><strong class="f60"><?=$vo['hprice'];?></strong></div></td>
                <td class="c12"><div class="info"><?=date("Y-m-d H:i:s",$vo['ctime']);?></div></td>
				<?php if($vo['grab_times']>=10){?>
				<td ><div class="info c999">很遗憾，名额已经被抢完了</div></td>
				<?php }else if($vo['grab_times']==0){?>
				<td ><div class="info">暂无人抢，还有<strong class="f00"><?=(10-$vo['grab_times']);?></strong>个名额　<a class="btn-lv" href="javascript:void(0)" onClick="grab(<?=$vo['id'];?>);"><span>抢房源</span></a></div></td>
				<?php }else{?>
                <td ><div class="info">已有<strong class="f00"><?=$vo['grab_times'];?></strong>人抢过，还有<strong class="f00"><?=(10-$vo['grab_times']);?></strong>个名额　<a class="btn-lv" href="javascript:void(0)" onClick="grab(<?=$vo['id'];?>);"><span>抢房源</span></a></div></td>
				<?php } ?>
            </tr>
		<?php }}else{?>
			<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
		<?php }?>
        </table>
    </div>
</div>

<div class="fun_btn clearfix" id="js_fun_btn">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>
<!--抢拍规则提示-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="width:350px; display:none;" id="js_grab_rule">
    <div class="hd">
        <div class="title">抢拍规则</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
			<p style="padding:10px 0 15px; line-height:24px; text-align:left;">1、委托房源及客户需求自开放后抢拍名额满10个，房源、客源将不能被继续抢拍；<br>
			2、每个人每天最多分别可以抢5条委托房源，5条求购求租；<br>
			3、未认证用户不可参与房源、客源的抢拍；<br>
			4、抢房源、客源成功后可显示用户联系方式。</p>
			<div><button class="btn-lv1 btn-mid JS_Close" type="button">确定</button></div>
		</div>
    </div>
</div>

<!--通用错误弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_grab_error">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"  onclick="check_list(<?=$page;?>);"></a></div>
    </div>
	<div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
				<div class="text-wrap">
					<table>
						<tr>
                            <td><div class="img"><img alt="" id="imgg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></div></td>
                            <td class="msg"><span class="bold"></span></td>
                        </tr>
					</table>
				</div>
				<button class="btn JS_Close" type="button"  onclick="check_list(<?=$page;?>);">确定</button>
			 </div>
         </div>
    </div>
</div>

<!--认证经纪人错误弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_grab_wrong">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="check_list(<?=$page;?>);""></a></div>
    </div>

	<div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
				<div class="text-wrap mb10">
					<table class="mb10">
						<tr>
                            <td><div class="img"><img alt="" id="imgg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></div></td>
                            <td class="msg"><span class="bold">请先到个人中心认证！</span></td>
                        </tr>
					</table>
				</div>
                <button class="btn-lv1 btn-left JS_Close" type="button" onclick="javascript:location.href='/my_info/index';return false;">确定</button>
			 </div>
         </div>
    </div>
</div>

<!--客户委托房源抢拍弹框-->
<div class="pop_box_g pop_see_info_deal" style="width:430px; height:300px; display:none;" id="js_grab">
    <div class="hd">
        <div class="title">客户委托房源</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="check_list(<?=$page;?>);">&#xe60c;</a></div>
    </div>
    <div class="mod">
    	<div class="inner" style="height:187px;">
			<table class="success border-bot">
				<tr>
					<td class="c20" align="right"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
					<td>
						<p class="pl10">抢拍成功，赶紧和客户联系吧！祝您早日开单！</p>
					</td>
				</tr>
			</table>
			<table class="table-col2 table-col3 mt10">
				<tbody><tr>
					<td class="td1">姓名：</td>
					<td class="td2" id="detail_realname"></td>
					<td class="td1">电话：</td>
					<td class="td2" id="detail_phone"  style="vertical-align:top;"></td>
				</tr>
				<tr>
					<td class="td1">楼盘：</td>
					<td class="td2" id="detail_comt"></td>
					<td class="td1">区属：</td>
					<td class="td2" id="detail_district"></td>
				</tr>
				<tr>
					<td class="td1">板块：</td>
					<td class="td2" id="detail_street"></td>
					<td class="td1">面积：</td>
					<td class="td2" id="detail_area"><strong></strong>㎡</td>
				</tr>
				<tr>
					<td class="td1">总价：</td>
					<td class="td2" id="detail_price"><strong class="f60"></strong>万元</td>
					<td class="td1"></td>
					<td class="td2"></td>
				</tr>
			</tbody></table>
        </div>
		<a href="javascript:void(0)" class="btn-lv1 btn-mid JS_Close" style="margin-top:10px;" onclick="check_list(<?=$page;?>);">确定</a>
    </div>
</div>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->

<script>
//详情操作弹出框
/*function detail_pop(id){
	alert(id);
	$.ajax({
		type: "POST",
		url: "/entrust_center/detail_sell/",
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
			$("#d_floor").html(data['floor']);
			$("#d_tfloor").html(data['tfloor']);
			$("#d_remark").html(data['remark']);
			openWin('js_pop_see_info_house');
		}
	});

}*/

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
}

//通过参数判断是否可以被提交
    function form_submit(){
        var is_submit = $("input[name='is_submit']").val();
        if(is_submit ==1){
            $('#search_form').submit();
        }
    }

//抢房源
function grab(id){
	$.ajax({
		url:"/ent_grab/grab/",
		type:"POST",
		dataType:"json",
		data:{
			isajax:1,
			id:id,
			type:'ent_sell'
		},
		success:function(data){
			if(data['result'] ==200){
				//$("#detail_id").val(data['data']['id']);
				$("#detail_realname").text(data['data']['realname']);
				$("#detail_phone").text(data['data']['phone']);
				$("#detail_district").text(data['data']['district']);
				$("#detail_street").text(data['data']['street']);
				$("#detail_comt").text(data['data']['comt_name']);
				$("#detail_area").children('strong').text(data['data']['area']);
				$("#detail_price").children('strong').text(data['data']['hprice']);
				//$("#js_grab").show();
				openWin('js_grab');
			}else if(data['result'] ==101){
				openWin('js_grab_wrong');
			}else{
				$("#js_grab_error").find('span.bold').text(data['msg']);
				openWin('js_grab_error');
			}
		}
	});
}

//抢拍成功之后刷新当前页，如果没有数据，返回上一页
	function check_list(page){
	    $.post(
            '/entrust_center/check_list',
			{'page':page
			},
            function(data){
                if(data == '0'){
                    if(page >1){
					    page = page-1;
					}
                }
				$('#search_form :input[name=page]').val(page);form_submit();return false;
			}
        );
	}
</script>
