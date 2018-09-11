<body>
    <!--应付应收添加和修改弹窗-->
    <div class="achievement_money_pop real_W580" style="display:block" id="js_add_should_pop">
        <dl class="title_top">
            <dd id='title_top1'><?=$id?'编辑':'添加'?>应收应付</dd>
        </dl>
     <!--弹出框内容-->
        <div class="add_pop_messages raal_H272">
             <div class="aad_pop_line1">
                <form action="" id='add_should'>

                <div style="width:100%;float:left;display:inline;">
                    <ul>

                        <li  class="aad_pop_line1_title aad_pop_p_B20" style="width:10%; float:left;display:inline;">应收</li>
                        <li class="aad_pop_p_B20" style="width:28%; float:left;display:inline;">
                            <p class="aad_pop_line1_title_p"><b class="resut_table_state_1">*</b>款项：</p>
                            <select class="aad_pop_select_W70" name="should_money_type">
                                <?php foreach($config['money_type'] as $key=>$val){?>
                                <option value="<?=$key;?>" <?=$flow_list['money_type']==$key?'selected':'';?>><?=$val;?></option>
                                <?php }?>
                            </select>
                            <div class="errorBox"></div>

                        </li>
                        <li class="aad_pop_p_B20" style="width:28%; float:left;display:inline;">
                            <p class="aad_pop_line1_title_p">收方：</p>
                            <select class="aad_pop_select_W70"  name="should_collect_type">
                                <option value="0">请选择</option>
                                <?php foreach($config['collect_type'] as $key=>$val){?>
                                <option value="<?=$key;?>" <?=$flow_list['collect_type']==$key?'selected':'';?>><?=$val;?></option>
                                <?php }?>
                            </select>
                        </li>
                        <li class="aad_pop_p_B20" style="width:32%; float:left;display:inline;">
                            <p class="aad_pop_line1_title_p">应收金额：</p>
                            <input type="text" class="aad_pop_select_W70 test_money" name="should_collect_money"  value="<?=$flow_list['collect_money'];?>" autocomplete="off">元
                            <div class="errorBox" style=" text-indent: 6em;"></div>
                        </li>
                    </ul>
                </div>
                <div>
                    <ul>
                        <li class="aad_pop_line1_title"  style="width:10%;float:left;display:inline;">应付</li>
                        <li class="aad_pop_p_B20" style="width:28%; float:left;display:inline;">
                            <p style=" width:3.5em;text-align:right" class="aad_pop_line1_title_p">付方：</p>
                            <select class="aad_pop_select_W70" name="should_pay_type">
                                <option value="0">请选择</option>
                                <?php foreach($config['pay_type'] as $key=>$val){?>
                                <option value="<?=$key;?>" <?=$flow_list['pay_type']==$key?'selected':'';?>><?=$val;?></option>
                                <?php }?>
                            </select>

                        </li>
                        <li style=" float:left;display:inline;">
                            <p class="aad_pop_line1_title_p">应付金额：</p>
                            <input type="text" class="aad_pop_select_W70 test_money" name="should_pay_money" value="<?=$flow_list['pay_money'];?>" autocomplete="off">元
                            <div class="errorBox" style="text-align:right;"></div>
                        </li>
                        <li style=" float:left;display:inline; width:100%;">
                             <p class="aad_pop_line1_title_p"><b class="resut_table_state_1">*</b>收付日期：</p>
                            <input type="text" class="aad_pop_select_W100 time_bg" name='should_flow_time' value="<?=$flow_list['flow_time'];?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off">
                            <div class="errorBox"  style=" text-indent: 6em;"></div>
                        </li>
                    </ul>
                </div>
                <table width="100%">
                    <tbody>
                        <tr>
                            <td width="12%" style="text-align:right" class="label aad_pop_p_T20">收付说明：</td>
                            <td width="86%" class="aad_pop_p_T20"><textarea class="aad_pop_select_textare_W" name="should_remark"><?=$flow_list['remark'];?></textarea><div class="errorBox"></div></td>
                        </tr>
                    </tbody>
                </table>
                <table width="100%" align="center">
                    <tbody><tr>
                        <td style="text-align:center" class="aad_pop_p_T20">
                            <input type="hidden" id="bargain_id" value="<?=$c_id?>">
                            <input type="hidden" id="flow_id" value="<?=$id?>">
                            <button class="btn-lv1 btn-left" type="submit">确定</button>
                            <button class="btn-hui1" type="button"  onclick="closeParentWin('js_should_pop');">取消</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </form>
             </div>
        </div>
    </div>
    <!--应付应收添加和修改弹窗-->
    <!--操作失败弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_false">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                            <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico.png"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>
</body>
