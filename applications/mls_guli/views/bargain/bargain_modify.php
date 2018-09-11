<?php if ($bargain['bargain_status'] == 2) { ?>
    <script>
        $(function () {
            $('#modifycont_form input,#modifycont_form select,#modifycont_form textarea,#modifycont_form button').attr('disabled', 'disabled');
//        $("#collect_momey input").removeAttr('disabled');
        })
    </script>
<?php } ?>


<div class="bargain-wrap clearfix">
    <div class="tab-left"><?= $user_tree_menu ?></div>
    <div class="forms_scroll h90">
        <form action="" id="modifycont_form" method="post">
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
                                                class="resut_table_state_1 zws_em ">*</b>签约日期：</p>
                                    <div class="input_add_F">
                                        <input type="text" class="border_color  input_add_F zws_W128 time_bg"
                                               value="<?= isset($bargain['signing_time']) ? date('Y-m-d', $bargain['signing_time']) : date('Y-m-d', time()); ?>"
                                               name="signing_time" style="border:1px solid #d1d1d1;"
                                               onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"
                                               autocomplete="off">
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b
                                                class="resut_table_state_1 zws_em ">*</b>签约人员：</p>
                                     <div class="input_add_F">
                                    <select class="border_color input_add_F zws_li_p_w142"
                                            style="height:24px;line-height:24px;background:#FFF;" name="signatory_id">
                                        <option value="">请选择</option>
                                        <?php if ($signatorys) {
                                            foreach ($signatorys as $key => $val) { ?>
                                                <option value='<?= $val['signatory_id'] ?>' <?= $bargain['signatory_id'] == $val['signatory_id'] ? "selected" : "" ?>><?= $val['truename'] ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                    <div class="zws_block errorBox"></div>
                                         </div>
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
                                <span class="zws_border_span" id="zws_num">
                                    <p class="border_input_title zws_li_p_w"><b
                                                class="resut_table_state_1 zws_em ">*</b>合同价：</p>
                                    <div class="input_add_F">
                                        <input type="text" class="border_color input_add_F zws_W128"
                                               value="<?= $bargain['price']; ?>" name="price" autocomplete="off">
                                        <strong class="zws_padd">元</strong>
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                            <li>
                                <span class="zws_border_span" id="zws_num">
                                    <p class="border_input_title zws_li_p_w"><b
                                                class="resut_table_state_1 zws_em ">*</b>装修款：</p>
                                    <div class="input_add_F">
                                        <input type="text" class="border_color input_add_F zws_W128"
                                               value="<?= $bargain['decoration_price']; ?>" name="decoration_price"
                                               autocomplete="off">
                                        <strong class="zws_padd">元</strong>
                                        <div class="zws_block errorBox"></div>
                                    </div>
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
                        <span class="zws_border_span ">
                            <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1">*</b>建筑面积：</p>
                            <div class="input_add_F">
                                <input type="text" class="border_color zws_W128 input_add_F"
                                       value="<?= $bargain['buildarea']; ?>" name="buildarea" autocomplete="off"><strong
                                        class="zws_padd">m²</strong>
                                <div class="zws_block errorBox"></div>
                            </div>
                        </span>
                                            </li>
                                            <li>
                        <span class="zws_border_span">
                            <p class="border_input_title zws_li_p_w "><b class="resut_table_state_1 zws_em ">*</b>产证编号：</p>
                            <div class="input_add_F">
                                <input type="text" class="border_color input_add_F zws_W128"
                                       value="<?= $bargain['certificate_number']; ?>" name="certificate_number"
                                       autocomplete="off">
                                <div class="zws_block errorBox"></div>
                            </div>
                        </span>
                                            </li>
                                            <li>
                            <span class="zws_border_span">
                                <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>区域：</p>
                                  <div class="input_add_F">
                                <select class="border_color input_add_F zws_li_p_w142"
                                        style="height:24px;line-height:24px;background:#FFF;" name="district_id">
                                      <option value="">请选择</option>
                                    <?php if ($config['district_id']) {
                                        foreach ($config['district_id'] as $key => $val) { ?>
                                            <option value='<?= $key ?>' <?= $bargain['district_id'] == $key ? "selected" : "" ?>><?= $val; ?></option>
                                        <?php }
                                    } ?>
                                </select>
                                <div class="zws_block errorBox"></div>
                                  </div>
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
                                <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>房屋类型：</p>
                                  <div class="input_add_F">
                                <select class="border_color input_add_F zws_li_p_w142"
                                        style="height:24px;line-height:24px;background:#FFF;" name="house_type">
                                     <option value=''>请选择</option>
                                    <?php if ($config["house_type"]) {
                                        foreach ($config["house_type"] as $key => $val) { ?>
                                            <option value='<?= $key ?>' <?= $bargain['house_type'] == $key ? "selected" : "" ?>><?= $val ?></option>
                                        <?php }
                                    } ?>
                                </select>
                                <div class="zws_block errorBox"></div>
                                  </div>
                            </span>
                                            </li>

                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w" style="line-height:28px;"><b
                                                class="resut_table_state_1 zws_em ">*</b>土地性质：</p>
                                    <div class="input_add_F" id="">
                                        <?php if ($config["land_nature"]) {
                                            if (!$id) {
                                                $bargain['land_nature'] = 1;
                                            }
                                            foreach ($config["land_nature"] as $key => $val) { ?>
                                                <p class="zws_radio_no <?= isset($bargain['land_nature']) && $bargain['land_nature'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                                    <input type="radio"
                                                           value=<?= $key; ?> name="land_nature" <?= isset($bargain['land_nature']) && $bargain['land_nature'] == $key ? 'checked' : ''; ?>
                                                           style="display: none"></p>
                                            <?php }
                                        } ?>
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <!--            第四行-->
                            <tr>
                                <td>
                                    <div class="zws_ht_w">
                                        <ul>
                                            <li>
                                <span class="zws_border_span" id="is_mortgage">
                                    <p class="border_input_title zws_li_p_w" style="line-height:28px;"><b
                                                class="resut_table_state_1 zws_em ">*</b>是否抵押：</p>
                                    <div class="input_add_F">
                                        <p class="zws_radio_no <?= isset($bargain['is_mortgage']) && $bargain['is_mortgage'] !== '0' ? 'yesOn' : ''; ?>">有抵押<input
                                                    type="radio" value="1"
                                                    name="is_mortgage" <?= isset($bargain['is_mortgage']) && $bargain['is_mortgage'] !== '0' ? 'checked' : ''; ?>
                                                    style="display: none"></p>
                                        <p class="zws_radio_no <?= !isset($bargain['is_mortgage']) || $bargain['is_mortgage'] == '0' ? 'yesOn' : ''; ?>">无抵押<input
                                                    type="radio" value="0"
                                                    name="is_mortgage" <?= !isset($bargain['is_mortgage']) || $bargain['is_mortgage'] == '0' ? 'checked' : ''; ?>
                                                    style="display: none"></p>
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            <li id="mortgage_thing" style="display: none">
                                <span class="zws_border_span">
                                     <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1"> </b>&nbsp;</p>
                                    <div class="input_add_F">
                                        <input type="text" class="border_color zws_W128 input_add_F"
                                               value="<?= $bargain['mortgage_thing']; ?>" name="mortgage_thing"
                                               autocomplete="off">
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                            <script>
                                                $(function () {
                                                    $("#is_mortgage").live('click', function () {
                                                        var value = $(this).find('input:checked').val();
                                                        if (value == '0') {
                                                            $("#mortgage_thing").hide();
                                                        } else {
                                                            $("#mortgage_thing").show();
                                                        }
                                                    })
                                                })
                                            </script>
                                            <?php if ($bargain['is_mortgage'] == 1) { ?>
                                                <script>
                                                    $(function () {
                                                        $("#mortgage_thing").show();
                                                    })
                                                </script>
                                            <?php } ?>
                                            <li>
                                <span class="zws_border_span" id="is_evaluate">
                                    <p class="border_input_title zws_li_p_w" style="line-height:28px;"><b
                                                class="resut_table_state_1 zws_em ">*</b>是否评估：</p>
                                    <div class="input_add_F">
                                         <?php if ($config["is_evaluate"]) {
                                             if (!$id) {
                                                 $bargain['is_evaluate'] = 1;
                                             }
                                             foreach ($config["is_evaluate"] as $key => $val) { ?>
                                                 <p class="zws_radio_no <?= isset($bargain['is_evaluate']) && $bargain['is_evaluate'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                                     <input
                                                             type="radio" value="<?= $key; ?>"
                                                             name="is_evaluate" <?= isset($bargain['is_evaluate']) && $bargain['is_evaluate'] == $key ? 'checked' : ''; ?>
                                                             style="display: none">
                                                 </p>
                                             <?php }
                                         } ?>
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <!--            第五行-->
                            <tr>
                                <td>
                                    <div class="zws_ht_w">
                                        <ul>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b
                                                class="resut_table_state_1 zws_em ">*</b>成交门店：</p>
                                    <div class="input_add_F">
                                         <input type="text" name="agency_name_a"
                                                value="<?= $bargain['agency_name_a']; ?>"
                                                class="border_color input_add_F zws_W128" autocomplete="off">
                                         <input name="agency_id_a" value="<?= $bargain['agency_id_a']; ?>"
                                                type="hidden">
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                            <script type="text/javascript">
                                                $(function () {
                                                    $.widget("custom.autocomplete", $.ui.autocomplete, {
                                                        _renderItem: function (ul, item) {
                                                            if (item.id > 0) {
                                                                return $("<li>")
                                                                    .data("item.autocomplete", item)
                                                                    .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">' + item.label + '</span></a>')
                                                                    .appendTo(ul);
                                                            } else {
                                                                return $("<li>")
                                                                    .data("item.autocomplete", item)
                                                                    .append('<a class="ui-corner-all" tabindex="-1">' + item.label + '</a>')
                                                                    .appendTo(ul);
                                                            }
                                                        },
                                                        _resizeMenu: function () {
                                                            this.menu.element.css({
                                                                "max-height": "240px",
                                                                "overflow-y": "auto"
                                                            });

                                                        }
                                                    });

                                                    $("input[name='agency_name_a']").autocomplete({
                                                        source: function (request, response) {
                                                            var term = request.term;
                                                            $.ajax({
                                                                url: "/bargain/get_agency_info_by_kw/",
                                                                type: "GET",
                                                                dataType: "json",
                                                                data: {
                                                                    keyword: term
                                                                },
                                                                success: function (data) {
                                                                    //判断返回数据是否为空，不为空返回数据。
                                                                    if (data[0]['id'] != '0') {
                                                                        response(data);
                                                                    } else {
                                                                        response(data);
                                                                    }
                                                                }
                                                            });
                                                        },
                                                        minLength: 1,
                                                        removeinput: 0,
                                                        select: function (event, ui) {
                                                            if (ui.item.id > 0) {
                                                                var agencyname = ui.item.label;
                                                                var id = ui.item.id;
                                                                //操作
                                                                $("input[name='agency_id_a']").val(id);
                                                                $("input[name='agency_name_a']").val(agencyname);
                                                                removeinput = 2;
                                                            } else {
                                                                removeinput = 1;
                                                            }
                                                        },
                                                        close: function (event) {
                                                            if (typeof(removeinput) == 'undefined' || removeinput == 1) {
                                                                $("input[name='agency_name_a']").val("");
                                                                $("input[name='agency_id_a']").val("");
                                                            }
                                                        }
                                                    });
                                                });

                                            </script>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b
                                                class="resut_table_state_1 zws_em ">*</b>经纪人：</p>
                                    <div class="input_add_F">
                                         <input type="text" name="broker_name_a"
                                                value="<?= $bargain['broker_name_a']; ?>"
                                                class="border_color input_add_F  zws_W128" autocomplete="off">
                                         <input name="broker_id_a" value="<?= $bargain['broker_id_a']; ?>"
                                                type="hidden">
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                            <script type="text/javascript">
                                                $(function () {
                                                    $.widget("custom.autocomplete", $.ui.autocomplete, {
                                                        _renderItem: function (ul, item) {
                                                            if (item.id > 0) {
                                                                return $("<li>")
                                                                    .data("item.autocomplete", item)
                                                                    .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">' + item.label + '</span><span class="ui_district">' + item.phone + '</span></a>')
                                                                    .appendTo(ul);
                                                            } else {
                                                                return $("<li>")
                                                                    .data("item.autocomplete", item)
                                                                    .append('<a class="ui-corner-all" tabindex="-1">' + item.label + '</a>')
                                                                    .appendTo(ul);
                                                            }
                                                        },
                                                        _resizeMenu: function () {
                                                            this.menu.element.css({
                                                                "max-height": "240px",
                                                                "overflow-y": "auto"
                                                            });

                                                        }
                                                    });
                                                    $("input[name='broker_name_a']").autocomplete({
                                                        source: function (request, response) {
                                                            var term = request.term;
                                                            var agency_id_a = $("input[name='agency_id_a']").val();
                                                            $.ajax({
                                                                url: "/bargain/get_broker_info_by_kw/",
                                                                type: "GET",
                                                                dataType: "json",
                                                                data: {
                                                                    keyword: term,
                                                                    agency_id: agency_id_a
                                                                },
                                                                success: function (data) {
                                                                    //判断返回数据是否为空，不为空返回数据。
                                                                    if (data[0]['id'] != '0') {
                                                                        response(data);
                                                                    } else {
                                                                        response(data);
                                                                    }
                                                                }
                                                            });
                                                        },
                                                        minLength: 1,
                                                        removeinput: 0,
                                                        select: function (event, ui) {
                                                            if (ui.item.id > 0) {
                                                                var brokername = ui.item.label;
                                                                var phone = ui.item.phone;
                                                                var id = ui.item.id;
                                                                var agency_id = ui.item.agency_id;
                                                                var company_id = ui.item.company_id;

                                                                //操作
                                                                $("input[name='broker_id_a']").val(id);
                                                                $("input[name='broker_name_a']").val(brokername);
                                                                $("input[name='broker_tel_a']").val(phone);

                                                                $.ajax({
                                                                    url: "/bargain/get_agency_info_by_agencyid_companyid/",
                                                                    type: "GET",
                                                                    dataType: "json",
                                                                    data: {
                                                                        agency_id: agency_id,
                                                                        company_id: company_id
                                                                    },
                                                                    success: function (data) {
                                                                        if (data.id > 0) {
                                                                            var agencyname = data.name;
                                                                            var id = data.id;

                                                                            //操作
                                                                            $("input[name='agency_id_a']").val(id);
                                                                            $("input[name='agency_name_a']").val(agencyname);
                                                                            removeinput = 2;
                                                                        } else {
                                                                            removeinput = 1;
                                                                        }
                                                                    }
                                                                });
                                                                removeinput = 2;
                                                            } else {
                                                                removeinput = 1;
                                                            }
                                                        },
                                                        close: function (event) {
                                                            if (typeof(removeinput) == 'undefined' || removeinput == 1) {
                                                                $("input[name='broker_id_a']").val("");
                                                                $("input[name='broker_name_a']").val("");
                                                                $("input[name='broker_tel_a']").val("");
                                                            }
                                                        }
                                                    })
                                                });
                                            </script>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b
                                                class="resut_table_state_1 zws_em ">*</b>电话：</p>
                                    <div class="input_add_F">
                                          <input type="text" name="broker_tel_a"
                                                 value="<?= $bargain['broker_tel_a']; ?>"
                                                 class="border_color input_add_F zws_W128" autocomplete="off">
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <!--            第六行-->
                            <tr>
                                <td>
                                    <div class="zws_ht_w">
                                        <ul>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w" style="line-height:28px;"><b
                                                class="resut_table_state_1 zws_em ">*</b>签约公司：</p>
                                    <div class="input_add_F">
                                         <?php if ($config["signatory_company"]) {
                                             if (!$id) {
                                                 $bargain['signatory_company'] = 1;
                                             }
                                             foreach ($config["signatory_company"] as $key => $val) { ?>
                                                 <p class="zws_radio_no <?= isset($bargain['signatory_company']) && $bargain['signatory_company'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                                     <input type="radio"
                                                            value=<?= $key; ?> name="signatory_company" <?= isset($bargain['signatory_company']) && $bargain['signatory_company'] == $key ? 'checked' : ''; ?>
                                                            style="display: none"></p>
                                             <?php }
                                         } ?>
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                            </li>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>权证人员：</p>
                                      <div class="input_add_F">
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
                                      </div>
                                </span>
                                            </li>
                                            <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>理财人员：</p>
                                      <div class="input_add_F">
                                    <select class="border_color input_add_F zws_li_p_w142"
                                            style="height:24px;line-height:24px;background:#FFF;" name="finance_id">
                                        <option value="">请选择</option>
                                        <?php if ($finances) {
                                            foreach ($finances as $key => $val) { ?>
                                                <option value='<?= $val['id'] ?>' <?= $bargain['finance_id'] == $val['id'] ? "selected" : "" ?>><?= $val['name'] ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                    <div class="zws_block errorBox"></div>
                                      </div>
                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <!--            卖方信息-->
                            <tr>
                                <td>
                                    <div class="zws_ht_w">
                                        <ul>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <!--            买方信息-->
                            <tr>
                                <td>
                                    <div class="zws_ht_w">
                                        <ul>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--卖方信息-->
                    <dl class="sale_message">
                        <dd class="aad_pop_pB_10">
                            <img src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/saler_03.png"/>
                            <p>卖方信息</p>
                        </dd>
                        <dt>
                        <div class="aad_pop_p_B10" style="display:inline;">
                            <li>
                                <strong><em class="resut_table_state_1">*</em>卖方姓名：</strong>
                                <b>
                                    <input type="text" class="border_color input_add_F zws_W128"
                                           value="<?= $bargain['owner']; ?>" name="owner" autocomplete="off">
                                    <span class="zws_block errorBox"></span>
                                </b>
                            </li>
                            <li>
                                <strong><em class="resut_table_state_1"></em>身份证号：</strong>
                                <b>
                                    <input type="text" class="border_color input_add_F zws_W128"
                                           value="<?= $bargain['owner_idcard']; ?>" name="owner_idcard"
                                           autocomplete="off">
                                    <div class="zws_block errorBox"></div>
                                </b>
                            </li>
                            <li>
                                <strong><em class="resut_table_state_1">*</em>电话：</strong>
                                <b>
                                    <input type="text" class="border_color input_add_F zws_W128"
                                           value="<?= $bargain['owner_tel_1']; ?>" name="owner_tel_1"
                                           autocomplete="off">
                                    <i id="owner_tel_1" style='background-color: #1dc680' class="icon iconfont">
                                        &#xe608;</i>
                                    <span class="zws_block errorBox"></span>
                                </b>

                            </li>
                            <li <?= empty($bargain['owner_tel_2']) ? "style = 'display:none'" : ''; ?> >

                                <input type="text" class="border_color input_add_F zws_W128"
                                       value="<?= $bargain['owner_tel_2']; ?>" name="owner_tel_2" autocomplete="off">
                                <i id="owner_tel_2" style='background-color: #1dc680' class="icon iconfont">&#xe60c;</i>
                                <span class="zws_block errorBox"></span>

                            </li>
                            <li <?= empty($bargain['owner_tel_3']) ? "style = 'display:none'" : ''; ?> >
                                <input type="text" class="border_color input_add_F zws_W128"
                                       value="<?= $bargain['owner_tel_3']; ?>" name="owner_tel_3" autocomplete="off">
                                <i id="owner_tel_3" style='background-color: #1dc680' class="icon iconfont">&#xe60c;</i>
                                <span class="zws_block errorBox"></span>
                            </li>
                        </div>
                        <div style="display:inline;">
                            <li id="show_trust_a">
                                <strong><em class="resut_table_state_1">*</em>公证委托：</strong>
                                <b class="input_add_F">
                                    <p style="width: 30px;padding-right:0"
                                       class="zws_radio_no <?= isset($bargain['show_trust_a']) && $bargain['show_trust_a'] !== '0' ? 'yesOn' : ''; ?>">
                                        是<input type="radio" value="1"
                                                name="show_trust_a" <?= isset($bargain['show_trust_a']) && $bargain['show_trust_a'] !== '0' ? 'checked' : ''; ?>
                                                style="display: none;"></p>
                                    <p style="width: 30px;padding-right:0"
                                       class="zws_radio_no <?= !isset($bargain['show_trust_a']) || $bargain['show_trust_a'] == '0' ? 'yesOn' : ''; ?>">
                                        否<input type="radio" value="0"
                                                name="show_trust_a" <?= !isset($bargain['show_trust_a']) || $bargain['show_trust_a'] == '0' ? 'checked' : ''; ?>
                                                style="display: none"></p>
                                    <span class="zws_block errorBox"></span>
                                </b>
                            </li>
                            <script>
                                $(function () {
                                    $("#show_trust_a").live('click', function () {
                                        var value = $(this).find('input:checked').val();
                                        if (value == '0') {
                                            $("#trust_name_a").hide();
                                            $("#trust_idcard_a").hide();
                                        } else {
                                            $("#trust_name_a").show();
                                            $("#trust_idcard_a").show();
                                        }
                                    })
                                })
                            </script>
                            <?php if ($bargain['show_trust_a'] == 1) { ?>
                                <script>
                                    $(function () {
                                        $("#trust_name_a").show();
                                        $("#trust_idcard_a").show();
                                    })
                                </script>
                            <?php }else{ ?>
                                <script>
                                    $(function () {
                                        $("#trust_name_a").hide();
                                        $("#trust_idcard_a").hide();
                                    })
                                </script>
                            <?php } ?>
                            <li id="trust_name_a">
                                <strong><em class="resut_table_state_1">*</em>受托人姓名：</strong>
                                <b>
                                    <input type="text" name="trust_name_a" value="<?= $bargain['trust_name_a']; ?>"
                                           class="border_color input_add_F zws_W128" autocomplete="off">
                                    <span class="zws_block errorBox"></span>
                                </b>
                            </li>
                            <li id="trust_idcard_a">
                                <strong><em class="resut_table_state_1">*</em>受托人证件号码：</strong>
                                <b>
                                    <input type="text" name="trust_idcard_a" value="<?= $bargain['trust_idcard_a']; ?>"
                                           class="border_color input_add_F zws_W128" autocomplete="off">
                                    <span class="zws_block errorBox"></span>
                                </b>
                            </li>
                        </div>
                        </dt>
                    </dl>
                    <!--买方信息-->
                    <dl class="sale_message">
                        <dd class="aad_pop_pB_10">
                            <img src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/buyer_06.png"/>
                            <p>买方信息</p>
                        </dd>
                        <dt>
                        <div class="aad_pop_p_B10" style="display:inline;">
                            <li>
                                <strong><em class="resut_table_state_1">*</em>买方姓名：</strong>
                                    <input type="text" class="border_color input_add_F zws_W128"
                                           value="<?= $bargain['customer']; ?>" name="customer" autocomplete="off">
                                    <span class="zws_block errorBox"></span>
                            </li>
                            <li>
                                <strong><em class="resut_table_state_1"></em>身份证号：</strong>
                                <b><input type="text" class="border_color input_add_F zws_W128"
                                          value="<?= $bargain['customer_idcard']; ?>" name="customer_idcard"
                                          autocomplete="off">
                                    <span class="zws_block errorBox"></span>
                                </b>
                            </li>
                            <li>
                                <strong><em class="resut_table_state_1">*</em>电话：</strong>
                                <b>
                                    <input type="text" class="border_color input_add_F zws_W128"
                                           value="<?= $bargain['customer_tel_1']; ?>" name="customer_tel_1"
                                           autocomplete="off">
                                    <i id="customer_tel_1" style='background-color: #1dc680' class="icon iconfont">
                                        &#xe608;</i>
                                    <span class="zws_block errorBox"></span>
                                </b>
                            </li>
                            <li <?= empty($bargain['customer_tel_2']) ? "style = 'display:none'" : ''; ?> >

                                <input type="text" class="border_color input_add_F zws_W128"
                                       value="<?= $bargain['customer_tel_2']; ?>" name="customer_tel_2"
                                       autocomplete="off">
                                <i id="customer_tel_2" style='background-color: #1dc680' class="icon iconfont">
                                    &#xe60c;</i>
                                <span class="zws_block errorBox"></span>
                            </li>
                            <li <?= empty($bargain['customer_tel_3']) ? "style = 'display:none'" : ''; ?> >

                                <input type="text" class="border_color input_add_F zws_W128"
                                       value="<?= $bargain['customer_tel_3']; ?>" name="customer_tel_3"
                                       autocomplete="off">
                                <i id="customer_tel_3" style='background-color: #1dc680' class="icon iconfont">
                                    &#xe60c;</i>
                                <span class="zws_block errorBox"></span>
                            </li>

                        </div>
                        <div class=" " style="display:inline;">
                            <li id="show_trust_b">
                                <strong><em class="resut_table_state_1">*</em>公证委托：</strong>
                                <b class="input_add_F">
                                    <p style="width: 30px;padding-right:0"
                                       class="zws_radio_no <?= isset($bargain['show_trust_b']) && $bargain['show_trust_b'] !== '0' ? 'yesOn' : ''; ?>">
                                        是<input type="radio" value="1"
                                                name="show_trust_b" <?= isset($bargain['show_trust_b']) && $bargain['show_trust_b'] !== '0' ? 'checked' : ''; ?>
                                                style="display: none;"></p>
                                    <p style="width: 30px;padding-right:0"
                                       class="zws_radio_no <?= !isset($bargain['show_trust_b']) || $bargain['show_trust_b'] == '0' ? 'yesOn' : ''; ?>">
                                        否<input type="radio" value="0"
                                                name="show_trust_b" <?= !isset($bargain['show_trust_b']) || $bargain['show_trust_b'] == '0' ? 'checked' : ''; ?>
                                                style="display: none"></p>
                                    <span class="zws_block errorBox"></span>
                                </b>
                            </li>
                            <script>
                                $(function () {
                                    $("#show_trust_b").live('click', function () {
                                        var value = $(this).find('input:checked').val();
                                        if (value == '0') {
                                            $("#trust_name_b").hide();
                                            $("#trust_idcard_b").hide();
                                        } else {
                                            $("#trust_name_b").show();
                                            $("#trust_idcard_b").show();
                                        }
                                    })
                                })
                            </script>
                            <?php if ($bargain['show_trust_b'] == 1) { ?>
                                <script>
                                    $(function () {
                                        $("#trust_name_b").show();
                                        $("#trust_idcard_b").show();
                                    })
                                </script>
                            <?php }else{ ?>
                                <script>
                                    $(function () {
                                        $("#trust_name_b").hide();
                                        $("#trust_idcard_b").hide();
                                    })
                                </script>
                            <?php } ?>
                            <li id="trust_name_b">
                                <strong><em class="resut_table_state_1">*</em>受托人姓名：</strong>
                                <b>
                                    <input type="text" name="trust_name_b" value="<?= $bargain['trust_name_b']; ?>"
                                           class="border_color input_add_F zws_W128" autocomplete="off">
                                    <span class="zws_block errorBox"></span>
                                </b>
                            </li>
                            <li id="trust_idcard_b">
                                <strong><em class="resut_table_state_1">*</em>受托人证件号码：</strong>
                                <b>
                                    <input type="text" name="trust_idcard_b" value="<?= $bargain['trust_idcard_b']; ?>"
                                           class="border_color input_add_F zws_W128" autocomplete="off">
                                    <span class="zws_block errorBox"></span>
                                </b>
                            </li>

                        </div>
                        </dt>
                    </dl>
                </div>
            </div>
            <div class="sale_message_h" style="line-height:1px;"></div>
            <div style="clear:both;"></div>
            <!--佣金结算-->
            <div class="sale_message_commission" style="margin-bottom:0;">
                <div style="width:100%;clear:both;display:block;">
                    <h4 class="h4 padding_size zws_h4_font">付款信息</h4>
                </div>
                <div class="sale_message_commission_detial" style="display:block;width:100%;float:left;">
                    <div style="display:inline;width:100%;float:left;">
                        <p class="aad_pop_p_B10" id="buy_type">
                            <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>付款方式：</b>
                            <span class="input_add_F" style="width:50%">
                      <?php if ($config["buy_type"]) {
                          if (!$id) {
                              $bargain['buy_type'] = 1;
                          }
                          foreach ($config["buy_type"] as $key => $val) { ?>
                              <em class="zws_radio_no <?= $bargain['buy_type'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                  <input type="radio"
                                         value=<?= $key; ?> name="buy_type" <?= $bargain['buy_type'] == $key ? 'checked' : ''; ?>
                                         style="display: none"></em>
                          <?php }
                      } ?>
                                <div class="zws_block errorBox"></div>
                </span>
                        </p>
                        <p class="aad_pop_p_B10">
                            <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>监管银行：</b>
                            <strong class="zws_ip_W150">
                                <select class="border_color zws_line input_add_F" name="loan_bank">
                                    <?php foreach ($loan_bank as $key => $val) { ?>
                                        <option value="<?= $val['id']; ?>" <?= $bargain['loan_bank'] == $val["id"] ? 'selected' : '' ?>><?= $val["bank_name"] . " " . $val["bank_deposit"] . " " . $val["card_no"]; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="zws_block errorBox"></span>
                            </strong>
                        </p>
                        <p class="aad_pop_p_B10 payment_once">
                            <b class="zws_ip_W100">于</b>
                            <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F time_bg"
                                                       value="<?= isset($bargain['payment_once_time']) ? date('Y-m-d', $bargain['payment_once_time']) : ''; ?>"
                                                       name="payment_once_time"
                                                       onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"
                                                       autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">日前，将全部购房款￥</b>
                            <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                       value="<?= $bargain['tatal_money']; ?>" name="tatal_money"
                                                       autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">元,存入对应银行监管账户</b>
                        </p>
                        <p class="aad_pop_p_B10 payment_period">
                            <b class="zws_ip_W100">于</b>
                            <span class="input_add_F">

                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F time_bg"
                                                       value="<?= isset($bargain['payment_period_time']) ? date('Y-m-d', $bargain['payment_period_time']) : ''; ?>"
                                                       name="payment_period_time"
                                                       onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"
                                                       autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">日前，将购房款￥</b>
                            <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                       value="<?= $bargain['purchase_money'][0]; ?>"
                                                       name="purchase_money1" autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">元,存入对应银行监管账户</b>
                        </p>
                        <p class="aad_pop_p_B10 payment_period">
                            <b class="zws_ip_W100">于</b>
                            <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                       value="<?= $bargain['purchase_condition'][0]; ?>"
                                                       name="purchase_condition1" autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">情况下，将购房款￥</b>

                            <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                       value="<?= $bargain['purchase_money'][1]; ?>"
                                                       name="purchase_money2" autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">元整,存入对应银行监管账户</b>
                        </p>
                        <p class="aad_pop_p_B10 payment_period">
                            <b class="zws_ip_W100">于</b>
                            <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                       value="<?= $bargain['purchase_condition'][1]; ?>"
                                                       name="purchase_condition2" autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">情况下，将购房款￥</b>

                            <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                       value="<?= $bargain['purchase_money'][2]; ?>"
                                                       name="purchase_money3" autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">元整,存入对应银行监管账户</b>
                        </p>
                        <p class="aad_pop_p_B10  payment_mortgage">
                            <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>贷款方式：</b>
                            <span class="input_add_F" style="width:60%">
                     <?php if ($config["loan_type"]) {
                         foreach ($config["loan_type"] as $key => $val) { ?>
                             <em class="zws_radio_no <?= $bargain['loan_type'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                 <input type="radio"
                                        value=<?= $key; ?> name="loan_type" <?= $bargain['loan_type'] == $key ? 'checked' : ''; ?>
                                        style="display: none"></em>
                         <?php }
                     } ?>
                </span>
                        </p>
                        <p class="aad_pop_p_B10 payment_mortgage">
                            <b class="zws_ip_W100">于</b>
                            <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F time_bg"
                                                       value="<?= isset($bargain['first_time']) ? date('Y-m-d', $bargain['first_time']) : ''; ?>"
                                                       value="<?= $bargain['first_time']; ?>" name="first_time"
                                                       onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"
                                                       autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">日前，将购房首付款￥</b>

                            <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                       value="<?= $bargain['first_money']; ?>" name="first_money"
                                                       autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">元整,存入对应银行监管账户</b>
                        </p>
                        <p class="aad_pop_p_B10 payment_mortgage">
                            <b class="zws_ip_W100">余款￥</b>
                            <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                       value="<?= $bargain['spare_money']; ?>" name="spare_money"
                                                       autocomplete="off"></strong>
                </span>
                            <b class="zws_ip_W100">元整,则办理按揭贷款</b>
                        </p>
                    </div>
                </div>
                <script>
                    function buy_type(value) {
                        $(".payment_once").hide();
                        $(".payment_period").hide();
                        $(".payment_mortgage").hide();
                        if (value == 3) {
                            $(".payment_mortgage").show();
                        } else if (value == 2) {
                            $(".payment_period").show();
                        } else {
                            $(".payment_once").show();
                        }
                    }
                    buy_type(<?=$bargain['buy_type'];?>);
                    $(function () {
                        $("#buy_type .zws_radio_no").live('click', function () {
                            var value = $(this).find('input').val();
                            buy_type(value);
                        })
                    })
                </script>

                <div class="sale_message_commission_detial" style="display:block;width:100%;float:left;">
                    <p class="aad_pop_p_B10">
                        <b class="zws_ip_W100">交房时间：</b>
                        <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                           value="<?= $bargain['house_time']; ?>" name="house_time"
                                                           autocomplete="off">
                            <span class="zws_block errorBox" style="text-indent:0;padding-left:0;"></span>
                        </strong>

                    </span>
                    </p>
                    <p class="aad_pop_p_B10">
                        <b class="zws_ip_W100" style="width: 330px">税费合计（不含中介费，评估费，签约费）：</b>
                        <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                           value="<?= $bargain['tax_pay_tatal']; ?>"
                                                           name="tax_pay_tatal" autocomplete="off"> 元
                            <span class="zws_block errorBox" style="text-indent:0;padding-left:0;"></span>
                        </strong>

                    </span>
                    </p>
                    <p class="aad_pop_p_B10">
                        <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>税费约定：</b>
                        <span class="input_add_F" id="tax_pay_type">
                         <?php if ($config["tax_pay_type"]) {
                             if (!$id) {
                                 $bargain['tax_pay_type'] = 1;
                             }
                             foreach ($config["tax_pay_type"] as $key => $val) { ?>
                                 <em class="zws_radio_no <?= $bargain['tax_pay_type'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                     <input type="radio"
                                            value=<?= $key; ?> name="tax_pay_type" <?= $bargain['tax_pay_type'] == $key ? 'checked' : ''; ?>
                                            style="display: none"></em>
                             <?php }
                         } ?>
                    </span>
                        <em class="zws_ip_W150" id="tax_pay_appoint" style="display: none"><input
                                    class="border_color zws_ip_W130 input_add_F" type="text"
                                    value="<?= $bargain['tax_pay_type'] == '4' && isset($bargain['tax_pay_appoint']) ? $bargain['tax_pay_appoint'] : ''; ?>"
                                    name="tax_pay_appoint"></em>
                    <div class="zws_block errorBox"></div>
                    </p>
                    <script>
                        $(function () {
                            $("#tax_pay_type").live('click', function () {
                                var value = $(this).find('input:checked').val();
                                if (value != '4') {
                                    $("#tax_pay_appoint").hide();
                                } else {
                                    $("#tax_pay_appoint").show();
                                }
                            })
                        })
                    </script>
                    <?php if ($bargain['tax_pay_type'] == 4) { ?>
                        <script>
                            $(function () {
                                $("#tax_pay_appoint").show();
                            })
                        </script>
                    <?php } ?>
                    <p class="aad_pop_p_B10">
                        <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>交易票据归属：</b>
                        <span class="input_add_F" style="width:60%">
                         <?php if ($config["note_belong"]) {
                             if (!$id) {
                                 $bargain['note_belong'] = 1;
                             }
                             foreach ($config["note_belong"] as $key => $val) { ?>
                                 <em class="zws_radio_no <?= $bargain['note_belong'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                     <input type="radio"
                                            value=<?= $key; ?> name="note_belong" <?= $bargain['note_belong'] == $key ? 'checked' : ''; ?>
                                            style="display: none"></em>
                             <?php }
                         } ?>
                            <div class="zws_block errorBox"></div>
                    </span>
                    </p>
                    <p class="aad_pop_p_B10" style="width:700px;text-align: left">
                        <b class="zws_ip_W100" style="height: 60px"><em class="resut_table_state_1"></em>卖方缺少资料：</b>
                        <span class="" style="width:60%;">
                         <?php if ($config["seller_lacks"]) {
                             foreach ($config["seller_lacks"] as $key => $val) { ?>
                                 <span class="" style="padding-right: 10px">
                            <input type="checkbox" class="" name="seller_lacks" value=<?= $key; ?>
                                <?= in_array($key, json_decode($bargain['seller_lacks'])) ? "checked" : "" ?>> <?= $val; ?>
                                     <?php if (isset($specSellerLacks[$key])) {$specField = 'seller_' . $specSellerLacks[$key]; ?>:
                                         <input type="text" class="border_color zws_ip_W130" name="<?php echo $specField; ?>"
                                                value="<?php echo $bargain[$specField]; ?>" />
                                     <?php } ?>
                        </span>
                             <?php }
                         } ?>
                            <span class="" style="padding-right: 10px">其他:
                                <input type="text" class="border_color zws_ip_W130" name="seller_lacks_others"
                                       value=<?= $bargain['seller_lacks_others']; ?>>
                            </span>
                                   <div class="zws_block errorBox"></div>
                </span>
                    </p>
                    <p class="aad_pop_p_B10" style="width:650px;text-align: left">
                        <b class="zws_ip_W100" style="height: 60px"><em class="resut_table_state_1"></em>买方缺少资料：</b>
                        <span class="" style="width:60%;">
                         <?php if ($config["buyer_lacks"]) {
                             foreach ($config["buyer_lacks"] as $key => $val) { ?>
                                 <span class="" style="padding-right: 10px">
                                     <input type="checkbox" class=""
                                            name="buyer_lacks"
                                            value=<?= $key; ?>
                                         <?= in_array($key, json_decode($bargain['buyer_lacks'])) ? "checked" : "" ?>>
                                     <?= $val; ?>
                                     <?php if (isset($specBuyerLacks[$key])) {$specField = 'buyer_' . $specBuyerLacks[$key]; ?>:
                                         <input type="text" class="border_color zws_ip_W130" name="<?php echo $specField; ?>"
                                                value="<?php echo $bargain[$specField]; ?>" />
                                     <?php } ?>
                        </span>
                             <?php }
                         } ?>
                            <span class="" style="padding-right: 10px">其他:
                                <input type="text" class="border_color zws_ip_W130" name="buyer_lacks_others"
                                       value=<?= $bargain['buyer_lacks_others']; ?>>
                            </span>
                        <div class="zws_block errorBox"></div>
                    </span>
                    </p>
                    <dl>
                        <dd>合同备注：</dd>
                        <dt><textarea class="zws_textarea" name="remarks"><?= $bargain['remarks']; ?></textarea>
                        </dt>
                    </dl>
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

        //卖方新增手机号输入框
        $("#owner_tel_1").on("click", function () {
            if ($("#owner_tel_2").parent().css("display") == 'none') {
                $("#owner_tel_2").parent().show();
            } else {
                $("#owner_tel_3").parent().show();
            }
        });
        $("#owner_tel_2,#owner_tel_3").on('click', function () {
            $(this).parent().hide();
            $(this).siblings("input").val('');
        })

        //买方新增手机号输入框
        $("#customer_tel_1").on("click", function () {
            if ($("#customer_tel_2").parent().css("display") == 'none') {
                $("#customer_tel_2").parent().show();
            } else {
                $("#customer_tel_3").parent().show();
            }
        });
        $("#customer_tel_2,#customer_tel_3").on('click', function () {
            $(this).parent().hide();
            $(this).siblings("input").val('');
        })

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
                        $('#modifycont_form input,#modifycont_form select,#modifycont_form textarea,#modifycont_form button').attr('disabled', 'disabled');
                    } else {
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }
    }
</script>

<img src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?b=mls_guli/js/v1.0&f=openWin.js,house.js,backspace.js"></script>
