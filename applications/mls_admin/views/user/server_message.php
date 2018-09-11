<?php require APPPATH.'views/header.php';date_default_timezone_set("PRC"); ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">系统信息</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>服务器软件</th>
                                            <th>服务器系统</th>
                                            <th>PHP版本</th>
                                            <th>服务器地址</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="gradeA">
                                            <td><?php echo $sys_os;?></td>
                                            <td><?php echo $server_sys;?></td>
                                            <td><?php echo $php_sversion;?></td>
                                            <td><?php echo $server_address;?></td>
                                        </tr>

                                    </tbody>
                                </table>
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


<?php require APPPATH.'views/footer.php'; ?>

