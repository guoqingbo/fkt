<!--预约详情弹窗-->
<div class="pop_box_g" id="js_pop_detail" style="width:580px; height:422px; display: block;overflow:hidden;border:none;">
    <div class="hd header">
        <div class="title"><?=$report_id?"编辑":"新增"?>预约</div>
    </div>
    <div class="reclaim-mod" style="height:360px;float:left;display:inline;width:98%; overflow-y:auto; overflow-x:hidden;padding:20px 0 0 10px;">
        <form action="" method="post" id="<?=$report_id?"report_edit_form":"report_add_form"?>">
            <table>
		<tr>
        <td width="75" class="label"><b class="resut_table_state_1 zws_em ">*</b>交易方式：</td>
        <td width="120">
            <select class="select" style="width:100px;" id="contract_type_add" disabled>
                <option value="1" <?=$type==1?'selected':'';?>>出售</option>
<!--                <option value="2" --><?//=$type==2?'selected':'';?><!-->出租</option>-->
            </select>
            <div class="errorBox"></div>
        </td>
      <?php
        if($report_id) {
      ?>
          <td width="75"  class="label"><b class="resut_table_state_1 zws_em ">*</b>预约编号：</td>
          <td><input class="input_text w90" type="text" size="14" style="width:254px;" id="contract_number_add" name="contract_number_add" disabled autocomplete="off" value="<?=$report['number'];?>" maxlength="30"><div class="errorBox"></div></td>
      <?php
        }
      ?>
        </tr>
        <tr>
          <td width="75"  class="label"><b class="resut_table_state_1 zws_em ">*</b>门店：</td>
          <td width="120">
            <input type="text" name="contract_agency_add_name" value="<?=$report['agency_name_a'];?>" class="input_text w90 ui-autocomplete-input" autocomplete="off"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
            <input name="contract_agency_add" id="contract_agency_add" value="<?=$report['agency_id_a'];?>" type="hidden">
            <script type="text/javascript">
              $(function(){
                $.widget( "custom.autocomplete", $.ui.autocomplete, {
                  _renderItem: function( ul, item ) {
                    if(item.id>0){
                      return $( "<li>" )
                        .data( "item.autocomplete", item )
                        .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.label+'</span></a>')
                        .appendTo( ul );
                    }else{
                      return $( "<li>" )
                        .data( "item.autocomplete", item )
                        .append('<a class="ui-corner-all" tabindex="-1">'+item.label+'</a>')
                        .appendTo( ul );
                    }
                  }
                });
                $("input[name='contract_agency_add_name']").autocomplete({
                  source: function( request, response ) {
                    var term = request.term;
                    $("input[name='contract_agency_add']").val("");
                    $.ajax({
                      url: "/contract/get_agency_info_by_kw/",
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
                      var agencyname = ui.item.label;
                      var id = ui.item.id;

                      //操作
                      $("input[name='contract_agency_add']").val(id);
                      $("input[name='contract_agency_add_name']").val(agencyname);
                      removeinput = 2;
                    }else{
                      $("input[name='contract_agency_add']").val("");
                      $("input[name='contract_agency_add_name']").val("");
                      removeinput = 1;
                    }
                  },
                  close: function(event) {
                    if(typeof(removeinput)=='undefined' || removeinput == 1){
                      $("input[name='contract_agency_add']").val("");
                      $("input[name='contract_agency_add_name']").val("");
                    }
                  }
                });
              });
            </script>
            <div class="errorBox"></div>
          </td>
          <td width="75"  class="label"><b class="resut_table_state_1 zws_em ">*</b>预约人：</td>
          <td>
            <input type="text" name="contract_broker_add_name" value="<?=$report['broker_name_a'];?>" class="input_text w90 ui-autocomplete-input" autocomplete="off"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
            <input name="contract_broker_add" id="contract_broker_add" value="<?=$report['broker_id_a'];?>" type="hidden">
            <script type="text/javascript">
              $(function(){
                $.widget( "custom.autocomplete", $.ui.autocomplete, {
                  _renderItem: function( ul, item ) {
                    if(item.id>0){
                      return $( "<li>" )
                        .data( "item.autocomplete", item )
                        .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.label+'</span><span class="ui_district">'+item.phone+'</span></a>')
                        .appendTo( ul );
                    }else{
                      return $( "<li>" )
                        .data( "item.autocomplete", item )
                        .append('<a class="ui-corner-all" tabindex="-1">'+item.label+'</a>')
                        .appendTo( ul );
                    }
                  }
                });
                $("input[name='contract_broker_add_name']").autocomplete({
                  source: function( request, response ) {
                    var term = request.term;
                    var contract_agency_add = $("input[name='contract_agency_add']").val();
                    $.ajax({
                      url: "/contract/get_broker_info_by_kw/",
                      type: "GET",
                      dataType: "json",
                      data: {
                        keyword: term,
                        agency_id: contract_agency_add
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
                      var brokername = ui.item.label;
                      var phone = ui.item.phone
                      var id = ui.item.id;
                      var agency_id = ui.item.agency_id;
                      var company_id = ui.item.company_id;

                      //操作
                      $("input[name='contract_broker_add']").val(id);
                      $("input[name='contract_broker_add_name']").val(brokername);
                      $("#contract_phone_add").val(phone);

                      $.ajax({
                          url: "/bargain/get_agency_info_by_agencyid_companyid//",
                        type: "GET",
                        dataType: "json",
                        data: {
                          agency_id: agency_id,
                          company_id: company_id
                        },
                        success: function(data) {
                          if(data.id > 0){
                            var agencyname = data.name;
                            var id = data.id;

                            //操作
                            $("input[name='contract_agency_add']").val(id);
                            $("input[name='contract_agency_add_name']").val(agencyname);
                            removeinput = 2;
                          }else{
                            removeinput = 1;
                          }
                        }
                      });
                      removeinput = 2;
                    }else{
                      removeinput = 1;
                    }
                  },
                  close: function(event) {
                    if(typeof(removeinput)=='undefined' || removeinput == 1){
                      $("input[name='contract_broker_add']").val("");
                      $("input[name='contract_broker_add_name']").val("");
                      $("#contract_phone_add").val("");
                    }
                  }
                });
              });
            </script>
            <input type="hidden" id="contract_phone_add" value="<?=$report['broker_phone_a']?>">
            <div class="errorBox"></div>
          </td>
        </tr>
        <tr>
        <td width="75" class="label">房源编号：</td>
        <td width="120"><input class="input_text w90" style="width:60px;" type="text" size="14" id="contract_houseid_add"  value="<?=$report['house_id'];?>" autocomplete="off"><b style="width:28px;border:1px solid #d1d1d1;height:22px;float:left;display:inline;background:#fcfcfc;border-left:none;text-align:center" onclick="window.parent.window.open_house();">选择</b></td>
        <td width="75"  class="label">楼盘名称：</td>
        <td>
        <input class="input_text w90"  style="width:254px;" type="text" size="14" name = "contract_blockname_add" id="contract_blockname_add" autocomplete="off" value="<?=$report['block_name'];?>">
        <input type="hidden" id="contract_blockid_add" value="<?=$report['block_id'];?>">
        </td>
        </tr>
        <tr>
          <td width="75" class="label"> 房源地址：</td>
          <td colspan="3">
            <input class="input_text w248" style="width:454px;" type="text" size="14" id="contract_addr_add" autocomplete="off" value="<?=$report['house_addr'];?>" maxlength="40">
          </td>
        </tr>
		<script type="text/javascript">
      $(function () {
        $.widget("custom.autocomplete", $.ui.autocomplete, {
          _renderItem: function (ul, item) {
            if (item.id > 0) {
              return $("<li>")
                .data("item.autocomplete", item)
                .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">' + item.label + '</span><span class="ui_district">' + item.districtname + '</span><span class="ui_address">' + item.address + '</span></a>')
                .appendTo(ul);
            } else {
              return $("<li>")
                .data("item.autocomplete", item)
                .append('<a class="ui-corner-all" tabindex="-1">' + item.label + '</a>')
                .appendTo(ul);
            }
          }
        });
        $("input[name='contract_blockname_add']").autocomplete({
          source: function (request, response) {
            var term = request.term;
            $.ajax({
              url: "/community/get_cmtinfo_by_kw/",
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
              var blockname = ui.item.label;
              var id = ui.item.id;
              var streetid = ui.item.streetid;
              var streetname = ui.item.streetname;
              var dist_id = ui.item.dist_id;
              var districtname = ui.item.districtname;
              var address = ui.item.address;

              //操作
              $("input[name='contract_blockid_add']").val(id);
              $("input[name='contract_blockname_add']").val(blockname);
              removeinput = 2;
            } else {
              removeinput = 1;
            }
          },
          close: function (event) {
            if (typeof(removeinput) == 'undefined' || removeinput == 1) {
              $("input[name='contract_blockname_add']").val("");
              $("input[name='contract_blockid_add']").val("");
            }
          }
        });
      });
		</script>
	    </table>

            <table>
                <tr>
                <td width="75" class="label"><b class="resut_table_state_1 zws_em ">*</b>预约时间：</td>
                <td width="120"><input type="text" size="14" class="input_text time_bg" id="contract_time_add" autocomplete="off" value="<?=$report['signing_time'];?>" name="signing_time" onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd HH:00'})"><div class="errorBox"></div></td>
            </tr>
            <tr>
                <td class="label">备注：</td>
                <td colspan="3"><textarea  class="textarea" id="contract_remark_add"><?=$report['remarks'];?></textarea><div class="errorBox"></div></td>
            </tr>
            <tr>
                <td colspan="4" class="center">
                <input type="hidden" name="contract_id" value="<?=$report_id?>">
                <button type="submit" id="submit_edit" class="btn-lv1 btn-left">保存</button>
                <button type="button" class="btn-hui1 JS_Close" onclick="closeParentWin('js_modify_box')">取消</button>
                </td>
            </tr>
            </table>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(function(){

        $(".textarea").blur(function(){
            if($(".textarea").val().length > 100){

                $(this).next(".errorBox").html("您输入的字符超过100，请重新输入！");
            }

        })

    })

</script>
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
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt1"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button" onclick="parent.window.location.reload(true)">确定</button>
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
			    <p class="left" style="font-size:14px;color:#666;"  id="js_prompt2">预约添加成功！</p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>
