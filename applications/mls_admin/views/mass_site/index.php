<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">群发站点列表</h1>
                    <h3><a href="<?php echo MLS_ADMIN_URL;?>/mass_site/add/">添加</a></h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <form name="search_form" method="post" action="" >
				<input type="hidden" name="pg" value="1">
                            </form>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>网站名称</th>
                                            <th>别名</th>
                                            <th>网站介绍</th>
                                            <th>启用状态</th>
                                            <th>认证状态</th>
                                            <th>是否传图</th>
                                            <th>是否刷新</th>
                                            <th>功能</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
																			if(isset($mass_site) && !empty($mass_site)){
																				foreach($mass_site as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php echo $value['name'];?></td>
                                            <td><?php echo $value['alias'];?></td>
                                            <td><?php echo $value['intro'];?></td>
                                            <td>
                                                <?php if($value['status']==0){echo "未启用";}else if($value['status']==1){echo "<span style='color:red'><b>已启用</b></span>";}?>
                                                <?php if($value['is_fix']==1){echo "<span style='color:red'><b>&amp; 维护中</b></span>";}?>
                                            </td>
                                            <td>
                                                <?php if($value['is_auth']==0){echo "未认证";}else if($value['is_auth']==1){echo "<span style='color:red'><b>已认证</b></span>";}?>
                                            </td>
                                            <td>
                                                <?php if($value['is_upic']==0){echo "不可传图";}else if($value['is_upic']==1){echo "<span style='color:red'><b>可以传图</b></span>";}?>
                                            </td>
                                            <td>
                                                <?php if($value['is_refresh']==0){echo "不可刷新";}else if($value['is_refresh']==1){echo "<span style='color:red'><b>可以刷新</b></span>";}?>
                                            </td>
                                            <td>
												<?php if($value['status']==0){?>
													<a href="<?php echo MLS_ADMIN_URL;?>/mass_site/open/<?php echo $value['id'];?>" >启用</a>
												<?php }else if($value['status']==1){?>
													<a href="<?php echo MLS_ADMIN_URL;?>/mass_site/close/<?php echo $value['id'];?>" >关闭</a>
												<?php }?>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/mass_site/modify/<?php echo $value['id'];?>" >修改</a>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/mass_site/del/<?php echo $value['id'];?>" onclick="return checkdel()">删除</a>
                                            </td>
                                        </tr>
                                    <?php }}?>


                                    </tbody>
                                </table>

                                <div class="row">
                                  <div class="col-sm-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

                                       <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                            <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/mass_site/index');?>
                                        </ul>
                                    </div>
                                  </div>
                                <div style="color:blue;position:absolute;right:33px;">
                                    <b>共查到<?php echo $mass_site_num;?>条数据</b>
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
function checkdel(){
	if(confirm("确实要删除吗？"))
    {
		return true;
	}
     else
    {	return false;
	}
}
</script>
<?php require APPPATH.'views/footer.php'; ?>

