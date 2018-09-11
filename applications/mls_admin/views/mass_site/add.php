<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">添加群发站点</h1>
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
                                <form name="search_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <input type='hidden' name='submit_flag' value='add'/>
                                                    网站名称:&nbsp&nbsp&nbsp&nbsp<input type="search" name="name" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    网站别名:&nbsp&nbsp&nbsp&nbsp<input type="search" name="alias" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>&nbsp&nbsp&nbsp&nbsp<span style="color:red;">用作群发。例如：58同城(http://nj.58.com/)，请填写 <span style='font-size: 20px;color:blue;'>“58”</span> ;安居客(http://nanjing.anjuke.com/)，请填写 <span style='font-size: 20px;color:blue;'>“anjuke”</span> 。若有疑问，请咨询技术人员。</span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    网站介绍:&nbsp&nbsp&nbsp&nbsp<input type="search" name="intro" class="form-control input-sm" aria-controls="dataTables-example" value="" size="60">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    网站地址:&nbsp&nbsp&nbsp&nbsp<input type="search" name="url" class="form-control input-sm" aria-controls="dataTables-example" value="" size="60">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    是否处于维护中:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_fix" value="0" checked="checked">否</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_fix" value="1">是</label>
                                                &nbsp&nbsp&nbsp&nbsp<span style="color:red;">默认选否，如果因群发规则改变，则改为是，即系统处于维护中。</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    是否&nbsp&nbsp&nbsp&nbsp已经认证:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_auth" value="0" checked="checked">否</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_auth" value="1">是</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    是否&nbsp&nbsp&nbsp&nbsp可以传图:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_upic" value="0" checked="checked">否</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_upic" value="1">是</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    是否&nbsp&nbsp&nbsp&nbsp可以刷新:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_refresh" value="0" checked="checked">否</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_refresh" value="1">是</label>
                                            </div>
                                        </div>
                                        <?php if (!empty($mess_error)) { ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <span style='color: red;font-size: 25px;'><?php echo $mess_error; ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
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
                window.location.href = "<?php echo MLS_ADMIN_URL . '/mass_site/' ?>";
            }, 1000);
        });
    </script>
<?php } ?>

<script>
	function	goback(){
		location.href = "<?=MLS_ADMIN_URL?>/mass_site/";
	}
</script>

<?php require APPPATH . 'views/footer.php'; ?>

