<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">房源查看量管理</h1>
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
                                    <div class="col-sm-6" style="width:100%;">
                                        日期：
                                          <label>
                                              <input style="width:183px" type="text" name="start_time" id="start_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['start_time'])){echo $_POST['start_time'];}?>" onclick="WdatePicker()"> &nbsp;&nbsp;到 &nbsp;&nbsp;<input style="width:183px" type="text" id="start_time" name="end_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['end_time'])){echo $_POST['end_time'];}?>" onclick="WdatePicker()">
                                          </label>
                                           <input type="hidden" name="pg" value="1">
                                           &nbsp;&nbsp;<input class="btn btn-primary" type="submit" value="查询">&nbsp;&nbsp;&nbsp;&nbsp;
                                           <input class="btn btn-primary" type="button" value="重置" onclick="res()">
                                    </div>
                                </div>
                             </form>
								</div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="width:20%;">序号</th>
                                            <th style="width:20%;">出售房源查看量(单位:条)</th>
                                            <th style="width:20%;">出租房源查看量(单位:条)</th>
                                            <th style="width:20%;">日期</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(isset($issue_msg) && !empty($issue_msg)){
                                            foreach($issue_msg as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php echo $value['sell_num'];?></td>
                                            <td><?php echo $value['rent_num'];?></td>
                                            <td><?php echo $value['ymd'];?></td>
                                        </tr>
                                    <?php }}else{
                                        echo '<tr class="gradeA"><td colspan=4 style="text-align:center;">暂无您查询的数据！</td></tr>';
                                    }?>


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
                                <div style="color:blue;position:absolute;right:33px;">
                                    <b>共查到<?php echo $view_house_num;?>条数据</b>
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
<script type="text/javascript">
    function res() {
            window.location.href="<?php echo MLS_ADMIN_URL;?>/house_read_stat/index";
    }
</script>
<?php require APPPATH.'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
