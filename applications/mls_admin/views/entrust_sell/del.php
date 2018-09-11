<?php require APPPATH.'views/header.php'; ?>
<?php if($result==1){?>
     <div>下架成功</div>
<?php }elseif($result==0){?>
     <div>下架失败</div>
<?php } ?>
<script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/entrust_sell/index/'; ?>";
            }, 1000);
        });
</script>
