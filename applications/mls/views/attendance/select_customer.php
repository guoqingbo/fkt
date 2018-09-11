<body >

<!--选择房源-->
<form action="" method="post" id="search_form">
<div class="search_box clearfix pop_house_select_iframe" id="js_search_box">
    <div class="fg_box">
        <p class="fg fg_tex"> 交易类型：</p>
        <div class="fg fg-edit">
            <select>
                <?php if($type == 1) {?>
                <option value="1">求购</option>
                <?php }elseif($type == 2) {?>
                <option value="2">求租</option>
                <?php }?><?php echo $val['id'];?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 状态：</p>
        <div class="fg fg-edit">
            <select name="status">
                <option value="0" >全部</option>
                <?php foreach($config['status'] as $k => $v){?>
                <option value="<?php echo $k;?>" <?php if($post_param['status']==$k){echo "selected='selected'";}?>><?php echo $v;?></option>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">姓名：</p>
        <div class="fg fg-edit"><input type="text" size="10" name="truename" value="<?php echo $post_param['truename'];?>"/></div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">客户编号：</p>
        <div class="fg fg-edit"><input type="text" size="10" name="customer_id" value="<?php echo $post_param['customer_id'];?>"/></div>
    </div>
    <?php if(in_array($func_area,array(2,3))){ ?>
    <div class="fg_box">
        <p class="fg fg_tex"> 所属业务员：</p>
        <div class="fg fg-edit">
            <?php if($func_area==3){ ?>
            <select name="agency_id" id="agency_id">
                <option value="0">请选择门店</option>
                <?php foreach($agencys as $k => $v){?>
				<option value="<?php echo $v['agency_id'];?>" <?php if($post_param['agency_id']==$v['agency_id']){echo "selected='selected'";}?>><?php echo $v['agency_name'];?></option>
				<?php }?>
            </select>&nbsp;&nbsp;
            <?php }?>
            <select name="broker_id" id="broker_id">
                <option value="0">请选择人员</option>
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
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
    </div>
</div>

<div class="table_all pop_house_select_iframe_table">
    <div class="title">
        <table class="table">
            <tr>
                <td class="c5"></td>
                <td class="c5">客源编号</td>
                <td class="c10">状态</td>
                <td class="c10">类型</td>
                <td class="c10">客户姓名</td>
                <td class="c20">意向区属</td>
                <td class="c10">面积<br/>(m&sup2;)</td>
                <td class="c10">价格<br/>(<?php if($type == 1) {?>万<?php }elseif($type == 2) {?>元/月<?php }?>)</td>
                <td class="c20">所属业务人员</td>
            </tr>
        </table>
    </div>
    <div class="inner">
        <table class="table list-table">
            <?php
            if($list){
                foreach($list as $key=>$val){
            ?>
            <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
                <td class="c5">
                    <input type="radio" class="customer" value="<?php echo $val['id'];?>" />
                    <input type="hidden" class="customer_id" value="<?php if($type == 1) {?>QG<?php }elseif($type == 2) {?>QZ<?php }?><?php echo $val['id'];?>" />
                </td>
                <td class="c5"><?php if($type == 1) {?>QG<?php }elseif($type == 2) {?>QZ<?php }?><?php echo $val['id'];?></td>
                <td class="c10"><?php echo $config['status'][$val['status']]; ?></td>
                <td class="c10"><?php if($type == 1) {?>求购<?php }elseif($type == 2) {?>求租<?php }?></td>
                <td class="c10"><?php echo $val['truename'];?></td>
                <td class="c20">
                    <?php
                    $district_str = '';
                    if($val['dist_id1'] > 0 && isset($district_arr[$val['dist_id1']]['district']))
                    {
                         $district_str =  $district_arr[$val['dist_id1']]['district'];
                    }

                    if($val['dist_id2'] > 0 && isset($district_arr[$val['dist_id2']]['district']))
                    {
                        $district_str .=  !empty($district_str) ? '，'.$district_arr[$val['dist_id2']]['district'] :
                            $district_arr[$val['dist_id2']]['district'];
                    }

                    if($val['dist_id2'] > 0 && isset($district_arr[$val['dist_id3']]['district']))
                    {
                         $district_str .=  !empty($district_str) ? '，'.$district_arr[$val['dist_id3']]['district'] :
                             $district_arr[$val['dist_id3']]['district'];
                    }
                    echo $district_str ;
                    ?>
                </td>
                <td class="c10"><?php echo strip_end_0($val['area_min']);?>-<?php echo strip_end_0($val['area_max']);?></td>
                <td class="c10"><?php echo strip_end_0($val['price_min']);?>-<?php echo strip_end_0($val['price_max']);?></td>
                <td class="c20"><?php echo $val['agency_name'];?>&nbsp;<?php echo $val['broker_name'];?></td>
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

<div class="fun_btn clearfix bottom-fun-btn" id="js_fun_btn">
    <div class="get_page">
        <?php echo $page_list; ?>
    </div>
</div>
</form>

<div class="btn-group">
    <input type="button" value="确定" id="confirm" class="btn-lv1 btn-left" onclick="insert(<?php echo $act;?>);"/>
    <input type="button" value="取消" id="cancel" class="btn-hui1" onclick="close_iframe();"/>
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
                 <p class="text" id='dialog_do_warnig_tip'><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/error_ico.png">&nbsp;&nbsp;<span></span></p>
            </div>
        </div>
    </div>
</div>
</body>
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
                    str = '<option value="0">请选择人员</option>';
                }else{
                    str = '<option value="0">请选择人员</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].broker_id+'">'+msg[i].truename+'</option>';
                    }
                }
                $('#broker_id').html(str);
            }
        });
    });
});
function insert(act){
    var customer = $(".customer:checked").val();
    if(customer){
        var _tr = $("#tr"+customer);
        var customer_id = _tr.find(".customer_id").val();
        $(self.parent.frames["iframe"+act].document).find("#customer_id").val(customer_id);
        close_iframe();
    }else{
        $("#dialog_do_warnig_tip span").html("请选择房源！");
        openWin('js_pop_do_warning');
    }
}
function close_iframe(){
    $(window.parent.document).find('#js_pop_customer_select').hide();
    $(window.parent.document).find('#' + 'GTipsCover' + 'js_pop_customer_select').remove();
}
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>
