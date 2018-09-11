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
                            <form name="search_form" method="post" action="">
                                <div class="row">
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <input type="hidden" name="pg" value="1">
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
                                <th>序号</th>
                                <th>公司名称</th>
                                <th>分店名称</th>
                                <th>类型</th>
                                <th>费用</th>
                                <th>余额</th>
                                <th>添加时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($balance_list) && !empty($balance_list)) {
                                foreach ($balance_list as $k => $v) {
                                    ?>
                                    <tr class="gradeA">
                                        <td><?php echo $v['id']; ?></td>
                                        <td><?php echo $agency['company_name']; ?></td>
                                        <td><?php echo $agency['agency_name']; ?></td>
                                        <td><?php echo $type[$v['type']]; ?></td>
                                        <td><?php echo $v['fee']; ?></td>
                                        <td><?php echo $v['balance']; ?></td>
                                        <td><?php echo date('Y-m-d H:i:s', $v['create_time']); ?></td>
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
    function checkdel() {
        if (confirm("删除加入区域公盘的门店，将同时删除门店发送到公盘的房源，确实要删除吗？")) {
            return true;
        }
        else {
            return false;
        }
    }
</script>
<?php require APPPATH . 'views/footer.php'; ?>

