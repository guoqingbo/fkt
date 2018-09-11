<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">修改奖品菜单</h1>
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
                                                    <input type='hidden' name='id' value='<?=$reward_details['id']?>'/>
                                                    奖品类型:&nbsp&nbsp&nbsp&nbsp
                                                    <select name="type" class="form-control input-sm" aria-controls="dataTables-example">
                                                        <option value="0">请选择</option>
                                                        <?php foreach($reward_type as $key=>$val){?>
                                                        <option value="<?php echo $val['id'];?>" <?php if($val['id']==$reward_details['type']){echo "selected='selected'";} ?>><?php echo $val['name'];?></option>
                                                        <?php }?>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
										<div class="col-sm-6" style="width:100%">
											<div class="dataTables_length" id="dataTables-example_length">
												<label>
													开放时间:&nbsp&nbsp&nbsp&nbsp
													<input type="text" name="open_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$reward_details['open_time']?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})">
												</label>
											</div>
										</div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    是否有效:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="valid_flag" value="0" <?php if ($reward_details['valid_flag']==0) {echo "checked='checked'";}?>>无效</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="valid_flag" value="1" <?php if ($reward_details['valid_flag']==1) {echo "checked='checked'";}?>>有效</label>
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
                window.location.href = "<?php echo MLS_ADMIN_URL . '/project_cooperate_lol_reward/' ?>";
            }, 1000);
        });
    </script>
<?php } ?>

<script>
	function	goback(){
		location.href = "<?=MLS_ADMIN_URL?>/project_cooperate_lol_reward/";
	}
</script>

<?php require APPPATH . 'views/footer.php'; ?>

