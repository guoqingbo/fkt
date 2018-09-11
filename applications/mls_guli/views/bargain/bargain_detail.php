<script>
    $(function () {
        var bargain_input_style = {
            "border": "0",
            "background-color": "rgb(235, 235, 228)",
            "word-wrap": "break-word"
        }
        $('#modifycont_form input,#modifycont_form select,#modifycont_form textarea').attr('readonly', 'readonly').css(bargain_input_style);
//        $("#collect_momey input").removeAttr('disabled');
    })

</script>
<div class="bargain-wrap clearfix">
    <div class="tab-left"><?= $user_tree_menu ?></div>
    <div class="forms_scroll h90">
        <div class="bargain_top_main">
            <div class="i_box">
                <div class="">
                    <form action="" id="modifycont_form" method="post">
                        <div class="bargain_top_main">
                            <div class="i_box" style=" padding:0;background:#f7f7f7">
                                <div class="clearfix" style=" padding: 12px 16px;background:#f7f7f7">
                                    <table width="100%">
                                        <thead>
                                        <tr>
                                            <!--                                            <div class="shop_tab_title" style="margin:0 15px  10px  0px;">-->
                                            <!--                                                <a class="btn-lv fr" href="/bargain/bargain_print/-->
                                            <?php //echo $bargain['id']; ?><!--"-->
                                            <!--                                                   style="margin-left: 20px"-->
                                            <!--                                                   target="_blank"><span>成交打印</span></a>-->
                                            <!--                                                <a class="btn-lv fr" href="/bargain/cash_detail_print/-->
                                            <?php //echo $bargain['id']; ?><!--"-->
                                            <!--                                                   target="_blank"><span>客户打款明细打印</span></a>-->
                                            <!--                                                <a href="/bargain/modify_bargain/-->
                                            <? //= $type; ?><!--/--><? //= $bargain['id']; ?><!--"-->
                                            <!--                                                   class="link link_on"><span class="iconfont hide"></span>成交编辑</a>-->
                                            <!--                                                <a href="/bargain/transfer_process/-->
                                            <? //= $bargain['id']; ?><!--" class="link "><span-->
                                            <!--                                                            class="iconfont hide"></span>过户流程</a>-->
                                            <!--                                                <a href="/bargain/finance_manage/-->
                                            <? //= $bargain['id']; ?><!--" class="link "><span-->
                                            <!--                                                            class="iconfont hide"></span>财务管理</a>-->
                                            <!--                                            </div>-->
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
                                        <input type="text" class="border_color  input_add_F zws_W128"
                                               value="<?= isset($bargain['signing_time']) ? date('Y-m-d', $bargain['signing_time']) : date('Y-m-d', time()); ?>"
                                               name="signing_time" style="border:1px solid #d1d1d1;"
                                               onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"
                                               autocomplete="off" disabled>
                                        <div class="zws_block errorBox"></div>
                                    </div>
                                </span>
                                                        </li>
                                                        <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>签约人员：</p>
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
                                </span>
                                                        </li>
                                                        <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>办理状态：</p>
                                    <select class="border_color input_add_F zws_li_p_w142"
                                            style="height:24px;line-height:24px;background:#FFF;" name="bargain_status">
                                        <?php if ($config["bargain_status"]) {
                                            foreach ($config["bargain_status"] as $key => $val) { ?>
                                                <option value='<?= $key ?>' <?= $bargain['bargain_status'] == $key ? "selected" : "" ?>><?= $val ?></option>
                                            <?php }
                                        } ?>
                                    </select>
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

                                                        <li style="width:500px">
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b
                                                class="resut_table_state_1 zws_em ">*</b>物业地址：</p>
                                    <div class="input_add_F">
                                    <input type="text" class="border_color zws_W128 input_add_F" style="width:378px"
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
                                                        <li style="width: 500px">
                        <span class="zws_border_span">
                            <p class="border_input_title zws_li_p_w "><b class="resut_table_state_1 zws_em ">*</b>产证编号：</p>
                            <div class="input_add_F">
                                <input type="text" class="border_color input_add_F zws_W128" style="width:378px"
                                       value="<?= $bargain['certificate_number']; ?>" name="certificate_number"
                                       autocomplete="off">
                                <div class="zws_block errorBox"></div>
                            </div>
                        </span>
                                                        </li>
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
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="zws_ht_w">
                                                    <ul>
                                                        <li style="width: 500px">
                            <span class="zws_border_span">
                                <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>房屋类型：</p>
                                <select class="border_color input_add_F zws_li_p_w142"
                                        style="height:24px;line-height:24px;background:#FFF;width: 392px"
                                        name="house_type">
                                    <?php if ($config["house_type"]) {
                                        foreach ($config["house_type"] as $key => $val) { ?>
                                            <option value='<?= $key ?>' <?= $bargain['house_type'] == $key ? "selected" : "" ?>><?= $val ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </span>
                                                        </li>
                                                        <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w" style="line-height:28px;"><b
                                                class="resut_table_state_1 zws_em ">*</b>土地性质：</p>
                                    <div class="input_add_F" id="">
                                        <?php if ($config["land_nature"]) {
                                            foreach ($config["land_nature"] as $key => $val) {
                                                if ($bargain['land_nature'] == $key) { ?>
                                                <p class="zws_radio_no <?= isset($bargain['land_nature']) && $bargain['land_nature'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                                    <input type="radio"
                                                           value=<?= $key; ?> name="land_nature" <?= isset($bargain['land_nature']) && $bargain['land_nature'] == $key ? 'checked' : ''; ?>
                                                           style="display: none"></p>
                                                <?php }
                                            }
                                        } ?>
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
                                        <p class="zws_radio_no yesOn"><?= isset($bargain['is_mortgage']) && $bargain['is_mortgage'] !== '0' ? '有抵押' : '无抵押'; ?></p>
                                    </div>
                                </span>
                                                        <li id="mortgage_thing" style="display: none">
                                <span class="zws_border_span">
                                     <p class="border_input_title zws_li_p_w" style="width:0;"><b
                                                 class="resut_table_state_1"> </b>&nbsp;</p>
                                    <div class="input_add_F">
                                        <input type="text" class="border_color zws_W128 input_add_F"
                                               style="width: 208px"
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
                                             foreach ($config["is_evaluate"] as $key => $val) {
                                                 if ($bargain['is_evaluate'] == $key) { ?>
                                                 <p class="zws_radio_no <?= isset($bargain['is_evaluate']) && $bargain['is_evaluate'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                                     <input
                                                             type="radio" value="<?= $key; ?>"
                                                             name="is_evaluate" <?= isset($bargain['is_evaluate']) && $bargain['is_evaluate'] == $key ? 'checked' : ''; ?>
                                                             style="display: none"></p>
                                                 <?php }
                                             }
                                         } ?>
                                        <!--                                        <p class="zws_radio_no -->
                                        <? //= isset($bargain['is_evaluate']) && $bargain['is_evaluate'] == '0' ? 'yesOn' : ''; ?><!--">否<input-->
                                        <!--                                                    type="radio" value="0"-->
                                        <!--                                                    name="is_evaluate" --><? //= isset($bargain['is_evaluate']) && $bargain['is_evaluate'] == '0' ? 'checked' : ''; ?>
                                        <!--                                                    style="display: none"></p>-->
                                        <!--                                        <p class="zws_radio_no -->
                                        <? //= isset($bargain['is_evaluate']) && $bargain['is_evaluate'] == '1' ? 'yesOn' : ''; ?><!--">是<input-->
                                        <!--                                                    type="radio" value="1"-->
                                        <!--                                                    name="is_evaluate" --><? //= isset($bargain['is_evaluate']) && $bargain['is_evaluate'] == '1' ? 'checked' : ''; ?>
                                        <!--                                                    style="display: none"></p>-->
                                    </div>
                                </span>
                                                        </li>

                                                        <!--                            <li id="evaluate_charges" style="display: none">-->
                                                        <!--                        <span class="zws_border_span" >-->
                                                        <!--                             <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em ">*</b>评估收费：</p>-->
                                                        <!--                            <div class="input_add_F">-->
                                                        <!--                                <input type="text" class="border_color input_add_F zws_li_p_w128" value="-->
                                                        <? //=$bargain['evaluate_charges'];?><!--" name="evaluate_charges" autocomplete="off">-->
                                                        <!--                               <strong class="zws_padd">元</strong>-->
                                                        <!--                                <div  class="zws_block errorBox"></div>-->
                                                        <!--                            </div>-->
                                                        <!--                        </span>-->
                                                        <!--                            </li>-->
                                                        <!--                            <script>-->
                                                        <!--                                $(function(){-->
                                                        <!--                                    $("#is_evaluate").live('click',function(){-->
                                                        <!--                                        var value = $(this).find('input:checked').val();-->
                                                        <!--                                        if(value == '0'){-->
                                                        <!--                                            $("#evaluate_charges").hide();-->
                                                        <!--                                        }else{-->
                                                        <!--                                            $("#evaluate_charges").show();-->
                                                        <!--                                        }-->
                                                        <!--                                    })-->
                                                        <!--                                })-->
                                                        <!--                            </script>-->
                                                        <!--                            --><?php //if($bargain['is_evaluate']==1){?>
                                                        <!--                                <script>-->
                                                        <!--                                    $(function(){-->
                                                        <!--                                        $("#evaluate_charges").show();-->
                                                        <!--                                    })-->
                                                        <!--                                </script>-->
                                                        <!--                            --><?php //}?>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <!--            第五行-->
                                        <tr>
                                            <td>
                                                <div class="zws_ht_w">
                                                    <ul>
                                                        <!--                                            <li>-->
                                                        <!--                                <span class="zws_border_span">-->
                                                        <!--                                    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>办理状态：</p>-->
                                                        <!--                                    <select class="border_color input_add_F zws_li_p_w142"-->
                                                        <!--                                            style="height:24px;line-height:24px;background:#FFF;" name="bargain_status">-->
                                                        <!--                                        --><?php //if ($config["bargain_status"]) {
                                                        //                                            foreach ($config["bargain_status"] as $key => $val) { ?>
                                                        <!--                                                <option value='-->
                                                        <? //= $key ?><!--' -->
                                                        <? //= $bargain['bargain_status'] == $key ? "selected" : "" ?><!-->
                                                        <? //= $val ?><!--</option>-->
                                                        <!--                                            --><?php //}
                                                        //                                        } ?>
                                                        <!--                                    </select>-->
                                                        <!--                                </span>-->
                                                        <!--                                            </li>-->
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
//
                                                                    select: function (event, ui) {
//
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
                                                                            var phone = ui.item.phone
                                                                            var id = ui.item.id;
                                                                            var agency_id = ui.item.agency_id;
                                                                            var company_id = ui.item.company_id;

                                                                            //操作
                                                                            $("input[name='broker_id_a']").val(id);
                                                                            $("input[name='broker_name_a']").val(brokername);
                                                                            $("input[name='broker_tel_a']").val(phone);

                                                                            $.ajax({
                                                                                url: "/bargain/get_agency_info_by_agencyid_companyid//",
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
                                                                });
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
                                             foreach ($config["signatory_company"] as $key => $val) { ?>
                                                 <p class="zws_radio_no <?= isset($bargain['signatory_company']) && $bargain['signatory_company'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                                     <input type="radio"
                                                            value=<?= $key; ?> name="signatory_company" <?= isset($bargain['signatory_company']) && $bargain['signatory_company'] == $key ? 'checked' : ''; ?>
                                                            style="display: none"></p>
                                             <?php }
                                         } ?>
                                    </div>
                                </span>
                                                        </li>
                                                        <li>
                                <span class="zws_border_span">
                                    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>权证人员：</p>
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
                                    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em "></b>理财人员：</p>
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
                                                       value="<?= $bargain['owner']; ?>" name="owner"
                                                       autocomplete="off">
                                                <span class="zws_block errorBox"></span>
                                            </b>
                                        </li>
                                        <li style="width: 380px">
                                            <strong><em class="resut_table_state_1"></em>身份证号：</strong>
                                            <b>
                                                <input type="text" class="border_color input_add_F zws_W128"
                                                       style="width: 270px"
                                                       value="<?= $bargain['owner_idcard']; ?>" name="owner_idcard"
                                                       autocomplete="off">
                                            </b>
                                        </li>
                                        <li>
                                            <strong><em class="resut_table_state_1">*</em>电话：</strong>
                                            <b>
                                                <input type="text" class="border_color input_add_F zws_W128"
                                                       value="<?= $bargain['owner_tel']; ?>" name="owner_tel"
                                                       autocomplete="off">
                                                <span class="zws_block errorBox"></span>
                                            </b>

                                        </li>

                                    </div>
                                    <div style="display:inline;">
                                        <li id="show_trust_a">
                                            <strong><em class="resut_table_state_1">*</em>公证委托：</strong>
                                            <b class="input_add_F">
                                                <p style="width: 30px;padding-right:0"
                                                   class="zws_radio_no yesOn"><?= isset($bargain['show_trust_a']) && $bargain['show_trust_a'] !== '0' ? '是' : '否'; ?>
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
                                                <input type="text" name="trust_name_a"
                                                       value="<?= $bargain['trust_name_a']; ?>"
                                                       class="border_color input_add_F zws_W128" autocomplete="off">
                                                <span class="zws_block errorBox"></span>
                                            </b>
                                        </li>
                                        <li id="trust_idcard_a">
                                            <strong><em class="resut_table_state_1">*</em>受托人证件号码：</strong>
                                            <b>
                                                <input type="text" name="trust_idcard_a"
                                                       value="<?= $bargain['trust_idcard_a']; ?>"
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
                                            <b>
                                                <input type="text" class="border_color input_add_F zws_W128"
                                                       value="<?= $bargain['customer']; ?>" name="customer"
                                                       autocomplete="off">
                                                <span class="zws_block errorBox"></span>
                                            </b>
                                        </li>
                                        <li style="width:380px;">
                                            <strong><em class="resut_table_state_1"></em>身份证号：</strong>
                                            <b><input type="text" class="border_color input_add_F zws_W128"
                                                      style="width: 270px"
                                                      value="<?= $bargain['customer_idcard']; ?>" name="customer_idcard"
                                                      autocomplete="off">
                                                <span class="zws_block errorBox"></span>
                                            </b>
                                        </li>
                                        <li>
                                            <strong><em class="resut_table_state_1">*</em>电话：</strong>
                                            <b>
                                                <input type="text" class="border_color input_add_F zws_W128"
                                                       value="<?= $bargain['customer_tel']; ?>" name="customer_tel"
                                                       autocomplete="off">
                                                <span class="zws_block errorBox"></span>
                                            </b>
                                        </li>

                                    </div>
                                    <div class=" " style="display:inline;">
                                        <li id="show_trust_b">
                                            <strong><em class="resut_table_state_1">*</em>公证委托：</strong>
                                            <b class="input_add_F">
                                                <p style="width: 30px;padding-right:0"
                                                   class="zws_radio_no yesOn">
                                                    <?= isset($bargain['show_trust_b']) && $bargain['show_trust_b'] !== '0' ? '是' : '否'; ?>
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
                                                <input type="text" name="trust_name_b"
                                                       value="<?= $bargain['trust_name_b']; ?>"
                                                       class="border_color input_add_F zws_W128" autocomplete="off">
                                                <span class="zws_block errorBox"></span>
                                            </b>
                                        </li>
                                        <li id="trust_idcard_b">
                                            <strong><em class="resut_table_state_1">*</em>受托人证件号码：</strong>
                                            <b>
                                                <input type="text" name="trust_idcard_b"
                                                       value="<?= $bargain['trust_idcard_b']; ?>"
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
                          foreach ($config["buy_type"] as $key => $val) {
                              if ($bargain['buy_type'] == $key) { ?>
                              <em class="zws_radio_no <?= $bargain['buy_type'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                  <input type="radio"
                                         value=<?= $key; ?> name="buy_type" <?= $bargain['buy_type'] == $key ? 'checked' : ''; ?>
                                         style="display: none"></em>
                          <?php }
                          }
                      } ?>
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
                         foreach ($config["loan_type"] as $key => $val) {
                             if ($bargain['loan_type'] == $key) { ?>
                             <em class="zws_radio_no <?= $bargain['loan_type'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                 <input type="radio"
                                        value=<?= $key; ?> name="loan_type" <?= $bargain['loan_type'] == $key ? 'checked' : ''; ?>
                                        style="display: none"></em>
                             <?php }
                         }
                     } ?>
                </span>
                                    </p>
                                    <p class="aad_pop_p_B10 payment_mortgage">
                                        <b class="zws_ip_W100">于</b>
                                        <span class="input_add_F">
                    <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
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
                            <!--                <div style="width:100%;clear:both;display:block;">-->
                            <!--                    <h4 class="h4 padding_size zws_h4_font">取款信息</h4>-->
                            <!--                </div>-->
                            <div class="sale_message_commission_detial" style="display:block;width:100%;float:left;">
                                <!--                    <div style="display:inline;width:100%;float:left;">-->
                                <!--                        <p class="aad_pop_p_B10">-->
                                <!--                            <b class="zws_ip_W100">于</b>-->
                                <!--                            <span class="input_add_F">-->
                                <!--                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"-->
                                <!--                                                           value="-->
                                <? //= $bargain['collect_condition'][0]; ?><!--"-->
                                <!--                                                           name="collect_condition1" autocomplete="off"></strong>-->
                                <!--                    </span>-->
                                <!--                            <b class="zws_ip_W100"></b>-->
                                <!---->
                                <!--                            <b class="zws_ip_W72">，划付房款￥</b>-->
                                <!--                            <span class="input_add_F">-->
                                <!--                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"-->
                                <!--                                                           value="-->
                                <? //= $bargain['collect_money'][0]; ?><!--"-->
                                <!--                                                           name="collect_money1" autocomplete="off"></strong>-->
                                <!--                    </span>-->
                                <!--                            <b class="zws_ip_W30">元整</b>-->
                                <!--                        </p>-->
                                <!---->
                                <!--                        <p class="aad_pop_p_B10">-->
                                <!--                            <b class="zws_ip_W100">于</b>-->
                                <!--                            <span class="input_add_F">-->
                                <!--                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"-->
                                <!--                                                           value="-->
                                <? //= $bargain['collect_condition'][1]; ?><!--"-->
                                <!--                                                           name="collect_condition2" autocomplete="off"></strong>-->
                                <!--                    </span>-->
                                <!--                            <b class="zws_ip_W100"></b>-->
                                <!---->
                                <!--                            <b class="zws_ip_W72">，划付房款￥</b>-->
                                <!--                            <span class="input_add_F">-->
                                <!--                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"-->
                                <!--                                                           value="-->
                                <? //= $bargain['collect_money'][1]; ?><!--"-->
                                <!--                                                           name="collect_money2" autocomplete="off"></strong>-->
                                <!--                    </span>-->
                                <!--                            <b class="zws_ip_W30">元整</b>-->
                                <!--                        </p>-->
                                <!---->
                                <!--                        <p class="aad_pop_p_B10">-->
                                <!--                            <b class="zws_ip_W100">于</b>-->
                                <!--                            <span class="input_add_F">-->
                                <!--                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"-->
                                <!--                                                           value="-->
                                <? //= $bargain['collect_condition'][2]; ?><!--"-->
                                <!--                                                           name="collect_condition3" autocomplete="off"></strong>-->
                                <!--                    </span>-->
                                <!--                            <b class="zws_ip_W100"></b>-->
                                <!---->
                                <!--                            <b class="zws_ip_W72">，划付房款￥</b>-->
                                <!--                            <span class="input_add_F">-->
                                <!--                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"-->
                                <!--                                                           value="-->
                                <? //= $bargain['collect_money'][2]; ?><!--"-->
                                <!--                                                           name="collect_money3" autocomplete="off"></strong>-->
                                <!--                    </span>-->
                                <!--                            <b class="zws_ip_W30">元整</b>-->
                                <!--                        </p>-->
                                <!---->
                                <!--                        <p class="aad_pop_p_B10">-->
                                <!--                            <b class="zws_ip_W100">于</b>-->
                                <!--                            <span class="input_add_F">-->
                                <!--                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"-->
                                <!--                                                           value="-->
                                <? //= $bargain['collect_condition'][3]; ?><!--"-->
                                <!--                                                           name="collect_condition4" autocomplete="off"></strong>-->
                                <!--                    </span>-->
                                <!--                            <b class="zws_ip_W100"></b>-->
                                <!---->
                                <!--                            <b class="zws_ip_W72">，划付房款￥</b>-->
                                <!--                            <span class="input_add_F">-->
                                <!--                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"-->
                                <!--                                                           value="-->
                                <? //= $bargain['collect_money'][3]; ?><!--"-->
                                <!--                                                           name="collect_money4" autocomplete="off"></strong>-->
                                <!--                    </span>-->
                                <!--                            <b class="zws_ip_W30">元整</b>-->
                                <!--                        </p>-->
                                <p class="aad_pop_p_B10">
                                    <b class="zws_ip_W100">交房时间：</b>
                                    <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                           style="width: 400px"
                                                           value="<?= $bargain['house_time']; ?>" name="house_time"
                                                           autocomplete="off"></strong>
                    </span>
                                </p>
                                <p class="aad_pop_p_B10">
                                    <b class="zws_ip_W100" style="width: 330px">税费合计（不含中介费，评估费，签约费）：</b>
                                    <span class="input_add_F">
                        <strong class="zws_ip_W150"><input type="text" class="border_color zws_ip_W130 input_add_F"
                                                           value="<?= $bargain['tax_pay_tatal']; ?>"
                                                           name="tax_pay_tatal" autocomplete="off"> 元</strong>
                    </span>
                                </p>
                                <p class="aad_pop_p_B10">
                                    <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>税费约定：</b>
                                    <span class="input_add_F" style="width:42%" id="tax_pay_type">
                         <?php if ($config["tax_pay_type"]) {
                             foreach ($config["tax_pay_type"] as $key => $val) {
                                 if ($bargain['tax_pay_type'] == $key) { ?>
                                 <em class="zws_radio_no <?= $bargain['tax_pay_type'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                     <input type="radio"
                                            value=<?= $key; ?> name="tax_pay_type" <?= $bargain['tax_pay_type'] == $key ? 'checked' : ''; ?>
                                            style="display: none"></em>
                                 <?php }
                             }
                         } ?>
                    </span>
                                    <em class="zws_ip_W150" id="tax_pay_appoint" style="display: none"><input
                                                class="border_color zws_ip_W130 input_add_F" type="text"
                                                value="<?= $bargain['tax_pay_type'] == '4' && isset($bargain['tax_pay_appoint']) ? $bargain['tax_pay_appoint'] : ''; ?>"
                                                name="tax_pay_appoint"></em>
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
                             foreach ($config["note_belong"] as $key => $val) {
                                 if ($bargain['note_belong'] == $key) { ?>
                                 <em class="zws_radio_no <?= $bargain['note_belong'] == $key ? 'yesOn' : ''; ?>"><?= $val; ?>
                                     <input type="radio"
                                            value=<?= $key; ?> name="note_belong" <?= $bargain['note_belong'] == $key ? 'checked' : ''; ?>
                                            style="display: none"></em>
                                 <?php }
                             }
                         } ?>
                    </span>
                                </p>
                                <dl>
                                    <dd>合同备注：</dd>
                                    <dt><textarea class="zws_textarea"
                                                  name="remarks"><?= $bargain['remarks']; ?></textarea>
                                    </dt>
                                </dl>
                                <dl>
                                    <dd>承办备注：</dd>
                                    <dt><textarea class="zws_textarea"
                                                  name="undertake_remarks"><?= $bargain['undertake_remarks']; ?></textarea>
                                    </dt>
                                </dl>
                            </div>
                        </div>
                        <!--保存和确认-->
                        <!--                <div style="padding-top:10px;clear: both;">-->
                        <!--                    <table width="100%">-->
                        <!--                        <tr>-->
                        <!--                            <td class="zws_center">-->
                        <!--                                --><?php //if ($id) { ?>
                        <!--                                    <input type="hidden" name="bargain_id" value="-->
                        <? //= $id ?><!--">-->
                        <!--                                    <input type="hidden" name="submit_flag" value="modify">-->
                        <!--                                --><?php //} else { ?>
                        <!--                                    <input type="hidden" name="submit_flag" value="add">-->
                        <!--                                --><?php //} ?>
                        <!---->
                        <!--                                --><?php //if ($auth['edit']['auth']) { ?>
                        <!--                                    <button type="submit" class="btn-lv1 btn-left">保存</button>-->
                        <!--                                --><?php //} else { ?>
                        <!--                                    <button type="button" class="btn-lv1 btn-left" onclick="purview_none();">保存</button>-->
                        <!--                                --><?php //} ?>
                        <!---->
                        <!--                                <button type="button" class="btn-hui1" onclick="history.go(-1);">取消</button>-->
                        <!--                            </td>-->
                        <!--                        </tr>-->
                        <!--                    </table>-->
                        <!--                </div>-->
                    </form>
                </div>
                <div style="clear:both;"></div>
                <!--成交细节-->
                <div id="js_search_box" class="shop_tab_title  scr_clear top_Marign"
                     style="float:left;display:inline;width:99%;padding-right:1%;background:#FFF;padding-top:10px;margin:0;">
                    <!--                    <a href="javascript:void(0);" class="bargain_filing link link_on" style="margin-left: 16px;"-->
                    <!--                       id="transfer_step" data="/bargain/bargain_transfer_manage/-->
                    <? //= $bargain['id']; ?><!--">过户流程<span-->
                    <!--                                class="iconfont hide"></span></a>-->
                    <a href="javascript:void(0);" class="link bargain_filing link_on" id="replace_flow"
                       data="/bargain/bargain_replace_manage/<?= $bargain['id']; ?>">代收付<span
                                class="iconfont hide"></span></a>
                    <a href="javascript:void(0);" class="link bargain_filing" id="replace_tax_flow"
                       data="/bargain/bargain_replace_tax_manage/<?= $bargain['id']; ?>">税费<span
                                class="iconfont hide"></span></a>
                    <a href="/bargain/finance_print/<?php echo $bargain['id']; ?>" target="_blank" class="btn-lv1 fr"
                       id="" style="margin-left:16px;" <span style="margin-right:16px;">打印收付</span></a>

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
                            src="/bargain/bargain_replace_manage/<?= $bargain['id']; ?>" id="iframepage"
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
            })

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
