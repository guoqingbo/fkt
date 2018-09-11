<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="format-detection" content="telephone=no">
        <title>error</title>
        <script type="text/javascript">
            !(function (doc, win) {
                var docEle = doc.documentElement,
                    evt = "onorientationchange" in window ? "orientationchange" : "resize",
                    fn = function () {
                        var width = docEle.clientWidth;
                        width && (docEle.style.fontSize = 20 * (width / 375) + "px");
                    };

                win.addEventListener(evt, fn, false);
                doc.addEventListener("DOMContentLoaded", fn, false);

            }(document, window));
        </script>
        
    </head>
    
    <body>
		<input type="hidden" value="<?=$result?>" id="result" name="result" />
        <input type="hidden" value="<?=$msg?>" id="msg" name="msg" />
        <input type="hidden" value="<?=$data?>" id="data" name="data" />
    </body>
</html>