<?php require APPPATH.'views/header.php'; ?>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<link href="<?=MLS_SOURCE_URL ?>/mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<link href="<?=MLS_SOURCE_URL ?>/mls/css/v1.0/autocomplete.css" rel="stylesheet" type="text/css">
<style>
    tr {text-align:center;}

    .ui-menu {background: none repeat scroll 0 0 #fff;border: 1px solid #d1d1d1;float: left; border-top:none;list-style: none;margin: 0;padding: 0;}
    .ui-menu .ui-menu-item {list-style: none;background: none repeat scroll 0 0 #fff;clear: left;float: left;margin: 0;padding: 0;width: 100%;}
    .ui-menu .ui-menu-item a {color: #333;cursor: pointer;display: block;font-family: Arial,Helvetica,sans-serif;height: 24px;line-height: 24px;overflow: hidden;padding: 0 4px;text-align: left;text-decoration: none;}
    .ui-menu .ui-menu-item a.ui-state-hover, .ui-menu .ui-menu-item a.ui-state-active {background: none repeat scroll 0 0 #ff9804;color: #fff;font-weight: normal;text-decoration: none;}
</style>

    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?php echo $title;?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(''==$addResult){; ?>
			<script type="text/javascript">
			$(function() {
				$("#apply_add").live("change",function(){
					var file = $(this).val();
					if(file != "")
					{
						var patrn=/(.apk|.ipa|.exe)$/;
						if (patrn.exec(file))
						{
							$("#fileform").submit();
						}
						else
						{
							alert("文件格式不正确,请重新选择");
							return false;
						}
					}
				});
			})
			</script>
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
									   <input type='hidden' name='submit_flag' value='add'/>
                                       应用名称<font color="red">*</font>:&nbsp;&nbsp;&nbsp;<input type="text" name="apply_name" class="form-control input-sm" aria-controls="dataTables-example" id='apply_name' value="" />
									  </label>
									</div>
								</div>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
										<label>
										   版&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp;本<font color="red">*</font>:&nbsp&nbsp&nbsp<input type="search" name="version" class="form-control input-sm" aria-controls="dataTables-example" id="version" value="">
										</label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
                                       是否强制更新<font color="red">*</font>:&nbsp&nbsp&nbsp;<input type="radio" name="is_forced" class="form-control input-sm is_forced" aria-controls="dataTables-example" id='' value="1">是&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									   <input type="radio" name="is_forced" class="form-control input-sm is_forced" aria-controls="dataTables-example" id='' value="0" checked>否
									  </label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
                                       版本类型<font color="red">*</font>:&nbsp&nbsp&nbsp;<input type="radio" name="version_type" class="form-control input-sm version_type" aria-controls="dataTables-example" id='' value="1" checked>IOS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									   <input type="radio" name="version_type" class="form-control input-sm version_type" aria-controls="dataTables-example" id='' value="2">Android
									  </label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
                                       应用地址<font color="red">*</font>:&nbsp&nbsp&nbsp;<input type="text" name="update_url" class="form-control input-sm" aria-controls="dataTables-example" id='update_url' value="" />
									  </label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
                                       应用大小<font color="red">*</font>:&nbsp&nbsp&nbsp;<input type="text" name="apply_size" class="form-control input-sm" aria-controls="dataTables-example" id='apply_size' value="" />
									  </label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
                                       更新内容<font color="red">*</font>:&nbsp&nbsp&nbsp;<textarea type="text" name="update_content" class="form-control input-sm" aria-controls="dataTables-example" id='update_content' rows='5' cols='120'></textarea>
									  </label>
									</div>
								</div>
								<?php if(!empty($mess_error)){?>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
											<font color='red'><?php echo $mess_error; ?></font>
									  </div>
									  </div>
								<?php } ?>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									   <input class="btn btn-primary" type="submit" value="提交">
									   <a class="btn btn-primary" onclick='reset()'>重置</a>
									  </div>
									  </div>
								</div>
							</form>
								</div>
                               </div>
                               </div>
                              </div>
                        <!-- /.panel-body -->
						<script>
						function reset(){
							$('#apply_name').val('');
							$('#version').val('');
							$('input[name="is_forced"]:checked').removeAttr('checked');
							$('input[name="version_type"]:checked').removeAttr('checked');
							$('#update_url').val('');
							$('#apply_size').val('');
							$('#update_content').val('');
						}
						</script>

                    </div>
            <?php }else if(0===$addResult){ ?>
            	<div>添加失败</div>
            <?php }else{?>
            	<div>添加成功</div>
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
