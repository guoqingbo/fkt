<body>
    <div class="achievement_money_pop" id="js_divide_pop" style="display:block">
    <dl class="title_top">
        <dd id="title_top2"><?=$id?'编辑':'添加'?>业绩分成</dd>
    </dl>
    <div style="height:380px;float:left;display:inline;width:100%;overflow:hidden;overflow-y:auto;">
    <p class="achievement_money_pop_point">温馨提示：业绩归属和归属经纪人是相互独立的，如A店业务员做了5000元业绩，但该业
    绩是属于B店进行结算的，在此可以灵活进行分配。</p>
        <div>
        <form action="" id="divide_form" method="post">

        <div class="achievement_money_pop_input" style="margin: 0 0 0 12px;">
            <h4>应分成金额：<b><?=$contract['commission_total'];?>元</b></h4>
            <dl>
                <dd><b>*</b>分成比例：</dd>
                <dt>
                <span class="money_pop_span"><input type="text" class="money_pop_input" name="divide_percent" value="<?=$divide_list['percent'];?>" onchange="check_percent()" autocomplete="off">%
                        <div class="errorBox" id="percent_error"></div>
                    </span>
                <p class="money_pop_p">分成金额 <b id="should_divide_money"><?=$divide_list['percent']?sprintf("%.2f",($divide_list['percent']*$contract['commission_total']/100))."元":""?></b></p>
                </dt>
            </dl>
            <dl>
                <dd><b>*</b>实际分成金额：</dd>
                <dt>
                    <span class="money_pop_span"><input type="text" class="money_pop_input test_money" name="divide_price"  value="<?=$divide_list['divide_price'];?>" autocomplete="off">元
                        <div class="errorBox"></div>
                    </span>
                </dt>
            </dl>
            <script>
                function check_percent(){
                    var percent1 = parseFloat($("input[name='divide_percent']").val());
                    var total1 = $("#percent_total").val();
                    if(percent1> 0){
                        var total = percent1+parseFloat(total1);
                        if(total > parseFloat(100)){
                            var remain = 100 - total1;
                            $("#percent_error").text("当前最大可填最大比例为"+remain.toFixed(2)+"%");
                        }else{
                            $("#percent_error").text("");
                        }
                        var actual = percent1*parseFloat(<?=$contract['commission_total'];?>)/100;
                        $("#should_divide_money").text(actual.toFixed(2)+"元");
                        $("input[name='divide_price']").val(actual.toFixed(2));
                    }else{
                        $("#should_divide_money").text('');
                        $("#percent_error").text("");
                        $("input[name='divide_price']").val('');
                    }
                }
            </script>
            <dl>
                <dd><b>*</b>归属人：</dd>
                <dt>
                    <span class="money_pop_span">
                        <select class="money_pop_select2" name="agency_id">
                            <?php if($agencys){foreach($agencys as $key =>$val){?>
                            <option value="<?=$val['id'];?>" <?=$divide_list['agency_id']==$val['id']?'selected':'';?>><?=$val['name'];?></option>
                            <?php }}?>
                        </select>
                        <div class="errorBox"></div>
                    </span>
                    <p class="money_pop_p">
                        <select class="money_pop_select" name="broker_id">
                            <?php if($brokers){foreach($brokers as $key =>$val){?>
                            <option value="<?=$val['broker_id'];?>" <?=$divide_list['broker_id']==$val['broker_id']?'selected':'';?>><?=$val['truename'];?></option>
                            <?php }}?>
                        </select>
                        <div class="errorBox"></div>
                    </p>
                </dt>
            </dl>
            <script>
                $("select[name='agency_id']").change(function(){
                    var agency_id = $("select[name='agency_id']").val();
                    if(agency_id){
                        $.ajax({
                            url:"/contract_earnest_money/broker_list",
                            type:"GET",
                            dataType:"json",
                            data:{
                               agency_id:agency_id
                            },
                            success:function(data){
                            if(data['result'] == 1){
                                var html = "<option value=''>请选择</option>";
                                for(var i in data['list']){
                                html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
                                }
                                $("select[name='broker_id']").html(html);
                            }else{
                                $("select[name='broker_id']").html("<option value=''>请选择</option>");
                            }
                            }
                        })
                    }else{
                    $("select[name='broker_id']").html("<option value=''>请选择</option>");
                    }
                })
		    </script>
            <dl>
                <dd><b>*</b>分成描述：</dd>
                <dt>
                    <span class="money_pop_span">
                        <select class="money_pop_select2" name="divide_type">
                            <option value="">请选择</option>
                            <?php foreach($config['divide_type'] as $key=>$val){?>
                            <option value="<?=$key;?>" <?=$divide_list['divide_type']==$key?'selected':'';?>><?=$val;?></option>
                            <?php }?>
                        </select>
                        <div class="errorBox"></div>
                    </span>
                </dt>
            </dl>
            <dl>
                <dd><b>*</b>门店业绩归属：</dd>
                <dt>
                    <span class="money_pop_span">
                        <select class="money_pop_select2" name="achieve_agency_id_b">
                            <?php if($agencys1){foreach($agencys as $key =>$val){?>
                            <option value="<?=$val['id'];?>" <?=$divide_list['achieve_agency_id_b']==$val['id']?'selected':'';?>><?=$val['name'];?></option>
                            <?php }}?>
                        </select>
                        <div class="errorBox"></div>
                    </span>
                    <p class="money_pop_p">
                        <select class="money_pop_select2" name="achieve_broker_id_b">
                            <?php if($brokers1){foreach($brokers as $key =>$val){?>
                            <option value="<?=$val['broker_id'];?>" <?=$divide_list['achieve_broker_id_b']==$val['broker_id']?'selected':'';?>><?=$val['truename'];?></option>
                            <?php }}?>
                        </select>
                        <span class="errorBox"></span>
                    </p>
                </dt>
            </dl>
            <script>
                $("select[name='achieve_agency_id_b']").change(function(){
                    var agency_id = $("select[name='achieve_agency_id_b']").val();
                    if(agency_id){
                        $.ajax({
                            url:"/contract_earnest_money/broker_list",
                            type:"GET",
                            dataType:"json",
                            data:{
                               agency_id:agency_id
                            },
                            success:function(data){
                            if(data['result'] == 1){
                                var html = "<option value=''>请选择人员</option>";
                                for(var i in data['list']){
                                html+="<option value='"+data['list'][i]['broker_id']+"'>"+data['list'][i]['truename']+"</option>";
                                }
                                $("select[name='achieve_broker_id_b']").html(html);
                            }else{
                                $("select[name='achieve_broker_id_b']").html("<option value=''>请选择人员</option>");
                            }
                            }
                        })
                    }else{
                    $("select[name='achieve_broker_id_b']").html("<option value=''>请选择人员</option>");
                    }
                })
		    </script>
        </div>

        <dl class="qz_prcess_btn money_pop_L164">
            <input type="hidden" id="percent_total" value="<?=$divide_total['percent_total']?>">
            <input type="hidden" name="divide_id" id="divide_id" value="<?=$id?>">
            <input type="hidden" id="contract_id" value="<?=$c_id?>">
            <button class="btn-lv1 btn-left" type="submit">确定</button>
            <button class="btn-hui1" type="button" onclick="closeParentWin('js_divide_pop');">取消</button>
        </dl>
        </form>
        </div>
         </div>
    </div>
<!--操作失败弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_false">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
                            <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
                        <td>
                            <p class="left" style="font-size:14px;color:#666;" id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>

<script type="text/javascript">
    $(".test_money").blur(function(){
                $(this).next(".errorBox").html(" ");
                if(!(/[0-9]$/).test($(this).val())){

                    $(this).next(".errorBox").html("请输入数字！");
                }

                if($(this).val().length > 10){
                    $(this).next(".errorBox").html("您输入的数字超过最大数，请从新输入");

                }
            })

</script>

</body>


