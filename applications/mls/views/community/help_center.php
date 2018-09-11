<script>
    window.parent.addNavClass(9);
</script>
<body>
<div class="tab_box" id="js_tab_box">
	<?php
    echo $user_menu;
    ?>
</div>
<div class="help-wrap">
	<div class="help-menu-wrap">
		<ul>
            <?php foreach ($parents as $key => $value) { ?>
                <li <?php if($value['id'] == $now_active) { ?>class="active"<?php } ?> >
                    <a href="/community/help_center/<?php echo $value['id']; ?>" ><?php echo $value['title']; ?></a>
                </li>
            <?php } ?>
		</ul>
	</div>
	<div class="help-content-wrap">
		<h1><?php echo $f_ptitle; ?></h1>
		<div>
            <?php  $i = 1;
            foreach ($first as $key => $value) { ?>
            <p class="help-title"><?php echo $i++; ?>.<?php echo $value['title']?><span class="iconfont">&#xe60a;</span></p>
            <div class="content hide">
                <?php echo $value['content']; ?>
                <div class="arrow"></div>
            </div>
            <?php } ?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		$(".help-title").click(function(){
            var self = $(this);
            if(self.hasClass("active")){
                self.find('span').html('&#xe60a;');
                self.removeClass("active");
                self.next().addClass("hide");
            }else{
                self.find('span').html('&#xe609;');
                self.addClass("active");
                self.next().removeClass("hide");
            }
        });
	});
</script>
</body>
