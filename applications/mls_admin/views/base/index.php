<?php require APPPATH . 'views/header.php'; ?>
<link type="text/css" rel="stylesheet" href="<?=MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css,css/v1.0/cal.css,css/v1.0/system_set.css,css/v1.0/personal_center.css">
<link type="text/css" rel="stylesheet" href="<?=MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/guest_disk.css ">
<style>
 .set_basic_wra {
    background: #fbfbfb none repeat scroll 0 0;
    border: 1px solid #e6e6e6;
    width: 100%;
}
</style>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $title ?></h1>
            </div>
        </div>
        <?php if ('' == $setResult) {?>
        <form name="search_form" method="post" action="" >
            <input type='hidden' name='submit_flag' value='set'/>
            <input type="hidden" name="is_default" value="<?php echo $base_setting['is_default'];?>">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                           <div style="position: relative; overflow-y: scroll; width: 100%; height:650px" id="js_inner">
                                <div class="set_basic_bgw">
                                    <div class="set_basic_wra" >
                                        <div class="set_basic_content">
                                            <p class="title">系统设置</p>
                                                <div class="set_basic_section clearfix">
                                                    <div class="set_basic_section_l fl clearfix">
                                                        <span class="fl auto_time">系统自动防护时间</span>
                                                        <input type="text" class="auto_pre fl" name="guard_time"
                                                               value="<?php if($base_setting['guard_time']!=='0'){echo $base_setting['guard_time'];}?>">
                                                        <span class="fl">分钟内（无操作自动安全切换到登录窗口）</span>
                                                    </div>
                                                </div>
                                            <p class="title">业务管理</p>
                                            <div class="set_basic_section ">
                                                <div class="set_basic_section_line clearfix">
                                                    <div class="set_basic_section_l fl clearfix">
                                                            <span class="set_basic_section_l_remind fl">信息录入进行黑名单校验</span>
                                                            <input type="radio" class="find_call" name="is_blacklist_check" value="1" <?php echo $base_setting['is_blacklist_check']=="1"?"checked":""?>>是
                                                            <input type="radio" class="find_call" name="is_blacklist_check" value="0" <?php echo $base_setting['is_blacklist_check']=="0"?"checked":""?>>否
                                                    </div>
                                                    <div class="set_basic_section_r fl">
                                                        <span class="fl w181">跟进内容不得少于</span>
                                                        <input type="text" class="auto_pre fl" name="follow_text_num" value="<?php echo $base_setting['follow_text_num']>"0"?$base_setting['follow_text_num']:""?>">
                                                        <span class="fl">字</span>
                                                    </div>　
                                                </div>　
                                                <div class="set_basic_section_line clearfix">
                                                    <div class="set_basic_section_l fl clearfix ">
                                                        <span class="fl auto_time">
                                                            <span class="set_basic_section_l_remind fl">楼盘名称只能选择录入</span>
                                                             <input type="radio" class="find_call" name="is_property_publish" value="1" <?php echo $base_setting['is_property_publish']=="1"?"checked":""?>>是
                                                             <input type="radio" class="find_call" name="is_property_publish" value="0" <?php echo $base_setting['is_property_publish']=="0"?"checked":""?>>否
                                                        </span>
                                                    </div>
                                                    <div class="set_basic_section_r  fl clearfix ">
                                                        <span class="fl auto_time">
                                                            <span class="set_basic_section_l_remind_no fl">房客源列表默认排序规则</span>
                                                            <select class="sel_time fl" name="house_list_order_field">
                                                                <option value="1" <?php echo $base_setting['house_list_order_field']=="1"?"selected":""?>>时间</option>
                                                                <option value="2" <?php echo $base_setting['house_list_order_field']=="2"?"selected":""?>>价格</option>
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                               <div class="set_basic_section_line clearfix">
                                                    <div class="set_basic_section_l fl clearfix ">
                                                        <span class="fl auto_time">
                                                            <span class="set_basic_section_l_remind_no fl">保密信息查看次数上限 </span>
                                                            <input type="text" class="auto_pre fl" name="secret_view_num" value="<?php echo $base_setting['secret_view_num']>"0"?$base_setting['secret_view_num']:""?>">
                                                            <span class="fl">次/日</span>
                                                        </span>
                                                    </div>
                                                    <div class="set_basic_section_r fl">
                                                        <span class="fl auto_time">
                                                             <span class="fl w170"> 登录时有提醒任务是否自动打开</span>
                                                                <input type="radio" class="find_call" name="is_remind_open" value="1" <?php echo $base_setting['is_remind_open']=="1"?"checked":""?>>是
                                                                <input type="radio" class="find_call" name="is_remind_open" value="0" <?php echo $base_setting['is_remind_open']=="0"?"checked":""?>>否
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="set_basic_section_line clearfix">
                                                    <div class="set_basic_section_l fl clearfix">
                                                         <span class="fl auto_time">
                                                             <span class="set_basic_section_l_remind fl">楼盘字典同步变化 </span>
                                                            <input type="radio" class="find_call" name="is_community_modify_house" value="1" <?php echo $base_setting['is_community_modify_house']=="1"?"checked":""?>>是
                                                             <input type="radio" class="find_call" name="is_community_modify_house" value="0" <?php echo $base_setting['is_community_modify_house']=="0"?"checked":""?>>否
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                    　　　　　　　　　　　　　<p class="title">策略参数</p>
                                            <div class="set_basic_section clearfix">
                                                <div class="set_basic_section_line clearfix">
                                                    <div class="set_basic_section_l fl clearfix ">
                                                        <span class="fl auto_time">
                                                            <span class="set_basic_section_l_remind_no fl"> 出租自动变公盘 </span>
                                                            <input type="text" class="auto_pre fl" name="rent_house_nature_public" value="<?php echo $base_setting['rent_house_nature_public']>"0"?$base_setting['rent_house_nature_public']:""?>">
                                                            <span class="fl">天</span>
                                                         </span>
                                                    </div>
                                                    <div class="set_basic_section_r fl">
                                                         <div class="set_basic_section_r fl">
                                                             <span class="fl w181">求购自动变公客</span>
                                                            <input type="text" class="auto_pre fl" name="buy_customer_nature_public" value="<?php echo $base_setting['buy_customer_nature_public']>"0"?$base_setting['buy_customer_nature_public']:""?>">
                                                             <span class="fl">天</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="set_basic_section_line clearfix">
                                                    <div class="set_basic_section_l fl clearfix ">
                                                        <span class="fl auto_time">
                                                            <span class="set_basic_section_l_remind_no fl">出售自动变公盘</span>
                                                            <input type="text" class="auto_pre fl" name="sell_house_nature_public" value="<?php echo $base_setting['sell_house_nature_public']>"0"?$base_setting['sell_house_nature_public']:""?>">
                                                            <span class="fl">天</span>
                                                        </span>
                                                    </div>
                            <div class="set_basic_section_r fl">
                                <div class="set_basic_section_r fl">
                                    <span class="fl w181">求租自动变公客</span>
                                    <input type="text" class="auto_pre fl" name="rent_customer_nature_public" value="<?php echo $base_setting['rent_customer_nature_public']>"0"?$base_setting['rent_customer_nature_public']:""?>">
                                    <span class="fl">天</span>
                                </div>
                            </div>
                        </div>
                        <div class="set_basic_section_line clearfix">
                            <div class="set_basic_section_l fl clearfix ">
                                <span class="fl auto_time">
                                 <span class="set_basic_section_l_remind_no fl">
                                  出售信息默认查询时间
                                </span>
                                    <select class="auto_pre fl" name="sell_house_query_time">
                                        <option value="1" <?php echo $base_setting['sell_house_query_time']=="1"?"selected":""?>>半年</option>
                                        <option value="2" <?php echo $base_setting['sell_house_query_time']=="2"?"selected":""?>>一年</option>
                                    </select>
                                </span>
                            </div>
                            <div class="set_basic_section_r fl">
                                <div class="set_basic_section_r fl">
                                    <span class="fl w181">求购信息默认查询时间</span>
                                    <select class="auto_pre fl" name="buy_customer_query_time">
                                        <option value="1" <?php echo $base_setting['buy_customer_query_time']=="1"?"selected":""?>>半年</option>
                                        <option value="2" <?php echo $base_setting['buy_customer_query_time']=="2"?"selected":""?>>一年</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="set_basic_section_line clearfix">
                            <div class="set_basic_section_l fl clearfix ">
                                <span class="fl auto_time">
                                 <span class="set_basic_section_l_remind_no fl">
                                  出租信息默认查询时间
                                </span>
                                    <select class="auto_pre fl" name="rent_house_query_time">
                                        <option value="1" <?php echo $base_setting['rent_house_query_time']=="1"?"selected":""?>>半年</option>
                                        <option value="2" <?php echo $base_setting['rent_house_query_time']=="2"?"selected":""?>>一年</option>
                                    </select>
                                </span>
                            </div>
                            <div class="set_basic_section_r fl">
                                <div class="set_basic_section_r fl">
                                    <span class="fl w181"> 求租信息默认查询时间</span>
                                    <select class="auto_pre fl" name="rent_customer_query_time">
                                        <option value="1" <?php echo $base_setting['rent_customer_query_time']=="1"?"selected":""?>>半年</option>
                                        <option value="2" <?php echo $base_setting['rent_customer_query_time']=="2"?"selected":""?>>一年</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="set_basic_section_line clearfix">
                            <div class="set_basic_section_l fl clearfix ">
                                <span class="fl auto_time">
                                 <span class="set_basic_section_l_remind_no fl">
                                 出租信息登记时间
                                </span>
                                    <input type="text" class="auto_pre fl" name="rent_house_check_time"
                                           value="<?php echo $base_setting['rent_house_check_time']>"0"?$base_setting['rent_house_check_time']:""?>">
                                    <span class="fl">天后未勘察</span>
                                </span>

                            </div>
                            <div class="set_basic_section_r fl">
                                <div class="set_basic_section_r fl">
                                    <span class="fl w181">求购信息登记时间</span>
                                    <input type="text" class="auto_pre fl" name="buy_customer_check_time"
                                           value="<?php echo $base_setting['buy_customer_check_time']>"0"?$base_setting['buy_customer_check_time']:""?>">
                                    <span class="fl">天后未勘察</span>
                                </div>
                            </div>
                        </div>
                        <div class="set_basic_section_line clearfix">
                            <div class="set_basic_section_l fl clearfix ">
                                <span class="fl auto_time">
                                 <span class="set_basic_section_l_remind_no fl">
                                 出售信息登记
                                </span>
                                    <input type="text" class="auto_pre fl" name="sell_house_check_time"
                                           value="<?php echo $base_setting['sell_house_check_time']>"0"?$base_setting['sell_house_check_time']:""?>">
                                    <span class="fl">天后未勘察</span>
                                </span>
                            </div>
                            <div class="set_basic_section_r fl">
                                <div class="set_basic_section_r fl">
                                    <span class="fl w181">求租信息登记时间</span>
                                    <input type="text" class="auto_pre fl" name="rent_customer_check_time"
                                           value="<?php echo $base_setting['rent_customer_check_time']>"0"?$base_setting['rent_customer_check_time']:""?>">
                                    <span class="fl">天后未勘察</span>
                                </div>
                            </div>
                        </div>
                        <div class="set_basic_section_line clearfix">
                            <div class="set_basic_section_l fl clearfix ">
                                <span class="fl auto_time">
                                 <span class="set_basic_section_l_remind_no fl">
                                 两次客源跟进间隔超过
                                </span>
                                    <input type="text" class="auto_pre fl" name="customer_follow_spacing_time"
                                           value="<?php echo $base_setting['customer_follow_spacing_time']>"0"?$base_setting['customer_follow_spacing_time']:""?>">
                                    <span class="fl">天</span>
                                </span>
                            </div>
                            <div class="set_basic_section_r fl">
                                <div class="set_basic_section_r fl">
                                    <span class="fl w181">两次房源跟进间隔超过</span>
                                    <input type="text" class="auto_pre fl" name="house_follow_spacing_time"
                                           value="<?php echo $base_setting['house_follow_spacing_time']>"0"?$base_setting['house_follow_spacing_time']:""?>">
                                    <span class="fl">天</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="set_bottom_bar">
                        <input type="submit" value="保&nbsp;&nbsp;&nbsp;存" class="submit_blue">
                    </div>
                </div>
            </div>
        </div>
    </div>
              </div>
            </div>
        </div>
        </form>
        <?php } else if (0 === $setResult) { ?>
            <div>设置失败</div>
        <?php } else{ ?>
            <div>设置成功</div>
        <?php } ?>
    </div>
</div>
<?php if ($setResult != "") { ?>
    <script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/base/index/'; ?>";
            }, 1000);
        });
    </script>
<?php } ?>

