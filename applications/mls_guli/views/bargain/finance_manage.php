<div class="bargain-wrap clearfix">
    <div class="tab-left"><?= $user_tree_menu ?></div>
    <div class="forms_scroll h90">
        <div class="bargain_top_main">
            <div class="i_box">
                <div class="shop_tab_title" style="margin:0 15px  10px  0px;">
                    <!--                        <a class="btn-lv fr"  href="/bargain/bargain_print/-->
                    <?php //echo $bargain['id']; ?><!--" style="margin-left: 20px"-->
                    <!--                           target="_blank"><span>成交打印</span></a>-->
                    <!--                        <a class="btn-lv fr"  href="/bargain/cash_detail_print/-->
                    <?php //echo $bargain['id']; ?><!--"-->
                    <!--                           target="_blank"><span>客户打款明细打印</span></a>-->
                    <a href="/bargain/bargain_look/<?= $bargain['id']; ?>" class="link "><span
                                class="iconfont hide"></span>成交信息</a>
                    <a href="/bargain/transfer_process/<?= $bargain['id']; ?>" class="link "><span
                                class="iconfont hide"></span>过户流程</a>
                    <a href="/bargain/finance_manage/<?= $bargain['id']; ?>" class="link link_on"><span
                                class="iconfont hide"></span>财务管理</a>
                </div>
                <!--成交细节-->
                <div id="js_search_box" class="shop_tab_title  scr_clear top_Marign"
                     style="float:left;display:inline;width:99%;padding-right:1%;background:#FFF;padding-top:10px;margin:0;">
                    <!--                    <a href="javascript:void(0);" class="bargain_filing link link_on" style="margin-left: 16px;"-->
                    <!--                       id="transfer_step" data="/bargain/bargain_transfer_manage/-->
                    <? //= $bargain['id']; ?><!--">过户流程<span-->
                    <!--                                class="iconfont hide"></span></a>-->
                    <?php if ($bargain['type'] != 1) { ?>
                    <a href="javascript:void(0);" class="link bargain_filing link_on" id="collect_money_massage"
                       data="/bargain/bargain_collect_view/<?= $bargain['id']; ?>">取款信息<span
                                class="iconfont hide"></span></a>
                    <a href="javascript:void(0);" class="link bargain_filing" id="replace_flow"
                       data="/bargain/bargain_replace_manage/<?= $bargain['id']; ?>">房款代收付<span
                                class="iconfont hide"></span></a>
                    <?php } ?>
                    <a href="javascript:void(0);" class="link bargain_filing <?php if ($bargain['type'] == 1) echo 'link_on'; ?>" id="replace_tax_flow"
                       data="/bargain/bargain_replace_tax_manage/<?= $bargain['id']; ?>">税费<span
                                class="iconfont hide"></span></a>
                    <a href="javascript:void(0);" class="link bargain_filing" id="replace_signing_flow"
                       data="/bargain/bargain_replace_signing_manage/<?= $bargain['id']; ?>"><?= $bargain['type'] == 1 ? '代办费' : '签约费'; ?>
                        <span
                                class="iconfont hide"></span></a>
                    <a href="/bargain/finance_print/<?php echo $bargain['id']; ?>" target="_blank" class="btn-lv1 fr"
                       id="" style="margin-left:16px;" <span style="margin-right:16px;">打印收付</span></a>
                    <a href="javascript:void(0)" class="btn-lv fr" id="replace_flow1" style="display:none"
                       <?php if ($auth['replace_add']['auth']){ ?>onclick="$('#replace').attr('src','/bargain/bargain_replace_modify/<?= $bargain['id']; ?>');openWin('js_replace_pop');"
                       <?php }else{ ?>onclick="purview_none();"<?php } ?>><span
                                style="margin-right:16px;">+ 添加代收付</span></a>
                    <a href="javascript:void(0)" class="btn-lv fr" id="replace_tax_flow1" style="display:none"
                       <?php if ($auth['replace_tax_add']['auth']){ ?>onclick="$('#replace_tax').attr('src','/bargain/bargain_replace_tax_modify/<?= $bargain['id']; ?>');openWin('js_replace_tax_pop');"
                       <?php }else{ ?>onclick="purview_none();"<?php } ?>><span
                                style="margin-right:16px;"><?= $bargain['type'] == 1 ? '+代办费' : '+签约费'; ?></span></a>
                    <a href="javascript:void(0)" class="btn-lv fr" id="replace_signing_flow1" style="display:none"
                       <?php if ($auth['replace_add']['auth']){ ?>onclick="$('#replace_signing').attr('src','/bargain/bargain_replace_signing_modify/<?= $bargain['id']; ?>');openWin('js_replace_signing_pop');"
                       <?php }else{ ?>onclick="purview_none();"<?php } ?>><span
                                style="margin-right:16px;"><?= $bargain['type'] == 1 ? '+代办费' : '+签约费'; ?></span></a>
                    <a href="javascript:void(0)" class="btn-lv fr" id="collect_money_massage1" style=""
                       <?php if ($auth['replace_add']['auth']){ ?>onclick="window.document.getElementById('iframepage').src = '/bargain/bargain_collect_money/<?= $bargain['id']; ?>'"
                       <?php }else{ ?>onclick="purview_none();"<?php } ?>><span
                                style="margin-right:16px;">修改取款信息</span></a>
                    <input type="hidden" id="stage_id">
                    <input type="hidden" id="flow_id">
                    <input type="hidden" id="divide_id">
                    <input type="hidden" id="bargain_id" value='<?= $bargain['id']; ?>'>
                    <input type="hidden" id="percent_total" value="<?= $divide_total['percent_total']; ?>">
                </div>
                <?php if ($auth['replace_add']['auth'] == 1) { ?>
                    <script>
                        function show_replace_add() {
                            var html = '<a href="javascript:void(0)" class="btn-lv fr" id="replace_flow1" style="display:none" onclick="open_replace_add(<?=$bargain['id']?>)"><span style="margin-right:16px;">+ 添加代收付</span></a>';
                            $("#js_search_box").append(html);
                        }
                    </script>
                <?php }else{ ?>
                    <script>
                        function show_replace_add() {
                            var html = '<a href="javascript:void(0)" class="btn-lv fr" id="replace_flow1" style="display:none" onclick="purview_none();"><span style="margin-right:16px;">+ 添加代收付</span></a>';
                            $("#js_search_box").append(html);
                        }
                    </script>
                <?php } ?>
                <!--嵌入模块弹框-->
                <div id="js_mukuai_box" class="iframePopBox"
                     style="width:100%;border:none; box-shadow:none;display:block;padding:0;padding-top:10px;background:#FFF;margin-top:0;">

                    <iframe frameborder="0" scrolling="no" width="100%" height="100%"
                        <?= $auth['replace_add']['auth'] && (count($bargain['collect_condition']) == 0 && count($bargain['collect_money']) == 0) ? "src='/bargain/bargain_collect_money/" . $bargain['id'] . "'" : "src='/bargain/" . ($bargain['type'] == 1 ? 'bargain_replace_tax_manage' : 'bargain_collect_view') . "/" . $bargain['id'] . "'"; ?>
                            id="iframepage"
                            name="iframepage"></iframe>
                </div>
            </div>
        </div>
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
                    <button class="btn-lv1 JS_Close" type="button">确定</button>
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
    <!--删除-->
    <div id="js_del_transfer" class="pop_box_g pop_see_inform pop_no_q_up">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;步骤删除之后不可恢复。<br/>是否确认删除？
                    </p>
                    <button type="button" class="btn-lv1 JS_Close" onclick="delete_transfer();"
                            style="margin-right:10px;">确定
                    </button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>
    <!--删除-->
    <div id="js_del_pop" class="pop_box_g pop_see_inform pop_no_q_up">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要删除此实收实付吗？<br/>确认删除后不可恢复。
                    </p>
                    <button type="button" class="btn-lv1 JS_Close" onclick="delete_replace_this();"
                            style="margin-right:10px;">确定
                    </button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>
    <!--删除-->
    <div id="js_tax_del_pop" class="pop_box_g pop_see_inform pop_no_q_up">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要删除此实收实付吗？<br/>确认删除后不可恢复。
                    </p>
                    <button type="button" class="btn-lv1 JS_Close" onclick="delete_replace_tax_this();"
                            style="margin-right:10px;">确定
                    </button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>
    <!--删除-->
    <div id="js_signing_del_pop" class="pop_box_g pop_see_inform pop_no_q_up">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要删除此实收实付吗？<br/>确认删除后不可恢复。
                    </p>
                    <button type="button" class="btn-lv1 JS_Close" onclick="delete_replace_signing_this();"
                            style="margin-right:10px;">确定
                    </button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>
    <!--删除-->
    <div id="js_del_pop1" class="pop_box_g pop_see_inform pop_no_q_up">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要删除此应收应付吗？<br/>确认删除后不可恢复。
                    </p>
                    <button type="button" class="btn-lv1 JS_Close" onclick="delete_should_this();"
                            style="margin-right:10px;">确定
                    </button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>

    <!--删除-->
    <div id="js_del_divide" class="pop_box_g pop_see_inform pop_no_q_up">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要删除此业绩分成吗？<br/>确认删除后不可恢复。
                    </p>
                    <button type="button" class="btn-lv1 JS_Close" onclick="delete_divide();"
                            style="margin-right:10px;">确定
                    </button>
                    <button type="button" class="btn-hui1  JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>

    <!--结佣-->
    <div id="js_commission_pop" class="pop_box_g pop_see_inform pop_no_q_up">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;分成结佣后，将不可对分成信息修改。<br/>是否确认操作？
                    </p>
                    <button type="button" class="btn-lv1 btn-left JS_Close" onclick="complete_commission();"
                            style="margin-right:10px;">确定
                    </button>
                    <button type="button" class="btn-hui1  JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>

    <!--确认收付-->
    <div id="js_sure_flow_pop" class="pop_box_g pop_see_inform pop_no_q_up">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;是否确认当前收付已收付？
                    </p>
                    <button type="button" class="btn-lv1 JS_Close" onclick="sure_flow();" style="margin-right:10px;">
                        确定
                    </button>
                    <button type="button" class="btn-hui1  JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>

    <!--权证办结-->
    <div id="js_all_complete_pop" class="pop_box_g pop_see_inform pop_no_q_up">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;权证流程办结后，该成交将默认为已结盘，成交将不可再修改，是否确认操作？
                    </p>
                    <button type="button" class="btn-lv1 JS_Close" onclick="complete_all_temp();"
                            style="margin-right:10px;">确定
                    </button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>
    <!--过户流程确认完成-->
    <div class="delate_btn1 wH335" style="display: none" id="js_complete_pop">
        <dl class="title_top">
            <dd>过户流程确认完成</dd>
            <dt class="JS_Close">X</dt>
        </dl>
        <div class="qz_set_input">
            <p><strong style="font-weight:normal;float:left;display:inline;">成交编号：<?= $bargain['number']; ?></strong>
            </p>
            <p><strong style="font-weight:normal;float:left;display:inline;"><b
                            style="font-weight:normal;float:right;display:inline;">步骤：<label id="confirm_step"></label></b>
            </p>
            <p>流程阶段：<label id="confirm_stage"></label></p>
            <p> 经办人：
                <select class="aad_pop_select_W100" name="complete_signatory_id">
                    <option value="<?= $bargain['complete_signatory_id']; ?>"
                            selected><?= $bargain['complete_signatory_name']; ?></option>
                </select>
            </p>
            <!--            <script>-->
            <!--                $("select[name='confirm_department_id']").change(function () {-->
            <!--                    var department_id = $("select[name='confirm_department_id']").val();-->
            <!--                    if (department_id) {-->
            <!--                        $.ajax({-->
            <!--                            url: "/bargain/signatory_list",-->
            <!--                            type: "GET",-->
            <!--                            dataType: "json",-->
            <!--                            data: {-->
            <!--                                department_id: department_id-->
            <!--                            },-->
            <!--                            success: function (data) {-->
            <!--                                var html = "<option>请选择人员</option>";-->
            <!--                                if (data['result'] == 1) {-->
            <!--                                    for (var i in data['list']) {-->
            <!--                                        html += "<option value='" + data['list'][i]['signatory_id'] + "'>" + data['list'][i]['truename'] + "</option>";-->
            <!--                                    }-->
            <!--                                }-->
            <!--                                $("select[name='confirm_signatory_id']").html(html);-->
            <!--                            }-->
            <!--                        })-->
            <!--                    } else {-->
            <!--                        $("select[name='confirm_signatory_id']").html("<option value=''>请选择</option>");-->
            <!--                    }-->
            <!--                })-->
            <!--            </script>-->
            <p>完成时期：<input type="text" class="aad_pop_select_W100 time_bg" name='confirm_time'
                           value="<?= date('Y-m-d', time()); ?>"
                           onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"></p>
        </div>
        <dl class="qz_prcess_btn">
            <dd><label onclick="confirm_complete();">确认</label></dd>
            <dt class="JS_Close">取消</dt>
        </dl>
    </div>
    <!--新建模版弹窗-->
    <div class="delate_btn1 qz_moudle_H500"
         style="display: none;width:300px;margin-left:-150px;height:200px;margin-top:-100px" id="js_template_pop">
        <dl class="title_top">
            <dd>新建过户流程模板</dd>
            <dt class="JS_Close">X</dt>
        </dl>
        <div class="qz_moudle_con2">
            <p>模版名称：<input type="text" class="qz_moudle_text" name="template_name" maxlength="8"></p>
            <div class="qz_moudle_con1">
                <a href="javascript:void(0)" onclick="save_template(1);" class="JS_Close">下一步</a>
                <a href="javascript:void(0)" class="JS_Close" style="margin:0 0 0 10px;">取　消</a>
            </div>
        </div>
    </div>

    <!--添加过户步骤弹框-->
    <div id="js_addtemp_pop" class="iframePopBox" style="width: 612px;height:438px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="612px" height="438px" class='iframePop' src=""
                id="addtemp"></iframe>
    </div>
    <!--编辑过户步骤弹框-->
    <div id="js_modifytemp_pop" class="iframePopBox" style="width: 612px;height:438px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="612px" height="438px" class='iframePop' src=""
                id="modifytemp"></iframe>
    </div>

    <!--添加应收应付弹框-->
    <div id="js_should_pop" class="iframePopBox" style="width: 582px;height:413px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="582px" height="413px" class='iframePop' src=""
                id="should"></iframe>
    </div>

    <!--添加实收实付弹框-->
    <div id="js_replace_pop" class="iframePopBox" style="width: 580px;height:340px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="580px" height="340px" class='iframePop' src=""
                id="replace"></iframe>
    </div>
    <!--代收付详情弹框-->
    <div id="js_replace_detail_pop" class="iframePopBox" style="width: 580px;height:300px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="580px" height="300px" class='iframePop' src=""
                id="replace_detail"></iframe>
    </div>
    <!--添加税费弹框-->
    <div id="js_replace_tax_pop" class="iframePopBox" style="width: 582px;height:430px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="582px" height="430px" class='iframePop' src=""
                id="replace_tax"></iframe>
    </div>

    <!--添加签约费弹框-->
    <div id="js_replace_signing_pop" class="iframePopBox" style="width: 582px;height:430px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="582px" height="430px" class='iframePop' src=""
                id="replace_signing"></iframe>
    </div>
    <!--添加业绩分成弹框-->
    <div id="js_divide_pop" class="iframePopBox" style="width: 502px;height:422px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="502px" height="420px" class='iframePop' src=""
                id="divide"></iframe>
    </div>

    <!--过户步骤详情弹框-->
    <div id="js_transfer_pop" class="iframePopBox" style="width: 400px;height:250px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="400px" height="300px" class='iframePop' src=""
                id="transfer"></iframe>
    </div>

    <!--过户步骤详情弹框-->
    <div id="js_transfer_pop1" class="iframePopBox" style="width: 400px;height:250px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="400px" height="300px" class='iframePop' src=""
                id="transfer1"></iframe>
    </div>

    <!--选择模板弹框-->
    <div id="js_temp_box" class="iframePopBox" style="width: 842px;height:463px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="842" height="500" class='iframePop' src=""
                id="choose_template"></iframe>
    </div>

    <!--新建模版弹框-->
    <div id="js_edit_template_pop" class="iframePopBox" style="width: 842px;height:504px;border:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="842" height="502" class='iframePop' src=""></iframe>
    </div>

    <!--房源详情弹框-->
    <div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
    </div>
    <img src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->
    <script src="<?php echo MLS_SOURCE_URL; ?>/min/?b=mls_guli/js/v1.0&f=openWin.js,house.js,backspace.js"></script>

    <script>
        $(function () {
            function re_width() {
                var h1 = $(window).height();
                var w1 = $(window).width() - 190;
                $(".tab-left, .forms_scroll").height(h1 - 65);
                $(".forms_scroll").width(w1).show();
            };
            re_width();
            $(window).resize(function (e) {
                re_width();
                $(".sale_message dt").css("width", ($(".sale_message").width() - 100 - 36) + "px");
                $(".qz_precess_add_modle p").css("padding-left", ($(".qz_precess_add_modle").width() - 450) / 2 + "px");
            });

            $(".qz_precess_add_modle p").css("padding-left", ($(".qz_precess_add_modle").width() - 450) / 2 + "px");
            $(".sale_message dt").css("width", ($(".sale_message").width() - 100 - 36) + "px");
            //items   table   隔行换色
            //$("tbody tr:odd").css("background","#f7f7f7");
            //$("tbody tr:even").css("background","#fcfcfc");
            $("#replace_tax_flow").find("tr").css("background", "none");
            $("#add_replace").find("tr").css("background", "none");
            $(".add_pop_messages").find("tr").css("background", "none");

            $("input[name='is_confirm']").live('click', function () {
                $("#confirm_error").text('');
            });

            //var history_num = 0;
            $("#js_search_box").find('.bargain_filing').live('click', function () {
                $("#js_search_box").find('a').removeClass('link_on');
                $(this).addClass('link_on');
                var id = $(this).attr('id');
                var data = $(this).attr('data');
                $("#js_search_box .btn-lv").hide();
                $("#" + id + '1').show();
                $("#iframepage").attr("src", data);
                //history_num = history_num-1;alert(history_num);
                //$("#return_last").live('click',function(){history.go(history_num);});
            })
        });


        //iframe自适应高度
        function iFrameHeight() {

            var ifm = document.getElementById("iframepage");

            var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;
            console.log(subWeb);
            if (ifm != null && subWeb != null) {
                ifm.height = subWeb.body.scrollHeight;

            }


        }

        //打开收付删除弹窗
        function open_replace_add(id) {
            $("#replace").attr('src', '/bargain/bargain_replace_modify/' + id);
            openWin('js_replace_pop');
        }

        //打开收付删除弹窗
        function open_should_delete(id) {
            $('#flow_id').val(id);
            openWin('js_del_pop1');
        }
        function open_divide_delete(id) {
            $('#divide_id').val(id);
            openWin('js_del_divide');
        }

        function open_template_edit(id) {
            $("#js_edit_template_pop").find(".iframePop").attr('src', '/bargain/transfer_template_add/' + id);
            openWin('js_edit_template_pop');
        }

        //删除此条收付记录
        function delete_replace_this() {
            $.ajax({
                url: "/bargain/flow_del",
                type: "GET",
                dataType: "json",
                data: {
                    id: $('#flow_id').val(),
                    c_id: $("#bargain_id").val(),
                    flow_type: 'replace'
                },
                success: function (data) {
                    if (data['result'] == 'ok') {
                        iframepage.window.location = iframepage.window.location;
                        $('#js_prompt1').text(data['msg']);
                        openWin('js_pop_success');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }
        //删除此条收付记录
        function delete_replace_tax_this() {
            $.ajax({
                url: "/bargain/flow_tax_del",
                type: "GET",
                dataType: "json",
                data: {
                    id: $('#flow_id').val(),
                    c_id: $("#bargain_id").val(),
                    flow_type: 'replace_tax'
                },
                success: function (data) {
                    if (data['result'] == 'ok') {
                        iframepage.window.location = iframepage.window.location;
                        $('#js_prompt1').text(data['msg']);
                        openWin('js_pop_success');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }
        //删除此条收付记录
        function delete_replace_signing_this() {
            $.ajax({
                url: "/bargain/flow_signing_del",
                type: "GET",
                dataType: "json",
                data: {
                    id: $('#flow_id').val(),
                    c_id: $("#bargain_id").val(),
                    flow_type: 'replace_signing'
                },
                success: function (data) {
                    if (data['result'] == 'ok') {
                        iframepage.window.location = iframepage.window.location;
                        $('#js_prompt1').text(data['msg']);
                        openWin('js_pop_success');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }
        //删除此条收付记录
        function delete_should_this() {
            $.ajax({
                url: "/bargain/flow_del",
                type: "GET",
                dataType: "json",
                data: {
                    id: $('#flow_id').val(),
                    c_id: $("#bargain_id").val(),
                    flow_type: 'should'
                },
                success: function (data) {
                    if (data['result'] == 'ok') {
                        iframepage.window.location = iframepage.window.location;
                        if (data['num'] == 0) {
                            $("#replace_flow1").remove();
                        }
                        $('#js_prompt1').text(data['msg']);
                        openWin('js_pop_success');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }

        //删除此条收付记录
        function delete_divide() {
            $.ajax({
                url: "/bargain/divide_del",
                type: "POST",
                dataType: "json",
                data: {
                    id: $('#divide_id').val(),
                    c_id: $("#bargain_id").val()
                },
                success: function (data) {
                    if (data['result'] == 'ok') {
                        iframepage.window.location = iframepage.window.location;
                        $('#js_prompt1').text(data['msg']);
                        openWin('js_pop_success');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            });
        }

        function delete_transfer() {
            $.ajax({
                url: "/bargain/delete_temp_step",
                type: "POST",
                dataType: "json",
                data: {
                    stage_id: $('#stage_id').val(),
                    c_id: $("#bargain_id").val()
                },
                success: function (data) {
                    if (data['result'] == 'ok') {
                        $('#js_prompt1').text(data['msg']);
                        iframepage.window.location = iframepage.window.location;
                        openWin('js_pop_success');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            });
        }

        function complete_commission() {
            var total = iframepage.window.document.getElementById("percent_total").value;
            if (parseInt(total) == 100) {
                $.ajax({
                    url: "/bargain/confirm_all_commission",
                    type: "POST",
                    dataType: "json",
                    data: {
                        c_id: $("#bargain_id").val()
                    },
                    success: function (data) {
                        if (data['result'] == 'ok') {
                            iframepage.window.location = iframepage.window.location;
                            $('#js_prompt1').text(data['msg']);
                            openWin('js_pop_success');
                        } else {
                            $('#js_prompt2').text(data['msg']);
                            openWin('js_pop_false');
                        }
                    }
                })
            } else {
                $('#js_prompt2').text('您还有剩余的业绩未分配！');
                openWin('js_pop_false');
            }
        }

        function complete_all_temp() {
            $.ajax({
                url: "/bargain/confirm_all_complete",
                type: "POST",
                dataType: "json",
                data: {
                    bargain_id: $("#bargain_id").val()
                },
                success: function (data) {
                    if (data['result'] == 'ok') {
                        iframepage.window.location = iframepage.window.location;
                        $('#js_prompt1').text(data['msg']);
                        openWin('js_pop_success');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }

        //        function confirm_transfer_detail(id) {
        //            $("#stage_id").val(id);
        //            $.ajax({
        //                type: 'post',
        //                url: '/bargain/sure_temp_judge',
        //                data: {
        //                    stage_id: id,
        //                    bargain_id: $("#bargain_id").val()
        //                },
        //                dataType: 'json',
        //                success: function (data) {
        //                    if (data['result'] == 'ok') {
        //                        $("input[name='is_confirm']").attr('checked', false);
        //                        $("input[name='confirm_time']").val('');
        //                        $("select[name='confirm_department_id']").attr('selected', false);
        //                        $("select[name='confirm_signatory_id']").attr('selected', false);
        //                        $.ajax({
        //                            type: 'post',
        //                            url: '/bargain/transfer_detail',
        //                            data: {
        //                                id: id
        //                            },
        //                            dataType: 'json',
        //                            success: function (data) {
        //                                if (data['result'] == 1) {
        //                                    $("#confirm_step").text(data['transfer_list']['step_name']);
        //                                    $("#confirm_stage").text(data['transfer_list']['stage_name']);
        //                                    if (data['transfer_list']['remark']) {
        //                                        $("#confirm_remark").text(data['transfer_list']['remark']);
        //                                    }
        //                                    openWin('js_complete_pop');
        //                                }
        //                            }
        //                        });
        //                    } else {
        //                        $('#js_prompt2').text(data['msg']);
        //                        openWin('js_pop_false');
        //                    }
        //                }
        //            });
        //        }
        function confirm_transfer_detail(id) {
            $("#stage_id").val(id);
            $.ajax({
                type: 'post',
                url: '/bargain/transfer_detail',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function (data) {
                    if (data['result'] == 1) {
                        $("#confirm_step").text(data['transfer_list']['step_name']);
                        $("#confirm_stage").text(data['transfer_list']['stage_name']);
//                        if (data['transfer_list']['remark']) {
//                            $("#confirm_remark").text(data['transfer_list']['remark']);
//                        }
                        openWin('js_complete_pop');
                    }
                }
            });
        }
        function confirm_complete() {

            $.ajax({
                url: "/bargain/confirm_complete",
                type: "POST",
                dataType: "json",
                data: {
                    bargain_id: $("#bargain_id").val(),

                    //department_id: $("select[name='confirm_department_id']").val(),

                    // signatory_id: $("select[name='confirm_signatory_id']").val(),
                    stage_id: $("#stage_id").val(),
                    complete_signatory_id: $("select[name='complete_signatory_id']").val(),
                    complete_signatory_name: $("select[name='complete_signatory_id']").find("option:selected").text(),
                    confirm_time: $("input[name='confirm_time']").val()
                },
                success: function (data) {
                    if (data['result'] == 'ok') {
                        $("#js_complete_pop").hide();
                        $("#GTipsCoverjs_complete_pop").remove();
                        iframepage.window.location = iframepage.window.location;
                        $('#js_prompt1').text(data['msg']);
                        openWin('js_pop_success');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            });
        }

        function save_template(key) {
            $.ajax({
                url: "/bargain/save_template",
                type: "POST",
                dataType: "json",
                data: {
                    bargain_id: $("#bargain_id").val(),
                    template_name: $("input[name='template_name']").val()
                },
                success: function (data) {
                    var c_id = $("#bargain_id").val();
                    if (data['result'] == 'ok') {
                        $("#js_template_pop").hide();
                        $("#GTipsCoverjs_template_pop").remove();
                        $("#js_edit_template_pop .iframePop").attr('src', '/bargain/transfer_template_add/' + data['data'] + "/" + key + "/" + c_id);
                        openWin('js_edit_template_pop');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }

        function sure_flow() {
            $.ajax({
                url: "/bargain/flow_sure",
                type: "POST",
                dataType: "json",
                data: {
                    id: $("#flow_id").val(),
                    c_id: $("#bargain_id").val()
                },
                success: function (data) {
                    if (data['result'] == 'ok') {
                        iframepage.window.location = iframepage.window.location;
                        $('#js_prompt1').text(data['msg']);
                        openWin('js_pop_success');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }

        function add_template_pop() {
            $("input[name='template_name']").val('');
            $.ajax({
                url: "/bargain/add_template_judge",
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data['result'] == 'ok') {
                        openWin('js_template_pop');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }

    </script>
