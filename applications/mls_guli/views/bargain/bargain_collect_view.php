<script>
    $(function () {
        $('#modifycont_form input,#modifycont_form select,#modifycont_form textarea,#collect_money_form input').attr('disabled', 'disabled').css("background-color", " rgb(235, 235, 228)");
//        $("#collect_momey input").removeAttr('disabled');
    })

</script>

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
                        <p class="aad_pop_p_B10" style="display: none">
                        <td class="zws_center">
                            <?php if ($id) { ?>
                                <input type="hidden" name="bargain_id" value="<?= $id ?>">
                                <input type="hidden" name="submit_flag" value="modify">
                            <?php } else { ?>
                                <input type="hidden" name="submit_flag" value="add">
                            <?php } ?>
                            <?php if ($auth['replace_edit']['auth']) { ?>
                                <button type="button" class="btn-lv1 btn-left"
                                        onclick="window.parent.document.getElementById('iframepage').src = '/bargain/bargain_collect_money/<?= $id; ?>'">
                                    修改
                                </button>
                            <?php } else { ?>
                                <button type="button" class="btn-lv1 btn-left"
                                        onclick="window.parent.window.purview_none();"">修改</button>
                            <?php } ?>
                        </td>
                        </p>
                    </div>
                </div>
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
                        onclick="window.parent.document.getElementById('iframepage').src = '/bargain/bargain_collect_view/<?= $bargain['id']; ?>'">
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
    function modify_collect_money() {

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
