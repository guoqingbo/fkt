<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">删除权限模块</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(11===$result){ ?>
            	<div>重新初始化权限成功</div>
            <?php }else{?>
            	<div>重新初始化权限失败，请重新初始化！</div>
            <?php }?>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->



        </div>
        <!-- /#page-wrapper -->

    </div>
<script>
$(function(){
	 setTimeout(function(){
		 window.location.href="<?php echo MLS_ADMIN_URL.'/company/index'?>";
	 },2000);
});
</script>
<?php require APPPATH.'views/footer.php'; ?>

