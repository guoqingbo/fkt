<script>
    window.parent.addNavClass(21);
</script>
<div class="contract-wrap clearfix">
<!--left 菜单部分-->
	<div class="tab-left"><?=$user_tree_menu?></div>

<!--右侧内容部分-->
<div class="forms_scroll h90"  style="overflow-y:hidden;">

    <div class="shop_tab_title scr_clear" id="js_search_box" style="margin:0 15px 10px 15px;">
  <!--<a href="/contract/modify_contract/<?=$type?>" class="btn-lv fr"><span>+ 录入新合同</span></a>-->
	    <a href="/contract/contract_review/1" class="link <?=$type==1?'link_on':''?>"><span class="iconfont hide"></span>出售</a>
	    <a href="/contract/contract_review/2" class="link <?=$type==2?'link_on':''?>"><span class="iconfont hide"></span>出租</a>
	</div>
        <!-- 上部菜单选项，按钮-->
        <form name="search_form" id="subform" method="post" action="">
            <div class="search_box clearfix" id="js_search_box_02">
                <div style="width:100%;display:block; float:left; display:inline;">
                    <div class="fg_box">
			<p class="fg fg_tex">合同编号：</p>
			<div class="fg">
			    <input type="text" value="<?=$post_param['number'];?>" class="input w90 ui-autocomplete-input" autocomplete="off" name="number">
			</div>
		    </div>
            <div class="fg_box">
                <p class="fg fg_tex">签约门店：</p>

                <div class="fg mr10" style="*padding-top:10px;">
                    <select class="select w80" name="agency_id_a" id="sign_agency">
                    <?php foreach($agencys as $key =>$val){?>
                    <option value="<?=$val['id'];?>" <?=$post_param['agency_id_a']==$val['id']?'selected':'';?>><?=$val['name'];?></option>
                    <?php }?>
                    </select>
                </div>
		    </div>
		    <div class="fg_box">
                <p class="fg fg_tex">签约人：</p>

                <div class="fg mr10" style="*padding-top:10px;">
                    <select class="select w80" name="broker_id_a" id="sign_broker">
                    <?php foreach($brokers as $key =>$val){?>
                    <option value="<?=$val['broker_id'];?>" <?=$post_param['broker_id_a']==$val['broker_id']?'selected':'';?>><?=$val['truename'];?></option>
                    <?php }?>
                    </select>
                </div>
		    </div>
		    <script>
			$("#sign_agency").change(function(){
			    var agency_id = $('#sign_agency').val();
			    if(agency_id){
                    $.ajax({
                        url:"/contract_earnest_money/broker_list",
                        type:"GET",
                        dataType:"json",
                        data:{
                           agency_id:agency_id
                        },
                        success:function(data){
                        var html = "<option value=''>请选择</option>";
					    if(data['result'] == 1){
					        for(var i in data['list']){
						        html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
					        }
					    }
                        $('#sign_broker').html(html);
                        }
                    })
			    }else{
                    $('#sign_broker').html("<option value=''>请选择</option>");
			    }
			})
		    </script>
		    <div class="fg_box">
                <p class="fg fg_tex">签约时间：</p>
                <div class="fg">
                    <input type="text" class="fg-time" name="start_time" value="<?=$post_param['start_time'];?>" autocomplete="off" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" onchange="check_num();">
                </div>
                <div class="fg fg_tex03">—</div>
                <div class="fg fg_tex03">
                <input type="text" class="fg-time" name="end_time" value="<?=$post_param['end_time'];?>" autocomplete="off" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" onchange="check_num();">
                &nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="time_reminder"></span>
                </div>
		    </div>
		    <div class="fg_box">
                <p class="fg fg_tex">状态：</p>

                <div class="fg mr10" style="*padding-top:10px;">
                    <select class="select w80" name="is_check">
                    <option value=''>请选择</option>
                    <?php foreach($config['is_check1'] as $key =>$val){?>
                    <option value="<?=$key;?>" <?=$post_param['is_check']==$key?"selected":"";?>><?=$val;?></option>
                    <?php }?>
                    </select>
                </div>
		    </div>
                    <div class="fg_box">
                        <input type="hidden" name="page" value="1">
                        <input type="hidden" name="is_submit" value="1">
                        <input type="hidden" id="contract_id">
                        <div class="fg"> <a href="javascript:void(0)" onclick="$('#subform :input[name=page]').val('1');form_submit();return false;" class="btn"><span class="btn_inner">搜索</span></a> </div>
                        <div class="fg"> <a href="/contract/contract_review/<?=$type;?>" class="reset">重置</a> </div>
                    </div>
                </div>
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
        </form>
        <!-- 上部菜单选项，按钮---end-->
        <div class="fun_btn clearfix count_info_border" id="js_fun_btn" style="float:left;padding:10px;border-top:none; display:inline;margin:0;margin-bottom:10px;margin-top:8px;margin-left:14px;">
            <div class="count_info count_info_float">
              <table>
                <tr>
                  <td style="padding-right:20px;">
                    共有<span class="bold highlight color_fontsize">
                      <?=$total;?>
                    </span>条待审核合同
                  </td>
                </tr>
              </table>
            </div>
            <div class="get_page">
              <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
            </div>
          </div>

   <div style="clear:both;"></div>
    <div class="table_all">
        <div class="title" id="js_title" >
            <table class="table">
                <tr>
                    <td class="c_table_title_1"><div class="info">合同编号</div></td>
                    <td class="c_table_title_2"><div class="info">房源地址</div></td>
                    <td class="c_table_title_3"><div class="info">业主</div></td>
                    <td class="c_table_title_3"><div class="info">客户</div></td>
                    <td class="c_table_title_3"><div class="info">面积<br/>(m&sup2;)</div></td>
                    <td class="c_table_title_3"><div class="info">成交价<br/><?php if ($type == 1) {echo '(W)';}else {echo '元/月';}?></div></td>
                    <td class="c_table_title_4"><div class="info">签约门店</div></td>
                    <td class="c_table_title_3"><div class="info">签约人</div></td>
                    <td class="c_table_title_3" style="width:8%;"><div class="info">状态</div></td>
                    <td class="c_table_title_5">操作</td>
                </tr>
            </table>
        </div>
        <!--列表-->
       <div class="inner" id="js_inner">
            <table class="table list-table cont_list_bottom_solid" align="center" style="*+width:98.5%;*+padding:0 1.5% 0 0;_width:98.5%;_padding:0 1.5% 0 0;">
                <?php if($list){foreach($list as $key=>$val){?>
                <tr id="contract_list">
                    <td class="c_table_title_1"  style="width:9.5%;"><div class="info"><!--<b class="sale_Bg"><?=$type==1?'售':'租'?></b>&nbsp;--><a href="/contract/contract_detail/<?=$val['id'];?>"><?=$val['number'];?></a></div></td>
                    <td class="c_table_title_2" style="width:18%;"><div class="info"><?=$val['house_addr'];?></div></td>
                    <td class="c_table_title_3" style="width:6%;"><div class="info"><?=$val['owner'];?></div></td>
                    <td class="c_table_title_3" style="width:8%;"><div class="info"><?=$val['customer'];?></div></td>
                    <td class="c_table_title_3" style="width:6%;"><div class="info"><?=strip_end_0($val['buildarea']);?></div></td>
                    <td class="c_table_title_3" style="width:6%;"><div class="info"><?=strip_end_0($val['price']);?></div></td>
                    <td class="c_table_title_4" style="width:14%;"><div class="info"><?=$val['agency_name_a'];?></div></td>
                    <td class="c_table_title_3"><div class="info"><?=$val['broker_name_a'];?></div></td>
                    <td class="c_table_title_3">
                        <div class="info <?php if($val['is_check']==1){echo 'c999';}elseif($val['is_check']==2){echo 'c680';}else{echo 'f00';}?>"><?=$config['is_check1'][$val['is_check']];?></div>
                    </td>
                    <td class="c_table_title_5">
                        <?php if($val['is_check']==1){?>
                        <a href="javascript:void(0);" <?php if($auth['review']['auth']){?>onclick="$('#contract_id').val(<?=$val['id'];?>);openWin('js_review_pop');"<?php }else{?>onclick="permission_none();"<?php }?>>审核</a>
                        <?php }elseif($val['is_check']==2){?>
                        <a href="javascript:void(0);" <?php if($auth['fanreview']['auth']){?>onclick="$('#contract_id').val(<?=$val['id'];?>);openWin('js_cancel_review_pop');"<?php }else{?>onclick="permission_none();"<?php }?>>反审核</a>
                        <?php }?>
                    </td>
                </tr>
                <?php }}else{?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
                 <?php }?>
            </table>
        </div>
    </div>

    <script type="text/javascript">
    window.onload=function(){

       $(function(){
      //$("#zws_js_inner").css("height",($("#js_inner").height()-110)+"px");
      //console.log($("#js_inner").height()-45);
      //
        $("#js_inner").css("height",($("#js_inner").height()-47)+"px");
       $("#js_fun_btn").css("width", ($(".forms_scroll").width()-50)+"px");
      })
    }




$(window).resize(function(){
$("#js_inner").css("height",($("#js_inner").height()-47)+"px");
 // $("#js_inner").css("height",($("#js_inner").height()-45)+"px");
  $("#js_fun_btn").css("width", ($(".forms_scroll").width()-50)+"px");
})

</script>
    </div>
</div>

<!--审核操作弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_review_pop">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <!-- <table>
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                            审核结果:
                        </td>
                        <td>
                            <input type ="radio" name="review_type" value="2" checked>通过
                            <input type ="radio" name="review_type" value="3">不通过
                        </td>
                    </tr>
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                            审核理由:
                        </td>
                        <td>
                            <textarea name="review_remark"></textarea>
                        </td>
                    </tr>
                </table> -->

                 <table>
                <tr>
                  <td width="70" class="label"><font class="red">*</font>审核结果：</td>
                            <td>
                              <div class="input_add_F" style="padding-right:10px;padding-bottom:8px;"><input  type ="radio" name="review_type" value="2" checked>通过</div>
                              <div class="input_add_F" style="padding-bottom:8px;"><input type ="radio" name="review_type" value="3">不通过</div>
                            </td>
                </tr>
                        <tr   style="padding:10px 0;">
                      <td width="70" class="label">审核结果：</td>
                            <td>
                              <textarea style="background:#fcfcfc;width:180px;height:70px;border:1px solid #e6e6e6;"  name="review_remark"></textarea>
                            </td>
                        </tr>
                        <tr>
                         <!--  <td colspan="2" class="center">

                    <button type="button" onclick="openWin('js_pop_warning');" id="dialog_share" class="btn-lv1 btn-left JS_Close">确定</button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>

                          </td> -->
                        </tr>
              </table>

                <button style="margin-top:14px;" class="btn" type="button" onclick="openWin('js_pop_warning');">确定</button>
                <button style="margin-top:14px;"  class="btn btn_none JS_Close" type="button">取消</button>
            </div>
         </div>
    </div>
</div>

<div id="js_pop_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;审核完成后合同将不可修改<br/>是否确定当前操作？</p>
		<button type="button" class="btn JS_Close" onclick="review_this();">确定</button>
		<button type="button" class="btn btn_none JS_Close">取消</button>
	    </div>
	</div>
    </div>
</div>

<div id="js_cancel_review_pop" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">&nbsp;&nbsp;当前合同已生效，反审核后合同将可修改，是否确定此操作</p>
		<button type="button" class="btn JS_Close" onclick="cancel_this();">确定</button>
		<button type="button" class="btn btn_none JS_Close">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="location=location;return false;"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt1"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button" onclick="location=location;return false;">确定</button>
            </div>
         </div>
    </div>
</div>

<!--审核不通过操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_success1">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="location=location;return false;"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/error_ico.png"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt3"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button" onclick="location=location;return false;">确定</button>
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
                            <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;"  id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>


<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js "></script>

<script>
  $(function () {
  function re_width(){
  var h1 = $(window).height();
  var w1 = $(window).width() - 180;
  $(".tab-left, .forms_scroll").height(h1-65);



  $(".forms_scroll").width(w1).show();
  };
  re_width();
  $(window).resize(function(e) {
  re_width();
  });


  $(".cont_list_bottom_solid tr:odd").addClass("bg");

  });

  //通过参数判断是否可以被提交
  function form_submit(){
  var is_submit = $("input[name='is_submit']").val();
  if(is_submit ==1){
  $('#subform').submit();
  }
  }

  //审核该条合同
  function review_this(){
  var contract_id = $('#contract_id').val();
  $.ajax({
  url:"/contract/sure_review",
  type:"POST",
  dataType:"json",
  data:{
  id:contract_id,
  review_type:$("input[name='review_type']:checked").val(),
  review_remark:$("textarea[name='review_remark']").val()
  },
  success:function(data){
  var type = $("input[name='review_type']:checked").val();
  $("#js_review_pop").hide();
  $("#GTipsCoverjs_review_pop").remove();
  if(data['result'] == 'ok'){
  if(type ==2){
  $('#js_prompt1').text('审核通过！');
  openWin('js_pop_success');
  }else{
  $('#js_prompt3').text('审核不通过！');
  openWin('js_pop_success1');
  }
  }else{
  $('#js_prompt2').text('审核失败！');
  openWin('js_pop_false');
  }
  }
  })
  }

  //反审核该条合同
  function cancel_this(){
  var contract_id = $('#contract_id').val();
  $.ajax({
  url:"/contract/cancel_review",
  type:"POST",
  dataType:"json",
  data:{
  id:contract_id
  },
  success:function(data){
  if(data['result'] == 'ok'){
  $('#js_prompt1').text('反审核成功！');
  openWin('js_pop_success');
  }else{
  $('#js_prompt2').text('反审核失败！');
  openWin('js_pop_false');
  }
  }
  })
  }

  //合同列表页 排序
  function list_order(id)
  {
  var orderby_id = $("input[name='orderby_id']").val();
  var other_id = id + 1;
  if( orderby_id == id )
  {
  $("input[name='orderby_id']").val(other_id);
  $("#subform").submit();
  }
  else
  {
  $("input[name='orderby_id']").val(id);
  $("#subform").submit();
  }
  }

  function check_num(){
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
</script>


