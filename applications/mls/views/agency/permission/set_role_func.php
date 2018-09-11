<div class="pop_box_g pop_box_add_shop pop_modification_role" id="js_modification_role" style="display: block;border:none;">
    <div class="hd">
        <div class="title">修改</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
        <div class="tab_pop_hd">
            <dl id="js_tab_t01" class="clearfix">
                <?php foreach ($module as $key => $val) { ?>
                <dd title="<?php echo  $val['name'];?>" class="js_t item<?php if($key == 0){echo " itemOn";}?>"><?php echo  $val['name'];?></dd>
                <?php } ?>
            </dl>
        </div>
        <div id="js_tab_b01" class="tab_pop_mod tab_pop_mod_shop clear">
            <iframe name="jQuerySaveRoleFuncIframe" style="display:none;"></iframe>
            <form name="search_form" method="post" action="/permission/save_role_func"  target="jQuerySaveRoleFuncIframe">
            <input type='hidden' name='role_id' value='<?php echo $role_id;?>' id="role_id"/>    

            <?php foreach ($module as $key => $val) { ?>
            <div <?php if($key == 0){echo "style='display:block;'";}?> class="js_d inner role_new_inner_box">
                <div class="role_new_inner">
                    <h5 class="h5">
                        <label class="label">
                            <input type="checkbox" name="module[]" value="<?php echo $val['id'];?>" <?php if(in_array($val['id'],$role_module)){echo "checked='checked'";}?> ><?php echo  $val['name'];?>
                        </label>
                    </h5>
                    
                    <?php foreach ($val['menu'] as $k => $v) { ?>
                    <div class="item js_role_new_item">
                        <table class="table">
                            <tr>
                                <th class="th">
                                    <label>
                                        <input type="checkbox" name="menu[<?php echo $val['id'];?>][]" value="<?php echo $v['id'];?>" <?php if(in_array($v['id'],$role_menu)){echo "checked='checked'";}?> ><?php echo  $v['name'];?>
                                    </label>
                                </th>
                                <td class="td">
                                    <dl class="list clearfix">
                                        
                                        <?php foreach ($v['func'] as $k1 => $v1) { ?>
                                        <dd class="dd">
                                            
                                            <?php if($v1['is_area']) { ?>
                                            <label class="i_label">
                                                <input class="js_role_checkbox" type="checkbox" name="" value="<?php echo isset($role_func2[$v1['id']])?$role_func2[$v1['id']]:0;?>" <?php if(in_array($v1['id'],$role_func1)){echo "checked='checked'";}?> ><?php echo  $v1['name']?>
                                            </label>
                                            <div class="js_h_div h_div">
                                                <?php foreach ($v1['area_arr'] as $k2 => $v2) { ?>
                                                <label class="i_lable">
                                                    <input type="radio" name="func[<?php echo $v['id'];?>][<?php echo $v1['id'];?>]" value="<?php echo $v2;?>"  <?php if( isset($role_func2[$v1['id']]) && $v2 == $role_func2[$v1['id']] ){echo "checked='checked'";}?> >
                                                    <?php if($v2 == 1) {echo "本人";}elseif($v2 == 2){echo "门店";}elseif($v2 == 3){echo "公司";}?>
                                                </label>
                                                <?php } ?>	
                                                <div class="iconfont js_c" title="关闭">&#xe60c;</div>
                                                <div class="sj"></div>
                                            </div>
                                            <?php }else{ ?>
                                            <label class="i_label">
                                                <input type="checkbox" name="func[<?php echo $v['id'];?>][<?php echo $v1['id'];?>]" value="1"  <?php if(in_array($v1['id'],$role_func1)){echo "checked='checked'";}?> ><?php echo  $v1['name']?>(本人)
                                            </label>
                                            <?php } ?>
                                            
                                        </dd>
                                        <?php } ?>
                                        
                                    </dl>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php } ?>
                    
                </div>
            </div>
            <?php } ?>

            <button class="btn-lv1 btn-mid" style="margin-top:20px; margin-bottom:20px;" type="submit">保存</button>
            </form>
        </div>
    </div>
</div>
<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">设置权限成功！</p>
				<button type="button" class="btn-lv1 btn-left JS_Close">确定</button>
			</div>
		</div>
	</div>
</div>
<div id="js_pop_do_warning"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_warnig_tip"></p>
			</div>
		</div>
	</div>
</div>
<script>
function save_role_func_result(result)
{
	if (result == 0)
	{
		$('#dialog_do_warnig_tip').html('设置权限失败！');
		openWin('js_pop_do_warning');
	}
	else if (result == 1)
	{
		openWin('js_pop_do_success');
	}
	else if (result == 2)
	{
		$('#dialog_do_warnig_tip').html('没有被修改的角色！');
		openWin('js_pop_do_warning');
	}
}
</script>