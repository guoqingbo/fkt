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

	</form>
</div>
<!-- 上部菜单选项，按钮---end-->
<div class="table_all">

	<div style="height: 226px;" class="inner shop_inner" id="js_inner">
        <table class="table">
            <tbody>
            <tr><td><span class="no-data-tip">请根据需要选择信息</span></td></tr>
          </tbody>
        </table>
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
				<button type="button" id="dialog_btn" class="btn-lv1 btn-left">确定</button>
				<button type="button" class="btn-hui1 JS_Close">取消</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	//转移数据
	function move_data(move_type){
		var id= [];
		var statu = confirm("你确定要转移吗?");
        if(!statu){
            return false;
        }
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
		var type = $("#type").val();
		//alert(id);
		//alert(broker_id);
		//alert(type);
		if(id.length == 0){
        	$("#dialog_do_warnig_tip").html("请勾选要转移的房客源！");
    		openWin('js_pop_do_warning');
			return false;
        }
		if(broker_id_out == 0){
			$("#dialog_do_warnig_tip").html("请选择转出的人！");
    		openWin('js_pop_do_warning');
			return false;
		}
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
				type:type
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


</script>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->

<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js,calculate.js"></script>

</body>
</html>
