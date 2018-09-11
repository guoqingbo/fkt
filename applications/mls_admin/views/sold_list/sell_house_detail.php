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
                    <h1 class="page-header"><?php echo $type;?>成交房源详情</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<table class="table table-striped table-bordered table-hover" id="dataTables-example">
	<thead>
		<tr>
			<th>房源编号</th>
			<th>区属</th>
			<th>板块</th>
			<th>楼盘名称</th>
			<th>户型</th>
			<th>装修</th>
			<th>朝向</th>
			<th>价格(单位:万)</th>
			<th>面积(平方米)</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(isset($house_detail) && !empty($house_detail)){?>
			<tr class="gradeA">
				<td><?php echo $house_detail['rowid'];?></td>
				<td><?php echo $house_detail['districtname'];?></td>
				<td><?php echo $house_detail['streetname'];?></td>
				<td><?php echo $house_detail['blockname'];?></td>
				<td><?php if($house_detail['room'] != ''){echo $house_detail['room']."室";}if($house_detail['hall']!=''){echo $house_detail['hall']."厅";}if($house_detail['toilet']!=''){echo $house_detail['toilet']."卫";}?></td>
				<td><?php echo $house_detail['fitment'];?></td>
				<td><?php echo $house_detail['forward'];?></td>
				<td><?php echo $house_detail['price'];?></td>
				<td><?php echo $house_detail['buildarea'];?></td>
			</tr>
		<?php }else{echo "<tr class='gradeA'><td colspan=9 style='text-align:center;'>暂无二手房成交数据~！</td></tr>";}?>
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
