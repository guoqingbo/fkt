<?php require APPPATH.'views/header.php'; ?>
<style>
    .ui_name {
        color: #ff9d11;
        padding-left: 5px;
    }
    .ui_district {
        color: #32272c;
        padding-left: 5px;
    }
    .ui_address {
        color: #32272c;
        padding-left: 5px;
    }
    ui-menu-item {
        background: none repeat scroll 0 0 #fff;
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu {
        background: #fff;
        border: 1px solid #f5d5b8;
        float: left;
        border-top: none;
        padding: 0 !important;
        width: 230px !important;
    }
</style>
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
                            <form name="search_form" method="post" action="">
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>&nbsp&nbsp经纪人号码&nbsp&nbsp
                                                    <input type='tel' class="form-control input-sm" size='12' name="phone" value="<?php echo $phone;?>" style="width:100px" />
                                                </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                <label>租售&nbsp&nbsp&nbsp
                                                    <select name="sell_type" id="sell_type" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <option value="1" <?php if($sell_type == 1){echo 'selected="selected"';}?>>出售</option>
                                                        <option value="2" <?php if($sell_type == 2){echo 'selected="selected"';}?>>出租</option>
                                                    </select>
                                                </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                <label>楼盘名&nbsp&nbsp&nbsp
                                                    <input type='text' id="block_name" size='16' name="block_name" style="width:100px" value="<?php echo $block_name;?>"/>
                                                    <input type="text" name="house" id="house" size='16' value="" style="display:none;width:100px">
                                                    <input type="hidden" name="house_id" id="house_id" value="<?php echo $house_id;?>">
                                                    <input type="hidden" name="block_id" id="block_id" value="">
                                                </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                <label>日期：&nbsp
                                                    <label>
                                                        <input style="width:100px" type="text" name="start_time" id="start_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['start_time'])){echo $_POST['start_time'];}?>" onclick="WdatePicker()"> 到 <input style="width:100px" type="text" id="start_time" name="end_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['end_time'])){echo $_POST['end_time'];}?>" onclick="WdatePicker()">
                                                    </label>
                                                </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                <label>群发结果&nbsp&nbsp&nbsp&nbsp
                                                    <select name="type" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <option value="1" <?php if($type == 1){echo 'selected="selected"';}?>>成功</option>
                                                        <option value="2" <?php if($type == 2){echo 'selected="selected"';}?>>失败</option>
                                                    </select>
                                                </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" name="pg" value="1">
                                                        <input class="btn btn-primary" type="submit" value="查询">
                                                        <input class="btn btn-primary" type="button" value="重置" onclick="res()">
                                                    </div>
                                                </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                <label>
                                                    <?php echo $success;?>&nbsp&nbsp&nbsp<?php echo $success_rate;?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>经纪人姓名</th>
                                        <th>经纪人号码</th>
                                        <th>群发楼盘名</th>
                                        <th>群发目标网站</th>
                                        <th>租售</th>
                                        <th>群发结果</th>
                                        <th>额外信息</th>
                                        <th>群发时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(isset($group_publish_log) && !empty($group_publish_log)){
                                    foreach($group_publish_log as $key=>$value){?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php echo $value['name'];?></td>
                                            <td><?php echo $value['phone'];?></td>
                                            <td><?php echo $value['block_name'];?></td>
                                            <td><?php echo $value['site_name'];?></td>
                                            <td><?php echo $value['sell_type']==1?'出售':'出租';?></td>
                                            <td><?php echo $value['type']==1?'成功':'失败';?></td>
                                            <td><?php echo $value['info'];?></td>
                                            <td><?php echo date('Y-m-d H:i:s',$value['ymd']);?></td>
                                        </tr>
                                <?php }}?>
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-sm-6" style="width:100%;">
                                    <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $publish_num;?>&nbsp;条数据！</b></span>
                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                        <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                            <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/group_publish_log/group_publish');?>
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
</div>
<script type="text/javascript">
function res() {
    window.location.href="<?php echo MLS_ADMIN_URL;?>/group_publish_log/group_publish";
}
$(function(){
    $("#block_name").focus(function(){
        var check_type = $("#sell_type").val();
        if(check_type == 0){
            layer.alert('请先选择租售方式！！', {
                title: ['提示', 'font-weight:bold'],
                icon: 2,
            })
        }
    });

    $.widget( "custom.autocomplete", $.ui.autocomplete, {
        _renderItem: function( ul, item ) {
            $(".ui-helper-hidden-accessible").hide();
            if(item.id>0){
                return $( "<li>" )
                .data( "item.autocomplete", item )
                .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.cmt_name+'</span><span class="ui_district">'+item.districtname+'</span><span class="ui_address">'+item.address+'</span></a>')
                .appendTo( ul );
            }else{
                return $( "<li>" )
                .data( "item.autocomplete", item )
                .append('<a class="ui-corner-all" tabindex="-1">'+item.cmt_name+'</a>')
                .appendTo( ul );
            }
        }
    });
    $("#block_name").autocomplete({
        source: function( request, response ) {
            var term = request.term;
            $.ajax({
                url: "/group_publish_log/group_publish_ajax/",
                type: "GET",
                dataType: "json",
                data: {
                    keyword: term,
                },
                success: function(data) {
                    //判断返回数据是否为空，不为空返回数据。
                    if( data['id'] != '0'){
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
                var house_name = ui.item.cmt_name;
                var id = ui.item.id;
                //操作
                $("#house_id").val(id);
                $("#block_id").val(id);
                $("#house").val(house_name);
                $("#block_name").hide();
                $("#house").show();
                $(".ui-helper-hidden-accessible").hide();
                removeinput = 2;
            }else{
                removeinput = 1;
            }
        },
        close: function(event) {
            if(typeof(removeinput)=='undefined' || removeinput == 1){
                $("#house_id").val("");
                $("#block_id").val("");
                $("#block_name").val("");
                $("#house").val("");
                $("#block_name").show();
                $("#house").hide();
            }
        }
    });
    $("#house").autocomplete({
        source: function( request, response ) {
            var term = request.term;
            $.ajax({
                url: "/group_publish_log/group_publish_ajax/",
                type: "GET",
                dataType: "json",
                data: {
                    keyword: term,
                },
                success: function(data) {
                    //判断返回数据是否为空，不为空返回数据。
                    if( data['id'] != '0'){
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
                var house_name = ui.item.cmt_name;
                var id = ui.item.id;
                //操作
                $("#house_id").val(id);
                $("#block_id").val(id);
                $("#block_name").val(house_name);
                $("#house").hide();
                $("#block_name").show();
                $(".ui-helper-hidden-accessible").hide();
                removeinput = 2;
            }else{
                removeinput = 1;
            }
        },
        close: function(event) {
            if(typeof(removeinput)=='undefined' || removeinput == 1){
                $("#house_id").val("");
                $("#block_id").val("");
                $("#block_name").val("");
                $("#house").val("");
                $("#house").show();
                $("#block_name").hide();
            }
        }
    });
})
</script>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/jquery-ui-1.9.2.custom.min.js"></script>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<?php require APPPATH.'views/footer.php'; ?>

