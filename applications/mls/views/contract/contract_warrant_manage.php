</body>
<!--权证流程开始-->
	<div class="js_result_pop" style="position:relative;width:100%;float:left;" id="warrant_step">
	    <?php if($warrant_step_total >0){?>
	    <div class="warrant_process">
		<ul>
		    <?php if($warrant_step){foreach($warrant_step as $key => $val){?>
                <?php if($val['isComplete']==1){?>
                    <li class="warrant_process_bg1" title="<?=$val['stage_name1'];?>" onclick="view_detail(<?=$val['id'];?>);">
                    <p><?=$val['stage_name2'];?></p>
                    <span><?=$val['complete_broker_name'];?>　<?=$val['complete_time'];?></span>
                    </li>
                <?php }else{?>
                    <li class="warrant_process_bg3" title="<?=$val['stage_name1'];?>" onclick="view_detail(<?=$val['id'];?>);">
                        <p><?=$val['stage_name2'];?></p>
                    </li>
                <?php }?>
                <li class="warrant_process_bg2">
                <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/qz_process5_10.gif" />
                </li>
		    <?php }}?>
            <?php if($contract['is_completed']==0){?>
		    <li class="warrant_process_bg5" <?php if($auth['warrant_add']['auth']){?>onclick="add_warrant();"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>
                <a href="javascript:void(0);">
                <p style=" font-weight: normal;">添加步骤</p></a>
		    </li>
            <?php }?>
		</ul>
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
			<td width="8%">流程阶段</td>
			<td width="5%">备注</td>
			<td width="8%">添加门店</td>
			<td width="5%">添加人</td>
			<td width="5%">添加时间</td>
			<td width="5%">状态</td>
			<td width="8%">完成门店</td>
			<td width="5%">完成人</td>
			<td width="5%">完成时间</td>
        <td width="10%" style="line-height:22px;"><b>操作</b> <?php if($contract['is_completed']==0){?><a href="javascript:void(0);"  class="warrant_process_over" style="float:none;padding:4px 8px;-webkit-border-radius:3px;-ms-border-radius:3px;" <?php if($auth['warrant_complete_all']['auth']){?>onclick="window.parent.window.openWin('js_all_complete_pop');"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?> id='warrant_completed'>已办结</a><?php }?></td>
		    </tr>
		</thead>
        <tbody id="warrant_list">
		    <?php foreach($warrant_step as $key=>$val){?>
		    <tr class="resut_table_border qz_porcee " onclick="view_detail(<?=$val['id'];?>);">
                <td width="5%"><?=$stage_conf[$val['step_id']]['text'];?></td>
                <td width="8%"><?=$val['stage_name1'];?></td>
                <td width="5%"><?=$val['remark'];?></td>
                <td width="8%"><?=$val['agency_name'];?></td>
                <td width="5%"><?=$val['broker_name'];?></td>
                <td width="5%"><?=date('Y-m-d',$val['createtime']);?></td>
                <td width="5%"><?=$val['isComplete']==1?'<span class="qz_color_over">已完结</span>':'未完结'?></td>
                <td width="8%"><?=$val['complete_agency_name'];?></td>
                <td width="5%"><?=$val['complete_broker_name'];?></td>
                <td width="5%"><?=$val['complete_time'];?></td>
                <?php if($val['isComplete']==1){?>
                <td width="10%">
                    <span class="qz_color_over1">确认完成</span>|
                    <span class="qz_color_over1">修改</span>|
                    <span class="qz_color_over1">删除</span>
                </td>
                <?php }else{?>
                <td width="10%">
                    <a href="javascript:void(0);" <?php if($auth['warrant_complete']['auth']){?>onclick="window.parent.window.confirm_warrant_detail(<?=$val['id']?>);"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>确认完成</a>|
                    <a href="javascript:void(0);" <?php if($auth['warrant_edit']['auth']){?>onclick="warrant_detail(<?=$val['id']?>,<?=$contract['id'];?>)"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>修改</a>|
                    <a href="javascript:void(0);" <?php if($auth['warrant_delete']['auth']){?>onclick="open_warrant_delete(<?=$val['id'];?>,<?=$contract['id'];?>);"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>删除</a>
                </td>
                <?php }?>
		    </tr>
		    <?php }?>
		</tbody>
	    </table>
	    <?php }else{?>
	    <!--新建模板-->
	    <div class="qz_precess_add_modle">
		 <p>如需要使用模板，请选择模板。<a href="javascript:void(0)" <?php if($auth['warrant_add']['auth']){?>onclick="open_choose_template(<?=$contract['id'];?>);"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>选择模板</a></p>
         <p>如现有模板不能满足您的需要，您可以新建权证流程模板。<a href="javascript:void(0)" <?php if($auth['warrant_add']['auth']){?>onclick="window.parent.window.add_template_pop();"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>新建权证流程模版</a></p>
         <p>如您不想应用模板也可以自行定义本合同应用的权证流程。<a href="javascript:void(0)" <?php if($auth['warrant_add']['auth']){?>onclick="add_warrant();"<?php }else{?>onclick="window.parent.window.permission_none();"<?php }?>>新建权证步骤</a></p>
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
        $.post("/contract/warrant_detail",{id:id},function(data){
            if(data['warrant_list']['is_remind']==1){
                window.parent.document.getElementById('warrant').src = '/contract/contract_warrant_detail/'+id;
                window.parent.window.openWin('js_warrant_pop');
            }else{
                window.parent.document.getElementById('warrant1').src = '/contract/contract_warrant_detail/'+id;
                window.parent.window.openWin('js_warrant_pop1');
            }
        },"json");
    }
    function stage_detail(id){
        $.post("/contract/warrant_detail",{id:id},function(data){
            if(data['warrant_list']['is_remind']==1){
                window.parent.document.getElementById('warrant').src = '/contract/contract_warrant_detail/'+id;
                window.parent.window.openWin('js_warrant_pop');
            }else{
                window.parent.document.getElementById('warrant1').src = '/contract/contract_warrant_detail/'+id;
                window.parent.window.openWin('js_warrant_pop1');
            }
        },"json");
    }
    function open_choose_template(id){
        window.parent.document.getElementById('choose_template').src = '/contract/get_all_template/'+id;
        window.parent.window.openWin('js_temp_box');
    }


    function add_warrant(){
        window.parent.document.getElementById('addtemp').src = '/contract/modify_warrant_index/'+'<?=$contract['id'];?>'+"/";
        window.parent.window.openWin('js_addtemp_pop');
    }

    function warrant_detail(id,c_id){
        window.parent.document.getElementById('stage_id').value = id;
        window.parent.document.getElementById('addtemp').src = '/contract/modify_warrant_index/'+c_id+"/"+id;
        window.parent.window.openWin('js_addtemp_pop');
    }

    function open_warrant_delete(id){
        window.parent.document.getElementById('stage_id').value = id;
        window.parent.window.openWin('js_del_warrant');
    }

</script>

