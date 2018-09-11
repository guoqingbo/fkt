<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">设置状态</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(1===$delResult){ ?>
            	<div>设置成功</div>
            <?php }else if(2===$delResult){?>
            	<div>设置失败。不能设置自己的帐号！</div>
            <?php }else{?>
            	<div>设置失败</div>
            <?php }?>
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

