<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">添加区域公盘</h1>
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
                                    <div role="grid" class="dataTables_wrapper form-inline"
                                         id="dataTables-example_wrapper">
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
                                                    所属区域:&nbsp&nbsp&nbsp&nbsp
                                                    <select name="district_id" class="form-control input-sm"
                                                            aria-controls="dataTables-example">
                                                        <option value="0">请选择</option>
                                                        <?php foreach ($district_list as $key => $val) { ?>
                                                            <option value="<?php echo $val['id']; ?>"><?php echo $val['district']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <input type="hidden" name="district_name" value="">
                                                </label>
                                            </div>
                                        </div>
                                        <script>
                                            $(function () {
                                                $("select[name='district_id']").change(function () {
                                                    var district_name = $(this).find('option:selected').text();
                                                    $("input[name='district_name']").val(district_name)
                                                    $("input[name='name']").val(district_name)
                                                })
                                            })
                                        </script>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    公盘名称:&nbsp&nbsp&nbsp&nbsp<input type="search" name="name"
                                                                                    class="form-control input-sm"
                                                                                    aria-controls="dataTables-example"
                                                                                    value="" readonly>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    是否有效:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="status" value="0">无效</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="status" value="1"
                                                                                  checked="checked">有效</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    是否启用隐号拨打:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="hidden_call_able"
                                                                                  value="1">启用</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="hidden_call_able"
                                                                                  value="0"
                                                                                  checked="checked">禁止</label>
                                            </div>
                                        </div>
                                        <?php if (!empty($mess_error)) { ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <font color='red'><?php echo $mess_error; ?></font>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="submit" value="提交">
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
                window.location.href = "<?php echo MLS_ADMIN_URL . '/district_public/' ?>";
            }, 1000);
        });
    </script>
<?php } ?>

<script>
    function goback() {
        location.href = "<?=MLS_ADMIN_URL?>/district_public/";
    }
</script>

<?php require APPPATH . 'views/footer.php'; ?>

