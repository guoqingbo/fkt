<!doctype html>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<html style="overflow:auto">

<!--客源详情页-弹层-->
<div class="pop_box_g" id="" style="width:814px; height:538px; display:block;">
    <div class="hd">
        <div class="title">客源跟进</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod clearfix">
        <div class="trail-fl">
			<div class="tab_pop_hd">
				<div class="clearfix">
                    <a class="item <?php if(1==$num){echo 'itemOn';}?> " href="/rent_customer/customer_follow/<?php echo $customer_id;?>/1">跟进明细</a>
					<a class="item <?php if(2==$num){echo 'itemOn';}?> " href="/rent_customer/customer_follow/<?php echo $customer_id;?>/2">带看明细</a>
					<a class="item <?php if(3==$num){echo 'itemOn';}?> " href="/rent_customer/customer_follow/<?php echo $customer_id;?>/3">提醒明细</a>
				</div>
			</div>
			<div class="trail-fl-cont tab_pop_mod clear" id="js_tab_b01">
                <?php
                    //根据数据个数，区分样式
                    $data_num = count($data_lists);
                    $class_str = '';
                ?>
                <?php if(3==$num){?>
                    <?php foreach($data_lists as $k => $v){
                        if($data_num==1){
                            $class_str = 'trail-li1';
                        }else{
                            if($k==0){
                                $class_str = '';
                            }else if($k==$data_num-1){
                                $class_str = 'trail-li3';
                            }else{
                                $class_str = 'trail-li2';
                            }
                        }
                    ?>
                    <div class="trail-li <?php echo $class_str;?>">
                        <h4><?php echo date('Y-m-d',$v['create_time']);?>　<?php echo $v['broker_name'];?></h4>
                        <div class="p-gai">提醒日期：<?php echo date('Y-m-d',$v['notice_time']);?><br>提醒内容：<?php echo $v['contents'];?></div>
                    </div>
                    <?php }?>
                <?php }else{?>
                    <?php foreach($data_lists as $k => $v){
                        if($data_num==1){
                            $class_str = 'trail-li1';
                        }else{
                            if($k==0){
                                $class_str = '';
                            }else if($k==$data_num-1){
                                $class_str = 'trail-li3';
                            }else{
                                $class_str = 'trail-li2';
                            }
                        }
                    ?>
                    <div class="trail-li <?php echo $class_str;?>">
                        <a class="trail-li-a <?php if($v['follow_way']!='12' && $v['follow_way']!='11' && $v['follow_way']!='8' && $v['follow_way']!='7'){ ?> per-add-inf <?php } ?>" href="javascript:void(0);"><?php echo $follow_config[$v['follow_way']];?></a>
                        <h4><?php echo $v['date'];?>　<?php echo $v['broker_name'];?></h4>
                        <?php if($v['follow_way']==5 || $v['follow_way']==4){?>
                        <div class="p-gai p-gai-add">房源：<?php echo 'CZ'.$v['house_id'];?><br>
                        <?php echo $v['text'];?></div>
                        <?php }else{?>
                        <div class="p-gai <?php if($v['follow_way']!='12' && $v['follow_way']!='11' && $v['follow_way']!='8' && $v['follow_way']!='7'){ ?> p-gai-add <?php } ?>"><?php echo $v['text'];?></div>
                        <?php }?>
                    </div>
                    <?php }?>
                <?php }?>
			</div>
		</div>
        <div class="trail-fr">
			<div class="item_fg_h2 clearfix">
				<p class="t_text2"> 提醒日期：</p>
				<div class="i_text2">
					<input class="input_text2 w135" name="pay_date" id="display1" onfocus="WdatePicker({lang:'zh-cn',startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true,minDate:'%y-%M-{%d}'})" readonly="" value="" type="text">
				</div>
			</div>
			<div class="item_fg_h2 clearfix">
				<div class="i_text2">
					<textarea class="textarea2" id="display2" value="" onkeyup="tixing_Counter(5)" onfocus="if(value=='请填写提醒内容'){value='';$(this).css('color','#535353')}" onblur="if(value==''){value='请填写提醒内容';$(this).css('color','#999')}">请填写提醒内容</textarea>
					<p id="tixing_id" style="display:none;"></p>
				</div>
			</div>
			<div class="item_fg_h2 clearfix" style="border-bottom:1px solid #DEDEDE;margin-bottom:0;padding-bottom:10px;">
				<div class="i_text2">
					<a class="btn-lan" onclick="add_remind();" href="javascript:void(0);"><span>仅提交提醒</span></a>
				</div>
			</div>
			<div class="item_fg_h2 clearfix" style="border-top:1px solid #fff;padding-top:10px;">
				<p class="t_text2"><b class="red">*</b> 跟进方式：</p>
				<div class="i_text2" style="padding-top:4px;">
					<i class="label"><input type="radio" value="3" name="radio01" id="tel">电话</i>
                    <i class="label"><input type="radio" value="5" name="radio01" id="daikan">带看</i>
                    <i class="label"><input type="radio" value="4" name="radio01" id="cuoshang">磋商</i>
                    <i class="label"><input type="radio" value="6" name="radio01" id="other">其它</i>
				</div>
			</div>
            <!--客户姓名-->
            <div class="left" id="kename"></div>
            <input type="hidden" value="<?=$customer_id?>" id="customer_id" name="customer_id"/>
            <input type="hidden"  id="house_id">
            <input type="hidden" value="<?=$task_id?>" id="task_id" name="task_id"/>
            <input type="hidden" value="1" id="status" name="status"/>

			<div class="item_fg_h2 clearfix display-daikan">
				<p class="t_text2"><b class="red">*</b> 选择房源： </p>
				<div class="i_text2">
                    <p type="text" class="input_text2" id="kputid" onclick="kputid_input()" style="width:80px;"></p>
                    <!--
					<input type="text" class="input_text2" id="kputid" value="" onclick="kputid_input()">
                    -->
				</div>
			</div>
			<div class="item_fg_h2 clearfix" style="line-height:20px;">
				<p class="t_text2" style="line-height:20px;">跟进人：</p>
				<div class="i_text2"><?php echo $broker_name;?></div>
			</div>
			<div class="item_fg_h2 clearfix">
				<div class="i_text2">
					<textarea class="textarea2" id="textid" value="" onkeyup="textCounter(<?php echo $follow_text_num;?>)" onfocus="if(value=='请填写跟进内容'){value='';$(this).css('color','#535353')}" onblur="if(value==''){value='请填写跟进内容';$(this).css('color','#999')}">请填写跟进内容</textarea>
					<p id="span_id" style="display:none;"></p>
				</div>
			</div>
			<div class="item_fg_h2 clearfix">
				<div class="i_text2">
					<a class="btn-lan" onclick="addsave(2)" href="javascript:void(0);"><span>提交</span></a>
				</div>
			</div>
		</div>
    </div>
</div>
<script>
//仅提交提醒
function add_remind(){
    var ti_time=$("#display1").val();//提醒时间
    var ti_text=$("#display2").val();//提醒内容
    var customer_id=$("#customer_id").val();//客源id

    var addata={
        'ti_time':ti_time,
        'ti_text':ti_text,
        'customer_id':customer_id
    };

    if(ti_time == '')
    {
        $("#dialog_do_itp").html("提醒时间不能为空");
        openWin('js_pop_do_success');
        return false;
    }
    else if(ti_text=='请填写提醒内容')
    {
        $("#dialog_do_itp").html("提醒内容不能为空");
        openWin('js_pop_do_success');
        return false;
    }else if(ti_text.length<5){
        $("#dialog_do_itp").html("提醒内容不能少于5个字");
        openWin('js_pop_do_success');
        return false;
    }else{
        $.ajax({
            url:'/rent_customer/add_remind',
            type:'get',
            data:addata,
            success:function(data){
                if('success' == data){
                    //记录操作的数据，为当前页的第几条，存入cookie
                    var page_id = $(window.parent.document).find('#tr'+customer_id).attr('page_id');
                    SetCookie('page_id',page_id);

                    $("#dialog_do_itp").html("添加成功");
                    openWin('js_pop_do_success');
                    $("#dialog_share").click(function(){
                        $(window.parent.document).find("#js_genjin").hide();
                        $(window.parent.document).find("#GTipsCoverjs_genjin").remove();
                        //详情页隐藏，列表页刷新
                        $(window.parent.document).find("#js_pop_box_g").hide();
                        $(window.parent.document).find("#GTipsCoverjs_pop_box_g").hide();
                        //$(window.parent.document).find("#search_form").submit();
                    })
                }else if('failed' == data){
                    $("#dialog_do_itp").html("添加失败");
                    openWin('js_pop_do_success');
                }
            }
        });
    }

}

function trim(str)
{ //删除左右两端的空格
    return str.replace(/(^\s*)|(\s*$)/g, "");
}

//总提交按钮
function addsave(foll_type)
{
    var follow_type=$("input[name=radio01]:checked").val();//跟进方式
    var follow_text_num = <?php echo $follow_text_num?>;
    var text=trim($("#textid").val());//跟进内容
    var text_lenght=text.length;
    var house_id=$("#house_id").val();//房源id
    var customer_id=$("#customer_id").val();//客源id
    var task_id=$("#task_id").val();//任务id
    var status=$("#status").val();//状态id
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

    //跟进判断
    if(text == '' || text == '请填写跟进内容')
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
    }else if(house_id == '' && (follow_type==4 || follow_type==5)){
        $("#dialog_do_itp").html("请选择房源");
        openWin('js_pop_do_success');
        return false;
    }
    else if(text_lenght<follow_text_num)
    {
        $("#dialog_do_itp").html("跟进内容不能少于"+follow_text_num+"个字");
        openWin('js_pop_do_success');
        return false;
    }
    else
    {
        //提醒判断
        //1.提醒日期和提醒内容都为空，只添加房源跟进
        if(ti_text=='请填写提醒内容' && ti_time==''){
            $.ajax({
                url:'/rent_customer/add_follow_remind',
                type:'get',
                dataType:'json',
                data:addata,
                success:function(data){
                    if('success'==data.result){
                        //记录操作的数据，为当前页的第几条，存入cookie
                        var page_id = $(window.parent.document).find('#tr'+customer_id).attr('page_id');
                        SetCookie('page_id',page_id);

                        $("#dialog_do_itp").html("添加成功");
                        openWin('js_pop_do_success');
                        $("#dialog_share").click(function(){
                            $(window.parent.document).find("#js_genjin").hide();
                            $(window.parent.document).find("#GTipsCoverjs_genjin").remove();
                            //详情页隐藏，列表页刷新
                            $(window.parent.document).find("#js_pop_box_g").hide();
                            $(window.parent.document).find("#GTipsCoverjs_pop_box_g").hide();
                            //$(window.parent.document).find("#search_form").submit();
                        })
                    }else{
                        $("#dialog_do_itp").html("添加失败");
                        openWin('js_pop_do_success');
                    }
                }
            });
        }else{
        //2.同时添加提醒和房源跟进。提醒日期和提醒内容必填
            if(ti_time == '')
            {
                $("#dialog_do_itp").html("提醒时间不能为空");
                openWin('js_pop_do_success');
                return false;
            }
            else if(ti_text=='请填写提醒内容')
            {
                $("#dialog_do_itp").html("提醒内容不能为空");
                openWin('js_pop_do_success');
                return false;
            }else if(ti_text.length<5){
                $("#dialog_do_itp").html("跟进内容不能少于5个字");
                openWin('js_pop_do_success');
                return false;
            }else{
                $.ajax({
                    url:'/rent_customer/add_follow_remind',
                    type:'get',
                    dataType:'json',
                    data:addata,
                    success:function(data){
                        if('success' == data.result){
                            //记录操作的数据，为当前页的第几条，存入cookie
                            var page_id = $(window.parent.document).find('#tr'+customer_id).attr('page_id');
                            SetCookie('page_id',page_id);

                            $("#dialog_do_itp").html("添加成功");
                            openWin('js_pop_do_success');
                            $("#dialog_share").click(function(){
                                $(window.parent.document).find("#js_genjin").hide();
                                $(window.parent.document).find("#GTipsCoverjs_genjin").remove();
                                //详情页隐藏，列表页刷新
                                $(window.parent.document).find("#js_pop_box_g").hide();
                                $(window.parent.document).find("#GTipsCoverjs_pop_box_g").hide();
                                //$(window.parent.document).find("#search_form").submit();
                            })
                        }else{
                            $("#dialog_do_itp").html("添加失败");
                            openWin('js_pop_do_success');
                        }
                    }
                });
            }
        }
    }
}

//查看个人的客源
function kputid_input()
{
    //$("#kputid").hide();

    var _url = '/rent_customer/source';
    if(_url)
    {
        $("#js_keyuan .iframePop").attr("src",_url);
    }

    openWin('js_keyuan');
}

	//检测输入字个数
function textCounter(num){
    var text_uid=$("#textid").val();
    var text_num=num-text_uid.length;
    if(text_uid.length<num){
        $('#span_id').show().html('<span style="color:red;">您还需输入'+text_num+'个字</span>');
    }else{
        $('#span_id').hide().html('');
    }
}

//检测输入字个数
function tixing_Counter(num)
{
	var text_uid = $("#display2").val();
	var text_num = num-text_uid.length;
	if(text_uid.length<num)
    {
		$('#tixing_id').show().html('<span style="color:red;">您还需输入'+text_num+'个字</span>');
	}
    else
    {
		$('#tixing_id').hide().html('');
	}
}

$(function(){
	$("#cuoshang,#daikan").change(function(){
		$('.display-daikan').show();
	});
	$("#tel,#see,#other").change(function(){
		$('.display-daikan').hide();
	})
})

</script>

</body>
</html>

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
