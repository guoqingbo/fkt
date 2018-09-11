<?php if ($bargain['bargain_status'] == 2) { ?>
    <script>
        $(function () {
            $('#modifycont_form_one input,#modifycont_form_one select,#modifycont_form_one textarea,#modifycont_form_one button').attr('disabled', 'disabled');
//        $("#collect_momey input").removeAttr('disabled');
        })
    </script>
<?php } ?>
<div class="bargain-wrap clearfix">
    <div class="tab-left"><?= $user_tree_menu ?></div>
    <div class="forms_scroll h90">
        <form action="" id="modifycont_form_one" method="post">
            <input type="hidden" name="type" value="<?= $type ?>">
            <div class="bargain_top_main">
                <div class="i_box" style=" padding:0;background:#f7f7f7">
                    <div class="clearfix" style=" padding: 12px 16px;background:#f7f7f7">
                        <table width="100%">
                            <thead>
                            <tr>
                                <?php if ($id) { ?>
                                    <div class="shop_tab_title" style="margin:0 15px  10px  0px;">
                                        <a class="btn-lv fr" href="/bargain/bargain_print/<?php echo $bargain['id']; ?>"
                                           style="margin-left: 20px"
                                           target="_blank"><span>成交打印</span></a>
                                        <a class="btn-lv fr"
                                           href="/bargain/cash_detail_print/<?php echo $bargain['id']; ?>"
                                           target="_blank"><span>客户打款明细打印</span></a>
                                        <a href="/bargain/bargain_look/<?= $bargain['id']; ?>"
                                           class="link link_on"><span class="iconfont hide"></span>成交信息</a>
                                        <a href="/bargain/transfer_process/<?= $bargain['id']; ?>" class="link "><span
                                                    class="iconfont hide"></span>过户流程</a>
                                        <a href="/bargain/finance_manage/<?= $bargain['id']; ?>" class="link "><span
                                                    class="iconfont hide"></span>财务管理</a>
                                    </div>
                                <?php } ?>
                                <td class="h4">成交信息</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <div class="zws_ht_w">
                                        <ul>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w "><b
                                                class="resut_table_state_1 zws_em ">*</b>成交编号：</p>
                                    <div class="input_add_F">
                                        <input type="text" class="border_color input_add_F zws_W128"
                                               value="<?= $bargain['number']; ?>" name="number" autocomplete="off">
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b
                                                class="resut_table_state_1 zws_em ">*</b>录入时间：</p>
                                    <div class="input_add_F">
                                        <input type="text" class="border_color  input_add_F zws_W128 time_bg"
                                               value="<?= isset($bargain['receipt_time']) ? date('Y-m-d', $bargain['receipt_time']) : date('Y-m-d', time()); ?>"
                                               name="receipt_time" style="border:1px solid #d1d1d1;"
                                               onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"
                                               autocomplete="off">
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>办证人员：</p>
                                    <select class="border_color input_add_F zws_li_p_w142"
                                            style="height:24px;line-height:24px;background:#FFF;" name="warrant_inside">
                                        <option value="">请选择</option>
                                        <?php if ($warrant_persons) {
                                            foreach ($warrant_persons as $key => $val) { ?>
                                                <option value='<?= $val['signatory_id'] ?>' <?= $bargain['warrant_inside'] == $val['signatory_id'] ? "selected" : "" ?>><?= $val['truename'] ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                    <div class="zws_block errorBox"></div>
                                </span>
                                            </li>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>办理状态：</p>
                                    <select class="border_color input_add_F zws_li_p_w142"
                                            style="height:24px;line-height:24px;width: 86px;background:#FFF;"
                                            name="bargain_status">
                                        <?php if ($config["bargain_status"]) {
                                            foreach ($config["bargain_status"] as $key => $val) { ?>
                                                <option value='<?= $key ?>' <?= $bargain['bargain_status'] == $key ? "selected" : "" ?>><?= $val ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                     <button type="button" class="btn-lv1" <?php if ($auth['replace_add']['auth']) { ?>
                                         onclick="openWin('js_completed_box');" <?php } else { ?>onclick="purview_none();"<?php } ?>>结案</button>
                                </span>
                                            </li>


                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <!--            第二行-->
                            <tr>
                                <td>
                                    <div class="zws_ht_w">
                                        <ul>
                                            <li>
                            <span class="zws_border_span">
                                <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>区域：</p>
                                <select class="border_color input_add_F zws_li_p_w142"
                                        style="height:24px;line-height:24px;background:#FFF;" name="district_id">
                                      <option value="">请选择</option>
                                    <?php if ($config['district_id']) {
                                        foreach ($config['district_id'] as $key => $val) { ?>
                                            <option value='<?= $key ?>' <?= $bargain['district_id'] == $key ? "selected" : "" ?>><?= $val; ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </span>
                                            </li>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b
                                                class="resut_table_state_1 zws_em ">*</b>楼盘名称：</p>
                                    <div class="input_add_F">
                                    <input type="text" class="border_color zws_W128 input_add_F"
                                           value="<?= $bargain['block_name']; ?>" name="block_name" autocomplete="off">
                                    <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b
                                                class="resut_table_state_1 zws_em ">*</b>物业地址：</p>
                                    <div class="input_add_F">
                                    <input type="text" class="border_color zws_W128 input_add_F"
                                           value="<?= $bargain['house_addr']; ?>" name="house_addr" autocomplete="off">
                                    <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>代办银行：</p>

                                    <select class="border_color input_add_F zws_li_p_w142"
                                            style="height:24px;line-height:24px;background:#FFF;" name="agent_bank">
                                        <option value="">请选择</option>
                                        <?php foreach ($agent_bank as $key => $val) { ?>
                                            <option value="<?= $val['id']; ?>" <?= $bargain['agent_bank'] == $val["id"] ? 'selected' : '' ?>><?= $val["bank_name"] . " " . $val["bank_deposit"] . " " . $val["card_no"]; ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="zws_block errorBox"></div>
                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="zws_ht_w">
                                        <ul>

                                            <li>
                            <span class="zws_border_span">
                                <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em ">*</b>代办类别：</p>
                                <div class="input_add_F">
                                <select class="border_color input_add_F zws_li_p_w142"
                                        style="height:24px;line-height:24px;background:#FFF;" name="agent_type">
                                      <option value="">请选择</option>
                                    <?php if ($config['agent_type']) {
                                        foreach ($config['agent_type'] as $key => $val) { ?>
                                            <option value='<?= $key ?>' <?= $bargain['agent_type'] == $key ? "selected" : "" ?>><?= $val; ?></option>
                                        <?php }
                                    } ?>
                                </select>
                                <div class="zws_block errorBox"></div>
                                </div>
                            </span>
                                            </li>
                                            <!--                                            <li>-->
                                            <!--                                <span class="zws_border_span">-->
                                            <!--                                    <p class="border_input_title zws_li_p_w "><b-->
                                            <!--                                                class="resut_table_state_1 zws_em ">*</b>代办公司：</p>-->
                                            <!--                                    <div class="input_add_F">-->
                                            <!--                                        <input type="text" class="border_color input_add_F zws_W128"-->
                                            <!--                                               value="-->
                                            <? //= $bargain['agent_company']; ?><!--" name="agent_company"-->
                                            <!--                                               autocomplete="off">-->
                                            <!--                                        <div class="zws_block errorBox"></div>-->
                                            <!--                                    </div>-->
                                            <!--                                </span>-->
                                            <!--                                            </li>-->
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w "><b
                                                class="resut_table_state_1 zws_em ">*</b>开发商：</p>
                                    <div class="input_add_F">
                                        <input type="text" class="border_color input_add_F zws_W128"
                                               value="<?= $bargain['developer']; ?>" name="developer"
                                               autocomplete="off">
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>

                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <!--第三行-->
                            <tr>
                                <td>
                                    <div class="zws_ht_w">
                                        <ul>

                                            <li>
                        <span class="zws_border_span">
                            <p class="border_input_title zws_li_p_w "><b class="resut_table_state_1 zws_em ">*</b>买方姓名：</p>
                            <div class="input_add_F">
                                <input type="text" class="border_color input_add_F zws_W128"
                                       value="<?= $bargain['customer']; ?>" name="customer"
                                       autocomplete="off">
                                <div class="zws_block errorBox"></div>
                            </div>
                        </span>
                                            </li>
                                            <li>
                        <span class="zws_border_span">
                            <p class="border_input_title zws_li_p_w "><b
                                        class="resut_table_state_1 zws_em "></b>身份证号：</p>
                            <div class="input_add_F">
                                <input type="text" class="border_color input_add_F zws_W128"
                                       value="<?= $bargain['customer_idcard']; ?>" name="customer_idcard"
                                       autocomplete="off">
                                <div class="zws_block errorBox"></div>
                            </div>
                        </span>
                                            </li>
                                            <li>
                        <span class="zws_border_span">
                            <p class="border_input_title zws_li_p_w "><b
                                        class="resut_table_state_1 zws_em ">*</b>电话：</p>
                            <div class="input_add_F">
                                <input type="text" class="border_color input_add_F zws_W128"
                                       value="<?= $bargain['customer_tel']; ?>" name="customer_tel"
                                       autocomplete="off">
                                <div class="zws_block errorBox"></div>
                            </div>
                        </span>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!--佣金结算-->
            <div class="sale_message_commission" style="margin-bottom:0;">
                <div class="sale_message_commission_detial" style="display:block;width:100%;float:left;">
                    <dl>
                        <dd>承办备注：</dd>
                        <dt><textarea class="zws_textarea"
                                      name="undertake_remarks"><?= $bargain['undertake_remarks']; ?></textarea></dt>
                    </dl>
                </div>
            </div>
            <!--保存和确认-->
            <div style="padding-top:10px;clear: both;">
                <table width="100%">
                    <tr>
                        <td class="zws_center">
                            <?php if ($id) { ?>
                                <input type="hidden" name="bargain_id" value="<?= $id ?>">
                                <input type="hidden" name="submit_flag" value="modify">
                            <?php } else { ?>
                                <input type="hidden" name="bargain_id" value="">
                                <input type="hidden" name="submit_flag" value="add">
                            <?php } ?>

                            <?php if ($auth['edit']['auth'] && $bargain['bargain_status'] != 2) { ?>
                                <button type="submit" class="btn-lv1 btn-left">保存</button>
                            <?php } else { ?>
                                <button type="button" class="btn-lv1 btn-left" onclick="purview_none();">保存</button>
                            <?php } ?>

                            <button type="button" class="btn-hui1" onclick="history.go(-1);">取消</button>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
</div>
<!--结案弹框-->
<div id="js_completed_box" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;结案之后成交信息不可再修改。<br/>是否确认结案？
                </p>
                <button type="button"
                        class="btn-lv1 JS_Close" <?php if ($auth['replace_add']['auth']) { ?> onclick="completed();"<?php } else { ?>onclick="purview_none();"<?php } ?>
                        style="margin-right:10px;">确定
                </button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            </div>
        </div>
    </div>
</div>
<!--结案成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success1">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="45%" align="right" style="padding-right:10px;">
                            <img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/dakacg.gif"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
        </div>
    </div>
</div>
<!--房源选择弹框-->
<div id="js_house_box" class="iframePopBox" style="width: 980px;height:575px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="980" height="575px" class='iframePop' src=""></iframe>
</div>

<!--客源选择弹框-->
<div id="js_customer_box" class="iframePopBox" style="width: 980px;height:575px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="980" height="575px" class='iframePop' src=""></iframe>
</div>

<!--合作选择弹框-->
<div id="js_cooperate_box" class="iframePopBox" style="width: 980px;height:575px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="980" height="575px" class='iframePop' src=""></iframe>
</div>

<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"
                                  onclick="bargain_look()"></a>
        </div>
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
                <button class="btn JS_Close" type="button"
                        onclick="bargain_look()">确定
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
                            <img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/dakacg.gif"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">


    $(function () {
        function re_width() {
            var h1 = $(window).height();
            var w1 = $(window).width() - 190;
            $(".tab-left, .forms_scroll").height(h1 - 35);
            $(".forms_scroll").width(w1).show();
        };
        re_width();
        $(window).resize(function (e) {
            re_width();
            $(".sale_message dt").css("width", ($(".sale_message").width() - 100 - 36) + "px");
        });


        $(".sale_message dt").css("width", ($(".sale_message").width() - 100 - 36) + "px");
        //items   table   隔行换色
        //房源地址输入框宽度
        $(".zws_W60").css("width", ($("#zws_first_tr").width() + $("#zws_num").width() - $(this).find(".border_input_title").width()) + "px");

        $(".input_add_F").children().click(function () {
            $(this).siblings().removeClass("yesOn");
            $(this).addClass("yesOn");
            $(this).parent().find("input").attr('checked', false);
            $(this).find("input").attr('checked', true);
        })


        $("#zws_choice").css("width", $("#zws_input_w").width() + "px");


    });

    $(window).resize(function () {
        //房源地址输入框宽度
        $(".zws_W60").css("width", ($("#zws_first_tr").width() + $("#zws_num").width() - $(this).find(".border_input_title").width()) + "px");

        $("#zws_choice").css("width", $("#zws_input_w").width() + "px");

    })

    function open_house_pop() {
        var house_id = $("input[name='house_id']").val();
        $("#js_house_box .iframePop").attr('src', '/bargain/get_house/1/' + house_id);
        openWin('js_house_box');
    }

    function get_info(id) {
        closeWindowWin('js_house_box');
        if (id) {
            $.post(
                '/bargain/get_info',
                {
                    'id': id,
                    'type': 1
                },
                function (data) {
                    $("input[name='block_id']").val(data['block_id']);
                    $("input[name='block_name']").val(data['block_name']);
                    $("input[name='house_addr']").val(data['address'] + data['dong'] + '栋' + data['unit'] + '单元' + data['door'] + '室');
                    $("input[name='house_id']").val(data['house_id']);
                    $("select[name='sell_type']").val(data['sell_type']);
                    $("input[name='buildarea']").val(data['buildarea']);
                    $("input[name='owner']").val(data['owner']);
                    $("input[name='owner_tel']").val(data['telno1']);
                    $("input[name='owner_idcard']").val(data['idcare']);
                    $("input[name='block_name']").attr('disabled', 'true');
                    $("input[name='house_addr']").attr('disabled', 'true');
                    $("input[name='house_id']").attr('disabled', 'true');
                    $("select[name='sell_type']").attr('disabled', 'true');
                    $("input[name='buildarea']").attr('disabled', 'true');
                    $("input[name='owner_tel']").attr('disabled', 'true');
                    $("input[name='owner']").attr('disabled', 'true');
                    $("input[name='owner_idcard']").attr('disabled', 'true');
                }, 'json'
            );
        } else {
            $("input[name='block_id']").val('');
            $("input[name='block_name']").val('');
            $("input[name='house_addr']").val('');
            $("input[name='house_id']").val('');
            $("select[name='sell_type']").val('');
            $("input[name='buildarea']").val('');
            $("input[name='owner']").val('');
            $("input[name='owner_tel']").val('');
            $("input[name='owner_idcard']").val('');
            $("input[name='block_name']").removeAttr('disabled');
            $("input[name='house_addr']").removeAttr('disabled');
            $("input[name='house_id']").removeAttr('disabled');
            $("select[name='sell_type']").removeAttr('disabled');
            $("input[name='buildarea']").removeAttr('disabled');
            $("input[name='owner_tel']").removeAttr('disabled');
            $("input[name='owner']").removeAttr('disabled');
            $("input[name='owner_idcard']").removeAttr('disabled');
        }
    }

    function open_customer_pop() {
        var customer_id = $("input[name='customer_id']").val();
        $('#js_customer_box .iframePop').attr('src', '/bargain/get_customer/1/' + customer_id);
        openWin('js_customer_box');
    }

    function get_customer_info(id) {
        closeWindowWin('js_customer_box');
        if (id) {
            $.post(
                '/bargain/get_customer_info',
                {
                    'id': id,
                    'type': 1
                },
                function (data) {
                    $("input[name='customer_id']").val(data['customer_id']);
                    $("input[name='customer']").val(data['truename']);
                    $("input[name='customer_tel']").val(data['telno1']);
                    $("input[name='customer_idcard']").val(data['idno']);
                    $("input[name='customer_id']").attr('disabled', 'true');
                    $("input[name='customer']").attr('disabled', 'true');
                    $("input[name='customer_tel']").attr('disabled', 'true');
                    $("input[name='customer_idcard']").attr('disabled', 'true');
                }, 'json'
            );
        } else {
            $("input[name='customer_id']").val('');
            $("input[name='customer']").val('');
            $("input[name='customer_tel']").val('');
            $("input[name='customer_idcard']").val('');
            $("input[name='customer_id']").removeAttr('disabled');
            $("input[name='customer']").removeAttr('disabled');
            $("input[name='customer_tel']").removeAttr('disabled');
            $("input[name='customer_idcard']").removeAttr('disabled');
        }
    }

    function open_cooperate_pop() {
        var order_sn = $("input[name='order_sn']").val();
        $('#js_cooperate_box .iframePop').attr('src', '/bargain/get_cooperate/1/' + order_sn);
        openWin('js_cooperate_box');
    }

    function get_cooperate_info(id) {
        closeWindowWin('js_cooperate_box');
        if (id) {
            $.post(
                '/bargain/get_cooperate_info',
                {
                    'id': id
                },
                function (data) {
                    $("input[name='order_sn']").val(data['order_sn']);
                    $("input[name='order_sn']").attr('disabled', 'true');
                }, 'json'
            );
        } else {
            $("input[name='order_sn']").val('');
            $("input[name='order_sn']").removeAttr('disabled');
        }
    }
    function bargain_look() {
        var bargain_id = $("input[name='bargain_id']").val();
        location.href = '/bargain/bargain_look/' + bargain_id;
    }
    function completed() {
        var bargain_id = $("input[name='bargain_id']").val();
        if (bargain_id) {
            $.ajax({
                url: "/bargain/bargain_completed",
                type: "POST",
                dataType: "json",
                data: {
                    bargain_id: bargain_id,
                },
                success: function (data) {
                    if (data['result'] == 'ok') {
                        $('#js_prompt').text(data['msg']);
                        openWin('js_pop_success1');
                        $('#modifycont_form_one input,#modifycont_form_one select,#modifycont_form_one textarea,#modifycont_form_one button').attr('disabled', 'disabled');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }
    }
    //    $("select[name = 'district_id'],input[name = 'block_name']").blur(function () {
    //        var districtName = $("select[name = 'district_id']").find('option:selected').text();
    //        var blockname = $("input[name = 'block_name']").val();
    //        $("input[name = 'house_addr']").val(districtName + ' ' + blockname)
    //    })
</script>

<img src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?b=mls_guli/js/v1.0&f=openWin.js,house.js,backspace.js"></script>
