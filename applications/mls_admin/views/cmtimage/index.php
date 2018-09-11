<?php require APPPATH.'views/header.php';date_default_timezone_set("PRC"); ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">楼盘图片</h1>
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
                                <form name="search_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>设置查询条件
                                                        <select name="condition" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <option value="cmt_name" <?php if((!empty($like_code['cmt_name']))){echo 'selected="selected"';}?>>小区名称</option>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        包含<input type='search' class="form-control input-sm" aria-controls="dataTables-example" size='12' name='strcode' value="<?php if((!empty($like_code['cmt_name']))){echo $like_code['cmt_name'];}?>"/>
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
                                                    <label>小区类型
                                                        <select name="build_type" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <option value="住宅" <?php if((!empty($like_code['build_type']))&&'住宅'==$like_code['build_type']){echo 'selected="selected"';}?>>住宅</option>
                                                            <option value="别墅" <?php if((!empty($like_code['build_type']))&&'别墅'==$like_code['build_type']){echo 'selected="selected"';}?>>别墅</option>
                                                            <option value="写字楼" <?php if((!empty($like_code['build_type']))&&'写字楼'==$like_code['build_type']){echo 'selected="selected"';}?>>写字楼</option>
                                                            <option value="商铺" <?php if((!empty($like_code['build_type']))&&'商铺'==$like_code['build_type']){echo 'selected="selected"';}?>>商铺</option>
                                                            <option value="厂房仓库" <?php if((!empty($like_code['build_type']))&&'厂房仓库'==$like_code['build_type']){echo 'selected="selected"';}?>>厂房仓库</option>

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
                                            <th>楼盘ID</th>
                                            <th>楼盘名称</th>
                                            <th>区属</th>
                                            <th>图片总数</th>
                                            <th>户型图</th>
                                            <th>外景图</th>
                                            <th>更新时间</th>
                                            <th>审核人</th>
                                            <th>操作</th>
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
                                            <td><?php echo $value['image_data']['img_num'];?></td>
                                            <td><?php echo $value['image_data']['apart_img_num'];?></td>
                                            <td><?php echo $value['image_data']['Location_img_num'];?></td>
                                            <td><?php echo date('Y-m-d',$value['updatetime']);?></td>
                                            <td><?php echo '审核人';?></td>
                                            <td>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/cmtimage/cmt_pic_manage_list/<?php echo $value['id'];?>" >查看该小区图库</a>
                                            </td>
                                        </tr>
                                    <?php }}?>


                                    </tbody>
                                </table>

                                <div class="row">
                                  <div class="col-sm-6" style='display:none;'>
                                   <div class="dataTables_info" id="dataTables-example_info" role="alert" aria-live="polite" aria-relevant="all"><input type="checkbox" id="sel-all">&nbsp;&nbsp;全选 &nbsp;&nbsp;<a href="javascript:void(0)"  data-target="#myModal1" data-toggle="modal">加入白名单</a> &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal" data-toggle="modal" >标记到推送库</a> &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal2" data-toggle="modal">标记到备选库</a>
                                   </div>
                                  </div>
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

});
</script>
<?php require APPPATH.'views/footer.php'; ?>

