<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">修改权限菜单</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <?php if ('' == $modifyResult) {
; ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <input type='hidden' name='submit_flag' value='modify'/>
                                                    权限模块:&nbsp&nbsp&nbsp&nbsp
                                                    <select name="module_id" class="form-control input-sm" aria-controls="dataTables-example">
                                                        <option value="0">请选择</option>
                                                        <?php foreach($module_list as $key=>$val){?>
                                                        <option value="<?php echo $val['id'];?>" <?php if($val['id']==$permission_menu['module_id']){echo "selected='selected'";} ?>><?php echo $val['name'];?></option>
                                                        <?php }?>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    菜单名称:&nbsp&nbsp&nbsp&nbsp<input type="search" name="name" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $permission_menu['name'] ?>">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    链接地址:&nbsp&nbsp&nbsp&nbsp<input type="search" name="url" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $permission_menu['url'] ?>" size="60">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    默认权限:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="init_auth" value="0" <?php if ($permission_menu['init_auth']==0) {echo "checked='checked'";}?>>无</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="init_auth" value="1" <?php if ($permission_menu['init_auth']==1) {echo "checked='checked'";}?>>有</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    菜单图标:&nbsp&nbsp&nbsp&nbsp<input type="search" name="icon" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $permission_menu['icon'] ?>">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    排序数值:&nbsp&nbsp&nbsp&nbsp<input type="search" name="order" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $permission_menu['order'] ?>" size="10">
                                                数值越大越靠前
                                                </label>
                                            </div>
                                        </div>
                                        <?php if (!empty($mess_error)) { ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <font color='red'><?php echo $mess_error; ?></font>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="submit" value="提交">
                                                <input class="btn btn-primary"  type="button" onclick="goback()" value="取消">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } else if (0 === $modifyResult) { ?>
            <div>更新失败</div>
        <?php } else { ?>
            <div>更新成功</div>
<?php } ?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->



</div>
<!-- /#page-wrapper -->

</div>
<?php if ($modifyResult != "") { ?>
    <script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/permission_menu/' ?>";
            }, 1000);
        });
    </script>
<?php } ?>

<script>
	function	goback(){
		location.href = "<?=MLS_ADMIN_URL?>/permission_menu/";
	}
</script>

<?php require APPPATH . 'views/footer.php'; ?>

