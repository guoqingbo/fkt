<script>
    window.parent.addNavClass(1);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if (isset($user_menu) && $user_menu != '') {
        echo $user_menu;
    } ?>
</div>
<!--<div id="js_search_box">-->
<!--    <div  class="shop_tab_title">-->
<!--        --><?php //if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
<!--    </div>-->
<!--</div>-->
<!--<a href="javascript:void(0);" class="btn-lv" style="position:absolute; top:48px; right:20px;" onclick="add_notice_pop();"><span>发布公告</span></a>-->
<a href="javascript:void(0);" class="btn-lv" style="position:absolute; top:48px; right:20px;"
   onclick="location.href='/signing_notice/index/'"><span>返回</span></a>


<div class="inner shop_inner" id="js_inner" style="overflow: auto">
    <div class="table-notice">
        <table>
            <tr>
                <td class="td-left"><span style="width: 50px">分类：</span></td>
                <td class="">
                    <select class="input" id='notice_type' name="notice_type" style="height: 25px">
                        <?php if ($config["notice_type"]) {
                            foreach ($config["notice_type"] as $key => $val) { ?>
                                <option value='<?= $key ?>' <?= $notice_detail['notice_type'] == $key ? 'selected' : '' ?>><?= $val ?></option>
                            <?php }
                        } ?>e
                    </select>
                </td>
            </tr>
            <tr>
                <td class="td-left">收件对象：</td>
                <td>
                    <input type="text" id="receipt_object_name" name='receipt_object_name'
                           value="<?= $notice_detail['receipt_object_name'] ? $notice_detail['receipt_object_name'] : '请选择' ?>"
                           class="input">　
                    <select disabled style="border: none;margin-left: -40px"> </select>
                    <input type="hidden" id="object_name" name='object_name'
                           value="<?= $notice_detail['receipt_object_name']; ?>">　
                    <input type="hidden" id="receipt_object_type" name="receipt_object_type"
                           value="<?= $notice_detail['receipt_object_type']; ?>"><!-- 全部-->
                    <input type="hidden" id="all" name="all" value=""><!-- 全部-->
                    <input type="hidden" id="receipt_object_district" name="receipt_object_district"
                           value="<?= $notice_detail['receipt_object_district']; ?>"><!-- 区域-->
                    <input type="hidden" id="receipt_object_agency" name="receipt_object_agency"
                           value="<?= $notice_detail['receipt_object_agency']; ?>"><!-- 门店-->
                    <div id="treediv"
                         style="display:none;position:absolute;overflow:auto;  width: 435px;height:200px;  padding: 5px;background: #fff;color: #fff;border: 1px solid #cccccc">
                        <script language="JavaScript" type="text/JavaScript">
                            //树代码
                            //
                            mydtree = new dTree('mydtree', '<?php echo MLS_SOURCE_URL;?>/common/images/dtreeckimg/', 'no', 'no');

                            mydtree.add(0, -1, "全部", "javascript:setvalue('0','全部','all')", "", "_self", false);
                            <?php if (is_array($district_agency)) {
                            foreach ($district_agency as $key => $val) { ?>
                            mydtree.add(<?= $val["district_id"] ?>, 0, "<?= $val["district_name"] ?>", "javascript:setvalue(<?= $val["district_id"] ?>,'<?= $val["district_name"] ?>','district')", "", "_self", false);
                            <?php }} ?>
                            <?php if (is_array($agencys)) {
                            foreach ($agencys as $agency_key => $agency_val) { ?>
                            mydtree.add(<?= $agency_val["id"] + 100 ?>, <?= $agency_val["dist_id"] ?>, "<?= $agency_val["name"] ?>", "javascript:setvalue(<?= $agency_val["id"] ?>,'<?= $agency_val["name"] ?>','agency')", "", "_self", false);
                            <?php }} ?>
                            document.write(mydtree);
                        </script
                    </div>
                </td>
            </tr>
            <tr>
                <td class="td-left">公告文号：</td>
                <td>
                    <input type="text" name='notice_number' id='notice_number' class="input"
                           value="<?= $notice_detail['notice_number']; ?>">　
                </td>
            </tr>
            <tr>
                <td class="td-left">标题：</td>
                <td><input type="text" name='phonenum' id='title' class="input" value="<?= $notice_detail['title']; ?>">　
                    <select class="select" id='color' name="color" style="color:<?= $notice_detail['color'] ?>">
                        <option value=''>默认色</option>
                        <?php if ($config["color"]) {
                            foreach ($config["color"] as $key => $val) { ?>
                                <option style="color:<?= $key ?>"
                                        value='<?= $key ?>' <?= $notice_detail['color'] == $key ? 'selected' : '' ?>><?= $val ?></option>
                            <?php }
                        } ?>
                    </select>
                    <!--                    <select class="select" id='color'>-->
                    <!--                        <option value='0' class=''>默认色</option>-->
                    <!--                        <option class="cf00">红色</option>-->
                    <!--                        <option class="cf90">橙色</option>-->
                    <!--                    </select>-->
                    <span class="error" id='error' style='display:none'>标题不能超过30字</span>
                </td>
            </tr>
            <tr>
                <td class="td-left">置顶排序：</td>
                <td><input type="text" name='top_rank' id='top_rank' class="input"
                           value="<?= $notice_detail['top_rank']; ?>">　
                </td>
            </tr>
            <tr>
                <td class="td-left">置顶过期时间：</td>
                <td><input type="text" name='top_rank_deadline' id='top_rank_deadline' class="input"
                           value="<?= $notice_detail['top_rank_deadline']; ?>">　
                </td>
            </tr>

            <tr>
                <td class="td-left">选择附件：</td>
                <td>

                    <form id="upload_file" action="/signing_notice/upload_attachment/<?= $id; ?>"
                          enctype="multipart/form-data"
                          target="upload_file_res" method="post">

                        <!--                        <div class="f_btn" style=" background-position: 0 0; "><div style="width: 44px;">浏览</div><input class="file" name="upfile" type="file" onchange="document.getElementById('aa').value=this.value"></div>-->
                        <!--                        <div class="btn_up_b" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">上传</div><input class="btn_up" type="submit" name="sub" value="上传"></div>-->
                        <input type="hidden" name="id" value="">
                        <input type="file" name='attachment' id='attachment' class="input"
                               onchange="document.getElementById('attachment_name').value=this.value">　
                        <input style="margin-left:-384px;height: 25px;border: 0;" type="text" class=""
                               id="attachment_name" name="attachment_name"
                               value="<?= $notice_detail['attachment_name']; ?>">
                        <!--                        <input class="" type="submit" name="sub" value="上传">-->
                        <iframe name="upload_file_res" border="0" width="0" height="0"></iframe>
                    </form>
                    <span>（可上传类型为 rar、zip、doc、docx、xls、xlsx、ppt、pptx、et、pdf, 附件大小不能超过3M！ 注：如需上传多个附件，请将其先压缩成一个文件压缩包后再上传）
</span>
                </td>
            </tr>
            <tr>
                <td class="td-left">内容：</td>
                <td><textarea class="input" id='bewrite' name='bewrite'><?= $notice_detail['contents']; ?></textarea>
                </td>
            </tr>

            <tr>
                <td class="td-left"></td>
                <td>
                    <span class="error" id='error_bewrite' style='display:none'>内容不能为空</span>
                </td>
            </tr>
        </table>
    </div>
    <div class="center mt10">
        <?php if ($id > 0) { ?>
            <button class="btn-lv1 btn-left" type="button" onclick='add_notice(<?= $id; ?>);'>确定</button>
        <?php } else { ?>
            <button class="btn-lv1 btn-left" type="button" onclick='add_notice();'>确定</button>
        <?php } ?>
        <button class="btn-hui1 JS_Close" type="button">取消</button>
    </div>
</div>
<!--<div id="js_fun_btn" class="fun_btn clearfix">-->
<!--	<form action="" name="search_form" method="post" id="subform">-->
<!--	<div class="get_page">-->
<!--		--><?php //if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
<!--	</div>-->
<!--    </form>-->
<!--	<input type="checkbox" id="js_checkbox" style="float:left; margin:3px 10px 0 0;">-->
<!--    <a class="btn-lan btn-left" href="javascript:void(0);" onclick="del_notice();"><span>删除</span></a></a>-->
<!--</div>-->
<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->


<!--详情页弹窗-->
<!--<input type='hidden' id='edit_broker_id'>-->
<!--<input type='hidden' id='edit_id'>-->
<!--<div class="pop_box_g" style="width:760px; height:470px; display:none;" id="js_see_msg_info">-->
<!--    <div class="hd">-->
<!--        <div class="title">公告详情</div>-->
<!--        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>-->
<!--    </div>-->
<!--    <div class="mod">-->
<!--        <div class="table-notice">-->
<!--            <table>-->
<!--                <tr>-->
<!--                    <td class="td-left">分类：</td>-->
<!--                    <td>-->
<!--                        <select class="select" id='notice_type' name="notice_type">-->
<!--                            --><?php //if ($config["notice_type"]) {
//                                foreach ($config["notice_type"] as $key => $val) { ?>
<!--                                    <option value='--><? //= $key ?><!--'>--><? //= $val ?><!--</option>-->
<!--                                --><?php //}
//                            } ?>
<!--                        </select>-->
<!--                    </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td class="td-left">收件对象：</td>-->
<!--                    <td>-->
<!--                        <input type="text" name='d_receipt_object_name ' id='d_receipt_object_name' class="input">　-->
<!--                        <ul style="position: absolute">-->
<!--                            <li>全部</li>-->
<!---->
<!--                            <li>-->
<!--                                <span>西湖区</span>-->
<!--                                <ul>-->
<!--                                    <li>豪门店</li>-->
<!--                                </ul>-->
<!--                            </li>-->
<!--                        </ul>-->
<!--                    </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td class="td-left">公告文号：</td>-->
<!--                    <td>-->
<!--                        <input type="text" name='d_notice_number' id='d_notice_number' class="input">　-->
<!--                    </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td class="td-left">标题：</td>-->
<!--                    <td><input type="text" name='phonenum' id='d_title' class="input">　-->
<!--                        <select class="select" id='d_color'>-->
<!--                            <option value='0' class=''>默认色</option>-->
<!--                            <option class="cf00">红色</option>-->
<!--                            <option class="cf90">橙色</option>-->
<!--                        </select>-->
<!--                        <span class="error" id='d_error' style='display:none'>标题不能超过30字</span>-->
<!--                    </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td class="td-left">置顶排序：</td>-->
<!--                    <td><input type="text" name='top_rank' id='top_rank' class="input">　-->
<!--                    </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td class="td-left">置顶过期时间：</td>-->
<!--                    <td><input type="text" name='top_rank_deadline' id='top_rank_deadline' class="input">　-->
<!--                    </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td class="td-left">选择附件：</td>-->
<!--                    <td><input type="file" name='attachment' id='attachment' class="input">　-->
<!--                    </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td class="td-left">内容：</td>-->
<!--                    <td><textarea class="input" id="d_bewrite" name='d_bewrite'></textarea></td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td class="td-left"></td>-->
<!--                    <td>-->
<!--                        <span class="error" id='error_d_bewrite' style='display:none'>内容不能为空</span>-->
<!--                    </td>-->
<!--                </tr>-->
<!--            </table>-->
<!--        </div>-->
<!--        <div class="center mt10">-->
<!--            <button class="btn-lv1 btn-left " type="button" onclick='edit_notice();'>确定</button>-->
<!--            <button class="btn-hui1 JS_Close" type="button">取消</button>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="dialog_do_success_tip">操作成功！</p>
                <button type="button" class="btn-lv1 btn-mid" onclick="location.href='/signing_notice/index/'">确定
                </button>
            </div>
        </div>
    </div>
</div>
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick='window.location.href=location' title="关闭"
               class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="dialog_do_warnig_tip">操作失败！</p>
            </div>
        </div>
    </div>
</div>
<!--询问操作确定弹窗-->
<div id="jss_pop_tip" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="dialogSaveDiv"></p><br/>
                <button type="button" id='dialog_share' class="btn-lv1 btn-left JS_Close">确定</button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
                <input type="hidden" name='ci_id' id='rowid' value=''>
                <input type="hidden" name='secret_key' id='secret_key' value=''>
                <input type="hidden" name='atction_type' id='atction_type' value=''>
                <input type="hidden" name='do_type' id='do_type' value=''>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    //生成弹出层的代码
    <!-- 弹出层-->
    xOffset = 0;//向右偏移量
    yOffset = 25;//向下偏移量
    var toshow = "treediv";//要显示的层的id
    var target = "receipt_object_name";//目标控件----也就是想要点击后弹出树形菜单的那个控件id
    $("#" + target).click(function () {
        $("#" + toshow)
            .css("position", "absolute")
            .css("left", $("#" + target).position().left + xOffset + "px")
            .css("top", $("#" + target).position().top + yOffset + "px").show();
    });
    //关闭层
    $("#closed").click(function () {
        $("#" + toshow).hide();
    });
    //判断鼠标在不在弹出层范围内
    function checkIn(id) {
        var yy = 20;   //偏移量
        var str = "";
        var x = window.event.clientX;
        var y = window.event.clientY;

        var obj = $("#" + id)[0];
        if (x > obj.offsetLeft && x < (obj.offsetLeft + obj.clientWidth) && y > (obj.offsetTop - yy) && y < (obj.offsetTop + obj.clientHeight)) {
            return true;
        } else {
            return false;
        }
    }
    //点击body关闭弹出层
    $(document).click(function () {
        var is = checkIn("treediv");
        if (!is) {
            $("#" + toshow).hide();
        }
    });
    <!-- 弹出层-->
    //生成弹出层的代码
    //点击菜单树给文本框赋值------------------菜单树里加此方法
    function setvalue(id, name, type) {
        $("#receipt_object_name").val(name);
        $("#object_name").val(name);
        $("#all").val("");
        $("#receipt_object_district").val("");
        $("#receipt_object_agency").val("");
        $("#receipt_object_type").val(type);
        if (type == "all") {
            $("#all").val(type);
        } else if (type == "district") {
            $("#receipt_object_district").val(id);
        } else if (type == "agency") {
            $("#receipt_object_agency").val(id);
        }
        $("#treediv").hide();
    }
</script>

<script charset='utf-8' src='<?php echo MLS_SOURCE_URL; ?>/common/js/kindeditor-4.1.10/kindeditor-min.js'></script>
<script charset='utf-8' src='<?php echo MLS_SOURCE_URL; ?>/common/js/kindeditor-4.1.10/lang/zh_CN.js'></script>
<script>
    $('#title').keyup(function () {
        var title = $('#title').val();
        if (title.length > 30) {
            $('#error').css('display', 'inline');
        } else {
            $('#error').css('display', 'none');
        }
    })

    //添加公告弹窗
    function add_notice_pop() {
        $('#title').val('');
        $('#color').val('0');
        $('#error').css('display', 'none');
        $('#error_bewrite').css('display', 'none');
        //alert($("textarea[name='bewrite']").val());
        KindEditor.ready(function (K) {
            K.html('#bewrite', '');
        });
        openWin('js_add_msg_info');
    }

    function add_notice(id=0) {
        var title = $('#title').val();
        if (title.length > 30) {
            $('#error').css('display', 'inline');
            return false;
        }
        if (title.length <= 0) {
            $('#error').css('display', 'inline');
            $('#error').html('标题不能为空');
        }
        var contents = $("#bewrite").val();
        if (contents.length <= 0) {
            $('#error_bewrite').css('display', 'block');
        }
        if (title.length <= 0 || contents.length <= 0) {
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/signing_notice/add_notice/",
            data: {
                id: id,
                notice_type: $("select[name='notice_type']").val(),
                receipt_object_name: $("input[name='object_name']").val(),
                receipt_object_type: $("input[name='receipt_object_type']").val(),
                receipt_object_district: $("input[name='receipt_object_district']").val(),
                receipt_object_agency: $("input[name='receipt_object_agency']").val(),
                notice_number: $("input[name='notice_number']").val(),
                top_rank: $("input[name='top_rank']").val(),
                top_rank_deadline: $("input[name='top_rank_deadline']").val(),

                title: $('#title').val(),
                contents: $("#bewrite").val(),
                color: $("select[name='color']").val(),
            },
            dataType: "json",
            cache: false,
            error: function () {
                alert("系统错误");
                return false;
            },
            success: function (data) {
                if (data['result'] == 'ok') {
                    if ($("#attachment").val()) {
                        $("input[name='id']").val(data["id"]);
                        $("#upload_file").submit();
                    }
                    $('#js_add_msg_info').hide();
                    openWin('js_pop_do_success');
                } else {
                    $('#js_add_msg_info').hide();
                    openWin('js_pop_do_warning');
                }

            }
        });
    }
    //详情操作弹出框
    function detail_pop(id) {
        $("#edit_id").val(id);
        $.ajax({
            type: "POST",
            url: "/signing_notice/detail/",
            data: "id=" + id,
            dataType: "json",
            cache: false,
            error: function () {
                alert("系统错误");
                return false;
            },
            success: function (data) {
                $('#d_title').val("");
                KindEditor.ready(function (K) {
                    K.html('#d_bewrite', '');
                });
                $('#d_color').val("");
                $('#d_title').val(data['title']);
                KindEditor.ready(function (K) {
                    K.html('#d_bewrite', data['contents']);
                });
                $("#d_color").find("option[class='" + data['color'] + "']").attr("selected", true);
                openWin('js_see_msg_info');

            }
        });
    }


    //添加公告弹窗
    function add_notice_pop() {
        $('#title').val('');
        $('#color').val('0');
        $('#error').css('display', 'none');
        $('#error_bewrite').css('display', 'none');
        //alert($("textarea[name='bewrite']").val());
        KindEditor.ready(function (K) {
            K.html('#bewrite', '');
        });
        openWin('js_add_msg_info');
    }

    //编辑公告
    function edit_notice() {
        var title = $('#d_title').val();
        var id = $("#edit_id").val(id);
        if (title.length > 30) {
            $('#d_error').css('display', 'inline');
            return false;
        }
        if (title.length <= 0) {
            $('#d_error').css('display', 'inline');
            $('#d_error').html('标题不能为空');
        }
        var contents = $("#bewrite").val();
        if (contents.length <= 0) {
            $('#error_bewrite').css('display', 'block');
        }
        if (title.length <= 0 || contents.length <= 0) {
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/signing_notice/update_notice/",
            data: {title: title, contents: contents, color: color, id: id},
            dataType: "json",
            cache: false,
            error: function () {
                alert("系统错误");
                return false;
            },
            success: function (data) {
                if (data['result'] == 'ok') {
                    $('#js_see_msg_info').hide();
                    openWin('js_pop_do_success');
                } else {
                    $('#js_see_msg_info').hide();
                    openWin('js_pop_do_warning');
                }

            }
        });
    }
    //删除公告
    function del_notice_one(id) {
        $("#dialogSaveDiv").html("你确定要删除所选择的吗？");
        openWin('jss_pop_tip');
        $("#dialog_share").click(function () {
            $.ajax({
                url: "/signing_notice/del",
                type: "post",
                dataType: "json",
                data: {
                    id: id
                },
                cache: false,
                error: function () {
                    alert("系统错误");
                    return false;
                },
                success: function (data) {
                    //alert(data);
                    if (data <= id.length && data != 0) {
                        window.location.reload();
                    } else {
                        $("#dialog_do_itp").html("已标记");
                        openWin('js_pop_do_success');
                    }
                }
            });
        });

    }
    function del_notice() {
        var id = [];
        var select_num = 0;
        $("input[name='items']").each(function () {
            if ($(this).attr("checked")) {
                id.push($(this).val());
                select_num++;
            }
        });

        if (select_num == 0) {
            $("#dialog_do_warnig_tip").html("请选择要标记的内容！");
            openWin('js_pop_do_warning');
            return false;
        } else {
            $("#dialogSaveDiv").html("你确定要删除所选择的吗？");
            openWin('jss_pop_tip');
            $("#dialog_share").click(function () {
                $.ajax({
                    url: "/signing_notice/del",
                    type: "post",
                    dataType: "json",
                    data: {
                        id: id
                    },
                    cache: false,
                    error: function () {
                        alert("系统错误");
                        return false;
                    },
                    success: function (data) {
                        //alert(data);
                        if (data <= id.length && data != 0) {
                            window.location.reload();
                        } else {
                            $("#dialog_do_itp").html("已标记");
                            openWin('js_pop_do_success');
                        }
                    }
                });
            });
        }
    }

    //阻止checkbox点选触发tr事件
    $(':checkbox.checkbox').click(function (evt) {
        var is = $(this).attr('checked');
        var xid = $(this).attr('xid');

        if (is) {
            $(':checkbox[xtype="' + xid + '"]').attr('checked', 'checked');
        }
        else {
            $(':checkbox[xtype="' + xid + '"]').removeAttr('checked');
        }
        // 阻止冒泡
        evt.stopPropagation();
    });

    //页面编辑器
    var editor;
    KindEditor.ready(function (K) {
        window.editor = K.create('#bewrite', {
            width: '616px',
            minWidth: 616,
            height: '278px',
            resizeType: 0,
            newlineTag: "p",
            allowPreviewEmoticons: false,
            allowImageUpload: false,
            items: ['fontname', 'fontsize', '|', 'forecolor',
                'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|',
                'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                'insertunorderedlist', '|', 'wordpaste', '|', 'image'],
            afterBlur: function () {

                this.sync();
                var bewrite = $("textarea[name='bewrite']").val();
                if (bewrite.length > 0) {
                    $('#error_bewrite').css('display', 'none');
                }
            }
        });
    });
    KindEditor.ready(function (K) {
        editor = K.create('#d_bewrite', {
            //readonlyMode : true,
            width: '678px',
            height: '278px',
            resizeType: 0,
            newlineTag: "p",
            allowPreviewEmoticons: false,
            allowImageUpload: false,
            items: ['fontname', 'fontsize', '|', 'forecolor',
                'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|',
                'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                'insertunorderedlist', '|', 'wordpaste', '|', 'image'],
            afterBlur: function () {
                this.sync();
                var bewrite = $("textarea[name='d_bewrite']").val();
                if (bewrite.length > 0) {
                    $('#error_d_bewrite').css('display', 'none');
                }
            }
        });
    });
</script>

