<?php require APPPATH . 'views/header.php'; ?>
<link href="<?=MLS_SOURCE_URL ?>/min/?f=mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?></h1>
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
                                        <label>
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input type="hidden" name="pg" value="1">
                                                <a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/permission_modules/add/'>添加</a>
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
                                    <th>权限</th>
                                    <th>状态</th>
                                    <th>功能</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($permission_modules) && !empty($permission_modules)) {?>

                                    <?php foreach ($permission_modules as $key => $vo) { ?>
                                        <tr class="gradeA" id="tr<?php echo $vo['pid'];?>">
                                            <td><?php echo $vo['pid']; ?></td>
                                            <td><?php echo $vo['name']; ?></td>
                                            <td><?php echo $vo['pname']; ?></td>
                                            <td id="status"><?php if($vo['status']==1){echo "<span style='color:green'>有效</span>";}else{echo "<span style='color:red'>失效</span>";} ?></td>
                                            <td>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/permission_modules/modify/<?php echo $vo['pid']; ?>" >修改</a>
                                                <!--<a href="<?php echo MLS_ADMIN_URL; ?>/permission_modules/del/<?php echo $vo['pid']; ?>" onclick="return checkdel()">删除</a>
                                                <a href="javascript:void(0)" onClick="_del(<?php echo $vo['pid'];?>)">删除</a>-->
                                            </td>
                                        </tr>
				    <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page, $pages, MLS_ADMIN_URL . '/permission_modules/index'); ?>
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
        if (confirm("删除权限模块将一并删除菜单及如下所有的功能，确实要删除吗？"))
        {
            return true;
        }
        else
        {
            return false;
        }
    }


	function _del(pid){
		$.ajax({
			type: "POST",
			url: "/permission_modules/del_del/",
			data: "pid="+pid,
			dataType:"json",
			cache:false,
			error:function(){
				alert("系统错误");
				return false;
			},
			success: function(data){
				if(data.status){
					$('#tr'+data.pid).children("#status").html("<span style='color:red'>失效</span>");
					alert("删除成功");
				}else{
					alert("删除失败");
				}
			}
		});

	}
</script>
<?php require APPPATH . 'views/footer.php'; ?>

