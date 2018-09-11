<?php require APPPATH.'views/header.php'; ?>
<div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">发布新消息</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(''==$addResult){ ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="">
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
                                                    内容:&nbsp&nbsp&nbsp&nbsp<textarea name="message" class="form-control" aria-controls="dataTables-example"  rows="7" cols="52"></textarea>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>选择用户组:&nbsp&nbsp&nbsp&nbsp
                                                    <input type='radio' name='user_group' value='1' checked/>所有用户
                                                    <input type='radio' name='user_group' value='2'/>认证用户
                                                    <input type='radio' name='user_group' value='3'/>未认证用户
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <input type='hidden' name='submit_flag' value='add'/>
                                                    链接:&nbsp&nbsp&nbsp&nbsp
                                                    <input type="radio" name="url_type" value="1" checked>功能模块
                                                    <select name="url1" id="url1">
                                                    <?php foreach($arr_url_type as $key=>$val) { ?>
                                                        <option value="<?=$val['url']?>"><?=$val['name']?></option>
                                                    <?php } ?>
                                                    </select>
                                                    <input type="radio" name="url_type" value="2">自定义
                                                    <input type="text" name="url2" id="url2" class="form-control input-sm" aria-controls="dataTables-example" value="" size="60" disabled>
                                                </label>
                                            </div>
                                        </div>
                                        <?php if(!empty($issue_msg_error)){?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <font color='red'><?php echo $issue_msg_error; ?></font>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="submit" value="发布">
                                                <input class="btn btn-primary" type="button" value="取消" onclick="javascript:history.go(-1)">
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
    <?php }else if(0===$addResult){
                echo "<script>alert('发布失败')</script>";
                echo "<script>location.href='".MLS_ADMIN_URL."/issue_msg/index'</script>";
         }else{
                echo "<script>alert('发布成功')</script>";
            echo "<script>location.href='".MLS_ADMIN_URL."/issue_msg/index'</script>";
         }?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<?php require APPPATH.'views/footer.php'; ?>
<script type="text/javascript">
    $(function(){
        $("input[name='url_type']").click(function(){
            if ($(this).val() == 2) {
                $('#url2').attr('disabled', false);
            } else {
                $('#url2').attr('disabled', true);
            }
        });
    });
</script>
