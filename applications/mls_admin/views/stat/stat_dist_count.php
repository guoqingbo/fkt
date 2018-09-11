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
                                                        登录时间&nbsp;&nbsp;介于&nbsp;<input type="text" name="start_time" style="width:183px" id="start_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['start_time'])){echo $_POST['start_time'];}?>" onclick="WdatePicker()">
                                                    &nbsp;至&nbsp;<input type="text" id="end_time" name="end_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['end_time'])){echo $_POST['end_time'];}?>" onclick="WdatePicker()">
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
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
									<th>统计时间</th>
                                    <th>数据统计</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($dataarr) && !empty($dataarr)) {
                                foreach ($dataarr as $key => $value) { ?>
                                    <tr class="gradeA">
										<td style='width:50px;'><?=$value['ymd']?></td>
										<td>
											<table class="table table-striped table-bordered table-hover">
												<tr>
													<td>统计项</td>
												<?php
													foreach($district as $k=>$v)
													{
														echo "<td>". $v . "</td>";
													}
												?>
												</tr>
											<?php foreach($value['keyarr'] as $kv){ ?>
												<tr>
													<td><?=$kv[2]?></td>
											<?php
												foreach($district as $k=>$v)
												{
													if($kv[0] == 0)
													{
														$num = isset($value[$kv[1]][$k]) ? $value[$kv[1]][$k] : 0;
														echo "<td>". $num . "</td>";
													}
													else if($kv[0] == 1)
													{
														$num1 = isset($value[$kv[1][0]][$k]) ? $value[$kv[1][0]][$k] : 0;
														$num2 = isset($value[$kv[1][1]][$k]) ? $value[$kv[1][1]][$k] : 0;
														$p = $num2 > 0 ? number_format($num1 / $num2 * 100, 2) : 0;
														echo "<td>". $p . "%<br />". $num1 . " / " . $num2. "</td>";
													}
													else if($kv[0] == 2)
													{
														$num1 = isset($value[$kv[1][0]][$k]) ? $value[$kv[1][0]][$k] : 0;
														$num2 = isset($value[$kv[1][1]]) ? $value[$kv[1][1]] : 0;
														$p = $num2 > 0 ? number_format($num1 / $num2 * 100, 2) : 0;
														echo "<td>". $p . "%<br />". $num1 . " / " . $num2. "</td>";
													}
												}
											?>
												</tr>
											<?php } ?>
											</table>
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
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/stat_dist_count/');?>
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
