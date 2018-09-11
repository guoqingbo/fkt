<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<div class="pop_box_g" style="display:block; height:340px;border:0;">
    <div class="hd">
        <div class="title">分配房源</div>
        <div class="close_pop"></div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner" style="height:270px;">
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
						<?php if($type=='sell'){?>
                        <th width="87">报价</th>
						<?php }else{?>
						 <th width="87">报价</th>
						<?php }?>
                    </tr>

                   	<?php
					if($house_list){
						foreach($house_list as $key=>$val){
					?>
                    <tr>
                        <td><?php if($type=='sell'){echo 'CS' . $val['id'];}else{echo 'CZ' . $val['id'];}?></td>
                        <td><?php echo $config['sell_type'][$val['sell_type']];?></td>
                        <td><?php echo $district[$val['district_id']]['district']; ?></td>
                        <td><?php echo $street[$val['street_id']]['streetname']; ?></td>
                        <td><?php echo $val['block_name']?></td>
                        <td><?php echo $val['room']."-".$val['hall']."-".$val['toilet'];?></td>
                        <td><?php echo strip_end_0($val['buildarea']);?></td>
                        <td><?php echo ('1'==$val['price_danwei'])?strip_end_0($val['price']/$val['buildarea']/30):strip_end_0($val['price']);?>
                            <?php if($type=='sell'){
                                echo 'W';
                            }else{
                                echo ('1'==$val['price_danwei'])?'元/㎡*天':'元/月';
                            }?>
                        </td>
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
					<input type="hidden" name="brokeed_id" value="<?php echo $broker_id;?>">
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
                    <select class="select" name="run_broker_id" id="seid">
                        <option value="0">请选择</option>
                    </select>
                    </p>
                </div>
            </div>
            <input type="hidden" name="house_id" value="<?php echo $house_id?>">
            <input type="hidden" name="secret_key" id="secret_key" value="<?=$secret_key?>">
            <a class="btn-lv1 btn-mid" style="margin-top:20px; cursor: pointer;" href="javascript:void(0)" onclick="add_allocate_house('<?php echo $type;?>')">确定</a> </div>
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
function add_allocate_house(type)
{
	var house_id = $("input[name='house_id']").val();//房源id
	var broker_id=$("input[name='brokeed_id']").val();//分配人的id
	var run_broker_id = $("select[name='run_broker_id']").val();//执行人id
    var secret_key = $("#secret_key").val();//内容
	var adddata = {
        'house_id':house_id,
        'run_broker_id':run_broker_id,
        'secret_key' : secret_key
	};
	if (parseInt(run_broker_id) == 0)
	{
		openWin('js_pop_do_warning');
		$('#dialog_do_warnig_tip').html('请选择接收方！');
		return;
	}else if(run_broker_id==broker_id){
		openWin('js_pop_do_warning');
		$('#dialog_do_warnig_tip').html('不能分配给自己！');
		return false;
	}
	$.ajax({
		url:"<?php echo MLS_URL;?>/" + type + "/add_allocate_house/",
		type:'post',
		data:adddata,
		success:function(return_data){
           if(return_data == 'errorCode401')
            {
                login_out();
                return false;
            }
            else if(return_data == 'errorCode403')
            {
                permission_none();
                return false;
            }
			else if( return_data == 1)
            {
                $("#dialog_do_itp").html("分配成功");
                $("#dialog_do_itp_src").attr('src','<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png');
                openWin('js_pop_do_success');
                //setTimeout(function() {window.parent.location.reload();}, 500);
			}
            else
            {
				$("#dialog_do_itp").html("分配失败");
                $("#dialog_do_itp_src").attr('src','<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png');
				openWin('js_pop_do_success');
			}
		}
	});
}
function fun_c_pop(){
    var _url = window.parent.location.href;
    $(window.parent.document).find("#js_allocate_house").hide();
    $(window.parent.document).find("#js_GTipsCoverWxr").remove();
    window.parent.location.href = _url;
}
</script>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="fun_c_pop()" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
				<img id="dialog_do_itp_src" style="margin-right:10px;" src="">
                <span class="text" id='dialog_do_itp'></span>
            </div>
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


