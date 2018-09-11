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
                    <h1 class="page-header">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $motice_details['title']; ?></h1>
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
                                                <div style="font-size: 15px;">
                                                    <b>作者：</b><?php echo $motice_details['author_name']; ?>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <b>发布时间：</b><?php echo date('Y-m-d',$motice_details['create_time']); ?>
													&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <b>关键词：</b><?php echo $motice_details['key_word']; ?><br><br><br>
                                                </div>

                                                <div style="font-size: 15px;">
                                                    <?php echo $motice_details['content']; ?>
                                                </div>
                                                <br>
                                                <?php if(is_full_array($file_list)){
                                                 ?>
                                                <div style="font-size: 15px;">
                                                <?php
                                                    foreach($file_list as $key => $value){
                                                ?>
                                                    <b>附件<?php echo $key+1; ?>：</b><a class="a a2" href="<?php echo $value['file_url']; ?>">点击下载</a> &nbsp;&nbsp;&nbsp;
                                                <?php } ?>
                                                </div>
                                                <br>
                                                <?php } ?>
                                                <?php if(is_full_array($leave_message_list)){ ?>
                                                     <div class="panel-body">
                                                         <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                                             <tbody>
                                                                 <thead>
                                                                     <tr>
                                                                         <th>留言</th>
                                                                         <th>留言者</th>
                                                                         <th>时间</th>
                                                                     </tr>
                                                                 </thead>
                                                                 <?php foreach($leave_message_list as $key => $value){ ?>
                                                                 <tr class="gradeA">
                                                                     <td><?php echo $value['content']; ?></td>
                                                                     <td><?php echo $value['writer_name']; ?></td>
                                                                     <td><?php echo date('Y-m-d H:i',$value['create_time']); ?></td>
                                                                 </tr>
                                                                 <?php } ?>
                                                             </tbody>
                                                         </table>
                                                     </div>
                                                <?php } ?>
                                                <div style="font-size: 15px;">
                                                    <b>留言：</b><input type="text" name="message_content" value="" style="width: 500px">
                                                </div>
                                                <br>
                                                <div>
                                                    <input type="hidden" class="form-control" name="parent_id" value="<?php echo $parent_id;?>">
                                                    <input class="btn btn-default" id="btn-save" type="button" value="提交">
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
        $(function(){
            $("#btn-save").click(function(){
                var message_content = $("input[name='message_content']").val();
                var notice_id = $("input[name='notice_id']").val();
                $.post("<?php echo MLS_ADMIN_URL;?>/features_notice/add_message",{message_content:message_content,notice_id:notice_id},
                    function(data){
                        if(data.status == 1){
                            alert('提交成功！');
                            window.parent.location.reload();
                        } else if(data.status == 2){
                            alert('请输入留言！');
                        } else {
                            alert('提交失败');
                            window.parent.location.reload();
                        }

                    },"json");
            });
        })
    </script>
<?php require APPPATH.'views/footer.php'; ?>
