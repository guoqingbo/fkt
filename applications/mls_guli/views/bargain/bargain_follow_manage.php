<body>
    <!--跟进明细列表开始-->
    <form action="" method="post" name="search_form">
	<div class="js_result_pop inner shop_inner" style=" width:100%; float:left;" id="follow_list">
	    <table class="result_item_list_table_head" style="float:left">
		<thead width="100%" align="center"  border="0" cellspacing="0" style="background:#f0f0f0; height: 29px;line-height: 29px;">
		    <tr width="100%" height="38" bgcolor="#f0f0f0">
			<td width="23%">跟进日期</td>
			<td width="14%">类别</td>
			<td width="40%">内容</td>
			<td width="23%">修改人</td>
		    </tr>
		</thead>
		<tbody>
		    <?php if($follow){foreach($follow as $key=>$val){?>
		    <tr class="resut_table_border">
                <td width="23%" ><?=date('Y-m-d',$val['updatetime']);?></td>
                <td width="14%" ><?=$val['type_name'];?></td>
                <td width="40%" title="<?=$val['content1']?>"><?=$val['content'];?></td>
                <td width="23%"><?=$val['signatory_name'];?></td>
		    </tr>
		    <?php }}else{?>
            <tr class="resut_table_border qz_porcee" id="follow_no_list"><td colspan="4"><span class="no-data-tip">你还未有跟进明细</span></td></tr>
            <?php }?>
		</tbody>
	    </table>

	</div>
	<!--跟进明细列表结束-->
        <div class="fun_btn clearfix count_info_border" id="js_fun_btn" style="margin:10px 0 0 0;">
            <div class="get_page">
                <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
            </div>
        </div>
    </form>
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

    window.onload = function(){
        var height = window.document.getElementById('follow_list').offsetHeight;
        window.parent.document.getElementById('js_mukuai_box').style.height = height+'px';
    }
</script>

