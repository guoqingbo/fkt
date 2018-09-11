<script>
    $(function() {
        var iframe_id = "<?=$iframe_id?>";
        if (iframe_id) {
            closeParentWin(iframe_id);
        }
        window.parent.permission_none();
    });
</script>