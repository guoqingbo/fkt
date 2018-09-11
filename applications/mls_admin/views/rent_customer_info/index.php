<?php require APPPATH . 'views/header.php'; ?>
   <script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/jquery-ui-1.9.2.custom.min.js"></script>

    <div id="wrapper">
    <div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= $title ?></h1>
        </div>
    </div>
    <div class="row">
    <div class="col-lg-12">
    <div class="panel panel-default">
    <div class="panel-body">
    <div class="table-responsive">
        <form name="search_form" method="post" action="<?php echo MLS_ADMIN_URL;?>/rent_customer_info/index" >
            <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                <div class="row">
                    <div class="col-sm-6" style="width:100%">
                        <div class="dataTables_length" id="dataTables-example_length">
                            客源编号：<input type="text" name="id">
                            客源内部编号：<input type="text" name="">
                            状态：<select name="status">
                                <option value="0">不限</option>
                                <?php if(is_array($conf_customer['status']) && !empty($conf_customer['status'])) { ?>
                                    <?php foreach($conf_customer['status'] as $key => $value){ ?>
                                        <option value='<?php echo $key;?>' <?php if($post_param['status'] == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            性质：<select name='public_type'>
                                <option value="0">不限</option>
                                <?php if(is_array($conf_customer['public_type']) && !empty($conf_customer['public_type'])) { ?>
                                    <?php foreach($conf_customer['public_type'] as $key => $value){ ?>
                                        <option value='<?php echo $key;?>' <?php if($post_param['public_type'] == $key){ echo 'selected';  } ?>> <?php echo $value;?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            用途：<select name='property_type'>
                                <option value="0">不限</option>
                                <?php if(is_array($conf_customer['property_type']) && !empty($conf_customer['property_type'])) { ?>
                                    <?php foreach($conf_customer['property_type'] as $key => $value){ ?>
                                        <option value='<?php echo $key;?>' <?php if($post_param['property_type'] == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select><br/><br>
                            区属：<select name='dist_id' onchange ="get_street_by_id(this , 'street_id')">
                                <option selected="" value="0">请选择区属</option>
                                <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                                    <?php foreach($district_arr as $key => $value){ ?>
                                        <option value="<?php echo $value['id'];?>" <?php if($post_param['dist_id'] == $value['id']){ echo 'selected';  } ?>>
                                            <?php echo $value['district'];?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            板块：<select name='street_id' id="street_id">
                                <option value="0">不限</option>
                                <?php if(is_array($select_info['street_info']) && !empty($select_info['street_info'])){ ?>
                                    <?php foreach($select_info['street_info'] as $key =>$value){ ?>
                                        <option value="<?php echo $value['id'];?>" <?php if($post_param['street_id'] == $value['id']){ echo 'selected';  } ?>>
                                            <?php echo $value['streetname'];?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            户型：<select name='room'>
                                <option value='0'>不限</option>
                                <?php if(is_array($conf_customer['room_type']) && !empty($conf_customer['room_type'])) { ?>
                                    <?php foreach($conf_customer['room_type'] as $key => $value){ ?>
                                        <option value='<?php echo $key;?>' <?php if($post_param['room'] == $key){ echo 'selected';  } ?>><?php echo $value;?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            面积：<input type="text" name='area_min' value='<?php echo $post_param['area_min'];?>' style="width:50px;">
                            —
                            <input type="text" name='area_max' value='<?php echo $post_param['area_max'];?>' style="width:50px;">
                            平米

                            租金：<input type="text" name='price_min' class="input w30" value='<?php echo $post_param['price_min'];?>' style="width:50px;">
                            —
                            <input type="text" name='price_max' class="input w30" value='<?php echo $post_param['price_max'];?>' style="width:50px;">
                            元/月<br><br>

                            公司名称：<input type="text" name="company_name" id="company_name" value="<?php  ?>">
                            <input type="hidden" name='agency_id' id='agency_id' value='<?php echo $post_param['agency_id'];?>'>

                            <select name="where_broker_info">
                                <option value="0">---按经纪人信息---</option>
                                <option value="b.broker_id" <?php if($post_param['where_broker_info'] == 'b.broker_id') { ?> selected="selected" <?php } ?>>经纪人帐号</option>
                                <option value="b.truename" <?php if($post_param['where_broker_info'] == 'b.truename') { ?> selected="selected" <?php } ?>>经纪人姓名</option>
                                <option value="b.phone" <?php if($post_param['where_broker_info'] == 'b.phone') { ?> selected="selected" <?php } ?>>联系方式</option>
                            </select><input type="text" name="broker_content" value="<?php echo $post_param['broker_content']; ?>">

                            查询结果按照：<select name="customer_order">
                                <option value="id">客源编号</option>
                            </select>
                            <select name="where_order">
                                <option value="0">不限</option>
                                <option value="1" <?php if($post_param['where_order'] == 1) { ?> selected="selected" <?php } ?>>正序排列</option>
                                <option value="2" <?php if($post_param['where_order'] == 2) { ?> selected="selected" <?php } ?>>倒序排列</option>
                            </select>
                            每页显示<select name="where_page">
                                <option value="10">10</option>
                                <option value="30" <?php if($pagesize == 30) { ?> selected="selected" <?php } ?>>30</option>
                                <option value="40" <?php if($pagesize == 40) { ?> selected="selected" <?php } ?>>40</option>
                                <option value="50" <?php if($pagesize == 50) { ?> selected="selected" <?php } ?>>50</option>
                            </select>条数据

                            <label>
                                <div class="dataTables_length" id="dataTables-example_length">
                                    <input type="hidden" name="pg" value="1">
                                    <input class="btn btn-primary" type="submit" value="查询">
                                    <!--<a href="/buy_customer_info/add/" class="btn btn-primary">添加</a>-->
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-striped table-bordered table-hover" id="dataTables-example" style="font-size: 12px;">
        <thead>
        <tr>
            <th style="width:68px;">客源编号</th>
            <th style="width:68px;">内部编号</th>
            <th style="width:42px;">状态</th>
            <th style="width:42px;">性质</th>
            <th style="width:68px;">物业类型</th>
            <th style="width:88px;">意向区属板块</th>
            <th style="width:78px;">户型（室）</th>
            <th style="width:70px;">面积（m²）</th>
            <th style="width:82px;">价格（元/月）</th>
            <th style="width:68px;">所在门店</th>
            <th style="width:68px;">经纪人姓名</th>
            <th style="width:42px;">帐号</th>
            <th style="width:68px;">联系方式</th>
            <th style="width:68px;">委托时间</th>
            <th style="width:42px;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($customer_list) && !empty($customer_list)) {
            foreach ($customer_list as $key => $value) { ?>
                <tr class="gradeA">
                    <td><?php echo "QZ".$value['id']; ?></td>
                    <td>内部编号</td>
                    <td>
                        <?php
                        if(isset($conf_customer['status'][$value['status']]) && $conf_customer['status'][$value['status']] != '')
                        {
                            echo $conf_customer['status'][$value['status']];
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if(isset($conf_customer['public_type'][$value['public_type']]) && $conf_customer['public_type'][$value['public_type']] != '')
                        {
                            echo $conf_customer['public_type'][$value['public_type']];
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if(isset($conf_customer['property_type'][$value['property_type']]))
                        {
                            echo $conf_customer['property_type'][$value['property_type']];
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $district1 = '';
                        $district2 = '';
                        $district3 = '';
                        $street1 = '';
                        $street2 = '';
                        $street3 = '';
                        $district_street = '';
                        if($value['dist_id1'] > 0 && isset($district_arr[$value['dist_id1']]['district']))
                        {
                            $district1 =  $district_arr[$value['dist_id1']]['district'];
                            if($value['street_id1'] > 0 && !empty($street_arr[$value['street_id1']]['streetname']))
                            {
                                $street1 =  $street_arr[$value['street_id1']]['streetname'];
                                $district_street .= $district1."-".$street1;
                            } else {
                                $district_street .= $district1;
                            }
                        }

                        if($value['dist_id2'] > 0 && isset($district_arr[$value['dist_id2']]['district']))
                        {
                            $district2 = $district_arr[$value['dist_id2']]['district'];
                            if($value['street_id2'] > 0 && !empty($street_arr[$value['street_id2']]['streetname']))
                            {
                                $street2 = $street_arr[$value['street_id2']]['streetname'];
                                $district_street .= $district2."-".$street2;
                            } else {
                                $district_street .= "、".$district2;
                            }
                        }

                        if($value['dist_id3'] > 0 && isset($district_arr[$value['dist_id3']]['district']))
                        {
                            $district3 = $district_arr[$value['dist_id3']]['district'];
                            if($value['street_id3'] > 0 && !empty($street_arr[$value['street_id3']]['streetname']))
                            {
                                $street3 = $street_arr[$value['street_id3']]['streetname'];
                                $district_street .= $district3."-".$street3;
                            } else {
                                $district_street .= "、".$district3;
                            }
                        }
                        echo $district_street;
                        ?>
                    </td>
                    <td>
                        <?php echo $value['room_min'];?>、<?php echo $value['room_max'];?>
                    </td>
                    <td>
                        <?php echo $value['area_min'];?>-<?php echo $value['area_max'];?>
                    </td>
                    <td>
                        <?php echo $value['price_min'];?>-<?php echo $value['price_max'];?>
                    </td>
                    <td>
                        <?php echo $value['agency_name'] ;?>
                    </td>
                    <td>
                        <?php
                        if(isset($value['broker_name']) && $value['broker_name'] !='')
                        {
                            echo $value['broker_name'];
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo $value['broker_id']; ?>
                    </td>
                    <td>
                        <?php
                        if(isset($customer_broker_info[$value['broker_id']]['phone']) && $customer_broker_info[$value['broker_id']]['phone'] !='')
                        {
                            echo $customer_broker_info[$value['broker_id']]['phone'];
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo date('Y-m-d H:i:s' , $value['updatetime']);?>
                    </td>
                    <td>
                        <a href="<?php echo MLS_ADMIN_URL; ?>/rent_customer_info/modify/<?php echo $value['id']; ?>" >详情</a>
                        <a href="<?php echo MLS_ADMIN_URL; ?>/rent_customer_info/follow/<?php echo $value['id']; ?>" >跟进</a>
                        <a href="<?php echo MLS_ADMIN_URL; ?>/rent_customer_info/del_customer/<?php echo $value['id']; ?>">关联失效</a>
                    </td>
                </tr>
            <?php }} ?>
        </tbody>
    </table>
    <div class="row">
        <div class="col-sm-6">
            <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                    <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/user/index');?>
                </ul>
            </div>
        </div>
    </div>
    <div style="color:blue;position:absolute;right:33px;">
        <b>共查到<?php echo $rent_customer_num;?>条数据</b>
    </div>

    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <script language="JavaScript">
        //区属找板块
        function get_street_by_id(obj , child_object_id)
        {
            var dist_id = parseInt($(obj).val());

            $.getJSON(
                '/district_street/get_streetinfo_by_distid/',
                {'dist_id':dist_id},
                function(data)
                {
                    if(data == 'errorCode401')
                    {
                        jump_win('', '请重新登录');
                        return false;
                    }

                    $("#"+child_object_id).empty();
                    $("#"+child_object_id).append("<option selected='' value='0'>不限</option>");
                    $.each(data, function(i, item) {
                        var child_option = "<option value="+ item.id +">"+item.streetname+"</option>";
                        $("#"+child_object_id).append(child_option);
                    });
                }
            );
        }

        //自动联想公司名称
        $(function(){
            $("#company_name").autocomplete({
                source: function( request, response )
                {
                    var company_name = request.term;

                    $.ajax({
                        url: "/buy_customer_info/get_agencyinfo_by_kw/",
                        type: "GET",
                        dataType: "JSON",
                        data: {keyword: company_name},
                        success: function(data)
                        {
                            //判断返回数据是否为空，不为空返回数据。
                            if(data[0]['id'] != '0')
                            {
                                response(data);
                            }
                            else
                            {
                                response(data);
                            }
                        }
                    });
                },
                minLength: 3,
                removeinput: 0,
                select: function(event , ui)
                {
                    if(ui.item.id > 0)
                    {
                        var company_name = ui.item.label;
                        var id = ui.item.id;
                        $(this).val(company_name);
                        $('#agency_id').val(id);
                        removeinput = 2;
                    }
                    else
                    {
                        removeinput = 1;
                    }
                },
                close: function(event) {
                    if( typeof(removeinput) == 'undefined' || removeinput == 1)
                    {
                        $(this).val('');
                        $('#agency_id').val('');
                    }
                }
            });
        });
    </script>
<?php require APPPATH.'views/footer.php'; ?>
