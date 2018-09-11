<body>
<div class="achievement_money_pop real_W660" style="width:840px;border:none;background:#FFF;">
    <dl class="title_top">
        <dd>权证流程模板 </dd>
    </dl>
     <!--弹出框内容-->
     <div class="qz_moudle_add_step_stage" style="width:96%;padding:0 2%;height:460px;overflow-y:auto;position:relative;">
        <div class="shop_tab_title scr_clear" id="js_search_box" style="margin: 20px 0 10px 0;">
            <a href="javascript:void(0);" class="btn-lv fr" onclick="add_template_pop();"><span  style="float:left;">新建模板</span></a>
            <?php if($id>1){?>
            <div style="display: block;" id="zws_deal">
                <a href="javascript:void(0);" class="btn-lv fr" style="margin-right:10px;" onclick="openWin('js_del_template');"><span style="float:left;">删除模板</span></a>
                <a href="javascript:void(0);" class="btn-lv fr" style="margin-right:10px;" onclick="edit_this(<?=$id;?>,3);"><span style="float:left;">编辑模板</span></a>
            </div>
            <?php }?>
            <div class="zws_tab_mod">
                <?php foreach($sys_temps as $key =>$val){?>
                <a href="/contract/warrant_template/<?=$val['id'];?>" class="link <?=$val['id']==$id?'link_on':'';?>"><span class="iconfont hide"></span  style="float:left;"><?=$val['template_name'];?></a>
                <?php }?>
            </div>
            <input type="hidden" name="template_id" value="<?=$id?>">
        </div>

        <div class="warrant_process zws_L0" style="padding-bottom:5px;">
                <ul>
                    <?php if($template_steps){foreach($template_steps as $key=>$val){?>
                    <li class="warrant_process_bg4" style="width:135px;padding-bottom:5px;">
                        <p class="stepHeight" title="<?=$val['stage_name1'];?>"><?=$val['stage_name2'];?></p>
                    </li>
                    <li style="padding-bottom:5px;">
                        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/qz_process5_10.gif"　 class="warrant_process_bg2">
                    </li>
                    <?php }}?>
                </ul>
            </div>
            <?php if($id==1){?>
            <table width="100%" style="text-align:center;background:#f0f0f0;" class="zws_border_none zws_border_line">
                <tbody>
                    <tr style="border-bottom:1px solid #e6e6e6;">
                        <td class="zws_border_td7 aad_pop_pB_10" >步骤</td>
                        <td class="zws_border_td6 aad_pop_pB_10">流程阶段</td>
                    </tr>
                    <?php if($template_steps){foreach($template_steps as $key=>$val){?>
                    <tr class="zws_border_bg_fc"  style="border-bottom:1px solid #e6e6e6;">
                        <td class="zws_border_td7 aad_pop_pB_20 "><?=$stage_conf[$key+1]['text'];?></td>
                        <td class="zws_border_td6"><p class="zws_border_hidden"><?=$val['stage_name1'];?></p></td>
                    </tr>
                    <?php }}?>
                </tbody>
            </table>
            <?php }else{?>
             <!--按揭模板一-->
             <table style="text-align:center;background:#f0f0f0;" class="zws_border_none zws_border_line" width="100%">
                <tbody>
                    <tr style="border-bottom:1px solid #e6e6e6;">
                        <td class="aad_pop_pB_10">步骤</td>
                        <td class="aad_pop_pB_10">流程阶段</td>
                        <td class="aad_pop_pB_10">添加门店</td>
                        <td class="aad_pop_pB_10">添加人</td>
                        <td class="aad_pop_pB_10">添加时间</td>
                    </tr>
                    <?php if($template_steps){foreach($template_steps as $key=>$val){?>
                    <tr class="zws_border_bg_fc" style="border-bottom:1px solid #e6e6e6;">
                        <td class="aad_pop_pB_20"><?=$stage_conf[$key+1]['text'];?></td>
                        <td class="aad_pop_pB_20"><?=$val['stage_name1'];?></td>
                        <td class="aad_pop_pB_20"><?=$val['agency_name'];?></td>
                        <td class="aad_pop_pB_20"><?=$val['broker_name'];?></td>
                        <td class="aad_pop_pB_20"><?=$val['createtime'];?></td>
                    </tr>
                    <?php }}?>
                </tbody>
            </table>
            <?php }?>
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
                <button class="btn" type="button" onclick="location.href=''">确定</button>
            </div>
         </div>
    </div>
</div>

<!--删除操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success1">
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
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt3"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn" type="button" onclick="location.href='/contract/warrant_template'">确定</button>
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
<div id="js_del_template" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;模板删除之后不可恢复。<br/>是否确认删除？</p>
		<button type="button" class="btn JS_Close" onclick="delete_this();">确定</button>
		<button type="button" class="btn btn_none JS_Close"  style="margin:0 0 0 10px;background:#fafafa;color:#000;border:1px soild #dcdcdc;border-radius:2px;">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--新建模版弹窗-->
<div class="delate_btn1 qz_moudle_H500" style="display: none;width:300px;height:200px;margin-top:-100px;margin-left:-150px" id="js_template_pop">
    <dl class="title_top">
        <dd>新建权证流程模板</dd>
        <dt class="JS_Close">X</dt>
    </dl>
    <div class="qz_moudle_con2">
        <p>模版名称：<input type="text" class="qz_moudle_text" name="template_name" maxlength="8"></p>
        <div class="qz_moudle_con1">
          <a href="javascript:void(0)" onclick="save_template(3);" class="JS_Close">下一步</a>
          <a href="javascript:void(0)" class="JS_Close" style="margin:0 0 0 10px;background:#fafafa;color:#000;border:1px solid #dcdcdc;border-radius:2px;">取　消</a>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        $("ul").find('li:last').remove();
    })

    $(".zws_border_none tr:even").css("background","#f7f7f7");



    function edit_this(id,key){
        window.parent.open_template_edit(id,key);
    }

    function delete_this(){
        $.ajax({
            url:"/contract/delete_template",
            type:"POST",
            dataType:"json",
            data:{
                template_id:$("input[name='template_id']").val()
            },
            success:function(data){
                if(data['result'] == 'ok'){
                    $('#js_prompt3').text(data['msg']);
                    openWin('js_pop_success1');
                }else{
                    $('#js_prompt2').text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        })
    }

    function save_template(key){
        $.ajax({
            url:"/contract/save_template",
            type:"POST",
            dataType:"json",
            data:{
                template_name:$("input[name='template_name']").val()
            },
            success:function(data){
                if(data['result'] == 'ok'){
                    window.location.href='/contract/warrant_template/'+data['data'];
                    window.parent.open_template_edit(data['data'],key);
                    openWin('js_edit_template_pop');
                }else{
                    $('#js_prompt2').text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        })
    }
    function add_template_pop(){
        $.ajax({
            url:"/contract/add_template_judge",
            type:"POST",
            dataType:"json",
            success:function(data){
                if(data['result'] == 'ok'){
                    openWin('js_template_pop');
                }else{
                    $('#js_prompt2').text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        })
      }
</script>
</body>
