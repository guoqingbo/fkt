<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">设置管理员</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(0===$modifyResult){ ?>
                <?php if($is_set){?>
            	<div>设置失败</div>
                <?php }else{ ?>
            	<div>设置失败,该帐号能管理多个分站。</div>
                <?php } ?>
            <?php }else{?>
            	<div>设置成功</div>
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

