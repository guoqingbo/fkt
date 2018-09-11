<body>
<!--实收实付添加弹窗开始-->
<div class="achievement_money_pop real_W580" style="display: block;">
    <dl class="title_top">
        <dd id='title_top'>税费详情</dd>
    </dl>
    <!--弹出框内容-->
    <div class="add_pop_messages raal_H356">
        <div class="aad_pop_line1">
            <form action="" id="add_replace" method="post">
                <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
                    <ul>
                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">收付对象：</p>
                            <?= $detail['target_name']?>
                        </li>
                        <li class="aad_pop_line1_title "
                            style="width:40%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">身份证号：</p>
                            <?= $detail['target_idcard']?>
                        </li>
                    </ul>
                </div>
                <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
                    <ul>
                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">银行卡号：</p>
                            <?= $detail['bank_account']?>
                        </li>
                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">类型：</p>
                            <?= $config['replace_type'][$detail['replace_type']]; ?>
                        </li>
                    </ul>
                </div>
                <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
                    <ul>
                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">收款方：</p>
                            <?= $detail['collect_person']?>
                        </li>
                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">金额：</p>
                            <?= $detail['money_number'] ? strip_end_0($detail['money_number']) . '元' : ''; ?>
                        </li>
                    </ul>
                </div>
                <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
                    <ul>

                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">费用类别：</p>
                            <?= $config['money_type'][$detail['money_type']]; ?>
                        </li>
                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">费用说明：</p>
                            <?= $detail['money_name']; ?>
                        </li>
                    </ul>
                </div>
                <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
                    <ul>
                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">支付方式：</p>
                            <?= $config['pay_type'][$detail['pay_type']]; ?>
                        </li>
                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">凭证号码：</p>
                            <?= $detail['certificate_number']; ?>
                        </li>

                    </ul>
                </div>
                <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
                    <ul>
                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">收付日期：</p>
                            <?= $detail['flow_time']; ?>
                        </li>
                    </ul>
                </div>
                <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
                    <ul>
                        <li class="aad_pop_line1_title "
                            style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">备注：</p>
                            <?= $detail['remark']; ?>
                        </li>
                    </ul>
                </div>
                <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
                    <ul>
                        <li class="aad_pop_line1_title "
                            style="width:60%;float:left;display:inline;font-weight:normal;line-height:24px;">
                            <p class="aad_pop_line1_title_p">建档：</p>
                            <?= $detail['entry_department_name'] . " " . $detail['entry_signatory_name'] . " " . date("Y-m-d H:i:d", $detail['entry_time']); ?>
                        </li>
                    </ul>
                </div>

                <table width="100%" align="center">
                    <tbody>
                    <tr>
                        <td style="text-align:center" class="aad_pop_p_T20">
                            <button class="btn-lv1 btn-left" type="button" onclick="closeParentWin('js_replace_pop');">
                                确定
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
        </div>
    </div>
</div>
</body>