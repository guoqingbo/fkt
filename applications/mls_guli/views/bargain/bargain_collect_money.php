<body>
<!--取款信息-->
<div class="" id="collect_money" style="height: 200px;padding-top: 0">
    <form action="" id="collect_money_form" method="post" disabled="disabled">
        <!--付款信息-->
        <div class="sale_message_commission" style="margin-bottom:0;">

            <div class="sale_message_commission_detial" style="display:block;width:100%;float:left;margin-top:20px">
                <div style="display:inline;width:100%;float:left;">
                    <!--                                    取款信息-->
                    <div id="collect_momey">
                        <p class="aad_pop_p_B10">
                            <b class="zws_ip_W100">于</b>
                            <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W220 input_add_F"
                                                           value="<?= $bargain['collect_condition'][0]; ?>"
                                                           name="collect_condition1" autocomplete="off"></strong>
                    </span>
                            <b class="zws_ip_W100"></b>

                            <b class="zws_ip_W160">，划付房款￥</b>
                            <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                           value="<?= $bargain['collect_money'][0]; ?>"
                                                           name="collect_money1" autocomplete="off"></strong>
                    </span>
                            <b class="zws_ip_W30">元整</b>
                        </p>
                        <p class="aad_pop_p_B10">
                            <b class="zws_ip_W100">于</b>
                            <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W220 input_add_F"
                                                           value="<?= $bargain['collect_condition'][1]; ?>"
                                                           name="collect_condition2" autocomplete="off"></strong>
                    </span>
                            <b class="zws_ip_W100"></b>

                            <b class="zws_ip_W160">，划付房款￥</b>
                            <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                           value="<?= $bargain['collect_money'][1]; ?>"
                                                           name="collect_money2" autocomplete="off"></strong>
                    </span>
                            <b class="zws_ip_W30">元整</b>
                        </p>
                        <p class="aad_pop_p_B10">
                            <b class="zws_ip_W100">于</b>
                            <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W220 input_add_F"
                                                           value="<?= $bargain['collect_condition'][2]; ?>"
                                                           name="collect_condition3" autocomplete="off"></strong>
                    </span>
                            <b class="zws_ip_W100"></b>

                            <b class="zws_ip_W160">，划付房款￥</b>
                            <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                           value="<?= $bargain['collect_money'][2]; ?>"
                                                           name="collect_money3" autocomplete="off"></strong>
                    </span>
                            <b class="zws_ip_W30">元整</b>
                        </p>
                        <p class="aad_pop_p_B10">
                            <b class="zws_ip_W100">于</b>
                            <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W220 input_add_F"
                                                           value="<?= $bargain['collect_condition'][3]; ?>"
                                                           name="collect_condition4" autocomplete="off"></strong>
                    </span>
                            <b class="zws_ip_W100"></b>

                            <b class="zws_ip_W160">，划付房款￥</b>
                            <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                           value="<?= $bargain['collect_money'][3]; ?>"
                                                           name="collect_money4" autocomplete="off"></strong>
                    </span>
                            <b class="zws_ip_W30">元整</b>
                        </p>
                        <p class="aad_pop_p_B10">
                        <td class="zws_center">
                            <?php if ($id) { ?>
                                <input type="hidden" name="bargain_id" value="<?= $id ?>">
                                <input type="hidden" name="submit_flag" value="modify">
                            <?php } else { ?>
                                <input type="hidden" name="submit_flag" value="add">
                            <?php } ?>
                            <?php if ($auth['replace_edit']['auth']) { ?>
                                <button type="button" class="btn-lv1 btn-left" onclick="save_collect_money();">保存
                                </button>
                            <?php } else { ?>
                                <button type="button" class="btn-lv1 btn-left"
                                        onclick="window.parent.window.purview_none();"">保存</button>
                            <?php } ?>
                            <button type="button" class="btn-hui1" onclick="history.go(0);">取消</button>
                        </td>
                        </p>
                    </div>
                </div>
            </div>
            <!--保存和确认-->
            <div style="padding-top:10px;clear: both;">
                <table width="100%">
                    <tr>

                    </tr>
                </table>
            </div>
    </form>
</div>
<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                            <img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/dakacg.gif"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt1"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 JS_Close" type="button"
                        onclick="window.parent.document.getElementById('iframepage').src = '/bargain/bargain_collect_view/<?= $id; ?>'">
                    确定
                </button>
            </div>
        </div>
    </div>
</div>

<!--操作失败弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_false">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                            <img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 JS_Close" type="button">确定</button>
            </div>
        </div>
    </div>
</div>
</body>
<!--取款信息结束-->
<script>

    //修改提交合同详情资料
    function save_collect_money() {
        collect_condition = new Array(
            $("input[name='collect_condition1']").val(),
            $("input[name='collect_condition2']").val(),
            $("input[name='collect_condition3']").val(),
            $("input[name='collect_condition4']").val()
        );
        collect_money = new Array(
            $("input[name='collect_money1']").val(),
            $("input[name='collect_money2']").val(),
            $("input[name='collect_money3']").val(),
            $("input[name='collect_money4']").val()
        );

        $.ajax({
            type: 'POST',
            url: '/bargain/save_collect_money',
            data: {
                submit_flag: $("input[name='submit_flag']").val(),
                id: $("input[name='bargain_id']").val(),//成交记录id
                collect_condition: collect_condition,//取款条件
                collect_money: collect_money,//取款金额
            },
            dataType: 'json',
            success: function (data) {
                if (data['result'] == 'ok') {
                    $("#js_prompt1").text(data['msg']);
                    openWin('js_pop_success');
                }
                else {
                    $("#js_prompt2").text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        });
    }
    $(window).resize(function (e) {
        $(".sale_message dt").css("width", ($(".sale_message").width() - 100 - 36) + "px");
        $(".qz_precess_add_modle p").css("padding-left", ($(".qz_precess_add_modle").width() - 450) / 2 + "px");
    });

    $(".qz_precess_add_modle p").css("padding-left", ($(".qz_precess_add_modle").width() - 450) / 2 + "px");
    $(".sale_message dt").css("width", ($(".sale_message").width() - 100 - 36) + "px");
    //items   table   隔行换色

    $("tbody tr:odd").css("background", "#f7f7f7");
    $("tbody tr:even").css("background", "#fcfcfc");
    $("#replace_list").find("a").click(function (event) {
        event.stopPropagation();
    });

    window.onload = function () {
        var height = window.document.getElementById('collect_money').offsetHeight;
        window.parent.document.getElementById('js_mukuai_box').style.height = height + 'px';
    }

    function view_detail(id) {
        window.parent.document.getElementById('replace_detail').src = '/bargain/bargain_replace_detail/' + id;
        window.parent.window.openWin('js_replace_detail_pop');
    }

    function flow_replace_detail(id, c_id) {
        window.parent.document.getElementById('replace').src = '/bargain/bargain_replace_modify/' + c_id + "/" + id;
        window.parent.window.openWin('js_replace_pop');
    }
    //打开收付删除弹窗
    function open_replace_delete(id) {
        window.parent.document.getElementById('flow_id').value = id;
        window.parent.window.openWin('js_del_pop');
    }

    //打开收付确认弹窗
    function open_replace_sure(id) {
        window.parent.document.getElementById('flow_id').value = id;
        window.parent.window.openWin('js_sure_flow_pop');
    }
</script>
