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
                    <div class="panel-head">
                        <button id="btn_add" style="margin-left: 15px;margin-top: 5px;">新增子菜单</button>
                        <button id="btn_back" style="margin-left: 15px;margin-top: 5px;">返回</button>
                        <input id="parent_id" type="hidden" value="<?php echo $parent_id;?>">
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>编号</th>
                                    <th>子菜单菜单</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($children) && !empty($children)) {
                                $i = 1;
                                foreach ($children as $key => $children) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $i++ ; ?></td>
                                        <td><?php echo $children['title']; ?></td>
                                        <td>
                                            <a href="<?php echo MLS_ADMIN_URL;?>/help_center/show_sall/<?php echo $children['id'];?>" >查看</a>
                                            <a href="<?php echo MLS_ADMIN_URL;?>/help_center/modify_sname/<?php echo $children['id'];?>/<?php echo $children['title'];?>" >修改子菜单单信息</a>
                                            <a href="#" onclick="del(<?php echo $children['id'];?>);">删除</a>
                                        </td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    $("#btn_add").click(function(){
        var parent_id = $('#parent_id').val();
        location.href="<?php echo MLS_ADMIN_URL;?>/help_center/add_sparent/"+parent_id;
    });

    $("#btn_back").click(function(){
        location.href="<?php echo MLS_ADMIN_URL;?>/help_center/";
    });

    function del(id) {
        if(confirm('确定要删除该子菜单吗？') == true){
            $.post("<?php echo MLS_ADMIN_URL;?>/help_center/del_parent",{id:id},function(data){
                if(data.status == 1){
                    alert('删除成功！');
                } else {
                    alert('删除失败！');
                }
                location.reload();
            },"json");
        }
    }
</script>
<?php require APPPATH . 'views/footer.php'; ?>
