<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">城市拓展情况统计</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>经纪人ID</th>
                                    <th>经纪人姓名</th>
                                    <th>公司名</th>
                                    <th>门店名</th>
									<th>登录次数</th>
									<th>出售房源发布量</th>
									<th>出租房源发布量</th>
									<th>出售采集查看量</th>
									<th>出租采集查看量</th>
									<th>出售房源群发量</th>
									<th>出租房源群发量</th>
									<th>统计时间</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($stat_broker_data) && !empty($stat_broker_data)) {
                                foreach ($stat_broker_data as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['broker_id']; ?></td>
										<td><?php echo $value['truename']; ?></td>
										<td><?php echo $value['company']; ?></td>
										<td><?php echo $value['agency']; ?></td>
										<td><?php echo $value['login_num']; ?></td>
										<td><?php echo $value['sell_publish_num']; ?></td>
										<td><?php echo $value['rent_publish_num']; ?></td>
										<td><?php echo $value['sell_collect_view_num']; ?></td>
                                        <td><?php echo $value['rent_collect_view_num']; ?></td>
                                        <td><?php echo $value['sell_group_publish_num']; ?></td>
										<td><?php echo $value['rent_group_publish_num']; ?></td>
                                        <td><?php echo $value['ymd'];?></td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6 clearfix" style="width:100%;">
                                <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $count;?>&nbsp;条数据！</b></span>
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/stat_broker/');?>
                                    </ul>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPPATH . 'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
