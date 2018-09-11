<head>
<link href="<?=MLS_SOURCE_URL ?>/min/?f=mls/css/v1.0/picpop.css" rel="stylesheet" type="text/css"/>
</head>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquey-bigic.js" type="text/javascript"></script>


<?php require APPPATH . 'views/header.php'; ?>
<style>
    .dataTables_length {
        line-height: 30px;
    }
</style>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$title?></h1>
            </div>
        </div>
        <?php if ($modifyResult === '') { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="add_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>合同编号：<input type="text" id="order_sn" value="<?php echo $success_applay['order_sn'] ?>" readonly/></label>
                                                </div>
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>甲乙双方：
                                                        <font style="color:red;">甲方经纪人：姓名： <?php echo $success_applay['broker_a']['broker_name'] ?> 门店名：<?php echo $success_applay['broker_a']['agency_name'] ?> <?php if($success_applay['broker_a']['agency_type'] == 1){
															echo '[直营]';
														}else if($success_applay['broker_a']['agency_type'] == 2){
															echo '[加盟]';
														}
														?>公司名：<?php echo $success_applay['broker_a']['company_name'] ?></font>
                                                    <br><font style="color:red;">&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
                                                    乙方经纪人：姓名：<?php echo $success_applay['broker_b']['broker_name'] ?>  门店名：<?php echo $success_applay['broker_b']['agency_name'] ?>  公司名：<?php echo $success_applay['broker_b']['company_name'] ?></font>
                                                    </label>
                                                </div>
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>业主姓名：<input type="text" value="<?php echo $success_applay['seller_owner'] ?>" readonly/></label>
                                                    &nbsp;&nbsp;<label>业主电话：<input type="text" value="<?php echo $success_applay['seller_telno'] ?>" readonly/></label>
                                                    &nbsp;&nbsp;<label>业主身份证：<input type="text" value="<?php echo $success_applay['seller_idcard'] ?>" readonly/></label>
                                                </div>
												<div class="dataTables_length" id="dataTables-example_length">
                                                    <label>买方姓名：<input type="text" value="<?php echo $success_applay['buyer_owner'] ?>" readonly/></label>
                                                    &nbsp;&nbsp;<label>买方电话：<input type="text" value="<?php echo $success_applay['buyer_telno'] ?>" readonly/></label>
                                                    &nbsp;&nbsp;<label>买方身份证：<input type="text" value="<?php echo $success_applay['buyer_idcard'] ?>" readonly/></label>
                                                </div>
                                            </div>
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>买卖契约：
                                                <?php
                                                if ($success_applay['photo'] !== '')
                                                {
                                                    $pics = explode(',', $success_applay['pic']);
                                                    foreach($pics as $k => $v) {
                                                ?><img width="200" height="200" src="<?php echo str_replace('thumb','initial',$v); ?>"  broker="1"/>
                                                <?php } }?>
												<!--<a href="javascript:void(0);" onclick="dl_pics();return false;">点击下载全部图片</button>
                                                </a>-->
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>状&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;态：</label>
                                                    <label>  
                                                        <select  class="form-control input-sm" style="width:168px" aria-controls="dataTables-example" name="status" id="status">
                                                            <option value="0" <?php if($success_applay['status'] == 0){echo 'selected="selected"';}?>>待审核</option>
                                                            <option value="1" <?php if($success_applay['status'] == 1){echo 'selected="selected"';}?>>通过</option>
                                                            <option value="2" <?php if($success_applay['status'] == 2){echo 'selected="selected"';}?>>驳回</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                            <?php if (!empty($mess_error)) { ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <font color='red'><?php echo $mess_error; ?></font>
                                                </div>
                                            </div>
                                            <?php } ?>									  								  
                                            <div class="col-sm-6" style="width:100%">
												<div class="dataTables_length" id="dataTables-example_length">
													<a class="btn btn-primary" href="#" onclick="submit('modify',<?=$success_applay['id']?>)"<?php if($review){echo 'disabled';}?>>提交</a>
													<a class="btn btn-primary" href="/project_cooperate_lol_success_applay/index">返回</a>
												</div>
											</div>							  
                                        </div>
                                    </div>
                                    <input type="hidden" name="submit_flag" value="modify">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } else if ($modifyResult == 'success') { ?>
            <div><h1><b>修改成功</b></h1></div>
            <a href="/auth_review/index" >点此返回</a>
        <?php } else { ?>
            <div><h1><b>修改失败</b></h1></div>
            <a href="/auth_review/index" >点此返回</a>
        <?php } ?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="col-lg-4" style="display:none" id="js_note1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            提示框
            <button type="button" class="close JS_Close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="panel-body">
            <p id="warning_text"></p>
        </div>
    </div>
</div>
<script type="text/javascript">
function submit(submit_flag,id){
    data = {'submit_flag' : submit_flag, 'status' : $('#status').val()}
	$.ajax({ 
		type: "POST", 
		url: "/project_cooperate_lol_success_applay/modify/"+id, 
		data:data, 
		cache:false, 
		error:function(){ 
			alert("系统错误");
			return false; 
		}, 
		success: function(data){ 
			alert(data);
		} 
	});	
}

function dl_pics(){
	var pic_urls="";
	var order_sn = $("#order_sn").val();
	var imgs = document.getElementsByTagName("img");
	for(var i=0;i<imgs.length;i++){
		pic_urls+=imgs [i].src;
		if(i+1<imgs.length)pic_urls+=",";
	}
	var url = "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>";
	$.ajax({ 
		type: "POST", 
		url: "/project_cooperate_lol_success_applay/download_pics/", 
		data:{pic_urls:pic_urls,order_sn:order_sn,url:url}, 
		cache:false, 
		error:function(){ 
			alert("系统错误");
			return false; 
		}, 
		success: function(data){
			alert(data);

		} 
	});
}
</script>
<script>
	$(function(){
		$('img').bigic();
	});

	/**
 * jQuery Plugin bigic v1.0.0
/*
*/
(function ($) {
    $.fn.bigic = function () {

        /*
         * 构造函数 @Bigic
         * 定义基础变量，初始化对象事件
         */
        function Bigic($obj){
            this.$win = $(window);
            this.$obj = $obj;
            this.$popup,
            this.$img,
            this.nWinWid = 0;
            this.nWinHei = 0;
            this.nImgWid = 0;
            this.nImgHei = 0;
            this.nImgRate = 0;
            this.sImgStatus;
            this.sImgSrc,
            this.bMoveX = true,
            this.bMoveY = true;

            this.init();
        }

        /*
         * 初始化 绑定基础事件
         */
        Bigic.prototype.init = function(){
            var oThis = this,
                timer = null;

            // 为图片绑定点击事件
            this.$obj.off('.bigic').on('click.bigic', function(){
                var sTagName = this.tagName.toLowerCase();
                if(sTagName == 'img'){
                    // 更新基础变量
                    oThis.sImgSrc = this.getAttribute('src');
                    oThis.sImgStatus = 'min';
                    // 显示弹窗
                    oThis.show();
                }else{
                    alert('非IMG标签');
                }
            });

            // 浏览器缩放
            this.$win.off('.bigic').on('resize.bigic', function(){
                clearTimeout(timer);
                timer = setTimeout(function(){
                    oThis.zoom();
                }, 30);
            });
        }

        /*
         * 弹窗初始化
         */
        Bigic.prototype.show = function(){
            var oThis = this,
                oImg = new Image();

            oThis.popup();   // 显示弹窗

            // 图片加载
            oImg.onload = function(){
                oThis.nImgWid = this.width;
                oThis.nImgHei = this.height;
                oThis.nImgRate = oThis.nImgWid/oThis.nImgHei;

                $('#LoadingBigic').remove();
                oThis.$popup.append('<img id="imgBigic" class="img-bigic" src="'+ oThis.sImgSrc +'" />');
                oThis.$img = $('#imgBigic');
                
                oThis.zoom();
            }
            oImg.src = oThis.sImgSrc;
        }

        /*
         * 弹窗显示 及相关控件事件绑定
         */
        Bigic.prototype.popup = function(){
            var sHtml = '',
                oThis = this;
			var margin_top = '';
			var margin_left = '';
			var rotate_width = '';
			var rotate_height = '';
            // 生成HTML 选中DOM节点
            sHtml += '<div id="popupBigic" class="popup-bigic" style="width:'+ this.nWinWid +'px;height:'+ this.nWinHei +'px;overflow:auto">' 
                  +     '<div class="option-bigic">'
				  +         '<span id="rotateBigic" class="rotate-bigic" >旋转</span>'
                  +         '<span id="changeBigic" class="change-bigic min-bigic" state-bigic="min">放大</span>'
                  +         '<span id="closeBigic" class="close-bigic">关闭</span>'
                  +     '</div>'
                  +     '<img id="LoadingBigic" class="loading-bigic" src="preloader.gif" />'
                  +  '</div>';
            $('body').append(sHtml);
            oThis.$popup = $('#popupBigic');
            
            // 事件绑定 - 关闭弹窗
            $('#closeBigic').off().on('click',function(){
                oThis.$popup.remove();
            });
			
			$('#rotateBigic').on('click',function(){
				rotate_width = $('#imgBigic').width();
				rotate_height = $('#imgBigic').height();
                $('#imgBigic').rotate(90);
				$('#imgBigic').css("width",rotate_height);
				$('#imgBigic').css("height",rotate_width);
				margin_left = Math.abs(($("#popupBigic").width()-$('#imgBigic').width())/2);
				margin_top = Math.abs(($("#popupBigic").height()-$('#imgBigic').height())/2);
				$('#imgBigic').css("margin-top",margin_top);
				$('#imgBigic').css("margin-left",margin_left);
            });

            // 事件绑定 - 切换尺寸
            $('#changeBigic').off().on('click',function(){
                if(!document.getElementById('imgBigic')) return;
                if($(this).hasClass('min-bigic')){
                    oThis.sImgStatus = 'max';
                    $(this).removeClass('min-bigic').addClass('max-bigic').html('缩小');
                }else{
                    oThis.sImgStatus = 'min';
                    $(this).removeClass('max-bigic').addClass('min-bigic').html('放大');;
                }
                oThis.zoom();
            });
        }

        /*
         * 图片放大缩小控制函数
         */
        Bigic.prototype.zoom = function(){
            var nWid = 0,cnHei = 0,
                nLeft = 0, nTop = 0,
                nMal = 0, nMat = 0;

            // 弹窗未打开 或 非img 返回
            if(!document.getElementById('popupBigic') || !this.nImgWid) return;

            this.nWinWid = this.$win.width();
            this.nWinHei = this.$win.height();
            this.bMoveX = true;
            this.bMoveY = true;

            // 显示隐藏放大缩小按钮
            if(this.nImgWid > this.nWinWid || this.nImgHei > this.nWinHei){
                $('#changeBigic')[0].style.display = 'inline-block';
            }else{
                $('#changeBigic')[0].style.display = 'none';
            }

            if(this.sImgStatus == 'min'){
                nWid = this.nImgWid > this.nWinWid ? this.nWinWid : this.nImgWid;
                nHei = nWid / this.nImgRate;

                if(nHei > this.nWinHei) nHei = this.nWinHei;
                nWid = nHei*this.nImgRate;

                this.$img.css({'width': nWid +'px', 'height': nHei +'px', 'left': '50%', 'top': '50%', 'margin-top': -nHei/2+'px', 'margin-left': -nWid/2+'px'});
                this.$popup.css({'width': this.nWinWid +'px', 'height': this.nWinHei+'px'});
                this.move(false);
            }else{
                if(this.nImgWid < this.nWinWid){
                    nLeft = '50%'
                    nMal = this.nImgWid / 2;
                    this.bMoveX = false;
                }
                if(this.nImgHei < this.nWinHei){
                    nTop = '50%'
                    nMat = this.nImgHei / 2;
                    this.bMoveY = false;
                }
                this.$img.css({'width': this.nImgWid +'px', 'height': this.nImgHei+'px', 'left': nLeft, 'top': nTop, 'margin-top': -nMat +'px', 'margin-left': -nMal +'px'});
                this.$popup.css({'width': this.nWinWid +'px', 'height': this.nWinHei+'px'});
                this.move(true);
            }
        }

        /*
         * 图片移动事件
         */
        Bigic.prototype.move = function(bln){
            var _x, _y, _winW, _winH,
                _move = false,
                _boxW = this.nImgWid,
                _boxH = this.nImgHei,
                oThis = this;

                if(!oThis.$img) return;
                // 解除绑定
                if(!bln){
                    oThis.$img.off('.bigic');
                    $(document).off('.bigic');
                    return;
                }

                // 弹窗移动
                oThis.$img.off('.bigic').on({
                    'click.bigic': function(e){
                            e.preventDefault();
                        },
                    'mousedown.bigic': function(e){
                            e.preventDefault();
                            _move=true;
                            _x=e.pageX-parseInt(oThis.$img.css("left"));
                            _y=e.pageY-parseInt(oThis.$img.css("top"));
                            _winW = oThis.nWinWid;
                            _winH = oThis.nWinHei;
                            oThis.$img.css('cursor','move');
                        }
                });
                $(document).off('.bigic').on({
                    'mousemove.bigic': function(e){
                            e.preventDefault();
                            if(_move){
                                var x=e.pageX-_x;
                                var y=e.pageY-_y;
                                if(x > 0) x = 0;
                                if(y > 0) y = 0;
                                if(_winW && x < _winW-_boxW) x = _winW - _boxW;
                                if(_winH && y < _winH-_boxH) y = _winH - _boxH;
                                if(oThis.bMoveX) oThis.$img[0].style.left = x +'px';
                                if(oThis.bMoveY) oThis.$img[0].style.top = y +'px';
                            }
                        },
                    'mouseup.bigic': function(){
                            _move=false;
                            oThis.$img.css('cursor','default');
                        }
                });
        }
        
        /*
         * 实例化
         */
        new Bigic($(this));
    };
})(jQuery);
</script> 
<?php require APPPATH . 'views/footer.php'; ?>

<script>
jQuery.fn.rotate = function(angle,whence) { 
    var p = this.get(0);
 
    // we store the angle inside the image tag for persistence  
    if (!whence) { 
        p.angle = ((p.angle==undefined?0:p.angle) + angle) % 360; 
    } else { 
        p.angle = angle; 
    } 
 
    if (p.angle >= 0) { 
        var rotation = Math.PI * p.angle / 180; 
    } else { 
        var rotation = Math.PI * (360+p.angle) / 180; 
    } 
    var costheta = Math.round(Math.cos(rotation) * 1000) / 1000; 
    var sintheta = Math.round(Math.sin(rotation) * 1000) / 1000; 
    //alert(costheta+","+sintheta);  
  
    if (document.all && !window.opera) { 
        var canvas = document.createElement('img'); 
 
        canvas.src = p.src; 
        canvas.height = p.height; 
        canvas.width = p.width; 
 
        canvas.style.filter = "progid:DXImageTransform.Microsoft.Matrix(M11="+costheta+",M12="+(-sintheta)+",M21="+sintheta+",M22="+costheta+",SizingMethod='auto expand')"; 
    } else { 
        var canvas = document.createElement('canvas'); 
        if (!p.oImage) { 
            canvas.oImage = new Image(); 
            canvas.oImage.src = p.src; 
        } else { 
            canvas.oImage = p.oImage; 
        } 
 
        canvas.style.width = canvas.width = Math.abs(costheta*canvas.oImage.width) + Math.abs(sintheta*canvas.oImage.height); 
        canvas.style.height = canvas.height = Math.abs(costheta*canvas.oImage.height) + Math.abs(sintheta*canvas.oImage.width); 
 
        var context = canvas.getContext('2d'); 
        context.save(); 
        if (rotation <= Math.PI/2) { 
            context.translate(sintheta*canvas.oImage.height,0); 
        } else if (rotation <= Math.PI) { 
            context.translate(canvas.width,-costheta*canvas.oImage.height); 
        } else if (rotation <= 1.5*Math.PI) { 
            context.translate(-costheta*canvas.oImage.width,canvas.height); 
        } else { 
            context.translate(0,-sintheta*canvas.oImage.width); 
        } 
        context.rotate(rotation); 
        context.drawImage(canvas.oImage, 0, 0, canvas.oImage.width, canvas.oImage.height); 
        context.restore(); 
    } 
    canvas.id = p.id; 
    canvas.angle = p.angle; 
    p.parentNode.replaceChild(canvas, p); 
} 
 
jQuery.fn.rotateRight = function(angle) { 
    this.rotate(angle==undefined?90:angle); 
} 
 
jQuery.fn.rotateLeft = function(angle) { 
    this.rotate(angle==undefined?-90:-angle); 
}
</script>

