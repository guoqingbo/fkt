<div class="pop_box_g pop_see_inform pop_no_q_up cancel_cancel_pop" id="js_pop_box_cooperation_refuse">
    <div class="hd">
        <div class="title">拒绝合作</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <div class="cancel_cancel_tex"> 确认要拒绝此次合作吗？</div>
                <div class="cancel_cancel_table">
                    <table>
                        <tbody>
                            <tr>
                                <th class="th">请选择拒绝理由：</th>
                                <td class="td">
                                    <select class="select" name="refuse_type" id="refuse_type" onchange="show_reason(this)">
                                        <option value="0">请选择理由</option>
                                        <?php foreach ($cooperate_info['config']['refuse_reason'] as $key => $value) { ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select> <span id="span_error" style="color: red; padding-left: 5px;display:none;">请选择理由</span>
                                </td>
                            </tr>
                            <tr style="display:none;" id="refuse_reason_remark">
                                <th class="th">&nbsp;</th>
                                <td class="td">
                                    <textarea class="textarea1" id = "refuse_reason" name = "refuse_reason" placeholder="说说拒绝原因吧，至少5个字"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button class="btn-lv1 btn-left" type="button" onclick="refuse_to_cooperation()">确定</button>
                <button class="btn-hui1" type="button">取消</button>
            </div>
        </div>
    </div>
</div>

<script>
//显示拒绝窗口
function show_refuse_window(obj)
{
    if(!$('#agreement').attr('checked'))
    {
        var msg = '需要勾选我已阅读并同意《合作协议》';
        $("#dialog_do_warnig_tip").html(msg);
        openWin('js_pop_do_warning');
        return false;
    }

    openWin(obj);
}


//拒绝合作
function refuse_to_cooperation()
{
    var c_id = $('#c_id').val();
    var step = $('#step').val();
    var old_esta = $('#old_esta').val();
    var broker_a_id = $('#broker_a_id').val();
    var refuse_type = $('#refuse_type').val();
    var refuse_reason = $('#refuse_reason').val();
    var secret_key = $('#secret_key').val();

    if (refuse_type == 0)
    {
        $("#span_error").show();
        return false;
    }
    else
    {
        $.ajax({
            url: "<?php echo MLS_URL;?>/cooperate/refuse_to_cooperation/",
            data: {'c_id': c_id,'broker_a_id':broker_a_id, 'step': step , 'old_esta':old_esta ,
                'refuse_type': refuse_type, 'refuse_reason': refuse_reason, 'secret_key': secret_key},
            type: "GET",
            dataType: 'JSON',
            success: function(data)
            {
                if (data['is_ok'] == 1)
                {
                    showParentDialog('dialog_do_itp' , 'js_pop_do_success' , data['msg']);
                }
                else if (data['is_ok'] == 0)
                {
                    showParentDialog('dialog_do_warnig_tip' , 'js_pop_do_warning' , data['msg']);
                    return false;
                }
            },
            error: function(er)
            {
                var error_msg = '异常错误';
                showParentDialog('dialog_do_warnig_tip' , 'js_pop_do_warning' , error_msg);
                return false;
            }
        });

        window.parent.closePopFun('js_pop_box_cooperation');
    }
}

//显示拒绝合作理由
function show_reason(obj)
{
    var select_val = $(obj).val();
    if (select_val == 4)
    {
        $('#refuse_reason_remark').show();
    }
    else
    {
        $('#refuse_reason_remark').hide();
    }
}
</script>
