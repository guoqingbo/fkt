<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>数据安全说明</title>
<link href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/css/v1.0/base.css" rel="stylesheet" type="text/css">
</head>
<body>
	<style>
		.subject_01{overflow-y:scroll;position:relative; width:100%;}
		.subject_01 .inner{width:740px; padding:30px 40px; border:1px solid #E5E5E5; margin:0 auto; margin-top:20px; font-family:'微软雅黑'; line-height:25px; color:#333;}
		.subject_01 h1{text-align:center; line-height:40px; font-size:16px; color:#000;}
		.subject_01 h5{text-align:center; color:#9c9c9c; border-bottom:1px solid #E5E5E5; padding-bottom:5px;}
		.subject_01 h3, .subject_01 h4{ font-size:14px; margin-top:20px;}
		.subject_01 h3{ font-weight:bold;}
		.subject_01 p{text-indent:30px;}
		.cf60{color:#f60;}
		.align-right{text-align:right}
		.subject_01 p.bot{padding:30px 0 50px 40px; font-weight:bold; font-size:14px; text-indent:0;}
	</style>
    <div class="subject_01">
        <div class="inner">
			<h1>数据安全说明</h1>
			<h5>发布时间：2016-3-18    发布人：平台</h5>
			<h4>尊敬的广大中介朋友：</h4>
			<p>本平台，致力于为中小中介提供全方位的互联网平台服务，数据安全也是我们服务的一部分。为广大中介朋友提供免费ERP数据管理系统和MLS合作平台（合称“<?=$title?>”），旨在通过数据管理、运用，提升大家的成交效率，帮助大家在“效率大于资源“的时代，能够更快的实现成交。针对大家关心的数据安全类问题，说明如下：</p>
			<h3>一、数据的管理</h3>
			<p>平台坚持以保护中介门店“数据隐私”为第一优先原则，利用先进技术+严格的等级管理双重保障，确保中介公司数据信息安全。</p>
			<ul>
				<li>1、<span class="cf60" >SSL数据加密技术，确保数据的独立性、安全性、完整性：</span>SSL数据加密技术，提供安全、可靠、可弹性扩展的数据库存储服务，将核心信息与其他部分相隔区分有效保护，同时拥有并发控制、故障恢复等先进功能。</li>
				<li>2、<span class="cf60" >七大虚拟化技术，消除数据泄露和失窃隐患：</span>统一认证、分级授权管理、服务器报警、用户密码、用户安全、访问控制和时间策略，保证服务器的稳定性以及实现用户安全访问应用程序的设置。</li>
				<li>3、<span class="cf60" >“线上+线下”双向把控，确保数据安全：</span>线上，时时跟踪系统安全状况并提供升级防护服务；线下，总部到分站，建立严格的安全等级制度，分站及非相关人员，无权接触到数据，数据管理专人负责，杜绝数据人为泄露可能。</li>
			</ul>
			<h3>二、数据的存取</h3>
			<p>在数据存取方面，提出“数据银行“概念，即数据所有方可随存随取，不收取任何所谓的”解密费“。我们在<?=$title?>系统内部有专门的导出功能，用户可设置专人权限操作。</p>
			<p>对于数据提供者，我们会根据等级提供相应的积分奖励，作为“数据银行”利息。</p>
			<h3>三、数据的保障：</h3>
			<ul>
				<li>1、对于中介朋友举报的，在平台提供的信息撮合服务中，出现的中介同行跳单、私单等行为，将坚决按照平台规则，给予处理；情节严重者，将终身关闭帐号；</li>
				<li>2、对于中介朋友举报，由系统或人员造成的，违反本说明和《<a href="<?php echo MLS_URL;?>/about/shengming.html" target="_blank">服务声明</a>》、《<a href="<?php echo MLS_URL;?>/about/xieyi.html" target="_blank">服务协议</a>》（服务声明和协议同样适用于<?=$title?>）的数据泄露、失窃、滥用等行为，凡举报属实的，将给予中介朋友相应的奖励，并严肃处理当事人，直至开除和行业通报；严重的，将追求其法律责任。若对中介朋友造成损失的，将按照法律规定，给予相应赔偿。</li>
			</ul>
			<p class="bot">感谢广大中介朋友使用<?=$title?>；平台也将竭力为大家提供<span class="cf60" >全面、优质、高效</span>的互联网平台服务！<br>祝大家：工作顺利！生意兴隆！</p>
			<p class="align-right">科地地产</p>
			<p class="align-right">2016年3月18日</p>

		</div>
	</div>
	<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js"></script>
	<script>
		$(function () {
			function re_height(){
				$(".subject_01").css({
					"height":$(window).height()
				});
			};
			re_height();
			$(window).resize(function(e) {
				re_height();
			});
		});
	</script>
</body>
</html>
