
<div class="contract-wrap clearfix">
<div class="tab-left"><?=$user_tree_menu?></div>
<div class="forms_scroll h90">
    <div class="contract_top_main">
        <div class="i_box">
        	<div class="clearfix">
		    <h4 class="h4">合同信息</h4>
		    <a class="btn_l" href="javascript:void(0)" id="return_last" onclick="location.href='/contract/contract_list/2';return false;">&lt;&lt;返回</a>
		    <?php if($contract['is_check'] != 2){?><a class="btn_l" href="javascript:void(0);" <?php if($auth['edit']['auth']){?>onclick="location.href='/contract/modify_contract/2/<?=$contract['id'];?>';return false;"<?php }else{?>onclick="permission_none();"<?php }?>>编辑</a><?php }?>
		</div>
		<div class="t_item clearfix contract_mess">
		    <p class="item w235"><span class="tex">合同编号：</span><a href="javascript:void(0)"><?=$contract['number'];?></a></p>
		    <p class="item w260"><span class="tex">房源编号：</span><a href="javascript:void(0)" onclick="$('#js_pop_box_g .iframePop').attr('src','/rent/details_house/<?=substr($contract['house_id'],2);?>/4');openWin('js_pop_box_g');"><?=$contract['house_id'];?></a></p>
		    <p class="item w267"><span class="tex">楼盘名称：</span><?=$contract['block_name'];?></p>
		    <p class="item wauto"><span class="tex">房源面积：</span><b style="font-weight:bold;"><?=strip_end_0($contract['buildarea']);?></b>m²</p>
		</div>
		<div class="t_item clearfix contract_mess">
		    <p class="item w500"><span class="tex">房源地址：</span><a href="javascript:void(0)"><?=$contract['house_addr'];?></a></p>
		    <p class="item w267"><span class="tex">物业类型：</span><?=$base_config['sell_type'][$contract['sell_type']];?></p>
		    <p class="item float:left;"><span class="tex">签约日期：</span><?=date('Y-m-d',$contract['signing_time']);?></p>
		</div>
		<div class="t_item clearfix contract_mess">
		    <p class="item w235"><span class="tex">交易方式：</span><?=$contract['type']==1?'出售':'出租';?></p>
		    <p class="item w260"><span class="tex">成交金额：</span><strong><?=strip_end_0($contract['price']);?><?=$config['price_type'][$contract['price_type']]?></strong></p>
		    <p class="item w267"><span class="tex">合作房源：</span><?=$contract['is_cooperate']==1?'是':'否';?></p>
            <?php if ($contract['is_cooperate']==1) { ?>
		    <p class="item wauto"><span class="tex">合作编号：</span><?=$contract['order_sn'];?></p>
            <?php } ?>
		</div>
        </div>

        <!--卖方信息-->
        <dl class="sale_message">
            <dd>
               <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws_lease_03.png" />
                <p>房东信息</p>
            </dd>
            <dt>
                <div>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">房东姓名：</span><b><?=$contract['owner']?></b></p>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">联系方式：</span><b><?=$contract['owner_tel']?></b></p>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">身份证号：</span><b><?=$contract['owner_idcard']?></b></p>
                </div>
                <div>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">签约门店：</span><b><?=$contract['agency_name_a']?></b></p>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">签约人：</span><b><?=$contract['broker_name_a']?></b></p>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">联系方式：</span><b><?=$contract['broker_tel_a']?></b></p>
                </div>

            </dt>
        </dl>
        <!--买方信息-->
        <dl class="sale_message">
            <dd>
               <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws_rent_06.png" />
                <p>租客信息</p>
            </dd>
            <dt>
                <div>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">客源编号：</span><b><?=$contract['customer_id']?></b></p>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">租客姓名：</span><b><?=$contract['customer']?></b></p>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">联系方式：</span><b><?=$contract['customer_tel']?></b></p>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">身份证号：</span><b><?=$contract['customer_idcard']?></b></p>
                </div>
                <div>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">签约门店：</span><b><?=$contract['agency_name_b']?></b></p>
                      <p><span class="input_add_F" style="width:80px;text-align:right;">签约人：</span><b><?=$contract['broker_name_b']?></b></p>
                      <p><span class="input_add_F" style="width:80px;text-align:right;" >联系方式：</span><b><?=$contract['broker_tel_b']?></b></p>
                </div>

            </dt>
        </dl>
        <span class="sale_message_h"></span>
        <!--佣金结算-->
        <div class="sale_message_commission">
            <div style="width:100%;clear:both;display:block;">
                <h4 class="h4 padding_size zws_h4_font">佣金结算</h4>
           </div>
           <div class="sale_message_commission_detial" style="display:block;width:100%;float:left;">
                <div style="display:inline;width:100%;float:left;">
                    <p class="aad_pop_p_B10">
                      <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>起租时间：</b><strong><?=$contract['start_time'];?></strong>
                    </p>
                    <p class="aad_pop_p_B10">
                      <b  class="zws_ip_W100"><em class="resut_table_state_1">*</em>到期时间：</b><strong><?=$contract['end_time'];?></strong>
                    </p>
                    <p class="aad_pop_p_B10">
                      <b  class="zws_ip_W100"><em class="resut_table_state_1">*</em>付款方式：</b><strong><?=$config['buy_type_r'][$contract['buy_type_r']];?></strong>
                    </p>
                    <p class="aad_pop_p_B10" id="zws_deposit">
                      <b  class="zws_ip_W100"><em class="resut_table_state_1">*</em>押金：</b>
                      <strong class="zws_strong_w"><?=strip_end_0($contract['deposit']);?></strong>
                    </p>
                </div>
                <div style="display:inline;width:100%;float:left;">
                    <p class="zws_goods_W1 aad_pop_p_B10">
                      <b class=" zws_ip_W100"><em class="resut_table_state_1">*</em>水电抄表：</b>
                      <strong><?=$contract['hydropower'];?></strong>
                    </p>
                    <p class="zws_goods_W1 aad_pop_p_B10">
                      <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>物品清单：</b>
                      <strong><?=$contract['list_items'];?></strong>
                       <span  class="zws_block errorBox"></span>
                    </p>
                </div>
                <div style="display:inline;width:100%;float:left;">
                      <p class="aad_pop_p_B10">
                        <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>房东应付佣金：</b>
                        <strong class="zws_strong_w"><?=strip_end_0($contract['owner_commission']);?>&nbsp;元</strong>
                      </p>
                      <p class="aad_pop_p_B10">
                        <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>租客应付佣金：</b>
                        <strong class="zws_strong_w"><?=strip_end_0($contract['customer_commission']);?>&nbsp;元</strong>
                      </p>
                      <p class="aad_pop_p_B10">
                        <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>其他收入：</b>
                        <strong class="zws_strong_w"><?=strip_end_0($contract['other_income']);?>&nbsp;元</strong>
                        <span  class="zws_block errorBox"></span>
                      </p>
                      <p class="aad_pop_p_B10">
                        <b class="zws_ip_W100"><em class="resut_table_state_1">*</em>佣金总计收入：</b>
                        <strong  class="zws_strong_w"><?=strip_end_0($contract['commission_total']);?>&nbsp;元</strong>
                      </p>

                </div>
                <dl>
                      <dd>合同备注：</dd>
                      <dt><?=$contract['remarks'];?></dt>
                </dl>
                <?php if(in_array($contract['is_check'],array(2,3))){?>
                <dl>
                      <dd>审核意见：</dd>
                      <dt class="colr_rest"><b>【<?=$contract['check_agency'];?>-<?=$contract['check_broker'];?>】</b><?=$contract['check_remark'];?></dt>
                </dl>
                <?php }?>

           </div>

        </div>
        <!--合同细节-->
        <div id="js_search_box" class="shop_tab_title  scr_clear top_Marign" style="float:left;display:inline;width:99%;padding-right:1%;">
            <a href="javascript:void(0);" class="link contract_filing link_on" id="contract_divide" data="/contract/contract_divide_manage/<?=$contract['id'];?>">业绩分成</a>
            <a href="javascript:void(0);" class="link contract_filing" id="should_flow" data="/contract/contract_should_manage/<?=$contract['id'];?>">应收应付</a>
            <a href="javascript:void(0);" class="link contract_filing" id="actual_flow" data="/contract/contract_actual_manage/<?=$contract['id'];?>">实收实付</a>
            <a href="javascript:void(0);" class="link contract_filing" id="contract_follow" data="/contract/contract_follow_manage/<?=$contract['id'];?>">跟进明细</a>
            <?php if ($contract['is_commission'] == 0) { ?><a href="javascript:void(0)" class="btn-lv fr"
                                                              id="contract_divide1"
                                                              style="" <?php if ($auth['divide_add']['auth']){ ?>onclick="$('#divide').attr('src','/contract/contract_divide_modify/<?= $contract['id']; ?>');openWin('js_divide_pop');"
                                                              <?php }else{ ?>onclick="permission_none();"<?php } ?>>
                    <span style="margin-right:16px;">+ 添加业绩分成</span></a><?php } ?>
            <?php if($should_num >0){?><a href="javascript:void(0)" class="btn-lv fr" id="actual_flow1" style="display:none" <?php if($auth['actual_add']['auth']){?>onclick="$('#actual').attr('src','/contract/contract_actual_modify/<?=$contract['id'];?>');openWin('js_actual_pop');"<?php }else{?>onclick="permission_none();"<?php }?>><span style="margin-right:16px;">+ 添加实收实付</span></a></div><?php }?>
            <a href="javascript:void(0)" class="btn-lv fr" id="should_flow1" style="display:none" <?php if($auth['should_add']['auth']){?>onclick="$('#should').attr('src','/contract/contract_should_modify/<?=$contract['id'];?>');openWin('js_should_pop');"<?php }else{?>onclick="permission_none();"<?php }?>><span style="margin-right:16px;">+ 添加应收应付</span></a>


            <input type="hidden" id="stage_id">
            <input type="hidden" id="flow_id">
            <input type="hidden" id="divide_id">
            <input type="hidden" id="contract_id" value='<?=$contract['id'];?>'>
            <input type="hidden" id="percent_total" value="<?=$divide_total['percent_total'];?>">
        </div>
    <?php if($auth['actual_add']['auth']==1){?>
    <script>
        function show_actual_add(){
            var html='<a href="javascript:void(0)" class="btn-lv fr" id="actual_flow1" style="display:none" onclick="open_actual_add(<?=$contract['id']?>)"><span style="margin-right:16px;">+ 添加实收实付</span></a>';
            $("#js_search_box").append(html);
        }
    </script>
    <?php }else{?>
    <script>
        function show_actual_add(){
            var html='<a href="javascript:void(0)" class="btn-lv fr" id="actual_flow1" style="display:none" onclick="permission_none();"><span style="margin-right:16px;">+ 添加实收实付</span></a>';
            $("#js_search_box").append(html);
        }
    </script>
    <?php }?>
        <!--嵌入模块弹框-->
        <div id="js_mukuai_box" class="iframePopBox" style="width: 99%;border:none; box-shadow:none;display:block;padding:1%;">
            <iframe frameborder="0" scrolling="no" width="100%" height="100%" src="/contract/contract_divide_manage/<?=$contract['id'];?>" id="iframepage" name="iframepage"></iframe>
        </div>
    </div>
    </div>
</div>
<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt1"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>

<!--操作失败弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_false">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                            <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>
<!--删除-->
<div id="js_del_pop" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要删除此实收实付吗？<br/>确认删除后不可恢复。</p>
		<button type="button" class="btn JS_Close" onclick="delete_actual_this();">确定</button>
		<button type="button" class="btn btn_none JS_Close">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--删除-->
<div id="js_del_pop1" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要删除此应收应付吗？<br/>确认删除后不可恢复。</p>
		<button type="button" class="btn JS_Close" onclick="delete_should_this();">确定</button>
		<button type="button" class="btn btn_none JS_Close">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--删除-->
<div id="js_del_divide" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;您确定要删除此业绩分成吗？<br/>确认删除后不可恢复。</p>
		<button type="button" class="btn JS_Close" onclick="delete_divide();">确定</button>
		<button type="button" class="btn btn_none JS_Close">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--结佣-->
<div id="js_commission_pop" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;分成结佣后，将不可对分成信息修改。是否确认操作？</p>
		<button type="button" class="btn JS_Close" onclick="complete_commission();">确定</button>
		<button type="button" class="btn btn_none JS_Close">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--确认收付-->
<div id="js_sure_flow_pop" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;是否确认当前收付已收付？</p>
		<button type="button" class="btn JS_Close" onclick="sure_flow();">确定</button>
		<button type="button" class="btn btn_none JS_Close">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--添加应收应付弹框-->
<div id="js_should_pop" class="iframePopBox" style="width: 582px;height:313px;border:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="582px" height="313px" class='iframePop' src="" id="should"></iframe>
</div>

<!--添加实收实付弹框-->
<div id="js_actual_pop" class="iframePopBox" style="width: 582px;height:413px;border:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="582px" height="413px" class='iframePop' src="" id="actual"></iframe>
</div>

<!--添加业绩分成弹框-->
<div id="js_divide_pop" class="iframePopBox" style="width: 502px;height:451px;border:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="502px" height="451px" class='iframePop' src="" id="divide"></iframe>
</div>

<!--权证步骤详情弹框-->
<div id="js_warrant_pop" class="iframePopBox" style="width: 400px;height:250px;border:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="400px" height="300px" class='iframePop' src="" id="warrant"></iframe>
</div>

<!--新建模版弹框-->
<div id="js_edit_template_pop" class="iframePopBox" style="width: 842px;height:504px;border:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="842" height="502" class='iframePop' src=""></iframe>
</div>
<!--房源详情弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js"></script>

<script>
$(function () {
    function re_width(){
      var h1 = $(window).height();
      var w1 = $(window).width() - 180;
      $(".tab-left, .forms_scroll").height(h1-65);
      $(".forms_scroll").width(w1).show();
    };
    re_width();
    $(window).resize(function(e) {
	re_width();
	$(".sale_message dt").css("width",($(".sale_message").width()-100-36)+"px");
	$(".qz_precess_add_modle p").css("padding-left",($(".qz_precess_add_modle").width()-450)/2+"px");
    });

	$(".qz_precess_add_modle p").css("padding-left",($(".qz_precess_add_modle").width()-450)/2+"px");
	$(".sale_message dt").css("width",($(".sale_message").width()-100-36)+"px");
     //items   table   隔行换色
    $("tbody tr:odd").css("background","#f7f7f7");
    $("tbody tr:even").css("background","#fcfcfc");
    $("#should_flow").find("tr").css("background","none");
    $("#add_actual").find("tr").css("background","none");
    $(".add_pop_messages").find("tr").css("background","none");

    $("input[name='is_confirm']").live('click',function(){
        $("#confirm_error").text('');
    })
    //var history_num = -1;
    $("#js_search_box").find('.contract_filing').live('click',function(){
        $("#js_search_box").find('a').removeClass('link_on');
        $(this).addClass('link_on');
        var id = $(this).attr('id');
        var data = $(this).attr('data');
        $("#js_search_box .btn-lv").hide();
        $("#"+id+'1').show();
        $("#iframepage").attr("src",data);
        //history_num = history_num-1;
        //$("#return_last").bind('click',function(){history.go(history_num);});
    })
});

    //iframe自适应高度
    function iFrameHeight() {
        var ifm= document.getElementById("iframepage");
        var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;
        if(ifm != null && subWeb != null) {
           ifm.height = subWeb.body.scrollHeight;
           //ifm.width = subWeb.body.scrollWidth;
        }
    }

    //打开收付删除弹窗
    function open_actual_add(id){
        $("#actual").attr('src','/contract/contract_actual_modify/'+id);
        openWin('js_actual_pop');
    }

    //打开收付删除弹窗
    function open_should_delete(id){
        $('#flow_id').val(id);
        openWin('js_del_pop1');
    }
    function open_divide_delete(id){
        $('#divide_id').val(id);
        openWin('js_del_divide');
    }

    function open_template_edit(id){
        $("#js_edit_template_pop").find(".iframePop").attr('src','/contract/warrant_template_add/'+id);
        openWin('js_edit_template_pop');
    }

    //删除此条收付记录
    function delete_actual_this(){
        $.ajax({
            url:"/contract/flow_del",
            type:"GET",
            dataType:"json",
            data:{
                id:$('#flow_id').val(),
                c_id:$("#contract_id").val(),
                flow_type:'actual'
            },
            success:function(data){
                if(data['result'] == 'ok'){
                    iframepage.window.location=iframepage.window.location;
                    $('#js_prompt1').text(data['msg']);
                    openWin('js_pop_success');
                }else{
                    $('#js_prompt2').text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        })
    }

    //删除此条收付记录
    function delete_should_this(){
        $.ajax({
            url:"/contract/flow_del",
            type:"GET",
            dataType:"json",
            data:{
                id:$('#flow_id').val(),
                c_id:$("#contract_id").val(),
                flow_type:'should'
            },
            success:function(data){
                if(data['result'] == 'ok'){
                    iframepage.window.location=iframepage.window.location;
                    if (data['num'] == 0)
                    {
                        $("#actual_flow1").remove();
                    }
                    $('#js_prompt1').text(data['msg']);
                    openWin('js_pop_success');
                }else{
                    $('#js_prompt2').text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        })
    }

    //删除此条收付记录
    function delete_divide(){
        $.ajax({
            url:"/contract/divide_del",
            type:"POST",
            dataType:"json",
            data:{
                id:$('#divide_id').val(),
                c_id:$("#contract_id").val()
            },
            success:function(data){
                if(data['result'] == 'ok'){
                    iframepage.window.location=iframepage.window.location;
                    $('#js_prompt1').text(data['msg']);
                    openWin('js_pop_success');
                }else{
                    $('#js_prompt2').text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        });
    }



    function complete_commission(){
        var total = iframepage.window.document.getElementById("percent_total").value;
        if(parseInt(total) == 100){
            $.ajax({
                url:"/contract/confirm_all_commission",
                type:"POST",
                dataType:"json",
                data:{
                    c_id:$("#contract_id").val()
                },
                success:function(data){
                    if(data['result'] == 'ok'){
                        iframepage.window.location=iframepage.window.location;
                        $('#js_prompt1').text(data['msg']);
                        openWin('js_pop_success');
                    }else{
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            })
        }else{
            $('#js_prompt2').text('您还有剩余的业绩未分配！');
            openWin('js_pop_false');
        }
    }

    function sure_flow(){
        $.ajax({
            url:"/contract/flow_sure",
            type:"POST",
            dataType:"json",
            data:{
                id:$("#flow_id").val(),
                c_id:$("#contract_id").val()
            },
            success:function(data){
                if(data['result'] == 'ok'){
                    iframepage.window.location=iframepage.window.location;
                    $('#js_prompt1').text(data['msg']);
                    openWin('js_pop_success');
                }else{
                    $('#js_prompt2').text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        })
    }

</script>
