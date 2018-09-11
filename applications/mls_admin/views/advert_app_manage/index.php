<?php require APPPATH.'views/header.php'; ?>
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/swf/swfupload.js"></script>
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/js/v1.0/uploadpic.js"></script>
<style>
 .tx_normal {
     background: url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/sfrz_bg.gif) no-repeat 0 0;
    width: 230px;
    height: 120px;
    margin: 0 auto;
    position: relative;
     border: 1px solid #d1d1d1;
     overflow: hidden;
}
 .file_input {
    position: absolute;
    right: -10px;
    top: -10px;
    font-size: 300px;
    filter: alpha(opacity=0);
    opacity: 0;
    cursor: pointer;

}
label{vertical-align:middle}
</style>
<script type="text/javascript">
    function createSwf(num) {
        var swfu_adver  = new SWFUpload({
            // Backend Settings
            file_post_name: "file",
            upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
            //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
            //post_params: {"postion" : position},
            // File Upload Settings,
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
//            upload_success_handler: function (file,serverData) {
//                alert(serverData);
//            },
            upload_complete_handler: uploadComplete,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,

            // Button Settings
            // button_image_url : "",
            button_placeholder_id: "photofile_add_"+num,
            button_width: 230,
            button_height: 120,
            button_text_top_padding: 0,
            button_text_left_padding: 0,
            button_cursor: SWFUpload.CURSOR.HAND,
            button_text: '',
            button_text_style: "",
            flash_url: "/swfupload.swf",
            debug: false
        });

        function uploadSuccess(file, serverData) {

          var resultData = JSON.parse(serverData);

          if(resultData.success === 1) {
            $("#fileurl_"+num).val(resultData.result.imgUrl);
            $("#fileform_photo_" + num).submit();
          }
        }

        //处理java上传图片接口返回的信息
        function uploadSuccessNew (file, serverData) {
            var resultData = JSON.parse(serverData);
            if (resultData.success == true) {
                $("#fileurl_"+num).val(resultData.result);
                $("#fileform_photo_" + num).submit();
            }
        }
    }

</script>
<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header" ><?php echo $title; ?></h1>
                    <a href="/advert_app_manage/news/" style="font-size:14px; font-weight: bold;">资讯中心设置</a>
                    <a href="/advert_app_manage/push/" style="font-size:14px; font-weight: bold; padding-left:30px;">推送中心设置</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                        <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                            <div class="row">
                                                <div class="col-sm-6" style="width:100%">
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php foreach($advert as $key => $value) {?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        广告类型<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <select  name="type" aria-controls="dataTables-example" class="form-control input-sm type" style="width:168px">
                                                            <?php foreach($type as $k => $v) {?>
                                                            <option value="<?php echo $k;?>" <?php if (isset($value['type']) && $value['type'] == $k) {echo 'selected';}?>><?php echo $v;?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </label>
                                                    <label style="display:<?php if ($value['type'] == 5) {echo 'inline;';} else {echo 'none;';}?>">
                                                        &nbsp&nbsp新盘名称<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <select name="newhouse_name" aria-controls="dataTables-example" class="form-control input-sm newhouse_name" style="width:168px">
                                                            <?php
                                                            $extra = unserialize($value['extra']);
                                                            foreach($project as $k => $v) {?>
                                                            <option value="<?php echo $v['newhouse_id'];?>" <?php if ($v['newhouse_id'] == $extra['newhouse_id']) {echo 'selected';}?>><?php echo $v['lp_name'];?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </label>
                                                    <label style="display:<?php if ($value['type'] == 6) {echo 'inline;';} else {echo 'none;';}?>">
                                                        <?php if ($value['type'] == 6) {
                                                            $extra = unserialize($value['extra']);
                                                            $url = $extra['url'];
                                                            $title = $extra['title'];
                                                        } ?>
                                                        &nbsp&nbspURL地址：<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <input type="text" name="url" value="<?php echo $url; ?>"
                                                               class="url" style="width:250px;">
                                                        &nbsp&nbspURL标题：<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <input type="text" name="title" value="<?php echo $title; ?>"
                                                               class="title" style="width:250px;">
                                                    </label>
                                                    <label style="margin-top:30px;">
                                                    <div class="tx_normal">
                                                        <form name='fileform_photo'
                                                              id='fileform_photo_<?php echo $key; ?>'
                                                              action='/advert_app_manage/upload_photo'
                                                              enctype='multipart/form-data' target='filepost_iframe'
                                                              method='post'>
                                                            <input type="hidden" name="fileurl_<?php echo $key?>" id="fileurl_<?php echo $key?>" value="">
                                                            <?php if (isset($value['pic']) && $value['pic'] != '') { ?>
                                                                <img class='myself_photo_a'
                                                                     src='<?php echo $value['pic']; ?>' width='255'
                                                                     height='132'/>
                                                            <div class='show_editor_remove'>
                                                             <span class='modify'>
                                                                 <input name='photofile_add' id="photofile_add_<?php echo $key; ?>" type=''
                                                                        class='file_input photofile'
                                                                        num="<?php echo $key; ?>">
                                                                 <input type='hidden' name='action'
                                                                        value='<?php echo $key; ?>'/></span>
<!--                                                                <script>createSwf(--><?php //echo $key; ?>//)</script>
                                                            </div>
                                                            <?php } else { ?>
                                                            <div class="zhanwei" width="255" height="132">
                                                                <input name="photofile_add" id="photofile_add_<?php echo $key; ?>" type=""
                                                                       class="file_input photofile"
                                                                       num= <?php echo $key; ?>>
                                                                <script>createSwf(<?php echo $key; ?>)</script>
                                                            </div>
                                                                <input type='hidden' name='action'
                                                                       value='<?php echo $key; ?>'/>
                                                            <?php } ?>
                                                        </form>
                                                    </div>
                                                    </label>
                                                    <label>
                                                        <a href='#' name='single_del' id="<?php echo $value['id'];?>">删除</a>&nbsp;&nbsp;
                                                     <input class="btn btn-primary" type="submit" value="提交">
                                                        <input class="ad_id" type="hidden"
                                                               value="<?php echo $key + 1; ?>">
                                                     </label>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
								</div>
                               </div>
                               </div>
                              </div>
                    </div>
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
<script src="<?php echo MLS_SOURCE_URL;?>/common/third/My97DatePicker/WdatePicker.js"></script>
<script>
    $(function() {
        var childnode;
        $('.type').change(function(){
             childnode = $(this).parent().siblings().find('.newhouse_name');
            if ($(this).val() == 5) {

                $(childnode).parent().css('display', 'inline');
            }
            else
            {
                $(childnode).parent().css('display', 'none');
            }
            childtxt = $(this).parent().siblings().find('.url');
            childTitle = $(this).parent().siblings().find('.title');
            if ($(this).val() == 6) {

                $(childtxt).parent().css('display', 'inline');
            }
            else
            {
                $(childtxt).parent().css('display', 'none');
            }
        });
//        $(".photofile").live("change",function(){
//            var num = $(this).attr('num');
//            var file = $(this).val();
//            if(file != "")
//            {
//                var patrn=/(.jpg|.JPG|.bmp|.BMP|.PNG|.png)$/;
//                if (patrn.exec(file))
//                {
//                    $("#fileform_photo_" + num).submit();
//                }
//                else
//                {
//                    $("#dialog_do_warnig_tip").html("图片格式不正确");
//                    openWin('js_pop_do_warning');
//                    return false;
//                }
//            }
//        });

        //提交保存广告信息
        $('.btn-primary').bind('click', function() {
            var parentObj = $(this).parent().siblings();
            var type = $(parentObj).find('.type').val();
            var newhouse = '';
            if (type == 5) {
                newhouse = $(parentObj).find('.newhouse_name').val();
            }
            var url = '';
            var title = '';
            if (type == 6) {
                url = $(parentObj).find('.url').val();
                title = $(parentObj).find('.title').val();
            }
            var pic = $(parentObj).find('.myself_photo_a').attr('src');
            var ad_id = $(this).parent().find('.ad_id').val();
            $.ajax({
                type : "POST",
                url  : "/advert_app_manage/save_ad",
                data : {'type' : type, 'newhouse' : newhouse, 'url' : url, 'title' : title,
                    'pic' : pic, 'ad_id' : ad_id},
                success: function(data) {
                    if (data == 1)
                    {
                        alert('操作成功');
                    }
                    else
                    {
                        alert('操作失败');
                    }
                }
            });
        });

        $('a[name="single_del"]').click(function(){//单独删除
                var id = $(this).attr('id');
                $.ajax({
                    type: 'get',
                    url : '<?php echo MLS_ADMIN_URL; ?>/advert_app_manage/del_photo/'+id,
                    success: function(msg){
                        if(msg=='success'){
                            alert('删除成功');
                        }else{
                            alert('删除失败');
                        }
                        location.reload();
                    }
                });
        });
    });
    //添加头像调用
    function changePhoto(fileurl, num){
        var photo_addHtml="<input type='hidden' name='fileurl_<?php echo $key?>'' id='fileurl_"+num+"' value=''>";
            photo_addHtml += "<img class='myself_photo_a' src='"+fileurl+"' width='255' height='132'/>";
            photo_addHtml += "<div class='show_editor_remove'>";
            photo_addHtml += "<span class='modify'><input name='photofile_add' id='photofile_add_"+num+"'  type='' class='file_input photofile' num=" + num
                    + "><input type='hidden' name='action' value='" + num+"'/></span>";
            $("#fileform_photo_" + num).html(photo_addHtml);
//            createSwf(num);

    }
</script>
<?php
if(isset($js) && !empty($js)){
    echo $js;
}
?>
<?php require APPPATH.'views/footer.php'; ?>

