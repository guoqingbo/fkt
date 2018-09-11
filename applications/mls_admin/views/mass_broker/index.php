<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $title ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>经纪人id</th>
                                    <th>经纪人号码</th>
                                    <th>网站id，对应mass_site表中的 id</th>
                                    <th>经纪人在对应网站上注册的用户名，对应site_id所关联的网站</th>
                                    <th>启用状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (is_full_array($mass_broker)) {
                                foreach ($mass_broker as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id']; ?></td>
                                        <td><?php echo $value['broker_id']; ?></td>
                                        <td><?php echo $value['phone']; ?></td>
                                        <td>
                                            <?php echo $value['site_id']; ?>（<?php foreach ($site_list as $val) {
                                                if($value['site_id'] == $val['id']){
                                                    echo $val['name'];
                                                }
                                            }?>）
                                        </td>
                                        <td><?php echo $value['username']; ?></td>
                                        <td><?=$value['status'] == 1?'已启用':'未启用';?></td>
                                        <td>
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/mass_site_broker/modify/<?=$value['id']; ?>" >查看</a>
                                        </td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPPATH . 'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
