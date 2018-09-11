<body>
<form name="search_form" id='search_form' action ="" method="post">
<div class="pop_box_g pop_box_cooperation pop_box_g_border_none" id="js_pop_box_cooperation02" style="display:block;">
    <!--公用合同信息-->
    <?php $this->view('cooperate/cooperate_common_info');?>
    <div class="checkbox_x">
        <label><input type="checkbox" disabled="disabled" id='agreement' checked name='agreement'>我已阅读并同意</label>
        <a href="javascript:void(0)" onclick="openWin('js_pop_protocol')">《合作协议》</a>
    </div>
    <!--<div class="btn_box">
       <button type="button" class="grey_btn JS_Close" onclick="window.parent.closePopFun('js_pop_box_cooperation');">关闭</button>
    </div>-->
    </div>
</div>
</form>
<?php $this->view('cooperate/cooperate_common_dialog_box');?>
<!--载入合作协议页面-->
<?php $this->view('cooperate/cooperative_agreements');?>