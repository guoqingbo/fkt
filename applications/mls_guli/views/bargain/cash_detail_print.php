<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<html>
<head>
    <title>客户打款明细</title>
    <style type="text/css">

        .table_title {
            font-weight: bold;
            font-size: 20px;
        }

        .table_1{
            background-color: #ffffff;
            width: 100%;
            border-right: solid 1px #000000;
            border-bottom: solid 1px #000000;
        }

        .table_1 td {
            height: 30px;
            line-height: 30px;
            text-align: center;
            font-weight: normal;
            font-size: 14px;
            border-top: solid 1px #000000;
            border-left: solid 1px #000000;
        }

        .remark {
            text-align: left;
            margin-top: 10px;
        }

    </style>
</head>

<body>
<div style="text-align:center;">
    <div style="width:595px;">
        <div class="table_title">客户打款明细</div>
        <div>
            <div class="" style="float:left;FONT-WEIGHT: bold; FONT-SIZE: 14px;padding-bottom: 2px">成交编号：<?= $bargain['number']; ?></div>
            <div style="float:right;FONT-WEIGHT: bold; FONT-SIZE: 14px;padding-bottom: 2px">签约日期：<?= date("Y-m-d", $bargain['signing_time']); ?></div>
        </div>
        <div style="width:100%;" class="">
            <table class="table_1" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td class="">
                        客户姓名
                    </td>
                    <td class="">
                        <?= $bargain['customer']; ?>
                    </td>
                    <td class="">
                        付款方式
                    </td>
                    <td class="">
                        <?php if ($bargain['buy_type'] == 3) { ?>
                            <?= $config['loan_type'][$bargain['loan_type']]; ?>
                        <?php } else { ?>
                            <?= $config['buy_type'][$bargain['buy_type']]; ?>
                        <?php } ?>
                    </td>
                    <td class="">
                        合同价
                    </td>
                    <td class="">
                        <span class=""><?= $bargain['price']; ?></span>(元)
                    </td>
                </tr>
                <tr>
                    <td class="">
                        业主姓名
                    </td>
                    <td class="">
                        <?= $bargain['owner']; ?>
                    </td>
                    <td class="">
                        物业地址
                    </td>
                    <td class="">
                        <?= $bargain['house_addr']; ?>
                    </td>
                    <td class="">
                        建筑面积
                    </td>
                    <td class="">
                        <span class=""><?= $bargain['buildarea']; ?></span>㎡
                    </td>
                </tr>
                <tr>
                    <td rowspan="6">首付房款</td>
                    <td colspan="2">收款方全称</td>
                    <td colspan="3">杭州科地房地产代理有限公司</td>
                </tr>
                <tr>
                    <td colspan="2">账号</td>
                    <td colspan="3"><?= $loan_bank['card_no']; ?></td>
                </tr>
                <tr>
                    <td colspan="2">开户银行</td>
                    <td colspan="3"><?= $loan_bank['bank_name'].$loan_bank['bank_deposit']; ?></td>
                </tr>
                <tr>
                    <td colspan="2">入账金额</td>
                    <td colspan="3"> <span class=""><?=$down_payment; ?></span>(元)</td>
                </tr>
                <tr>
                    <td colspan="2">款项来源</td>
                    <td colspan="1" style="width: 30%"><?= $bargain['customer']; ?></td>
                    <td colspan="2">购房款</td>
                </tr>
                <tr>
                    <td colspan="2">银行地址</td>
                    <td colspan="3"><?= $loan_bank['card_name']; ?></td>
                </tr>


                <tr>
                    <td rowspan="5">税费</td>
                    <td colspan="2">收款方全称</td>
                    <td colspan="3">杭州科地房地产代理有限公司</td>
                </tr>
                <tr>
                    <td colspan="2">账号</td>
                    <td colspan="3">19000301040007645</td>
                </tr>
                <tr>
                    <td colspan="2">开户银行</td>
                    <td colspan="3">中国农业银行延庆支行</td>
                </tr>
                <tr>
                    <td colspan="2">入账金额</td>
                    <td colspan="3"><span class=""><?= $bargain['tax_pay_tatal']; ?></span>(元)</td>
                </tr>
                <tr>
                    <td colspan="2">款项来源</td>
                    <td colspan="1"><?= $bargain['customer']; ?></td>
                    <td colspan="2">税费</td>
                </tr>
                <tr>
                    <td colspan="1">成交门店</td>
                    <td colspan="2"><?= $bargain['agency_name_a']; ?></td>
                    <td colspan="1">店长/经办人签字</td>
                    <td colspan="2"></td>
                </tr>
            </table>
            <p class="remark">
                备注：1.以上税费仅为估算费用（不含中介费，评估费用），具体费用以相关部门开具的发票为准，多还少补。
                2.为了不影响您的交易流程，房款在存入资金监管账户后请及时到我财务窗口换取收据。
                3.税费可存入我司上述指定的税费账户后到财务窗口换取收据，1万元以下可交现金，您也可由我司旗下加盟店代交。
            </p>
        </div>
</div>
</body>
</html>