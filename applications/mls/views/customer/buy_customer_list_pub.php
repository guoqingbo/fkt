<!--页面部分-->
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
<div id="js_search_box" class="shop_tab_title" style="margin-bottom:0;">
 <?php echo $user_func_menu;?>
</div>
<form action = '<?php echo MLS_URL;?>/customer/manage_pub' method = 'post' name = 'search_form' id ='search_form'>
<div class="search_box clearfix" id="js_search_box_02">
    <a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this , 'buy_customer_pub_list_extend')" data-h="0" id="extend">更多<span class="iconfont">&#xe609;</span></a>
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
            <input type="text" name='cmt_name' class="input w90 ui-autocomplete-input" value="<?php echo $post_param['cmt_name'];?>">
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
    <div class="fg_box hide">
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
    <div class="fg_box hide">
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
    <div class="fg_box hide">
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
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" onclick="sub_form('search_form');return false;" class="btn" ><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"> <a href="javascript:void(0)" class="reset" onclick='del_cookie();'>重置</a> </div>
    </div>
</div>
<script>
function log_data_replace(){
    var window_min_id = [];
    $('input[name="window_min_id"]').each(function (){
        window_min_id.push($(this).val());
    });
    $.ajax({
        url: "/customer/min_log_replace/",
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
        url: "/customer/min_log_del/",
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
                <td><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner" id="js_innerHouse" style="height:331px;">
        <table class="table table_q" id="js_table_box_Sincerity">
            <?php if(is_array($customer_list) && !empty($customer_list)){ ?>
            <?php foreach ($customer_list as $key =>$value) {?>
            <tr info_id = "<?php echo $value['id'];?>" <?php if($key % 2 == 1){ ?>class="bg" <?php }?> date-url="<?php echo MLS_URL;?>/customer/details/<?php echo $value['id'];?>/1" controller="customer" _id="<?php echo $value['id'];?>" min_title="<?php echo $district_arr[$value['dist_id1']]['district'].'-'.$street_arr[$value['street_id1']]['streetname'].' '.intval($value['price_min']).'-'.intval($value['price_max']).'万'; ?>">
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
                        echo '--';
                    }else{
                        echo $customer_broker_info[$value['broker_id']]['good_rate']."%";
                    }
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
                        echo '--';
                    }
                    ?>
                    </div>
                </td>
                -->
                <td class="c7"><div class="info"><?php echo date('Y-m-d H:i',$value['set_share_time']);?></div></td>
                <td class="js_no_click">
                <div class="info_p_r">
                    <?php if($check_coop_reulst[$value['id']] == 1) { ?>
                            <a href="javascript:void(0)" title = "已申请" style="color:#b2b2b2;text-decoration:none;">已申请</a>
                    <?php } else if( $value['broker_id'] != $broker_id && '1'==$open_cooperate){?>
                            <a href="javascript:void(0)" onclick="cooperate_customer('buy_customer',<?php echo $value['id'];?>);">合作申请</a>
                    <?php } else if( '0'==$open_cooperate ){?>
                            <a href="javascript:void(0)" title = "当前公司未开启合作中心" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                    <?php } else { ?>
                            <a href="javascript:void(0)" title = "自己不能跟自己合作" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                    <?php } ?>
                    <span style="margin:0 2px;color:#b2b2b2;">|</span><a href="javascript:void(0)" onclick="open_match_customer('customer',1,<?php echo $value['id'];?>)">智能匹配</a><span style="margin:0 2px;color:#b2b2b2;">|</span>
                    <?php if( isset($collected_ids) && in_array($value['id'] , $collected_ids)){ ?>
                    <a href="javascript:void(0)" class="shcang" style="color:#b2b2b2;text-decoration:none;">已收藏</a>
                    <?php
                    } else {
                    ?>
                    <a href="javascript:void(0)" class="shcang" onclick="collect_customer(<?php echo $value['id'];?>,'buy_customer');" id="collect_<?php echo $value['id'];?>">收藏</a>
                    <?php
                    }
                    ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
            <?php }else{ ?>
            <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php } ?>
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
				$("#js_innerHouse").css("height",(ListHeight-TabHeight-SearchHeight-195)+"px");

			}
			reHeightList();
			setInterval(function(){
				reHeightList();
			},500)

            //'收起'，‘更多’按钮，获得cookie值
            var buy_customer_pub_list_extend = getCookie('buy_customer_pub_list_extend');
            if(1==buy_customer_pub_list_extend){
                $('#js_search_box_02').find(".hide").css("display","inline");
                $('#extend').html('收起<span class="iconfont">&#xe60a;</span>');
                $('#extend').attr("data-h","1");
            }else{
                $('#js_search_box_02').find(".hide").hide();
                $('#extend').html('更多<span class="iconfont">&#xe609;</span>');
                $('#extend').attr("data-h","0");
            }
		})
	</script>
</div>
<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>
<!--最小化导航栏-->
<!--<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/sreen_small.js"></script>-->
<div class="zws_bottom_nav" style="margin-top:2px;">
	<div class="zws_bottom_nav_dao">
		<ul id="window_min">
			<?php if(is_full_array($buy_list_min_arr)){
				foreach($buy_list_min_arr as $k => $v){
			?>
			<li id="window_min_id_<?php echo $v['customer_id']; ?>">
			<span class="zws_bottom_nav_dao_img "></span>
			<span class="zws_bottom_span"><?php echo $v['name']; ?></span>
			<input type="hidden" value="<?php echo '/customer/details/'.$v['customer_id'].'/1' ?>"/>
			<input type="hidden" value="<?php echo $v['customer_id']; ?>" name="window_min_id" />
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
    <input type="hidden" value="" id="window_min_name"/>
    <input type="hidden" value="" id="window_min_url"/>
    <input type="hidden" value="" id="window_min_id"/>
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" style="right:46px;" id="window_min_click">一</a>
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" id="window_min_close">&#xe60c;</a>
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
<div id="js_woyaojubao" class="iframePopBox" style=" width:500px; height:445px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="500" height="445" class='iframePop' src=""></iframe>
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
<script type="text/javascript">
    function del_cookie()
    {
        $.ajax({
            url: "/customer/del_search_cookie/customer_manage_pub",
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
