<?php require APPPATH . 'views/header.php'; ?>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/uploadpic.js"></script>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/swf/swfupload.js"></script>
<link href="<?=MLS_SOURCE_URL ?>/mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $title ?></h1>
            </div>
        </div>
        <div class="forms">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
            <form action="/rent_house/add/" id="jsUpForm" method="post">
                <div class="forms_details_fg">
                    <div class="clearfix item_fg js_fields">
                        <span class="text_fg"><b class="red">*</b>物业类型：</span>
                        <?php
                        foreach ($config['sell_type'] as $key => $value) {
                            if ($house_detail['sell_type'] == $key) {
                                $checked = 'checked';
                            } else {
                                $checked = '';
                            }
                            echo '<input name="sell_type" type="radio" value="' . $key . '" id="js_house_type_' . $key . '" ' . $checked . ' disabled="disabled">' . $value;
                        }?>
                    </div>
                    <br>
                    <div class="clearfix item_fg">
                        <span class="text_fg"><b class="red">*</b>楼盘名称：</span>
                        <input name="block_name" id="block_name" value="<?php echo $house_detail['block_name']; ?>" class="input_text w150" type="text" placeholder="输入拼音或汉字筛选" disabled="disabled">
                        <input name="block_id" id="block_id" value="" type="hidden" onblur="check_unique_house()">
                        <script type="text/javascript">
                            $(function() {
                                $("#block_name").autocomplete({
                                    source: function(request, response) {
                                        var term = request.term;
                                        $.ajax({
                                            url: "/community/get_cmtinfo_by_kw/",
                                            type: "GET",
                                            dataType: "json",
                                            data: {
                                                keyword: term
                                            },
                                            success: function(data) {
                                                //判断返回数据是否为空，不为空返回数据。
                                                if (data[0]['id'] != '0') {
                                                    response(data);
                                                } else {
                                                    response(data);
                                                }
                                            }
                                        });
                                    },
                                    minLength: 1,
                                    removeinput: 0,
                                    select: function(event, ui) {
                                        if (ui.item.id > 0) {
                                            var blockname = ui.item.label;
                                            var id = ui.item.id;
                                            var streetid = ui.item.streetid;
                                            var streetname = ui.item.streetname;
                                            var dist_id = ui.item.dist_id;
                                            var districtname = ui.item.districtname;
                                            var address = ui.item.address;
                                            var build_date = ui.item.build_date;

                                            //操作
                                            $("#block_id").val(id);
                                            $("#select_q").val(districtname);
                                            $("#district_id").val(dist_id);
                                            $("#select_b").val(streetname);
                                            $("#street_id").val(streetid);
                                            $("#address").val(address);
                                            $("#block_name").val(blockname);
                                            $('#buildyear').val(build_date);
                                            removeinput = 2;
                                        } else {
                                            //新增楼盘弹框中内容设置为空
                                            $('#js_pop_add_new_block input').val('');
                                            $('#js_pop_add_new_block select').each(function() {
                                                $(this).children('option').first().attr('selected', 'selected');
                                            });
                                            $('#js_pop_add_new_block textarea').val('');
                                            $('#js_pop_add_new_block input[type="checkbox"]').attr('checked', false);
                                            openWin('js_pop_add_new_block');
                                            removeinput = 1;
                                        }
                                    },
                                    close: function(event) {
                                        if (typeof (removeinput) == 'undefined' || removeinput == 1) {
                                            $("#block_name").val("");
                                            $("#block_id").val("");
                                        }
                                    }
                                });
                            });
                        </script>
                        <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;区属：</span>
                        <input class="input_text w60" name="select_q" value="<?php echo $house_detail['districtname']; ?>" id="select_q" type="text" readonly >
                        <input name="district_id" id="district_id"  value="<?php echo $house_detail['district_id']; ?>" type="hidden">
                        <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;板块：</span>
                        <input class="input_text w60" id="select_b" name="select_b" value="<?php echo $house_detail['streetname']; ?>" type="text" readonly >
                        <input name="street_id" id="street_id" value="<?php echo $house_detail['street_id']; ?>" type="hidden">
                        <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;地址：</span>
                        <input class="input_text w260" id="address" name="address" value="<?php echo $house_detail['address']; ?>" type="text" readonly >
                    </div>
                </div>
                <div class="forms_details_fg forms_details_fg_bg clearfix">
                    <h3 class="h3">业主信息(加密)<span class="tip_text"></span></h3>
                    <div class="item_fg clearfix">
                        <span class="text_fg"><b class="red">*</b>栋座：</span>
                        <input class="input_text w80" name="dong" id="dong" value="<?php echo $house_detail['dong']; ?>" type="text" onblur="check_unique_house()" disabled="disabled">
                        <span class="text_fg"><b class="red">*</b>&nbsp;&nbsp;单元：</span>
                        <input class="input_text w80" name="unit" id="unit" value="<?php echo $house_detail['unit']; ?>" type="text" onblur="check_unique_house()" disabled="disabled">
                        <span class="text_fg"><b class="red">*</b>&nbsp;&nbsp;门牌：</span>
                        <input class="input_text w80" type="text" id="door" value="<?php echo $house_detail['door']; ?>" name="door" onblur="check_unique_house()" disabled="disabled">
                        <br><br>
                        <span class="text_fg"><b class="red">*</b>业主姓名：</span>
                        <input class="input_text w80" type="text" value="<?php echo $house_detail['owner']; ?>" name="owner" disabled="disabled">
                        <span class="text_fg">&nbsp;&nbsp;身份证号：</span>
                        <input class="input_text w130" name="idcare" value="<?php echo $house_detail['idcare']; ?>" type="text" maxlength="18" disabled="disabled">
                        <br><br>
                        <span class="text_fg"><b class="red">*</b>业主电话：</span>
                        <input class="input_text w80" type="text" name="telno1" value="<?php echo $house_detail['telno1']; ?>" id="tel01" disabled="disabled">
                        <a href="javascript:void(0)" class="iconfont addTel" id="addTel01">&#xe608;</a>
                        <div class="y_fg js_fields" <?php if (empty($house_detail['telno2'])) {
                            echo 'style="display:none"';
                        }?>>
                            <input class="input_text w80" type="text" value="<?php echo $house_detail['telno2']; ?>" name="telno2" id="tel02" disabled="disabled">
                            <a href="javascript:void(0)" class="iconfont delTel" id="delTel02">&#xe60c;</a> <a href="javascript:void(0)" class="iconfont addTel" id="addTel02">&#xe608;</a>
                        </div>
                        <div class="y_fg js_fields"  <?php if (empty($house_detail['telno3'])) {
                            echo 'style="display:none"';
                        }?>>
                            <input class="input_text w80" type="text" value="<?php echo $house_detail['telno3']; ?>" name="telno3" id="tel03" disabled="disabled">
                            <a href="javascript:void(0)" class="iconfont delTel"  id="delTel03">&#xe60c;</a>
                        </div>
                    </div>
                    <script>
                        $(function() {
                            $("#addTel01").click(function() {
                                $("#tel02").parent().show();
                            });
                            $("#addTel02").click(function() {
                                $("#tel03").parent().show();
                            });
                            $("#delTel02,#delTel03").click(function() {
                                $(this).parent().hide();
                                $(this).siblings(".input_text").val('');
                            })

                        })
                    </script>
                    <br>
                    <span class="text_fg">书证号：</span>
                    <input class="input_text w80" value="<?php echo $house_detail['proof']; ?>" name="proof" type="text" disabled="disabled">
                    <span class="text_fg">丘地号：</span>
                    <input class="input_text w80" name="mound_num" value="<?php echo $house_detail['mound_num']; ?>"  type="text" disabled="disabled">
                    <span class="text_fg">备案号：</span>
                    <input class="input_text w80" name="record_num" value="<?php echo $house_detail['record_num']; ?>" type="text" disabled="disabled">
                </div>
                <script type="text/javascript">
                    function check_unique_house()
                    {
                        var block_id = $.trim($('#block_id').val());
                        var dong = $.trim($('#dong').val());
                        var unit = $.trim($('#unit').val());
                        var door = $.trim($('#door').val());

                        if (block_id != '' && dong != '' && unit != '' && door != '')
                        {
                            $.ajax({
                                url: "/sell/check_unique_house/",
                                type: "GET",
                                dataType: "HTML",
                                data: {block_id: block_id, dong: dong, unit: unit, door: door},
                                success: function(data)
                                {
                                    //判断返回数据是否为空，不为空返回数据。
                                    if (data == 0)
                                    {
                                        $('.tip_text').html('非重复房源，可以录入');
                                    }
                                    else
                                    {
                                        $('.tip_text').html('<font style="color:red;">您的库中已有该房源，不可重复录入</font>');
                                    }
                                }
                            });
                        }
                    }

                    function show_input(s_obi, h_obj)
                    {
                        $("#" + s_obi).show();
                        $("#" + h_obj).hide();
                    }
                </script>
                <div class="forms_details_fg forms_details_fg_bg clearfix">
                    <h3 class="h3">房源信息</h3>
                    <div class="item_fg clearfix">
                        <span class="text_fg"><b class="red">*</b>状态：</span>
                        <?php
                        foreach ($config['status'] as $key => $val) {
                            echo '<input type="radio"';
                            if ($key == $house_detail['status']) {
                                echo ' checked ';
                            }
                            echo ' name="status" value="' . $key . '" disabled="disabled"> ' . $val . '';
                        }?>
                        <span class="text_fg"><b class="red">&nbsp;&nbsp;*</b>房源性质：</span>
                        <?php
                        foreach ($config['nature'] as $key => $val) {
                            echo '<input type="radio"';
                            if ($key == $house_detail['nature']) {
                                echo ' checked ';
                            }
                            echo ' name="nature" value="' . $key . '" disabled="disabled"> ' . $val . '';
                        }?>
                    </div><br>
					<?php if($house_detail['sell_type']<3){?>
                    <div class="item_fg clearfix">
                        <span class="text_fg"><b class="red">*</b>户型：</span>
                        <select class="select" name="room">
                            <?php
                            foreach ($config['room'] as $key => $val) {
								if($house_detail['room']){
									echo '<option value="' . $key . '"';
									if ($key == $house_detail['room']) {
										echo 'selected';
									}
									echo ' disabled="disabled">' . $val . '</option>';
								}else{
									echo "<option value='0' disabled='disabled' selected>0</option>";
								}
                            }?>
                        </select>
                        <span class="y_fg y_fg_p5">室</span>
                        <select class="select" name="hall">
                            <?php
                            foreach ($config['hall'] as $key => $val) {
								if($house_detail['hall']){
									echo '<option value="' . $key . '"';
									if ($key == $house_detail['hall']) {
										echo 'selected';
									}
									echo ' disabled="disabled">' . $val . '</option>';
								}else{
									echo "<option value='0' disabled='disabled' selected>0</option>";
								}
                            }?>
                        </select>
                        <span class="y_fg y_fg_p5">厅</span>
                        <select class="select" name="toilet">
                            <?php
                            foreach ($config['toilet'] as $key => $val) {
								if($house_detail['toilet']){
									echo '<option value="' . $key . '"';
									if ($key == $house_detail['toilet']) {
										echo 'selected';
									}
									echo ' disabled="disabled">' . $val . '</option>';
								}else{
									echo "<option value='0' disabled='disabled' selected>0</option>";
								}
                            }
                            ?>
                        </select>
                        <span class="y_fg y_fg_p5">卫</span>
                        <select class="select" name="kitchen">
                            <?php
                            foreach ($config['kitchen'] as $key => $val) {
								if($house_detail['kitchen']){
									echo '<option value="' . $key . '"';
									if ($key == $house_detail['kitchen']) {
										echo 'selected';
									}
									echo ' disabled="disabled">' . $val . '</option>';
								}else{
									echo "<option value='0' disabled='disabled' selected>0</option>";
								}
                            }
                            ?>
                        </select>
                        <span class="y_fg y_fg_p5">厨</span>
                        <select class="select" name="balcony">
                            <?php
                            foreach ($config['balcony'] as $key => $val) {
								if($house_detail['balcony']){
									echo '<option value="' . $key . '"';
									if ($key == $house_detail['balcony']) {
										echo 'selected';
									}
									echo ' disabled="disabled">' . $val . '</option>';
								}else{
									echo "<option value='0' disabled='disabled' selected>0</option>";
								}
                            }
                            ?>
                        </select>
                        <span class="y_fg y_fg_p5">阳台</span>
                    </div>
					<?php } ?>
                    <br>
                    <div class="text_fg "><b class="red">*</b>是否合作：
                        <input type="radio" name="isshare" id="js_gs_01" value="1" <?php
                            if ($house_detail['isshare'] == '1') {
                                echo 'checked';
                            }?> disabled="disabled">是
                        <input type="radio" name="isshare" id="js_gs_02" value="0" <?php
                           if ($house_detail['isshare'] == '0') {
                               echo 'checked';
                           }?> disabled="disabled">否
                    </div>
                    <br>
                    <div class="item_fg clearfix " id="js_show_yj" <?php if(empty($house_detail['isshare'])){echo "style='display:none;'";}?>>
                        房源合作佣金分配<span class="fy_text">获得本次交易双方佣金总金额：</span>
                        <span class="left">甲方</span>
                        <input class="input_text jzf w60 left" id="a_ratio" value="<?php echo !empty($ratio_info['a_ratio']) ? strip_end_0($ratio_info['a_ratio']) : '';?>" name="a_ratio" type="text" disabled="disabled"/>%
                        <span class="left" style="padding-left:15px;">&nbsp;&nbsp;乙方</span>
                        <input class="input_text jzf w60" readonly name="b_ratio" value="<?php echo !empty($ratio_info['b_ratio']) ? strip_end_0($ratio_info['b_ratio']) : '';?>" id="b_ratio" type="text" disabled="disabled">%
                    </div>
                    <div class="left dq_fy">
                        <span class="left">支付交易总金额：</span>
                        <span class="left">买方</span>
                        <input class="input_text jzf w60" name="buyer_ratio" value="<?php echo !empty($ratio_info['buyer_ratio']) ? strip_end_0($ratio_info['buyer_ratio']) : '';?>" id="buyer_ratio" type="text" disabled="disabled">%
                        <span class="left" style="padding-left:15px;">卖方</span>
                        <input class="input_text jzf w60" name="seller_ratio" value="<?php echo !empty($ratio_info['seller_ratio']) ? strip_end_0($ratio_info['seller_ratio']) : '';?>" id="seller_ratio" type="text" disabled="disabled">%
                    </div>
                    <br>
                    <div class="item_fg clearfix">
						<?php if($house_detail['sell_type']<5){?>
                        <span class="text_fg"><b class="red">*</b>朝向：</span>
                        <?php
                        foreach ($config['forward'] as $key => $val) {
                            echo '<input type="radio"';
                            if ($key == $house_detail['forward']) {
                                echo ' checked ';
                            }
                            echo ' name="forward" value="' . $key . '" disabled="disabled"> ' . $val . '';
                        }
                        ?>
                        <br><br>
						<?php } ?>
                        <div class="left">
                            <span class="text_fg"><b class="red">*</b>楼层：</span>
                            <input type="radio"  name="floor_type" value="1" <?php
                               if ($house_detail['floor_type'] == '1') {
                                   echo "checked";
                               }?> onChange="show_input('d_input', 'y_input')" disabled="disabled">单层
                            <input class="input_text w20" name="floor" value="<?php echo $house_detail['floor']; ?>" type="text" disabled="disabled">
                            &nbsp;&nbsp;
                            <input type="radio" name="floor_type"  value="2"  <?php
                               if ($house_detail['floor_type'] == '2') {
                                   echo "checked";
                               }?> onChange="show_input('y_input', 'd_input')" disabled="disabled">跃层
                            <input class="input_text w20" name="floor2" value="<?php echo $house_detail['floor']; ?>"  type="text" disabled="disabled">
                            <span class="y_fg y_fg_p5">一</span>
                            <input class="input_text w20 js_fields" value="<?php echo $house_detail['subfloor']; ?>"  name="subfloor" type="text" disabled="disabled">
                            <span class="y_fg y_fg_p_l">&nbsp;&nbsp;总楼层：</span>
                            <input class="input_text w50" type="text" name="totalfloor" value="<?php echo $house_detail['totalfloor']; ?>" id="z_louceng" disabled="disabled">
                        </div>
                    </div>
                    <br>
                    <div class="item_fg clearfix">
						<?php if($house_detail['sell_type']<5){?>
                        <span class="text_fg"><b class="red">*</b>装修：</span>
                        <?php
                        foreach ($config['fitment'] as $key => $val) {
                            echo '<input type="radio"';
                            if ($key == $house_detail['fitment']) {
                                echo ' checked ';
                            }
                            echo ' name="fitment" value="' . $key . '" disabled="disabled"> ' . $val . '';
                        }
                        ?>
                        <span class="text_fg">
						<?php } ?>
						<b class="red">*</b>房龄：</span>
                        <select class="select" name="buildyear" id="buildyear">
                            <option value="0" selected>请选择</option>
                            <?php
                            for ($_i = 1970; $_i <= 2015; $_i++) {
                                echo '<option value="' . $_i . '"';
                                if ($_i == $house_detail['buildyear']) {
                                    echo "selected";
                                }
                                echo ' disabled="disabled">' . $_i . '年</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <br>
                    <div class="item_fg clearfix">
                        <span class="text_fg"><b class="red">*</b>面积：</span>
                        <input class="input_text w60" name="buildarea" id="buildarea" type="text" value="<?php echo $house_detail['buildarea']; ?>" onblur="get_avgprice();" disabled="disabled">
                        <span class="y_fg y_fg_p_l_5">平方米</span>
                        <span class="text_fg"><b class="red">&nbsp;&nbsp;&nbsp;&nbsp;*</b>租金：</span>
                        <input class="input_text w60" name="price" type="text" id="price" value="<?php	if($house_detail['price_danwei']>0){
									echo $house_detail['price_buildarea'];
									//($house_detail['price']/$house_detail['buildarea']/30);
								}else{
									echo $house_detail['price'];
									/*floor($house_detail['price'])==$house_detail['price']?intval($house_detail['price']):$house_detail['price'];*/
								}

						?>" onblur="get_avgprice();" disabled="disabled">
                        <span class="y_fg y_fg_p_l_5">
						<?php	if($house_detail['price_danwei']>0){
									echo "元/㎡*天";
								}else{
									echo "元/月";
								}
						?>
						</span>
                        <script>
                            function get_avgprice() {
                                var price = $('#price').val();
                                var buildarea = $('#buildarea').val();
                                if (price > 0 && buildarea > 0)
                                {
                                    var avgprice = Math.round(price * 1000000 / buildarea) / 100;
                                    $('#avgprice').val(avgprice);
                                }
                            }
                            window.onload = get_avgprice;
                        </script>
                        <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;底价：</span>
                        <input class="input_text w60" name="lowprice" value="<?php	if($house_detail['lowprice_danwei']>0){
									echo ($house_detail['lowprice']/$house_detail['buildarea']/30);
								}else{
									echo $house_detail['lowprice'];
									/*floor($house_detail['lowprice'])==$house_detail['lowprice']?intval($house_detail['lowprice']):$house_detail['lowprice'];*/
								}

						?>" type="text" disabled="disabled">
                        <span class="y_fg y_fg_p_l_5">
						<?php	if($house_detail['lowprice_danwei']>0){
									echo "元/㎡*天";
								}else{
									echo "元/月";
								}
						?>
						</span>
                        <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;单价：</span>
                        <input class="input_text w60" name="avgprice" id="avgprice"  type="text" readonly>
                        <span class="y_fg y_fg_p_l_5">元/平米</span>
                    </div>
                    <br>
                    <div class="item_fg clearfix">
                        <span class="text_fg "><b class="red">*</b>钥匙：</span>
                        <input type="radio" name="keys" value="1" onclick="javascript:$('.key_label').show();" disabled="disabled"/>有
                        <div class="key_label" style='display: none;'>
                            <span class="text_fg">钥匙编号：</span>
                            <input class="input_text w80" type="text" value="<?php echo $house_detail['key_number']; ?>"  id="key_number" name="key_number" disabled="disabled">
                        </div>
                        <input type="radio" checked name="keys" value="0" onclick="javascript:$('#key_number').val('');$('.key_label').hide();" disabled="disabled"/>无
                    </div>
                    <br>
                    <div class="item_fg clearfix">
                        <span class="text_fg "><b class="red">*</b>委托类型：</span>
                        <?php
                        foreach ($config['entrust'] as $key => $val) {
                            echo '<input type="radio"';
                            if ($key == $house_detail['rententrust']) {
                                echo ' checked ';
                            }
                            echo ' name="entrust" value="' . $key . '" disabled="disabled"> ' . $val . '';
                        }
                        ?>
                    </div>
                </div>
                <script>
                    $("#js_house_type_1").change(function() {
                        $(".js_s_h_info").hide();
                        $(".js_s_ZZ_info").show();
                    });
                    $("#js_house_type_2").change(function() {
                        $(".js_s_h_info").hide();
                        $(".js_s_BS_info").show();
                    });
                    $("#js_house_type_3").change(function() {
                        $(".js_s_h_info").hide();
                        $(".js_s_SP_info").show();
                    });
                    $("#js_house_type_4").change(function() {
                        $(".js_s_h_info").hide();
                        $(".js_s_XZL_info").show();
                    });
                    $("#js_house_type_5").change(function() {
                        $(".js_s_h_info").hide();
                        $(".js_s_ZZ_info").show();
                    });
                    $("#js_house_type_6").change(function() {
                        $(".js_s_h_info").hide();
                        $(".js_s_ZZ_info").show();
                    });
                    $("#js_house_type_7").change(function() {
                        $(".js_s_h_info").hide();
                        $(".js_s_ZZ_info").show();
                    });
                    function all_checked(obj, to_obj, s_class) {//全选
                        var c = $(obj).attr("checked")
                        if (c)
                        {
                            $("#" + to_obj).find("." + s_class).attr("checked", true)
                        }
                        else
                        {
                            $("#" + to_obj).find("." + s_class).attr("checked", false)
                        }
                    }
                    ;
                </script>

                <div class="forms_details_fg forms_details_fg_bg clearfix">
                    <h3 class="h3">补充信息<span class="js_s_h_btn s_h_btn">&lt;&lt;</span></h3>
                    <div class="js_s_h_info_house">
                        <div class="item_fg clearfix <?php
                            if ($house_detail['sell_type'] != 3) {
                                echo 'hide';
                            }
                            ?> js_s_h_info js_s_SP_info"><!--商铺-->
                            <span class="text_fg">类型：</span>
                            <?php
                            foreach ($config['shop_type'] as $key => $val) {
                                echo '<input type="radio"';
                                if ($key == $house_detail['shop_type']) {
                                    echo "checked";
                                }
                                echo ' name="shop_type" value="' . $key . '" disabled="disabled"> ' . $val . '';
                            }
                            ?>
                        </div>
                        <div class="item_fg clearfix <?php
                                if ($house_detail['sell_type'] != 3) {
                                    echo 'hide';
                                }?> js_s_h_info js_s_SP_info" ><!--商铺-->
                                <span class="text_fg">目标业态：</span>
                                <?php
                                foreach ($config['shop_trade'] as $key => $val) {
                                    echo '<input type="checkbox"';
                                    if ($key == $house_detail['shop_trade']) {
                                        echo "checked";
                                    }
                                    echo ' name="shop_trade" class="js_checkbox" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                }
                                ?>
                        </div>
                        <div class="item_fg clearfix <?php
                            if ($house_detail['sell_type'] != 4) {
                                echo 'hide';
                            }?> js_s_h_info js_s_XZL_info"><!--写字楼-->
                            <span class="text_fg"> 是否可分割：</span>
                            <input type="radio" value="1" <?php
                            if ($house_detail['division'] == 1) {
                                echo 'checked';
                            }
                            ?>  name="division2">是
                            <input type="radio" value="0" <?php
                             if ($house_detail['division'] == 0) {
                                 echo 'checked';
                             }
                             ?>  name="division2">否
                            <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;写字楼级别：</span>
                            <?php
                                foreach ($config['office_trade'] as $key => $val) {
                                 echo '<input type="radio"';
                                 if ($key == $house_detail['office_trade']) {
                                     echo "checked";
                                 }
                                 echo ' name="office_trade" value="' . $key . '" disabled="disabled"> ' . $val . '';
                             }?>
                        </div>
                        <div class="item_fg clearfix">
                            <div class="left width_b <?php
                                if ($house_detail['sell_type'] != 3) {
                                    echo 'hide';
                                }
                                 ?> js_s_h_info js_s_SP_info"><!--商铺-->
                                <span class="text_fg">是否可分割：</span>
                                <input type="radio" value="1"  name="division" disabled="disabled">
                                是
                                <input type="radio" value="0" checked name="division" disabled="disabled">
                                否
                            </div>
                            <div class="left width_b <?php
                                if ($house_detail['sell_type'] != 4) {
                                    echo 'hide';
                                }
                                ?> js_s_h_info js_s_XZL_info" ><!--写字楼-->
                                <span class="text_fg"> 类型：</span>
                                 <?php
                                 foreach ($config['office_type'] as $key => $val) {
                                     echo '<input type="radio"';
                                     if ($key == $house_detail['office_type']) {
                                         echo "checked";
                                     }
                                     echo ' name="office_type" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                 }
                                 ?>
                            </div>
                            <div class="left width_b <?php
                                if ($house_detail['sell_type'] != 1) {
                                    echo 'hide';
                                }
                                 ?>  js_s_h_info js_s_ZZ_info" ><!--住宅-->
                                <span class="text_fg">类型：</span>
                                <?php
                                foreach ($config['house_type'] as $key => $val) {
                                    echo '<input type="radio"';
                                    if ($key == $house_detail['house_type']) {
                                        echo "checked";
                                    }
                                    echo ' name="house_type" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                }
                                ?>
                            </div>
                            <div class="left width_b js_s_h_info <?php
                                if ($house_detail['sell_type'] != 2) {
                                    echo 'hide';
                                }?>  js_s_BS_info" > <!--别墅-->
                                <span class="text_fg">类型：</span>
                                <?php
                                foreach ($config['villa_type'] as $key => $val) {
                                    echo '<input type="radio"';
                                    if ($key == $house_detail['villa_type']) {
                                        echo "checked";
                                    }
                                    echo ' name="villa_type" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="item_fg clearfix <?php
                                if ($house_detail['sell_type'] != 2) {
                                    echo 'hide';
                                }
                                ?>  js_s_h_info js_s_BS_info" ><!--别墅-->
                                <span class="text_fg">厅结构：</span>
                                <?php
                                foreach ($config['hall_struct'] as $key => $val) {
                                    echo '<input type="radio"';
                                    if ($key == $house_detail['hall_struct']) {
                                        echo "checked";
                                    }
                                    echo ' name="hall_struct" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                }
                                ?>
                            <br>
                            <span class="text_fg">地下面积：</span>
                            <input class="input_text w60" value="<?php echo $house_detail['floor_area']; ?>" name="floor_area" type="text">
                            <span class="y_fg y_fg_p_l_5">平方米&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <?php
                                foreach ($config['light_type'] as $key => $val) {
                                    echo '<input type="radio"';
                                    if ($key == $house_detail['light_type']) {
                                        echo "checked";
                                    }
                                echo ' name="light_type" value="' . $key . '" disabled="disabled"> ' . $val . '';
                            }?>
                        </div>
                        <div class="item_fg <?php
                            if ($house_detail['sell_type'] != 2) {
                                echo 'hide';
                            }
                            ?>  js_s_h_info js_s_BS_info clearfix" ><!--别墅-->
                            <span class="text_fg"> 花园面积：</span>
                            <input class="input_text w60" name="garden_area" value="<?php $house_detail['garden_area']; ?>"  type="text" disabled="disabled">
                            <span class="y_fg y_fg_p_l_5">平方米</span>
                            <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;车位数量：</span>
                            <input class="input_text w60" name="park_num" value="<?php $house_detail['park_num']; ?>" type="text" disabled="disabled">
                            <span class="y_fg y_fg_p_l_5">个</span> </div>
                        </div>
                    <br>
                        <div class="item_fg clearfix">
                            <span class="text_fg">产权：</span>
                            <?php
                            foreach ($config['property'] as $key => $val) {
                                echo '<input type="radio"';
                                if ($key == $house_detail['property']) {
                                    echo "checked";
                                }
                                echo ' name="property" value="' . $key . '" disabled="disabled"> ' . $val . '';
                            }
                            ?>
                            <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;租赁期限：</span>
                            <?php
                            foreach ($config['renttime'] as $key => $val) {
                                echo '<input type="radio"';
                                if ($key == $house_detail['renttime']) {
                                    echo "checked";
                                }
                                echo ' name="renttime" value="' . $key . '" disabled="disabled"> ' . $val . '</label>';
                            }
                            ?>
                            <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;付款方式：</span>
                            <?php
                            foreach ($config['rentpaytype'] as $key => $val) {
                                echo '<input type="radio"';
                                if ($key == $house_detail['rentpaytype']) {
                                    echo "checked";
                                }
                                echo ' name="rentpaytype" value="' . $key . '" disabled="disabled"> ' . $val . '</label>';
                            }
                            ?>
                            <span class="text_fg"> &nbsp;&nbsp;&nbsp;&nbsp;押金：</span>
                            <input class="input_text w60" name="deposit" value="<?php echo $house_detail['deposit']; ?>" type="text" disabled="disabled">
                            <span class="y_fg y_fg_p_l_5">元</span>
                        </div>
                    <br>
                        <div class="item_fg clearfix">
                            <span class="text_fg">现状：</span>
                            <?php
                            foreach ($config['current'] as $key => $val) {
                                echo '<input type="radio"';
                                if ($key == $house_detail['current']) {
                                    echo "checked";
                                }
                                echo ' name="current" value="' . $key . '" disabled="disabled"> ' . $val . '';
                            }
                            ?>
                            <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;信息来源：</span>
                            <?php
                            foreach ($config['infofrom'] as $key => $val) {
                                echo '<input type="radio"';
                                if ($key == $house_detail['infofrom']) {
                                    echo "checked";
                                }
                                echo ' name="infofrom" value="' . $key . '" disabled="disabled"> ' . $val . '';
                            }
                            ?>
                        </div>
                    <br>
                        <div class="item_fg clearfix">
                            <span class="text_fg"> 房屋设施：</span>
                            <input type="checkbox" onChange="all_checked(this, 'js_check_all01', 'js_checkbox')" disabled="disabled">
                            全选
                            <div class="clearfix check_all" id="js_check_all01">
                                <?php
                                $equipment = explode(',', $house_detail['equipment']);
                                foreach ($config['equipment'] as $key => $val) {
                                    echo '<input type="checkbox"';
                                    if (in_array($key, $equipment)) {
                                        echo "checked";
                                    }
                                    echo ' name="equipment[]" class="js_checkbox" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                }
                                ?>
                            </div>
                        </div>
                    <br>
                        <div class="item_fg clearfix">
                            <span class="text_fg">周边环境：</span>
                            <input type="checkbox" onChange="all_checked(this, 'js_check_all02', 'js_checkbox')" disabled="disabled">
                            全选
                            <div class="clearfix check_all" id="js_check_all02">
                                <?php
                                $setting = explode(',', $house_detail['setting']);
                                foreach ($config['setting'] as $key => $val) {
                                    echo '<input type="checkbox"';
                                    if (in_array($key, $setting)) {
                                        echo "checked";
                                    }
                                    echo ' name="setting[]" class="js_checkbox" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                }
                                ?>
                            </div>
                        </div>
                    <br>
                        <div class="item_fg clearfix">
                            <span class="text_fg">物业费：</span>
                            <input class="input_text w55" value="<?php echo $house_detail['strata_fee']; ?>" name="strata_fee" type="text" disabled="disabled">
                            <select class="select" name="costs_type">
                                <option value="1" <?php
                                    if ($house_detail['costs_type'] == 1) {
                                        echo 'selected';
                                    }
                                    ?>>元/月/㎡</option>
                                <option value="2" <?php
                                    if ($house_detail['costs_type'] == 2) {
                                        echo 'selected';
                                    }
                                    ?>>元/月</option>
                            </select>
                            <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;交房时间：</span>
                            <input class="input_text w75" value="<?php echo $house_detail['pay_date']; ?>" name="pay_date" id="pay_date"  onclick="WdatePicker()" readonly="readonly" type="text" disabled="disabled">
                        </div>
                    <br>
                        <div class="item_fg clearfix">
                            <span class="text_fg">备注：</span> <span class="y_fg">
                                <textarea class="textarea" name="remark" disabled="disabled"><?php echo $house_detail['remark']; ?></textarea>
                            </span>
                        </div>
						<div class="item_fg clearfix">
                            <span class="text_fg">房源标题：</span> <span class="y_fg"><?=$house_detail['title']?></span>
                        </div>
						<div class="item_fg clearfix">
                            <span class="text_fg">房源描述：</span> <span class="y_fg"><?=$house_detail['bewrite']?></span>
                        </div>
                    </div>
                </div>
                <div class="forms_details_fg forms_details_fg_bg clearfix" style="display:none;">
                    <h3 class="h3">房源图片<span class="js_s_h_btn s_h_btn">&lt;&lt;</span></h3>
                    <div class="js_s_h_info_house">
                        <div class="add_pic_house_title">室内图<span class="t">至多上传10张室内图</span></div>
                        <div class="add_pic_house_box clearfix">
                            <div class="add_item">
                                <span id="spanButtonPlaceholder2"></span>
                            </div>

                            <script type="text/javascript">
                                var swfu2;
                                $(function() {
                                    swfu2 = new SWFUpload({
                                        // Backend Settings
                                        file_post_name: "file",
                                        upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                                        //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                                        //post_params: {"postion" : position},
                                        // File Upload Settings
                                        file_size_limit: "5 MB",
                                        file_types: "*.jpg;*.png",
                                        file_types_description: "JPG Images",
                                        file_upload_limit: "0",
                                        file_queue_limit: "5",
                                        custom_settings: {
                                            upload_target: "jsPicPreviewBoxM2",
                                            upload_limit: 10,
                                            upload_nail: "thumbnails2",
                                            upload_infotype: 2},
                                        // Event Handler Settings - these functions as defined in Handlers.js
                                        //  The handlers are not part of SWFUpload but are part of my website and control how
                                        //  my website reacts to the SWFUpload events.
                                        swfupload_loaded_handler: swfUploadLoaded,
                                        file_queue_error_handler: fileQueueError,
                                        file_dialog_start_handler: fileDialogStart,
                                        file_dialog_complete_handler: fileDialogComplete,
                                        upload_progress_handler: uploadProgress,
                                        upload_error_handler: uploadError,
                                        upload_success_handler: uploadSuccessNew,
                                        upload_complete_handler: uploadComplete,
                                        // Button Settings
                                        button_image_url: "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn.png",
                                        button_placeholder_id: "spanButtonPlaceholder2",
                                        button_width: 130,
                                        button_height: 100,
                                        button_cursor: SWFUpload.CURSOR.HAND,
                                        button_text: "",
                                        flash_url: "<?php echo MLS_SOURCE_URL; ?>/common/third/swfupload.swf"
                                    });

                                });
                            </script>
                            <div id="jsPicPreviewBoxM2" style="display:none" ></div>
                            <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails2">

                            </div>
                        </div>
                        <div class="add_pic_house_title">户型图
                            <span class="t">至多上传3张户型图</span>
                            <!--<a class="link" href="<?php echo MLS_ADMIN_URL; ?>/community/draw/">户型图画图工具>></a>-->
                        </div>
                        <div class="add_pic_house_box clearfix">
                            <div class="add_item">
                                <span id="spanButtonPlaceholder1"></span>
                            </div>
                            <script type="text/javascript">
                                var swfu1;
                                $(function() {
                                    swfu1 = new SWFUpload({
                                        file_post_name: "file",
                                        upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                                        file_size_limit: "5 MB",
                                        file_types: "*.jpg;*.png",
                                        file_types_description: "JPG Images",
                                        file_upload_limit: "0",
                                        file_queue_limit: "5",
                                        custom_settings: {
                                            upload_target: "jsPicPreviewBoxM1",
                                            upload_limit: 3,
                                            upload_nail: "thumbnails1",
                                            upload_infotype: 1
                                        },
                                        swfupload_loaded_handler: swfUploadLoaded,
                                        file_queue_error_handler: fileQueueError,
                                        file_dialog_start_handler: fileDialogStart,
                                        file_dialog_complete_handler: fileDialogComplete,
                                        upload_progress_handler: uploadProgress,
                                        upload_error_handler: uploadError,
                                        upload_success_handler: uploadSuccessNew,
                                        upload_complete_handler: uploadComplete,
                                        button_image_url: "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn02.png",
                                        button_placeholder_id: "spanButtonPlaceholder1",
                                        button_width: 130,
                                        button_height: 100,
                                        button_cursor: SWFUpload.CURSOR.HAND,
                                        button_text: "",
                                        flash_url: "<?php echo MLS_SOURCE_URL; ?>/common/third/swfupload.swf"
                                    });

                                });
                            </script>
                            <div id="jsPicPreviewBoxM1" style="display:none" ></div>
                            <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails1">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="forms_details_fg clearfix" style="display:none;">
                    <input name="house_id" value="<?php echo $house_detail['id'] ?>" type="hidden"/>
                    <button type="submit" class="submit">提交修改</button>
                </div>
            </form>
        </div>
            </div>
        </div>
    </div>
        </div>

        <div id="js_pop_add_new_block" class="pop_box_g">
            <div class="hd">
                <div class="title">新建楼盘</div>
                <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
            </div>
            <div class="mod">
                <div class="tab_pop_mod add_new_block clear">
                    <div style="display:block;" class="inner">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="w70 t_l">楼盘名称<font color="red">*</font>：</td>
                                    <td class="w160"><input type="text" class="input_text" name="cmt_name"></td>
                                    <td class="w60 t_l">区属<font color="red">*</font>：</td>
                                    <td class="w160">
                                        <select id="district" name="add_dist_id" class="select">
                                            <option value="">请选择</option>
<?php foreach ($district as $k => $v) { ?>
                                                <option value="<?php echo $v['id'] ?>"><?php echo $v['district'] ?></option>
<?php } ?>
                                        </select>
                                    </td>
                                    <td class="w70 t_l">板块<font color="red">*</font>：</td>
                                    <td>
                                        <select id="street" name="add_streetid" class="select">
                                            <option value="">请选择</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="w60 t_l">地址<font color="red">*</font>：</td>
                                    <td class="w160"><input type="text" class="input_text" name="com_address"></td>
                                    <td class="w60 t_l">物业费：</td>
                                    <td><input type="text" class="input_text w50" name="property_fee">元/月/㎡</td>
                                    <td class="w70 t_l">建筑年代<font color="red">*</font>：</td>
                                    <td>
                                        <select id="build_date" name="build_date" class="select">
                                            <option value="">请选择</option>
<?php for ($i = 1970; $i < 2016; $i++) { ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?>年</option>
<?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="w60 t_l">产权年限：</td>
                                    <td class="w160">
                                        <select id="property_year" name="property_year" class="select">
                                            <option value="">请选择</option>
                                            <option value="40">40年</option>
                                            <option value="50">50年</option>
                                            <option value="70">70年</option>
                                        </select>
                                    </td>
                                    <td class="w60 t_l">建筑面积：</td>
                                    <td class="w160"><input type="text" class="input_text w50" name="buildarea2">平方米</td>
                                    <td class="w70 t_l">占地面积：</td>
                                    <td><input type="text" class="input_text w50" name="coverarea">平方米</td>
                                </tr>
                                <tr>
                                    <td class="w60 t_l">物业公司：</td>
                                    <td class="w160"><input type="text" class="input_text" name="property_company"></td>
                                    <td class="w60 t_l">开发商：</td>
                                    <td class="w160"><input type="text" class="input_text" name="developers"></td>
                                    <td class="w70 t_l">车位：</td>
                                    <td><input type="text" class="input_text w40" name="parking">如：车位充足</td>
                                </tr>
                                <tr>
                                    <td class="w60 t_l">绿化率：</td>
                                    <td class="w160"><input type="text" class="input_text w50" name="green_rate">%</td>
                                    <td class="w60 t_l">容积率：</td>
                                    <td class="w160"><input type="text" class="input_text w50" name="plot ratio">%</td>
                                    <td class="w70 t_l">总栋数：</td>
                                    <td><input type="text" class="input_text w50" name="build_num"></td>

                                </tr>
                                <tr>
                                    <td class="w60 t_l">总户数：</td>
                                    <td class="w160"><input type="text" class="input_text" name="total_room"></td>
                                    <td class="w60 t_l">楼层情况：</td>
                                    <td><input type="text" class="input_text w50" name="floor_instruction">如：一梯两户</td>
                                </tr>

                                <tr>
                                    <td class="w60 t_l">楼盘介绍：</td>
                                    <td colspan="5">
                                        <textarea class="textarea" name="introduction"></textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="w60 t_l">物业类型：</td>
                                    <td colspan="5">
                                        <input type="checkbox" name="build_type" value="住宅">住宅
                                        <input type="checkbox" name="build_type" value="别墅">别墅
                                        <input type="checkbox" name="build_type" value="写字楼">写字楼
                                        <input type="checkbox" name="build_type" value="商铺">商铺
                                        <input type="checkbox" name="build_type" value="厂房">厂房
                                        <input type="checkbox" name="build_type" value="仓库">仓库
                                        <input type="checkbox" name="build_type" value="车库">车库
                                    </td>
                                </tr>

                                <tr>
                                    <td class="w60 t_l">周边配套：</td>
                                    <td colspan="5">
                                        <textarea class="textarea" name="facilities"></textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="w60 t_l">图片：</td>
                                    <td colspan="5">
                                        <div class="add_pic_house_title">外景图<span class="t">至多上传10张室内图</span></div>
                                        <div class="add_item">
                                            <span id="spanButtonPlaceholder3"></span>
                                        </div>
                                        <script type="text/javascript">
                                            var swfu3;
                                            $(function() {
                                                swfu3 = new SWFUpload({
                                                    file_post_name: "file",
                                                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                                                    file_size_limit: "5 MB",
                                                    file_types: "*.jpg;*.png",
                                                    file_types_description: "JPG Images",
                                                    file_upload_limit: "0",
                                                    file_queue_limit: "5",
                                                    custom_settings: {
                                                        upload_target: "jsPicPreviewBoxM3",
                                                        upload_limit: 3,
                                                        upload_nail: "thumbnails3",
                                                        upload_infotype: 3
                                                    },
                                                    swfupload_loaded_handler: swfUploadLoaded,
                                                    file_queue_error_handler: fileQueueError,
                                                    file_dialog_start_handler: fileDialogStart,
                                                    file_dialog_complete_handler: fileDialogComplete,
                                                    upload_progress_handler: uploadProgress,
                                                    upload_error_handler: uploadError,
                                                    upload_success_handler: uploadSuccessNew,
                                                    upload_complete_handler: uploadComplete,
                                                    button_image_url: "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn03.png",
                                                    button_placeholder_id: "spanButtonPlaceholder3",
                                                    button_width: 130,
                                                    button_height: 100,
                                                    button_cursor: SWFUpload.CURSOR.HAND,
                                                    button_text: "",
                                                    flash_url: "<?php echo MLS_SOURCE_URL; ?>/common/third/swfupload.swf"
                                                });

                                            });
                                        </script>
                                        <div id="jsPicPreviewBoxM3" style="display:none" ></div>
                                        <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails3"></div>

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <div style="color:red;" id="xqerror" class="errorBox clear"></div>
                                    </td>
                                </tr>


                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab_pop_bd add_new_block_bd"> <a href="javascript:void(0);" class="btn" id='add_cmt_submit'>新建楼盘</a><a href="javascript:void(0);" class="btn btn_del JS_Close">取消</a> </div>
            </div>
        </div>

        <!--操作结果弹出提示框-->
        <div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
            <div class="hd">
                <div class="title">提示</div>
                <div class="close_pop">
                    <a href="javascript:void(0);" onclick="sub_form();" title="关闭" class="JS_Close iconfont"></a>
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

        <script type="text/javascript">
            $(function() {
                $('#district').change(function() {
                    var districtID = $(this).val();
                    $.ajax({
                        type: 'get',
                        url: '/sell/find_street_bydis/' + districtID,
                        dataType: 'json',
                        success: function(msg) {
                            var str = '';
                            if (msg.result == 'no result') {
                                str = '<option value="">请选择</option>';
                            } else {
                                str = '<option value="">请选择</option>';
                                for (var i = 0; i < msg.length; i++) {
                                    str += '<option value="' + msg[i].id + '">' + msg[i].streetname + '</option>';
                                }
                            }
                            $('#street').empty();
                            $('#street').append(str);
                        }
                    });
                });

                $('#add_cmt_submit').click(function() {
                    var cmt_name = $('input[name="cmt_name"]').val();//楼盘名称
                    var dist_id = $('select[name="add_dist_id"]').val();//区属
                    var streetid = $('select[name="add_streetid"]').val();//板块
                    var address = $('input[name="com_address"]').val();//地址
                    var build_date = $('select[name="build_date"]').val();//建筑年代
                    var property_year = $('select[name="property_year"]').val();//产权年限
                    var buildarea = $('input[name="buildarea2"]').val();//建筑面积
                    var coverarea = $('input[name="coverarea"]').val();//占地面积
                    var property_company = $('input[name="property_company"]').val();//物业公司
                    var developers = $('input[name="developers"]').val();//开发商
                    var parking = $('input[name="parking"]').val();//车位
                    var green_rate = $('input[name="green_rate"]').val();//绿化率
                    var plot_ratio = $('input[name="plot ratio"]').val();//容积率
                    var property_fee = $('input[name="property_fee"]').val();//物业费
                    var build_num = $('input[name="build_num"]').val();//总栋数
                    var total_room = $('input[name="total_room"]').val();//总户数
                    var floor_instruction = $('input[name="floor_instruction"]').val();//楼层情况
                    var introduction = $('textarea[name="introduction"]').val();//楼盘介绍
                    var facilities = $('textarea[name="facilities"]').val();//周边配套
                    //物业类型
                    var build_type = [];
                    $('input[name="build_type"]:checked').each(function() {
                        build_type.push($(this).val());
                    });
                    //图片
                    var p_filename3 = [];
                    $('input[name="p_filename3[]"]').each(function() {
                        p_filename3.push($(this).val());
                    });
                    //是否为封面
                    var surface = $('input[name="add_pic3"]:checked').val();
                    var addData = {
                        'cmt_name': cmt_name,
                        'dist_id': dist_id,
                        'streetid': streetid,
                        'address': address,
                        'build_date': build_date,
                        'property_year': property_year,
                        'buildarea': buildarea,
                        'coverarea': coverarea,
                        'property_company': property_company,
                        'developers': developers,
                        'parking': parking,
                        'green_rate': green_rate,
                        'plot_ratio': plot_ratio,
                        'property_fee': property_fee,
                        'build_num': build_num,
                        'total_room': total_room,
                        'floor_instruction': floor_instruction,
                        'introduction': introduction,
                        'facilities': facilities,
                        'build_type': build_type,
                        'location_pic': p_filename3,
                        'surface': surface
                    };
                    $.ajax({
                        type: 'get',
                        url: '/sell/add_community',
                        data: addData,
                        success: function(msg) {
                            if ('true' == msg) {
                                $('#dialog_do_itp').html('新建成功');
                                openWin('js_pop_do_success');
                                $("#js_pop_add_new_block").css('display', 'none');
                                $("#GTipsCoverjs_pop_add_new_block").remove();
                            } else {
                                if ('100' == msg) {
                                    $("#xqerror").html('请填写楼盘名');
                                } else if ('200' == msg) {
                                    $("#xqerror").html('请选择区属!');
                                } else if ('300' == msg) {
                                    $("#xqerror").html('请选择板块!');
                                } else if ('400' == msg) {
                                    $("#xqerror").html('请填写地址!');
                                } else if ('500' == msg) {
                                    $("#xqerror").html('请填写建筑年代!');
                                } else if ('600' == msg) {
                                    $("#xqerror").html('已存在同名小区!');
                                }
                            }
                        }
                    });

                });
                $("#js_gs_01").click(function(){
                    $("#js_show_yj").show();
                })
                $("#js_gs_02").click(function(){
                    $("#js_show_yj").hide();
                })
            });
            $("#a_ratio").blur(function(){
                var a_ratio = $("#a_ratio").val();
                var b_ratio = 100-a_ratio;
                $("#b_ratio").val(b_ratio);
            })
        </script>
    </div>
</div>


<?php require APPPATH . 'views/footer.php'; ?>
