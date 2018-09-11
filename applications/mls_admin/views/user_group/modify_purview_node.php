<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">权限列表</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <?php if(''==$modifyResult){?>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body"> 
                                <form name="search_form" method="post" action="">
                                    <input type='hidden' name="submit_flag" value="modify"/>
                                <table class="table table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>类型</th>
                                            <th>权限名称</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                        if(isset($purview_node_arr) && !empty($purview_node_arr)){
                                            foreach($purview_node_arr as $key=>$value){
                                      ?>
                                        <tr class="gradeA">
                                            <td><b>一级菜单</b></td>
                                            <td><b><?php echo $value['p_name'];?></b></td>
                                        </tr>
                                    <?php
                                           foreach($value['purview_node_children'] as $k => $v){
                                    ?>
                                        <tr class="gradeA">
                                            <td>二级菜单</td>
                                            <td><input type='checkbox' value="<?php echo $v['id'];?>" name='purview_nodes[]' <?php if(strstr($this_purview_nodes,$v['id'])!=false){echo 'checked="checked"';}?>/><?php echo $v['name'];?></td>
                                        </tr>
                                        <?php }}}?>
                                    </tbody>
                                </table>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									   <input class="btn btn-primary" type="button" value="全选" id="select_all">
									   <input class="btn btn-primary" type="button" value="重置" id="select_none">
									   <input class="btn btn-primary" type="submit" value="提交">
									  </div>
									  </div>	
                                </form>
                               </div>
                               </div>
                              </div>
                        <!-- /.panel-body --> 
                    </div>
                    <!-- /.panel -->
                    <?php }else if(0===$modifyResult){?>
                        <div>更新失败</div>
                    <?php }else{?>
                        <div>更新成功</div>
                    <?php }?>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
  


        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
<script>
</script>
<?php require APPPATH.'views/footer.php'; ?>
<script type="text/javascript">
$(function(){
    $('#select_all').click(function(){
        $('input[name="purview_nodes[]"]').attr('checked',true);
    });
    $('#select_none').click(function(){
        $('input[name="purview_nodes[]"]').attr('checked',false);
    });
});
</script>

