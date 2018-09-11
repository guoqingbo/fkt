<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">删除权限二级菜单功能</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(1===$delResult){ ?>
            	<div>删除成功</div>
            <?php }else{?>
            	<div>删除失败</div>
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
		 window.location.href="<?php echo MLS_ADMIN_URL.'/permission_secondtab_menu/index/'.$module_id.'/'.$tab_id; ?>";
	 },1000);
});
</script>
<?php require APPPATH.'views/footer.php'; ?>

