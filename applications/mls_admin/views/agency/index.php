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
														<option value="companyname" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'companyname')){echo 'selected="selected"';}?>>公司名称</option>
                                                        <option value="name" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'name')){echo 'selected="selected"';}?>>门店名称</option>
														<option value="dist" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'dist')){echo 'selected="selected"';}?>>区属</option>
                                                    </select>
                                                </label>
                                                <label>
                                                    包含<input type='search' class="form-control input-sm" size='12' name="search_value" value="<?php if(!empty($where_cond['search_value'])) {echo $where_cond['search_value'];}?>"/>
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" id="pg" name="pg" value="<?=$page?>">
                                                        <input class="btn btn-primary" onclick="$('#pg').val('1');" type="submit" value="查询">
                                                        <?php if(!$is_user_manager){ ?><a class="btn btn-primary" href='###' onclick="add_agency();">添加</a><?php }?>
														<a class="btn btn-primary" href='/agency/exportReport/<?php
															if(isset($where_cond['search_where']) && isset($where_cond['search_value'])){
																echo '?search_where='.$where_cond['search_where'].'&search_value='.$where_cond['search_value'];
															}

														?>'>导出</a>
                                                    </div>
                                                </label>

												<?php if(!$is_user_manager){ ?>
												<label>　　　　批量设置客户经理
                                                    <select name="master" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <?php foreach($masters as $key=>$value){ ?>
														<option value="<?php echo $key; ?>"><?php echo $value['truename']; ?></option>
														<?php } ?>
                                                    </select>
													<input type="hidden" id="plid" name="plid" />
													<input class="btn btn-primary" onclick="setids();$('#pg').val('1');" type="submit" value="设置">
                                                </label>
												<?php } ?>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                           </form>
                        </div>
						<script>
						var ac = false;
						function checkall(obj)
						{
							ac = ac == false ? true : false;
							$(".checkall").attr("checked", obj.checked);
						}
						function setids()
						{
							var setids = '';
							var arrChk=$(".checkall:checked");
							$(arrChk).each(function(){
							   setids = setids != '' ? setids + ',' + this.value : this.value;
							});
							$("#plid").val(setids);
						}
						</script>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" onclick="checkall(this)" />批量设置</th>
									<th>区属</th>
                                    <th>公司名称</th>
                                    <th>分店名称</th>
                                    <th>电话</th>
									<th>开通时间</th>
                                    <th>经纪人数量</th>
									<th>门店类型</th>
                                    <th>客户经理</th>
                                    <?php if(!$is_user_manager){ ?><th>操作</th><?php }?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($agency) && !empty($agency)) {
                                foreach ($agency as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><label><input class="checkall" type="checkbox" name="plid" value="<?php echo $value['id']; ?>" /><?php echo $value['id']; ?></label></td>
										<td><?php echo $value['dist_name']; ?></td>
                                        <td><?php echo $value['company_name']; ?></td>
                                        <td><?php echo $value['name']; ?></td>
                                        <td><?php echo $value['telno']; ?></td>
										<td><?php echo $value['add_time'] == 0 ? '' : date('Y-m-d H:i:s', $value['add_time']); ?></td>
                                        <td><?php echo $value['broker_count']; ?></td>
                                        <td><?php echo $value['agency_type'] == 1 ? '直营' : ($value['agency_type'] == 2 ? '加盟' : ($value['agency_type'] == 3 ? '合作' : '')); ?></td>
                                         <td><?php if (isset($masters[$value['master_id']])) {echo $masters[$value['master_id']]['truename'];} ?></td>
                                        <?php if(!$is_user_manager){ ?><td>
                                            <a href="###" onclick="modify_agency(<?php echo $value['id']; ?>);" >修改</a>
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/agency/delete/<?php echo $value['id']; ?>"  onclick="return checkdel()">删除</a>
                                        </td><?php }?>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <div class="row">
                           <div class="col-sm-6 clearfix" style="width:100%;">
                                <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $count;?>&nbsp;条数据！</b></span>
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/agency/');?>
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
	function add_agency()
	{
		layer.open({
			type: 2,
			title: '添加门店',
			shadeClose: false,
			maxmin: true, //开启最大化最小化按钮
			area: ['800px', '450px'],
			content: '/agency/add',
		    end: function(){
				$("#search_form").submit();
			}
		});
	}
	function modify_agency(id)
	{
		layer.open({
			type: 2,
			title: '修改门店',
			shadeClose: false,
			maxmin: true, //开启最大化最小化按钮
			area: ['800px', '450px'],
			content: '/agency/modify/'+id,
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
</script>
<?php require APPPATH . 'views/footer.php'; ?>

