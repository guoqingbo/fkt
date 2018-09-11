<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>房源详情信息打印</title>
</head>
<body>
<div class="house_info_print">
    <dl class="house_info_print_title">
        <dd>房源打印</dd>
        <dt></dt>
    </dl>
    <!--打印信息-->
    <form action="<?php echo MLS_SIGN_URL;?>/sell/pic_deal" method="post" target="_blank">
        <input type="hidden" value="<?php echo $house_details['id']; ?>" name="house_id"/>
    <div class="house_info_print_con">
        <!--左侧信息输入-->
        <div class="house_info_print_con_left">
            <dl>
                <dd>样式：</dd>
                <dt id="tab_v_h">
                    <p class="house_info_print_con_left_p"><input type="radio" checked name="type" value="1" /><b>横向</b></p>
                    <p class="house_info_print_con_left_p"><input type="radio" name="type" value="2" /><b>竖向</b></p>

                </dt>
            </dl>
            <dl>
                <dd>备注：</dd>
                <dt>
                    <p class="house_info_print_con_left_p"><input id="remark_input" type="text" value="" class="print_con_left_input" name="remark"/></p>
                    <div id="remark_error" style="display: none;font-size:0;"></div>
                </dt>
            </dl>
            <dl>
                <dd>门店：</dd>
                <dt>
                    <p class="house_info_print_con_left_p"><input id="agency_name_input" type="text" value="<?php echo $house_details['agency_name']; ?>" class="print_con_left_input" name="agency_name"/></p>
                    <div id="agency_name_error" style="diplay:none;font-size:0;"></div>
                </dt>
            </dl>
            <dl>
                <dd>楼盘：</dd>
                <dt>
                <p class="house_info_print_con_left_p"><input id="cmt_name_input" type="text" value="<?php echo $house_details['block_name']; ?>" class="print_con_left_input" name="cmt_name"/></p>
                <div id="cmt_name_error" style="display: none;"></div>
                </dt>
            </dl>
            <dl>
                <dd>微店：</dd>
                <dt class="show_ewm">
                    <p class="house_info_print_con_left_p"><input type="radio" checked name="is_qrcode" value="1" /><b>展示</b></p>
                    <p class="house_info_print_con_left_p"><input type="radio" name="is_qrcode" value="2" /><b>不展示</b></p>
                </dt>
            </dl>
			<dl>
                <dd style="width:72px;">连接打印机：</dd>
                <dt>
                    <p class="house_info_print_con_left_p"><input type="radio" checked name="is_print" value="1" /><b>是</b></p>
                    <p class="house_info_print_con_left_p"><input type="radio" name="is_print" value="2" /><b>否</b></p>
                </dt>
            </dl>
            <input type="submit" value="保存图片" class="print_con_btn"/>
			<input type="hidden" name="qrcode" value="<?php echo $house_details['qrcode'];?>" />
        </div>
        <!--右侧展示-->
        <div class="house_info_print_con_right">
            <!--横向打印-->
            <div class="print_con_right_horizontal">
                <dl class="horizontal_title">
                    <dd class="agency_name_pic"><?php echo $house_details['agency_name']; ?></dd>
                    <dt>售</dt>
                </dl>
                <div style="float: left;display:inline;height:260px;overflow: hidden;width:100%;">
                <div class="horizontal_con_l">
                    <span class="horizontal_con_name">
                        <p class="cmt_name_pic"><?php echo $house_details['block_name'];?></p>
                        <p class="pcolor_n price_pic"><?php echo intval($house_details['price']);?>万</p>
                        <input type="hidden" value="<?php echo intval($house_details['price']);?>万" name="price"/>
                    </span>
                    <div class="horizontal_con_date">
                        <dl>
                            <dd class="buildarea_pic"><?php echo round($house_details['buildarea']);?>平方米</dd>
                            <input type="hidden" value="<?php echo round($house_details['buildarea']);?>平方米" name="buildarea"/>
                            <dt class="room_pic_pic"><?php echo $house_details['room'];?>-<?php echo $house_details['hall'];?>-<?php echo $house_details['toilet'];?></dt>
                            <input type="hidden" value="<?php echo $house_details['room'];?>-<?php echo $house_details['hall'];?>-<?php echo $house_details['toilet'];?>" name="room_pic"/>
                        </dl>
                        <dl>
                            <dd class="fitment_pic"><?php echo $fitment_config[$house_details['fitment']];?></dd>
                            <input type="hidden" value="<?php echo $fitment_config[$house_details['fitment']];?>" name="fitment" />
                            <dt class="forward_pic"><?php echo $forward_config[$house_details['forward']];?></dt>
                            <input type="hidden" value="<?php echo $forward_config[$house_details['forward']];?>" name="forward" />
                        </dl>
                        <span class="horizontal_con_date_sm remark_pic"></span>
                        <span class="horizontal_con_date_line broker_name_pic">经纪人 <?php echo $house_details['broker_name'];?> <?php echo $house_details['broker_phone'];?><img style="width: 25px;height:25px;padding-left:4px;" src="<?php echo $house_details['qrcode']; ?>" id="qrcode" /></span>
                        <input type="hidden" value="经纪人 <?php echo $house_details['broker_name'];?> <?php echo $house_details['broker_phone'];?>" name="broker_name"/>
                    </div>
                </div>

                <!--图形部分-->
                <div class="horizontal_con_r"  style="float:right;padding:0;">
                    <span><?php if(!empty($house_details['shineipic'])){ ?><img src="<?php echo $house_details['shineipic']; ?>"/><?php } ?></span>
                    <span><?php if(!empty($house_details['huxingpic'])){ ?><img src="<?php echo $house_details['huxingpic']; ?>"/><?php } ?></span>
                    <input type="hidden" value="<?php echo $house_details['shineipic']; ?>" name="shinei"/>
                    <input type="hidden" value="<?php echo $house_details['huxingpic']; ?>" name="huxing"/>
                </div>
                </div>
            </div>

            <!--竖向打印-->
            <div class="print_con_right_vertical">
                <dl class="horizontal_title vertical_W">
                    <dd class="agency_name_pic"><?php echo $house_details['agency_name']; ?></dd>
                    <dt>售</dt>
                </dl>
                <span class="horizontal_con_name name_vertical_W">
                    <p class="cmt_name_pic"><?php echo $house_details['block_name'];?></p>
                    <p class="pcolor_n price_pic"><?php echo intval($house_details['price']);?>万</p>
                </span>
                <div class="vertical_con_date">
                    <dl>
                        <dd class="buildarea_pic"><?php echo round($house_details['buildarea']);?>平方米</dd>
                        <dt class="room_pic"><?php echo $house_details['room'];?>-<?php echo $house_details['hall'];?>-<?php echo $house_details['toilet'];?></dt>
                    </dl>
                    <dl>
                        <dd class="fitment_pic"><?php echo $fitment_config[$house_details['fitment']];?></dd>
                        <dt class="forward_pic"><?php echo $forward_config[$house_details['forward']];?></dt>
                    </dl>
                    <span class="vertical_con_date_con_date_sm hb_font remark_pic"></span>
                    <span class="horizontal_con_date_line padd_H broker_name_pic" style="width:275px;">经纪人 <?php echo $house_details['broker_name'];?> <?php echo $house_details['broker_phone'];?><img id="qrcode_shu" style="width: 25px;height:25px;padding-left:4px;" src="<?php echo $house_details['qrcode']; ?>"/></span>
                </div>
                <!--图形部分-->
                <div class="horizontal_con_r vertical_img_w">
                    <span><?php if(!empty($house_details['shineipic'])){ ?><img src="<?php echo $house_details['shineipic']; ?>" id="shineipic" /><?php } ?></span>
                    <span><?php if(!empty($house_details['huxingpic'])){ ?><img src="<?php echo $house_details['huxingpic']; ?>" id="huxingpic" /><?php } ?></span>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
</body>
<script type="text/javascript">
var agency_name_max = 18;
function agency_name_check(){
    var agency_value = $('#agency_name_input').val();
    var agency_num = agency_value.length;
    //不能超过18个字
    if(agency_num > agency_name_max){
        $('#agency_name_error').html('门店不能超过'+agency_name_max+'个字');
        $('#agency_name_error').show();
        $('#agency_name_error').css("font-size","12px");
        agency_value = agency_value.substr(0,agency_name_max);
    }else{
        $('#agency_name_error').hide();
        $('#agency_name_error').css("font-size","0");
    }
    $('.agency_name_pic').html(agency_value);
}

$(function(){
    //微店是否展示
    $(".show_ewm").find("p").on("click",function(){
        //alert("bcc");
        $(this).find("input").attr("checked","checked");
        var is_qrcode = $(this).index();
        //alert(is_qrcode);

        if(1==is_qrcode){
            $('#qrcode').hide();
            $("#qrcode_shu").hide();

        }else{
            $('#qrcode').show();
            $("#qrcode_shu").show();

        }
    })

    //横竖切换事件
    $('#tab_v_h').find("p").on("click",function(){
       $(this).find("input").attr("checked","checked");
        var index_num = $(this).index();

        if(index_num == 0){
            agency_name_max = 18;
            $(".print_con_right_vertical").css("display","none");
            $(".print_con_right_horizontal").css("display","block");
            //横竖切换事件时，验证提示。
            agency_name_check();
        }else{
            agency_name_max = 13;
            $(".print_con_right_vertical").css("display","block");
            $(".print_con_right_horizontal").css("display","none");
            //横竖切换事件时，验证提示。
            agency_name_check();
        }
    })

    //备注
    $('#remark_input').on('keyup',function(){
        var remark_value = $(this).val();
        var remark_num = remark_value.length;
        //不能超过12个字
        if(remark_num > 12){

            $('#remark_error').html('备注不能超过12个字');
            $('#remark_error').show();
            $('#remark_error').css("font-size","12px");
            remark_value = remark_value.substr(0,12);
        }else{

            $('#remark_error').hide();
            $('#remark_error').css("font-size","0");
        }
        $('.remark_pic').html(remark_value);
    });
    //门店
    $('#agency_name_input').on('keyup',function(){
        agency_name_check();
    });
    //楼盘
    $('#cmt_name_input').on('keyup',function(){
        var cmt_value = $(this).val();
        var cmt_num = cmt_value.length;
        //不能超过15个字
        if(cmt_num > 15){
            $('#cmt_name_error').html('楼盘不能超过15个字');
            $('#cmt_name_error').show();
            $('#agency_name_error').css("font-size","12px");
            cmt_value = cmt_value.substr(0,15);
            $(".horizontal_con_name p:first-child").css("font-size","17px");
        }else{
            $('#cmt_name_error').hide();
            $('#agency_name_error').css("font-size","0");
            //超过10个字，根据个数设置字体大小。
            switch(cmt_num)
            {
                case 11:
                    $(".horizontal_con_name p:first-child").css("font-size","23px");
                  break;
                case 12:
                   $(".horizontal_con_name p:first-child").css("font-size","21px");
                  break;
                case 13:
                  $(".horizontal_con_name p:first-child").css("font-size","20px");
                  break;
                case 14:
                    $(".horizontal_con_name p:first-child").css("font-size","18px");
                  break;
                case 15:
                   $(".horizontal_con_name p:first-child").css("font-size","17px");
                  break;
                default:
                    $(".horizontal_con_name p:first-child").css("font-size","23px");
                  break;
            }
        }
        $('.cmt_name_pic').html(cmt_value);
    });

})

</script>
</html>
