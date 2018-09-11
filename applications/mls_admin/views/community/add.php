<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">添加楼盘</h1>
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
                                                        楼盘名称<font color="red">*</font>:&nbsp&nbsp&nbsp<input type="search" name="cmt_name" id="cmt_name" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    <label>
                                                        拼音:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="name_spell" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘别名:&nbsp&nbsp&nbsp&nbsp<input type="text" name="alias" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    <label>
                                                        别名拼音:&nbsp&nbsp&nbsp<input type="text" name="alias_spell" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘类型<font color="red">*</font>:&nbsp;&nbsp;<input type="radio" value="1" name="type" checked>住宅&nbsp;&nbsp;
                                                    </label>
                                                    <label>
                                                        <input type="radio" value="2" name="type">别墅
                                                    </label>
                                                    <label>
                                                        <input type="radio" value="4" name="type">写字楼
                                                    </label>
                                                    <label>
                                                        <input type="radio" value="3" name="type">商铺
                                                    </label>
													<label>
                                                        <input type="radio" value="5" name="type">厂房
                                                    </label>
													<label>
                                                        <input type="radio" value="6" name="type">仓库
                                                    </label>
													<label>
                                                        <input type="radio" value="7" name="type">车库
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        区属<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <select id="district" name="dist_id" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                            <option value="">请选择</option>
                                                            <?php foreach ($district as $k => $v) { ?>
                                                                <option value="<?php echo $v['id'] ?>"><?php echo $v['district'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        板块<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <select id="street" name="streetid" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                            <option value="">请选择</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘地址<font color="red">*</font>:&nbsp&nbsp&nbsp<input type="search" name="address" class="form-control input-sm" aria-controls="dataTables-example" value="" size="62">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        建筑年代:&nbsp&nbsp&nbsp&nbsp
                                                        <select id="build_date" name="build_date" class="form-control input-sm" style="width:168px">
                                                            <option value="">请选择</option>
                                                            <?php for($i=1970;$i<2021;$i++){?>
                                                            <option value="<?php echo $i;?>"><?php echo $i;?>年</option>
                                                            <?php }?>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        建筑面积:&nbsp&nbsp&nbsp&nbsp<input type="search" name="buildarea" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    平方米
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        交付日期:&nbsp&nbsp&nbsp&nbsp<input type="search" name="deliver_date" onclick="WdatePicker()" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    <label>
                                                        均价:&nbsp&nbsp&nbsp&nbsp<input type="search" name="averprice" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    元/平方米
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        产权年限:&nbsp&nbsp&nbsp&nbsp
                                                        <select id="build_date" name="property_year" class="form-control input-sm" style="width:168px">
                                                            <option value="">请选择</option>
                                                            <option value="40">40年</option>
                                                            <option value="50">50年</option>
                                                            <option value="70">70年</option>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        占地面积:&nbsp&nbsp&nbsp&nbsp<input type="search" name="coverarea" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    平方米
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        物业公司:&nbsp&nbsp&nbsp&nbsp<input type="search" name="property_company" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    <label>
                                                        开发商:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="developers" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        停车位:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="parking" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    <label>
                                                        绿化率:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="green_rate" class="form-control input-sm" aria-controls="dataTables-example" value="">%
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        容积率:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="plot_ratio" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    <label>
                                                        物业费:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="property_fee" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    元/月·平米
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        总栋数:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="build_num" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    <label>
                                                        总户数:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="total_room" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼层状况:&nbsp&nbsp&nbsp&nbsp<input type="search" name="floor_instruction" class="form-control input-sm" aria-controls="dataTables-example" value="" size="62">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘简介:&nbsp&nbsp&nbsp&nbsp<input type="search" name="introduction" class="form-control input-sm" aria-controls="dataTables-example" value="" size="62">
                                                    </label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        物业业态:&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" value="住宅" name="build_type[]">住宅
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="别墅" name="build_type[]">别墅
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="写字楼" name="build_type[]">写字楼
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="商铺" name="build_type[]">商铺
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="厂房" name="build_type[]">厂房
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="仓库" name="build_type[]">仓库
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="车库" name="build_type[]">车库
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        周边配套:&nbsp&nbsp&nbsp&nbsp<input type="search" name="facilities" class="form-control input-sm" aria-controls="dataTables-example" value="" size="62">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        公交:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="bus_line" class="form-control input-sm" aria-controls="dataTables-example" value="" size="62">&nbsp;&nbsp;
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        地铁:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="subway" class="form-control input-sm" aria-controls="dataTables-example" value="" size="62">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        百度X:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="b_map_x" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    <label>
                                                        百度Y:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="b_map_y" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    <a href="#" onclick="window.open('<?php echo MLS_SOURCE_URL;?>/map/map_cp.php')">获取地图坐标</a>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        对应小学:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="primary_school" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                    <label>
                                                        对应中学:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="high_school" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘状态:&nbsp;&nbsp;<input type="radio" name="status" value="1"/> 临时小区
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="2" /> 正式小区
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="0" /> 待审核小区
                                                    </label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        显示上传图片按钮:&nbsp;&nbsp;<input type="radio" name="is_upload_pic" value="0"/> 否
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="is_upload_pic" value="1" /> 是
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘封面:
                                                        <div class="addBtn radius5">
                                                            <span id="spanButtonPlaceholder1"></span>
                                                        </div>
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
                                                                    upload_success_handler : uploadSuccess,
                                                                    upload_complete_handler : uploadComplete,

                                                                    button_image_url : "",
                                                                    button_placeholder_id : "spanButtonPlaceholder1",
                                                                    button_width: 88,
                                                                    button_height: 28,
                                                                    button_cursor: SWFUpload.CURSOR.HAND,
                                                                    button_text:"上传封面图",
                                                                    flash_url : "/swfupload.swf"
                                                                });

                                                                });
                                                            </script>
                                                            <div id="jsPicPreviewBoxM1" style="display:none" ></div>
                                                            <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails1"></div>
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
                                                    <input class="btn btn-primary" type="button" value="取消" onclick="window.history.go(-1);">
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
    $("#cmt_name").blur(function(){
        var self = $(this);
        var cmt_name = self.val();
        $.ajax({
            type: 'get',
            url : '<?php echo MLS_ADMIN_URL; ?>/community/check_cmt',
            data : {cmt_name : cmt_name},
            dataType:'json',
            success: function(msg){
                if(msg.result == 1){
                    alert('该楼盘名称已存在！请核实！');
                    self.val('');
                }
            }
        });
    });
});
</script>
<script src="<?php echo MLS_SOURCE_URL;?>/common/third/My97DatePicker/WdatePicker.js"></script>
<?php
    if(isset($js) && !empty($js)){
        echo $js;
    }
?>
<?php require APPPATH.'views/footer.php'; ?>

