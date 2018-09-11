<div class="achievement_money_pop zws_second_w">
    <dl class="title_top" style="margin:0 0 10px 0;">
        <dd>选择房源</dd>
    </dl>
    <!-- 上部菜单选项，按钮-->
    <div class="search_box clearfix zws_position " id="js_search_box_02" style="padding:0 0 10px 0;">
      <form name="search_form" id="search_form" method="post" action="">
        <div class="fg_box">
			<p class="fg zws_input_p">状态：</p>

			<div class="fg mr10" style="*padding-top:10px;">
				<select class="select zws_w" name="status">
					<option>不限</option>
					<?php foreach($config['status'] as $key =>$val){?>
					<option value="<?=$key;?>" <?=$post_param['status']==$key?"selected":"";?>><?=$val;?></option>
					<?php }?>
				</select>
			</div>
        </div>
        <div class="fg_box">
			<p class="fg zws_input_p">楼盘名称：</p>
			<div class="fg">
			  <input type="text" name="block_name" class="input w90 ui-autocomplete-input" autocomplete="off" value="<?=$post_param['block_name'];?>">
			  <input type="hidden" name="block_id" value="<?=$post_param['block_id'];?>">
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
				$("#block_id").val("");
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
			<p class="fg zws_input_p">房源编号：</p>
			<div class="fg">
				<input type="text" value="<?=$post_param['house_id'];?>" name ="house_id" class="input w90 ui-autocomplete-input" autocomplete="off">
			</div>
        </div>
        <div class="fg_box">
            <p class="fg zws_input_p">所属经纪人：</p>

            <div class="fg mr10" style="*padding-top:10px;">
				<select class="select zws_w88" name="agency_id" id='agency_id' style=" width:14em;">
						<?php
					if (is_full_array($post_config['agencys'])) {
						foreach($post_config['agencys'] as $val){?>
							<option value="<?=$val['id'];?>" <?php if ($val['id'] == $post_param['agency_id']) {echo 'selected';}?>><?=$val['name'];?></option>
				<?php }}?>
				</select>
			</div>
			<div class="fg mr10" style="*padding-top:10px;">
				<select class="select zws_w88" name="broker_id" id='broker_id' style="width:6em;">
						<?php if (is_full_array($post_config['brokers'])) { 
                        foreach($post_config['brokers'] as $val){?>
                             <option value="<?=$val['broker_id'];?>" <?php if ($val['broker_id'] == $post_param['broker_id']) {echo 'selected';}?>><?=$val['truename'];?></option>
                 <?php }}?>
				</select>
			</div>
        </div>
	 <script>	
			$("#agency_id").change(function(){
			    var agency_id = $('#agency_id').val();
			    if(agency_id){
				$.ajax({
				    url:"/collocation_contract/broker_list",
				    type:"GET",
				    dataType:"json",
				    data:{
				       agency_id:agency_id
				    },
				    success:function(data){
					if(data['result'] == 1){
					    var html = "";
					    for(var i in data['list']){
						html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
					    }
					    $('#broker_id').html(html);
					}
				    }
				})
			    }else{
				$('#broker_id').html("<option value=''>请选择</option>");
			    }
			});
			</script>
        <div class="fg_box">
		<input type="hidden" name="pg" value="1">
	    <input type="hidden" name="page" value="1">
	    <div class="fg"> 
			<a href="javascript:void(0)" class="btn" onclick="$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;"><span  class="btn_inner">搜索</span></a> 
		</div>
	    <div class="fg"> <a href="/collocation_contract/get_house" class="reset">重置</a> </div>
        </div>
      </form>
    </div>
<script>
$(function(){
	document.onkeydown = function(e){ //enter
		var ev = document.all ? window.event : e;
		if(ev.keyCode==13) {
			$('#search_form :input[name=page]').val('1');$('#search_form').submit();return false;
		 }
	}
});  
</script>
    <!-- 上部菜单选项，按钮---end-->
    <div class="table_all m0">
    <div class="title shop_title">
      <table class="table">
        <tbody><tr>
          <td class="zws_W8">房源编号</td>
          <td class="zws_W8">状态</td>
          <td class="zws_W8">类型</td>
          <td class="zws_W22">楼盘</td>
          <td class="zws_W8">楼层</td>
          <td class="zws_W8">业主姓名</td>
          <td class="zws_W8">面积<br/>(m²)</td>
          <td class="zws_W8">价格<br/>(W)</td>
          <td>所属人员</td>                                                   
        </tr>
      </tbody></table>
    </div>
    <div class="inner shop_inner">
      <table class="table">
        <tbody>
	<?php if($list){foreach($list as $key=>$val){?>
        <tr class="">
	    <td class="zws_W8"><div class="info"><input type="radio" name="id" value="<?=$val['id'];?>"><?=$val['house_id'];?></div></td>
          <td class="zws_W8"><div class="info"><?=$config['status'][$val['status']];?></div></td>
          <td class="zws_W8"><div class="info"><?=$type==1?"出售":"出租"?></div></td>
          <td class="zws_W22"><div class="info"><?=$val['block_name'];?></div></td>
          <td class="zws_W8"><div class="info"><?=$val['floor'];?></div></td>
          <td class="zws_W8"><div class="info"><?=$val['owner'];?></div></td>
          <td class="zws_W8"><div class="info"><?=$val['buildarea'];?></div></td>
          <td class="zws_W8"><div class="info"><?=$val['price'];?></div></td>
          <td><div class="info"><?=$val['agency_name'];?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$val['broker_name'];?></div></td>
        </tr>
	<?php }}else{?>
	<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
	<?php }?>
      </tbody>
      </table>
    </div>
  </div>
    <div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn" style="margin-top:0;">
	<div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
	</div>
    </div>

    <!--保存和确认-->  
   <div  class="aad_pop_p_T20 aad_pop_p_B20">
        <table width="100%">
             <tr>
               <td class="zws_center">
                  <button type="button" id="dialog_share" class="btn-lv1 btn-left" onclick='sure_choose();'>确定</button>
                  <button type="button" class="btn-hui1"  onclick="hide()">取消</button>
              </td>
             </tr>
        </table>

   </div> 
 
</div>
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" style="width:300px;height:140px;">
    <div class="hd">
	<div class="title">提示</div>
	<div class="close_pop">
	    <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
	</div>
    </div>
    <div class="mod">
	<div class="inform_inner">
	    <div class="up_inner">
		<p class="text" id="dialog_do_warnig_tip">请选择一条房源</p>
		<button type="button" class="btn-lv1 btn-mid JS_Close">确定</button>
	    </div>
	</div>
    </div>
</div>
<div class="shade"></div>
<script>
    function sure_choose(){
	var id = $("input[type='radio']:checked").val();
	if(id){
	    window.parent.window.get_info(id);
	}else{
	    openWin('js_pop_do_warning');
	}
    }
    function hide(){
	window.parent.document.getElementById('GTipsCoverjs_house_box').style.display='none';
	window.parent.document.getElementById('js_house_box').style.display='none';
	window.parent.document.getElementById('js_pop_box').style.display='none';
    }
</script>
