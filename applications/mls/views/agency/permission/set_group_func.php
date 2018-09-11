<body>
<!--导航栏-->
<div class="tab_box" id="js_tab_box">
    <?php echo $user_menu;?>
    <a style="float:right; margin-right:10px;" class="btn-lv" href="/permission/index"><span>&lt;&lt;返回角色权限</span></a>
</div>
<!--主要内容-->
<form action="" method="post" name="search_form">
<div class="limit-set-out clearfix">
<div class="limit-set">
    <input type="hidden" value="<?php echo $level; ?>" id="user_level"/>
        <input type="hidden" name="id" id="post_id">
        <input type="hidden" name="company_id" id="post_company_id">
        <input type="hidden" name="agency_id" id="post_agency_id">
	<div class="limit-right" id="content" style="overflow-y:auto;position:relative;">
	</div>
	<div class="limit-left" id="js_left">
		<ul>
                    <?php foreach($group as $key => $val) {?>
                        <li><a href="javascript:void(0);"
                               onclick="get_system_func(<?php echo $val['system_group_id']; ?>,<?php echo $val['id']; ?>, <?php echo $val['level']; ?>);"
                               id="func_link<?php echo $val['id']; ?>" name="<?php echo $val['system_group_id'] ?>"
                               level="<?php echo $val['level'] ?>"><?php echo $val['name'] ?></a></li>
                    <?php }?>
		</ul>
	</div>
</div>
</div>
</form>
<script>
    var equipment = [];
    var send_data = {};
	$(function () {
                $("#js_left a").live("click",function(){
                    $("#js_left a").removeClass("on");
                    $(this).addClass("on");
                });
                function re_width(){
                    var h1 = $(window).height();
                    var w1 = $(document).width() - 279;
                    var w2 = $(document).width() - 230;
                    $("#js_left").height(h1 - 54);
                    $(".limit-right").height(h1 - 54);
                    $(".limit-right-cont").width(w1);
                    $(".limit-right-cont-inner").width(w2);
                    $(".limit-right").width(w1).show();
                };
                re_width();
                $(window).resize(function(e) {
                    re_width();
                });

                $("#js_left a:first").attr("class","on");
                var id = $("#js_left a:first").attr("id");
                var cid =id.substring(9);
                var name=$("#js_left a:first").attr("name");
        var level = $("#js_left a:first").attr("level");
        get_system_func(name, cid, level);

                //保存按钮
                $('input[name="save"]').live('click',function(){
                    equipment = [];
                    send_data = {};
                    $('input[name="equipment[]"]:checked').each(function(){
                        equipment.push($(this).val());
                    });
                    send_data.id = $('#post_id').val();
                    send_data.equipment = equipment;
                    $.ajax({
                            url:"<?php echo MLS_URL;?>/permission/save_button_submit/",
                            type:"GET",
                            dataType:"json",
                            data:send_data,
                            success:function(data){
                                if(1==data.kind || 2==data.kind){
                                    if(data.update_result > 0){
                                        $("#dialog_do_itp").text("保存成功");
                                        openWin("js_pop_do_success");
                                    }else{
                                        $("#dialog_do_itp").text("保存成功");
                                        openWin("js_pop_do_success");
                                    }
                                }else{
                                    openWin("js_set_per_agency_area");
                                }
                            }
                    });

                });
        });

    function choose_authority(level) {
        //var level = parseInt($("input[name='level']").val());
        var user_level = parseInt($("#user_level").val());
        if (level > user_level) {
		$(".labelall").click(function(){
                    var i = $(this).parent().next(".limit-right-cont");
                    if($(this).hasClass('labelon')){
                            $(this).removeClass('labelon');
                            i.find("b.label").removeClass("labelon");
                            i.find(".js_checkbox").prop("checked",false);
                            $(this).find(".input_checkbox").prop("checked",false);
                            i.find(".input_checkbox").prop("checked",false);
                    }else
                    {
                            $(this).addClass('labelon');
                            i.find("b.label").addClass("labelon");
                            i.find(".js_checkbox").prop("checked",true);
                            $(this).find(".input_checkbox").prop("checked",true);
                            i.find(".input_checkbox").prop("checked",true);
                    }
		});
		$(".label_one").click(function(){
			if($(this).hasClass('labelon')){
				$(this).removeClass('labelon');
				$(this).find(".input_checkbox").prop("checked",false);
			}else
			{
				$(this).addClass('labelon');
				$(this).find(".input_checkbox").prop("checked",true);
			}
		});
		$(".label_all").click(function(){
			if($(this).hasClass('labelon')){
				$(this).removeClass('labelon');
				$("b.label").removeClass("labelon");
				$(".js_checkbox").attr("checked",false);
				$(this).find(".input_checkbox").attr("checked",false);
				$(".input_checkbox").attr("checked",false);
			}else
			{
				$(this).addClass('labelon');
				$("b.label").addClass("labelon");
				$(".js_checkbox").attr("checked",true);
				$(this).find(".input_checkbox").attr("checked",true);
				$(".input_checkbox").attr("checked",true);
			}
		});
            }else{
                $(".set_bottom_bar").hide();
            }
        }
    function get_system_func($sid, $role_id, level) {
            $.ajax({
                    url:"<?php echo MLS_URL;?>/permission/get_system_group/"+$sid,
                    type:"GET",
                    dataType:"json",
                    data:{
                       isajax:1
                    },
                    success:function(data){
                       var info=data['list'];
                       var html = "<input type='hidden' name='level' value="+data['level']+">";
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
                                        html+="<b class='label label_one' id='func_auth"+func[l]['pid']+"' name='func_auth'><input type='checkbox' value='"+info[i]['id']+"/"+func[l]['pid']+"' name='equipment[]' class='js_checkbox input_checkbox'  id='input"+func[l]['pid']+"'>"+func[l]['pname']+"</b>";
                                    }
                                    html += '</div>';
                                }
                                html += '</div></div></div>';
                           }

                       }
                       //console.log(html);
                        html+="<div class='set_bottom_bar'>"+/*<b class='label label_all'><input type='checkbox' class='js_checkbox input_checkbox'>全选所有权限</b>*/"<input type='button' value='保&#12288;存' class='submit_blue' name='save'></div>";
                       $("#content").html(html);
                        get_group_func($role_id, level);
                        choose_authority(level);
                    }
                });
        }
    function get_group_func($id, level) {
            $.ajax({
                    url:"<?php echo MLS_URL;?>/permission/get_group_func/"+$id,
                    type:"GET",
                    dataType:"json",
                    data:{
                       isajax:1
                    },
                    success:function(data){
                          $("#post_id").val(data["id"]);
                          $("#agency_group_id").val(data["id"]);
                          $("#post_company_id").val(data["company_id"]);
                          $("#post_agency_id").val(data["agency_id"]);
                        //var level = parseInt($("input[name='level']").val());
                        var user_level = parseInt($("#user_level").val());
                          for(var j in data['func_auth'])
                          {
                               $("#input"+data['func_auth'][j]).attr('checked',true);
                              if (level > user_level) {
                                    $("#input"+data['func_auth'][j]).parent().addClass('labelon');
                                }else{
                                    $("#input"+data['func_auth'][j]).parent().addClass('label-hui');
                                }
                          }
                    }
            });
        }

        function submit_agency_per_area_form()
        {
            //将上一步操作的所选权限节点，放到表单中。
            var _id_str = '';
            for(var i in equipment){
                _id_str += equipment[i] + ',';
            }
            $('#per_id_str').val(_id_str);
            $.ajax({
                type: "POST",
                url: "/permission/save_button_submit_acency/",
                dataType:"json",
                data:$("#agency_per_area_form").serialize(),
                cache:false,
                error:function(){
                    $("#dialog_do_warnig_tip").html("系统错误");
                    openWin('js_pop_do_warning');
                    return false;
                },
                success: function(data){
                    if('success'==data.result){
                        $("#dialog_do_itp").text("保存成功");
                        openWin("js_pop_do_success");
                    }else{
                        $("#dialog_do_itp").text("保存失败");
                        openWin("js_pop_do_success");
                    }
                }
            });
        }

        function checkallagency(obj)
        {
            var checkall = obj.checked ? 1 : 0;

            $(".agency_access_area").each(function(){
                checkall ? $(this).attr('checked', true) : $(this).attr('checked', false);
            });
        }

</script>

<?php if(0===$info){?>
             <script>
                  $(function(){
                      $("#dialog_do_itp").text("保存失败");
                      openWin("js_pop_do_success");
                  });
             </script>
<?php }else if(is_int($info) && $info > 0){ ?>
             <script>
                  $(function(){
                     $("#dialog_do_itp").text("保存成功");
                     openWin("js_pop_do_success");
                   });
             </script>
<?php }?>
 <!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" style="display: block">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="/permission/index/" onclick="sub_form();" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'></p>
            </div>
        </div>
    </div>
</div>

<!--权限范围弹框-->
<div class="pop_box_g pop_box_add_shop" id="js_set_per_agency_area" style="width:600px;height:300px;">
    <div class="hd">
        <div class="title">修改范围</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" ></a></div>
    </div>
    <div class="mod">
	<form name="agency_per_area_form" id="agency_per_area_form" method="post">
        <input type="hidden" name="agency_group_id" id="agency_group_id">
        <input type="hidden" name="per_id_str" id="per_id_str">
		<div style="overflow-y:scroll;width:560px;height:193px;margin:10px auto;background:#FFF; padding:6px 0 0 5px;">
        <?php if(is_array($all_company_info) && !empty($all_company_info)){
			foreach(array_reverse($all_company_info) as $k=>$v) {?>
			<div onmouseover="this.style.background='#EEE';" onmouseout="this.style.background='#FFF';" style="float:left;width:120px;height:26px;overflow:hidden;line-height:22px;font-weight:14px;padding:5px 0 0 10px;">
				<label style="display:block;width:120px;cursor:pointer;">
					<input type="checkbox" <?php if($now_agency_id == $v['id']){ ?>checked disabled="disabled"<?php }else{ ?> class="agency_access_area" name="agency_access_area[]"<?php } ?> value="<?php echo $v['id']?>">&nbsp;&nbsp;<?php echo $now_agency_id == $v['id'] ? "<span style='color:#999;'>".$v['name']."</span>" : $v['name']?>
				</label>
			</div>
            <?php
                if(isset($v['next_agency_data']) && !empty($v['next_agency_data'])){
                    foreach($v['next_agency_data'] as $key => $value){ ?>
                        <div onmouseover="this.style.background='#EEE';" onmouseout="this.style.background='#FFF';" style="float:left;width:120px;height:26px;overflow:hidden;line-height:22px;font-weight:14px;padding:5px 0 0 10px;">
                            <label style="display:block;width:120px;cursor:pointer;">
                                <input type="checkbox" <?php if($now_agency_id == $value['id']){ ?>checked disabled="disabled"<?php }else{ ?> class="agency_access_area" name="agency_access_area[]"<?php } ?> value="<?php echo $value['id']?>">&nbsp;&nbsp;<?php echo $now_agency_id == $value['id'] ? "<span style='color:#999;'>".$value['name']."</span>" : $value['name']?>
                            </label>
                        </div>
            <?php
                    }
                }
            ?>
		<?php } }?>
		</div>
		<input type="hidden" name="agency_access_area[]" value="<?php echo $now_agency_id; ?>">
		<input type="hidden" name="now_agency_id" value="<?php echo $now_agency_id; ?>">
		<div style="position:relative; text-align:center; width:100%;"><label style="position:absolute; left:13px; top:0;"><input type="checkbox" onclick="checkallagency(this);"> 全选</label><a title="根据勾选设置关联指定部门" class="btn-lv btn-left" style="padding-left: 10px;" href="javascript:void(0)" onclick="submit_agency_per_area_form();"><span class="btn_inner" style="padding-right: 10px;">保存设置</span></a><a class="btn-hui1 JS_Close" href="/permission/index/"><span>取消</span></a></div>
	</form>
    </div>
</div>


<img id="mainloading" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif"><!--遮罩 loading-->

<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js,calculate.js"></script>


</body>
