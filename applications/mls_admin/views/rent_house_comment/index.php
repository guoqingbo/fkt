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
                                <div style="width:100%" class="col-sm-6">
                                    编号：&nbsp;<input type="text" value="" aria-controls="dataTables-example" class="form-control input-sm " style="width:120px;display: inline-block;"  name="id">
                                    用户名称：&nbsp;<input type="text" value="" aria-controls="dataTables-example" class="form-control input-sm " style="width:120px;display: inline-block;"  name="nickname">
                                    房源编号：&nbsp;<input type="text" value="" aria-controls="dataTables-example" class="form-control input-sm " style="width:120px;display: inline-block;"  name="hid">
                                    评论来源：&nbsp;<select name="is_from">
                                                        <option value="">不限</option>
                                                        <option value="1">pc</option>
                                                        <option value="2">手机</option>
                                                    </select>
                                    评分星级：&nbsp;<select name="score">
                                                        <option value="">不限</option>
                                                        <option value="1">一星</option>
                                                        <option value="2">二星</option>
                                                        <option value="3">三星</option>
                                                        <option value="4">四星</option>
                                                        <option value="5">五星</option>
                                                    </select>
                                    状态：&nbsp;<select name="status">
                                                    <option value="">不限</option>
                                                    <option value="0">未审核</option>
                                                    <option value="1">审核通过</option>
                                                    <option value="2">审核不通过</option>
                                                </select>
                                    <input type="hidden" name="pg" value="1">
                                    <input class="btn btn-primary" type="submit" value="查询" name="search">&nbsp;&nbsp;&nbsp;<input class="btn btn-primary" type="button" onclick="javascript:location.href = '/sell_house_comment/'" value="重置">
                                </div>
                            </form>
                        </div>
                        <table id="dataTables-example" class="table table-striped table-bordered table-hover">
                            <thead>
                                 <tr>
                                    <th style="width:45px">编号</th>
                                    <th style="width:75px">用户昵称</th>
                                    <th style="width:75px">房源编号</th>
                                    <th style="width:450px;">评论内容</th>
                                    <th style="width:95px">评论时间</th>
                                    <th style="width:75px">评论来源</th>
                                    <th style="width:180px">评分星级</th>
                                    <th style="width:90px">审核状态</th>
                                    <th style="width:200px;text-align:center">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($list) && !empty($list)) {
                                            foreach($list as $key =>$val){?>
                                <tr class="gradeA">
                                    <td><?php echo $val['id'];?></td>
                                    <td><?php echo $val['nickname'];?></td>
                                    <td><?php echo $val['hid'];?></td>
                                    <td><?php echo $val['content'];?></td>
                                    <td><?php echo date('Y-m-d h:i:s',$val['ctime']);?></td>
                                    <td><?php switch($val['is_from']){case 1:echo "pc";break;case 2:echo "手机";break;}?></td>
                                    <td><?php switch($val['score']){case 5:echo "<img src='".MLS_SOURCE_URL."/mls_admin/images/5-55.png'>";break;
                                                                     case 4:echo "<img src='".MLS_SOURCE_URL."/mls_admin/images/5-44.png'>";break;
                                                                     case 3:echo "<img src='".MLS_SOURCE_URL."/mls_admin/images/5-33.png'>";break;
                                                                     case 2:echo "<img src='".MLS_SOURCE_URL."/mls_admin/images/5-22.png'>";break;
                                                                     case 1:echo "<img src='".MLS_SOURCE_URL."/mls_admin/images/5-11.png'>";break;
                                    }?></td>
                                    <td><?php switch ($val['status']){case 0:echo "<font>未审核</font>";break;
                                                                       case 1:echo "<font color='green'>审核通过</font>";break;
                                                                       case 2:echo "<font color='red'>审核不通过</font>";break;}?></td>
                                    <td><?php switch ($val['status']){
                                                case 0:echo"<a href='".MLS_ADMIN_URL."/sell_house_comment/index/".$val['id']."/1'>审核通过</a> | 
                                                            <a href='".MLS_ADMIN_URL."/sell_house_comment/index/".$val['id']."/2'>审核不通过</a> | 
                                                            <a href='".MLS_ADMIN_URL."/sell_house_comment/del/".$val['id']."'>删除</a>";break;
                                                case 1:echo"<a href='".MLS_ADMIN_URL."/sell_house_comment/del/".$val['id']."'>删除</a>";break;
                                                case 2:echo"<a href='".MLS_ADMIN_URL."/sell_house_comment/del/".$val['id']."'>删除</a>";break;
                                        }?>
                                    </td>
                                </tr>
                                <?php }}else{
                                       echo "<tr class='gradeA'><td colspan=15 style='text-align:center;color:red;font-weight:bold;'>暂无您查询的出租评论数据~！</td></tr>";
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



