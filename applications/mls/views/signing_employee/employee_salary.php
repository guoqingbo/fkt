<body >
<div class="tab_box" id="js_tab_box">
    <?php
    echo $user_menu;
    ?>
</div>
<div id="js_search_box" class="shop_tab_title">
    <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
</div>
<form method='post' action='/company_employee/salary' id='search_form' name='search_form'>
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
            <p class="fg fg_tex"> 时间：</p>
            <div class="fg">
                <select class="select"  name='count_year' id="count_year">
                    <?php for($i = $now_year ; $i >= 2014 ; $i -- ){ ?>
                    <option value="<?php echo $i;?>" <?php if($count_year == $i){ echo 'selected'; } ?>>
                    <?php echo $i.'年';?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box" id = 'count_month_block' >
            <p class="fg fg_tex"> 月份：</p>
            <div class="fg">
                <select class="select"  name='count_month' id="count_month" >
                    <?php for($i = 1 ; $i <= 12 ; $i ++ ){ ?>
                    <option value="<?php echo $i;?>" <?php if($count_month == $i){ echo 'selected'; } ?>>
                    <?php echo $i.'月';?>
                    </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
            <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
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
                <td class="c10">年-月</td>
                <td class="c10">分店名称</td>
                <td class="c10">员工名称</td>
                <td class="c10">基本工资</td>
                <td class="c10">买卖提成</td>
                <td class="c10">租赁提成</td>
                <td class="c10">总提成</td>
                <td class="c10">总工资</td>
            </tr>
        </table>
    </div>
    <div class="inner" id="js_inner" style="height: 389px !important;">
        <table class="table list-table">
            <?php if($list){
                    foreach($list as $key=>$val) { ?>
                        <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
                            <td class="c10"><?php echo $show_date;?></td>
                            <td class="c10"><?php echo $val['name'];?></td>
                            <td class="c10"><?php echo $val['truename'];?></td>
                            <td class="c10"><?php echo $val['base_salary'];?></td>
                            <td class="c10"><?php echo $val['sell_price'];?></td>
                            <td class="c10"><?php echo $val['rent_price'];?></td>
                            <td class="c10"><?php echo $val['sell_price'] + $val['rent_price'];?></td>
                            <td class="c10"><?php echo $val['sell_price'] + $val['rent_price'] + $val['base_salary'];?></td>
                        </tr>
                    <?php } 
            } else { ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php } ?>
        </table>
    </div>
</div>
<script>
    function reset() {
        window.location.href = window.location.href;
        window.location.reload;
    }
</script>

</body>
