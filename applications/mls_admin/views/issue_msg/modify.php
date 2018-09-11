<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">修改消息</h1>
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
									  </div>
									</div>
								</div>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <input type='hidden' name='submit_flag' value='modify'/>
									  <label>
									   标题:&nbsp&nbsp&nbsp&nbsp<textarea name="title" class="form-control" aria-controls="dataTables-example" rows="3" cols="52" style='resize:none' ><?php echo $issue_msg['title']?></textarea>
									  </label>
							        </div>
							    </div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
									   内容:&nbsp&nbsp&nbsp&nbsp<textarea name="message" class="form-control" aria-controls="dataTables-example" rows="7" cols="52" style='resize:none'><?php echo $issue_msg['message']?></textarea>
									  </label>
									</div>
							    </div>
							    <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            <input type='hidden' name='submit_flag' value='modify'/>
                                            <input type="radio" name="url_type" value="1" <?=in_array($issue_msg['url'], $url_arr)?'checked':''?>>
                                            <select name="url1" id="url1">
                                            <?php foreach($arr_url_type as $key=>$val) { ?>
                                                <option value="<?=$val['url']?>" <?=$issue_msg['url']==$val['url']?'selected':''?>><?=$val['name']?></option>
                                            <?php } ?>
                                            </select>
                                            <input type="radio" name="url_type" value="2" <?=!in_array($issue_msg['url'], $url_arr)?'checked':''?>>
                                            <input type="text" name="url2" id="url2" class="form-control input-sm" aria-controls="dataTables-example" value="<?=!in_array($issue_msg['url'], $url_arr)?$issue_msg['url']:''?>" size="60" >
                                        </label>
									</div>
							    </div>
									  <?php if(!empty($issue_msg_error)){?>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
											<font color='red'><?php echo $issue_msg_error; ?></font>
									  </div>
									  </div>
									  <?php } ?>									  								  
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									   <input class="btn btn-primary" type="submit" value="提交">
									   <input class="btn btn-primary" type="button" value="取消" onclick="javascript:location.href='/issue_msg/'">
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
            <?php }else if(0===$modifyResult){
            	echo "<script>alert('更新失败');</script>";
            	echo "<script>window.history.go(-1);</script>";
                }else{
            	echo "<script>alert('更新成功')</script>";
                echo "<script>window.history.go(-1)</script>";}?>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
  


        </div>
        <!-- /#page-wrapper -->

    </div>
<script type="text/javascript">
    $(function(){
        $("input[name='url_type']").click(function(){
            if ($(this).val() == 0) {
                $('#urlId').css('display', 'inline');
            } else {
                $('#urlId').css('display', 'none');
            }
        });
    });
</script>
<?php require APPPATH.'views/footer.php'; ?>

