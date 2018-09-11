<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">添加楼盘图片</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(''==$addResult){; ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="add_form" method="post" enctype="multipart/form-data" action="">
                                        <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                            <div class="row">
                                                <div class="col-sm-6" style="width:100%">
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                    </div>
                                                </div>
                                            </div>
                                            <input type='hidden' name='submit_flag' value='add'/>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        所属楼盘:&nbsp&nbsp&nbsp<?php echo $cmt_name;?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        图片类型<font color="red">*</font>:
                                                        <select id="district" name="pic_type" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                            <option value="">请选择</option>
                                                            <?php foreach ($pic_type_arr as $k => $v) { ?>
                                                                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length add_pic_house_box clearfix" id="dataTables-example_length">
                                                <label>
                                                    图片地址<font color="red">*</font>（支持多图上传）:
                                                </label>
                                                <div>
                                                <div class="add_item">
                                                    <span id="spanButtonPlaceholder1"></span>
                                                </div>
                                                <script type="text/javascript">
                                                    var swfu1;
                                                    $(function() {
                                                    swfu1 = new SWFUpload({
                                                        file_post_name: "file",
                                                        upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                                                        file_size_limit : "5 MB",
                                                        file_types : "*.jpg;*.png",
                                                        file_types_description : "JPG Images",
                                                        file_upload_limit : "0",
                                                        file_queue_limit : "5",
                                                        custom_settings : {
                                                            upload_target : "jsPicPreviewBoxM1",
                                                            upload_limit  : 3,
                                                            upload_nail	  : "thumbnails1",
                                                            upload_infotype : 1
                                                        },
                                                        swfupload_loaded_handler : swfUploadLoaded,
                                                        file_queue_error_handler : fileQueueError,
                                                        file_dialog_start_handler : fileDialogStart,
                                                        file_dialog_complete_handler : fileDialogComplete,
                                                        upload_progress_handler : uploadProgress,
                                                        upload_error_handler : uploadError,
                                                        upload_success_handler : uploadSuccessNew,
                                                        upload_complete_handler : uploadComplete,


                                                        // Button Settings
                                                        button_image_url: "<?=MLS_SOURCE_URL?>/mls/images/v1.0/flash_btn02.png",
                                                        button_placeholder_id: "spanButtonPlaceholder1",
                                                        button_width: 130,
                                                        button_height: 100,
                                                        button_cursor: SWFUpload.CURSOR.HAND,
                                                        button_text: "",
                                                        flash_url : "/swfupload.swf"
                                                    });

                                                    });
                                                    </script>
                                                    <div id="jsPicPreviewBoxM1" style="display:none" ></div>
                                                    <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails1"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    房型:&nbsp&nbsp&nbsp<input type="search" name="room" class="form-control input-sm" aria-controls="dataTables-example" value=""> 室
                                                </label>
                                                <label>
                                                    &nbsp&nbsp&nbsp<input type="search" name="hall" class="form-control input-sm" aria-controls="dataTables-example" value=""> 厅
                                                    &nbsp（如果图片类型为户型图，必须填写房型）
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
                                    </form>
								</div>
                               </div>
                               </div>
                              </div>
                        <!-- /.panel-body -->

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
    $('#district').change(function(){
        var districtID = $(this).val();
        $.ajax({
            type: 'get',
            url : '<?php echo MLS_ADMIN_URL; ?>/community/find_street_bydis/'+districtID,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg.result=='no result'){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
                    }
                }
                $('#street').empty();
                $('#street').append(str);
            }
        });
    });
});
</script>
<script src="<?php echo MLS_SOURCE_URL;?>/common/third/My97DatePicker/WdatePicker.js"></script>
<?php
if ( isset($js) && $js != '')
{
    echo $js;
}

if ( isset($css) && $css != '')
{
    echo $css;
}
?>
<?php require APPPATH.'views/footer.php'; ?>

