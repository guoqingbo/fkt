<div class="bargain-wrap clearfix">
    <div class="tab-left"><?= $user_tree_menu ?></div>
    <div class="forms_scroll h90">
        <div class="bargain_top_main">
            <div class="i_box">
                <div class="shop_tab_title" style="margin:0 15px  10px  0px;">
                    <a class="btn-lv fr" href="/bargain/bargain_print/<?= $bargain['id']; ?>"
                       style="margin-left: 20px"
                       target="_blank"><span>成交打印</span></a>
                    <a class="btn-lv fr" href="/bargain/cash_detail_print/<?= $bargain['id']; ?>"
                       target="_blank"><span>客户打款明细打印</span></a>
                    <a href="/bargain/bargain_look/<?= $bargain['id']; ?>"
                       class="link link_on"><span class="iconfont hide"></span>成交信息</a>
                    <a href="/bargain/transfer_process/<?= $bargain['id']; ?>" class="link "><span
                                class="iconfont hide"></span>过户流程</a>
                    <a href="/bargain/finance_manage/<?= $bargain['id']; ?>" class="link "><span
                                class="iconfont hide"></span>财务管理</a>
                </div>
                <div class="clearfix">
                    <h4 class="h4">成交信息</h4>
                    <a class="btn_l" href="javascript:void(0);" style="margin-right:16px"
                       <?php if ($auth['edit']['auth']){ ?>onclick="location.href='/bargain/modify_bargain/<?= $bargain['type']; ?>/<?= $bargain['id']; ?>';return false;"
                       <?php }else{ ?>onclick="purview_none();"<?php } ?>>修改</a>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">成交编号：</span><?= $bargain['number']; ?></p>
                    <p class="item w260"><span class="tex">签约日期：</span><?= date("Y-m-d", $bargain['signing_time']); ?>
                    </p>
                    <p class="item w260"><span class="tex">签约人员：</span><?= $bargain['signatory_name']; ?></p>
                    <p class="item w260"><span
                                class="tex">办理状态：</span><?= $config["bargain_status"][$bargain['bargain_status']]; ?>
                    </p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w520"><span class="tex">物业地址：</span><?= $bargain['house_addr']; ?></p>
                    <p class="item w260"><span class="tex">合同价：</span><?= $bargain['price']; ?>元</p>
                    <p class="item w260"><span class="tex">装修款：</span><?= $bargain['decoration_price']; ?>元</p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">建筑面积：</span><?= $bargain['buildarea']; ?>㎡</p>
                    <p class="item w260"><span class="tex">产证编号：</span><?= $bargain['certificate_number']; ?></p>
                    <p class="item w260"><span
                                class="tex">区域：</span><?= $config['district_id'][$bargain['district_id']]; ?></p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span
                                class="tex">房屋类型：</span><?= $config["house_type"][$bargain['house_type']]; ?></p>
                    <p class="item w260"><span
                                class="tex">土地性质：</span><?= $config["land_nature"][$bargain['land_nature']]; ?></p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item  <?= $bargain['is_mortgage'] == 1 ? "w520" : "w260"; ?>"><span
                                class="tex">是否抵押：</span><?= $bargain['is_mortgage'] == 1 ? "是" . " " . $bargain['mortgage_thing'] : "否"; ?>
                    </p>
                    <p class="item w260"><span
                                class="tex">是否评估：</span><?= $config["is_evaluate"][$bargain['is_evaluate']]; ?></p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">成交门店：</span><?= $bargain['agency_name_a']; ?></p>
                    <p class="item w260"><span class="tex">经纪人：</span><?= $bargain['broker_name_a']; ?></p>
                    <p class="item w260"><span class="tex">电话：</span><?= $bargain['broker_tel_a']; ?></p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span
                                class="tex">签约公司：</span><?= $config["signatory_company"][$bargain['signatory_company']]; ?>
                    </p>
                    <p class="item w260"><span class="tex">权证人员：</span><?= $bargain['warrant_inside_name']; ?></p>
                    <p class="item w260"><span class="tex">理财人员：</span><?= $bargain['finance_name']; ?></p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">卖方姓名：</span><?= $bargain['owner']; ?></p>
                    <p class="item w520"><span class="tex">身份证号：</span><?= $bargain['owner_idcard']; ?></p>
                    <p class="item w260"><span class="tex">电话：</span><?= $bargain['owner_tel']; ?></p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">公证委托：</span><?= $bargain['show_trust_a'] == 1 ? "是" : "否"; ?>
                    </p>
                    <?php if ($bargain['show_trust_a'] == 1) { ?>
                        <p class="item w260"><span class="tex">受托人姓名：</span><?= $bargain['trust_name_a']; ?></p>
                        <p class="item w260"><span class="tex">受托人证件号码：</span><?= $bargain['trust_idcard_a']; ?></p>
                    <?php } ?>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">买方姓名：</span><?= $bargain['customer']; ?></p>
                    <p class="item w520"><span class="tex">身份证号：</span><?= $bargain['customer_idcard']; ?></p>
                    <p class="item w260"><span class="tex">电话：</span><?= $bargain['customer_tel']; ?></p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">公证委托：</span><?= $bargain['show_trust_b'] == 1 ? "是" : "否"; ?>
                    </p>
                    <?php if ($bargain['show_trust_a'] == 1) { ?>
                        <p class="item w260"><span class="tex">受托人姓名：</span><?= $bargain['trust_name_b']; ?></p>
                        <p class="item w260"><span class="tex">受托人证件号码：</span><?= $bargain['trust_idcard_b']; ?></p>
                    <?php } ?>
                </div>
                <!--                付款信息-->
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span
                                class="tex">付款方式：</span><?= $config["buy_type"][$bargain['buy_type']] . " " . $config["loan_type"][$bargain['loan_type']]; ?>
                    </p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w520"><span
                                class="tex">监管银行：</span><?= $loan_bank["bank_name"] . " " . $loan_bank["bank_deposit"] . " " . $loan_bank["card_no"]; ?>
                    </p>
                </div>
                <?php if ($bargain['buy_type'] == 1) { ?>
                    <div class="t_item clearfix bargain_mess">
                        <p class="item w520" style="padding-left: 60px"><span class="tex">  </span>
                            于<?= date('Y-m-d', $bargain['payment_once_time']); ?>
                            日前,将全部购房款￥<?= $bargain['tatal_money']; ?>元整，存入对应银行监管账户。</p>
                    </div>


                <?php } elseif ($bargain['buy_type'] == 2) { ?>
                    <div class="t_item clearfix bargain_mess">
                        <p class="item w520" style="padding-left: 60px"><span class="tex">  </span>
                            于<?= date('Y-m-d', $bargain['payment_period_time']); ?>
                            日前,将购房款￥<?= $bargain['purchase_money'][0]; ?>元整，存入对应银行监管账户。
                    </div>
                    <?php foreach ($bargain['purchase_condition'] as $key => $val) { ?>
                        <div class="t_item clearfix bargain_mess">
                            <p class="item w520" style="padding-left: 60px"><span class="tex">  </span>
                                于<?= $val; ?>情况下,将购房款￥<?= $bargain['purchase_money'][$key + 1]; ?>
                                元整，存入对应银行监管账户。</p>
                        </div>
                    <?php } ?>

                <?php } elseif ($bargain['buy_type'] == 3) { ?>
                    <div class="t_item clearfix bargain_mess">
                        <p class="item w520" style="padding-left: 60px"><span class="tex">  </span>
                            于<?= date('Y-m-d', $bargain['first_time']); ?>
                            日前,将购房首付款￥<?= $bargain['first_money']; ?>
                            元整，存入对应银行监管账户,余款￥<?= $bargain['spare_money']; ?>元整则办理按揭贷款。</p>
                    </div>
                <?php } ?>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w520"><span class="tex">交房时间：</span><?= $bargain['house_time']; ?></p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">税费合计：</span><?= $bargain['tax_pay_tatal']; ?>元</p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span
                                class="tex">税费约定：</span><?= $config["tax_pay_type"][$bargain['tax_pay_type']]; ?><?= $bargain['tax_pay_type'] == '4' && isset($bargain['tax_pay_appoint']) ? $bargain['tax_pay_appoint'] : ''; ?>
                    </p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span
                                class="tex">交易票据归属：</span><?= $config["note_belong"][$bargain['note_belong']]; ?></p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item "><span
                                class="tex">卖方缺少资料：</span>
                        <?php foreach (json_decode($bargain['seller_lacks']) as $key => $val) {
                            echo $config["seller_lacks"][$val] . '、 ';
                        } ?>
                        <?= $bargain['seller_lacks_others']; ?>
                    </p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item "><span
                                class="tex">买方缺少资料：</span>
                        <?php foreach (json_decode($bargain['buyer_lacks']) as $key => $val) {
                            echo $config["buyer_lacks"][$val] . '、 ';
                        } ?>
                        <?= $bargain['buyer_lacks_others']; ?>
                    </p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">合同备注：</span><?= $bargain['remarks']; ?></p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">承办备注：</span><?= $bargain['undertake_remarks']; ?></p>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(function () {
            function re_width() {
                var h1 = $(window).height();
                var w1 = $(window).width() - 190;
                $(".tab-left, .forms_scroll").height(h1 - 65);
                $(".forms_scroll").width(w1).show();
            };
            re_width();
            $(window).resize(function (e) {
                re_width();
            });

        });


    </script>
