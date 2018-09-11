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
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <input type="hidden" name="pg" value="1"/>
                                                    <input type="hidden" name="export" id="export" value=""/>
                                                    <input class="btn btn-primary import" type="button" value="导入"/>
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
                                <th style="width:5%">序号</th>
                                <th style="width:10%">虚拟号码</th>
                                <th style="width:10%">添加时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($phone_list) && !empty($phone_list)) {
                                foreach ($phone_list as $v) {
                                    ?>
                                    <tr class="gradeA">
                                        <td><?php echo $v['id']; ?></td>
                                        <td><?php echo $v['virtual_phone']; ?></td>
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
$(function(){
    $('.export').click(function(){
        $('#export').val(1);
        $('#search_form').submit();
        $('#export').val("");
    })
    $('.import').click(function(){
        layer.open({
            type: 2,
            title: '导入虚拟号码',
            shadeClose: false,
            maxmin: true, //开启最大化最小化按钮
            area: ['425px', '320px'],
            content: '/call/phone/import'
            /*
            end: function(){
                $("#search_form").submit();
            }
            */
        });
    })
})
</script>
<?php require APPPATH . 'views/footer.php'; ?>

