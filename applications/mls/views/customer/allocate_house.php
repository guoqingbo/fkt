<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<div class="pop_box_g" style="display:block">
    <div class="hd">
        <div class="title">分配房源</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <h3 class="title">分配对象</h3>
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="w90">房源编号</th>
                        <th class="w90">物业类型</th>
                        <th class="w80">区属</th>
                        <th class="w90">板块</th>
                        <th class="w170">楼盘</th>
                        <th class="w80">户型</th>
                        <th class="w80">面积(㎡)</th>
						<?php if($type=='customer'){
						?>
                        <th>报价(W)</th>
						<?php }else{?>
						 <th>月/元</th>
						<?php }?>
                    </tr>

                   	<?php
					if($house_list){
						foreach($house_list as $key=>$val){
					?>
                    <tr>
                        <td><?php echo $val['id'];?></td>
                        <td><?php echo $conf_customer['property_type'][$val['property_type']];?></td>
                        <td><?php echo $district[$val['dist_id1']]['district']; ?></td>
                        <td><?php echo $street[$val['street_id1']]['streetname']; ?></td>
                        <td><?php if(isset($val['cmt_id1']) && $val['cmt_id1'] != 0 && isset($community[$val['cmt_id1']])) { echo $community[$val['cmt_id1']];}?></td>
                        <td><?php echo $val['room_min']."-".$val['room_max'];?></td>
                        <td><?php echo $val['area_min']."-".$val['area_max'];?></td>
                        <td><?php echo $val['price_min']."-".$val['price_max'];?></td>
                    </tr>
					<?php }}?>
                </table>
            </div>
            <div class="clear">&nbsp;</div>
            <div class="inner inner02">
                <div class="item_fg_h clearfix">
                    <p class="t_text">分配人：</p>
                    <p class="i_text"><?php echo $agency_name;?></p>
                    <p class="t_text"><?php echo $broker_name?></p>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">接收方：</p>
                    <p class="i_text">
                    <?php if (isset($agency_list)) {?>
                    <select class="select" name="agentname" onchange="chang('sell')">
						<option value="0">请选择公司</option>
						<?php if($agency_list){
					       foreach($agency_list as $key=>$val){
					       ?>
                            <option value="<?php echo $val['agency_id']?>"><?php echo $val['agency_name']?></option>
							<?php } }?>
                        </select>
                    <p class="left">&nbsp;&nbsp;&nbsp;</p>
                    <?php } ?>
                    <select class="select" id="seid" name="run_broker_id" >
                        <option value="0">请选择经纪人</option>
                        <?php
                        if (isset($broker_list)) {
                            foreach($broker_list as $v) {
                        ?>
                            <option value="<?php echo $v['broker_id']?>"><?php echo $v['truename']?></option>
                        <?php }} ?>
                    </select>
                    </p>
                </div>
            </div>
            <input type="hidden" name="house_id" value="<?php echo $house_id?>">
            <a class="save_btn" onclick="add_allocate_house('<?php echo $type;?>')" style="cursor: pointer;">保存</a> </div>
    </div>
</div>
<script>
function chang(type){
 var agency_id=$("select[name='agentname']").val();
 $.ajax({
	url: "<?php echo MLS_URL;?>/"+type+"/broker_list/",
	type: "GET",
	dataType: "json",
	data:{agency_id: agency_id},
	success:function(data_list){
		var str_html='';
		for(var i=0;i<data_list.length;i++){
			str_html +='<option value='+data_list[i].broker_id+'>'+data_list[i].truename+'</option>';
		}
		$("#seid").empty().html(str_html);
	}
 });

}
</script>
<script>
function add_allocate_house(type){
	var house_id=$("input[name='house_id']").val();//房源id
	var run_broker_id=$("select[name='run_broker_id']").val();//执行人id
	var adddata = {
        'house_id':house_id,
        'run_broker_id':run_broker_id,
	};
	$.ajax({
		url:"<?php echo MLS_URL;?>/" + type + "/add_allocate_house/",
		type:'post',
		data:adddata,
		success:function(return_data){
			if(return_data=1){
				 $("#dialog_do_itp").html("分配成功");
				  openWin('js_pop_do_success');

			}else{
				$("#dialog_do_itp").html("分配失败");
				 openWin('js_pop_do_success');
			}
		}
	});


}
</script>
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
