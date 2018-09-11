<?php require APPPATH.'views/header.php'; ?>
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
                                <input type="hidden" name="submit_flag" value="search">
                                <div style="width:100%" class="col-sm-6">
                                    编号：&nbsp;<input type="text" value="<?php echo $para_array['id'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="id">
                                    用户姓名：&nbsp;<input type="text" value="<?php echo $para_array['uid'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:80px;display: inline-block;"  name="uid">
                                    电话：&nbsp;<input type="text" value="<?php echo $para_array['phone'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:100px;display: inline-block;"  name="phone">
                                    小区：&nbsp;<input type="text" value="<?php echo $para_array['comt_name'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:120px;display: inline-block;"  name="comt_name">
                                    所属公司&nbsp;<input id="company_name"  value="<?php echo $para_array['company_name'];?>" class="input_text input_text_r w150 form-control input-sm" style="width:120px;display: inline-block;height:30px; line-height: 30px" type="text" placeholder="输入汉字筛选">
                                                   <input type="hidden" id="company_id" name="company_id" value="<?php echo $para_array['company_id'];?>">
                                    所属门店&nbsp;<select name="agency_id"  id="agency_id" >
                                                    <option value="">请选择</option>
                                                </select>
                                    <script>
                                         $("#company_name").change(function(){
                                             var company_name= $(this).val();
                                             $.ajax({
                                                type: 'get',
                                                url : '<?php echo MLS_ADMIN_URL; ?>/entrust_rent/get_agency/',
                                                dataType:'json',
                                                data:{
                                                    name:company_name
                                                },
                                                success: function(msg){
                                                    var str = '';
                                                    if(msg===""){
                                                        str = '<option value="">请选择</option>';
                                                    }else{
                                                        for(var i=0;i<msg.length;i++){
                                                            str +='<option value="'+msg[i].id+'" >'+msg[i].name+'</option>';
                                                            $("#company_id").val(msg[i].company_id);
                                                        }

                                                    }
                                                    $('#agency_id').empty();
                                                    $('#agency_id').append(str);
                                                }
                                             });
                                         })
                                    </script>
                                    委托状态：&nbsp;<select name="status">
                                                    <option value="">不限</option>
                                                    <option value="0" >未委托</option>
                                                    <option value="1">已委托</option>
                                                </select>
                                    <input type="hidden" name="pg" value="1">
                                    <input class="btn btn-primary" type="submit" value="查询" name="search">&nbsp;&nbsp;&nbsp;<input class="btn btn-primary" type="button" onclick="javascript:location.href = '/entrust_rent/'" value="重置">
                                </div>
                            </form>
                        </div>
                        <table id="dataTables-example" class="table table-striped table-bordered table-hover">
                            <thead>
                                 <tr>
                                    <th style="width:45px">编号</th>
                                    <th style="width:75px">用户姓名</th>
                                    <th style="width:105px">电话</th>
                                    <th style="width:250px;text-align:center">小区名称</th>
                                    <th style="width:75px">面积(㎡)</th>
                                    <th style="width:70px">期望租金(元)</th>
                                    <th style="width:90px">创建时间</th>
                                    <th style="width:150px">委托门店</th>
                                    <th style="width:90px">委托经纪人</th>
                                    <th style="width:90px">状态</th>
                                    <th style="width:200px;text-align:center">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($list) && !empty($list)) {
                                            foreach($list as $key =>$val){?>
                                <tr class="gradeA">
                                    <td><?php echo $val['id'];?></td>
                                    <td><?php echo $val['realname'];?></td>
                                    <td><?php echo $val['phone'];?></td>
                                    <td><?php echo $val['comt_name'];?></td>
                                    <td><?php echo $val['area'];?></td>
                                    <td><?php echo $val['hprice'];?></td>
                                    <td><?php echo date("Y-m-d H:i:s",$val['ctime']);?></td>
                                    <td><?php echo $val['agency_name'];?></td>
                                    <td><?php echo $val['broker_name'];?></td>
                                    <td><?php switch($val['status']){case 0:echo "未委托";break;case 1:echo "<font color='green'>已委托</font>";break;}?></td>
                                    <td><?php switch($val['status']){case 0:echo "<a href='/entrust_rent/entrust/".$val['id']."'>委托</a> | 
                                                                                   <a href='/entrust_rent/del/".$val['id']."'>删除</a>";break;
                                                                      case 1:echo "<a href='/entrust_rent/edit_entrust/".$val['id']."'>修改委托</a> | 
                                                                                   <a href='/entrust_rent/cancel_entrust/".$val['id']."'>取消委托</a> | 
                                                                                   <a href='/entrust_rent/del/".$val['id']."'>删除</a>";break;}?></td>
                                </tr>
                                <?php }}else{
                                       echo "<tr class='gradeA'><td colspan=15 style='text-align:center;color:red;font-weight:bold;'>暂无您查询的出租委托数据~！</td></tr>";
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
<?php
if ( isset($js) && $js != '')
{
    echo $js;
}

if ( isset($css) && $css != '')
{
    echo $css;
}
?>




