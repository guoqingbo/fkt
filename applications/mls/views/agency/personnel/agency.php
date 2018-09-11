<script>
    window.parent.addNavClass(11);
</script>
<script type="text/javascript">
$(function(){
	$("#modify").live("click",function(){
	   var rel= $(this).attr("rel");
	   var name = $("#"+rel+"_name").text();
	   var telno = $("#"+rel+"_telno").text();
	   var address = $("#"+rel+"_address").text();
	   $("#agency_id").val(rel);
	   $("#modify_name").val(name);
	   $("#modify_telno").val(telno);
	   $("#modify_address").val(address);
	});
});
</script>


<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>

<div id="js_search_box" class="shop_tab_title">
    <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
    <a href="javascript:void(0)" onClick="openWin('js_add_shop')" class="btn-lan right"><span>添加门店</span></a>
</div>

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <td class="c5"><div class="info"><input type="checkbox" id="js_checkbox"></div></td>
                <td class="c15"><div class="info">分店名称</div></td>
                <td class="c15"><div class="info">电话</div></td>
                <td class="c15"><div class="info">地址</div></td>
                <td class="c15"><div class="info">类型</div></td>
                <td class="c15"><div class="info">状态</div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
            <?php
            if(is_array($agency) && !empty($agency)){
                foreach ($agency as $key =>$value) {
            ?>
            <tr>
                <td class="c5">
                    <div class="info">
                        <?php
                        if($value['status'] == 1){
                            echo '<input type="checkbox" class="checkbox" name="agencyId" value="'.$value['id'].'" >';
                        }
                        ?>
                    </div>
                </td>
                <td class="c15"><div class="info" id="<?=$value['id']?>_name"><?=$value['name'] ?></div></td>
                <td width="14.5%"><div class="info" id="<?=$value['id']?>_telno"><?=$value['telno'] ?></div></td>
                <td width="15.7%"><div class="info" id="<?=$value['id']?>_address"><?=$value['address'] ?></div></td>
                <td width="15.5%">
                    <div class="info">
                    <?php
                    if($value['company_id'] == 0)
                    {
                        echo '总店';
                    }else{
                        echo '分店';
                    }
                    ?>
                    </div>
                </td>
                <td width="15%">
                    <div class="info">
                    <?php
                    if($value['status'] == 0)
                    {
                        echo '审核中';
                    }elseif($value['status'] == 1){
                        echo '<span class="s">成功</span>';
                    }else{
                        echo '失效';
                    }
                    ?>
                    </div>
                </td>
                 <td>
                    <div class="info">
                    <?php
                    if($value['status'] == 1)
                    {
                        echo '<a id="modify" rel="'.$value['id'].'" href="javascript:void(0)" onClick=openWin("js_r_shop") class="fun_link">修改</a>|<a href="javascript:void(0)" onclick="delete_agency('.$value['id'].')" class="fun_link">删除</a>';
                    }else{
                        echo '<a class="fun_link" style="color:#ccc">修改</a>|<a class="fun_link" style="color:#ccc">删除</a>';
                    }
                    ?>
                    </div>
                </td>
            </tr>
           <?php
                }
            }else{
           ?>
            <tr><td colspan="7" style="height: 340px;">抱歉，暂无店面部门信息，请添加</td></tr>
            <?php }?>
        </table>
    </div>
</div>
<div class="fun_btn clearfix" id="js_fun_btn">
    <!--<label class="btn btn_del"><input type="checkbox" id="js_checkbox">全选</label>-->
    <form action="" name="search_form" method="post" id="subform">
        <div class="get_page">
			<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
        </div>
    </form>
    <a href="javascript:void(0);" class="grey_btn" onclick="delete_all()">删除</a>
</div>
<div class="pop_box_g pop_box_add_shop" id="js_add_shop">
    <div class="hd">
        <div class="title">添加门店</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
        <label class="label clearfix"><span class="text">门店名称：</span><input class="text_input" id="add_name" placeholder="店面或部门的名称，如：三牌楼店" type="text"></label>
        <label class="label clearfix"><span class="text">门店电话：</span><input class="text_input" id="add_telno" placeholder="店面的电话，如：02589898989" type="text"></label>
        <label class="label clearfix"><span class="text">门店地址：</span><input class="text_input" id="add_address" placeholder="店面的地址,如：奥体大街100号" type="text"></label>
        <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="add_agency()">保存</button>
    </div>
</div>

<div class="pop_box_g pop_box_add_shop" id="js_r_shop">
    <div class="hd">
        <div class="title">修改门店信息</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
        <label class="label clearfix"><span class="text">门店名称：</span><input class="text_input" id="modify_name" value="" type="text"></label>
        <label class="label clearfix"><span class="text">门店电话：</span><input class="text_input" id="modify_telno" value="" type="text"></label>
        <label class="label clearfix"><span class="text">门店地址：</span><input class="text_input" id="modify_address" value="" type="text"></label>
        <input type="hidden" value="" id="agency_id">
        <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="modify_agency()">保存</button>
    </div>
</div>

<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">操作成功！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="location.href='/agency/'">确定</button>
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
				<p class="text" id="dialog_do_delete_tip">确定要删除选定的记录吗？</p>
				<button type="button" id="dialog_btn" class="btn-lv1 btn-left">确定</button>
				<button type="button" class="btn-hui1 JS_Close">取消</button>
			</div>
		</div>
	</div>
</div>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/loading.gif" id="mainloading">
<!--遮罩 loading-->

<script type="text/javascript">
    function add_agency(){
    	var name = $("#add_name").val();
        var telno = $("#add_telno").val();
        var address = $("#add_address").val();
        if(!name){alert("请输入门店名称");return false;}
        if(!telno){alert("请输入门店电话");return false;}
        if(!address){alert("请输入门店地址");return false;}
        var data = {name:name,telno:telno,address:address};
    	$.ajax({
    		type: "POST",
    		url: "/agency/add",
    		dataType:"json",
    		data:data,
    		cache:false,
    		error:function(){
    			$("#dialog_do_warnig_tip").html("系统错误");
        		openWin('js_pop_do_warning');
    			return false;
    		},
    		success: function(data){
    			if(data['errorCode'] == '401')
                {
                    login_out();
                    $("#jss_pop_tip").hide();
                }
                else if(data['errorCode'] == '403')
                {
                    /*permission_none();
                    $("#jss_pop_tip").hide();*/
                	closeWindowWin('js_add_shop');
                	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
                    openWin('js_pop_do_warning');return false;
                }else{
                	if(data.status=="success"){
            			$("#dialog_do_success_tip").html(data.msg);
                		openWin('js_pop_do_success');
            		}else{
            			$("#dialog_do_warnig_tip").html(data.msg);
                		openWin('js_pop_do_warning');
            		}
                }
    		}
    	});

    }

    function modify_agency(){
    	var agency_id = $("#agency_id").val();
    	var name = $("#modify_name").val();
        var telno = $("#modify_telno").val();
        var address = $("#modify_address").val();
        if(!name){alert("请输入门店名称");return false;}
        if(!telno){alert("请输入门店电话");return false;}
        if(!address){alert("请输入门店地址");return false;}
        var data = {agency_id:agency_id,name:name,telno:telno,address:address};
        $.ajax({
        	type: "POST",
        	url: "/agency/modify",
        	dataType:"json",
        	data:data,
        	cache:false,
        	error:function(){
        		$("#dialog_do_warnig_tip").html("系统错误");
        		openWin('js_pop_do_warning');
        		return false;
        	},
        	success: function(data){
        		if(data['errorCode'] == '401')
                {
                    login_out();
                    $("#jss_pop_tip").hide();
                }
                else if(data['errorCode'] == '403')
                {
                    /*permission_none();
                    $("#jss_pop_tip").hide();*/
                	closeWindowWin('js_r_shop');
                	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
                    openWin('js_pop_do_warning');
                }else{
                	if(data.status=="success"){
            			$("#dialog_do_success_tip").html(data.msg);
                		openWin('js_pop_do_success');
            		}else{
            			$("#dialog_do_warnig_tip").html(data.msg);
                		openWin('js_pop_do_warning');
            		}
                }
        	}
        });

    }

    function delete_agency(agency_id){
    	openWin('js_pop_do_delete');
    	$('#dialog_btn').bind('click', function() {
            var data = {agency_id:agency_id};
            $.ajax({
            	type: "POST",
            	url: "/agency/delete",
            	dataType:"json",
            	data:data,
            	cache:false,
            	error:function(){
            		$("#dialog_do_warnig_tip").html("系统错误");
            		openWin('js_pop_do_warning');
            		return false;
            	},
            	success: function(data){
            		$("#js_pop_do_delete").remove();
            		if(data['errorCode'] == '401')
                    {
                        login_out();
                        $("#jss_pop_tip").hide();
                    }
                    else if(data['errorCode'] == '403')
                    {
                        /*permission_none();
                        $("#jss_pop_tip").hide();*/
                    	closeWindowWin('js_pop_do_delete');
                    	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
                        openWin('js_pop_do_warning');
                    }else{
                		if(data.status=="success"){
                			$("#dialog_do_success_tip").html(data.msg);
                    		openWin('js_pop_do_success');
                		}else{
                			$("#dialog_do_warnig_tip").html(data.msg);
                    		openWin('js_pop_do_warning');
                		}
                    }
            	}
            });
    	});
    }

    function delete_all(){
    	var agency_id= [];
        $("input[name=agencyId]").each(function() {
            if ($(this).attr("checked")) {
            	agency_id.push($(this).val());
            }
        });
        if(agency_id.length==0){
        	$("#dialog_do_warnig_tip").html("未勾选，请选择！");
    		openWin('js_pop_do_warning');
        }else{
        	delete_agency(agency_id);
        }
     }

    function submit()
	{
       $("#subform").submit() ;
    }
</script>
