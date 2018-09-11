<script>
    window.parent.addNavClass(7);
</script>
<div class="tab_box" id="js_tab_box">
   <a href="/site_set/index" class="link"><span class="iconfont">&#xe611;</span>站点设置</a>
    <!--   <a href="/sell/group_publish" class="link link_on"><span class="iconfont">&#xe612;</span>群发房源</a>-->
</div>

<div id="js_search_box" class="shop_tab_title" style="margin-bottom:0">
	<a href="/sell/group_publish" class="link link_on">出售<span class="iconfont hide">&#xe607;</span></a>
   <a href="/rent/group_publish" class="link">出租<span class="iconfont hide">&#xe607;</span></a>
</div>
<form method='post' action='' id='search_form'>
<div id="js_search_box_02" style="padding-bottom:1px;">
	<div class="search_box clearfix" >
        <a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this)" data-h="0">展开<span class="iconfont">&#xe609;</span></a>
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
                <input type="text" name="block_name" id="block_name" value="<?php echo $post_param['block_name']; ?>" class="input w60">
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
                            openWin('js_pop_add_new_block');
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
                <input type="text" name='areamin' value="<?php echo $post_param['areamin']; ?>" class="input w30">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input type="text" name='areamax' value="<?php echo $post_param['areamax']; ?>" class="input w30">
            </div>
            <p class="fg fg_tex fg_tex03">平米</p>
        </div>
        <div class="fg_box">
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
            <p class="fg fg_tex"> 房龄：</p>
            <div class="fg">
                <select class="select" name='yearmin'>
                    <option value='0'>不限</option>
                    <?php
                        for($_i=1970;$_i<=2015;$_i++)
                        {
                            echo '<option value="'.$_i.'"';
                            if($_i == $post_param['yearmin'])
                                echo "selected";
                            echo '>'.$_i.'年</option>';
                        }
                    ?>
                </select>
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <select class="select" name='yearmax'>
                    <option value='0'>不限</option>
                    <?php
                        for($_i=2015;$_i>=1970;$_i--)
                        {
                            echo '<option value="'.$_i.'"';
                            if($_i == $post_param['yearmin'])
                                echo "selected";
                            echo '>'.$_i.'年</option>';
                        }
                    ?>
                </select>
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
            <p class="fg fg_tex"> 房源编号：</p>
            <div class="fg">
                <input type="text" name='house_id' value="<?php echo $post_param['house_id']?>" class="input w60">
                <input type="hidden" name='orderby_id' id="orderby_id" value="<?php echo $post_param['orderby_id']?>">
            </div>
        </div>
        <div class="fg_box">
            <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
            <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
        </div>
    </div>
	<div class="configuration_website">
        <div style="float:left;">
            <?php if($siteinfo){?>已配置站点：<?php foreach($siteinfo as $key=>$val){?><span class="t"><?php echo $val['name'];?></span><?php }} ?><a href="/site_set/index" class="l">配置站点</a>
        </div>
        <div class="get_page" style="margin-top:6px;">
            <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
        </div>
    </div>

</div>



<!-- div id="js_fun_btn" class="fun_btn clearfix">
    <label class="btn btn_del"><input type="checkbox" id="js_checkbox">全选</label>
    <a class="btn btn_del" href="javascript:void(0);" onClick="group_publish('sell');">群发房源</a>
  		<a class="btn btn_del" href="javascript:void(0);">批量刷新</a>
    <a class="btn btn_del" href="javascript:void(0);">批量下架</a>
    <div class="get_page">
        < ?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div -->


<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
              	<!-- td class="c3"><div class="info">&nbsp;</div></td -->
                <td class="c8"><div class="info">编号</div></td>
                <td class="c30"><div class="info">房源情况</div></td>
                <td class="c8"><div class="info">经纪人</div></td>
                <td class="c35"><div class="info">房源群发详情</div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table table_q">
            <?php
            if($list)
            {
                foreach($list as $key => $val)
                {
            ?>
            <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
              	<!-- td class="c3"><div class="info"><input type="checkbox" class="checkbox" name="items" value="<?php echo $val['id'];?>"></div></td -->
                <td class="c8"><div class="info"><?php echo $val['id'];?></div></td>
                <td class="c30"><div class="info"><?php echo $val['block_name'].$val['room'].'室'.$val['hall'].'厅'.$val['toilet'].'卫'.strip_end_0($val['buildarea']).'㎡'.$val['buildyear'];?></div></td>
                <td class="c8"><div class="info"><?php echo $val['broker_name']; ?></div></td>
                <td class="c35">
                			<div class="info">
                   			<div class="configuration_website_list">
                            <dl class="list">
                                <?php if($val['siteinfo']){
                                    foreach($val['siteinfo'] as $k => $v){
                                ?>
                                    <dd class="item  none">
                              			<p class="p">
                                            <span class="n"><?php echo $v['name']; ?></span>
                                            <!-- span>今日刷新：<strong class="num">0</strong></span -->
                                        </p>
                                    </dd>
                                <?php
                                    }
                                }
                                ?>
                            </dl>
                        </div>
                				</div>
                </td>

                <td ><div class="info info_p_r"><a href="javascript:void(0);" onclick="group_publish('sell',<?php echo $val['id'];?>);" class="fun_link">发布</a><span class="fg">|</span><a href="javascript:void(0);"  class="fun_link" onClick="fun_ref(<?php echo $val['id'];?>);">刷新</a><span class="fg">|</span><a href="/sell/modify/<?php echo $val['id'];?>"  class="fun_link">编辑</a><span class="fg">|</span><a href="javascript:void(0);"  class="fun_link" onClick="fun_esta(<?php echo $val['id'];?>);">下架</a></div></td>
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
</form>



<!--群发弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:600px; height:365px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="600" height="365" class='iframePop' src=""></iframe>
</div>

<!--群发发布中-->
<div id="js_pop_box_g_publishing" class="iframePopBox" style=" width:600px; height:335px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="600" height="334" class='iframePop' src=""></iframe>
</div>


<div id="js_pop_no_q" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    					<div class="inform_inner">
																	<div class="up_inner">

                 <p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;确定刷新该房源?</p>
                 <input type = 'hidden' id = 'refresh_id'>
                 <button type="button" class="btn-lv1 btn-left" onclick="$('#js_pop_no_q').hide();">确定</button>
                 <button type="button" class="btn-hui1 JS_Close">取消</button>


                 </div>
         </div>
    </div>
</div>

<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
			<div class="text-wrap">
                    <table>
                        <tr>
                            <td><div class="img"><img alt="" id="imgg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png"></div></td>
                            <td class="msg" ><span class="bold" id="dialog_do_success_tip">操作成功</span></td>
                        </tr>
                    </table>
                </div>
				<a href="javascript:void(0);"  onclick='$("#search_form").submit();return false;' id="sure_yes" class="btn-lv1 btn-mid btn_qd_text JS_Close" style="margin:0 auto !important">确定</a>
            </div>

        </div>
    </div>
</div>

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
                            <td><div class="img"<img alt="" id="imgg" "src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></div></td>
                            <td class="msg" ><span class="bold" id="dialog_do_warnig_tip">操作失败</span></td>
                        </tr>
                    </table>
                </div>
				<a href="javascript:void(0);" id="sure_no" class="btn-lv1 btn-mid btn_qd_text JS_Close" style="margin:0 auto !important">确定</a>
            </div>

        </div>
    </div>
</div>
<script>
    function fun_ref(house_id){
        $.ajax({
            type: 'get',
            url : '/sell/change_zsb/'+house_id+'/1',
            dataType:'json',
            success: function(msg){
                if(msg['state'] == 'success'){
                    openWin('js_pop_do_success');
                }else{
                    openWin('js_pop_do_warning');
                }
            }
        });

    }

    function fun_esta(house_id){
        $.ajax({
            type: 'get',
            url : '/sell/change_zsb/'+house_id+'/2',
            dataType:'json',
            success: function(msg){
                if(msg['state'] == 'success'){
                    openWin('js_pop_do_success');
                }else{
                    openWin('js_pop_do_warning');
                }
            }
        });
    }
    function reset() {
        window.location.href = window.location.href;
        window.location.reload;
    }
</script>
