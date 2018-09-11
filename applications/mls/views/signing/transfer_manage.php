</body>
<!--权证流程开始-->
	<div class="js_result_pop" style="position:relative;width:100%;float:left;" id="warrant_step">
	    <?php if($warrant_step_total >0){?>
	    <div class="warrant_process">
	    </div>
        <?php if($contract['is_completed']==0){?>
	    <script>
		$(function(){
		    $('.warrant_process').find('.warrant_process_bg2').last().children('img').attr('src','<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/qz_process6_12.gif');
		})
	    </script>
        <?php }else{?>
        <script>
		$(function(){
		    $('.warrant_process').find('.warrant_process_bg2').last().remove();
		})
	    </script>
        <?php }?>
	    <!--权证明细列表开始-->
	    <table class="result_item_list_table_head" style="float:left">
		<thead width="100%" align="center"  border="0" cellspacing="0">
		    <tr width="100%" height="38" bgcolor="#f0f0f0">
                <td width="5%">步骤</td>
                <td width="8%">流程名称</td>
<!--                <td width="8%">流程分类</td>-->
<!--                <td width="5%">天数</td>-->
<!--                <td width="5%">开始日期</td>-->
                <td width="5%">完成日期</td>
                <td width="5%">经办人</td>
                <td width="5%">状态</td>
<!--                <td width="5%">备注</td>-->
		    </tr>
		</thead>
        <tbody id="warrant_list">
		    <?php foreach($warrant_step as $key=>$val){?>
                <!--		    <tr class="resut_table_border qz_porcee " onclick="stage_detail(<? //=$val['id'];?>//);">-->
                <tr class="resut_table_border qz_porcee " onclick="stage_detail(<?= $val['id']; ?>);">
                <td width="5%"><?=$stage_conf[$val['step_id']]['text'];?></td>
                <td width="8%"><?=$val['stage_name1'];?></td>
<!--                <td width="5%">--><?//=$config["stage_type"][$val['stage_type']];?><!--</td>-->
<!--                <td width="5%">--><?//=$val['number_days'];?><!--</td>-->
<!--                <td width="5%">--><?//=date('Y-m-d',$val['start_time']);?><!--</td>-->
                <td width="5%"><?=isset($val['complete_time'])&&$val['complete_time']!=""?date('Y-m-d',$val['complete_time']):"";?></td>
                <td width="5%"><?=$val['complete_signatory_name'];?></td>
<!--                <td width="5%">--><?//=$val['remark'];?><!--</td>-->
                <td width="5%"><?=$val['isComplete']==1?"已完成":"处理中";?></td>
		    </tr>
		    <?php }?>
		</tbody>
	    </table>
	    <?php }else{?>
	    <!--新建模板-->
	    <div class="qz_precess_add_modle">
		 <p>还未进行到过户流程，请耐心等待</p>
	     </div>
	    <?php  }?>
	</div>
	<!--权证流程结束-->
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
    $("#warrant_list").find("a").click(function(event){
            event.stopPropagation();
    });
    window.onload = function(){
        var height = window.document.getElementById('warrant_step').offsetHeight;
        window.parent.document.getElementById('js_mukuai_box').style.height = height+'px';
    }

    function view_detail(id){
        $.post("/signing/warrant_detail",{id:id},function(data){
            if(data['warrant_list']['is_remind']==1){
                window.parent.document.getElementById('warrant').src = '/signing/contract_warrant_detail/'+id;
                window.parent.window.openWin('js_warrant_pop');
            }else{
                window.parent.document.getElementById('warrant1').src = '/signing/contract_warrant_detail/'+id;
                window.parent.window.openWin('js_warrant_pop1');
            }
        },"json");
    }
    function stage_detail(id){
        $.post("/signing/stage_detail",{id:id},function(data){
            if(data['warrant_list']['is_remind']==1){
                window.parent.document.getElementById('warrant').src = '/signing/bargain_transfer_detail/'+id;
                window.parent.window.openWin('js_warrant_pop');
            }else{
                window.parent.document.getElementById('warrant1').src = '/signing/bargain_transfer_detail/'+id;
                window.parent.window.openWin('js_warrant_pop1');
            }
        },"json");
    }
    function open_choose_template(id){
        window.parent.document.getElementById('choose_template').src = '/signing/get_all_template/'+id;
        window.parent.window.openWin('js_temp_box');
    }


    function add_warrant(){
        window.parent.document.getElementById('addtemp').src = '/signing/modify_warrant_index/'+'<?=$contract['id'];?>'+"/";
        window.parent.window.openWin('js_addtemp_pop');
    }

    function warrant_detail(id,c_id){
        window.parent.document.getElementById('stage_id').value = id;
        window.parent.document.getElementById('addtemp').src = '/signing/modify_warrant_index/'+c_id+"/"+id;
        window.parent.window.openWin('js_addtemp_pop');
    }

    function open_warrant_delete(id){
        window.parent.document.getElementById('stage_id').value = id;
        window.parent.window.openWin('js_del_warrant');
    }

</script>

