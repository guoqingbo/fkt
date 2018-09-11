
<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<html>
<head>
    <title>成交打印</title>
    <style type="text/css">

        .table_title{  font-weight: bold; font-size:20px;  }
        .table_1,.table_2,.table_3{background-color:#ffffff;width:100%;border-right:solid 1px #000000;border-bottom:solid 1px #000000;}

        .table_1 {
            border-bottom: solid 0 #000000;
        }
        .table_1 td, .table_2 td {
            height: 30px;
            line-height: 20px;
            text-align: left;
            font-weight: normal;
            font-size: 12px;
            border-top: solid 1px #000000;
            border-left: solid 1px #000000;
        }
        .table_1 .td_1{background-color:#e3effb;text-align:right; width:18%;}
        .table_1 .td_2{background-color:#ffffff;text-align:left;width:32%;}
        .table_1_1 td{border:0; }
        .table_3 td { font-weight:bold;height:30px; line-height:30px; font-size:12px; text-align:left;border-top:solid 1px #000000;border-left:solid 1px #000000;}
    </style>
</head>

<body>
    <div style="text-align:center;">
        <div style="width:595px;">
            <div class="table_title">产权交易签约审核表</div>
            <div>
                <div class="" style="float:left;FONT-WEIGHT: bold; FONT-SIZE: 12px">基本资料</div>
                <div style="float:right;FONT-WEIGHT: bold; FONT-SIZE: 12px">制表日期：<?=date("Y-m-d",time());?></div>
            </div>
            <div style="width:100%;" class=""><!--基本资料-->
                <table class="table_1" cellspacing="0" ;>
                    <tr>
                        <td class="td_1">成交编号：</td>
                        <td class="td_2"><span><?=$bargain['number'];?></span></td>
                        <td class="td_1">合同价：</td>
                        <td class="td_2"><span class=""><?=$bargain['price'];?></span>(元)</td>
                    </tr>

                    <tr>
                        <td class="td_1">签约日期：</td>
                        <td class="td_2" ><span id="bargain_date" class=""><?=date("Y-m-d",$bargain['signing_time']);?></span></td>
                        <td class="td_1"  >装修款：</td>
                        <td class="td_2"><span id="fitting" class=""><?= $bargain['decoration_price']; ?></span>(元)</td>
                    </tr>

                    <tr>
                        <td class="td_1"  >物业地址：</td>
                        <td class="td_2" ><span id="est_addr" class=""><?=$bargain['house_addr'];?></span></td>
                        <td class="td_1"  >建筑面积：</td>
                        <td class="td_2" ><span id="est_reg_size" class=""><?=$bargain['buildarea'];?></span>㎡</td>
                    </tr>

                    <tr>
                        <td class="td_1" >产证编号：</td>
                        <td class="td_2"  ><span class=""><?=$bargain['certificate_number'];?></span></td>
                        <td class="td_1"> 土地性质：</td>
                        <td class="td_2" ><span  class=""><?=$config['land_nature'][$bargain['land_nature']];?></span></td>

                    </tr>

                    <tr>
                        <td class="td_1">卖方姓名：</td>
                        <td class="td_2" ><span id="sdptname" class=""><?=$bargain['owner'];?></span></td>
                        <td class="td_1">买方姓名：</td>
                        <td class="td_2" ><span id="xdptname" class=""><?=$bargain['customer'];?></span></td>
                    </tr>

                    <tr>
                        <td class="td_1"  >身份证号：</td>
                        <td class="td_2" >
                            <?=$bargain['owner_idcard'];?></td>
                        <td class="td_1"  >身份证号：</td>
                        <td class="td_2" >
                            <?=$bargain['customer_idcard'];?></td>
                    </tr>

                    <tr>
                        <td class="td_1"  >联系电话：</td>
                        <td class="td_2" ><span id="scustommobile" class=""><?=$bargain['owner_tel'];?></span></td>
                        <td class="td_1"  >联系电话：</td>
                        <td class="td_2" ><span id="xcustommobile" class=""><?=$bargain['customer_tel'];?></span></td>
                    </tr>

                    <tr>
                        <td class="td_1">成交门店：</td>
                        <td class="td_2" ><span id="updptname" class=""><?=$bargain['agency_name_a'];?></span></td>
                        <td class="td_1">经纪人：</td>
                        <td class="td_2"><span id="dwdptname"
                                               class=""><?= $bargain['broker_name_a'] . " " . $bargain['broker_tel_a']; ?></span>
                        </td>
                    </tr>

                    <!--                    <tr>-->
                    <!--                        <td class="td_1"  >经纪人：</td>-->
                    <!--                        <td class="td_2" ><span  class="">-->
                    <? //=$bargain['broker_name_a'].$bargain['broker_tel_a'];?><!--</span></td>-->
                    <!--                        <td class="td_1" >经纪人：</td>-->
                    <!--                        <td class="td_2" ><span  class="">-->
                    <? //=$bargain['broker_name_b'].$bargain['broker_tel_b'];?><!--</span></td>-->
                    <!--                    </tr>-->
                    <tr>
                        <td class="td_1"  >签约人员：</td>
                        <td class="td_2" ><span  class=""><?=$bargain['signatory_name'];?></span></td>
                        <td class="td_1">权证人员：</td>
                        <td class="td_2" ><span  class=""><?=$bargain['warrant_inside_name'];?></span></td>
                    </tr>
                    <tr>
                        <td class="td_1"  >签约公司：</td>
                        <td class="td_2" ><span  class=""><?=$config['signatory_company'][$bargain['signatory_company']];?></span></td>
                        <td class="td_1"  >交房时间：</td>
                        <td class="td_2" ><span id="txtgivetime" class=""><?=$bargain['house_time'];?></span></td>
                    </tr>
                    <tr>
                        <td class="td_1"  >贷款银行：</td>
                        <td class="td_2"><span id="txt_buybank"
                                               class=""><?= $loan_bank['bank_name'] . $loan_bank['bank_deposit']; ?></span>
                        </td>
                        <td class="td_1"  >是否评估：</td>
                        <td class="td_2"><span id="radtxt_buytype"
                                               class=""><?= $config['is_evaluate'][$bargain['is_evaluate']]; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_1"  >抵押：</td>
                        <td class="td_2" colspan="3">
                            <span><?= $bargain['is_mortgage'] == 1 ? "有抵押，抵押权利人：" . $bargain['mortgage_thing'] : "无抵押"; ?></span>
                        </td>
                    </tr>

                    <tr>
                        <td class="td_1"  >付款方式：</td>
                        <td class="td_2" colspan="3" >
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table_1_1">

                                <tr>
                                    <td><span class=""><?=$config['buy_type'][$bargain['buy_type']];?> <?=$bargain['buy_type']==3?$config["loan_type"][$bargain['loan_type']]:"";?> </span></td>
                                </tr>
                                <?php if ($bargain['buy_type']==1){ ?>
                                <tr>
                                    <td>
                                        <span id="pay_money" class="">于<?=date('Y-m-d',$bargain['payment_once_time']);?>日前,将全部购房款￥<?=$bargain['tatal_money'];?>元整，存入对应银行监管账户。</span>
                                    </td>
                                </tr>
                                <?php }elseif($bargain['buy_type']==2){ ?>
                                    <tr>
                                        <td>
                                            <span id="pay_money"
                                                  class="">于<?= date('Y-m-d', $bargain['payment_period_time']); ?>
                                                日前,将购房款￥<?= $bargain['purchase_money'][0]; ?>元整，存入对应银行监管账户。</span>
                                        </td>
                                    </tr>
                                    <?php foreach( $bargain['purchase_condition'] as $key=>$val){?>
                                        <tr>
                                            <td>
                                                <span id="pay_money" class="">于<?= $val;?>情况下,将购房款￥<?=$bargain['purchase_money'][$key+1];?>元整，存入对应银行监管账户。</span>
                                            </td>
                                        </tr>
                                    <?php }?>
                                <?php }elseif($bargain['buy_type']==3){ ?>
                                    <tr>
                                        <td>
                                            <span id="pay_money" class="">于<?=date('Y-m-d',$bargain['first_time']);?>日前,将购房首付款￥<?=$bargain['first_money'];?>元整，存入对应银行监管账户,余款￥<?=$bargain['spare_money'];?>元整则办理按揭贷款。</span>
                                        </td>
                                    </tr>
                                <?php }?>
                            </table>
                        </td>
                    </tr>

                    <!--                    <tr>-->
                    <!--                        <td class="td_1"  >取款方式：</td>-->
                    <!--                        <td class="td_2" colspan="3" >-->
                    <!--                            --><?php //foreach( $bargain['collect_condition']  as $key=>$val){?>
                    <!--                                    --><? //= $key+1;?><!--、于--><? //= $val;?><!--,划付房款￥ -->
                    <? //= $bargain['collect_money'][$key];?><!--元整。<br />-->
                    <!--                            --><?php //}?>
                    <!--                        </td>-->
                    <!--                    </tr>-->
                    <tr>
                        <td class="td_1"  >税费约定：</td>
                        <td class="td_2" colspan="3" >
                            <span id="radfax_type" class=""><?=$config['tax_pay_type'][$bargain['tax_pay_type']];?> </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_1"  > 交易税票归属：</td>
                        <td class="td_2" colspan="3" >
                            <span id="get_content" class=""><?=$config["note_belong"][$bargain['note_belong']];?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_1"> 卖方缺少资料：</td>
                        <td class="td_2" colspan="3">
                            <span id="get_content" class="">
                               <?php foreach (json_decode($bargain['seller_lacks']) as $key => $val) {
                                   echo $config["seller_lacks"][$val] . '、 ';
                               } ?>
                               <?= $bargain['seller_lacks_others']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_1"> 买方缺少资料：</td>
                        <td class="td_2" colspan="3">
                            <span id="get_content" class="">
                               <?php foreach (json_decode($bargain['buyer_lacks']) as $key => $val) {
                                   echo $config["buyer_lacks"][$val] . '、 ';
                               } ?>
                               <?= $bargain['buyer_lacks_others']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_1"> 备注：</td>
                        <td class="td_2" colspan="3">
                            <span id="memo" class=""><?=$bargain['remarks'];?></span>
                        </td>
                    </tr>
                </table>
            </div>

            <table class="table_3" cellspacing="0" cellpadding="0" width="100%" border="0">

                <tr>
                    <td class="">交易主管审核：</td>
                    <td class="">评估审核：</td>
                    <td class="">办证审核：</td>
                </tr>
                <tr>

                    <td class="">理财人员：<?= $bargain['finance_name']; ?></td>
                    <td class="">制表：<?= $handler["name"]; ?></td>
                    <td class=""></td>
                </tr>
        </div>
    </div>
</body>
</html>