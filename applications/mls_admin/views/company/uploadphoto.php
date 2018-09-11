<?php require APPPATH . 'views/header.php'; ?>

<link href="<?= MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css,css/v1.0/cal.css,css/v1.0/system_set.css" rel="stylesheet" type="text/css">
<link href="<?= MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/guest_disk.css,css/v1.0/house_manage.css,css/v1.0/personal_center.css,css/v1.0/house_new.css" rel="stylesheet" type="text/css">
<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>

<!--<script src="--><?php //echo MLS_SOURCE_URL; ?><!--/min/?f=common/third/swf/swfupload.js&debug=true"></script>-->
<!--<script src="--><?php //echo MLS_SOURCE_URL; ?><!--/min/?f=mls/js/v1.0/upload_logo.js&debug=true"></script>-->
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/swf/swfupload.js"></script>
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/js/v1.0/upload_logo.js"></script>

<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/cal.js"></script>
<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/shuifei.js"></script>
<script src="<?= MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/personal_center.js,mls/js/v1.0/house.js"></script>
<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js"></script>
<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js"></script>

<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>

<body>
<script type="text/javascript">
    //$(function() {
    //    $("#photofile").live("change",function(){
    //        var file = $(this).val();
    //        if(file != "")
    //        {
    //            var patrn=/(.jpg|.JPG|.bmp|.BMP)$/;
    //            if (patrn.exec(file))
    //            {
    //                $("#fileform_photo").submit();
    //            }
    //            else
    //            {
    //                $("#dialog_do_warnig_tip").html("图片格式不正确");
    //                openWin('js_pop_do_warning');
    //                return false;
    //            }
    //        }
    //    });
    //});
    function createSwf(w, h, text) {
        var swfu_head = new SWFUpload({
            // Backend Settings
            file_post_name: "file",
            upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
            //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
            //post_params: {"postion" : position},
            // File Upload Settings
            file_size_limit: "5 MB",
            file_types: "*.jpg;*.png",
            file_types_description: "JPG Images",
            file_upload_limit: 0,
            file_queue_limit: 1,

            custom_settings: {
                upload_target: "headpic_previewBoxM",
                upload_limit: 1,
                upload_nail: "headpic_img",
                upload_infotype: 1
            },

            // Event Handler Settings - these functions as defined in Handlers.js
            //  The handlers are not part of SWFUpload but are part of my website and control how
            //  my website reacts to the SWFUpload events.
            swfupload_loaded_handler: swfUploadLoaded,
            file_queue_error_handler: fileQueueError,
            file_dialog_start_handler: fileDialogStart,
            file_dialog_complete_handler: fileDialogComplete,
            upload_progress_handler: uploadProgress,
            upload_error_handler: uploadError,
            upload_success_handler: uploadSuccessNew,
            upload_complete_handler: uploadComplete,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,

            // Button Settings
            // button_image_url : "",
            button_placeholder_id: "photofile",
            button_width: w,
            button_height: h,
            button_text_top_padding: 6,
            button_text_left_padding: 2,
            button_cursor: SWFUpload.CURSOR.HAND,
            button_text: '<span class="btn-upload">' + text + '</span>',
            button_text_style: ".btn-upload { color: #ffffff; width: 65pt; font-size: 12pt; line-height: 34pt; text-align: center; vertical-align: middle;}",
            flash_url: "/swfupload.swf",
            debug: false
        });
    }
</script>

<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$title?></h1>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <!--<form name="add_form" method="post" action="">-->
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="set_basic_section clearfix">
                                        <div class="set_basic_section_line clearfix">
                                            <!--<span class="hr_framework_l_remind fl"><label>上传logo：</label></span>
                                            上传头像前-->
                                            <div class="my_info fl" style="margin-bottom: 0;width: 140px;">
                                                <div class="my_tx">
                                                    <div class="tx_normal">
                                                        <form name='fileform_photo' id='fileform_photo'
                                                              action='/company/upload_logo'
                                                              enctype='multipart/form-data' target='filepost_iframe'
                                                              method='post'>
                                                            <input type="hidden" name="fileurl" id="fileurl" value="">
                                                            <input type="hidden" name="company_id" id="company_id"
                                                                   value="<?= $company['id'] ?>">
                                                            <?php if($company['photo']) {?>
                                                            <img class="myself_photo_a" id="photo_replace" src="<?=$company['photo']?>" width="130" height="170"/>
                                                              <div class="show_editor_remove">
                                                                <span class="modify">
                                                                  <input name="photofile_modify" id="photofile"
                                                                         type="button" class="file_input">修改</span>
                                                                <span class="remove" style="border-right:none;"
                                                                      onclick="remove_photofile()">删除</span>
                                                                <script>createSwf(50, 34, "修改");</script>
                                                              </div>
                                                            <input type='hidden' name='action' value='photofile_modify' />
                                                            <input type='hidden' name='div_id' value='photo_replace' />
                                                            <?php }else{?>
                                                                <div class="zhanwei" width="130" height="170">
                                                                    <input name="photofile_add" id="photofile" type=""
                                                                           class="file_input">
                                                                    <script>createSwf(130.170, "");</script>
                                                            </div>
                                                            <input type='hidden' name='action' value='photofile_add' />
                                                            <?php }?>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>


                                            <div style="float:left">
                                                <p>提示</p>

                                                <p>1.图片小于2M</p>

                                                <p>2.支持JPG和BMPG格式图片</p>

                                                <p>3.尺寸大小150*150px</p>

                                                <p>4.即刻上传,无需点击保存</p>
                                            </div>
                                        </div>

                                    </div>
                                   <!-- <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <a class="btn btn-primary" href="/company/index">返回</a>
                                        </div>
                                    </div>	-->
                                </div>
                                <input type="hidden" name="submit_flag" value="modify">
                            <!--</form>-->
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
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
<!--提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="dialog_do_success_tip">操作成功！</p>
                <button type="button" class="btn-lv1 btn-mid" onclick="location.href='/company/'">确定</button>
            </div>
        </div>
    </div>
</div>
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="dialog_do_warnig_tip">操作失败！</p>
            </div>
        </div>
    </div>
</div>
<style>
    .show_editor_remove {
        display: block
    }
</style>
<script type="text/javascript">
$(function(){
    $(window).resize(function(e) {
        personHeight()
    });
    personHeight();


    function personHeight(){//窗口改变大小的时候  计算高度
        var _height = document.documentElement.clientHeight;
        $("#person_inner").height(_height - 40);
    }
});

//更改头像
function changePic(fileurl,div_id){
    if (fileurl == '') {
        delete_photofile()
    } else {
        changePhoto(fileurl);
    }
//  $("#"+div_id).attr("src",fileurl);
}
//添加头像调用
function changePhoto(fileurl){
    var photo_addHtml = "<input type='hidden' name='fileurl' id='fileurl' value='" + fileurl + "'>";
    photo_addHtml += "<input type='hidden' name='company_id' id='company_id' value='<?=$company['id']?>'>";
    photo_addHtml += "<img class='myself_photo_a' id='photo_replace' src='" + fileurl + "' width='130' height='170'/>";
    photo_addHtml += "<div class='show_editor_remove'>";
    photo_addHtml += "<span class='modify'><input name='photofile_modify' id='photofile' type='' class='file_input'>修改</span>";
    photo_addHtml += "<span class='remove' style='border-right:none;' onclick='remove_photofile()'>删除</span></div>";
    photo_addHtml += "<input type='hidden' name='action' value='photofile_modify' /><input type='hidden' name='div_id' value='photo_replace' />";
    $("#fileform_photo").html(photo_addHtml);
    createSwf(50, 34, "修改");
}
//删除头像
function remove_photofile()
{
    $('#fileurl').val('');
    $("#fileform_photo").submit();
//    var photo_modifyHtml = "<input type='hidden' name='fileurl' id='fileurl' value=''>";
//    photo_modifyHtml += "<input type='hidden' name='company_id' id='company_id' value='<?//=$company['id']?>//'>";
//    photo_modifyHtml += "<div class='zhanwei' width='130' height='170'>";
//    photo_modifyHtml += "<input type='' name='photofile_add' class='file_input' id='photofile'>";
//    photo_modifyHtml += "</div><input type='hidden' name='action' value='photofile_add' />";
//    $("#fileform_photo").html(photo_modifyHtml);
//    createSwf(130.170, "");
}

//去除头像
function delete_photofile() {
    var photo_modifyHtml = "<input type='hidden' name='fileurl' id='fileurl' value=''>";
    photo_modifyHtml += "<input type='hidden' name='company_id' id='company_id' value='<?=$company['id']?>'>";
    photo_modifyHtml += "<div class='zhanwei' width='130' height='170'>";
    photo_modifyHtml += "<input type='' name='photofile_add' class='file_input' id='photofile'>";
    photo_modifyHtml += "</div><input type='hidden' name='action' value='photofile_add' />";
    $("#fileform_photo").html(photo_modifyHtml);
    createSwf(130.170, "");
}
</script>
<?php require APPPATH . 'views/footer.php'; ?>
</body>
