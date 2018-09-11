$(function(){
    document.oncontextmenu = function (e) { return false; }//禁止右键
    if (!window.XMLHttpRequest)
    {
        $(".forms_scroll").scroll(function(){
                $(".ui-autocomplete").hide();
        })
    }
});
$.validator.addMethod("contract_number",function(value,element,params){
    var reg = /^\w{0,30}$/;
    if( reg.test(value) )
    {
        return  true;
    }
},"合同编号应小于30个字符");
/*$.validator.addMethod("house_address",function(value,element,params){
    var reg = /^\w{0,80}$/;
    if( reg.test(value) )
    {
        return  true;
    }
},"房源地址应小于80个字符");*/
$.validator.addMethod("valid_name",function(value,element,params){
    var reg = /^[A-Za-z0-9\u4e00-\u9fa5]+$/;
    if( reg.test(value) )
    {
        return  true;
    }
},"业主姓名只能包含汉字、字母、数字");

$.validator.addMethod("isCardNo",function(value,element,params){
    var reg = /(^\d{15}$)|(^\d{18}$)|(^\[a-zA-Z]{6}$)|(^\d{17}(\d|X|x)$)/;
    if(reg.test(value))
    {
        return  true;
    }
},"身份证输入不合法");


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

$.validator.addMethod("isTNAME",function(value,element,params){
    if(value.length <= 10)
    {
        return true;
    }
},"业主姓名最多10个字符");

$.validator.addMethod("isPrice",function(value,element,params){
	if(value!=""){
		var reg =  /^\d+(\.\d{1,2})?$/;
		if(reg.test(value))
		{
			return true;
		}
	}else{
	    return true;
	}
},"请输入正确的价格,保留两位小数");

$.validator.addMethod("isTimeRange",function(value,element,params){
	var startime =  $("input[name='start_time']").val();
	var endtime =  $("input[name='end_time']").val();
    if(startime < endtime)
    {
        return true;
    }
},"请输入正确的时间范围");

$.validator.addMethod("isArea",function(value,element,params){
    var reg = /^[0-9]+(.[0-9]{1,2})?$/;///^[0-9]+(.[0-9]{1,2})?$/
    if(reg.test(value))
    {
        return true;
    }
},"面积只能是数字,小数可以保留两位");
//托管时间
$.validator.addMethod("isTimeCollo",function(value,element,params){
	var startime =  $("input[name='collo_start_time']").val();
	var endtime =  $("input[name='collo_end_time']").val();
    if(startime < endtime)
    {
        return true;
    }
},"托管结束时间不能早于托管开始时间");
//出租时间
$.validator.addMethod("isTimeRent",function(value,element,params){
	var startime =  $("input[name='rent_start_time']").val();
	var endtime =  $("input[name='rent_end_time']").val();
    if(startime < endtime)
    {
        return true;
    }
},"停租时间不能早于起租时间");

//添加托管合同的判断
$(function(){
	$("#jsUpForm").validate({
        errorPlacement: function(error, element) {
			error.appendTo(element.parents(".js_fields").find(".errorBox"));
        },
        submitHandler:function(form)
        {
			//如果是弹窗的形式就得隐藏$("#js_pop_add_attendance").attr('display','none');
            collocation_contract_add();
        },
        rules:{
			//托管合同编号
			 collocation_id:{
				required: true,
				maxlength:30
			 },
			//房源编号
			/* house_num:{
				required: true
			 },*/
			//楼盘名称
			 houses_name:{
				required: true
			 },
			//房源面积
			 houses_area:{
				required: true,
				isArea: true,
				maxlength:10
			 },
			//房源地址
			 houses_address:{
				required: true,
				maxlength:40
			 },
			//托管时间
			 collo_start_time:{
				required: true
			 },
			 collo_end_time:{
				required: true,
				isTimeCollo: true
			 },
			//签约时间
			 signing_time:{
				required: true
			 },
			//业主名字
			owner:{
				required: true,
				isTNAME:true,
				valid_name:true
			},
			//业主联系方式
			owner_tel:{
				required: true,
				isZWNo:true,
				isZMNo:true
			},
			//签约门店
			agency_id:{
				required: true
			},
			//签约人
			broker_id:{
				required: true
			},
			//签约人联系方式
			broker_tel:{
				required: true,
				isZWNo:true,
				isZMNo:true
			},
			//每月租金
			rental:{
				required: true,
				isPrice:true
			},
			//付款方式
			pay_type:{
				required: true
			},
			//租金总额
			rental_total:{
				required: true,
				isPrice:true
			},
			//押金金额
			desposit:{
				required: true,
				isPrice:true
			}
		 },
        messages:{
			collocation_id:{
				required: '请填写托管合同编号',
				maxlength: '合同编号最多30个字符'
			},
			/*house_num:{
				required: '请填写房源编号'
			},*/
            houses_name:{
                required: '请选择楼盘'
            },
			houses_area:{
                required: '请填写房源面积',
				isArea: '面积只能是数字，且可以保留两位小数',
				maxlength: '房源面积不能超过10位'
            },
            houses_address:{
				required: '请填写房源地址',
				maxlength: '房源地址最多40个字符'
            },
			collo_start_time:{
                required: '请选择托管开始时间'
            },
			collo_end_time:{
                required: '请选择托管结束时间',
				isTimeCollo: '托管结束时间不能早于托管开始时间'
            },
			signing_time:{
				required: '请选择签约时间'
			},
			owner:{
				required: '请填写业主姓名',
				isTNAME:'业主姓名最多10个字符',
				valid_name:'业主姓名只能包含汉字、字母、数字'
			},
			owner_tel:{
				required: '请填写业主联系方式',
				isZWNo: '电话号码不能有中文',
				isZMNo: '请填写正确的电话号码格式'
			},
			agency_id:{
				required: '请选择签约公司'
			},
			broker_id:{
				required: '请选择签约人'
			},
			broker_tel:{
				required: '请填写签约人联系方式',
				isZWNo: '电话号码不能有中文',
				isZMNo: '请填写正确的电话号码格式'
			},
			rental:{
				required: '请填写每月租金',
				isPrice: '请输入正确的租金'
			},
			pay_type:{
				required: '请选择付款方式'
			},
			rental_total:{
				required: '请填写租金总额',
				isPrice: '请输入正确的租金总额'
			},
			desposit:{
				required: '请填写押金金额',
				isPrice: '请输入正确的押金金额'
			}
        }
    });
	//托管合同提交初审资料
	function collocation_contract_add(){
		$.ajax({
			type: 'POST',
			url: '/collocation_contract/add_contract/',
			data:{
				'submit_flag' :$("input[name='submit_flag']").val(),
				'collocation_id':$("input[name='collocation_id']").val(),
				'house_id' :$("input[name='house_id']").val(),
				'block_name' : $("input[name='block_name']").val(),
				'block_id' : $("input[name='block_id']").val(),
				'houses_area' : $("input[name='houses_area']").val(),
				'houses_address' : $("input[name='houses_address']").val(),
				'type' :$("select[name='type']").val(),
				'collo_start_time':$("input[name='collo_start_time']").val(),
				'collo_end_time':$("input[name='collo_end_time']").val(),
				'total_month' :$("input[name='total_month']").val(),
				'owner' :$("input[name='owner']").val(),
				'owner_tel' :$("input[name='owner_tel']").val(),
				'owner_idcard' :$("input[name='owner_idcard']").val(),
				'pay_ditch' :$("input[name='pay_ditch']").val(),
				'agency_id' :$("select[name='agency_id']").val(),
				'broker_id' :$("select[name='broker_id']").val(),
				'broker_tel' :$("input[name='broker_tel']").val(),
				'rental' :$("input[name='rental']").val(),
				'pay_type' :$("select[name='pay_type']").val(),
				'rental_total' :$("input[name='rental_total']").val(),
				'desposit' :$("input[name='desposit']").val(),
				'penal_sum' :$("input[name='penal_sum']").val(),
				'tax_type' :$("select[name='tax_type']").val(),
				'property_manage_assume' :$("select[name='property_manage_assume']").val(),
				'property_fee' :$("input[name='property_fee']").val(),
				'agency_commission' :$("input[name='agency_commission']").val(),
				'rent_free_time' :$("input[name='rent_free_time']").val(),
				'desposit_type' :$("input[name='desposit_type']").val(),
				'divide_a_agency_id' :$("select[name='divide_a_agency_id']").val(),
				'divide_a_broker_id' :$("select[name='divide_a_broker_id']").val(),
				'divide_a_money' :$("input[name='divide_a_money']").val(),
				'divide_b_agency_id' :$("select[name='divide_b_agency_id']").val(),
				'divide_b_broker_id' :$("select[name='divide_b_broker_id']").val(),
				'divide_b_money' :$("input[name='divide_b_money']").val(),
				'out_agency_id' :$("select[name='out_agency_id']").val(),
				'out_broker_id' :$("select[name='out_broker_id']").val(),
				'stop_agreement_num' :$("input[name='stop_agreement_num']").val(),
				'list_items' :$("input[name='list_items']").val(),
				'remarks' :$("textarea[name='remarks']").val(),
				'signing_time' :$("input[name='signing_time']").val()
		    },
			dataType: 'json',
			success: function(data){
				if(data['result'] == 'ok'){
						$("#js_prompt").text('托管合同添加成功！');
						openWin('js_pop');
						setTimeout(function(){location.href="/collocation_contract/index"},2000);
				}else if(data['result'] == 'no'){
						$("#js_prompt").text('托管合同添加失败！');
						openWin('js_pop');
						setTimeout(function(){location.href="/collocation_contract/index"},2000);
				}else if(data['result'] == '0'){
					$("#js_prompt2").text(data['msg']);
					openWin('js_pop_false');
				}
			}
		});
	}
});
//修改托管合同的判断
$(function(){
	$("#jsUpForm_modify").validate({
        errorPlacement: function(error, element) {
			error.appendTo(element.parents(".js_fields").find(".errorBox"));
        },
        submitHandler:function(form)
        {
			//如果是弹窗的形式就得隐藏$("#js_pop_add_attendance").attr('display','none');
            collocation_contract_modify();
        },
        rules:{
			//托管合同编号
			 collocation_id:{
				required: true,
				maxlength:30
			 },
			//房源编号
			/* house_num:{
				required: true
			 },*/
			//楼盘名称
			 houses_name:{
				required: true
			 },
			//房源面积
			 houses_area:{
				required: true,
				isArea: true,
				maxlength:10
			 },
			//房源地址
			 houses_address:{
				required: true,
				maxlength:40
			 },
			//托管时间
			 collo_start_time:{
				required: true
			 },
			 collo_end_time:{
				required: true,
				isTimeCollo: true
			 },
			//签约时间
			 signing_time:{
				required: true
			 },
			//业主名字
			owner:{
				required: true,
				isTNAME:true,
				valid_name:true
			},
			//业主联系方式
			owner_tel:{
				required: true,
				isZWNo:true
			},
			//签约门店
			agency_id:{
				required: true
			},
			//签约人
			broker_id:{
				required: true
			},
			//签约人联系方式
			broker_tel:{
				required: true,
				isZWNo:true
			},
			//每月租金
			rental:{
				required: true
			},
			//付款方式
			pay_type:{
				required: true
			},
			//租金总额
			rental_total:{
				required: true
			},
			//押金金额
			desposit:{
				required: true
			}
		 },
        messages:{
			collocation_id:{
				required: '请填写托管合同编号',
				maxlength:'合同编号最多30个字符'
			},
			/*house_num:{
				required: '请填写房源编号'
			},*/
            houses_name:{
                required: '请选择楼盘'
            },
			houses_area:{
                required: '请填写房源面积',
				isArea: '面积只能是数字，且可以保留两位小数',
				maxlength:'房源面积不能超过10位'
            },
            houses_address:{
				required: '请填写房源地址',
				maxlength:'房源地址最多40个字符'
            },
			collo_start_time:{
                required: '请选择托管开始时间'
            },
			collo_end_time:{
                required: '请选择托管结束时间',
				isTimeCollo: '托管结束时间不能早于托管开始时间'
            },
			signing_time:{
				required: '请选择签约时间'
			},
			owner:{
				required: '请填写业主姓名'
			},
			owner_tel:{
				required: '请填写业主联系方式',
				isZWNo: '电话号码不能有中文',
				isZMNo: '请填写正确的电话号码格式'
			},
			agency_id:{
				required: '请选择签约公司'
			},
			broker_id:{
				required: '请选择签约人'
			},
			broker_tel:{
				required: '请填写签约人联系方式',
				isZWNo: '电话号码不能有中文',
				isZMNo: '请填写正确的电话号码格式'
			},
			rental:{
				required: '请填写每月租金'
			},
			pay_type:{
				required: '请选择付款方式'
			},
			rental_total:{
				required: '请填写租金总额'
			},
			desposit:{
				required: '请填写押金金额'
			}
        }
    });
	//托管合同提交初审资料
	function collocation_contract_modify(){
		var id=$('#modify_id').val();
		$.ajax({
			type: 'POST',
			url: '/collocation_contract/modify/'+id,
			data:{
				'modify_id':$("input[name='modify_id']").val(),
				'submit_flag' :$("input[name='submit_flag']").val(),
				'collocation_id':$("input[name='collocation_id']").val(),
				'house_id' : $("input[name='house_id']").val(),
				'block_name' : $("input[name='block_name']").val(),
				'block_id' : $("input[name='block_id']").val(),
				'houses_area' : $("input[name='houses_area']").val(),
				'houses_address' : $("input[name='houses_address']").val(),
				'type' :$("select[name='type']").val(),
				'collo_start_time':$("input[name='collo_start_time']").val(),
				'collo_end_time':$("input[name='collo_end_time']").val(),
				'total_month' :$("input[name='total_month']").val(),
				'owner' :$("input[name='owner']").val(),
				'owner_tel' :$("input[name='owner_tel']").val(),
				'owner_idcard' :$("input[name='owner_idcard']").val(),
				'pay_ditch' :$("input[name='pay_ditch']").val(),
				'agency_id' :$("select[name='agency_id']").val(),
				'broker_id' :$("select[name='broker_id']").val(),
				'broker_tel' :$("input[name='broker_tel']").val(),
				'rental' :$("input[name='rental']").val(),
				'pay_type' :$("select[name='pay_type']").val(),
				'rental_total' :$("input[name='rental_total']").val(),
				'desposit' :$("input[name='desposit']").val(),
				'penal_sum' :$("input[name='penal_sum']").val(),
				'tax_type' :$("select[name='tax_type']").val(),
				'property_manage_assume' :$("select[name='property_manage_assume']").val(),
				'property_fee' :$("input[name='property_fee']").val(),
				'agency_commission' :$("input[name='agency_commission']").val(),
				'rent_free_time' :$("input[name='rent_free_time']").val(),
				'desposit_type' :$("input[name='desposit_type']").val(),
				'divide_a_agency_id' :$("select[name='divide_a_agency_id']").val(),
				'divide_a_broker_id' :$("select[name='divide_a_broker_id']").val(),
				'divide_a_money' :$("input[name='divide_a_money']").val(),
				'divide_b_agency_id' :$("select[name='divide_b_agency_id']").val(),
				'divide_b_broker_id' :$("select[name='divide_b_broker_id']").val(),
				'divide_b_money' :$("input[name='divide_b_money']").val(),
				'out_agency_id' :$("select[name='out_agency_id']").val(),
				'out_broker_id' :$("select[name='out_broker_id']").val(),
				'stop_agreement_num' :$("input[name='stop_agreement_num']").val(),
				'list_items' :$("input[name='list_items']").val(),
				'remarks' :$("textarea[name='remarks']").val(),
				'signing_time' :$("input[name='signing_time']").val()
			},
			dataType: 'json',
			success: function(data){
				if(data['result'] == 'ok'){
						$("#js_prompt").text('托管合同修改成功！');
						openWin('js_pop');
						setTimeout(function(){location.href="/collocation_contract/index"},2000);
				}else if(data['result'] == 'no'){
						$("#js_prompt").text('托管合同修改失败！');
						openWin('js_pop');
						setTimeout(function(){location.href="/collocation_contract/index"},2000);
				}else if(data['result'] == '0'){
					$("#js_prompt2").text(data['msg']);
					openWin('js_pop_false');
				}
			}
		});
	}
});
//添加托管下出租合同的判断
$(function(){
	$("#jsUpForm_rent").validate({
        errorPlacement: function(error, element) {
			error.appendTo(element.parents(".js_fields").find(".errorBox"));
        },
        submitHandler:function(form)
        {	var action = $('#action').val();
			//如果是弹窗的形式就得隐藏$("#js_pop_add_attendance").attr('display','none');
            collocation_rent_add();
        },
        rules:{
			//托管合同编号
			 collocation_id:{
				required: true,
				maxlength:30
			 },
			//出租合同编号
			 collo_rent_id:{
				required: true
			 },
			//楼盘名称
			houses_name:{
				required: true
			 },
			//所属经纪人门店id
			 agency_id_a:{
				required: true
			 },
			//所属经纪人id
			 broker_id_a:{
				required: true
			 },
			//房源地址
			 houses_address:{
				required: true,
				maxlength:40
			 },
			//出租时间
			 rent_start_time:{
				required: true
			 },
			 rent_end_time:{
				required: true,
				isTimeRent: true
			 },
			//签约时间
			 signing_time:{
				required: true
			 },
			//客户姓名
			customer_name:{
				required: true
			},
			//联系方式
			customer_tel:{
				required: true,
				isZWNo: true,
				isZMNo: true
			},
			//签约门店
			agency_id:{
				required: true
			},
			//签约人
			broker_id:{
				required: true
			},
			//签约人联系方式
			broker_tel:{
				required: true,
				isZWNo: true,
				isZMNo: true
			},
			//每月租金
			rental:{
				required: true
			},
			//付款方式
			pay_type:{
				required: true
			},
			//租金总额
			rental_total:{
				required: true
			},
			//押金金额
			desposit:{
				required: true
			}
		 },
        messages:{
			collocation_id:{
				required: '请选择托管合同编号',
				maxlength:'合同编号最多30个字符'
			},
			collo_rent_id:{
				required: '请填写出租合同编号'
			},
            houses_name:{
                required: '请选择楼盘'
            },
			agency_id_a:{
                required: '请选择所属经纪门店'
            },
			broker_id_a:{
                required: '请选择所属经纪人'
            },
            houses_address:{
				required: '请填写房源地址',
				maxlength:'房源地址最多40个字符'
            },
			rent_start_time:{
                required: '请选择出租开始时间'
            },
			rent_end_time:{
                required: '请选择出租结束时间',
				isTimeRent: '停租时间不能早于起租时间'
            },
			signing_time:{
				required: '请选择签约时间'
			},
			customer_name:{
				required: '请填写客户姓名'
			},
			customer_tel:{
				required: '请填写客户联系方式',
				isZWNo: '电话号码不能有中文',
				isZMNo: '请填写正确的电话号码格式'
			},
			agency_id:{
				required: '请选择签约公司'
			},
			broker_id:{
				required: '请选择签约人'
			},
			broker_tel:{
				required: '请填写签约人联系方式',
				isZWNo: '电话号码不能有中文',
				isZMNo: '请填写正确的电话号码格式'
			},
			rental:{
				required: '请填写每月租金'
			},
			pay_type:{
				required: '请选择付款方式'
			},
			rental_total:{
				required: '请填写租金总额'
			},
			desposit:{
				required: '请填写押金金额'
			}
        }
    });
	//出租合同提交初审资料
	function collocation_rent_add(){
		var c_id = $('#c_id').val();
		var action = $('#action').val();
		$.ajax({
			type: 'POST',
			url: '/collocation_contract/add_rent_contract/',
			data:{
				'submit_flag' : $("input[name='submit_flag']").val(),
				'c_id' :$("input[name='c_id']").val(),
				'collocation_id' :$("input[name='collocation_id']").val(),
				'company_id' :$("input[name='company_id']").val(),
				'collo_rent_id' :$("input[name='collo_rent_id']").val(),
				'block_name' :$("input[name='block_name']").val(),
				'block_id' :$("input[name='block_id']").val(),
				'agency_id_a' :$("select[name=''agency_id_a']").val(),
				'broker_id_a' :$("select[name='broker_id_a']").val(),
				'houses_address' :$("input[name='houses_address']").val(),
				'rent_start_time' :$("input[name='rent_start_time']").val(),
				'rent_end_time' :$("input[name='rent_end_time']").val(),
				'rent_total_month' :$("input[name='rent_total_month']").val(),
				'signing_time' :$("input[name='signing_time']").val(),
				'customer_name' :$("input[name='customer_name']").val(),
				'customer_tel' :$("input[name='customer_tel']").val(),
				'customer_idcard' :$("input[name='customer_idcard']").val(),
				'pay_ditch' :$("input[name='pay_ditch']").val(),
				'agency_id' :$("select[name='agency_id']").val(),
				'broker_id' :$("select[name='broker_id']").val(),
				'broker_tel' :$("input[name='broker_tel']").val(),
				'rental' :$("input[name='rental']").val(),
				'pay_type' :$("select[name='pay_type']").val(),
				'rental_total' :$("input[name='rental_total']").val(),
				'desposit' :$("input[name='desposit']").val(),
				'penal_sum' :$("input[name='penal_sum']").val(),
				'tax_type' :$("select[name='tax_type']").val(),
				'property_fee' :$("input[name='property_fee']").val(),
				'agency_commission' :$("input[name='agency_commission']").val(),
				'rent_free_time':$("input[name='rent_free_time']").val(),
				'rent_type' :$("select[name='rent_type']").val(),
				'property_manage_assume' :$("input[name='property_manage_assume']").val(),
				'houses_preserve_agency_id' :$("select[name='houses_preserve_agency_id']").val(),
				'houses_preserve_broker_id' :$("select[name='houses_preserve_broker_id']").val(),
				'houses_preserve_money' :$("input[name='houses_preserve_money']").val(),
				'customer_preserve_agency_id' :$("select[name='customer_preserve_agency_id']").val(),
				'customer_preserve_broker_id' :$("select[name='customer_preserve_broker_id']").val(),
				'customer_preserve_money' :$("input[name='customer_preserve_money']").val(),
				'out_broker_agency_id' :$("select[name='out_broker_agency_id']").val(),
				'out_broker_broker_id' :$("select[name='out_broker_broker_id']").val(),
				'stop_agreement_num' :$("input[name='stop_agreement_num']").val(),
				'expire_time' :$("input[name='expire_time']").val(),
				'remark' :$("textarea[name='remark']").val()
			},
			dataType: 'json',
			success: function(data){
				if(data['result'] == 'ok'){
						$("#js_prompt").text('出租合同添加成功！');
						openWin('js_pop');
						if(action == '1'){
							setTimeout(function(){location.href="/collocation_contract/contract_detail/"+c_id+"/4"},2000);
						}else if(action == '2'){
							setTimeout(function(){location.href="/collocation_contract/rent_contract_list/"},2000);
						}

				}else if(data['result'] == 'no'){
						$("#js_prompt").text('出租合同添加失败！');
						openWin('js_pop');
						if(action == '1'){
							setTimeout(function(){location.href="/collocation_contract/index"+c_id+"/4"},2000);
						}else if(action == '2'){
							setTimeout(function(){location.href="/collocation_contract/rent_contract_list/"},2000);
						}

				}else if(data['result'] == '0'){
					$("#js_prompt2").text(data['msg']);
					openWin('js_pop_false');
				}
			}
		});
	}
});
//添加托管下出租合同的判断
$(function(){
	$("#jsUpForm_rent_modify").validate({
        errorPlacement: function(error, element) {
			error.appendTo(element.parents(".js_fields").find(".errorBox"));
        },
        submitHandler:function(form)
        {
			//如果是弹窗的形式就得隐藏$("#js_pop_add_attendance").attr('display','none');
            collocation_rent_modify();
        },
        rules:{
			//托管合同编号
			 collocation_id:{
				required: true,
				maxlength:30
			 },
			//出租合同编号
			 collo_rent_id:{
				required: true
			 },
			//楼盘名称
			 houses_name:{
				required: true
			 },
			//所属经纪人门店id
			 agency_id_a:{
				required: true
			 },
			//所属经纪人id
			 broker_id_a:{
				required: true
			 },
			//房源地址
			 houses_address:{
				required: true,
				maxlength:40
			 },
			//出租时间
			 rent_start_time:{
				required: true
			 },
			 rent_end_time:{
				required: true,
				isTimeRent: true
			 },
			//签约时间
			 signing_time:{
				required: true
			 },
			//客户姓名
			customer_name:{
				required: true
			},
			//联系方式
			customer_tel:{
				required: true,
				isZWNo: true,
				isZMNo: true
			},
			//付款渠道
			pay_ditch:{
				maxlength:20
			},
			//签约门店
			agency_id:{
				required: true
			},
			//签约人
			broker_id:{
				required: true
			},
			//签约人联系方式
			broker_tel:{
				required: true,
				isZWNo: true,
				isZMNo: true
			},
			//每月租金
			rental:{
				required: true
			},
			//付款方式
			pay_type:{
				required: true
			},
			//租金总额
			rental_total:{
				required: true
			},
			//押金金额
			desposit:{
				required: true
			},
			list_items:{
				maxlength:50
			},
			remarks:{
				maxlength:300
			}
		 },
        messages:{
			collocation_id:{
				required: '请选择托管合同编号',
				maxlength:'合同编号最多30个字符'
			},
			collo_rent_id:{
				required: '请填写出租合同编号'
			},
            houses_name:{
                required: '请选择楼盘'
            },
			agency_id_a:{
                required: '请选择所属经纪门店'
            },
			broker_id_a:{
                required: '请选择所属经纪人'
            },
            houses_address:{
				required: '请填写房源地址',
				maxlength:'房源地址最多40个字符'
            },
			rent_start_time:{
                required: '请选择出租开始时间'
            },
			rent_end_time:{
                required: '请选择出租结束时间',
				isTimeRent: '停租时间不能早于起租时间'
            },
			signing_time:{
				required: '请选择签约时间'
			},
			customer_name:{
				required: '请填写客户姓名'
			},
			customer_tel:{
				required: '请填写客户联系方式',
				isZWNo: '电话号码不能有中文',
				isZMNo: '请填写正确的电话号码格式'
			},
			pay_ditch:{
				maxlength:'付款渠道最多20个字符'
			},
			agency_id:{
				required: '请选择签约公司'
			},
			broker_id:{
				required: '请选择签约人'
			},
			broker_tel:{
				required: '请填写签约人联系方式',
				isZWNo: '电话号码不能有中文',
				isZMNo: '请填写正确的电话号码格式'
			},
			rental:{
				required: '请填写每月租金'
			},
			pay_type:{
				required: '请选择付款方式'
			},
			rental_total:{
				required: '请填写租金总额'
			},
			desposit:{
				required: '请填写押金金额'
			},
			list_items:{
				maxlength:'物品清单最多50个字符'
			},
			remarks:{
				maxlength:'备注最多300个字符'
			}
        }
    });
	//出租合同修改提交初审资料
	function collocation_rent_modify(){
		var id = $('#rent_modify_id').val();
		var c_id = $('#c_id').val();
		//var action = $('#action').val();
		$.ajax({
			type: 'POST',
			url: '/collocation_contract/rent_modify/'+id,
			data:{
				'id' : $("input[name='id']").val(),
				'modify_id' : $("input[name='modify_id']").val(),
				'submit_flag' : $("input[name='submit_flag']").val(),
				'c_id' :$("input[name='c_id']").val(),
				'collocation_id' :$("input[name='collocation_id']").val(),
				'company_id' :$("input[name='company_id']").val(),
				'collo_rent_id' :$("input[name='collo_rent_id']").val(),
				'block_name' :$("input[name='block_name']").val(),
				'block_id' :$("input[name='block_id']").val(),
				'agency_id_a' :$("select[name=''agency_id_a']").val(),
				'broker_id_a' :$("select[name='broker_id_a']").val(),
				'houses_address' :$("input[name='houses_address']").val(),
				'rent_start_time' :$("input[name='rent_start_time']").val(),
				'rent_end_time' :$("input[name='rent_end_time']").val(),
				'rent_total_month' :$("input[name='rent_total_month']").val(),
				'signing_time' :$("input[name='signing_time']").val(),
				'customer_name' :$("input[name='customer_name']").val(),
				'customer_tel' :$("input[name='customer_tel']").val(),
				'customer_idcard' :$("input[name='customer_idcard']").val(),
				'pay_ditch' :$("input[name='pay_ditch']").val(),
				'agency_id' :$("select[name='agency_id']").val(),
				'broker_id' :$("select[name='broker_id']").val(),
				'broker_tel' :$("input[name='broker_tel']").val(),
				'rental' :$("input[name='rental']").val(),
				'pay_type' :$("select[name='pay_type']").val(),
				'rental_total' :$("input[name='rental_total']").val(),
				'desposit' :$("input[name='desposit']").val(),
				'penal_sum' :$("input[name='penal_sum']").val(),
				'tax_type' :$("select[name='tax_type']").val(),
				'property_fee' :$("input[name='property_fee']").val(),
				'agency_commission' :$("input[name='agency_commission']").val(),
				'rent_free_time':$("input[name='rent_free_time']").val(),
				'rent_type' :$("select[name='rent_type']").val(),
				'property_manage_assume' :$("input[name='property_manage_assume']").val(),
				'houses_preserve_agency_id' :$("select[name='houses_preserve_agency_id']").val(),
				'houses_preserve_broker_id' :$("select[name='houses_preserve_broker_id']").val(),
				'houses_preserve_money' :$("input[name='houses_preserve_money']").val(),
				'customer_preserve_agency_id' :$("select[name='customer_preserve_agency_id']").val(),
				'customer_preserve_broker_id' :$("select[name='customer_preserve_broker_id']").val(),
				'customer_preserve_money' :$("input[name='customer_preserve_money']").val(),
				'out_broker_agency_id' :$("select[name='out_broker_agency_id']").val(),
				'out_broker_broker_id' :$("select[name='out_broker_broker_id']").val(),
				'stop_agreement_num' :$("input[name='stop_agreement_num']").val(),
				'expire_time' :$("input[name='expire_time']").val(),
				'remark' :$("textarea[name='remark']").val()
			},
			dataType: 'json',
			success: function(data){
				if(data['result'] == 'ok'){
						$("#js_prompt").text('出租合同修改成功！');
						openWin('js_pop');
						setTimeout(function(){location.href="/collocation_contract/rent_contract_list/"},2000);
				}else if(data['result'] == 'no'){
						$("#js_prompt").text('出租合同修改失败！');
						openWin('js_pop');
						setTimeout(function(){location.href="/collocation_contract/rent_contract_list/"},2000);
				}else if(data['result'] == '0'){
					$("#js_prompt2").text(data['msg']);
					openWin('js_pop_false');
				}
			}
		});
	}
});

//添加合同报备的判断
$(function(){

	$("#report_add_form").validate({
        errorPlacement: function(error, element) {
			element.siblings('.errorBox').html(error);
        },
        submitHandler:function(form)
        {
			report_add();
        },
        rules:{
			contract_type_add:{
				required: true
			},
			contract_number_add:{
				required: true,
				maxlength:30
			},
			contract_addr_add:{
				maxlength:40
			},
			contract_time_add:{
				required: true
			},
			contract_agency_add:{
				required: true
			},
			contract_broker_add:{
				required: true
			},
			signing_time:{
				required: true
			}
		 },
        messages:{
			contract_type_add:{
				required: '请选择交易方式'
			},
            contract_number_add:{
                required: '请填写合同编号',
				maxlength: '合同编号最多30个字符'
            },
			contract_addr_add:{
				maxlength:'房源地址最多40个字符'
			},
			contract_time_add:{
                required: '请选择签约时间'
            },
            contract_agency_add:{
				required: '请选择签约门店'
            },
			contract_broker_add:{
                required: '请选择签约人员'
            },
			signing_time:{
				required: '请选择签约时间'
			}
        }
    });
	//活动提交初审资料
	function report_add(){
		$.ajax({
			type: 'POST',
			url: '/contract/add_report',
			data: {
				id:$("input[name='contract_id']").val() ,
				type:$("#contract_type_add").val() ,
				number : $("#contract_number_add").val(),
				house_id : $("#contract_houseid_add").val(),
				block_name : $("#contract_blockname_add").val(),
				block_id : $("#contract_blockid_add").val(),
				house_addr : $("#contract_addr_add").val(),
				signing_time : $("#contract_time_add").val(),
				agency_id : $("#contract_agency_add").val(),
				broker_id : $("#contract_broker_add").val(),
				phone : $("#contract_phone_add").val(),
				remarks : $("#contract_remark_add").val()
			},
			dataType: 'json',
			success: function(data){
				if(data['result'] > 0){
					//window.parent.document.getElementById('GTipsCoverjs_modify_box').remove();
					//window.parent.document.getElementById('js_modify_box').style="width: 580px;height:352px;display:none";
					//window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
					$('#js_prompt1').html(data['msg']);
					openWin('js_pop_success');
				}
				else
				{
					$("#js_prompt2").text(data['msg']);
					openWin('js_pop_false');
				}
			}
		});
	}

	$("#addcont_form").validate({
        errorPlacement: function(error, element) {
			element.siblings('.errorBox').html(error);
        },
        submitHandler:function(form)
        {
            save_contract();
        },
        rules:{
			 number:{
				required: true,
				maxlength:30
			 },
			 buildarea:{
				required: true,
				isPrice: true,
				maxlength:10,
				isArea:true
			 },
			 house_addr:{
				required: true,
				maxlength:40
			 },
			 block_name:{
				required: true
			 },
			 signing_time:{
				required: true
			 },
			 type:{
				required: true
			 },
			 price:{
				required: true,
				isPrice: true
			 },
			 owner:{
				required: true,
				isTNAME:true,
				valid_name:true
			 },
			 owner_tel:{
				required: true,
				isZWNo:true
			 },
			 customer:{
				required: true,
				isTNAME:true,
				valid_name:true
			 },
			 customer_tel:{
				required: true,
				isZWNo:true
			 },
			 agency_id_a:{
				required: true
			 },
			 broker_id_a:{
				required: true,
				isTNAME:true,
				valid_name:true
			 },
			 broker_tel_a:{
				required: true,
				isZWNo:true
			 },
			 agency_id_b:{
				required: true
			 },
			 broker_id_b:{
				required: true,
				isTNAME:true,
				valid_name:true
			 },
			 broker_tel_b:{
				required: true,
				isZWNo:true
			 },
			 buy_type:{
				required: true
			 },
			 tax_pay_type:{
				required: true
			 },
			 owner_commission:{
				required: true,
				isPrice: true
			 },
			 customer_commission:{
				required: true,
				isPrice: true
			 },
			 other_income:{
				required: true,
				isPrice: true
			 },
			 commission_total:{
				required: true,
				isPrice: true
			 },
			 divide_percent:{
				max:100
			 }

		 },
        messages:{
			number:{
				required: '请输入合同编号',
				maxlength:'合同编号最多30个字符'
			 },
			 buildarea:{
				required: '请输入房源面积',
				isArea: '请输入正确的面积',
				maxlength:'房源面积最多输入10位'
			 },
			 house_addr:{
				required: '请输入房源地址',
				maxlength:'房源地址最多40个字符'
			 },
			 block_name:{
				required: '请输入楼盘名称'
			 },
			 signing_time:{
				required: '请输入签约时间'
			 },
			 type:{
				required: '请输入交易方式'
			 },
			 price:{
				required: '请输入成交金额',
				isPrice: '请输入正确的成交金额'
			 },
			 owner:{
				required: '请输入业主姓名'
			 },
			 owner_tel:{
				required: '请输入联系方式'
			 },
			 customer:{
				required: '请输入客户姓名'
			 },
			 customer_tel:{
				required: '请输入联系方式'
			 },
			 agency_id_a:{
				required: '请输入签约门店'
			 },
			 broker_id_a:{
				required: '请输入签约人姓名',
				isTNAME:'签约人姓名最多5个字符',
				valid_name:'签约人姓名只能包含汉字、字母、数字'
			 },
			 broker_tel_a:{
				required: '请输入联系方式'
			 },
			 agency_id_b:{
				required: '请输入签约门店'
			 },
			 broker_id_b:{
				required: '请输入签约人姓名',
				isTNAME:'签约人姓名最多5个字符',
				valid_name:'签约人姓名只能包含汉字、字母、数字'
			 },
			 broker_tel_b:{
				required: '请输入联系方式'
			 },
			 buy_type:{
				required: '请选择付款方式'
			 },
			 tax_pay_type:{
				required: '请选择税费支付'
			 },
			 owner_commission:{
				required: '请输入业主应付佣金',
				isPrice: '请输入正确的佣金'
			 },
			 customer_commission:{
				required: '请输入客户应付佣金',
				isPrice: '请输入正确的佣金'
			 },
			 other_income:{
				required:'请输入其他佣金',
				isPrice: '请输入正确的佣金'
			 },
			 commission_total:{
				required: '请输入佣金总计收入',
				isPrice: '请输入正确的总计收入'
			 },
			 divide_percent:{
				max:"分佣比例最大可填100%"
			 }
        }
    });


	$("#addcont_rent_form").validate({
        errorPlacement: function(error, element) {
			element.siblings('.errorBox').html(error);
        },
        submitHandler:function(form)
        {
            save_contract();
        },
        rules:{
			 number:{
				required: true,
				maxlength:30
			 },
			 buildarea:{
				required: true,
				isPrice: true,
				maxlength:10,
				isArea:true
			 },
			 house_addr:{
				required: true,
				maxlength:40
			 },
			 block_name:{
				required: true
			 },
			 signing_time:{
				required: true
			 },
			 type:{
				required: true
			 },
			 price:{
				required: true,
				isPrice: true
			 },
			 owner:{
				required: true,
				isTNAME:true,
				valid_name:true
			 },
			 owner_tel:{
				required: true,
				isZWNo:true
			 },
			 customer:{
				required: true,
				isTNAME:true,
				valid_name:true
			 },
			 customer_tel:{
				required: true,
				isZWNo:true
			 },
			 agency_id_a:{
				required: true
			 },
			 broker_id_a:{
				required: true,
				isTNAME:true,
				valid_name:true
			 },
			 broker_tel_a:{
				required: true,
				isZWNo:true
			 },
			 agency_id_b:{
				required: true
			 },
			 broker_id_b:{
				required: true,
				isTNAME:true,
				valid_name:true
			 },
			 broker_tel_b:{
				required: true,
				isZWNo:true
			 },
			 buy_type:{
				required: true
			 },
			 owner_commission:{
				required: true,
				isPrice: true
			 },
			 customer_commission:{
				required: true,
				isPrice: true
			 },
			 other_income:{
				required: true,
				isPrice: true
			 },
			 commission_total:{
				required: true,
				isPrice: true
			 },
			 start_time:{
				required: true,
				isTimeRange: true
			 },
			 end_time:{
				required: true,
				isTimeRange: true
			 },
			 deposit:{
				required: true,
				isPrice: true
			 },
			 divide_percent:{
				max:100
			 }

		 },
        messages:{
			number:{
				required: '请输入合同编号',
				maxlength:'合同编号最多30个字符'
			 },
			 buildarea:{
				required: '请输入房源面积',
				isArea: '请输入正确的面积',
				maxlength:'房源面积最多输入10位'
			 },
			 house_addr:{
				required: '请输入房源地址',
				maxlength:'房源地址最多40个字符'
			 },
			 block_name:{
				required: '请输入楼盘名称'
			 },
			 signing_time:{
				required: '请输入签约时间'
			 },
			 type:{
				required: '请输入交易方式'
			 },
			 price:{
				required: '请输入成交金额',
				isPrice: '请输入正确的成交金额'
			 },
			 owner:{
				required: '请输入房东姓名'
			 },
			 owner_tel:{
				required: '请输入联系方式'
			 },
			 customer:{
				required: '请输入租客姓名'
			 },
			 customer_tel:{
				required: '请输入联系方式'
			 },
			 agency_id_a:{
				required: '请输入签约门店'
			 },
			 broker_id_a:{
				required: '请输入签约人姓名',
				isTNAME:'签约人姓名最多5个字符',
				valid_name:'签约人姓名只能包含汉字、字母、数字'
			 },
			 broker_tel_a:{
				required: '请输入联系方式'
			 },
			 agency_id_b:{
				required: '请输入签约门店'
			 },
			 broker_id_b:{
				required: '请输入签约人姓名',
				isTNAME:'签约人姓名最多5个字符',
				valid_name:'签约人姓名只能包含汉字、字母、数字'
			 },
			 broker_tel_b:{
				required: '请输入联系方式'
			 },
			 buy_type:{
				required: '请选择付款方式'
			 },
			 owner_commission:{
				required: '请输入业主应付佣金',
				isPrice: '请输入正确的佣金'
			 },
			 customer_commission:{
				required: '请输入客户应付佣金',
				isPrice: '请输入正确的佣金'
			 },
			 other_income:{
				required:'请输入其他佣金',
				isPrice: '请输入正确的佣金'
			 },
			 commission_total:{
				required: '请输入佣金总计收入',
				isPrice: '请输入正确的总计收入'
			 },
			 start_time:{
				required: '请输入起租时间',
				isTimeRange: '起租时间不能大于到期时间'
			 },
			 end_time:{
				required: '请输入到期时间',
				isTimeRange: '起租时间不能大于到期时间'
			 },
			 deposit:{
				required: '请输入押金',
				isPrice: '请输入正确的押金'
			 },
			 divide_percent:{
				max:"分佣比例最大可填100%"
			 }
        }
    });

	//提交合同详情资料
	function save_contract(){
		$.ajax({
			type: 'POST',
			url: '/contract/save_contract',
			data: {
				submit_flag : $("input[name='submit_flag']").val(),
				id : $("input[name='contract_id']").val(),
				type : $("select[name='type']").val() ,
				number : $("input[name='number']").val(),
				house_id : $("input[name='house_id']").val(),
				block_name : $("input[name='block_name']").val(),
				block_id : $("input[name='block_id']").val(),
				house_addr : $("input[name='house_addr']").val(),
				pay_type : $("select[name='pay_type']").val(),
				price : $("input[name='price']").val(),
				price_type : $("select[name='price_type']").val(),
				is_cooperate : $("input[name='is_cooperate']:checked").val(),
				buildarea : $("input[name='buildarea']").val(),
				order_sn : $("input[name='order_sn']").val(),
				sell_type : $("select[name='sell_type']").val(),
				signing_time : $("input[name='signing_time']").val(),
				owner : $("input[name='owner']").val(),
				owner_tel : $("input[name='owner_tel']").val(),
				owner_idcard : $("input[name='owner_idcard']").val(),
				agency_id_a : $("select[name='agency_id_a']").val(),
				broker_id_a : $("select[name='broker_id_a']").val(),
				broker_tel_a : $("input[name='broker_tel_a']").val(),
				customer : $("input[name='customer']").val(),
				customer_id : $("input[name='customer_id']").val(),
				customer_tel : $("input[name='customer_tel']").val(),
				customer_idcard : $("input[name='customer_idcard']").val(),
				agency_id_b : $("select[name='agency_id_b']").val(),
				broker_id_b : $("select[name='broker_id_b']").val(),
				broker_tel_b : $("input[name='broker_tel_b']").val(),
				buy_type : $("select[name='buy_type']").val(),
				shoufu : $("input[name='shoufu']").val(),
				loan : $("input[name='loan']").val(),
				business_tax : $("input[name='business_tax']:checked").val(),
				tax : $("input[name='tax']:checked").val(),
				tax_pay_type : $("select[name='tax_pay_type']").val(),
				owner_tax_total : $("input[name='owner_tax_total']").val(),
				customer_tax_total : $("input[name='customer_tax_total']").val(),
				owner_commission : $("input[name='owner_commission']").val(),
				customer_commission : $("input[name='customer_commission']").val(),
				other_income : $("input[name='other_income']").val(),
				divide_percent : $("input[name='divide_percent']").val(),
				divide_money : $("input[name='divide_money']").val(),
				commission_total : $("input[name='commission_total']").val(),
				start_time:$("input[name='start_time']").val(),
				end_time:$("input[name='end_time']").val(),
				deposit:$("input[name='deposit']").val(),
				list_items:$("input[name='list_items']").val(),
				hydropower:$("input[name='hydropower']").val(),
				remarks:$("textarea[name='remarks']").val()
			},
			dataType: 'json',
			success: function(data){
				var submit_flag = $("input[name='submit_flag']").val();
				if(data['result'] == 'ok'){
					$("#js_prompt1").text(data['msg']);
					openWin('js_pop_success');
				}
				else
				{
					$("#js_prompt2").text(data['msg']);
					openWin('js_pop_false');
				}
			}
		});
	}

	$("#add_actual").validate({
        errorPlacement: function(error, element) {
			element.siblings('.errorBox').html(error);
        },
        submitHandler:function(form)
        {
            save_actual_flow();
        },
        rules:{
			 actual_money_type:{
				required: true
			 },
			 actual_flow_time:{
				required: true
			 },
			 actual_payment_method:{
				required: true
			 },
			 actual_collect_money: {
			     isPrice: true,
			     maxlength: 10
			 },
			 actual_pay_money: {
			     isPrice: true,
			     maxlength: 10
			 },
			 actual_remark:{
			    maxlength:300
			 }
		 },
        messages:{
			 actual_money_type:{
				required: '请选择款类'
			 },
			 actual_flow_time:{
				required: '请选择收付时间'
			 },
			 actual_payment_method:{
				required: '请选择收付方式'
			 },
			 actual_collect_money: {
			     isPrice: '请输入数字字符',
			     maxlength: '填写金额超过限制金额'
			 },
			 actual_pay_money: {
			     isPrice: '请输入数字字符',
			     maxlength: '填写金额超过限制金额'
			 },
			 should_remark:{
			    maxlength:"备注最多300字"
	        }
        }
    });

	//提交实收实付资料
	function save_actual_flow(){
		$.ajax({
			type: 'get',
			url: '/contract/add_flow',
			data: {
				id:$("#flow_id").val(),
				'flow_type':'actual',
				'c_id':$("#contract_id").val(),
				'money_type':$("select[name='actual_money_type']").val(),
				'collect_type':$("select[name='actual_collect_type']").val(),
				'collect_money':$("input[name='actual_collect_money']").val(),
				'pay_type':$("select[name='actual_pay_type']").val(),
				'pay_money':$("input[name='actual_pay_money']").val(),
				'flow_time':$("input[name='actual_flow_time']").val(),
				'payment_method':$("select[name='actual_payment_method']").val(),
				'remark':$("textarea[name='actual_remark']").val(),
				'flow_agency_id':$("select[name='actual_flow_agency']").val(),
				'flow_broker_id':$("select[name='actual_flow_broker']").val(),
				'counter_fee':$("input[name='actual_counter_fee']").val(),
				'docket':$("input[name='actual_docket']").val(),
				'docket_type':$("select[name='actual_docket_type']").val()
			},
			dataType: 'json',
			success: function(data){
				var id = $("#flow_id").val();
				closeParentWin('js_actual_pop');
				if(data['result'] == 'ok'){
					window.parent.frames["iframepage"].location=window.parent.frames["iframepage"].location;
					window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
					window.parent.window.openWin('js_pop_success');				}
				else
				{
					$("#js_prompt2").text(data['msg']);
					openWin('js_pop_false');
				}
			}
		});
	}

	$("#add_should").validate({
        errorPlacement: function(error, element) {
			element.siblings('.errorBox').html(error);
        },
        submitHandler:function(form)
        {
            save_should_flow();
        },
        rules:{
			 should_money_type:{
				required: true
			 },

			 should_collect_money: {
			     isPrice: true,
			     maxlength: 10
			 },
			 should_flow_time: {
			     required: true
			 },
			 should_pay_money: {
			     isPrice: true,
			     maxlength: 10
			 },

			 should_remark: {
			     maxlength: 300
			 }
		 },
        messages:{
			 should_money_type:{
				required: '请选择款类'
			 },

			 should_collect_money: {
			     isPrice: '请输入数字字符',
			     maxlength: '填写金额超过限制金额'
			 },
			 should_flow_time: {
			     required: '请选择收付时间'
			 },
			 should_pay_money: {
			     isPrice: '请输入数字字符',
			     maxlength: '填写金额超过限制金额'
			 },
			 should_remark:{
			    maxlength:"备注最多300字"
			 }
        }
    });

	//提交实收实付资料
	function save_should_flow(){
		$.ajax({
			type: 'get',
			url: '/contract/add_flow',
			data: {
				id:$("#flow_id").val(),
				'flow_type':$('#flow_type').val(),
				'c_id':$("#contract_id").val(),
				'money_type':$("select[name='should_money_type']").val(),
				'collect_type':$("select[name='should_collect_type']").val(),
				'collect_money':$("input[name='should_collect_money']").val(),
				'pay_type':$("select[name='should_pay_type']").val(),
				'pay_money':$("input[name='should_pay_money']").val(),
				'flow_time':$("input[name='should_flow_time']").val(),
				'remark':$("textarea[name='should_remark']").val()
			},
			dataType: 'json',
			success: function(data){
				var id = $("#flow_id").val();
				closeParentWin('js_should_pop');
				if(data['result'] == 'ok'){
					if(data['num']==1 ){
						window.parent.window.show_actual_add();
					}
					window.parent.frames["iframepage"].location=window.parent.frames["iframepage"].location;
					window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
					window.parent.window.openWin('js_pop_success');
				}
				else
				{
					$("#js_prompt2").text(data['msg']);
					openWin('js_pop_false');
				}
			}
		});
	}


	$("#divide_form").validate({
        errorPlacement: function(error, element) {
			element.siblings('.errorBox').html(error);
        },
        submitHandler:function(form)
        {
            save_divide();
        },
        rules:{
			 divide_percent:{
				required: true,
				isPrice:true,
				max:100
			 },
			 divide_price:{
				required: true,
				isPrice:true
			 },
			 divide_type:{
				required: true
			 },
			 agency_id:{
				required: true
			 },
			 broker_id:{
				required: true
			 },
			 achieve_agency_id_b:{
				required: true
			 },
			 achieve_broker_id_b:{
				required: true
			 },
			 achieve_agency_id_a:{
				required: true
			 },
			 achieve_broker_id_a:{
				required: true
			 }
		 },
        messages:{
			 divide_percent:{
				required: '请填写分成比例',
				isPrice:"请填写正确的分成比例",
				max:'分佣比例最大为100%'
			 },
			 divide_price:{
				required: '请填写实际分成金额',
				isPrice:"请填写正确的实际分成金额"
			 },
			 divide_type:{
				required: '请选择分成描述'
			 },
			 agency_id:{
				required: '请填写归属门店'
			 },
			 broker_id:{
				required: '请选择归属经纪人'
			 },
			 achieve_agency_id_b:{
				required: '请选择业绩归属门店'
			 },
			 achieve_broker_id_b:{
				required: '请选择业绩归属经纪人'
			 },
			 achieve_agency_id_a:{
				required: '请选择业绩归属区域'
			 },
			 achieve_broker_id_a:{
				required: '请选择业绩归属经纪人'
			 }
        }
    });

	//提交业绩分成资料
	function save_divide(){
		var percent1 = $("input[name='divide_percent']").val();
		var total1 = $("#percent_total").val();
		if(percent1> 0){
			$.ajax({
				type: 'post',
				url: '/contract/divide_manage',
				data: {
					divide_id:$("#divide_id").val(),
					c_id:$("#contract_id").val(),
					divide_percent:$("input[name='divide_percent']").val(),
					divide_price:$("input[name='divide_price']").val(),
					divide_type:$("select[name='divide_type']").val(),
					agency_id:$("select[name='agency_id']").val(),
					broker_id:$("select[name='broker_id']").val(),
					achieve_agency_id_b:$("select[name='achieve_agency_id_b']").val(),
					achieve_broker_id_b:$("select[name='achieve_broker_id_b']").val(),
					achieve_agency_id_a:$("select[name='achieve_agency_id_a']").val(),
					achieve_broker_id_a:$("select[name='achieve_broker_id_a']").val()
				},
				dataType: 'json',
				success: function(data){
					var id = $("#divide_id").val();
					closeParentWin('js_divide_pop');
					if(data['result'] == 'ok'){
						window.parent.frames["iframepage"].location=window.parent.frames["iframepage"].location;
						window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
						window.parent.window.openWin('js_pop_success');
					}
					else
					{
						$("#js_prompt2").text(data['msg']);
						openWin('js_pop_false');
					}
				}
			});
		}else{
			$("#should_divide_money").text('');
			$("#percent_error").text("");
		}
	}
	$("#earnest_edit_form").validate({
        errorPlacement: function(error, element) {
			element.siblings('.errorBox').html(error);
        },
        submitHandler:function(form)
        {
            sumbit_earnest();
        },
        rules:{
            earnest_price:{
                required:true,
                number:true,
                min:1
            },
            block_name:{
                required: true
            },
            address:{
                required: true
            },
			seller_owner:{
				required: true,
				isTNAME:true,
				valid_name:true
			},
			seller_telno:{
				required: true,
				isZMNo:true,
				isZWNo:true
			},
			buyer_owner:{
				required: true,
				isTNAME:true,
				valid_name:true
			},
			buyer_telno:{
				required: true,
				isZMNo:true,
				isZWNo:true
			},
			collection_time:{
				required: true
			},
			status:{
			    required: true,
                min:1
			},
            payee_agency_id:{
                required: true,
				min:1
            },
            payee_broker_id:{
                required: true,
				min:1
            },
            collect_type:{
                required: true,
				min:1
            }
		 },
        messages:{
            earnest_price:{
                required:'请填写诚意金额',
                number:'诚意金额必须为数字',
                min:'诚意金额不能小于1'
            },
            block_name:{
                required:"请填写楼盘名"
            },
			address:{
				required:"请填写房源地址"
			},
            seller_owner:{
                required: '请填写业主姓名'
            },
            seller_telno:{
                required: "请填写联系方式"
            },
            buyer_owner:{
                required: '请填写客户姓名'
            },
            buyer_telno:{
                required: "请填写联系方式"
            },
            collection_time:{
                required: "请填写收款日期"
            },
            status:{
                required: '请选诚意金状态',
                min:'请选诚意金状态'
            },
            payee_agency_id:{
                required: "请选收款人",
				min:'请选收款人'
            },
            payee_broker_id:{
                required: "请选收款人",
				min:'请选收款人'
            },
            collect_type:{
                required: "请选收款方式",
				min:'请选收款方式'
            }
        }
    });

	function sumbit_earnest()
	{
		$.ajax({
			type: 'POST',
			url: '/contract_earnest_money/save/',
			data: {
				id : $("input[name='earnest_money_id']").val(),
				trade_type : $("select[name='trade_type']").val(),
				house_id : $("input[name='house_id']").val(),
				sell_type : $("select[name='sell_type']").val(),
				intension_price : $("input[name='intension_price']").val(),
				block_name : $("input[name='block_name']").val(),
				block_id : $("input[name='block_id']").val(),
				address : $("input[name='address']").val(),
				seller_owner : $("input[name='seller_owner']").val(),
				seller_telno : $("input[name='seller_telno']").val(),
				seller_idcard : $("input[name='seller_idcard']").val(),
				buyer_owner : $("input[name='buyer_owner']").val(),
				buyer_telno : $("input[name='buyer_telno']").val(),
				buyer_idcard : $("input[name='buyer_idcard']").val(),
				earnest_price : $("input[name='earnest_price']").val(),
				collection_time : $("input[name='collection_time']").val(),
				status : $("select[name='status']").val(),
				payee_agency_id : $("select[name='payee_agency_id']").val(),
				payee_broker_id : $("select[name='payee_broker_id']").val(),
				collect_type : $("select[name='collect_type']").val(),
				refund_type : $("select[name='refund_type']").val(),
				refund_reason : $("input[name='refund_reason']").val(),
				remark:$("textarea[name='remark']").val()
			},
			dataType: 'json',
			success: function(data){
				var submit_flag = data['id'];
				if(data['result'] == -1){
					$("#js_prompt").text('没有权限修改诚意金状态！');
					openWin('js_pop_success');
				} else if(data['result'] > 0){
					if(submit_flag > 0){
						$("#js_prompt").text('诚意金编辑成功！');
					}else{
						$("#js_prompt").text('诚意金新增成功！');
					}
					openWin('js_pop_success');
				}
				else
				{
					if(submit_flag > 0){
						$("#js_prompt").text('诚意金编辑失败！');
					}else{
						$("#js_prompt").text('诚意金新增失败！');
					}
					openWin('js_pop_success');
				}
			}
		});
	}

	$("#warrant_form").validate({
        errorPlacement: function(error, element) {
			element.siblings('.errorBox').html(error);
        },
        submitHandler:function(form)
        {
            save_warrant();
        },
        rules:{
			warrant_remark:{
				maxlength:300
			},
			remind_time:{
				required:true
			},
			remind_agency_id:{
				required:true
			},
			remind_broker_id:{
				required:true
			},
			remind_remark:{
				maxlength:300
			}
		 },
        messages:{
			warrant_remark:{
				maxlength:"备注最多300字"
			},
			remind_time:{
				required:'请填写提醒时间'
			},
			remind_agency_id:{
				required:'请填写提醒人'
			},
			remind_broker_id:{
				required:'请填写提醒人'
			},
			remind_remark:{
				maxlength:"提醒信息最多300字"
			}
        }
    });

	//提交权证步骤
	function save_warrant(){
		var arr = new Array;
		$("input[name='step']:checked").each(function(){
		   arr.push($(this).val());
		})
		if(arr.length>0){
			if(arr.length<=3){
				$.ajax({
					type: 'post',
					url: '/contract/modify_temp_step',
					data: {
						'stage_id':$("#stage_id").val(),
						'c_id':$("#contract_id").val(),
						'stage':arr,
						'warrant_remark':$("textarea[name='warrant_remark']").val(),
						'is_remind':$("input[name='is_remind']:checked").val(),
						'remind_agency_id':$("select[name='remind_agency_id']").val(),
						'remind_broker_id':$("select[name='remind_broker_id']").val(),
						'remind_remark':$("textarea[name='remind_remark']").val(),
						'remind_time':$("input[name='remind_time']").val()
					},
					dataType: 'json',
					success: function(data){
						var id = $("#stage_id").val();
						$("#js_temp_pop").hide();
						$("#GTipsCoverjs_temp_pop").remove();
						closeParentWin('js_addtemp_pop');
						if(data['result'] == 'ok'){
							window.parent.frames["iframepage"].location=window.parent.frames["iframepage"].location;
							window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
							window.parent.window.openWin('js_pop_success');
						}
						else
						{
							$("#js_prompt2").text(data['msg']);
							openWin('js_pop_false');
						}
					}
				});
			}else{
				$("#step_error").text('最多选择三个步骤！');
			}
		}else{
			$("#step_error").text('请至少选择一个步骤！');
		}
	}
})