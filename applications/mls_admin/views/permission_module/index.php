<?php require APPPATH . 'views/header.php'; ?>
<link href="<?=MLS_SOURCE_URL ?>/min/?f=mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?></h1>
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
                                                <a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/permission_module/add/'>添加</a>
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
                                    <th>权限链接地址</th>
                                    <th>默认权限</th>
                                    <th>功能</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($permission_module) && !empty($permission_module)) {
                                    foreach ($permission_module as $key => $value) {
                                        ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id']; ?></td>
                                            <td><?php echo $value['name']; ?></td>
                                            <td><?php echo $value['url']; ?></td>
                                            <td><?php if($value['init_auth']){echo "是";}else{echo "<span style='color:red'>否</span>";} ?></td>
                                            <td>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/permission_module/modify/<?php echo $value['id']; ?>" >修改</a>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/permission_module/del/<?php echo $value['id']; ?>" onclick="return checkdel()">删除</a>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/permission_menu/index/<?php echo $value['id']; ?>">模块菜单</a>
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
                                        <?php echo page_uri($page, $pages, MLS_ADMIN_URL . '/permission_module/index'); ?>
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
        if (confirm("删除权限模块将一并删除菜单及如下所有的功能，确实要删除吗？"))
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

