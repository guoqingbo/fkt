<!-- 没有权限页面 -->
<div id="js_pop_do_purview_none" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <div class="text-wrap">
                    <table>
                        <tr>
                            <td>
                                <div class="img"><img id="imgg" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico.png"></div>
                            </td>
                            <td>
                                <span class="text" id='dialog_do_purview_none_tip'>对不起，您没有访问权限!</span>
                            </td>
                        </tr>
                    </table>
                </div>
              <a href="javascript:void(0);" id="sure_yes" class="btn-lv1 btn-mid btn_qd_text JS_Close" style="margin:0 auto !important">确定</a>
            </div>
        </div>
    </div>
</div>
<!--遮罩 loading-->
<img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/loading.gif" id='mainloading'>
<?php
if ( isset($footer_js) && $footer_js != '')
{
    echo $footer_js;
}
if ( isset($fuck_js) && $fuck_js != '')
{
    echo $fuck_js;
}

?>
</body>
</html>
