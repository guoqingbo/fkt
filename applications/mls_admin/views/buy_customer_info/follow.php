<?php require APPPATH . 'views/header.php'; ?>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/jquery-ui-1.9.2.custom.min.js"></script>

    <div id="wrapper">
    <div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= $title ?></h1>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover" id="dataTables-example" style="font-size: 12px;">
        <thead>
          <tr>
              <th>跟进时间</th>
              <th>类别</th>
              <th>内容</th>
              <th>客户姓名</th>
              <th>跟进人</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($follows) && isset($follows)) { foreach ($follows as $key => $value) { ?>
              <tr>
                  <td><?php echo $value['date']; ?></td>
                  <td><?php echo $value['follow_name']; ?></td>
                  <td><?php echo $value['text']; ?></td>
                  <td><?php echo $value['truename']; ?></td>
                  <td><?php echo $value['broker_name']; ?></td>
              </tr>
          <?php } } ?>
        </tbody>

    </table>

    </div>
<?php require APPPATH.'views/footer.php'; ?>
