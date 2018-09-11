<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=$title?></title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css,css/v1.0/register_login_password.css" rel="stylesheet" type="text/css">
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/home.css" rel="stylesheet" type="text/css">
    <script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js,openWin.js"></script>
    <script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=backspace.js,jquery.validate.min.js,register_login_password.js,jquery.elastislide.js"></script>
    <script src="/source/mls/js/v1.0/layer/layer.js"></script>
</head>

    <body id='body'>
        <div class="header-seo">
          <a class="logo-seo" href="javascript:void(0);" onclick="window.location.reload(true);" title="<?=$title?>"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/codi/logo/codi-white.png"></a>
          <div id="nav-seo">
            <div class="nav-seo"  style="overflow:hidden;margin-left:10px;">
              <ul>
                <?php
                  $index_url = '';
                  if (is_array($menu) && !empty($menu)) {
                    foreach ($menu as $module => $value) {
                      $select_style = '';
                      if ($module == 0) {
                        $select_style = 'class="nav-on"';
                        $index_url = $value['url'];
                      }
                  ?>
                      <li id="<?= $value['id'] ?>"<?= $select_style ?>>
                        <a class="nav-<?= $value['style'] ?>" href="javascript:void(0);" onClick="show_html('<?= $value['url'] ?>', this)"><?= $value['name'] ?></a>
                      </li>
                  <?php }} ?>
                  <!-- 自己添加简易退出登录按钮 -->
                  <!--                  <li><a href="--><?php //echo MLS_URL; ?><!--/login/quit">退出登录</a></li>-->
                  <li id="">
                      <a class="" href="javascript:void(0);"
                         onClick="login_out()">退出登录</a>
                  </li>
              </ul>
            </div>
            <div class="es-nav" >
              <a href="javascript:void(0);" class="es-nav-prev" style="display:none;">Previous</a>
              <a href="javascript:void(0);" class="es-nav-next" style="display:none;">Next</a>
            </div>
          </div>
        </div>
            <?php
            if($deviceid != '' && $osid > 0)
            {
                $index_url = MLS_URL.'/login/pc_signin/'.$deviceid.'/'.$osid.'/iframe/';
            }
            ?>
            <iframe src="<?=$index_url?>" frameBorder="0" scrolling="no" width="100%" height="600"id="mainIframe" ></iframe>

        <div class="footer">
        	<ul class="left">
        		<li><a href="javascript:void(0);" onclick="openWin('js_modify_pass')">修改密码<span></span></a></li>
                <li><a href="javascript:void(0);" onclick="to_url('bulletin');">我的消息<span
                                id="message_num"> <?php echo $message_num; ?></span></a></li>
                <!--<li><a href="javascript:void(0);" onclick="to_url('my_remind');">事件提醒<span> <?php echo $remind_num;?></span></a></li>
                <li><a href="javascript:void(0);" onclick="to_url('task');">跟进任务<span> <?php echo $task_num;?></span></a></li>-->
                <li class="trumpet">
                    <!--<script>
                        setInterval("getnewdata()",30000);
                        function getnewdata()
                        {

                            $.ajax({
                                url: "<?php echo MLS_URL;?>/user_request/checknotice/",
                                type: "GET",
                                dataType: "html",
                                success: function(data) {
                                    //$("#marqueebox").html(data);
                                }
                            });

                        }
                    </script>-->
                </li>
            </ul>

            <div class="kfqq" style="float:right; padding-right:20px;"><a href="javascript:void(0);" onclick="my_friends()" style="margin-left:20px; font-weight: bold;">我的好友</a><!--<a href="javascript:void(0);" class="notice_a"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/notice.png"> 消息中心</a>--><a href="javascript:void(0);" onclick="clear_suggest();openWin('js_feedback')" style="margin-left:20px; font-weight: bold;">意见反馈</a></div>
        </div>

        <div class="pop_box_g pop_box_g03 pop_box_password" id="js_modify_pass">
          <div class="hd">
            <div class="title">修改密码</div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
          </div>
          <div class="find_password">
            <form action="" id="js_r_password">
              <dl class="list">

                <dd class="list_item clearfix">
                  <label class="label">原始密码</label>
                  <div class="info">
                    <div class="detail"><input type="password" id="old_password"  name="old_password" class="input_t w240" >

                      <label class="placeholder_for" for="old_password">请输入原始密码</label>
                    </div>
                    <div class="error_add"></div>
                  </div>
                </dd>

                <dd class="list_item clearfix">
                  <label class="label">新密码</label>
                  <div class="info">
                    <div class="detail"><input type="password" id="new_password"  name="new_password" class="input_t w240" >

                      <label class="placeholder_for" for="new_password">请输入新密码</label>
                    </div>
                    <div class="error_add"></div>
                  </div>
                </dd>

                <dd class="list_item clearfix">
                  <label class="label">确认密码</label>
                  <div class="info">
                    <div class="detail"><input type="password" id="equal_password" name="equal_password" class="input_t w240" >
                      <label class="placeholder_for"  for="equal_password">请重复输入新密码</label>
                    </div>
                    <div class="error_add"></div>
                  </div>
                </dd>
                <dd class="list_item clearfix">
                  <label class="label">&nbsp;</label>
                  <div class="info">
                    <button type="button" class="btn_submit" onclick="modify_pass();">修改密码</button>
                    <button type="button" class="btn_submit btn_none JS_Close">取消</button>
                  </div>
                </dd>
              </dl>
            </form>
          </div>
        </div>

        <div class="pop_box_g pop_box_g03" id="js_feedback">
            <div class="hd">
                <div class="title">意见反馈</div>
                <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
            </div>
           <div class="find_password" style="width:500px; height:300px;">
               <form action="/welcome/feedback/" id="js_r_feedback">
                      <dl class="list">

                      <dd class="list_item clearfix">
                          <label class="label">反馈内容</label>
                          <div class="info">
                              <div class="detail">
                                <textarea class="input_t" style="width:360px;height:200px;" id="feedback" name="feedback"></textarea>
                                <label class="placeholder_for" for="feedback">请详细描述您遇到的问题，您的意见或者您的想法</label>
                              </div>
                              <div class="error_add"></div>
                          </div>
                      </dd>

                      <dd class="list_item clearfix">
                          <label class="label">&nbsp;</label>
                          <div class="info">
                             <button type="button" class="btn_submit" onclick="suggest();">提交反馈</button>
                             <button type="button" class="btn_submit btn_none JS_Close">取消</button>
                          </div>
                      </dd>
                  </dl>
               </form>
           </div>
        </div>
		<!--操作结果弹出提示框-->
		<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
			<div class="hd">
				<div class="title">提示</div>
				<div class="close_pop">
					<a href="javascript:void(0);" onclick="sub_form();" title="关闭" class="JS_Close iconfont"></a>
				</div>
			</div>
			<div class="mod">
				<div class="inform_inner">
					<div class="up_inner">
						 <p class="text" id='dialog_do_itp'></p>
					</div>
				</div>
			</div>
		</div>
		<!--操作结果弹出提示框-->
		<div id="js_pop_do_suggest" class="pop_box_g pop_see_inform pop_no_q_up" >
			<div class="hd">
				<div class="title">提示</div>
				<div class="close_pop">
					<a href="javascript:void(0);" onclick="close_suggest();" title="关闭" class="JS_Close iconfont"></a>
				</div>
			</div>
			<div class="mod">
				<div class="inform_inner">
					<div class="up_inner">
						 <p class="text" id='dialog_t'></p>
					</div>
				</div>
			</div>
		</div>


		<div class="notice" style="display:none;" id="notice">
			<div class="hdr">
				<a class="right" title="关闭" href="javascript:void(0);" id="close" onclick="update_all_message_pop();">关闭</a>
				<h3>消息中心</h3>
			</div>
			<div class="modr">
				<h4 id="notice_title"></h4>
						<p><div id='notice_text'></div><a title="关闭" href="javascript:void(0);" onclick="update_message_pop();">点击查看详情 <em>&gt;&gt;</em></a></p>
				<p class="morer"><a  href="javascript:void(0);" class="up" onclick="previous_one();" style="display: none">上一条</a>&#12288;&#12288;<a  href="javascript:void(0);" class="down" onclick="next_one();" style="display: none">下一条</a><span>共<em id="list_num" class="f0"></em>条</span></p>
					<input type="hidden" name="notice_url">
						<input type="hidden" name="notice_id" >
						<input type="hidden" name="time">
				</div>
		</div>

    <!-- 群发队列弹窗 -->
<!--	<div id="js_publish_queue_pop" style="position:absolute;top:20%;left:30%;display:none;">
		<iframe frameborder="0" scrolling="auto" width="400px" height="400px" src="/group_site_deal/index" allowTransparency="true"></iframe>
	</div>-->
	<!--<div id="js_publish_queue_pop" class="iframePopBox" style=" width:400px; height:400px; display:none;">
        <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
        <iframe frameborder="0" scrolling="no" width="400" height="400" class="iframePop" src="/group_site_deal/index"></iframe>
    </div>-->


		<!--我的好友弹窗-->
		<div id="js_friends_pop"  style="width:247px; height:460px;position:absolute;right:18px;bottom:20px;display:none">
			<iframe frameborder="0" scrolling="auto" width="250px" height="460px" name="friend_iframe" src="/cooperate_friends/index"></iframe>
		</div>
        <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/loading.gif" id="mainloading" >
        <script>
          function close_publish_queue_pop() {
            $("#js_publish_queue_pop").hide();
            $('#GTipsCoverjs_publish_queue_pop').remove();
          }

          function to_url(data) {
            openWin('mainloading');
            if ('my_remind' == data) {
              $('#mainIframe').attr('src', '<?php echo MLS_URL;?>/my_remind/');
            } else if ('bulletin' == data) {
              $('#mainIframe').attr('src', '<?php echo MLS_URL;?>/message/bulletin/');
            } else if ('task' == data) {
              $('#mainIframe').attr('src', '<?php echo MLS_URL;?>/' + $("input[name='notice_url']").val());
            } else if ('collection_house' == data) {
              $('#mainIframe').attr('src', '<?php echo MLS_URL;?>/house_collections/collect_sell/');
            } else if ('friend' == data) {
              $('#mainIframe').attr('src', '<?php echo MLS_URL;?>/sell/friend_lists_pub/');
            }
          }

          function suggest() {
            var feedback = $('#feedback').val();
            if (!feedback) {
              $('#dialog_do_itp').html('请输入您的意见或建议');
              openWin('js_pop_do_success');
              return false;
            }
            var data = {
              'feedback': feedback
            };
            $.ajax({
              type: "POST",
              url: "/welcome/save_suggest",
              data: data,
              cache: false,
              error: function () {
                alert("系统错误");
                return false;
              },
              success: function (return_data) {
                if (1 == return_data) {
                  $('#dialog_t').html('提交成功');
                  openWin('js_pop_do_suggest');
                } else {
                  $('#dialog_t').html('提交失败');
                  openWin('js_pop_do_suggest');
                }
              }
            });
          }
          //清空建议弹窗
          function clear_suggest() {
            $('#feedback').val('');
            $('.placeholder_for').show();
          }
          //关闭建议弹窗
          function close_suggest() {
            $("#js_feedback").hide();
          }

          function modify_pass() {
            var old_password = $('#old_password').val();
            var new_password = $('#new_password').val();
            var equal_password = $('#equal_password').val();
            if (!old_password) {
              $('#dialog_do_itp').html('请输入正确的原密码');
              openWin('js_pop_do_success');
              return false;
            }
            if (!new_password) {
              $('#dialog_do_itp').html('请输入新密码');
              openWin('js_pop_do_success');
              return false;
            }
            if (!equal_password) {
              $('#dialog_do_itp').html('两次密码输入不一致');
              openWin('js_pop_do_success');
              return false;
            }
            var data = {
              'old_password': old_password,
              'new_password': new_password,
              'equal_password': equal_password
            };
            $.ajax({
              type: "POST",
              url: "/broker/modify_password",
                dataType: "json",
              data: data,
              cache: false,
              error: function () {
                alert("系统错误");
                return false;
              },
              success: function (return_data) {
                  if ('password_not_true' == return_data["result"]) {
                  $('#dialog_do_itp').html('请输入正确的原密码');
                  openWin('js_pop_do_success');
                  } else if ('password_not_same' == return_data["result"]) {
                  $('#dialog_do_itp').html('两次密码输入不一致');
                  openWin('js_pop_do_success');
                  } else if (1 == return_data["result"]) {
                  $('#dialog_do_itp').html('修改成功');
                  openWin('js_pop_do_success');
                } else {
                  $('#dialog_do_itp').html('修改失败');
                  openWin('js_pop_do_success');
                }
              }
            });
          }

          height_resize();
          openWin('mainloading');
          //跟弹窗相关的全局变量
          var num = 0;     //消息数组key值
          var list = null; //消息数组
          var list_num = 0;//记录消息数量
          var num_max = null;//消息数组key最大值
          var time = 0;    //计时

          //弹窗翻看上一条
          function previous_one() {
            if (num > 0) {
              num--;
              if (num == 0) {
                $(".up").hide();
              } else {
                $(".up").css('display', 'inline');
                $(".down").css('display', 'inline');
              }
              $("#notice_title").text(list[num]['title']);
              if (list[num]['message'].length > 80) {
                $("#notice_text").text(list[num]['message'].substring(0, 80) + "......");
              } else {
                $("#notice_text").text(list[num]['message']);
              }
              $("input[name='notice_url']").val(list[num]['url']);
              $("input[name='notice_id']").val(list[num]['id']);
            }
          }

          //弹窗翻看下一条
          function next_one() {
            if (num < num_max) {
              num++;
              if (num == num_max) {
                $(".down").hide();
              } else {
                $(".up").css('display', 'inline');
                $(".down").css('display', 'inline');
              }
              $("#notice_title").text(list[num]['title']);
              if (list[num]['message'].length > 80) {
                $("#notice_text").text(list[num]['message'].substring(0, 80) + "......");
              } else {
                $("#notice_text").text(list[num]['message']);
              }
              $("input[name='notice_url']").val(list[num]['url']);
              $("input[name='notice_id']").val(list[num]['id']);
            }
          }

          //点击查看消息详情，同时关闭该消息弹窗
          function update_message_pop() {
            to_url('task');
            var id = $("input[name='notice_id']").val();
            $.ajax({
              url: "<?php echo MLS_URL;?>/user_request/update_message_pop/" + id,
              type: "GET",
              dataType: "json",
              success: function (data) {
                var msg = data.msg;
                if (msg == "ok") {
                  list = data.message_array;
                  num_max = list.length - 1;
                  list_num = list.length;
                  time = 0;
                  if (num > num_max) {
                    num = num_max;
                  }
                  if (list.length > 1) {
                    $(".down").css('display', 'inline');
                  }
                  $("#notice_title").text(list[num]['title']);
                  if (list[num]['message'].length > 80) {
                    $("#notice_text").text(list[num]['message'].substring(0, 80) + "......");
                  } else {
                    $("#notice_text").text(list[num]['message']);
                  }
                  $("input[name='notice_url']").val(list[num]['url']);
                  $("input[name='notice_id']").val(list[num]['id']);
                  $("#list_num").text(list_num);
                } else if (msg == "failed") {
                  $("#notice").attr("style", "display:none");

                }
              }
            });
          }

          //关闭所有消息弹窗
          function update_all_message_pop() {
            $.ajax({
              url: "<?php echo MLS_URL;?>/user_request/update_all_message_pop/",
              type: "GET",
              dataType: "json",
              data: {
                list: list
              },
              success: function (data) {

              }
            });
            $("#notice").attr("style", "display:none");
          }

          //定期检查是否有新的弹窗消息
          function getnewdata() {
            if ($('#notice').is(':hidden')) {
              $.ajax({
                url: "<?php echo MLS_URL;?>/user_request/checknotice/",
                type: "GET",
                dataType: "json",
                success: function (data) {
                  var msg = data.msg;
                  if (msg == "ok") {
                    num = 0;
                    time = 0;
                    list = data.message_array;
                    list_num = list.length;
                    num_max = list.length - 1;
                    $(".up").hide();
                    $(".down").hide();
                    if (list.length > 1) {
                      $(".down").css('display', 'inline');
                    }
                    $("#notice_title").text(list[num]['title']);
                    if (list[num]['message'].length > 80) {
                      $("#notice_text").text(list[num]['message'].substring(0, 80) + "......");
                    } else {
                      $("#notice_text").text(list[num]['message']);
                    }
                    $("input[name='notice_url']").val(list[num]['url']);
                    $("input[name='notice_id']").val(list[num]['id']);
                    $("#notice").attr('style', 'display:block');
                    $("#list_num").text(list_num);
                  }
                  else if (msg == "login_at_other_pc") //增加一个单点登录的判断
                  {
                    try {
                      //调用客户端方法
                      external.go2Login();
                    }
                    catch (e) {
                    }
                  }
                }
              });
            }
          }
          getnewdata();
          //setInterval("getnewdata()",15000);
          //定期关闭长期存在的弹窗
          function close_pop() {
            time = time + 100;
            if (time >= 1000) {
              $("#notice").attr("style", "display:none");
              time = 0;
              update_all_message_pop();
            }
          }
          //          setInterval("close_pop()", 60000);

          $(function () {
            document.oncontextmenu = function (e) {
              return false;
            };
            //	height_resize();
            $(window).resize(function (e) {//窗口改变大小时 计算
              height_resize();
            });


            $("#nav dd").hover(function () {
              $("#nav dd").removeClass("hover");
              $(this).addClass("hover");
            }, function () {
              $("#nav dd").removeClass("hover");
            });

            $.ajax({
              url: "<?php echo MLS_URL;?>/user_request/login_checknotice/",
              type: "GET",
              dataType: "json",
              success: function (data) {
                var msg = data.msg;
                if (msg == "ok") {
                  list = data.message_array;
                  num_max = list.length - 1;
                  list_num = list.length;
                  time = 0;
                  if (list.length > 1) {
                    $(".down").css('display', 'inline');
                  }
                  $("#notice_title").text(list[num]['title']);
                  if (list[num]['message'].length > 80) {
                    $("#notice_text").text(list[num]['message'].substring(0, 80) + "......");
                  } else {
                    $("#notice_text").text(list[num]['message']);
                  }
                  $("input[name='notice_url']").val(list[num]['url']);
                  $("input[name='notice_id']").val(list[num]['id']);
                  $("#notice").attr('style', 'display:block');
                  $("#list_num").text(list_num);
                }
              }
            });

            $('#close').live('click', function () {
              $('#notice').attr('style', 'display:none;');
            });

          });
          function height_resize() {
            var _height = document.documentElement.clientHeight;
            _height > 693 ? $("#mainIframe").height(_height - 106) : $("#mainIframe").height(587);
          }
          //导航
          function show_html(url, obj) {
              $("#mainIframe").attr("src", url);
              $(obj).parent().addClass("on").siblings().removeClass("on");
              openWin('mainloading');
              //$("#GTipsCovermainloading").remove();
          }
          // 退出登录
          function login_out() {
              // 提示消息
              layer.confirm('您确定退出系统吗？', {
                  title: '',
                  btn: ['确定', '取消'] //按钮
              }, function(index){
                  var url = '<?php echo MLS_URL; ?>/login/quit';
                  window.location.href = url;
                  layer.close(index);
                  openWin('mainloading');
              })
          }

          function showHide() {
            $("#GTipsCovermainloading").remove();
            $("#mainloading").hide();
          }


          //顶部导航
          function addNavClass(Id) {
            $("#nav-seo li").removeClass("nav-on");
            $("#nav-seo li").each(function (index, element) {
              var _id = $(this).attr("id");
              if (_id == Id) {
                $(this).addClass("nav-on");
              }
            });
          }

          function refreshOnTime() {
            window.frames["friend_iframe"].window.refreshOnTime();
          }

          function my_friends() {
            if ($('#js_friends_pop').css('display') == "block") {
              $('#js_friends_pop', parent.document).css('display', 'none');
            } else {
              $('#js_friends_pop').css('display', 'block');
              window.frames['friend_iframe'].location.href = "/cooperate_friends/index";
            }
          }

          $(function () {
            var num_show = 0, nav_l = 0, nav_w = 83;

            $(window).resize(function (e) {//窗口改变大小时 计算
              nav_Sroll();
            });

            function nav_Sroll() {
              var aScreen_W = $(window).width(); //获取屏幕宽度
              var Deal_Area = aScreen_W - 250;
              var Deal_Num = Math.floor(Deal_Area / nav_w);
              var Deal_Area_View = Deal_Num * nav_w;
              var aWidth_Nav = $(".nav-seo li").length * nav_w;
              nav_l = $(".nav-seo li").length - Deal_Num > 0 ? $(".nav-seo li").length - Deal_Num : 0;

              $(".nav-seo ul").css({"width": aWidth_Nav + "px"});
//              console.log(num_show, nav_l, Deal_Area_View >= aWidth_Nav)
              if (Deal_Area_View >= aWidth_Nav) {

                $(".nav-seo").css("width", Deal_Area_View + "px");
                if(num_show > 0) {
                  if(num_show >= nav_l) {
                    $(".es-nav").show();
                    $(".es-nav-prev").show();
                    $(".es-nav-next").hide();
                  } else {
                    $(".es-nav").show();
                    $(".es-nav-prev").show();
                    $(".es-nav-next").show();
                  }
                } else if(num_show == 0) {
                  $(".es-nav").hide();
                  $(".es-nav-prev").hide();
                  $(".es-nav-next").hide();
                } else if(num_show >= nav_l) {
                  $(".es-nav").show();
                  $(".es-nav-prev").show();
                  $(".es-nav-next").hide();
                }
                //alert("a")
              } else {

                $(".nav-seo").css({"width": Deal_Area_View + "px"});
                $(".es-nav-next").css("left", (Deal_Area_View + 20) + "px");

                if(num_show > 0) {
                  if(num_show >= nav_l) {
                    $(".es-nav").show();
                    $(".es-nav-prev").show();
                    $(".es-nav-next").hide();
                  } else {
                    $(".es-nav").show();
                    $(".es-nav-prev").show();
                    $(".es-nav-next").show();
                  }
                } else if(num_show == 0) {
                  $(".es-nav").show();
                  $(".es-nav-prev").hide();
                  $(".es-nav-next").show();
                } else if(num_show >= nav_l) {
                  $(".es-nav").show();
                  $(".es-nav-prev").show();
                  $(".es-nav-next").hide();
                }
                //alert("b");
              }
            }

            nav_Sroll();

            $(".es-nav-next").on("click", function () {

              num_show++;
              $(".es-nav-prev").show();

              if (num_show >= nav_l) {
                $(".es-nav-next").hide();
              }
              else {
                $(".es-nav-next").show();
              }
              $(".nav-seo ul").animate({"margin-left": (-num_show * nav_w) + "px"}, 500);
//              console.log(num_show, nav_l)
            })

            $(".es-nav-prev").on("click", function () {
              num_show--;
              if(num_show < nav_l)
                $(".es-nav-next").show();
              if (num_show == 0) {
                $(".es-nav-prev").hide();
              }
              $(".nav-seo ul").animate({"margin-left": (-num_show * nav_w) + "px"}, 500);
//              console.log(num_show, nav_l)
            })
          })
        </script>
        <style>
        .js_GTipsCoverWxr{top:97px !important; width:1px !important; height:1px !important;overflow:hidden;}
        .mask_bg{width:100%;height:90px;float:left;display:none;background:#000;position:absolute;left:0;top:0;z-index: 9;opacity:0;filter:alpha(opacity=0);filter: progid:DXImageTransform.Microsoft.Alpha(opacity=0);}
        </style>
        <div class="mask_bg"></div>
        <div id="noneClick" style="position:absolute;top:0;left:0;width:100%;height:100px;background:#FFF; display:none;filter:(opacity=0);opacity:0;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0)"></div>
        <div id="noneClick2" style="position:absolute;bottom:0;left:0;width:100%;height:30px;background:#FFF; display:none;filter:(opacity=0);opacity:0;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0)"></div>
    </body>
</html>
