<body>

<script>
    window.parent.addNavClass(17);
</script>
<!--导航栏-->
<div class="tab_box" id="js_tab_box"><?php echo $user_menu;?>
</div>
<!--主要内容-->

<!-- 上部菜单选项，按钮-->

<div class="search_box clearfix" id="js_search_box_02"><a href="javascript:void(0)" class="s_h" onclick="show_hide_info(this)" data-h="0">展开<span class="iconfont"></span></a>
    <form name="search_form" id="search_form" method="post" action="<?php echo MLS_URL;?>/data_transfer/index">
		<input type="hidden" id="rent_id_all_string" value="<?=$rent_id_all_string?>">
        <div class="fg_box">
            <p class="fg fg_tex">数据类型：</p>

            <div class="fg">
                <select class="select w90" name="type" id="type">
					<option value="none">请选择</option>
                    <option value="sell" <?=($post_param['type']=='sell')?"selected":""?>>出售房源</option>
                    <option value="rent" <?=($post_param['type']=='rent')?"selected":""?>>出租房源</option>
                    <option value="buy_customer" <?=($post_param['type']=='buy_customer')?"selected":""?>>求购客户</option>
                    <option value="rent_customer" <?=($post_param['type']=='rent_customer')?"selected":""?>>求租客户</option>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">区属：</p>
            <div class="fg">
                <select class="select" id='district' name='dist_id' onchange="districtchange(this.value);">
                    <option value='0'>不限</option>
                    <?php foreach ($district as $k => $v) { ?>
                        <option value="<?php echo $v['id'] ?>" <?php if($v['id']==$post_param['dist_id']){ echo "selected"; }?>><?php echo $v['district'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="fg_box">
            <p class="fg fg_tex"> 楼盘：</p>
            <div class="fg">
                <input type="text" name="cmt_name" id="block_name" value="<?php echo $post_param['cmt_name']; ?>" class="input w90">
                <input name="cmt_id" id="block_id" value="<?php echo $post_param['cmt_id']?>" type="hidden">
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
        <div class="fg_box hide">
            <p class="fg fg_tex"> 物业类型：</p>
            <div class="fg">
                <select class="select" name='property_type'>
                    <option value='0'>不限</option>
                    <?php
                        foreach($config['sell_type'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'" ';
                            if($key == $post_param['property_type'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">转出：</p>

            <div class="fg" style="*padding-top:10px;">
                <select class="select " name="store_name_out" id="store_name_out">
                    <option value="none" selected>请选择</option>
                    <?php foreach($agency as $key=>$val) { ?>
                        <option value="<?php echo $val['agency_id'];?>" <?=($post_param['store_name_out']==$val['agency_id'])?"selected":""?>><?php echo $val['agency_name'];?></option>
                    <?php }?>
                </select>
            </div>
			<div class="fg" style="*padding-top:10px; padding-left:10px ">
				<input type="hidden" id="broker_id_out" value="<?=$post_param['broker_id_out']?>">
                <select class="select" id="group_list_out" name="broker_id_out">
                    <option>请选择</option>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">转入：</p>

            <div class="fg" style="*padding-top:10px;">
                <select class="select " name="store_name_in" id="store_name_in">
                    <option value="none" selected>请选择</option>
                    <?php foreach($agency as $key=>$val) { ?>
                        <option value="<?php echo $val['agency_id'];?>" <?=($post_param['store_name_in']==$val['agency_id'])?"selected":""?>><?php echo $val['agency_name'];?></option>
                    <?php }?>
                </select>
            </div>

            <div class="fg" style="*padding-top:10px; padding-left:10px ">
				<input type="hidden" id="broker_id_in" value="<?=$post_param['broker_id_in']?>">
                <select class="select" id="group_list_in" name="broker_id_in">
                    <option>请选择</option>
                </select>
            </div>
        </div>

		<div class="fg_box">
			<div class="fg"> <a href="javascript:void(0)" class="btn" onclick="sub_form('search_form');return false;"><span class="btn_inner">查询</span></a> </div>
		</div>
		<div class="fg_box">
			<div class="fg"> <a href="javascript:void(0)" class="btn" onclick="move_data(1);"><span class="btn_inner">转移</span></a> </div>
		</div>
		<div class="fg_box">
			<div class="fg"> <a href="javascript:void(0)" class="btn" onclick="move_data(2);"><span class="btn_inner">全部转移</span></a> </div>
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
            <tbody>
                <tr>
                <td class="c2">
                    <div class="info">
                        <input id="js_checkbox" type="checkbox">
                    </div>
                </td>
                <td class="c3">
                    <div class="info">标签</div>
                </td>
                <td class="c3">
                    <div class="info">状态</div>
                </td>
                <td class="c3">
                    <div class="info">性质</div>
                </td>
                <td class="c3">
                    <div class="info">合作</div>
                </td>
                <td class="c4">
                    <div class="info">物业类型</div>
                </td>
                <td class="c3">
                    <div class="info">区属</div>
                </td>
                <td class="c3">
                    <div class="info">板块</div>
                </td>
                <td class="c6">
                    <div class="info">楼盘</div>
                </td>
                <td class="c3">
                    <div class="info">房龄</div>
                </td>
                <td class="c3">
                    <div class="info">户型</div>
                </td>
                <td class="c3">
                    <div class="info">朝向</div>
                </td>
                <td class="c3">
                    <div class="info">楼层</div>
                </td>
                <td class="c3">
                    <div class="info">装修</div>
                </td>
                <td class="c3">
                    <div class="info">面积 (㎡)</div>
                </td>
                <td class="c3">
                    <div class="info">租金</div>
                </td>
                <!--<td class="c4">
                    <div class="info">单价 (元/㎡)</div>
                </td>-->
                <td class="c6">
                    <div class="info">委托门店</div>
                </td>
                <td class="c3">
                    <div class="info">委托经纪人</div>
                </td>
                <td class="c6">
                    <div class="info">联系方式</div>
                </td>
            </tr>
        </tbody></table>
    </div>
    <div style="height: 226px;" class="inner shop_inner" id="js_inner">
        <table class="table">
            <tbody>
            <?php
			if(is_array($list) && !empty($list)){
				foreach($list as $key =>$val) {?>
            <tr class="bg">
                <td class="c2">
                    <div class="info">
                        <input class="checkbox" type="checkbox" value="<?php echo $val['id']?>" name="move_data">
                    </div>
                </td>
                <td class="c3">
                    <div class="info">
                        <?php if($val['pic']){ ?><span title="此房源有图片" class="iconfont ts">&#xe645;</span><?php } ?>
                        <?php if($val['rententrust']==1){ ?><span title="独家代理" class="iconfont ts">&#xe646;</span><?php } ?>
                        <?php if($val['keys']){?><span title="此房源有钥匙"  class="iconfont ts ts02">&#xe60d;</span><?php } ?>
                        <?php if($val['lock']){ ?><span title="已被锁定"  class="iconfont ts ts02">&#xe632;</span><?php } ?>
                    </div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $config['status'][$val['status']]; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $config['nature'][$val['nature']]; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php if($val['isshare']){ ?>是<?php }else{ ?>否<?php } ?></div>
                </td>
                <td class="c4">
                    <div class="info"><?php echo $config['sell_type'][$val['sell_type']]; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $district[$val['district_id']]['district']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $street[$val['street_id']]['streetname']; ?></div>
                </td>
                <td class="c6">
                    <div class="info"><?php echo $val['block_name']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $val['buildyear']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $val['room']; ?>-<?php echo $val['hall']; ?>-<?php echo $val['toilet']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $config['forward'][$val['forward']]; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $val['floor']; ?><?php if($val['floor_type']==2){ echo "-".$val['subfloor'];}?>/<?php echo $val['totalfloor']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo !empty($config['fitment'][$val['fitment']]) ? $config['fitment'][$val['fitment']] : ''; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo strip_end_0($val['buildarea']);?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo strip_end_0($val['price']); if($val['price_danwei'] == 1){echo "元/㎡*天";}else{echo "元/月";}?></div>
                </td>
                <!--<td class="c4">
                    <div class="info">28888</div>
                </td>-->
                <td class="c6">
                    <div class="info"><?php echo $val['agency_name'];?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $val['broker_name']; ?></div>
                </td>
                <td class="c6">
                    <div class="info"><?php echo $val['telno']?></div>
                </td>
            </tr>
            <?php
                }
                }else{
            ?>
            <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php } ?>
          </tbody>
        </table>
    </div>
</div>
<!-- 上部菜单选项，按钮---end-->
<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
        <!--<a onclick="search_form.page.value=2;search_form.submit();return false;" href="javascript:void(0)">下一页</a>
        <span>共13455条</span><input class="input" name="page" value="1" type="hidden">-->
    </div>
</div>

<!--提示框-->
<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">操作成功！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="location.href='/data_transfer/'">确定</button>
			</div>
		</div>
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
<div id="js_pop_do_delete"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_delete_tip">确定要转移选定的记录吗？</p>
				<button type="button" id="dialog_btn" class="btn-lv1 btn-left JS_Close" move_data_type="">确定</button>
				<button type="button" class="btn-hui1 JS_Close">取消</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    //确认转移数据
    $('#dialog_btn').bind('click', function() {
        _move_data($(this).attr('move_data_type'));
    });
	//转移数据
	function _move_data(move_type){
		var broker_id_out = $("#group_list_out").val();
		var id= [];
		if(move_type==1){
			$("input[name='move_data']").each(function() {
				if ($(this).attr("checked")) {
					id.push($(this).val());
				}
			});
		}else if(move_type==2){
			var id_string = $("#rent_id_all_string").val();
			id = id_string.split(",");
		}else{
			return false;
		}
		var broker_id = $("#group_list_in").val();
		var type = $("#type").val();
		//alert(id);
		//alert(broker_id);
		//alert(type);
		if(id.length == 0){
        	$("#dialog_do_warnig_tip").html("请勾选要转移的房客源！");
    		openWin('js_pop_do_warning');
			return false;
        }
//		if(broker_id_out == 0){
//			$("#dialog_do_warnig_tip").html("请选择转出的人！");
//    		openWin('js_pop_do_warning');
//			return false;
//		}
		if(broker_id == 0){
			$("#dialog_do_warnig_tip").html("请选择转入的人！");
    		openWin('js_pop_do_warning');
			return false;
		}

		$.ajax({
			url:"/data_transfer/move_data",
			type:"post",
			//dataType:"json",
			data:{
				id:id,
				broker_id:broker_id,
				type:type,
				broker_id_out:broker_id_out
			},
			cache:false,
			error:function(){
				alert("系统错误");
				return false;
			},
			success: function (data) {
				//alert(data);
				if(data==id.length && data!=0){
					$("#dialog_do_success_tip").html("转移成功");
					openWin('js_pop_do_success');
				}else{
					$("#dialog_do_warnig_tip").html("转移失败");
					openWin('js_pop_do_warning');
				}
			}
		});

	};

	function move_data(move_type){
        openWin('js_pop_do_delete');
        $('#dialog_btn').attr('move_data_type', move_type);
	};

</script>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->

