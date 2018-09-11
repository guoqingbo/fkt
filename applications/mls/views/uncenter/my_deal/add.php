<style>
input.error {
    border: 1px solid red;
}
 .error {
    color: #e92e2e;
}
</style>
<div class="pop_box_g pop_see_inform pop_see_info_deal pop_add_info_deal" id="js_pop_add_info_house"  style="height:448px;">
    <div class="hd">
        <div class="title">成交房源详情</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod mod_bg" style="padding-right:0;">
            <div class="inform_inner" style="overflow-x:hidden; overflow-y:scroll; height:412px;">


            <table class="deal_table deal_table_see">
                <tr>
                    <th>区属：</th>
                    <td><?php echo $data_info['district_name'];?></td>
                    <th>板块：</th>
                    <td><?php echo $data_info['street_name'];?></td>
                    <th>楼盘：</th>
                    <td><?php echo $data_info['block_name'];?></td>
                </tr>
                <tr>
                    <th>楼盘地址：</th>
                    <td><?php echo $data_info['address'];?></td>
                    <th>用途：</th>
                    <td><?php echo $config['sell_type'][$data_info['sell_type']]; ?></td>
                    <th>类型：</th>
                    <td>
                        <?php 
                            if($data_info['sell_type'] == 2 && $data_info['villa_type']){
                                echo $config['villa_type'][$data_info['villa_type']];
                            }elseif($data_info['sell_type'] == 3 && $data_info['shop_type']){
                                echo $config['shop_type'][$data_info['shop_type']];
                            }elseif($data_info['sell_type'] == 4 && $data_info['office_type']){
                                echo $config['office_type'][$data_info['office_type']];
                            }elseif($data_info['sell_type'] == 1 && $data_info['house_type']){
                                echo $config['house_type'][$data_info['house_type']];
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <?php if($type == "sell"){?>
                    
                    <th>总价：</th>
                    <td><?php echo strip_end_0($data_info['price']); ?>万</td>
                    <th>单价：</th>
                    <td><?php echo strip_end_0($data_info['avgprice']); ?>元/㎡</td>
                    
                    <?php }elseif($type == "rent"){?>
                    
                    <th>租金：</th>
                    <td><?php echo strip_end_0($data_info['price']); ?>元/月</td>
                    <th>委托类型：</th>
                    <td><?php echo $config['rententrust'][$data_info['rententrust']]; ?></td>
                    
                    <?php }?>
                    
                    <th>物业费：</th>
                    <td>
                        <?php 
						if($data_info['strata_fee']){
							echo strip_end_0($data_info['strata_fee']);
						}
						?><?php if($data_info['sell_type']==1){echo "元/月/m²";}else{echo "元/月";}?>
                    </td>
                </tr>

                <tr>
                    <th>朝向：</th>
                    <td><?php echo $config['forward'][$data_info['forward']]; ?></td>
                    <th>户型：</th>
                    <td><?php echo $data_info['room'];?>室<?php echo $data_info['hall'];?>厅<?php echo $data_info['toilet'];?>卫<?php echo $data_info['kitchen'];?>厨<?php echo $data_info['balcony'];?>阳台</td>
                    <th>面积：</th>
                    <td><?php echo strip_end_0($data_info['buildarea']); ?>㎡</td>
                </tr>

                <tr>
                    <th>楼层：</th>
                    <td><?php echo $data_info['floor'];?><?php if($data_info['floor_type'] == 2){ echo "-".$data_info['subfloor'];}?>/<?php echo $data_info['totalfloor'];?></td>
                    <th>装修：</th>
                    <td><?php echo $config['fitment'][$data_info['fitment']]; ?></td>
                    <th>发布时间：</th>
                    <td><?php echo date('Y.m.d',$data_info['createtime']);?></td>
                </tr>
                <tr>
                    <th>建筑年代：</th>
                    <td><?php echo $data_info['buildyear']; ?></td>
                    <th>联系人：</th>
                    <td><?php echo $data_info['owner']; ?></td>
                    <th>电话：</th>
                    <td><?php echo $data_info['telnos']; ?></td>
                </tr>
                <tr>
                    <th valign="top">备注：</th>
                    <td colspan="5" ><?php echo $data_info['remark'];?></td>
                </tr>

            </table>
            <hr style="background: #e6e6e6;">
            <form method='post' action='' id='add_form'>
                <input type='hidden' name='submit_flag' value='add'/>
            <table class="deal_table deal_table_see" style="height:60px;">
                <tr>
                    <th valign="top">价格：</th>
                    <td valign="top"><input class="" type="text" name="price" style="width:80px;" /> 万<span class="error"></span></td>
                    <th valign="top">姓名：</th>
                    <td valign="top"><input class="" type="text" name="name" /><div class="error"></div></td>
                    <th valign="top">电话：</th>
                    <td valign="top"><input class="" type="text" name="tel" /><div class="error"></div></td>
                </tr>
            </table>
            <div class="clear">&nbsp;</div>
            <div class="m_bd">
                <button class="btn-lv1 btn-left" type="submit">添加</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
            </form>

        </div>

    </div>
</div>
<script>
$(function() {
    openWin('js_pop_add_info_house');
    $.validator.addMethod("isZWNo",function(value,element,params){
        var reg = /[\u0391-\uFFE5]/ ;
        if(!reg.test(value))  
        {  
            return  true; 
        }  
    },"电话不能有中文");

    $.validator.addMethod("isZMNo",function(value,element,params){
        var reg = /(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/;
        if(reg.test(value))  
        {  
            return true;
        }  
    },"电话只能包含7-13位数字和中划线&nbsp;");
    //还业主操作验证
    $("#add_form").validate({
        errorPlacement: function(error, element) {
        error.appendTo(element.siblings(".error"));
        },
        rules:{
            price:{
                required:true,
                number:true,
                min:1
            },
            name:{
                required: true
            },
            tel:{
                required: true,
                isZMNo:true,
                isZWNo:true
            }

        },
        messages:{
            price:{
                required:'请填写价格',
                number:'总价必须为数字',
                min:'总价不能小于1'
            },
            name:{
                required: "请填写姓名"
            },
            tel:{
                required: "请填写电话号码"
            }	
        }
    });
});
</script>

