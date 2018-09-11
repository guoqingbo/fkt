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
                                                    权限模块:&nbsp&nbsp&nbsp&nbsp
							<select class="select w90" name="mid" >
							<?php foreach ($modules as $key => $vo) { ?>
								<option value="<?php echo $vo['id']?>" <?php echo ($permission_menu['mid']==$vo['id'])?selected:"" ?>><?php echo $vo['name']?></option>
							<?php } ?>
							</select>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    权限名称:&nbsp&nbsp&nbsp&nbsp<input type="search" name="pname" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $permission_menu['pname'] ?>">
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    状态:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="status" value="0" <?php echo ($permission_menu['status']==0)?checked:"" ?>>失效</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="status" value="1" <?php echo ($permission_menu['status']==1)?checked:"" ?>>有效</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    当前经济人是否默认拥有该操作权限:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_this_user_hold" value="0" <?php echo ($permission_menu['is_this_user_hold']==0)?checked:"" ?>>否</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_this_user_hold" value="1" <?php echo ($permission_menu['is_this_user_hold']==1)?checked:"" ?>>是</label>
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
                                                <input type='hidden' name='submit_flag' value='modify'/>
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
                window.location.href = "<?php echo MLS_ADMIN_URL . '/signatory_purview_modules/' ?>";
            }, 1000);
        });
    </script>
<?php } ?>

<script>
	function	goback(){
		location.href = "<?=MLS_ADMIN_URL?>/signatory_purview_modules/";
	}
</script>

<?php require APPPATH . 'views/footer.php'; ?>

