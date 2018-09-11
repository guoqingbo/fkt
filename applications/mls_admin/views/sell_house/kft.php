<?php require APPPATH . 'views/header.php'; ?>
<style type="text/css">
    #r_s_popUP {position: absolute;top: 100px; left:100px;display: none}
    #r_s_popUP .replace_stores_popUp {position: relative;width: 410px; padding: 9px; border: 1px solid #6aa8e6; background: #fff; }
    .replace_stores_popUp .upgou { display: block; width: 7px;height: 5px; background: url(<?=MLS_SOURCE_URL ?>/mls_admin/images/xiangx.png) no-repeat; position: absolute; top: 230px;left: 45px; }
    .replace_stores_popUp li { padding: 10px 0;border-bottom: 1px dashed #dadada; zoom: 1;}
</style>
<div id="wrapper">
    <div id="page-wrapper">
        <?php if ($addResult == '') { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default" style="margin:20px 0; padding:20px 0;">
                        <form name="add_form" method="post" action="" onsubmit="button_deal();">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    分佣来源<font color="red">*</font>:
                                                    <select id="commission_source" name="commission_source" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                        <option value="1">由发布方支付</option>
                                                        <option value="3">由参与方支付</option>
                                                        <option value="2">双方各付</option>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 type_3_price" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span id="type_1_price">参与方分佣约为价格的</span><span id="type_2_price" style="display:none;">发布方分佣约为价格的</span><font color="red">*</font>:<input type="text" name="commission_ratio" class="form-control input-sm" aria-controls="dataTables-example" value="" id="commission_ratio">%
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 type_3_price" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <input type="hidden" name="commission_price" id="commission_price"/>
                                                    预计佣金<font color="red">*</font>:<span id="commission_price_view"></span>万
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    带看方<font color="red">*</font>:
                                                    <select id="look" name="look" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                        <option value="1">由双方共同带看</option>
                                                        <option value="2">由发布方带看</option>
                                                        <option value="3">由参与方带看</option>
                                                    </select>
                                                </label>
                                                <label>
                                                    签约方<font color="red">*</font>:
                                                    <select id="signed" name="signed" aria-controls="dataTables-example" class="form-control input-sm" style="width:200px">
                                                        <option value="1">由发布方负责与客户签订合约</option>
                                                        <option value="2">由参与方负责与客户签订合约</option>
                                                        <option value="3">由双方共同与客户签订合约</option>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        <!--end-->
                                       <?php if (!empty($mess_error)) { ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <font color='red'><?php echo $mess_error; ?></font>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="submit" value="提交" id='submit'>
                                               <!-- <a class="btn btn-primary" href="/company/index">返回</a>-->
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="submit_flag" value="add">
                                </form>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } else if (!$addResult) { ?>
            <div><h1><b>同步失败<?=$agency_mess_error;?></b></h1></div>
        <?php } else {?>
            <div><h1><b>同步成功<?=$agency_mess_error;?></b></h1></div>
        <?php }?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="col-lg-4" style="display:none" id="js_note1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            提示框
            <button type="button" class="close JS_Close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="panel-body">
            <p id="warning_text"></p>
        </div>
    </div>
</div>
<script>
function button_deal(){
    $('#submit').attr('disabled','disabled');
}

var Digit = {};
Digit.round = function(digit, length) {
    length = length ? parseInt(length) : 0;
    if (length <= 0) return Math.round(digit);
    digit = Math.round(digit * Math.pow(10, length)) / Math.pow(10, length);
    return digit;
};

$(function(){
//    $('#submit').live('click',function(){
//        $(this).attr('disabled','disabled');
//    });

    $('#commission_ratio').keyup(function(){
        var price = <?php echo $price; ?>;
        var result = price*$(this).val()*0.000001;
        var result_2 = Digit.round(result, 2);
        $('#commission_price').val(result_2);
        $('#commission_price_view').html(result_2);
    });

    $('#commission_source').change(function(){
        var commission_source_val = $("#commission_source option:selected").val();
        if(1==commission_source_val){
            $('.type_3_price').show();
            $('#type_1_price').show();
            $('#type_2_price').hide();
        }else if(3==commission_source_val){
            $('.type_3_price').show();
            $('#type_1_price').hide();
            $('#type_2_price').show();
        }else{
            $('.type_3_price').hide();
            $('#type_1_price').hide();
            $('#type_2_price').hide();
        }
    });

    //提示
    $("#per-hover").hover(function(){
        $("#r_s_popUP").toggle();
    });
    $("#add_agency").change(function(){
        if($("#add_agency").prop("checked")){
            $('#is_show').show();
            $('#agency').val(1);
        }else{
            $('#is_show').hide();
            $('#agency').val('');
        }
    });
    $('#district').change(function(){
        var districtID = $(this).val();
        $.ajax({
            type: 'get',
            url : '<?php echo MLS_ADMIN_URL; ?>/community/find_street_bydis/'+districtID,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg.result=='no result'){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
                    }
                }
                $('#street').empty();
                $('#street').append(str);
            }
        });
    });
});
</script>
<?php require APPPATH . 'views/footer.php'; ?>

