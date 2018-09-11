<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">删除消息</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(1===$delResult){
            	echo "<script>alert('删除成功')</script>";
            	echo "<script>window.history.go(-1);</script>";
                  }else{
            	echo "<script>alert('删除失败')</script>";
            	echo "<script>window.history.go(-1);</script>";
                  }?>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
  


        </div>
        <!-- /#page-wrapper -->

    </div>

</script>
<?php require APPPATH.'views/footer.php'; ?>

