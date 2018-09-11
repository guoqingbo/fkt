<script>
    window.parent.addNavClass(3);
</script>
<div class="bargain-wrap clearfix">
    <!--left 菜单部分-->
    <div class="tab-left"><?= $user_tree_menu ?></div>
    <!--右侧内容部分-->
    <div class="forms_scroll h90" style="overflow-y:hidden;">
        <div class="shop_tab_title scr_clear" id="js_search_box" style="margin:0 15px  10px  15px;">
            <?php if ($type != 0) { ?>
                <a href="javascript:void(0);" class="btn-lv fr"
                   <?php if ($auth['add']['auth']){ ?>onclick="location.href='/bargain/bargain_add/<?= $type; ?>';return false;"
                   <?php }else{ ?>onclick="purview_none();"<?php } ?>><span>+ 录入</span></a>
            <?php } ?>
            <a href="/bargain/deal_manage/0" class="link <?= $type == 0 ? 'link_on' : '' ?>"><span
                        class="iconfont hide"></span>全部</a>
            <a href="/bargain/deal_manage/2" class="link <?= $type == 2 ? 'link_on' : '' ?>"><span
                        class="iconfont hide"></span>二手</a>
            <a href="/bargain/deal_manage/1" class="link <?= $type == 1 ? 'link_on' : '' ?>"><span
                        class="iconfont hide"></span>一手</a>
            <a href="/bargain/deal_manage/3" class="link <?= $type == 3 ? 'link_on' : '' ?>"><span
                        class="iconfont hide"></span>托管</a>
        </div>
        <!-- 上部菜单选项，按钮-->
        <form name="search_form" id="subform" method="post" action="">
            <div class="search_box clearfix" id="js_search_box_02">
                <div style="width:100%;display:block; float:left; display:inline;">
                    <div class="fg_box">
                        <p class="fg fg_tex">关键字：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="keyword_type" value="<?= $post_param['keyword_type']; ?>">
                                <option value="">请选择</option>
                                <?php if ($config['keyword_type']) {
                                    foreach ($config['keyword_type'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?php if (!empty($post_param['keyword_type'])) {echo $post_param['keyword_type'] == $key ? 'selected' : '';} else {echo $key == 5 ? 'selected' : '';} ?> ><?= $val; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <div class="fg">
                            <input type="text" class="" autocomplete="off" name="keyword"
                                   value="<?= $post_param['keyword']; ?>">
                        </div>
                    </div>
                    <div class="fg_box">
                        <p class="fg fg_tex">门店：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <input type="text" name="agency_name_a" value="<?= $post_param['agency_name_a']; ?>"
                                   class="border_color input_add_F zws_W128" autocomplete="off">
                            <input name="agency_id_a" value="<?= $post_param['agency_id_a']; ?>" type="hidden">
                            <span class="zws_block errorBox"></span>
                        </div>
                    </div>
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
                                    $("input[name='agency_id_a']").val("");
                                    $.ajax({
                                        url: "/contract/get_agency_info_by_kw/",
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
                        $("#sign_department").change(function () {
                            var department_id = $('#sign_department').val();
                            if (department_id) {
                                $.ajax({
                                    url: "/bargain/signatory_list",
                                    type: "GET",
                                    dataType: "json",
                                    data: {
                                        department_id: department_id
                                    },
                                    success: function (data) {
                                        var html = "<option value=''>请选择</option>";
                                        if (data['result'] == 1) {
                                            for (var i in data['list']) {
                                                html += "<option value='" + data['list'][i]['signatory_id'] + "'>" + data['list'][i]['truename'] + "</option>";
                                            }
                                        }
                                        $('#sign_signatory').html(html);
                                    }
                                })
                            } else {
                                $('#sign_signatory').html("<option value=''>请选择</option>");
                            }
                        })
                    </script>
                    <div class="fg_box">
                        <p class="fg fg_tex">成交类别：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="bargain_type" value="<?= $post_param['bargain_type']; ?>">
                                <option value="">请选择</option>
                                <?php if ($config['bargain_type']) {
                                    foreach ($config['bargain_type'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['bargain_type'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <p class="fg fg_tex">审核状态：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="is_check">
                                <option value="">请选择</option>
                                <?php if ($config['is_check']) {
                                    foreach ($config['is_check'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['is_check'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div style="width:100%;display:block; float:left; display:inline;">
                    <div class="fg_box">
                        <p class="fg fg_tex">日期：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="date_type">
                                <option value="">请选择</option>
                                <?php if ($config['date_type']) {
                                    foreach ($config['date_type'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['date_type'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <div class="fg">
                            <input type="text" class="fg-time"
                                   onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off"
                                   name="start_time" value="<?= $post_param['start_time']; ?>">
                        </div>
                        <div class="fg fg_tex03">—</div>
                        <div class="fg fg_tex03">
                            <input type="text" class="fg-time"
                                   onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off"
                                   name="end_time" value="<?= $post_param['end_time']; ?>">
                            &nbsp;&nbsp;<span style="font-weight:bold;color:red;" class="reminder"></span>
                        </div>
                    </div>
                    <div class="fg_box">
                        <p class="fg fg_tex">收款状态：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="collect_status">
                                <option value="">请选择</option>
                                <?php if ($config['collect_status']) {
                                    foreach ($config['collect_status'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['collect_status'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <p class="fg fg_tex">付款方式：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="buy_type">
                                <option value="">请选择</option>
                                <?php if ($config["buy_type"]) {
                                    foreach ($config['buy_type'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['buy_type'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div style="width:100%;display:block; float:left; display:inline;">
                    <div class="fg_box">
                        <p class="fg fg_tex">流程完结日期：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="stage_id">
                                <option value="">选择流程</option>
                                <?php if ($stage) {
                                    foreach ($stage as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['stage_id'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <div class="fg">
                            <input type="text" class="fg-time"
                                   onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off"
                                   name="transfer_start_time"
                                   value="<?= $post_param['transfer_start_time']; ?>">
                        </div>
                        <div class="fg fg_tex03">—</div>
                        <div class="fg fg_tex03">
                            <input type="text" class="fg-time"
                                   onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off"
                                   name="transfer_end_time"
                                   value="<?= $post_param['transfer_end_time']; ?>">
                            <span style="font-weight:bold;color:red;" class="reminder"></span>
                        </div>
                    </div>
                    <script>
                        $("select[name='date']").change(function () {
                            if ($(this).val() == '0') {
                                $("#datetime").show();
                            } else {
                                $("input[name='start_time']").val('');
                                $("input[name='end_time']").val('');
                                $("#datetime").hide();
                            }
                        });
                    </script>
                    <div class="fg_box">
                        <p class="fg fg_tex">监管银行：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="loan_bank">
                                <option value="">请选择</option>
                                <?php if ($loan_bank) {
                                    foreach ($loan_bank as $key => $val) { ?>
                                        <option value="<?= $val['id']; ?>" <?= $post_param['loan_bank'] == $val["id"] ? 'selected' : '' ?>><?= $val["bank_name"] . " " . $val["bank_deposit"] . " " . $val["card_no"]; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <p class="fg fg_tex">贷款方式：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="loan_type">
                                <option value="">请选择</option>
                                <?php if ($config["loan_type"]) {
                                    foreach ($config['loan_type'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['loan_type'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <input type="hidden" name="page" value="1">
                        <input type="hidden" name="is_submit" value="1">
                        <div class="fg"><a href="javascript:void(0);"
                                           onclick="$('#subform :input[name=page]').val('1');$('#subform').attr('action', '/bargain/deal_manage/<?= $type ?>/');form_submit();return false;"
                                           class="btn"><span class="btn_inner">搜索</span></a></div>
                        <div class="fg"><a href="javascript:void(0);"
                                           onclick="$('#subform').attr('action', '/bargain/exportbargain/<?= $type ?>/');form_submit();$('#subform').attr('action', '');return false;"
                                           class="btn"><span class="btn_inner">导出</span></a></div>
                        <div class="fg"><a href="/bargain/deal_manage/<?= $type; ?>" class="reset">重置</a></div>
                    </div>
                </div>
            </div>

            <script>
                $(function () {
                    document.onkeydown = function (e) { //enter
                        var ev = document.all ? window.event : e;
                        if (ev.keyCode == 13) {
                            $('#subform :input[name=page]').val('1');
                            form_submit();
                            return false;
                        }
                    }
                });
            </script>
        </form>
        <!-- 上部菜单选项，按钮---end-->

        <div class="fun_btn clearfix count_info_border" id="js_fun_btn">
            <div class="get_page">
                <?php if (isset($page_list) && $page_list != '') {
                    echo $page_list;
                } ?>
            </div>
        </div>
        <div class="table_all">
            <div class="title" id="js_title">
                <table class="table" style="*+width:98.5%;_width:98.5%;">
                    <?php if ($type == 0) { ?>
                        <tr>
                            <td class="c_table_title_3">成交类别</td>
                            <td class="c_table_title_3">成交编号</td>
                            <td class="c_table_title_3">签约日期</td>
                            <td class="c_table_title_3">楼盘名称</td>
                            <td class="c_table_title_3">物业地址</td>
                            <td class="c_table_title_3">成交价（元）</td>
                            <td class="c_table_title_3">成交门店</td>
                            <td class="c_table_title_3">签约人员</td>
                            <td class="c_table_title_3">操作</td>
                        </tr>
                    <?php } elseif ($type == 1) { ?>
                        <tr>
                            <td class="c_table_title_3">成交编号</td>
                            <td class="c_table_title_3">收件日期</td>
                            <td class="c_table_title_3">买方姓名</td>
                            <td class="c_table_title_3">物业地址</td>
                            <td class="c_table_title_3">开发商</td>
                            <td class="c_table_title_3">代办类型</td>
                            <td class="c_table_title_3">办证人员</td>
                            <td class="c_table_title_3">办理状态</td>
                            <td class="c_table_title_3">操作</td>
                        </tr>
                    <?php } elseif ($type == 2) { ?>
                        <tr>
                            <td class="c_table_title_3">成交编号</td>
                            <td class="c_table_title_3">签约日期</td>
                            <td class="c_table_title_3">物业地址</td>
                            <td class="c_table_title_3">卖方姓名</td>
                            <td class="c_table_title_3">买方姓名</td>
                            <td class="c_table_title_3">成交门店</td>
                            <td class="c_table_title_3">签约人员</td>
                            <td class="c_table_title_3">权证人员</td>
                            <td class="c_table_title_3">办理状态</td>
                            <td class="c_table_title_3">操作</td>
                        </tr>
                    <?php } elseif ($type == 3) { ?>
                        <tr>
                            <td class="c_table_title_3">成交类别</td>
                            <td class="c_table_title_3">楼盘名称</td>
                            <td class="c_table_title_3">成交编号</td>
                            <td class="c_table_title_3">物业地址</td>
                            <td class="c_table_title_3">签约日期</td>
                            <td class="c_table_title_3">房源方</td>
                            <td class="c_table_title_3">卖方姓名</td>
                            <td class="c_table_title_3">客源方</td>
                            <td class="c_table_title_3">买方姓名</td>
                            <td class="c_table_title_3">操作</td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <input type="hidden" id="bargain_id">
            <!--列表-->
            <div class="inner" id="zws_js_inner_H" style="height: 686px !important;border-bottom:1px dashed #e8e8e8;">
                <?php if ($list) {
                    foreach ($list as $key => $val) { ?>
                        <table class="table list-table cont_list_bottom_solid" align="center"
                               style="border-bottom:1px solid #e5e5e5;*+width:98.5%;_width:98.5%;">
                            <?php if ($type == 0) { ?>
                                <tr id="deal_manage">
                                    <td class="c_table_title_3 "><?= $config['bargain_type'][$val['bargain_type']]; ?></td>
                                    <td class="c_table_title_3 "><?= $val['number']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['signing_time'] ? date('Y-m-d', $val['signing_time']) : ""; ?></td>
                                    <td class="c_table_title_3 "><?= $val['block_name']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['house_addr']; ?></td>

                                    <td class="c_table_title_3 "><?= intval($val['price']); ?></td>
                                    <td class="c_table_title_3 "><?= $val['agency_name_a']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['signatory_name']; ?></td>
                                    <td class="c_table_title_3 ">
                                        &nbsp;<a
                                                href="javascript:void(0);"
                                                onclick="edit_this(<?= $val['id']; ?>,<?= $val['type']; ?>);">编辑</a>
                                        |
                                        <a href="javascript:void(0);"
                                           onclick="location.href='/bargain/bargain_look/<?= $val['id']; ?>';return false;">查看</a>
                                    </td>
                                </tr>
                            <?php } elseif ($type == 1) { ?>
                                <tr id="deal_manage">
                                    <td class="c_table_title_3 "><?= $val['number']; ?></td>
                                    <td class="c_table_title_3 "><?= date('Y-m-d', $val['receipt_time']); ?></td>
                                    <td class="c_table_title_3 "><?= $val['customer']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['house_addr']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['developer']; ?></td>
                                    <td class="c_table_title_3 "><?= $config['agent_type'][$val['agent_type']]; ?></td>
                                    <td class="c_table_title_3 "><?= $val['warrant_inside_name']; ?></td>
                                    <td class="c_table_title_3 "><?= $config['bargain_status'][$val['bargain_status']]; ?></td>
                                    <td class="c_table_title_3 ">
                                        &nbsp;<a
                                                href="javascript:void(0);" <?php if ($auth['edit']['auth']) { ?> onclick="location.href='/bargain/modify_bargain/<?= $val['type']; ?>/<?= $val['id']; ?>';return false;"  <?php } else { ?>onclick="purview_none();" <?php } ?>>编辑</a>

                                        |
                                        <a href="javascript:void(0);"
                                           onclick="location.href='/bargain/bargain_look/<?= $val['id']; ?>';return false;">查看</a>
                                    </td>
                                </tr>
                            <?php } elseif ($type == 2) { ?>
                                <tr id="deal_manage">
                                    <td class="c_table_title_3 "><?= $val['number']; ?></td>
                                    <td class="c_table_title_3 "><?= date('Y-m-d', $val['signing_time']); ?></td>
                                    <td class="c_table_title_3 "><?= $val['house_addr']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['owner']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['customer']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['agency_name_a']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['signatory_name']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['warrant_inside_name']; ?></td>
                                    <td class="c_table_title_3 "><?= $config['bargain_status'][$val['bargain_status']]; ?></td>
                                    <td class="c_table_title_3 ">
                                        &nbsp;<a
                                                href="javascript:void(0);" <?php if ($auth['edit']['auth']) { ?> onclick="location.href='/bargain/modify_bargain/<?= $val['type']; ?>/<?= $val['id']; ?>';return false;"  <?php } else { ?>onclick="purview_none();" <?php } ?>>编辑</a>

                                        |
                                        <a href="javascript:void(0);"
                                           onclick="location.href='/bargain/bargain_look/<?= $val['id']; ?>';return false;">查看</a>
                                    </td>
                                </tr>
                            <?php } elseif ($type == 3) { ?>
                                <tr id="deal_manage">
                                    <td class="c_table_title_3 "><?= $config['bargain_type'][$val['bargain_type']]; ?></td>
                                    <td class="c_table_title_3 "><?= $val['block_name']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['number']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['house_addr']; ?></td>
                                    <td class="c_table_title_3 "><?= date('Y-m-d', $val['signing_time']); ?></td>
                                    <td class="c_table_title_3 "><?= $val['agency_name_a']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['owner']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['department_name_b']; ?></td>
                                    <td class="c_table_title_3 "><?= $val['customer']; ?></td>
                                    <td class="c_table_title_3 ">
                                        &nbsp;<a
                                                href="javascript:void(0);" <?php if ($auth['edit']['auth']) { ?> onclick="location.href='/bargain/modify_bargain/<?= $val['type']; ?>/<?= $val['id']; ?>';return false;"  <?php } else { ?>onclick="purview_none();" <?php } ?>>编辑</a>

                                        |
                                        <a href="javascript:void(0);"
                                           onclick="location.href='/bargain/bargain_look/<?= $val['id']; ?>';return false;">查看</a>
                                    </td>
                                </tr>
                            <?php } ?>

                        </table>
                    <?php }
                } else { ?>
                    <table class="table list-table cont_list_bottom_solid" align="center">
                        <tr>
                            <td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td>
                        </tr>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    window.onload = function () {
        $(function () {
            $("#js_inner").css("height", ($("#js_inner").height() - 45) + "px");
            $("#zws_js_inner_H").css("height", ($(".tab-left").height() - 228) + "px");
            //alert($("#js_search_box_02").height());
            //console.log($("#js_inner").height()-45);
            //

        })

    }

    $(window).resize(function () {

        $("#js_inner").css("height", ($("#js_inner").height() - 45) + "px");
        $("#zws_js_inner_H").css("height", ($(".tab-left").height() - 228) + "px");
    })

</script>

<div id="js_pop_warning" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要删除此成交吗？<br/>确认删除后不可恢复。
                </p>
                <button type="button" class="btn JS_Close" onclick="delete_this();">确定</button>
                <button type="button" class="btn btn_none JS_Close">取消</button>
            </div>
        </div>
    </div>
</div>

<div id="js_pop_cancel" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要作废此成交吗？<br/>确认作废后不可恢复。
                </p>
                <button type="button" class="btn JS_Close" onclick="cancel_this();">确定</button>
                <button type="button" class="btn btn_none JS_Close">取消</button>
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
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button" onclick="location=location">确定</button>
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
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt1">成交报备添加成功！</p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
        </div>
    </div>
</div>

<!--详情弹跳页-->
<div id="js_pop_box_g" class="iframePopBox" style="width:816px; height:540px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<img src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?b=mls_guli/js/v1.0&f=openWin.js,house.js,backspace.js "></script>

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
        });
        $('.table_all .inner tr').find("a").click(function (event) {
            event.stopPropagation();
        });
    });

    //通过参数判断是否可以被提交
    function form_submit() {
        var is_submit = $("input[name='is_submit']").val();
        if (is_submit == 1) {
            $('#subform').submit();
        }
    }

    //删除该条成交
    function delete_this() {
        var bargain_id = $('#bargain_id').val();
        $.ajax({
            url: "/bargain/del",
            type: "GET",
            dataType: "json",
            data: {
                id: bargain_id
            },
            success: function (data) {
                if (data['result'] == 1) {
                    $('#js_prompt').text('成交已删除！');
                    openWin('js_pop_success');
                } else {
                    $('#js_prompt1').text('成交删除失败！');
                    openWin('js_pop_false');
                }
            }
        })
    }

    //删除该条成交
    function cancel_this() {
        var bargain_id = $('#bargain_id').val();
        $.ajax({
            url: "/bargain/cancel",
            type: "GET",
            dataType: "json",
            data: {
                id: bargain_id
            },
            success: function (data) {
                if (data['result'] == 1) {
                    $('#js_prompt').text('成交作废成功！');
                    openWin('js_pop_success');
                } else {
                    $('#js_prompt').text('成交作废失败！');
                    openWin('js_pop_false');
                }
            }
        })
    }

    //操作成功之后刷新当前页，如果没有数据，返回上一页
    function check_list(page, type) {
        $.post(
            '/bargain/check_list1',
            {
                'page': page,
                'type': type
            },
            function (data) {
                if (data == '0') {
                    if (page > 1) {
                        page = page - 1;
                    }
                }
                $('#search_form :input[name=page]').val(page);
                form_submit();
                return false;
            }
        );
    }
    //编辑该条成交
    function edit_this(id, type) {
        $.ajax({
            url: "/bargain/purview_check",
            type: "POST",
            dataType: "json",
            data: {
                type: type
            },
            success: function (data) {
                if (data['edit']['auth']) {
                    location.href = '/bargain/modify_bargain/' + type + '/' + id;
                } else {
                    purview_none()
                }
            }
        })
    }
</script>
