<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
		<div class="">
            <div class="col-lg-12">
                <h1 class="page-header">今日待办事项</h1>
				<table style="width:70%;" class="table table-striped table-bordered table-hover">
				<tr><td width="25%">
				<a href="javascript:void(0);" onclick="to_surl('liid<?php echo $auth_review['id']?>','<?php echo '/'.$auth_review['path'];?>');" style="font-size:14px">待认证用户(<span style="color:red"><?php echo $auth_review['num']?></span>)</a>
				</td><td width="25%">
				<a href="javascript:void(0);" onclick="to_surl('liid<?php echo $head_review['id']?>','<?php echo '/'.$head_review['path'];?>');" style="font-size:14px">待审核头像(<span style="color:red"><?php echo $head_review['num']?></span>)</a>
				</td><td width="25%">
				<a href="javascript:void(0);" onclick="to_surl('liid<?php echo $community['id']?>','<?php echo '/'.$community['path'];?>');" style="font-size:14px">待审核楼盘(<span style="color:red"><?php echo $community['num']?></span>)</a>
				</td><td width="25%">
				<a href="javascript:void(0);" onclick="to_surl('liid<?php echo $blacklist['id']?>','<?php echo '/'.$blacklist['path'];?>');" style="font-size:14px">待审核中介举报(<span style="color:red"><?php echo $blacklist['num']?></span>)</a>
				</td></tr>
				<tr><td>
				<a href="javascript:void(0);" onclick="to_surl('liid<?php echo $cooperate_chushen['id']?>','<?php echo '/'.$cooperate_chushen['path'];?>');" style="font-size:14px">待审核合作资料(<span style="color:red"><?php echo $cooperate_chushen['num']?></span>)</a>
				</td><td>
				<a href="javascript:void(0);" onclick="to_surl('liid<?php echo $cooperate_check['id']?>','<?php echo '/'.$cooperate_check['path'];?>');" style="font-size:14px">待审核真实合作(<span style="color:red"><?php echo $cooperate_check['num']?></span>)</a>
				</td><td>
				<a href="javascript:void(0);" onclick="to_surl('liid<?php echo $entrust_sell_review['id']?>','<?php echo '/'.$entrust_sell_review['path'];?>');" style="font-size:14px">待审核出售委托(<span style="color:red"><?php echo $entrust_sell_review['num']?></span>)</a>
				</td><td>&nbsp;</td></tr></table>
            </div>
        </div>

		<script>
			function to_surl(liid,url){
				window.location.href = url;
				parent.window.frames['leftFrame'].switch_tab(liid);
			}
		</script>
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $title ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>编号</th>
                                    <th>功能标题</th>
                                    <th>关键词</th>
                                    <th>发布时间</th>
                                    <th>发布者</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($lists) && !empty($lists)) {
                                $i = 1;
								$nowtime = time();
                                foreach ($lists as $key => $value) {
									$newstr = ($nowtime - $value['create_time']) < 86400 * 7 ? "<img src='".MLS_SOURCE_URL."/mls_admin/images/new_icon.gif' />" : '';
							?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id'] ; ?></td>
                                        <td><?php echo $newstr.$value['title']; ?></td>
                                        <td><?php echo $value['key_word']; ?></td>
                                        <td><?php echo date('Y-m-d',$value['create_time']); ?></td>
                                        <td><?php echo $value['author_name']; ?></td>
                                        <td>
                                            <a href="###" onclick="view_details(<?php echo $value['id']; ?>);" >查看</a>
                                        </td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <form name="search_form" method="post" action="">
                            <input type="hidden" name="pg" value="1">
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    $("#btn_add").click(function(){
        location.href="<?=MLS_ADMIN_URL ?>/features_notice/add";
    });

    function view_details(id){
		var index = layer.open({
			type: 2,
			title: '查看功能迭代',
			shadeClose: false,
			maxmin: true, //开启最大化最小化按钮
			area: ['800px', '480px'],
			content: '/features_notice/details/'+id,
		    end: function(){
				$("#search_form").submit();
			}
		});
		layer.full(index);
    }

    function del(id) {
        if(confirm('确定要删除该主菜单吗？') == true){
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
