<div class="pop_box_g pop_box_g02" id="js_zhineng" style="display: block;border:0;width:930px;">
    <div class="hd">
        <div class="title">智能匹配</div>
        <div class="close_pop"></div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="c5">物业类型</th>
                        <th class="c6">区属</th>
                        <th class="c6">板块</th>
                        <th class="c9">楼盘</th>
                        <th class="c6">户型</th>
                        <th class="c6">面积&nbsp;(㎡)</th>
                        <th class="c7">租金</th>
                        <?php if($is_public){?>
                        <th class="c8">委托门店</th>
                        <?php } ?>
                        <th class="c8">经纪人</th>
                        <?php if($is_public){?>
                        <th class="c7">操作</th>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td><?php echo $config['sell_type'][$house_info['sell_type']]; ?></td>
                        <td><?php echo $house_info['district_name']; ?></td>
                        <td><?php echo $house_info['street_name']; ?></td>
                        <td><?php echo $house_info['block_name']; ?></td>
                        <td><?php echo $house_info['room']; ?>-<?php echo $house_info['hall']; ?>-<?php echo $house_info['toilet']; ?></td>
                        <td><?php echo strip_end_0($house_info['buildarea']); ?></td>
                        <td><?php echo $house_info['price_info']; ?></td>
                        <?php if($is_public){?>
                        <td><?php echo $house_info['agency_name']; ?></td>
                        <?php } ?>
                        <td><?php echo $house_info['broker_name']; ?></td>
                        <?php if($is_public){?>
                            <td>
                            <?php if($house_info['broker_id'] == $broker_id){?>
                            <a href="javascript:void(0)" title="自己不能跟自己合作" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                            <?php }else{ ?>
                            <a href="javascript:void(0);" onclick="parent.cooperate_house('rent',<?php echo $house_info['id'];?>,<?php echo $house_info['broker_id'];?>);">合作申请</a>
                            <?php } ?>
                            </td>
                        <?php } ?>
                    </tr>
                </table>
            </div>
            <form method='post' action='/rent/match/<?php echo $house_info['id'];?>/<?php echo $is_public;?>' id='search_form'>
            <input type="hidden" name="searchrange" id="searchrange" value="<?php if($post_param['searchrange'] > 0){echo $post_param['searchrange'];}else{echo 2;}?>">
            <div class="inner inner02 clearfix match">
                <div class="fg_box fg_title">
                    <h3 class="title"> 匹配条件筛选</h3>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex">时间：</p>
                    <div class="fg">
                        <select class="select" name="searchtime">
                            <option value="1" <?php if($post_param['searchtime'] == 1 || empty($post_param['searchtime'])){echo "selected";}?>>一个月内</option>
                            <option value="2" <?php if($post_param['searchtime'] == 2){echo "selected";}?>>一季内</option>
                            <option value="3" <?php if($post_param['searchtime'] == 3){echo "selected";}?>>半年内</option>
                            <option value="4" <?php if($post_param['searchtime'] == 4){echo "selected";}?>>一年内</option>
                        </select>
                    </div>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex"> 物业类型：<?php echo $config['sell_type'][$house_info['sell_type']];?></p>
                    <input type="hidden" name="sell_type" value="<?php echo $house_info['sell_type'];?>">
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex"> 户型：<?php echo $house_info['room']?>室</p>
                    <input type="hidden" name="room" value="<?php echo $house_info['room'];?>">
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex">区属：<?php echo $house_info['district_name'];?></p>
                    <input type="hidden" name="district_id" value="<?php echo $house_info['district_id'];?>">
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex">板块：</p>
                    <div class="fg">
                        <select class="select" name="street_id">
                            <option value='0'>不限</option>
                            <?php

                                    foreach($street as $k => $v)
                                    {
                                        if($v['dist_id'] == $house_info['district_id'])
                                        {
                                            echo "<option value='".$v['id']."'";
                                            if($post_param['street_id']){
                                                if($v['id'] == $post_param['street_id'])
                                                    echo " selected ";
                                            }
                                            echo ">".$v['streetname']."</option>";
                                        }
                                    }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex"> 楼盘：</p>
                    <div class="fg">
                        <input type="text" name="search_block_name" id="search_block_name" value="<?php echo !empty($post_param['search_block_name']) ? $post_param['search_block_name'] : ''; ?>">
                        <input name="search_block_id" type="hidden" id="search_block_id" value="<?php echo !empty($post_param['search_block_id']) ? $post_param['search_block_id'] : '';?>">
                    </div>
                </div>
                <script type="text/javascript">
                    $(function(){
						$.widget( "custom.autocomplete", $.ui.autocomplete, {
							_renderItem: function( ul, item ) {
								if(item.id>0){
									return $( "<li>" )
									.data( "item.autocomplete", item )
									.append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.label+'</span><span class="ui_district">'+item.districtname+'</span><span class="ui_address">'+item.address+'</span></a>')
									.appendTo( ul );
								}else{
									return $( "<li>" )
									.data( "item.autocomplete", item )
									.append('<a class="ui-corner-all" tabindex="-1">'+item.label+'</a>')
									.appendTo( ul );
								}
							}
						});
                        $("#search_block_name").autocomplete({
                            source: function( request, response ) {
                                var term = request.term;
                                $("#search_block_id").val("");
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

                                    //操作
                                    $("#search_block_id").val(id);
                                    $("#search_block_name").val(blockname);
                                    removeinput = 2;
                                }else{
                                    openWin('js_pop_add_new_block');
                                    removeinput = 1;
                                }
                            },
                            close: function(event) {
                                if(typeof(removeinput)=='undefined' || removeinput == 1){
                                    $("#search_block_name").val("");
                                    $("#search_block_id").val("");
                                }
                            }
                        });
                    });
                    </script>
                <div class="fg_box">
                    <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;"><span class="btn_inner">精准匹配</span></a> </div>
                </div>
            </div>
            <?php if($searchlists=1){?>
            <div class="clearfix pop_fg_fun_box match-pager">
                <div class="get_page">
                    <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
				</div>
				<div id="js_search_box" class="shop_tab_title  scr_clear">
                    <a href="javascript:void(0);" onclick="$('#searchrange').val(2);$('input[name=page]').val(1);$('#search_form').submit();return false;" class="link <?php if($post_param['searchrange'] == 2){echo "link_on";}?>">所在公司（<span class="highlight"><?php echo $matchcount[2];?></span>）<span class="iconfont <?php if($post_param['searchrange'] != 2){echo "hide";}?>">&#xe607;</span></a>
                    <?php if(!$is_public){?>
			        <a href="javascript:void(0);" onclick="$('#searchrange').val(1);$('input[name=page]').val(1);$('#search_form').submit();return false;" class="link <?php if($post_param['searchrange'] == 1){echo "link_on";}?>">合作客源（<span class="highlight"><?php echo $matchcount[1];?></span>）<span class="iconfont <?php if($post_param['searchrange'] != 1){echo "hide";}?>">&#xe607;</span></a>
                    <?php } ?>
			        <a href="javascript:void(0);" onclick="$('#searchrange').val(3);$('input[name=page]').val(1);$('#search_form').submit();return false;" class="link <?php if($post_param['searchrange'] == 3){echo "link_on";}?>">所在门店（<span class="highlight"><?php echo $matchcount[3];?></span>）<span class="iconfont <?php if($post_param['searchrange'] != 3){echo "hide";}?>">&#xe607;</span></a>
			        <a href="javascript:void(0);" onclick="$('#searchrange').val(4);$('input[name=page]').val(1);$('#search_form').submit();return false;" class="link <?php if($post_param['searchrange'] == 4){echo "link_on";}?>">本人客源（<span class="highlight"><?php echo $matchcount[4];?></span>）<span class="iconfont <?php if($post_param['searchrange'] != 4){echo "hide";}?>">&#xe607;</span></a>
			    </div>
            </div>
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="c7">物业类型</th>
                        <th class="c15">意向区属板块</th>
                        <th class="c9">意向楼盘</th>
                        <th class="c6">户型</th>
                        <th class="c6">面积&nbsp;(㎡)</th>
                        <th class="c6">租金</th>
                        <th class="c9">委托门店</th>
                        <th class="c7">经纪人</th>
                        <?php if(!$is_public){?>
                        <th class="c7">操作</th>
                        <?php } ?>
                    </tr>
                    <?php if(is_array($customer_list) && !empty($customer_list)){
                        if('1'==$post_param['searchrange']){
                            $details_url = 5;
                        }else{
                            $details_url = 4;
                        }
                    ?>
                    <?php foreach ($customer_list as $key =>$value) {?>
                    <tr class="data_list <?php if($key % 2 == 1){ ?>bg<?php }?>" date-url="<?php echo MLS_URL;?>/rent_customer/details/<?php echo $value['id'];?>/<?php echo $details_url;?>" controller="rent_customer">
                        <td><?php echo $conf_customer['property_type'][$value['property_type']];?></td>
                        <td><?php
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
                        </td>
                        <td>
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
                        </td>
                        <td><?php echo $value['room_min'];?>-<?php echo $value['room_max'];?></td>
                        <td><?php echo strip_end_0($value['area_min']);?>-<?php echo strip_end_0($value['area_max']);?></td>
                        <td><?php echo $value['price_info'];?></td>
                        <td><?php echo $customer_broker_info[$value['broker_id']]['agency_name'];?></td>
                        <td><?php echo $customer_broker_info[$value['broker_id']]['truename'];?></td>
                        <?php if(!$is_public){?>
                            <td>
                            <?php if($value['broker_id'] == $broker_id){?>
                            <a href="javascript:void(0)" title="自己不能跟自己合作" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                            <?php }else{ ?>
                            <a href="javascript:void(0);" onclick="parent.cooperate_customer('rent_customer',<?php echo $value['id'];?>);">合作申请</a>
                            <?php } ?>
                            </td>
                        <?php }?>
                    </tr>
                    <?php } ?>
                    <?php }else{ ?>
                    <tr><td colspan="16"><span class="no-data-tip">抱歉，没有找到符合条件的客源信息</span></td></tr>
                    <?php } ?>
                </table>
            </div>
            <?php } ?>
            </form>
        </div>
    </div>
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
