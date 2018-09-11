<?php require APPPATH . 'views/header.php'; ?>
<style>
    span{text-align: right;display: inline-block;width:75px}
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
                                <form name="search_form" method="post" action="">
                                    <input type="hidden" name="submit_flag" value="save">
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
                                                    <span>意向区属:&nbsp&nbsp</span><?php echo $district[$list['district_id']]['district'];?>                                                   
                                                </label>
                                            </div>
                                        </div>
										<div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>面积范围:&nbsp&nbsp</span><?php echo $list['larea'];?>-<?php echo $list['harea'];?>㎡                                                  
                                                </label>
                                            </div>
                                        </div>
										<div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>价格范围:&nbsp&nbsp</span><?php echo $list['lprice'];?>-<?php echo $list['hprice'];?>万元                                                   
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>联系人:&nbsp&nbsp</span><?php echo $list['realname'];?>                                                   
                                                </label>
                                            </div>
                                        </div>
										<div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>手机号:&nbsp&nbsp</span><?php echo $list['phone'];?>                                                   
                                                </label>
                                            </div>
                                        </div>
										<div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>委托状态:&nbsp&nbsp</span><?php switch($list['status']){case 1:echo "<font color='green'>已委托</font>";break;case 2:echo "<font color='red'>已下架</font>";break;}?>                                                   
                                                </label>
                                            </div>
                                        </div>
										<div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>委托时间:&nbsp&nbsp</span><?php echo date("Y-m-d H:i:s",$list['ctime']);?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
											<div class="dataTables_length" id="dataTables-example_length">
												<label><span>状态:&nbsp;&nbsp</span>  <?php switch($list['is_check']){case 1:echo "<font color='black'>待审核</font>";break;case 2:echo "<font color='green'>通过</font>";break;case 3:echo "<font color='red'>驳回</font>";break;}?> 
												</label>
											</div>
										</div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="submit" value="保存" disabled>
                                                <a class="btn btn-primary" href="/seek_rent_review/index">返回</a>
                                            </div>
                                        </div>							  
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

