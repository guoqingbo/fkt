<?php require APPPATH . 'views/header.php'; ?>
<style>
    span{text-align: right;display: inline-block;width:60px}
</style>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/jquery-ui-1.9.2.custom.min.js"></script>
<link href="<?=MLS_SOURCE_URL ?>/mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<link href="<?=MLS_SOURCE_URL ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<link href="<?=MLS_SOURCE_URL ?>/mls/css/v1.0/autocomplete.css" rel="stylesheet" type="text/css">
<style>
    tr {text-align:center;}

    .ui-menu {background: none repeat scroll 0 0 #fff;border: 1px solid #d1d1d1;float: left; border-top:none;list-style: none;margin: 0;padding: 0;}
    .ui-menu .ui-menu-item {list-style: none;background: none repeat scroll 0 0 #fff;clear: left;float: left;margin: 0;padding: 0;width: 100%;}
    .ui-menu .ui-menu-item a {color: #333;cursor: pointer;display: block;font-family: Arial,Helvetica,sans-serif;height: 24px;line-height: 24px;overflow: hidden;padding: 0 4px;text-align: left;text-decoration: none;}
    .ui-menu .ui-menu-item a.ui-state-hover, .ui-menu .ui-menu-item a.ui-state-active {background: none repeat scroll 0 0 #ff9804;color: #fff;font-weight: normal;text-decoration: none;}


</style>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$title?></h1>
            </div>
        </div>
        <?php if ($result == '') { ?>
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
                                                    <span>委托编号:&nbsp&nbsp</span><?php echo $list['id'];?>
                                                    <input type="hidden" name="id" value="<?php echo $list['id'];?>">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>小区:&nbsp&nbsp</span><?php echo $list['comt_name'];?>
                                                </label>
                                            </div>
                                        </div>
										<div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>面积&nbsp&nbsp</span><input value="<?php echo $list['area'];?>"  class="input_text input_text_r w150 form-control input-sm" style="width:180px;display: inline-block;height:30px; line-height: 30px" type="text" placeholder="输入汉字筛选" name='area'>
                                                </label>
                                            </div>
                                        </div>
										<div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>期望售价:&nbsp&nbsp</span><input value="<?php echo $list['hprice'];?>"  class="input_text input_text_r w150 form-control input-sm" style="width:180px;display: inline-block;height:30px; line-height: 30px" type="text" placeholder="输入汉字筛选" name='hprice'>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>联系人:&nbsp&nbsp</span><input value="<?php echo $list['realname'];?>"  class="input_text input_text_r w150 form-control input-sm" style="width:180px;display: inline-block;height:30px; line-height: 30px" type="text" placeholder="输入汉字筛选" name='realname'>
                                                </label>
                                            </div>
                                        </div>
										<div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>手机号:&nbsp&nbsp</span><input value="<?php echo $list['phone'];?>"  class="input_text input_text_r w150 form-control input-sm" style="width:180px;display: inline-block;height:30px; line-height: 30px" type="text" placeholder="输入汉字筛选" name='phone'>
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
                                                <label>
                                                    <span>对应楼盘:&nbsp&nbsp</span><input type="text" name="block_name" id="block_name" style="width:183px" class="form-control input-sm" aria-controls="dataTables-example" value="<?php
													if (isset($list['comt_name'])) {
														echo $list['comt_name'];
													}
													?>" ><span id='reminder3' style='font-weight:bold;color:red;'></span>
													<input name="block_id" id="block_id" value="<?php
														if (isset($list['comt_id'])) {
															echo $list['comt_id'];
														}
														?>" type="hidden">
                                                </label>
                                            </div>
                                        </div>
										<script type="text/javascript">
                                            $(function() {
                                                $("#block_name").autocomplete({
                                                    source: function(request, response) {
                                                        var term = request.term;
                                                        $("#block_id").val("");
                                                        $.ajax({
                                                            url: "/community/get_cmtinfo_by_kw/",
                                                            type: "GET",
                                                            dataType: "json",
                                                            data: {
                                                                keyword: term
                                                            },
                                                            success: function(data) {
                                                                //判断返回数据是否为空，不为空返回数据。
                                                                if (data[0]['id'] != '0') {
                                                                    response(data);
                                                                } else {
                                                                    response(data);
                                                                }
                                                            }
                                                        });
                                                    },
                                                    minLength: 1,
                                                    removeinput: 0,
                                                    select: function(event, ui) {
                                                        if (ui.item.id > 0) {
                                                            var blockname = ui.item.label;
                                                            var id = ui.item.id;
                                                            var streetid = ui.item.streetid;
                                                            var streetname = ui.item.streetname;
                                                            var dist_id = ui.item.dist_id;
                                                            var districtname = ui.item.districtname;
                                                            var address = ui.item.address;

                                                            //操作
                                                            $("#block_id").val(id);
                                                            $("#block_name").val(blockname);
                                                            removeinput = 2;
                                                        } else {
                                                            openWin('js_pop_add_new_block');
                                                            removeinput = 1;
                                                        }
                                                    },
                                                    close: function(event) {
                                                        if (typeof (removeinput) == 'undefined' || removeinput == 1) {
                                                            $("#block_name").val("");
                                                            $("#block_id").val("");
                                                        }
                                                    }
                                                });
                                            });
                                        </script>

                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input type="hidden" name="status" value="1">
                                                <input class="btn btn-primary" type="submit" value="保存">
                                                <a class="btn btn-primary" href="/entrust_sell/index">返回</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        <?php } else if (0 === $result) { ?>
            <div><h1><b>添加失败</b><h1></div>
        <?php } else { ?>
            <div><h1><b>添加成功</b><h1></div>
        <?php } ?>
    </div>
</div>
<?php if($result!==""){?>
<script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/entrust_sell/index/'; ?>";
            }, 1000);
        });
</script>
<?php }?>
