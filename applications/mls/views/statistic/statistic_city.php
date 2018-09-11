<!--页面部分-->
<script>
    window.parent.addNavClass(8);
</script>
<body>
<!--描述：导航栏开始 -->
<div class="tab_box" id="js_tab_box">
<?php echo $user_menu;?>
</div>
<!--描述：导航栏结束-->
<!--描述：选择区域开始-->
<form action = '<?php echo MLS_URL;?>/statistic/city_statistic/' method = 'post' name = 'search_form' id ='search_form'>
<div class="search_box clearfix" id="js_search_box">
    <div class="fg_box">
        <p class="fg fg_tex">统计内容：</p>
        <div class="fg">
            <select class="select"  name="field">
                <?php if(is_array($config['fields']) && !empty($config['fields'])){ ?>
                <?php foreach ($config['fields'] as $key => $value) { ?>
                <option value="<?php echo $key;?>" <?php if($key == $field){?> selected <?php }?>><?php echo $value;?></option>
                <?php } ?>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 类型：</p>
        <div class="fg">
            <select class="select" name="type">
                <?php if(is_array($config['type']) && !empty($config['type'])){ ?>
                <?php foreach ($config['type'] as $key => $value) { ?>
                <option value="<?php echo $key;?>" <?php if($key == $type){?> selected <?php }?>><?php echo $value;?></option>
                <?php } ?>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 单位：</p>
        <div class="fg">
            <select class="select" name="unit">
                <?php if(is_array($config['unit']) && !empty($config['unit'])){ ?>
                <?php foreach ($config['unit'] as $key => $value) { ?>
                <option value="<?php echo $key;?>" <?php if($key == $unit){?> selected <?php }?>><?php echo $value;?></option>
                <?php } ?>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 开始时间：</p>
         <div class="fg">
         <input readonly type="text" class="Wdate input w100 time_bg" style='cursor:pointer' name='start_day' value='<?php echo $start_day;?>' onFocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd',minDate:'%y-%M-{%d-30}',maxDate:'%y-%M-{%d-1}'})"/>
         </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">结束时间：</p>
         <div class="fg">
            <input readonly type="text" class="Wdate input w100 time_bg" style='cursor:pointer' name='end_day' value='<?php echo $end_day;?>'  onFocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd',minDate:'%y-%M-{%d-30}',maxDate:'%y-%M-{%d-1}'})"/>
         </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="sub_statistic_form();return false;" ><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="javascript:void(0)" class="reset">重置</a> </div>
    </div>
</div>


<!--描述：主要内容区域开始-->
<div class="table_all">
  <div  id="js_inner">
  </div>
</div>
</form>
<!--描述：选择区域结束-->
<!--描述：主要内容区域结束-->
<script src="<?php echo MLS_SOURCE_URL;?>/common/third/My97DatePicker/WdatePicker.js"> </script>
<script>
function sub_statistic_form()
{
    $('#search_form').submit();
}

$(function () {
$('#js_inner').highcharts({
    chart: {type: 'column'},
    title: {text: '<?php echo $chart_title;?>', x: -20}, //指定图表标题
    xAxis: { categories: <?php echo $x_json;?> }, //指定x轴分组
    yAxis: {
        title: { text: '' }, //指定y轴的标题
        labels: {formatter:function(){return this.value+"<?php echo $show_suffix;?>";}}
    },
    tooltip: { pointFormat: '{point.y} <?php echo $show_suffix;?></b>'},
    legend: { layout: 'vertical', align: 'right', verticalAlign: 'middle', borderWidth: 0 },
    credits : { enabled:false },  //不显示highCharts版权信息,不显示为false
    legend: {
        enabled : true,//控制显示
        verticalAlign: 'bottom',
        y: -30
    },
    series: [{
        name: "<?php echo $item_title;?>",
        marker: { symbol: 'square'},
        color	: '#227ac6',
        data: <?php echo $y_json;?>
        }]
    });
});
</script>
