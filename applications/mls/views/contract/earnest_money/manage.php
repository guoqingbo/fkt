<script>
    window.parent.addNavClass(21);
</script>
<div class="contract-wrap clearfix">
	<div class="tab-left"><?=$user_tree_menu?></div>
	<div class="forms_scroll2">
		<div class="shop_tab_title scr_clear" id="js_search_box">
            <a href="javascript:void(0);" class="btn-lv fr" onclick="$('#js_edit_pop .iframePop').attr('src','/contract_earnest_money/edit/<?=$post_config['type']?>');openWin('js_edit_pop');">
                <span>+ 新增诚意金</span></a>
            <a href="<?=$post_config['request_url']?>sell/" class="link <?=$post_config['type']=='sell'?'link_on':''?>"><span class="iconfont hide"></span>出售</a>
            <a href="<?=$post_config['request_url']?>rent/" class="link <?=$post_config['type']=='rent'?'link_on':''?>"><span class="iconfont hide"></span>出租</a>
        </div>
		<!-- 上部菜单选项，按钮-->
		<div class="search_box clearfix" id="js_search_box_02" style="overflow:hidden;">
			<form name="search_form" id="subform" method="post" action="">
				<div class="fg_box">
					<p class="fg fg_tex">楼盘名称：</p>
                    <div class="fg">
                        <input type="text" name="block_name" value="<?=$post_param['block_name'];?>" class="input w120 ui-autocomplete-input" autocomplete="off">
                        <input name="block_id" value="<?=$post_param['block_id'];?>" type="hidden">
                    </div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex"></p>
					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select" name="keyword_type">
                            <?php foreach($post_config['keyword_type'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if ($key == $post_param['keyword_type']) {echo 'selected';}?>><?=$val;?></option>
                            <?php }?>
						</select>
					</div>
					<div class="fg">
						<input type="text" name="keyword" id="keyword" value="<?=$post_param['keyword']?>" class="input w90 ui-autocomplete-input" autocomplete="off">
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">收款门店：</p>

					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select" name="payee_agency_id" id="payee_agency_id">
                            <?php
                                if (is_full_array($post_config['agencys'])) {
                                    foreach($post_config['agencys'] as $val){?>
                                        <option value="<?=$val['id'];?>" <?php if ($val['id'] == $post_param['payee_agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
                                <?php }
                            }?>
						</select>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">收款人：</p>
					<div class="fg mr10" style="*padding-top:10px;">
						<select class="select" name="payee_broker_id" id="payee_broker_id">
                            <?php if (is_full_array($post_config['brokers'])) {
                                foreach($post_config['brokers'] as $val){?>
                                <option value="<?=$val['broker_id'];?>" <?php if ($val['broker_id'] == $post_param['payee_broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                                <?php
                                    }
                                }
                            ?>
						</select>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">收款时间：</p>
					<div class="fg">
						<input type="text" class="fg-time" name="start_time" value="<?=$post_param['start_time'];?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"  onchange="check_num();">
					</div>
					<div class="fg fg_tex03">—</div>
					<div class="fg fg_tex03">
					<input type="text" class="fg-time" name="end_time" value="<?=$post_param['end_time'];?>" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})"  onchange="check_num();">
                    <span style="font-weight:bold;color:red;" id="time_reminder"></span>
					</div>
				</div>
				<div class="fg_box">
					<p class="fg fg_tex">状态：</p>
					<div class="fg mr10" style="*padding-top:10px;">
                        <select class="select w80" name="status">
                           <?php foreach($post_config['status'] as $key => $val){?>
                            <option value="<?=$key;?>" <?php if ($key == $post_param['status']) {echo 'selected';}?>><?=$val;?></option>
                            <?php }?>
						</select>
					</div>
				</div>
				<div class="fg_box">
                    <input type="hidden" name="page" value="1">
                    <input type="hidden" name="is_submit" value="1">
					<div class="fg"> <a href="javascript:void(0)" onclick="$('#subform :input[name=page]').val('1');form_submit();return false;" class="btn"><span class="btn_inner">搜索</span></a> </div>
					<div class="fg"> <a href="" class="btn" onclick="$('#subform').attr('action', '/contract_earnest_money/export/<?=$post_config[type]?>/');form_submit();$('#subform').attr('action', '');return false;"><span class="btn_inner">导出</span></a> </div>
					<div class="fg"> <a href="<?=$post_config['request_url'] . $post_config['type']?>/" class="reset">重置</a> </div>
				</div>
			</form>
		</div>
<script>
$(function(){
	document.onkeydown = function(e){ //enter
		var ev = document.all ? window.event : e;
		if(ev.keyCode==13) {
			$('#subform :input[name=page]').val('1');form_submit();return false;
		 }
	}
});
</script>
		<!-- 上部菜单选项，按钮---end-->

		<div class="table_all">
			<div class="title shop_title" id="js_title" style="_padding-right:0;*padding-right:0;">
				<table class="table">
					<tr>
						<td class="c6">房源编号</td>
						<td class="c15">房源地址</td>
						<td class="c6">业主姓名</td>
                        <td class="c10">意向金额<br>(<?php if ($post_config['type'] == 'sell'){echo '万';}?>元)</td>
						<td class="c6">客户姓名</td>
						<td class="c10">诚意金额<br>(元)</td>
						<td class="c10">收款门店</td>
						<td class="c10">收款人</td>
						<td class="c6">收款方式</td>
						<td class="c6">状态</td>
						<td>操作</td>
					</tr>
				</table>
			</div>
			<div class="inner shop_inner" id="js_inner">
				<table class="table ghbs_table">
                <?php if(is_full_array($list)){foreach($list as $key=>$val){?>
					<tr class="bg" date-url="/contract_earnest_money/details/<?=$post_config['type']?>/<?=$val['id']?>/">
						<td class="c6"><div class="info c227ac6" onclick="open_house_details('<?=$post_config[type]?>',<?=substr(trim($val['house_id']),2)?>)">
                        <?=$val['house_id']?></div></td>
						<td class="c15"><div class="info"><?=$val['address']?></div></td>
						<td class="c6"><div class="info"><?=$val['seller_owner']?></div></td>
						<td class="c10"><div class="info"><?=strip_end_0($val['intension_price'])?></div></td>
						<td class="c6"><div class="info"><?=$val['buyer_owner']?></div></td>
						<td class="c10"><div class="info price_l_t"><?=strip_end_0($val['earnest_price'])?></div></td>
                        <td class="c10"><div class="info"><?=$val['agency_name']?></div></td>
						<td class="c10"><div class="info"><?=$val['broker_name']?></div></td>
						<td class="c6"><div class="info"><?=$config['collect_type'][$val['collect_type']]?></div></td>
						<td class="c6"><div class="info"><?=$config['status'][$val['status']]?></div></td>
						<td>
							<a href="javascript:void(0)" onclick="edit('<?=$post_config[type]?>',<?=$val['id']?>);">编辑</a>
                            <span style="margin:0 6px 0 5px;color:#b2b2b2;">|</span>
                            <a href="javascript:void(0)" onclick="$('#earnest_money_id').val(<?=$val['id'];?>);openWin('js_pop_del');">删除</a>
						</td>
					</tr>
                    <?php }}else{?>
                    <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
                    <?php }?>
				</table>
			</div>
		</div>
		<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
            <span style="font-size:12px;color:#565656;float:left;display:inline;">诚意金总计：<b style="color:#ff9d11;font-weight:bold;"><?=strip_end_0($sum['earnest_price'])?></b>元</span>
			<div class="get_page">
                <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
            </div>
		</div>
	</div>
</div>
<script type="text/javascript">
    $(function(){


        //隔行变色
        $(".ghbs_table tr:odd").find("td").css("background","#fcfcfc");

        for(var i =0; i < $(".price_l_t").length; i++){

               if($(".price_l_t").eq(i).html() != " "){

                    if($(".price_l_t").eq(i).html() >999999){

                        $(".price_l_t").eq(i).html("999999.99");
                    }

               }

        }

    })


</script>
<input type="hidden" id="earnest_money_id">
<!--详情页弹窗-->
<div id="js_details_pop" class="iframePopBox" style="width:765px; height:460px;border-left:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="auto" width="765px" height="460px" class='iframePop' src=""></iframe>
</div>
<!--编辑资料弹窗-->
<div id="js_edit_pop" class="iframePopBox" style="width:820px; height:460px;border-left:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="auto" width="840px" height="460px" class='iframePop' src="" name="childIframe"></iframe>
</div>
<!--房源选择弹框-->
<div id="js_house_box" class="iframePopBox" style="width:980px;height:575px;border-left:none;border-bottom:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="980" height="575px" class='iframePop' src=""></iframe>
</div>
<!--删除提示框-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_del">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td class="c14" valign="top"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
                        <td>
			    <p class="left" style="color:#666;">诚意金删除后不可修改，是否确认删除？</p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left JS_Close" type="button" onclick="delete_this();">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
         </div>
    </div>
</div>
<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner" style="width:76%;padding-left:15%;">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button" onclick="window.location.reload(true)">确定</button>
            </div>
         </div>
    </div>
</div>
<!--房源详情弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<script>
    //通过参数判断是否可以被提交
    function form_submit(){
        var is_submit = $("input[name='is_submit']").val();
        if(is_submit ==1){
            $('#subform').submit();
        }
    }

    $(function () {
        function re_width2(){//有表格的时候
            var h1 = $(window).height();
            var w1 = $(window).width() - 170;
            $(".tab-left").height(h1-70);
            $(".forms_scroll2").width(w1);
        };
        re_width2();
        $(window).resize(function(e) {
            re_width2();
        });

        //楼盘名称联想
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
        $("input[name='block_name']").autocomplete({
            source: function( request, response ) {
            var term = request.term;
            $("input[name='block_id']").val("");
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
                $("input[name='block_id']").val(id);
                $("input[name='block_name']").val(blockname);
                removeinput = 2;
                }else{
                removeinput = 1;
                }
            },
            close: function(event) {
                if(typeof(removeinput)=='undefined' || removeinput == 1){
                $("input[name='block_name']").val("");
                $("input[name='block_id']").val("");
                }
            }
        });

        //获取门店下经纪人
        $("#payee_agency_id").change(function(){
            var agency_id = $('#payee_agency_id').val();
            if(agency_id){
                $.ajax({
                    url:"/contract_earnest_money/broker_list/",
                    type:"GET",
                    dataType:"json",
                    data:{
                       agency_id:agency_id
                    },
                    success:function(data){
                        var html = "<option value='0'>请选择</option>";
                        if(data['result'] == 1){
                            for(var i in data['list']){
                                html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
                            }
                        }
                        $('#payee_broker_id').html(html);
                    }
                });
            } else {
                $('#payee_broker_id').html("<option value='0'>请选择</option>");
            }
        });
    });
    function open_house(type,house_id)
    {
        $('#js_house_box .iframePop').attr('src','/contract/get_house/' + type+'/'+house_id);
        openWin('js_house_box');
    }

    function get_info(id){
        $("#js_house_box").hide();
	    $("#GTipsCoverjs_house_box").remove();
        if(id){
            $.post(
                '/contract/get_info',
                {'id':id,
                'type':<?=$trade_type?>
                },
                    function(data){
                        childIframe.window.document.getElementById("house_id").value = data['house_id'];
                        childIframe.window.document.getElementById("block_name").value = data['block_name'];
                        childIframe.window.document.getElementById("block_id").value = data['block_id'];
                        childIframe.window.document.getElementById("address").value = data['address']+data['dong']+'栋'+data['unit']+'单元'+data['door']+'室';
                        childIframe.window.document.getElementById("sell_type").value = data['sell_type'];
                        childIframe.window.document.getElementById("seller_owner").value = data['owner'];
                        childIframe.window.document.getElementById("seller_telno").value = data['telno1'];
                        childIframe.window.document.getElementById("seller_idcard").value = data['idcare'];
                        childIframe.window.document.getElementById('house_id').disabled = true;
                        childIframe.window.document.getElementById('block_name').disabled = true;
                        childIframe.window.document.getElementById('address').disabled = true;
                        childIframe.window.document.getElementById("sell_type").disabled = true;
                        childIframe.window.document.getElementById("seller_owner").disabled = true;
                        childIframe.window.document.getElementById("seller_telno").disabled = true;
                        childIframe.window.document.getElementById("seller_idcard").disabled = true;
                        },'json'
                );
        }else{
            childIframe.window.document.getElementById("house_id").value = '';
            childIframe.window.document.getElementById("block_name").value = '';
            childIframe.window.document.getElementById("block_id").value = '';
            childIframe.window.document.getElementById("address").value = '';
            childIframe.window.document.getElementById("sell_type").value = '';
            childIframe.window.document.getElementById("seller_owner").value = '';
            childIframe.window.document.getElementById("seller_telno").value = '';
            childIframe.window.document.getElementById("seller_idcard").value = '';
            childIframe.window.document.getElementById('house_id').disabled = false;
            childIframe.window.document.getElementById('block_name').disabled = false;
            childIframe.window.document.getElementById('address').disabled = false;
            childIframe.window.document.getElementById("sell_type").disabled = false;
            childIframe.window.document.getElementById("seller_owner").disabled = false;
            childIframe.window.document.getElementById("seller_telno").disabled = false;
            childIframe.window.document.getElementById("seller_idcard").disabled = false;
        }
    }

    //删除该条诚意金
    function delete_this(){
        var earnest_money_id = $('#earnest_money_id').val();
        $.ajax({
            url:"/contract_earnest_money/del/",
            type:"GET",
            dataType:"json",
            data:{
                id:earnest_money_id
            },
            success:function(data){
                if (data['errorCode'] == '403') { //无权限
                    permission_none();
                }
                else if(data['result'] == 1){
                    $('#js_prompt').text('诚意金已删除！');
                    openWin('js_pop_success');
                }else{
                    $('#js_prompt').text('诚意金删除失败！');
                    openWin('js_pop_success');
                }
            }
        });
    }

    function check_num()
    {
        var timemin    =    $("input[name='start_time']").val();	//最小面积
        var timemax    =    $("input[name='end_time']").val();	//最大面积

        if(!timemin && !timemax){
            $("#time_reminder").html("");
            $("input[name='is_submit']").val('1');
        }

        //最小面积timemin 必须小于 最大面积timemax
        if(timemin && timemax){
            if(timemin>timemax){
                $("#time_reminder").html("时间筛选区间输入有误！");
                $("input[name='is_submit']").val('0');
                return;
            }else{
                $("#time_reminder").html("");
                $("input[name='is_submit']").val('1');
            }
        }
    }

    //打开房源详情弹层
    function open_house_details(type,id)
    {
        var _id = parseInt(id);
        if (id == 0)
        {
            return false;
        }
        var _url = '/' + type + '/details/' + id + '/4/';

        if(_url)
        {
             $("#js_pop_box_g .iframePop").attr("src",_url);
        }
        openWin('js_pop_box_g');
    }

    function edit(type, id)
    {
        $('#js_edit_pop .iframePop').attr('src','/contract_earnest_money/edit/<?=$post_config[type]?>/' + id);
        openWin('js_edit_pop');
    }

    $(".table_all .inner tr").each(function(index, element) {
        var _url = $(this).attr("date-url");
        $(this).find("td:gt(0)").on("click",function(event){
            if(!$(this).hasClass("js_no_click"))
            {
                if(_url)
                {
                    $("#js_details_pop .iframePop").attr("src",_url);
                    openWin('js_details_pop');
                }
                event.stopPropagation();
            }
            else{
                event.stopPropagation();
            }
        });
    });

    $('.table_all .inner tr').find("a").click(function(event){
		event.stopPropagation();
	});

    function pop_hide(){
        $('#GTipsCoverjs_house_box').remove();
        $('#js_house_box').hide();
    }
</script>
