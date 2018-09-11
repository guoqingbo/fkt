$(function() {

    $('#side-menu').metisMenu();

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse')
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse')
        }

        height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    })
})

var openWin = function(obj){
	var winElemt = $('#'+ obj);
	var $coverDom = "<div id=\"GTipsCover\" style=\"background:#000;position:absolute;filter:alpha(opacity=0);opacity:0;width:100%;left:0;top:0;z-index:9999901\"><iframe src=\"about:blank\" style=\"height:"+$(document).height()+"px;filter:alpha(opacity=0);opacity:0;scrolling=no;z-index:870610\"></iframe></div>"
	var win = {
		
		height : function(){
			return winElemt.outerHeight();
		},
		
		width : function(){
			return winElemt.outerWidth();
		},
		
		show : function(){
			winElemt.css({
				'position':'absolute',
				'z-index':'9999902',
				'left':'50%',
				'margin-left':'-' + win.width() / 2 + 'px',
				'top':$(window).height()/2 - (win.height()/2) + $(window).scrollTop() + 'px'
			}).show();
			$($coverDom).appendTo('body').css({opacity:0.5})
		},
		
		hide : function(){
			winElemt.hide();
			$('#GTipsCover').remove();
		}
	
	};
	win.show();
	$('.JS_Close').click(function(){
		win.hide();
	});
};

