<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<style>
	#l-map{height:400px;width:600px;float:left;border:1px solid #bcbcbc;}
	#r-result{height:400px;width:230px;float:right;}
</style>
<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">个人房源举报审核</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if('modify'==$submit_flag){ ?>
            <h3><?php echo $result_text; ?></h3>
            <br>
            <input class="btn btn-primary" onclick="goback()" value="返回" type="button">
            <?php }else{ ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="">
                                        <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                            <div class="row">
                                                <div class="col-sm-6" style="width:100%">
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                    </div>
                                                </div>
                                            </div>
                                            <input type='hidden' name='submit_flag' value='modify'/>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        <h4>被举报房源:</h4>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <?php if(is_full_array($house_info)){ ?>
                                                        <label>
                                                            楼盘名称:&nbsp&nbsp&nbsp&nbsp <?php echo $house_info['house_name'];?>
                                                        </label>
                                                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <label>
                                                            面积:&nbsp&nbsp&nbsp <?php echo $house_info['buildarea'];?>㎡
                                                        </label>
                                                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <label>
                                                            价格:&nbsp&nbsp&nbsp&nbsp <?php echo $house_info['price'];?>万
                                                        </label>
                                                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <label>
                                                            来源:&nbsp&nbsp&nbsp <?php echo $house_info['source_from_str'];?>
                                                        </label>
                                                        <br>
                                                        <label>
                                                            业主姓名:&nbsp&nbsp&nbsp&nbsp <?php echo $house_info['owner'];?>
                                                        </label>
                                                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <label>
                                                            业主电话:&nbsp&nbsp&nbsp <?php echo $house_info['telno1'];?>
                                                        </label>
                                                    <?php }else{ ?>
                                                        <label>
                                                            暂无信息
                                                        </label>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        <h4>举报人信息:</h4>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        用户手机号:&nbsp&nbsp&nbsp&nbsp <?php echo $broker_info['phone'];?>
                                                    </label>
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <label>
                                                        举报时间:&nbsp&nbsp&nbsp <?php echo date('Y-m-d H:i:s' , $reportlist_details['r_addtime']);?>
                                                    </label>
                                                    <br>
                                                    <label>
                                                        举报理由:&nbsp&nbsp&nbsp&nbsp <?php echo $reportlist_details['r_reason'];?>
                                                    </label>
                                                </div>
                                            </div>
                                            <?php if('3'==$reportlist_details['r_status']){ ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        <h4>处理:</h4>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        <label><input name="r_status" type="radio" value="2"/>该号码加入黑名单（该号码下所有房源将被下架）</label><br>
                                                        <label><input name="r_status" type="radio" value="4"/>下架该房源</label><br>
                                                        <label><input name="r_status" type="radio" value="1" checked="checked" />举报不属实，驳回</label>
                                                    </label>
                                                    <br><br>
                                                    <label>
                                                        备注:&nbsp&nbsp&nbsp&nbsp<br>
                                                        <textarea rows="3" cols="20" name="r_comment"></textarea>
                                                    </label>
                                                </div>
                                            </div>
                                            <?php } ?>
											<div class="col-sm-6" style="width:100%">
									</div>
                                        </div>
								</div>
                               </div>
                               </div>
                              </div>
                        <!-- /.panel-body -->
                            <?php if('3'==$reportlist_details['r_status']){ ?>
                                <input class="btn btn-primary" value="提交" type="submit">
                            <?php } ?>
                            <input class="btn btn-primary" onclick="goback()" value="返回" type="button">
                        </form>
                    </div>
                    <!-- /.panel -->
            <?php } ?>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->



        </div>
        <!-- /#page-wrapper -->

    </div>
				 <div class="col-lg-4" style="display:none" id="js_note1">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            提示框
							<button type="button" class="close JS_Close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="panel-body">
                            <p id="warning_text"></p>
                        </div>
                    </div>
                </div>
<script>
function goback(){
    window.location.href = "<?php echo MLS_ADMIN_URL . '/blacklist/reportlist/'; ?>";
}

function close_window()
{
    var userAgent = navigator.userAgent;
    if (userAgent.indexOf("Firefox") != -1 || userAgent.indexOf("Presto") != -1) {
        window.location.replace("about:blank");
    } else {
        window.opener = null;
        window.open("", "_self");
        window.close();
    }
}
$(function(){
    $('#district').change(function(){
        var districtID = $(this).val();
        $.ajax({
            type: 'get',
            url : '<?php echo MLS_ADMIN_URL; ?>/community/find_street_bydis/'+districtID,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg.result=='no result'){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
                    }
                }
                $('#street').empty();
                $('#street').append(str);
            }
        });
    });

});
</script>
<script src="<?php echo MLS_SOURCE_URL;?>/common/third/My97DatePicker/WdatePicker.js"></script>
<?php require APPPATH.'views/footer.php'; ?>
