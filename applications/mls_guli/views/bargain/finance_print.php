<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<html>
<head>
    <title>成交打印</title>
    <style type="text/css">
        .table_2 {
            background-color: #ffffff;
            width: 100%;
            border-right: solid 1px #000000;
            border-bottom: solid 1px #000000;
        }

        .table_2 td {
            height: 20px;
            line-height: 20px;
            text-align: center;
            font-weight: normal;
            font-size: 12px;
            border-top: solid 1px #000000;
            border-left: solid 1px #000000;
        }
    </style>
</head>

<body>
<div style="text-align:center;">
    <div style="width:595px;">
        <div>
            <br/>
            <table class="table_2" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td colspan="1" class="" style="background-color:#e3effb;">
                        成交编号：<?= $bargain['number']; ?>
                    </td>
                    <td colspan="2" class="" style=" background-color:#e3effb;">
                        物业地址：<?= $bargain['house_addr']; ?>
                    </td>
                    <td colspan="1" class="" style=" background-color:#e3effb;">
                        成交门店：<?= $bargain['agency_name_a']; ?>
                    </td>
                    <td colspan="1" class="" style=" background-color:#e3effb;">
                        经纪人：<?= $bargain['broker_name_a']; ?>
                    </td>
                    <td colspan="1" class="" style=" background-color:#e3effb;">
                        经纪人电话：<?= $bargain['broker_tel_a']; ?>
                    </td>
                    <td colspan="1" class="" style=" background-color:#e3effb;">
                        签约日期：<?= date("Y-m-d", $bargain['signing_time']); ?>
                    </td>
                    <td colspan="1" class="" style=" background-color:#e3effb;">
                        理财跟进：<?= $bargain['finance_name']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" class="" style=" text-align:left; background-color:#e3effb;">
                        代收付
                    </td>
                </tr>
                <tr>
                    <td class="">
                        对象
                    </td>
                    <td class="">
                        代收付
                    </td>
                    <td class="">
                        收金额(元)
                    </td>
                    <td class="">
                        付金额(元)
                    </td>

                    <td class="">
                        费用类别
                    </td>
                    <td class="">
                        费用名称
                    </td>
                    <td class="">
                        日期
                    </td>
                    <td class="">
                        备注
                    </td>
                </tr>
                <?php if ($replace_flow) {
                    foreach ($replace_flow as $key => $val) { ?>
                        <tr id="tr">
                            <td class="">
                                <?= $config['target_type'][$val['target_type']]; ?>
                            </td>
                            <td class="">
                                <?= $config['replace_type'][$val['replace_type']]; ?>
                            </td>
                            <td class="">
                                <?= $val['collect_money']; ?>
                            </td>
                            <td class="">
                                <?= $val['pay_money']; ?>
                            </td>
                            <td class="">
                                <?= $config['money_type'][$val['money_type']]; ?>
                            </td>
                            <td class="">
                                <?= $val['money_name']; ?>
                            </td>
                            <td class="">
                                <?= $val['flow_time']; ?>
                            </td>
                            <td class="">
                                <?= $val['remark']; ?>
                            </td>
                        </tr>
                    <?php }
                } ?>
                <tr>
                    <td colspan="2" align="right" class="">
                        <b style="font-weight: normal; color: #F00;">合计：</b>
                    </td>

                    <td colspan="1" align="center" class="">
                        <b style="font-weight: normal; color: #F00;">
                            <?= $replace_collect_money_total; ?></b>
                    </td>
                    <td colspan="1" align="center" class="">
                        <b style="font-weight: normal; color: #F00;">
                            <?= $replace_pay_money_total; ?></b>
                    </td>
                    <td colspan="4" align="left" class="">
                        <b style="font-weight: normal; color: #F00;">
                            余额：<?= ($replace_collect_money_total - $replace_pay_money_total); ?></b></b>
                    </td>

                </tr>
            </table>
        </div>
        <br/>
        <div>
            <table class="table_2" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td colspan="10" class="" style=" text-align:left; background-color:#e3effb;">
                        税费
                    </td>
                </tr>
                <tr>
                    <td class="">
                        对象
                    </td>
                    <td class="">
                        代收付
                    </td>
                    <td class="">
                        收金额(元)
                    </td>
                    <td class="">
                        付金额(元)
                    </td>
                    <td class="">
                        收款方
                    </td>
                    <td class="">
                        费用类别
                    </td>
                    <td class="">
                        费用名称
                    </td>
                    <td class="">
                        日期
                    </td>

                    <td class="">
                        方式
                    </td>
                    <td class="">
                        备注
                    </td>
                </tr>
                <?php if ($replace_tax_flow) {
                    foreach ($replace_tax_flow as $key => $val) { ?>
                        <tr>
                            <td class="">
                                <?= $config['target_type'][$val['target_type']]; ?>
                            </td>
                            <td class="">
                                <?= $config['replace_type'][$val['replace_type']]; ?>
                            </td>
                            <td class="">
                                <?= $val['collect_money']; ?>
                            </td>
                            <td class="">
                                <?= $val['pay_money']; ?>
                            </td>
                            <td class="">
                                <?= $val['collect_person']; ?>
                            </td>
                            <td class="">
                                <?= $config['money_type'][$val['money_type']]; ?>
                            </td>
                            <td class="">
                                <?= $val['money_name']; ?>
                            </td>
                            <td class="">
                                <?= $val['flow_time']; ?>
                            </td>
                            <td class="">
                                <?= $config['money_type'][$val['money_type']]; ?>
                            </td>
                            <td class="">
                                <?= $val['remark']; ?>
                            </td>
                        </tr>
                    <?php }
                } ?>

                <tr>
                    <td colspan="2" align="right" class="">
                        <b style="font-weight: normal; color: #F00;">合计：</b>
                    </td>

                    <td colspan="1" align="center" class="">
                        <b style="font-weight: normal; color: #F00;">
                            <?= $replace_tax_collect_money_total; ?></b>
                    </td>
                    <td colspan="1" align="center" class="">
                        <b style="font-weight: normal; color: #F00;">
                            <?= $replace_tax_pay_money_total; ?></b>
                    </td>
                    <td colspan="6" align="left" class="">
                        <b style="font-weight: normal; color: #F00;">
                            余额：<?= ($replace_tax_collect_money_total - $replace_tax_pay_money_total); ?></b></b>
                    </td>

                </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>