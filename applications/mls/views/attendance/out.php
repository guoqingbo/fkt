<script>
    window.parent.addNavClass(11);
</script>

<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<body >
<div class="tab_box" id="js_tab_box">
<?php echo $user_menu;?>
</div>
<div id="js_search_box" class="shop_tab_title">
    <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
</div>
<div id="js_search_box" class="shop_tab_title  scr_clear">
       <a href="javascript:;" class="add_link" onClick="openWin('js_pop_add_attendance_kq')"><span class="iconfont">&#xe608;</span>添加外出登记</a>
</div>

<form action="" method="post" id="search_form">
<div class="search_box clearfix" id="js_search_box">
    <?php if($func_area==3){ ?>
    <div class="fg_box">
        <p class="fg fg_tex"> 分店：</p>
        <div class="fg">
            <select class="select" id="agency_id" name="agency_id">
                <option value="0">不限</option>
                <?php foreach($agencys as $k => $v){?>
                <option value="<?php echo $v['agency_id'];?>" <?php if($post_param['agency_id']==$v['agency_id']){echo "selected='selected'";}?>><?php echo $v['agency_name'];?></option>
                <?php }?>
            </select>
        </div>
    </div>
    <?php }?>
    <?php if(in_array($func_area,array(2,3))){ ?>
    <div class="fg_box">
        <p class="fg fg_tex"> 员工：</p>
        <div class="fg">
            <select class="select" id="broker_id" name="broker_id">
                <option value="0">不限</option>
                <?php if( isset($brokers) ){?>
                <?php foreach($brokers as $k => $v){?>
                    <option value="<?php echo $v['broker_id'];?>" <?php if($post_param['broker_id']==$v['broker_id']){echo "selected='selected'";}?>><?php echo $v['truename'];?></option>
                    <?php }?>
                <?php }?>
            </select>
        </div>
    </div>
    <?php }?>
    <div class="fg_box">
        <p class="fg fg_tex">外出时间：</p>
        <div class="fg fg-edit">
            <input type="text" name="start_time1" size="12" class="time_bg" readonly="readonly" value="<?php echo $post_param['start_time1'];?>" onclick="WdatePicker()"/>
            &nbsp;—&nbsp;
            <input type="text" name="end_time1" size="12" class="time_bg" readonly="readonly" value="<?php echo $post_param['end_time1'];?>" onclick="WdatePicker()"/>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">返回时间：</p>
        <div class="fg fg-edit">
            <input type="text" name="start_time2" size="12" class="time_bg" readonly="readonly" value="<?php echo $post_param['start_time2'];?>" onclick="WdatePicker()"/>
            &nbsp;—&nbsp;
            <input type="text" name="end_time2" size="12" class="time_bg" readonly="readonly" value="<?php echo $post_param['end_time2'];?>" onclick="WdatePicker()"/>
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset()">重置</a></div>
    </div>
</div>
<div class="table_all report-form-wrap">
    <div class="title" id="js_title">
        <table class="table">
            <tr>
                <td class="c10">序号</td>
                <td class="c10">外出员工部门</td>
                <td class="c10">外出员工</td>
                <td class="c10">外出时间</td>
                <td class="c10">返回时间</td>
                <td class="c10">备注</td>
                <td class="c10">操作</td>
            </tr>
        </table>
    </div>
    <div class="inner" id="js_inner" style="height: 389px !important;">
        <table class="table list-table">
            <?php
            if($list){
                foreach($list as $key=>$val){
            ?>
            <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
                <td class="c10"><?php echo $val['id'];?></td>
                <td class="c10"><?php echo $val['agency_name'];?></td>
                <td class="c10"><?php echo $val['broker_name'];?></td>
                <td class="c10"><?php echo $val['datetime1'];?></td>
                <td class="c10"><?php echo $val['datetime2'];?></td>
                <td class="c10"><?php echo $val['remarks'];?></td>
                <td class="c10">
                    <a href="javascript:;" onclick="checkdel(<?php echo $val['id'];?>);">删除</a>
                </td>
            </tr>
            <?php
                }
            }else{
            ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>
<div class="fun_btn clearfix" id="js_fun_btn" style="margin-top: 15px;">
    <div class="get_page">
        <?php echo $page_list; ?>
    </div>
</div>
</form>

<!--添加考勤 外出登记-->
<div class="pop_box_g" id="js_pop_add_attendance_kq">
    <div class="hd header">
        <div class="title">添加外出</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="warrant-step-kq">
        <form action="#" method="post">
            <table class="edit-table-kq">
                <tr>
                    <td width="70" class="label">考勤类型：</td>
                    <td class="kqbt">
                       外出
                    </td>
                </tr>
                <tr>
                        <td class="label">外出员工：</td>
                    <td colspan="2">
                        <select style="height:24px;" id="agency_id1" name="agency_id1">
                            <option value="">不限</option>
                            <?php foreach($agencys1 as $k => $v){?>
                            <option value="<?php echo $v['agency_id'];?>" <?php if($post_param['agency_id']==$v['agency_id']){echo "selected='selected'";}?>><?php echo $v['agency_name'];?></option>
                            <?php }?>
                        </select>&nbsp;&nbsp;
                        <select style="height:24px;" id="broker_id1" name="broker_id1">
                            <option value="">不限</option>
                            <?php if( isset($brokers1) ){?>
                            <?php foreach($brokers1 as $k => $v){?>
                            <option value="<?php echo $v['broker_id'];?>" <?php if($post_param['broker_id']==$v['broker_id']){echo "selected='selected'";}?>><?php echo $v['truename'];?></option>
                            <?php }?>
                            <?php }?>
                        </select>

                    </td>
                </tr>
                <tr>
                    <td class="label" id="add_time_type">外出时间：</td>
                    <td colspan="5"><input type="text" size="14" name="datetime1" id="datetime1" class="time_bg" readonly="readonly" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})"></td>
                </tr>
                <tr>
                    <td class="label align-top">外出备注：</td>
                    <td colspan="5"><textarea name="remarks" id="remarks" class="att-remark"></textarea></td>
                </tr>
                <tr class="bcBtn">
                	<td colspan="6"><input type="button" class="btn-lv1 btn-mid" value="保存"  onclick="add_out();"/></td>
                </tr>
            </table>
        </form>
    </div>
</div>




<div id="js_pop_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">&nbsp;&nbsp;外出登记已删除！</p>
            </div>
        </div>
    </div>
</div>

<!--操作结果弹出警告-->
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_warnig_tip'><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;<span></span></p>
            </div>
        </div>
    </div>
</div>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">&nbsp;&nbsp;<span></span></p>
            </div>
        </div>
    </div>
</div>
<!--询问操作确定弹窗-->
<div id="js_pop_tip" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" id="dialogSaveDiv"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;<span></span></p>
                    <button type="button" id = 'dialog_share' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
    </div>
</div>
</body>
<script>
$(function(){
    $('#agency_id').change(function(){
        var agencyId = $(this).val();
        $.ajax({
            type: 'get',
            url : '/my_task/get_broker_ajax/'+agencyId,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg===''){
                    str = '<option value="0">不限</option>';
                }else{
                    str = '<option value="0">不限</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].broker_id+'">'+msg[i].truename+'</option>';
                    }
                }
                $('#broker_id').html(str);
            }
        });
    });
    $('#agency_id1').change(function(){
        var agencyId = $(this).val();
        $.ajax({
            type: 'get',
            url : '/my_task/get_broker_ajax/'+agencyId,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg===''){
                    str = '<option value="">不限</option>';
                }else{
                    str = '<option value="">不限</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].broker_id+'">'+msg[i].truename+'</option>';
                    }
                }
                $('#broker_id1').html(str);
            }
        });
    });
});
//添加添加外出
function add_out()
{
	var act = 1;
	var agency_id = $("#agency_id1").val();
        var broker_id = $("#broker_id1").val();
	if((!agency_id || !broker_id) && act == 1){
		$("#dialog_do_warnig_tip span").html("请选择外出员工");
		openWin('js_pop_do_warning');
		act = 0;
	}
	var datetime1 = $.trim($("#datetime1").val());
	if((!datetime1) && act == 1){
		$("#dialog_do_warnig_tip span").html("请输入外出时间");
		openWin('js_pop_do_warning');
		act = 0;
	}
        var remarks = $.trim($("#remarks").val());
	if((!remarks) && act == 1){
		$("#dialog_do_warnig_tip span").html("请输入外出备注");
		openWin('js_pop_do_warning');
		act = 0;
	}
	if(act == 1){
            $.ajax({
                url: "<?php echo MLS_URL;?>/attendance/add_out/",
                type: "POST",
                dataType: "json",
                data: {
                    agency_id: agency_id,
                    broker_id: broker_id,
                    datetime1: datetime1,
                    remarks:remarks
                },
                success: function(data) {
                    if(data['errorCode'] == '401')
                    {
                        login_out();
                        return false;
                    }
                    else if(data['errorCode'] == '403')
                    {
                        permission_none();
                        return false;
                    }

                    if(data['result'] == 'ok')
                    {
                        $("#js_pop_tip").remove();
                        $("#dialog_do_itp span").html("添加成功");
                        openWin('js_pop_do_success');
                        $(".JS_Close").click(function(){
                            $('#search_form').submit();
                        });
                    }else{
                        $("#js_pop_tip").remove();
                        $("#dialog_do_warnig_tip span").html("添加失败");
                        openWin('js_pop_do_warning');
                    }
                }
            });
	}
}
//确认是否删除
function checkdel(id){
    $("#dialogSaveDiv span").html("您确定要删除此外出登记吗？<br/>删除后不可恢复。");
	//打开询问操作确定弹窗
    openWin('js_pop_tip');
    $("#dialog_share").click(function(){
		$.ajax({
			url: "<?php echo MLS_URL;?>/attendance/del/",
			type: "GET",
			dataType: "json",
			data: {
				str: id,
				isajax:1
			},
			success: function(data) {
                if(data['errorCode'] == '401')
                {
                    login_out();
                    $("#js_pop_tip").hide();
                }
                else if(data['errorCode'] == '403')
                {
                    permission_none();
                    $("#js_pop_tip").hide();
                }else{
                    if(data['result'] == 'ok')
                    {
                        $("#dialog_do_itp span").html("外出登记已删除！");
                        openWin('js_pop_do_success');
                        $("#tr"+id).remove();
                    }else{
                        $("#dialog_do_warnig_tip span").html("外出登记删除失败！");
                         openWin('js_pop_do_warning');
                    }
                }
			}
		});
	});

}
$('.JS_Close').bind('click', function() {

	$('#dialog_share').unbind('click');
});
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>
