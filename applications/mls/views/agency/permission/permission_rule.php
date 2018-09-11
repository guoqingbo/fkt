<!doctype html>
<html  >
<head>
    <meta charset="utf-8">
    <title>权限说明</title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/myStyle.css" />
    <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
</head>

<body>

    <!--房源详情弹框-->
    <div class="pop_box_g zwsJsH" id="js_pop_box_g" style="display:block;">
        <div class="hd">
            <div class="title">权限说明</div>
        </div>
        <div class="mod">
            <div class="tab_pop_hd">
                <dl class="clearfix" id="js_tab_t01">
                    <dd class="js_t item itemOn" title="房源管理">房源管理</dd>
                    <dd class="js_t item " title="客源管理">客源管理</dd>
					<dd class="js_t item " title="系统管理">系统管理</dd>
					<dd class="js_t item " title="合同管理">合同管理</dd>
                </dl>
            </div>
            <div class="tab_pop_mod clear" id="js_tab_b01">
                <div class="js_d inner" style="display:block;padding:10px 24px;">
                    <table class="table zws_power_border">
                        <tr>
                            <td class="zws_power_explain">查看他人房源</td>
							<td class="zws_power_explain_con">勾选之后，可以查看公司所有房源。每个人默认含有查看自己房源权限。总经理到经纪人查看权限均为 公司范围。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">共享他人房源</td>
							<td class="zws_power_explain_con">可以设置合作/取消合作房源。每个人默认含有共享/取消共享自己房源的权限。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>

                        <tr>
                            <td class="zws_power_explain">私盘公盘转换</td>
							<td class="zws_power_explain_con">可以设置合作/取消合作同房源。每个人默认含有共享/取消共享自己房源的权限。总经理到区域经理权限均为公司范围。店长和店务秘书的权限为门店级别</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">分配任务</td>
							<td class="zws_power_explain_con">可以分配任务。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
						<tr>
                            <td class="zws_power_explain">分配他人房源</td>
							<td class="zws_power_explain_con">可以对公司房源进行分配。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">查看他人保密信息</td>
							<td class="zws_power_explain_con">勾选之后，可以查看公司其他房源的保密信息。每个人默认含有查看自己保密信息权限。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
						<tr>
                            <td class="zws_power_explain">修改他人出租信息房源</td>
							<td class="zws_power_explain_con">勾选之后可以修改公司其他出租房源，每个人默认含有修改自己房源权限。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">修改他人出售信息房源</td>
							<td class="zws_power_explain_con">勾选之后可以修改公司其他出售房源，每个人默认含有修改自己房源权限。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
						<tr>
                            <td class="zws_power_explain">出售跟进查看权</td>
							<td class="zws_power_explain_con">勾选之后，可以查看公司出售的跟进。总经理到经纪人查看权限均为 公司范围。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">出租跟进查看权</td>
							<td class="zws_power_explain_con">勾选之后，可以查看公司出租的跟进。总经理到经纪人查看权限均为 公司范围。</td>
                        </tr>
                    </table>
                </div>

                <div class="js_d inner inner02"  style="display:none;padding:10px 24px;">
                     <table class="table zws_power_border">
                        <tr>
                            <td class="zws_power_explain">查看他人客源</td>
							<td class="zws_power_explain_con">勾选之后，可以查看公司所有客源。每个人默认含有查看自己客源权限。总经理到经纪人查看权限均为 公司范围。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">修改他人求购信息权</td>
							<td class="zws_power_explain_con">勾选之后可以修改公司其他求购客源，每个人默认含有修改自己求购客源权限。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>

                        <tr>
                            <td class="zws_power_explain">修改他人求租信息权</td>
							<td class="zws_power_explain_con">勾选之后可以修改公司其他求租客源，每个人默认含有修改自己求租客源权限。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">求购跟进查看权</td>
							<td class="zws_power_explain_con">勾选之后，可以查看公司求购的跟进。总经理到经纪人查看权限均为 公司范围。</td>
                        </tr>
						<tr>
                            <td class="zws_power_explain">求租跟进查看权</td>
							<td class="zws_power_explain_con">勾选之后，可以查看公司求租的跟进。总经理到经纪人查看权限均为 公司范围。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">客源分配任务</td>
							<td class="zws_power_explain_con">可以公司级别的分配任务。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
						<tr>
                            <td class="zws_power_explain">分配他人客源</td>
							<td class="zws_power_explain_con">可以对公司房源进行分配。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                    </table>
                </div>
				<div class="js_d inner inner02"  style="display:none;padding:10px 24px;">
                     <table class="table zws_power_border">
                        <tr>
                            <td class="zws_power_explain">组织架构管理</td>
							<td class="zws_power_explain_con">勾选之后，可以操作组织机构管理，并对里面的内容进行各种操作。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">系统参数管理</td>
							<td class="zws_power_explain_con">勾选之后，可以操作系统参数管理，并对里面的内容进行各种操作。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>

                        <tr>
                            <td class="zws_power_explain">数据转移设置</td>
							<td class="zws_power_explain_con">勾选之后，可以进行数据转移设置，并对里面的内容进行各种操作。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">角色权限</td>
							<td class="zws_power_explain_con">勾选之后，可以进行角色权限设置，并对里面的内容进行各种操作总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
						<tr>
                            <td class="zws_power_explain">合作方审核</td>
							<td class="zws_power_explain_con">勾选之后，可以进行合作方审核设置，并对里面的内容进行各种操作。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">考勤管理</td>
							<td class="zws_power_explain_con">勾选之后，可以进行考勤管理设置，并对里面的内容进行各种操作。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
						<tr>
                            <td class="zws_power_explain">公司公告</td>
							<td class="zws_power_explain_con">勾选之后，可以进行公司公告设置，并对里面的内容进行各种操作。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">朋友圈管理</td>
							<td class="zws_power_explain_con">勾选之后，可以进行朋友圈管理设置，并对里面的内容进行各种操作。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>

                    </table>
                </div>
				<div class="js_d inner inner02"  style="display:none;padding:10px 24px;">
                     <table class="table zws_power_border">
                        <tr>
                            <td class="zws_power_explain">查看诚意金</td>
							<td class="zws_power_explain_con">勾选之后，可以查看诚意金。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">新增诚意金</td>
							<td class="zws_power_explain_con">勾选之后，可以新增诚意金。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>

                        <tr>
                            <td class="zws_power_explain">修改诚意金</td>
							<td class="zws_power_explain_con">勾选之后，可以修改诚意金。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">删除诚意金</td>
							<td class="zws_power_explain_con">勾选之后，可以修改诚意金。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
						<tr>
                            <td class="zws_power_explain">修改诚意金状态</td>
							<td class="zws_power_explain_con">勾选之后，可以修改诚意金的状态位。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">查看合同报备</td>
							<td class="zws_power_explain_con">勾选之后，可以查看合同报备。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
						<tr>
                            <td class="zws_power_explain">新增合同报备</td>
							<td class="zws_power_explain_con">勾选之后,可以新增合同报备。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">修改合同报备</td>
							<td class="zws_power_explain_con">勾选之后,可以修改合同报备。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
						<tr>
                            <td class="zws_power_explain">删除合同报备</td>
							<td class="zws_power_explain_con">勾选之后，可以删除合同报备。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
                        <tr>
							<td class="zws_power_explain">合同报备转正</td>
							<td class="zws_power_explain_con">勾选之后，可以转正合同报备。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
                        </tr>
						<tr>
							<td class="zws_power_explain">查看交易合同</td>
							<td class="zws_power_explain_con">勾选之后，可以查看交易合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">新增合同</td>
							<td class="zws_power_explain_con">勾选之后，可以新增交易合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">修改合同</td>
							<td class="zws_power_explain_con">勾选之后，可以修改交易合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">删除合同</td>
							<td class="zws_power_explain_con">勾选之后，可以删除交易合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">作废合同</td>
							<td class="zws_power_explain_con">勾选之后，可以作废交易合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">审核交易合同</td>
							<td class="zws_power_explain_con">勾选之后，可以审核交易合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain"></td>
							<td class="zws_power_explain_con">勾选之后，可以反审核交易合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">新增业务分成</td>
							<td class="zws_power_explain_con">勾选之后，可以新增业务分成。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">修改业务分成</td>
							<td class="zws_power_explain_con">勾选之后，可以修改业务分成。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">删除业务分成</td>
							<td class="zws_power_explain_con">勾选之后，可以删除业务分成。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">结佣</td>
							<td class="zws_power_explain_con">勾选之后，可以进行结佣操作。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">新增 应收应付</td>
							<td class="zws_power_explain_con">勾选之后，可以新增应收应付。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">修改应收应付</td>
							<td class="zws_power_explain_con">勾选之后，可以修改应收应付。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">删除应收应付</td>
							<td class="zws_power_explain_con">勾选之后，可以删除应收应付。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">审核应收应付</td>
							<td class="zws_power_explain_con">勾选之后，可以审核应收应付。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">新增 实收实付</td>
							<td class="zws_power_explain_con">勾选之后，可以新增实收实付。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">修改实收实付</td>
							<td class="zws_power_explain_con">勾选之后，可以修改实收实付。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">删除实收实付</td>
							<td class="zws_power_explain_con">勾选之后，可以删除实收实付。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">审核实收实付</td>
							<td class="zws_power_explain_con">勾选之后，可以审核实收实付。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">反审核实收实付</td>
							<td class="zws_power_explain_con">勾选之后，可以反审核实收实付。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">确认支付</td>
							<td class="zws_power_explain_con">勾选之后，可以确认支付。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">新增权限流程</td>
							<td class="zws_power_explain_con">勾选之后，可以新增权限流程。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">修改权限流程</td>
							<td class="zws_power_explain_con">勾选之后，可以修改权限流程。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">删除权限流程</td>
							<td class="zws_power_explain_con">勾选之后，可以删除权限流程。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">完成权限流程</td>
							<td class="zws_power_explain_con">勾选之后，可以完成权限流程。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">结盘</td>
							<td class="zws_power_explain_con">勾选之后，可以进行结盘。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">查看托管合同</td>
							<td class="zws_power_explain_con">勾选之后，可以查看托管合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">新增合同</td>
							<td class="zws_power_explain_con">勾选之后，可以新增托管合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">修改合同</td>
							<td class="zws_power_explain_con">勾选之后，可以修改托管合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">删除合同</td>
							<td class="zws_power_explain_con">勾选之后，可以删除托管合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">作废合同</td>
							<td class="zws_power_explain_con">勾选之后，可以作废托管合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">审核交易合同</td>
							<td class="zws_power_explain_con">勾选之后，可以审核托管合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">反审核交易合同</td>
							<td class="zws_power_explain_con">勾选之后，可以反审核托管合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">新增付款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以新增付款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">修改付款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以修改付款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">删除付款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以删除付款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">审核付款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以审核付款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">反审核付款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以反审核付款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">确认付款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以确认付款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">新增管家费用</td>
							<td class="zws_power_explain_con">勾选之后，可以新增管家费用。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">修改管家费用</td>
							<td class="zws_power_explain_con">勾选之后，可以修改管家费用。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">删除管家费用</td>
							<td class="zws_power_explain_con">勾选之后，可以删除管家费用。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">审核管家费用</td>
							<td class="zws_power_explain_con">勾选之后，可以审核管家费用。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">反审核管家费用</td>
							<td class="zws_power_explain_con">勾选之后，可以反审核管家费用。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">新增出租合同</td>
							<td class="zws_power_explain_con">勾选之后，可以新增出租合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">修改出租合同</td>
							<td class="zws_power_explain_con">勾选之后，可以修改出租合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">删除出租合同</td>
							<td class="zws_power_explain_con">勾选之后，可以删除出租合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">审核出租合同</td>
							<td class="zws_power_explain_con">勾选之后，可以审核出租合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">反审核出租合同</td>
							<td class="zws_power_explain_con">勾选之后，可以反审核出租合同。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">新增收款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以新增收款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">修改收款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以修改收款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">删除收款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以删除收款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">审核收款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以审核收款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">反审核收款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以反审核收款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
						<tr>
							<td class="zws_power_explain">确认收款业主</td>
							<td class="zws_power_explain_con">勾选之后，可以确认收款业主。总经理到区域经理权限均为 公司范围。店长和店务秘书的权限为门店级别。</td>
						</tr>
                    </table>
                </div>

            </div>
            <div class="tab_pop_bd">
                <div class="zws_caozuo"><a class="zws_jb zws_power_close" onclick="close_pop()"href="javascript:void(0);">关闭</a> </div>
            </div>
			<script>
				function close_pop(){
					window.parent.window.close_rule();
				}
			</script>
        </div>
    </div>
    <script type="text/javascript" src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic03.js"></script>
</body>
</html>
