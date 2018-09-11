<?php require APPPATH . 'views/header.php'; ?>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/swf/swfupload.js" type="text/javascript"></script>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/uploadpic.js" type="text/javascript"></script>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<style type="text/css">
    #r_s_popUP {position: absolute;top: 100px; left:100px;display: none}
    #r_s_popUP .replace_stores_popUp {position: relative;width: 410px; padding: 9px; border: 1px solid #6aa8e6; background: #fff; }
    .replace_stores_popUp .upgou { display: block; width: 7px;height: 5px; background: url(<?=MLS_SOURCE_URL ?>/mls_admin/images/xiangx.png) no-repeat; position: absolute; top: 230px;left: 45px; }
    .replace_stores_popUp li { padding: 10px 0;border-bottom: 1px dashed #dadada; zoom: 1;}
</style>
<div id="wrapper">
    <div id="page-wrapper">
		<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?php echo $title;?></h1>
                </div>
                <!-- /.col-lg-12 -->
        </div>
        <?php if ($addResult == '') { ?>
            <div class="row">
                <div class="col-lg-12">
					<form name="add_form" method="post" action="">
						<div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
										<font color="red">*</font>&nbsp&nbsp&nbsp商品类型:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?=($type == 1)?'兑换':'抽奖'?>
									</label>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
										<font color="red">*</font>&nbsp&nbsp&nbsp商品名称:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="search" class="form-control input-sm" aria-controls="dataTables-example" name="product_name" placeholder="请输入商品名称" value='<?=$paramArray['product_name']?>'/>
									</label>
								</div>
							</div>
							<?php if($type == 1){?>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
										<font color="red">*</font>&nbsp&nbsp&nbsp兑换所需积分:&nbsp&nbsp<input type="search" name="score" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$paramArray['score']?>" placeholder="请输入兑换所需积分数" id='need_score'/>
										<span id='text_waring'></span>
									</label>
								</div>
							</div>
							<?php } ?>
							<script>
								$(function(){
									$('#need_score').blur(function(){
										var score = $(this).val();
										var reg = /^[1-9]\d*$/;
										if(!reg.test(score)){
											$('#text_waring').html('<font color="red">积分值只能是正整数</font>');
										}
									});
									$('#need_score').focus(function(){
										$('#text_waring').html('');
									})
								});
							</script>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
								<label>
                                        <font color="red">*</font>&nbsp&nbsp&nbsp商品图片:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span class="addBtn radius5">
                                            <span id="spanButtonPlaceholder2"></span>
                                        </span>
                                        <script type="text/javascript">
                                            var swfu2;
                                            $(function() {
                                            swfu2 = new SWFUpload({
                                                file_post_name: "file",
                                                upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                                                file_size_limit : "5 MB",
                                                file_types : "*.jpg;*.png",
                                                file_types_description : "JPG Images",
                                                file_upload_limit : "0",
                                                file_queue_limit : "5",
                                                custom_settings : {
                                                    upload_target : "jsPicPreviewBoxM2",
                                                    upload_limit  : 1,
                                                    upload_nail	  : "thumbnails2",
                                                    upload_infotype : 2
                                                },
                                                swfupload_loaded_handler : swfUploadLoaded,
                                                file_queue_error_handler : fileQueueError,
                                                file_dialog_start_handler : fileDialogStart,
                                                file_dialog_complete_handler : fileDialogComplete,
                                                upload_progress_handler : uploadProgress,
                                                upload_error_handler : uploadError,
                                                upload_success_handler : uploadSuccessNew,
                                                upload_complete_handler : uploadComplete,

                                                button_image_url : "",
                                                button_placeholder_id : "spanButtonPlaceholder2",
                                                button_width: 88,
                                                button_height: 28,
                                                button_cursor: SWFUpload.CURSOR.HAND,
                                                button_text:"上传商品图",
                                                flash_url : "/swfupload.swf"
                                            });

                                            });
                                        </script>
                                        <div id="jsPicPreviewBoxM2" style="display:none" ></div>
                                        <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails2"></div>
                                    </label>
								</div>
							</div>
							<?php if($type == 1){?>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp&nbsp&nbsp商品详情:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="product_detail" class="form-control input-sm" aria-controls="dataTables-example" rows='5' cols='120'><?=$paramArray['product_detail']?></textarea>
									</label>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   &nbsp;&nbsp;&nbsp;&nbsp;注意事项:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="attention_matter" class="form-control input-sm" aria-controls="dataTables-example" rows='5' cols='120'><?=$paramArray['attention_matter']?></textarea>
									</label>
								</div>
							</div>
							<?php } ?>
							<?php if($type == 2){?>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp;&nbsp;商品中奖率:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="rate" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$paramArray['rate']?>"/>
									</label>
									<span style="color:red">注：请填入小于1的数字，1代表中奖率100%，0.1代表中奖率10%</span>
								</div>
							</div>
							<?php } ?>
							<?php if($type == 1){?>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   &nbsp;&nbsp;&nbsp;&nbsp;商品排序:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="order" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$paramArray['order']?>"/>
									</label>
									<span style="color:red">数字越小越靠前，若与当前已有的数字重复，最新修改的将靠前，不填写将按时间倒序排列</span>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp;&nbsp;&nbsp;&nbsp;商品库存:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="stock" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$paramArray['stock']?>"/>
									</label>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp;&nbsp;&nbsp;&nbsp;下架时间:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="down_time" class="form-control input-sm" aria-controls="dataTables-example" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?=$paramArray['down_time']?>"/>
									</label>
								</div>
							</div>
							<?php }else{?>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp;&nbsp;&nbsp;&nbsp;商品库存:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="stock" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$paramArray['stock']?>"/>
									</label>
									<span style="color:red">序号为3和8时，无需填写，默认为1件</span>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red"></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;中奖限额:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="raffle_num" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$paramArray['raffle_num']?>" onkeyup="value=this.value.replace(/\D+/g,'')"/>
									</label>
									<span style="color:red">此项为每月中奖个数上限，不填默认不受限</span>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp;&nbsp;&nbsp;&nbsp;商品排序:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="order" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$paramArray['order']?>" onkeyup="value=this.value.replace(/\D+/g,'')"/>
									</label>
									<span style="color:red">请按照大转盘奖品初始0点位置顺时针从1开始填写，最大10。(3和8为默认积分中奖，请误填错)</span>
								</div>
							</div>
							<?php } ?>
							<!--end-->
						   <?php if (!empty($mess_error)) { ?>
								<div class="col-sm-6" style="width:100%">
									<div class="dataTables_length" id="dataTables-example_length">
										<font color='red'><?php echo $mess_error; ?></font>
									</div>
								</div>
							<?php } ?>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<input class="btn btn-primary" type="submit" value="保存">
								   <a class="btn btn-primary" href="/gift_manage/index">返回列表</a>
								</div>
							</div>
						</div>
						<input type="hidden" name="submit_flag" value="add">
					</form>
                </div>
                <!-- /.panel-body -->
            </div>
        <?php } else if (0 === $addResult) { ?>
            <div><h1><b>添加失败<?=$agency_mess_error;?></b></h1></div>
        <?php } elseif($addResult === 'no') { ?>
            <div><h1><b>公司权限初始化失败，请在列表页找到自己的公司，点击“重新初始化”</b></h1></div>
        <?php } else {?>
            <div><h1><b>商品添加成功<?=$agency_mess_error;?></b></h1></div>
        <?php }?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<script>
$(function(){

});
</script>
<?php require APPPATH . 'views/footer.php'; ?>

