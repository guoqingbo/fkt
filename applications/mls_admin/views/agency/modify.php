<?php require APPPATH . 'views/header.php'; ?>
<script type="text/javascript">
    $(function () {
        $("#company_name").autocomplete({
            source: function (request, response) {
                var term = request.term;
                $.ajax({
                    url: "/company/get_company_by_kw/",
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
                    var company_name = ui.item.label;
                    var id = ui.item.id;
                    //操作
                    $("#company_id").val(id);
                    $("#company_name").val(company_name);
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
    });
</script>
<div id="wrapper">
    <div id="page-wrapper">
        <?php if ($modifyResult === '') { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default" style="margin:20px 0; padding:20px 0;">
                        <form name="add_form" method="post" action="" class="container">
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
                                            总公司<font color="red">*</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input name="company_name" id="company_name"
                                                   value="<?= $agency['company_name'] ?>"
                                                   class="input_text input_text_r w150 form-control input-sm"
                                                   type="text" placeholder="输入汉字筛选"
                                                   style="height:30px; line-height: 30px;">
                                            <input type="hidden" name="company_id" id="company_id"
                                                   value="<?= $agency['company_id'] ?>">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <label>
                                        门店类型:&nbsp&nbsp&nbsp&nbsp
                                        <select name="agency_type" class="form-control input-sm" style="width:155px">
                                            <option value="0" <?php echo $agency['agency_type'] == 0 ? 'selected' : ''; ?>>
                                                请选择
                                            </option>
                                            <option value="1" <?php echo $agency['agency_type'] == 1 ? 'selected' : ''; ?>>
                                                直营
                                            </option>
                                            <option value="2" <?php echo $agency['agency_type'] == 2 ? 'selected' : ''; ?>>
                                                加盟
                                            </option>
                                            <option value="3" <?php echo $agency['agency_type'] == 3 ? 'selected' : ''; ?>>
                                                合作
                                            </option>
                                        </select>
                                    </label>
                                </div>
                                <?php if (!empty($code_img_url)) { ?>
                                    <img style="position:absolute; top:20px; right: 33px;" width="125" height="125"
                                         src="<?php echo $code_img_url; ?>">
                                <?php } ?>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            区属<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                            <select id="district" name="dist_id" aria-controls="dataTables-example"
                                                    class="form-control input-sm" style="width:155px">
                                                <option value="0">请选择</option>
                                                <?php foreach ($district as $k => $v) { ?>
                                                    <option value="<?php echo $v['id'] ?>"<?php if ($v['id'] == $agency['dist_id']) {
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo $v['district'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </label>
                                        <label>
                                            &nbsp&nbsp板块:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                            <select id="street" name="streetid" aria-controls="dataTables-example"
                                                    class="form-control input-sm" style="width:155px">
                                                <?php foreach ($agency['street_arr'] as $k => $v) { ?>
                                                    <option value="<?php echo $v['id'] ?>"<?php if ($v['id'] == $agency['street_id']) {
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo $v['streetname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            分店<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input
                                                    type="search" name="name" class="form-control input-sm"
                                                    aria-controls="dataTables-example" value="<?= $agency['name'] ?>">
                                        </label>
                                        <label>
                                            &nbsp&nbsp分店电话:&nbsp&nbsp&nbsp&nbsp<input type="search" name="telno"
                                                                                      class="form-control input-sm"
                                                                                      aria-controls="dataTables-example"
                                                                                      value="<?= $agency['telno'] ?>">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            分店地址:&nbsp&nbsp<input type="search" name="address"
                                                                  class="form-control input-sm"
                                                                  aria-controls="dataTables-example"
                                                                  value="<?= $agency['address'] ?>" size="62">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            客户经理:&nbsp&nbsp&nbsp&nbsp
                                            <select name="master_id" class="form-control input-sm" style="width:155px">
                                                <option value="">请选择</option>
                                                <?php foreach ($masters as $v) { ?>
                                                    <option value="<?php echo $v['uid'] ?>" <?php if ($v['uid'] == $agency['master_id']) {
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo $v['truename'] ?></option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" name="old_master_id"
                                                   value="<?= $agency['master_id'] ?>">
                                        </label>
                                    </div>
                                </div>
                                <!--
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    默认用户挂靠的门店:
                                                </label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="init" value="0" <?php if ($agency['init'] == 0) {
                                    echo 'checked="checked"';
                                } ?>>否</label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="init" value="1" <?php if ($agency['init'] == 1) {
                                    echo 'checked="checked"';
                                } ?>>是</label>
                                            </div>
                                        </div>-->
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
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="submit_flag" value="modify">
                        </form>

                        <p>
                            <br/><?php echo MLS_ADMIN_URL . '/' . $_SESSION[WEB_AUTH]["city"] . '/broker_info/agency_house/' . $agency['id']; ?>
                        </p>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } else if (0 === $modifyResult) { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default" style="margin:20px 0; padding:20px 0;">
                        <h1><b>修改失败</b></h1>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default" style="margin:20px 0; padding:20px 0;">
                        <h1><b>修改成功</b></h1>
                    </div>
                </div>
            </div>
        <?php } ?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>
</div>
<div class="col-lg-4" style="display:none" id="js_note1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            提示框
            <button type="button" class="close JS_Close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="panel-body">
            <p id="warning_text"></p>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#district').change(function () {
            var districtID = $(this).val();
            $.ajax({
                type: 'get',
                url: '<?php echo MLS_ADMIN_URL; ?>/community/find_street_bydis/' + districtID,
                dataType: 'json',
                success: function (msg) {
                    var str = '';
                    if (msg.result == 'no result') {
                        str = '<option value="">请选择</option>';
                    } else {
                        str = '<option value="">请选择</option>';
                        for (var i = 0; i < msg.length; i++) {
                            str += '<option value="' + msg[i].id + '">' + msg[i].streetname + '</option>';
                        }
                    }
                    $('#street').empty();
                    $('#street').append(str);
                }
            });
        });
    });
</script>
<?php require APPPATH . 'views/footer.php'; ?>

