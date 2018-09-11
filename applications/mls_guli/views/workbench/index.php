<!doctype html>
<script>
  window.parent.addNavClass(1);
</script>
<html>
<head>
  <meta charset="utf-8">
  <title>主框架</title>
  <link href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls_guli/css/v1.0/base.css" rel="stylesheet" type="text/css">
  <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&amp;f=css/v1.0/home.css" rel="stylesheet" type="text/css">
</head>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
<body>
<div class="content_scroll">
  <div class="main-seo clearfix">
    <div class="left-seo">
      <div class="opt-seo clearfix">
        <!--<ul>
                    <li class="opt-a opt-a1"><a class="w1-seo" href="<?php echo MLS_SIGN_URL;?>/my_task/index/"><span><?php echo $task_num;?></span>新增采集房源</a></li>
					<li class="opt-a opt-a2"><a class="w1-seo" href="<?php echo MLS_SIGN_URL;?>/house_collections/collect_sell"><span><?php echo $all_collect_house_num;?></span>未完成提醒</a></li>
					<li class="opt-a opt-a3"><a class="w1-seo" href="<?php echo MLS_SIGN_URL;?>/cooperate/accept_order_list/"><span><?php echo $accept['all_estas_num'];?></span>我收到的合作</a></li>
					<li class="w1-seo opt-a4">
						<div class="opt-a4-inner">
							<h3>悬赏总金额</h3>
                            <?php if($reward_sum > 10000){?>
							<p class="shang-text"><em>¥<?php echo $reward_sum_big;?></em> 万元</p>
                            <?php }else{ ?>
							<p class="shang-text"><em>¥<?php echo $reward_sum;?></em> 元</p>
                            <?php }?>
                            <?php if($reward_max > 10000){?>
							<p class="b60">最高悬赏 ¥<?php echo $reward_max_big;?>万元</p>
                            <?php }else{?>
							<p class="b60">最高悬赏 ¥<?php echo $reward_max;?>元</p>
                            <?php }?>
							<p class="qiang-img"></p>
						</div>
					</li>
				</ul>-->
        <ul>
          <li><a class="w1-seo opt-a opt-a1" href="<?php echo MLS_SIGN_URL;?>/my_task/index/"><span><?php echo $task_num;?></span>我的新任务</a></li>
          <!--					<li><a class="w1-seo opt-a opt-a2" href="--><?php //echo MLS_SIGN_URL;?><!--/house_collections_new/collect_sell"><span>--><?php //echo $all_collect_house_num;?><!--</span>新增采集</a></li>-->
          <li><a class="w1-seo opt-a opt-a3" href="javascript:void(0);" id="accept_cooperate"><span><?php echo $accept['all_estas_num'];?></span>我收到的合作</a></li>
          <li><a class="w1-seo opt-a opt-a4" href="javascript:void(0);" id="send_cooperate"><span><?php echo $send['all_estas_num'];?></span>我发起的合作</a></li>
        </ul>
      </div>
      <div class="good-news clearfix" id="breakingnews">
        <?php if(!empty($slider_list)){?>
          <div class="bn-title"></div>
          <ul id="abc">
            <?php foreach($slider_list as $v) {
              $link_detail = '查看详情';
              if ($v['url'] == '/sell/lists_pub/')
              {
                $link_detail = '>>去合作中心';
              }
              else if ($v['url'] == '/house_collections/collect_sell/')
              {
                $link_detail = '>>去采集中心';
              }
              else if ($v['url'] == '/entrust_center/ent_sell/')
              {
                $link_detail = ' >>去营销中心';
              }
              $url = $v['url'] != '' ? '<a href=\''.$v['url'].'\'>' . $link_detail . '</a>' : '';
              ?>
              <li><a href="#" title="<?=htmlspecialchars($v['title'])?>" message = "<?php echo htmlspecialchars(nl2br($v['message'])) . $url;?>" updatetime="<?php echo date('Y-m-d H:i:s',$v['updatetime']);?>"><?=$v['title']?></a></li>
            <?php } ?>
          </ul>
        <?php }?>
      </div>
      <div class="mobile-seo">
        <ul class="slide-pic">
          <!--
					<li class="current">
						<div class="banner-01">
							<a style="background:#FEEB9C;" href="<?php echo MLS_SIGN_URL;?>/project/fang100/"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/subject/xcc2.jpg" ></a>
						</div>
					</li>
					<li>
						<div class="banner-01">
							<a href="<?php echo MLS_SIGN_URL;?>/project/fang100/"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/subject/banner.jpg" ></a>
						</div>
					</li>
                    -->
          <?php if (false && $signatory['city'] == 'cd') { ?>
            <li class="current">
              <div class="banner-01">
                <a style="background:#f22f4d;" href="<?php echo MLS_SIGN_URL;?>/notice/credit_active/"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner3.png" ></a>
              </div>
            </li>
          <?php } ?>
          <?php if (false && ($signatory['city'] == 'sz' || $signatory['city'] == 'km')) { ?>
            <li  class="current">
              <div class="banner-01">
                <a style="background:#d7083e;" href="<?php echo MLS_SIGN_URL;?>/notice/credit_active/"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner2.png" ></a>
              </div>
            </li>
          <?php } ?>
          <?php if ($signatory['city'] == 'sz') { ?>
            <li class="current">
              <div class="banner-01">
                <a style="background:#1576d9;" href="<?php echo MLS_SIGN_URL;?>/finance/apply_pledge"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner.png" ></a>
              </div>
            </li>
            <li>
              <div class="banner-01">
                <a style="background:#B1302D;"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner1_sz.png" ></a>
              </div>
            </li>
            <li>
              <div class="banner-01">
                <a style="background:#6081C0;"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner2_sz.png" ></a>
              </div>
            </li>
            <li>
              <div class="banner-01">
                <a style="background:#EC6422;"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner3_sz.png" ></a>
              </div>
            </li>
          <?php } ?>
          <?php if ($signatory['city'] == 'cd') { ?>
            <li class="current">
              <div class="banner-01">
                <a style="background:#EC6422;"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner1_cd.png" ></a>
              </div>
            </li>
            <li>
              <div class="banner-01">
                <a style="background:#F6AC16;"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner2_cd.png" ></a>
              </div>
            </li>
          <?php } ?>
          <?php if ($signatory['city'] == 'km') { ?>
            <li class="current">
              <div class="banner-01">
                <a style="background:#cca97f;" href="#"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner4.png" ></a>
              </div>
            </li>
            <li>
              <div class="banner-01">
                <a style="background:#24A3D8;"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner1_km.png" ></a>
              </div>
            </li>
            <li>
              <div class="banner-01">
                <a style="background:#FF8C07;"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/banner2_km.png" ></a>
              </div>
            </li>
          <?php } ?>
          <li style="background:#1D4DA2;" <?php if ($signatory['city'] != 'sz' && $signatory['city'] != 'km' && $signatory['city'] != 'cd') { ?>class="current" <?php } ?>>
            <div class="mobile-inner-seo">

            </div><!--<a class="a1" href="###" onclick="openWin('js_pop_jjsx');"><img src="<?php /*echo MLS_SOURCE_URL;*/?>/mls_guli/images/v1.0/new/m1_1.png" ></a>
							<a href="###"  onclick="openWin('js_pop_jjsx');"><img src="<?php /*echo MLS_SOURCE_URL;*/?>/mls_guli/images/v1.0/new/m2_1.png"></a>-->
          </li>
        </ul>
        <?php if ($signatory['city'] == 'sz') { ?>
          <ul class="slide-li op">
            <li class="current"></li>
            <li class=""></li>
            <li class=""></li>
            <li class=""></li>
            <li class=""></li>
          </ul>
        <?php } ?>
        <?php if ($signatory['city'] == 'km') { ?>
          <ul class="slide-li op">
            <li class="current"></li>
            <li class=""></li>
            <li class=""></li>
            <li class=""></li>
          </ul>
        <?php } ?>
        <?php if ($signatory['city'] == 'cd') { ?>
          <ul class="slide-li op">
            <li class="current"></li>
            <li class=""></li>
            <li class=""></li>
          </ul>
        <?php } ?>
        <?php if ($signatory['city'] == 'sz') { ?>
          <ul class="slide-li slide-txt">
            <li class="current">1</li>
            <li class="">2</li>
            <li class="">3</li>
            <li class="">4</li>
            <li class="">5</li>
          </ul>
        <?php } ?>
        <?php if ($signatory['city'] == 'km') { ?>
          <ul class="slide-li slide-txt">
            <li class="current">1</li>
            <li class="">2</li>
            <li class="">3</li>
            <li class="">4</li>
          </ul>
        <?php } ?>
        <?php if ($signatory['city'] == 'cd') { ?>
          <ul class="slide-li slide-txt">
            <li class="current">1</li>
            <li class="">2</li>
            <li class="">3</li>
          </ul>
        <?php } ?>
      </div>
      <div class="add-seo clearfix">
        <div class="rate-seo">
          <h3>本市房价走势</h3>
          <div id="cont" style="padding:0 20px; height:286px;"></div>
        </div>
        <div class="w1-seo sign-seo">
          <div class="tab">
            <a href="javascript:void(0);" class="on">系统公告</a>
            <a href="javascript:void(0);">公司公告</a>
          </div>
          <ul>
            <?php if(!empty($message_list)){?>
              <?php foreach($message_list as $k => $v){?>
                <li>
                  <a id="message_<?php echo $v['id'];?>" href="javascript:void(0);" title="<?php echo $v['title'];?>"><?php echo '['.date('m-d',$v['updatetime']).']';?>
                    <?php if ($v['is_top'] == 1) { ?>
                      <font style="color:red;"><?php echo $v['title'];?></font>
                    <?php } else { ?>
                      <?php echo $v['title'];?>
                    <?php } ?>
                  </a>
                  <?php
                  $link_detail = '查看详情';
                  if ($v['url'] == '/sell/lists_pub/')
                  {
                    $link_detail = '>>去合作中心';
                  }
                  else if ($v['url'] == '/house_collections/collect_sell/')
                  {
                    $link_detail = '>>去采集中心';
                  }
                  else if ($v['url'] == '/entrust_center/ent_sell/')
                  {
                    $link_detail = ' >>去营销中心';
                  }
                  $url = $v['url'] != '' ? '<a href=\''.$v['url'].'\'>' . $link_detail . '</a>' : '';
                  ?>
                  <input type="hidden" value="<?php echo nl2br($v['message']) . $url;?>"/>
                  <input type="hidden" value="<?php echo date('Y-m-d H:i:s',$v['updatetime']);?>"/>
                  <input type="hidden" value="<?php echo $v['title'];?>"/>
                </li>
              <?php }}else{?>
              <li>暂无公告</li>
            <?php }?>
          </ul>
          <ul style="display:none">
            <?php
            if(!empty($company_notice_list)){
              foreach($company_notice_list as $ke=>$vv){
                ?>
                <li>
                  <a class="<?=$vv['color']?>" id="" href="<?php echo MLS_SIGN_URL;?>/message/notice/" title="放假安排">[<?=date('m-d',$vv['createtime'])?>]<?=$vv['title']?></a>
                </li>
              <?php }}else{?>
              <li>暂无公告</li>
            <?php }?>
          </ul>
          <!--					<a class="help-seo" href="-->
          <?php //echo MLS_SIGN_URL;?><!--/homepage/web_help" target="_blank">帮助中心</a>-->
          <a class="help-seo" href="#">帮助中心</a>
        </div>
      </div>
    </div>
    <div class="right-seo">
      <div class="info-seo clearfix newH">

        <?php if ($signatory['photo']) { ?>
          <img class="info-img" src="<?=$signatory['photo']?>" width="55" height="70">
        <?php } ?>
        <p><?php echo $signatory['truename'];?>
          <b class="cur_bg_c" style="padding:0 10px;font-size: 12px;font-family: Arial;font-weight: bold;color: #FFF;text-align: center;font-style: italic;_background:url(<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/IE6_level_bj2_03.jpg) no-repeat center;cursor:pointer" onclick="openWin('my_level')">Lv<?=$level['level']?></b></p>
        <div class="clearfix"><span class="left" style="color:#8BBBEB; line-height:24px; margin-right:6px;">信用</span>
          <?php echo $signatory['trust_level']['level'];?></div>
        <a style="color:#8BBBEB;" href='<?php echo MLS_SIGN_URL;?>/my_credit/index'>我的积分：<?php echo $signatory['credit']?></a>

      </div>
      <div class="apr-seo">
        <div class="clearfix">

          <a class="<?php echo ($signatory['ident_auth_status'] == 2) ? 'apr1' : 'apr1-1'; ?>"
             href="<?php echo MLS_SIGN_URL; ?>/my_info/index/">&nbsp;
            <?php
            if($signatory['ident_auth_status'] == 2){
              echo '身份已认证';
            }else{
              echo '身份未认证';
            }
            ?>
          </a>
          <a class="<?php echo ($signatory['ident_auth_status'] == 2) ? 'apr2' : 'apr2-2'; ?>"
             href="<?php echo MLS_SIGN_URL; ?>/my_info/index/">
            <?php
            if($signatory['ident_auth_status'] == 2){
              echo '资质已认证';
            }else{
              echo '资质未认证';
            }
            ?>
          </a>
        </div>
        <p onclick="window.location.href='/my_info/index/';" style="cursor:pointer">认证可以获得更多功能哦！</p>
        <div class="info-seo clearfix paddNull">

          <a class="daily" href="javascript:void(0);">提交日报</a>
          <a class="daka dakanew" href="javascript:void(0);">考勤打卡</a>
        </div>
      </div>
      <div class="eva-seo">
        <p>好评率：
          <span>
                        <?php
                        if(!empty($signatory['good_rate'])&&$signatory['good_rate']!='-1'){
                          echo $signatory['good_rate'].'%';
                        }else{
                          echo '--';
                        }
                        ?>
                    </span>
        </p>
        <ul>
          <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;信息真实度：<span class="<?php if($signatory['appraise_and_avg']['infomation']['up_down']=='down'){echo 'down-seo';}else if($signatory['appraise_and_avg']['infomation']['up_down']=='up'){echo 'up-seo';}?>"><?php echo $signatory['appraise_and_avg']['infomation']['score'];?></span></li>
          <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;合作满意度：<span class="<?php if($signatory['appraise_and_avg']['attitude']['up_down']=='down'){echo 'down-seo';}else if($signatory['appraise_and_avg']['attitude']['up_down']=='up'){echo 'up-seo';}?>"><?php echo $signatory['appraise_and_avg']['attitude']['score'];?></span></li>
          <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;业务专业度：<span class="<?php if($signatory['appraise_and_avg']['business']['up_down']=='down'){echo 'down-seo';}else if($signatory['appraise_and_avg']['business']['up_down']=='up'){echo 'up-seo';}?>"><?php echo $signatory['appraise_and_avg']['business']['score'];?></span></li>
        </ul>
      </div>
      <div class="calendar-wrap">
        <div class="calendar" id="calendar">
          <div class="btn-wrap">
            <div class="prev-year hand" id="prev_year"></div>
            <div class="prev-month hand" id="prev_month"></div>
            <div class="next-year hand-right" id="next_year"></div>
            <div class="next-month hand-right" id="next_month"></div>
            <div class="date">
              <span id="year"></span>年<span id="month"></span>月
            </div>
          </div>
          <table>
            <thead>
            <tr class="week"><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr>
            </thead>
            <tbody>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!--载入等级分值(成长值)页面-->
<?php $this->view('my_level/level');?>
<?php if ($count_day == 1 && $signatory['city'] == 'sz') { ?>
  <div class="js_GTipsCoverWxr" style="position: absolute; opacity:0.3; filter:alpha(opacity=30);width: 100%; left: 0px; top: 0px; z-index: 199802; height: 100%; background:#000"><iframe src="about:blank" style="height:713px;width:100%;filter:alpha(opacity=0);opacity:0;scrolling=no;"></iframe></div>
  <div class="jd">
    <a class="close" href="javascript:void(0);"></a>
    <a href="<?php echo MLS_SIGN_URL;?>/finance/apply_pledge"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/ad/screen.jpg" /></a>
  </div>
<?php } ?>

<div class="pop_box_g pop_see_msg_info" id="js_see_msg_info">
  <div class="hd">
    <div class="title">消息详情</div>
    <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);">&#xe60c;</a></div>
  </div>
  <div class="mod">
    <div class="inform_inner">
      <h3 class="h3" id="message_title"></h3>
      <p class="time" id="message_time"></p>
      <p class="text index-text" id="message_content"></p>
      <div class="clearfix m_bd">
        <button class="btn-lv1 btn-mids JS_Close" type="button">确定</button>
      </div>
    </div>

  </div>
</div>
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_do_warning">
  <div class="hd">
    <div class="title">提示</div>
  </div>
  <div class="mod">
    <div class="inform_inner">
      <div class="up_inner">
        <div class="text-wrap">
          <table>
            <tbody><tr>
              <td><div class="img"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico.png" id="imgg" alt=""></div></td>
              <td class="msg"><span id="dialog_do_warnig_tip" class="bold"></span></td>
            </tr>
            </tbody></table>
        </div>
        <button class="btn-lv1 btn-mid JS_Close" id="sure_yes" type="button" href="javascript:void(0);">确定</button>
      </div>

    </div>
  </div>
</div>

<div class="pop_box_g pop_see_inform pop_no_q_up" style="width:350px;border:0px;" id="js_pop_jjsx">
  <div class="mod" style="padding:0px !important;width:350px;height:205px;">
    <div class="inform_inner">
      <div class="up_inner" style="padding:0px;">
        <div class="text-wrap">
          <div class="close_pop" style="position:absolute;right:0px;"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);">&#xe60c;</a></div>
          <div class="img"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/app.jpg" id="imgg" alt=""></div>
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" name="is_submit" >
<input type="hidden" name="" id="nowtime" value="" >
<!--添加考勤 外出登记-->
<div class="pop_box_g"  style="width:330px; height:180px; display: none;" id="work_pop">
  <div class="hd header">
    <div class="title">考勤登记</div>
    <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
  </div>
  <div class="transfer-step-kq">
    <form action="#" method="post">
      <table class="edit-table-kq">
        <tr>
          <td width="70" class="label">考勤类型：</td>
          <td colspan="6">
            <div class="qjcode">
              <a id="sbbtn" class="kqbtn JS_Close" href="javascript:void(0);" onclick='add_work(1);'><input class="radio_input" type="radio" name="status" value="1" checked="true" >上班</a>
              <?php if($is_check_work){?>
                <a class="kqbtn JS_Close" href="javascript:void(0);" onclick='add_work(2);'><input class="radio_input" type="radio" name="status" value="2" >下班</a>
              <?php }else{?>
                <a class="kqbtn kqbtn2" href="javascript:void(0);"><input class="radio_input" type="radio" name="status" value="2" >下班</a>
              <?php } ?>
              <a href="javascript:void(0);" class="kqbtn JS_Close" onclick='add_work(3);'><input class="radio_input" type="radio" name="status" value="3" >请假</a>
              <a class="kqbtn JS_Close" href="javascript:void(0);" onclick='add_work(4);'><input class="radio_input" type="radio" name="status" value="4" >外出</a>
            </div>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>

<!--请假-->
<div class="pop_box_g"  style="width:450px; height:260px; display: none;" id='leave_out'>
  <div class="hd header">
    <div class="title">请假登记</div>
    <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
  </div>
  <div class="transfer-step">
    <table class="table edit-table">
      <tr>
        <td colspan="5" id='timeShow_leave'>服务器当前时间：2015-03-01 10:00:00</td>&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="stime_leave_reminder"></span>
      </tr>
      <tr>
        <td class="label" id="add_time_type">请假时间：</td>
        <td colspan="5"><input type="text" class="time_bg" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'%y-%M-#{%d} <?=$work_day_up_time?>'})" value="" id='ltime_up_leave' onblur="check_num(3);"> -- <input type="text" class="time_bg" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'%y-%M-#{%d} <?=$work_day_up_time?>'})" value="" id='ltime_down_leave' onblur="check_num(3);"></td>
      </tr>
      <tr>
        <td class="label align-top">考勤备注：</td>
        <td colspan="5"><textarea name="" id="leave_remark" class="att-remark"></textarea></td>
      </tr>
      <tr class="btn-line">
        <td colspan="6"><button type="button" id="dialog_share_leave" class="btn-lv1 btn-left " onclick="add_work_out(3);">确定</button>
          <button type="button" class="btn-hui1 JS_Close">取消</button></td>
      </tr>
    </table>
  </div>
</div>

<!--外出-->
<div class="pop_box_g" style="width:450px; height:260px; display: none;" id='go_out'>
  <div class="hd header">
    <div class="title">外出登记</div>
    <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
  </div>
  <div class="transfer-step">
    <table class="table edit-table">
      <tr>
        <td colspan="6" id='timeShow_go'>服务器当前时间：2015-03-01 10:00:00</td>&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="stime_go_reminder"></span>
      </tr>
      <tr>
        <td class="label" id="add_time_type">外出时间：</td>
        <td colspan="5"><input type="text" class="time_bg" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'%y-%M-%d 00:00:00',maxDate:'%y-%M-%d 23:59:59'})" value="" id='ltime_up_go' onblur="check_num(4);"> -- <input type="text" class="time_bg" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'%y-%M-%d 00:00:00',maxDate:'%y-%M-%d 23:59:59'})" value="" id='ltime_down_go'  onblur="check_num(4);"></td>
      </tr>
      <tr>
        <td class="label align-top">考勤备注：</td>
        <td colspan="5"><textarea name="" id="go_remark" class="att-remark"></textarea></td>
      </tr>
      <tr class="btn-line">
        <td colspan="6"><button type="button" id="dialog_go_leave" class="btn-lv1 btn-left " onclick="add_work_out(4);">确定</button>
          <button type="button" class="btn-hui1 JS_Close">取消</button></td>
      </tr>
    </table>
  </div>
</div>
<!--打卡成功弹窗-->
<div  class="pop_box_g pop_see_inform pop_no_q_up" style=" display:none;" id='work_end_sucess'>
  <div class="hd">
    <div class="title" id='work_title'></div>
    <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
  </div>
  <div class="dakaisSucc">
    <dl class class="clearfix">
      <dt class="left"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/dakacg.gif"></dt>
      <dd class="left" id='work_contents'></dd>
    </dl>
  </div>
</div>

<!--打卡失败弹窗-->
<div  class="pop_box_g pop_see_inform pop_no_q_up" style=" display:none;" id='work_end_fail'>
  <div class="hd">
    <div class="title" id='work_title_fail'></div>
    <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
  </div>
  <div class="dakaisSucc">
    <dl class class="clearfix">
      <dt class="left"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico.png"></dt>
      <dd class="left" id='work_contents_fail'></dd>
    </dl>
  </div>
</div>

<!--我的日报详情页-->
<div class="pop_box_g zws_my_report_pop popIndex" style=" display:none;"  id="daily_pop">
  <div class="hd">
    <div class="title">日报详情</div>
    <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
  </div>
  <div class="mod">
    <div class="tab_pop_mod clear zws_my_report_popIndex">
      <dl class="zws_report_dl">
        <dd>日报标题：</dd>
        <dt>
          <input type="text" value="" class="zws_report_input newIndex_w" id="daily_title" />
          <span><em>0</em>/50</span>
        <p class="remind"></p>
        </dt>
      </dl>
      <dl class="zws_report_dl">
        <dd>工作内容：</dd>
        <dt><textarea class="zws_report_textarea newIndex_w"  id="daily_content"></textarea>
          <span  class="topH"><em>0</em>/500</span>
        <p class="remind"></p>
        </dt>
      </dl>
      <dl class="zws_report_dl">
        <dd>问题反馈：</dd>
        <dt>
          <textarea class="zws_report_textarea newIndex_w" id="daily_promble"></textarea>
          <span  class="topH"><em>0</em>/500</span>
        <p class="remind"></p>
        </dt>
      </dl>
      <dl class="zws_report_dl">
        <dd>填写日期：</dd>
        <dt class="zws_font"><?php echo date('Y') . '年' . date('m') . '月' . date('d') . '日';?></dt>
      </dl>

      <div class="btn-pane center">
        <a class="btn-lv btn-left" href="javascript:void(0)"><span class="btn_inner JS_Close" style="padding-right: 10px;" id ="validate_daily">确定</span>

        </a>
        <a class="btn-hui1 JS_Close"><span>取消</span></a>
      </div>
    </div>

  </div>
</div>

<img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls_guli/js/v1.0/home_calendar.js"></script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls_guli/js/v1.0/BreakingNews.js"></script>
<script>
  function numLimit(obj,num){
    $(obj).bind("keyup",function(){
      $(obj).parent("dt").find("p").html("");
      var len= $(obj).val().length;

      if(len > num){
        $(obj).parent("dt").find("p").html("您输入的字符超过规定字符限制！");
      }
      else{
        $(obj).next("span").find("em").html(len);
      }
    });
  }
  $(function () {

    $('#breakingnews').BreakingNews({//喜报
      title: '滚动消息',
      timer: 4000,
      effect: 'slide'
    });

    $('.sign-seo .tab').find('a').hover(function(){
      var index = $(this).index();
      $('.sign-seo .tab a').removeClass('on');
      $(this).addClass('on');
      $('.sign-seo ul').hide();
      $('.sign-seo ul').eq(index).show();
    });

    $('.jd').find('.close').click(function(){
      $('.jd, .js_GTipsCoverWxr').hide();
      SetCookie('mortgage', 1);
      //console.log(document.cookie);
    });

    function SetCookie(name, value) {
      var exp = new Date();
      exp.setTime(exp.getTime() + 3 * 24 * 60 * 60 * 1000); //3天过期
      document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exp.toGMTString()+";path=/";
      return true;
    }

    function high_charts(){
      $('#cont').highcharts({
        /*chart: {
         type: 'area',
         margin: [60, 30, 60, 100]
         },*/
        title: {
          text: ''
        },
        xAxis: {
          title: {
            text: ''
          },
          categories: <?php echo $x_data;?>,
          labels: {
            formatter: function () {
              var x_data = this.value;
              var x_data_str = x_data.substr(4,2)+'月';
              return x_data_str;
            }
          }
        },
        yAxis: {
          title: {
            text: ''
          },
          labels: {
            formatter: function() {
              return this.value +'元/㎡';
            }
          },
          min: <?php echo $min_price;?>
        },
        credits: {
          enabled: false
        },
        legend:{
          enabled: true
        },
        tooltip:{
          formatter:function(){
            var x_data = this.x;
            var y_data = this.y;
            var x_str = x_data.substr(0,4)+'年'+x_data.substr(4,2)+'月';
            var y_str = y_data+'元/平方';
            var _html = x_str+'<br>';
            _html += '<span>价格</span>：<b style="color:#FEB016">'+y_str+'</b>'
            return _html;
          }
        },
        series: [{
          name: '二手房价格',
          color:'#7AB3E8',
          data: <?php echo $y_data;?>
        }]
      });
    }

    function re_width(){
      $(".content_scroll").css({
        "height":$(window).height(),
        "opacity":"0",
        "filter":"alpha(opacity=0)",
        "_display":"none"
      });
      //这里修改子banner的大小
      var w1 = $(window).width() - 305 + "px";
      var w2 = ($(window).width() - 365)/3 + "px";
      var w3 = $(window).width()*2/3 - 214 + "px";
      var w4 = $(window).width()*2/3 - 274 + "px";
      $(".left-seo, #breakingnews").css("width",w1);
      $(".w1-seo").css("width",w2);
      $(".rate-seo").css("width",w3);
      $("#cont").css("width",w4);

      setTimeout(function () {
        $(".content_scroll").animate({
          "opacity":"1",
          "filter":"alpha(opacity=100)"
        }, 100).css({
          "display":"block"
        });
      }, 30);
    };
    re_width();
    high_charts();
    $(window).resize(function(e) {
      re_width();
      high_charts();
    });

    $('#accept_cooperate,#send_cooperate').click(function(){
      var group_id = <?php echo $group_id;?>;
      if('1'==group_id){
        $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
        openWin('js_pop_do_warning');
        return false;
      }
      var href = '';
      var id = $(this).attr('id');
      if('accept_cooperate'==id){
        href = '<?php echo MLS_SIGN_URL;?>/cooperate/accept_order_list/';
      }else if('send_cooperate'==id){
        href = '<?php echo MLS_SIGN_URL;?>/cooperate/send_order_list/';
      }
      window.location.href = href;
      return false;
    });

    $('.daka').click(function(){
      var group_id = <?php echo $group_id;?>;
      if('1'==group_id){
        $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
        openWin('js_pop_do_warning');
        return false;
      }
      openWin('work_pop');
    });

    numLimit("#daily_title",50);
    numLimit("#daily_content",500);
    numLimit("#daily_promble",500);
    //弹框判断
    $('.daily').click(function(){
      $.ajax({
        type: 'get',
        url : '/my_daily/is_exist_daily/',
        dataType:'json',
        success: function(data){
          if(data['status'] == 1) {
            //如果没有提交日报
            openWin('daily_pop');
          } else {
            openWin('work_end_fail');
            $("#work_title_fail").html('工作日报');
            $("#work_contents_fail").html('您已提交过日报');
          }
        }
      });
    });
  });

  $('#validate_daily').click(function(){
    var daily_title = $.trim($('#daily_title').val());
    var daily_content = $.trim($('#daily_content').val());
    var daily_promble = $.trim($('#daily_promble').val());
    if (daily_title == '') {
      $('#daily_title').parent("dt").find("p").html("请输入日报标题");
      return false;
    }
    if (daily_content == '') {
      $('#daily_content').parent("dt").find("p").html("请输入工作内容");
      return false;
    }
    if (daily_promble == '') {
      $('#daily_promble').parent("dt").find("p").html("请输入问题反馈");
      return false;
    }
    $.ajax({
      type: 'post',
      url : '/my_daily/add_daily/',
      dataType:'json',
      data: {'title' : daily_title, 'content' : daily_content, 'promble' : daily_promble},
      success: function(data){
        if(data['status'] == 1) {
          openWin('work_end_sucess');
          $("#work_title").html('工作日报');
          $("#work_contents").html('成功提交日报');
          $("#daily_pop").hide();
          //$("#GTipsCoverdaily_pop").hide();
        }
      }
    });
  });

  $('a[id^="message_"]').click(function(){
    var title = $(this).next().next().next().val();
    var content = $(this).next().val();
    var time = $(this).next().next().val();

    $('#message_title').html(title);
    $('#message_time').html(time);
    $('#message_content').html(content);
    openWin('js_see_msg_info');
  });

  $('#abc li a').click(function(){
    var title = $(this).next().next().next().val();
    var content = $(this).next().val();
    var time = $(this).next().next().val();

    $('#message_title').html($(this).attr('title'));
    $('#message_time').html($(this).attr('updatetime'));
    $('#message_content').html($(this).attr('message'));
    openWin('js_see_msg_info');
  });

  //图片淡隐淡现
  var defaultOpts ={ interval:5000, fadeInTime:300, fadeOutTime:200 };

  var _titles = $("ul.slide-txt li");
  var _titles_bg = $("ul.op li");
  var _bodies = $("ul.slide-pic li");
  var _count = _titles.length;
  var _current = 0;
  var _intervalID = null;

  var stop = function(){
    window.clearInterval(_intervalID);
  };

  var slide = function(opts){
    if (opts){
      _current = opts.current || 0;
    } else{
      _current = (_current >= (_count - 1)) ? 0 :(++_current);
    };
    _bodies.filter(":visible").fadeOut(defaultOpts.fadeOutTime, function(){
      _bodies.eq(_current).fadeIn(defaultOpts.fadeInTime);
      _bodies.removeClass("current").eq(_current).addClass("current");
    });
    _titles.removeClass("current").eq(_current).addClass("current");
    _titles_bg.removeClass("current").eq(_current).addClass("current");
  };

  var go = function(){
    stop();
    _intervalID = window.setInterval(function(){
      slide();
    }, defaultOpts.interval);
  };

  var itemMouseOver = function(target, items){
    stop();
    var i = $.inArray(target, items);
    slide({ current:i });
  };

  _titles.hover(function(){
    if($(this).attr('class')!='current'){
      itemMouseOver(this, _titles);
    }else{
      stop();
    }
  }, go);

  _bodies.hover(stop, go);

  go();


  //考勤打卡
  function add_work(type){
    if(type == 3){
      $("#ltime_up_leave").val('');
      $("#ltime_down_leave").val('');
      $("#leave_remark").val('');
      $("#stime_leave_reminder").html('');
      openWin('leave_out');
      //$("#work_pop").hide();
      return false;
    }else if(type == 4){
      $("#ltime_up_go").val('');
      $("#ltime_down_go").val('');
      $("#go_remark").val('');
      $("#stime_go_reminder").html('');
      openWin('go_out');
      //$("#work_pop").hide();
      return false;
    }
    $.ajax({
      type: 'post',
      url : '/check_work_center/add_work/'+<?=$signatory_id?>,
      dataType:'json',
      data: {type:type},
      success: function(data){
        if(data['result'] == 'ok'){
          if(data['type'] == 1){
            $("#work_title").html('上班登记');
            $("#work_contents").html('上班打卡成功！');
          }else if(data['type'] == 2){
            $("#work_title").html('下班登记');
            $("#work_contents").html('下班打卡成功！');
          }
          openWin('work_end_sucess');
          //$("#work_pop").hide();
        }else if(data['result'] == 'once'){
          if(data['type'] == 1){
            $("#work_title_fail").html('上班登记');
            $("#work_contents_fail").html('今日已打卡！');
          }else if(data['type'] == 2){
            $("#work_title_fail").html('下班登记');
            $("#work_contents_fail").html('今日已打卡！');
          }
          openWin('work_end_fail');
        }else if(data['result'] == 'uplose'){
          $("#work_title_fail").html('下班登记');
          $("#work_contents_fail").html('请先打卡上班！');
          openWin('work_end_fail');
        }else{
          if(data['type'] == 1){
            $("#work_title_fail").html('上班登记');
            $("#work_contents_fail").html('上班打卡失败！');
          }else if(data['type'] == 2){
            $("#work_title_fail").html('下班登记');
            $("#work_contents_fail").html('下班打卡失败！');
          }
          openWin('work_end_fail');
        }
      }
    });
  }
  //请假外出
  function add_work_out(type){

    if(type == 3){
      var remark = $('#leave_remark').val();
      var ltime_up = $("#ltime_up_leave").val();
      var ltime_down = $("#ltime_down_leave").val();
    }else if(type == 4){
      var remark = $('#go_remark').val();
      var ltime_up = $("#ltime_up_go").val();
      var ltime_down = $("#ltime_down_go").val();
    }
    if(!ltime_up || !ltime_down){
      $("#stime_leave_reminder").html("请选择时间！");
      $("#stime_go_reminder").html("请选择时间！");
      return false;
    }
    if($("input[name='is_submit']").val() != 1){
      return false;
    }
    $.ajax({
      type: 'post',
      url : '/check_work_center/add_work/'+<?=$signatory_id?>,
      dataType:'json',
      data: {type:type,remark:remark,ltime_up:ltime_up,ltime_down:ltime_down},
      success: function(data){
        if(data['result'] == 'ok'){
          if(data['type'] == 3){
            $("#work_title").html('请假登记');
            $("#work_contents").html('请假登记成功！');
            $("#leave_out").hide();
          }else if(data['type'] == 4){
            $("#work_title").html('外出登记');
            $("#work_contents").html('外出登记成功！');
            $("#go_out").hide();
          }

          openWin('work_end_sucess');
        }else if(data['result'] == 'timeout'){
          if(data['type'] == 3){
            $("#work_title_fail").html('请假登记');
            $("#work_contents_fail").html('请假截至时间小于起始时间！');
            $("#leave_out").hide();
          }else if(data['type'] == 4){
            $("#work_title_fail").html('外出登记');
            $("#work_contents_fail").html('外出截至时间小于起始时间！');
            $("#go_out").hide();
          }
          openWin('work_end_fail');
        }else if(data['result'] == 'timerepeat'){
          if(data['type'] == 3){
            $("#work_title_fail").html('请假登记');
            $("#work_contents_fail").html('请不要提交重复的时间区间！');
            $("#leave_out").hide();
          }else if(data['type'] == 4){
            $("#work_title_fail").html('外出登记');
            $("#work_contents_fail").html('请不要提交重复的时间区间！');
            $("#go_out").hide();
          }
          openWin('work_end_fail');
        }else{
          if(data['type'] == 3){
            $("#work_title_fail").html('请假登记');
            $("#work_contents_fail").html('请假登记失败！');
            $("#leave_out").hide();
          }else if(data['type'] == 4){
            $("#work_title_fail").html('外出登记');
            $("#work_contents_fail").html('外出登记失败！');
            $("#go_out").hide();
          }
          openWin('work_end_fail');
        }
      }
    });
  }

  //时间动态显示
  var t = null;
  t = setTimeout(time,1000);//开始执行
  function time()
  {
    clearTimeout(t);//清除定时器
    dt = new Date();
    var h=dt.getHours();
    var m=dt.getMinutes();
    var s=dt.getSeconds();
    var y=dt.getFullYear();
    var mo=dt.getMonth()+1;
    var d=dt.getDate();
    if(('"'+mo+'"').length < 4){mo = '0'+mo;}//alert(('"'+mo+'"').length);
    if(('"'+d+'"').length < 4){d = '0'+d;}
    if(('"'+h+'"').length < 4){h = '0'+h;}
    if(('"'+m+'"').length < 4){m = '0'+m;}
    if(('"'+s+'"').length < 4){s = '0'+s;}
    document.getElementById("timeShow_leave").innerHTML =  "服务器当前时间："+y+"-"+mo+"-"+d+" "+h+":"+m+":"+s;
    document.getElementById("timeShow_go").innerHTML =  "服务器当前时间："+y+"-"+mo+"-"+d+" "+h+":"+m+":"+s;
    t = setTimeout(time,1000); //设定定时器，循环执行
    $("#nowtime").val(y+"-"+mo+"-"+d+" "+h+":"+m+":"+s);
  }

  /*
   *	aim:	时间等 onchange 事件的校验
   *	author: angel_in_us
   *	date:	2015.03.04
   */
  function check_num(type){
    var nowtime = $("#nowtime").val(); //当前时间
    //alert(nowtime);
    if(type == 3){
      $("#stime_leave_reminder").html("");
      var stimemin	  =    $("#ltime_up_leave").val();		//最小时间
      var stimemax	  =    $("#ltime_down_leave").val();		//最大时间
      if(stimemin<nowtime){
        $("#stime_leave_reminder").html("起始时间不能小于当前时间！");
        $("input[name='is_submit']").val('0');
        return;
      }else{
        $("#stime_leave_reminder").html("");
      }
      //最小时间 stimemin 必须小于 最大时间 stimemax
      if(stimemin && stimemax){
        if(stimemin>=stimemax){
          $("#stime_leave_reminder").html("时间筛选区间输入有误！");
          $("input[name='is_submit']").val('0');
          return;
        }else{
          $("#stime_leave_reminder").html("");
          $("input[name='is_submit']").val('1');
          $("#dialog_share_leave").addClass('JS_Close');
        }
      }
    }else{
      $("#stime_go_reminder").html("");
      var stimemin	  =    $("#ltime_up_go").val();		//最小时间
      var stimemax	  =    $("#ltime_down_go").val();		//最大时间
      /*if(stimemin<nowtime){
       $("#stime_go_reminder").html("起始时间不能小于当前时间！");
       $("input[name='is_submit']").val('0');
       return;
       }else{
       $("#stime_go_reminder").html("");
       }*/
      //最小时间 stimemin 必须小于 最大时间 stimemax
      if(stimemin && stimemax){
        if(stimemin>=stimemax){
          $("#stime_go_reminder").html("时间筛选区间输入有误！");
          $("input[name='is_submit']").val('0');
          return;
        }else{
          $("#stime_go_reminder").html("");
          $("input[name='is_submit']").val('1');
          $("#dialog_go_leave").addClass('JS_Close');
        }
      }
    }
  }

</script>
</body>
</html>
