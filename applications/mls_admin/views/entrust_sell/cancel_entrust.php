<?php require APPPATH.'views/header.php'; ?>
<?php if($result==1){?>
     <div>取消成功</div>
<?php }elseif($result==0){?>
     <div>取消失败</div>
<?php } ?>
<script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/entrust_sell/index/'; ?>";
            }, 1000);
        });
</script>
