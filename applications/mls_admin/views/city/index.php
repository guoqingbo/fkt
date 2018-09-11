<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">城市列表</h1>
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
                                                            <a class="btn btn-primary" href='add'>添加</a>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                </form>
                            </div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>省份</th>
                                            <th>城市名</th>
                                            <th>城市拼音</th>
                                            <th>状态</th>
                                            <th>排序</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(isset($city_list) && !empty($city_list)){
                                            foreach($city_list as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php echo $value['province'];?></td>
                                            <td><?php echo $value['cityname'];?></td>
                                            <td><?php echo $value['spell'];?></td>
                                            <td><?php echo ($value['status']==1)?'有效':'无效';?></td>
                                            <td><?php echo $value['order'];?></td>
                                            <td>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/city/modify/<?php echo $value['id'];?>" >修改</a>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/city/change_status/<?php echo $value['id'];?>/<?php echo ('1'==$value['status'])?'2':'1';?>" ><?php echo ('1'==$value['status'])?'设置无效':'设置有效';?></a>
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

