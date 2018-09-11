<?php require APPPATH.'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper" style="min-height: 337px;">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default" style="width:500px">
                    <div class="panel-body" >
                        <table id="dataTables-example" class="table table-striped table-bordered table-hover" style="width:500px">
                            <thead>
                                 <tr>
                                    <th style="width:120px">分类类型</th>
                                    <th>房源编号</th>
                                    <th style="width:100px">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($list){foreach($list as $key =>$val){?>
                                <tr class="gradeA">
                                    <td><?php switch ($val['type']){ case 1:echo"二手房房源"; break;case 2:echo"出售房源";break;case 3:echo"新房房源";break;}?></td>
                                    <td><?php echo $val['row_ids'];?></td>
                                    <td><a href="<?=MLS_ADMIN_URL?>/recommend/edit/<?php echo $val['id'];?>">修改</a></td>
                                </tr>
                                <?php }}?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
