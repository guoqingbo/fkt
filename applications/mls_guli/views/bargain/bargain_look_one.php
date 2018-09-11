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
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">成交编号：</span><?= $bargain['number']; ?></p>
                    <p class="item w260"><span class="tex">收件日期：</span><?= date("Y-m-d", $bargain['receipt_time']); ?>
                    </p>
                    <p class="item w260"><span class="tex">办证人员：</span><?= $bargain['warrant_inside_name']; ?></p>
                    <p class="item w260"><span
                                class="tex">办理状态：</span><?= $config["bargain_status"][$bargain['bargain_status']]; ?>
                    </p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">楼盘名称：</span><?= $bargain['block_name']; ?></p>
                    <p class="item w260"><span class="tex">物业地址：</span><?= $bargain['house_addr']; ?></p>
                    <p class="item w260"><span
                                class="tex">区域：</span><?= $config['district_id'][$bargain['district_id']]; ?></p>
                    <p class="item w260"><span
                                class="tex">代办银行：</span><?= $agent_bank["bank_name"] . " " . $agent_bank["bank_deposit"] ?>
                    </p>
                </div>
                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span
                                class="tex">代办类别：</span><?= $config['agent_type'][$bargain['agent_type']]; ?></p>
                    <!--                    <p class="item w260"><span class="tex">代办公司：</span>-->
                    <? //= $bargain['agent_company']; ?><!--</p>-->
                    <p class="item w260"><span class="tex">开发商：</span><?= $bargain['developer']; ?></p>

                </div>

                <div class="t_item clearfix bargain_mess">
                    <p class="item w260"><span class="tex">买方姓名：</span><?= $bargain['customer']; ?></p>
                    <p class="item w260"><span class="tex">身份证号：</span><?= $bargain['customer_idcard']; ?></p>
                    <p class="item w260"><span class="tex">电话：</span><?= $bargain['customer_tel']; ?></p>
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
