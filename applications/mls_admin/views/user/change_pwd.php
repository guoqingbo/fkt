<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">修改密码</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(''==$modifyResult){; ?>
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
									  <label>
									   输入当前密码:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="password" name="password" class="form-control input-sm" aria-controls="dataTables-example" value="">
									  </label>
									</div>
								</div>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
									   输入新密码:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="password" name="password1" class="form-control input-sm" aria-controls="dataTables-example" value="">
									  </label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
									   输入确认密码:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="password" name="password2" class="form-control input-sm" aria-controls="dataTables-example" value="">
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
									   <input type="hidden" name="submit_flag" value="modify">
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
            <?php }else if(1===$modifyResult){ ?>
            	<div>更新成功</div>
            <?php }else{?>
            	<div><?=$modifyResult?></div>
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

