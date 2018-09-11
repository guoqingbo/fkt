<!--合作信息-->
<div class="pop_box_g pop_box_g02 pop_box_g_big pop_box_g_border_none" id="js_zhineng" style='display: block;width:930px;'>
    <div class="hd">
        <div class="title">智能匹配</div>
        <div class="close_pop"></div>
    </div>
    <form name="search_form" id = 'search_form' method="post" action="<?php echo MLS_URL;?>/rent_customer/match/<?php echo $data_info['id'];?>/<?php echo $is_public;?>">
    <div class="mod">
        <div class="mod_zn_inner">
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="c7">物业类型</th>
                        <th class="c16">意向区属板块</th>
                        <th class="c12">意向楼盘</th>
                        <th class="c9">户型(室)</th>
                        <th class="c9">面积(㎡)</th>
                        <th class="c12">租金</th>
                        <th>经纪人</th>
                        <?php if($is_public) { ?>
                        <th>操作</th>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>
                        <?php
                        if(isset($conf_customer['property_type'][$data_info['property_type']]))
                        {
                            echo $conf_customer['property_type'][$data_info['property_type']];
                        }
                        ?>
                        </td>
                        <td>
                        <?php
                        $district_str = '';
                        if(!empty($data_info['dist_id1']) && isset($district_arr[$data_info['dist_id1']]['district']))
                        {
                            $district_str =  $district_arr[$data_info['dist_id1']]['district'];
                            if($district_str != '' && $data_info['street_id1'] > 0 && !empty($street_arr[$data_info['street_id1']]['streetname']))
                            {
                                $district_str .=  '-'.$street_arr[$data_info['street_id1']]['streetname'];
                            }
                        }

                        if(!empty($data_info['dist_id2']) && isset($district_arr[$data_info['dist_id2']]['district']))
                        {
                            $district_str .=  !empty($district_str) ? '，'.$district_arr[$data_info['dist_id2']]['district'] :
                                $district_arr[$data_info['dist_id2']]['district'];

                            if( !empty($district_arr[$data_info['dist_id2']]['district']) &&
                                $data_info['street_id2'] > 0 && !empty($street_arr[$data_info['street_id2']]['streetname']))
                            {
                               $district_str .=  '-'.$street_arr[$data_info['street_id2']]['streetname'];
                            }
                        }

                        if(!empty($data_info['dist_id3']) && isset($district_arr[$data_info['dist_id3']]['district']))
                        {
                            $district_str .=  !empty($district_str) ? '，'.$district_arr[$data_info['dist_id3']]['district'] :
                                     $district_arr[$data_info['dist_id3']]['district'];

                            if(!empty($district_arr[$data_info['dist_id3']]['district']) &&
                               $data_info['street_id3'] > 0 && !empty($street_arr[$data_info['street_id3']]['streetname']))
                            {
                               $district_str .= '-'.$street_arr[$data_info['street_id3']]['streetname'];
                            }
                        }

                        echo $district_str ;
                        ?>
                        </td>
                        <td>
                        <?php
                        if(isset($data_info['cmt_name1']) && $data_info['cmt_name1'] != '' )
                        {
                            echo $data_info['cmt_name1'];
                        }

                        if(isset($data_info['cmt_name2']) && $data_info['cmt_name2'] != '' )
                        {
                            echo '，'.$data_info['cmt_name2'];
                        }

                        if(isset($data_info['cmt_name3']) && $data_info['cmt_name3'] != '')
                        {
                            echo '，'.$data_info['cmt_name3'];
                        }
                        ?>
                        </td>
                        <td><?php echo $data_info['room_min'];?>-<?php echo $data_info['room_max'];?></td>
                        <td><?php echo strip_end_0($data_info['area_min']);?>-<?php echo strip_end_0($data_info['area_max']);?></td>
                        <td><?php echo $data_info['price_info'];?></td>
                        <td>
                        <?php
                        if(isset($customer_broker_info['truename']) && $customer_broker_info['truename'] !='')
                        {
                            echo $customer_broker_info['truename'];
                        }
                        ?>
                        </td>
                        <?php if($is_public) { ?>
                        <?php if($customer_broker_info['broker_id'] != $broker_id){?>
                        <td><a  href="javascript:void(0);" onclick="parent.cooperate_customer('rent_customer',<?php echo $data_info['id'];?>);">合作申请</a></td>
                        <?php } else { ?>
                        <td><a href="javascript:void(0)" title = "自己不能跟自己合作" style="color:#b2b2b2;text-decoration:none;">合作申请</a></td>
                        <?php }?>
                        <?php }?>
                    </tr>
                </table>
            </div>
            <div class="inner inner02 clearfix match">
                <div class="clearfix">
                <div class="fg_box fg_title">
                    <h3 class="title">匹配条件筛选</h3>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex">时间：</p>
                    <div class="fg">
                        <select class="select" name="match_time">
                            <?php if(is_array($conf_customer['match_time']) && !empty($conf_customer['match_time'])) { ?>
                            <?php foreach($conf_customer['match_time'] as $key => $value){ ?>
                            <option value='<?php echo $key;?>' <?php if($post_param['match_time'] == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="fg_box">
                <p class="fg fg_tex">区属：</p>
                <div class="fg">
                    <select class="select" name='dist_id' onchange ="get_street_by_id(this , 'street_id')">
                        <option selected="" value="0">请选择区属</option>
                        <?php if( is_array($district_select_arr) && !empty($district_select_arr) ){ ?>
                        <?php foreach($district_select_arr as $key => $value){ ?>
                        <option value="<?php echo $value['id'];?>" <?php if( isset($post_param['dist_id']) && $post_param['dist_id'] == $value['id']){ echo 'selected';  } ?>>
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
                        <select style="width:80px;" class="select"  name='street_id' id="street_id">
                            <option value="0">不限</option>
                            <?php if(is_array($select_info['street_info']) && !empty($select_info['street_info'])){ ?>
                            <?php foreach($select_info['street_info'] as $key =>$value){ ?>
                            <option value="<?php echo $value['id'];?>" <?php if( isset($post_param['street_id']) && $post_param['street_id'] == $value['id']){ echo 'selected';  } ?>>
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
                    <input type="text" name='cmt_name' class="input w90" id='block01' value="<?php echo isset($post_param['cmt_name']) ? $post_param['cmt_name']:'';?>">
                    <input type="hidden" name='cmt_id' id='cmt_id' value='<?php echo isset($post_param['cmt_id']) ? $post_param['cmt_id']:'';?>'>
                    </div>
                </div>
                <div class="fg_box">
                    <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="sub_form();return false;"><span class="btn_inner">精准匹配</span></a> </div>
                </div>
                </div>
                <div class="fg_box" style="padding-left:90px;">
                    <p class="fg fg_tex"> 租金：</p>
                    <div class="fg">
                        <input type="text" class="input w30" name="price_min" value="<?php echo strip_end_0($post_param['price_min']);?>">
                    </div>
                    <p class="fg fg_tex fg_tex02">—</p>
                    <div class="fg">
                        <input type="text" class="input w30" name="price_max" value="<?php echo strip_end_0($post_param['price_max']);?>">
                    </div>
                    <p class="fg fg_tex fg_tex03">元/月</p>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex"> 面积：</p>
                    <div class="fg">
                        <input type="text" class="input w30" name="area_min" value="<?php echo strip_end_0($post_param['area_min']);?>">
                    </div>
                    <p class="fg fg_tex fg_tex02">—</p>
                    <div class="fg">
                        <input type="text" class="input w30" name="area_max" value="<?php echo strip_end_0($post_param['area_max']);?>">
                    </div>
                    <p class="fg fg_tex fg_tex03">平米</p>
                </div>

            </div>
            <div class="clearfix pop_fg_fun_box match-pager">
                <div class="get_page">
                    <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
                </div>
                <div id="js_search_box" class="shop_tab_title  scr_clear">
                    <?php if(is_array($conf_customer['match_range']) && !empty($conf_customer['match_range'])) { ?>
                    <?php foreach($conf_customer['match_range'] as $key => $value){ ?>
                    <a href="javascript:void(0);" onclick="$('#match_range').val(<?php echo $key;?>);$('input[name=page]').val(1);$('#search_form').submit();return false;" class="link <?php if(isset($post_param['match_range']) && $post_param['match_range'] == $key){ echo 'link_on';  } ?>">
                        <?php echo $value;?>（<span class="highlight"><?php echo $tab_num[$key];?></span>）<span class="iconfont hide">&#xe607;</span>
                    </a>
                    <?php } ?>
                    <?php } ?>
                    <input type = "hidden" name = "match_range" id="match_range"  value = "<?php echo $post_param['match_range'];?>" >
                </div>
                <input type="hidden" name='property_type' value='<?php echo isset($post_param['property_type']) ? $post_param['property_type'] : 0;?>'>
            </div>
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="c7">物业类型</th>
                        <th class="c6">区属</th>
                        <th class="c6">板块</th>
                        <th class="c11">楼盘</th>
                        <th class="c5">户型</th>
                        <th class="c6">面积(㎡)</th>
                        <th class="c6">报价</th>
                        <th class="c9">门店</th>
                        <th class="c8">经纪人</th>
                        <?php if(!$is_public) { ?>
                        <th>操作</th>
                        <?php }?>
                    </tr>
                    <?php if(is_array($house_list) && !empty($house_list)) {
                        $match_num = 5;
                        if('2'==$post_param['match_range']){
                            $match_num = 6;
                        }
                    ?>
                    <?php foreach ( $house_list as $key => $value) {?>
                    <tr class="data_list bg" date-url="<?php echo MLS_URL;?>/rent/details/<?php echo $value['id'];?>/<?php echo $match_num;?>" controller="rent">
                        <td><?php echo isset($conf_customer['property_type'][$value['sell_type']]) ? $conf_customer['property_type'][$value['sell_type']]: '';?></p>
                        <td><?php echo !empty($district_arr[$value['district_id']]['district']) ? $district_arr[$value['district_id']]['district'] : '';?></td>
                        <td><?php echo $street_arr[$value['street_id']]['streetname'];?></td>
                        <td><?php echo $value['block_name'];?></td>
                        <td><?php echo $value['room'];?>-<?php echo $value['hall'];?>-<?php echo $value['toilet'];?></td>
                        <td><?php echo strip_end_0($value['buildarea']);?></td>
                        <td><?php echo strip_end_0($value['price_info']);?></td>
                        <td>
                        <?php
                        if(!empty($value['broker_id']) &&
                                isset($house_broker_info[$value['broker_id']]['agency_name']) &&
                                $house_broker_info[$value['broker_id']]['agency_name'] !='')
                        {
                        echo $house_broker_info[$value['broker_id']]['agency_name'];
                        }
                        ?>
                        </td>
                        <td>
                        <?php
                        if(!empty($value['broker_id']) && isset($house_broker_info[$value['broker_id']]['truename']) && $house_broker_info[$value['broker_id']]['truename'] !='')
                        {
                        echo $house_broker_info[$value['broker_id']]['truename'];
                        }
                        ?>
                        </td>
                        <?php if(!$is_public) { ?>
                        <td>
                            <?php if($value['broker_id'] != $broker_id){?>
                            <a  href="javascript:void(0);" onclick="parent.cooperate_house('rent',<?php echo $value['id'];?>,<?php echo $value['broker_id'];?>);">合作申请</a>
                            <?php } else { ?>
                            <a href="javascript:void(0)" title = "自己不能跟自己合作" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                            <?php }?>
                        </td>
                        <?php }?>
                    </tr>
                    <?php } ?>
                    <?php } else {?>
                    <tr>
                        <td colspan="16"><span class="no-data-tip">很遗憾，没有匹配的房源！修改匹配条件再试试吧！</span></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    </form>
</div>

<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<script typ="text/javascript">
$(function(){
    $(".data_list").each(function(index, element) {
        var _url = $(this).attr("date-url");
        var type = $(this).attr("controller");
        var id = $(this).attr("_id");
        var ajax_data = {};
        var msg = '';
        $(this).find("td:gt(0)").on("click",function(event){
            if(!$(this).hasClass("js_no_click"))
            {
                if(_url)
                {
                    $("#js_pop_box_g .iframePop").attr("src",_url);
                    openWin('js_pop_box_g');

                    $(this).parent(".table").addClass("tr_hover").find(".checkbox").attr("checked",true);
                }
                event.stopPropagation();
            }
            else{
                $(this).parent(".table").addClass("tr_hover").find(".checkbox").attr("checked",true);
                event.stopPropagation();
            }
        });

        this.oncontextmenu=function (ev){
            if(_url)
            {
                var oEvent=ev||event;
                var oUl=document.getElementById('openList');
                var w = $("body").width();
                var h = document.documentElement.clientHeight;
                var oH = $(oUl).outerHeight(true);
                var oW = $(oUl).outerWidth(true);
                var _id = $(this).find(".checkbox").length?$(this).find(".checkbox").val():$(this).attr("info_id");
                $("#openList").find(".js_input").val(_id);
                oUl.style.display='block';
                w<(oW+oEvent.clientX)?oUl.style.left=w-oW -1+'px':oUl.style.left=oEvent.clientX-1+'px';
                h<(oH+oEvent.clientY)?oUl.style.top=h- oH -1+'px':oUl.style.top=oEvent.clientY-1+'px';
                $(".checkbox,#js_checkbox").attr("checked",false);
                $(this).addClass("tr_hover").siblings().removeClass("tr_hover");
                $(this).find(".checkbox").attr("checked",true);
                return false;
            }
        };
    });
});
</script>
