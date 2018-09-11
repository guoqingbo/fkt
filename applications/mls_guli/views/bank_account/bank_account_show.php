
<script>
    window.parent.addNavClass(2);
</script>

<style>

  .limit-right2 {
    width: 100%;
  }
  .limit-set-out2:after {
    content: '';
    clear: both;
    display: block;
  }
</style>

<script type="text/javascript">
  $(function () {

    $.widget("custom.autocomplete", $.ui.autocomplete, {
      _renderItem: function (ul, item) {
        if (item.id > 0) {
          return $("<li>")
            .data("item.autocomplete", item)
            .append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.label+'</span><span class="ui_district">'+item.card_no+'</span></a>').appendTo(ul);
        } else {
          return $("<li>")
            .data("item.autocomplete", item)
            .append('<a class="ui-corner-all" tabindex="-1">' + item.label + '</a>')
            .appendTo(ul);
        }
      },
        _resizeMenu: function () {
            this.menu.element.css({
                "max-height": "240px",
                "overflow-y": "auto"
            });

        }
    });

    $("#search_keyword").autocomplete({
      source: function (request, response) {
        var term = request.term;
        //$("#dictionary_type_id").val("");
        $.ajax({
          url: "/bank_account/get_bank_account_info_by_kw/",
          type: "GET",
          dataType: "json",
          data: {
            keyword: term
          },
          success: function (data) {
            //判断返回数据是否为空，不为空返回数据。
            if (data[0]['id'] != '0') {
              //alert(data[0]['dictionary_id']);
              response(data);
            } else {
              response(data);
            }
          }
        });
      },
      minLength: 1,
      removeinput: 0,
      width: 2,
      select: function (event, ui) {
        if (ui.item.id > 0) {
          var search_keyword = ui.item.card_no;
          var id = ui.item.id;
          //操作
          $("#select_bank_account_id").val(id);
          $("#search_keyword").val(search_keyword);
          search_keyword_search(id, search_keyword);
          removeinput = 2;
        } else {
          removeinput = 1;
        }
      },
      close: function (event) {
        if (typeof(removeinput) == 'undefined' || removeinput == 1) {
          $("#select_bank_account_id").val("");
          //$("#search_keyword").val("");
        }
      }
    });

    $('#search_button').live('click', function () {
      var bank_account_id = $('#select_bank_account_id').val();
      var search_keyword = $('#search_keyword').val();
      window.location.href = '/bank_account/index/' + bank_account_id + '?view_type=search&search_keyword=' + encodeURIComponent(search_keyword);
      return false;
    });
  });

  function search_keyword_search(bank_account_id, bank_name) {
    window.location.href = '/bank_account/index/' + bank_account_id + '?search_keyword=' + encodeURIComponent(bank_name);
    return false;
  }

  jQuery.fn.limit = function () {
    var self = $(this);
    self.each(function () {
      var objString = $(this).text();
      var objLength = $(this).text().length;
      var num = $(this).attr("limit");
      if (objLength > num) {
        $(this).attr("title", objString);
        objString = $(this).text(objString.substring(0, num) + "...");
      }
    })
  }
</script>
<div class="tab_box" id="js_tab_box">
    <?php
    echo $user_menu;
    ?>
</div>

<!--主要内容-->

    <div class="limit-set-out2">
		<script>
			$(function(){
				$("li").hover(function(){
					$(this).toggleClass('hover');
				})
			});
		</script>

		<div class="limit-right2">
      <form name="search_form" id="search_form" method="post" action="">
		  <div style="display:block;">
        <div class="tool-bar limit-top mb10" id="js_search_box">
          <div class="limit-search ml10 fr"><input type="text" name="search_keyword" id="search_keyword" value="<?php echo $search_keyword ? $search_keyword : '请输入关键字查找'?>" onfocus="if(value=='<?php echo $search_keyword ? $search_keyword : '请输入关键字查找'?>'){value='';$(this).css('color','#535353');}" onblur="if(value==''){value='<?php echo $search_keyword ? $search_keyword : '请输入关键字查找'?>';$(this).css('color','#999');}" autocomplete="off" class="ui-autocomplete-input"><button id="search_button">搜索</button></div>
          <a href="javascript:void(0)" onClick="openWin('js_add_bank_account')" class="add_link" style="margin-top:0;"><span class="iconfont"></span>添加银行卡</a>
          <input name="select_bank_account_id" id="select_bank_account_id" value="" type="hidden">
          <h3><?php echo $store_name?></h3>
        </div>
			</div>
			<div class="table_all" style="margin-right:0; margin-left:0;">
				<div id="js_inner" class="inner" style="overflow-y: scroll; overflow-x:auto;">
					<table class="table">
              <thead>
                <tr>
                  <th class="c10">序号</th>
                  <th class="c10">开户行</th>
                  <th class="c10">开户行</th>
                  <th class="c20">开户支行</th>
                  <th>地址</th>
                  <th class="c20">卡号</th>
                  <th class="c10">操作</th>
                </tr>
              </thead>
							<tbody>
              <?php
              if ($signatory_all_info) {
                foreach ($signatory_all_info as $k => $v) { ?>
                  <tr height="40">
                    <td class="c10"><?= ($page_params['now_page'] - 1) * $page_params['list_rows'] + $k + 1 ?></td>
                    <td class="c10">
                      <?php
                      $bank_name = '';
                      foreach ($bank_info_list as $b) {
                        if ($b['id'] == $v['bank_id']) {
                          $bank_name = $b['key'];
                        }
                      }
                      ?>
                      <span style="width: 126px; height: 36px; background-position: 100% 100%; display:inline-block; vertical-align: middle;" class="banklogo banklogo-<?php echo $bank_name;?>" title="<?php echo $bank_name; ?>"></span>
                    </td>
                    <td class="c10"><?php echo $v['bank_name'] ?></td>
                    <td class="c20"><?php echo $v['bank_deposit'] ?></td>
                    <td><?php echo $v['card_name'] ?></td>
                    <td class="c20"><?php echo $v['card_no'] ?></td>
                    <td class="c10">
                      <a href="javascript:void(0);" onclick="modify_bank_account_pop(<?php echo $v['id']?>)">编辑</a>
                      <span style="margin:0 5px;color:#b2b2b2;">|</span>
                      <a href="javascript:void(0)" onclick="delete_bank_account(<?php echo $v['id'] ?>)">删除</a>
                    </td>
                  </tr>
                  <?php
                }
              } else { ?>
                <tr>
                  <td align="center" colspan='6'>暂无数据！</td>
                </tr>

              <?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="fun_btn clearfix" id="js_fun_btn" style="">
				<div class="get_page">
				<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
				</div>
			</div>
      </form>
		</div>

	</div>
	</div>

<!--编辑资料弹窗-->
<div id="js_edit_pop" class="iframePopBox" style="width:840px; height:470px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="auto" width="840px" height="470px" class='iframePop' src=""></iframe>
</div>

<!--提示框-->
<div id="js_del_do_result"	class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id="dialog_do_result_tip">操作成功</p>
                <button type="button" class="btn-lv1 btn-mid" onclick="location.href='/bank_account/index/'">确定</button>
            </div>
        </div>
    </div>
</div>
<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">操作成功！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="location.href='/bank_account/index/'">确定</button>
			</div>
		</div>
	</div>
</div>
<div id="js_pop_do_warning"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_warnig_tip">操作失败！</p>
			</div>
		</div>
	</div>
</div>
<!--添加银行卡-->
<div class="pop_box_g pop_box_add_shop" id="js_add_bank_account">
    <div class="hd">
        <div class="title">添加银行卡</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod" style="background:#fff;">
		  <label class="label clearfix"><span class="text">开户行：</span>
        <select id="bank_id" style="margin-top:4px;">
          <?php
          if (is_array($bank_info_list) && !empty($bank_info_list)) {
            foreach (array_reverse($bank_info_list) as $k => $v) {
              if ($k < count($bank_info_list) - 1) {
                ?>
                <option value="<?= $v['id'] ?>">├─<?= $v['name'] ?></option>
                <?php
              } else {
                ?>
                <option value="<?= $v['id'] ?>">└─<?= $v['name'] ?></option>
                <?php
              }
            }
          }
          ?>
        </select>
      </label>
      <label class="label clearfix"><span class="text">开户支行：</span><input class="text_input" type="text" placeholder="如：杭州文三路支行" id="bank_deposit"></label>
      <label class="label clearfix"><span class="text">地址：</span><input class="text_input" type="text" placeholder="如：杭州市下城区上塘路20号" id="card_name"></label>
      <label class="label clearfix"><span class="text">卡号：</span><input class="text_input" type="text" placeholder="如：6222057188888888" id="card_no"></label>
      <label class="label clearfix"><span class="text">确认卡号：</span><input class="text_input" type="text" placeholder="如：6222057188888888" id="card_no_confirm"></label>
      <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="add_bank_account()">保存</button>
    </div>
</div>

<!--修改银行卡-->
<div class="pop_box_g pop_box_add_shop" id="js_modify_bank_account">
  <div class="hd">
    <div class="title">修改银行卡</div>
    <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
  </div>
  <div class="mod">
    <label class="label clearfix"><span class="text">数据类型：</span>
      <select id="modify_bank_id" style="margin-top:4px;">
        <?php
        if (is_array($bank_info_list) && !empty($bank_info_list)) {
          foreach (array_reverse($bank_info_list) as $k => $v) {
            if ($k < count($bank_info_list) - 1) {
              ?>
              <option value="<?= $v['id'] ?>">├─<?= $v['name'] ?></option>
              <?php
            } else {
              ?>
              <option value="<?= $v['id'] ?>">└─<?= $v['name'] ?></option>
              <?php
            }
          }
        }
        ?>
      </select>
    </label>
    <label class="label clearfix"><span class="text">开户支行：</span><input class="text_input" type="text" placeholder="如：杭州文三路支行" id="modify_bank_deposit"></label>
    <label class="label clearfix"><span class="text">地址：</span><input class="text_input" type="text" placeholder="如：杭州市下城区上塘路20号" id="modify_card_name"></label>
    <label class="label clearfix"><span class="text">卡号：</span><input class="text_input" type="text" placeholder="如：6222057188888888" id="modify_card_no"></label>
    <label class="label clearfix"><span class="text">确认卡号：</span><input class="text_input" type="text" placeholder="如：6222057188888888" id="modify_card_no_confirm"></label>

    <input type="hidden" value="" id="bank_account_id">
    <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="modify_bank_account()">保存</button>
  </div>
</div>

<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_delete_dictionary2">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
        <div class="up_inner">
          <table class="del_table_pop">
              <tr>
                  <td class="c14">	<img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/s_ico.png"></td>
                  <td>
                    <p class="left" style="font-size:14px;color:#666;">您确定要注销此帐号吗?</p>
                  </td>
              </tr>
          </table>
          <button class="btn-lv1 btn-left" type="button" onclick="cancel_account();">确定</button>
          <button class="btn-hui1 JS_Close" type="button">取消</button>
        </div>
       </div>
    </div>
</div>


<script>

  $(function () {
    function re_width() {
      var h1 = $(window).height();
      $("#limit-table").height(h1 - 180);
      $(".limit-right2").show();
    };
    re_width();
//    openWin('mainloading');
    $(window).resize(function (e) {
      re_width();
//      setTimeout(function () {
//        $("#js_inner").css("height", ($("#js_inner").height() - 30) + "px");
//      }, 20)

    });

  });
  window.onload = function () {
//    $("#js_inner").css("height", ($("#js_inner").height() - 30) + "px");
  }

  //添加银行卡
  function add_bank_account() {
    var bank_id = $("#bank_id").val();
    var bank_deposit = $("#bank_deposit").val();
    var card_name = $("#card_name").val();
    var card_no = $("#card_no").val();
    var card_no_confirm = $("#card_no_confirm").val();

    if (!bank_deposit) {
      $("#dialog_do_warnig_tip").html("请输入开户支行");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!card_name) {
      $("#dialog_do_warnig_tip").html("请输入地址");
      openWin('js_pop_do_warning');
      return false;
    }

    if (!card_no) {
      $("#dialog_do_warnig_tip").html("请输入银行卡号");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!card_no_confirm) {
      $("#dialog_do_warnig_tip").html("请确认银行卡号");
      openWin('js_pop_do_warning');
      return false;
    }
    if (card_no_confirm !== card_no) {
      $("#dialog_do_warnig_tip").html("两次输入的卡号不相同，请重新输入");
      openWin('js_pop_do_warning');
      return false;
    }

    var data = {
      bank_id: bank_id,
      bank_deposit: bank_deposit,
      card_name: card_name,
      card_no: card_no
    };
    $.ajax({
      type: "POST",
      url: "/bank_account/add_bank_account",
      dataType: "json",
      data: data,
      cache: false,
      error: function () {
        $("#dialog_do_warnig_tip").html("系统错误");
        openWin('js_pop_do_warning');
        return false;
      },
      success: function (data) {
        if (data['errorCode'] == '401') {
          login_out();
          $("#jss_pop_tip").hide();
        }
        else if (data['errorCode'] == '403') {
          /*purview_none();
           $("#jss_pop_tip").hide();*/
          closeWindowWin('js_add_shop');
          $("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
          openWin('js_pop_do_warning');
          return false;
        } else {
          if (data.status == "success") {
            $("#dialog_do_success_tip").html(data.msg);
            openWin('js_pop_do_success');
          } else {
            $("#dialog_do_warnig_tip").html(data.msg);
            openWin('js_pop_do_warning');
          }
        }
      }
    });

  }

  // 修改数据弹框
  function modify_bank_account_pop(bank_account_id) {
    $.ajax({
      type: "POST",
      url: "/bank_account/modify_bank_account_pop",
      data: "bank_account_id=" + bank_account_id,
      cache: false,
      dataType: 'json',
      error: function () {
        alert("系统错误");
        return false;
      },
      success: function (data) {
        if (data) {
          $('#bank_account_id').val(data.id);
          $('#modify_bank_deposit').val(data.bank_deposit);
          $('#modify_card_no').val(data.card_no);
          $('#modify_card_no_confirm').val(data.card_no);
          $('#modify_card_name').val(data.card_name);
          $('#modify_bank_id').val(data.bank_id);
          openWin('js_modify_bank_account');
        } else {
          openWin('js_modify_bank_account');
        }
      }
    });
  }
  //修改数据
  function modify_bank_account() {
    var bank_account_id = $("#bank_account_id").val();
    var bank_id = $("#modify_bank_id").val();
    var bank_deposit = $("#modify_bank_deposit").val();
    var card_name = $("#modify_card_name").val();
    var card_no = $("#modify_card_no").val();
    var card_no_confirm = $("#modify_card_no_confirm").val();

    if (!bank_deposit) {
      $("#dialog_do_warnig_tip").html("请输入开户支行");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!card_name) {
      $("#dialog_do_warnig_tip").html("请输入地址");
      openWin('js_pop_do_warning');
      return false;
    }

    if (!card_no) {
      $("#dialog_do_warnig_tip").html("请输入银行卡号");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!card_no_confirm) {
      $("#dialog_do_warnig_tip").html("请确认银行卡号");
      openWin('js_pop_do_warning');
      return false;
    }
    if (card_no_confirm !== card_no) {
      $("#dialog_do_warnig_tip").html("两次输入的卡号不相同，请重新输入");
      openWin('js_pop_do_warning');
      return false;
    }

    var data = {
      bank_account_id: bank_account_id,
      bank_id: bank_id,
      bank_deposit: bank_deposit,
      card_name: card_name,
      card_no: card_no
    };

    $.ajax({
      type: "POST",
      url: "/bank_account/modify_bank_account",
      dataType: "json",
      data: data,
      cache: false,
      error: function () {
        $("#dialog_do_warnig_tip").html("系统错误");
        openWin('js_pop_do_warning');
        return false;
      },
      success: function (data) {
        if (data['errorCode'] == '401') {
          login_out();
          $("#jss_pop_tip").hide();
        }
        else if (data['errorCode'] == '403') {
          closeWindowWin('js_r_shop');
          $("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
          openWin('js_pop_do_warning');
        } else {
          if (data.status == "success") {
            $("#dialog_do_success_tip").html(data.msg);
            openWin('js_pop_do_success');
          } else {
            $("#dialog_do_warnig_tip").html(data.msg);
            openWin('js_pop_do_warning');
          }
        }
      }
    });

  }

  //删除银行卡
  function delete_bank_account(id) {
    if (confirm('确定要删除此银行卡？')) {
      var data = {bank_account_id: id};
      $.ajax({
        type: "POST",
        url: "/bank_account/delete_bank_account",
        dataType:"json",
        data: data,
        cache: false,
        error: function () {
          $("#dialog_do_warnig_tip").html("系统错误");
          openWin('js_pop_do_warning');
          return false;
        },
        error: function () {
          $("#dialog_do_warnig_tip").html("系统错误");
          openWin('js_pop_do_warning');
          return false;
        },
        success: function (data) {
          $("#dialog_do_result_tip").html(data.msg);
          openWin('js_del_do_result');
        }
      });
    } else {
      return false;
    }
  }

  jQuery.fn.limit = function () {
    var self = $(this);
    self.each(function () {
      var objString = $(this).text();
      var objLength = $(this).text().length;
      var num = $(this).attr("limit");
      if (objLength > num) {
        $(this).attr("title", objString);
        objString = $(this).text(objString.substring(0, num) + "...");
      }
    })
  }
</script>
