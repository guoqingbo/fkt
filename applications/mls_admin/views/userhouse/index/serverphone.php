<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title; ?></h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <form name="search_form" id="form1" method="post" action="">
                                <input type='hidden' name='submit_flag' value='edit'/>
                                <div role="grid" class="dataTables_wrapper form-inline"
                                     id="dataTables-example_wrapper">
                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                电话：
                                                <textarea name="phone" id="phone" maxlength="100" rows="3" cols="40"><?php if (!empty($phone)) echo $phone['phone']; ?></textarea>
                                            </label>
                                        </div>
                                    </div>

                                    <?php if (!empty($mess_error)) { ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length" style="color:red;">
                                                <?php echo $mess_error; ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <input class="btn btn-primary" type="button" id="edit" value="提交"/>
                                            <input class="btn btn-primary" type="button" onclick="goback()" value="取消"/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
</div>
<!-- /#page-wrapper -->
</div>

<script>
function goback() {
    location.href = "/userhouse/index/index";
}
$(function(){
    $("#edit").click(function(){
        var phone = $.trim($("#phone").val());
        if (phone == '') {
            alert('电话不能为空');
            return false;
        }
        $("#form1").submit();
    });
})
</script>

<?php require APPPATH . 'views/footer.php'; ?>