<?php if ($friend == 'manage') { ?>
<script>
    window.parent.addNavClass(17);
</script>
<?php } elseif ($friend == 'district_manage' || $friend == 'district') { ?>
    <script>
        window.parent.addNavClass(27);
    </script>
<?php }else{?>
<script>
    window.parent.addNavClass(4);
</script>
<?php } ?>
<body>
	<!--描述：导航栏开始 -->
<div class="tab_box" id="js_tab_box">
<?php echo $user_menu;?>
</div>
<!--
<a href="/cooperate_lol/"><img style="position:absolute; top:48px; right:20px;" src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/hz.jpg"></a>
-->
<!--描述：导航栏结束-->
<!--描述：分类导航栏开始-->
    <form method='post' action='' id='search_form'>
<div id="js_search_box" class="shop_tab_title  scr_clear" style="margin-bottom:0;">
    <?php echo $user_func_menu;?>
    <?php if ($friend == 'district') { ?>
        <label class="label_t">
            <input type="checkbox" onclick="search_form.submit();return false;" value="1" name="is_district_public"
                   id="is_district_public" <?php if ($post_param['is_district_public'] == 1) {
                echo "checked='checked'";
            } ?>>公共房源
        </label>
    <?php } ?>
</div>
<!--描述：分类导航栏结束-->
<!--描述：选择区域开始-->

        <div class="search_box clearfix" id="js_search_box_02"> <a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this , 'sell_pub_list_extend')" data-h="0" id="extend">更多<span class="iconfont">&#xe609;</span></a>
            <div class="fg_box">
                <p class="fg fg_tex">时间：</p>
                <div class="fg">
                    <?php if ($friend !== 'district' && $friend !== 'manage') { ?>
                        <select class="select" id="searchtime" name="set_share_time">
                            <option value="1" <?php if ($post_param['set_share_time'] == 1) {
                                echo "selected";
                            } ?>>一个月内
                            </option>
                            <option value="2" <?php if ($post_param['set_share_time'] == 2) {
                                echo "selected";
                            } ?>>一季内
                            </option>
                            <option value="3" <?php if ($post_param['set_share_time'] == 3 || empty($post_param['set_share_time'])) {
                                echo "selected";
                            } ?>>半年内
                            </option>
                            <option value="4" <?php if ($post_param['set_share_time'] == 4) {
                                echo "selected";
                            } ?>>一年内
                            </option>
                            <option value="5" <?php if ($post_param['set_share_time'] == 5) {
                                echo "selected";
                            } ?>>一年以上
                            </option>
                        </select>
                    <?php } else { ?>
                        <select class="select" id="searchtime2" name="set_district_share_time">
                            <option value="1" <?php if ($post_param['set_district_share_time'] == 1) {
                                echo "selected";
                            } ?>>一个月内
                            </option>
                            <option value="2" <?php if ($post_param['set_district_share_time'] == 2) {
                                echo "selected";
                            } ?>>一季内
                            </option>
                            <option value="3" <?php if ($post_param['set_district_share_time'] == 3 || empty($post_param['set_district_share_time'])) {
                                echo "selected";
                            } ?>>半年内
                            </option>
                            <option value="4" <?php if ($post_param['set_district_share_time'] == 4) {
                                echo "selected";
                            } ?>>一年内
                            </option>
                            <option value="5" <?php if ($post_param['set_district_share_time'] == 5) {
                                echo "selected";
                            } ?>>一年以上
                            </option>
                        </select>
                    <?php } ?>
                </div>
            </div>
            <div class="fg_box">
                <p class="fg fg_tex"> 物业类型：</p>
                <div class="fg">
                    <select class="select" name='sell_type'>
                        <option value='0'>不限</option>
                        <?php
                        foreach($config['sell_type'] as $key => $val) {
                            echo '<option value="'.$key.'" ';
                            if($key == $post_param['sell_type'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?php if ($friend != 'district' && $friend != 'district_manage') { ?>
                <div class="fg_box">
                    <p class="fg fg_tex"> 区属：</p>
                    <div class="fg">
                        <select class="select" id='district' name='district' onchange="districtchange(this.value,'sell');">
                            <option value='0'>不限</option>
                            <?php foreach ($district as $k => $v) { ?>
                                <option value="<?php echo $v['id'] ?>" <?php if($v['id']==$post_param['district']){ echo "selected"; }?>><?php echo $v['district'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex"> 板块：</p>
                    <div class="fg">
                        <select class="select" name='street' id='street'>
                            <option value='0'>不限</option>
                            <?php
                            if($post_param['district']>0) {
                                foreach($street as $k => $v) {
                                    if($v['dist_id'] == $post_param['district']) {
                                        echo "<option value='".$v['id']."'";
                                        if($v['id'] == $post_param['street'])
                                            echo " selected ";
                                        echo ">".$v['streetname']."</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if ($friend == 'friend' || $friend == 'manage' || $friend == 'district' || $friend == 'district_manage') { ?>
                <div class="fg_box">
                    <p class="fg fg_tex"> 关键字：</p>
                    <div class="fg">
                        <input type="text" name="block_name" id="block_name" value="<?php echo $post_param['block_name']; ?>" class="input w90" placeholder="请输入经纪人姓名或楼盘名" style='width:170px'>
                        <input name="block_id" id="block_id" value="<?php echo $post_param['block_id']?>" type="hidden">
                    </div>
                </div>
            <?php }else{?>
                <div class="fg_box">
                    <p class="fg fg_tex"> 楼盘：</p>
                    <div class="fg">
                        <input type="text" name="block_name" id="block_name" value="<?php echo $post_param['block_name']; ?>" class="input w90">
                        <input name="block_id" id="block_id" value="<?php echo $post_param['block_id']?>" type="hidden">
                    </div>
                </div>
            <?php } ?>
            <script type="text/javascript">
//            $(function(){
//                $.widget( "custom.autocomplete", $.ui.autocomplete, {
//                    _renderItem: function( ul, item ) {
//                        if(item.id>0){
//                            return $( "<li>" )
//                            .data( "item.autocomplete", item )
//                            .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.label+'</span><span class="ui_district">'+item.districtname+'</span><span class="ui_address">'+item.address+'</span></a>')
//                            .appendTo( ul );
//                        }else{
//                            return $( "<li>" )
//                            .data( "item.autocomplete", item )
//                            .append('<a class="ui-corner-all" tabindex="-1">'+item.label+'</a>')
//                            .appendTo( ul );
//                        }
//                    }
//                });
//                $("#block_name").autocomplete({
//                    source: function( request, response ) {
//                        var term = request.term;
//                        $("#block_id").val("");
//                        $.ajax({
//                            url: "<?php echo MLS_URL;?>/community/get_cmtinfo_by_kw/",
//                            type: "GET",
//                            dataType: "json",
//                            data: {
//                                keyword: term
//                            },
//                            success: function(data) {
//                                //判断返回数据是否为空，不为空返回数据。
//                                if( data[0]['id'] != '0'){
//                                    response(data);
//                                }else{
//                                    response(data);
//                                }
//                            }
//                        });
//                    },
//                    minLength: 1,
//                    removeinput: 0,
//                    select: function(event,ui) {
//                        if(ui.item.id > 0){
//                            var blockname = ui.item.label;
//                            var id = ui.item.id;
//                            var streetid = ui.item.streetid;
//                            var streetname = ui.item.streetname;
//                            var dist_id = ui.item.dist_id;
//                            var districtname = ui.item.districtname;
//                            var address = ui.item.address;
//
//                            //操作
//                            $("#block_id").val(id);
//                            $("#block_name").val(blockname);
//                            removeinput = 2;
//                        }else{
//                            removeinput = 1;
//                        }
//                    },
//                    close: function(event) {
//                        if(typeof(removeinput)=='undefined' || removeinput == 1){
//                            $("#block_name").val("");
//                            $("#block_id").val("");
//                        }
//                    }
//                });
//            });
            </script>

            <div class="fg_box hide">
                <p class="fg fg_tex"> 户型：</p>
                <div class="fg">
                    <select class="select" name='room'>
                        <option value='0' <?php if($post_param['room'] == 0 ) echo "selected"; ?>>不限</option>
                        <option value='1' <?php if($post_param['room'] == 1 ) echo "selected"; ?>>一室</option>
                        <option value='2' <?php if($post_param['room'] == 2 ) echo "selected"; ?>>二室</option>
                        <option value='3' <?php if($post_param['room'] == 3 ) echo "selected"; ?>>三室</option>
                        <option value='4' <?php if($post_param['room'] == 4 ) echo "selected"; ?>>四室</option>
                        <option value='5' <?php if($post_param['room'] == 5 ) echo "selected"; ?>>五室</option>
                        <option value='6' <?php if($post_param['room'] == 6 ) echo "selected"; ?>>六室</option>
                    </select>
                </div>
            </div>
            <div class="fg_box hide">
                <p class="fg fg_tex"> 面积：</p>
                <div class="fg">
                    <input type="text" name='areamin' value="<?php echo $post_param['areamin']; ?>" class="input w30">
                </div>
                <p class="fg fg_tex fg_tex02">—</p>
                <div class="fg">
                    <input type="text" name='areamax' value="<?php echo $post_param['areamax']; ?>" class="input w30">
                </div>
                <p class="fg fg_tex fg_tex03">平米</p>
            </div>
            <div class="fg_box hide">
                <p class="fg fg_tex"> 总价：</p>
                <div class="fg">
                    <input type="text" name='pricemin' value="<?php echo $post_param['pricemin']; ?>" class="input w30">
                </div>
                <p class="fg fg_tex fg_tex02">—</p>
                <div class="fg">
                    <input type="text" name='pricemax' value="<?php echo $post_param['pricemax']; ?>" class="input w30">
                </div>
                <p class="fg fg_tex fg_tex03">万元</p>
            </div>
            <?php if ($friend != 'manage' && $friend != 'district') { ?>
                <div class="fg_box hide">
                    <p class="fg fg_tex"> 奖励方式：</p>
                    <div class="fg">
                        <select class="select" name='reward_type'>
                            <option value='0' <?php if($post_param['reward_type'] == 0 ) echo "selected"; ?>>不限</option>
                            <option value='1' <?php if($post_param['reward_type'] == 1 ) echo "selected"; ?>>佣金</option>
                            <option value='2' <?php if($post_param['reward_type'] == 2 ) echo "selected"; ?>>悬赏</option>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <div class="fg_box hide">
                <p class="fg fg_tex"> 房源特色：</p>
                <div class="fg">
                    <select class="select" name='house_degree'>
                        <option value='0' <?php if($post_param['house_degree'] == '0' ) echo "selected"; ?>>不限</option>
                        <option value='1' <?php if($post_param['house_degree'] == 1 ) echo "selected"; ?>>普通房源</option>
                        <option value='2' <?php if($post_param['house_degree'] == 2 ) echo "selected"; ?>>优质房源</option>
                        <option value='3' <?php if($post_param['house_degree'] == 3 ) echo "selected"; ?>>独家房源</option>
                    </select>
                </div>
            </div>
            <div class="fg_box hide">
                <p class="fg fg_tex"> 楼层：</p>
                <div class="fg">
                    <select class="select" name='floor_scale'>
                        <option value='0' <?php if($post_param['floor_scale'] == '0' ) echo "selected"; ?>>不限</option>
                        <option value='1' <?php if($post_param['floor_scale'] == 1 ) echo "selected"; ?>>高</option>
                        <option value='2' <?php if($post_param['floor_scale'] == 2 ) echo "selected"; ?>>中</option>
                        <option value='3' <?php if($post_param['floor_scale'] == 3 ) echo "selected"; ?>>低</option>
                    </select>
                </div>
            </div>
            <?php if (false) { ?>
                <div class="fg_box hide">
                    <p class="fg fg_tex"> 是否视频房源：</p>
                    <div class="fg">
                        <select class="select" name='is_video'>
                            <option value='0' <?php if($post_param['is_video'] == '0' ) echo "selected"; ?>>不限</option>
                            <option value='1' <?php if($post_param['is_video'] == 1 ) echo "selected"; ?>>是</option>
                            <option value='2' <?php if($post_param['is_video'] == 2 ) echo "selected"; ?>>否</option>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <!--获取经纪人信息-->
<script>
function chang(type){
    var agency_id=$("select[name='post_agency_id']").val();
    $.ajax({
        url: "<?php echo MLS_URL;?>/"+type+"/broker_list/",
        type: "GET",
        dataType: "json",
        data:{agency_id: agency_id},
        success:function(data_list){
            var str_html='<option value="0">不限</option>';
            for(var i=0; i<data_list.length; i++){
                str_html +='<option value='+data_list[i].broker_id+'>'+data_list[i].truename+'</option>';
            }
            $("#list_broker").empty().html(str_html);
        }
    });
}
</script>
            <div class="fg_box">
                <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;" ><span  class="btn_inner">搜索</span></a> </div>
                <div class="fg"> <a href="javascript:void(0)" class="reset" onclick='del_cookie();'>重置</a> </div>    </div>
</div>
<script>
function log_data_replace(){
    var window_min_id = [];
    $('input[name="window_min_id"]').each(function (){
        window_min_id.push($(this).val());
    });
    $.ajax({
        url: "/sell/min_log_replace/",
        type: "GET",
        data:{
            'window_min_id':window_min_id,
            'is_pub':1
        }
    });
}

function log_data_del(){
    var window_min_id = [];
    $('input[name="window_min_id"]').each(function (){
        window_min_id.push($(this).val());
    });
    $.ajax({
        url: "/sell/min_log_del/",
        type: "GET",
        data:{
            'window_min_id':window_min_id,
            'is_pub':1
        }
    });
}

$(function(){
    document.onkeydown = function(e){ //enter
        var ev = document.all ? window.event : e;
        if(ev.keyCode==13) {
            //$('#search_form :input[name=page]').val('1');
            $('#search_form').submit();return false;
        }
    }

    //最小化
    $('#window_min_click').live('click',function(){
        $(this).parents("div").hide();
        var window_min_name = $('#window_min_name').val();
        var window_min_url = $('#window_min_url').val();
        var window_min_id = $('#window_min_id').val();

        //判断该数据是否已最小化
        var window_min = $('#window_min_id_'+window_min_id);
        if('undefined'==typeof(window_min[0])){
            var window_min_html = '';
            window_min_html += '<li id="'+'window_min_id_'+window_min_id+'">';
            window_min_html += '<span class="zws_bottom_nav_dao_img "></span>';
            window_min_html += '<span class="zws_bottom_span">'+window_min_name+'</span>';
            window_min_html += '<input type="hidden" value="'+window_min_url+'"/>';
            window_min_html += '<input type="hidden" value="'+window_min_id+'" name="window_min_id" />';
            window_min_html += '<span class="iconfont zws_bottom_span_close">&#xe62c;</span>';
            window_min_html += '</li>';
            $('#window_min').append(window_min_html);
            var num = $('#window_min').children().size();
            $('#window_min').css('width',210*num);

            totalNumLi = ($(".zws_bottom_nav_dao li").length);
            samllTab();

            //操作日志数据
            log_data_replace();
        }
    });

    //关闭弹框删除最小化
    $('#window_min_close').live('click',function(){
        var window_min_id = $('#window_min_id').val();
        $('#window_min_id_'+window_min_id).remove();
        $(this).parents("div").hide();
        //操作日志数据
        log_data_del();
    });

    var totalNumLi = $(".zws_bottom_nav_dao li").length;
    var smallCur = 0;
    var objNum = 0;

    function samllTab() {
        totalNumLi = $(".zws_bottom_nav_dao li").length;
        //当前标签处理
        titleShowBj();
        //弹出内容
        detialShow();
        //切换箭头显示与隐藏
        tabShow();

    }

    //左右切换
    function preNex(){
        //左切换
        $(".small_nex").live("click", function () {
            //alert("a");
            objNum--;
            objNum = objNum < 1 ? 0 : objNum;
            $(".zws_bottom_nav_dao").find("ul").animate({ "margin-left": -objNum * 200+"px" }, 300)

        })
        //右切换
        $(".small_pre").live("click", function () {
            //alert("b");
            objNum++;
            objNum = objNum < totalNumLi ? objNum : totalNumLi-1;
            $(".zws_bottom_nav_dao").find("ul").animate({ "margin-left": -objNum * 200 + "px" }, 300)

        })


    }

    preNex();

    //切换显示与否
    function tabShow(){
        var aW = 210;
        var aBody = $(window).width()*0.95;
        var aLi = $(".zws_bottom_nav_dao li").length;
        var totalLen = aW * aLi ;
        if(totalLen < aBody){
            $(".zws_container").css("display","none");
            //alert(aLi);
        }
        else{
            $(".zws_container").css("display","block");
            //alert(aLi);
        }
    }

    tabShow();

    //底部标题关闭处理
    function titleClose(){

        $(".zws_bottom_nav_dao li").find(".zws_bottom_span_close").live("click", function () {
            //alert("a");
            $(this).parent("li").remove();
            //UlLength(aObjUl, aObjLl);
            tabShow();
            //操作日志数据
            log_data_del();
        })
        totalNumLi = $(".zws_bottom_nav_dao li").length;
    }

    titleClose();


    //弹出内容
    function detialShow(){
        $(".zws_bottom_nav_dao").find(".zws_bottom_span").live("click",function(){
            smallCur =($(this).parent("li").index()); //当前最小化的标签高亮
            var aUrl = $(this).next("input").val();
            var id = $(this).next("input").next("input").val();
            $('#window_min_id').val(id);

            openWin('js_pop_box_g');

            $("#js_pop_box_g").find("iframe").attr("src",aUrl);

            $(".zws_bottom_nav_dao_img").removeClass("curSmall_S");
            $(this).prev("span").addClass("curSmall_S");
        })

    }

    detialShow();
    //当前标签显示
    function titleShowBj(){
        $(".zws_bottom_nav_dao").find("li").on("click", function () {
            $(".zws_bottom_nav_dao_img").removeClass("curSmall_S");
            $(this).find(".zws_bottom_nav_dao_img").addClass("curSmall_S");

        })

    }

    titleShowBj();


});
</script>
<!--描述：内容选择项结束-->
<!--描述：主要内容区域开始-->
<div class="table_all">
    <div class="title" id="js_title">
        <table class="table">
            <tr>
                <td class="c3">
                    <div class="info">

                    </div>
                </td>
                <?php if ($friend != 'district' && $friend != 'manage') { ?>
                    <td class="c8"><div class="info f60">合作奖励</div></td>
                <?php } ?>
                <td class="c5"><div class="info">标签</div></td>
                <td class="c5"><div class="info">物业类型</div></td>
                <td class="c5"><div class="info">区属</div></td>
                <td class="c5"><div class="info">板块</div></td>
                <td class="c10"><div class="info">楼盘</div></td>
                <td class="c5"><div class="info">户型</div></td>
                <td class="c5">
                    <div class="info">面积<br>(㎡)</div>
                </td>
                <input type="hidden" name='orderby_id' id="orderby_id" value="<?php echo $post_param['orderby_id']?>">
                <td class="c5"><div class="info"><a href="javascript:void(0)" onclick="selllist_order(7);return false;" id="order_price" class="i_text <?php if($post_param['orderby_id'] == 8 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 7){ echo 'i_up'; } ?>">报价<br>
                            (W)</a></div></td>
                <td class="c6"><div class="info"><a href="javascript:void(0)" onclick="selllist_order(9);return false;" id="order_avgprice" class="i_text <?php if($post_param['orderby_id'] == 10 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 9){ echo 'i_up'; } ?>">单价<br>
                            (元/㎡)</a></div></td>
                <td class="c7"><div class="info">经纪人</div></td>
                <!--
                <td class="c5"><div class="info">好评率</div></td>
                <td class="c4"><div class="info">合作成功率</div></td>
                -->
                <td class="c9"><div class="info"><a href="javascript:void(0)" onclick="selllist_order(15);return false;" id="order_avgprice" class="i_text <?php if($post_param['orderby_id'] == 16 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 15){ echo 'i_up'; } ?>">发布时间<br>
                            <?php if ($friend !== 'district' && $friend !== 'manage'){ ?>
                <td colspan="3" ><div class="info">操作</div></td>
            <?php } ?>
            </tr>
        </table>
    </div>
    <div id="js_innerHouse" class="inner" style="overflow-y: scroll; *position:relative;height:331px;">
        <table class="table table_q" id="js_table_box_Sincerity">
            <?php
            if($list) {
                foreach($list as $key => $val) {
                    ?>
                    <tr info_id="<?php echo $val['id']; ?>" <?php if ($key % 2 == 1){ ?>class="bg" <?php } ?>
                        id="tr<?php echo $val['id']; ?>"
                        date-url="<?php echo MLS_URL; ?>/sell/details_house/<?php echo $val['id']; ?>/<?= $friend == 'district' ? 7 : 2; ?>"
                        controller="sell" _id="<?php echo $val['id']; ?>"
                        min_title="<?php echo $val['block_name'] . '-' . intval($val['price']) . '万' . '-' . intval($val['buildarea']) . '平米'; ?>">
                        <td class="c3">
                            <div class="info">
                                <input type="checkbox" name="items" value="<?php echo $val['id'];?>" class="checkbox" style="display:none;">
                            </div>
                        </td>
                        <?php if ($friend !== 'district' && $friend !== 'manage') { ?>
                            <td class="c8">
                                <div class="info ff9c00">
                                    <?php if('0'==$val['reward_type']){?>
                                        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/y.png">
                                        <?=$config['commission_ratio'][$val['commission_ratio']]?>
                                    <?php }else if('1'==$val['reward_type']){?>
                                        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/y.png">
                                        <?=$config['commission_ratio'][$val['commission_ratio']]?>
                                    <?php }else if ('2'==$val['reward_type']) { ?>
                                        <span class="iconfont ts2">&#xe658;</span>
                                        <strong><?php echo $val['cooperate_reward']; ?></strong>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </td>
                        <?php } ?>
                        <td class="c5">
                            <div class="info">
                                <?php if($val['pic']){ ?><span title="此房源有图片" class="iconfont ts">&#xe645;</span><?php } ?>
                                <?php if($val['keys']){ ?><span title="此房源有钥匙" class="iconfont ts ts02">&#xe60d;</span><?php } ?>
                                <?php if($val['video_id']){ ?><span title="此房源有视频" class="iconfont ts">&#xe65e;</span><?php } ?>
                            </div>
                        </td>
                        <td class="c5"><div class="info"><?php echo $config['sell_type'][$val['sell_type']]; ?></div></td>
                        <td class="c5"><div class="info"><?php echo $district[$val['district_id']]['district']; ?></div></td>
                        <td class="c5"><div class="info"><?php echo $street[$val['street_id']]['streetname']; ?></div></td>
                        <td class="c10">
                            <div class="info f14 fblod">
                                <?php if('2'==$val['house_degree']){?>
                                    <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zs02.png">
                                <?php }else if('3'==$val['house_degree']){ ?>
                                    <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zs03.png">
                                <?php } ?>
                                <?php echo $val['block_name']; ?>
                            </div>
                        </td>
                        <td class="c5"><div class="info fblod"><?php echo $val['room']; ?>-<?php echo $val['hall']; ?>-<?php echo $val['toilet']; ?></div></td>
                        <td class="c5"><div class="info"><?php echo strip_end_0($val['buildarea']); ?></div></td>
                        <td class="c5"><div class="info f13 fblod"><?php echo strip_end_0($val['price']); ?></div></td>
                        <td class="c6"><div class="info f13 fblod"><?php echo round($val['avgprice']); ?></div></td>
                        <?php if ($friend !== 'district' && $friend !== 'manage') { ?>
                            <td class="c7 js_info broker" data-brokerId ="<?php echo $val['broker_id'];?>" data_id="<?php echo $val['id'];?>" type="sell_house"><div class="info"><?php echo $val['broker_name']; ?><!--<span class="onper iconfont">&#xe616;</span>--> </div></td>
                        <?php } else { ?>
                            <td class="c7 js_info broker" data-brokerId="<?php echo $val['district_broker_id']; ?>"
                                data_id="<?php echo $val['id']; ?>" type="sell_house">
                                <div class="info">
                                    <?php echo $val['district_broker_name']; ?><!--<span class="onper iconfont">&#xe616;</span>--> </div>
                            </td>
                        <?php } ?>

                        <!--
                <td class="c5">
                    <div class="info">
                    <?php
                        if( $val['good_rate'] == '') {
                            echo '--';
                        }else{
                            echo $val['good_rate']."%";
                        }
                        ?>
                    </div>
                </td>
                <td class="c4">
                    <div class="info">
                    <?php
                        if(!empty($val['cop_succ_ratio_info']['cop_succ_ratio']) &&
                            $val['cop_succ_ratio_info']['cop_succ_ratio'] > 0) {
                            echo $val['cop_succ_ratio_info']['cop_succ_ratio'].'%';
                        } else if($val['cop_succ_ratio_info']['cop_succ_ratio'] == 0 ) {
                            echo '--';
                        }
                        ?>
                    </div>
                </td>
                -->
                        <?php if ($friend !== 'district' && $friend !== 'manage') { ?>
                            <td class="c9"><div class="info"><?php echo date('Y-m-d H:i',$val['set_share_time']); ?></div></td>
                        <?php } else { ?>
                            <td class="c9">
                                <div class="info"><?php echo date('Y-m-d H:i', $val['set_district_share_time']); ?></div>
                            </td>
                        <?php } ?>
                        <?php if ($friend != 'district' && $friend != 'manage') { ?>
                            <td class="js_no_click">
                                <div class="info_p_r">
                                    <?php if($friend == 'manage'){?>
                                        <a href="javascript:void(0)" onclick = "sharecancel(0,'sell',<?php echo $val['id'];?>,'friend');" class="js_input_7">从朋友圈下架</a>
                                    <?php } elseif ($friend == 'district_manage') { ?>
                                        <a href="javascript:void(0)" onclick="sharecancel(0,'sell',<?php echo $val['id']; ?>,'district');"
                                           class="js_input_7">从区域公盘下架</a>
                                    <?php } else { ?>
                                        <?php if($check_coop_reulst[$val['id']] == 1){ ?>
                                            <a href="javascript:void(0)" title = "已申请" style="color:#b2b2b2;text-decoration:none;">已申请</a>
                                        <?php }else if( $val['broker_id'] != $broker_id && '1'==$open_cooperate){?>
                                            <a href="javascript:void(0)" onclick="cooperate_house('sell',<?php echo $val['id'];?>,<?php echo $val['broker_id'];?>);">合作申请</a>
                                        <?php }else if('0'==$open_cooperate){?>
                                            <a href="javascript:void(0)" title = "当前公司未开启合作中心" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                                        <?php } else { ?>
                                            <a href="javascript:void(0)" title = "自己不能跟自己合作" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                                        <?php } ?>
                                        <span style="margin:0 2px;color:#b2b2b2;">|</span>
                                        <a href="javascript:void(0)" onclick="open_match('sell',1,<?php echo $val['id'];?>)">智能匹配</a>
                                        <span style="margin:0 2px;color:#b2b2b2;">|</span>
                                        <?php if(in_array($val['id'] , $num_id)){ ?>
                                            <a href="javascript:void(0)" class="shcang" style="color:#b2b2b2;text-decoration:none;">已收藏</a>
                                        <?php } else { ?>
                                            <a href="javascript:void(0)" class="shcang" id = "cang<?php echo $val['id']?>" onclick="shcang('house_collect','sell_house',<?php echo $val['id'] ?>)">收藏</a>
                                        <?php } ?>
                                        <?php if($friend != 'friend'){?>
                                            <span style="margin:0 2px;color:#b2b2b2;">|</span>
                                            <?php if($val['status_friend'] == 0){?>
                                                <a href="javascript:void(0);" class="MyFriendSearchAdd_cooperate sendColor">自己</a>
                                            <?php }elseif($val['status_friend'] == 1){?>
                                                <a href="javascript:void(0);" class="MyFriendSearchAdd_cooperate sendColor">已添加</a>
                                            <?php }elseif($val['status_friend'] == 2){?>
                                                <a href="javascript:void(0);" class="MyFriendSearchAdd_cooperate sendColor">已申请</a>
                                            <?php }else{?>
                                                <a href="javascript:void(0);" class="MyFriendSearchAdd_cooperate bid<?php echo $val['broker_id']?>" onclick="add_friend(<?php echo $val['broker_id']?>)">添加好友</a>
                                            <?php }}} ?>
                                </div>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php }?>
        </table>
    </div>
    <script type="text/javascript">
        function getCookie(name)//取cookies函数
        {
            var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
            if(arr != null) return unescape(arr[2]); return null;
        }

        $(function(){
            function reHeightList(){

                var ListHeight = $(window).height();
                var TabHeight = $(".tab_box").height();
                var SearchHeight = $("#js_search_box").height();
                var SearchHeight2 = $('#js_search_box_02').height();
                //$("#js_innerHouse").css("height",(ListHeight-TabHeight-SearchHeight-195)+"px");
                $("#js_innerHouse").css("height",(ListHeight-TabHeight-SearchHeight-SearchHeight2-147)+"px");
            }

            reHeightList();
            setInterval(function(){
                reHeightList();
            },500)

            //'收起'，‘更多’按钮，获得cookie值
            var sell_pub_list_extend = getCookie('sell_pub_list_extend');
            if(1==sell_pub_list_extend){
                $('#js_search_box_02').find(".hide").css("display","inline");
                $('#extend').html('收起<span class="iconfont">&#xe60a;</span>');
                $('#extend').attr("data-h","1");
            }else{
                $('#js_search_box_02').find(".hide").hide();
                $('#extend').html('更多<span class="iconfont">&#xe609;</span>');
                $('#extend').attr("data-h","0");
            }

        })

        function add_friend(broker_id_friend){
            $.ajax({
                type: "post",
                url: "/cooperate_friends/add_apply/",
                dataType:"json",
                data: {
                    broker_id_friend: broker_id_friend
                },
                cache:false,
                error:function(){
                },
                success: function(data){
                    if(data['status'] == 1){
                        $(".bid"+data['broker_id_friend']).html('已申请');
                        $(".bid"+data['broker_id_friend']).addClass('sendColor');
                    }
                }
            });
        }
    </script>
</div>
<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
    <div class="get_page"><?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?></div>
</div>
<!--最小化导航栏-->
        <!--<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/sreen_small.js"></script>-->
        <div class="zws_bottom_nav" style="margin-top:2px;">
            <div class="zws_bottom_nav_dao">
                <ul id="window_min">
                    <?php if(is_full_array($sell_list_min_arr)){
                        foreach($sell_list_min_arr as $k => $v){
                            ?>
                            <li id="window_min_id_<?php echo $v['house_id']; ?>">
                                <span class="zws_bottom_nav_dao_img "></span>
                                <span class="zws_bottom_span"><?php echo $v['name']; ?></span>
                                <input type="hidden" value="<?php echo '/sell/details_house/'.$v['house_id'].'/2' ?>"/>
                                <input type="hidden" value="<?php echo $v['house_id']; ?>" name="window_min_id" />
                                <span class="iconfont zws_bottom_span_close">&#xe62c;</span>
                            </li>
                        <?php }} ?>
                </ul>
            </div>
            <!--切换-->
            <div class="zws_bottom_nav_dao_tab zws_container">
                <p class="small_pre"></p>
                <p class="small_nex"></p>
            </div>
        </div>
</form>

<!--描述：主要内容区域结束-->
<ul id="openList">
    <input type="hidden" id="right_id" class="js_input" value="">
    <!--右键菜单-->
    <?php if ($friend != 'district' && $friend != 'manage') { ?>
        <li onClick="openHouseDetails('sell',2);">查看详情</li>
    <?php } else { ?>
        <li onClick="openHouseDetails('sell',7);">查看详情</li>
    <?php } ?>
</ul>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/loading.gif" id="mainloading" ><!--遮罩 loading-->
<!--描述：右击弹出列表页面*END*-->

<!--弹出框列表*STARTING*-->
<!--合作申请弹框-->
<div id="js_pop_box_cooperation_customer" class="iframePopBox" style=" width:920px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="920" height="540" class='iframePop' src=""></iframe>
</div>
<!--弹出框列表*ENDING*-->

<!--分配房源-->
<div id="js_allocate_house" class="iframePopBox" style=" width:816px; height:340px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="340" class='iframePop' src=""></iframe>
</div>

<!--详情页弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <input type="hidden" value="" id="window_min_name"/>
    <input type="hidden" value="" id="window_min_url"/>
    <input type="hidden" value="" id="window_min_id"/>
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" style="right:46px;" id="window_min_click">一</a>
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" id="window_min_close">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--匹配页弹框-->
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
<div id="js_woyaojubao" class="iframePopBox" style=" width:500px; height:445px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="500" height="445" class='iframePop' src=""></iframe>
</div>

<!--跟进信息弹框-->
<div id="js_genjin" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--详情页弹框
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>-->

<!--匹配页弹框
<div id="js_pop_box_g_match" class="iframePopBox" style=" width:916px; height:504px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="916" height="504" class='iframePop' src=""></iframe>
</div>-->

<!--评价弹框-->
<div id="js_pop_box_appraise1" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--页面处理中弹层-->
<div style="display:none; text-align: center;" id ='docation_loading'>
    <img src ="<?php echo MLS_SOURCE_URL; ?>/common/images/loading_6.gif">
    <p style="font-size: 16px; font-family:'微软雅黑'; line-height: 30px; color: #fff;">正在处理</p>
</div>

<!--经纪人信用弹框-->
<div class="broker-info-wrap" id="broker_info_wrap"></div>
    <?php if ($friend == 'manage' || $friend == 'district_manage') { ?>
<!--操作结果弹出警告-->
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
			<div class="text-wrap">
                    <table>
                        <tr>
                            <td><div class="img"><img alt="" id="imgg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></div></td>
                            <td class="msg" ><span class="bold" id="dialog_do_warnig_tip"></span></td>
                        </tr>
                    </table>
                </div>
				<a href="javascript:void(0);" id="sure_yes" class="btn-lv1 btn-mid btn_qd_text JS_Close" style="margin:0 auto !important">确定</a>
            </div>

        </div>
    </div>
</div>
<!--取消分享弹框-->
<div id="js_pop_cancel_share_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <div class="text-wrap">
                    <table>
                        <tr>
                            <td><div class="img"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></div></td>
                            <td class="msg"><span class="bold">您确定要将该房源下架吗？</span></td>
                        </tr>
                    </table>
                </div>
                <div class="center">
                <button type="button" class="btn-lv1 btn-left" id="quxiao_share">确定</button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            	</div>
            </div>
        </div>
    </div>
</div>
<?php }?>
<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>
</body>
<script type="text/javascript">
    function del_cookie()
    {
        $.ajax({
            url: "/sell/del_search_cookie/sell_lists_pub",
            type: "POST",
            dataType: "json",
            success: function(data) {
                if ('success' == data.status) {
                    window.location.href=window.location.href;
                    window.location.reload;
                }
            }
        });
    }
</script>
