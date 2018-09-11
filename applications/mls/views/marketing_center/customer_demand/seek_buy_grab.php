<div class="tab_box" id="js_tab_box">
   	<?php echo $user_menu;?>
</div>

<div id="js_search_box" class="shop_tab_title forms">
    <p class="fr">今日新增<strong class="f00"><?=$today_total;?></strong>条客源</p>
    <?php echo $user_func_menu;?>
    <b class="label labelOn" onclick = "window.location.href='/customer_demand/seek_buy/';">已抢客源</b>
    <a class="wh fl" href="javascript:void(0);" onclick="openWin('js_grab_rule');">查看抢拍规则</a>
</div>

<div class="search_box clearfix" id="js_search_box_02">
    <form name="search_form" id="search_form" method="post" action="">
        <!--<a data-h="0" onclick="show_hide_info(this)" class="s_h" href="javascript:void(0)">展开<span class="iconfont"></span></a>-->
        <input type="hidden" name="is_submit" value="1">
        <div class="fg_box">
            <p class="fg fg_tex">姓名：</p>
            <div class="fg">
                <input type="text" name="realname" id="name" value="<?php echo $post_param['realname'];?>" class="input w110">
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">电话：</p>
            <div class="fg">
                <input type="text" name="phone" id="phone" value="<?php echo $post_param['phone'];?>" class="input w110">
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">意向区属：</p>
            <div class="fg">
                <select class="select" name="district_id">
                    <option value="">不限</option>
                    <?php if($district){
                           foreach($district as $key=>$val){?>
                       <option value="<?php echo $val['id']?>" <?php echo $post_param['district_id']==$val['id']?"selected":"";?>><?php echo $val['district'];?></option>
                    <?php }}?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">户型：</p>
            <div class="fg">
                <select class="select" name="room">
                    <option value="">不限</option>
                    <?php if(isset($hope_room)){foreach($hope_room as $key=>$val){?>
                    <option value="<?php echo $key;?>" <?php echo $post_param['room']==$key?"selected":"";?>><?php echo $val;?></option>
                    <?php }}?>
                </select>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">面积范围：</p>
            <div class="fg">
                <input name="larea" id="area1" onkeyup="check_num()" value="<?php echo $post_param['larea'];?>" class="input w40" type="text">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input name="harea" id="area2" onkeyup="check_num()" value="<?php echo $post_param['harea'];?>" class="input w40" type="text">&nbsp;&nbsp;平米&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="areamin_reminder"></span>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">价格范围：</p>
            <div class="fg">
                <input name="lprice" id="price1" onkeyup="check_num()" value="<?php echo $post_param['lprice'];?>" class="input w60" type="text">
            </div>
            <p class="fg fg_tex fg_tex02">—</p>
            <div class="fg">
                <input name="hprice" id="price2" onkeyup="check_num()" value="<?php echo $post_param['hprice'];?>" class="input w60" type="text">&nbsp;&nbsp;万元&nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="pricemin_reminder"></span>
            </div>
        </div>
        <div class="fg_box">
            <div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');form_submit();return false;"><span class="btn_inner">搜索</span></a> </div>
            <div class="fg"> <a href="javascript:void(0)" class="reset" onclick="javascript:location=location;return false;">重置</a> </div>
        </div>
        <div class="get_page" style="display: none">
            <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
        </div>
    </form>
</div>
<div class="table_all">
   <div class="title shop_title" id="js_title">
        <table class="table">
            <tbody>
                <tr>
                    <td class="c9"><div class="info">姓名</div></td>
                    <td class="c15"><div class="info">电话</div></td>
                    <td class="c15"><div class="info">意向区属</div></td>
                    <td class="c15"><div class="info">户型</div></td>
                    <td class="c15"><div class="info">面积范围（㎡）</div></td>
                    <td class="c15"><div class="info">价格范围</div></td>
                    <td class="c15"><div class="info">发布时间</div></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
            <tbody>
                <?php if($list){foreach($list as $key=>$val) {?>
                <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
                    <td class="c9"><div class="info"><?=$val['realname'];?></div></td>
                    <td class="c15"><div class="info"><?=$val['phone'];?></div></td>
                    <td class="c15"><div class="info"><?=$val['district'];?></div></td>
                    <td class="c15"><div class="info"><?=$val['room'];?>-<?=$val['hall'];?>-<?=$val['toilet'];?></div></td>
                    <td class="c15"><div class="info"><?=$val['larea'];?>-<?=$val['harea'];?></div></td>
                    <td class="c15"><div class="info f60"><?=$val['lprice'];?>万-<?=$val['hprice'];?>万</div></td>
                    <td class="c15"><div class="info"><?=$val['ctime'];?></div></td>
                </tr>
                <?php }}else{?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
                <?php }?>
            </tbody>
        </table>
    </div>
</div>
<div id="js_fun_btn" class="fun_btn fun_btn_bottom clearfix" id="js_search_box">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>


<!--抢拍规则提示-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="width:350px; display:none;" id="js_grab_rule">
    <div class="hd">
        <div class="title">抢拍规则</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
			<p style="padding:10px 0 15px; line-height:24px; text-align:left;">1、委托房源及客户需求自开放后抢拍名额满10个，房源、客源将不能被继续抢拍；<br>
			2、每个人每天最多分别可以抢5条委托房源，5条求购求租；<br>
			3、未认证用户不可参与房源、客源的抢拍；<br>
			4、抢房源、客源成功后可显示用户联系方式。</p>
			<div><button class="btn-lv1 btn-mid JS_Close" type="button">确定</button></div>
		</div>
    </div>
</div>
<script>
    function check_num(){
        var areamin    =    $("input[name='larea']").val();	//最小面积
        var areamax    =    $("input[name='harea']").val();	//最大面积
        var pricemin   =    $("input[name='lprice']").val();	//最小总价
        var pricemax   =    $("input[name='hprice']").val();	//最大总价


        if(!areamin && !areamax){
            $("#areamin_reminder").html("");
            $("input[name='is_submit']").val('1');
        }

        //最小面积
        if(areamin){
            var   type="^\\d+$";
            var   re   =   new   RegExp(type);

            if(areamin.match(re)==null)
            {
                $("#areamin_reminder").html("面积必须为正整数！");
                $("input[name='is_submit']").val('0');
                return;
            }else{
                $("#areamin_reminder").html("");
                $("input[name='is_submit']").val('1');
            }
        }

        //最大面积
        if(areamax){
            var   type="^\\d+$";
            var   re   =   new   RegExp(type);
            if(areamax.match(re)==null)
            {
                $("#areamin_reminder").html("面积必须为正整数！");
                $("input[name='is_submit']").val('0');
                return;
            }else{
                $("#areamin_reminder").html("");
                $("input[name='is_submit']").val('1');
            }
        }

        //最小面积areamin 必须小于 最大面积areamax
        if(areamin && areamax){
            if(parseInt(areamin)>=parseInt(areamax)){
                $("#areamin_reminder").html("面积筛选区间输入有误！");
                $("input[name='is_submit']").val('0');
                return;
            }else{
                $("#areamin_reminder").html("");
                $("input[name='is_submit']").val('1');
            }
        }

        if(!pricemin && !pricemax){
            $("#pricemin_reminder").html("");
            $("input[name='is_submit']").val('1');
        }
        //最小总价
        if(pricemin){
            var   type="^\\d+$";
            var   re   =   new   RegExp(type);
            if(pricemin.match(re)==null)
            {
                $("#pricemin_reminder").html("价格必须为正整数！");
                $("input[name='is_submit']").val('0');
                return;
            }else{
                $("#pricemin_reminder").html("");
                $("input[name='is_submit']").val('1');
            }
        }

        //最大总价
        if(pricemax){
            var   type="^\\d+$";
            var   re   =   new   RegExp(type);
            if(pricemax.match(re)==null)
            {
                $("#pricemin_reminder").html("价格必须为正整数！");
                $("input[name='is_submit']").val('0');
                return;
            }else{
                $("#pricemin_reminder").html("");
                $("input[name='is_submit']").val('1');
            }
        }

        //最小总价pricemin 必须小于 最大总价pricemax
        if(pricemin && pricemax){
            if(parseInt(pricemin)>=parseInt(pricemax)){
                $("#pricemin_reminder").html("价格筛选区间输入有误！");
                $("input[name='is_submit']").val('0');
                return;
            }else{
                $("#pricemin_reminder").html("");
                $("input[name='is_submit']").val('1');
            }
        }
    }
    //通过参数判断是否可以被提交
    function form_submit(){
        var is_submit = $("input[name='is_submit']").val();
        if(is_submit ==1){
            $('#search_form').submit();
        }
    }
</script>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js "></script>
