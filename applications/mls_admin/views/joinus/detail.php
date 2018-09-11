<head>
<link href="<?=MLS_SOURCE_URL ?>/min/?f=mls/css/v1.0/picpop.css" rel="stylesheet" type="text/css"/>
</head>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquey-bigic.js" type="text/javascript"></script>
<?php require APPPATH . 'views/header.php'; ?>
<style>
    .dataTables_length {
        line-height: 30px;
    }
</style>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$title?></h1>
            </div>
        </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="add_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>详情编号：</label>
													<label><?=$list['id']?></label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>客户姓名：</label>
													<label><?=$list['name']?></label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>客户手机：</label>
													<label><?=$list['phone']?></label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>客户邮箱：</label>
													<label><?=$list['email']?></label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>申请省市：</label>
													<label><?=$list['province']?>--<?=$list['city']?></label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>公司名称：</label>
													<label><?=$list['company_name']?></label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>公司地址：</label>
													<label><?=$list['address']?></label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>公司电话：</label>
													<label><?=$list['company_phone']?></label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>公司介绍：</label>
                                                    <label>
														<textarea name="remark" rows="3" cols="50" id="remark"><?=$list['remark']?></textarea>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
											<div class="dataTables_length" id="dataTables-example_length">
												<input type="hidden" name="submit_flag" value="modify">
												<!--<input type="submit" class="btn btn-primary" value="提交">-->
												<a class="btn btn-primary" href="/joinus/index/">返回</a>
											</div>
											</div>
                                        </div>
                                    </div>
                                </form>
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
