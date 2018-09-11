<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">租房采集内容列表</h1>
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
							<form name="search_form" method="post" action="" >
						 	 <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
							  <div class="row">
								<div class="row">
						        <div class="col-sm-6" style="width:100%">
									日期：
									  <label>
									   <input type="text" name="start_time" style="width:183px" id="start_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $start_time;?>" onclick="WdatePicker()"> 到 <input type="text" id="start_time" name="end_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $end_time;?>" onclick="WdatePicker()">
									  </label>
									   <input type="hidden" name="pg" value="1">
									   <input class="btn btn-primary" type="submit" value="查询">&nbsp;&nbsp;&nbsp;&nbsp;
									   <input class="btn btn-primary" type="button" value="导出" onclick="javascript:window.location.href='<?php echo MLS_ADMIN_URL."/rent_house_collect/export"?>'"><br>
								</div>
								</div>
                               </form>
								</div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>内容标题</th>
                                            <th>采集时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
																			if(isset($rent_house_collect) && !empty($rent_house_collect)){
																				foreach($rent_house_collect as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php echo $value['house_title'];?></td>
											<td><?php echo date("Y-m-d H:i:s",$value['createtime']);?></td>
                                        </tr>
                                    <?php }}?>


                                    </tbody>
                                </table>

                                <div class="row">
                                  <div class="col-sm-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

                                       <ul class="pagination" style="margin:-8px 0;padding-left:20px">
									   										<?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/rent_house_collect/index');?>
									   									 </ul>
                                    </div>
                                  </div>
                                <div style="color:blue;position:absolute;right:33px;">
                                    <b>共查到<?php echo $rent_house_collect_num;?>条数据</b>
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
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>

