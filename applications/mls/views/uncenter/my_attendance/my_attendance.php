<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<div class="tab_box" id="js_tab_box">
	<a class="link" href="/my_info/index/"><span class="iconfont">&#xe601;</span>个人资料</a>
	<a href="/my_attendance/index/" class="link link_on"><span class="iconfont">&#xe601;</span>个人考勤</a>
    <a href="#" class="link"><span class="iconfont">&#xe601;</span>充值管理</a>
	<a class="link" href="/message/bulletin"><span class="iconfont">&#xe601;</span>消息管理</a>
	<a class="link" href="/my_log/index/"><span class="iconfont">&#xe601;</span>工作日志</a>
	<a class="link" href="/my_evaluate/index/"><span class="iconfont">&#xe601;</span>我的评价</a>
	<a class="link" href="/my_remind/index/"><span class="iconfont">&#xe601;</span>事件提醒</a>
	<a class="link" href="/my_task/index/"><span class="iconfont">&#xe601;</span>跟进任务</a>
	<a class="link" href="/my_deal_sell/index/"><span class="iconfont">&#xe601;</span>我的成交</a>
	<a class="link" href="/my_growing_punish/index/"><span class="iconfont">&#xe601;</span>我的成长</a>
	<a href="#" class="link"><span class="iconfont">&#xe601;</span>个人记事本</a>
</div>
<div class="search_box clearfix" id="js_search_box">
    <form action="" method="post" id="search_form">
    <div class="fg_box">
        <p class="fg fg_tex">时间：</p>
        <div class="fg gg">
            <input type="text" name="date" id="date" class="input w60"  onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM'})" value="<?php echo $post_param['date'] ? $post_param['date'] : date("Y-m");?>">
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
    </div>
    </form>
</div>
<h1 class="attendance-title"><?php echo $date_str;?>&nbsp;&nbsp;<?php echo $broker_name;?>&nbsp;&nbsp;考勤表</h1>
<div class="attendance-wrap">
    <div class="thead">
        <table>
            <thead>
                <tr>
                    <th width="14.2%">星期日</th>
                    <th width="14.2%">星期一</th>
                    <th width="14.2%">星期二</th>
                    <th width="14.2%">星期三</th>
                    <th width="14.2%">星期四</th>
                    <th width="14.2%">星期五</th>
                    <th width="14.2%">星期六</th>
                </tr>
            </thead>
        </table>
    </div>
    <div style="margin-right:17px;">
        <div class="tbody">
            <table>
                <tbody>
                    <?php
                    if($date_array){
                        foreach($date_array as $key=>$val){
                    ?>
                        <?php if($val['week'] == 0){?>
                        <tr>
                        <?php }?>
                        <?php if($key == 1 && $val['week'] > 0){?>
                        <tr>
                            <?php for($i = 1; $i <= $val['week'];$i++){?>
                            <td width="14.2%">
                                <div class="record-wrap">
                                    <div class="day"></div>
                                    <div class="record"></div>
                                </div>
                            </td>
                            <?php }?>
                        <?php }?>
                            <td width="14.2%">
                                <div class="record-wrap<?php if($val['date'] == date("Y-m-d")){echo " active";}?>">
                                    <div class="day"><?php echo $key;?></div>
                                    <div class="record">
                                        <?php
                                        if($val['list']){
                                        ?>
                                        <ul>
                                        <?php
                                            foreach($val['list'] as $k=>$v){
                                        ?>
                                            <li>
                                                <a href="javascript:void(0);">
                                                    <span><?php echo $config['type'][$v['type']];?></span>
                                                    <span class="time"><?php echo substr($v['datetime1'], 11);?></span>
                                                    <span<?php if($v['status'] == 0){echo " class='error'";}?>>
                                                        <?php if($v['status'] == 1){?>
                                                        正常
                                                        <?php }else{
                                                            if($v['type'] == 1){
                                                                echo "迟到";
                                                            }elseif($v['type'] == 2) {
                                                                echo "早退";
                                                            }else{
                                                                echo "未归";
                                                            }
                                                        }?>
                                                    </span>
                                                </a>
                                            </li>
                                        <?php
                                            }
                                        ?>
                                        </ul>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </td>
                        <?php if($val['week'] == 6){?>
                        </tr>
                        <?php }?>
                        <?php if($key == $date_t && $val['week'] < 6){?>
                            <?php for($i = 1; $i <= 6-$val['week'];$i++){?>
                            <td width="14.2%">
                                <div class="record-wrap">
                                    <div class="day"></div>
                                    <div class="record"></div>
                                </div>
                            </td>
                            <?php }?>
                        </tr>
                        <?php }?>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function reset() {
        window.location.href = window.location.href;
        window.location.reload;
    }
</script>

