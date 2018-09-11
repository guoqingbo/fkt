<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0)" onclick="$('#search_form').submit();return false;" title="关闭"
               class="JS_Close iconfont">&#xe60c;</a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                  <p class="text" ><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/r_ico.png" style="margin-right:10px;"><span  id='dialog_do_itp'></span></p>
                  <button type="button" class="btn-lv1 btn-left JS_Close" onclick="$('#search_form').submit();return false;">确定</button>
            </div>
        </div>
    </div>
</div>

<!--操作结果弹出警告-->
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/s_ico.png" style="margin-right:10px;"><span id='dialog_do_warnig_tip'></span></p>
            </div>
				<a href="javascript:void(0);" id="sure_yes" class="btn-lv1 btn-mid btn_qd_text JS_Close" style="margin:0 auto !important">确定</a>
        </div>
    </div>
</div>

<!--询问操作确定弹窗-->
<div id="jss_pop_tip" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" id="dialogSaveDiv"></p>
                    <button type="button" id = 'dialog_share' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                    <input type ="hidden" name='ci_id' id = 'rowid' value = ''>
                    <input type ="hidden" name='secret_key' id = 'secret_key' value = ''>
                    <input type ="hidden" name='atction_type' id = 'atction_type' value = ''>
                    <input type ="hidden" name='do_type' id = 'do_type' value = ''>
                </div>
            </div>
    </div>
</div>
<!--余额不足弹出提示框-->
<div id="js_pop_do_skip" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0)" title="关闭" class="JS_Close iconfont">&#xe60c;</a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/r_ico.png"
                                     style="margin-right:10px;"><span id='dialog_do_skip_tip'></span></p>
                <button type="button" class="btn-bg btn-left JS_Close"
                        onClick="hidden_call_button('<?php echo $data_info['id']; ?>','<?php echo $data_info['telno1']; ?>',1)">
                    跳过,继续拨打
                </button>
            </div>
        </div>
    </div>
</div>
<!--缴月租弹出提示框-->
<div id="js_pop_do_rent_monthly" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0)" title="关闭" class="JS_Close iconfont">&#xe60c;</a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/r_ico.png"
                                     style="margin-right:10px;"><span id='dialog_do_monthly_tip'></span></p>
                <button type="button" class="btn-lv1 btn-left JS_Close"
                        onClick="hidden_call_button('<?php echo $data_info['id']; ?>','<?php echo $data_info['telno1']; ?>',2)">
                    跳过
                </button>
            </div>
        </div>
    </div>
</div>

<!--正常显示隐号拨打提示框-->
<div id="js_pop_do_normal" class="pop_box_g pop_see_inform pop_no_q_up" style="width: 300px">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0)" title="关闭" class="iconfont" onclick="openWin('jss_pop_unbindnumber');">
                &#xe60c;</a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner" style="padding: 0;">
                <p class="text" style="padding: 0;color: #ff0000;line-height: 20px;font-size: 10px">
                    <span id='rent_monthly_tip'></span>
                </p>
                <p class="text" style="padding: 0;line-height: 20px">
                    <span id='dialog_do_per_talk_time'></span>
                </p>
                <p class="text" style="padding: 0;"><span class="iconfont"
                                                          style="margin-right: 10px;color:#45C050;font-size:26px">&#xe66d;</span><span
                            id='dialog_do_normal_tip'
                            style="font-size:26px;color:#36CC32"></span></p>
                <p class="text">
                    <span id='dialog_do_blind_time_tip'></span>
                    <a href="javascript:void(0);" onclick="$('#dialog_do_balance_tip').toggle();">查看余额</a>
                </p>
                <p class="text" style="padding: 0;"><span id='dialog_do_balance_tip'
                                                          style="color:#36CC32;display:none"></span></p>
            </div>
        </div>
    </div>
</div>
<!--解除绑定询问操作确定弹窗-->
<div id="jss_pop_unbindnumber" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="">
                    <span id='unbindnumber'></span></span>
                </p>
                <button type="button" id='unbindnumber_sure' class="btn-lv1 btn-left JS_Close" onclick="unbindnumber()">
                    确定
                </button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
                <input type="hidden" name="bindid" id='bindid' value="">
            </div>
        </div>
    </div>
</div>