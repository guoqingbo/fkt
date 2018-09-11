<?php require APPPATH . 'views/header.php'; ?>
<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                <div class="row">
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            序号&nbsp;&nbsp;<input type="search" name="id" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$mass_broker['id']?>" readonly>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                           经纪人id&nbsp;&nbsp;<input type="search" name="broker_id" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$mass_broker['broker_id']?>" readonly>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            网站id，对应mass_site表中的 id&nbsp;&nbsp;<input type="search" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$mass_broker['site_id']?>" readonly>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            启用状态&nbsp;&nbsp;<input type="radio" name="status" value="1" <?php if($mass_broker['status']==1){ echo 'checked';}?>>已启用&nbsp;&nbsp;<input type="radio" name="status" value="0" <?php if($mass_broker['status']==0){ echo 'checked';}?>>未启用
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            经纪人在对应网站上注册的用户名，对应site_id所关联的网站&nbsp;&nbsp;<input type="search" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$mass_broker['username']?>" readonly>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            密码&nbsp;&nbsp;<input type="search" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$mass_broker['password']?>" readonly>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            用户id&nbsp;&nbsp;<input type="search" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$mass_broker['user_id']?>" readonly>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            三方加密密码&nbsp;&nbsp;<input type="search" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$mass_broker['otherpwd']?>" readonly>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            三方用户id&nbsp;&nbsp;<input type="search" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$mass_broker['agent_id']?>" readonly>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                            登录cookie&nbsp;&nbsp;<textarea style="width:800px;height:300px;" class="form-control input-sm" aria-controls="dataTables-example" readonly><?=$mass_broker['cookies']?></textarea>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <label>
                                           创建时间&nbsp;&nbsp;<input type="text" id="end_time" name="end_time" readonly class="form-control input-sm" value="<?php echo date('Y-m-d H:i:s', $mass_broker['createtime'])?>">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6" style="width:100%;display:none;">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                        <a class="btn btn-primary" href="#" onclick="submit('modify',<?=$broker_info['id']?>)">提交</a>
                                        <a class="btn btn-primary" href="/mass_site_broker/index">返回</a>
                                    </div>
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
