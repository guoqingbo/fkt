<?php require APPPATH.'views/header.php'; ?>
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
                            <form name="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%;">
                                            日期：
                                            <label>
                                                <input style="width:183px" type="text" name="start_time" id="start_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['start_time'])){echo $_POST['start_time'];}?>" onclick="WdatePicker()"> 到 <input style="width:183px" type="text" id="start_time" name="end_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['end_time'])){echo $_POST['end_time'];}?>" onclick="WdatePicker()">
                                            </label>
                                            <input type="hidden" name="pg" value="1">
                                            <input class="btn btn-primary" type="submit" value="查询">&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input class="btn btn-primary" type="button" value="重置" onclick="res()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input class="btn btn-primary" type="button" value="发布" onclick="issue()"><br>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">序号</th>
                                        <th style="width:5%;">弹窗</th>
                                        <th style="width:15%;">标题</th>
                                        <th style="width:30%;">内容</th>
                                        <th style="width:10%;">时间</th>
                                        <th style="width:15%;">城市</th>
                                        <th style="width:20%;">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(isset($notice_msg) && !empty($notice_msg)){
                                        foreach($notice_msg as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php if($value['show'] == 1){ echo "有";}else{echo "无";}?></td>
                                            <td><?php echo $value['title'];?></td>
                                            <td title="<?php echo $value['content'];?>"><?php $str = mb_substr(strip_tags($value['content']),0,30,'utf-8');echo $str."...";?></td>
                                            <td><?php echo date('Y-m-d',$value['createtime']);?></td>
                                            <td title="<?php echo $value['city'];?>"><?php $str = mb_substr(strip_tags($value['city']),0,11,'utf-8');echo $str."...";?></td>
                                            <td>
                                                <a href="<?php echo MLS_ADMIN_URL;?>/collect_mass_notice/modify/<?php echo $value['id'];?>" >修改</a>&nbsp;
                                                <a href="<?php echo MLS_ADMIN_URL;?>/collect_mass_notice/del/<?php echo $value['id'];?>"  onclick="return checkdel()">删除</a>&nbsp;
                                                <a href="javascript:void(0)" onclick="set_hard(<?php echo $value['id']?>)"><?php echo $value['hard']==1 ? '取消重要' : '设为重要';?></a>&nbsp;
                                                <a href="javascript:void(0)" onclick="set_news(<?php echo $value['id']?>)"><?php echo $value['news']==1 ? '取消最新' : '设为最新';?></a>
                                                <br>
                                                <span value="<?=$value['id']?>" slider = "<?=$value['collect_type']?>" style="color: red; cursor: pointer;" class="collect_type"><?php if ($value['collect_type'] == 0) { echo '设为采集轮播';}else{echo '取消采集轮播';}?></span>
                                                <span value="<?=$value['id']?>" slider = "<?=$value['mass_type']?>" style="color: red; cursor: pointer;" class="mass_type"><?php if ($value['mass_type'] == 0) { echo '设为群发轮播';}else{echo '取消群发轮播';}?></span>
                                            </td>
                                        </tr>
                                    <?php }}?>
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                        <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                            <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/user/index');?>
                                        </ul>
                                    </div>
                                </div>
                                <div style="color:blue;position:absolute;right:33px;">
                                    <b>共查到<?php echo $notice_num;?>条数据</b>
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
//设为重要
function set_hard(id){
	$.ajax({
        type : "GET",
        url  : "/collect_mass_notice/set_hard?id="+id,
        dataType : "json",
        success: function(data) {
            if (data) {
                window.location.href = window.location.href;
            } else {
                alert('操作失败，请重试!');
            }
        }
    });
}
//设为最新
function set_news(id){
	$.ajax({
        type : "GET",
        url  : "/collect_mass_notice/set_news?id="+id,
        dataType : "json",
        success: function(data) {
            if (data) {
                window.location.href = window.location.href;
            } else {
                alert('操作失败，请重试!');
            }
        }
    });
}



    function checkdel(){
	if(confirm("确实要删除吗？")){
            return true;
	}else{
            return false;
	}
    }
    $(function(){
        $('.collect_type').bind('click', function() {
            var id=$(this).attr('value');
            var collect_type=$(this).attr('slider');
            $.ajax({
                type : "GET",
                url  : "/collect_mass_notice/set_collect/",
                data : {
                    'id' :id,
                    'collect_type' :collect_type,
                },
                success: function(data) {
                    if (data) {
                        window.location.href = window.location.href;
                    } else {
                        alert('操作失败，请重试!');
                    }
                }
            });
        });
        $('.mass_type').bind('click', function() {
            var id=$(this).attr('value');
            var mass_type=$(this).attr('slider');
            $.ajax({
                type : "GET",
                url  : "/collect_mass_notice/set_mass/",
                data : {
                    'id' :id,
                    'mass_type' :mass_type,
                },
                success: function(data) {
                    if (data) {
                        window.location.href = window.location.href;
                    } else {
                        alert('操作失败，请重试!');
                    }
                }
            });
        });
    })
    function res() {
        window.location.href="<?php echo MLS_ADMIN_URL;?>/collect_mass_notice/index";
    }
    function issue() {
        location.href="<?php echo MLS_ADMIN_URL;?>/collect_mass_notice/add/";
    }
</script>
<?php require APPPATH.'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
