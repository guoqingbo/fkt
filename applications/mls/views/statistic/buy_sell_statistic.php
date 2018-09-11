<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>租赁成交统计</title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/guest_disk.css " rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo MLS_SOURCE_URL; ?>/min/?b=mls&f=css/v1.0/myStyle.css"/>
    <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
    <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
</head>

<body >
<div class="tab_box" id="js_tab_box">
    <a href="#" class="link link_on"><span class="iconfont">&#xe630;</span>我的报表</a>
    <a href="#" class="link"><span class="iconfont">&#xe631;</span>全程统计</a>
</div>
<div id="js_search_box" class="shop_tab_title  scr_clear">
    <a href="/statistic/msg_entering_statistic" class="link">信息录入统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/lease_statistic" class="link">租赁成交统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/buy_sell_statistic" class="link link_on">买卖成交统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="#" class="link">佣金参数统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/broker_action" class="link">员工行为统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/performance_rank" class="link">业绩排行统计<span class="iconfont hide">&#xe607;</span></a>
    <a href="/statistic/performance_detail" class="link">业绩明细统计<span class="iconfont hide">&#xe607;</span></a>
</div>
<div class="search_box clearfix" id="js_search_box">
    <form action="/statistic/lease_statistic" method="post" id="search_form">
        <div class="fg_box">
            <p class="fg fg_tex">分店：</p>
            <div class="fg fg-edit">
                <select class="select" id="agency_id" name="agency_id">
                    <option value="0">全部</option>
                    <?php foreach($agency_conf as $k=>$v) { ?>
                        <option value="<?php echo $v['id']; ?>" <?php if($post_param['agency_id'] == $v['id']){echo "selected";} ?>><?php echo $v['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">员工：</p>
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
            <p class="fg fg_tex">开始时间：</p>
            <div class="fg fg-edit">
                <input type="text" name="start_time" size="12" class="time_bg" onclick="WdatePicker()" value="<?php echo $post_param['start_time']; ?>"/>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">结束时间：</p>
            <div class="fg fg-edit">
                <input type="text" name="end_time" size="12" class="time_bg" onclick="WdatePicker()" value="<?php echo $post_param['end_time']; ?>"/>
            </div>
        </div>
        <div class="fg_box">
            <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="search_form.submit();"><span class="btn_inner">搜索</span></a> </div>
            <div class="fg"> <a href="javascript:void(0)" class="reset" onclick="search_form.reset();">重置</a> </div>
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
    <div class="inner" id="js_inner" style="height: 343px !important;">
        <table class="table list-table">
            <tr>
                <td class="c14">出售房源</td>
                <td colspan="6">
                    <table class="table inner-table">
                        <tr class="first">
                            <td class="c15"><?php echo intval(@$sell_house_data[1]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$sell_house_data[2]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$sell_house_data[3]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$sell_house_data[4]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$sell_house_data[5]['count(*)'])?></td>
                            <td class="c15"><?php echo intval(@$sell_house_data[0]['count(*)'] + @$sell_house_data[6]['count(*)'] );?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                        <tr class="second">
                            <td class="c15"><?php echo doubleval(@$sell_house_data[1]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_house_data[2]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_house_data[3]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_house_data[4]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$sell_house_data[5]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo (doubleval(@$sell_house_data[0]['ratio']) + doubleval(@$sell_house_data[6]['ratio']))."%"; ?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="bg">
                <td class="c14">成交房源</td>
                <td colspan="6">
                    <table class="table inner-table">
                        <tr class="first">
                            <td class="c15"><?php echo intval(@$sell_house_groups[1]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$sell_house_groups[2]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$sell_house_groups[3]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$sell_house_groups[4]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$sell_house_groups[5]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$sell_house_groups[0]['count(*)']) + intval(@$sell_house_groups[6]['count(*)']);?></td>
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
            <tr>
                <td class="c14">求购客户</td>
                <td colspan="6">
                    <table class="table inner-table">
                        <tr class="first">
                            <td class="c15"><?php echo intval(@$buy_customer_data[1]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$buy_customer_data[2]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$buy_customer_data[3]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$buy_customer_data[4]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$buy_customer_data[5]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$buy_customer_data[0]['count(*)']) + intval(@$buy_customer_data[6]['count(*)']);?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                        <tr class="second">
                            <td class="c15"><?php echo doubleval(@$buy_customer_data[1]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$buy_customer_data[2]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$buy_customer_data[3]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$buy_customer_data[4]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$buy_customer_data[5]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo (doubleval(@$buy_customer_data[0]['ratio']) + doubleval(@$buy_customer_data[6]['ratio']))."%"; ?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="bg">
                <td class="c14">成交客户</td>
                <td colspan="6">
                    <table class="table inner-table">
                        <tr class="first">
                            <td class="c15"><?php echo intval(@$buy_customer_groups[1]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$buy_customer_groups[2]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$buy_customer_groups[3]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$buy_customer_groups[4]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$buy_customer_groups[5]['count(*)']);?></td>
                            <td class="c15"><?php echo intval(@$buy_customer_groups[0]['count(*)']) + intval(@$buy_customer_groups[6]['count(*)']);?></td>
                            <td class="c10">&nbsp;</td>
                        </tr>
                        <tr class="second">
                            <td class="c15"><?php echo doubleval(@$buy_customer_groups[1]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$buy_customer_groups[2]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$buy_customer_groups[3]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$buy_customer_groups[4]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo doubleval(@$buy_customer_groups[5]['ratio'])."%"; ?></td>
                            <td class="c15"><?php echo (doubleval(@$buy_customer_groups[0]['ratio']) + doubleval(@$buy_customer_groups[6]['ratio']))."%"; ?></td>
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
