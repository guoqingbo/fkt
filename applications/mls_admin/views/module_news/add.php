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
                    <h1 class="page-header"><?php echo $title?></h1>
                </div>
            </div>
            <?php if(''==$addResult){ ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                <input type='hidden' name='submit_flag' value='add'/>
                                                标题:&nbsp&nbsp&nbsp&nbsp<input type="search" name="title" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                内容:&nbsp&nbsp&nbsp&nbsp                                        <textarea name="content" id="content" cols="100" rows="4" style="visibility:hidden;"></textarea>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label>
                                        上传图片：
                                        </label>
                                        <label>
                                            <form name="fileform_photo" id="fileform_photo" action="/module_news/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                                                <input name="photofile" id="photofile" type="file" class="file_input mt10">
                                                <input type='hidden' name='action' value='photofile' />
                                                <input type='hidden' name='fileurl' id="fileurl" value='' />
                                            </form>
                                        </label>
                                    </div>
                                    <br>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <input class="btn btn-primary" type="button" value="发布" id="btn-save">
                                            <input class="btn btn-primary" type="button" value="取消" onclick="javascript:history.go(-1)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php }else if(0===$addResult){
                echo "<script>alert('发布失败')</script>";
                echo "<script>location.href='".MLS_ADMIN_URL."/collect_mass_notice/index'</script>";
         }else{
                echo "<script>alert('发布成功')</script>";
            echo "<script>location.href='".MLS_ADMIN_URL."/collect_mass_notice/index'</script>";
         }?>
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

    $(function(){
        $("#btn-save").click(function(){
            var title = $("input[name='title']").val();
            var content = $("[name='content']").val();
            var submit_flag = $("[name='submit_flag']").val();
            $.post("/module_news/add",{title:title,content:content,submit_flag:submit_flag},
                function(data){
                    if(data >= 1){
                        alert('发布成功！');
                        location.href = '/module_news/lists/';
                    } else if(data == -1){
                        alert('请输入标题和内容');
                    } else {
                        alert('发布失败');
                    }
                },"json");
        });
    });
</script>
<?php require APPPATH.'views/footer.php'; ?>
