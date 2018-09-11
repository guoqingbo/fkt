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
                                    <input type='hidden' name='submit_flag' value='add'/>
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
                                                    公司名称：<?php echo $agency['company_name']; ?>
                                                </label>
                                            </div>
                                            <div class="dataTables_length">
                                                <label>
                                                    分店名称：<?php echo $agency['agency_name']; ?>
                                                </label>
                                            </div>
                                            <div class="dataTables_length">
                                                <label>
                                                    使用号码总数：<?php echo $agency['phone_num']; ?>
                                                </label>
                                            </div>
                                            <div class="dataTables_length">
                                                <label>
                                                    减少号码个数：
                                                    <input name="phone_num" id="phone_num" value=""
                                                           class="input_text input_text_r w150 form-control input-sm"
                                                           type="text" style="height:30px; line-height: 30px;">
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
                                                <input class="btn btn-primary" type="button" id="apply" value="提交">
                                                <input class="btn btn-primary" type="button" onclick="goback()"
                                                       value="取消">
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
        $("#phone_num").blur(function(){
            var reg = /^[1-9][0-9]*$/, phone_num =$(this).val(), use_phone_num = Number(<?php echo $agency['phone_num']; ?>);
            if (!reg.test(phone_num)) {
                alert('请输入大于0的数字');
                return false;
            }
            phone_num = Number(phone_num);
            if (phone_num > use_phone_num) {
                alert('不能超过使用号码总数');
                return false;
            }
        });
        $("#apply").click(function(){
            var reg = /^[1-9][0-9]*$/, phone_num =$("#phone_num").val(), use_phone_num = Number(<?php echo $agency['phone_num']; ?>);
            if (!reg.test(phone_num)) {
                alert('减少号码个数必须是数字');
                return false;
            }
            phone_num = Number(phone_num);
            if (phone_num > use_phone_num) {
                alert('不能超过使用号码总数');
                return false;
            }
            $("#form1").submit();
        });
    })
</script>

<?php require APPPATH . 'views/footer.php'; ?>

