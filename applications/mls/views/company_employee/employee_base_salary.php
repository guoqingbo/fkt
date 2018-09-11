<body >
<div class="tab_box" id="js_tab_box">
    <?php
    echo $user_menu;
    ?>
</div>
<div id="js_search_box" class="shop_tab_title">
    <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
</div>
<form method='post' action='/company_employee/employee_base_salary' id='search_form' name='search_form'>
    <div class="search_box clearfix" id="js_search_box">
        <div class="fg_box">
            <p class="fg fg_tex">分店：</p>
            <div class="fg">
                <input type="text" class="input w80" name="store_name" value="<?php echo $store_name; ?>">
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">员工：</p>
            <div class="fg">
                <input type="text" class="input w80" name="e_name" value="<?php echo $e_name; ?>">
            </div>
        </div>
        <div class="fg_box">
            <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
            <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset()">重置</a></div>
        </div>
        <div class="get_page">
            <?php echo $page_list;?>
        </div>
    </div>
</form>
<div class="table_all report-form-wrap">
    <div class="title" id="js_title">
        <table class="table">
            <tr>
                <td class="c10">序号</td>
                <td class="c10">分店名称</td>
                <td class="c10">员工名称</td>
                <td class="c10">基本工资</td>
                <td class="c10">操作</td>
            </tr>
        </table>
    </div>
    <div class="inner" id="js_inner" style="height: 389px !important;">
        <table class="table list-table">
            <?php if($list){
                    foreach($list as $key=>$val) { ?>
                        <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['broker_id'];?>">
                            <td class="c10"><?php echo $val['id'];?></td>
                            <td class="c10"><?php echo $val['store_name'];?></td>
                            <td class="c10"><?php echo $val['truename'];?></td>
                            <td class="c10"><?php echo $val['base_salary'];?></td>
                            <td class="c10"><a href="javascript:void(0);" onClick="modify_salary('<?php echo $val['broker_id'];?>',
                                '<?php echo $val['truename'];?>','<?php echo $val['store_name'];?>','<?php echo $val['base_salary'];?>')" class="fun_link">修改</a></td>
                        </tr>
                    <?php }
            } else { ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php } ?>
        </table>
    </div>
</div>
<div id="js_pop_modify_info_r" class="pop_box_g pop_see_inform pop_add_prz" >
    <div class="hd">
        <div class="title">修改基本工资</div>
         <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <form id="modify_salary" method="post" action="<?php echo MLS_URL;?>/company_employee/modify_salary/">
    <div class="mod mod_bg">
        <div class="inform_inner">
            <table class="deal_table deal_table_see">
                <tr>
                    <td colspan="5">
                        <input type="hidden" class="input_text"  id="broker_id" name="broker_id" value="">
                        部门名称：<span id="store_name" ></span>
                    </td>
                 </tr>
                 <tr>
                    <td colspan="5">
                        员工姓名：<span id="truename" ></span>
                    </td>
                 </tr>
                 <tr>
                    <td class="label">基本工资：</td>
                    <td><input name="base_salary" id="base_salary" value="" size="30">&nbsp;元</td>
                 </tr>
            </table>

            <button type="button" class="btn"  onclick="update_salary();">提交</button>
        </div>

    </div>
    </form>
</div>
<script>
function modify_salary(broker_id,truename,store_name,base_salary)
{
    $('#js_pop_modify_info_r').find("#broker_id").val(broker_id);
    $("#js_pop_modify_info_r").find("#truename").text(truename);
    $("#js_pop_modify_info_r").find("#base_salary").val(base_salary);
    $("#js_pop_modify_info_r").find("#store_name").text(store_name);
    openWin('js_pop_modify_info_r');//打开弹层
}

//修改记事本条件判断
function update_salary()
{
    var salary = $("#base_salary").val();
    var pattern = '^[0-9]+$';
    if(salary.match(pattern)){
        $("#modify_salary").submit();
    }else{
        alert('请输入正整数！');
    }
}
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>

</body>
