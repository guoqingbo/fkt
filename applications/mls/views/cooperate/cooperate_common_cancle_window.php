<div class="pop_box_g pop_see_inform pop_no_q_up cancel_cancel_pop" id="js_pop_box_cooperation_cancle">
    <div class="hd">
        <div class="title">取消合作</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <div class="cancel_cancel_tex"> 确认要取消此次合作吗？<?php if($cooperate_info['step'] >= 3){ ?>由于合作已生效，如现在取消，将会扣减您的信用分。<?php } ?></div>
                <div class="cancel_cancel_table">
                    <table>
                        <tbody>
                            <tr>
                                <th class="th">请选择取消理由：</th>
                                <td class="td">
                                    <select class="select" name="cancle_type" id="cancle_type" onchange="show_reason(this)">
                                        <option value="0">请选择理由</option>
                                        <?php foreach ($cooperate_info['config']['cancel_reason'] as $key => $value) { ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select> <span id="span_error" style="color: red; padding-left: 5px;display:none;">请选择理由</span>
                                </td>
                            </tr>
                            <tr style="display:none;" id="cancle_reason_remark">
                                <th class="th">&nbsp;</th>
                                <td class="td">
                                    <textarea class="textarea1" id = "cancle_reason" name = "cancle_reason"  placeholder="说说取消原因吧，至少5个字"></textarea>
                                    <span id="cancle_reason_span_error" style="color: red; padding-left: 5px;display:none;">说说取消原因吧，至少5个字</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button class="btn-lv1 btn-left" type="button" onclick="cooperate_cancle()">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
//弹框
function show_cancle_window(obj_id)
{
    if(!$('#agreement').attr('checked'))
    {
        var msg = '需要勾选我已阅读并同意《合作协议》';
        $("#dialog_do_warnig_tip").html(msg);
        openWin('js_pop_do_warning');
        return false;
    }

    openWin(obj_id);
}

//取消合作
function cooperate_cancle()
{
    var c_id = $('#c_id').val();
    var step = $('#step').val();
    var old_esta = $('#old_esta').val();
    var cancle_type = $('#cancle_type').val();
    var cancle_reason = $.trim($('#cancle_reason').val());
    var secret_key = $('#secret_key').val();
    var cancle_type = $('#cancle_type').val();

    if (cancle_type == 0)
    {
        $("#span_error").show();
        return false;
    }
    else if(cancle_type == 4 && cancle_reason.length < 5)
    {
        $("#cancle_reason_span_error").show();
        return false;
    }
    else
    {
        $.ajax({
            url: "<?php echo MLS_URL;?>/cooperate/cancle_cooperation/",
            data: {'c_id': c_id, 'step': step, 'old_esta':old_esta, 'cancle_type': cancle_type, 'cancle_reason': cancle_reason, 'secret_key': secret_key},
            type: "GET",
            dataType: 'JSON',
            success: function(data)
            {
                if (data['is_ok'] == 1)
                {
                    showParentDialog('dialog_do_itp' ,'js_pop_do_success',data['msg']);
                }
                else if (data['is_ok'] == 0)
                {
                    showParentDialog('dialog_do_warnig_tip' ,'js_pop_do_warning',data['msg']);
                }
            },
            error: function(er)
            {
                var error_msg = '异常错误';
                showParentDialog('dialog_do_warnig_tip' ,'js_pop_do_warning',error_msg);
            }
        });

        //关闭父窗口
        window.parent.closePopFun('js_pop_box_cooperation');
    }
}

function show_reason(obj)
{
    var select_val = $(obj).val();
    if (select_val == 4)
    {
        $('#cancle_reason_remark').show();
    }
    else
    {
        $('#cancle_reason_remark').hide();
    }
}
</script>
