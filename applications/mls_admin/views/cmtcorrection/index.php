<?php require APPPATH.'views/header.php';date_default_timezone_set("PRC"); ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">楼盘纠错信息</h1>
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
                                <form name="search_form" method="post" action="" style="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>设置查询条件
                                                        <select name="condition" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <option value="cmt_name" <?php if((!empty($like_code['cmt_name']))){echo 'selected="selected"';}?>>楼盘名称</option>
                                                            <option value="cmt_id" <?php if((!empty($like_code['cmt_id']))){echo 'selected="selected"';}?>>楼盘ID</option>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        包含<input type='search' class="form-control input-sm" aria-controls="dataTables-example" size='12' name='strcode' value="<?php if((!empty($like_code[$condition]))){echo $like_code[$condition];}?>"/>
                                                    </label>
                                                    <label>楼盘状态
                                                        <select id="esta" name="esta" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="">请选择</option>
                                                            <option value="0" <?php if(isset($where_cond['esta'])&&$where_cond['esta']==0){echo 'selected="selected"';}?>>待审核</option>
                                                            <option value="2" <?php if(!empty($where_cond['esta'])&&$where_cond['esta']==2){echo 'selected="selected"';}?>>未通过</option>
                                                            <option value="1" <?php if(!empty($where_cond['esta'])&&$where_cond['esta']==1){echo 'selected="selected"';}?>>通过</option>
                                                        </select>
                                                    </label>
                                                    <label>提交时间
                                                        <select name="creattime" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <option value="1" <?php if((!empty($where_cond['creattime']))&&'1'==$where_cond['creattime']){echo 'selected="selected"';}?>>最近1日内提交</option>
                                                            <option value="3" <?php if((!empty($where_cond['creattime']))&&'3'==$where_cond['creattime']){echo 'selected="selected"';}?>>最近3日内提交</option>
                                                            <option value="7" <?php if((!empty($where_cond['creattime']))&&'7'==$where_cond['creattime']){echo 'selected="selected"';}?>>最近7日内提交</option>

                                                        </select>
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
                                </form>
                            </div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="width:68px;">楼盘ID</th>
                                            <th style="width:68px;">楼盘名称</th>
                                            <th style="width:68px;">楼盘别名</th>
                                            <th style="width:68px;">纠错用户</th>
                                            <th style="width:68px;">纠错类目</th>
                                            <th style="width:55px;">原内容</th>
                                            <th style="width:55px;">新内容</th>
                                            <th style="width:68px;">提交时间</th>
                                            <th style="width:42px;">状态</th>
                                            <th style="width:42px;">通过</th>
                                            <th style="width:55px;">不通过</th>
                                            <th style="width:42px;">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(isset($cmt_correction2) && !empty($cmt_correction2)){
                                            foreach($cmt_correction2 as $key=>$value){
                                    ?>
                                        <tr class="gradeA" id="corr<?php echo $value['id'];?>">
                                            <td><?php echo $value['cmt_id'];?></td>
                                            <td><?php echo $value['cmt_name'];?></td>
                                            <td><?php echo !empty($value['alias'])?$value['alias']:'';?></td>
                                            <td><?php echo $value['user_name'];?></td>
                                            <td><?php echo $value['correction_feild_name'];?></td>
                                            <td><?php echo !empty($value['org_info'])?$value['org_info']:'';?></td>
                                            <td><?php echo $value['correctioninfo'];?></td>
                                            <td><?php echo date('Y-m-d',$value['creattime']);?></td>
                                            <td><?php echo !empty($value['esta_str'])?$value['esta_str']:'';?></td>
                                            <td><input type="radio" value="1" class="pass" name="check_esta<?php echo $value['id'];?>"></td>
                                            <td><input type="radio" value="2" class="pass" name="check_esta<?php echo $value['id'];?>" checked="checked"></td>
                                            <td>
                                                <a href="#" id="corr_submit<?php echo $value['id']?>">提交</a>
                                                <input type="hidden" name="id" value="<?php echo $value['id']?>"/>
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
                                    <div style="color:blue;position:absolute;right:33px;"><b>共查到<?php echo $corr_num;?>条数据</b></div>
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
<script>
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

$("a[id^='corr_submit']").click(function(){
    var id = $(this).next().val();//纠错信息id
    var check_esta = $('input[name="check_esta'+id+'"]:checked').val();//状态操作（是否通过）

    var correct_data = {
        'id':id,
        'check_esta':check_esta
    }
    var isSubmit = confirm('您确定要提交本条信息？');
    if(isSubmit){
        $.ajax({
            type: 'get',
            url : 'submit_action',
            data: correct_data,
            success: function(msg){
                if('submitSuccess'==msg){
                    alert('提交成功');
                }else{
                    alert('提交失败');
                }
            }
        });
    }

});

});
</script>
<?php require APPPATH.'views/footer.php'; ?>

