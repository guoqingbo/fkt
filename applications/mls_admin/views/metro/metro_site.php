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
                                                    <label>&nbsp&nbsp地铁线路
                                                        <select name="metro_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <?php foreach($all_metro_line as $k => $v){?>
                                                            <option value="<?php echo $v['id'];?>"<?php if(isset($where_cond['metro_id'])){if($v['id']==$where_cond['metro_id']){echo 'selected="selected"';}}?>><?php echo $v['line_name'];?></option>
                                                            <?php }?>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            <input type="hidden" name="pg" value="1">
                                                            <input class="btn btn-primary" type="submit" value="查询">
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
                                            <th>地铁站点名</th>
                                            <th>所属地铁线路</th>
                                            <th>排序</th>
                                            <th>是否展示</th>
                                            <th>是否设为线路中心站台</th>
											<th>经度</th>
											<th>纬度</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(isset($metro_site_list2) && !empty($metro_site_list2)){
                                            foreach($metro_site_list2 as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td style="width:5%"><?php echo $value['id'];?></td>
                                            <td style="width:15%"><?php echo $value['site_name'];?></td>
                                            <td style="width:15%"><?php echo $value['line_name'];?></td>
                                            <td style="width:5%"><input type="text" name="order" id="order" value="<?php echo $value['order'];?>"/></td>
                                            <td style="width:10%"><?php echo ($value['is_show']==1)?'是':'否';?></td>
                                            <td style="width:10%"><?php echo ($value['line_center_point']==1)?'是':'否';?></td>
											<td style="width:15%"><?php echo $value['b_map_x'];?></td>
											<td style="width:15%"><?php echo $value['b_map_y'];?></td>
                                            <td style="width:20%">
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/metro_site/modify/<?php echo $value['id'];?>" >修改</a>&nbsp;&nbsp;
												<a href="#" onclick="del(<?php echo $value['id'];?>);">删除</a>
                                            </td>
                                        </tr>
                                    <?php }}?>
                                    </tbody>
                                </table>
								<a href="#" class="btn btn-primary" style="float:right;display:none;" onclick="dosave()">保存</a>
                                <div class="row">
									<div class="col-sm-6" style='display:none;'>
										<div class="dataTables_info" id="dataTables-example_info" role="alert" aria-live="polite" aria-relevant="all">
											<input type="checkbox" id="sel-all">&nbsp;&nbsp;全选 &nbsp;&nbsp;<a href="javascript:void(0)"  data-target="#myModal1" data-toggle="modal">加入白名单</a> &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal" data-toggle="modal" >标记到推送库</a> &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal2" data-toggle="modal">标记到备选库</a>
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
$("#order").change(function(){
var order = $(this).val();
alert(order);


})
function del(id){
    var is_del = confirm('确定删除站点吗？');
    del_url = "<?php echo MLS_ADMIN_URL;?>/metro_site/del/"+id;
    if(is_del){
        window.location.href = del_url;
    }
}
</script>
<?php require APPPATH.'views/footer.php'; ?>

