<body>

<style>
  .aad_pop_line1_title_p {
    min-width: 80px;
    text-align: right;
  }
</style>
<!--实收实付添加弹窗开始-->
<div class="achievement_money_pop real_W580" style="display: block;">
  <dl class="title_top">
    <dd id='title_top'><?= $id ? "编辑" : "新增" ?>税费</dd>
  </dl>
  <!--弹出框内容-->
  <div class="add_pop_messages raal_H360">
    <div class="aad_pop_line1">
      <form action="" id="add_replace_tax" method="post">
        <div style="width:98%; padding:1%;float:left;display:inline;">
          <ul>
              <li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
                  <p class="aad_pop_line1_title_p">收付对象：</p>
                  <select class="aad_pop_select_W100" name="target_type" onchange="select_target_name()">
                      <?php foreach ($config['target_type'] as $key => $val) { ?>
                          <option value="<?= $key; ?>" target_idcard="<?= $target_idcard[$key]; ?>" <?= $flow_list['target_type'] == $key ? 'selected' : '' ?>><?= $val." ".$target_name[$key]; ?></option>
                      <?php } ?>
                  </select>
                  <input type="hidden" name="target_name" value="">
              </li>
              <script>
                  function select_target_name() {
                      var selected_opt=target_text= $("select[name=target_type]").find("option:selected");
                      var target_text= selected_opt.text();
                      var target_val=$("select[name=target_type]").val();
                      var target_idcard= selected_opt.attr('target_idcard');
                      $("input[name='target_idcard']").val(target_idcard);
                      $("input[name='target_name']").val(target_text);
                  }
                  select_target_name();
              </script>
              <li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
                  <p class="aad_pop_line1_title_p"><b class="resut_table_state_1"></b>身份证号：</p>
                  <input type="text" class="aad_pop_select_W100" name="target_idcard"
                         value="<?= $id ? $flow_list['target_idcard'] : $target_idcard[1]; ?>" autocomplete="off">
                  <div class="errorBox" style="text-indent:92px;"></div>
              </li>
          </ul>
        </div>
          <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
              <ul>
                  <li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
                      <p class="aad_pop_line1_title_p"><b class="resut_table_state_1"></b>银行卡号：</p>
                      <input type="text" class="aad_pop_select_W100" name="bank_account" value="<?= $flow_list['bank_account']; ?>" autocomplete="off">
                      <div class="errorBox" style="text-indent:92px;"></div>
                  </li>
                  <li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
                      <p class="aad_pop_line1_title_p"><b class="resut_table_state_1"></b>类型：</p>
                      <select class="aad_pop_select_W100" name="replace_type">
                          <?php foreach ($config['replace_type'] as $key => $val) { ?>
                              <option value="<?= $key; ?>" <?= $flow_list['replace_type'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                          <?php } ?>
                      </select>
                  </li>
              </ul>
          </div>
        <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
          <ul>
              <li class="" style="width:38%;float:left;display:inline;font-weight:normal;">
                  <p class="aad_pop_line1_title_p"><b class="resut_table_state_1"></b>收款方：</p>
                  <input type="text" class="aad_pop_select_W100 " name="collect_person" value='<?= $flow_list['collect_person']; ?>' autocomplete="off">
                  <div class="errorBox" style="text-align:right;"></div>
              </li>
            <li class="" style="width:38%;float:left;display:inline;font-weight:normal;">
              <p class="aad_pop_line1_title_p"><b class="resut_table_state_1"></b>金额：</p>
              <input type="text" class="aad_pop_select_W70 test_money" name="money_number" value='<?= $flow_list['money_number']; ?>' autocomplete="off">元
              <div class="errorBox" style="text-align:right;"></div>
            </li>
          </ul>
        </div>
        <div style="width:98%; padding:1%;float:left;display:inline;">
          <ul>
            <li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
              <p class="aad_pop_line1_title_p"><b class="resut_table_state_1" style="font-weight:normal;"></b>费用类别：</p>
              <select class="aad_pop_select_W100" name="replace_money_type">
                <?php foreach ($config['money_type'] as $key => $val) { ?>
                  <option value="<?= $key; ?>" <?= $flow_list['money_type'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                <?php } ?>
              </select>
              <div class="errorBox"></div>
            </li>
            <li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
              <p class="aad_pop_line1_title_p"><b class="resut_table_state_1"></b>费用说明：</p>
              <input type="text" class="aad_pop_select_W100" name="money_name" value="<?= $flow_list['money_name']; ?>" autocomplete="off">
              <div class="errorBox" style="text-indent:92px;"></div>
            </li>

          </ul>
        </div>
        <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
          <ul>
              <li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
                  <p class="aad_pop_line1_title_p"><b class="resut_table_state_1" style="font-weight:normal;"></b>支付方式：</p>
                  <select class="aad_pop_select_W100" name="pay_type">
                      <?php foreach ($config['pay_type'] as $key => $val) { ?>
                          <option value="<?= $key; ?>" <?= $flow_list['pay_type'] == $key ? 'selected' : '' ?>><?= $val; ?></option>
                      <?php } ?>
                  </select>
                  <div class="errorBox"></div>
              </li>
              <li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
                  <p class="aad_pop_line1_title_p"><b class="resut_table_state_1"></b>凭证号码：</p>
                  <input type="text" class="aad_pop_select_W100" name="certificate_number" value="<?= $flow_list['certificate_number']; ?>" autocomplete="off">
                  <div class="errorBox" style="text-indent:92px;"></div>
              </li>

          </ul>
        </div>
          <div style="width:98%; padding:1%;float:left;display:inline;">
              <ul>
                  <li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
                      <p class="aad_pop_line1_title_p"><b class="resut_table_state_1">*</b>收付日期：</p>
                      <input type="text" class="aad_pop_select_W100 time_bg" name="replace_flow_time"
                             value="<?= $id ? $flow_list['flow_time'] : date('Y-m-d', time()); ?>"
                             onclick="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" autocomplete="off">
                      <div class="errorBox" style="text-indent:92px;"></div>
                  </li>
              </ul>
          </div>
          <div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
              <ul>
                  <li class="aad_pop_line1_title " style="width:60%;float:left;display:inline;font-weight:normal;">
                      <p class="aad_pop_line1_title_p"><b class="resut_table_state_1"></b>建档：</p>
                      <input type="text" class="aad_pop_select_W200"
                             value="<?= $id ? $flow_list['entry_department_name'] . " " . $flow_list['entry_signatory_name'] . " " . date("Y-m-d H:i:s", $flow_list['entry_time']) : ''; ?>"
                             autocomplete="off" disabled>
                      <div class="errorBox" style="text-indent:92px;"></div>
                  </li>
              </ul>
          </div>
        <table width="100%">
          <tbody>
          <tr>
            <td width="100" style="width: 100px;text-align:right" class="label aad_pop_p_T20">备注：</td>
            <td width="83%" class="aad_pop_p_T20"><textarea class="aad_pop_select_textare_W" name="replace_remark"><?= $flow_list['remark']; ?></textarea>
              <div class="errorBox"></div>
            </td>
          </tr>
          </tbody>
        </table>
        <table width="100%" align="center">
          <tbody>
          <tr>
            <td style="text-align:center" class="aad_pop_p_T20">
              <input type="hidden" id="bargain_id" value="<?= $c_id ?>">
              <input type="hidden" id="flow_id" value="<?= $id ?>">
              <button class="btn-lv1 btn-left" type="submit">确定</button>
              <button class="btn-hui1" type="button" onclick="closeParentWin('js_replace_pop');">取消</button>
            </td>
          </tr>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>
<!--实收实付添加弹窗结束-->
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
              <img alt="" src="<?php echo MLS_SOURCE_URL; ?>/mls_guli/images/v1.0/s_ico.png"></td>
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
</body>

<script type="text/javascript">
  $(function () {
    $("input[name='is_fee']").click(function () {
      if ($("input[name='is_fee']:checked").val() == 1) {
        $("input[name='replace_counter_fee']").removeAttr('disabled');
        $("select[name='replace_docket_type']").removeAttr('disabled');
        $("input[name='replace_docket']").removeAttr('disabled');
      } else {
        $("input[name='replace_counter_fee']").attr('disabled', 'true');
        $("select[name='replace_docket_type']").attr('disabled', 'true');
        $("input[name='replace_docket']").attr('disabled', 'true');
      }
    })

    //获取门店下经纪人
    $("#replace_flow_department").change(function () {
      var department_id = $('#replace_flow_department').val();
      if (department_id) {
        $.ajax({
          url: "/bargain_earnest_money/signatory_list/",
          type: "GET",
          dataType: "json",
          data: {
            department_id: department_id
          },
          success: function (data) {
            var html = "<option value=''>请选择</option>";
            if (data['result'] == 1) {
              for (var i in data['list']) {
                html += "<option value='" + data['list'][i]['signatory_id'] + "'>" + data['list'][i]['truename'] + "</option>";
              }
            }
            $('#replace_flow_signatory').html(html);
          }
        });
      } else {
        $('#replace_flow_signatory').html("<option value=''>请选择</option>");
      }
    });
  });
</script>
