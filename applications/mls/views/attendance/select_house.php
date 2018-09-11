<body >
<!--选择房源-->
<form action="" method="post" id="search_form">
<div class="search_box clearfix pop_house_select_iframe" id="js_search_box">
    <div class="fg_box">
        <p class="fg fg_tex"> 交易类型：</p>
        <div class="fg fg-edit">
            <select>
                <?php if($type == 1) {?>
                <option value="1">出售</option>
                <?php }elseif($type == 2) {?>
                <option value="2">出租</option>
                <?php }?><?php echo $val['id'];?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 状态：</p>
        <div class="fg fg-edit">
            <select name="status">
                <option value="0" >全部</option>
                <?php foreach($config['status'] as $k => $v){?>
                <option value="<?php echo $k;?>" <?php if($post_param['status']==$k){echo "selected='selected'";}?>><?php echo $v;?></option>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex">楼盘：</p>
        <div class="fg fg-edit">
            <input type="text" size="20" name="block_name" id="block_name" value="<?php echo $post_param['block_name']; ?>"/>
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
        <p class="fg fg_tex">房源编号：</p>
        <div class="fg fg-edit"><input type="text" size="10" name="house_id" value="<?php echo $post_param['house_id'];?>"/></div>
    </div>
    <?php if(in_array($func_area,array(2,3))){ ?>
    <div class="fg_box">
        <p class="fg fg_tex"> 所属业务员：</p>
        <div class="fg fg-edit">
            <?php if($func_area==3){ ?>
            <select name="agency_id" id="agency_id">
                <option value="0">请选择门店</option>
                <?php foreach($agencys as $k => $v){?>
				<option value="<?php echo $v['agency_id'];?>" <?php if($post_param['agency_id']==$v['agency_id']){echo "selected='selected'";}?>><?php echo $v['agency_name'];?></option>
				<?php }?>
            </select>&nbsp;&nbsp;
            <?php }?>
            <select name="broker_id" id="broker_id">
                <option value="0">请选择人员</option>
                <?php if( isset($brokers) ){?>
                <?php foreach($brokers as $k => $v){?>
				<option value="<?php echo $v['broker_id'];?>" <?php if($post_param['broker_id']==$v['broker_id']){echo "selected='selected'";}?>><?php echo $v['truename'];?></option>
				<?php }?>
                <?php }?>
            </select>
        </div>
    </div>
    <?php }?>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
    </div>
</div>

<div class="table_all pop_house_select_iframe_table">
    <div class="title">
        <table class="table">
            <tr>
                <td class="c5"></td>
                <td class="c6">房源编号</td>
                <td class="c7">状态</td>
                <td class="c7">类型</td>
                <td class="c20">楼盘</td>
                <td class="c7">栋座<br/>(栋)</td>
                <td class="c7">单元</td>
                <td class="c7">门牌</td>
                <td class="c7">面积<br/>(m&sup2;)</td>
                <td class="c7">价格<br/>(<?php if($type == 1) {?>万<?php }elseif($type == 2) {?>元/月<?php }?>)</td>
                <td class="c20">所属业务人员</td>
            </tr>
        </table>
    </div>
    <div class="inner">
        <table class="table list-table">
            <?php
            if($list){
                foreach($list as $key=>$val){
            ?>
            <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
                <td class="c5">
                    <input type="radio" class="house" value="<?php echo $val['id'];?>" />
                    <input type="hidden" class="house_id" value="<?php if($type == 1) {?>CS<?php }elseif($type == 2) {?>CZ<?php }?><?php echo $val['id'];?>" />
                </td>
                <td class="c6"><?php if($type == 1) {?>CS<?php }elseif($type == 2) {?>CZ<?php }?><?php echo $val['id'];?></td>
                <td class="c7"><?php echo $config['status'][$val['status']]; ?></td>
                <td class="c7"><?php if($type == 1) {?>出售<?php }elseif($type == 2) {?>出租<?php }?></td>
                <td class="c20"><?php echo $val['block_name'];?></td>
                <td class="c7"><?php echo $val['dong'];?></td>
                <td class="c7"><?php echo $val['unit'];?></td>
                <td class="c7"><?php echo $val['door'];?></td>
                <td class="c7"><?php echo strip_end_0($val['buildarea']);?></td>
                <td class="c7"><?php echo strip_end_0($val['price']);?></td>
                <td class="c20"><?php echo $val['agency_name'];?>&nbsp;<?php echo $val['broker_name'];?></td>
            </tr>
            <?php
                }
            }else{
            ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>

<div class="fun_btn clearfix bottom-fun-btn" id="js_fun_btn">
    <div class="get_page">
        <?php echo $page_list; ?>
    </div>
</div>
</form>

<div class="btn-group">
    <input type="button" value="确定" id="confirm" class="btn-lv1 btn-left" onclick="insert(<?php echo $act;?>);"/>
    <input type="button" value="取消" id="cancel" class="btn-hui1" onclick="close_iframe();"/>
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
                 <p class="text" id='dialog_do_warnig_tip'><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/error_ico.png">&nbsp;&nbsp;<span></span></p>
            </div>
        </div>
    </div>
</div>
</body>
<script>
$(function(){
    $('#agency_id').change(function(){
        var agencyId = $(this).val();
        $.ajax({
            type: 'get',
            url : '/my_task/get_broker_ajax/'+agencyId,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg===''){
                    str = '<option value="0">请选择人员</option>';
                }else{
                    str = '<option value="0">请选择人员</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].broker_id+'">'+msg[i].truename+'</option>';
                    }
                }
                $('#broker_id').html(str);
            }
        });
    });
});
function insert(act){
    var house = $(".house:checked").val();
    if(house){
        var _tr = $("#tr"+house);
        var house_id = _tr.find(".house_id").val();
        $(self.parent.frames["iframe"+act].document).find("#house_id").val(house_id);
        close_iframe();
    }else{
        $("#dialog_do_warnig_tip span").html("请选择房源！");
        openWin('js_pop_do_warning');
    }
}
function close_iframe(){
    $(window.parent.document).find('#js_pop_house_select').hide();
    $(window.parent.document).find('#' + 'GTipsCover' + 'js_pop_house_select').remove();
}
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>
