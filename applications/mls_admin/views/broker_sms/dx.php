<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">经纪人验证码</h1>
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
                                <form name="search_form" method="post" action="dx/index">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>&nbsp&nbsp 经纪人手机号码
                                                        <input type="tel" name="phone">
                                                    </label>
                                                    <label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            <input class="btn btn-primary" type="submit" value="查询">
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
                                            <th>手机号码</th>
                                            <th>验证码</th>
                                            <th>验证状态</th>
                                            <th>创建时间</th>
                                            <th>过期时间</th>
                                            <th>模块</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php  if($broker_sms &&!empty($broker_sms)){
                                    foreach($broker_sms as $value){ ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php echo $value['phone'];?></td>
                                            <td><?php echo $value['validcode'];?></td>
                                            <td><?php echo ($value['esta']==1)?'已验证':'未验证';?></td>
                                            <td><?php echo date("Y-m-d H:i:s", $value['createtime']) ;?></td>
                                            <td><?php echo date("Y-m-d H:i:s", $value['expiretime']) ;?></td>
                                            <?php
                                            switch ($value['type']) {
                                                case 1:$type = '登录';break;
                                                case 2:$type = '修改资料';break;
                                                case 3:$type = '注册';break;
                                                case 4:$type = '找回密码';break;
                                            }
                                            ?>
                                            <td><?php echo $type;?></td>
                                        </tr>
                                    <?php }}?>


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
    <!-- /#wrapper -->
<?php require APPPATH.'views/footer.php'; ?>

