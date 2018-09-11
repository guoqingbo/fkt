<div class="hd">
    <div class="title">合作申请详情</div>
    <div class="close_pop"></div>
</div>
<div class="cooperation_detailed">
<?php if(is_array($cooperate_info) && !empty($cooperate_info)) { ?>
<p class="c_title"> 合同编号：<strong class="color"><?php echo $cooperate_info['order_sn'];?></strong></p>
<div class="clearfix cooperation_num_b">
   <div class="left item <?php if($cooperate_info['step'] == 1){?>item02<?php } ?>">
       <div class="bz">
          <img class="img01" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01f_03-06.png">
          <p>合作申请</p>
       </div>
       <div class="tipe">
           <div class="sj">&nbsp;</div>
           <?php if($cooperate_info['step'] >= 1){ ?>
                <?php if($cooperate_info['step'] == 1 && $cooperate_info['esta'] == 2){?>
                    <p class="t"><?php echo $cooperate_info['config']['esta'][$cooperate_info['esta']];?></p>
                    <p><?php echo date('Y-m-d H:i:s' , $cooperate_info['creattime']);?></p>
                <?php } else if($cooperate_info['step'] == 1 && $cooperate_info['esta'] == 5){?>
                    <p class="t t_j"><?php echo $cooperate_info['config']['esta'][$cooperate_info['esta']];?></p>
                    <?php if(!empty($cooperate_info['refuse_reason']) && $cooperate_info['refuse_reason']['step'] == 1){?>
                    <p>拒绝原因：
                        <?php if($cooperate_info['refuse_reason']['type'] != 4){ ?>
                        <?php echo $cooperate_info['config']['refuse_reason'][$cooperate_info['refuse_reason']['type']];?>
                        <?php }else{ ?>
                        <?php echo $cooperate_info['refuse_reason']['reason'];?>
                        <?php }?>
                    </p>
                    <p><?php echo date('Y-m-d H:i:s',$cooperate_info['step_time']);?></p>
                    <?php }?>
                <?php } else if($cooperate_info['step'] == 1 && $cooperate_info['esta'] == 6){?>
                    <p class="t t_j"><?php echo $cooperate_info['config']['esta'][$cooperate_info['esta']];?></p>
                    <?php if(!empty($cooperate_info['cancel_reason']) && $cooperate_info['cancel_reason']['step'] == 1){?>
                    <p>取消原因：
                        <?php if($cooperate_info['cancel_reason']['type'] != 4){ ?>
                            <?php echo $cooperate_info['config']['cancel_reason'][$cooperate_info['cancel_reason']['type']];?>
                        <?php }else{ ?>
                            <?php echo $cooperate_info['cancel_reason']['reason'];?>
                        <?php }?>
                    </p>
                    <p><?php echo date('Y-m-d H:i:s',$cooperate_info['step_time']);?></p>
                    <?php }?>
                <?php } else if( !empty($cooperate_info['log_record']['3']['4'])){?>
                    <?php if($cooperate_info['step'] > 1) {?>
                    <p class="t">已完成</p>
                    <?php }else {?>
                    <p class="t"><?php echo $cooperate_info['config']['esta'][$cooperate_info['log_record']['1']['2']['esta']];?></p>
                    <?php } ?>
                    <p><?php echo date('Y-m-d H:i:s',$cooperate_info['log_record']['1']['1']['dateline']);?></p>
                <?php } else {?>
                <p class="t <?php if($cooperate_info['esta'] == 10 || $cooperate_info['esta'] == 11){?>t_j<?php }?>"><?php echo $cooperate_info['config']['esta'][$cooperate_info['esta']];?></p>
                    <p><?php echo date('Y-m-d H:i:s',$cooperate_info['creattime']);?></p>
                <?php }?>
           <?php }?>
       </div>
   </div>
    <div class="jt">
        <img class="img01" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01j_15.png">
    </div>
     <?php if($cooperate_info['step'] >= 3){?>
        <div class="left item <?php if($cooperate_info['step'] < 3) { ?>item_none<?php }else if($cooperate_info['step'] == 3) { ?>item02<?php } ?>">
            <div class="bz">
                <img class="img01" alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01h_09.png">
               <p>确认合作</p>
            </div>
            <div class="tipe">
                <div class="sj">&nbsp;</div>
                <?php if($cooperate_info['step'] == 3 && $cooperate_info['esta'] == 6){?>
                    <p class="t t_j"><?php echo $cooperate_info['config']['esta'][$cooperate_info['esta']];?></p>
                    <?php if(!empty($cooperate_info['cancel_reason']) && $cooperate_info['cancel_reason']['step'] == 3){?>
                        <p>取消原因：
                        <?php if($cooperate_info['cancel_reason']['type'] != 4){ ?>
                        <?php echo $cooperate_info['config']['cancel_reason'][$cooperate_info['cancel_reason']['type']];?>
                        <?php }else{ ?>
                        <?php echo $cooperate_info['cancel_reason']['reason'];?>
                        <?php }?>
                        </p>
                        <p><?php echo date('Y-m-d H:i:s',$cooperate_info['step_time']);?></p>
                    <?php }?>
                <?php } else if(!empty($cooperate_info['log_record']['3']['4'])){?>
                    <?php if($cooperate_info['step'] > 3) {?>
                    <p class="t">已完成</p>
                    <?php }else {?>
                    <p class="t"><?php echo $cooperate_info['config']['esta'][$cooperate_info['log_record']['2']['4']['esta']];?></p>
                    <?php } ?>
                    <p><?php echo date('Y-m-d H:i:s',$cooperate_info['log_record']['3']['4']['dateline']);?></p>
                <?php } else{ ?>
                <p class="t <?php if($cooperate_info['esta'] == 10 || $cooperate_info['esta'] == 11){?>t_j<?php }?>"><?php echo $cooperate_info['config']['esta'][$cooperate_info['esta']];?></p>
                <?php }?>
            </div>
        </div>
    <?php }else{?>
    <div class="left item item_none">
        <div class="bz">
            <img class="img01" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01h_09.png">
           <p>确认合作</p>
        </div>
    </div>
     <?php } ?>
    <div class="jt">
        <img class="img01" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01j_15.png">
    </div>

    <?php if($cooperate_info['step'] >= 4){?>
    <div class="left item <?php if($cooperate_info['step'] < 4) { ?>item_none<?php }else if($cooperate_info['step'] == 4) { ?>item02<?php } ?>">
        <div class="bz">
            <img class="img01" alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01y_12.png">
           <p>等待分佣</p>
        </div>
         <?php if($cooperate_info['step'] == 4 && $cooperate_info['esta'] == 7) { ?>
        <div class="tipe">
           <div class="sj">&nbsp;</div>
           <p class="t t_s"><?php echo $cooperate_info['config']['esta'][$cooperate_info['esta']];?></p>
           <?php echo date('Y-m-d H:i:s' , $cooperate_info['dateline']);?>
       </div>
         <?php } else if(in_array($cooperate_info['esta'] , array(6,8,9,10))) {?>
        <div class="tipe">
            <div class="sj">&nbsp;</div>
            <p class="t t_j"><?php echo $cooperate_info['config']['esta'][$cooperate_info['esta']];?></p>
            <?php echo date('Y-m-d H:i:s',$cooperate_info['dateline']);?>
        </div>
        <?php } ?>
    </div>
    <?php }else{?>
    <div class="left item item_none">
        <div class="bz">
            <img class="img01" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01y_12.png">
            <p>等待分佣</p>
        </div>
    </div>
     <?php } ?>
</div>

<p class="c_title b_t">合作房源信息</p>
<div class="cooperation_house_d clearfix">
	<div class="clearfix">
		<div class="h_pic my_h_pic">
			<img src="<?php echo $cooperate_info['houseinfo']['photo'] != '' ? $cooperate_info['houseinfo']['photo'] :MLS_SOURCE_URL.'/mls/images/v1.0/no_img_small.png';?>" width="110px" height="80px">
		</div>
		<div class="h_text my_h_text">
			<p><span class="phone"><?php echo !empty($cooperate_info['title']) ? $cooperate_info['title'] : '';?></span></p>
			<p><?php echo $cooperate_info['houseinfo']['blockname'];?>&nbsp;&nbsp;
			   <?php echo $cooperate_info['houseinfo']['districtname'];?>-<?php echo $cooperate_info['houseinfo']['streetname'];?>&nbsp;&nbsp;
			   <?php echo $cooperate_info['houseinfo']['room'];?>室<?php echo $cooperate_info['houseinfo']['hall'];?>厅<?php echo $cooperate_info['houseinfo']['toilet'];?>卫&nbsp;
			   /&nbsp;<?php echo !empty($cooperate_info['houseinfo']['forward']) ? $config['forward'][$cooperate_info['houseinfo']['forward']] : '';?>&nbsp;
			   /&nbsp;<?php echo !empty($cooperate_info['houseinfo']['fitment']) ? $config['fitment'][$cooperate_info['houseinfo']['fitment']] : '';?>&nbsp;
			   /&nbsp;<?php echo !empty($cooperate_info['houseinfo']['buildyear']) ? $cooperate_info['houseinfo']['buildyear'] : '';?>
			</p>
		</div>
		<div class="h_num">
			<p class="pirce">
				<strong class="num"><?php echo ('1'==$cooperate_info['houseinfo']['price_danwei'])?$cooperate_info['houseinfo']['price']/$cooperate_info['houseinfo']['buildarea']/30 : strip_end_0($cooperate_info['houseinfo']['price']);?></strong>&nbsp;
			   <?php if($cooperate_info['tbl'] == 'sell'){?>
			   万
               <?php }else {
                echo ('1'==$cooperate_info['houseinfo']['price_danwei'])?'元/㎡*天':'元/月';
               }?>
			</p>
			<p><?php echo strip_end_0($cooperate_info['houseinfo']['buildarea']);?>&nbsp;平米</p>
		</div>
	</div>
     <div class="trade-wrap">
         <?php if('1' == $cooperate_info['reward_type']){ ?>
            <table class="outer-table-02">
                <tr class="tr-one">
                    <td width="120" align="center"><strong>房源合作佣金分配</strong></td>
                    <td>甲方可获得本次交易双方佣金总金额&nbsp;<strong class="f60 f14"><?=$cooperate_info['houseinfo']['commission_ratio_arr']['house']?></strong><em>|</em>乙方可获得本次交易双方佣金总金额&nbsp;<strong class="f60 f14"><?=$cooperate_info['houseinfo']['commission_ratio_arr']['house']?></strong>
                    <p class="b8b7b7">注：此佣金分配方案仅做参考，具体佣金方案需经纪人线下商定</p></td>
                </tr>
            </table>
         <?php }else if('2' == $cooperate_info['reward_type']){ ?>
            <table class="outer-table-02 outer-table-b02">
                <tr>
                    <td width="120" align="center"><strong>房源合作悬赏赏金</strong></td>
                    <td><strong class="f60 f14">¥<?php echo $cooperate_info['houseinfo']['cooperate_reward'];?> 元</strong>
                    <p class="b8b7b7">注：在房源成交后需给予合作方佣金以外的额外奖励，具体以经纪人双方线下商定为准</p></td>
                </tr>
            </table>
         <?php } ?>
     </div>
 </div>
<div class="clearfix cooperation_p_d">
   <div class="item">
       <h4 class="h4">甲方经纪人信息</h4>
       <?php if(is_array($house_broker_info) && !empty($house_broker_info)) { ?>
       <div class="m clearfix">
           <div class="pic">
               <img src="<?php echo !empty($house_broker_info['photo']) ? $house_broker_info['photo'] : MLS_SOURCE_URL.'/mls/images/v1.0/defaultface.jpg';?>" width="105" height="140" >
           </div>
           <div class="info">
               <p class="tex"><span>姓名：<?php echo $house_broker_info['truename'];?></span></p>
                <div class="tex clearfix">
                    <span class="left">等级：</span>
                    <div class="d_box">
                        <?php echo $trust_info_house;?>
                    </div>
                    <span class="left ml20">&nbsp;&nbsp;好评度：<?php echo strip_end_0($broker_house_now['good_rate']);?>%</span>
                </div>
                <p class="tex">
                <span class="s">信息真实度：<strong class="n"><?php echo $appraise_avg_info_house['infomation']['score'];?></strong></span>
                <span class="s">态度满意度：<strong class="n"><?php echo $appraise_avg_info_house['attitude']['score'];?></strong></span>
                <span class="s">业务专业度：<strong class="n"><?php echo $appraise_avg_info_house['business']['score'];?></strong></span>
                </p>
               <p class="tex">门店：<?php echo $house_broker_info['agency_name'];?><?php echo '('.$house_company_name.')';?></p>
               <p class="tex">联系方式：<?php echo $house_broker_info['phone'];?></p>
               <input type ="hidden" name="broker_a_id" id ="broker_a_id"  value="<?php echo $house_broker_info['broker_id']; ?>">
               <!--<a href="javascript:void(0)" class="im"><span class="iconfont">&#xe616;</span>在线联系</a>-->
           </div>
        </div>
       <?php }?>
   </div>
   <div class="item item_r">
       <h4 class="h4">乙方经纪人信息</h4>
           <?php if(is_array($customer_broker_info) && !empty($customer_broker_info)) { ?>
        <div class="m clearfix">
           <div class="pic">
               <img src="<?php echo !empty($customer_broker_info['photo']) ? $customer_broker_info['photo'] : MLS_SOURCE_URL.'/mls/images/v1.0/defaultface.jpg';?>" width="105" height="140" >
           </div>
           <div class="info">
               <p class="tex"><span>姓名：<?php echo $customer_broker_info['truename'];?></span></p>
                <div class="tex clearfix">
                    <span class="left">等级：</span>
                    <div class="d_box">
                         <?php echo $trust_info_customer;?>
                    </div>
                    <span class="left ml20">&nbsp;&nbsp;好评度：<?php echo strip_end_0($broker_customer_now['good_rate']);?>%</span>
                </div>
                <p class="tex">
                <span class="s">信息真实度：<strong class="n"><?php echo $appraise_avg_info_customer['infomation']['score'];?></strong></span>
                <span class="s">态度满意度：<strong class="n"><?php echo $appraise_avg_info_customer['attitude']['score'];?></strong></span>
                <span class="s">业务专业度：<strong class="n"><?php echo $appraise_avg_info_customer['business']['score'];?></strong></span>
                </p>
               <p class="tex">门店：<?php echo $customer_broker_info['agency_name'];?><?php echo '('.$customer_company_name.')';?></p>
               <p class="tex">联系方式：<?php echo $customer_broker_info['phone'];?></p>
               <!--<a href="javascript:void(0)" class="im"><span class="iconfont">&#xe616;</span>在线联系</a>-->
           </div>
           </div>
           <?php }?>
       </div>
    <input type="hidden" name="c_id" id = 'c_id' value='<?php echo $cooperate_info['id']; ?>'>
    <input type="hidden" name="secret_key" id = 'secret_key' value='<?php echo $secret_key; ?>'>
    <input type="hidden" name="step" id = 'step' value='<?php echo $cooperate_info['step']; ?>'>
    <input type="hidden" name="old_esta" id = 'old_esta' value='<?php echo $cooperate_info['esta']; ?>'>

    <!--意外终止弹框-->
    <?php
        if(!$is_admin){
    ?>
        <?php if($cooperate_info['esta'] == 10 || $cooperate_info['esta'] == 11){?>
        <input type="hidden" name="stop_reason" id ="stop_reason" value="<?php echo !empty($cooperate_info['stop_reason']) ? $cooperate_info['config']['stop_reason'][$cooperate_info['stop_reason']] : '';?>">
        <div class="pop_bg_b" style="width:920px; height:840px; cursor:pointer; margin:0; padding:0;">&nbsp; </div>
        <div class="pop_box_g pop_see_inform pop_no_q_up pop_d_j_m" id="js_pop_box_cooperation_stop" style="zoom: 1; z-index: 199909 !important; position: absolute; left: 50%; margin-left: -218.5px; margin-top: -90px; top: 50%; display: none;">
            <div class="hd">
                <div class="title"></div>
                <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"  onclick="$(window.parent.document).find('#js_pop_box_cooperation').hide();$(window.parent.document).find('#GTipsCoverjs_pop_box_cooperation').remove();$('#js_pop_box_cooperation02').remove();return false;">&#xe60c;</a></div>
            </div>
            <div class="mod">
                    <div class="inform_inner">
                       <div class="up_inner">
                          <div class="d_table_pop">
                             <table>
                                <tr>
                                   <td class="th">
                                     <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">
                                   </td>
                                   <td class="td">
                                       <p class="color" id = 'dialog_dongjie_alert_tip'></p>
                                       <p>您可以再关注下合作中心的其它合作信息，祝您顺利开单！</p>
                                    </td>
                                </tr>
                             </table>
                          </div>
                          <button class="btn-lv1 JS_Close" type="button" onclick="$(window.parent.document).find('#js_pop_box_cooperation').hide();$(window.parent.document).find('#GTipsCoverjs_pop_box_cooperation').remove();$('#js_pop_box_cooperation02').remove();return false;">确定</button>
                       </div>
                 </div>
            </div>
        </div>
        <script>
            function show_alert_window()
            {
                var esta = <?php echo $cooperate_info['esta'];?>;
                var reason_str = $('#stop_reason').val();
                if(esta == 10)
                {
                    $('#js_pop_box_cooperation_stop .title').html('合作冻结');
                    $("#dialog_dongjie_alert_tip").html("合作已被平台运营人员冻结，详情请联系400123123。");
                }
                else if(esta == 11)
                {
                    $('#js_pop_box_cooperation_stop .title').html('合作终止');

                    if(reason_str != '')
                    {
                       reason_show =  '抱歉，由于'+reason_str+'，此合作自动终止。';
                    }
                    else
                    {
                        reason_show =  '抱歉，此合作自动终止。';
                    }

                    $("#dialog_dongjie_alert_tip").html(reason_show);
                }

                $('#js_pop_box_cooperation_stop').show();
            }

            //打开弹窗
            $(function(){ show_alert_window();})
        </script>
        <?php } ?>
    <?php
        }
    ?>
    <?php }else{ ?>
      <div class="clearfix" style="text-align: center">无此合作信息！</div>
    <?php }?>
</div>
