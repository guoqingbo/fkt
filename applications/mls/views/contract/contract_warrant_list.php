<div class="contract-wrap clearfix">
<div class="tab-left"><?=$user_tree_menu?></div>
<!--右侧内容部分-->

<div style="height: 452px;" class="forms_scroll h90">
        <!-- 上部菜单选项，按钮-->
        <form name="search_form" id="search_form" method="post" action="">
            <div class="search_box clearfix" id="js_search_box_02" style="margin: 5px 0 10px;overflow: hidden">
                <div style="width:100%;display:block; float:left; display:inline;">
                    <div class="fg_box">
                        <p class="fg fg_tex">合同编号：</p>
                        <div class="fg">
                            <input type="text" value="<?=$post_param['number'];?>" class="input w90 ui-autocomplete-input" autocomplete="off" name="number">
                        </div>
                    </div>
                     <div class="fg_box">
                        <p class="fg fg_tex">楼盘名称：</p>
                        <div class="fg">
                            <input type="text" name="block_name" value="<?=$post_param['block_name'];?>" class="input w120 ui-autocomplete-input" autocomplete="off"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                            <input name="block_id" value="<?=$post_param['block_id'];?>" type="hidden">
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
                    });
                    </script>

                    <div class="fg_box">
                        <p class="fg fg_tex">签约门店：</p>

                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" style="width:210px" name="agency_id_a" value="<?=$post_param['agency_id_a'];?>" id="sign_agency">
                            <?php foreach($agencys as $key =>$val){?>
                            <option value="<?=$val['id'];?>" <?=$post_param['agency_id_a']==$val['id']?'selected':''?>><?=$val['name'];?></option>
                            <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <p class="fg fg_tex">签约人：</p>

                        <div class="fg mr10" style="*padding-top:10px;">
                            <select class="select w80" name="broker_id_a" value="<?=$post_param['broker_id_a'];?>" id="sign_broker">
                            <?php if($brokers){foreach($brokers as $key =>$val){?>
                            <option value="<?=$val['broker_id'];?>" <?=$post_param['broker_id_a']==$val['broker_id']?'selected':''?>><?=$val['truename'];?></option>
                            <?php }}?>
                            </select>
                        </div>
                    </div>
                    <div class="fg_box">
                        <p class="fg fg_tex">签约时间：</p>
                        <div class="fg">
                            <input type="text" class="fg-time" name="start_time" value="<?=$post_param['start_time'];?>" autocomplete="off" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" onchange="check_num();" style="padding-right:16px;">
                        </div>
                        <div class="fg fg_tex03">—</div>
                        <div class="fg fg_tex03">
                        <input type="text" class="fg-time" name="end_time" value="<?=$post_param['end_time'];?>" autocomplete="off" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" onchange="check_num();"  style="padding-right:16px;">
                        &nbsp;&nbsp;<span style="font-weight:bold;color:red;" id="time_reminder"></span>
                        </div>
                    </div>

                    <div class="fg_box">
                        <input type="hidden" name="page" value="1">
                        <input type="hidden" name="is_submit" value="1">
                        <div class="fg"> <a href="javascript:void(0)" onclick="$('#subform :input[name=page]').val('1');form_submit();return false;" class="btn"><span class="btn_inner">搜索</span></a> </div>
                        <div class="fg"> <a href="/contract/warrant_list" class="reset">重置</a> </div>
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
        <span class="zws_h5"><a href="javascript:void(0);" class=" title_bg_yj" onclick="judge_company_template();">权证流程模板</a></span>
        <?php if($list){foreach($list as $key=>$val){?>
        <table class=" zws_border_none" style="text-align:center;width:100%;" align="center" >
            <tbody class="zws_bs_bg">
            <tr>
                <td style="width:15%;padding:20px 1% 0;text-align:left;" class="zws_border_bold aad_pop_p_T20">合同编号：<a href="/contract/contract_detail/<?=$val['id'];?>"><?=$val['number'];?></a></td>
                <td style="width:26%;padding:20px 1% 0;text-align:left;"  class=" aad_pop_p_T20">成交房源：<?=$val['house_addr'];?></td>
                <td style="width:16%;padding:20px 1% 0;text-align:left;"  class=" aad_pop_p_T20">签约门店：<?=$val['agency_name_a'];?></td>
                <td style="width:12%;padding:20px 1% 0;text-align:left;"  class=" aad_pop_p_T20">签约人：<?=$val['broker_name_a'];?></td>
                <td style="width:14%;padding:20px 1% 0;text-align:left;"  class=" aad_pop_p_T20">签约日期：<?=date('Y-m-d',$val['signing_time']);?></td>
            </tr>
            <tr>
                <td colspan="5">
                    <div class="warrant_process">
                        <ul class="input_add_F zws_process_center" style="width:auto;float:left; display:inline;">
                            <?php if($val['warrant_list']){foreach($val['warrant_list'] as $k => $v){?>
                                <?php if($v['isComplete']==1){?>
                                    <li class="warrant_process_bg1" title="<?=$v['stage_name1'];?>"  onclick="view_detail(<?=$v['id'];?>);">
                                    <p style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;line-height:22px;"><?=$v['stage_name2'];?></p>
                                    <span><?=$v['complete_broker_name'];?>　<?=$v['complete_time'];?></span>
                                    </li>
                                <?php }else{?>
                                    <li class="warrant_process_bg3" title="<?=$v['stage_name1'];?>" onclick="view_detail(<?=$v['id'];?>);">
                                        <p style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;line-height:22px;"><?=$v['stage_name2'];?></p>
                                    </li>
                                <?php }?>
                                    <li class="warrant_process_bg2">
                                    <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/qz_process5_10.gif" />

                                    </li>
                            <?php }}?>
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
        </table>
        <?php }}else{?>
        <table class="zws_W50 zws_border_none" style="text-align:center;" align="center">
            <tbody><tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            </tbody>
        </table>
        <?php }?>
     <span class="zws_border_bottom"></span>
    <!--翻页-->
    <div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn" style="margin:8px 0 0 0;">
        <div class="get_page" style="position:static">
            <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
        </div>
    </div>
</div>
</div>

<div style="display:none" style="width:600px;" class="delate_btn1 qz_moudle_W600" id="js_template_pop1">
    <dl class="title_top">
        <dd>权证流程模板</dd>
        <dt class="JS_Close">X</dt>
    </dl>
    <div class="qz_moudle_con1">您还没有设置任何权证流程模板哦！<a href="javascript:void(0)" onclick="$('input[name=template_name]').val('');openWin('js_template_pop');">新建模板</a></div>

    <dl class="qz_moudle_con1_img" style="width:550px;">
        <dd>系统默认模板</dd>
        <dt>
             <div class="warrant_process L25">
                <ul>
                    <?php foreach($default_temp['steps'] as $k =>$v){?>
                    <li class="warrant_process_bg4">
                        <p title="<?=$v['stage_name1'];?>"><?=$v['stage_name2'];?></p>

                    </li>
                    <li class="warrant_process_bg2">
                        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/qz_process5_10.gif">
                    </li>
                    <?php }?>
                </ul>
            </div>
        </dt>
    </dl>
</div>

<!--权证步骤详情弹框-->
<div id="js_warrant_pop" class="iframePopBox" style="width: 400px;height:250px;border:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="400px" height="300px" class='iframePop' src="" id="warrant"></iframe>
</div>

<!--权证步骤详情弹框-->
<div id="js_warrant_pop1" class="iframePopBox" style="width: 342px;height:252px;border:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="342px" height="252px" class='iframePop' src="" id="warrant1"></iframe>
</div>

<!--新建模版弹窗-->
<div class="delate_btn1 qz_moudle_H500" style="display: none;width:300px;height:200px;margin-top:-100px;margin-left:-150px" id="js_template_pop">
    <dl class="title_top">
        <dd>新建权证流程模板</dd>
        <dt class="JS_Close">X</dt>
    </dl>
    <div class="qz_moudle_con2">
        <p>模版名称：<input type="text" class="qz_moudle_text" name="template_name" maxlength="8"></p>
        <div class="qz_moudle_con1">
          <a href="javascript:void(0)" onclick="save_template(2);" class="JS_Close">下一步</a>
          <a href="javascript:void(0)" class="JS_Close" style="margin:0 0 0 10px;background:#fafafa;color:#000;border:1px solid #dcdcdc;border-radius:2px;">取　消</a>
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
                <button class="btn" type="button" onclick="location=location;return false;">确定</button>
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
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>

<!--选择模板弹框-->
<div id="js_template_pop2" class="iframePopBox" style="width: 840px;height:504px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="842" height="502" class='iframePop' src="" name="template_pop"></iframe>
</div>

<!--编辑模板弹框-->
<div id="js_edit_template_pop" class="iframePopBox" style="width: 840px;height:504px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" onclick ="reload_iframe()">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="842" height="502" class='iframePop' src=""></iframe>
</div>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->
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
  //$(".zws_process_center").css("padding-left",($(".warrant_process").width()-$(".zws_process_center").width())/2+"px");

  for(var n= 0;n< $(".zws_border_none").length;n++){

        if((n+1)%2==0){
           $(".zws_border_none").eq(n).css("background","#f7f7f7");
           //alert(n);
        }
    }

  });




    //文字高度获取
    //
    for(var x = 0 ; x < $(".warrant_process_bg1").length ; x++){

        //console.log($(".warrant_process_bg1").find("p").eq(x).height())
        //
        $(".warrant_process_bg1").find("p").eq(x).css("padding-top",(107-($(".warrant_process_bg1").find("p").eq(x).height()))/2+"px");
    }


    var zws_bg_temp="";
    $(".zws_border_none").hover(

      function () {
         zws_bg_temp = $(this).css("background-color");
        // alert(zws_bg_temp);
        $(this).css("background","#edf7fe");


      },

      function () {

        //alert(zws_bg_temp);
        $(this).css("background",zws_bg_temp);

      }

    );

var zws_table_W  = $(".forms_scroll").width(); //表格宽度
var zws_table_con = $(".zws_process_center").width(); //内容区域宽度

//alert( zws_table_con);
//$(".zws_process_center").css("padding-left",(zws_table_W-zws_table_con)/2+"px");

 $('.warrant_process').each(function(){
     $(this).find("li:last").remove();
 })

});

    function check_num(){
        var timemin    =    $("input[name='starttime']").val();	//最小面积
        var timemax    =    $("input[name='endtime']").val();	//最大面积

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

    //通过参数判断是否可以被提交
    function form_submit(){
        var is_submit = $("input[name='is_submit']").val();
        if(is_submit ==1){
            $('#subform').submit();
        }
    }

    function judge_company_template(){
        $.ajax({
            url:"/contract/judge_company_template",
            type:"POST",
            dataType:"json",
            success:function(data){
                if(data['result'] == 'ok'){
                    $("#js_template_pop2").find(".iframePop").attr('src','/contract/warrant_template');
                    openWin('js_template_pop2');
                }else{
                    openWin('js_template_pop1');
                }
            }
        })
    }

    function open_template_edit(id,key){
        $("#js_edit_template_pop").find(".iframePop").attr('src','/contract/warrant_template_add/'+id+'/'+key);
        openWin('js_edit_template_pop');
    }

    function reload_iframe(){
        template_pop.window.location=template_pop.window.location;
    }

    function save_template(key){
        $.ajax({
            url:"/contract/save_template",
            type:"POST",
            dataType:"json",
            data:{
                template_name:$("input[name='template_name']").val()
            },
            success:function(data){
                if(data['result'] == 'ok'){
                    $("#js_template_pop1").hide();
                    $("#GTipsCoverjs_template_pop1").remove();
                    $("#js_edit_template_pop .iframePop").attr('src','/contract/warrant_template_add/'+data['data']+"/"+key);
                    openWin('js_edit_template_pop');
                }else{
                    $('#js_prompt2').text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        })
    }

    function view_detail(id){
        $.post("/contract/warrant_detail",{id:id},function(data){
            if(data['warrant_list']['is_remind']==1){
                $('#warrant').attr('src','/contract/contract_warrant_detail/'+id);
                openWin('js_warrant_pop');
            }else{
                $('#warrant1').attr('src','/contract/contract_warrant_detail/'+id);
                openWin('js_warrant_pop1');
            }
        },"json");
    }
</script>
