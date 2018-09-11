<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<div class="tab_box" id="js_tab_box" style="margin-bottom:0">
    <?php echo $user_menu;?>
</div>
<div class="search_box clearfix" id="js_search_box"> <a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this)" data-h="0">展开<span class="iconfont">&#xe609;</span></a>
    <form action="" method="post" id="search_form">
    <div class="fg_box">
        <p class="fg fg_tex"> 房源编号：</p>
        <div class="fg">
            <input type="text" class="input w90" name="house_id" id="house_id" value="<?php echo $post_param['house_id']?>">
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 楼盘：</p>
        <div class="fg">
            <input type="text" class="input w120" name="block_name" id="block_name" value="<?php echo $post_param['block_name']; ?>">
            <input name="block_id" id="block_id" value="<?php echo $post_param['block_id']?>" type="hidden">
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
                        change_dong();//改变栋座下拉菜单
                    }else{
                        openWin('js_pop_add_new_block');
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
        <p class="fg fg_tex fg_tex02"></p>
        <div class="fg">
            <select class="select" name="dong" id="dong" onchange="change_unit();">
                <option value="0">栋座</option>
                <?php if( isset($dongs) ){?>
                <?php foreach($dongs as $k => $v){?>
				<option value="<?php echo $v;?>" <?php if($post_param['dong']==$v){echo "selected='selected'";}?>><?php echo $v;?></option>
				<?php }?>
                <?php }?>
            </select>
        </div>
    </div>

    <div class="fg_box">
        <p class="fg fg_tex fg_tex02"></p>
        <div class="fg">
            <select class="select" name="unit" id="unit">
                <option value="0">单元</option>
                <?php if( isset($units) ){?>
                <?php foreach($units as $k => $v){?>
				<option value="<?php echo $v;?>" <?php if($post_param['unit']==$v){echo "selected='selected'";}?>><?php echo $v;?></option>
				<?php }?>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 钥匙编号：</p>
        <div class="fg">
            <input type="text" class="input w60" name="number" value="<?php echo $post_param['number']?>">
        </div>
    </div>

    <div class="fg_box">
        <p class="fg fg_tex"> 钥匙状态：</p>

        <?php foreach($status_arr as $key=>$val){ ?>
        <label class="fg fg_tex fg_tex02">
            <input type="radio" name="status" value="<?php echo $key;?>" <?php if($post_param['status']==$key){echo "checked='checked'";}?>><?php echo $val;?>
        </label>
        <?php }?>

    </div>



    <div class="fg_box hide">
        <p class="fg fg_tex"> 收钥匙人：</p>
        <div class="fg">
            <select class="select agency_id" name="agency_id" rel="broker_id_0">
                <option value="0">请选择门店</option>
                <?php foreach($agencys as $k => $v){?>
				<option value="<?php echo $v['agency_id'];?>" <?php if($post_param['agency_id']==$v['agency_id']){echo "selected='selected'";}?>><?php echo $v['agency_name'];?></option>
				<?php }?>
            </select>
        </div>
        <div class="fg fg_tex fg_tex03">
            <select class="select broker_id broker_id_0" name="broker_id">
                <option value="0">请选择员工</option>
                <?php if( isset($brokers) ){?>
                <?php foreach($brokers as $k => $v){?>
				<option value="<?php echo $v['broker_id'];?>" <?php if($post_param['broker_id']==$v['broker_id']){echo "selected='selected'";}?>><?php echo $v['truename'];?></option>
				<?php }?>
                <?php }?>
            </select>
        </div>
    </div>

    <div class="fg_box hide">
        <p class="fg fg_tex"> 收钥匙时间：</p>
        <div class="fg">
            <input type="text" class="input w100 time_bg" id="start_time" name="start_time" value="<?php echo $post_param['start_time'];?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH'})">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" class="input w100 time_bg" id="end_time" name="end_time" value="<?php echo $post_param['end_time'];?>" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH'})">
        </div>

    </div>


    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span  class="btn_inner">搜索</span></a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
    </div>
</div>

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <td class="c8"><div class="info">钥匙编号</div></td>
                <td class="c8"><div class="info">钥匙状态</div></td>
                <td class="c15"><div class="info">房源楼盘</div></td>
                <td class="c5"><div class="info">栋座</div></td>
                <td class="c5"><div class="info">单元</div></td>
                <td class="c5"><div class="info">门牌</div></td>
                <td class="c8"><div class="info">收钥匙人</div></td>
                <td class="c12"><div class="info">收钥匙门店</div></td>
                <td class="c12"><div class="info">收钥时间</div></td>
                <td class="c5"><div class="info">借用次数</div></td>
                <td class="c5"><div class="info">详情</div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table table_q" style="*width:99%;">
            <?php
            if($list){
                foreach($list as $key=>$val){
            ?>
            <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
                <td class="c8"><div class="info"><a href="javascript:void(0)"><?php echo $val['number'];?></a></div></td>
                <td class="c8"><div class="info"><?php echo $status_arr[$val['status']];?></div></td>
                <td class="c15"><div class="info"><?php echo $val['block_name'];?></div></td>
                <td class="c5"><div class="info"><?php echo $val['dong'];?></div></td>
                <td class="c5"><div class="info"><?php echo $val['unit'];?></div></td>
                <td class="c5"><div class="info"><?php echo $val['door'];?></div></td>
                <td class="c8"><div class="info"><?php echo $val['truename'];?></div></td>
                <td class="c12"><div class="info"><?php echo $val['agency_name'];?></div></td>
                <td class="c12"><div class="info"><?php echo date("Y-m-d H:i:s",$val['add_time']);?></div></td>
                <td class="c5"><div class="info"><?php echo $val['num'];?></div></td>
                <td class="c5"><div class="info">
                <a href="javascript:void(0)" onClick="open_details(<?php echo $val['id'];?>)">查看</a>
                               </div>
                </td>
                <td >
                    <div class="info info_p_r">
                        <?php if($val['status'] == 1){ ?>
                            <a href="javascript:void(0)" onClick="add_key_log(<?php echo $val['id'];?>,1,'borrow_key')">借钥匙 </a>
                            <span class="fg">|</span>
                            <a href="javascript:void(0)" onClick="add_key_log(<?php echo $val['id'];?>,3,'also_owner')">还业主</a>
                        <?php }elseif($val['status'] == 2){ ?>
                            <a href="javascript:void(0)" onClick="add_key_log(<?php echo $val['id'];?>,2,'also_key')">还钥匙</a>
                        <?php }?>
                    </div>
                </td>
            </tr>
            <?php
                }
            }else{
            ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php
            }
            ?>

        </table>
    </div>
</div>
<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
    <div class="get_page">
        <?php echo $page_list; ?>
    </div>
</div>
</form>





<div class="pop_box_g pop_box_g_big pop_box_g_big02" id="js_ys_xiangqing">
    <div class="hd">
        <div class="title">钥匙详情</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="c10">经纪人</th>
                        <th class="c15">门店</th>
                        <th class="c10">操作类型</th>
						<th class="c15">联系方式</th>
						<th class="c15">借出原因</th>
						<th class="c25">操作时间</th>
						<th>操作人</th>
                    </tr>
                </table>
            </div>
        </div>
        <a href="javascript:void(0);" class="JS_Close btn-lv1 btn-mid">确定</a>

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

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
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
                    <button type="button" id = 'dialog_share' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                    <input type ="hidden" name='ci_id' id = 'rowid' value = ''>
                    <input type ="hidden" name='secret_key' id = 'secret_key' value = ''>
                    <input type ="hidden" name='atction_type' id = 'atction_type' value = ''>
                    <input type ="hidden" name='do_type' id = 'do_type' value = ''>
                </div>
            </div>
    </div>
</div>
<!-- 确认操作成功+提示 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg1">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="<?php echo MLS_SIGN_URL;?>/key/index"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img class="img_msg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                    <span id="dialog_do_itp" class="span_msg">操作成功！</span>
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
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="<?php echo MLS_SIGN_URL;?>/key/index"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">

                <p class="text"><img class="img_msg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                    <span id="dialog_do_itp" class="span_msg">操作失败！</span>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
/*$(function(){
	$("#borrow_person").blur(function(){
		var name=$(this).val();
		if(name == ''){
			$("#name").html("<font color='red'>姓名不能为空</font>");

		}
	});
	$("#borrow_telephone").blur(function(){
		var telephone=$(this).val();
		if(telephone == ''){
			$("#telephone").html("<font color='red'>联系方式不能为空</font>");

		}
	});
	$("#borrow_company").blur(function(){
		var company=$(this).val();
		if(company == ''){
			$("#company").html("<font color='red'>公司不能为空</font>");

		}
	});
});*/
//改变栋座下拉菜单
function change_dong(){
    var block_id = $("#block_id").val();
    if(block_id){
        $.ajax({
            url: "<?php echo MLS_SIGN_URL;?>/key/get_dong_list/",
            type: "GET",
            dataType: "json",
            data: {
                block_id:block_id
            },
            success: function(data) {
                var html ="<option value='0'>栋座</option>";
                if(data['result'] == 'ok')
                {
                    var list = data['list'];
                    for(var i in list){
                        html += "<option value='"+list[i]+"'>"+list[i]+"</option>";
                    }
                }
                $("#dong").html(html);
            }
        });
    }
}
//改变单元下拉菜单
function change_unit(){
    var block_id = $("#block_id").val();
    var dong = $("#dong").val();
    if(block_id){
        $.ajax({
            url: "<?php echo MLS_SIGN_URL;?>/key/get_unit_list/",
            type: "GET",
            dataType: "json",
            data: {
                block_id:block_id,
                dong:dong
            },
            success: function(data) {
                var html ="<option value='0'>单元</option>";
                if(data['result'] == 'ok')
                {
                    var list = data['list'];
                    for(var i in list){
                        html += "<option value='"+list[i]+"'>"+list[i]+"</option>";
                    }
                }
                $("#unit").html(html);
            }
        });
    }
}
//打开详情弹层
function open_details(id)
{
    $('#js_ys_xiangqing .table tr.list').remove();
	//ajax异步获取钥匙详情信息
    $.ajax({
        url: "<?php echo MLS_SIGN_URL;?>/key/details/"+id,
        type: "GET",
        dataType: "json",
        data: {
            isajax:1
        },
        success: function(data) {
            if(data['errorCode'] == '401')
            {
                login_out();
            }
            else if(data['errorCode'] == '403')
            {
                permission_none();
            }else{
                if(data['result'] == 'ok')
                {
                    var list = data['list'];
                    var html ="";
                    for(var i in list){
                        html += "<tr class='list";
                        if(i%2 == 1){
                            html += " bg";
                        }
						if(list[i]['company_status']==2){
							 html += "'><td>"+list[i]['borrow_person']+"</td><td>"+list[i]['borrow_company']+"</td><td>"+list[i]['act_name']+"</td><td>"+list[i]['borrow_telephone']+"</td><td>"+list[i]['reason']+"</td><td>"+list[i]['time']+"</td><td>"+list[i]['truename']+"</td></tr>";
						}else{
							html += "'><td>"+list[i]['truename']+"</td><td>"+list[i]['agency_name']+"</td><td>"+list[i]['act_name']+"</td><td>"+list[i]['phone']+"</td><td>"+list[i]['reason']+"</td><td>"+list[i]['time']+"</td><td>"+list[i]['truename']+"</td></tr>";
						}

                    }
                    $('#js_ys_xiangqing .table').append(html);
                    openWin('js_ys_xiangqing');//打开弹层
                }else{
                    $("#dialog_do_warnig_tip").html("抱歉，没有详情信息");
                    openWin('js_pop_do_warning');
                }
            }
        }
    });

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
				url: "<?php echo MLS_SIGN_URL;?>/key/"+type+"/",
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
function tab_click(type){
	if(type==1){
		$("#self_store,#self_staff").show();
		$("#other_name,#other_tel,#other_company").hide();
	}else{
		$("#self_store,#self_staff").hide();
		$("#other_name,#other_tel,#other_company").show();
	}
}

$(function(){
    //门店、员工下拉菜单关联
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
    //借钥匙操作验证
/*    $("#jieyaoshi_form").validate({
        errorPlacement: function(error, element) {
        error.appendTo(element.siblings(".error"));
        },
        rules:{
            agency_id: {
                min:1
            },
            broker_id:{
                min:1
            },
            time:{
                required: true
            },
            reason:{
                required: true
            }

        },
        messages:{
            agency_id: {
                min: "请选择归还门店"
            },
            broker_id:{
                min:"请选择归还员工"
            },
            time:{
                required: "请选择归还时间"
            },
            reason:{
                required: "请选择借用原因"
            }
        }
    });
    //还钥匙操作验证
    $("#huanyaoshi_form").validate({
        errorPlacement: function(error, element) {
        error.appendTo(element.siblings(".error"));
        },
        rules:{
            agency_id: {
                min:1
            },
            broker_id:{
                min:1
            },
            time:{
                required: true
            }

        },
        messages:{
            agency_id: {
                min: "请选择归还门店"
            },
            broker_id:{
                min:"请选择归还员工"
            },
            time:{
                required: "请选择归还时间"
            }
        }
    });
    //还业主操作验证
  /*  $("#huanyezhu_form").validate({
        errorPlacement: function(error, element) {
        error.appendTo(element.siblings(".error"));
        },
        rules:{
            agency_id: {
                min:1
            },
            broker_id:{
                min:1
            },
            time:{
                required: true
            }

        },
        messages:{
            agency_id: {
                min: "请选择归还门店"
            },
            broker_id:{
                min:"请选择归还员工"
            },
            time:{
                required: "请选择归还时间"
            }
        }
    });*/
});
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>

