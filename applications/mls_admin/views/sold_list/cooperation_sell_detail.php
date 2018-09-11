<style>
	table{border:1px solid grey;width:400px;height:280px;text-align:center;}
	tr td{border:1px solid grey;}
	td th{padding:2px;}
	#do{text-align:center;}
	.left{text-align:right;width:40%;}
</style>
<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">合作详情</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<table class="table table-striped table-bordered table-hover" id="dataTables-example">
	<thead>
		<tr>
			<th>订单编号</th>
			<th>合作步骤</th>
			<th>合作状态</th>
			<th>房源类型</th>
			<th>房源编号</th>
			<th>甲方姓名</th>
			<th>甲方电话</th>
			<th>乙方姓名</th>
			<th>乙方电话</th>
			<th>经纪人报价(单位:万)</th>
			<th>实际成交价(单位:万)</th>
			<th>创建时间</th>
		</tr>
	</thead>
	<tbody>
		<?php  if(isset($detail) && !empty($detail)){?>
			<tr class="gradeA">
				<td><?php echo $detail['order_sn'];?></td>
				<td><?php echo $detail['step'];?></td>
				<td><?php echo $detail['esta'];?></td>
				<td><?php if($detail['tbl'] == 'sell'){echo '二手房';}else{echo '租房';}?></td>
				<td><?php echo $detail['rowid'];?></td>
				<td><?php echo $detail['broker_name_a'];?></td>
				<td><?php echo $detail['phone_a'];?></td>
				<td><?php echo $detail['broker_name_b'];?></td>
				<td><?php echo $detail['phone_b'];?></td>
				<td><?php echo $detail['price'];?></td>
				<td><?php $price = intval($detail['real_price']);if($price == 0){echo '暂无资料';}else{echo $price;}?></td>
				<td><?php date_default_timezone_set('PRC');echo date('Y-m-d',$detail['creattime']);?></td>
			</tr>
		<?php }else{echo "<tr class='gradeA'><td colspan=10 style='text-align:center;'>暂无二手房成交数据~！</td></tr>";}?>
	</tbody>
</table><input type = 'button' class="btn btn-primary" onclick='goback()' value='返回'>
        </div>
        <!-- /#page-wrapper -->

    </div>
<script>
	function	goback(){
		location.href = "<?=MLS_ADMIN_URL?>/index.php/sell_house_sold/index";
	}
</script>
<?php require APPPATH.'views/footer.php'; ?>
