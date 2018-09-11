<body>


<script>
    window.parent.addNavClass(17);
</script>
<!--导航栏-->
<div class="tab_box" id="js_tab_box">
    <?php echo $user_menu;?>
</div>
<!--主要内容-->

<!-- 上部菜单选项，按钮-->

<div class="search_box clearfix" id="js_search_box">
    <form name="search_form" id="search_form" method="post" action="<?php echo MLS_URL;?>/operate_log/index">
		<input type="hidden" id="sell_id_all_string" value="<?=$sell_id_all_string?>">
		 <div class="fg_box">
            <p class="fg fg_tex">操作部门：</p>
            <div class="fg" style="*padding-top:10px;">
                <select class="select " name="agency_id" id="agency_id" onchange="chang('sell')">
                    <?php if($this_role_level < 6){ ?>
                    <option value="">请选择</option>
                    <?php } ?>
                    <?php foreach($agency_list as $key=>$val) { ?>
                        <option value="<?php echo $val['id'];?>" <?=($where_cond['agency_id']==$val['id'])?"selected":""?>><?php echo $val['name'];?></option>
                    <?php }?>
                </select>
            </div>

            <div class="fg" style="*padding-top:10px; padding-left:10px ">
                <select class="select" id="list_broker" name="broker_id">
                    <option value="">请选择</option>
                    <?php if(is_full_array($broker_arr)){ ?>
                        <?php foreach($broker_arr as $key => $val){ ?>
                        <option value="<?php echo $val['broker_id'];?>" <?=($where_cond['broker_id']==$val['broker_id'])?"selected":""?>><?php echo $val['truename'];?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">操作日期：</p>
            <div class="fg">
                <input type="text" class="fg-time" name="start_time" value="<?=$where_cond['start_time'];?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"  onchange="check_num();">
            </div>
            <div class="fg fg_tex03">—</div>
            <div class="fg fg_tex03">
            <input type="text" class="fg-time" name="end_time" value="<?=$where_cond['end_time'];?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"  onchange="check_num();">
            <span style="font-weight:bold;color:red;" id="time_reminder"></span>
            </div>
        </div>

        <div class="fg_box">
			<p class="fg fg_tex"> 操作类型：</p>
			<div class="fg">
				<select class="select" name="type">
					<option value='0'>不限</option>
					<?php
							foreach($type as $key =>$val)
							{
								echo '<option value="'.$key.'" ';
								if($key == $where_cond['type'])
									echo "selected";
								echo '> '.$val.'</option>';
							}
						?>
				</select>
			</div>
		</div>
		<script type="text/javascript">
            $(function(){
                $.widget( "custom.autocomplete", $.ui.autocomplete, {
                    _renderItem: function( ul, item ) {
                        if(item.id>0){
                            return $( "<li>" )
                            .data( "item.autocomplete", item )
                            .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.label+'</span><span class="ui_district">'+item.districtname+'</span><span class="ui_address">'+item.address+'</span></a>')
                            .appendTo( ul );
                        }else{
                            return $( "<li>" )
                            .data( "item.autocomplete", item )
                            .append('<a class="ui-corner-all" tabindex="-1">'+item.label+'</a>')
                            .appendTo( ul );
                        }
                    }
                });
			$("#block_name").autocomplete({
				source: function( request, response ) {
					var term = request.term;
					$("#block_id").val("");
					$.ajax({
						url: "/community/get_cmtinfo_by_kw/",
						type: "GET",
						dataType: "json",
						data: {
							keyword: term
						},
						success: function(data) {
							//判断返回数据是否为空，不为空返回数据。
							if( data[0]['id'] != '0'){
								response(data);
							}else{
								response(data);
							}
						}
					});
				},
				minLength: 1,
				removeinput: 0,
				select: function(event,ui) {
					if(ui.item.id > 0){
						var blockname = ui.item.label;
						var id = ui.item.id;
						var streetid = ui.item.streetid;
						var streetname = ui.item.streetname;
						var dist_id = ui.item.dist_id;
						var districtname = ui.item.districtname;
						var address = ui.item.address;

						//操作
						$("#block_id").val(id);
						$("#block_name").val(blockname);
						removeinput = 2;
					}else{
						removeinput = 1;
					}
				},
				close: function(event) {
					if(typeof(removeinput)=='undefined' || removeinput == 1){
						$("#block_name").val("");
						$("#block_id").val("");
					}
				}
			});
		});
		</script>
        <div class="fg_box">
			<p class="fg fg_tex"> 操作内容：</p>
			<div class="fg">
                <input type="text" class="input w90 ui-autocomplete-input" value="<?php echo $like_code['text']; ?>" name="text" autocomplete="off">
			</div>
		</div>

        <div class="fg_box">
			<div class="fg"> <a href="javascript:void(0)" class="btn" onclick="sub_form('search_form');return false;"><span class="btn_inner">查询</span></a> </div>
		</div>
        <div class="fun_btn clearfix" id="js_fun_btn" style="display:none">
			<div class="get_page">
				<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
			</div>
		</div>
	</form>
</div>

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tbody>
                <tr>
                <td class="c3">
                    <div class="info">操作部门</div>
                </td>
                <td class="c3">
                    <div class="info">员工</div>
                </td>
                <td class="c4">
                    <div class="info">操作类型</div>
                </td>
                <td class="c3">
                    <div class="info">操作内容</div>
                </td>
                <td class="c3">
                    <div class="info">来源系统</div>
                </td>
                <td class="c3">
                    <div class="info">设备号</div>
                </td>
                <td class="c3">
                    <div class="info">来源IP</div>
                </td>
                <td class="c3">
                    <div class="info">操作时间</div>
                </td>
            </tr>
        </tbody></table>
    </div>
    <div style="height: 226px;" class="inner shop_inner" id="js_inner">
        <table class="table">
            <tbody>
            <?php
			if(is_array($operate_log2) && !empty($operate_log2)){
			foreach($operate_log2 as $key =>$val) {//print_r($val);echo "<br>";?>
            <tr class="bg" onclick="openWin('js_information<?php echo $val['id'];?>');">
                <td class="c3">
                    <div class="info"><?php echo $val['agency_name']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $val['broker_name']; ?></div>
                </td>
                <td class="c4">
                    <div class="info"><?php echo $val['type_name']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $val['text']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $val['from_system_name']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $val['device_id']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo $val['from_ip']; ?></div>
                </td>
                <td class="c3">
                    <div class="info"><?php echo date('Y-m-d H:i:s',$val['time']); ?></div>
                </td>
            </tr>
            <?php
                }
                }else{
            ?>
            <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php } ?>
          </tbody>
        </table>
    </div>
</div>
<!-- 上部菜单选项，按钮---end-->
<div class="fun_btn clearfix" id="js_fun_btn">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>

<!--详细资料-->
<?php
if(is_full_array($operate_log2)){
    foreach($operate_log2 as $key => $value){
?>
<div class="pop_box_g information_popup" id="js_information<?php echo $value['id'];?>" style="width:450px;height:320px;">
    <div class="hd" style="width:450px">
        <div class="title">详细资料</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="information_content" style="width:392px;height: 230px;overflow-y: hidden;">
        <input type="hidden" value="0" id="js_information_input">
    	<table class="table if_table2" id="modifynew">
            <tr>
				<td colspan="3"><em class="c666">操作部门：</em><?php echo $value['agency_name']; ?></td>
			</tr>
            <tr>
				<td colspan="3"><em class="c666">员工：</em><?php echo $value['broker_name']; ?></td>
			</tr>
            <tr>
				<td colspan="3"><em class="c666">操作类型：</em><?php echo $value['type_name']; ?></td>
			</tr>
            <tr>
				<td colspan="3"><em class="c666">操作内容：</em><?php echo $value['text']; ?></td>
			</tr>
            <tr>
				<td><em class="c666">操作时间：</em><?php echo date('Y-m-d',$value['time']); ?></td>
				<td><em class="c666">来源IP：</em><?php echo $value['from_ip']; ?></td>
                <td><em class="c666">来源系统：</em><?php echo $value['from_system']; ?></td>
			</tr>
            <tr>
                <td colspan="3"><em class="c666">设备号：</em><?php echo $value['device_id']; ?></td>
			</tr>
    	</table>
    </div>
</div>
<?php }} ?>

<!--提示框-->
<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">操作成功！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="location.href='/data_transfer/'">确定</button>
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
				<p class="text" id="dialog_do_warnig_tip">操作失败！</p>
			</div>
		</div>
	</div>
</div>
<div id="js_pop_do_delete"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_delete_tip">确定要转移选定的记录吗？</p>
				<button type="button" id="dialog_btn" class="btn-lv1 btn-left JS_Close" move_data_type="">确定</button>
				<button type="button" class="btn-hui1 JS_Close">取消</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
function chang(type){
 var agency_id=$("select[name='agency_id']").val();
 $.ajax({
	url: "<?php echo MLS_URL;?>/operate_log/broker_list/",
	type: "GET",
	dataType: "json",
	data:{agency_id: agency_id},
	success:function(data_list){
		var str_html='<option value="0">不限</option>';
        if(agency_id>0 || agency_id==0){
            for(var i=0;i<data_list.length;i++){
                str_html +='<option value='+data_list[i].broker_id+'>'+data_list[i].truename+'</option>';
            }
        }
		$("#list_broker").empty().html(str_html);
	}
 });

}

</script>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->


