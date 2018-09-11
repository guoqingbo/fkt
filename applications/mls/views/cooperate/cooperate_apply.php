<body>
    <form name="search_form" id='search_form' action ="<?php echo MLS_URL;?>/cooperate/add_cooperation_info" method="post">
    <div class="pop_box_g pop_box_cooperation" id="js_pop_box_cooperation" style="display:block; border:0;">
        <div class="hd">
            <div class="title">合作申请详情</div>
        </div>
        <div class="cooperation_detailed" style="height:450px;">
            <p class="c_title"></p>
            <div class="clearfix cooperation_num_b">
                <div class="left item">
                    <div class="bz">
                        <img class="img01" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01f_03-06.png">
                        <p>合作申请</p>
                    </div>
                </div>
                <div class="jt"><img class="img01" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01j_15.png"></div>
                <div class="left item item_none">
                    <div class="bz">
                        <img class="img01" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01h_09.png">
                        <p>确认合作</p>
                    </div>
                </div>
                <div class="jt"><img class="img01" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01j_15.png"></div>
                <div class="left item item_none">
                    <div class="bz">
                        <img class="img01"  src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/01y_12.png">
                        <p>等待分佣</p>
                    </div>
                </div>
            </div>
            <p class="c_title b_t">合作房源信息</p>
            <div class="cooperation_house_d clearfix">
                <?php if(is_array($cooperate_info['houseinfo']) && !empty($cooperate_info['houseinfo'])) { ?>
				<div class="clearfix">
					<div class="h_pic my_h_pic">
						<img src="<?php echo $cooperate_info['houseinfo']['photo'] != '' ? $cooperate_info['houseinfo']['photo'] :MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg';?>" width="110px" height="80px">
					</div>
					<div class="h_text my_h_text">
						<p><span class="phone"><?php echo !empty($cooperate_info['houseinfo']['title']) ? $cooperate_info['houseinfo']['title'] : '';?></span></p>
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
						   <strong class="num"><?php echo ('1'==$cooperate_info['houseinfo']['price_danwei'])?$cooperate_info['houseinfo']['price']/$cooperate_info['houseinfo']['buildarea']/30:strip_end_0($cooperate_info['houseinfo']['price']);?></strong>&nbsp;
						   <?php if($cooperate_info['houseinfo']['tbl'] == 'sell'){?>
						   万
                           <?php }else {
                               echo ('1'==$cooperate_info['houseinfo']['price_danwei'])?'元/㎡*天':'元/月';
                           }
                           ?>
						</p>
						<p><?php echo strip_end_0($cooperate_info['houseinfo']['buildarea']);?>&nbsp;平米</p>
					</div>
				</div>
                <div class="trade-wrap">
                    <?php if('1'==$cooperate_info['houseinfo']['reward_type']){ ?>
                        <table class="outer-table-02">
                            <tr class="tr-one">
                                <td width="120" align="center"><strong>房源合作佣金分配</strong></td>
                                <td>甲方可获得本次交易双方佣金总金额&nbsp;<strong class="f60 f14"><?=$cooperate_info['houseinfo']['commission_ratio_arr']['house']?></strong><em>|</em>乙方可获得本次交易双方佣金总金额&nbsp;<strong class="f60 f14"><?=$cooperate_info['houseinfo']['commission_ratio_arr']['customer']?></strong>
                                <p class="b8b7b7">注：此佣金分配方案仅做参考，具体佣金方案需经纪人线下商定</p></td>
                            </tr>
                        </table>
                    <?php }else if('2'==$cooperate_info['houseinfo']['reward_type']){ ?>
                        <table class="outer-table-02 outer-table-b02">
                            <tr>
                                <td width="120" align="center"><strong>房源合作悬赏赏金</strong></td>
                                <td><strong class="f60 f14">¥<?php echo $cooperate_info['houseinfo']['cooperate_reward'];?> 元</strong>
                                <p class="b8b7b7">注：在房源成交后需给予合作方佣金以外的额外奖励，具体以经纪人双方线下商定为准</p></td>
                            </tr>
                        </table>
                    <?php }?>
                 </div>
                <?php } ?>
             </div>

            <div class="clearfix cooperation_p_d">
                <div class="item">
                    <h4 class="h4">甲方经纪人信息</h4>
                     <div class="m clearfix">
                    <?php if(is_array($house_broker_info) && !empty($house_broker_info)) { ?>
                    <div class="pic">
                        <img src="<?php echo !empty($house_broker_info['photo']) ? $house_broker_info['photo'] : MLS_SOURCE_URL.'/mls/images/v1.0/defaultface.jpg';?>" width="105" height="140" >
                    </div>
                    <div class="info">
                        <p class="tex"><span>姓名：<?php echo $house_broker_info['truename'];?></span></p>
                         <div class="tex clearfix">
                             <span class="left">等级：</span>
                             <div class="d_box">
                                <?php echo $trust_info_house['level'];?>
                             </div>
                             <span class="left ml20">&nbsp;&nbsp;好评度：<?php echo strip_end_0($house_broker_info['good_rate']);?>%</span>
                         </div>
                         <p class="tex">
                        <span class="s">信息真实度：<strong class="n"><?php echo $appraise_avg_info_house['infomation']['score'];?></strong></span>
                        <span class="s">态度满意度：<strong class="n"><?php echo $appraise_avg_info_house['attitude']['score'];?></strong></span>
                        <span class="s">业务专业度：<strong class="n"><?php echo $appraise_avg_info_house['business']['score'];?></strong></span>
                         </p>
                        <p class="tex">门店：<?php echo $house_broker_info['agency_name'];?><?php echo '('.$house_company_name.')';?></p>
                        <?php if(2==$apply_type){?>
                            <p class="tex">联系方式：<?php echo $house_broker_info['phone'];?></p>
                        <?php }else{?>
                            <p class="tex">联系方式：<span style="color:red;">提交申请后方能查看</span></p>
                        <?php }?>
                        <!--<a href="javascript:void(0)" class="im"><span class="iconfont">&#xe616;</span>在线联系</a>-->
                    </div>
                    <?php }else{?>
                        <div class="info">暂无甲方经纪人信息，无法合作</div>
                    <?php }?>
                    </div>
                </div>
                <div class="item item_r">
                    <h4 class="h4">乙方经纪人信息</h4>
                    <div class="m clearfix">
                        <?php if(is_array($customer_broker_info) && !empty($customer_broker_info)) { ?>
                        <div class="pic">
                            <img src="<?php echo !empty($customer_broker_info['photo']) ? $customer_broker_info['photo'] :  MLS_SOURCE_URL.'/mls/images/v1.0/defaultface.jpg';?>" width="105" height="140" >
                        </div>
                        <div class="info">
                            <p class="tex"><span>姓名：<?php echo $customer_broker_info['truename'];?></span></p>
                             <div class="tex clearfix">
                                 <span class="left">等级：</span>
                                 <div class="d_box">
                                   <?php echo $trust_info_customer['level'];?>
                                 </div>
                                 <span class="left ml20">&nbsp;&nbsp;好评度：<?php echo strip_end_0($customer_broker_info['good_rate']);?>%</span>
                             </div>
                             <p class="tex">
                            <span class="s">信息真实度：<strong class="n"><?php echo $appraise_avg_info_customer['infomation']['score'];?></strong></span>
                            <span class="s">态度满意度：<strong class="n"><?php echo $appraise_avg_info_customer['attitude']['score'];?></strong></span>
                            <span class="s">业务专业度：<strong class="n"><?php echo $appraise_avg_info_customer['business']['score'];?></strong></span>
                             </p>
                            <p class="tex">门店：<?php echo $customer_broker_info['agency_name'];?><?php echo '('.$customer_company_name.')';?></p>

                            <?php if(2==$apply_type){?>
                                <p class="tex">联系方式：<span style="color:red;">提交申请后方能查看</span></p>
                            <?php }else{?>
                                <p class="tex">联系方式：<?php echo $customer_broker_info['phone'];?></p>
                            <?php }?>
                            <!--<a href="javascript:void(0)" class="im"><span class="iconfont">&#xe616;</span>在线联系</a>-->
                        </div>
                        <?php }else{?>
                            <div class="info">暂无已方经纪人信息，无法合作</div>
                        <?php }?>
                    </div>
                </div>

            </div>
            <div class="checkbox_x">
                <label><input type="checkbox" id='agreement' checked name='agreement'>我已阅读并同意</label>
                <a href="javascript:void(0)" onclick="openWin('js_pop_protocol')">《合作协议》</a>
            </div>
        </div>
		<div class="btn_box">
			<button type="button" onclick="apply_cooperation()" class="grey_btn grey_btn3">提交合作申请</button>
			<!--<button type="button" class="grey_btn JS_Close" onclick="window.parent.closePopFun('js_pop_box_cooperation_customer');">关闭</button>-->
		</div>
    </div>
    <input type="hidden" name="tbl" id='tbl' value='<?php echo $tbl;?>'>
    <input type="hidden" name="rowid" id='rowid'  value='<?php echo $cooperate_info['houseinfo']['rowid'];?>'>
    <input type="hidden" name="customer_id" id='customer_id' value='<?php echo $customer_id;?>'>
    <input type="hidden" name="agentid_a" id='agentid_a' value='<?php echo $cooperate_info['brokerinfo_a']['agency_id'];?>'>
    <input type="hidden" name="brokerid_a" id='brokerid_a' value='<?php echo $cooperate_info['brokerinfo_a']['broker_id'];?>'>
    <input type="hidden" name="broker_name_a" id='broker_name_a' value='<?php echo $cooperate_info['brokerinfo_a']['truename'];?>'>
    <input type="hidden" name="phone_a" id='phone_a' value='<?php echo $cooperate_info['brokerinfo_a']['phone'];?>'>
    <input type="hidden" name="agentid_b" id='agentid_b' value='<?php echo $cooperate_info['brokerinfo_b']['agency_id'];?>'>
    <input type="hidden" name="brokerid_b" id='brokerid_b'  value='<?php echo $cooperate_info['brokerinfo_b']['broker_id'];?>'>
    <input type="hidden" name="phone_b" id='phone_b' value='<?php echo $cooperate_info['brokerinfo_b']['phone'];?>'>
    <input type="hidden" name="broker_name_b" id='broker_name_b' value='<?php echo $cooperate_info['brokerinfo_b']['truename'];?>'>
    <input type="hidden" name="house" id='house' value='<?php echo serialize($cooperate_info['houseinfo']);?>'>
    <input type="hidden" name="block_name" id ="block_name" value='<?php echo $cooperate_info['houseinfo']['blockname'];?>'>
    <input type="hidden" name="broker_a" id='broker_a' value='<?php echo serialize($cooperate_info['brokerinfo_a']);?>'>
    <input type="hidden" name="broker_b" id='broker_b' value='<?php echo serialize($cooperate_info['brokerinfo_b']);?>'>
    <input type="hidden" name="apply_type" id='apply_type' value='<?php echo $apply_type;?>'>
    <input type="hidden" name="secret_key" id='secret_key' value='<?php echo $secret_key;?>'>
</form>

<!--载入合作协议页面-->
<?php $this->view('cooperate/cooperative_agreements');?>

<!--页面处理中弹层-->
<div style="display:none; text-align: center;" id ='docation_loading'>
    <img src ="<?php echo MLS_SOURCE_URL; ?>/common/images/loading_6.gif">
    <p style="font-size: 16px; font-family:'微软雅黑'; line-height: 30px; color: #fff;">正在处理</p>
</div>
<!--载入公共弹框页面-->
<?php $this->view('cooperate/cooperate_common_dialog_box');?>
<!--载入合作协议页面-->
<?php $this->view('cooperate/cooperative_agreements');?>

<script>
//申请合作
function apply_cooperation()
{
    if(!$('#agreement').attr('checked'))
    {
        var msg = '需要勾选我已阅读并同意《合作协议》';
        $("#dialog_do_warnig_tip").html(msg);
        openWin('js_pop_do_warning');
        return false;
    }

    var tbl = $('#tbl').val();
    var rowid = $('#rowid').val();
    var customer_id = $('#customer_id').val();
    var agentid_a = $('#agentid_a').val();
    var brokerid_a = $('#brokerid_a').val();
    var broker_name_a = $('#broker_name_a').val();
    var phone_a = $('#phone_a').val();
    var agentid_b = $('#agentid_b').val();
    var brokerid_b = $('#brokerid_b').val();
    var broker_name_b = $('#broker_name_b').val();
    var phone_b = $('#phone_b').val();
    var house = $('#house').val();
    var broker_a = $('#broker_a').val();
    var broker_b = $('#broker_b').val();
    var apply_type = $('#apply_type').val();
    var secret_key = $('#secret_key').val();
    var block_name = $('#block_name').val();

    $.ajax({
        url: "<?php echo MLS_URL;?>/cooperate/add_cooperation_info/",
        data:{'tbl':tbl,'rowid':rowid,'customer_id':customer_id,'agentid_a':agentid_a,
              'brokerid_a':brokerid_a,'broker_name_a':broker_name_a,'phone_a':phone_a,
              'agentid_b':agentid_b,'brokerid_b':brokerid_b,'broker_name_b':broker_name_b,
              'phone_b':phone_b,'house':house,'block_name':block_name,'broker_a':broker_a,
              'broker_b':broker_b,'apply_type':apply_type,'secret_key':secret_key},
        type: "POST",
        dataType:"JSON",
        success:function (data)
        {
            if(data['is_ok'] == 1)
            {
                showParentDialog('dialog_do_itp' ,'js_pop_do_success',data['msg']);
            }
            else if(data['is_ok'] == 0)
            {
                showParentDialog('dialog_do_warnig_tip' , 'js_pop_do_warning' , data['msg']);
            }
        },
        error:function(er)
        {
            var error_msg = '异常错误';
            showParentDialog('dialog_do_warnig_tip' , 'js_pop_do_warning' , error_msg);
        }
    });

    //关闭父窗口
    window.parent.closePopFun('js_pop_box_cooperation_customer');
}
</script>
