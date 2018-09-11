<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?=$title?></h1>
                </div>
            </div>
            <?php if($statusResult == 1){ ?>
            	<div><h1><b>上架成功</b></h1></div>
                <div><a href="/gift_manage/index">点此返回</a></div>
            <?php } elseif($statusResult == 2) {?>
                <div><h1><b>下架成功</b></h1></div>
                <div><a href="/gift_manage/index">点此返回</a></div>
            <?php }else{?>
				<div><h1><b>状态修改失败</b></h1></div>
                <div><a href="/gift_manage/index">点此返回</a></div>
			<?php }?>
                </div>
            </div>
        </div>
    </div>
<?php require APPPATH.'views/footer.php'; ?>

