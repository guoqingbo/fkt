<body>
    <!--应付应收添加和修改弹窗-->
    <div class="achievement_money_pop real_W580" style="display:block" id="js_add_should_pop">
        <dl class="title_top">
            <dd id='title_top1'>应收应付详情</dd>
        </dl>
     <!--弹出框内容-->
        <div class="add_pop_messages raal_H272">	
             <div class="aad_pop_line1">
                <form action="" id='add_should'>

                <div style="width:100%;float:left;display:inline;">
                    <ul>

                        <li  class="aad_pop_line1_title aad_pop_p_B20" style="width:10%; float:left;display:inline;">应收</li>
                        <li class="aad_pop_p_B20" style="width:28%; float:left;display:inline;line-height:24px;">
                            <p class="aad_pop_line1_title_p"  style=" width:3.5em;text-align:right"><b class="resut_table_state_1"></b>款项：</p>
                            <?=$config['money_type'][$detail['money_type']];?>

                        </li>
                        <li class="aad_pop_p_B20" style="width:28%; float:left;display:inline;line-height:24px;">
                            <p class="aad_pop_line1_title_p" style=" width:5em;text-align:right">收方：</p>
                             <?=$config['collect_type'][$detail['collect_type']];?>
                        </li>
                        <li class="aad_pop_p_B20" style="width:32%; float:left;display:inline;line-height:24px;">
                            <p class="aad_pop_line1_title_p">应收金额：</p>
                             <?=$detail['collect_money']?strip_end_0($detail['collect_money']).'元':'';?>

                        </li>
                        
                    </ul>

                </div>
                <div>
                    <ul>

                        <li class="aad_pop_line1_title"  style="width:10%;float:left;display:inline;">应付</li>
                        <li class="aad_pop_p_B20" style="width:28%; float:left;display:inline;line-height:24px;">
                            <p style=" width:3.5em;text-align:right" class="aad_pop_line1_title_p">付方：</p>
                            <?=$config['pay_type'][$detail['pay_type']];?>

                        </li>
                        <li style=" float:left;display:inline;line-height:24px;">
                            <p class="aad_pop_line1_title_p">应付金额：</p>
                            <?=$detail['pay_money']?strip_end_0($detail['pay_money']).'元':'';?>	
                        </li>
                        <li style=" float:left;display:inline; width:100%;line-height:24px;">
                             <p class="aad_pop_line1_title_p"  style=" width:12%;text-align:right"><b class="resut_table_state_1"></b>收付日期：</p>
                            <?=$detail['flow_time'];?>

                        </li>
                    </ul>

                </div>
                <table width="100%">
                    <tbody>
                        <tr>
                            <td width="12%" style="text-align:right" class="label aad_pop_p_T20">收付说明：</td>
                            <td width="86%" class="aad_pop_p_T20"><?=$detail['remark'];?></td>
                        </tr>
                    </tbody>
                </table>
                <table width="100%" align="center">
                    <tbody><tr>
                    <td style="text-align:center" class="aad_pop_p_T20">
                    <button class="btn-lv1 btn-left" type="botton" onclick="closeParentWin('js_should_pop');">确定</button>
                    </td>
                    </tr>
                    </tbody>
                </table>
                </form>
             </div>
        </div>
    </div>
</body>
