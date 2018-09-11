<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">修改区域公盘门店</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <?php if ('' == $modifyResult) {
            ; ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="">
                                    <input type='hidden' name='submit_flag' value='modify'/>
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
                                                    选择门店
                                                    <input name="agency_name" id="agency_name"
                                                           value="<?= $agency_ditrict_public["agency_name"]; ?>"
                                                           class="input_text input_text_r w150 form-control input-sm"
                                                           type="text" placeholder="输入门店名筛选"
                                                           style="height:30px; line-height: 30px;">
                                                    <input type="hidden" name="agency_id" id="agency_id"
                                                           value="<?= $agency_ditrict_public["agency_id"]; ?>">
                                                </label>
                                            </div>
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    门店所在区域
                                                    <input name="district_name" id="district_name"
                                                           value="<?= $agency_ditrict_public["district_name"]; ?>"
                                                           class="input_text input_text_r w150 form-control input-sm"
                                                           type="text" style="height:30px; line-height: 30px;" readonly>
                                                    <input type="hidden" name="district_id" id="district_id"
                                                           value="<?= $agency_ditrict_public["district_id"]; ?>">
                                                </label>
                                            </div>
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    所属区域公盘
                                                    <input name="cooperate_district_name" id="cooperate_district_name"
                                                           value="<?= $agency_ditrict_public["cooperate_district_name"]; ?>"
                                                           class="input_text input_text_r w150 form-control input-sm"
                                                           type="text" style="height:30px; line-height: 30px;" readonly>
                                                    <input type="hidden" name="cooperate_district_id"
                                                           id="cooperate_district_id"
                                                           value="<?= $agency_ditrict_public["cooperate_district_id"]; ?>">
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    是否有效:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="status"
                                                                                  value="0" <?php if ($agency_ditrict_public['status'] == 0) {
                                                        echo "checked='checked'";
                                                    } ?>>无效</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="status"
                                                                                  value="1" <?php if ($agency_ditrict_public['status'] == 1) {
                                                        echo "checked='checked'";
                                                    } ?>>有效</label>
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
        <?php } else if (0 === $modifyResult) { ?>
            <div>更新失败</div>
        <?php } else { ?>
            <div>更新成功</div>
        <?php } ?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


</div>
<!-- /#page-wrapper -->

</div>
<?php if ($modifyResult != "") { ?>
    <script>
        $(function () {
            setTimeout(function () {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/district_public_join/' ?>";
            }, 1000);
        });
    </script>
<?php } ?>

<script>
    function goback() {
        location.href = "<?=MLS_ADMIN_URL?>/district_public_join/";
    }
    $(function () {
        $("#agency_name").autocomplete({
            source: function (request, response) {
                var term = request.term;
                $.ajax({
                    url: "/district_public_join/get_agency_info_by_kw/",
                    type: "GET",
                    dataType: "json",
                    data: {
                        keyword: term
                    },
                    success: function (data) {
                        //判断返回数据是否为空，不为空返回数据。
                        if (data[0]['id'] != '0') {
                            response(data);
                        } else {
                            response(data);
                        }
                    }
                });
            },
            minLength: 1,
            removeinput: 0,
            select: function (event, ui) {
                if (ui.item.id > 0) {
                    var agency_name = ui.item.label;
                    var id = ui.item.id;
                    //操作
                    $("#agency_id").val(id);
                    $("#agency_name").val(agency_name);
                    get_agency_district(id);
                    removeinput = 2;
                } else {
                    removeinput = 1;
                }
            },
            close: function (event) {
                if (typeof(removeinput) == 'undefined' || removeinput == 1) {
                    $("#company_id").val("");
                    $("#company_name").val("");
                }
            }
        });
        function get_agency_district(agency_id) {
            $.ajax({
                type: 'POST',
                url: '<?php echo MLS_ADMIN_URL; ?>/district_public_join/get_agency_district/',
                dataType: 'json',
                data: {
                    agency_id: agency_id,
                },
                success: function (data) {

                    if (data.msg == "success") {
                        $('#district_name').val(data.district["district"]);
                        $('#district_id').val(data.district["id"]);
                        $('#cooperate_district_name').val(data.district_public["name"]);
                        $('#cooperate_district_id').val(data.district_public["id"]);
                    } else {
                        $('#agency_district').val("");
                        $('#cooperate_district_name').val("");
                        $('#cooperate_district_id').val("");
                        alert(data.msg);
                        return false;
                    }
                }
            });
        }
    })

</script>

<?php require APPPATH . 'views/footer.php'; ?>

