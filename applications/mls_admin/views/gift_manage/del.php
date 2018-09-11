<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?=$title?></h1>
                </div>
            </div>
            <?php if($deleteResult){ ?>
            	<div><h1><b>删除成功</b></h1></div>
                <div><a href="/gift_manage/index">点此返回</a></div>
            <?php } else {?>
                <div><h1><b>删除失败</b></h1></div>
                <div><a href="/gift_manage/index">点此返回</a></div>
             <?php }?>
                </div>
            </div>
        </div>
    </div>
<?php require APPPATH.'views/footer.php'; ?>

