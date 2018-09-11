<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?></h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <?php if ('' == $addResult) { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="" method="post" action="">
<!--                                    <form name="search_form" method="post" action="--><?php //echo MLS_ADMIN_URL; ?><!--/signatory_purview_module/add/">-->
                                    <div role="grid" class="dataTables_wrapper form-inline" id="">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="">
                                                <label>
                                                    权限名称:&nbsp&nbsp&nbsp&nbsp<input type="search" name="name" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="">
                                                <label>
                                                    链接地址:&nbsp&nbsp&nbsp&nbsp<input type="search" name="url" class="form-control input-sm" aria-controls="dataTables-example" value="" size="60">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="">
                                                <label>
                                                    样式:&nbsp&nbsp&nbsp&nbsp<input type="search" name="style" class="form-control input-sm" aria-controls="dataTables-example" value="" size="60">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="">
                                                <label>
                                                    默认权限:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="init_auth" value="0">无</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="init_auth" value="1" checked="checked">有</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="">
                                                <label>
                                                    排序数值:&nbsp&nbsp&nbsp&nbsp<input type="search" name="order" class="form-control input-sm" aria-controls="dataTables-example" value="" size="10">
                                                数值越大越靠前
                                                </label>
                                            </div>
                                        </div>
                                        <?php if (!empty($mess_error)) { ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="">
                                                    <font color='red'><?php echo $mess_error; ?></font>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="">
                                                <input type='hidden' name='submit_flag' value='add'/>
                                                <input class="btn btn-primary" type="submit" value="提交">
                                                <input class="btn btn-primary"  type="button" onclick="goback()" value="取消">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } else if (0 === $addResult) { ?>
            <div>插入失败</div>
        <?php } else { ?>
            <div>插入成功</div>
        <?php } ?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->



</div>
<!-- /#page-wrapper -->

</div>
<?php if ($addResult != "") { ?>
    <script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/signatory_purview_module/' ?>";
            }, 1000);
        });
    </script>
<?php } ?>

<script>
	function	goback(){
		location.href = "<?=MLS_ADMIN_URL?>/signatory_purview_module/";
	}
</script>

<?php require APPPATH . 'views/footer.php'; ?>

