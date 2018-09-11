<!--页面部分-->
<body >
<!--描述：导航栏开始 -->
<div class="tab_box" id="js_tab_box">
<?php echo $user_menu;?>
</div>
<!--描述：导航栏结束-->
<!--描述：分类导航栏开始-->
<div id="js_search_box" class="shop_tab_title" style="margin-bottom:0;">
 <?php echo $user_func_menu;?>
</div>
<form action = '<?php echo MLS_URL;?>/customer_collect/buy_customer_collects' method = 'post' name = 'search_form' id ='search_form'>
<div class="search_box clearfix" id="js_search_box_02">
    <?php if($cond_show == 'hide'){ ?>
    <a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this)" data-h="0">展开<span class="iconfont">&#xe609;</span></a>
    <?php }else{ ?>
    <a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this)" data-h="1">收起<span class="iconfont">&#xe60a;</span></a>
    <?php } ?>
    <div class="fg_box">
        <p class="fg fg_tex">区属：</p>
        <div class="fg">
            <select class="select" name='dist_id' onchange ="get_street_by_id(this , 'street_id')">
                <option selected="" value="0">请选择区属</option>
                <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                <?php foreach($district_arr as $key => $value){ ?>
                <option value="<?php echo $value['id'];?>" <?php if($post_param['dist_id'] == $value['id']){ echo 'selected';  } ?>>
                <?php echo $value['district'];?>
                </option>
                <?php } ?>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 板块：</p>
        <div class="fg">
            <select class="select"  name='street_id' id="street_id">
                <option value="0">不限</option>
                <?php if(is_array($select_info['street_info']) && !empty($select_info['street_info'])){ ?>
                <?php foreach($select_info['street_info'] as $key =>$value){ ?>
                <option value="<?php echo $value['id'];?>" <?php if($post_param['street_id'] == $value['id']){ echo 'selected';  } ?>>
                <?php echo $value['streetname'];?>
                </option>
                <?php } ?>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 楼盘：</p>
        <div class="fg">
            <input type="text" name='cmt_name' class="input w90" value="<?php echo $post_param['cmt_name'];?>">
            <input type="hidden" name='cmt_id' id='cmt_id' value='<?php echo $post_param['cmt_id'];?>'>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 面积：</p>
        <div class="fg">
            <input type="text" name='area_min' class="input w30" value='<?php echo $post_param['area_min'];?>'>
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" name='area_max' class="input w30" value='<?php echo $post_param['area_max'];?>'>
        </div>
        <p class="fg fg_tex fg_tex03">平米</p>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">总价：</p>
        <div class="fg">
            <input type="text" name='price_min' class="input w30" value='<?php echo $post_param['price_min'];?>'>
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" name='price_max' class="input w30" value='<?php echo $post_param['price_max'];?>'>
        </div>
        <p class="fg fg_tex fg_tex03">万元</p>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">范围：</p>
        <div class="fg">
            <select class="select" name='agenctcode' onchange="get_broker_by_agencyid(this,'broker_id');">
                <option value="0">不限</option>
                <?php foreach($agencys as $k => $v){?>
                <option value="<?php echo $v['agency_id'];?>"><?php echo $v['agency_name'];?></option>
                <?php } ?>
            </select>
        </div>
        <div class="fg fg_tex fg_tex03">
            <select class="select" name='broker_id' id="broker_id">
                <option value="0">不限</option>
            </select>
        </div>
    </div>
    <div class="fg_box hide" <?php if(empty($cond_show)){?> style='display: inline;'<?php }?>>
        <p class="fg fg_tex"> 物业类型：</p>
        <div class="fg">
            <select class="select" name='property_type'>
                <option value="0">不限</option>
                 <?php if(is_array($conf_customer['property_type']) && !empty($conf_customer['property_type'])) { ?>
                    <?php foreach($conf_customer['property_type'] as $key => $value){ ?>
                    <option value='<?php echo $key;?>' <?php if($post_param['property_type'] == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                    <?php } ?>
                    <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box hide" <?php if(empty($cond_show)){?> style='display: inline;'<?php }?>>
        <p class="fg fg_tex"> 户型：</p>
        <div class="fg">
            <select class="select" name='room'>
                <option value='0'>不限</option>
                 <?php if(is_array($conf_customer['room_type']) && !empty($conf_customer['room_type'])) { ?>
                    <?php foreach($conf_customer['room_type'] as $key => $value){ ?>
                    <option value='<?php echo $key;?>' <?php if($post_param['room'] == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                    <?php } ?>
                    <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box hide" <?php if(empty($cond_show)){?> style='display: inline;'<?php }?>>
        <p class="fg fg_tex"> 性质：</p>
        <div class="fg">
            <select class="select" name='public_type'>
                <option value="0">不限</option>
                    <?php if(is_array($conf_customer['public_type']) && !empty($conf_customer['public_type'])) { ?>
                    <?php foreach($conf_customer['public_type'] as $key => $value){ ?>
                    <option value='<?php echo $key;?>' <?php if($post_param['public_type'] == $key){ echo 'selected';  } ?>> <?php echo $value;?></option>
                    <?php } ?>
                    <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box hide" <?php if(empty($cond_show)){?> style='display: inline;'<?php }?>>
        <p class="fg fg_tex"> 状态：</p>
        <div class="fg">
            <select class="select" name='status'>
                <option value="0">不限</option>
                <?php if(is_array($conf_customer['status']) && !empty($conf_customer['status'])) { ?>
                    <?php foreach($conf_customer['status'] as $key => $value){ ?>
                   <option value='<?php echo $key;?>' <?php if($post_param['status'] == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                    <?php } ?>
                    <?php } ?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0);" onclick ="sub_form('search_form');return false;" class="btn" ><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="/customer_collect/buy_customer_collects/" class="reset">重置</a> </div>
    </div>
</div>
<div class="table_all">
    <div class="title" id="js_title">
        <table class="table">
            <tr>
                <td class="c5"><div class="info">物业类型</div></td>
                <td class="c15"><div class="info">意向区属板块</div></td>
                <td class="c15"><div class="info">意向楼盘</div></td>
                <td class="c7"><div class="info">户型(室)</div></td>
                <td class="c8"><div class="info">面积(㎡)</div></td>
                <td class="c8"><div class="info">总价(万)</div></td>
                <td class="c7"><div class="info">经纪人</div></td>
                <!--
                <td class="c5"><div class="info">好评率</div></td>
                <td class="c6"><div class="info">合作成功率</div></td>
                -->
                <td class="c6"><div class="info">发布时间</div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner" id="js_inner">
        <table class="table table_q" id="js_table_box_Sincerity">
            <?php if(is_array($customer_list) && !empty($customer_list)){
            ?>
            <?php foreach ($customer_list as $key =>$value) { ?>
            <tr info_id = "<?php echo $value['c_id'];?>" <?php if($key % 2 == 1){ ?>class="bg" <?php }?> date-url="<?php echo MLS_URL;?>/customer/details/<?php echo $value['id'];?>/1">
                <td class="c5"><div class="info">
                <?php
                    if(isset($conf_customer['property_type'][$value['property_type']]))
                    {
                        echo $conf_customer['property_type'][$value['property_type']];
                    }
                ?>
                </div></td>
                <td class="c15">
                    <div class="info">
                    <?php
                    $district_str = '';
                    if($value['dist_id1'] > 0 && isset($district_arr[$value['dist_id1']]['district']))
                    {
                        $district_str =  $district_arr[$value['dist_id1']]['district'];
                        if($district_str != '' && $value['street_id1'] > 0 && !empty($street_arr[$value['street_id1']]['streetname']))
                        {
                            $district_str .=  '-'.$street_arr[$value['street_id1']]['streetname'];
                        }
                    }

                    if($value['dist_id2'] > 0 && isset($district_arr[$value['dist_id2']]['district']))
                    {
                        $district_str .=  !empty($district_str) ? '，'.$district_arr[$value['dist_id2']]['district'] :
                            $district_arr[$value['dist_id2']]['district'];

                        if( !empty($district_arr[$value['dist_id2']]['district']) &&
                            $value['street_id2'] > 0 && !empty($street_arr[$value['street_id2']]['streetname']))
                        {
                           $district_str .=  '-'.$street_arr[$value['street_id2']]['streetname'];
                        }
                    }

                    if($value['dist_id3'] > 0 && isset($district_arr[$value['dist_id3']]['district']))
                    {
                        $district_str .=  !empty($district_str) ? '，'.$district_arr[$value['dist_id3']]['district'] :
                             $district_arr[$value['dist_id3']]['district'];

                        if(!empty($district_arr[$value['dist_id3']]['district']) &&
                           $value['street_id3'] > 0 && !empty($street_arr[$value['street_id3']]['streetname']))
                        {
                           $district_str .= '-'.$street_arr[$value['street_id3']]['streetname'];
                        }
                    }
                    echo $district_str ;
                    ?>
                    </div>
                </td>
                <td class="c15"><div class="info f14 fblod">
                    <?php
                    if(isset($value['cmt_name1']) && $value['cmt_name1'] != '' )
                    {
                        echo $value['cmt_name1'];
                    }

                    if(isset($value['cmt_name2']) && $value['cmt_name2'] != '' )
                    {
                        echo '，'.$value['cmt_name2'];
                    }

                    if(isset($value['cmt_name3']) && $value['cmt_name3'] != '')
                    {
                        echo '，'.$value['cmt_name3'];
                    }
                    ?>
                </div></td>
                <td class="c7"><div class="info fblod"><?php echo $value['room_min'];?>-<?php echo $value['room_max'];?></div></td>
                <td class="c8"><div class="info"><?php echo strip_end_0($value['area_min']);?>-<?php echo strip_end_0($value['area_max']);?></div></td>
                <td class="c8"><div class="info f13 fblod"><?php echo strip_end_0($value['price_min']);?>-<?php echo strip_end_0($value['price_max']);?></div></td>
                <td class="c7 js_info broker" data-brokerId ="<?php echo $value['broker_id'];?>" data_id="<?php echo $value['id'];?>" type="buy_customer">
                <div class="info">
                <?php
                if(isset($customer_broker_info[$value['broker_id']]['truename']) && $customer_broker_info[$value['broker_id']]['truename'] !='')
                {
                    echo $customer_broker_info[$value['broker_id']]['truename'];
                }
                ?>
                </div>
                </td>
                <!--
                <td class="c5">
                    <div class="info">
                    <?php
                    if( $customer_broker_info[$value['broker_id']]['good_rate'] == '')
                    {
                        $customer_broker_info[$value['broker_id']]['good_rate'] = '--';
                    }
                    echo $customer_broker_info[$value['broker_id']]['good_rate']."%";
                    ?>
                    </div>
                </td>
                <td class="c6">
                    <div class="info">
                    <?php
                    if(!empty($customer_broker_info[$value['broker_id']]['cop_succ_ratio_info']['cop_succ_ratio']) &&
                            $customer_broker_info[$value['broker_id']]['cop_succ_ratio_info']['cop_succ_ratio'] > 0)
                    {
                         echo $customer_broker_info[$value['broker_id']]['cop_succ_ratio_info']['cop_succ_ratio'].'%';
                    }
                    else if($customer_broker_info[$value['broker_id']]['cop_succ_ratio_info']['cop_succ_ratio'] == 0 )
                    {
                        if(intval($customer_broker_info[$value['broker_id']]['cop_succ_ratio_info']['cooperate_num']) > 0)
                        {
                            echo '0%';
                        } else {
                           echo '--%';
                        }
                    }
                    ?>
                    </div>
                </td>
                -->
                <td class="c9">
                    <div class="info"><?php echo date('Y-m-d H:i', $value['set_share_time']); ?></div>
                </td>
                <td class="js_no_click">
                <div class="info_p_r">
                    <?php if($check_coop_reulst[$value['customer_id']] == 1) { ?>
                    <a href="javascript:void(0)" title = "已申请" style="color:#b2b2b2;text-decoration:none;">已申请</a>
                    <?php } else if( $value['broker_id'] != $broker_id && '1'==$open_cooperate){?>
                    <a href="javascript:void(0)" class="hezuo" onclick="cooperate_customer('buy_customer',<?php echo $value['id'];?>);">合作申请</a>
                    <?php } else if( '0'==$open_cooperate ){?>
                    <a href="javascript:void(0)" title = "当前公司未设置合作中心" class="hezuo" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                    <?php } else {?>
                    <a href="javascript:void(0)" title = "自己不能跟自己合作" class="hezuo" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                    <?php } ?>
                    <span style="margin:0 2px;color:#b2b2b2;">|</span><a href="javascript:void(0)" onclick="open_match_customer('customer',2,<?php echo $value['id'];?>)">智能匹配</a><span style="color:#b2b2b2; margin:0 2px;">|</span><a href="javascript:void(0)" class="shcang" onclick="cancle_collect_customer(<?php echo $value['c_id'];?> , 'buy_customer');" id = "collect_<?php echo $value['id'];?>">取消收藏</a>
                </div>
                  </td>

            </tr>
            <?php } ?>
            <?php }else{ ?>
            <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php } ?>
        </table>
    </div>
</div>
<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
  <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
   </div>
</div>
</form>
<!--右键菜单-->
<ul id="openList">
    <input type="hidden" id="right_id" class="js_input">
    <li onclick="openDetails('customer',1);">查看详情</li>
</ul>

<!--合作申请弹框-->
<div id="js_pop_box_cooperation_customer" class="iframePopBox" style=" width:920px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="920" height="540" class='iframePop' src=""></iframe>
</div>

<!--合作申请房源选择弹框-->
<div id="js_pop_box_cooperation" class="iframePopBox" style=" width:520px; height:496px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="520" height="496" class='iframePop' src=""></iframe>
</div>

<!--详情页弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--匹配详情页弹框-->
<div id="js_pop_box_g_match" class="iframePopBox" style=" width:930px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="930" height="540" class='iframePop' src=""></iframe>
</div>

<!--页面处理中弹层-->
<div style="display:none; text-align: center;" id ='docation_loading'>
    <img src ="<?php echo MLS_SOURCE_URL; ?>/common/images/loading_6.gif">
    <p style="font-size: 16px; font-family:'微软雅黑'; line-height: 30px; color: #fff;">正在处理</p>
</div>

<!--举报信息弹框-->
<div id="js_woyaojubao" class="iframePopBox" style=" width:500px; height:360px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="500" height="360" class='iframePop' src=""></iframe>
</div>

<!--评价弹框-->
<div id="js_pop_box_appraise1" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>

<!--经纪人信用弹框-->
<div class="broker-info-wrap" id="broker_info_wrap"></div>
