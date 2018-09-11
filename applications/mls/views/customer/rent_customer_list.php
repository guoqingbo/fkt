<!--页面部分-->
<script>
    window.parent.addNavClass(3);
</script>
<body>
<form action='<?php echo MLS_URL; ?>/rent_customer/manage' method='post' name='search_form' id='search_form'>
    <input type="hidden" class="input w40" id="myoffset2" name="myoffset" value="0"/>
    <input type="hidden" class="input w40" id="mylimit2" name="mylimit" value="10"/>
    <div class="tab_box" id="js_tab_box">
        <?php echo $user_menu; ?>
        <label class="label_left_t">
            <input type="checkbox" onclick="search_form.submit();return false;" value="1" name="is_public"
                   id="is_public" <?php if ($post_param['is_public'] == 1) {
                echo "checked='checked'";
            } ?>>公共客源
        </label>
        <script>
            function nextexport() {
                var mylimit = parseInt($('#mylimit').val());
                var myoffset = parseInt($('#myoffset').val());
                /*
                mylimit = myoffset + mylimit;

                $('#mylimit').val(mylimit);
                */
                myoffset = myoffset + mylimit;

                $('#myoffset').val(myoffset);
            }
        </script>
        <!-- <span style="float:right;">从第<input type="text"  class="input w40" id="mylimit" name="mylimit" value="0" />条房源开始导出，每次导出<input type="text" class="input w40" id="myoffset" name="myoffset" value="100" />条<a href="###" onclick="nextexport();">下一组</a></span>-->
        <!--    <a onclick="sub_export_btn_2();" class="add_link">导出客源</a>-->
        <!--        <a onclick="document.getElementById('js_rent_export').style.display='block';" class="add_link">导出客源</a>-->
        <a class="add_lead" id="import">导入客源</a>
        <a href="<?php echo customer_publish_url('rent'); ?>" class="add_link"><span class="iconfont">&#xe608;</span>录入客源</a>
    </div>
    <div class="search_box clearfix" id="js_search_box">
        <a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this , 'rent_customer_list_extend')" data-h="0"
           id="extend">更多<span class="iconfont">&#xe609;</span></a>
        <div class="fg_box">
            <p class="fg fg_tex">区属：</p>
            <div class="fg">
                <select class="select" name='dist_id' onchange="get_street_by_id(this , 'street_id')">
                    <option selected="" value="0">请选择区属</option>
                    <?php if (is_array($district_arr) && !empty($district_arr)) { ?>
                        <?php foreach ($district_arr as $key => $value) { ?>
                            <option value="<?php echo $value['id']; ?>" <?php if ($post_param['dist_id'] == $value['id']) {
                                echo 'selected';
                            } ?>>
                                <?php echo $value['district']; ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex"> 板块：</p>
            <div class="fg">
                <select class="select" name='street_id' id="street_id">
                    <option value="0">不限</option>
                    <?php if (is_array($select_info['street_info']) && !empty($select_info['street_info'])) { ?>
                        <?php foreach ($select_info['street_info'] as $key => $value) { ?>
                            <option value="<?php echo $value['id']; ?>" <?php if ($post_param['street_id'] == $value['id']) {
                                echo 'selected';
                            } ?>>
                                <?php echo $value['streetname']; ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex"> 楼盘：</p>
            <div class="fg">
                <input type="text" name='cmt_name' class="input w90" id='block01'
                       value="<?php echo $post_param['cmt_name']; ?>">
                <input type="hidden" name='cmt_id' id='cmt_id' value='<?php echo $post_param['cmt_id']; ?>'>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex"> 面积：</p>
            <div class="fg">
                <input type="text" name='area_min' id='area_min' class="input w30"
                       value='<?php echo $post_param['area_min']; ?>' onblur="check_num()">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input type="text" name='area_max' id='area_max' class="input w30"
                       value='<?php echo $post_param['area_max']; ?>' onblur="check_num()">&nbsp;&nbsp;<span
                        style="font-weight:bold;color:red;" id="areamin_reminder"></span>
            </div>
            <p class="fg fg_tex fg_tex03">平米</p>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">租金：</p>
            <div class="fg">
                <input type="text" name='price_min' id='price_min' class="input w30"
                       value='<?php echo $post_param['price_min']; ?>' onblur="check_num()">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input type="text" name='price_max' id='price_max' class="input w30"
                       value='<?php echo $post_param['price_max']; ?>' onblur="check_num()">&nbsp;&nbsp;<span
                        style="font-weight:bold;color:red;" id="pricemin_reminder"></span>
            </div>
            <p class="fg fg_tex fg_tex03">元/月</p>
        </div>
        <?php if (is_int($company_id) && $company_id > 0) { ?>
            <div class="fg_box">
                <p class="fg fg_tex">范围：</p>
                <?php if ($lists_auth) { ?>
                    <div class="fg">
                        <select class="select" name='agenctcode' onchange="get_broker_by_agencyid(this,'broker_id');">
                            <option value="0">不限</option>
                            <?php if (!empty($agencys)) { ?>
                                <?php foreach ($agencys as $k => $v) { ?>
                                    <option value="<?php echo $v['agency_id']; ?>" <?php if ($v['agency_id'] == $post_param['agenctcode']) {
                                        echo 'selected';
                                    } ?>><?php echo $v['agency_name']; ?></option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="fg fg_tex fg_tex03">
                        <select class="select" name='broker_id' id="broker_id">
                            <option value="0">不限</option>
                            <?php if (!empty($broker_list)) { ?>
                                <?php foreach ($broker_list as $key => $val) { ?>
                                    <option <?php if ($val['broker_id'] == $post_param['broker_id']) echo "selected"; ?>
                                            value='<?php echo $val['broker_id'] ?>'><?php echo $val['truename'] ?></option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                <?php } else { ?>
                    <div class="fg">
                        <select class="select" name='agenctcode'>
                            <option value="<?php echo $agency_id; ?>"><?php echo $agency_name; ?></option>
                        </select>
                    </div>
                    <div class="fg fg_tex fg_tex03">
                        <select class="select" name='broker_id' id="broker_id">
                            <option value="<?php echo $broker_id; ?>"><?php echo $truename; ?></option>
                        </select>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <?php if (!empty($register_info['corpname']) && !empty($register_info['storename'])) { ?>
                <div class="fg_box">
                    <p class="fg fg_tex"> 范围：</p>
                    <div class="fg">
                        <select class="select">
                            <option><?php echo $register_info['corpname']; ?></option>
                        </select>
                    </div>
                    <div class="fg fg_tex fg_tex03">
                        <select class="select">
                            <option><?php echo $register_info['storename']; ?></option>
                        </select>
                    </div>
                </div>

            <?php } ?>
        <?php } ?>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 录入时间：</p>
            <div class="fg">
                <select class="select" name='create_time_range'>
                    <option value="0">不限</option>
                    <?php
                    if (is_array($conf_customer['create_time_range']) && !empty($conf_customer['create_time_range'])) {
                        foreach ($conf_customer['create_time_range'] as $key => $value) {
                            ?>
                            <option value='<?php echo $key; ?>' <?php if ($post_param['create_time_range'] == $key) {
                                echo 'selected';
                            } ?>><?php echo $value; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 物业类型：</p>
            <div class="fg">
                <select class="select" name='property_type'>
                    <option value="0">不限</option>
                    <?php if (is_array($conf_customer['property_type']) && !empty($conf_customer['property_type'])) { ?>
                        <?php foreach ($conf_customer['property_type'] as $key => $value) { ?>
                            <option value='<?php echo $key; ?>' <?php if ($post_param['property_type'] == $key) {
                                echo 'selected';
                            } ?>><?php echo $value; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 户型：</p>
            <div class="fg">
                <select class="select" name='room'>
                    <option value='0'>不限</option>
                    <?php if (is_array($conf_customer['room_type']) && !empty($conf_customer['room_type'])) { ?>
                        <?php foreach ($conf_customer['room_type'] as $key => $value) { ?>
                            <option value='<?php echo $key; ?>' <?php if ($post_param['room'] == $key) {
                                echo 'selected';
                            } ?>><?php echo $value; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 性质：</p>
            <div class="fg">
                <select class="select" name='public_type'>
                    <option value="0">不限</option>
                    <?php if (is_array($conf_customer['public_type']) && !empty($conf_customer['public_type'])) { ?>
                        <?php foreach ($conf_customer['public_type'] as $key => $value) { ?>
                            <option value='<?php echo $key; ?>' <?php if ($post_param['public_type'] == $key) {
                                echo 'selected';
                            } ?>> <?php echo $value; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 状态：</p>
            <div class="fg">
                <select class="select" name='status'>
                    <option value="test">不限</option>
                    <?php
                    if (is_array($conf_customer['status']) && !empty($conf_customer['status'])) {
                        foreach ($conf_customer['status'] as $key => $value) {
                            ?>
                            <?php if ($check_on == 'check_on' && $key == 1) {
                                echo '<option value="' . $key . '" selected = "selected">' . $value . '</option>';
                            } else {
                                if ($post_param['status'] == $key) {
                                    $sign = 'selected = "selected"';
                                } else {
                                    $sign = '';
                                }
                                echo '<option value="' . $key . '" ' . $sign . '>' . $value . '</option>';
                            } ?>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 是否合作：</p>
            <div class="fg">
                <select class="select" name='is_share'>
                    <option value='' <?php if ($post_param['is_share'] == '') {
                        echo 'selected';
                    } ?>>不限
                    </option>
                    <?php
                    if (is_array($conf_customer['is_share']) && !empty($conf_customer['is_share'])) {
                        foreach ($conf_customer['is_share'] as $key => $value) {
                            ?>
                            <option value='<?php echo $key; ?>' <?php if ($post_param['is_share'] == $key && $post_param['is_share'] != '') {
                                echo 'selected';
                            } ?>> <?php echo $value; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 客户电话：</p>
            <div class="fg">
                <input type="text" name='telno' value="<?php echo $post_param['telno']; ?>" class="input w80">
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 客户姓名：</p>
            <div class="fg">
                <input type="text" name='truename' value="<?php echo $post_param['truename']; ?>" class="input w80">
            </div>
        </div>
        <div class="fg_box">
            <input type="hidden" name='orderby_id' id="orderby_id" value="<?php echo $post_param['orderby_id'] ?>">
            <div class="fg"><a href="javascript:void(0)" onclick="sub_form();return false;" class="btn"><span
                            class="btn_inner">搜索</span></a></div>
            <div class="fg"><a href="javascript:void(0)" class="reset" onclick='del_cookie();'>重置</a></div>
        </div>
    </div>
    <script type="text/javascript">
        function log_data_replace() {
            var window_min_id = [];
            $('input[name="window_min_id"]').each(function () {
                window_min_id.push($(this).val());
            });
            $.ajax({
                url: "/rent_customer/min_log_replace/",
                type: "GET",
                data: {
                    'window_min_id': window_min_id,
                    'is_pub': 0
                }
            });
        }

        function log_data_del() {
            var window_min_id = [];
            $('input[name="window_min_id"]').each(function () {
                window_min_id.push($(this).val());
            });
            $.ajax({
                url: "/rent_customer/min_log_del/",
                type: "GET",
                data: {
                    'window_min_id': window_min_id,
                    'is_pub': 0
                }
            });
        }

        $(function () {
            document.onkeydown = function (e) { //enter
                var ev = document.all ? window.event : e;
                if (ev.keyCode == 13) {
                    //$('#search_form :input[name=page]').val('1');
                    $('#search_form').submit();
                    return false;
                }
            }

            //最小化
            $('#window_min_click').live('click', function () {
                $(this).parents("div").hide();
                var window_min_name = $('#window_min_name').val();
                var window_min_url = $('#window_min_url').val();
                var window_min_id = $('#window_min_id').val();

                //判断该数据是否已最小化
                var window_min = $('#window_min_id_' + window_min_id);
                if ('undefined' == typeof(window_min[0])) {
                    var window_min_html = '';
                    window_min_html += '<li id="' + 'window_min_id_' + window_min_id + '">';
                    window_min_html += '<span class="zws_bottom_nav_dao_img "></span>';
                    window_min_html += '<span class="zws_bottom_span">' + window_min_name + '</span>';
                    window_min_html += '<input type="hidden" value="' + window_min_url + '"/>';
                    window_min_html += '<input type="hidden" value="' + window_min_id + '" name="window_min_id" />';
                    window_min_html += '<span  title=""  class="iconfont zws_bottom_span_close">&#xe62c;</span>';
                    window_min_html += '</li>';
                    $('#window_min').append(window_min_html);
                    var num = $('#window_min').children().size();
                    $('#window_min').css('width', 210 * num);


                    totalNumLi = ($(".zws_bottom_nav_dao li").length);
                    samllTab();

                    //操作日志数据
                    log_data_replace();
                }
            });

            //关闭弹框删除最小化
            $('#window_min_close').live('click', function () {
                var window_min_id = $('#window_min_id').val();
                $('#window_min_id_' + window_min_id).remove();
                $(this).parents("div").hide();
                //操作日志数据
                log_data_del();
            });

            var totalNumLi = $(".zws_bottom_nav_dao li").length;
            var smallCur = 0;
            var objNum = 0;

            function samllTab() {
                //当前标签处理
                titleShowBj();
                //弹出内容
                detialShow();
                //切换箭头显示与隐藏
                tabShow();

            }

            //左右切换
            function preNex() {
                //左切换
                $(".small_nex").live("click", function () {
                    //alert("a");
                    objNum--;
                    objNum = objNum < 1 ? 0 : objNum;
                    $(".zws_bottom_nav_dao").find("ul").animate({"margin-left": -objNum * 200 + "px"}, 300)

                })
                //右切换
                $(".small_pre").live("click", function () {
                    //alert("b");
                    objNum++;
                    objNum = objNum < totalNumLi ? objNum : totalNumLi - 1;
                    $(".zws_bottom_nav_dao").find("ul").animate({"margin-left": -objNum * 200 + "px"}, 300)

                })


            }

            preNex();

            //切换显示与否
            function tabShow() {
                var aW = 210;
                var aBody = $(window).width() * 0.95;
                var aLi = $(".zws_bottom_nav_dao li").length;
                var totalLen = aW * aLi;
                if (totalLen < aBody) {
                    $(".zws_container").css("display", "none");
                    //alert(aLi);
                }
                else {
                    $(".zws_container").css("display", "block");
                    //alert(aLi);
                }
            }

            tabShow();

            //底部标题关闭处理
            function titleClose() {
                $(".zws_bottom_nav_dao li").find(".zws_bottom_span_close").live("click", function () {
                    //alert("a");
                    $(this).parent("li").remove();
                    //UlLength(aObjUl, aObjLl);
                    tabShow();
                    //操作日志数据
                    log_data_del();
                })
            }

            titleClose();

            //弹出内容
            function detialShow() {
                $(".zws_bottom_nav_dao").find(".zws_bottom_span").live("click", function () {
                    smallCur = ($(this).parent("li").index()); //当前最小化的标签高亮
                    var aUrl = $(this).next("input").val();
                    var id = $(this).next("input").next("input").val();
                    $('#window_min_id').val(id);

                    $("#js_pop_box_g").css("display", "block");
                    $("#js_pop_box_g").css({
                        "position": "absolute",
                        "z-index": "199804",
                        "left": "50%",
                        "margin-left": "-409px",
                        "margin-top": "-271px",
                        "top": "50%"

                    });
                    $("#js_pop_box_g").find("iframe").attr("src", aUrl);

                    $(".zws_bottom_nav_dao_img").removeClass("curSmall_S");
                    $(this).prev("span").addClass("curSmall_S");
                })

            }

            detialShow();

            //当前标签显示
            function titleShowBj() {
                $(".zws_bottom_nav_dao").find("li").on("click", function () {
                    $(".zws_bottom_nav_dao_img").removeClass("curSmall_S");
                    $(this).find(".zws_bottom_nav_dao_img").addClass("curSmall_S");

                })

            }

            titleShowBj();

            //导入客源
            $('#import').click(function () {
                var group_id = <?php echo $group_id;?>;
                if ('1' == group_id) {
                    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                    openWin('js_pop_do_warning');
                    return false;
                }
                openn_import('rent_customer');
            });
            //客源跟进
            $('#follow_openlist').click(function () {
                var group_id = <?php echo $group_id;?>;
                if ('1' == group_id) {
                    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                    openWin('js_pop_do_warning');
                    return false;
                }
                open_follow('rent_customer', 1);
            });
            //智能匹配
            $('#match_openlist').click(function () {
                var group_id = <?php echo $group_id;?>;
                if ('1' == group_id) {
                    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                    openWin('js_pop_do_warning');
                    return false;
                }
                open_match_right('rent_customer', 0);
            });
            //分配任务
            $('#task_openlist').click(function () {
                var group_id = <?php echo $group_id;?>;
                if ('1' == group_id) {
                    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                    openWin('js_pop_do_warning');
                    return false;
                }
                ringt_tasks('rent_customer', 4);
            });
            //分配客源
            $('#fenpei_openlist').click(function () {
                var group_id = <?php echo $group_id;?>;
                if ('1' == group_id) {
                    $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                    openWin('js_pop_do_warning');
                    return false;
                }
                allocate_customer('rent_customer');
            });
        });
    </script>
    <div class="table_all">
        <div class="title" id="js_title">
            <table class="table">
                <tr>
                    <td class="c3">
                        <div class="info"></div>
                    </td>
                    <?php if (in_array(1, $rent_customer_field_arr)) { ?>
                        <td class="c5">
                            <div class="info">标签</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(18, $rent_customer_field_arr)) { ?>
                        <td class="c3">
                            <div class="info">编号</div>
                        </td>
                    <?php } ?>
                    <!--td class="c4"><div class="info">交易</div></td>-->
                    <?php if (in_array(2, $rent_customer_field_arr)) { ?>
                        <td class="c3">
                            <div class="info">状态</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(3, $rent_customer_field_arr)) { ?>
                        <td class="c3">
                            <div class="info">性质</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(4, $rent_customer_field_arr)) { ?>
                        <td class="c3">
                            <div class="info">合作</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(5, $rent_customer_field_arr)) { ?>
                        <td class="c6">
                            <div class="info">客户</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(6, $rent_customer_field_arr)) { ?>
                        <td class="c15">
                            <div class="info">意向区属板块</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(7, $rent_customer_field_arr)) { ?>
                        <td class="c15">
                            <div class="info">意向楼盘</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(8, $rent_customer_field_arr)) { ?>
                        <td class="c6">
                            <div class="info">物业类型</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(9, $rent_customer_field_arr)) { ?>
                        <td class="c6">
                            <div class="info">面积(㎡)</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(10, $rent_customer_field_arr)) { ?>
                        <td class="c8">
                            <div class="info">租金</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(11, $rent_customer_field_arr)) { ?>
                        <td class="c6">
                            <div class="info">户型(室)</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(12, $rent_customer_field_arr)) { ?>
                        <td>
                            <div class="info">装修</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(13, $rent_customer_field_arr)) { ?>
                        <td>
                            <div class="info">楼层</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(14, $rent_customer_field_arr)) { ?>
                        <td>
                            <div class="info">朝向</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(15, $rent_customer_field_arr)) { ?>
                        <td>
                            <div class="info">房龄</div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(16, $rent_customer_field_arr)) { ?>
                        <td>
                            <div class="info">
                                <a href="javascript:void(0)" onclick="selllist_order(1);return false;"
                                   id="order_avgprice" class="i_text <?php if ($post_param['orderby_id'] == 1) {
                                    echo 'i_down';
                                } elseif ($post_param['orderby_id'] == 2) {
                                    echo 'i_up';
                                } ?>">跟进时间<br></a>
                            </div>
                        </td>
                    <?php } ?>
                    <?php if (in_array(17, $rent_customer_field_arr)) { ?>
                        <td class="c6">
                            <div class="info">经纪人</div>
                        </td>
                    <?php } ?>
                </tr>
            </table>
        </div>
        <div class="inner" id="js_innerHouse" style="height:371px;">
            <table class="table table_q">
                <input type="hidden" value="<?php echo $group_id ?>" id="group_id">
                <?php if (is_array($customer_list) && !empty($customer_list)) { ?>
                    <?php foreach ($customer_list as $key => $value) {
                        //获得客源的创建天数
                        $real_time = $rent_customer_check_day * 24 * 3600 + $value['creattime'];
                        if ($real_time > time()) {
                            $is_check_day = 1;
                        } else {
                            $is_check_day = 0;
                        }
                        //红色警告（房源跟进信息无堪房）
                        if (in_array($value['id'], $follow_red_customer_id)) {
                            $tag_follow_red = 1;
                        } else {
                            $tag_follow_red = 0;
                        }

                        $tag_class2 = 'zws-red';
                        $tag_str = '该客源自登记以来超过' . $rent_customer_check_time . '天未勘察';
                        if (1 === $tag_follow_red || 1 === $is_check_day) {
                            if (in_array($value['id'], $follow_green_customer_id)) {
                                $tag_class2 = 'zws-green';
                                $tag_str = '该客源距离上一次跟进已超' . $rent_customer_follow_last_time1 . '天';
                            } else {
                                if (in_array($value['id'], $follow_zi_customer_id)) {
                                    $tag_class2 = 'zws-zi';
                                    $tag_str = '该客源距离上一次跟进已超' . $rent_customer_follow_last_time2 . '天';
                                } else {
                                    if (in_array($value['id'], $yellow_customer_id)) {
                                        $tag_class2 = 'zws-yellow2';
                                        $tag_str = '该客源两次跟进间隔已超过' . $customer_follow_spacing_time . '天';
                                    } else {
                                        $tag_class2 = '';
                                        $tag_str = '';
                                    }
                                }
                            }
                        }

                        //当客源又是紧急又有提醒的话，只展示提醒(仅限自己的客源)
                        if ($broker_id == intval($value['broker_id'])) {
                            if (in_array($value['id'], $remind_customer_id)) {
                                $tag_remind = 1;
                            } else {
                                $tag_remind = 0;
                            }
                        } else {
                            $tag_remind = 0;
                        }
                        $tag_jingji = $value['user_level'] == 3 ? 1 : 0;
                        if (1 == $tag_remind) {
                            $tag_class = 'bg-yellow';
                        } else {
                            if (1 == $tag_jingji) {
                                $tag_class = 'bg-red';
                            } else {
                                $tag_class = '';
                            }
                        }
                        $tdclass = 1 == $key % 2 ? 'bg' : '';

                        $tdclass = $tdclass . ' ' . $tag_class;
                        ?>
                        <tr <?php if (!empty($tag_str)){ ?>title="<?php echo $tag_str; ?>"<?php } ?>
                            <?php if ('' != $tdclass){ ?>class="<?php echo $tdclass; ?>" <?php } ?>
                            id="tr<?php echo $value['id']; ?>"
                            date-url="<?php echo MLS_URL; ?>/rent_customer/details/<?php echo $value['id']; ?>"
                            controller="rent_customer" _id="<?php echo $value['id']; ?>"
                            min_title="<?php echo $district_arr[$value['dist_id1']]['district'] . '-' . $street_arr[$value['street_id1']]['streetname'] . ' ' . intval($value['price_min']) . '-' . intval($value['price_max']) . '万'; ?>"
                            page_id="<?php echo $key + 1; ?>">
                            <td class="c3">
                                <div class="info">
                                    <input style="" type="checkbox" class="checkbox" name='customer_id'
                                           value="<?php echo $value['id']; ?>">
                                    <?php if (1 == $tag_remind) { ?>
                                        <img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/new/tip.png">
                                    <?php } else {
                                        if (1 == $tag_jingji) {
                                            ?>
                                            <img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/new/hot.png">
                                        <?php }
                                    } ?>
                                </div>
                            </td>
                            <?php if (in_array(1, $rent_customer_field_arr)) { ?>
                                <td class="c5">
                                    <div class="info">
                                        <?php if ($value['lock']) { ?><span class="iconfont ts ts02" title="已被锁定">&#xe632;</span><?php } ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(18, $rent_customer_field_arr)) { ?>
                                <td class="c3">
                                    <div class="info"><?php echo get_custom_id($value['id'], 'rent'); ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <!--td class="c4"><div class="info">租</div></td>-->
                            <?php if (in_array(2, $rent_customer_field_arr)) { ?>
                                <td class="c3">
                                    <div class="info">
                                        <?php
                                        if (isset($conf_customer['status'][$value['status']]) && $conf_customer['status'][$value['status']] != '') {
                                            echo $conf_customer['status'][$value['status']];
                                        }
                                        ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(3, $rent_customer_field_arr)) { ?>
                                <td class="c3">
                                    <div class="info" id="public_type<?php echo $value['id']; ?>">
                                        <?php
                                        if (isset($conf_customer['public_type'][$value['public_type']]) && $conf_customer['public_type'][$value['public_type']] != '') {
                                            echo $conf_customer['public_type'][$value['public_type']];
                                        }
                                        ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(4, $rent_customer_field_arr)) { ?>
                                <td class="c3">
                                    <div class="info">
                                        <?php
                                        if (isset($conf_customer['is_share'][$value['is_share']]) && $conf_customer['is_share'][$value['is_share']] != '') {
                                            echo $conf_customer['is_share'][$value['is_share']];
                                        } else {
                                            echo '否';
                                        }
                                        ?>
                                    </div>
                                    <input type="hidden" id="share_num<?php echo $value['id'] ?>"
                                           value="<?php echo $value['is_share']; ?>"/>
                                    <input type="hidden" value="<?php echo $value['is_report'] ?>"
                                           id="is_report<?php echo $value['id'] ?>">
                                </td>
                            <?php } ?>
                            <?php if (in_array(5, $rent_customer_field_arr)) { ?>
                                <td class="c6">
                                    <div class="info"><?php echo $value['truename']; ?></div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(6, $rent_customer_field_arr)) { ?>
                                <td class="c15">
                                    <div class="info">
                                        <div class="info">
                                            <?php
                                            $district_str = '';
                                            if ($value['dist_id1'] > 0 && isset($district_arr[$value['dist_id1']]['district'])) {
                                                $district_str = $district_arr[$value['dist_id1']]['district'];
                                                if ($district_str != '' && $value['street_id1'] > 0 && !empty($street_arr[$value['street_id1']]['streetname'])) {
                                                    $district_str .= '-' . $street_arr[$value['street_id1']]['streetname'];
                                                }
                                            }

                                            if ($value['dist_id2'] > 0 && isset($district_arr[$value['dist_id2']]['district'])) {
                                                $district_str .= !empty($district_str) ? '，' . $district_arr[$value['dist_id2']]['district'] :
                                                    $district_arr[$value['dist_id2']]['district'];

                                                if (!empty($district_arr[$value['dist_id2']]['district']) &&
                                                    $value['street_id2'] > 0 && !empty($street_arr[$value['street_id2']]['streetname'])
                                                ) {
                                                    $district_str .= '-' . $street_arr[$value['street_id2']]['streetname'];
                                                }
                                            }

                                            if ($value['dist_id3'] > 0 && isset($district_arr[$value['dist_id3']]['district'])) {
                                                $district_str .= !empty($district_str) ? '，' . $district_arr[$value['dist_id3']]['district'] :
                                                    $district_arr[$value['dist_id3']]['district'];

                                                if (!empty($district_arr[$value['dist_id3']]['district']) &&
                                                    $value['street_id3'] > 0 && !empty($street_arr[$value['street_id3']]['streetname'])
                                                ) {
                                                    $district_str .= '-' . $street_arr[$value['street_id3']]['streetname'];
                                                }
                                            }

                                            echo $district_str;
                                            ?>
                                        </div>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(7, $rent_customer_field_arr)) { ?>
                                <td class="c15">
                                    <div class="info f14 fblod <?php echo $tag_class2; ?>">
                                        <?php
                                        if (isset($value['cmt_name1']) && $value['cmt_name1'] != '') {
                                            echo $value['cmt_name1'];
                                        }

                                        if (isset($value['cmt_name2']) && $value['cmt_name2'] != '') {
                                            echo '，' . $value['cmt_name2'];
                                        }

                                        if (isset($value['cmt_name3']) && $value['cmt_name3'] != '') {
                                            echo '，' . $value['cmt_name3'];
                                        }
                                        ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(8, $rent_customer_field_arr)) { ?>
                                <td class="c6">
                                    <div class="info">
                                        <?php
                                        if (isset($conf_customer['property_type'][$value['property_type']])) {
                                            echo $conf_customer['property_type'][$value['property_type']];
                                        }
                                        ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(9, $rent_customer_field_arr)) { ?>
                                <td class="c6">
                                    <div class="info f60"><?php echo strip_end_0($value['area_min']); ?>
                                        -<?php echo strip_end_0($value['area_max']); ?></div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(10, $rent_customer_field_arr)) { ?>
                                <td class="c8">
                                    <div class="info f60 f13 fblod"><?php echo ('1' == $value['price_danwei']) ? strip_end_0($value['price_min'] / $value['area_min'] / 30) : strip_end_0($value['price_min']); ?>
                                        -<?php echo ('1' == $value['price_danwei']) ? strip_end_0($value['price_max'] / $value['area_max'] / 30) : strip_end_0($value['price_max']); ?><?php echo '1' == $value['price_danwei'] ? '元/㎡*天' : '元/月'; ?></div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(11, $rent_customer_field_arr)) { ?>
                                <td class="c6">
                                    <div class="info fblod"><?php echo $value['room_min']; ?>
                                        -<?php echo $value['room_max']; ?></div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(12, $rent_customer_field_arr)) { ?>
                                <td>
                                    <div class="info">
                                        <?php
                                        if (isset($value['fitment']) && !empty($value['fitment'])) {
                                            echo $conf_customer['fitment'][$value['fitment']];
                                        }
                                        ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(13, $rent_customer_field_arr)) { ?>
                                <td>
                                    <div class="info fblod"><?php echo $value['floor_min']; ?>
                                        -<?php echo $value['floor_max']; ?></div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(14, $rent_customer_field_arr)) { ?>
                                <td>
                                    <div class="info">
                                        <?php
                                        if (isset($value['forward']) && !empty($value['forward'])) {
                                            echo $conf_customer['forward'][$value['forward']];
                                        }
                                        ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(15, $rent_customer_field_arr)) { ?>
                                <td>
                                    <div class="info">
                                        <?php
                                        if (isset($value['house_age']) && !empty($value['house_age'])) {
                                            echo $conf_customer['house_age'][$value['house_age']];
                                        }
                                        ?>
                                    </div>
                                </td>                <?php } ?>
                            <?php if (in_array(16, $rent_customer_field_arr)) { ?>
                                <td>
                                    <div class="info info_p_r"><?php echo date('Y-m-d H:i', $value['updatetime']); ?></div>
                                </td>
                            <?php } ?>
                            <?php if (in_array(17, $rent_customer_field_arr)) { ?>
                                <td class="c5">
                                    <div class="info">
                                        <?php
                                        if (isset($customer_broker_info[$value['broker_id']]['truename']) && $customer_broker_info[$value['broker_id']]['truename'] != '') {
                                            echo $customer_broker_info[$value['broker_id']]['truename'];
                                        }
                                        ?>
                                    </div>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <script type="text/javascript">
            function getCookie(name)//取cookies函数
            {
                var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
                if (arr != null) return unescape(arr[2]);
                return null;
            }

            function delCookie(name)//删除cookie
            {
                var exp = new Date();
                exp.setTime(exp.getTime() - 1);
                var cval = getCookie(name);
                if (cval != null) document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString() + ";path=/";
                ;
            }

            $(function () {
                function reHeightList() {

                    var ListHeight = $(window).height();
                    var TabHeight = $(".tab_box").height();
                    var SearchHeight = $("#js_search_box").height();
                    $("#js_innerHouse").css("height", (ListHeight - TabHeight - SearchHeight - 138) + "px");

                }

                reHeightList();
                setInterval(function () {
                    reHeightList();
                }, 500)

                //获得上次跟进或合作操作的数据值。
                var Num = getCookie('page_id') - 6;
                if (Num > 0) {
                    var HeightTr = $(".inner").find("tr").height();
                    var $content = $(".inner");
                    $content.scrollTop(Num * HeightTr);
                }

                delCookie('page_id');

                //'收起'，‘更多’按钮，获得cookie值
                var rent_customer_list_extend = getCookie('rent_customer_list_extend');
                if (1 == rent_customer_list_extend) {
                    $('#js_search_box').find(".hide").css("display", "inline");
                    $('#extend').html('收起<span class="iconfont">&#xe60a;</span>');
                    $('#extend').attr("data-h", "1");
                } else {
                    $('#js_search_box').find(".hide").hide();
                    $('#extend').html('更多<span class="iconfont">&#xe609;</span>');
                    $('#extend').attr("data-h", "0");
                }
            })
        </script>
    </div>
    <div class="fun_btn clearfix" id="js_fun_btn" style="">
        <div class="get_page">
            <?php if (isset($page_list) && $page_list != '') {
                echo $page_list;
            } ?>
        </div>
    </div>
    <!--最小化导航栏-->
    <!--<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/js/v1.0/sreen_small.js"></script>-->
    <div class="zws_bottom_nav" style="margin-top:-8px;">
        <div class="zws_bottom_nav_dao">
            <ul id="window_min">
                <?php if (is_full_array($rent_list_min_arr)) {
                    foreach ($rent_list_min_arr as $k => $v) {
                        ?>
                        <li id="window_min_id_<?php echo $v['customer_id']; ?>">
                            <span class="zws_bottom_nav_dao_img "></span>
                            <span class="zws_bottom_span"><?php echo $v['name']; ?></span>
                            <input type="hidden" value="<?php echo '/rent_customer/details/' . $v['customer_id'] ?>"/>
                            <input type="hidden" value="<?php echo $v['customer_id']; ?>" name="window_min_id"/>
                            <span class="iconfont zws_bottom_span_close">&#xe62c</span>
                        </li>
                    <?php }
                } ?>
            </ul>
        </div>
        <!--切换-->
        <div class="zws_bottom_nav_dao_tab zws_container">
            <p class="small_pre"></p>
            <p class="small_nex"></p>
        </div>
    </div>
    </div>
    </div>

</form>
<ul id="openList">
    <input type="hidden" id="right_id" class="js_input">
    <!--右键菜单-->
    <?php if ('1' == $post_param['is_public']) { ?>
        <li onclick="openDetails('rent_customer');" class="js_input_1">查看详情</li>
        <li class="js_input_3" id="follow_openlist">客源跟进</li>
    <?php } else { ?>
        <li onclick="openDetails('rent_customer');" class="js_input_1">查看详情</li>
        <li onclick="modifyInfo('rent_customer');" class="js_input_2">修改详情</li>
        <li class="line"></li>
        <li class="js_input_3" id="follow_openlist">客源跟进</li>
        <li class="line"></li>
        <li class="js_input_5" id="match_openlist">智能匹配</li>
        <?php if ('1' == $open_cooperate) { ?>
            <!--是否开启合作审核区分-->
            <?php if ('1' == $check_cooperate) { ?>
                <li onclick="share_check('rent_customer')" class="js_input_6">设置合作</li>
            <?php } else { ?>
                <li onclick="set_share_right('rent_customer')" class="js_input_6">设置合作</li>
            <?php } ?>
            <li onclick="cancle_share_right('rent_customer')" class="js_input_7">取消合作</li>
        <?php } ?>

        <li class="line"></li>
        <li class="js_input_8" id="task_openlist">分配任务</li>
        <li class="js_input_9" id="fenpei_openlist">分配客源</li>
    <?php } ?>
</ul>

<!--合作申请弹框-->
<div id="js_pop_box_cooperation_customer" class="iframePopBox" style=" width:920px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="920" height="540" class='iframePop' src=""></iframe>
</div>

<!--合作申请房源选择弹框-->
<div id="js_pop_box_cooperation" class="iframePopBox" style=" width:520px; height:496px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="520" height="496" class='iframePop' src=""></iframe>
</div>

<!--分配任务-->
<div id="js_fenpeirenwu" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--分配客源-->
<div id="js_fenpeikeyuan" class="iframePopBox" style=" width:816px; height:340px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="340" class='iframePop' src=""></iframe>
</div>
<!--分配房源-->
<div id="js_allocate_customer" class="iframePopBox" style=" width:816px; height:340px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="340" class='iframePop' src=""></iframe>
</div>
<!--跟进信息弹框-->
<div id="js_genjin" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--详情页弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <input type="hidden" value="" id="window_min_name"/>
    <input type="hidden" value="" id="window_min_url"/>
    <input type="hidden" value="" id="window_min_id"/>
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" style="right:46px;"
       id="window_min_click">一</a>
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" id="window_min_close">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--匹配详情页弹框-->
<div id="js_pop_box_g_match" class="iframePopBox" style=" width:930px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="930" height="540" class='iframePop' src=""></iframe>
</div>

<!--页面处理中弹层-->
<div style="display:none; text-align: center;" id='docation_loading'>
    <img src="<?php echo MLS_SOURCE_URL; ?>/common/images/loading_6.gif">
    <p style="font-size: 16px; font-family:'微软雅黑'; line-height: 30px; color: #fff;">正在处理</p>
</div>
<div id="jss_pop_import" class="pop_box_g pop_see_inform" style=" display:none;">
    <div class="hd">
        <div class="title">求租客源导入</div>
        <div class="close_pop"><a href="/rent_customer/manage/" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">

        <div class="up_m_b_tex">客源导入功能可以将外部客源直接导入<?php echo SOFTWARE_NAME; ?>
            中，省去手动录入的麻烦。为保证您的客源顺利导入，请使用我们提供的标准模板，且勿对模板样式做任何删改。<a
                    href="<?php echo MLS_SOURCE_URL; ?>/xls/example4.xls" target="_blank">
                <img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/page_white_excel.png">点击下载求租客源导入模板</a>
        </div>
        <style>
            .up_m_b_file .text {
                float: left;
                line-height: 26px;
            }

            .up_m_b_file .text_input {
                width: 150px;
                height: 24px;
                line-height: 24px;
                padding: 0 10px;
                border: 1px solid #E9E9E9;
                float: left;
            }

            .up_m_b_file .f_btn {
                margin-left: 10px;
                _display: inline;
                float: left;
                background: url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0;
                width: 44px;
                height: 26px;
                overflow: hidden;
                position: relative;
                overflow: hidden;
                text-align: center;
                line-height: 26px;
            }

            .up_m_b_file .f_btn .file {
                cursor: pointer;
                font-size: 50px;
                filter: alpha(opacity:0);
                opacity: 0;
                position: absolute;
                right: -5px;
                top: -5px;
            }

            .up_m_b_file .btn_up_b {
                margin-left: 10px;
                _display: inline;
                float: left;
                overflow: hidden;
                width: 44px;
                height: 26px;
                position: relative;
                line-height: 26px;
                text-align: center;
                background: url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0;
            }

            .up_m_b_file .btn_up_b .btn_up {
                cursor: pointer;
                font-size: 100px;
                position: absolute;
                filter: alpha(opacity:0);
                opacity: 0;
                right: -5px;
                top: -5px;
            }
        </style>
        <div class="up_m_b_file clearfix">
            <form action="/rent_customer/import" enctype="multipart/form-data" target="new" method="post">
                <p class="text">上传导入文件：</p>
                <input type="text" class="text_input" id="aa" name="aa">
                <div class="f_btn" style=" background-position: 0 0; ">
                    <div style="width: 44px; position: absolute; left:0; top: 0;">浏览</div>
                    <input class="file" name="upfile" type="file"
                           onchange="document.getElementById('aa').value=this.value"></div>
                <div class="btn_up_b" style=" background-position: 0 0; ">
                    <div style="width: 44px; position: absolute; left:0; top: 0;">上传</div>
                    <input class="btn_up" type="submit" name="sub" value="上传"></div>
            </form>
        </div>
        <iframe allowtransparency="true" src="<?php echo MLS_URL; ?>/sell/blank" frameborder="0" scrolling="no"
                name="new" id="xx1x" height="35" width="393"></iframe>
        <a class="btn-lv1 btn-mid" href="javascript:void(0)" onclick="openn_sure('rent_customer')">确认导入</a>
    </div>
</div>
<script>
    function see_reason() {
        var xxx = $(document.getElementById('xx1x').contentWindow.document.body).html();
        xxx = xxx.replace(/<p .*?>(.*?)<\/p>/g, " ");
        xxx = xxx.replace(/<P .*?>(.*?)<\/P>/g, " "); //为了兼容ie6
        xxx = xxx.replace(/display:none/g, "display:block");
        xxx = xxx.replace(/DISPLAY: none/g, "DISPLAY: block"); //为了兼容ie6
        //alert(xxx);
        $("#js_pop_msg_excel .up_inner").html(xxx);
        openWin('js_pop_msg_excel');
    }
</script>

<!-- 导入表格错误提示框 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg_excel" style="margin-left:-200px;width:400px">
    <div class="hd">
        <div class="title">失败列表</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="javascript:void(0)"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner" style="height:150px;overflow-x:hidden;overflow-y:auto">
            <div class="up_inner" style="padding:0px">
                <p class="text"><img class="img_msg" style="margin-right:10px;"
                                     src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/r_ico.png">
                    <span class="span_msg"></span><!-- id="dialog_do_itp"-->
                </p>
            </div>
        </div>
    </div>
</div>

<!--确认导入表格弹窗-->
<div id="jss_pop_sure" class="pop_box_g pop_see_inform pop_no_q_up stop_pop_box">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="/rent_customer/manage/" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" style="line-height:28px;"><br>
                    <img alt="" src="">
                    <span></span>
                </p>
            </div>
        </div>
    </div>
</div>

<!--提示导入表格弹窗-->
<div id="jss_pop_error" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" style="line-height:28px;"><br>
                    <img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/error_ico.png">
                    <span> 请上传表格！</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- 提示消息弹窗 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="javascript:void(0)"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img class="img_msg" style="margin-right:10px;"
                                     src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/r_ico.png">
                    <span class="span_msg"></span>
                </p>
            </div>
        </div>
    </div>
</div>

<!--合作审核操作结果弹出警告-->
<div id="js_pop_do_warning_share_check" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <div class="text-wrap">
                    <p><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/r_ico.png"> <strong class="msg">提交合作申请成功</strong><br>
                        管理人员审核通过后，消息会第一时间告知！</p>
                </div>
                <a href="javascript:void(0);" id="sure_yes_share_check" class="btn-lv1 btn-mid btn_qd_text JS_Close"
                   style="margin:0 auto !important">确定</a>
            </div>

        </div>
    </div>
</div>

<!--设置合作弹框发送审核-->
<div id="js_pop_set_share_warning2" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <div class="text-wrap">
                    <table>
                        <tr>
                            <td>
                                <div class="img"><img alt=""
                                                      src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/s_ico.png">
                                </div>
                            </td>
                            <td class="msg"><span class="bold">您确定要将该客源设置合作吗？</span></td>
                        </tr>
                    </table>
                </div>
                <div style="width:120px; margin:0 auto; height:auto; overflow:hidden; zoom:1;">
                    <button type="button" class="btn btn_qd_text" id="dialog_share_share2"
                            style="float:left !important;">确定
                    </button>
                    <button type="button" class="btn btn_none btn_qx_text JS_Close" style="float:right !important;">取消
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!--导出求租客源报表弹出窗口-->
<div class="pop_box_g pop_box_g_big pop_box_d_c"
     style="display:none;position: fixed;top: 0;bottom: 0;left: 0;right: 0;margin: auto;" id="js_rent_export">
    <div class="hd">
        <div class="title">报表导出</div>
        <div class="close_pop"><a onclick="document.getElementById('js_rent_export').style.display='none';" title="关闭"
                                  class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <p class="d_c_title">报表导出</p>
        <div class="inner">
            <form action="<?php echo MLS_URL; ?>/rent_customer/exportReport" method="post" id="myform">
                <!--存放当前的总页数用于判断-->
                <input type="hidden" name="hid_total_page" value="<?php echo $pages ?>"/>
                <strong class="t">请选择导出类型：</strong>
                <label class="label"><input type="radio" name="ch" value="1">仅导出所选客源</label>
                <label class="label"><input type="radio" name="ch" value="2">导出当前页所有客源</label>
                <label class="label"><input type="radio" name="ch" value="3">导出多页客源</label>
                导出范围：<input type="text" class="text_input w40" name="start_page" disabled="disabled">
                <span class="fg">一</span>
                <input type="text" class="text_input w40" name="end_page" disabled="disabled">（一次最多只能导出10页）
                <input type="hidden" name="ch_1_data" value="">   <!--用于存放ch的值为1的时候的ID数组-->
                <input type="hidden" name="final_data" value="">  <!--用于存放ch的值为2和3的时候的提交数据-->
                <div style="margin:10px 0;padding:0 15px; ">
                    <label class="label"><input type="radio" name="ch" value="4">按条数导出</label>
                    <span>
                从第
                <input type="text" class="input w40" id="myoffset" name="myoffset" value="0" disabled="disabled"/>
                条房源开始导出，每次导出
                <input type="text" class="input w40" id="mylimit" name="mylimit" value="10" disabled="disabled"/>
                条
                <a href="###" onclick="nextexport();">
                    下一组
                </a>
            </span>
                </div>

            </form>
        </div>

        <!--        <a class="btn-lv1 btn-mid" onclick="sub_export_btn()" style="margin-top:10px;" target="_blank">导出客源</a>-->

    </div>
</div>

<script>
    function check_num() {
        var areamin = $("#area_min").val();	//最小面积
        var areamax = $("#area_max").val();	//最大面积
        var pricemin = $("#price_min").val();	//最小总价
        var pricemax = $("#price_max").val();	//最大总价

        //最小面积
        if (areamin) {
            var type = "^\\d+$";
            var re = new RegExp(type);

            if (areamin.match(re) == null) {
                $("#areamin_reminder").html("面积必须为正整数！");
                return;
            } else {
                $("#areamin_reminder").html("");
            }
        }

        //最大面积
        if (areamax) {
            var type = "^\\d+$";
            var re = new RegExp(type);

            if (areamax.match(re) == null) {
                $("#areamin_reminder").html("面积必须为正整数！");
                return;
            } else {
                $("#areamin_reminder").html("");
            }
        }

        //最小总价
        if (pricemin) {
            var type = "^\\d+$";
            var re = new RegExp(type);

            if (pricemin.match(re) == null) {
                $("#pricemin_reminder").html("总价必须为正整数！");
                return;
            } else {
                $("#pricemin_reminder").html("");
            }
        }

        //最大总价
        if (pricemax) {
            var type = "^\\d+$";
            var re = new RegExp(type);

            if (pricemax.match(re) == null) {
                $("#pricemin_reminder").html("总价必须为正整数！");
                return;
            } else {
                $("#pricemin_reminder").html("");
            }
        }

    }

    function del_cookie() {
        $.ajax({
            url: "/rent_customer/del_search_cookie/rent_customer_manage",
            type: "POST",
            dataType: "json",
            success: function (data) {
                if ('success' == data.status) {
                    window.location.href = window.location.href;
                    window.location.reload;
                }
            }
        });
    }
</script>

<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php'); ?>
