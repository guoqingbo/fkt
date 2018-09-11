<body style="background:#FAFAFA; position:relative;">
<div class="tab_box" id="js_tab_box">
   	<a href="/entrust/my_entrust/" class="btn-lv" style="float:right; margin-right:10px;"><span>返回房源列表</span></a>
	<p class="tab_title">查看详情</p>
</div>
<div id="js_inner" style="width:100%; overflow-x:hidden;overflow-y:scroll;position:absolute;top:40px;left:0; ">
	<div class="mark-cont">
		<h2>房源详情</h2>
		<div class="mark-cont-inner mark-detail clearfix">
			<div class="see-img left"><a href="javascript:void(0);" onclick="openwix('see_pic')"><img width="420" height="300" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/tmp/01.jpg" /></a><em>10图</em></div>
			<div class="mark-detail-right right forms clearfix">
				<h3><?=$my_entrust_detail['blockname']?></h3>
				<p><strong>售价：</strong><em><?=strip_end_0($my_entrust_detail['price'])?></em>万 </p>
				<p><strong>区属板块：</strong><?=$my_entrust_detail['district_street']?></p>
				<p><strong>户型：</strong><?=$my_entrust_detail['housetype']?></p>
				<p><strong>面积：</strong><?=strip_end_0($my_entrust_detail['buildarea'])?>m² <strong>楼层：</strong><?=$my_entrust_detail['floor']?>层/<?=$my_entrust_detail['totalfloor']?>层　<strong>朝向：</strong><?=$my_entrust_detail['forward']?></p>
				<?php if($my_entrust_detail['receive']){?>
				<div class="b"><span class="left">状态：<b>已认领</b></span><a href="javascript:void(0);" class="btn-lan"><span>取消认领</span></a></div>
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/mark_tip2.png" />
				<?php }else{?>
				<b class="label labelOn">认领此房源</b>
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/mark_tip1.png" />
				<div class="mark-rob"><button type="submit" class="submit">马上去抢</button>已有<b><?=$my_entrust_detail['num']?></b>人抢拍，还剩<b><?=$my_entrust_detail['remain_num']?></b>个名额</div>
				<?php }?>
				<div class="mark-rob2">业主：<?=$my_entrust_detail['contactor']?>　电话：<em><?=$my_entrust_detail['telno']?></em></div>
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
			<ul>
			<?php
			if(is_full_array($appraise_list)){
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
				 <img class="" src="<?=$v['photo'] ?>" />
    		<?php
    			}
			}
			?>
			</ul>
			<div id="js_fun_btn" class="fun_btn clearfix">
		         <form name="search_form" id="search_form" method="post" action="">
                    <input type="hidden" name="page" value="1">
                    <input type="hidden" id="houseid" value="<?=$houseid ?>">
                 </form>
				<div class="get_page">
					<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
				</div>
			</div>
			<div class="editing clearfix">
				<h4><span>请勿填写联系方式、公司名称、链接、与房源无关信息及从其它网站拷贝的内容。限100-500字。</span>填写房源评价描述</h4>
				<textarea id="appraise" placeholder="请输入房源评价" onkeyup="textCounter()"></textarea>
				<p id="p_id_text"></p>
				<button type="submit" class="submit" onclick="appraise_submit()">提交</button>
			</div>
		</div>
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
                            upload_url: "<?=MLS_FILE_SERVER_URL?>/uploadimg/index/",
    						file_size_limit : "4 MB",
    						file_types : "*.jpg;*.png",
    						file_types_description : "JPG Images",
    						file_upload_limit : "0",
    						file_queue_limit : "20",

    						custom_settings : {
    							upload_target : "jsPicPreviewBoxM1",
    							upload_limit  : 20,
    							upload_nail	  : "thumbnails1",
    							upload_infotype : 1
    						},
    						swfupload_loaded_handler : swfUploadLoaded,
    						file_queue_error_handler : fileQueueError,
    						file_dialog_start_handler : fileDialogStart,
    						file_dialog_complete_handler : fileDialogComplete,
    						upload_progress_handler : uploadProgress,
    						upload_error_handler : uploadError,
    						upload_success_handler : uploadSuccess,
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
				<div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails1"></div>
			</div>
			<div class="clearfix"><button type="submit" class="submit" onclick="upload_pic()">提交</button></div>
			<p class="mark-pic-tip">注意事项：</br>
				1、上传宽度大于600像素，比例为4:3的图片可获得更好的展示效果。</br>
				2、请勿上传有水印、盖章等任何侵犯他人版权或含有广告信息的图片。</br>
				3、可上传20张图片，每张小于4M，建议尺寸大于400x300像素，最佳尺寸为500*375像素。
			</p>
		</div>
    </div>
</div>

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
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
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

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script type="text/javascript">
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

function appraise_submit(){
	var houseid=$("#houseid").val();
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
        			parent.location.reload();
				 });
    		}else{
    			$("#dialog_do_warnig_tip").html(data.msg);
        		openWin('js_pop_do_warning');
    		}
		}
	});
}

function upload_pic(){
	var houseid=$("#houseid").val();
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
        			parent.location.reload();
				 });
    		}else{
    			$("#dialog_do_warnig_tip").html(data.msg);
        		openWin('js_pop_do_warning');
    		}
		}
	});
}
</script>
