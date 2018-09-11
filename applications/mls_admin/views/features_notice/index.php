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
                            <form name="search_form" id="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                         <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>功能状态
                                                    <select name="status" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <option value="1" <?php if(1==$where_cond['status']){echo 'selected="selected"';}?>>有效</option>
                                                        <option value="2" <?php if(2==$where_cond['status']){echo 'selected="selected"';}?>>草稿</option>
                                                    </select>
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" id="pg" name="pg" value="<?=$page?>">
                                                        <input class="btn btn-primary" onclick="$('#pg').val('1');" type="submit" value="查询">
                                                        <a class="btn btn-primary" href='###' id="btn_add">添加</a>
                                                    </div>
                                                </label>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                           </form>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>编号</th>
                                    <th>功能标题</th>
                                    <th>关键词</th>
                                    <th>发布时间</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($lists) && !empty($lists)) {
                                $i = 1;
                                foreach ($lists as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id'] ; ?></td>
                                        <td><?php echo $value['title']; ?></td>
                                        <td><?php echo $value['key_word']; ?></td>
                                        <td><?php echo date('Y-m-d',$value['create_time']); ?></td>
                                        <td><?php echo $status_arr[$value['status']]; ?></td>
                                        <td>
                                            <a href="###" onclick="view_details(<?php echo $value['id']; ?>);" >查看</a>
                                            <a href="<?php echo MLS_ADMIN_URL;?>/features_notice/modify/<?php echo $value['id'];?>" >修改</a>
                                            <?php if('1'==$value['status']){ ?>
                                            <a href="#" onclick="del(<?php echo $value['id'];?>);">下架</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                               <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                    <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/user/index');?>
                                 </ul>
                            </div>
                          </div>
                            <div style="color:blue;position:absolute;right:33px;">
                                <b>共查到<?php echo $user_num;?>条数据</b>
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

    $("#btn_add").click(function(){
        location.href="<?php echo MLS_ADMIN_URL;?>/features_notice/add";
    });

    function view_details(id){
		layer.open({
			type: 2,
			title: '查看功能迭代',
			shadeClose: false,
			maxmin: true, //开启最大化最小化按钮
			area: ['800px', '450px'],
			content: '/features_notice/details/'+id,
		    end: function(){
				$("#search_form").submit();
			}
		});
    }

    function del(id) {
        if(confirm('确定要下架吗？') == true){
            $.post("<?php echo MLS_ADMIN_URL;?>/features_notice/change_status/2",{notice_id:id},function(data){
                if(data.status == 1){
                    alert('下架成功！');
                } else {
                    alert('下架失败！');
                }
                location.reload();
            },"json");
        }
    }
</script>
<?php require APPPATH . 'views/footer.php'; ?>
