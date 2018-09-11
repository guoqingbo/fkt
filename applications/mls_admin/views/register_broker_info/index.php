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
                                                <label>
                                                    <div>
                                                        手机号：<input type='search' class="form-control input-sm" size='12' name="search_phone" value="<?php if(!empty($where_cond['search_phone'])) {echo $where_cond['search_phone'];}?>"/>&nbsp;&nbsp;
                                                        姓名：<input type='search' class="form-control input-sm" size='12' name="search_name" value="<?php if(!empty($where_cond['search_name'])) {echo $where_cond['search_name'];}?>"/>&nbsp;&nbsp;
                                                        状态：<select name="search_status" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <option value="1" <?php if((!empty($where_cond['search_status']) && $where_cond['search_status'] == 1)){echo 'selected="selected"';}?>>待处理</option>
															<option value="3" <?php if((!empty($where_cond['search_status']) && $where_cond['search_status'] == 3)){echo 'selected="selected"';}?>>已电联</option>
                                                            <option value="2" <?php if((!empty($where_cond['search_status']) && $where_cond['search_status'] == 2)){echo 'selected="selected"';}?>>已处理</option>
                                                        </select>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>注册时间&nbsp;
                                                    &nbsp;介于&nbsp;<input type="text" name="start_time" style="width:183px" id="start_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['start_time'])){echo $_POST['start_time'];}?>" onclick="WdatePicker()">
                                                    &nbsp;至&nbsp;<input type="text" id="end_time" name="end_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['end_time'])){echo $_POST['end_time'];}?>" onclick="WdatePicker()">
                                                </label>
                                            </div>
                                         </div>
                                         <div class="col-sm-6" style="width:100%">
                                            <label>
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <input type="hidden" name="pg" value="1">
													<input type="hidden" name="call" id="call" value="0">
                                                    <input class="btn btn-primary" type="submit" value="查询">
													<a class="btn btn-primary" href='/register_broker_info/exportReport/<?php if(isset($where_cond['search_phone']) || isset($where_cond['search_name']) || isset($where_cond['search_status']) || (isset($_POST['start_time']) && isset($_POST['end_time']))){?><?php echo '?';?><?php }?>
														<?php
															if(isset($where_cond['search_phone'])){
																echo '&search_phone='.$where_cond['search_phone'];
															}
															if(isset($where_cond['search_name'])){
																echo '&search_name='.$where_cond['search_name'];
															}
															if(isset($where_cond['search_status'])){
																echo '&search_status='.$where_cond['search_status'];
															}
															if(isset($_POST['start_time']) && isset($_POST['end_time'])){
																echo '&start_time='.$_POST['start_time'].'&end_time='.$_POST['end_time'];
															}
														?>'>导出</a>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
						<script type="text/javascript">
						function set_call(id)
						{
							$('#call').val(id);
							$('#search_form').submit();
						}
						</script>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>手机号码</th>
                                    <th>真实姓名</th>
                                    <th>公司名称</th>
                                    <th>分店名称</th>
                                    <th>IP</th>
									<th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($broker_info) && !empty($broker_info)) {
                                foreach ($broker_info as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id']; ?></td>
                                        <td><?php echo $value['phone']; ?></td>
                                        <td><?php echo $value['truename']; ?></td>
                                        <td><?php echo $value['corpname']; ?></td>
                                        <td><?php echo $value['storename']; ?></td>
                                        <td><?php echo $value['ip']; ?></td>
										<td><?php echo $value['status'] == 1 ? '待处理' : ($value['status'] == 2 ? '已处理' : '已电联'); ?></td>
                                        <td>
											<?php if($value['status'] == 1){ ?>
											<a href="###" onclick="set_call(<?php echo $value['id']; ?>);">已电联</a>&nbsp;&nbsp;&nbsp;
											<a href="<?php echo MLS_ADMIN_URL; ?>/broker_info/modify/<?php echo $value['broker_info_id']; ?>/<?php echo $value['id']; ?>" >审核修改</a>
											<?php }else if($value['status'] == 3){ ?>
											<a href="<?php echo MLS_ADMIN_URL; ?>/broker_info/modify/<?php echo $value['broker_info_id']; ?>/<?php echo $value['id']; ?>" >审核修改</a>
											<?php }else{ ?>
											已处理
											<?php } ?>
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
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/register_broker_info/');?>
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
<?php require APPPATH . 'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
