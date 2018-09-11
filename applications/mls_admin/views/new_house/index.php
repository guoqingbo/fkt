<?php require APPPATH.'views/header.php'; ?>
<link href="<?=MLS_SOURCE_URL ?>/mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<link href="<?=MLS_SOURCE_URL ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<link href="<?=MLS_SOURCE_URL ?>/mls/css/v1.0/autocomplete.css" rel="stylesheet" type="text/css">
<style>
    td{text-align: center}
     th{text-align: center}
</style>
<div id="wrapper">
    <div id="page-wrapper" style="min-height: 337px;">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                </div>
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <div class="table-responsive">
                            <form action="" method="post" name="search_form">
                                <input type="hidden" name="search" value="search">
                                <div style="width:100%" class="col-sm-6">
                                    编号：&nbsp;<input type="text" value="<?php echo $params['id'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="id">
                                    房源名称：&nbsp;<input type="text" value="<?php echo $params['title'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:150px;display: inline-block;"  name="title">
                                    电话：&nbsp;<input type="text" value="<?php echo $params['phone'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:120px;display: inline-block;"  name="phone">
                                    均价：&nbsp;<input type="text" value="<?php echo $params['price'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:80px;display: inline-block;"  name="price">
                                    区域：&nbsp;<select name="district_id" id="district_id">
                                                        <option value="">不限</option>
                                                        <?php foreach($district as $key => $val){?>
                                                        <option value="<?php echo $val['id'];?>" <?php echo $val['id']==$params['district_id']?"selected":""?>><?php echo $val['district'];?></option>
                                                        <?php }?>
                                                    </select>
                                    板块：&nbsp;<select name="street_id" id="street_id">
                                                        <option value="">不限</option>
                                    <script>
                                         $("#district_id").change(function(){
                                             var district_id= $(this).val();
                                             $.ajax({
                                                type: 'get',
                                                url : '<?php echo MLS_ADMIN_URL; ?>/new_house/get_street/',
                                                dataType:'json',
                                                data:{
                                                    district_id:district_id
                                                },
                                                success: function(msg){
                                                    var str = '';
                                                    if(msg.length==0){
                                                        str = '<option value="">不限</option>';
                                                    }else{
                                                        for(var i=0;i<msg.length;i++){
                                                            str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
                                                        }
                                                    }
                                                    $('#street_id').empty();
                                                    $('#street_id').append(str);
                                                }
                                             });
                                         })
                                    </script>
                                                    </select>
                                    物业类型：&nbsp;<select name="type">
                                                        <option value="">不限</option>
                                                        <?php if($house_config){
                                                                  foreach($house_config['sell_type'] as $key =>$val){?>
                                                        <option value="<?php echo $key;?>" <?php echo $key==$params['type']?"selected":""?>><?php echo $val;?></option>
                                                        <?php }}?>
                                                    </select>
                                    装修情况：&nbsp;<select name="renovation">
                                                        <option value="">不限</option>
                                                        <?php if($house_config){
                                                                  foreach($house_config['fitment'] as $key =>$val){?>
                                                        <option value="<?php echo $key;?>" <?php echo $key==$params['renovation']?"selected":""?>><?php echo $val;?></option>
                                                        <?php }}?>
                                                    </select>
                                </div>
                                <div style="width:100%" class="col-sm-6">
                                    主力户型：&nbsp;<select name="room">
                                                        <?php if($house_config){
                                                                  foreach($house_config['room'] as $key =>$val){?>
                                                        <option value="<?php echo $key;?>" <?php echo $key==2?"selected":""?>><?php echo $val;?></option>
                                                        <?php }}?>
                                                    </select>室
                                                    <select name="hall">
                                                        <?php if($house_config){
                                                                  foreach($house_config['hall'] as $key =>$val){?>
                                                        <option value="<?php echo $key;?>" <?php echo $key==2?"selected":""?>><?php echo $val;?></option>
                                                        <?php }}?>
                                                    </select>厅
                                                    <select name="kitchen">
                                                       <?php if($house_config){
                                                                  foreach($house_config['kitchen'] as $key =>$val){?>
                                                        <option value="<?php echo $key;?>"><?php echo $val;?></option>
                                                        <?php }}?>
                                                    </select>厨
                                                    <select name="toilet">
                                                        <?php if($house_config){
                                                                  foreach($house_config['toilet'] as $key =>$val){?>
                                                        <option value="<?php echo $key;?>"><?php echo $val;?></option>
                                                        <?php }}?>
                                                    </select>卫
                                    成交状态：&nbsp;<select name="is_over">
                                                    <option value="">不限</option>
                                                    <option value="2">已成交</option>
                                                    <option value="1" >未成交</option>
                                                </select>
                                    <input type="hidden" name="pg" value="1">
                                    <input class="btn btn-primary" type="submit" value="查询" name="search">&nbsp;&nbsp;&nbsp;
                                    <input class="btn btn-primary" type="button" onclick="javascript:location.href = '/new_house/'" value="重置">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input class="btn btn-primary" type="button" onclick="javascript:location.href = '/new_house/add'" value="录入房源">
                                </div>
                            </form>
                        </div>
                        <table id="dataTables-example" class="table table-striped table-bordered table-hover">
                            <thead>
                                 <tr>
                                    <th style="width:45px">编号</th>
                                    <th style="width:200px">房源名称</th>
                                    <th style="width:100px">电话</th>
                                    <th style="width:50px">区域</th>
                                    <th style="width:50px">板块</th>
                                    <th style="width:65px">物业类型</th>
                                    <th style="width:65px">装修情况</th>
                                    <th style="width:80px">均价(元)</th>
                                    <th style="width:162px">主力户型(室-厅-厨-卫)</th>
                                    <th style="width:65px">出售情况</th>
                                    <th style="width:65px">状态</th>
                                    <th style="width:150px">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($list) && !empty($list)) {
                                            foreach($list as $key =>$val){?>
                                <tr class="gradeA">
                                    <td><?php echo $val['id'];?></td>
                                    <td><?php echo $val['title'];?></td>
                                    <td><?php echo $val['phone'];?></td>
                                    <td><?php echo $val['district_id'];?></td>
                                    <td><?php echo $val['street_id'];?></td>
                                    <td><?php switch ($val['type']){case 1:echo "住宅";break;
                                                              case 2:echo "别墅";break;
                                                              case 3:echo "商铺";break;
                                                              case 4:echo "写字楼";break;
                                                              case 5:echo "厂房";break;
                                                              case 6:echo "仓库";break;
                                                              case 7:echo "车库";break;}?></td>
                                    <td><?php switch ($val['renovation']){case 1:echo "毛坯";break;
                                                              case 2:echo "简装";break;
                                                              case 3:echo "中装";break;
                                                              case 4:echo "精装";break;
                                                              case 5:echo "豪装";break;
                                                              case 6:echo "婚装";break;}?></td>
                                    <td><?php echo $val['price'];?></td>
                                    <td><?php echo $val['apartment'];?></td>
                                    <td><?php switch($val['is_sell']){case 0:echo "未出售";break;case 1:echo "在售中";break;case 2:echo "已售完";break;}?></td>
                                    <td><?php switch ($val['is_over']){case 2:echo "<font color='green'>已成交</font>";break;
                                                                       case 1:echo "<font color='black'>未成交</font>";break;}?></td>
                                    <td><?php switch ($val['is_over']){
                                                case 1:echo"<a href='".MLS_ADMIN_URL."/new_house/info/".$val['id']."'>详情</a>
                                                          | <a href='".MLS_ADMIN_URL."/new_house/index/".$val['id']."/2'>确认成交</a>
                                                          | <a href='".MLS_ADMIN_URL."/new_house/del/".$val['id']."'>删除</a>";break;
                                                case 2:echo"<a href='".MLS_ADMIN_URL."/new_house/info/".$val['id']."'>详情</a>
                                                          | <a href='".MLS_ADMIN_URL."/new_house/del/".$val['id']."'>删除</a>";break;
                                        }?>
                                    </td>
                                </tr>
                                <?php }}else{
                                       echo "<tr class='gradeA'><td colspan=15 style='text-align:center;color:red;font-weight:bold;'>暂无您查询的新房房源数据~！</td></tr>";
                                }?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                         <?php echo page_uri($page, $pages, MLS_ADMIN_URL . '/user/index'); ?>
                                    </ul>
                                </div>
                            </div>
                            <div style="color:blue;position:absolute;right:33px;">
                                <b>共查到<?php echo $sold_num;?>条数据</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

