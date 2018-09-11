<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<div class="pop_box_g" id="js_fenpeirenwu" style="display:block;border:none;">
    <div class="hd">
        <div class="title">分配任务</div>
        <!--<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>-->
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <h3 class="title">跟进对象</h3>
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
						<?php if($num==1){?>
						 <th width="87">报价</th>
						<?php }else{?>
						 <th width="87">租金</th>
						<?php }?>
                    </tr>

                   	<?php
					if($sell_list){
						foreach($sell_list as $key=>$val){
					?>
                    <tr>
                        <td><?php echo $sell_number.$val['id'];?></td>
                        <td><?php echo $config['sell_type'][$val['sell_type']];?></td>
                        <td><?php echo $district['3']['district']; ?></td>
                        <td><?php echo $street[$val['street_id']]['streetname']; ?></td>
                        <td><?php echo $val['block_name']?></td>
                        <td><?php echo $val['room']."-".$val['hall']."-".$val['toilet'];?></td>
                        <td><?php echo strip_end_0($val['buildarea']);?></td>
                        <td><?php echo ('1'==$val['price_danwei'])?strip_end_0($val['price']/$val['buildarea']/30):strip_end_0($val['price']);?>
                            <?php
                                if($num==1){
                                    echo 'W';
                                }else{
                                    echo ('1'==$val['price_danwei'])?'元/㎡*天':'元/月';
                                }
                            ?>
                        </td>
                    </tr>
					<?php }}?>
                </table>
            </div>
            <div class="clear">&nbsp;</div>
            <div class="inner inner02">
                <div class="item_fg_h clearfix">
                    <p class="t_text">任务分配人：</p>
                    <p class="i_text"><?php echo $agency_name;?></p>
                    <p class="t_text"><?php echo $broker_name;?></p>
					<input type="hidden" name="brokeed_id" value="<?php echo $broker_id;?>">
                </div>
				<div class="item_fg_h clearfix">
                    <p class="t_text">任务执行人：</p>
                    <p class="i_text">
					   <select class="select" name="agename" onchange="chang('sell')">
						<option value="0">请选择</option>
						<?php if($agency_list){
					foreach($agency_list as $key=>$val){
					?>
                            <option value="<?php echo $val['agency_id']?>"><?php echo $val['agency_name']?></option>
							<?php } }?>
                        </select>
						<p class="left">&nbsp;&nbsp;&nbsp;</p>
                    <select class="select" id="seid" name="run_broker_id" onchange="check_broker('sell')">
						<option value="0">请选择</option>
                    </select>
					    </p>
                </div>


                <div class="item_fg_h clearfix">
                    <p class="t_text">任务类型：</p>
                    <div class="i_text">
                        <label class="label">
                            <input type="hidden" name="task_type" id="task_type" value="2" checked="checked">
                            房源跟进</label>
                    </div>
					<?php if($num==1){
					?>
					<input type="hidden" name="task_style" value="1">
					<?php }else{?>
					<input type="hidden" name="task_style" value="2">
					<?php }?>
					<input type="hidden" name="house_id" value="<?php echo $house_id?>">
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">执行期限：</p>

                    <input class="input_text w135" name="pay_date" id='display1' onFocus="WdatePicker({lang:'zh-cn',alwaysUseStartDate:true,minDate:'%y-%M-{%d}'})" readonly value="" type="text"/>

                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">任务说明：</p>
                    <textarea class="textarea" id="textid" placeholder="请填写任务说明不少于五个字" onkeyup="textCounter()"></textarea>
                </div>
				<div class="clearfix"id="span_id" style="padding-left:86px;line-height: normal;">

				</div>
            </div>
            <input type="hidden" name="secret_key" id="secret_key" value="<?=$secret_key?>">
            <a href="javascript:void(0)" class="save_btn" id="hhh" onclick="add_tasks('<?php echo $type;?>')" >分配任务</a>
			<input value="0" type="hidden" id="aa"/>
			</div>
    </div>
</div>
<script>
function chang(type){
 var agency_id=$("select[name='agename']").val();
 $.ajax({
	url: "<?php echo MLS_URL;?>/"+type+"/broker_list/",
	type: "GET",
	dataType: "json",
	data:{agency_id: agency_id},
	success:function(data_list){
		var str_html='<option value="0">请选择</option>';
		if(agency_id>0){
			for(var i=0;i<data_list.length;i++){
				str_html +='<option value='+data_list[i].broker_id+'>'+data_list[i].truename+'</option>';
			}
		}
		$("#seid").empty().html(str_html);
	}
 });

}
//检测输入字个数
	function textCounter(){
	var text_uid=$("#textid").val();
	var text_num=5-text_uid.length;
	if(text_uid.length<5){
		$('#span_id').html('<span style="color:red;">您至少还需要输入'+text_num+'个字</span>');
	}else{
		$('#span_id').html('');
	}

}
</script>
<script>
function add_tasks(type){
	var broker_id=$("input[name='brokeed_id']").val();//分配人的id
	var task_type=$("#task_type").val();//任务类型
	var task_style=$("input[name='task_style']").val();//任务方式
	var house_id=$("input[name='house_id']").val();//房源id
	var run_broker_id=$("select[name='run_broker_id']").val();//执行人id
	var over_date=$("input[name='pay_date']").val();//期限时间
	var content=$("#textid").val();//内容
	var text_length=content.length;
        var secret_key = $("#secret_key").val();//内容
	var adddata={'task_type':task_type,
	          'task_style':task_style,
			  'house_id':house_id,
			  'run_broker_id':run_broker_id,
			  'over_date':over_date,
			  'content':content,
              'secret_key' : secret_key
	};

	if(run_broker_id==0){
		$("#dialog_do_itp").html("执行人不能为空");
		openWin('js_pop_do_success');
		return false;
	} else if(over_date==''){
		$("#dialog_do_itp").html("执行时间不能为空");
		openWin('js_pop_do_success');
		return false;
	} else if(run_broker_id==broker_id){
		$("#dialog_do_itp").html("不能分配给自己");
		openWin('js_pop_do_success');
		return false;
	}else if(content==''){
		$("#dialog_do_itp").html("跟进内容不要为空");
		openWin('js_pop_do_success');
		return false;
	}else if(text_length<5){
		return false;
	}else{
		$.ajax({
		url:"<?php echo MLS_URL;?>/" + type + "/add_tasks/",
		type:'GET',
            dataType: "json",
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
            else if (return_data["result"] == 1) {
				 $("#dialog_do_itp").html("任务分配成功");
				  openWin('js_pop_do_success');
				$("#dialog_share").click(function(){
					  $(window.parent.document).find("#js_fenpeirenwu").hide();
					  $(window.parent.document).find("#GTipsCoverjs_fenpeirenwu").remove();
					  $(window.parent.document).find("#search_form").submit();
				  })
			}else{
				$("#dialog_do_itp").html("任务分配失败");
				 openWin('js_pop_do_success');

			}
		}
	});
	}



}



</script>
<script>

 function check_broker(type){
	 var broker_id=$("select[name='run_broker_id']").val();
	 $.ajax({
		url: "<?php echo MLS_URL;?>/"+type+"/check_broker/",
		type: "GET",
		//dataType: "json",
		data:{broker_id: broker_id},
		success:function(data){
			if(data=='true'){
				$("#dialog_do_itp").html("不能分配给自己");
				 openWin('js_pop_do_success');
				 return false;
			}
		}
	 })

 }

</script>
<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'></p>
				  <button type="button" id = 'dialog_share' class="btn-lv1 btn-mid JS_Close" >确定</button>
            </div>
        </div>
    </div>
</div>
