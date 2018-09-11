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
                                                    <b>新增子菜单</b><br>
                                                    <div class="form-group">
                                                        子菜单名称：<input type="text" name="title" value="" />
                                                    </div>
                                                    <div>
                                                        展示顺号值：<input type="text" name="orderby" value="">&nbsp;&nbsp;<b>数值越大越靠前，若不填将默认为0</b>
                                                    </div>
                                                    <input type="hidden" value="<?php echo $parent_id; ?>" id="parent_id">
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
                var parent_id = $('#parent_id').val();
                var title = $("input[name='title']").val();
                var orderby = $("input[name='orderby']").val();
                $.post("<?php echo MLS_ADMIN_URL;?>/help_center/save_add_spname",{title:title,orderby:orderby,parent_id:parent_id},
                    function(data){
                        if(data.status == 1){
                            alert('新增成功！');
                        } else if (data.status == 2) {
                            alert('请输入新子菜单名称！');
                        } else {
                            alert('新增失败')
                        }
                        location.href = "<?php echo MLS_ADMIN_URL;?>/help_center/show/"+parent_id;
                    },"json");
            });
            $("#btn-return").click(function(){
                var parent_id = $('#parent_id').val();
                location.href = "<?php echo MLS_ADMIN_URL;?>/help_center/show/"+parent_id;
            });
        })
    </script>
<?php require APPPATH.'views/footer.php'; ?>
