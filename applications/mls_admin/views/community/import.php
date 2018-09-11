
<!-- 出售房源入 -->
<div id="jss_pop_import" class="pop_box_g pop_see_inform" style=" display:block;" >
    <div class="mod">

        <div class="up_m_b_tex">楼盘导入功能可以将外部楼盘直接导入系统中，省去手动录入的麻烦。为保证您的楼盘顺利导入，请使用我们提供的标准模板，且勿对模板样式做任何删改。</br><a href="<?php echo MLS_SOURCE_URL;?>/xls/community.xls" target="_blank">
                <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/page_white_excel.png">点击下载出售楼盘导入模板</a>
        </div>
        <style>
            .up_m_b_tex{margin: 20px 0 40px 0;}
            .up_m_b_tex a{margin: 10px 0;}
            .up_m_b_file .text{ float:left; line-height:26px; margin: 0;}
            .up_m_b_file .text_input{width:150px;height: 24px;line-height: 24px;padding: 0 10px;border: 1px solid #E9E9E9;float: left;}
            .up_m_b_file .f_btn{ margin-left:10px;_display:inline; float:left; background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0; width:44px; height:26px; overflow:hidden; position:relative; overflow:hidden; text-align:center; line-height:26px; }
            .up_m_b_file .f_btn .file{cursor:pointer;font-size:50px;filter:alpha(opacity:0); opacity: 0; position:absolute; right:-5px; top:-5px;}
            .up_m_b_file .btn_up_b{ margin-left:10px; _display:inline; float:left; overflow:hidden; width:44px; height:26px; position:relative; line-height:26px; text-align:center;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0;}
            .up_m_b_file .btn_up_b .btn_up{ cursor:pointer; font-size:100px; position:absolute;filter:alpha(opacity:0); opacity: 0; right:-5px; top:-5px;}
            .btn-lv{text-decoration: none;padding: .5em 1em;color: #fff;background-color: #3071a9;border-color: #285e8e;}

            .btn-lv1 {
                text-decoration: none;
                padding: .5em 1em;
                color: #fff;
                background-color: #3071a9;
                border-color: #285e8e;
            }
        </style>
        <div class="up_m_b_file clearfix">
            <form action="/community/importExcel" enctype="multipart/form-data" target="new" method="post">
                <p class="text">上传导入文件：</p>
                <input type="text" class="text_input" id="aa" name="aa">
                <div class="f_btn" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">浏览</div><input class="file" name="upfile" type="file" onchange="document.getElementById('aa').value=this.value"></div>
                <div class="btn_up_b" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">上传</div><input class="btn_up" type="submit" name="sub" value="上传"></div>
            </form>
        </div>
        <iframe allowtransparency="true" src="<?php echo MLS_ADMIN_URL;?>/blank.php" frameborder="0" scrolling="no" name="new" id="xx1x" height="34" width="393" style="bac"></iframe><!-- width="470"  wty---->
        <!--<p class="up_m_b_date_up" style="text-align: center">出售房源12321.xls<span class="up_s">上传成功</span>，共上传123条房源。</p>
        <p class="up_m_b_date_up" style="text-align: center">出售房源12321.xls<span class="up_e">上传失败</span>，共上传123条房源。</p> -->
       <!-- <div style="text-align:center;margin:20px 0 0 0;"><a class="btn-lv" href="javascript:void(0)" onclick="openn_sure('sell')">确认导入</a></div>-->
    </div>
</div>
<!--确认导入表格弹窗-->
<div id="jss_pop_sure" class="pop_box_g pop_see_inform pop_no_q_up stop_pop_box" style="display: none;">
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

<!--提示导入表格弹窗-->
<div id="jss_pop_error" class="pop_box_g pop_see_inform pop_no_q_up" style="display: none;">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" style="line-height:28px;"><br>
                    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/error_ico.png">
                    <span> 请上传表格！</span>
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    //确认导入
    /*
    function openn_sure(type)
    {
        var id = $("#xx1x").contents().find("#tmp_id").val();
        var broker_id = $("#broker_id").val();
        if(id > 0){
            $("#xx1x").contents().find("body").empty();
            openWin('jss_pop_sure',ajax_import(id,type,broker_id));
        }else{
            openWin('jss_pop_error');
        }
    }
    */
</script>
