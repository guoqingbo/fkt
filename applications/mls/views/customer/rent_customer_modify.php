<!--页面部分-->
<script>
    window.parent.addNavClass(3);
</script>
<body>
<div class="tab_box" id="js_tab_box">
<?php if($modify_auth == 1){?>
<a href="##" class="link link_on"><span class="iconfont">&#xe604;</span>修改求租</a>
<a href="/rent_customer/manage" class="btn-lv" style="float:right; margin-right:10px;"><span>&lt;&lt;返回客源列表</span></a>
<?php }?>
</div>
<form action=''  method='' id = 'jsUpForm'>
<div class="forms forms_scroll h91" id="js_inner">
        <div class="h_15">&nbsp;</div>
        <input type="hidden" name="truename" value="<?php echo $customer_info['truename']; ?>" />
        <input type="hidden" name="sex" value="<?php echo $customer_info['sex']; ?>" />
        <input type="hidden" name="idno" value="<?php echo $customer_info['idno']; ?>" />
        <input type="hidden" name="age_group" value="<?php echo $customer_info['age_group']; ?>" />
        <input type="hidden" name="telno[]" value="<?php echo $customer_info['telno1']; ?>" />
        <input type="hidden" name="telno[]" value="<?php echo $customer_info['telno2']; ?>" />
        <input type="hidden" name="telno[]" value="<?php echo $customer_info['telno3']; ?>" />
        <input type="hidden" name="job_type" value="<?php echo $customer_info['job_type']; ?>" />
        <input type="hidden" name="address" value="<?php echo $customer_info['address']; ?>" />
        <input type="hidden" name="user_level" value="<?php echo $customer_info['user_level']; ?>" />

        <div class="forms_details_fg forms_details_fg_bg clearfix">
            <div class="clearfix">
						<h3 class="h3">客源需求</h3>
					</div>
            <div class="item_fg clearfix">
                <div class="left width_b js_fields" >
                    <div class="text_fg"><b class="red">*</b>状态：</div>
                    <?php if(is_array($conf_customer['status']) && !empty($conf_customer['status'])) { ?>
                    <?php foreach($conf_customer['status'] as $key => $value){ ?>
                    <i class="label display_htype_yes <?php if($customer_info['status'] == $key){echo "labelOn";}?>">
                        <input type="radio" class="input_radio" class="input_radio" name="status" <?php if($customer_info['status'] == $key){echo 'checked';}?> value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                    <div class="errorBox"></div>
                </div>
                <div class="left js_fields">
                    <div class="text_fg"><b class="red">*</b>客源性质：</div>
                    <?php if(is_array($conf_customer['public_type']) && !empty($conf_customer['public_type'])) { ?>
                    <?php
                    $public_type_order = 1;
                    foreach($conf_customer['public_type'] as $key => $value)
                    {
                    ?>
                    <i class="label display_htype_yes <?php if($customer_info['public_type'] == $key){echo "labelOn";}?>">
                        <input type="radio" class="input_radio" name="public_type" <?php if($customer_info['public_type'] == $key){echo 'checked';}else if($public_type_order == 1){?> checked <?php } ?> value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php
                        $public_type_order ++;
                    }?>
                    <?php } ?>
                    <input type="hidden" id="old_nature" value="<?php echo $customer_info['public_type'];?>"/>
                    <div class="errorBox"></div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b js_fields">
                    <div class="text_fg"><b class="red">*</b>物业类型：</div>
					<div class="text_fg" style="text-align:left">
                    <?php if(is_array($conf_customer['property_type']) && !empty($conf_customer['property_type'][$customer_info['property_type']]))
                    {
                        echo $conf_customer['property_type'][$customer_info['property_type']];
                        echo "<input type='hidden' name='property_type' value=".$customer_info['property_type'].">";
                    } ?>
                    </div>
                </div>
                <div class="left" id="huxing" <?php if(!empty($customer_info['property_type']) && $customer_info['property_type'] != 1 && $customer_info['property_type'] != 2) {?>style="display:none"<?php } ?>>
                    <div class="label "> <span class="text_fg"><b class="red">*</b>户型：</span>
                        <div class="y_fg" >
                            <div class="js_fields left">
                                <input type="text" id="room_min" name='room_min' class="input_text input_text_r w60" value="<?php echo $customer_info['room_min'];?>">
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">一</span>
                            <div class="js_fields left">
                                <input type="text" name="room_max" id="room_max"  class="input_text input_text_r w60 " value="<?php echo $customer_info['room_max'];?>">
                                <div class="errorBox clear"></div>
                            </div>
                        </div>
                        <span class="y_fg y_fg_p_l_5">室</span> </div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b">
                    <div class="text_fg"><b class="red">*</b>租金：</div>
                    <div class="y_fg" >
                        <div class="js_fields left">
                            <input type="text" class="input_text input_text_r w60" name="price_min" id="price_min" value="<?php echo strip_end_0($customer_info['price_min']);?>">
                            <div class="errorBox clear"></div>
                        </div>
                        <span class="y_fg y_fg_p5">一</span>
                        <div class="js_fields left">
                            <input type="text"  class="input_text input_text_r w60 " name="price_max" id="price_max"  value="<?php echo strip_end_0($customer_info['price_max']);?>">
                            <div class="errorBox clear"></div>
                        </div>
                    </div>
                    <?php if($customer_info['property_type'] != 3 && $customer_info['property_type'] != 4){?>
                    <span class="y_fg y_fg_p_l_5 js_show_pirce" id="js_show_pirce_1">元/月</span>
                    <?php }else{ ?>
                    <select class="select  js_select_pirce" style="margin-left:10px;" name="price_danwei">
                        <?php foreach($conf_customer['rent_price_unit'] as $key => $value){ ?>
                        <option value="<?php echo $key;?>" <?php if($key == $customer_info['price_danwei']){  echo 'selected';  }?> ><?php echo $value;?></option>
                        <?php }?>
                    </select>
                    <?php }?>
                </div>
                <div class="left">
                    <div class="label "> <span class="text_fg"><b class="red">*</b>面积：</span>
                        <div class="y_fg" >
                            <div class="js_fields left">
                                <input type="text" name="area_min" class="input_text input_text_r w60" id="mianji01" value="<?php echo strip_end_0($customer_info['area_min']);?>">
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">一</span>
                            <div class="js_fields left ">
                                <input type="text" name="area_max" class="input_text input_text_r w60 " value="<?php echo strip_end_0($customer_info['area_max']);?>">
                                <div class="errorBox clear"></div>
                            </div>
                        </div>
                        <span class="y_fg y_fg_p_l_5">平方米</span> </div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b">
                    <div class="text_fg">楼层：</div>
                    <div class="y_fg" >
                        <div class="js_fields left">
                            <input type="text" class="input_text w60" name='floor_min' id='floor_min' value="<?php echo $customer_info['floor_min'] > 0 ? $customer_info['floor_min'] : '';?>">
                            <div class="errorBox clear"></div>
                        </div>
                        <span class="y_fg y_fg_p5">一</span>
                        <div class="js_fields left">
                            <input type="text"  class="input_text w60" name='floor_max'  id='floor_max' value="<?php echo $customer_info['floor_max'] > 0 ? $customer_info['floor_max'] : '';?>">
                            <div class="errorBox clear"></div>
                        </div>
                    </div>
                    <span class="y_fg y_fg_p_l_5">层</span> </div>
                    <div class="left">
                    <div class="text_fg">租赁期限：</div>
                    <div class="left js_fields" id="QS01">
                                <select name="lease" id="lease" class="select" >
                                    <option value="">不限</option>
                                    <?php
                                        foreach($conf_customer['lease'] as $key => $value){
                                            if($customer_info['lease'] == $key){
                                                $selected = 'selected = "selected"';
                                            }else{
                                                $selected = ' ';
                                            }
                                            echo '<option  value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    ?>
                                </select>
                                <div class="errorBox clear"></div>
                    </div>
                    </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left ">
                    <div class="label "> <span class="text_fg"><b class="red">*</b>意向区属：</span>
                        <div class="y_fg">
                            <div class="left js_fields" id="QS01">
                                <select name="dist_id[]" id="dist_id" class="select" onchange ="get_street_by_id(this , 'street_id1')">
                                    <option selected="" value="0">请选择区属</option>
                                    <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                                    <?php foreach($district_arr as $key => $value){?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['dist_id1'] == $value['id']){echo 'selected';}?>><?php echo $value['district'];?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">&nbsp;</span>
                            <div class="left js_fields">
                                <select name="street_id[]" class="select" id = 'street_id1'>
                                    <option selected="" value="0">请选择板块</option>
                                    <?php if(is_array($select_info1['street_info']) && !empty($select_info1['street_info'])){ ?>
                                    <?php foreach($select_info1['street_info'] as $key =>$value){ ?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['street_id1'] == $value['id']){ echo 'selected';  } ?>>
                                    <?php echo $value['streetname'];?>
                                    </option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <a href="javascript:void(0)" class="iconfont addTel" id="addQS01">&#xe608;</a> </div>
                        <span class="y_fg y_fg_p5">&nbsp;</span>
                        <div class="y_fg <?php if(empty($customer_info['dist_id2'])){ echo 'hide'; }?>" id="QS02">
                            <div class="left js_fields">
                                <select name="dist_id[]" id="dist_id02" class="select" onchange ="get_street_by_id(this , 'street_id2')">
                                    <option selected="" value="0">请选择区属</option>
                                    <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                                    <?php foreach($district_arr as $key => $value){?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['dist_id2'] == $value['id']){echo 'selected';}?>><?php echo $value['district'];?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">&nbsp;</span>
                            <div class="left js_fields">
                                <select name="street_id[]" class="select" id ='street_id2'>
                                    <option selected="" value="0">请选择板块</option>
                                    <?php if(is_array($select_info2['street_info']) && !empty($select_info2['street_info'])){ ?>
                                    <?php foreach($select_info2['street_info'] as $key =>$value){ ?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['street_id2'] == $value['id']){ echo 'selected';  } ?>>
                                    <?php echo $value['streetname'];?>
                                    </option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <a href="javascript:void(0)" class="iconfont delTel delTel02" id="delQS02">&#xe60c;</a>
                        </div>
                        <span class="y_fg y_fg_p5">&nbsp;</span>
                        <div class="y_fg <?php if(empty($customer_info['dist_id3'])){echo 'hide';}?>" id="QS03">
                            <div class="left js_fields">
                                <select name="dist_id[]" id="dist_id03" class="select" onchange ="get_street_by_id(this , 'street_id3')">
                                    <option  value="0">请选择区属</option>
                                    <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                                    <?php foreach($district_arr as $key => $value){?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['dist_id3'] == $value['id']){echo 'selected';}?>><?php echo $value['district'];?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">&nbsp;</span>
                            <div class="left js_fields">
                                <select name="street_id[]" class="select" id ='street_id3'>
                                    <option  value="0">请选择板块</option>
                                    <?php if(is_array($select_info3['street_info']) && !empty($select_info3['street_info'])){ ?>
                                    <?php foreach($select_info3['street_info'] as $key =>$value){ ?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['street_id3'] == $value['id']){ echo 'selected';  } ?>>
                                    <?php echo $value['streetname'];?>
                                    </option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <a href="javascript:void(0)" class="iconfont delTel delTel02"  id="delQS03">&#xe60c;</a> </div>
                    </div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left"> <span class="text_fg">意向楼盘：</span>
                    <div class="y_fg">
                        <input class="js_block input_text w200" type="text" name="cmt_name[]" id="block01" <?php if(!empty($customer_info['cmt_id1'])) { ?> change_tag ="1" <?php }else{?> change_tag ="0" <?php }?>  value="<?php echo $customer_info['cmt_name1'];?>"  placeholder="输入拼音或汉字筛选">
                        <input type="hidden" name="cmt_id[]" class='cmt_id' id = 'cmt_id01' value="<?php echo $customer_info['cmt_id1'];?>">
                        <a href="javascript:void(0)" class="iconfont addTel" id="addBlock01" >&#xe608;</a>
                    </div>
                    <div class="y_fg <?php if(empty($customer_info['cmt_id2'])) { echo 'hide';}?>">
                        <input class="js_block input_text w200" type="text" name="cmt_name[]" id="block02" <?php if(!empty($customer_info['cmt_id2'])) { ?> change_tag ="1" <?php }else{?> change_tag ="0" <?php }?> value="<?php echo $customer_info['cmt_name2'];?>"  placeholder="输入拼音或汉字筛选">
                        <input type="hidden" name="cmt_id[]" id = 'cmt_id02' class='cmt_id' value="<?php echo $customer_info['cmt_id2'];?>">
                        <a href="javascript:void(0)" class="iconfont delTel" id="delBlock02">&#xe60c;</a>
                    </div>
                    <div class="y_fg <?php if(empty($customer_info['cmt_id3'])) { echo 'hide';}?>">
                        <input class="js_block input_text w200" type="text" name="cmt_name[]" id="block03" <?php if(!empty($customer_info['cmt_id3'])) { ?> change_tag ="1" <?php }else{ ?> change_tag ="0" <?php }?> value="<?php echo $customer_info['cmt_name3'];?>"  placeholder="输入拼音或汉字筛选">
                        <input type="hidden" name="cmt_id[]" class='cmt_id' id = 'cmt_id03' value="<?php echo $customer_info['cmt_id3'];?>" >
                        <a href="javascript:void(0)" class="iconfont delTel"  id="delBlock03">&#xe60c;</a>
                    </div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b">
                    <div class="text_fg">朝向：</div>
                <div class="left js_fields">
                    <select class="select" name="forward" id="forward">
                        <option selected="" value="">请选择</option>
                        <?php if(is_array($conf_customer['forward']) && !empty($conf_customer['forward'])) { ?>
                        <?php foreach($conf_customer['forward'] as $key => $value){ ?>
                            <option value="<?php echo $key;?>" <?php if($customer_info['forward'] == $key){echo 'selected="selected"';}?>><?php echo $value;?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                </div>
                <div class="left">
                    <div class="text_fg">装修：</div>
                    <div class="left js_fields">
                    <?php if(is_array($conf_customer['fitment']) && !empty($conf_customer['fitment'])) { ?>
                    <?php foreach($conf_customer['fitment'] as $key => $value){ ?>
                    <i class="label display_htype_yes <?php if($customer_info['fitment'] == $key){echo "labelOn";}?>">
                        <input type="radio" class="input_radio" name="fitment" value='<?php echo $key;?>' <?php if($customer_info['fitment'] == $key){echo 'checked';}?>> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                    </div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left  ">
                    <div class="text_fg">目的：</div>
                    <?php if(is_array($conf_customer['intent']) && !empty($conf_customer['intent'])) { ?>
                    <?php foreach($conf_customer['intent'] as $key => $value){ ?>
                    <i class="label display_htype_yes <?php if($customer_info['intent'] == $key){echo "labelOn";}?>">
                        <input type="radio" class="input_radio" name="intent" value='<?php echo $key;?>' <?php if($customer_info['intent'] == $key){echo 'checked';}?>> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b">
                    <div class="text_fg">房龄：</div>
                    <div class="left js_fields">
                    <?php if(is_array($conf_customer['house_age']) && !empty($conf_customer['house_age'])) { ?>
                    <?php foreach($conf_customer['house_age'] as $key => $value){ ?>
                    <i class="label display_htype_yes <?php if($customer_info['house_age'] == $key){echo "labelOn";}?>">
                        <input type="radio" class="input_radio" name="house_age" value='<?php echo $key;?>' <?php if($customer_info['house_age'] == $key){echo 'checked';}?>> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                    </div>
                </div>
                <div class="left width_b ">
                    <div class="text_fg">付款方式：</div>
                    <select class="select" name="payment" id="forward">
                        <option selected="" value="">请选择</option>
                        <?php if(is_array($conf_customer['rent_payment']) && !empty($conf_customer['rent_payment'])) { ?>
                        <?php foreach($conf_customer['rent_payment'] as $key => $value){ ?>
                            <option value="<?php echo $key;?>" <?php if($customer_info['payment'] == $key){echo 'selected="selected"';}?>><?php echo $value;?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left  width_b">
                    <div class="text_fg">期限：</div>
                    <?php if(is_array($conf_customer['deadline']) && !empty($conf_customer['deadline'])) { ?>
                    <?php foreach($conf_customer['deadline'] as $key => $value){ ?>
                    <i class="label display_htype_yes <?php if($customer_info['deadline'] == $key){echo "labelOn";}?>">
                        <input type="radio" class="input_radio" name="deadline" value='<?php echo $key;?>' <?php if($customer_info['deadline'] == $key){echo 'checked';}?>> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                </div>
                <div class="left  ">
                    <div class="text_fg">信息来源：</div>
                    <?php if(is_array($conf_customer['infofrom']) && !empty($conf_customer['infofrom'])) { ?>
                    <?php foreach($conf_customer['infofrom'] as $key => $value){ ?>
                    <i class="label display_htype_yes <?php if($customer_info['infofrom'] == $key){echo "labelOn";}?>">
                        <input type="radio" name="infofrom" class="input_radio" value='<?php echo $key;?>' <?php if($customer_info['infofrom'] == $key){echo 'checked';}?>> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left  ">
                    <div class="text_fg">备注：</div>
                    <textarea class="textarea" name='remark'><?php echo $customer_info['remark'];?></textarea>
                </div>
            </div>
        </div>
        <div class="forms_details_fg forms_details_fg_bg clearfix">
            <div class="item_fg reset_P clearfix">
                <div class="text_fg"><b class="red">*</b>是否合作：</div>
                <div class="left">
                    <?php if('1'==$open_cooperate){?>
                        <?php if('1'==$check_cooperate){?>
                            <?php if($customer_info['is_share'] == 2){?>
                                <span style="color:red;">合作审核中</span>
                            <?php }else{?>
                            <i class="label mod_p <?php if($customer_info['is_share'] != "0"){echo "labelOn";}?>" id = "js_gs_01">是
                                <input type="radio" <?php if($customer_info['is_share'] != "0"){echo "checked";}?> class="input_radio" name="is_share" value="2">
                            </i>
                            <i class="label mod_p <?php if(empty($customer_info['is_share'])){echo "labelOn";}?>"  id = "js_gs_02">否
                                <input type="radio" <?php if(empty($customer_info['is_share'])){echo "checked";}?> class="input_radio" value="0" name="is_share">
                            </i>
                            <?php }?>
                        <?php }else{?>
                            <i class="label mod_p <?php if($customer_info['is_share'] != "0"){echo "labelOn";}?>" id = "js_gs_01">是
                                <input type="radio" <?php if($customer_info['is_share'] != "0"){echo "checked";}?> class="input_radio" name="is_share" value="1">
                            </i>
                            <i class="label mod_p <?php if($customer_info['is_share']!=1){echo "labelOn";}?>"  id = "js_gs_02">否
                                <input type="radio" <?php if($customer_info['is_share']!=1){echo "checked";}?> class="input_radio" value="<?php echo ($customer_info['is_share']=='2')?'2':'0'; ?>" name="is_share">
                            </i>
                        <?php }?>
                    <?php }else{?>
                                <input type="hidden" name="is_share" value="<?php echo $customer_info['is_share']; ?>"/>
                    <i class="label mod_p <?php if($customer_info['is_share'] != "0"){echo "label-no2";}else{echo "label-no";}?>" id = "js_gs_01">是
                    </i>
                    <i class="label mod_p <?php if(empty($customer_info['is_share'])){echo "label-no2";}else{echo "label-no";}?>"  id = "js_gs_02">否
                    </i>
                    <?php }?>
                </div>
                <span class="info_bc left">
                    <?php if('1'==$open_cooperate){?>
                        <?php if('1'==$check_cooperate){?>
                        需通过合作审核后，进入合作中心
                        <?php }else{?>
                        合作客源将在合作中心展示，帮助您高效合作，快速成交！
                        <?php }?>
                    <?php }else{?>
                        店长已关闭合作功能
                    <?php } ?>
                </span>
            </div>
        </div>
        <div style="height:61px;"></div>
        </div>
        <!--操作结果弹出提示框-->
        <div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
            <div class="hd">
                <div class="title">提示</div>
                <div class="close_pop">
                    <a href="javascript:void(0)" onclick="jump_to_url('/rent_customer/manage/');return false;" title="关闭" class="JS_Close iconfont"></a>
                </div>
            </div>
            <div class="mod">
                <div class="inform_inner">
                    <div class="up_inner">
                          <p class="text" ><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/r_ico.png" style="margin-right:10px;"><span  id='dialog_do_itp'></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!--操作结果弹出警告-->
        <div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
            <div class="hd">
                <div class="title">提示</div>
                <div class="close_pop">
                    <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
                </div>
            </div>
            <div class="mod">
                <div class="inform_inner">
                    <div class="up_inner">
                        <p class="text" ><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/s_ico.png" style="margin-right:10px;"><span id='dialog_do_warnig_tip'></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="forms_details_fg forms_details_fg_btn" id="js_forms_details_fg">
            <div class="bg">&nbsp;</div>
            <iframe class="iframe_bg"></iframe>
            <button type="submit" class="submit" id="js_forms_submit">更新客源</button>
        </div>
        <input type="hidden" name="customer_id" id ="customer_id" value="<?php echo $customer_info['id'];?>">
        <input type="hidden" name="customer_broker_id" value="<?php echo $customer_info['broker_id'];?>">
        <input type="hidden" name="do_key" value="<?php echo md5($customer_info['id'].$customer_info['broker_id'].'_365mls');?>">
        <input type = 'hidden' name = 'publish_type' id = 'publish_type' value = 'rent_customer_modify'>
        <input type = 'hidden' id = 'group_id' value = '<?php echo $group_id;?>'>
    </form>
<script>
$(function(){//发布页底部按钮 悬浮

    $("#js_forms_submit").hover(function(){
            $(this).addClass("submit_hover")
    },function(){
            $(this).removeClass("submit_hover")
    });
    innerHeightForm();
    $(window).resize(function(){
            innerHeightForm();
    });

	function innerHeightForm()
	{
		//窗口改变大小的时候  计算高度
		if($("#js_inner").length>0)
		{
			var _height = document.documentElement.clientHeight;
			var _height_tab = $("#js_tab_box").outerHeight(true);
			$("#js_inner").css("height", _height - _height_tab );
		}
	};

})
</script>
