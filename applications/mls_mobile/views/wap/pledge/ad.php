<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="email=no" name="format-detection" />
    <title>抵押贷详情</title>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/ajd.css" type="text/css"/>
</head>
<body>
<div class="wrapper apply_dyd">
    <section class="advantage">
		<span>
			<img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/zjd/detial_icon1.png" alt="额度高,最高500万" />
			<p>额度高</p>
			<b>最高500万</b>
		</span>
		<span>
			<img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/zjd/detial_icon2.png" alt="放款快 最快3天放款" />
			<p>放款快</p>
			<b>最快3天放款</b>
		</span>
		<span>
			<img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/zjd/detial_icon3.png" alt="月息低 最低仅1.2分" />
			<p>月息低</p>
			<b>最低仅1.2分</b>
		</span>
		<span>
			<img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/zjd/detial_icon4.png" alt="返点多 可代收费" />
			<p>返点多</p>
			<b>可代收费</b>
		</span>
		<div class="indroduce_zws">
			<p><b>贷款额度：</b>最高500万</p>
			<p><b>贷款期限：</b>3个月、6个月、9个月</p>
			<p><b>贷款利率：</b>月息1.2%起</p>
			<p><b>放款时间：</b>2-4个工作日</p>
			<p><b>还款方式：</b>先息后本</p>
			<p><b>抵押成数：</b>最高7成</p>
			<p><b class="redcolor">佣金说明：</b>中介可代收服务费；每笔需收取2.5%的服务费（含担保抵押公证等服务费用），服务费用根据不同城市会有所调整，以当地实际情况为准。</p>
		</div>
	</section>
	<!--申请条件-->
	<dl class="apply_contidion_zws">
		<dd>申请条件</dd>
		<dt>1. 年龄18-55周岁；<br/>
			2. 抵押物在市区范围以内的全产权住宅（无按揭款，无产权纠纷）；<br/>
			3. 1995年后房产；
		</dt>
	</dl>
    <!--申请流程-->
    <dl class="apply_contidion_zws">
        <dd>申请流程</dd>
        <dt>
            <div class="abb clearfix">
                <span>1</span>
                <em></em>
                <div class="abb_inner">
                    <h2>在线申请</h2>
                    <p>进入“我要借款”填写贷款信息，提交申请，等待客服回电，核对信息</p>
                </div>
            </div>
            <div class="abb clearfix">
                <span>2</span>
                <em></em>
                <div class="abb_inner">
                    <h2>材料审核</h2>
                    <p>申请成功后，我们会将材料发送至贷款部门，1-3天通知审核结果</p>
                </div>
            </div>
            <div class="abb clearfix">
                <span>3</span>
                <em></em>
                <div class="abb_inner">
                    <h2>面签协议</h2>
                    <p>审核通过后，客户前往线下门店进行签约</p>
                </div>
            </div>
            <div class="abb clearfix">
                <span>4</span>
                <div class="abb_inner">
                    <h2>完成放款</h2>
                    <p>完成签约后，出借方根据协议约定，将款项汇入指定账户</p>
                </div>
            </div>
        </dt>
    </dl>
	<!--准备材料-->
	<dl class="apply_contidion_zws">
		<dd>准备资料</dd>

		<dt class="dlBttom">
			1. 个人及配偶二代身份证（如有配偶则提供）；<br/>
			2. 房产证（产权人和共有人须齐全）；<br/>
			3. 户口薄或户籍证明；<br/>
			4. 收入证明；<br/>
			5. 银行流水；<br/>
			6. 其它贷款方需要提供的资料。<br/>
			（市区内上门收集资料）
		</dt>
	</dl>
	<div class="zws_btn_apply_box"><a class="bt_subdyd zws_btn_apply" href="/wap/pledge/apply/">立即申请</a></div>

</div>

<script src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zepto.min.js"></script>
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

    //product_photo
    $('.bt_procuct').on('touchend',function(){
        $('.ma_kuang').html('');
        var imgs = $('<img src="" alt=""/>');
        var imgSrc= 'images/show.jpg';
        imgs.attr('src',imgSrc)
        $('.ma_kuang').append(imgs);
    });
</script>
</body>
</html>
