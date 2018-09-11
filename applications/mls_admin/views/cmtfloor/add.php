<?php require APPPATH.'views/header.php';date_default_timezone_set("PRC"); ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">添加楼栋号</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(''===$room_add_result){?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                                <form method="post" action="">
                            <div>
                            添加楼栋号<font color="red">*</font>：<input type="text" name="num"/> 百度坐标 X：<input type="text" name="baidu_x"/> 百度坐标 Y：<input type="text" name="baidu_y"/>
                            <a onclick="window.open('FRAME/map/map_cp.php')" href="#">获取地图坐标</a>
                            </div>
                            <br>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>单元明细</th>
                                            <th>楼层说明</th>
                                            <th>每层房间号码</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="gradeA">
                                            <td>起始单元<font color="red">*</font>：<input type="text" name="unit_start"/></td>
                                            <td>起始楼层<font color="red">*</font>：<input type="text" name="level_start"/></td>
                                            <td>起始房间<font color="red">*</font>：<input type="text" name="room_start"/></td>
                                        </tr>
                                        <tr class="gradeA">
                                            <td>终止单元<font color="red">*</font>：<input type="text" name="unit_end"/></td>
                                            <td>终止楼层<font color="red">*</font>：<input type="text" name="level_end"/></td>
                                            <td>终止房间<font color="red">*</font>：<input type="text" name="room_end"/></td>
                                        </tr>
                                        <tr class="gradeA">
                                            <td>没有的单元：<input type="text" name="unit_miss"/></td>
                                            <td>没有的楼层：<input type="text" name="level_miss"/></td>
                                            <td>没有的房间：<input type="text" name="room_miss"/></td>
                                        </tr>
                                        <tr class="gradeA">
                                            <td><input type="radio" name="room_status" value="1" checked="checked"/>房间号延续 &nbsp;&nbsp;&nbsp;<input type="radio" name="room_status" value="2"/>房间号重复</td>
                                        </tr>
                                    </tbody>
                                </table>
                                                楼栋规则说明：<br>
                                            1.楼层数字（2位）+房间号（数字2位）如：一楼，0101,0102,0103；二楼，0201,0202,0203...以此类推<br>
                                            2.楼层数字（2位）+房间号（字母1位）如：一楼，01A,01B,01C；二楼，02A,02B,02C...以此类推<br>        
                                            3.楼层数字（2位）+房间号（字母1位 数字1位）如：一楼，01A1,01A2,01A3；二楼，02A1,02A2,02A3...以此类推<br>   
                                            <?php if (!empty($mess_error)) { ?>
                                            <div class="col-sm-6" style="width:100%">
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <font color='red'><?php echo $mess_error; ?></font>
                                                    </div>
                                                </div>
                                            <?php } ?>	
                                            <input class="btn btn-primary" type="submit" value="确定"/>
                                            <input class="btn btn-primary" type="button" value="取消" onclick="window.history.go(-1);"/>
                                </form>
                               </div>
                        <?php }else if($room_add_result===0){?>
                        <div><h3>添加失败</h3></div>
                        <?php }else{?>
                        <div><h4>添加成功</h4>
                        <input class="btn btn-primary" type="button" value="返回" onclick="window.location.href = '../floor_detail/<?php echo $cmt_id;?>';"/>
                        </div>
                        <?php } ?>
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

