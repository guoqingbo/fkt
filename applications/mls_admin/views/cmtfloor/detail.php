<?php require APPPATH.'views/header.php';date_default_timezone_set("PRC"); ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">楼栋号详情</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            楼盘名称：<?php echo $cmt_name;?>&nbsp;&nbsp;&nbsp;楼栋号：<?php echo $floor_num_str;?> &nbsp;&nbsp;&nbsp;<a href="../add_floor/<?php echo $cmt_id;?>">添加楼栋</a>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <br>
                                <?php foreach($cmt_room_arr as $k => $v){?>
                                <?php echo $k.'栋';?>&nbsp&nbsp&nbsp<a href="#" onclick="del('<?php echo $v['id'];?>');">删除</a>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <tbody>
                                        <?php foreach($v['data'] as $key => $value){?>
                                        <tr class="gradeA">
                                            <?php foreach($value['room'] as $i => $j){?>
                                            <td><a href="../room_detail/<?php echo $j['id'];?>" <?php if(!empty($j['room_count']) && !empty($j['hall_count']) && !empty($j['area'])){echo 'style="color:#919191;"';} ?>><?php echo $j['room_num'];?></a></td>
                                            <?php }?>
                                        </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                                <?php }?>
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
<script type="text/javascript">
function del(floor_id){
    var is_del = confirm('确定删除该楼栋');
    if(is_del){
        window.location.href = '<?php echo MLS_ADMIN_URL;?>/cmtfloor/del_floor/'+floor_id;
    }
}
</script>
