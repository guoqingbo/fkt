<script>
    window.parent.addNavClass(10);
</script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<div class="tab_box" id="js_tab_box">
<?php echo $user_menu;?>
</div>
<form name="search_form" action="/my_remind/index" method="post">
<div id="js_search_box_02">
    <div class="search_box clearfix">
        <a class="add_p_rz" onclick="openWin('js_pop_add_info_t')" href="javascript:void(0)"><span>添加提醒</span></a>
        <input type="hidden" value="1" name="pg"/>
        <div class="fg_box">
            <p class="fg fg_tex"> 时间：</p>
            <div class="fg">
                <input type="text" class="input w150 time_bg" name="min_create_time" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input type="text" class="input w150 time_bg" name="max_create_time" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})">
            </div>
        </div>
        <div class="fg_box">
            <div class="fg"><a href="javascript:void(0)" class="btn" id="search_form_submit"><span class="btn_inner">搜索</span></a> </div>
            <div class="fg"> <a href="javascript:void(0)" class="reset">重置</a> </div>
        </div>
    </div>
</div>

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <td class="c5"><div class="info"><input type="checkbox" id="js_checkbox"></div></td>
                <!-- <td class="c5"><div class="info">序号</div></td> -->
                <td class="c15"><div class="info">标题</div></td>
                <td class="c20"><div class="info">内容</div></td>
                <td class="c10"><div class="info">发布人</div></td>
                <td class="c15"><div class="info">发布时间</div></td>
                <td class="c15"><div class="info">提醒时间</div></td>
                <td><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
            <?php foreach ($remind_list as $k=>$v){?>
            <tr>
                <td class="c5"><div class="info"><input type="checkbox" name="rows_id[]" class="checkbox" value="<?php echo $v['id'];?>"></div></td>
                <!-- <td class="c5"><div class="info"><?php echo $v['id'];?></div></td> -->
                <td class="c15"><div class="info"><?php echo $v['title'];?></div></td>
                <td class="c20"><div class="info"><?php echo $v['contents'];?></div></td>
                <td class="c10"><div class="info"><?php echo $v['broker_name'];?></div></td>
                <td class="c15"><div class="info"><?php echo $v['create_time'];?></div></td>
                <td class="c15"><div class="info"><?php echo $v['notice_time'];?></div></td>
                <td>
                    <div class="info">
                        <?php if($v['status']==1){?>
                        <p class="s">已处理</p>
                        <?php }else if($v['status']==2){ ?>
                        <p class="n">已忽略</p>
                        <?php }else{ ?>
                        <a href="#" class="fun_link" onClick="openWin('js_pop_no_qd01');deal_set_id(<?php echo $v['id'];?>);">处理</a>
                        <a href="#" class="fun_link" onClick="openWin('js_pop_no_qd02');ignore_set_id(<?php echo $v['id'];?>);">忽略</a>
                        <a href="#" class="fun_link" onclick="openWin('js_pop_no_qd03');del_set_id(<?php echo $v['id'];?>);">删除</a>
                        <?php }?>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>
<div id="js_fun_btn" class="fun_btn clearfix">
	<a class="grey_btn" href="#" id="batch_deal" onclick="openWin('js_pop_no_qd05');">批量处理</a>
	<a class="grey_btn" href="#" id="batch_ignore" onclick="openWin('js_pop_no_qd06');">批量忽略</a>
	<a class="grey_btn" href="#" id="batch_del" onclick="openWin('js_pop_no_qd04');">删除</a>
	<div class="get_page">
		<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
	</div>
</div>
</form>

<div id="js_pop_no_qd01" class="pop_box_g pop_see_inform pop_no_q_up">
    <input type="hidden" value="" id="remind_id"/>
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a> </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;确定处理该事件？</p>
                <button type="button" class="btn-lv1 btn-left" onclick="sure_action('deal');">确定</button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            </div>
        </div>
    </div>
</div>

<div id="js_pop_no_qd02" class="pop_box_g pop_see_inform pop_no_q_up">
    <input type="hidden" value="" id="remind_id"/>
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a> </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;确定忽略该事件?</p>
                <button type="button" class="btn-lv1 btn-left" onclick="sure_action('ignore');">确定</button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            </div>
        </div>
    </div>
</div>

<div id="js_pop_no_qd03" class="pop_box_g pop_see_inform pop_no_q_up">
    <input type="hidden" value="" id="remind_id"/>
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a> </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;确定删除该事件?</p>
                <button type="button" class="btn-lv1 btn-left" onclick="sure_action('del');">确定</button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            </div>
        </div>
    </div>
</div>

<div id="js_pop_no_qd04" class="pop_box_g pop_see_inform pop_no_q_up">
    <input type="hidden" value="" id="remind_id"/>
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a> </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;确定删除选中事件?</p>
                <button type="button" class="btn-lv1 btn-left" onclick="batch_action('del');">确定</button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            </div>
        </div>
    </div>
</div>

<div id="js_pop_no_qd05" class="pop_box_g pop_see_inform pop_no_q_up">
    <input type="hidden" value="" id="remind_id"/>
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a> </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;确定处理选中事件?</p>
                <button type="button" class="btn-lv1 btn-left" onclick="batch_action('deal');">确定</button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            </div>
        </div>
    </div>
</div>

<div id="js_pop_no_qd06" class="pop_box_g pop_see_inform pop_no_q_up">
    <input type="hidden" value="" id="remind_id"/>
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a> </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;确定忽略选中事件?</p>
                <button type="button" class="btn-lv1 btn-left" onclick="batch_action('ignore');">确定</button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            </div>
        </div>
    </div>
</div>


<div id="js_pop_add_info_t" class="pop_box_g pop_see_inform pop_add_prz" >
    <div class="hd">
        <div class="title">添加提醒</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a> </div>
    </div>
    <div class="mod mod_bg">
        <div class="inform_inner">
            <form name="add_remind" action="/my_remind/add_remind" method="post">
            <table class="deal_table deal_table_see">
                <tr>
                    <th width="40">时间：</th>
                    <td colspan="5"><input type="text" class="input_text time_bg" name="notice_time" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'%y-%M-%d'})" ></td>
                </tr>
                <tr>
                    <th>标题：</th>
                    <td colspan="5"><input type="text" class="input_text" name="title" placeholder="  请输入标题"></td>
                </tr>
                <tr>
                    <th>内容：</th>
                    <td colspan="5"><textarea class="textarea" name="contents" placeholder="  请输入内容"></textarea></td>
                </tr>
                <tr>
                    <th colspan="5">&nbsp;</th>
                </tr>
            </table>
                <input type="button" class="btn-lv1 btn-mid" value="提交" id="add_remind_button"/>
            </form>
        </div>
    </div>
</div>
<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" id="success_close"></a>
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

<script type="text/javascript">
$(function(){
    //筛选搜索按钮
    $('#search_form_submit').click(function(){
        $('form[name="search_form"]').submit();
    });

    //添加事件提交事件
    $('#add_remind_button').click(function(){
        var add_data = {
            'notice_time':$('input[name="notice_time"]').val(),
            'title':$('input[name="title"]').val(),
            'contents':$('textarea[name="contents"]').val()
        };
        if(add_data.notice_time==''||add_data.title==''||add_data.contents==''){
            $('#dialog_do_itp').html('时间/标题/内容不能为空');
            openWin('js_pop_do_success');
        }else{
            $.ajax({
                url: '/my_remind/add_remind/',
                type: 'POST',
                data: add_data,
                success: function(data)
                {
                    //判断返回数据是否为空，不为空返回数据。
                    if('add_success'==data)
                    {
                        $('#dialog_do_itp').html('添加成功');
                        openWin('js_pop_do_success');
                    }
                    else
                    {
                        $('#dialog_do_itp').html('添加失败');
                        openWin('js_pop_do_success');
                    }
                    $('#success_close').live('click',function(){
                        window.location.reload();
                    })

                }
            });
        }
    });
});

//批量处理、忽略、删除
function batch_action(method){
    var checked_remind = [];
    $('input[name="rows_id[]"]:checked').each(function(){
        checked_remind.push($(this).val());
    });
    var data = {
        'method':method,
        'remind_ids':checked_remind
    }
    if(checked_remind.length==0){
        $('#dialog_do_itp').html('请选择事件');
        openWin('js_pop_do_success');
    }else{
        $.ajax({
            url: "/my_remind/batch_action/",
            type: "GET",
            data: data,
            success: function(data)
            {
                if('success'==data){
                    window.location.href="<?php echo MLS_URL;?>/my_remind/";
                }
            }
        });
    }
}

//门店、经纪人二级联动
function get_broker_by_agencyid(obj , child_object_id)
{
	var agency_id = parseInt($(obj).val());

	$.getJSON(
            '/agency/get_brokerinfo_by_agencyid/',
            {'agency_id':agency_id},
            function(data)
            {
                $("#"+child_object_id).empty();
                $("#"+child_object_id).append("<option selected='' value='0'>不限</option>");
                $.each(data, function(i, item) {
                    var child_option = "<option value="+ item.broker_id +">"+item.truename+"</option>";
                    $("#"+child_object_id).append(child_option);
                });
            }
	);
}

//点击处理设置id动作
function deal_set_id(id){
    $('#js_pop_no_qd01 #remind_id').val(id);
}
//点击忽略设置id动作
function ignore_set_id(id){
    $('#js_pop_no_qd02 #remind_id').val(id);
}
//点击删除设置id动作
function del_set_id(id){
    $('#js_pop_no_qd03 #remind_id').val(id);
}
//弹出框点击确定按钮
function sure_action(type){
    var url = '';
    var id = '';
    if(type=='deal'){
        url = "/my_remind/deal_remind/";
        id = $('#js_pop_no_qd01 #remind_id').val();
    }else if(type=='ignore'){
        url = "/my_remind/ignore_remind/";
        id = $('#js_pop_no_qd02 #remind_id').val();
    }else if(type=='del'){
        url = "/my_remind/del_remind/";
        id = $('#js_pop_no_qd03 #remind_id').val();
    }
    $.ajax({
        url: url,
        type: "GET",
        data: {'id' : id},
        success: function(data)
        {
            if(data=='deal_success'||data=='ignore_success'||data=='del_success'){
                window.location.href="<?php echo MLS_URL;?>/my_remind/";
            }
        }
    });
}
</script>
