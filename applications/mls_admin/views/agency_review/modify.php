<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$title?></h1>
            </div>
        </div>
        <?php if ($modifyResult == '') { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="add_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    总店:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <?=$agency_review['company_name']?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    分店名称<font color="red">*</font>:&nbsp&nbsp<input type="search" name="name" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$agency_review['agency_name']?>" readonly="readonly">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                   分店电话<font color="red">*</font>:&nbsp&nbsp<input type="search" name="telno" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$agency_review['telno']?>" readonly="readonly">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    分店地址<font color="red">*</font>:&nbsp&nbsp<input type="search" name="address" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$agency_review['address']?>" size="62" readonly="readonly">
                                                </label>
                                            </div>
                                        </div>
                                       <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    申请人  王海  操作 <?=$agency_review['action_str']?>门店
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    审核通过<font color="red">*</font>:&nbsp
                                                    <select id="status" name="status" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                        <?php foreach ($agency_review['status_arr'] as $k => $v) { ?>
                                                            <option value="<?=$k?>"<?php if($k==$agency_review['status']){echo 'selected="selected"';}?>><?php echo $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span style="vertical-align: top;">备注</span>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<textarea name="remark" rows="3" cols="50"><?=$agency_review['remark']?></textarea>
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
                                                <a class="btn btn-primary" href="/agency_review/index">返回</a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="submit_flag" value="modify">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } else if (0 === $modifyResult) { ?>
            <div><h1><b>修改失败</b></h1></div>
            <a href="/agency_review/index">点此返回</a>
        <?php } else { ?>
            <div><h1><b>修改成功</b></h1></div>
            <a href="/agency_review/index">点此返回</a>
        <?php } ?>
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
});
</script>
<?php require APPPATH . 'views/footer.php'; ?>

