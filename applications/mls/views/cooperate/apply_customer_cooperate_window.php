<body>
<div class="pop_box_g hzsq_pop_box pop_box_g_border_none" id="" style="display:block; border:0;">
    <div class="hd">
        <div class="title">合作申请</div>
    </div>
    <div class="mod"  style="height:440px; overflow-x: hidden; overflow-y: scroll;">
        <p class="b_title">申请合作客源</p>
        <div class="hzsq_mod">
            <?php if(is_array($customer_info) && !empty($customer_info)) { ?>
            <table class="table" style="width:450px;">
                <tr>
                    <td class="td">客户编号：<?php echo format_info_id($customer_info['id'] , $customer_kind);?></td>
                    <td> 户型：<?php echo $customer_info['room_min'];?>-<?php echo $customer_info['room_max'];?>室</td>
                </tr>
                <tr>
                    <td class="td">面积：
                        <?php echo strip_end_0($customer_info['area_min']);?>
                        -
                        <?php echo strip_end_0($customer_info['area_max']);?>㎡
                    </td>
                    <td> 价格：
                        <?php echo ('1'==$customer_info['price_danwei'])?strip_end_0($customer_info['price_min']/$customer_info['area_min']/30):strip_end_0($customer_info['price_min']);?>
                        -
                        <?php echo ('1'==$customer_info['price_danwei'])?strip_end_0($customer_info['price_max']/$customer_info['area_max']/30):strip_end_0($customer_info['price_max']); ?>
                        <?php if($customer_kind == 'buy_customer'){ ?>
                        万
                        <?php }else if($customer_kind == 'rent_customer'){
                        echo ('1'==$customer_info['price_danwei'])?'元/㎡*天':'元/月';
                        }?>
                    </td>
                </tr>
                <tr>
                    <td class="td" colspan="2">意向区属板块：
                    <?php
                    if($customer_info['dist_id1'] > 0 && isset($district_arr[$customer_info['dist_id1']]['district']))
                    {
                        echo $district_arr[$customer_info['dist_id1']]['district'];
                        if( $customer_info['street_id1'] > 0 && !empty($street_arr[$customer_info['street_id1']]['streetname']))
                        {
                            echo  '-'.$street_arr[$customer_info['street_id1']]['streetname'];
                        }
                    }

                    if($customer_info['dist_id2'] > 0 && isset($district_arr[$customer_info['dist_id2']]['district']))
                    {
                        echo '，'.$district_arr[$customer_info['dist_id2']]['district'];
                        if( $customer_info['street_id2'] > 0 && !empty($street_arr[$customer_info['street_id2']]['streetname']))
                        {
                            echo  '-'.$street_arr[$customer_info['street_id2']]['streetname'];
                        }
                    }

                    if($customer_info['dist_id2'] > 0 && isset($district_arr[$customer_info['dist_id3']]['district']))
                    {
                        echo '，'.$district_arr[$customer_info['dist_id3']]['district'];
                        if( $customer_info['street_id3'] > 0 && !empty($street_arr[$customer_info['street_id3']]['streetname']))
                        {
                            echo  '-'.$street_arr[$customer_info['street_id3']]['streetname'];
                        }
                    }
                    ?>
                    </td>
                </tr>
                <tr>
                    <td class="td" colspan="2">意向楼盘：
                    <?php
                    if(isset($customer_info['cmt_name1']) && $customer_info['cmt_name1'] != '' )
                    {
                        echo $customer_info['cmt_name1'];
                    }

                    if(isset($customer_info['cmt_name2']) && $customer_info['cmt_name2'] != '' )
                    {
                        echo '，'.$customer_info['cmt_name2'];
                    }

                    if(isset($customer_info['cmt_name3']) && $customer_info['cmt_name3'] != '')
                    {
                        echo '，'.$customer_info['cmt_name3'];
                    }
                    ?>
                    </td>
                </tr>
            </table>
            <?php } else { ?>
               <table class="table" style="width:450px;">
					<tr>
						<td class="td" colspan="2">没查到相关客源信息！</td>
					</tr>
				</table>
            <?php } ?>
        </div>
        <?php if(is_array($house_list) && !empty($house_list)){ ?>
         <p class="b_title">请选择一条匹配房源</p>
          <div class="hzsq_mod">
          <table class="table" style="width:450px;">
          <?php foreach($house_list as $key => $value) { ?>
            <tr>
                <td class="td" colspan="2">
                <input type="radio" name="rowid" value="<?php echo $value['id'];?>">
                <?php echo format_info_id( $value['id'], $tbl);?>
                <?php echo $district_arr[$value['district_id']]['district'];?>-<?php echo $street_arr[$value['street_id']]['streetname'];?>
                <?php echo $value['block_name'];?>
                <?php echo $value['room'];?>室<?php echo $value['hall'];?>厅<?php echo $value['toilet'];?>卫
                <?php echo strip_end_0($value['buildarea']);?>㎡ <?php echo strip_end_0($value['price']);?>
                <?php echo $tbl == 'sell' ? '万' : '元/月';?>
                </td>
            </tr>
            <?php } ?>
          </table>
          </div>
         <input type="hidden" name ="broker_a_id" id="broker_a_id" value ="<?php echo $customer_info['broker_id'];?> ">
         <input type="hidden" name ="tbl" id="tbl" value ="<?php echo $tbl;?>">
         <input type="hidden" name ="customer_id" id="customer_id" value ="<?php echo $customer_info['id'];?>">
         <input type="hidden" name ="apply_type" id="apply_type" value ="2">
         <button type="button" class="btn-lv1 btn-mid" onclick="showParentPop();return false;">确定</button>
          <?php }else{?>
            <div class="tips_text">
            <p class="t"> 很遗憾，您暂时还没有符合条件的合作房源哦！您可以</p>
            <p class="p">1.设置符合匹配条件的房源为合作房源。 <a href="<?php echo MLS_URL;?>/<?php echo $tbl;?>/lists" target="_parent">去设置合作房源&gt;&gt;</a></p>
             <p class="p">2.发布一条符合匹配条件的房源并且设置合作。 <a href="<?php echo MLS_URL;?>/<?php echo $tbl;?>/publish" target="_parent">去发布房源&gt;&gt;</a></p>
            </div>
          <?php } ?>
    </div>
</div>

<!--操作结果弹出警告-->
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_warnig_tip'></p>
            </div>
        </div>
    </div>
</div>
<script>
function showParentPop()
{
    var broker_a_id = $('#broker_a_id').val();
    var rowid = $("input[name='rowid']:checked").val();
    var customer_id = $('#customer_id').val();
    var apply_type = $('#apply_type').val();
    var tbl = $('#tbl').val();

    if( rowid == '' || typeof(rowid) == 'undefined' )
    {
        $("#dialog_do_warnig_tip").html("请选择要合作的房源！");
        openWin('js_pop_do_warning');

        return false;
    }

    window.parent.show_customer_cooperate(tbl , rowid , broker_a_id , customer_id , apply_type);
}
</script>
