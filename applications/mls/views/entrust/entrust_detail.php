<?php
if($entrust_detail['id']){
    $url = '/entrust/my_entrust/';
}else{
    $url = '/entrust/index/';
}
?>
<script type="text/javascript">
window.parent.addNavClass(17);
//失效弹框跳转
function friend(){
	$("#dialog_do_warnig_tip").html("房源已失效");
	openWin('js_pop_do_warning');
	$("#close").click(function(){
		   window.location.href="<?=$url?>";
		   return false;
    });
}
</script>
<style>
body{background:#FAFAFA;}
</style>

<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">操作成功！</p>
				<button type="button" class="btn" id="sure_yes">确定</button>
			</div>
		</div>
	</div>
</div>
<div id="js_pop_do_warning"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" id="close"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_warnig_tip">操作失败！</p>
			</div>
		</div>
	</div>
</div>

<div class="tab_box" id="js_tab_box">
	<a href=<?=$url?> class="btn-lv" style="float:right; margin-right:10px;"><span>返回房源列表</span></a>
	<p class="tab_title">查看详情</p>
</div>
<div id="js_inner" style="position:relative;overflow-y:scroll; width:100%;">
<?php
if($entrust_detail['status'] == 1){
?>
	<div class="mark-cont">
		<h2>房源详情</h2>
		<div class="mark-cont-inner mark-detail clearfix">
			<div class="see-img left"><a href="javascript:void(0)" onclick="openWin('see_pic')"><img width="420" height="300" src="<?=$pic_default ?>" /></a><em><?=$pic_len ?>图</em></div>
			<div class="mark-detail-right right forms clearfix">
				<h3><?=$entrust_detail['blockname']?></h3>
				<p><strong>售价：</strong><em><?=strip_end_0($entrust_detail['price'])?></em>万 </p>
				<p><strong>区属板块：</strong><?=$entrust_detail['district_street']?></p>
				<p><strong>户型：</strong><?=$entrust_detail['housetype']?></p>
				<p><strong>面积：</strong><?=strip_end_0($entrust_detail['buildarea'])?>m²　<strong>楼层：</strong><?=$entrust_detail['floor']?>层/<?=$entrust_detail['totalfloor']?>层　<strong>朝向：</strong><?=$entrust_detail['forward']?></p>
				<?php
				if($entrust_detail['id']&&$entrust_detail['receive']){
				?>
				<div class="b"><span class="left">状态：<b>已认领</b></span><a class="btn-lan" onclick="update_entrust_broker(<?=$entrust_detail['id']?>,0)"><span>取消认领</span></a></div>
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/mark_tip2.png" />
				<?php
				}else{
					if($entrust_detail['id']&&$entrust_detail['receive']==0){
					?>
                        <div class="b"><span class="left">状态：<b>未认领</b></span><a class="btn-lan" onclick="update_entrust_broker(<?=$entrust_detail['id']?>,1)"><span>马上认领</span></a></div>
			            <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/mark_tip1.png" />
					<?php
					}else{
				    ?>
				    <b class="label">认领此房源</b>
					<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/mark_tip1.png" />
					<div class="mark-rob">
				    <?php
						if($entrust_detail['num']==10){
					?>
							<button type="submit" class="btn-bhui fl">已抢光</button><b>已经被抢光了，您下次早点来</b>
					<?php
						}else{
					?>
							<button type="submit" class="submit" onclick="add_entrust_broker(<?=$houseid ?>)">马上去抢</button>
							<?php
							if($entrust_detail['num']&&$entrust_detail['num']<10){
							?>
							 已有<b><?=$entrust_detail['num']?></b>人抢拍，还剩<b><?=$entrust_detail['remain_num']?></b>个名额
							<?php
							}else{
								echo '<b>还没有人抢哦，快来做第一个吧~</b>';
							}
						}
					?>
					</div>
					<?php
					}
				 }
				 if($entrust_detail['id']){
				 ?>
				<div class="mark-rob2">业主：<?=$entrust_detail['contactor']?>　电话：<em><?=$entrust_detail['telno']?></em></div>
				<?php }?>
			</div>
		</div>
		<h2>已抢委托经纪人（<?=$entrust_total?>人）</h2>
		<div class="mark-cont-inner mark-list">
			<ul class="clearfix">
			<?php
			if(is_full_array($broker_list)){
				foreach($broker_list as $value){
			?>
				 <li>
				 <?php
				 $strlen = mb_strlen($value['truename'])-1;
				 echo mb_substr($value['truename'],0,1,'utf-8');
				 for($i=1;$i<=$strlen;$i++){
					 echo '*';
				 }
				 ?>
				 <em><?=date('Y-m-d H:i:s', $value['dateline'])?></em>　已抢拍</li>
			<?php
				}
			}
			?>
			</ul>
		</div>
		<h2>房源评价（<?=$appraise_total?>人）</h2>
		<div class="mark-cont-inner mark-comt">
			<?php
			if(is_full_array($appraise_list)){
			?>
			<ul>
			<?php
				foreach($appraise_list as $v){
			?>
				 <li><h3>
				 <?php
				 $strlen = mb_strlen($v['truename'])-1;
				 echo mb_substr($v['truename'],0,1,'utf-8');
				 for($i=1;$i<=$strlen;$i++){
					 echo '*';
				 }
				 echo date('Y-m-d H:i:s', $v['dateline']);
				 ?></h3>
				 <p><?=$v['appraise'] ?></p>
				 <img width="50" height="50" src="<?=$v['photo'] ?>" /></li>
			<?php
				}
			?>
			</ul>
			<div id="js_fun_btn" class="fun_btn clearfix">
				<form name="search_form" id="search_form" method="post" action="">
					<div class="get_page">
						<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
					</div>
				</form>
			</div>
			<?php
			}else{
				echo '<p class="no-comt">暂无评价<p>';
			}
			if($entrust_detail['id']){
			?>
			<div class="editing clearfix">
				<h4><span>请勿填写联系方式、公司名称、链接、与房源无关信息及从其它网站拷贝的内容。限100-500字。</span>填写房源评价描述</h4>
				<?php if($my_appraise){?>
				<textarea id="appraise" onkeyup="textCounter()"><?=$my_appraise['appraise'] ?></textarea>
				<p id="p_id_text"></p>
				<button type="submit" class="submit" onclick="appraise_update(<?=$houseid ?>)">修改</button>
				<?php }else{?>
				<textarea id="appraise" placeholder="请输入房源评价" onkeyup="textCounter()"></textarea>
				<p id="p_id_text"></p>
				<button type="submit" class="submit" onclick="appraise_submit(<?=$houseid ?>)">提交</button>
				<?php }?>
			</div>
			<?php
			}
			?>
		</div>
		<?php
		if($entrust_detail['id']){
		?>
		<h2>上传图片</h2>
		<div class="mark-cont-inner mark-pic">
			<div class="add-pic clearfix">
				<div class="add_item">
					<span id="spanButtonPlaceholder1"></span>
				</div>
				<script type="text/javascript">
					var swfu1;
					$(function() {
						swfu1 = new SWFUpload({
                            file_post_name: "file",
                            upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
							file_size_limit : "5 MB",
							file_types : "*.jpg;*.png",
							file_types_description : "JPG Images",
							file_upload_limit : "0",
							file_queue_limit : "10",

							custom_settings : {
								upload_target : "jsPicPreviewBoxM1",
								upload_limit  : 10,
								upload_nail	  : "thumbnails1",
								upload_infotype : 1
							},
							swfupload_loaded_handler : swfUploadLoaded,
							file_queue_error_handler : fileQueueError,
							file_dialog_start_handler : fileDialogStart,
							file_dialog_complete_handler : fileDialogComplete,
							upload_progress_handler : uploadProgress,
							upload_error_handler : uploadError,
							upload_success_handler : uploadSuccessNew,
							upload_complete_handler : uploadComplete,

							button_image_url : "<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/flash_btn02.png",
							button_placeholder_id : "spanButtonPlaceholder1",
							button_width: 130,
							button_height: 100,
							button_cursor: SWFUpload.CURSOR.HAND,
							button_text:"",
							flash_url : "/swfupload.swf"
						});
					});
				</script>
				<div id="jsPicPreviewBoxM1" style="display:none" ></div>
				<div class="picPreviewBoxM ui-sortable" id="thumbnails1">
                <?php
                if(isset($mypic_arr)&&is_full_array($mypic_arr)){
                   foreach($mypic_arr as $v_pic){
                ?>
                    <div class="add_item_pic">
                        <div class="pic">
                            <img width="130" height="100" src="<?=$v_pic ?>">
                        </div>
                        <input type="hidden" name="p_filename1[]" value="<?=$v_pic ?>">
                        <input type="hidden" name="p_fileids1[]" value="0">
                        <div class="fun">
                            <a id="1" class="del_pic" onclick="fun_hide_p(this);swfu1.setButtonDisabled(false);" href="javascript:void(0)">删除</a>
                        </div>
                    </div>
                <?php
                    }
                }
				?>
				</div>
			</div>
			<div class="clearfix"><button type="submit" class="submit" onclick="upload_pic(<?=$houseid ?>)">提交</button></div>
			<p class="mark-pic-tip">注意事项：</br>
				1、上传宽度大于600像素，比例为4:3的图片可获得更好的展示效果。</br>
				2、请勿上传有水印、盖章等任何侵犯他人版权或含有广告信息的图片。</br>
				3、可上传10张图片，每张小于5M，建议尺寸大于400x300像素，最佳尺寸为500*375像素。
			</p>
		</div>
		<?php }?>
	</div>
<?php
}else{
	echo '<script>friend();</script>';
}
?>
</div>

<div class="pop_box_g" id="see_pic" style="width:786px; height:570px; display:none; background:#fff;">
    <div class="hd">
        <div class="title">查看房源图片</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod" style="position:relative;">
		<div id="see-pic">
            <ul>
                <?php
                if(is_full_array($pic_arr)){
                    foreach($pic_arr as $pic_v){
                        $pic_v = str_replace('_130x100','',$pic_v);
//                        $pic_v = str_replace('thumb/','',$pic_v);
                        $pic_v = changepic($pic_v);
                ?>
                <li><img width="750" height="500" src="<?=$pic_v ?>"></li>
                <?php
                    }
                }else{
                ?>
                <li><img width="750" height="530" src="<?=$pic_default ?>"></li>
                <?php
                }
                ?>
			</ul>
		</div>
		<?php if(is_full_array($pic_arr)){?>
		<span class="goL" title="上一页">上一页</span>
		<span class="goR" title="下一页">下一页</span>
		<div class="see-tip"><span class="t1">1</span>/<span class="t2"></span></div>
		<?php }?>
    </div>


<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script type="text/javascript">
$(function(){
    $(".label").click(function(){
        $(this).toggleClass("labelOn")
    });
    $('#see-pic').kxbdSuperMarquee({
		distance:750,
		time:0,
		newAmount:1,
		duration:10,
		isAuto:false,
		btnGo:{left:'.goR',right:'.goL'},
		direction:'left'
	});
});

function textCounter(){
	var text_uid=$("#appraise").val();
	var text_len=text_uid.length;
	var text_num=100-text_len;
	if(text_len<100){
		$('#p_id_text').html('<span style="color:red;">您至少还需要输入'+text_num+'字</span>');
	}else if(text_len>500){
		$('#p_id_text').html('<span style="color:red;">请输入少于500个字</span>');
	}else{
		$('#p_id_text').html('');
	}

}

function appraise_submit(houseid){
	var appraise=$("#appraise").val();
	var appraise_len=appraise.length;
	if(appraise_len<100){
		$("#dialog_do_warnig_tip").html("请输入至少100个字");
	    openWin('js_pop_do_warning');
	    return false;
	}else if(appraise_len>500){
		$("#dialog_do_warnig_tip").html("请输入少于500个字");
	    openWin('js_pop_do_warning');
	    return false;
	}
	var data = {houseid:houseid,appraise:appraise};
	$.ajax({
		type: "POST",
		url: "/entrust/appraise/",
		dataType:"json",
		data:data,
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
  		    openWin('js_pop_do_warning');
			return false;
		},
		success: function(data){
			if(data.status=="success"){
    			$("#dialog_do_success_tip").html(data.msg);
        		openWin('js_pop_do_success');
        		$("#sure_yes").click(function(){
        			window.location.reload();
				 });
    		}else{
    			$("#dialog_do_warnig_tip").html(data.msg);
        		openWin('js_pop_do_warning');
    		}
		}
	});
}

function appraise_update(houseid){
	var appraise_update=$("#appraise").val();
	var appraise_update_len=appraise_update.length;
	if(appraise_update_len<100){
		$("#dialog_do_warnig_tip").html("请输入至少100个字");
	    openWin('js_pop_do_warning');
	    return false;
	}else if(appraise_update_len>500){
		$("#dialog_do_warnig_tip").html("请输入少于500个字");
	    openWin('js_pop_do_warning');
	    return false;
	}
	var data = {houseid:houseid,appraise_update:appraise_update};
	$.ajax({
		type: "POST",
		url: "/entrust/appraise_update/",
		dataType:"json",
		data:data,
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
  		    openWin('js_pop_do_warning');
			return false;
		},
		success: function(data){
			if(data.status=="success"){
    			$("#dialog_do_success_tip").html(data.msg);
        		openWin('js_pop_do_success');
        		$("#sure_yes").click(function(){
        			window.location.reload();
				 });
    		}else{
    			$("#dialog_do_warnig_tip").html(data.msg);
        		openWin('js_pop_do_warning');
    		}
		}
	});
}

function upload_pic(houseid){
	var photo_url = "";
    $("input[name='p_filename1[]']").each(function(index,item){
        photo_url += $(this).val()+',';
    });

	var data = {houseid:houseid,photo_url:photo_url};
	$.ajax({
		type: "POST",
		url: "/entrust/entrust_pic/",
		dataType:"json",
		data:data,
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
  		    openWin('js_pop_do_warning');
			return false;
		},
		success: function(data){
			if(data.status=="success"){
    			$("#dialog_do_success_tip").html(data.msg);
        		openWin('js_pop_do_success');
        		$("#sure_yes").click(function(){
        			window.location.reload();
				 });
    		}else{
    			$("#dialog_do_warnig_tip").html(data.msg);
        		openWin('js_pop_do_warning');
    		}
		}
	});
}

//认领
function update_entrust_broker(id,receive){
	var data = {id:id,receive:receive};
	$.ajax({
		type: "POST",
		url: "/entrust/update_entrust_broker/",
		dataType:"json",
		data:data,
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
  		    openWin('js_pop_do_warning');
			return false;
		},
		success: function(data){
			if(data.status=="success"){
    			$("#dialog_do_success_tip").html(data.msg);
        		openWin('js_pop_do_success');
        		$("#sure_yes").click(function(){
        			window.location.reload();
				 });
    		}else{
    			$("#dialog_do_warnig_tip").html(data.msg);
        		openWin('js_pop_do_warning');
    		}
		}
	});
}

//抢拍
function add_entrust_broker(houseid){
	if($(".label").hasClass("labelOn")){
		receive = 1;
	}else{
		receive = 0;
	}

	var data = {houseid:houseid,receive:receive};
	$.ajax({
		type: "POST",
		url: "/entrust/add_entrust_broker/",
		dataType:"json",
		data:data,
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
  		    openWin('js_pop_do_warning');
			return false;
		},
		success: function(data){
			if(data.status=="success"){
    			$("#dialog_do_success_tip").html(data.msg);
        		openWin('js_pop_do_success');
        		$("#sure_yes").click(function(){
        			window.location.reload();
				 });
    		}else{
    			$("#dialog_do_warnig_tip").html(data.msg);
        		openWin('js_pop_do_warning');
        		$("#close").click(function(){
        			window.location.reload();
				 });
    		}
		}
	});
}
</script>
