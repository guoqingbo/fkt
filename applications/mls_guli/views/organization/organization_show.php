
<script>
    window.parent.addNavClass(1);
</script>
<style>
	.limit-left2 li{background:#F9F9F9 url(<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/tab_01.png) no-repeat 15px center; padding-left:30px;}
	.limit-left2 li.hover{background-color:#CCE8FE !important;}
	.limit-left2 li.hover a{color:#227AC6;}
	.limit-left2 li.tab-top{background:#F9F9F9 url(<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/tab_02.png) no-repeat 15px center;}
	.limit-left2 .on{background:#5CA2DF url(<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/tab_02.png) no-repeat 15px center !important; border:none; margin-top:-1px;}
	.limit-left2 .on a{color:#fff;}
	.limit-left2 .on a:hover{color:#227AC6;}
	.ui-menu{width:164px !important; border-color:#D1D1D1;}
  .limit-left2 li.nullMuen{background: #f9f9f9}
  .relevance .pull-left h3 a.txte.aMuen{background: #f9f9f9;}
  .relevance .pull-left h3 a.txte-on.aMuen{background: #5ca2de;}
</style>
<script type="text/javascript">
	$(function(){
		$('#guanlian_openwin_button').live('click',function(){
		$('input[name="child_node_id[]"]').attr('checked',false);
		$('input[name="child_node_id[]"]').parent().addClass('label-hui');
		$('#submit_department_perm_button').hide();
        $('#copy_department_perm_button').hide();
		openWin('js_set_department_access_area_new');
	});

    //当前公司下的一级门店。（用于修改门店中的下拉框，组装html）
    var department_oneleval_data = <?php echo json_encode($department_oneleval_data);?>;
    var html_str = '';
    var company_str = '<option value="0">[总公司]<?php echo $company_name;?></option>';
    html_str += company_str;
    for(key in department_oneleval_data){
        if(key < department_oneleval_data.length-1){
            html_str += '<option value="'+department_oneleval_data[key].id+'">'+department_oneleval_data[key].name+'</option>';
        }else{
            html_str += '<option value="'+department_oneleval_data[key].id+'"> '+department_oneleval_data[key].name+'</option>';
        }
    }
	$("#modify").live("click",function(event){
	   var rel= $(this).attr("rel");
	   var name = $("#"+rel+"_name").text();
	   var telno = $("#"+rel+"_telno").text();
	   var address = $("#"+rel+"_address").text();
	   var dist_id = $("#"+rel+"_dist_id").text();
	   var street_id = $("#"+rel+"_street_id").text();
	   var department_id = $("#"+rel+"_department_id").text();
	   var is_has_department = $("#"+rel+"is_has_department").text();
	   $("#department_id").val(rel);
	   $("#modify_name").val(name);
	   $("#modify_telno").val(telno);
	   $("#modify_address").val(address);
	   $("#modify_district").val(dist_id);
	   $("#modify_street").val(street_id);
       //判断门店是否有下属门店
       if(0==is_has_department){
            $("#modify_father_department_id").html('');
            $("#modify_father_department_id").html(html_str);
            $("#modify_father_department_id").val(department_id);
       }else{
            $("#modify_father_department_id").html('');
            $("#modify_father_department_id").html(company_str);
            $("#modify_father_department_id").val(0);
       }
		event.stopPropagation();
		return false;
	});

    //$("[limit]").limit();

    $(".labelall").live('click',function(){
		var i = $(this).parent().next(".limit-right-cont");
        if($(this).hasClass('labelon')){
            $(this).removeClass('labelon')
            i.find("b.label").removeClass("labelon");
            i.find(".js_checkbox").prop("checked",false);
            $(this).find(".input_checkbox").prop("checked",false);
            i.find(".input_checkbox").prop("checked",false);
        }else
        {
            $(this).addClass('labelon')
            i.find("b.label").addClass("labelon");
            i.find(".js_checkbox").prop("checked",true);
            $(this).find(".input_checkbox").prop("checked",true);
            i.find(".input_checkbox").prop("checked",true);
        }
    });
    $(".relevance .pull-left h3 a.txte").click(function(){
        $(".relevance .pull-left h3 a.txte").removeClass('txte-on');
        $(".relevance .pull-left li a.txte").removeClass('txte-on');
        $(this).toggleClass('txte-on');
        $(this).parent().next("ul").toggle();


    });
    $(".relevance .pull-left h3 a.on-off").on('click',function(){
        var deal_type = $(this).attr('deal_type');
        if('2'==deal_type){
            return false;
        }
        var is_effective_department_id = $(this).parent().attr('value');

        if($(this).hasClass('off-on')){
            $(this).removeClass('off-on');
           // $(this).parent().next('ul').find("a.on-off").removeClass('off-on');
            is_effective_ajax_level_two(1,is_effective_department_id);
        }else{
            $(this).addClass('off-on');
            //$(this).parent().next('ul').find("a.on-off").addClass('off-on');
           is_effective_ajax_level_two(0,is_effective_department_id);
        }
    });

    //模拟单选按钮
    $(".relevance .pull-left li a.on-off").on('click',function(){
        var self = $(this).parents("ul").find("a.on-off");
        var chknum = self.size();
        var chk = 0;
        var i = $(this);
        var is_effective_department_id = i.parent().val();
        if($(this).hasClass("off-on")){
            i.find(".js_checkbox").prop("checked",true);
            //i.removeClass("off-on");
            is_effective_ajax_level_two(1,is_effective_department_id);
        }
        else
        {
            i.find(".js_checkbox").prop("checked",false);
            //i.addClass("off-on");
            is_effective_ajax_level_two(0,is_effective_department_id);
        };

        self.each(function () {
          if($(this).hasClass("off-on")){
                chk++;
            }
        });
        if(chknum==chk){//全选
            //i.parents("ul").prev().find("a.on-off").addClass('off-on');
            i.parents("ul").prev().find(".js_checkbox").prop("checked",false);
        }else{//不全选
           // i.parents("ul").prev().find("a.on-off").removeClass('off-on');
            i.parents("ul").prev().find(".js_checkbox").prop("checked",true);
        };
    });

    $(".relevance .pull-left li a.txte").click(function(){
		$(".relevance .pull-left h3 a.txte").removeClass('txte-on');
        $(".relevance .pull-left li a.txte").removeClass('txte-on');
        $(this).toggleClass('txte-on');
    });

    $(".label_radio").live('click',function(){
        if($(this).hasClass('labelon')){
            $(this).removeClass('labelon')
            $(this).find(".input_checkbox").prop("checked",false);
        }else
        {
            $(this).addClass('labelon')
            $(this).find(".input_checkbox").prop("checked",true);
        }
    });


    $(".label_all").live('click',function(){
        if($(this).hasClass('labelon')){
            $(this).removeClass('labelon')
            $("b.label").removeClass("labelon");
            $(".js_checkbox").prop("checked",false);
            $(this).find(".input_checkbox").prop("checked",false);
            $(".input_checkbox").prop("checked",false);
        }else
        {
            $(this).addClass('labelon')
            $("b.label").addClass("labelon");
            $(".js_checkbox").prop("checked",true);
            $(this).find(".input_checkbox").prop("checked",true);
            $(".input_checkbox").prop("checked",true);
        }
    });

    $('.department_id').live('click',function(){
        var main_department_id = <?php echo $department_id;?>;
        var sub_department_id = $(this).attr('value');
        $('#main_department_id').val(main_department_id);
        $('#sub_department_id').val(sub_department_id);
        $('#copy_main_department_id').val(main_department_id);
        $('#copy_sub_department_id').val(sub_department_id);
        if(main_department_id == sub_department_id){
            $('input[name="child_node_id[]"]').attr('checked',false);
            $('input[name="child_node_id[]"]').parent().addClass('label-hui');
            $('#submit_department_perm_button').hide();
            $('#copy_department_perm_button').hide();
        }else{
            $('input[name="child_node_id[]"]').parent().removeClass('label-hui');
            $('#submit_department_perm_button').show();
            $('#copy_department_perm_button').show();
            $.ajax({
                    url:"<?php echo MLS_SIGN_URL;?>/organization/get_department_per_node/"+main_department_id+"/"+sub_department_id,
                    type:"GET",
                    dataType:"json",
                    success:function(data){
                        $('input[name="child_node_id[]"]').attr('checked',false);
                        $('input[name="child_node_id[]"]').parent().removeClass('labelon');
                        var func_auth = data['func_auth'];
                        for(var i in func_auth){
                            $("#input"+func_auth[i]).attr('checked',true);
                            $("#input"+func_auth[i]).parent().addClass('labelon');
                        }
                    }
            });
        }
    });

    $('.department_id_2').live('click',function(){
        var main_department_id = <?php echo $department_id;?>;
        var sub_department_id = $(this).attr('value');
        $('#main_department_id').val(main_department_id);
        $('#sub_department_id').val(sub_department_id);
        $('#copy_main_department_id_2').val(main_department_id);
        $('#copy_sub_department_id_2').val(sub_department_id);
        if(main_department_id == sub_department_id){
            $('input[name="child_node_id[]"]').attr('checked',false);
            $('input[name="child_node_id[]"]').parent().addClass('label-hui');
        }else{
            $('input[name="child_node_id[]"]').attr('checked',false);
            $('input[name="child_node_id[]"]').parent().removeClass('label-hui');
            $.ajax({
                    url:"<?php echo MLS_SIGN_URL;?>/organization/get_department_per_node/"+main_department_id+"/"+sub_department_id,
                    type:"GET",
                    dataType:"json",
                    success:function(data){
                        $('input[name="child_node_id[]"]').attr('checked',false);
                        $('input[name="child_node_id[]"]').parent().removeClass('labelon');
                        var func_auth = data['func_auth'];
                        for(var i in func_auth){
                            $("#input_copy"+func_auth[i]).parent().addClass('label-hui');
                        }
                    }
            });
        }
    });

    $.widget( "custom.autocomplete", $.ui.autocomplete, {
		_renderItem: function( ul, item ) {
			if(item.signatory_id>0){
				return $( "<li>" )
				.data( "item.autocomplete", item )
				.append('<a class="ui-corner-all" tabindex="-1"><span ><font color="red">'+item.label+'</font></span><span style=" ">'+item.phone+'</span></a>').appendTo( ul );
			}else{
				return $( "<li>" )
				.data( "item.autocomplete", item )
				.append('<a class="ui-corner-all" tabindex="-1">'+item.label+'</a>')
				.appendTo( ul );
			}
		}
	});
    $("#department_name").autocomplete({
        source: function( request, response ) {
            var term = request.term;
            $.ajax({
                url: "<?php echo MLS_SIGN_URL;?>/department/get_department_info_by_kw/",
                type: "GET",
                dataType: "json",
                data: {
                    keyword: term
                },
//                error:function(){
//                    $("#dialog_do_warnig_tip_copy").html("系统错误");
//                    openWin('js_pop_do_warning_copy');
//                    return false;
//                },
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
        width:2,
        select: function(event,ui) {
            if(ui.item.id > 0){
                var departmentname = ui.item.name;
                var id = ui.item.id;
                //操作
                $("#select_department_id").val(id);
                $("#department_name").val(departmentname);
                department_name_search(departmentname);
                removeinput = 2;
            }else{
                removeinput = 1;
            }
        },
        close: function(event) {
            if(typeof(removeinput)=='undefined' || removeinput == 1){
                //$("#department_name").val("");
                $("#select_department_id").val("");
            }
        }
    });

	$("#signatory_name").autocomplete({
        source: function( request, response ) {
            var term = request.term;
            $.ajax({
                url: "<?php echo MLS_SIGN_URL;?>/department/get_signatory_info_by_kw_2/",
                type: "GET",
                dataType: "json",
                data: {
                    keyword: term,department_id:'<?=$now_department_id?>'
                },
                error:function(){
                    $("#dialog_do_warnig_tip_copy").html("系统错误");
                    openWin('js_pop_do_warning_copy');
                    return false;
                },
                success: function(data) {
                    //判断返回数据是否为空，不为空返回数据。
                    if( data[0]['signatory_id'] != '0'){
						//alert(data[0]['signatory_id']);
                        response(data);
                    }else{
                        response(data);
                    }
                }
            });
        },
        minLength: 1,
        removeinput: 0,
        width:2,
        select: function(event,ui) {
            if(ui.item.signatory_id > 0){
                var signatoryname = ui.item.name;
                var id = ui.item.signatory_id;
                var department_id = ui.item.department_id;
                //操作
                $("#select_signatory_id").val(id);
                $("#signatory_name").val(signatoryname);
                signatory_name_search(department_id,id);
                removeinput = 2;
            }else{
                removeinput = 1;
            }
        },
        close: function(event) {
            if(typeof(removeinput)=='undefined' || removeinput == 1){
                $("#select_signatory_id").val("");
                $("#signatory_name").val("");
            }
        }
    });

    $('#search_button').live('click',function(){
        var department_name = $('#department_name').val();
        department_name_search(department_name);
        return false;
    });

	$('#search_signatory').live('click',function(){
        var signatory_id = $('#select_signatory_id').val();
        var department_id = <?=$now_department_id?>;
        window.location.href = '/organization/index/'+department_id+'/'+signatory_id;
        return false;
    });
});

jQuery.fn.limit=function(){
    var self = $(this);
    self.each(function(){
        var objString = $(this).text();
        var objLength = $(this).text().length;
        var num = $(this).attr("limit");
        if(objLength > num){
            $(this).attr("title",objString);
            objString = $(this).text(objString.substring(0,num) + "...");
        }
    })
}
</script>
<div class="tab_box" id="js_tab_box">
    <?php
    echo $user_menu;
    ?>
</div>

<!--主要内容-->
    <form name="search_form" id="search_form" method="post" action="">
    <div class="limit-set-out2">
    <div class="limit-set2 clearfix">
      <div class="limit-left2" id="js_left">
        <h3 class="l-h1 f14" style="height:30px; display:none;"><?php echo $company_name?></h3>
        <div class="limit-search clearfix"><input type="text" name="department_name" id="department_name" value="请输入门店关键词" onfocus="if(value=='请输入门店关键词'){value='';$(this).css('color','#535353');}" onblur="if(value==''){value='请输入门店关键词';$(this).css('color','#999');}" autocomplete="off"><button id='search_button'>搜索</button></div>
        <div class="limit-search-text" style="<?php echo ('search'==$view_type)?'display:block;':'display:none;'; ?>"><a class="fr" href="<?php echo MLS_SIGN_URL;?>/organization/index/">返回</a>已搜索到<span class="f00" id="search_result_num"><?php echo $search_result_num;?></span>条相关门店</div>
        <input name="select_department_id" id="select_department_id" value="" type="hidden">
          <ul id="department_list">
              <?php if('index'==$view_type){?>
                  <?php if(is_array($company_info) && !empty($company_info)){
                      foreach(array_reverse($company_info) as $key=>$vo) {?>
                      <li <?php if($now_department_id == $vo['id']){
                          $now_department_name = $vo['name'];
                          echo "class='tab-top on'";
                      }else if($vo['is_has_department']!='1'){
                          echo "class='nullMuen'";
                      }
                      ?>>
                          <a class="l-edit l-edit-3" href="javascript:void(0)" onclick="checkdel(<?php echo $vo['id']?>)" title="删除">删除</a>
                          <a class="l-edit l-edit-2" href="javascript:void(0)" onClick="modify_pop(<?=$vo['id']?>)" rel="<?php echo $vo['id']?>" id="modify" title="编辑">编辑</a>
                          <a id=""  style="font-weight:bold;" href="/organization/index/<?php echo $vo['id']?>"><?php echo $vo['name']?></a>
                      </li>
                      <li style="display:none"><div  id="<?php echo $vo['id']?>_name"><?php echo $vo['name'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $vo['id']?>_telno"><?php echo $vo['telno'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $vo['id']?>_address"><?php echo $vo['address'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $vo['id']?>_dist_id"><?php echo $vo['dist_id'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $vo['id']?>_street_id"><?php echo $vo['street_id'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $vo['id']?>_department_id"><?php echo $vo['department_id'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $vo['id']?>is_has_department"><?php echo $vo['is_has_department'] ?></div></li>

                      <!--二级门店-->
                      <?php if(isset($vo['next_department_data']) && !empty($vo['next_department_data'])){
                          foreach($vo['next_department_data'] as $key => $value){
                      ?>
                      <li style="<?php echo ($vo['id']==$now_department_id||$value['department_id']==$now_father_department_id)?'display:block; background:url('.MLS_SOURCE_URL.'/mls_guli/images/v1.0/tab_02.png) no-repeat 30px center; padding-left:45px;':'display:none;';?>" id="<?php echo $vo['id']?>_next_department" <?php if($value['id']==$now_department_id){echo 'class="on tab-bottom2"';}?>>
                          <a class="l-edit l-edit-3" href="javascript:void(0)" onclick="return checkdel(<?php echo $value['id']?>)" title="删除">删除</a>
                          <a class="l-edit l-edit-2" href="javascript:void(0)" onClick="modify_pop(<?=$value['id']?>)" rel="<?php echo $value['id']?>" id="modify" title="编辑">编辑</a>
                          <a id="" href="/organization/index/<?php echo $value['id']?>"><?php echo $value['name']?></a>
                      </li>
                      <li style="display:none"><div  id="<?php echo $value['id']?>_name"><?php echo $value['name'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['id']?>_telno"><?php echo $value['telno'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['id']?>_address"><?php echo $value['address'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['id']?>_dist_id"><?php echo $value['dist_id'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['id']?>_street_id"><?php echo $value['street_id'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['id']?>_department_id"><?php echo $value['department_id'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['id']?>is_has_department"><?php echo $value['is_has_department'] ?></div></li>
                      <?php
                          }
                      }
                      ?>
                  <?php } }?>
              <?php }else if('search'==$view_type){?>
                      <?php if(is_full_array($search_department_data)){
                          foreach($search_department_data as $key => $value){
                      ?>
                      <li <?php if($now_department_id == $value['department_id']){
                          $now_department_name = $value['department_name'];
                          echo "class='tab-top on'";
                      }?>>
                          <a class="l-edit l-edit-2" href="javascript:void(0)" onClick="modify_pop(<?=$value['department_id']?>)" rel="<?php echo $value['department_id']?>" id="modify" title="编辑">编辑</a>
                          <a id=""  style="font-weight:bold;" href="/organization/index/<?php echo $value['department_id']?>?view_type=search&search_department_id=<?php echo $search_department_id_str;?>"><?php echo $value['department_name']?></a>
                      </li>
                      <li style="display:none"><div  id="<?php echo $value['department_id']?>_name"><?php echo $value['department_name'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['department_id']?>_telno"><?php echo $value['telno'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['department_id']?>_address"><?php echo $value['address'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['department_id']?>_dist_id"><?php echo $value['dist_id'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['department_id']?>_street_id"><?php echo $value['street_id'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['department_id']?>_department_id"><?php echo $value['father_department_id'] ?></div></li>
                      <li style="display:none"><div  id="<?php echo $value['department_id']?>is_has_department"><?php echo $value['is_has_department'] ?></div></li>
                      <?php }}?>
              <?php }?>

          </ul>
          <?php if (in_array($level, array(1, 5))) { ?>
        <div class="limit-left2-operation">
          <a href="javascript:void(0)" class="btn-lan4 f14" onClick="openWin('js_add_shop')">添加部门</a>
          <!--			<a href="javascript:void(0)" class="btn-lan4 f14" onClick="openWin('js_copy_department_access_area_2')">复制关联门店权限</a>-->
        </div>
        <?php } ?>
		</div>
		<script>
			$(function(){
				$("li").hover(function(){
					$(this).toggleClass('hover');
				})
			});

		</script>

		<div class="limit-right2">
		  <div style="display:block;">
        <div class="tool-bar limit-top mb10" id="js_search_box">
          <div class="limit-search ml10 fr"><input type="text" name="signatory_name" id="signatory_name" value="请输入您想找的人员" onfocus="if(value=='请输入您想找的人员'){value='';$(this).css('color','#535353');}" onblur="if(value==''){value='请输入您想找的人员';$(this).css('color','#999');}" autocomplete="off" class="ui-autocomplete-input"><button id="search_signatory">搜索</button></div>
          <input name="select_signatory_id" id="select_signatory_id" value="" type="hidden">
<!--          --><?php //if(in_array($level, array(1,2,3,4,5))){?>
<!--          <a href="javascript:void(0)" class="f12 btn-lan-big ml10 fr" id="guanlian_openwin_button"><span style='float:left'>设置关联部门</span></a>-->
<!--          --><?php //} ?>
          <a href="javascript:void(0)" onClick="openWin('js_add_account')" class="add_link" style="margin-top:0;"><span class="iconfont"></span>新增员工帐号</a>

          <h3><?php echo $store_name?></h3>
        </div>
				<p style="padding-bottom:11px;"><span>负责人：<?php echo isset($linkman)?$linkman:"暂无"?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>联系方式：<?php echo isset($telno)?$telno:"暂无"?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>地址：<?php echo $address?></span></p>
			</div>
			<div class="table_all" style="margin-right:0; margin-left:0;">
				<div class="title" id="js_title">
					<table class="table">
						<tbody>
							<tr>
								<td class="c10">序号</td>
								<td class="c20">姓名</td>
								<td class="c15">职位</td>
								<td class="c15">联系电话</td>
                                <td class="c20">注册时间</td>
								<td>操作</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="js_inner" class="inner" style="overflow-y: scroll; height: 349px;">
					<table class="table">
							<tbody>
              <?php
              if ($signatory_all_info) {
                foreach ($signatory_all_info as $k => $v) { ?>
                  <tr height="40">
                    <td class="c10"><?= $k + 1 ?></td>
                    <td class="c20"><?php echo $v['truename'] ?></td>
                      <td class="c15"><?php echo $v['role_name']; ?></td>
                    <td class="c15"><?php echo $v['phone'] ?></td>
                      <td class="c20"><?php echo date("Y-m-d", $v['register_time']) ?></td>
                    <td>
                    <?php
                    if (in_array($level, [1, 5])) {
                        if (!in_array($v['role_level'], [1])) { ?>
                              <a href="javascript:void(0);" onclick="$('#js_edit_pop .iframePop').attr('src','/organization/organization_edit/<?= $v['signatory_id']; ?>');openWin('js_edit_pop');">编辑</a>
                              <span style="margin:0 5px;color:#b2b2b2;">|</span>
                              <a href="javascript:void(0)" onclick="modify_pass_pop(<?php echo $v['signatory_id'] ?>)">修改密码</a>
                              <span style="margin:0 5px;color:#b2b2b2;">|</span>
                              <a href="javascript:void(0)" onclick="cancel_account_pop(<?php echo $v['signatory_id'] ?>)">注销</a>
                          <?php }
                    }
                    ?>
                    </td>
                  </tr>
                  <?php
                }
              } else { ?>
                <tr>
                  <td align="center" colspan='6'>暂无相关员工，请创建帐号！</td>
                </tr>

              <?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="fun_btn clearfix" id="js_fun_btn" style="">
				<div class="get_page" >
				<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
				</div>
			</div>
		</div>

	</div>
	</div>
	</form>
<img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->

<!--编辑资料弹窗-->
<div id="js_edit_pop" class="iframePopBox" style="width:840px; height:470px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="auto" width="840px" height="470px" class='iframePop' src=""></iframe>
</div>

<!--编辑员工资料弹窗-->
<div id="js_department_edit_pop" class="iframePopBox" style="width:450px; height:390px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="auto" width="450px" height="390px" class='iframePop' src=""></iframe>
</div>

<!--添加门店-->
<div class="pop_box_g pop_box_add_shop" id="js_add_shop">
    <div class="hd">
        <div class="title">添加部门</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod" style="background:#fff;">
        <label class="label clearfix"><span class="text">部门名称：</span><input class="text_input" id="add_name" placeholder="店面或部门的名称，如：三牌楼店" type="text"></label>
        <label class="label clearfix"><span class="text">部门电话：</span><input class="text_input" id="add_telno" placeholder="店面的电话，如：02589898989" type="text"></label>
        <label class="label clearfix"><span class="text">部门地址：</span><input class="text_input" id="add_address" placeholder="店面的地址,如：奥体大街100号" type="text"></label>
		<label class="label clearfix"><span class="text">部门挂靠：</span>
			<select id="father_department_id" aria-controls="dataTables-example" class="form-control input-sm" style="line-height:25px;height:25px;font-size:12px;border-radius:3px;">
				<option value="0">[总公司] <?php echo $company_name;?></option>
        <?php if (!empty($department_oneleval_data)) {
          foreach ($department_oneleval_data as $k => $v) {
            if ($k < count($department_oneleval_data) - 1) {?>
              <option value="<?php echo $v['id'] ?>">├─<?php echo $v['name'] ?></option>
              <?php } else {?>
              <option value="<?php echo $v['id'] ?>">└─<?php echo $v['name'] ?></option>
              <?php
            }
          }
        }
        ?>
			</select>
		</label>
		<input type="hidden" id="department_type" value="1" />
        <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="add_department()">保存</button>
    </div>
</div>

<script type="text/javascript">
function department_name_search(department_name){
    var department_id_str = '';
    var _href = '';
	$.ajax({
		type: "GET",
		url: "<?php echo MLS_SIGN_URL;?>/department/get_department_info_by_kw/",
		dataType:"json",
        data: {
            keyword: department_name
        },
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
			openWin('js_pop_do_warning');
			return false;
		},
		success: function(data){
            var department_html = '';
            for(i in data){
                department_id_str += data[i].id+',';
            }
            for(i in data){
                department_html += '<li class="">';
                department_html += '<a title="编辑" id="modify" rel="9233" onclick="modify_pop(9233)" href="javascript:void(0)" class="l-edit l-edit-2">编辑</a>';
                department_html += '<a href="/organization/index/'+data[i].id+'?view_type=search&amp;search_department_id='+department_id_str+'" style="font-weight:bold;" id="">'+data[i].name+'</a>';
                department_html += '</li>';
            }
            if(0==data[0].id){
                $('#department_list').html('');
                $('#search_result_num').html(0);
            }else{
                $('#department_list').html(department_html);
                $('#search_result_num').html(data.length);
            }
            $('.limit-search-text').show();
		}
	});
}

function signatory_name_search(department_id,signatory_id){
    window.location.href = '/organization/index/'+department_id+'/'+signatory_id;
    return false;
}

function checkalldepartment(obj)
{
	var checkall = obj.checked ? 1 : 0;

	$(".department_access_area").each(function(){
		checkall ? $(this).attr('checked', true) : $(this).attr('checked', false);
	});
}
function submit_department_perm_form()
{
	$.ajax({
		type: "POST",
		url: "/organization/set_department_per/",
		dataType:"json",
		data:$("#department_per_form").serialize(),
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
			openWin('js_pop_do_warning');
			return false;
		},
		success: function(data){
			if(data['errorCode'] == '401')
			{
				login_out();
				$("#jss_pop_tip").hide();
			}
			else if(data['errorCode'] == '403')
			{
				/*purview_none();
				$("#jss_pop_tip").hide();*/
				closeWindowWin('js_add_shop');
				$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
				openWin('js_pop_do_warning');return false;
			}else{
				if(data.status=="success"){
					$("#dialog_do_success_tip").html(data.msg);
					openWin('js_pop_do_success');
				}else{
					$("#dialog_do_warnig_tip").html(data.msg);
					openWin('js_pop_do_warning');
				}
			}
		}
	});
}

//一级门店是否有效开关
function is_effective_ajax_level_one(type, sub_department_id){
    var main_department_id = <?php echo $department_id;?>;
    var post_data = {
        'department_level':'1',
        'type':type,
        'main_department_id':main_department_id,
        'sub_department_id':sub_department_id
    };
	$.ajax({
		type: "POST",
		url: "/organization/set_is_effective/",
		dataType:"json",
		data:post_data,
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
			openWin('js_pop_do_warning');
			return false;
		}
	});
}
//二级门店是否有效开关
function is_effective_ajax_level_two(type, sub_department_id){
    var main_department_id = <?php echo $department_id;?>;
    var post_data = {
        'department_level':'2',
        'type':type,
        'main_department_id':main_department_id,
        'sub_department_id':sub_department_id
    };
	$.ajax({
		type: "POST",
		url: "/organization/set_is_effective/",
		dataType:"json",
		data:post_data,
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
			openWin('js_pop_do_warning');
			return false;
		}
	});
}
</script>

<!--修改门店-->
<div class="pop_box_g pop_box_add_shop" id="js_r_shop">
    <div class="hd">
        <div class="title">修改部门信息</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
        <label class="label clearfix"><span class="text">部门名称：</span><input class="text_input" id="modify_name" value="" type="text"></label>
        <label class="label clearfix"><span class="text">部门电话：</span><input class="text_input" id="modify_telno" value="" type="text"></label>
        <label class="label clearfix"><span class="text">部门地址：</span><input class="text_input" id="modify_address" value="" type="text"></label>
		<label class="label clearfix"><span class="text">区属板块：</span>
			<select id="modify_district" name="dist_id" aria-controls="dataTables-example" class="form-control input-sm" style="line-height:25px;height:25px;font-size:12px;border-radius:3px;">
				<option value="0">请选择</option>
				<?php foreach ($district as $k => $v) { ?>
					<option value="<?php echo $v['id'] ?>" ><?php echo $v['district'] ?></option>
				<?php } ?>
			</select> <span>&nbsp;&nbsp;</span>
			<select id="modify_street" name="street_id" aria-controls="dataTables-example" class="form-control input-sm" style="line-height:25px;height:25px;font-size:12px;border-radius:3px;">
				<option value="0">请选择</option>
			</select>
		</label>
		<label class="label clearfix"><span class="text">部门挂靠：</span>
			<select id="modify_father_department_id" aria-controls="dataTables-example" class="form-control input-sm" style="line-height:25px;height:25px;font-size:12px;border-radius:3px;">
			</select><span><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico2.png" title="一级部门下如无二级部门，则可调整重新挂靠总公司或其它一级部门。
二级部门可重新挂靠其它一级部门，或挂靠总公司成为一级部门。" /></span>
		</label>

        <input type="hidden" value="" id="department_id">
        <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="modify_department()">保存</button>
    </div>
</div>
<!--提示框-->
<div id="js_del_do_result"	class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="dialog_do_result_tip">操作成功</p>
                <button type="button" class="btn-lv1 btn-mid" onclick="location.href='/organization/index/'">确定</button>
            </div>
        </div>
    </div>
</div>
<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">操作成功！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="location.href='/organization/index/<?php echo $now_department_id; ?>'">确定</button>
			</div>
		</div>
	</div>
</div>
<div id="js_pop_do_warning"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_warnig_tip">操作失败！</p>
			</div>
		</div>
	</div>
</div>
<!--复制权限提示-->
<div id="js_pop_do_warning_copy"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" id="copy_close"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_warnig_tip_copy">操作失败！</p>
			</div>
		</div>
	</div>
</div>
<!--创建员工帐号-->
<div class="pop_box_g pop_box_add_shop" id="js_add_account">
    <div class="hd">
        <div class="title">添加员工帐号</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod" style="background:#fff;">
		<label class="label clearfix"><span class="text">部门：</span>
            <select id="account_department_id" style="margin-top:4px;">
            <?php
            if(is_array($company_info_account) && !empty($company_info_account)){
                foreach (array_reverse($company_info_account) as $k =>$v) {
                    if($k < count($company_info_account)-1){
            ?>
                <option value="<?=$v['id'] ?>">├─<?=$v['name']?></option>
                <?php if(isset($v['next_department_data']) && !empty($v['next_department_data'])){
                    foreach($v['next_department_data'] as $key => $value){
                ?>
                    <option value="<?=$value['id'] ?>">　├─<?=$value['name']?></option>
                <?php
                    }
                }?>
            <?php
                    }else{
            ?>
                <option value="<?=$v['id'] ?>">└─<?=$v['name']?></option>
                <?php if(isset($v['next_department_data']) && !empty($v['next_department_data'])){
                    foreach($v['next_department_data'] as $key => $value){
                ?>
                    <option value="<?=$value['id'] ?>">　├─<?=$value['name']?></option>
                <?php
                    }
                }?>
            <?php
                    }
                  }
                 }
                    ?>
            </select>
        </label>
		    <label class="label clearfix"><span class="text">姓名：</span>
            <input class="text_input" type="text" id="truename">
        </label>
        <label class="label clearfix"><span class="text">手机号码：</span>
            <input class="text_input" type="text" id="phone">
        </label>
<!--        <div class="label clearfix"><span class="text">验证码：</span>
            <input class="text_input input_code" type="text" id="code">
            <input type="button" class="get_code" value="获取验证码" onclick="get_code()">
        </div>-->
        <label class="label clearfix"><span class="text">登录密码：</span>
            <input class="text_input"  type="password" id="password">
        </label>
        <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="add_signatory()">确定</button>
    </div>
</div>

<!--重置密码-->
<div class="pop_box_g pop_box_add_shop" id="js_modify_pass">
	<input type="hidden" id="password_signatory_id" >
	<div class="hd">
		<div class="title">修改密码</div>
		<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
	</div>
	<div class="mod">
		<label class="label clearfix"><span class="text">新密码：</span>
            <input class="text_input" type="password" id="new_password"  name="new_password">
        </label>
		<label class="label clearfix"><span class="text">重复新密码：</span>
            <input class="text_input" type="password" id="equal_password" name="equal_password">
        </label>

		<button type="button"  class="btn-lv1 " style="margin-top:10px;margin-left:100px" onclick="modify_pass();">修改密码</button>
		<button type="button"  class="btn-lv1 JS_Close" style="margin-top:10px;margin-left:20px">取消</button>

	</div>
</div>

<!--注销页面-->
<input type='hidden' id='signatory_id' name='signatory_id' >
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_cancel_account1">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td class="c14" valign="top"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico.png"></td>
                        <td>
							<p class="left" style="color:#666;">员工 <strong id="cancel_name"></strong> 名下仍有<strong class="f00" id="sell_num">0</strong>套房源、<strong class="f00" id="rent_num">0</strong>个客源信息，若注销后将会跟随其一起转移，是否仍然注销？</p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left" type="button" onclick="cancel_account();">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
         </div>
    </div>
</div>
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_cancel_account2">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td class="c14">	<img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico.png"></td>
                        <td>
							<p class="left" style="font-size:14px;color:#666;">您确定要注销此帐号吗?</p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left" type="button" onclick="cancel_account();">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
         </div>
    </div>
</div>
<!--删除部门提示框-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_cancel_department">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td class="c14"><img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png">
                        </td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;">您确定要删除该部门吗?</p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left" type="button" onclick="checkdel('',1);">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
                <input type="hidden" value="" id="cancel_department_id">
            </div>
        </div>
    </div>
</div>
<script>
	//添加区属板块触发
	$(function(){
		$('#add_district').change(function(){
			var districtID = $(this).val();
			$.ajax({
				type: 'get',
				url : '/community/find_street_bydis/'+districtID,
				dataType:'json',
				success: function(msg){
					var str = '';
					if(msg.result=='no result'){
						str = '<option value="">请选择</option>';
					}else{
						str = '<option value="">请选择</option>';
						for(var i=0;i<msg.length;i++){
							str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
						}
					}
					$('#add_street').empty();
					$('#add_street').append(str);
				}
			});
		});
	});

	//修改区属板块触发
	$(function(){
		$('#modify_district').change(function(){
			var districtID = $(this).val();
			$.ajax({
				type: 'get',
				url : '/community/find_street_bydis/'+districtID,
				dataType:'json',
				success: function(msg){
					var str = '';
					if(msg.result=='no result'){
						str = '<option value="">请选择</option>';
					}else{
						str = '<option value="">请选择</option>';
						for(var i=0;i<msg.length;i++){
							str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
						}
					}
					$('#modify_street').empty();
					$('#modify_street').append(str);
				}
			});
		});
	});


	$(function () {
		$(".limit-left2 li").hover(function(){
			$(this).find(".l-edit").toggleClass("display-block");
		});
		function re_width(){
			var h1 = $(window).height();
			var w1 = $(window).width() - 280;
			$("#js_left").height(h1 - 50);
//			$("#limit-table").height(h1-180);
			$(".limit-set2 ul").height(h1-136);
			$(".limit-right2").width(w1).show();
		};
		re_width();
		$(window).resize(function(e) {
			re_width();
			setTimeout(function(){
			  $("#js_inner").css("height",($("#js_inner").height()-30)+"px");
			},20)

		});

	});
	window.onload=function(){
		$("#js_inner").css("height",($("#js_inner").height()-30)+"px");
	}

	//门店添加
	function add_department(){
    	var name = $("#add_name").val();
        var telno = $("#add_telno").val();
        var address = $("#add_address").val();
		var department_type = $("#department_type").val();
		var father_department_id = $("#father_department_id").val();

        if(!name){
			$("#dialog_do_warnig_tip").html('请输入部门名称');
            openWin('js_pop_do_warning');
			return false;
		}
        var data = {name:name,telno:telno,address:address,department_type:department_type,father_department_id:father_department_id};
    	$.ajax({
    		type: "POST",
    		url: "/organization/add",
    		dataType:"json",
    		data:data,
    		cache:false,
    		error:function(){
    			$("#dialog_do_warnig_tip").html("系统错误");
        		openWin('js_pop_do_warning');
    			return false;
    		},
    		success: function(data){
    			if(data['errorCode'] == '401')
                {
                    login_out();
                    $("#jss_pop_tip").hide();
                }
                else if(data['errorCode'] == '403')
                {
                    /*purview_none();
                    $("#jss_pop_tip").hide();*/
                	closeWindowWin('js_add_shop');
                	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
                    openWin('js_pop_do_warning');return false;
                }else{
                	if(data.status=="success"){
            			$("#dialog_do_success_tip").html(data.msg);
                		openWin('js_pop_do_success');
            		}else{
            			$("#dialog_do_warnig_tip").html(data.msg);
                		openWin('js_pop_do_warning');
            		}
                }
    		}
    	});

    }

	//门店板块对应id获取
	function modify_pop(id){
		$.ajax({
			type: "POST",
			url: "/organization/edit",
			data: "id="+id,
			dataType:"json",
			cache:false,
			error:function(){
				alert("系统错误");
				return false;
			},
			success: function(data){
				if("none"==data.street){
					$("#modify_street").empty();
					$("#modify_street").append("<option value='0'>请选择</option>");
					openWin("js_r_shop");
				}else if(data.street.length=="0"){
					$("#modify_street").empty();
					$("#modify_street").append("<option value='0'>请选择</option>");
					openWin("js_r_shop");
				}else{
					$("#modify_street").empty();
					$("#modify_street").append("<option value='0'>请选择</option>");
					for( var i in data.street){
						$("#modify_street").append("<option value='"+data.street[i]['id']+"'>"+data.street[i]['streetname']+"</option>");
						if(data.street[i]['id']==data.street_id){
							$("#modify_street").val(data.street_id);
						}
					}
					openWin("js_r_shop");
				}
			}
		});
	}

	//门店修改
	function modify_department(){
    	var department_id = $("#department_id").val();
    	var name = $("#modify_name").val();
        var telno = $("#modify_telno").val();
        var address = $("#modify_address").val();
		var dist_id = $("#modify_district").val();
        var street_id = $("#modify_street").val();
        var modify_father_department_id = $("#modify_father_department_id").val();
        if(!name){alert("请输入部门名称");return false;}
        if(!telno){alert("请输入部门电话");return false;}
        if(!address){alert("请输入部门地址");return false;}
		if(!street_id){alert("请输入区属板块");return false;}
        var data = {department_id:department_id,name:name,telno:telno,address:address,dist_id:dist_id,street_id:street_id,modify_father_department_id:modify_father_department_id};
        $.ajax({
        	type: "POST",
        	url: "/organization/modify",
        	dataType:"json",
        	data:data,
        	cache:false,
        	error:function(){
        		$("#dialog_do_warnig_tip").html("系统错误");
        		openWin('js_pop_do_warning');
        		return false;
        	},
        	success: function(data){
        		if(data['errorCode'] == '401')
                {
                    login_out();
                    $("#jss_pop_tip").hide();
                }
                else if(data['errorCode'] == '403')
                {
                	closeWindowWin('js_r_shop');
                	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
                    openWin('js_pop_do_warning');
                }else{
                	if(data.status=="success"){
            			$("#dialog_do_success_tip").html(data.msg);
                		openWin('js_pop_do_success');
            		}else{
            			$("#dialog_do_warnig_tip").html(data.msg);
                		openWin('js_pop_do_warning');
            		}
                }
        	}
        });

    }
    //门店删除
    function checkdel(id, sure)
    {

        if (sure == 1) {
            var id = $('cancel_department_id').val();
            var data = {department_id:id};
            $.ajax({
                type: "POST",
                url: "/organization/delete",
                dataType:"json",
                data:data,
                error:function(){
                    $("#dialog_do_warnig_tip").html("系统错误");
                    openWin('js_pop_do_warning');
                    return false;
                },
                success: function(data){
                    closeWindowWin('js_cancel_department');
                    $("#dialog_do_result_tip").html(data.msg);
                    openWin('js_del_do_result');
                }
            });
        } else {
            $('cancel_department_id').val(id);
            openWin('js_cancel_department');
        }
    }
//
//  //获取验证码
//  var InterValObj; //timer变量，控制时间
//  var count = 60; //间隔函数，1秒执行
//  var curCount;//当前剩余秒数
//
//  function get_code() {
//    curCount = count;
//    var phone = $("#phone").val();
//    var partten = /^1\d{10}$/;
//    if (!phone || !partten.test(phone)) {
//      $("#dialog_do_warnig_tip").html("请输入正确的11位手机号码");
//      openWin('js_pop_do_warning');
//      return false;
//    }
//    $.ajax({
//      type : 'get',
//      url: '/signatory_sms/',
//      data : {phone : phone, type : 'register'},
//      dataType :'json',
//      cache: false,
//      error: function (resp) {
//        $("#dialog_do_warnig_tip").html("系统错误");
//        openWin('js_pop_do_warning');
//        return false;
//      },
//      success: function (data) {
//        if (data.status == 1) {
//          $(".get_code").attr("disabled", "true");
//          InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
//        } else {
//          $("#dialog_do_warnig_tip").html(data.msg);
//          openWin('js_pop_do_warning');
//          return false;
//        }
//      }
//    });
//  }
//
//  //timer处理函数
//  function SetRemainTime() {
//    if (curCount == 0) {
//      window.clearInterval(InterValObj);//停止计时器
//      $(".get_code").removeAttr("disabled");//启用按钮
//      $(".get_code").val("重新获取验证码");
//    }
//    else {
//      $(".get_code").val(curCount + "s");
//      curCount--;
//    }
//  }
  //添加经纪人
  function add_signatory() {
    var partten = /^1\d{10}$/;
    var department_id = $("#account_department_id").val();
    var truename = $("#truename").val();
    var phone = $("#phone").val();
    var password = $("#password").val();
    var code = $("#code").val();
    /*if(!department_id){
     department_id = 0;
     }*/
    if (!truename) {
      $("#dialog_do_warnig_tip").html("请输入姓名");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!phone || !partten.test(phone)) {
      $("#dialog_do_warnig_tip").html("请输入正确11位手机号码");
      openWin('js_pop_do_warning');
      return false;
    }

    if (!password) {
      $("#dialog_do_warnig_tip").html("请输入注册密码");
      openWin('js_pop_do_warning');
      return false;
    }
/*    if (!code) {
      $("#dialog_do_warnig_tip").html("请输入验证码");
      openWin('js_pop_do_warning');
      return false;
    }*/
    var data = {department_id: department_id, phone: phone, password: password,/* code: code,*/ truename: truename};
    $.ajax({
      type: "POST",
      url: "/organization/add_account",
      dataType: "json",
      data: data,
      cache: false,
      error: function () {
        $("#dialog_do_warnig_tip").html("系统错误");
        openWin('js_pop_do_warning');
        return false;
      },
      success: function (data) {
        if (data['errorCode'] == '401') {
          login_out();
          $("#jss_pop_tip").hide();
        }
        else if (data['errorCode'] == '403') {
          /*purview_none();
           $("#jss_pop_tip").hide();*/
          closeWindowWin('js_add_shop');
          $("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
          openWin('js_pop_do_warning');
          return false;
        } else {
          if (data.status == "success") {
            $("#dialog_do_success_tip").html(data.msg);
            openWin('js_pop_do_success');
          } else {
            $("#dialog_do_warnig_tip").html(data.msg);
            openWin('js_pop_do_warning');
          }
        }
      }
    });

  }
  //重置密码弹出框

  function modify_pass_pop(signatory_id) {
    $.ajax({
      type: "POST",
      url: "/organization/modify_password_pop",
      dataType: "json",
      data: {
        "signatory_id": signatory_id
      },
      cache: false,
      error: function () {
        alert("系统错误");
        return false;
      },
      success: function (data) {
        if (data && data.signatory_id) {
          $('#password_signatory_id').val(data.signatory_id);
          openWin('js_modify_pass', '', function() {
            $('#new_password').val('');
            $('#equal_password').val('');
          });
        } else {
          openWin('js_modify_pass', '', function() {
            $('#new_password').val('');
            $('#equal_password').val('');
          });
        }
      }
    });
  }
  //重置密码
  function modify_pass() {
    //var old_password = $('#old_password').val();
    var signatory_id = $('#password_signatory_id').val();
    var new_password = $('#new_password').val();
    var equal_password = $('#equal_password').val();
    /*if(!old_password){
     $('#dialog_do_warnig_tip').html('请输入正确的原密码');
     openWin('js_pop_do_success');
     return false;
     }*/
    if (!new_password) {
      $('#dialog_do_warnig_tip').html('请输入新密码');
      openWin('js_pop_do_warning');
      return false;
    }
    if (!equal_password) {
      $('#dialog_do_warnig_tip').html('两次密码输入不一致');
      openWin('js_pop_do_warning');
      return false;
    }
    var data = {
      //'old_password':old_password,
      'signatory_id': signatory_id,
      'new_password': new_password,
      'equal_password': equal_password
    };
    $.ajax({
      type: "POST",
      url: "/organization/modify_password",
        dataType: "json",
      data: data,
      cache: false,
      error: function () {
        alert("系统错误");
        openWin('js_pop_do_warning');
        return false;
      },
      success: function (return_data) {
        /*if('password_not_true'==return_data){
         $('#dialog_do_warnig_tip').html('请输入正确的原密码');
         openWin('js_pop_do_warning');
         }else */
        //alert(return_data);
          if ('password_not_same' == return_data["result"]) {
          $('#dialog_do_warnig_tip').html('两次密码输入不一致');
//          closeWindowWin('js_modify_pass');
          openWin('js_pop_do_warning');
          } else if (1 == return_data["result"]) {
          $('#dialog_do_warnig_tip').html('修改成功');
          closeWindowWin('js_modify_pass');
          $('#new_password').val('');
          $('#equal_password').val('');
          openWin('js_pop_do_success');
        } else {
          $('#dialog_do_warnig_tip').html('修改失败');
          closeWindowWin('js_modify_pass');
          $('#new_password').val('');
          $('#equal_password').val('');
          openWin('js_pop_do_warning');
        }
      }
    });

  }
  //注销帐号弹框
  function cancel_account_pop(signatory_id) {
    $.ajax({
      type: "POST",
      url: "/organization/cancel_account",
      data: "signatory_id_pop=" + signatory_id,
      dataType: "json",
      cache: false,
      error: function () {
        alert("系统错误");
        return false;
      },
      success: function (data) {
        //alert(data.id);
        if (data.id) {
          if (data.house_num || data.customer_num) {
            $('#signatory_id').val(data['id']);
            $('#sell_num').html(data['house_num']);
            $('#rent_num').html(data['customer_num']);
            $('#cancel_name').html(data['cancel_name']);
            openWin('js_cancel_account1');
          } else {
            $('#signatory_id').val(data['id']);
            openWin('js_cancel_account2');
          }
        } else {
          openWin('js_cancel_account2');
        }
      }
    });

  }

	//注销帐号
	function cancel_account(){
		var signatory_id = $('#signatory_id').val();
		//alert(signatory_id);
		$.ajax({
			type: "POST",
			url: "/organization/cancel_account",
			data: "signatory_id="+signatory_id,
			//dataType:"json",
			cache:false,
			error:function(){
				alert("系统错误");
				return false;
			},
			success: function(data){
				//alert(data);
				if(1==data){
					$('#js_cancel_account1').hide();
					$('#js_cancel_account2').hide();
					$('#dialog_do_warnig_tip').html('注销成功');
					openWin('js_pop_do_success');
				}else{
					$('#js_cancel_account1').hide();
					$('#js_cancel_account2').hide();
					$('#dialog_do_warnig_tip').html('此帐号已被注销');
					openWin('js_pop_do_warning');
				}
			}
		});

	}

    //复制权限弹框
	$(function () {
		//复制全选中状态
		$("h3.on").find(".zws_copy_power_checkbox").live("click",function(){
			//alert($(this).parents("h3").next().html())
			$(this).parents("h3").next("ul").find("b").addClass("copy_checkon");


		});

	    $(".zws_copy_power_checkbox").live("click", function () {
	        if ($(this).hasClass("copy_checkon")) {
	            $(this).removeClass("copy_checkon");
	            $(this).find("input").removeAttr("checked", false);
	        }
	        else {
	            $(this).addClass("copy_checkon");
	            $(this).find("input").attr("checked", true);
	        }
	    });
	    $(".zws_store_list li:last-child").each(function () {
	        $(this).find("a").eq(0).addClass("zws_copy_power_child_last");

	    })

		$(".relevance .pull-left h3 a.zws_copy_power_parent").click(function () {
			$(this).toggleClass('txte-on');
			$(this).parent().next('ul').toggle();
		});

		//模拟单选按钮
		$(".relevance .pull-left li a.on-off").on('click' ,function(){
			var self = $(this).parents("ul").find("a.on-off");
			var chknum = self.size();
			var chk = 0;

			var i = $(this);
			if($(this).hasClass("off-on"))
			{
				i.find(".js_checkbox").prop("checked",true);
				i.removeClass("off-on");
			}
			else
			{
				i.find(".js_checkbox").prop("checked",false);
				i.addClass("off-on");
			};

		})

        //复制权限提交按钮
        $('#submit_copy_department_perm').live('click',function(){
            var copy_department = [];
            $('input[name="copy_department[]"]:checked').each(function(){
                copy_department.push($(this).val());
            });
            if(copy_department.length == 0){
                $("#dialog_do_warnig_tip_copy").html("请选择部门");
                openWin('js_pop_do_warning_copy');
                return false;
            }
            $.ajax({
                type: "POST",
                url: "/organization/copy_department_per/",
                data:$("#copy_department_per_form").serialize(),
                cache:false,
                error:function(){
                    $("#dialog_do_warnig_tip_copy").html("系统错误");
                    openWin('js_pop_do_warning_copy');
                    return false;
                },
                success: function(data){
                    if('success'==data){
                        $("#dialog_do_warnig_tip_copy").html("操作成功");
                        openWin('js_pop_do_warning_copy');
                        return false;
                    }else{
                        $("#dialog_do_warnig_tip_copy").html("系统错误");
                        openWin('js_pop_do_warning_copy');
                        return false;
                    }
                }
            });
        });

        //复制权限2，保存设置按钮
        $('#submit_copy_department_perm_2').live('click',function(){
            //主门店id
            var main_department_id = <?php echo $department_id;?>;
            //勾选被复制的门店
            var copy_department_2 = [];
            $('input[name="copy_department_2[]"]:checked').each(function(){
                copy_department_2.push($(this).val());
            });
            if(copy_department_2.length == 0){
                $("#dialog_do_warnig_tip_copy").html("请选择部门");
                openWin('js_pop_do_warning_copy');
                return false;
            }
            $.ajax({
                type: "POST",
                url: "/organization/copy_department_per_2/",
                data:$("#copy_department_per_form_2").serialize(),
                cache:false,
                error:function(){
                    $("#dialog_do_warnig_tip_copy").html("系统错误");
                    openWin('js_pop_do_warning_copy');
                    return false;
                },
                success: function(data){
                    if('success'==data){
                        $("#dialog_do_warnig_tip_copy").html("操作成功");
                        openWin('js_pop_do_warning_copy');
                        return false;
                    }else{
                        $("#dialog_do_warnig_tip_copy").html("系统错误");
                        openWin('js_pop_do_warning_copy');
                        return false;
                    }
                }
            });
        });

        $('#copy_close').live('click',function(){
            closeWindowWin('js_copy_department_access_area');
            closeWindowWin('js_copy_department_access_area_2');
        });
	});

	jQuery.fn.limit=function(){
		var self = $(this);
		self.each(function(){
			var objString = $(this).text();
			var objLength = $(this).text().length;
			var num = $(this).attr("limit");
			if(objLength > num){
				$(this).attr("title",objString);
				objString = $(this).text(objString.substring(0,num) + "...");
			}
		})
	}
</script>
