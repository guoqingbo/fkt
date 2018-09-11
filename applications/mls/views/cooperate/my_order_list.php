<div class="tab_box" id="js_tab_box">
   	<a href="/cooperate/my_order_list/accept/" class="link link_padding <?php if($fun_type=="accept"){echo "link_on";}?>"><span class="iconfont">&#xe611;</span>收到的合作申请</a>
    <a href="/cooperate/my_order_list/send/" class="link link_padding <?php if($fun_type=="send"){echo "link_on";}?>"><span class="iconfont">&#xe612;</span>发起的合作申请</a>
</div>
<div id="js_fun_btn" class="t_bg_box_text">
    <span class="item">待处理申请：<strong class="color"><?php echo $all_estas_num1;?></strong></span>
    <span class="item">待评价合作：<strong class="color"><?php echo $all_estas_num2;?></strong></span>
    <span class="item">合作生效：<strong class="color"><?php echo $all_estas_num3;?></strong></span>
    <span class="item">交易成功：<strong class="color"><?php echo $all_estas_num4;?></strong></span>
</div>
<form method='post' action='' id='search_form' name='search_form'>
<div class="search_box clearfix"  id="js_search_box_02">
    <div class="fg_box">
        <p class="fg fg_tex">状态：</p>
        <div class="fg">
            <select class="select" id="esta" name="esta">
                <option value="0">不限</option>
				<?php foreach($esta_conf as $k => $v){?>
				<option value="<?php echo $k;?>" <?php if($post_param['esta']==$k){echo "selected='selected'";}?>><?php echo $v;?></option>
				<?php }?>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 交易编号：</p>
        <div class="fg">
            <input type="text" class="input w60" id="order_sn" name="order_sn" value="<?php echo $post_param['order_sn'];?>">
        </div>
    </div>

    <div class="fg_box">
        <p class="fg fg_tex"> 房源编号：</p>
        <div class="fg">
            <input type="text" class="input w60" id="rowid" name="rowid" value="<?php echo $post_param['rowid'];?>">
        </div>
    </div>
    <?php if($role_type==3){ ?>
    <div class="fg_box">
        <p class="fg fg_tex">我方门店：</p>
        <div class="fg">
            <select class="select" id="agentid_w" name="agentid_w">
                <option value="0">不限</option>
                <?php foreach($agencys as $k => $v){?>
				<option value="<?php echo $v['agency_id'];?>" <?php if($post_param['agentid_w']==$v['agency_id']){echo "selected='selected'";}?>><?php echo $v['agency_name'];?></option>
				<?php }?>
            </select>
        </div>
    </div>
    <?php }?>
    <?php if(in_array($role_type,array(2,3))){ ?>
    <div class="fg_box">
        <p class="fg fg_tex">我方经纪人：</p>
        <div class="fg">
            <select class="select" id="brokerid_w" name="brokerid_w">
                <option value="0">不限</option>
                <?php if( isset($brokers) ){?>
                <?php foreach($brokers as $k => $v){?>
				<option value="<?php echo $v['broker_id'];?>" <?php if($post_param['brokerid_w']==$v['broker_id']){echo "selected='selected'";}?>><?php echo $v['truename'];?></option>
				<?php }?>
                <?php }?>
            </select>
        </div>
    </div>
    <?php }?>
    <div class="fg_box">
        <p class="fg fg_tex"> 合作经纪人姓名：</p>
        <div class="fg">
            <input type="text" class="input w40" id="broker_name" name="broker_name" value="<?php echo $post_param['broker_name'];?>">
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 手机：</p>
        <div class="fg">
            <input type="text" class="input w60" id="phone" name="phone" value="<?php echo $post_param['phone'];?>">
        </div>
    </div>

   <div class="fg_box">
        <p class="fg fg_tex"> 门店：</p>
        <div class="fg">
            <input type="text" class="input w60" id="agentid" name="agentid" value="<?php echo $post_param['agentid'];?>">
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();">搜索</a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();">重置</a></div>
    </div>
</div>
<div id="js_search_box" class="shop_tab_title">
	<input type="hidden" value="<?php echo @$_POST['estas'];?>" id="estas" name="estas">
	<a href="javascript:void(0)" onclick="search_form.estas.value=0;search_form.submit();return false;" class="link <?php if($estas==0){echo "link_on";}?>">全部（<?php echo $estas_num;?>）<span class="iconfont hide">&#xe607;</span></a>
	<a href="javascript:void(0)" onclick="search_form.estas.value=1;search_form.submit();return false;" class="link <?php if($estas==1){echo "link_on";}?>">待处理（<?php echo $estas_num1;?>）<span class="iconfont hide">&#xe607;</span></a>
	<a href="javascript:void(0)" onclick="search_form.estas.value=2;search_form.submit();return false;" class="link <?php if($estas==2){echo "link_on";}?>">待评价（<?php echo $estas_num2;?>）<span class="iconfont hide">&#xe607;</span></a>
	<a href="javascript:void(0)" onclick="search_form.estas.value=3;search_form.submit();return false;" class="link <?php if($estas==3){echo "link_on";}?>">合作生效（<?php echo $estas_num3;?>）<span class="iconfont hide">&#xe607;</span></a>
	<a href="javascript:void(0)" onclick="search_form.estas.value=4;search_form.submit();return false;" class="link <?php if($estas==4){echo "link_on";}?>">交易成功（<?php echo $estas_num4;?>）<span class="iconfont hide">&#xe607;</span></a>
	<label class="label_left_t">
        <input type="checkbox" id="order_key_rowid" name="order_key_rowid" value="1" <?php if($order_key_rowid==1){echo "checked='checked'";}?> onchange="search_form.submit();return false;">相同房源申请合并展示
	</label>


    <div class="get_page">
    	<?php echo $page_list;?>
    </div>

</div>
</form>

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <td class="c5"><div class="info">我方经纪人</div></td>
              	<td class="c9"><div class="info">交易编号</div></td>
                <td class="c5"><div class="info">类型</div></td>
                <td class="c8"><div class="info">房源编号</div></td>
                <td class="c25"><div class="info">房源信息</div></td>
                <td class="c10"><div class="info">合作经纪人</div></td>
                <td class="c8"><div class="info">手机</div></td>
  				<td class="c15"><div class="info">状态更新时间</div></td>
                <td class="c8"><div class="info">状态 </div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
			<?php
			if($list)
			{
				foreach($list as $key => $val)
				{
			?>
            <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
                <td class="c5"><div class="info"><?php echo $val['broker_name'.$primary_postfix];?></div></td>
      			<td class="c9"><div class="info"><?php echo $val['order_sn'];?></div></td>
                <td class="c5"><div class="info"><?php if($val['tbl']=="sell"){echo "售";}else{ echo "租";}?></div></td>
                <td class="c8"><div class="info"><?php echo $val['rowid'];?></div></td>
                <td class="c25">
					<div class="info">
					<?php echo $val['house']['districtname']."-".$val['house']['streetname']." ".$val['house']['blockname']." ".$val['house']['room']."室".$val['house']['hall']."厅".$val['house']['toilet']."卫 ".$config['fitment'][$val['house']['fitment']]." ".$config['forward'][$val['house']['forward']]." ".$val['house']['buildarea']."㎡ ".$val['house']['price']."万";?>
					</div>
				</td>
                <td class="c10">
					<div class="info">
                        <?php echo $val['broker_name'.$secondary_postfix];?>
						<span class="iconfont im">&#xe616;</span>
					</div>
				</td>
                <td class="c8">
					<div class="info">
						<?php echo $val['phone'.$secondary_postfix];?>
					</div>
				</td>
  				<td class="c15"><div class="info"><?php echo date("Y-m-d H:i:s",$val['dateline']);?></div></td>
                <td class="c8">
                    <div class="info">
                        <p class="is_esta <?php if(in_array($val['esta'],array(2,4,11))){echo "s";}elseif(in_array($val['esta'],array(5,6,10))){echo "e";}?>">
                            <?php echo $esta_conf[$val['esta']];?>
                        </p>
                    </div>
                </td>
                <td >
                    <div class="info">
                        <a href="javascript:void(0)" class="fun_link" onclick="open_details('<?php echo $fun_type;?>',<?php echo $val['id'];?>)">详情</a>
                        <?php if(!$val['appraise'.$primary_postfix]){?>
                        <?php if(in_array($val['esta'],array(5,7,9,11))){?><a href="javascript:void(0)" class="fun_link" onclick="open_appraise_details('<?php echo $fun_type;?>',<?php echo $val['id'];?>)">评价</a><?php }?>
                        <?php }else{ echo "已评价";}?>
                    </div>
                </td>
            </tr>
			<?php
                }
            }else{
                ?>
                <tr><td colspan="9">抱歉，没有找到符合条件的信息</td></tr>
            <?php }?>
        </table>
    </div>
</div>

<!--详情弹框-->
<div id="js_pop_box_cooperation" class="iframePopBox" style=" width:920px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="920" height="540" class='iframePop' src=""></iframe>
</div>
<!--评价弹框-->
<div id="js_pop_box_appraise" class="iframePopBox" style=" width:580px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="580" height="540" class='iframePop' src=""></iframe>
</div>
<script>
//打开详情弹层
function open_details(type,id)
{
    var _id = parseInt(id);
    var _url = '<?php echo MLS_URL;?>/cooperate/my_'+type+'_order/'+ _id;

    if(_url)
    {
         $("#js_pop_box_cooperation .iframePop").attr("src",_url);
    }
    openWin('js_pop_box_cooperation');
}

function open_appraise_details(type,id)
{
    var _id = parseInt(id);
    var _url = '<?php echo MLS_URL;?>/cooperate/my_appraise/' + type + '/' + _id;

    if(_url)
    {
         $("#js_pop_box_appraise .iframePop").attr("src",_url);
    }
    openWin('js_pop_box_appraise');
}

$(function(){
    $('#agentid_w').change(function(){
        var agencyId = $(this).val();
        $.ajax({
            type: 'get',
            url : '/my_task/get_broker_ajax/'+agencyId,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg===''){
                    str = '<option value="0">不限</option>';
                }else{
                    str = '<option value="0">不限</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].broker_id+'">'+msg[i].truename+'</option>';
                    }
                }
                $('#brokerid_w').html(str);
            }
        });
    });
});
function reset() {
    window.location.href = window.location.href;
    window.location.reload;
}
</script>


