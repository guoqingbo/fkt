<body>
<!--实收实付-->
	<div class="js_result_pop" style="width:100%;float:left;position:relative;" id="replace_list">
	    <table class="result_item_list_table_head" style="float:left">
		<thead width="100%" align="center"  border="0" cellspacing="0" style="background:#f0f0f0; height: 29px;line-height: 29px;">
        <tr>
                <td width="6%">对象</td>
                <td width="6%">代收付</td>
                <td width="6%">收金额（元）</td>
                <td width="6%">付金额（元）</td>
                <td width="6%">费用类别</td>
                <td width="6%">费用名称</td>
                <td width="12%">日期</td>
                <td width="6%">备注</td>
                <td width="10%">操作</td>
		    </tr>
		</thead>
		<tbody>
		    <?php if($replace_flow){foreach($replace_flow as $key=>$val){?>
            <tr class="resut_table_border qz_porcee " onclick="view_detail(<?=$val['id'];?>);">
                <td width="6%"><?=$config['target_type'][$val['target_type']];?></td>
                <td width="6%"><?=$config['replace_type'][$val['replace_type']];?></td>
                <td width="6%"><?=strip_end_0($val['collect_money']);?></td>
                <td width="6%"><?=strip_end_0($val['pay_money']);?></td>
                <td width="6%"><?=$config['money_type'][$val['money_type']];?></td>
                <td width="6%"><?=$val['money_name'];?></td>
                <td width="12%"><?=$val['flow_time'];?></td>
                <td width="6%"><?=$val['remark']?$val['remark']:"—";?></td>
                <td width="10%">
                    <?php if($val['status'] ==0){?>
                    <a href="javascript:void(0);" <?php if($auth['replace_edit']['auth']){?>onclick="flow_replace_detail(<?=$val['id'];?>,<?=$bargain['id']?>);"<?php }else{?>onclick="window.parent.window.purview_none();"<?php }?>>修改</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="javascript:void(0);" <?php if($auth['replace_delete']['auth']){?>onclick="open_replace_delete(<?=$val['id'];?>);"<?php }else{?>onclick="window.parent.window.purview_none();"<?php }?>>删除</a>
                    <?php }elseif($val['flow_status'] ==1){?>
                        <?php if($val['is_flow'] ==0){?>
                            <a href="javascript:void(0);" <?php if($auth['replace_complete']['auth']){?>onclick="open_replace_sure(<?=$val['id'];?>);"<?php }else{?>onclick="window.parent.window.purview_none();"<?php }?>>确认收付</a>
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
                <td width="6%"  style="font-weight:bold">合计：</td>
                <td width="6%"></td>
                <td width="6%" class="money_color" style="font-weight:bold" id="replace_collect_total"><?=strip_end_0($replace_collect_money_total);?></td>
                <td width="6%" class="money_color" style="font-weight:bold" id="replace_pay_total"><?=strip_end_0($replace_pay_money_total);?></td>
                <td width="6%">余额</td>
                <td width="6%" class="money_color"
                    style="font-weight:bold"><?= strip_end_0($replace_collect_money_total - $replace_pay_money_total); ?></td>
                <td width="12%"></td>
                <td width="6%"></td>
                <td width="10%"></td>

		    </tr>
		    <?php }else{?>
		    <tr class="resut_table_border qz_porcee"><td colspan="12"><span class="no-data-tip">你还未添加代收付</span></td></tr>
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
    $("#replace_list").find("a").click(function(event){
            event.stopPropagation();
    });

    window.onload = function(){
        var height = window.document.getElementById('replace_list').offsetHeight;
        window.parent.document.getElementById('js_mukuai_box').style.height = height+'px';
    }

    function view_detail(id){
        window.parent.document.getElementById('replace_detail').src = '/bargain/bargain_replace_detail/'+id;
        window.parent.window.openWin('js_replace_detail_pop');
    }

    function flow_replace_detail(id,c_id){
        window.parent.document.getElementById('replace').src = '/bargain/bargain_replace_modify/'+c_id+"/"+id;
        window.parent.window.openWin('js_replace_pop');
    }
    //打开收付删除弹窗
    function open_replace_delete(id){
        window.parent.document.getElementById('flow_id').value = id;
        window.parent.window.openWin('js_del_pop');
    }

    //打开收付确认弹窗
    function open_replace_sure(id){
        window.parent.document.getElementById('flow_id').value = id;
        window.parent.window.openWin('js_sure_flow_pop');
    }
</script>
