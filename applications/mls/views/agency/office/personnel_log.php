<script>
    window.parent.addNavClass(11);
</script>

<div class="tab_box" id="js_tab_box">
   <?php echo $user_menu;?>
</div>

<div id="js_search_box" class="shop_tab_title">
	<?php echo $user_func_menu;?>
</div>

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
            	<td class="c5"><div class="info"><input type="checkbox" id="js_checkbox"></div></td>
              	 <td class="c10"><div class="info">编号</div></td>
                <td class="c15"><div class="info">分店</div></td>
                <td class="c10"><div class="info">员工</div></td>
                <td class="c20"><div class="info">日志内容</div></td>
                <td class="c15"><div class="info">批阅</div></td>
                <td class="c15"><div class="info">日期</div></td>
                <td ><div class="info">操作</div></td>
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
					<td class="c5">
						<div class="info">
							<input type="checkbox" class="checkbox" name="items" value="<?php echo $val['id'];?>">
						</div>
					</td>
					<td class="c10"><div class="info"><?php echo $val['id'];?></div></td>
					<td class="c15"><div class="info"><?php echo $val['agency_name'];?></div></td>
					<td class="c10"><div class="info"><?php echo $val['truename'];?></div></td>
					<td class="c20"><div class="info"><?php echo $val['title'];?></div></td>
					<td class="c15">
						<div class="info info_color">
							<?php if($val['is_see']==1){echo "<span class='is_see s'>已阅</span>";}else{ echo "<span class='is_see'>未阅</span>";}?>
							<span class="fg">|</span>
							<?php if($val['is_ins']==1){echo "<span class='is_ins s'>已批示</span>";}else{ echo "<span class='is_ins'>未批示</span>";}?>
						</div>
					</td>
					<td class="c15"><div class="info"><?php echo date("Y-m-d H:i:s",$val['create_time']);?></div></td>

					 <td >
						<div class="info">
							<a href="javascript:void(0)" onClick="open_details('js_see_inform','<?php echo $val['id'];?>')" class="fun_link">查看</a>
							<?php if($val['is_ins']==0){?>
							|
							<a href="javascript:void(0)" onClick="open_details('js_see_inform_log','<?php echo $val['id'];?>')" class="fun_link">批示</a>
							<?php }?>
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

<form method='post' action='' id='search_form' name='search_form'>
<div class="fun_btn clearfix" id="js_fun_btn">
    <div class="get_page">
    <?php echo $page_list;?>
    </div>
    <a href="javascript:void(0);" class="btn-hui1" onclick="batch_instruct()">批量批示</a>
    <a href="javascript:void(0);" class="btn-hui1" onclick="batch_see()">已阅读</a>
    <a href="javascript:void(0);" class="btn-hui1" onclick="del('personnel_log')">删除</a>
</div>
</form>

<!--详情页弹框-->
<div class="pop_box_g pop_see_inform pop_see_log" id="js_see_inform">
    <div class="hd">
        <div class="title">查看日志</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
	<div class="mod">
		<div class="inform_inner">
			<input type="hidden" class="log_id" value="" />
			<div class="clearfix">
				<p class="l_item">标题：</p>
				<p class="r_info log_title"></p>
			</div>
			<div class="clearfix">
				<p class="l_item">内容：</p>
				<p class="r_info log_content"></p>
			</div>
			<button class="btn-lv1 btn-mid JS_Close" type="button">确定</button>
		</div>
	</div>
</div>




<!--批示弹框-->
<div class="pop_box_g pop_see_inform pop_see_log" id="js_see_inform_log">
    <div class="hd">
        <div class="title">批示日志</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
		<div class="inform_inner">
			<input type="hidden" class="log_id" name="log_id" value="" />
			<div class="clearfix">
				<p class="l_item">标题：</p>
				<p class="r_info log_title"></p>
			</div>
			<div class="clearfix">
				<p class="l_item">内容：</p>
				<p class="r_info log_content"></p>
			</div>
			<div class="clearfix">
				<p class="l_item">批示内容：</p>
				<div class="r_info">
					<textarea class="textarea" name="content" placeholder="   至少输入10个字"></textarea>
				</div>
			</div>
			<div class="shop_tab_none">&nbsp;</div>
			<button class="btn-lv1 btn-mid" type="button" onclick="instruct();">保存</button>
		</div>
    </div>
</div>

<!--批量批示弹框-->
<div class="pop_box_g pop_see_inform pop_see_log" id="js_batch_instruct_inform">
    <div class="hd">
        <div class="title">批示日志</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
		<div class="inform_inner">
			<div class="clearfix">
				<p class="l_item">批示内容：</p>
				<div class="r_info">
					<textarea class="textarea" name="content" placeholder="   至少输入10个字"></textarea>
				</div>
			</div>
			<div class="shop_tab_none">&nbsp;</div>
			<button class="btn-lv1 btn-mid" id="btn" type="button">保存</button>
		</div>
    </div>
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
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
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
//批量批示
function batch_instruct(){
    var text="";
    var arr = new Array();
    var select_num = 0;
    var textarr = new Array();
    $(".table").find("input:checked[name=items]").each(function(i) {
        arr[i] = $(this).val();
        text += ","+ arr[i];
        textarr[arr[i]] = 'tr'+arr[i];
        select_num ++;
    });
    if(select_num==0){
        $("#dialog_do_warnig_tip").html("请选择要批量操作的日志");
        openWin('js_pop_do_warning');//弹出警告
        return false;
    }else{
		openWin('js_batch_instruct_inform'); //打开批量批示弹框

        $("#js_batch_instruct_inform #btn").click(function(){
			var act = 1;
			//批量批示条件判断
			var content = $.trim($("#js_batch_instruct_inform .textarea").val());
			if(content.length < 10 && act == 1){
				$("#dialog_do_warnig_tip").html("至少输入10个字");
				openWin('js_pop_do_warning');//弹出警告
				act = 0;
			}
			if(act == 1){
				$.ajax({
					url: "<?php echo MLS_URL;?>/personnel_log/batch_instruct/",
					type: "GET",
					dataType: "json",
					data: {
						str: text,
						content: content,
						isajax:1
					},
					success: function(data) {
							//关闭批量批示弹框
							$('#js_batch_instruct_inform').hide();
							$('#' + 'GTipsCover' + 'js_batch_instruct_inform').remove();

							if(data['errorCode'] == '401')
			                {
			                    login_out();
			                    $("#jss_pop_tip").hide();
			                }
			                else if(data['errorCode'] == '403')
			                {
			                    /*permission_none();
			                    $("#jss_pop_tip").hide();*/
			                	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
			                    openWin('js_pop_do_warning');return false;
			                }else{
    							if(data['result'] == 'ok')
    							{
    								$("#dialog_do_itp").html("批示成功");
    								openWin('js_pop_do_success');
    								for(var i in textarr)
    								{
    									$("#"+textarr[i]+" .is_ins").addClass("s").html("已批示");
    								}
    							}
			                }
					}
				});
			}
		});

	}
}

//设为批量已阅读
function batch_see(){
    var select_num = 0;
    $(".table").find("input:checked[name=items]").each(function(i) {
        select_num ++;
    });
    if(select_num==0){
        $("#dialog_do_warnig_tip").html("请选择要批量操作的日志");
        openWin('js_pop_do_warning');//弹出警告
        return false;
    }else{
		$("#dialogSaveDiv").html("你确定设为批量已阅读吗？");
		openWin('jss_pop_tip');//打开询问操作确定弹窗
        $("#dialog_share").addClass("batch_see_button");

	}
}

//打开详情或批示弹层
function open_details(obj,id)
{
	//判断打开日志是否不变
	if($("#"+obj+" .log_id").val() == id){
		openWin(obj);//打开弹层
	}else{
		//ajax异步获取日志详情信息
		$.ajax({
			url: "<?php echo MLS_URL;?>/personnel_log/details/"+id,
			type: "GET",
			dataType: "json",
			data: {
				isajax:1
			},
			success: function(data) {
				if(data['result'] == 'ok')
				{
					//获取日志信息成功
					$("#"+obj+" .log_id").val(id);
					$("#"+obj+" .log_title").html(data['data']['title']);
					$("#"+obj+" .log_content").html(data['data']['content']);
					openWin(obj);//打开弹层
					$("#tr"+id+" .is_see").addClass("s").html("已阅");
				}
			}
		});
	}

}

//批示日志
function instruct()
{
	var act = 1;
	//批示日志条件判断
	var log_id = $("#js_see_inform_log .log_id").val();
	var content = $.trim($("#js_see_inform_log .textarea").val());
	if(content.length < 10 && act == 1){
		$("#dialog_do_warnig_tip").html("至少输入10个字");
		openWin('js_pop_do_warning');
		act = 0;
	}

	if(act == 1){
		$.ajax({
			url: "<?php echo MLS_URL;?>/personnel_log/instruct/",
			type: "POST",
			dataType: "json",
			data: {
				log_id: log_id,
				content: content,
				isajax:1
			},
			success: function(data) {
				//关闭批示弹框
				$('#js_see_inform_log').hide();
				$('#' + 'GTipsCover' + 'js_see_inform_log').remove();

				if(data['errorCode'] == '401')
                {
                    login_out();
                    $("#jss_pop_tip").hide();
                }
                else if(data['errorCode'] == '403')
                {
                    /*permission_none();
                    $("#jss_pop_tip").hide();*/
                	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
                    openWin('js_pop_do_warning');return false;
                }else{
    				if(data['result'] == 'ok')
    				{
    					$("#dialog_do_itp").html("批示成功");
    					openWin('js_pop_do_success');
    					$("#tr"+log_id+" .is_ins").addClass("s").html("已批示");
    				}else{
    					$("#dialog_do_warnig_tip").html(data['ms']);
    					openWin('js_pop_do_warning');
    				}
                }
			}
		});
	}

}

$(function(){
    //删除
    $(".batch_see_button").live("click",function(){
        var arr=new Array();
        var select_num = 0;
        var text="";
        var textarr = new Array();
        $(".table").find("input:checked[name=items]").each(function(i){
             arr[i] = $(this).val();
             select_num ++;
             textarr[arr[i]] = 'tr'+arr[i];
        });
        text = arr.join(",");
        if(select_num>0){
            $.ajax({
                url: "<?php echo MLS_URL;?>/personnel_log/batch_see/",
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
	                    $("#jss_pop_tip").hide();
	                }
	                else if(data['errorCode'] == '403')
	                {
	                    /*permission_none();
	                    $("#jss_pop_tip").hide();*/
	                	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
	                    openWin('js_pop_do_warning');return false;
	                }else{
						if(data['result'] == 'ok')
						{
							$("#dialog_do_itp").html("设置成功");
							openWin('js_pop_do_success');
							for(var i in textarr)
							{
								$("#"+textarr[i]+" .is_see").addClass("s").html("已阅");

							}
						}
	                }
				}
            });
        }
	});
    //清除操作确定弹窗中的方法参数
    $("#jss_pop_tip .JS_Close").live("click",function(){
        $("#dialog_share").removeClass("batch_see_button");//删除
    });
});
</script>
