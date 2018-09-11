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
                        <div class="table-responsive">
                            <form name="search_form" id="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                         <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>设置查询条件
                                                    <select name="search_where" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <option value="name" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'name')){echo 'selected="selected"';}?>>公司名称</option>
                                                    </select>
                                                </label>
                                                <label>
                                                  包含<input type='search' class="form-control input-sm" size='12' name="search_value" value="<?php if(!empty($where_cond['search_value'])) {echo $where_cond['search_value'];}?>"/>
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" id="pg" name="pg" value="<?=$page?>">
                                                        <input class="btn btn-primary" onclick="$('#pg').val('1');" type="submit" value="查询">
                                                        <a class="btn btn-primary" href='###' onclick="add_company();">添加</a>
                                                        <a class="btn btn-primary" href='/company/exportReport/<?php
                                                          if(isset($where_cond['search_where']) && isset($where_cond['search_value'])){
                                                            echo '?search_where='.$where_cond['search_where'].'&search_value='.$where_cond['search_value'];
                                                          }
                                                          ?>'>导出</a>
                                                    </div>
                                                </label>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                           </form>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>公司名称</th>
                                    <th>电话</th>
                                    <th>开通时间</th>
                                    <th>门店数量</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($company) && !empty($company)) {
                                foreach ($company as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id']; ?></td>
                                        <td><?php echo $value['name']; ?></td>
                                        <td><?php echo $value['telno']; ?></td>
										<td><?php echo $value['add_time'] == 0 ? '' : date('Y-m-d H:i:s', $value['add_time']); ?></td>
                                        <td><a href="/agency/index/<?=$value['id'];?>"><?php echo $value['agency_count']; ?></a></td>
                                        <td>
                                            <a href="###" onclick="modify_company(<?php echo $value['id']; ?>);" >修改</a>&nbsp;|&nbsp;
                                            <?php if ($value['agency_count'] > 0) { ?>
                                            删除&nbsp;|&nbsp;
                                            <?php } else { ?>
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/company/delete/<?php echo $value['id']; ?>"  onclick="return checkdel()">删除</a>&nbsp;|&nbsp;
                                            <?php } ?>
					    <!--<a href="<?php echo MLS_ADMIN_URL; ?>/company/uploadphoto/<?php echo $value['id']; ?>" >上传公司Logo</a>&nbsp;|&nbsp;-->
					    <a href="#" onclick="uploadphoto(<?php echo $value['id']; ?>);">上传公司Logo</a>&nbsp;|&nbsp;
                                            <?php if($value['is_permission_initialize_success'] <> 11){?>
                                            <!--<a href="<?php echo MLS_ADMIN_URL; ?>/company/update_company_permission/<?php echo $value['id']; ?>" >重新初始化权限</a>-->
                                            <a href="#" onclick="update_company_permission(<?php echo $value['id']; ?>);">重新初始化权限</a>
                                            <?php }?>
                                        </td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6 clearfix" style="width:100%;">
                                <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $count;?>&nbsp;条数据！</b></span>
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/company/');?>
                                    </ul>
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
    function add_company()
    {
            layer.open({
                    type: 2,
                    title: '添加公司',
                    shadeClose: false,
                    maxmin: true, //开启最大化最小化按钮
                    area: ['600px', '550px'],
                    content: '/company/add',
                end: function(){
                            $("#search_form").submit();
                    }
            });
    }
    function modify_company(id)
    {
            layer.open({
                    type: 2,
                    title: '修改公司',
                    shadeClose: false,
                    maxmin: true, //开启最大化最小化按钮
                    area: ['800px', '650px'],
                    content: '/company/modify/'+id,
                end: function(){
                            $("#search_form").submit();
                    }
            });
    }
    function checkdel(id)
    {
         if (confirm('确定要删除此公司？')) {
             return true;
         } else {
             return false;
         }
    }
    function uploadphoto(id)
    {
            layer.open({
                    type: 2,
                    title: '上传公司Logo ',
                    shadeClose: false,
                    maxmin: true, //开启最大化最小化按钮
                    area: ['400px', '400px'],
                    content: '/company/uploadphoto/'+id,
                end: function(){
                            $("#search_form").submit();
                    }
            });
    }
    function update_company_permission(id){
        $.ajax({
            type : 'post',
            url  : '<?php echo MLS_ADMIN_URL; ?>/company/update_company_permission',
            dataType :'json',
            data:{id:id},
            success : function(data){
                if(data.result == 11){
                    layer.alert('<strong>重新初始化权限成功！</strong>', {
                        icon:1,
                        area: ['320px'],
                        shadeClose: false,
                        title: '删除权限模块',
                        closeBtn :false
                    },function(){
                        window.location.href="<?php echo MLS_ADMIN_URL.'/company/index'?>";
                    });
                }else{
                    layer.alert('<strong>重新初始化权限失败，请重新初始化！</strong>', {
                        icon:2,
                        area: ['320px'],
                        shadeClose: false,
                        title: '删除权限模块',
                        closeBtn :false
                    },function(){
                        window.location.href="<?php echo MLS_ADMIN_URL.'/company/index'?>";
                    });
                }
            }
        });
    }
</script>
<?php require APPPATH . 'views/footer.php'; ?>

