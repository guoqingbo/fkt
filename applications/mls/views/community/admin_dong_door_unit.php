<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/lpsj.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="wrapper ywgj" style="margin: 0 auto;margin-top: 20px;">
        <h1><?php echo $cmt_name; ?>的楼栋门牌单元</h1>
        <?php if(empty($all_dong_num)){ ?>
        <p class="no_message"><a>您尚未添加任何楼栋门牌单元哦！请填写以下信息完善楼盘字典</a></p>
        <?php } ?>
        <?php if(is_full_array($all_dong_unit_door)){ ?>
            <ul class="ld_ul">
                <?php foreach($all_dong_unit_door as $key => $value){ ?>
                <li class="li_ld clearfix">
                    <div class="add_ld">
                        <input type="text" name="dong_name[]" value="<?php echo $key; ?>" msgs="请输入楼栋号" onfocus="bindFocus(this,this.value,true)"
                               onblur="bindBlur(this,$(this).attr('msgs'))"/>
                        <span class="bt_ad_ld">+&nbsp;楼栋号</span>
                    </div>
                    <div class="add_dy">
                        <?php foreach($value as $key2=>$value2){ ?>
                        <div class="dy_main">
                            <div class="bind_dy">
                                <input type="text" name="unit_name[]" value="<?php echo $key2; ?>" msgs="请输入单元号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr('msgs'))"/>
                            </div>
                            <div class="bind_mp clearfix">
                                <div class="mp_li clearfix">
                                    <?php foreach($value2 as $key3=>$value3){ ?>
                                    <div class="more_mp">
                                        <input class="door" type="text" value="<?php echo $value3['name']; ?>" msgs="请输入门牌号" onfocus="bindFocus(this,this.value)" onblur="bindBlur(this,$(this).attr('msgs'))"/>
                                        <?php if($key3 > 0){ ?>
                                        <span class="mp_close">&nbsp;</span>
                                        <?php } ?>
                                    </div>

                                    <?php } ?>
                                     <span class="bt_add_mp"></span>
                                </div>
                            </div>
                        </div>

                        <?php } ?>
                         <div class="add_dy_main clearfix">
                            <span class="bt_add_dy add_dy_left">+&nbsp;单元号</span>
                            <span class="bt_add_dy add_dy_right">-&nbsp;删除单元号</span>
                         </div>

                    </div>
                </li>
                <?php } ?>
            </ul>
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

        <div class="all_btns">
            <div class="bt_xg clearfix" style="<?php if(is_full_array($all_dong_unit_door)){echo 'display: block;';} ?>">
                <?php if('1'==$is_lock){ ?>
                <button class="button_bc bt_readd" id="modify_button" style="display:none;">修改资料</button>
                <button class="button_bc  bt_readd_no"  id="modify_button3">修改资料</button>
                <?php }else{ ?>
                <button class="button_bc bt_readd" id="modify_button">修改资料</button>
                <button class="button_bc  bt_readd_no"  id="modify_button3" style="display:none;">修改资料</button>
                <?php } ?>
                <button class="button_bc bt_readd" id="modify_button_2" style="display:none;">保存修改</button>
                <button class="button_bc bt_ss" <?php echo ('1'==$is_lock)?'style="display:none;"':'style="display:block;"'; ?>>锁盘</button>
                <button class="button_bc bt_js" <?php echo ('1'==$is_lock)?'style="display:block;"':'style="display:none;"'; ?>>解锁</button>
            </div>
            <div class="bt_xg_no clearfix" style="display: none;">
                <button class="button_bc bt_readd_no">修改资料</button>
                <button class="button_bc bt_js">解锁</button>
            </div>
            <?php if(!is_full_array($all_dong_unit_door)){ ?>
            <button class="button_bc bt_add" id="save_submit">保存资料</button>
            <?php } ?>
        </div>
    </div>
</body>
<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" id="close_refresh"></a>
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

<script>
    window.onload = function(){

          setInterval(function(){
                    var winHeight = $(window).height()-43;
                    $('.wrapper').css('height',winHeight);
          },500);

    }

  <?php if(is_full_array($all_dong_unit_door)){ ?>
    //禁止input输入
    $('input').attr('disabled','true');

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
        $('#modify_button_2').show();
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
            $('#modify_button3').show();
            $('#modify_button_2').hide();
            $('#modify_button').hide();
            //锁盘ajax请求
            $.ajax({
                url: "/community/deal_is_lock_wh/",
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
                    }
                }
            });
     });

    //解锁
     $('.ywgj').delegate('.bt_js','click',function(){

             $('#modify_button').show();
             $('#modify_button3').hide();
             $('.bt_ss').show();
             $('.bt_js').hide();
            //锁盘ajax请求
            $.ajax({
                url: "/community/deal_is_lock_wh/",
                type: "GET",
                data: {
                    type: 2,
                    cmt_id:<?php echo $cmt_id; ?>
                },
                success: function(data) {
                    if('success'==data){
                        $('.bt_readd').removeClass('button_hui');
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
            url: "/community/admin_dong_door_unit_add/",
            type: "GET",
            data: {
                cmt: cmt,
                cmt_id:<?php echo $cmt_id; ?>
            },
            success: function(data) {
                if('success'==data){
                    $('#dialog_do_itp').html('添加成功');
                    openWin('js_pop_do_success');
                }else{
                    $('#dialog_do_itp').html('添加失败');
                    openWin('js_pop_do_success');
                }
            }
        });
    });

    //提交修改
    var cmt = {};
    $('#modify_button_2').live('click',function(){
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
                    unit.push($(this).val());
                });
                dong[unit_name] = unit;
            });
            cmt[dong_name] = dong;
        });
        $.ajax({
            url: "/community/admin_dong_door_unit_modify/",
            type: "GET",
            data: {
                cmt: cmt,
                cmt_id:<?php echo $cmt_id; ?>
            },
            success: function(data) {
                if('success'==data){
                    $('#dialog_do_itp').html('修改成功');
                    openWin('js_pop_do_success');
                }else{
                    $('#dialog_do_itp').html('修改失败');
                    openWin('js_pop_do_success');
                }
            }
        });
    });

</script>
</html>
