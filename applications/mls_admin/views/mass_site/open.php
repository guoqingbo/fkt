<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">启用群发站点</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(1===$delResult){ ?>
            	<div>启用成功</div>
            <?php }else{?>
            	<div>启用失败</div>
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
		 window.location.href="<?php echo MLS_ADMIN_URL.'/mass_site/'?>";
	 },1000);
});
</script>
<?php require APPPATH.'views/footer.php'; ?>

