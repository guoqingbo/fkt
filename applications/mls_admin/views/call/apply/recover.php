<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title; ?></h1>
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
                                <form name="search_form" id="form1" method="post" action="">
                                    <input type='hidden' name='submit_flag' value='recover'/>
                                    <div role="grid" class="dataTables_wrapper form-inline"
                                         id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length">
                                                <label>
                                                    确定要启用该门店的隐号拨打吗？
                                                </label>
                                            </div>
                                        </div>

                                        <?php if (!empty($mess_error)) { ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length" style="color:red;">
                                                    <?php echo $mess_error; ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="button" id="charge" value="提交">
                                                <input class="btn btn-primary" type="button" onclick="goback()" value="取消">
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
            <div>启用失败</div>
        <?php } else { ?>
            <div>启用成功</div>
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
        $(function () {
            setTimeout(function () {
                window.location.href = "<?php echo $formUrl; ?>";
            }, 1000);
        });
    </script>
<?php } ?>

<script>
    function goback() {
        location.href = "<?php echo $formUrl; ?>";
    }
    $(function(){
        $("#charge").click(function(){
            $("#form1").submit();
        });
    })
</script>

<?php require APPPATH . 'views/footer.php'; ?>

