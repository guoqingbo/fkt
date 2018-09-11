</body>
<!--权证流程开始-->
<div class="js_result_pop" style="position:relative;width:100%;float:left;" id="transfer_step">
    <?php if ($transfer_step_total > 0) { ?>
        <div class="transfer_process">

        </div>
    <?php if ($bargain['is_completed'] == 0){ ?>
        <script>
            $(function () {
                $('.transfer_process').find('.transfer_process_bg2').last().children('img').attr('src', '<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/qz_process6_12.gif');
            })
        </script>
    <?php }else{ ?>
        <script>
            $(function () {
                $('.transfer_process').find('.transfer_process_bg2').last().remove();
            })
        </script>
    <?php } ?>
        <!--权证明细列表开始-->
        <table class="result_item_list_table_head" style="float:left">
            <thead width="100%" align="center" border="0" cellspacing="0"
                   style="background:#f0f0f0; height: 29px;line-height: 29px;">
            <tr width="100%" height="38" bgcolor="#f0f0f0">
                <td width="5%">步骤</td>
                <td width="8%">流程名称</td>
                <!--                <td width="5%">天数</td>-->
                <!--                <td width="5%">开始日期</td>-->
                <td width="5%">完成日期</td>
                <td width="5%">经办人</td>
                <td width="5%">状态</td>
                <!--                <td width="5%">备注</td>-->
                <td width="10%" style="line-height:22px;"><b>操作</b>
                    <!--                     --><?php //if($bargain['is_completed']==0){?>
                    <!--                     <a href="javascript:void(0);"  class="transfer_process_over" style="float:none;padding:4px 8px;-webkit-border-radius:3px;-ms-border-radius:3px;" -->
                    <?php //if($auth['transfer_complete_all']['auth']){?><!--onclick="window.parent.window.openWin('js_all_complete_pop');"-->
                    <?php //}else{?><!--onclick="window.parent.window.purview_none();"-->
                    <?php //}?><!-- id='transfer_completed'>已办结</a>--><?php //}?>
                </td>
            </tr>
            </thead>
            <tbody id="transfer_list">
            <?php foreach ($transfer_step as $key => $val) { ?>
                <tr class="resut_table_border qz_porcee ">
                    <td width="5%"><?= $stage_conf[$val['step_id']]['text']; ?></td>
                    <td width="8%"><?= $val['stage_name1']; ?></td>
                    <td width="5%"><?= !empty($val['complete_time']) ? date('Y-m-d H:i', $val['complete_time']) : ""; ?></td>
                    <td width="5%"><?= $val['complete_signatory_name']; ?></td>
                    <td width="5%"><?= $val['isComplete'] == 1 ? "已完成" : "处理中"; ?></td>
                    <td width="10%">
                        <?php if ($val['isComplete'] == 0) { ?>
                            <a href="javascript:void(0);"
                               <?php if ($auth['transfer_stage' . $val["stage_id"] . '_edit']['auth']){ ?>onclick="window.parent.window.confirm_transfer_detail(<?= $val['id'] ?>);"
                               <?php }else{ ?>onclick="window.parent.window.purview_none();"<?php } ?>>确认完成</a> |
                        <?php } ?>
                        <?php if ($val['isComplete'] == 1) { ?>
                            <a href="javascript:void(0);"
                               <?php if ($auth['transfer_stage' . $val["stage_id"] . '_edit']['auth']){ ?>onclick="transfer_detail(<?= $val['id'] ?>,<?= $bargain['id']; ?>)"
                               <?php }else{ ?>onclick="window.parent.window.purview_none();"<?php } ?>>撤销</a> |
                        <?php } ?>
                            <?php if ($val['isComplete'] == 1) { ?>
                                <a href="javascript:void(0);"
                                   <?php if ($auth['transfer_stage' . $val["stage_id"] . '_edit']['auth']){ ?>onclick="window.parent.window.send_massage(<?= $val['id'] ?>);"
                                   <?php }else{ ?>onclick="window.parent.window.purview_none();"<?php } ?>>发送短信</a> |
                            <?php } ?>
                            <a href="javascript:void(0);"
                               <?php if ($auth['transfer_stage' . $val["stage_id"] . '_edit']['auth']){ ?>onclick="open_transfer_delete(<?= $val['id']; ?>,<?= $bargain['id']; ?>);"
                               <?php }else{ ?>onclick="window.parent.window.purview_none();"<?php } ?>>删除</a>
                        </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php }else{ ?>
        <!--新建模板-->
        <div class="qz_precess_add_modle">
            <p>如需要使用模板，请选择模板。<a href="javascript:void(0)"
                                <?php if ($auth['transfer_add']['auth']){ ?>onclick="open_choose_template(<?= $bargain['id']; ?>);"
                                <?php }else{ ?>onclick="window.parent.window.purview_none();"<?php } ?>>选择模板</a></p>
            <p>如现有模板不能满足您的需要，您可以新建权证流程模板。<a href="javascript:void(0)"
                                            <?php if ($auth['transfer_add']['auth']){ ?>onclick="window.parent.window.add_template_pop();"
                                            <?php }else{ ?>onclick="window.parent.window.purview_none();"<?php } ?>>新建权证流程模版</a>
            </p>
            <!--         <p>如您不想应用模板也可以自行定义本成交应用的权证流程。<a href="javascript:void(0)" <?php if ($auth['transfer_add']['auth']) { ?>onclick="add_transfer();"<?php } else { ?>onclick="window.parent.window.purview_none();"<?php } ?>>新建权证步骤</a></p> -->
        </div>
    <?php } ?>
</div>
<!--权证流程结束-->
</body>
<script>
    $(window).resize(function (e) {
        $(".sale_message dt").css("width", ($(".sale_message").width() - 100 - 36) + "px");
        $(".qz_precess_add_modle p").css("padding-left", ($(".qz_precess_add_modle").width() - 450) / 2 + "px");
    });

    $(".qz_precess_add_modle p").css("padding-left", ($(".qz_precess_add_modle").width() - 450) / 2 + "px");
    $(".sale_message dt").css("width", ($(".sale_message").width() - 100 - 36) + "px");
    //items   table   隔行换色

    $("tbody tr:odd").css("background", "#f7f7f7");
    $("tbody tr:even").css("background", "#fcfcfc");
    $("#transfer_list").find("a").click(function (event) {
        event.stopPropagation();
    });
    window.onload = function () {
        var height = window.document.getElementById('transfer_step').offsetHeight;
        window.parent.document.getElementById('js_mukuai_box').style.height = height + 'px';
    }

    function view_detail(id) {
        $.post("/bargain/transfer_detail", {id: id}, function (data) {
            if (data['transfer_list']['is_remind'] == 1) {
                window.parent.document.getElementById('transfer').src = '/bargain/bargain_transfer_detail/' + id;
                window.parent.window.openWin('js_transfer_pop');
            } else {
                window.parent.document.getElementById('transfer1').src = '/bargain/bargain_transfer_detail/' + id;
                window.parent.window.openWin('js_transfer_pop1');
            }
        }, "json");
    }
    function open_choose_template(id) {
        window.parent.document.getElementById('choose_template').src = '/bargain/get_all_template/' + id;
        window.parent.window.openWin('js_temp_box');
    }


    function add_transfer() {
        window.parent.document.getElementById('addtemp').src = '/bargain/add_transfer_index/' + '<?=$bargain['id'];?>' + "/";
        window.parent.window.openWin('js_addtemp_pop');
    }

    function transfer_detail(id, c_id) {
        window.parent.document.getElementById('stage_id').value = id;
        window.parent.window.openWin('js_cancel_transfer');
    }

    function open_transfer_delete(id) {
        window.parent.document.getElementById('stage_id').value = id;
        window.parent.window.openWin('js_del_transfer');
    }

</script>

