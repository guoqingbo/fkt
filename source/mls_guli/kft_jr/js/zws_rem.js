/**
 * date : 20160919
 * 移动端根据屏幕尺寸计算rem，以达到适配需求
 * 
 * 
 * **/
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