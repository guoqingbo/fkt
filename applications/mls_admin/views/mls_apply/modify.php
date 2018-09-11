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
            <?php if(''==$modifyResult){; ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
							<form name="search_form" method="post" action="">
							<input type='hidden' name='submit_flag' value='modify'/>
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
									  <input type='hidden' name='submit_flag' value='edit'/>
                                       应用名称<font color="red">*</font>:&nbsp&nbsp&nbsp<input type="search" name="apply_name" class="form-control input-sm" aria-controls="dataTables-example" id='apply_name' value="<?php echo $list['apply_name'];?>">
									  </label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
										<label>
										   版&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp;本<font color="red">*</font>:&nbsp&nbsp&nbsp<input type="search" name="version" class="form-control input-sm" aria-controls="dataTables-example" id="version" value="<?php echo $list['version'];?>">
										</label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
                                       是否强制更新<font color="red">*</font>:&nbsp&nbsp&nbsp;<input type="radio" name="is_forced" class="form-control input-sm is_forced" aria-controls="dataTables-example" value="1" <?php if($list['is_forced'] == 1){?><?php echo 'checked';?><?php }?>>是&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									   <input type="radio" name="is_forced" class="form-control input-sm is_forced" aria-controls="dataTables-example" value="0" <?php if($list['is_forced'] == 0){?><?php echo 'checked';?><?php }?>>否
									  </label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
                                       版本类型<font color="red">*</font>:&nbsp&nbsp&nbsp;<input type="radio" name="version_type" class="form-control input-sm version_type" aria-controls="dataTables-example" value="1" <?php if($list['version_type'] == 1){?><?php echo 'checked';?><?php }?>>IOS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									   <input type="radio" name="version_type" class="form-control input-sm version_type" aria-controls="dataTables-example" value="2" <?php if($list['version_type'] == 2){?><?php echo 'checked';?><?php }?>>Android&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									   <input type="radio" name="version_type" class="form-control input-sm version_type" aria-controls="dataTables-example" value="3" <?php if($list['version_type'] == 3){?><?php echo 'checked';?><?php }?>>PC
									  </label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
                                       应用地址<font color="red">*</font>:&nbsp&nbsp&nbsp;<input type="text" name="update_url" class="form-control input-sm" aria-controls="dataTables-example" id='update_url' value="<?php echo $list['update_url']?>" />
									  </label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
                                       应用大小<font color="red">*</font>:&nbsp&nbsp&nbsp;<input type="text" name="apply_size" class="form-control input-sm" aria-controls="dataTables-example" id='apply_size' value="<?php echo $list['apply_size']?>" />
									  </label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
                                       更新内容<font color="red">*</font>:&nbsp&nbsp&nbsp;<textarea type="text" name="update_content" class="form-control input-sm" aria-controls="dataTables-example" id='update_content' rows='5' cols='120'><?php echo $list['update_content']?></textarea>
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
            <?php }else if(0===$modifyResult){ ?>
            	<div>更新失败</div>
            <?php }else{?>
            	<div>更新成功</div>
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
						<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade" style="display: none;">
                                <div class="modal-dialog"  style="margin:200px auto">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                            <h4 id="myModalLabel" class="modal-title">标记到推送库</h4>
                                        </div>
                                        <div class="modal-body">确定加入推送库？
                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                                            <button class="btn btn-primary" type="button" id="addpush">添加推送</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal1" class="modal fade" style="display: none;">
                                <div class="modal-dialog"  style="margin:200px auto">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                            <h4 id="myModalLabel" class="modal-title">白名单</h4>
                                        </div>
                                        <div class="modal-body">白名单原因:
                                        <select class="input-sm" aria-controls="dataTables-example" id="kind" name="kind">
										  	<option value="1">公司内部人士</option>
										  	<option value="2">经纪人</option>
								        </select>
										 备注
										 <input type="search" name="remark" id="remark" value="">
                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                                            <button class="btn btn-primary" type="button" id="addwhite">确定</button>
                                        </div>
                                    </div>
                                </div>
                             </div>
							 <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal2" class="modal fade" style="display: none;">
                                <div class="modal-dialog"  style="margin:200px auto">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                            <h4 id="myModalLabel" class="modal-title">备选库</h4>
                                        </div>
                                        <div class="modal-body">确定加入备选库？

                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                                            <button class="btn btn-primary" type="button" id="addalternatives">确定</button>
                                        </div>
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
<script>
$(function(){
	$('#sel-all').click(function()
	{
        var aa = document.getElementsByName("rows_id");
        for (var i=0; i<aa.length; i++) {
           aa[i].checked = document.getElementById('sel-all').checked;;
        }
	});


   $("#addpush").click(function(){
   		var arr = new Array();
		$(".gradeA").find("input:checked[name=rows_id]").each(function(i) {
		partten = /^1[0-9]{10}$/;
		if($(this).val()!=0 && partten.test($(this).val())){
		arr[i]=$(this).val();
		}
		});
		var ids = arr.join(",");
        var city = 'nj';
	     $("#myModal").hide();
		 $(".modal-backdrop").remove();
         if(ids){
         $.ajax({
            type: 'post',
            url : '<?php echo MLS_ADMIN_URL;?>/user/addpushlist',
            data: 'ids='+ids+'&city='+city,
            dataType:'html',
            success: function(msg){
			     if(msg>0)
			     {
				   $("#warning_text").html("成功添加"+msg+"个进入推送库！")
				   openWin('js_note1');
				 }else{
			       $("#warning_text").html("添加失败或没有用户符合要求！")
				   openWin('js_note1');
                 }
                }
            });
         }else{
			       $("#warning_text").html("请先勾选用户！")
				   openWin('js_note1');
         }
   });

      $("#addwhite").click(function(){
   		var arr = new Array();
		$(".gradeA").find("input:checked[name=rows_id]").each(function(i) {
		partten = /^1[0-9]{10}$/;
		if($(this).val()!=0 && partten.test($(this).val())){
		arr[i]=$(this).val();
		}
		});
		var ids = arr.join(",");
		 $("#myModal1").hide();
		 $(".modal-backdrop").remove();
		 var remark = $("#remark").val();
		 var kind = $("#kind").val();
		 var city = 'nj';
         if(ids){
         $.ajax({
            type: 'post',
            url : '<?php echo MLS_ADMIN_URL;?>/user/addwhitelist',
            data: 'ids='+ids+'&city='+city+'&kind='+kind+'&remark='+remark,
            dataType:'html',
            success: function(msg){
			     if(msg>0)
			     {
				  $("#warning_text").html("成功添加"+msg+"个白名单！")
				  openWin('js_note1');
				 }else{
                  $("#warning_text").html("添加失败！")
				  openWin('js_note1');
                 }
                }
            });
         }else{
                  $("#warning_text").html("请先勾选用户！")
				  openWin('js_note1');
         }
   });

      $("#addalternatives").click(function(){
   		var arr = new Array();
		$(".gradeA").find("input:checked[name=rows_id]").each(function(i) {
		partten = /^1[0-9]{10}$/;
		if($(this).val()!=0 && partten.test($(this).val())){
		arr[i]=$(this).val();
		}
		});
		var ids = arr.join(",");
	     $("#myModal2").hide();
		 $(".modal-backdrop").remove();
         if(ids){
         $.ajax({
            type: 'post',
            url : '<?php echo MLS_ADMIN_URL;?>/user/addalternatives',
            data: 'ids='+ids,
            dataType:'html',
            success: function(msg){
			     if(msg>0)
			     {
				   $("#warning_text").html("成功添加"+msg+"个备选库！")
				   openWin('js_note1');
				 }else{
				   $("#warning_text").html("添加失败！")
				   openWin('js_note1');
                 }
                }
            });
         }else{
				   $("#warning_text").html("请先勾选用户！")
				   openWin('js_note1');
         }
   });


});
</script>
<?php require APPPATH.'views/footer.php'; ?>

