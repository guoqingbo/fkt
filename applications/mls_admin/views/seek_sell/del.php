<?php require APPPATH.'views/header.php'; ?>
<?php if($result==1){?>
     <div>删除成功</div>
<?php }elseif($result==0){?>
     <div>删除失败</div>
<?php } ?>
<script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/seek_sell/index/'; ?>";
            }, 1000);
        });
</script>
