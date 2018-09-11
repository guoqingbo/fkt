<body>
	<!--描述：导航栏开始 -->
<div class="tab_box" id="js_tab_box">
	<?php echo $user_menu; ?>
</div>
<!--描述：导航栏结束-->
<!--描述：分类导航栏开始-->
<div id="js_search_box" class="shop_tab_title  scr_clear" style="margin-bottom:0;">
    <?php echo $user_func_menu;?>
</div>
<!--描述：分类导航栏结束-->
<!--描述：选择区域开始-->
<form method='post' action='' id='search_form'>
<div class="search_box clearfix" id="js_search_box_02"> <a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this)" data-h="0">展开<span class="iconfont">&#xe609;</span></a>
    <div class="fg_box">
        <p class="fg fg_tex">时间：</p>
        <div class="fg">
            <select class="select" id="searchtime" name="searchtime">
                <option value="1" <?php if($post_param['searchtime'] == 1){echo "selected";}?>>一个月内</option>
                <option value="2" <?php if($post_param['searchtime'] == 2){echo "selected";}?>>一季内</option>
                <option value="3" <?php if($post_param['searchtime'] == 3 || empty($post_param['searchtime'])){echo "selected";}?>>半年内</option>
                <option value="4" <?php if($post_param['searchtime'] == 4){echo "selected";}?>>一年内</option>
                <option value="5" <?php if($post_param['searchtime'] == 5){echo "selected";}?>>一年以上</option>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 状态：</p>
        <div class="fg">
            <select class="select" name='status'>
                    <option value='0'>不限</option>
                    <?php
                        foreach($config['status'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'"';
                            if($key == $post_param['status'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>
                </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 物业类型：</p>
        <div class="fg">
            <select class="select" name='sell_type'>
                    <option value='0'>不限</option>
                    <?php
                        foreach($config['sell_type'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'" ';
                            if($key == $post_param['sell_type'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>
                </select>
        </div>
    </div>
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
                        if($post_param['district']>0)
                        {
                            foreach($street as $k => $v)
                            {
                                if($v['dist_id'] == $post_param['district'])
                                {
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
    <div class="fg_box">
        <p class="fg fg_tex"> 楼盘：</p>
        <div class="fg">
            <input type="text" name="block_name" id="block_name" value="<?php echo $post_param['block_name']; ?>" class="input w90">
                <input name="block_id" id="block_id" value="<?php echo $post_param['block_id']?>" type="hidden">
        </div>
    </div>
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
//                            url: "/community/get_cmtinfo_by_kw/",
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
//                            openWin('js_pop_add_new_block');
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
    <div style="float:left;clear:left;" class="hide"></div>
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
                    <option value='7' <?php if($post_param['room'] == 7 ) echo "selected"; ?>>六室以上</option>
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
    <div class="fg_box hide">
					 <p class="fg fg_tex"> 范围：</p>
                <div class="fg">
                <select class="select" name="agency_id" onchange="chang('sell')">
					<option value='0'>不限</option>
					<?php if($agency_list){
						foreach($agency_list as $key=>$val){
						?>
						<option <?php if($val['agency_id'] == $post_param['agency_id'])
                                echo "selected"; ?> value="<?php echo $val['agency_id'];?>"><?php echo $val['agency_name'];?></option>
					<?php }}?>

					     </select>
                     </div>
					 <div class="fg fg_tex fg_tex03" >
                <select class="select" name="broker_id" id="list_broker">
				<option value='0'>不限</option>
				<?php if($broker_list){ ?>
						<?php foreach($broker_list as $key=>$val){ ?>
						<option  <?php if($val['broker_id'] == $post_param['broker_id'])
                                echo "selected"; ?> value='<?php echo $val['broker_id']?>'><?php echo $val['truename']?></option>
					<?php }}?>
                </select>
            </div>
        </div>
		<!--获取经纪人信息-->
<script>
function chang(type){
 var agency_id=$("select[name='agency_id']").val();
 $.ajax({
	url: "<?php echo MLS_URL;?>/"+type+"/broker_list/",
	type: "GET",
	dataType: "json",
	data:{agency_id: agency_id},
	success:function(data_list){
		var str_html='<option value="0">不限</option>';
		for(var i=0;i<data_list.length;i++){
			str_html +='<option value='+data_list[i].broker_id+'>'+data_list[i].truename+'</option>';
		}
		$("#list_broker").empty().html(str_html);
	}
 });

}
</script>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;" ><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
    </div>
</div>

<!--描述：内容选择项结束-->
<!--描述：主要内容区域开始-->
<div class="table_all">
     <div class="title" id="js_title">
        <table class="table">
            <tr>
                <!--
                <td class="c8"><div class="info">特色</div></td>
                -->
                <td class="c5"><div class="info">物业类型</div></td>
                <td class="c6"><div class="info">区属</div></td>
                <td class="c6"><div class="info">板块</div></td>
                <td class="c12"><div class="info">楼盘</div></td>
                 <td class="c6"><div class="info">户型</div></td>
                <td class="c5">
                	<div class="info">面积<br>(㎡)</div>
                </td>
				<input type="hidden" name='orderby_id' id="orderby_id" value="<?php echo $post_param['orderby_id']?>">
                <td class="c7"><div class="info"><a href="javascript:void(0)" onclick="selllist_order(7);" id="order_price" class="i_text <?php if($post_param['orderby_id'] == 8 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 7){ echo 'i_up'; } ?>">租价<br>
                            (元/月)</a></div>
                </td>
                <td class="c8"><div class="info">经纪人</div></td>
                <!--
                <td class="c5"><div class="info">好评率</div></td>
                <td class="c6"><div class="info">合作成功率</div></td>
                -->
                <td class="c9"><div class="info">发布时间</div></td>
                <td><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div  id="js_inner" class="inner" style="overflow-y: scroll;">
        <table class="table table_q" id="js_table_box_Sincerity">
		<?php
            if($house_list)
            {
                foreach($house_list as $key => $val)
                {
            ?>
        <tr info_id = "<?php echo $val['id'];?>" <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>" date-url="/rent/details_house/<?php echo $val['id'];?>/2">
                <!--
                <td class="c8"><div class="info">
					<?php if($val['pic']){ ?><span title="此房源有图片" class="iconfont ts">&#xe645;</span><?php } ?>
					<?php if($val['rententrust']==1){ ?><span title="独家代理" class="iconfont ts">&#xe646;</span><?php } ?>
					<?php if($val['keys']){ ?><span title="此房源有钥匙" class="iconfont ts ts02">&#xe60d;</span><?php } ?>
					<?php if($val['lock']){ ?><span  title="已被锁定"  class="iconfont ts ts02">&#xe632;</span><?php } ?>
				</div></td>
                -->
                <td class="c5"><div class="info"><?php echo $config['sell_type'][$val['sell_type']]; ?></div></td>
                <td class="c6"><div class="info"><?php echo $district[$val['district_id']]['district']; ?></div></td>
                <td class="c6"><div class="info"><?php echo $street[$val['street_id']]['streetname']; ?></div></td>
                <td class="c12"><div class="info f14 fblod"><?php echo $val['block_name']; ?></div></td>
                <td class="c6"><div class="info fblod"><?php echo $val['room']; ?>-<?php echo $val['hall']; ?>-<?php echo $val['toilet']; ?></div></td>
                <td class="c5"><div class="info"><?php echo strip_end_0($val['buildarea']); ?></div></td>
                <td class="c7"><div class="info f13 fblod"><?php echo strip_end_0($val['price']); ?></div></td>
                <td class="c8 js_info broker" data-brokerId ="<?php echo $val['broker_id'];?>" data_id="<?php echo $val['id'];?>" type="rent_house">
                    <div class="info">
                        <?php echo $val['broker_name']; ?>
                        <!--<span class="onper iconfont">&#xe616;</span>-->
                    </div>
                    <?php if ($check_coop_reulst[$val['id']] == 1) { ?>
                        <a href="###" broker_id="<?= $val['broker_id'] ?>" broker_name="<?= $val['broker_name'] ?>"
                           class="im_a" id="im_icon" title="在线IM">
                            <img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/im.png">
                        </a>
                    <?php } else { ?>
                        <img id="uncooperate" src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/im_out.png">
                    <?php } ?>
                </td>

            <!--
                <td class="c5">
                    <div class="info">
                    <?php
                    if( $house_broker_info[$val['broker_id']]['good_rate'] == '')
                    {
                        $house_broker_info[$val['broker_id']]['good_rate'] = '--';
                    }
                    echo $house_broker_info[$val['broker_id']]['good_rate']."%";
                    ?>
                    </div>
                </td>
                <td class="c6">
                    <div class="info">
                    <?php
                    if(!empty($house_broker_info[$val['broker_id']]['cop_succ_ratio_info']['cop_succ_ratio']) &&
                            $house_broker_info[$val['broker_id']]['cop_succ_ratio_info']['cop_succ_ratio'] > 0)
                    {
                        echo $house_broker_info[$val['broker_id']]['cop_succ_ratio_info']['cop_succ_ratio'].'%';
                    }
                    else if($house_broker_info[$val['broker_id']]['cop_succ_ratio_info']['cop_succ_ratio'] == 0 )
                    {
                        if($house_broker_info[$val['broker_id']]['cop_succ_ratio_info']['cooperate_num'] > 0)
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
                <td class="c9"><div class="info"><?php echo date('Y-m-d H:i',$val['set_share_time']); ?></div></td>
                <td class="js_no_click">
                    <div class="info_p_r">
                    <?php if($check_coop_reulst[$val['id']] == 1){ ?>
                        <a href="javascript:void(0)" title = "已申请" style="color:#b2b2b2;text-decoration:none;">已申请</a>
                    <?php }else if($val['broker_id'] != $broker_id && '1'==$open_cooperate){?>
                        <a href="javascript:void(0)" onclick="cooperate_house('rent',<?php echo $val['id'];?>,<?php echo $val['broker_id'];?>);">合作申请</a>
                    <?php }else if('0'==$open_cooperate){?>
                        <a href="javascript:void(0)" title = "当前公司未开启合作中心" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                    <?php } else {?>
                        <a href="javascript:void(0)" title = "自己不能跟自己合作" style="color:#b2b2b2;text-decoration:none;">合作申请</a>
                    <?php }?>
                    <span style="margin:0 2px;color:#b2b2b2;">|</span><a href="javascript:void(0)" onclick="open_match('rent',2,<?php echo $val['id'];?>)">智能匹配</a><span style="margin:0 2px;color:#b2b2b2;">|</span><a href="javascript:void(0)" class="shcang" onclick="qucang('house_collect','rent_house',<?php echo $val['id']?>)">取消收藏</a>
                </div>
                </td>
            </tr>
           <?php
                }
            }else{
                ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php }?>

          </table>
    </div>
</div>
<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
<div class="get_page">  <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?></div>
</div>
</form>
<!--描述：主要内容区域结束-->
<ul id="openList">
    <input type="hidden" id="right_id" class="js_input">
    <!--右键菜单-->
    <li onClick="openHouseDetails('rent',2);">查看详情</li>
</ul>
<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/loading.gif" id="mainloading" ><!--遮罩 loading-->
<!--房源信息-->
<div id="js_pop_box_cooperation_customer" class="iframePopBox" style=" width:920px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="920" height="540" class='iframePop' src=""></iframe>
</div>

<!--分配房源-->
<div id="js_allocate_house" class="iframePopBox" style=" width:816px; height:340px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="340" class='iframePop' src=""></iframe>
</div>

<!--弹出框列表*ENDING*-->
<!--合作申请弹框-->
<div id="js_pop_box_cooperation_customer" class="iframePopBox" style=" width:920px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="920" height="540" class='iframePop' src=""></iframe>
</div>

<!--详情页弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
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

<!--评价弹框-->
<div id="js_pop_box_appraise1" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--举报信息弹框-->
<div id="js_woyaojubao" class="iframePopBox" style=" width:500px; height:360px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="500" height="360" class='iframePop' src=""></iframe>
</div>

<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>

<!--经纪人信用弹框-->
<div class="broker-info-wrap" id="broker_info_wrap"></div>

    <script>
        function reset() {
            window.location.href = window.location.href;
            window.location.reload;
        }
    </script>
</body>
