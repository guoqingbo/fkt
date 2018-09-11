<div class="achievement_money_pop zws_second_w">
    <dl class="title_top" style="margin:0 0 10px 0;">
        <dd>选择合作</dd>
    </dl>
    <!-- 上部菜单选项，按钮-->
    <div class="search_box clearfix zws_position " id="js_search_box_02" style="padding:0 0 10px 0;">
	<form name="search_form" id="subform" method="post" action="">
	<div class="fg_box">
	    <p class="fg fg_tex">交易类型：</p>

	    <div class="fg mr10" style="*padding-top:10px;">
		<select class="select zws_w" disabled>
		    <option value="1" <?=$type==1?"selected":""?>>出售</option>
		    <option value="2" <?=$type==2?"selected":""?>>出租</option>
		</select>
	    </div>
        </div>

        <div class="fg_box">
	    <p class="fg zws_input_p">状态：</p>

	    <div class="fg mr10" style="*padding-top:10px;">
		<select class="select zws_w" name='esta' style="width:72px;">
		    <option value=''>不限</option>
		    <?php foreach($esta_conf as $k => $v){?>
		    <option value="<?php echo $k;?>" <?php if($post_param['esta']==$k){echo "selected='selected'";}?>><?php echo $v;?></option>
		    <?php }?>
		</select>
	    </div>
        </div>
	<div class="fg_box">
	    <p class="fg zws_input_p">楼盘：</p>
	    <div class="fg">
		<input type="text" value="<?=$post_param['block_name'];?>" name="block_name" class="input w90 ui-autocomplete-input" autocomplete="off">
        <input type="hidden" value="<?=$post_param['block_id'];?>" name="block_id">
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
        </div>
	<div class="fg_box">
          <p class="fg zws_input_p">所属经纪人：</p>

          <div class="fg mr10" style="*padding-top:10px;">
		<select  class="select zws_w88" style="width:120px;" name="agency_id" <?=$level==6?'disabled':'';?>>
		    <?php if($level<6){?>
		    <option value = ''>请选择门店</option>
		    <?php }?>
		    <?php if($agency){foreach($agency as $key =>$val){?>
		    <option value="<?=$val['id'];?>" <?=$post_param['agency_id']==$val['id']?"selected":""?>><?=$val['name'];?></option>
		    <?php }}?>
		</select>
          </div>
          <div class="fg mr10" style="*padding-top:10px;">
		<select class="select zws_w88" name="broker_id" style="width:100px;">
		<option value = ''>请选择人员</option>
		<?php if($brokers){ foreach($brokers as $key=>$val){ ?>
		    <option value='<?=$val['broker_id']?>' <?=$post_param['broker_id']==$val['broker_id']?"selected":""?>><?=$val['truename']?></option>
		<?php }}?>
            </select>
          </div>
        </div>
	  <script>
	    $("select[name='agency_id']").change(function(){
		var agency_id = $("select[name='agency_id']").val();
		if(agency_id){
		    $.ajax({
			url:"/contract/broker_list",
			type:"GET",
			dataType:"json",
			data:{
			   agency_id:agency_id
			},
			success:function(data){
			    if(data['result'] == 1){
				var html = "<option value=''>请选择</option>";
				for(var i in data['list']){
				    html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
				}
				$("select[name='broker_id']").html(html);
			    }else{
				$("select[name='broker_id']").html("<option value=''>请选择</option>");
			    }
			}
		    })
		}else{
		    $("select[name='broker_id']").html("<option value=''>请选择</option>");
		}
	    })
	</script>
        <div class="fg_box">
	    <input type='hidden' name='page' value='1'>
	    <div class="fg"> <a href="javascript:void(0)" onclick="$('#subform').submit();return false;" class="btn"><span class="btn_inner">搜索</span></a> </div>
	    <div class="fg"> <a href="/contract/get_cooperate/<?=$type?>" class="reset">重置</a> </div>
        </div>
      </form>
    </div><script>
$(function(){
	document.onkeydown = function(e){ //enter
		var ev = document.all ? window.event : e;
		if(ev.keyCode==13) {
			$('#subform').submit();return false;
		 }
	}
});  
</script>
    <!-- 上部菜单选项，按钮---end-->
    <div class="table_all m0">
	<div class="title shop_title">
	    <table class="table">
		<tbody>
		    <tr>
			<td class="zws_W22">合作编号</td>
			<td class="zws_W8">状态</td>
			<td class="zws_W8">类型</td>
			<td>房源信息</td>
			<td class="zws_W22">甲方经纪人</td> 
			<td class="zws_W22">乙方经纪人</td>  
		    </tr>
		</tbody>
	    </table>
	</div>
	<div class="inner shop_inner">
	    <table class="table">
		<tbody>
		    <?php if($list){foreach($list as $key=>$val){?>
		    <tr class="">
			<td class="zws_W22"><div class="info"><input type="radio" name="order_sn" value="<?=$val['id'];?>" <?=$order_sn==$val['order_sn']?'checked':'';?>><?=$val['order_sn'];?></div></td>
			<td class="zws_W8 "><div class="info <?=$val['esta']==7?'resut_table_state_2':''?>"><?=$esta_conf[$val['esta']];?></div></td>
			<td class="zws_W8"><div class="info"><?=$type==1?'出售':'出租'?></div></td>
			<td>
			    <div class="info">
				<?=$district_arr[$val['house_info']['district_id']]['district']?>-<?=$district_arr[$val['house_info']['district_id']]['street'][$val['house_info']['street_id']]['streetname'];?>
				<?=$val['house_info']['block_name'];?>
				<?=$val['house_info']['room'];?>室<?=$val['house_info']['hall'];?>厅<?=$val['house_info']['toilet'];?>卫
				<?=intval($val['house_info']['buildarea']);?>㎡
				<?=intval($val['house_info']['price']);?>万
			    </div>
			</td>
			<td class="zws_W22"><div class="info"><?=$val['apply_type']==1?$val['agent_name_a'].'&nbsp;&nbsp;'.$val['broker_name_a']:$val['agent_name_b'].'&nbsp;&nbsp;'.$val['broker_name_b']?></div></td>
			<td class="zws_W22"><div class="info"><?=$val['apply_type']==2?$val['agent_name_a'].'&nbsp;&nbsp;'.$val['broker_name_a']:$val['agent_name_b'].'&nbsp;&nbsp;'.$val['broker_name_b']?></div></td>
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
                    <button type="button" class="btn-lv1 btn-left" onclick="$('input[type=radio]').removeAttr('checked');">清空</button>
                    <button type="button" class="btn-lv1 btn-left" onclick='sure_choose();'>确定</button>
                    <button type="button" class="btn-hui1" onclick="closeParentWin('js_cooperate_box');">取消</button>
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
		<p class="text" id="dialog_do_warnig_tip">请选择一条合作</p>
		<button type="button" class="btn-lv1 btn-mid JS_Close">确定</button>
	    </div>
	</div>
    </div>
</div>
<div class="shade"></div>
<script>
    $(function(){
        $("tr").live('click',function(){
            $(this).find("input").attr('checked',true);
        });
    })
    function sure_choose(){
        var id = $("input[type='radio']:checked").val();
	    window.parent.window.get_cooperate_info(id);
    }
</script>
