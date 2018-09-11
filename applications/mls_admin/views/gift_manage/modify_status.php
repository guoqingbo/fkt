<?php require APPPATH . 'views/header.php'; ?>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<div id="wrapper">
    <div style='width:267px;height:158px'>
        <?php if ($modifyResult === '') { ?>
            <div class="row" style="margin-top:-15px; margin-left:20px;">
                <div class="col-lg-12">
                    <div class="panel panel-default" style="margin:20px 0; padding:20px 0;">
                                <form name="add_form" method="post" action="">
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
												<?php if($type == 1){?>
												下架时间<font color="red">*</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input name="down_time" id="down_time" class="input_text input_text_r w150 form-control input-sm" type="text" style="height:30px; line-height: 30px;" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})" autocomplete='off'/>
												<?php }else{?>
												确定上架吗？
												<?php } ?>
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
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="submit_flag" value="modify">
                                </form>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } else if (0 === $modifyResult) { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default" style="margin:20px 0; padding:20px 0;">
						<h1><b>修改失败</b></h1>
					</div>
				</div>
			</div>
        <?php } else { ?>
            <div class="row" style="margin-top:-15px; margin-left:20px;">
                <div class="col-lg-12">
                    <div class="panel panel-default" style="margin:20px 0; padding:20px 0;">
						<h1><b>修改成功</b></h1>
					</div>
				</div>
			</div>
        <?php } ?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>
</div>
<div class="col-lg-4" style="display:none" id="js_note1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            提示框
            <button type="button" class="close JS_Close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="panel-body">
            <p id="warning_text"></p>
        </div>
    </div>
</div>

<?php require APPPATH . 'views/footer.php'; ?>

