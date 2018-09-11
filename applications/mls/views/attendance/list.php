<script>
    window.parent.addNavClass(11);
</script>

<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<div class="tab_box" id="js_tab_box">
<?php echo $user_menu;?>
</div>
<div id="js_search_box" class="shop_tab_title">
    <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
</div>
<form action="" method="post" id="search_form">
<div class="search_box clearfix" id="js_search_box">
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
        <p class="fg fg_tex">时间：</p>
        <div class="fg gg">
            <input type="text" name="date" id="date" class="input w60" readonly="readonly" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM'})" value="<?php echo $post_param['date'] ? $post_param['date'] : date("Y-m");?>">
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset()">重置</a></div>
    </div>
</div>
<h1 class="attendance-title"><?php echo $date_str;?>&nbsp;&nbsp;<?php echo $attendance_name;?>&nbsp;&nbsp;考勤表</h1>
<div class="attendance-count-wrap">
    <table>
        <thead>
            <tr>
                <th></th>
                <?php for ($i = 1; $i <= $date_t; $i++) {?>
                <th class="day"><?php echo $i;?></th>
                <?php }?>
            </tr>
        </thead>
        <tbody>
            <?php
            if($list){
                foreach($list as $key=>$val){
            ?>
            <tr>
                <td rowspan="2"><?php echo $val['truename'];?></td>
                <?php foreach($val['am'] as $k=>$v){ ?>
                <td>
                    <span class="att-tab <?php if($v == 0){echo "late";}elseif($v == 1){echo "normal";}elseif($v == 2){echo "vacation";} ?>"></span>
                </td>
                <?php }?>
            </tr>
            <tr>
                <?php foreach($val['pm'] as $k=>$v){ ?>
                <td>
                    <span class="att-tab <?php if($v == 0){echo "leave-early";}elseif($v == 1){echo "normal";}elseif($v == 2){echo "vacation";} ?>"></span>
                </td>
                <?php }?>
            </tr>
            <?php
                }
            }else{
            ?>
                <tr><td colspan="<?php echo $date_t+1;?>"><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>
<div class="fun_btn clearfix" id="js_fun_btn">
    <div class="tab-info">
        <table>
            <tr>
                <td class="remind"><span class="bold">1</span>.&nbsp;“</td>
                <td><span class="att-tab normal"></span></td>
                <td>”&nbsp;表示正常&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><span class="bold">2</span>.&nbsp;空表示没有考勤记录&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><span class="bold">3</span>.&nbsp;“</td>
                <td><span class="att-tab leave-early"></span></td>
                <td>”&nbsp;表示早退&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><span class="bold">4</span>.&nbsp;“</td>
                <td><span class="att-tab late"></span></td>
                <td>”&nbsp;表示迟到&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><span class="bold">5</span>.&nbsp;“</td>
                <td><span class="att-tab vacation"></span></td>
                <td>”&nbsp;表示请假</td>
            </tr>
        </table>
    </div>
    <div class="get_page">
        <?php echo $page_list; ?>
    </div>
</div>
</form>

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
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>
