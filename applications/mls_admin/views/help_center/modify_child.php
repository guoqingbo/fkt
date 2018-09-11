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
                                                    <div class="form-group">
                                                        <b>子项标题：</b><input type="text" name="title" value="<?php echo $child_info['title']; ?>" >
                                                    </div>
                                                    <div>
                                                        <b>内&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;容：</b>
                                                    </div>
                                                    </div>
                                                        <textarea name="content" id="content" cols="100" rows="4" style="visibility:hidden;"><?php echo $child_info['content']; ?></textarea>
                                                    </div>
                                                    <div>
                                                        <label>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;上传图片：
                                                        </label>
                                                        <label>
                                                            <form name="fileform_photo" id="fileform_photo" action="/help_center/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                                                                <input name="photofile" id="photofile" type="file" class="file_input mt10">
                                                                <input type='hidden' name='action' value='photofile' />
                                                                <input type='hidden' name='fileurl' id="fileurl" value='' />
                                                            </form>
                                                        </label>
                                                    </div>
                                                    <div>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;<b>序&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号：</b><input type="text" name="orderby" value="<?php echo $child_info['orderby']; ?>">&nbsp;&nbsp;<b>数值越大越靠前，若不填将默认为0</b>
                                                    </div>
                                                    <div>
                                                        <input type="hidden" class="form-control" name="parent_id" value="<?php echo $parent_id;?>">
                                                        <input type="hidden" class="form-control" name="id" value="<?php echo $child_info['id'];?>">
                                                        <input type="hidden" class="form-control" name="old_orderby" value="<?php echo $child_info['orderby'];?>">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-default" id="btn-save" type="button" value="保存">
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
                    editor.html(bewrite);
                    editor.sync();
                });
            </script>
        </div>
    </div>

    <script>
        $(function(){
            $("#btn-save").click(function(){
                var title = $("input[name='title']").val();
                var id = $("input[name='id']").val();
                var content = $("[name='content']").val();
                var parent_id = $("input[name='parent_id']").val();
                var orderby = $("input[name='orderby']").val();
                var old_orderby = $("input[name='old_orderby']").val();
                $.post("<?php echo MLS_ADMIN_URL;?>/help_center/save_modify_child",{id:id,title:title,content:content,orderby:orderby,old_orderby:old_orderby},
                    function(data){
                        if(data.status == 1){
                            alert('修改成功！');
                        } else {
                            alert('修改失败')
                        }
                        location.href = "<?php echo MLS_ADMIN_URL;?>/help_center/show_sall/"+parent_id;
                    },"json");
            });
            $("#btn-return").click(function(){
                var parent_id = $("input[name='parent_id']").val();
                location.href = "<?php echo MLS_ADMIN_URL;?>/help_center/show_sall/"+parent_id;
            });
        })
    </script>
<?php require APPPATH.'views/footer.php'; ?>
