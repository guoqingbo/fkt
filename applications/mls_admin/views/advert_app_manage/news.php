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

    //推送的单击事件
    $("input:radio[name=is_push]").click(function () {
        if ($(this).val() == 1)
        {
            $('#push_new').css('display', 'inline-block');
        }
        else
        {
             $('#push_new').css('display', 'none');
        }
    });
});

function changePic(fileurl){
    var word = $('#bewrite').val();
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
                <h1 class="page-header"><a href="/advert_app_manage/"><?=$title?></a></h1>
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
                                        <input type='hidden' name='submit_flag' value='add'/>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    标题：<font color="red">*</font>:&nbsp&nbsp&nbsp
                                                    <input type="search" name="title" id="title" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$news['title']?>">
                                                    <font style="color: red;font-size: 14px;">
                                                    <strong>
                                                    <?php
                                                    if ($news['new_content'] == $news['content'] && $news['content'] != '')
                                                    {
                                                        echo '已提交';
                                                    }
                                                    else
                                                    {
                                                        echo '未提交';
                                                    }
                                                    ?>
                                                    </strong>
                                                    </font>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <textarea name="bewrite" id="bewrite" cols="0" rows="0" style="margin-top:5px; width:835px; height:155px; visibility:hidden;"><?=$news['new_content']?></textarea>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                 <label>
                                                    &nbsp;&nbsp;上传图片：
                                                 </label>
                                                <label>
                                                  <form name="fileform_photo" id="fileform_photo" action="/advert_app_manage/upload_new_photo/" enctype="multipart/form-data" target="filepost_iframe" method="post">
                                                        <input name="photofile" id="photofile" type="file" class="file_input mt10">
                                                        <input type='hidden' name='action' value='photofile' />
                                                        <input type='hidden' name='fileurl' id="fileurl" value='' />
                                                    </form>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    是否同时发布APP推送:
                                                </label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="is_push" value="0" <?php if ($news['is_push'] == 0) {echo 'checked="checked"';}?>>否</label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="is_push" value="1" <?php if ($news['is_push'] == 1) {echo 'checked="checked"';}?>>是</label>
                                                <span id = "push_new" style="<?php echo $news['is_push'] == 1 ? 'display: inline-block;' : 'display:none;';?>">
                                                <input type="text" name="push_name" id = "push_name" value="<?=$news['push_name']?>" style="width:300px;"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="button" value="提交" id="submitBtn">&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="/advert_app_manage/preview/" target="_blank">
                                                <input class="btn btn-primary" type="button"  value="预览"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input class="btn btn-primary" type="button"  value="保存" id="saveBtn">&nbsp;&nbsp;
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
            <script charset='utf-8'  src='<?=MLS_SOURCE_URL ?>/common/js/kindeditor-4.1.10/kindeditor-min.js'></script>
            <script charset='utf-8'  src='<?=MLS_SOURCE_URL ?>/common/js/kindeditor-4.1.10/lang/zh_CN.js'></script>
            <script>
                 //页面编辑器
                var editor;
                KindEditor.ready(function(K) {
                    editor = K.create('#bewrite', {
                        width: '820px',
                        height: '350px',
                        resizeType: 0,
                        allowPreviewEmoticons: false,
                        allowImageUpload: false,
                        items: ['fontname', 'fontsize', '|', 'forecolor',
                            'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|',
                            'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                            'insertunorderedlist', '|', 'wordpaste'],//, '|', 'image' 出售、出租房源模版均去除图片功能。 bug #5741
                        afterBlur: function() {
                            this.sync();
                        }
                    });
                });

            //保存广告信息
            $('#saveBtn').bind('click', function() {
               save_news(1);
            });

            //提交广告信息
            $('#submitBtn').bind('click', function() {
                save_news(2)
            });

            function save_news(type)
            {
                var title = $('#title').val();
                var content = $('#bewrite').val();
                var push_name = $('#push_name').val();
                if (title == '')
                {
                    alert('标题不能为空');
                    $('#title').focus();
                    return false;
                }
                if (content == '')
                {
                    alert('内容不能为空');
                    $('#content').focus();
                    return false;
                }
                if ($("input[name='is_push']:checked").val() == 1 && push_name == '')
                {
                    alert('推送内容不能为空');
                    $('#push_name').focus();
                    return false;
                }
                $.ajax({
                    type : "POST",
                    url  : "/advert_app_manage/save_news",
                    data : {'type' : type, 'content' : $('#bewrite').val(),
                            'is_push' : $("input[name='is_push']:checked").val(),
                            'title' : $('#title').val(), 'push_name' : push_name},
                    success: function(data) {
                        if (data == 1)
                        {
                            alert('操作成功');
                            window.location.reload();
                        }
                        else
                        {
                            alert('操作失败');
                        }
                    }
                });
            }
      </script>
<?php require APPPATH.'views/footer.php'; ?>

