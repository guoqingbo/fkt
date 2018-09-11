<body>
    <!--业绩分成明细开始-->
	<div class="js_result_pop"  style="width:100%;float:left;position:relative;" id="divide_list">
        <input type="hidden" id="percent_total" value="<?=$divide_total['percent_total'];?>">
	    <table class="result_item_list_table_head" style="float:left">
		<thead width="100%" align="center"  border="0" cellspacing="0">
		    <tr width="100%" height="38" bgcolor="#f0f0f0">
			<td width="10%">录入时间</td>
			<td width="10%">归属门店</td>
			<td width="10%">归属人</td>
			<td width="10%">分成占比</td>
			<td width="10%">应分成金额（元）</td>
			<td width="10%">实际分成金额（元）</td>
			<td width="10%">分成描述</td>
			<td width="15%">门店业绩</td>
            <td width="10%" style="line-height:22px;"><b style="float:left;display:inline;">操作</b> <?php if($contract['is_commission']==0 && $divide_num > 0){?><a href="javascript:void(0);" class="warrant_process_over" <?php if($auth['divide_complete']['auth']){?>onclick="window.parent.window.openWin('js_commission_pop');"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?> id="complete_commission" style="margin-right:18px;">已结佣</a><?php }?></td>
		    </tr>
		</thead>
		<tbody>
            <?php if($divide_list){foreach($divide_list as $key=>$val){?>
		    <tr class="resut_table_border qz_porcee "  onclick="view_detail(<?=$val['id'];?>);">
                <td width="5%"><?=date('Y-m-d',$val['entry_time']);?></td>
                <td width="15%"><?=$val['agency_name'];?></td>
                <td width="10%"><?=$val['broker_name'];?></td>
                <td width="10%"><?=strip_end_0($val['percent']);?>%</td>
                <td width="10%"><?=strip_end_0($contract['commission_total']*$val['percent']/100);?></td>
                <td width="10%"><?=strip_end_0($val['divide_price']);?></td>
                <td width="10%"><?=$config['divide_type'][$val['divide_type']];?></td>
                <td width="15%"><?=$val['achieve_agency_name_b'];?>—<?=$val['achieve_broker_name_b'];?></td>
                <td width="10%">
                    <?php if($val['is_complete']==0){?>
                    <a href="javascript:void(0);" <?php if($auth['divide_edit']['auth']){?>onclick="divide_edit(<?=$val['id'];?>,<?=$contract['id'];?>)"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>修改</a>|
                    <a href="javascript:void(0);" <?php if($auth['divide_delete']['auth']){?>onclick="window.parent.window.open_divide_delete(<?=$val['id'];?>);"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>删除</a>
                    <?php }else{?>
                    <span class="qz_color_over1">修改</span>|
                    <span class="qz_color_over1">删除</span>
                    <?php }?>
                </td>
		    </tr>
            <?php }?>
		    <tr class="resut_table_border qz_porcee ">
                <td width="10%" style="font-weight:bold">合计</td>
                <td width="15%"></td>
                <td width="7%"></td>
                <td width="7%" class="money_color" style="font-weight:bold" id="divide_percent_total"><?=strip_end_0($divide_total['percent_total']);?>%</td>
                <td width="7%" class="money_color" style="font-weight:bold" id="divide_commission_total"><?=strip_end_0($contract['commission_total']);?></td>
                <td width="7%" class="money_color" style="font-weight:bold" id="divide_price_total"><?=strip_end_0($divide_total['price_total']);?></td>
                <td width="7%"></td>
                <td width="15%"></td>
                <td width="15%"></td>
                <td width="10%"></td>
		    </tr>
            <?php }else{?>
            <tr class="resut_table_border qz_porcee "><td colspan="11"><span class="no-data-tip">你还未添加业绩分成</span></td></tr>
            <?php }?>
		</tbody>
	    </table>
	</div>
	<!--业绩分成明细结束-->
</body>
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
    $("#should_flow").find("tr").css("background","none");
    $("#add_actual").find("tr").css("background","none");
    
    $("#divide_list").find("a").click(function(event){
            event.stopPropagation();
    });
    
    window.onload = function(){
        var height = window.document.getElementById('divide_list').offsetHeight;
        window.parent.document.getElementById('js_mukuai_box').style.height = height+'px';
    }
    
    function view_detail(id){
        window.parent.document.getElementById('divide').src = '/contract/contract_divide_detail/'+id;
        window.parent.window.openWin('js_divide_pop');
    }
    function divide_edit(id,c_id){
        window.parent.document.getElementById('divide').src = '/contract/contract_divide_modify/'+c_id+"/"+id;
        window.parent.window.openWin('js_divide_pop');
    }
    //打开收付删除弹窗
    function open_should_delete(id){
        window.parent.document.getElementById('divide_id').value = id;
        window.parent.window.openWin('js_del_divide');
    }
</script>

