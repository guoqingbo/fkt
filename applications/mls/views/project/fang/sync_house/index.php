<style>
.subject_01{overflow-y:scroll; position:absolute; left:0; top:0; width:100%; background:#BD2729;}
.subject_01 .inner{width:1280px; margin:0 auto; background:#BE2729;}
</style>
<div class="subject_01">
    <div class="inner">
        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/b2.jpg" >
        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/b3.jpg" >
        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/b4.jpg" >
        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/b5.jpg" >
        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/b6.jpg" >
        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/b7.jpg" >
    </div>
</div>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js,openWin.js"></script>
<script>
    $(function () {
        function re_height(){
            $(".subject_01").css({
                "height":$(window).height()
            });
        };
        re_height();
    });
</script>
