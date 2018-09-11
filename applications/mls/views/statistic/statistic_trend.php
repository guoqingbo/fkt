<body>
<!--描述：导航栏开始 -->
<div class="tab_box" id="js_tab_box">
<?php echo $user_menu;?>
</div>
<!--描述：导航栏结束-->
<!--描述：选择区域开始-->
<form action = '<?php echo MLS_URL;?>/statistic/trend_statistic/' method = 'post' name = 'search_form' id ='search_form'>
<div class="search_box clearfix" id="js_search_box"> 
    <a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this)" data-h="0">展开<span class="iconfont">&#xe609;</span></a>
    <div class="fg_box">
        <p class="fg fg_tex">统计内容:</p>
        <div class="fg">
            <select class="select" name="field" id="field">
                <?php if($type == 1) {?>
                    <?php if(is_array($config['fields']) && !empty($config['fields'])){ ?>
                    <?php foreach ($config['fields'] as $key => $value) { ?>
                    <option value="<?php echo $key;?>" <?php if($key == $field){?> selected <?php }?>><?php echo $value;?></option>
                    <?php } ?>
                    <?php }?>
                <?php } else if($type == 2){?>
                    <?php if(is_array($config['fields_rent']) && !empty($config['fields_rent'])){ ?>
                    <?php foreach ($config['fields_rent'] as $key => $value) { ?>
                    <option value="<?php echo $key;?>" <?php if($key == $field){?> selected <?php }?>><?php echo $value;?></option>
                    <?php } ?>
                    <?php }?>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">类型:</p>
        <div class="fg">
            <select class="select" name="type" id ="type" onchange="change_field_name()">
                    <?php if(is_array($config['type']) && !empty($config['type'])){ ?>
                    <?php foreach ($config['type'] as $key => $value) { ?>
                    <option value="<?php echo $key;?>" <?php if($key == $type){?> selected <?php }?>><?php echo $value;?></option>
                    <?php } ?>
                    <?php }?>
            </select>
        </div>
    </div> 
    <div class="fg_box">
        <p class="fg fg_tex">统计维度:</p>
        <div class="fg">
            <select class="select"  name='unit_trend' id="unit_trend" onchange = "show_month()">
                <?php foreach($config['unit_trend'] as $key =>$value){ ?>
                <option value="<?php echo $key;?>" <?php if($unit_trend == $key){ echo 'selected'; } ?>>
                <?php echo $value;?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">时间:</p>
        <div class="fg">
            <select class="select"  name='count_year' id="count_year">
                <?php for($i = $now_year ; $i >= 2001 ; $i -- ){ ?>
                <option value="<?php echo $i;?>" <?php if($count_year == $i){ echo 'selected'; } ?>>
                <?php echo $i.'年';?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box" id ='count_month_block' <?php if($count_month >=1  && $unit_trend == 'day') {?> style = "display:inline;" <?php } else {?> style = "display:none;" <?php }?>>
        <p class="fg fg_tex">月份:</p>
        <div class="fg">
            <select class="select"  name='count_month' id="count_month" <?php if(empty($count_month)) {?> disabled="true" <?php }?>>
                <?php for($i = 1 ; $i <= 12 ; $i ++ ){ ?>
                <option value="<?php echo $i;?>" <?php if($count_month == $i){ echo 'selected'; } ?>>
                <?php echo $i.'月';?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">区域：</p>
        <div class="fg">
            <select class="select"  name='district' id="district">
                <?php foreach($district_arr as $key => $value){ ?>
                <option value="<?php echo $value['id'];?>" <?php if($district == $value['id']){ echo 'selected'; }else if($key == 0){ echo 'selected'; } ?>>
                <?php echo $value['district'];?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">面积：</p>
        <div class="fg">
            <select class="select"  name='buildarearea' id="buildarearea">
                <option value="0">全部</option>
                <?php foreach($config['sell_buildare'] as $key => $value){ ?>
                <option value="<?php echo $key;?>" <?php if($buildarearea == $key){ echo 'selected'; } ?>>
                <?php echo $value;?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box <?php echo $cond_show;?>">
        <p class="fg fg_tex">总价：</p>
        <div class="fg">
            <select class="select"  name='price' id="price">
                <option value="0">全部</option>
                 <?php if($type == 1) {?>
                <?php foreach($config['sell_price'] as $key => $value){ ?>
                <option value="<?php echo $key;?>" <?php if($price == $key){ echo 'selected'; } ?>>
                <?php echo $value;?>
                </option>
                <?php } ?>
                <?php } else if($type == 2){?>
                    <?php foreach ($config['rent_price'] as $key => $value) { ?>
                    <option value="<?php echo $key;?>" <?php if($key == $field){?> selected <?php }?>><?php echo $value;?></option>
                    <?php } ?>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="fg_box <?php echo $cond_show;?>">
        <p class="fg fg_tex">物业类型：</p>
        <div class="fg">
            <select class="select" name='infotype'>
                <option value="0">全部</option>
                 <?php if(is_array($conf_house['sell_type']) && !empty($conf_house['sell_type'])) { ?>
                    <?php foreach($conf_house['sell_type'] as $key => $value){ ?>
                    <option value='<?php echo $key;?>' <?php if($infotype == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                    <?php } ?>
                    <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box <?php echo $cond_show;?>">
        <p class="fg fg_tex">装修：</p>
        <div class="fg">
            <select class="select" name='fitment'>
                <option value="0">全部</option>
                 <?php if(is_array($conf_house['fitment']) && !empty($conf_house['fitment'])) { ?>
                    <?php foreach($conf_house['fitment'] as $key => $value){ ?>
                    <option value='<?php echo $key;?>' <?php if($fitment == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                    <?php } ?>
                    <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box <?php echo $cond_show;?>">
        <p class="fg fg_tex"> 户型：</p>
        <div class="fg">
            <select class="select" name='room_type' id="avgprice">
                <option value="0">全部</option>
                 <?php if(is_array($config['room_type']) && !empty($config['room_type'])) { ?>
                    <?php foreach($config['room_type'] as $key => $value){ ?>
                    <option value='<?php echo $key;?>' <?php if($room_type == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                    <?php } ?>
                    <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box <?php echo $cond_show;?>">
        <p class="fg fg_tex"> 单价：</p>
        <div class="fg">
            <select class="avgprice" name='avgprice'>
                <option value="0">全部</option>
                <?php if($type == 1) {?>
                 <?php if(is_array($config['sell_avgprice']) && !empty($config['sell_avgprice'])) { ?>
                    <?php foreach($config['sell_avgprice'] as $key => $value){ ?>
                    <option value='<?php echo $key;?>' <?php if($avgprice == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                    <?php } ?>
                    <?php } ?>
                <?php } else if($type == 2){?>
                    <?php foreach($config['rent_avgprice'] as $key => $value){ ?>
                    <option value='<?php echo $key;?>' <?php if($avgprice == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                    <?php } ?>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" onclick = "sub_statistic_form();return false;" class="btn" ><span class="btn_inner">统计</span></a> </div>
        <div class="fg"> <a href="javascript:void(0)" onclick = "reset_form()" class="reset">重置</a> </div>
    </div>
</div>
</form>
<!--描述：选择区域结束-->

<!--描述：主要内容区域开始-->
<div class="table_all">
  <div  id="js_inner">
  	
  </div>
</div>
<!--描述：主要内容区域结束-->

<script>
function sub_statistic_form()
{
    $('#search_form').submit();
}

function show_month ()
{
    if($('#unit_trend').val() == "day")
    {
        $('#count_month_block').show();
        $('#count_month').removeAttr("disabled");
    }
    else
    {
        $('#count_month_block').hide();
        $('#count_month').attr("disabled","true");
    }
}

function change_field_name()
{   
    var json_arr = '';
    var json_price_arr = '';
    var json_avg_price_arr = '';
    
    var type = $('#type').val();
    var json_field = <?php echo json_encode($config['fields']);?>;
    var json_rent_field = <?php  echo json_encode($config['fields_rent']);?>;
    var json_price = <?php echo json_encode($config['sell_price']);?>;
    var json_rent_price = <?php  echo json_encode($config['rent_price']);?>;
    var json_avgprice = <?php echo json_encode($config['sell_avgprice']);?>;
    var json_rent_avgprice = <?php  echo json_encode($config['rent_avgprice']);?>;

    if(type == 1)
    {
        json_arr = json_field;
        json_price_arr = json_price;
        json_avg_price_arr = json_avgprice;
    }
    else if(type == 2)
    {
        json_arr = json_rent_field;
        json_price_arr = json_rent_price;
        json_avg_price_arr = json_rent_avgprice;
    }
    
    if( type > 0)
    {   
        //查询字段
        $('#field').empty();
        $.each( json_arr, function(key, value) {   
            var child_option = child_option + "<option value="+key+">"+value+"</option>";
            $('#field').append(child_option);
        });
        
        var empty_option = "<option value='0'>全部</option>";
        
        //总价
        $('#price').empty();
        $("#price").prepend(empty_option);
        $.each( json_price_arr , function(key, value) {   
            var child_option = "<option value="+key+">"+value+"</option>";
            $('#price').append(child_option);
        });
        
        //均价
        $('#avgprice').empty();
        $("#avgprice").prepend(empty_option);
        $.each( json_avg_price_arr , function(key, value) {   
            var child_option = "<option value="+key+">"+value+"</option>";
            $('#avgprice').append(child_option);
        });
    }
}

$(function () { 
$('#js_inner').highcharts({
    chart: {type: 'line'},
    title: {text: '<?php echo $chart_title;?>', x: -20}, //指定图表标题
    xAxis: { categories: <?php echo $x_json;?> }, //指定x轴分组
    yAxis: { 
        title: { text: '' }, //指定y轴的标题     
        labels: {formatter:function(){return this.value+"<?php echo $show_suffix;?>";}}
    }, 
    tooltip: { pointFormat: '{point.y} <?php echo $show_suffix;?></b>'},
    legend: { layout: 'vertical', align: 'right', verticalAlign: 'middle', borderWidth: 0},
    credits : { enabled : false},  //不显示highCharts版权信息,不显示为false
    legend: {
        enabled : true,//控制显示
        verticalAlign: 'bottom',
        y: -30
    },
    series: [{
        name: "<?php echo $item_title;?>",
        marker: { symbol: 'square' },
        color	: '#227ac6',
        data: <?php echo $y_json;?>
        }]
    }); 
});
</script>
