<script>
    window.parent.addNavClass(21);
</script>
<div class="contract-wrap clearfix">
    <!--left 菜单部分-->
    <div class="tab-left"><?= $user_tree_menu ?></div>
    <!--右侧内容部分-->
    <div class="forms_scroll h90" style="overflow-y:hidden;">

        <!--    <div class="shop_tab_title scr_clear" id="js_search_box" style="margin:0 15px  10px  15px;">-->
        <!--	    <a href="/contract/contract_list/1" class="link -->
        <? //=$type==1?'link_on':''?><!--"><span class="iconfont hide"></span>出售</a>-->
        <!--	    <a href="/contract/contract_list/2" class="link -->
        <? //=$type==2?'link_on':''?><!--"><span class="iconfont hide"></span>出租</a>-->
        <!--	</div>-->
        <!-- 上部菜单选项，按钮-->
        <form name="search_form" id="subform" method="post" action="">
            <div class="search_box clearfix" id="js_search_box_02">
                <div style="width:100%;display:block; float:left; display:inline;">
                    <div class="fg_box">
                        <p class="fg fg_tex">成交编号：</p>
                        <div class="fg">
                            <input type="text" value="<?= $post_param['number']; ?>"
                                   class="input w90 ui-autocomplete-input" autocomplete="off" name="number">
                        </div>
                    </div>
                    <div class="fg_box">
                        <p class="fg fg_tex">楼盘名称：</p>
                        <div class="fg">
                            <input type="text" name="block_name" id="block_name"
                                   value="<?= $post_param['block_name']; ?>" class="input w120 ui-autocomplete-input"
                                   autocomplete="off"><span role="status" aria-live="polite"
                                                            class="ui-helper-hidden-accessible"></span>
                            <input name="block_id" id="block_id" value="<?= $post_param['block_id']; ?>" type="hidden">
                        </div>
                    </div>
                    <script type="text/javascript">
                        $(function () {
                            $.widget("custom.autocomplete", $.ui.autocomplete, {
                                _renderItem: function (ul, item) {
                                    if (item.id > 0) {
                                        return $("<li>")
                                            .data("item.autocomplete", item)
                                            .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">' + item.label + '</span><span class="ui_district">' + item.districtname + '</span><span class="ui_address">' + item.address + '</span></a>')
                                            .appendTo(ul);
                                    } else {
                                        return $("<li>")
                                            .data("item.autocomplete", item)
                                            .append('<a class="ui-corner-all" tabindex="-1">' + item.label + '</a>')
                                            .appendTo(ul);
                                    }
                                }
                            });
                            $("#block_name").autocomplete({
                                source: function (request, response) {
                                    var term = request.term;
                                    $("#block_id").val("");
                                    $.ajax({
                                        url: "/community/get_cmtinfo_by_kw/",
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
                                        var blockname = ui.item.label;
                                        var id = ui.item.id;
                                        var streetid = ui.item.streetid;
                                        var streetname = ui.item.streetname;
                                        var dist_id = ui.item.dist_id;
                                        var districtname = ui.item.districtname;
                                        var address = ui.item.address;

                                        //操作
                                        $("#block_id").val(id);
                                        $("#block_name").val(blockname);
                                        removeinput = 2;
                                    } else {
                                        removeinput = 1;
                                    }
                                },
                                close: function (event) {
                                    if (typeof(removeinput) == 'undefined' || removeinput == 1) {
                                        $("#block_name").val("");
                                        $("#block_id").val("");
                                    }
                                }
                            });
                        });
                    </script>
                    <div class="fg_box">
                        <p class="fg fg_tex">姓名：</p>

                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="owner_type">
                                <option value="">请选择</option>
                                <option value="1" <?= $post_param['owner_type'] == 1 ? 'selected' : ''; ?>>业主姓名</option>
                                <option value="2" <?= $post_param['owner_type'] == 2 ? 'selected' : ''; ?>>客户姓名</option>
                            </select>
                        </div>
                        <div class="fg">
                            <input type="text" value="<?= $post_param['owner_name']; ?>"
                                   class="input w90 ui-autocomplete-input" autocomplete="off" name="owner_name">
                        </div>
                    </div>
                    <div class="fg_box">
                        <p class="fg fg_tex">门店：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="agency_id_a" value="<?= $post_param['agency_id_a']; ?>"
                                    id="sign_agency">
                                <?php foreach ($agencys as $key => $val) { ?>
                                    <option value="<?= $val['id']; ?>" <?php if ($val['id'] == $post_param['agency_id_a']) {
                                        echo 'selected';
                                    } ?>><?= $val['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <p class="fg fg_tex">经纪人：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="broker_id_a" value="<?= $post_param['broker_id_a']; ?>"
                                    id="sign_broker">
                                <?php if (is_full_array($brokers)) {
                                    foreach ($brokers as $val) {
                                        ?>
                                        <option value="<?= $val['broker_id']; ?>" <?php if ($val['broker_id'] == $post_param['broker_id_a']) {
                                            echo 'selected';
                                        } ?>><?= $val['truename']; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <script>
                        $("#sign_agency").change(function () {
                            var agency_id = $('#sign_agency').val();
                            if (agency_id) {
                                $.ajax({
                                    url: "/contract/broker_list",
                                    type: "GET",
                                    dataType: "json",
                                    data: {
                                        agency_id: agency_id
                                    },
                                    success: function (data) {
                                        var html = "<option value=''>请选择</option>";
                                        if (data['result'] == 1) {
                                            for (var i in data['list']) {
                                                html += "<option value='" + data['list'][i]['broker_id'] + "'>" + data['list'][i]['truename'] + "</option>";
                                            }
                                        }
                                        $('#sign_broker').html(html);
                                    }
                                })
                            } else {
                                $('#sign_broker').html("<option value=''>请选择</option>");
                            }
                        })
                    </script>
                </div>
                <div class="100%;display:block;">
                    <div class="fg_box">
                        <p class="fg fg_tex">日期：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="datetype">
                                <?php if ($type == 1) {
                                    foreach ($config['datetype'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['datetype'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } else {
                                    foreach ($config['datetype_r'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['datetype'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select self_w100" name="date">
                                <option value="0" <?= $post_param['date'] == 0 ? 'selected' : '' ?> >自定义日期</option>
                                <option value="1" <?= $post_param['date'] == 1 ? 'selected' : '' ?> >今天</option>
                                <option value="2" <?= $post_param['date'] == 2 ? 'selected' : '' ?> >7天</option>
                                <option value="3" <?= $post_param['date'] == 3 ? 'selected' : '' ?> >一个月内</option>
                                <option value="4" <?= $post_param['date'] == 4 ? 'selected' : '' ?> >三个月内</option>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box <?= $post_param['date'] > 0 ? 'hide' : ''; ?>" id="datetime">
                        <div class="fg">
                            <input type="text" class="fg-time"
                                   onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off"
                                   name="start_time" onchange="check_time();" value="<?= $post_param['start_time']; ?>">
                        </div>
                        <div class="fg fg_tex03">—</div>
                        <div class="fg fg_tex03">
                            <input type="text" class="fg-time"
                                   onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off"
                                   name="end_time" onchange="check_time();" value="<?= $post_param['end_time']; ?>">
                            &nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="time_reminder"></span>
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
                        function check_time() {
                            var timemin = $("input[name='start_time']").val();	//最小面积
                            var timemax = $("input[name='end_time']").val();	//最大面积

                            if (!timemin && !timemax) {
                                $("#time_reminder").html("");
                                $("input[name='is_submit']").val('1');
                            }

                            //最小面积timemin 必须小于 最大面积timemax
                            if (timemin && timemax) {
                                if (timemin > timemax) {
                                    $("#time_reminder").html("时间筛选区间输入有误！");
                                    $("input[name='is_submit']").val('0');
                                    return;
                                } else {
                                    $("#time_reminder").html("");
                                    $("input[name='is_submit']").val('1');
                                }
                            }
                        }
                    </script>
                    <div class="fg_box">
                        <p class="fg fg_tex">状态：</p>
                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="is_check">
                                <option value="">请选择</option>
                                <?php if ($type == 0) {
                                    foreach ($config['cont_status'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['is_check'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } else {
                                    foreach ($config['cont_status_r'] as $key => $val) { ?>
                                        <option value="<?= $key; ?>" <?= $post_param['is_check'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <input type="hidden" name="page" value="1">
                        <input type="hidden" name="is_submit" value="1">
                        <input type="hidden" name="orderby_id" value="<?= $post_param['orderby_id']; ?>">
                        <div class="fg"><a href="javascript:void(0);"
                                           onclick="$('#subform :input[name=page]').val('1');$('#subform').attr('action', '/contract/transfer_list');form_submit();return false;"
                                           class="btn"><span class="btn_inner">搜索</span></a></div>
                        <div class="fg"><a href="/contract/transfer_list/<?= $type; ?>" class="reset">重置</a></div>
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
                    <tr>
                        <td class="c_table_title_1">
                            <div class="info">合同编号</div>
                        </td>
                        <td class="c_table_title_2" style="width:18%;">
                            <div class="info">成交房源</div>
                        </td>
                        <td class="c_table_title_3">
                            <div class="info">业主姓名</div>
                        </td>
                        <td class="c_table_title_3">
                            <div class="info">客户姓名</div>
                        </td>
                        <td class="c_table_title_3">
                            <div class="info">面积<br/>(m&sup2;)</div>
                        </td>
                        <td class="c_table_title_3">
                            <div class="info">成交价<br/>（元）</div>
                        </td>
                        <td class="c_table_title_4" style="width:10.5%">
                            <div class="info">门店</div>
                        </td>
                        <td class="c_table_title_3">
                            <div class="info">签约人</div>
                        </td>
                        <td class="c_table_title_6">
                            <div class="info">签约日期</div>
                        </td>
                        <td class="c_table_title_3">
                            <div class="info">状态</div>
                        </td>
                    </tr>
                </table>
            </div>

            <input type="hidden" id="contract_id">
            <!--列表-->
            <div class="inner" id="zws_js_inner_H" style="height: 686px !important;border-bottom:1px dashed #e8e8e8;">
                <?php if ($list) {
                    foreach ($list as $key => $val) { ?>
                        <table class="table list-table cont_list_bottom_solid" align="center"
                               style="border-bottom:1px solid #e5e5e5;*+width:98.5%;_width:98.5%;">
                            <tr id="contract_list"
                                onclick="location.href='/contract/transfer_detail/<?= $val['id']; ?>';return false;">
                                <td class="c10 cont_list_right_dashed" style="text-align:left;padding:0 15px;">
                                    &nbsp;<?= $val['number']; ?></td>
                                <td style="width:77%">
                                    <table class="table inner-table">
                                        <tr class="first cont_list_bottom_dashed ">
                                            <td class="c_table_body_1 zws_border_dashed"
                                                style="text-align:left;padding:0 10px;"><?= $val['house_addr']; ?></td>
                                            <td class="c_table_body_2 zws_border_dashed"><?= $val['owner']; ?></td>
                                            <td class="c_table_body_2 zws_border_dashed"><?= $val['customer']; ?></td>
                                            <td class="c_table_body_3 zws_border_dashed"><?= $val['buildarea']; ?></td>
                                            <td class="c_table_body_4 zws_border_dashed"><?= $val['price']; ?></td>
                                            <td class="c_table_body_5 zws_border_dashed"
                                                style="width:15%"><?= $val['agency_name_a']; ?></td>
                                            <td class="c_table_body_2 zws_border_dashed"><?= $val['signatory_name']; ?></td>
                                            <td class="c_table_body_3 zws_border_dashed"><?= date('Y-m-d', $val['signing_time']); ?></td>
                                            <td class="zws_border_dashed"
                                                style="padding:0 6px;"><?= $config['cont_status'][$val['is_check']]; ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
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
                <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要删除此合同吗？<br/>确认删除后不可恢复。
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
                <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要作废此合同吗？<br/>确认作废后不可恢复。
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
                            <img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/dakacg.gif"></td>
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
                            <img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;">预约签约新增失败！</p>
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

<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js "></script>

<script>
    $(function () {
        function re_width() {
            var h1 = $(window).height();
            var w1 = $(window).width() - 180;
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

    //删除该条合同
    function delete_this() {
        var contract_id = $('#contract_id').val();
        $.ajax({
            url: "/contract/del",
            type: "GET",
            dataType: "json",
            data: {
                id: contract_id
            },
            success: function (data) {
                if (data['result'] == 1) {
                    $('#js_prompt').text('合同已删除！');
                    openWin('js_pop_success');
                } else {
                    $('#js_prompt').text('合同删除失败！');
                    openWin('js_pop_false');
                }
            }
        })
    }

    //删除该条合同
    function cancel_this() {
        var contract_id = $('#contract_id').val();
        $.ajax({
            url: "/contract/cancel",
            type: "GET",
            dataType: "json",
            data: {
                id: contract_id
            },
            success: function (data) {
                if (data['result'] == 1) {
                    $('#js_prompt').text('合同作废成功！');
                    openWin('js_pop_success');
                } else {
                    $('#js_prompt').text('合同作废失败！');
                    openWin('js_pop_false');
                }
            }
        })
    }

    //操作成功之后刷新当前页，如果没有数据，返回上一页
    function check_list(page, type) {
        $.post(
            '/contract/check_list1',
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

    //合同列表页 排序
    function list_order(id) {
        var orderby_id = $("input[name='orderby_id']").val();
        var other_id = id + 1;
        if (orderby_id == id) {
            $("input[name='orderby_id']").val(other_id);
            $("#subform").submit();
        }
        else {
            $("input[name='orderby_id']").val(id);
            $("#subform").submit();
        }
    }
</script>
