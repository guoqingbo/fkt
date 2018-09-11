<div class="pop_box_g" id="js_pop_box_g"  style="display:block; border:none">
    <div class="hd">
        <div class="title">配置发布房源字段</div>
        <div class="close_pop"></div>
    </div>
    <div class="mod">
        <div class="tab_pop_hd">
            <div class="clearfix">
                <?php foreach ($sellTypes as $k => $v) { ?>
                <a class="item <?php if ($sell_type == $k) {echo 'itemOn';} ?>" href="/house/set_field/<?php echo $k; ?>"><?php echo $v; ?></a>
                <?php } ?>
            </div>
        </div>
        <div class="tab_pop_mod clear" id="js_tab_b01">
            <div class="js_d inner" style="display:block;">
            <form id="form1">
                <table class="table">
                <?php foreach ($lists as $v) { ?>
                    <tr>
                        <td class="w70 t_l"><?php echo $v['field_value']; ?></td>
                        <td class="w170" style="color:#F75000">
                            <input type="hidden" name="agency_id[<?php echo $v['id']; ?>]" value="<?php echo $v['agency_id']; ?>"/>
                            <input type="hidden" name="field_name[<?php echo $v['id']; ?>]" value="<?php echo $v['field_name']; ?>"/>
                            <input type="hidden" name="field_value[<?php echo $v['id']; ?>]" value="<?php echo $v['field_value']; ?>"/>
                        </td>
                        <td class="w70 t_l" >是否显示：</td>
                        <td class="w170" style="color:#F75000">
                            <select class="select" name="display[<?php echo $v['id']; ?>]">
                                <option value="1" <?php if (1 == $v['display']) echo 'selected="selected"'; ?>>显示</option>
                                <option value="0" <?php if (0 == $v['display']) echo 'selected="selected"'; ?>>隐藏</option>
                            </select>
                        </td>
                        <td class="w70 t_l">是否必选：</td>
                        <td style="color:#F75000">
                            <select class="select" name="required[<?php echo $v['id']; ?>]">
                                <option value="1" <?php if (1 == $v['required']) echo 'selected="selected"'; ?>>是</option>
                                <option value="0" <?php if (0 == $v['required']) echo 'selected="selected"'; ?>>否</option>
                            </select>
                        </td>
                    </tr>
                <?php } ?>
				</table>
            </form>
            </div>
        </div>

        <div class="tab_pop_bd">
            <div>
                <a id="edit_field" class="btn-lan" href="javascript:void(0);"><span>提交</span></a>
            </div>
        </div>
    </div>
</div>
<style>
.pop_box_g .tab_pop_hd .item{
    width: 97px;
}
</style>

<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>

<script>
$(function(){
    $('#edit_field').click(function(){
        $("#dialogSaveDiv").html('确定要提交吗？');
        openWin('jss_pop_tip');
        $("#dialog_share").click(function(){
            $.ajax({
                url: "/house/save_field/<?php echo $sell_type; ?>",
                type: "POST",
                dataType: "json",
                data: $('#form1').serialize(),
                success: function(data) {
                    //alert(data);
                    if (data['errorCode'] == '401')
                    {
                        login_out();
                        return false;
                    }
                    else if(data['errorCode'] == '403')
                    {
                        permission_none();
                        return false;
                    }

                    if (data.error == 0) {
                        window.parent.location.reload();
                    } else {
                        $("#dialog_do_warnig_tip").html(data.msg);
                        openWin('js_pop_do_warning');
                    }
                }
            });
        });
    })
});
</script>
