
<script>
    window.parent.addNavClass(2);
</script>
<style>
	.limit-left2 li{background:#F9F9F9 url(<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/tab_01.png) no-repeat 15px center; padding-left:30px;}
	.limit-left2 li.hover{background-color:#CCE8FE !important;}
	.limit-left2 li.hover a{color:#227AC6;}
	.limit-left2 li.tab-top{background:#F9F9F9 url(<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/tab_02.png) no-repeat 15px center;}
	.limit-left2 .on{background:#5CA2DF !important; border:none; margin-top:-1px;}
	.limit-left2 .on a{color:#fff;}
	.limit-left2 .on a:hover{color:#227AC6;}
	.ui-menu{width:164px !important; border-color:#D1D1D1;}
  .limit-left2 li.nullMuen{background: #f9f9f9}
  .relevance .pull-left h3 a.txte.aMuen{background: #f9f9f9;}
  .relevance .pull-left h3 a.txte-on.aMuen{background: #5ca2de;}
</style>
<script type="text/javascript">
  $(function () {

    //当前类型下的一级类型。（用于修改类型中的下拉框，组装html）
    var dictionary_type_oneleval_data = <?php echo json_encode($dictionary_type_oneleval_data);?>;
    var html_str = '';
    var company_str = '<option value="0">[类型]</option>';
    html_str += company_str;
    for (key in dictionary_type_oneleval_data) {
      if (key < dictionary_type_oneleval_data.length - 1) {
        html_str += '<option value="' + dictionary_type_oneleval_data[key].id + '">' + dictionary_type_oneleval_data[key].name + '</option>';
      } else {
        html_str += '<option value="' + dictionary_type_oneleval_data[key].id + '"> ' + dictionary_type_oneleval_data[key].name + '</option>';
      }
    }
    $(".modify").live("click", function (event) {
      var rel = $(this).attr("rel");
      var name = $("#" + rel + "_name").text();
      var name_abbr = $("#" + rel + "_name_abbr").text();
      var desc = $("#" + rel + "_desc").text();
      var dictionary_type_id = $("#" + rel + "_dictionary_type_id").text();
      var is_has_dictionary_type = $("#" + rel + "is_has_dictionary_type").text();
      $("#dictionary_type_id").val(rel);
      $("#modify_name").val(name);
      $("#modify_name_abbr").val(name_abbr);
      $("#modify_desc").val(desc);
      //判断门店是否有下属类型
      if (0 == is_has_dictionary_type) {
        $("#modify_father_dictionary_type_id").html('');
        $("#modify_father_dictionary_type_id").html(html_str);
        $("#modify_father_dictionary_type_id").val(dictionary_type_id);
      } else {
        $("#modify_father_dictionary_type_id").html('');
        $("#modify_father_dictionary_type_id").html(html_str);
        $("#modify_father_dictionary_type_id").val(0);
      }
      event.stopPropagation();
      return false;
    });


    $(".modify").live("click", function (event) {
      var rel = $(this).attr("rel");
      var name = $("#" + rel + "_name").text();
      var name_abbr = $("#" + rel + "_name_abbr").text();
      var desc = $("#" + rel + "_desc").text();
      var dictionary_type_id = $("#" + rel + "_dictionary_type_id").text();
      var is_has_dictionary_type = $("#" + rel + "is_has_dictionary_type").text();
      $("#dictionary_type_id").val(rel);
      $("#modify_name").val(name);
      $("#modify_name_abbr").val(name_abbr);
      $("#modify_desc").val(desc);
      //判断门店是否有下属类型
      if (0 == is_has_dictionary_type) {
        $("#modify_father_dictionary_type_id").html('');
        $("#modify_father_dictionary_type_id").html(html_str);
        $("#modify_father_dictionary_type_id").val(dictionary_type_id);
      } else {
        $("#modify_father_dictionary_type_id").html('');
        $("#modify_father_dictionary_type_id").html(html_str);
        $("#modify_father_dictionary_type_id").val(0);
      }
      event.stopPropagation();
      return false;
    });

    //$("[limit]").limit();

    $(".labelall").live('click', function () {
      var i = $(this).parent().next(".limit-right-cont");
      if ($(this).hasClass('labelon')) {
        $(this).removeClass('labelon')
        i.find("b.label").removeClass("labelon");
        i.find(".js_checkbox").prop("checked", false);
        $(this).find(".input_checkbox").prop("checked", false);
        i.find(".input_checkbox").prop("checked", false);
      } else {
        $(this).addClass('labelon')
        i.find("b.label").addClass("labelon");
        i.find(".js_checkbox").prop("checked", true);
        $(this).find(".input_checkbox").prop("checked", true);
        i.find(".input_checkbox").prop("checked", true);
      }
    });
    $(".relevance .pull-left h3 a.txte").click(function () {
      $(".relevance .pull-left h3 a.txte").removeClass('txte-on');
      $(".relevance .pull-left li a.txte").removeClass('txte-on');
      $(this).toggleClass('txte-on');
      $(this).parent().next("ul").toggle();


    });
    $(".relevance .pull-left h3 a.on-off").on('click', function () {
      var deal_type = $(this).attr('deal_type');
      if ('2' == deal_type) {
        return false;
      }
      var is_effective_dictionary_type_id = $(this).parent().attr('value');

      if ($(this).hasClass('off-on')) {
        $(this).removeClass('off-on');
        // $(this).parent().next('ul').find("a.on-off").removeClass('off-on');
        is_effective_ajax_level_two(1, is_effective_dictionary_type_id);
      } else {
        $(this).addClass('off-on');
        //$(this).parent().next('ul').find("a.on-off").addClass('off-on');
        is_effective_ajax_level_two(0, is_effective_dictionary_type_id);
      }
    });

    //模拟单选按钮
    $(".relevance .pull-left li a.on-off").on('click', function () {
      var self = $(this).parents("ul").find("a.on-off");
      var chknum = self.size();
      var chk = 0;
      var i = $(this);
      var is_effective_dictionary_type_id = i.parent().val();
      if ($(this).hasClass("off-on")) {
        i.find(".js_checkbox").prop("checked", true);
        //i.removeClass("off-on");
        is_effective_ajax_level_two(1, is_effective_dictionary_type_id);
      }
      else {
        i.find(".js_checkbox").prop("checked", false);
        //i.addClass("off-on");
        is_effective_ajax_level_two(0, is_effective_dictionary_type_id);
      }
      ;

      self.each(function () {
        if ($(this).hasClass("off-on")) {
          chk++;
        }
      });
      if (chknum == chk) {//全选
        //i.parents("ul").prev().find("a.on-off").addClass('off-on');
        i.parents("ul").prev().find(".js_checkbox").prop("checked", false);
      } else {//不全选
        // i.parents("ul").prev().find("a.on-off").removeClass('off-on');
        i.parents("ul").prev().find(".js_checkbox").prop("checked", true);
      }
      ;
    });

    $(".relevance .pull-left li a.txte").click(function () {
      $(".relevance .pull-left h3 a.txte").removeClass('txte-on');
      $(".relevance .pull-left li a.txte").removeClass('txte-on');
      $(this).toggleClass('txte-on');
    });

    $(".label_radio").live('click', function () {
      if ($(this).hasClass('labelon')) {
        $(this).removeClass('labelon')
        $(this).find(".input_checkbox").prop("checked", false);
      } else {
        $(this).addClass('labelon')
        $(this).find(".input_checkbox").prop("checked", true);
      }
    });


    $(".label_all").live('click', function () {
      if ($(this).hasClass('labelon')) {
        $(this).removeClass('labelon')
        $("b.label").removeClass("labelon");
        $(".js_checkbox").prop("checked", false);
        $(this).find(".input_checkbox").prop("checked", false);
        $(".input_checkbox").prop("checked", false);
      } else {
        $(this).addClass('labelon')
        $("b.label").addClass("labelon");
        $(".js_checkbox").prop("checked", true);
        $(this).find(".input_checkbox").prop("checked", true);
        $(".input_checkbox").prop("checked", true);
      }
    });

    $('.dictionary_type_id').live('click', function () {
      var main_dictionary_type_id = <?php echo $dictionary_type_id;?>;
      var sub_dictionary_type_id = $(this).attr('value');
      $('#main_dictionary_type_id').val(main_dictionary_type_id);
      $('#sub_dictionary_type_id').val(sub_dictionary_type_id);
      $('#copy_main_dictionary_type_id').val(main_dictionary_type_id);
      $('#copy_sub_dictionary_type_id').val(sub_dictionary_type_id);
      if (main_dictionary_type_id == sub_dictionary_type_id) {
        $('input[name="child_node_id[]"]').attr('checked', false);
        $('input[name="child_node_id[]"]').parent().addClass('label-hui');
        $('#submit_dictionary_type_perm_button').hide();
        $('#copy_dictionary_type_perm_button').hide();
      } else {
        $('input[name="child_node_id[]"]').parent().removeClass('label-hui');
        $('#submit_dictionary_type_perm_button').show();
        $('#copy_dictionary_type_perm_button').show();
        $.ajax({
          url: "<?php echo MLS_SIGN_URL;?>/dictionary_type/get_dictionary_type_per_node/" + main_dictionary_type_id + "/" + sub_dictionary_type_id,
          type: "GET",
          dataType: "json",
          success: function (data) {
            $('input[name="child_node_id[]"]').attr('checked', false);
            $('input[name="child_node_id[]"]').parent().removeClass('labelon');
            var func_auth = data['func_auth'];
            for (var i in func_auth) {
              $("#input" + func_auth[i]).attr('checked', true);
              $("#input" + func_auth[i]).parent().addClass('labelon');
            }
          }
        });
      }
    });

    $('.dictionary_type_id_2').live('click', function () {
      var main_dictionary_type_id = <?php echo $dictionary_type_id;?>;
      var sub_dictionary_type_id = $(this).attr('value');
      $('#main_dictionary_type_id').val(main_dictionary_type_id);
      $('#sub_dictionary_type_id').val(sub_dictionary_type_id);
      $('#copy_main_dictionary_type_id_2').val(main_dictionary_type_id);
      $('#copy_sub_dictionary_type_id_2').val(sub_dictionary_type_id);
      if (main_dictionary_type_id == sub_dictionary_type_id) {
        $('input[name="child_node_id[]"]').attr('checked', false);
        $('input[name="child_node_id[]"]').parent().addClass('label-hui');
      } else {
        $('input[name="child_node_id[]"]').attr('checked', false);
        $('input[name="child_node_id[]"]').parent().removeClass('label-hui');
        $.ajax({
          url: "<?php echo MLS_SIGN_URL;?>/dictionary_type/get_dictionary_type_per_node/" + main_dictionary_type_id + "/" + sub_dictionary_type_id,
          type: "GET",
          dataType: "json",
          success: function (data) {
            $('input[name="child_node_id[]"]').attr('checked', false);
            $('input[name="child_node_id[]"]').parent().removeClass('labelon');
            var func_auth = data['func_auth'];
            for (var i in func_auth) {
              $("#input_copy" + func_auth[i]).parent().addClass('label-hui');
            }
          }
        });
      }
    });

    $.widget("custom.autocomplete", $.ui.autocomplete, {
      _renderItem: function (ul, item) {
        if (item.id > 0) {
          return $("<li>")
            .data("item.autocomplete", item)
            .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.label+'</span></a>').appendTo(ul);
        } else {
          return $("<li>")
            .data("item.autocomplete", item)
            .append('<a class="ui-corner-all" tabindex="-1">' + item.label + '</a>')
            .appendTo(ul);
        }
      }
    });
    $("#dictionary_type_name").autocomplete({
      source: function (request, response) {
        var term = request.term;
        $.ajax({
          url: "/dictionary_type/get_dictionary_type_info_by_kw/",
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
      width: 2,
      select: function (event, ui) {
        if (ui.item.id > 0) {
          var dictionary_typename = ui.item.name;
          var id = ui.item.id;
          //操作
          $("#select_dictionary_type_id").val(id);
          $("#dictionary_type_name").val(dictionary_typename);
          dictionary_type_name_search(dictionary_typename);
          removeinput = 2;
        } else {
          removeinput = 1;
        }
      },
      close: function (event) {
        if (typeof(removeinput) == 'undefined' || removeinput == 1) {
          //$("#dictionary_type_name").val("");
          $("#select_dictionary_type_id").val("");
        }
      }
    });

    $("#dictionary_name").autocomplete({
      source: function (request, response) {
        var term = request.term;
        //$("#dictionary_type_id").val("");
        $.ajax({
          url: "/dictionary_type/get_dictionary_info_by_kw/",
          type: "GET",
          dataType: "json",
          data: {
            keyword: term,
            dictionary_type_id: '<?=$now_dictionary_type_id?>'
          },
          success: function (data) {
            //判断返回数据是否为空，不为空返回数据。
            if (data[0]['id'] != '0') {
              //alert(data[0]['dictionary_id']);
              response(data);
            } else {
              response(data);
            }
          }
        });
      },
      minLength: 1,
      removeinput: 0,
      width: 2,
      select: function (event, ui) {
        if (ui.item.id > 0) {
          var dictionary_name = ui.item.name;
          var id = ui.item.id;
          var dictionary_type_id = '<?=$now_dictionary_type_id?>'; //ui.item.dictionary_type_id;
          //操作
          $("#select_dictionary_id").val(id);
          $("#dictionary_name").val(dictionary_name);
          dictionary_name_search(dictionary_type_id, id, dictionary_name);
          removeinput = 2;
        } else {
          removeinput = 1;
        }
      },
      close: function (event) {
        if (typeof(removeinput) == 'undefined' || removeinput == 1) {
          $("#select_dictionary_id").val("");
          //$("#dictionary_name").val("");
        }
      }
    });

    $('#search_button').live('click', function () {
      var dictionary_type_name = $('#dictionary_type_name').val();
      dictionary_type_name_search(dictionary_type_name);
      return false;
    });

    $('#search_dictionary').live('click', function () {
      var dictionary_id = $('#select_dictionary_id').val();
      var dictionary_type_id = '<?=$now_dictionary_type_id?>';
      var dictionary_name = $('#dictionary_name').val();
      window.location.href = '/dictionary_type/index/' + dictionary_type_id + '/' + dictionary_id + '?view_type_sub=list&search_keyword=' + encodeURIComponent(dictionary_name);
      return false;
    });
  });

  jQuery.fn.limit = function () {
    var self = $(this);
    self.each(function () {
      var objString = $(this).text();
      var objLength = $(this).text().length;
      var num = $(this).attr("limit");
      if (objLength > num) {
        $(this).attr("title", objString);
        objString = $(this).text(objString.substring(0, num) + "...");
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
        <h3 class="l-h1 f14" style="height:30px; display:none;"><?php echo $dictionary_type_name?></h3>
        <div class="limit-search clearfix"><input type="text" name="dictionary_type_name" id="dictionary_type_name" value="<?php echo $search_type_keyword ? $search_type_keyword : '请输入类型关键词'?>" onfocus="if(value=='<?php echo $search_type_keyword ? $search_type_keyword : '请输入类型关键词'?>'){value='';$(this).css('color','#535353');}" onblur="if(value==''){value='<?php echo $search_type_keyword ? $search_type_keyword : '请输入类型关键词'?>';$(this).css('color','#999');}" autocomplete="off"><button id='search_button'>搜索</button></div>
        <div class="limit-left2-wrapper">
        <div class="limit-search-text" style="<?php echo ('search'==$view_type)?'display:block;':'display:none;'; ?>"><a class="fr" href="<?php echo MLS_SIGN_URL;?>/dictionary_type/index/">返回</a>已搜索到<span class="f00" id="search_result_num"><?php echo $search_result_num;?></span>条相关门店</div>
        <input name="select_dictionary_type_id" id="select_dictionary_type_id" value="" type="hidden">
        <ul id="dictionary_type_list">
        <?php if('index'==$view_type){?>
          <?php if(is_array($dictionary_type_info) && !empty($dictionary_type_info)){
            foreach(array_reverse($dictionary_type_info) as $key=>$vo) {?>
              <li <?php if($now_dictionary_type_id == $vo['id']){
                $now_dictionary_type_name = $vo['name'];
                echo "class='tab-top on'";
              }else if($vo['is_has_dictionary_type']!='1'){
                echo "class='nullMuen'";
              }
              ?>>
                <a class="l-edit l-edit-3" href="javascript:void(0)" onclick="checkdel(<?php echo $vo['id']?>)" title="删除">删除</a>
                <a class="l-edit l-edit-2 modify" href="javascript:void(0)" onClick="modify_pop(<?=$vo['id']?>)" rel="<?php echo $vo['id']?>" title="编辑">编辑</a>
                <a id=""  style="font-weight:bold;" href="/dictionary_type/index/<?php echo $vo['id']?>"><?php echo $vo['name']?></a>
              </li>
              <li style="display:none"><div  id="<?php echo $vo['id']?>_name"><?php echo $vo['name'] ?></div></li>
              <li style="display:none"><div  id="<?php echo $vo['id']?>_name_abbr"><?php echo $vo['name_abbr'] ?></div></li>
              <li style="display:none"><div  id="<?php echo $vo['id']?>_desc"><?php echo $vo['desc'] ?></div></li>
              <li style="display:none"><div  id="<?php echo $vo['id']?>_dictionary_type_id"><?php echo $vo['dictionary_type_id'] ?></div></li>
              <li style="display:none"><div  id="<?php echo $vo['id']?>is_has_dictionary_type"><?php echo $vo['is_has_dictionary_type'] ?></div></li>

              <!--二级门店-->
              <?php if(isset($vo['next_dictionary_type_data']) && !empty($vo['next_dictionary_type_data'])){
                foreach($vo['next_dictionary_type_data'] as $key => $value){
                  ?>
                  <li style="<?php echo ($vo['id']==$now_dictionary_type_id||$value['dictionary_type_id']==$now_father_dictionary_type_id)?'display:block; background:url('.MLS_SOURCE_URL.'/mls_guli/images/v1.0/tab_02.png) no-repeat 30px center; padding-left:45px;':'display:none;';?>" id="<?php echo $vo['id']?>_next_dictionary_type" <?php if($value['id']==$now_dictionary_type_id){echo 'class="on tab-bottom2"';}?>>
                    <a class="l-edit l-edit-3" href="javascript:void(0)" onclick="return checkdel(<?php echo $value['id']?>)" title="删除">删除</a>
                    <a class="l-edit l-edit-2 modify" href="javascript:void(0)" onClick="modify_pop(<?=$value['id']?>)" rel="<?php echo $value['id']?>" title="编辑">编辑</a>
                    <a id="" href="/dictionary_type/index/<?php echo $value['id']?>"><?php echo $value['name']?></a>
                  </li>
                  <li style="display:none"><div  id="<?php echo $value['id']?>_name"><?php echo $value['name'] ?></div></li>
                  <li style="display:none"><div  id="<?php echo $value['id']?>_name_abbr"><?php echo $value['name_abbr'] ?></div></li>
                  <li style="display:none"><div  id="<?php echo $value['id']?>_desc"><?php echo $value['desc'] ?></div></li>
                  <li style="display:none"><div  id="<?php echo $value['id']?>_dictionary_type_id"><?php echo $value['dictionary_type_id'] ?></div></li>
                  <li style="display:none"><div  id="<?php echo $value['id']?>is_has_dictionary_type"><?php echo $value['is_has_dictionary_type'] ?></div></li>
                  <?php
                }
              }
              ?>
          <?php } }?>
          <?php }else if('search'==$view_type){?>
            <?php if(is_full_array($search_dictionary_type_data)){
              foreach($search_dictionary_type_data as $key => $value){
                ?>
                <li <?php if($now_dictionary_type_id == $value['dictionary_type_id']){
                  $now_dictionary_type_name = $value['name'];
                  echo "class='tab-top on'";
                }else if($vo['is_has_dictionary_type']!='1'){
                  echo "class='nullMuen'";
                }
                ?>>
                  <a class="l-edit l-edit-2 modify" href="javascript:void(0)" onClick="modify_pop(<?=$value['dictionary_type_id']?>)" rel="<?php echo $value['dictionary_type_id']?>" title="编辑">编辑</a>
                  <a id=""  style="font-weight:bold;" href="/dictionary_type/index/<?php echo $value['id']?>?view_type=search&search_type_keyword=<?php echo $search_type_keyword ? urlencode($search_type_keyword): '';?>&search_dictionary_type_id=<?php echo $search_dictionary_type_id_str;?>"><?php echo $value['name']?></a>
                </li>
                <li style="display:none"><div  id="<?php echo $value['id']?>_name"><?php echo $value['name'] ?></div></li>
                <li style="display:none"><div  id="<?php echo $value['id']?>_name_abbr"><?php echo $value['name_abbr'] ?></div></li>
                <li style="display:none"><div  id="<?php echo $value['id']?>_desc"><?php echo $value['desc'] ?></div></li>
                <li style="display:none"><div  id="<?php echo $value['id']?>_dictionary_type_id"><?php echo $value['dictionary_type_id'] ?></div></li>
                <li style="display:none"><div  id="<?php echo $value['id']?>is_has_dictionary_type"><?php echo $value['is_has_dictionary_type'] ?></div></li>
              <?php }}?>
          <?php }?>
        </ul>
      </div>

			<?php if(in_array($level, array(1,2,3,4,5))){?>
        <div class="limit-left2-operation">
          <a href="javascript:void(0)" class="btn-lan4 f14" onClick="openWin('js_add_shop')">添加类型</a>
          <!--			<a href="javascript:void(0)" class="btn-lan4 f14" onClick="openWin('js_copy_dictionary_type_access_area_2')">复制关联门店权限</a>-->
        </div>
      <?php } ?>
		</div>
		<script>
			$(function(){
				$(".limit-left2 li").hover(function(){
					$(this).addClass('hover');
          $(this).find(".l-edit").addClass("display-block");
				},function(){
          $(this).removeClass('hover');
          $(this).find(".l-edit").removeClass("display-block");
        })
			});

		</script>

		<div class="limit-right2">
		  <div style="display:block;">
        <div class="tool-bar limit-top mb10" id="js_search_box">
          <div class="limit-search ml10 fr"><input type="text" name="dictionary_name" id="dictionary_name" value="<?php echo $search_keyword ? $search_keyword : '请输入关键字查找'?>" onfocus="if(value=='<?php echo $search_keyword ? $search_keyword : '请输入关键字查找'?>'){value='';$(this).css('color','#535353');}" onblur="if(value==''){value='<?php echo $search_keyword ? $search_keyword : '请输入关键字查找'?>';$(this).css('color','#999');}" autocomplete="off" class="ui-autocomplete-input"><button id="search_dictionary">搜索</button></div>
          <a href="javascript:void(0)" onClick="openWin('js_add_dictionary')" class="add_link" style="margin-top:0;"><span class="iconfont"></span>添加数据</a>
          <input name="select_dictionary_id" id="select_dictionary_id" value="" type="hidden">
          <h3><?php echo $store_name?></h3>
        </div>
			</div>
			<div class="table_all" style="margin-right:0; margin-left:0;">
				<div id="js_inner" class="inner" style="overflow-y: scroll; overflow-x:auto;">
					<table class="table">
              <thead>
                <tr>
                  <th class="c10">序号</th>
                  <th class="c10">数据类型</th>
                  <th class="c20">键/Key</th>
                  <th class="c20">值/Value</th>
                  <th class="c10">缩写/Abbr</th>
                  <th>描述</th>
                  <th class="c10">操作</th>
                </tr>
              </thead>
							<tbody>
              <?php
              if ($signatory_all_info) {
                foreach ($signatory_all_info as $k => $v) { ?>
                  <tr height="40">
                    <td class="c10"><?= ($page_params['now_page'] - 1) * $page_params['list_rows'] + $k + 1 ?></td>
                    <td class="c10">
                    <?php
                    $signatory_role_name = '';
                    foreach ($group_arr as $g) {
                      if ($g['id'] == $v['dictionary_type_id']) {
                          $signatory_role_name = $g['name'];
                      }
                    }
                    echo $signatory_role_name;
                    ?>
                    </td>
                    <td class="c20">
                      <?php
                      if($v['dictionary_type_id'] == 1) {?>
                        <span style="width: 126px; height: 36px; background-position: 100% 100%; display:inline-block; vertical-align: middle;" class="banklogo banklogo-<?php echo $v[key];?>" title="<?php echo $v['key']; ?>"></span>
                      <?php } else {
                        echo $v['key'];
                      }
                      ?>
                    </td>
                    <td class="c20"><?php echo $v['name'] ?></td>
                    <td class="c10"><?php echo $v['name_abbr'] ?></td>
                    <td><?php echo $v['desc'] ?></td>
                    <td class="c10">
                      <a href="javascript:void(0);" onclick="modify_dictionary_pop(<?php echo $v['id']?>)">编辑</a>
                      <span style="margin:0 5px;color:#b2b2b2;">|</span>
                      <a href="javascript:void(0)" onclick="delete_dictionary(<?php echo $v['id'] ?>)">删除</a>
                    </td>
                  </tr>
                  <?php
                }
              } else { ?>
                <tr>
                  <td align="center" colspan='6'>暂无数据！</td>
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

<!--编辑门店资料弹窗-->
<div id="js_dictionary_type_edit_pop" class="iframePopBox" style="width:450px; height:390px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="auto" width="450px" height="390px" class='iframePop' src=""></iframe>
</div>

<!--添加类型-->
<div class="pop_box_g pop_box_add_shop" id="js_add_shop">
    <div class="hd">
        <div class="title">添加类型</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod" style="background:#fff;">
        <label class="label clearfix"><span class="text">类型名称：</span><input class="text_input" id="add_name" placeholder="类型的名称，如：银行" type="text"></label>
        <label class="label clearfix"><span class="text">名称缩写：</span><input class="text_input" id="add_name_abbr" placeholder="类型的缩写，如：银行" type="text"></label>
        <label class="label clearfix"><span class="text">类型描述：</span><input class="text_input" id="add_desc" placeholder="类型的描述,如：系统支持的银行" type="text"></label>
        <label class="label clearfix"><span class="text">父类型：</span>
          <select id="father_dictionary_type_id" aria-controls="dataTables-example" class="form-control input-sm" style="line-height:25px;height:25px;font-size:12px;border-radius:3px;">
            <option value="0">[类型] <?php echo $dictionary_type_name;?></option>
            <?php
            if (!empty($dictionary_type_oneleval_data)) {
              foreach ($dictionary_type_oneleval_data as $k => $v) {
                if ($k < count($dictionary_type_oneleval_data) - 1) {
                  ?>
                  <option value="<?php echo $v['id'] ?>">├─<?php echo $v['name'] ?></option>
                  <?php
                } else {
                  ?>
                  <option value="<?php echo $v['id'] ?>">└─<?php echo $v['name'] ?></option>
                  <?php
                }
              }
            }
            ?>
          </select>
          <span><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico2.png" title="系统支持父子类型的继承关系" /></span>
        </label>
        <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="add_dictionary_type()">保存</button>
    </div>
</div>

<script type="text/javascript">
  function dictionary_type_name_search(dictionary_type_name) {
    var dictionary_type_id_str = '';
    var _href = '';
    $.ajax({
      type: "GET",
      url: "/dictionary_type/get_dictionary_type_info_by_kw/",
      dataType: "json",
      data: {
        keyword: dictionary_type_name
      },
      cache: false,
      error: function () {
        $("#dialog_do_warnig_tip").html("系统错误");
        openWin('js_pop_do_warning');
        return false;
      },
      success: function (data) {
        var dictionary_type_html = '';
        for (i in data) {
          dictionary_type_id_str += data[i].id + ',';
          dictionary_type_html += '<li' + (data[i]['is_has_dictionary_type']!='1' ? ' class="nullMuen"' : '') + '>';
          dictionary_type_html += '<a title="编辑" onclick="modify_pop(' +  data[i].id + ')" href="javascript:void(0)" class="l-edit l-edit-2 modify">编辑</a>';
          dictionary_type_html += '<a href="/dictionary_type/index/' + data[i].id + '?view_type=search&search_type_keyword=' + encodeURIComponent(dictionary_type_name)+ '&search_dictionary_type_id=' + dictionary_type_id_str + '" style="font-weight:bold;" id="">' + data[i].name + '</a>';
          dictionary_type_html += '</li>';
        }
        if (0 == data[0].id) {
          $('#dictionary_type_list').html('');
          $('#search_result_num').html(0);
        } else {
          $('#dictionary_type_list').html(dictionary_type_html);
          $('#search_result_num').html(data.length);
        }
        $('.limit-search-text').show();
      }
    });
  }

  function dictionary_name_search(dictionary_type_id, dictionary_id, dictionary_name) {
    window.location.href = '/dictionary_type/index/' + dictionary_type_id + '/' + dictionary_id + '?view_type_sub=list&search_keyword=' + encodeURIComponent(dictionary_name)
    return false;
  }

  function checkalldictionary_type(obj) {
    var checkall = obj.checked ? 1 : 0;

    $(".dictionary_type_access_area").each(function () {
      checkall ? $(this).attr('checked', true) : $(this).attr('checked', false);
    });
  }
  function submit_dictionary_type_perm_form() {
    $.ajax({
      type: "POST",
      url: "/dictionary_type/set_dictionary_type_per/",
      dataType: "json",
      data: $("#dictionary_type_per_form").serialize(),
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

  //一级门店是否有效开关
  function is_effective_ajax_level_one(type, sub_dictionary_type_id) {
    var main_dictionary_type_id = <?php echo $dictionary_type_id;?>;
    var post_data = {
      'dictionary_type_level': '1',
      'type': type,
      'main_dictionary_type_id': main_dictionary_type_id,
      'sub_dictionary_type_id': sub_dictionary_type_id
    };
    $.ajax({
      type: "POST",
      url: "/dictionary_type/set_is_effective/",
      dataType: "json",
      data: post_data,
      cache: false,
      error: function () {
        $("#dialog_do_warnig_tip").html("系统错误");
        openWin('js_pop_do_warning');
        return false;
      }
    });
  }
  //二级门店是否有效开关
  function is_effective_ajax_level_two(type, sub_dictionary_type_id) {
    var main_dictionary_type_id = <?php echo $dictionary_type_id;?>;
    var post_data = {
      'dictionary_type_level': '2',
      'type': type,
      'main_dictionary_type_id': main_dictionary_type_id,
      'sub_dictionary_type_id': sub_dictionary_type_id
    };
    $.ajax({
      type: "POST",
      url: "/dictionary_type/set_is_effective/",
      dataType: "json",
      data: post_data,
      cache: false,
      error: function () {
        $("#dialog_do_warnig_tip").html("系统错误");
        openWin('js_pop_do_warning');
        return false;
      }
    });
  }
</script>

<!--修改类型-->
<div class="pop_box_g pop_box_add_shop" id="js_r_shop">
    <div class="hd">
        <div class="title">修改类型</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
      <label class="label clearfix"><span class="text">类型名称：</span><input class="text_input" id="modify_name" placeholder="类型的名称，如：银行" type="text"></label>
      <label class="label clearfix"><span class="text">名称缩写：</span><input class="text_input" id="modify_name_abbr" placeholder="类型的缩写，如：银行" type="text"></label>
      <label class="label clearfix"><span class="text">类型描述：</span><input class="text_input" id="modify_desc" placeholder="类型的描述,如：系统支持的银行" type="text"></label>
      <label class="label clearfix"><span class="text">父类型：</span>
        <select id="modify_father_dictionary_type_id" aria-controls="dataTables-example" class="form-control input-sm" style="line-height:25px;height:25px;font-size:12px;border-radius:3px;">
        </select>
        <span><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico2.png" title="系统支持父子类型的继承关系" /></span>
      </label>

          <input type="hidden" value="" id="dictionary_type_id">
          <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="modify_dictionary_type()">保存</button>
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
                <button type="button" class="btn-lv1 btn-mid" onclick="location.href='/dictionary_type/index/'">确定</button>
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
				<button type="button" class="btn-lv1 btn-mid" onclick="location.href='/dictionary_type/index/<?php echo $now_dictionary_type_id; ?>'">确定</button>
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
<!--添加数据字典-->
<div class="pop_box_g pop_box_add_shop" id="js_add_dictionary">
    <div class="hd">
        <div class="title">添加数据</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod" style="background:#fff;">
		  <label class="label clearfix"><span class="text">数据类型：</span>
        <select id="dictionary_dictionary_type_id" style="margin-top:4px;">
        <?php
        if(is_array($dictionary_type_info_dictionary) && !empty($dictionary_type_info_dictionary)){
            foreach (array_reverse($dictionary_type_info_dictionary) as $k =>$v) {
                if($k < count($dictionary_type_info_dictionary)-1){
        ?>
            <option value="<?=$v['id'] ?>">├─<?=$v['name']?></option>
            <?php if(isset($v['next_dictionary_type_data']) && !empty($v['next_dictionary_type_data'])){
                foreach($v['next_dictionary_type_data'] as $key => $value){
            ?>
                <option value="<?=$value['id'] ?>">　├─<?=$value['name']?></option>
            <?php
                }
            }?>
        <?php
                }else{
        ?>
            <option value="<?=$v['id'] ?>">└─<?=$v['name']?></option>
            <?php if(isset($v['next_dictionary_type_data']) && !empty($v['next_dictionary_type_data'])){
                foreach($v['next_dictionary_type_data'] as $key => $value){
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
      <label class="label clearfix"><span class="text">键/Key：</span><input class="text_input" type="text" placeholder="键，如：ICBC" id="key"></label>
      <label class="label clearfix"><span class="text">值/Value：</span><input class="text_input" type="text" placeholder="值，如：工商银行" id="name"></label>
      <label class="label clearfix"><span class="text">缩写/Abbr：</span><input class="text_input" type="text" placeholder="缩写，如：工行" id="name_abbr"></label>
      <label class="label clearfix"><span class="text">描述：</span><input class="text_input" type="text" placeholder="描述，如：中国工商银行"  id="desc"></label>
      <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="add_dictionary()">保存</button>
    </div>
</div>

<!--修改数据-->
<div class="pop_box_g pop_box_add_shop" id="js_modify_dictionary">
  <div class="hd">
    <div class="title">修改数据</div>
    <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
  </div>
  <div class="mod">
    <label class="label clearfix"><span class="text">数据类型：</span>
      <select id="modify_dictionary_dictionary_type_id" style="margin-top:4px;">
        <?php
        if(is_array($dictionary_type_info_dictionary) && !empty($dictionary_type_info_dictionary)){
          foreach (array_reverse($dictionary_type_info_dictionary) as $k =>$v) {
            if($k < count($dictionary_type_info_dictionary)-1){
              ?>
              <option value="<?=$v['id'] ?>">├─<?=$v['name']?></option>
              <?php if(isset($v['next_dictionary_type_data']) && !empty($v['next_dictionary_type_data'])){
                foreach($v['next_dictionary_type_data'] as $key => $value){
                  ?>
                  <option value="<?=$value['id'] ?>">　├─<?=$value['name']?></option>
                  <?php
                }
              }?>
              <?php
            }else{
              ?>
              <option value="<?=$v['id'] ?>">└─<?=$v['name']?></option>
              <?php if(isset($v['next_dictionary_type_data']) && !empty($v['next_dictionary_type_data'])){
                foreach($v['next_dictionary_type_data'] as $key => $value){
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
    <label class="label clearfix"><span class="text">键/Key：</span><input class="text_input" type="text" placeholder="键，如：ICBC" id="modify_dictionary_key"></label>
    <label class="label clearfix"><span class="text">值/Value：</span><input class="text_input" type="text" placeholder="值，如：工商银行" id="modify_dictionary_name"></label>
    <label class="label clearfix"><span class="text">缩写/Abbr：</span><input class="text_input" type="text" placeholder="缩写，如：工行" id="modify_dictionary_name_abbr"></label>
    <label class="label clearfix"><span class="text">描述：</span><input class="text_input" type="text" placeholder="描述，如：中国工商银行"  id="modify_dictionary_desc"></label>

    <input type="hidden" value="" id="dictionary_id">
    <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="modify_dictionary()">保存</button>
  </div>
</div>

<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_delete_dictionary2">
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

<!--删除字典类型提示框-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_cancel_dictionary_type">
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
                            <p class="left" style="font-size:14px;color:#666;">您确定要删除吗?</p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left" type="button" onclick="checkdel('',1);">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
                <input type="hidden" value="" id="cancel_dictionary_type">
            </div>
        </div>
    </div>
</div>

<script>
  //添加区属板块触发
  $(function () {
    $('#add_district').change(function () {
      var districtID = $(this).val();
      $.ajax({
        type: 'get',
        url: '/community/find_street_bydis/' + districtID,
        dataType: 'json',
        success: function (msg) {
          var str = '';
          if (msg.result == 'no result') {
            str = '<option value="">请选择</option>';
          } else {
            str = '<option value="">请选择</option>';
            for (var i = 0; i < msg.length; i++) {
              str += '<option value="' + msg[i].id + '">' + msg[i].streetname + '</option>';
            }
          }
          $('#add_street').empty();
          $('#add_street').append(str);
        }
      });
    });
  });

  //修改区属板块触发
  $(function () {
    $('#modify_district').change(function () {
      var districtID = $(this).val();
      $.ajax({
        type: 'get',
        url: '/community/find_street_bydis/' + districtID,
        dataType: 'json',
        success: function (msg) {
          var str = '';
          if (msg.result == 'no result') {
            str = '<option value="">请选择</option>';
          } else {
            str = '<option value="">请选择</option>';
            for (var i = 0; i < msg.length; i++) {
              str += '<option value="' + msg[i].id + '">' + msg[i].streetname + '</option>';
            }
          }
          $('#modify_street').empty();
          $('#modify_street').append(str);
        }
      });
    });
  });


  $(function () {
    function re_width() {
      var h1 = $(window).height();
      var w1 = $(window).width() - 280;
      $("#js_left").height(h1 - 50);
//      $("#limit-table").height(h1 - 180);
      $(".limit-set2 .limit-left2-wrapper").height(h1-136);
      $(".limit-right2").width(w1).show();
    };
    re_width();
    $(window).resize(function (e) {
      re_width();
//      setTimeout(function () {
//        $("#js_inner").css("height", ($("#js_inner").height() - 30) + "px");
//      }, 20)

    });

  });
  window.onload = function () {
//    $("#js_inner").css("height", ($("#js_inner").height() - 30) + "px");
  }

  //类型添加
  function add_dictionary_type() {
    var name = $("#add_name").val();
    var name_abbr = $("#add_name_abbr").val();
    var desc = $("#add_desc").val();
    var father_dictionary_type_id = $("#father_dictionary_type_id").val();

    if (!name) {
      $("#dialog_do_warnig_tip").html('请输入类型名称');
      openWin('js_pop_do_warning');
      return false;
    }
    if (!name_abbr) {
      $("#dialog_do_warnig_tip").html('请输入类型缩写');
      openWin('js_pop_do_warning');
      return false;
    }
    if (!desc) {
      $("#dialog_do_warnig_tip").html('请输入类型描述');
      openWin('js_pop_do_warning');
      return false;
    }
    var data = {
      name: name,
      name_abbr: name_abbr,
      desc: desc,
      father_dictionary_type_id: father_dictionary_type_id
    };
    $.ajax({
      type: "POST",
      url: "/dictionary_type/add",
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

  //类型对应id获取
  function modify_pop(id) {
    $.ajax({
      type: "POST",
      url: "/dictionary_type/edit",
      data: "id=" + id,
      dataType: "json",
      cache: false,
      error: function () {
        alert("系统错误");
        return false;
      },
      success: function (data) {
        openWin("js_r_shop");
      }
    });
  }

  //类型修改
  function modify_dictionary_type() {
    var dictionary_type_id = $("#dictionary_type_id").val();
    var name = $("#modify_name").val();
    var name_abbr = $("#modify_name_abbr").val();
    var desc = $("#modify_desc").val();
    var modify_father_dictionary_type_id = $("#modify_father_dictionary_type_id").val();
    if (!name) {
      $("#dialog_do_warnig_tip").html('请输入类型名称');
      openWin('js_pop_do_warning');
      return false;
    }
    if (!name_abbr) {
      $("#dialog_do_warnig_tip").html('请输入类型缩写');
      openWin('js_pop_do_warning');
      return false;
    }
    if (!desc) {
      $("#dialog_do_warnig_tip").html('请输入类型描述');
      openWin('js_pop_do_warning');
      return false;
    }
    var data = {
      dictionary_type_id: dictionary_type_id,
      name: name,
      name_abbr: name_abbr,
      desc: desc,
      modify_father_dictionary_type_id: modify_father_dictionary_type_id
    };
    $.ajax({
      type: "POST",
      url: "/dictionary_type/modify",
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
          closeWindowWin('js_r_shop');
          $("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
          openWin('js_pop_do_warning');
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
  //类型那个删除
  function checkdel(id, sure) {
      if (sure == 1) {
          var dictionary_type_id = $('cancel_dictionary_type').val();
          var data = {dictionary_type_id: dictionary_type_id};
      $.ajax({
        type: "POST",
        url: "/dictionary_type/delete",
        dataType: "json",
        data: data,
        error: function () {
          $("#dialog_do_warnig_tip").html("系统错误");
          openWin('js_pop_do_warning');
          return false;
        },
        success: function (data) {
            closeWindowWin('js_cancel_dictionary_type');
          $("#dialog_do_result_tip").html(data.msg);
          openWin('js_del_do_result');
        }
      });
    } else {
          $('cancel_dictionary_type').val(id)
          openWin('js_cancel_dictionary_type');
    }
  }

  //获取验证码
  var InterValObj; //timer变量，控制时间
  var count = 60; //间隔函数，1秒执行
  var curCount;//当前剩余秒数

  function get_code() {
    curCount = count;
    var phone = $("#phone").val();
    var partten = /^1\d{10}$/;
    if (!phone || !partten.test(phone)) {
      $("#dialog_do_warnig_tip").html("请输入正确的11位手机号码");
      openWin('js_pop_do_warning');
      return false;
    }
    $.ajax({
      type: 'get',
      url: '/signatory_sms/',
      data: {phone: phone, type: 'register'},
      dataType: 'json',
      cache: false,
      error: function (resp) {
        $("#dialog_do_warnig_tip").html("系统错误");
        openWin('js_pop_do_warning');
        return false;
      },
      success: function (data) {
        if (data.status == 1) {
          $(".get_code").attr("disabled", "true");
          InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
        } else {
          $("#dialog_do_warnig_tip").html(data.msg);
          openWin('js_pop_do_warning');
          return false;
        }
      }
    });
  }

  //timer处理函数
  function SetRemainTime() {
    if (curCount == 0) {
      window.clearInterval(InterValObj);//停止计时器
      $(".get_code").removeAttr("disabled");//启用按钮
      $(".get_code").val("重新获取验证码");
    }
    else {
      $(".get_code").val(curCount + "s");
      curCount--;
    }
  }
  //添加经纪人
  function add_dictionary() {
    var dictionary_type_id = $("#dictionary_dictionary_type_id").val();
    var key = $("#key").val();
    var name = $("#name").val();
    var name_abbr = $("#name_abbr").val();
    var desc = $("#desc").val();

    if (!key) {
      $("#dialog_do_warnig_tip").html("请输入键/Key");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!name) {
      $("#dialog_do_warnig_tip").html("请输入值/Value");
      openWin('js_pop_do_warning');
      return false;
    }

    if (!name_abbr) {
      $("#dialog_do_warnig_tip").html("请输入值缩写/Value");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!desc) {
      $("#dialog_do_warnig_tip").html("请输入描述");
      openWin('js_pop_do_warning');
      return false;
    }
    var data = {
      dictionary_type_id: dictionary_type_id,
      key: key,
      name: name,
      name_abbr: name_abbr,
      desc: desc
    };
    $.ajax({
      type: "POST",
      url: "/dictionary_type/add_dictionary",
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

  // 修改数据弹框
  function modify_dictionary_pop(dictionary_id) {
    $.ajax({
      type: "POST",
      url: "/dictionary_type/modify_dictionary_pop",
      data: "dictionary_id=" + dictionary_id,
      cache: false,
      dataType: 'json',
      error: function () {
        alert("系统错误");
        return false;
      },
      success: function (data) {
        if (data) {
          $('#dictionary_id').val(data.id);
          $('#modify_dictionary_key').val(data.key);
          $('#modify_dictionary_name').val(data.name);
          $('#modify_dictionary_name_abbr').val(data.name_abbr);
          $('#modify_dictionary_desc').val(data.desc);
          $('#modify_dictionary_dictionary_type_id').val(data.dictionary_type_id);
          openWin('js_modify_dictionary');
        } else {
          openWin('js_modify_dictionary');
        }
      }
    });
  }
  //修改数据
  function modify_dictionary() {
    var dictionary_id = $("#dictionary_id").val();
    var key = $('#modify_dictionary_key').val();
    var name = $('#modify_dictionary_name').val();
    var name_abbr = $('#modify_dictionary_name_abbr').val();
    var desc = $('#modify_dictionary_desc').val();
    var dictionary_type_id = $('#modify_dictionary_dictionary_type_id').val();

    if (!key) {
      $("#dialog_do_warnig_tip").html("请输入键/Key");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!name) {
      $("#dialog_do_warnig_tip").html("请输入值/Value");
      openWin('js_pop_do_warning');
      return false;
    }

    if (!name_abbr) {
      $("#dialog_do_warnig_tip").html("请输入值缩写/Value");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!desc) {
      $("#dialog_do_warnig_tip").html("请输入描述");
      openWin('js_pop_do_warning');
      return false;
    }
    var data = {
      dictionary_id: dictionary_id,
      dictionary_type_id: dictionary_type_id,
      key: key,
      name: name,
      name_abbr: name_abbr,
      desc: desc
    };
    $.ajax({
      type: "POST",
      url: "/dictionary_type/modify_dictionary",
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
          closeWindowWin('js_r_shop');
          $("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
          openWin('js_pop_do_warning');
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

  //删除数据
  function delete_dictionary(id) {
    if (confirm('确定要删除此类型？')) {
      var data = {dictionary_id: id};
      $.ajax({
        type: "POST",
        url: "/dictionary_type/delete_dictionary",
        dataType:"json",
        data: data,
        cache: false,
        error: function () {
          $("#dialog_do_warnig_tip").html("系统错误");
          openWin('js_pop_do_warning');
          return false;
        },
        success: function (data) {
            console.log(1234567)
          $("#dialog_do_result_tip").html(data.msg);
          openWin('js_del_do_result');
        }
      });
    } else {
      return false;
    }
  }

  jQuery.fn.limit = function () {
    var self = $(this);
    self.each(function () {
      var objString = $(this).text();
      var objLength = $(this).text().length;
      var num = $(this).attr("limit");
      if (objLength > num) {
        $(this).attr("title", objString);
        objString = $(this).text(objString.substring(0, num) + "...");
      }
    })
  }
</script>
