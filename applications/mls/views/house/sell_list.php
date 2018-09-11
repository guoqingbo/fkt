<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/js/v1.0/json2.js" type="text/javascript"></script>
<script>
    window.parent.addNavClass(2);
</script>
<style>
    .house-follow{
        background: #227ac6;color: #fff;padding: 4px 6px;border: none;border-radius: 4px;
    }
</style>

<form method='post' action='' id='search_form' name="search_form">
    <div class="tab_box" id="js_tab_box">
        <?php echo $user_menu;?>
        <label class="label_left_t">
            <input type="checkbox" onclick="search_form.submit();return false;" value="1" name="is_public" id="is_public" <?php if($post_param['is_public']==1){echo "checked='checked'";}?>>公共房源
        </label>
        <?php /*if($imadmin == 1 || (31==$city_id && 13186==$broker_id)){ */?><!--
        <script>
        function nextexport()
        {
            var mylimit = parseInt($('#mylimit').val());
            var myoffset = parseInt($('#myoffset').val());

            mylimit = myoffset + mylimit;

            $('#mylimit').val(mylimit);
        }
        </script>
        <span style="float:right;">从第<input type="text"  class="input w40" id="mylimit" name="mylimit" value="<?php /*echo $mylimit; */?>" />条房源开始导出，每次导出<input type="text" class="input w40" id="myoffset" name="myoffset" value="<?php /*echo $myoffset; */?>" />条<a href="###" onclick="nextexport();">下一组</a></span>
        <a onclick="sub_export_btn_2();" class="add_link">导出房源</a>
        --><?php /*} */?>
        <script>
            function nextexport()
            {
                var mylimit = parseInt($('#mylimit').val());
                var myoffset = parseInt($('#myoffset').val());

                mylimit = myoffset + mylimit;

                $('#mylimit').val(mylimit);
            }
        </script>
        <!-- <span style="float:right;">从第<input type="text"  class="input w40" id="mylimit" name="mylimit" value="<?php echo $mylimit; ?>" />条房源开始导出，每次导出<input type="text" class="input w40" id="myoffset" name="myoffset" value="<?php echo $myoffset; ?>" />条<a href="###" onclick="nextexport();">下一组</a></span> -->
        <!--        <a onclick="sub_export_btn_2();" class="add_link">导出房源</a>-->
        <a class="add_link" id="import">导入房源</a>
        <a href="/sell/publish/" class="add_link"><span class="iconfont">&#xe608;</span>录入房源</a>
    </div>
    <div class="search_box clearfix" id="js_search_box"> <a href="javascript:void(0);" class="s_h" onClick="show_hide_info(this , 'sell_list_extend')" data-h="0" id="extend">更多<span class="iconfont">&#xe609;</span></a>
        <div class="fg_box">
            <p class="fg fg_tex">区属：</p>
            <div class="fg">
                <select class="select" id='district' name='district' onchange="districtchange(this.value);">
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
                <input type="text" name="block_name" <?php if('1'==$is_property_publish){ ?>id="block_name"<?php } ?> value="<?php echo $post_param['block_name']; ?>" class="input w90">
                <input name="block_id" id="block_id" value="<?php echo $post_param['block_id']?>" type="hidden">
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
                $("#block_name").autocomplete({
                    source: function( request, response ) {
                        var term = request.term;
                        $("#block_id").val("");
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
                            $("#block_id").val(id);
                            $("#block_name").val(blockname);
                            removeinput = 2;
                        }else{
                            removeinput = 1;
                        }
                    },
                    close: function(event) {
                        if(typeof(removeinput)=='undefined' || removeinput == 1){
                            $("#block_name").val("");
                            $("#block_id").val("");
                        }
                    }
                });
            });
            </script>
        <div class="fg_box">
            <p class="fg fg_tex"> 面积：</p>
            <div class="fg">
                <input type="text" name='areamin' id="areamin" onblur="check_num()"  value="<?php echo $post_param['areamin']; ?>" class="input w30">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input type="text" name='areamax' id="areamax" onblur="check_num()"  value="<?php echo $post_param['areamax']; ?>" class="input w30">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="areamin_reminder"></span>
            </div>
            <p class="fg fg_tex fg_tex03">平米</p>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex"> 总价：</p>
            <div class="fg">
                <input type="text" name='pricemin' id="pricemin" onblur="check_num()"  value="<?php echo $post_param['pricemin']; ?>" class="input w30">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input type="text" name='pricemax' id="pricemax" onblur="check_num()"  value="<?php echo $post_param['pricemax']; ?>" class="input w30">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="pricemin_reminder"></span>
            </div>
            <p class="fg fg_tex fg_tex03">万元</p>
        </div>

        <?php if(is_int($company_id) && $company_id>0){?>
        <div class="fg_box">
            <?php if($view_other_per){?>
				        <p class="fg fg_tex"> 范围：</p>
                <div class="fg">
                  <select class="select" name="post_agency_id" onchange="chang('sell')">
                    <option selected value='0'>不限</option>
                    <?php if($agency_list){
                      foreach($agency_list as $key=>$val){ ?>
                        <option <?php if ($val['agency_id'] == $post_param['post_agency_id']) echo "selected"; ?>
                                value="<?php echo $val['agency_id']; ?>"><?php echo $val['agency_name']; ?>
                        </option>
                    <?php }}?>
                  </select>
                </div>
                <div class="fg fg_tex fg_tex03" >
                  <select class="select" name="post_broker_id" id="list_broker">
                    <option value='0'>不限</option>
                    <?php if($broker_list){ ?>
                    <?php foreach($broker_list as $key=>$val){ ?>
                    <option  <?php if($val['broker_id'] == $post_param['post_broker_id']) echo "selected"; ?> value='<?php echo $val['broker_id']?>'><?php echo $val['truename']?></option>
                    <?php }}?>
                  </select>
                </div>
            <?php }else{?>
                <p class="fg fg_tex"> 范围：</p>
                <div class="fg">
                  <select class="select" name="post_agency_id">
                    <option value="<?php echo $agency_id?>"><?php echo $agency_name?></option>
                  </select>
                </div>
			 	        <div class="fg fg_tex fg_tex03" >
                  <select class="select"name="post_broker_id">
                    <option value="<?php echo $broker_id;?>"><?php echo $truename; ?></option>
                  </select>
                </div>
            <?php }?>
        </div>
        <?php }else{?>
            <?php if(!empty($register_info['corpname']) && !empty($register_info['storename'])){?>
                <div class="fg_box">
                  <p class="fg fg_tex"> 范围：</p>
                  <div class="fg">
                    <select class="select">
                      <option><?php echo $register_info['corpname'];?></option>
                    </select>
                  </div>
                  <div class="fg fg_tex fg_tex03" >
                    <select class="select">
                      <option><?php echo $register_info['storename'];?></option>
                    </select>
                  </div>
                </div>

            <?php }?>
        <?php }?>

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
        if(agency_id>0 || agency_id==0){
            for(var i=0;i<data_list.length;i++){
                str_html +='<option value='+data_list[i].broker_id+'>'+data_list[i].truename+'</option>';
            }
        }
		$("#list_broker").empty().html(str_html);
	}
 });

}
</script>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 录入时间：</p>
            <div class="fg">
                <select class="select" name='create_time_range'>
                    <option value='0'>不限</option>
                    <?php
                        foreach($config['create_time_range'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'" ';
                            if($key == $post_param['create_time_range'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
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
            <p class="fg fg_tex"> 楼层：</p>
            <div class="fg"  style="padding-right:10px;">
                <select class="select" name='story_type'>
                    <option value='0'>不限</option>
                    <?php
                        foreach($config['story_type'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'" ';
                            if($key == $post_param['story_type'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="fg">
                <input type="text" name='floormin' id="floormin" onblur="check_num()"  value="<?php echo $post_param['floormin']; ?>" class="input w30">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input type="text" name='floormax' id="floormax" onblur="check_num()"  value="<?php echo $post_param['floormax']; ?>" class="input w30">&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="floormin_reminder"></span>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 栋座：</p>
            <div class="fg">
                <input type="text" name='dong' value="<?php echo $post_param['dong']; ?>" class="input w30">
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 单元：</p>
            <div class="fg">
                <input type="text" name='unit' value="<?php echo $post_param['unit']; ?>" class="input w30">
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 门牌：</p>
            <div class="fg">
                <input type="text" name='door' value="<?php echo $post_param['door']; ?>" class="input w30">
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 性质：</p>
            <div class="fg">
                <select class="select" name='nature'>
                    <option>不限</option>
                    <?php
                        foreach($config['nature'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'"';
                            if($key == $post_param['nature'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
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
        <div class="fg_box hide">
            <p class="fg fg_tex"> 装修：</p>
            <div class="fg">
                <select class="select" name='fitment'>
                    <option value='0'>不限</option>
                    <?php
                        foreach($config['fitment'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'"';
                            if($key == $post_param['fitment'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 朝向：</p>
            <div class="fg">
                <select class="select" name='forward'>
                    <option value='0'>不限</option>
                    <?php
                        foreach($config['forward'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'"';
                            if($key == $post_param['forward'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 钥匙：</p>
            <div class="fg">
                <select class="select" name='keys'>
                    <option value='0'>不限</option>
                    <?php
                        arsort($config['keys']);
                        foreach($config['keys'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'"';
                            if($key == $post_param['keys'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 委托类型：</p>
            <div class="fg">
                <select class="select" name='entrust'>
                    <option value='0'>不限</option>
                    <?php
                        foreach($config['entrust'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'"';
                            if($key == $post_param['entrust'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 是否合作：</p>
            <div class="fg">
                <select class="select" name='isshare'>
                    <option value='' <?php if(isset($post_param['isshare'])){echo "selected";}?>>不限</option>
                    <option value='1' <?php if($post_param['isshare'] == 1){echo "selected";}?>>是</option>
                    <option value='0' <?php if($post_param['isshare'] === '0'){echo "selected";}?>>否</option>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 已同步：</p>
            <div class="fg">
                <select class="select" name='is_outside'>
                    <option value='' <?php if(isset($post_param['is_outside'])){echo "selected";}?>>不限</option>
                    <option value='1' <?php if($post_param['is_outside'] == '1'){echo "selected";}?>>是</option>
                    <option value='0' <?php if($post_param['is_outside'] === '0'){echo "selected";}?>>否</option>
                </select>
            </div>
        </div>
		<div class="fg_box hide">
            <p class="fg fg_tex"> 是否发布到朋友圈：</p>
            <div class="fg">
                <select class="select" name='isshare_friend'>
                    <option value='' <?php if(isset($post_param['isshare_friend'])){echo "selected";}?>>不限</option>
                    <option value='1' <?php if($post_param['isshare_friend'] == 1){echo "selected";}?>>是</option>
                    <option value='0' <?php if($post_param['isshare_friend'] === '0'){echo "selected";}?>>否</option>
                </select>
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 业主电话：</p>
            <div class="fg">
                <input type="text" name='telno' value="<?php echo $post_param['telno']; ?>" class="input w80">
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 房源编号：</p>
            <div class="fg">
                <input type="text" name='house_id' value="<?php echo $post_param['house_id']; ?>" class="input w60">
            </div>
        </div>
        <div class="fg_box hide">
            <p class="fg fg_tex"> 标签：</p>
            <div class="fg">
                <select class="select" name='sell_tag'>
                    <option value='0'>不限</option>
                    <?php
                        foreach($config['sell_tag'] as $key =>$val)
                        {
                            echo '<option value="'.$key.'"';
                            if($key == $post_param['sell_tag'])
                                echo "selected";
                            echo '> '.$val.'</option>';
                        }
                    ?>                    
                </select>
            </div>
        </div>        
        <div class="fg_box">
            <input type="hidden" name='orderby_id' id="orderby_id" value="<?php echo $post_param['orderby_id']?>">
            <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;"><span  class="btn_inner">搜索</span></a> </div>
            <div class="fg"> <a href="javascript:void(0)" class="reset" onclick='del_cookie();'>重置</a> </div>        </div>
    </div>

<script type="text/javascript">
//顶部遮罩显示
function show_noneClick(){
    $(window.parent.document).find("#noneClick").show();
    $(window.parent.document).find("#noneClick2").show();
}
//顶部遮罩隐藏
function hide_noneClick(){
    $(window.parent.document).find("#noneClick").hide();
    $(window.parent.document).find("#noneClick2").hide();
}

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
            'is_pub':0
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
            'is_pub':0
        }
    });
}

$(function(){
    //保密与跟进进程，判断是否直接弹出弹框
    var alert_house_id = <?php echo $alert_house_id; ?>;
    if(alert_house_id > 0){
        var alert_url = '/sell/details_house/'+alert_house_id+'/1/2/0/0';
        $("#js_pop_box_g .iframePop").attr("src",alert_url);
        openWin('js_pop_box_g');
        //详情弹框最小化、关闭按钮不能点；
        $("#window_min_click").attr('class','close_pop iconfont');
        $("#window_min_close").attr('class','close_pop iconfont');
        $("#window_min_close").attr("id",'window_min_close_2');
        $("#window_min_click").attr("id",'window_min_click_2');
        //外部添加遮罩
        show_noneClick();
    }

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
            window_min_html += '<span  title=""  class="iconfont zws_bottom_span_close">&#xe62c;</span>';
            window_min_html += '</li>';
            $('#window_min').append(window_min_html);
            var num = $('#window_min').children().size();
            $('#window_min').css('width',210*num+"px");



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
         totalNumLi = ($(".zws_bottom_nav_dao li").length);
        //当前标签处理
		//titleShowBj();
		//弹出内容
		//detialShow();
		//切换箭头显示与隐藏
	//	tabShow();
		//alert(totalNumLi);

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
				//alert(totalNumLi);
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

				totalNumLi = ($(".zws_bottom_nav_dao li").length);
				//alert(totalNumLi);
				$(this).parent("li").remove();
				//UlLength(aObjUl, aObjLl);
				tabShow();
				//操作日志数据
				log_data_del();
			})
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

        //导入房源
		$('#import').click(function(){
            var group_id = <?php echo $group_id;?>;
            if('1'==group_id){
                 $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                 openWin('js_pop_do_warning');
                 return false;
            }
            openn_import('sell');
        });
        //群发房源
		$('#qunfa_openlist').click(function(){
            var group_id = <?php echo $group_id;?>;
            if('1'==group_id){
                 $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                 openWin('js_pop_do_warning');
                 return false;
            }
            publish_before('sell');
        });
        //房源跟进
		$('#follow_openlist').click(function(){
            var group_id = <?php echo $group_id;?>;
            if('1'==group_id){
                 $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                 openWin('js_pop_do_warning');
                 return false;
            }
            open_follow('sell',1);
        });
        //操作中的房源跟进
        $('.house-follow').click(function (e) {
            //js阻止事件冒泡
            e.preventDefault();
            e.stopPropagation();
            var house_id = $(this).attr('houseId');
            var group_id = <?php echo $group_id;?>;
            if ('1' == group_id) {
                $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                openWin('js_pop_do_warning');
                return false;
            }
            $('#right_id').val(house_id);
            open_follow('sell', 1);
        });
        //房源打印
		$('#dayin_openlist').click(function(){
            var group_id = <?php echo $group_id;?>;
            if('1'==group_id){
                 $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                 openWin('js_pop_do_warning');
                 return false;
            }
            house_print('sell');
        });
        //智能匹配
		$('#match_openlist').click(function(){
            var group_id = <?php echo $group_id;?>;
            if('1'==group_id){
                 $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                 openWin('js_pop_do_warning');
                 return false;
            }
            openMatch('sell',0);
        });
        //分配任务
		$('#task_openlist').click(function(){
            var group_id = <?php echo $group_id;?>;
            if('1'==group_id){
                 $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                 openWin('js_pop_do_warning');
                 return false;
            }
            ringt_tasks('sell',1);
        });
        //分配房源
		$('#fenpei_openlist').click(function(){
            var group_id = <?php echo $group_id;?>;
            if('1'==group_id){
                 $("#dialog_do_warnig_tip").html("您的帐号尚未认证");
                 openWin('js_pop_do_warning');
                 return false;
            }
            allocate_house('sell');
        });

});

</script>
<div class="table_all">
    <div class="title" id="js_title">
        <table class="table">
             <tr>
                <td class="c3">
                    <div class="info">

                    </div>
                </td>
                <?php if(in_array(1, $sell_house_field_arr)){ ?>
                <td class="c5"><div class="info">标签</div></td>
                <?php } ?>
                <?php if(in_array(24, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info">编号</div></td>
                <?php } ?>
                <?php if(in_array(2, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info">状态</div></td>
                <?php } ?>
                <?php if(in_array(3, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info">性质</div></td>
                <?php } ?>
                <?php if(in_array(4, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info">合作</div></td>
                <?php } ?>
                <?php if(in_array(5, $sell_house_field_arr)){ ?>
                <td class="c4"><div class="info">区属</div></td>
                <?php } ?>
                <?php if(in_array(6, $sell_house_field_arr)){ ?>
                <td class="c6"><div class="info">板块</div></td>
                <?php }?>
                <?php if(in_array(7, $sell_house_field_arr)){ ?>
                <td class="c10"><div class="info">楼盘</div></td>
                <?php }?>
                <?php if(in_array(8, $sell_house_field_arr)){ ?>
                <td class="c5"><div class="info">物业类型</div></td>
                <?php }?>
                <?php if(in_array(9, $sell_house_field_arr)){ ?>
                <td class="c4"><div class="info"><a href="javascript:void(0)" onclick="selllist_order(5);return false;" id="order_area" class="i_text <?php if($post_param['orderby_id'] == 6 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 5){ echo 'i_up'; } ?>">面积<br>
                        (㎡)</a></div></td>
                <?php }?>
                <?php if(in_array(10, $sell_house_field_arr)){ ?>
                <td class="c5"><div class="info"><a href="javascript:void(0)" onclick="selllist_order(7);return false;" id="order_price" class="i_text <?php if($post_param['orderby_id'] == 8 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 7){ echo 'i_up'; } ?>">总价<br>
                         (W)</a></div></td>
                <?php }?>
                <?php if(in_array(11, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info">栋座</div></td>
                <?php }?>
                <?php if(in_array(12, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info">单元</div></td>
                <?php } ?>
                <?php if(in_array(13, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info">门牌</div></td>
                <?php }?>
                <?php if(in_array(14, $sell_house_field_arr)){ ?>
                <td class="c5"><div class="info">户型</div></td>
                <?php } ?>
                <?php if(in_array(15, $sell_house_field_arr)){ ?>
                <td class="c5"><div class="info"><a href="javascript:void(0)" onclick="selllist_order(24);return false;" id="order_floor" class="i_text <?php if($post_param['orderby_id'] == 25 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 24){ echo 'i_up'; } ?>">楼层<br>
                <?php }?>
                <?php if(in_array(16, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info">朝向</div></td>
                <?php }?>
                <?php if(in_array(17, $sell_house_field_arr)){ ?>
                <td class="c4"><div class="info"><a href="javascript:void(0)" onclick="selllist_order(26);return false;" id="order_area" class="i_text <?php if($post_param['orderby_id'] == 27 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 26){ echo 'i_up'; } ?>">单价</a></div></div></td>
                <?php } ?>
                <?php if(in_array(18, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info">税费</div></td>
                <?php } ?>
                <?php if(in_array(22, $sell_house_field_arr)){ ?>
                <td class="c4"><div class="info">车库<br>(㎡)</div></td>
                <?php } ?>
                <?php if(in_array(19, $sell_house_field_arr)){ ?>
                <td class="c4"><div class="info">装修</div></td>
                <?php } ?>
                <?php if(in_array(23, $sell_house_field_arr)){ ?>
                <td class="c6">
                    <div class="info">
                        <a href="javascript:void(0)" onclick="selllist_order(28);return false;" id="order_genjin" class="i_text <?php if($post_param['orderby_id'] == 29 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 28){ echo 'i_up'; } ?>">登记时间<br></a>
                    </div>
                </td>
                <?php } ?>
                <?php if(in_array(20, $sell_house_field_arr)){ ?>
                <td class="c6">
                    <div class="info">
                        <a href="javascript:void(0)" onclick="selllist_order(11);return false;" id="order_genjin" class="i_text <?php if($post_param['orderby_id'] == 12 ){ echo 'i_down'; }elseif($post_param['orderby_id'] == 11){ echo 'i_up'; } ?>">跟进时间<br></a>
                    </div>
                </td>
                <?php } ?>
                <?php if(in_array(21, $sell_house_field_arr)){ ?>
                <td class="c6"><div class="info">经纪人</div></td>
                <?php } ?> 
                <td class="c6"><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner" id="js_innerHouse" style="height:371px">
        <table class="table table_q">
            <input type="hidden" value="<?php echo $group_id?>" id="group_id">
            <input type="hidden" value="<?php echo $is_join_district ?>" id="is_join_district">
            <?php
            if($list)
            {
                foreach($list as $key => $val)
                {
                    //获得房源的创建天数
                    $real_time = $sell_house_check_day*24*3600 + $val['createtime'];
                    if($real_time > time()){
                        $is_check_day = 1;
                    }else{
                        $is_check_day = 0;
                    }
                    //红色警告（房源跟进信息无堪房）
                    if(in_array($val['id'], $follow_red_house_id)){
                        $tag_follow_red = 1;
                    }else{
                        $tag_follow_red = 0;
                    }

                    $tag_class2 = 'zws-red';
                    $tag_str = '该房源自登记以来超过'.$sell_house_check_time.'天未勘察';
                    if(1===$tag_follow_red || 1===$is_check_day){
                        if(in_array($val['id'] , $follow_green_house_id)){
                            $tag_class2 = 'zws-green';
                            $tag_str = '该房源距离上一次跟进已超'.$sell_house_follow_last_time1.'天';
                        }else{
                            if(in_array($val['id'] , $follow_zi_house_id)){
                                $tag_class2 = 'zws-zi';
                                $tag_str = '该房源距离上一次跟进已超'.$sell_house_follow_last_time2.'天';
                            }else{
                                if(in_array($val['id'] , $yellow_house_id)){
                                    $tag_class2 = 'zws-yellow2';
                                    $tag_str = '该房源两次跟进间隔已超过'.$house_follow_spacing_time.'天';
                                }else{
                                    $tag_class2 = '';
                                    $tag_str = '';
                                }
                            }
                        }
                    }
					//当房源又是紧急又有提醒的话，只展示提醒（仅限自己的房源）
                    if($broker_id==intval($val['broker_id'])){
                        if(in_array($val['id'], $remind_house_id)){
                            $tag_remind = 1;
                        }else{
                            $tag_remind = 0;
                        }
                    }else{
                            $tag_remind = 0;
                    }
                    if(1 == $tag_remind){
                        $tag_class = 'bg-yellow';
                    }else{
                        if($val['house_grade'] == 3){
                            $tag_class = 'bg-red';
                        }else{
                            $tag_class = '';
                        }
                    }
					$tdclass = 1 == $key % 2 ? 'bg' : '';

					$tdclass = $tdclass . ' ' . $tag_class;
            ?>
            <tr <?php if(!empty($tag_str)){ ?>title="<?php echo $tag_str; ?>"<?php } ?> <?php if('' != $tdclass){ ?>class="<?php echo $tdclass; ?>"<?php } ?> id="tr<?php echo $val['id'];?>" date-url="/sell/details_house/<?php echo $val['id'];?>/1" controller="sell" _id="<?php echo $val['id'];?>" min_title="<?php echo $val['block_name'].'-'.intval($val['price']).'万'.'-'.intval($val['buildarea']).'平米'; ?>" page_id="<?php echo $key+1; ?>">
                <td class="c3">
                    <div class="info">
						<input type="checkbox" name="items" value="<?php echo $val['id'];?>" class="checkbox" style="display:none;">
						<?php if(1 == $tag_remind){ ?>
                            <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/tip.png">
                        <?php }else{
                            if($val['house_grade'] == 3 ){
                        ?>
                            <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/hot.png">
                        <?php }else if($val['house_grade'] == 2){
                        ?>
                            <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/tuijian.png">
                        <?php
                        }
                            }?>
                    </div>
                </td>
                <?php if(in_array(1, $sell_house_field_arr)){ ?>
                <td class="c5">
                    <div class="info">
                        <?php if($val['pic']){ ?><span title="此房源有图片" class="iconfont ts">&#xe645;</span><?php } ?>
                        <?php if($val['entrust']==1){ ?><span title="独家代理" class="iconfont ts">&#xe646;</span><?php } ?>
                        <?php if($val['keys']){ ?><span title="此房源有钥匙" class="iconfont ts ts02">&#xe60d;</span><?php } ?>
                        <?php if($val['lock']){ ?><span  title="已被锁定"  class="iconfont ts ts02">&#xe632;</span><?php } ?>
                        <?php if(!empty($val['video_id'])){ ?><span  title="此房源有视频"  class="iconfont ts ts02">&#xe65e;</span><?php } ?>
                        <span title="此房源已同步" class="iconfont ts" id="fang100<?=$val['id']?>" style="display:<?=($val['is_outside']=='1')?"inline":"none"?>">&#xe656;</span>
                    </div>
                </td>
                <?php } ?>
                <?php if(in_array(24, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info">CS<?php echo $val['id']; ?></div></td>
                <?php } ?>
                <?php if(in_array(2, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info"><?php echo $config['status'][$val['status']]; ?></div></td>
                <?php } ?>
                <?php if(in_array(3, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info" id="nature<?php echo $val['id'];?>"><?php echo $config['nature'][$val['nature']]; ?></div></td>
                <?php } ?>
                <?php if(in_array(4, $sell_house_field_arr)){ ?>
                <td class="c3">
                    <div class="info" id="share<?php echo $val['id'];?>">
                    <?php if($val['isshare']==1){?>
                        是
                    <?php }else if($val['isshare']==2){?>
                        店长审核中
                    <?php }else if($val['isshare']==3){ ?>
                        资料审核中
                    <?php }else{ ?>
                        否
                    <?php } ?>
                    </div>
				<input type="hidden" value="<?php echo $val['isshare']?>" id="share_num<?php echo $val['id']?>">
				<input type="hidden" value="<?php echo $val['is_report']?>" id="is_report<?php echo $val['id']?>">
				<input type="hidden" value="<?php echo $val['status']?>" id="status<?php echo $val['id']?>">
                    <input type="hidden" value="<?php echo $val['block_id'] ?>" id="block_id<?php echo $val['id'] ?>">

                    <input type="hidden" value="<?php echo $val['house_in_district'] ?>"
                           id="house_in_district<?php echo $val['id'] ?>">
                    <input type="hidden" value="<?php echo $val['is_have_certificate'] ?>"
                           id="is_have_certificate<?php echo $val['id'] ?>">
                    <input type="hidden" value="<?php echo $val['is_send_district'] ?>"
                           id="is_send_district<?php echo $val['id'] ?>">

                </td>
                <?php } ?>
                <?php if(in_array(5, $sell_house_field_arr)){ ?>
                <td class="c4"><div class="info"><?php echo $district[$val['district_id']]['district']; ?></div></td>
                <?php } ?>
                <?php if(in_array(6, $sell_house_field_arr)){ ?>
                <td class="c6"><div class="info"><?php echo $street[$val['street_id']]['streetname']; ?></div></td>
                <?php } ?>
                <?php if(in_array(7, $sell_house_field_arr)){ ?>
                <td class="c10"><div class="info f14 fblod <?php echo $tag_class2; ?>"><?php echo $val['block_name']; ?></div></td>
                <?php } ?>
                <?php if(in_array(8, $sell_house_field_arr)){ ?>
                <td class="c4"><div class="info"><?php echo $config['sell_type'][$val['sell_type']]; ?></div></td>
                <?php } ?>
                <?php if(in_array(9, $sell_house_field_arr)){ ?>
                <td class="c5"><div class="info f60 f13 fblod"><?php echo strip_end_0($val['buildarea']); ?></div></td>
                <?php } ?>
                <?php if(in_array(10, $sell_house_field_arr)){ ?>
                <td class="c5">
					<div class="info f60 f13 fblod">
					<?php echo strip_end_0($val['price']); ?>
					<?php if($val['price_change'] == 1){
						echo "<img class='price_img' src='".MLS_SOURCE_URL."/mls/images/v1.0/price_up.png'>";
					}elseif($val['price_change'] == 2){
						echo "<img class='price_img' src='".MLS_SOURCE_URL."/mls/images/v1.0/price_down.png'>";
					}?>
					</div>
				</td>
                <?php } ?>
                <?php if(in_array(11, $sell_house_field_arr)){ ?>
                <td class="c3">
                    <div class="info">
                        <?php
                        if ($broker_id == $val['broker_id']) {
                            echo $val['dong'];
                        } else {
                            if ('1' == $val['is_secrecy_information']) {
                                echo '*';
                            } else {
                                echo $val['dong'];
                            }
                        }
                        ?>
                    </div>
                </td>
                <?php } ?>
                <?php if(in_array(12, $sell_house_field_arr)){ ?>
                <td class="c3">
                    <div class="info">
                        <?php
                        if ($broker_id == $val['broker_id']) {
                            echo $val['unit'];
                        } else {
                            if ('1' == $val['is_secrecy_information']) {
                                echo '*';
                            } else {
                                echo $val['unit'];
                            }
                        }
                        ?>
                    </div>
                </td>
                <?php } ?>
                <?php if(in_array(13, $sell_house_field_arr)){ ?>
                <td class="c3">
                    <div class="info">
                        <?php
                        if ($broker_id == $val['broker_id']) {
                            echo $val['door'];
                        } else {
                            if ('1' == $val['is_secrecy_information']) {
                                echo '*';
                            } else {
                                echo $val['door'];
                            }
                        }
                        ?>
                    </div>
                </td>
                <?php } ?>
                <?php if(in_array(14, $sell_house_field_arr)){ ?>
                <td class="c5"><div class="info fblod"><?php echo $val['room']; ?>-<?php echo $val['hall']; ?>-<?php echo $val['toilet']; ?></div></td>
                <?php } ?>
                <?php if(in_array(15, $sell_house_field_arr)){ ?>
                <td class="c5"><div class="info fblod"><?php echo $val['floor']; ?><?php if($val['floor_type']==2){ echo "-".$val['subfloor'];}?>/<?php echo $val['totalfloor']; ?></div></td>
                <?php } ?>
                <?php if(in_array(16, $sell_house_field_arr)){ ?>
                <td class="c3"><div class="info"><?php echo $config['forward'][$val['forward']]; ?></div></td>
                <?php } ?>
                <?php if(in_array(17, $sell_house_field_arr)){ ?>
                <td class="c4"><div class="info"><?php echo intval($val['avgprice']);?>元/平米</div></td>
                <?php } ?>
                <?php if(in_array(18, $sell_house_field_arr)){ ?>
                <td class="c2"><div class="info"><?php echo $config['taxes'][$val['taxes']]; ?></div></td>
                <?php } ?>
                <?php if(in_array(22, $sell_house_field_arr)){ ?>
                <td class="c4"><div class="info"><?php echo $val['garage_area'] >0?$val['garage_area']:0; ?></div></td>
                <?php } ?>
                <?php if(in_array(19, $sell_house_field_arr)){ ?>
                <td class="c4"><div class="info"><?php echo $config['fitment'][$val['fitment']]; ?></div></td>
                <?php } ?>
                <?php if(in_array(23, $sell_house_field_arr)){ ?>
                <td class="c6">
                    <div class="info info_p_r zws_line16"><?php echo date('Y/m/d',$val['createtime']); ?><br/><?php echo date('H:i',$val['createtime']); ?></div>
                </td>
                <?php } ?>
                <?php if(in_array(20, $sell_house_field_arr)){ ?>
                <td class="c6"><div class="info info_p_r zws_line16"><?php echo date('Y/m/d',$val['updatetime']); ?><br/><?php echo date('H:i',$val['updatetime']); ?></div></td>
                <?php } ?>
                <?php if(in_array(21, $sell_house_field_arr)){ ?>
                <td class="c6"><div class="info"><?php echo $val['broker_name']; ?></div></td>
                <?php } ?>
                <td class="c6">
                    <div class="info">
                        <button class='house-follow' houseId= <?php echo $val['id'] ?>>
                            <span>房源跟进</span>
                        </button>
                    </div>
                </td>
            </tr>
                <?php }
            } else {
                ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php }?>
        </table>
    </div>
    <script type="text/javascript">
		$(function(){
			function reHeightList(){

				var ListHeight = $(window).height();
				var TabHeight = $(".tab_box").height();
				var SearchHeight = $("#js_search_box").height();
				$("#js_innerHouse").css("height",(ListHeight-TabHeight-SearchHeight-138)+"px");

			}
			reHeightList();
			setInterval(function(){
				reHeightList();
			},500)

		})
	</script>
</div>

<div class="fun_btn clearfix" id="js_fun_btn" style="">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>
<!--最小化导航栏-->
    <div class="zws_bottom_nav" style="margin-top:-8px;">
        <div class="zws_bottom_nav_dao">
            <ul id="window_min">
                <?php if(is_full_array($sell_list_min_arr)){
                    foreach($sell_list_min_arr as $k => $v){
                ?>
                <li id="window_min_id_<?php echo $v['house_id']; ?>">
                <span class="zws_bottom_nav_dao_img "></span>
                <span class="zws_bottom_span"><?php echo $v['name']; ?></span>
                <input type="hidden" value="<?php echo '/sell/details_house/'.$v['house_id'].'/1' ?>"/>
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
<ul id="openList">
    <input type="hidden" id="right_id" class="js_input">
    <!--右键菜单-->
    <?php if('1'==$post_param['is_public']){ ?>
        <li onClick="openHouseDetails('sell',1);" class="js_input_1">查看详情</li>
        <li  onClick="open_follow('sell',1)" class="js_input_4">房源跟进</li>
    <?php }else{ ?>
        <li onClick="openHouseDetails('sell',1);" class="js_input_1">查看详情</li>
        <li onclick="modifyInfo('sell');" class="js_input_2">修改详情</li>

        <li class="line"></li>
        <!--        <li class="js_input_4" id="qunfa_openlist">群发房源</li>-->
        <li class="js_input_4" id="follow_openlist">房源跟进</li>
        <!--        <li class="js_input_13" id="dayin_openlist">房源打印</li>-->
        <li class="line"></li>
        <li class="js_input_5" id="match_openlist">智能匹配</li>
        <?php if('2'==$group_id){ ?>

        <?php if('1'==$open_cooperate){?>
            <!--是否开启合作审核区分-->
            <?php if('1'==$check_cooperate){?>
            <li class="js_input_6 secondLevelParent" style="position:relative;">
            设置合作<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/jt.gif" style="padding-left:3px;">
            <div class="secondLevel">
                <p id="isshare"><a href="javascript:void(0);" onclick="share_check('sell',0);">发到合作中心</a></p>
                <p id="isshare_friend"><a href="javascript:void(0);" onclick="share_check('sell',1);">发到朋友圈</a></p>
                <!--                <p id="isshare_district"><a href="javascript:void(0);" onclick="sharechange(1,'sell',2);">发到区域公盘</a></p>-->
            </div>
            </li>
            <?php }else{?>
            <li  class="js_input_6 secondLevelParent" style="position:relative;">
            设置合作<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/jt.gif" style="padding-left:3px;">
                <div class="secondLevel">
                    <p id="isshare"><a href="javascript:void(0);" onclick="sharechange(1,'sell',0);">发到合作中心</a></p>
                    <p id="isshare_friend"><a href="javascript:void(0);" onclick="sharechange(1,'sell',1);">发到朋友圈</a>
                    </p>
                    <!--                    <p id="isshare_district"><a href="javascript:void(0);" onclick="sharechange(1,'sell',2);">发到区域公盘</a>-->
                    </p>
                </div>
            </li>
            <?php }?>
                <li class="js_input_14 " id="send_to_district" style="position:relative;display: none"
                    onclick="sharechange(1,'sell',2)">
                    发到公盘
                </li>
                <li class="js_input_14 " id="cancel_to_district" style="position:relative;display: none"
                    onclick="sharecancel(0,'sell','','district');">
                    公盘下架
                </li>
        <li onclick = "sharecancel(0,'sell');" class="js_input_7">取消合作</li>
        <?php }?>
        <?php } ?>

        <li class="line"></li>
        <li class="js_input_8" id="task_openlist">分配任务</li>
        <li class="js_input_9" id="fenpei_openlist">分配房源</li>
        <?php if('2'==$group_id){?>
<!--        <li onclick="fang100('sell',1)" class="js_input_11">同步至平台</li>
        <li onclick="fang100('sell',0)" class="js_input_12">从平台下架</li>-->
        <?php }?>
    <?php } ?>

</ul>

<!--设置合作的二级菜单-->
<script type="text/javascript">
function getCookie(name)//取cookies函数
{
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
     if(arr != null) return unescape(arr[2]); return null;
}

function delCookie(name)//删除cookie
{
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString()+ ";path=/";;
}

$(function(){

    $('#block_name').keydown(function(){
        $('#block_id').val('');
    });

	var timer =null;
//获取合作
    var isshare_html = $(".secondLevel").html();
    $(".js_input_6").live("mouseenter", function () {
        var _this = $(this);
		$(this).find("img").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/baijt.gif");

//        var arr = [];
//        $(".table").find("input:checked[name=items]").each(function (i) {
//            arr[i] = $(this).val();
//        });
//        text = arr.join(",");
//        var is_send_district_str = 'is_send_district' + $(".table").find("input:checked[name=items]").val();
//        var is_send_district = $('#' + is_send_district_str).val();
//        if (is_send_district == 1) {
//            $("#isshare_district").empty().html('<a href="javascript:void(0);" onclick = "sharecancel(0,\'sell\',\'\',\'district\');">从区域公盘下架</a>');
//        } else {
//            $("#isshare_district").empty().html('<a href="javascript:void(0);" onclick="sharechange(1,\'sell\',2);">发到区域公盘</a>');
//        }

        _this.find("div").show();

        clearTimeout(timer);
//
//        $.ajax({
//            url: "<?php //echo MLS_URL;?>///sell/house_info/",
//            async: true,
//            type: "POST",
//            dataType: "json",
//            data: {house_id: text},
//            success: function (data) {
//                $(".secondLevel").empty().html(isshare_html);
//                if (data.isshare == 1) {
//                    $("#isshare").empty().html('<a href="javascript:void(0);" onclick = "sharecancel(0,\'sell\');">从合作中心下架</a>');
//                }
//                if (data.isshare_friend == 1) {
//                    $("#isshare_friend").empty().html('<a href="javascript:void(0);" onclick = "sharecancel(0,\'sell\');">从朋友圈下架</a>');
//                }
//                if (data.isshare_district == 1) {
//                    $("#isshare_district").empty().html('<a href="javascript:void(0);" onclick = "sharecancel(0,\'sell\',\'\',\'district\');">从区域公盘下架</a>');
//                }
//            }
//
//        });
//        console.log(123456);
//        _this.find("div").show();
//        clearTimeout(timer);
	});
    timer = setTimeout(function () {
        $(this).find("img").attr("src", "<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/jt.gif");
        $(".js_input_6").find("div").hide();
    }, 500);
    $(".js_input_6").live("mouseleave", function () {
        console.log(123);
		$(this).find("img").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/jt.gif");
        timer = setTimeout(function () {
            $(".js_input_6").find("div").hide();
        }, 500);

	})


	//获得上次跟进或合作操作的数据值。
    var Num = getCookie('page_id')-6;
    if(Num > 0){
        var HeightTr = $(".inner").find("tr").height();
        var $content = $(".inner");
        $content.scrollTop( Num*HeightTr );
    }
    delCookie('page_id');
	//'收起'，‘更多’按钮，获得cookie值
    var sell_list_extend = getCookie('sell_list_extend');
    if(1==sell_list_extend){
        $('#js_search_box').find(".hide").css("display","inline");
        $('#extend').html('收起<span class="iconfont">&#xe60a;</span>');
        $('#extend').attr("data-h","1");
    }else{
        $('#js_search_box').find(".hide").hide();
        $('#extend').html('更多<span class="iconfont">&#xe609;</span>');
        $('#extend').attr("data-h","0");
    }
})
</script>

<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/loading.gif" id="mainloading" ><!--遮罩 loading-->
</div>
<!--分配任务-->
<div id="js_fenpeirenwu" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--分配房源-->
<div id="js_allocate_house" class="iframePopBox" style=" width:816px; height:340px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="340" class='iframePop' src=""></iframe>
</div>
<!--跟进信息弹框-->
<div id="js_genjin" class="iframePopBox" style=" width:816px; height:610px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="610" class='iframePop genjinD' src=""></iframe>
</div>
<!--详情页弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <input type="hidden" value="" id="window_min_name"/>
    <input type="hidden" value="" id="window_min_url"/>
    <input type="hidden" value="" id="window_min_id"/>
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" style="right:46px;" id="window_min_click">一</a>
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" id="window_min_close">&#xe60c;</a>
    <iframe frameborder="0" name="detialIframe" scrolling="no" width="816" height="540" class='iframePop detialIframe' id="detialIframe" src=""></iframe>
</div>
<!--房源打印-->
<div id="js_house_print" class="iframePopBox" style=" width:818px; height:491px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="818" height="491" class='iframePop' src=""></iframe>
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
<!--上传房产证弹框-->
<div id="upload_certificate" class="pop_box_g pop_see_inform pop_no_q_up" style="width: 350px">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <div class="text-wrap">
                    <table width="100%">
                        <tr>
                            <td>
                                <div class="img"><img alt="" id="imgg"
                                                      src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/s_ico.png">
                                </div>
                            </td>
                            <td class="msg"><span class="bold" id="upload_certificate_tip"></span></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="js_s_h_info_house item_fg3">
                                    <div class="add_pic_house_box clearfix" style="min-height: auto">
                                        <div class="add_item">
                                            <span id="spanButtonPlaceholder3"></span>
                                        </div>

                                        <script type="text/javascript">
                                            var swfu5;
                                            $(function () {
                                                swfu5 = new SWFUpload({
                                                    // Backend Settings
                                                    file_post_name: "file",
                                                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                                                    //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                                                    //post_params: {"postion" : position},
                                                    // File Upload Settings
                                                    file_size_limit: "5 MB",
                                                    file_types: "*.jpg;*.png",
                                                    file_types_description: "JPG Images",
                                                    file_upload_limit: "0",
                                                    file_queue_limit: "5",

                                                    custom_settings: {
                                                        upload_target: "jsPicPreviewBoxM5",
                                                        upload_limit: 1,
                                                        upload_nail: "thumbnails5",
                                                        upload_infotype: 5
                                                    },

                                                    // Event Handler Settings - these functions as defined in Handlers.js
                                                    //  The handlers are not part of SWFUpload but are part of my website and control how
                                                    //  my website reacts to the SWFUpload events.
                                                    swfupload_loaded_handler: swfUploadLoaded,
                                                    file_queue_error_handler: fileQueueError,
                                                    file_dialog_start_handler: fileDialogStart,
                                                    file_dialog_complete_handler: fileDialogComplete,
                                                    upload_progress_handler: uploadProgress,
                                                    upload_error_handler: uploadError,
                                                    upload_success_handler: uploadSuccessNew,
                                                    upload_complete_handler: uploadComplete,


                                                    // Button Settings
                                                    button_image_url: "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn05.png",
                                                    button_placeholder_id: "spanButtonPlaceholder3",
                                                    button_width: 130,
                                                    button_height: 100,
                                                    button_cursor: SWFUpload.CURSOR.HAND,
                                                    button_text: "",
                                                    flash_url: "/swfupload.swf"
                                                });

                                                //标签个数限制
                                                $('.sell_tag b').live('click', function () {
                                                    var sell_tag_num = $('.sell_tag').find('.labelOn').size();
                                                    if (sell_tag_num > 3) {
                                                        $(this).find(".js_checkbox").prop("checked", false);
                                                        $(this).removeClass("labelOn");
                                                    }
                                                });
                                            });
                                        </script>
                                        <div id="jsPicPreviewBoxM5" style="display:none"></div>
                                        <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails5">
                                        </div>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <a href="javascript:void(0);" id="sure_yes" onclick="save_certificate()"
                   class="btn-lv1 btn-mid btn_qd_text JS_Close" style="margin:0 auto !important">确定</a>
            </div>

        </div>
    </div>
</div>
<!--合作审核操作结果弹出警告-->
<div id="js_pop_do_warning_share_check" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
			<div class="text-wrap">
                <p><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png"> <strong class="msg">提交合作申请成功</strong><br>
                管理人员审核通过后，消息会第一时间告知！</p>
            </div>
			<a href="javascript:void(0);" id="sure_yes_share_check" class="btn-lv1 btn-mid btn_qd_text JS_Close" style="margin:0 auto !important">确定</a>
            </div>

        </div>
    </div>
</div>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="sub_form();" title="关闭" class="JS_Close iconfont"></a>
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
<!--询问操作确定弹窗-->
<div id="jss_pop_tip" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" id="dialogSaveDiv" style="font-size:14px;"></p>
                     <div class="center">
                    <button type="button" id = 'dialog_share' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button"  class="btn-hui1 JS_Close">取消</button>
                    </div>
                    <input type ="hidden" name='ci_id' id = 'rowid' value = ''>
                    <input type ="hidden" name='secret_key' id = 'secret_key' value = ''>
                    <input type ="hidden" name='atction_type' id = 'atction_type' value = ''>
                    <input type ="hidden" name='do_type' id = 'do_type' value = ''>
                </div>
            </div>
    </div>
</div>
<!--是否上传室内图户型图询问操作确定弹窗-->
<div id="pic_pop_tip" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="dialog_tip"></p>
                <button type="button" id='dialog_sure' class="btn-lv1 btn-left JS_Close" onclick="modifyInfo('sell');">
                    去编辑
                </button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            </div>
        </div>
    </div>
</div>
<!-- 出售房源入 -->
<div id="jss_pop_import" class="pop_box_g pop_see_inform" style=" display:none;" >
    <div class="hd">
        <div class="title">出售房源导入</div>
        <div class="close_pop"><a href="/sell/lists" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">

        <div class="up_m_b_tex">房源导入功能可以将外部房源直接导入系统中，省去手动录入的麻烦。为保证您的房源顺利导入，请使用我们提供的标准模板，且勿对模板样式做任何删改。</br>
            <a href="<?php echo MLS_SOURCE_URL; ?>/xls/example1.xls" target="_blank">
                <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/page_white_excel.png">点击下载出售房源导入模板</a>
        </div>
        <style>
            .up_m_b_file .text{ float:left; line-height:26px;}
            .up_m_b_file .text_input{width:150px;height: 24px;line-height: 24px;padding: 0 10px;border: 1px solid #E9E9E9;float: left;}
            .up_m_b_file .f_btn{ margin-left:10px;_display:inline; float:left; background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0; width:44px; height:26px; overflow:hidden; position:relative; overflow:hidden; text-align:center; line-height:26px; }
            .up_m_b_file .f_btn .file{cursor:pointer;font-size:50px;filter:alpha(opacity:0); opacity: 0; position:absolute; right:-5px; top:-5px;}
            .up_m_b_file .btn_up_b{ margin-left:10px; _display:inline; float:left; overflow:hidden; width:44px; height:26px; position:relative; line-height:26px; text-align:center;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0;}
            .up_m_b_file .btn_up_b .btn_up{ cursor:pointer; font-size:100px; position:absolute;filter:alpha(opacity:0); opacity: 0; right:-5px; top:-5px;}
        </style>
        <div class="up_m_b_file clearfix">
            <form action="/sell/import" enctype="multipart/form-data" target="new" method="post">
            <p class="text">上传导入文件：</p>
            <input type="text" class="text_input" id="aa" name="aa">
            <div class="f_btn" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">浏览</div><input class="file" name="upfile" type="file" onchange="document.getElementById('aa').value=this.value"></div>
            <div class="btn_up_b" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">上传</div><input class="btn_up" type="submit" name="sub" value="上传"></div>
            </form>
        </div>
        <iframe allowtransparency="true" src="<?php echo MLS_URL;?>/blank.php" frameborder="0" scrolling="no" name="new" id="xx1x" height="34" width="393" style="bac"></iframe><!-- width="470"  wty---->
        <!--<p class="up_m_b_date_up" style="text-align: center">出售房源12321.xls<span class="up_s">上传成功</span>，共上传123条房源。</p>
        <p class="up_m_b_date_up" style="text-align: center">出售房源12321.xls<span class="up_e">上传失败</span>，共上传123条房源。</p> -->
        <div style="text-align:center;"><a class="btn-lv" href="javascript:void(0)" onclick="openn_sure('sell')"><span>确认导入</span></a></div>
    </div>
</div>
<script>
function see_reason(){
	var xxx = $(document.getElementById('xx1x').contentWindow.document.body).html();
	xxx = xxx.replace(/<p .*?>(.*?)<\/p>/g," ");
	xxx = xxx.replace(/<P .*?>(.*?)<\/P>/g," "); //为了兼容ie6
	xxx = xxx.replace(/display:none/g,"display:block");
	xxx = xxx.replace(/DISPLAY: none/g,"DISPLAY: block"); //为了兼容ie6
	//alert(xxx);
	$("#js_pop_msg_excel .up_inner").html(xxx);
	openWin('js_pop_msg_excel');
}
</script>


<!--确认导入表格弹窗-->
<div id="jss_pop_sure" class="pop_box_g pop_see_inform pop_no_q_up stop_pop_box" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:location.reload();" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod" style="_margin-top:-10px;">
    	<div class="inform_inner">
             <div class="up_inner">
				<p class="text" style="line-height:28px;"><br>
				   <img alt="" src="">
					<span></span>
				</p>
			</div>
        </div>
    </div>
</div>

<!--提示导入表格弹窗-->
<div id="jss_pop_error" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" style="line-height:28px;"><br>
                    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/error_ico.png">
                    <span> 请上传表格！</span>
                    </p>
                </div>
            </div>
    </div>
</div>


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


<!-- 提示消息弹窗 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="javascript:void(0)"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img class="img_msg" style="margin-right:10px;" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                    <span class="span_msg"></span><!-- id="dialog_do_itp"-->
                </p>
            </div>
        </div>
    </div>
</div>

<!-- 导入表格错误提示框 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg_excel" style="margin-left:-200px;width:400px">
    <div class="hd">
        <div class="title">失败列表</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="javascript:void(0)"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner" style="height:150px;overflow-x:hidden;overflow-y:auto">
            <div class="up_inner" style="padding:0px">
                <p class="text"><img class="img_msg" style="margin-right:10px;" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                    <span class="span_msg"></span><!-- id="dialog_do_itp"-->
                </p>
            </div>
        </div>
    </div>
</div>

<!--导出求购房源报表弹出窗口-->
<div class="pop_box_g pop_box_g_big pop_box_d_c pop_box_d_c02" style="display:none" id="js_sell_export">
    <div class="hd">
        <div class="title">报表导出</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <form action="<?php echo MLS_URL;?>/sell/exportReport" method="post" id="myform">
            <!--存放当前的总页数用于判断-->
            <input type="hidden" name="hid_total_page" value="<?php echo $pages?>"/>
        <p class="d_c_title"><!--方式一：报表导出--></p>
        <div class="inner">
            <strong class="t">请选择导出类型：</strong>
                <label class="label"><input type="radio" name="ch" value="1">仅导出所选房源</label>
                <label class="label"><input type="radio" name="ch" value="2">导出当前页所有房源</label>
                <label class="label"><input type="radio" name="ch" value="3">导出多页房源</label>
                导出范围：<input type="text" class="text_input w40" name="start_page" disabled="disabled">
                <span class="fg">一</span>
                <input type="text" class="text_input w40" name="end_page" disabled="disabled">（一次最多只能导出10页）
                <input type="hidden" name="ch_1_data" value="">   <!--用于存放ch的值为1的时候的ID数组-->
                <input type="hidden" name="final_data" value="">  <!--用于存放ch的值为2和3的时候的提交数据-->
        </div>
            <!--		<div style="text-align:center; padding-top:10px;"><a class="btn-lv" onclick="sub_export_btn()" target="_blank"><span>导出房源</span></a></div>-->
        <div class="inner inner02">
            <div class="item item01">
                <p class="t_n"><label><input type="radio" name="ch" value="4">模版一</label></p>
                <div class="img_b"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/mb01.png"></div>

                <div>
                    每页的打印条数： <input type="text" class="text_input w50" name="con1" disabled="disabled">  条
                </div>
            </div>
            <div class="item item02">
                <p class="t_n"><label><input type="radio" name="ch" value="5">模版二</label></p>
                <div class="img_b"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/mb02.png"></div>

                <div>
                    每页的打印条数： <input type="text" class="text_input w50" name="con2" disabled="disabled">  条
                </div>
            </div>
            <div class="item item03">
                <p class="t_n"><label><input type="radio" name="ch" value="6">模版三</label></p>
                <div class="img_b"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/mb03.png"></div>

                <div>
                    单页只能打印一条房源<br>
                    如选择多条房源默认打印<br>
                    所选第一条房源&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
            </div>
        </div>
        <div style="text-align:center; padding-top:10px;"><a href="javascript:void(0);" class="btn-lv" onclick="sub_print_btn()"><span>打印预览</span></a></div>
        </form>
    </div>
</div>
<!-- 用于提交打印预览的数据 -->
<!--<form action="/sell/print_hid_one" method="post" id="hid_form" target="_blank">
    <input type="hidden" name="hid_data" id="hid_data" value="">
</form>
<form action="/sell/print_hid_two" method="post" id="hid_form1" target="_blank">
    <input type="hidden" name="hid_data1" id="hid_data1" value="">
</form>
<form action="/sell/print_hid_three" method="get" id="hid_form2" target="_blank">
    <input type="hidden" name="hid_data2" id="hid_data2" value="">
</form>-->

<script>
	/*
	*	aim:	面积、总价等 onblur 事件的校验
	*	author: angel_in_us
	*	date:	2015.03.04
	*/
	function check_num(){
		var areamin    =    $("#areamin").val();	//最小面积
		var areamax    =    $("#areamax").val();	//最大面积
		var pricemin   =    $("#pricemin").val();	//最小总价
		var pricemax   =    $("#pricemax").val();	//最大总价
		var floormin   =    $("#floormin").val();	//最小楼层
		var floormax   =    $("#floormax").val();	//最大楼层
		//最小面积
		if(areamin){
			var   type="^\\d+$";
			var   re   =   new   RegExp(type);

			if(areamin.match(re)==null)
			{
				$("#areamin_reminder").html("面积必须为正整数！");
				return;
			}else{
				$("#areamin_reminder").html("");
			}
		}

		//最大面积
		if(areamax){
			var   type="^\\d+$";
			var   re   =   new   RegExp(type);

			if(areamax.match(re)==null)
			{
				$("#areamin_reminder").html("面积必须为正整数！");
				return;
			}else{
				$("#areamin_reminder").html("");
			}
		}

		//最小总价
		if(pricemin){
			var   type="^\\d+$";
			var   re   =   new   RegExp(type);

			if(pricemin.match(re)==null)
			{
				$("#pricemin_reminder").html("总价必须为正整数！");
				return;
			}else{
				$("#pricemin_reminder").html("");
			}
		}

		//最大总价
		if(pricemax){
			var   type="^\\d+$";
			var   re   =   new   RegExp(type);

			if(pricemax.match(re)==null)
			{
				$("#pricemin_reminder").html("总价必须为正整数！");
				return;
			}else{
				$("#pricemin_reminder").html("");
			}
		}

		//最小楼层
		if(floormin){
			var   type="^\\d+$";
			var   re   =   new   RegExp(type);

			if(floormin.match(re)==null)
			{
				$("#floormin_reminder").html("楼层必须为正整数！");
				return;
			}else{
				$("#floormin_reminder").html("");
			}
		}

		//最大楼层
		if(floormax){
			var   type="^\\d+$";
			var   re   =   new   RegExp(type);

			if(floormax.match(re)==null)
			{
				$("#floormin_reminder").html("楼层必须为正整数！");
				return;
			}else{
				$("#floormin_reminder").html("");
			}
		}

	}

    function del_cookie()
    {
        $.ajax({
            url: "/sell/del_search_cookie/sell_list",
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

    function save_certificate() {
        var p_filename5 = $("input[name='p_filename5[]']").val();
        var house_id = $(".table").find("input:checked[name=items]").val();

        var block_id = $("#block_id" + house_id).val();
        console.log(block_id)

        $.ajax({
            url: "/sell/save_cetificate",
            type: "POST",
            dataType: "json",
            data: {house_id: house_id, p_filename5: p_filename5, block_id: block_id},
            success: function (data) {
                $("#dialog_do_warnig_tip").html(data.msg);
                openWin('js_pop_do_warning')
            }
        });


    }






</script>
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
                            <td class="msg"><span class="bold">您确定要将该房源取消合作吗？</span></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="msg">取消后该房源信息将错过全网经纪人，<br/>可能会影响到房源快速成交，再考虑下吧！</td>
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

<!--设置合作弹框-->
<div id="js_pop_set_share_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
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
                            <td class="msg"><span class="bold"><span>请选择佣金比例
                            <select  class="select" name="commission_ratio" id="commission_ratio">
                                <?php foreach ($config['commission_ratio'] as $k => $v) {?>
                                    <option value="<?php echo $k;?>" <?php if ($k == 5) {echo ' selected="true"';} ?>><?php echo $v;?></option>
                               <?php } ?>
                            </select>
                            <b style="font-weight:normal;color:#FAC16B">(房源方：客源方)</b>
                            </span><br/>您确定要将该房源设置合作吗？<br>快捷设置合作默认为佣金分配奖励方式，如需使用赏金奖励方式，请在房源编辑界面发布合作</span></td>
                        </tr>
                    </table>
                </div>
                <div class="center">
                <button type="button" class="btn-lv1 btn-left" id="dialog_share_share">确定</button>
                <button type="button" class="btn-hui1 JS_Close">取消</button>
            	</div>
            </div>
        </div>
    </div>
</div>

<!--群发弹框-->
<div id="js_pop_box_g_publishing_first" class="iframePopBox" style=" width:440px; height:295px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="440" height="295" class='iframePop' src=""></iframe>
    <input type="hidden" id="pub_first" value="1"/>
</div>
<!--群发发布中-->
<div id="js_pop_box_g_publishing" class="iframePopBox" style=" width:690px; height:360px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="690" height="360" class='iframePop' src=""></iframe>
</div>
