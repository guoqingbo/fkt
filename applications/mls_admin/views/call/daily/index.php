<?php require APPPATH . 'views/header.php'; ?>
<link href="<?= MLS_SOURCE_URL ?>/min/?f=mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title; ?></h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                            <form name="search_form" id="search_form" method="post" action="">
                                <div class="row">
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                <input type="text" name="company_name" id="company_name" value="<?php echo isset($param['company_name']) ? $param['company_name'] : ''; ?>" placeholder="请输入公司名" class="form-control"/>
                                                <input type="text" name="agency_name" id="agency_name" value="<?php echo isset($param['agency_name']) ? $param['agency_name'] : ''; ?>" placeholder="请输入门店名" class="form-control"/>
                                            </label>
                                            <label>日期：
                                                <input style="width:183px" type="text" name="start_time" id="start_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo isset($param['start_time']) ? $param['start_time'] : ''; ?>" onclick="WdatePicker()"/> 到 <input style="width:183px" type="text" id="end_time" name="end_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo isset($param['end_time']) ? $param['end_time'] : ''; ?>" onclick="WdatePicker()"/>
                                            </label>
                                            <label>
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <input type="hidden" name="pg" value="1">
                                                    <input type="hidden" name="export" id="export" value=""/>
                                                    <input class="btn btn-primary" type="submit" value="查询">
                                                    <input class="btn btn-primary export" type="button" value="导出"/>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- /.panel-heading -->

                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th style="width:5%">结算日期</th>
                                <th style="width:10%">公司名称</th>
                                <th style="width:10%">分店名称</th>
                                <th style="width:10%">充值金额</th>
                                <th style="width:10%">消费金额</th>
                                <th style="width:10%">账户余额</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($fee_list) && !empty($fee_list)) {
                                foreach ($fee_list as $v) {
                                    ?>
                                    <tr class="gradeA">
                                        <td><?php echo date('Y-m-d', $v['statis_time']); ?></td>
                                        <td><?php echo $v['company_name']; ?></td>
                                        <td><?php echo $v['agency_name']; ?></td>
                                        <td><?php echo $v['recharge_amount']; ?></td>
                                        <td><?php echo $v['consume_amount']; ?></td>
                                        <td><?php echo $v['balance']; ?></td>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page, $pages); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->

        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


</div>
<!-- /#page-wrapper -->

</div>
<script>
$(function(){
    $('.export').click(function(){
        $('#export').val(1);
        $('#search_form').submit();
        $('#export').val("");
    })
})
</script>
<?php require APPPATH . 'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>

