<script src="<?php echo MLS_SOURCE_URL;?>/common/third/My97DatePicker/WdatePicker.js"></script>
<div class="pop_box_g" id="new_moban" style="height:512px;width:700px;display: block; border:none;">
    <div class="hd">
        <div class="title">图片详情</div>
    </div>
    <div class="photo_popcon1 clearfix">
		<div class="show_house_pic1">
			<div class="pic1"> <img alt="" src="<?php echo $surface_img;?>" height="300" width="390"> </div>
			<div class="small_pic1 clearfix">
                <?php if(empty($small_img_2)){?>
				<img class="no-img1" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/no_img2.png">
                <?php }else{ ?>
			    <div class="prev"><span class="iconfont">&#xe607;</span></div>
			    <div class="next"><span class="iconfont">&#xe607;</span></div>
			    <div class="list list1">
			        <ul class="clearfix">
                        <?php foreach ($small_img_2 as $k => $v){?>
						<li class="item"><img alt="" src="<?php echo $v;?>" height="64" width="85"></li>
                        <?php }?>
			        </ul>
			    </div>
                <?php }?>
			</div>
		</div>
		<div class="show_house_pic1">
			<div class="pic1"> <img alt="" src="<?php echo $surface_img;?>" height="300" width="390"> </div>
			<div class="small_pic1 clearfix">
                <?php if(empty($small_img_3)){?>
				<img class="no-img1" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/no_img2.png">
                <?php }else{ ?>
			    <div class="prev"><span class="iconfont">&#xe607;</span></div>
			    <div class="next"><span class="iconfont">&#xe607;</span></div>
			    <div class="list list2">
			        <ul class="clearfix">
                        <?php foreach ($small_img_3 as $k => $v){?>
                        <li class="item"><img alt="" src="<?php echo $v;?>" height="64" width="85"></li>
                        <?php }?>
			        </ul>
			    </div>
                <?php }?>
			</div>
		</div>
		<div class="show_house_pic1">
			<div class="pic1"> <img alt="" src="<?php echo $surface_img;?>" height="300" width="390"> </div>
			<div class="small_pic1 clearfix">
                <?php if(empty($small_img_1)){?>
				<img class="no-img1" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/no_img2.png">
                <?php }else{ ?>
			    <div class="prev"><span class="iconfont">&#xe607;</span></div>
			    <div class="next"><span class="iconfont">&#xe607;</span></div>
			    <div class="list list3">
			        <ul class="clearfix">
                        <?php foreach ($small_img_1 as $k => $v){?>
                        <li class="item"><img alt="" src="<?php echo $v;?>" height="64" width="85"></li>
                        <?php }?>
			        </ul>
			    </div>
                <?php }?>
			</div>
		</div>
		<div class="show_house_pic1">
			<div class="pic1"> <img alt="" src="<?php echo $surface_img;?>" height="300" width="390"> </div>
			<div class="small_pic1 clearfix">
                <?php if(empty($small_img_4)){?>
				<img class="no-img1" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/no_img2.png">
                <?php }else{ ?>
			    <div class="prev"><span class="iconfont">&#xe607;</span></div>
			    <div class="next"><span class="iconfont">&#xe607;</span></div>
			    <div class="list list4">
			        <ul class="clearfix">
                        <?php foreach ($small_img_4 as $k => $v){?>
                        <li class="item"><img alt="" src="<?php echo $v;?>" height="64" width="85"></li>
                        <?php }?>
			        </ul>
			    </div>
                <?php } ?>
			</div>
		</div>
		<div class="show_house_pic1">
			<div class="pic1"> <img alt="" src="<?php echo $surface_img;?>" height="300" width="390"> </div>
			<div class="small_pic1 clearfix">
                <?php if(empty($small_img_5)){?>
				<img class="no-img1" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/no_img2.png">
                <?php }else{ ?>
			    <div class="prev"><span class="iconfont">&#xe607;</span></div>
			    <div class="next"><span class="iconfont">&#xe607;</span></div>
			    <div class="list list5">
			        <ul class="clearfix">
                        <?php foreach ($small_img_5 as $k => $v){?>
                        <li class="item"><img alt="" src="<?php echo $v;?>" height="64" width="85"></li>
                        <?php }?>
			        </ul>
			    </div>
                <?php } ?>
			</div>
		</div>
		<div class="show_house_pic1">
			<div class="pic1"> <img alt="" src="<?php echo $surface_img;?>" height="300" width="390"> </div>
			<div class="small_pic1 clearfix">
                <?php if(empty($small_img_6)){?>
				<img class="no-img1" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/no_img2.png">
                <?php }else{ ?>
			    <div class="prev"><span class="iconfont">&#xe607;</span></div>
			    <div class="next"><span class="iconfont">&#xe607;</span></div>
			    <div class="list list6">
			        <ul class="clearfix">
                        <?php foreach ($small_img_6 as $k => $v){?>
                        <li class="item"><img alt="" src="<?php echo $v;?>" height="64" width="85"></li>
                        <?php }?>
			        </ul>
			    </div>
                <?php } ?>
			</div>
		</div>
		<ul class="photo_opera">
                <li class="img_type" id="1">
					<div class="photo_icon iconfont">&#xe627;</div>
					<div class="photo_title">小区正门</div>
				</li>
				<li class="img_type" id="2">
					<div class="photo_icon iconfont">&#xe628;</div>
					<div class="photo_title">外景图</div>
				</li>
				<li class="img_type" id="3">
					<div class="photo_icon iconfont">&#xe61e;</div>
					<div class="photo_title">户型图</div>
				</li>
				<li class="img_type" id="4">
					<div class="photo_icon iconfont">&#xe629;</div>
					<div class="photo_title">小区环境</div>
				</li>
				<li class="img_type" id="5">
					<div class="photo_icon iconfont">&#xe62a;</div>
					<div class="photo_title">内部设施</div>
				</li>
				<li class="img_type" id="6">
					<div class="photo_icon iconfont">&#xe60e;</div>
					<div class="photo_title">周边配套</div>
				</li>
			</ul>

    </div>
	<div class="photo_anniu">
        <input type="hidden" value="<?php echo $cmt_id;?>" id="cmt_id"/>
		<a href="javascript:void(0);" class="btn-lan btn-left"  onClick="$('#js_upload .iframePop').attr('src','<?php echo MLS_URL;?>/community/img_upload/<?php echo $cmt_id;?>');openWin('js_upload')"><span>我要上传</span></a>
		<!--<a href="javascript:void(0);" class="btn-lan JS_Close"><span>确定</span></a>-->
	</div>
</div>


<!--图片上传弹框-->
<div id="js_upload" class="iframePopBox" style="width:697px;height:420px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="697" height="420" class='iframePop' src=""></iframe>
</div>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/Marquee.js"></script>
<script>
$(function(){
	$('.list').kxbdSuperMarquee({
		distance:89,
		time:3,
		isAuto:false,
		btnGo:{left:'.prev',right:'.next'},
		direction:'left'
	});

	$('.show_house_pic1').hide();
	$('.show_house_pic1').eq(0).show();

	$(".list .item").on('click',function(){
		var src = $(this).find("img").prop('src');
		$(".list .item").removeClass("active");
		$(this).addClass("active");
		$(this).parents(".small_pic1").siblings(".pic1").find("img").prop('src',src);
	});

    //根据图片类型筛选图片，获取图片地址重新组装html
	$('.img_type').on('click',function(){
		var img_type = $(this).attr('id') - 1;
		$(".show_house_pic1").hide();
		$(".show_house_pic1").eq(img_type).show();
	});
});
</script>
