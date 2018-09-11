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
                                                    推送标题:
                                                </label>
                                                <label>
                                                    <input type="text" name="title" id = "title" value="<?=$news['title']?>" style="width:300px;">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%;padding-top:30px;">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    跳转地址:
                                                </label>
                                                <?php $extra = unserialize($news['extra']);
                                                ?>
                                                <label>
                                                    <input type="radio" name="href_infofrom" class="href_infofrom" value="1"
                                                    <?php if (!isset($news['href_infofrom']) || $news['href_infofrom'] == 1 ) {echo 'checked';}?>>内部
                                                </label>
                                                <label>
                                                    <select  name="type" aria-controls="dataTables-example" class="form-control input-sm type" style="width:168px">
                                                        <?php foreach($type as $k => $v) {?>
                                                        <option value="<?php echo $k;?>" <?php if (isset($extra['type']) && $extra['type'] == $k) {echo 'selected';}?>><?php echo $v;?></option>
                                                        <?php } ?>
                                                     </select>
                                                 </label>
                                                <label style="display:<?php if ($extra['type'] == 2) {echo 'inline;';} else {echo 'none;';}?>">
                                                    <select name="element_id2" aria-controls="dataTables-example" class="form-control input-sm element_id2" style="width:168px">
                                                        <?php
                                                        foreach($project_newhouse as $k => $v) {?>
                                                        <option value="<?php echo $v['newhouse_id'];?>" <?php if ($v['newhouse_id'] == $extra['element_id']) {echo 'selected';}?>><?php echo $v['lp_name'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label style="display:<?php if ($extra['type'] == 8) {echo 'inline;';} else {echo 'none;';}?>">
                                                    <select name="element_id8" aria-controls="dataTables-example" class="form-control input-sm element_id8" style="width:168px">
                                                        <?php
                                                        foreach($abroad as $k => $v) {?>
                                                        <option value="<?php echo $v['id'];?>" <?php if ($v['id'] == $extra['element_id']) {echo 'selected';}?>><?php echo $v['block_name'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label style="display:<?php if ($extra['type'] == 9) {echo 'inline;';} else {echo 'none;';}?>">
                                                    <select name="element_id9" aria-controls="dataTables-example" class="form-control input-sm element_id9" style="width:168px">
                                                        <?php
                                                        foreach($tourism as $k => $v) {?>
                                                        <option value="<?php echo $v['id'];?>" <?php if ($v['id'] == $extra['element_id']) {echo 'selected';}?>><?php echo $v['block_name'];?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <label>
                                                <input type="radio" class ="href_infofrom" name="href_infofrom" value="2" <?php if (isset($news['href_infofrom']) && $news['href_infofrom'] == 2) {echo 'checked';}?> > 外部
                                                <input type="text" name="url" id = "url" value="<?=$extra['url']?>" style="width:300px;"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%;padding-top:30px;">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="button" value="保存" id="submitBtn">&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input class="btn btn-primary" type="button"  value="推送" id="saveBtn">&nbsp;&nbsp;
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
            $(function() {
                var childnode;
                $('.type').change(function(){
                    var display_id = [2, 8, 9];
                    childnode = $(this).parent().siblings();
                    for(var i in display_id) {
                        if (display_id[i] == $(this).val())
                        {
                            $(childnode).find('.element_id' + display_id[i]).parent().css('display', 'inline');
                        }
                        else
                        {
                            $(childnode).find('.element_id' + display_id[i]).parent().css('display', 'none');
                        }
                    }
                });
            });
            //保存广告信息
            $('#saveBtn').bind('click', function() {
                $.ajax({
                    type : "POST",
                    url  : "/advert_app_manage/push_action/",
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
            });

            //提交广告信息
            $('#submitBtn').bind('click', function() {
                save_news();
            });

            function save_news()
            {
                var title = $('#title').val();
                var url = $('#url').val();
                if (title == '')
                {
                    alert('标题不能为空');
                    $('#title').focus();
                    return false;
                }
                var type = $('.type').val();
                var element_id = $('.element_id' + type).val();
                var href_infofrom = $('input:radio[name="href_infofrom"]:checked').val();
                $.ajax({
                    type : "POST",
                    url  : "/advert_app_manage/save_push",
                    data : {'title' : title, 'href_infofrom' : href_infofrom, 'url' : url, 'type' : type,
                        'element_id' : element_id},
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

