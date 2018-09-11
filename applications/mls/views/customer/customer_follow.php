<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<div class="pop_box_g pop_box_g_border_none" id="js_genjin" style="display:block;border:none;">
    <div class="hd">
        <div class="title">客源写跟进</div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">


            <h3 class="title">客源写跟进<span class="text">(
			<?php if($customer_info){
				echo '求购'.'/'.strip_end_0($customer_info['area_min']).'-'.
					 strip_end_0($customer_info['area_max']).'㎡'.'/'.
					 strip_end_0($customer_info['price_min']).'-'.
					 strip_end_0($customer_info['price_max']).'W';
				if($customer_info['dist_id1']){
					echo '/'.$district[$customer_info['dist_id1']]['district'].'-';
				}
				if($customer_info['street_id1']){
					echo $street[$customer_info['street_id1']]['streetname'].',';
				}
				if($customer_info['dist_id2']){
					echo $district[$customer_info['dist_id2']]['district'].'-';
				}
				if($customer_info['street_id2']){
					echo $street[$customer_info['street_id2']]['streetname'].',';
				}
				if($customer_info['dist_id3']){
					echo $district[$customer_info['dist_id3']]['district'].'-';
				}
				if($customer_info['street_id3']){
					echo $street[$customer_info['street_id3']]['streetname'].',';
				}

			}?>

			)</span></h3>

            <div class="inner">
                <div class="item_fg_h clearfix">
                    <p class="t_text">跟进日期：</p>
                    <p class="i_text"><?php echo $time?></p>
                    <p class="t_text"><b class="red">*</b>跟进方式：</p>
                    <div class="i_text" style="_margin-top:5px;">
                        <label class="label">
                            <input type="radio"value="1" name="radio01">
                            看房</label>
                        <label class="label">
                            <input type="radio" value="3"  name="radio01">
                            电话</label>
                        <label class="label">
                            <input type="radio"  value="4"name="radio01">
                            磋商</label>
                        <label class="label">
                            <input type="radio"  value="5" name="radio01">
                            带看</label>
                        <label class="label">
                            <input type="radio"  value="6" name="radio01">
                            其它</label>
                    </div>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">带看员工：</p>
                    <p class="i_text"><?php echo $broker_name?></p>
                    <div class="left">
                        <p class="t_text">客户类型：</p>
                        <div class="i_text">

                               <?php
							   if($house_type==1){
								   echo ' 求购';
							   }elseif($house_type==2){
								   echo '求租';

							   }
							   ?>

                        </div>
                    </div>

                </div>
				 <input type="hidden" value="<?php echo $customer_id?>" id="customer_id" name="customer_id"/>
				 <input type="hidden" value="<?=$task_id?>" id="task_id" name="task_id"/>
                                 <input type="hidden" value="1" name="status" id="status">
                <div class="item_fg_h clearfix">
                    <p class="t_text"><b class="red">*</b>跟进内容：</p>
                    <textarea class="textarea" id="textid" placeholder="请填写跟进内容不少于十个字" onkeyup="textCounter()"></textarea>
                </div>
				<div class="clearfix"id="span_id" style="padding-left:86px;line-height: normal;">

				</div>
                 <div class="item_fg_h clearfix">
                    <p class="t_label">
                        <label>
                            <input type="checkbox" id="remind" value="0" onclick="displayInfo(this);">
                            提醒</label>
                    </p>
                </div>
                  <div class="inner_in">
                    <div class="in_fg clearfix">
                        <p class="text_t">提醒日期：</p>
						<input class="input_text w135" name="pay_date" id='display1' disabled  onFocus="WdatePicker({lang:'zh-cn',startDate:'%y-%M-%d %H:%m:%s',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true,minDate:'%y-%M-{%d}'})" readonly value="" type="text"/>
                    </div>
                     <div class="in_fg clearfix">
                        <p class="text_t">提醒内容：</p>
                       <textarea class="textarea-1" id="display2" disabled
					    onkeyup="tixing_Counter(5)" ></textarea>
					   <div class="clearfix"id="tixing_id" style="padding-left:86px;line-height: normal;">
				</div>
                    </div>
                </div>
            </div>

            <a class="btn-lv1 btn-mid" style="margin-top:20px;" href="javascript:void(0)" onclick="save(3,2)">确定</a> </div>
    </div>
	</div>
	<script type="text/javascript">
	function save(type,foll_type){
	    var follow_type=$("input[name=radio01]:checked").val();//跟进方式
		var text=$("#textid").val();//跟进内容
		var customer_id=$("#customer_id").val();//客源id
		var task_id=$("#task_id").val();//任务id
                var status=$("#status").val();//任务id
		var type=parseInt(type);
		var text_lenght=text.length;
		var foll_type=parseInt(foll_type);
		var remind=$("#remind").val();
		var ti_time=$("#display1").val();//提醒时间
		var ti_text=$("#display2").val();//提醒内容
		if($("#display2").attr("disabled")){
			var ti_text_lenght=5;
		} else{
			var ti_text_lenght=ti_text.length;
		}
		var addata={
		'follow_type':follow_type,
                'text':text,
		'customer_id':customer_id,
		'task_id':task_id,
                'status':status,
		'type':type,
		'foll_type':foll_type,
		'ti_time':ti_time,
		'ti_text':ti_text
		};
		if(remind==1&&!ti_time){
        	$("#dialog_do_itp").html("提醒时间不能为空");
			openWin('js_pop_do_success');
			return false;
        }else if(remind==1&&!ti_text){
        	$("#dialog_do_itp").html("提醒内容不能为空");
			openWin('js_pop_do_success');
			return false;
        }
		if(follow_type==undefined){
			$("#dialog_do_itp").html("跟进方式不能为空");
		    openWin('js_pop_do_success');
			return false;
		}else if(text==''){
			$("#dialog_do_itp").html("跟进内容不能为空");
		    openWin('js_pop_do_success');
			return false;
		}else if(text_lenght<10){
			$("#dialog_do_itp").html("跟进内容不能少于10个字");
		    openWin('js_pop_do_success');
			return false;
		}else if(ti_text_lenght<5){
			$("#dialog_do_itp").html("提醒内容不能少于5个字");
		    openWin('js_pop_do_success');
			return false;
		}else{
			$.ajax({
		        url:'<?php echo MLS_URL;?>/customer/addfollow',
		        type:'get',
		        data:addata,
		        success:function(data){
			 	if(data =1){
				 	$("#dialog_do_itp").html("添加成功");
				  	openWin('js_pop_do_success');
				   	$("#dialog_share").click(function(){
					  	$(window.parent.document).find("#js_genjin").hide();
					  	$(window.parent.document).find("#GTipsCoverjs_genjin").remove();
					  	$(window.parent.document).find("#search_form").submit();
				  	})
				}else if(data = 2){
					$("#dialog_do_itp").html("添加失败");
				 	openWin('js_pop_do_success');
			 	}
		 	}
		});

		}


	}

	function displayInfo(flag){
        if(flag.checked){
            $("#display1").prop("disabled",false);
            $("#display2").prop("disabled",false);
            $("#remind").val(1);
        }else{
            $("#display1").prop("disabled",true);
            $("#display2").prop("disabled",true);
            $("#remind").val(0);
        }
    }
	//检测输入字个数
	function textCounter(){
	var text_uid=$("#textid").val();
	var text_num=10-text_uid.length;
	if(text_uid.length<10){
		$('#span_id').html('<span style="color:red;">您至少还需要输入'+text_num+'个字</span>');
	}else{
		$('#span_id').html('');
	}

}


//检测输入字个数
	function tixing_Counter(num){
	var text_uid=$("#display2").val();
	var text_num=num-text_uid.length;
	if(text_uid.length<num){
		$('#tixing_id').html('<span style="color:red;">您还需输入'+text_num+'个字</span>');
	}else{
		$('#tixing_id').html('');
	}

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
