<!doctype html>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<html style="overflow:auto">
<head>
    <meta charset="utf-8">
    <title>房源跟进-弹层</title>
</head>

<body>

<!--房源跟进-弹层-->
<div class="pop_box_g" id="" style="width:816px; height:610px; display:block;">
    <div class="hd">
        <div class="title">房源跟进</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod clearfix">
        <div class="trail-fl">
			<div class="tab_pop_hd">
				<div class="clearfix">
                    <a class="item <?php if(1==$num){echo 'itemOn';}?> " href="/rent/house_follow/<?php echo $house_id;?>/1">跟进明细</a>
					<a class="item <?php if(2==$num){echo 'itemOn';}?> " href="/rent/house_follow/<?php echo $house_id;?>/2">带看明细</a>
					<a class="item <?php if(3==$num){echo 'itemOn';}?> " href="/rent/house_follow/<?php echo $house_id;?>/3">提醒明细</a>
				</div>
			</div>
			<div class="trail-fl-cont tab_pop_mod">
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
                        <div class="p-gai p-gai-add">带看客户：<?php echo $v['customer_name'];?><br>
                        <?php echo $v['text'];?></div>
                        <?php }else if($v['follow_way']==19){?>
                            <div class="p-gai p-gai-add">
                                商谈经纪人：<?php echo $v['broker_f_name'];?><br>
                                商谈客户：<?php echo $v['customer_f_name'];?><br>
                                封盘时间：<?php echo $v['seal_start_date'].'--'.$v['seal_end_date'];?><br>
                                <?php echo $v['text'];?>
                            </div>
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
			<div class="item_fg_h2 clearfix" style="border-bottom:1px solid #DEDEDE;margin-bottom:0;padding:5px 0 10px;">
				<div class="i_text2">
					<a class="btn-lan" onclick="add_remind();" href="javascript:void(0);"><span>仅提交提醒</span></a>
				</div>
			</div>
			<div class="item_fg_h2 clearfix" style="border-top:1px solid #fff;padding-top:10px;">
				<p class="t_text2"><b class="red">*</b> 跟进方式：</p>
				<div class="i_text2" style="padding-top:4px;">
                    <i class="label"><input type="radio" value="3" name="radio01" id="tel">电话</i><i class="label"><input type="radio" value="1" name="radio01" id="see">勘房</i><i class="label"><input type="radio" value="5" name="radio01" id="daikan">带看</i><i class="label"><input type="radio" value="4" name="radio01" id="cuoshang">磋商</i><i class="label"><input type="radio" value="6" name="radio01" id="other">其它</i>
                    <?php if($role_level < 7 ){ ?>
                        <?php if('1'==$is_seal){ ?>
                        <i class="label"><input type="radio" value="20" name="radio01">解封</i>
                        <?php }else{ ?>
                        <i class="label"><input type="radio" value="19" name="radio01" id="fengpan">封盘</i>
                        <?php } ?>
                    <?php } ?>
                </div>
			</div>
            <!--客户姓名-->
            <div class="left" id="kename"></div>
            <input type="hidden" value="<?=$house_id?>" id="house_id" name="house_id"/>
            <input type="hidden" value="<?=$task_id?>" id="task_id" name="task_id"/>
            <input type="hidden"  id="cn_id">
            <input type="hidden"  id="status" name="status" value="1">
			<div class="item_fg_h2 clearfix display-daikan">
				<p class="t_text2"><b class="red">*</b> 带看客户： </p>
				<div class="i_text2">
                    <p id="kputid" class="input_text2" value="" onclick="kputid_input(1)" style="width:80px;"></p>
				</div>
			</div>
			<div class="item_fg_h2 clearfix" style="line-height:20px;">
				<p class="t_text2" style="line-height:20px;">跟进人：</p>
				<div class="i_text2"><?php echo $broker_name;?></div>
			</div>
            <input type="hidden"  id="broker_id_f">
			<div class="item_fg_h2 clearfix display-fengpan">
				<p class="t_text2"><b class="red">*</b> 商谈经纪人： </p>
				<div class="i_text2">
                    <p id="kputid_broker" class="input_text2" value="" onclick="broker_id_f_input()" style="width:80px;"></p>
				</div>
			</div>
            <input type="hidden"  id="cn_id_f">
			<div class="item_fg_h2 clearfix display-fengpan">
				<p class="t_text2"><b class="red">*</b> 商谈客户： </p>
				<div class="i_text2">
                    <p id="kputid_2" class="input_text2" value="" onclick="kputid_input(2)" style="width:80px;"></p>
				</div>
			</div>
			<div class="item_fg_h2 clearfix display-fengpan">
				<p class="t_text2"><b class="red">*</b> 封盘时间： </p>
				<div class="i_text2">
					当前时间&nbsp;&nbsp;——&nbsp;&nbsp;<input class="input_text2 w135" name="seal_end_time" id="seal_end_time" onfocus="WdatePicker({lang:'zh-cn',startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true,minDate:'%y-%M-{%d}'})" readonly="" value="" type="text">
				</div>
			</div>
			<div class="item_fg_h2 clearfix">
				<div class="i_text2">
					<textarea class="textarea2" id="textid" value="" onkeyup="textCounter(<?php echo $follow_text_num;?>)" onfocus="if(value=='请填写跟进内容'){value='';$(this).css('color','#535353')}" onblur="if(value==''){value='请填写跟进内容';$(this).css('color','#999')}">请填写跟进内容</textarea>
					<p id="span_id" style="display:none;"></p>
				</div>
			</div>
			<?php
			if($key_number){
			?>
			<div class="item_fg_h2 clearfix" style="line-height:20px;">
				<p class="t_text2" style="line-height:20px;">钥匙编号：</p>
				<div class="i_text2">
					<?=$key_number?>
					<?php
					if($key_status == 1){
					?>
					<a href="javascript:void(0)" onclick="add_key_log(<?=$key_id?>,1,'borrow_key')">借钥匙 </a>
					<span class="fg">|</span>
					<a href="javascript:void(0)" onclick="add_key_log(<?=$key_id?>,3,'also_owner')">还业主</a>
					<?php
					}elseif($key_status == 2){
					?>
					<a href="javascript:void(0)" onclick="add_key_log(<?=$key_id?>,2,'also_key')">还钥匙</a>
					<?php
					}
					?>
				</div>
			</div>
			<?php
			}
			?>
			<div class="item_fg_h2 clearfix">
				<div class="i_text2">
					<a class="btn-lan" onclick="addsave(1)" href="javascript:void(0);"><span>提交</span></a>
				</div>
			</div>
		</div>
    </div>
</div>

<iframe name="yaoshi_submit_iframe" id="yaoshi_submit_iframe" style="display:none"></iframe>
<div class="pop_box_g pop_box_g_big pop_box_g_big03 pop_box_g_big04" id="js_ys_jieyaoshi">
    <form action="" id="jieyaoshi_form" method="post" target="yaoshi_submit_iframe">
    <input type="hidden" class="key_id" name="key_id" value="" />
    <input type="hidden" class="act" name="act" value="1" />
    <div class="hd">
        <div class="title">钥匙借出登记</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">

        <table class="table">
			<tr>
				<td class="td_l" valign="top">借用方：</td>
				<td class="td_r" valign="top">
					<input type="radio" name="company_status" value='1' checked onclick="tab_click(1);" id="company">本公司&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="company_status" value='2' onclick="tab_click(2);" id="company">其他公司

				</td>
			</tr>
            <tr id="self_store">
                <td class="td_l" valign="top">借用门店：</td>
                <td class="td_r" valign="top">
                    <select class="select w110 agency_id" name="agency_id" rel="broker_id_1" id="agency">
                        <option value="">请选择门店</option>
                        <?php foreach($agencys as $k => $v){?>
                        <option value="<?php echo $v['agency_id'];?>"><?php echo $v['agency_name'];?></option>
                        <?php }?>
                    </select>
                    <span class="error_agency1"></span>
                </td>
            </tr>
			<tr id="self_staff">
                <td class="td_l" valign="top">借用员工：</td>
                <td class="td_r" valign="top">
                    <select class="select w110  broker_id broker_id_1" name="broker_id" id="broker">
                        <option value="">请选择员工</option>
                    </select>
                    <span class="error_broker1"></span>
                </td>
            </tr>
			<tr id="other_name" style="display:none">
                <td class="td_l" valign="top">姓名：</td>
                <td class="td_r" valign="top">
                    <input type="text" name="borrow_person" id="borrow_person" class="null"/>
                    <span class="error_person"></span>
                </td>
            </tr>
			<tr id="other_tel" style="display:none">
                <td class="td_l" valign="top">联系方式：</td>
                <td class="td_r" valign="top">
                    <input type="text" name="borrow_telephone" id="borrow_telephone"  class="null"/>
                    <span class="error_telephone"></span>
                </td>
            </tr>
			<tr id="other_company" style="display:none">
                <td class="td_l" valign="top">所属公司：</td>
                <td class="td_r" valign="top">
                    <input type="text" name="borrow_company" id="borrow_company"  class="null"/>
                    <span class="error_company"></span>
                </td>
            </tr>

            <tr>
                <td class="td_l" valign="top">借用时间：</td>
                <td class="td_r" valign="top">
                    <input type="text" class="text_input w160 time_bg" name="time" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})" readonly id="time">
                    <span class="error_time1"></span>
                </td>
            </tr>
            <tr>
                <td class="td_l" valign="top">备注：</td>
                <td class="td_r" valign="top">
                    <textarea class="textarea" name="reason" style="height:100px;" id="reason"></textarea>
                    <span class="error_reason"></span>
                </td>
            </tr>
        </table>
        <input type="submit" class="btn-lv1 btn-mid sure" value="确定" style="margin-top:5px;" >

    </div>
    </form>
</div>



<iframe name="yaoshi_submit_iframe" id="yaoshi_submit_iframe" style="display:none"></iframe>
<div class="pop_box_g pop_box_g_big pop_box_g_big03" id="js_ys_huanyaoshi" style="height:268px;">
    <form action="" id="huanyaoshi_form" method="post"  target="yaoshi_submit_iframe">
    <input type="hidden" class="key_id" name="key_id" value="" />
    <input type="hidden" class="act" name="act" value="2" />
    <div class="hd">
        <div class="title">钥匙归还</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">

        <table class="table">
            <tr>
                <td class="td_l">接收门店：</td>
                <td class="td_r">
                    <select class="select w120 agency_id" name="agency_id" rel="broker_id_2" id="agency_huan">
                        <option value="">请选择门店</option>
                        <?php foreach($agencys as $k => $v){?>
                        <option value="<?php echo $v['agency_id'];?>"><?php echo $v['agency_name'];?></option>
                        <?php }?>
                    </select>
                    <span class="error_agency2"></span>
                </td>
            </tr>
            <tr>
                <td class="td_l">接收员工：</td>
                <td class="td_r">
                    <select  class="select w160  broker_id broker_id_2" name="broker_id" id="broker_huan">
                        <option value="">请选择员工</option>
                    </select>
                    <span class="error_broker2"></span>
                </td>
            </tr>
            <tr>
                <td class="td_l">归还时间：</td>
                <td class="td_r">
                    <input type="text" class="text_input w160 time_bg" name="time" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})" id="time_huan">
                    <span class="error_time2"></span>
                </td>
            </tr>
        </table>

        <input type="submit" class="btn-lv1 btn-mid" value="确定" style="margin-top:10px;">

    </div>
    </form>
</div>




<iframe name="yaoshi_submit_iframe" id="yaoshi_submit_iframe" style="display:none"></iframe>
<div class="pop_box_g pop_box_g_big pop_box_g_big03" id="js_ys_huanyezhu" style="height:268px;">
    <form action="" id="huanyezhu_form" method="post" target="yaoshi_submit_iframe">
    <input type="hidden" class="key_id" name="key_id" value="" />
    <input type="hidden" class="act" name="act" value="3" />
    <div class="hd">
        <div class="title">还业主</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">

        <table class="table">
            <tr>
                <td class="td_l">门店：</td>
                <td class="td_r">
                    <select class="select w160 agency_id" name="agency_id" rel="broker_id_3" id="agency3">
                        <option value="">请选择门店</option>
                        <?php foreach($agencys as $k => $v){?>
                        <option value="<?php echo $v['agency_id'];?>"><?php echo $v['agency_name'];?></option>
                        <?php }?>
                    </select>
                    <span class="error_agency3"></span>
                </td>
            </tr>
            <tr>
                <td class="td_l">员工：</td>
                <td class="td_r">
                    <select  class="select w160 broker_id broker_id_3" name="broker_id" id="broker3">
                        <option value="">请选择员工</option>
                    </select>
                    <span class="error_broker3"></span>
                </td>
            </tr>
            <tr>
                <td class="td_l">归还时间：</td>
                <td class="td_r">
                    <input type="text" class="text_input w160 time_bg" name="time" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})" id="time3">
                    <span class="error_time3"></span>
                </td>
            </tr>
        </table>

        <input type="submit" class="btn-lv1 btn-mid" value="确定" style="margin-top:5px;">

    </div>
    </form>
</div>

<!-- 确认操作成功+提示 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg1">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="/rent/house_follow/<?=$house_id?>/<?=$num?>"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img class="img_msg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                    <span id="dialog_msg" class="span_msg">操作成功！</span>
                </p>
            </div>
        </div>
    </div>
</div>
<!-- 操作失败+提示 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg2">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="/rent/house_follow/<?=$house_id?>/<?=$num?>"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">

                <p class="text"><img class="img_msg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                    <span id="dialog_msg" class="span_msg">操作失败！</span>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
//仅提交提醒
function add_remind(){
    var ti_time=$("#display1").val();//提醒时间
    var ti_text=$("#display2").val();//提醒内容
    var house_id=$("#house_id").val();//房源id
    var task_id=$("#task_id").val();//房源id
    var status=$("#status").val();//房源id
    var addata={
        'ti_time':ti_time,
        'ti_text':ti_text,
        'house_id':house_id,
        'status':status,
        'task_id':task_id
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
            url:'/rent/add_remind',
            type:'get',
            data:addata,
            success:function(data){
                if('success' == data){
                    //记录操作的数据，为当前页的第几条，存入cookie
                    var page_id = $(window.parent.document).find('#tr'+house_id).attr('page_id');
                    SetCookie('page_id',page_id);

                    $("#dialog_do_itp").html("添加成功");
                    openWin('js_pop_do_success');
                    $("#dialog_share").click(function(){
                        $(window.parent.document).find("#js_genjin").hide();
                        $(window.parent.document).find("#GTipsCoverjs_genjin").remove();

                        //跟进添加成功，所有遮罩去除
                        window.parent.hide_noneClick();
                        //详情关闭、最小化按钮恢复点击
                        $(window.parent.document).find("#window_min_close_2").attr('id','window_min_close');
                        $(window.parent.document).find("#window_min_click_2").removeAttr('id','window_min_click');
                        $(window.parent.document).find("#window_min_click").attr('class','JS_Close close_pop iconfont');
                        $(window.parent.document).find("#window_min_close").attr('class','JS_Close close_pop iconfont');
                        //查找父级的iframe中子元素
                       $(window.parent.document).find("#detialIframe").contents().find(".mask_bg").css("display","none");
                       $(window.parent.document).find("#detialIframe").contents().find("#rent_house_match").attr('class','btn-lan');
                       $(window.parent.document).find("#detialIframe").contents().find("#rent_house_share_tasks").attr('class','btn-lan');
                       $(window.parent.document).find("#detialIframe").contents().find("#rent_allocate_house").attr('class','btn-lan');
                       $(window.parent.document).find("#detialIframe").contents().find("#rent_zhuxiao").show();
                       $(window.parent.document).find("#detialIframe").contents().find("#rent_bianji").show();
                        //详情页隐藏，列表页刷新
                       $(window.parent.document).find("#js_pop_box_g").hide();
                       $(window.parent.document).find("#search_form").submit();
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
    var customer_id=$("#cn_id").val();//客户id
    var customer_id_f=$("#cn_id_f").val();//商谈客户id
    var broker_id_f=$("#broker_id_f").val();//商谈经纪人id
    var house_id=$("#house_id").val();//房源id
    var task_id=$("#task_id").val();//任务id
    var ti_time=$("#display1").val();//提醒时间
    var seal_end_time=$("#seal_end_time").val();//封盘结束时间
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
        'customer_id_f':customer_id_f,
        'broker_id_f':broker_id_f,
        'seal_end_time':seal_end_time,
        'house_id':house_id,
        'task_id':task_id,
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
    }else if(customer_id == '' && (follow_type==4 || follow_type==5)){
        $("#dialog_do_itp").html("请选择客户");
        openWin('js_pop_do_success');
        return false;
    }
    else if(text_lenght<follow_text_num)
    {
        $("#dialog_do_itp").html("跟进内容不能少于"+follow_text_num+"个字");
        openWin('js_pop_do_success');
        return false;
    }
    else if(follow_type==19 && (''==customer_id_f || ''==broker_id_f || ''==seal_end_time))
    {
        if(''==broker_id_f){
            $("#dialog_do_itp").html("商谈经纪人不能为空");
        }else if(''==customer_id_f){
            $("#dialog_do_itp").html("商谈客户不能为空");
        }else if(''==seal_end_time){
            $("#dialog_do_itp").html("封盘时间不能为空");
        }
        openWin('js_pop_do_success');
        return false;
    }
    else
    {
        //提醒判断
        //1.提醒日期和提醒内容都为空，只添加房源跟进
        if(ti_text=='请填写提醒内容' && ti_time==''){
            $.ajax({
                url:'/rent/add_follow_remind',
                type:'get',
                dataType:'json',
                data:addata,
                success:function(data){
                    if('success' == data.result){
                        //记录操作的数据，为当前页的第几条，存入cookie
                        var page_id = $(window.parent.document).find('#tr'+house_id).attr('page_id');
                        SetCookie('page_id',page_id);

                        $("#dialog_do_itp").html("添加成功");
                        openWin('js_pop_do_success');
                        $("#dialog_share").click(function(){
                            $(window.parent.document).find("#js_genjin").hide();
                            $(window.parent.document).find("#GTipsCoverjs_genjin").remove();
                            $(window.parent.document).find("#GTipsCoverjs_pop_box_g").remove();

                            //跟进添加成功，所有遮罩去除
                            window.parent.hide_noneClick();
                            //详情关闭、最小化按钮恢复点击
                            $(window.parent.document).find("#window_min_close_2").attr('id','window_min_close');
                            $(window.parent.document).find("#window_min_click_2").removeAttr('id','window_min_click');
                            $(window.parent.document).find("#window_min_click").attr('class','JS_Close close_pop iconfont');
                            $(window.parent.document).find("#window_min_close").attr('class','JS_Close close_pop iconfont');
                            //查找父级的iframe中子元素
						   $(window.parent.document).find("#detialIframe").contents().find(".mask_bg").css("display","none");
						   $(window.parent.document).find("#detialIframe").contents().find("#rent_house_match").attr('class','btn-lan');
						   $(window.parent.document).find("#detialIframe").contents().find("#rent_house_share_tasks").attr('class','btn-lan');
						   $(window.parent.document).find("#detialIframe").contents().find("#rent_allocate_house").attr('class','btn-lan');
						   $(window.parent.document).find("#detialIframe").contents().find("#rent_zhuxiao").show();
						   $(window.parent.document).find("#detialIframe").contents().find("#rent_bianji").show();
                            //详情页隐藏，列表页刷新
                           $(window.parent.document).find("#js_pop_box_g").hide();
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
                    url:'/rent/add_follow_remind',
                    type:'get',
                    dataType:'json',
                    data:addata,
                    success:function(data){
                        if('success' == data.result){
                            //记录操作的数据，为当前页的第几条，存入cookie
                            var page_id = $(window.parent.document).find('#tr'+house_id).attr('page_id');
                            SetCookie('page_id',page_id);

                            $("#dialog_do_itp").html("添加成功");
                            openWin('js_pop_do_success');
                            $("#dialog_share").click(function(){
                                $(window.parent.document).find("#js_genjin").hide();
                                $(window.parent.document).find("#GTipsCoverjs_genjin").remove();
                                $(window.parent.document).find("#GTipsCoverjs_pop_box_g").remove();

                                //跟进添加成功，所有遮罩去除
                                window.parent.hide_noneClick();
                                //详情关闭、最小化按钮恢复点击
                                $(window.parent.document).find("#window_min_close_2").attr('id','window_min_close');
                                $(window.parent.document).find("#window_min_click_2").removeAttr('id','window_min_click');
                                $(window.parent.document).find("#window_min_click").attr('class','JS_Close close_pop iconfont');
                                $(window.parent.document).find("#window_min_close").attr('class','JS_Close close_pop iconfont');
                                //查找父级的iframe中子元素
                               $(window.parent.document).find("#detialIframe").contents().find(".mask_bg").css("display","none");
                               $(window.parent.document).find("#detialIframe").contents().find("#rent_house_match").attr('class','btn-lan');
                               $(window.parent.document).find("#detialIframe").contents().find("#rent_house_share_tasks").attr('class','btn-lan');
                               $(window.parent.document).find("#detialIframe").contents().find("#rent_allocate_house").attr('class','btn-lan');
                               $(window.parent.document).find("#detialIframe").contents().find("#rent_zhuxiao").show();
                               $(window.parent.document).find("#detialIframe").contents().find("#rent_bianji").show();
                                //详情页隐藏，列表页刷新
                               $(window.parent.document).find("#js_pop_box_g").hide();
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
function kputid_input(type)
{
    var _url = '/rent/source/'+type;
    if(_url)
    {
        $("#js_keyuan .iframePop").attr("src",_url);
    }

    openWin('js_keyuan');
}

//查看商谈经纪人
function broker_id_f_input()
{
    var _url = '/rent/broker_source';
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
    //磋商 带看
	$("#cuoshang,#daikan").change(function(){
		$('.display-daikan').show();
	});
	$("#tel,#see,#other,#fengpan").change(function(){
		$('.display-daikan').hide();
	})
    //封盘
	$("#fengpan").change(function(){
		$('.display-fengpan').show();
	});
	$("#tel,#see,#other,#daikan,#cuoshang").change(function(){
		$('.display-fengpan').hide();
	})

	$('.agency_id').change(function(){
        var agencyId = $(this).val();
        var broker_class = $(this).attr("rel");
        $.ajax({
            type: 'get',
            url : '/my_task/get_broker_ajax/'+agencyId,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg===''){
                    str = '<option value="">请选择员工</option>';
					return false;
                }else{
                    str = '<option value="">请选择员工</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].broker_id+'">'+msg[i].truename+'</option>';
                    }
                }
                $('.'+broker_class).html(str);
            }
        });
    });

})

function tab_click(type){
	if(type==1){
		$("#self_store,#self_staff").show();
		$("#other_name,#other_tel,#other_company").hide();
	}else{
		$("#self_store,#self_staff").hide();
		$("#other_name,#other_tel,#other_company").show();
	}
}

//打开详情弹层
function add_key_log(key_id,act,type)
{

    var _id;
    if(act == 1){
        _id = "js_ys_jieyaoshi";
    }else if(act == 2){
        _id = "js_ys_huanyaoshi";
    }else if(act == 3){
        _id = "js_ys_huanyezhu";
    }

    $("#"+_id+" .key_id").val(key_id);

	$('#'+_id+' .null').val('');
	$('#'+_id+' #time').val('');
	$('#'+_id+' select').each(function(){
		$(this).children('option').first().attr('selected','selected');
	});
	$('#'+_id+' textarea').val('');
	$('#'+_id+' span').html('');

    openWin(_id);
	$("form").submit(function(){
		var company=$("input[name='company_status']:checked").val();
		//alert(company);
		var agency=$('#agency').val();
		var agency_huan=$('#agency_huan').val();
		//alert(agency_huan);
		var agency3=$('#agency3').val();
		var broker=$('#broker').val();
		var broker_huan=$('#broker_huan').val();
		var broker3=$('#broker3').val();
		var time =$('#time').val();
		var time_huan =$('#time_huan').val();
		var time3 =$('#time3').val();
		var reason=$("#reason").val();
		var borrow_person=$("#borrow_person").val();
		var borrow_telephone=$("#borrow_telephone").val();
		var borrow_company=$("#borrow_company").val();
		//借钥匙
		if(_id == "js_ys_jieyaoshi"){
			if(company == 1){
				//alert(agency);
				if(agency == ''){
					$('.error_agency1').html("<font color='red'>请填写借用门店</font>");
					return false;
				}else{
					$('.error_agency1').html('');
				}
				if(broker == ''){
					$('.error_broker1').html("<font color='red'>请填写借用员工</font>");
					return false;
				}else{
					$('.error_broker1').html('');
				}
				if(time == ''){
					$('.error_time1').html("<font color='red'>请填写借用时间</font>");
					return false;
				}else{
					$('.error_time1').html('');
				}
				if(reason == ''){
					$('.error_reason').html("<font color='red'>请填写借用原因</font>");
					return false;
				}else{
					$('.error_reason').html('');
				}
			}else if(company == 2){
				if(borrow_person == ''){
					$('.error_person').html("<font color='red'>请填写借钥匙人姓名</font>");
					return false;
				}else{
					$('.error_person').html('');
				}
				if(borrow_telephone == ''){
					$('.error_telephone').html("<font color='red'>请填写借钥匙人联系方式</font>");
					return false;
				}else{
					$('.error_telephone').html('');
				}
				if(borrow_company == ''){
					$('.error_company').html("<font color='red'>请填写借钥匙人所属公司</font>");
					return false;
				}else{
					$('.error_company').html('');
				}
				if(time == ''){
					$('.error_time1').html("<font color='red'>请填写借用时间</font>");
					return false;
				}else{
					$('.error_time1').html('');
				}
				if(reason == ''){
					$('.error_reason').html("<font color='red'>请填写借用原因</font>");
					return false;
				}else{
					$('.error_reason').html('');
				}

			}
		}
		//还钥匙
		if(_id == "js_ys_huanyaoshi"){
			//alert(agency_huan);
			if(agency_huan == ''){
				$('.error_agency2').html("<font color='red'>请填写接收门店</font>");
				return false;
			}else{
				$('.error_agency2').html('');
			}
			if(broker_huan == ''){
				$('.error_broker2').html("<font color='red'>请填写接收员工</font>");
				return false;
			}else{
				$('.error_broker2').html('');
			}
			if(time_huan == ''){
				$('.error_time2').html("<font color='red'>请填写接收归还时间</font>");
				return false;
			}else{
				$('.error_time2').html('');
			}
		}
		//还业主
		if(_id == "js_ys_huanyezhu"){
			if(agency3 == ''){
				$('.error_agency3').html("<font color='red'>请填写归还门店</font>");
				return false;
			}else{
				$('.error_agency3').html('');
			}
			if(broker3 == ''){
				$('.error_broker3').html("<font color='red'>请填写归还员工</font>");
				return false;
			}else{
				$('.error_broker3').html('');
			}
			if(time3 == ''){
				$('.error_time3').html("<font color='red'>请填写归还时间</font>");
				return false;
			}else{
				$('.error_time3').html('');
			}
		}

		//console.log($(this).serialize());
		$('.pop_box_g_big').hide();
		//closeWindowWin('pop_box_g_big');
		//alert($(this).serialize());
		$.ajax({
				url: "<?php echo MLS_URL;?>/key/"+type+"/",
				type: "POST",
				dataType: "json",
				data:$(this).serialize(),
				success: function(data) {
					if(data['result'] == 'ok')
					{
						openWin('js_pop_msg1');
					}
				},
				error: function(data){
					if(data['result'] == 'fail')
					{
						openWin('js_pop_msg2');
					}
				}
			});
	});
}
</script>

</body>
</html>

<!--客户信息弹框-->
<div id="js_keyuan" class="iframePopBox" style=" width:530px; height:345px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
   <iframe frameborder="0" scrolling="no" width="530" height="345" class='iframePop' src=""></iframe>
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
