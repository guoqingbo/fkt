
<div class="person_level" id='my_level' style='display:none'>
    <!--title-->
    <dl class="person_level_title">
        <dd>我的等级</dd>
        <dt class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></dt>
    </dl>
    <!--内容那个区域-->
    <div class="person_level_con">
        <div class="person_level_current">
            <span class="person_level_num">经验值：<b><?=$level['level_score']?></b></span>
            <!--level-->
            <div class="person_level_load">
                <b class="cur_bg_c">LV<?=$level['level']?></b>
                <!--等级条-->
                <div class="person_level_grade">
                    <span class="person_level_grade_cur"></span>
                    <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/level_jt_03.jpg" class="person_level_grade_img" />
                    <div class="person_level_grade_remind">
                        <p><?=$level['score_now']?>/<?=$level['score_max']?></p>
                    </div>
                </div>
                <b class="nex_bg_c">LV<?=$level['level']+1?></b>
            </div>
            <span class="person_level_num W_C"><a href="javascript:void(0);" onclick="openWin('my_level_rule')"></a></span>
        </div>
        <!--如何获取积分-->
        <div class="person_level_get">
            <p class="get_level_title">如何获取经验值？</p>
            <!--获取路径-->
            <table class="get_level_table">
                <thead>
                    <tr>
                        <td class="td_w15">操作</td>
                        <td class="td_w15">单次经验值</td>
                        <td>限制条件</td>
                    </tr>
                </thead>
            </table>
            <div class="div_scroll">
                <table class="get_level_table magin_TP">
                    <tr>
                        <td class="td_w16">注册</td>
                        <td class="td_w16"><b>100</b></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td_w16">认证</td>
                        <td class="td_w16"><b>200</b></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td_w16">登录</td>
                        <td class="td_w16"><b>5</b></td>
                        <td>每天获取一次</td>
                    </tr>
<!--                    <tr>
                        <td class="td_w16">发布到平台</td>
                        <td class="td_w16"><b>5</b></td>
                        <td>每天最多获取20分，同一房源加一次分，必须一张室内图、一张户外图</td>
                    </tr>-->
                    <tr>
                        <td class="td_w16">发布合作</td>
                        <td class="td_w16"><b>10</b></td>
                        <td>每天最多获取20分，同一房源加一次分<tr>
                    </tr>
                    <tr>
                        <td class="td_w16">接受合作</td>
                        <td class="td_w16"><b>10</b></td>
                        <td>每天最多获取20分，同公司（加盟店除外）不加分</td>
                    </tr></td>
                    </tr>
                    <tr>
                        <td class="td_w16">合作成交</td>
                        <td class="td_w16"><b>300</b></td>
                        <td>同公司（加盟店除外）不加分同公司（加盟店除外）不加分</td>
                    </tr>
                    <tr>
                        <td class="td_w16">合作评价</td>
                        <td class="td_w16"><b>5</b></td>
                        <td>每天最多获取25分，同公司（加盟店除外）不加分</td>
                    </tr>
                    <tr>
                        <td class="td_w16">采集举报</td>
                        <td class="td_w16"><b>2</b></td>
                        <td>每天最多获取20分，审核通过加分</td>
                    </tr>
                    <tr>
                        <td class="td_w16">抢房/客源</td>
                        <td class="td_w16"><b>5</b></td>
                        <td>每天最多获取25分</td>
                    </tr>
					<tr>
                        <td class="td_w16">新房报备成功</td>
                        <td class="td_w16"><b>10</b></td>
                        <td>每天最多获取30分，审核通过加分</td>
                    </tr>
					<tr>
                        <td class="td_w16">新房带看成功</td>
                        <td class="td_w16"><b>100</b></td>
                        <td>审核通过加分</td>
                    </tr>
					<tr>
                        <td class="td_w16">新房认购成功</td>
                        <td class="td_w16"><b>300</b></td>
                        <td>审核通过加分</td>
                    </tr>
					<tr>
                        <td class="td_w16">海外地产报备成功</td>
                        <td class="td_w16"><b>10</b></td>
                        <td>每天最多获取20分，审核通过加分</td>
                    </tr>
					<tr>
                        <td class="td_w16">旅游地产报备成功</td>
                        <td class="td_w16"><b>10</b></td>
                        <td>每天最多获取20分，审核通过加分</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	$(function () {
		var width_total = $('.person_level_grade').width();
		var aW = parseInt(<?=$level['score_now']?>/<?=$level['score_max']?>*width_total);
		$('.person_level_grade_cur').css("width", aW);
		$(".person_level_grade_img").css("left", (aW-6) + "px");
		$(".person_level_grade_remind").css("left", (aW-$(".person_level_grade_remind").width() / 2-30) + "px");

	})
</script>

