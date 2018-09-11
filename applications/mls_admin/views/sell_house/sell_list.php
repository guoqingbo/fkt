<?php require APPPATH . 'views/header.php'; ?>
<script type="text/javascript" src="<?= MLS_SOURCE_URL ?>/min/?f=common/third/jquery-ui-1.9.2.custom.min.js"></script>
<link href="<?= MLS_SOURCE_URL ?>/mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<link href="<?= MLS_SOURCE_URL ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<link href="<?= MLS_SOURCE_URL ?>/mls/css/v1.0/autocomplete.css" rel="stylesheet" type="text/css">

<style>
    tr {
        text-align: center;
    }

    .ui-menu {
        background: none repeat scroll 0 0 #fff;
        border: 1px solid #d1d1d1;
        float: left;
        border-top: none;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .ui-menu .ui-menu-item {
        list-style: none;
        background: none repeat scroll 0 0 #fff;
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .ui-menu .ui-menu-item a {
        color: #333;
        cursor: pointer;
        display: block;
        font-family: Arial, Helvetica, sans-serif;
        height: 24px;
        line-height: 24px;
        overflow: hidden;
        padding: 0 4px;
        text-align: left;
        text-decoration: none;
    }

    .ui-menu .ui-menu-item a.ui-state-hover, .ui-menu .ui-menu-item a.ui-state-active {
        background: none repeat scroll 0 0 #ff9804;
        color: #fff;
        font-weight: normal;
        text-decoration: none;
    }


</style>

<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">出售房源</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <form name="search_form" method="post" action="" id="search_form">
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            房源编号：&nbsp;<input type="text" name="id" id="id" style="width:183px"
                                                              class="form-control input-sm"
                                                              aria-controls="dataTables-example" value="<?php
                                            if (isset($post_param['id'])) {
                                                echo $post_param['id'];
                                            }
                                            ?>" onclick="add_content1()"><span id='reminder1'
                                                                               style='font-weight:bold;color:red;'></span>
                                            房源内部编号：&nbsp;<input type="text" name="order_sn" id="order_sn"
                                                                style="width:183px" class="form-control input-sm"
                                                                aria-controls="dataTables-example" value="<?php
                                            if (isset($post_param['order_sn'])) {
                                                echo $post_param['order_sn'];
                                            }
                                            ?>" onclick="add_content2()"><span id='reminder2'
                                                                               style='font-weight:bold;color:red;'></span>

                                            状态：&nbsp;
                                            <select name="status">
                                                <option value="">不限</option>
                                                <?php
                                                foreach ($config['status'] as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $key; ?>" <?php
                                                    if ($post_param['status'] == $key) {
                                                        echo "selected";
                                                    }
                                                    ?> ><?php echo $value; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>

                                            性质：&nbsp;
                                            <select name="nature">
                                                <option value="">不限</option>
                                                <?php
                                                foreach ($config['nature'] as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $key; ?>" <?php
                                                    if ($post_param['nature'] == $key) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $value; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>

                                            物业类型：&nbsp;
                                            <select name="sell_type">
                                                <option value="">不限</option>
                                                <?php
                                                foreach ($config['sell_type'] as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $key; ?>" <?php
                                                    if ($post_param['sell_type'] == $key) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $value; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>

                                            合作状态：&nbsp;
                                            <select name="isshare">
                                                <option value="">不限</option>
                                                <?php
                                                $isshare_conf = array(1 => '合作中');
                                                foreach ($isshare_conf as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $key; ?>" <?php
                                                    if ($post_param['isshare'] == $key) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $value; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-sm-6" style="width:100%">
                                        区域：&nbsp;
                                        <select class="select" id='district' name='district_id'
                                                onchange="districtchange(this.value);">
                                            <option value="">不限</option>
                                            <?php
                                            foreach ($district as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value['id']; ?>" <?php if ($value['id'] == $post_param['district_id']) {
                                                    echo ' selected';
                                                } ?>><?php echo $value['district']; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        板块：&nbsp;
                                        <select class="select" name='street' id='street'>
                                            <option value='0'>不限</option>
                                            <?php
                                            if ($post_param['district_id'] > 0) {
                                                foreach ($street as $k => $v) {
                                                    if ($v['dist_id'] == $post_param['district_id']) {
                                                        echo "<option value='" . $v['id'] . "'";
                                                        if ($v['id'] == $post_param['street'])
                                                            echo " selected ";
                                                        echo ">" . $v['streetname'] . "</option>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>

                                        楼盘：&nbsp;<input type="text" name="block_name" id="block_name"
                                                        style="width:183px" class="form-control input-sm"
                                                        aria-controls="dataTables-example" value="<?php
                                        if (isset($_POST['block_name'])) {
                                            echo $_POST['block_name'];
                                        }
                                        ?>" onclick="add_content3()"><span id='reminder3'
                                                                           style='font-weight:bold;color:red;'></span>
                                        <input name="block_id" id="block_id" value="<?php
                                        if (isset($_POST['block_id'])) {
                                            echo $_POST['block_id'];
                                        }
                                        ?>" type="hidden">
                                        <script type="text/javascript">
                                            $(function () {
                                                $("#block_name").autocomplete({
                                                    source: function (request, response) {
                                                        var term = request.term;
                                                        $("#block_id").val("");
                                                        $.ajax({
                                                            url: "/community/get_cmtinfo_by_kw/",
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
                                                            var blockname = ui.item.label;
                                                            var id = ui.item.id;
                                                            var streetid = ui.item.streetid;
                                                            var streetname = ui.item.streetname;
                                                            var dist_id = ui.item.dist_id;
                                                            var districtname = ui.item.districtname;
                                                            var address = ui.item.address;

                                                            //操作
                                                            $("#block_id").val(id);
                                                            $("#block_name").val(blockname);
                                                            removeinput = 2;
                                                        } else {
                                                            openWin('js_pop_add_new_block');
                                                            removeinput = 1;
                                                        }
                                                    },
                                                    close: function (event) {
                                                        if (typeof (removeinput) == 'undefined' || removeinput == 1) {
                                                            $("#block_name").val("");
                                                            $("#block_id").val("");
                                                        }
                                                    }
                                                });
                                            });
                                        </script>

                                        户型：&nbsp;
                                        <select name="room">
                                            <option value="">不限</option>
                                            <?php
                                            foreach ($config['room'] as $key => $value) {
                                                ?>
                                                <option value="<?php echo $key; ?>" <?php
                                                if ($post_param['room'] == $key) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $value; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>

                                        面积:&nbsp;
                                        <input type="text" name="min_area" id="min_area" style="width:53px"
                                               class="form-control input-sm" aria-controls="dataTables-example"
                                               value="<?php
                                               if (isset($_POST['min_area'])) {
                                                   echo $_POST['min_area'];
                                               }
                                               ?>" onclick="add_content3()"> 至 <input type="text" name="max_area"
                                                                                      id="max_area" style="width:53px"
                                                                                      class="form-control input-sm"
                                                                                      aria-controls="dataTables-example"
                                                                                      value="<?php
                                                                                      if (isset($_POST['max_area'])) {
                                                                                          echo $_POST['max_area'];
                                                                                      }
                                                                                      ?>" onclick="add_content3()"> 平米

                                        总价:&nbsp;
                                        <input type="text" name="min_price" id="min_price" style="width:53px"
                                               class="form-control input-sm" aria-controls="dataTables-example"
                                               value="<?php
                                               if (isset($_POST['min_price'])) {
                                                   echo $_POST['min_price'];
                                               }
                                               ?>" onclick="add_content3()"> 至 <input type="text" name="max_price"
                                                                                      id="max_price" style="width:53px"
                                                                                      class="form-control input-sm"
                                                                                      aria-controls="dataTables-example"
                                                                                      value="<?php
                                                                                      if (isset($_POST['max_price'])) {
                                                                                          echo $_POST['max_price'];
                                                                                      }
                                                                                      ?>" onclick="add_content3()"> 万元
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            所属公司&nbsp;&nbsp;
                                            <input name="company_name" id="company_name"
                                                   value="<?php if (isset($_POST['company_name'])) {
                                                       echo $_POST['company_name'];
                                                   } ?>" class="input_text input_text_r w150 form-control input-sm"
                                                   type="text" placeholder="输入汉字筛选"
                                                   style="height:30px; line-height: 30px;">
                                            <input type="hidden" name="company_id" id="company_id"
                                                   value="<?php if (isset($_POST['company_id'])) {
                                                       echo $_POST['company_id'];
                                                   } ?>">
                                            &nbsp;&nbsp;所属门店&nbsp;
                                            <select name="agency_id" id="agency_id" aria-controls="dataTables-example"
                                                    class="form-control input-sm">
                                                <option value="0">请选择</option>
                                                <?php if ($agencys) {
                                                    foreach ($agencys as $v) { ?>
                                                        <option value="<?= $v['id'] ?>" <?php if ((!empty($_POST['agency_id']) && $_POST['agency_id'] == $v['id'])) {
                                                            echo 'selected="selected"';
                                                        } ?>><?= $v['name'] ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
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

                                            <select name="type">
                                                <option value="broker_id" <?php
                                                if ($post_param['type'] == 'broker_id') {
                                                    echo "selected";
                                                }
                                                ?>>经纪人帐号
                                                </option>
                                                <option value="broker_name" <?php
                                                if ($post_param['type'] == 'broker_name') {
                                                    echo "selected";
                                                }
                                                ?>>经纪人姓名
                                                </option>
                                                <option value="phone" <?php
                                                if ($post_param['type'] == 'phone') {
                                                    echo "selected";
                                                }
                                                ?>>联系方式
                                                </option>
                                            </select>
                                            <input type="text" name="search_value" id="search_value" style="width:183px"
                                                   class="form-control input-sm" aria-controls="dataTables-example"
                                                   value="
<?php
                                                   if (isset($_POST['search_value'])) {
                                                       echo $_POST['search_value'];
                                                   }
                                                   ?>" onclick="add_content3()"><span id='reminder3'
                                                                                      style='font-weight:bold;color:red;'></span>

                                            是否为推荐房源: <select name="recommend_house">

                                                <option value="0">不限</option>
                                                <option value="1" <?php if ($post_param['recommend_house'] == 1) {
                                                    echo "selected";
                                                } ?>>
                                                    是
                                                </option>
                                                <option value="2" <?php if ($post_param['recommend_house'] == 2) {
                                                    echo "selected";
                                                } ?>>
                                                    否
                                                </option>
                                            </select>
                                            是否为喜欢房源:
                                            <select name="is_like_house">
                                                <option value="0">不限</option>
                                                <option value="1" <?php if ($post_param['is_like_house'] == 1) {
                                                    echo "selected";
                                                } ?>>
                                                    是
                                                </option>
                                                <option value="2" <?php if ($post_param['is_like_house'] == 2) {
                                                    echo "selected";
                                                } ?>>
                                                    否
                                                </option>
                                            </select>
                                            查询结果按照: &nbsp;
                                            <select name="order_type">
                                                <option value="createtime" <?php
                                                if ($post_param['order_type'] == 'createtime') {
                                                    echo "selected";
                                                }
                                                ?>>发布时间
                                                </option>
                                                <option value="broker_id" <?php
                                                if ($post_param['order_type'] == 'broker_id') {
                                                    echo "selected";
                                                }
                                                ?>>经纪人帐号
                                                </option>
                                            </select>

                                            <select name="order_value">

                                                <option value="DESC" <?php
                                                if ($post_param['order_value'] == 'DESC') {
                                                    echo "selected";
                                                }
                                                ?>>倒序排列
                                                </option>
                                                <option value="ASC" <?php
                                                if ($post_param['order_value'] == 'ASC') {
                                                    echo "selected";
                                                }
                                                ?>>正序排列
                                                </option>
                                            </select>

                                            每页显示&nbsp;<select name="pages">
                                                <option value="10" <?php if ($pagesize == '10') {
                                                    echo "selected";
                                                } ?>>10
                                                </option>
                                                <option value="30" <?php if ($pagesize == '30') {
                                                    echo "selected";
                                                } ?>>30
                                                </option>
                                                <option value="40" <?php if ($pagesize == '40') {
                                                    echo "selected";
                                                } ?>>40
                                                </option>
                                                <option value="50" <?php if ($pagesize == '50') {
                                                    echo "selected";
                                                } ?>>50
                                                </option>
                                            </select>条数据
                                        </div>

                                        <br>
                                    </div>
                                    <input type="hidden" name="angela_wen" value="angel_in_us">
                                    <input type="hidden" name="pg" value="<?php echo $page; ?>">
                                    <input class="btn btn-primary" type="submit" value="查询">&nbsp;&nbsp;&nbsp;<input
                                            class="btn btn-primary" type="button"
                                            onclick="javascript:location.href = '/sell_house/'" value="重置">
                                    <!--<a class="btn btn-primary" href="/sell_house/add">添加</a>-->
                            </form>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th>房源编号</th>
                                <th>状态</th>
                                <th>物业类型</th>
                                <th>楼盘</th>
                                <th>户型</th>
                                <th>面积(㎡)</th>
                                <th>楼层</th>
                                <th>装修</th>
                                <th>报价(W)</th>
                                <th>所在门店</th>
                                <th>经纪人姓名</th>
                                <th>联系方式</th>
                                <th>委托时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($sell_list) && !empty($sell_list)) {
                                foreach ($sell_list as $key => $value) {
                                    ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id']; ?></td>
                                        <td><?php echo $value['status']; ?></td>
                                        <td><?php
                                            switch ($value['sell_type']) {
                                                case '1':
                                                    echo '住宅';
                                                    break;
                                                case '2':
                                                    echo '别墅';
                                                    break;
                                                case '3':
                                                    echo '商铺';
                                                    break;
                                                case '4':
                                                    echo '写字楼';
                                                    break;
                                                case '5':
                                                    echo '厂房';
                                                    break;
                                                case '6':
                                                    echo '仓库';
                                                    break;
                                                case '7':
                                                    echo '车库';
                                                    break;
                                                default:
                                                    echo '--';
                                                    break;
                                            }
                                            ?></td>
                                        <td><?php echo $value['block_name']; ?></td>
                                        <td><?php echo $value['room'] . '-' . $value['hall'] . '-' . $value['toilet']; ?></td>
                                        <td><?php echo floor($value['buildarea']) == $value['buildarea'] ? intval($value['buildarea']) : $value['buildarea']; ?></td>
                                        <td><?php echo $value['floor']; ?></td>
                                        <td><?php echo $value['fitment']; ?></td>
                                        <td><?php echo floor($value['price']) == $value['price'] ? intval($value['price']) : $value['price']; ?></td>
                                        <td><?php echo $value['agency_name']; ?></td>
                                        <td><?php echo $value['broker_name']; ?></td>
                                        <td><?php echo $value['phone']; ?></td>
                                        <td><?php echo date('Y-m-d H:i:s', $value['createtime']); ?></td>
                                        <td>
                                            <?php if ($value['recommend_house_id'] > 0) { ?>
                                                <a class="btn btn-danger btn-xs"
                                                   onclick="cancel_recommend_house(<?php echo $value['id']; ?>,<?php echo $value['recommend_house_id']; ?>)">取消优质房源</a>
                                            <?php } else { ?>
                                                <a class="btn btn-primary btn-xs"
                                                   onclick="add_recommend_house(<?php echo $value['id']; ?>)">设为优质房源</a>
                                            <?php } ?>

                                            <?php if ($value['is_set_like'] == 1) { ?>
                                                <a class="btn btn-danger btn-xs"
                                                   onclick="cancel_like_house(<?php echo $value['id']; ?>,<?php echo $value['recommend_house_id']; ?>)">取消喜欢房源</a>
                                            <?php } else { ?>
                                                <a class="btn btn-primary btn-xs"
                                                   onclick="add_like_house(<?php echo $value['id']; ?>)">设为喜欢房源</a>
                                            <?php } ?>


                                            <!--                                                <a href="-->
                                            <?php //echo MLS_ADMIN_URL; ?><!--/sell_house/del_sell/-->
                                            <?php //echo $value['id']; ?><!--">关联失效</a>-->
                                            <!--                                                --><?php //if('0'==$value['is_kft']){ ?>

                                            <!--                                                    <a href="#" onclick="add_data(-->
                                            <!--                                                    --><?php //echo $value['id']; ?>
                                            <!--                                                    -->
                                            <?php //echo $value['price']; ?><!--//);">-->
                                            <!--//                                                       同步快房通-->
                                            <!--                                                   </a>-->
                                            <?php //}else{ ?>
                                            <!--                                                    <a href="#" style="color:black;">已同步</a>-->
                                            <!--                                                --><?php //} ?>

                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr class='gradeA'><td colspan=15 style='text-align:center;color:red;font-weight:bold;'>暂无您查询的出售房源数据~！</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-6" style='display:none;'>
                                <div class="dataTables_info" id="dataTables-example_info" role="alert"
                                     aria-live="polite" aria-relevant="all"><input type="checkbox" id="sel-all">&nbsp;&nbsp;全选
                                    &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal1"
                                                   data-toggle="modal">加入白名单</a> &nbsp;&nbsp;<a
                                            href="javascript:void(0)" data-target="#myModal"
                                            data-toggle="modal">标记到推送库</a> &nbsp;&nbsp;<a href="javascript:void(0)"
                                                                                          data-target="#myModal2"
                                                                                          data-toggle="modal">标记到备选库</a>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page, $pages, MLS_ADMIN_URL . '/user/index'); ?>
                                    </ul>
                                </div>
                            </div>
                            <div style="color:blue;position:absolute;right:33px;">
                                <b>共查到<?php echo $sold_num; ?>条数据</b>
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
<!-- /#wrapper -->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="margin:200px auto">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 id="myModalLabel" class="modal-title">标记到推送库</h4>
            </div>
            <div class="modal-body">确定加入推送库？
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                <button class="btn btn-primary" type="button" id="addpush">添加推送</button>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal1" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="margin:200px auto">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 id="myModalLabel" class="modal-title">白名单</h4>
            </div>
            <div class="modal-body">白名单原因:
                <select class="input-sm" aria-controls="dataTables-example" id="kind" name="kind">
                    <option value="1">公司内部人士</option>
                    <option value="2">经纪人</option>
                </select>
                备注
                <input type="search" name="remark" id="remark" value="">
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                <button class="btn btn-primary" type="button" id="addwhite">确定</button>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal2" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="margin:200px auto">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 id="myModalLabel" class="modal-title">备选库</h4>
            </div>
            <div class="modal-body">确定加入备选库？

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                <button class="btn btn-primary" type="button" id="addalternatives">确定</button>
            </div>
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
<script type="text/javascript">
    function add_data(house_id, price) {
        layer.open({
            type: 2,
            title: '同步快房通',
            shadeClose: false,
            maxmin: true, //开启最大化最小化按钮
            area: ['750px', '400px'],
            content: '/sell_house/kft/' + house_id + '/' + price,
            end: function () {
                $("#search_form").submit();
            }
        });
    }

    //房源列表页 区属联动
    function districtchange(districtid) {
        $.ajax({
            type: 'get',
            url: '/sell_house/find_street_bydis/' + districtid,
            dataType: 'json',
            success: function (msg) {
                var str = '';
                if (msg.result == 'no result') {
                    str = '<option value="">不限</option>';
                } else {
                    str = '<option value="">不限</option>';
                    for (var i = 0; i < msg.length; i++) {
                        str += '<option value="' + msg[i].id + '">' + msg[i].streetname + '</option>';
                    }
                }
                $('#street').empty();
                $('#street').append(str);
            }
        });
    }

    //    function add_content1() {
    //        $('#reminder1').html('&nbsp;&nbsp;请输入要查询的房源编号!');
    //    }
    //    function add_content2() {
    //        $('#reminder2').html('&nbsp;&nbsp;请输入要查询的交易编号!');
    //    }
    //    function add_content3() {
    //        $('#reminder3').html('&nbsp;&nbsp;请输入要查询的经纪人姓名!');
    //    }
    //    function add_content4() {
    //        $('#reminder4').html('&nbsp;&nbsp;请输入要查询的经纪人编号!');
    //    }

    function change_price(id, rand) {
        var cid = 'real_price' + rand;
        var obj = $("#" + cid);
        var real_price = obj.val().replace(/\s+/g, "");  //获取值并去空格

        if (real_price == "") {
            alert('请输入真实成交总价~！');
            $("#" + cid).focus();
            return;
        }
        else if (isNaN(real_price)) {
            alert('真实成交总价必须为数字~！');
            $("#" + cid).focus();
            return;
        }
        else {
            //ajax 改变 cooperate 表里的 real_price
            $.ajax({
                type: 'get',
                url: '<?=MLS_ADMIN_URL?>/sell_house_sold/change_real_price',
                data: {'id': id, 'real_price': real_price},
                dataType: 'json',
                success: function (msg) {
                    if (msg == '123') {
                        alert('改动失败，请稍后重试~！');
                        location.href = '<?=MLS_ADMIN_URL?>/sell_house_sold/';
                        return;
                    } else {
                        location.href = '<?=MLS_ADMIN_URL?>/sell_house_sold/';
                        return;
                    }
                }
            });
        }
    }

    //设为优质房源（推送至金品app）
    function add_recommend_house(house_id) {
        $.ajax({
            type: 'POST',
            url: '<?=MLS_ADMIN_URL?>/sell_house/add_recommend_house',
            data: {'house_id': house_id},
            dataType: "json",
            success: function (data) {
                if (data.status == "success") {
                    layer.msg(data.msg);
                    location.href = '<?=MLS_ADMIN_URL?>/sell_house/';
                } else {
                    layer.msg(data.msg);
                }
            }
        })
    }
    //取消优质房源（推送至金品app）
    function cancel_recommend_house(house_id, recommend_house_id) {
        $.ajax({
            type: 'POST',
            url: '<?=MLS_ADMIN_URL?>/sell_house/cancel_recommend_house',
            data: {'house_id': house_id, 'recommend_house_id': recommend_house_id},
            dataType: "json",
            success: function (data) {
                if (data.status == "success") {
                    layer.msg(data.msg);
                    location.href = '<?=MLS_ADMIN_URL?>/sell_house/';
                } else {
                    layer.msg('操作失败');
                }
            }
        })
    }

    //设为喜欢房源（推送至金品app）
    function add_like_house(house_id) {
        $.ajax({
            type: 'POST',
            url: '<?=MLS_ADMIN_URL?>/sell_house/add_like_house',
            data: {'house_id': house_id},
            dataType: "json",
            success: function (data) {
                if (data.status == "success") {
                    layer.msg(data.msg);
                    location.href = '<?=MLS_ADMIN_URL?>/sell_house/';
                } else {
                    layer.msg(data.msg);
                }
            }
        })
    }
    //取消喜欢房源（推送至金品app）
    function cancel_like_house(house_id) {
        $.ajax({
            type: 'POST',
            url: '<?=MLS_ADMIN_URL?>/sell_house/cancel_like_house',
            data: {'house_id': house_id},
            dataType: "json",
            success: function (data) {
                if (data.status == "success") {
                    layer.msg(data.msg);
                    location.href = '<?=MLS_ADMIN_URL?>/sell_house/';
                } else {
                    layer.msg('操作失败');
                }
            }
        })
    }

</script>
<?php require APPPATH . 'views/footer.php'; ?>
