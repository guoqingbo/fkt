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
                                                <input type="text" name="company_name" id="company_name" value="<?php echo isset($param['company_name']) ? $param['company_name'] : ''; ?>" placeholder="请输入公司名" class="form-control"/>
                                                <input type="text" name="agency_name" id="agency_name" value="<?php echo isset($param['agency_name']) ? $param['agency_name'] : ''; ?>" placeholder="请输入门店名" class="form-control"/>
                                            </label>
                                            <label>
                                                <select name="type" aria-controls="dataTables-example" class="form-control input-sm">
                                                    <option value="">全部</option>
                                                    <?php foreach ($type as $k => $v) { ?>
                                                        <option value="<?php echo $k; ?>" <?php if (isset($param['type']) && $param['type'] == $k) echo 'selected="selected"'; ?>><?php echo $v; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </label>
                                            <label>
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <input type="hidden" name="pg" value="1">
                                                    <input class="btn btn-primary" type="submit" value="查询">
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
                                <th style="width:5%">序号</th>
                                <th style="width:10%">公司名称</th>
                                <th style="width:10%">分店名称</th>
                                <th style="width:10%">经纪人</th>
                                <th style="width:10%">经纪人手机号码</th>
                                <th style="width:10%">虚拟号码</th>
                                <th style="width:10%">业主手机号码</th>
                                <th style="width:10%">类型</th>
                                <th style="width:10%">通话时长 / 秒</th>
                                <th style="width:10%">发生金额 / 元</th>
                                <th style="width:10%">发生时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($fee_list) && !empty($fee_list)) {
                                foreach ($fee_list as $v) {
                                    ?>
                                    <tr class="gradeA">
                                        <td><?php echo $v['id']; ?></td>
                                        <td><?php echo $v['company_name']; ?></td>
                                        <td><?php echo $v['agency_name']; ?></td>
                                        <td><?php echo $v['truename']; ?></td>
                                        <td><?php echo $v['broker_phone']; ?></td>
                                        <td><?php echo $v['virtual_phone']; ?></td>
                                        <td><?php echo $v['house_phone']; ?></td>
                                        <td><?php echo isset($type[$v['type']]) ? $type[$v['type']] : ''; ?></td>
                                        <td><?php echo $v['phone_duration'] ? $v['phone_duration'] : ''; ?></td>
                                        <td><?php echo $v['fee']; ?></td>
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

