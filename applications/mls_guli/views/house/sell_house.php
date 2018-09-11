<script>
    window.parent.addNavClass(2);
</script>
<body>
<div class="tab_box" id="js_tab_box">
	<div class="clearfix" id="js_fun_btn">
		<a href="javascript:void(0);" class="link link_on"><span class="iconfont">&#xe605;</span>修改出售</a>
		<?php if($app_id){?>
		<a href="/appoint_center/app_sell" class="btn-lv" style="float:right; margin-right:10px;"><span>&lt;&lt;返回预约列表</span></a>
		<?php }else{ ?>
		<a href="/sell/lists" class="btn-lv" style="float:right; margin-right:10px;"><span>&lt;&lt;返回房源列表</span></a>
		<?php } ?>
	</div>
</div>
<form action="/sell/update/" id="jsUpForm" method="post">
<div class="forms forms_scroll h91" id="js_inner">
        <input type="hidden" id="comdict" name="comdict" value="<?php echo $comdict;?>">
        <input type="hidden" id="nopublish" name="nopublish" value="<?php echo $nopublish;?>">
        <input type="hidden" name="action_type" value="modify"/>
        <input name="house_id" id="house_id" value="<?php echo $house_detail['id'];?>" type="hidden">
        <input name="pic_ids" value="<?php echo $house_detail['pic_ids'];?>" type="hidden">
        <input name="refer" value="<?php echo $house_detail['refer'];?>" type="hidden">
		<input type="hidden" id="group_id" value="<?php echo $group_id;?>">
		<input type="hidden" id="old_nature" value="<?php echo $house_detail['nature'];?>">
		<input type="hidden" id="old_status" value="<?php echo $house_detail['status'];?>">
		<input type="hidden" id="is_outside" value="<?php echo $house_detail['is_outside'];?>">
		<input type="hidden" id="property_type_per" value="<?php echo $property_type_per;?>">
        <div class="forms_details_fg">
            <div class="clearfix item_fg">
                <label class="label">
                <span class="text_fg"><b class="red">*</b>楼盘名称：</span>
                <div class="y_fg js_fields">
                    <input name="block_name" id="block_name" value="<?php echo $house_detail['block_name']; ?>" class="input_text input_text_r  w150" type="text" placeholder="输入拼音或汉字筛选" onblur="check_unique_house()">
                    <?php if(!('1'===$is_property_publish)){?>
					 <a href="javascript:void(0)" class="btn-lv1 left btn-right" id="addBlock" onclick="addBlock()">添加楼盘</a>
                    <?php }?>
                    <input name="block_id" id="block_id" value="<?php echo $house_detail['block_id']; ?>" type="hidden"  onchange="check_unique_house()">
                    <div class="errorBox clear"></div>
                </div>
                </label>
                <script type="text/javascript">
                $(function(){
                    $.widget( "custom.autocomplete", $.ui.autocomplete, {
                        _renderItem: function( ul, item ) {
                            if(item.id>0){
                                return $( "<li>" )
                                .data( "item.autocomplete", item )
                                .append('<a class="ui-corner-all" tabindex="-1"><font class="ui_name">'+item.label+'</font><font class="ui_district">'+item.districtname+'</font><font class="ui_address">'+item.address+'</font></a>')
                                .appendTo( ul );
                            }else{
                                return $( "<li>" )
                                .data( "item.autocomplete", item )
                                .append('<a class="ui-corner-all" tabindex="-1">'+item.label+'</a>')
                                .appendTo( ul );
                            }
                        }
                    });
                    $("#block_name").autocomplete({
                        source: function( request, response ) {
                            var dong_select_str = '';
                            var unit_select_str = '';
                            var door_select_str = '';
                            //栋座
                            dong_select_str += '<input class="input_text input_text_r w80" name="dong" id="dong" value="" type="text" onBlur="check_unique_house()">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="dong_reminder"></span><div class="errorBox clear"></div>';
                            $('#dong_div').html(dong_select_str);
                            //单元
                            unit_select_str += '<input class="input_text input_text_r w80" name="unit" id="unit" value="" type="text" onBlur="check_unique_house()">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="unit_reminder"></span><div class="errorBox clear"></div>';
                            $('#unit_div').html(unit_select_str);
                            //门牌
                            door_select_str += '<input class="input_text input_text_r w80" name="door" id="door" value="" type="text" onBlur="check_unique_house()">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="door_reminder"></span><div class="errorBox clear"></div>';
                            $('#door_div').html(door_select_str);

                            $("#select_b").val("");
                            $("#select_q").val("");
                            $("#address").val("");
                            $('#buildyear').val("");
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
                                    if( data[0]['id'] != '0'){
                                        response(data);
                                    }else{
                                        response(data);
                                    }
                                }
                            });
                        },
                        minLength: 1,
                        removeinput: 0,
                        select: function(event,ui) {
                            if(ui.item.id > 0){
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

                                //判断所选楼盘，是否锁盘，如果锁盘，楼栋单元门牌下拉选择
                                $.ajax({
                                    url: "/community/check_is_lock/",
                                    type: "GET",
                                    dataType: "HTML",
                                    data: {cmt_id: id},
                                    success: function(data)
                                    {
                                        var result_obj = eval('(' + data + ')');
                                        var dong_select_str = '';
                                        var unit_select_str = '';
                                        var door_select_str = '';

                                        if('1'==result_obj.is_lock){
                                            //栋座
                                            dong_select_str += '<select class="select" name="dong">';
                                            dong_select_str += '<option value="">请选择</option>';
                                            for(var i = 0;i < result_obj.dong.length;i++){
                                                dong_select_str += '<option value="'+result_obj.dong[i].name+'" _id="'+result_obj.dong[i].id+'">'+result_obj.dong[i].name+'</option>';
                                            }
                                            dong_select_str += '</select>';
                                            dong_select_str += '<span style="font-weight:bold;color:red;" id="dong_reminder"></span>';
                                            dong_select_str += '<div class="errorBox clear"></div>';
                                            $('#dong_div').html(dong_select_str);
                                            //单元
                                            unit_select_str += '<select class="select" name="unit">';
                                            unit_select_str += '<option value="">请选择</option>';
                                            unit_select_str += '</select>';
                                            unit_select_str += '<span id="unit_reminder" style="font-weight:bold;color:red;"></span>';
                                            unit_select_str += '<div class="errorBox clear"></div>';
                                            $('#unit_div').html(unit_select_str);
                                            //门牌
                                            door_select_str += '<select class="select" name="door">';
                                            door_select_str += '<option value="">请选择</option>';
                                            door_select_str += '</select>';
                                            door_select_str += '<span id="door_reminder" style="font-weight:bold;color:red;"></span>';
                                            door_select_str += '<div class="errorBox clear"></div>';
                                            $('#door_div').html(door_select_str);
                                        }else{
                                            //栋座
                                            dong_select_str += '<input class="input_text input_text_r w80" name="dong" id="dong" value="" type="text" onBlur="check_unique_house()">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="dong_reminder"></span><div class="errorBox clear"></div>';
                                            $('#dong_div').html(dong_select_str);
                                            //单元
                                            unit_select_str += '<input class="input_text input_text_r w80" name="unit" id="unit" value="" type="text" onBlur="check_unique_house()">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="unit_reminder"></span><div class="errorBox clear"></div>';
                                            $('#unit_div').html(unit_select_str);
                                            //门牌
                                            door_select_str += '<input class="input_text input_text_r w80" name="door" id="door" value="" type="text" onBlur="check_unique_house()">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="door_reminder"></span><div class="errorBox clear"></div>';
                                            $('#door_div').html(door_select_str);
                                        }
                                    }
                                });
                            }else{
                                //新增楼盘弹框中内容设置为空
                                $('#js_pop_add_new_block input').val('');
                                $('#js_pop_add_new_block select').each(function(){
                                    $(this).children('option').first().attr('selected','selected');
                                });
                                $('#js_pop_add_new_block textarea').val('');
                                $('#js_pop_add_new_block input[type="checkbox"]').attr('checked',false);
                                //openWin('js_pop_add_new_block');
                                removeinput = 1;
                            }
                        },
                        close: function(event) {
                            if(typeof(removeinput)=='undefined' || removeinput == 1){
                                $("#block_name").val("");
                                $("#block_id").val("");
                                $("#select_b").val("");
                                $("#select_q").val("");
                                $("#address").val("");
                                $('#buildyear').val("");
                            }
                        }
                    });
                });
                </script>
                <label class="label">
                <span class="text_fg">区属：</span>
                <div class="y_fg js_fields">
                    <input class="input_text w60" name="select_q" value="<?php echo $house_detail['district_name']; ?>" id="select_q" type="text" readonly >
                    <input name="district_id" id="district_id"  value="<?php echo $house_detail['district_id']; ?>" type="hidden">
                    <div class="errorBox clear"></div>
                </div>
                </label>
                <label class="label">
                <span class="text_fg">板块：</span>
                <div class="y_fg js_fields">
                    <input class="input_text w60" id="select_b" name="select_b" value="<?php echo $house_detail['street_name']; ?>" type="text" readonly >
                    <input name="street_id" id="street_id" value="<?php echo $house_detail['street_id']; ?>" type="hidden">
                    <div class="errorBox clear"></div>
                </div>
                </label>
                <label class="label">
                <span class="text_fg">地址：</span>
                <div class="y_fg js_fields">
                    <input class="input_text w260" id="address" name="address" value="<?php echo $house_detail['address']; ?>" type="text" readonly >
                    <div class="errorBox clear"></div>
                </div>
                </label>
            </div>
        </div>
        <input type="hidden" name="dong" id="dong" value="<?php echo $house_detail['dong']; ?>" />
        <input type="hidden" name="unit" id="unit" value="<?php echo $house_detail['unit']; ?>" />
        <input type="hidden" name="door" id="door" value="<?php echo $house_detail['door']; ?>" />
        <input type="hidden" name="owner" value="<?php echo $house_detail['owner']; ?>" />
        <input type="hidden" name="idcare" value="<?php echo $house_detail['idcare']; ?>" />
        <input type="hidden" name="telno1" value="<?php echo $house_detail['telno1']; ?>" id="telno1" />
        <input type="hidden" value="<?php echo $house_detail['telno2']; ?>" name="telno2" id="telno2" />
        <input type="hidden" value="<?php echo $house_detail['telno3']; ?>" name="telno3" id="telno3" />

        <script type="text/javascript">
            function check_unique_house()
            {
                var block_id = $.trim($('#block_id').val());
                var dong = $.trim($('#dong').val());
                var unit = $.trim($('#unit').val());
                var door = $.trim($('#door').val());
                var house_id = $.trim($('#house_id').val());

                if( block_id != '' && dong != '' && unit != '' && door != '' && house_id != '')
                {
                    $.ajax({
                        url: "/sell/check_unique_house_modify/",
                        type: "GET",
                        dataType: "HTML",
                        data: {block_id: block_id,dong: dong,unit: unit,door: door,house_id:house_id},
                        success: function(data)
                        {
                            //判断返回数据是否为空，不为空返回数据。
                            if(data == 0 )
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
				$(".display_htype").click(function(){
					$("#house_type").hide();
				});
			//住宅.别墅.有户型选项   显示
				$(".display_htype_yes").click(function(){
				$("#house_type").show();
				});
            }

        </script>
        <div class="forms_details_fg forms_details_fg_bg clearfix">
            <div class="clearfix">
						<h3 class="h3">房源信息</h3>
					</div>

            <div class="item_fg clearfix">
			 <div class="left width_b js_fields">
			 <div class="text_fg"><b class="red">*</b>物业类型：</div>
			 <span class="y_fg y_fg_p_l"><?php
					if($house_detail['sell_type']){
						echo $config['sell_type'][$house_detail['sell_type']];
					}
					?>
					<input name="sell_type" type="hidden" value="<?php if($house_detail['sell_type']){
                        echo $house_detail['sell_type'];}
					?>">
					</span>
					<div class="errorBox"></div>
			 </div>
                <div class="left width_b js_fields" >
                    <div class="text_fg"><b class="red">*</b>状态：</div>
                    <?php
						if($house_detail['isshare'] != "0"){
							foreach($config['status'] as $key =>$val)
							{
							   echo '<i class="label display_htype_yes label-no';
								if($house_detail['status'] > 0 && $house_detail['status'] == $key )
								{
								   echo ' label-no2 ';
								}
								echo '" style="color:#afafaf"><input type="radio" class="input_radio"';
								if($house_detail['status'] > 0 && $house_detail['status'] == $key ){  echo ' checked ';}
								echo ' > '.$val.'</i>';
							}
                            echo '<input type="hidden" name="status" value="'.$house_detail['status'].'"/>';
						}else{
							foreach($config['status'] as $key =>$val)
							{
							   echo '<i class="label display_htype_yes';
								if($house_detail['status'] > 0 && $house_detail['status'] == $key )
								{
								   echo ' labelOn ';
								}
								echo '" onclick="get_status('.$key.');"><input type="radio" class="input_radio"';
								if($house_detail['status'] > 0 && $house_detail['status'] == $key ){  echo ' checked ';}
								echo ' name="status" value="'.$key.'"> '.$val.'</i>';
							}
						}
                    ?>
                    <div class="errorBox"></div>
                </div>
            </div>

            <div class="item_fg clearfix">
                <div class="width_b left js_fields">
                    <div class="text_fg"><b class="red">*</b>房源性质：</div>
                    <?php
                        foreach($config['nature'] as $key =>$val)
                        {
                           echo '<i class="label display_htype_yes';
                            if($house_detail['nature'] > 0 && $house_detail['nature'] == $key )
                            {
                               echo ' labelOn ';

                            }
							echo '"><input type="radio" class="input_radio"';
							if($house_detail['nature'] > 0 && $house_detail['nature'] == $key ){  echo ' checked ';}
                            echo ' name="nature" value="'.$key.'"> '.$val.'</i>';
                        }
                    ?>
                    <div class="errorBox"></div>
                </div>
                <div class="left width_b" id="house_type" <?php if($house_detail['sell_type']>2){echo "style='display:none;'";}?>>
                    <div class="label "> <span class="text_fg"><b class="red">*</b>户型：</span>
                        <div class="y_fg">
                            <div class="left js_fields">
							<?php if($house_detail['isshare'] == "0"){?>
                                <select class="select" name="room" >
                                    <option value=""<?php if($house_detail['room'] == 1){echo "selected";}?> >请选择</option>
                                    <?php for($i=0;$i<10;$i++){ ?>
                                    <option value="<?php echo $i; ?>" <?php if($house_detail['room'] == $i){echo "selected";}?>><?php echo $i; ?></option>
                                    <?php }?>
                                </select>
							<?php } else {?>
								<select class="select" name="room" <?=($house_detail['isshare'] != "0")?" style='color:#afafaf'":''?>>
                                    <option value="<?=$house_detail['room']?>"><?=$house_detail['room']?></option>
                                </select>
							<?php } ?>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">室</span>
                            <div class="left js_fields">
							<?php if($house_detail['isshare'] == "0"){?>
                                <select class="select" name="hall" >
                                    <option value=""<?php if($house_detail['hall'] == 1){echo "selected";}?> >请选择</option>
                                    <?php for($i=0;$i<10;$i++){ ?>
                                    <option value="<?php echo $i; ?>" <?php if($house_detail['hall'] == $i){echo "selected";}?>><?php echo $i; ?></option>
                                    <?php }?>
                                </select>
							<?php } else {?>
								<select class="select" name="hall" <?=($house_detail['isshare'] != "0")?" style='color:#afafaf'":''?>>
                                    <option value="<?=$house_detail['hall']?>"><?=$house_detail['hall']?></option>
                                </select>
							<?php } ?>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">厅</span>
                            <div class="left js_fields">
							<?php if($house_detail['isshare'] == "0"){?>
                                <select class="select" name="toilet" >
                                    <option value=""<?php if($house_detail['toilet'] == 1){echo "selected";}?> >请选择</option>
                                    <?php for($i=0;$i<10;$i++){ ?>
                                    <option value="<?php echo $i; ?>" <?php if($house_detail['toilet'] == $i){echo "selected";}?>><?php echo $i; ?></option>
                                    <?php }?>
                                </select>
							<?php } else {?>
								<select class="select" name="toilet" <?=($house_detail['isshare'] != "0")?" style='color:#afafaf'":''?>>
                                    <option value="<?=$house_detail['toilet']?>"><?=$house_detail['toilet']?></option>
                                </select>
							<?php } ?>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">卫</span>
                            <div class="left js_fields">
							<?php if($house_detail['isshare'] == "0"){?>
                                <select class="select" name="kitchen" >
                                    <option value=""<?php if($house_detail['kitchen'] == 1){echo "selected";}?> >请选择</option>
                                    <?php for($i=0;$i<10;$i++){ ?>
                                    <option value="<?php echo $i; ?>" <?php if($house_detail['kitchen'] == $i){echo "selected";}?>><?php echo $i; ?></option>
                                    <?php }?>
                                </select>
							<?php } else {?>
								<select class="select" name="kitchen" <?=($house_detail['isshare'] != "0")?" style='color:#afafaf'":''?>>
                                    <option value="<?=$house_detail['kitchen']?>"><?=$house_detail['kitchen']?></option>
                                </select>
							<?php } ?>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">厨</span>
                            <div class="left js_fields">
							<?php if($house_detail['isshare'] == "0"){?>
                                <select class="select" name="balcony" >
                                    <option value=""<?php if($house_detail['balcony'] == 1){echo "selected";}?> >请选择</option>
                                    <?php for($i=0;$i<10;$i++){ ?>
                                    <option value="<?php echo $i; ?>" <?php if($house_detail['balcony'] == $i){echo "selected";}?>><?php echo $i; ?></option>
                                    <?php }?>
                                </select>
							<?php } else {?>
								<select class="select" name="balcony" <?=($house_detail['isshare'] != "0")?" style='color:#afafaf'":''?>>
                                    <option value="<?=$house_detail['balcony']?>"><?=$house_detail['balcony']?></option>
                                </select>
							<?php } ?>

                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">阳台</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item_fg clearfix" <?php if($house_detail['sell_type']>4){echo "style='display:none;'";}?>>
                <div class="width_b left">
                <div class="text_fg"><b class="red">*</b>朝向：</div>
                <div class="left js_fields">
				<?php if($house_detail['isshare'] == "0"){?>
                    <select class="select" name="forward" id="forward" >
                        <?php
                            foreach($config['forward'] as $key =>$val){
                        ?>
                        <option value="<?php echo $key;?>" <?php if($key==$house_detail['forward']){echo 'selected="selected"';}?>><?php echo $val;?></option>
                        <?php
                            }
                        ?>
                    </select>
				<?php }else{?>
					<select class="select" name="forward" id="forward" <?=($house_detail['isshare'] != "0")?" style='color:#afafaf'":''?>>
                        <option value="<?php echo $house_detail['forward'];?>">
						<?php
						foreach($config['forward'] as $key =>$val){
							if($key==$house_detail['forward']){
								echo $val;
							}
						}?></option>
                    </select>
				<?php } ?>
                    <div class="errorBox clear"></div>
                </div>
                </div>
                 <div class="left width_b">
                    <div class="text_fg"><b class="red">*</b>楼层：</div>
                    <i class="label <?php if($house_detail['floor_type']==1 || empty($house_detail['floor_type'])){echo "labelOn";}?> y_fg_p_r label_none" onclick="show_input('d_input','y_input')">
                        <input type="radio"  class="input_radio" name="floor_type" value="1" <?php if($house_detail['floor_type']==1 || empty($house_detail['floor_type'])){echo "checked";}?> >
                        单层</i>
                    <div class="y_fg js_fields" id="d_input" style="margin-right: 5px;" <?php if($house_detail['floor_type']==1 || empty($house_detail['floor_type'])){echo 'style="display:block;"';}else{echo 'style="display:none;"';}?>>
                        <input id="floor" class="input_text input_text_r w20" name="floor" value="<?php echo $house_detail['floor'];?>" type="text" <?=($house_detail['isshare'] != "0")?"readonly style='color:#afafaf'":''?>>
                        <div class="errorBox clear"></div>
                    </div>
                    <i class="label <?php if($house_detail['floor_type']==2){echo "labelOn";}?> label_none y_fg_p5 font_y" onclick="show_input('y_input','d_input')">
                        <input type="radio"  class="input_radio" name="floor_type"  value="2" <?php if($house_detail['floor_type']==2){echo "checked";}?> >
                        跃层</i>
                    <div class="y_fg hide"  id="y_input" <?php if($house_detail['floor_type']==2){echo 'style="display:block;"';}?>>
                        <div class="js_fields left js_fields">
                            <input class="input_text input_text_r w20" name="floor2"  value="<?php echo $house_detail['floor'];?>"  type="text" <?=($house_detail['isshare'] != "0")?"readonly style='color:#afafaf'":''?>>
                            <div class="errorBox clear"></div>
                        </div>
                        <span class="y_fg y_fg_p5">一</span>
                        <div class="js_fields left">
                            <input id="subfloor" class="input_text input_text_r w20 js_fields"  value="<?php echo $house_detail['subfloor'];?>"  name="subfloor" type="text" <?=($house_detail['isshare'] != "0")?"readonly style='color:#afafaf'":''?>>
                            <div class="errorBox clear"></div>
                        </div>
                    </div>
                    <label class="label label_none js_fields">
                    <span class="y_fg y_fg_p_l">总楼层：</span>
                    <div class="left">
                        <input class="input_text input_text_r w50" type="text" name="totalfloor" value="<?php echo $house_detail['totalfloor'];?>" id="z_louceng" <?=($house_detail['isshare'] != "0")?"readonly style='color:#afafaf'":''?>>
                        <div class="errorBox clear"></div>
                    </div>
                    </label>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b" <?php if($house_detail['sell_type']>4){echo "style='display:none;'";}?>>
                    <div class="text_fg"><b class="red">*</b>装修：</div>
                    <div class="left js_fields">
                        <?php
						if($house_detail['isshare'] != "0"){
							foreach($config['fitment'] as $key =>$val)
							{
							   echo '<i class="label display_htype_yes label-no';
								if($house_detail['fitment'] > 0 && $house_detail['fitment'] == $key )
								{
								   echo ' label-no2 ';

								}
								echo '" style="color:#afafaf"><input type="radio" class="input_radio"';
								if($house_detail['fitment'] > 0 && $house_detail['fitment'] == $key ){  echo ' checked ';}
								echo '> '.$val.'</i>';
							}
                            echo '<input type="hidden" name="fitment" value="'.$house_detail['fitment'].'"/>';
						}else{
							foreach($config['fitment'] as $key =>$val)
							{
							   echo '<i class="label display_htype_yes';
								if($house_detail['fitment'] > 0 && $house_detail['fitment'] == $key )
								{
								   echo ' labelOn ';

								}
								echo '"><input type="radio" class="input_radio"';
								if($house_detail['fitment'] > 0 && $house_detail['fitment'] == $key ){  echo ' checked ';}
								echo ' name="fitment" value="'.$key.'"> '.$val.'</i>';
							}
						}
                        ?>
                        <div class="errorBox"></div>
                    </div>
                </div>
                <label class="label">
                <span class="text_fg"><b class="red">*</b>房龄：</span>
                <div class="y_fg js_fields">
				<?php if($house_detail['isshare'] == "0"){?>
                    <select class="select" name="buildyear" id="buildyear">
                        <option value="0" selected >请选择</option>
                        <?php
                            for($_i=2015;$_i>=1970;$_i--)
                            {
                                echo '<option value="'.$_i.'"';
                                if($house_detail['buildyear'] == $_i)
                                {
                                    echo " selected ";
                                }
								if($house_detail['isshare'] != "0"){ echo " disabled style='color:#afafaf'";}
                                echo '>'.$_i.'年</option>';
                            }
                        ?>
                    </select>
				<?php }else{?>
					<select class="select" name="buildyear" id="buildyear" <?=($house_detail['isshare'] != "0")?" style='color:#afafaf'":''?>>
                        <?php
                            for($_i=2015;$_i>=1970;$_i--)
                            {
                                if($house_detail['buildyear'] == $_i)
                                {
									echo '<option value="'.$_i.'"';
									echo '>'.$_i.'年</option>';
								}
                            }
                        ?>
                    </select>
				<?php } ?>
                    <div class="errorBox clear"></div>
                </div>
                </label>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b">
                    <label class="label">
                    <span class="text_fg"><b class="red">*</b>建筑面积：</span>
                    <div class="y_fg js_fields">
                        <input class="input_text input_text_r w60" name="buildarea" id="buildarea" type="text" value="<?php echo strip_end_0($house_detail['buildarea']);?>" onblur="get_avgprice();" <?=($house_detail['isshare'] != "0")?"readonly style='color:#afafaf'":''?>>
                        <span class="y_fg y_fg_p_l_5">平方米</span>
                        <div class="errorBox clear"></div>
                    </div>
                    </label>
                    <label class="label">
                    <span class="text_fg">使用面积：</span>
                    <div class="y_fg js_fields">
                        <input class="input_text w60" name="usage_area" id="usage_area" type="text" value="<?php echo strip_end_0($house_detail['usage_area']);?>" >
                        <span class="y_fg y_fg_p_l_5">平方米</span>
                        <div class="errorBox clear"></div>
                    </div>
                    </label>
                </div>
                <div class="left js_s_h_info js_s_ZZ_info" <?php if($house_detail['sell_type'] > 1){echo 'style="display:none;"';}?>><!--住宅-->
                    <div class="text_fg">类型：</div>
                    <?php
                    foreach($config['house_type'] as $key =>$val)
                    {
                        echo '<i class="label display_htype_yes';
                        if($house_detail['house_type'] == $key )
                        {
                           echo ' labelOn ';
                        }
                        echo '"><input type="radio" class="input_radio"';
                            if($house_detail['house_type'] == $key ){  echo ' checked ';}
                        echo ' name="house_type" value="'.$key.'"> '.$val.'</i>';
                    }
                    ?>
                </div>
            </div>
            <div class="item_fg clearfix">
                <label class="label">
                <span class="text_fg"><b class="red">*</b>总价：</span>
                <div class="y_fg js_fields">
                    <input class="input_text input_text_r w60" name="price" type="text" id="price" value="<?php echo strip_end_0($house_detail['price']);?>" onblur="get_avgprice();">
                    <span class="y_fg y_fg_p_l_5">万元</span>
                    <div class="errorBox clear"></div>
                </div>
                </label>
                <span class="text_fg"	>底价：</span>
                <div class="y_fg js_fields">
                    <input class="input_text w60" name="lowprice" value="<?php if($house_detail['lowprice']>0){echo strip_end_0($house_detail['lowprice']);}?>" type="text">
                    <span class="y_fg y_fg_p_l_5">万元(加密)</span>
                    <div class="errorBox clear"></div>
                </div>
                </label>
                <label class="label">
                <span class="text_fg">单价：</span>
                <div class="y_fg js_fields">
                    <input class="input_text w60" name="avgprice" id="avgprice" value="<?php echo strip_end_0($house_detail['avgprice']);?>" type="text" readonly>
                    <span class="y_fg y_fg_p_l_5">元/平米</span>
                    <div class="errorBox clear"></div>
                </div>
                </label>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b js_fields">
                    <div class="text_fg"><b class="red">*</b>税费：</div>
                    <?php
                        foreach($config['taxes'] as $key =>$val)
                        {   echo '<i class="label display_htype_yes';
                            if($house_detail['taxes'] > 0 && $house_detail['taxes'] == $key )
                            {
                               echo ' labelOn ';
                            }
							echo ' onclick="get_status('.$key.');" ><input type="radio" class="input_radio"';
								if($house_detail['taxes'] > 0 && $house_detail['taxes'] == $key ){  echo ' checked ';}
                            echo ' name="taxes" value="'.$key.'"> '.$val.'</i>';
                        }
                    ?>
                    <div class="errorBox"></div>
                </div>
               <div class="left js_fields">
                    <div class="text_fg "><b class="red">*</b>钥匙：</div>
                     <i class="label <?php if($house_detail['keys']==1){echo "labelOn";}?> display_htype_yes" onclick="javascript:$('#key_number').val('<?php echo $house_detail['key_number'];?>');javascript:$('.key_label').show();">
                        <input type="radio" class="input_radio" name="keys" value="1" <?php if($house_detail['keys']==1){echo "checked";}?> />
                        有</i>
                    <label class="label key_label" <?php if(empty($house_detail['keys'])){echo "style='display: none;'";}?>>
                        <span class="text_fg">钥匙编号：</span>
                        <div class="y_fg">
                            <input class="input_text input_text_r w80" type="text" value="<?php echo $house_detail['key_number'];?>" id="key_number" name="key_number">
                        </div>
                    </label>
                    <i class="label <?php if(empty($house_detail['keys'])){echo "labelOn";}?> display_htype_yes" onclick="javascript:$('#key_number').val('');$('.key_label').hide();">
                        <input type="radio"class="input_radio" <?php if(empty($house_detail['keys'])){echo "checked";}?> name="keys" value="0"/>
                        无</i>
                    <div class="errorBox"></div>
                </div>
            </div>
            <div class="item_fg clearfix">

                <div class="left js_fields">
                    <div class="text_fg "><b class="red">*</b>委托类型：</div>
                    <?php
                        foreach($config['entrust'] as $key =>$val)
                        {
                           echo '<i class="label display_htype_yes';
                            if($house_detail['entrust'] > 0 && $house_detail['entrust'] == $key )
                            {
                               echo ' labelOn ';
                            }
							echo '"><input type="radio" class="input_radio"';
								if($house_detail['entrust'] > 0 && $house_detail['entrust'] == $key ){  echo ' checked ';}
                            echo ' name="entrust" value="'.$key.'"> '.$val.'</i>';
                        }
                    ?>
                    <div class="errorBox"></div>
                </div>
            </div>
        </div>

		<?php if($group_id == '2'){ ?>
        <div class="forms_details_fg forms_details_fg_bg clearfix">
			<input type="hidden" name="isshare_back" value="<?=$house_detail['isshare']?>">
            <?php if($house_detail['isshare'] == "0"){ ?>
                <div  class="item_fg reset_P clearfix">
                    <div class="text_fg"><b class="red">*</b>是否合作：</div>
                    <div class="left" id="status_1" style="display:block;">
                        <?php if('1'==$open_cooperate){?>
                            <?php if('1'==$check_cooperate){?>
                                <?php if($house_detail['isshare'] == 2){?>
                                    <span style="color:red;">店长审核中</span>
                                <?php }else if($house_detail['isshare'] == 3){?>
                                    <span style="color:red;">资料审核中</span>
                                <?php }else{ ?>
                                <i class="label mod_p <?php if($house_detail['isshare'] != "0"){echo "labelOn";}?>" id = "js_gs_01">是
                                    <input type="radio" <?php if($house_detail['isshare'] != "0"){echo "checked";}?> class="input_radio" name="isshare" value="2">
                                </i>
                                <i class="label mod_p <?php if(empty($house_detail['isshare'])){echo "labelOn";}?>"  id = "js_gs_02">否
                                    <input type="radio" <?php if(empty($house_detail['isshare'])){echo "checked";}?> class="input_radio" value="0" name="isshare">
                                </i>
                                <?php }?>
                            <?php }else{?>
                                    <i class="label mod_p <?php if($house_detail['isshare'] != "0"){echo "labelOn";}?>" id = "js_gs_01">是
                                        <input type="radio" <?php if($house_detail['isshare'] != "0"){echo "checked";}?> class="input_radio" name="isshare" value="1">
                                    </i>
                                    <i class="label mod_p <?php if($house_detail['isshare']!=1){echo "labelOn";}?>"  id = "js_gs_02">否
                                        <input type="radio" <?php if($house_detail['isshare']!=1){echo "checked";}?> class="input_radio" value="<?php echo ($house_detail['isshare']=='2')?'2':'0'; ?>" name="isshare">
                                    </i>
                            <?php }?>
                        <?php }else{?>
                        <input type="hidden" name="isshare" value="<?php echo $house_detail['isshare']; ?>"/>
                        <i class="label mod_p <?php if($house_detail['isshare'] != "0"){echo "label-no2";}else{echo "label-no";}?>" id = "js_gs_01">是
                        </i>
                        <i class="label mod_p <?php if(empty($house_detail['isshare'])){echo "label-no2";}else{echo "label-no";}?>"  id = "js_gs_02">否
                        </i>
                        <?php }?>
                    </div>
                    <div class="left" id="status_not_1" style="display:none;">
                        <i class="label-no mod_p">是
                        </i>
                        <i class="label-no2 mod_p labelOn ">否
                        </i>
                    </div>
                    <span class="info_bc left" id="prompt">
                    <?php if('1'==$open_cooperate){?>
                        <?php if('1'==$check_cooperate){?>
                        需通过合作审核后，进入合作中心
                        <?php }else{?>
                        合作房源将在合作中心展示，帮助您高效合作，快速成交！甲方为房源委托方，乙方为购房委托方。
                        <?php }?>
                    <?php }else{?>
                        店长已关闭合作功能
                    <?php }?>
                    </span>
                </div>

				<div  class="item_fg reset_P clearfix" style="display:none;" id="js_show_friend">
					<div class="text_fg"><b class="red">*</b>发送到朋友圈：</div>
					<div class="left" >
						<i class="label mod_p" id = "js_gs_01_friend">是
							<input type="radio"  class="input_radio" name="isshare_friend" value="1">
						</i>
						<i class="label mod_p labelOn "  id = "js_gs_02_friend">否
							<input type="radio" checked="true"  class="input_radio" value="0" name="isshare_friend">
						</i>
					</div>
					<span class="info_bc left" id="prompt">
					<?php if('1'==$open_cooperate){?>
						<?php if('1'==$check_cooperate){?>
						需通过合作审核后，进入合作朋友圈。
						<?php }else{?>
						选择发送到朋友圈后，该合作房源只有朋友圈用户才能看到。
						<?php }?>
					<?php }else{?>
						店长已关闭合作功能
					<?php }?>
					</span>
				</div>
            <?php } ?>

            <?php if('1'==$open_cooperate){?>
            <div id="js_show_yj" <?php echo (($house_detail['isshare']=='1' || $house_detail['isshare']=='2'|| $house_detail['isshare']=='3') )?'style="display:block"':'style="display:none"';?>>
				<div  class="item_fg reset_P clearfix">
					<div class="text_fg" style="width:106px; padding-left:7px;"><b class="red">*</b>请选择奖励方式：</div>
				</div>
				<div  class="item_fg reset_P item_fg3 clearfix">
                    <?php if($house_detail['isshare']=='0'){ ?>
                        <i class="label display_htype_yes shangjin_tab <?php if('2'!=$house_detail['reward_type']){echo 'labelOn';}?>" style="width:95%;">
                        <span style="float:left;">
                        佣金分成
                            <input type="radio"  class="input_radio" name="reward_type" value="1" <?php if('2'!=$house_detail['reward_type']){echo 'checked="true"';}?>><img title="房源成交后，需要给予合作方成交佣金一定比例的奖励，5:5分成为参考方案，具体以经纪人商定为准" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico2.png">
                            </span>
                            <span class="text_fg" style="width:38px;"></span>
                            <div class="left">
                                <div class="label ">
                                    <div class="y_fg">
                                    <span class="y_fg y_fg_p5">分佣比例</span>
                                        <div class="left js_fields">
                                            <select class="select" name="commission_ratio"  readonly>
                                            <?php foreach ($config['commission_ratio'] as $k => $v) {?>
                                                <option value="<?php echo $k;?>" <?php if ($k == 5) {echo 'selected';} ?>><?php echo $v;?>分成</option>
                                            <?php } ?>
                                            </select>
                                            &nbsp;&nbsp;
                                            <b style="font-weight:normal;color:#FAC16B">(房源方：客源方)</b>
                                            <div class="errorBox clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </i>
                        <i class="label display_htype_yes shangjin_tab mt10 <?php if('2'==$house_detail['reward_type']){echo 'labelOn';}?>">设置奖金
                            <input type="radio"  class="input_radio" name="reward_type" value="2" <?php if('2'==$house_detail['reward_type']){echo 'checked="true"';}?>><img title="待房源成交后，需要给予合作方一定的奖励金额，该奖励金额最低1000元，最高为总价的3%" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico2.png">
                        </i>
                    <?php }else{ ?>
                        <i class="label display_htype_yes label-no <?php if('1'==$house_detail['reward_type']){echo 'label-no2';}?>" style="width:95%;">
                        <span style="float:left;margin-right:13px">
                        佣金分成
                            <input type="radio"  class="input_radio" <?php if('1'==$house_detail['reward_type']){echo 'checked="true"';}?>><img title="房源成交后，需要给予合作方成交佣金一定比例的奖励，5:5分成为参考方案，具体以经纪人商定为准" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico2.png">
                            </span>
                            <span class="text_fg" style="width:38px;"></span>
                            <div class="left js_fields">
                                <select class="select" name="commission_ratio"  disabled="disabled">
                                    <?php foreach ($config['commission_ratio'] as $k => $v) {?>
                                        <option value="<?php echo $k;?>" <?php if ($k == $house_detail['commission_ratio']) {echo 'selected';} ?>><?php echo $v;?>分成</option>
                                    <?php } ?>
                                </select>
                                &nbsp;&nbsp;
                                <b style="font-weight:normal;color:#FAC16B">(房源方：客源方)</b>
                                <div class="errorBox clear"></div>
                            </div>
                        </i>
                        <i class="label display_htype_yes label-no mt10 <?php if('2'==$house_detail['reward_type']){echo 'label-no2';}?>">设置奖金
                            <input type="radio"  class="input_radio" <?php if('2'==$house_detail['reward_type']){echo 'checked="true"';}?>><img title="待房源成交后，需要给予合作方一定的奖励金额，该奖励金额最低1000元，最高为总价的3%" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico2.png">
                        </i>
                        <input type="hidden" value="<?php echo $house_detail['reward_type'];?>" name="reward_type"/>
                    <?php }?>

					<div class="left" style="margin-top:7px;">
						<span class="text_fg" style="width:38px;"></span>
						<div class="y_fg js_fields">
							<input class="input_text input_text_02 w160" id="shangjin" name="shangjin" type="text" value="<?php echo empty($house_detail['cooperate_reward'])?'最低金额不能少于1000元':$house_detail['cooperate_reward']; ?>"  onfocus="if(value=='最低金额不能少于1000元'){value='';$(this).css('color','#535353')}" onblur="if(value==''){value='最低金额不能少于1000元';$(this).css('color','#999')}">
							<span class="y_fg y_fg_p_l_5">元</span>
							<div class="errorBox clear"></div>
						</div>
					</div>
				</div>
			 <div class="js_s_h_info_house mt10 item_fg3">
                <div class="add_pic_house_title">委托协议书<span style="color:#deb171;">（如设置奖金，须上传合作资料 ，选择佣金分成，非必传项）</span></div>
                <div class="add_pic_house_box clearfix">
                    <div class="add_item">
                        <span id="spanButtonPlaceholder3"></span>
                    </div>

                    <script type="text/javascript">
                    var swfu3;
                    $(function() {
                    swfu3 = new SWFUpload({
                        // Backend Settings
                        file_post_name: "file",
                        upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                        //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                        //post_params: {"postion" : position},
                        // File Upload Settings
                        file_size_limit : "5 MB",
                        file_types : "*.jpg;*.png",
                        file_types_description : "JPG Images",
                        file_upload_limit : "0",
                        file_queue_limit : "5",

                        custom_settings : {
                            upload_target : "jsPicPreviewBoxM3",
                            upload_limit  :
                                <?php
                                    if('2'==$house_detail['cooperate_check']){
                                        echo '0';
                                    }else if('3'==$house_detail['cooperate_check']){
                                        if(is_full_array($picinfo3)){
                                            echo '0';
                                        }else{
                                            echo '3';
                                        }
                                    }else{
                                        echo '3';
                                    }
                                ?>,
                            upload_nail	  : "thumbnails3",
                            upload_infotype : 3	},

                        // Event Handler Settings - these functions as defined in Handlers.js
                        //  The handlers are not part of SWFUpload but are part of my website and control how
                        //  my website reacts to the SWFUpload events.
                        swfupload_loaded_handler : swfUploadLoaded,
                        file_queue_error_handler : fileQueueError,
                        file_dialog_start_handler : fileDialogStart,
                        file_dialog_complete_handler : fileDialogComplete,
                        upload_progress_handler : uploadProgress,
                        upload_error_handler : uploadError,
                        upload_success_handler : uploadSuccessNew,
                        upload_complete_handler : uploadComplete,


                        // Button Settings
                        button_image_url : "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn02.png",
                        button_placeholder_id : "spanButtonPlaceholder3",
                        button_width: 130,
                        button_height: 100,
                        button_cursor: SWFUpload.CURSOR.HAND,
                        button_text:"",
                        flash_url : "/swfupload.swf"
                    });

                    //标签个数限制
                        $('.sell_tag b').live('click',function(){
                            var sell_tag_num = $('.sell_tag').find('.labelOn').size();
                            if(sell_tag_num > 3){
                                $(this).find(".js_checkbox").prop("checked",false);
                                $(this).removeClass("labelOn");
                            }
                        });
                    });
                    </script>
                    <div id="jsPicPreviewBoxM3" style="display:none" ></div>
                    <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails3">
                        <?php
                            if($picinfo3)
                            {
                               $coo_check_str = '';
                               if('2'==$house_detail['cooperate_check']){
                                   $coo_check_str = '<span class="shh shh1"></span>';
                               }else if('3'==$house_detail['cooperate_check']){
                                   $coo_check_str = '<span class="shh shh3"></span>';
                               }else if('4'==$house_detail['cooperate_check']){
                                   $coo_check_str = '<span class="shh shh4"></span>';
                               }
                               $check_inner = 1;
                               foreach($picinfo3 as $key => $val)
                                {
                                   if($check_inner == 1){
                                       echo '<div class="add_item_pic add_item_pic0"><div class="pic"><img height="100" width="130" src="'.$val['url'].'"><input class="hidden_1" type="hidden" value="'.$val['url'].'" name="p_filename3[]"><input class="hidden_2" type="hidden" value="'.$val['id'].'" name="p_fileids3[]"></div><div class="fun"><a href="javascript:void(0);" class="label_pic" onClick="prevOrNextFun(this)">设为首图</a><a class="del_pic" href="javascript:void(0);" onClick="fun_hide_p(this);swfu3.setButtonDisabled(false);">删除</a><a class="del_left" href="javascript:void(0);"  onClick="prevOrNextFun(this)">左移</a><a class="del_right" href="javascript:void(0);" onClick="prevOrNextFun(this)">右移</a> <p class="fun-bg">背景</p></div>'.$coo_check_str.'</div>
                                        ';
                                   }else{
                                       echo '<div class="add_item_pic"><div class="pic"><img height="100" width="130" src="'.$val['url'].'"><input class="hidden_1" type="hidden" value="'.$val['url'].'" name="p_filename3[]"><input class="hidden_2" type="hidden" value="'.$val['id'].'" name="p_fileids3[]"></div><div class="fun"><a href="javascript:void(0);" class="label_pic" onClick="prevOrNextFun(this)">设为首图</a><a class="del_pic" href="javascript:void(0);" onClick="fun_hide_p(this);swfu3.setButtonDisabled(false);">删除</a><a class="del_left" href="javascript:void(0);"  onClick="prevOrNextFun(this)">左移</a><a class="del_right" href="javascript:void(0);" onClick="prevOrNextFun(this)">右移</a> <p class="fun-bg">背景</p></div>'.$coo_check_str.'</div>
                                        ';
                                   }
                                   $check_inner++;
                                }
                            }
                        ?>
                    </div>
                </div>

            </div>
			 <div class="js_s_h_info_house item_fg3">
                <div class="add_pic_house_title">卖家身份证</div>
                <div class="add_pic_house_box clearfix">
                    <div class="add_item">
                        <span id="spanButtonPlaceholder3"></span>
                    </div>

                    <script type="text/javascript">
                    var swfu4;
                    $(function() {
                    swfu4 = new SWFUpload({
                        // Backend Settings
                        file_post_name: "file",
                        upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                        //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                        //post_params: {"postion" : position},
                        // File Upload Settings
                        file_size_limit : "5 MB",
                        file_types : "*.jpg;*.png",
                        file_types_description : "JPG Images",
                        file_upload_limit : "0",
                        file_queue_limit : "5",

                        custom_settings : {
                            upload_target : "jsPicPreviewBoxM4",
                            upload_limit  : 1,
                            upload_nail	  : "thumbnails4",
                            upload_infotype : 4	},

                        // Event Handler Settings - these functions as defined in Handlers.js
                        //  The handlers are not part of SWFUpload but are part of my website and control how
                        //  my website reacts to the SWFUpload events.
                        swfupload_loaded_handler : swfUploadLoaded,
                        file_queue_error_handler : fileQueueError,
                        file_dialog_start_handler : fileDialogStart,
                        file_dialog_complete_handler : fileDialogComplete,
                        upload_progress_handler : uploadProgress,
                        upload_error_handler : uploadError,
                        upload_success_handler : uploadSuccessNew,
                        upload_complete_handler : uploadComplete,


                        // Button Settings
                        button_image_url : "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn02.png",
                        button_placeholder_id : "spanButtonPlaceholder3",
                        button_width: 130,
                        button_height: 100,
                        button_cursor: SWFUpload.CURSOR.HAND,
                        button_text:"",
                        flash_url : "/swfupload.swf"
                    });

                    //标签个数限制
                        $('.sell_tag b').live('click',function(){
                            var sell_tag_num = $('.sell_tag').find('.labelOn').size();
                            if(sell_tag_num > 3){
                                $(this).find(".js_checkbox").prop("checked",false);
                                $(this).removeClass("labelOn");
                            }
                        });
                    });
                    </script>
                    <div id="jsPicPreviewBoxM4" style="display:none" ></div>
                    <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails4">
                        <?php
                            if($picinfo4)
                            {
                               $coo_check_str = '';
                               if('2'==$house_detail['cooperate_check']){
                                   $coo_check_str = '<span class="shh shh1"></span>';
                               }else if('3'==$house_detail['cooperate_check']){
                                   $coo_check_str = '<span class="shh shh3"></span>';
                               }else if('4'==$house_detail['cooperate_check']){
                                   $coo_check_str = '<span class="shh shh4"></span>';
                               }
                               $check_inner = 1;
                               foreach($picinfo4 as $key => $val)
                                {
                                   if($check_inner == 1){
                                       echo '<div class="add_item_pic add_item_pic0"><div class="pic"><img height="100" width="130" src="'.$val['url'].'"><input class="hidden_1" type="hidden" value="'.$val['url'].'" name="p_filename4[]"><input class="hidden_2" type="hidden" value="'.$val['id'].'" name="p_fileids4[]"></div><div class="fun"><a href="javascript:void(0);" class="label_pic" onClick="prevOrNextFun(this)">设为首图</a><a class="del_pic" href="javascript:void(0);" onClick="fun_hide_p(this);swfu4.setButtonDisabled(false);">删除</a><a class="del_left" href="javascript:void(0);"  onClick="prevOrNextFun(this)">左移</a><a class="del_right" href="javascript:void(0);" onClick="prevOrNextFun(this)">右移</a> <p class="fun-bg">背景</p></div>'.$coo_check_str.'</div>
                                        ';
                                   }else{
                                       echo '<div class="add_item_pic"><div class="pic"><img height="100" width="130" src="'.$val['url'].'"><input class="hidden_1" type="hidden" value="'.$val['url'].'" name="p_filename4[]"><input class="hidden_2" type="hidden" value="'.$val['id'].'" name="p_fileids4[]"></div><div class="fun"><a href="javascript:void(0);" class="label_pic" onClick="prevOrNextFun(this)">设为首图</a><a class="del_pic" href="javascript:void(0);" onClick="fun_hide_p(this);swfu4.setButtonDisabled(false);">删除</a><a class="del_left" href="javascript:void(0);"  onClick="prevOrNextFun(this)">左移</a><a class="del_right" href="javascript:void(0);" onClick="prevOrNextFun(this)">右移</a> <p class="fun-bg">背景</p></div>'.$coo_check_str.'</div>
                                        ';
                                   }
                                   $check_inner++;
                                }
                            }
                        ?>
                    </div>
                </div>
                </div>

			 <div class="js_s_h_info_house item_fg3">
                <div class="add_pic_house_title">房产证</div>
                <div class="add_pic_house_box clearfix">
                    <div class="add_item">
                        <span id="spanButtonPlaceholder3"></span>
                    </div>

                    <script type="text/javascript">
                    var swfu5;
                    $(function() {
                    swfu5 = new SWFUpload({
                        // Backend Settings
                        file_post_name: "file",
                        upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                        //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                        //post_params: {"postion" : position},
                        // File Upload Settings
                        file_size_limit : "5 MB",
                        file_types : "*.jpg;*.png",
                        file_types_description : "JPG Images",
                        file_upload_limit : "0",
                        file_queue_limit : "5",

                        custom_settings : {
                            upload_target : "jsPicPreviewBoxM5",
                            upload_limit  : 1,
                            upload_nail	  : "thumbnails5",
                            upload_infotype : 5	},

                        // Event Handler Settings - these functions as defined in Handlers.js
                        //  The handlers are not part of SWFUpload but are part of my website and control how
                        //  my website reacts to the SWFUpload events.
                        swfupload_loaded_handler : swfUploadLoaded,
                        file_queue_error_handler : fileQueueError,
                        file_dialog_start_handler : fileDialogStart,
                        file_dialog_complete_handler : fileDialogComplete,
                        upload_progress_handler : uploadProgress,
                        upload_error_handler : uploadError,
                        upload_success_handler : uploadSuccessNew,
                        upload_complete_handler : uploadComplete,


                        // Button Settings
                        button_image_url : "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn02.png",
                        button_placeholder_id : "spanButtonPlaceholder3",
                        button_width: 130,
                        button_height: 100,
                        button_cursor: SWFUpload.CURSOR.HAND,
                        button_text:"",
                        flash_url : "/swfupload.swf"
                    });

                    //标签个数限制
                        $('.sell_tag b').live('click',function(){
                            var sell_tag_num = $('.sell_tag').find('.labelOn').size();
                            if(sell_tag_num > 3){
                                $(this).find(".js_checkbox").prop("checked",false);
                                $(this).removeClass("labelOn");
                            }
                        });
                    });
                    </script>
                    <div id="jsPicPreviewBoxM5" style="display:none" ></div>
                    <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails5">
                        <?php
                            if($picinfo5)
                            {
                               $coo_check_str = '';
                               if('2'==$house_detail['cooperate_check']){
                                   $coo_check_str = '<span class="shh shh1"></span>';
                               }else if('3'==$house_detail['cooperate_check']){
                                   $coo_check_str = '<span class="shh shh3"></span>';
                               }else if('4'==$house_detail['cooperate_check']){
                                   $coo_check_str = '<span class="shh shh4"></span>';
                               }
                               $check_inner = 1;
                               foreach($picinfo5 as $key => $val)
                                {
                                   if($check_inner == 1){
                                       echo '<div class="add_item_pic add_item_pic0"><div class="pic"><img height="100" width="130" src="'.$val['url'].'"><input class="hidden_1" type="hidden" value="'.$val['url'].'" name="p_filename5[]"><input class="hidden_2" type="hidden" value="'.$val['id'].'" name="p_fileids5[]"></div><div class="fun"><a href="javascript:void(0);" class="label_pic" onClick="prevOrNextFun(this)">设为首图</a><a class="del_pic" href="javascript:void(0);" onClick="fun_hide_p(this);swfu5.setButtonDisabled(false);">删除</a><a class="del_left" href="javascript:void(0);"  onClick="prevOrNextFun(this)">左移</a><a class="del_right" href="javascript:void(0);" onClick="prevOrNextFun(this)">右移</a> <p class="fun-bg">背景</p></div>'.$coo_check_str.'</div>
                                        ';
                                   }else{
                                       echo '<div class="add_item_pic"><div class="pic"><img height="100" width="130" src="'.$val['url'].'"><input class="hidden_1" type="hidden" value="'.$val['url'].'" name="p_filename5[]"><input class="hidden_2" type="hidden" value="'.$val['id'].'" name="p_fileids5[]"></div><div class="fun"><a href="javascript:void(0);" class="label_pic" onClick="prevOrNextFun(this)">设为首图</a><a class="del_pic" href="javascript:void(0);" onClick="fun_hide_p(this);swfu5.setButtonDisabled(false);">删除</a><a class="del_left" href="javascript:void(0);"  onClick="prevOrNextFun(this)">左移</a><a class="del_right" href="javascript:void(0);" onClick="prevOrNextFun(this)">右移</a> <p class="fun-bg">背景</p></div>'.$coo_check_str.'</div>
                                        ';
                                   }
                                   $check_inner++;
                                }
                            }
                        ?>
                    </div>
                </div>
                </div>
            </div>

            <?php }?>

            <?php if('1'==$open_cooperate){
                          if($house_detail['isshare'] !== 2){?>
                <script>
                    function get_status(status){
                        var cooperate_html = "";
                        var check_cooperate = <?php echo $check_cooperate; ?>;
                        if(1==check_cooperate){
                            cooperate_html = "需通过合作审核后，进入合作中心";
                        }else{
                            cooperate_html = "合作房源将在合作中心展示，帮助您高效合作，快速成交！甲方为房源委托方，乙方为购房委托方。"
                        }
                        if(status >1){
                            $('#status_1').hide();
                            $('#status_not_1').show();
                            $("#prompt").text("只有有效房客源才能设置合作");
                        }else{
                            $('#status_1').show();
                            $('#status_not_1').hide();
                            $("#prompt").text(cooperate_html);
                        }
                    }
                </script>
            <?php }}?>
            <div  class="item_fg reset_P clearfix" style="display:none">
                <div class="text_fg"><b class="red">*</b>是否群发房源：</div>
                <div class="left">
                    <i class="label mod_p <?php if($house_detail['is_publish'] == 1){echo "labelOn";}?>" id = "js_gs_01">是
                        <input type="radio" <?php if($house_detail['is_publish'] == 1){echo "checked";}?> class="input_radio" name="is_publish" value="1">
                    </i>
                    <i class="label mod_p <?php if(empty($house_detail['is_publish'])){echo "labelOn";}?>"  id = "js_gs_02">否
                        <input type="radio" <?php if(empty($house_detail['is_publish'])){echo "checked";}?> class="input_radio" value="0" name="is_publish">
                    </i>
                </div>
                <span class="info_bc left">群发房源将在群发管理模块展示，帮助您在多个网站一键发布房源，方便快捷！</span>
            </div>

            <!--            <div  class="item_fg reset_P clearfix">-->
            <!--                <div class="text_fg"><b class="red">*</b>是否同步：</div>-->
            <!--                <div class="left">-->
            <!--					--><?php
            //					if($group_id!='2'){//未认证用户
            //					?>
            <!--                    <i class="label mod_p label-no" id = "js_gs_01">是-->
            <!--                        <input type="radio"  class="input_radio" name="is_outside" value="1">-->
            <!--                    </i>-->
            <!--                    <i class="label mod_p labelOn label-no2"  id = "js_gs_02">否-->
            <!--                        <input type="radio" checked="true"  class="input_radio" value="0" name="is_outside">-->
            <!--                    </i>-->
            <!--					--><?php
            //					}elseif($is_fang100_insert==1){//开启房源必须同步
            //					?>
            <!--					<i class="label mod_p labelOn label-no2" id = "js_gs_01">是-->
            <!--                        <input type="radio"  checked="true" class="input_radio" name="is_outside" value="1">-->
            <!--                    </i>-->
            <!--                    <i class="label mod_p label-no" id = "js_gs_02">否-->
            <!--                        <input type="radio" class="input_radio" value="0" name="is_outside">-->
            <!--                    </i>-->
            <!--					--><?php
            //					}else{
            //					?>
            <!--					<i class="label mod_p -->
            <?php //if($house_detail['is_outside'] == 1){echo "labelOn";}?><!--">是-->
            <!--						<input type="radio" -->
            <?php //if($house_detail['is_outside'] == 1){echo "checked";}?><!-- class="input_radio" name="is_outside" value="1">-->
            <!--					</i>-->
            <!--					<i class="label mod_p -->
            <?php //if(empty($house_detail['is_outside'])){echo "labelOn";}?><!--">否-->
            <!--						<input type="radio" -->
            <?php //if(empty($house_detail['is_outside'])){echo "checked";}?><!-- class="input_radio" value="0" name="is_outside">-->
            <!--					</i>-->
            <!--					--><?php
            //					}
            //					?>
            <!--                </div>-->
            <!--            </div>-->
        </div>
		<div class="forms_details_fg forms_details_fg_bg clearfix" <?php if($house_detail['isshare']!="1" && $house_detail['isshare']!="2"){echo "style='display:none'";}?>>
			<p class="f00" style="padding-left:30px; margin-bottom:10px;">该房源已经设置合作，合作房源部分选项不能修改，如需要修改请取消合作</p>
		</div>
        <?php } ?>

        <div class="forms_details_fg forms_details_fg_bg clearfix" >
           <div class="clearfix"> <h3 class="h3">补充信息</h3><span class="js_s_h_btn s_h_btn">展开<span class="iconfont">&#xe609;</span></span></div>
			 <div class="js_s_h_info_house hide">
                <div class="item_fg clearfix">
                    <div class="left width_b" >
                        <div class="text_fg">房源等级：</div>
                        <?php
                        foreach($config['house_grade'] as $key =>$val)
                        {
                            echo '<i class="label display_htype_yes';
                            if($house_detail['house_grade'] == $key )
                            {
                               echo ' labelOn ';
                            }
							echo '"><input type="radio" class="input_radio"';
								if($house_detail['house_grade'] == $key ){  echo ' checked ';}
                            echo ' name="house_grade" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>
                    <div class="left">
                        <div class="text_fg"> 房源结构：</div>
                        <select class="select" name="house_structure">
						<option value="0" <?php if($house_detail['house_structure']==0){echo 'selected="selected"';}?>>不限</option>
                        <?php
                        foreach($config['house_structure'] as $key =>$val)
                        {
                        ?>
                            <option value="<?php echo $key;?>" <?php if($house_detail['house_structure']==$key){echo 'selected="selected"';}?>><?php echo $val;?></option>
                        <?php
						}
                        ?>
                        </select>
                    </div>
                </div>
                <div class="item_fg clearfix">
                    <div class="left width_b" >
                        <div class="text_fg">看房时间：</div>
                        <?php
                        foreach($config['read_time'] as $key =>$val)
                        {
                            echo '<i class="label display_htype_yes';
                            if($house_detail['read_time'] == $key )
                            {
                               echo ' labelOn ';
                            }
							echo '"><input type="radio" class="input_radio"';
								if($house_detail['read_time'] == $key ){  echo ' checked ';}
                            echo ' name="read_time" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>
                </div>
                <div class="item_fg clearfix hide js_s_h_info js_s_SP_info" <?php if($house_detail['sell_type'] == 3){echo 'style="display:block"';}?>><!--商铺-->
                    <div class="left">
                        <div class="text_fg">类型：</div>
                        <?php
                        foreach($config['shop_type'] as $key =>$val)
                        {
                            echo '<i class="label display_htype_yes';
                            if($house_detail['shop_type'] > 0 && $house_detail['shop_type'] == $key )
                            {
                               echo ' labelOn ';
                            }
                            echo '"><input type="radio" class="input_radio"';
                            if($house_detail['shop_type'] > 0 && $house_detail['shop_type'] == $key )
                            {
							   echo ' checked ';
                            }
                            echo ' name="shop_type" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>
                </div>
                <div class="item_fg clearfix hide js_s_h_info js_s_SP_info" <?php if($house_detail['sell_type'] == 3){echo 'style="display:block"';}?>><!--商铺-->
                    <div class="left check_box">
                        <div class="text_fg">目标业态：</div>
                        <?php
                        foreach($config['shop_trade'] as $key =>$val)
                        {
                            echo '<b class="label';
                            if(in_array($key,$house_detail['shop_trade_arr']))
                            {
								echo ' labelOn ';

                            }
							echo '"><input type="checkbox"  class="js_checkbox input_checkbox"';
							if(in_array($key,$house_detail['shop_trade_arr'])){echo ' checked ';}
                            echo ' name="shop_trade[]" class="js_checkbox" value="'.$key.'"> '.$val.'</b>';
                        }
                        ?>
                    </div>
                </div>
                <div class="item_fg clearfix hide js_s_h_info js_s_XZL_info" <?php if($house_detail['sell_type'] == 4){echo 'style="display:block"';}?>><!--写字楼-->

                 <div class="left width_b  ">
                        <div class="text_fg"> 是否可分割：</div>
                        <i class="label <?php if($house_detail['division'] == 1 ){echo "labelOn";}?> display_htype_yes">
                            <input type="radio"
						class="input_radio" value="1" <?php if($house_detail['division'] == 1){echo "checked";}?> name="division2">
                            是</i>

						<i class="label <?php if($house_detail['division'] == 2 && $house_detail['id']>0 ){echo "labelOn";}?> display_htype_yes">
                            <input type="radio"
					class="input_radio" value="2" <?php if($house_detail['division'] == 2 && $house_detail['id']>0){echo "checked";}?>  name="division2">
                            否</i>
                    </div>
                    <div class="left">
                        <div class="text_fg">写字楼级别：</div>
                        <?php
                        foreach($config['office_trade'] as $key =>$val)
                        {
                            echo '<i class="label display_htype_yes';
                            if($house_detail['office_trade'] == $key )
                            {
                               echo ' labelOn ';

                            }
								echo '"><input type="radio" class="input_radio"';
								if($house_detail['office_trade'] == $key ){  echo ' checked ';}
                            echo ' name="office_trade" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>
                </div>
                <div class="item_fg clearfix hide" <?php if($house_detail['sell_type'] == 3 || $house_detail['sell_type'] == 4 || $house_detail['sell_type'] == 2){echo 'style="display:block"';}?>>
                    <div class="left width_b hide js_s_h_info js_s_SP_info" <?php if($house_detail['sell_type'] == 3){echo 'style="display:block"';}?>><!--商铺-->
                        <div class="text_fg"> 是否可分割：</div>
                       <i class="label <?php if($house_detail['division'] == 1 ){echo "labelOn";}?> display_htype_yes">
                            <input type="radio" class="input_radio" value="1" <?php if($house_detail['division'] == 1){echo "checked";}?> name="division">
                            是</i>
                       <i class="label <?php if($house_detail['division'] == 2 && $house_detail['id']>0 ){echo "labelOn";}?> display_htype_yes">
                            <input type="radio" class="input_radio" value="2" <?php if($house_detail['division'] == 2 && $house_detail['id']>0){echo "checked";}?> name="division">
                            否</i>
                    </div>
                    <div class="left width_b hide js_s_h_info js_s_XZL_info" <?php if($house_detail['sell_type'] == 4){echo 'style="display:block"';}?>><!--写字楼-->
                        <div class="text_fg"> 类型：</div>
                        <?php
                        foreach($config['office_type'] as $key =>$val)
                        {
							echo '<i class="label display_htype_yes';
                            if($house_detail['office_type'] == $key )
                            {
                               echo ' labelOn ';
                            }
							echo '"><input type="radio" class="input_radio"';
								if($house_detail['office_type'] == $key ){  echo ' checked ';}
                            echo ' name="office_type" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>
                    <div class="left width_b js_s_h_info hide js_s_BS_info" <?php if($house_detail['sell_type'] == 2){echo 'style="display:block"';}?>> <!--别墅-->
                        <div class="text_fg">类型：</div>
                        <?php
                        foreach($config['villa_type'] as $key =>$val)
                        {
                             echo '<i class="label display_htype_yes';
                            if($house_detail['villa_type'] == $key )
                            {
                               echo ' labelOn ';

                            }
							echo '"><input type="radio" class="input_radio"';
								if($house_detail['villa_type'] == $key ){  echo ' checked ';}
                            echo ' name="villa_type" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>
                </div>
                <div class="item_fg clearfix hide js_s_h_info js_s_BS_info" <?php if($house_detail['sell_type'] == 2){echo 'style="display:block"';}?>>
                    <div class="left width_b"><!--别墅-->
                        <div class="text_fg">厅结构：</div>
                        <?php
                        foreach($config['hall_struct'] as $key =>$val)
                        {

                             echo '<i class="label display_htype_yes';
                            if($house_detail['hall_struct'] == $key )
                            {
                               echo ' labelOn ';
                            }
							echo '"><input type="radio" class="input_radio"';
								if($house_detail['hall_struct'] == $key ){  echo ' checked ';}
                            echo ' name="hall_struct" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>
                    <div class="left"><!--别墅-->
                        <div class="text_fg"> 地下面积：</div>
                        <input class="input_text w60" name="floor_area" value="<?php if($house_detail['floor_area']>0){ echo strip_end_0($house_detail['floor_area']);} ?>" type="text">
                        <span class="y_fg y_fg_p_l_5">平方米</span>
                        <div class="label">&nbsp;</div>
                        <?php
                        foreach($config['light_type'] as $key =>$val)
                        {
                             echo '<i class="label display_htype_yes';
                            if($house_detail['light_type'] == $key )
                            {
                               echo ' labelOn ';
                            }
							echo '"><input type="radio" class="input_radio"';
								if($house_detail['light_type'] == $key ){  echo ' checked ';}
                            echo ' name="light_type" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>
                </div>
                <div class="item_fg hide js_s_h_info js_s_BS_info clearfix" <?php if($house_detail['sell_type'] == 2){echo 'style="display:block"';}?>>
                    <div class="left width_b"><!--别墅-->
                        <div class="text_fg"> 花园面积：</div>
                        <input class="input_text w60" name="garden_area"  value="<?php if($house_detail['garden_area']>0){ echo strip_end_0($house_detail['garden_area']);} ?>" type="text">
                        <span class="y_fg y_fg_p_l_5">平方米</span> </div>
                    <div class="left"><!--别墅-->
                        <div class="text_fg"> 车位数量：</div>
                        <input class="input_text w60" name="park_num"  value="<?php if($house_detail['park_num']>0){ echo $house_detail['park_num'];}?>" type="text">
                        <span class="y_fg y_fg_p_l_5">个</span> </div>
                </div>

                 <?php if($house_detail['sell_type'] == 1 || $house_detail['sell_type'] == 2 || $house_detail['sell_type'] == 3 || $house_detail['sell_type'] == 4){ ?>
                <div class="item_fg clearfix js_s_ZZ_info js_s_BS_info js_s_XZL_info js_s_SP_info">
                    <div class="left width_b">
                        <div class="text_fg">车库面积：</div>
                        <input class="input_text w60" name="garage_area"  value="<?php if($house_detail['garage_area']>0){ echo strip_end_0($house_detail['garage_area']);} ?>" type="text">
                        <span class="y_fg y_fg_p_l_5">平方米</span>
                    </div>
                    <div class="left">
                        <div class="text_fg"> 阁楼面积：</div>
                        <input class="input_text w60" name="loft_area"  value="<?php if($house_detail['loft_area']>0){ echo strip_end_0($house_detail['loft_area']);} ?>" type="text">
                        <span class="y_fg y_fg_p_l_5">平方米</span>
                    </div>
                </div>
                 <?php } ?>

                <div class="item_fg clearfix" <?php if($house_detail['sell_type'] == 1 || $house_detail['sell_type'] == 2){echo 'style="display:block"';}else{echo 'style="display:none"';}?>>
                    <div class="left width_b">
                        <div class="text_fg"> 产权：</div>
                        <?php
                        foreach($config['property'] as $key =>$val)
                        {
                            echo '<i class="label ';
                            if($house_detail['property'] == $key )
                            {
                               echo ' labelOn ';
                            }
							echo '"><input type="radio" class="input_radio"';
								if($house_detail['property'] == $key ){  echo ' checked ';}
                            echo ' name="property" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>

                </div>
                <div class="item_fg clearfix">
                    <div class="left width_b" <?php if($house_detail['sell_type'] == 1 || $house_detail['sell_type'] == 2){echo 'style="display:block"';}else{echo 'style="display:none"';}?>>
                        <div class="text_fg">现状：</div>
                        <?php
                        foreach($config['current'] as $key =>$val)
                        {
                            echo '<i class="label display_htype_yes';
                            if($house_detail['current'] == $key )
                            {
                               echo ' labelOn ';
                            }
							echo '"><input type="radio" class="input_radio"';
								if($house_detail['current'] == $key ){  echo ' checked ';}
                            echo ' name="current" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>
                    <div class="left">
                        <div class="text_fg"> 信息来源：</div>
                        <?php
                        foreach($config['infofrom'] as $key =>$val)
                        {
                             echo '<i class="label display_htype_yes';
                            if($house_detail['infofrom'] == $key )
                            {
                               echo ' labelOn ';
                            }
							echo '"><input type="radio" class="input_radio"';
								if($house_detail['infofrom'] == $key ){  echo ' checked ';}
                            echo ' name="infofrom" value="'.$key.'"> '.$val.'</i>';
                        }
                        ?>
                    </div>
                </div>
                  <div class="item_fg clearfix">
				<div class="width_b left">
                    <div class="left" width="19%">
                        <div class="text_fg"> 房屋设施：</div>

									<div class="text_fg clear"><b style="float:right;" class="label checkbox_all" srrc="js_check_all01">
										<input type="checkbox" class="js_checkbox input_checkbox"> 全选 </b>
									</div>
									</div>
                    <div class="check_all check_box" id="js_check_all01">
                        <?php
                        foreach($config['equipment'] as $key =>$val)
                        {
                            echo '<b class="label';
                            if(in_array($key,$house_detail['equipment_arr']))
                            {
                                echo ' labelOn ';
                            }
							echo '"><input type="checkbox"  class="js_checkbox input_checkbox"';
							if(in_array($key,$house_detail['equipment_arr'])){echo ' checked ';}
                            echo ' name="equipment[]" class="js_checkbox" value="'.$key.'"> '.$val.'</b>';
                        }
                        ?>
                    </div>
                </div>
               <div class="width_b left">
                   <div class="left">
                       <div class="text_fg"> 周边环境：</div>
						<div class="text_fg clear"><b style="float:right;" class="label checkbox_all" srrc="js_check_all02">
							<input type="checkbox" class="js_checkbox input_checkbox"> 全选 </b>
						</div>
						</div>
                    <div class="check_all check_box" id="js_check_all02">
                        <?php
                        foreach($config['setting'] as $key =>$val)
                        {
                            echo '<b class="label';
                            if(in_array($key,$house_detail['setting_arr']))
                            {	echo ' labelOn ';
                            }
							echo '"><input type="checkbox"  class="js_checkbox input_checkbox"';
							if(in_array($key,$house_detail['setting_arr'])){echo ' checked ';}
                            echo ' name="setting[]" class="js_checkbox" value="'.$key.'"> '.$val.'</b>';
                        }
                        ?>
                    </div>
                </div>
				</div>
                <div class="item_fg clearfix" <?php if($house_detail['sell_type'] == 3 || $house_detail['sell_type'] == 4){echo 'style="display:block"';}else{echo 'style="display:none"';}?>>
                    <label class="label label_none">
                    <span class="text_fg">物业费：</span>
                    <div class="y_fg">
                        <input class="input_text w55" name="strata_fee" value="<?php if($house_detail['strata_fee']>0){ echo strip_end_0($house_detail['strata_fee']);}?>" type="text">
                    </div>
                    </label>
                    元/平方米·月
                    <input type="hidden" name="costs_type" value="1"/>
                </div>
				<!--
				<div class="item_fg clearfix">
                    <label class="label label_h_auto"> <span class="text_fg">描述：</span> <span class="y_fg">
                        <textarea class="textarea" name="bewrite1"><?php echo $house_detail['bewrite'];?></textarea>
                        </span> </label>
                </div>
				-->
                <div class="item_fg clearfix">
                    <label class="label label_h_auto"> <span class="text_fg">备注：</span> <span class="y_fg">
                        <textarea class="textarea" name="remark"><?php echo $house_detail['remark'];?></textarea>
                        </span> </label>
                </div>

            </div>
        </div>
        <!-- 描述信息 -->
        <div class="forms_details_fg forms_details_fg_bg clearfix">
            <div class="clearfix">
                <h3 class="h3">发布信息</h3>
                <span class="js_s_h_btn s_h_btn">收起<span class="iconfont">&#xe60a;</span></span>
            </div>
            <div class="js_s_h_info_house">
                <div class="item_fg fy_describe">
                    <div class ="js_fields"><b class="red">*</b>房源标题：<input type="text" class="fybt_search" name="title" id="title" value="<?php echo $house_detail['title'];?>"  onkeyup="textCounter()"><div class="errorBox clear"></div>
                    <script>
                    function textCounter(){
                        var text_uid=$("#title").val();
                        var text_num=30-text_uid.length;
                        var more=text_uid.length-30;
                        if(text_uid.length<=30){
                            $('#house_title_num').html('您还可以输入'+text_num+'个字');
                        }
                        if(text_uid.length>30){
                            $('#house_title_num').html('<span style="color:red;">您已经超出了'+more+'个字</span>');
                        }
                    }
					</script>
                    <span class="span1"><span class="span1" id="house_title_num">您还可输入30个字</span></span><a href="javascript:void(0)" class="btn-lv" id="title_template_button"><span>模板</span></a> </div>
                </div>
                <div class="item_fg clearfix eidter"><?php if(empty($tmps)){ ?><p class="d_c left" id="no_tmps_message">详细描述： 您还没有设置过模版哦！</p><?php } ?>

                        <a href="javascript:void(0)" class="mobanBtn btnw66 btn-add-tmp" id="btn-add-tmp">新建模板</a>
                        <?php if(!empty($tmps) && isset($tmps)) {
                            foreach($tmps as $k=>$v) {
                                ?>
                                <a href="javascript:void(0)" class="mobanBtn_N mobanBtn_N_<?php echo $v['id'];?>" data-id="<?php echo $v['id'];?>">
									<?php echo $v['template_name'];?>
                                </a>
                            <?php } }?>
                        <a href="javascript:void(0)" class="mobanBtn btnw66" id="btn-manage-tmp" onClick="openWin('gl_moban')" style="<?php echo (is_int($temp_num) && $temp_num>0)?'display:block;':'display:none;';?>">管理模板</a>
                        <a href="javascript:void(0)" class="fl btn-lan" id="content_template_button"><span>描述模板</span></a>
                </div>
                <textarea name="bewrite" id="bewrite" cols="0" rows="0" style="margin-top:5px; width:835px; height:155px; visibility:hidden;"><?php echo $house_detail['bewrite'];?></textarea>
            </div>
            <div class="clearfix">
               <div class="left" style="coloe:#666; line-height:37px; margin-right:5px;">标签：</div>
               <div class="check_all check_box sell_tag" style="width:764px; border:none; background:none;">
                <?php
                foreach($config['sell_tag'] as $key =>$val)
                {
                    echo '<b class="label';
                    if(in_array($key,$house_detail['sell_tag_arr']))
                    {	echo ' labelOn ';
                    }
                    echo '"><input type="checkbox"  class="js_checkbox input_checkbox"';
                    if(in_array($key,$house_detail['sell_tag_arr'])){echo ' checked ';}
                    echo ' name="sell_tag[]" class="js_checkbox" value="'.$key.'"> '.$val.'</b>';
                }
                ?>
               </div>
            </div>
            <br>
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
	file_size_limit : "5 MB",
	file_types : "*.jpg;*.png",
	file_types_description : "JPG Images",
	file_upload_limit : "0",
	file_queue_limit : "5",

	custom_settings : {
		upload_target : "jsPicPreviewBoxM2",
		upload_limit  : 10,
		upload_nail	  : "thumbnails2",
		upload_infotype : 2	},

	// Event Handler Settings - these functions as defined in Handlers.js
	//  The handlers are not part of SWFUpload but are part of my website and control how
	//  my website reacts to the SWFUpload events.
	swfupload_loaded_handler : swfUploadLoaded,
	file_queue_error_handler : fileQueueError,
	file_dialog_start_handler : fileDialogStart,
	file_dialog_complete_handler : fileDialogComplete,
	upload_progress_handler : uploadProgress,
	upload_error_handler : uploadError,
	upload_success_handler : uploadSuccessNew,
	upload_complete_handler : uploadComplete,


	// Button Settings
	button_image_url : "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn02.png",
	button_placeholder_id : "spanButtonPlaceholder2",
	button_width: 130,
	button_height: 100,
	button_cursor: SWFUpload.CURSOR.HAND,
    button_text:"",
    flash_url : "/swfupload.swf"
});

//标签个数限制
    $('.sell_tag b').live('click',function(){
        var sell_tag_num = $('.sell_tag').find('.labelOn').size();
        if(sell_tag_num > 3){
            $(this).find(".js_checkbox").prop("checked",false);
			$(this).removeClass("labelOn");
        }
    });
});
</script>
                    <div id="jsPicPreviewBoxM2" style="display:none" ></div>
                    <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails2">
                        <?php
                            if($picinfo1)
                            {
                               $check_inner = 1;
                               foreach($picinfo1 as $key => $val)
                                {
                                   if($check_inner == 1){
                                       echo '<div class="add_item_pic add_item_pic0"><div class="pic"><img height="100" width="130" src="'.$val['url'].'"><input class="hidden_1" type="hidden" value="'.$val['url'].'" name="p_filename2[]"><input class="hidden_2" type="hidden" value="'.$val['id'].'" name="p_fileids2[]"></div><div class="fun"><a href="javascript:void(0);" class="label_pic" onClick="prevOrNextFun(this)">设为首图</a><a class="del_pic" href="javascript:void(0);" onClick="fun_hide_p(this);swfu2.setButtonDisabled(false);">删除</a><a class="del_left" href="javascript:void(0);"  onClick="prevOrNextFun(this)">左移</a><a class="del_right" href="javascript:void(0);" onClick="prevOrNextFun(this)">右移</a> <p class="fun-bg">背景</p></div><span class="first-img"></span></div>

                                    <!--<div class="add_item_pic">
                                             <div class="pic">
                                                 <img height="100" width="130" src="'.$val['url'].'">
                                             </div>
                                             <input type="hidden" value="'.$val['url'].'" name="p_filename2[]">
                                             <input type="hidden" value="'.$val['id'].'" name="p_fileids2[]">
                                             <div class="fun">  
                                                 <label class="label_pic">
                                                 <input type="radio" value="'.$val['url'].'" ';
                                                 if($val['url'] == $house_detail['pic'])
                                                 {
                                                     echo "checked";
                                                 }
                                                 echo ' name="add_pic">
                                                 设为封面
                                                 </label>
                                                 <a class="del_pic" href="javascript:void(0)" id="2" onclick="fun_hide_p(this)">删除</a> 
                                             </div>
                                         </div>-->

                                        ';
                                   }else{
                                       echo '<div class="add_item_pic"><div class="pic"><img height="100" width="130" src="'.$val['url'].'"><input class="hidden_1" type="hidden" value="'.$val['url'].'" name="p_filename2[]"><input class="hidden_2" type="hidden" value="'.$val['id'].'" name="p_fileids2[]"></div><div class="fun"><a href="javascript:void(0);" class="label_pic" onClick="prevOrNextFun(this)">设为首图</a><a class="del_pic" href="javascript:void(0);" onClick="fun_hide_p(this);swfu2.setButtonDisabled(false);">删除</a><a class="del_left" href="javascript:void(0);"  onClick="prevOrNextFun(this)">左移</a><a class="del_right" href="javascript:void(0);" onClick="prevOrNextFun(this)">右移</a> <p class="fun-bg">背景</p></div><span class="first-img"></span></div>

                                    <!--<div class="add_item_pic">
                                             <div class="pic">
                                                 <img height="100" width="130" src="'.$val['url'].'">
                                             </div>
                                             <input type="hidden" value="'.$val['url'].'" name="p_filename2[]">
                                             <input type="hidden" value="'.$val['id'].'" name="p_fileids2[]">
                                             <div class="fun">  
                                                 <label class="label_pic">
                                                 <input type="radio" value="'.$val['url'].'" ';
                                                 if($val['url'] == $house_detail['pic'])
                                                 {
                                                     echo "checked";
                                                 }
                                                 echo ' name="add_pic">
                                                 设为封面
                                                 </label>
                                                 <a class="del_pic" href="javascript:void(0)" id="2" onclick="fun_hide_p(this)">删除</a> 
                                             </div>
                                         </div>-->

                                        ';
                                   }
                                   $check_inner++;
                                }
                            }
                        ?>
                    </div>
                </div>
                <div class="add_pic_house_title">户型图<span class="t">至多上传3张户型图</span></div>
                <div class="add_pic_house_box add_pic_house_box2 clearfix">
                    <div class="add_item">
                        <span id="spanButtonPlaceholder1"></span>
                    </div>
<script type="text/javascript">
var swfu1;
$(function() {
swfu1 = new SWFUpload({
    file_post_name: "file",
    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
	file_size_limit : "5 MB",
	file_types : "*.jpg;*.png",
	file_types_description : "JPG Images",
	file_upload_limit : "0",
	file_queue_limit : "5",

	custom_settings : {
		upload_target : "jsPicPreviewBoxM1",
		upload_limit  : 3,
		upload_nail	  : "thumbnails1",
		upload_infotype : 1
    },
	swfupload_loaded_handler : swfUploadLoaded,
	file_queue_error_handler : fileQueueError,
	file_dialog_start_handler : fileDialogStart,
	file_dialog_complete_handler : fileDialogComplete,
	upload_progress_handler : uploadProgress,
	upload_error_handler : uploadError,
	upload_success_handler : uploadSuccessNew,
	upload_complete_handler : uploadComplete,

	button_image_url : "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn02.png",
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
                    <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails1">
                        <?php
                            if($picinfo2)
                            {
                                $check_huxing = 1;
                               foreach($picinfo2 as $key => $val)
                                {
                                   if($check_huxing == 1){
                                       echo '<div class="add_item_pic add_item_pic0">
                                             <div class="pic">
                                                 <img height="100" width="130" src="'.$val['url'].'">
												 <input class="hidden_1" type="hidden" value="'.$val['url'].'" name="p_filename1[]">
												 <input class="hidden_2"type="hidden" value="'.$val['id'].'" name="p_fileids1[]">
                                             </div><div class="fun"><a href="javascript:void(0);" class="label_pic" onClick="prevOrNextFun(this)">设为首图</a><a class="del_pic" href="javascript:void(0);" onClick="fun_hide_p(this);swfu2.setButtonDisabled(false);">删除</a><a class="del_left" href="javascript:void(0);"  onClick="prevOrNextFun(this)">左移</a><a class="del_right" href="javascript:void(0);" onClick="prevOrNextFun(this)">右移</a> <p class="fun-bg">背景</p></div><span class="first-img"></span></div>';
                                   }else{
                                       echo '<div class="add_item_pic">
                                             <div class="pic">
                                                 <img height="100" width="130" src="'.$val['url'].'">
												 <input class="hidden_1" type="hidden" value="'.$val['url'].'" name="p_filename1[]">
												 <input class="hidden_2" type="hidden" value="'.$val['id'].'" name="p_fileids1[]">
                                             </div><div class="fun"><a href="javascript:void(0);" class="label_pic" onClick="prevOrNextFun(this)">设为首图</a><a class="del_pic" href="javascript:void(0);" onClick="fun_hide_p(this);swfu2.setButtonDisabled(false);">删除</a><a class="del_left" href="javascript:void(0);"  onClick="prevOrNextFun(this)">左移</a><a class="del_right" href="javascript:void(0);" onClick="prevOrNextFun(this)">右移</a> <p class="fun-bg">背景</p></div><span class="first-img"></span></div>';
                                   }
                                   $check_huxing++;
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <script charset='utf-8'  src='<?php echo MLS_SOURCE_URL;?>/common/js/kindeditor-4.1.10/kindeditor-min.js'></script>
        <script charset='utf-8'  src='<?php echo MLS_SOURCE_URL;?>/common/js/kindeditor-4.1.10/lang/zh_CN.js'></script>
        <script>
            //页面编辑器
            var editor;
            KindEditor.ready(function(K) {
                editor = K.create('#bewrite', {
                    width: '820px',
                    height: '350px',
                    resizeType: 0,
                    allowPreviewEmoticons: false,
                    allowImageUpload: false,
                    items: ['fontname', 'fontsize', '|', 'forecolor',
                        'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|',
                        'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                        'insertunorderedlist', '|', 'wordpaste', '|', 'image'],
                    afterBlur: function() {
                        this.sync();
                    }
                });
            });
            $(function(){
				$(".ke-edit-iframe").show();
                //新建模板判断模板个数
                $(".btn-add-tmp").live('click',function(){
                    $.post("/sell/judge_tmp_num",{ },function(data){
                        if(data.status == 1){
                            $(".span_msg").html("模板数已达上限（10个）");
                            $(".img_msg").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png");
                            openWin('js_pop_msg');
                        } else {
                            $(".iframePop").attr("src","/sell/house_temp");
                            setTimeout(function () {
								openWin("new_moban");
							}, 500);
                        }
                    },'json');
                });
                //点击模板名的操作
                $(".mobanBtn_N").live("click",function(){
					$(".mobanBtn_N").removeClass("mobanBtn_N_on");
					$(this).addClass("mobanBtn_N_on");
                    var id = $(this).attr("data-id");
                    $.post("/sell/search_temp/",{id:id},function(data){
                        editor.html(data.remark);
                        editor.sync();
                    },"json");
                });
            });
        </script>

     <div style="height:61px;"></div>

</div>
		<div class="forms_details_fg forms_details_fg_btn hide" id="js_forms_details_fg">
			<div class="bg">&nbsp;</div>
			<iframe class="iframe_bg"></iframe>
			<?php if($app_id){?>
			<input type='hidden' value ='3' id = 'add_num'>
			<?php }else{?>
			<input type='hidden' value ='1' id = 'add_num'>
			<?php } ?>
            <input type='hidden' value ='modify' id = 'action'>
			<input type='hidden' value = "<?php echo $house_detail['status']?>" name = 'current_status'>
			<button type="submit" class="submit" id="js_forms_submit">保存修改</button>
			<div class="forms" style="position:absolute; top:20px; left:50%; margin-left:35px;">  <!-- b class="label labelOn"><input type="checkbox" class="js_checkbox input_checkbox" checked="checked">同步至淘房网</b -->
			</div>
		</div>
  </form>
<div id="js_pop_add_new_block" class="pop_box_g" style="width:350px; height:360px;overflow:hidden;">

    <div class="hd">
        <div class="title">新建楼盘</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
        <div class="">
            <div style="display:block;" class="inner inner-xj">
                <table class="table" >
                    <tbody>
						<tr>
							<td class="w70 t_l"><font class="red">*</font>楼盘名称：</td>
                            <td><input id="js_cmt_name" type="text" class="input_text" name="cmt_name">
                            </td>
						</tr>
						<tr>
							<td class="w70 t_l"><font class="red">*</font>区属：</td>
                            <td>
                                <select id="district" name="add_dist_id" class="select">
                                    <option value="">请选择</option>
                                    <?php foreach ($district as $k => $v) { ?>
                                        <option value="<?php echo $v['id'] ?>"><?php echo $v['district'] ?></option>
                                    <?php } ?>
                                </select>
                            </td>
						</tr>
						<tr>
                            <td class="w70 t_l"><font class="red">*</font>板块：</td>
                            <td>
                                <select id="street" name="add_streetid" class="select">
                                    <option value="">请选择</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="w70 t_l"><font class="red">*</font>地址：</td>
                            <td>
							<input id="com_address" type="text" class="address" name="com_address">
							</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab_pop_bd add_new_block_bd clearfix"> <a href="javascript:void(0);" class="btn-lv1 btn-left" style="margin-left:98px;" id='add_cmt_submit'>新建楼盘</a><a href="javascript:void(0);" class="btn-hui1 JS_Close" style="float:left;">取消</a> </div>
    </div>
</div>

<!-- 管理模板弹窗  -->
<div class="pop_box_g" id="ms_moban" style="display:none; width:700px; height:460px;">
    <div class="hd">
        <div class="title">选择描述模版</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="miaoshu_pop_mb clearfix">
        <ul id="content_template_list">
		</ul>
    </div>
</div>

<!-- 模板提示弹窗-->
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text"><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/s_ico.png" style="margin-right:10px;"><span id='dialog_do_warnig_tip'></span></p>
            </div>
        </div>
    </div>
</div>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
           <a href="/sell/lists" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
				<p class="text" ><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/r_ico.png" id="image_id" style="margin-right:10px;"><span id='dialog_do_itp'></span></p>
				<?php if($app_id){?>
				<a id = 'dialog_share' class=""  href = "/appoint_center/app_sell">返回预约列表>></a>
				<?php }else{ ?>
				<a id = 'dialog_share' class=""  href = "/sell/lists">返回房源列表>></a>
				<?php } ?>

            </div>
        </div>
    </div>
</div>

<!--标题模板提示框-->
<div id="js_house_title_template_success" class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp_house_title_template'></p>
            </div>
        </div>
    </div>
</div>

    <!-- 模板确认修改状态弹窗-->
    <div id="js_pop_warning_change_status" class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;状态改为非有效，该房源将从平台下架。<br>确定修改吗？</p>
                    <button type="button" class="btn-lv1 btn-left JS_Close" id="btn_confirm_change_status">确定</button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 模板确认删除弹窗-->
    <div id="js_pop_warning" class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <input type="hidden" name="hid_del_step_id" value="">
                    <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;确定要删除此数据吗？<br/>删除后不可恢复。</p>
                    <button type="button" class="btn-lv1 btn-left JS_Close" id="btn_confirm_del">确定</button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 模板管理消息弹窗 -->
    <div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg" style="display:none;">
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop">
                <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="javascript:void(0);"></a>
            </div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text"><img class="img_msg" style="margin-right:10px;" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                        <span id="dialog_do_itp" class="span_msg"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

<!--新建模板iframe弹窗-->
<div id="new_moban" class="iframePopBox" style="width: 640px; height:375px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)">&#xe60c;</a>  <!--date-iframe="1"-->
    <iframe frameborder="0" scrolling="no" width="640" height="375" class="iframePop" name="iframePop" id="iframePop" src=""></iframe>
</div>

<!--修改模板iframe弹窗-->
<div id="upd_moban" class="iframePopBox" style="width:  640px; height:375px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)">&#xe60c;</a>  <!--date-iframe="1"-->
    <iframe frameborder="0" scrolling="no" width="640" height="375" class="upd_iframePop" name="upd_iframePop" id="upd_iframePop" src=""></iframe>
</div>

<!--新建楼盘操作结果弹出提示框-->
<div id="js_community_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0)" title="关闭" class="JS_Close iconfont" onclick='$("#GTipsCoverjs_pop_add_new_block").remove();'></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_community_do_itp'></p>
				 <button type="button" class="btn-lv1 btn-mid JS_Close" onclick='$("#GTipsCoverjs_pop_add_new_block").remove();'>确定</button>
            </div>
        </div>
    </div>
</div>

<!-- 管理模板弹窗  -->
<div class="pop_box_g" id="gl_moban" style="display: none;">
    <div class="hd">
        <div class="title">管理模板</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="guanli_pop_mb clearfix">
        <?php if(!empty($tmps) && isset($tmps)) {
            foreach($tmps as $k=>$v) {
                ?>
                <dl  class="glmb_num glmb_dl glmb_dl_<?php echo $v['id'];?>">
                    <dt><span><?php echo $v['template_name'];?></span></dt>
                    <dd class="glmb_dd clearfix">
                        <a href="javascript:void(0);" class="left modify_tmp modify_tmp_<?php echo $v['id'];?>" data-id="<?php echo $v['id'];?>">修改
                        </a><!--
                    --><a href="javascript:void(0);" class="right remove_tmp remove_tmp_<?php echo $v['id'];?>" data-id="<?php echo $v['id'];?>">删除
                        </a>
                    </dd>
                </dl>
            <?php } }?>
        <?php if($temp_num<10) { ?>
            <a class="xjmb_dd btn-add-tmp manage_add_tmp" href="javascript:void(0);">新建模板</a>
        <?php } ?>
    </div>
</div>
<style>
    #upd_moban, #js_pop_warning,#js_pop_msg,#new_moban{z-index: 99999999 !important;}
</style>
<script>
    $(function(){
        $(".modify_tmp").live("click",function(){
            var id = $(this).attr("data-id");

            $("#upd_iframePop").attr("src","/sell/house_modify_temp/" + id);
            setTimeout(function () {
				openWin("upd_moban");
			}, 200);
        });


        //点击删除按钮时
        $(".remove_tmp").live("click",function(){
            var id = $(this).attr('data-id');
            $("input[name='hid_del_step_id']").val(id);
            openWin('js_pop_warning');
        });
        $("#btn_confirm_del").live("click",function(){
            var id = $("input[name='hid_del_step_id']").val();
            $.post("/sell/del_tmp",{id:id},function(data){
                if(data.status == 1) {
                    $(".span_msg").html("删除成功！");
					$(".img_msg").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png");
                    openWin('js_pop_msg');
					var gl_num = ($(".glmb_num").length);
					if(gl_num == 10){
						$(".guanli_pop_mb").append('<a class="xjmb_dd btn-add-tmp manage_add_tmp" href="javascript:void(0);"  style="display:none">新建模板</a>');
					};
                    //点击关闭消息弹窗的
                        $(".msg_iconfont_close").click(function(){
							$('.manage_add_tmp').show();
                        //移除原页中的标签
                        $(".mobanBtn_N_"+data.template_id).remove();
                        //移除管理弹窗中的标签
                        $(".glmb_dl_"+data.template_id).remove();
                    });
                } else {
                    //设置操作结果提示窗的一些值
                    $(".span_msg").html("删除失败！");
                    $(".img_msg").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png");
                    openWin("js_pop_msg");
                }

            },'json');
        });
    });
</script>

<div class="pop_box_g" id="zj_moban" style="display: none;overflow: hidden;">
    <div class="hd mt16">
        <div class="title">标题模板选择</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="warrant-step-mb">
       <p>我希望标题包含以下内容：</p>
	   <div class="select_info clearfix">
			<div class="forms fo" id="title_template_select">
				<b class="label labelOn checkbox_all" srrc="js_check_all03"><input class="js_checkbox input_checkbox" type="checkbox" name="title_category" value="all">全部</b><span class="check_box2" id="js_check_all03">
				<b class="label labelOn"><input class="js_checkbox input_checkbox"  type="checkbox" name="title_category" value="name">楼盘</b>
				<b class="label labelOn"><input class="js_checkbox input_checkbox"  type="checkbox" name="title_category" value="fitment">装修</b>
				<b class="label labelOn"><input class="js_checkbox input_checkbox"  type="checkbox" name="title_category" value="room">户型</b>
				<b class="label labelOn"><input class="js_checkbox input_checkbox"  type="checkbox" name="title_category" value="price">价格</b>
				<b class="label labelOn"><input class="js_checkbox input_checkbox"  type="checkbox" name="title_category" value="area">建筑面积</b></span>
			</div>
	   </div>
	   <div class="data_list">
           <div style="height: 409px; overflow-x: hidden; overflow-y: scroll;">
                <ul class="btmb_u clearfix" id="btmb_select_list">
                </ul>
           </div>
	   </div>
    </div>
</div>

<script type="text/javascript">
$(function(){
    $('#district').change(function(){
        var districtID = $(this).val();
        $.ajax({
            type: 'get',
            url : '/sell/find_street_bydis/'+districtID,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg.result=='no result'){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
                    }
                }
                $('#street').empty();
                $('#street').append(str);
            }
        });
    });

    $('#add_cmt_submit').click(function(){
        var cmt_name = $('input[name="cmt_name"]').val();//楼盘名称
        var dist_id = $('#district').val();//区属
        var districtname = $('#district option:selected').text();
        var streetid = $('#street').val();//板块
        var streetname = $('#street option:selected').text();
        var address = $('input[name="com_address"]').val();//地址
        var build_date = $('#build_date').val();//建筑年代
        var property_year = $('#property_year').val();//产权年限
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
        $('input[name="build_type"]:checked').each(function(){
            build_type.push($(this).val());
        });
        //图片
        var p_filename3 = [];
        $('input[name="p_filename3[]"]').each(function(){
            p_filename3.push($(this).val());
        });
        //是否为封面
        var surface = $('input[name="add_pic"]:checked').val();
        var addData = {
            'cmt_name':cmt_name,
            'dist_id':dist_id,
            'districtname':districtname,
            'streetid':streetid,
            'streetname':streetname,
            'address':address,
            'build_date':build_date,
            'property_year':property_year,
            'buildarea':buildarea,
            'coverarea':coverarea,
            'property_company':property_company,
            'developers':developers,
            'parking':parking,
            'green_rate':green_rate,
            'plot_ratio':plot_ratio,
            'property_fee':property_fee,
            'build_num':build_num,
            'total_room':total_room,
            'floor_instruction':floor_instruction,
            'introduction':introduction,
            'facilities':facilities,
            'build_type':build_type,
            'location_pic':p_filename3,
            'surface':surface
        };
        $.ajax({
            type: 'get',
            url : '/sell/add_community',
            dataType:'json',
            data: addData,
            error:function(){
                alert("系统错误");
                return false;
            },
            success: function(data){
                if(data.status == 1){
                    $('#dialog_community_do_itp').html('新建成功');
                    openWin('js_community_do_success');
                    $("#block_id").val(data.id);//楼盘
                    $("#block_name").val(data.cmt_name);
                    $("#district_id").val(data.dist_id);//区属
                    $("#select_q").val(data.districtname);
                    $("#street_id").val(data.streetid);//板块
                    $("#select_b").val(data.streetname);
                    $("#address").val(data.address);//地址

                    $("#js_pop_add_new_block").css('display','none');
                }else{
                    if(data.status==100){
                        $(".js_cmt_name_error").remove();
                        for(var i=0;i<data.list.length;i++){
                            $("#"+data.list[i].name).parent().append('<p class="js_cmt_name_error" style="color: red; clear: both; line-height: 16px;">'+data.list[i].msg+'</p>');
                        }
                    }else if(data.status==600){
                        $(".js_cmt_name_error").remove();
                        for(var i=0;i<data.list.length;i++){
                            $("#"+data.list[i].name).parent().append('<p class="js_cmt_name_error" style="color: red; clear: both; line-height: 16px;">'+data.list[i].msg+'</p>');
                        }
                    }
                }
            }
        });
    });
});

$("#a_ratio").blur(function(){
			var a_ratio=$("#a_ratio").val();
			var b_ratio=100-a_ratio;
			$("#b_ratio").val("");
			if($.isNumeric(a_ratio)){
				$("#b_ratio").val(b_ratio);
			}
		})
$(function(){//发布页底部按钮 悬浮
	$(".forms_scroll ").scroll(function(){
		var navH = jQuery("#title").offset().top + 10;
		if($(this).scrollTop()> navH)
		{
			$("#js_forms_details_fg").show();
		}
		else
		{
			$("#js_forms_details_fg").hide()
		}
	})
	$("#js_forms_submit").hover(function(){
		$(this).addClass("submit_hover")
	},function(){
		$(this).removeClass("submit_hover")
	});
	innerHeightForm();
	$(window).resize(function(){
		innerHeightForm();
	});
})

function innerHeightForm()
{//窗口改变大小的时候  计算高度

    if($("#js_inner").length>0)
    {
        var _height = document.documentElement.clientHeight;
        var _height_tab = $("#js_tab_box").outerHeight(true);
		$("#js_inner").css("height", _height - _height_tab );
    }
};

function addBlock(){
    //新增楼盘弹框中内容设置为空
    $('#js_pop_add_new_block input').val('');
    $('#js_pop_add_new_block select').each(function(){
        $(this).children('option').first().attr('selected','selected');
    });
    $('#js_pop_add_new_block textarea').val('');
    $('#js_pop_add_new_block input[type="checkbox"]').attr('checked',false);
    $(".js_cmt_name_error").remove();
    openWin('js_pop_add_new_block');
}

$(function(){
    //楼栋、单元、门牌三级联动
    $('select[name="dong"]').live('change',function(){
        var dong_id = $('select[name="dong"] option:selected').attr('_id');
        $.ajax({
                url: "/community/get_unit_by_dong/",
                type: "GET",
                data: {dong_id: dong_id},
                success:function(data){
                    var unit_data_obj = eval('(' + data + ')');
                    var unit_select_str = '';
                    unit_select_str += '<option value="">请选择</option>';
                    for(var i = 0;i < unit_data_obj.length;i++){
                        unit_select_str += '<option value="'+unit_data_obj[i].name+'" _id="'+unit_data_obj[i].id+'">'+unit_data_obj[i].name+'</option>';
                    }
                    $('select[name="unit"]').html(unit_select_str);
                }
        });
    });

    $('select[name="unit"]').live('change',function(){
        var unit_id = $('select[name="unit"] option:selected').attr('_id');
        $.ajax({
                url: "/community/get_door_by_unit/",
                type: "GET",
                data: {unit_id: unit_id},
                success:function(data){
                    var door_data_obj = eval('(' + data + ')');
                    var door_select_str = '';
                    door_select_str += '<option value="">请选择</option>';
                    for(var i = 0;i < door_data_obj.length;i++){
                        door_select_str += '<option value="'+door_data_obj[i].name+'" _id="'+door_data_obj[i].id+'">'+door_data_obj[i].name+'</option>';
                    }
                    $('select[name="door"]').html(door_select_str);
                }
        });
    });

    <?php if($house_detail['isshare']=='0'){ ?>
        var reward_type = $('input[name="reward_type"]:checked').val();
    <?php }else{ ?>
        var reward_type = $('input[name="reward_type"]').val();
    <?php }?>
    if(2==reward_type){
        $('#shangjin').attr('name','shangjin');
    }else if(1==reward_type){
        $('#shangjin').removeAttr('name');
    }

    $('.shangjin_tab').live('click',function(){
        var reward_type = $('input[name="reward_type"]:checked').val();
        if(2==reward_type){
            $('#shangjin').attr('name','shangjin');
        }else if(1==reward_type){
            $('#shangjin').removeAttr('name');
        }
    });

    textCounter();

    $('#telno1,#telno2,#telno3').live('blur',function(){
        var telno = $(this).val();
        $.ajax({
                url: "/sell/check_blacklist/",
                type: "GET",
                data: {telno: telno},
                success:function(data){
                    if('success'==data){
                        $("#dialog_do_warnig_tip").html('该电话号码是黑名单');
                        openWin('js_pop_do_warning');
                    }
                }
        });
    });

	//赏金显示与否
	$(".shangjin-show").on('click',function(){
		$(".shangjin-div").show();
	});
	$(".shangjin-hide").on('click',function(){
		$(".shangjin-div").hide();
	});

    $("#js_gs_01").on('click',function(){
        $("#js_show_yj").show();
        $("#js_show_friend").show();
    })
    $("#js_gs_02").on('click',function(){
		$("input[name='isshare_friend'][value='0']").attr('checked',true);
		$("input[name='isshare_friend'][value='1']").attr('checked',false);
		$("#js_gs_01_friend").removeClass('labelOn');
		$("#js_gs_02_friend").addClass('labelOn');
        $("#js_show_yj").hide();
        $("#js_show_friend").hide();
		//alert($("input[name='isshare_friend']:checked").val());
    })

	$("#js_gs_01_friend").on('click',function(){
		$("input[name='reward_type'][value='1']").attr('checked',true);
		$("input[name='reward_type'][value='2']").attr('checked',false);
		$("#remind_div2").removeClass('labelOn');
		$("#remind_div").addClass('labelOn');
		$("#js_show_yj").hide();
	})
	$("#js_gs_02_friend").on('click',function(){
		$("#js_show_yj").show();
	})

})
</script>

