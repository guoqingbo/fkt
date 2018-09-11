<body>
<div class="pop_box_g evaluate_pop_box pop_box_g_border_none" id="js_woyaopingjia" style="display: block;"> 
    <div class="hd">
        <div class="title"><?php echo $page_title;?></div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
 <div class="cooperation_detailed evaluate_detailed">
        <div class="s_house">
            <p>合同编号：<?php echo $order_sn;?></p>
            <p>房源详情：<?php echo $house['districtname'];?>-<?php echo $house['streetname'];?>  <?php echo $house['blockname'];?> 
            <?php echo $house['room'];?>室<?php echo $house['hall'];?>厅<?php echo $house['toilet'];?>卫   
            <?php echo $house['fitment_str'];?>  
            <?php echo $house['forward_str'];?>  
            <?php echo $house['buildarea'];?> ㎡   
            <?php echo strip_end_0($house['price']);?>
            <?php echo $house['tbl'] == 'sell' ? '万' : '元/月'; ?>
            </p>
            <p>交易状态：
                <span class="<?php echo $esta == 7 ? 's' : 'w';?>"><?php echo $esta_description;?></span>
            </p>
        </div>
        <div class="p_detailed">
            <div class="clearfix" style="padding-bottom:10px;">
                <strong class="name"><?php echo $partner_info['truename'];?></strong>
                <div class="d_box"><?php echo $partner_level['level'];?></div>
                <span class="pericon per1"></span> <span class="pericon per0"></span>
                <span class="tel left" style="line-height:24px;"><?php echo $partner_info['phone'];?></span>
                <span class="s_name left" style="line-height:24px;"><?php echo $partner_info['agency_name'];?></span>
              
            </div>
          
            <div class="clearfix">
                <p class="d_t">信息真实度</p>
                <div class="d_s_box"><?php echo $partner_appraise_avg['infomation']['level'];?></div>
                <p class="d_f">得分<strong class="c"><?php echo $partner_appraise_avg['infomation']['score'];?></strong>分</p>
                <div class="d_p_f"> <span class="d_tex">比平均值</span>
                    <?php
                     $compare_str = '';
                     $compare_css = '';
                     if ($partner_appraise_avg['infomation']['rate'] >= 0) { 
                    	$compare_str = '高';
                    	$compare_css = '';
                     } 
                     else
                   {
                       $compare_str = '低';
                       $compare_css = 'n_tex02';
                     } 
                    ?>
                    <p class="n_tex <?php echo $compare_css;?>"> 
                        <strong class="z"><?php echo $compare_str;?></strong>
                        <span class="num"><?php echo abs($partner_appraise_avg['infomation']['rate']);?>%</span> 
                    </p>
                </div>
            </div>
            <div class="clearfix">
                <p class="d_t">态度满意度</p>
                <div class="d_s_box"><?php echo $partner_appraise_avg['attitude']['level'];?></div>
                <p class="d_f">得分<strong class="c"><?php echo $partner_appraise_avg['attitude']['score'];?></strong>分</p>
                <div class="d_p_f"> <span class="d_tex">比平均值</span>
                     <?php
                     $compare_str = '';
                     $compare_css = '';
                     if ($partner_appraise_avg['attitude']['rate'] >= 0) { 
                    	$compare_str = '高';
                    	$compare_css = '';
                     } 
                     else
                   {
                       $compare_str = '低';
                       $compare_css = 'n_tex02';
                     } 
                    ?>
                    <p class="n_tex <?php echo $compare_css;?>"> 
                        <strong class="z"><?php echo $compare_str;?></strong>
                        <span class="num"><?php echo abs($partner_appraise_avg['attitude']['rate']);?>%</span>
                    </p>
                </div>
            </div>
            <div class="clearfix">
                <p class="d_t">业务专业度</p>
                <div class="d_s_box"><?php echo $partner_appraise_avg['business']['level'];?></div>
                <p class="d_f">得分<strong class="c"><?php echo $partner_appraise_avg['business']['score'];?></strong>分</p>
                <div class="d_p_f"> <span class="d_tex">比平均值</span>
                     <?php
                     $compare_str = '';
                     $compare_css = '';
                     if ($partner_appraise_avg['business']['rate'] >= 0) { 
                    	$compare_str = '高';
                    	$compare_css = '';
                     } 
                     else
                   {
                       $compare_str = '低';
                       $compare_css = 'n_tex02';
                     } 
                    ?>
                    <p class="n_tex <?php echo $compare_css;?>">
                     <strong class="z"><?php echo $compare_str;?></strong>
                     <span class="num"><?php echo abs($partner_appraise_avg['business']['rate']);?>%</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="fun_s_box clearfix">
            <h3 class="h3">我来评一评</h3>
            <p class="text">为了打造公平公正的合作平台，请您如实评价，评价之后可获得相应的积分奖励<!-- <span class="iconfont">&#xe614;</span> --></p>
            <div class="p_box forms">
                <div class="p_l js_fields"> <span class="span">整体评价：</span>
                   
                        <!-- <label class="label"><input type="radio" name="trust_type" value="good">
                        好评</label>
                        -->
                        <i class="label">
							<input type="radio" class="input_radio" name="trust_type" value="good">  好评
						</i>
                  
                        <!-- <label class="label"><input type="radio" name="trust_type" value="medium">
                        中评</label>-->
                        <i class="label">
							<input type="radio" class="input_radio" name="trust_type" value="medium">中评
						</i>
                   
                       <!--  <label class="label"><input type="radio" name="trust_type" value="bad">
                        </label>-->
                        <i class="label">
							<input type="radio" class="input_radio" name="trust_type" value="bad">  差评
						</i>
                </div>
                <div class="d_f_box clearfix">
                    <p class="t">细节评价：</p>
                    <div class="df">
                        <div class="item">
                            <p class="df_text">信息真实度</p>
                            <div class="rating-level" id="stars1">
                            	<span class="star0" star:value="1">1</span>
                                <span class="stars1" star:value="2">2</span> 
                                <span class="stars2" star:value="3">3</span> 
                                <span class="stars3" star:value="4">4</span> 
                                <span class="stars4" star:value="5">5</span> 
                             </div>
                            <div class="df_tipsDiv" id="stars1-tips"> </div>
                            <input type="hidden" id="stars1-input" name="infomation" value="" size="2" />
                        </div>
                        <div class="item">
                            <p class="df_text">合作满意度</p>
                            <div class="rating-level" id="stars2">
                            	<span class="star0" star:value="1">1</span>
                                <span class="stars1" star:value="2">2</span> 
                                <span class="stars2" star:value="3">3</span> 
                                <span class="stars3" star:value="4">4</span> 
                                <span class="stars4" star:value="5">5</span> 
                            </div>
                            <div class="df_tipsDiv" id="stars2-tips"> </div>
                            <input type="hidden" id="stars2-input" name="attritude" value="" size="2" />
                        </div>
                        <div class="item">
                            <p class="df_text">业务专业度</p>
                            <div class="rating-level" id="stars3">
                            	<span class="star0" star:value="1">1</span>
                                <span class="stars1" star:value="2">2</span> 
                                <span class="stars2" star:value="3">3</span> 
                                <span class="stars3" star:value="4">4</span> 
                                <span class="stars4" star:value="5">5</span> 
                            </div>
                            <div class="df_tipsDiv" id="stars3-tips"> </div>
                            <input type="hidden" id="stars3-input" name="business" value="" size="2" />
                        </div>
                    </div>
                </div>
                <div class="d_f_box clearfix">
                    <p class="t"><br>评价内容：</p>
                    <div class="df">
                    	<textarea class="d_f_textarea" id="content" name="content"></textarea>
                        <div class="s_tex_num clear" id="content_tips">最少<strong id="js_num">5</strong>个字</div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
	<style>
		.cooperation_detailed{ height:450px;}
	</style>
	<div class="btn_box">
		<input type="hidden" name="type" value="<?php echo $type;?>" id="type">
		<input type="hidden" name="c_id" value="<?php echo $c_id;?>" id="c_id">
		<button type="button" class="btn-lv1 btn-mid" id="appraise_submit">提交</button>
	</div>
</div>
<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="dialog_do_success_tip"></p>
                <button type="button" class="btn-lv1 btn-mid" onclick="fun_c_pop()">确定</button>
            </div>
        </div>
    </div>
</div>
<div id="js_pop_do_warning"	class="pop_box_g pop_see_inform pop_no_q_up">
    		<div class="hd">
    			<div class="title">提示</div>
    			<div class="close_pop">
    				<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
    			</div>
    		</div>
    		<div class="mod">
    			<div class="inform_inner">
    				<div class="up_inner">
    					<p class="text" id="dialog_do_warnig_tip"></p>
    				</div>
    			</div>
    		</div>
    	</div>
<script type="text/javascript">
     $(function() {
         //评价内容的倒计数
         $('#content').bind('keyup', function() {
             var content = $.trim($("#content").val());
             var text_num = content.length;
             if (text_num <= 5)
             {
                 $('#content_tips').html('还剩下<strong id="js_num">' + 
                         (5 - text_num) + '</strong>个字');
             }
             else if (text_num > 5)
             {
                 $('#content_tips').html('');
             }
         });
         $('#appraise_submit').bind('click', function() {
        	 var trust_type = $("input[name='trust_type']:checked").val();
        	 if (typeof trust_type == 'undefined')
        	 {
        		 openWin('js_pop_do_warning');
        		 $('#dialog_do_warnig_tip').html('请选择整体评价');
            	 return false;
             }
             var infomation = $('#stars1-input').val();
        	 if (infomation == '')
        	 {
        		 openWin('js_pop_do_warning');
        		 $('#dialog_do_warnig_tip').html('请评价信息真实度');
            	 return false;
             }
             var attritude = $('#stars2-input').val();
        	 if (attritude == '')
        	 {
        		 openWin('js_pop_do_warning');
        		 $('#dialog_do_warnig_tip').html('请评价合作满意度');
            	 return false;
             }
             var business = $('#stars3-input').val();
        	 if (business == '')
        	 {
        		 openWin('js_pop_do_warning');
        		 $('#dialog_do_warnig_tip').html('请评价业务专业度');
            	 return false;
             }
             var content = $('#content').val();
        	 if (content == '' || content.length<5)
        	 {
        		 openWin('js_pop_do_warning');
        		 $('#dialog_do_warnig_tip').html('请填写评价内容最少5个字');
            	 return false;
             }
         	 $.ajax({
                 type: 'POST',
                 url : '/cooperate/my_appraise_submit/',
                 data: {'type' : $('#type').val(), 'c_id' : $('#c_id').val(),
                        'trust_type' : trust_type, 'infomation' : infomation,
                        'attritude' : attritude, 'business' : business,
                        'content' : content
                 },
                 dataType:'json',
                 success: function(data){
        			if(data.errorCode == '401')
        			{
        				login_out();
        			} else if (data.errorCode == '403') {
        				permission_none();
            	    } else if (data.result == 0) {
                  		openWin('js_pop_do_warning');
                		$('#dialog_do_warnig_tip').html(data.reason);
     			    } else if(data.result == 1) {
    			    	$('#dialog_do_success_tip').html(data.reason);
      					openWin('js_pop_do_success');
       					//setTimeout(function() {window.parent.location.reload();}, 500);
         			}
                 }
             });
         });
     });

function fun_c_pop(){
    var _url = window.parent.location.href;
    $(window.parent.document).find("#js_pop_box_appraise").hide();
    $(window.parent.document).find("#js_GTipsCoverWxr").remove();
    window.parent.location.href = _url;
}
     
</script>