<body>
<style type="text/css">

  .fl{line-height:24px;}
</style>
<div style="width: 400px; height: 250px; display:inline;float:left;" class="pop_box_g">
    <div class="hd">
        <div class="title">权证步骤详情</div>
    </div>
    <div class="mod">
        <div class="create_newb_wrapall paddiing_down">

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">合同编号：</div>
                <span class="name_right fl" id="list_storename"><?=$contract['number'];?></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">步骤：</div>
                <span class="name_right fl" id="list_truename"><?=$stage_conf[$warrant_list['step_id']]['text'];?></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">流程：</div>
                <span class="name_right fl" id="list_phone"><?=$warrant_list['stage_name'];?></span>
            </div>
            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">结案人：</div>
                <span class="name_right fl" id="list_p_name"><?=$warrant_list['complete_signatory_name'];?></span>
            </div>
            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl">备注：</div>
                 <span class="name_right fl" id="list_p_name"><?=$warrant_list['remark'];?></span>
            </div>
        </div>

<!--        <div style="width:120px; margin:10px auto 0; height:auto; overflow:hidden; zoom:1;padding-left:50px;">-->
<!--            <button class="btn-lv1 btn-left" style="float:left;" type="button" onclick="closeParentWin('js_warrant_pop1');">确定</button>-->
<!--        </div>-->
    </div>
</div>
</body>
