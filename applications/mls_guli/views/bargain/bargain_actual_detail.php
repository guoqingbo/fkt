<body>
	<!--实收实付添加弹窗开始-->
	<div class="achievement_money_pop real_W580" style="display: block;">
	    <dl class="title_top">
            <dd id='title_top'>实收实付详情</dd>
	    </dl>
	    <!--弹出框内容-->
	   <div class="add_pop_messages raal_H372">
		<div class="aad_pop_line1">
		    <form action="" id="add_replace" method="post">
		    <div style="width:98%; padding:1%;float:left;display:inline;">
		    	<ul>
					<li class="aad_pop_line1_title " style="width:10%; float:left;display:inline;">
							实收
					</li>
					<li class="aad_pop_line1_title " style="width:28%; float:left;display:inline;line-height:24px;">
							<p class="aad_pop_line1_title_p"  style=" width:3.5em;text-align:right"><b class="resut_table_state_1 input_add_F" style="font-weight:normal;"></b>款项：</p>
						    <?=$config['money_type'][$detail['money_type']];?>
					</li>
					<li class="aad_pop_line1_title "  style="width:28%; float:left;display:inline;line-height:24px;">
						<p class="aad_pop_line1_title_p" style=" width:5em;text-align:right">　　收方：</p>
					    <?=$config['collect_type'][$detail['collect_type']];?>
					</li>
					<li class="" style="float:left;display:inline;font-weight:normal;line-height:24px;">
							 <p class="aad_pop_line1_title_p">实收金额：</p>
						    <?=$detail['collect_money']?strip_end_0($detail['collect_money']).'元':'';?>
					</li>
		    	</ul>
		    </div>

			<div style="width:98%; padding:1%;float:left;display:inline;font-weight:normal;">
		    	<ul>
					<li class="aad_pop_line1_title " style="width:10%;float:left;display:inline;">
							实付
					</li>
					<li class="aad_pop_line1_title " style="width:28%;float:left;display:inline;font-weight:normal;line-height:24px;">
							<p style=" width:3.5em;text-align:right" class="aad_pop_line1_title_p">付方：</p>
							<?=$config['pay_type'][$detail['pay_type']];?>
					</li>
					<li class="aad_pop_line1_title " style="width:32%;float:left;display:inline;font-weight:normal;line-height:24px;">
						<p class="aad_pop_line1_title_p">实付金额：</p>
						<?=$detail['pay_money']?strip_end_0($detail['pay_money']).'元':'';?>
					</li>


		    	</ul>
		    </div>

		    <div style="width:98%; padding:1%;float:left;display:inline;">
		    	<ul>
					<li class="aad_pop_line1_title " style="width:38%;float:left;display:inline;font-weight:normal;">
						<p class="aad_pop_line1_title_p"  style="width:12%;text-align:right"><b class="resut_table_state_1"></b>收付日期：</p>
					    <?=$detail['flow_time'];?>
					</li>
					<li class="aad_pop_line1_title " style="width:56%;float:left;display:inline;font-weight:normal;">

							<p class="aad_pop_line1_title_p ">　收付人：</p>
						    <?=$detail['flow_department_name']?$detail['flow_department_name'].'-'.$detail['flow_signatory_name']:'';?>
					</li>
		    	</ul>
		    </div>

			<div style="width:98%; padding:1%;float:left;display:inline;">
		    	<ul>
					<li class="aad_pop_line1_title " style="width:100%;float:left;display:inline;font-weight:normal;">
						<p class="aad_pop_line1_title_p"><b class="resut_table_state_1"></b>收付方式：</p>
					    <?=$config['payment_method'][$detail['payment_method']];?>
					</li>

		    	</ul>
		    </div>

		    <div style="width:98%; padding:1%;float:left;display:inline;">
		    	<ul>
					<li class="aad_pop_line1_title " style="width:16%;float:left;display:inline;font-weight:normal;">
						刷卡手续费
					</li>
					<li class="aad_pop_line1_title " style="width:18.5%;float:left;display:inline;font-weight:normal;">

						<?=$detail['count_fee']?strip_end_0($detail['counter_fee']).'元':'';?>
					</li>
					<li class="aad_pop_line1_title " style="width:31%;float:left;display:inline;font-weight:normal;">
						<p class="aad_pop_line1_title_p">单据号：</p>
				    	<?=$detail['docket']?$detail['docket']:'';?>
					</li>
					<li class="aad_pop_line1_title " style="width:31%;float:left;display:inline;font-weight:normal;">
						<p class="aad_pop_line1_title_p">单据类型：</p>
						<?=$config['docket_type'][$detail['docket_type']];?>
					</li>

		    	</ul>
		    </div>
			<table width="100%">
                    <tbody>
                        <tr>
                            <td width="12%" style="text-align:right" class="label aad_pop_p_T20">收付说明：</td>
                            <td width="86%" class="aad_pop_p_T20"><?=$detail['remark'];?></td>
                        </tr>
                    </tbody>
                </table>
            <table width="100%" align="center">
			    <tbody><tr>
				<td style="text-align:center" class="aad_pop_p_T20">
                <button class="btn-lv1 btn-left" type="button" onclick="closeParentWin('js_replace_pop');">确定</button>
				</td>
			    </tr>
			    </tbody>
			</table>
		</div>
		</div>
	</div>
</body>
