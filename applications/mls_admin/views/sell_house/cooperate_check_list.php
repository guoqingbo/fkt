<?php require APPPATH . 'views/header.php'; ?>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/jquery-ui-1.9.2.custom.min.js"></script>
<link href="<?=MLS_SOURCE_URL ?>/mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<link href="<?=MLS_SOURCE_URL ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<link href="<?=MLS_SOURCE_URL ?>/mls/css/v1.0/autocomplete.css" rel="stylesheet" type="text/css">

<style>
    tr {text-align:center;}

    .ui-menu {background: none repeat scroll 0 0 #fff;border: 1px solid #d1d1d1;float: left; border-top:none;list-style: none;margin: 0;padding: 0;}
    .ui-menu .ui-menu-item {list-style: none;background: none repeat scroll 0 0 #fff;clear: left;float: left;margin: 0;padding: 0;width: 100%;}
    .ui-menu .ui-menu-item a {color: #333;cursor: pointer;display: block;font-family: Arial,Helvetica,sans-serif;height: 24px;line-height: 24px;overflow: hidden;padding: 0 4px;text-align: left;text-decoration: none;}
    .ui-menu .ui-menu-item a.ui-state-hover, .ui-menu .ui-menu-item a.ui-state-active {background: none repeat scroll 0 0 #ff9804;color: #fff;font-weight: normal;text-decoration: none;}


</style>

<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">出售房源合作审核</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <form name="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            房源编号：&nbsp;<input type="text"  name="id" id="id" style="width:183px" class="form-control input-sm" aria-controls="dataTables-example" value="<?php
                                            if (isset($post_param['id'])) {
                                                echo $post_param['id'];
                                            }
                                            ?>"  onclick="add_content1()"><span id='reminder1' style='font-weight:bold;color:red;'></span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            所属公司&nbsp;&nbsp;
                                                <input name="company_name" id="company_name" value="<?php if (isset($_POST['company_name'])){ echo $_POST['company_name']; } ?>" class="input_text input_text_r w150 form-control input-sm" type="text" placeholder="输入汉字筛选" style="height:30px; line-height: 30px;" >
                                                <input type="hidden" name="company_id" id="company_id" value="<?php if (isset($_POST['company_id'])){ echo $_POST['company_id']; } ?>">
                                            &nbsp;&nbsp;所属门店&nbsp;
                                                <select name="agency_id"  id="agency_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                    <option value="0">请选择</option>
                                                    <?php if ($agencys) {
                                                            foreach ($agencys as $v) { ?>
                                                                <option value="<?=$v['id']?>"  <?php if((!empty($_POST['agency_id']) && $_POST['agency_id'] == $v['id'])){echo 'selected="selected"';}?>><?=$v['name']?></option>
                                                            <?php }?>
                                                    <?php } ?>
                                                </select>
                                            <script type="text/javascript">

                                            function get_agency(companyId)
                                            {
                                                $.ajax({
                                                    type: 'get',
                                                    url : '<?php echo MLS_ADMIN_URL; ?>/agency/get_agency_ajax/'+companyId,
                                                    dataType:'json',
                                                    success: function(msg){
                                                        var str = '';
                                                        if(msg===''){
                                                            str = '<option value="">请选择</option>';
                                                        }else{
                                                            str = '<option value="">请选择</option>';
                                                            for(var i=0;i<msg.length;i++){
                                                                str +='<option value="'+msg[i].id+'">'+msg[i].name+'</option>';
                                                            }
                                                        }
                                                        $('#agency_id').empty();
                                                        $('#agency_id').append(str);
                                                    }
                                                });
                                            }
                                            $(function() {
                                                $("#company_name").autocomplete({
                                                    source: function( request, response ) {
                                                        var term = request.term;
                                                        $.ajax({
                                                            url: "/company/get_company_by_kw/",
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
                                                            get_agency(id);
                                                            removeinput = 2;
                                                        }else{
                                                            removeinput = 1;
                                                        }
                                                    },
                                                    close: function(event) {
                                                        if(typeof(removeinput)=='undefined' || removeinput == 1){
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
                                                ?>>经纪人帐号</option>
                                                <option value="broker_name" <?php
                                                if ($post_param['type'] == 'broker_name') {
                                                    echo "selected";
                                                }
                                                ?>>经纪人姓名</option>
                                                <option value="phone" <?php
                                                        if ($post_param['type'] == 'phone') {
                                                            echo "selected";
                                                        }
                                                        ?>>联系方式</option>
                                            </select>
                                            <input type="text"  name="search_value" id="search_value" style="width:183px" class="form-control input-sm" aria-controls="dataTables-example" value="
<?php
if (isset($_POST['search_value'])) {
    echo $_POST['search_value'];
}
?>"  onclick="add_content3()"><span id='reminder3' style='font-weight:bold;color:red;'></span>
                                            查询结果按照: &nbsp;
                                            <select name="order_type">
                                                <option value="createtime" <?php
if ($post_param['order_type'] == 'createtime') {
    echo "selected";
}
?>>发布时间</option>
                                                <option value="broker_id" <?php
if ($post_param['order_type'] == 'broker_id') {
    echo "selected";
}
?>>经纪人帐号</option>
                                            </select>

                                            <select name="order_value">

                                                <option value="DESC" <?php
if ($post_param['order_value'] == 'DESC') {
    echo "selected";
}
?>>倒序排列</option>
                                                <option value="ASC" <?php
if ($post_param['order_value'] == 'ASC') {
    echo "selected";
}
?>>正序排列</option>
                                            </select>

                                            每页显示&nbsp;<select name="pages">
                                                <option value="10" <?php if ($pagesize == '10') {
    echo "selected";
} ?>>10</option>
                                                <option value="30" <?php if ($pagesize == '30') {
    echo "selected";
} ?>>30</option>
                                                <option value="40" <?php if ($pagesize == '40') {
    echo "selected";
} ?>>40</option>
                                                <option value="50" <?php if ($pagesize == '50') {
                                    echo "selected";
                                } ?>>50</option>
                                            </select>条数据
                                        </div>

                                        <br>
                                    </div>
                                    <input type="hidden" name="angela_wen" value="angel_in_us">
                                    <input type="hidden" name="pg" value="1">
                                    <input class="btn btn-primary" type="submit" value="查询">&nbsp;&nbsp;&nbsp;<input class="btn btn-primary" type="button" onclick="javascript:location.href = '/sell_house/'" value="重置">
                                    <!--<a class="btn btn-primary" href="/sell_house/add">添加</a>-->
                            </form>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>房源编号</th>
                                    <th>楼盘</th>
                                    <th>所在门店</th>
                                    <th>经纪人姓名</th>
                                    <th>联系方式</th>
                                    <th>设置合作时间</th>
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
                                            <td><?php echo $value['block_name']; ?></td>
                                            <td><?php echo $value['agency_name']; ?></td>
                                            <td><?php echo $value['broker_name']; ?></td>
                                            <td><?php echo $value['phone']; ?></td>
                                            <td><?php echo date('Y-m-d H:i:s', $value['set_share_time']); ?></td>
                                            <td>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/sell_house/cooperate_check_detail/<?php echo $value['id']; ?>" >审核</a><br>
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
                                <div class="dataTables_info" id="dataTables-example_info" role="alert" aria-live="polite" aria-relevant="all"><input type="checkbox" id="sel-all">&nbsp;&nbsp;全选 &nbsp;&nbsp;<a href="javascript:void(0)"  data-target="#myModal1" data-toggle="modal">加入白名单</a> &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal" data-toggle="modal" >标记到推送库</a> &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal2" data-toggle="modal">标记到备选库</a>
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
                                        <b>共查到<?php echo $sold_num;?>条数据</b>
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
    //房源列表页 区属联动
    function districtchange(districtid)
    {
        $.ajax({
            type: 'get',
            url: '/sell_house/find_street_bydis/' + districtid,
            dataType: 'json',
            success: function(msg) {
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
        else
        {
            //ajax 改变 cooperate 表里的 real_price
            $.ajax({
                type: 'get',
                url: '<?=MLS_ADMIN_URL?>sell_house_sold/change_real_price',
                data: {'id': id, 'real_price': real_price},
                dataType: 'json',
                success: function(msg) {
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
</script>
<?php require APPPATH . 'views/footer.php'; ?>
