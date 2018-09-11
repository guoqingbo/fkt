<script src="<?php echo MLS_SOURCE_URL; ?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js,openWin.js"></script>
<div class="mod">
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
        <form action="/import_export/import_sell_house" enctype="multipart/form-data" target="new" method="post">
            <p class="text">上传导入文件：</p>
            <input type="text" class="text_input" id="aa" name="aa">
            <div class="f_btn" style=" background-position: 0 0; ">
                <div style="width: 44px; position: absolute; left:0; top: 0;">浏览</div>
                <input class="file" name="upfile" type="file" onchange="document.getElementById('aa').value=this.value">
            </div>
            <div class="btn_up_b" style=" background-position: 0 0; ">
                <div style="width: 44px; position: absolute; left:0; top: 0;">上传</div>
                <input class="btn_up" type="submit" name="sub" value="上传"></div>
        </form>
    </div>
    <iframe allowtransparency="true" src="<?php echo MLS_URL; ?>/blank.php" frameborder="0" scrolling="no" name="new"
            id="xx1x" height="34" width="393" style="bac"></iframe><!-- width="470"  wty---->
    <!--<p class="up_m_b_date_up" style="text-align: center">出售房源12321.xls<span class="up_s">上传成功</span>，共上传123条房源。</p>
    <p class="up_m_b_date_up" style="text-align: center">出售房源12321.xls<span class="up_e">上传失败</span>，共上传123条房源。</p> -->
    <div style="text-align:center;"><a class="btn-lv" href="javascript:void(0)"
                                       onclick="openn_sure('sell')"><span>确认导入</span></a></div>
</div>

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
        <div class="close_pop"><a href="javascript:location.reload();" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod" style="_margin-top:-10px;">
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

    //确认导入
    function openn_sure(type) {
        var id = $("#xx1x").contents().find("#tmp_id").val();
        var broker_id = $("#broker_id").val();

        if (id > 0) {
            $("#xx1x").contents().find("body").empty();
            openWin('jss_pop_sure', ajax_import(id, type, broker_id));
        } else {
            openWin('jss_pop_error');
        }
    }

    function ajax_import(id, type, broker_id) {
        var url;
        if (type == 'broker_info') {
            url = "<?php echo MLS_URL;?>/import_export/" + type + "_sure/";
        } else {
            url = "<?php echo MLS_URL;?>/import_export/" + type + "_sure/";
        }
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: {id: id, broker_id: broker_id},
            success: function (data) {
                if (data.status == 'ok') {
                    $('#jss_pop_sure .mod .inform_inner .text span').html(data.success);
                    $("#jss_pop_sure .mod .inform_inner .text img").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/r_ico.png");
                } else {
                    $('#jss_pop_sure .mod .inform_inner .text span').html(data.error);
                    $("#jss_pop_sure .mod .inform_inner .text img").attr("src", MLS_SOURCE_URL + "/mls/images/v1.0/error_ico.png");
                }
            }
        });
    }

</script>