<script>
    window.parent.addNavClass(4);
</script>
<div class="tab_box" id="js_tab_box">
   	<?php echo $user_menu;?>
</div>
<form method='post' action='/cooperate/<?=$form_action?>' id='search_form' name='search_form'>
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
        <p class="fg fg_tex"> 合同编号：</p>
        <div class="fg">
            <input type="text" class="input w60" id="order_sn" name="order_sn" value="<?php echo $post_param['order_sn'];?>">
        </div>
    </div>

    <div class="fg_box">
        <p class="fg fg_tex"> 楼盘名称：</p>
        <div class="fg">
            <input type="text" class="input w90" id="rowid" name="block_name" value="<?php echo $post_param['block_name'];?>">
        </div>
    </div>
    <?php if($func_area==3){ ?>
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
    <?php if(in_array($func_area,array(2,3))){ ?>
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
            <input type="text" class="input w60" id="broker_name" name="broker_name" value="<?php echo $post_param['broker_name'];?>">
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
        <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
        <div class="fg"><a href="javascript:void(0)" class="reset" onclick="reset();return false;">重置</a></div>
    </div>
</div>
<div id="js_search_box" class="shop_tab_title" style="margin-top:0;">
	<input type="hidden" value="<?php echo $estas;?>" id="estas" name="estas">
	<a href="javascript:void(0)" onclick="search_form.estas.value='all';search_form.submit();return false;" class="link <?php if($estas == 'all'){echo "link_on";}?>">全部（<span class="highlight"><?php echo $estas_num;?></span>）<span class="iconfont hide">&#xe607;</span></a>
    <?php if($type == 'send'){?>
	<a href="javascript:void(0)" onclick="search_form.estas.value='wait_do_b';search_form.submit();return false;" class="link <?php if($estas == 'wait_do_b'){echo "link_on";}?>">待处理（<span class="highlight"><?php echo $estas_num1;?></span>）<span class="iconfont hide">&#xe607;</span></a>
    <?php } else if($type == 'accept') {?>
    <a href="javascript:void(0)" onclick="search_form.estas.value='wait_do_a';search_form.submit();return false;" class="link <?php if($estas == 'wait_do_a'){echo "link_on";}?>">待处理（<span class="highlight"><?php echo $estas_num1;?></span>）<span class="iconfont hide">&#xe607;</span></a>
    <?php }?>
	<a href="javascript:void(0)" onclick="search_form.estas.value='wait_appraise';search_form.submit();return false;" class="link <?php if($estas == 'wait_appraise'){echo "link_on";}?>">待评价（<span class="highlight"><?php echo $estas_num2;?></span>）<span class="iconfont hide">&#xe607;</span></a>
	<a href="javascript:void(0)" onclick="search_form.estas.value='cop_effect';search_form.submit();return false;" class="link <?php if($estas == 'cop_effect'){echo "link_on";}?>">合作生效（<span class="highlight"><?php echo $estas_num3;?></span>）<span class="iconfont hide">&#xe607;</span></a>
	<a href="javascript:void(0)" onclick="search_form.estas.value='cop_success';search_form.submit();return false;" class="link <?php if($estas == 'cop_success'){echo "link_on";}?>">交易成功（<span class="highlight"><?php echo $estas_num4;?></span>）<span class="iconfont hide">&#xe607;</span></a>
	<label class="label_left_t">
        <input type="checkbox" id="order_key_rowid" name="order_key_rowid" value="1" <?php if($order_key_rowid==1){echo "checked='checked'";}?> onclick="search_form.submit();return false;">相同房源申请合并展示<span class="iconfont" style="color:#ff6b3e;" title="将同一房源的合作申请集中展示，方便您查找同房源的合作哦">&#xe614;</span>
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
                <td class="c8"><div class="info">我方经纪人</div></td>
              	<td class="c12"><div class="info">合同编号</div></td>
                <td class="c5"><div class="info">类型</div></td>
                <td class="c30"><div class="info">房源信息</div></td>
                <td class="c8"><div class="info">合作经纪人</div></td>
                <td class="c8"><div class="info">手机</div></td>
  				<td class="c12"><div class="info">状态更新时间</div></td>
                <td class="c8"><div class="info">状态 </div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table table_q" id="js_table_box_Sincerity">
	    <?php
	    if(is_array($list) && !empty($list))
	    {
		    foreach($list as $key => $val)
		    {
	    ?>
            <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>" date-url="/<?php echo $val['tbl'];?>/details_house/<?php echo $val['rowid'];?>/3" controller="sell" _id="<?php echo $val['id'];?>">
                <td class="c8">
                    <div class="info"><?php echo $val['broker_name'.$primary_postfix];?></div>
                </td>
      			<td class="c12">
                    <div class="info"><?php echo $val['order_sn'];?></div>
                </td>
                <td class="c5">
                    <div class="info"><?php echo $val['tbl'] == 'sell' ?  '售' : '租';?></div>
                </td>
                <td class="c30">
		    <div class="info">
		    <?php
                        if(!empty($val['house']) && is_array($val['house']))
                        {
                           echo $val['house']['districtname']."-".$val['house']['streetname'];
                           echo '&nbsp;&nbsp;';
                           echo $val['house']['blockname'];
                           echo '&nbsp;&nbsp;';
                           echo $val['house']['room']."室".$val['house']['hall']."厅".$val['house']['toilet']."卫 ";
                           echo '&nbsp;&nbsp;';
                           if(!empty($val['house']['fitment']))
                           {
                            echo $config['fitment'][$val['house']['fitment']];
                            echo '&nbsp;&nbsp;';
                           }
                           if(!empty($val['house']['forward']))
                           {
                            echo $config['forward'][$val['house']['forward']];
                            echo '&nbsp;&nbsp;';
                           }
                           echo strip_end_0($val['house']['buildarea'])."㎡ ";
                           echo '&nbsp;&nbsp;';
                           echo ('1'==$val['house']['price_danwei'])?$val['house']['price']/$val['house']['buildarea']/30 : strip_end_0($val['house']['price']);
                           if($val['tbl'] == 'sell')
                           {
                               echo '万';
                           }
                           else
                           {
                               echo ('1'==$val['house']['price_danwei'])?'元/㎡*天':'元/月';
                           }
                        }
                        else
                        {
                            echo '房源已下架或合作房源信息异常';
                        }
                       ?>
			</div>
		</td>
                <td class="c8 js_info broker"  data-brokerId = "<?php echo $val['brokerid'.$secondary_postfix];?>">
		    <div class="info">
                        <?php echo $val['broker_name'.$secondary_postfix];?>
                        <!--<span class="iconfont im">&#xe616;</span>-->
                    </div>
		</td>
                <td class="c8">
		    <div class="info">
			<?php echo $val['phone'.$secondary_postfix];?>
		    </div>
		</td>
		<td class="c12"><div class="info"><?php echo date("Y-m-d H:i",$val['dateline']);?></div></td>
                <td class="c8">
                    <div class="info">
                        <p class="is_esta <?php if(in_array($val['esta'],array(2,4,7))){echo "s";}elseif(in_array($val['esta'],array(5,6,8,9,10,11))){echo "e";}?>">
                            <?php echo $esta_conf[$val['esta']];?>
                        </p>
                    </div>
                </td>
                <td class="js_no_click">
                    <div class="info info_p_r">
                        <a href="javascript:void(0)" class="fun_link" onclick="open_details('<?php echo $fun_type;?>',<?php echo $val['id'];?>)">
                            <?php if ($val['step'] <= 2) { ?>
                                <?php if($fun_type == 'send'){ ?>
                                    <?php if($val['esta'] == 1){ ?>
                                    等待接受
                                    <?php } else if($val['esta'] == 2){ ?>
                                    待确认佣金分配
                                    <?php } else if($val['esta'] == 3){ ?>
                                    确认佣金分配
                                    <?php }else{ ?>
                                    详情
                                    <?php }?>
                                <?php }else if($fun_type == 'accept'){ ?>
                                    <?php if($val['esta'] == 1){ ?>
                                    处理申请
                                    <?php } else if($val['esta'] == 2){ ?>
                                    提交佣金分配
                                    <?php } else if($val['esta'] == 3){ ?>
                                    等待确认
                                    <?php }else{ ?>
                                    详情
                                    <?php }?>
                                <?php }?>
                            <?php } else {?>
                            详情
                            <?php }?>
                        </a>
                        <?php if(!$val['appraise'.$primary_postfix]){?>
                        <?php if((in_array($val['esta'] , array(7,8,9)) || ( ($val['esta'] == 6 || $val['esta'] == 10 || $val['esta'] == 11) && $val['step'] >= 3))
                                 && ($val['brokerid'.$primary_postfix] == $user_arr['broker_id'])){?><a href="javascript:void(0)" class="fun_link" onclick="open_appraise_details('<?php echo $fun_type;?>' , <?php echo $val['id'];?>)">评价</a><?php }?>
                        <?php }else{ echo "已评价";}?>
			<?php if($val['esta']==7){if(isset($val['is_apply'])){if($val['is_apply'] == 1){if($val['status']==0){?>
				审核资料审核中
				<?php }elseif($val['status']==2){?>
				<a href="javascript:void(0);" onclick="$('#js_chushen_pop .iframePop').attr('src','/cooperate/chushen/<?=$val['id'];?>');openWin('js_chushen_pop');">提交审核资料</a>
				<?php }}else{?>
				<a href="javascript:void(0);" onclick="$('#js_chushen_pop .iframePop').attr('src','/cooperate/chushen/<?=$val['id'];?>');openWin('js_chushen_pop');">提交审核资料</a>
			<?php }}}?>
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
<!--右键菜单，不要删除-->
<div class="hide">
<ul style="display:none !important" id="openList"></ul>
</div>
<!--详情弹框-->
<div id="js_pop_box_cooperation" class="iframePopBox" style=" width:920px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="920" height="540" class='iframePop' src=""></iframe>
</div>
<!--填写审核资料痰弹窗-->
<div id="js_chushen_pop" class="iframePopBox" style="width:800px; height:500px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" onclick="search_form.submit();return false;">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
<!--房源注销页弹框-->
<div id="js_pop_box_message" class="iframePopBox">
    <div class="pop_box_g pop_see_inform pop_no_q_up" style="display:block; border:none">
		<div class="hd">
			<div class="title">提示</div>
			<div class="close_pop"><a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a></div>
		</div>
		<div class="mod">
			<div class="inform_inner">
				<div class="up_inner">
					<div class="text-wrap">
						<table>
							<tr>
								<td><div class="img"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></div></td>
								<td class="msg"><span class="bold">该房源已注销</span></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--房源注销页弹框-->
<div id="js_pop_box_appraise_message" class="iframePopBox">
    <div class="pop_box_g pop_see_inform pop_no_q_up" style="display:block; border:none">
		<div class="hd">
			<div class="title">提示</div>
			<div class="close_pop"><a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a></div>
		</div>
		<div class="mod">
			<div class="inform_inner">
				<div class="up_inner">
					<div class="text-wrap">
						<table>
							<tr>
								<td><div class="img"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></div></td>
								<td class="msg"><span class="bold">该合作已评价。</span></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--房源详情页弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--评价弹框-->
<div id="js_pop_box_appraise" class="iframePopBox" style=" width:580px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="580" height="540" class='iframePop' src=""></iframe>
</div>

<!--评价弹框-->
<div id="js_pop_box_appraise1" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--经纪人信用弹框-->
<div class="broker-info-wrap" id="broker_info_wrap"></div>

<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>

<script>
//打开注销提示
function open_appraise_openwin()
{
	$("#js_pop_box_appraise").hide();
	$("#GTipsCoverjs_pop_box_appraise").remove();
	openWin('js_pop_box_appraise_message');
}
//打开注销提示
function open_house_openwin()
{
	$("#js_pop_box_g").hide();
	$("#GTipsCoverjs_pop_box_g").remove();
	openWin('js_pop_box_message');
}
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
    var _url = '<?php echo MLS_URL;?>/cooperate/my_appraise_' + type + '/' + _id;

    if(_url)
    {
         $("#js_pop_box_appraise .iframePop").attr("src",_url);
    }

    openWin('js_pop_box_appraise');
    $("#js_pop_box_cooperation").hide();
    $("#GTipsCoverjs_pop_box_cooperation").remove();
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
