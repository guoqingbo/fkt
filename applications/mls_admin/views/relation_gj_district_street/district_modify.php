<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><a href='<?php echo MLS_ADMIN_URL;?>/relation_district_street/ganji_district_index' class="btn btn-primary">赶集区属列表</a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href='<?php echo MLS_ADMIN_URL;?>/relation_district_street/ganji_street_index' class="btn btn-primary">赶集板块列表</a></h1>
                </div>
                <div class="col-lg-12">
                    <h1 class="page-header"><?=$title?></h1>
                </div>
            </div>
            <!-- /.row -->
            <?php if(''==$modifyResult){; ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="">
                                    <input type='hidden' name='submit_flag' value='modify'>
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    本地区属名:&nbsp&nbsp&nbsp&nbsp<input type="search" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$modify_district['district_id']?>&nbsp&nbsp&nbsp&nbsp<?=$modify_district['district_name']?>" readonly>
                                                </label>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                <label>
                                                    赶集区属&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<select id="district" name = 'district' class="form-control input-sm" aria-controls="dataTables-example">
                                                        <option value="0">请选择...</option>
                                                        <?php foreach ($district as $value) {?>
                                                            <option value="<?=$value['district_id'];?>&<?=$value['name'];?>"><?=$value['district_id'];?>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?=$value['name'];?></option>
                                                        <?php }?>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        <?php if(!empty($mess_error)){?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <font color='red'><?php echo $mess_error; ?></font>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="submit" value="提交">
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
            <?php }else if(0===$modifyResult){ ?>
            	<div>更新失败</div>
            <?php }else{?>
            	<div>更新成功</div>
            <?php }?>
            <!-- /.row -->
        </div>
    </div>

<?php require APPPATH.'views/footer.php'; ?>

