<?php require APPPATH.'views/header.php'; ?>
<script src="<?=MLS_ADMIN_URL?>/applications/mls_admin/static/js/jquery-1.8.3.min.js"></script>
<?php if($setinfo==""){?>
<div id="wrapper">
    <div id="page-wrapper" style="min-height: 337px;">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?></h1>
            </div>
        </div>
         <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form action="" method="post" name="search_form">
                                    <input type="hidden" value="<?php echo $list['id']?>" name="id">
                                    <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline" role="grid">
                                        <div class="row">
                                            <div style="width:100%" class="col-sm-6">
                                                <div id="dataTables-example_length" class="dataTables_length">
                                                </div>
                                            </div>
                                        </div>
					<div style="width:100%" class="col-sm-6">
                                            <div id="dataTables-example_length" class="dataTables_length">
                                                <label>
                                                    权限模块:&nbsp;&nbsp;&nbsp;&nbsp;
						    <?php switch ($list['type']){ case 1:echo"二手房房源"; break;case 2:echo"出售房源";break;case 3:echo"新房房源";break;}?>
                                                </label>
                                            </div>
                                        </div>
                                        <div style="width:100%" class="col-sm-6">
                                            <div id="dataTables-example_length" class="dataTables_length">
                                                <label>
                                                    房源编号:&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <?php foreach($list['row_ids'] as $key => $val){?>
                                                    <input type="search" value="<?php echo $val;?>" aria-controls="dataTables-example" class="form-control input-sm"style='width:80px' name="row_ids<?php echo $key;?>">&nbsp,&nbsp
                                                    <?php }?>
                                                    (此处填写的顺序是推荐展示的顺序)
                                                </label>
                                            </div>
                                        </div>
                                        <div style="width:100%" class="col-sm-6">
                                            <div id="dataTables-example_length" class="dataTables_length">
                                                <input type="hidden" value="edit" name="submit_flag">
                                                <input type="submit" value="提交" class="btn btn-primary">
                                                <input type="button" value="取消" onclick="goback()" class="btn btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                            <?php }elseif($setinfo===0){?>
                                  <div>修改失败</div>
                            <?php }else{?>
                                  <div>修改成功</div>
                            <?php }?>
                    </div>
                </div>
            </div>
    </div>
</div>
<?php if ($setinfo != "") { ?>
    <script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/recommend/index/'; ?>";
            }, 1000);
        });
    </script>
<?php } ?>
<script>
   function goback(){
       window.location.href = "<?php echo MLS_ADMIN_URL . '/recommend/index/'; ?>";
   }
</script>
