<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<!--修改考勤-->
<div class="pop_box_g" id="js_pop_update_attendance" style="display: block;">
    <div class="hd header">
        <div class="title">修改考勤</div>
    </div>
    <div class="warrant-step">
        <form id="attendance_form" name="attendance_form" action="" method="post">
            <input type='hidden' name='submit_flag' value='update'/>
            <table class="table edit-table">
                <tr>
                    <td class="label">考勤类型：</td>
                    <td colspan="2">
                        <?php echo $config['type'][$info['type']];?>
                    </td>
                    <td class="label">考勤员工：</td>
                    <td colspan="2">
                        <?php echo $info['agency_name'];?>&nbsp;&nbsp;<?php echo $info['broker_name'];?>
                    </td>
                </tr>
                <tr>
                    <td class="label" id="update_time_type"><?php echo substr($config['type'][$info['type']],0,6);?>时间：</td>
                    <td colspan="2"><input type="text" size="22" class="time_bg" name="datetime1" id="datetime1" readonly="readonly" value="<?php echo $info['datetime1'];?>"  onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})"></td>
                    <td class="label back_time">
                        <?php if( in_array($info['type'],array(3,4))){ ?>
                        返回时间：
                        <?php }?>
                    </td>
                    <td colspan="2" class="back_time">
                        <?php if( in_array($info['type'],array(3,4))){ ?>
                        <input type="text" size="22" class="time_bg" name="datetime2" id="datetime2" readonly="readonly" value="<?php echo $info['datetime2'];?>"  onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                        <?php }?>
                    </td>
                </tr>
                <tr>
                    <td class="label align-top">备注：</td>
                    <td colspan="5"><textarea name="remarks" id="remarks" class="att-remark"><?php echo $info['remarks'];?></textarea></td>
                </tr>
                <tr>
                    <td class="label">考勤说明：</td>
                    <td colspan="5"><input type="text" name="explain" id="explain" class="att-desc" value="<?php echo $info['explain'];?>"></td>
                </tr>
                <tr>
                    <td colspan="6"><span class="error"></span></td>
                </tr>
                <tr class="btn-line">
                    <td colspan="6">
                        <input type="button" class="my_btn" value="保存" onclick="update_attendance();"/>
                        <input type="button" class="my_btn" value="删除" onclick="checkdel(<?php echo $info['id'];?>);">
                        <input type="button" class="cancel_btn" value="取消" onclick="close_iframe();">
                    </td>
                </tr>
            </table>
        </form>
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
                 <p class="text" id='dialog_do_warnig_tip'><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"> <span></span></p>
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
                 <p class="text" id='dialog_do_itp'><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png"> <span></span></p>
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
                    <p class="text" id="dialogSaveDiv"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"> <span></span></p>
                    <button type="button" id = 'dialog_share' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
    </div>
</div>

<script type="text/javascript">
function close_iframe(){
    $(window.parent.document).find('#js_pop_update_attendance').hide();
    $(window.parent.document).find('#' + 'GTipsCover' + 'js_pop_update_attendance').remove();
}
//确认是否删除
function checkdel(id){
    $("#dialogSaveDiv span").html("您确定要删除此考勤吗？<br/>删除后不可恢复。");
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
                    $(window.parent.document).login_out();
                    $("#js_pop_tip").hide();
                }
                else if(data['errorCode'] == '403')
                {
                    $(window.parent.document).permission_none();
                    $("#js_pop_tip").hide();
                }else{
                    if(data['result'] == 'ok')
                    {
                        $("#dialog_do_itp span").html("考勤已删除！");
                        openWin('js_pop_do_success');
                        $(window.parent.document).find('#search_form').submit();
                    }else{
                        $("#dialog_do_warnig_tip span").html("考勤删除失败！");
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
//修改考勤
function update_attendance(){
    var act = 1;
    var type = <?php echo $info['type'];?>;
    var att_type = '<?php echo $config['type'][$info['type']];?>';
    var datetime1 = $("#datetime1").val();
    if(!datetime1 && act == 1){
        $("#dialog_do_warnig_tip span").html("请填写"+att_type+"时间！");
        openWin('js_pop_do_warning');
        act = 0;
    }
    var datetime2 = $("#datetime2").val();
    if(type == 3){
        if(!datetime2 && act == 1){
            $("#dialog_do_warnig_tip span").html("请填写返回时间！");
            openWin('js_pop_do_warning');
            act = 0;
        }
    }
    if(type == 3 || type == 4){
        if(datetime2 <= datetime1 && act == 1){
            $("#dialog_do_warnig_tip span").html("返回时间大于"+att_type+"时间！");
            openWin('js_pop_do_warning');
            act = 0;
        }
    }
    if(act == 1){
        var remarks = $.trim($("#remarks").val());
        var explain = $.trim($("#explain").val());
        var data_arr = {type:type,datetime1:datetime1,datetime2:datetime2,remarks:remarks,explain:explain};
        $.ajax({
            url: "<?php echo MLS_URL;?>/attendance/update_attendance_ajax/"+<?php echo $info['id'];?>,
            type: "POST",
            dataType: "json",
            data:data_arr,
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

                var msg = data['msg'];
                if(data['result'] == 'ok')
                {
                    $("#dialog_do_itp span").html("修改考勤成功");
                    openWin('js_pop_do_success');
                    $(".JS_Close").click(function(){
                        $(window.parent.document).find('#search_form').submit();
                    });
                }else{
                    $("#dialog_do_warnig_tip span").html(msg);
                    openWin('js_pop_do_warning');
                }
            }
        });
    }

}
</script>
