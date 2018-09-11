<?php require APPPATH . 'views/header.php'; ?>
<link href="<?=MLS_SOURCE_URL ?>/min/?f=mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">中奖名单</h1>
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
							<input type="hidden" name="pg" value="1">
                            <div class="row">
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
													<label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
															获奖姓名：&nbsp;<input type="text" value="<?=$post_param['broker_name']?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:150px;display: inline-block;"  name="broker_name">&nbsp;&nbsp;&nbsp;&nbsp;获奖电话：&nbsp;<input type="text" value="<?=$post_param['phone']?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:150px;display: inline-block;"  name="phone">
                                                            <input class="btn btn-primary" type="submit" value="查询" name="search">&nbsp;&nbsp;&nbsp;<input class="btn btn-primary" type="button" onclick="javascript:location.href = '/project_cooperate_lol_win_list/'" value="重置">
                                                        </div>
                                                    </label>
													<label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            &nbsp;&nbsp;<a class="btn btn-primary" href='/project_cooperate_lol_win_list/exportReport/<?php if(isset($post_param['broker_name']) || isset($post_param['phone']) ){?><?php echo '?';?><?php }?>
														<?php
															if(isset($post_param['broker_name'])){
																echo 'broker_name='.$post_param['broker_name'];
															}
															if(isset($post_param['phone'])){
																echo '&phone='.$post_param['phone'];
															}
														?>'>导出</a>
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
                                    <th>序号</th>
                                    <th>中奖者姓名</th>
                                    <th>中奖者电话</th>
									<th>奖品类型</th>
                                    <th>中奖者所在城市</th>
                                    <th>中奖时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($win_list) && !empty($win_list)) {
                                    foreach ($win_list as $key => $value) {
                                        ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id']; ?></td>
											<td><?php echo $value['broker_name']; ?></td>
											<td><?php echo $value['phone']; ?></td>
                                            <td>
											<?php
											foreach($reward_type as $key=>$val){
												if($val['id']== $value['reward_type']){
													echo $val['name'];
												}
											}
											?>

											</td>
											<td><?php echo $value['cityname']; ?></td>
                                            <td><?php echo date('Y-m-d' , $value['create_time']); ?></td>

                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
<?php echo page_uri($page, $pages, MLS_ADMIN_URL . '/permission_tab_menu/index'); ?>
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
<script>
    function checkdel() {
        if (confirm("操作将使奖品失效，确实要使其失效吗？"))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
</script>
<?php require APPPATH . 'views/footer.php'; ?>

