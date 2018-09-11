<?php require APPPATH . 'views/header.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/mls/css/v1.0/select2.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/mls/js/v1.0/select2.js"></script>
<style type="text/css">
    .ui-menu {
        width: 180px !important;
    }
</style>
<script type="text/javascript">
    function get_agency(companyId) {
        $.ajax({
            type: 'get',
            url: '<?php echo MLS_ADMIN_URL; ?>/agency/get_agency_ajax/' + companyId,
            dataType: 'json',
            success: function (msg) {
                var str = '';
                if (msg === '') {
                    str = '<option value="">请选择</option>';
                } else {
                    str = '<option value="">请选择</option>';
                    for (var i = 0; i < msg.length; i++) {
                        str += '<option value="' + msg[i].id + '">' + msg[i].name + '</option>';
                    }
                }
                $('#agency_id').empty();
                $('#agency_id').append(str);
            }
        });
    }
    $(function () {
        $("#agency_id").select2();
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
                    get_agency(id);
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
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $title ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <form name="search_form" id="search_form" method="post" action="">
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>设置查询条
                                                    <select name="search_where" aria-controls="dataTables-example"
                                                            class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <?php foreach ($where_config['search_where'] as $k => $v) { ?>
                                                            <option value="<?= $k ?>" <?php if ((!empty($where_cond['search_where']) && $where_cond['search_where'] == $k)) {
                                                                echo 'selected="selected"';
                                                            } ?>><?= $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label>
                                                    包含<input type='search' class="form-control input-sm" size='12'
                                                             name="search_value"
                                                             value="<?php if (!empty($where_cond['search_value'])) {
                                                                 echo $where_cond['search_value'];
                                                             } ?>"/>
                                                </label>
                                                <label>帐号有效性
                                                    <select name="search_status" class="form-control input-sm">
                                                        <option value="99">请选择</option>
                                                        <option value="1"
                                                                <?php if ($where_cond['search_status'] == 1) { ?>selected<?php } ?>>
                                                            有效
                                                        </option>
                                                        <option value="2"
                                                                <?php if ($where_cond['search_status'] == 2) { ?>selected<?php } ?>>
                                                            失效
                                                        </option>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>&nbsp;所属公司&nbsp;
                                                    <input name="company_name" id="company_name"
                                                           value="<?= $where_cond['company_name'] ?>"
                                                           class="input_text_r form-control input-sm" type="text"
                                                           placeholder="输入汉字筛选"
                                                           style="height:30px; line-height: 30px; width:180px;">
                                                    <input type="hidden" name="company_id" id="company_id"
                                                           value="<?= $where_cond['company_id'] ?>">
                                                </label>
                                                <label>&nbsp;所属门店&nbsp;
                                                    <select name="agency_id" id="agency_id"
                                                            aria-controls="dataTables-example">
                                                        <option value="0">请选择</option>
                                                        <?php if ($agencys) {
                                                            foreach ($agencys as $v) { ?>
                                                                <option value="<?= $v['id'] ?>" <?php if ((!empty($where_cond['agency_id']) && $where_cond['agency_id'] == $v['id'])) {
                                                                    echo 'selected="selected"';
                                                                } ?>><?= $v['name'] ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" name="pg" value="1">
                                                        <input class="btn btn-primary"
                                                               onclick="$('#search_form').attr('action', '/stat_effective_house/index/')"
                                                               type="submit" value="查询">

                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>手机号码</th>
                                <th>真实姓名</th>
                                <th>门店名称</th>
                                <th>公司名称</th>
                                <th>积分</th>
                                <th>有效房源量/总房源量（出售）</th>
                                <th>有效房源量/总房源量（出租）</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($broker_info) && !empty($broker_info)) {
                                foreach ($broker_info as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['broker_id']; ?></td>
                                        <td><?php echo $value['phone']; ?></td>
                                        <td><?php echo $value['truename']; ?></td>
                                        <td><?php echo $value['agency_name']; ?></td>
                                        <td><?php echo $value['company_name']; ?></td>
                                        <td><?php echo $value['credit']; ?></td>
                                        <td><?php echo $value['effective_sell_num']; ?>
                                            / <?php echo $value['tatal_sell_num']; ?></td>
                                        <td><?php echo $value['effective_rent_num']; ?>
                                            / <?php echo $value['tatal_rent_num']; ?></td>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6 clearfix" style="width:100%;">
                                <span style="float:right; color:blue;padding-right:20px"><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $count; ?>
                                        &nbsp;条数据！</b></span>
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
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#company_id').change(function () {
            var companyId = $(this).val();
            $.ajax({
                type: 'get',
                url: '<?php echo MLS_ADMIN_URL; ?>/company/get_agency_ajax/' + companyId,
                dataType: 'json',
                success: function (msg) {
                    var str = '';
                    if (msg === '') {
                        str = '<option value="">请选择</option>';
                    } else {
                        str = '<option value="">请选择</option>';
                        for (var i = 0; i < msg.length; i++) {
                            str += '<option value="' + msg[i].id + '">' + msg[i].name + '</option>';
                        }
                    }
                    $('#agency_id').empty();
                    $('#agency_id').append(str);
                }
            });
        });
    });
</script>
<?php require APPPATH . 'views/footer.php'; ?>

<!--遮罩-->
<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/loading.gif" id="mainloading">

<script type="text/javascript">
    window.onload = function () {
        $(function () {
            $("#wrapper").css({"height": ($("body").height()) + "px", "overflow-y": "auto"});
            $("#page-wrapper").css("min-height", "auto");
            $(window).resize(function () {
                $("#wrapper").css({"height": ($("body").height()) + "px", "overflow-y": "auto"});
                $("#page-wrapper").css("min-height", "auto");
            })

        })
    }
</script>
