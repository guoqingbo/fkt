<?php require APPPATH . 'views/header.php'; ?>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/openWin.js"></script>
<link href="<?=MLS_SOURCE_URL ?>/min/?b=mls&f=third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<style>
    #js_pop_do_success{border-color: #3E444;}
    .pop_box_g{width:250px;height:150px}
</style>
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
            <form action="/sell_house/add/" id="jsUpForm" method="post">
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
                        } ?>
                    </div><br>
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
                        <span class="text_fg"><b class="red">&nbsp;&nbsp;&nbsp;&nbsp;*</b>房源性质：</span>
                        <?php
                        foreach ($config['nature'] as $key => $val) {
                            echo '<input type="radio"';
                            if ($key == $house_detail['nature']) {
                                echo ' checked ';
                            }
                            echo ' name="nature" value="' . $key . '" disabled="disabled"> ' . $val . '';
                        }
                        ?>
                    </div><br>
                    <div class="item_fg clearfix">
						<?php if($house_detail['sell_type']<3){?>
                        <div class="left width_b">
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
                                }
                                ?>
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
								}
                                ?>
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
                        <div><b class="red">*</b>是否合作：
                            <input type="radio" name="isshare" id="js_gs_01" value="1" <?php if ($house_detail['isshare'] == '1') {
                                echo 'checked';
                            } ?> disabled="disabled">是
                            <input type="radio" name="isshare" id="js_gs_02" value="0" <?php if ($house_detail['isshare'] == '0') {
                                echo 'checked';
                            } ?> disabled="disabled">否
                        </div>
                        <br>
                        <div class="item_fg clearfix " id="js_show_yj" <?php if(empty($house_detail['isshare'])){echo "style='display:none;'";}?>>
                            房源合作佣金分配<span class="fy_text">获得本次交易双方佣金总金额： </span>
                            <span class="left">甲方</span>
                            <input class="input_text jzf w60 left" id="a_ratio" value="<?php echo !empty($ratio_info['a_ratio']) ? strip_end_0($ratio_info['a_ratio']) : '';?>" name="a_ratio" type="text" disabled="disabled"/>%
                            <span class="left" style="padding-left:15px;">&nbsp;&nbsp;&nbsp;乙方</span>
                            <input class="input_text jzf w60" readonly name="b_ratio" value="<?php echo !empty($ratio_info['b_ratio']) ? strip_end_0($ratio_info['b_ratio']) : '';?>" id="b_ratio" type="text" disabled="disabled">%
                        </div><br>
                        <div class="left dq_fy">
                            <span class="left">支付交易总金额：</span>
                            <span class="left">买方</span>
                            <input class="input_text jzf w60" name="buyer_ratio" value="<?php echo !empty($ratio_info['buyer_ratio']) ? strip_end_0($ratio_info['buyer_ratio']) : '';?>" id="buyer_ratio" type="text" disabled="disabled">%
                            <span class="left" style="padding-left:15px;">&nbsp;&nbsp;卖方</span>
                            <input class="input_text jzf w60" name="seller_ratio" value="<?php echo !empty($ratio_info['seller_ratio']) ? strip_end_0($ratio_info['seller_ratio']) : '';?>" id="seller_ratio" type="text" disabled="disabled">%
                        </div>
                    </div>
                    <br>
                    <div class="item_fg clearfix">
						<?php if($house_detail['sell_type']<5){?>
                        <b class="red">*</b>朝向：
                            <?php
                            foreach ($config['forward'] as $key => $val) {
                                echo '<input type="radio"';
                                if ($key == $house_detail['forward']) {
                                    echo ' checked ';
                                }
                                echo ' name="forward" value="' . $key . '" disabled="disabled"> ' . $val . '';
                            }?>
                        <br><br>
						<?php }?>
                        <div class="left">
                            <span class="text_fg"><b class="red">*</b>楼层：</span>
                            <input type="radio"  name="floor_type" value="1" <?php if ($house_detail['floor_type'] == '1') {
                                echo "checked";
                            } ?> onChange="show_input('d_input', 'y_input')" disabled="disabled">
                            单层
                            <input class="input_text w20" name="floor" value="<?php echo $house_detail['floor']; ?>" type="text" disabled="disabled">
                            &nbsp;&nbsp;
                            <input type="radio" name="floor_type"  value="2"  <?php if ($house_detail['floor_type'] == '2') {
                                echo "checked";
                            } ?> onChange="show_input('y_input', 'd_input')" disabled="disabled">
                            跃层
                            <input class="input_text w20" name="floor2" value="<?php echo $house_detail['floor']; ?>"  type="text" disabled="disabled">
                            <span class="y_fg y_fg_p5">一</span>
                            <input class="input_text w20 js_fields" value="<?php echo $house_detail['subfloor']; ?>"  name="subfloor" type="text" disabled="disabled">
                            &nbsp;&nbsp;<span class="y_fg y_fg_p_l">总楼层：</span>
                            <input class="input_text w50" type="text" name="totalfloor" value="<?php echo $house_detail['totalfloor']; ?>" id="z_louceng" disabled="disabled">
                        </div>
                        <br>
                    </div>
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
                        }?>
						
                        <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;
						<?php } ?><b class="red">*</b>房龄：</span>
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
                        <br><br>
                        <span class="text_fg"><b class="red">*</b>面积：</span>
                        <input class="input_text w60" name="buildarea" id="buildarea" type="text" value="<?php echo $house_detail['buildarea']; ?>" onblur="get_avgprice();" disabled="disabled">
                        <span class="y_fg y_fg_p_l_5">平方米</span>
                        <span class="text_fg"><b class="red">&nbsp;&nbsp;&nbsp;&nbsp;*</b>总价：</span>
                        <input class="input_text w60" name="price" type="text" id="price" value="<?php echo $house_detail['price']; ?>" onblur="get_avgprice();" disabled="disabled">
                        <span class="y_fg y_fg_p_l_5">万元</span>
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
                        <input class="input_text w60" name="lowprice" value="<?php echo $house_detail['lowprice']; ?>" type="text" disabled="disabled">
                        <span class="y_fg y_fg_p_l_5">万元(加密)</span>
                        <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;单价：</span>
                        <input class="input_text w60" name="avgprice" id="avgprice"  type="text" readonly>
                        <span class="y_fg y_fg_p_l_5">元/平米</span>
                    </div>
                    <br>
                    <div class="item_fg clearfix">
                        <span class="text_fg"><b class="red">*</b>税费：</span>
                        <?php
                        foreach ($config['taxes'] as $key => $val) {
                            echo '<input type="radio"';
                            if ($key == $house_detail['taxes']) {
                                echo ' checked ';
                            }
                            echo ' name="taxes" value="' . $key . '" disabled="disabled"> ' . $val . '';
                        }
                        ?>
                        <span class="text_fg "><b class="red">&nbsp;&nbsp;&nbsp;&nbsp;*</b>钥匙：</span>
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
                            if ($key == $house_detail['entrust']) {
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
                        <div class="item_fg clearfix <?php if ($house_detail['sell_type'] != 3) {
                            echo 'hide';
                        } ?> js_s_h_info js_s_SP_info"><!--商铺-->
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
                        <div class="item_fg clearfix <?php if ($house_detail['sell_type'] != 3) {
                                    echo 'hide';
                                } ?> js_s_h_info js_s_SP_info" ><!--商铺-->
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
                        <div class="item_fg clearfix <?php if ($house_detail['sell_type'] != 4) {
                            echo 'hide';
                        } ?> js_s_h_info js_s_XZL_info"><!--写字楼-->
                            <span class="text_fg"> 是否可分割：</span>
                            <input type="radio" value="1" <?php if ($house_detail['division'] == 1) {
                                echo 'checked';
                            } ?>  name="division2" disabled="disabled">是
                            <input type="radio" value="0" <?php if ($house_detail['division'] == 0) {
                                echo 'checked';
                            } ?>  name="division2" disabled="disabled">否
                            <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;写字楼级别：</span>
                            <?php
                            foreach ($config['office_trade'] as $key => $val) {
                                echo '<input type="radio"';
                                if ($key == $house_detail['office_trade']) {
                                    echo "checked";
                                }
                                echo ' name="office_trade" value="' . $key . '" disabled="disabled"> ' . $val . '';
                            }
                            ?>
                        </div>
                        <div class="item_fg clearfix">
                            <div class="left width_b <?php if ($house_detail['sell_type'] != 3) {
                                echo 'hide';
                            } ?> js_s_h_info js_s_SP_info"><!--商铺-->
                                <span class="text_fg"> 是否可分割：</span>
                                <input type="radio" value="1"  name="division" disabled="disabled">是
                                <input type="radio" value="0" checked name="division" disabled="disabled">否
                            </div>
                            <div class="left width_b <?php if ($house_detail['sell_type'] != 4) {
                                echo 'hide';
                            } ?> js_s_h_info js_s_XZL_info" ><!--写字楼-->
                                <span class="text_fg"> 类型：</span>
                                <?php
                                foreach ($config['office_type'] as $key => $val) {
                                    echo '<input type="radio"';
                                    if ($key == $house_detail['office_type']) {
                                        echo "checked";
                                    }
                                    echo ' name="office_type" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                }?>
                            </div>
                            <div class="left width_b <?php if ($house_detail['sell_type'] != 1) {
                                echo 'hide';
                            } ?>  js_s_h_info js_s_ZZ_info" ><!--住宅-->
                                <span class="text_fg">类型：</span>
                                <?php
                                foreach ($config['house_type'] as $key => $val) {
                                    echo '<input type="radio"';
                                    if ($key == $house_detail['house_type']) {
                                        echo "checked";
                                    }
                                    echo ' name="house_type" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                }?>
                            </div>
                            <div class="left width_b js_s_h_info <?php if ($house_detail['sell_type'] != 2) {
                                echo 'hide';
                            } ?>  js_s_BS_info" > <!--别墅-->
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
                        <div class="item_fg clearfix <?php if ($house_detail['sell_type'] != 2) {
                                echo 'hide';
                            } ?>  js_s_h_info js_s_BS_info" ><!--别墅-->
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
                            <span class="text_fg"> 地下面积：</span>
                            <input class="input_text w60" value="<?php echo $house_detail['floor_area']; ?>" name="floor_area" type="text">
                            <span class="y_fg y_fg_p_l_5">平方米&nbsp;&nbsp;</span>
                            <?php
                            foreach ($config['light_type'] as $key => $val) {
                                echo '<input type="radio"';
                                if ($key == $house_detail['light_type']) {
                                    echo "checked";
                                }
                                echo ' name="light_type" value="' . $key . '" disabled="disabled"> ' . $val . '';
                            }
                            ?>
                        </div>
                        <div class="item_fg <?php if ($house_detail['sell_type'] != 2) {
                            echo 'hide';
                        } ?>  js_s_h_info js_s_BS_info clearfix" ><!--别墅-->
                            <span class="text_fg"> 花园面积：</span>
                            <input class="input_text w60" name="garden_area" value="<?php $house_detail['garden_area']; ?>"  type="text" disabled="disabled">
                            <span class="y_fg y_fg_p_l_5">平方米</span>
                            <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;车位数量：</span>
                            <input class="input_text w60" name="park_num" value="<?php $house_detail['park_num']; ?>" type="text" disabled="disabled">
                            <span class="y_fg y_fg_p_l_5">个</span>
                        </div>
                        <br>
                        <div class="item_fg clearfix">
                                <span class="text_fg"> 产权：</span>
                                <?php
                                foreach ($config['property'] as $key => $val) {
                                    echo '<input type="radio"';
                                    if ($key == $house_detail['property']) {
                                        echo "checked";
                                    }
                                    echo ' name="property" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                }?>
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
                            }?>
                        </div>
                        <br>
                        <div class="item_fg clearfix">
                            <span class="text_fg"> 房屋设施：</span>
                            <input type="checkbox" onChange="all_checked(this, 'js_check_all01', 'js_checkbox')" disabled="disabled">全选
                            <div class="clearfix check_all" id="js_check_all01">
                                <?php
                                $equipment = explode(',', $house_detail['equipment']);
                                foreach ($config['equipment'] as $key => $val) {
                                    echo '<input type="checkbox"';
                                    if (in_array($key, $equipment)) {
                                        echo "checked";
                                    }
                                    echo ' name="equipment[]" class="js_checkbox" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                }?>
                            </div>
                        </div>
                        <br>
                        <div class="item_fg clearfix">
                            <span class="text_fg"> 周边环境：</span>
                            <input type="checkbox" onChange="all_checked(this, 'js_check_all02', 'js_checkbox')" disabled="disabled">全选 
                            <div class="clearfix check_all" id="js_check_all02">
                                <?php
                                $setting = explode(',', $house_detail['setting']);
                                foreach ($config['setting'] as $key => $val) {
                                    echo '<input type="checkbox"';
                                    if (in_array($key, $setting)) {
                                        echo "checked";
                                    }
                                    echo ' name="setting[]" class="js_checkbox" value="' . $key . '" disabled="disabled"> ' . $val . '';
                                } ?>
                            </div>
                        </div>
                        <br>
                        <div class="item_fg clearfix">
                            <span class="text_fg">物业费：</span>
                            <input class="input_text w55" value="<?php echo $house_detail['strata_fee']; ?>" name="strata_fee" type="text" disabled="disabled">
                            <select class="select" name="costs_type" disabled="disabled">
                                <option value="1" <?php if ($house_detail['costs_type'] == 1) { 
                                    echo 'selected';
                                } ?>>元/月/㎡</option>
                                <option value="2" <?php if ($house_detail['costs_type'] == 2) {
                                    echo 'selected';
                                } ?>元/月</option>
                            </select>
                            <span class="text_fg">&nbsp;&nbsp;&nbsp;&nbsp;交房时间：</span>
                            <input class="input_text w75" value="<?php echo $house_detail['pay_date'];?>" name="pay_date" id="pay_date"  onclick="WdatePicker()" readonly="readonly" type="text" disabled="disabled">
                        </div>
                        <div class="item_fg clearfix">
                            <span class="text_fg">备注：</span> <span class="y_fg">
                                <textarea class="textarea" name="remark" disabled="disabled"><?php echo $house_detail['remark'];?></textarea>
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
                <div style="width:100%" class="col-sm-6">
                    <div id="dataTables-example_length" class="dataTables_length">
                        <label>操&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;作：</label>
                        <label>  
                            <select name="is_check" aria-controls="dataTables-example" style="width:168px" class="form-control input-sm">
                                <option value="1">审核通过</option>
                                <option value="2">驳回</option>
                            </select>
                        </label>
                    </div>
                </div>
                <div style="width:100%" class="col-sm-6">
                    <div id="dataTables-example_length" class="dataTables_length">
                        <label>审核理由：</label>
                        <label>  
                            <textarea name="check_reason" rows="3" cols="50" id="remark"></textarea>
                        </label>
                    </div>
                </div>
                <div style="width:100%" class="col-sm-6">
                    <div id="dataTables-example_length" class="dataTables_length">
                        <input type="hidden" id="house_id" value="<?=$house_id?>">
                        <input type="hidden" id="id" value="<?=$id?>">
                        <input type="button" class="btn btn-primary" value="提交" onclick="pingan_data_deal()">
                        <a href="javascript:void(0);" class="btn btn-primary" onclick="javascript:history.go(-1);">返回</a>
                    </div>
                </div>
            </form>
        </div>
            </div>
        </div>
    </div>
</div>
<div id="fakeloader" style="display:none;"><img src="<?=MLS_SOURCE_URL ?>/mls_admin/images/load3.gif" width="50" height="50"></div>
<script>
    //平安好房房源同步
    function pingan_data_deal(){
        openWin('fakeloader');
        var house_id = $("#house_id").val();
        var id = $("#id").val();
        var is_check = $("select[name='is_check']").val();
        var check_reason = $("#remark").val();
        $.ajax({
            url: "/pinganFang/update_status",
            type: "post",
            data: {'house_id':house_id,id:id,is_check:is_check,check_reason:check_reason},
            dataType: "json",
            success: function(data) {
                if(data['code'] !=='success'){
                    $("#dialog_do_itp").html(data.msg);
                    openWin('js_pop_do_success');
                }else{
                    closeWindowWin('fakeloader');
                    $("#dialog_do_itp").html('修改成功');
                    openWin('js_pop_do_success');
                    setTimeout(function(){history.go(-1);},2000);
                }
            }
         });
                   
    }
</script>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd" style="background: #3E444B;">
        <div class="title" >提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
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
<?php require APPPATH . 'views/footer.php'; ?>
