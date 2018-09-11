<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">权限二级菜单功能列表</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                            <form name="search_form" method="post" action="">
                            <div class="row">
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>模块
                                            <select id="module_id" name="module_id" aria-controls="dataTables-example" class="form-control input-sm" onchange="get_tab_list();">
                                                <option value="0">请选择</option>
                                                <?php foreach($module_list as $key=>$val){?>
                                                <option value="<?php echo $val['id'];?>" <?php if($val['id']==$module_id){echo "selected='selected'";} ?>><?php echo $val['name'];?></option>
                                                <?php }?>
                                            </select>
                                        </label>
                                        <label>一级菜单
                                            <select id="tab_id" name="tab_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                <option value="0">请选择</option>
                                                <?php foreach($tab_list as $key=>$val){?>
                                                <option value="<?php echo $val['id'];?>" <?php if($val['id']==$tab_id){echo "selected='selected'";} ?>><?php echo $val['name'];?></option>
                                                <?php }?>
                                            </select>
                                        </label>
                                        <label>
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input type="hidden" name="pg" value="1">
                                                <input class="btn btn-primary" type="submit" value="查询">
                                                <a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/permission_secondtab_menu/add/<?php echo $module_id; ?>/<?php echo $tab_id; ?>'>添加</a>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- /.panel-heading -->

                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>权限模块</th>
                                    <th>一级菜单</th>
                                    <th>二级菜单</th>
                                    <th>权限节点</th>
                                    <th>是否显示</th>
                                    <th>类名</th>
                                    <th>方法名</th>
                                    <th>功能</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($permission_secondtab_menu) && !empty($permission_secondtab_menu)) {
                                    foreach ($permission_secondtab_menu as $key => $value) {
                                        ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id']; ?></td>
                                            <td><?php echo $value['module_name']; ?></td>
                                            <td><?php echo $value['menu_name']; ?></td>
                                            <td><?php echo $value['name']; ?></td>
                                            <td><?php foreach ($permission_list as $l){
														if($value['pid']==$l['pid']){
															echo $l['pname'];
														}
												}?>
											</td>
                                            <td><?php if($value['is_display']){echo "是";}else{echo "<span style='color:red'>否</span>";} ?></td>
                                            <td><?php echo $value['class']; ?></td>
                                            <td><?php echo $value['method']; ?></td>
                                            <td>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/permission_secondtab_menu/modify/<?php echo $value['id']; ?>" >修改</a>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/permission_secondtab_menu/del/<?php echo $value['id']; ?>" onclick="return checkdel()">删除</a>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
<?php echo page_uri($page, $pages, MLS_ADMIN_URL . '/permission_secondtab_menu/index'); ?>
                                    </ul>
                                </div>
                            </div>
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
    function checkdel() {
        if (confirm("确实要删除吗？"))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    //改变菜单
function get_tab_list(){
    var module_id = $("#module_id").val();
    if(module_id){
        $.ajax({
            url: "/permission_secondtab_menu/get_tab_list/",
            type: "GET",
            dataType: "json",
            data: {
                module_id:module_id
            },
            success: function(data) {
                var html ="<option value='0'>请选择</option>";
                if(data['result'] == 'ok')
                {
                    var list = data['list'];
                    for(var i in list){
                        html += "<option value='"+list[i]['id']+"'>"+list[i]['name']+"</option>";
                    }
                }
                $("#tab_id").html(html);
            }
        });
    }
}
</script>
<?php require APPPATH . 'views/footer.php'; ?>

