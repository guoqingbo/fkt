<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">经纪人数据统计查询</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <form name="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
												 <label>设置查询条件
                                                    <select name="search_where" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <option value="company" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'company')){echo 'selected="selected"';}?>>公司名称</option>
														<option value="truename" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'truename')){echo 'selected="selected"';}?>>经纪人</option>
                                                    </select>
                                                </label>
                                                <label>
                                                  包含<input type='search' class="form-control input-sm" size='12' name="search_value" value="<?php if(!empty($where_cond['search_value'])) {echo $where_cond['search_value'];}?>"/>
                                                </label>
												<?php
												if(is_full_array($agencys))
												{
												?>
												<label>
												<select class="form-control input-sm" aria-controls="dataTables-example" name="agency_id">
													<option value="0">请选择门店</option>
												<?php
													foreach($agencys as $agency)
													{
													$selected = $agency['id'] == $agency_id ? 'selected' : '';
												?>
													<option value="<?php echo $agency['id']; ?>" <?php echo $selected; ?>><?php echo $agency['name']; ?></option>
												<?php
													}
												?>
												</select>
												</label>
												<?php
												}
												?>
                                                <label>
                                                    <div>
                                                        查询时间&nbsp;&nbsp;介于&nbsp;
														<input type="text" name="start_time" style="width:123px" id="start_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$start_time?>" onclick="WdatePicker()"/>
														&nbsp;至&nbsp;
														<input type="text" id="end_time" name="end_time" style="width:123px"  class="form-control input-sm" aria-controls="dataTables-example" value="<?=$end_time?>" onclick="WdatePicker()"/>
													</div>
                                                </label>
												<label>&nbsp;客户经理&nbsp;
                                                        <?php if($is_user_manager){ ?>
                                                            <select name="master_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                                <option value="<?php echo $this_user_id; ?>" selected="selected" ><?php echo $this_user_name; ?></option>
                                                            </select>
                                                        <?php }else{ ?>
                                                            <select name="master_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                                <option value="0">请选择</option>
                                                                <?php foreach($masters as $k => $v) { ?>
                                                                <option value="<?=$v['uid']?>" <?php if((!empty($master_id) && $v['uid'] == $master_id)){echo 'selected="selected"';}?>><?=$v['truename']?></option>
                                                                <?php } ?>
                                                            </select>
                                                        <?php }?>
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" name="pg" value="1">
                                                        <input class="btn btn-primary" type="submit" value="查询">

														<a class="btn btn-primary" href='/stat_broker/exportReport/<?php
															if(isset($where_cond['search_where']) && isset($where_cond['search_value'])){
																echo '?search_where='.$where_cond['search_where'].'&search_value='.$where_cond['search_value'];
															}
															if(isset($_POST['start_time']) && isset($_POST['end_time'])){
																echo '&start_time='.$_POST['start_time'].'&end_time='.$_POST['end_time'];
															}
															if(isset($_POST['master_id'])){
																echo '&master_id='.$_POST['master_id'];
															}else if ($is_user_manager){
                                                                echo '&master_id='.$this_user_id;
                                                            }
														?>'>单个导出</a>
														<a class="btn btn-primary" href='/stat_broker/exportReport/<?php
															if(isset($where_cond['search_where']) && isset($where_cond['search_value'])){
																echo '?total=1&search_where='.$where_cond['search_where'].'&search_value='.$where_cond['search_value'];
															}
															if(isset($_POST['start_time']) && isset($_POST['end_time'])){
																echo '&start_time='.$_POST['start_time'].'&end_time='.$_POST['end_time'];
															}
															if(isset($_POST['master_id'])){
																echo '&master_id='.$_POST['master_id'];
															}else if ($is_user_manager){
                                                                echo '&master_id='.$this_user_id;
                                                            }
														?>'>汇总导出</a>
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
                                    <th>客户经理</th>
                                    <th>经纪人姓名</th>
                                    <th>经纪人电话</th>
                                    <th>公司名</th>
                                    <th>门店名</th>
                                    <th>登录次数</th>
                                    <th>ERP出售新增量</th>
                                    <th>ERP出租新增量</th>
                                    <th>出售采集查看量</th>
                                    <th>出租采集查看量</th>
                                    <th>出售群发新增量</th>
                                    <th>出租群发新增量</th>
                                    <th>外网出售总量</th>
                                    <th>外网出租总量</th>
                                    <th>外网出售有图房源总量</th>
                                    <th>外网出租有图房源总量</th>
                                    <th>外网出售多图房源总量</th>
                                    <th>外网出租多图房源总量</th>
                                    <th>ERP出售总量</th>
                                    <th>ERP出租总量</th>
                                    <th>合作出售房源总量</th>
                                    <th>合作出租房源总量</th>
                                    <th>APP使用量</th>
                                    <th>出售视频房源总量</th>
                                    <th>出租视频房源总量</th>
                                    <th>统计时间</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($stat_broker_data) && !empty($stat_broker_data)) {
                                foreach ($stat_broker_data as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $masters[$value['master']]['truename']; ?></td>
										<td><?php echo $value['truename']; ?></td>
										<td><?php echo $value['phone']; ?></td>
										<td><?php echo $value['company']; ?></td>
										<td><?php echo $value['agency']; ?></td>
										<td><?php echo $value['login_num']; ?></td>
										<td><?php echo $value['sell_publish_num']; ?></td>
										<td><?php echo $value['rent_publish_num']; ?></td>
										<td><?php echo $value['sell_collect_view_num']; ?></td>
                                        <td><?php echo $value['rent_collect_view_num']; ?></td>
                                        <td><?php echo $value['sell_group_publish_num']; ?></td>
										<td><?php echo $value['rent_group_publish_num']; ?></td>
										<td><?php echo $value['sell_outside_num']; ?></td>
										<td><?php echo $value['rent_outside_num']; ?></td>
										<td><?php echo $value['sell_level_2_num']; ?></td>
										<td><?php echo $value['rent_level_2_num']; ?></td>
										<td><?php echo $value['sell_level_3_num']; ?></td>
										<td><?php echo $value['rent_level_3_num']; ?></td>
										<td><?php echo $value['sell_num']; ?></td>
										<td><?php echo $value['rent_num']; ?></td>
										<td><?php echo $value['sell_cooperate_num']; ?></td>
										<td><?php echo $value['rent_cooperate_num']; ?></td>
										<td><?php echo $value['app_access_num']; ?></td>
										<td><?php echo $value['sell_video_num']; ?></td>
										<td><?php echo $value['rent_video_num']; ?></td>
                                        <td><?php echo $value['ymd'];?></td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6 clearfix" style="width:100%;">
                                <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $count;?>&nbsp;条数据！</b></span>
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/stat_broker/');?>
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
