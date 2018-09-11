<?php require APPPATH . 'views/header.php'; ?>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
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
                                                <label>
                                                    <select name="search_where" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">条件筛选</option>
														<option value="product_serial_num" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'product_serial_num')){echo 'selected="selected"';}?>>商品编号</option>
                                                        <option value="product_name" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'product_name')){echo 'selected="selected"';}?>>商品名称</option>
                                                    </select>
                                                </label>
												<label>
                                                    <div><input type='search' class="form-control input-sm" size='12' name="search_value" value="<?php if(!empty($where_cond['search_value'])) {echo $where_cond['search_value'];}?>"/>
                                                    </div>
                                                </label>
												<?php if($where_cond['type'] == 1){?>
												<label>
                                                    <div>
                                                        商品状态
                                                      <select id='status' name="status"  class="form-control input-sm" aria-controls="dataTables-example">
                                                        <option value="">请选择</option>
                                                        <option value="1" <?php if((!empty($where_cond['status']) && $where_cond['status'] == '1')) {echo 'selected="selected"';}?>>已上架</option>
                                                        <option value="2" <?php if((!empty($where_cond['status']) && $where_cond['status'] == '2')) {echo 'selected="selected"';}?>>已下架</option>
                                                    </select>
                                                    </div>
                                                </label>
												<?php } ?>
                                                <label>
                                                    <select name="search_where_time" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">时间筛选</option>
														<option value="release_time" <?php if((!empty($where_cond['search_where_time']) && $where_cond['search_where_time'] == 'release_time')){echo 'selected="selected"';}?>>发布时间</option>
														<?php if($where_cond['type'] == 1){?>
                                                        <option value="down_time" <?php if((!empty($where_cond['search_where_time']) && $where_cond['search_where_time'] == 'down_time')){echo 'selected="selected"';}?>>下架时间</option>
														<?php } ?>
                                                    </select>
                                                </label>
												 <label>
                                                    <div>
														<input type="text" name="time_s" style="width:123px"  class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['time_s'])) {echo $_POST['time_s'];}?>" onclick="WdatePicker()"/>
														&nbsp;至&nbsp;
														<input type="text" name="time_e" style="width:123px"  class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['time_e'])) {echo $_POST['time_e'];}?>" onclick="WdatePicker()"/>
													</div>
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" id="pg" name="pg" value="<?=$page?>">
                                                        <input class="btn btn-primary" onclick="$('#pg').val('1');" type="submit" value="查询">
														<?php if($where_cond['type'] == 1){?>
                                                        <a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/gift_manage/add/1'>添加</a>
														<?php }else{?>
														<a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/gift_manage/add/2'>添加</a>
														<?php } ?>
														<a class="btn btn-primary" href='/gift_manage/exportReport/<?php if((isset($where_cond['search_where']) && isset($where_cond['search_value'])) || isset($where_cond['status']) || (isset($_POST['time_s']) && isset($_POST['time_e'])) || isset($where_cond['type'])){?><?php echo '?';?><?php }?>
														<?php
															if(isset($where_cond['search_where']) && isset($where_cond['search_value'])){
																echo 'search_where='.$where_cond['search_where'].'&search_value='.$where_cond['search_value'];
															}
															if(isset($where_cond['status'])){
																echo '&status='.$where_cond['status'];
															}
															if(isset($where_cond['search_where_time']) && isset($_POST['time_s']) && isset($_POST['time_e'])){
																echo '&search_where_time='.$where_cond['search_where_time'].'&time_s='.$_POST['time_s'].'&time_e='.$_POST['time_e'];
															}
															if(isset($where_cond['type'])){
																echo '&type='.$where_cond['type'];
															}
														?>'>导出</a>
														<?php if($where_cond['type'] == 1){?>
														<a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/gift_manage/index/1'>重置</a>
														<?php }else{?>
														<a class="btn btn-primary" href='<?php echo MLS_ADMIN_URL; ?>/gift_manage/index/2'>重置</a>
														<?php } ?>
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
                                    <th>商品编号</th>
                                    <th>商品排序</th>
                                    <th>商品类型</th>
                                    <th>商品名称</th>
									<?php if($where_cond['type'] == 1){?>
									<th>积分值</th>
									<?php } ?>
                                    <th>已兑换(抽奖)数量</th>
									<th>状态</th>
									<th>发布时间</th>
									<?php if($where_cond['type'] == 1){?>
									<th>下架时间</th>
									<?php } ?>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($gift) && !empty($gift)) {
                                foreach ($gift as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['product_serial_num'];?></td>
                                        <td><?php echo $value['order'];?></td>
                                        <td><?php echo ($value['type'] == 1)?'兑奖':'抽奖';?></td>
                                        <td><?php echo $value['product_name'];?></td>
										<?php if($where_cond['type'] == 1){?>
										<td><?php echo $value['score'];?></td>
										<?php } ?>
                                        <td><?php echo $value['over_exchange_num']; ?></td>
										<?php if($value['type'] == 1){?>
											<?php if($value['status'] == 1 && ($value['down_time'] > time())){?>
											<td><?php echo '已上架';?></td>
											<?php }elseif($value['status'] == 2 && ($value['down_time'] > time())){?>
											<td><?php echo '<font color="red">已下架</font';?></td>
											<?php }elseif($value['down_time'] <= time()){?>
											<td><?php echo '<font color="red">已下架</font';?></td>
											<?php }?>
										<?php }else{?>
											<?php if($value['status'] == 1 ){?>
											<td><?php echo '已上架';?></td>
											<?php }elseif($value['status'] == 2 ){?>
											<td><?php echo '<font color="red">已下架</font';?></td>
											<?php }?>
										<?php } ?>
										<td><?php echo date("Y-m-d H:i:s",$value['release_time']);?></td>
										<?php if($where_cond['type'] == 1){?>
										<td><?php echo date("Y-m-d H:i:s",$value['down_time']);?></td>
										<?php } ?>
                                        <td>
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/gift_manage/edit/<?php echo $value['id']; ?>/<?=$value['type']?>">编辑</a>&nbsp;|&nbsp;
											<?php if($value['type'] == 1){?>
												<?php if($value['status'] == 1 && ($value['down_time'] > time())){?>
												<a href="<?php echo MLS_ADMIN_URL; ?>/gift_manage/status/<?php echo $value['id']?>/2" onclick="return checkdown()">下架</a>
												<?php }elseif($value['status'] == 1 && ($value['down_time'] <= time())){?>
												<a href="###" onclick="return checkup('<?php echo $value['id']?>','<?php echo $value['type']?>')">上架</a>
												<?php }elseif($value['status'] == 2){?>
												<a href="###" onclick="return checkup('<?php echo $value['id']?>','<?php echo $value['type']?>')">上架</a>
												<?php }?>
											<?php }else{?>
												<?php if($value['status'] == 1 ){?>
												<a href="<?php echo MLS_ADMIN_URL; ?>/gift_manage/status/<?php echo $value['id']?>/2" onclick="return checkdown()">下架</a>
												<?php }elseif($value['status'] == 2){?>
												<a href="###" onclick="return checkup('<?php echo $value['id']?>','<?php echo $value['type']?>')">上架</a>
												<?php }?>
											<?php } ?>
											&nbsp;|&nbsp;
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/gift_manage/delete/<?php echo $value['id']?>"  onclick="return checkdel()">删除</a>
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
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/gift_manage/');?>
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
    function checkdel(id)
    {
         if (confirm('确定要删除此商品？')) {
             return true;
         } else {
             return false;
         }
    }
	function checkdown(id)
    {
         if (confirm('确定将改商品下架？')) {
             return true;
         } else {
             return false;
         }
    }
	function checkup(id,type)
    {
        /* if (confirm('确定将改商品上架？')) {
             return true;
         } else {
             return false;
         }*/
		layer.open({
			type: 2,
			title: '上架',
			shadeClose: true,
			//2016/1/26maxmin: true,
			scrollbar: false,
			area: ['300px', '200px'],
			content: '/gift_manage/modify_status/'+id+'/'+type,
			end: function(){
				$("#search_form").submit();
			}
		});
    }

</script>
<?php require APPPATH . 'views/footer.php'; ?>
