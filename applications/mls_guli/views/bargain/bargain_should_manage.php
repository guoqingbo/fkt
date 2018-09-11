<body>
	<!--应收应付开始-->
        <div class="js_result_pop"  style="width:100%;float:left;position:relative;" id="should_list">
	    <table class="result_item_list_table_head" style="float:left">
		<thead width="100%" align="center"  border="0" cellspacing="0" style="background:#f0f0f0; height: 29px;line-height: 29px;">
		    <tr width="100%" height="38" bgcolor="#f0f0f0">
			<td width="12%">收付日期</td>
			<td width="8%">款类</td>
			<td width="8%">收方</td>
			<td width="8%">应收金额(元)</td>
			<td width="8%">付方</td>
			<td width="10%">应付金额(元)</td>
			<td width="8%">说明</td>
			<td width="10%">录入门店</td>
			<td width="8%">录入人</td>
			<td width="8%">状态</td>
			<td width="10%">操作</td>
		    </tr>
		</thead>
		<tbody>
		    <?php if($should_flow){foreach($should_flow as $key=>$val){?>
		    <tr class="resut_table_border qz_porcee " onclick="view_detail(<?=$val['id'];?>);">
			<td width="12%"><?=$val['flow_time'];?></td>
			<td width="6%"><?=$config['money_type'][$val['money_type']];?></td>
			<td width="6%"><?=$val['collect_type']?$config['collect_type'][$val['collect_type']]:'—';?></td>
			<td width="8%"><?=strip_end_0($val['collect_money']);?></td>
			<td width="6%"><?=$val['pay_type']?$config['pay_type'][$val['pay_type']]:'—';?></td>
			<td width="10%"><?=strip_end_0($val['pay_money']);?></td>
			<td width="8%"><?=$val['remark']?$val['remark']:"—";?></td>
			<td width="10%"><?=$val['entry_department_name'];?></td>
			<td width="6%"><?=$val['entry_signatory_name'];?></td>
            <td width="6%" class="<?php if($val['status']==1){?>qz_color_over<?php }elseif($val['status']==2){?>qz_color_over result_fail<?php }else{?><?php }?>"><?=$config['flow_status'][$val['status']];?></td>
			<td width="10%">
			    <?php if($val['status'] ==0){?>
			    <a href="javascript:void(0);" <?php if($auth['should_edit']['auth']){?>onclick="flow_should_detail(<?=$val['id'];?>,<?=$bargain['id'];?>);"<?php }else{?>onclick="window.parent.window.purview_none();"<?php }?>>修改</a>&nbsp;&nbsp;|&nbsp;&nbsp;
			    <a href="javascript:void(0);" <?php if($auth['should_delete']['auth']){?>onclick="open_should_delete(<?=$val['id'];?>)"<?php }else{?>onclick="window.parent.window.purview_none();"<?php }?>>删除</a>
			    <?php }else{?>
			    <span class="qz_color_over1">修改</span>&nbsp;&nbsp;|&nbsp;&nbsp;
                <span class="qz_color_over1">删除</span>
			    <?php }?>
			</td>
		    </tr>
		    <?php }?>
		    <tr class="resut_table_border qz_porcee ">
			<td width="12%" style="font-weight:bold">合计</td>
			<td width="8%"></td>
			<td width="8%"></td>
			<td width="8%" class="money_color" style="font-weight:bold" id="should_collect_total"><?=strip_end_0($should_collect_money_total);?></td>
			<td width="8%"></td>
			<td width="10%" class="money_color" style="font-weight:bold" id="should_pay_total"><?=strip_end_0($should_pay_money_total);?></td>
			<td width="8%"></td>
			<td width="10%"></td>
			<td width="8%"></td>
			<td width="8%"></td>
			<td width="10%"></td>
		    </tr>
		    <?php }else{?>
		    <tr class="resut_table_border qz_porcee "><td colspan="12"><span class="no-data-tip">你还未添加应收应付</span></td></tr>
		    <?php }?>
		</tbody>
	    </table>
	</div>
	<!--应收应付结束-->
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

    $("#should_list").find("a").click(function(event){
            event.stopPropagation();
    });

    window.onload = function(){
        var height = window.document.getElementById('should_list').offsetHeight;
        window.parent.document.getElementById('js_mukuai_box').style.height = height+'px';
    }

    function view_detail(id){
        window.parent.document.getElementById('should').src = '/bargain/bargain_should_detail/'+id;
        window.parent.window.openWin('js_should_pop');
    }

    function flow_should_detail(id,c_id){
        window.parent.document.getElementById('should').src = '/bargain/bargain_should_modify/'+c_id+"/"+id;
        window.parent.window.openWin('js_should_pop');
    }
    //打开收付删除弹窗
    function open_should_delete(id){
        window.parent.document.getElementById('flow_id').value = id;
        window.parent.window.openWin('js_del_pop1');
    }



</script>

