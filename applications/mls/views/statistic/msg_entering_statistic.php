<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>无标题文档</title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/guest_disk.css " rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/myStyle.css"/>
    <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
</head>

<body >
<div class="tab_box" id="js_tab_box">
    <a href="#" class="link link_on"><span class="iconfont">&#xe615;</span>我的报表</a>
    <a href="#" class="link"><span class="iconfont">&#xe61d;</span>全程统计</a>
</div>
<div id="js_search_box" class="shop_tab_title  scr_clear">
    <a href="/statistic/msg_entering_statistic" class="link link_on">信息录入统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/lease_statistic" class="link">租赁成交统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/buy_sell_statistic" class="link">买卖成交统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="#" class="link">佣金参数统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/broker_action" class="link">员工行为统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/performance_rank" class="link">业绩排行统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/performance_detail" class="link">业绩明细统计<span class="iconfont hide">&#xe607;</span></a>
</div>
<div class="search_box clearfix" id="js_search_box">
    <form action="/statistic/msg_entering_statistic" method="post" id="myform">
    <div class="fg_box">
        <div class="fg fg-edit">
            <select class="select" name="date_type">
                <option>日期类型</option>
                <option value="1" <?php if($post_param['date_type']==1) {echo "selected";} ?>>登记时间</option>
                <option value="2" <?php if($post_param['date_type']==1) {echo "selected";} ?>>最后跟进时间</option>
            </select>
        </div>
        <div class="fg fg-edit">
            <input type="text" name="s_time" size="12" class="time_bg" onclick="WdatePicker()" value="<?php echo $post_param['s_time']; ?>"/>&nbsp;—&nbsp;
            <input type="text" name="e_time" size="12" class="time_bg" onclick="WdatePicker()" value="<?php echo $post_param['e_time']; ?>"/>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">部门：</p>
        <div class="fg fg-edit">
            <select class="select" id="agency_id" name="agency_id">
                <option value="0">全部</option>
                <?php foreach($agency_conf as $k=>$v) { ?>
                    <option value="<?php echo $v['id']; ?>" <?php if($post_param['agency_id'] == $v['id']){echo "selected";} ?>><?php echo $v['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <p class="fg fg_tex">人员：</p>
        <div class="fg fg-edit">
            <select class="select" id="broker_id" name="broker_id">
                <option value="0">全部</option>
                <?php foreach ($brokers as $k=>$v) { ?>
                    <option <?php if($v['broker_id'] == $post_param['broker_id']) { echo "selected";}?>><?php echo $v['truename']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" onclick="myform.submit()" class="btn" ><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="javascript:void(0)" class="reset">重置</a> </div>
    </div>
    </form>
</div>
<div class="table_all report-form-wrap">
    <div class="title" id="js_title">
        <table class="table">
            <tr>
                <td class="c14">信息来源</td>
                <td class="c14">店面</td>
                <td class="c14">老客户</td>
                <td class="c14">广告</td>
                <td class="c14">社区推广</td>
                <td class="c14">网络</td>
                <td class="c14">其它</td>
            </tr>
        </table>
    </div>
    <div class="inner" id="js_inner" style="height: 342px !important;">
        <table class="table list-table">
            <tr>
                <td class="c14">出售房源</td>
                <td colspan="6">
                    <table class="table inner-table">
                        <tr class="first">
                            <td class="c15">
                                <?php echo intval(@$sell_house_groups[1]['count(*)']); ?>
                            </td>
                            <td class="c15">
                                <?php echo intval(@$sell_house_groups[2]['count(*)']); ?>
                            </td>
                            <td class="c15">
                                <?php echo intval(@$sell_house_groups[3]['count(*)']); ?>
                            </td>
                            <td class="c15">
                                <?php echo intval(@$sell_house_groups[4]['count(*)']); ?>
                            </td>
                            <td class="c15">
                                <?php echo intval(@$sell_house_groups[5]['count(*)']); ?>
                            </td>
                            <td class="c15">
                                <?php echo intval(@$sell_house_groups[0]['count(*)'] + @$sell_house_groups[6]['count(*)']); ?>
                            </td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                        <tr class="second">
                            <td class="c15"><?php echo doubleval(@$sell_house_groups[1]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_house_groups[2]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_house_groups[3]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_house_groups[4]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_house_groups[5]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo (doubleval(@$sell_house_groups[0]['ratio']) + doubleval(@$sell_house_groups[6]['ratio']))."%"; ?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="bg">
                <td class="c14">出租房源</td>
                <td colspan="6">
                    <table class="table inner-table">
                        <tr class="first">
                            <td class="c15"><?php echo intval(@$rent_house_groups[1]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$rent_house_groups[2]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$rent_house_groups[3]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$rent_house_groups[4]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$rent_house_groups[5]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$rent_house_groups[0]['count(*)'] + @$rent_house_groups[6]['count(*)']); ?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                        <tr class="second">
                            <td class="c15"><?php echo doubleval(@$rent_house_groups[1]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$rent_house_groups[2]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$rent_house_groups[3]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$rent_house_groups[4]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$rent_house_groups[5]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo (doubleval(@$rent_house_groups[0]['ratio']) + doubleval(@$rent_house_groups[6]['ratio']))."%"; ?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="c14">出售客源</td>
                <td colspan="6">
                    <table class="table inner-table">
                        <tr class="first">
                            <td class="c15"><?php echo intval(@$sell_customer_groups[1]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$sell_customer_groups[2]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$sell_customer_groups[3]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$sell_customer_groups[4]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$sell_customer_groups[5]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$sell_customer_groups[0]['count(*)'] + @$sell_customer_groups[6]['count(*)']); ?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                        <tr class="second">
                            <td class="c15"><?php echo doubleval(@$sell_customer_groups[1]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_customer_groups[2]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_customer_groups[3]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_customer_groups[4]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_customer_groups[5]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo (doubleval(@$sell_customer_groups[0]['ratio']) + doubleval(@$sell_customer_groups[6]['ratio']))."%"; ?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="bg">
                <td class="c14">出租客源</td>
                <td colspan="6">
                    <table class="table inner-table">
                        <tr class="first">
                            <td class="c15"><?php echo intval(@$rent_customer_groups[1]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$rent_customer_groups[2]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$rent_customer_groups[3]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$rent_customer_groups[4]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$rent_customer_groups[5]['count(*)']); ?></td>
                            <td class="c15"><?php echo intval(@$rent_customer_groups[0]['count(*)'] + @$rent_customer_groups[6]['count(*)']); ?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                        <tr class="second">
                            <td class="c15"><?php echo doubleval(@$rent_customer_groups[1]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$rent_customer_groups[2]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$rent_customer_groups[3]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$rent_customer_groups[4]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$rent_customer_groups[5]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo (doubleval(@$rent_customer_groups[0]['ratio']) + doubleval(@$rent_customer_groups[6]['ratio']))."%"; ?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js,scrollPic.js "></script>

<script>
    $(function(){
        /** 门店---经纪人联动 */
        $("#agency_id").change(function(){
            //先清空经纪人下拉列表
            $("#broker_id").html("<option value='0'>全部</option>");
            //AJAX请求数据并添加到相应位置
            var agency_id = $("select[name='agency_id']").val();
            $.post('/contract/get_broker_by_agency',{agency_id:agency_id},function(data){
                $.each(data,function(i,item){
                    $("#broker_id").append("<option value='"+ item.broker_id +"'>"+ item.truename +"</option>");
                });
            },'json');
        });
    });
</script>
</body>
</html>
