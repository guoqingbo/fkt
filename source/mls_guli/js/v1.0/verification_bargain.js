$(function () {
  document.oncontextmenu = function (e) {
    return false;
  }//禁止右键
  if (!window.XMLHttpRequest) {
    $(".forms_scroll").scroll(function () {
      $(".ui-autocomplete").hide();
    })
  }
});
$.validator.addMethod("bargain_number", function (value, element, params) {
  var reg = /^\w{0,30}$/;
  if (reg.test(value)) {
    return true;
  }
}, "合同编号应小于30个字符");
/*$.validator.addMethod("house_address",function(value,element,params){
 var reg = /^\w{0,80}$/;
 if( reg.test(value) )
 {
 return  true;
 }
 },"房源地址应小于80个字符");*/
$.validator.addMethod("valid_name", function (value, element, params) {
  var reg = /^[A-Za-z0-9\u4e00-\u9fa5]+$/;
  if (reg.test(value)) {
    return true;
  }
}, "业主姓名只能包含汉字、字母、数字");

$.validator.addMethod("isCardNo", function (value, element, params) {
  var reg = /(^\d{15}$)|(^\d{18}$)|(^\[a-zA-Z]{6}$)|(^\d{17}(\d|X|x)$)/;
  if (reg.test(value)) {
    return true;
  }
}, "身份证输入不合法");


$.validator.addMethod("isZWNo", function (value, element, params) {
  var reg = /[\u0391-\uFFE5]/;
  if (!reg.test(value)) {
    return true;
  }
}, "电话不能有中文");

$.validator.addMethod("isZMNo", function (value, element, params) {
  var reg = /(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/;
  if (reg.test(value)) {
    return true;
  }
}, "电话只能包含7-13位数字和中划线&nbsp;");

$.validator.addMethod("isTNAME", function (value, element, params) {
  if (value.length <= 10) {
    return true;
  }
}, "业主姓名最多10个字符");

$.validator.addMethod("isPrice", function (value, element, params) {
  if (value != "") {
    var reg = /^\d+(\.\d{1,2})?$/;
    if (reg.test(value)) {
      return true;
    }
  } else {
    return true;
  }
}, "请输入正确的价格,保留两位小数");

$.validator.addMethod("isTimeRange", function (value, element, params) {
  var startime = $("input[name='start_time']").val();
  var endtime = $("input[name='end_time']").val();
  if (startime < endtime) {
    return true;
  }
}, "请输入正确的时间范围");

$.validator.addMethod("isArea", function (value, element, params) {
  var reg = /^[0-9]+(.[0-9]{1,2})?$/;///^[0-9]+(.[0-9]{1,2})?$/
  if (reg.test(value)) {
    return true;
  }
}, "面积只能是数字,小数可以保留两位");
//托管时间
$.validator.addMethod("isTimeCollo", function (value, element, params) {
  var startime = $("input[name='collo_start_time']").val();
  var endtime = $("input[name='collo_end_time']").val();
  if (startime < endtime) {
    return true;
  }
}, "托管结束时间不能早于托管开始时间");
//出租时间
$.validator.addMethod("isTimeRent", function (value, element, params) {
  var startime = $("input[name='rent_start_time']").val();
  var endtime = $("input[name='rent_end_time']").val();
  if (startime < endtime) {
    return true;
  }
}, "停租时间不能早于起租时间");

//添加托管合同的判断
$(function () {
  $("#jsUpForm").validate({
    errorPlacement: function (error, element) {
      error.appendTo(element.parents(".js_fields").find(".errorBox"));
    },
    submitHandler: function (form) {
      //如果是弹窗的形式就得隐藏$("#js_pop_add_attendance").attr('display','none');
      collocation_bargain_add();
    },
    rules: {
      //托管合同编号
      collocation_id: {
        required: true,
        maxlength: 30
      },
      //房源编号
      /* house_num:{
       required: true
       },*/
      //楼盘名称
      houses_name: {
        required: true
      },
      //房源面积
      houses_area: {
        required: true,
        isArea: true,
        maxlength: 10
      },
      //房源地址
      houses_address: {
        required: true,
        maxlength: 40
      },
      //托管时间
      collo_start_time: {
        required: true
      },
      collo_end_time: {
        required: true,
        isTimeCollo: true
      },
      //签约时间
      signing_time: {
        required: true
      },
      //业主名字
      owner: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      //业主联系方式
      owner_tel: {
        required: true,
        isZWNo: true,
        isZMNo: true
      },
      //签约门店
      department_id: {
        required: true
      },
      //签约人
      signatory_id: {
        required: true
      },
      //签约人联系方式
      signatory_tel: {
        required: true,
        isZWNo: true,
        isZMNo: true
      },
      //每月租金
      rental: {
        required: true,
        isPrice: true
      },
      //付款方式
      pay_type: {
        required: true
      },
      //租金总额
      rental_total: {
        required: true,
        isPrice: true
      },
      //押金金额
      desposit: {
        required: true,
        isPrice: true
      }
    },
    messages: {
      collocation_id: {
        required: '请填写托管合同编号',
        maxlength: '合同编号最多30个字符'
      },
      /*house_num:{
       required: '请填写房源编号'
       },*/
      houses_name: {
        required: '请选择楼盘'
      },
      houses_area: {
        required: '请填写房源面积',
        isArea: '面积只能是数字，且可以保留两位小数',
        maxlength: '房源面积不能超过10位'
      },
      houses_address: {
        required: '请填写房源地址',
        maxlength: '房源地址最多40个字符'
      },
      collo_start_time: {
        required: '请选择托管开始时间'
      },
      collo_end_time: {
        required: '请选择托管结束时间',
        isTimeCollo: '托管结束时间不能早于托管开始时间'
      },
      signing_time: {
        required: '请选择签约时间'
      },
      owner: {
        required: '请填写业主姓名',
        isTNAME: '业主姓名最多10个字符',
        valid_name: '业主姓名只能包含汉字、字母、数字'
      },
      owner_tel: {
        required: '请填写业主联系方式',
        isZWNo: '电话号码不能有中文',
        isZMNo: '请填写正确的电话号码格式'
      },
      department_id: {
        required: '请选择签约公司'
      },
      signatory_id: {
        required: '请选择签约人'
      },
      signatory_tel: {
        required: '请填写签约人联系方式',
        isZWNo: '电话号码不能有中文',
        isZMNo: '请填写正确的电话号码格式'
      },
      rental: {
        required: '请填写每月租金',
        isPrice: '请输入正确的租金'
      },
      pay_type: {
        required: '请选择付款方式'
      },
      rental_total: {
        required: '请填写租金总额',
        isPrice: '请输入正确的租金总额'
      },
      desposit: {
        required: '请填写押金金额',
        isPrice: '请输入正确的押金金额'
      }
    }
  });
  //托管合同提交初审资料
  function collocation_bargain_add() {
    $.ajax({
      type: 'POST',
      url: '/collocation_bargain/add_bargain/',
      data: {
        'submit_flag': $("input[name='submit_flag']").val(),
        'collocation_id': $("input[name='collocation_id']").val(),
        'house_id': $("input[name='house_id']").val(),
        'block_name': $("input[name='block_name']").val(),
        'block_id': $("input[name='block_id']").val(),
        'houses_area': $("input[name='houses_area']").val(),
        'houses_address': $("input[name='houses_address']").val(),
        'type': $("select[name='type']").val(),
        'collo_start_time': $("input[name='collo_start_time']").val(),
        'collo_end_time': $("input[name='collo_end_time']").val(),
        'total_month': $("input[name='total_month']").val(),
        'owner': $("input[name='owner']").val(),
        'owner_tel': $("input[name='owner_tel']").val(),
        'owner_idcard': $("input[name='owner_idcard']").val(),
        'pay_ditch': $("input[name='pay_ditch']").val(),
        'department_id': $("select[name='department_id']").val(),
        'signatory_id': $("select[name='signatory_id']").val(),
        'signatory_tel': $("input[name='signatory_tel']").val(),
        'rental': $("input[name='rental']").val(),
        'pay_type': $("select[name='pay_type']").val(),
        'rental_total': $("input[name='rental_total']").val(),
        'desposit': $("input[name='desposit']").val(),
        'penal_sum': $("input[name='penal_sum']").val(),
        'tax_type': $("select[name='tax_type']").val(),
        'property_manage_assume': $("select[name='property_manage_assume']").val(),
        'property_fee': $("input[name='property_fee']").val(),
        'department_commission': $("input[name='department_commission']").val(),
        'rent_free_time': $("input[name='rent_free_time']").val(),
        'desposit_type': $("input[name='desposit_type']").val(),
        'divide_a_department_id': $("select[name='divide_a_department_id']").val(),
        'divide_a_signatory_id': $("select[name='divide_a_signatory_id']").val(),
        'divide_a_money': $("input[name='divide_a_money']").val(),
        'divide_b_department_id': $("select[name='divide_b_department_id']").val(),
        'divide_b_signatory_id': $("select[name='divide_b_signatory_id']").val(),
        'divide_b_money': $("input[name='divide_b_money']").val(),
        'out_department_id': $("select[name='out_department_id']").val(),
        'out_signatory_id': $("select[name='out_signatory_id']").val(),
        'stop_agreement_num': $("input[name='stop_agreement_num']").val(),
        'list_items': $("input[name='list_items']").val(),
        'remarks': $("textarea[name='remarks']").val(),
        'signing_time': $("input[name='signing_time']").val()
      },
      dataType: 'json',
      success: function (data) {
        if (data['result'] == 'ok') {
          $("#js_prompt").text('托管合同添加成功！');
          openWin('js_pop');
          setTimeout(function () {
            location.href = "/collocation_bargain/index"
          }, 2000);
        } else if (data['result'] == 'no') {
          $("#js_prompt").text('托管合同添加失败！');
          openWin('js_pop');
          setTimeout(function () {
            location.href = "/collocation_bargain/index"
          }, 2000);
        } else if (data['result'] == '0') {
          $("#js_prompt2").text(data['msg']);
          openWin('js_pop_false');
        }
      }
    });
  }
});
//修改托管合同的判断
$(function () {
  $("#jsUpForm_modify").validate({
    errorPlacement: function (error, element) {
      error.appendTo(element.parents(".js_fields").find(".errorBox"));
    },
    submitHandler: function (form) {
      //如果是弹窗的形式就得隐藏$("#js_pop_add_attendance").attr('display','none');
      collocation_bargain_modify();
    },
    rules: {
      //托管合同编号
      collocation_id: {
        required: true,
        maxlength: 30
      },
      //房源编号
      /* house_num:{
       required: true
       },*/
      //楼盘名称
      houses_name: {
        required: true
      },
      //房源面积
      houses_area: {
        required: true,
        isArea: true,
        maxlength: 10
      },
      //房源地址
      houses_address: {
        required: true,
        maxlength: 40
      },
      //托管时间
      collo_start_time: {
        required: true
      },
      collo_end_time: {
        required: true,
        isTimeCollo: true
      },
      //签约时间
      signing_time: {
        required: true
      },
      //业主名字
      owner: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      //业主联系方式
      owner_tel: {
        required: true,
        isZWNo: true
      },
      //签约门店
      department_id: {
        required: true
      },
      //签约人
      signatory_id: {
        required: true
      },
      //签约人联系方式
      signatory_tel: {
        required: true,
        isZWNo: true
      },
      //每月租金
      rental: {
        required: true
      },
      //付款方式
      pay_type: {
        required: true
      },
      //租金总额
      rental_total: {
        required: true
      },
      //押金金额
      desposit: {
        required: true
      }
    },
    messages: {
      collocation_id: {
        required: '请填写托管合同编号',
        maxlength: '合同编号最多30个字符'
      },
      /*house_num:{
       required: '请填写房源编号'
       },*/
      houses_name: {
        required: '请选择楼盘'
      },
      houses_area: {
        required: '请填写房源面积',
        isArea: '面积只能是数字，且可以保留两位小数',
        maxlength: '房源面积不能超过10位'
      },
      houses_address: {
        required: '请填写房源地址',
        maxlength: '房源地址最多40个字符'
      },
      collo_start_time: {
        required: '请选择托管开始时间'
      },
      collo_end_time: {
        required: '请选择托管结束时间',
        isTimeCollo: '托管结束时间不能早于托管开始时间'
      },
      signing_time: {
        required: '请选择签约时间'
      },
      owner: {
        required: '请填写业主姓名'
      },
      owner_tel: {
        required: '请填写业主联系方式',
        isZWNo: '电话号码不能有中文',
        isZMNo: '请填写正确的电话号码格式'
      },
      department_id: {
        required: '请选择签约公司'
      },
      signatory_id: {
        required: '请选择签约人'
      },
      signatory_tel: {
        required: '请填写签约人联系方式',
        isZWNo: '电话号码不能有中文',
        isZMNo: '请填写正确的电话号码格式'
      },
      rental: {
        required: '请填写每月租金'
      },
      pay_type: {
        required: '请选择付款方式'
      },
      rental_total: {
        required: '请填写租金总额'
      },
      desposit: {
        required: '请填写押金金额'
      }
    }
  });
  //托管合同提交初审资料
  function collocation_bargain_modify() {
    var id = $('#modify_id').val();
    $.ajax({
      type: 'POST',
      url: '/collocation_bargain/modify/' + id,
      data: {
        'modify_id': $("input[name='modify_id']").val(),
        'submit_flag': $("input[name='submit_flag']").val(),
        'collocation_id': $("input[name='collocation_id']").val(),
        'house_id': $("input[name='house_id']").val(),
        'block_name': $("input[name='block_name']").val(),
        'block_id': $("input[name='block_id']").val(),
        'houses_area': $("input[name='houses_area']").val(),
        'houses_address': $("input[name='houses_address']").val(),
        'type': $("select[name='type']").val(),
        'collo_start_time': $("input[name='collo_start_time']").val(),
        'collo_end_time': $("input[name='collo_end_time']").val(),
        'total_month': $("input[name='total_month']").val(),
        'owner': $("input[name='owner']").val(),
        'owner_tel': $("input[name='owner_tel']").val(),
        'owner_idcard': $("input[name='owner_idcard']").val(),
        'pay_ditch': $("input[name='pay_ditch']").val(),
        'department_id': $("select[name='department_id']").val(),
        'signatory_id': $("select[name='signatory_id']").val(),
        'signatory_tel': $("input[name='signatory_tel']").val(),
        'rental': $("input[name='rental']").val(),
        'pay_type': $("select[name='pay_type']").val(),
        'rental_total': $("input[name='rental_total']").val(),
        'desposit': $("input[name='desposit']").val(),
        'penal_sum': $("input[name='penal_sum']").val(),
        'tax_type': $("select[name='tax_type']").val(),
        'property_manage_assume': $("select[name='property_manage_assume']").val(),
        'property_fee': $("input[name='property_fee']").val(),
        'department_commission': $("input[name='department_commission']").val(),
        'rent_free_time': $("input[name='rent_free_time']").val(),
        'desposit_type': $("input[name='desposit_type']").val(),
        'divide_a_department_id': $("select[name='divide_a_department_id']").val(),
        'divide_a_signatory_id': $("select[name='divide_a_signatory_id']").val(),
        'divide_a_money': $("input[name='divide_a_money']").val(),
        'divide_b_department_id': $("select[name='divide_b_department_id']").val(),
        'divide_b_signatory_id': $("select[name='divide_b_signatory_id']").val(),
        'divide_b_money': $("input[name='divide_b_money']").val(),
        'out_department_id': $("select[name='out_department_id']").val(),
        'out_signatory_id': $("select[name='out_signatory_id']").val(),
        'stop_agreement_num': $("input[name='stop_agreement_num']").val(),
        'list_items': $("input[name='list_items']").val(),
        'remarks': $("textarea[name='remarks']").val(),
        'signing_time': $("input[name='signing_time']").val()
      },
      dataType: 'json',
      success: function (data) {
        if (data['result'] == 'ok') {
          $("#js_prompt").text('托管合同修改成功！');
          openWin('js_pop');
          setTimeout(function () {
            location.href = "/collocation_bargain/index"
          }, 2000);
        } else if (data['result'] == 'no') {
          $("#js_prompt").text('托管合同修改失败！');
          openWin('js_pop');
          setTimeout(function () {
            location.href = "/collocation_bargain/index"
          }, 2000);
        } else if (data['result'] == '0') {
          $("#js_prompt2").text(data['msg']);
          openWin('js_pop_false');
        }
      }
    });
  }
});
//添加托管下出租合同的判断
$(function () {
  $("#jsUpForm_rent").validate({
    errorPlacement: function (error, element) {
      error.appendTo(element.parents(".js_fields").find(".errorBox"));
    },
    submitHandler: function (form) {
      var action = $('#action').val();
      //如果是弹窗的形式就得隐藏$("#js_pop_add_attendance").attr('display','none');
      collocation_rent_add();
    },
    rules: {
      //托管合同编号
      collocation_id: {
        required: true,
        maxlength: 30
      },
      //出租合同编号
      collo_rent_id: {
        required: true
      },
      //楼盘名称
      houses_name: {
        required: true
      },
      //所属经纪人门店id
      department_id_a: {
        required: true
      },
      //所属经纪人id
      signatory_id_a: {
        required: true
      },
      //房源地址
      houses_address: {
        required: true,
        maxlength: 40
      },
      //出租时间
      rent_start_time: {
        required: true
      },
      rent_end_time: {
        required: true,
        isTimeRent: true
      },
      //签约时间
      signing_time: {
        required: true
      },
      //客户姓名
      customer_name: {
        required: true
      },
      //联系方式
      customer_tel: {
        required: true,
        isZWNo: true,
        isZMNo: true
      },
      //签约门店
      department_id: {
        required: true
      },
      //签约人
      signatory_id: {
        required: true
      },
      //签约人联系方式
      signatory_tel: {
        required: true,
        isZWNo: true,
        isZMNo: true
      },
      //每月租金
      rental: {
        required: true
      },
      //付款方式
      pay_type: {
        required: true
      },
      //租金总额
      rental_total: {
        required: true
      },
      //押金金额
      desposit: {
        required: true
      }
    },
    messages: {
      collocation_id: {
        required: '请选择托管合同编号',
        maxlength: '合同编号最多30个字符'
      },
      collo_rent_id: {
        required: '请填写出租合同编号'
      },
      houses_name: {
        required: '请选择楼盘'
      },
      department_id_a: {
        required: '请选择所属经纪门店'
      },
      signatory_id_a: {
        required: '请选择所属经纪人'
      },
      houses_address: {
        required: '请填写房源地址',
        maxlength: '房源地址最多40个字符'
      },
      rent_start_time: {
        required: '请选择出租开始时间'
      },
      rent_end_time: {
        required: '请选择出租结束时间',
        isTimeRent: '停租时间不能早于起租时间'
      },
      signing_time: {
        required: '请选择签约时间'
      },
      customer_name: {
        required: '请填写客户姓名'
      },
      customer_tel: {
        required: '请填写客户联系方式',
        isZWNo: '电话号码不能有中文',
        isZMNo: '请填写正确的电话号码格式'
      },
      department_id: {
        required: '请选择签约公司'
      },
      signatory_id: {
        required: '请选择签约人'
      },
      signatory_tel: {
        required: '请填写签约人联系方式',
        isZWNo: '电话号码不能有中文',
        isZMNo: '请填写正确的电话号码格式'
      },
      rental: {
        required: '请填写每月租金'
      },
      pay_type: {
        required: '请选择付款方式'
      },
      rental_total: {
        required: '请填写租金总额'
      },
      desposit: {
        required: '请填写押金金额'
      }
    }
  });
  //出租合同提交初审资料
  function collocation_rent_add() {
    var c_id = $('#c_id').val();
    var action = $('#action').val();
    $.ajax({
      type: 'POST',
      url: '/collocation_bargain/add_rent_bargain/',
      data: {
        'submit_flag': $("input[name='submit_flag']").val(),
        'c_id': $("input[name='c_id']").val(),
        'collocation_id': $("input[name='collocation_id']").val(),
        'company_id': $("input[name='company_id']").val(),
        'collo_rent_id': $("input[name='collo_rent_id']").val(),
        'block_name': $("input[name='block_name']").val(),
        'block_id': $("input[name='block_id']").val(),
        'department_id_a': $("select[name=''department_id_a']").val(),
        'signatory_id_a': $("select[name='signatory_id_a']").val(),
        'houses_address': $("input[name='houses_address']").val(),
        'rent_start_time': $("input[name='rent_start_time']").val(),
        'rent_end_time': $("input[name='rent_end_time']").val(),
        'rent_total_month': $("input[name='rent_total_month']").val(),
        'signing_time': $("input[name='signing_time']").val(),
        'customer_name': $("input[name='customer_name']").val(),
        'customer_tel': $("input[name='customer_tel']").val(),
        'customer_idcard': $("input[name='customer_idcard']").val(),
        'pay_ditch': $("input[name='pay_ditch']").val(),
        'department_id': $("select[name='department_id']").val(),
        'signatory_id': $("select[name='signatory_id']").val(),
        'signatory_tel': $("input[name='signatory_tel']").val(),
        'rental': $("input[name='rental']").val(),
        'pay_type': $("select[name='pay_type']").val(),
        'rental_total': $("input[name='rental_total']").val(),
        'desposit': $("input[name='desposit']").val(),
        'penal_sum': $("input[name='penal_sum']").val(),
        'tax_type': $("select[name='tax_type']").val(),
        'property_fee': $("input[name='property_fee']").val(),
        'department_commission': $("input[name='department_commission']").val(),
        'rent_free_time': $("input[name='rent_free_time']").val(),
        'rent_type': $("select[name='rent_type']").val(),
        'property_manage_assume': $("input[name='property_manage_assume']").val(),
        'houses_preserve_department_id': $("select[name='houses_preserve_department_id']").val(),
        'houses_preserve_signatory_id': $("select[name='houses_preserve_signatory_id']").val(),
        'houses_preserve_money': $("input[name='houses_preserve_money']").val(),
        'customer_preserve_department_id': $("select[name='customer_preserve_department_id']").val(),
        'customer_preserve_signatory_id': $("select[name='customer_preserve_signatory_id']").val(),
        'customer_preserve_money': $("input[name='customer_preserve_money']").val(),
        'out_signatory_department_id': $("select[name='out_signatory_department_id']").val(),
        'out_signatory_signatory_id': $("select[name='out_signatory_signatory_id']").val(),
        'stop_agreement_num': $("input[name='stop_agreement_num']").val(),
        'expire_time': $("input[name='expire_time']").val(),
        'remark': $("textarea[name='remark']").val()
      },
      dataType: 'json',
      success: function (data) {
        if (data['result'] == 'ok') {
          $("#js_prompt").text('出租合同添加成功！');
          openWin('js_pop');
          if (action == '1') {
            setTimeout(function () {
              location.href = "/collocation_bargain/bargain_detail/" + c_id + "/4"
            }, 2000);
          } else if (action == '2') {
            setTimeout(function () {
              location.href = "/collocation_bargain/rent_bargain_list/"
            }, 2000);
          }

        } else if (data['result'] == 'no') {
          $("#js_prompt").text('出租合同添加失败！');
          openWin('js_pop');
          if (action == '1') {
            setTimeout(function () {
              location.href = "/collocation_bargain/index" + c_id + "/4"
            }, 2000);
          } else if (action == '2') {
            setTimeout(function () {
              location.href = "/collocation_bargain/rent_bargain_list/"
            }, 2000);
          }

        } else if (data['result'] == '0') {
          $("#js_prompt2").text(data['msg']);
          openWin('js_pop_false');
        }
      }
    });
  }
});
//添加托管下出租合同的判断
$(function () {
  $("#jsUpForm_rent_modify").validate({
    errorPlacement: function (error, element) {
      error.appendTo(element.parents(".js_fields").find(".errorBox"));
    },
    submitHandler: function (form) {
      //如果是弹窗的形式就得隐藏$("#js_pop_add_attendance").attr('display','none');
      collocation_rent_modify();
    },
    rules: {
      //托管合同编号
      collocation_id: {
        required: true,
        maxlength: 30
      },
      //出租合同编号
      collo_rent_id: {
        required: true
      },
      //楼盘名称
      houses_name: {
        required: true
      },
      //所属经纪人门店id
      department_id_a: {
        required: true
      },
      //所属经纪人id
      signatory_id_a: {
        required: true
      },
      //房源地址
      houses_address: {
        required: true,
        maxlength: 40
      },
      //出租时间
      rent_start_time: {
        required: true
      },
      rent_end_time: {
        required: true,
        isTimeRent: true
      },
      //签约时间
      signing_time: {
        required: true
      },
      //客户姓名
      customer_name: {
        required: true
      },
      //联系方式
      customer_tel: {
        required: true,
        isZWNo: true,
        isZMNo: true
      },
      //付款渠道
      pay_ditch: {
        maxlength: 20
      },
      //签约门店
      department_id: {
        required: true
      },
      //签约人
      signatory_id: {
        required: true
      },
      //签约人联系方式
      signatory_tel: {
        required: true,
        isZWNo: true,
        isZMNo: true
      },
      //每月租金
      rental: {
        required: true
      },
      //付款方式
      pay_type: {
        required: true
      },
      //租金总额
      rental_total: {
        required: true
      },
      //押金金额
      desposit: {
        required: true
      },
      list_items: {
        maxlength: 50
      },
      remarks: {
        maxlength: 300
      }
    },
    messages: {
      collocation_id: {
        required: '请选择托管合同编号',
        maxlength: '合同编号最多30个字符'
      },
      collo_rent_id: {
        required: '请填写出租合同编号'
      },
      houses_name: {
        required: '请选择楼盘'
      },
      department_id_a: {
        required: '请选择所属经纪门店'
      },
      signatory_id_a: {
        required: '请选择所属经纪人'
      },
      houses_address: {
        required: '请填写房源地址',
        maxlength: '房源地址最多40个字符'
      },
      rent_start_time: {
        required: '请选择出租开始时间'
      },
      rent_end_time: {
        required: '请选择出租结束时间',
        isTimeRent: '停租时间不能早于起租时间'
      },
      signing_time: {
        required: '请选择签约时间'
      },
      customer_name: {
        required: '请填写客户姓名'
      },
      customer_tel: {
        required: '请填写客户联系方式',
        isZWNo: '电话号码不能有中文',
        isZMNo: '请填写正确的电话号码格式'
      },
      pay_ditch: {
        maxlength: '付款渠道最多20个字符'
      },
      department_id: {
        required: '请选择签约公司'
      },
      signatory_id: {
        required: '请选择签约人'
      },
      signatory_tel: {
        required: '请填写签约人联系方式',
        isZWNo: '电话号码不能有中文',
        isZMNo: '请填写正确的电话号码格式'
      },
      rental: {
        required: '请填写每月租金'
      },
      pay_type: {
        required: '请选择付款方式'
      },
      rental_total: {
        required: '请填写租金总额'
      },
      desposit: {
        required: '请填写押金金额'
      },
      list_items: {
        maxlength: '物品清单最多50个字符'
      },
      remarks: {
        maxlength: '备注最多300个字符'
      }
    }
  });
  //出租合同修改提交初审资料
  function collocation_rent_modify() {
    var id = $('#rent_modify_id').val();
    var c_id = $('#c_id').val();
    //var action = $('#action').val();
    $.ajax({
      type: 'POST',
      url: '/collocation_bargain/rent_modify/' + id,
      data: {
        'id': $("input[name='id']").val(),
        'modify_id': $("input[name='modify_id']").val(),
        'submit_flag': $("input[name='submit_flag']").val(),
        'c_id': $("input[name='c_id']").val(),
        'collocation_id': $("input[name='collocation_id']").val(),
        'company_id': $("input[name='company_id']").val(),
        'collo_rent_id': $("input[name='collo_rent_id']").val(),
        'block_name': $("input[name='block_name']").val(),
        'block_id': $("input[name='block_id']").val(),
        'department_id_a': $("select[name=''department_id_a']").val(),
        'signatory_id_a': $("select[name='signatory_id_a']").val(),
        'houses_address': $("input[name='houses_address']").val(),
        'rent_start_time': $("input[name='rent_start_time']").val(),
        'rent_end_time': $("input[name='rent_end_time']").val(),
        'rent_total_month': $("input[name='rent_total_month']").val(),
        'signing_time': $("input[name='signing_time']").val(),
        'customer_name': $("input[name='customer_name']").val(),
        'customer_tel': $("input[name='customer_tel']").val(),
        'customer_idcard': $("input[name='customer_idcard']").val(),
        'pay_ditch': $("input[name='pay_ditch']").val(),
        'department_id': $("select[name='department_id']").val(),
        'signatory_id': $("select[name='signatory_id']").val(),
        'signatory_tel': $("input[name='signatory_tel']").val(),
        'rental': $("input[name='rental']").val(),
        'pay_type': $("select[name='pay_type']").val(),
        'rental_total': $("input[name='rental_total']").val(),
        'desposit': $("input[name='desposit']").val(),
        'penal_sum': $("input[name='penal_sum']").val(),
        'tax_type': $("select[name='tax_type']").val(),
        'property_fee': $("input[name='property_fee']").val(),
        'department_commission': $("input[name='department_commission']").val(),
        'rent_free_time': $("input[name='rent_free_time']").val(),
        'rent_type': $("select[name='rent_type']").val(),
        'property_manage_assume': $("input[name='property_manage_assume']").val(),
        'houses_preserve_department_id': $("select[name='houses_preserve_department_id']").val(),
        'houses_preserve_signatory_id': $("select[name='houses_preserve_signatory_id']").val(),
        'houses_preserve_money': $("input[name='houses_preserve_money']").val(),
        'customer_preserve_department_id': $("select[name='customer_preserve_department_id']").val(),
        'customer_preserve_signatory_id': $("select[name='customer_preserve_signatory_id']").val(),
        'customer_preserve_money': $("input[name='customer_preserve_money']").val(),
        'out_signatory_department_id': $("select[name='out_signatory_department_id']").val(),
        'out_signatory_signatory_id': $("select[name='out_signatory_signatory_id']").val(),
        'stop_agreement_num': $("input[name='stop_agreement_num']").val(),
        'expire_time': $("input[name='expire_time']").val(),
        'remark': $("textarea[name='remark']").val()
      },
      dataType: 'json',
      success: function (data) {
        if (data['result'] == 'ok') {
          $("#js_prompt").text('出租合同修改成功！');
          openWin('js_pop');
          setTimeout(function () {
            location.href = "/collocation_bargain/rent_bargain_list/"
          }, 2000);
        } else if (data['result'] == 'no') {
          $("#js_prompt").text('出租合同修改失败！');
          openWin('js_pop');
          setTimeout(function () {
            location.href = "/collocation_bargain/rent_bargain_list/"
          }, 2000);
        } else if (data['result'] == '0') {
          $("#js_prompt2").text(data['msg']);
          openWin('js_pop_false');
        }
      }
    });
  }
});

//添加合同报备的判断
$(function () {

  $("#report_add_form").validate({
    errorPlacement: function (error, element) {
      element.siblings('.errorBox').html(error);
    },
    submitHandler: function (form) {
      report_add();
    },
    rules: {
      bargain_type_add: {
        required: true
      },
      bargain_number_add: {
        required: true,
        maxlength: 30
      },
      bargain_addr_add: {
        maxlength: 40
      },
      bargain_time_add: {
        required: true
      },
      bargain_department_add: {
        required: true
      },
      bargain_signatory_add: {
        required: true
      },
      signing_time: {
        required: true
      }
    },
    messages: {
      bargain_type_add: {
        required: '请选择交易方式'
      },
      bargain_number_add: {
        required: '请填写合同编号',
        maxlength: '合同编号最多30个字符'
      },
      bargain_addr_add: {
        maxlength: '房源地址最多40个字符'
      },
      bargain_time_add: {
        required: '请选择签约时间'
      },
      bargain_department_add: {
        required: '请选择签约门店'
      },
      bargain_signatory_add: {
        required: '请选择签约人员'
      },
      signing_time: {
        required: '请选择签约时间'
      }
    }
  });
  //活动提交初审资料
  function report_add() {
    $.ajax({
      type: 'POST',
      url: '/bargain/add_report',
      data: {
        id: $("input[name='bargain_id']").val(),
        type: $("#bargain_type_add").val(),
        number: $("#bargain_number_add").val(),
        house_id: $("#bargain_houseid_add").val(),
        block_name: $("#bargain_blockname_add").val(),
        block_id: $("#bargain_blockid_add").val(),
        house_addr: $("#bargain_addr_add").val(),
        signing_time: $("#bargain_time_add").val(),
        department_id: $("#bargain_department_add").val(),
        signatory_id: $("#bargain_signatory_add").val(),
        phone: $("#bargain_phone_add").val(),
        remarks: $("#bargain_remark_add").val()
      },
      dataType: 'json',
      success: function (data) {
        if (data['result'] > 0) {
          //window.parent.document.getElementById('GTipsCoverjs_modify_box').remove();
          //window.parent.document.getElementById('js_modify_box').style="width: 580px;height:352px;display:none";
          //window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
          $('#js_prompt1').html(data['msg']);
          openWin('js_pop_success');
        }
        else {
          $("#js_prompt2").text(data['msg']);
          openWin('js_pop_false');
        }
      }
    });
  }

  $("#addcont_form").validate({
    errorPlacement: function (error, element) {
      element.siblings('.errorBox').html(error);
    },
    submitHandler: function (form) {
      save_add_bargain();
    },
    rules: {
      number: {
        required: true,
        maxlength: 30
      },
      house_addr: {
        required: true,
        maxlength: 40
      },
      bargain_type: {
        required: true
      },
      block_name: {
        required: true
      },
      signing_time: {
        required: true
      },
      owner: {
        required: true,
        isTNAME: true,
          // valid_name: true
      },
      owner_tel: {
        required: true,
        isZWNo: true
      },
      owner_idcard: {
        required: true,
      },
      customer: {
        required: true,
        isTNAME: true,
          //   valid_name: true
      },
      customer_tel: {
        required: true,
        isZWNo: true
      },
      customer_idcard: {
        required: true,
      },
      agency_name_a: {
        required: true
      },
      agency_name_b: {
        required: true
      },
    },
    messages: {
      number: {
        required: '请输入成交编号',
        maxlength: '成交编号最多30个字符'
      },
      house_addr: {
        required: '请输入物业地址',
        maxlength: '物业地址最多40个字符'
      },
      bargain_type: {
        required: '请选择成交类别'
      },
      block_name: {
        required: '请输入楼盘名称'
      },
      signing_time: {
        required: '请输入成交日期'
      },
      owner: {
        required: '请输入卖方姓名'
      },
      owner_tel: {
        required: '请输入联系方式'
      },
      owner_idcard: {
        required: '请输入卖方身份证号'
      },
      customer: {
        required: '请输入客户姓名'
      },
      customer_tel: {
        required: '请输入联系方式'
      },
      customer_idcard: {
        required: '请输入买方身份证号'
      },
      agency_name_a: {
        required: '请输入经纪人门店'
      },
      agency_name_b: {
        required: '请输入经纪人门店'
      }
    }
  });

    $("#modifycont_form_one").validate({
        errorPlacement: function (error, element) {
            element.siblings('.errorBox').html(error);
        },
        submitHandler: function (form) {
            save_modify_bargain_one();
        },
        rules: {
            number: {
                required: true,
                maxlength: 30
            },
            house_addr: {
                required: true,
                maxlength: 40
            },
            signing_time: {
                required: true
            },

            customer: {
                required: true,
                //   isTNAME: true,
                // valid_name: true
            },
            customer_tel: {
                required: true,
                isZWNo: true
            },
            agent_type: {
                required: true,
                //   isTNAME: true,
                // valid_name: true
            },
            block_name: {
                required: true,
            },
            developer: {
                required: true,
            },
            agent_company: {
                required: true,
            }
        },
        messages: {
            number: {
                required: '请输入合同编号',
                maxlength: '合同编号最多30个字符'
            },
            house_addr: {
                required: '请输入物业地址',
                maxlength: '物业地址最多40个字符'
            },
            signing_time: {
                required: '请输入签约时间'
            },
            customer: {
                required: '请输入客户姓名'
            },
            customer_tel: {
                required: '请输入联系方式'
            },
            agent_type: {
                required: '请选择代办类别'
            },
            block_name: {
                required: '请输入楼盘名称'
            },
            developer: {
                required: '请输入开发商'
            },
            agent_company: {
                required: '请输入代办公司'
            }

        }
    });
    $("#modifycont_form").validate({
        errorPlacement: function (error, element) {
            element.siblings('.errorBox').html(error);
        },
        submitHandler: function (form) {
            save_modify_bargain();
        },
        rules: {
            number: {
                required: true,
                maxlength: 30
            },
            signing_time: {
                required: true
            },
            signatory_id: {
                required: true
            },
            house_addr: {
                required: true,
                maxlength: 40
            },
            price: {
                required: true,
            },
            decoration_price: {
                required: true,
            },
            buildarea: {
                required: true,
            },
            certificate_number: {
                required: true,
            },
            district_id: {
                required: true,
            },
            house_type: {
                required: true,
            },
            land_nature: {
                required: true,
            },
            is_mortgage: {
                required: true,
            },
            is_evaluate: {
                required: true,
            },
            agency_name_a: {
                required: true
            },
            broker_name_a: {
                required: true,
            },
            broker_tel_a: {
                required: true,
            },
            signatory_company: {
                required: true,
            },
            warrant_inside: {
                required: true,
            },
            finance_id: {
                required: true,
            },
            owner: {
                required: true
                // isTNAME: true,
                // valid_name: true
            },
            owner_idcard: {
                required: true
            },
            show_trust_a: {
                required: true
            },
            owner_tel_1: {
                required: true,
                isZWNo: true,
                isZMNo: true
            },
            owner_tel_2: {
                isZWNo: true,
                isZMNo: true
            },
            owner_tel_3: {
                isZWNo: true,
                isZMNo: true
            },
            customer: {
                required: true,
                //isTNAME: true,
                // valid_name: true
            },
            customer_idcard: {
                required: true,
            },
            customer_tel_1: {
                required: true,
                isZWNo: true,
                isZMNo: true
            },
            customer_tel_2: {
                isZWNo: true,
                isZMNo: true
            },
            customer_tel_3: {
                isZWNo: true,
                isZMNo: true
            },
            show_trust_b: {
                required: true
            },
            buy_type: {
                required: true
            },
            tax_pay_type: {
                required: true
            },
            note_belong: {
                required: true
            },
            house_time: {
                required: true
            },
            tax_pay_tatal: {
                required: true
            }
        },
        messages: {
            number: {
                required: '请输入合同编号',
                maxlength: '合同编号最多30个字符'
            },
            signing_time: {
                required: '请输入签约时间'
            },
            house_addr: {
                required: '请输入物业地址',
                maxlength: '物业地址最多40个字符'
            },
            signatory_id: {
                required: '请选择签约人员'
            },
            price: {
                required: '请输入合同价'
            },
            decoration_price: {
                required: '请输入装修款'
            },
            buildarea: {
                required: '请输入建筑面积',
            },
            certificate_number: {
                required: '请输入产证编号',
            },
            district_id: {
                required: '请选择区域',
            },
            house_type: {
                required: '请选择房屋类型',
            },
            land_nature: {
                required: '请选择土地性质',
            },
            is_mortgage: {
                required: '请选择有无抵押',
            },
            is_evaluate: {
                required: '请选择是否评估',
            },
            agency_name_a: {
                required: '请输入成交门店'
            },
            broker_name_a: {
                required: '请输入经纪人',
            },
            broker_tel_a: {
                required: '请输入经纪人电话',
            },
            signatory_company: {
                required: '请选择签约公司',
            },
            warrant_inside: {
                required: '请选择权证人员',
            },
            finance_id: {
                required: '请选择理财人员',
            },
            owner: {
                required: '请输入卖方姓名'
            },
            owner_idcard: {
                required: '请输入身份卖方证号'
            },
            owner_tel_1: {
                required: '请输入卖方联系方式',
                isZWNo: '电话号码不能有中文',
                isZMNo: '请填写正确的电话号码格式'
            },
            owner_tel_2: {
                isZWNo: '电话号码不能有中文',
                isZMNo: '请填写正确的电话号码格式'
            },
            owner_tel_3: {
                isZWNo: '电话号码不能有中文',
                isZMNo: '请填写正确的电话号码格式'
            },
            customer: {
                required: '请输入买方姓名'
            },
            customer_idcard: {
                required: '请输入买方身份证号'
            },
            customer_tel_1: {
                required: '请输入买方联系方式',
                isZWNo: '电话号码不能有中文',
                isZMNo: '请填写正确的电话号码格式'
            },
            customer_tel_2: {
                isZWNo: '电话号码不能有中文',
                isZMNo: '请填写正确的电话号码格式'
            },
            customer_tel_3: {
                isZWNo: '电话号码不能有中文',
                isZMNo: '请填写正确的电话号码格式'
            },
            show_trust_a: {
                required: '请选择是否公证委托'
            },
            show_trust_b: {
                required: '请选择是否公证委托'
            },
            buy_type: {
                required: '请选择付款方式'
            },
            tax_pay_type: {
                required: '请选择税费类型'
            },
            note_belong: {
                required: '请选择票据归属'
            },
            house_time: {
                required: '请输入交房时间'
            },
            tax_pay_tatal: {
                required: '请输入税费合计'
            }
        }
    });
    $("#collect_money_form").validate({
        errorPlacement: function (error, element) {
            element.siblings('.errorBox').html(error);
        },
        submitHandler: function (form) {
            save_collect_money();
        },
        rules: {},
        messages: {}
    });
  $("#addcont_rent_form").validate({
    errorPlacement: function (error, element) {
      element.siblings('.errorBox').html(error);
    },
    submitHandler: function (form) {
      save_bargain();
    },
    rules: {
      number: {
        required: true,
        maxlength: 30
      },
      buildarea: {
        required: true,
        isPrice: true,
        maxlength: 10,
        isArea: true
      },
      house_addr: {
        required: true,
        maxlength: 40
      },
      block_name: {
        required: true
      },
      signing_time: {
        required: true
      },
      type: {
        required: true
      },
      price: {
        required: true,
        isPrice: true
      },
      owner: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      owner_tel: {
        required: true,
        isZWNo: true
      },
      customer: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      customer_tel: {
        required: true,
        isZWNo: true
      },
      department_id_a: {
        required: true
      },
      signatory_id_a: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      signatory_tel_a: {
        required: true,
        isZWNo: true
      },
      department_id_b: {
        required: true
      },
      signatory_id_b: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      signatory_tel_b: {
        required: true,
        isZWNo: true
      },
      buy_type: {
        required: true
      },
      owner_commission: {
        required: true,
        isPrice: true
      },
      customer_commission: {
        required: true,
        isPrice: true
      },
      other_income: {
        required: true,
        isPrice: true
      },
      commission_total: {
        required: true,
        isPrice: true
      },
      start_time: {
        required: true,
        isTimeRange: true
      },
      end_time: {
        required: true,
        isTimeRange: true
      },
      deposit: {
        required: true,
        isPrice: true
      },
      divide_percent: {
        max: 100
      }

    },
    messages: {
      number: {
        required: '请输入合同编号',
        maxlength: '合同编号最多30个字符'
      },
      buildarea: {
        required: '请输入房源面积',
        isArea: '请输入正确的面积',
        maxlength: '房源面积最多输入10位'
      },
      house_addr: {
        required: '请输入房源地址',
        maxlength: '房源地址最多40个字符'
      },
      block_name: {
        required: '请输入楼盘名称'
      },
      signing_time: {
        required: '请输入签约时间'
      },
      type: {
        required: '请输入交易方式'
      },
      price: {
        required: '请输入成交金额',
        isPrice: '请输入正确的成交金额'
      },
      owner: {
        required: '请输入房东姓名'
      },
      owner_tel: {
        required: '请输入联系方式'
      },
      customer: {
        required: '请输入租客姓名'
      },
      customer_tel: {
        required: '请输入联系方式'
      },
      department_id_a: {
        required: '请输入签约门店'
      },
      signatory_id_a: {
        required: '请输入签约人姓名',
        isTNAME: '签约人姓名最多5个字符',
        valid_name: '签约人姓名只能包含汉字、字母、数字'
      },
      signatory_tel_a: {
        required: '请输入联系方式'
      },
      department_id_b: {
        required: '请输入签约门店'
      },
      signatory_id_b: {
        required: '请输入签约人姓名',
        isTNAME: '签约人姓名最多5个字符',
        valid_name: '签约人姓名只能包含汉字、字母、数字'
      },
      signatory_tel_b: {
        required: '请输入联系方式'
      },
      buy_type: {
        required: '请选择付款方式'
      },
      owner_commission: {
        required: '请输入业主应付佣金',
        isPrice: '请输入正确的佣金'
      },
      customer_commission: {
        required: '请输入客户应付佣金',
        isPrice: '请输入正确的佣金'
      },
      other_income: {
        required: '请输入其他佣金',
        isPrice: '请输入正确的佣金'
      },
      commission_total: {
        required: '请输入佣金总计收入',
        isPrice: '请输入正确的总计收入'
      },
      start_time: {
        required: '请输入起租时间',
        isTimeRange: '起租时间不能大于到期时间'
      },
      end_time: {
        required: '请输入到期时间',
        isTimeRange: '起租时间不能大于到期时间'
      },
      deposit: {
        required: '请输入押金',
        isPrice: '请输入正确的押金'
      },
      divide_percent: {
        max: "分佣比例最大可填100%"
      }
    }
  });

  //添加提交合同详情资料
  function save_add_bargain() {
    $.ajax({
      type: 'POST',
      url: '/bargain/save_add_bargain',
      data: {
        submit_flag: $("input[name='submit_flag']").val(),
        id: $("input[name='bargain_id']").val(),
          type: $("input[name='type']").val(),
        bargain_type: $("select[name='bargain_type']").val(),
        block_name: $("input[name='block_name']").val(),
        number: $("input[name='number']").val(),
        house_addr: $("input[name='house_addr']").val(),
        signing_time: $("input[name='signing_time']").val(),

        department_id_a: $("select[name='department_id_a']").val(),
        owner: $("input[name='owner']").val(),
        owner_tel: $("input[name='owner_tel']").val(),
        owner_idcard: $("input[name='owner_idcard']").val(),

        customer: $("input[name='customer']").val(),
        customer_tel: $("input[name='customer_tel']").val(),
        customer_idcard: $("input[name='customer_idcard']").val(),
        department_id_b: $("select[name='department_id_b']").val()
      },
      dataType: 'json',
      success: function (data) {
        var submit_flag = $("input[name='submit_flag']").val();
        if (data['result'] == 'ok') {
          $("#js_prompt1").text(data['msg']);
          openWin('js_pop_success');
        }
        else {
          $("#js_prompt2").text(data['msg']);
          openWin('js_pop_false');
        }
      }
    });
  }

//修改提交合同详情资料
    function save_modify_bargain_one() {
        $.ajax({
            type: 'POST',
            url: '/bargain/save_modify_bargain',
            data: {
                submit_flag: $("input[name='submit_flag']").val(),
                id: $("input[name='bargain_id']").val(),
                type: $("input[name='type']").val(),
                bargain_type: $("select[name='bargain_type']").val(),
                number: $("input[name='number']").val(),
                receipt_time: $("input[name='receipt_time']").val(),
                warrant_inside: $("select[name='warrant_inside']").val(),//办证人员
                warrant_inside_name: $("select[name='warrant_inside']").find("option:selected").text(),//办证人员名称
                bargain_status: $("select[name='bargain_status']").val(),//办理状态
                block_name: $("input[name='block_name']").val(),
                house_addr: $("input[name='house_addr']").val(),
                district_id: $("select[name='district_id']").val(),//区域
                district_name: $("select[name='district_id']").find("option:selected").text(),//区域名称
                agent_bank: $("select[name='agent_bank']").val(),//代办银行
                agent_type: $("select[name='agent_type']").val(),//代办类别
                agent_company: $("input[name='agent_company']").val(),//代办公司
                developer: $("input[name='developer']").val(),//开发商
                customer: $("input[name='customer']").val(),
                customer_tel: $("input[name='customer_tel']").val(),
                customer_idcard: $("input[name='customer_idcard']").val(),
                undertake_remarks: $("textarea[name='undertake_remarks']").val(),//承办备注

            },
            dataType: 'json',
            success: function (data) {
                var submit_flag = $("input[name='submit_flag']").val();

                if (data['result'] == 'ok') {
                    $("#js_prompt1").text(data['msg']);
                    if (submit_flag == "add") {
                        $("input[name='bargain_id']").val(data['bargain_id'])
                    }
                    openWin('js_pop_success');
                }
                else {
                    $("#js_prompt2").text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        });
    }
//修改提交合同详情资料
  function save_modify_bargain() {

      var purchase_money = new Array(
      $("input[name='purchase_money1']").val(),
      $("input[name='purchase_money2']").val(),
      $("input[name='purchase_money3']").val()
    );
      var purchase_condition = new Array(
      $("input[name='purchase_condition1']").val(),//购房款二期情况
      $("input[name='purchase_condition2']").val()//购房款三期情况
    );
      var collect_condition = new Array(
      $("input[name='collect_condition1']").val(),
      $("input[name='collect_condition2']").val(),
      $("input[name='collect_condition3']").val(),
      $("input[name='collect_condition4']").val()
    );
      var collect_money = new Array(
      $("input[name='collect_money1']").val(),
      $("input[name='collect_money2']").val(),
      $("input[name='collect_money3']").val(),
      $("input[name='collect_money4']").val()
    );
      var seller_lacks = [];
      $("input[name='seller_lacks']:checked").each(function (i) {//把所有被选中的复选框的值存入数组
          seller_lacks[i] = $(this).val();
      });
      var buyer_lacks = [];
      $("input[name='buyer_lacks']:checked").each(function (i) {//把所有被选中的复选框的值存入数组
          buyer_lacks[i] = $(this).val();
      });
      var owner_tel = new Array(
          $("input[name='owner_tel_1']").val().replace(/^\s+|\s+$/g, ""),
          $("input[name='owner_tel_2']").val().replace(/^\s+|\s+$/g, ""),
          $("input[name='owner_tel_3']").val().replace(/^\s+|\s+$/g, "")
      );
      var customer_tel = new Array(
          $("input[name='customer_tel_1']").val().replace(/^\s+|\s+$/g, ""),
          $("input[name='customer_tel_2']").val().replace(/^\s+|\s+$/g, ""),
          $("input[name='customer_tel_3']").val().replace(/^\s+|\s+$/g, "")
      );
      console.log(owner_tel);
      console.log(customer_tel);
    $.ajax({
      type: 'POST',
      url: '/bargain/save_modify_bargain',
      data: {
        submit_flag: $("input[name='submit_flag']").val(),
          type: $("input[name='type']").val(),
        id: $("input[name='bargain_id']").val(),//成交记录id
        number: $("input[name='number']").val(),//成交编号
        house_addr: $("input[name='house_addr']").val(),//物业地址
        price: $("input[name='price']").val(),//合同价
        decoration_price: $("input[name='decoration_price']").val(),//装修款
        buildarea: $("input[name='buildarea']").val(),//建筑面积
        certificate_number: $("input[name='certificate_number']").val(),//产证编号
        land_nature: $("input[name='land_nature']:checked").val(),//土地性质
        signatory_company: $("input[name='signatory_company']:checked").val(),//签约公司
        is_mortgage: $("input[name='is_mortgage']:checked").val(),//是否抵押
        mortgage_thing: $("input[name='mortgage_thing']").val(),//抵押物
        is_evaluate: $("input[name='is_evaluate']:checked").val(),//是否评估
        evaluate_charges: $("input[name='evaluate_charges']").val(),//评估收费
        mortgage_bank: $("select[name='mortgage_bank']").val(),//抵押银行
        note_belong: $("input[name='note_belong']:checked").val(),//交易税票归属
        note_other: $("input[name='note_other']").val(),//交易税票归属其他
        signing_time: $("input[name='signing_time']").val(),//签约时间
        house_time: $("input[name='house_time']").val(),//交房时间
        owner: $("input[name='owner']").val(),//卖方
          owner_tel: owner_tel.join(',').replace(/^,+|,+$/g, ""),//卖方电话
        owner_idcard: $("input[name='owner_idcard']").val(),//卖方身份证
        agency_id_a: $("input[name='agency_id_a']").val(),//房源方门店
        trust_name_a: $("input[name='trust_name_a']").val(),//委托人姓名
          show_trust_a: $("input[name='show_trust_a']:checked").val(),//公证委托
          trust_idcard_a: $("input[name='trust_idcard_a']").val(),//委托人证件
        agency_name_a: $("input[name='agency_name_a']").val(),//房源方门店
        broker_id_a: $("input[name='broker_id_a']").val(),//房源方经纪人id
        broker_name_a: $("input[name='broker_name_a']").val(),//房源方经纪人名称
        broker_tel_a: $("input[name='broker_tel_a']").val(),//房源方经纪人电话

        agency_id_b: $("input[name='agency_id_a']").val(),//客源方门店
          trust_name_b: $("input[name='trust_name_b']").val(),//委托人姓名
          show_trust_b: $("input[name='show_trust_b']:checked").val(),//公证委托
          trust_idcard_b: $("input[name='trust_idcard_b']").val(),//委托人证件
        agency_name_b: $("input[name='agency_name_a']").val(),//房源方门店
        broker_id_b: $("input[name='broker_id_a']").val(),//经纪人id
        broker_name_b: $("input[name='broker_name_a']").val(),//经纪人名称
        broker_tel_b: $("input[name='broker_tel_a']").val(),//经纪人电话
        signing_fee_type: $("select[name='signing_fee_type']").val(),//签约收费类型
        signing_fee: $("select[name='signing_fee']").val(),//签约收费

        warrant_type: $("input[name='warrant_type']").val(),//权证类型
          warrant_inside: $("select[name='warrant_inside']").val(),//权证人员
          warrant_inside_name: $("select[name='warrant_inside']").find("option:selected").text(),//权证人员名称
        signatory_id: $("select[name='signatory_id']").val(),//签约人id
          signatory_name: $("select[name='signatory_id']").find("option:selected").text(),//签约人姓名
          finance_id: $("select[name='finance_id']").val(),//理财人员
          finance_name: $("select[name='finance_id']").find("option:selected").text(),//理财人员名称
          district_id: $("select[name='district_id']").val(),//区域
          district_name: $("select[name='district_id']").find("option:selected").text(),//区域名称
          house_type: $("select[name='house_type']").val(),//房屋类型
        tracker: $("input[name='tracker']").val(),//跟单人员
        customer: $("input[name='customer']").val(),//买方
          customer_tel: customer_tel.join(',').replace(/^,+|,+$/g, ""),//买方电话
        customer_idcard: $("input[name='customer_idcard']").val(),//买方身份证号
        tax_pay_tatal: $("input[name='tax_pay_tatal']").val(),//税费合计
        tax_pay_type: $("input[name='tax_pay_type']:checked").val(),//税费约定
        tax_pay_appoint: $("input[name='tax_pay_appoint']").val(),//按约定税费
        undertake_remarks: $("textarea[name='undertake_remarks']").val(),//承办备注
        remarks: $("textarea[name='remarks']").val(),//合同备注
        bargain_status: $("select[name='bargain_status']").val(),//办理状态

        buy_type: $("input[name='buy_type']:checked").val(),//付款方式
        loan_bank: $("select[name='loan_bank']").val(),//贷款银行
        tatal_money: $("input[name='tatal_money']").val(),//全部购房款
        purchase_money: purchase_money,//购房款
        purchase_condition: purchase_condition,//购房款分期情况
        loan_type: $("input[name='loan_type']:checked").val(),//贷款方式
        payment_period_time: $("input[name='payment_period_time']").val(),//分期首付款时间
        payment_once_time: $("input[name='payment_once_time']").val(),//一次性付款首付款时间
        first_time: $("input[name='first_time']").val(),//首付款时间
        first_money: $("input[name='first_money']").val(),//首付款
        spare_money: $("input[name='spare_money']").val(),//余款

        collect_condition: collect_condition,//取款条件
        collect_money: collect_money,//取款金额
          seller_lacks: seller_lacks,
          buyer_lacks: buyer_lacks,
          seller_lacks_others: $("input[name='seller_lacks_others']").val(),
          buyer_lacks_others: $("input[name='buyer_lacks_others']").val(),
          seller_id_card: $("input[name='seller_id_card']").val(),
          buyer_id_card: $("input[name='buyer_id_card']").val(),
          seller_marry_info: $("input[name='seller_marry_info']").val(),
          buyer_marry_info: $("input[name='buyer_marry_info']").val(),
      },
      dataType: 'json',
      success: function (data) {
        var submit_flag = $("input[name='submit_flag']").val();

        if (data['result'] == 'ok') {
          $("#js_prompt1").text(data['msg']);
            if (submit_flag == "add") {
                $("input[name='bargain_id']").val(data['bargain_id'])
            }
          openWin('js_pop_success');
        }
        else {
          $("#js_prompt2").text(data['msg']);
          openWin('js_pop_false');
        }
      }
    });
  }

  $("#add_replace").validate({
    errorPlacement: function (error, element) {
      element.siblings('.errorBox').html(error);
    },
    submitHandler: function (form) {
      save_replace_flow();
    },
    rules: {
      replace_money_type: {
        required: true
      },
      replace_flow_time: {
        required: true
      },
      replace_payment_method: {
        required: true
      },
        replace_money_number: {
            isPrice: true,
            maxlength: 10
        },
      replace_remark: {
        maxlength: 300
      }
    },
    messages: {
      replace_money_type: {
        required: '请选择款类'
      },
      replace_flow_time: {
        required: '请选择收付时间'
      },
      replace_payment_method: {
        required: '请选择收付方式'
      },
        money_number: {
        isPrice: '请输入数字字符',
        maxlength: '填写金额超过限制金额'
      },
      should_remark: {
        maxlength: "备注最多300字"
      }
    }
  });
    $("#add_replace_tax").validate({
        errorPlacement: function (error, element) {
            element.siblings('.errorBox').html(error);
        },
        submitHandler: function (form) {
            save_replace_tax_flow();
        },
        rules: {
            replace_money_type: {
                required: true
            },
            replace_flow_time: {
                required: true
            },
            replace_payment_method: {
                required: true
            },
            money_number: {
                isPrice: true,
                maxlength: 10
            },
            replace_remark: {
                maxlength: 300
            }
        },
        messages: {
            replace_money_type: {
                required: '请选择款类'
            },
            replace_flow_time: {
                required: '请选择收付时间'
            },
            replace_payment_method: {
                required: '请选择收付方式'
            },
            money_number: {
                isPrice: '请输入数字字符',
                maxlength: '填写金额超过限制金额'
            },
            should_remark: {
                maxlength: "备注最多300字"
            }
        }
    });
    $("#add_replace_signing").validate({
        errorPlacement: function (error, element) {
            element.siblings('.errorBox').html(error);
        },
        submitHandler: function (form) {
            save_replace_signing_flow();
        },
        rules: {
            replace_money_type: {
                required: true
            },
            replace_flow_time: {
                required: true
            },
            replace_payment_method: {
                required: true
            },
            money_number: {
                isPrice: true,
                maxlength: 10
            },
            replace_remark: {
                maxlength: 300
            }
        },
        messages: {
            replace_money_type: {
                required: '请选择款类'
            },
            replace_flow_time: {
                required: '请选择收付时间'
            },
            replace_payment_method: {
                required: '请选择收付方式'
            },
            money_number: {
                isPrice: '请输入数字字符',
                maxlength: '填写金额超过限制金额'
            },
            should_remark: {
                maxlength: "备注最多300字"
            }
        }
    });
  //提交实收实付资料
  function save_replace_flow() {
    $.ajax({
      type: 'get',
      url: '/bargain/add_flow',
      data: {
        id: $("#flow_id").val(),
        'flow_type': 'replace',
        'c_id': $("#bargain_id").val(),
        'money_type': $("select[name='replace_money_type']").val(),
        'collect_type': $("select[name='replace_collect_type']").val(),
        'collect_money': $("input[name='replace_collect_money']").val(),
        'pay_type': $("select[name='replace_pay_type']").val(),

          'money_number': $("input[name='money_number']").val(),
        'pay_money': $("input[name='replace_pay_money']").val(),
        'flow_time': $("input[name='replace_flow_time']").val(),
        'payment_method': $("select[name='replace_payment_method']").val(),
        'remark': $("textarea[name='replace_remark']").val(),
        'flow_department_id': $("select[name='replace_flow_department']").val(),
        'flow_signatory_id': $("select[name='replace_flow_signatory']").val(),
        'counter_fee': $("input[name='replace_counter_fee']").val(),
        'docket': $("input[name='replace_docket']").val(),
        'docket_type': $("select[name='replace_docket_type']").val(),
        'target_type': $("select[name='target_type']").val(),
          'target_name': $("input[name='target_name']").val(),
        'replace_type': $("select[name='replace_type']").val(),
        'money_name': $("input[name='money_name']").val()
      },
      dataType: 'json',
      success: function (data) {
        var id = $("#flow_id").val();
        closeParentWin('js_replace_pop');
        if (data['result'] == 'ok') {
          window.parent.frames["iframepage"].location = window.parent.frames["iframepage"].location;
          window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
          window.parent.window.openWin('js_pop_success');
        }
        else {
          $("#js_prompt2").text(data['msg']);
          openWin('js_pop_false');
        }
      }
    });
  }
//提交实收实付资料
    function save_replace_tax_flow() {
        $.ajax({
            type: 'get',
            url: '/bargain/add_tax_flow',
            data: {
                id: $("#flow_id").val(),
                'flow_type': 'replace_tax',
                'c_id': $("#bargain_id").val(),
                'money_type': $("select[name='replace_money_type']").val(),
                'collect_type': $("select[name='replace_collect_type']").val(),
                'collect_money': $("input[name='replace_collect_money']").val(),
                'pay_type': $("select[name='pay_type']").val(),
                'certificate_number': $("input[name='certificate_number']").val(),
                'money_number': $("input[name='money_number']").val(),
                'pay_money': $("input[name='replace_pay_money']").val(),
                'flow_time': $("input[name='replace_flow_time']").val(),
                'payment_method': $("select[name='replace_payment_method']").val(),
                'remark': $("textarea[name='replace_remark']").val(),
                'flow_department_id': $("select[name='replace_flow_department']").val(),
                'flow_signatory_id': $("select[name='replace_flow_signatory']").val(),
                'counter_fee': $("input[name='replace_counter_fee']").val(),
                'docket': $("input[name='replace_docket']").val(),
                'docket_type': $("select[name='replace_docket_type']").val(),
                'target_type': $("select[name='target_type']").val(),
                'target_name': $("input[name='target_name']").val(),
                'target_idcard': $("input[name='target_idcard']").val(),
                'bank_account': $("input[name='bank_account']").val(),
                'collect_person': $("input[name='collect_person']").val(),

                'replace_type': $("select[name='replace_type']").val(),
                'money_name': $("input[name='money_name']").val()
            },
            dataType: 'json',
            success: function (data) {
                var id = $("#flow_id").val();
                closeParentWin('js_replace_tax_pop');
                if (data['result'] == 'ok') {
                    window.parent.frames["iframepage"].location = window.parent.frames["iframepage"].location;
                    window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
                    window.parent.window.openWin('js_pop_success');
                }
                else {
                    $("#js_prompt2").text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        });
    }

    //提交签约费资料
    function save_replace_signing_flow() {
        $.ajax({
            type: 'get',
            url: '/bargain/add_signing_flow',
            data: {
                id: $("#flow_id").val(),
                'flow_type': 'replace_signing',
                'c_id': $("#bargain_id").val(),
                'money_type': $("select[name='replace_money_type']").val(),
                'collect_type': $("select[name='replace_collect_type']").val(),
                'collect_money': $("input[name='replace_collect_money']").val(),
                'pay_type': $("select[name='pay_type']").val(),
                'certificate_number': $("input[name='certificate_number']").val(),
                'money_number': $("input[name='money_number']").val(),
                'pay_money': $("input[name='replace_pay_money']").val(),
                'flow_time': $("input[name='replace_flow_time']").val(),
                'payment_method': $("select[name='replace_payment_method']").val(),
                'remark': $("textarea[name='replace_remark']").val(),
                'flow_department_id': $("select[name='replace_flow_department']").val(),
                'flow_signatory_id': $("select[name='replace_flow_signatory']").val(),
                'counter_fee': $("input[name='replace_counter_fee']").val(),
                'docket': $("input[name='replace_docket']").val(),
                'docket_type': $("select[name='replace_docket_type']").val(),
                'target_type': $("select[name='target_type']").val(),
                'target_name': $("input[name='target_name']").val(),
                'target_idcard': $("input[name='target_idcard']").val(),
                'bank_account': $("input[name='bank_account']").val(),
                'collect_person': $("input[name='collect_person']").val(),

                'replace_type': $("select[name='replace_type']").val(),
                'money_name': $("input[name='money_name']").val()
            },
            dataType: 'json',
            success: function (data) {
                var id = $("#flow_id").val();
                closeParentWin('js_replace_signing_pop');
                if (data['result'] == 'ok') {
                    window.parent.frames["iframepage"].location = window.parent.frames["iframepage"].location;
                    window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
                    window.parent.window.openWin('js_pop_success');
                }
                else {
                    $("#js_prompt2").text(data['msg']);
                    openWin('js_pop_false');
                }
            }
        });
    }

  $("#add_should").validate({
    errorPlacement: function (error, element) {
      element.siblings('.errorBox').html(error);
    },
    submitHandler: function (form) {
      save_should_flow();
    },
    rules: {
      should_money_type: {
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
    messages: {
      should_money_type: {
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
      should_remark: {
        maxlength: "备注最多300字"
      }
    }
  });

  //提交实收实付资料
  function save_should_flow() {
    $.ajax({
      type: 'get',
      url: '/bargain/add_flow',
      data: {
        id: $("#flow_id").val(),
        'flow_type': $('#flow_type').val(),
        'c_id': $("#bargain_id").val(),
        'money_type': $("select[name='should_money_type']").val(),
        'collect_type': $("select[name='should_collect_type']").val(),
        'collect_money': $("input[name='should_collect_money']").val(),
        'pay_type': $("select[name='should_pay_type']").val(),
        'pay_money': $("input[name='should_pay_money']").val(),
        'flow_time': $("input[name='should_flow_time']").val(),
        'remark': $("textarea[name='should_remark']").val()
      },
      dataType: 'json',
      success: function (data) {
        var id = $("#flow_id").val();
        closeParentWin('js_should_pop');
        if (data['result'] == 'ok') {
          if (data['num'] == 1) {
            window.parent.window.show_replace_add();
          }
          window.parent.frames["iframepage"].location = window.parent.frames["iframepage"].location;
          window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
          window.parent.window.openWin('js_pop_success');
        }
        else {
          $("#js_prompt2").text(data['msg']);
          openWin('js_pop_false');
        }
      }
    });
  }


  $("#divide_form").validate({
    errorPlacement: function (error, element) {
      element.siblings('.errorBox').html(error);
    },
    submitHandler: function (form) {
      save_divide();
    },
    rules: {
      divide_percent: {
        required: true,
        isPrice: true,
        max: 100
      },
      divide_price: {
        required: true,
        isPrice: true
      },
      divide_type: {
        required: true
      },
      department_id: {
        required: true
      },
      signatory_id: {
        required: true
      },
      achieve_department_id_b: {
        required: true
      },
      achieve_signatory_id_b: {
        required: true
      },
      achieve_department_id_a: {
        required: true
      },
      achieve_signatory_id_a: {
        required: true
      }
    },
    messages: {
      divide_percent: {
        required: '请填写分成比例',
        isPrice: "请填写正确的分成比例",
        max: '分佣比例最大为100%'
      },
      divide_price: {
        required: '请填写实际分成金额',
        isPrice: "请填写正确的实际分成金额"
      },
      divide_type: {
        required: '请选择分成描述'
      },
      department_id: {
        required: '请填写归属门店'
      },
      signatory_id: {
        required: '请选择归属经纪人'
      },
      achieve_department_id_b: {
        required: '请选择业绩归属门店'
      },
      achieve_signatory_id_b: {
        required: '请选择业绩归属经纪人'
      },
      achieve_department_id_a: {
        required: '请选择业绩归属区域'
      },
      achieve_signatory_id_a: {
        required: '请选择业绩归属经纪人'
      }
    }
  });

  //提交业绩分成资料
  function save_divide() {
    var percent1 = $("input[name='divide_percent']").val();
    var total1 = $("#percent_total").val();
    if (percent1 > 0) {
      $.ajax({
        type: 'post',
        url: '/bargain/divide_manage',
        data: {
          divide_id: $("#divide_id").val(),
          c_id: $("#bargain_id").val(),
          divide_percent: $("input[name='divide_percent']").val(),
          divide_price: $("input[name='divide_price']").val(),
          divide_type: $("select[name='divide_type']").val(),
          department_id: $("select[name='department_id']").val(),
          signatory_id: $("select[name='signatory_id']").val(),
          achieve_department_id_b: $("select[name='achieve_department_id_b']").val(),
          achieve_signatory_id_b: $("select[name='achieve_signatory_id_b']").val(),
          achieve_department_id_a: $("select[name='achieve_department_id_a']").val(),
          achieve_signatory_id_a: $("select[name='achieve_signatory_id_a']").val()
        },
        dataType: 'json',
        success: function (data) {
          var id = $("#divide_id").val();
          closeParentWin('js_divide_pop');
          if (data['result'] == 'ok') {
            window.parent.frames["iframepage"].location = window.parent.frames["iframepage"].location;
            window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
            window.parent.window.openWin('js_pop_success');
          }
          else {
            $("#js_prompt2").text(data['msg']);
            openWin('js_pop_false');
          }
        }
      });
    } else {
      $("#should_divide_money").text('');
      $("#percent_error").text("");
    }
  }

  $("#earnest_edit_form").validate({
    errorPlacement: function (error, element) {
      element.siblings('.errorBox').html(error);
    },
    submitHandler: function (form) {
      sumbit_earnest();
    },
    rules: {
      earnest_price: {
        required: true,
        number: true,
        min: 1
      },
      block_name: {
        required: true
      },
      address: {
        required: true
      },
      seller_owner: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      seller_telno: {
        required: true,
        isZMNo: true,
        isZWNo: true
      },
      buyer_owner: {
        required: true,
        isTNAME: true,
        valid_name: true
      },
      buyer_telno: {
        required: true,
        isZMNo: true,
        isZWNo: true
      },
      collection_time: {
        required: true
      },
      status: {
        required: true,
        min: 1
      },
      payee_department_id: {
        required: true,
        min: 1
      },
      payee_signatory_id: {
        required: true,
        min: 1
      },
      collect_type: {
        required: true,
        min: 1
      }
    },
    messages: {
      earnest_price: {
        required: '请填写诚意金额',
        number: '诚意金额必须为数字',
        min: '诚意金额不能小于1'
      },
      block_name: {
        required: "请填写楼盘名"
      },
      address: {
        required: "请填写房源地址"
      },
      seller_owner: {
        required: '请填写业主姓名'
      },
      seller_telno: {
        required: "请填写联系方式"
      },
      buyer_owner: {
        required: '请填写客户姓名'
      },
      buyer_telno: {
        required: "请填写联系方式"
      },
      collection_time: {
        required: "请填写收款日期"
      },
      status: {
        required: '请选诚意金状态',
        min: '请选诚意金状态'
      },
      payee_department_id: {
        required: "请选收款人",
        min: '请选收款人'
      },
      payee_signatory_id: {
        required: "请选收款人",
        min: '请选收款人'
      },
      collect_type: {
        required: "请选收款方式",
        min: '请选收款方式'
      }
    }
  });

  function sumbit_earnest() {
    $.ajax({
      type: 'POST',
      url: '/bargain_earnest_money/save/',
      data: {
        id: $("input[name='earnest_money_id']").val(),
        trade_type: $("select[name='trade_type']").val(),
        house_id: $("input[name='house_id']").val(),
        sell_type: $("select[name='sell_type']").val(),
        intension_price: $("input[name='intension_price']").val(),
        block_name: $("input[name='block_name']").val(),
        block_id: $("input[name='block_id']").val(),
        address: $("input[name='address']").val(),
        seller_owner: $("input[name='seller_owner']").val(),
        seller_telno: $("input[name='seller_telno']").val(),
        seller_idcard: $("input[name='seller_idcard']").val(),
        buyer_owner: $("input[name='buyer_owner']").val(),
        buyer_telno: $("input[name='buyer_telno']").val(),
        buyer_idcard: $("input[name='buyer_idcard']").val(),
        earnest_price: $("input[name='earnest_price']").val(),
        collection_time: $("input[name='collection_time']").val(),
        status: $("select[name='status']").val(),
        payee_department_id: $("select[name='payee_department_id']").val(),
        payee_signatory_id: $("select[name='payee_signatory_id']").val(),
        collect_type: $("select[name='collect_type']").val(),
        refund_type: $("select[name='refund_type']").val(),
        refund_reason: $("input[name='refund_reason']").val(),
        remark: $("textarea[name='remark']").val()
      },
      dataType: 'json',
      success: function (data) {
        var submit_flag = data['id'];
        if (data['result'] == -1) {
          $("#js_prompt").text('没有权限修改诚意金状态！');
          openWin('js_pop_success');
        } else if (data['result'] > 0) {
          if (submit_flag > 0) {
            $("#js_prompt").text('诚意金编辑成功！');
          } else {
            $("#js_prompt").text('诚意金新增成功！');
          }
          openWin('js_pop_success');
        }
        else {
          if (submit_flag > 0) {
            $("#js_prompt").text('诚意金编辑失败！');
          } else {
            $("#js_prompt").text('诚意金新增失败！');
          }
          openWin('js_pop_success');
        }
      }
    });
  }

  $("#transfer_form_modify").validate({
    errorPlacement: function (error, element) {
      element.siblings('.errorBox').html(error);
    },
    submitHandler: function (form) {
      save_transfer_modify();
    },
    rules: {
      transfer_remark: {
        maxlength: 300
      },
      remind_time: {
        required: true
      },
      remind_signatory_id: {
        required: true
      },
      remind_remark: {
        maxlength: 300
      }
    },
    messages: {
      transfer_remark: {
        maxlength: "备注最多300字"
      },
      remind_time: {
        required: '请填写提醒时间'
      },
      remind_signatory_id: {
        required: '请填写提醒人'
      },
      remind_remark: {
        maxlength: "提醒信息最多300字"
      }
    }
  });
  $("#transfer_form").validate({
    errorPlacement: function (error, element) {
      element.siblings('.errorBox').html(error);
    },
    submitHandler: function (form) {
      save_transfer();
    },
    rules: {
      transfer_remark: {
        maxlength: 300
      },
      remind_time: {
        required: true
      },
      remind_department_id: {
        required: true
      },
      remind_signatory_id: {
        required: true
      },
      remind_remark: {
        maxlength: 300
      }
    },
    messages: {
      transfer_remark: {
        maxlength: "备注最多300字"
      },
      remind_time: {
        required: '请填写提醒时间'
      },
      remind_department_id: {
        required: '请填写提醒人'
      },
      remind_signatory_id: {
        required: '请填写提醒人'
      },
      remind_remark: {
        maxlength: "提醒信息最多300字"
      }
    }
  });

  //提交权证步骤
  function save_transfer() {
    var arr = new Array;
    $("input[name='step']:checked").each(function () {
      arr.push($(this).val());
    })
    if (arr.length > 0) {
      if (arr.length <= 3) {
        $.ajax({
          type: 'post',
          url: '/bargain/modify_temp_step',
          data: {
            'stage_id': $("#stage_id").val(),
            'c_id': $("#bargain_id").val(),
            'stage': arr,
            'transfer_remark': $("textarea[name='transfer_remark']").val(),
            'is_remind': $("input[name='is_remind']:checked").val(),
            'remind_department_id': $("select[name='remind_department_id']").val(),
            'remind_signatory_id': $("select[name='remind_signatory_id']").val(),
            'remind_remark': $("textarea[name='remind_remark']").val(),
            'remind_time': $("input[name='remind_time']").val()
          },
          dataType: 'json',
          success: function (data) {
            var id = $("#stage_id").val();
            $("#js_temp_pop").hide();
            $("#GTipsCoverjs_temp_pop").remove();
            closeParentWin('js_addtemp_pop');
            if (data['result'] == 'ok') {
              window.parent.frames["iframepage"].location = window.parent.frames["iframepage"].location;
              window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
              window.parent.window.openWin('js_pop_success');
            }
            else {
              $("#js_prompt2").text(data['msg']);
              openWin('js_pop_false');
            }
          }
        });
      } else {
        $("#step_error").text('最多选择三个步骤！');
      }
    } else {
      $("#step_error").text('请至少选择一个步骤！');
    }
  }

  //修改提交权证步骤
  function save_transfer_modify() {
    var arr = new Array;
    $("input[name='step']").each(function () {
      arr.push($(this).val());
    })
    if (arr.length > 0) {
      if (arr.length <= 3) {
        $.ajax({
          type: 'post',
          url: '/bargain/modify_temp_step',
          data: {
            'stage_id': $("#stage_id").val(),
            'c_id': $("#bargain_id").val(),
            'transfer_remark': $("textarea[name='transfer_remark']").val(),
            'is_remind': $("input[name='is_remind']:checked").val(),
            'remind_department_id': $("select[name='remind_department_id']").val(),
            'remind_signatory_id': $("select[name='remind_signatory_id']").val(),
            'remind_remark': $("textarea[name='remind_remark']").val(),
            'remind_time': $("input[name='remind_time']").val(),
            'stage_type': $("select[name='stage_type']").val(),
            'number_days': $("input[name='number_days']").val(),
            'start_time': $("input[name='start_time']").val(),
            'complete_time': $("input[name='complete_time']").val(),
            'complete_signatory_id': $("select[name='complete_signatory_id']").val()
          },
          dataType: 'json',
          success: function (data) {
            var id = $("#stage_id").val();
            $("#js_temp_pop").hide();
            $("#GTipsCoverjs_temp_pop").remove();
            closeParentWin('js_modifytemp_pop');
            if (data['result'] == 'ok') {
              window.parent.frames["iframepage"].location = window.parent.frames["iframepage"].location;
              window.parent.document.getElementById("js_prompt1").innerHTML = data['msg'];
              window.parent.window.openWin('js_pop_success');
            }
            else {
              $("#js_prompt2").text(data['msg']);
              openWin('js_pop_false');
            }
          }
        });
      } else {
        $("#step_error").text('最多选择三个步骤！');
      }
    } else {
      $("#step_error").text('请至少选择一个步骤！');
    }
  }
})
