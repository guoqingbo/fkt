<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script>
window.parent.addNavClass(1);
</script>
<body class="home-body">
    <div class="index_scroll" id="js_index_scroll" style="position: absolute; left: 0; top: 0; width: 100%;">
        <div class="home-wrap clearfix" style=" margin: 0 auto">
	<div class="left-wrap clearfix">
		<div class="work-remind">
			<div class="remind-tab">
				<div class="tab"></div>
				<p>工作提醒</p>
				<div class="arrow"></div>
			</div>
			<div class="remind-content clearfix">
				<ul>
					<li class="li1">
						<div>
							<div class="task-wrap clearfix"><span class="task-num"><?php echo $task_num;?></span><p class="p1">条新任务</p></div>
						</div>
						<p class="desc"><a href="../my_task/index/">房源客源跟进</a></p>
					</li>
					<li class="li2">
						<div class="task-wrap clearfix">
                            <p class="p1">待处理<a href="#"><span class="highlight" id="accept_wait_do_a"><?php echo $accept['all_estas_num1'];?></span></a></p>
                            <p class="p2" id="accept_wait_appraise">待评价<a href="#"><span class="highlight"><?php echo $accept['all_estas_num2'];?></span></a></p>
                        </div>
						<p class="desc"><a href="../cooperate/accept_order_list/">我收到的合作申请</a></p>
					</li>
					<li class="li3">
						<div class="task-wrap clearfix">
                            <p class="p1">待处理<a href="#"><span class="highlight" id="send_wait_do_a"><?php echo $send['all_estas_num1'];?></span></a></p>
                            <p class="p2" id="send_wait_appraise">待评价<a href="#"><span class="highlight"><?php echo $send['all_estas_num2'];?></span></a></p></div>
						<p class="desc"><a href="../cooperate/send_order_list/">我发起的合作申请</a></p>
					</li>
					<li class="li4">
						<a href="javascript:void(0);" onclick="openWin('js_attendance_kq');">考勤打卡</a>
					</li>
				</ul>
			</div>
            <form id="cooperate_form" method="post" action="">
                <input type="hidden" id="estas" name="estas" value=""/>
                <input type="hidden" id="esta" name="esta" value="0"/>
                <input type="hidden" id="order_sn" name="order_sn" value=""/>
                <input type="hidden" id="block_name" name="block_name" value=""/>
                <input type="hidden" id="agentid_w" name="agentid_w" value="0"/>
                <input type="hidden" id="brokerid_w" name="brokerid_w" value="0"/>
                <input type="hidden" id="broker_name" name="broker_name" value=""/>
                <input type="hidden" id="phone" name="phone" value=""/>
                <input type="hidden" id="agentid" name="agentid" value=""/>
                <input type="hidden" id="page" name="page" value="1"/>
            </form>
		</div>
		<div class="quick-entrance clearfix">
			<div class="left">
				<div class="tab"></div>
				<p>快捷入口</p>
				<div class="arrow"></div>
			</div>
			<div class="right">
				<table>
					<tbody>
						<tr>
							<td>
								<a href="../sell/lists/" class="a1"></a>
								<p>我的房源</p>
							</td>
							<td>
								<a href="../my_collections/my_collect_sell" class="a2"></a>
								<p>我的采集</p>
							</td>
							<td>
								<a href="../sell/publish/" class="a3"></a>
								<p>发布房源</p>
							</td>
							<td>
								<a href="../customer/publish/" class="a4"></a>
								<p>发布客源</p>
							</td>
							<td>
								<a href="../sell/lists_pub/" class="a5"></a>
								<p>查看合作房客源</p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="part3">
			<div class="left">
				<div class="dv1">
					<p>采集房源</p>
					<div></div>
				</div>
				<div class="dv2">
					<p><span class="highlight">实时&nbsp;</span>采集房产网站个人房源</p>
					<p>最近更新<span class="highlight">&nbsp;<?php echo $recent_hosue_num;?>&nbsp;</span>条个人房源信息</p>
					<p>当前有<span class="highlight">&nbsp;<?php echo $broker_num;?>&nbsp;</span>名经纪人</p>
					<p>查询了<span class="highlight">&nbsp;<?php echo $recent_brower_hosue_num;?>&nbsp;</span>条个人房源信息</p>
					<div class="btn-wrap"><a href="../house_collections/collect_sell">我要查看</a></div>
				</div>
			</div>
			<div class="right">
				<p>合作中心</p>
				<div class="step-wrap">
					<ul class="clearfix">
						<li class="step1-name">查看合作房客源</li>
						<li class="step2-name">申请合作</li>
						<li class="step3-name">确认佣金分配</li>
						<li class="step4-name">确认合作生效</li>
						<li class="step5-name">交易成功</li>
					</ul>
				</div>
				<div class="btn-wrap"><a href="../sell/lists_pub/">立即开始合作</a></div>
			</div>
		</div>
		<div class="part4">
			<div class="left">
				<div class="top clearfix">
					<div class="block-name">帮助中心</div>
					<a href="../community/help_center">查看更多</a>
				</div>
				<div class="bottom">
					<ul>
                        <?php foreach($help_center_data as $k=>$v){?>
						<li><a href="../community/help_center/<?php echo $v['parent_id'];?>"><?php echo $v['title'];?></a></li>
                        <?php } ?>
					</ul>
				</div>
			</div>
			<div class="right">
				<div class="top clearfix">
					<div class="block-name">系统公告</div>
					<a href="../message/bulletin">查看更多</a>
				</div>
				<div class="bottom">
                    <?php if(!empty($message_list)){?>
                    <?php foreach($message_list as $k=>$v){?>
                    <dl class="clearfix">
						<dt>
                            <a href="javascript:void(0);" id="message_<?php echo $v->id;?>"><?php echo $v->title;?></a>
                            <input type="hidden" value="<?php echo $v->message;?>"/>
                            <input type="hidden" value="<?php echo date('Y-m-d',$v->updatetime);?>"/>
                        </dt>
						<dd><?php echo date('Y-m-d',$v->updatetime);?></dd>
					</dl>
                    <?php }?>
                    <?php }else{
                        echo '抱歉，您暂无公告消息！';
                    }?>
				</div>
			</div>
		</div>
	</div>
	<div class="right-wrap clearfix">
		<div class="user-info-wrap">
			<div class="info info1 clearfix">
				<p><?php echo $broker['truename'];?></p>
                <?php echo $broker['trust_level']['level'];?>
			</div>
			<div class="info info2 clearfix">
                <p class="p1 <?php if($broker['ident_auth'] != 1){ echo 'p_1';}?>">
				    <a href="../my_info/index/">
				    <?php
				    if($broker['ident_auth'] == 1){
				        echo '身份已认证';
				    }else{
				        echo '身份未认证';
				    }
				    ?>
                    </a>
			    </p>
				<p class="p2 <?php if($broker['quali_auth'] != 1){ echo 'p_2';}?>">
				    <a href="../my_info/index/">
				    <?php
				    if($broker['quali_auth'] == 1){
				        echo '资质已认证';
				    }else{
				        echo '资质未认证';
				    }
				    ?>
	                </a>
                </p>
				<p class="p3">认证可以获得更多功能哦！</p>
			</div>
			<div class="info info3 clearfix">
				<div class="dv1 clearfix">
                    <p class="p1">好&nbsp;&nbsp;&nbsp;&nbsp;评&nbsp;&nbsp;&nbsp;&nbsp;率：<span>
                        <?php
                            if(!empty($broker['good_rate'])&&$broker['good_rate']!='-1'){
                                echo $broker['good_rate'].'%';
                            }else{
                                echo '&nbsp;&nbsp;&nbsp;&nbsp;--%';
                            }
                        ?>
                        </span></p>
					<p class="p2">
                        <span class="rate">
                            <?php
                            if($broker['good_rate']>0){
                            ?>
                            <?php if($broker['good_rate_avg_high']>0){?>
                            比平均值高<?php echo abs($broker['good_rate_avg_high']);?>%
                            <?php }else if($broker['good_rate_avg_high']<0){?>
                            比平均值低<?php echo abs($broker['good_rate_avg_high']);?>%
                            <?php }else if($broker['good_rate_avg_high']==0){?>
                            与平均值持平
                            <?php }?>
                            <?php }?>
                        </span></p>
				</div>
				<div class="dv2 clearfix">
					<p class="p1">合作成功率：<span>
					<?php //php echo strip_end_0($broker['cop_suc_ratio']);
                    if(!empty($cop_succ_ratio_info['cop_succ_ratio']) && $cop_succ_ratio_info['cop_succ_ratio'] > 0){
                        echo $cop_succ_ratio_info['cop_succ_ratio'].'%';
                    }else if($cop_succ_ratio_info['cop_succ_ratio'] == 0 ) {
                        if(!empty($cop_succ_ratio_info['cooperate_num']) && $cop_succ_ratio_info['cooperate_num'] > 0) {
                    ?>
                    0%
                    <?php
                        } else {
                    ?>
                    --%
                    <?php
                        }
                    }
                    ?>
					</span></p>
					<p class="p2">
                        <span class="rate"><!--
                            <?//php if($broker['cop_suc_ratio'] > $broker['cop_suc_ratio_avg']){?>
                            比平均值高<?//php echo abs($broker['cop_suc_ratio'] - $broker['cop_suc_ratio_avg']);?>%
                            <?//php }else if($broker['cop_suc_ratio'] < $broker['cop_suc_ratio_avg']){?>
                            比平均值低<?//php echo abs($broker['cop_suc_ratio'] - $broker['cop_suc_ratio_avg']);?>%
                            <?//php }else if($broker['cop_suc_ratio'] == $broker['cop_suc_ratio_avg']){?>
                            与平均值持平
                            <//?php }?> -->
                            <?php
                            if($cop_succ_ratio_info['cop_succ_ratio']>0){
                            ?>
                            <span class="gy gy02">比平均值
                            <?php
                                $n = $avg_cop_suc_ratio > 0 ? round(($cop_succ_ratio_info['cop_succ_ratio'] - $avg_cop_suc_ratio)/$avg_cop_suc_ratio , 2) : 0;
                                if($n>0){
                                    echo '高'.abs($n).'%';
                                }elseif($n<0){
                                    echo '低'.abs($n).'%';
                                }else{
                                    echo '持平';
                                }
                            ?>
                            </span>
                            <?php
                            }
                            ?>
                        </span>
                    </p>
				</div>
			</div>
			<div class="info info4 clearfix">
				<div class="dv1">
					<p class="p1">信息真实度</p>
                    <p class="p2 <?php if($broker['appraise_and_avg']['infomation']['up_down']=='down'){echo 'down';}else{echo 'up';}?>"><?php echo $broker['appraise_and_avg']['infomation']['score'];?><span>&nbsp;</span></p>
				</div>
				<div class="dv2">
					<p class="p1">态度满意度</p>
                    <p class="p2 <?php if($broker['appraise_and_avg']['attitude']['up_down']=='down'){echo 'down';}else{echo 'up';}?>"><?php echo $broker['appraise_and_avg']['attitude']['score'];?><span>&nbsp;</span></p>
				</div>
				<div class="dv3">
					<p class="p1">业务专业度</p>
                    <p class="p2 <?php if($broker['appraise_and_avg']['business']['up_down']=='down'){echo 'down';}else{echo 'up';}?>"><?php echo $broker['appraise_and_avg']['business']['score'];?><span>&nbsp;</span></p>
				</div>
			</div>
		</div>
		<div class="calendar-wrap">
			<div class="calendar" id="calendar">
				<div class="btn-wrap">
            		<div class="prev-year hand" id="prev_year"></div>
            		<div class="prev-month hand" id="prev_month"></div>
            		<div class="date">
            			<span id="year"></span>年<span id="month"></span>月
            		</div>
            		<div class="next-month hand" id="next_month"></div>
            		<div class="next-year hand" id="next_year"></div>
            	</div>
				<table>
		            <thead>
		            <tr class="week">
		                <th>日</th>
		                <th>一</th>
		                <th>二</th>
		                <th>三</th>
		                <th>四</th>
		                <th>五</th>
		                <th>六</th>
		            </tr>
		            </thead>
		            <tbody>
		            <tr>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr>
		            <tr>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr>
		            <tr>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr>
		            <tr>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr>
		            <tr>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr>
		            <tr>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr>
		            </tbody>
		        </table>
			</div>
			<div class="bottom">
                <ul class="clearfix">
					<li class="gap"><a href="../sell/lists/" class="add">新增任务</a></li>
					<li class="gap"><a href="../my_task/index/">查看我的所有任务</a></li>
				</ul>
			</div>
		</div>
		<div class="adv-wrap"></div>
	</div>
</div>
</div>
<div class="pop_box_g pop_see_msg_info" id="js_see_msg_info">
    <div class="hd">
        <div class="title">消息详情</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);">&#xe60c;</a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
      		  <h3 class="h3" id="message_title">哈哈哈</h3>
		      <p class="time" id="message_time"></p>
              <p class="text" id="message_content">哈啊呵呵</p>
		       <div class="m_bd">
	       	 		<button class="btn-lv1 btn-mid JS_Close" type="button">确定</button>
		       </div>
         </div>

    </div>
</div>

<!--添加考勤 外出登记-->
<div class="pop_box_g kqBoxheight" id="js_attendance_kq">
    <div class="hd header">
        <div class="title">添加考勤</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="warrant-step-kq">
        <form action="#" method="post">
            <table class="edit-table-kq">
                <tr>
                    <td width="70" class="label">考勤类型：</td>
                    <td colspan="6">
                        <div class="qjcode">
                            <a class="kqbtn kqbtnCur" href="javascript:void(0);"><input class="radio_input" type="radio" name="type" value="1" rel="上班" checked="true">上班</a>
                            <a class="kqbtn" href="javascript:void(0);"><input class="radio_input" type="radio" name="type" value="2" rel="下班">下班</a>
                            <a class="kqbtn" href="javascript:void(0);"><input class="radio_input" type="radio" name="type" value="3" rel="请假">请假</a>
                            <a class="kqbtn" href="javascript:void(0);"><input class="radio_input" type="radio" name="type" value="4" rel="外出">外出</a>
                        </div>
                    </td>
                </tr>
                <tr class="hide time_tr">
                    <td class="label" id="add_time_type">上班时间：</td>
                    <td colspan="6">
                        <input type="text" size="14" name="datetime1" id="datetime1" class="time_bg timer_width" readonly="readonly" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                        <span class="back_time hide">--</span>
                        <input type="text" size="14" name="datetime2" id="datetime2" class="time_bg wct_width back_time hide" readonly="readonly" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                    </td>
                </tr>
                <tr class="hide remark_tr">
                    <td class="label align-top">备注：</td>
                    <td colspan="6"><textarea class="att-remark" name="remarks" id="remarks"></textarea></td>
                </tr>
                <tr class="bcBtn">
                    <td colspan="6"><input type="button" class="btn-lv1 btn-mid" value="提交" onclick="add_attendance();"/></td>
                </tr>
            </table>
        </form>
    </div>
</div>

<!--操作结果弹出警告-->
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
			<div class="text-wrap">
                    <table>
                        <tr>
                            <td><div class="img"><img alt="" id="imgg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></div></td>
                            <td class="msg" ><span class="bold" id="dialog_do_warnig_tip"></span></td>
                        </tr>
                    </table>
                </div>
				<a href="javascript:void(0);" id="sure_yes" class="btn-lv1 btn-mid btn_qd_text JS_Close" style="margin:0 auto !important">确定</a>
            </div>

        </div>
    </div>
</div>

<!--打卡成功提示-->
<div id="add_attendance" class="pop_box_g pop_see_inform set_tj_WH">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="tijiaoSucc">
	<p class="left">提交成功！</p>
    </div>
</div>

<script type="text/javascript">
$(function(){
    $(".qjcode .kqbtn").click(function(){
        var current = $(this);
        current.parent(".qjcode").find(".radio_input").attr("checked",false);
        current.siblings(".kqbtn").removeClass("kqbtnCur");
        current.find(".radio_input").attr("checked",true);
        current.addClass("kqbtnCur");
        var radioValue=current.find(".radio_input").val();
        if(radioValue==3 || radioValue==4)
        {
            $(".remark_tr").show();
            $(".time_tr").show();
            if(!$("#js_attendance_kq").hasClass("modifykqBoxheight"))
            $("#js_attendance_kq").addClass("modifykqBoxheight")
            var text=current.find(".radio_input").attr("rel");
            $("#add_time_type").text(text + '时间：');
            if(radioValue == 3){
                $("#datetime1").removeClass("timer_width");
                $("#datetime1").addClass("wct_width");
                $(".back_time").show();
            }else{
                $("#datetime1").removeClass("wct_width");
                $("#datetime1").addClass("timer_width");
                $(".back_time").hide();
            }
        }else{
            $(".remark_tr").hide();
            $(".time_tr").hide();
            if($("#js_attendance_kq").hasClass("modifykqBoxheight"))
            $("#js_attendance_kq").removeClass("modifykqBoxheight")
        }
    })
});
//提交考勤
function add_attendance()
{
	var act = 1;
	var type = $(".radio_input:checked").val();
	if(!type && act == 1){
            $("#dialog_do_warnig_tip").html("请选择考勤类型");
            openWin('js_pop_do_warning');
            act = 0;
	}
        if(type == 1 || type == 2){
            var data_arr = {type:type};
        }else if(type == 3 || type == 4){
            var datetime1 = $.trim($("#datetime1").val());
            var datetime2 = $.trim($("#datetime2").val());
            if(type == 3){
                if((!datetime1 || !datetime2) && act == 1){
                    $("#dialog_do_warnig_tip").html("请输入请假时间");
                    openWin('js_pop_do_warning');
                    act = 0;
                }
                if(datetime1 >= datetime2 && act == 1){
                    $("#dialog_do_warnig_tip").html("开始时间不能小于结束时间");
                    openWin('js_pop_do_warning');
                    act = 0;
                }
            }
            if(type == 4){
                if(!datetime1 && act == 1){
                    $("#dialog_do_warnig_tip").html("请输入外出时间");
                    openWin('js_pop_do_warning');
                    act = 0;
                }
            }
            var remarks = $.trim($("#remarks").val());
            if((remarks.length <= 0) && act == 1){
                $("#dialog_do_warnig_tip").html("请输入备注");
                openWin('js_pop_do_warning');
                act = 0;
            }
            if(type == 3){
                var data_arr = {type:type,datetime1:datetime1,datetime2:datetime2,remarks:remarks};
            }
            if(type == 4){
                var data_arr = {type:type,datetime1:datetime1,remarks:remarks};
            }
        }
	if(act == 1){
            $.ajax({
                url: "<?php echo MLS_URL;?>/attendance/add_attendance_ajax/",
                type: "POST",
                dataType: "json",
                data:data_arr,
                success: function(data) {
                    if(data['errorCode'] == '401')
                    {
                        login_out();
                        return false;
                    }
                    else if(data['errorCode'] == '403')
                    {
                        permission_none();
                        return false;
                    }

                    if(data['result'] == 'ok')
                    {
                        openWin('add_attendance');
                    }else{
                        var msg = data['msg'];
                        $("#dialog_do_warnig_tip").html(msg);
                        openWin('js_pop_do_warning');
                    }
                }
            });
	}
}
</script>


<script type="text/javascript">
    $(function(){

		$("#js_index_scroll").css("height",$(window).height()+"px");
		$(window).resize(function(e) {
            $("#js_index_scroll").css("height",$(window).height()+"px")
        });

        $('a[id^="message_"]').click(function(){
            var title = $(this).html();
            var content = $(this).next().val();
            var time = $(this).next().next().val();
            $('#message_title').html(title);
            $('#message_time').html(time);
            $('#message_content').html(content);
            openWin('js_see_msg_info');
        });

        //待处理、待评价表单提交
        //收到的合作申请（待处理）
        $('#accept_wait_do_a').click(function(){
            $('#cooperate_form').attr('action','../cooperate/accept_order_list/');
            $('#estas').val('wait_do_a');
            $('#cooperate_form').submit();
        });
        //收到的合作申请（待评价）
        $('#accept_wait_appraise').click(function(){
            $('#cooperate_form').attr('action','../cooperate/accept_order_list/');
            $('#estas').val('wait_appraise');
            $('#cooperate_form').submit();
        });
        //发起的合作申请（待处理）
        $('#send_wait_do_a').click(function(){
            $('#cooperate_form').attr('action','../cooperate/send_order_list/');
            $('#estas').val('wait_do_b');
            $('#cooperate_form').submit();
        });
        //收到的合作申请（待评价）
        $('#send_wait_appraise').click(function(){
            $('#cooperate_form').attr('action','../cooperate/send_order_list/');
            $('#estas').val('wait_appraise');
            $('#cooperate_form').submit();
        });
    });
</script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/home_calendar.js"></script>
</body>

