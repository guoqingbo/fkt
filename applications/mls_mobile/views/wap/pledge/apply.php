<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="email=no" name="format-detection" />
    <title>申请抵押贷</title>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/ajd.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/zwsPop.css" type="text/css"/>
</head>
<body>
<div class="wrapper  apply_dyd">
    <section class="information recommend">
        <h1>客户信息</h1>
        <div class="forms">
            <div class="mes_div mes_div_epx">
                <label for="">客户姓名</label><input class="lines" type="text" placeholder="请输入客户姓名" name="borrower" id="borrower"/>
                <div class="bt_sex clearfix">
                    <span class="bt_man choose" rel="1">先生</span>
                    <span class="bt_women" rel="2">女士</span>
                </div>
            </div>
            <div class="mes_div">
                <label for="">客户电话</label><input class="lines" type="tel" placeholder="请输入客户电话" name="phone" id="phone" maxlength = '11'/>
            </div>
            <div class="mes_div">
                <label for="">房产小区</label><input class="lines" type="text" placeholder="请输入小区名称" name="block_name" id="block_name"/>
            </div>
            <div class="mes_div">
                <label for="">房屋总价</label><input class="lines" style="width:8rem" type="tel" placeholder="请输入总价" name="price" id="price"/>万
            </div>
            <div class="mes_div">
                <label for="">意向额度</label><input class="lines" type="tel" placeholder="请输入意向额度" name="intentional_money" id="intentional_money"/>万
            </div>
        </div>
    </section>
    <section class="dyd_msg_show">
    </section>
    <button class="bt_subdyd" id="apply_add">提交</button>
</div>

<script src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zepto.min.js"></script>
<script src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zwsPop.js"></script>

<script>
    ;(function (doc, win) {
        var docEl = doc.documentElement,
                resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                recalc = function () {
                    var clientWidth = docEl.clientWidth;
                    if (!clientWidth) return;
                    docEl.style.fontSize = 20 * (clientWidth / 320) + 'px';
                };

        if (!doc.addEventListener) return;
        win.addEventListener(resizeEvt, recalc, false);
        doc.addEventListener('DOMContentLoaded', recalc, false);
    })(document, window);

    //choose_sex
    $('.bt_sex span').on('touchend',function(){

        $(this).addClass('choose').siblings().removeClass('choose');
    });

    //关闭
    $.dyd_msg_show = function(options){
        var borrower = options.borrower || '';
        var block_name = options.block_name || '';
        var intentional_money = options.intentional_money || '';
        var id = options.id || '';

        var _show = $('.dyd_msg_show');
        var html = '<div class="dms_inner"><span class="dyd_close_bt"></span><h1>申请成功!</h1><p class="clearfix"><em>姓名:</em><span>{borrower}</span></p><p class="clearfix"><em>小区:</em><span class="jjjh">{block_name}</span></p><p class="clearfix"><em>意向额度:</em><span>{intentional_money}</span></p><a href="/wap/pledge/customer/">查看进度</a></div>';
        html = html.replace('{borrower}',borrower);
        html = html.replace('{block_name}',block_name);
        html = html.replace('{intentional_money}',intentional_money);
        html = html.replace('{id}',id);
        _show.html(html);
        _show.show();
        _show.find('.dyd_close_bt').on('touchend',function(){
            _show.hide();
        });
    }

	$("#apply_add").on("click", function () {
		var borrower = $("#borrower").val();
		var sex = $(".choose").attr('rel');
		var phone = $("#phone").val();
		var block_name = $("#block_name").val();
		var price = $("#price").val();
		var intentional_money = $("#intentional_money").val();

		if(!borrower){
			showError("客户姓名不能为空");
			return false;
		}
		if(!phone){
			showError("客户电话不能为空");
			return false;
		}
		if(!block_name){
			showError("小区名称不能为空");
			return false;
		}
		if(!price){
			showError("房屋价格不能为空");
			return false;
		}
		if(!intentional_money){
			showError("意向额度不能为空");
			return false;
		}

		$.ajax({
			type : 'post',
			url  : '/wap/pledge/apply/',
			data : {
				borrower : borrower,
				sex : sex,
				phone : phone,
				block_name : block_name,
				price : price,
                intentional_money : intentional_money,
                from : '4',
				submit_flag : "1"
			},
			dataType :'json',
			success : function(data){
				if(data.result){
                    $.dyd_msg_show({'borrower':borrower,'block_name':block_name,'intentional_money':intentional_money,'id':'0'});
					//window.location.href="/wap/pledge/customer/";
				}else{
					showError(data.msg);
				}
			}
		});
	});
</script>
</body>
</html>
