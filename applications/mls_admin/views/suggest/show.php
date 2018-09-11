<?php require APPPATH . 'views/header.php'; ?>
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
                        <div class="col-sm-6" style="width:100%">
                            <div class="dataTables_length" id="dataTables-example_length">
                                <input type="hidden" id="suggest_id" value="<?php echo $suggest['id']?>"/>
                                <b>反馈内容：</b><br>
                                <p><?php echo $suggest['feedback']?></p>
                            </div>
                        </div>
                        <?php if(($suggest['status'] == 3) || ($suggest['status'] == 4)) { ?>
                        <div class="col-sm-6" style="width:100%">
                            <div class="dataTables_length" id="dataTables-example_length">
                                <label>
                                    处理意见：&nbsp;&nbsp;&nbsp;&nbsp;<?php if($suggest['status']==3){?>已处理<?php } else {?>已忽略<?php } ?>
                                </label>  
                            </div>
                            <div class="dataTables_length" id="dataTables-example_length">
                                以下为您填写的意见处理结果<br>
                                <textarea id="adminfeedback" class="input_t" disabled="disabled" style="width:600px;height:100px;" ><?php echo $suggest['adminfeedback'];?></textarea>
                            </div>
                        </div>
                        <?php }else {?>  
                        <div class="col-sm-6" style="width:100%">
                            <div class="dataTables_length" id="dataTables-example_length">
                                <label>
                                    处理意见：&nbsp;&nbsp;&nbsp;&nbsp;
                                </label>
                                <label>
                                    <input type="radio" name="dealtype" value="3" checked='checked'/>已处理&nbsp;&nbsp;&nbsp;&nbsp;
                                </label>
                                <label>
                                    <input type="radio" name="dealtype" value="4" />忽略&nbsp;&nbsp;&nbsp;&nbsp;<font color="red">*</font>注：当您打开该意见反馈时，该意见反馈已自动变更为【处理中】状态！
                                </label>  
                            </div>
                            <div class="dataTables_length" id="dataTables-example_length">
                                <font color="red">*</font>当您选择【已处理】后，您可以在下框中填写回复，我们将以系统消息的形式发送给提议者<br>
                                <textarea id="adminfeedback" class="input_t" style="width:600px;height:100px;" ></textarea>
                            </div>
                            <div class="dataTables_length" id="dataTables-example_length">
                                <font color="red">*</font>若您现在不需要更改处理状态，请点击<b>【返回】</b>按钮,该条意见反馈将保持<b>【处理中】</b>的状态<br>
                            </div>
                        </div>
                        <?php } ?> 
                        <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                            <div class="row">
                            <div class="col-sm-6" style="width:100%">
                                <div class="dataTables_length" id="dataTables-example_length">
                                    <?php if(($suggest['status'] != 3) && ($suggest['status'] != 4)) { ?>
                                    <input class="btn btn-default" id="btn-save" type="button" value="保存">
                                    <?php } ?>
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
<script>
    $(function(){
        $("#btn-save").click(function(){
            var status = $("input:radio[name='dealtype']:checked").val();
            var suggest_id = $("#suggest_id").val();
            var adminfeedback = $("#adminfeedback").val();
            $.post("<?php echo MLS_ADMIN_URL;?>/suggest/change_status",{status:status,id:suggest_id,adminfeedback:adminfeedback},
                function(data){
                    if(data.status == 1){
                        alert('更改成功！');
                    } else {
                        alert('更改失败！')
                    }
                    location.href = "<?php echo MLS_ADMIN_URL;?>/suggest/index";
                },"json");
        });
        $("#btn-return").click(function(){
            location.href = "<?php echo MLS_ADMIN_URL;?>/suggest/index";
        });
    })
</script>
<?php require APPPATH . 'views/footer.php'; ?>
