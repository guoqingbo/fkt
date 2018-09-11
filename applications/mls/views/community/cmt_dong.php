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
        <div class="title">楼盘详情</div>
        <div class="close_pop"></div>
    </div>
    <div class="wrapper ywgj" style="background:#fff;margin: 0 auto;margin-top: 20px;position:relative;">
        <h1 style="margin-bottom: 20px;"><?php echo $cmt_name; ?>的楼栋门牌单元</h1>
        <?php if(empty($all_dong_num)){ ?>
        <p class="no_message"><a>您尚未添加任何楼栋门牌单元哦！请填写以下信息完善楼盘字典</a></p>
        <?php } ?>
        <div class="clearfix" style="margin-bottom: 20px;">
          <?php if(is_full_array($all_dong)){ ?>
                      <?php foreach($all_dong as $key => $value){ ?>
                          <a href="#" class="btn-hui1 btn-left dong_click <?php echo ('1'==$is_lock)?'':'dong_click_2';  ?>" value="<?php echo $value['id']; ?>" style="margin-bottom:20px;"><span><?php echo $value['name']; ?></span></a>
                      <?php } ?>

                  <?php }else{ ?>
                      <ul class="ld_ul">
                          <li class="li_ld clearfix">
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
                  <?php } ?>
        </div>

        <?php if(!is_full_array($all_dong_unit_door)){ ?>
            <div class="all_btns">
                <div class="bt_xg_no clearfix" style="display: block;">
                <button class="button_bc bt_add" id="save_submit" style="float: left">保存资料</button>
                <button class="button_bc" onclick="dong_door_unit_import()" style="float: right">批量导入</button>
                </div>
            </div>
        <?php }else{ ?>
            <div class="all_btns">
                <div class="bt_xg_no clearfix" style="width:340px;display: block;">
                <?php if('1'==$is_lock){ ?>
                    <button class="button_bc bt_readd jll" id="add_dong" style="display:none;">添加楼栋</button>
                    <button class="button_bc  bt_readd_no jll"  id="add_dong3">添加楼栋</button>
                    <button class="button_bc data_import" onclick="dong_door_unit_import()" style="display:none;float: right">批量导入</button>
                <?php }else{ ?>
                    <button class="button_bc bt_readd jll" id="add_dong">添加楼栋</button>
                    <button class="button_bc  bt_readd_no jll"  id="add_dong3" style="display:none;">添加楼栋</button>
                    <button class="button_bc data_import" onclick="dong_door_unit_import()" style="float: right">批量导入</button>
                <?php } ?>
                <button class="button_bc bt_ss jll" <?php echo ('1'==$is_lock)?'style="display:none;float:left;"':'style="display:block;float:left;"'; ?>>锁盘</button>
                <button class="button_bc bt_js jll" <?php echo ('1'==$is_lock)?'style="display:block;float:left;"':'style="display:none;float:left;"'; ?>>解锁</button>
                </div>
            </div>
        <?php } ?>


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

	//确认导入
	function sure()
	{
	     var path = $("#xx1x").contents().find("#path").val();
	     if(path){
    	     $("#xx1x").contents().find("body").empty();
    	     openWin('jss_pop_sure',ajax_import(path,'community',<?=$cmt_id?>));
	     }else{
	        openWin('jss_pop_error');
	    }
	}



  <?php if(is_full_array($all_dong_unit_door)){ ?>
    //禁止input输入
    $('input[type="text"]').attr('disabled','true');

    //隐藏元素
    $('.add_dy_main').hide();
    $('.mp_close').hide();
    $('.bt_add_mp').hide();
    $('.bt_ad_ld').hide();
    $('.bt_del_ld').hide();

    //点击修改
    $('.ywgj').delegate('#modify_button','click',function(){
        $('input').removeAttr('disabled');
        $('.add_dy_main').show();
        $('.mp_close').show();
        $('.bt_add_mp').show();
        $('.bt_ad_ld').show();
        $('.bt_del_ld').show();
        $('#modify_button').hide();
        $('.bt_readd').removeClass('button_hui');
     });

    //锁盘
     $('.ywgj').delegate('.bt_ss','click',function(){
            $('input').attr('disabled','true');
            $('.add_dy_main').hide();
            $('.mp_close').hide();
            $('.bt_add_mp').hide();
            $('.bt_ad_ld').hide();
            $('.bt_del_ld').hide();
            $('#add_dong3').show();
            $('#add_dong').hide();
            //锁盘ajax请求
            $.ajax({
                url: "/community/deal_is_lock/",
                type: "GET",
                data: {
                    type: 1,
                    cmt_id:<?php echo $cmt_id; ?>
                },
                success: function(data) {
                    if('success'==data){
                        $('.bt_js').show();
                        $('.bt_ss').hide();
                        $('.bt_readd').addClass('button_hui');
                        $('.data_import').hide();
                        $('.dong_click').removeClass('dong_click_2');
                    }
                }
            });
     });

    //解锁
     $('.ywgj').delegate('.bt_js','click',function(){
             $('#add_dong').show();
             $('#add_dong3').hide();
             $('.bt_ss').show();
             $('.bt_js').hide();
            //锁盘ajax请求
            $.ajax({
                url: "/community/deal_is_lock/",
                type: "GET",
                data: {
                    type: 2,
                    cmt_id:<?php echo $cmt_id; ?>
                },
                success: function(data) {
                    if('success'==data){
                        $('.bt_readd').removeClass('button_hui');
                        $('.data_import').show();
                        $('.dong_click').addClass('dong_click_2');
                    }
                }
            });
     });


  <?php } ?>

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
                '<li class="li_ld li_ld_add clearfix">'
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

    $('.dong_click_2').live('click',function(){
        var dong_id = $(this).attr('value');
        var _url = '/community/dong_door_unit/'+dong_id+'/'+<?php echo $cmt_id; ?>;

        if(_url)
        {
            window.location.href = _url;
        }
    });

    $('#add_dong').click(function(){
        var _url = '/community/add_dong/'+<?php echo $cmt_id; ?>;
        if(_url)
        {
            window.location.href = _url;
        }
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
