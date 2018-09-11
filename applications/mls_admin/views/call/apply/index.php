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
                                            <label>状态：
                                                <select name="status" aria-controls="dataTables-example" class="form-control input-sm">
                                                    <option value="">请选择</option>
                                                    <?php foreach ($status as $k => $v) { ?>
                                                        <option value="<?php echo $k; ?>" <?php if (isset($param['status']) && $param['status'] === $k) echo 'selected="selected"'; ?>><?php echo $v; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </label>
                                            <label>
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <input type="hidden" name="pg" value="1">
                                                    <input class="btn btn-primary" type="submit" value="查询">
                                                    <a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/call/apply/add?id=<?php echo $agency['id']; ?>'>增加号码个数</a>
                                                    <a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/call/apply/reduce?id=<?php echo $agency['id']; ?>'>减少号码个数</a>
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
                                <th>使用号码个数</th>
                                <th>月租费用</th>
                                <th>添加时间</th>
                                <th>开通时间</th>
                                <th>到期时间</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($agency_list) && !empty($agency_list)) {
                                foreach ($agency_list as $k => $v) {
                                    ?>
                                    <tr class="gradeA">
                                        <td><?php echo $v['id']; ?></td>
                                        <td><?php echo $agency['company_name']; ?></td>
                                        <td><?php echo $agency['agency_name']; ?></td>
                                        <td><?php echo $v['phone_num']; ?></td>
                                        <td><?php echo $v['monthly_fee']; ?></td>
                                        <td><?php echo date('Y-m-d H:i:s', $v['create_time']); ?></td>
                                        <td><?php echo empty($v['start_time']) ? '' : date('Y-m-d', $v['start_time']); ?></td>
                                        <td><?php echo empty($v['end_time']) ? '' : date('Y-m-d', $v['end_time']); ?></td>
                                        <td><?php echo $status[$v['status']]; ?></td>
                                        <td>
                                        <?php if (0 == $v['status']) { ?>
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/call/apply/charge?id=<?php echo $v['id']; ?>&call_agency_id=<?php echo $agency['id']; ?>">扣月租费</a>&nbsp;&nbsp;
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/call/apply/edit?id=<?php echo $v['id']; ?>&call_agency_id=<?php echo $agency['id']; ?>">修改</a>&nbsp;&nbsp;
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/call/apply/delete?id=<?php echo $v['id']; ?>&call_agency_id=<?php echo $agency['id']; ?>" onclick="if (!confirm('确定要删除吗？')) return false;">删除</a>
                                        <?php } elseif (2 == $v['status']) { ?>
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/call/apply/recover?id=<?php echo $v['id']; ?>">启用</a>
                                        <?php } elseif (-1 == $v['status']) { ?>
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/call/apply/delete?id=<?php echo $v['id']; ?>&call_agency_id=<?php echo $agency['id']; ?>" onclick="if (!confirm('确定要删除吗？')) return false;">删除</a>
                                        <?php } ?>
                                        </td>
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

