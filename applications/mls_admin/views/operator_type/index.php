<?php require APPPATH . 'views/header.php'; ?>
<link href="<?=MLS_SOURCE_URL ?>/min/?f=mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">操作日志类型列表</h1>
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
                                        <label>模块
                                            <select name="module_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                <option value="0">请选择</option>
                                                <?php foreach($module_list as $key=>$val){?>
                                                <option value="<?php echo $val['id'];?>" <?php if($val['id']==$post_param['module_id']){echo "selected='selected'";} ?>><?php echo $val['name'];?></option>
                                                <?php }?>
                                            </select>
                                        </label>
                                        <label>
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input type="hidden" name="pg" value="1">
                                                <input class="btn btn-primary" type="submit" value="查询">
                                                <a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/operator_type/add/'>添加</a>
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
                                    <th>权限模块</th>
                                    <th>操作名称</th>
                                    <th>功能</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($operator_type) && !empty($operator_type)) {
                                    foreach ($operator_type as $key => $value) {
                                        ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id']; ?></td>
                                            <td><?php echo $value['module_name']; ?></td>
                                            <td><?php echo $value['name']; ?></td>
                                            <td>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/operator_type/modify/<?php echo $value['id']; ?>" >修改</a>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/operator_type/del/<?php echo $value['id']; ?>" onclick="return checkdel()">删除</a>
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
<?php echo page_uri($page, $pages, MLS_ADMIN_URL . '/operator_type/index'); ?>
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
        if (confirm("确实要删除吗？"))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
</script>
<?php require APPPATH . 'views/footer.php'; ?>

