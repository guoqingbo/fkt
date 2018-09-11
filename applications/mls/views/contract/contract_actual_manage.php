<body>
<!--实收实付-->
	<div class="js_result_pop" style="width:100%;float:left;position:relative;" id="actual_list">
	    <table class="result_item_list_table_head" style="float:left">
		<thead width="100%" align="center"  border="0" cellspacing="0">
		    <tr width="100%" height="38" bgcolor="#f0f0f0">
			<td width="12%">收付日期</td>
			<td width="6%">款类</td>
			<td width="6%">收方</td>
			<td width="8%">实收金额(元)</td>
			<td width="6%">付方</td>
			<td width="10%">实付金额(元)</td>
			<td width="8%">说明</td>
			<td width="10%">录入门店</td>
			<td width="6%">录入人</td>
			<td width="10%">单据号</td>
			<td width="6%">状态</td>
			<td width="10%">操作</td>
		    </tr>
		</thead>
		<tbody>
		    <?php if($actual_flow){foreach($actual_flow as $key=>$val){?>
            <tr class="resut_table_border qz_porcee " onclick="view_detail(<?=$val['id'];?>);">
                <td width="12%"><?=$val['flow_time'];?></td>
                <td width="6%"><?=$config['money_type'][$val['money_type']];?></td>
                <td width="6%"><?=$val['collect_type']?$config['collect_type'][$val['collect_type']]:'—';?></td>
                <td width="8%"><?=strip_end_0($val['collect_money']);?></td>
                <td width="6%"><?=$val['pay_type']?$config['pay_type'][$val['pay_type']]:'—';?></td>
                <td width="10%"><?=strip_end_0($val['pay_money']);?></td>
                <td width="8%"><?=$val['remark']?$val['remark']:"—";?></td>
                <td width="10%"><?=$val['entry_agency_name'];?></td>
                <td width="6%"><?=$val['entry_broker_name'];?></td>
                <td width="10%"><?=$val['docket']?$val['docket']:"—";?></td>
                <td width="6%"><?=$config['flow_status'][$val['status']];?></td>
                <td width="10%">
                    <?php if($val['status'] ==0){?>
                    <a href="javascript:void(0);" <?php if($auth['actual_edit']['auth']){?>onclick="flow_actual_detail(<?=$val['id'];?>,<?=$contract['id']?>);"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>修改</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="javascript:void(0);" <?php if($auth['actual_delete']['auth']){?>onclick="open_actual_delete(<?=$val['id'];?>);"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>删除</a>
                    <?php }elseif($val['flow_status'] ==1){?>
                        <?php if($val['is_flow'] ==0){?>
                            <a href="javascript:void(0);" <?php if($auth['actual_complete']['auth']){?>onclick="open_actual_sure(<?=$val['id'];?>);"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>确认收付</a>
                        <?php }else{?>
                            <span class="qz_color_over1">修改</span>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <span class="qz_color_over1">删除</span>
                        <?php }?>
                    <?php }else{?>
                        <span class="qz_color_over1">修改</span>&nbsp;&nbsp;|&nbsp;&nbsp;
                        <span class="qz_color_over1">删除</span>
                    <?php }?>
                </td>
		    </tr>
		    <?php }?>
		    <tr class="resut_table_border qz_porcee">
                <td width="12%"  style="font-weight:bold">合计：</td>
                <td width="6%"></td>
                <td width="6%"></td>
                <td width="8%" class="money_color" style="font-weight:bold" id="actual_collect_total"><?=strip_end_0($actual_collect_money_total);?></td>
                <td width="6%"></td>
                <td width="10%" class="money_color" style="font-weight:bold" id="actual_pay_total"><?=strip_end_0($actual_pay_money_total);?></td>
                <td width="8%"></td>
                <td width="10%"></td>
                <td width="6%"></td>
                <td width="10%"></td>
                <td width="6%"></td>
                <td width="10%">
                </td>
		    </tr>
		    <?php }else{?>
		    <tr class="resut_table_border qz_porcee"><td colspan="12"><span class="no-data-tip">你还未添加实收实付</span></td></tr>
		    <?php }?>
		</tbody>
	    </table>
	</div>
</body>
	<!--实收实付结束-->
<script>
    $(window).resize(function(e) {
        $(".sale_message dt").css("width",($(".sale_message").width()-100-36)+"px");
        $(".qz_precess_add_modle p").css("padding-left",($(".qz_precess_add_modle").width()-450)/2+"px");
    });
    
    $(".qz_precess_add_modle p").css("padding-left",($(".qz_precess_add_modle").width()-450)/2+"px");
	$(".sale_message dt").css("width",($(".sale_message").width()-100-36)+"px");
     //items   table   隔行换色
  
    $("tbody tr:odd").css("background","#f7f7f7");
    $("tbody tr:even").css("background","#fcfcfc");
    $("#actual_list").find("a").click(function(event){
            event.stopPropagation();
    });
    
    window.onload = function(){
        var height = window.document.getElementById('actual_list').offsetHeight;
        window.parent.document.getElementById('js_mukuai_box').style.height = height+'px';
    }
    
    function view_detail(id){
        window.parent.document.getElementById('actual').src = '/contract/contract_actual_detail/'+id;
        window.parent.window.openWin('js_actual_pop');
    }
    
    function flow_actual_detail(id,c_id){
        window.parent.document.getElementById('actual').src = '/contract/contract_actual_modify/'+c_id+"/"+id;
        window.parent.window.openWin('js_actual_pop');
    }
    //打开收付删除弹窗
    function open_actual_delete(id){
        window.parent.document.getElementById('flow_id').value = id;
        window.parent.window.openWin('js_del_pop');
    }
    
    //打开收付确认弹窗
    function open_actual_sure(id){
        window.parent.document.getElementById('flow_id').value = id;
        window.parent.window.openWin('js_sure_flow_pop');
    }
</script>
