<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<div class="tab_box" id="js_tab_box">
   <a href="/broker/" class="link"><span class="iconfont">&#xe615;</span>员工管理</a>
   <a href="/permission/" class="link"><span class="iconfont">&#xe61d;</span>角色权限</a>
   <a href="/notice_manage/notice_list/" class="link link_on"><span class="iconfont">&#xe617;</span>办公管理</a>
</div>

<div id="js_search_box" class="shop_tab_title">
    <a href="/notice_manage/notice_list/" class="link">通知管理<span class="iconfont hide">&#xe607;</span></a>
    <a href="/operator_log/" class="link">操作日志<span class="iconfont hide">&#xe607;</span></a>
    <a href="/personnel_log/" class="link">员工日志<span class="iconfont hide">&#xe607;</span></a>
    <a href="/follow_log/" class="link">跟进日志<span class="iconfont hide">&#xe607;</span></a>
    <a href="/cooperate/cooperate_query/" class="link link_on">合作查询<span class="iconfont hide">&#xe607;</span></a>
    <a href="/deal_check/" class="link">成交查询<span class="iconfont hide">&#xe607;</span></a>
</div>


<form method='post' action='' id='search_form' name='search_form'>
<div class="search_box clearfix" id="js_search_box_02">
    <div class="fg_box">
        <p class="fg fg_tex"> 分店：</p>
        <div class="fg">
            <input type="text" class="input w60">
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 员工：</p>
        <div class="fg">
            <input type="text" class="input w60">
        </div>
    </div>
    <div class="fg_box">
            <p class="fg fg_tex"> 开始时间：</p>
            <div class="fg">
                <input type="text" class="input w90 time_bg" id="start_time" name="start_time" value="<?php if(isset($_POST['start_time'])){echo $_POST['start_time'];}?>" onclick="WdatePicker()">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <p class="fg fg_tex"> 结束时间：</p>
            <div class="fg">
                <input type="text" class="input w90 time_bg" id="end_time" name="end_time" value="<?php if(isset($_POST['end_time'])){echo $_POST['end_time'];}?>" onclick="WdatePicker()">
            </div>
    </div>
 	<div class="fg_box">
        <p class="fg fg_tex"> 模糊搜索：</p>
        <div class="fg">
            <input type="text" class="input w60">
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();">搜索</a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset()">重置</a></div>
    </div>
</div>


<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
              	<td class="c8"><div class="info">看房人</div></td>
                <td class="c15"><div class="info">所属门店</div></td>
                <td class="c10"><div class="info">联系方式</div></td>
                <td class="c8"><div class="info">委托人</div></td>
                <td class="c15"><div class="info">所属门店</div></td>
                <td class="c10"><div class="info">电话</div></td>
               	<td class="c10"><div class="info">房源编号</div></td>
               	<td class="c10"><div class="info">状态</div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
            <?php
			if($list)
			{
				foreach($list as $key => $val)
				{
			?>
            <tr>
              	<td class="c8"><div class="info"><?php echo $val['broker_name_b'];?></div></td>
                <td class="c15"><div class="info"><?php echo $val['broker_b']['agency_name'];?></div></td>
                <td class="c10"><div class="info"><?php echo $val['phone_b'];?></div></td>
                <td class="c8"><div class="info"><?php echo $val['broker_name_a'];?></div></td>
                <td class="c15"><div class="info"><?php echo $val['broker_a']['agency_name'];?></div></td>
                <td class="c10"><div class="info"><?php echo $val['phone_a'];?></div></td>
               	<td class="c10"><div class="info"><?php echo $val['rowid'];?></div></td>
               	<td class="c10">
                    <div class="info">
                        <span class="is_esta <?php if(in_array($val['esta'],array(2,4,10))){echo "s";}elseif(in_array($val['esta'],array(5,6,9))){echo "e";}?>">
                            <?php echo $esta_conf[$val['esta']];?>
                        </span>
                    </div>
                </td>
                <td ><div class="info"><a href="javascript:void(0)" class="fun_link" onclick="open_details(<?php echo $val['id'];?>)">查看</a></div></td>
            </tr>
            <?php
                }
            }else{
                ?>
                <tr><td colspan="9">抱歉，没有找到符合条件的信息</td></tr>
            <?php }?>

        </table>
    </div>
</div>


<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
    <div class="get_page">
    	<?php echo $page_list;?>
    </div>
</div>
</form>

<!--详情弹框-->
<div id="js_pop_box_cooperation" class="iframePopBox" style=" width:920px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="920" height="540" class='iframePop' src=""></iframe>
</div>

<script>
//打开详情弹层
function open_details(id)
{
    var _id = parseInt(id);
    var _url = '<?php echo MLS_URL;?>/cooperate/my_send_order/'+ _id;

    if(_url)
    {
         $("#js_pop_box_cooperation .iframePop").attr("src",_url);
    }
    openWin('js_pop_box_cooperation');
}

function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>


