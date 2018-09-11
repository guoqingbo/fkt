<script>
    window.parent.addNavClass(11);
</script>

<div class="tab_box" id="js_tab_box"> 
   <?php echo $user_menu;?>
</div>

<div id="js_search_box" class="shop_tab_title">
    <?php echo $user_func_menu;?>
</div>


<form method='post' action='' id='search_form' name='search_form'>
<div class="search_box clearfix" id="js_search_box_02"> 
    
    <?php if($func_area==3){ ?>
    <div class="fg_box">
        <p class="fg fg_tex"> 分店：</p>
        <div class="fg">
            <select class="select" id="agency_id" name="agency_id">
                <option value="0">不限</option>
                <?php foreach($agencys as $k => $v){?>
				<option value="<?php echo $v['agency_id'];?>" <?php if($post_param['agency_id']==$v['agency_id']){echo "selected='selected'";}?>><?php echo $v['agency_name'];?></option>
				<?php }?>
            </select>
        </div>
    </div>
    <?php }?>
    <?php if(in_array($func_area,array(2,3))){ ?>
    <div class="fg_box">
        <p class="fg fg_tex"> 员工：</p>
        <div class="fg">
            <select class="select" id="broker_id" name="broker_id">
                <option value="0">不限</option>
                <?php if( isset($brokers) ){?>
                <?php foreach($brokers as $k => $v){?>
				<option value="<?php echo $v['broker_id'];?>" <?php if($post_param['broker_id']==$v['broker_id']){echo "selected='selected'";}?>><?php echo $v['truename'];?></option>
				<?php }?>
                <?php }?>
            </select>
        </div>
    </div>
    <?php }?>
    <div class="fg_box">
        <p class="fg fg_tex"> 开始时间：</p>
        <div class="fg">
            <input type="text" class="input w90 time_bg" id="start_time" name="start_time" value="<?php echo $post_param['start_time'];?>" onclick="WdatePicker()">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <p class="fg fg_tex"> 结束时间：</p>
        <div class="fg">
            <input type="text" class="input w90 time_bg" id="end_time" name="end_time" value="<?php $post_param['end_time'];?>" onclick="WdatePicker()">
        </div>
    </div>
 	<div class="fg_box">
        <p class="fg fg_tex"> 模糊搜索：</p>
        <div class="fg">
            <input type="text" class="input w60">
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
    </div>
</div>


<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
              	<td class="c8"><div class="info">成交人</div></td>
                <td class="c10"><div class="info">联系方式</div></td>
                <td class="c14"><div class="info">所属门店</div></td>
                <td class="c8"><div class="info">经纪人</div></td>
                <td class="c14"><div class="info">小区</div></td>
                <td class="c8"><div class="info">面积(㎡)</div></td>
               	<td class="c8"><div class="info">价格</div></td>
                <td class="c10"><div class="info">房源编号</div></td>
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
            <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
              	 <td class="c8"><div class="info"><?php echo $val['name']; ?></div></td>
                 <td class="c10"><div class="info"><?php echo $val['tel']; ?></div></td>
                <td class="c14"><div class="info"><?php echo $val['agency_name']; ?></div></td>
                <td class="c8"><div class="info"><?php echo $val['truename']; ?></div></td>
                <td class="c14"><div class="info"><?php echo $val['house_info']['block_name'];?></div></td>
                <td class="c8"><div class="info"><?php echo strip_end_0($val['house_info']['buildarea']); ?></div></td>
               	<td class="c8"><div class="info"><?php echo strip_end_0($val['house_info']['price']); ?><?php if($val['type'] == 1){echo "万";}elseif($val['type'] == 2){echo "元/月";}?></div></td>
                <td class="c10"><div class="info"><?php if($val['type'] == 1){echo "CS";}elseif($val['type'] == 2){echo "CZ";}?><?php echo $val['house_id']; ?></div></td>
                <td ><div class="info"><a href="javascript:void(0)" onClick="open_js_pop_see_info_deal(<?php echo $val['id']; ?>);">查看</a></div></td>
            </tr>
            <?php        
                }
            }else{
                ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
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


<!--查看详情-->
<div id="js_pop_see_info_deal" class="iframePopBox" style=" width:600px;height: 410px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="600" height="410" class='iframePop' src=""></iframe>
</div>

<script>
$(function(){
    $('#agency_id').change(function(){
        var agencyId = $(this).val();
        $.ajax({
            type: 'get',
            url : '/my_task/get_broker_ajax/'+agencyId,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg===''){
                    str = '<option value="0">不限</option>';
                }else{
                    str = '<option value="0">不限</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].broker_id+'">'+msg[i].truename+'</option>';
                    }
                }
                $('#broker_id').html(str);
            }            
        });
    });   
});

//查看详情
function open_js_pop_see_info_deal(id)
{ 
    var _url = '/deal_check/details/'+id;         
    
    $("#js_pop_see_info_deal .iframePop").attr("src",_url);
    
    openWin('js_pop_see_info_deal');
};
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>