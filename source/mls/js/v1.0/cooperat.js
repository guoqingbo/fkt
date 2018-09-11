$(function () {

  /*label*/
  $(".holder_name").blur(function () {
    if ($(this).val() == "") {
      $(this).next(".holder_for").show();
    } else {
      $(this).next(".holder_for").hide();
    }
  });
  $(".holder_name ").focus(function () {
    if ($(this).val() == "") {
      $(this).next(".holder_for").hide();
    }
  });

  /*手机  姓名验证*/

  function validatePhone(phone) {
    var re = /^1\d{10}$/;
    if (re.test(phone)) {
      return true;
    } else {
      return false;
    }
  }

  $('#cm_name').blur(function () {
    if ($('#cm_name').val()) {
      if ($('#cm_name').val().length > 6) {
        $('#p3').css('display', 'block');
        $('#p4').css('display', 'none');
      } else {
        $('#p3').css('display', 'none');
        $('#p4').css('display', 'block');
      }

    } else {
      $('#p3').css('display', 'block');
      $('#p4').css('display', 'none');
    }
  });
  $('#phone').blur(function () {
    if ($('#phone').val()) {
      if (validatePhone($('#phone').val())) {
        $('#p1').css('display', 'none');
        $('#p2').css('display', 'block');
      } else {
        $('#p1').css('display', 'block');
        $('#p2').css('display', 'none');
      }
    } else {
      $('#p1').css('display', 'block');
      $('#p2').css('display', 'none');
    }
  });
  $('#highprice').change(function () {
    var lowprice = $('#lowprice').val() == '-1' ? '-1' : parseInt($('#lowprice').val());
    var highprice = $('#highprice').val() == '-1' ? '-1' : parseInt($('#highprice').val());

    if (lowprice == '-1' && highprice == '-1') {
      alert('最高价要大于最低价');
    }
    if (lowprice == '-1' && highprice != '-1') {
      alert('最高价要大于最低价');
    }

    if (lowprice != '-1' && highprice != '-1') {
      if (lowprice >= highprice) {
        alert('最高价要大于最低价');
      }
    }
  });

  /*全选*/
  $('#checkallplate').click(function () {
    if ($(this).attr("checked")) {
      $("input[name='plate[]']").each(function () {
        $(this).attr("checked", true);
      })
    } else {
      $("input[name='plate[]']").each(function () {
        $(this).attr("checked", false);
      })
    }
  })

  $(".plate").click(function () {
    $('#checkallplate').attr("checked", false);
  })

  $('#checkalllayout').click(function () {
    if ($(this).attr("checked")) {
      $("input[name='layout[]']").each(function () {
        $(this).attr("checked", true);
      })
    } else {
      $("input[name='layout[]']").each(function () {
        $(this).attr("checked", false);
      })
    }
  })
  $(".layout").click(function () {
    $('#checkalllayout').attr("checked", false);
  })
  /*全选end*/

});
