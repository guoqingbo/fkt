<body>
<div style="width: 400px; height: 250px; display: block" class="show pop_box_g">
    <div class="hd">
        <div class="title">权证步骤详情</div>
    </div>
    <div class="mod">
        <div class="create_newb_wrapall paddiing_down">

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">成交编号：</div>
                <span class="name_right fl" id="list_storename"><?=$bargain['number'];?></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">步骤：</div>
                <span class="name_right fl" id="list_truename"><?=$stage_conf[$transfer_list['step_id']]['text'];?></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">流程：</div>
                <span class="name_right fl" id="list_phone"><?=$transfer_list['stage_name'];?></span>
            </div>
            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">添加人：</div>
                <span class="name_right fl" id="list_p_name"><?=$transfer_list['department_name'];?>  <?=$transfer_list['signatory_name'];?></span>
            </div>
            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">备注：</div>
                 <span class="name_right fl" id="list_p_name"><?=$transfer_list['remark'];?></span>
            </div>
            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">提醒时间：</div>
                 <span class="name_right fl" id="list_p_name"><?=$transfer_list['remind_time'];?></span>
            </div>
            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">提醒对象：</div>
                 <span class="name_right fl" id="list_p_name"><?=$transfer_list['remind_department_name'];?>  <?=$transfer_list['remind_signatory_name'];?></span>
            </div>
            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">提醒内容：</div>
                 <span class="name_right fl" id="list_p_name"><?=$transfer_list['remind_remark'];?></span>
            </div>
        </div>

        <div style="width:120px; margin:10px auto 0; height:auto; overflow:hidden; zoom:1;">
            <button class="btn-lv1 btn-left" style="float:left;" type="button" onclick="closeParentWin('js_transfer_pop');">确定</button>
        </div>
    </div>
</div>
</body>
