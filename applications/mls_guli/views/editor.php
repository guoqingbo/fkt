<!DOCTYPE>
<html>
    <head>
        <style>
            *{margin:0;padding:0;}
        </style>
        <script charset='utf-8'  src='<?php echo MLS_SOURCE_URL;?>/common/js/kindeditor-4.1.10/kindeditor-min.js'></script>
        <script charset='utf-8'  src='<?php echo MLS_SOURCE_URL;?>/common/js/kindeditor-4.1.10/lang/zh_CN.js'></script>
    </head>
    <body style="backgroud:none;">
        <textarea name="remark" id="remark" cols="0" rows="0" style="width:835px;height:155px;display:none;"></textarea>
        <script>
            document.getElementById('remark').value = window.parent.document.getElementById('remark').value;

            var editor;
            KindEditor.ready(function(K) {
                editor = K.create('#remark', {
                    width: '<?= $width ?>px',
                    height: '<?= $height ?>px',
                    resizeType: 0,
                    allowPreviewEmoticons: false,
                    allowImageUpload: false,
                    items: [<?=$items?>],
                    afterBlur: function() {
                        this.sync();
                        window.parent.document.getElementById('remark').value = document.getElementById('remark').value;
                    }
                });
            });
        </script>
    </body>
</html>
