<?php require APPPATH.'views/header.php'; ?>
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/jquery-ui-1.9.2.custom.min.js"></script>
    <link href="<?=MLS_SOURCE_URL ?>/mls/third/iconfont/iconfont.css" rel="stylesheet" type="text/css">
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
   <form name="search_form" method="post" action="<?php echo MLS_ADMIN_URL;?>/buy_customer_info/modify_info" >
        <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
            <div class="row">
                <div class="col-sm-6" style="width:100%">
                    <div class="dataTables_length" id="dataTables-example_length">
                        <b>客源信息【加密】</b><br>
                        <input type="hidden" name="customer_id" value="<?php echo $customer_info['id'] ?>">
                        <input type="hidden" name="customer_broker_id" value="<?php echo $customer_info['broker_id'] ?>">
                        <div class="form-group">
                            姓名：
                            <input type="text" class="form-control" value="<?php echo $customer_info['truename']; ?>" name="truename" placeholder="Enter name" disabled="disabled">
                        </div>
                        <?php if(is_array($conf_customer['sex']) && !empty($conf_customer['sex'])) { ?>
                            <?php foreach($conf_customer['sex'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="sex" <?php if($customer_info['sex'] == $key){ ?> checked <?php } ?> value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>

                        <div class="form-group">
                            身份证号：
                            <input type="text" class="form-control" name="idno" value="<?php echo $customer_info['idno'] ?>" disabled="disabled">
                        </div><br><br>
                        <!-- -----------------第一行-----------------  -->
                        <div class="form-group">
                            联系方式：
                            <input type="text" class="form-control" name="telno[]" value="<?php echo $customer_info['telno1'] ?>" disabled="disabled">
                            <a href="javascript:void(0)" class="iconfont addTel" id="btn_addTel1" >&#xe608;</a>
                        </div>
                        <div class="form-group" <?php if(empty($customer_info['telno2'])) { ?> style="display: none;" <?php } ?> id="tel_form2">
                            <input type="text" class="form-control" name="telno[]" value="<?php echo $customer_info['telno2'] ?>" disabled="disabled">
                            <a href="javascript:void(0)" class="iconfont delTel" id="btn_delTel2">&#xe60c;</a>
                            <a href="javascript:void(0)" class="iconfont addTel" id="btn_addTel2">&#xe608;</a>
                        </div>
                        <div class="form-group" <?php if(empty($customer_info['telno3'])) { ?> style="display: none;" <?php } ?> id="tel_form3">
                            <input type="text" class="form-control" name="telno[]" value="<?php echo $customer_info['telno3'] ?>" disabled="disabled">
                            <a href="javascript:void(0)" class="iconfont delTel" id="btn_delTel3">&#xe60c;</a>
                            <a href="javascript:void(0)" class="iconfont addTel" id="btn_addTel3">&#xe608;</a>
                        </div>
                        <br><br>
                        <!-- -----------------第二行-----------------  -->
                        <div class="form-group">
                            联系地址：
                            <input type="text" class="form-control" name="address" value="<?php echo $customer_info['address'] ?>" disabled="disabled">
                        </div><br><br>
                        <!-- -----------------第三行-----------------  -->
                        客源类型：
                        <?php if(is_array($conf_customer['job_type']) && !empty($conf_customer['job_type'])) { ?>
                            <?php foreach($conf_customer['job_type'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="job_type" <?php if($customer_info['job_type'] == $key) { ?> checked <?php } ?> value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        <!-- -----------------第四行-----------------  -->
                        客源等级：
                        <?php if(is_array($conf_customer['user_level']) && !empty($conf_customer['user_level'])) { ?>
                            <?php foreach($conf_customer['user_level'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="user_level" <?php if($customer_info['user_level'] == $key) { ?> checked <?php } ?> value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        <!-- -----------------第五行-----------------  -->
                        年龄：
                        <?php if(is_array($conf_customer['age_group']) && !empty($conf_customer['age_group'])) { ?>
                            <?php foreach($conf_customer['age_group'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="age_group" <?php if($customer_info['age_group'] == $key) { ?> checked <?php } ?> value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>

                        <!-- ---------------------------------------  -->
                        <!-- -----------------第六行-----------------  -->
                        是否合作：
                        <?php if(is_array($conf_customer['is_share']) && !empty($conf_customer['is_share'])) { ?>
                            <?php foreach($conf_customer['is_share'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="is_share" <?php if($customer_info['is_share'] == $key) { ?> checked <?php } ?> value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        <!-- ---------------------------------------  -->

                        <b>客源信息</b><br>
                        状态：
                        <?php if(is_array($conf_customer['status']) && !empty($conf_customer['status'])) { ?>
                            <?php foreach($conf_customer['status'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="status" <?php if($customer_info['status'] == $key){?> checked <?php } ?> value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        客源性质：
                        <?php if(is_array($conf_customer['public_type']) && !empty($conf_customer['public_type'])) { ?>
                            <?php
                            foreach($conf_customer['public_type'] as $key => $value)
                            {
                                ?>
                                <label class="radio-inline">
                                    <input type="radio" name="public_type" <?php if($customer_info['public_type'] == $key){?> checked <?php } ?> value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                                <?php
                            }?>
                        <?php } ?>
                        <br><br>
                        <!-------------------第六行----------------->
                        户型：
                        <div class="form-group">
                            <input type="text" class="form-control" name="room_min" value="<?php echo $customer_info['room_min'] ?>" disabled="disabled">——<input type="text" class="form-control" name="room_max" value="<?php echo $customer_info['room_max'] ?>" disabled="disabled">室
                        </div>
                        <br><br>
                        <!-------------------第七行----------------->
                        面积：
                        <div class="form-group">
                        <input type="text" class="form-control" name="area_min" value="<?php echo $customer_info['area_min'] ?>" disabled="disabled">——<input type="text" class="form-control" name="area_max" value="<?php echo $customer_info['area_max'] ?>" disabled="disabled">平方米
                        </div>
                        价格：
                        <div class="form-group">
                            <input type="text" class="form-control" name="price_min" value="<?php echo $customer_info['price_min'] ?>" disabled="disabled">——<input type="text" class="form-control" name="price_max" value="<?php echo $customer_info['price_max'] ?>" disabled="disabled">万元
                        </div>
                        <br><br>
                        <!-------------------第八行----------------->
                        <div style="height:40px;">
                            <span style="float: left;">意向区属：</span>
                            <div style="float: left;" id="box_dist1">
                        <select class="form-control" name="dist_id[]" onchange ="get_street_by_id(this , 'street_id1')" disabled="disabled">
                            <option value="0">请选择区属</option>
                            <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                                <?php foreach($district_arr as $key => $value){?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['dist_id1'] == $value['id']){echo 'selected';}?>><?php echo $value['district'];?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <select class="form-control" name="street_id[]" id="street_id1" disabled="disabled">
                            <option value="0">请选择板块</option>
                            <?php if(is_array($select_info1['street_info']) && !empty($select_info1['street_info'])){ ?>
                                <?php foreach($select_info1['street_info'] as $key =>$value){ ?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['street_id1'] == $value['id']){ echo 'selected';  } ?>>
                                        <?php echo $value['streetname'];?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <a href="javascript:void(0)" class="iconfont addTel" id="btn_add_dist1">&#xe608;</a>
                            </div>

                        <!-------------------------第二个区属板块---------------------------->

                            <div style="float: left;<?php if(empty($customer_info['dist_id2']) || $customer_info['dist_id2'] == 0 ) { ?> display: none; <?php } ?>" id="box_dist2">
                        <select class="form-control" name="dist_id[]" id="dist_id2" onchange ="get_street_by_id(this , 'street_id2')" disabled="disabled">
                            <option value="0">请选择区属</option>
                            <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                                <?php foreach($district_arr as $key => $value){?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['dist_id2'] == $value['id']){echo 'selected';}?>><?php echo $value['district'];?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <select class="form-control" name="street_id[]" id="street_id2" disabled="disabled">
                            <option value="0">请选择板块</option>
                            <?php if(is_array($select_info2['street_info']) && !empty($select_info2['street_info'])){ ?>
                                <?php foreach($select_info2['street_info'] as $key =>$value){ ?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['street_id2'] == $value['id']){ echo 'selected';  } ?>>
                                        <?php echo $value['streetname'];?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <a href="javascript:void(0)" class="iconfont delTel" id="btn_del_dist2">&#xe60c;</a>
                        <a href="javascript:void(0)" class="iconfont addTel" id="btn_add_dist2">&#xe608;</a>
                            </div>
                        <!--------------------------------第三个区属板块----------------------------------->

                             <div style="float: left;<?php if(empty($customer_info['dist_id3']) || $customer_info['dist_id3'] == 0 ) { ?> display: none; <?php } ?>" id="box_dist3">
                        <select class="form-control" name="dist_id[]" id="dist_id3" onchange ="get_street_by_id(this , 'street_id3')" disabled="disabled">
                            <option value="0">请选择区属</option>
                            <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                                <?php foreach($district_arr as $key => $value){?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['dist_id3'] == $value['id']){echo 'selected';}?>><?php echo $value['district'];?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <select class="form-control" name="street_id[]" id="street_id3" disabled="disabled">
                            <option value="0">请选择板块</option>
                            <?php if(is_array($select_info3['street_info']) && !empty($select_info3['street_info'])){ ?>
                                <?php foreach($select_info3['street_info'] as $key =>$value){ ?>
                                    <option value="<?php echo $value['id'];?>" <?php if($customer_info['street_id3'] == $value['id']){ echo 'selected';  } ?>>
                                        <?php echo $value['streetname'];?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <a href="javascript:void(0)" class="iconfont delTel" id="btn_del_dist3">&#xe60c;</a>
                             </div>
                        (如有多个意向请添加)
                        </div>
                        <!--------------------第九行------------------>
                        意向楼盘：
                        <div class="form-group">
                            <input type="text" class="form-control" name="cmt_name[]" id="block01" value="<?php echo $customer_info['cmt_name1'];?>" disabled="disabled">
                            <input type="hidden" class="form-control" name="cmt_id[]" value="<?php echo $customer_info['cmt_id1'];?>">
                            <a href="javascript:void(0)" class="iconfont addTel" id="btn_add_cmt1">&#xe608;</a>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="form-group" id="box_cmt1" <?php if(empty($customer_info['cmt_id2']) || $customer_info['cmt_id2'] == 0) { ?> style="display: none;" <?php } ?>>
                            <input type="text" class="form-control" name="cmt_name[]" id="block02" value="<?php echo $customer_info['cmt_name2'];?>" disabled="disabled">
                            <input type="hidden" class="form-control" name="cmt_id[]" value="<?php echo $customer_info['cmt_id2'];?>">
                            <a href="javascript:void(0)" class="iconfont addTel" id="btn_del_cmt2">&#xe60c;</a>
                            <a href="javascript:void(0)" class="iconfont addTel" id="btn_add_cmt2">&#xe608;</a>
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="form-group" id="box_cmt2" <?php if(empty($customer_info['cmt_id3']) || $customer_info['cmt_id3'] == 0) { ?> style="display: none;" <?php } ?>>
                            <input type="text" class="form-control" name="cmt_name[]" id="block03" value="<?php echo $customer_info['cmt_name3'];?>" disabled="disabled">
                            <input type="hidden" class="form-control" name="cmt_id[]" value="<?php echo $customer_info['cmt_id3'];?>">
                            <a href="javascript:void(0)" class="iconfont addTel" id="btn_del_cmt3">&#xe60c;</a>
                        </div>
                        <br><br>
                        朝向：
                        <?php if(is_array($conf_customer['forward']) && !empty($conf_customer['forward'])) { ?>
                            <?php foreach($conf_customer['forward'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio"  <?php if($customer_info['forward'] == $key){echo 'checked';}?> name="forward" value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        装修：
                        <?php if(is_array($conf_customer['fitment']) && !empty($conf_customer['fitment'])) { ?>
                            <?php foreach($conf_customer['fitment'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" name="fitment" <?php if($customer_info['fitment'] == $key){echo 'checked';}?> value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        楼层：
                        <div class="form-group">
                            <input type="text" class="form-control" name="floor_min" value="<?php echo $customer_info['floor_min']?>">——<input type="text" class="form-control" name="floor_max" value="<?php echo $customer_info['floor_max'] ?>">
                        </div>
                        <br><br>
                        地段：
                        <?php if(is_array($conf_customer['location']) && !empty($conf_customer['location'])) { ?>
                            <?php foreach($conf_customer['location'] as $key => $value){ ?>
                                <label class="checkbox-inline">
                                    <input type="checkbox" <?php if($customer_info['location'] == $key) { ?> checked <?php } ?> name="location" value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        类型：
                        <?php if(is_array($conf_customer['house_type']) && !empty($conf_customer['house_type'])) { ?>
                            <?php foreach($conf_customer['house_type'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" <?php if($customer_info['house_type'] == $key) { ?> checked <?php } ?> name="house_type" value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        用途：
                        <?php if(is_array($conf_customer['property_type']) && !empty($conf_customer['property_type'])) { ?>
                            <?php foreach($conf_customer['property_type'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" <?php if($customer_info['property_type'] == $key) { ?> checked <?php } ?>  name="property_type" value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        房龄：
                        <?php if(is_array($conf_customer['house_age']) && !empty($conf_customer['house_age'])) { ?>
                            <?php foreach($conf_customer['house_age'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" <?php if($customer_info['house_age'] == $key) { ?> checked <?php } ?> name="house_age" value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        付款方式：
                        <?php if(is_array($conf_customer['payment']) && !empty($conf_customer['payment'])) { ?>
                            <?php foreach($conf_customer['payment'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" <?php if($customer_info['payment'] == $key) { ?> checked <?php } ?> name="payment" value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        付佣方式：
                        <?php if(is_array($conf_customer['pay_commission']) && !empty($conf_customer['pay_commission'])) { ?>
                            <?php foreach($conf_customer['pay_commission'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" <?php if($customer_info['pay_commission'] == $key) { ?> checked <?php } ?> name="pay_commission" value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        目的：
                        <?php if(is_array($conf_customer['intent']) && !empty($conf_customer['intent'])) { ?>
                            <?php foreach($conf_customer['intent'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" <?php if($customer_info['intent'] == $key) { ?> checked <?php } ?> name="intent" value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        期限：
                        <?php if(is_array($conf_customer['deadline']) && !empty($conf_customer['deadline'])) { ?>
                            <?php foreach($conf_customer['deadline'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" <?php if($customer_info['deadline'] == $key) { ?> checked <?php } ?> name="deadline" value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        来源：
                        <?php if(is_array($conf_customer['infofrom']) && !empty($conf_customer['infofrom'])) { ?>
                            <?php foreach($conf_customer['infofrom'] as $key => $value){ ?>
                                <label class="radio-inline">
                                    <input type="radio" <?php if($customer_info['infofrom'] == $key) { ?> checked <?php } ?> name="infofrom" value='<?php echo $key;?>' disabled="disabled"> <?php echo $value;?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                        <br><br>
                        <div class="form-group">
                            委托时间：
                            <input type="text" class="form-control" name="creattime" value="<?php echo date('Y-m-d',$customer_info['creattime'])?>" disabled="disabled">
                        </div><br><br>委托时间
                        备注：
                        <textarea class="form-control" name="remark" rows="5" cols="100" disabled="disabled"><?php echo $customer_info['remark'] ?></textarea>
                        <br><br>

                        <input class="btn btn-default" type="submit" value="修改客源信息" style="display:none;">
                        <input class="btn btn-default" type="button" value="取消" style="display:none;">

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


</div>
</div>
</div>
</div>
</div>
</div>

<script>
    //区属找板块
    function get_street_by_id(obj , child_object_id)
    {
        var dist_id = parseInt($(obj).val());
        $.getJSON('/district_street/get_streetinfo_by_distid/',
            {'dist_id':dist_id},
            function(data)
            {
                $("#"+child_object_id).empty();
                $("#"+child_object_id).append("<option selected='' value='0'>请选择板块</option>");
                $.each(data, function(i, item) {
                    var child_option = "<option value="+ item.id +">"+item.streetname+"</option>";
                    $("#"+child_object_id).append(child_option);
                });
            }
        );
    };

    //点击联系方式加号按钮
    $(function(){
        //电话部分
        $("#btn_addTel1").click(function(){
            $("#tel_form2").show();
        });
        $("#btn_addTel2").click(function(){
            $("#tel_form3").show();
        });
        $("#btn_delTel2").click(function(){
            $("#tel_form2").hide();
        });
        $("#btn_delTel3").click(function(){
            $("#tel_form3").hide();
        });
        //区属部分
        $("#btn_add_dist1").click(function(){
            $("#box_dist2").show();
        });
        $("#btn_add_dist2").click(function(){
            $("#box_dist3").show();
        });
        $("#btn_del_dist2").click(function(){
            $("#box_dist2").hide();
        });
        $("#btn_del_dist3").click(function(){
            $("#box_dist3").hide();
        });
        //楼盘部分
        $("#btn_add_cmt1").click(function(){
            $("#box_cmt1").show();
        });
        $("#btn_add_cmt2").click(function(){
            $("#box_cmt2").show();
        });
        $("#btn_del_cmt2").click(function(){
            $("#box_cmt1").hide();
        });
        $("#btn_del_cmt3").click(function(){
            $("#box_cmt2").hide();
        });
    })

    $(function(){
        $("#block01,#block02,#block03").autocomplete({
            source: function( request, response )
            {
                var cmt_name = request.term;

                $.ajax({
                    url: "/buy_customer_info/get_cmtinfo_by_kw/",
                    type: "GET",
                    dataType: "JSON",
                    data: {keyword: cmt_name},
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
                    var cmt_name = ui.item.label;
                    var id = ui.item.id;
                    $(this).val(cmt_name);
                    $(this).next('.cmt_id').val(id);
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
                    $(this).next('.cmt_id').val('');
                }
            }
        });
    });
</script>
<?php require APPPATH.'views/footer.php'; ?>
