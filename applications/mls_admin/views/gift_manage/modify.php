<?php require APPPATH . 'views/header.php'; ?>
<!--<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/swf/swfupload.js" type="text/javascript"></script>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/uploadpic.js" type="text/javascript"></script>-->
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script type="text/javascript">


$(function() {

  var regExp_pic = /(.jpg|.JPG|.png|.PNG)$/;

	$("#photofile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=regExp_pic;

			if (patrn.exec(file))
			{
				$("#fileform_photo").submit();
			}
			else
			{
				alert("图片格式不正确");
				return false;
			}
		}
	});


});


function changePic(fileurl,div_id){
    $("#"+div_id).attr("src",fileurl);
}

function submit(submit_flag,id,type){
	var product_name = $("input[name='product_name']").val();
	var score = $("input[name='score']").val();
    var product_detail = $("#product_detail").val();
    var attention_matter = $("#attention_matter").val();
    var order = $("input[name='order']").val();
	var stock = $("input[name='stock']").val();
	var down_time = $("input[name='down_time']").val();
	var photopic = $("#photopic_replace").attr("src");
	var rate = $("input[name='rate']").val();
	var raffle_num = $("input[name='raffle_num']").val();
    if(photopic.substring(photopic.lastIndexOf("/")+1,photopic.lastIndexOf(".")) == "sfrz_bg"){
    	photopic = "";
    }
	var data = {submit_flag:submit_flag,product_name:product_name,score:score,photopic:photopic,product_detail:product_detail,attention_matter:attention_matter,order:order,down_time:down_time,stock:stock,rate:rate,raffle_num:raffle_num};
	$.ajax({
		type: "POST",
		url: "/gift_manage/edit/"+id+'/'+type,
		data:data,
		dataType: "json",
		cache:false,
		error:function(){
			alert("系统错误");
			return false;
		},
		success: function(data){
			if(data['result'] != 1){
				alert(data['msg']);
				location.reload();
				return false;
			}else{
				alert(data['msg']);
				window.location.href = '/gift_manage/index';
			}
		}
	});

}
</script>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:block;"></iframe>
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
        <?php if ($modifyResult == '') { ?>
            <div class="row">
                <div class="col-lg-12">
					<!--<form name="search_form" method="post" action="">-->
					<input type='hidden' name='submit_flag' value='modify'/>
						<div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
										<font color="red">*</font>&nbsp&nbsp&nbsp商品类型:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<?=($list['type'] == 1)?'兑奖':'抽奖'?>
									</label>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									<input type='hidden' name='submit_flag' value='edit'/>
										<font color="red">*</font>&nbsp&nbsp&nbsp商品名称:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="search" class="form-control input-sm" aria-controls="dataTables-example" name="product_name" placeholder="请输入商品名称" value="<?php echo $list['product_name'];?>"/>
									</label>
								</div>
							</div>
							<?php if($type == 1){?>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
										<font color="red">*</font>&nbsp&nbsp&nbsp兑换所需积分:&nbsp&nbsp<input type="search" name="score" class="form-control input-sm" aria-controls="dataTables-example"  placeholder="请输入兑换所需积分数" value="<?php echo $list['score'];?>" id='need_score'/>
										<span id='text_waring'></span>
									</label>
								</div>
							</div>
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
							<?php } ?>
							<!--<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
										<font color="red">*</font>&nbsp&nbsp&nbsp商品图片:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<span class="addBtn radius5">
											<span id="spanButtonPlaceholder1"></span>
										</span>
										<script type="text/javascript">
											var swfu1;
											$(function() {
												swfu1 = new SWFUpload({
													upload_url: "<?php echo MLS_FILE_SERVER_URL; ?>/uploadimg/index",
													file_size_limit : "5 MB",
													file_types : "*.jpg;*.png",
													file_types_description : "JPG Images",
													file_upload_limit : "0",
													file_queue_limit : "5",

													custom_settings : {
														upload_target : "jsPicPreviewBoxM1",
														upload_limit  : 1,
														upload_nail	  : "thumbnails1",
														upload_infotype : 1
													},
													swfupload_loaded_handler : swfUploadLoaded,
													file_queue_error_handler : fileQueueError,
													file_dialog_start_handler : fileDialogStart,
													file_dialog_complete_handler : fileDialogComplete,
													upload_progress_handler : uploadProgress,
													upload_error_handler : uploadError,
													upload_success_handler : uploadSuccess,
													upload_complete_handler : uploadComplete,

													button_image_url : "",
													button_placeholder_id : "spanButtonPlaceholder1",
													button_width: 88,
													button_height: 28,
													button_cursor: SWFUpload.CURSOR.HAND,
													button_text:"上传商品图",
													flash_url : "/swfupload.swf"
												});

											});
											</script>
											<div id="jsPicPreviewBoxM1" style="display:none" ></div>
                                            <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails1">
                                                <img src="<?php echo $list['product_picture'];?>"></div>
									</label>
								</div>
							</div>-->
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label><font color="red">*</font>商品图片:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
									<label>
									<?php
									if($list['product_picture']){
										$photo = $list['product_picture'];
										$photo_big = changepic($list['product_picture']);
									}else{
                                        $photo = MLS_SOURCE_URL . '/mls/images/v1.0/grzx/sfrz_bg.gif';
									}
									?>
									<a href="<?=$photo_big ?>" target="_blank"><img id="photopic_replace" src="<?=$photo ?>" width="242" height="170"/></a>
									</label>

									<label>
										<form name="fileform_photo" id="fileform_photo" action="/gift_manage/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
											<input name="photofile" id="photofile" type="file" class="file_input mt10">
											<input type='hidden' name='action' value='photofile' />
											<input type='hidden' name='div_id' value='photopic_replace' />
										</form>
									</label>
								</div>
							</div>
							<?php if($type == 1){?>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp&nbsp&nbsp商品详情:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="product_detail" id="product_detail" class="form-control input-sm" aria-controls="dataTables-example" value="" rows='5' cols='120'><?php echo $list['product_detail'];?></textarea>
									</label>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   &nbsp;&nbsp;&nbsp;&nbsp;注意事项:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="attention_matter" class="form-control input-sm" id="attention_matter" aria-controls="dataTables-example" value="" rows='5' cols='120'><?php echo $list['attention_matter'];?></textarea>
									</label>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   &nbsp;&nbsp;&nbsp;&nbsp;商品排序:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="order" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $list['order'];?>"/>
									</label>
									<span style="color:red">数字越小越靠前，若与当前已有的数字重复，最新修改的将靠前，不填写将按时间倒序排列</span>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp;&nbsp;&nbsp;&nbsp;商品库存:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="stock" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $list['stock']?>"/>
									</label>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp;&nbsp;&nbsp;&nbsp;下架时间:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="down_time" class="form-control input-sm" aria-controls="dataTables-example" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo date('Y-m-d H:i:s',$list['down_time'])?>"/>
									</label>
								</div>
							</div>
							<?php }else{?>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp;&nbsp;商品中奖率:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="rate" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$list['rate']?>"/>
									</label>
									<span style="color:red">注：请填入小于1的数字，1代表中奖率100%，0.1代表中奖率10%</span>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp;&nbsp;&nbsp;&nbsp;商品库存:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="stock" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $list['stock']?>"/>
									</label>
									<span style="color:red">序号为3和8时，无需填写，默认为1件</span>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red"></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;中奖限额:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="raffle_num" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $list['raffle_num']?>" onkeyup="value=this.value.replace(/\D+/g,'')"/>
									</label>
									<span style="color:red">此项为每月中奖个数上限，不填默认不受限</span>
								</div>
							</div>
							<div class="col-sm-6" style="width:100%">
								<div class="dataTables_length" id="dataTables-example_length">
									<label>
									   <font color="red">*</font>&nbsp;&nbsp;&nbsp;&nbsp;商品排序:&nbsp&nbsp&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="order" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $list['order'];?>" onkeyup="value=this.value.replace(/\D+/g,'')" />
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
									<a class="btn btn-primary" href="#" onclick="submit('edit',<?=$list['id']?>,<?=$list['type']?>)">保存</a>
								   <a class="btn btn-primary" href="/gift_manage/index">返回列表</a>
								</div>
							</div>
						</div>
						<input type="hidden" name="submit_flag" value="add">
					<!--</form>-->
                </div>
                <!-- /.panel-body -->
            </div>
        <?php } else if (0 === $modifyResult) { ?>
            <div><h1><b>修改失败<?=$agency_mess_error;?></b></h1></div>
        <?php } elseif($modifyResult === 'no') { ?>
            <div><h1><b>公司权限初始化失败，请在列表页找到自己的公司，点击“重新初始化”</b></h1></div>
        <?php } else {?>
            <div><h1><b>商品修改成功<?=$agency_mess_error;?></b></h1></div>
        <?php }?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
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

