
<div class="bargain-wrap clearfix">
<div class="tab-left"><?=$user_tree_menu?></div>
<div class="forms_scroll h90">
    <form action="" id="addcont_form" method="post">
        <input type="hidden" name="type" value="<?=$type;?>">
    <div class="bargain_top_main">
        <div class="i_box" style=" padding:0;background:#f7f7f7">
	    <div class="clearfix"  style=" padding: 12px 16px;background:#f7f7f7">
		<table width="100%">
		    <thead>
			<tr>
			    <td class="h4">成交信息</td>
			</tr>
		    </thead>
		    <tbody>
                    <tr>
                        <td>
			    <div class="zws_ht_w">
				<ul>
				    <li>
					<span class="zws_border_span">
					    <p class="border_input_title zws_li_p_w "><b class="resut_table_state_1 zws_em ">*</b>成交编号：</p>
					    <div class="input_add_F">
						<input type="text" class="border_color input_add_F zws_W128" value="<?=$bargain['number'];?>" name="number" autocomplete="off">
						<div class="zws_block errorBox"></div>
					    </div>
					</span>

				    </li>
				    <li>
                        <span class="zws_border_span">
                                <p class="border_input_title zws_li_p_w "><b class="resut_table_state_1 zws_em ">*</b>楼盘名称：</p>
                              <div class="input_add_F">
                                <span  class="input_add_F">
                                  <input type="text" class="border_color input_add_F zws_W128"
                                         value="<?= $bargain['block_name']; ?>" name="block_name"
                                         autocomplete="off" <?= $bargain['house_id'] ? 'disabled' : '' ?>>
                                  <input type="hidden" value="<?=$bargain['block_id'];?>" name="block_id">
                                  <div  class="zws_block errorBox"></div>
                                 </span>
                              </div>
                          </span>
				    </li>
                    <li>
                        <span class="zws_border_span">
                            <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em ">*</b>物业地址：</p>
                            <div class="input_add_F">
                            <input type="text" class="border_color input_add_F zws_W128"
                                   value="<?= $bargain['house_addr']; ?>" name="house_addr"
                                   autocomplete="off" <?= $bargain['house_id'] ? 'disabled' : '' ?>>
                            <div  class="zws_block errorBox"></div>
                            </div>
                        </span>
                    </li>
				</ul>
			    </div>
                        </td>
                    </tr>
                    <tr>
			<td>
			    <div class="zws_ht_w">
				<ul>

				    <li>
					<span class="zws_border_span">
					    <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em " >*</b>成交类别：</p>
                         <div class="input_add_F">
                            <select  class="border_color input_add_F zws_li_p_w130" style="height:28px;line-height:28px;background:#FFF;" name="bargain_type" ?>
                            <option value="">请选择</option>
                                <?php foreach ($config['bargain_type'] as $key => $val) { ?>
                                    <option value="<?= $key; ?>" <?= $bargain['bargain_type'] == $key ? 'selected' : ''; ?>><?= $val; ?></option>
                            <?php }?>
                            </select>
                            <div  class="zws_block errorBox"></div>
                         </div>
					</span>

				    </li>
				    <li>
                        <span class="zws_border_span">
                            <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em ">*</b>签约日期：</p>
                            <div class="input_add_F">
                            <input type="text" class="border_color zws_W154 input_add_F time_bg" value="<?=isset($bargain['signing_time'])?date('Y-m-d',$bargain['signing_time']):date('Y-m-d',time());?>" name="signing_time" style="border:1px solid #d1d1d1;" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off" disabled>
                            <div  class="zws_block errorBox"></div>
                        </span>
				    </li>
                    <li>
                        <p class="border_input_title zws_li_p_w"><b class="resut_table_state_1 zws_em ">*</b>成交门店：</p>
                        <div class="input_add_F">
                            <input type="text" name="agency_name_a" value="<?= $bargain['agency_name_a']; ?>"
                                   class="border_color input_add_F zws_W128" autocomplete="off">
                            <input name="agency_id_a" value="<?= $bargain['agency_id_a']; ?>" type="hidden">
                            <span class="zws_block errorBox"></span>
                        </div>
                        <div class="zws_block errorBox"></div>
                    </li>
                    <script type="text/javascript">
                        $(function () {
                            $.widget("custom.autocomplete", $.ui.autocomplete, {
                                _renderItem: function (ul, item) {
                                    if (item.id > 0) {
                                        return $("<li>")
                                            .data("item.autocomplete", item)
                                            .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">' + item.label + '</span></a>')
                                            .appendTo(ul);
                                    } else {
                                        return $("<li>")
                                            .data("item.autocomplete", item)
                                            .append('<a class="ui-corner-all" tabindex="-1">' + item.label + '</a>')
                                            .appendTo(ul);
                                    }

                                },
                                _resizeMenu: function () {
                                    this.menu.element.css({
                                        "max-height": "240px",
                                        "overflow-y": "auto"
                                    });
                                }
                            });
                            $("input[name='agency_name_a']").autocomplete({
                                source: function (request, response) {
                                    var term = request.term;
                                    $("input[name='agency_id_a']").val("");
                                    $.ajax({
                                        url: "/bargain/get_agency_info_by_kw/",
                                        type: "GET",
                                        dataType: "json",
                                        data: {
                                            keyword: term
                                        },
                                        success: function (data) {
                                            //判断返回数据是否为空，不为空返回数据。
                                            if (data[0]['id'] != '0') {
                                                response(data);
                                            } else {
                                                response(data);
                                            }
                                        }
                                    });
                                },
                                minLength: 1,
                                removeinput: 0,
                                select: function (event, ui) {
                                    if (ui.item.id > 0) {
                                        var agencyname = ui.item.label;
                                        var id = ui.item.id;

                                        //操作
                                        $("input[name='agency_id_a']").val(id);
                                        $("input[name='agency_name_a']").val(agencyname);
                                        removeinput = 2;
                                    } else {
                                        removeinput = 1;
                                    }
                                },
                                close: function (event) {
                                    if (typeof(removeinput) == 'undefined' || removeinput == 1) {
                                        $("input[name='agency_name_a']").val("");
                                        $("input[name='agency_id_a']").val("");
                                    }
                                }
                            });
                        });
                    </script>
                    <script type="text/JavaScript">
                        function today() {
                            var today = new Date();
                            var h = today.getFullYear();
                            var m = today.getMonth() + 1;
                            var d = today.getDate();
                            m = m < 10 ? "0" + m : m;   //  这里判断月份是否<10,如果是在月份前面加'0'
                            d = d < 10 ? "0" + d : d;        //  这里判断日期是否<10,如果是在日期前面加'0'
                            return h + "-" + m + "-" + d;
                        }
                    </script>

                </ul>
                </div>
            </td>
                    </tr>
            </tbody>
        </table>
        </div>
            <!--卖方信息-->
            <dl class="sale_message">
                <dd class="aad_pop_pB_10">
                    <img src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/saler_03.png"/>
                    <p>卖方信息</p>
                </dd>
                <dt>
                <div class="aad_pop_p_B10" style="display:inline;">
                      <li>
                        <strong><em class="resut_table_state_1">*</em>卖方姓名：</strong>
                        <b>
                        	<input type="text" class="border_color input_add_F zws_W128" value="<?=$bargain['owner'];?>" name="owner" autocomplete="off" <?=$bargain['house_id']?'disabled':''?>>
                        	<span  class="zws_block errorBox"></span>
                        </b>

                      </li>
                      <li>
                        <strong><em class="resut_table_state_1">*</em>联系方式：</strong>
                        <b>
                        <input type="text" class="border_color input_add_F zws_W128" value="<?=$bargain['owner_tel'];?>" name="owner_tel" autocomplete="off" <?=$bargain['house_id']?'disabled':''?>>
                        <span  class="zws_block errorBox"></span>
                        </b>

                      </li>
                      <li>
                        <strong><em class="resut_table_state_1">*</em>身份证号：</strong>
                        <b>
	                        <input type="text" class="border_color input_add_F zws_W128" value="<?=$bargain['owner_idcard'];?>" name="owner_idcard" autocomplete="off" <?=$bargain['house_id']?'disabled':''?>>
                            <span  class="zws_block errorBox"></span>
                        </b>

                      </li>
                </div>
            </dt>
        </dl>
        <!--买方信息-->
        <dl class="sale_message">
            <dd  class="aad_pop_pB_10">
               <img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/buyer_06.png" />
                <p>买方信息</p>
            </dd>
            <dt>
                    <li>
                        <strong><em class="resut_table_state_1">*</em>买方姓名：</strong>
                        <b>
                        <input type="text" class="border_color input_add_F zws_W128" value="<?=$bargain['customer'];?>" name="customer" autocomplete="off" <?=$bargain['customer_id']?'disabled':''?>>
                        <span  class="zws_block errorBox"></span>
                        </b>
                    </li>
                    <li>
                        <strong><em class="resut_table_state_1">*</em>联系方式：</strong>
                        <b><input type="text" class="border_color input_add_F zws_W128" value="<?=$bargain['customer_tel'];?>" name="customer_tel" autocomplete="off" <?=$bargain['customer_id']?'disabled':''?>><span  class="zws_block errorBox"></span></b>

                    </li>
                    <li>
                        <strong><em class="resut_table_state_1">*</em>身份证号：</strong>
                        <b><input type="text" class="border_color input_add_F zws_W128" value="<?=$bargain['customer_idcard'];?>" name="customer_idcard" autocomplete="off" <?=$bargain['customer_id']?'disabled':''?>>
                            <span  class="zws_block errorBox"></span>
                        </b>

                    </li>
            </div>
            </dt>
        </dl>
    </div>
</div>
<div class="sale_message_h" style="line-height:1px;"></div>
<div style="clear:both;"></div>
    <div class="sale_message_commission" style="margin-bottom:0;">
	    <div class="sale_message_commission_detial" style="display:block;width:100%;float:left;">
                <dl>
                      <dd>成交备注：</dd>
                      <dt><textarea class="zws_textarea" name="remarks"><?=$bargain['remarks'];?></textarea></dt>
                </dl>
           </div>
        </div>
 <!--保存和确认-->
	<div  style="padding-top:10px;clear: both;">
	  <table width="100%">
	    <tr>
	      <td class="zws_center">
		 <input type="hidden" name="submit_flag" value="add">
		 <button type="submit" class="btn-lv1 btn-left">保存</button>
		 <button type="button" class="btn-hui1" onclick="history.go(-1);">取消</button>
	     </td>
	    </tr>
	  </table>
	</div>
    </form>
</div>
</div>


<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"
                                  onclick="location.href=location"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt1"></p>
                        </td>
                    </tr>
                </table>
            <button class="btn JS_Close" type="button" onclick="location.href=location">确定</button>
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
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt2"></p>
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
    function re_width(){
      var h1 = $(window).height();
        var w1 = $(window).width() - 190;
      $(".tab-left, .forms_scroll").height(h1-35);
      $(".forms_scroll").width(w1).show();
    };
    re_width();
    $(window).resize(function(e) {
      re_width();
      $(".sale_message dt").css("width",($(".sale_message").width()-100-36)+"px");
    });


    $(".sale_message dt").css("width",($(".sale_message").width()-100-36)+"px");
     //items   table   隔行换色
    //房源地址输入框宽度
    $(".zws_W60").css("width",($("#zws_first_tr").width()+$("#zws_num").width()-$(this).find(".border_input_title").width())+"px");

    $(".input_add_F").children().click(function(){
        $(this).siblings().removeClass("yesOn");
        $(this).addClass("yesOn");
        $(this).parent().find("input").attr('checked',false);
        $(this).find("input").attr('checked',true);
    })


    $("#zws_choice").css("width",$("#zws_input_w").width()+"px");


});

$(window).resize(function(){
  //房源地址输入框宽度
    $(".zws_W60").css("width",($("#zws_first_tr").width()+$("#zws_num").width()-$(this).find(".border_input_title").width())+"px");

    $("#zws_choice").css("width",$("#zws_input_w").width()+"px");

})

    function open_house_pop(){
        var house_id = $("input[name='house_id']").val();
        $("#js_house_box .iframePop").attr('src','/bargain/get_house/1/'+house_id);
        openWin('js_house_box');
    }

    function get_info(id){
        closeWindowWin('js_house_box');
        if(id){
            $.post(
                '/bargain/get_info',
                {'id':id,
                 'type':1
                },
                function(data){
                    $("input[name='block_id']").val(data['block_id']);
                    $("input[name='block_name']").val(data['block_name']);
                    $("input[name='house_addr']").val(data['address']+data['dong']+'栋'+data['unit']+'单元'+data['door']+'室');
                    $("input[name='house_id']").val(data['house_id']);
                    $("select[name='sell_type']").val(data['sell_type']);
                    $("input[name='buildarea']").val(data['buildarea']);
                    $("input[name='owner']").val(data['owner']);
                    $("input[name='owner_tel']").val(data['telno1']);
                    $("input[name='owner_idcard']").val(data['idcare']);
                    $("input[name='block_name']").attr('disabled','true');
                    $("input[name='house_addr']").attr('disabled','true');
                    $("input[name='house_id']").attr('disabled','true');
                    $("select[name='sell_type']").attr('disabled','true');
                    $("input[name='buildarea']").attr('disabled','true');
                    $("input[name='owner_tel']").attr('disabled','true');
                    $("input[name='owner']").attr('disabled','true');
                    $("input[name='owner_idcard']").attr('disabled','true');
                },'json'
            );
        }else{
            $("input[name='block_id']").val('');
            $("input[name='block_name']").val('');
            $("input[name='house_addr']").val('');
            $("input[name='house_id']").val('');
            $("select[name='sell_type']").val('');
            $("input[name='buildarea']").val('');
            $("input[name='owner']").val('');
            $("input[name='owner_tel']").val('');
            $("input[name='owner_idcard']").val('');
            $("input[name='block_name']").removeAttr('disabled');
            $("input[name='house_addr']").removeAttr('disabled');
            $("input[name='house_id']").removeAttr('disabled');
            $("select[name='sell_type']").removeAttr('disabled');
            $("input[name='buildarea']").removeAttr('disabled');
            $("input[name='owner_tel']").removeAttr('disabled');
            $("input[name='owner']").removeAttr('disabled');
            $("input[name='owner_idcard']").removeAttr('disabled');
        }
    }

    function open_customer_pop(){
        var customer_id = $("input[name='customer_id']").val();
        $('#js_customer_box .iframePop').attr('src','/bargain/get_customer/1/'+customer_id);
        openWin('js_customer_box');
    }

    function get_customer_info(id){
        closeWindowWin('js_customer_box');
        if(id){
            $.post(
                '/bargain/get_customer_info',
                {'id':id,
                 'type':1
                },
                function(data){
                    $("input[name='customer_id']").val(data['customer_id']);
                    $("input[name='customer']").val(data['truename']);
                    $("input[name='customer_tel']").val(data['telno1']);
                    $("input[name='customer_idcard']").val(data['idno']);
                    $("input[name='customer_id']").attr('disabled','true');
                    $("input[name='customer']").attr('disabled','true');
                    $("input[name='customer_tel']").attr('disabled','true');
                    $("input[name='customer_idcard']").attr('disabled','true');
                },'json'
            );
        }else{
            $("input[name='customer_id']").val('');
            $("input[name='customer']").val('');
            $("input[name='customer_tel']").val('');
            $("input[name='customer_idcard']").val('');
            $("input[name='customer_id']").removeAttr('disabled');
            $("input[name='customer']").removeAttr('disabled');
            $("input[name='customer_tel']").removeAttr('disabled');
            $("input[name='customer_idcard']").removeAttr('disabled');
        }
    }

    function open_cooperate_pop(){
        var order_sn = $("input[name='order_sn']").val();
        $('#js_cooperate_box .iframePop').attr('src','/bargain/get_cooperate/1/'+order_sn);
        openWin('js_cooperate_box');
    }

    function get_cooperate_info(id){
        closeWindowWin('js_cooperate_box');
        if(id){
            $.post(
                '/bargain/get_cooperate_info',
                {'id':id
                },
                function(data){
                    $("input[name='order_sn']").val(data['order_sn']);
                    $("input[name='order_sn']").attr('disabled','true');
                },'json'
            );
        }else{
            $("input[name='order_sn']").val('');
            $("input[name='order_sn']").removeAttr('disabled');
        }
    }
</script>

<img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls_guli/js/v1.0&f=openWin.js,house.js,backspace.js"></script>
