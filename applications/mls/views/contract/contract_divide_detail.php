<body>
    <div class="achievement_money_pop" id="js_divide_pop" style="display:block">
    <dl class="title_top">
        <dd id="title_top2">业绩分成详情</dd>
    </dl>
    <p class="achievement_money_pop_point">温馨提示：业绩归属和归属经纪人是相互独立的，如A店业务员做了5000元业绩，但该业
    绩是属于B店进行结算的，在此可以灵活进行分配。</p>
        <div>
        
        <div class="achievement_money_pop_input" style="margin: 0 0 0 12px;">
            <h4>应分成金额：<b><?=strip_end_0($contract['commission_total']);?>元</b></h4>
            <dl>
                <dd><b>*</b>分成比例：</dd>
                <dt>
                <span class="money_pop_span"><?=strip_end_0($divide_list['percent']).'%'?>
                    </span>
                </dt>
            </dl>
            <dl>
                <dd><b>*</b>实际分成金额：</dd>
                <dt>
                    <span class="money_pop_span"><?=strip_end_0($divide_list['divide_price']);?>
                    </span>
                </dt>
            </dl>
            <dl>
                <dd><b>*</b>归属人：</dd>
                <dt><?=$divide_list['agency_name'];?>&nbsp;—&nbsp;<?=$divide_list['broker_name'];?>
                </dt>
            </dl>
            <dl>
                <dd><b>*</b>分成描述：</dd>
                <dt>
                    <span class="money_pop_span">
                       <?=$config['divide_type'][$divide_list['divide_type']];?>
                    </span>
                </dt>
            </dl>
            <dl>
                <dd><b>*</b>门店业绩归属：</dd>
                <dt><?=$divide_list['achieve_agency_name_b'];?>&nbsp;—&nbsp;<?=$divide_list['achieve_broker_name_b'];?>
                </dt>
            </dl>
        </div>
            <table width="100%" align="center">
			    <tbody><tr>
				<td style="text-align:center" class="aad_pop_p_T20">
                <button class="btn-lv1 btn-left" type="button" onclick="closeParentWin('js_divide_pop');">确定</button>
				</td>
			    </tr>
			    </tbody>
			</table>
        </div>
    </div>
</body>


