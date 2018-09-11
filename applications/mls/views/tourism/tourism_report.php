<script>
    window.parent.addNavClass(23);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="oversea_main">
    <!--搜索-->
    <dl class="oversea_contract">
        <dd>
            <form action="" method="post" name="search_form" id="search_form">
                <input class="sea_dd_input" name = "keyword" value="<?=$post_param['keyword']?$post_param['keyword']:'请输入客户名或手机号码';?>" type="text" onfocus="if (value == '请输入客户名或手机号码') { value = '' }" onblur="    if (value == '') { value = '请输入客户名或手机号码' }">
                <input value="1" type="hidden" name="page">
                 <div class="fg"><p class="sea_dd_sub"  onclick="$('#search_form').submit();return false;">搜索</p></div>
                <div class="fg"> <a href="/tourism_report" class="sea_dd_sub">重置</a> </div>
            </form>
        </dd>
        <dt><a href="javascript:void(0);" class="sea_dd_sub" <?php if($group_id==2){?>onclick="openWin('js_report_pop');"<?php }else{?>onclick="permission_none('您的帐号尚未认证');"<?php }?>>客户报备</a></dt>
    </dl>
    <!--合同报备列表-->
    <div class="oversea_contract_list">
        <table class="oversea_table" align="center" border="0" cellspacing="0">
            <thead>
                <tr class="tr_underline">
                    <td class="oversea_tdw1 td_pad">序号</td>
                    <td class="oversea_tdw1 td_pad">客户姓名</td>
                    <td class="oversea_tdw1 td_pad">手机号码</td>
                    <td class="oversea_tdw6 td_pad">意向房产信息</td>
                    <td>状态</td>
                </tr>
            </thead>
            <tbody>
                <?php if($list){foreach($list as $key=>$val){?>
                <tr class="tr_underline">
                    <td class="oversea_tdw1 td_pad"><?=sprintf("%02d", $key+1);?></td>
                    <td class="oversea_tdw1 td_pad"><?=$val['user_name'];?></td>
                    <td class="oversea_tdw1 td_pad"><?=$val['user_phone'];?></td>
                    <td class="oversea_tdw6 td_pad"><?=$val['house_info'];?></td>
                    <?php if($val['status']==1){?>
                    <td class="td_state td_position"><?=$val['status_name'];?></td>
                    <?php }elseif($val['status']==6){?>
                    <td class="td_state_deal td_position"><?=$val['status_name'];?></td>
                    <?php }elseif($val['status']==3){?>
                    <td class="td_state_fail td_position">
                        <?=$val['status_name'];?>
                        <span style="margin-top: 1px;" class="td_state_fail_img"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico2.png"></span>
                        <div style="bottom: 35px;" class="td_state_fail_remind">审核不通过原因： <?=$val['reason'];?></div>
                    </td>
                    <?php }else{?>
                    <td class="td_state_ok td_position"><?=$val['status_name'];?></td>
                    <?php }?>
                </tr>
                <?php }}else{?>
                <tr class="tr_underline tr_bg"><td class="oversea_tdw1 td_pad" colspan="5"><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
                <?php }?>
            </tbody>
        </table>

    </div>
    <!--分页-->
    <div class="over_sea_page oversea_dl_f">
        <div class="get_page">
            <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
        </div>
    </div>
</div>

<div class="sea_pop" style="display: none;" id="js_report_pop">
    <span class="sea_pop_title"><b style="float:left;width:auto;display:inline;">客户报备</b><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" style="float:right;color:#fff"></a></span>
    <div class="sea_pop_con">
        <li class="sea_color">请填写您的客户报备信息</li>
        <li>
            <dl class="sea_pop_con_dl">
                <dd>姓名：</dd>
                <dt><input type="text" class="sea_input" id="user_name"></dt>
            </dl>
            <dl class="errorBox" id="name_error" style="display: none"></dl>
        </li>
        <li>
            <dl class="sea_pop_con_dl">
                <dd>电话：</dd>
                <dt><input type="text" class="sea_input" id="user_phone" onkeyup="value=value.replace(/[^\d-]/g,'')"></dt>
            </dl>
            <dl class="errorBox" id="phone_error" style="display: none"></dl>
        </li>
        <li class="sea_margin sea_align"><a class="sea_btn_sub" href="javascript:void(0);" onclick="add_report();">立即报备</a>
    </li></div>
</div>


<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                        <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
                        <p class="left" style="font-size:14px;color:#666;">报备成功</p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button" onclick="location=location">确定</button>
            </div>
         </div>
    </div>
</div>

<!--操作失败弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_false">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                        <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
                        <p class="left" style="font-size:14px;color:#666;">报备失败！</p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $(".sea_input").on('focus',function(){
            $(this).parent().parent().siblings('.errorBox').hide();

        })
		//隔行变色

		for(var x= 0; x <$(".oversea_table tr").length;x++){
			if((x+1)%2==0){

				$(".oversea_table tr").eq(x).find("td").css("background","#fcfcfc");
			}
			else{

				$(".oversea_table tr").eq(x).find("td").css("background","#f0f0f0");
			}
		}
            //行高计算
        function rHeight() {


			var aBody_H = $(window).height()-40;
			$(".oversea_main").css("height",aBody_H+"px");
			//$(".oversea_main").css("overflow-y","auto");
               //alert($(".oversea_main").css("height"));


            for (var i = 0; i < $(".td_state_fail").length ; i++) {
                var aTd_H = $(".td_state_fail").eq(i).prev("td").height();
                $(".td_state_fail").eq(i).find("span").css("margin-top", (aTd_H - 16) / 2 + "px");
                $(".td_state_fail").eq(i).find("div").css("bottom", (aTd_H/2+26) + "px");
            }


         }
        rHeight();
       $(".td_state_fail").hover(function () {
            $(this).children("span").addClass("td_state_fail_jt");
            $(this).children("div").fadeIn();
        },function(){
            $(this).children("span").removeClass("td_state_fail_jt");
            $(this).children("div").fadeOut();

        })
        //窗口调整时实时调整高度
       $(window).resize(function () {
           rHeight();
       })

        $(".sea_input").click(function(){
            $(this).parent().parent().siblings('.errorBox').css('display','none');
        })
    })

    function add_report(){
        var name = $('#user_name').val();
        var phone = $('#user_phone').val();
        var phonereg = /(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/;
        if(name ==''){
            $("#name_error").text('请填写姓名');
            $("#name_error").css('display','block');
            return false;
        }
        if(phone ==''){
            $("#phone_error").text('请填写电话号码');
            $("#phone_error").css('display','block');
            return false;
        }

        if(!phonereg.test(phone)){
            $("#phone_error").text('请正确填写电话号码');
            $("#phone_error").css('display','block');
            return false;
        }

        if(name && phone){
            $.ajax({
                url:"/tourism_report/add",
                type:"post",
                dataType:"json",
                data:{
                   name:name,
                   phone:phone
                },
                success: function(data){
                    if(data['result']==1){
                        closeWindowWin('js_report_pop');
                        openWin('js_pop_success');
                    }else{
                        openWin('js_pop_false');
                    }
                }
            })
        }
    }
</script>




