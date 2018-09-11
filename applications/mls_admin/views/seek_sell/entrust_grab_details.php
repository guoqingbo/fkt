<?php require APPPATH.'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper" style="min-height: 337px;" >
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
                                    <input type="hidden" name="pg" value="1">
                                </div>
                            </form>
                        </div>
                        <table id="dataTables-example" class="table table-striped table-bordered table-hover">
                            <thead>
                                 <tr>
                                    <th style="width:6%;">编号</th>
                                    <th style="width:10%;">经纪人姓名</th>
                                    <th style="width:10%;">经纪人电话</th>
                                    <th style="width:27%;">公司名称</th>
                                    <th style="width:27%;">门店名称</th>
                                    <th style="width:20%;">抢拍时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($list) && !empty($list)) {
                                            foreach($list as $key =>$val){?>
                                <tr class="gradeA">
                                    <td><?php echo $val['id'];?></td>
                                    <td><?php echo $val['broker_name'];?></td>
                                    <td><?php echo $val['phone'];?></td>
                                    <td><?php echo $val['company_name'];?></td>
                                    <td><?php echo $val['agency_name'];?></td>
                                    <td><?php echo date("Y-m-d H:i:s",$val['createtime']);?></td>

                                </tr>
                                <?php }}else{
                                       echo "<tr class='gradeA'><td colspan=15 style='text-align:center;color:red;font-weight:bold;'>暂无您查询的出售委托明细~！</td></tr>";
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





