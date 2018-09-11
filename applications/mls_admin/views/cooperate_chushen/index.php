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
                                <form action="" method="post" name="search_form">
				    <input type="hidden" name="submit_flag" value="search">
				    <div style="width:100%" class="col-sm-6">
					订单编号：&nbsp;<input type="text" value="<?=$param_array['order_sn']?>" name="order_sn" aria-controls="dataTables-example" class="form-control input-sm " style="width:120px;display: inline-block;">
					甲方经纪人姓名：&nbsp;<input type="text" value="<?=$param_array['broker_name_a']?>" name="broker_name_a" aria-controls="dataTables-example" class="form-control input-sm " style="width:150px;display: inline-block;">
					甲方公司名称：&nbsp;<input type="text" value="<?=$param_array['company_name_a']?>" name="company_name_a" aria-controls="dataTables-example" class="form-control input-sm " style="width:150px;display: inline-block;">
					甲方门店名称：&nbsp;<input type="text" value="<?=$param_array['agency_name_a']?>" name="agency_name_a" aria-controls="dataTables-example" class="form-control input-sm " style="width:150px;display: inline-block;">
				    </div>
				    <div style="width:100%;margin:10px 0px;" class="col-sm-6">
					乙方经纪人姓名：&nbsp;<input type="text" value="<?=$param_array['broker_name_b']?>" name="broker_name_b" aria-controls="dataTables-example" class="form-control input-sm " style="width:150px;display: inline-block;">
					乙方公司名称：&nbsp;<input type="text" value="<?=$param_array['company_name_b']?>" name="company_name_b" aria-controls="dataTables-example" class="form-control input-sm " style="width:150px;display: inline-block;">
					乙方门店名称：&nbsp;<input type="text" value="<?=$param_array['agency_name_b']?>" name="agency_name_b" aria-controls="dataTables-example" class="form-control input-sm " style="width:150px;display: inline-block;">
					&nbsp;&nbsp;审核状态：&nbsp;
					<select name="status" name="status">
						<option value="">不限</option>
						<option value="0" <?=(isset($param_array['status']) && $param_array['status']=='0')?'selected':''?>>审核中</option>
						<option value="1" <?=(isset($param_array['status']) && $param_array['status']=='1')?'selected':''?>>审核通过</option>
						<option value="2" <?=(isset($param_array['status']) && $param_array['status']=='2')?'selected':''?>>驳回</option>
					</select>
					<input type="hidden" name="pg" value="1">
					<input class="btn btn-primary" type="submit" value="查询" name="search">&nbsp;&nbsp;&nbsp;
					<input class="btn btn-primary" type="button" onclick="javascript:location.href = '/cooperate_chushen/'" value="重置">
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
                                    <?php if(isset($list) && !empty($list)){foreach($list as $key=>$val){?>
                                        <tr class="gradeA">
                                            <td><?php echo $val['id'];?></td>
                                            <td><?php echo $val['order_sn'];?></td>
                                            <td><?php echo $val['seller_owner'];?></td>
                                            <td><?php echo $val['seller_telno'];?></td>
                                            <td><?php echo $val['company_name_a'];?></td>
                                            <td><?php echo $val['agency_name_a'];?></td>
                                            <td><?php echo $val['broker_name_a'];?></td>
                                            <td><?php echo $val['phone_a'];?></td>
                                            <td><?php echo $val['company_name_b'];?></td>
                                            <td><?php echo $val['agency_name_b'];?></td>
                                            <td><?php echo $val['broker_name_b'];?></td>
                                            <td><?php echo $val['phone_b'];?></td>
                                            <td><?php echo date('Y-m-d', $val['create_time']);?></td>
                                            <td>
                                            <?php if($val['status'] == 0){ ?>
                                                    <a href="<?php echo MLS_ADMIN_URL; ?>/cooperate_chushen/modify/<?php echo $val['id']; ?>/" >审核</a>
                                            <?php } else if ($val['status'] == 1) {?>
                                                    <font color="green">审核通过</font>
                                            <?php } else if ($val['status'] == 2) { ?>
                                                    <font color="red">驳回</font>
                                            <?php }?>
                                            </td>
                                        </tr>
				    <?php }}else{?>
					<tr class='gradeA'><td colspan=14 style='text-align:center;color:red;font-weight:bold;'>暂无您查询的合作审核资料！</td></tr>
				    <?php }?>
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

