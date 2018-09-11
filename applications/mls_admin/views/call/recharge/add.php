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
                                                    公司名称：
                                                    <input name="company_name" id="company_name" value=""
                                                           class="input_text input_text_r w150 form-control input-sm"
                                                           type="text" placeholder="输入公司名筛选"
                                                           style="height:30px; line-height: 30px;">
                                                    <input type="hidden" name="company_id" id="company_id" value="">
                                                </label>
                                            </div>
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    分店名称：
                                                    <input name="agency_name" id="agency_name" value=""
                                                           class="input_text input_text_r w150 form-control input-sm"
                                                           type="text" placeholder="输入门店名筛选"
                                                           style="height:30px; line-height: 30px;">
                                                    <input type="hidden" name="agency_id" id="agency_id" value="">
                                                </label>
                                            </div>
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    充值金额：
                                                    <input name="fee" id="fee"
                                                           value=""
                                                           class="input_text input_text_r w150 form-control input-sm"
                                                           type="text" style="height:30px; line-height: 30px;">元
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
            <div>充值失败</div>
        <?php } else { ?>
            <div>充值成功</div>
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
        var old_company_id;
        $("#company_name").autocomplete({
            source: function( request, response ) {
                var term = request.term;
                old_company_id = $("#company_id").val();
                removeinput = 1;
                $.ajax({
                    url: "/call/recharge/get_company_by_kw",
                    type: "GET",
                    dataType: "json",
                    data: {
                        keyword: term
                    },
                    success: function(data) {
                        //判断返回数据是否为空，不为空返回数据。
                        if( data[0]['id'] != '0'){
                            response(data);
                        }else{
                            response(data);
                        }
                    }
                });
            },
            minLength: 1,
            removeinput: 0,
            select: function(event,ui) {
                if(ui.item.id > 0){
                    var company_name = ui.item.label;
                    var id = ui.item.id;
                    //操作
                    $("#company_id").val(id);
                    $("#company_name").val(company_name);
                    if (old_company_id != id) {
                        $("#agency_id").val("");
                        $("#agency_name").val("");
                    }
                    removeinput = 2;
                }else{
                    removeinput = 1;
                }
            },
            close: function(event) {
                /*if(typeof(removeinput)=='undefined' || removeinput == 1){
                    $("#company_id").val("");
                    $("#company_name").val("");
                }*/
            },
            change: function(event, ui) {
                if ($("#company_id").val() != '' && removeinput == 1) {
                    $("#company_id").val("");
                    $("#agency_id").val("");
                    $("#agency_name").val("");
                }
            }
        });
        $("#agency_name").autocomplete({
            source: function (request, response) {
                if ($("#company_id").val() == '') {
                    alert('请先选择公司');
                    $("#agency_name").val("");
                    return false;
                }
                var term = request.term;
                $.ajax({
                    url: "/call/recharge/get_agency_info_by_kw",
                    type: "GET",
                    dataType: "json",
                    data: {
                        keyword: term,
                        company_id: $("#company_id").val()
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
                    removeinput = 2;
                } else {
                    removeinput = 1;
                }
            },
            close: function (event) {
                if (typeof(removeinput) == 'undefined' || removeinput == 1) {
                    $("#agency_id").val("");
                    $("#agency_name").val("");
                }
            }
        });
        $("#apply").click(function(){
            var reg = /^[0-9]+(\.[0-9]{1,2})?$/, fee =$("#fee").val();
            if ($("#company_id").val() == '') {
                alert('请先选择公司');
                return false;
            }
            if ($("#agency_id").val() == '') {
                alert('请选择门店');
                return false;
            }
            if (!reg.test(fee)) {
                alert('充值金额必须是数字，小数点后最多两位');
                return false;
            }
            $("#form1").submit();
        });
    })
</script>

<?php require APPPATH . 'views/footer.php'; ?>

