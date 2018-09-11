<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<div class="pop_box_g" id="js_genjin" style="display:block;border:none;">
    <div class="hd">
        <div class="title">房源写跟进</div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <h3 class="title">房源写跟进<span class="text">(<?php echo $house_info['block_name'].' '.$house_info['district_name'].'-'.$house_info['street_name'].' '.'/'.strip_end_0($house_info['buildarea']).'㎡'.'/'.strip_end_0($house_info['price']).'W'.'/'.$house_info['room'].'室'.$house_info['hall'].'厅'.$house_info['toilet'].'卫/'.$config['forward'][$house_info['forward']].'/'.$config['fitment'][$house_info['fitment']].'/'.$house_info['buildyear'];?>)</span></h3>

            <div class="inner inner02">
                <div class="item_fg_h clearfix">
                    <p class="t_text">跟进日期：</p>
                    <p class="i_text"><?php echo $time?></p>
                    <p class="t_text"><b class="red">*</b> 跟进方式：</p>
                   <div class="i_text" style="_padding-top:6px;">
                        <i class="label">
                            <input type="radio"value="1" name="radio01" id="see" >
                            看房</i>
                        <i class="label">
                            <input type="radio" value="3"  name="radio01" id="tel">
                            电话</i>
                        <i class="label">
                            <input type="radio"  value="4"name="radio01"  id="kanfang">
                            磋商</i>
                        <i class="label">
                            <input type="radio"  value="5" name="radio01" id="cuoshang">
                            带看</i>
                        <i class="label">
                            <input type="radio"  value="6" name="radio01" id="other">
                            其它</i>
                    </div>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">带看员工：</p>
                    <p class="i_text"><?php echo $broker_name?></p>
                    <div class="left">
                        <p class="t_text" id="t_text_type"></p>
                        <div class="i_text" id="house_type">
                        </div>
                    </div>
					<!--客户姓名-->
                    <div class="left" id="kename"></div>
                </div>
                    <input type="hidden" value="<?=$house_id?>" id="house_id" name="house_id"/>
                    <input type="hidden" value="<?=$task_id?>" id="task_id" name="task_id"/>
                    <input type="hidden" value="1" name="status" id="status">
                <div class="item_fg_h clearfix">
                    <p class="t_text"><b class="red">*</b> 跟进内容：</p>
                    <textarea class="textarea" id="textid" placeholder="请填写跟进内容不少于十个字" onkeyup="textCounter(10)"></textarea>
                </div>
				<div class="clearfix"id="span_id" style="padding-left:86px;line-height: normal;">
				</div>
                <div class="item_fg_h clearfix">
                    <p class="t_label">
                        <label><input type="checkbox" id="remind" value="0" onclick="displayInfo(this);">提醒</label>
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
            <a href="javascript:void(0)" class="btn-lv1 btn-mid" style="margin-top:20px;" onclick="addsave(1)">确定</a> </div>
    </div>
	</div>
	<script type="text/javascript">
	function addsave(foll_type)
    {
	    var follow_type=$("input[name=radio01]:checked").val();//跟进方式
		var text=$("#textid").val();//跟进内容
		var text_lenght=text.length;
		var customer_id=$("#cn_id").val();//客户id
		var house_id=$("#house_id").val();//房源id
		var task_id=$("#task_id").val();//任务id
                var status=$("#status").val();//已完成的status  1
		var remind=$("#remind").val();
		var ti_time=$("#display1").val();//提醒时间
		var ti_text=$("#display2").val();//提醒内容：
		var foll_type=parseInt(foll_type);
                if($("#display2").attr("disabled")){
                    var ti_text_lenght=5;
                } else{
                    var ti_text_lenght=ti_text.length;
                }
		var addata={
                        'follow_type':follow_type,
                        'text':text,
                        'customer_id':customer_id,
                        'house_id':house_id,
                        'task_id':task_id,
                        'status':status,
                        'foll_type':foll_type,
			'ti_time':ti_time,
			'ti_text':ti_text
		};
        if(remind&&!ti_time){
        	$("#dialog_do_itp").html("提醒时间不能为空");
			openWin('js_pop_do_success');
			return false;
        }else if(remind&&!ti_text){
        	$("#dialog_do_itp").html("提醒内容不能为空");
			openWin('js_pop_do_success');
			return false;
        }

	if(text == '')
        {
	   $("#dialog_do_itp").html("跟进内容不能为空");
	   openWin('js_pop_do_success');
			return false;
	}
        else if(follow_type == undefined)
        {
			$("#dialog_do_itp").html("跟进方式不能为空");
			openWin('js_pop_do_success');
			return false;
		}
        else if(text_lenght<10)
        {
            $("#dialog_do_itp").html("跟进内容不能少于10个字");
            openWin('js_pop_do_success');
            return false;
        }
        else if(ti_text_lenght<5)
        {
            $("#dialog_do_itp").html("提醒内容不能少于5个字");
            openWin('js_pop_do_success');
            return false;
        }
        else
        {
	    $.ajax({
                url:'/sell/addfollow',
                type:'get',
                data:addata,
                success:function(data){
                    console.log(data);
                    alert('aaaaa');
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

	//查看个人的客源
	function kputid_input()
    {
		$("#kputid").hide();

		var _url = '/sell/source';
		if(_url)
		{
			$("#js_keyuan .iframePop").attr("src",_url);
		}

		openWin('js_keyuan');
	}

    $(function(){
		$(".JS_Close").click(function(){$("#kputid").show();});

		$("#kanfang,#cuoshang").change(function(){
			var str='<p class="t_text">客户姓名: </p>';
			str +=' <input type="text" class="k_input" id="kputid" value="" onclick="kputid_input()">';
			$("#t_text_type").html('客户类型:');
			$("#house_type").html('求购');
			$("#kename").html(str);
		});

		$("#tel,#see,#other").change(function(){
            $("#kename").empty();
            $("#house_type").html('');
            $("#t_text_type").html('');
		})
	});

    function displayInfo(flag){
        if(flag.checked){
            $("#display1").attr("disabled",false);
            $("#display2").attr("disabled",false);
            $("#remind").val(1);
        }else{
            $("#display1").attr("disabled",true);
            $("#display2").attr("disabled",true);
            $("#remind").val(0);
        }
    }
	//检测输入字个数
	function textCounter(num){
	var text_uid=$("#textid").val();
	var text_num=num-text_uid.length;
	if(text_uid.length<num){
		$('#span_id').html('<span style="color:red;">您还需输入'+text_num+'个字</span>');
	}else{
		$('#span_id').html('');
	}
}

//检测输入字个数
function tixing_Counter(num)
{
	var text_uid = $("#display2").val();
	var text_num = num-text_uid.length;
	if(text_uid.length<num)
    {
		$('#tixing_id').html('<span style="color:red;">您还需输入'+text_num+'个字</span>');
	}
    else
    {
		$('#tixing_id').html('');
	}
}
</script>
<!--客户信息弹框-->
<div id="js_keyuan" class="iframePopBox" style=" width:505px; height:345px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
   <iframe frameborder="0" scrolling="no" width="505" height="345" class='iframePop' src=""></iframe>
</div>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'></p>
	    <button type="button" id = 'dialog_share' class="btn-lv1 btn-mid JS_Close">确定</button>
            </div>
        </div>
    </div>
</div>
