<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/lpsj.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="pop_box_g" id="js_pop_box_g"  style="display:block; border:none;width: 950px;">
    <div class="hd">
        <div class="title">楼栋详情</div>
        <div class="close_pop"></div>
    </div>
    <div class="wrapper ywgj" style="margin: 0 auto;margin-top: 40px;position:relative;">
        <h1 style="margin-bottom:40px;">添加楼栋</h1>
            <ul class="ld_ul">
                <li class="li_ld clearfix li_ld_new">
                    <div class="add_ld">
                        <input type="text" name="dong_name[]" value="请输入楼栋号" msgs="请输入楼栋号" onfocus="bindFocus(this,this.value,true)"
                               onblur="bindBlur(this,$(this).attr('msgs'))"/>
                        <span class="bt_ad_ld">+&nbsp;楼栋号</span>
                    </div>
                    <div class="add_dy">
                        <div class="dy_main">
                            <div class="bind_dy">
                                <input type="text" name="unit_name[]" value="请输入单元号" msgs="请输入单元号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr('msgs'))"/>
                            </div>
                            <div class="bind_mp clearfix">
                                <div class="mp_li clearfix">
                                    <div class="more_mp">
                                        <input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr('msgs'))"/>
                                    </div>
                                    <div class="more_mp">
                                        <input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr('msgs'))"/>
                                        <span class="mp_close">&nbsp;</span>
                                    </div>
                                    <div class="more_mp">
                                        <input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr('msgs'))"/>
                                        <span class="mp_close">&nbsp;</span>
                                    </div>
                                    <div class="more_mp">
                                        <input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr('msgs'))"/>
                                        <span class="mp_close">&nbsp;</span>
                                    </div>
                                    <div class="more_mp">
                                        <input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr('msgs'))"/>
                                        <span class="mp_close">&nbsp;</span>
                                    </div>
                                    <div class="more_mp">
                                        <input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr('msgs'))"/>
                                        <span class="mp_close">&nbsp;</span>
                                    </div>
                                    <span class="bt_add_mp"></span>
                                </div>
                            </div>
                        </div>
                        <div class="add_dy_main clearfix">
                            <span class="bt_add_dy add_dy_left">+&nbsp;单元号</span>
                            <span class="bt_add_dy add_dy_right">-&nbsp;删除单元号</span>
                        </div>

                    </div>
                </li>
            </ul>

        <div class="all_btns" style="padding-bottom:40px;">
            <div class="bt_xg_no clearfix" style="display: block;">
            <button class="button_bc bt_add" id="save_submit">保存资料</button>
            </div>
        </div>
    </div>
    </div>
</body>
<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="#" title="关闭" class="JS_Close iconfont" id="close_refresh"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'></p>
            </div>
        </div>
    </div>
</div>

<!--提示导入表格弹窗-->
<div id="jss_pop_error" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" style="line-height:28px;"><br>
                    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/error_ico.png">
                    <span> 请上传表格！</span>
                    </p>
                </div>
            </div>
    </div>
</div>

<!--确认导入表格弹窗-->
<div id="jss_pop_sure" class="pop_box_g pop_see_inform pop_no_q_up stop_pop_box" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:location.reload();" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod" style="_margin-top:-10px;">
    	<div class="inform_inner">
             <div class="up_inner">
				<p class="text" style="line-height:28px;"><br>
				   <img alt="" src="">
					<span></span>
				</p>
			</div>
        </div>
    </div>
</div>

<!-- 出售房源入 -->
<div id="jss_pop_import" class="pop_box_g pop_see_inform" style="display:none;" >
    <div class="hd">
        <div class="title">栋座单元门牌导入</div>
        <div class="close_pop"><a onclick="$('#jss_pop_import').hide();$('#xx1x').contents().find('body').empty();" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">

        <div class="up_m_b_tex">栋座单元门牌导入功能可以将外部栋座单元门牌直接导入系统中，省去手动录入的麻烦。为保证您顺利导入，请使用我们提供的标准模板，且勿对模板样式做任何删改。</br><a href="<?php echo MLS_SOURCE_URL;?>/xls/example6.xls">
                <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/page_white_excel.png">点击下载栋座单元门牌导入模板</a>
        </div>
        <style>
            .up_m_b_file .text{ float:left; line-height:26px;}
            .up_m_b_file .text_input{width:150px;height: 24px;line-height: 24px;padding: 0 10px;border: 1px solid #E9E9E9;float: left;}
            .up_m_b_file .f_btn{ margin-left:10px;_display:inline; float:left; background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0; width:44px; height:26px; overflow:hidden; position:relative; overflow:hidden; text-align:center; line-height:26px; }
            .up_m_b_file .f_btn .file{cursor:pointer;font-size:50px;filter:alpha(opacity:0); opacity: 0; position:absolute; right:-5px; top:-5px;}
            .up_m_b_file .btn_up_b{ margin-left:10px; _display:inline; float:left; overflow:hidden; width:44px; height:26px; position:relative; line-height:26px; text-align:center;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0;}
            .up_m_b_file .btn_up_b .btn_up{ cursor:pointer; font-size:100px; position:absolute;filter:alpha(opacity:0); opacity: 0; right:-5px; top:-5px;}
        </style>
        <div class="up_m_b_file clearfix">
            <form action="/community/import" enctype="multipart/form-data" target="new" method="post">
            <p class="text">上传导入文件：</p>
            <input type="text" class="text_input" id="aa" name="aa">
            <div class="f_btn" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">浏览</div><input class="file" name="upfile" type="file" onchange="document.getElementById('aa').value=this.value"></div>
            <div class="btn_up_b" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">上传</div><input class="btn_up" type="submit" name="sub" value="上传"></div>
            </form>
        </div>
        <iframe allowtransparency="true" src="<?php echo MLS_URL;?>/blank.php" frameborder="0" scrolling="no" name="new" id="xx1x" height="34" width="393" style="bac"></iframe><!-- width="470"  wty---->
        <!--<p class="up_m_b_date_up" style="text-align: center">出售房源12321.xls<span class="up_s">上传成功</span>，共上传123条房源。</p>
        <p class="up_m_b_date_up" style="text-align: center">出售房源12321.xls<span class="up_e">上传失败</span>，共上传123条房源。</p> -->
        <div style="text-align:center;"><a class="btn-lv" href="javascript:void(0)" onclick="sure()"><span>确认导入</span></a></div>
    </div>
</div>

<script>
    window.onload = function(){

          setInterval(function(){
                    var winHeight = $(window).height()-43;
                    $('.wrapper').css('height',winHeight);
          },500);

    }

	function dong_door_unit_import(){
		//先清空上传文本框
	    $("input[name='upfile']").val('');
	    $("#aa").val('');
	    openWin('jss_pop_import');
	}

    function bindFocus(obj,msg,tag){
        if(obj.value == msg) obj.value = '';
        obj.style.color = '#333';
        if(tag){
            obj.style.fontWeight = 'bold';
        }

    }
    function bindBlur(obj,msg){

        if(obj.value == '') {
            obj.value = msg;
            obj.style.color = 'rgb(179,179,179)';
            obj.style.fontWeight = 'normal';
        }

    }

    // - hover
    $('.ywgj').delegate('.mp_close','mouseenter',function(){
        $(this).addClass('mp_close_hover');
    }).delegate('.mp_close','mouseleave',function(){
        $(this).removeClass('mp_close_hover');
    })
    // + hover
    $('.ywgj').delegate('.bt_add_mp','mouseenter',function(){
        $(this).addClass('bt_add_mp_hover');
    }).delegate('.bt_add_mp','mouseleave',function(){
        $(this).removeClass('bt_add_mp_hover');
    })
    $('.ywgj').delegate('.bt_add_dy','mouseenter',function(){
        $(this).addClass('bt_add_dy_hover');
    }).delegate('.bt_add_dy','mouseleave',function(){
        $(this).removeClass('bt_add_dy_hover');
    })



    //删除门牌号
    $('.ywgj').delegate('.mp_close','click',function(){

        $(this).parents('.more_mp').remove();
    });

    //增加门牌号

    $('.ywgj').delegate('.bt_add_mp','click',function(){
        var mp = $(
                '<div class="more_mp">'
                +
                '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
                + '<span class="mp_close">&nbsp;</span>'
                + '</div>');
        $(this).before(mp);
    });


    //增加单元号
    $('.ywgj').delegate('.add_dy_left','click',function(){
        var dy_clone = $(

                '<div class="dy_main">'
                +'<div class="bind_dy">'
                + '<input type="text" name="unit_name[]" value="请输入单元号" msgs="请输入单元号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
                +'</div>'
                +'<div class="bind_mp">'
                +   '<div class="mp_li clearfix">'
                +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
                +'</div>'
                +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
                +'<span class="mp_close">&nbsp;</span>'
                +'</div>'
                +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
                +'<span class="mp_close">&nbsp;</span>'
                +'</div>'
                +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
                +'<span class="mp_close">&nbsp;</span>'
                +'</div>'
                +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
                +'<span class="mp_close">&nbsp;</span>'
                +'</div>'
                +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
                +'<span class="mp_close">&nbsp;</span>'
                +'</div>'
                +'<span class="bt_add_mp"></span>'
                +   '</div>'
                +'</div>'
                +'</div>'
        );
        $(this).siblings().show();
        $(this).parents('.add_dy_main').before(dy_clone);
    });
    //删除单元号
    $('.ywgj').delegate('.add_dy_right','click',function(){
        if($(this).parents('.add_dy').find('.dy_main').length >= 2){
            $(this).parents('.add_dy_main').prev('.dy_main').remove();
        }
        if($(this).parents('.add_dy').find('.dy_main').length == 1){
            $(this).hide();
        }

    });

    //增加楼栋号
    $('.ywgj').delegate('.bt_ad_ld','click',function(){
        var ld_clone = $(
                '<li class="li_ld li_ld_add clearfix li_ld_new">'
                    +'<div class="add_ld">'
                + '<input type="text" name="dong_name[]" value="请输入楼栋号" msgs="请输入楼栋号" onfocus="bindFocus(this,this.value,true)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
        +'<span class="bt_ad_ld">+&nbsp;楼栋号</span>'
        +'<span class="bt_del_ld">-&nbsp;删除楼栋号</span>'
        +'</div>'
        +'<div class="add_dy">'
        +'<div class="dy_main">'
        +'<div class="bind_dy">'
                + '<input type="text" name="unit_name[]" value="请输入单元号" msgs="请输入单元号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
        +'</div>'
        +'<div class="bind_mp">'
        +'<div class="mp_li clearfix">'
        +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
        +'</div>'
        +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
        +'<span class="mp_close">&nbsp;</span>'
        +'</div>'
        +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
        +'<span class="mp_close">&nbsp;</span>'
        +'</div>'
        +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
        +'<span class="mp_close">&nbsp;</span>'
        +'</div>'
        +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
        +'<span class="mp_close">&nbsp;</span>'
        +'</div>'
        +'<div class="more_mp">'
                + '<input class="door" type="text" value="请输入门牌号" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr(\'msgs\') )"/>'
        +'<span class="mp_close">&nbsp;</span>'
        +'</div>'
        +'<span class="bt_add_mp"></span>'
        +'</div>'
        +'</div>'
        +'</div>'
        +'<div class="add_dy_main clearfix">'
        +'<span class="bt_add_dy add_dy_left">+&nbsp;单元号</span>'
        +'<span class="bt_add_dy add_dy_right">-&nbsp;删除单元号</span>'
        +'</div>'
        +'</div>'
        +'</li>');
        $('.ld_ul').append(ld_clone);
    });

    //删除楼栋号
    $('.ywgj').delegate('.bt_del_ld','click',function(){

        $(this).parents('.li_ld_add').remove();
    });

    //添加按钮
    var cmt = {};
    $('#save_submit').live('click',function(){
        //楼栋循环，添加对应的单元
        $('.li_ld').each(function(){
            //定义楼栋
            var dong = {};
            var dong_name = $(this).find('div input').first().val();
            //单元循环，添加对应的门牌号
            $(this).find('.dy_main').each(function(){
                var unit = [];
                var unit_name = $(this).find('div input').first().val();
                $(this).find('.door').each(function(){
                    if($(this).val()!='请输入门牌号'){
                        unit.push($(this).val());
                    }
                });
                if(unit_name!='请输入单元号'){
                    dong[unit_name] = unit;
                }
            });
            if(dong_name!='请输入楼栋号'){
                cmt[dong_name] = dong;
            }
        });
        $.ajax({
            url: "/community/dong_door_unit_add/",
            type: "GET",
            data: {
                cmt: cmt,
                cmt_id:<?php echo $cmt_id; ?>
            },
            success: function(data) {
                if('success'==data){
                    $('#dialog_do_itp').html('添加成功');
                    openWin('js_pop_do_success');
                }else if('no_check'==data){
                    $('#dialog_do_itp').html('请至少填满一个楼栋单元门牌');
                    openWin('js_pop_do_success');
                }else{
                    $('#dialog_do_itp').html('添加失败');
                    openWin('js_pop_do_success');
                }
            }
        });
    });

    $('#close_refresh').click(function(){
        var _url = '/community/cmt_dong/'+<?php echo $cmt_id; ?>;
        if(_url)
        {
            window.location.href = _url;
        }
    });

</script>
</html>
