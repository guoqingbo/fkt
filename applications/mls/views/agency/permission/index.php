<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js,openWin.js"></script>
<script>
    window.parent.addNavClass(17);
</script>
<div class="tab_box" id="js_tab_box">
	<?php echo $user_menu;?>
</div>

<div class="limit-set-out clearfix">
<div class="limit-set">
	<div class="limit-right" id="content" style="overflow-y:auto;position:relative;">
			<a class="zws_power_btn" href="javascript:void(0);" onclick = "$('#js_rule_pop .iframePop').attr('src','/permission/permission_rule');openWin('js_rule_pop');"><span>权限说明</span></a>
            <a class="btn-lan right" href="/permission/set_group_func/<?php echo $user_arr['role_id'];?>/<?php echo $user_arr['agency_id'];?>"><span>角色配置</span></a>

	</div>
	<div class="limit-left">
            <select name="store_name" class="select" id="store_name" <?php echo $system_group_id >= 4 ? "disabled":""?>>
		    <?php if($system_group_id < 4){?>
                        <option value="no">不限</option>
                    <?php if($agency){foreach($agency as $key=>$val) {?>
                            <option value="<?php echo $val['store_name'];?>" <?php if($store_name == $val['store_name']){echo selected;}?>><?php echo $val['store_name'];?></option>
                    <?php }}}else{
                           if($agency){foreach($agency as $key=>$val) {?>
                            <option value="<?php echo $val['store_name'];?>" <?php if($store_name == $val['store_name']){echo selected;}?>><?php echo $val['store_name'];?></option>
                    <?php }}}?>
		</select>
		<ul id="js_left">
                    <?php if($list){foreach($list as $key =>$val) {?>
			<li id="group_list"><a href="javascript:void(0);" onclick="get_company_func(<?php echo $val['role_id'];?>);" id="func_link<?php echo $val['role_id'];?>" name="<?php echo $val['sid'];?>"><?php echo $val['truename'];?> <em>【<?php echo $val['name']?>】</em></a></li>
                    <?php }}?>
		</ul>
	</div>
</div>
</div>
<script>

        $("#store_name").change(function(){
            var store_name = $("#store_name").val();
		$.ajax({
                    url:"<?php echo MLS_URL;?>/permission/get_group",
                    type:"GET",
                    dataType:"json",
                    data:{
                       'store_name':store_name
                    },
                    success:function(data){
                        var html="";
                        if(data && data.length > 0){
                           for(var i in data){
                               html+="<li><a href='javascript:void(0);' onclick='get_company_func("+data[i]['role_id']+");' id='func_link"+data[i]['role_id']+"' name ='"+data[i]['sid']+"'>"+data[i]['truename']+"<em>【"+data[i]['name']+"】</em></a></li>";
                           }
                           $("#js_left").html(html);
                           $("li").children("a:first").attr("class","on");
                           var id = $("li").children("a:first").attr("id");
                           var cid =id.substring(9);
                           get_company_func(cid);
                        }else{
                              html+="<li><span class='no-data-tip'>抱歉，该门店暂时没有员工</span></li>";
                              $("#js_left").html(html);
                              $("#content").children("div").remove();
                              $("#content").append("<div class='limit-right-title clearfix'><span class='no-data-tip fl'style='position:relative;top:50px;left:460px'>抱歉，没有找到符合条件的信息</span></div>");
                        }
                    }
                  });

        });
         function get_company_func($role_id){
            $("#content").children("div").remove();
            $.ajax({
                    url:"<?php echo MLS_URL;?>/permission/get_company_func/"+$role_id,
                    type:"GET",
                    dataType:"json",
                    data:{
                       isajax:1
                    },
                    success:function(data){
                       var html="";
                       var info=data['list'];
                        for(var i in info){
                           var tab = info[i]['func'];
                           //一级菜单
                           html += '<div class="limit-right-title clearfix"><span class="fl">' + info[i]['name'] + '</span></div>';
                           for(var j in tab) {
                                html += '<div style="position:relative">';
                                if (tab[j].name != '')
                                {
                                    html += '<span class="zws_roles_subtitle" style="top:-12px;">' + tab[j].name + '</span>';
                                }
                                var secondtab = tab[j].list;
                                html += '<div class="limit-right-cont zws_roles_marginTop"><div class="zws_roles_marginTop20">';
                                for (var k in secondtab )
                                {
                                    if (secondtab[k].name != '')
                                    {
                                        html += '<span class="zws_roles_third_title">' + secondtab[k].name + '</span>';
                                    }
                                    var func = secondtab[k].list;
                                    html += '<div class="limit-right-cont-inner clearfix" id="js_check_all03">'
                                    for(var l in func)
                                    {
                                        html+="<b class='label label_one labelon' id='func_auth"+func[l]['pid']+"' name='func_auth'><input type='checkbox' value='"+info[i]['id']+"/"+func[l]['pid']+"' name='equipment[]' class='js_checkbox input_checkbox'  id='input"+func[l]['pid']+"'>"+func[l]['pname']+"</b>";
                                    }
                                    html += '</div>';
                                }
                                html += '</div></div></div>';
                           }
                       }
                       $("#content").append(html);
                    }
                });
        }

	$(function () {
		function re_width(){
			var h1 = $(window).height();
			var w1 = $(document).width() - 279;
			var w2 = $(document).width() - 230;
			$("#js_left").height(h1 - 106);
			$(".limit-right").height(h1 - 54);
			$(".limit-right-cont").width(w1);
			$(".limit-right-cont-inner").width(w2);
			$(".limit-right").width(w1).show();
		};
		re_width();
		$(window).resize(function(e) {
			re_width();
		});

                $("#js_left a").live("click",function(){
                    $("#js_left a").removeClass("on");
                    $(this).addClass("on");
                });

		$("li").children("a:first").attr("class","on");
                var name=$("li").children("a:first").attr("name");
                var id = $("li").children("a:first").attr("id");
                var cid =id.substring(9);
                get_company_func(cid);
	});
</script>

<!--权限说明弹窗-->
<div id="js_rule_pop" class="iframePopBox" style="width:816px; height:540px;overflow:hidden;display:none">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="auto" width="816px" height="540px" class='iframePop' src=""></iframe>
</div>
<script>
	function close_rule(){
		$("#js_rule_pop").css("display","none");
		$("#GTipsCoverjs_rule_pop").css("display","none");
	}
</script>

<img id="mainloading" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif"><!--遮罩 loading-->

<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js,calculate.js"></script>
