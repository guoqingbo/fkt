<?php require APPPATH . 'views/header.php'; ?>
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
                        <div class="row">
                            <form name="search_form" method="post" action="" >
                            <div class="col-sm-6" style="width:100%">
                                状态查询：
                                <select name='status' style="width:183px;height:33px;">
                                    <option value="99" <?php if ($status == 99){ echo 'selected="selected"'; } ?> >请选择</option>
                                    <option value="1" <?php if ($status == 1){ echo 'selected="selected"'; } ?> >未处理</option>
                                    <option value="2" <?php if ($status == 2){ echo 'selected="selected"'; } ?> >处理中</option>
                                    <option value="3" <?php if ($status == 3){ echo 'selected="selected"'; } ?> >已处理</option>
                                    <option value="4" <?php if ($status == 4){ echo 'selected="selected"'; } ?> >已忽略</option>
                                </select>
                                <input type="hidden" name="pg" value="1">
                                <input class="btn btn-primary" type="submit" value="查询">
                            </div>
                            </form>
                            <p>
                                <span style="color:blue;"><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $info_num;?>&nbsp;条数据！</b></span>
                            </p>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>编号</th>
                                    <th>时间</th>
                                    <th>反馈内容</th>
                                    <th>反馈人电话</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($suggest) && !empty($suggest)) {
                                foreach ($suggest as $key => $suggest) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $suggest['id']; ?></td>
                                        <td><?php echo date('Y-m-d H:i:s',$suggest['dateline']); ?></td>
                                        <td><?php echo $suggest['feedback']; ?></td>
                                        <td><?php echo $suggest['telno']; ?></td>
                                        <td><?php if ($suggest['status'] == 1){ echo '未处理';
                                                  } else if($suggest['status'] == 2){
                                                    echo '处理中';
                                                  } else if($suggest['status'] == 3){
                                                    echo '已处理';
                                                  } else if($suggest['status'] == 4){
                                                    echo '已忽略';
                                                  } ?></td>
                                        <td>
                                            <a href="<?php echo MLS_ADMIN_URL;?>/suggest/show/<?php echo $suggest['id'];?>" >查看</a>
                                        </td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <div class="row">
                           <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                <ul class="pagination" style="margin:-8px 0;padding-left:20px"> 
                                    <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/suggest/');?>
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
<?php require APPPATH . 'views/footer.php'; ?>
