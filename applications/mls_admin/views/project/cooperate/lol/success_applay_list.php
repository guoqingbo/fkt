<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?php echo $title;?></h1>
                </div>
                <!-- /.col-lg-12 -->
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
                                                    <label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            <input type="hidden" name="pg" value="1">
                                                        </div>
                                                    </label>
													<label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
															合同编号：&nbsp;<input type="text" value="<?=$param_array['order_sn']?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:150px;display: inline-block;"  name="order_sn">
                                                            <input class="btn btn-primary" type="submit" value="查询" name="search">&nbsp;&nbsp;&nbsp;<input class="btn btn-primary" type="button" onclick="javascript:location.href = '/project_cooperate_lol_success_applay/'" value="重置">
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">序号</th>
                                            <th rowspan="2">合同内部编号</th>
                                            <th rowspan="2">业主姓名</th>
                                            <th rowspan="2">业主电话</th>
                                            <th rowspan="2">业主身份证</th>
											<th colspan="4" style="text-align:center">甲方</th>
                                            <th colspan="4" style="text-align:center">乙方</th>
                                            <th rowspan="2">创建时间</th>
                                            <th rowspan="2">操作</th>
                                        </tr>
										<tr>
											<th style="text-align:center">公司</th>
                                            <th style="text-align:center">门店</th>
                                            <th style="text-align:center">经纪人</th>
                                            <th style="text-align:center">手机</th>
											<th style="text-align:center">公司</th>
											<th style="text-align:center">门店</th>
                                            <th style="text-align:center">经纪人</th>
                                            <th style="text-align:center">手机</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(isset($list) && !empty($list)){
                                            foreach($list as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php echo $value['order_sn'];?></td>
                                            <td><?php echo $value['seller_owner'];?></td>
                                            <td><?php echo $value['seller_telno'];?></td>
                                            <td><?php echo $value['seller_idcard'];?></td>
											<?php if($value['success_list_arr']){
													foreach($value['success_list_arr'] as $v){
														if($v['broker_id'] == $v['operate_broker_id']){
											?>
													<td><?php echo $v['company_name'];?></td>
													<td><?php echo $v['agency_name'];?></td>
													<td><?php echo $v['broker_name'];?></td>
													<td><?php echo $v['phone'];?></td>
													<?php }else if($v['broker_id'] != $v['operate_broker_id']){?>
													<td><?php echo $v['company_name'];?></td>
													<td><?php echo $v['agency_name'];?></td>
													<td><?php echo $v['broker_name'];?></td>
													<td><?php echo $v['phone'];?></td>
											<?php }}}else{ ?>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											<?php }?>
                                            <td><?php echo date('Y-m-d', $value['create_time']);?></td>
                                            <td>
                                                <?php if ($value['status'] == 0) { ?>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/project_cooperate_lol_success_applay/modify/<?php echo $value['id']; ?>/" >审核</a>
                                                <?php } else if ($value['status'] == 1) {?>
                                                审核通过
                                                <?php } else if ($value['status'] == 2) { ?>
                                                驳回
                                                <?php }?><a href="<?php echo MLS_ADMIN_URL; ?>/project_cooperate_lol_success_applay/modify/<?php echo $value['id']; ?>/1" >查看</a>
                                            </td>
                                        </tr>
                                    <?php }}?>


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
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->



        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
<script>
function del(id){
    var is_del = confirm('确定删除该用户组吗？');
    del_url = "<?php echo MLS_ADMIN_URL;?>/user_group/del/"+id;
    if(is_del){
        window.location.href = del_url;
    }
}
</script>
<?php require APPPATH.'views/footer.php'; ?>

