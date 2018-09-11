<!--合作协议弹框-->
<div class="pop_box_g" id="js_pop_protocol" style="width: 700px !important; height: auto !important; display: none;">
    <div class="hd">
        <div class="title">合作协议</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="protocol-wrap">
        <h1 class="title">合作协议</h1>
        <div class="head">
<!--	<?php

	if($cooperate_info['houseinfo']['broker_id'] == $cooperate_info['brokerinfo_a']['broker_id'])
	{
		$brokera = '房源方';
		$brokerb = '客户方';
	}
	else
	{
		$brokera = '客户方';
		$brokerb = '房源方';
	}

	?>-->

			<?php if(is_array($house_broker_info) && !empty($house_broker_info)) { ?>
            <p>甲方（房源方）：&nbsp;<span class="success bold"><?php echo $house_broker_info['truename'];?></span>&nbsp;<?php echo $house_broker_info['phone'];?>&nbsp;(<?php echo $house_broker_info['agency_name'];?>&nbsp;/&nbsp;<strong>总公司：</strong><span class="success bold"><?php echo $house_company_name;?></span>)</p>
            <?php } ?>
            <?php if(is_array($customer_broker_info) && !empty($customer_broker_info)) { ?>
            <p>乙方（客户方）：&nbsp;<span class="success bold"><?php echo $customer_broker_info['truename'];?></span>&nbsp;<?php echo $customer_broker_info['phone'];?>&nbsp;(<?php echo $customer_broker_info['agency_name'];?>&nbsp;/&nbsp;<strong>总公司：</strong><span class="success bold"><?php echo $customer_company_name;?></span>)</p>
            <?php } ?>

            <p>丙方：&nbsp;科地地产</p>
        </div>

        <div class="content-wrap">
            <div>
                <p>甲、乙双方就通过丙方提供合作平台，共同为买卖双方提供居间服务相关事宜协商一致达成以下协定。</p>
            </div>
            <div class="content">
				<h1 class="bold highlight"><strong>一、合作事项</strong></h1>
				<div>
					<table>
						<tr>
							<td class="sequence">1.</td>
							<td>合作标的物业：
								<span>
								   <strong>楼盘名称：</strong><?php echo $cooperate_info['houseinfo']['blockname'];?>&nbsp;
								   <strong>区属：</strong><?php echo $cooperate_info['houseinfo']['districtname'];?>-<?php echo $cooperate_info['houseinfo']['streetname'];?>&nbsp;<strong>面积：</strong><?php echo strip_end_0($cooperate_info['houseinfo']['buildarea']);?>&nbsp;平米&nbsp;
								   &nbsp;<strong>总价：</strong><strong class="num"><?php echo ('1'==$cooperate_info['houseinfo']['price_danwei'])?$cooperate_info['houseinfo']['price']/$cooperate_info['houseinfo']['price_danwei']/30:strip_end_0($cooperate_info['houseinfo']['price']);?></strong>&nbsp;
								   <?php if($cooperate_info['houseinfo']['tbl'] == 'sell'){?>
								   万
								   <?php }else {
									   echo ('1'==$cooperate_info['houseinfo']['price_danwei'])?'元/㎡*天':'元/月';
								   }
								   ?>&nbsp;<strong>户型：</strong>
								   <?php echo $cooperate_info['houseinfo']['room'];?>室<?php echo $cooperate_info['houseinfo']['hall'];?>厅<?php echo $cooperate_info['houseinfo']['toilet'];?>卫&nbsp;<strong>楼层：</strong><?php echo $cooperate_info['houseinfo']['floor']; ?><?php if($cooperate_info['houseinfo']['floor_type']==2){ echo "-".$cooperate_info['houseinfo']['subfloor'];}?>/<?php echo $cooperate_info['houseinfo']['totalfloor']; ?>
								</span>
							</td>
						</tr>
						<tr>
							<td class="sequence">2.</td>
							<td>合作事由：甲乙双方本着互惠互利原则，甲方提供标的物业房源，乙方向甲方推荐意向客户，双方共同促进此物业的成交。</td>
						</tr>
						<tr>
							<td class="sequence">3.</td>
							<td>双方约定由（√甲方  √乙方  √共同）完成：带客户看房、签约、贷款、过户等相关手续。
							</td>
						</tr>
					</table>
				</div>
				<h1 class="bold highlight"><strong>二、佣金分配方式</strong></h1>
				<div>
					<table>
						<tr>
							<td class="sequence">1.</td>
							<td>双方约定本次交易的居间佣金的总额（指本次交易的买卖双方分别支付给甲、乙双方的佣金总额）按照<strong><u>50%：50% </u></strong>进行合作分成。佣金须等买卖双方物业交验完成后方可分配。</td>
						</tr>
						<tr>
							<td class="sequence">2.</td>
							<td>佣金分配后,若因买卖双方原因导致该服务项目有变化,佣金需要退还时,甲乙双方均须如数退还佣金,否则所产生任何法律诉讼及纠纷时,不肯退费方应自行承担所有经济纠纷及法律责任。</td>
						</tr>
					</table>
				</div>
				<h1 class="bold highlight"><strong>三、关于赏金</strong></h1>
				<div>
					<table>
						<tr>
							<td class="sequence">1.</td>
							<td>甲乙双方为了能尽快促成成交，可发布针对对方的“赏金”；赏金为佣金以外的合作费用；在买卖双方物业交验完成后由发布方支付。</td>
						</tr>
						<tr>
							<td class="sequence">2.</td>
							<td>首次发布的赏金金额不得低于人民币壹千元；否则，丙方作为合作发布的第三方平台，有权不予以发布；</td>
						</tr>
						<tr>
							<td class="sequence">3.</td>
							<td>赏金发布方在合作过程中，可根据实际情况随时调整赏金金额，但不得低于初次发布和上次发调整后的金额；</td>
						</tr>
						<tr>
							<td class="sequence">4.</td>
							<td>赏金发布方在与其他中介方达成居间合作之前，可根据实际情况随时取消赏金奖励，甲乙双方均不得就此有异议；居间合作达成后，已发布的赏金不得取消，发布方应据实支付赏金费用</td>
						</tr>
					</table>
				</div>
				<h1 class="bold highlight"><strong>四、甲方权利义务</strong></h1>
				<div>
					<table>
						<tr>
							<td class="sequence">1.</td>
							<td>甲方必须保证所提供的物业信息真实有效。</td>
						</tr>
						<tr>
							<td class="sequence">2.</td>
							<td>甲方负责联系合作房源的业主，安排看房等事宜。</td>
						</tr>
						<tr>
							<td class="sequence">3.</td>
							<td>甲方为乙方完成本次交易提供相应的便利和协助。</td>
						</tr>
						<tr>
							<td class="sequence">4.</td>
							<td>甲方承诺自本协议签订之日起90日内不与乙方提供的客户发生直接联系，不通过其他渠道非法获取客户信息，承诺不与客户互留电话及其它联系方式，所有相关谈判必须通过乙方与客户进行沟通。</td>
						</tr>
						<tr>
							<td class="sequence">5.</td>
							<td>如甲方发现乙方的客户信息虚假或发现乙方通过其他渠道直接或间接接触甲方业主，并试图获取此房源信息，取得确凿证据（必须有但不限于客户书面证明、乙方录音等）后，经双方协商无法解决的，则甲方可向丙方投诉，丙方有权要求双方无条件配合核实所有信息。一经查实，丙方视情节轻重，有权给予违约方警告公示、降级、暂停帐号、直至永久关闭帐号等处罚，同时将结果通知甲、乙双方。</td>
						</tr>
					</table>
				</div>
				<h1 class="bold highlight"><strong>五、乙方权利义务</strong></h1>
				<div>
					<table>
						<tr>
							<td class="sequence">1.</td>
							<td>乙方保证乙方的客户真实有效。</td>
						</tr>
						<tr>
							<td class="sequence">2.</td>
							<td>乙方负责联系客户，安排看房事宜。</td>
						</tr>
						<tr>
							<td class="sequence">3.</td>
							<td>乙方为甲方完成本次交易提供相应的便利和协助。</td>
						</tr>
						<tr>
							<td class="sequence">4.</td>
							<td>乙方承诺自本协议签订之日起90日内不与甲方业主发生直接联系，不通过其他渠道非法获取此房源信息，承诺不与业主互留电话及其它联系方式，所有相关谈判必须通过甲方与业主进行沟通。</td>
						</tr>
						<tr>
							<td class="sequence">5.</td>
							<td>如乙方发现甲方的房源信息虚假或发现甲方通过其他渠道直接或间接接触乙方客户，并试图获取该客户信息，取证取得确凿证据（必须有但不限于客户书面证明、乙方录音等）后，经双方协商无法解决的，则乙方可向丙方投诉，丙方有权要求双方无条件配合核实所有信息，一经查实，丙方视情节轻重，有权给予违约方警告公示、降级、暂停帐号、直至永久关闭帐号等处罚，同时将结果通知甲、乙双方。</td>
						</tr>
					</table>
				</div>
				<h1 class="bold highlight"><strong>六、丙方权利义务</strong></h1>
				<div>
					<table>
						<tr>
							<td class="sequence">1.</td>
							<td>丙方承诺秉持公平、公正、公开的第三方立场，不偏袒合作中的任何一方。</td>
						</tr>
						<tr>
							<td class="sequence">2.</td>
							<td>丙方作为第三方平台，仅为双方提供信息展示、推荐、匹配服务；对于甲乙双方与客户或甲乙双方的客户在房产交易过程中的产生的任何纠纷，丙方不负任何责任；</td>
						</tr>
						<tr>
							<td class="sequence">3.</td>
							<td>甲乙双方就本次合作的相关事宜，双方确认并认可丙方或丙方所认可的经纪人联盟的权威裁决，并无条件的遵守其作出的裁决。</td>
						</tr>
					</table>
				</div>
				<h1 class="bold highlight"><strong>七、其他约定</strong></h1>
				<div>
					<table>
						<tr>
							<td class="sequence">1.</td>
							<td>甲、乙双方保证遵守丙方关于合作的规章条款。甲乙双方可根据具体情况单独约定其他商务条件，丙方对甲乙双方私下达成的协议或约定不承担任何责任。一旦双方在私下达成合作协议和承诺，丙方将不承担本协议约定的应由丙方承担的责任。</td>
						</tr>
						<tr>
							<td class="sequence">2.</td>
							<td>本次交易的法律责任由甲、乙双方根据佣金分配比例各自承担相应的风险责任；如任一方违反国家法律法规造成经济纠纷和法律责任由违反方自行承担。</td>
						</tr>
						<tr>
							<td class="sequence">3.</td>
							<td>甲乙双方必须如实及时沟通并在合作平台更新状态，不得采用隐瞒事实真相、串通欺骗等非法手段或枉顾同行利益,通过他人或单位完成交易并私吞服务报酬, 一经查实，丙方视情节轻重，有权给予违约方警告公示、降级、暂停帐号、直至永久关闭帐号等处罚，同时违约方必须按照以上服务报酬分配金额双倍赔偿给守约方。</td>
						</tr>
						<tr>
							<td class="sequence">4.</td>
							<td>甲乙方按照本合同约定开展合作的，应遵守本合同的约定，如因一方违约，给另一方造成损失的，守约方可要求违约方赔偿，同时，丙方可按照本合同约定对违约方进行处罚，但丙方不对任一方的违约行为向守约方承担任何责任。</td>
						</tr>
						<tr>
							<td class="sequence">5.</td>
							<td>甲、乙、丙三方均应按照国家有关税法规定自行纳税。</td>
						</tr>
						<tr>
							<td class="sequence">6.</td>
							<td>本协议双方确认提交后即时生效，与线下签字协议具有同等法律效力。</td>
						</tr>
					</table>
				</div>
				<div class="head">
					<?php if(is_array($house_broker_info) && !empty($house_broker_info)) { ?>
					<p>甲方：&nbsp;<span class="success bold"><?php echo $house_broker_info['truename'];?></span></p>
					<?php } ?>
					<?php if(is_array($customer_broker_info) && !empty($customer_broker_info)) { ?>
					<p>乙方：&nbsp;<span class="success bold"><?php echo $customer_broker_info['truename'];?></span></p>
					<?php } ?>
					<p>丙方：&nbsp;<!--<?php echo SOFTWARE_NAME;?>-->科地地产</p>
					<p>签订日期：<?php echo date("Y-m-d");?></p>
				</div>
                <!--<h1 class="bold highlight">甲方责任：</h1>
                <div>
                    <table>
                        <tr>
                            <td class="sequence">1.</td>
                            <td>甲、乙双方保证遵守丙方关于看房的规章条款。甲方必须保证甲方代理的房源真实有效。甲方保证不与乙方的下家（买主或者求租客人）发生直接联 系，不和下家互留电话及其它联系方式。乙方保证不与甲方业主直接联系，不可以把这个物业作为自己的房源，保证不与业主互留电话及其它联系方式，所有关于出 价的谈判必须通过甲方和业主进行。如乙方发现房源信息虚假，或者甲方发现乙方直接或间接接触甲方业主，并试图挖走房源，则可向丙方投诉，丙方有权要求双方 无条件配合核实所有信息，并根据《合发合作平台规定》做出相应处置后，将结果通知甲、乙双方。</td>
                        </tr>
                        <tr>
                            <td class="sequence">2.</td>
                            <td>甲方负责联系上述房源的业主，安排看房事宜。</td>
                        </tr>
                        <tr>
                            <td class="sequence">3.</td>
                            <td>甲方为乙方完成本次交易提供相应的便利和协助。</td>
                        </tr>
                        <tr>
                            <td class="sequence">4.</td>
                            <td>在本合同期内及合同期结束后6个月内，甲、乙双方未经对方同意，不得擅自联系对方的客户及 房源。一经发现，甲乙双方有权向丙方投诉，丙方核实情况后有权并根据《保证金处置协议》对违规方进行惩罚。本协议的签订，确认双方承认丙方或丙方所认可的 经纪人联盟的权威仲裁权利，并无条件的遵守仲裁的决定。</td>
                        </tr>
                        <tr>
                            <td class="sequence">5.</td>
                            <td>甲乙双方不得隐瞒任何关于本次交易的事实真相，不得串通欺骗等非正当手段侵害对方利益，必须如实及时在合发平台更新状态。</td>
                        </tr>
                    </table>
                </div>
                <h1 class="bold highlight">乙方责任：</h1>
                <div>
                    <table>
                        <tr>
                            <td class="sequence">1.</td>
                            <td>乙方保证乙方的客户真实有效。如甲方发现乙方的客户信息虚假，则甲方可向丙方投诉，丙方有权核实相应客户信息的真实性，，并根据《合发合作平台规定》做出相应处置后，将结果通知甲、乙双方。</td>
                        </tr>
                        <tr>
                            <td class="sequence">2.</td>
                            <td>乙方负责联系客户，安排看房事宜。</td>
                        </tr>
                        <tr>
                            <td class="sequence">3.</td>
                            <td>乙方为甲方完成本次交易提供相应的便利和协助。</td>
                        </tr>
                        <tr>
                            <td class="sequence">4.</td>
                            <td>在本合同期内或合同期结束后6个月内，乙方未经甲方同意，不得以任何方式擅自联系甲方提供的房源的业主。一经发现，甲方有权向丙方投诉，丙方核实情况后有权并根据《保证金处置协议》对乙方进行惩罚。</td>
                        </tr>
                        <tr>
                            <td class="sequence">5.</td>
                            <td>乙方不得隐瞒任何关于本次交易的事实真相，不得串通欺骗等非正当手段侵害甲方或丙方利益。</td>
                        </tr>
                        <tr>
                            <td class="sequence">6.</td>
                            <td>本次交易的法律责任由甲、乙双方根据佣金分配比例，各自承担相应的风险责任。如任何一方违反国家的法律法规造成法律责任有违反方承担。</td>
                        </tr>
                        <tr>
                            <td class="sequence">7.</td>
                            <td>甲、乙双方通过丙方平台成功完成本次合作交易后，各自向丙方支付相当于总佣金0%的第三方监管服务费。</td>
                        </tr>
                        <tr>
                            <td class="sequence">8.</td>
                            <td>甲、乙、丙双方均应按照国家有关税法规定依法纳税,并自行负责。</td>
                        </tr>
                    </table>
                </div>-->
            </div>
        </div>
    </div>
</div>
