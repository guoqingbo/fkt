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
                                                <input type="text" name="telephone" id="telephone" value="<?php echo isset($param['telephone']) ? $param['telephone'] : ''; ?>" placeholder="请输入用户联系方式" class="form-control"/>
                                                <select name="status" aria-controls="dataTables-example" class="form-control input-sm">
                                                    <option value="">请选择</option>
                                                    <option value="0" <?php if (isset($param['status']) && $param['status'] === '0') echo 'selected="selected"'; ?>>未处理</option>
                                                    <option value="1" <?php if (isset($param['status']) && $param['status'] == 1) echo 'selected="selected"'; ?>>上架</option>
                                                    <option value="-1" <?php if (isset($param['status']) && $param['status'] == -1) echo 'selected="selected"'; ?>>无效</option>
                                                </select>
                                            </label>
                                            <label>
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <input type="hidden" name="pg" value="1"/>
                                                    <input type="hidden" name="export" id="export" value=""/>
                                                    <input class="btn btn-primary" type="submit" value="查询"/>
                                                    <input class="btn btn-primary export" type="button" value="导出"/>
                                                    <a class="btn btn-primary" href="/userhouse/index/serverphone">客服电话</a>
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
                                <th style="width:5%;">序号</th>
                                <th style="width:10%;">用户联系方式</th>
                                <th style="width:5%;">称呼</th>
                                <th style="width:10%;">小区名称</th>
                                <th style="width:8%;">发布状态</th>
                                <th style="width:12%;">发布时间</th>
                                <th style="width:12%;">处理时间</th>
                                <th style="width:20%;">备注</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($house_list)) { ?>
                                <tr class="gradeA">
                                    <td colspan="9">没有数据</td>
                                </tr>
                            <?php } else {foreach ($house_list as $k => $v) { ?>
                                <tr class="gradeA">
                                    <td><?php echo $v['id']; ?></td>
                                    <td><?php echo $v['telephone']; ?></td>
                                    <td><?php echo $v['user_name']; ?></td>
                                    <td><?php echo $v['block_name']; ?></td>
                                    <td><?php echo isset($status[$v['status']]) ? $status[$v['status']] : ''; ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', $v['create_time']); ?></td>
                                    <td><?php echo empty($v['update_time']) ? '' : date('Y-m-d H:i:s', $v['update_time']); ?></td>
                                    <td><?php echo $v['remark']; ?></td>
                                    <td>
                                    <?php if (empty($v['status'])) { ?>
                                        <a href="<?php echo MLS_ADMIN_URL; ?>/userhouse/index/cancel?id=<?php echo $v['id']; ?>">设置为无效房源</a>&nbsp;&nbsp;
                                        <a href="<?php echo MLS_ADMIN_URL; ?>/userhouse/index/edit?id=<?php echo $v['id']; ?>">发布房源</a>
                                    <?php } ?>
                                    </td>
                                </tr>
                            <?php }} ?>
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