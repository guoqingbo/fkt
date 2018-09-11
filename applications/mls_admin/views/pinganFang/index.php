<?php require APPPATH.'views/header.php'; ?>
<link href="<?=MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css" rel="stylesheet" type="text/css">
<link href="<?=MLS_SOURCE_URL ?>/mls/css/v1.0/autocomplete.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/openWin.js"></script>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquey-bigic.js"></script>
<style>
    td{text-align: center}
     th{text-align: center}
	html, body {
    height: 100%;
    overflow: auto;
    width: 100%;
	}
    .ui-menu {background: none repeat scroll 0 0 #fff;border: 1px solid #d1d1d1;float: left; border-top:none;list-style: none;margin: 0;padding: 0;}
    .ui-menu .ui-menu-item {list-style: none;background: none repeat scroll 0 0 #fff;clear: left;float: left;margin: 0;padding: 0;width: 100%;}
    .ui-menu .ui-menu-item a {color: #333;cursor: pointer;display: block;font-family: Arial,Helvetica,sans-serif;height: 24px;line-height: 24px;overflow: hidden;padding: 0 4px;text-align: left;text-decoration: none;}
    .ui-menu .ui-menu-item a.ui-state-hover, .ui-menu .ui-menu-item a.ui-state-active {background: none repeat scroll 0 0 #ff9804;color: #fff;font-weight: normal;text-decoration: none;}

h1{font-size:25px;font-weight: bold}
</style>
<div id="wrapper">
    <div id="page-wrapper" style="min-height: 337px;" >
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <div class="table-responsive">
                            <form action="" method="post" name="search_form">
                                <input type="hidden" name="submit_flag" value="search">
                                <div style="width:100%" class="col-sm-6">
                                    所属公司&nbsp;
                                            <input name="company_name" id="company_name" value="<?php if (isset($post_params['company_name'])){ echo $post_params['company_name']; } ?>" class="input_text input_text_r w150 form-control input-sm" type="text" placeholder="输入汉字筛选" style="width:200px;height:30px; line-height: 30px;display: inline-block;" >
                                            <input type="hidden" name="company_id" id="company_id" value="<?php if (isset($post_params['company_id'])){ echo $post_params['company_id']; } ?>">
                                        &nbsp;&nbsp;所属门店&nbsp;
                                            <select name="agency_id"  id="agency_id" aria-controls="dataTables-example" class="form-control input-sm" style="display:inline-block;width:150px">
                                                <option value="0">请选择</option>
                                                <?php if ($agencys) {
                                                        foreach ($agencys as $v) { ?>
                                                            <option value="<?=$v['id']?>"  <?php if((!empty($post_params['agency_id']) && $post_params['agency_id'] == $v['id'])){echo 'selected="selected"';}?>><?=$v['name']?></option>
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
                                        &nbsp;&nbsp;经纪人手机号:&nbsp;
                                        <input type="text"  name="phone" aria-controls="dataTables-example" class="form-control input-sm " style="width:100px;display: inline-block;" value="<?php if (isset($post_params['phone'])) {echo $post_params['phone'];}?>">
                                        &nbsp;&nbsp;经纪人姓名:&nbsp;
                                        <input type="text"  name="broker_name" aria-controls="dataTables-example" class="form-control input-sm " style="width:100px;display: inline-block;" value="<?php if (isset($post_params['broker_name'])) {echo $post_params['broker_name'];}?>">

                                        &nbsp;&nbsp;楼盘：&nbsp;<input type="text"  name="block_name" id="block_name"  aria-controls="dataTables-example" class="form-control input-sm " style="width:190px;display: inline-block;" value="<?php
                                            if (isset($post_params['block_name'])) {
                                                echo $post_params['block_name'];
                                            }
                                            ?>"><span id='reminder3' style='font-weight:bold;color:red;'></span>
                                        <input name="block_id" id="block_id" value="<?php if (isset($post_params['block_id'])) {echo $post_params['block_id'];}?>" type="hidden">
                                        <script type="text/javascript">
                                            $(function() {
                                                $("#block_name").autocomplete({
                                                    source: function(request, response) {
                                                        var term = request.term;
                                                        $("#block_id").val("");
                                                        $.ajax({
                                                            url: "/community/get_cmtinfo_by_kw/",
                                                            type: "GET",
                                                            dataType: "json",
                                                            data: {
                                                                keyword: term
                                                            },
                                                            success: function(data) {
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
                                                    select: function(event, ui) {
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
                                                    close: function(event) {
                                                        if (typeof (removeinput) == 'undefined' || removeinput == 1) {
                                                            $("#block_name").val("");
                                                            $("#block_id").val("");
                                                        }
                                                    }
                                                });
                                            });
                                        </script>
                                    </div>
                                        <div class="col-sm-6" style="width:100%">
                                        &nbsp;&nbsp;区域：&nbsp;
                                            <select class="select" id='district' name='district_id' onchange="districtchange(this.value);">
                                                <option value="" >不限</option>
                                                <?php
                                                foreach ($district as $key => $value) {
                                                    ?>
                                                    <option  value="<?php echo $value['id']; ?>" <?=$value['id']==$post_params['district_id']?'selected':''?>><?php echo $value['district']; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        &nbsp;&nbsp;板块：&nbsp;
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
                                        &nbsp;&nbsp;户型：&nbsp;
                                        <select name="room">
                                            <option value="" >不限</option>
                                            <?php
                                                foreach ($config['room'] as $key => $value) {
                                            ?>
                                                <option  value="<?php echo $key; ?>" <?php
                                            if ($post_param['room'] == $key) {
                                                echo "selected";
                                            }
                                            ?>><?php echo $value; ?></option>
                                                   <?php
                                               }
                                               ?>
                                        </select>

                                        &nbsp;&nbsp;面积:&nbsp;
                                        <input type="text"  name="areamin" id="min_area"  aria-controls="dataTables-example" class="form-control input-sm " style="width:53px;display: inline-block;" value="<?php if (isset($post_params['areamin'])) {echo $post_params['areamin'];}
                                        ?>"> 至 <input type="text"  name="areamax" id="max_area" style="width:53px;display: inline-block;" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if (isset($post_params['areamax'])) {echo $post_params['areamax'];}?>"> 平米

                                        &nbsp;&nbsp;总价:&nbsp;
                                        <input type="text"  name="pricemin" id="min_price" style="width:53px;display: inline-block;" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if (isset($post_params['pricemin'])) {echo $post_params['pricemin'];}
                                               ?>"> 至 <input type="text"  name="pricemax" id="max_price" style="width:53px;display: inline-block;" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if (isset($post_params['pricemax'])) {echo $post_params['pricemax'];}?>"> 万元
                                        &nbsp;&nbsp;上传时间:&nbsp;
                                        <input type="text"  name="timemin" id="min_price" style="width:100px;display: inline-block;" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if (isset($post_params['timemin'])) {echo $post_params['timemin'];}
                                               ?>" onclick="WdatePicker({lang:'zh-cn',minDate:'{%y-10}-%M-%d',startime:'%y-%M-%d'})"> 至 <input type="text"  name="timemax" id="max_price" style="width:100px;display: inline-block;" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if (isset($post_params['timemax'])) {echo $post_params['timemax'];}?>" onclick="WdatePicker({lang:'zh-cn',minDate:'{%y-10}-%M-%d',startime:'%y-%M-%d'})">
                                        &nbsp;&nbsp;审核状态：&nbsp;<select name="is_check">
                                                    <option value="">不限</option>
                                                    <option value="0" <?=$post_params['is_check']=='0' && isset($post_params['is_check'])?'selected':''?>>未审核</option>
                                                    <option value="1" <?=$post_params['is_check']=='1' && isset($post_params['is_check'])?'selected':''?>>审核通过</option>
													<option value="2" <?=$post_params['is_check']=='2' && isset($post_params['is_check'])?'selected':''?>>审核不通过</option>
                                                </select>
                                        <input type="hidden" name="pg" value="1">
                                            <input class="btn btn-primary" type="submit" value="查询" name="search">&nbsp;&nbsp;&nbsp;<input
                                                    class="btn btn-primary" type="button" value="重置">
                                </div>
                            </form>
                        </div>
                        <table id="dataTables-example" class="table table-striped table-bordered table-hover">
                            <thead>
                                 <tr>
                                    <th style="width:45px">序号</th>
                                    <th style="width:75px">房源编号</th>
                                    <th style="width:105px">房源图片</th>
                                    <th style="width:250px;text-align:center">小区名称</th>
                                    <th style="width:75px">区属</th>
                                    <th style="width:75px">板块</th>
                                    <th style="width:75px">面积(㎡)</th>
                                    <th style="width:70px">价格(万元)</th>
                                    <th style="width:45px">户型</th>
                                    <th style="width:45px">楼层</th>
                                    <th style="width:90px">经纪人姓名</th>
                                    <th style="width:90px">手机号</th>
                                    <th style="width:90px">上传时间</th>
                                    <th style="width:90px">状态</th>
                                    <th style="width:200px;text-align:center">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($list) && !empty($list)) {
                                            foreach($list as $key =>$val){?>
                                <tr class="gradeA">
                                    <td><?php echo sprintf('%02d',$key+1);?></td>
                                    <td><?php echo 'CS'.$val['house_id'];?></td>
                                    <td><a href="javascript:void(0);" onclick="open_pic(<?=$val['house_id']?>);">查看图片</a></td>
                                    <td><?php echo $val['block_name'];?></td>
                                    <td><?php echo $district[$val['district_id']]['district'];?></td>
                                    <td><?php echo $street[$val['street_id']]['streetname'];?></td>
                                    <td><?php echo intval($val['buildarea']);?></td>
									<td><?php echo intval($val['price']);?></td>
                                    <td><?php echo $val['room']?></td>
                                    <td><?php echo $val['floor']?></td>
                                    <td><?php echo $val['truename']?></td>
                                    <td><?php echo $val['phone']?></td>
                                    <td><?php echo date('Y-m-d',$val['outside_time']);?></td>
									<td><?php switch($val['is_check']){case 0:echo "未审核";break;case 1:echo "<font color='green'>已审核</font>";break;case 2:echo "<font color='red'>审核不通过</font>";break;}?><br>
                                    <?php switch($val['is_outside']){case 0:echo "未同步";break;case 1:echo "<font color='green'>已同步</font>";break;case 2:echo "<font color='red'>已下架</font>";break;}?></td>
                                    <td><a href="/pinganFang/house_detail/<?=$val['house_id']?>/<?=$val['id']?>">审核</a>
                                        <?php if($val['is_outside']==1){?> | <a href="javascript:void(0);" onclick="house_down(<?=$val['house_id']?>,<?=$val['id']?>);">下架</a>
                                        <?php }elseif($val['is_outside']==2){?> | <a href="javascript:void(0);" onclick="pingan_data_deal(<?=$val['house_id']?>,<?=$val['id']?>);">上架</a>
                                        <?php }elseif($val['is_outside']==0 && $val['is_check'] >0){?> | <a href="javascript:void(0);" onclick="pingan_data_deal(<?=$val['house_id']?>,<?=$val['id']?>);">同步</a>
                                        <?php }?>
                                    </td>
                                </tr>
                                <?php }}else{
                                       echo "<tr class='gradeA'><td colspan=15 style='text-align:center;color:red;font-weight:bold;'>暂无您查询的平安好房数据！</td></tr>";
                                }?>
                            </tbody>
                        </table>
                        <div class="row">
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
        </div>
    </div>
</div>
<div id="fakeloader" style="display:none;"><img src="<?php echo MLS_SOURCE_URL ?>/mls_admin/images/load3.gif"
                                                width="50" height="50"></div>
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
                url: '<?=MLS_ADMIN_URL?>/sell_house_sold/change_real_price',
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

    function house_down(house_id,id){
        openWin('fakeloader');
        $.ajax({
            type: 'get',
            url: '/pinganFang/house_down/'+house_id+'/'+id,
            data:{is_ajax:1},
            dataType: 'json',
            success: function(data) {
                if(data['code'] !=='success'){
                    $("#dialog_do_itp").html(data.msg);
                    openWin('js_pop_do_success');
                }else{
                    closeWindowWin('fakeloader');
                    $("#dialog_do_itp").html('下架成功');
                    openWin('js_pop_do_success');
                }
            }
        });
    }

    //平安好房房源同步
    function pingan_data_deal(house_id,id){
        openWin('fakeloader');
        $.ajax({
            url: '/pinganFang/post_all_data/'+house_id+'/'+id,
            type: "get",
            data:{is_ajax:1},
            dataType: "json",
            success: function(data) {
                if(data['code'] !=='success'){
                    $("#dialog_do_itp").html(data.msg);
                    openWin('js_pop_do_success');
                }else{
                    closeWindowWin('fakeloader');
                    $("#dialog_do_itp").html('上架成功');
                    openWin('js_pop_do_success');
                }
            }
         });

    }
    function open_pic(house_id){
        $('#js_template_pop').find('.iframePop').attr('src','/pinganFang/get_house_pic/'+house_id);
        openWin('js_template_pop');
    }
</script>
<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" style="border-color:#3E44;">
    <div class="hd" style="background: #3E444B;">
        <div class="title" >提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="location.reload();" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'></p>
            </div>
        </div>
    </div>
</div>
<!--选择模板弹框-->
<div id="js_template_pop" class="iframePopBox" style="width: 842px;height:502px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" onclick="$('#js_template_pop').find('.iframePop').attr('src','');">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="842" height="502" class='iframePop' src="" name="template_pop"></iframe>
</div>
<?php require APPPATH.'views/footer.php'; ?>





