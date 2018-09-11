<script>
    window.parent.addNavClass(11);
</script>

<div class="tab_box" id="js_tab_box">
   <?php echo $user_menu;?>
</div>

<div id="js_search_box" class="shop_tab_title">
	<?php echo $user_func_menu;?>
        <a href="javascript:void(0)" onClick="openWin('js_add_inform')" class="btn-lan right"><span>发布通知</span></a>
</div>

<form name="search_form" id="subform" method="post" action="">

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
              <td class="c5"><div class="info"><input type="checkbox" id="js_checkbox"</div></td>
                <td class="c10"><div class="info">编号</div></td>
                <td class="c35"><div class="info">通知标题</div></td>
                <td class="c17"><div class="info">发布日期</div></td>
                <td class="c17"><div class="info">发送对象</div></td>

                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table"><?php
			if(!empty($notice_list)){
				foreach($notice_list as $key=>$value){
					date_default_timezone_set('PRC');
					echo "<tr>
							<td class='c5'><div class='info'><input id='notice_id".$value['id']."' name='notice_ids[]' value='".$value['id']."' type='checkbox' class='checkbox'></div></td>
							<td class='c10'><div class='info'>".$value['id']."</div></td>
							<td class='c35'><div class='info'>".$value['title']."</div></td>
							<td class='c17'><div class='info'>".date("Y-m-d",$value['create_time'])."</div></td>
							<td class='c17'><div class='info'>".$value['receiver_name']."</div></td>
							<td ><div class='info'><a href='javascript:void(0)' class='fun_link' onclick='openCollectDetails(\"".MLS_URL."/notice_manage/notice_detail/".$value['id']."\")'>查看</a><span style='color:#b2b2b2'>|</span><label for='notice_id".$value['id']."'><a onclick='checkdel(".$value['id'].")' class='fun_link'>删除</a></label></div></td>
						</tr>";
				}
			}else{
				echo "<tr>
							<td class='c5' colspan='6'><div class='info'>抱歉，您目前还没有发布过通知信息！</div></td>
					</tr>";
			}
		?>
        </table>
		<input type='hidden' name='angel' value='angel_in_us'>
    </div>
</div>
<div class="fun_btn clearfix" id="js_fun_btn">
    <div class="get_page">
		<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
	</div>
    <a href="javascript:void(0);" class="btn-hui1" onclick="del()">删除</a>
</div>

<div class="pop_box_g pop_see_inform" id="js_add_inform">
    <div class="hd">
        <div class="title">发布通知</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
    					<div class="inform_inner">
         					<div class="clearfix item_list">
              			<p class="l_item">标题：</p>
                 <div class="r_info"><input name="title" id="title" onchange="check_title()"  class="input_text" type="text"><span id="title2" style='color:red;'></span></div>
              </div>
              <div class="clearfix item_list">
              			<p class="l_item">内容：</p>
                 <div class="r_info"><textarea onchange="check_contents()" name="contents" id="contents" class="textarea"></textarea><span id="contents2" style='color:red;'></span></div>
              </div>
              <div class="clearfix">
                    <p class="l_item">对象：</p>
                    <div class="r_info">
                       <span class="add_name" onClick="openWin('js_add_inform_name')">添加人员</span>
                       <!--<div class="add_name add_name_del">王大鹏
                       					<div class="iconfont del" title="删除">&#xe60c;</div>
                       </div>-->
                       <span class="add_name_tip">不指定则发布给所有员工</span>
                    </div>
              </div>
             	<div class="clearfix item_list">
              		<p class="l_item">时间：</p>
                <div class="r_info"><input name="notice_time" type="text"  class="input_text" readonly onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></div>
              </div>
             	<div class="clearfix item_list">
              		<p class="l_item">级别：</p>
                <div class="r_info">
                			<label class="l"><input name="level" type="radio" value='0' checked>普通</label>
                   <label class="l"><input name="level" value='1' type="radio">重要</label>
                   <label class="l"><input name="level" value='2' type="radio">紧急</label>
                </div>
             	</div>
        		<button class="btn-lv1 btn-mid" onclick="document.getElementById('subform').submit();return false;" type="button">发布</button>
         </div>
    </div>
</div>

<div class="pop_box_g pop_see_inform pop_add_inform_name" id="js_add_inform_name">
    <div class="hd">
        <div class="title">添加人员</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
         <div class="inform_inner_add_name">
         			<div class="shop_box">
            			<p class="title">门店</p>
               <div class="info">
				   <?php foreach($agency_name as $key=>$val){?>
						<label class="s_label s_label_on"><input type="checkbox" value="<?php echo $agency_id[$key];?>" onclick="show_agent(<?php echo $agency_id[$key];?>)"><?php echo $val;?><span class="iconfont">&#xe607;</span></label>
					<?php } ?>
               	   <!--<label class="s_label s_label_on"><input type="checkbox">三牌楼店<span class="iconfont">&#xe607;</span></label>
                   <label class="s_label"><input type="checkbox">桥北<span class="iconfont">&#xe607;</span>店</label>
                   <label class="s_label"><input type="checkbox">万达<span class="iconfont">&#xe607;</span>店</label>
                   <label class="s_label"><input type="checkbox">鼓楼店<span class="iconfont">&#xe607;</span></label>-->
               </div>
             </div>
             <div class="name_box">
               <p class="title">员工</p>
                <div class="info" id="broker_list">
               </div>
         	 </div>
         </div>

         <button class="btn-lv1 btn-mid JS_Close" type="button">添加</button>
    </div>
    </div>
</div>
</form>

<div id="js_pop_box_g" class="iframePopBox" style=" width:504px;height:274px">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="504" height="274" class='iframePop' src=""></iframe>
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
                    <button type="button" id = 'dialog_share' onclick='del_yes()' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button" onclick='del_no()' class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
    </div>
</div>
<script>
	//确认删除函数，点击 "确定" 提交表单，进行删除 ,反之返回 "false"
	function del_yes(){
		document.getElementById('subform').submit();
		return false;
	}
	function del_no(){
		location.href = '/notice_manage/notice_list';
		return false;
	}
	function checkdel(){
		$("#dialogSaveDiv").html("您确定要删除此通知吗？");
		openWin('jss_pop_tip');
		return false;
		//  href='<?php echo MLS_URL;?>/notice_manage/del_notice/".$value['id']."'

	}
	function check_title(){
		var title = $('#title').val();
		if(title.length >= 20){
			document.getElementById('title').value = '';
			document.getElementById('title2').innerHTML = '<h1>*标题不能多于二十字！</h1>';
		}
	}
	function check_contents(){
		var title = $('#contents').val();
		if(title.length >= 100){
			document.getElementById('contents').value = '';
			document.getElementById('contents2').innerHTML = '<h1>*通知内容不能超过一百字！</h1>';
		}
	}
	function show_agent(agency_id){
			//alert(agency_id);
			$.ajax({
				type: 'get',
				url : '<?php echo MLS_URL;?>/notice_manage/get_brokerinfo_by_agencyid/'+agency_id,
				dataType:'json',
				success: function(msg){
					var str = '';
					if(msg.result=='no result'){
						str = '<label class="n_label"><input type="checkbox" name="receiver[]">暂无资料</label>';
					}else{
						for(var i=0;i<msg.length;i++){
							str +='<label class="n_label"><input type="checkbox" name="receiver[]" value="'+msg[i].broker_id+'">'+msg[i].truename+'</label>';
						}
					}
					$('#broker_list').empty();
					$('#broker_list').append(str);
				}
			});
	};


</script>

<script>
	function del(){
			//判断用户用没有选择要删除的通知
			var condition  = document.getElementsByName("notice_ids[]");
			var select_len = condition.length;
			for(var i=0;i<condition.length;i++){
				if(condition[i].checked == true){
					$("#dialogSaveDiv").html("您确定要删除所选的通知吗？");
					openWin('jss_pop_tip');
				}else if(condition[select_len-1].checked == false){
					$("#dialog_do_warnig_tip").html("请选择要删除的通知！");
					openWin('js_pop_do_warning');
					return false;
				}
			}

	}
</script>
