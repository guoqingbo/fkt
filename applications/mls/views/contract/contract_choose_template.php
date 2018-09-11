<div class="achievement_money_pop qz_pop_W700"  style="width:840px;">
    <dl class="title_top">
        <dd><?=$page_title;?></dd>
    </dl>
    <form action="" method="post" name="search_form">
    <div  style="height:322px; overflow:hidden;overflow-y:auto; float:left;position:relative;width:100%;" class="zws_pop_li_del">
    <?php if($template_temps){foreach($template_temps as $key=>$val){?>
   <div class="qz_add_pop_item pop_padT">
        <span><?=$val['template_name'];?></span>
        <dl>
            <dd><input type="radio" class="qz_add_pop_item_radio" name="template_id" value="<?=$val['id'];?>"></dd>
            <dt style="width:96%;">
                <div class="warrant_process L0">
                    <ul>
                        <?php foreach($val['steps'] as $k =>$v){?>
                        <li class="warrant_process_bg4 srical_W120" style="padding-bottom:10px;">
                            <p title="<?=$v['stage_name1'];?>" class="stepHeight"><?=$v['stage_name2'];?> </p>
                        </li>
                        <li  style="padding-bottom:10px;">
                            <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/qz_process5_10.gif"  class="warrant_process_bg2">
                        </li>
                        <?php }?>
                    </ul>
                </div>
            </dt>
        </dl>
    </div>
    <?php }}?>
    </div>
    <div class="time_section_page money_pop_L700" style="display:inline; width:100%;">
        <div class="get_page">
            <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
        </div>
    </div>
    </form>
    <dl class="qz_prcess_btn money_pop_L330" style="    padding-left: 404px;">
        <input type="hidden" id="contract_id" value="<?=$c_id?>">
        <dd onclick="select_temp();">选择</dd>
    </dl>
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
		<p class="text" id="dialog_do_warnig_tip">请选择一个模板</p>
		<button type="button" class="btn-lv1 btn-mid JS_Close">确定</button>
	    </div>
	</div>
    </div>
</div>
<script>
    $(function(){
        $('.warrant_process').find('li .warrant_process_bg2').last().remove();

        $(".zws_pop_li_del .warrant_process").find("li:last-child").remove();
    })

    function select_temp(){
        var id = $("input[type='radio']:checked").val();
        if(id){
            $.ajax({
                url:"/contract/sel_choose",
                type:"POST",
                dataType:"json",
                data:{
                   template_id:id,
                   contract_id:$("#contract_id").val()
                },
                success:function(data){
                    if(data['status'] == 1){
                        closeParentWin('js_temp_box');
                        window.parent.frames["iframepage"].location=window.parent.frames["iframepage"].location;
                    }
                }
            })
        }else{
            openWin('js_pop_do_warning');
        }
    }
</script>

