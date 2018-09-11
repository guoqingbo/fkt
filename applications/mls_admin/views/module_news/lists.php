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
                                        <th style="width:10%;">序号</th>
                                        <th style="width:20%;">标题</th>
                                        <th style="width:30%;">链接</th>
                                        <th style="width:15%;">时间</th>
                                        <th style="width:25%;">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(isset($news_msg) && !empty($news_msg)){
                                        foreach($news_msg as $key=>$value){
                                            $url = MLS_ADMIN_URL.'/notice/module_news/?id='.$value['id'];
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php echo $value['title'];?></td>
                                            <td><a href="<?=$url?>" target="_blank">/notice/module_news/?id=<?=$value['id']?></a></td>
                                            <td><?php echo date('Y-m-d',$value['createtime']);?></td>
                                            <td>
                                                <a href="<?php echo MLS_ADMIN_URL;?>/module_news/modify/<?php echo $value['id'];?>" >修改</a>&nbsp;
                                                <a href="<?php echo MLS_ADMIN_URL;?>/module_news/del/<?php echo $value['id'];?>"  onclick="return checkdel()">删除</a>
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
                                    <b>共查到<?php echo $news_num;?>条数据</b>
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
    function checkdel(){
        if(confirm("确实要删除吗？")){
                return true;
        }else{
                return false;
        }
    }
    function res() {
        window.location.href="<?php echo MLS_ADMIN_URL;?>/module_news/index";
    }
    function issue() {
        location.href="<?php echo MLS_ADMIN_URL;?>/module_news/add/";
    }
</script>
<?php require APPPATH.'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
