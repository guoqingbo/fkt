<div class="pop_box_g pop_box_g_border_none" id="js_pop_box_g" style="display:block;">
    <div class="hd">
        <div class="title">客源详情</div>
        <div class="close_pop"><!--<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a>--></div>
    </div>
    <div class="mod">
        <div class="tab_pop_hd">
            <dl class="clearfix" id="js_tab_t01">
                <a class="item <?php if($tab == 1) { ?> itemOn <?php }?>" href="<?php echo MLS_URL;?>/rent_customer/customer_detail/<?php echo $customer_id;?>/3/1/<?php echo $hide_btn;?>">客源详情</a>
                <a class="item <?php if($tab == 2) { ?> itemOn <?php }?>" href="<?php echo MLS_URL;?>/rent_customer/confidential_info/<?php echo $customer_id;?>/3/2/<?php echo $hide_btn;?>">保密信息</a>
                <a class="item <?php if($tab == 3) { ?> itemOn <?php }?>" href="<?php echo MLS_URL;?>/rent_customer/cooperation/<?php echo $customer_id;?>/3/3/<?php echo $hide_btn;?>">合作统计</a>
            </dl>
        </div>
        <div class="tab_pop_mod clear" id="js_tab_b01">
        <?php if($tab == 1) { ?>
            <div class="js_d inner" style="display:block;">
                <?php if(is_array($data_info) && !empty($data_info)) {?>
                <table class="table">
                    <tr>
                        <td class="w110 t_l">状态：</td>
                        <td class="w170"style="color:#F75000">
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
                        <td><?php echo get_custom_id($data_info['id'],'rent');?></td>
                    </tr>
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
                        <td class="w110 t_l">租金：</td>
                        <td style="color:#F75000">
                        <?php
                        if(!empty($data_info['price_min']) && !empty($data_info['price_max']) )
                        {
                            if('1'==$data_info['price_danwei']){
                                echo strip_end_0($data_info['price_min']/$data_info['area_min']/30).'-'.strip_end_0($data_info['price_max']/$data_info['area_max']/30);
                            }else{
                                echo strip_end_0($data_info['price_min']).'-'.strip_end_0($data_info['price_max']);
                            }
                         echo ('1'==$data_info['price_danwei'])?'元/㎡*天':'元/月';
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
                    <tr>
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
                        <td class="w110 t_l">租赁期限：</td>
                        <td>
                        <?php
                            if(isset($conf_customer['lease'][$data_info['lease']]) &&
                                    $conf_customer['lease'][$data_info['lease']] != '')
                            {
                                echo $conf_customer['lease'][$data_info['lease']];
                            }else{
                                echo '不限';
                            }
                        ?>
                        </td>
                        <td class="w110 t_l">付款方式：</td>
                        <td colspan="3">
                        <?php
                            if(isset($conf_customer['rent_payment'][$data_info['payment']]) &&
                                    $conf_customer['rent_payment'][$data_info['payment']] != '')
                            {
                                echo $conf_customer['rent_payment'][$data_info['payment']];
                            }
                        ?>
                        </td>
                    </tr>
				</table>
				<table class="table">
                    <tr>
                        <td class="w110 t_l">备注：</td>
						<td><div class="ie-break-all">
                            <?php
                            if(isset($data_info['infofrom']) &&
                                    $data_info['infofrom'] != '')
                            {
                                echo $data_info['remark'];
                            }
                            ?>
						</div></td>
                    </tr>
				</table>
				<table class="table">
                    <tr class="clear-line">
                        <td class="w110 t_l">委托经纪人：</td>
                        <td class="w170">
                        <?php echo $data_info['broker_name'];?>
                        </td>
                        <td class="w110 t_l">委托门店：</td>
                        <td class="w170">
                        <?php echo $data_info['agency_name'];?>
                        <?php
                            if(!empty($data_info['company_name'])){
                                echo '('.$data_info['company_name'].')';
                            }
                        ?>
                        </td>
                        <td class="w110 t_l">联系电话：</td>
                        <td class="w170">
                        <?php echo $data_info['broker_phone'];?>
                        </td>
					</tr>
                </table>
                <?php }else{ ?>
                <table class="table"><tr><td colspan="6">很遗憾，没有找到相关求购信息。</td></table>
                <?php } ?>
            </div>
        <?php } else if($tab == 2) {?>
            <div class="js_d inner inner02" style="display:block">
                <div class="t_box">
                    <?php if(is_array($data_info) && !empty($data_info)) {?>
                    <table class="table" id="js_table_ys">
                        <tr>
                            <td class="w110 t_l">客户姓名：</td>
                            <td class="w170">
                                <strong class="color js_y_hide" id="truename">***</strong>
                            </td>
                            <td class="w110 t_l">客户电话：</td>
                            <td class="w170">
                                <strong class="color js_y_hide" id="telno">***</strong>
                            </td>
                            <td class="w110 t_l">身份证号：</td>
                            <td class="w170" id="idno">***</td>
                        </tr>
                        <tr>
                            <td class="w110 t_l">客源职业：</td>
                            <td class="w170" id="job_type">***</td>
                            <td class="w110 t_l">客源等级：</td>
                            <td class="w170" id="user_level">***</td>
                            <td class="w110 t_l">年龄：</td>
                            <td id="age_group">***</td>
                        </tr>
                        <tr>
                            <td class="w110 t_l">联系地址：</td>
                            <td colspan="5" id="address">***</td>
                        </tr>
                    </table>
                    <?php }else{ ?>
                    <table class="table"><tr><td colspan="6">很遗憾，没有找到相关求购信息。</td></table>
                    <?php } ?>
                    <p class="link_btn_b">
                        <?php if( $data_info['lock']==0 || in_array( $broker_id , array($data_info['broker_id'],$data_info['lock']) ) ){ ?>
                        <a onClick="rent_show_ys_inner(<?php echo $data_info['id'];?>)" href="javascript:void(0)"><span class="iconfont">&#xe60f;</span> 查看保密信息</a>
                        <?php }else{ ?>
                        很遗憾，您无权查看相关保密信息。
                        <?php } ?>
                    </p>
                </div>
                <div class="clearfix pop_fg_fun_box">
                    <div class="text left"><span class="fg">总查阅次数：<a href="#"><?php echo $user_num;?></a></span><span class="fg">今日查阅次数：<a href="#"><?php echo $today_brower_all_num;?></a></span></div>
                    <div class="get_page">
                        <input type="hidden" name="pg" value="1"/>
                        <?php if($pages > 1){?>
                        <span><strong id='thispage'><?php echo $page;?></strong>/<?php echo $pages;?>页</span>
                        <a href="javascript:void(0)" id="prev_page">上一页</a>
                        <a href="javascript:void(0)" id='next_page'>下一页</a>
                        <?php }?>
                        <span>共<?php echo $group_by_num;?>条</span>
                    </div>
                </div>
                <div class="table_list_box">
                    <table class="table_list" id='table_list'>
                        <tr>
                            <th class="w130">最近查阅时间</th>
                            <th class="w170">查阅门店</th>
                            <th class="w60">查阅人</th>
                            <th class="w90">总查阅次数</th>
                            <th class="w90">今日查阅次数</th>
                            <th>初次查阅时间</th>
                        </tr>
                        <?php foreach($brower_list2 as $k => $v){?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i:s',$v['recent_brower']);?></td>
                            <td><?php echo $v['agency_name'];?></td>
                            <td><?php echo $v['broker_name'];?></td>
                            <td><?php echo $v['brower_num'];?></td>
                            <td><?php echo $v['today_brower_num'];?></td>
                            <td><?php echo date('Y-m-d H:i:s',$v['first_brower']);?></td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
            </div>
        <?php  }else if($tab == 3) {?>
            <div class="js_d inner inner02" style="display:block">
                <div class="hz_inner_info">
                    <div class=" clearfix">
                        <div class="title left"><span class="fg">本客源被查看次数：<?php echo $view_num;?></span>本客源被查看人数：<?php echo $view_people;?></div>
                        <?php if($pages>0){?>
                        <div class="get_page">
                            <span><strong id='thispage'><?php echo $page;?></strong>/<?php echo $pages;?>页</span>
                            <input type="hidden" name="pg" value="1"/>
                            <?php if($pages!=1){?>
                            <a href="javascript:void(0)" id="prev_page">上一页</a><a href="javascript:void(0)" id='next_page'>下一页</a>
                            <?php }?>
                            <span>共<?php echo $log_num;?>条</span>
                        </div>
                        <?php }?>
                    </div>
                    <div class="info">
                        <div class="list">
                            <table class="table" id="table_list">
                                <tr>
                                    <th class="w90">查看人</th>
                                    <th class="w200">所属门店</th>
                                    <th class="w160">联系方式</th>
                                    <th class="w70">查看次数</th>
                                    <th>最近查看时间</th>
                                </tr>
                                <?php if( $view_people > 0){?>
                                    <?php foreach($view_log_list as $key => $value){ ?>
                                    <tr <?php if($key%2 == 0) {?> class="bg"<?php }?>>
                                        <td><?php echo $value['broker_name_v'];?></td>
                                        <td><?php echo $value['agency_name_v'];?></td>
                                        <td><?php echo $value['broker_telno_v'];?></td>
                                        <td><?php echo $value['num'];?></td>
                                        <td><?php echo date('Y-m-d H:i:s',$value['datetime']);?></td>
                                    </tr>
                                    <?php }?>
                                <?php }else {?>
                                    <tr  class="bg"><td colspan="5">暂无数据</td></tr>
                                <?php }?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="hz_inner_info">
                    <div class="title"></span>本客源被申请合作人数：<?php echo count($cooperate_log_list);?></div>
                    <?php if($cooperate_pages>0){?>
                    <div class="get_page">
                        <span><strong id='cooperate_thispage'><?php echo $cooperate_page;?></strong>/<?php echo $cooperate_pages;?>页</span>
                        <input type="hidden" name="cooperate_pg" value="1"/>
                        <?php if($cooperate_pages!=1){?>
                        <a href="javascript:void(0)" id="cooperate_prev_page">上一页</a><a href="javascript:void(0)" id='cooperate_next_page'>下一页</a>
                        <?php }?>
                        <span>共<?php echo $cooperate_num;?>条</span>
                    </div>
                    <?php }?>
                    <div class="info">
                        <div class="list">
                            <table class="table" id="cooperate_table_list">
                                <tr>
                                    <th class="w90">申请人</th>
                                    <th class="w200">所属门店</th>
                                    <th class="w160">联系方式</th>
                                    <th class="w170">申请时间</th>
                                    <th>状态</th>
                                </tr>
                                <?php if( is_array($cooperate_log_list) && !empty($cooperate_log_list) ){?>
                                    <?php foreach ($cooperate_log_list as $key => $value ){?>
                                    <tr class="bg">
                                        <td><?php echo $value['broker_name_b'];?></td>
                                        <td><?php echo $value['agent_a_name'];?></td>
                                        <td><?php echo $value['phone_b'];?></td>
                                        <td><?php echo date('Y-m-d H:i:s',$value['creattime']);?></td>
                                        <td><strong class="s_z"><?php echo $cooperate_conf['esta'][$value['esta']];?></strong></td>
                                    </tr>
                                   <?php }?>
                                <?php }else {?>
                                    <tr  class="bg"><td colspan="5">暂无数据</td></tr>
                                <?php }?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
        </div>
    </div>
</div>

<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>
<script type="text/javascript">
var tab = '<?php echo $tab;?>';
var ajax_url = '/rent_customer/ajax_get_brower_log/';
function ajax_submit(method,page,cunstomerid,is_cooperate)
{
    if(tab == 2)
    {
        ajax_url = '/rent_customer/ajax_get_brower_log/';
    }
    else if(tab==3)
    {
        if('cooperate'==is_cooperate)
        {
            ajax_url = '/rent_customer/ajax_get_cooperate_log/';
        }
        else
        {
            ajax_url = '/rent_customer/ajax_get_customer_view_log/';
        }
    }

    var submit_data = {};
    if('next_page'==method){
        submit_data.pg = parseInt(page)+1;
    }else if('prev_page'==method){
        submit_data.pg = parseInt(page)-1;
    }else if('to_page'==method){
        submit_data.pg = page;
    }
    $.ajax({
        url: ajax_url+cunstomerid,
        type: 'GET',
        data: submit_data,
        dataType: 'JSON',
        success:function(return_data){
            if(tab==2){
                var html_str = '<tbody>';
                html_str += '<tr>';
                html_str += '<th class="w130">最近查阅时间</th>';
                html_str += '<th class="w170">查阅门店</th>';
                html_str += '<th class="w60">查阅人</th>';
                html_str += '<th class="w90">总查阅次数</th>';
                html_str += '<th class="w90">今日查阅次数</th>';
                html_str += '<th>初次查阅时间</th>';
                html_str += '</tr>';
                for(var i=0;i<return_data.length;i++){
                    html_str += '<tr>';
                    html_str += '<td>'+return_data[i].recent_brower+'</td>';//查阅时间
                    html_str += '<td>'+return_data[i].agency_name+'</td>';//查阅门店
                    html_str += '<td>'+return_data[i].broker_name+'</td>';//查阅人
                    html_str += '<td>'+return_data[i].brower_num+'</td>';//总查阅次数
                    html_str += '<td>'+return_data[i].today_brower_num+'</td>';//今日查阅次数
                    html_str += '<td>'+return_data[i].first_brower+'</td>';//初次查阅时间
                    html_str += '</tr>';
                }
                html_str += '</tbody>';
                $('#table_list').empty().html(html_str);
            }else if(tab==3){
                if('cooperate'==is_cooperate){
                    var html_str = '<tbody>';
                    html_str += '<tr>';
                    html_str += '<th class="w90">申请人</th>';
                    html_str += '<th class="w200">所属门店</th>';
                    html_str += '<th class="w160">联系方式</th>';
                    html_str += '<th class="w170">查看时间</th>';
                    html_str += '<th>状态</th>';
                    html_str += '</tr>';
                    for(var i=0;i<return_data.length;i++){
                        html_str += '<tr>';
                        html_str += '<td>'+return_data[i].broker_name_b+'</td>';//申请人
                        html_str += '<td>'+return_data[i].agency_name_b+'</td>';//所属门店
                        html_str += '<td>'+return_data[i].phone_b+'</td>';//联系方式
                        html_str += '<td>'+return_data[i].creattime+'</td>';//查看时间
                        html_str += '<td>'+return_data[i].esta+'</td>';//状态
                        html_str += '</tr>';
                    }
                    html_str += '</tbody>';
                    $('#cooperate_table_list').empty().html(html_str);
                }else{
                    var html_str = '<tbody>';
                    html_str += '<tr>';
                    html_str += '<th class="w90">查看人</th>';
                    html_str += '<th class="w200">所属门店</th>';
                    html_str += '<th class="w160">联系方式</th>';
                    html_str += '<th class="w70">查看次数</th>';
                    html_str += '<th>最近查看时间</th>';
                    html_str += '</tr>';
                    for(var i=0;i<return_data.length;i++){
                        html_str += '<tr>';
                        html_str += '<td>'+return_data[i].broker_name_v+'</td>';//查看人
                        html_str += '<td>'+return_data[i].agency_name_v+'</td>';//所属门店
                        html_str += '<td>'+return_data[i].broker_telno_v+'</td>';//联系方式
                        html_str += '<td>'+return_data[i].num+'</td>';//查看次数
                        html_str += '<td>'+return_data[i].datetime+'</td>';//最近查看时间
                        html_str += '</tr>';
                    }
                    html_str += '</tbody>';
                    $('#table_list').empty().html(html_str);
                }
            }
        }
    });
}


$(function(){
    var customer_id = <?php echo $data_info['id'];?>;//客源id
    var pages = <?php echo $pages;?>;//总查阅次数
    //
    //下一页
    $('#next_page').click(function(){
        var page = $('input[name="pg"]').val();
        ajax_submit('next_page',page,customer_id);
        $('input[name="pg"]').val(parseInt(page)+1);
        var newpage = $('input[name="pg"]').val();
        $('#thispage').html(newpage);
        $('#prev_page').attr('style','');
        if(newpage>pages-1){
            $('#next_page').css('display','none');
        }else{
            $('#next_page').attr('style','');
        }
    });
    //上一页
    $('#prev_page').click(function(){
        var page = $('input[name="pg"]').val();
        ajax_submit('prev_page',page,customer_id);
        $('input[name="pg"]').val(parseInt(page)-1);
        var newpage = $('input[name="pg"]').val();
        $('#thispage').html(newpage);
        $('#next_page').attr('style','');
        if(newpage<2){
            $('#prev_page').css('display','none');
        }else{
            $('#prev_page').attr('style','');
        }
    });
    //合作申请下一页
    $('#cooperate_next_page').click(function(){
        var page = $('input[name="cooperate_pg"]').val();
        ajax_submit('next_page',page,customer_id,'cooperate');
        $('input[name="cooperate_pg"]').val(parseInt(page)+1);
        var newpage = $('input[name="cooperate_pg"]').val();
        $('#cooperate_thispage').html(newpage);
        $('#cooperate_prev_page').attr('style','');
        if(newpage>pages-1){
            $('#cooperate_next_page').css('display','none');
        }else{
            $('#cooperate_next_page').attr('style','');
        }
    });
    //合作申请上一页
    $('#cooperate_prev_page').click(function(){
        var page = $('input[name="cooperate_pg"]').val();
        ajax_submit('prev_page',page,customer_id,'cooperate');
        $('input[name="cooperate_pg"]').val(parseInt(page)-1);
        var newpage = $('input[name="cooperate_pg"]').val();
        $('#cooperate_thispage').html(newpage);
        $('#cooperate_next_page').attr('style','');
        if(newpage<2){
            $('#cooperate_prev_page').css('display','none');
        }else{
            $('#cooperate_prev_page').attr('style','');
        }
    });
});
</script>
