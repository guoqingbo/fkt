<?php require APPPATH.'views/header.php';date_default_timezone_set("PRC"); ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">楼盘审核</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="" id="search_form">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>设置查询条件
                                                        <select name="condition" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="">请选择</option>
                                                            <option value="cmt_name" <?php if((!empty($like_code['cmt_name']))){echo 'selected="selected"';}?>>楼盘名称</option>
                                                            <option value="address" <?php if((!empty($like_code['address']))){echo 'selected="selected"';}?>>楼盘地址</option>
                                                            <option value="add_broker_name" <?php if((!empty($like_code['add_broker_name']))){echo 'selected="selected"';}?>>经纪人名称</option>
                                                            <option value="add_broker_phone" <?php if((!empty($like_code['add_broker_phone']))){echo 'selected="selected"';}?>>经纪人电话</option>
                                                            <option value="add_agency_name" <?php if((!empty($like_code['add_agency_name']))){echo 'selected="selected"';}?>>门店名称</option>
                                                            <option value="add_company_name" <?php if((!empty($like_code['add_company_name']))){echo 'selected="selected"';}?>>公司名称</option>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        包含<input type='search' class="form-control input-sm" aria-controls="dataTables-example" size='12' name='strcode' value="<?php if((!empty($strcode))){echo $strcode;}?>"/>
                                                    </label>
                                                    <label>区属
                                                        <select id="district" name="district" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <?php foreach($district as $k=>$v){ ?>
                                                            <option value="<?php echo $v['id']?>" <?php if((!empty($where_cond['dist_id']))&&$v['id']==$where_cond['dist_id']){echo 'selected="selected"';}?>><?php echo $v['district']?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </label>
                                                    <label>板块
                                                        <select id="street" name="street" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <?php if(!empty($street_arr)){
                                                                    foreach($street_arr as $k=>$v){
                                                            ?>
                                                            <option value="<?php echo $v['id']?>" <?php if((!empty($where_cond['streetid']))&&$v['id']==$where_cond['streetid']){echo 'selected="selected"';}?>><?php echo $v['streetname']?></option>
                                                            <?php }} ?>
                                                        </select>
                                                    </label>
                                                    <label>楼盘状态
                                                        <select name="status" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <?php foreach($status as $k => $v){?>
                                                            <option value="<?php echo $k;?>" <?php if((!empty($where_cond['status']))&&$where_cond['status'] == $k){echo 'selected="selected"';}?>><?php echo $v;?></option>
                                                            <?php }?>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            <input type="hidden" name="pg" value="1">
                                                            <input class="btn btn-primary" type="submit" value="查询">
                                                        </div>
                                                    </label>
                                                    <label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            <input class="btn btn-primary" onclick="$('#search_form').attr('action','/community/export/');" type="submit" value="导出">
                                                        </div>
                                                    </label>
                                                    <label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            <input class="btn btn-primary" type="button" value="重置" onclick="window.location.href = window.location;">
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                </form>
                            </div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="width:68px;">楼盘ID</th>
                                            <th style="width:68px;">楼盘名称</th>
                                            <th style="width:42px;">区属</th>
                                            <th style="width:42px;">板块</th>
                                            <th style="width:68px;">楼盘地址</th>
                                            <th style="width:68px;">楼盘状态</th>
                                            <th style="width:68px;">经纪人</th>
                                            <th style="width:68px;">经纪人电话</th>
                                            <th style="width:68px;">经纪人门店</th>
                                            <th style="width:68px;">经纪人公司</th>
                                            <th style="width:42px;">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(isset($community2) && !empty($community2)){
                                            foreach($community2 as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>
                                            <td><?php echo $value['cmt_name'];?></td>
                                            <td><?php echo $value['dist_name'];?></td>
                                            <td><?php echo $value['street_name'];?></td>
                                            <td><?php echo $value['address'];?></td>
                                            <td><?php echo $status[$value['status']];?></td>
                                            <td><?php echo $value['add_broker_name'] ?: '';?></td>
                                            <td><?php echo $value['add_broker_phone'] ?: '';?></td>
                                            <td><?php echo $value['add_agency_name'] ?: '';?></td>
                                            <td><?php echo $value['add_company_name'] ?: '';?></td>
                                            <td>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/community/check/<?php echo $value['id'];?>" ><?php echo ('3'==$value['status'])?'审核':'查看';?></a>
                                            </td>
                                        </tr>
                                    <?php }}?>


                                    </tbody>
                                </table>

                                <div class="row">
                                  <div class="col-sm-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

                                       <ul class="pagination" style="margin:-8px 0;padding-left:20px">
									   										<?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/user/index');?>
									   									 </ul>
                                    </div>
                                  </div>
                                    <div style="color:blue;position:absolute;right:33px;">
                                        <b>共查到<?php echo $user_num;?>条数据</b>
                                    </div>
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
            <!-- /.row -->



        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade" style="display: none;">
        <div class="modal-dialog"  style="margin:200px auto">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h4 id="myModalLabel" class="modal-title">标记到推送库</h4>
                </div>
                <div class="modal-body">确定加入推送库？
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                    <button class="btn btn-primary" type="button" id="addpush">添加推送</button>
                </div>
            </div>
        </div>
    </div>
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal1" class="modal fade" style="display: none;">
        <div class="modal-dialog"  style="margin:200px auto">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h4 id="myModalLabel" class="modal-title">白名单</h4>
                </div>
                <div class="modal-body">白名单原因:
                    <select class="input-sm" aria-controls="dataTables-example" id="kind" name="kind">
                        <option value="1">公司内部人士</option>
                        <option value="2">经纪人</option>
                    </select>
                    备注
                    <input type="search" name="remark" id="remark" value="">
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                    <button class="btn btn-primary" type="button" id="addwhite">确定</button>
                </div>
            </div>
        </div>
    </div>
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal2" class="modal fade" style="display: none;">
        <div class="modal-dialog"  style="margin:200px auto">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h4 id="myModalLabel" class="modal-title">备选库</h4>
                </div>
                <div class="modal-body">确定加入备选库？

                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                    <button class="btn btn-primary" type="button" id="addalternatives">确定</button>
                </div>
            </div>
        </div>
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

function del(cmt_id){
    var is_del = confirm('确定删除该楼盘');
    if(is_del){
        window.location.href = '<?php echo MLS_ADMIN_URL;?>/community/del/'+cmt_id;
    }
}

$(function(){
    //区属、板块二级联动
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
<?php require APPPATH.'views/footer.php'; ?>

