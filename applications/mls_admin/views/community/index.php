<?php require APPPATH.'views/header.php';date_default_timezone_set("PRC"); ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">楼盘列表</h1>
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
                                <form action="derive" style="display: none" method="post" id="output-form">
                                    <input type="text" name="limit" value="0" id="post-limit">
                                    <input type=" text" name="offset" value="100" id="post-offset">
                                </form>
                                <form name="search_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>设置查询条件
                                                        <select name="condition" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <option value="cmt_name" <?php if((!empty($like_code['cmt_name']))){echo 'selected="selected"';}?>>楼盘名称</option>
                                                            <option value="id" <?php if((!empty($like_code['id']))){echo 'selected="selected"';}?>>楼盘ID</option>
                                                            <option value="address" <?php if((!empty($like_code['address']))){echo 'selected="selected"';}?>>楼盘地址</option>
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
                                                    <!-- <label>物业类型
                                                        <select name="build_type" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <option value="住宅" <?php if((!empty($like_code['build_type']))&&'住宅'==$like_code['build_type']){echo 'selected="selected"';}?>>住宅</option>
                                                            <option value="别墅" <?php if((!empty($like_code['build_type']))&&'别墅'==$like_code['build_type']){echo 'selected="selected"';}?>>别墅</option>
                                                            <option value="写字楼" <?php if((!empty($like_code['build_type']))&&'写字楼'==$like_code['build_type']){echo 'selected="selected"';}?>>写字楼</option>
                                                            <option value="商铺" <?php if((!empty($like_code['build_type']))&&'商铺'==$like_code['build_type']){echo 'selected="selected"';}?>>商铺</option>
                                                            <option value="厂房仓库" <?php if((!empty($like_code['build_type']))&&'厂房仓库'==$like_code['build_type']){echo 'selected="selected"';}?>>厂房仓库</option>

                                                        </select>
                                                    </label> -->
                                                    <label>楼盘状态
                                                        <select name="status" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <?php foreach($status as $k => $v){?>
                                                            <option value="<?php echo $k;?>" <?php if((!empty($where_cond['status']))&&$k==$where_cond['status']){echo 'selected="selected"';}?>><?php echo $v;?></option>
                                                            <?php }?>
                                                        </select>
                                                    </label>
													<label>楼盘类型
                                                        <select name="type" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <option value="1" <?php if((!empty($like_code['type']))&&$like_code['type'] == 1){echo 'selected="selected"';}?>>住宅</option>
                                                            <option value="2" <?php if((!empty($like_code['type']))&&$like_code['type'] == 2){echo 'selected="selected"';}?>>别墅</option>
                                                            <option value="3" <?php if((!empty($like_code['type']))&&$like_code['type'] == 3){echo 'selected="selected"';}?>>商铺</option>
                                                            <option value="4" <?php if((!empty($like_code['type']))&&$like_code['type'] == 4){echo 'selected="selected"';}?>>写字楼</option>
															<option value="5" <?php if((!empty($like_code['type']))&&$like_code['type'] == 5){echo 'selected="selected"';}?>>厂房</option>
															<option value="6" <?php if((!empty($like_code['type']))&&$like_code['type'] == 6){echo 'selected="selected"';}?>>仓库</option>
															<option value="7" <?php if((!empty($like_code['type']))&&$like_code['type'] == 7){echo 'selected="selected"';}?>>车库</option>
                                                        </select>
                                                    </label>
                                                    <label>是否热门
                                                        <select name="is_hot" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <?php foreach($is_hot as $k => $v){?>
                                                                <option value="<?php echo $k;?>" <?php if((!empty($where_cond['is_hot']))&&$k==$where_cond['is_hot']){echo 'selected="selected"';}?>><?php echo $v;?></option>
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
                                                        <a class="btn btn-primary" href='add'>添加</a>
                                                        <a class="btn btn-primary" onclick="addHouse();">导入</a>
                                                        <a class="btn btn-primary" onclick="outPutE();">导出</a>
                                                        <script>
                                                            function nextexport()
                                                            {
                                                                var mylimit = parseInt($('#mylimit').val());
                                                                var myoffset = parseInt($('#myoffset').val());

                                                                mylimit = myoffset + mylimit;

                                                                $('#mylimit').val(mylimit);
                                                            }
                                                            function outPutE() {
                                                                $('#post-limit').val($('#mylimit').val());
                                                                $('#post-offset').val($('#myoffset').val());
                                                                $("#output-form").submit();
                                                            }
                                                            function addHouse()
                                                            {
                                                                layer.open({
                                                                    type: 2,
                                                                    title: '添加楼盘',
                                                                    shadeClose: false,
                                                                    maxmin: true, //开启最大化最小化按钮
                                                                    area: ['425px', '320px'],
                                                                    content: '/community/import'
                                                                    /*
                                                                    end: function(){
                                                                        $("#search_form").submit();
                                                                    }
                                                                    */
                                                                });
                                                            }
                                                        </script>
                                                        <span style="float:right;">
                                                                从第
                                                                <input type="text"  class="form-control input-sm" id="mylimit" name="mylimit" value="0" />
                                                                条房源开始导出，每次导出
                                                                <input type="text" class="form-control input-sm" id="myoffset" name="myoffset" value="100" />
                                                                条
                                                                <a href="###" onclick="nextexport();">
                                                                    下一组
                                                                </a>
                                                            </span>
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
                                            <th style="width:68px;">楼盘类型</th>

                                            <th style="width:42px;">区属</th>
                                            <th style="width:42px;">板块</th>
                                            <th style="width:68px;">楼盘地址</th>
                                            <!-- <th style="width:68px;">物业类型</th> -->
                                            <th style="width:68px;">楼盘坐标</th>
                                            <th style="width:78px;">楼栋号</th>
                                            <th style="width:68px;">建筑面积</th>
                                            <th style="width:55px;">总套数</th>
                                            <th style="width:68px;">楼盘状态</th>
                                            <th style="width:55px;">户型图</th>
                                            <th style="width:55px;">外景图</th>
                                            <th style="width:45px;">更新时间</th>
                                            <th style="width:90px;">操作</th>
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
                                            <td><?php echo $value['alias'];?></td>
                                            <td>
											<?php
											if($value['type']==1){
												echo "住宅";
											}elseif($value['type']==2){
												echo "别墅";
											}elseif($value['type']==3){
												echo "商铺";
											}else{
												echo "写字楼";
											}
											?>
											</td>
                                            <td><?php echo $value['dist_name'];?></td>
                                            <td><?php echo $value['street_name'];?></td>
                                            <td><?php echo $value['address'];?></td>
                                            <!-- <td><?php echo $value['build_type'];?></td> -->
                                            <td><?php echo $value['b_map_x'] !='0' && $value['b_map_y'] !='0'?'有':'无';?></td>
                                            <td>
                                                <?php if('37'==$city_id){ ?>
                                                    <a href="<?php echo MLS_ADMIN_URL;?>/community/add_dong_unit_door/<?php echo $value['id'];?>" class="btn btn-primary btn-xs">添加楼栋号</a>
                                                <?php }else{ ?>
                                                    <?php if(empty($value['floor'])){?>
                                                    <a href="<?php echo MLS_ADMIN_URL;?>/cmtfloor/add_floor/<?php echo $value['id'];?>" class="btn btn-primary btn-xs">添加楼栋号</a>
                                                    <?php }else{?>
                                                    <a href="<?php echo MLS_ADMIN_URL;?>/cmtfloor/floor_detail/<?php echo $value['id'];?>" ><?php echo $value['floor'];?></a>
                                                    <?php }?>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $value['buildarea'];?></td>
                                            <td><?php echo $value['total_room'];?></td>
                                            <td><?php echo $value['status'];?></td>
                                            <td><?php echo $value['image_data']['apart_img_num'];?></td>
                                            <td><?php echo $value['image_data']['Location_img_num'];?></td>
                                            <td><?php echo ($value['updatetime']==0)?'未更新':date('Y-m-d',$value['updatetime']);?></td>
                                            <td>
                                                <a href="<?php echo MLS_ADMIN_URL;?>/community/modify/<?php echo $value['id'];?>" class="btn btn-primary btn-xs" target="_blank">修改</a>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/cmtimage/cmt_pic_manage_list/<?php echo $value['id'];?>" class="btn btn-primary btn-xs">图库</a>
                                                <?php if($value['status']=='正式小区'){ ?>
                                                    <?php if( empty($value['is_hot']) ){ ?>
                                                        <a class="btn btn-primary btn-xs is_hot" commid = '<?php echo $value['id']; ?>'>设为热门</a>
                                                    <?php }else{ ?>
                                                        <a class="btn btn-danger btn-xs is_hot" commid = '<?php echo $value['id']; ?>'>取消热门</a>
                                                    <?php } ?>
                                                <?php } ?>
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
                                           <li>
                                               &nbsp;&nbsp;&nbsp;跳转到<input type="text" id="to_page" value="" style="width: 30px;"/>页 <input type="button" id="to_page_button" value="确定" onclick="search_form.pg.value=$('#to_page').val();search_form.submit();return false;" />
                                           </li>
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
<script>
//function del(cmt_id){
//    var is_del = confirm('确定删除该楼盘');
//    if(is_del){
//        window.location.href = '<?php //echo MLS_ADMIN_URL;?>///community/del/'+cmt_id;
//    }
//}

//function modify(cmt_id){
//    window.location.href = '<?php echo MLS_ADMIN_URL;?>/community/modify/'+cmt_id;
//}

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

    //设置热门小区状态
    $('.is_hot').click(function () {
        var commid = $(this).attr('commid');
        $.ajax({
            type: 'POST',
            url : '<?php echo MLS_ADMIN_URL;?>/community/change_community_hot/'+commid,
            dataType:'json',
            success: function(msg){
                switch(msg)
                {
                    case 2:
                        layer.msg("热门小区最多只能三个！");
                        break;
                    case 0:
                        $(this).removeClass('btn-danger');
                        $(this).addClass('btn-primary');
                        layer.msg("取消成功");
                        location.href = '<?=MLS_ADMIN_URL?>/community/';
                        break;
                    case 1:
                        $(this).removeClass('btn-primary');
                        $(this).addClass('btn-danger');
                        layer.msg("设置成功");
                        location.href = '<?=MLS_ADMIN_URL?>/community/';
                        break;

                }
            }
        });
    });
});
</script>
<?php require APPPATH.'views/footer.php'; ?>

