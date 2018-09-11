<?php require APPPATH.'views/header.php'; ?>
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
                                <form name="search_form" method="post" action="" >
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <b>修改主菜单名称</b><br>
                                                    <div class="form-group">
                                                        主菜单名称：<input type="text" name="title" value="<?php echo $parent_name['title'] ?>" >
                                                        <input type="hidden" class="form-control" name="parent_id" value="<?php echo $id;?>">
                                                        <input type="hidden" class="form-control" name="old_title" value="<?php echo $parent_name['title'];?>">
                                                    </div>
                                                    <div>
                                                        展示顺号值：<input type="text" name="orderby" value="<?php echo $parent_name['orderby']; ?>">&nbsp;&nbsp;<b>数值越大越靠前，若不填将默认为0</b>
                                                        <input type="hidden" class="form-control" name="old_orderby" value="<?php echo $parent_name['orderby'];?>">
                                                    </div>
                                                    <input class="btn btn-default" id="btn-save" type="button" value="保存">
                                                    <input class="btn btn-default" id="btn-return" type="button" value="返回">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
                var title = $("input[name='title']").val();
                var parent_id = $("input[name='parent_id']").val();
                var old_title = $("input[name='old_title']").val();
                var orderby = $("input[name='orderby']").val();
                var old_orderby = $("input[name='old_orderby']").val();
                $.post("<?php echo MLS_ADMIN_URL;?>/help_center/save_modify_pname",{parent_id:parent_id,title:title,old_title:old_title,old_orderby:old_orderby,orderby:orderby},
                    function(data){
                        if(data.status == 1){
                            alert('修改成功！');
                        } else if(data.status == 2) {
                            alert('请注意，您未修改任何信息！');
                        } else {
                            alert('修改失败')
                        }
                        location.href = "<?php echo MLS_ADMIN_URL;?>/help_center/index";
                    },"json");
            });
            $("#btn-return").click(function(){
                location.href = "<?php echo MLS_ADMIN_URL;?>/help_center/index";
            });
        })
    </script>
<?php require APPPATH.'views/footer.php'; ?>
