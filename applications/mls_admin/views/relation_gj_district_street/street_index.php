<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><a href='<?php echo MLS_ADMIN_URL;?>/relation_district_street/ganji_district_index' class="btn btn-primary">赶集区属列表</a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href='<?php echo MLS_ADMIN_URL;?>/relation_district_street/ganji_street_index' class="btn btn-primary">赶集板块列表</a></h1>
                </div>
                <div class="col-lg-12">
                    <h1 class="page-header"><?=$title?></h1>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>&nbsp&nbsp 区属&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <select name="dist_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <?php foreach($all_district as $k => $v){?>
                                                            <option value="<?php echo $v['district_id'];?>"<?php if(isset($where_cond['dist_id'])){if($v['district_id']==$where_cond['dist_id']){echo 'selected="selected"';}}?>><?php echo $v['district_id'];?>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $v['district_name'];?></option>
                                                            <?php }?>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            <input type="hidden" name="pg" value="1">
                                                            <input class="btn btn-primary" type="submit" value="查询">
                                                            <a class="btn btn-primary" href='ganji_street_add'>添加</a>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>本地序号</th>
                                            <th>赶集板块id</th>
                                            <th>赶集板块名</th>
                                            <th>赶集区属id</th>
                                            <th>赶集区属</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(isset($street_list2) && !empty($street_list2)){
                                            foreach($street_list2 as $key=>$value){?>
                                                <tr class="gradeA">
                                                    <td><?php echo $value['id'];?></td>
                                                    <td><?php echo $value['street_id'];?></td>
                                                    <td><?php echo $value['street_name'];?></td>
                                                    <td><?php echo $value['dist_id'];?></td>
                                                    <td><?php echo $value['dist_name'];?></td>
                                                    <td>
                                                        <a href="<?php echo MLS_ADMIN_URL;?>/relation_district_street/ganji_street_modify/<?php echo $value['id'];?>" >修改</a>
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
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.row -->
        </div>
    </div>
    <!-- /#wrapper -->
<?php require APPPATH.'views/footer.php'; ?>

