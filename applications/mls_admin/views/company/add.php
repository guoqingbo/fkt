<?php require APPPATH . 'views/header.php'; ?>
<style type="text/css">
    #r_s_popUP {position: absolute;top: 100px; left:100px;display: none}
    #r_s_popUP .replace_stores_popUp {position: relative;width: 410px; padding: 9px; border: 1px solid #6aa8e6; background: #fff; }
    .replace_stores_popUp .upgou { display: block; width: 7px;height: 5px; background: url(<?=MLS_SOURCE_URL ?>/mls_admin/images/xiangx.png) no-repeat; position: absolute; top: 230px;left: 45px; }
    .replace_stores_popUp li { padding: 10px 0;border-bottom: 1px dashed #dadada; zoom: 1;}
</style>
<div id="wrapper">
    <div id="page-wrapper">
        <?php if ($addResult == '') { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default" style="margin:20px 0; padding:20px 0;">
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
                                                    区属<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <select id="district" name="dist_id" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                        <option value="0">请选择</option>
                                                        <?php foreach ($district as $k => $v) { ?>
                                                            <option value="<?php echo $v['id'] ?>"><?php echo $v['district'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label>
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp板块:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <select id="street" name="streetid" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                        <option value="">请选择</option>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    总店<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="name" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                                <label>
                                                   &nbsp&nbsp总店电话:&nbsp&nbsp&nbsp&nbsp<input type="search" name="telno" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    联系人:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="linkman" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                                <label>
                                                   &nbsp&nbsp&nbsp邮编:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="zip_code" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                                <label>
                                                   &nbsp&nbsp&nbsp传真:&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="fax" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    总店地址:&nbsp&nbsp<input type="search" name="address" class="form-control input-sm" aria-controls="dataTables-example" value="" size="62">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    邮箱:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="email" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                                <label>
                                                   &nbsp&nbsp网址:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="website" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input type="checkbox" name="add_agency" id="add_agency">是否快捷添加门店&nbsp<a href="" id="per-hover"><img src="<?=MLS_SOURCE_URL ?>/mls_admin/images/tips.png" width="13px" height="13px" border="0"></a>
                                                <input type="hidden" id="agency" name="agency" value="">
                                            </div>
                                        </div>
                                        <div id="r_s_popUP">
                                            <div class="replace_stores_popUp">
                                                <ol>
                                                    <li>快捷添加门店可快捷的添加该公司的第一个门店，选择快捷添加后，填写分店名称即可添加
                                                    <li>分店的区属、板块、电话、地址将和创建公司时所填内容一致
                                                    <li>快捷添加操作完成后，修改公司或该分店的资料，不会影响快捷添加的分店或对应的公司资料
                                                    <li>此快捷添加只能添加该公司的第一个门店，若要添加更多门店，请至门店管理进行操作
                                                </ol>
                                                <i class="upgou"> </i>
                                            </div>
                                        </div>
                                        <!--添加门店 start -->
                                        <div class="col-sm-6" style="width:100%;display: none" id="is_show">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    分店<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="agency_name" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                                <label>
                                                    门店类型:&nbsp&nbsp&nbsp&nbsp
                                                    <select name="agency_type" class="form-control input-sm" style="width:155px">
                                                        <option value="0">请选择</option>
                                                        <option value="1">直营</option>
                                                        <option value="2">加盟</option>
                                                        <option value="3">合作</option>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        <!--end-->
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
                                               <!-- <a class="btn btn-primary" href="/company/index">返回</a>-->
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="submit_flag" value="add">
                                </form>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } else if (0 === $addResult) { ?>
            <div><h1><b>公司添加失败<?=$agency_mess_error;?></b></h1></div>
        <?php } elseif($addResult === 'no') { ?>
            <div><h1><b>公司权限初始化失败，请在列表页找到自己的公司，点击“重新初始化”</b></h1></div>
        <?php } else {?>
            <div><h1><b>公司添加成功<?=$agency_mess_error;?></b></h1></div>
        <?php }?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
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
    //提示
    $("#per-hover").hover(function(){
        $("#r_s_popUP").toggle();
    });
    $("#add_agency").change(function(){
        if($("#add_agency").prop("checked")){
            $('#is_show').show();
            $('#agency').val(1);
        }else{
            $('#is_show').hide();
            $('#agency').val('');
        }
    });
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

