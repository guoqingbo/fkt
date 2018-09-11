<!DOCTYPE html>
<html>
<head>
  <title>登录</title>
  <meta charset="utf-8">
  <script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js"></script>
  <script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=backspace.js"></script>
  <style type="text/css">
  body{margin:0;padding:0;font-size: 14px;}
  div,ul,li,p,b,span,dl,dd,dt,strong{list-style:none;margin:0;padding:0;}
  input,textarea{margin:0;padding:0;outline:medium;}
  a,a:link{text-decoration:none;font-size: 14px;}
  a:hover{text-decoration:underline;}
  .login_index{width:440px;height:335px;float:left;display:inline;overflow:hidden;background:#f0f0f0;}
  .login_index_logo{width:440px;height:124px;float:left;display:inline;position:relative;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/newlogin/logo.jpg)}
  .login_index_btn{width:24px;height:24px;position:absolute;z-index:22;top:0;cursor:pointer;}
  .btn_samll{width:24px;height:24px;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/newlogin/btn2.png) no-repeat 0 0;overflow:hidden;right:28px;}
  .btn_samll:hover{background-color:#4775b4;}
  .btn_close{width:24px;height:24px;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/newlogin/btn2.png) no-repeat -24px 0;overflow:hidden;right:0;}
  .btn_close:hover{background-color:#921616;}

  .login_index_dl{width:90%;padding:0 5%;float:left;margin-top:20px;}
  .login_index_dl dd{width:100%;float:left;display:inline;}
  .login_index_dl dt{width:100%;float:left;display:inline;margin-top:11px;}
  .span_name{width:65px;float:left;display:inline;line-height:37px;color:#b0b0b0;font-family:"微软雅黑";}
  .span_input{width:251px;height:41px;float:left;display:inline;overflow:hidden;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/newlogin/input_bg.png) no-repeat 0 0;}
  .span_apply{float:right;line-height:41px;color:#419dd4;}
  .span_apply a{color:#419dd4;}
  .input_text{width:221px;height:36px;float:left;display:inline;background:none;border:0;line-height:36px;color:#000;font-family:"微软雅黑";padding-left:30px;margin-top:3px;}
  .input_text_name{background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/newlogin/name.png) no-repeat 8px 5px;}
  .input_text_psd{background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/newlogin/password.png) no-repeat 8px 5px;}
  .login_index_submint{width:249px;height:40px;float:left;display:inline;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/newlogin/btn_07.png) no-repeat center;margin:20px 0 0 86px; }
  .sunmit_btn{width:100%;height:40px;float:left;text-align:center;line-height:40px;color:#FFF;font-family:"微软雅黑";background:none;border:none;cursor:pointer;}
  .login_index_errormsg{line-height:22px;font-size:12px; clear:both; text-align:center;padding:10px 0 0 0;color:red;}
  </style>


 </head>
 <body id="body">
  <div class="login_index">
		<div class="login_index_logo" id="titlebox">
			<span class="login_index_btn btn_samll" onclick="minSize();"></span>
			<span class="login_index_btn btn_close" onclick="exitForm();"></span>
		</div>
<script type="text/javascript">
	document.oncontextmenu = function(e) {
        return false;
    };
    var move = false;
	var EventUtil = {
		addHandler:function(elem,type,handler){
			if(elem.addEventListener)
			{
				elem.addEventListener(type,handler,false);
			}else if(elem.attachEvent)
			{
				elem.attachEvent("on"+type,handler);
			}else
			{
				elem["on"+type]=handler;
			}
		},
		removeHandler:function(elem,type,handler){
			if(elem.removeEventListener)
			{
				elem.removeEventListener(type,handler,false);
			}else if(elem.detachEvent)
			{
				elem.detachEvent("on"+type,handler);
			}else
			{
				elem["on"+type]=null;
			}
		},
		getEvent:function(event){
			return event?event:window.event;
		},
		getTarget:function(event){
			return event.target||event.srcElement;
		},
		preventDefault:function(event){
			if(event,preventDefault){
				event.preventDefault();
			}else{
				event.returnValue = false;
			}
		},
		stopPropagation:function(event){
			if(event.stopPropagation){
				event.stopPropagation();
			}else{
				event.cancelBubble=true;
			}
		}

	};

	//移动窗体
	var div = document.getElementById("titlebox");
	var screenx = screeny = 0;
	var start = 0;
	EventUtil.addHandler(div,"mousemove",function(event){
		if(move == true)
		{
			event = EventUtil.getEvent(event);

			$("#titlebox").css("cursor","move");

			if(start == 0)
			{
				screenx = event.screenX;
				screeny = event.screenY;
			}
			else
			{
				movex = event.screenX - screenx;
				movey = event.screenY - screeny;
				moveForm(movex, movey);

				screenx = event.screenX;
				screeny = event.screenY;
			}

			start = 1;
		}
	});
	EventUtil.addHandler(div,"mousedown",function(event){
		move = true;
		$("#titlebox").css("cursor","move");
	});
	EventUtil.addHandler(div,"mouseup",function(event){
		move = false;
		start = 0;
		$("#titlebox").css("cursor","default");
	});
	EventUtil.addHandler(div,"mouseout",function(event){
		move = false;
		start = 0;
		$("#titlebox").css("cursor","default");
	});

	function moveForm(x, y)
	{
		document.title = "登录#move#"+x+"*"+y;
	}
	function changeSize(size)
	{
		document.title = "登录#resize#"+size;
	}
	function exitForm()
	{
		document.title = "登录#exit#0";
	}
	var maxsize = true;
	function maxSize()
	{
		maxsize == true ? document.title = "登录#maxsize#0" : document.title = "登录#normalsize#0";
		maxsize = maxsize == true ? false : true;
	}
	function minSize()
	{
		document.title = "登录#minsize#0";
	}
	function targetblank(url)
	{
		document.title = "登录#taget_blank#"+url;
		setTimeout(function(){document.title = "登录";}, 500);
	}
	function openNewPage(e)
	{
		e = e || null;
		Cef.openMyPc(e);
	}
	function closeLoading()
	{
		document.title = "登录#closeloading#0";
	}
	closeLoading();
	changeSize('440*335');
</script>
		<?php if($update){ ?>
		<dl class="login_index_dl">
			<dd>
				<div style="margin: 10px auto; font-size:16px;color:#333;line-height:24px;height:100px;overflow-x:hidden; width:320px;overflow-y:auto;">
					<b>有新版本更新啦！</b><br>
					1、优化了一个很傻逼的问题；<br>
					2、修正了一个会崩溃的问题。<br>
				</div>
			</dd>
			<dd>
				<div style="margin:0 auto;line-height:24px;height:30px; width:130px;">
					<a href="###" onclick="targetblank('<?php echo MLS_URL;?>/homepage/download'); setTimeout(function(){exitForm();}, 1000);" style="color:blue; font-size:16px;">点此立刻下载更新</a>
				</div>
			</dd>
		</dl>
		<?php }else{ ?>
		<dl class="login_index_dl">
			<dd>
				<span class="span_name">手机号</span>
				<span class="span_input"><input type="text" value="" onchange="$('.input_text_psd').val('');" class="input_text input_text_name" onkeypress="if(event.keyCode==13) {login();}" onkeyup="this.value=this.value.replace(/\D/g,'')"  onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="11"></span>
				<span class="span_apply"><a href="###" onclick="targetblank('<?php echo MLS_URL;?>/register/');">申请帐号</a></span>
			</dd>
			<dt>
				<span class="span_name">密　码</span>
				<span class="span_input"><input type="password" onkeypress="if(event.keyCode==13) {login();}" value="" class="input_text input_text_psd"></span>
				<span class="span_apply"><a href="###" onclick="targetblank('<?php echo MLS_URL;?>/login/findpw/');">找回密码</a></span>
			</dt>

		</dl>

		<span class="login_index_submint"><input type="button" onclick="login();" value="登&nbsp;&nbsp;&nbsp;&nbsp;录" class="sunmit_btn"></span>
		<p class="login_index_errormsg"></p>
		<?php } ?>
  </div>
	<script>
	var loginstate = true;
	function login()
	{
		if(loginstate == true)
		{
			var uname = $('.input_text_name').val();
			var upwd = $('.input_text_psd').val();

			$.post("/login/signin/",{phone:uname,password:upwd,action:'pcsignin'},function(result){
				var obj = jQuery.parseJSON(result);
				if(obj.result == 1)
				{
					$(".login_index").hide();
					changeSize('1280*768');
					window.location.href = obj.data;
				}
				else
				{
					$('.login_index_errormsg').html(obj.msg);
				}
			});
		}
	}
	</script>
 </body>
</html>
