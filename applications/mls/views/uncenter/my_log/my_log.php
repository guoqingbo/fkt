<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<div class="tab_box" id="js_tab_box">
    <?php echo $user_menu;?>
</div>
<form method='post' action='' id='search_form' name='search_form'>
<div id="js_search_box_02">
    <div class="search_box clearfix">
        <a class="add_p_rz" onclick="openWin('js_pop_add_info_r')" href="javascript:void(0)"><span>添加日志</span></a>
        <div class="fg_box">
            <p class="fg fg_tex"> 时间：</p>
            <div class="fg">
                <input type="text" class="input w90 time_bg" id="start_time" name="start_time" value="<?php if(isset($_POST['start_time'])){echo $_POST['start_time'];}?>" onclick="WdatePicker()">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input type="text" class="input w90 time_bg" id="end_time" name="end_time" value="<?php if(isset($_POST['end_time'])){echo $_POST['end_time'];}?>" onclick="WdatePicker()">
            </div>
        </div>
        <div class="fg_box">
            <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
            <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
        </div>
    </div>
</div>

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <td class="c3"><div class="info"><input type="checkbox" id="js_checkbox"></div></td>
                <!-- <td class="c5"><div class="info">序号</div></td> -->
                <td class="c15"><div class="info">日期</div></td>
                <td class="c15"><div class="info">标题</div></td>
                <td class="c30"><div class="info">内容</div></td>
                <td class="c20"><div class="info">批示内容</div></td>
                <td><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
			<?php
			if($list)
			{
				foreach($list as $key => $val)
				{
			?>
				<tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
					<!-- <td class="c3"> -->
						<!-- <div class="info"> -->
							<!-- <input type="checkbox" class="checkbox" name="items" value="<?php echo $val['id'];?>"> --><!--个人记事本 2015.04.02 wty-->
						<!-- </div> -->
					<!-- </td> -->
					<td class="c5"><div class="info"><?php echo $val['id'];?></div></td>
					<td class="c15"><div class="info"><?php echo date("Y-m-d H:i:s",$val['create_time']);?></div></td>
					<td class="c15"><div class="info"><?php echo $val['title'];?></div></td>
					<td class="c30"><div class="info"><p class="left_text"><?php echo $val['content'];?></p></div></td>
					<td class="c20">
						<div class="info">
                        <?php
						if($val['instructions'])
						{
							foreach($val['instructions'] as $k => $v)
							{
						?>
								<p class="left_text"><?php echo $v['truename'];?>：<?php echo $v['content'];?></p>
						<?php
							}
						}else{
						?>
								<p class="left_text"></p>
						<?php }?>
						</div>
					</td>
					<td><div class="info"><a href="javascript:void(0);" onClick="open_details('<?php echo $val['id'];?>')" class="fun_link">查看</a><a  href="javascript:void(0);" onclick="return checkdel('<?php echo $val['id'];?>')" class="fun_link">删除</a></div></td>
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

<div id="js_fun_btn" class="fun_btn clearfix">
	<div class="get_page">
		<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
	</div>
    <a class="grey_btn" href="javascript:void(0);"><span>删除</span></a>
</div>
</form>


<!--详情页弹框-->
<div id="js_pop_see_prz" class="pop_box_g pop_see_inform pop_see_prz">
    <div class="hd">
        <div class="title">日志详情</div>
         <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a> </div>
    </div>
    <div class="mod mod_bg">
    	<div class="inform_inner">
		    <input type="hidden" class="se_id" value="" />
                    <table class="deal_table deal_table_see" style="width:100%;">
                        <tr>
                            <th style="width:10%; padding:10px 0;" valign="top">标题：</th>
                            <td style="padding:10px 0;" class="se_title"></td>
                        </tr>
                        <tr class="se_text">
                            <th style="padding:10px 0;" valign="top">内容：</th>
                            <td style="padding:10px 0;" class="se_content"></td>
                        </tr>
                    </table>

                    <table class="table">
                    </table>

			<button type="button" class="btn-lv1 btn-mid JS_Close">确定</button>
		</div>
    </div>
</div>



<div id="js_pop_add_info_r" class="pop_box_g pop_see_inform pop_add_prz" >
    <div class="hd">
        <div class="title">发布日志</div>
         <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a> </div>
    </div>
	<form id="add_log" method="post" action="<?php echo MLS_URL;?>/my_log/add/">
    <div class="mod mod_bg">
    	<div class="inform_inner">
            <table class="deal_table deal_table_see">
                <tr>
                    <th width="40">标题：</th>
                    <td colspan="5"><input type="text" class="input_text"  id="title" name="title" placeholder="  请输入标题"></td>
                 </tr>
                 <tr>
                    <th>内容：</th>
                    <td colspan="5"><textarea class="textarea"  id="content" name="content" placeholder="  请输入内容(至少输入20个字)"></textarea></td>
                 </tr>
			</table>

			<button type="button" class="btn-lv1 btn-mid"  onclick="add_log();">提交</button>
		</div>

    </div>
	</form>
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

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="sub_form();" title="关闭" class="JS_Close iconfont"></a>
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
                    <p class="text" id="dialogSaveDiv" style="font-size:14px;"></p>
                     <div style="width:120px; margin:10px auto 0; height:auto; overflow:hidden; zoom:1;">
                    <button type="button" id = 'dialog_share' style="float:left;" class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button"  style="float:right;" class="btn-hui1 JS_Close">取消</button>
                    </div>
                    <input type ="hidden" name='ci_id' id = 'rowid' value = ''>
                    <input type ="hidden" name='secret_key' id = 'secret_key' value = ''>
                    <input type ="hidden" name='atction_type' id = 'atction_type' value = ''>
                    <input type ="hidden" name='do_type' id = 'do_type' value = ''>
                </div>
            </div>
    </div>
</div>
<script>
//添加日志条件判断
function add_log()
{
	var act = 1;
	var title = $.trim($("#title").val());
	if((title == "请输入标题" || title == "") && act == 1){
		$("#dialog_do_warnig_tip").html("请输入标题");
		openWin('js_pop_do_warning');
		act = 0;
	}
	var content = $.trim($("#content").val());
	if((content == "请输入内容(至少输入20个字)" || content.length==0||content.length<0) && act == 1){
		$("#dialog_do_warnig_tip").html("请输入内容");
		openWin('js_pop_do_warning');
		act = 0;
	}
	if(content.length < 20 && act == 1){
		$("#dialog_do_warnig_tip").html("至少输入20个字");
		openWin('js_pop_do_warning');
		act = 0;
	}
	if(act == 1){
            $.ajax({
                url: "<?php echo MLS_URL;?>/my_log/add/",
                type: "GET",
                dataType: "json",
                data: {
                    title: title,
                    content:content
                },
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
                        $("#js_pop_tip").remove();
                        $("#dialog_do_itp").html("添加成功");
                        openWin('js_pop_do_success');
                        $(".JS_Close").click(function(){
                            $('#search_form').submit();
                        });
                    }else{
                        $("#js_pop_tip").remove();
                        $("#dialog_do_warnig_tip").html("添加失败");
                        openWin('js_pop_do_warning');
                    }
                }
            });
	}
}

//打开详情弹层
function open_details(id)
{
	//判断打开日志是否不变
	if($("#js_pop_see_prz .se_id").val() == id){
		openWin('js_pop_see_prz');//打开弹层
	}else{
		//ajax异步获取日志详情信息
		$.ajax({
			url: "<?php echo MLS_URL;?>/my_log/details/"+id,
			type: "GET",
			dataType: "json",
			data: {
				isajax:1
			},
			success: function(data) {
				if(data['result'] == 'ok')
				{
					//获取日志信息成功
					$("#js_pop_see_prz .se_id").val(id);
					$("#js_pop_see_prz .se_title").html(data['data']['title']);
					$("#js_pop_see_prz .se_content").html(data['data']['content']);
					$("#js_pop_see_prz .table").html("");
					for(var i in data['data']['instructions'])
					{
						var data1 = data['data']['instructions'][i];
						var tr ="<tr><th>"+data1['truename']+"："+data1['content']+"</th><td>"+data1['create_time']+"</td></tr>";
						$("#js_pop_see_prz .table").append(tr);
					}
					openWin('js_pop_see_prz');//打开弹层
				}
			}
		});
	}

}

//出售出租删除房源
function del(type){
    var text="";
    var arr = new Array();
    var select_num = 0;
    var textarr = new Array();
    $(".table").find("input:checked[name=items]").each(function(i){
        arr[i] = $(this).val();
        textarr[arr[i]] = 'tr'+arr[i];
        select_num ++;
    });
    text = arr.join(",");
    if(select_num==0){
        $("#dialog_do_warnig_tip").html("请选择需要删除的工作日志");
        openWin('js_pop_do_warning');
        return false;
    }else{
        $("#dialogSaveDiv").html("你确定删除吗？");
        openWin('jss_pop_tip');
        $("#dialog_share").click(function(){
            $.ajax({
                url: "<?php echo MLS_URL;?>/"+type+"/del/",
                type: "GET",
                dataType: "json",
                data: {
                    str: text,
                    isajax:1
                },
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
                        $("#js_pop_tip").remove();
                        $("#dialog_do_itp").html("删除成功");
                        openWin('js_pop_do_success');
                        for(var i in textarr)
                        {
                           $("#"+textarr[i]).remove();
                        }
                    }
                }
            });
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
			url: "<?php echo MLS_URL;?>/my_log/del/",
			type: "GET",
			dataType: "json",
			data: {
				str: id,
				isajax:1
			},
			success: function(data) {
				if(data['result'] == 'ok')
				{
					$("#dialog_do_itp").html("删除成功");
					openWin('js_pop_do_success');
					$("#tr"+id).remove();
				}
			}
		});
	});

}
$('.JS_Close').bind('click', function() {
    $('#dialog_share').unbind('click');
});
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>
