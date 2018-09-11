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
<div class="search_box clearfix" id="js_search_box">
    <form name="search_form" id="search_form" method="post" action="/signing_document/index">
        <div class="fg_box">
            <p class="fg fg_tex">发布部门：</p>
            <div class="fg" style="*padding-top:10px;">
                <select class="select " name="department_id">
                    <option value="">请选择</option>
                    <?php foreach ($departments as $key => $val) { ?>
                        <option value="<?php echo $val['department_id']; ?>" <?= ($where_cond['department_id'] == $val['department_id']) ? "selected" : "" ?>><?php echo $val['department_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">发布日期：</p>
            <div class="fg">
                <input type="text" class="fg-time" name="start_time" value="<?= $where_cond['start_time']; ?>"
                       onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" onchange="check_num();">
            </div>
            <div class="fg fg_tex03">—</div>
            <div class="fg fg_tex03">
                <input type="text" class="fg-time" name="end_time" value="<?= $where_cond['end_time']; ?>"
                       onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" onchange="check_num();">
                <span style="font-weight:bold;color:red;" id="time_reminder"></span>
            </div>
        </div>

        <div class="fg_box">
            <p class="fg fg_tex">关键字：</p>
            <div class="fg mr10" style="*padding-top:10px;">
                <select class="select w80" name="document_keyword_type"
                        value="<?= $where_cond['document_keyword_type']; ?>">
                    <option value="">请选择</option>
                    <?php if ($config['document_keyword_type']) {
                        foreach ($config['document_keyword_type'] as $key => $val) { ?>
                            <option value="<?= $key; ?>" <?= $where_cond['document_keyword_type'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                        <?php }
                    } ?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <div class="fg">
                <input type="text" class="" autocomplete="off" name="keyword" value="<?= $where_cond['keyword']; ?>">
            </div>
        </div>

        <div class="fg_box">
            <div class="fg"><a href="javascript:void(0)" class="btn"
                               onclick="sub_form('search_form');return false;"><span class="btn_inner">查询</span></a>
            </div>
        </div>
        <div class="fun_btn clearfix" id="js_fun_btn" style="display:none">
            <div class="get_page">
                <?php if (isset($page_list) && $page_list != '') {
                    echo $page_list;
                } ?>
            </div>
        </div>
    </form>
</div>
<a href="javascript:void(0);" class="btn-lv" style="position:absolute; top:53px; right:20px;"
   onclick="add_notice_pop();"><span>上传文件</span></a>

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <!--				<td class="c1" style="text-align:right"></td>-->
                <td class="c5">
                    <div class="info">序号</div>
                </td>
                <td class="c5">
                    <div class="info">文件名</div>
                </td>
                <td class="c5">
                    <div class="info">上传时间</div>
                </td>
                <td class="c5">
                    <div class="info">上传人</div>
                </td>
                <td class="c5">
                    <div class="info">操作</div>
                </td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
            <?php if ($list) {
                foreach ($list as $key => $vo) {
                    ?>
                    <tr id="tr<?php echo $vo['id']; ?>">
                        <td class="c5">
                            <div class="info"><?= $key + 1 ?></div>
                        </td>
                        <td class="c5">
                            <div class="info"><?= $vo['attachment_name'] ?></div>
                        </td>
                        <td class="c5">
                            <div class="info"><?= date('Y-m-d H:i:s', $vo['updatetime']) ?></div>
                        </td>
                        <td class="c5">
                            <div class="info"><?= $vo['department_name'] ?>/<?= $vo['signatory_name'] ?></div>
                        </td>
                        <td class="c5">
                            <div class="info c227ac6">
                                <a href="<?= $vo['attachment'] ?>" target="_blank">下载</a> |
                                <a href='javascript:void(0);' onclick="del_notice_one(<?php echo $vo['id']; ?>);">删除</a>
                            </div>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
<div id="js_fun_btn" class="fun_btn clearfix">
    <form action="" name="search_form" method="post" id="subform">
        <div class="get_page">
            <?php if (isset($page_list) && $page_list != '') {
                echo $page_list;
            } ?>
        </div>
    </form>
    <input type="checkbox" id="js_checkbox" style="float:left; margin:3px 10px 0 0;">
    <!--    <a class="btn-lan btn-left" href="javascript:void(0);" onclick="del_notice();"><span>删除</span></a></a>-->
</div>
<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->

<!--添加公告弹窗-->
<div class="pop_box_g" style="width:400px; height:210px; display:none;" id="js_add_msg_info">
    <div class="hd">
        <div class="title">上传文件</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod" style="height: 160px;overflow-y: auto">
        <div style="margin: 10px 0">
            <form id="upload_file" action="/signing_document/upload_document/"
                  enctype="multipart/form-data"
                  target="upload_file_res" method="post">

                <input type="file" name='upload_document' id='upload_document'
                       onchange="select_file_change()">　
                <!--                            <input  type="text" class=""-->
                <!--                                   id="attachment_name" name="attachment_name"-->
                <!--                                   value="">-->
                <!--                        <input class="" type="submit" name="sub" value="上传">-->
                <span class="error" id='upload_document_error' style='display:none;color: red;'>内容不能为空</span>
                <iframe name="upload_file_res" border="0" width="0" height="0"></iframe>
            </form>
            <p class="text" style="padding: 10px 0" id=""> 可上传类型为 rar、zip、doc、docx、xls、xlsx、ppt、pptx、et、pdf, 附件大小不能超过3M！
                注：如需上传多个附件，请将其先压缩成一个文件压缩包后再上传
                ！</p>

        </div>
        <div class="center mt10">
            <button class="btn-lv1 btn-left" type="button" onclick='add_notice();'>确定上传</button>
            <button class="btn-hui1 JS_Close" type="button">取消</button>
        </div>
    </div>
</div>


<!--提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="dialog_do_success_tip">操作成功！</p>
                <button type="button" class="btn-lv1 btn-mid" onclick="location.href=location">确定
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
    //
    function select_file_change() {
//        var filenamr=$(this).val();
//        $('#filename').val(filenamr);
        $('#upload_document_error').css('display', 'none');
    }
    function upload_res_pop(data) {
        var data = JSON.parse(data);
        if (data['result'] == 'ok') {
            $('#js_add_msg_info').hide();
            openWin('js_pop_do_success');
        } else {
            $('#js_add_msg_info').hide();
            openWin('js_pop_do_warning');
        }
    }
    //添加公告弹窗
    function add_notice_pop() {
        openWin('js_add_msg_info');
    }

    function add_notice() {
        var upload_document = $('#upload_document').val();
        if (upload_document.length <= 0) {
            $('#upload_document_error').css('display', 'inline');
            $('#upload_document_error').html('未选择任何文件');
            return false;
        } else {
            $('#upload_file').submit();
        }
    }

    //详情操作弹出框
    function detail_pop(id) {
        $("#edit_id").val(id);
        $.ajax({
            type: "POST",
            url: "/signing_document/detail/",
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



    //编辑公告
    function edit_notice() {
        var title = $('#d_title').val();
        var id = $('#edit_id').val();
        if (title.length > 30) {
            $('#d_error').css('display', 'inline');
            return false;
        }
        if (title.length <= 0) {
            $('#d_error').css('display', 'inline');
            $('#d_error').html('标题不能为空');
        }
        var contents = $("#d_bewrite").val();
        if (contents.length <= 0) {
            $('#error_d_bewrite').css('display', 'block');
        }
        if (title.length <= 0 || contents.length <= 0) {
            return false;
        }
        var color = $('#d_color option:selected').attr('class');
        $.ajax({
            type: "POST",
            url: "/signing_document/update_notice/",
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
    //下载

    function download_notice_one(id) {
        $.ajax({
            url: "/signing_document/document_download",
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
//                    if (data <= id.length && data != 0) {
//                        window.location.reload();
//                    } else {
//                        $("#dialog_do_itp").html("已标记");
//                        openWin('js_pop_do_success');
//                    }
            }
        });
    }
    //删除公告
    function del_notice_one(id) {
        $("#dialogSaveDiv").html("你确定要删除所选择的吗？");
        openWin('jss_pop_tip');
        $("#dialog_share").click(function () {
            $.ajax({
                url: "/signing_document/del",
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
                    url: "/signing_document/del",
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
        editor = K.create('#bewrite', {
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

