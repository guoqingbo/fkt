<?php require APPPATH.'views/header.php';date_default_timezone_set("PRC"); ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <iframe frameborder="0" scrolling="no" width="920" height="540" class='iframePop' src="<?php echo $iframe_src;?>"></iframe>
        </div>
    </div>
<?php require APPPATH.'views/footer.php'; ?>
<input type="button" class="btn btn-primary" onclick="window.history.go(-1);" value="返回"/>