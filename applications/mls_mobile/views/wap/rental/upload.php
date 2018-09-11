<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>房屋资料</title>
        <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/zjd.css" type="text/css"/>

		<!-- 引用控制层插件样式 -->
		<link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zyFile/control/css/zyUpload.css" type="text/css">

		<!--图片弹出层样式 必要样式-->
		<script type="text/javascript" src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zyFile/jquery-1.7.2.js"></script>
		<!-- 引用核心层插件 -->
		<script type="text/javascript" src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zyFile/core/zyFile.js"></script>
		<!-- 引用控制层插件 -->
		<script type="text/javascript" src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zyFile/control/js/zyUpload.js"></script>
		<!-- 引用初始化JS -->
        <script>
          $(function(){
            var limit = '<?=$limit;?>';
            var inputid = '<?=$inputid;?>';
        	// 初始化插件
        	$("#fileupload").zyUpload({
        		width            :   "100%",                 // 宽度
        		height           :   "auto",                 // 宽度
        		itemWidth        :   "80px",                 // 文件项的宽度
        		itemHeight       :   "60px",                 // 文件项的高度
        		url              :   "/wap/finance/upload/",
        		multiple         :   false,                    // 是否可以多个文件上传
        		dragDrop         :   false,                    // 是否可以拖动上传文件
        		del              :   false,                    // 是否可以删除文件
        		finishDel        :   false,  				  // 是否在上传文件完成后删除预览
        		/* 外部获得的回调接口 */
        		onSelect: function(selectFiles, allFiles){    // 选择文件的回调方法  selectFile:当前选中的文件  allFiles:还没上传的全部文件
        			//console.info("当前选择了以下文件：");
        			//console.info(selectFiles);
                    if(limit && allFiles.length >= limit){
                        $('.add_upload').html('');
                    }
        		},
        		onProgress: function(file, loaded, total){    // 正在上传的进度的回调方法
        			//console.info("当前正在上传此文件：");
        			//console.info(file.name);
        			//console.info("进度等信息如下：");
        			//console.info(loaded);
        			//console.info(total);
        		},
        		onDelete: function(file, files){              // 删除一个文件的回调方法 file:当前删除的文件  files:删除之后的文件
        			//console.info("当前删除了此文件：");
        			//console.info(file.name);
        		},
        		onSuccess: function(file, response){          // 文件上传成功的回调方法
        			//console.info("此文件上传成功：");
                    var obj = JSON.parse(response);
                    $('#file').val(obj.data.other_image_url);
                    window.parent.hidden(inputid,obj.data.other_image_url);
                    $("#uploadInf").html("");
        		},
        		onFailure: function(file, response){          // 文件上传失败的回调方法
        			//console.info("此文件上传失败：");
        			//console.info(file.name);
        		},
        		onComplete: function(response){           	  // 上传完成的回调方法
        			//console.info("文件上传完成");
        			//console.info(response);
        		}
        	});
        });
        </script>
    </head>
    <body style="margin: 0;padding:0;">
        <div id="fileupload"></div>
    </body>
</html>
