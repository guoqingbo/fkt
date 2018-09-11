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

									<input type="hidden" name="pg" value="1">
									<!--<input class="btn btn-primary" type="submit" value="查询" name="search">&nbsp;&nbsp;&nbsp;-->
									<input class="btn btn-primary" type="button" onclick="javascript:location.href = '/joinus/'" value="重置">
									<label>
									<div class="dataTables_length" id="dataTables-example_length">
										&nbsp;&nbsp;<a class="btn btn-primary" href='/joinus/exportReport/'>导出</a>
									</div>
									</label>
								</form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>姓名</th>
                                            <th>手机</th>
                                            <th>电子邮箱</th>
											<th>申请省份</th>
											<th>申请市区</th>
                                            <th>公司名称</th>
                                            <th>公司地址</th>
                                            <th>公司电话</th>
                                            <th>申请时间</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(isset($list) && !empty($list)){foreach($list as $key=>$val){?>
                                        <tr class="gradeA">
                                            <td><?=$val['id'];?></td>
                                            <td><?=$val['name'];?></td>
                                            <td><?=$val['phone'];?></td>
                                            <td><?=$val['email'];?></td>
											<td><?=$val['province'];?></td>
											<td><?=$val['city'];?></td>
                                            <td><?=$val['company_name'];?></td>
                                            <td><?=$val['address'];?></td>
                                            <td><?=$val['company_phone'];?></td>
                                            <td><?=date('Y-m-d', $val['createtime']);?></td>
                                            <td><a href='/joinus/detail/<?=$val['id'];?>'>详情</a></td>
                                        </tr>
									<?php }}else{?>
									<tr class='gradeA'><td colspan=14 style='text-align:center;color:red;font-weight:bold;'>暂无信息！</td></tr>
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

