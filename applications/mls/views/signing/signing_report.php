<script>
    window.parent.addNavClass(26);
</script>
<div class="contract-wrap clearfix">
   <!--left 菜单部分-->
    <div class="tab-left"><?=$user_tree_menu?></div>
    <div class="forms_scroll2">
	    <div class="shop_tab_title scr_clear" id="js_search_box">
            <a href="javascript:void(0);" class="btn-lv fr" <?php if($auth['add']['auth']){?>onclick="$('#js_modify_box .iframePop').attr('src','/signing/modify_report_index/<?=$type?>');openWin('js_modify_box');"<?php }else{?>onclick="permission_none();"<?php }?>><span>+ 新增预约</span></a>
		<a href="/signing/report/0" class="link <?=$type==0?'link_on':''?>"><span class="iconfont hide"></span>买卖</a>
<!--		<a href="/signing/report/2" class="link <?/*=$type==2?'link_on':''*/?>"><span class="iconfont hide"></span>出租</a>-->
	    </div>
	    <!-- 上部菜单选项，按钮-->
      <div class="fl">
	    <div class="search_box clearfix" id="js_search_box_02">
		<form name="search_form" id="subform" method="post" action="">
		    <div class="fg_box">
			<p class="fg fg_tex">预约编号：</p>
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
			<p class="fg fg_tex">门店：</p>
			<div class="fg mr10" style="*padding-top:10px;">
			    <select class="select w80" name="agency_id_a" id="sign_agency" style="width:14em;">
				<?php foreach($agencys as $key =>$val){?>
				<option value="<?=$val['id'];?>" <?=$post_param['agency_id_a']==$val['id']?'selected':'';?>><?=$val['name'];?></option>
				<?php }?>
			    </select>
			</div>
		    </div>
		    <div class="fg_box">
			<p class="fg fg_tex">预约人：</p>

			<div class="fg mr10" style="*padding-top:10px;">
			    <select class="select w80" name="broker_id_a" value="<?=$post_param['broker_id'];?>" id="sign_broker">
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
                <p class="fg fg_tex">预约时间：</p>
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
			    <select class="select w80" name="status">
				<option value=''>请选择</option>
				<?php foreach($config['report_status'] as $key =>$val){?>
				<option value="<?=$key;?>" <?=isset($post_param['status']) && $post_param['status']==$key?"selected":"";?>><?=$val;?></option>
				<?php }?>
			    </select>
			</div>
		    </div>
		    <div class="fg_box">
			<input type="hidden" name="page" value="1">
			<input type="hidden" name="is_submit" value="1">
			<div class="fg"> <a href="javascript:void(0);" onclick="$('#subform :input[name=page]').val('1');$('#subform').attr('action', '/signing/report/<?=$type?>/');form_submit();return false;" class="btn"><span class="btn_inner">搜索</span></a> </div>
<!--			<div class="fg"> <a href="javascript:void(0);" onclick="$('#subform').attr('action', '/signing/exportReport/<?/*=$type*/?>/');form_submit();$('#subform').attr('action', '');return false;" class="btn"><span class="btn_inner">导出</span></a> </div>-->
			<div class="fg"> <a href="/signing/report/<?=$type?>" class="reset">重置</a> </div>
		    </div>
		</form>
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
<script>
  $(function () {
    function re_width2(){//有表格的时候
      var h1 = $(window).height();
      var w1 = $(window).width() - 170;
      $(".tab-left").height(h1);
      $(".forms_scroll2").width(w1);
      $("#js_inner").height(h1 - 170);
    };
    re_width2();
    $(window).resize(function(e) {
      re_width2();
    });
    $('.table_all .inner tr').find("a").click(function(event){
      event.stopPropagation();
    });
  });

  //通过参数判断是否可以被提交
  function form_submit(){
    var is_submit = $("input[name='is_submit']").val();
    if(is_submit ==1){
      $('#subform').submit();
    }
  }

  function open_house(){
    var house_id = childiframe.window.document.getElementById('contract_houseid_add').value;
    $('#js_house_box .iframePop').attr('src','/signing/get_house/<?=$type;?>/'+house_id);
    openWin('js_house_box');
  }

  //获取信息，打开修改界面
  function edit_report(id){
    $('#js_pop_edit').find('.errorBox').html('');
    $("#js_pop_edit").find('input').val('');
    $("#js_pop_edit").find('textarea').val('');
    $('#contract_id').val(id);
    $.ajax({
      url:"/signing/edit",
      type:"GET",
      dataType:"json",
      data:{
        id:id,
        type:<?=$type?>
      },
      success:function(data){
        if(data['result'] == 1){
          $("select[name='contract_type_edit']").val(data['arr']['type']);
          $("input[name='contract_number_edit']").val(data['arr']['number']);
          $("input[name='contract_houseid_edit']").val(data['arr']['house_id']);
          $("input[name='contract_blockname_edit']").val(data['arr']['block_name']);
          $("input[name='contract_blockid_edit']").val(data['arr']['block_id']);
          $("input[name='contract_addr_edit']").val(data['arr']['house_addr']);
          $("select[name='contract_paytype_edit']").val(data['arr']['pay_type']);
          $("input[name='contract_time_edit']").val(data['arr']['signing_time']);
          $("select[name='contract_agency_edit']").val(data['arr']['agency_id_a']);
          $("input[name='contract_broker_tel']").val(data['arr']['broker_tel_a']);
          $("textarea[name='contract_remark_edit']").val(data['arr']['remarks']);
          var html = "";
          for(var i in data['broker_list']){
            html +='<option value="'+data['broker_list'][i]['broker_id']+'">'+data['broker_list'][i]['truename']+'</option>';
          }
          $("#contract_broker_edit").html(html);
          $("select[name='contract_broker_edit']").val(data['arr']['broker_id_a']);
          $("input[name='contract_remark_edit']").val(data['arr']['remarks']);
          $('#report_edit_form').find('input').attr('disabled',true);
          $('#report_edit_form').find('select').attr('disabled',true);
          $('#report_edit_form').find('textarea').attr('disabled',true);
          openWin('js_pop_detail');
        }
      }
    })
  }
  //删除该条合同
  function delete_this(){
    var contract_id = $('#contract_id').val();
    $.ajax({
      url:"/signing/cancel_report",
      type:"GET",
      dataType:"json",
      data:{
        id:contract_id
      },
      success:function(data){
        if(data['result'] == 1){
          $('#js_prompt1').text('预约已取消！');
          openWin('js_pop_success');
        }else{
          $('#js_prompt2').text('取消预约失败！');
          openWin('js_pop_false');
        }
      }
    })
  }
  //修改预约
  function modify_this(contract_id) {
      $.ajax({
          url: "/signing/modify_report_check",
          type: "GET",
          dataType: "json",
          data: {
              id: contract_id,
          },
          success: function (data) {
              if (data['has_other_report'] == 1) {
                  $('#js_prompt2').text('已有他人预约该房源！');
                  openWin('js_pop_false');
              } else {
                  $('#js_modify_box .iframePop').attr('src', '/signing/modify_report_index/<?=$type?>/' + contract_id);
                  openWin('js_modify_box');
              }
          }
      })

  }
  function update_status(){
    var contract_id = $('#contract_id').val();
    $.ajax({
      url:"/signing/update_report_status",
      type:"GET",
      dataType:"json",
      data:{
        id:contract_id
      },
      success:function(data){
        if(data['result'] == 1){
          $("#number").text(data['number']);
          openWin('js_pop_report_sussess');
        }else{
          $('#js_prompt2').text('预约状态修改失败！');
          openWin('js_pop_false');
        }
      }
    })
  }

  //操作成功之后刷新当前页，如果没有数据，返回上一页
  function check_list(page,type){
    $.post(
      '/signing/check_list',
      {'page':page,
        'type':type},
      function(data){
        if(data == '0'){
          if(page >1){
            page = page-1;
          }
        }
        $('#search_form :input[name=page]').val(page);form_submit();return false;
      }
    );
  }

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

  function get_info(id){
    closeWindowWin('js_house_box');
    if(id){
      $.post(
        '/signing/get_info',
        {'id':id,
          'type':<?=$type;?>
        },
        function(data){
            if (data['has_appointment'] == 1) {
                openWin('js_pop_do_has_appointment');
                return false;
            }
            if (data['belong_district']) {
                openWin('js_pop_do_success');
                childiframe.window.document.getElementById('contract_blockid_add').value = data['district_house']['block_id'];
                childiframe.window.document.getElementById('contract_blockname_add').value = data['district_house']['block_name'];
                childiframe.window.document.getElementById('contract_addr_add').value = data['district_house']['address'] + data['district_house']['dong'] + '栋' + data['district_house']['unit'] + '单元' + data['district_house']['door'] + '室';
                childiframe.window.document.getElementById('contract_houseid_add').value = data['district_house']['house_id'];
                childiframe.window.document.getElementById('contract_blockname_add').disabled = true;
                childiframe.window.document.getElementById('contract_addr_add').disabled = true;
                childiframe.window.document.getElementById('contract_houseid_add').disabled = true;
                return false;
            } else {
                childiframe.window.document.getElementById('contract_blockid_add').value = data['block_id'];
                childiframe.window.document.getElementById('contract_blockname_add').value = data['block_name'];
                childiframe.window.document.getElementById('contract_addr_add').value = data['address'] + data['dong'] + '栋' + data['unit'] + '单元' + data['door'] + '室';
                childiframe.window.document.getElementById('contract_houseid_add').value = data['house_id'];
                childiframe.window.document.getElementById('contract_blockname_add').disabled = true;
                childiframe.window.document.getElementById('contract_addr_add').disabled = true;
                childiframe.window.document.getElementById('contract_houseid_add').disabled = true;
                return false;
            }

        },'json'
      );
    }else{
      childiframe.window.document.getElementById('contract_blockid_add').value ='';
      childiframe.window.document.getElementById('contract_blockname_add').value = '';
      childiframe.window.document.getElementById('contract_addr_add').value = '';
      childiframe.window.document.getElementById('contract_houseid_add').value = '';
      childiframe.window.document.getElementById('contract_blockname_add').disabled = false;
      childiframe.window.document.getElementById('contract_addr_add').disabled = false;
      childiframe.window.document.getElementById('contract_houseid_add').disabled = false;
        return false;
    }
  }

</script>
	    <!-- 上部菜单选项，按钮---end-->
	    <div class="table_all">
		<div class="title shop_title" id="js_title">
		    <table class="table">
			<tr>
			    <td class="c9" style="width:10%;">预约编号</td>
			    <td class="c9" style="width:10%;">房源编号</td>
			    <td class="c20">房源地址</td>
			    <td class="c8" style="width:10%;">预约时间</td>
			    <td class="c15">门店</td>
			    <td class="c8">预约人</td>
			    <td class="c8">状态</td>
			    <td>操作</td>
			</tr>
		    </table>
		</div>
		<div class="inner shop_inner" id="js_inner">
		    <table class="table" style="*+width:98.5%;_width:98.5%;">
			<?php if(is_full_array($list)){foreach($list as $key=>$val){?>
			<tr onclick="edit_report(<?=$val['id']?>);">
			    <td class="c9"  style="width:10%;"><div class="info c227ac6"><?=$val['number'];?></div></td>
                <td class="c9" style="width:10%;">
                    <div class="info c227ac6" onclick=""><a href="javascript:void(0);"
                                                            onclick="$('#js_pop_box .iframePop').attr('src','/<?php echo $type !== 2 ? 'sell' : 'rent'; ?>/details_house/<?= substr($val['house_id'], 2); ?>/4');openWin('js_pop_box');"><?= $val['house_id']; ?></a>
                    </div>
                </td>
			    <td class="c20"><div class="info"><?=$val['house_addr'];?></div></td>
			    <td class="c8"  style="width:10%;"><div class="info"><?=date('Y-m-d H:i:s',$val['signing_time']);?></div></td>
			    <td class="c15"><div class="info"><?=$val['agency_name_a'];?></div></td>
			    <td class="c8"><div class="info"><?=$val['broker_name_a'];?></div></td>
			    <td class="c8">
            <?php if($val['status'] == 1){?>
              <div class="info f60"><?=$config['report_status'][$val['status']];?></div></td>
            <?php }else if($val['status'] == 2){?>
              <div class="info c1ab273"><?=$config['report_status'][$val['status']];?></div></td>
            <?php }else if($val['status'] == 3){?>
              <div class="info cf90"><?=$config['report_status'][$val['status']];?></div></td>
            <?php }else if($val['status'] == 4){?>
              <div class="info c227ac6"><?=$config['report_status'][$val['status']];?></div></td>
            <?php }else if($val['status'] == 5){?>
              <div class="info f00"><?=$config['report_status'][$val['status']];?></div></td>
            <?php }else if($val['status'] == 6){?>
              <div class="info c999"><?=$config['report_status'][$val['status']];?></div></td>
            <?php }else{?>
              <div class="info"><?=$config['report_status'][$val['status']];?></div></td>
            <?php }?>
			    <td>
                    <!--				--><?php //if(($val['status'] == 1) && $val['is_check'] =='0'){?>
                    <a href="javascript:void(0)"
                       <?php if ($auth['edit']['auth']){ ?>onclick="modify_this(<?= $val['id']; ?>);"
                       <?php }else{ ?>onclick="permission_none();"<?php } ?>>修改预约</a>
                    <!--				--><?php //}else{?>
                    <!--				<span style="color:#b2b2b2;">修改预约</span>-->
                    <!--				--><?php //}?>
				<span style="margin:0 5px;color:#b2b2b2;">|</span>
                    <!--        --><?php //if(($val['status'] == 1 || $val['status'] == 2) && $val['is_check'] =='0'){?>
          <a href="javascript:void(0)" <?php if($auth['delete']['auth']){?>onclick="$('#contract_id').val(<?=$val['id'];?>);openWin('js_pop_del');"<?php }else{?>onclick="permission_none();"<?php }?>>取消预约</a>
                    <!--        --><?php //}else{?>
                    <!--          <span style="color:#b2b2b2;">取消预约</span>-->
                    <!--        --><?php //}?>
            <input type="hidden" id="contract_id" value="">
        </td>
			</tr>
			<?php }}else{?>
			<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
			<?php }?>
		    </table>
		</div>
	    </div>
	    <div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
            <div class="get_page">
                <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
            </div>
	    </div>
    </div>
</div>


<!--预约详情弹窗-->
<div class="pop_box_g" id="js_pop_detail" style="width:580px; height:414px; display: none;overflow:hidden;">
    <div class="hd header">
        <div class="title">预约详情</div>
	<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="reclaim-mod" style="height:328px;float:left;display:inline; overflow-y:auto; overflow-x:hidden;">
        <form action="" method="post" id="report_edit_form">
            <table>
		<tr>
		    <td width="70" class="label"><b class="resut_table_state_1 zws_em ">*</b>交易方式：</td>
                    <td width="120">
                        <select class="select" style="width:100px;" name="contract_type_edit" disabled>
                            <option value="1" <?=$type==1?'selected':'';?>>出售</option>
<!--                            <option value="2" --><?//=$type==2?'selected':'';?><!-->出租</option>-->
                        </select>
                        <div class="errorBox"></div>
                    </td>
		    <td width="70"  class="label"><b class="resut_table_state_1 zws_em ">*</b>预约编号：</td>
                    <td><input class="input_text w90" style="width:254px;" type="text" size="14" name="contract_number_edit" autocomplete="off" disabled><div class="errorBox"></div></td>
		    </tr>
                <tr>
		    <td width="70" class="label">房源编号：</td>
                    <td width="120"><input class="input_text w90" style="width:88px;" type="text" size="14" name="contract_houseid_edit" autocomplete="off" disabled></td>
		    <td width="70"  class="label">楼盘名称：</td>
                    <td>
			<input class="input_text w90" style="width:254px;" type="text" size="14" name="contract_blockname_edit" autocomplete="off" disabled>
			<input type="hidden" name="contract_blockid_edit">
		    </td>
                </tr>
              <tr>
                <td width="70" class="label"> 房源地址：</td>
                <td colspan="3">
                  <input class="input_text w248" style="width:450px;" type="text" size="14" name="contract_addr_edit" autocomplete="off" disabled="">
                </td>
              </tr>
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
			    $("input[name='contract_blockname_edit']").autocomplete({
				    source: function( request, response ) {
					var term = request.term;
					$("input[name='contract_blockid_edit']").val("");
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
						$("input[name='contract_blockid_edit']").val(id);
						$("input[name='contract_blockname_edit']").val(blockname);
						removeinput = 2;
					    }else{
						removeinput = 1;
					    }
				    },
				    close: function(event) {
					    if(typeof(removeinput)=='undefined' || removeinput == 1){
						$("input[name='contract_blockname_edit']").val("");
						$("input[name='contract_blockid_edit']").val("");
					    }
				    }
			    });
		    });
		</script>
	    </table>

            <table style="width:100%;">
                <tr>
		    		<td width="70" class="label" style="width:70px;"><b class="resut_table_state_1 zws_em ">*</b>预约时间：</td>
                    <td width="100">
	                    <input type="text" size="14" class="input_text time_bg" name="contract_time_edit" autocomplete="off" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH'})" disabled>
	                    <div class="errorBox"></div>
                    </td>
                  <td width="70"  class="label"  style="width:70px;">
                  </td>
                  <td width="272"></td>
                  </tr>
            </table>
          <table style="width:100%;">
            <tr>
              <td width="70"  class="label"  style="width:70px;">
                <b class="resut_table_state_1 zws_em ">*</b>预约人：
              </td>
              <td width="62">
                <select style="width:100px;" class="select mr10" name="contract_agency_edit" id="contract_agency_edit" disabled>
                  <?php foreach($agencys as $key =>$val){?>
                    <option value="<?=$val['id'];?>"><?=$val['name'];?></option>
                  <?php }?>
                </select>
                <div class="errorBox"></div>
              </td>
              <td width="40"  class="label">
                <select class="select" name="contract_broker_edit" id="contract_broker_edit" disabled>
                  <option value="1">请选择人员</option>
                </select>
                <div class="errorBox"></div>
              </td>
              <td width="150">
                <span class="input_add_F" style="padding:0; width:110px;display:inline;">
                  <input class="input_text w90" type="text" size="14" name="contract_broker_tel" autocomplete="off" disabled><div class="errorBox"></div>
                </span>
              </td>
            </tr>
		<script>
		    $("#contract_agency_edit").change(function(){
			var agency_id = $('#contract_agency_edit').val();
			if(agency_id){
			    $.ajax({
				url:"/signing/broker_list",
				type:"GET",
				dataType:"json",
				data:{
				   agency_id:$('#contract_agency_edit').val()
				},
				success:function(data){
				    var html = "<option value=''>请选择</option>";
                    if(data['result'] == 1){
                        for(var i in data['list']){
                            html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
                        }
                        $('#contract_broker_edit').html(html);
                    }
                    $('#contract_broker_edit').html(html);
				}
			    })
			}else{
			    $('#contract_broker_edit').html("<option value=''>请选择</option>");
			}
		    })
		</script>
                <tr>
                    <td class="label">备注：</td>
                    <td colspan="3"><textarea  class="textarea" id="contract_remark_edit" name="contract_remark_edit" disabled></textarea></td>
                </tr>
                <tr>
		    <td colspan="4" class="center">
<!--			<button  type="button" class="btn-lv1 btn-left JS_Close zws_btn_bg_w93" onclick="openWin('js_pop_report');">转为正式合同</button>-->

			<button type="button" class="btn-hui1 JS_Close">取消</button>
		    </td>
                </tr>
            </table>
        </form>
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
	    <div class="up_inner" style="width:76%;padding-left:15%;">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			                    <p class="left" style="font-size:14px;color:#666;" id="js_prompt1"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button" onclick="window.location.reload(true)">确定</button>
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
			    <p class="left" style="font-size:14px;color:#666;"  id="js_prompt2">预约添加成功！</p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>

<!--删除提示框-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_del">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td class="c14" valign="top"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
                        <td>
			    <p class="left" style="color:#666;">预约取消后不可修改,是否确认取消？</p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left JS_Close" type="button" onclick="delete_this();">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
         </div>
    </div>
</div>

<!--预约转正提示框-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_report">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop" style="padding-left:47px;width:auto;">
                    <tr>
                        <td class="c14" valign="top"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
                        <td>
			    			<p class="left" style="color:#666;">是否确定转为正式合同？</p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left JS_Close" type="button" onclick="update_status();">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
         </div>
    </div>
</div>

<!--预约转正成功提示框-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_report_sussess">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont" onclick="check_list(<?=$page?>,<?=$type?>)"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td class="c14" valign="top"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
                        <td>
                            <p class="left" style="color:#666;">合同<span id="number"></span>已转为正式合同！</p>
                            <p class="left" style="color:#666;width:100%;">是否去添加合同信息？</p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left JS_Close" type="button" onclick="location.href='/signing/modify_contract/<?=$type;?>/'+$('#contract_id').val();return false;">去添加</button>
                <button class="btn-hui1 JS_Close" type="button" onclick="check_list(<?=$page?>,<?=$type?>)">取消</button>
            </div>
         </div>
    </div>
</div>

<!--房源选择弹框-->
<div id="js_house_box" class="iframePopBox" style="width: 980px;height:575px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="980" height="575px" class='iframePop' src=""></iframe>
</div>

<!--添加和修改弹框-->
<div id="js_modify_box" class="iframePopBox" style="width: 580px;height:414px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="580" height="414px" class='iframePop' src="" name="childiframe"></iframe>
</div>

<!--详情弹跳页-->
<div id="js_pop_box" class="iframePopBox" style="width:816px; height:540px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id='dialog_do_itp'>区域公盘已有该房源，将以区域公盘内房源预约</p>
                <button type="button" id="dialog_btn" class="btn-lv1 btn-left JS_Close">确定</button>
            </div>
        </div>
    </div>
</div>
<!--重复预约弹出提示框-->
<div id="js_pop_do_has_appointment" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id='dialog_has_appointment_itp'>该房源已预约，请勿重复预约</p>
                <button type="button" id="dialog_has_appointment_btn" class="btn-lv1 btn-left JS_Close">确定</button>
            </div>
        </div>
    </div>
</div>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->

