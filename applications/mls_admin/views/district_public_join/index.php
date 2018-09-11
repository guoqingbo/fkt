<?php require APPPATH . 'views/header.php'; ?>
<link href="<?= MLS_SOURCE_URL ?>/min/?f=mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">加入公盘门店列表</h1>
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
                                            <label>区域
                                                <select name="district_id" aria-controls="dataTables-example"
                                                        class="form-control input-sm">
                                                    <option value="0">请选择</option>
                                                    <?php foreach ($district_list as $key => $val) { ?>
                                                        <option value="<?php echo $val['id']; ?>" <?php if ($val['id'] == $cond_where['district_id']) {
                                                            echo "selected='selected'";
                                                        } ?>><?php echo $val['district']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </label>
                                            <label>
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <input type="hidden" name="pg" value="1">
                                                    <input class="btn btn-primary" type="submit" value="查询">
                                                    <a class="btn btn-primary"
                                                       href='<?php echo MLS_ADMIN_URL; ?>/district_public_join/add/'>添加</a>
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
                                <th>门店名称</th>
                                <th>所在公盘名</th>
                                <th>创建时间</th>
                                <th>更新时间</th>
                                <th>是否有效</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($agency_join_list) && !empty($agency_join_list)) {
                                foreach ($agency_join_list as $key => $value) {
                                    ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id']; ?></td>
                                        <td><?php echo $value['agency_name']; ?></td>
                                        <td><?php echo $value['cooperate_district_name']; ?></td>
                                        <td><?php echo date('Y-m-d', $value['createtime']); ?></td>
                                        <td><?php echo date('Y-m-d', $value['updatetime']); ?></td>

                                        <td><?php if ($value['status'] == 1) {
                                                echo "有效";
                                            } else {
                                                echo "<span style='color:red'>无效</span>";
                                            } ?></td>
                                        <td>
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/district_public_join/modify/<?php echo $value['id']; ?>">修改</a>
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/district_public_join/del/<?php echo $value['id']; ?>"
                                               onclick="return checkdel()">删除</a>
                                            <!--                                            <a href="-->
                                            <?php //echo MLS_ADMIN_URL; ?><!--/district_public_join/agency/-->
                                            <?php //echo $value['id']; ?><!--/-->
                                            <?php //echo $value['id']; ?><!--">查看门店</a>-->
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
                                        <?php echo page_uri($page, $pages, MLS_ADMIN_URL . '/permission_tab_menu/index'); ?>
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

