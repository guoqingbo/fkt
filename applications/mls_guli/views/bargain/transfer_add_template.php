<body>
<div class="delate_btn1 moudle_finsh" style="width:842px;height:502px; border:none;">
    <dl class="title_top">
        <dd><?=$page_title;?></dd>
    </dl>
    <div class="qz_moudle_add_step_stage"  style="height:460px;overflow-y:auto;position:relative;">
        <p class="finsh_stepW">模版名称：<input type="text" name ='template_name' class="qz_moudle_text" value="<?=$template['template_name'];?>" maxlength="8"></p>
        <div class="transfer_process L25" style="padding-bottom:20px;">
                <ul>
                    <?php if($template['step']){foreach($template['step'] as $key => $val){?>
                    <li class="transfer_process_bg3"  style="padding-bottom:0;" title="<?=$val['stage_name1'];?>">
                    <p><?=$val['stage_name2'];?></p>
                    </li>
                    <li class="transfer_process_bg2"  style="padding-bottom:0;">
                        <img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/qz_process5_10.gif" />
                    </li>
                    <?php }}?>
                    <li class="transfer_process_bg5"  style="padding-bottom:0;" onclick="$('#title').html('新建权证流程模板步骤');openWin('js_temp_box2');">
                        <a href="javascript:void(0);">
                        <p style=" font-weight: normal;">添加步骤</p></a>
                    </li>
                </ul>
            </div>
            <script>
                $(function(){
                    $('.transfer_process').find('.transfer_process_bg2').last().children('img').attr('src','<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/qz_process6_12.gif');
                })
            </script>
            <table align="center" style="float:left" class="qz_finse_step_table">
                <thead align="center" cellspacing="0" border="0" width="100%">
                    <tr bgcolor="#f0f0f0" height="38" width="100%">
                        <td width="8%">步骤</td>
                        <td width="40%">流程阶段</td>
                        <td width="20%">添加门店</td>
                        <td width="8%">添加人</td>
                        <td width="10%">添加时间</td>
                        <td width="14%">操作</td>
                    </tr>
                </thead>
                <tbody class="ghbscq" align="center" id="template_table">
                    <?php if($template['step']){foreach($template['step'] as $key =>$val){?>
                    <tr class="resut_table_border qz_porcee"  id="tr<?=$val['id'];?>">
                        <td width="7%"><?=$stage_conf[$key+1]['text'];?></td>
                        <td width="40%" class="qz_text_ellipsis"><?=$val['stage_name1'];?></td>
                        <td width="20%"><?=$val['department_name'];?></td>
                        <td width="7%"><?=$val['signatory_name'];?></td>
                        <td width="12%"><?=$val['createtime'];?></td>
                        <td width="14%">
                            <a href="javascript:void(0);" onclick="open_transfer_edit(<?=$val['id'];?>,<?=$key+1?>)">修改</a>|
                            <a href="javascript:void(0);" onclick="open_transfer_delete(<?=$val['id'];?>);">删除</a>
                        </td>
                    </tr>
                    <?php }}?>
                </tbody>
            </table>
         <div class="qz_moudle_con1 "><a href="javascript:void(0)" onclick="save(<?=$id;?>,<?=$key1?>,<?=$c_id?>);">保存</a></div>
    </div>

</div>
<script type="text/javascript">
    $(function(){

        $(".ghbscq tr:odd").find("td").css("background","#f7f7f7");
        (".ghbscq tr:even").find("td").css("background","#fcfcfc")

    })
</script>
<div style="display:none" class="delate_btn1 qz_stepW qz_stepH" id="js_temp_box2" style="position:relative;">
    <dl class="title_top">
        <dd id="title">新建权证流程模板步骤</dd>
        <dt class="JS_Close">X</dt>
    </dl>
   <div class="qz_moudle_add_step" style="height: 303px;overflow-y: auto;">
        <dl class="qz_moudle_add_step_type">
            <dd>权证流程模板：<b><?=$template['template_name'];?></b></dd>
            <dt>步骤：<b id="step_name"><?=$stage_conf[count($template['step'])+1]['text'];?></b></dt>
        </dl>
        <dl class="qz_moudle_add_step_stage">
              <dd>流程阶段：</dd>
              <dt>
                <?php if($stage){foreach($stage as $key=>$val){?>
                    <?php if($key%4 == 0){?>
                    <p class="step_checkboxW" style="padding-right:20px;width:auto;"><input type="checkbox" name="step[]" value="<?=$val['id'];?>"><?=$val['stage_name'];?></p>
                    <?php }elseif($key%4 == 1){?>
                    <p class="step_checkboxW2" style="padding-right:20px;width:auto;"><input type="checkbox" name="step[]" value="<?=$val['id'];?>"><?=$val['stage_name'];?></p>
                    <?php }elseif($key%4 == 2){?>
                    <p class="step_checkboxW3" style="padding-right:20px;width:auto;"><input type="checkbox" name="step[]" value="<?=$val['id'];?>"><?=$val['stage_name'];?></p>
                    <?php }elseif($key%4 == 3){?>
                    <p class="step_checkboxW4" style="padding-right:20px;width:auto;"><input type="checkbox" name="step[]" value="<?=$val['id'];?>"><?=$val['stage_name'];?></p>
                    <?php }?>
                <?php }}?>
                <div class="errorBox" id="step_error"></div>
              </dt>
        </dl>
        <input type="hidden" id="stage_id">
        <input type="hidden" id="step_id">
        <input type="hidden" id="template_id" value="<?=$id;?>">
         <div class="qz_moudle_con1 t"><a href="javascript:void(0)" onclick="save_transfer();">确定</a></div>
  </div>
</div>

<!--删除-->
<div id="js_del_transfer" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;模板步骤删除之后不影响使用该模板的成交权证流程步骤。是否确认删除？</p>
		<button type="button" class="btn-lv1 JS_Close" onclick="delete_transfer();">确定</button>
		<button type="button" class="btn-hui1 JS_Close"  style="margin:0 0 0 10px;background:#fafafa;color:#000;border:1px soild #dcdcdc;border-radius:2px;">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--删除-->
<div id="js_edit_transfer_pop" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico.png">&nbsp;&nbsp;修改模板步骤之后不影响使用该模板的成交权证流程步骤。是否确认修改？</p>
		<button type="button" class="btn-lv1 JS_Close" onclick="transfer_detail()">确定</button>
		<button type="button" class="btn-hui1 JS_Close"  style="margin:0 0 0 10px;background:#fafafa;color:#000;border:1px soild #dcdcdc;border-radius:2px;">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="window.location.href='/bargain/transfer_template_add/<?=$id?>/<?=$key1?>/<?=$c_id?>';return false;"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt1"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1" type="button" onclick="window.location.href='/bargain/transfer_template_add/<?=$id?>/<?=$key1?>/<?=$c_id?>';return false;">确定</button>
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
                            <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico.png"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>
<script>
    $(function(){
        $("input[type='checkbox']").click(function(){
            $("#step_error").text('');
        });
    })
     for(var n= 0;n< $(".zws_border_none").length;n++){

        if((n+1)%2==0){
           $(".zws_border_none").eq(n).css("background","#f7f7f7");
           //alert(n);
        }
    }


    function open_transfer_delete(id,key){
        $('#stage_id').val(id);
        $('#step_id').val(key);
        openWin('js_del_transfer');
    }

    function open_transfer_edit(id,key){
        $('#stage_id').val(id);
        $('#step_id').val(key);
        openWin('js_edit_transfer_pop');
    }

    function transfer_detail(){
        $('#title').html('修改权证流程模板步骤');
        $.ajax({
			type: 'post',
			url: '/bargain/template_detail',
			data: {
				id:$('#stage_id').val(),
                key:$('#step_id').val()
			},
			dataType: 'json',
			success: function(data){
                if(data['result'] == 1){
                    $("#step_name").text(data['key']);
                    for(var i in  data['transfer_list']['stage_id']){
                        $("input[value='"+data['transfer_list']['stage_id'][i]+"']").attr('checked',true);
                    }
                    openWin('js_temp_box2');
                }
			}
		});
    }

    //提交权证步骤
	function save_transfer(){
		var arr = new Array;
		$("input[name='step[]']:checked").each(function(){
		   arr.push(this.value);
		})
		if(arr.length>0){
            if(arr.length<=3){
			$.ajax({
				type: 'post',
				url: '/bargain/save_template_step',
				data: {
					'stage_id':$("#stage_id").val(),
                    'template_id':$("#template_id").val(),
					'stage':arr
				},
				dataType: 'json',
				success: function(data){
					if(data['result'] == 'ok'){
                        $('#js_temp_box2').hide();
                        $("#GTipsCoverjs_temp_box2").remove();
                        $("#js_prompt1").text(data['msg']);
						openWin('js_pop_success');
					}
					else
					{
						$("#js_prompt2").text(data['msg']);
						openWin('js_pop_false');
					}
				}
			});
            }else{
				$("#step_error").text('最多选择三个步骤！');
			}
		}else{
			$("#step_error").text('请至少选择一个步骤！');
		}
	}

    function delete_transfer(){
        $.ajax({
            url:"/bargain/delete_template_step",
            type:"POST",
            dataType:"json",
            data:{
                stage_id:$('#stage_id').val(),
                step_id:$('#step_id').val()
            },
            success:function(data){
                var step = $('#step_id').val();
                if(data['result'] == 'ok'){
                    $('#js_prompt1').text(data['msg']);
                    openWin('js_pop_success');
                }else{
                    $('#js_prompt2').text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        });
    }

    function save(id,key,c_id){
        if(key ==1){
            $.ajax({
                url:"/bargain/save_transfer_step",
                type:"POST",
                dataType:"json",
                data:{
                    template_id:id,
                    bargain_id:c_id,
                    template_name:$("input[name='template_name']").val()
                },
                success:function(data){
                    if(data['result'] == 'ok'){
                        closeParentWin('js_edit_template_pop');
                        window.parent.frames["iframepage"].location=window.parent.frames["iframepage"].location;
                    }else{
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            });
        }else if(key ==2){
            $.ajax({
                url:"/bargain/save_template_name",
                type:"POST",
                dataType:"json",
                data:{
                    template_id:$('#template_id').val(),
                    template_name:$("input[name='template_name']").val()
                },
                success:function(data){
                    if(data['result'] == 'ok'){
                        window.parent.frames["template_pop"].location='/bargain/transfer_template/'+id;
                        window.parent.window.openWin('js_template_pop2');
                        closeParentWin('js_edit_template_pop');
                    }else{
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            });
        }else if(key ==3){
            $.ajax({
                url:"/bargain/save_template_name",
                type:"POST",
                dataType:"json",
                data:{
                    template_id:$('#template_id').val(),
                    template_name:$("input[name='template_name']").val()
                },
                success:function(data){
                    if(data['result'] == 'ok'){
                        window.parent.window.reload_iframe();
                        closeParentWin('js_edit_template_pop');
                    }else{
                        $('#js_prompt2').text(data['msg']);
                        openWin('js_pop_false');
                    }
                }
            });
        }
    }
</script>
</body>
