<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<div class="tab_box" id="js_tab_box">
    <?php echo $user_menu;?>
</div>
<form method='post' action='' id='search_form' name='search_form'>
<div id="js_search_box_02">
    <div class="search_box clearfix">
		<a class="add_p_rz" onclick="openWin('js_pop_add_info_r')" href="javascript:void(0)"><span>添加</span></a>
        <div class="fg_box">
            <p class="fg fg_tex"> 时间：</p>
            <div class="fg">
                <input type="text" class="input w90 time_bg" id="start_time" name="start_time" value="<?php if(isset($_POST['start_time'])){echo $_POST['start_time'];}?>" onclick="WdatePicker()">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input type="text" class="input w90 time_bg" id="end_time" name="end_time" value="<?php if(isset($_POST['end_time'])){echo $_POST['end_time'];}?>" onclick="WdatePicker()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            <div class="fg">
            	<input type="text" class="input w90" id="blur" name="blur" value="<?php if(isset($_POST['blur'])){echo $_POST['blur'];}?>" />
            </div>
        </div>
        <div class="fg_box">
            <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">查询</span></a> </div>
            <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
        </div>
    </div>
</div>
<p class="tips1" id="js_gz_box_bg">【个人记事本】是方便用户记录个人工作中或生活中的一些私事或私人便签。每个人只能看到自己的记事内容，任何其它人都无权查看他人信息（包括管理员），可以用于记录您的私人信息。</p>
<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
				<td class="c2"><div class="info"><input type="checkbox" id="js_checkbox"></div></td>
                <!-- <td class="c5"><div class="info">序号</div></td> -->
                <td class="c15"><div class="info">修改时间</div></td>
                <td class="c15"><div class="info">标题</div></td>
                <td class="c30"><div class="info">内容预览(详细请点击查看)</div></td>
                <td><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
			<?php
			if($list)
			{
				$i = 1;
				foreach($list as $key => $val)
				{
			?>
				<tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
					<td class="c2">
						<div class="info">
							<input type="checkbox" class="checkbox" name = 'notebook_id' value="<?php echo $val['id'];?>"><!--个人记事本 2015.04.02 wty-->
						</div>
					</td>
					<!-- <td class="c5"><div class="info"><?php echo $val['id'];?></div></td> -->
					<td class="c15"><div class="info"><?php echo date("Y-m-d H:i:s",$val['created']);?></div></td>
					<td class="c15"><div class="info"><?php echo $val['title'];?></div></td>
					<td class="c30"><div class="info"><p class="left_text" ><?php echo $val['content'];?></p></div></td>
					<td>
						<div class="info">
							<a href="javascript:void(0);" onClick="open_details('<?php echo $val['id'];?>')" class="fun_link">查看</a>&nbsp;&nbsp;
							<a href="javascript:void(0);" onClick="modify_details('<?php echo $val['id'];?>')" class="fun_link">修改</a>&nbsp;&nbsp;
							<a href="javascript:void(0);" onclick="return checkdel('<?php echo $val['id'];?>')" class="fun_link">删除</a>
						</div>
					</td>
				</tr>
			<?php
                }
            }else{
                ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php }?>
        </table>
    </div>
</div>
<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
	<a class="grey_btn" href="javascript:void(0)" onclick="openWin('js_pop_no_qd05');">删除</a><!--个人记事本 2015.04.02 wty-->
	<div class="get_page">
		<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
	</div>
</div>
</form>
<!--详情页弹框-->
<div id="js_pop_see_prz" class="pop_box_g pop_see_inform pop_see_prz">
    <div class="hd">
        <div class="title">记录详情</div>
         <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a> </div>
    </div>
    <div class="mod mod_bg">
    	<div class="inform_inner">
		    <input type="hidden" class="se_id" value="" />
         	<p class="se_title"></p>

            <div class="se_text"></div>

			<table class="table">
			</table>

			<button type="button" class="btn-lv1 btn-mid JS_Close">确定</button>
		</div>
    </div>
</div>

<div id="js_pop_no_qd05" class="pop_box_g pop_see_inform pop_no_q_up">
    <input type="hidden" value="" id="remind_id"/>
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a> </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;确定删除选中的记事本?</p>
                <button type="button" class="btn-lv1 btn-left" onclick="batch_action();">确定</button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            </div>
        </div>
    </div>
</div>

<div id="js_pop_add_info_r" class="pop_box_g pop_see_inform pop_add_prz" >
    <div class="hd">
        <div class="title">建立新的记事本</div>
         <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
	<form id="add_notebook" method="post" action="<?php echo MLS_URL;?>/my_notebook/add/">
    <div class="mod mod_bg">
    	<div class="inform_inner">
            <table class="deal_table deal_table_see">
                <tr>
                    <th width="40">标题：</th>
                    <td colspan="5"><input type="text" class="input_text"  id="title" name="title" placeholder="  请输入标题"></td>
                 </tr>
                 <tr>
                    <th valign="top">内容：</th>
                    <td colspan="5"><textarea class="textarea"  id="content" name="content" placeholder="  请输入内容"></textarea></td>
                 </tr>
			</table>

			<button type="button" class="btn-lv1 btn-mid"  onclick="add_notebook();">提交</button>
		</div>

    </div>
	</form>
</div>
<div id="js_pop_modify_info_r" class="pop_box_g pop_see_inform pop_add_prz" >
    <div class="hd">
        <div class="title">修改记事本</div>
         <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
	<form id="modify_notebook" method="post" action="<?php echo MLS_URL;?>/my_notebook/modify/">
    <div class="mod mod_bg">
    	<div class="inform_inner">
            <table class="deal_table deal_table_see">
                <tr>
                    <th>标题：</th>
                    <td colspan="5">
                    	<input type="hidden" class="input_text"  id="m_id" name="m_id" >
                    	<input type="text" class="input_text"  id="m_title" name="m_title" >
                    </td>
                 </tr>
                 <tr>
                    <th valign="top">内容：</th>
                    <td colspan="5"><textarea class="textarea"  id="m_content" name="m_content"></textarea></td>
                 </tr>
			</table>

			<button type="button" class="btn-lv1 btn-mid"  onclick="modify_notebook();">提交</button>
		</div>

    </div>
	</form>
</div>

<!--操作结果弹出警告-->
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_warnig_tip'></p>
            </div>
        </div>
    </div>
</div>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="/my_notebook/" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'></p>
            </div>
        </div>
    </div>
</div>
<!--询问操作确定弹窗-->
<div id="jss_pop_tip" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" id="dialogSaveDiv"></p>
                    <button type="button" id = 'dialog_share' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                    <input type ="hidden" name='ci_id' id = 'rowid' value = ''>
                    <input type ="hidden" name='secret_key' id = 'secret_key' value = ''>
                    <input type ="hidden" name='atction_type' id = 'atction_type' value = ''>
                    <input type ="hidden" name='do_type' id = 'do_type' value = ''>
                </div>
            </div>
    </div>
</div>
<script>
//删除多条记录
function batch_action(){
    var checked_notebook = [];
    $('input[name="notebook_id"]:checked').each(function(){
        checked_notebook.push($(this).val());
    });
    var data = {
        'notebook_ids':checked_notebook
    }
    if(checked_notebook.length==0){
        $('#dialog_do_itp').html('请选择事件');
        openWin('js_pop_do_success');
    }else{
        $.ajax({
            url: "/my_notebook/del_more/",
            type: "get",
            data: data,
            success: function(data)
            {
                if('success'==data){
                    window.location.href="<?php echo MLS_URL;?>/my_notebook/";
                }
            }
        });
    }
}
//添加日志条件判断
function add_notebook()
{
	var act = 1;
	var title = $.trim($("#title").val());
	if((title == "请输入标题" || title == "") && act == 1){
		$("#dialog_do_warnig_tip").html("请输入标题");
		openWin('js_pop_do_warning');
		act = 0;
	}
	var content = $.trim($("#content").val());
	if((content == "请输入内容" || content.length==0||content.length<0) && act == 1){
		$("#dialog_do_warnig_tip").html("请输入内容");
		openWin('js_pop_do_warning');
		act = 0;
	}
	if(act == 1){
		$.ajax({
            type : 'post',
            url  : '/my_notebook/add',
            data : {title : title, content : content},
            dataType :'json',
            success : function(data){
                if (data['result'] == 'ok') {
                    $("#dialog_do_itp").html("添加成功");
                    openWin('js_pop_do_success');
                } else {
                    $("#dialog_do_itp").html("添加失败");
                    openWin('js_pop_do_success');
                }
            }
        });
	}
}

//修改记事本条件判断
function modify_notebook()
{
	var act = 1;
	var title = $.trim($("#m_title").val());
	if((title == "请输入标题" || title == "") && act == 1){
		$("#dialog_do_warnig_tip").html("请输入标题");
		openWin('js_pop_do_warning');
		act = 0;
	}
	var content = $.trim($("#m_content").val());
	if((content == "请输入内容" || content.length==0||content.length<0) && act == 1){
		$("#dialog_do_warnig_tip").html("请输入内容");
		openWin('js_pop_do_warning');
		act = 0;
	}
	if(act == 1){
		$.ajax({
            type : 'post',
            url  : '/my_notebook/modify',
            data : {m_id : $('#m_id').val(), m_title : title, m_content : content},
            dataType :'json',
            success : function(data){
                if (data['result'] == 'ok') {
                    $("#dialog_do_itp").html("修改成功");
                    openWin('js_pop_do_success');
                } else {
                    $("#dialog_do_itp").html("修改失败");
                    openWin('js_pop_do_success');
                }
            }
        });
	}
}

function modify_details(id)
{
	$.ajax({
			url: "<?php echo MLS_URL;?>/my_notebook/details/"+id,
			type: "GET",
			dataType: "json",
			data: {
				isajax:1
			},
			success: function(data) {
				if(data['result'] == 'ok')
				{
					//获取记事本信息成功
					$("#js_pop_modify_info_r").find("#m_id").val(id);
					$("#js_pop_modify_info_r").find("#m_title").val(data['data']['title']);
					$("#js_pop_modify_info_r").find("#m_content").val(data['data']['content']);
					openWin('js_pop_modify_info_r');//打开弹层
				}
			}
		});

}

//打开详情弹层
function open_details(id)
{
	//判断打开记事本是否不变
	if($("#js_pop_see_prz .se_id").val() == id){
		openWin('js_pop_see_prz');//打开弹层
	}else{
		//ajax异步获取记事本详情信息
		$.ajax({
			url: "<?php echo MLS_URL;?>/my_notebook/details/"+id,
			type: "GET",
			dataType: "json",
			data: {
				isajax:1
			},
			success: function(data) {
				if(data['result'] == 'ok')
				{
					//获取记事本信息成功
					$("#js_pop_see_prz .se_id").val(id);
					$("#js_pop_see_prz .se_title").html(data['data']['title']);
					$("#js_pop_see_prz .se_text").html(data['data']['content']);
					$("#js_pop_see_prz .table").html("");
					openWin('js_pop_see_prz');//打开弹层
				}
			}
		});
	}

}

//确认是否删除
function checkdel(id){
	$("#dialogSaveDiv").html("你确定删除吗？");
	//打开询问操作确定弹窗
    openWin('jss_pop_tip');
    $("#dialog_share").click(function(){
		$.ajax({
			url: "<?php echo MLS_URL;?>/my_notebook/del/",
			type: "GET",
			dataType: "json",
			data: {
				id: id
			},
			success: function(data) {
				if(data['result'] == 'ok')
				{
					$("#dialog_do_itp").html("删除成功");
					openWin('js_pop_do_success');
					$("#tr"+id).remove();
				}else{
					$("#dialog_do_itp").html("删除失败");
					openWin('js_pop_do_success');
					location.reload();
				}
			}
		});
	});

}
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>
