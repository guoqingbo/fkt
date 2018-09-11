<?php require APPPATH . 'views/header.php'; ?>
<link href="<?=MLS_SOURCE_URL ?>/min/?f=mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">预约看房列表</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                            <form name="search_form" method="post" action="">
                            <div class="row">
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>房源类型：
                                            <select name="type" aria-controls="dataTables-example" class="form-control input-sm">
                                                <option value="sell" <?=($post_param['type']=='sell')?"selected":""?>>出售房源</option>
												<option value="rent" <?=($post_param['type']=='rent')?"selected":""?>>出租房源</option>
                                                <!--												<option value="new_house"-->
                                                <?php //=($post_param['type']=='new_house')?"selected":""?><!-->
                                                新房报名</option>-->
                                            </select>
                                        </label>
                                        <label>
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input type="hidden" name="pg" value="1">
                                                <input class="btn btn-primary" type="submit" value="查询">
                                                <!--<a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/permission_tab_menu/add/'>添加</a>-->
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- /.panel-heading -->

                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th width="4%">序号</th>
                                    <th width="6%">房源编号</th>
                                    <th >预约房源</th>
                                    <th >房源地址</th>
                                    <th width="6%">预约人</th>
                                    <th width="10%">预约人电话</th>
                                    <th width="12%">预约经纪人门店</th>
                                    <th width="10%">预约经纪人</th>
                                    <th width="8%">创建时间</th>
                                    <th width="12%">预约时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($check_house) && !empty($check_house)) {
                                    foreach ($check_house as $key => $vo) {
                                        ?>
                                        <tr class="gradeA">
                                            <td><?=$vo['id']; ?></td>
                                            <td><?=$vo['hid']; ?></td>
                                            <td><?=$vo['block_name']; ?></td>
                                            <td><?=$vo['address']; ?></td>
                                            <td><?=$vo['uname']; ?></td>
                                            <td><?=$vo['phone']; ?></td>
                                            <td><?=$vo['agency_name']; ?></td>
                                            <td><?=$vo['truename']; ?></td>
                                            <td><?=date("Y-m-d",$vo['ctime']); ?></td>
                                            <td><?=date("Y-m-d,H:i:s",$vo['stime']); ?></td>
                                        </tr>
                                    <?php }
                                }else{ ?>
								<tr class="gradeA">
									<td align="center" colspan=10>请选择需要的信息查询</td>
								</tr>
								<?php } ?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
										<?php echo page_uri($page, $pages, MLS_ADMIN_URL . '/check_house/index'); ?>
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
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->



</div>
<!-- /#page-wrapper -->

</div>

<?php require APPPATH . 'views/footer.php'; ?>

