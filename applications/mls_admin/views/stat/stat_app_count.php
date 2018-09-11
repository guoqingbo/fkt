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
                            <form name="search_form" method="post" action="" id="search_form">
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
                                                        <input class="btn btn-primary" onclick="$('#search_form').attr('action','/stat_app_count/export/');" type="submit" value="导出">
														<span style="margin-left:50px;font-size:14px;color:red;">当前累计安装总量：<?php echo $count;?></span>
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
                                    <th>经纪人姓名</th>
									<th>所属公司</th>
									<th>所属门店</th>
                                    <th>设备类型</th>
									<th>设备号</th>
                                    <th>日期</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($dataarr) && !empty($dataarr)) {
                                foreach ($dataarr as $key => $value) { ?>
                                    <tr class="gradeA">
										<td><?=$value['broker_name']?></td>
										<td><?=$value['company_name']?></td>
										<td><?=$value['agency_name']?></td>
                                        <td><?=$value['devicetype']?></td>
										<td><?=$value['deviceid']?></td>
										<td><?php echo date('Y-m-d H:i:s', $value['dateline']); ?></td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6 clearfix" style="width:100%;">
                                <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $count;?>&nbsp;条数据！</b></span>
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/stat_app_count/');?>
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
