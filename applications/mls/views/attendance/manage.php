<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<div class="tab_box" id="js_tab_box">
<?php echo $user_menu;?>
</div>
<div id="js_search_box" class="shop_tab_title">
    <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
</div>
<div class="search_box clearfix" id="js_search_box">
    <form action="" method="post" id="search_form">
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
    </form>
</div>
<h1 class="attendance-title"><?php echo $date_str;?>&nbsp;&nbsp;<?php echo $broker_name;?>&nbsp;&nbsp;考勤表</h1>
<div class="attendance-wrap">
    <div class="thead">
        <table>
            <thead>
                <tr>
                    <th width="14.2%">星期日</th>
                    <th width="14.2%">星期一</th>
                    <th width="14.2%">星期二</th>
                    <th width="14.2%">星期三</th>
                    <th width="14.2%">星期四</th>
                    <th width="14.2%">星期五</th>
                    <th width="14.2%">星期六</th>
                </tr>
            </thead>
        </table>
    </div>
    <div style="margin-right:17px;">
        <div class="tbody">
            <table>
                <tbody>
                    <?php
                    if($date_array){
                        foreach($date_array as $key=>$val){
                    ?>
                        <?php if($val['week'] == 0){?>
                        <tr>
                        <?php }?>
                        <?php if($key == 1 && $val['week'] > 0){?>
                        <tr>
                            <?php for($i = 1; $i <= $val['week'];$i++){?>
                            <td width="14.2%">
                                <div class="record-wrap">
                                    <div class="day"></div>
                                    <div class="record"></div>
                                </div>
                            </td>
                            <?php }?>
                        <?php }?>
                            <td width="14.2%">
                                <div class="record-wrap<?php if($val['date'] == date("Y-m-d")){echo " active";}?>">
                                    <div class="day"><?php echo $key;?><?php if($val['date'] == date("Y-m-d")){?><a href="javascript:void(0);" onclick="add_attendance();">+</a><?php }?></div>
                                    <div class="record">
                                        <?php
                                        if($val['list']){
                                        ?>
                                        <ul>
                                        <?php
                                            foreach($val['list'] as $k=>$v){
                                        ?>
                                            <li>
                                                <a href="javascript:void(0);" onclick="update_attendance(<?php echo $v['id'];?>);">
                                                    <span><?php echo $config['type'][$v['type']];?></span>
                                                    <span class="time"><?php echo substr($v['datetime1'], 11);?></span>
                                                    <span<?php if($v['status'] == 0){echo " class='error'";}?>>
                                                        <?php if($v['status'] == 1){?>
                                                        正常
                                                        <?php }else{
                                                            if($v['type'] == 1){
                                                                echo "迟到";
                                                            }elseif($v['type'] == 2) {
                                                                echo "早退";
                                                            }else{
                                                                echo "未归";
                                                            }
                                                        }?>
                                                    </span>
                                                </a>
                                            </li>
                                        <?php
                                            }
                                        ?>
                                        </ul>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </td>
                        <?php if($val['week'] == 6){?>
                        </tr>
                        <?php }?>
                        <?php if($key == $date_t && $val['week'] < 6){?>
                            <?php for($i = 1; $i <= 6-$val['week'];$i++){?>
                            <td width="14.2%">
                                <div class="record-wrap">
                                    <div class="day"></div>
                                    <div class="record"></div>
                                </div>
                            </td>
                            <?php }?>
                        </tr>
                        <?php }?>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!--添加考勤-->
<div id="js_pop_add_attendance" class="iframePopBox" style=" width:600px; height:350px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="600" height="350" name="iframe1" id="iframe1" class='iframePop' src=""></iframe>
</div>

<!--修改考勤-->
<div id="js_pop_update_attendance" class="iframePopBox" style=" width:600px; height:380px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="600" height="380" name="iframe2" id="iframe2" class='iframePop' src=""></iframe>
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
//添加考勤
function add_attendance(){
    var _url = '<?php echo MLS_URL;?>/attendance/add_attendance';
    if(_url){
        $("#js_pop_add_attendance .iframePop").attr("src",_url);
    }
    openWin('js_pop_add_attendance');
}
//修改考勤
function update_attendance(id){
    var _url = '<?php echo MLS_URL;?>/attendance/update_attendance/'+id;
    if(_url){
        $("#js_pop_update_attendance .iframePop").attr("src",_url);
    }
    openWin('js_pop_update_attendance');
}
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>

