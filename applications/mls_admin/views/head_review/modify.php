<?php require APPPATH . 'views/header.php'; ?>
<head>
<link href="<?=MLS_SOURCE_URL ?>/min/?f=mls/css/v1.0/picpop.css" rel="stylesheet" type="text/css"/>
</head>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.11.0.min.js" type="text/javascript"></script>
<script>
function submit(submit_flag,id){
	//alert(agency_id);
	var status = $("#status").val();
	var remark = $("#remark").val();

	var data = {submit_flag:submit_flag,status:status,remark:remark};

	$.ajax({
		type: "POST",
		url: "/head_review/modify/"+id,
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

</script>
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
                                                <label>新头像：&nbsp;&nbsp;<img width="200" height="200" src="<?php echo str_replace('thumb', 'initial', $head_review_info_new['headpic']); ?>"/></label>
                                              </div>
                                              <div class="dataTables_length" id="dataTables-example_length">
                                                <label>原头像：&nbsp;&nbsp;<?php if (!empty($broker_info['photo'])) { ?>
                                                    <img width="200" height="200" src="<?php echo str_replace('thumb', 'initial', $broker_info['photo']); ?>" />
                                                    <?php } else { ?>
                                                    <?php if (!empty($head_info_old1['headpic'])) { ?>
                                                    <img width="200" height="200" src="<?php echo str_replace('thumb', 'initial', $head_info_old1['headpic']); ?>" /><?php } ?>
                                                    <?php if (!empty($head_info_old2['headpic'])) { ?>
                                                    <img width="200" height="200" src="<?php echo str_replace('thumb', 'initial', $head_info_old2['headpic']); ?>" /><?php } ?>
                                                    <?php if (!empty($head_info_old3['headpic'])) { ?>
                                                    <img width="200" height="200" src="<?php echo str_replace('thumb', 'initial', $head_info_old3['headpic']); ?>" /><?php } ?>
                                                    <?php } ?>
                                                </label>
                                              </div>
                                            </div>
                                        </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>状态：</label>
                                                    <label>
                                                        <select  class="form-control input-sm" style="width:168px" aria-controls="dataTables-example" name="status" id="status">
                                                            <option value="1" <?php if($head_review_info_new['status'] == 1){echo 'selected="selected"';}?>>待审核</option>
                                                            <option value="2" <?php if($head_review_info_new['status'] == 2){echo 'selected="selected"';}?>>通过</option>
                                                            <option value="3" <?php if($head_review_info_new['status'] == 3){echo 'selected="selected"';}?>>驳回</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
											<!-- <br>11<?php echo $head_review_info_new['status'];?>22<br> -->
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>理由：</label>
                                                    <label>
                                                    <textarea name="remark" rows="3" cols="50" id="remark"><?php echo $head_review_info_new['remark'] ?></textarea>
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
												<?php if($head_review_info_new['status'] == 1){ ?>
													<a class="btn btn-primary" href="#" onclick="submit('modify',<?=$head_id?>)">提交</a>
													<?php }else{ ?>
													<a class="btn btn-primary" href="#" disabled>提交</a>
													<?php } ?>
													<a class="btn btn-primary" href="/head_review/index">返回</a>
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
            <a href="/head_review/index" >点此返回</a>
        <?php } else { ?>
            <div><h1><b>修改失败</b></h1></div>
            <a href="/head_review/index" >点此返回</a>
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

<script>
$(function(){
	$('img').bigic();
});

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
            // 生成HTML 选中DOM节点
            sHtml += '<div id="popupBigic" class="popup-bigic" style="width:'+ this.nWinWid +'px;height:'+ this.nWinHei +'px;">'
                  +     '<div class="option-bigic">'
                  +         '<span id="changeBigic" class="change-bigic min-bigic" state-bigic="min">放大</span>'
                  +         '<span id="closeBigic" class="close-bigic">关闭</span>'
                  +     '</div>'
                  +     '<img id="LoadingBigic" class="loading-bigic" src="" />'
                  +  '</div>';
            $('body').append(sHtml);
            oThis.$popup = $('#popupBigic');

            // 事件绑定 - 关闭弹窗
            $('#closeBigic').off().on('click',function(){
                oThis.$popup.remove();
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

