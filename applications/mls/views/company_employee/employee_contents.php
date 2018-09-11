<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<body >
<div class="tab_box" id="js_tab_box">
    <?php
    echo $user_menu;
    ?>
</div>
<form method='post' action='' id='search_form' name='search_form'>
    <div class="search_box clearfix" id="js_search_box">
        <div class="fg_box">
            <p class="fg fg_tex">分店：</p>
            <div class="fg">
                <select name="store_name" class="input w150" <?php echo $company_id==0?"disabled":""?>>
                    <?php if($view_other_per){ ?>
                        <option value="no" id="no" selected>不限</option>
                        <?php if($agency){
                        foreach($agency as $key=>$val) {?>
                        <option value="<?php echo $val['agency_name'];?>" <?php if($store_name == $val['agency_name']){echo selected;}?>><?php echo $val['agency_name'];?></option>
                        <?php }}?>
                    <?php }else{ ?>
                        <option value="<?php echo $agency_name?>"><?php echo $agency_name?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">员工：</p>
            <div class="fg">
                <input type="text" class="input w110" name="e_name" id="e_name"  value="<?php echo $e_name; ?>">
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">手机号：</p>
            <div class="fg">
                <input type="text" class="input w110" name="tel" id="tel" value="<?php echo $tel; ?>">
            </div>
        </div>
        <div class="fg_box">
            <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
            <div class="fg"> <a href="javascript:void(0)" class="reset" onclick="javascript:location.href ='/company_employee/index';return false;">重置</a> </div>
        </div>
        <div id="js_fun_btn" class="fun_btn fun_btn_bottom clearfix" id="js_search_box" style="display:none">
            <div class="get_page">
                <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
            </div>
        </div>
    </div>
<script>
$(function(){
	document.onkeydown = function(e){ //enter
		var ev = document.all ? window.event : e;
		if(ev.keyCode==13) {
			$('#search_form').submit();return false;
		 }
	}
});
</script>
</form>
<div class="table_all report-form-wrap">
    <div class="title" id="js_title">
        <table class="table">
            <tr>
                <td class="c10">分店名称</td>
                <td class="c10">员工名称</td>
                <td class="c10">电话</td>
                <td class="c10">权限</td>
                <td class="c10">角色</td>
                <td class="c15">备注</td>
            </tr>
        </table>
    </div>
    <div class="inner" id="js_inner" style="height: 389px !important;">
        <table class="table list-table">
            <?php if($list){
                    foreach($list as $key=>$val) {?>
            <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
                            <td class="c10"><?php echo $val['store_name']?$val['store_name']:$storename;?></td>
                            <td class="c10"><?php echo $val['truename'];?></td>
                            <td class="c10"><?php echo $val['phone'];?></td>
                            <td class="c10"><?php switch($val['package_id']){case 1:echo "总店长";break;case 2:echo "经纪人";break;}?></td>
                            <td class="c10"><?php echo $val['sname'];?></td>
                            <td class="c15">
                                <div class="info clearfix">
                                    <div class="width_left fl" id="remark<?php echo $val['id'];?>"><?php echo $val['remark'];?></div>
                                    <div class="width_left fr" >
                                        <a href="javascript:void(0);" class="blue_pen_new fl" onclick="open_remark(<?php echo $val['id'];?>);"></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php }
            } else { ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php } ?>
        </table>
    </div>

</div>

<div id="js_fun_btn" class="fun_btn fun_btn_bottom clearfix" id="js_search_box">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>

<form action="" >
<div style="width:325px; height:274px; display:none;" id="js_remark" class="pop_box_g">
    <input type="hidden" id="val_id">
    <input type="hidden" name="id" id="list_id">
    <input type="hidden" name="remarker_id" id="list_remarker_id">
    <input type="hidden" id="old_remark">
    <div class="hd">
        <div class="title">员工通讯录编辑</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
        <div class="create_newb_wrapall paddiing_down">

            <div class="create_newb_wrap create_newblack clearfix" >
                <div class="name fl">分店名称：</div>
                <span class="name_right fl" id="list_storename"></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix" >
                <div class="name fl">员工：</div>
                <span class="name_right fl" id="list_truename"></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix" >
                <div class="name fl">手机号：</div>
                <span class="name_right fl" id="list_phone"></span>
            </div>
            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">权限：</div>
                <span class="name_right fl" id="list_p_name"></span>
            </div>
            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">备注：</div>
                <input type="text" class="loupan fl" id="list_remark" name="remark" onkeyup="limit_num()"><span class="name_right_remind2" id="limited"></span>
            </div>
        </div>

        <div style="width:120px; margin:10px auto 0; height:auto; overflow:hidden; zoom:1;">
            <button class="btn-lv1 btn-left JS_Close" style="float:left;" type="button" onclick="update_remark();">确定</button>
            <button class="btn-hui1 JS_Close" type="button">取消</button>
        </div>
    </div>
</div>
    <!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="sub_form();" title="关闭" class="JS_Close iconfont"></a>
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
</form>
</body>
<script>
function open_remark(id){
    //异步获取当前备注信息
    $.ajax({
        url:"<?php echo MLS_URL;?>/company_employee/get_remark/"+id,
        type:"GET",
        dataType:"json",
        data:{
            isajax:1
        },
        success:function(data){
            if(data['errorCode'] == '401')
            {
                login_out();
            }
            else if(data['errorCode'] == '403')
            {
                permission_none();
            }else{
                if(data['result'] == 'ok')
                {
                    var list=data['list'];
                    var html = "";
                    for(var i in list)
                    {
                       $("#val_id").val(list[i]['id']);
                       if(list[i]['store_name']==""){
                           list[i]['store_name'] ="暂无";
                       }
                       $("#list_storename").text(list[i]['store_name']);
                       $("#list_truename").text(list[i]['truename']);
                       $("#list_phone").text(list[i]['phone']);
                       //$("#list_qq").text(list[i]['qq']);
                       //$("#list_agency").text(list[i]['store_name']);
                       $("#list_p_name").text(list[i]['package_id']);
                       $("#list_remark").val(list[i]['remark']);
                       $("#old_remark").val(list[i]['remark']);
                       $("#list_id").val(list[i]['rid']);
                       $("#list_remarker_id").val(list[i]['broker_id']);
                       if(!list[i]['remark'] == ""){
                           var num = list[i]['remark'].length;
                           $("#limited").text(num+"/10");
                       }else{
                           $("#limited").text("0/10");
                       }

                    }
                    openWin('js_remark');
                }
            }
        }
    });
}
//提交修改的备注
function update_remark()
{
    var old_remark =  $("#old_remark").val();
    var val_id = "remark"+$("#val_id").val();
    var id = $("#list_id").val();
    var remarker_id = $("#list_remarker_id").val();
    var remark = $("#list_remark").val();
    var data = {
        'id':id,
        'remarker_id':remarker_id,
        'remark':remark
    };
    $.ajax({
        type:"POST",
        url:"/company_employee/update_remark",
        data:data,
        cache:false,
        error:function(){
            alert("系统错误");
            return false;
        },
        success:function(return_data){
            if(old_remark == remark){
                $('#dialog_do_itp').html('您没有做任何修改！');
                openWin('js_pop_do_success');
            }else if(1==return_data){
                $('#dialog_do_itp').html('修改成功');
                $("#"+val_id).html(remark);
                openWin('js_pop_do_success');
            }else{
                $('#dialog_do_itp').html('修改失败');
                openWin('js_pop_do_success');
            }
        }
    })
}

//弹出框备注信息限制字数
function limit_num(){
    var maxnum = 10;  //最大字数
    var input = $("#list_remark").val().length;  //获取当前字数
    $("#limited").text(input+"/"+maxnum);
    if(input > maxnum){    //如果字数超过，将无法输入
         $("#list_remark").val( $("#list_remark").val().substr(0, maxnum));
         $("#limited").text(maxnum+"/"+maxnum);
    }
}

</script>
