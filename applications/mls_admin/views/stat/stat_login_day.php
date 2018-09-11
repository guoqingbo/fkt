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
						    <form name="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <div>
                                                        登录时间<input type="text" name="stat_time" style="width:183px" id="stat_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$stat_time?>" onclick="WdatePicker()">
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" name="pg" value="1">
                                                        <input class="btn btn-primary" type="submit" value="查询">
                                                    </div>
                                                </label>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                            </form>
                        </div>
						<div style="font-size:16px;"><?=$stat_time?>登录人数为：<?=$num?></div>

                        <div class="table-responsive" style="margin-top:30px;">
							以下信息统计数据是根据经纪人PC、APP两端的实时最后在线操作时间进行统计分析处理的：<br />
							注册经纪人总量：<?php echo $arr['broker_total']; ?>&nbsp;&nbsp;
							已挂靠门店的经纪人总量：<?php echo $arr['broker_agency_total']; ?>&nbsp;&nbsp;
							有登录使用记录的门店总量：<?php echo $arr['agency_total']; ?>&nbsp;&nbsp;
							<a class="btn btn-primary" href="/stat_login/index/">查看每天登录次数</a>&nbsp;&nbsp;
                        </div>

						<table style="margin-top:20px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th> </th>
                                    <?php foreach($timearr as $tk=>$time){ ?>
									<th><?php echo $tk == 1 ? '今' : $tk; ?>天内登录使用量</th>
									<?php } ?>
									<th>超过30天未登录使用量</th>
                                </tr>
                            </thead>
                            <tbody>
								<tr>
									<td>已挂靠门店经纪人</td>
									<?php $bt = 0; foreach($timearr as $tk=>$time){$num = intval($arr['brokernum'][$tk]); ?>
									<td><?php echo $num; $bt = $num; ?></td>
									<?php } ?>
									<td><?php echo $arr['broker_agency_total'] - $bt; ?></td>
								</tr>
								<tr>
									<td>独立经纪人</td>
									<?php $bt2 = 0; foreach($timearr as $tk=>$time){$num = intval($arr['brokernum2'][$tk]); ?>
									<td><?php echo $num; $bt2 = $num; ?></td>
									<?php } ?>
									<td><?php echo $arr['broker_total'] - $arr['broker_agency_total'] - $bt2; ?></td>
								</tr>
								<tr>
									<td>门店</td>
									<?php $at = 0; foreach($timearr as $tk=>$time){$num = intval($arr['agencynum'][$tk]); ?>
									<td><?php echo $num; $at = $num; ?></td>
									<?php } ?>
									<td><?php echo $arr['agency_total'] - $at; ?></td>
								</tr>
							</tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPPATH . 'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
