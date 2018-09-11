<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<style>
input.error,select.error,textarea.error {
    border: 1px solid #e92e2e;
}
</style>
<!--添加考勤-->
<div class="pop_box_g" id="js_pop_add_attendance" style="display: block;">
    <div class="hd header">
        <div class="title">添加考勤</div>
    </div>
    <div class="warrant-step">
        <form id="attendance_form" name="attendance_form" action="" method="post">
            <input type='hidden' name='submit_flag' value='add'/>
            <table class="table edit-table">
                <tr>
                    <td class="label">考勤类型：</td>
                    <td colspan="2">
                        <select name="type" id="type">
                            <option value="1" data-name="上班">上班</option>
                            <option value="2" data-name="下班">下班</option>
                            <option value="3" data-name="请假">请假</option>
                            <option value="4" data-name="外出">外出</option>
                        </select>
                    </td>
                    <td class="label">考勤员工：</td>
                    <td colspan="2">
                        <?php if($func_area==3){ ?>
                            <select class="select" id="agency_id" name="agency_id">
                                <option value="0">不限</option>
                                <?php foreach($agencys as $k => $v){?>
                                <option value="<?php echo $v['agency_id'];?>"><?php echo $v['agency_name'];?></option>
                                <?php }?>
                            </select>&nbsp;&nbsp;
                        <?php }?>
                        <?php if(in_array($func_area,array(2,3))){ ?>
                            <select class="select" id="broker_id" name="broker_id">
                                <option value="0">不限</option>
                                <?php if( isset($brokers) ){?>
                                <?php foreach($brokers as $k => $v){?>
                                    <option value="<?php echo $v['broker_id'];?>"><?php echo $v['truename'];?></option>
                                    <?php }?>
                                <?php }?>
                            </select>
                        <?php }?>
                        <?php if($func_area==2){ ?>
                        <input type="hidden" id="agency_id" name="agency_id" value="<?php echo $user_arr['agency_id'];?>">
                        <?php }?>
                        <?php if($func_area==1){ ?>
                        <input type="hidden" id="broker_id" name="broker_id" value="<?php echo $user_arr['broker_id'];?>">
                        <?php echo $user_arr['truename'];?>
                        <?php }?>
                    </td>
                </tr>
                <tr>
                    <td class="label" id="add_time_type">上班时间：</td>
                    <td colspan="2"><input type="text" size="22" class="time_bg" name="datetime1" id="datetime1" readonly="readonly" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})"></td>
                    <td class="label back_time hide">
                        返回时间：
                    </td>
                    <td colspan="2" class="back_time hide">
                        <input type="text" size="22" class="time_bg" name="datetime2" id="datetime2" readonly="readonly"  onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                    </td>
                </tr>
                <tr>
                    <td class="label align-top">备注：</td>
                    <td colspan="5"><textarea name="remarks" id="remarks" class="att-remark"></textarea></td>
                </tr>
                <tr>
                    <td colspan="6"><span class="error"></span></td>
                </tr>
                <tr class="btn-line">
                    <td colspan="6"><input type="button" class="btn-lv1 btn-mid JS_Close" value="保存" onclick="add_attendance();"/></td>
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

<script type="text/javascript">
$(function() {
    $("#type").change(function() {
        var val = $(this).val();
        var att_type = $(this).find("option:selected").attr("data-name");
        $("#add_time_type").text(att_type + '时间：');
        if(val == 3){
            $(".back_time").show();
        }else{
            $(".back_time").hide();
        }
    });
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
});
//添加考勤
function add_attendance(){
    var act = 1;
    var type = $("#type").val();
    if(type <= 0 && act == 1){
        $("#dialog_do_warnig_tip span").html("请选择考勤类型！");
        openWin('js_pop_do_warning');
        act = 0;
    }
    var agency_id = $("#agency_id").val();
    if(agency_id <= 0 && act == 1){
        $("#dialog_do_warnig_tip span").html("请选择考勤门店！");
        openWin('js_pop_do_warning');
        act = 0;
    }
    var broker_id = $("#broker_id").val();
    if(broker_id <= 0 && act == 1){
        $("#dialog_do_warnig_tip span").html("请选择考勤员工！");
        openWin('js_pop_do_warning');
        act = 0;
    }
    var att_type = $("#type").find("option:selected").attr("data-name");
    var datetime1 = $("#datetime1").val();
    if(!datetime1 && act == 1){
        $("#dialog_do_warnig_tip span").html("请填写"+att_type+"时间！");
        openWin('js_pop_do_warning');
        act = 0;
    }
    if(type == 3){
        var datetime2 = $("#datetime2").val();
        if(!datetime2 && act == 1){
            $("#dialog_do_warnig_tip span").html("请填写返回时间！");
            openWin('js_pop_do_warning');
            act = 0;
        }
        if(datetime2 <= datetime1 && act == 1){
            $("#dialog_do_warnig_tip span").html("返回时间大于"+att_type+"时间！");
            openWin('js_pop_do_warning');
            act = 0;
        }
    }
    if(act == 1){
        var remarks = $.trim($("#remarks").val());
        var data_arr = {type:type,agency_id:agency_id,broker_id:broker_id,datetime1:datetime1,datetime2:datetime2,remarks:remarks};
        $.ajax({
            url: "<?php echo MLS_URL;?>/attendance/add_attendance_ajax1/",
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
                    $("#dialog_do_itp span").html("添加考勤成功");
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
