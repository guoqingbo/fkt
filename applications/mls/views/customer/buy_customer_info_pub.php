<div class="pop_box_g pop_box_g_border_none" id="js_pop_box_g" style="display:block; border:none">
    <div class="hd">
        <div class="title">客源详情</div>
        <div class="close_pop"></div>
    </div>
    <div class="mod">
        <div class="tab_pop_hd">
            <dl class="clearfix" id="js_tab_t01">
                <dd class="js_t item itemOn" title="客源详情">客源详情</dd>
            </dl>
        </div>
        <div class="tab_pop_mod clear" id="js_tab_b01">
            <div class="js_d inner" style="display:block;">
                <?php if(is_array($data_info) && !empty($data_info)) {?>
                <table class="table">
                    <tr>
                        <!--<td class="w110 t_l">状态：</td>
                        <td class="w170" style="color:#F75000">
                        <?php
                            if(!empty($data_info['status']) && !empty($conf_customer['status'][$data_info['status']]))
                            {
                                if($conf_customer['status'][$data_info['status']] == '下架'){
                                    echo '注销';
                                }else{
                                    echo $conf_customer['status'][$data_info['status']];
                                }
                            }
                        ?>
                        </td>
                        <td class="w110 t_l">客源性质：</td>
                        <td class="w170" style="color:#F75000">
                            <?php
                            if(!empty($data_info['public_type']) && !empty($conf_customer['public_type'][$data_info['public_type']]))
                            {
                                echo $conf_customer['public_type'][$data_info['public_type']];
                            }
                            ?>
                        </td>
                        <td class="w110 t_l">客源编号：</td>
                        <td><?php echo get_custom_id($data_info['id']);?></td>
                    </tr>-->
                    <tr>
                        <?php
                        if(!empty($data_info['room_min']) && !empty($data_info['room_max']) )
                        {
                        ?>
                        <td class="w110 t_l">户型：</td>
                        <td class="w170" style="color:#F75000">
                        <?php
                         echo $data_info['room_min'].'-'.$data_info['room_max'].'室';
                        ?>
                        </td>
                        <?php }?>
                        <td class="w110 t_l">面积：</td>
                        <td class="w170" style="color:#F75000">
                        <?php
                        if(!empty($data_info['area_min']) && !empty($data_info['area_max']) )
                        {
                         echo strip_end_0($data_info['area_min']).'-'.strip_end_0($data_info['area_max']).'平方米';
                        }
                        ?>
                        </td>
                        <td class="w110 t_l">售价：</td>
                        <td style="color:#F75000">
                        <?php
                        if(!empty($data_info['price_min']) && !empty($data_info['price_max']) )
                        {
                         echo strip_end_0($data_info['price_min']).'-'.strip_end_0($data_info['price_max']).'万元';
                        }
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="w110 t_l">意向区属板块：</td>
                        <td  colspan="5" style="color:#F75000">
                        <?php 
                        $district_str = '';
                        if(!empty($data_info['dist_id1']) && isset($district_arr[$data_info['dist_id1']]['district']))
                        {
                            $district_str .= $district_arr[$data_info['dist_id1']]['district'];

                            if(!empty($data_info['street_id1']) && isset($street_arr[$data_info['street_id1']]['streetname']))
                            {
                                $district_str .= '-'.$street_arr[$data_info['street_id1']]['streetname'];
                            }  
                        }

                        if(!empty($data_info['dist_id2']) && isset($district_arr[$data_info['dist_id2']]['district']))
                        {
                            $district_str .= '&nbsp;，'.$district_arr[$data_info['dist_id2']]['district'];

                            if(!empty($data_info['street_id2']) && isset($street_arr[$data_info['street_id2']]['streetname']))
                            {
                                $district_str .= '-'.$street_arr[$data_info['street_id2']]['streetname'];
                            }  
                        }

                        if(!empty($data_info['dist_id3']) && isset($district_arr[$data_info['dist_id3']]['district']))
                        {
                            $district_str .= '&nbsp;，'.$district_arr[$data_info['dist_id3']]['district'];

                            if(!empty($data_info['street_id3']) && isset($street_arr[$data_info['street_id3']]['streetname']))
                            {
                                $district_str .= '-'.$street_arr[$data_info['street_id3']]['streetname'];
                            }
                        }
                        
                        echo $district_str;
                        ?>
                    </td>
                    </tr>
                    <tr>
                        <td class="w110 t_l">意向楼盘：</td>
                        <td  colspan="5" style="color:#F75000">
                        <?php 
                        if(isset($data_info['cmt_name1']) && $data_info['cmt_name1'] != '' )
                        {
                            echo $data_info['cmt_name1'];
                        }
                        
                        if(isset($data_info['cmt_name2']) && $data_info['cmt_name2'] != '' )
                        {
                            echo '，'.$data_info['cmt_name2'];
                        }

                        if(isset($data_info['cmt_name3']) && $data_info['cmt_name3'] != '')
                        {
                            echo '，'.$data_info['cmt_name3'];
                        }
                        ?>
                    </td>
                    </tr>
                    <tr>
                        <td class="w110 t_l">物业类型：</td>
                        <td style="color:#F75000">
                        <?php
                        if(isset($conf_customer['property_type'][$data_info['property_type']]) &&
                                $conf_customer['property_type'][$data_info['property_type']] != '')
                        {
                            echo $conf_customer['property_type'][$data_info['property_type']];
                        }
                        ?>
                        </td>
                        <td class="w110 t_l">是否合作：</td>
                        <td class="w170" style="color:#F75000">
                            <?php
                            if(isset($conf_customer['is_share'][$data_info['is_share']]) &&
                                    $conf_customer['is_share'][$data_info['is_share']] != '')
                            {
                                echo $conf_customer['is_share'][$data_info['is_share']];
                            }
                            ?>
                        </td>
                        <td class="w110 t_l">装修：</td>
                        <td>
                        <?php 
                            if(isset($conf_customer['fitment'][$data_info['fitment']]) && $conf_customer['fitment'][$data_info['fitment']] != '')
                            {
                                echo $conf_customer['fitment'][$data_info['fitment']];
                            }
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="w110 t_l">楼层：</td>
                        <td class="w170">
                        <?php
                        if(!empty($data_info['floor_min']) && !empty($data_info['floor_max']) )
                        {
                         echo $data_info['floor_min'].'-'.$data_info['floor_max'].'层';
                        }
                        ?>
                        </td>
                        <td class="w110 t_l">朝向：</td>
                        <td class="w170">
                        <?php 
                            if(isset($conf_customer['forward'][$data_info['forward']]) && 
                                    $conf_customer['forward'][$data_info['forward']] != '')
                            {
                                echo $conf_customer['forward'][$data_info['forward']];
                            }
                        ?>
                        </td>
                        <td class="w110 t_l">房龄：</td>
                        <td class="w170">
                            <?php
                            if(isset($conf_customer['house_age'][$data_info['house_age']]) &&
                                    $conf_customer['house_age'][$data_info['house_age']] != '')
                            {
                                echo $conf_customer['house_age'][$data_info['house_age']];
                            }
                            ?>
                        </td>
                    </tr>
                    <!--<tr>
                        <td class="w110 t_l">期限：</td>
                        <td class="w170">
                            <?php
                            if(isset($conf_customer['deadline'][$data_info['deadline']]) &&
                                    $conf_customer['deadline'][$data_info['deadline']] != '')
                            {
                                echo $conf_customer['deadline'][$data_info['deadline']];
                            }
                            ?>
                        </td>
                        <td class="w110 t_l">目的：</td>
                        <td class="w170">
                            <?php
                            if(isset($conf_customer['intent'][$data_info['intent']]) &&
                                    $conf_customer['intent'][$data_info['intent']] != '')
                            {
                                echo $conf_customer['intent'][$data_info['intent']];
                            }
                            ?>
                        </td>
                        <td class="w110 t_l">信息来源：</td>
                        <td>
                            <?php
                            if(isset($conf_customer['infofrom'][$data_info['infofrom']]) &&
                                    $conf_customer['infofrom'][$data_info['infofrom']] != '')
                            {
                                echo $conf_customer['infofrom'][$data_info['infofrom']];
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="w110 t_l">委托时间：</td>
                        <td><?php echo date('Y-m-d H:i:s',$data_info['creattime']);?></td>
                        <td class="w110 t_l">跟进时间：</td>
                        <td colspan="3"><?php echo date('Y-m-d H:i:s',$data_info['updatetime']);?></td>
                    </tr>
                    <tr>
                        <td class="w110 t_l">备注：</td>
                        <td colspan="5">
                            <?php
                            if(isset($data_info['infofrom']) &&
                                    $data_info['infofrom'] != '')
                            {
                                echo $data_info['remark'];
                            }
                            ?>
                        </td>
                    </tr>-->
                    <tr class="clear-line">
                        <td class="w110 t_l">委托经纪人：</td>
                        <td class="w170">
                        <?php echo $data_info['broker_name'];?>
                        </td>
                        <td class="w80 t_l">委托门店：</td>
                        <td class="w170">
                        <?php echo $data_info['agency_name'];?>
                        <?php if(!empty($data_info['company_name'])){
                            echo '('.$data_info['company_name'].')';
                        }?>
                        </td>
                        <td class="w80 t_l">联系电话：</td>
                        <td class="w170">
                        <?php if($is_phone_show){?>
                            <?php echo $data_info['broker_phone'];?>
                        <?php }else{?>
                        <span style="color:red;">提交申请后方能查看</span>
                        <?php }?>
                        </td>
					</tr>
                </table>
                <?php }else{ ?>
                <table class="table"><tr><td colspan="6">很遗憾，没有找到相关求购信息。</td></table>
                <?php } ?>
            </div>
        </div>
        <div class="tab_pop_bd">
        <?php if(is_array($data_info) && !empty($data_info)) { ?>
            <div>
                <?php if( isset($collected_ids) && in_array($data_info['id'] , $collected_ids)){ ?>
                    <a class="collect-btn collect-success" href="javascript:void(0);" >已收藏</a>
                <?php }else{?>
                    <a class="collect-btn collect" href="javascript:void(0);"  id="collect_info<?php echo $data_info['id'];?>" onclick="collect_customer(<?php echo $data_info['id'];?>,'buy_customer','info')">收藏房源</a>
                <?php } ?>
            </div>
            <div>
                <a class="btn-lan" href="javascript:void(0);" onclick="parent.open_match_customer('customer',1,<?php echo $data_info['id'];?>)"><span>智能匹配</span></a> 
                <?php if('1'==$open_cooperate){?>
                <a class="btn-lan" href="javascript:void(0);" onclick="parent.cooperate_customer('buy_customer',<?php echo $data_info['id'];?>);"><span>合作申请</span></a>
                <?php }?>
            </div>
        <?php }?>
        </div>
    </div>
</div>

<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>