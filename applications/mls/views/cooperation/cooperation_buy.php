<script>
    window.parent.addNavClass(17);
</script>
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>

<div id="js_search_box" class="tab_box">
	<?php echo $user_menu;?>
</div>
<div id="js_search_box" class="shop_tab_title  scr_clear" style="margin-bottom:0;">
   <?php echo $user_func_menu;?>
</div>
<div class="search_box clearfix"  id="js_search_box_02">
	<form name="search_form" id="search_form" method="post" action="">
	 <?php if(is_int($company_id) && $company_id>0){?>
    <div class="fg_box">
        <p class="fg fg_tex"> 范围：</p>
        <div class="fg">
            <select class="select" name="agenctcode" onchange="chang('sell')">
					<?php if($agency_list && $role_level != 6){?>
                		<option selected value='0'>不限</option>
                        <?php
						foreach($agency_list as $key=>$val){
						?>
						<option <?php if($val['agency_id'] == $post_param['agenctcode'] || ($val['agency_id']==$agency_id && $post_param['broker_id'] == ''))
                                echo "selected"; ?> value="<?php echo $val['agency_id'];?>"><?php echo $val['agency_name'];?></option>
					<?php }}else{?>
						<option value='<?php echo $agency_id;?>' selected><?php echo $agency_list;?></option>
					<?php }?>
				</select>
        </div>
		<div class="fg fg_tex fg_tex03" >
                    <select class="select" name="broker_id" id="list_broker">
                        <option value='0'>不限</option>
                    <?php if($broker_list){ ?>
						<?php foreach($broker_list as $key=>$val){ ?>
						<option  <?php if($val['broker_id'] == $post_param['broker_id'] ||($val['broker_id']==$broker_id && $post_param['broker_id'] == ''))
                                echo "selected"; ?> value='<?php echo $val['broker_id']?>'><?php echo $val['truename']?></option>
					<?php }}?>
                    </select>

		</div>
    </div>
	<?php }else{?>
            <?php if(!empty($register_info['corpname']) && !empty($register_info['storename'])){?>
                <div class="fg_box">
                        <p class="fg fg_tex"> 范围：</p>
                        <div class="fg">
                        <select class="select">
                            <option><?php echo $register_info['corpname'];?></option>
                        </select>
                        </div>
                        <div class="fg fg_tex fg_tex03" >
                            <select class="select">
                                <option><?php echo $register_info['storename'];?></option>
                            </select>
                        </div>
                </div>

            <?php }?>
     <?php }?>

		<!--获取经纪人信息-->
<script>
function chang(type){
 var agency_id=$("select[name='agenctcode']").val();
 $.ajax({
	url: "<?php echo MLS_URL;?>/"+type+"/broker_list/",
	type: "GET",
	dataType: "json",
	data:{agency_id: agency_id},
	success:function(data_list){
		var str_html='<option value="0">不限</option>';
        if(agency_id>0){
            for(var i=0;i<data_list.length;i++){
                str_html +='<option value='+data_list[i].broker_id+'>'+data_list[i].truename+'</option>';
            }
        }
		$("#list_broker").empty().html(str_html);
	}
 });

}
</script>

    <div class="fg_box">
        <p class="fg fg_tex"> 区属：</p>
        <div class="fg">
           <select class="select" name='dist_id' onchange ="get_street_by_id(this , 'street_id')">
                <option selected="" value="0">不限</option>
                <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                <?php foreach($district_arr as $key => $value){ ?>
                <option value="<?php echo $value['id'];?>" <?php if($post_param['dist_id'] == $value['id']){ echo 'selected';  } ?>>
                <?php echo $value['district'];?>
                </option>
                <?php } ?>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">  板块：</p>
        <div class="fg">
           <select class="select"  name='street_id' id="street_id">
                <option value="0">不限</option>
                <?php if(is_array($select_info['street_info']) && !empty($select_info['street_info'])){ ?>
                <?php foreach($select_info['street_info'] as $key =>$value){ ?>
                <option value="<?php echo $value['id'];?>" <?php if($post_param['street_id'] == $value['id']){ echo 'selected';} ?>>
                <?php echo $value['streetname'];?>
                </option>
                <?php } ?>
                <?php } ?>
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

	<input type="hidden" name="pg" value="1">
	<input type="hidden" name="black" value="blacklist">
    <div class="fg_box">
            <input type="hidden" name='orderby_id' id="orderby_id" value="<?php echo $post_param['orderby_id']?>">
            <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;"><span  class="btn_inner">搜索</span></a> </div>
            <div class="fg"> <a href="/customer/lists_buy/" class="reset">重置</a> </div>
        </div>
</div>


<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
				<td class="c3"><div class="info"></div></td>
              	<td class="c5"><div class="info">交易</div></td>
                <td class="c5"><div class="info">客源编号</div></td>
                <td class="c5"><div class="info">物业类型</div></td>

                <td class="c9"><div class="info">意向区属板块</div></td>
                <td class="c9"><div class="info">意向楼盘</div></td>
                <td class="c5"><div class="info"><a href="javascript:void(0);" onclick="selllist_order(3);return false;" id="order_year" class="i_text <?php if($post_param['orderby_id'] == 3 ){ echo "i_down"; }elseif($post_param['orderby_id'] == 4){ echo "i_up"; } ?>">房龄</a></div></td>
                <td class="c5"><div class="info">户型</div></td>

                <td class="c5"><div class="info"><a href="javascript:void(0)" onclick="selllist_order(5);return false;" id="order_area" class="i_text <?php if($post_param['orderby_id'] == 6 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 5){ echo 'i_up'; } ?>">面积<br>
                        (㎡)</a></div></td>
                <td class="c5"><div class="info"><a href="javascript:void(0)" onclick="selllist_order(7);return false;" id="order_price" class="i_text ">总价<br>(W)</a></div></td>
                <td class="c7"><div class="info">委托门店</div></td>
                <td class="c7"><div class="info">委托人</div></td>
                <td class="c7"><div class="info">联系方式</div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table table_q">
            <?php if(is_array($customer_list) && !empty($customer_list)){ ?>
            <?php foreach ($customer_list as $key =>$val) {?>

			<tr id="<?php echo $val['id'];?>">
				<td class="c3"><div class="info"><input type="checkbox" class="checkbox" name="items" value="28"></div></td>
				<td class="c5"><div class="info">交易</div></td>
				<td class="c5"><div class="info"><?php echo $val['id'];?></div></td>
				<td class="c5"><div class="info">
				<?php
                    if(isset($conf_customer['property_type'][$val['property_type']]))
                    {
                        echo $conf_customer['property_type'][$val['property_type']];
                    }
                ?>
				</div></td>

				<td class="c9"><div class="info">
				<?php
                    $district_str = '';
                    if($val['dist_id1'] > 0 && isset($district_arr[$val['dist_id1']]['district']))
                    {
                        $district_str =  $district_arr[$val['dist_id1']]['district'];
                        if($district_str != '' && $val['street_id1'] > 0 && !empty($street_arr[$val['street_id1']]['streetname']))
                        {
                            $district_str .=  '-'.$street_arr[$val['street_id1']]['streetname'];
                        }
                    }

                    if($val['dist_id2'] > 0 && isset($district_arr[$val['dist_id2']]['district']))
                    {
                        $district_str .=  !empty($district_str) ? '，'.$district_arr[$val['dist_id2']]['district'] :
                            $district_arr[$val['dist_id2']]['district'];

                        if( !empty($district_arr[$val['dist_id2']]['district']) &&
                            $val['street_id2'] > 0 && !empty($street_arr[$val['street_id2']]['streetname']))
                        {
                           $district_str .=  '-'.$street_arr[$val['street_id2']]['streetname'];
                        }
                    }

                    if($val['dist_id3'] > 0 && isset($district_arr[$val['dist_id3']]['district']))
                    {
                        $district_str .=  !empty($district_str) ? '，'.$district_arr[$val['dist_id3']]['district'] :
                             $district_arr[$val['dist_id3']]['district'];

                        if(!empty($district_arr[$val['dist_id3']]['district']) &&
                           $val['street_id3'] > 0 && !empty($street_arr[$val['street_id3']]['streetname']))
                        {
                           $district_str .= '-'.$street_arr[$val['street_id3']]['streetname'];
                        }
                    }
                    echo $district_str ;
                    ?>



				</div></td>
				<td class="c9"><div class="info">
				<?php
                    if(isset($val['cmt_name1']) && $val['cmt_name1'] != '' )
                    {
                        echo $val['cmt_name1'];
                    }

                    if(isset($val['cmt_name2']) && $val['cmt_name2'] != '' )
                    {
                        echo '，'.$val['cmt_name2'];
                    }

                    if(isset($val['cmt_name3']) && $val['cmt_name3'] != '')
                    {
                        echo '，'.$val['cmt_name3'];
                    }
				?>
				</div></td>
				<td class="c5"><div class="info"><?php echo $val['house_age']; ?></div></td>
				<td class="c5"><div class="info"><?php echo $val['room_min'];?>-<?php echo $val['room_max'];?></div></td>

				<td class="c5"><div class="info"><?php echo strip_end_0($val['area_min']);?>-<?php echo strip_end_0($val['area_max']);?></div></td>
				<td class="c5"><div class="info"><?php echo strip_end_0($val['price_min']);?>-<?php echo strip_end_0($val['price_max']);?></div></td>
				<td class="c7"><div class="info"><?php echo $agency_name;?></div></td>
				<td class="c8 js_info broker"><div class="info"><?php echo $val['broker_name']; ?></div></td>
				<td class="c7"><div class="info"><?php echo $phone;?></div></td>
				<td >
					<div class="info"><div class="info">
						<a href="javascript:void(0);" onclick="is_share('<?=$val['id']?>',1)" class="fun_link">通过</a>
						<a href="javascript:void(0);" onclick="is_refuse('<?=$val['id']?>',0)" class="fun_link">拒绝</a>
					</div></div>
				</td>
			</tr>
			<?php }}else{?>
			<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
			<?php }?>
        </table>
    </div>
</div>
<div id="js_fun_btn" class="fun_btn clearfix">
	<div class="get_page">
		<span><?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?></span>
		<input type="hidden" class="input" name="page" value="1">
	</div>
	<input type="checkbox" id="js_checkbox" style="float:left; margin:3px 10px 0 0;" onclick="getid()">
    <a class="btn-lan btn-left" href="javascript:void(0);" onclick="all_share(1)"><span>通过</span></a>
	<a class="btn-lan" href="javascript:void(0);" onclick="all_refuse(0)"><span>拒绝</span></a>
	<span class='error'></span>
</div>
<p class="tips1" id="js_gz_box_bg">&nbsp;&nbsp;</p>
</form>

<!--询问操作确定弹窗-->
<div id="jss_pop_tip" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" id="dialogSaveDiv" style="font-size:14px;"></p>
                     <div class="center">
                    <button type="button" id = 'dialog_share' class="btn-lv1 btn-left JS_Close" >确定</button>
                         <button type="button" style="" class="btn-hui1 JS_Close">取消</button>
                    </div>
                    <input type ="hidden" name='ci_id' id = 'rowid' value = ''>
                    <input type ="hidden" name='secret_key' id = 'secret_key' value = ''>
                    <input type ="hidden" name='atction_type' id = 'atction_type' value = ''>
                    <input type ="hidden" name='do_type' id = 'do_type' value = ''>
                </div>
            </div>
    </div>
</div>
<!-- 确认通过+提示 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg1">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="<?php echo MLS_URL;?>/customer/lists_buy"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">

                <p class="text"><img class="img_msg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                    <span id="dialog_do_itp" class="span_msg">合作通过！</span>
                </p>
            </div>
        </div>
    </div>
</div>
<!-- 拒绝通过+提示 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg2">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="<?php echo MLS_URL;?>/customer/lists_buy"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">

                <p class="text"><img class="img_msg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                    <span id="dialog_do_itp" class="span_msg">拒绝合作！</span>
                </p>
            </div>
        </div>
    </div>
</div>
<script>
function getIds() {
	var idstring = "";
	$(".checkbox:checked").each(function(){
		idstring += $(this).parent().parent().parent().attr("id")+"|";
		//alert($(this).parent().parent().html());
	});
	var len = idstring.length;
	//idstring = idstring.substr(-1);
	//alert(idstring.substr(0,len-1));
	return idstring.substr(0,len-1);
}
function all_share(type){
	var ids=getIds();
	if(ids == ''){
		$('.error').html('<font size="2px" color="red"><strong>请选择需要审核的房(客)源</strong></font>');
		return false;
	}else{
		$('.error').html('');
	}
	$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 确认房源合作审核全部通过？');
	openWin('jss_pop_tip');
	$("#dialog_share").click(function(){

		$.ajax({
			url: "<?php echo MLS_URL;?>/customer/change_all_share/",
			type: "GET",
			dataType: "json",
			data:{ids:ids,type:type},
			success: function(data) {
				if(data == 'ok')
				{
					openWin('js_pop_msg1');
				}
			}
		});
	});
}
function all_refuse(type){
	var ids=getIds();
	if(ids == ''){
		$('.error').html('<font size="2px" color="red"><strong>请选择需要审核的房(客)源</strong></font>');
		return false;
	}else{
		$('.error').html('');
	}
	$("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 确认房源合作审核全部拒绝？');
	openWin('jss_pop_tip');
	$("#dialog_share").click(function(){

		$.ajax({
			url: "<?php echo MLS_URL;?>/customer/change_all_share/",
			type: "GET",
			dataType: "json",
			data:{ids:ids,type:type},
			success: function(data) {
				if(data == 'fail')
				{
					openWin('js_pop_msg2');
				}
			}
		});
	});
}


function is_share(buy_customer_id,type){
    $("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 确认房源合作审核通过？');
    openWin('jss_pop_tip');
    $("#dialog_share").click(function(){
        $.ajax({
            url: "<?php echo MLS_URL;?>/customer/change_is_share/",
            type: "GET",
            dataType: "json",
            data: {

                buy_customer_id:buy_customer_id,
                type:type
            },
            success: function(data) {
                if(data == 'ok')
                {
                    openWin('js_pop_msg1');
                }
            }
        });
    });
}
function is_refuse(buy_customer_id,type){
    $("#dialogSaveDiv").html('<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" id="imgg" alt=""> 确认拒绝房源合作？');
    openWin('jss_pop_tip');
    $("#dialog_share").click(function(){
        $.ajax({
            url: "<?php echo MLS_URL;?>/customer/change_is_share/",
            type: "GET",
            dataType: "json",
            data: {

                buy_customer_id:buy_customer_id,
                type:type
            },
            success: function(data) {
                if(data == 'fail')
                {
                    openWin('js_pop_msg2');
                }
            }
        });
    });
}
</script>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->

<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=house.js,openWin.js,backspace.js,broker_common.js"></script>
