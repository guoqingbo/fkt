<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<div class="pop_box_g" id="js_fenpeirenwu" style="display:block">
    <?php if($num==3){?>
    <input type="hidden" name="task_style" value="3"/>
    <?php }else if($num==4){?>
    <input type="hidden" name="task_style" value="4"/>
    <?php }?>
    <input type="hidden" name="customer_ids" value="<?php echo $customer_ids;?>"/>
    <div class="hd">
        <div class="title">分配任务</div>
        <div class="close_pop"></div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <h3 class="title">跟进对象</h3>
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="w60">客源编号</th>
                        <th class="w70">物业类型</th>
                        <th class="w170">意向区属板块</th>
                        <th class="w170">意向楼盘</th>
                        <th class="w80">户型</th>
                        <th class="w80">面积(㎡)</th>
                        <th width="87"><?php if($type=='buy_customer'){echo '总价';}else{echo '租金';} ?></th>
                    </tr>
                    <?php foreach($customer_list as $k=>$v){?>
                    <tr>
                        <td><?php echo $v['id'];?></td>
                        <td><?php echo !empty($config['property_type'][$v['property_type']]) ? $config['property_type'][$v['property_type']] : '';?></td>
                        <td><?php echo $v['dist_name'];?></td>
                        <td><?php echo $v['cmt_name'];?></td>
                        <td><?php echo $v['room'];?></td>
                        <td><?php echo $v['area'];?></td>
                        <td><?php echo $v['price'];?>
                            <?php if($type=='buy_customer'){
                                echo 'W';
                            }else{
                                echo ('1'==$v['price_danwei'])?'元/㎡*天':'元/月';
                            }?>
                        </td>
                    </tr>
                    <?php }?>
                </table>
            </div>
            <div class="clear">&nbsp;</div>
            <div class="inner inner02">
                <div class="item_fg_h clearfix">
                    <p class="t_text">任务分配人：</p>
                    <p class="i_text"><?php echo $broker_data['agency_name'];?></p>
                    <p class="t_text"><?php echo $broker_data['truename'];?></p>
                </div>
                <!--<div class="item_fg_h clearfix">
                    <p class="t_text">任务执行人：</p>
                    <p class="i_text">
                        <select class="select" onchange="get_broker('customer');" name="agename">
                            <option value="0">请选择</option>
                            <?php foreach($agency_list as $k=>$v){?>
                            <option value="<?php echo $v['agency_id'];?>"><?php echo $v['agency_name'];?></option>
                            <?php }?>
                        </select>
                    </p>
                    <p class="left">&nbsp;&nbsp;&nbsp;</p>
                    <select class="select" id="seid" name="run_broker_id">
                            <option value="0">请选择</option>
                    </select>
                </div>-->

				<div class="item_fg_h clearfix">
                    <p class="t_text">任务执行人：</p>
                    <p class="i_text">
					   <select class="select" name="agename" onchange="get_broker('customer');">
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

                        <label class="label">
                            <input type="hidden" name="radio04" value="3">
                            客源跟进</label>
                    </div>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">执行期限：</p>
                    <input type="text"  class="k_input" onclick="WdatePicker({lang:'zh-cn',minDate:'%y-%M-#{%d}'})" name="pay_date">
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">任务说明：</p>
                    <textarea class="textarea" id="text" placeholder="请填写任务说明不少于五个字" onkeyup="textCounter()"></textarea>
                </div>
				<div class="clearfix"id="span_id" style="padding-left:86px;line-height: normal;">

				</div>
            </div>
           <a class="btn-lv1 btn-mid" style="margin-top:20px; cursor: pointer;" href="javascript:void(0)" onclick="add_tasks('<?php echo $type;?>')">分配任务</a> </div>
    </div>
</div>
<script>
//string对象添加原型属性去除前后空格
String.prototype.trim=function() {
    return this.replace(/(^\s*)|(\s*$)/g,'');
}

 function get_broker(type){
    var agency_id=$("select[name='agename']").val();
    $.ajax({
       url: "<?php echo MLS_URL;?>/"+type+"/ajax_broker_list/",
       type: "GET",
       dataType: "json",
       data:{agency_id: agency_id},
       success:function(data_list){
			var str_html = '<option value="0">请选择</option>';
			if(agency_id>0){
				for(var i=0;i<data_list.length;i++){
					str_html +='<option value='+data_list[i].broker_id+'>'+data_list[i].truename+'</option>';
				}
			}
           $("#seid").empty().html(str_html);
       }
    });
}
function add_tasks(type){
    var _url = '';
    if('rent_customer'==type){
        _url = "<?php echo MLS_URL;?>/rent_customer/add_tasks/"
    }else{
        _url = "<?php echo MLS_URL;?>/customer/add_tasks/"
    }
	var task_type=$("input[name='radio04']").val();//任务类型
	var task_style=$("input[name='task_style']").val();//任务方式
	var customer_id=$("input[name='customer_ids']").val();//客源id
	var run_broker_id=$("select[name='run_broker_id']").val();//执行人id
	var over_date=$("input[name='pay_date']").val();//期限时间
	var content=$("#text").val();//内容
	var add_data={
        'task_type':task_type,
        'task_style':task_style,
        'customer_id':customer_id,
        'run_broker_id':run_broker_id,
        'over_date':over_date,
        'content':content
	};
	if(add_data.run_broker_id==0){
        $("#dialog_do_itp").html("请选择任务执行人");
        $("#dialog_do_itp_src").attr('src','<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png');
        openWin('js_pop_do_success');
    }else if(over_date==''){
		$("#dialog_do_itp").html("执行时间不能为空");
		openWin('js_pop_do_success');
		return false;
	}else if(add_data.content.trim().length<5){
        var text_uid=$("#text").val();
        var text_num=5-text_uid.length;
		$('#span_id').html('<span style="color:red;">您至少还需要输入'+text_num+'个字</span>');
        return false;
    }else{
        $.ajax({
            url: _url,
            type:'GET',
            dataType: "json",
            data:add_data,
            success:function(return_data){
                if (return_data['result'] == 'insert_success') {
                     $("#dialog_do_itp").html("任务分配成功");
                    $("#dialog_do_itp_src").attr('src','<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png');
                     openWin('js_pop_do_success');
					 $("#dialog_share").click(function(){
					  $(window.parent.document).find("#js_fenpeirenwu").hide();
					  $(window.parent.document).find("#GTipsCoverjs_fenpeirenwu").remove();
					  $(window.parent.document).find("#search_form").submit();
				  })
                }else{
                    $("#dialog_do_itp").html("任务分配失败");
                    $("#dialog_do_itp_src").attr('src','<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png');
                    openWin('js_pop_do_success');

                }
            }
        });
    }
}

//检测输入字个数
	function textCounter(){
	var text_uid=$("#text").val();
	var text_num=5-text_uid.length;
	if(text_uid.length<5){
		$('#span_id').html('<span style="color:red;">您至少还需要输入'+text_num+'个字</span>');
	}else{
		$('#span_id').html('');
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
				$("#dialog_do_itp_b").html("不能分配给自己");
				 openWin('js_pop_do_success_b');
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
<div id="js_pop_do_success_b" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp_b'></p>
				  <button type="button" id = 'dialog_share' class="btn-lv1 btn-mid JS_Close" >确定</button>
            </div>
        </div>
    </div>
</div>
