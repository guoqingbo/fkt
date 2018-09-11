<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">添加权限二级菜单功能</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <?php if ('' == $addResult) { ?>
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
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <input type='hidden' name='submit_flag' value='add'/>
                                                    权限模块:&nbsp&nbsp&nbsp&nbsp
                                                    <select id="module_id" name="module_id" class="form-control input-sm" aria-controls="dataTables-example" onchange="get_tab_list();">
                                                        <option value="0">请选择</option>
                                                        <?php foreach($module_list as $key=>$val){?>
                                                        <option value="<?php echo $val['id'];?>" <?php if($val['id']==$module_id){echo "selected='selected'";} ?>><?php echo $val['name'];?></option>
                                                        <?php }?>
                                                    </select>
                                                </label>
                                                <label>
                                                    菜单:&nbsp&nbsp&nbsp&nbsp
                                                    <select id="tab_id" name="tab_id" class="form-control input-sm" aria-controls="dataTables-example">
                                                        <option value="0">请选择</option>
                                                        <?php foreach($tab_list as $key=>$val){?>
                                                        <option value="<?php echo $val['id'];?>" <?php if($val['id']==$tab_id){echo "selected='selected'";} ?>><?php echo $val['name'];?></option>
                                                        <?php }?>
                                                    </select>
                                                </label>
                                            </div>
											<div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <input type='hidden' name='submit_flag' value='add'/>
                                                    权限节点:&nbsp&nbsp&nbsp&nbsp
                                                    <select name="pid" class="form-control input-sm" aria-controls="dataTables-example">
                                                        <option value="0">请选择</option>
                                                        <?php foreach($permission_list as $key=>$val){?>
                                                        <option value="<?php echo $val['pid'];?>"><?php echo $val['pname'];?></option>
                                                        <?php }?>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    功能名称:&nbsp&nbsp&nbsp&nbsp<input type="search" name="name" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    是否显示:
                                                </label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_display" value="0">无</label>
                                                &nbsp&nbsp&nbsp&nbsp<label><input type="radio" name="is_display" value="1" checked="checked">有</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    类名:&nbsp&nbsp&nbsp&nbsp<input type="search" name="class" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                                <label style="color:#F00">有且只能唯一</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    方法名:&nbsp&nbsp&nbsp&nbsp<input type="search" name="method" class="form-control input-sm" aria-controls="dataTables-example" value="" size="40">
                                                </label>
                                                <label style="color:#F00">当前类下多个方法用逗号隔开</label>
                                            </div>
                                        </div>
										<div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    排序数值:&nbsp&nbsp&nbsp&nbsp<input type="search" name="order" class="form-control input-sm" aria-controls="dataTables-example" value="" size="10">
                                                数值越大越靠前
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
                                                <input class="btn btn-primary"  type="button" onclick="goback()" value="取消">
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
        <?php } else if (0 === $addResult) { ?>
            <div>插入失败</div>
        <?php } else { ?>
            <div>插入成功</div>
        <?php } ?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->



</div>
<!-- /#page-wrapper -->

</div>
<?php if ($addResult != "") { ?>
    <script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/permission_secondtab_menu/index/'.$module_id.'/'.$tab_id; ?>";
            }, 1000);
        });
    </script>
<?php } ?>

<script>
	function	goback(){
		location.href = "<?php echo MLS_ADMIN_URL . '/permission_secondtab_menu/index/'.$module_id.'/'.$tab_id; ?>";
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

