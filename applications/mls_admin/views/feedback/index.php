<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">意见反馈列表</h1>
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
                                                    <label>&nbsp&nbsp 城市
                                                        <select name="city_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <?php foreach($all_city_data as $k => $v){?>
                                                            <option value="<?php echo $v['id'];?>"<?php if(isset($where_cond['city_id'])){if($v['id']==$where_cond['city_id']){echo 'selected="selected"';}}?>><?php echo $v['cityname'];?></option>
                                                            <?php }?>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            <input type="hidden" name="pg" value="1">
                                                            <input class="btn btn-primary" type="submit" value="查询">
                                                        </div>
                                                    </label>
                                            </div>
                                        </div>
                                </form>
                            </div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>内容</th>
                                            <th>联系电话</th>
                                            <th>城市</th>
                                            <th>提交时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(isset($feedback_list) && !empty($feedback_list)){
                                            foreach($feedback_list as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php echo $value['feedback'];?></td>
                                            <td><?php echo $value['telno'];?></td>
                                            <td><?php echo $value['city_name'];?></td>
                                            <td><?php echo date('Y-m-d H:i:s',$value['dateline']);?></td>
                                            <td>
                                            	<a href="#" onclick="del(<?php echo $value['id'];?>);">删除</a>
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
    var is_del = confirm('确定删除该意见反馈吗？');
    del_url = "<?php echo MLS_ADMIN_URL;?>/feedback/del/"+id;
    if(is_del){
        window.location.href = del_url;
    }
}
</script>
<?php require APPPATH.'views/footer.php'; ?>

