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

//添加按揭客户信息
$(function () {
  $("#jsUpForm").validate({
    errorPlacement: function (error, element) {
      error.appendTo(element.parents(".js_fields").find(".errorBox"));
    },
    submitHandler: function (form) {
      //如果是弹窗的形式就得隐藏$("#js_pop_add_attendance").attr('display','none');
      sub();
    },
    rules: {
      block_name: {
        required: true
      },
      room: {
        required: true
      },
      hall: {
        required: true
      },
      toilet: {
        required: true
      },
      floor: {
        required: true
      },
      floor2: {
        required: true
      },
      subfloor: {
        required: true
      },
      totalfloor: {
        required: true
      },
      buildyear: {
        min: 1
      },
      buildarea: {
        required: true
      },
      price: {
        required: true
      },
      title: {
        required: true
      },
      unique: {
        required: true
      }
    },
    messages: {
      block_name: {
        required: '请选择小区'
      },
      room: {
        required: '请选择户型(室)'
      },
      hall: {
        required: '请选择户型(厅)'
      },
      toilet: {
        required: '请选择户型(卫)'
      },
      floor: {
        required: '请输入楼层'
      },
      floor2: {
        required: '请输入楼层'
      },
      subfloor: {
        required: '请输入楼层'
      },
      totalfloor: {
        required: '请输入总楼层'
      },
      buildyear: {
        min: '请选择房龄'
      },
      buildarea: {
        required: '请输入面积'
      },
      price: {
        required: '请输入价格'
      },
      title: {
        required: '请输入标题'
      },
      unique: {
        required: '房源重复'
      }
    }
  });
  //提交按揭信息
  /*function apply_now(){
   $.ajax({
   type: 'POST',
   url: '/finance/apply/',
   data:$("#jsUpForm").serialize(),
   dataType: 'json',
   success: function(data){
   if(data['result'] == 'ok'){
   $("#js_prompt").text('申请成功！');
   openWin('js_pop');
   setTimeout(function(){location.href="/finance/my_customer"},2000);
   }else if(data['result'] == 'no'){
   $("#js_prompt").text('申请失败！');
   openWin('js_pop');
   setTimeout(function(){location.href="/finance/my_customer"},2000);
   }
   }
   });
   }*/
  function sub() {
    var numArr2 = [];
    $("input[name='p_filename2[]']").each(function (index, item) {
      numArr2.push($(this).val());
    })
    $("[name='p_filename22']").val(numArr2);

    var numArr1 = [];
    $("input[name='p_filename1[]']").each(function (index, item) {
      numArr1.push($(this).val());
    })
    $("[name='p_filename11']").val(numArr1);
    document.getElementById("jsUpForm").submit();
  }
});
//修改按揭客户信息
$(function () {
  $("#jsUpForm_modify").validate({
    errorPlacement: function (error, element) {
      error.appendTo(element.parents(".js_fields").find(".errorBox"));
    },
    submitHandler: function (form) {
      //如果是弹窗的形式就得隐藏$("#js_pop_add_attendance").attr('display','none');
      apply_now_modify();
    },
    rules: {
      block_name: {
        required: true
      },
      block_address: {
        required: true
      },
      block_num: {
        required: true
      }, /*
       price:{
       required: true
       },
       first_pay:{
       required: true
       },*/
      borrower: {
        required: true
      },
      borrower_phone: {
        required: true
      },
      sell_name: {
        required: true
      },
      sell_phone: {
        required: true
      }
    },
    messages: {
      block_name: {
        required: '请输入小区名称'
      },
      block_address: {
        required: '请输入小区地址'
      },
      block_num: {
        required: '请输入楼栋单位门牌'
      }, /*
       price:{
       required: '请选择托管结束时间'
       },
       first_pay:{
       required: '请选择托管结束时间'
       },*/
      borrower: {
        required: '请输入买方姓名'
      },
      borrower_phone: {
        required: '请输入买方联系电话'
      },
      sell_name: {
        required: '请输入卖方姓名'
      },
      sell_phone: {
        required: '请输入卖方联系电话'
      }
    }
  });
  //提交按揭信息
  function apply_now_modify() {
    var id = $("#modify_id").val();
    $.ajax({
      type: 'POST',
      url: '/finance/modify/' + id,
      data: $("#jsUpForm_modify").serialize(),
      dataType: 'json',
      success: function (data) {
        if (data['result'] == 'ok') {
          $("#js_prompt").text('修改资料成功！');
          openWin('js_pop');
          setTimeout(function () {
            location.href = "/finance/my_customer"
          }, 2000);
        } else if (data['result'] == 'no') {
          $("#js_prompt").text('修改资料失败！');
          openWin('js_pop');
          setTimeout(function () {
            location.href = "/finance/my_customer"
          }, 2000);
        }
      }
    });
  }
});
