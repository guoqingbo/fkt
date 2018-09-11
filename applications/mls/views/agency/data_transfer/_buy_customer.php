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
		<input type="hidden" id="customer_id_all_string" value="<?=$customer_id_all_string?>">
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
                <select class="select w90" name='dist_id'>
                    <option value="0">不限</option>
                    <?php
					if( is_array($district_arr) && !empty($district_arr) ){
						foreach($district_arr as $key => $value){
					?>
					<option value="<?php echo $value['id'];?>" <?php if($post_param['dist_id'] == $value['id']){ echo 'selected';  } ?>>
					<?php echo $value['district'];?>
					</option>
					<?php
						}
					}
					?>
                </select>
            </div>
        </div>

        <div class="fg_box">
            <p class="fg fg_tex">楼盘： </p>

            <div class="fg">
				<input type="text" name='cmt_name' class="input w90" id='block01' value="<?php echo $post_param['cmt_name'];?>" class="input w110">
				<input type="hidden" name='cmt_id' id='cmt_id' value='<?php echo $post_param['cmt_id'];?>'>
            </div>
        </div>
		<div class="fg_box hide">
			<p class="fg fg_tex"> 物业类型：</p>
			<div class="fg">
				<select class="select" name='property_type'>
					<option value="0">不限</option>
					<?php
					if(is_array($conf_customer['property_type']) && !empty($conf_customer['property_type'])) {
						foreach($conf_customer['property_type'] as $key => $value){
					?>
						<option value='<?php echo $key;?>' <?php if($post_param['property_type'] == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
					<?php
						}
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
                    <?php foreach($agency as $key=>$val) { print_r($val);?>
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
                    <?php foreach($agency as $key=>$val) { print_r($val);?>
                        <option value="<?php echo $val['agency_id'];?>" <?=($post_param['store_name_in']==$val['agency_id'])?"selected":""?>><?php echo $val['agency_name'];?></option>
                    <?php }?>
                </select>
            </div>

            <div class="fg" style="*padding-top:10px; padding-left:10px ">
				<input type="hidden" id="broker_id_in" value="<?=$post_param['broker_id_in']?>">
                <select class="select" id="group_list_in" name="broker_id_in">
                    <option value="0">请选择</option>
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
<!-- 上部菜单选项，按钮---end-->

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <td class="c2">
                    <div class="info">
                        <input type="checkbox" id="js_checkbox">
                    </div>
                </td>
                <td class="c5">
                    <div class="info">标签</div>
                </td>
                <td class="c6">
                    <div class="info">性质</div>
                </td>
                <td class="c4">
                    <div class="info">合作</div>
                </td>
                <td class="c6">
                    <div class="info">客户</div>
                </td>
                <td class="c15">
                    <div class="info">意向区属板块</div>
                </td>
                <td class="c15">
                    <div class="info">意向楼盘</div>
                </td>
                <td class="c6">
                    <div class="info">物业类型</div>
                </td>
                <td class="c6">
                    <div class="info">面积(㎡)</div>
                </td>
                <td class="c6">
                    <div class="info">总价(W)</div>
                </td>
                <td class="c6">
                    <div class="info">户型(室)</div>
                </td>
                <td >
                    <div class="info">跟进时间</div>
                </td>
                <td class="c6">
                    <div class="info">经纪人</div>
                </td>

            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
			<input type="hidden" value="<?php echo $group_id?>" id="group_id">
			<?php
            if(is_array($customer_list) && !empty($customer_list)){
                foreach ($customer_list as $key =>$value) {
			?>
            <tr class="bg">
                <td class="c2">
                    <div class="info">
                        <input type="checkbox" class="checkbox" value="<?php echo $value['id']?>" name="move_data">
                    </div>
                </td>
                <td class="c5">
                    <div class="info">
                        <?php if($value['lock']){ ?><span class="iconfont ts ts02" title="已被锁定">&#xe632;</span><?php } ?>
                    </div>
                </td>
                <td class="c6">
                    <div class="info" id="public_type<?php echo $value['id'];?>">
					<?php
					if(isset($conf_customer['public_type'][$value['public_type']]) && $conf_customer['public_type'][$value['public_type']] != '')
					{
						echo $conf_customer['public_type'][$value['public_type']];
					}
					?>
					</div>
                </td>
                <td class="c4">
                    <div class="info">
                    <?php
                    if(isset($conf_customer['is_share'][$value['is_share']]) && $conf_customer['is_share'][$value['is_share']] != '')
                    {
                        echo $conf_customer['is_share'][$value['is_share']];
                    }
                    else
                    {
                       echo '否';
                    }
                    ?>
					<input type="hidden" id="share_num<?php echo $value['id']?>" value="<?php echo $value['is_share'];?>"/>
    				<input type="hidden" value="<?php echo $value['is_report']?>" id="is_report<?php echo $value['id']?>">
                    </div>
                </td>
                <td class="c6">
                    <div class="info"><?php echo $value['truename'];?></div>
                </td>
                <td class="c15">
                    <div class="info">
                    <?php
                    $district_str = '';
                    if($value['dist_id1'] > 0 && isset($district_arr[$value['dist_id1']]['district']))
                    {
                        $district_str =  $district_arr[$value['dist_id1']]['district'];
                        if($district_str != '' && $value['street_id1'] > 0 && !empty($street_arr[$value['street_id1']]['streetname']))
                        {
                            $district_str .=  '-'.$street_arr[$value['street_id1']]['streetname'];
                        }
                    }

                    if($value['dist_id2'] > 0 && isset($district_arr[$value['dist_id2']]['district']))
                    {
                        $district_str .=  !empty($district_str) ? '，'.$district_arr[$value['dist_id2']]['district'] :
                            $district_arr[$value['dist_id2']]['district'];

                        if( !empty($district_arr[$value['dist_id2']]['district']) &&
                            $value['street_id2'] > 0 && !empty($street_arr[$value['street_id2']]['streetname']))
                        {
                           $district_str .=  '-'.$street_arr[$value['street_id2']]['streetname'];
                        }
                    }

                    if($value['dist_id3'] > 0 && isset($district_arr[$value['dist_id3']]['district']))
                    {
                        $district_str .=  !empty($district_str) ? '，'.$district_arr[$value['dist_id3']]['district'] :
                             $district_arr[$value['dist_id3']]['district'];

                        if(!empty($district_arr[$value['dist_id3']]['district']) &&
                           $value['street_id3'] > 0 && !empty($street_arr[$value['street_id3']]['streetname']))
                        {
                           $district_str .= '-'.$street_arr[$value['street_id3']]['streetname'];
                        }
                    }
                    echo $district_str ;
                    ?>
                    </div>
                </td>
                <td class="c15">
                    <div class="info">
                        <?php
                        if(isset($value['cmt_name1']) && $value['cmt_name1'] != '' )
                        {
                            echo $value['cmt_name1'];
                        }

                        if(isset($value['cmt_name2']) && $value['cmt_name2'] != '' )
                        {
                            echo '，'.$value['cmt_name2'];
                        }

                        if(isset($value['cmt_name3']) && $value['cmt_name3'] != '')
                        {
                            echo '，'.$value['cmt_name3'];
                        }
                        ?>
                    </div>
                </td>
                <td class="c6">
                    <div class="info">
					<?php
						if(isset($conf_customer['property_type'][$value['property_type']]))
						{
							echo $conf_customer['property_type'][$value['property_type']];
						}
					?>
					</div>
                </td>
                <td class="c6">
                    <div class="info f60"><?php echo strip_end_0($value['area_min']);?>-<?php echo strip_end_0($value['area_max']);?></div>
                </td>
                <td class="c6">
                    <div class="info f60"><?php echo strip_end_0($value['price_min']);?>-<?php echo strip_end_0($value['price_max']);?></div>
                </td>
                <td class="c6">
                    <div class="info"><?php echo $value['room_min'];?>-<?php echo $value['room_max'];?></div>
                </td>
                <td >
                    <div class="info info_p_r" >
                        <?php echo date('Y-m-d H:i' , $value['updatetime']);?>
                    </div>
                </td>
                <td class="c6">
                    <div class="info">
					<?php
					if(isset($customer_broker_info[$value['broker_id']]['truename']) && $customer_broker_info[$value['broker_id']]['truename'] != '')
					{
						echo $customer_broker_info[$value['broker_id']]['truename'];
					}
					?>
					</div>
                </td>

            </tr>
            <?php
                }
                }else{
            ?>
            <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php } ?>
        </table>
    </div>
</div>
<div class="fun_btn clearfix" id="js_fun_btn">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
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
			var id_string = $("#customer_id_all_string").val();
			id = id_string.split(",");
		}else{
			return false;
		}
		var broker_id = $("#group_list_in").val();
		var broker_id_out = $("#group_list_out").val();
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

	//转移数据
	function move_data(move_type){
        openWin('js_pop_do_delete');
        $('#dialog_btn').attr('move_data_type', move_type);
	};


</script>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->

<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js,calculate.js"></script>

</body>
</html>
