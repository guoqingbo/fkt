<?php require APPPATH.'views/header.php'; ?>
<script type="text/javascript">
$(function() {
    $("#photofile").live("change",function(){
        var file = $(this).val();
        if(file != "")
        {
            var patrn=/(.jpg|.JPG|.bmp|.BMP|.png|.PNG)$/;
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

function changePic(fileurl){
    var word = $('#content').val();
    newword = word+'<img src="'+fileurl+'" height="300" width="500" />';
    editor.html(newword);
    editor.sync();
}

</script>

<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?= $title ?></h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input type="hidden" name="notice_id" value="<?php echo $motice_details['id']; ?>"/>
                                                <input type="hidden" name="notice_upload_file_1" id="notice_upload_file_1" value="<?php echo $file_old_1; ?>" />
                                                <input type="hidden" name="notice_upload_file_2" id="notice_upload_file_2" value="<?php echo $file_old_2; ?>" />
                                                <div>
                                                    <b>标题：</b><input type="text" name="title" value="<?php echo $motice_details['title']; ?>" style="width:500px;">
                                                </div>
                                                <br>
                                                <div>
                                                    <b>作者：</b><input type="text" name="author_name" value="<?php echo $motice_details['author_name']; ?>" >
                                                </div>
                                                <br>
                                                <div>
                                                    <b>内&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;容：</b>
                                                </div>
                                                <div>
                                                    <textarea name="content" id="content" cols="100" rows="4" style="visibility:hidden;"><?php echo $motice_details['content']; ?></textarea>
                                                </div>
                                                <div>
                                                    <label>
                                                    上传图片：
                                                    </label>
                                                    <label>
                                                        <form name="fileform_photo" id="fileform_photo" action="/help_center/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                                                            <input name="photofile" id="photofile" type="file" class="file_input mt10">
                                                            <input type='hidden' name='action' value='photofile' />
                                                            <input type='hidden' name='fileurl' id="fileurl" value='' />
                                                        </form>
                                                    </label>
                                                </div>
                                                <br>
                                                <?php if(is_full_array($file_list)){ ?>
                                                <div style="font-size: 15px;">
                                                    <form name="fileform_fujian" id="fileform_fujian" action="/features_notice/upload_file" enctype="multipart/form-data" target="filepost_iframe" method="post">
                                                    <input type="hidden" value="" name="file_type" id="file_type"/>
                                                <?php
                                                    foreach($file_list as $key => $value){
                                                ?>
                                                    <b>附件<?php echo $key+1; ?>：</b>
                                                    <a class="a a2" href="javascript:void(0);">重新上传</a>&nbsp;&nbsp;
                                                    <input name="fujian_file_<?php echo $key+1; ?>" id="fujian_file_<?php echo $key+1; ?>" type="file" class="file_input mt10">
                                                <?php } ?>
                                                    </form>
                                                </div>
                                                <br>
                                                <?php }else{ ?>
                                                <div style="font-size: 15px;">
                                                    <form name="fileform_fujian" id="fileform_fujian" action="/features_notice/upload_file" enctype="multipart/form-data" target="filepost_iframe" method="post">
                                                    <input type="hidden" value="" name="file_type" id="file_type"/>
                                                    <label>
                                                    附件1：
                                                    </label>
                                                    <label>
                                                            <input name="fujian_file_1" id="fujian_file_1" type="file" class="file_input mt10">
                                                            <input type='hidden' name='action' value='uploadfile' />
                                                    </label>
                                                    <label>
                                                    附件2：
                                                    </label>
                                                    <label>
                                                            <input name="fujian_file_2" id="fujian_file_2" type="file" class="file_input mt10">
                                                    </label>
                                                    </form>
                                                </div>
                                                <?php } ?>
                                                <div>
                                                    <b>关键词：</b><input type="text" name="key_word" value="<?php echo $motice_details['key_word']; ?>" >
                                                </div>
                                                <br>
                                                <div>
                                                    <input type="hidden" class="form-control" name="parent_id" value="<?php echo $parent_id;?>">
                                                    <input class="btn btn-default" id="btn-save" type="button" value="保存并发布">
                                                    <input class="btn btn-default" id="btn-save2" type="button" value="仅保存草稿">
                                                    <input class="btn btn-default" id="btn-return" type="button" value="返回">
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
            <script charset='utf-8'  src='<?= MLS_SOURCE_URL ?>/common/js/kindeditor-4.1.10/kindeditor-min.js'></script>
            <script charset='utf-8'  src='<?= MLS_SOURCE_URL ?>/common/js/kindeditor-4.1.10/lang/zh_CN.js'></script>
            <script>
                //页面编辑器
                var editor;
                KindEditor.ready(function(K) {
                    editor = K.create('#content', {
                        width: '820px',
                        height: '350px',
                        resizeType: 0,
                        allowPreviewEmoticons: false,
                        allowImageUpload: false,
                        items: ['fontname', 'fontsize', '|', 'forecolor',
                            'hilitecolor', 'bold', 'underline', 'removeformat', '|',
                            'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                            'insertunorderedlist', '|', 'wordpaste', '|', 'image'],
                        afterBlur: function() {
                            this.sync();
                        }
                    });
                });
            </script>
        </div>
    </div>

    <script>
        function update(type = 0){
            var notice_id = $("input[name='notice_id']").val();
            var title = $("input[name='title']").val();
            var content = $("[name='content']").val();
            var author_name = $("input[name='author_name']").val();
            var key_word = $("input[name='key_word']").val();
            var notice_upload_file_1 = $("input[name='notice_upload_file_1']").val();
            var notice_upload_file_2 = $("input[name='notice_upload_file_2']").val();
            var status = 0;
            if(1==type){
                status = 1;
            }else{
                status = 2;
            }
            $.post("<?php echo MLS_ADMIN_URL;?>features_notice/update_notice",{notice_id:notice_id,title:title,content:content,author_name:author_name,key_word:key_word,status:status,notice_upload_file_1:notice_upload_file_1,notice_upload_file_2:notice_upload_file_2},
                function(data){
                    if(data.status == 1){
                        alert('修改成功！');
                        location.href = "<?php echo MLS_ADMIN_URL;?>/features_notice/index/";
                    } else if(data.status == 2){
                        alert('请输入标题！');
                    } else {
                        alert('修改失败');
                        location.href = "<?php echo MLS_ADMIN_URL;?>/features_notice/index/";
                    }

                },"json");
        }

        function set_upload_file_1(file_name){
            $('#notice_upload_file_1').val(file_name);
        }
        function set_upload_file_2(file_name){
            $('#notice_upload_file_2').val(file_name);
        }

        $(function(){
            $("#btn-save").click(function(){
                update(1);
            });
            $("#btn-save2").click(function(){
                update(2);
            });
            $("#btn-return").click(function(){
                location.href = "<?php echo MLS_ADMIN_URL;?>/features_notice/index/";
            });

            $("#fujian_file_1").live("change",function(){
                $('#file_type').val(1);
                var file = $(this).val();
                if(file != "")
                {
                    var patrn=/(.doc|.docx|.xls|.xlsx)$/;
                    if (patrn.exec(file))
                    {
                        $("#fileform_fujian").submit();
                    }
                    else
                    {
                        alert("文件格式不正确");
                        return false;
                    }
                }
            });
            $("#fujian_file_2").live("change",function(){
                $('#file_type').val(2);
                var file = $(this).val();
                if(file != "")
                {
                    var patrn=/(.doc|.docx|.xls|.xlsx)$/;
                    if (patrn.exec(file))
                    {
                        $("#fileform_fujian").submit();
                    }
                    else
                    {
                        alert("文件格式不正确");
                        return false;
                    }
                }
            });
        })
    </script>
<?php require APPPATH.'views/footer.php'; ?>
