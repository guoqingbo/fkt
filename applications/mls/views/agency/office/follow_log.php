<script>
    window.parent.addNavClass(11);
</script>

<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){

    $('#run_agency').change(function(){
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
                $('#run_broker').html(str);
            }
        });
    });

    $('#search_follow').click(function(){
        $('#search_form').submit();
    });

    $('#reset_follow').click(function(){
    	$("#search_form").find(":input").not(":button,:submit,:reset,:hidden").val("").removeAttr("checked").removeAttr("selected");
        $("#run_broker").html("<option value='0'>不限</option>");
    });
});
</script>


<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>

<div id="js_search_box" class="shop_tab_title">
    <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
</div>

<div class="search_box clearfix" id="js_search_box">
    <form name="search_form" id="search_form" method="post" action="/follow_log/" >
    <div class="fg_box">
        <p class="fg fg_tex">任务类型：</p>
        <div class="fg">
            <select class="select" name="follow_type" id="follow_type">
                <option value="0">不限</option>
                <option value="1" <?php if($follow_type == 1){echo 'selected="selected"';}?>>系统跟进</option>
                <option value="2" <?php if($follow_type == 2){echo 'selected="selected"';}?>>房源跟进</option>
                <option value="3" <?php if($follow_type == 3){echo 'selected="selected"';}?>>客源跟进</option>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 跟进日期：</p>
        <div class="fg">
            <input type="text" class="input time_bg w90" id="start_date_begin" name="start_date_begin" onclick="WdatePicker()" value="<?=$start_date_begin?>">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" class="input time_bg w90" id="start_date_end" name="start_date_end" onclick="WdatePicker()" value="<?=$start_date_end?>">
        </div>
    </div>

    <div class="fg_box">
        <p class="fg fg_tex"> 跟进部门：</p>
        <div class="fg">
            <select class="select" id="run_agency" name="run_agency">
                <option value="0">不限</option>
                <?php
                if ($agency_info) {
                    foreach ($agency_info as $v) {
                ?>
                <option value="<?=$v['agency_id']?>"<?php if((!empty($run_agency) && $run_agency == $v['agency_id'])){echo 'selected="selected"';}?>><?=$v['agency_name']?></option>
                <?php
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 跟进人：</p>
        <div class="fg">
            <select class="select" id="run_broker" name="run_broker">
                <option value="0">不限</option>
                <?php if(is_array($broker_info_run) && !empty($broker_info_run)){ ?>
                <?php foreach($broker_info_run as $key =>$value){ ?>
                <option value="<?php echo $value['broker_id'];?>" <?php if($run_broker == $value['broker_id']){ echo 'selected="selected"';  } ?>>
                <?php echo $value['truename'];?>
                </option>
                <?php
                    }
                 }
                 ?>
            </select>
        </div>

    </div>

    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" id="search_follow"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="javascript:void(0)" class="reset" id="reset_follow">重置</a> </div>
    </div>
    </form>
</div>

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <td class="c5"><div class="info">任务类型</div></td>
                <td class="c8"><div class="info">房源/客源编号</div></td>
                <td class="c6"><div class="info">业主/客户</div></td>
                <td class="c15"><div class="info">跟进内容</div></td>
                <td class="c12"><div class="info">跟进部门</div></td>
                <td class="c5"><div class="info">跟进人</div></td>
                <td class="c11"><div class="info">跟进时间</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
            <?php
            if($follow_info){
                foreach ($follow_info as $key=>$value){
            ?>
            <tr>
                <td class="c5">
                    <div class="info">
                    <?php
                    if($value['follow_type'] == 1){
                        echo '系统跟进';
                    }elseif($value['follow_type'] == 2){
                        echo '房源跟进';
                    }elseif($value['follow_type'] == 3){
                        echo '客源跟进';
                    }
                    ?>
                    </div>
                </td>
                <td class="c8">
                    <div class="info">
                        <?php
                        if($value['type'] == 1){
                        ?>
                        <a href="javascript:void(0)" date-url="/sell/details/<?=$value['house_id']?>/1" onClick="openUrl(this)"><?=$value['house_id'] ?></a>
                        <?php
                        }elseif($value['type'] == 2){
                        ?>
                        <a href="javascript:void(0)" date-url="/rent/details/<?=$value['house_id']?>/1" onClick="openUrl(this)"><?=$value['house_id'] ?></a>
                        <?php
                        }elseif($value['type'] == 3){
                        ?>
                        <a href="javascript:void(0)" date-url="/customer/details/<?=$value['customer_id'] ?>" onClick="openUrl(this)"><?=$value['customer_id'] ?></a>
                        <?php
                        }elseif($value['type'] == 4){
                        ?>
                        <a href="javascript:void(0)" date-url="/rent_customer/details/<?=$value['customer_id'] ?>" onClick="openUrl(this)"><?=$value['customer_id'] ?></a>
                        <?php
                        }
                        ?>
                    </div>
                </td>
                <td class="c6"><div class="info"><?=$value['name']?></div></td>
                <td class="c15"><div class="info"><?=$value['text']?></div></td>
                <td class="c12"><div class="info"><?=$value['agencyname']?></div></td>
                <td width="6%"><div class="info"><?=$value['truename']?></div></td>
                <td width="15%"><div class="info"><?=$value['date']?></div></td>
            </tr>
            <?php
                }
            }
            ?>
        </table>
    </div>
</div>
<div class="fun_btn clearfix" id="js_fun_btn">
    <div class="get_page">
		<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
	</div>
</div>
<!--详情页弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>


<script type="text/javascript">

function openUrl(obj)
{
    var _url = $(obj).attr("date-url");
    $("#js_pop_box_g .iframePop").attr("src",_url);
    openWin('js_pop_box_g');

}
</script>
